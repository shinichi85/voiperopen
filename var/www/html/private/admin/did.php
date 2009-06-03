<?php /* $Id: did.php,v 1.20 2005/06/14 00:00:00 rcourtna Exp $ */
//Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
//Copyright (c) 2005-2006 SpheraIT
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

function deleteCheck(f2) {

    cancel = false;
    ok = true;

    if (confirm("Are you sure to delete this Incoming Route?"))
          return ! cancel;
    else
          return ! ok;
}

</script>

<?php

function getIncoming(){
    global $db;
    $sql = "SELECT extension,cidnum,cidname,channel FROM incoming ORDER BY extension,cidnum ASC";
    $results = $db->getAll($sql,DB_FETCHMODE_ASSOC);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }
    return $results;
}

function getIncomingInfo($extension="",$cidnum="",$channel=""){
    global $db;
    $sql = "SELECT * FROM incoming WHERE cidnum = \"$cidnum\" AND extension = \"$extension\" AND channel = \"$channel\"";
    $results = $db->getRow($sql,DB_FETCHMODE_ASSOC);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }
    return $results;
}

function delIncoming($extension,$cidnum,$channel){
    global $db;
    $sql="DELETE FROM incoming WHERE cidnum = \"$cidnum\" AND extension = \"$extension\" AND channel = \"$channel\"";
    $results = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }

    if(empty($extension)) {
        $extension = "s";
        $catchaccount = "_X.".(empty($cidnum)?"":"/".$cidnum);
    }

    $account = $extension.(empty($cidnum)?"":"/".$cidnum);

    $sql="DELETE FROM extensions WHERE context = \"ext-did\" AND extension = \"$account\"";
    $results = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }

    if ($catchaccount) {
        $sql="DELETE FROM extensions WHERE context = \"ext-did\" AND extension = \"$catchaccount\"";
        $results = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage());
        }
    }

    if ($channel) {

        $sql="DELETE FROM extensions WHERE context = \"macro-from-zaptel-{$channel}\" AND extension = \"$account\"";
        $results = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage());
        }
    }

}

function addIncoming($incoming){
    global $db;
    foreach ($incoming as $key => $val) { ${$key} = addslashes($val); } // create variables from request
    $existing=getIncomingInfo($extension,$cidnum,$channel);
    if (empty($existing)) {
        $destination=buildActualGoto($incoming,0);
        $destination_only_numba = explode(",", $destination);
        if ($destination_only_numba[1] == "s") {
            $destination_only_numba = "";
        }
        $alertinfofix = mysql_escape_string($alertinfo);
        $sql= "INSERT INTO incoming (cidnum,extension,destination,faxexten,faxemail,faxemail2,answer,wait,CIDName,privacyman,alertinfo,channel,ringing,addprefix,phonebook) values ('$cidnum','$extension','$destination','$faxexten','$faxemail','$faxemail2','$answer','$wait','$CIDName','$privacyman','$alertinfofix','$channel','$ringing','$ADDPrefix','$phonebook')";
        $results = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage());
        }

        $extension = (empty($extension)?"s":$extension);
        $account = $extension.(empty($cidnum)?"":"/".$cidnum);

        $i=1;
        $catchall = false;

        if (empty($channel)) {
            $context = "ext-did";
        } else {
            $context = "macro-from-zaptel-{$channel}";
            if (!isset($zapchan[$channel])) {

                $addarray[] = array($context,$account,sprintf('%02s',$i++),'NoOp',$context.' with DID = ${DID}',$destination_only_numba[1],'0');
                $zapchan[$channel] = "unfinished";
            }
        }
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'Set','FROM_DID='.$account,$destination_only_numba[1],'0');

        if ($ringing == "CHECKED") {
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'Ringing','',$destination_only_numba[1],'0');
        }
        if ($extension == "s" && $context == "ext-did") {
            $catchaccount = "_X.".(empty($cidnum)?"":"/".$cidnum);
            if ($catchaccount == "_X.") {
                $catchall = true;
                $addarray[] = array($context,$catchaccount,"1",'NoOp','Catch-All DID Match - Found ${EXTEN} - You probably want a DID for this.',$destination_only_numba[1],'0');
                $addarray[] = array($context,$catchaccount,"2",'Goto','ext-did,s,1',$destination_only_numba[1],'0');
            }
        }
        if ($faxexten != "default") {
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'Set','FAX_RX='.$faxexten,$destination_only_numba[1],'0');
        }
        if (!empty($faxemail)) {
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'Set','FAX_RX_EMAIL='.$faxemail,$destination_only_numba[1],'0');
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'Set','FAX_RX_EMAIL2='.$faxemail2,$destination_only_numba[1],'0');
        }
        if ($answer == "1") {
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'Answer','',$destination_only_numba[1],'0');
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'Wait',$wait,$destination_only_numba[1],'0');
        }
        if (!empty($CIDName)) {
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'Set','CALLERID(name)='.$CIDName,$destination_only_numba[1],'0');
        }
        if ($phonebook == "CHECKED") {
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'Macro','inboundphonebook',$destination_only_numba[1],'0');
        }
        if ($ADDPrefix != "") {
            $xcounter = $i;
            $true = $xcounter + 1;
            $false = $xcounter + 2;
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'GotoIf','$[$[${LEN(${CALLERID(num)})} > 2] & $["${CALLERID(num)}" != "anonymous" ] & $["${CALLERID(num)}" != "unknown" ]]?'.$true.':'.$false,$destination_only_numba[1],'0');
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'Set','CALLERID(number)='.$ADDPrefix.'${CALLERID(number)}',$destination_only_numba[1],'0');
        }
        if ($privacyman == "1") {
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'Macro','privacy-mgr',$destination_only_numba[1],'0');
        }
        if (!empty($alertinfo)) {
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'SIPAddHeader','"'.$alertinfo.'"',$destination_only_numba[1],'0');
        }

            $zapchan[$channel] = "set";
            $addarray[] = array($context,$account,sprintf('%02s',$i++),'Goto',$destination,$destination_only_numba[1],'0');

        foreach($addarray as $add) {
            addextensions($add);
        }

    } else {
        echo "<script>javascript:alert('"._("A route for this DID/CID already exists!")."')</script>";
    }
}

$wScript1 = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_extensions_from_mysql.pl';
$wScript2 = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_a2b_from_mysql.pl';
$dispnum = 7;

$skip = $_REQUEST['skip'];
$perpage = $_REQUEST['perpage'];
if ($skip == "") $skip = 0;
if ($perpage == "") $perpage = 30;
$skipped = 0;
$index = 0;
$pagerz = 0;

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$extdisplay= isset($_REQUEST['extdisplay'])?$_REQUEST['extdisplay']:'';
$account = isset($_REQUEST['account'])?$_REQUEST['account']:'';
$goto = isset($_REQUEST['goto0'])?$_REQUEST['goto0']:'';

switch ($action) {
    case 'addIncoming':
        extract($_REQUEST);
        addIncoming($_REQUEST);
        exec($wScript1);
        exec($wScript2);
        needreload();
    break;
    case 'delIncoming':
        $extarray=explode('/',$extdisplay,3);
        delIncoming($extarray[0],$extarray[1],$extarray[2]);
        exec($wScript1);
        exec($wScript2);
        needreload();
    break;
    case 'edtIncoming':
        $extarray=explode('/',$extdisplay,3);
        delIncoming($extarray[0],$extarray[1],$extarray[2]);
        addIncoming($_REQUEST);
        exec($wScript1);
        exec($wScript2);
        needreload();
    break;
}

?>
</div>

<div class="rnav" style="width:210px;">
    <li><a id="<?php echo ($extdisplay=='' ? 'current':'') ?>" href="config.php?mode=pbx&display=<?php echo $dispnum?>" onFocus="this.blur()"><?php echo _("Add Incoming Route")?></a></li>
<?php

$inroutes = getIncoming();
if (isset($inroutes)) {

    foreach ($inroutes as $key=>$inroute) {

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

        $displaydid = ( empty($inroute['extension'])? _("any DID") : $inroute['extension'] );
        $displaycid = ( empty($inroute['cidnum'])? _("any CID") : $inroute['cidnum'] );
        $zapchan = ( strlen($inroute['channel'])? "Zaptel Channel {$inroute['channel']}" : "" );
        if ($zapchan != "")
            echo "<li><a id=\"".($extdisplay==$inroute['extension']."/".$inroute['cidnum']."/".$inroute['channel'] ? 'current':'nul')."\" href=\"config.php?mode=pbx&display=".urlencode($dispnum)."&skip=$skip&amp;extdisplay=".urlencode($inroute['extension'])."/".urlencode($inroute['cidnum'])."/".urlencode($inroute['channel'])."\" onFocus=\"this.blur()\" title=\"{$inroute['cidname']}\">{$zapchan} </a></li>";
        else
            echo "<li><a id=\"".($extdisplay==$inroute['extension']."/".$inroute['cidnum']."/".$inroute['channel'] ? 'current':'nul')."\" href=\"config.php?mode=pbx&display=".urlencode($dispnum)."&skip=$skip&amp;extdisplay=".urlencode($inroute['extension'])."/".urlencode($inroute['cidnum'])."/".urlencode($inroute['channel'])."\" onFocus=\"this.blur()\" title=\"{$inroute['cidname']}\">{$displaydid} / {$displaycid} {$zapchan} </a></li>";

            }
}

if    ($pagerz == 1){

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

    if ($action == 'delDID') {
        echo '<br><h3>Route '.$extdisplay.' deleted!</h3>';
    } else {

        $delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=delIncoming';
?>
<?php if ($extdisplay) {

    $extarray=explode('/',$extdisplay,3);
    $ininfo=getIncomingInfo($extarray[0],$extarray[1],$extarray[2]);
    if (is_array($ininfo)) extract($ininfo);
?>
        <h3><?php echo _("Route")?>: <?php echo $extdisplay; ?></h3>
        <p><a href="<?php echo $delURL ?>" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete Route")?> <?php echo $extdisplay ?> </a></p>
<?php } else { ?>
        <h3><?php echo _("Add Incoming Route:")?></h3>
<?php } ?>
        <form name="editGRP" action="<?php $_SERVER['PHP_SELF'].'&mode=pbx' ?>" method="post">
        <input type="hidden" name="display" value="<?php echo $dispnum?>">
        <input type="hidden" name="action" value="<?php echo ($extdisplay ? 'edtIncoming' : 'addIncoming') ?>">
        <input type="hidden" name="extdisplay" value="<?php echo $extdisplay ?>">
        <input type="hidden" name="skip" value="<?php echo $skip ?>">
        <table>
        <tr><td colspan="2"><h5><?php echo ($extdisplay ? _('Edit Incoming Route') : _('Add Incoming Route')) ?></h5></td></tr>
        <tr>
            <td><a href="#" class="info"><?php echo _("DID Number")?><span><?php echo _('Define the expected DID Number if your trunk passes DID on incoming calls. <br><br>Leave this blank to match calls with any or no DID info.<br><br>You can also use a pattern match (eg _2[345]X) to match a range of numbers')?></span></a>:</td>
            <td><input type="text" size="22" name="extension" value="<?php echo htmlspecialchars(isset($extension)?$extension:''); ?>"></td>
        </tr>
        <tr>
            <td><a href="#" class="info"><?php echo _("Caller ID Number")?><span><?php echo _('Define the Caller ID Number to be matched on incoming calls.<br><br>Leave this field blank to match any or no CID info.')?></span></a>:</td>
            <td><input type="text" size="22" name="cidnum" value="<?php echo htmlspecialchars(isset($cidnum)?$cidnum:'') ?>"></td>
        </tr>
        <tr>
            <td><a href="#" class="info"><?php echo _("Set a CallerID Name")?><span><?php echo _('Define the Caller ID Name.')?></span></a>:</td>
            <td><input type="text" size="22" name="CIDName" value="<?php echo htmlspecialchars(isset($CIDName)?$CIDName:'') ?>"></td>
            </tr>
        <tr>
            <td><a href="#" class="info"><?php echo _("Zaptel Channel")?><span><?php echo _('Match calls that come in on this specific Zaptel channel number. zapata.conf must have "context=from-zaptel" rather than context="from-pstn or from-trunk" to use this feature')?></span></a>:</td>
            <td><input type="text" size="2" name="channel" value="<?php echo htmlspecialchars(isset($channel)?$channel:''); ?>"></td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
        <tr><td colspan="2"><h5><?php echo _("Fax Handling")?></h5></td></tr>
        <tr>
            <td>
                <a class="info" href="#"><?php echo _("Fax Extension")?><span><?php echo _("Select 'system' to have the system receive and email faxes.<br><br>The Default settings are defined in General Settings.")?></span></a>:
            </td>
            <td>
            &nbsp;&nbsp;<select name="faxexten">

<?php

if (!isset($faxexten))
    $faxexten = "disabled";
if (!isset($faxemail))
    $faxemail = null;
if (!isset($faxemail2))
    $faxemail2 = null;

?>
                    <option value="default" <?php  echo ($faxexten == 'default' ? 'SELECTED' : '')?>><?php echo _("default")?></option>
                    <option value="disabled" <?php  echo ($faxexten == 'disabled' ? 'SELECTED' : '')?>><?php echo _("autosense disabled")?></option>
                    <option value="system" <?php  echo ($faxexten == 'system' ? 'SELECTED' : '')?>><?php echo _("system")?></option>
            <?php

                $extens = getextens();
                if (isset($extens)) {
                    foreach ($extens as $exten) {
                        $tech=strtoupper($exten[2]);
                        if($tech == 'ZAP') {
                            echo '<option value="'.$tech.'/${ZAPCHAN_'.$exten[0].'}" '.($faxexten == $tech.'/${ZAPCHAN_'.$exten[0].'}' ? 'SELECTED' : '').'>'._("Extension #").$exten[0]."</option>\n";
                        } else {
                            echo '<option value="'.$tech.'/'.$exten[0].'" '.($faxexten == $tech.'/'.$exten[0] ? 'SELECTED' : '').'>'._("Extension #").$exten[0]."</option>\n";
                        }
                    }
                }
            ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <a class="info" href="#"><?php echo _("Fax Email")?><span><?php echo _("Email address is used if 'system' has been chosen for the fax extension above.<br><br>Leave this blank to use the VMP default in General Settings.")?></span></a>:
            </td>
            <td>
                <input type="text" size="22" name="faxemail" value="<?php echo $faxemail?>"/>
            </td>
        </tr>

        <tr>
            <td>
                <?php echo _("Fax Email (Carbon Copy)")?>:
            </td>
            <td>
                <input type="text" size="22" name="faxemail2" value="<?php echo $faxemail2?>"/>
            </td>
        </tr>

        <tr>
            <td><br></td>
        </tr>
        <tr><td colspan="2"><h5><?php echo _("Options")?></h5></td></tr>
        <tr>
            <td><a href="#" class="info"><?php echo _("Immediate Answer")?><span><?php echo _('Answer calls the moment they detected?  Note: If using a "Fax Extension" (above) you may wish to enable this so that we can listen for a fax tone.')?></span></a>:</td>
            <td>
            &nbsp;&nbsp;<select name="answer">
<?php
if (!isset($answer))
    $answer = '0';
if (!isset($privacyman))
    $privacyman = '0';
if (!isset($alertinfo))
    $alertinfo = '0';
if (!isset($ADDPrefix))
    $ADDPrefix = null;
?>
                    <option value="0" <?php  echo ($answer == '0' ? 'SELECTED' : '')?>><?php echo _("No")?></option>
                    <option value="1" <?php  echo ($answer == '1' ? 'SELECTED' : '')?>><?php echo _("Yes")?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><a href="#" class="info"><?php echo _("Pause after answer")?><span><?php echo _('The number of seconds we should wait after performing an Immediate Answer. The primary purpose of this is to pause and listen for a fax tone before allowing the call to proceed.')?></span></a>:</td>
            <td><input type="text" name="wait" size="1" value="<?php echo isset($wait)?$wait:'0' ?>"></td>
        </tr>
        <tr>
            <td><a href="#" class="info"><?php echo _("Privacy Manager")?><span><?php echo _('If no Caller ID is sent, Privacy Manager will asks the caller to enter their 10 digit phone number. The caller is given 3 attempts.')?></span></a>:</td>
            <td>
            &nbsp;&nbsp;<select name="privacyman">
                    <option value="0" <?php  echo ($privacyman == '0' ? 'SELECTED' : '')?>><?php echo _("No")?></option>
                    <option value="1" <?php  echo ($privacyman == '1' ? 'SELECTED' : '')?>><?php echo _("Yes")?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><a href="#" class="info"><?php echo _("Alert Info")?><span><?php echo _('ALERT_INFO can be used for distinctive ring with SIP devices.')?></span></a>:</td>
            <td><input type="text" name="alertinfo" size="22" value="<?php echo ($alertinfo)?$alertinfo:'' ?>"></td>
        </tr>

        <tr>
            <td><a href="#" class="info"><?php echo _("Signal RINGING")?><span><?php echo _('Some devices or providers require RINGING to be sent before ANSWER. You\'ll notice this happening if you can send calls directly to a phone, but if you send it to an IVR, it won\'t connect the call.')?></span></a>:</td>
            <td><input type="checkbox" name="ringing" value="CHECKED" <?php echo $ringing ?> /></td>
        </tr>

        <tr>
            <td><a href="#" class="info"><?php echo _("Use PhoneBook")?><span><?php echo _('If you Use the Phonebook Function for this Inbound Routing and the CallerID Number is found in the Phonebook, the CallerID Name will be overwritten with a new one. If you use the Set CallerID Name in this Inbound Route will be overwritten Only if a CallerID Number is found.')?></span></a>:</td>
            <td><input type="checkbox" name="phonebook" value="CHECKED" <?php echo $phonebook ?> /></td>
        </tr>

        <tr>
            <td><a href="#" class="info"><?php echo _("Inbound Add Prefix")?><span><?php echo _('Add a Inbound Prefix.')?></span></a>:</td>
            <td><input type="text" name="ADDPrefix" size="1" value="<?php echo $ADDPrefix ?>"></td>
        </tr>

        <tr>
            <td><br></td>
        </tr>

        <tr><td colspan="2"><h5><?php echo _("Set Destination")?></h5></td></tr>

        <tr><td colspan="2">
<?php
        echo drawselects('editGRP',isset($destination)?$destination:null,0,'fixINCOMING','fixFAX','fixCALLBACK','','fixMEETME');
?>
        </td></tr>
        <tr>
        <td colspan="2"><br><h6><input name="Submit" type="button" value="Submit Changes" onclick="checkDID(editGRP);"></h6></td>

        </tr>
        </table>
        </form>
<?php
    }
?>
