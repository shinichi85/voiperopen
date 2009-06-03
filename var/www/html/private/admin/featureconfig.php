<?php
// Copyright (C) 2005-2006 SpheraIT
?>

<script language="Javascript">

	function checkFEATUREConfig(theForm) {


	theForm.submit();
	}

</script>
<?php

$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_featureconf_from_mysql.pl';

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';

$dispnum = 7;

if ($action == 'editglobals') {

	$count = $_POST["count"];

	for($i = 0; $i < $count; $i++) {

		$testfeature = $_POST["testfeature".$i];
		$pickupexten = $_POST["pickupexten".$i];
		$automon = $_POST["automon".$i];
		$disconnect = $_POST["disconnect".$i];
		$atxfer = $_POST["atxfer".$i];
		$blindxfer = $_POST["blindxfer".$i];
		$adsipark = $_POST["adsipark".$i];
		$featuredigittimeout = $_POST["featuredigittimeout".$i];
		$xferfailsound = $_POST["xferfailsound".$i];
		$xfersound = $_POST["xfersound".$i];
		$courtesytone = $_POST["courtesytone".$i];
		$transferdigittimeout = $_POST["transferdigittimeout".$i];
		$parkingtime = $_POST["parkingtime".$i];
		$context = $_POST["context".$i];
		$parkpos = $_POST["parkpos".$i];
		$parkext = $_POST["parkext".$i];
		$id = $_POST["id".$i];

		$sql = "UPDATE featureconfig SET parkext='$parkext',parkpos='$parkpos',context='$context',parkingtime='$parkingtime',transferdigittimeout='$transferdigittimeout',courtesytone='$courtesytone',pickupexten='$pickupexten',xfersound='$xfersound',xferfailsound='$xferfailsound',featuredigittimeout='$featuredigittimeout',blindxfer='$blindxfer',disconnect='$disconnect',automon='$automon',atxfer='$atxfer',adsipark='$adsipark',testfeature='$testfeature' WHERE id='$id'";
		$res =& $db->query($sql);
		if (DB::isError($res)) {
		    die($res->getMessage());
		}

	}

	unset($id);
	unset($parkext);
	unset($parkpos);
	unset($context);
	unset($parkingtime);
	unset($transferdigittimeout);
	unset($courtesytone);
	unset($xfersound);
	unset($xferfailsound);
	unset($featuredigittimeout);
	unset($adsipark);
	unset($blindxfer);
	unset($atxfer);
	unset($disconnect);
	unset($automon);
	unset($pickupexten);
	unset($testfeature);

	exec($wScript);
	needreload();
}

$sql = "SELECT * FROM featureconfig ORDER BY id ASC";
$results = $db->getAll($sql);
if(DB::IsError($results)) {
die($results->getMessage());
}

$count = 0;
foreach ($results as $result) {

	$testfeature[$count] = $result[16];
	$adsipark[$count] = $result[15];
	$atxfer[$count] = $result[14];
	$automon[$count] = $result[13];
	$disconnect[$count] = $result[12];
	$blindxfer[$count] = $result[11];
	$featuredigittimeout[$count] = $result[10];
	$xferfailsound[$count] = $result[9];
	$xfersound[$count] = $result[8];
	$pickupexten[$count] = $result[7];
	$courtesytone[$count] = $result[6];
	$transferdigittimeout[$count] = $result[5];
	$parkingtime[$count] = $result[4];
	$context[$count] = $result[3];
	$parkpos[$count] = $result[2];
	$parkext[$count] = $result[1];
	$id[$count] = $result[0];

	$count++;
}


?>

<form name="featureconfig" action="config.php?mode=settings&amp;display=<?php echo urlencode($dispnum)?>" method="post">
<input type="hidden" name="display" value="<?php echo $dispnum?>"/>
<input type="hidden" name="action" value="editglobals"/>
<input type="hidden" name="count" value="<?php echo $count; ?>"/>
<h3><?php echo _("General FeatureMap Settings:")?></h3>
<p>
<table border="0" cellpadding="3" cellspacing="1">

	<?php
	for($i = 0; $i < $count; $i++) {
		echo "<tr>";
		echo "<td></td><td><input type=\"hidden\" name=\"id$i\" value=\"".$id[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Parking Extension:</td><td><input type=\"text\" size=\"10\" name=\"parkext$i\" value=\"".$parkext[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Parking Positions:</td><td><input type=\"text\" size=\"20\" name=\"parkpos$i\" value=\"".$parkpos[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Parking Context:</td><td><input type=\"text\" size=\"20\" name=\"context$i\" value=\"".$context[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Parking Timeout:</td><td><input type=\"text\" size=\"5\" name=\"parkingtime$i\" value=\"".$parkingtime[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Transfer Digit Timeout:</td><td><input type=\"text\" size=\"5\" name=\"transferdigittimeout$i\" value=\"".$transferdigittimeout[$i]."\"> <b>Seconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Courtesy Tone:</td><td><input type=\"text\" size=\"10\" name=\"courtesytone$i\" value=\"".$courtesytone[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Attended Complete Transfer:</td><td><input type=\"text\" size=\"10\" name=\"xfersound$i\" value=\"".$xfersound[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Attended Failed Transfer:</td><td><input type=\"text\" size=\"10\" name=\"xferfailsound$i\" value=\"".$xferfailsound[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Feature Digit Timeout:</td><td><input type=\"text\" size=\"5\" name=\"featuredigittimeout$i\" value=\"".$featuredigittimeout[$i]."\"> <b>MilliSeconds</b></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">ADSI Parking Announcements:</td><td>&nbsp;&nbsp;<select name=\"adsipark$i\" size=\"1\"><option value=\"yes\">Yes</option><option value=\"no\"";if ($adsipark[$i] == 'no') { echo "selected"; }
		echo ">No</option></select></td>";
		echo "</tr>";

        echo "<tr>";
        echo "<td><h5>FeatureMap Phone Extensions:</h5></td>";
        echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Blind Transfer Extension:</td><td><input type=\"text\" size=\"10\" name=\"blindxfer$i\" value=\"".$blindxfer[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Attended Transfer Extension:</td><td><input type=\"text\" size=\"10\" name=\"atxfer$i\" value=\"".$atxfer[$i]."\"></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align=\"left\">Disconnect Extension:</td><td><input type=\"text\" size=\"10\" name=\"disconnect$i\" value=\"".$disconnect[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Realtime Recording Extension:</td><td><input type=\"text\" size=\"10\" name=\"automon$i\" value=\"".$automon[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Pickup Extension</td><td><input type=\"text\" size=\"10\" name=\"pickupexten$i\" value=\"".$pickupexten[$i]."\"></td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=\"left\">Special Extension:</td><td><input type=\"text\" size=\"40\" name=\"testfeature$i\" value=\"".$testfeature[$i]."\"></td>";
		echo "</tr>";

		}
	 ?>
</table>
</p>
<br>
<h6>
<input name="Submit" type="button" value="<?php echo _("Submit Changes")?>" onclick="checkFEATUREConfig(featureconfig)">
</h6>
</form>
