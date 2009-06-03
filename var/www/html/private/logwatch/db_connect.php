<?php

// Coding by XAD of Nightfall
// Copyright by SpheraIT
// GPL Source

require_once('DB.php');

function parse_amportal_conf($filename) {
        $file = file($filename);
        foreach ($file as $line) {
                if (preg_match("/^\s*([a-zA-Z0-9]+)\s*=\s*(.*)\s*([;#].*)?/",$line,$matches)) {
                        $conf[ $matches[1] ] = $matches[2];
                }
        }
        return $conf;
}

$amp_conf = parse_amportal_conf("/etc/amportal.conf");

$db_user = $amp_conf["AMPDBUSER"];
$db_pass = $amp_conf["AMPDBPASS"];
$db_host = $amp_conf["AMPDBHOST"];
$db_name = $amp_conf["AMPDBNAME"];
$db_engine = 'mysql';

$datasource = $db_engine.'://'.$db_user.':'.$db_pass.'@'.$db_host.'/'.$db_name;

$db = DB::connect($datasource);

if(DB::isError($db)) {
	die($db->getDebugInfo()); 
}
