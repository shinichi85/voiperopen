<?php 

//Copyright (C) 2005-2008 SpheraIT

?>

<script language="javascript">
<!--

function checkSpeeddial(theForm)
{
 defaultEmptyOK = false;
 if (!isInteger(theForm.speednr.value))
 return warnInvalid(theForm.speednr, "Please enter a valid SpeedDial Number");

 defaultEmptyOK = true;
 if (!isAlphanumeric(theForm.name.value))
 return warnInvalid(theForm.name, "Please enter a valid Name/Description");

 defaultEmptyOK = false;
 if (!isIntegerWithSpecialChar(theForm.telnr.value))
 return warnInvalid(theForm.telnr, "Please enter a valid Forward/Phone Number");

 return true;
}

function deleteCheck(f2) {

    cancel = false;
    ok = true;

    if (confirm("Are you sure to delete this SpeedDial Number?"))
        return ! cancel;
    else
        return ! ok;
}

-->
</script>

<?php

$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_speeddial_from_mysql.pl';

isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action='';
isset($_REQUEST['extdisplay'])?$extdisplay=$_REQUEST['extdisplay']:$extdisplay='';
$dispnum = 14; //used for switch on config.php
$skip = $_REQUEST['skip'];
$perpage = $_REQUEST['perpage'];
if ($skip == "") $skip = 0;
if ($perpage == "") $perpage = 30;
$skipped = 0;
$index = 0;
$pagerz = 0;

switch ($action) {
    case "add":
        $err_speeddial = speeddial_add($_REQUEST['speednr'],$_REQUEST['name'],$_REQUEST['telnr'],$_REQUEST['permission']);
		if ($err_speeddial != false) {
            exec($wScript);
            needreload();
		}
    break;
    case "delete":
        speeddial_del($extdisplay);
        exec($wScript);
        needreload();
    break;
    case "edit":
        speeddial_edit($_REQUEST['speednr'],$_REQUEST['name'],$_REQUEST['telnr'],$_REQUEST['permission']);
        exec($wScript);
        needreload();
    break;
}

$speeddials = speeddial_list();
?>

</div>

<!-- right side menu -->
<div class="rnav" style="width:225px;">
    <li><a id="<?php echo ($extdisplay=='' ? 'current':'') ?>" href="config.php?mode=pbx&display=<?php echo $dispnum?>&skip=<?php echo $skip?>" onFocus="this.blur()"><?php echo _("Add SpeedDial")?></a></li>
<?php
if (isset($speeddials)) {

        foreach ($speeddials AS $key=>$speeddial) {
            if ($index >= $perpage) {
                $shownext= 1;
				$pagerz=1;
                break;
                }
            if ($skipped<$skip && $skip!= 0) {
                $skipped= $skipped + 1;
				$pagerz=1;
                continue;
                }
            $index= $index + 1;

        if ($speeddial[2]) {
            echo "<li class=\"permission\"><a id=\"".($extdisplay==$speeddial[0] ? 'current':'')."\" title=\"$speeddial[1]\" href=\"config.php?mode=pbx&display=".urlencode($dispnum)."&extdisplay=".urlencode($speeddial[0])."&skip=$skip\" onFocus=\"this.blur()\">".(substr($speeddial[1],0,17))." <{$speeddial[0]}></a></li>";
        } else {
                echo "<li><a id=\"".($extdisplay==$speeddial[0] ? 'current':'')."\" title=\"$speeddial[1]\" href=\"config.php?mode=pbx&display=".urlencode($dispnum)."&extdisplay=".urlencode($speeddial[0])."&skip=$skip\" onFocus=\"this.blur()\">".(substr($speeddial[1],0,17))." <{$speeddial[0]}></a></li>";
        }
    }
}

if	($pagerz == 1){

    print "<li><center><div class='paging'>";
}

	if ($skip) {

	    $prevskip= $skip - $perpage;
	    if ($prevskip<0) $prevskip= 0;
	    $prevtag_pre= "<a onFocus='this.blur()' href='?mode=pbx&display=".$dispnum."&skip=$prevskip'>[PREVIOUS]</a>";
	    print "$prevtag_pre";
	    }
    	if (isset($shownext)) {

    	    $nextskip= $skip + $index;
    	    if ($prevtag_pre) $prevtag .= " | ";
    	    print "$prevtag <a onFocus='this.blur()' href='?mode=pbx&display=".$dispnum."&skip=$nextskip'>[NEXT]</a>";
    	    }

            print "</div></center></li>";

?>
</div>

<div class="content">
<?php
if ($action == 'delete') {
    echo '<br><h3>SpeedDial '.$extdisplay.' deleted!</h3><br><br><br><br><br><br><br><br>';
} else {
    if ($extdisplay){
        $thisSpedddial = speeddial_get($extdisplay);
        extract($thisSpedddial);
    }

    $delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=delete';
?>

<?php if ($extdisplay){ ?>
    <h3><?php echo _("SpeedDial:")." <". $extdisplay; ?>></h3>
    <p><a href="<?php echo $delURL ?>"  title="<?php echo $name ?> <<?php echo $speednr; ?>>" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete speeddial entry.")?></a></p>
<?php } else { ?>
    <h3><?php echo _("Add a new SpeedDial/Forward:"); ?></h3>
<?php }
?>
    <form autocomplete="off" name="speeddial" action="config.php?mode=pbx&display=14&skip=<?php echo $skip ?>&extdisplay=<?php echo $extdisplay ?>" method="post" onsubmit="return checkSpeeddial(speeddial);">
    <input type="hidden" name="display" value="<?php echo $dispnum?>">
    <input type="hidden" name="action" value="<?php echo ($extdisplay ? 'edit' : 'add') ?>">
    <input type="hidden" name="skip" value="<?php echo $skip ?>">
    <table>
    <tr><td colspan="2"><h5><?php echo ($extdisplay ? _("Edit SpeedDial") : _("Add SpeedDial")) ?></h5></td></tr>
    <tr><td></td></tr><tr><td></td></tr>
    <tr>
    <td><a href="#" class="info"><?php echo _("Extensions:")?><span><?php echo _("You can type any number here, but remember the speeddial number have a high priority on all pbx extensions.<br>Valid Range are from:<br>100 to 899<br>1000 to 8999<br>10000 to 89999")?></span></a></td>
    <td><input size="5" maxlength="5" type="text" name="speednr" value="<?php echo $speednr; ?>"></td>
    </tr>
    <tr>
    <td><a href="#" class="info"><?php echo _("Name/Description:")?><span><?php echo _("if set override the original CallerIDName.")?></span></a></td>
    <td><input size="30" type="text" name="name" value="<?php echo $name; ?>"></td>
    </tr>
    <tr>
    <td><a href="#" class="info"><?php echo _("Forward/Phone:")?><span><?php echo _("You can include an extension on a remote system,forward on a special extensions or an external number. Outbound Routing must contain a valid route for external numbers.")?></span></a></td>
    <td><input size="20" type="text" name="telnr" value="<?php echo $telnr; ?>"></td>
    </tr>
    <tr>
    <td><a href="#" class="info"><?php echo _("Permission:")?><span><?php echo _("if set override the Extension Call Permission.")?></span></a></td>
    <td><input type="checkbox" name="permission" value="CHECKED" <?php echo $permission; ?>></td>
    </tr>
    <tr>
    <td colspan="2"><br><h6><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>"></h6></td>
    </tr>
    </table>
    </form>
<?php
}
?>
