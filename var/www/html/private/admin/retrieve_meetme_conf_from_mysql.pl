#!/usr/bin/perl -w
#Copyright  2005-2006 SpheraIT

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
$table_name = "meetme";
# the path to the extensions.conf file
# WARNING: this file will be substituted by the output of this program
$meetme_conf = "/etc/asterisk/meetme_additional.conf";

# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"AMPDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"AMPDBNAME"};
# username to connect to the database
$username = $User_Preferences{"AMPDBUSER"};
# password to connect to the database
$password = $User_Preferences{"AMPDBPASS"};

################### END OF CONFIGURATION #######################

open( EXTEN, ">$meetme_conf" ) or die "Cannot create/overwrite extensions file: $meetme_conf (!$)\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");

$statement = "SELECT * from $table_name order by exten";
$result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
}

my @resultSet = @{$result};
if ( $#resultSet == -1 ) {
  print "No meetme extensions defined in $table_name\n";
  exit;
}

foreach my $row ( @{ $result } ) {
	my @result = @{ $row };

		if ( $result[2] eq "" ) {

			print EXTEN "conf => $result[0]";

		} else {

		print EXTEN "conf => $result[0],$result[2],$result[3]";

		}

		print EXTEN "\n";
	}

exit 0;
