<?php
// Copyright (C) 2005-2008 SpheraIT
?>

<?php

$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_cdrpush_from_mysql.pl';
$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$dispnum = 9;
$count = 0;

if ($action == 'edit') {

    $sql = "SELECT * FROM conf WHERE name LIKE '%cdrpush%'";
    $results = $db->getAll($sql);
        if(DB::IsError($results)) {
            die($results->getMessage().$sql);
        }

        foreach ($results as $result) {
            $nomelabel[$count] = $result[0];
            $count++;
        }
        
        for($i = 0; $i < $count; $i++) {
            $setlabel = $nomelabel[$i];
            $striplabel = explode(".", $nomelabel[$i]);
            $setvalue = "'" . mysql_real_escape_string($_REQUEST[$striplabel[1]]) . "'";
            $sql = "UPDATE conf SET value = $setvalue WHERE name = '$setlabel' LIMIT 1";
            $results =& $db->query($sql);
            if (DB::isError($results)) {
                die($results->getMessage().$sql);
            }
        }

	exec($wScript);
}

$sql = "SELECT * FROM conf WHERE name LIKE '%cdrpush%'";
$results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage().$sql);
    }

$count = 0;
foreach ($results as $result) {
    $lunghezza[$count] = $result[4];
    $descrizione[$count] = $result[3];
    $titolo[$count] = $result[2];
    $valore[$count] = $result[1];
    $striplabel = explode(".", $result[0]);
    $nomelabel[$count] = $striplabel[1];
    $count++;
}


?>

<form autocomplete="off" name="cdrpushsettings" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkConf(this);">
<input type="hidden" name="display" value="<?php echo $dispnum?>"/>
<input type="hidden" name="action" value="edit"/>
<h3><?php echo _("BI4Data Settings:")?></h3>
<p><table border="0" cellpadding="3" cellspacing="1">

<?php

    for($i = 0; $i < $count; $i++) {
        echo "<tr>";

        if ($nomelabel[$i] == "password") {
            echo "<td align=\"left\"><a href=\"#\" class=\"info\">$titolo[$i]:<span>$descrizione[$i]</span></a></td><td><input type=\"password\" size=\"$lunghezza[$i]\" name=\"$nomelabel[$i]\" value=\"$valore[$i]\"></td>";
        } else if ($nomelabel[$i] == "oldfiles") {
                    echo "<td align=\"left\"><a href=\"#\" class=\"info\">$titolo[$i]:<span>$descrizione[$i]</span></a></td><td>&nbsp;&nbsp;<select name=\"$nomelabel[$i]\">";
                    $tresults = array('no','yes');
                    $default = (isset($valore[$i]) ? $valore[$i] : 'no');
                    if (isset($tresults)) {
                        foreach ($tresults as $tresult) {
                            $searchvalue="$tresult";
                            if ($tresult == "no") { $selectname  = "No"; }
                            if ($tresult == "yes") { $selectname = "Yes"; }
                            echo '<option value="'.$tresult.'" '.($searchvalue == $default ? 'SELECTED' : '').'>'.$selectname.'</option>\n';
                        }
                    }
                echo '</select></td>';
        } else {
                        echo "<td align=\"left\"><a href=\"#\" class=\"info\">$titolo[$i]:<span>$descrizione[$i]</span></a></td><td><input type=\"text\" size=\"$lunghezza[$i]\" name=\"$nomelabel[$i]\" value=\"$valore[$i]\"></td>";
        }
        echo "</tr>";
    }
echo "<tr>";
echo "<td colspan=\"2\"><h5>When you SUBMIT if the service has started, will be restarted.</h5></td>";
echo "</tr>";

?>
</table></p><br>
<h6><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>"></h6></form>

<script language="javascript">
<!--

function checkConf()
{
    var error = "<?php echo _('This field cannot be empty.'); ?>";
    var theForm = document.cdrpushsettings;

    defaultEmptyOK = false;
    if (theForm.username.value.length == 0)
        return warnInvalid(theForm.username, error);
    if (theForm.password.value.length == 0)
        return warnInvalid(theForm.password, error);
    if (theForm.hostname.value.length == 0)
        return warnInvalid(theForm.hostname, error);
    if (theForm.port.value.length == 0)
        return warnInvalid(theForm.port, error);
    if (theForm.devfilename.value.length == 0)
        return warnInvalid(theForm.devfilename, error);
    if (theForm.period.value.length == 0)
        return warnInvalid(theForm.period, error);
    if (theForm.userfilename.value.length == 0)
        return warnInvalid(theForm.userfilename, error);
    if (theForm.idleinterval.value.length == 0)
        return warnInvalid(theForm.idleinterval, error);
    if (theForm.minfilesize.value.length == 0)
        return warnInvalid(theForm.minfilesize, error);
    if (theForm.maxdelay.value.length == 0)
        return warnInvalid(theForm.maxdelay, error);
    return true;
}

//-->
</script>
