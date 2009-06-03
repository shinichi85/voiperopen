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
$iax_table_name = "iaxconf";
# WARNING: this file will be substituted by the output of this program
$iax_conf = "/etc/asterisk/iax_default.conf";

# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"AMPDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"AMPDBNAME"};
# username to connect to the database
$username = $User_Preferences{"AMPDBUSER"};
# password to connect to the database
$password = $User_Preferences{"AMPDBPASS"};

################### END OF CONFIGURATION #######################

open( EXTEN, ">$iax_conf" ) or die "Cannot create/overwrite extensions file: $iax_conf (!$)\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
$statement = "SELECT * FROM $iax_table_name order by id";
my $result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
  exit;
}

#print every iax conf line
my @resultSet = @{$result};
if ( $#resultSet > -1 ) {
	print EXTEN "\n";
	foreach $row (@{ $result }) {
		my @result = @{ $row };
		$bindport = $result[1];
		$bindaddr = $result[2];
		$disallow = $result[3];
#		$allow = $result[4];
		$mailboxdetail = $result[5];
		$iaxcompat = $result[6];
		$delayreject = $result[7];
		$language = $result[8];
		$bandwidth = $result[9];
		$jitterbuffer = $result[10];
		$tos = $result[11];
		$autokill = $result[12];
		$trunkfreq = $result[13];
		$authdebug = $result[14];
		$amaflags = $result[15];
		$accountcode = $result[16];
		$dropcount = $result[17];
		$maxjitterbuffer = $result[18];
		$maxexcessbuffer = $result[19];
		$minexcessbuffer = $result[20];
		$jittershrinkrate = $result[21];
		$trunktimestamps = $result[22];
		$minregexpire = $result[23];
		$maxregexpire = $result[24];
		$iaxthreadcount = $result[25];
		$iaxmaxthreadcount = $result[26];

		print EXTEN "bindport = $bindport\n";
		print EXTEN "bindaddr = $bindaddr\n";
		print EXTEN "disallow = $disallow\n";

	foreach my $row ( @{ $result } ) {
		my @result = @{ $row };
	        @opts=split("&",$result[4]);
	        foreach $opt (@opts) {
			print EXTEN "allow = $opt\n";
		}
	}

		print EXTEN "mailboxdetail = $mailboxdetail\n";
		print EXTEN "iaxcompat = $iaxcompat\n";
		print EXTEN "delayreject = $delayreject\n";
		print EXTEN "language = $language\n";
		print EXTEN "bandwidth = $bandwidth\n";
		print EXTEN "jitterbuffer = $jitterbuffer\n";
		print EXTEN "tos = $tos\n";
		print EXTEN "autokill = $autokill\n";
		print EXTEN "trunkfreq = $trunkfreq\n";
		print EXTEN "authdebug = $authdebug\n";
		print EXTEN "amaflags = $amaflags\n";
		print EXTEN "accountcode = $accountcode\n";
		print EXTEN "dropcount = $dropcount\n";
		print EXTEN "maxjitterbuffer = $maxjitterbuffer\n";
		print EXTEN "maxexcessbuffer = $maxexcessbuffer\n";
		print EXTEN "minexcessbuffer = $minexcessbuffer\n";
		print EXTEN "jittershrinkrate = $jittershrinkrate\n";
		print EXTEN "trunktimestamps = $trunktimestamps\n";
		print EXTEN "minregexpire = $minregexpire\n";
		print EXTEN "maxregexpire = $maxregexpire\n";
		print EXTEN "iaxthreadcount = $iaxthreadcount\n";
		print EXTEN "iaxmaxthreadcount = $iaxmaxthreadcount\n";

		}
	print EXTEN "\n";
}

exit 0;
