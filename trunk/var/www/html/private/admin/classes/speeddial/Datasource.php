<?php
/**
  \file Datasource.php
  \brief datastream manager
  \date 14/apr/2008 24/apr/2008
  \author A.Santin
*/

class DatasourceSpeeddial extends PDO {
//  protected $conn;

  function __construct($dbHost, $dbName, $dbuser, $dbpasswd) {
//    $this->conn = new PDO('mysql:host=localhost;dbname=ivrcruscotto', 'asteriskuser', 'amp109');
    parent::__construct("mysql:host=$dbHost;dbname=$dbName", $dbuser, $dbpasswd);
    //autocommit=false
  }//__construct

//------------------------------------------------------------------
// Datasource = PDO

  function execute(&$sql) {
//    $result = $this->conn->exec($sql);
    $result = $this->exec($sql);
    return $result;
  }

  function makequery(&$query) {
//    $result = $this->conn->query($query);
    $result = $this->query($query);
    return $result;
  }

  function nextRow(&$result) {
//    $row = $conn->nextRow($result);
    $row = $result->fetch();
    return $row;
  }
}

?>
