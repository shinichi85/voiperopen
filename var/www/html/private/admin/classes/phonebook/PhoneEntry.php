<?php
/**
  \file PhoneEntry.php
  \brief phonebook entry
  \date 14/apr/2008 24/apr/2008
  \author A.Santin
*/

class PhoneEntry {

  /**
   * mapped to the columns of database table

CREATE TABLE `ivrcruscotto`.`phonebook` (
`phonebook_id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
`number` VARCHAR( 15 ) NOT NULL ,
`description` VARCHAR( 30 ) NOT NULL ,
PRIMARY KEY ( `phonebook_id` )
)
   */
  protected $phonebook_id=NULL;
  protected $number;
  protected $description;

  function __construct() {
  }//__construct

  /*
   * get/set phonebook_id
   */
  function getID() {
    return $this->phonebook_id;
  }
  function setID($phonebook_id) {
    $this->phonebook_id = $phonebook_id;
  }

  /*
   * get/set number
   */
  function getNumber() {
    return $this->number;
  }
  function setNumber($number) {
    $this->number = $number;
  }

  /*
   * get/set description
   */
  function getDescription() {
    return $this->description;
  }
  function setDescription($description) {
    $this->description = $description;
  }


  /*
   * set all PhoneEntry parameters
   * @param phonebook_id (could be empty)
   * @param number
   * @param description
   */
  function setAll($phonebook_id,$number,$description) {
    $this->phonebook_id = $phonebook_id;
    $this->number = $number;
    $this->description = $description;
  }//setAll

  /*
   * clone a PhoneEntry
   * @return PhoneEntry object
   */
  function cloneEntry() {
    $cloned = new PhoneEntry();

    $cloned->setID($this->phonebook_id);
    $cloned->setNumber($this->number);
    $cloned->setDescription($this->description);

    return $cloned;
  }//cloneEntry

  /*
   * copy PhoneEntry to an Array
   * @return array
   */
  function toArray() {
    $arr = array(
           0=>$this->phonebook_id,
           1=>$this->number,
           2=>$this->description,
           'phonebook_id'=>$this->phonebook_id,
           'number'=>$this->number,
           'description'=>$this->description
              );

    return $arr;
  }//toArray

  /*
   * copy PhoneEntry to a string
   * @return string
   */
  function toString() {
    $st = $this->number.' '.$this->description;

    return $st;
  }//toString

  /*
   * compare with second PhoneEntry (IGNORE phonebook_id!)
   * @param PhoneEntry object
   * @return bool
   */
  function compare(&$valueObject) {
    if (($this->number==$valueObject->number) and
        ($this->description==$valueObject->description))
      return true;
    else
      return false;
  }//compare


}//PhoneEntry


?>
