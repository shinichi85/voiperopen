#!/usr/bin/perl -w

use DBI;

open(FILE, "/etc/amportal.conf") || die "Failed to open amportal.conf\n";
while (<FILE>) {
    chomp;                  # no newline
    s/#.*//;                # no comments
    s/^\s+//;               # no leading white
    s/\s+$//;               # no trailing white
    next unless length;     # anything left?
    my ($var, $value) = split(/\s*=\s*/, $_, 2);
    $User_Preferences{$var} = $value;
}
close(FILE);

################### BEGIN OF CONFIGURATION ####################

# the name of the extensions table
$table_name = "queues";
# the path to the extensions.conf file
# WARNING: this file will be substituted by the output of this program
$queues_conf = "/etc/asterisk/queues_additional.conf";

# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"AMPDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"AMPDBNAME"};
# username to connect to the database
$username = $User_Preferences{"AMPDBUSER"};
# password to connect to the database
$password = $User_Preferences{"AMPDBPASS"};

################### END OF CONFIGURATION #######################

$additional = "";

open( EXTEN, ">$queues_conf" ) or die "Cannot create/overwrite extensions file: $queues_conf (!$)\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
$statement = "SELECT keyword,data from $table_name where id=0 and keyword <> 'account' and flags <> 1";
my $result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
  exit;
}
$additional = "";
my @resultSet = @{$result};
if ( $#resultSet > -1 ) {
    foreach $row (@{ $result }) {
        my @result = @{ $row };
        $additional .= $result[0]."=".$result[1]."\n";
    }
}

$statement = "SELECT data,id from $table_name where keyword='account' and flags <> 1 group by data";

$result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
}

@resultSet = @{$result};
if ( $#resultSet == -1 ) {
  print "No queues defined in $table_name\n";
  exit;
}

foreach my $row ( @{ $result } ) {
    my $account = @{ $row }[0];
    my $id = @{ $row }[1];
    print EXTEN "[$account]\n";
    $statement = "SELECT keyword,data from $table_name where id=$id and keyword <> 'account' and keyword <> 'rtone' and keyword <> 'prefix' and keyword <> 'password' and keyword <> 'name' and keyword <> 'maxwait' and keyword <> 'goto' and keyword <> 'agentannounce' and keyword <> 'callerannounce' and keyword <> 'alertinfo' and keyword <> 'cwignore' and flags <> 1 order by keyword DESC";
    my $result = $dbh->selectall_arrayref($statement);
    unless ($result) {
        # check for errors after every single database call
        print "dbh->selectall_arrayref($statement) failed!\n";
        print "DBI::err=[$DBI::err]\n";
        print "DBI::errstr=[$DBI::errstr]\n";
        exit;
    }

    my @resSet = @{$result};
    if ( $#resSet == -1 ) {
        print "no results\n";
        exit;
    }

    foreach my $row ( @{ $result } ) {
        my @result = @{ $row };
        print EXTEN "$result[0]=$result[1]\n";
    }

    print EXTEN "$additional\n";
}

exit 0;


