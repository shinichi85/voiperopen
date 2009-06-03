<?php
include_once("common/iam_csvdump.php");
require_once('functions.php');

$amp_conf = parse_amportal_conf("/etc/amportal.conf");

$dbhost = $amp_conf['AMPDBHOST'];
$dbname = $amp_conf['AMPDBNAME'];
$dbuser = $amp_conf['AMPDBUSER'];
$dbpw = $amp_conf['AMPDBPASS'];

$dumpfile = new iam_csvdump;
$query = "(select data from sip where keyword = 'callerid') UNION (select data from iax where keyword = 'callerid') UNION (select data from zap where keyword = 'callerid') ORDER BY data ASC";
$dumpfile->dump($query, "local_extensions_". date("d-m-Y"), "csv", $dbname, $dbuser, $dbpw, $dbhost, "mysql" );

?>
