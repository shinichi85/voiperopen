<?php
// Copyright (C) 2005-2008 SpheraIT
require_once('./classes/phonebook/Datasource.php');
require_once('./classes/phonebook/PhoneEntry.php');
require_once('./classes/phonebook/PhoneEntryDAO.php');

require_once('./classes/speeddial/Datasource.php');
require_once('./classes/speeddial/SpeedEntry.php');
require_once('./classes/speeddial/SpeedEntryDAO.php');

$ds = new Datasource($amp_conf['PHONEBOOKDBHOST'],
                     $amp_conf['PHONEBOOKDBNAME'],
                     $amp_conf['PHONEBOOKDBUSER'],
                     $amp_conf['PHONEBOOKDBPASS']);
$dao = new PhoneEntryDAO();

$dsspeed = new DatasourceSpeeddial($amp_conf['AMPDBHOST'],
                     $amp_conf['AMPDBNAME'],
                     $amp_conf['AMPDBUSER'],
                     $amp_conf['AMPDBPASS']);
$daospeed = new SpeedEntryDAO();

//echo "<pre>"; print_r($amp_conf);
?>

<?php
$delimiter = isset($_REQUEST['delimiter'])?$_REQUEST['delimiter']:'';
$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_speeddial_from_mysql.pl';
$display= 3;
?>

</div>

<div class="rnav">
    <li><a onFocus="this.blur()" href="config.php?mode=tools&display=<?php echo $display?>&action=import_phonebook" onclick="return confirm('Warning: The phonebook will be overwritten! please do an export now.')"><?php echo _("Import Phonebook")?></a></li>
    <li><a onFocus="this.blur()" href="config.php?mode=tools&display=<?php echo $display?>&action=import_speeddial" onclick="return confirm('Warning: The speeddial will be overwritten! please do an export now.')"><?php echo _("Import Speeddial")?></a></li>
</div>

<div class="content">

<?php
if ($action == 'import_phonebook')
{
  ?>
  <h3><?php echo _("Import Phonebook")?></h3>
<?php
  if(!isset($_POST["submitted"])){
//work only in Opera:<input type='file' accept="text/comma-separated-values" name='file[]'>
  ?>
    <form name='upload' action='<?php echo $PHP_SELF ?>' method='post' enctype='multipart/form-data' onsubmit='return checkConf();'>
    <table border="0" cellspacing="0" cellpadding="3">
    <tr>
	<td align="left">
    Input Delimiter:
    <input type="text" name="delimiter" value=";" size="1" maxlength="1" class="inputText">
    </td></tr><tr>
	<td align="left">
    Import file:
    <input type='file' name='file[]'>
    <input type='submit' value='Send'>
    <input type='hidden' name='submitted' value='TRUE'>
    </td>
    </tr>
    </table>
  </form>
<?php
  } else {
    if ($_FILES["file"]["size"][0]>0) {
      $filename = $_FILES["file"]["tmp_name"][0];
      $f = fopen($filename,"r");
    //filter empty rows and check for "header"
    do {
      $data = fgetcsv($f, 1000, $delimiter);
      if ( (trim(strtolower($data[0]))=='description') and
           (trim(strtolower($data[1]))=='number') ) {
        break;
      }
    } while ($data !== FALSE);
    if ($data !== FALSE) {
      //DELETE phonebook (empty phonebookEntryDel means no filters, so all!)
      $phonebookEntryDel = new PhoneEntry();
      $dao->delete($ds,$phonebookEntryDel);
      unset($phonebookEntryDel);
      while (($data = fgetcsv($f, 1000, $delimiter)) !== FALSE) {
        $phonebookEntry = new PhoneEntry();
        $description = trim($data[0]);//first field
        $number = trim($data[1]);//second field
        //insert only lines with both fields
        if ( ($description!="") and
             ($number!="") ) {
          $phonebookEntry->setNumber($number);//search only by number
          //search if number exist, if exist overwrite description only
          $phonebookEntrySearchedArr = $dao->search($ds,$phonebookEntry);
          if (count($phonebookEntrySearchedArr)>0) {
            $phonebookEntry->setID($phonebookEntrySearchedArr[0]->getID());//overwrite description
          }
          $phonebookEntry->setDescription($description);
          $dao->save($ds,$phonebookEntry);
          unset($phonebookEntrySearchedArr);
        }//if not empty
        unset($phonebookEntry);
      }//while
      $filename = $_FILES["file"]["name"][0];
      echo "The file <b>$filename</b> is successfully sent.<br>";
    } else {
      $filename = $_FILES["file"]["name"][0];
      echo "<font color='#FF0000'><b>$filename</b>, Wrong file format.</font><br>";
    }
    } else {
      echo("<font color='#FF0000'>The file is Empty.</font><br>");
    }//if size>0
    fclose($f);
  }
  
} else if ($action == 'import_speeddial')
{

?>
  <h3><?php echo _("Import Speeddial")?></h3>
<?php
  if(!isset($_POST["submitted"])){
//work only in Opera:<input type='file' accept="text/comma-separated-values" name='file[]'>
  ?>
    <form name='upload' action='<?php echo $PHP_SELF ?>' method='post' enctype='multipart/form-data' onsubmit='return checkConf();'>
    <table border="0" cellspacing="0" cellpadding="3">
    <tr>
	<td align="left">
    Input Delimiter:
    <input type="text" name="delimiter" value=";" size="1" maxlength="1" class="inputText">
    </td></tr><tr>
	<td align="left">
    Import file:
    <input type='file' name='file[]'>
    <input type='submit' value='Send'>
    <input type='hidden' name='submitted' value='TRUE'>
    </td>
    </tr>
    </table>
  </form>
<?php
  } else {
    if ($_FILES["file"]["size"][0]>0) {
      $filename = $_FILES["file"]["tmp_name"][0];
      $f = fopen($filename,"r");
    //filter empty rows and check for "header"
    do {
      $data = fgetcsv($f, 1000, $delimiter);
      if ( (trim(strtolower($data[0]))=='number') and
           (trim(strtolower($data[1]))=='callerid') and
           (trim(strtolower($data[2]))=='forward') and
           (trim(strtolower($data[3]))=='permission') ) {
        break;
      }
    } while ($data !== FALSE);
    if ($data !== FALSE) {
      //DELETE phonebook (empty phonebookEntryDel means no filters, so all!)
      $speedEntryDel = new SpeedEntry();
      $daospeed->delete($dsspeed,$speedEntryDel);
      unset($speedEntryDel);
      while (($data = fgetcsv($f, 1000, $delimiter)) !== FALSE) {
        $speeddialEntry = new SpeedEntry();
        $number = trim($data[0]);//1 field
        $description = trim($data[1]);//2 field
        $telnr = trim($data[2]);//3 field
        $permission = trim($data[3]);//4 field
        if (trim(strtolower($permission))=='no') {
            $permission = "";
        }
        if (trim(strtolower($permission))=='yes') {
            $permission = "CHECKED";
        }
        //insert only lines with both fields
        if ( ($number!="") and
             ($description!="") and ($telnr!="") ) {
          $speeddialEntry->setNumber($number);//search only by number
          //search if number exist, if exist overwrite description only
//          $speedEntrySearchedArr = $daospeed->search($dsspeed,$speeddialEntry);
//          if (count($speedEntrySearchedArr)>0) {
//            $speeddialEntry->setID($speedEntrySearchedArr[0]->getID());//overwrite description
//          }
          $speeddialEntry->setDescription($description);
          $speeddialEntry->setTelnr($telnr);
          $speeddialEntry->setPermission($permission);
          $daospeed->save($dsspeed,$speeddialEntry);
//          unset($speedEntrySearchedArr);
        }//if not empty
        unset($speeddialEntry);
      }//while
      $filename = $_FILES["file"]["name"][0];
      echo "The file <b>$filename</b> is successfully sent.<br>";
			// esegue script per il parsing mysql -> conf solo se la procedura di upload e' andata a buon fine.
			exec($wScript);
			// richiama funzione per il reload.
			needreload();
    } else {
      $filename = $_FILES["file"]["name"][0];
      echo "<font color='#FF0000'><b>$filename</b>, Wrong file format.</font><br>";
    }
    } else {
      echo("<font color='#FF0000'>The file is Empty.</font><br>");
    }//if size>0
    fclose($f);
  }  
  
} else {
  ?>
  <h3><?php echo _("Csv Import")?></h3>
<?php
}

?>

<script language="javascript">

var theForm = document.upload;

function checkConf()
{
    var errDelimiter = "<?php echo _('The Delimiter cannot be empty.'); ?>";

    defaultEmptyOK = false;
    if (theForm.delimiter.value.length == 0) {
        warnInvalid(theForm.delimiter, errDelimiter);
        return false;
    }
    return true;
}

</script>
