<script language="JavaScript">

function deleteCheck(f2) {

    cancel = false;
    ok = true;

    if (confirm("Are you sure to delete this Extension?"))
        return ! cancel;
    else
        return ! ok;
}

</script>

<?php

$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_sip_conf_from_mysql.pl';
$wIaxScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_iax_conf_from_mysql.pl';
$wZapScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_zap_conf_from_mysql.pl';
$wScript1 = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_extensions_from_mysql.pl';
$wOpScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_op_conf_from_mysql.pl';

$dispnum = 3;

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$extdisplay= isset($_REQUEST['extdisplay'])?$_REQUEST['extdisplay']:'';
$account = isset($_REQUEST['account'])?$_REQUEST['account']:'';
$account_end = isset($_REQUEST['account_end'])?$_REQUEST['account_end']:'';
$goto = isset($_REQUEST['goto0'])?$_REQUEST['goto0']:'';

$skip = $_REQUEST['skip'];
$perpage = $_REQUEST['perpage'];
if ($skip == "") $skip = 0;
if ($perpage == "") $perpage = 30;
$skipped = 0;
$index = 0;
$pagerz = 0;
$jumpto = 0;

$uservm = getVoicemail();
$vmcontexts = array_keys($uservm);
$vm = 0;

foreach ($vmcontexts as $vmcontext) {
    if(isset($extdisplay) && isset($uservm[$vmcontext][$extdisplay])){
        $incontext = $vmcontext;
        $vmpwd = $uservm[$vmcontext][$extdisplay]['pwd'];
        $name = $uservm[$vmcontext][$extdisplay]['name'];
        $email = $uservm[$vmcontext][$extdisplay]['email'];
        $pager = $uservm[$vmcontext][$extdisplay]['pager'];

        $options="";
        if (is_array($uservm[$vmcontext][$extdisplay]['options'])) {
            $alloptions = array_keys($uservm[$vmcontext][$extdisplay]['options']);
            if (isset($alloptions)) {
                foreach ($alloptions as $option) {
                    if ( ($option!="attach") && ($option!="envelope") && ($option!="saycid") && ($option!="delete") && ($option!="nextaftercmd") && ($option!='') )
                        $options .= $option.'='.$uservm[$vmcontext][$extdisplay]['options'][$option].'|';
                }
                $options = rtrim($options,'|');
                $options = rtrim($options,'=');

            }
            extract($uservm[$vmcontext][$extdisplay]['options'], EXTR_PREFIX_ALL, "vmops");
        }
        $vm=1;
    }
}

$vmcontext = 'default';

    if ($action == 'add') {

        $callerid = '"'.$_REQUEST['name'].'" '.'<'.$account.'>';

        if ($_REQUEST['tech'] == 'iax2') {
            $erriax = addiax($account,$callerid,$action);
            $hint = "IAX2/".$account;
        } else if ($_REQUEST['tech'] == 'sip') {
            $errsip = addsip($account,$callerid,$action);
            $hint = "SIP/".$account;
        } else {
            $errzap = addzap($account,$callerid,$action);
            $hint = ($_REQUEST['channel'])?("Zap/".$_REQUEST['channel']):'';
        }

        if ($errsip != false || $erriax != false || $errzap != false) {

        exec($wScript);
        exec($wIaxScript);
        exec($wZapScript);
        exec($wOpScript);

        if ($_REQUEST['vm'] == 'enabled')

        {
            $vmoption = explode("=",$_REQUEST['attach']);
                $vmoptions[$vmoption[0]] = $vmoption[1];
            $vmoption = explode("=",$_REQUEST['saycid']);
                $vmoptions[$vmoption[0]] = $vmoption[1];
            $vmoption = explode("=",$_REQUEST['envelope']);
                $vmoptions[$vmoption[0]] = $vmoption[1];
            $vmoption = explode("=",$_REQUEST['delete']);
                $vmoptions[$vmoption[0]] = $vmoption[1];
            $vmoption = explode("=",$_REQUEST['nextaftercmd']);
                $vmoptions[$vmoption[0]] = $vmoption[1];
            $uservm[$vmcontext][$account] = array(
                                        'mailbox' => $account,
                                        'pwd' => $_REQUEST['vmpwd'],
                                        'name' => $_REQUEST['name'],
                                        'email' => $_REQUEST['email'],
                                        'pager' => $_REQUEST['pager'],
                                        'options' => $vmoptions);
            saveVoicemail($uservm);

            $mailb = ($_REQUEST['vm'] == 'disabled' || $_REQUEST['mailbox'] == '') ? 'novm' : $_REQUEST['mailbox'];
            addaccount($account,$mailb,$hint);
        }

        if ($_REQUEST['vm'] == 'disabled')

        {

            $mailb = ($_REQUEST['vm'] == 'disabled' || $_REQUEST['mailbox'] == '') ? 'novm' : $_REQUEST['mailbox'];
            addaccount($account,$mailb,$hint);

        }

        if ($_REQUEST['vm'] == 'destination')

        {

            $mailb = "jump";
            $gotojumpto = setGotoJumpTo($goto,0);
            addaccountjump($account,$mailb,$hint,$gotojumpto);

        }

        exec($wScript1);
        needreload();
        setrecordingstatus($account, "In", $_REQUEST['record_in']);
        setrecordingstatus($account, "Out", $_REQUEST['record_out']);
        
        setnocallstatus($account, $_REQUEST['nocall'], 'NOCALL');
        setnocallstatus($account, $_REQUEST['allowcall'], 'ALLOWCALL');

        setrobstatus($account, $_REQUEST['rob'],'write');
        setcwstatus($account, $_REQUEST['cw'],'write');
        
        $account='';
        $email='';
        $pager='';
        $name='';

        }

    }

    if ($action == 'delete') {

        delExten($extdisplay,"deletefromextension");

        exec($wScript);
        exec($wIaxScript);
        exec($wZapScript);
        exec($wOpScript);

        unset($uservm[$incontext][$extdisplay]);
        saveVoicemail($uservm);

        $result = delextensions('ext-local',$extdisplay);

        exec($wScript1);

        needreload();
        deleteastdb($extdisplay);
        rmdirr("/var/spool/asterisk/vm/$extdisplay");

    }

    if ($action == 'advEdit') {

        $callerid = '"'.$_REQUEST['cidname'].'" '.'<'.$account.'>';


            delExten($account);

            if ($_REQUEST['tech'] == 'iax2') {
                addiax($account,$callerid);
            } else if ($_REQUEST['tech'] == 'sip') {
                addsip($account,$callerid);
            } else {
                addzap($account,$callerid);
            }

        exec($wScript);
        exec($wIaxScript);
        exec($wZapScript);
        exec($wOpScript);

        if ($_REQUEST['vm'] == 'disabled') {

            unset($uservm[$incontext][$account]);
            rmdirr("/var/spool/asterisk/vm/$extdisplay");

            saveVoicemail($uservm);

            $mailb = ($_REQUEST['vm'] == 'disabled' || $_REQUEST['mailbox'] == '') ? 'novm' : $_REQUEST['mailbox'];
            $sql = "UPDATE `extensions` SET `args` = 'exten-vm,".$mailb.",".$account."' WHERE `context` = 'ext-local' AND `extension` = '".$account."' AND `priority` = '1' LIMIT 1 ;";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage());
            }

        }

        if ($_REQUEST['vm'] == 'enabled') {

            unset($uservm[$incontext][$account]);

            if ($_REQUEST['options']!=''){
                $options = explode("|",$_REQUEST['options']);
                foreach($options as $option) {
                    $vmoption = explode("=",$option);
                    $vmoptions[$vmoption[0]] = $vmoption[1];
                }
            }
            $vmoption = explode("=",$_REQUEST['attach']);
                $vmoptions[$vmoption[0]] = $vmoption[1];
            $vmoption = explode("=",$_REQUEST['saycid']);
                $vmoptions[$vmoption[0]] = $vmoption[1];
            $vmoption = explode("=",$_REQUEST['envelope']);
                $vmoptions[$vmoption[0]] = $vmoption[1];
            $vmoption = explode("=",$_REQUEST['delete']);
                $vmoptions[$vmoption[0]] = $vmoption[1];
            $vmoption = explode("=",$_REQUEST['nextaftercmd']);
                $vmoptions[$vmoption[0]] = $vmoption[1];
            $uservm[$vmcontext][$account] = array(
                                        'mailbox' => $account,
                                        'pwd' => $_REQUEST['vmpwd'],
                                        'name' => $_REQUEST['name'],
                                        'email' => $_REQUEST['email'],
                                        'pager' => $_REQUEST['pager'],
                                        'options' => $vmoptions);

            saveVoicemail($uservm);

            $mailb = ($_REQUEST['vm'] == 'disabled' || $_REQUEST['mailbox'] == '') ? 'novm' : $_REQUEST['mailbox'];
            $sql = "UPDATE `extensions` SET `args` = 'exten-vm,".$mailb.",".$account."' WHERE `context` = 'ext-local' AND `extension` = '".$account."' AND `priority` = '1' LIMIT 1 ;";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage());
            }
        }

        if ($_REQUEST['vm'] == 'destination') {

            $mailb = "jump";
            $gotojumpto = setGotoJumpTo($goto,0);
            $sql = "UPDATE `extensions` SET `args` = 'exten-vm,".$mailb.",".$account.','.$gotojumpto."' WHERE `context` = 'ext-local' AND `extension` = '".$account."' AND `priority` = '1' LIMIT 1 ;";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage());
            }

        }

        exec($wScript1);
        needreload();
        setrecordingstatus($account, "In", $_REQUEST['record_in']);
        setrecordingstatus($account, "Out", $_REQUEST['record_out']);

        setnocallstatus($account, $_REQUEST['nocall'], "NOCALL");
        setnocallstatus($account, $_REQUEST['allowcall'], "ALLOWCALL");

        setrobstatus($account, $_REQUEST['rob'],'write');
        setcwstatus($account, $_REQUEST['cw'],'write');

        $options=$options[0];
        if (is_array($vmoptions))
            extract($vmoptions, EXTR_PREFIX_ALL, "vmops");
        $vmpwd=$_REQUEST['vmpwd'];
        $email=$_REQUEST['email'];
        $pager=$_REQUEST['pager'];
        $name=$_REQUEST['name'];

    }

    $uservm = getVoicemail();
    $vmcontexts = array_keys($uservm);
    $vm = 0;
    foreach ($vmcontexts as $vmcontext) {
            if(isset($extdisplay) && isset($uservm[$vmcontext][$extdisplay])){
                    $incontext = $vmcontext;
                    $vmpwd = $uservm[$vmcontext][$extdisplay]['pwd'];
                    $name = $uservm[$vmcontext][$extdisplay]['name'];
                    $email = $uservm[$vmcontext][$extdisplay]['email'];
                    $pager = $uservm[$vmcontext][$extdisplay]['pager'];

                    if (is_array($uservm[$vmcontext][$extdisplay]['options'])) {
                            $alloptions = array_keys($uservm[$vmcontext][$extdisplay]['options']);
                            if (isset($alloptions)) {
                                    foreach ($alloptions as $option) {
                                            if ( ($option!="attach") && ($option!="envelope") && ($option!="saycid") && ($option!="delete") && ($option!="nextaftercmd") && ($option!='') )
                                                    $options .= $option.'='.$uservm[$vmcontext][$extdisplay]['options'][$option].'|';
                                    }
                                    $options = rtrim($options,'|');
                                    $options = rtrim($options,'=');

                            }
                            extract($uservm[$vmcontext][$extdisplay]['options'], EXTR_PREFIX_ALL, "vmops");
                    }
                    $vm=1;
            }
    }

    $checkjumpto = getargs($extdisplay,1,'ext-local');

    if  (strpos($checkjumpto,'jump') == true) {

        $vm = 2;

    }

?>
</div>

<div class="rnav" style="width:225px;">
    <li><a id="<?php  echo ($extdisplay=='' ? 'current':'') ?>" href="config.php?mode=pbx&display=<?php echo $dispnum?>" onFocus="this.blur()"><?php echo _("Add Extension")?></a></li>
<?php

$results = getextens();
$vmcontext = "default";

if (isset($results)) {

        foreach ($results AS $key=>$result) {
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

            $cidname = explode('"',$result[1]);

        echo "<li><a id=\"".($extdisplay==$result[0] ? 'current':'')."\" title=\"$cidname[1]\" href=\"config.php?mode=pbx&display=".$dispnum."&extdisplay={$result[0]}&skip=$skip\" onFocus=\"this.blur()\">".(substr($cidname[1],0,22))." <{$result[0]}></a></li>";

    }
}

if  ($pagerz == 1){

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
switch($extdisplay) {
    default:

        if ($_REQUEST['action'] == 'delete') {
            echo '<br><h3>Extension '.$extdisplay.' Deleted!</h3>';
        } else {

        $thisExten = exteninfo($extdisplay);

        $delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&mode=pbx&action=delete';
    ?>

        <h3>Extension: <?php  echo $extdisplay ?> (
            <?php
            foreach ($thisExten as $result) {
                if ($result[1] == 'tech') {
                    echo '<span style="text-transform:uppercase;">'.$result[2].'</span>';
                    $tech = $result[2];
                }
            }
            ?>
        )</h3>
        <p><a href="<?php  echo $delURL ?>" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete Extension")." ".$extdisplay; ?></a></p>

        <form autocomplete="off" name="advEdit" action="<?php  $_SERVER['PHP_SELF'].'&mode=pbx' ?>" method="post">
        <input type="hidden" name="display" value="<?php echo $dispnum?>">
        <input type="hidden" name="action" value="advEdit">
        <input type="hidden" name="tech" value="<?php echo $tech?>"/>
        <input type="hidden" name="account" value="<?php  echo $extdisplay ?>">
        <input type="hidden" name="skip" value="<?php echo $skip ?>">
        <p>

        <table>
                <tr><td colspan="2"><h5><?php echo _("Account Settings")?>:</h5></td></tr>

<?php



foreach ($thisExten as $result) {

                if ($result[1] != 'account' && $result[1] != 'tech') {
// SIP
                    if ($result[1] == '1outcid') { $outcid = $result[2]; }
                    if ($result[1] == 'account') { $account = $result[2]; }
                    if ($result[1] == 'accountcode') { $accountcode = $result[2]; }
                    if ($result[1] == 'allow') { $allow = $result[2]; }
                    if ($result[1] == 'allowcall') { $allowcall = $result[2]; }
                    if ($result[1] == 'callerid') { $cidname = explode('"',$result[2]); $cidnum = $result[0]; }
                    if ($result[1] == 'callgroup') { $callgroup = $result[2]; }
                    if ($result[1] == 'canreinvite') { $canreinvite = $result[2]; }
                    if ($result[1] == 'context') { $context = $result[2]; }
                    if ($result[1] == 'disallow') { $disallow = $result[2]; }
                    if ($result[1] == 'dtmfmode') { $dtmfmode = $result[2]; }
                    if ($result[1] == 'host') { $host = $result[2]; }
                    if ($result[1] == 'mailbox') { $mailbox = $result[2]; }
                    if ($result[1] == 'nat') { $nat = $result[2]; }
                    if ($result[1] == 'nocall') { $nocall = $result[2]; }
                    if ($result[1] == 'pickupgroup') { $pickupgroup = $result[2]; }
                    if ($result[1] == 'port') { $port = $result[2]; }
                    if ($result[1] == 'qualify') { $qualify = $result[2]; }
                    if ($result[1] == 'cw') { $cw = $result[2]; }
                    if ($result[1] == 'rob') { $rob = $result[2]; }
                    if ($result[1] == 'record_in') { $record_in = $result[2]; }
                    if ($result[1] == 'record_out') { $record_out = $result[2]; }
                    if ($result[1] == 'secret') { $secret = $result[2]; }
                    if ($result[1] == 'subscribecontext') { $subscribecontext = $result[2]; }
                    if ($result[1] == 'type') { $type = $result[2]; }
                    if ($result[1] == 'username') { $username = $result[2]; }
                    if ($result[1] == 'allowsubscribe') { $allowsubscribe = $result[2]; }
                    if ($result[1] == 'call-limit') { $calllimit = $result[2]; }
                    if ($result[1] == 'videosupport') { $videosupport = $result[2]; }
                    if ($result[1] == 't38pt_udptl') { $t38pt_udptl = $result[2]; }
                    if ($result[1] == 'language') { $language = $result[2]; }
// IAX2
                    if ($result[1] == 'transfer') { $transfer = $result[2]; }
// ZAP
                    if ($result[1] == 'busycount') { $busycount = $result[2]; }
                    if ($result[1] == 'busydetect') { $busydetect = $result[2]; }
                    if ($result[1] == 'callprogress') { $callprogress = $result[2]; }
                    if ($result[1] == 'callreturn') { $callreturn = $result[2]; }
                    if ($result[1] == 'callwaiting') { $callwaiting = $result[2]; }
                    if ($result[1] == 'callwaitingcallerid') { $callwaitingcallerid = $result[2]; }
                    if ($result[1] == 'cancallforward') { $cancallforward = $result[2]; }
                    if ($result[1] == 'channel') { $channel = $result[2]; }
                    if ($result[1] == 'echocancel') { $echocancel = $result[2]; }
                    if ($result[1] == 'echocancelwhenbridged') { $echocancelwhenbridged = $result[2]; }
                    if ($result[1] == 'echotraining') { $echotraining = $result[2]; }
                    if ($result[1] == 'relaxdtmf') { $relaxdtmf = $result[2]; }
                    if ($result[1] == 'restrictcid') { $restrictcid = $result[2]; }
                    if ($result[1] == 'signalling') { $signalling = $result[2]; }
                    if ($result[1] == 'threewaycalling') { $threewaycalling = $result[2]; }
                    if ($result[1] == 'transfer') { $transfer = $result[2]; }

                }
}

$ringtime = getringtime($extdisplay);
$vmphone = checkVoiceMailManager($extdisplay);
$record_in_db = checkRecordingINManager($extdisplay);

if ($record_in != $record_in_db and $record_in != "") {

    $record_in_color = "#FF0000";
    $record_in = $record_in_db;
    
    } else if ($record_in == "") {
    
        $cwcolor = "#FFFFFF";
        $record_in = $record_in_db;
        
    } else {
    
            $cwcolor = "#FFFFFF";

}

$record_out_db = checkRecordingOUTManager($extdisplay);

if ($record_out != $record_out_db and $record_out != "") {

    $record_out_color = "#FF0000";
    $record_out = $record_out_db; 
 
    } else if ($record_out == "") {
    
        $cwcolor = "#FFFFFF";
        $record_out = $record_out_db;
        
    } else {
    
            $cwcolor = "#FFFFFF";

}

$cw_db = setcwstatus($extdisplay,null,'read');

if ($cw != $cw_db and $cw != "") {

    $cwcolor = "#FF0000";
    $cw = $cw_db;
    
    } else if ($cw == "") {
    
        $cwcolor = "#FFFFFF";
        $cw = $cw_db;
        
    } else {
    
            $cwcolor = "#FFFFFF";

}

$rob_db = setrobstatus($extdisplay,null,'read');

if ($rob != $rob_db and $rob != "") {

    $robcolor = "#FF0000";
    $rob = $rob_db;
    
    } else if ($rob == "") {
    
        $cwcolor = "#FFFFFF";
        $rob = $rob_db;
        
    } else {
    
            $cwcolor = "#FFFFFF";

}

if ($tech == 'sip') {

        echo '<tr><td align="left" width="150"><a href="#" class="info">'._("Outbound Callerid").'<span>'._("Overrides the caller id when dialing out a trunk. Any setting here will override the common outbound caller id set in the Trunks admin.<br><br>Format: <b>caller name &lt;#######&gt;</b><br><br>Leave this field blank to disable the outbound callerid feature for this extension.").'<br></span></a>:</td>';
        echo '<td><input type="text" size="27" name="outcid" value="'.htmlentities($outcid).'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Username:</td><td><input type="text" size="15" name="username" value="'.$username.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Password:</td><td><input type="password" size="15" name="secret" value="'.$secret.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">MailBox:</td><td><input type="text" size="27" name="mailbox" value="'.$mailbox.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Accountcode:</td><td><input type="text" size="27" maxlength="9" name="accountcode" value="'.$accountcode.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Codec (Disallow):</td><td><input type="text" size="27" name="disallow" value="'.$disallow.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Codec (Allow):</td><td><input type="text" size="27" name="allow" value="'.$allow.'"></td>';
        echo '</tr>';

        echo '<tr><td align="left"><a href="#" class="info">'._("Call (Disallow)").'<span>'._("
<strong>0</strong> for disable all local prefix.<br>
<strong>1-8</strong> for disable prefix with special rate.<br>
<strong>3</strong> for disable prefix of mobile phone.<br>
<strong>4</strong> for disable prefix with special rate.<br>
<strong>7</strong> for disable internet ISP calls.<br><br>
Numbers <strong>2</strong> - <strong>5</strong> - <strong>6</strong> - <strong>9</strong> they are reserved for future requirements.<br><br>
Example: <strong>0;3;7</strong>. Example:<strong>040;347;800</strong>.<br>").'</span></a>:</td>';
        echo '<td><input type="text" size="27" name="nocall" value="'.$nocall.'"></td>';
        echo '</tr>';

        echo '<tr><td align="left"><a href="#" class="info">'._("Call (Allow)").'<span>'._("
<strong>0</strong> for enabled all local prefix.<br>
<strong>1-8</strong> for enabled prefix with special rate.<br>
<strong>3</strong> for enabled prefix of mobile phone.<br>
<strong>4</strong> for enabled prefix with special rate.<br>
<strong>7</strong> for enabled internet ISP calls.<br><br>
Numbers <strong>2</strong> - <strong>5</strong> - <strong>6</strong> - <strong>9</strong> they are reserved for future requirements.<br><br>
Example: <strong>0;3;7</strong>. Example:<strong>040;347;800</strong>.<br>").'</span></a>:</td>';
        echo '<td><input type="text" size="27" name="allowcall" value="'.$allowcall.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">CallerID Name:</td><td><input type="text" size="27" name="cidname" value="'.$cidname[1].'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">CallerID Num:</td><td><input type="text" size="15" name="cidnum" value="'.$cidnum.'" disabled></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Callgroup:</td><td><input type="text" size="15" name="callgroup" value="'.$callgroup.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">PickupGroup:</td><td><input type="text" size="15" name="pickupgroup" value="'.$pickupgroup.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">CanReinvite:</td><td>&nbsp;&nbsp;<select name="canreinvite" size="1">';

        echo '<option value="yes"';
        if ($canreinvite == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($canreinvite == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Context:</td><td><input type="text" size="27" name="context" value="'.$context.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">DTMF Mode:</td><td>&nbsp;&nbsp;<select name="dtmfmode" size="1">';

        echo '<option value="rfc2833"';
        if ($dtmfmode == 'rfc2833') { echo 'selected'; }
        echo '>rfc2833</option>';

        echo '<option value="inband"';
        if ($dtmfmode == 'inband') { echo 'selected'; }
        echo '>inband</option>';

        echo '<option value="info"';
        if ($dtmfmode == 'info') { echo 'selected'; }
        echo '>info</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Host:</td><td><input type="text" size="20" name="host" value="'.$host.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">NAT:</td><td>&nbsp;&nbsp;<select name="nat" size="1">';

        echo '<option value="no"';
        if ($nat == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '<option value="yes"';
        if ($nat == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="never"';
        if ($nat == 'never') { echo 'selected'; }
        echo '>Never</option>';
        
        echo '<option value="route"';
        if ($nat == 'route') { echo 'selected'; }
        echo '>Route</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">SIP Port:</td><td><input type="text" size="5" name="port" value="'.$port.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left"><a href="#" class="info">Qualify<span>If you turn on qualify, Asterisk will send a SIP OPTIONS command regularly to check that the device is still online. If you leave empty Qualify is Disabled.<br></span></a>:</td><td><input id="qualify" type="text" size="5" name="qualify" value="'.$qualify.'"';

        echo ($type == 'user' ? 'disabled': '');

        echo ' > <b>MilliSeconds</b></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">SubscribeContext:</td><td><input type="text" size="27" name="subscribecontext" value="'.$subscribecontext.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Type:</td><td>&nbsp;&nbsp;<select name="type" size="1" onchange="checkQualify(advEdit);">';

        echo '<option value="friend"';
        if ($type == 'friend') { echo 'selected'; }
        echo '>Friend</option>';

        echo '<option value="user"';
        if ($type == 'user') { echo 'selected'; }
        echo '>User</option>';

        echo '<option value="peer"';
        if ($type == 'peer') { echo 'selected'; }
        echo '>Peer</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Allow Subscribe:</td><td>&nbsp;&nbsp;<select name="allowsubscribe" size="1">';

        echo '<option value="yes"';
        if ($allowsubscribe == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($allowsubscribe == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<td align="left">Call Limit:</td><td><input type="text" size="2" maxlength="2" name="calllimit" value="'.$calllimit.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Video Support:</td><td>&nbsp;&nbsp;<select name="videosupport" size="1">';

        echo '<option value="yes"';
        if ($videosupport == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($videosupport == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">T.38 Passthrough:</td><td>&nbsp;&nbsp;<select name="t38pt_udptl" size="1">';

        echo '<option value="yes"';
        if ($t38pt_udptl == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($t38pt_udptl == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Language:</td><td>&nbsp;&nbsp;<select name="language" size="1">';

        echo '<option value="en"';
        if ($language == 'en') { echo 'selected'; }
        echo '>English</option>';

        echo '<option value="it"';
        if ($language == 'it') { echo 'selected'; }
        echo '>Italian</option>';

        echo '</select></td>';
        echo '</tr>';

}

            if ($tech == 'iax2') {

        echo '<tr><td align="left" width="150"><a href="#" class="info">'._("Outbound Callerid").'<span>'._("Overrides the caller id when dialing out a trunk. Any setting here will override the common outbound caller id set in the Trunks admin.<br><br>Format: <b>caller name &lt;#######&gt;</b><br><br>Leave this field blank to disable the outbound callerid feature for this extension.").'<br></span></a>:</td>';
        echo '<td><input type="text" size="27" name="outcid" value="'.htmlentities($outcid).'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Username:</td><td><input type="text" size="15" name="username" value="'.$username.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Password:</td><td><input type="password" size="15" name="secret" value="'.$secret.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">MailBox:</td><td><input type="text" size="27" name="mailbox" value="'.$mailbox.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Accountcode:</td><td><input type="text" size="27" maxlength="9" name="accountcode" value="'.$accountcode.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Codec (Allow):</td><td><input type="text" size="27" name="allow" value="'.$allow.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Codec (Disallow):</td><td><input type="text" size="27" name="disallow" value="'.$disallow.'"></td>';
        echo '</tr>';

        echo '<tr><td align="left"><a href="#" class="info">'._("Call (Allow)").'<span>'._("
<strong>0</strong> for enabled all local prefix.<br>
<strong>1-8</strong> for enabled prefix with special rate.<br>
<strong>3</strong> for enabled prefix of mobile phone.<br>
<strong>4</strong> for enabled prefix with special rate.<br>
<strong>7</strong> for enabled internet ISP calls.<br><br>
Numbers <strong>2</strong> - <strong>5</strong> - <strong>6</strong> - <strong>9</strong> they are reserved for future requirements.<br><br>
Example: <strong>0;3;7</strong>. Example:<strong>040;347;800</strong>.<br>").'</span></a>:</td>';
        echo '<td><input type="text" size="27" name="allowcall" value="'.$allowcall.'"></td>';
        echo '</tr>';

        echo '<tr><td align="left"><a href="#" class="info">'._("Call (Disallow)").'<span>'._("
<strong>0</strong> for disable all local prefix.<br>
<strong>1-8</strong> for disable prefix with special rate.<br>
<strong>3</strong> for disable prefix of mobile phone.<br>
<strong>4</strong> for disable prefix with special rate.<br>
<strong>7</strong> for disable internet ISP calls.<br><br>
Numbers <strong>2</strong> - <strong>5</strong> - <strong>6</strong> - <strong>9</strong> they are reserved for future requirements.<br><br>
Example: <strong>0;3;7</strong>. Example:<strong>040;347;800</strong>.<br>").'</span></a>:</td>';
        echo '<td><input type="text" size="27" name="nocall" value="'.$nocall.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">CallerID Name:</td><td><input type="text" size="27" name="cidname" value="'.$cidname[1].'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">CallerID Num:</td><td><input type="text" size="15" name="cidnum" value="'.$cidnum.'" disabled></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Transfer:</td><td>&nbsp;&nbsp;<select name="transfer" size="1">';

        echo '<option value="yes"';
        if ($notransfer == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($notransfer == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '<option value="mediaonly"';
        if ($notransfer == 'mediaonly') { echo 'selected'; }
        echo '>Mediaonly</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Context:</td><td><input type="text" size="27" name="context" value="'.$context.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Host:</td><td><input type="text" size="20" name="host" value="'.$host.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">IAX2 Port:</td><td><input type="text" size="5" name="port" value="'.$port.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left"><a href="#" class="info">Qualify<span>If you turn on qualify, Asterisk will send a SIP OPTIONS command regularly to check that the device is still online. If you leave empty Qualify is Disabled.<br></span></a>:</td><td><input id="qualify" type="text" size="5" name="qualify" value="'.$qualify.'"';

        echo ($type == 'user' ? 'disabled': '');

        echo ' > <b>MilliSeconds</b></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Type:</td><td>&nbsp;&nbsp;<select name="type" size="1" onchange="checkQualify(advEdit);">';

        echo '<option value="friend"';
        if ($type == 'friend') { echo 'selected'; }
        echo '>Friend</option>';

        echo '<option value="user"';
        if ($type == 'user') { echo 'selected'; }
        echo '>User</option>';

        echo '<option value="peer"';
        if ($type == 'peer') { echo 'selected'; }
        echo '>Peer</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Language:</td><td>&nbsp;&nbsp;<select name="language" size="1">';

        echo '<option value="en"';
        if ($language == 'en') { echo 'selected'; }
        echo '>English</option>';

        echo '<option value="it"';
        if ($language == 'it') { echo 'selected'; }
        echo '>Italian</option>';

        echo '</select></td>';
        echo '</tr>';

}

            if ($tech == 'zap') {

        echo '<tr><td align="left" width="150"><a href="#" class="info">'._("Outbound Callerid").'<span>'._("Overrides the caller id when dialing out a trunk. Any setting here will override the common outbound caller id set in the Trunks admin.<br><br>Format: <b>caller name &lt;#######&gt;</b><br><br>Leave this field blank to disable the outbound callerid feature for this extension.").'<br></span></a>:</td>';
        echo '<td><input type="text" size="27" name="outcid" value="'.htmlentities($outcid).'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Accountcode:</td><td><input type="text" size="27" maxlength="9" name="accountcode" value="'.$accountcode.'"></td>';
        echo '</tr>';

        echo '<tr><td align="left"><a href="#" class="info">'._("Call (Allow)").'<span>'._("
<strong>0</strong> for enabled all local prefix.<br>
<strong>1-8</strong> for enabled prefix with special rate.<br>
<strong>3</strong> for enabled prefix of mobile phone.<br>
<strong>4</strong> for enabled prefix with special rate.<br>
<strong>7</strong> for enabled internet ISP calls.<br><br>
Numbers <strong>2</strong> - <strong>5</strong> - <strong>6</strong> - <strong>9</strong> they are reserved for future requirements.<br><br>
Example: <strong>0;3;7</strong>. Example:<strong>040;347;800</strong>.<br>").'</span></a>:</td>';
        echo '<td><input type="text" size="27" name="allowcall" value="'.$allowcall.'"></td>';
        echo '</tr>';

        echo '<tr><td align="left"><a href="#" class="info">'._("Call (Disallow)").'<span>'._("
<strong>0</strong> for disable all local prefix.<br>
<strong>1-8</strong> for disable prefix with special rate.<br>
<strong>3</strong> for disable prefix of mobile phone.<br>
<strong>4</strong> for disable prefix with special rate.<br>
<strong>7</strong> for disable internet ISP calls.<br><br>
Numbers <strong>2</strong> - <strong>5</strong> - <strong>6</strong> - <strong>9</strong> they are reserved for future requirements.<br><br>
Example: <strong>0;3;7</strong>. Example:<strong>040;347;800</strong>.<br>").'</span></a>:</td>';
        echo '<td><input type="text" size="27" name="nocall" value="'.$nocall.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Busycount:</td><td><input type="text" size="2" name="busycount" value="'.$busycount.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Busydetect:</td><td>&nbsp;&nbsp;<select name="busydetect" size="1">';

        echo '<option value="yes"';
        if ($busydetect == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($busydetect == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">CallerID Name:</td><td><input type="text" size="27" name="cidname" value="'.$cidname[1].'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">CallerID Num:</td><td><input type="text" size="15" name="cidnum" value="'.$cidnum.'" disabled></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Callgroup:</td><td><input type="text" size="15" name="callgroup" value="'.$callgroup.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">PickupGroup:</td><td><input type="text" size="15" name="pickupgroup" value="'.$pickupgroup.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Call Progress:</td><td>&nbsp;&nbsp;<select name="callprogress" size="1">';

        echo '<option value="yes"';
        if ($callprogress == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($callprogress == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Call Return:</td><td>&nbsp;&nbsp;<select name="callreturn" size="1">';

        echo '<option value="yes"';
        if ($callreturn == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($callreturn == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Call Waiting:</td><td>&nbsp;&nbsp;<select name="callwaiting" size="1">';

        echo '<option value="yes"';
        if ($callwaiting == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($callwaiting == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Call WaitingCallerid:</td><td>&nbsp;&nbsp;<select name="callwaitingcallerid" size="1">';

        echo '<option value="yes"';
        if ($callwaitingcallerid == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($callwaitingcallerid == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Cancall Forward:</td><td>&nbsp;&nbsp;<select name="cancallforward" size="1">';

        echo '<option value="yes"';
        if ($cancallforward == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($cancallforward == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Channel:</td><td><input type="text" size="2" name="channel" value="'.$channel.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Context:</td><td><input type="text" size="27" name="context" value="'.$context.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Echo Cancel:</td><td>&nbsp;&nbsp;<select name="echocancel" size="1">';

        echo '<option value="yes"';
        if ($echocancel == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($echocancel == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">EchoCancel Bridged:</td><td>&nbsp;&nbsp;<select name="echocancelwhenbridged" size="1">';

        echo '<option value="yes"';
        if ($echocancelwhenbridged == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($echocancelwhenbridged == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">EchoTraining:</td><td><input type="text" size="4" name="echotraining" value="'.$echotraining.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">MailBox:</td><td><input type="text" size="27" name="mailbox" value="'.$mailbox.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Relax DTMF:</td><td>&nbsp;&nbsp;<select name="relaxdtmf" size="1">';

        echo '<option value="yes"';
        if ($relaxdtmf == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($relaxdtmf == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Restrict CID:</td><td>&nbsp;&nbsp;<select name="restrictcid" size="1">';

        echo '<option value="yes"';
        if ($restrictcid == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($restrictcid == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Signalling:</td><td><input type="text" size="10" name="signalling" value="'.$signalling.'"></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Threeway Calling:</td><td>&nbsp;&nbsp;<select name="threewaycalling" size="1">';

        echo '<option value="yes"';
        if ($threewaycalling == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($threewaycalling == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Transfer:</td><td>&nbsp;&nbsp;<select name="transfer" size="1">';

        echo '<option value="yes"';
        if ($transfer == 'yes') { echo 'selected'; }
        echo '>Yes</option>';

        echo '<option value="no"';
        if ($transfer == 'no') { echo 'selected'; }
        echo '>No</option>';

        echo '</select></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td align="left">Language:</td><td>&nbsp;&nbsp;<select name="language" size="1">';

        echo '<option value="en"';
        if ($language == 'en') { echo 'selected'; }
        echo '>English</option>';

        echo '<option value="it"';
        if ($language == 'it') { echo 'selected'; }
        echo '>Italian</option>';

        echo '</select></td>';
        echo '</tr>';
}

?>
        <tr><td colspan="2"><h5><?php echo _("Extra Features")?>:</h5></td></tr>
        
        <tr bgcolor="<? echo $robcolor; ?>">
            <td align="left"><a href="#" class="info"><?php echo _("Recall on Busy")?><span><?php echo _("Recall on Busy. *61")?><br></span></a>:</td><td>&nbsp;&nbsp;<select name="rob" size="1">
            <option value="Always"
<?
        if ($rob == 'Always') { echo 'selected'; }
        echo '>Yes</option>';
?>
            <option value="Never"
<?
        if ($rob == 'Never' || $rob == '') { echo 'selected'; }
        echo '>No</option>';
?>
            </select></td>
        </tr>

        <tr bgcolor="<? echo $cwcolor; ?>">
            
            <td align="left"><a href="#" class="info"><?php echo _("Call Waiting")?><span><?php echo _("Call Waiting. *70/*71")?><br></span></a>:</td><td>&nbsp;&nbsp;<select name="cw" size="1">
            <option value="Always"
<?
        if ($cw == 'Always') { echo 'selected'; }
        echo '>Yes</option>';
?>
            <option value="Never"
<?
        if ($cw == 'Never' || $cw == '') { echo 'selected'; }
        echo '>No</option>';
?>
            </select></td>
        </tr>

         <tr bgcolor="<? echo $record_in_color; ?>">
            <td align="left"><a href="#" class="info"><?php echo _("Record Incoming")?><span><?php echo _("Record Incoming Record all inbound calls received at this extension. *67/*68")?><br></span></a>:</td><td>&nbsp;&nbsp;<select name="record_in" size="1">
            <option value="Always"
<?
        if ($record_in == 'Always') { echo 'selected'; }
        echo '>Yes</option>';
?>
            <option value="Never"
<?
        if ($record_in == 'Never' || $record_in == '') { echo 'selected'; }
        echo '>No</option>';
?>
            </select></td>
        </tr>       
       
         <tr bgcolor="<? echo $record_out_color; ?>">
            <td align="left"><a href="#" class="info"><?php echo _("Record Outgoing")?><span><?php echo _("Record Incoming Record all inbound calls received at this extension. *67/*68")?><br></span></a>:</td><td>&nbsp;&nbsp;<select name="record_out" size="1">
            <option value="Always"
<?
        if ($record_out == 'Always') { echo 'selected'; }
        echo '>Yes</option>';
?>
            <option value="Never"
<?
        if ($record_out == 'Never' || $record_out == '') { echo 'selected'; }
        echo '>No</option>';
?>
            </select></td>
        </tr>

         <tr>
            <td align="left"><a href="#" class="info"><?php echo _("Ring Time")?><span><?php echo _("Number of seconds to ring phones before sending callers to Voicemail/Destination.<br>If default is set, the General Settings value are used!")?><br></span></a>:</td>
            <td>&nbsp;&nbsp;<select id="ringtime" name="ringtime" <?php echo ($vm == 0 ? 'disabled': '') ?>>
            <?php
                $default = (isset($ringtime) ? $ringtime : 0);
                for ($i=0; $i <= 60; $i+=5) {
                    if ($i == 0)
                            echo '<option value="">'._("Default").'</option>';
                                else
                                echo '<option value="'.$i.'" '.($i == $ringtime ? 'SELECTED' : '').'>'.$i.' Seconds</option>\n';
                }
            ?>
            </select></td>
        </tr> 

            <tr><td colspan=2>
                <h5><br><?php echo _("Voicemail/Destination:")?>&nbsp;
                    <select name="vm" onchange="checkVoicemail(advEdit);">
                        <option value="enabled" <?php echo ($vm == 1 ? 'selected':'') ?>><?php echo _("Voicemail")?></option>
                        <option value="destination" <?php echo ($vm == 2 ? 'selected':'') ?>><?php echo _("Destination")?></option>
                        <option value="disabled" <?php echo ($vm == 0 ? 'selected':'') ?>><?php echo _("Ringing Forever")?></option>
                     </select>
                </h5>
            </td></tr>
                    <?php if ($vmphone == "disabledbyphone") {
                        echo '<tr><td colspan=2 style=color:red;>This Voicemail is Disabled by Phone (*74 for enable)</td></tr>';
                    }?>
            <tr><td colspan=2>
                <table id="voicemail" <?php echo ($vm == 1 ? 'style="display:block;"': 'style="display:none;"') ?>>
                <tr>
                    <td>
                        <a href="#" class="info"><?php echo _("Voicemail password")?><span><?php echo _("This is the password used to access the voicemail system.<br><br>This password can only contain numbers.<br><br>A user can change the password you enter here after logging into the voicemail system (*98) with a phone.")?><br></span></a>:
                    </td><td>
                        <input size="10" type="password" name="vmpwd" value="<?php echo isset($vmpwd)?$vmpwd:'' ?>"/>
                    </td>
                </tr>
                <tr>
                    <td><?php echo _("Full Name:")?></td>
                    <td><input size="25" type="text" name="name" value="<?php echo htmlspecialchars(isset($name)?$name:''); ?>"/></td>
                </tr>
                <tr>
                    <td><a href="#" class="info"><?php echo _("Email Address")?><span><?php echo _("The email address that voicemails are sent to.")?></span></a>: </td>
                    <td><input size="25" type="text" name="email" value="<?php echo htmlspecialchars(isset($email)?$email:''); ?>"/></td>
                </tr>
                <tr>
                    <td><a href="#" class="info"><?php echo _("Pager Email")?><span><?echo _("Pager/mobile email address that short voicemail notifcations are sent to.")?></span></a>: </td>
                    <td><input size="25" type="text" name="pager" value="<?php echo htmlspecialchars(isset($pager)?$pager:''); ?>"/></td>
                </tr>
                <tr>
                    <td><a href="#" class="info"><?php echo _("Email Attachment")?><span><?php echo _("Option to attach voicemails to email.")?></span></a>: </td>
                    <?php if (isset($vmops_attach) && $vmops_attach == "yes"){?>
                    <td><input type="radio" name="attach" value="attach=yes" checked=checked/> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="attach" value="attach=no"/> <?php echo _("no")?></td>
                    <?php } else{ ?>
                    <td><input type="radio" name="attach" value="attach=yes" /> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="attach" value="attach=no" checked=checked /> <?php echo _("no")?></td> <?php }?>
                </tr>

                <tr>
                    <td><a href="#" class="info"><?php echo _("Play CID")?><span><?php echo _("Read back caller's telephone number prior to playing the incoming message, and just after announcing the date and time the message was left.")?></span></a>: </td>
                    <?php if (isset($vmops_saycid) && $vmops_saycid == "yes"){?>
                    <td><input type="radio" name="saycid" value="saycid=yes" checked=checked/> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="saycid" value="saycid=no"/> <?php echo _("no")?></td>
                    <?php } else{ ?>
                    <td><input type="radio" name="saycid" value="saycid=yes" /> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="saycid" value="saycid=no" checked=checked /> <?php echo _("no")?></td> <?php }?>
                </tr>

                <tr>
                    <td><a href="#" class="info"><?php echo _("Play Envelope")?><span><?php echo _("Envelope controls whether or not the voicemail system will play the message envelope (date/time) before playing the voicemail message. This settng does not affect the operation of the envelope option in the advanced voicemail menu.")?></span></a>: </td>
                    <?php if (isset($vmops_envelope) && $vmops_envelope == "yes"){?>
                    <td><input type="radio" name="envelope" value="envelope=yes" checked=checked/> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="envelope" value="envelope=no"/> <?php echo _("no")?></td>
                    <?php } else{ ?>
                    <td><input type="radio" name="envelope" value="envelope=yes" /> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="envelope" value="envelope=no" checked=checked /> <?php echo _("no")?></td> <?php }?>
                </tr>

                <tr>
                    <td><a href="#" class="info"><?php echo _("Play Next")?><span><?php echo _("If set to \"yes,\" after deleting or saving a voicemail message, the system will automatically play the next message, if no the user will have to press \"6\" to go to the next message")?></span></a>: </td>
                    <?php if (isset($vmops_nextaftercmd) && $vmops_nextaftercmd == "yes"){?>
                    <td><input type="radio" name="nextaftercmd" value="nextaftercmd=yes" checked=checked/> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="nextaftercmd" value="nextaftercmd=no"/> <?php echo _("no")?></td>
                    <?php } else{ ?>
                    <td><input type="radio" name="nextaftercmd" value="nextaftercmd=yes" /> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="nextaftercmd" value="nextaftercmd=no" checked=checked /> <?php echo _("no")?></td> <?php }?>
                </tr>

                <tr>
                    <td><a href="#" class="info"><?php echo _("Delete Vmail")?><span><?php echo _("If set to \"yes\" the message will be deleted from the voicemailbox (after having been emailed). Provides functionality that allows a user to receive their voicemail via email alone, rather than having the voicemail able to be retrieved from the Webinterface or the Extension handset.  CAUTION: MUST HAVE email attachment SET TO YES OTHERWISE YOUR MESSAGES WILL BE LOST FOREVER.")?></span></a>: </td>
                    <?php if (isset($vmops_delete) && $vmops_delete == "yes"){?>
                    <td><input type="radio" name="delete" value="delete=yes" checked=checked/> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="delete" value="delete=no"/> <?php echo _("no")?></td>
                    <?php } else{ ?>
                    <td><input type="radio" name="delete" value="delete=yes" /> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="delete" value="delete=no" checked=checked /> <?php echo _("no")?></td> <?php }?>
                </tr>

                <tr>
                    <td><a href="#" class="info"><?php echo _("VM Options")?><span><?php echo _("Separate options with pipe ( | )")?><br><br><?php echo _("ie: review=yes|maxmessage=60")?></span></a>: </td>
                    <td><input size="25" type="text" name="options" value="<?php  echo htmlspecialchars(isset($options)?$options:''); ?>" /></td>
                </tr>
                <tr>
                    <td><?php echo _("VM Context:")?> </td>
                    <td><input size="20" type="text" name="vmcontext" value="<?php  echo $vmcontext; ?>"></td>
                    </tr>
                </table>
            </td></tr>

            <tr><td colspan=2>

            <?php
            $goto = getargs($extdisplay,1,'ext-local');
            echo extdrawselects('advEdit',isset($goto)?$goto:null,0,$vm);

            ?>
            </td></tr>

            <tr>
                <td colspan=2>
                    <br><h6><input name="Submit" type="button" value="<?php echo _("Submit Changes")?>" onclick="javascript:if(advEdit.vm.value=='enabled'&&advEdit.mailbox.value=='') advEdit.mailbox.value=advEdit.account.value+'@'+advEdit.vmcontext.value;checkForm(advEdit)"></h6>
                </td>
            </tr>
            </table>
        </p>
        </form>
<?php
        } //end if action=delete

    break;
    case '':
?>

    <form autocomplete="off" name="addNew" action="<?php  $_SERVER['PHP_SELF'].'&mode=pbx' ?>" method="post">
        <input type="hidden" name="display" value="<?php echo $dispnum?>">
        <input type="hidden" name="action" value="add">
        <h3><?php echo _("Add an Extension:")?></h3>
        <p>
            <table>
            <tr><td colspan=2><h5><?php echo _("Account Settings")?>:</h5></td></tr>
            <tr>
                <td width="145">
                    <a href="#" class="info"><?php echo _("Phone Protocol")?><span><?php echo _("The technology your phone supports")?><br></span></a>:
                </td>
                <td>&nbsp;
                    <select name="tech" onchange="hideExtenFields_Exten(addNew)">
                        <option value="sip">SIP</option>
                        <option value="iax2">IAX2</option>
                        <option value="zap">ZAP (FXs)</option>
                    </select>
                    &nbsp;
                    <select name="dtmfmode" id="dtmfmode">
                        <option value="rfc2833">rfc2833</option>
                        <option value="inband">inband</option>
                        <option value="info">info</option>
                    </select>
                </td>
            </tr>
            </table>
            <table>
            <tr>
                <td width="145">
                    <a href="#" class="info"><?php echo _("Extension Number")?><span><?php echo _('Use a unique number. Valid Range are from:<br>100 to 899<br>1000 to 8999<br>10000 to 89999<br> The device will use this number to authenicate to the system, and users will dial it to ring the device.').' '._('You can not use 0')?></span></a>:
                </td>
                <td>
                    <input tabindex="1" size="9" type="text" name="account" value="<?php  echo ($result[0] == '' ? '200' : ($result[0] + 1))?>"/>
                </td>
            </tr>
            </table>
            <table id="secret" style="display:inline">
            <tr>
                <td width="145">
                <a href="#" class="info"><?php echo _("Extension Password")?><span><?php echo _("The client (phone) uses this password to access the system.<br>This password can contain numbers and letters. Ignored on Zap channels.")?><br></span></a>:
                </td><td>
                    <input tabindex="3" size="15" type="password" name="secret" value="">
                </td>
            </tr>
            </table>
            <table id="channel" style="display:none">
            <tr>
                <td width="145">
                <a href="#" class="info"><?php echo _("Zap Channel")?><span><?php echo _("The zap channel this extension is connected. Ignored on SIP or IAX channels.")?><br></span></a>:
                </td><td>
                    <input tabindex="4" size="4" type="text" name="channel" value=""/>
                </td>
            </tr>
            </table>

            <table>
            <tr>
                <td  width="145"><a href="#" class="info"><?php echo _("Full Name")?><span><?php echo _("User's full name. This is used for the Caller ID Name and for the Company Directory (if enabled below).")?></span></a>: </td>
                <td><input tabindex="5" size="24" type="text" name="name" value="<?php  echo $name; ?>"/></td>
            </tr>

            <tr><td colspan=2><h5><?php echo _("Extra Features")?>:</h5></td></tr>
            <tr>
                <td width="145">
                    <a href="#" class="info"><?php echo _("Recall on Busy")?><span><?php echo _("Recall on Busy. *61")?><br></span></a>:
                </td>
                <td>&nbsp;
                    <select name="rob" size="1">
                        <option value="Always">Yes</option>
                        <option value="Never" selected>No</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="145">
                    <a href="#" class="info"><?php echo _("Call Waiting")?><span><?php echo _("Call Waiting. *70/*71")?><br></span></a>:
                </td>
                <td>&nbsp;
                    <select name="cw" size="1">
                        <option value="Always" selected>Yes</option>
                        <option value="Never">No</option>
                    </select>
                </td>
            </tr>

         <tr>
            <td align="left"><a href="#" class="info"><?php echo _("Record Incoming")?><span><?php echo _("Record Incoming Record all inbound calls received at this extension. *67/*68")?><br></span></a>:</td><td>&nbsp;&nbsp;<select name="record_in" size="1">
            <option value="Always">Yes</option>
            <option value="Never" selected>No</option>
            </select></td>
        </tr>       
       
         <tr>
            <td align="left"><a href="#" class="info"><?php echo _("Record Outgoing")?><span><?php echo _("Record Incoming Record all inbound calls received at this extension. *67/*68")?><br></span></a>:</td><td>&nbsp;&nbsp;<select name="record_out" size="1">
            <option value="Always">Yes</option>
            <option value="Never" selected>No</option>
            </select></td>
        </tr>

         <tr>
            <td align="left"><a href="#" class="info"><?php echo _("Ring Time")?><span><?php echo _("Number of seconds to ring phones before sending callers to Voicemail/Destination.<br>If default is set, the General Settings value are used!")?><br></span></a>:</td>
            <td>&nbsp;&nbsp;<select id="ringtime" name="ringtime"/>
            <?php
                $default = (isset($ringtime) ? $ringtime : 0);
                for ($i=0; $i <= 60; $i+=5) {
                    if ($i == 0)
                            echo '<option value="">'._("Default").'</option>';
                                else
                                echo '<option value="'.$i.'" '.($i == $ringtime ? 'SELECTED' : '').'>'.$i.' Seconds</option>\n';
                }
            ?>
            </select></td>
        </tr> 

            <tr><td width=351 colspan=2>
                <h5><br><?php echo _("Voicemail/Destination:")?>&nbsp;
                    <select name="vm" onchange="checkVoicemail(addNew);">
                        <option value="enabled" selected><?php echo _("Voicemail")?></option>
                        <option value="destination"><?php echo _("Destination")?></option>
                        <option value="disabled"><?php echo _("Ringing Forever")?></option>
                     </select>
                </h5>
            </td></tr>

            </table>

                <table id="voicemail">

                <tr>
                    <td>
                        <a href="#" class="info"><?php echo _("Voicemail Password")?><span><?php echo _("This is the password used to access the voicemail system.<br><br>This password can only contain numbers.<br><br>A user can change the password you enter here after logging into the voicemail system (*98) with a phone.")?><br></span></a>:
                    </td><td>
                        &nbsp;<input tabindex="6" size="10" type="password" name="vmpwd" value=""/>
                    </td>
                </tr>

                <tr>
                    <td><a href="#" class="info"><?php echo _("Email Address")?><span><?php echo _("The email address that voicemails are sent to.")?></span></a>: </td>
                    <td>&nbsp;<input tabindex="7" size="24" type="text" name="email" value="<?php echo htmlspecialchars(isset($email)?$email:''); ?>"/></td>
                </tr>

                <tr>
                    <td><a href="#" class="info"><?php echo _("Pager Email")?><span><?echo _("Pager/mobile email address that short voicemail notifcations are sent to.")?></span></a>: </td>
                    <td>&nbsp;<input tabindex="8" size="24" type="text" name="pager" value="<?php echo htmlspecialchars(isset($pager)?$pager:''); ?>"/></td>
                </tr>

                <tr>
                    <td><a href="#" class="info"><?php echo _("Email Attachment")?><span><?php echo _("Option to attach voicemails to email.")?></span></a>: </td>
                    <?php if (isset($vmops_attach) && $vmops_attach == "yes"){?>
                    <td>&nbsp;<input  tabindex="9" type="radio" name="attach" value="attach=yes" /> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input  tabindex="8" type="radio" name="attach" value="attach=no" checked=checked/> <?php echo _("no")?></td>
                    <?php } else{ ?>
                    <td>&nbsp;<input  tabindex="9" type="radio" name="attach" value="attach=yes" checked=checked/> <?php echo _("yes");?> &nbsp;&nbsp;&nbsp;&nbsp;<input  tabindex="8" type="radio" name="attach" value="attach=no"/> <?php echo _("no")?></td> <?php }?>
                </tr>

                <tr>
                    <td><a href="#" class="info"><?php echo _("Play CID")?><span><?php echo _("Read back caller's telephone number prior to playing the incoming message, and just after announcing the date and time the message was left.")?></span></a>: </td>
                    <?php if (isset($vmops_saycid) && $vmops_saycid == "yes"){?>
                    <td>&nbsp;<input  tabindex="10" type="radio" name="saycid" value="saycid=yes" /> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input  tabindex="10" type="radio" name="saycid" value="saycid=no" checked=checked/> <?php echo _("no")?></td>
                    <?php } else{ ?>
                    <td>&nbsp;<input  tabindex="10" type="radio" name="saycid" value="saycid=yes" checked=checked/> <?php echo _("yes");?> &nbsp;&nbsp;&nbsp;&nbsp;<input  tabindex="10" type="radio" name="saycid" value="saycid=no" /> <?php echo _("no")?></td> <?php }?>
                </tr>

                <tr>
                    <td><a href="#" class="info"><?php echo _("Play Envelope")?><span><?php echo _("Envelope controls whether or not the voicemail system will play the message envelope (date/time) before playing the voicemail message. This settng does not affect the operation of the envelope option in the advanced voicemail menu.")?></span></a>: </td>
                    <?php if (isset($vmops_envelope) && $vmops_envelope == "yes"){?>
                    <td>&nbsp;<input  tabindex="11" type="radio" name="envelope" value="envelope=yes" /> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input  tabindex="12" type="radio" name="envelope" value="envelope=no" checked=checked/> <?php echo _("no")?></td>
                    <?php } else{ ?>
                    <td>&nbsp;<input  tabindex="11" type="radio" name="envelope" value="envelope=yes" checked=checked/> <?php echo _("yes");?> &nbsp;&nbsp;&nbsp;&nbsp;<input  tabindex="12" type="radio" name="envelope" value="envelope=no" /> <?php echo _("no")?></td> <?php }?>
                </tr>

                <tr>
                    <td><a href="#" class="info"><?php echo _("Play Next")?><span><?php echo _("If set to \"yes,\" after deleting or saving a voicemail message, the system will automatically play the next message, if no the user will have to press \"6\" to go to the next message")?></span></a>: </td>
                    <?php if (isset($vmops_nextaftercmd) && $vmops_nextaftercmd == "yes"){?>
                    <td>&nbsp;<input  tabindex="12" type="radio" name="nextaftercmd" value="nextaftercmd=yes" /> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input  tabindex="14" type="radio" name="nextaftercmd" value="nextaftercmd=no" checked=checked/> <?php echo _("no")?></td>
                    <?php } else{ ?>
                    <td>&nbsp;<input  tabindex="12" type="radio" name="nextaftercmd" value="nextaftercmd=yes" checked=checked/> <?php echo _("yes");?> &nbsp;&nbsp;&nbsp;&nbsp;<input  tabindex="14" type="radio" name="nextaftercmd" value="nextaftercmd=no" /> <?php echo _("no")?></td> <?php }?>
                </tr>

                <tr>
                    <td><a href="#" class="info"><?php echo _("Delete Vmail")?><span><?php echo _("If set to \"yes\" the message will be deleted from the voicemailbox (after having been emailed). Provides functionality that allows a user to receive their voicemail via email alone, rather than having the voicemail able to be retrieved from the Webinterface or the Extension handset.  CAUTION: MUST HAVE attach voicemail to email SET TO YES OTHERWISE YOUR MESSAGES WILL BE LOST FOREVER.")?>
                </span></a>: </td>
                    <?php if (isset($vmops_delete) && $vmops_delete == "yes"){?>
                    <td>&nbsp;<input  tabindex="13" type="radio" name="delete" value="delete=yes" checked=checked/> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input  tabindex="16" type="radio" name="delete" value="delete=no"/> <?php echo _("no")?></td>
                    <?php } else{ ?>
                    <td>&nbsp;<input  tabindex="13" type="radio" name="delete" value="delete=yes" /> <?php echo _("yes")?> &nbsp;&nbsp;&nbsp;&nbsp;<input tabindex="16" type="radio" name="delete" value="delete=no" checked=checked /> <?php echo _("no")?></td> <?php }?>
                </tr>

                </table>


            <?php

            echo extdrawselects('addNew',isset($goto)?$goto:null,0,$vm);

            ?>

            <input type="hidden" name="canreinvite" value="no"/>
            <input type="hidden" name="context" value="from-internal"/>
            <input type="hidden" name="host" value="dynamic"/>
            <input type="hidden" name="type" value="friend"/>
            <input type="hidden" name="nat" value="no"/>
            <input type="hidden" name="mailbox" value=""/>
            <input type="hidden" name="username" value=""/>
            <input type="hidden" name="transfer" value="yes"/>
            <input type="hidden" name="qualify" value="500"/>
            <input type="hidden" name="callgroup" value=""/>
            <input type="hidden" name="pickupgroup" value=""/>
            <input type="hidden" name="disallow" value="all"/>
            <input type="hidden" name="allow" value="alaw&ulaw&gsm&g729&ilbc&g726"/>
            <input type="hidden" name="nocall" value=""/>
            <input type="hidden" name="allowcall" value=""/>
            <input type="hidden" name="subscribecontext" value="ext-local"/>
            <input type="hidden" name="vmcontext" value="<?php echo $vmcontext ?>"/>
            <input type="hidden" name="accountcode" value=""/>
            <input type="hidden" name="allowsubscribe" value="yes"/>
            <input type="hidden" name="calllimit" value="99"/>
            <input type="hidden" name="videosupport" value="no"/>
            <input type="hidden" name="t38pt_udptl" value="no"/>
            
        <table>
            <tr>
                <td width=351 colspan=2>
                    <br><h6><input name="Submit" type="button" value="<?php echo _("Add Extension")?>" onclick="javascript:if(addNew.vm.value=='enabled') addNew.mailbox.value=addNew.account.value+'@'+addNew.vmcontext.value;checkForm(addNew)"></h6>
                </td>
            </tr>
        </table>
        </p>
    </form>

<?php
    break;
}
?>
