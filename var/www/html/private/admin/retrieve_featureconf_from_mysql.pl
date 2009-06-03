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
$vm_table_name = "featureconfig";

# WARNING: this file will be substituted by the output of this program
$feature_conf = "/etc/asterisk/features_additional.conf";
# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"AMPDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"AMPDBNAME"};
# username to connect to the database
$username = $User_Preferences{"AMPDBUSER"};
# password to connect to the database
$password = $User_Preferences{"AMPDBPASS"};

################### END OF CONFIGURATION #######################

open( EXTEN, ">$feature_conf" ) or die "Cannot create/overwrite extensions file: $feature_conf (!$)\n";

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
	foreach $row (@{ $result }) {
		my @result = @{ $row };

		$parkext = $result[1];
		$parkpos = $result[2];
		$context = $result[3];
		$parkingtime = $result[4];
		$transferdigittimeout = $result[5];
		$courtesytone = $result[6];
		$pickupexten = $result[7];
		$xfersound = $result[8];
		$xferfailsound = $result[9];
		$featuredigittimeout = $result[10];
		$blindxfer = $result[11];
		$disconnect = $result[12];
		$automon = $result[13];
		$atxfer = $result[14];
		$adsipark = $result[15];
		$testfeature = $result[16];


		print EXTEN "parkext => $parkext\n";
		print EXTEN "parkpos => $parkpos\n";
		print EXTEN "context => $context\n";
		print EXTEN "parkingtime => $parkingtime\n";
		print EXTEN "transferdigittimeout => $transferdigittimeout\n";
		print EXTEN "courtesytone = $courtesytone\n";
		print EXTEN "pickupexten => $pickupexten\n";
		print EXTEN "xfersound => $xfersound\n";
		print EXTEN "xferfailsound => $xferfailsound\n";
		print EXTEN "featuredigittimeout => $featuredigittimeout\n";
		print EXTEN "adsipark => $adsipark\n";

		print EXTEN "\n";
		print EXTEN "[featuremap]\n";

		print EXTEN "blindxfer => $blindxfer\n";
		print EXTEN "disconnect => $disconnect\n";
		print EXTEN "automon => $automon\n";
		print EXTEN "atxfer => $atxfer\n";

		print EXTEN "\n";
		print EXTEN "[applicationmap]\n";

		print EXTEN "voiperapps => $testfeature\n";

		}
	print EXTEN "\n";
}

exit 0;
