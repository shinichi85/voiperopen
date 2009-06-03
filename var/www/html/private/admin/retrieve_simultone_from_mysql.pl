#!/usr/bin/perl -w
# Use these commands to create the appropriate tables in MySQL
#
# mysql -p asterisk < simultone.sql
#
#CREATE TABLE `simultone` (
#  `id` int(11) NOT NULL auto_increment,
#  `trunk_num` varchar(255) default NULL,
#  `simul_num` varchar(255) default NULL,
#  PRIMARY KEY  (`id`)
#) TYPE=MyISAM;

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
$table_name = "simultone";
# the path to the conf file
# WARNING: this file will be substituted by the output of this program
$tone_conf = "/etc/asterisk/extensions_tone.conf";

# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"AMPDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"AMPDBNAME"};
# username to connect to the database
$username = $User_Preferences{"AMPDBUSER"};
# password to connect to the database
$password = $User_Preferences{"AMPDBPASS"};

################### END OF CONFIGURATION #######################

open( EXTEN, ">$tone_conf" ) or die "Cannot create/overwrite extensions file: $tone_conf (!$)\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
$statement = "SELECT trunk_num,simul_num,description from $table_name order by id";
my $result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
  exit;
}

	print EXTEN "[custom-simultone]\n";
	print EXTEN "\n";

my @resultSet = @{$result};
if ( $#resultSet > -1 ) {
	print EXTEN "\n";
	foreach $row (@{ $result }) {
		my @result = @{ $row };
		$trunk_num = $result[0];
		$simul_num = $result[1];
		$description = $result[2];


		print EXTEN "exten => $simul_num,1,Macro(tono_simulato,\${EXTEN},voiper-trunk-$trunk_num,";

		if ( length($simul_num) > 1 ) {
			print EXTEN "2";
		} else {
 			print EXTEN "1";
		}

		print EXTEN ",dial)";

		print EXTEN " ; $description";
		print EXTEN "\n";
		print EXTEN "exten => $simul_num,2,Hangup\n";
		print EXTEN "\n";

	}

	foreach $row (@{ $result }) {
		my @result = @{ $row };
		$trunk_num = $result[0];
		$trunk_cut = substr($trunk_num, 4);


		print EXTEN "[voiper-trunk-$trunk_num]\n";
		print EXTEN "exten => _X.,1,Macro(dialout-trunk_simul,$trunk_cut,\${EXTEN},)\n";
		print EXTEN "exten => _X.,2,Hangup\n";
#		print EXTEN "exten => _X.,2,Macro(outisbusy)\n";
		print EXTEN "\n";

	}
	print EXTEN "\n";
}

exit 0;
