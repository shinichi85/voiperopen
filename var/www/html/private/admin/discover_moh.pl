#!/usr/bin/perl
# Check wether mpg123 or madplay is installed. Whinge and complain if they're
# not. Tell the database either way. madplay is preferred over mpg123.
# Released under the GPL V2. (C) Rob Thomas rob@wpm4L.com

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

my @p = (split /:/, $ENV{'PATH'});
my ($mpg123,$madplay) = ("notfound", "notfound");
my $mohcmd = "";

while (my $dir = shift @p) {
	$mpg123 = $dir if (stat("$dir/mpg123"));
	$madplay = $dir if (stat("$dir/madplay"));
}
if ($madplay eq "notfound" && $mpg123 eq "notfound") {
	print "ALERT: madplay or mpg123 not found. Aborting.\n";
	exit -1;
}
if ($madplay ne "notfound") {
	# We're happy. Don't need to care (too much) about the version.
	$mohcmd="$madplay/madplay";
	} else {
	# We have mpg123. Need to check to make sure it's a correct version
	open (MPG123, "$mpg123/mpg123 --help|");
	<MPG123>;
	$vers = <MPG123>;
	if ($vers !~ /^Version 0.59r/) {
		print "ALERT: madplay not found and mpg123 version is NOT 0.59r. Aborting.\n";
		exit -1;
	}
	$mohcmd="mgp123";
}

# the name of the box the MySQL database is running on
$hostname = $User_Preferences{"AMPDBHOST"};
# the name of the database our tables are kept
$database = $User_Preferences{"AMPDBNAME"};
# username to connect to the database
$username = $User_Preferences{"AMPDBUSER"};
# password to connect to the database
$password = $User_Preferences{"AMPDBPASS"};

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
$q=$dbh->prepare("select * from globals where variable=?");
$s=$dbh->prepare("update globals set value=? where variable=?");
$q->execute("MOH_COMMAND");
if ($q->rows == 0) {
	$dbh->do("insert into globals values('MOH_COMMAND', '$mohcmd')");
} else {
	$s->execute($mohcmd, "MOH_COMMAND");
}
$q->execute("MOH_VOLUME");
# If the volume is already there, don't screw with it..
if ($q->rows == 0) {
	$dbh->do("insert into globals values('MOH_VOLUME', '-12')");
}
