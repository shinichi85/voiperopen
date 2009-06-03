<?php
// Copyright (C) 2005-2006 SpheraIT
?>

<script language="Javascript">

	function checkNatConfig(theForm) {

	defaultEmptyOK = false;
	if (!isInteger(theForm.port0.value))
	return warnInvalid(theForm.port0, "Please enter a valid sip Port. Example: 5060");

	defaultEmptyOK = false;
	if (!isInteger(theForm.registertimeout0.value))
	return warnInvalid(theForm.registertimeout0, "Please enter a valid Register Timeout Time. Example: 20");

	defaultEmptyOK = false;
	if (!isInteger(theForm.registerattempts0.value))
	return warnInvalid(theForm.registerattempts0, "Please enter a valid Register Attempts. Example: 10");

	defaultEmptyOK = false;
	if (!isInteger(theForm.checkmwi0.value))
	return warnInvalid(theForm.checkmwi0, "Please enter a valid Mailbox check Time. Example: 10");

	defaultEmptyOK = false;
	if (!isInteger(theForm.maxexpirey0.value))
	return warnInvalid(theForm.maxexpirey0, "Please enter a valid Max expirey Time. Example: 3600");

	defaultEmptyOK = false;
	if (!isInteger(theForm.defaultexpirey0.value))
	return warnInvalid(theForm.defaultexpirey0, "Please enter a valid Default expirey Time. Example: 120");

	defaultEmptyOK = false;
	if (!isAlphanumeric(theForm.context0.value))
	return warnInvalid(theForm.context0, "Please enter a valid Default Context. Example: from-sip-external");

	defaultEmptyOK = false;
	if (!isAlphanumeric(theForm.language0.value))
	return warnInvalid(theForm.language0, "Please enter a valid Default Language. Example: it");

	defaultEmptyOK = false;
	if (theForm.bindaddr0.value == "")
	return warnInvalid(theForm.bindaddr0, "Please enter a valid Bind Adress IP. Example: 0.0.0.0");

	defaultEmptyOK = false;
	if (theForm.rtptimeout0.value == "")
	return warnInvalid(theForm.rtptimeout0, "Please enter a valid RTP Timeout. Example: 60");

	defaultEmptyOK = false;
	if (theForm.rtpholdtimeout0.value == "")
	return warnInvalid(theForm.rtpholdtimeout0, "Please enter a valid RTP Hold Timeout. Example: 300");

	defaultEmptyOK = false;
	if (theForm.rtpportstart.value == "")
	return warnInvalid(theForm.rtpportstart, "Please enter a valid RTP Port Start. Example: 15000");

	defaultEmptyOK = false;
	if (theForm.rtpportend.value == "")
	return warnInvalid(theForm.rtpportend, "Please enter a valid RTP Port End. Example: 20000");

	defaultEmptyOK = false;
	if (theForm.localnet0.value == "")
	return warnInvalid(theForm.localnet0, "Please enter a valid localnet Adress IP/NETMask. Example: 192.168.0.0/255.255.0.0");

	defaultEmptyOK = true;
	if (!isInteger(theForm.externrefresh0.value))
	return warnInvalid(theForm.externrefresh0, "Please enter a valid External Refresh Time. Example: 10");

	theForm.submit();
	}

</script>
<?php

$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_natconf_from_mysql.pl';

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';

$dispnum = 2;

if ($action == 'editglobals') {

	$count = $_POST["count"];

	for($i = 0; $i < $count; $i++) {

		$rtpkeepalive = $_POST["rtpkeepalive".$i];
		$t38pt_udptl = $_POST["t38pt_udptl".$i];
		$tos_video = $_POST["tos_video".$i];
		$tos_audio = $_POST["tos_audio".$i];
		$allowsubscribe = $_POST["allowsubscribe".$i];
		$notifyhold = $_POST["notifyhold".$i];
		$limitonpeer = $_POST["limitonpeer".$i];
		$pedantic = $_POST["pedantic".$i];
		$progressinband = $_POST["progressinband".$i];
		$insecure = $_POST["insecure".$i];
		$notifyringing = $_POST["notifyringing".$i];
		$registerattempts = $_POST["registerattempts".$i];
		$autodomain = $_POST["autodomain".$i];
		$externip = $_POST["externip".$i];
		$localnet =  $_POST["localnet".$i];
		$externrefresh =  $_POST["externrefresh".$i];
		$externhost =  $_POST["externhost".$i];
		$musicclass = $_POST["musicclass".$i];
		$relaxdtmf = $_POST["relaxdtmf".$i];
		$nat = $_POST["nat".$i];
		$recordhistory = $_POST["recordhistory".$i];
		$rtpholdtimeout = $_POST["rtpholdtimeout".$i];
		$rtptimeout = $_POST["rtptimeout".$i];
		$videosupport = $_POST["videosupport".$i];
		$tos_sip = $_POST["tos_sip".$i];
		$usereqphone = $_POST["usereqphone".$i];
		$allowguest = $_POST["allowguest".$i];
		$defaultexpirey = $_POST["defaultexpirey".$i];
		$maxexpirey = $_POST["maxexpirey".$i];
		$srvlookup = $_POST["srvlookup".$i];
		$checkmwi = $_POST["checkmwi".$i];
		$useragent = $_POST["useragent".$i];
		$registertimeout = $_POST["registertimeout".$i];
		$language = $_POST["language".$i];
		$callerid = $_POST["callerid".$i];
		$context = $_POST["context".$i];
		$allow = $_POST["allow".$i];
		$disallow = $_POST["disallow".$i];
		$bindaddr = $_POST["bindaddr".$i];
		$port = $_POST["port".$i];
		$id =  $_POST["id".$i];

        $localnet = str_replace("\r\n",";",$localnet);

		$sql = "UPDATE natconf SET port='$port',bindaddr='$bindaddr',disallow='$disallow',allow='$allow',context='$context',callerid='$callerid',language='$language',registertimeout='$registertimeout',useragent='$useragent',checkmwi='$checkmwi',srvlookup='$srvlookup',maxexpirey='$maxexpirey',defaultexpirey='$defaultexpirey',allowguest='$allowguest',usereqphone='$usereqphone',tos_sip='$tos_sip', videosupport='$videosupport', rtptimeout='$rtptimeout', rtpholdtimeout='$rtpholdtimeout', recordhistory='$recordhistory', nat='$nat', relaxdtmf='$relaxdtmf', musicclass='$musicclass',externip='$externip', localnet='$localnet', externrefresh='$externrefresh', externhost='$externhost', autodomain='$autodomain', registerattempts='$registerattempts', notifyringing='$notifyringing', insecure='$insecure', progressinband='$progressinband', pedantic='$pedantic', limitonpeer='$limitonpeer', notifyhold='$notifyhold', allowsubscribe='$allowsubscribe', tos_audio='$tos_audio', tos_video='$tos_video', t38pt_udptl='$t38pt_udptl', rtpkeepalive='$rtpkeepalive' WHERE id='$id'";
		$res =& $db->query($sql);
		if (DB::isError($res)) {
		    die($res->getMessage());
		}

		$rtpportend = $_POST["rtpportend"];
		$rtpportstart = $_POST["rtpportstart"];


		$sql = "UPDATE rtpconf SET rtpportstart='$rtpportstart',rtpportend='$rtpportend' WHERE id='$id'";
		$res =& $db->query($sql);
		if (DB::isError($res)) {
		    die($res->getMessage());
		}

	}

	unset($rtpkeepalive);
	unset($t38pt_udptl);
	unset($tos_video);
	unset($tos_audio);
	unset($allowsubscribe);
	unset($notifyhold);
	unset($limitonpeer);
	unset($pedantic);
	unset($progressinband);
	unset($insecure);
	unset($notifyringing);
	unset($registerattempts);
	unset($autodomain);
	unset($externip);
	unset($localnet);
	unset($externrefresh);
	unset($externhost);
	unset($musicclass);
	unset($relaxdtmf);
	unset($nat);
	unset($recordhistory);
	unset($rtpholdtimeout);
	unset($rtptimeout);
	unset($videosupport);
	unset($tos_sip);
	unset($usereqphone);
	unset($allowguest);
	unset($defaultexpirey);
	unset($maxexpirey);
	unset($srvlookup);
	unset($checkmwi);
	unset($useragent);
	unset($registertimeout);
	unset($language);
	unset($callerid);
	unset($context);
	unset($allow);
	unset($disallow);
	unset($bindaddr);
	unset($port);
	unset($id);

	unset($rtpportend);
	unset($rtpportstart);

	exec($wScript);
	needreload();
}

$sql = "SELECT * FROM natconf ORDER BY id ASC";
$results = $db->getAll($sql);
if(DB::IsError($results)) {
die($results->getMessage());
}

$count = 0;
foreach ($results as $result) {

	$rtpkeepalive[$count] = $result[40];
	$t38pt_udptl[$count] = $result[39];
	$tos_video[$count] = $result[38];
	$tos_audio[$count] = $result[37];
	$allowsubscribe[$count] = $result[36];
	$notifyhold[$count] = $result[35];
	$limitonpeer[$count] = $result[34];
	$pedantic[$count] = $result[33];
	$progressinband[$count] = $result[32];
	$insecure[$count] = $result[31];
	$notifyringing[$count] = $result[30];
	$registerattempts[$count] = $result[29];
	$autodomain[$count] = $result[28];
	$externhost[$count] = $result[27];
	$externrefresh[$count] = $result[26];
	$localnet[$count] = $result[25];
	$externip[$count] = $result[24];
	$musicclass[$count] = $result[23];
	$relaxdtmf[$count] = $result[22];
	$nat[$count] = $result[21];
	$recordhistory[$count] = $result[20];
	$rtpholdtimeout[$count] = $result[19];
	$rtptimeout[$count] = $result[18];
	$videosupport[$count] = $result[17];
	$tos_sip[$count] = $result[16];
	$usereqphone[$count] = $result[15];
	$allowguest[$count] = $result[14];
	$defaultexpirey[$count] = $result[13];
	$maxexpirey[$count] = $result[12];
	$srvlookup[$count] = $result[11];
	$checkmwi[$count] = $result[10];
	$useragent[$count] = $result[9];
	$registertimeout[$count] = $result[8];
	$language[$count] = $result[7];
	$callerid[$count] = $result[6];
	$context[$count] = $result[5];
	$allow[$count] = $result[4];
	$disallow[$count] = $result[3];
	$bindaddr[$count] = $result[2];
	$port[$count] = $result[1];
	$id[$count] = $result[0];
	$count++;
}

$sql = "SELECT * FROM rtpconf ORDER BY id ASC";
$results = $db->getAll($sql);
if(DB::IsError($results)) {
die($results->getMessage());
}

$count = 0;
foreach ($results as $result) {

	$rtpportend = $result[2];
	$rtpportstart = $result[1];
	$count++;
}

?>

<form name="speeddial" action="config.php?mode=settings&amp;display=<?php echo urlencode($dispnum)?>" method="post">
<input type="hidden" name="display" value="<?php echo $dispnum?>"/>
<input type="hidden" name="action" value="editglobals"/>
<input type="hidden" name="count" value="<?php echo $count; ?>"/>
<h3><?php echo _("General Sip Settings:")?></h3>
<p>
<table border="0" cellpadding="3" cellspacing="1">

	<?php
	for($i = 0; $i < $count; $i++) {
		echo "<tr>";
		echo "<td></td><td><input type=\"hidden\" name=\"id$i\" value=\"".$id[$i]."\"></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">Default Port:</td><td><input type=\"text\" size=\"5\" name=\"port$i\" value=\"".$port[$i]."\"></td>";
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
		echo "<td align=\"left\">Default context:</td><td><input type=\"text\" size=\"25\" name=\"context$i\" value=\"".$context[$i]."\"></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">Default callerid:</td><td><input type=\"text\" size=\"25\" name=\"callerid$i\" value=\"".$callerid[$i]."\"></td>";
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
		echo "<td align=\"left\">Register Timeout:</td><td><input type=\"text\" size=\"8\" name=\"registertimeout$i\" value=\"".$registertimeout[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">Register Attempts:</td><td><input type=\"text\" size=\"4\" name=\"registerattempts$i\" value=\"".$registerattempts[$i]."\"> <b>0 = Continue forever</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">Useragent name:</td><td><input type=\"text\" size=\"25\" name=\"useragent$i\" value=\"".$useragent[$i]."\"></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">Mailbox checks time:</td><td><input type=\"text\" size=\"8\" name=\"checkmwi$i\" value=\"".$checkmwi[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">DNS Server lookups:</td><td>&nbsp;&nbsp;<select name=\"srvlookup$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($srvlookup[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Max expirey:</td><td><input type=\"text\" size=\"8\" name=\"maxexpirey$i\" value=\"".$maxexpirey[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">Default expirey:</td><td><input type=\"text\" size=\"8\" name=\"defaultexpirey$i\" value=\"".$defaultexpirey[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Allow guest:</td><td>&nbsp;&nbsp;<select name=\"allowguest$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($allowguest[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Usereqphone:</td><td>&nbsp;&nbsp;<select name=\"usereqphone$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($usereqphone[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">TOS RTP audio packets:</td><td><input type=\"text\" size=\"4\" name=\"tos_audio$i\" value=\"".$tos_audio[$i]."\"></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=\"left\">TOS RTP video packets:</td><td><input type=\"text\" size=\"4\" name=\"tos_video$i\" value=\"".$tos_video[$i]."\"></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=\"left\">TOS SIP packets:</td><td><input type=\"text\" size=\"4\" name=\"tos_sip$i\" value=\"".$tos_sip[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">SIP Video Support:</td><td>&nbsp;&nbsp;<select name=\"videosupport$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($videosupport[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Record SIP history:</td><td>&nbsp;&nbsp;<select name=\"recordhistory$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($recordhistory[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">NAT Global settings:</td><td>&nbsp;&nbsp;<select name=\"nat$i\" size=\"1\">";

		echo "<option value=\"yes\"";
		if ($nat[$i] == 'yes') { echo "selected"; }
		echo ">Yes</option>";

		echo "<option value=\"no\"";
		if ($nat[$i] == 'no') { echo "selected"; }
		echo ">No</option>";

		echo "<option value=\"never\"";
		if ($nat[$i] == 'never') { echo "selected"; }
		echo ">Never</option>";

		echo "<option value=\"route\"";
		if ($nat[$i] == 'route') { echo "selected"; }
		echo ">Route</option>";

		echo "</select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">More DTMF detection:</td><td>&nbsp;&nbsp;<select name=\"relaxdtmf$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($relaxdtmf[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">AutoDomain:</td><td>&nbsp;&nbsp;<select name=\"autodomain$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($autodomain[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Notify Ringing:</td><td>&nbsp;&nbsp;<select name=\"notifyringing$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($notifyringing[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Notify Hold:</td><td>&nbsp;&nbsp;<select name=\"notifyhold$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($notifyhold[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Allow Subscribe:</td><td>&nbsp;&nbsp;<select name=\"allowsubscribe$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($allowsubscribe[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";
		
	    echo "<tr>";
		echo "<td align=\"left\">Limit on Peer:</td><td>&nbsp;&nbsp;<select name=\"limitonpeer$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($limitonpeer[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Insecure:</td><td>&nbsp;&nbsp;<select name=\"insecure$i\" size=\"1\">";

		echo "<option value=\"yes\"";
		if ($insecure[$i] == 'yes') { echo "selected"; }
		echo ">Yes</option>";

		echo "<option value=\"no\"";
		if ($insecure[$i] == 'no') { echo "selected"; }
		echo ">No</option>";

		echo "<option value=\"very\"";
		if ($insecure[$i] == 'very') { echo "selected"; }
		echo ">Very</option>";

		echo "<option value=\"invite\"";
		if ($insecure[$i] == 'invite') { echo "selected"; }
		echo ">Invite</option>";

		echo "<option value=\"port\"";
		if ($insecure[$i] == 'port') { echo "selected"; }
		echo ">Port</option>";

		echo "</select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">ProgressInBand:</td><td>&nbsp;&nbsp;<select name=\"progressinband$i\" size=\"1\">";

		echo "<option value=\"yes\"";
		if ($progressinband[$i] == 'yes') { echo "selected"; }
		echo ">Yes</option>";

		echo "<option value=\"no\"";
		if ($progressinband[$i] == 'no') { echo "selected"; }
		echo ">No</option>";

		echo "<option value=\"never\"";
		if ($progressinband[$i] == 'never') { echo "selected"; }
		echo ">Never</option>";

		echo "</select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Pedantic:</td><td>&nbsp;&nbsp;<select name=\"pedantic$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($pedantic[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">T.38 Fax passthrough:</td><td>&nbsp;&nbsp;<select name=\"t38pt_udptl$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($t38pt_udptl[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Hold Music Category:</td>";
		echo "<td>&nbsp;&nbsp;";
		echo "<select name=\"musicclass$i\" size=\"1\">";

				$tresults = getmusiccategory("/var/lib/asterisk/mohmp3");
				$default = (isset($musicclass[$i]) ? $musicclass[$i] : 'default');
				echo '<option value="default">'._("Default");
				if (isset($tresults)) {
					foreach ($tresults as $tresult) {
						$searchvalue="$tresult";
						echo '<option value="'.$tresult.'" '.($searchvalue == $default ? 'SELECTED' : '').'>'.$tresult;
					}
				}

		echo "</select>";
		echo "</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Externhost:</td><td><input type=\"text\" size=\"25\" name=\"externhost$i\" value=\"".$externhost[$i]."\"></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">Externrefresh:</td><td><input type=\"text\" size=\"8\" name=\"externrefresh$i\" value=\"".$externrefresh[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";

        $localnet = str_replace(";","\r\n",$localnet[$i]);

		echo "<tr>";
        echo "<td valign=\"top\" align=\"left\">Localnet:</td><td>&nbsp;&nbsp;<textarea rows=\"5\" cols=\"35\" name=\"localnet$i\">".$localnet."</textarea></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">Externip:</td><td><input type=\"text\" size=\"25\" name=\"externip$i\" value=\"".$externip[$i]."\"></td>";
		echo "</tr>";

        echo "<tr>";
        echo "<td><h5>RTP Sip Settings:</h5></td>";
        echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">RTP Timeout:</td><td><input type=\"text\" size=\"8\" name=\"rtptimeout$i\" value=\"".$rtptimeout[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">RTP Hold Timeout:</td><td><input type=\"text\" size=\"8\" name=\"rtpholdtimeout$i\" value=\"".$rtpholdtimeout[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">RTP Keepalive:</td><td><input type=\"text\" size=\"8\" name=\"rtpkeepalive$i\" value=\"".$rtpkeepalive[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">RTP Port Start:</td><td><input type=\"text\" size=\"10\" name=\"rtpportstart\" value=\"".$rtpportstart."\"></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=\"left\">RTP Port End:</td><td><input type=\"text\" size=\"10\" name=\"rtpportend\" value=\"".$rtpportend."\"></td>";
		echo "</tr>";



		}
	 ?>
</table>
</p>

<h6>
<input name="Submit" type="button" value="<?php echo _("Submit Changes")?>" onclick="checkNatConfig(speeddial)">
</h6>
</form>
