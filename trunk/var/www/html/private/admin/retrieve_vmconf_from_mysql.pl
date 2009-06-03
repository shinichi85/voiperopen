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

# the name of the speeddial table
$vm_table_name = "vmconfig";

# WARNING: this file will be substituted by the output of this program
$vm_conf = "/etc/asterisk/vm_general.inc";
# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"AMPDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"AMPDBNAME"};
# username to connect to the database
$username = $User_Preferences{"AMPDBUSER"};
# password to connect to the database
$password = $User_Preferences{"AMPDBPASS"};

################### END OF CONFIGURATION #######################

open( EXTEN, ">$vm_conf" ) or die "Cannot create/overwrite extensions file: $vm_conf (!$)\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
$statement = "SELECT * FROM $vm_table_name order by id";
my $result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
  exit;
}

#print every vm conf line
my @resultSet = @{$result};
if ( $#resultSet > -1 ) {
	print EXTEN "\n";
	foreach $row (@{ $result }) {
		my @result = @{ $row };
		$format = $result[1];
		$serveremail = $result[2];
		$attach = $result[3];
		$maxmessage = $result[4];
		$minmessage = $result[5];
		$skipms = $result[6];
		$maxsilence = $result[7];
		$silencethreshold = $result[8];
		$maxlogins = $result[9];
		$pbxskip = $result[10];
		$fromstring = $result[11];
		$sendvoicemail = $result[12];
		$review = $result[13];
		$operator = $result[14];
		$emailsubject = $result[15];
		$emailbody = $result[16];
		$maxmsg = $result[17];
		$maxgreet = $result[18];
		$externnotify = $result[19];
		$externpass = $result[20];

		print EXTEN "format = $format\n";
		print EXTEN "serveremail = $serveremail\n";
		print EXTEN "attach = $attach\n";
		print EXTEN "maxmessage = $maxmessage\n";
		print EXTEN "minmessage = $minmessage\n";
		print EXTEN "skipms = $skipms\n";
		print EXTEN "maxsilence = $maxsilence\n";
		print EXTEN "silencethreshold = $silencethreshold\n";
		print EXTEN "maxlogins = $maxlogins\n";
		print EXTEN "pbxskip = $pbxskip\n";
		print EXTEN "fromstring = $fromstring\n";
		print EXTEN "sendvoicemail = $sendvoicemail\n";
		print EXTEN "review = $review\n";
		print EXTEN "operator = $operator\n";
		print EXTEN "maxmsg = $maxmsg\n";
		print EXTEN "maxgreet = $maxgreet\n";
		print EXTEN "externnotify = $externnotify\n";
		print EXTEN "externpass = $externpass\n";
		print EXTEN "emailsubject = $emailsubject\n";
		print EXTEN "emailbody = $emailbody\n";


		}
	print EXTEN "\n";
}

exit 0;
