<?php /* $Id: routing.php,v 1.23 2005/07/26 15:49:24 ronhartmann Exp $ */
// routing.php Copyright (C) 2004 Greg MacLellan (greg@mtechsolutions.ca)
// routing.php <trunk & roting priority additions> Copyright (C) 2005 Ron Hartmann (rhartmann@vercomsystems.com)
// Asterisk Management Portal Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
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

$extenScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_extensions_from_mysql.pl';
$sipScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_sip_conf_from_mysql.pl';
$iaxScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_iax_conf_from_mysql.pl';
$wOpScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_op_conf_from_mysql.pl';

$display= 8;

$extdisplay=isset($_REQUEST['extdisplay'])?$_REQUEST['extdisplay']:'';
$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$repotrunkdirection = isset($_REQUEST['repotrunkdirection'])?$_REQUEST['repotrunkdirection']:'';
$repotrunkkey = isset($_REQUEST['repotrunkkey'])?$_REQUEST['repotrunkkey']:'';
$ctisupport = isset($_REQUEST['ctisupport'])?$_REQUEST['ctisupport']:'';
$localcid = isset($_REQUEST['localcid'])?$_REQUEST['localcid']:'';

$dialpattern = array();
if (isset($_REQUEST["dialpattern"])) {
    //$dialpattern = $_REQUEST["dialpattern"];
    $dialpattern = explode("\n",$_REQUEST["dialpattern"]);

    if (!$dialpattern) {
        $dialpattern = array();
    }

    foreach (array_keys($dialpattern) as $key) {
        //trim it
        $dialpattern[$key] = trim($dialpattern[$key]);

        // remove blanks
        if ($dialpattern[$key] == "") unset($dialpattern[$key]);

        // remove leading underscores (we do that on backend)
        if ($dialpattern[$key][0] == "_") $dialpattern[$key] = substr($dialpattern[$key],1);
    }

    // check for duplicates, and re-sequence
    $dialpattern = array_values(array_unique($dialpattern));
}

if ( (isset($_REQUEST['reporoutedirection'])) && (isset($_REQUEST['reporoutekey']))) {
    $routepriority = getroutenames();
    $routepriority = setroutepriority($routepriority, $_REQUEST['reporoutedirection'], $_REQUEST['reporoutekey']);
}

$trunkpriority = array();
if (isset($_REQUEST["trunkpriority"])) {
    $trunkpriority = $_REQUEST["trunkpriority"];

    if (!$trunkpriority) {
        $trunkpriority = array();
    }

    // delete blank entries and reorder
    foreach (array_keys($trunkpriority) as $key) {
        if (empty($trunkpriority[$key])) {
            // delete this empty
            unset($trunkpriority[$key]);

        } else if (($key==($repotrunkkey-1)) && ($repotrunkdirection=="up")) {
            // swap this one with the one before (move up)
            $temptrunk = $trunkpriority[$key];
            $trunkpriority[ $key ] = $trunkpriority[ $key+1 ];
            $trunkpriority[ $key+1 ] = $temptrunk;

        } else if (($key==($repotrunkkey)) && ($repotrunkdirection=="down")) {
            // swap this one with the one after (move down)
            $temptrunk = $trunkpriority[ $key+1 ];
            $trunkpriority[ $key+1 ] = $trunkpriority[ $key ];
            $trunkpriority[ $key ] = $temptrunk;
        }
    }
    unset($temptrunk);
    $trunkpriority = array_values($trunkpriority); // resequence our numbers
}

$routename = isset($_REQUEST["routename"]) ? $_REQUEST["routename"] : "";
$routepass = isset($_REQUEST["routepass"]) ? $_REQUEST["routepass"] : "";

//if submitting form, update database
switch ($action) {
    case "addroute":
        addRoute($routename, $dialpattern, $trunkpriority,"new", $routepass,$ctisupport,$localcid);
        exec($extenScript);
        needreload();
        $extdisplay = ''; // resets back to main screen
        $routename = ''; // resets back to main screen
        $routepass = ''; // resets back to main screen
        $dialpattern=array();
        $trunkpriority=array();
    break;
    case "editroute":
        editRoute($routename, $dialpattern, $trunkpriority, $routepass,$ctisupport,$localcid);
        exec($extenScript);
        needreload();
    break;
    case "delroute":
        deleteRoute($extdisplay);
        // re-order the routes to make sure that there are no skipped numbers.
        // example if we have 001-test1, 002-test2, and 003-test3 then delete 002-test2
        // we do not want to have our routes as 001-test1, 003-test3 we need to reorder them
        // so we are left with 001-test1, 002-test3
        $routepriority = getroutenames();
        $routepriority = setroutepriority($routepriority, '','');
        exec($extenScript);
        needreload();

        $extdisplay = ''; // resets back to main screen
    break;
    case 'renameroute':
        if (renameRoute($routename, $_REQUEST["newroutename"])) {
            exec($extenScript);
            needreload();
        } else {
            echo "<script language=\"javascript\">alert('"._("Error renaming route: duplicate name")."');</script>";
        }
        $route_prefix=substr($routename,0,4);
        $extdisplay=$route_prefix.$_REQUEST["newroutename"];

    break;
    case 'prioritizeroute':
        exec($extenScript);
        needreload();
    break;
    case 'populatenpanxx':
        if (preg_match("/^([2-9]\d\d)-?([2-9]\d\d)$/", $_REQUEST["npanxx"], $matches)) {
            // first thing we do is grab the exch:
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
                        $dialpattern[] = "1".$matches[1].$matches[2]."XXXX";
                        //$localprefixes[] = "1".$matches[1].$matches[2];
                    }
                }

                // check for duplicates, and re-sequence
                $dialpattern = array_values(array_unique($dialpattern));
            } else {
                $errormsg = _("Error fetching prefix list for: "). $_REQUEST["npanxx"];
            }

        } else {
            // what a horrible error message... :p
            $errormsg = _("Invalid format for NPA-NXX code (must be format: NXXNXX)");
        }

        if (isset($errormsg)) {
            echo "<script language=\"javascript\">alert('".addslashes($errormsg)."');</script>";
            unset($errormsg);
        }
    break;
}



//get all rows from globals
$sql = "SELECT * FROM globals";
$globals = $db->getAll($sql);
if(DB::IsError($globals)) {
die($globals->getMessage());
}

//create a set of variables that match the items in global[0]
foreach ($globals as $global) {
    ${trim($global[0])} = htmlentities($global[1]);
}

?>
</div>



<div class="rnav" style="width:200px;">
    <li><a id="<?php  echo ($extdisplay=='' ? 'current':'') ?>" href="config.php?mode=pbx&display=<?php echo urlencode($display)?>" onFocus="this.blur()"><?php echo _("Add Route")?></a></li>
<?php

$reporoutedirection = isset($_REQUEST['reporoutedirection'])?$_REQUEST['reporoutedirection']:'';
$reporoutekey = isset($_REQUEST['reporoutekey'])?$_REQUEST['reporoutekey']:'';
$key = -1;
$routepriority = getroutenames();
$positions=count($routepriority);
foreach ($routepriority as $tresult) {
$key++;
?>
            <?php   // move up
                echo "<li><a id=\"".($extdisplay==$tresult[0] ? 'current':'')."\" href=\"config.php?mode=pbx&display=".urlencode($display)."&extdisplay=".urlencode($tresult[0])."\" onFocus=\"this.blur()\">$key ". substr($tresult[0],4)."</a>";
            if ($key > 0) {?>
                <img src="images/scrollup.gif" onclick="repositionRoute('<?php echo $key ?>','up')" alt="<?php echo _("Move Up")?>" style="float:none; margin-left:0px; margin-bottom:0px;" width="9" height="11">
            <?php  } else { ?>
                <img src="images/blank.gif" style="float:none; margin-left:0px; margin-bottom:0px;" width="9" height="11">
            <?php  }

            // move down

            if ($key < ($positions-1)) {?>
                <img src="images/scrolldown.gif" onclick="repositionRoute('<?php echo $key ?>','down')" alt="<?php echo _("Move Down")?>"  style="float:none; margin-left:0px; margin-bottom:0px;" width="9" height="11">
            <?php  } else { ?>
                <img src="images/blank.gif" style="float:none; margin-left:0px; margin-bottom:0px;" width="9" height="11">
            <?php  }
            echo "</li>";?>

<?php
} // foreach
?>
</div>

<div class="content">

<?php
if ($extdisplay) {

    // load from db

    if (!isset($_REQUEST["dialpattern"])) {
        $dialpattern = getroutepatterns($extdisplay);
    }
    if (!isset($_REQUEST["trunkpriority"])) {
        $trunkpriority = getroutetrunks($extdisplay);
    }
    if (!isset($_REQUEST["routepass"])) {
        $routepass = getroutepassword($extdisplay);
    }
    if (!isset($_REQUEST["ctisupport"])) {
        $ctisupport = getctisupport($extdisplay);
    }
    if (!isset($_REQUEST["localcid"])) {
        $localcid = getlocalcid($extdisplay);
    }

    echo "<h3>"._("Edit Route:")."</h3>";
} else {
    echo "<h3>"._("Add Route:")."</h3>";
}

// build trunks associative array
foreach (gettrunks() as $temp) {
    $trunks[$temp[0]] = $temp[1];
//  $trunks[trim($temp[0])] = trim($temp[1]);
}

if ($extdisplay) { // editing
?>
    <p><a href="config.php?mode=pbx&display=<?php echo urlencode($display) ?>&extdisplay=<?php echo urlencode($extdisplay) ?>&action=delroute" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete Route")?> <?php  echo substr($extdisplay,4); ?></a></p>
<?php  } ?>

    <form autocomplete="off" id="routeEdit" name="routeEdit" action="config.php?mode=pbx" method="POST" onsubmit="return routeEdit_onsubmit('<?php echo ($extdisplay ? "editroute" : "addroute") ?>');">
        <input type="hidden" name="display" value="<?php echo $display?>"/>
        <input type="hidden" name="extdisplay" value="<?php echo $extdisplay ?>"/>
        <input type="hidden" id="action" name="action" value=""/>
        <table>
        <tr>
            <td align="left" width="145">
                <a href=# class="info"><?php echo _("Route Name")?><span><?php echo _("Name of this route. Should be used to describe what type of calls this route matches (for example, 'local' or 'longdistance').")?><br></span></a>:
            </td>
<?php  if ($extdisplay) { // editing?>
            <td>
                &nbsp;&nbsp;<?php echo '<span style="text-transform:uppercase;">'.substr($extdisplay,4).'</span>';?>
                <input type="hidden" id="routename" name="routename" value="<?php echo $extdisplay;?>"/>
                <input type="button" onClick="renameRoute();" value="<?php echo _("Rename")?>" style="font-size:10px;"/>
                <input type="hidden" id="newroutename" name="newroutename" value=""/>
                <script language="javascript">
                function renameRoute() {
                    do {
                        var newname = prompt("<?php echo _("Rename route")?> " + document.getElementById('routename').value + " <?php echo _("to:")?>");
                        if (newname == null) return;
                    } while (!newname.match('^[a-zA-Z0-9][a-zA-Z0-9]+$') && !alert("<?php echo _("Route name is invalid...please try again")?>"));

                    document.getElementById('newroutename').value = newname;
                    document.getElementById('routeEdit').action.value = 'renameroute';
                    document.getElementById('routeEdit').submit();
                }
                </script>
            </td>
<?php  } else { // new ?>
            <td>
                <input type="text" size="20" name="routename" value="<?php echo htmlspecialchars($routename);?>"/>
            </td>
<?php  } ?>
        </tr>
        <tr>
            <td><a href=# class="info"><?php echo _("Route Password")?>:<span><?php echo _("Optional: A route can prompt users for a password before allowing calls to progress.  This is useful for restricting calls to international destinations or 1-900 numbers.<br><br>A numerical password, or the path to an Authenticate password file can be used.<br><br>Leave this field blank to not prompt for password.")?></span></a></td>
            <td><input type="text" size="20" name="routepass" value="<?php echo $routepass;?>"/></td>
        </tr>
        <tr>
            <td><a href=# class="info"><?php echo _("Force local CallerID")?>:<span><?php echo _("Optional: Enable Local CallerID (Name/Number) for this Outbound route. This Option override Outbound CallerID Settings of Extension and Trunk.")?></span></a></td>
            <td><input type="checkbox" value="1" name="localcid" <?php  echo ($localcid ? 'CHECKED' : '')?>></td>
        </tr>
        <tr>
            <td><a href=# class="info"><?php echo _("Cti Support")?>:<span><?php echo _("Optional: Enable CTI Support for this Outbound route.")?></span></a></td>
            <td><input type="checkbox" value="1" name="ctisupport" <?php  echo ($ctisupport ? 'CHECKED' : '')?>></td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
        <td valign="top">
                <a href=# class="info"><?php echo _("Dial Patterns")?>:<span><?php echo _("A Dial Pattern is a unique set of digits that will select this trunk. Enter one dial pattern per line.")?><br><br><b><?php echo _("Rules:")?></b><br>
   <strong>X</strong>&nbsp;&nbsp;&nbsp; <?php echo _("matches any digit from 0-9")?><br>
   <strong>Z</strong>&nbsp;&nbsp;&nbsp; <?php echo _("matches any digit from 1-9")?><br>
   <strong>N</strong>&nbsp;&nbsp;&nbsp; <?php echo _("matches any digit from 2-9")?><br>
   <strong>[1237-9]</strong>&nbsp;   <?php echo _("matches any digit or letter in the brackets (in this example, 1,2,3,7,8,9)")?><br>
   <strong>.</strong>&nbsp;&nbsp;&nbsp; <?php echo _("wildcard, matches one or more characters")?> <br>
   <strong>|</strong>&nbsp;&nbsp;&nbsp; <?php echo _("seperates a dialing prefix from the number (for example, 9|NXXXXXX would match when some dialed \"95551234\" but would only pass \"5551234\" to the trunks)")?>
                </span></a><br>
            </td>
            <td>

                &nbsp;&nbsp;<textarea cols="20" rows="<?php  $rows = count($dialpattern)+1; echo (($rows < 5) ? 5 : (($rows > 20) ? 20 : $rows) ); ?>" id="dialpattern" name="dialpattern"><?php echo  implode("\n",$dialpattern);?></textarea><br>

<?

if (ae_detect_ie()) {

?>
                <input type="submit" width="160" style="width:160px" style="font-size:10px;" value="<?php echo _("Clean & Remove duplicates")?>" />

<? } else { ?>

                <input type="submit" width="186" style="width:186px" style="font-size:10px;" value="<?php echo _("Clean & Remove duplicates")?>" />

<? } ?>
            </td>
        </tr>
        <tr>
            <td><?php echo _("Insert:")?></td>
            <input id="npanxx" name="npanxx" type="hidden" />
            <script language="javascript">

            function populateLookup() {
<?php
    if (function_exists("curl_init")) { // curl is installed
?>
                //var npanxx = prompt("What is your areacode + prefix (NPA-NXX)?", document.getElementById('areacode').value);
                do {
                    var npanxx = <?php echo 'prompt("'._("What is your areacode + prefix (NPA-NXX)?\\n\\n(Note: this database contains North American numbers only, and is not guaranteed to be 100% accurate. You will still have the option of modifying results.)\\n\\nThis may take a few seconds.").'")'?>;
                    if (npanxx == null) return;
                } while (!npanxx.match("^[2-9][0-9][0-9][-]?[2-9][0-9][0-9]$") && <?php echo '!alert("'._("Invalid NPA-NXX. Must be of the format \'NXX-NXX\'").'")'?>);

                document.getElementById('npanxx').value = npanxx;
                document.getElementById('routeEdit').action.value = "populatenpanxx";
                document.getElementById('routeEdit').submit();
<?php
    } else { // curl is not installed
?>
                <?php echo "alert('"._("Error: Cannot continue!\\n\\nPrefix lookup requires cURL support in PHP on the server. Please install or enable cURL support in your PHP installation to use this function. See http://www.php.net/curl for more information.")."')"?>;
<?php
    }
?>
            }


            function insertCode() {
                code = document.getElementById('inscode').value;
                insert = '';
                switch(code) {
                    case "local":
                        insert = 'NXXXXXX\n';
                    break;
                    case "local10":
                        insert = 'NXXXXXX\n'+
                            'NXXNXXXXXX\n';
                    break;
                    case 'tollfree':
                        insert = '1800NXXXXXX\n'+
                            '1888NXXXXXX\n'+
                            '1877NXXXXXX\n'+
                            '1866NXXXXXX\n';
                    break;
                    case "ld":
                        insert = '1NXXNXXXXXX\n';
                    break;
                    case "int":
                        insert = '011.\n';
                    break;
                    case 'info':
                        insert = '411\n'+
                            '311\n';
                    break;
                    case 'emerg':
                        insert = '911\n';
                    break;
                    case 'lookup':
                        populateLookup();
                        insert = '';
                    break;

                }
                dialPattern=document.getElementById('dialpattern');
                if (dialPattern.value[ dialPattern.value.length - 1 ] == "\n") {
                    dialPattern.value = dialPattern.value + insert;
                } else {
                    dialPattern.value = dialPattern.value + '\n' + insert;
                }

                // reset element
                document.getElementById('inscode').value = '';
            }

            --></script>
            <td>&nbsp;&nbsp;<select onChange="insertCode();" id="inscode">
            <option value=""><?php echo _("Pick pre-defined patterns")?></option>
            <option value="local"><?php echo _("Local 7 digit")?></option>
            <option value="local10"><?php echo _("Local 7/10 digit")?></option>
            <option value="tollfree"><?php echo _("Toll-free")?></option>
            <option value="ld"><?php echo _("Long-distance")?></option>
            <option value="int"><?php echo _("International")?></option>
            <option value="info"><?php echo _("Information")?></option>
            <option value="emerg"><?php echo _("Emergency")?></option>
            <option value="lookup"><?php echo _("Lookup local prefixes")?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
            <br>
                <a href=# class="info"><?php echo _("Trunk Sequence")?>:<span><?php echo _("The Trunk Sequence controls the order of trunks that will be used when the above Dial Patterns are matched. <br><br>For Dial Patterns that match long distance numbers, for example, you'd want to pick the cheapest routes for long distance (ie, VoIP trunks first) followed by more expensive routes (POTS lines).")?><br></span></a>
            </td>
        </tr>
        <input type="hidden" id="repotrunkdirection" name="repotrunkdirection" value="">
        <input type="hidden" id="repotrunkkey" name="repotrunkkey" value="">
        <input type="hidden" id="reporoutedirection" name="reporoutedirection" value="">
        <input type="hidden" id="reporoutekey" name="reporoutekey" value="">
<?php
$key = -1;
$positions=count($trunkpriority);
foreach ($trunkpriority as $key=>$trunk) {
?>
        <tr><td align="right"><?php echo $key; ?></td>
            <td>&nbsp;&nbsp;<select id='trunkpri<?php echo $key ?>' name="trunkpriority[<?php echo $key ?>] style="width:170px;">
                <option value=""></option>
                <?php
                foreach ($trunks as $name=>$display) {
                    echo "<option id=\"trunk".$key."\" value=\"".$name."\" ".($name == $trunk ? "selected" : "").">".(strpos($display,'AMP:')===0 ? substr($display,4) : $display)."</option>";
                }
                ?>
                </select>

                <img src="images/trash.png" style="float:none; margin-left:0px; margin-bottom:0px;" title="Click here to remove this trunk" onclick="deleteTrunk(<?php echo $key ?>)">
            <?php   // move up
            if ($key > 0) {?>
                <img src="images/scrollup.gif" onclick="repositionTrunk('<?php echo $key ?>','up')" alt="<?php echo _("Move Up")?>" style="float:none; margin-left:0px; margin-bottom:0px;" width="9" height="11">
            <?php  } else { ?>
                <img src="images/blank.gif" style="float:none; margin-left:0px; margin-bottom:0px;" width="9" height="11">
            <?php  }

            // move down

            if ($key < ($positions-1)) {?>
                <img src="images/scrolldown.gif" onclick="repositionTrunk('<?php echo $key ?>','down')" alt="<?php echo _("Move Down")?>"  style="float:none; margin-left:0px; margin-bottom:0px;" width="9" height="11">
            <?php  } else { ?>
                <img src="images/blank.gif" style="float:none; margin-left:0px; margin-bottom:0px;" width="9" height="11">
            <?php  } ?>
            </td>
        </tr>
<?php
} // foreach

$key += 1; // this will be the next key value
$name = "";

// display 1 additional box if editing, or one for each trunk (to a max of 3)
$num_new_boxes = ($extdisplay ? 1 : ((count($trunks) > 3) ? 3 : count($trunks)));

for ($i=0; $i < $num_new_boxes; $i++) {

?>
        <tr>
            <td>&nbsp;</td>
            <td>
                &nbsp;&nbsp;<select id='trunkpri<?php echo $key ?>' name="trunkpriority[<?php echo $key ?>]">
                <option value="" SELECTED></option>
                <?php
                foreach ($trunks as $name=>$display) {
                    echo "<option value=\"".$name."\">".(strpos($display,'AMP:')===0 ? substr($display,4) : $display)."</option>";
                }
                ?>
                </select>
            </td>
        </tr>
<?php

$key++;

} //for 0..$num_new_boxes ?>

<?php if ($extdisplay): // editing ?>
        <tr>
            <td>&nbsp;</td>
            <td>
                <input name="Submit" type="submit" value="<?php echo _("Add")?>">
            </td>
        </tr>
<?php endif; // if $extdisplay ?>
        <tr>
            <td colspan="2">
            <br>
                <h6><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>">
                </h6>
            </td>
        </tr>
        </table>

<script language="javascript">
<!--

var theForm = document.routeEdit;

if (theForm.routename.value == "") {
    theForm.routename.focus();
} else {
    theForm.routepass.focus();
}

function routeEdit_onsubmit(act) {
    var msgInvalidRouteName = "<?php echo _('Route name is invalid, please try again'); ?>";
    var msgInvalidRoutePwd = "<?php echo _('Route password must be numberic or leave blank to disable'); ?>";
    var msgInvalidDialPattern = "<?php echo _('Dial pattern is invalid'); ?>";
    var msgInvalidTrunkSelection = "<?php echo _('At least one trunk must be picked'); ?>";

    defaultEmptyOK = false;
    if (isEmpty(theForm.routename.value))
        return warnInvalid(theForm.routename, msgInvalidRouteName);

    defaultEmptyOK = true;
    if (!isInteger(theForm.routepass.value))
        return warnInvalid(theForm.routepass, msgInvalidRoutePwd);

    defaultEmptyOK = false;
    if (!isDialpattern(theForm.dialpattern.value))
        return warnInvalid(theForm.dialpattern, msgInvalidDialPattern);

    if (theForm.trunkpri0.value == "") { // should they all be checked ?
        theForm.trunkpri0.focus();
        alert(msgInvalidTrunkSelection);
        return false;
    }

    theForm.action.value = act;
    return true;
}

function repositionTrunk(key,direction) {
    if(direction == "up"){
        document.getElementById('repotrunkdirection').value=direction;
        document.getElementById('repotrunkkey').value=key;
    }else if(direction == "down" ){
        document.getElementById('repotrunkdirection').value=direction;
        document.getElementById('repotrunkkey').value=key;
    }
    document.getElementById('routeEdit').submit();
}

function deleteTrunk(key) {
    document.getElementById('trunkpri'+key).value = '';
    document.getElementById('routeEdit').submit();
}

function repositionRoute(key,direction){
    if(direction == "up"){
        document.getElementById('reporoutedirection').value=direction;
        document.getElementById('reporoutekey').value=key;
    }else if(direction == "down" ){
        document.getElementById('reporoutedirection').value=direction;
        document.getElementById('reporoutekey').value=key;
    }
    document.getElementById('action').value='prioritizeroute';
    document.getElementById('routeEdit').submit();
}

function deleteTrunk(key) {

    cancel = false;
    ok = true;

    if (confirm("Are you sure to delete this Trunk Sequence? \nIf yes press SUBMIT after Delete.")){
        document.getElementById('trunkpri'+key).value = '';
        document.getElementById('routeEdit').submit();
        }else{
    ok = true;
}
}

function deleteCheck(f2) {

    cancel = false;
    ok = true;

    if (confirm("Are you sure to delete this Outbound Routing?"))
        return ! cancel;
    else
        return ! ok;
}

//-->
</script>

</form>
