<?php /* $Id */
//Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
//Copyright (C) 2005-2006 SpheraIT
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

<script language="javascript">
<!--

function checkConf(theForm)
{
 defaultEmptyOK = false;
 if (!isInteger(theForm.account.value))
 return warnInvalid(theForm.account, "Please enter a valid Conference Number");

 if (!isAlphanumeric(theForm.name.value))
 return warnInvalid(theForm.name, "Please enter a valid Conference Name");

 // update $options
 var theOptionsFld = theForm.options;
 theOptionsFld.value = "";
 for (var i = 0; i < theForm.elements.length; i++)
 {
 var theEle = theForm.elements[i];
 var theEleName = theEle.name;
 if (theEleName.indexOf("#") > 1)
 {
 var arr = theEleName.split("#");
 if (arr[0] == "opt")
 theOptionsFld.value += theEle.value;
 }
 }

 // not possible to have a 'leader' conference with no adminpin
 if (theForm.options.value.indexOf("w") > -1 && theForm.adminpin.value == "")
 return warnInvalid(theForm.adminpin, "You must set an admin PIN for the Conference Leader when selecting the leader wait option");

 return true;
}

function deleteCheck(f2) {

    cancel = false;
    ok = true;

    if (confirm("Are you sure to delete this Conference?"))
        return ! cancel;
    else
        return ! ok;
}

-->
</script>

<?php

$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_extensions_from_mysql.pl';
$wScript1 = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_meetme_conf_from_mysql.pl';

isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action='';
//the extension we are currently displaying
isset($_REQUEST['extdisplay'])?$extdisplay=$_REQUEST['extdisplay']:$extdisplay='';
$dispnum = 22; //used for switch on config.php

    //if submitting form, update database
    switch ($action) {
        case "add":
            $options = ($_REQUEST['opt#w'].$_REQUEST['opt#q'].$_REQUEST['opt#c'].$_REQUEST['opt#i'].$_REQUEST['opt#M'].$_REQUEST['opt#s'].$_REQUEST['opt#p'].$_REQUEST['opt#x'].$_REQUEST['opt#o'].$_REQUEST['opt#F'].$_REQUEST['opt#r']);
            conferences_add($_REQUEST['account'],$_REQUEST['name'],$_REQUEST['userpin'],$_REQUEST['adminpin'],$_REQUEST['language'],$options);
            exec($wScript);
            exec($wScript1);
            needreload();
        break;
        case "delete":
            conferences_del($extdisplay);
            exec($wScript);
            exec($wScript1);
            needreload();
        break;
        case "edit":  //just delete and re-add
            conferences_del($_REQUEST['account']);
            $options = ($_REQUEST['opt#w'].$_REQUEST['opt#q'].$_REQUEST['opt#c'].$_REQUEST['opt#i'].$_REQUEST['opt#M'].$_REQUEST['opt#s'].$_REQUEST['opt#p'].$_REQUEST['opt#x'].$_REQUEST['opt#o'].$_REQUEST['opt#F'].$_REQUEST['opt#r']);
            conferences_add($_REQUEST['account'],$_REQUEST['name'],$_REQUEST['userpin'],$_REQUEST['adminpin'],$_REQUEST['language'],$options);
            exec($wScript);
            exec($wScript1);
            needreload();
        break;
    }

//get meetme rooms
//this function needs to be available to other modules (those that use goto destinations)
//therefore we put it in globalfunctions.php
$meetmes = conferences_list();
?>

</div>

<!-- right side menu -->
<div class="rnav" style="width:225px;">
    <li><a id="<?php echo ($extdisplay=='' ? 'current':'') ?>" href="config.php?mode=pbx&display=<?php echo $dispnum?>" onFocus="this.blur()"><?php echo _("Add Conference")?></a></li>
<?php
if (isset($meetmes)) {
    foreach ($meetmes as $meetme) {
        echo "<li><a id=\"".($extdisplay==$meetme[0] ? 'current':'')."\" href=\"config.php?mode=pbx&display=".$dispnum."&extdisplay={$meetme[0]}\" onFocus=\"this.blur()\">".(substr($meetme[1],0,22))." <{$meetme[0]}></a></li>";
    }
}
?>
</div>


<div class="content">
<?php
if ($action == 'delete') {
    echo '<br><h3>Conference '.$extdisplay.' deleted!</h3><br><br><br><br><br><br><br><br>';
} else {
    if ($extdisplay){
        //get details for this meetme
        $thisMeetme = conferences_get($extdisplay);
        //create variables
        extract($thisMeetme);
    }

    $delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=delete';
?>

<?php if ($extdisplay){ ?>
    <h3><?php echo _("Conference:")." ". $extdisplay; ?></h3>

    <p><a href="<?php echo $delURL ?>" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete Conference")?> <?php echo $extdisplay; ?></a></p>
<?php } else { ?>
    <h3><?php echo _("Add Conference:"); ?></h3>
<?php }
?>
    <form autocomplete="off" name="editMM" action="<?php $_SERVER['PHP_SELF'].'&mode=pbx' ?>" method="post" onsubmit="return checkConf(editMM);">
    <input type="hidden" name="display" value="<?php echo $dispnum?>">
    <input type="hidden" name="action" value="<?php echo ($extdisplay ? 'edit' : 'add') ?>">
    <table>
    <tr><td colspan="2"><h5><?php echo ($extdisplay ? _("Edit Conference") : _("Add Conference")) ?></h5></td></tr>
    <tr><td></td></tr><tr><td></td></tr>
    <tr>
<?php       if ($extdisplay){ ?>
        <input type="hidden" name="account" value="<?php echo $extdisplay; ?>">
<?php       } else { ?>
        <td><a href="#" class="info"><?php echo _("Conference number:")?><span><?php echo _("Use this number to dial into the conference.")?></span></a></td>
        <td><input size="10" type="text" name="account" value=""></td>
<?php       } ?>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Conference name:")?><span><?php echo _("Give this conference a brief name to help you identify it.")?></span></a></td>
        <td><input size="25" type="text" name="name" value="<?php echo (isset($description) ? $description : ''); ?>"></td>
    </tr>
    <tr>
    <td><a href="#" class="info"><?php echo _("User PIN:")?><span><?php echo _("You can require callers to enter a password before they can enter this conference.<br><br>This setting is optional.<br><br>If either PIN is entered, the user will be prompted to enter a PIN.")?></span></a></td>
    <td><input size="8" type="text" name="userpin" value="<?php echo (isset($userpin) ? $userpin : ''); ?>"></td>
    </tr>
    <tr>
    <td><a href="#" class="info"><?php echo _("Admin PIN:")?><span><?php echo _("Enter a PIN number for the admin user.<br><br>This setting is optional unless the 'leader wait' option is in use, then this PIN will identify the leader.")?></span></a></td>
    <td><input size="8" type="text" name="adminpin" value="<?php echo (isset($adminpin) ? $adminpin : ''); ?>"></td>
    </tr>
    <tr>
    <td><a href="#" class="info"><?php echo _("Language:")?><span><?php echo _("Choice your Language")?></span></a></td>
        <td>&nbsp;
            <select name="language">
            <?php
                echo '<option value="it"' . ($language == 'it' ? ' SELECTED' : '') . '>'._("Italian") . '</option>';
                echo '<option value="en"'. ($language == 'en' ? ' SELECTED' : '') . '>'._("English"). '</option>';
            ?>
            </select>
        </td>
    </tr>

    <?php
    $options = (isset($options) ? $options : "");
    ?>
    <input type="hidden" name="options" value="<?php echo $options; ?>">

    <tr><td colspan="2"><br><h5><?php echo _("Conference Options")?></h5></td></tr>
    <tr><td></td></tr><tr><td></td></tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Leader wait:")?><span><?php echo _("wait until the conference leader (admin user) arrives before starting the conference")?></span></a></td>
        <td>&nbsp;
            <select name="opt#w">
            <?php
                $optselect = strpos($options, "w");
                echo '<option value=""' . ($optselect === false ? ' SELECTED' : '') . '>'._("No") . '</option>';
                echo '<option value="w"'. ($optselect !== false ? ' SELECTED' : '') . '>'._("Yes"). '</option>';
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Quiet mode:")?><span><?php echo _("quiet mode (do not play enter/leave sounds)")?></span></a></td>
        <td>&nbsp;
            <select name="opt#q">
            <?php
                $optselect = strpos($options, "q");
                echo '<option value=""' . ($optselect === false ? ' SELECTED' : '') . '>'._("No") . '</option>';
                echo '<option value="q"'. ($optselect !== false ? ' SELECTED' : '') . '>'._("Yes"). '</option>';
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("User count:")?><span><?php echo _("announce user(s) count on joining conference")?></span></a></td>
        <td>&nbsp;
            <select name="opt#c">
            <?php
                $optselect = strpos($options, "c");
                echo '<option value=""' . ($optselect === false ? ' SELECTED' : '') . '>'._("No") . '</option>';
                echo '<option value="c"'. ($optselect !== false ? ' SELECTED' : '') . '>'._("Yes"). '</option>';
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("User join/leave:")?><span><?php echo _("Announce user join/leave with review")?></span></a></td>
        <td>&nbsp;
            <select name="opt#i">
            <?php
                $optselect = strpos($options, "i");
                echo '<option value=""' . ($optselect === false ? ' SELECTED' : '') . '>'._("No") . '</option>';
                echo '<option value="i"'. ($optselect !== false ? ' SELECTED' : '') . '>'._("Yes"). '</option>';
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Music on hold:")?><span><?php echo _("enable music on hold when the conference has a single caller")?></span></a></td>
        <td>&nbsp;
            <select name="opt#M">
            <?php
                $optselect = strpos($options, "M");
                echo '<option value=""' . ($optselect === false ? ' SELECTED' : '') . '>'._("No") . '</option>';
                echo '<option value="M"'. ($optselect !== false ? ' SELECTED' : '') . '>'._("Yes"). '</option>';
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Allow menu:")?><span><?php echo _("present menu (user or admin) when '*' is received ('send' to menu)")?></span></a></td>
        <td>&nbsp;
            <select name="opt#s">
            <?php
                $optselect = strpos($options, "s");
                echo '<option value=""' . ($optselect === false ? ' SELECTED' : '') . '>'._("No") . '</option>';
                echo '<option value="s"'. ($optselect !== false ? ' SELECTED' : '') . '>'._("Yes"). '</option>';
            ?>
            </select>
        </td>
    </tr>

    <tr>
        <td><a href="#" class="info"><?php echo _("Exit conference:")?><span><?php echo _("allow user to exit the conference by pressing '#'")?></span></a></td>
        <td>&nbsp;
            <select name="opt#p">
            <?php
                $optselect = strpos($options, "p");
                echo '<option value=""' . ($optselect === false ? ' SELECTED' : '') . '>'._("No") . '</option>';
                echo '<option value="p"'. ($optselect !== false ? ' SELECTED' : '') . '>'._("Yes"). '</option>';
            ?>
            </select>
        </td>
    </tr>

    <tr>
        <td><a href="#" class="info"><?php echo _("Close conference:")?><span><?php echo _("close the conference when last marked user exits")?></span></a></td>
        <td>&nbsp;
            <select name="opt#x">
            <?php
                $optselect = strpos($options, "x");
                echo '<option value=""' . ($optselect === false ? ' SELECTED' : '') . '>'._("No") . '</option>';
                echo '<option value="x"'. ($optselect !== false ? ' SELECTED' : '') . '>'._("Yes"). '</option>';
            ?>
            </select>
        </td>
    </tr>

    <tr>
        <td><a href="#" class="info"><?php echo _("Talker Optimization:")?><span><?php echo _("set talker optimization - treats talkers who aren't speaking as being muted, meaning (a) No encode is done on transmission and (b) Received audio that is not registered as talking is omitted causing no buildup in background noise.")?></span></a></td>
        <td>&nbsp;
            <select name="opt#o">
            <?php
                $optselect = strpos($options, "o");
                echo '<option value=""' . ($optselect === false ? ' SELECTED' : '') . '>'._("No") . '</option>';
                echo '<option value="o"'. ($optselect !== false ? ' SELECTED' : '') . '>'._("Yes"). '</option>';
            ?>
            </select>
        </td>
    </tr>

    <tr>
        <td><a href="#" class="info"><?php echo _("Pass DTMF:")?><span><?php echo _("Pass DTMF through the conference.")?></span></a></td>
        <td>&nbsp;
            <select name="opt#F">
            <?php
                $optselect = strpos($options, "F");
                echo '<option value=""' . ($optselect === false ? ' SELECTED' : '') . '>'._("No") . '</option>';
                echo '<option value="F"'. ($optselect !== false ? ' SELECTED' : '') . '>'._("Yes"). '</option>';
            ?>
            </select>
        </td>
    </tr>

    <tr>
        <td><a href="#" class="info"><?php echo _("Recording:")?><span><?php echo _("Recording the conference")?></span></a></td>
        <td>&nbsp;
            <select name="opt#r">
            <?php
                $optselect = strpos($options, "r");
                echo '<option value=""' . ($optselect === false ? ' SELECTED' : '') . '>'._("No") . '</option>';
                echo '<option value="r"'. ($optselect !== false ? ' SELECTED' : '') . '>'._("Yes"). '</option>';
            ?>
            </select>
        </td>
    </tr>

    <tr>
        <td colspan="2"><br><h6><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>"></h6></td>
    </tr>
    </table>
    </form>
<?php
}
?>
