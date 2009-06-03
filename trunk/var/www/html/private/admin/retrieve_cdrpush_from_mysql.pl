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

$bi4sftp_table_name = "conf";
# WARNING: this file will be substituted by the output of this program
$bi4sftp_conf = "/etc/asterisk/bi4sftp.conf";
# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"AMPDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"AMPDBNAME"};
# username to connect to the database
$username = $User_Preferences{"AMPDBUSER"};
# password to connect to the database
$password = $User_Preferences{"AMPDBPASS"};

################### END OF CONFIGURATION #######################

open( EXTEN, ">$bi4sftp_conf" ) or die "Cannot create/overwrite configuration file: $bi4sftp_conf (!$)\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
my $sth = $dbh->prepare( "SELECT name,value FROM $bi4sftp_table_name WHERE name LIKE '%cdrpush%'" )
      or die "Can't prepare SQL statement: $DBI::errstr\n";
$sth->execute
      or die "Can't execute SQL statement: $DBI::errstr\n";
my @row;
while (@row = $sth->fetchrow_array()) {
    push (@mycol,$row[1]);
}
print EXTEN "[parameters]\n";
print EXTEN "username=$mycol[0]\n";
print EXTEN "password=$mycol[1]\n";
print EXTEN "host address:port=$mycol[2]:$mycol[3]\n";
if ($mycol[4] && $mycol[5]) {
    print EXTEN "host address2:port2=$mycol[4]:$mycol[5]\n";
} else {
        print EXTEN ";host address2:port2=$mycol[4]:$mycol[5]\n";
}
print EXTEN "device filename=$mycol[6]\n";
print EXTEN "process old files=$mycol[8]\n";
print EXTEN "user filename=$mycol[9]\n";
print EXTEN "transfer period (s)=$mycol[7]\n";
print EXTEN "idle interval=$mycol[10]\n";
print EXTEN "minimum file size for transfer (kb)=$mycol[11]\n";
print EXTEN "maximum period for transfer (m)=$mycol[12]\n";
print EXTEN "\n";
close(EXTEN);

my $runservice = `/usr/bin/pgrep bi4sftp`;
if ($runservice) { exec "sudo /sbin/service bi4sftpd restart > /dev/null 2>&1" or die "Unable to exec bi4sftpd: $!\n"; };
exit 0;
