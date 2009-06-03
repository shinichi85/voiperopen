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
$speed_table_name = "speednr";
# the path to the extensions.conf file
# WARNING: this file will be substituted by the output of this program
$speed_conf = "/etc/asterisk/extensions_custom_speeddial.conf";

# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"AMPDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"AMPDBNAME"};
# username to connect to the database
$username = $User_Preferences{"AMPDBUSER"};
# password to connect to the database
$password = $User_Preferences{"AMPDBPASS"};

################### END OF CONFIGURATION #######################

open( EXTEN, ">$speed_conf" ) or die "Cannot create/overwrite extensions file: $speed_conf (!$)\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
$statement = "SELECT speednr,telnr,name,permission from $speed_table_name order by speednr";
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

	print EXTEN "[custom-speeddial]\n";
	foreach $row (@{ $result }) {
		my @result = @{ $row };
		$speednr = $result[1];
		$speednr =~ s/ //g;
		$cidesc = $result[2];
		$cidesc =~ s/'/\\'/g; 
		$permission = $result[3];

		if ($cidesc){
			print EXTEN "exten => $result[0],1,Set(CALLERID(name)=".$cidesc.")\n";
		} else {
				print EXTEN "exten => $result[0],1,Noop(Default CalleridName are used.)\n";
		}

		if ($permission){
			print EXTEN "exten => $result[0],n,Set(SPEEDDIALPERM=$permission)\n";		
		} else {
				print EXTEN "exten => $result[0],n,Noop(Default Call Permission are used.)\n";
		}
		
		print EXTEN "exten => $result[0],n,Goto(from-internal,".$speednr.",1)\n";
	}
	print EXTEN "\n";
}

exit 0;
