<?php
/**
  \file PhoneEntryDAO.php
  \brief phonebook PhoneEntryDAO
  \date 14/apr/2008 24/apr/2008
  \author A.Santin
*/

class PhoneEntryDAO {

  protected $pager_items;
  protected $pager_totalpages;
  protected $pager_lastorderby;
  protected $pager_lastorderbydir;

  function __construct() {
  }//__construct

//------------------------------------------------------------------
// Datasource = PDO
// move in Datasource class???
/*
  function execute(&$conn, &$sql) {
    $result = $conn->exec($sql);
    return $result;
  }

  function makequery(&$conn, &$query) {
    $result = $conn->query($query);
    return $result;
  }

  function nextRow(&$conn, &$result) {
//    $row = $conn->nextRow($result);
    $row = $result->fetch();
    return $row;
  }
*/
//------------------------------------------------------------------

  /*
   * insert a new PhoneEntry into DB
   * @param &$conn database connection
   * @param &$valueObject
   * @return
   */
  function create(&$conn, &$valueObject) {
    //ignore phonebook_id
    $number = $valueObject->getNumber();
    $description = $valueObject->getDescription();
	if (!is_numeric($description)) {
		$description = "'" . mysql_real_escape_string($description) . "'";
	}
		$sql = "INSERT INTO phonebook
              (number,description) VALUES
              ('$number',$description);";

    $result = $conn->execute($sql);

    return $result;
  }//create

  /*
   * save a PhoneEntry into DB (insert or update as needed)
   * @param &$conn database connection
   * @param &$valueObject
   * @return
   */
  function save(&$conn, &$valueObject) {
    $phonebook_id = $valueObject->getID();
    $number = $valueObject->getNumber();
	$description = $valueObject->getDescription();
	if (!is_numeric($description)) {
		$description = "'" . mysql_real_escape_string($description) . "'";
	}
//    $searchentry = $this->search(&$conn, $oneentry);
//    if (count($searchentry)>0) {
//      if ($phonebook_id==0) $phonebook_id = $searchentry[0]->getID();
    if ($phonebook_id>0) {
      $sql = "UPDATE phonebook SET
                number='$number',
                description=$description
                  WHERE phonebook_id=$phonebook_id";
    } else {
      $sql = "INSERT INTO phonebook
                (number,description) VALUES
                ('$number',$description);";
    }

    $result = $conn->execute($sql);

    return true;
  }//save

  /*
   * read one PhoneEntry from phonebook
   * @param &$conn database connection
   * @param $phonebook_id database id
   * @return PhoneEntry object
   */
  function read(&$conn, $phonebook_id) {
    $one_entry = new PhoneEntry();
    $sql = "SELECT
              phonebook_id,
              number,
              description
                FROM phonebook
                  WHERE phonebook_id=$phonebook_id";
    $result = $conn->makequery($sql);
    $row = $conn->nextRow($result);
    $one_entry->setID($row[0]);
    $one_entry->setNumber($row[1]);
    $one_entry->setDescription($row[2]);

    return $one_entry;
  }//read

  /*
   * read all phonebook
   * @param &$conn database connection
   * @return array of PhoneEntry objects
   */
  function readAll(&$conn) {
    $sql = "SELECT
              phonebook_id,
              number,
              description
                FROM phonebook ORDER by description ASC";
    $result = $conn->makequery($sql);
    $arr_obj = array();
    while (($result) and ($row = $conn->nextRow($result))) {
      $one_entry = new PhoneEntry();
      $one_entry->setID($row[0]);
      $one_entry->setNumber($row[1]);
      $one_entry->setDescription($row[2]);
      $arr_obj[] = $one_entry;
//      $two_entry = new PhoneEntry();
//      $two_entry = $one_entry->cloneEntry();
//      $arr_obj[] = $two_entry;
    }

    return $arr_obj;
  }//readAll

  /*
   * delete one PhoneEntry from phonebook
   * @param &$conn database connection
   * @param $phonebook_id database id
   * @return true
   */
  function delete(&$conn, &$valueObject) {
    $one_entry = new PhoneEntry();
    $sql = "DELETE FROM phonebook
              WHERE 1=1 ";

    $phonebook_id = $valueObject->getID();
    $number = $valueObject->getNumber();
    $description = $valueObject->getDescription();

    if ($phonebook_id > 0) {
        if ($full) { $full = false; }
        $sql = $sql."AND phonebook_id=$phonebook_id ";
    }

    if ($number != "") {
        if ($full) { $full = false; }
        $sql = $sql."AND number LIKE '$number' ";
    }

    if ($description != "") {
        if ($full) { $full = false; }
        $sql = $sql."AND description LIKE '$description' ";
    }

    $result = $conn->execute($sql);

    return true;
  }//delete

  /*
   * count all phonebook entries
   * @param &$conn database connection
   * @return number of entries
   */
  function countAll(&$conn) {
    $sql = "SELECT count(phonebook_id) FROM phonebook";
    $allRows = 0;
    $result = $conn->makequery($sql);
    if ($row = $conn->nextRow($result)) {
      $allRows = $row[0];
    }

    return $allRows;
  }//countAll

  /*
   * search phonebook entries
   * @param &$conn database connection
   * @param &$valueObject search for one or more matching
   * @return
   */
  function search(&$conn, &$valueObject) {
    $full = true;
    $sql = "SELECT
              phonebook_id,
              number,
              description
                FROM phonebook WHERE 1=1 ";

    $phonebook_id = $valueObject->getID();
    $number = $valueObject->getNumber();
    $description = $valueObject->getDescription();

    if ($phonebook_id > 0) {
        if ($full) { $full = false; }
        $sql = $sql."AND phonebook_id=$phonebook_id ";
    }

    if ($number != "") {
        if ($full) { $full = false; }
        $sql = $sql."AND number LIKE '$number' ";
    }

    if ($description != "") {
        if ($full) { $full = false; }
        $sql = $sql."AND description LIKE '$description' ";
    }

    $sql = $sql."ORDER BY number ASC ";

    // Prevent accidential full table results.
    // Use loadAll if all rows must be returned.
    if ($full) {
      return array();
    }

    $result = $conn->makequery($sql);
    $arr_obj = array();
    while (($result) and ($row = $conn->nextRow($result))) {
      $one_entry = new PhoneEntry();
      $one_entry->setID($row[0]);
      $one_entry->setNumber($row[1]);
      $one_entry->setDescription($row[2]);
      $arr_obj[] = $one_entry;
    }

    return $arr_obj;
  }//search



  /*
   * search partial phonebook entries
   * @param &$conn database connection
   * @param &$valueObject search for one or more matching
   * @return
   */
  function searchpartial(&$conn, &$valueObject) {
    $full = true;
    $sql = "SELECT
              phonebook_id,
              number,
              description
                FROM phonebook WHERE 1=1 ";

    $number = $valueObject->getNumber();
    $description = $valueObject->getDescription();

    if ($number != "") {
        if ($full) { $full = false; }
        $sql = $sql."AND number LIKE '%$number%' ";
    }

    if ($description != "") {
        if ($full) { $full = false; }
        $sql = $sql."AND description LIKE '%$description%' ";
    }

    $sql = $sql."ORDER BY number ASC ";

    // Prevent accidential full table results.
    // Use loadAll if all rows must be returned.
    if ($full) {
      return array();
    }

    $result = $conn->makequery($sql);
    $arr_obj = array();
    while (($result) and ($row = $conn->nextRow($result))) {
      $one_entry = new PhoneEntry();
      $one_entry->setID($row[0]);
      $one_entry->setNumber($row[1]);
      $one_entry->setDescription($row[2]);
      $arr_obj[] = $one_entry;
    }

    return $arr_obj;
  }//searchpartial


  /*
   * set paging and sorting
   * @param &$conn database connection
   * @param &$valueObject search for one or more matching
   * @param $items elements per page
   * @param $orderby column name for sort (optional)
   * @param $orderbydir sort direction (optional)
   * @return total number of pages
   */
  function pager(&$conn, &$valueObject, $items, $orderby='', $orderbydir='') {
    $this->pager_items = $items;

    $full = true;
    $sql = " FROM phonebook WHERE 1=1 ";

    $number = $valueObject->getNumber();
    $description = $valueObject->getDescription();

    if ($number != "") {
        if ($full) { $full = false; }
        $sql = $sql."AND number LIKE '%$number%' ";
    }

    if ($description != "") {
        if ($full) { $full = false; }
        $sql = $sql."AND description LIKE '%$description%' ";
    }

    $count_sql = "SELECT count(phonebook_id) ".$sql;
    $result = $conn->makequery($count_sql);
    if ($row = $conn->nextRow($result)) {
      $allRows = $row[0];
    }
    $this->pager_totalpages = ceil($allRows / $items);
    $this->pager_lastentry = $valueObject;

    $sql = "SELECT
              phonebook_id,
              number,
              description ".$sql;

    $orderbydir = strtoupper($orderbydir);
    if (($orderbydir!='ASC') and ($orderbydir!='DESC')) $orderbydir = '';
    if ($orderby!='') {
      if ($this->pager_lastorderby!=$orderby) {
        if ($orderbydir=='') {
          //toggle order direction
          if ($this->pager_lastorderbydir=='DESC') {
            $orderbydir = 'ASC';
          } else {
            $orderbydir = 'DESC';
          }
        }//if $orderbydir==''
      } else {
        $orderbydir='DESC';//default order direction
      }//if $this->pager_lastorderby==$orderby
      $this->pager_lastorderby = $orderby;
      $this->pager_lastorderbydir = $orderbydir;

      $sql = $sql." ORDER BY $orderby $orderbydir ";
    }//if $orderby!=''
    $this->pager_sql = $sql;

    return $this->pager_totalpages;
  }//pager

  /*
   * search partial phonebook entries with paging and sorting
   * @param &$conn database connection
   * @param $page number pf page to retrieve
   * @return array of objects
   */
  function searchpager(&$conn, $page) {

    $sql = $this->pager_sql;
    $offset = ($page-1)*$this->pager_items;
    $limit = $this->pager_items;
    $sql .= " LIMIT $offset,$limit";
//print_r($sql);

    $result = $conn->makequery($sql);
    $arr_obj = array();
    while (($result) and ($row = $conn->nextRow($result))) {
      $one_entry = new PhoneEntry();
      $one_entry->setID($row[0]);
      $one_entry->setNumber($row[1]);
      $one_entry->setDescription($row[2]);
      $arr_obj[] = $one_entry;
    }

    return $arr_obj;
  }//searchpager



}//PhoneEntryDAO



class pagerDAO extends PhoneEntryDAO {

  protected $current;
  protected $total;

  function setpage($current,$total) {
    $this->current = $current;
    $this->total = $total;
  }//setpage

  function firstpage() {
    return 1;
  }//firstpage

  function prevpage() {
    if ($this->current - 1 > 1) {
      $this->current = $this->current -1;
    } else {
      $this->current = 1;
    }
    return $this->current;
  }//prevpage

  function nextpage() {
    if ($this->current + 1 < $this->total) {
      $this->current = $this->current +1;
    } else {
      $this->current = $this->total;
    }
    return $this->current;
  }//nextpage

  function lastpage() {
    return $this->total;
  }//lastpage

}//pagerDAO


?>
