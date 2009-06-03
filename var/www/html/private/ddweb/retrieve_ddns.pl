#!/usr/bin/perl -Tw
## Coding by XAD of Nightfall
## Copyright by SpheraIT
## GPL Source

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

$ddns_table_name = "ddns";
$ddclient_conf = "/etc/ddclient/ddclient.conf";
$hostname = $User_Preferences{"AMPDBHOST"};
$database = $User_Preferences{"AMPDBNAME"};
$username = $User_Preferences{"AMPDBUSER"};
$password = $User_Preferences{"AMPDBPASS"};

open EXTEN, ">$ddclient_conf" or die "Cannot create/overwrite ddclient config file: $ddclient_conf\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
$statement = "SELECT * from $ddns_table_name";
my $result = $dbh->selectall_arrayref($statement);
unless ($result) {
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
  exit;
}
my @resultSet = @{$result};
if ( $#resultSet > -1 ) {

	print EXTEN "pid=/var/run/ddclient.pid\n";

	foreach $row (@{ $result }) {
		my @result = @{ $row };
		
		
		if ($result[0] eq "host") {
		
			print EXTEN "$result[1]\n";

			}
		
			else {
		
		print EXTEN "$result[0]=$result[1]\n";

			}
		}
}

exit 0;