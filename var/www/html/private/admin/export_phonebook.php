<?php
// Copyright (C) 2005-2008 SpheraIT

$filename="phonebook_".date("d-m-Y");
header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
header('Content-Type: application/csv');
require_once('functions.php');
require_once('./classes/phonebook/Datasource.php');
require_once('./classes/phonebook/PhoneEntry.php');
require_once('./classes/phonebook/PhoneEntryDAO.php');

$amp_conf = parse_amportal_conf("/etc/amportal.conf");

$ds = new Datasource($amp_conf['PHONEBOOKDBHOST'],
                     $amp_conf['PHONEBOOKDBNAME'],
                     $amp_conf['PHONEBOOKDBUSER'],
                     $amp_conf['PHONEBOOKDBPASS']);
$dao = new PhoneEntryDAO();
//echo "<pre>"; print_r($amp_conf);

$results = $dao->readAll($ds);

  $stdout = fopen('php://output', 'w');
  if (count($results)>0) {
    fwrite($stdout,'"Description";"Number"'."\n");
    foreach ($results as $r) {
      $number = $r->getNumber();
      settype($number,'string');
//      fputcsv($stdout,array(0=>$number,1=>' '.$r->getDescription()));
        fwrite($stdout,'"'.$r->getDescription().'";"'.$r->getNumber().'"'."\n");
    }//foreach
  }//if $results>0
  fclose($stdout);
?>