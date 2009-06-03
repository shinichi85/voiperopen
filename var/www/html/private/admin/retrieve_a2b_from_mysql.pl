#!/usr/bin/perl
# Davide.Gustin (C) SpheraIT

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

# the name of the globals table
$table_name = "cc_sip_buddies";
# the path to the extensions.conf file
# WARNING: this file will be substituted by the output of this program
$extensions_conf = "/etc/asterisk/extensions_additional_a2billing.conf";
# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"A2BDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"A2BDBNAME"};
# username to connect to the database
$username = $User_Preferences{"A2BDBUSER"};
# password to connect to the database
$password = $User_Preferences{"A2BDBPASS"};
# check if a2billing enabled
$a2benabled = $User_Preferences{"A2BENABLED"};
################### END OF CONFIGURATION #######################

if ($a2benabled eq "yes") {

    $dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password", { PrintError => 0 }) or exit(1);

    open( EXTEN, ">$extensions_conf" ) or die "Cannot create/overwrite extensions file: $extensions_conf (!$)\n";

    $statement = "SELECT username from $table_name order by id";
    $result = $dbh->selectall_arrayref($statement);
    unless ($result) {
        print "dbh->selectall_arrayref($statement) failed!\n";
        print "DBI::err=[$DBI::err]\n";
        print "DBI::errstr=[$DBI::errstr]\n";
        exit;
    }

    my @resSet = @{$result};
    if ( $#resSet == -1 ) {
        print "no a2billing sip users found\n";
        exit;
    }

    print EXTEN "[ext-local-a2b]\n";
    print EXTEN "\n";

    foreach my $row ( @{ $result } ) {
        my @result = @{ $row };

            print EXTEN "exten => $result[0],1,Macro(exten-vm,novm,$result[0])\n";
            print EXTEN "exten => $result[0],2,Hangup\n";
            print EXTEN "exten => $result[0],hint,SIP/$result[0]\n";
            print EXTEN "\n";
    }

}

exit 0;

