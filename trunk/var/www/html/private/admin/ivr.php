<?php /* $Id: ivr.php,v 1.17 2005/04/07 09:07:18 julianjm Exp $ */
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

function checkCidnum(f2) {

    defaultEmptyOK = false;
    if (!isInteger(f2.cidnum.value))
    return warnInvalid(f2.cidnum, "Please enter your user/extension number:");

    return true;
}

function checkLoop(f2) {

    defaultEmptyOK = false;
    if (!isInteger(f2.loopmenu.value))
    return warnInvalid(f2.loopmenu, "Please enter a valid loop number:");

    defaultEmptyOK = false;
    if (!isInteger(f2.ivr_num_options.value))
    return warnInvalid(f2.ivr_num_options, "Please enter a valid number of options:");

    return true;
}

function checkNameNote(f2) {

    defaultEmptyOK = false;
    if (!isAlphanumeric(f2.mname.value))
    return warnInvalid(f2.mname, "Please enter a valid ivr name:");

    defaultEmptyOK = false;
    if (!isAlphanumeric(f2.notes.value))
    return warnInvalid(f2.notes, "Please enter a valid ivr description:");

    return true;

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

</script>

<?php

isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action='';
isset($_REQUEST['menu_id'])?$menu_id = $_REQUEST['menu_id']:$menu_id='';

$dept = str_replace(' ','_',$_SESSION["user"]->_deptname);

$sql = "SELECT * FROM globals";
$globals = $db->getAll($sql);
    if(DB::IsError($globals)) {
        die($globals->getMessage());
    }

foreach ($globals as $global) {
        ${trim($global[0])} = $global[1];
    }

$dircontext = $_SESSION["user"]->_deptname;
if (empty($dircontext))
    $dircontext = 'default';

if (empty($menu_id)) $menu_id = $dept.'aa_1';

        $aalines = aainfo($menu_id);
        $optioncount = 0;
        $loopmenu = 1;
        $extlocal = "disabled";
        $speeddial = "disabled";
        $loopdestination = "hangup";

        foreach ($aalines as $aaline) {
            $extension = $aaline[1];
            $application = $aaline[3];
            $args = explode(',',$aaline[4]);
            $argslen = count($args);
            if (($application == 'Macro' && $args[0] == 'exten-vm') || ($application == 'Goto' && $args[0] == 'ext-local'))  {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Macro' && $args[0] == 'vm') {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && !(strpos($args[0],$dept.'aa_') === false)) {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && !(strpos($args[0],'ext-group') === false)) {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Background') {
                    $description = $aaline[5];
            }
            elseif ($application == 'GotoIf' && !(strpos($args[0],'LOOPED') === false)) {
                    $loopmenu = $aaline[5];

                    if ($application == 'GotoIf' && !(strpos($args[0],'ext-local') === false)) {
                        $loopdestination = 'ext-local,'.$args[1];
                    }

                    if ($application == 'GotoIf' && !(strpos($args[0],'ext-local') === false) && !(strpos($args[1],'VM_PREFIX') === false)) {
                        $loopdestination = 'vm,'.substr($args[1],12,10);
                    }

                    if ($application == 'GotoIf' && !(strpos($args[0],'ext-group') === false)) {
                        $loopdestination = 'ext-group,'.$args[1];
                    }

                    if ($application == 'GotoIf' && !(strpos($args[0],'ext-queues') === false)) {
                        $loopdestination = 'ext-queues,'.$args[1];
                    }

                    if ($application == 'GotoIf' && !(strpos($args[0],'ext-miscdests') === false)) {
                        $loopdestination = 'ext-miscdests,'.$args[1];
                    }

                    if ($application == 'GotoIf' && !(strpos($args[0],'outbound-allroutes') === false)) {
                        $loopdestination = 'outbound-allroutes,'.$args[1].',1';
                    }
                    if ($application == 'GotoIf' && !(strpos($args[0],'aa_') === false)) {
                        $loopdestination = $args[0];
                    }

            }
            elseif ($application == 'DigitTimeout') {
                    $mname = $aaline[5];
            }
            elseif ($application == 'Set' && $args[0] == 'TIMEOUT(digit)=3') {
                    $mname = $aaline[5];
            }
            elseif ($application == 'Set' && $args[0] == 'TIMEOUT(digit)=1') {
                    $mname = $aaline[5];
            }
            elseif ($application == 'Goto' && !(strpos($args[0],'custom') === false)) {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && !(strpos($args[0],'ext-queues') === false)) {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && $args[0] == 'native-fax') {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && $args[0] == 'from-pstn-timecheck') {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && $args[0] == 'from-pstn-timecheck_1') {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && $args[0] == 'from-pstn-timecheck_2') {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && $args[0] == 'from-pstn-timecheck_3') {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && $args[0] == 'from-pstn-timecheck_4') {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && $args[0] == 'from-pstn-timecheck_5') {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && $args[0] == 'outbound-allroutes') {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && $args[0] == 'callbackext') {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Goto' && !(strpos($args[0],'ext-meetme') === false)) {
                    $optioncount++;
                    $dropts[]= $extension;
            }
            elseif ($application == 'Set') {
                    $dircontext = ltrim('=',strstr('=',$args));
            }
            elseif ($extension == 'include' && $application == 'ext-local') {
                    $extlocal = "enabled";
            }
            elseif ($extension == 'include' && $application == 'custom-speeddial') {
                    $speeddial = "enabled";
            }
            }

switch($action) {
    default:
?>
<h4><?php echo _("Your Current Extension")?></h4>
<form name="prompt" action="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx' ?>" method="post" onsubmit="return checkCidnum(prompt);">
    <input type="hidden" name="action" value="ivr_start">
    <input type="hidden" name="menu_id" value="<?php echo $menu_id?>">
    <input type="hidden" name="ivr_action" value="<?php echo isset($_REQUEST['ivr_action'])?$_REQUEST['ivr_action']:''?>">
    <input type="hidden" name="display" value="2">
    <?php echo _("This Digital Receptionist wizard asks you to record and playback a greeting using your phone.")?><br><br>
    <?php echo _("Please enter your current extension number:")?>
    <input type="text" size="6" name="cidnum"><br>
    <h6><input name="Submit" type="submit" value="<?php echo _("Continue")?>"></h6><br><br><br><br><br><br>
</form>

<?php
    break;
    case 'ivr_start':
?>

<h4>Record Menu: <?php echo $mname?></h4>
<?php

    if (isset($_REQUEST['ivr_action']) && $_REQUEST['ivr_action'] == 'edit'){
        copy('/var/lib/asterisk/sounds/custom/'.$menu_id.'.wav','/var/lib/asterisk/sounds/'.$_REQUEST['cidnum'].'ivrrecording.wav');
        echo '<h5>'._("Dial *99 to listen to your current recording - click continue if you wish to re-use it.").'</h5>';
    }
?>
<h5><?php echo _("Step 1: Record")?></h5>
<p>
    <?php echo _("Using your phone,")?> <a href="#" class="info"><?php echo _("dial *77")?><span><?php echo _("Start speaking at the tone. Hangup when finished.")?></span></a> <?php echo _("and record the message you wish to greet callers with.")?>
</p>
<p>
    <form enctype="multipart/form-data" name="upload" action="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx' ?>" method="POST" onsubmit="return checkUpload(upload);"/>
        <?php echo _("Alternatively, upload a recording in")?> <a href="#" class="info"><?php echo _(".wav format")?><span><?php echo _("The .wav file _must_ have a sample rate of 8000Hz Mono")?></span></a>:<br><br>
        <input type="hidden" name="display" value="2">
        <input type="hidden" name="ivr_action" value="<?php echo isset($_REQUEST['ivr_action'])?$_REQUEST['ivr_action']:''?>">
        <input type="hidden" name="menu_id" value="<?php echo $menu_id?>">
        <input type="hidden" name="action" value="ivr_start">
        <input type="hidden" name="cidnum" value="<?php echo $_REQUEST['cidnum'];?>">
        <input type="file" size="30" name="ivrfile"/><input type="submit" name="Submit" value="Upload">
    </form>
<?php
if (isset($_FILES['ivrfile']['tmp_name']) && is_uploaded_file($_FILES['ivrfile']['tmp_name'])) {
    move_uploaded_file($_FILES['ivrfile']['tmp_name'], "/var/lib/asterisk/sounds/".$_REQUEST['cidnum']."ivrrecording.wav");
    echo "<h6>"._("Successfully uploaded")." ".$_FILES['ivrfile']['name']."</h6>";
}
?>
</p>
<form name="prompt" action="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx' ?>" method="post" onsubmit="return checkNameNote(prompt);">
<input type="hidden" name="action" value="ivr_recorded">
<input type="hidden" name="cidnum" value="<?php echo isset($_REQUEST['cidnum'])?$_REQUEST['cidnum']:'' ;?>">
<input type="hidden" name="menu_id" value="<?php echo $menu_id?>">
<input type="hidden" name="display" value="2">
<input type="hidden" name="ivr_action" value="<?php echo isset($_REQUEST['ivr_action'])?$_REQUEST['ivr_action']:'' ?>">
<h5><?php echo _("Step 2: Verify")?></h5>
<p>
    <?php echo _("After recording or uploading,")?> <em><?php echo _("dial *99")?></em> <?php echo _("to listen to your message.")?>
</p>
<p>
    <?php echo _("If you wish to re-record your message, dial *77 again.")?>
</p>
<h5><?php echo _("Step 3: Name & Describe")?></h5>
<table style="text-align:right;">
<tr valign="top">
    <td valign="top"><?php echo _("Name this menu:")?> </td>
    <td style="text-align:left"><input type="text" size="25" maxlength="20" name="mname" value="<?php echo $mname ?>"></td>
</tr>
<tr>
    <td valign="top"><?php echo _("Describe the menu:")?> </td>
    <td>&nbsp;&nbsp;<textarea name="notes" rows="3" cols="50"><?php echo $description ?></textarea></td>
</tr>
</table>
<h6><?php echo _("Click \"Continue\" when you are satisfied with your recording")?><input name="Submit" type="submit" value="<?php echo _("Continue")?>"></h6>

<h4><?php echo _("Consider including in your recording:")?></h4>
<p>
    <li>"<?php echo _("If you know the extension you are trying to reach, dial it now.")?>"
    <li>"<?php echo _("Dial # to access the company directory.")?>"
</p>
<p>
    <?php echo _("Example:  Thank you for calling. Please press 1 for our locations, 2 for hours of operation, or 0 to speak with a representative. If you know the extension of the party you are calling, dial it now.  To access the company directory, press pound now.")?>
</p>
</form>

<?php
    break;
    case 'ivr_recorded':
?>
<h4>Options for Menu: <?php echo $_REQUEST['mname']; ?></h4>
<form name="prompt" action="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx' ?>" method="post" onsubmit="return checkLoop(prompt);">
<input type="hidden" name="action" value="ivr_options_yes_num"/>
<input type="hidden" name="notes" value="<?php echo $_REQUEST['notes'];?>">
<input type="hidden" name="mname" value="<?php echo $_REQUEST['mname']; ?>">
<input type="hidden" name="cidnum" value="<?php echo $_REQUEST['cidnum'];?>">
<input type="hidden" name="menu_id" value="<?php echo $menu_id?>">
<input type="hidden" name="ivr_action" value="<?php echo $_REQUEST['ivr_action']?>">
<input type="hidden" name="display" value="2">
<p><?php echo _("Callers to this Menu can press the pound key (#) to access the user directory.")?><br><br>
<?php echo _("Directory context to be used:")?>
<select name="dir-context" <?php if ($DIRECTORY == 'disabled') { echo "disabled"; } ?>>
<?php
$uservm = getVoicemail();
$vmcontexts = array_keys($uservm);
echo 'ctx:'.$dircontext;
foreach ($vmcontexts as $vmcontext) {
    echo '<option value="'.$vmcontext.'" '.(strpos($dircontext,$vmcontext) === false ? '' : 'SELECTED').'>'.($vmcontext=='general' ? 'Entire Directory' : $vmcontext);
}
?>
</select>
</p>
<p>
<?php echo _("Aside from local extensions and the pound key (#), how many other options should callers be able to dial during the playback of this menu prompt?")?>
<br><br><?php echo _("Number of options for Menu:")?><b> <?php echo $_REQUEST['mname']; ?></b><input size="2" maxlength="2" type="text" name="ivr_num_options" value="<?php echo $optioncount ?>">
<br><br><?php echo _("How many times you want loop this IVR menu'?:")?><input size="2" maxlength="1" type="text" name="loopmenu" value="<?php echo $loopmenu ?>">
<br><br><?php echo _("Direct dial to Extension & Voicemail?:")?> <select name="extlocal-context">
<option value="enabled" <? if ($extlocal == 'enabled') { echo "selected"; } ?>>Enabled</option>
<option value="disabled" <? if ($extlocal == 'disabled') { echo "selected"; } ?>>Disabled</option>
</select>
<br><br><?php echo _("Direct dial to Speeddial/Forward & Misc Destinations?:")?> <select name="custom-speeddial">
<option value="enabled" <? if ($speeddial == 'enabled') { echo "selected"; } ?>>Enabled</option>
<option value="disabled" <? if ($speeddial == 'disabled') { echo "selected"; } ?>>Disabled</option>
</select>
<br><br><?php echo _("Loop Timeout goto into:")?> <select name="loopdestinationcontext" onchange="checkLoopDestination(prompt);">
<option value="enabled" <? if ($loopdestination != 'hangup') { echo "selected"; } ?>>Destination</option>
<option value="hangup" <? if ($loopdestination == 'hangup') { echo "selected"; } ?>>Hangup</option>
</select>
<br><br>
<?php
            echo ivrdrawselects('prompt',$loopdestination == 'hangup'?null:$loopdestination,999);
?>
</p>
<h6><input name="Submit" type="button" value="Continue" onclick="checkIVRGOTO(prompt);"></h6>
</form>

<?php
    break;
    case 'ivr_options_yes_num':

    if (( $_REQUEST['ivr_num_options'] == '0' ) || ( $_REQUEST['ivr_num_options'] == '' )) {
?>
    <form name="prompt" action="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx' ?>" method="post">
        <input type="hidden" name="display" value="2">
        <input type="hidden" name="action" value="ivr_options_set">
        <input type="hidden" name="notes" value="<?php echo $_REQUEST['notes'];?>">
        <input type="hidden" name="mname" value="<?php echo $_REQUEST['mname']; ?>">
        <input type="hidden" name="cidnum" value="<?php echo $_REQUEST['cidnum'];?>">
        <input type="hidden" name="menu_id" value="<?php echo $menu_id?>">
        <input type="hidden" name="ivr_action" value="<?php echo $_REQUEST['ivr_action']?>">
        <input type="hidden" name="dir-context" value="<?php echo $_REQUEST['dir-context'];?>">
        <input type="hidden" name="loopmenu" value="<?php echo $_REQUEST['loopmenu'];?>">
        <input type="hidden" name="extlocal-context" value="<?php echo $_REQUEST['extlocal-context'];?>">
        <input type="hidden" name="custom-speeddial" value="<?php echo $_REQUEST['custom-speeddial'];?>">
        <input type="hidden" name="loopdestinationcontext" value="<?php echo $_REQUEST['loopdestinationcontext'];?>">
        <input type="hidden" name="goto999" value="<?php echo $_REQUEST['goto999'];?>">
        <input type="hidden" name="ivr999" value="<?php echo $_REQUEST['ivr999'];?>">
        <input type="hidden" name="extension999" value="<?php echo $_REQUEST['extension999'];?>">
        <input type="hidden" name="voicemail999" value="<?php echo $_REQUEST['voicemail999'];?>">
        <input type="hidden" name="group999" value="<?php echo $_REQUEST['group999'];?>">
        <input type="hidden" name="queue999" value="<?php echo $_REQUEST['queue999'];?>">
        <input type="hidden" name="miscdest999" value="<?php echo $_REQUEST['miscdest999'];?>">
        <input type="hidden" name="dial_args999" value="<?php echo $_REQUEST['dial_args999'];?>">
        <center><input name="Submit" type="submit" value="<?php echo _("Finished!  Click to save your changes.")?>"></center>
    </form>

<?php
    } else {
        $unique_aas = getaas();

        $extens = getextens();

        $gresults = getgroups();
?>
<h4><?php echo _("Options for Menu:")." ".$_REQUEST['mname']; ?></h4>
<p>
    <?php echo _("Define the various options you expect your callers to dial after/during the playback of this recorded menu.")?>
</p>
<p>
    "<b><?php echo _("Dialed Option #")?></b>" <?php echo _("is the number you expect the caller to dial.")?><br>
    "<b><?php echo _("Action")?></b>" <?php echo _("is the result of the caller dialing the option #.  This can send the caller to an internal extension, a voicemail box, ring group, queue, or to another recorded menu.")?>
</p>
<h5></h5>
<p>    <form name="prompt" action="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx' ?>" method="post">
    <input type="hidden" name="display" value="2">
    <input type="hidden" name="action" value="ivr_options_set"/>
    <input type="hidden" name="notes" value="<?php echo $_REQUEST['notes'];?>">
    <input type="hidden" name="mname" value="<?php echo $_REQUEST['mname']; ?>">
    <input type="hidden" name="ivr_num_options" value="<?php echo $_REQUEST['ivr_num_options'] ?>">
    <input type="hidden" name="cidnum" value="<?php echo $_REQUEST['cidnum'];?>">
    <input type="hidden" name="menu_id" value="<?php echo $menu_id?>">
    <input type="hidden" name="ivr_action" value="<?php echo $_REQUEST['ivr_action']?>">
    <input type="hidden" name="dir-context" value="<?php echo $_REQUEST['dir-context'];?>">
    <input type="hidden" name="extlocal-context" value="<?php echo $_REQUEST['extlocal-context'];?>">
    <input type="hidden" name="custom-speeddial" value="<?php echo $_REQUEST['custom-speeddial'];?>">
    <input type="hidden" name="loopdestinationcontext" value="<?php echo $_REQUEST['loopdestinationcontext'];?>">
    <input type="hidden" name="goto999" value="<?php echo $_REQUEST['goto999'];?>">
    <input type="hidden" name="ivr999" value="<?php echo $_REQUEST['ivr999'];?>">
    <input type="hidden" name="extension999" value="<?php echo $_REQUEST['extension999'];?>">
    <input type="hidden" name="voicemail999" value="<?php echo $_REQUEST['voicemail999'];?>">
    <input type="hidden" name="group999" value="<?php echo $_REQUEST['group999'];?>">
    <input type="hidden" name="queue999" value="<?php echo $_REQUEST['queue999'];?>">
    <input type="hidden" name="miscdest999" value="<?php echo $_REQUEST['miscdest999'];?>">
    <input type="hidden" name="dial_args999" value="<?php echo $_REQUEST['dial_args999'];?>">
    <input type="hidden" name="loopmenu" value="<?php echo $_REQUEST['loopmenu'];?>">
    <table>
    <tr>
        <td><h4><?php echo _("Dialed Option #")?></h4></td>
        <td width="40px">&nbsp;</td>
        <td><h4><?php echo _("Action")?></h4></td>
    </tr>
<?php
    for ($i = 0; $i < $_REQUEST['ivr_num_options']; $i++) {
?>
    <tr>
        <td style="text-align:right;">
        <input size="2" type="text" name="ivr_option<?php echo $i ?>" value="<?php echo ($dropts[$i]=='')?$i+1:$dropts[$i] ?>">
        </td>
        <td></td>
        <td>

<?php

            $sql = "SELECT args FROM extensions WHERE extension = '".$dropts[$i]."' AND priority = '1' AND context = '".$menu_id."'";
            list($goto) = $db->getRow($sql);
            echo drawselects('prompt',$goto,$i,'fixINCOMING','fixFAX','','fixCALLBACKEXT','fixMEETME');

?>
        </td>
    </tr>

    <tr><td><br></td></tr>
<?php
    }
?>
    </table>
    <h6>
    <input type="button" value="<?php echo _("SAVE")?>" onClick="checkIVR(prompt,<?php echo $_REQUEST['ivr_num_options']?>)"
    </h6>
    </form>
</p>
<?php

    }
?>

<?php
    break;
    case 'ivr_options_set':

    if ($_REQUEST['ivr_action'] == 'edit') {
        $_REQUEST['ivr_action'] = 'delete';
        $_REQUEST['map_display'] = 'no';
        include 'ivr_action.php';
    }

    $_REQUEST['map_display'] = 'yes';
    $_REQUEST['ivr_action'] = 'write';
    include 'ivr_action.php';
    }
?>
