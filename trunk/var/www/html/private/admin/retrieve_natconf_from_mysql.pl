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
$nat_table_name = "natconf";

$rtp_table_name = "rtpconf";

# WARNING: this file will be substituted by the output of this program
$nat_conf = "/etc/asterisk/sip_nat.conf";

$rtp_conf = "/etc/asterisk/rtp.conf";

# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"AMPDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"AMPDBNAME"};
# username to connect to the database
$username = $User_Preferences{"AMPDBUSER"};
# password to connect to the database
$password = $User_Preferences{"AMPDBPASS"};

################### END OF CONFIGURATION #######################

open( EXTEN, ">$nat_conf" ) or die "Cannot create/overwrite extensions file: $nat_conf (!$)\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
$statement = "SELECT * FROM $nat_table_name order by id";
my $result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
  exit;
}

#print every nat conf line
my @resultSet = @{$result};
if ( $#resultSet > -1 ) {
	print EXTEN "\n";
	foreach $row (@{ $result }) {
		my @result = @{ $row };
		$port = $result[1];
		$bindaddr = $result[2];
		$disallow = $result[3];
		$context = $result[5];
		$callerid = $result[6];
		$language = $result[7];
		$registertimeout = $result[8];
		$useragent = $result[9];
		$checkmwi = $result[10];
		$srvlookup = $result[11];
		$maxexpirey = $result[12];
		$defaultexpirey = $result[13];
		$allowguest = $result[14];
		$usereqphone = $result[15];
		$tos_sip = $result[16];
		$videosupport = $result[17];
		$rtptimeout = $result[18];
		$rtpholdtimeout = $result[19];
		$recordhistory = $result[20];
		$nat = $result[21];
		$relaxdtmf = $result[22];
		$musicclass = $result[23];
		$externip = $result[24];
		$localnet = $result[25];
		$externrefresh = $result[26];
		$externhost = $result[27];
		$autodomain = $result[28];
		$registerattempts = $result[29];
		$notifyringing = $result[30];
		$insecure = $result[31];
		$progressinband = $result[32];
		$pedantic = $result[33];
		$limitonpeer = $result[34];
		$notifyhold = $result[35];
		$allowsubscribe = $result[36];
		$tos_audio = $result[37];
		$tos_video = $result[38];
		$t38pt_udptl = $result[39];
		$rtpkeepalive = $result[40];
		
		print EXTEN "bindport = $port\n";
		print EXTEN "bindaddr = $bindaddr\n";
		print EXTEN "disallow = $disallow\n";

	foreach my $row ( @{ $result } ) {
		my @result = @{ $row };
	        @opts=split("&",$result[4]);
	        foreach $opt (@opts) {
			print EXTEN "allow = $opt\n";
		}
	}

		print EXTEN "context = $context\n";
		print EXTEN "callerid = $callerid\n";
		print EXTEN "language = $language\n";
		print EXTEN "registertimeout = $registertimeout\n";
		print EXTEN "registerattempts = $registerattempts\n";
		print EXTEN "useragent = $useragent\n";
		print EXTEN "checkmwi = $checkmwi\n";
		print EXTEN "srvlookup = $srvlookup\n";
		print EXTEN "maxexpirey = $maxexpirey\n";
		print EXTEN "defaultexpirey = $defaultexpirey\n";
		print EXTEN "allowguest = $allowguest\n";
		print EXTEN "usereqphone = $usereqphone\n";

        if ($tos_sip){

		    print EXTEN "tos_sip = $tos_sip\n";

        }

        if ($tos_audio){
        
		print EXTEN "tos_audio = $tos_audio\n";

        }

        if ($tos_video){

		print EXTEN "tos_video = $tos_video\n";

        }

		print EXTEN "videosupport = $videosupport\n";
		print EXTEN "rtptimeout = $rtptimeout\n";
		print EXTEN "rtpholdtimeout = $rtpholdtimeout\n";
		print EXTEN "recordhistory = $recordhistory\n";
		print EXTEN "nat = $nat\n";
		print EXTEN "relaxdtmf = $relaxdtmf\n";
		print EXTEN "musicclass = $musicclass\n";

			if ($externip){
				print EXTEN "externip = $externip\n";
			}

			if ($localnet){

                my @localnetvalues = split(';', $localnet);
                foreach my $finalvalues (@localnetvalues) {
   			        print EXTEN "localnet = $finalvalues\n";
                }
			}

			if ($externrefresh){
				print EXTEN "externrefresh = $externrefresh\n";
			}

			if ($externhost){
				print EXTEN "externhost = $externhost\n";
			}

		print EXTEN "autodomain = $autodomain\n";
		print EXTEN "notifyringing = $notifyringing\n";
		print EXTEN "insecure = $insecure\n";
		print EXTEN "progressinband = $progressinband\n";
		print EXTEN "pedantic = $pedantic\n";
		print EXTEN "limitonpeer = $limitonpeer\n";
		print EXTEN "notifyhold = $notifyhold\n";
		print EXTEN "allowsubscribe = $allowsubscribe\n";
		print EXTEN "t38pt_udptl = $t38pt_udptl\n";
		print EXTEN "rtpkeepalive = $rtpkeepalive\n";

		}
	print EXTEN "\n";
}

open EXTEN, ">$rtp_conf" or die "Cannot create/overwrite extensions file: $rtp_conf\n";

$dbhrtp = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
$statement = "SELECT * FROM $rtp_table_name order by id";
my $resultrtp = $dbhrtp->selectall_arrayref($statement);
unless ($resultrtp) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
  exit;
}

#print every rtp conf line
my @resultSetRtp = @{$resultrtp};
if ( $#resultSetRtp > -1 ) {
	foreach $row (@{ $resultrtp }) {
		my @resultrtp = @{ $row };
		$rtpstart= $resultrtp[1];
		$rtpend = $resultrtp[2];

		print EXTEN "[general]";
		print EXTEN "\n";
		print EXTEN "rtpstart = $rtpstart\n";
		print EXTEN "rtpend = $rtpend\n";

		}
}

exit 0;
