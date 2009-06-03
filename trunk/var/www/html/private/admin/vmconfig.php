<?php
// Copyright (C) 2005-2006 SpheraIT
?>

<script language="Javascript">

	function checkVMConfig(theForm) {

	defaultEmptyOK = false;
	if (!isAlphanumeric(theForm.serveremail0.value))
	return warnInvalid(theForm.serveremail0, "Please enter a valid name for email notification. Example: voiper");

	defaultEmptyOK = false;
	if (!isInteger(theForm.maxmessage0.value))
	return warnInvalid(theForm.maxmessage0, "Please enter a valid Max Message Lenght. Example: 180 seconds");

	defaultEmptyOK = false;
	if (!isInteger(theForm.minmessage0.value))
	return warnInvalid(theForm.minmessage0, "Please enter a valid Min Message Lenght. Example: 3 seconds");

	defaultEmptyOK = false;
	if (!isInteger(theForm.skipms0.value))
	return warnInvalid(theForm.skipms0, "Please enter a valid Skip Message Lenght. Example: 3000 MilliSeconds");

	defaultEmptyOK = false;
	if (!isInteger(theForm.maxsilence0.value))
	return warnInvalid(theForm.maxsilence0, "Please enter a valid Max Silence Lenght. Example: 5 MilliSeconds");

	defaultEmptyOK = false;
	if (!isInteger(theForm.silencethreshold0.value))
	return warnInvalid(theForm.silencethreshold0, "Please enter a valid Max Silence Lenght. Example: 128 MilliSeconds");

	defaultEmptyOK = false;
	if (!isInteger(theForm.maxlogins0.value))
	return warnInvalid(theForm.maxlogins0, "Please enter a valid Max Login Attempt. Example: 3");

	defaultEmptyOK = false;
	if (!isAlphanumeric(theForm.fromstring0.value))
	return warnInvalid(theForm.fromstring0, "Please enter a valid FROM string. Example: Voiper Voicemail System");

	defaultEmptyOK = false;
	if (!isInteger(theForm.maxmsg0.value))
	return warnInvalid(theForm.maxmsg0, "Please enter a valid Max Message for the Voicemail (MAX 9999). Example: 100");

	defaultEmptyOK = false;
	if (!isInteger(theForm.maxgreet0.value))
	return warnInvalid(theForm.maxgreet0, "Please enter a valid Message Lenght. Example: 60 seconds");

	theForm.submit();
	}

</script>
<?php

$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_vmconf_from_mysql.pl';

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';

$dispnum = 6;

if ($action == 'editglobals') {

	$count = $_POST["count"];

	for($i = 0; $i < $count; $i++) {

		$externpass = $_POST["externpass".$i];
		$externnotify = $_POST["externnotify".$i];
		$maxgreet = $_POST["maxgreet".$i];
		$maxmsg = $_POST["maxmsg".$i];
		$emailbody = addslashes($_POST["emailbody".$i]);
		$emailsubject = $_POST["emailsubject".$i];
		$operator = $_POST["operator".$i];
		$review = $_POST["review".$i];
		$sendvoicemail = $_POST["sendvoicemail".$i];
		$fromstring = $_POST["fromstring".$i];
		$pbxskip = $_POST["pbxskip".$i];
		$maxlogins = $_POST["maxlogins".$i];
		$silencethreshold = $_POST["silencethreshold".$i];
		$maxsilence = $_POST["maxsilence".$i];
		$skipms = $_POST["skipms".$i];
		$minmessage = $_POST["minmessage".$i];
		$maxmessage = $_POST["maxmessage".$i];
		$attach = $_POST["attach".$i];
		$serveremail = $_POST["serveremail".$i];
		$format = $_POST["format".$i];
		$id = $_POST["id".$i];

		$sql = "UPDATE vmconfig SET format='$format',serveremail='$serveremail',attach='$attach',maxmessage='$maxmessage',minmessage='$minmessage',skipms='$skipms',maxsilence='$maxsilence',silencethreshold='$silencethreshold',maxlogins='$maxlogins',pbxskip='$pbxskip',fromstring='$fromstring',sendvoicemail='$sendvoicemail',review='$review',operator='$operator',emailsubject='$emailsubject',emailbody='$emailbody',maxmsg='$maxmsg',maxgreet='$maxgreet',externnotify='$externnotify',externpass='$externpass' WHERE id='$id'";
		$res =& $db->query($sql);
		if (DB::isError($res)) {
		    die($res->getMessage());
		}

	}


	unset($id);
	unset($format);
	unset($serveremail);
	unset($attach);
	unset($maxmessage);
	unset($minmessage);
	unset($skipms);
	unset($maxsilence);
	unset($silencethreshold);
	unset($maxlogins);
	unset($pbxskip);
	unset($fromstring);
	unset($sendvoicemail);
	unset($review);
	unset($operator);
	unset($emailsubject);
	unset($emailbody);
	unset($maxmsg);
	unset($maxgreet);
	unset($externnotify);
	unset($externpass);

	exec($wScript);
	needreload();
}

$sql = "SELECT * FROM vmconfig ORDER BY id ASC";
$results = $db->getAll($sql);
if(DB::IsError($results)) {
die($results->getMessage());
}

$count = 0;
foreach ($results as $result) {

	$externpass[$count] = $result[20];
	$externnotify[$count] = $result[19];
	$maxgreet[$count] = $result[18];
	$maxmsg[$count] = $result[17];
	$emailbody[$count] = $result[16];
	$emailsubject[$count] = $result[15];
	$operator[$count] = $result[14];
	$review[$count] = $result[13];
	$sendvoicemail[$count] = $result[12];
	$fromstring[$count] = $result[11];
	$pbxskip[$count] = $result[10];
	$maxlogins[$count] = $result[9];
	$silencethreshold[$count] = $result[8];
	$maxsilence[$count] = $result[7];
	$skipms[$count] = $result[6];
	$minmessage[$count] = $result[5];
	$maxmessage[$count] = $result[4];
	$attach[$count] = $result[3];
	$serveremail[$count] = $result[2];
	$format[$count] = $result[1];
	$id[$count] = $result[0];

	$count++;
}


?>

<form name="vmconfig" action="config.php?mode=settings&amp;display=<?php echo urlencode($dispnum)?>" method="post">
<input type="hidden" name="display" value="<?php echo $dispnum?>"/>
<input type="hidden" name="action" value="editglobals"/>
<input type="hidden" name="count" value="<?php echo $count; ?>"/>
<h3><?php echo _("General VoiceMail Settings:")?></h3>
<p>
<table border="0" cellpadding="3" cellspacing="1">

	<?php
	for($i = 0; $i < $count; $i++) {
		echo "<tr>";
		echo "<td></td><td><input type=\"hidden\" name=\"id$i\" value=\"".$id[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Format:</td><td>&nbsp;&nbsp;<select name=\"format$i\" size=\"1\">";

		echo "<option value=\"wav49\"";
		if ($format[$i] == 'wav49') { echo "selected"; }
		echo ">wav49</option>";

		echo "<option value=\"gsm\"";
		if ($format[$i] == 'gsm') { echo "selected"; }
		echo ">gsm</option>";

		echo "<option value=\"wav\"";
		if ($format[$i] == 'wav') { echo "selected"; }
		echo ">wav</option>";

		echo "<option value=\"wav49|gsm|wav\"";
		if ($format[$i] == 'wav49|gsm|wav') { echo "selected"; }
		echo ">wav49|gsm|wav</option>";

		echo "</select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Server Email:</td><td><input type=\"text\" size=\"30\" name=\"serveremail$i\" value=\"".$serveremail[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">File Attachment:</td><td>&nbsp;&nbsp;<select name=\"attach$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($attach[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Max Message Lenght:</td><td><input type=\"text\" size=\"5\" name=\"maxmessage$i\" value=\"".$maxmessage[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Min Message Lenght:</td><td><input type=\"text\" size=\"5\" name=\"minmessage$i\" value=\"".$minmessage[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Skip Message:</td><td><input type=\"text\" size=\"5\" name=\"skipms$i\" value=\"".$skipms[$i]."\"> <b>MilliSeconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Max Silence:</td><td><input type=\"text\" size=\"5\" name=\"maxsilence$i\" value=\"".$maxsilence[$i]."\"> <b>MilliSeconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Silence Threshold:</td><td><input type=\"text\" size=\"5\" name=\"silencethreshold$i\" value=\"".$silencethreshold[$i]."\"> <b>MilliSeconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Max Logins Attempt:</td><td><input type=\"text\" size=\"5\" name=\"maxlogins$i\" value=\"".$maxlogins[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">PBX Subject:</td><td>&nbsp;&nbsp;<select name=\"pbxskip$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($pbxskip[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">From String:</td><td><input type=\"text\" size=\"30\" name=\"fromstring$i\" value=\"".$fromstring[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Send Voicemail:</td><td>&nbsp;&nbsp;<select name=\"sendvoicemail$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($sendvoicemail[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Review:</td><td>&nbsp;&nbsp;<select name=\"review$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($review[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Operator:</td><td>&nbsp;&nbsp;<select name=\"operator$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($operator[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Max Messages:</td><td><input type=\"text\" size=\"5\" name=\"maxmsg$i\" value=\"".$maxmsg[$i]."\"> <b>Max: 9999</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Max Greetings Lenght:</td><td><input type=\"text\" size=\"5\" name=\"maxgreet$i\" value=\"".$maxgreet[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">External Notify Script:</td><td><input type=\"text\" size=\"40\" name=\"externnotify$i\" value=\"".$externnotify[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">External Password Script:</td><td><input type=\"text\" size=\"40\" name=\"externpass$i\" value=\"".$externpass[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td valign=\"top\" align=\"left\">Email Subject:</td>";
		echo "<td>&nbsp;&nbsp;<textarea rows=\"5\" cols=\"40\" name=\"emailsubject$i\">$emailsubject[$i]</textarea></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td valign=\"top\" align=\"left\">Email Body:</td>";
		echo "<td>&nbsp;&nbsp;<textarea rows=\"5\" cols=\"40\" size=\"512\" name=\"emailbody$i\">$emailbody[$i]</textarea></td>";
		echo "</tr>";

		}
	 ?>
</table>
</p>

<h6>
<input name="Submit" type="button" value="<?php echo _("Submit Changes")?>" onclick="checkVMConfig(vmconfig)">
</h6>
</form>
