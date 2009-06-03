<?php /* $Id: recordings.php,v 1.5 2005/04/28 19:45:39 rcourtna Exp $ */
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
	if (!isFilename(f2.rname.value))
	return warnInvalid(f2.rname, "Please enter a valid Name for this System Recording");

}

function checkUpload(f2) {
    if (f2.ivrfile.value == "") {
        alert("No valid file(s) selected! Please press the Browse button and pick a file.");
		return false;
} else {
		alert("Please wait until the page loads. Your file is being processed.");
		return true;
}
}

function checkCidnum(f2) {

	defaultEmptyOK = false;
	if (!isInteger(f2.cidnum.value))
	return warnInvalid(f2.cidnum, "Please enter your user/extension number:");

	return true;
}

function deleteCheck(f2) {

	cancel = false;
	ok = true;

	if (confirm("Are you sure to delete this Recording?"))
  		return ! cancel;
	else
  		return ! ok;
}

</script>


<?php
isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action='';
isset($_REQUEST['promptnum'])?$promptnum = $_REQUEST['promptnum']:$promptnum='';
isset($_REQUEST['recordingdisplay'])?$prompt = $_REQUEST['recordingdisplay']:$prompt='';
isset($_REQUEST['rname'])?$rname = $_REQUEST['rname']:$rname='';
if ($promptnum == null) $promptnum = '1';
$display=12;


switch($action) {
	default:
?>
<h3><?php echo _("Your User/Extension:")?></h3>
<form name="prompt" action="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx' ?>" method="post" onsubmit="return checkCidnum(prompt);">
        <input type="hidden" name="action" value="recordings_start">
        <input type="hidden" name="display" value="<?php echo $display?>">
        <?php echo _("You can use your extension to record and playback a System Recording.")?><br><br>
        <?php echo _("Please enter your user/extension number:")?>
        <input type="text" size="6" name="cidnum"><br>
        <h6><input name="Submit" type="submit" value="Continue"></h6>
</form>

<?php
        break;
	case 'recorded':
		$rname=strtr($rname," ", "_");
		rename('/var/lib/asterisk/sounds/'.$_REQUEST['cidnum'].'ivrrecording.wav','/var/lib/asterisk/sounds/custom/'.$rname.'.wav');
		unlink('/var/lib/asterisk/sounds/'.$_REQUEST['cidnum'].'ivrrecording.wav');
		echo '<br><h3>'._("System Recording").' "'.$rname.'" '._("Saved").'!</h3>';

	break;
	case 'delete':
		unlink('/var/lib/asterisk/sounds/custom/'.$prompt.'.wav');
		echo '<br><h3>'._("System Recording").' "'.$prompt.'" '._("Deleted").'!</h3>';
	break;
        case 'recordings_start':
?>

</div>
<div class="rnav">

		<li><a id="<?php echo isset($extdisplay)?'current':''; ?>" href="config.php?mode=pbx&display=<?php echo $display?>&action=recordings_start&cidnum=<?php echo $_REQUEST['cidnum'] ?>"><?php echo _("Add Recording")?></a></li>
<?php

$tresults = getsystemrecordings("/var/lib/asterisk/sounds/custom");

if (isset($tresults)){
	foreach ($tresults as $tresult) {
		echo "<li><a id=\"".($recordingdisplay==$tresult ? 'current':'')."\" href=\"config.php?mode=pbx&display=".$display."&recordingdisplay={$tresult}&recording_action=edit&action=recordings_start&cidnum=$_REQUEST[cidnum]\" onFocus=\"this.blur()\">".substr($tresult,0,20)."</a></li>";
	}
}
?>
</div>

<div class="contentrecording">

<?php
if ($prompt) {
 echo "<h3>"._("Recording").": ".$prompt."</h3>";
} else {
 echo "<h3>"._("Add Recording")."</h3>";
}
?>
<?php

	if (isset($_REQUEST['recording_action']) && $_REQUEST['recording_action'] == 'edit'){

?>
	<p><a href="config.php?mode=pbx&display=<?php echo $display ?>&recordingdisplay=<?php echo $prompt ?>&action=delete" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete Recording")?> <?php echo $prompt; ?></a> / <a href="download_recording/<?php echo $prompt ?>.wav" onFocus="this.blur()" target="_blank"><?php echo _("Download")?></a></p>
<?php
		copy('/var/lib/asterisk/sounds/custom/'.$prompt.'.wav','/var/lib/asterisk/sounds/'.$_REQUEST['cidnum'].'ivrrecording.wav');

		echo '<h5>'._('Dial *99 to listen to your current recording.').'</h5>';
	}
?>
<h5><?php echo _("Step 1: Record")?></h5>
<p>
	<?php echo _("Using your phone,")?> <a href="#" class="info"><?php echo _("dial *77")?><span><?php echo _("Start speaking at the tone. Hangup when finished.")?></span></a> <?php echo _("and speak the message you wish to record.")?>
</p>
<p>
	<form enctype="multipart/form-data" name="upload" action="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx' ?>" method="post" onsubmit="return checkUpload(upload);"/>
		<?php echo _('Alternatively, upload a recording in')?> <a href="#" class="info"><?php echo _(".wav format")?><span><?php echo _("The .wav file _must_ have a sample rate of 8000Hz Mono")?></span></a>:<br><br>
		<input type="hidden" name="display" value="<?php echo $display?>">
		<input type="hidden" name="promptnum" value="<?php echo $promptnum?>">
		<input type="hidden" name="action" value="recordings_start">
        <input type="hidden" name="cidnum" value="<?php echo $_REQUEST['cidnum'];?>">
		<input type="file" size="30" name="ivrfile"/>
		<input type="submit" name="Submit" value="Upload">
	</form>
<?php
if (isset($_FILES['ivrfile']['tmp_name']) && is_uploaded_file($_FILES['ivrfile']['tmp_name'])) {
	move_uploaded_file($_FILES['ivrfile']['tmp_name'], "/var/lib/asterisk/sounds/".$_REQUEST['cidnum']."ivrrecording.wav");
	echo "<h6>"._("Successfully uploaded")." ".$_FILES['ivrfile']['name']."</h6>";
}
?>
</p>
<form name="prompt" action="<?php $_SERVER['PHP_SELF'].'?mode=pbx' ?>" method="post" onsubmit="return checkName(prompt);">
<input type="hidden" name="action" value="recorded">
<input type="hidden" name="cidnum" value="<?php echo $_REQUEST['cidnum'];?>">
<input type="hidden" name="promptnum" value="<?php echo $promptnum?>">
<input type="hidden" name="display" value="<?php echo $display?>">
<h5><?php echo _("Step 2: Verify")?></h5>
<p>
	<?php echo _("After recording or uploading, <em>dial *99</em> to listen to your recording.")?>
</p>
<p>
	<?php echo _("If you wish to re-record your message, dial *77 again.")?>
</p>
<h5><?php echo _("Step 3: Name")?> </h5>

<p>
<?php echo _("Name this Recording")?>: <input type="text" maxlength="20" size="30" name="rname" value="">
</p>

<h6><?php echo _('Click "SAVE" when you are satisfied with your recording')?><input name="Submit" type="submit" value="Save"></h6>

</form>

<?php
	break;
}
?>
