<?php
// Copyright (C) 2005-2008 SpheraIT
?>

<?php

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$dispnum = 8;
$count = 0;
global $db2;

if ($action == 'edit') {

    $sql = "SELECT * FROM cti_config";
    $results = $db2->getAll($sql);
        if(DB::IsError($results)) {
            die($results->getMessage().$sql);
        }

        foreach ($results as $result) {
            $nomelabel[$count] = $result[0];
            $count++;
        }

        for($i = 0; $i < $count; $i++) {
            $setlabel = $nomelabel[$i];
            $setvalue = "'" . mysql_real_escape_string($_REQUEST[$nomelabel[$i]]) . "'";
            $sql = "UPDATE cti_config SET valore = $setvalue WHERE chiave = '$setlabel' LIMIT 1";
            $results =& $db2->query($sql);
            if (DB::isError($results)) {
                die($results->getMessage().$sql);
            }
        }
 }

$sql = "SELECT * FROM cti_config";
$results = $db2->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage().$sql);
    }

$count = 0;
foreach ($results as $result) {
    $descrizione[$count] = $result[3];
    $titolo[$count] = $result[2];
    $valore[$count] = $result[1];
    $nomelabel[$count] = $result[0];
    $count++;
}
?>

<form autocomplete="off" name="ctisettings" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkConf(this);">
<input type="hidden" name="display" value="<?php echo $dispnum?>"/>
<input type="hidden" name="action" value="edit"/>
<h3><?php echo _("Cti Settings:")?></h3>
<p><table border="0" cellpadding="3" cellspacing="1">

<?php

    for($i = 0; $i < $count; $i++) {
        echo "<tr>";

        if ($nomelabel[$i] == "pass") {
            echo "<td align=\"left\"><a href=\"#\" class=\"info\">$titolo[$i]:<span>$descrizione[$i]</span></a></td><td><input type=\"password\" size=\"25\" name=\"$nomelabel[$i]\" value=\"$valore[$i]\"></td>";
        } else if ($nomelabel[$i] == "parkmoh") {
                echo "<td align=\"left\"><a href=\"#\" class=\"info\">$titolo[$i]:<span>$descrizione[$i]</span></a></td><td>&nbsp;&nbsp;<select name=\"$nomelabel[$i]\">";
                $tresults = getmusiccategory("/var/lib/asterisk/mohmp3");
                $default = (isset($valore[$i]) ? $valore[$i] : 'default');
                echo '<option value="default">'._("Default");
                if (isset($tresults)) {
                    foreach ($tresults as $tresult) {
                        $searchvalue="$tresult";
                        echo '<option value="'.$tresult.'" '.($searchvalue == $default ? 'SELECTED' : '').'>'.$tresult.'</option>\n';
                    }
                }
                echo '</select></td>';
        } else if ($nomelabel[$i] == "recbitrate") {
                    echo "<td align=\"left\"><a href=\"#\" class=\"info\">$titolo[$i]:<span>$descrizione[$i]</span></a></td><td>&nbsp;&nbsp;<select name=\"$nomelabel[$i]\">";
                    $tresults = array('32','48','64','96','112','128');
                    $default = (isset($valore[$i]) ? $valore[$i] : '96');
                    if (isset($tresults)) {
                        foreach ($tresults as $tresult) {
                            $searchvalue="$tresult";
                            echo '<option value="'.$tresult.'" '.($searchvalue == $default ? 'SELECTED' : '').'>'.$tresult.'</option>\n';
                        }
                    }
                echo '</select></td>';
        } else {
                        echo "<td align=\"left\"><a href=\"#\" class=\"info\">$titolo[$i]:<span>$descrizione[$i]</span></a></td><td><input type=\"text\" size=\"25\" name=\"$nomelabel[$i]\" value=\"$valore[$i]\"></td>";
        }
        echo "</tr>";
    }

?>
</table></p><br>
<h6><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>"></h6></form>

<script language="javascript">
<!--

function checkConf()
{
    var error = "<?php echo _('This field cannot be empty.'); ?>";
    var theForm = document.ctisettings;

    defaultEmptyOK = false;
    if (theForm.pass.value.length == 0)
        return warnInvalid(theForm.pass, error);
    if (theForm.host.value.length == 0)
        return warnInvalid(theForm.host, error);
    if (theForm.port.value.length == 0)
        return warnInvalid(theForm.port, error);
    if (theForm.user.value.length == 0)
        return warnInvalid(theForm.user, error);
    return true;
}

//-->
</script>