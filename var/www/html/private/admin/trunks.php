<?php /* $Id: trunks.php,v 1.19 2005/05/03 19:07:14 gregmac Exp $ */
// routing.php Copyright (C) 2004 Greg MacLellan (greg@mtechsolutions.ca)
// Asterisk Management Portal Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
// New improvment by SpheraIT
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

<?php

$extenScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_extensions_from_mysql.pl';
$sipScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_sip_conf_from_mysql.pl';
$iaxScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_iax_conf_from_mysql.pl';
$wOpScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_op_conf_from_mysql.pl';
$localPrefixFile = "/etc/asterisk/localprefixes.conf";

$display= 6;

$extdisplay=isset($_REQUEST['extdisplay'])?$_REQUEST['extdisplay']:'';
$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$tech = strtolower(isset($_REQUEST['tech'])?$_REQUEST['tech']:'');
$trunknum = ltrim($extdisplay,'OUT_');
$acode = $_REQUEST['permission'];
$outtrunkright= isset($_REQUEST['outtrunkright'])?$_REQUEST['outtrunkright']:'0';

foreach ( $acode as $acodes ) {
    $accountcodefinal .= trim($acodes) ."|";
}

$set_globals = array("outcid","maxchans","dialoutprefix","channelid","peerdetails","usercontext","userconfig","register");
foreach ($set_globals as $var) {
    if (isset($_REQUEST[$var])) {
        $$var = stripslashes( $_REQUEST[$var] );
    }
}

$dialrules = array();
if (isset($_REQUEST["dialrules"])) {
    $dialrules = explode("\n",$_REQUEST["dialrules"]);

    if (!$dialrules) {
        $dialrules = array();
    }

    foreach (array_keys($dialrules) as $key) {

        $dialrules[$key] = trim($dialrules[$key]);
        if ($dialrules[$key] == "") unset($dialrules[$key]);
        if ($dialrules[$key][0] == "_") $dialrules[$key] = substr($dialrules[$key],1);
    }

    $dialrules = array_values(array_unique($dialrules));
} else {
  $dialrules = array();
}

switch ($action) {
    case "addtrunk":
        $trunknum = addTrunk($tech, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register, $dialrules, $accountcodefinal, $outtrunkright);

        addDialRules($trunknum, $dialrules);
        exec($extenScript);
        exec($sipScript);
        exec($iaxScript);
        exec($wOpScript);
        needreload();

        $extdisplay = "OUT_".$trunknum;
    break;
    case "edittrunk":
        editTrunk($trunknum, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register, $accountcodefinal, $outtrunkright);
        addDialRules($trunknum, $dialrules);
        exec($extenScript);
        exec($sipScript);
        exec($iaxScript);
        exec($wOpScript);
        needreload();
    break;
    case "deltrunk":

        deleteTrunk($trunknum);
        deleteDialRules($trunknum);
        exec($extenScript);
        exec($sipScript);
        exec($iaxScript);
        exec($wOpScript);
        needreload();

        $extdisplay = '';
    break;
    case "populatenpanxx":
        if (preg_match("/^([2-9]\d\d)-?([2-9]\d\d)$/", $_REQUEST["npanxx"], $matches)) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, "http://members.dandy.net/~czg/lca_prefix.php?npa=".$matches[1]."&nxx=".$matches[2]."&ocn=&pastdays=0&nextdays=0");
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Voiper Local Trunks Configuration)");
            $str = curl_exec($ch);
            curl_close($ch);

            if (preg_match("/exch=(\d+)/",$str, $matches)) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL, "http://members.dandy.net/~czg/lprefix.php?exch=".$matches[1]);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Voiper Local Trunks Configuration)");
                $str = curl_exec($ch);
                curl_close($ch);

                foreach (explode("\n", $str) as $line) {
                    if (preg_match("/^(\d{3});(\d{3})/", $line, $matches)) {
                        $dialrules[] = "1".$matches[1]."|".$matches[2]."XXXX";

                    }
                }

                $dialrules = array_values(array_unique($dialrules));
            } else {
                $errormsg = _("Error fetching prefix list for: "). $_REQUEST["npanxx"];
            }

        } else {

            $errormsg = _("Invalid format for NPA-NXX code (must be format: NXXNXX)");
        }

        if (isset($errormsg)) {
            echo "<script language=\"javascript\">alert('".addslashes($errormsg)."');</script>";
            unset($errormsg);
        }
    break;
}

$sql = "SELECT * FROM globals";
$globals = $db->getAll($sql);
if(DB::IsError($globals)) {
    die($globals->getMessage());
}

foreach ($globals as $global) {
    ${trim($global[0])} = htmlentities($global[1]);
}

?>
</div>

<div class="rnav">
    <li><a id="<?php  echo ($extdisplay=='' ? 'current':'') ?>" href="config.php?mode=pbx&display=<?php echo urlencode($display)?>" onFocus="this.blur()"><?php echo _("Add Trunk")?></a></li>

<?php

$tresults = gettrunks();

foreach ($tresults as $tresult) {
    echo "<li><a id=\"".($extdisplay==$tresult[0] ? 'current':'')."\" href=\"config.php?mode=pbx&display=".urlencode($display)."&extdisplay=".urlencode($tresult[0])."\" title=\"".$tresult[1]."\" onFocus=\"this.blur()\">"._("Trunk")." ".substr(ltrim($tresult[1],"AMP:"),0,14)."</a></li>";
}

?>
</div>

<div class="content">

<?php

if (!$tech && !$extdisplay) {
?>
    <h3><?php echo _("Add a Trunk:")?></h3>
    <a href="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx&display='.urlencode($display); ?>&tech=ZAP" onFocus="this.blur()"><?php echo _("Add ZAP Trunk")?></a><br><br>
    <a href="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx&display='.urlencode($display); ?>&tech=MISDN" onFocus="this.blur()"><?php echo _("Add MISDN Trunk")?></a><br><br>
    <a href="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx&display='.urlencode($display); ?>&tech=IAX2" onFocus="this.blur()"><?php echo _("Add IAX2 Trunk")?></a><br><br>
    <a href="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx&display='.urlencode($display); ?>&tech=SIP" onFocus="this.blur()"><?php echo _("Add SIP Trunk")?></a><br><br>
    <a href="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx&display='.urlencode($display); ?>&tech=ENUM" onFocus="this.blur()"><?php echo _("Add ENUM Trunk")?></a><br><br>
    <a href="<?php echo $_SERVER['PHP_SELF'].'?mode=pbx&display='.urlencode($display); ?>&tech=CUSTOM" onFocus="this.blur()"><?php echo _("Add Custom Trunk")?></a><br><br>
<?php
} else {
    if ($extdisplay) {

        $tech = getTrunkTech($trunknum);

        $outcid = ${"OUTCID_".$trunknum};
        $maxchans = ${"OUTMAXCHANS_".$trunknum};
        $dialoutprefix = ${"OUTPREFIX_".$trunknum};

        if ($tech!="enum") {

            if (!isset($channelid)) {
                $channelid = getTrunkTrunkName($trunknum);
            }

            if ($tech!="custom") {

                if (!isset($peerdetails)) {
                    $peerdetails = getTrunkPeerDetails($trunknum);
                }

                if (!isset($usercontext)) {
                    $usercontext = getTrunkUserContext($trunknum);
                }

                if (!isset($userconfig)) {
                    $userconfig = getTrunkUserConfig($trunknum);
                }

                if (!isset($register)) {
                    $register = getTrunkRegister($trunknum);
                }
            }
        }

        if (!isset($dialrules)) { $dialrules = null; }

        if (count($dialrules) == 0) {
            if ($temp = getDialRules($trunknum)) {
                foreach ($temp as $key=>$val) {
                    if (preg_match("/^rule\d+$/",$key)) {
                        $dialrules[] = $val;
                    }
                }
            }
            unset($temp);
        }

        echo "<h3>".sprintf(_("Edit %s Trunk:"),strtoupper($tech))."</h3>";
?>
        <p><a title="<?php echo $channelid ?>" href="config.php?mode=pbx&display=<?php echo urlencode($display) ?>&extdisplay=<?php echo urlencode($extdisplay) ?>&action=deltrunk" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete Trunk")?> <?php  echo substr($channelid,0,20); ?></a></p>
<?php

        $routes = gettrunkroutes($trunknum);
        $num_routes = count($routes);
        if ($num_routes > 0) {
            echo "<a href=# class=\"info\">"._("In use by")." ".$num_routes." ".($num_routes == 1 ? _("route") : _("routes"))."<span>";
            foreach($routes as $route=>$priority) {
                echo _("Route")." <b>".$route."</b>: "._("Sequence")." <b>".$priority."</b><br>";
            }
            echo "</span></a>";
        } else {
        $routes_tone = gettrunktone($trunknum);
        $num_routes_tone = count($routes_tone);
        if ($num_routes_tone > 0) {

            foreach($routes_tone as $route_tone=>$description) {
                echo "This trunk is used by: <b>".$route_tone."</b>";
                }
            } else {

            echo "<b>WARNING:</b> <a href=# class=\"info\">"._("This trunk is not used by any routes!")."<span>";
            echo _("This trunk will not be able to be used for outbound calls until a route is setup that uses it. Click on <b>Outbound Routes</b> to setup routing.");
            echo "</span></a>";
            }
        }
        echo "<br><br>";

    } else {


        $outcid = "";
        $maxchans = "";
        $dialoutprefix = "";

        if ($tech == "zap") {
            $channelid = "g1";
        } else if ($tech == "misdn") {
            $channelid = "mISDN/g:1/\$OUTNUM\$";
        } else {
            $channelid = "";
        }

        if ($tech == "sip") {

        $peerdetails = "allow=alaw&ulaw&gsm&g729&ilbc&g726\ncanredirect=no\ncanreinvite=no\ncontext=from-trunk\ndisallow=all\ndtmfmode=rfc2833\nfromdomain=***provider address***\nfromuser=***username***\nhost=***provider address***\ninsecure=very\nnat=yes\nport=5060\nqualify=500\nsecret=***password***\ntype=peer\nusername=***username***\n";
        $usercontext = "";
        $userconfig = "";
        $channelid = "***providername***";
        $register = "username:password@provideraddress:5060/phonenumber";
        $localpattern = "NXXXXXX";
        $lddialprefix = "1";
        $areacode = "";

        }

        if ($tech == "iax2") {

        $peerdetails = "host=***provider ip address***\nusername=***userid***\nsecret=***password***\ntype=peer";
        $userconfig = "secret=***password***\ntype=user\ncontext=from-trunk";
        $localpattern = "NXXXXXX";
        $lddialprefix = "1";
        $areacode = "";
        $channelid = "";
        $usercontext = "";

        }


        echo "<h3>".sprintf("Add %s Trunk",strtoupper($tech))."</h3>";
    }
?>

        <form name="trunkEdit" action="config.php?mode=pbx" method="post" onsubmit="return trunkEdit_onsubmit('<?php echo ($extdisplay ? "edittrunk" : "addtrunk") ?>');">
            <input type="hidden" name="display" value="<?php echo $display?>"/>
            <input type="hidden" name="extdisplay" value="<?php echo $extdisplay ?>"/>
            <input type="hidden" name="action" value=""/>
            <input type="hidden" name="tech" value="<?php echo $tech?>"/>
            <table>
            <tr>
                <td colspan="2">
                    <h4><?php echo _("General Settings")?></h4>
                </td>
            </tr>
            <tr>
                <td>
                    <a href=# class="info"><?php echo _("Outbound Caller ID")?><span><?php echo _("Caller ID for calls placed out on this trunk<br><br>Format: <b>caller name &lt;#######&gt;</b>")?><br></span></a>:
                </td><td>
                    <input type="text" size="30" name="outcid" value="<?php echo $outcid;?>"/>
                </td>
            </tr>
            <tr>
                <td>
                    <a href=# class="info"><?php echo _("Maximum channels")?><span><?php echo _("Controls the maximum number of channels (simultaneous calls) that can be used on this trunk, including both incoming and outgoing calls. Leave blank to specify no maximum.")?></span></a>:
                </td><td>
                    <input type="text" size="3" name="maxchans" value="<?php echo htmlspecialchars($maxchans); ?>"/>
                </td>
            </tr>

<?php

        $sql = "SELECT * FROM globals WHERE variable LIKE '%OUTTRUNKRIGHT_$trunknum'";
        $outtrunkrights = $db->getAll($sql);
                    if(DB::IsError($globals)) {die($globals->getMessage());}
        foreach ($outtrunkrights as $outtrunkright) {
                ${trim($outtrunkright[0])} = ($outtrunkright[1]);
        }

?>

            <tr>
                <td valign="top">
                    <a href=# class="info"><?php echo _("Call Permission")?><span><?php echo _("Enable Call Permission (Call Disallow / Call Allow) in extensions.")?></span></a>:
                </td>
                    <td><input type="checkbox" onFocus="this.blur()" value="1" name="outtrunkright" <?php  echo ($outtrunkright[1] ? 'CHECKED' : '')?>>
                </td>
            </tr>

            <tr>
                <td valign="top">
                    <a href=# class="info"><?php echo _("Group Permission")?><span><?php echo _("Permission Group. If you want to use it, you must set the AccountCode variable in Extensions. If Empty all Permission are enabled.")?></span></a>:
                </td><td>&nbsp;

<?php     $extens = getextens_acode();
?>

                <select name="permission[]" id="permission" multiple onMouseDown="GetCurrentListValues(this);" onchange="FillListValues(this);" size="<?php  $rows = count($extens)+1; echo (($rows < 5) ? 5 : (($rows > 10) ? 10 : $rows) ); ?>" width="225" style="width:225px">
<?php

    if (isset($extens)) {

        $sql = "SELECT * FROM globals WHERE variable LIKE '%OUTRIGHT_$trunknum'";
                $outrights = $db->getAll($sql);
                    if(DB::IsError($globals)) {die($globals->getMessage());}

            foreach ($outrights as $outright) {
                ${trim($outright[0])} = ($outright[1]);
            }
                $acode_sel = explode("|",$outright[1]);

                    $check_uniq = array();
                    foreach ($extens as $exten) {
                        if(!in_array ($exten[1], $check_uniq)) {
                            $check_uniq[]=$exten[1];
                            $option_tag = '<option value="'.$exten[1].'" ';
                            foreach ($acode_sel as $selected) {
                                if ($selected == $exten[1]) {
                                    $option_tag .= 'SELECTED';

                                }
                            }
                            $option_tag .= '>'.$exten[1] .'</option>';
                            echo $option_tag;
                        }
                    }

    }

?>
            </select></td></tr>
<tr>
    <td></td>
    <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face="verdana" size="1"><a href="javascript:SelectAllList(document.trunkEdit.permission);" style="color:black;">select all</a>&nbsp;<a href="javascript:DeselectAllList(document.trunkEdit.permission);" style="color:black;">deselect all</a></font>
</tr>
            <tr>
                <td colspan="2">
                    <br><h4><?php echo _("Outgoing Dial Rules")?></h4>
                </td>
            </tr>
            <tr>
                <td valign="top">
                <a href=# class="info"><?php echo _("Dial Rules")?><span><?php echo _("A Dial Rule controls how calls will be dialed on this trunk. It can be used to add or remove prefixes. Numbers that don't match any patterns defined here will be dialed as-is. Note that a pattern without a + or | (to add or remove a prefix) will not make any changes but will create a match. Only the first matched rule will be executed and the remaining rules will not be acted on.")?><br><br><b><?php echo _("Rules:")?></b><br>
    <strong>X</strong>&nbsp;&nbsp;&nbsp; <?php echo _("matches any digit from 0-9")?><br>
    <strong>Z</strong>&nbsp;&nbsp;&nbsp; <?php echo _("matches any digit from 1-9")?><br>
    <strong>N</strong>&nbsp;&nbsp;&nbsp; <?php echo _("matches any digit from 2-9")?><br>
    <strong>[1237-9]</strong>&nbsp;   <?php echo _("matches any digit or letter in the brackets (in this example, 1,2,3,7,8,9)")?><br>
    <strong>.</strong>&nbsp;&nbsp;&nbsp; <?php echo _("wildcard, matches one or more characters (not allowed before a | or +)")?><br>
    <strong>|</strong>&nbsp;&nbsp;&nbsp; <?php echo _("removes a dialing prefix from the number (for example, 613|NXXXXXX would match when some dialed \"6135551234\" but would only pass \"5551234\" to the trunk)")?>
    <strong>+</strong>&nbsp;&nbsp;&nbsp; <?php echo _("adds a dialing prefix from the number (for example, 1613+NXXXXXX would match when some dialed \"5551234\" and would pass \"16135551234\" to the trunk)")?>
    <?php echo _("You can also use both + and |, for example: 011+0|1ZXXXXXXXXX would match \"012555555555\" and dial it as \"0112555555555\" Note that the order does not matter, eg. 0|011+1ZXXXXXXXXX does the same thing."); ?>
                    </span></a>:
                </td><td valign="top">&nbsp;
                    <textarea id="dialrules" cols="20" rows="<?php  $rows = count($dialrules)+1; echo (($rows < 5) ? 5 : (($rows > 20) ? 20 : $rows) ); ?>" name="dialrules"><?php echo implode("\n",$dialrules);?></textarea><br>

<?              
if (ae_detect_ie()) {

?>                              <input type="submit" width="160" style="width:160px" style="font-size:10px;" value="<?php echo _("Clean & Remove duplicates")?>" />
            
<? } else { ?>

                <input type="submit" width="186" style="width:186px" style="font-size:10px;" value="<?php echo _("Clean & Remove duplicates")?>" /><? } ?>


                </td>
            </tr>
            <tr>
                <td>
                    <a href=# class="info"><?php echo _("Dial rules wizards")?><span>
                    <strong><?php echo _("Always add prefix to local numbers")?></strong> <?php echo _("is useful for VoIP trunks, where if a number is dialed as \"5551234\", it can be converted to \"16135551234\".")?><br>
                    <strong><?php echo _("Remove prefix from local numbers")?></strong> <?php echo _("is useful for ZAP trunks, where if a local number is dialed as \"16135551234\", it can be converted to \"555-1234\".")?><br>
                    <strong><?php echo _("Lookup and remove local prefixes")?></strong> <?php echo _("is the same as Remove prefix from local numbers, but uses the database at http://members.dandy.net/~czg/search.html to find your local calling area (NA-only)")?><br>
                    </span></a>:
                </td><td valign="top">&nbsp;&nbsp;<select id="autopop" name="autopop" onChange="changeAutoPop(); ">
                        <option value="" SELECTED><?php echo _("(pick one)")?></option>
                        <option value="always"><?php echo _("Always add prefix to local numbers")?></option>
                        <option value="remove"><?php echo _("Remove prefix from local numbers")?></option>
                        <option value="lookup"><?php echo _("Lookup and remove local prefixes")?></option>
                    </select>
                </td>
            </tr>
            <input id="npanxx" name="npanxx" type="hidden" />
            <script language="javascript">

            function populateLookup() {
<?php
    if (function_exists("curl_init")) {
?>
                do {
                    var npanxx = <?php echo 'prompt("'._("What is your areacode + prefix (NPA-NXX)?\\n\\n(Note: this database contains North American numbers only, and is not guaranteed to be 100% accurate. You will still have the option of modifying results.)\\n\\nThis may take a few seconds.".'")')?>;
                    if (npanxx == null) return;
                } while (!npanxx.match("^[2-9][0-9][0-9][-]?[2-9][0-9][0-9]$") && <?php echo '!alert("'._("Invalid NPA-NXX. Must be of the format \'NXX-NXX\'").'")'?>);

                document.getElementById('npanxx').value = npanxx;
                trunkEdit.action.value = "populatenpanxx";
                trunkEdit.submit();
<?php
    } else {
?>
                <?php echo 'alert("'._("Error: Cannot continue!\\n\\nPrefix lookup requires cURL support in PHP on the server. Please install or enable cURL support in your PHP installation to use this function. See http://www.php.net/curl for more information.").'")'?>;
<?php
    }
?>
            }

            function populateAlwaysAdd() {
                do {
                    var localpattern = <?php echo 'prompt("'._("What is the local dialing pattern?\\n\\n(ie. NXXNXXXXXX for US/CAN 10-digit dialing, NXXXXXX for 7-digit)").'"'?>,"NXXXXXX");
                    if (localpattern == null) return;
                } while (!localpattern.match('^[0-9#*ZXN\.]+$') && <?php echo '!alert("'._("Invalid pattern. Only 0-9, #, *, Z, N, X and . are allowed.").'")'?>);

                do {
                    var localprefix = <?php echo 'prompt("'._("What prefix should be added to the dialing pattern?\\n\\n(ie. for US/CAN, 1+areacode, ie, \'1613\')?").'")'?>;
                    if (localprefix == null) return;
                } while (!localprefix.match('^[0-9#*]+$') && <?php echo '!alert("'._("Invalid prefix. Only dialable characters (0-9, #, and *) are allowed.").'")'?>);

                dialrules = document.getElementById('dialrules');
                if (dialrules.value[dialrules.value.length-1] != '\n') {
                    dialrules.value = dialrules.value + '\n';
                }
                dialrules.value = dialrules.value + localprefix + '+' + localpattern + '\n';
            }

            function populateRemove() {
                do {
                    var localprefix = <?php echo 'prompt("'._("What prefix should be removed from the number?\\n\\n(ie. for US/CAN, 1+areacode, ie, \'1613\')").'")'?>;
                    if (localprefix == null) return;
                } while (!localprefix.match('^[0-9#*ZXN\.]+$') && <?php echo '!alert("'._('Invalid prefix. Only 0-9, #, *, Z, N, and X are allowed.').'")'?>);

                do {
                    var localpattern = <?php echo 'prompt("'._("What is the dialing pattern for local numbers after")?> "+localprefix+"? \n\n<?php echo _("(ie. NXXNXXXXXX for US/CAN 10-digit dialing, NXXXXXX for 7-digit)").'"'?>,"NXXXXXX");
                    if (localpattern == null) return;
                } while (!localpattern.match('^[0-9#*ZXN\.]+$') && <?php echo '!alert("'._("Invalid pattern. Only 0-9, #, *, Z, N, X and . are allowed.").'")'?>);

                dialrules = document.getElementById('dialrules');
                if (dialrules.value[dialrules.value.length-1] != '\n') {
                    dialrules.value = dialrules.value + '\n';
                }
                dialrules.value = dialrules.value + localprefix + '|' + localpattern + '\n';
            }

            function changeAutoPop() {
                switch(document.getElementById('autopop').value) {
                    case "always":
                        populateAlwaysAdd();
                    break;
                    case "remove":
                        populateRemove();
                    break;
                    case "lookup":
                        populateLookup();
                    break;
                }
                document.getElementById('autopop').value = '';
            }
            </script>



            <tr>
                <td>
                    <a href=# class="info"><?php echo _("Outbound Dial Prefix")?><span><?php echo _("The outbound dialing prefix is used to prefix a dialing string to all outbound calls placed on this trunk. For example, if this trunk is behind another PBX or is a Centrex line, then you would put 9 here to access an outbound line. Another common use is to prefix calls with 'w' on a POTS line that need time to obtain dialtone to avoid eating digits.<br><br>Most users should leave this option blank.")?></span></a>:
                </td><td>
                    <input type="text" size="8" name="dialoutprefix" value="<?php echo htmlspecialchars($dialoutprefix) ?>"/>
                </td>
            </tr>


            <?php if ($tech != "enum") { ?>

                <tr><td colspan="2">
                <br><h4><?php echo _("Outgoing Settings")?></h4>
                </td></tr>

            <?php } ?>

    <?php
    switch ($tech) {
        case "zap":
    ?>
                <tr>
                    <td>
                        <a href=# class="info"><?php echo _("Zap Identifier")?><span><?php echo _("ZAP channels are referenced either by a group number or channel number (which is defined in zapata.conf).  <br><br>The default setting is <b>g1</b>.")?><br></span></a>:
                    </td><td>
                        <input type="text" size="8" name="channelid" value="<?php echo htmlspecialchars($channelid) ?>"/>
                        <input type="hidden" size="14" name="usercontext" value="notneeded"/>
                    </td>
                </tr>
    <?php
        break;
        case "misdn":
    ?>
                <tr>
                    <td>
                        <a href=# class="info"><?php echo _("mISDN Identifier")?><span><?php echo _("mISDN channels are referenced either by a group number or channel number (which is defined in misdn.conf).<br><b>examples:</b><br>mISDN/g:[GROUP]/$OUTNUM$/[OPTIONS]<br>mISDN/[PORTS]/$OUTNUM$/[OPTIONS]")?><br></span></a>:
                    </td><td>
                        <input type="text" size="30" maxlength="50" name="channelid" value="<?php echo htmlspecialchars($channelid) ?>"/>
                        <input type="hidden" size="14" name="usercontext" value="notneeded"/>
                    </td>
                </tr>
    <?php
        break;
        case "enum":
        break;
        case "custom":
    ?>
                <tr>
                    <td>
                        <a href=# class="info"><?php echo _("Custom Dial String")?><span><?php echo _("Define the custom Dial String.  Include the token")?> $OUTNUM$ <?php echo _("wherever the number to dial should go.<br><b>examples:</b><br>CAPI/XXXXXXXX/")?>$OUTNUM$<?php echo _("/b<br>H323/")?>$OUTNUM$@XX.XX.XX.XX<br>OH323/$OUTNUM$@XX.XX.XX.XX:XXXX<br>vpb/1-1/$OUTNUM$</span></a>:
                    </td><td>
                        <input type="text" size="30" maxlength="50" name="channelid" value="<?php echo htmlspecialchars($channelid) ?>"/>
                        <input type="hidden" size="14" name="usercontext" value="notneeded"/>
                    </td>
                </tr>
    <?php
        break;
        default:
    ?>
                <tr>
                    <td>
                        <a href=# class="info"><?php echo _("Trunk Name")?><span><?php echo _("Give this trunk a unique name.  Example: myiaxtel")?></span></a>:
                    </td><td>
                        <input type="text" size="19" name="channelid" value="<?php echo htmlspecialchars($channelid) ?>"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a href=# class="info"><?php echo _("PEER Details")?><span><?php echo _("Modify the default PEER connection parameters for your VoIP provider.<br><br>You may need to add to the default lines listed below, depending on your provider.")?></span></a>:
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <textarea rows="10" cols="40" name="peerdetails"><?php echo htmlspecialchars($peerdetails) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <br><h4><?php echo _("Incoming Settings")?></h4>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href=# class="info"><?php echo _("USER Context")?><span><?php echo _("This is most often the account name or number your provider expects.<br><br>This USER Context will be used to define the below user details.")?></span></a>:
                    </td><td>
                        <input type="text" size="19" name="usercontext" value="<?php echo htmlspecialchars($usercontext)  ?>"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a href=# class="info"><?php echo _("USER Details")?><span><?php echo _("Modify the default USER connection parameters for your VoIP provider.")?><br><br><?php echo _("You may need to add to the default lines listed below, depending on your provider.")?></span></a>:
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <textarea rows="10" cols="40" name="userconfig"><?php echo htmlspecialchars($userconfig); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <br><h4><?php echo _("Registration")?></h4>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a href=# class="info"><?php echo _("Register String")?><span><?php echo _("Most VoIP providers require your system to REGISTER with theirs. Enter the registration line here. Example:<br>username:password@switch.voipprovider.com.<br><br>Many providers will require you to provide a DID number, add a '/didnumber' at the end of registration string in order for any DID matching to work. (This Features works only with SIP channels)")?></span></a>:
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="text" size="50" name="register" value="<?php echo htmlspecialchars($register) ?>"/>
                    </td>
                </tr>
    <?php
        break;
    }
    ?>
            <tr>
                <td colspan="2">
                    <h6><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>"></h6>
                </td>
            </tr>
            </table>

<script language="javascript">
<!--

var theForm = document.trunkEdit;

theForm.outcid.focus();

function trunkEdit_onsubmit(act) {
    var msgInvalidOutboundCID = "<?php echo _('Invalid Outbound Caller ID'); ?>";
    var msgInvalidMaxChans = "<?php echo _('Invalid Maximum Channels'); ?>";
    var msgInvalidDialRules = "<?php echo _('Invalid Dial Rules'); ?>";
    var msgInvalidOutboundDialPrefix = "<?php echo _('Invalid Outbound Dial Prefix'); ?>";
    var msgInvalidTrunkName = "<?php echo _('Invalid Trunk Name entered'); ?>";
    var msgInvalidChannelName = "<?php echo _('Invalid Custom Dial String entered'); ?>";
    var msgInvalidTrunkAndUserSame = "<?php echo _('Trunk Name and User Context cannot be set to the same value'); ?>";
    var msgConfirmBlankContext = "<?php echo _('User Context was left blank and User Details will not be saved!'); ?>";

    defaultEmptyOK = true;
    if (!isCallerID(theForm.outcid.value))
        return warnInvalid(theForm.outcid, msgInvalidOutboundCID);

    if (!isInteger(theForm.maxchans.value))
        return warnInvalid(theForm.maxchans, msgInvalidMaxChans);

    if (!isDialrule(theForm.dialrules.value))
        return warnInvalid(theForm.dialrules, msgInvalidDialRules);

    if (!isDialIdentifierSpecial(theForm.dialoutprefix.value))
        return warnInvalid(theForm.dialoutprefix, msgInvalidOutboundDialPrefix);

    <?php if ($tech != "enum" && $tech != "custom") { ?>
    defaultEmptyOK = true;
    if (isEmpty(theForm.channelid.value) || isWhitespace(theForm.channelid.value))
        return warnInvalid(theForm.channelid, msgInvalidTrunkName);

    if (theForm.channelid.value == theForm.usercontext.value)
        return warnInvalid(theForm.usercontext, msgInvalidTrunkAndUserSame);
    <?php } else if ($tech == "custom" || $tech == "misdn") { ?>
    if (isEmpty(theForm.channelid.value) || isWhitespace(theForm.channelid.value))
        return warnInvalid(theForm.channelid, msgInvalidChannelName);

    if (theForm.channelid.value == theForm.usercontext.value)
        return warnInvalid(theForm.usercontext, msgInvalidTrunkAndUserSame);
    <?php } ?>

    <?php if ($tech == "sip" || substr($tech,0,3) == "iax") { ?>
                if ((isEmpty(theForm.usercontext.value) || isWhitespace(theForm.usercontext.value)) &&
                       (!isEmpty(theForm.userconfig.value) && !isWhitespace(theForm.userconfig.value)) &&
                          (theForm.userconfig.value != "secret=***password***\ntype=user\ncontext=from-trunk")) {
                                 if (confirm(msgConfirmBlankContext) == false)
                                       return false;
                                    }
    <?php } ?>

    theForm.action.value = act;
    return true;
}

function isDialIdentifierSpecial(s) { // special chars allowed in dial prefix (e.g. fwdOUT)
    var i;

    if (isEmpty(s))
       if (isDialIdentifierSpecial.arguments.length == 1) return defaultEmptyOK;
       else return (isDialIdentifierSpecial.arguments[1] == true);

    for (i = 0; i < s.length; i++)
    {
        var c = s.charAt(i);

   	     if ( !isDialDigitChar(c) && (c != "w") && (c != "W") && (c != "q") && (c != "Q") && (c != "+") ) return false;
    }

    return true;
}


function deleteCheck(f2) {

    cancel = false;
    ok = true;

    if (confirm("Are you sure to delete this trunk?"))
          return ! cancel;
    else
          return ! ok;
}


var arrOldValues;

function SelectAllList(CONTROL){
for(var i = 0;i < CONTROL.length;i++){
CONTROL.options[i].selected = true;
}
}

function DeselectAllList(CONTROL){
for(var i = 0;i < CONTROL.length;i++){
CONTROL.options[i].selected = false;
}
}


function FillListValues(CONTROL){
var arrNewValues;
var intNewPos;
var strTemp = GetSelectValues(CONTROL);
arrNewValues = strTemp.split(",");
for(var i=0;i<arrNewValues.length-1;i++){
if(arrNewValues[i]==1){
intNewPos = i;
}
}

for(var i=0;i<arrOldValues.length-1;i++){
if(arrOldValues[i]==1 && i != intNewPos){
CONTROL.options[i].selected= true;
}
else if(arrOldValues[i]==0 && i != intNewPos){
CONTROL.options[i].selected= false;
}

if(arrOldValues[intNewPos]== 1){
CONTROL.options[intNewPos].selected = false;
}
else{
CONTROL.options[intNewPos].selected = true;
}
}
}


function GetSelectValues(CONTROL){
var strTemp = "";
for(var i = 0;i < CONTROL.length;i++){
if(CONTROL.options[i].selected == true){
strTemp += "1,";
}
else{
strTemp += "0,";
}
}
return strTemp;
}

function GetCurrentListValues(CONTROL){
var strValues = "";
strValues = GetSelectValues(CONTROL);
arrOldValues = strValues.split(",")
}




//-->
</script>

        </form>
<?php
}
?>
