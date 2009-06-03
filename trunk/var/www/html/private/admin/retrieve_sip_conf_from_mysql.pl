#!/usr/bin/perl -w
# Retrieves the sip user/peer entries from the database
# Use these commands to create the appropriate tables in MySQL
#
#CREATE TABLE sip (id INT(11) DEFAULT -1 NOT NULL,keyword VARCHAR(20) NOT NULL,data VARCHAR(50) NOT NULL, flags INT(1) DEFAULT 0 NOT NULL,PRIMARY KEY (id,keyword));
#
# if flags = 1 then the records are not included in the output file

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
$table_name = "sip";
# the path to the extensions.conf file
# WARNING: this file will be substituted by the output of this program
$sip_conf = "/etc/asterisk/sip_additional.conf";
$sip_reg = "/etc/asterisk/sip_registrations.conf";

# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"AMPDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"AMPDBNAME"};
# username to connect to the database
$username = $User_Preferences{"AMPDBUSER"};
# password to connect to the database
$password = $User_Preferences{"AMPDBPASS"};

################### END OF CONFIGURATION #######################

$additional = "";

open( EXTEN, ">$sip_conf" ) or die "Cannot create/overwrite extensions file: $sip_conf (!$)\n";
open( REG, ">$sip_reg" ) or die "Cannot create/overwrite extensions file: $sip_reg (!$)\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
$statement = "SELECT keyword,data from $table_name where id=0 and keyword <> 'account' and flags <> 1";
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
	foreach $row (@{ $result }) {
		my @result = @{ $row };
		$additional .= $result[0]."=".$result[1]."\n";
	}
}

# items with id like 9999999% get put at the top of the file - these are only registration strings, currently.
$statement = "SELECT keyword,data from $table_name where id LIKE '9999999%' and keyword <> 'account' and flags <> 1";
$result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
  exit;
}
@resultSet = @{$result};
if ( $#resultSet > -1 ) {
	foreach $row (@{ $result }) {
		my @result = @{ $row };
		$top .= $result[0]."=".$result[1]."\n";
	}
		print REG "$top\n";
}
# select for unique accounts
$statement = "SELECT data,id from $table_name where keyword='account' and flags <> 1 group by data";
$result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
}

@resultSet = @{$result};
if ( $#resultSet == -1 ) {
  print "No sip accounts defined in $table_name\n";
  exit;
}

#get the details for each account found above
foreach my $row ( @{ $result } ) {
	my $account = @{ $row }[0];
	my $id = @{ $row }[1];
	print EXTEN "[$account]\n";
	$statement = "SELECT keyword,data from $table_name where id=$id and keyword <> 'account' and keyword <> 'allowcall' and keyword <> 'cw' and keyword <> 'nocall' and keyword <> 'record_in' and keyword <> 'record_out' and keyword <> 'rob' and flags <> 1 order by keyword DESC";
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
		print "No Outgoind Trunk settings Found .. I will continue...\n";
#		exit;
	}
	foreach my $row ( @{ $result } ) {
		my @result = @{ $row };
	        @opts=split("&",$result[1]);
	        foreach $opt (@opts) {
			print EXTEN "$result[0]=$opt\n";
		}
	}
	print EXTEN "$additional\n";
}
close EXTEN;
close REG;
exit 0;
