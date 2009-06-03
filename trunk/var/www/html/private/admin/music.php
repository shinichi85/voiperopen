<?php /* $Id: music.php,v 1.4 2005/04/07 09:44:50 julianjm Exp $ */
//Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
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

<script language="JavaScript">

function checkName(f2) {

	defaultEmptyOK = false;

	if (!isAlphanumeric(f2.category.value))
	return warnInvalid(f2.category, "Please enter a valid Category Name");
	
	return true;
}

function checkUpload(f2) {
    if (f2.mohfile.value == "") {
        alert("No valid file(s) selected! Please press the Browse button and pick a file.");
		return false;
} else {
		alert("Please wait until the page loads. Your file is being processed.");
		return true;
}
}
function deleteCheck(f2) {

	cancel = false;
	ok = true;

	if (confirm("Are you sure to delete this Music Category?"))
  		return ! cancel;
	else
  		return ! ok;
}

function deleteCheck2(f2) {

	cancel = false;
	ok = true;

	if (confirm("Are you sure to delete this MP3 Files?"))
  		return ! cancel;
	else
  		return ! ok;
}

</script>


<?php
$display= 1;

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$category = strtr(isset($_REQUEST['category'])?$_REQUEST['category']:''," ", "-");
if ($category == null) $category = 'Default';

if ($category == "Default")
	$path_to_dir = "/var/lib/asterisk/mohmp3"; //path to directory u want to read.
else
	$path_to_dir = "/var/lib/asterisk/mohmp3/$category"; //path to directory u want to read.

switch ($action) {
	case "addednew":
		makemusiccategory($path_to_dir,$category); 
		createmusicconf();
		needreload();
	break;
	case "addedfile":
		createmusicconf();
		needreload();
	break;
	case "delete":
		rmdirr("$path_to_dir"); 
		$path_to_dir = "/var/lib/asterisk/mohmp3"; //path to directory u want to read.
		$category='Default';
		createmusicconf();
		needreload();
	break;
}


?>
</div>
<div class="rnav">
    <li><a href="config.php?mode=pbx&display=<?php echo $display?>&action=add" onFocus="this.blur()"><?php echo _("Add Music Category")?></a></li>
    <li><a id="<?php echo ($category=='Default' ? 'current':'')?>" href="config.php?mode=pbx&display=<?php echo $display?>&category=Default" onFocus="this.blur()"><?php echo _("Default")?></a></li>

<?php
//get existing trunk info
$tresults = getmusiccategory("/var/lib/asterisk/mohmp3");
if (isset($tresults)) {
	foreach ($tresults as $tresult) {
		echo "<li><a id=\"".($category==$tresult ? 'current':'')."\" href=\"config.php?mode=pbx&display={$display}&category={$tresult}&action=edit\" onFocus=\"this.blur()\">{$tresult}</a></li>";
	}
}
?>
</div>


<?php
function createmusicconf()
{
  	global $db;

		$mp3command = $db->getRow("SELECT value FROM globals where variable='MOH_COMMAND'");
        if (!isset($mp3command)) {
		$mohcmd = "quietmp3:/var/lib/asterisk/mohmp3/";
		$mohend = "";
	} else {
  		if ($mp3command[0] == "mpg123" ) {
			$mohcmd = "quietmp3:/var/lib/asterisk/mohmp3/";
			$mohend = "";
		} else {
			$mohatt = $db->getRow("SELECT value FROM globals where variable='MOH_VOLUME'");
//			$mohcmd = "custom:/var/lib/asterisk/mohmp3/";
			$mohcmd = "/var/lib/asterisk/mohmp3/";
//			$mohend = ",$mp3command[0] --mono -R 8000 -a$mohatt[0] --output=raw:-";
			$mohend = "$mp3command[0] --mono -R 8000 -a$mohatt[0] --output=raw:-";
		}
	}

	$File_Write="";
	$tresults = getmusiccategory("/var/lib/asterisk/mohmp3/");
	if (isset($tresults)) {
		foreach ($tresults as $tresult) 
//            $File_Write.="[{$tresult}]\nmode=custom\ndirectory=$mohcmd{$tresult}\napplication=$mohend\n\n";
            $File_Write.="[{$tresult}]\nmode=files\ndirectory=$mohcmd{$tresult}\nrandom=yes\n\n";

//			$File_Write.="{$tresult} => $mohcmd{$tresult}$mohend\n";
	}

$handle = fopen("/etc/asterisk/musiconhold_additional.conf", "w");

if (fwrite($handle, $File_Write) === FALSE)
{
        echo _("Cannot write to file")." ($tmpfname)";
        exit;
}

fclose($handle);


}
function makemusiccategory($category)
{
	mkdir("$path_to_dir/$category", 0755); 
}
 
function build_list() 
{
	global $path_to_dir;
	$pattern = '';
	$handle=opendir($path_to_dir) ;
	$extensions = array('.mp3'); // list of extensions which only u want to read.
	
	//generate the pattern to look for.
	foreach ($extensions as $value)
		$pattern .= "$value|";
	
	$length = strlen($pattern);
	$length -= 1;
	$pattern = substr($pattern,0,$length);
	
	
	//store file names that match pattern in an array
	$i = 0;
	while (($file = readdir($handle))!==false) 
	{
		if ($file != "." && $file != "..") 
		{ 
		
			if(eregi($pattern,$file))
			{
				$file_array[$i] = $file; //pattern is matched store it in file_array.
				$i++;		
			}
		} 
	
	}
	closedir($handle); 
	
	return $file_array;  //return the size of the array
	
}

function draw_list($file_array, $path_to_dir, $category) 
{
	//list existing mp3s and provide delete buttons

   if ($category == "Default") {
   
      $categoryfix = "";
      
   } else {
   
   
         $categoryfix = $category;

   }

	$display=1;
	if ($file_array) {
		foreach ($file_array as $thisfile) {
			print "<div style=\"text-align:right;width:67%;border: 1px solid;padding:2px;\">";
			print "<a style=\"float:left;margin-left:5px;\" onFocus=\"this.blur()\" href=\"/private/admin/download_moh/$categoryfix/$thisfile\" target=\"_blank\"><b>".$thisfile."</a></b>";
			print "<a style=\"margin-right:5px;\" href=\"".$_SERVER['SCRIPT_NAME']."?mode=pbx&display=";
			print (isset($display)?$display:'')."&del=".$thisfile."&category=".$category."\" onFocus=\"this.blur()\" onClick=\"return deleteCheck2(this);\">"._("Delete")."</a>";
			print "</div><br>";
		}
	}
}

function process_mohfile($mohfile)
{
	global $path_to_dir;
	$origmohfile=$path_to_dir."/orig_".$mohfile;
	$newname = strtr($mohfile,"&", "_");
	$newmohfile=$path_to_dir."/". ((strpos($newname,'.mp3') === false) ? $newname.".mp3" : $newname);
//	$lamecmd="/usr/local/bin/lame --cbr -m m -t -F \"".$origmohfile."\" \"".$newmohfile."\"";
	$lamecmd="/usr/local/bin/lame --quiet --resample 16 --cbr -b 16 -m m -t -q 5 -F \"".$origmohfile."\" \"".$newmohfile."\"";
	exec($lamecmd);
	$rmcmd="rm -f \"". $origmohfile."\"";
	exec($rmcmd);
}

function kill_mpg123()
{
	$killcmd="killall -9 madplay";
	exec($killcmd);
}
?>

<div class="content">
<h3><?php echo _("On Hold Music:")?></h3>

<?php
if ($action == 'add')
{
	?>
	<form name="addcategory" action="<?php $_SERVER['PHP_SELF'].'&mode=pbx' ?>" method="post" onsubmit="return checkName(addcategory);">
	<input type="hidden" name="display" value="<?php echo $display?>">
	<input type="hidden" name="action" value="addednew">
	<table>
	<tr><td colspan="2"><h5><?php echo _("Add Music Category")?></h5></td></tr>
	<tr>
		<td><a href="#" class="info"><?php echo _("Category Name:")?><span><?php echo _("Allows you to Set up Different Categories for music on hold.  This is useful if you would like to specify different Hold Music or Commercials for various ACD Queues.")?> </span></a></td>
		<td><input type="text" name="category" value=""></td>
	</tr>
	<tr>
		<td colspan="2"><br><h6><input name="Submit" type="submit" value='<?php echo _("Submit Changes")?>' ></h6></td>		
	</tr>
	</table></form>

<?php
}
else
{
?>
	<table>
	<tr><td colspan="2"><h5><?php echo _("Category:")?> <?php echo $category=="Default"?_("Default"):$category;?></h5></td></tr>

	<?php  if ($category!="Default"){?>
	<p><a href="config.php?mode=pbx&display=<?php echo $display ?>&action=delete&category=<?php echo $category ?>" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete Music Category")?> <?php echo $category; ?></a></p><?php }?>
	<tr><td colspan="2">
	<form enctype="multipart/form-data" name="upload" action="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx' ?>" method="post" onsubmit="return checkUpload(upload);">
		<?php echo _("Upload a .wav or .mp3 file:")?><br><br>
		<input type="hidden" name="display" value="1">
		<input type="hidden" name="category" value="<?php echo "$category" ?>">
		<input type="hidden" name="action" value="addedfile">
		<input type="file" size="28" name="mohfile"><input type="submit" name="Submit" value="Upload">
	</form>
	</td></tr>
	</table>
	
	<?php

	if (isset($_FILES['mohfile']['tmp_name']) && is_uploaded_file($_FILES['mohfile']['tmp_name'])) {


		move_uploaded_file($_FILES['mohfile']['tmp_name'], $path_to_dir."/orig_".$_FILES['mohfile']['name']);
		process_mohfile($_FILES['mohfile']['name']);
		
		echo "<h5>"._("Completed processing")." ".$_FILES['mohfile']['name']."!</h5>";
		kill_mpg123();
	}

	//build the array of files
	$file_array = build_list();
	$numf = count($file_array);


	if (isset($_REQUEST['del'])) {
		if (($numf == 1) && ($category == "Default") ){
			echo "<h5>"._("You must have at least one file for On Hold Music.  Please upload one before deleting this one.")."</h5>";
		} else {
			$rmcmd="rm -f \"".$path_to_dir."/".$_REQUEST['del']."\"";
			exec($rmcmd);
			echo "<h5>"._("Deleted")." ".$_REQUEST['del']."!</h5>";
			kill_mpg123();
		}
	}
	$file_array = build_list();
	draw_list($file_array, $path_to_dir, $category);
	?>

	<?php
}
?>
