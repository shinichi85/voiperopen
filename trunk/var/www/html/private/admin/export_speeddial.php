<?php
// Copyright (C) 2005-2008 SpheraIT

$filename="speeddial_".date("d-m-Y");
header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
header('content-type: text/csv');
require_once('functions.php');
require_once('./classes/speeddial/Datasource.php');
require_once('./classes/speeddial/SpeedEntry.php');
require_once('./classes/speeddial/SpeedEntryDAO.php');

$amp_conf = parse_amportal_conf("/etc/amportal.conf");

$ds = new DatasourceSpeeddial($amp_conf['AMPDBHOST'],
                     $amp_conf['AMPDBNAME'],
                     $amp_conf['AMPDBUSER'],
                     $amp_conf['AMPDBPASS']);
$dao = new SpeedEntryDAO();
//echo "<pre>"; print_r($amp_conf);

$results = $dao->readAll($ds);

  $stdout = fopen('php://output', 'w');
  if (count($results)>0) {
    fwrite($stdout,'"Number";"Callerid";"Forward";"Permission"'."\n");
    foreach ($results as $r) {
        $permission = $r->getPermission();
        if ($permission == "") {
            $permission = "NO";
        } 
        if ($permission == "CHECKED") {
            $permission = "YES";
        } 
        fwrite($stdout,'"'.$r->getNumber().'";"'.$r->getDescription().'";"'.$r->getTelnr().'";"'.$permission.'"'."\n");
    }//foreach
  }//if $results>0
  fclose($stdout);
?>
