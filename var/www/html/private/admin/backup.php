<?php
// backup.php Copyright (C) 2005 VerCom Systems, Inc. & Ron Hartmann (rhartmann@vercomsystems.com)
// Asterisk Management Portal Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
// New backup features by SpheraIT (2005)
//
//This program is free software; you can redistribute it and/or
//modify it under the terms of the GNU General Public License
//as published by the Free Software Foundation; either version 2
//of the License, or (at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
?>

<?php
include_once "schedule_functions.php";
$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$serialpn = file_parser_serialpn("/etc/voiper_pn");
$display= 1;

function uploader($num_of_uploads=1, $file_types_array=array("gz"), $max_file_size=100000000, $upload_dir="/var/lib/asterisk/backups/Voiper-BAK/"){
  if(!is_numeric($max_file_size)){
   $max_file_size = 100000000;
  }
  if(!isset($_POST["submitted"])){
   $form = "<form name='upload' action='".$PHP_SELF."' method='post' enctype='multipart/form-data'>Backup: <input type='hidden' name='submitted' value='TRUE' id='".time()."'><input type='hidden' name='MAX_FILE_SIZE' value='".$max_file_size."'>";
   for($x=0;$x<$num_of_uploads;$x++){
     $form .= "<input type='file' name='file[]'>";
   }
   $form .= "<input type='submit' value='Send'><br>";
   $form .= "</form>";
   echo($form);
  }else{
   foreach($_FILES["file"]["error"] as $key => $value){
     if($_FILES["file"]["name"][$key]!=""){
       if($value==UPLOAD_ERR_OK){
         $origfilename = $_FILES["file"]["name"][$key];
         $filename = explode(".", $_FILES["file"]["name"][$key]);
         $filenameext = $filename[count($filename)-1];
         unset($filename[count($filename)-1]);
         $filename = implode(".", $filename);
         $filename = substr($filename, 0, 40).".".$filenameext;
         $file_ext_allow = FALSE;
         for($x=0;$x<count($file_types_array);$x++){
           if($filenameext==$file_types_array[$x]){
             $file_ext_allow = TRUE;
           }
         }
         if($file_ext_allow){
           if($_FILES["file"]["size"][$key]<$max_file_size){
             if(move_uploaded_file($_FILES["file"]["tmp_name"][$key], $upload_dir.$filename)){

    		print "\n";
    		print "\n";
    		print "The file <b>$filename</b> is uploaded correctly.\n";
    		print "\n";

             }else{

               echo("<font color='#FF0000'>There is a errors in your file upload.</font><br>");
             }

           }else{

             echo("<font color='#FF0000'>The filesize is not right.</font><br>");
           }

         }else{

           echo("<font color='#FF0000'>The file extension is not right.</font><br>");
         }

       }else{

         echo("<font color='#FF0000'>There is a errors in your file upload.</font><br>");
       }
     }else{

       echo("<font color='#FF0000'>No filename selected.</font><br>");
     }
   }
  }
}

switch ($action) {
	case "addednew":
		$ALL_days=isset($_POST['all_days'])?$_POST['all_days']:'';
		$ALL_months=isset($_POST['all_months'])?$_POST['all_months']:'';
		$ALL_weekdays=isset($_POST['all_weekdays'])?$_POST['all_weekdays']:'';

		$backup_schedule=isset($_REQUEST['backup_schedule'])?$_REQUEST['backup_schedule']:'';
		$name=(empty($_REQUEST['name'])?'backup':$_REQUEST['name']);
		$mins=isset($_REQUEST['mins'])?$_REQUEST['mins']:'';
		$hours=isset($_REQUEST['hours'])?$_REQUEST['hours']:'';
		$days=isset($_REQUEST['days'])?$_REQUEST['days']:'';
		$months=isset($_REQUEST['months'])?$_REQUEST['months']:'';
		$weekdays=isset($_REQUEST['weekdays'])?$_REQUEST['weekdays']:'';

		$backup_options[]=$_REQUEST['bk_voicemail'];
		$backup_options[]=$_REQUEST['bk_sysrecordings'];
		$backup_options[]=$_REQUEST['bk_sysconfig'];
		$backup_options[]=$_REQUEST['bk_cdr'];
		$backup_options[]=$_REQUEST['bk_fop'];

		$Backup_Parms=Get_Backup_String($name,$backup_schedule, $ALL_days, $ALL_months, $ALL_weekdays, $mins, $hours, $days, $months, $weekdays);
		Save_Backup_Schedule($Backup_Parms, $backup_options);
	break;
	case "edited":
		$ID=$_REQUEST['backupid'];
		Delete_Backup_set($ID);
		$ALL_days=$_REQUEST['all_days'];
		$ALL_months=$_REQUEST['all_months'];
		$ALL_weekdays=$_REQUEST['all_weekdays'];

		$backup_schedule=$_REQUEST['backup_schedule'];
		$name=(empty($_REQUEST['name'])?'Voiper-BAK':$_REQUEST['name']);
		$mins=$_REQUEST['mins'];
		$hours=$_REQUEST['hours'];
		$days=$_REQUEST['days'];
		$months=$_REQUEST['months'];
		$weekdays=$_REQUEST['weekdays'];

		$backup_options[]=$_REQUEST['bk_voicemail'];
		$backup_options[]=$_REQUEST['bk_sysrecordings'];
		$backup_options[]=$_REQUEST['bk_sysconfig'];
		$backup_options[]=$_REQUEST['bk_cdr'];
		$backup_options[]=$_REQUEST['bk_fop'];

		$Backup_Parms=Get_Backup_String($name,$backup_schedule, $ALL_days, $ALL_months, $ALL_weekdays, $mins, $hours, $days, $months, $weekdays);
		Save_Backup_Schedule($Backup_Parms, $backup_options);
	break;
	case "delete":
		$ID=$_REQUEST['backupid'];
		Delete_Backup_set($ID);
	break;
	case "deletedataset":
		$dir=$_REQUEST['dir'];
		exec("/bin/rm -rf '$dir'");
	break;
	case "deletefileset":
		$dir=$_REQUEST['dir'];
		exec("/bin/rm -rf '$dir'");
	break;
	case "restored":
		$dir=$_REQUEST['dir'];
		$file=$_REQUEST['file'];
		$filetype=$_REQUEST['filetype'];
		$Message=Restore_Tar_Files($dir, $file, $filetype, $display);
		needreload();
	break;
	case "backupnow":
		exec("/var/lib/asterisk/bin/ampbackup.pl Voiper-BAK yes yes yes yes yes");
	break;
}


?>
</div>
<div class="rnav">
    <li><a onFocus="this.blur()" href="config.php?mode=tools&display=<?php echo $display?>&action=restore"><?php echo _("Restore from HD")?></a></li>

    <?php if ($serialpn == "0101") {?>
    
        <li><a onFocus="this.blur()" href="config.php?mode=tools&display=<?php echo $display?>&action=copydom"><?php echo _("Copy backup from DOM")?></a></li>

    <?php }?>
    
    <li><a onFocus="this.blur()" href="/private/admin/download_backup/voiper.tar.gz"><?php echo _("Download Backup")?></a></li>
    <li><a onFocus="this.blur()" href="config.php?mode=tools&display=<?php echo $display?>&action=upload"><?php echo _("Upload Backup")?></a></li>
    <li><a onFocus="this.blur()" href="config.php?mode=tools&display=<?php echo $display?>&action=backupnow" onclick="alert('This procedure take from 1 to 10 mins and override the System Backup settings. Press OK when you are ready!');"><?php echo _("Full Backup Now")?></a></li>
    <li><a onFocus="this.blur()" href="config.php?mode=tools&display=<?php echo $display?>&action=ftpconfig"><?php echo _("Backup Ftp Config")?></a></li>
<?php
//get unique account rows for navigation menu
$results = Get_Backup_Sets();

if (isset($results)) {
	foreach ($results as $result) {
		echo "<li><a id=\"".($extdisplay==$result[13] ? 'current':'')."\" onFocus=\"this.blur()\" href=\"config.php?mode=tools&display=".$display."&action=edit&backupid={$result[13]}&backupname=Voiper-BAK\">System Backup</a></li>";
	}
}
?>
</div>


<div class="content">

<?php
if ($action == 'add')
{
	?>
	<h3><?php echo _("System Backup:")?></h3>
	<form name="addbackup" action="<?php $_SERVER['PHP_SELF'].'&mode=tools' ?>" method="post">
	<input type="hidden" name="display" value="<?php echo $display?>">
	<input type="hidden" name="action" value="addednew">
        <table>
	<?php Show_Backup_Options(); ?>
        </table>
    <h5><?php echo _("Run Schedule")?></h5>
        <table>
	<?php show_schedule("yes",""); ?>
	<tr>
        <td colspan="5" align="center"><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>" ></td>
        </tr>
        </table>
	</form>
<?php
}
else if ($action == 'edit')
{
	?>
	<h3><?php echo _("System Backup:")?></h3>
	<form name="addbackup" action="<?php $_SERVER['PHP_SELF'].'&mode=tools' ?>" method="post">
	<input type="hidden" name="display" value="<?php echo $display?>">
	<input type="hidden" name="action" value="edited">
	<input type="hidden" name="backupid" value="<?php echo $_REQUEST['backupid']; ?>">
        <table>
	<?php Show_Backup_Options($_REQUEST['backupid']); ?>
        </table>
    <h5><?php echo _("Run Schedule")?></h5>
        <table>
	<?php show_schedule("yes", "$_REQUEST[backupid]"); ?>
	<tr>
        <td colspan="5" align="center"><input name="Submit" type="submit" value="Submit Changes" ></td>
        </tr>
        </table>
	</form>
<?php
}

else if ($action == 'upload')
{
	?>
	<h3><?php echo _("Upload Backup:")?></h3>

	<table><tr><td>

	<? echo uploader(); ?>

	<br>
	Max Upload size is 100mb.
	<br><br><br><br><br><br><br><br><br><br><br>

	</td></tr></table>

<?php
}

else if ($action == 'ftpconfig')
{

$ftpconfig = $_REQUEST['ftpconfig'];

if ($ftpconfig == 'save') {
	
		$ftpbackup = $_POST["ftpbackup"];
		$ftpuser = $_POST["ftpuser"];
		$ftppassword = $_POST["ftppassword"];
		$ftpsubdir = $_POST["ftpsubdir"];
		$ftpserver = $_POST["ftpserver"];
		$ftpemail = $_POST["ftpemail"];
		
		$sql = "UPDATE backupftp SET ftpbackup='$ftpbackup',ftpuser='$ftpuser',ftppassword='$ftppassword',ftpsubdir='$ftpsubdir',ftpserver='$ftpserver',ftpemail='$ftpemail'";

		$res =& $db->query($sql);
		if (DB::isError($res)) {
		    die($res->getMessage());
		}

	    unset($ftpemail);
	    unset($ftpserver);
	    unset($ftpsubdir);
	    unset($ftppassword);
	    unset($ftpuser);
        unset($ftpbackup);
}

$sql = "SELECT * FROM backupftp";
$results = $db->getAll($sql);
if(DB::IsError($results)) {
	die($results->getMessage());
}

foreach ($results as $result) {

	    $ftpemail = $result[6];
	    $ftpserver = $result[5];
	    $ftpsubdir = $result[4];
	    $ftppassword = $result[3];
	    $ftpuser = $result[2];
	    $ftpbackup = $result[1];
	
}

?>
<h3><?php echo _("Backup Ftp Config:")?></h3>

<form name="ftpconfig" action="config.php?mode=tools&display=<?php echo $display?>" method="post" onSubmit="return backupftpcheck(this);">
<input type="hidden" name="action" value="ftpconfig"/>
<input type="hidden" name="ftpconfig" value="save"/>

<table border="0" cellspacing="8" cellpadding="1">
	<tr>
    <td><b><a href=# class="info"><?php echo _("Ftp Backup")?><span><?php echo _("If set to Yes Ftp Backup is Active.")?><br></span></a>:</b></td>
    <td>&nbsp;&nbsp;<select name="ftpbackup">
    <option value="no"><?php echo _("No"); ?></option>
    <option <?php if ($ftpbackup == "yes") echo "SELECTED "?>value="yes"><?php echo _("Yes"); ?></option>
    </select></td>
	</tr>
	<tr>
    <td><b><a href=# class="info"><?php echo _("Ftp Username")?><span><?php echo _("Ftp Username.")?><br></span></a>:</b></td>
    <td><input name="ftpuser" type="text" id="ftpuser" value="<?php  echo $ftpuser?>" size="20" maxlength="20"></td>
	</tr>
	<tr>
    <td><b><a href=# class="info"><?php echo _("Ftp Password")?><span><?php echo _("Ftp Password.")?><br></span></a>:</b></td>
    <td><input name="ftppassword" type="password" id="ftppassword" value="<?php  echo $ftppassword?>" size="20" maxlength="50"></td>
	</tr>
	<tr>
    <td><b><a href=# class="info"><?php echo _("Ftp Subdir")?><span><?php echo _("Ftp Subdirectory, without trailing slash. example: backup")?><br></span></a>:</b></td>
    <td><input name="ftpsubdir" type="text" id="ftpsubdir" value="<?php  echo $ftpsubdir?>" size="30" maxlength="50"></td>
	</tr>
	<tr>
    <td><b><a href=# class="info"><?php echo _("Ftp Server")?><span><?php echo _("IP Adress of the Ftp Server")?><br></span></a>:</b></td>
    <td><input name="ftpserver" type="text" id="ftpserver" value="<?php  echo $ftpserver?>" size="30" maxlength="50"></td>
	</tr>
	<tr>
    <td><b><a href=# class="info"><?php echo _("Email Alert")?><span><?php echo _("If you want to receive a email alert when the transfer is done, please type a valid e-mail address or type root. If you want to disable this features, leave it empty.")?><br></span></a>:</b></td>
    <td><input name="ftpemail" type="text" id="ftpemail" value="<?php  echo $ftpemail?>" size="30" maxlength="50"></td>
	</tr>
	<tr>
	<td colspan="2"><div align="right"><h6><input name="Submit" type="submit" value="Submit Changes"></h6></div></td>
	</tr>
  </table>
</form>

<?php
}

else if ($action == 'restore')
{
?>
	<h3><?php echo _("System Restore from HD:")?></h3>
<?php


	if (!isset($_REQUEST['dir'])) {
		$dir = "/var/lib/asterisk/backups/Voiper-BAK";
		if(!is_dir($dir)) mkdir($dir);
	} else {
		$dir = "$_REQUEST[dir]";
	}
	$file = "$_REQUEST[file]";

    echo "<table cellSpacing=8 cellPadding=1><tr><td>";

	Get_Tar_Files($dir, $display, $file);

	echo "<br><br><br><br><br><br>";

	echo "</td></tr></table>";
}
else if ($action == 'copydom')
{
?>
	<h3><?php echo _("Copy backup from DOME & Restore:")?></h3>
<?php


	if (!isset($_REQUEST['dir'])) {
		$dir = "/var/lib/asterisk/backups/Voiper-BAK";
		if(!is_dir($dir)) mkdir($dir);
	} else {
		$dir = "$_REQUEST[dir]";
	}
	$file = "$_REQUEST[file]";

	exec("/bin/rm -f /var/lib/asterisk/backups/Voiper-BAK/voiper.tar.gz");
	exec("/bin/cp -br --remove-destination --reply=yes /mnt/flashcard/backups/Voiper-BAK/voiper.tar.gz /var/lib/asterisk/backups/Voiper-BAK");

    echo "<table cellSpacing=8 cellPadding=1><tr><td>";

	Get_Tar_Files($dir, $display, $file);

	echo "<br><br><br><br><br><br><br><br><br><br><br><br>";

	echo "</td></tr></table>";
}
else if ($action == 'backupnow')
{
?>
	<h3><?php echo _("Full Backup Now:")?></h3>
<?php

    echo "<table cellSpacing=8 cellPadding=1><tr><td>";

	echo "Full Backup is Done";
    echo "<br><br>";
	echo "Some Services like: <b>ntp</b>,<b>snmp</b>,<b>ddclient</b>,<b>logwatch</b> and <b>mail</b> if are enabled ";
	echo "require a reboot to complete the restore procedure.";
    echo "<br><br><br><br><br><br><br><br><br><br>";

	echo "</td></tr></table>";
}

else

{
	if (isset($Message)){
	?>
		<h3><?php echo $Message ?></h3>
	<?php }
	else{
	?>
		<h3><?php echo _("Backup & Restore:") ?></h3>
<?php

    echo "<table cellSpacing=8 cellPadding=1><tr><td>";

	echo "After each configuration action remember to:<br><br>";
	echo "1. Perform a <b>Full Backup Now</b>.<br>";
	echo "2. Save the voiper.tar.gz file with <b>Download Backup</b> menu.<br>";
	echo "3. Secure the file in a storage and note the Voiper version.<br>";
	echo "<br><br><br><br><br><br><br>";

	echo "</td></tr></table>";
}
?>
<?php
}
?>

<SCRIPT language="javascript" type="text/javascript">
<!--

var errfound = false;

function error(elem, text) {
	if (errfound) return;
	window.alert(text);
	elem.select();
	elem.focus();
	errfound = true;
}

function backupftpcheck(form) {

	errfound = false;

	if (form.ftpuser.value == "") {
	   error(form.ftpuser,"Please type the Login Name of the Ftp Server.");
       form.ftpuser.focus();
    }

	if (form.ftppassword.value == "") {
	   error(form.ftppassword,"Please type the Login Password of the Ftp Server.");
       form.ftppassword.focus();
    }

	if (form.ftpserver.value == "") {
	   error(form.ftpserver,"Please type the Ftp Server address.");
       form.ftpserver.focus();
    }

  	return ! errfound;
}
// -->
</SCRIPT>
