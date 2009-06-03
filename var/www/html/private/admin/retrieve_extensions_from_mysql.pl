#!/usr/bin/perl -w
# Use these commands to create the appropriate tables in MySQL
# If flags is 1 then this record is not included in the output extensions file
#
#CREATE TABLE extensions (
#   context CHAR(20) DEFAULT 'default' NOT NULL,
#   extension CHAR(20) NOT NULL,
#   priority INT(2) DEFAULT '1' NOT NULL,
#   application CHAR(20) NOT NULL,
#   args CHAR(50),
#   descr TEXT,
#   flags INT(1) DEFAULT '0' NOT NULL,
#   PRIMARY KEY(context, extension, priority)
#);
#
#CREATE TABLE globals (
#   variable CHAR(20) NOT NULL,
#   value CHAR(50) NOT NULL,
#   PRIMARY KEY(variable, value)
#);

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
$table_name = "extensions";
# the name of the globals table
$global_table_name = "globals";
# the name of the globals table
$meetme_table_name = "meetme";
# the path to the extensions.conf file
# WARNING: this file will be substituted by the output of this program
$extensions_conf = "/etc/asterisk/extensions_additional.conf";

# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"AMPDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"AMPDBNAME"};
# username to connect to the database
$username = $User_Preferences{"AMPDBUSER"};
# password to connect to the database
$password = $User_Preferences{"AMPDBPASS"};

################### END OF CONFIGURATION #######################

open( EXTEN, ">$extensions_conf" ) or die "Cannot create/overwrite extensions file: $extensions_conf (!$)\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
$statement = "SELECT * from $global_table_name order by variable DESC";
my $result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
  exit;
}
my @resultSet = @{$result};
if ( $#resultSet > -1 ) {
    print EXTEN "[globals]\n";
    print EXTEN "#include globals_custom.conf\n";
    foreach $row (@{ $result }) {
        my @result = @{ $row };
        print EXTEN "$result[0] = $result[1]\n";
    }
    print EXTEN "\n";
}

$statement = "SELECT context from $table_name group by context";

$result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
}

@resultSet = @{$result};
if ( $#resultSet == -1 ) {
  print "No extensions defined in $table_name\n";
  exit;
}

foreach my $row ( @{ $result } ) {
    my $context = @{ $row }[0];
    print EXTEN "[$context]\n";
    print EXTEN "include => $context-custom\n";
    $statement = "SELECT * from $table_name where context='$context' order by extension, priority";
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
        if ($result[6] == 0) {

            $result[2] =~ s/^0//g;

            print EXTEN "exten => $result[1],$result[2],$result[3]";

            if (defined $result[4] && $result[4] ne '') {
                print EXTEN "($result[4])";
            }

            if (defined $result[5] && $result[5] ne '') {
                print EXTEN "\t; $result[5]";
            }
            print EXTEN "\n";
        }
        elsif ($result[6] == 2) {
            print EXTEN "include => $result[3]\n";
        }
        # add voice mail extension
        if($context eq "ext-local" && ($result[4])) {
             if (($result[4] =~ m/,novm,/)) {

                print EXTEN "exten => $result[1],2,Hangup\n";

                } elsif (($result[4] =~ m/,jump,/)) {

                print EXTEN "exten => $result[1],2,Hangup\n";

                } else {

                print EXTEN "exten => $result[1],2,Hangup\n";
                print EXTEN "exten => \${VM_PREFIX}$result[1],1,Macro(vm,$result[1],DIRECTDIAL)\n";
                print EXTEN "exten => \${VM_PREFIX}$result[1],2,Hangup\n";

                }
        }
    }
    print EXTEN "\n";
}

    $statement = "SELECT * from $meetme_table_name order by exten";
    $result = $dbh->selectall_arrayref($statement);
    unless ($result) {
        print "dbh->selectall_arrayref($statement) failed!\n";
        print "DBI::err=[$DBI::err]\n";
        print "DBI::errstr=[$DBI::errstr]\n";
        exit;
    }

    my @resSet = @{$result};
    if ( $#resSet == -1 ) {
        print "no meetme results\n";
        exit;
    }

    print EXTEN "[ext-meetme]\n";
    print EXTEN "include => ext-meetme-custom\n";

    foreach my $row ( @{ $result } ) {
        my @result = @{ $row };


            print EXTEN "exten => $result[0],1,Macro(joinmeetme,$result[0],$result[1],$result[2],$result[3],$result[5])";
            print EXTEN "\t; $result[4]";
            print EXTEN "\n";
            print EXTEN "exten => $result[0],2,Hangup";
            print EXTEN "\n";

}

exit 0;
