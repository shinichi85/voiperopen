<?php
header ("Content-type: application/x-javascript");


if (!extension_loaded('gettext')) {
       function _($str) {
               return $str;
       }
} else {
    if (isset($_COOKIE['lang'])) {
        setlocale(LC_MESSAGES,  $_COOKIE['lang']);
    } else {
        setlocale(LC_MESSAGES,  'en_US');
    }
    bindtextdomain('amp','../i18n');
    textdomain('amp');
}

?>

function checkForm(theForm) {

    $action = theForm.action.value;
    $nocall = theForm.nocall.value;
    $allowcall = theForm.allowcall.value;
    $tech = theForm.tech.value;
    $account = theForm.account.value;
    $accountfix = parseInt($account);
    $vmpwd = theForm.vmpwd.value;
    $email = theForm.email.value;
    $pager = theForm.pager.value;
    $name = theForm.name.value;
    $context = theForm.context.value;
    var bad = "false";

    if ($tech != "zap") {
        $secret = theForm.secret.value;
        $host = theForm.host.value;
        $type = theForm.type.value;
        $username = theForm.username.value;
        if ($username == "") {
            theForm.username.value = $account;
            $username = $account;
        }
    }

    $mailbox = theForm.mailbox.value;
    $fullname = theForm.name.value;
    $vm = theForm.vm.value;

    if ($tech == "zap") {
        $channel=theForm.channel.value;
    }
    
    if (($tech != "zap") && $secret == "") {
        var answer = confirm("You have not entered a Extension Password.\n\nAre you sure to continue without password?")
        if (answer==false) {
            bad="true";
            theForm.secret.focus();
        } else {
                bad="false";
        }
    }
    
    if (($tech != "zap") && ($account == "" || $context == "" || $host == "" || $type == ""  || $username == "")) {
        <?php echo "alert('"._("Please fill in all required fields.")."')"?>;
    } else if (($tech == "zap") && ( $account == "" || $context == "" || $channel=="")) {
        <?php echo "alert('"._("Please fill in all required fields.")."')"?>;
    } else if (($account.indexOf('0') == 0) && ($account.length > 1)) {
        <?php echo "alert('"._("Wrong Extensions. Valid Range are from 100 to 899 - 1000 to 8999 - 10000 to 89999")."')"?>;
    } else if (($accountfix < 100) || ($accountfix > 89999) && $action == "add") {
        <?php echo "alert('"._("Wrong Extensions. Valid Range are from 100 to 899 - 1000 to 8999 - 10000 to 89999")."')"?>;
    } else if ($account != $accountfix) {
        <?php echo "alert('"._("There is something wrong with your Extension Number - it must be in integer")."')"?>;
    } else if ($name == "" && $action == "add") {
        <?php echo "alert('"._("You have not entered a Full Name for this Extension.")."')"?>;
    } else if (($allowcall != "") && (!$allowcall.match('^[0-9;]+$'))) {
        <?php echo "alert('"._("Call Allow can only contain numbers and a special separator character.")."')"?>;
    } else if (($nocall != "") && (!$nocall.match('^[0-9;]+$'))) {
        <?php echo "alert('"._("Call Disallow can only contain numbers and a special separator character.")."')"?>;

        } else if (theForm.vm.value == "enabled") {

        defaultEmptyOK = false;
        if (!isInteger(theForm.vmpwd.value))
        return warnInvalid(theForm.vmpwd, "Please enter a valid Voicemail Password, using digits only");

        defaultEmptyOK = true;
        if (!isEmail(theForm.email.value))
        return warnInvalid(theForm.email, "Please enter a valid Email Address");

        defaultEmptyOK = true;
        if (!isEmail(theForm.pager.value))
        return warnInvalid(theForm.pager, "Please enter a valid Pager Email Address");

        defaultEmptyOK = false;
        if (isEmpty(theForm.vmcontext.value) || isWhitespace(theForm.vmcontext.value))
        return warnInvalid(theForm.vmcontext, "VM Context cannot be blank");

        defaultEmptyOK = false;
        if (isEmpty(theForm.name.value))
        return warnInvalid(theForm.name, "FullName cannot be blank");

            if (bad == "false") {

                    theForm.submit();
    }

        } else if (theForm.vm.value == "destination") {

    $dialgoto = theForm.dial_args0.value;
    var whichitem = 0;
    while (whichitem < theForm.goto_indicate0.length) {
        if (theForm.goto_indicate0[whichitem].checked) {
            theForm.goto0.value=theForm.goto_indicate0[whichitem].value;
        }
        whichitem++;
    }

    var gotoType = theForm.elements[ "goto0" ].value;
    if (gotoType == 'custom') {
        var gotoVal = theForm.elements[ "custom_args0"].value;
        if (gotoVal.indexOf('custom') == -1) {
            bad = "true";
            <?php echo "alert('"._("Custom Goto contexts must contain the string \"custom\".  ie: custom-app,s,1")."')"?>;
        }
    }


    if (gotoType == 'dial') {
        if ($dialgoto == "") {

                bad="true";
                <?php echo "alert('"._("Custom number to dial must not be blank")."')"?>;

        } else if (!$dialgoto.match('^[0-9]+$')) {

                bad="true";
                <?php echo "alert('"._("Custom number to dial only contain numbers")."')"?>;

        }
    }

            if (bad == "false") {

                    theForm.submit();
    }

    } else {

            if (bad == "false") {

                    theForm.submit();
    }

    }
}

function checkMeetme(theForm) {
    $account = theForm.account.value;
    $name = theForm.name.value;
    $userpin = theForm.userpin.value;
    $adminpin = theForm.adminpin.value;
    var bad = "false";

           if ($account == "") {

                bad="true";
                <?php echo "alert('"._("Conference number must not be blank")."')"?>;

        } else if (!$account.match('^[0-9]+$')) {

                bad="true";
                <?php echo "alert('"._("Conference number only contain numbers")."')"?>;

        } else if ($name == "") {

                bad="true";
                <?php echo "alert('"._("Conference name must not be blank")."')"?>;

        }

            if (bad == "false") {

                    theForm.submit();
    }
}


function checkGeneral(theForm) {
    defaultEmptyOK = false;

    if (!isInteger(theForm.RINGTIMER.value)) {
    return warnInvalid(theForm.RINGTIMER, "Please enter a valid numeric RingTimer");

    return true;

    }

    if (theForm.CB_TRUNK.value == "") {
    <?php echo "alert('"._("Please enter a valid CallBack Trunk.")."')"?>;

    return true;

    }


    if (!isEmail(theForm.FAX_RX_EMAIL.value)) {
    return warnInvalid(theForm.FAX_RX_EMAIL, "Please enter a valid Email address");

    return true;

    }

    defaultEmptyOK = true;

    if (!isEmail(theForm.FAX_RX_EMAIL2.value)) {
    return warnInvalid(theForm.FAX_RX_EMAIL2, "Please enter a valid CarbonCopy Email address");

    return true;

    }

    defaultEmptyOK = true;

    if (!isEmail(theForm.FAX_RX_FROM.value)) {
    return warnInvalid(theForm.FAX_RX_FROM, "Please enter a valid Email address that faxes appear to come from");

    return true;

    }

    if (!isInteger(theForm.ZAP_PASSWORD.value)) {
    return warnInvalid(theForm.ZAP_PASSWORD, "Please enter a valid numeric Password");

    return true;

    }

    if (!isInteger(theForm.MONITOR_PASSWORD.value)) {
    return warnInvalid(theForm.MONITOR_PASSWORD, "Please enter a valid numeric Password");

    return true;

    }

    if (!isInteger(theForm.CALLBACKEXT_PASSWORD.value)) {
    return warnInvalid(theForm.CALLBACKEXT_PASSWORD, "Please enter a valid numeric Password");

    return true;

    } else {

    theForm.submit();

    }
}

function incoming_switch(listID) {
  if(listID.style.display=="none") {
    listID.style.display="";
  } else {
    listID.style.display="none";
  }
}
function incoming_icoswitch(bid) {
  var incoming_imagepath = "images/";
  icoID = document.getElementById('pic'+bid);
  if(icoID.src.indexOf("minus") != -1) {
    icoID.src = incoming_imagepath+"plus.gif";
  } else {
    icoID.src = incoming_imagepath+"minus.gif";
  }
}
function incoming_div_switch(bid) {
    incoming_switch(document.getElementById('pe'+bid));
    incoming_icoswitch(bid);
}

function checkIncoming(theForm) {
    $INCOMING = theForm.INCOMING.value;
    $AFTER_INCOMING = theForm.AFTER_INCOMING.value;
    $HOLIDAY_INCOMING = theForm.HOLIDAY_INCOMING.value;
    $INCOMING_DESC = theForm.INCOMING_DESC.value

    if ($INCOMING == "") {
        <?php echo "alert('"._("Please select where you would like to send incoming calls in Regular Hours.")."')"?>;
    } else if ($AFTER_INCOMING == "") {
        <?php echo "alert('"._("Please select where you would like to send incoming calls in After Hours.")."')"?>;
    } else if ($HOLIDAY_INCOMING == "") {
        <?php echo "alert('"._("Please select where you would like to send incoming calls in Holiday.")."')"?>;
        incoming_div_switch(3);
    } else if ($INCOMING_DESC == "") {
        <?php echo "alert('"._("Incoming Description must not be blank")."')"?>;
    } else {
    theForm.submit();
    }
}

function checkGRP(theForm,action) {
    var bad = "false";
    $dialgoto = theForm.dial_args0.value;

    var whichitem = 0;
    while (whichitem < theForm.goto_indicate0.length) {
        if (theForm.goto_indicate0[whichitem].checked) {
            theForm.goto0.value=theForm.goto_indicate0[whichitem].value;
        }
        whichitem++;
    }

    var gotoType = theForm.elements[ "goto0" ].value;
    if (gotoType == 'custom') {
        var gotoVal = theForm.elements[ "custom_args0"].value;
        if (gotoVal.indexOf('custom') == -1) {
            bad = "true";
            <?php echo "alert('"._("Custom Goto contexts must contain the string \"custom\".  ie: custom-app,s,1")."')"?>;
        }
    }


    if (gotoType == 'dial') {
        if ($dialgoto == "") {

                bad="true";
                <?php echo "alert('"._("Custom number to dial must not be blank")."')"?>;

        } else if (!$dialgoto.match('^[0-9]+$')) {

                bad="true";
                <?php echo "alert('"._("Custom number to dial only contain numbers")."')"?>;

        }
    }


    defaultEmptyOK = false;
    if (isEmpty(theForm.grplist.value)) {
        bad="true";
        <?php echo "alert('"._("Please enter an extension list.")."')"?>;
    }

    $account = theForm.account.value;
    defaultEmptyOK = false;
    if (!isInteger(theForm.account.value)) {
        bad="true";
        <?php echo "alert('"._("Invalid Group Number specified")."')"?>;

    } else if (($account.indexOf('0') == 0) && ($account.length > 1)) {
        bad="true";
        <?php echo "alert('"._("Group numbers with more than one digit cannot begin with 0")."')"?>;

    }

    defaultEmptyOK = false;
    if (!isAlphanumeric(theForm.description.value)) {
        bad="true";
        <?php echo "alert('"._("Please enter a valid Description.")."')"?>;
    }

    defaultEmptyOK = true;
    if (!isPrefix(theForm.grppre.value)) {
        <?php echo "alert('"._("Invalid prefix. Valid characters: a-z A-Z 0-9 : _ -")."')"?>;
        bad = "true";
    }

    defaultEmptyOK = true;
    if (!isPrefix(theForm.alertinfo.value)) {
        <?php echo "alert('"._("Invalid Alert Info. Valid characters: a-z A-Z 0-9 : _ -")."')"?>;
        bad = "true";
    }

    defaultEmptyOK = false;
    var grptimeVal = theForm.grptime.value;
    if (!isInteger(theForm.grptime.value)) {
            bad = "true";
            <?php echo "alert('"._("Invalid time specified")."')"?>;
    } else if (grptimeVal < 1 || grptimeVal > 60) {
            bad = "true";
            <?php echo "alert('"._("Time must be between 1 and 60 seconds")."')"?>;
        }

    if (bad == "false") {
        theForm.action.value = action;
        theForm.submit();
    }
}

function checkQ(theForm,action) {
        $queuename = theForm.name.value;
        $dialgoto = theForm.dial_args0.value;
        var bad = "false";

        var whichitem = 0;
        while (whichitem < theForm.goto_indicate0.length) {
                if (theForm.goto_indicate0[whichitem].checked) {
                        theForm.goto0.value=theForm.goto_indicate0[whichitem].value;
                }
                whichitem++;
        }

        var gotoType = theForm.elements[ "goto0" ].value;
        if (gotoType == 'custom') {
                var gotoVal = theForm.elements[ "custom_args0"].value;
                if (gotoVal.indexOf('custom') == -1) {
                        bad = "true";
                        <?php echo "alert('"._("Custom Goto contexts must contain the string \"custom\".  ie: custom-app,s,1")."')"?>;
                }
        }

        if (gotoType == 'dial') {
        if ($dialgoto == "") {

                bad="true";
                <?php echo "alert('"._("Custom number to dial must not be blank")."')"?>;

        } else if (!$dialgoto.match('^[0-9]+$')) {

                bad="true";
                <?php echo "alert('"._("Custom number to dial only contain numbers")."')"?>;

        }
    }

        $account = theForm.account.value;
        if ($account == "") {
                <?php echo "alert('"._("Queue Number must not be blank")."')"?>;
                bad="true";
        }
        else if (($account.indexOf('0') == 0) && ($account.length > 1)) {
                <?php echo "alert('"._("Queue numbers with more than one digit cannot begin with 0")."')"?>;
                bad="true";
        }
        else if (!$account.match('^[0-9]+$')) {
                <?php echo "alert('"._("Queue numbers must only contain numbers")."')"?>;
                bad="true";
        }

        if ($queuename == "") {
                <?php echo "alert('"._("Queue name must not be blank")."')"?>;
                bad="true";
        } else if (!$queuename.match('^[a-zA-Z][a-zA-Z0-9]+$')) {
                <?php echo "alert('"._("Queue name cannot start with a number, and can only contain letters and numbers")."')"?>;
                bad="true";
        }

    if (bad == "false") {
        theForm.action.value = action;
        theForm.submit();
    }
}

function checkDID(theForm) {
    var bad = "false";
    var whichitem = 0;
    defaultEmptyOK = false;

    $dialgoto = theForm.dial_args0.value;
    while (whichitem < theForm.goto_indicate0.length) {
        if (theForm.goto_indicate0[whichitem].checked) {
            theForm.goto0.value=theForm.goto_indicate0[whichitem].value;
        }
        whichitem++;
    }

    var gotoType = theForm.elements[ "goto0" ].value;
    if (gotoType == 'custom') {
        var gotoVal = theForm.elements[ "custom_args0"].value;
        if (gotoVal.indexOf('custom') == -1) {
            bad = "true";
            <?php echo "alert('"._("Custom Goto contexts must contain the string \"custom\".  ie: custom-app,s,1")."')"?>;
        }
    }

        if (gotoType == 'dial') {
        if ($dialgoto == "") {

                bad="true";
                <?php echo "alert('"._("Custom number to dial must not be blank")."')"?>;

        } else if (!$dialgoto.match('^[0-9]+$')) {

                bad="true";
                <?php echo "alert('"._("Custom number to dial only contain numbers")."')"?>;

        }
    }

    defaultEmptyOK = true;
    if (!isEmail(theForm.faxemail.value)) {
    bad="true";
    return warnInvalid(theForm.faxemail, "Please enter a valid Fax Email or leave it empty to use the default");

    }

    defaultEmptyOK = true;
    if (!isEmail(theForm.faxemail2.value)) {
    bad="true";
    return warnInvalid(theForm.faxemail2, "Please enter a valid Carboncopy Fax Email or leave it empty to use the default");

    }

    defaultEmptyOK = true;
    if (!isDialpattern(theForm.extension.value)) {
    bad="true";
        if (!confirm("DID information is normally just an incoming telephone number or for advanced users, a valid Asterisk Dial Pattern\n\nYou have entered a non standard DID pattern.\n\nAre you sure this is correct?"))
            return false;
            else
            bad="false";
    }

    defaultEmptyOK = true;
    if (!isDialpattern(theForm.extension.value)) {
    bad="true";
    return warnInvalid(theForm.extension, "Please enter a valid DID Number");

    }

    defaultEmptyOK = true;
    if (!isDialpattern(theForm.cidnum.value)) {
    bad="true";
    return warnInvalid(theForm.cidnum, "Please enter a valid Caller ID Number");

    }

    if (!isInteger(theForm.wait.value)) {
    bad="true";
    return warnInvalid(theForm.wait, "Please enter a valid number for Pause after answer");

    }

    if (!isInteger(theForm.ADDPrefix.value)) {
    bad="true";
    return warnInvalid(theForm.ADDPrefix, "Please enter a valid number for ADD Inbound Prefix");

    }

    if (theForm.cidnum.value != "" && theForm.channel.value != "" ) {
    bad="true";
    return warnInvalid(theForm.extension, "DID number and CID number MUST be blank when used with zaptel channel routing");
    }

    if (theForm.extension.value != "" && theForm.channel.value != "" ) {
    bad="true";
    return warnInvalid(theForm.extension, "DID number and CID number MUST be blank when used with zaptel channel routing");
    }


    if (theForm.extension.value == "" && theForm.cidnum.value == "") {
    bad="true";
        if (!confirm("Leaving the DID Number AND the Caller ID Number empty will match all incoming\ncalls received not routed using any other defined Incoming Route.\n\nAre you sure?"))
            return false;
            else
            bad="false";
    }


    if (bad == "false") {
        theForm.submit();
    }


}

function openWindow(url,width,height) {
    popupWin = window.open(url, '', 'width='+width + ',height='+height)
}

function checkIVR(theForm,ivr_num_options) {
    var bad = "false";
    for (var formNum = 0; formNum < ivr_num_options; formNum++) {

        var whichitem = 0;

        while (whichitem < theForm['goto_indicate'+formNum].length) {
            if (theForm['goto_indicate'+formNum][whichitem].checked) {
                theForm['goto'+formNum].value=theForm['goto_indicate'+formNum][whichitem].value;
            }
            whichitem++;
        }

        var gotoType = theForm.elements[ "goto"+formNum ].value;
        if (gotoType == 'custom') {
            var gotoVal = theForm.elements[ "custom_args"+formNum ].value;
            if (gotoVal.indexOf('custom') == -1) {
                bad = "true";
                var item = formNum + 1;
                <?php echo "alert('"._("There is a problem with option number")?> '+item+'.\n\n<?php echo _("Custom Goto contexts must contain the string \"custom\".  ie: custom-app,s,1")."')"?>;
            }
        }

        var OptionType = theForm.elements[ "ivr_option"+formNum ].value;
        if (!OptionType.match('^[0-9]+$')) {
                bad = "true";
                var item = formNum + 1;
                <?php echo "alert('"._("There is a problem with option number")?> '+item+'.\n\n<?php echo _("Option number must contain only numbers")."')"?>;
            }


        if (gotoType == 'dial') {
                $dialgoto = theForm.elements[ "dial_args"+formNum ].value;
        if ($dialgoto == "") {

                bad="true";
                var item = formNum + 1;
                <?php echo "alert('"._("There is a problem with option number")?> '+item+'.\n\n<?php echo _("Custom number to dial must not be blank")."')"?>;

        } else if (!$dialgoto.match('^[0-9]+$')) {

                bad="true";
                var item = formNum + 1;
                <?php echo "alert('"._("There is a problem with option number")?> '+item+'.\n\n<?php echo _("Custom number to dial only contain numbers")."')"?>;

        }
    }


    }
    if (bad == "false") {
        theForm.submit();
    }
}

function checkVoicemail(theForm) {
    $vm = theForm.elements["vm"].value;
    if ($vm == 'disabled') {
        document.getElementById('voicemail').style.display='none';
        document.getElementById('jumpto').style.display='none';
        document.getElementById('ringtime').disabled=true;
        theForm.vmpwd.value = '';
        theForm.email.value = '';
        theForm.pager.value = '';
        theForm.ringtime.value = '';
    } else if ($vm == 'destination') {
        document.getElementById('voicemail').style.display='none';
        document.getElementById('jumpto').style.display='block';
        document.getElementById('ringtime').disabled=false;
        theForm.vmpwd.value = '';
        theForm.email.value = '';
        theForm.pager.value = '';
    } else if ($vm == 'enabled') {
        document.getElementById('voicemail').style.display='block';
        document.getElementById('jumpto').style.display='none';
        document.getElementById('ringtime').disabled=false;
    }
}

function checkVoicemailAdd(theForm) {
    $vm = theForm.elements["vm"].value;
    if ($vm == 'disabled') {
        document.getElementById('voicemail').style.display='none';
        theForm.vmpwd.value = '';
        theForm.email.value = '';
        theForm.pager.value = '';
    } else {
        document.getElementById('voicemail').style.display='block';
    }
}

function checkQualify(theForm) {
    $type = theForm.elements["type"].value;
    if ($type == 'user') {
        document.getElementById('qualify').disabled=true;
        theForm.qualify.value = 'no';
    } else {
        document.getElementById('qualify').disabled=false;
        theForm.qualify.value = '500';
    }
}

function checkQualifyUpdateSipGenerator(theForm) {
    $type = theForm.elements["type"].value;
    if ($type == 'user') {
        document.getElementById('qualify').disabled=true;
        theForm.qualify.value = 'no';
    } else {
        document.getElementById('qualify').disabled=false;
        theForm.qualify.value = '';
    }
}

function checkAddSipAuto(theForm) {
    $vm = theForm.elements["vm"].value;
    if ($vm == 'destination') {
        document.getElementById('jumpto').style.display='block';
        document.getElementById('ringtime').disabled=false;
    } else {
        document.getElementById('jumpto').style.display='none';
        document.getElementById('ringtime').disabled=true;
        theForm.ringtime.value = '';
    }
}

function checkLoopDestination(theForm) {
    $loopdestinationcontext = theForm.elements["loopdestinationcontext"].value;
    if ($loopdestinationcontext == 'hangup') {
        document.getElementById('loopdestination').style.display='none';
    } else {
        document.getElementById('loopdestination').style.display='block';
    }
}


function hideExtenFields(theForm) {
    if(theForm.tech.value == 'iax2') {
        document.getElementById('dtmfmode').style.display = 'none';
        document.getElementById('secret').style.display = 'inline';
        document.getElementById('channel').style.display = 'none';
        document.getElementById('dial').style.display = 'none';
    } else if (theForm.tech.value == 'sip') {
        document.getElementById('dtmfmode').style.display = 'inline';
        document.getElementById('secret').style.display = 'inline';
        document.getElementById('channel').style.display = 'none';
        document.getElementById('dial').style.display = 'none';
    } else if (theForm.tech.value == 'zap') {
        document.getElementById('dtmfmode').style.display = 'none';
        document.getElementById('secret').style.display = 'none';
        document.getElementById('channel').style.display = 'block';
        document.getElementById('dial').style.display = 'none';
    } else if (theForm.tech.value == 'custom') {
        document.getElementById('dtmfmode').style.display = 'none';
        document.getElementById('secret').style.display = 'none';
        document.getElementById('channel').style.display = 'none';
        document.getElementById('dial').style.display = 'block';
    }
}

function hideExtenFields_Exten(theForm) {
    if(theForm.tech.value == 'iax2') {
        document.getElementById('dtmfmode').style.display = 'none';
        document.getElementById('secret').style.display = 'inline';
        document.getElementById('channel').style.display = 'none';
    } else if (theForm.tech.value == 'sip') {
        document.getElementById('dtmfmode').style.display = 'inline';
        document.getElementById('secret').style.display = 'inline';
        document.getElementById('channel').style.display = 'none';
    } else if (theForm.tech.value == 'zap') {
        document.getElementById('dtmfmode').style.display = 'none';
        document.getElementById('secret').style.display = 'none';
        document.getElementById('channel').style.display = 'block';
    }
}

function checkAmpUser(theForm, action) {
    $username = theForm.username.value;
    $deptname = theForm.deptname.value;

    if ($username == "") {
        <?php echo "alert('"._("Username must not be blank")."')"?>;
    } else if (!$username.match('^[a-zA-Z][a-zA-Z0-9]+$')) {
        <?php echo "alert('"._("Username cannot start with a number, and can only contain letters and numbers")."')"?>;
    } else if ($deptname == "default") {
        <?php echo "alert('"._("For security reasons, you cannot use the department name default")."')"?>;
    } else if ($deptname == " ") {
        <?php echo "alert('"._("Department name cannot have a space")."')"?>;
    } else {
        theForm.action.value = action;
        theForm.submit();
    }
}

function changeLang(lang) {
    document.cookie='lang='+lang;
    window.location.reload();
}

function decision(message, url){
if(confirm(message)) location.href = url;
}

// Various form checking helper functions -- very useful
var whitespace = " \t\n\r";
var decimalPointDelimiter = ".";
var defaultEmptyOK = false;

function isEmail (s) {
    if (isEmpty(s))
       if (isEmail.arguments.length == 1) return defaultEmptyOK;
       else return (isEmail.arguments[1] == true);
    // is s whitespace?
    if (isWhitespace(s)) return false;
    // there must be >= 1 character before @, so we
    // start looking at character position 1
    // (i.e. second character)
    var i = 1;
    var sLength = s.length;
    // look for @
    while ((i < sLength) && (s.charAt(i) != "@")) {
        i++;
    }

    if ((i >= sLength) || (s.charAt(i) != "@")) return false;
    else i += 2;

    // look for .
    while ((i < sLength) && (s.charAt(i) != ".")) {
        i++;
    }
    // there must be at least one character after the .
    if ((i >= sLength - 1) || (s.charAt(i) != ".")) return false;
    else return true;
}

function isAlphabetic (s) {
    var i;
    if (isEmpty(s))
       if (isAlphabetic.arguments.length == 1) return defaultEmptyOK;
       else return (isAlphabetic.arguments[1] == true);
    // Search through string's characters one by one
    // until we find a non-alphabetic character.
    // When we do, return false; if we don't, return true.
    for (i = 0; i < s.length; i++) {
        // Check that current character is letter.
        var c = s.charAt(i);

        if (!isLetter(c))
        return false;
    }
    // All characters are letters.
    return true;
}

function isAlphanumeric (s) {
    var i;
    if (isEmpty(s))
       if (isAlphanumeric.arguments.length == 1) return defaultEmptyOK;
       else return (isAlphanumeric.arguments[1] == true);
    // Search through string's characters one by one
    // until we find a non-alphanumeric character.
    // When we do, return false; if we don't, return true.
    for (i = 0; i < s.length; i++) {
        // Check that current character is number or letter.
        var c = s.charAt(i);
        if (! (isLetter(c) || isDigit(c) ) )
        return false;
    }
    // All characters are numbers or letters.
    return true;
}

function isPrefix (s) {
    var i;
    if (isEmpty(s))
       if (isPrefix.arguments.length == 1) return defaultEmptyOK;
       else return (isPrefix.arguments[1] == true);
    // Search through string's characters one by one
    // until we find a non-prefix character.
    // When we do, return false; if we don't, return true.
    for (i = 0; i < s.length; i++) {
        // Check that current character is number or letter.
        var c = s.charAt(i);
        if (! (isPrefixChar(c) ) )
        return false;
    }
    // All characters are numbers or letters.
    return true;
}

function isCallerID (s) {
    var i;
    if (isEmpty(s))
       if (isCallerID.arguments.length == 1) return defaultEmptyOK;
       else return (isCallerID.arguments[1] == true);
    // Search through string's characters one by one
    // until we find a non-prefix character.
    // When we do, return false; if we don't, return true.
    for (i = 0; i < s.length; i++) {
        // Check that current character is number or letter.
        var c = s.charAt(i);
        if (! (isCallerIDChar(c) ) )
        return false;
    }
    // All characters are numbers or letters.
    return true;
}

function isDialpattern (s) {
    var i;
    if (isEmpty(s))
       if (isDialpattern.arguments.length == 1) return defaultEmptyOK;
       else return (isDialpattern.arguments[1] == true);
    // Search through string's characters one by one
    // until we find a non-prefix character.
    // When we do, return false; if we don't, return true.
    for (i = 0; i < s.length; i++) {
        // Check that current character is number or letter.
        var c = s.charAt(i);
        if ( !isDialpatternChar(c) ) {
        if (c.charCodeAt(0) != 13 && c.charCodeAt(0) != 10) {
            //alert(c.charCodeAt(0));
            return false;
        }
    }
    }
    // All characters are numbers or letters.
    return true;
}

function isDialrule (s) {
    var i;

    if (isEmpty(s))
       if (isDialrule.arguments.length == 1) return defaultEmptyOK;
       else return (isDialrule.arguments[1] == true);

    for (i = 0; i < s.length; i++) {
        var c = s.charAt(i);
        if ( !isDialruleChar(c) ) {
            if (c.charCodeAt(0) != 13 && c.charCodeAt(0) != 10) {
                return false;
            }
        }
    }

    return true;
}

function isAddress (s) {
    var i;
    if (isEmpty(s))
       if (isAddress.arguments.length == 1) return defaultEmptyOK;
       else return (isAddress.arguments[1] == true);
    // Search through string's characters one by one
    // until we find a non-alphanumeric character.
    // When we do, return false; if we don't, return true.
    for (i = 0; i < s.length; i++) {
        // Check that current character is number or letter.
        var c = s.charAt(i);
        if (! (isAddrLetter(c) || isDigit(c) ) )
        return false;
    }
    // All characters are numbers or letters.
    return true;
}

function isPhone (s) {
    var i;
    if (isEmpty(s))
       if (isPhone.arguments.length == 1) return defaultEmptyOK;
       else return (isPhone.arguments[1] == true);
    // Search through string's characters one by one
    // until we find a non-alphanumeric character.
    // When we do, return false; if we don't, return true.
    for (i = 0; i < s.length; i++) {
        // Check that current character is number or letter.
        var c = s.charAt(i);
        if (!isPhoneDigit(c))
        return false;
    }
    // All characters are numbers or letters.
    return true;
}

function isURL (s) {
    var i;
    if (isEmpty(s))
       if (isURL.arguments.length == 1) return defaultEmptyOK;
       else return (isURL.arguments[1] == true);

    for (i = 0; i < s.length; i++) {
        // Check that current character is number or letter.
        var c = s.charAt(i);
        if (! (isURLChar(c) || isDigit(c) ) )
        return false;
    }

    return true;
}

function isInteger (s)

{   var i;

    if (isEmpty(s))
       if (isInteger.arguments.length == 1) return defaultEmptyOK;
       else return (isInteger.arguments[1] == true);

    // Search through string's characters one by one
    // until we find a non-numeric character.
    // When we do, return false; if we don't, return true.

    for (i = 0; i < s.length; i++)
    {
        // Check that current character is number.
        var c = s.charAt(i);

        if (!isDigit(c)) return false;
    }

    // All characters are numbers.
    return true;
}

function isIntegerWithSpecialChar (s)

{   var i;

    if (isEmpty(s))
       if (isIntegerWithSpecialChar.arguments.length == 1) return defaultEmptyOK;
       else return (isIntegerWithSpecialChar.arguments[1] == true);

    // Search through string's characters one by one
    // until we find a non-numeric character.
    // When we do, return false; if we don't, return true.

    for (i = 0; i < s.length; i++)
    {
        // Check that current character is number.
        var c = s.charAt(i);

        if (!isDigitWithSpecialChar(c)) return false;
    }

    // All characters are numbers.
    return true;
}

function isPINList (s)

{   var i;

    if (isEmpty(s))
       if (isPINList.arguments.length == 1) return defaultEmptyOK;
       else return (isPINList.arguments[1] == true);

    // Search through string's characters one by one
    // until we find a non-numeric character.
    // When we do, return false; if we don't, return true.

    for (i = 0; i < s.length; i++)
    {
        // Check that current character is number.
        var c = s.charAt(i);

        if (!isDigit(c) && c != ",") return false;
    }

    // All characters are numbers.
    return true;
}

function isDialidentifier(s)
{
    var i;

    if (isEmpty(s))
       if (isDialidentifier.arguments.length == 1) return defaultEmptyOK;
       else return (isDialidentifier.arguments[1] == true);

    for (i = 0; i < s.length; i++)
    {
        // Check that current character is number or a 'w'.
        var c = s.charAt(i);

        if (!isDigit(c) && c != "w" && c != "W") return false;
    }

    // All characters are numbers.
    return true;
}

function isDialDigits(s)
{
    var i;

    if (isEmpty(s))
       if (isDialDigits.arguments.length == 1) return defaultEmptyOK;
       else return (isDialDigits.arguments[1] == true);

    for (i = 0; i < s.length; i++)
    {
        // Check that current character is number or a 'w'.
        var c = s.charAt(i);

        if (!isDialDigitChar(c)) return false;
    }

    // All characters are numbers.
    return true;
}

function isDialPrefix(s)
{
    var i;

    if (isEmpty(s))
       if (isDialPrefix.arguments.length == 1) return defaultEmptyOK;
       else return (isDialPrefix.arguments[1] == true);

    for (i = 0; i < s.length; i++)
    {
        // Check that current character is number or a 'w'.
        var c = s.charAt(i);

        if ( !isDialDigitChar(c) && (c != "q") ) return false;
    }

    // All characters are numbers.
    return true;
}

function isFloat (s) {
    var i;
    var seenDecimalPoint = false;

    if (isEmpty(s))
       if (isFloat.arguments.length == 1) return defaultEmptyOK;
       else return (isFloat.arguments[1] == true);

    if (s == decimalPointDelimiter) return false;

    // Search through string's characters one by one
    // until we find a non-numeric character.
    // When we do, return false; if we don't, return true.

    for (i = 0; i < s.length; i++) {
        // Check that current character is number.
        var c = s.charAt(i);

        if ((c == decimalPointDelimiter) && !seenDecimalPoint) seenDecimalPoint = true;
        else if (!isDigit(c)) return false;
    }

    // All characters are numbers.
    return true;
}

function checkNumber(object_value) {

    if (object_value.length == 0)
        return true;

    var start_format = " .+-0123456789";
    var number_format = " .0123456789";
    var check_char;
    var decimal = false;
    var trailing_blank = false;
    var digits = false;

    check_char = start_format.indexOf(object_value.charAt(0))
    if (check_char == 1)
        decimal = true;
    else if (check_char < 1)
        return false;

    for (var i = 1; i < object_value.length; i++)
    {
        check_char = number_format.indexOf(object_value.charAt(i))
        if (check_char < 0)
            return false;
        else if (check_char == 1)
        {
            if (decimal)
                return false;
            else
                decimal = true;
        }
        else if (check_char == 0)
        {
            if (decimal || digits)
                trailing_blank = true;
        }
        else if (trailing_blank)
            return false;
        else
            digits = true;
    }

    return true
 }


function isWhitespace (s)

{   var i;

    // Is s empty?
    if (isEmpty(s)) return true;

    // Search through string's characters one by one
    // until we find a non-whitespace character.
    // When we do, return false; if we don't, return true.

    for (i = 0; i < s.length; i++)
    {
        // Check that current character isn't whitespace.
        var c = s.charAt(i);

        if (whitespace.indexOf(c) == -1) return false;
    }

    // All characters are whitespace.
    return true;
}


function isFilename(s)
{
    var i;

    if (isEmpty(s))
       if (isFilename.arguments.length == 1) return defaultEmptyOK;
       else return (isFilename.arguments[1] == true);

    for (i = 0; i < s.length; i++)
    {
        var c = s.charAt(i);

        if (!isFilenameChar(c)) return false;
    }

    return true;
}

function isEmpty(s)
{   return ((s == null) || (s.length == 0));
}

function isLetter (c)
{   return ( ((c >= "a") && (c <= "z")) || ((c >= "A") && (c <= "Z")) || (c == " ") || (c == "&") || (c == "'") || (c == "(") || (c == ")") || (c == "-") || (c == "/"))
}

function isAddrLetter (c)
{   return ( ((c >= "a") && (c <= "z")) || ((c >= "A") && (c <= "Z")) || (c == " ") || (c == "&") || (c == ",") || (c == ".") || (c == "(") || (c == ")") || (c == "-") || (c == "'") || (c == "/") )
}

function isDigit (c)
{   return ((c >= "0") && (c <= "9"))
}

function isDigitWithSpecialChar (c)
{   return ((c >= "0") && (c <= "9") || (c == "*"))
}

function isPhoneDigit (c)
{   return ( ((c >= "0") && (c <= "9")) || (c == " ") || (c == "-") || (c == "(") || (c == ")") )
}

function isPrefixChar (c)
{   return ( ((c >= "a") && (c <= "z")) || ((c >= "A") && (c <= "Z")) || ((c >= "0") && (c <= "9")) || (c == ":") || (c == "_") || (c == "-") )
}

function isCallerIDChar (c)
{   return ( ((c >= "a") && (c <= "z")) || ((c >= "A") && (c <= "Z")) || ((c >= "0") && (c <= "9")) || (c == "<") || (c == ">") || (c == "(") || (c == ")") || (c == " ") || (c == "\"") || (c == "&") || (c == "@") || (c == ".") )
}

function isDialpatternChar (c)
{   return ( ((c >= "0") && (c <= "9")) || (c == "[") || (c == "]") || (c == "-") || (c == "+") || (c == ".") || (c == "|") || (c == "Z" || c == "z") || (c == "X" || c == "x") || (c == "N" || c == "n") || (c == "*") || (c == "#" ) || (c == "_") || (c == "!"))
 }

function isDialDigitChar (c)
{   return ( ((c >= "0") && (c <= "9")) || (c == "*") || (c == "#" ) )
}

function isURLChar (c)
{   return ( ((c >= "a") && (c <= "z")) || ((c >= "A") && (c <= "Z")) || (c == ":") || (c == ",") || (c == ".") || (c == "%") || (c == "#") || (c == "-") || (c == "/") || (c == "?") || (c == "&") || (c == "=") )
}

function isDialruleChar (c)
{   return ( ((c >= "0") && (c <= "9")) || (c == "[") || (c == "]") || (c == "-") || (c == "+") || (c == ".") || (c == "|") || (c == "Z" || c == "z") || (c == "X" || c == "x") || (c == "N" || c == "n") || (c == "*") || (c == "#" ) || (c == "_") || (c == "!") || (c == "w") || (c == "W") )
}

function isDialDigitChar (c)
{   return ( ((c >= "0") && (c <= "9")) || (c == "*") || (c == "#" ) )
}

function isFilenameChar (c)
{   return ( ((c >= "0") && (c <= "9")) || ((c >= "a") && (c <= "z")) || ((c >= "A") && (c <= "Z")) || (c == "_") || (c == "-") )
}


// this will display a message, select the content of the relevent field and
// then set the focus to that field.  finally return FALSE to the 'onsubmit' event
// NOTE: <select> boxes do not support the .select method, therefore you cannot
// use this function on any <select> elements
function warnInvalid (theField, s) {
    theField.focus();
    theField.select();
    alert(s);
    return false;
}

function checkIVRGOTO(theForm) {
    var bad = "false";
    var whichitem = 0;
    defaultEmptyOK = false;

    $dialgoto = theForm.dial_args999.value;
    while (whichitem < theForm.goto_indicate999.length) {
        if (theForm.goto_indicate999[whichitem].checked) {
            theForm.goto999.value=theForm.goto_indicate999[whichitem].value;
        }
        whichitem++;
    }

    var gotoType = theForm.elements[ "goto999" ].value;
    if (gotoType == 'custom') {
        var gotoVal = theForm.elements[ "custom_args999"].value;
        if (gotoVal.indexOf('custom') == -1) {
            bad = "true";
            <?php echo "alert('"._("Custom Goto contexts must contain the string \"custom\".  ie: custom-app,s,1")."')"?>;
        }
    }

        if (gotoType == 'dial') {
        if ($dialgoto == "") {

                bad="true";
                <?php echo "alert('"._("Custom number to dial must not be blank")."')"?>;

        } else if (!$dialgoto.match('^[0-9]+$')) {

                bad="true";
                <?php echo "alert('"._("Custom number to dial only contain numbers")."')"?>;

        }
    }

    if (bad == "false") {
        theForm.submit();
    }

}
