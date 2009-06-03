<?php /* $Id: general.php,v 1.14 2005/06/03 00:30:45 rcourtna Exp $ */
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

$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_extensions_from_mysql.pl';
$mohScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'gen_moh.pl';
$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$dispnum = 1;

if ($action == 'editglobals') {
    $globalfields = array(array($_REQUEST['RINGTIMER'],'RINGTIMER'),
                        array($_REQUEST['FAX_RX'],'FAX_RX'),
                        array($_REQUEST['FAX_RX_EMAIL'],'FAX_RX_EMAIL'),
                        array($_REQUEST['FAX_RX_EMAIL2'],'FAX_RX_EMAIL2'),
                        array($_REQUEST['FAX_RX_FROM'],'FAX_RX_FROM'),
                        array($_REQUEST['ZAP_PASSWORD'],'ZAP_PASSWORD'),
                        array($_REQUEST['CALLBACKEXT_PASSWORD'],'CALLBACKEXT_PASSWORD'),
                        array($_REQUEST['DIRECTORY'],'DIRECTORY'),
                        array($_REQUEST['VM_PREFIX'],'VM_PREFIX'),
                        array($_REQUEST['VM_DDTYPE'],'VM_DDTYPE'),
                        array($_REQUEST['VM_GAIN'],'VM_GAIN'),
                        array($_REQUEST['DIAL_OPTIONS'],'DIAL_OPTIONS'),
                        array($_REQUEST['DIAL_OPTIONS2'],'DIAL_OPTIONS2'),
                        array($_REQUEST['MOH_VOLUME'],'MOH_VOLUME'),
                        array($_REQUEST['CB_TRUNK'],'CB_TRUNK'),
                        array($_REQUEST['ALLOW_SIP_ANON'], 'ALLOW_SIP_ANON'),
                        array($_REQUEST['WAV2MP3'], 'WAV2MP3'),
                        array($_REQUEST['TRUNK_ALERT'], 'TRUNK_ALERT'),
                        array($_REQUEST['MONITOR_PASSWORD'], 'MONITOR_PASSWORD'),
                        array($_REQUEST['OPERATOR_XTN'], 'OPERATOR_XTN'),
                        array(isset($_REQUEST['DIRECTORY_OPTS']) ? $_REQUEST['DIRECTORY_OPTS'] : "",'DIRECTORY_OPTS'),
                        array(isset($_REQUEST['VM_OPTS']) ? $_REQUEST['VM_OPTS'] : "",'VM_OPTS'),
                        array($_REQUEST['DAYNIGHT_PASSWORD'], 'DAYNIGHT_PASSWORD'),
                        array($_REQUEST['TRUNKBUSY_ALERT'], 'TRUNKBUSY_ALERT')
                        );

    $compiled = $db->prepare('UPDATE globals SET value = ? WHERE variable = ?');

    $result = $db->executeMultiple($compiled,$globalfields);
    if(DB::IsError($result)) {
        echo $action.'<br>';
        die($result->getMessage());
    }

    exec($wScript);
    exec($mohScript);
    needreload();
}

$sql = "SELECT * FROM globals";
$globals = $db->getAll($sql);
if(DB::IsError($globals)) {
die($globals->getMessage());
}
foreach (gettrunks() as $temp) {
    $trunks[trim($temp[0])] = trim($temp[1]);
}

foreach ($globals as $global) {
    ${trim($global[0])} = $global[1];
}

$extens = getextens();

?>

<form name="general" action="config.php?mode=settings&amp;display=<?php echo urlencode($dispnum)?>" method="post">
<input type="hidden" name="display" value="<?php echo urlencode($dispnum)?>"/>
<input type="hidden" name="action" value="editglobals"/>
<h3><?php echo _("General Settings:")?></h3>

<table width="99%" border="0" cellpadding="1" cellspacing="2">

<h5><?php echo _("Dialing Options")?></h5>
<p>
    <?php echo _("Number of seconds to ring phones before sending callers to voicemail/destination:")?><input type="text" size="2" name="RINGTIMER" value="<?php  echo htmlspecialchars($RINGTIMER)?>"/>
    <br><br>
    <?php echo _("Extension prefix for dialing direct to voicemail:")?><input type="text" size="2" name="VM_PREFIX" value="<?php  echo htmlspecialchars($VM_PREFIX)?>"/>
    <br><br>
    <?php echo _("Direct Dial to Voicemail message type:")?>
    <select name="VM_DDTYPE">
    <option value=""><?php echo _("Default"); ?></option>
    <option value="u"<?php if ($VM_DDTYPE == "u") echo " SELECTED"; ?>><?php echo _("Unavailable"); ?></option>
    <option value="su"<?php if ($VM_DDTYPE == "su") echo " SELECTED"; ?>><?php echo _("Unavailable")."--"._("no instructions"); ?></option>
    <option value="b"<?php if ($VM_DDTYPE == "b") echo " SELECTED"; ?>><?php echo _("Busy"); ?></option>
    <option value="sb"<?php if ($VM_DDTYPE == "sb") echo " SELECTED"; ?>><?php echo _("Busy")."--"._("no instructions"); ?></option>
    <option value="s"<?php if ($VM_DDTYPE == "s") echo " SELECTED"; ?>><?php echo ("No Message"); ?></option>
    </select>
    <br><br>
    <a href=# class="info"><?php echo _("Use gain when recording the voicemail message (optional):")?><span>
    <?php echo _("Use the specified amount of gain when recording the voicemail message."); ?><br><br>
    <?php echo _("The units are whole-number decibels (dB)."); ?></span></a><input type="text" size="2" name="VM_GAIN" value="<?php  echo htmlspecialchars($VM_GAIN)?>"/>
    <br><br>
<a href=# class="info"><?php echo _("Dial Trunk Alert")?><span>
<?php echo _("This allow to enable a ALERT song 'sonar' if a dial Trunk fail.")?><br></span></a>:
    <select name="TRUNK_ALERT">
    <option value="silence"><?php echo _("no"); ?></option>
    <option <?php if ($TRUNK_ALERT == "sonar") echo "SELECTED "?>value="sonar"><?php echo _("yes"); ?></option>
    </select>
    <br><br>
<a href=# class="info"><?php echo _("Trunk Busy Message")?><span>
<?php echo _("This allow to enable a Message when all outbound channels are full or happens a unknown error.")?><br></span></a>:
    <select name="TRUNKBUSY_ALERT">
    <option value="disabled"><?php echo _("no"); ?></option>
    <option <?php if ($TRUNKBUSY_ALERT == "enabled") echo "SELECTED "?>value="enabled"><?php echo _("yes"); ?></option>
    </select>
    <br><br>
    <a href=# class="info"><?php echo _("Dial command options:")?><span>
t: Allow the called user to transfer the call by hitting #<br>
T: Allow the calling user to transfer the call by hitting #<br>
r: Generate a ringing tone for the calling party<br>
h: Allow the called to hang up by dialing *<br>
H: Allow the caller to hang up by dialing *<br>
C: Reset the CDR (Call Detail Record) for this call.<br>
m: Provide Music on Hold to the calling party until the called channel answers<br>
R: Indicate ringing to the calling party when the called party indicates ringing, pass no audio until answered.<br>
D(digits): After the called party answers, send digits as a DTMF stream<br>
A(x): Play an announcement (x.gsm) to the called party.<br>
S(n): Hangup the call n seconds AFTER called party picks up.<br>
w: Allow the called user to start recording after pressing *1<br>
W: Allow the calling user to start recording after pressing *1<br>
    </span></a>
    <input type="text" size="11" maxlength="11" name="DIAL_OPTIONS" value="<?php  echo htmlspecialchars($DIAL_OPTIONS)?>"/>
<br><br>
    <a href=# class="info"><?php echo _("Outbound Dial command options:")?><span>
t: Allow the called user to transfer the call by hitting #<br>
T: Allow the calling user to transfer the call by hitting #<br>
r: Generate a ringing tone for the calling party<br>
h: Allow the called to hang up by dialing *<br>
H: Allow the caller to hang up by dialing *<br>
C: Reset the CDR (Call Detail Record) for this call.<br>
m: Provide Music on Hold to the calling party until the called channel answers<br>
R: Indicate ringing to the calling party when the called party indicates ringing, pass no audio until answered.<br>
D(digits): After the called party answers, send digits as a DTMF stream<br>
A(x): Play an announcement (x.gsm) to the called party.<br>
S(n): Hangup the call n seconds AFTER called party picks up.<br>
w: Allow the called user to start recording after pressing *1<br>
W: Allow the calling user to start recording after pressing *1<br>
    </span></a>
    <input type="text" size="11" maxlength="11" name="DIAL_OPTIONS2" value="<?php  echo htmlspecialchars($DIAL_OPTIONS2)?>"/>
    <br><br>
    <input type="checkbox" value="s" name="VM_OPTS" <?php  echo ($VM_OPTS ? 'CHECKED' : '')?>> <a href=# class="info"><?php echo _("Do Not Play")?><span><?php echo _("Check this to remove the default message \"Please leave your message after the tone. When done, hang-up, or press the pound key.\" That is played after the voicemail greeting (the s option). This applies globally to all vm boxes.")?></span></a> <?php echo _("please leave message after tone to caller")?>
</p>
<h5><?php echo _("Company Directory")?></h5>
<p>
    <?php echo _("Find users in the")?> <a href=# class="info"><?php echo _("Company Directory")?><span>
    <?php echo _("Callers who are greeted by a Digital Receptionist can dial pound (#) to access the Company Directory.<br><br>Internal extensions can dial *411 to access the Company Directory.")?></span></a> <?php echo _("by:")?>
    <select name="DIRECTORY">
        <option value="first" <?php  echo ($DIRECTORY == 'first' ? 'SELECTED' : '')?>><?php echo _("first name")?>
        <option value="last" <?php  echo ($DIRECTORY == 'last' ? 'SELECTED' : '')?>><?php echo _("last name")?>
        <option value="disabled" <?php  echo ($DIRECTORY == 'disabled' ? 'SELECTED' : '')?>><?php echo _("directory disabled")?>
    </select>
    <br><br>
    <a href=# class="info"><?php echo _("Operator Extension:")?><span>
    <?php echo _("When users hit '0' in the directory, they are put through to this number. Note that it"); ?>
    <?php echo _(" does NOT need to be an extension, it can be a Ring Group, or even an external number."); ?></span></a>
    <input type="text" size="10" name="OPERATOR_XTN" value="<?php  echo htmlspecialchars($OPERATOR_XTN)?>"/>
    <br><br><input type="checkbox" onFocus="this.blur()" value="e" name="DIRECTORY_OPTS" <?php  echo ($DIRECTORY_OPTS ? 'CHECKED' : '')?>> <a href=# class="info"><?php echo _("Play extension number")?><span><?php echo _("Plays a message \"Please hold while I transfer you to extension xxx\" that lets the caller know what extension to use in the future.")?></span></a> <?php echo _("to caller before transferring call")?>
</p>
<h5><?php echo _("Fax Machine")?></h5>
<p>
    <?php echo _("Extension of")?> <a class="info" href="#"><?php echo _("fax machine")?><span><?php echo _("Select 'system' to have the system receive and email faxes.<br>Selecting 'disabled' will result in incoming calls being answered more quickly.")?></span></a> <?php echo _("for receiving faxes:")?>

    <select name="FAX_RX">
        <option value="disabled" <?php  echo ($FAX_RX == 'disabled' ? 'SELECTED' : '')?>><?php echo _("autosense disabled")?>
        <option value="system" <?php  echo ($FAX_RX == 'system' ? 'SELECTED' : '')?>><?php echo _("system")?>
<?php
    if (isset($extens)) {
        foreach ($extens as $exten) {
            $tech=strtoupper($exten[2]);
            if($tech == 'ZAP') {
                echo '<option value="'.$tech.'/${ZAPCHAN_'.$exten[0].'}" '.($FAX_RX == $tech.'/${ZAPCHAN_'.$exten[0].'}' ? 'SELECTED' : '').'>'._("Extension #").$exten[0];
            } else {
                echo '<option value="'.$tech.'/'.$exten[0].'" '.($FAX_RX == $tech.'/'.$exten[0] ? 'SELECTED' : '').'>'._("Extension #").$exten[0];
            }
        }
    }
?>
    </select>
</p>
<p>
    <a class="info" href="#"><?php echo _("Email address")?><span><?php echo _("Email address used if 'system' has been chosen for the fax extension above.")?></span></a> <?php echo _("to have faxes emailed to:")?>
    <input type="text" size="30" name="FAX_RX_EMAIL" value="<?php  echo htmlspecialchars($FAX_RX_EMAIL)?>"/><br><br>
    <?php echo _("Optional Email address (Carbon Copy):")?>
    <input type="text" size="30" name="FAX_RX_EMAIL2" value="<?php  echo htmlspecialchars($FAX_RX_EMAIL2)?>"/><br><br>
    <a class="info" href="#"><?php echo _("Email address")?><span><?php echo _("Email address that faxes appear to come from if 'system' has been chosen for the fax extension above.")?></span></a> <?php echo _("that faxes appear to come from:")?>
    <input type="text" size="30" name="FAX_RX_FROM" value="<?php  echo htmlspecialchars($FAX_RX_FROM)?>"/>
</p>
<h5><?php echo _("Application Password")?></h5>
<p>
    <a class="info" href="#"><?php echo _("Password")?><span><?php echo _("Leave this field blank to not prompt for password.")?></span></a> <?php echo _("for ZapBarge+/ZapScan/ChanSpy Extension (Only Numeric):")?>
    <input type="password" size="4" maxlength="4" name="ZAP_PASSWORD" value="<?php  echo $ZAP_PASSWORD?>"/><br><br>

    <a class="info" href="#"><?php echo _("Password")?><span><?php echo _("Leave this field blank to not prompt for password.")?></span></a> <?php echo _("for Enable/Disable Recording Monitor (*67/*68) (Only Numeric):")?>
    <input type="password" size="4" maxlength="4" name="MONITOR_PASSWORD" value="<?php  echo $MONITOR_PASSWORD?>"/><br><br>

    <a class="info" href="#"><?php echo _("Password")?><span><?php echo _("This field cannot be empty.")?></span></a> <?php echo _("for Callback on Demand (Only Numeric):")?>
    <input type="password" size="4" maxlength="4" name="CALLBACKEXT_PASSWORD" value="<?php  echo $CALLBACKEXT_PASSWORD?>"/><br><br>

    <a class="info" href="#"><?php echo _("Password")?><span><?php echo _("This field cannot be empty.")?></span></a> <?php echo _("for Day & Night Application (*60) (Only Numeric):")?>
    <input type="password" size="4" maxlength="4" name="DAYNIGHT_PASSWORD" value="<?php  echo $DAYNIGHT_PASSWORD?>"/><br>
</p>
<!--
<h5><?php echo _("Music On Hold Volume")?></h5>
<p>
    <?php echo _("MOH")?> <a class="info" href="#"><?php echo _("Attenuation")?><span><?php echo _("The lower the number (eg, as the negative number increases), the more attenuation. A standard value is -12.")?></span></a> <?php echo _("level for MP3s:")?>
    <input type="text" maxlength="3" size="3" name="MOH_VOLUME" value="<?php  echo $MOH_VOLUME?>"/>
</p>
-->
    <h5><?php echo _("CallBack Trunk")?></h5>
<p>
    <?php echo _("Trunk")?><a class="info" href="#"><span><?php echo _("The lower the number (eg, as the negative number increases), the more attenuation. This is only usable with madplay, if you're using mpg123 this option is ignored. A standard value is -12.")?></span></a>:
    <select id="" name="CB_TRUNK">
                <option value="" SELECTED></option>
                <?php
                foreach ($trunks as $name=>$display) {
                    echo "<option id=\"trunk".$key."\" value=\"".$name."\" ".($name == "$CB_TRUNK" ? "selected" : "").">".(strpos($display,'AMP:')===0 ? substr($display,4) : $display)."</option>";
                }
                ?>
    </select>
</p>
<h5><?php echo _("Security Settings")?></h5>
<p>
    <a href=# class="info"><?php echo _("Allow Anonymous Inbound SIP Calls?")?><span>
<?php echo _("** WARNING **")?><br><br>
<?php echo _("Setting this to 'yes' will potentially allow ANYBODY to call into your Asterisk server using the SIP protocol")?><br><br>
<?php echo _("It should only be used if you fully understand the impact of allowing anonymous calls into your server")?><br></span></a>:
    <select name="ALLOW_SIP_ANON">
    <option value="no"><?php echo _("no"); ?></option>
    <option <?php if ($ALLOW_SIP_ANON == "yes") echo "SELECTED "?>value="yes"><?php echo _("yes"); ?></option>
    </select>
</p>
<h5><?php echo _("Recording Monitor Settings")?></h5>
<p>
    <a href=# class="info"><?php echo _("General WAV to MP3 Conversion")?><span>
<?php echo _("This allow to encoding WAV file to MP3 for Recording Monitor. Wav to MP3 Encoding need some Memory and CPU.")?><br></span></a>:
    <select name="WAV2MP3">
    <option value="disabled"><?php echo _("no"); ?></option>
    <option <?php if ($WAV2MP3 == "enabled") echo "SELECTED "?>value="enabled"><?php echo _("yes"); ?></option>
    </select>
</p>
</table>
<br>
<h6><input name="Submit" type="button" value="<?php echo _("Submit Changes")?>" onclick="checkGeneral(general)"></h6>
</form>
