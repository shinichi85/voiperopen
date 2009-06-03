<?php
// Copyright (C) 2005-2006 SpheraIT
?>

<script language="Javascript">
	
	function checkIAXConfig(theForm) {

	defaultEmptyOK = false;
	if (!isInteger(theForm.bindport0.value))
	return warnInvalid(theForm.bindport0, "Please enter a valid Iax Port. Example: 4569");

	defaultEmptyOK = false;
	if (theForm.bindaddr0.value == "")
	return warnInvalid(theForm.bindaddr0, "Please enter a valid Bind Adress IP. Example: 0.0.0.0");		
	
	defaultEmptyOK = false;
	if (!isAlphanumeric(theForm.language0.value))
	return warnInvalid(theForm.language0, "Please enter a valid Default Language. Example: it");
	
	defaultEmptyOK = false;
	if (!isInteger(theForm.trunkfreq0.value))
	return warnInvalid(theForm.trunkfreq0, "Please enter a valid Trunk Frequency Time. Example: 20");	

	defaultEmptyOK = false;
	if (!isAlphanumeric(theForm.amaflags0.value))
	return warnInvalid(theForm.amaflags0, "Please enter a valid Amaflags. Example: default");	
	
	defaultEmptyOK = false;
	if (!isAlphanumeric(theForm.accountcode0.value))
	return warnInvalid(theForm.accountcode0, "Please enter a valid Accountcode. Example: lss0101");	

	defaultEmptyOK = false;
	if (!isInteger(theForm.dropcount0.value))
	return warnInvalid(theForm.dropcount0, "Please enter a valid Dropcount. Example: 2");	
	
	defaultEmptyOK = false;
	if (!isInteger(theForm.maxjitterbuffer0.value))
	return warnInvalid(theForm.maxjitterbuffer0, "Please enter a valid Max Jitter Buffer. Example: 500");	
	
	defaultEmptyOK = false;
	if (!isInteger(theForm.maxexcessbuffer0.value))
	return warnInvalid(theForm.maxexcessbuffer0, "Please enter a valid Max Excess Buffer. Example: 80");	
	
	defaultEmptyOK = false;
	if (!isInteger(theForm.minexcessbuffer0.value))
	return warnInvalid(theForm.minexcessbuffer0, "Please enter a valid Min Excess Buffer. Example: 10");	
	
	defaultEmptyOK = false;
	if (!isInteger(theForm.jittershrinkrate0.value))
	return warnInvalid(theForm.jittershrinkrate0, "Please enter a valid Jittershrink Rate. Example: 1");	

	
			theForm.submit();
	}

</script>
<?php 

$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_iaxconf_from_mysql.pl';

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';

$dispnum = 3;

if ($action == 'editglobals') {
	
	$count = $_POST["count"];
	
	for($i = 0; $i < $count; $i++) {

		$iaxmaxthreadcount = $_POST["iaxmaxthreadcount".$i];
		$iaxthreadcount = $_POST["iaxthreadcount".$i];
		$maxregexpire = $_POST["maxregexpire".$i];
		$minregexpire = $_POST["minregexpire".$i];
		$trunktimestamps = $_POST["trunktimestamps".$i];
		$jittershrinkrate = $_POST["jittershrinkrate".$i];
		$minexcessbuffer = $_POST["minexcessbuffer".$i];
		$maxexcessbuffer = $_POST["maxexcessbuffer".$i];
		$maxjitterbuffer = $_POST["maxjitterbuffer".$i];
		$dropcount = $_POST["dropcount".$i];
		$accountcode = $_POST["accountcode".$i];
		$amaflags = $_POST["amaflags".$i];
		$authdebug = $_POST["authdebug".$i];
		$trunkfreq = $_POST["trunkfreq".$i];
		$autokill = $_POST["autokill".$i];
		$tos = $_POST["tos".$i];
		$jitterbuffer = $_POST["jitterbuffer".$i];
		$bandwidth = $_POST["bandwidth".$i];
		$language = $_POST["language".$i];
		$delayreject = $_POST["delayreject".$i];
		$iaxcompat = $_POST["iaxcompat".$i];
		$mailboxdetail = $_POST["mailboxdetail".$i];
		$allow = $_POST["allow".$i];
		$disallow = $_POST["disallow".$i];
		$bindaddr = $_POST["bindaddr".$i];
		$bindport = $_POST["bindport".$i];
		$id =  $_POST["id".$i];
		
		$sql = "UPDATE iaxconf SET bindport='$bindport',bindaddr='$bindaddr',disallow='$disallow',allow='$allow',mailboxdetail='$mailboxdetail',iaxcompat='$iaxcompat',delayreject='$delayreject',language='$language',bandwidth='$bandwidth',jitterbuffer='$jitterbuffer',tos='$tos',autokill='$autokill',trunkfreq='$trunkfreq',authdebug='$authdebug',amaflags='$amaflags',accountcode='$accountcode',dropcount='$dropcount',maxjitterbuffer='$maxjitterbuffer',maxexcessbuffer='$maxexcessbuffer',minexcessbuffer='$minexcessbuffer',jittershrinkrate='$jittershrinkrate',trunktimestamps='$trunktimestamps',minregexpire='$minregexpire',maxregexpire='$maxregexpire',maxregexpire='$maxregexpire',iaxthreadcount='$iaxthreadcount',iaxmaxthreadcount='$iaxmaxthreadcount' WHERE id='$id'";

		$res =& $db->query($sql);
		if (DB::isError($res)) {
		    die($res->getMessage());
		}
	}
	
	unset($id);
	unset($bindport);
	unset($bindaddr);
	unset($disallow);
	unset($allow);
	unset($mailboxdetail);
	unset($iaxcompat);
	unset($delayreject);
	unset($language);
	unset($bandwidth);
	unset($jitterbuffer);
	unset($tos);
	unset($autokill);
	unset($trunkfreq);
	unset($authdebug);
	unset($amaflags);
	unset($accountcode);
	unset($dropcount);
	unset($maxjitterbuffer);
	unset($maxexcessbuffer);
	unset($minexcessbuffer);
	unset($jittershrinkrate);
	unset($trunktimestamps);
	unset($minregexpire);
	unset($maxregexpire);
	unset($iaxthreadcount);
	unset($iaxmaxthreadcount);
	
	exec($wScript);
	needreload();
}

$sql = "SELECT * FROM iaxconf ORDER BY id ASC";
$results = $db->getAll($sql);
if(DB::IsError($results)) {
die($results->getMessage());
}

$count = 0;
foreach ($results as $result) {

	$iaxmaxthreadcount[$count] = $result[26];
    $iaxthreadcount[$count] = $result[25];
	$maxregexpire[$count] = $result[24];
	$minregexpire[$count] = $result[23];
	$trunktimestamps[$count] = $result[22];
	$jittershrinkrate[$count] = $result[21];
	$minexcessbuffer[$count] = $result[20];
	$maxexcessbuffer[$count] = $result[19];
	$maxjitterbuffer[$count] = $result[18];
	$dropcount[$count] = $result[17];
	$accountcode[$count] = $result[16];
	$amaflags[$count] = $result[15];
	$authdebug[$count] = $result[14];
	$trunkfreq[$count] = $result[13];
	$autokill[$count] = $result[12];
	$tos[$count] = $result[11];
	$jitterbuffer[$count] = $result[10];
	$bandwidth[$count] = $result[9];
	$language[$count] = $result[8];
	$delayreject[$count] = $result[7];
	$iaxcompat[$count] = $result[6];
	$mailboxdetail[$count] = $result[5];
	$allow[$count] = $result[4];
	$disallow[$count] = $result[3];
	$bindaddr[$count] = $result[2];
	$bindport[$count] = $result[1];
	$id[$count] = $result[0];
	$count++;
}
?>

<form name="speeddial" action="config.php?mode=settings&amp;display=<?php echo urlencode($dispnum)?>" method="post">
<input type="hidden" name="display" value="<?php echo $dispnum?>"/>
<input type="hidden" name="action" value="editglobals"/>
<input type="hidden" name="count" value="<?php echo $count; ?>"/>
<h3><?php echo _("General Iax Settings:")?></h3>
<p>
<table border="0" cellpadding="3" cellspacing="1">

	<?php 
	for($i = 0; $i < $count; $i++) { 
		echo "<tr>";
		echo "<td></td><td><input type=\"hidden\" name=\"id$i\" value=\"".$id[$i]."\"></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">Default Port:</td><td><input type=\"text\" size=\"5\" name=\"bindport$i\" value=\"".$bindport[$i]."\"></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">Bind Address:</td><td><input type=\"text\" size=\"25\" name=\"bindaddr$i\" value=\"".$bindaddr[$i]."\"></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">Default Codec (disallow):</td><td><input type=\"text\" size=\"35\" name=\"disallow$i\" value=\"".$disallow[$i]."\"></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">Default Codec (allow):</td><td><input type=\"text\" size=\"35\" name=\"allow$i\" value=\"".$allow[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Mailbox Detail:</td><td>&nbsp;&nbsp;<select name=\"mailboxdetail$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($mailboxdetail[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";		
		
		echo "<tr>";
		echo "<td align=\"left\">Iax compatibility:</td><td>&nbsp;&nbsp;<select name=\"iaxcompat$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($iaxcompat[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Delayreject:</td><td>&nbsp;&nbsp;<select name=\"delayreject$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($delayreject[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Default language:</td><td>&nbsp;&nbsp;<select name=\"language$i\" size=\"1\">";

		echo "<option value=\"en\"";
		if ($language[$i] == 'en') { echo "selected"; }
		echo ">English</option>";

		echo "<option value=\"it\"";
		if ($language[$i] == 'it') { echo "selected"; }
		echo ">Italian</option>";

		echo "</select></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=\"left\">Bandwidth:</td><td>&nbsp;&nbsp;<select name=\"bandwidth$i\" size=\"1\">";
		
		echo "<option value=\"low\"";
		if ($bandwidth[$i] == 'low') { echo "selected"; }
		echo ">low</option>";

		echo "<option value=\"medium\"";
		if ($bandwidth[$i] == 'medium') { echo "selected"; }
		echo ">medium</option>";

		echo "<option value=\"high\"";
		if ($bandwidth[$i] == 'high') { echo "selected"; }
		echo ">high</option>";

		echo "</select></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=\"left\">Jitterbuffer:</td><td>&nbsp;&nbsp;<select name=\"jitterbuffer$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($jitterbuffer[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";	
		
		echo "<tr>";
		echo "<td align=\"left\">Tos Presets:</td><td>&nbsp;&nbsp;<select name=\"tos$i\" size=\"1\">";
		
		echo "<option value=\"lowdelay\"";
		if ($tos[$i] == 'lowdelay') { echo "selected"; }
		echo ">low delay (0x10)</option>";

		echo "<option value=\"throughput\"";
		if ($tos[$i] == 'throughput') { echo "selected"; }
		echo ">high throughput (0x08)</option>";

		echo "<option value=\"reliability\"";
		if ($tos[$i] == 'reliability') { echo "selected"; }
		echo ">high reliability (0x04)</option>";

		echo "<option value=\"0x02\"";
		if ($tos[$i] == '0x02') { echo "selected"; }
		echo ">ECT bit set (0x02)</option>";		

		echo "<option value=\"0x01\"";
		if ($tos[$i] == '0x01') { echo "selected"; }
		echo ">CE bit set (0x01)</option>";		
		
		echo "<option value=\"0x18\"";
		if ($tos[$i] == '0x18') { echo "selected"; }
		echo ">low delay & high throughput (0x18)</option>";

		echo "<option value=\"0x14\"";
		if ($tos[$i] == '0x14') { echo "selected"; }
		echo ">low delay & high reliability (0x14)</option>";	

		echo "<option value=\"0x12\"";
		if ($tos[$i] == '0x12') { echo "selected"; }
		echo ">high throughput & high reliability (0x12)</option>";			
		
		echo "<option value=\"none\"";
		if ($tos[$i] == 'none') { echo "selected"; }
		echo ">none</option>";	
		
		echo "</select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Autokill:</td><td>&nbsp;&nbsp;<select name=\"autokill$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($autokill[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";	
		
		echo "<tr>";
		echo "<td align=\"left\">Send Trunk msgs:</td><td><input type=\"text\" size=\"8\" name=\"trunkfreq$i\" value=\"".$trunkfreq[$i]."\"> <b>MilliSeconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Authentication Debug:</td><td>&nbsp;&nbsp;<select name=\"authdebug$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($authdebug[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";		

		echo "<tr>";
		echo "<td align=\"left\">Amaflags:</td><td><input type=\"text\" size=\"15\" name=\"amaflags$i\" value=\"".$amaflags[$i]."\"></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=\"left\">Accountcode:</td><td><input type=\"text\" size=\"15\" name=\"accountcode$i\" value=\"".$accountcode[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Dropcount:</td><td><input type=\"text\" size=\"8\" name=\"dropcount$i\" value=\"".$dropcount[$i]."\"></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=\"left\">Max Jitter Buffer:</td><td><input type=\"text\" size=\"8\" name=\"maxjitterbuffer$i\" value=\"".$maxjitterbuffer[$i]."\"></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=\"left\">Max Excess Buffer:</td><td><input type=\"text\" size=\"8\" name=\"maxexcessbuffer$i\" value=\"".$maxexcessbuffer[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Min Excess Buffer:</td><td><input type=\"text\" size=\"8\" name=\"minexcessbuffer$i\" value=\"".$minexcessbuffer[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Jittershrink Rate:</td><td><input type=\"text\" size=\"8\" name=\"jittershrinkrate$i\" value=\"".$jittershrinkrate[$i]."\"></td>";
		echo "</tr>";



		echo "<tr>";
		echo "<td align=\"left\">Trunk Timestamps:</td><td><input type=\"text\" size=\"8\" name=\"trunktimestamps$i\" value=\"".$trunktimestamps[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Min registration Expire:</td><td><input type=\"text\" size=\"8\" name=\"minregexpire$i\" value=\"".$minregexpire[$i]."\"></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=\"left\">Max registration Expire Rate:</td><td><input type=\"text\" size=\"8\" name=\"maxregexpire$i\" value=\"".$maxregexpire[$i]."\"></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=\"left\">IAX thread count:</td><td><input type=\"text\" size=\"8\" name=\"iaxthreadcount$i\" value=\"".$iaxthreadcount[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">IAX max thread count:</td><td><input type=\"text\" size=\"8\" name=\"iaxmaxthreadcount$i\" value=\"".$iaxmaxthreadcount[$i]."\"></td>";
		echo "</tr>";
		
		} 
	 ?>
</table>
</p>

<h6>
<input name="Submit" type="button" value="<?php echo _("Submit Changes")?>" onclick="checkIAXConfig(speeddial)">
</h6>
</form>
