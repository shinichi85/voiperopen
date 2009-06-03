<script language="Javascript">

    function checkaddsipauto(theForm) {

    defaultEmptyOK = false;
    var start_fix = theForm.account_start.value;
    var account_start_fix = parseInt(start_fix);
    if (!isInteger(theForm.account_start.value) || (account_start_fix < 100) || (account_start_fix > 89999))
    return warnInvalid(theForm.account_start, "There is something wrong with your Start Extension Number. Valid Range are from: 100 to 899 - 1000 to 8999 - 10000 to 89999.");

    defaultEmptyOK = false;
    var start_fix = theForm.account_start.value;
    var end_fix = theForm.account_end.value;
    var account_start_fix = parseInt(start_fix);
    var account_end_fix = parseInt(end_fix);
    if (!isInteger(theForm.account_end.value) || (account_end_fix < account_start_fix) || (account_end_fix > 89999))
    return warnInvalid(theForm.account_end, "There is something wrong with your End Extension Number. Valid Range are from: 100 to 899 - 1000 to 8999 - 10000 to 89999.");

    defaultEmptyOK = false;
    if (!isInteger(theForm.port.value))
    return warnInvalid(theForm.port, "Please enter a valid sip Port. Example: 5060");

    defaultEmptyOK = true;
    if (!isInteger(theForm.directdid.value))
    return warnInvalid(theForm.directdid, "Please enter a valid DirectDID. Example: 8001234");

    defaultEmptyOK = true;
    if ((theForm.pickupgroup.value != "") && (!theForm.pickupgroup.value.match('^[0-9\-\,]+$')))
    return warnInvalid(theForm.pickupgroup, "Please enter a valid Pickupgroup. Example: 1");

    defaultEmptyOK = true;
    if ((theForm.callgroup.value != "") && (!theForm.callgroup.value.match('^[0-9\-\,]+$')))
    return warnInvalid(theForm.callgroup, "Please enter a valid Callgroup. Example: 1");

    defaultEmptyOK = true;
    if (!isInteger(theForm.qualify.value))
    return warnInvalid(theForm.qualify, "Please enter a valid Qualify Time. Example: 500");

    defaultEmptyOK = true;
    if (!isAlphanumeric(theForm.accountcode.value))
    return warnInvalid(theForm.accountcode, "Please enter a valid Accountcode. Example: lss0101");

    defaultEmptyOK = true;
    if (!isAlphanumeric(theForm.subscribecontext.value))
    return warnInvalid(theForm.subscribecontext, "Please enter a valid Subscribe Context. Example: ext-local");

    defaultEmptyOK = true;
    if (!isAlphanumeric(theForm.context.value))
    return warnInvalid(theForm.context, "Please enter a valid Context. Example: from-internal");

    defaultEmptyOK = false;
    if (!isAlphanumeric(theForm.secret.value))
    return warnInvalid(theForm.secret, "Please enter a valid Password.");

    defaultEmptyOK = true;
    if (!isCallerID(theForm.outcidname.value))
    return warnInvalid(theForm.outcidname, "Please enter a valid CallerID Name.");

    defaultEmptyOK = true;
    if (!isInteger(theForm.outcidnum.value))
    return warnInvalid(theForm.outcidnum, "Please enter a valid CallerID Num.");

    defaultEmptyOK = true;
    if (!isInteger(theForm.cidnuminc.value))
    return warnInvalid(theForm.cidnuminc, "Please enter a valid GNR CallerID Num.");

    defaultEmptyOK = false;
    if (!isInteger(theForm.calllimit.value))
    return warnInvalid(theForm.calllimit, "Please enter a valid number for Call Limit.");

        defaultEmptyOK = true;
        if ((theForm.outcidnum.value != "") && (theForm.outcidname.value == ""))
        return warnInvalid(theForm.outcidname, "Please enter a valid CallerID Name.");

        defaultEmptyOK = true;
        if ((theForm.outcidname.value != "") && (theForm.outcidnum.value == ""))
        return warnInvalid(theForm.outcidnum, "Please enter a valid CallerID Num.");

    defaultEmptyOK = true;
    if ((theForm.allowcall.value != "") && (!theForm.allowcall.value.match('^[0-9;]+$')))
    return warnInvalid(theForm.allowcall, "Call Allow can only contain numbers and a special separator character.");
    if ((theForm.nocall.value != "") && (!theForm.nocall.value.match('^[0-9;]+$')))
    return warnInvalid(theForm.nocall, "Call Disallow can only contain numbers and a special separator character.");


            theForm.submit();
    }

    function checkupdatesipauto(theForm) {

    defaultEmptyOK = false;
    var start_fix = theForm.account_start.value;
    var account_start_fix = parseInt(start_fix);
    if (!isInteger(theForm.account_start.value) || (account_start_fix < 100) || (account_start_fix > 89999))
    return warnInvalid(theForm.account_start, "There is something wrong with your Start Extension Number. Valid Range are from: 100 to 899 - 1000 to 8999 - 10000 to 89999.");

    defaultEmptyOK = false;
    var start_fix = theForm.account_start.value;
    var end_fix = theForm.account_end.value;
    var account_start_fix = parseInt(start_fix);
    var account_end_fix = parseInt(end_fix);
    if (!isInteger(theForm.account_end.value) || (account_end_fix < account_start_fix) || (account_end_fix > 89999))
    return warnInvalid(theForm.account_end, "There is something wrong with your End Extension Number. Valid Range are from: 100 to 899 - 1000 to 8999 - 10000 to 89999.");

    defaultEmptyOK = true;
    if (!isInteger(theForm.port.value))
    return warnInvalid(theForm.port, "Please enter a valid sip Port. Example: 5060");

    defaultEmptyOK = true;
    if ((theForm.pickupgroup.value != "") && (!theForm.pickupgroup.value.match('^[0-9\-\,]+$')))
    return warnInvalid(theForm.pickupgroup, "Please enter a valid Pickupgroup. Example: 1");

    defaultEmptyOK = true;
    if ((theForm.callgroup.value != "") && (!theForm.callgroup.value.match('^[0-9\-\,]+$')))
    return warnInvalid(theForm.callgroup, "Please enter a valid Callgroup. Example: 1");

    defaultEmptyOK = true;
    if (!isInteger(theForm.qualify.value))
    return warnInvalid(theForm.qualify, "Please enter a valid Qualify Time. Example: 500");

    defaultEmptyOK = true;
    if (!isAlphanumeric(theForm.accountcode.value))
    return warnInvalid(theForm.accountcode, "Please enter a valid Accountcode. Example: lss0101");

    defaultEmptyOK = true;
    if (!isAlphanumeric(theForm.subscribecontext.value))
    return warnInvalid(theForm.subscribecontext, "Please enter a valid Subscribe Context. Example: ext-local");

    defaultEmptyOK = true;
    if (!isAlphanumeric(theForm.context.value))
    return warnInvalid(theForm.context, "Please enter a valid Context. Example: from-internal");

    defaultEmptyOK = true;
    if (!isAlphanumeric(theForm.secret.value))
    return warnInvalid(theForm.secret, "Please enter a valid Password.");

    defaultEmptyOK = true;
    if (!isCallerID(theForm.outcidname.value))
    return warnInvalid(theForm.outcidname, "Please enter a valid CallerID Name.");

    defaultEmptyOK = true;
    if (!isInteger(theForm.outcidnum.value))
    return warnInvalid(theForm.outcidnum, "Please enter a valid CallerID Num.");

    defaultEmptyOK = true;
    if (!isInteger(theForm.cidnuminc.value))
    return warnInvalid(theForm.cidnuminc, "Please enter a valid GNR CallerID Num.");

        defaultEmptyOK = true;
        if ((theForm.outcidnum.value != "") && (theForm.outcidname.value == ""))
        return warnInvalid(theForm.outcidname, "You have update your CallerID Num, please enter a valid CallerID Name.");

        defaultEmptyOK = true;
        if ((theForm.outcidname.value != "") && (theForm.outcidnum.value == ""))
        return warnInvalid(theForm.outcidnum, "You have update your CallerID Name, please enter a valid CallerID Num.");

    defaultEmptyOK = true;
    if ((theForm.allowcall.value != "") && (!theForm.allowcall.value.match('^[0-9;]+$')))
    return warnInvalid(theForm.allowcall, "Call Allow can only contain numbers and a special separator character.");
    if ((theForm.nocall.value != "") && (!theForm.nocall.value.match('^[0-9;]+$')))
    return warnInvalid(theForm.nocall, "Call Disallow can only contain numbers and a special separator character.");


            theForm.submit();
    }


    function checkdeletesipauto(theForm) {

    cancel = false;
    ok = true;

    defaultEmptyOK = false;
    var start_fix = theForm.account_start.value;
    var account_start_fix = parseInt(start_fix);
    if (!isInteger(theForm.account_start.value) || (account_start_fix < 100) || (account_start_fix > 89999))
    return warnInvalid(theForm.account_start, "There is something wrong with your Start Extension Number. Valid Range are from: 100 to 899 - 1000 to 8999 - 10000 to 89999.");

    defaultEmptyOK = false;
    var start_fix = theForm.account_start.value;
    var end_fix = theForm.account_end.value;
    var account_start_fix = parseInt(start_fix);
    var account_end_fix = parseInt(end_fix);
    if (!isInteger(theForm.account_end.value) || (account_end_fix < account_start_fix) || (account_end_fix > 89999))
    return warnInvalid(theForm.account_end, "There is something wrong with your End Extension Number. Valid Range are from: 100 to 899 - 1000 to 8999 - 10000 to 89999.");

        if (confirm("Are you sure to delete this Range of Extensions?")) {
            theForm.submit();
        } else {
            return ! ok;
        }
    }


</script>

<?php

$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_sip_conf_from_mysql.pl';
$wScript1 = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_extensions_from_mysql.pl';
$wOpScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_op_conf_from_mysql.pl';

$dispnum = 21;

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$account_start = isset($_REQUEST['account_start'])?$_REQUEST['account_start']:'';
$account_end = isset($_REQUEST['account_end'])?$_REQUEST['account_end']:'';
$autogensip = $_REQUEST['autogensip'];

    if ($action == 'addsip') {

        $errsipauto = addsipauto($account_start,$account_end);

        if ($errsipauto != false) {

            exec($wScript);
            exec($wOpScript);
            exec($wScript1);

            needreload();

        }
    }

    if ($action == 'deletesip') {

        $errdeletesipauto = deletesipauto($account_start,$account_end);

        if ($errdeletesipauto != false) {

            exec($wScript);
            exec($wOpScript);
            exec($wScript1);

            needreload();

        }

    }

    if ($action == 'updatesip') {

        $errupdatesipauto = updatesipauto($account_start,$account_end);

        if ($errupdatesipauto != false) {

            exec($wScript);
            exec($wOpScript);
            exec($wScript1);

            needreload();

        }
    }



?>
<h3><?php echo _("SIP Extensions Generator:")?></h3>

<table width="99%" bgcolor="#EEEEEE" border="0" cellpadding="3" cellspacing="1">
<form name="deletesipauto" action="config.php?mode=pbx&display=<?php echo urlencode($dispnum)?>" method="post">
<input type="hidden" name="display" value="<?php echo $dispnum?>"/>
<input type="hidden" name="action" value="deletesip"/>

    <?php
        echo '<tr>';
        echo '<td width="180" align="left"><a href="#" class="info">DELETE SIP Extensions<span>Warning: This Option delete all SIP extensions in your Range. Valid Range are from:<br>100 to 899, 1000 to 8999, 10000 to 89999.</span></a>:</td><td><input type="text" size="6" name="account_start" value=""><b> to</b><input type="text" size="6" name="account_end" value="">';
        echo '</td><td align="right"><input name="Submit" type="button" value="Submit Changes" onclick="checkdeletesipauto(deletesipauto)"></td>';
        echo '</tr>';
    ?>
</form>
</table>
<br>
<table width="99%" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0">
    <?php
        echo '<tr>';
        echo '<td><div align="center"><a onFocus="this.blur()" href="config.php?mode=pbx&autogensip=addsip&display='.urlencode($dispnum).'">Add Sip Extensions</a> - <a onFocus="this.blur()" href="config.php?mode=pbx&autogensip=updatesip&display='.urlencode($dispnum).'">Update SIP Extensions</a></div></td>';
        echo '</tr>';
    ?>
</table>
<br>

<?php

    if ($autogensip == "addsip" || $autogensip == "") {

?>

<table width="99%" bgcolor="#DDDDDD" border="0" cellpadding="3" cellspacing="1">
<form name="addsipauto" action="config.php?mode=pbx&display=<?php echo urlencode($dispnum)?>" method="post">
<input type="hidden" name="display" value="<?php echo $dispnum?>"/>
<input type="hidden" name="action" value="addsip"/>
<input type="hidden" name="mailbox" value=""/>

    <?php
        echo '<tr>';
        echo '<td width="180" align="left"><a href="#" class="info">ADD SIP Extensions<span>This Option add SIP extensions in your Range. Valid Range are from:<br>100 to 899, 1000 to 8999, 10000 to 89999.</span></a>:</td><td><input type="text" size="6" name="account_start" value=""><b> to</b><input type="text" size="6" name="account_end" value="">';
        echo '</td>';
        echo '</tr>';

        echo '<tr><td colspan="2"><h5>Account Settings:</h5></td></tr>';

        echo '<tr>';
        echo '<td align="left"><a href="#" class="info">'._("Outbound CallerIDName").'<span>'._("Overrides the caller id when dialing out a trunk. Any setting here will override the common outbound caller id set in the Trunks admin.<br>Format: <b>caller name</b>").'</span></a>:</td><td><input type="text" size="25" name="outcidname" value="">';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left"><a href="#" class="info">'._("Outbound CallerIDNum").'<span>'._("Overrides the caller id num when dialing out a trunk. Any setting here will override the common outbound caller id set in the Trunks admin.").'</span></a>:</td><td><input type="text" size="20" name="outcidnum" value="">';
        echo '<input size="4" maxlength="4" name="cidnuminc" type="text" value=""> (n+1) </td>';
        echo '</tr>';

        echo "<tr>";
        echo "<td align=\"left\">Direct DID:</td><td><input type=\"text\" size=\"15\" name=\"directdid\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Extension Password:</td><td><input type=\"password\" size=\"15\" name=\"secret\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Accountcode:</td><td><input type=\"text\" size=\"25\" maxlength=\"9\" name=\"accountcode\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Default Codec (disallow):</td><td><input type=\"text\" size=\"35\" name=\"disallow\" value=\"all\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Default Codec (allow):</td><td><input type=\"text\" size=\"35\" name=\"allow\" value=\"alaw&ulaw&gsm&g729&ilbc&g726\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo '<td align="left"><a href="#" class="info">'._("Call (Disallow)").'<span>'._("
<strong>0</strong> for disable all local prefix.<br>
<strong>1-8</strong> for disable prefix with special rate.<br>
<strong>3</strong> for disable prefix of mobile phone.<br>
<strong>4</strong> for disable prefix with special rate.<br>
<strong>7</strong> for disable internet ISP calls.<br><br>
Numbers <strong>2</strong> - <strong>5</strong> - <strong>6</strong> - <strong>9</strong> they are reserved for future requirements.<br><br>
Example: <strong>0;3;7</strong>. Example:<strong>040;347;800</strong>.<br>").'</span></a>:</td>';
        echo '<td><input type="text" size="30" name="nocall" value=""></td>';
        echo "</tr>";

        echo "<tr>";
        echo '<tr><td align="left"><a href="#" class="info">'._("Call (Allow)").'<span>'._("
<strong>0</strong> for enabled all local prefix.<br>
<strong>1-8</strong> for enabled prefix with special rate.<br>
<strong>3</strong> for enabled prefix of mobile phone.<br>
<strong>4</strong> for enabled prefix with special rate.<br>
<strong>7</strong> for enabled internet ISP calls.<br><br>
Numbers <strong>2</strong> - <strong>5</strong> - <strong>6</strong> - <strong>9</strong> they are reserved for future requirements.<br><br>
Example: <strong>0;3;7</strong>. Example:<strong>040;347;800</strong>.<br>").'</span></a>:</td>';
        echo '<td><input type="text" size="30" name="allowcall" value=""></td>';
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Full Name:</td><td><input type=\"text\" size=\"30\" name=\"name\" value=\"Your Name here\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Callgroup:</td><td><input type=\"text\" size=\"15\" name=\"callgroup\" value=\"1\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">PickupGroup:</td><td><input type=\"text\" size=\"15\" name=\"pickupgroup\" value=\"1\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">CanReinvite:</td><td>&nbsp;&nbsp;<select name=\"canreinvite\" size=\"1\"><option value=\"no\">No</option><option value=\"yes\">Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Context:</td><td><input type=\"text\" size=\"30\" name=\"context\" value=\"from-internal\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">DTMF Mode:</td><td>&nbsp;&nbsp;<select name=\"dtmfmode\" size=\"1\"><option value=\"rfc2833\">rfc2833</option><option value=\"inband\">inband</option><option value=\"info\">info</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">NAT:</td><td>&nbsp;&nbsp;<select name=\"nat\" size=\"1\"><option value=\"no\">No</option><option value=\"yes\">Yes</option><option value=\"never\">Never</option><option value=\"route\">Route</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Sip Port:</td><td><input type=\"text\" size=\"5\" name=\"port\" value=\"5060\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo '<td align="left"><a href="#" class="info">'._("Qualify").'<span>'._("If you turn on qualify, Asterisk will send a SIP OPTIONS command regularly to check that the device is still online. If you leave empty Qualify is Disabled.").'</span></a>:</td><td><input type="text" size="5" id="qualify" name="qualify" value="500"> <b>MilliSeconds</b></td>';
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">SubscribeContext:</td><td><input type=\"text\" size=\"30\" name=\"subscribecontext\" value=\"ext-local\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Type:</td><td>&nbsp;&nbsp;<select name=\"type\" size=\"1\" onchange=\"checkQualify(addsipauto);\"><option value=\"friend\">Friend</option><option value=\"user\">User</option><option value=\"peer\">Peer</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Allow Subscribe:</td><td>&nbsp;&nbsp;<select name=\"allowsubscribe\" size=\"1\"><option value=\"no\">No</option><option value=\"yes\" selected>Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Call Limit:</td><td><input type=\"text\" size=\"2\" name=\"calllimit\" value=\"99\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Video Support:</td><td>&nbsp;&nbsp;<select name=\"videosupport\" size=\"1\"><option value=\"no\">No</option><option value=\"yes\">Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">T.38 Passthrough:</td><td>&nbsp;&nbsp;<select name=\"t38pt_udptl\" size=\"1\"><option value=\"no\">No</option><option value=\"yes\">Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Language:</td><td>&nbsp;&nbsp;<select name=\"language\" size=\"1\"><option value=\"en\">English</option><option value=\"it\" selected>Italian</option></select></td>";
        echo "</tr>";

        echo '<tr><td colspan="2"><h5>Extra Features:</h5></td></tr>';

        echo "<tr>";
        echo "<td align=\"left\">Recall on Busy:</td><td>&nbsp;&nbsp;<select name=\"rob\" size=\"1\"><option value=\"Never\">No</option><option value=\"Always\">Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Call Waiting:</td><td>&nbsp;&nbsp;<select name=\"cw\" size=\"1\"><option value=\"Never\">No</option><option value=\"Always\" selected>Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\"><a href=\"#\" class=\"info\">Record Incoming Calls<span>Record ALL INBOUND CALLS received at this extension.</span></a>:</td><td>&nbsp;&nbsp;<select name=\"record_in\" size=\"1\"><option value=\"Never\">No</option><option value=\"Always\">Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\"><a href=\"#\" class=\"info\">Record Ougoing Calls<span>Record ALL OUTBOUND CALLS received at this extension.</span></a>:</td><td>&nbsp;&nbsp;<select name=\"record_out\" size=\"1\"><option value=\"Never\">No</option><option value=\"Always\">Yes</option></select></td>";
        echo "</tr>";

        echo '<tr>';
        echo '<td align="left"><a href="#" class="info">'._("Ring Time").'<span>'._("Number of seconds to ring phones before sending callers to Voicemail/Destination.<br>If default is set, the General Settings value are used!").'<br></span></a>:</td>';
        echo '<td>&nbsp;&nbsp;<select id="ringtime" name="ringtime" disabled>';

                $default = (isset($ringtime) ? $ringtime : 0);
                for ($i=0; $i <= 60; $i+=5) {
                    if ($i == 0)
                            echo '<option value="">'._("Default").'</option>';
                                else
                                echo '<option value="'.$i.'" '.($i == $ringtime ? 'SELECTED' : '').'>'.$i.' Seconds</option>\n';
                }

        echo '</select></td>';
        echo '</tr>';

        echo '<tr><td colspan="2"><h5>Failover Destination:</h5></td></tr>';

        echo '<tr><td align="left">';
        echo 'Destination type:</td><td>&nbsp;&nbsp;';
        echo '<select name="vm" onchange="checkAddSipAuto(addsipauto);">';
        echo '<option value="disabled" selected>Ringing Forever</option>';
        echo '<option value="destination">Destination</option>';
        echo '</select>';
        echo '</td></tr>';

        echo '<tr><td colspan="2">';

        echo autodrawselects('addsipauto',isset($goto)?$goto:null,0,$vm);

        echo '</td><tr>';

        echo '<tr>';
        echo '<td colspan="2" align="right"><input name="Submit" type="button" value="Submit Changes" onclick="checkaddsipauto(addsipauto)"></td>';
        echo '</tr>';

     ?>
        </form>
     </table>

<?php

        } else if ($autogensip == "updatesip") {
?>

<table width="99%" bgcolor="#DDDDDD" border="0" cellpadding="3" cellspacing="1">
<form name="updatesipauto" action="config.php?mode=pbx&display=<?php echo urlencode($dispnum)?>" method="post">
<input type="hidden" name="display" value="<?php echo $dispnum?>"/>
<input type="hidden" name="action" value="updatesip"/>
<input type="hidden" name="mailbox" value=""/>

    <?php
        echo '<tr>';
        echo '<td width="180" align="left"><a href="#" class="info">UPDATE SIP Extensions<span>This Option update SIP extensions in your Range. Valid Range are from:<br>100 to 899, 1000 to 8999, 10000 to 89999.</span></a>:</td><td><input type="text" size="6" name="account_start" value=""><b> to</b><input type="text" size="6" name="account_end" value="">';
        echo '</td>';
        echo '</tr>';

        echo '<tr><td colspan="2"><h5>Account Settings:</h5></td></tr>';

        echo '<tr>';
        echo '<td align="left"><a href="#" class="info">'._("Outbound CallerIDName").'<span>'._("Overrides the caller id when dialing out a trunk. Any setting here will override the common outbound caller id set in the Trunks admin.<br>Format: <b>caller name</b>").'</span></a>:</td><td><input type="text" size="25" name="outcidname" value="">';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left"><a href="#" class="info">'._("Outbound CallerIDNum").'<span>'._("Overrides the caller id num when dialing out a trunk. Any setting here will override the common outbound caller id set in the Trunks admin.").'</span></a>:</td><td><input type="text" size="20" name="outcidnum" value="">';
        echo '<input size="4" maxlength="4" name="cidnuminc" type="text" value=""> (n+1) </td>';
        echo '</tr>';

        echo "<tr>";
        echo "<td align=\"left\">Direct DID:</td><td><input type=\"text\" size=\"15\" name=\"directdid\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Extension Password:</td><td><input type=\"password\" size=\"15\" name=\"secret\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Accountcode:</td><td><input type=\"text\" size=\"25\" maxlength=\"9\" name=\"accountcode\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Default Codec (disallow):</td><td><input type=\"text\" size=\"35\" name=\"disallow\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Default Codec (allow):</td><td><input type=\"text\" size=\"35\" name=\"allow\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo '<td align="left"><a href="#" class="info">'._("Call (Disallow)").'<span>'._("
<strong>0</strong> for disable all local prefix.<br>
<strong>1-8</strong> for disable prefix with special rate.<br>
<strong>3</strong> for disable prefix of mobile phone.<br>
<strong>4</strong> for disable prefix with special rate.<br>
<strong>7</strong> for disable internet ISP calls.<br><br>
Numbers <strong>2</strong> - <strong>5</strong> - <strong>6</strong> - <strong>9</strong> they are reserved for future requirements.<br><br>
Example: <strong>0;3;7</strong>. Example:<strong>040;347;800</strong>.<br>").'</span></a>:</td>';
        echo '<td><input type="text" size="30" name="nocall" value=""></td>';
        echo "</tr>";

        echo "<tr>";
        echo '<tr><td align="left"><a href="#" class="info">'._("Call (Allow)").'<span>'._("
<strong>0</strong> for enabled all local prefix.<br>
<strong>1-8</strong> for enabled prefix with special rate.<br>
<strong>3</strong> for enabled prefix of mobile phone.<br>
<strong>4</strong> for enabled prefix with special rate.<br>
<strong>7</strong> for enabled internet ISP calls.<br><br>
Numbers <strong>2</strong> - <strong>5</strong> - <strong>6</strong> - <strong>9</strong> they are reserved for future requirements.<br><br>
Example: <strong>0;3;7</strong>. Example:<strong>040;347;800</strong>.<br>").'</span></a>:</td>';
        echo '<td><input type="text" size="30" name="allowcall" value=""></td>';
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Full Name:</td><td><input type=\"text\" size=\"30\" name=\"name\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Callgroup:</td><td><input type=\"text\" size=\"15\" name=\"callgroup\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">PickupGroup:</td><td><input type=\"text\" size=\"15\" name=\"pickupgroup\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">CanReinvite:</td><td>&nbsp;&nbsp;<select name=\"canreinvite\" size=\"1\"><option value=\"\"></option><option value=\"no\">No</option><option value=\"yes\">Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Context:</td><td><input type=\"text\" size=\"30\" name=\"context\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">DTMF Mode:</td><td>&nbsp;&nbsp;<select name=\"dtmfmode\" size=\"1\"><option value=\"\"></option><option value=\"rfc2833\">rfc2833</option><option value=\"inband\">inband</option><option value=\"info\">info</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">NAT:</td><td>&nbsp;&nbsp;<select name=\"nat\" size=\"1\"><option value=\"\"></option><option value=\"no\">No</option><option value=\"yes\">Yes</option><option value=\"never\">Never</option><option value=\"route\">Route</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Sip Port:</td><td><input type=\"text\" size=\"5\" name=\"port\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo '<td align="left"><a href="#" class="info">'._("Qualify").'<span>'._("If you turn on qualify, Asterisk will send a SIP OPTIONS command regularly to check that the device is still online. If you leave empty Qualify is Disabled.").'</span></a>:</td><td><input type="text" size="5" id="qualify" name="qualify" value=""> <b>MilliSeconds</b></td>';
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">SubscribeContext:</td><td><input type=\"text\" size=\"30\" name=\"subscribecontext\" value=\"\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Type:</td><td>&nbsp;&nbsp;<select name=\"type\" size=\"1\" onchange=\"checkQualifyUpdateSipGenerator(updatesipauto);\"><option value=\"\"></option><option value=\"friend\">Friend</option><option value=\"user\">User</option><option value=\"peer\">Peer</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Allow Subscribe:</td><td>&nbsp;&nbsp;<select name=\"allowsubscribe\" size=\"1\"><option value=\"\"></option><option value=\"no\">No</option><option value=\"yes\">Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Call Limit:</td><td><input type=\"text\" size=\"2\" name=\"calllimit\" value=\"99\"></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Video Support:</td><td>&nbsp;&nbsp;<select name=\"videosupport\" size=\"1\"><option value=\"\"></option><option value=\"no\">No</option><option value=\"yes\">Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">T.38 Passthrough:</td><td>&nbsp;&nbsp;<select name=\"t38pt_udptl\" size=\"1\"><option value=\"\"></option><option value=\"no\">No</option><option value=\"yes\">Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Language:</td><td>&nbsp;&nbsp;<select name=\"language\" size=\"1\"><option value=\"\"></option><option value=\"en\">English</option><option value=\"it\">Italian</option></select></td>";
        echo "</tr>";

        echo '<tr><td colspan="2"><h5>Extra Features:</h5></td></tr>';

        echo "<tr>";
        echo "<td align=\"left\">Recall on Busy:</td><td>&nbsp;&nbsp;<select name=\"rob\" size=\"1\"><option value=\"\"></option><option value=\"Never\">No</option><option value=\"Always\">Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\">Call Waiting:</td><td>&nbsp;&nbsp;<select name=\"cw\" size=\"1\"><option value=\"\"></option><option value=\"Never\">No</option><option value=\"Always\">Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\"><a href=\"#\" class=\"info\">Record Incoming Calls<span>Record ALL INBOUND CALLS received at this extension.</span></a>:</td><td>&nbsp;&nbsp;<select name=\"record_in\" size=\"1\"><option value=\"Never\"></option><option value=\"Never\">No</option><option value=\"Always\">Yes</option></select></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"left\"><a href=\"#\" class=\"info\">Record Ougoing Calls<span>Record ALL OUTBOUND CALLS received at this extension.</span></a>:</td><td>&nbsp;&nbsp;<select name=\"record_out\" size=\"1\"><option value=\"Never\"></option><option value=\"Never\">No</option><option value=\"Always\">Yes</option></select></td>";
        echo "</tr>";

        echo '<tr>';
        echo '<td align="left"><a href="#" class="info">'._("Ring Time").'<span>'._("Number of seconds to ring phones before sending callers to Voicemail/Destination.<br>If default is set, the General Settings value are used!").'<br></span></a>:</td>';
        echo '<td>&nbsp;&nbsp;<select name="ringtime">';
        echo '<option value=""></option>';

                $default = (isset($ringtime) ? $ringtime : 0);
                for ($i=0; $i <= 60; $i+=5) {
                    if ($i == 0)
                            echo '<option value="default">'._("Default").'</option>';
                                else
                                echo '<option value="'.$i.'" '.($i == $ringtime ? 'SELECTED' : '').'>'.$i.' Seconds</option>\n';
                }

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="2" align="right"><input name="Submit" type="button" value="Submit Changes" onclick="checkupdatesipauto(updatesipauto)"></td>';
        echo '</tr>';

     ?>
        </form>
     </table>

<?php
        }
?>

     <br><h6></h6>
