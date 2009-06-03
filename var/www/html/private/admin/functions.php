<?php

function file_parser_serialpn($filename) {
    $file = file($filename);
    $conf = trim($file[0]);
    return $conf;
}

function ae_detect_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}

function sql($sql,$type="query",$fetchmode=null) {
    global $db;
    $results = $db->$type($sql,$fetchmode);
    if(DB::IsError($results)) {
        die($results->getDebugInfo());
    }
    return $results;
}

function parse_amportal_conf($filename) {
    $file = file($filename);
    if (is_array($file)) {
        foreach ($file as $line) {
            if (preg_match("/^\s*([a-zA-Z0-9]+)=([a-zA-Z0-9 .&-@=_<>\"\']+)\s*$/",$line,$matches)) {
                $conf[ $matches[1] ] = $matches[2];
            }
        }
    } else {
        die("<h1>Missing or unreadable config file ($filename)...cannot continue</h1>");
    }

    if ( !isset($conf["AMPDBENGINE"]) || ($conf["AMPDBENGINE"] == ""))
    {
        $conf["AMPDBENGINE"] = "mysql";
    }

    return $conf;
}

function timeString($seconds, $full = false) {
        if ($seconds == 0) {
                return "0 ".($full ? "seconds" : "s");
        }

        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;

        $days = floor($hours / 24);
        $hours = $hours % 24;

        if ($full) {
                return substr(
                                ($days ? $days." day".(($days == 1) ? "" : "s").", " : "").
                                ($hours ? $hours." hour".(($hours == 1) ? "" : "s").", " : "").
                                ($minutes ? $minutes." minute".(($minutes == 1) ? "" : "s").", " : "").
                                ($seconds ? $seconds." second".(($seconds == 1) ? "" : "s").", " : ""),
                               0, -2);
        } else {
                return substr(($days ? $days."d, " : "").($hours ? $hours."h, " : "").($minutes ? $minutes."m, " : "").($seconds ? $seconds."s, " : ""), 0, -2);
        }
}

function addAmpUser($username, $password, $extension_low, $extension_high, $deptname, $sections) {
    global $db;
    $sql = "INSERT INTO ampusers (username, password, extension_low, extension_high, deptname, sections) VALUES (";
    $sql .= "'".$username."',";
    $sql .= "'".$password."',";
    $sql .= "'".$extension_low."',";
    $sql .= "'".$extension_high."',";
    $sql .= "'".$deptname."',";
    $sql .= "'".implode(";",$sections)."');";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().'<hr>'.$sql);
    }
}

function deleteAmpUser($username) {
    global $db;

    $sql = "DELETE FROM ampusers WHERE username = '".$username."'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }
}

function getAmpUsers() {
    global $db;

    $sql = "SELECT username FROM ampusers ORDER BY username";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
       die($results->getMessage());
    }
    return $results;
}

function getAmpAdminUsers() {
    global $db;

    $sql = "SELECT username FROM ampusers WHERE sections='*'";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
       die($results->getMessage());
    }
    return $results;
}

function getAmpUser($username) {
    global $db;

    $sql = "SELECT username, password, extension_low, extension_high, deptname, sections FROM ampusers WHERE username = '".$username."'";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
       die($results->getMessage());
    }

    if (count($results) > 0) {
        $user = array();
        $user["username"] = $results[0][0];
        $user["password"] = $results[0][1];
        $user["extension_low"] = $results[0][2];
        $user["extension_high"] = $results[0][3];
        $user["deptname"] = $results[0][4];
        $user["sections"] = explode(";",$results[0][5]);
        return $user;
    } else {
        return false;
    }
}

class ampuser {
    var $username;
    var $_password;
    var $_extension_high;
    var $_extension_low;
    var $_deptname;
    var $_sections;

    function ampuser($username) {
        $this->username = $username;
        if ($user = getAmpUser($username)) {
            $this->_password = $user["password"];
            $this->_extension_high = $user["extension_high"];
            $this->_extension_low = $user["extension_low"];
            $this->_deptname = $user["deptname"];
            $this->_sections = $user["sections"];
        } else {
            // user doesn't exist
            $this->_password = false;
            $this->_extension_high = "";
            $this->_extension_low = "";
            $this->_deptname = "";
            $this->_sections = array();
        }
    }

    /** Give this user full admin access
    */
    function setAdmin() {
        $this->_extension_high = "";
        $this->_extension_low = "";
        $this->_deptname = "";
        $this->_sections = array("*");
    }

    function checkPassword($password) {
        // strict checking so false will never match
        return ($this->_password === $password);
    }

    function checkSection($section) {
        // if they have * then it means all sections
        return in_array("*", $this->_sections) || in_array($section, $this->_sections);
    }
}

// returns true if extension is within allowed range
function checkRange($extension){
    $low = $_SESSION["user"]->_extension_low;
    $high = $_SESSION["user"]->_extension_high;
    if ((($extension >= $low) && ($extension <= $high)) || (empty($low) && empty($high)))
        return true;
    else
        return false;
}


//get unique voice menu numbers - returns 2 dimensional array
function getaas() {
    global $db;
    $dept = str_replace(' ','_',$_SESSION["user"]->_deptname);
    if (empty($dept)) $dept='%';  //if we are not restricted to dept (ie: admin), then display all AA menus
    $sql = "SELECT context,descr FROM extensions WHERE extension = 's' AND (application LIKE 'DigitTimeout' OR args LIKE 'TIMEOUT(digit)=3' OR args LIKE 'TIMEOUT(digit)=1') AND context LIKE '".$dept."aa_%' ORDER BY context";
    $unique_aas = $db->getAll($sql);
    if(DB::IsError($unique_aas)) {
       die('unique: '.$unique_aas->getMessage().'<hr>'.$sql);
    }
    return $unique_aas;
}


function getextens_acode() {
    $sip = getSip_acode();
    $iax = getIax_acode();
    $zap= getZap_acode();
    $results = array_merge((array)$sip,(array) $iax,(array)$zap);
    foreach($results as $result){
        if (checkRange($result[0])){
            $extens[] = array($result[0],$result[1],$result[2]);
        }
    }
    if (isset($extens)) sort($extens);
    return $extens;
}

function getSip_acode() {
    global $db;
    sipexists();
    $sql = "SELECT id,data FROM sip WHERE keyword = 'accountcode' AND data != '' ORDER BY id";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    //add value 'sip' to each array
    foreach ($results as $result) {
        $result[] = 'sip';
        $sip[] = $result;
    }
    return $sip;
}

function getIax_acode() {
    global $db;
    iaxexists();
    $sql = "SELECT id,data FROM iax WHERE keyword = 'accountcode' AND data != '' ORDER BY id";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    //add value 'iax' to each array
    foreach ($results as $result) {
        $result[] = 'iax';
        $iax[] = $result;
    }
    return $iax;
}

function getZap_acode() {
    global $db;
    zapexists();
    $sql = "SELECT id,data FROM zap WHERE keyword = 'accountcode' AND data != '' ORDER BY id";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    //add value 'zap' to each array
    foreach ($results as $result) {
        $result[] = 'zap';
        $zap[] = $result;
    }
    return $zap;
}


// get the existing extensions
// the returned arrays contain [0]:extension [1]:CID [2]:technology
function getextens() {
    $sip = getSip();
    $iax = getIax();
    $zap= getZap();
    $results = array_merge((array)$sip,(array)$iax,(array)$zap);
    foreach($results as $result){
        if (checkRange($result[0])){
            $extens[] = array($result[0],$result[1],$result[2]);
        }
    }
    if (isset($extens)) sort($extens);
    return $extens;
}

// get the existing extensions
// the returned arrays contain [1]:extension
function getextensa2b() {
    $sipa2b = getSipA2B();
    $results = array_merge((array)$sipa2b);
    foreach($results as $result){
        if (checkRange($result[0])){
            $extensa2b[] = array($result[0],$result[1],$result[2]);
        }
    }
    return $extensa2b;
}

function getSipA2B() {
    global $db3;
    $sql = "SELECT id,username FROM cc_sip_buddies ORDER BY username";
    $results = $db3->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    //add value 'sip' to each array
    foreach ($results as $result) {
        $result[] = 'sip';
        $sipa2b[] = $result;
    }
    return $sipa2b;
}

function getringtime($extdisplay) {
    global $db;
    $sql = "SELECT value FROM globals WHERE variable = 'RINGTIME$extdisplay'";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }
    foreach ($results as $result) {
        $ringtime = $result[0];
    }
    return $ringtime;
}

function getSip() {
    global $db;
    sipexists();
    $sql = "SELECT id,data FROM sip WHERE keyword = 'callerid' ORDER BY id";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    //add value 'sip' to each array
    foreach ($results as $result) {
        $result[] = 'sip';
        $sip[] = $result;
    }
    return $sip;
}

function getIax() {
    global $db;
    iaxexists();
    $sql = "SELECT id,data FROM iax WHERE keyword = 'callerid' ORDER BY id";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    //add value 'iax' to each array
    foreach ($results as $result) {
        $result[] = 'iax';
        $iax[] = $result;
    }
    return $iax;
}

function getZap() {
    global $db;
    zapexists();
    $sql = "SELECT id,data FROM zap WHERE keyword = 'callerid' ORDER BY id";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    //add value 'zap' to each array
    foreach ($results as $result) {
        $result[] = 'zap';
        $zap[] = $result;
    }
    return $zap;
}

//get the existing group extensions
function getgroups() {
    global $db;
    $sql = "SELECT DISTINCT extension,descr FROM extensions WHERE context = 'ext-group' GROUP by extension ORDER BY extension";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    foreach($results as $result){
        if (checkRange($result[0])){
            $extens[] = array($result[0],$result[1]);
            }
    }


    return $extens;
}

//get the existing queue extensions
function getmeetmes() {
    global $db;
    $sql = "SELECT exten,description FROM meetme ORDER BY exten";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    foreach($results as $result){
        if (checkRange($result[0])){
            $extens[] = array($result[0],$result[1]);
        }
    }
    return $extens;
}

function getmiscdest() {
    global $db;
    $sql = "SELECT DISTINCT extension,descr FROM extensions WHERE context = 'ext-miscdests' ORDER BY extension";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    foreach($results as $result){
        if (checkRange($result[0])){
            $extens[] = array($result[0],$result[1]);
        }
    }
    return $extens;
}

//get the existing queue extensions
function getqueues() {
    global $db;
    $sql = "SELECT extension,descr FROM extensions WHERE application = 'Queue' ORDER BY extension";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    foreach($results as $result){
        if (checkRange($result[0])){
            $extens[] = array($result[0],$result[1]);
        }
    }
    return $extens;
}

//get the existing did extensions
function getdids() {
    global $db;
    $sql = "SELECT extension FROM extensions WHERE context = 'ext-did' and priority ='1' ORDER BY extension";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }
    return $results;
}

//get goto in specified group
function getgroupgoto($grpexten) {
    global $db;
    $sql = "SELECT args FROM extensions WHERE extension = '".$grpexten."' AND (args LIKE 'ext-local,%,%' OR args LIKE 'vm,%' OR args LIKE 'aa_%,%,%' OR args LIKE 'ext-group,%,%' OR args LIKE 'from-pstn,s,1' OR args LIKE '%custom%')";
    $thisGRPgoto = $db->getAll($sql);
    if(DB::IsError($thisGRPgoto)) {
       die($thisGRPgoto->getMessage());
    }
    return $thisGRPgoto;
}


function getgroupinfo($grpexten, &$strategy, &$time, &$prefix, &$group, &$callerannounce, &$alertinfo, &$ringing, &$description) {
    global $db;
    $sql = "SELECT args FROM extensions WHERE context = 'ext-group' AND extension = '".$grpexten."' AND priority = '1'";
    $res = $db->getAll($sql);
    if(DB::IsError($res)) {
       die($res->getMessage());
    }

        $sql = "SELECT descr FROM extensions WHERE context = 'ext-group' AND extension = '".$grpexten."' AND priority = '1'";
        $resz = $db->getAll($sql);
        if(DB::IsError($resz)) {
            die($res->getMessage());
        }

    if (isset($res[0][0]) && preg_match("/^rg-group,(.*),(.*),(.*),(.*),(.*),(.*),(.*)$/", $res[0][0], $matches)) {
        $strategy = $matches[1];
        $time = $matches[2];
        $prefix = $matches[3];
        $group = $matches[4];
        $callerannounce = $matches[5];
        $alertinfo = $matches[6];
        $ringing = $matches[7];
        $description = $resz[0][0];
        return true;
    }

    return false;
}

//add to extensions table - used in callgroups.php
function addextensions($addarray) {
    global $db;
    $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ('".$addarray[0]."', '".$addarray[1]."', '".$addarray[2]."', '".$addarray[3]."', '".$addarray[4]."', ".sql_formattext($addarray[5])." , '".$addarray[6]."')";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().$sql);
    }
    return $result;
}

//delete extension from extensions table
function delextensions($context,$exten) {
    global $db;
    $sql = "DELETE FROM extensions WHERE context = '".$context."' AND `extension` = '".$exten."'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }
    return $result;
}

//tell application we need to reload asterisk
function needreload() {
    global $db;
    $sql = "UPDATE admin SET value = 'true' WHERE variable = 'need_reload'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }
}

//get info about auto-attendant
function aainfo($menu_id) {
    global $db;
    //do another select for all parts in this aa_
//    $sql = "SELECT * FROM extensions WHERE context = '".$dept."aa_".$menu_num."' ORDER BY extension";
    $sql = "SELECT * FROM extensions WHERE context = '".$menu_id."' ORDER BY extension";
    $aalines = $db->getAll($sql);
    if(DB::IsError($aalines)) {
        die('aalines: '.$aalines->getMessage());
    }
    return $aalines;
}

//get the version number
function getversion() {
    global $db;
    $sql = "SELECT value FROM admin WHERE variable = 'version'";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }
    return $results;
}

function extension_list() {
    $sql = "SELECT extension FROM extensions WHERE context = 'ext-local'";
    $results = sql($sql,"getAll");

    foreach($results as $result){
        if (checkRange($result[0])){
            $extens[] = array($result[0]);
        }
    }
    if (isset($extens)) {
        sort($extens);
        return $extens;
    } else {
        return null;
    }
}

function zapexists() {
    global $db;
    $sql = "CREATE TABLE IF NOT EXISTS `zap` (`id` bigint(11) NOT NULL default '-1',`keyword`varchar(20) NOT NULL default '',`data`varchar(150) NOT NULL default '',`flags` int(1) NOT NULL default '0',PRIMARY KEY (`id`,`keyword`))";
    $results = $db->query($sql);
}

function addzap($account,$callerid,$action) {
    zapexists();
    global $db;

    if ($action == "add") {

        $devices = extension_list();
        if (is_array($devices)) {
            foreach($devices as $device) {
                if ($device[0] === $account) {
                    echo "<script>javascript:alert('"._("This ZAP Extension [").$device[0].("] is already in use")."');</script>";
                    return false;
                }
            }
        }

    }

    $zapfields = array(
    array($account,'account',$account),
    array($account,'context',(isset($_REQUEST['context']))?$_REQUEST['context']:''),
    array($account,'mailbox',(isset($_REQUEST['mailbox']))?$_REQUEST['mailbox']:''),
    array($account,'callerid',$callerid),
    array($account,'signalling',(isset($_REQUEST['signalling']))?$_REQUEST['signalling']:'fxo_ks'),
    array($account,'echocancel',(isset($_REQUEST['echocancel']))?$_REQUEST['echocancel']:'yes'),
    array($account,'echocancelwhenbridged',(isset($_REQUEST['echocancelwhenbridged']))?$_REQUEST['echocancelwhenbridged']:'yes'),
    array($account,'echotraining',(isset($_REQUEST['echotraining']))?$_REQUEST['echotraining']:'400'),
    array($account,'busydetect',(isset($_REQUEST['busydetect']))?$_REQUEST['busydetect']:'yes'),
    array($account,'busycount',(isset($_REQUEST['busycount']))?$_REQUEST['busycount']:'5'),
    array($account,'callprogress',(isset($_REQUEST['callprogress']))?$_REQUEST['callprogress']:'no'),
    array($account,'callwaiting',(isset($_REQUEST['callwaiting']))?$_REQUEST['callwaiting']:'yes'),
    array($account,'restrictcid',(isset($_REQUEST['restrictcid']))?$_REQUEST['restrictcid']:'no'),
    array($account,'callwaitingcallerid',(isset($_REQUEST['callwaitingcallerid']))?$_REQUEST['callwaitingcallerid']:'no'),
    array($account,'threewaycalling',(isset($_REQUEST['threewaycalling']))?$_REQUEST['threewaycalling']:'yes'),
    array($account,'transfer',(isset($_REQUEST['transfer']))?$_REQUEST['transfer']:'yes'),
    array($account,'cancallforward',(isset($_REQUEST['cancallforward']))?$_REQUEST['cancallforward']:'yes'),
    array($account,'callreturn',(isset($_REQUEST['callreturn']))?$_REQUEST['callreturn']:'yes'),
    array($account,'relaxdtmf',(isset($_REQUEST['relaxdtmf']))?$_REQUEST['relaxdtmf']:'yes'),
    array($account,'callgroup',(isset($_REQUEST['callgroup']))?$_REQUEST['callgroup']:''),
    array($account,'pickupgroup',(isset($_REQUEST['pickupgroup']))?$_REQUEST['pickupgroup']:''),
    array($account,'record_in',(isset($_REQUEST['record_in']))?$_REQUEST['record_in']:'Never'),
    array($account,'record_out',(isset($_REQUEST['record_out']))?$_REQUEST['record_out']:'Never'),
    array($account,'accountcode',(isset($_REQUEST['accountcode']))?$_REQUEST['accountcode']:''),
    array($account,'nocall',(isset($_REQUEST['nocall']))?$_REQUEST['nocall']:''),
    array($account,'allowcall',(isset($_REQUEST['allowcall']))?$_REQUEST['allowcall']:''),
    array($account,'language',(isset($_REQUEST['language']))?$_REQUEST['language']:''),
    array($account,'channel',(isset($_REQUEST['channel']))?$_REQUEST['channel']:''));

    $compiled = $db->prepare('INSERT INTO zap (id, keyword, data) values (?,?,?)');
    $result = $db->executeMultiple($compiled,$zapfields);
    if(DB::IsError($result)) {
        die($result->getMessage()."<br><br>error adding to ZAP table");
    }

    //add E<enten>=ZAP to global vars (appears in extensions_additional.conf)
    $sql = "INSERT INTO globals VALUES ('E$account', 'ZAP')";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().$sql);
    }

    //add ZAPCHAN_<exten>=<zapchannel> to global vars. Needed in dialparties.agi to decide channel number without hitting the database.
    $zapchannel=$_REQUEST['channel'];
    $sql = "INSERT INTO globals VALUES ('ZAPCHAN_$account', '$zapchannel')";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().$sql);
    }

    //add ECID<enten> to global vars if using outbound CID
    if ($_REQUEST['outcid'] != '') {
        $outcid = $_REQUEST['outcid'];
        $sql = "INSERT INTO globals VALUES ('ECID$account', '$outcid')";
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage().$sql);
        }
    }

        return true;
}

//create iax if it doesn't exist
function iaxexists() {
    global $db;
    $sql = "CREATE TABLE IF NOT EXISTS `iax` (`id` bigint(11) NOT NULL default '-1',`keyword` varchar(20) NOT NULL default '',`data` varchar(150) NOT NULL default '',`flags` int(1) NOT NULL default '0',PRIMARY KEY  (`id`,`keyword`))";
    $results = $db->query($sql);
}

//add to iax table
function addiax($account,$callerid,$action) {
    iaxexists();
    global $db;

    if ($action == "add") {

        $devices = extension_list();
        if (is_array($devices)) {
            foreach($devices as $device) {
                if ($device[0] === $account) {
                    echo "<script>javascript:alert('"._("This IAX Extension [").$device[0].("] is already in use")."');</script>";
                    return false;
                }
            }
        }
    }

    $iaxfields = array(array($account,'account',$account),
    array($account,'accountcode',(isset($_REQUEST['accountcode']))?$_REQUEST['accountcode']:''),
    array($account,'secret',(isset($_REQUEST['secret']))?$_REQUEST['secret']:''),
    array($account,'transfer',(isset($_REQUEST['transfer']))?$_REQUEST['transfer']:'yes'),
    array($account,'context',(isset($_REQUEST['context']))?$_REQUEST['context']:''),
    array($account,'host',(isset($_REQUEST['host']))?$_REQUEST['host']:'dynamic'),
    array($account,'type',(isset($_REQUEST['type']))?$_REQUEST['type']:'friend'),
    array($account,'mailbox',(isset($_REQUEST['mailbox']))?$_REQUEST['mailbox']:''),
    array($account,'username',(isset($_REQUEST['username']))?$_REQUEST['username']:''),
    array($account,'port',(isset($_REQUEST['port']))?$_REQUEST['port']:'4569'),
    array($account,'qualify',(!empty($_REQUEST['qualify']))?$_REQUEST['qualify']:'no'),
    array($account,'disallow',(isset($_REQUEST['disallow']))?$_REQUEST['disallow']:''),
    array($account,'allow',(isset($_REQUEST['allow']))?$_REQUEST['allow']:''),
    array($account,'record_in',(isset($_REQUEST['record_in']))?$_REQUEST['record_in']:'Never'),
    array($account,'record_out',(isset($_REQUEST['record_out']))?$_REQUEST['record_out']:'Never'),
    array($account,'nocall',(isset($_REQUEST['nocall']))?$_REQUEST['nocall']:''),
    array($account,'allowcall',(isset($_REQUEST['allowcall']))?$_REQUEST['allowcall']:''),
    array($account,'language',(isset($_REQUEST['language']))?$_REQUEST['language']:''),
    array($account,'callerid',$callerid));

    $compiled = $db->prepare('INSERT INTO iax (id, keyword, data) values (?,?,?)');
    $result = $db->executeMultiple($compiled,$iaxfields);
    if(DB::IsError($result)) {
        die($result->getMessage()."<br><br>error adding to IAX table");
    }

    //add E<enten>=IAX2 to global vars (appears in extensions_additional.conf)
    $sql = "INSERT INTO globals VALUES ('E$account', 'IAX2')";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().$sql);
    }

    //add ECID<enten> to global vars if using outbound CID
    if ($_REQUEST['outcid'] != '') {
        $outcid = $_REQUEST['outcid'];
        $sql = "INSERT INTO globals VALUES ('ECID$account', '$outcid')";
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage().$sql);
        }
    }

        return true;
}

//create sip if it doesn't exist
function sipexists() {
    global $db;
    $sql = "CREATE TABLE IF NOT EXISTS `sip` (`id` bigint(11) NOT NULL default '-1',`keyword` varchar(20) NOT NULL default '',`data` varchar(150) NOT NULL default '',`flags` int(1) NOT NULL default '0',PRIMARY KEY  (`id`,`keyword`))";
    $results = $db->query($sql);
}

function addsipauto($account_start,$account_end) {
    sipexists();
    global $db;

    for ($i=$account_start;$i<=$account_end;$i++) {
        $sipext_array[] = $i;
        }

    foreach (extension_list() as $row) {
        $extension_list[] = $row[0];
    }

    $existant_extensions = array_intersect($sipext_array, $extension_list);

                    if (count($existant_extensions) > 0) {
                    echo "<script>javascript:alert('"._("One or more SIP Extension in your range is already in use!")."');</script>";
                    return false;
                }

    $sipfields = array(
    array('accountcode',(isset($_REQUEST['accountcode']))?$_REQUEST['accountcode']:''),
    array('secret',(isset($_REQUEST['secret']))?$_REQUEST['secret']:''),
    array('canreinvite',(isset($_REQUEST['canreinvite']))?$_REQUEST['canreinvite']:'no'),
    array('context',(isset($_REQUEST['context']))?$_REQUEST['context']:'from-internal'),
    array('dtmfmode',(isset($_REQUEST['dtmfmode']))?$_REQUEST['dtmfmode']:'rfc2833'),
    array('host',(isset($_REQUEST['host']))?$_REQUEST['host']:'dynamic'),
    array('type',(isset($_REQUEST['type']))?$_REQUEST['type']:'friend'),
    array('mailbox',(isset($_REQUEST['mailbox']))?$_REQUEST['mailbox']:''),
    array('nat',(isset($_REQUEST['nat']))?$_REQUEST['nat']:'no'),
    array('port',(isset($_REQUEST['port']))?$_REQUEST['port']:'5060'),
    array('qualify',(!empty($_REQUEST['qualify']))?$_REQUEST['qualify']:'no'),
    array('callgroup',(isset($_REQUEST['callgroup']))?$_REQUEST['callgroup']:''),
    array('pickupgroup',(isset($_REQUEST['pickupgroup']))?$_REQUEST['pickupgroup']:''),
    array('disallow',(isset($_REQUEST['disallow']))?$_REQUEST['disallow']:''),
    array('allow',(isset($_REQUEST['allow']))?$_REQUEST['allow']:''),
    array('record_in',(isset($_REQUEST['record_in']))?$_REQUEST['record_in']:'Never'),
    array('record_out',(isset($_REQUEST['record_out']))?$_REQUEST['record_out']:'Never'),
    array('nocall',(isset($_REQUEST['nocall']))?$_REQUEST['nocall']:''),
    array('allowcall',(isset($_REQUEST['allowcall']))?$_REQUEST['allowcall']:''),
    array('rob',(isset($_REQUEST['rob']))?$_REQUEST['rob']:'Never'),
    array('cw',(isset($_REQUEST['cw']))?$_REQUEST['cw']:'Never'),
    array('allowsubscribe',(isset($_REQUEST['allowsubscribe']))?$_REQUEST['allowsubscribe']:''),
    array('call-limit',(!empty($_REQUEST['calllimit']))?$_REQUEST['calllimit']:'99'),
    array('videosupport',(isset($_REQUEST['videosupport']))?$_REQUEST['videosupport']:''),
    array('t38pt_udptl',(isset($_REQUEST['t38pt_udptl']))?$_REQUEST['t38pt_udptl']:''),
    array('language',(isset($_REQUEST['language']))?$_REQUEST['language']:''),
    array('subscribecontext',(isset($_REQUEST['subscribecontext']))?$_REQUEST['subscribecontext']:'ext-local'));
    
    $cidnuminc = $_REQUEST['cidnuminc'];
    $outcidname = $_REQUEST['outcidname'];
    $outcidnum = $_REQUEST['outcidnum'];
    $directdid = $_REQUEST['directdid'];
    $ringtime = $_REQUEST['ringtime'];
    $vm = $_REQUEST['vm'];
    $goto = isset($_REQUEST['goto0'])?$_REQUEST['goto0']:'';

        if ($cidnuminc != "") {
                settype($cidnuminc, "integer");
            } else {
                $cidnuminc = "";
            }

    $mailb = "novm";
    $name = $_REQUEST['name'];

    for ($accountX= $account_start; ; $accountX++) {

       if ($accountX == $account_end + 1) {
       break;
        }

        if ($outcidname != "" && $outcidnum != "") {

                $outcid = ''.$outcidname.' '.'<'.$outcidnum.$cidnuminc.'>';

                    } else {

                        $outcid = "";

                    }

        $hint = "SIP/".$accountX;
        $callerid = '"'.$name.'" '.'<'.$accountX.'>';

        $compiled = $db->prepare("INSERT INTO sip (id, keyword, data) VALUES ('$accountX',?,?)");
        $result = $db->executeMultiple($compiled,$sipfields);
        if(DB::IsError($result)) {
            die($result->getMessage()."<br><br>".'Error to Adding to SIP table');
        }

        $sql = "INSERT INTO sip (id, keyword, data) VALUES ('$accountX', 'account', '$accountX')";
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage()."<br><br>".'Error to Adding to SIP table');
        }

        $sql = "INSERT INTO sip (id, keyword, data) VALUES ('$accountX', 'username', '$accountX')";
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage()."<br><br>".'Error to Adding to SIP table');
        }

        $sql = "INSERT INTO sip (id, keyword, data) VALUES ('$accountX', 'callerid', '$callerid')";
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage()."<br><br>".'Error to Adding to SIP table');
        }

        $sql = "INSERT INTO globals VALUES ('E$accountX', 'SIP')";
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage()."<br><br>".'Error to Adding to SIP table');
        }

        if ($outcidname != "" && $outcidnum != "") {

            $sql = "INSERT INTO globals VALUES ('ECID$accountX', '$outcid')";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Adding to Globals table');
            }
        }

        if ($vm == "destination") {

            $mailb = "jump";
            $gotojumpto = setGotoJumpTo($goto,0);
            $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ('ext-local', '".$accountX."', '1', 'Macro', 'exten-vm,".$mailb.",".$accountX.','.$gotojumpto."', NULL , '0')";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Adding to Extensions table');
            }

            $sql = "INSERT INTO extensions (context, extension, priority, application) VALUES ('ext-local', '".$accountX."', 'hint', '".$hint."')";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Adding to Extensions table');
            }

            $sql = "INSERT INTO globals VALUES ('RINGTIME$accountX', '$ringtime')";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'error adding to Globals table');
            }

            } else {

                $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ('ext-local', '".$accountX."', '1', 'Macro', 'exten-vm,".$mailb.",".$accountX."', NULL , '0')";
                $result = $db->query($sql);
                if(DB::IsError($result)) {
                    die($result->getMessage()."<br><br>".'Error to Adding to Extensions table');
                }

                $sql = "INSERT INTO extensions (context, extension, priority, application) VALUES ('ext-local', '".$accountX."', 'hint', '".$hint."')";
                $result = $db->query($sql);
                if(DB::IsError($result)) {
                    die($result->getMessage()."<br><br>".'Error to Adding to Extensions table');
                }
            }

        if ($directdid != "") {

            $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ('ext-did', '$directdid$accountX', '01', 'Set', 'FROM_DID=$directdid$accountX', '$accountX' , '0')";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Adding to Extensions table');
            }

            $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ('ext-did', '$directdid$accountX', '02', 'Set', 'FAX_RX=disabled', '$accountX' , '0')";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Adding to Extensions table');
            }

            $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ('ext-did', '$directdid$accountX', '03', 'Goto', 'ext-local,$accountX,1', '$accountX' , '0')";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Adding to Extensions table');
            }

            $sql="INSERT INTO incoming (cidnum,extension,destination,faxexten,faxemail,faxemail2,answer,wait,CIDName,privacyman,alertinfo,channel,ringing) values ('','$directdid$accountX','ext-local,$accountX,1','disabled','','','0','0','','0','','','')";
            $results = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Adding to Incoming table');
            }

        }

        setrecordingstatus($accountX, "In", $_REQUEST['record_in']);
        setrecordingstatus($accountX, "Out", $_REQUEST['record_out']);

        setnocallstatus($accountX, $_REQUEST['nocall'], 'NOCALL');
        setnocallstatus($accountX, $_REQUEST['allowcall'], 'ALLOWCALL');

        setrobstatus($accountX, $_REQUEST['rob'],'write');
        setcwstatus($accountX, $_REQUEST['cw'],'write');

        if (isset($cidnuminc)) {
                $cidnuminc = $cidnuminc + 1;
        }

    }
        return true;
}


function deletesipauto($account_start,$account_end) {
    global $db;

    $vmcontext = "default";
    $extwithvm = false;

        for ($accountX= $account_start; ; $accountX++) {

            if ($accountX == $account_end + 1) {

                if ($extwithvm == true) {
                    echo "<script>javascript:alert('"._("One or more SIP Extension in your range are not deleted because are associated with a voicemail!")."');</script>";
                }

                break;

            }

                $parsevc = parseconfvoicemail($vmcontext,$accountX);

                    if ($parsevc == true) {

                            $extwithvm = true;

                    } else {

                        $sql = "DELETE FROM sip WHERE id = '$accountX'";
                        $result = $db->query($sql);
                        if(DB::IsError($result)) {
                            die($result->getMessage()."<br><br>".'Error to Delete SIP table');
                        }

                        $sql = "DELETE FROM globals WHERE variable = 'E$accountX'";
                        $result = $db->query($sql);
                        if(DB::IsError($result)) {
                            die($result->getMessage()."<br><br>".'Error to Delete GLOBALS table');
                        }

                        $sql = "DELETE FROM globals WHERE variable = 'RINGTIME$accountX'";
                        $result = $db->query($sql);
                        if(DB::IsError($result)) {
                            die($result->getMessage()."<br><br>".'Error to Delete GLOBALS table');
                        }

                        $sql = "DELETE FROM globals WHERE variable = 'ECID$accountX'";
                        $result = $db->query($sql);
                        if(DB::IsError($result)) {
                            die($result->getMessage()."<br><br>".'Error to Delete GLOBALS table');
                        }

                        $sql = "DELETE FROM extensions WHERE context = 'ext-local' AND extension = '".$accountX."'";
                        $result = $db->query($sql);
                        if(DB::IsError($result)) {
                            die($result->getMessage()."<br><br>".'Error to Delete EXTENSIONS table');
                        }

                        $sql="DELETE FROM extensions WHERE context = 'ext-did' AND descr = '$accountX'";
                        $results = $db->query($sql);
                        if(DB::IsError($result)) {
                            die($result->getMessage()."<br><br>".'Error to Deleting EXTENSIONS table');
                        }

                        $sql="DELETE FROM incoming WHERE destination = 'ext-local,$accountX,1'";
                        $results = $db->query($sql);
                        if(DB::IsError($result)) {
                            die($result->getMessage()."<br><br>".'Error to Deleting INCOMING table');
                        }

                        deleteastdb($accountX);

                    }
    }

        return true;
}


function parseconfvoicemail($vmcontext,$extdisplay) {

    $uservm = getVoicemail();
    $vmcontexts = array_keys($uservm);
    foreach    ($vmcontexts as $vmcontext) {
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
                    return true;
            }
    }

}

function updatesipauto($account_start,$account_end) {
    sipexists();
    global $db;

        for ($i=$account_start;$i<=$account_end;$i++) {
        $sipext_array[] = $i;
        }

    foreach (extension_list() as $row) {
        $extension_list[] = $row[0];
    }

    $diff_extensions = array_diff($sipext_array, $extension_list);

    if (count($diff_extensions) != 0) {
        echo "<script>javascript:alert('"._("One or more SIP Extension in your range do not exist, please check your range!")."');</script>";
        return false;
    }

    $sipfields = array(
    array($_REQUEST['accountcode'],'accountcode'),
    array($_REQUEST['secret'],'secret'),
    array($_REQUEST['canreinvite'],'canreinvite'),
    array($_REQUEST['context'],'context'),
    array($_REQUEST['dtmfmode'],'dtmfmode'),
    array($_REQUEST['host'],'host'),
    array($_REQUEST['type'],'type'),
    array($_REQUEST['mailbox'],'mailbox'),
    array($_REQUEST['nat'],'nat'),
    array($_REQUEST['port'],'port'),
    array($_REQUEST['qualify'],'qualify'),
    array($_REQUEST['callgroup'],'callgroup'),
    array($_REQUEST['pickupgroup'],'pickupgroup'),
    array($_REQUEST['disallow'],'disallow'),
    array($_REQUEST['allow'],'allow'),
    array($_REQUEST['record_in'],'record_in'),
    array($_REQUEST['record_out'],'record_out'),
    array($_REQUEST['nocall'],'nocall'),
    array($_REQUEST['allowcall'],'allowcall'),
    array($_REQUEST['rob'],'rob'),
    array($_REQUEST['cw'],'cw'),
    array($_REQUEST['allowsubscribe'],'allowsubscribe'),
    array($_REQUEST['calllimit'],'call-limit'),
    array($_REQUEST['videosupport'],'videosupport'),
    array($_REQUEST['t38pt_udptl'],'t38pt_udptl'),
    array($_REQUEST['language'],'language'),
    array($_REQUEST['subscribecontext'],'subscribecontext'));

    $cidnuminc = $_REQUEST['cidnuminc'];
    $outcidname = $_REQUEST['outcidname'];
    $outcidnum = $_REQUEST['outcidnum'];
    $directdid = $_REQUEST['directdid'];
    $ringtime = $_REQUEST['ringtime'];

    if ($cidnuminc != "") {
                settype($cidnuminc, "integer");
            } else {
                $cidnuminc = "";
            }

    $name = $_REQUEST['name'];

    foreach ($sipfields as $row) {
        if ($row[0] != '') {
            $sipfields_cleaned[] = array($row[0],$row[1]);
        }
    }

    for ($accountX= $account_start; ; $accountX++) {

        if ($accountX == $account_end + 1) {
            break;
        }

        $outcid = ''.$outcidname.' '.'<'.$outcidnum.$cidnuminc.'>';
        $callerid = '"'.$name.'" '.'<'.$accountX.'>';

        $compiled = $db->prepare("UPDATE `sip` SET `data` = ? WHERE `id` = $accountX AND `keyword` = ? LIMIT 1");
        $result = $db->executeMultiple($compiled,$sipfields_cleaned);
        if(DB::IsError($result)) {
            die($result->getMessage()."<br><br>".'Error to Updating SIP table');
        }


        if ($name != "") {

            $sql = "UPDATE `sip` SET `data` = '$callerid' WHERE `id` = $accountX AND `keyword` = 'callerid' LIMIT 1";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Updating SIP table');
            }

        }

        if ($outcid != " <>") {

            $sql = "UPDATE `globals` SET `value` = '$outcid' WHERE `variable` = 'ECID$accountX' LIMIT 1";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Updating Globals table');
            }

        }

        if ($directdid != "") {


            $sql="DELETE FROM extensions WHERE context = 'ext-did' AND descr = '$accountX'";
            $results = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Deleting Extensions table');
            }

            $sql="DELETE FROM incoming WHERE destination = 'ext-local,$accountX,1'";
            $results = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Deleting Incoming table');
            }

            $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ('ext-did', '$directdid$accountX', '01', 'Set', 'FROM_DID=$directdid$accountX', '$accountX' , '0')";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Updating Extensions table');
            }

            $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ('ext-did', '$directdid$accountX', '02', 'Set', 'FAX_RX=disabled', '$accountX' , '0')";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Updating Extensions table');
            }

            $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ('ext-did', '$directdid$accountX', '03', 'Goto', 'ext-local,$accountX,1', '$accountX' , '0')";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Updating Extensions table');
            }

            $sql="INSERT INTO incoming (cidnum,extension,destination,faxexten,faxemail,faxemail2,answer,wait,CIDName,privacyman,alertinfo,channel,ringing) values ('','$directdid$accountX','ext-local,$accountX,1','disabled','','','0','0','','0','','','')";
            $results = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Updating Incoming table');
            }

        }

       if ($ringtime != "") {

            if ($ringtime == "default") {

                $sql = "DELETE FROM globals WHERE variable = 'RINGTIME$accountX'";
                $result = $db->query($sql);
                if(DB::IsError($result)) {
                    die($result->getMessage()."<br><br>".'Error to Delete GLOBALS table');
                }
        
            } else {
        
                    $sql = "DELETE FROM globals WHERE variable = 'RINGTIME$accountX'";
                    $result = $db->query($sql);
                    if(DB::IsError($result)) {
                        die($result->getMessage()."<br><br>".'Error to Delete GLOBALS table');
                    }
                    $sql = "INSERT INTO globals VALUES ('RINGTIME$accountX', '$ringtime')";
                    $result = $db->query($sql);
                    if(DB::IsError($result)) {
                        die($result->getMessage()."<br><br>".'error adding to Globals table');
                    }
            }
        
        }

        setrecordingstatus($accountX, "In", $_REQUEST['record_in']);
        setrecordingstatus($accountX, "Out", $_REQUEST['record_out']);

        setnocallstatus($accountX, $_REQUEST['nocall'], 'NOCALL');
        setnocallstatus($accountX, $_REQUEST['allowcall'], 'ALLOWCALL');

        setrobstatus($accountX, $_REQUEST['rob'],'write');
        setcwstatus($accountX, $_REQUEST['cw'],'write');

        if ($outcid != " <>") {
                $cidnuminc = $cidnuminc + 1;
        }

}
        return true;
}



//add to sip table
function addsip($account,$callerid,$action) {
    sipexists();
    global $db;

    if ($action == "add") {

        $devices = extension_list();
        if (is_array($devices)) {
            foreach($devices as $device) {
                if ($device[0] === $account) {
                    echo "<script>javascript:alert('"._("This SIP Extension [").$device[0].("] is already in use")."');</script>";
                    return false;
                }
            }
        }

    }

    $sipfields = array(array($account,'account',$account),
    array($account,'accountcode',(isset($_REQUEST['accountcode']))?$_REQUEST['accountcode']:''),
    array($account,'secret',(isset($_REQUEST['secret']))?$_REQUEST['secret']:''),
    array($account,'canreinvite',(isset($_REQUEST['canreinvite']))?$_REQUEST['canreinvite']:'no'),
    array($account,'context',(isset($_REQUEST['context']))?$_REQUEST['context']:'from-internal'),
    array($account,'dtmfmode',(isset($_REQUEST['dtmfmode']))?$_REQUEST['dtmfmode']:'rfc2833'),
    array($account,'host',(isset($_REQUEST['host']))?$_REQUEST['host']:'dynamic'),
    array($account,'type',(isset($_REQUEST['type']))?$_REQUEST['type']:'friend'),
    array($account,'mailbox',(isset($_REQUEST['mailbox']))?$_REQUEST['mailbox']:''),
    array($account,'username',(isset($_REQUEST['username']))?$_REQUEST['username']:''),
    array($account,'nat',(isset($_REQUEST['nat']))?$_REQUEST['nat']:'no'),
    array($account,'port',(isset($_REQUEST['port']))?$_REQUEST['port']:'5060'),
    array($account,'qualify',(!empty($_REQUEST['qualify']))?$_REQUEST['qualify']:'no'),
    array($account,'callgroup',(isset($_REQUEST['callgroup']))?$_REQUEST['callgroup']:''),
    array($account,'pickupgroup',(isset($_REQUEST['pickupgroup']))?$_REQUEST['pickupgroup']:''),
    array($account,'disallow',(isset($_REQUEST['disallow']))?$_REQUEST['disallow']:''),
    array($account,'allow',(isset($_REQUEST['allow']))?$_REQUEST['allow']:''),
    array($account,'record_in',(isset($_REQUEST['record_in']))?$_REQUEST['record_in']:'Never'),
    array($account,'record_out',(isset($_REQUEST['record_out']))?$_REQUEST['record_out']:'Never'),
    array($account,'nocall',(isset($_REQUEST['nocall']))?$_REQUEST['nocall']:''),
    array($account,'allowcall',(isset($_REQUEST['allowcall']))?$_REQUEST['allowcall']:''),
    array($account,'subscribecontext',(isset($_REQUEST['subscribecontext']))?$_REQUEST['subscribecontext']:'ext-local'),
    array($account,'rob',(isset($_REQUEST['rob']))?$_REQUEST['rob']:'Never'),
    array($account,'cw',(isset($_REQUEST['cw']))?$_REQUEST['cw']:'Never'),
    array($account,'allowsubscribe',(isset($_REQUEST['allowsubscribe']))?$_REQUEST['allowsubscribe']:''),
    array($account,'call-limit',(!empty($_REQUEST['calllimit']))?$_REQUEST['calllimit']:'99'),
    array($account,'videosupport',(isset($_REQUEST['videosupport']))?$_REQUEST['videosupport']:''),
    array($account,'t38pt_udptl',(isset($_REQUEST['t38pt_udptl']))?$_REQUEST['t38pt_udptl']:''),
    array($account,'language',(isset($_REQUEST['language']))?$_REQUEST['language']:''),
    array($account,'callerid',$callerid));


    $compiled = $db->prepare('INSERT INTO sip (id, keyword, data) values (?,?,?)');
    $result = $db->executeMultiple($compiled,$sipfields);
    if(DB::IsError($result)) {
        die($result->getMessage()."<br><br>".'error adding to SIP table');
    }

    $sql = "INSERT INTO globals VALUES ('E$account', 'SIP')";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage()."<br><br>".'error adding to Globals table');
    }


    if ($_REQUEST['outcid'] != '') {

        $outcid = $_REQUEST['outcid'];
        $sql = "INSERT INTO globals VALUES ('ECID$account', '$outcid')";
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage()."<br><br>".'error adding to Globals table');
        }

    }

    if ($_REQUEST['ringtime'] != '') {

        $ringtime = $_REQUEST['ringtime'];
        $sql = "INSERT INTO globals VALUES ('RINGTIME$account', '$ringtime')";
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage()."<br><br>".'error adding to Globals table');
        }

    }

    return true;
}

function addaccount($account,$mailb,$hint = '') {
    extensionsexists();
    global $db;
    $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ('ext-local', '".$account."', '1', 'Macro', 'exten-vm,".$mailb.",".$account."', NULL , '0')";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage()."<br><br>".'error adding to Extensions table');
        }

    if ($hint != '') {
        $sql = "INSERT INTO extensions (context, extension, priority, application) VALUES ('ext-local', '".$account."', 'hint', '".$hint."')";
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage()."<br><br>".'error adding to Extensions table');
        }
    }

        return $result;
}

function addaccountjump($account,$mailb,$hint = '',$gotojumpto) {
    extensionsexists();
    global $db;
    $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ('ext-local', '".$account."', '1', 'Macro', 'exten-vm,".$mailb.",".$account.','.$gotojumpto."', NULL , '0')";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage()."<br><br>".'error adding to Extensions table');
        }

    if ($hint != '') {
        $sql = "INSERT INTO extensions (context, extension, priority, application) VALUES ('ext-local', '".$account."', 'hint', '".$hint."')";
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage()."<br><br>".'error adding to Extensions table');
        }
    }

        return $result;
}

//create extensions if it doesn't exist
function extensionsexists() {
    global $db;
    $sql = "CREATE TABLE IF NOT EXISTS `extensions` (`context` varchar(20) NOT NULL default 'default',`extension` varchar(20) NOT NULL default '',`priority` int(2) NOT NULL default '1',`application` varchar(20) NOT NULL default '',`args` varchar(50) default NULL,`descr` text,`flags` int(1) NOT NULL default '0',PRIMARY KEY  (`context`,`extension`,`priority`))";
    $results = $db->query($sql);
}

//get all rows relating to selected account
function exteninfo($extdisplay) {
    global $db;
    $sql = "SELECT * FROM sip WHERE id = '$extdisplay'";
    $thisExten = $db->getAll($sql);
    if(DB::IsError($thisExten)) {
       die($thisExten->getMessage());
    }
    if (count($thisExten) > 0) {
        $thisExten[] = array('$extdisplay','tech','sip','info');  //add this to the array - as it doesn't exist in the table
    } else {
    //if (count($thisExten) == 0) {  //if nothing was pulled from sip, then it must be iax
        $sql = "SELECT * FROM iax WHERE id = '$extdisplay'";
        $thisExten = $db->getAll($sql);
        if(DB::IsError($thisExten)) {
           die($thisExten->getMessage());
        }
        if (count($thisExten) > 0) {
            $thisExten[] = array('$extdisplay','tech','iax2','info');  //add this to the array - as it doesn't exist in the table
        } else {
            $sql = "SELECT * FROM zap WHERE id = '$extdisplay'";
            $thisExten = $db->getAll($sql);
            if(DB::IsError($thisExten)) {
                die($thisExten->getMessage());
            }
            if (count($thisExten) > 0) {
                $thisExten[] = array('$extdisplay','tech','zap','info');
            }
        }
    }
    //get var containing external cid
    $sql = "SELECT * FROM globals WHERE variable = 'ECID$extdisplay'";
    $ecid = $db->getAll($sql);
    if(DB::IsError($ecid)) {
       die($ecid->getMessage());
    }
    $thisExten[] = array('$extdisplay','1outcid',$ecid[0][1],'info');
    sort($thisExten);

    return $thisExten;
}


//Delete an extension (extensions.php)
function delExten($extdisplay,$action) {
    global $db;
    $sql = "DELETE FROM sip WHERE id = '$extdisplay'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().$sql);
    }
    $sql = "DELETE FROM iax WHERE id = '$extdisplay'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().$sql);
    }
    $sql = "DELETE FROM zap WHERE id = '$extdisplay'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().$sql);
    }
    $sql = "DELETE FROM globals WHERE variable = 'E$extdisplay'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().$sql);
    }
    $sql = "DELETE FROM globals WHERE variable = 'ECID$extdisplay'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().$sql);
    }
    $sql = "DELETE FROM globals WHERE variable = 'RINGTIME$extdisplay'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().$sql);
    }
    $sql = "DELETE FROM globals WHERE variable = 'ZAPCHAN_$extdisplay'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().$sql);
    }

    if ($action == 'deletefromextension') {

        $sql = "SELECT descr FROM extensions WHERE descr = '$extdisplay'";
        $getextensnumb = $db->getAll($sql);
        if(DB::IsError($results)) {
                die($result->getMessage()."<br><br>".'Error to Query Extensions table');
        }

        if (count($getextensnumb) > 0) {

            $sql="DELETE FROM extensions WHERE context = 'ext-did' AND descr = '$extdisplay'";
            $results = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Deleting Extensions table');
            }

            $sql="DELETE FROM incoming WHERE destination = 'ext-local,$extdisplay,1'";
            $results = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage()."<br><br>".'Error to Deleting Incoming table');
            }
        }
    }
}


//add trunk to outbound-trunks context
function addOutTrunk($trunknum) {
    extensionsexists();
    global $db;
    $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ('outbound-trunks', '_\${DIAL_OUT_".$trunknum."}.', '1', 'Macro', 'dialout,".$trunknum.",\${EXTEN}', NULL , '0')";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage()."<br><br>".$sql);
    }
    return $result;
}


//write the OUTIDS global variable (used in dialparties.agi)
function writeoutids() {
    global $db;
    $sql = "SELECT variable FROM globals WHERE variable LIKE 'OUT\\\_%'"; // we have to escape _ for mysql: normally a wildcard
    $unique_trunks = $db->getAll($sql);
    if(DB::IsError($unique_trunks)) {
       die('unique: '.$unique_trunks->getMessage());
    }
    foreach ($unique_trunks as $unique_trunk) {
        $outid = strtok($unique_trunk[0],"_");
        $outid = strtok("_");
        $outids .= trim($outid) ."/";
    }
    $sql = "UPDATE globals SET value = '$outids' WHERE variable = 'DIALOUTIDS'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }
}

//get unique trunks
function gettrunks() {
    global $db;
    $sql = "SELECT * FROM globals WHERE variable LIKE 'OUT\\\_%' ORDER BY value"; // we have to escape _ for mysql: normally a wildcard
    $unique_trunks = $db->getAll($sql);
    if(DB::IsError($unique_trunks)) {
       die('unique: '.$unique_trunks->getMessage());
    }
    //if no trunks have ever been defined, then create the proper variables with the default zap trunk
    if (count($unique_trunks) == 0) {
        //If all trunks have been deleted from admin, dialoutids might still exist
        $sql = "DELETE FROM globals WHERE variable = 'DIALOUTIDS'";
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage());
        }
        $glofields = array(array('OUT_1','ZAP/g1'),
                            array('DIAL_OUT_1','9'),
                            array('DIALOUTIDS','1'));
        $compiled = $db->prepare('INSERT INTO globals (variable, value) values (?,?)');
        $result = $db->executeMultiple($compiled,$glofields);
        if(DB::IsError($result)) {
            die($result->getMessage()."<br><br>".$sql);
        }
        $unique_trunks[] = array('OUT_1','ZAP/g1');
        addOutTrunk("1");
    }
    // asort($unique_trunks);
    return $unique_trunks;
}


//add trunk info to sip or iax table
function addSipOrIaxTrunk($config,$table,$channelid,$trunknum) {
    global $db;

    //echo "addSipOrIaxTrunk($config,$table,$channelid,$trunknum)";

    $confitem['account'] = $channelid;
    $gimmieabreak = nl2br($config);
    $lines = split('<br />',$gimmieabreak);
    foreach ($lines as $line) {
        $line = trim($line);
        if (count(split('=',$line)) > 1) {
            $tmp = split('=',$line);
            $key=trim($tmp[0]);
            $value=trim($tmp[1]);
            if (isset($confitem[$key]) && !empty($confitem[$key]))
                $confitem[$key].="&".$value;
            else
                $confitem[$key]=$value;
        }
    }
    foreach($confitem as $k=>$v) {
        $dbconfitem[]=array($k,$v);
    }
    $compiled = $db->prepare("INSERT INTO $table (id, keyword, data) values ('9999$trunknum',?,?)");
    $result = $db->executeMultiple($compiled,$dbconfitem);
    if(DB::IsError($result)) {
        die($result->getMessage()."<br><br>INSERT INTO $table (id, keyword, data) values ('9999$trunknum',?,?)");
    }
}

function getTrunkTech($trunknum) {
    global $db;

    $sql = "SELECT value FROM globals WHERE variable = 'OUT_".$trunknum."'";
    if (!$results = $db->getAll($sql)) {
        return false;
    }
    if(strpos($results[0][0],"AMP:") === 0) {  //custom trunks begin with AMP:
        $tech = "custom";
    } else {
        $tech = strtolower( strtok($results[0][0],'/') ); // the technology.  ie: ZAP/g1 is ZAP

        if ($tech == "iax2") $tech = "iax"; // same thing, here
    }
    return $tech;
}



function addTrunkDialRules($trunknum, $rules) {
    global $db;

    foreach ($rules as $rule) {
        $values = array();

        if (false !== ($pos = strpos($rule,"|"))) {
            // we have a | meaning to not dial the numbers before it
            // (ie, 1613|NXXXXXX should use the pattern _1613NXXXXXX but only pass NXXXXXX, not the leading 1613)

            $exten = "EXTEN:".$pos; // chop off leading digit
            $prefix = "";

            $rule = str_replace("|","",$rule); // remove all |'s

        } else if (false !== ($pos = strpos($rule,"+"))) {
            // we have a + meaning to add the numbers before it
            // (ie, 1613+NXXXXXX should use the pattern _NXXXXXX but pass it as 1613NXXXXXX)

            $prefix = substr($rule,0,$pos); // get the prefixed digits
            $exten = "EXTEN"; // pass as is

            $rule = substr($rule, $pos+1); // only match pattern after the +
        } else {
            // we pass the full dialed number as-is
            $exten = "EXTEN";
            $prefix = "";
        }

        if (!preg_match("/^[0-9*]+$/",$rule)) {
            // note # is not here, as asterisk doesn't recoginize it as a normal digit, thus it requires _ pattern matching

            // it's not strictly digits, so it must have patterns, so prepend a _
            $rule = "_".$rule;
        }

        $values[] = array('1', 'Dial', '${OUT_'.$trunknum.'}/${OUTPREFIX_'.$trunknum.'}'.$prefix.'${'.$exten.'}');
        $values[] = array('2', 'Congestion', '');
        $values[] = array('102', 'NoOp', 'outdial-'.$trunknum.' dial failed');

        $sql = "INSERT INTO extensions (context, extension, priority, application, args) VALUES ";
        $sql .= "('outdial-".$trunknum."', ";
        $sql .= "'".$rule."', ";
        // priority, application, args:
        $sql .= "?, ?, ?)";

        $compiled = $db->prepare($sql);
        $result = $db->executeMultiple($compiled,$values);
        if(DB::IsError($result)) {
            //var_dump($result);
            die($result->getMessage());
        }

    }

    // catch-all extension
    $sql = "INSERT INTO extensions (context, extension, priority, application, args) VALUES ";
    $sql .= "('outdial-".$trunknum."-catchall', ";
    $sql .= "'_.', ";
    // priority, application, args:
    $sql .= "'1', ";
    $sql .= "'Dial', ";
    $sql .= "'\${OUT_".$trunknum."}/\${OUTPREFIX_".$trunknum."}\${EXTEN}');";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }

    // include catch-all in main context
    $sql = "INSERT INTO extensions (context, extension, priority, application, flags) VALUES ";
    $sql .= "('outdial-".$trunknum."', ";
    $sql .= "'include', ";
    $sql .= "'1', ";
    $sql .= "'outdial-".$trunknum."-catchall', ";
    $sql .= "'2');";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }
}

function deleteTrunkDialRules($trunknum) {
    global $db;

    $sql = "DELETE FROM extensions WHERE context = 'outdial-".$trunknum."'";

    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }

    // the "catch-all" extension
    $sql = "DELETE FROM extensions WHERE context = 'outdial-".$trunknum."-catchall'";

    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }
}

function getTrunkDialRules($trunknum) {
    global $db;
    $sql = "SELECT extension, args FROM extensions WHERE context = 'outdial-".$trunknum."' AND application = 'Dial' ORDER BY extension ";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }

    $rules = array();
    foreach ($results as $row) {
        if ($row[0][0] == "_") {
            // remove leading _
            $rule = substr($row[0],1);
        } else {
            $rule = $row[0];
        }

        if (preg_match("/(\d*){EXTEN:(\d+)}/", $row[1], $matches)) {
            // this has a digit offset, we need to insert a |
            $rule = substr($rule,0,$matches[2])."|".substr($rule,$matches[2]);
        } else if (preg_match("/(\d){EXTEN}/", $row[1], $matches)) {
            // this has a prefix, insert a +
            $rule = substr($rule,0,strlen($matches[1]))."+".substr($rule,strlen($matches[1]));
        }

        $rules[] = $rule;
    }
    return array_unique($rules);

}

// just used internally by addTrunk() and editTrunk()
function backendAddTrunk($trunknum, $tech, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register, $accountcodefinal, $outtrunkright) {
    global $db;

    if  (is_null($dialoutprefix)) $dialoutprefix = "";
    if  (is_null($accountcodefinal)) $accountcodefinal = "";
    if  (is_null($outtrunkright)) $outtrunkright = "";

    //echo  "backendAddTrunk($trunknum, $tech, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register)";

    // change iax to "iax2" (only spot we actually store iax2, since its used by Dial()..)
    $techtemp = ((strtolower($tech) == "iax") ? "iax2" : $tech);
    $outval = (($techtemp == "custom" or $techtemp == "misdn") ? "AMP:".$channelid : strtoupper($techtemp).'/'.$channelid);

    $glofields = array(
            array('OUT_'.$trunknum, $outval),
            array('OUTPREFIX_'.$trunknum, $dialoutprefix),
            array('OUTMAXCHANS_'.$trunknum, $maxchans),
            array('OUTCID_'.$trunknum, $outcid),
            array('OUTRIGHT_'.$trunknum, $accountcodefinal),
            array('OUTTRUNKRIGHT_'.$trunknum, $outtrunkright),
            );

    unset($techtemp);

    $compiled = $db->prepare('INSERT INTO globals (variable, value) values (?,?)');

//    print_r ($glofields);

    $result = $db->executeMultiple($compiled,$glofields);
    if(DB::IsError($result)) {
        die($result->getMessage()."<br><br>".$sql);
    }

    writeoutids();

    //addOutTrunk($trunknum); don't need to add to outbound-routes anymore

    switch (strtolower($tech)) {
        case "iax":
        case "iax2":
            addSipOrIaxTrunk($peerdetails,'iax',$channelid,$trunknum);
            if ($usercontext != ""){
                addSipOrIaxTrunk($userconfig,'iax',$usercontext,'9'.$trunknum);
            }
            if ($register != ""){
                addTrunkRegister($trunknum,'iax',$register);
            }
        break;
        case "sip":
            addSipOrIaxTrunk($peerdetails,'sip',$channelid,$trunknum);
            if ($usercontext != ""){
                addSipOrIaxTrunk($userconfig,'sip',$usercontext,'9'.$trunknum);
            }
            if ($register != ""){
                addTrunkRegister($trunknum,'sip',$register);
            }
        break;
    }

}

// we're adding ,don't require a $trunknum
function addTrunk($tech, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register, $accountcodefinal, $outtrunkright) {
    global $db;

    // find the next available ID
    $trunknum = 1;
    foreach(gettrunks() as $trunk) {
        if ($trunknum == ltrim($trunk[0],"OUT_")) {
            $trunknum++;
        }
    }

    backendAddTrunk($trunknum, $tech, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register, $accountcodefinal, $outtrunkright);

    return $trunknum;
}

function deleteTrunk($trunknum, $tech = null) {
    global $db;

    if ($tech === null) { // in EditTrunk, we get this info anyways
        $tech = getTrunkTech($trunknum);
    }

    //delete from globals table
    //$sql = "DELETE FROM globals WHERE variable LIKE '%OUT_$trunknum' OR  variable LIKE '%OUTCID_$trunknum'";
    $sql = "DELETE FROM globals WHERE variable LIKE '%OUT_$trunknum' OR variable IN ('OUTCID_$trunknum','OUTMAXCHANS_$trunknum','OUTPREFIX_$trunknum','OUTRIGHT_$trunknum','OUTTRUNKRIGHT_$trunknum')";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }

    //write outids
    writeoutids();

    //delete from extensions table
    //delextensions('outbound-trunks','_${DIAL_OUT_'.$trunknum.'}.');
    //DIALRULES deleteTrunkRules($trunknum);

    //and conditionally, from iax or sip
    switch (strtolower($tech)) {
        case "iax":
        case "iax2":
            $sql = "DELETE FROM iax WHERE id = '9999$trunknum' OR id = '99999$trunknum' OR id = '9999999$trunknum'";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage());
            }
        break;
        case "sip":
            $sql = "DELETE FROM sip WHERE id = '9999$trunknum' OR id = '99999$trunknum' OR id = '9999999$trunknum'";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage());
            }
        break;
    }
}

function editTrunk($trunknum, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register, $accountcodefinal, $outtrunkright) {
    //echo "editTrunk($trunknum, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register)";
    $tech = getTrunkTech($trunknum);
    deleteTrunk($trunknum, $tech);
    backendAddTrunk($trunknum, $tech, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register, $accountcodefinal, $outtrunkright);
}

//get and print peer details (prefixed with 4 9's)
function getTrunkPeerDetails($trunknum) {
    global $db;

    $tech = getTrunkTech($trunknum);

    if ($tech == "zap") return ""; // zap has no details

    $sql = "SELECT keyword,data FROM $tech WHERE id = '9999$trunknum' ORDER BY id";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }
    foreach ($results as $result) {
        if ($result[0] != 'account') {
            if (isset($confdetail))
                $confdetail .= $result[0] .'='. $result[1] . "\n";
            else
                $confdetail = $result[0] .'='. $result[1] . "\n";
        }
    }
    return $confdetail;
}

//get and print user config (prefixed with 5 9's)
function getTrunkUserConfig($trunknum) {
    global $db;

    $tech = getTrunkTech($trunknum);

    if ($tech == "zap") return ""; // zap has no details

    $sql = "SELECT keyword,data FROM $tech WHERE id = '99999$trunknum' ORDER BY id";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }
    foreach ($results as $result) {
        if ($result[0] != 'account') {
            if (isset($confdetail))
                $confdetail .= $result[0] .'='. $result[1] . "\n";
            else
                $confdetail = $result[0] .'='. $result[1] . "\n";
        }
    }
    return $confdetail;
}

//get trunk user context (prefixed with 5 9's)
function getTrunkUserContext($trunknum) {
    global $db;

    $tech = getTrunkTech($trunknum);
    if ($tech == "zap") return ""; // zap has no account

    $sql = "SELECT keyword,data FROM $tech WHERE id = '99999$trunknum' ORDER BY id";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }
    foreach ($results as $result) {
        if ($result[0] == 'account') {
            $account = $result[1];
        }
    }
    return $account;
}

function getTrunkTrunkName($trunknum) {
    global $db;

    $sql = "SELECT value FROM globals WHERE variable = 'OUT_".$trunknum."'";
    if (!$results = $db->getAll($sql)) {
        return false;
    }
    if(strpos($results[0][0],"AMP:") === 0) {  //custom trunks begin with AMP:
        $tname = ltrim($results[0][0],"AMP:");
    } else {
    strtok($results[0][0],'/');
        $tname = strtok('/'); // the text _after_ technology.  ie: ZAP/g1 is g0
    }
    return $tname;
}

//get trunk account register string
function getTrunkRegister($trunknum) {
    global $db;
    $tech = getTrunkTech($trunknum);

    if ($tech == "zap") return ""; // zap has no register

    $sql = "SELECT keyword,data FROM $tech WHERE id = '9999999$trunknum'";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }
    foreach ($results as $result) {
            $register = $result[1];
    }
    return $register;
}

function addTrunkRegister($trunknum,$tech,$reg) {
    global $db;
    $sql = "INSERT INTO $tech (id, keyword, data) values ('9999999$trunknum','register','$reg')";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }
}

//get unique outbound route names
function getroutenames() {
    global $db;
    $sql = "SELECT DISTINCT SUBSTRING(context,7) FROM extensions WHERE context LIKE 'outrt-%' ORDER BY context ";
    // we SUBSTRING() to remove "outrt-"
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }

    if (count($results) == 0) {
        // see if they're still using the old dialprefix method
        $sql = "SELECT variable,value FROM globals WHERE variable LIKE 'DIAL\\\_OUT\\\_%'";
        // we SUBSTRING() to remove "outrt-"
        $results = $db->getAll($sql);
        if(DB::IsError($results)) {
            die($results->getMessage());
        }

        if (count($results) > 0) {
            // yes, they are using old method, let's update

            // get the default trunk
            $sql = "SELECT value FROM globals WHERE variable = 'OUT'";
            $results_def = $db->getAll($sql);
            if(DB::IsError($results_def)) {
                die($results_def->getMessage());
            }

            if (preg_match("/{OUT_(\d+)}/", $results_def[0][0], $matches)) {
                $def_trunk = $matches[1];
            } else {
                $def_trunk = "";
            }

            $default_patterns = array(    // default patterns that used to be in extensions.conf
                        "NXXXXXX",
                        "NXXNXXXXXX",
                        "1800NXXXXXX",
                        "1888NXXXXXX",
                        "1877NXXXXXX",
                        "1866NXXXXXX",
                        "1NXXNXXXXXX",
                        "011.",
                        "911",
                        "411",
                        "311",
                        );

            foreach ($results as $temp) {
                // temp[0] is "DIAL_OUT_1"
                // temp[1] is the dial prefix

                $trunknum = substr($temp[0],9);

                $name = "route".$trunknum;

                $trunks = array(1=>"OUT_".$trunknum); // only one trunk to use

                $patterns = array();
                foreach ($default_patterns as $pattern) {
                    $patterns[] = $temp[1]."|".$pattern;
                }

                if ($trunknum == $def_trunk) {
                    // this is the default trunk, add the patterns with no prefix
                    $patterns = array_merge((array)$patterns,(array)$default_patterns);
                }

                // add this as a new route
                addroute($name, $patterns, $trunks,"new");
            }


            // delete old values
            $sql = "DELETE FROM globals WHERE (variable LIKE 'DIAL\\\_OUT\\\_%') OR (variable = 'OUT') ";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage());
            }

            // we need to re-generate extensions_additional.conf
            // i'm not sure how to do this from here

            // re-run our query
            $sql = "SELECT DISTINCT SUBSTRING(context,7) FROM extensions WHERE context LIKE 'outrt-%' ORDER BY context ";
            // we SUBSTRING() to remove "outrt-"
            $results = $db->getAll($sql);
            if(DB::IsError($results)) {
                die($results->getMessage());
            }
        }

    } // else, it just means they have no routes.

    return $results;
}


function getctisupport($route) {
    global $db;
    $sql = "SELECT DISTINCT args FROM extensions WHERE context = 'outrt-".$route."' AND (args LIKE 'dialout-cti%' OR args LIKE 'dialout-enum-cti%')";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }

    return $results;
}

//get unique outbound route patterns for a given context
function getroutepatterns($route) {
    global $db;
    $sql = "SELECT extension, args FROM extensions WHERE context = 'outrt-".$route."' AND (args LIKE 'dialout-trunk%' OR args LIKE 'dialout-enum%' OR args LIKE 'dialout-cti%' OR args LIKE 'dialout-enum-cti%') ORDER BY extension ";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }

    $patterns = array();
    foreach ($results as $row) {
        if ($row[0][0] == "_") {
            // remove leading _
            $pattern = substr($row[0],1);
        } else {
            $pattern = $row[0];
        }

        if (preg_match("/{EXTEN:(\d+)}/", $row[1], $matches)) {
            // this has a digit offset, we need to insert a |
            $pattern = substr($pattern,0,$matches[1])."|".substr($pattern,$matches[1]);
        }

        $patterns[] = $pattern;
    }
    return array_unique($patterns);
}

//get unique outbound route trunks for a given context
function getroutetrunks($route) {
    global $db;
    $sql = "SELECT DISTINCT args FROM extensions WHERE context = 'outrt-".$route."' AND (args LIKE 'dialout-trunk,%' OR args LIKE 'dialout-enum,%' OR args LIKE 'dialout-cti%' OR args LIKE 'dialout-enum-cti%') ORDER BY priority ";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }

    $trunks = array();
    foreach ($results as $row) {
        if (preg_match('/^dialout-trunk,(\d+)/', $row[0], $matches)) {
            // check in_array -- even though we did distinct
            // we still might get ${EXTEN} and ${EXTEN:1} if they used | to split a pattern
            if (!in_array("OUT_".$matches[1], $trunks)) {
                $trunks[] = "OUT_".$matches[1];
            }
        } else if (preg_match('/^dialout-enum,(\d+)/', $row[0], $matches)) {
            if (!in_array("OUT_".$matches[1], $trunks)) {
                $trunks[] = "OUT_".$matches[1];
            }
        } else if (preg_match('/^dialout-cti,(\d+)/', $row[0], $matches)) {
            if (!in_array("OUT_".$matches[1], $trunks)) {
                $trunks[] = "OUT_".$matches[1];
            }
        }  else if (preg_match('/^dialout-enum-cti,(\d+)/', $row[0], $matches)) {
            if (!in_array("OUT_".$matches[1], $trunks)) {
                $trunks[] = "OUT_".$matches[1];
            }
        }

    }
    return $trunks;
}

//get password for this route
function getroutepassword($route) {
    global $db;
    $sql = "SELECT DISTINCT args FROM extensions WHERE context = 'outrt-".$route."' AND (args LIKE 'dialout-trunk,%' OR args LIKE 'dialout-enum,%' OR args LIKE 'dialout-cti%' OR args LIKE 'dialout-enum-cti%') ORDER BY priority ";
    $results = $db->getOne($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }
    if (preg_match('/^.*,.*,.*,.*,(\d+|\/\S+)/', $results, $matches)) {
        $password = $matches[1];
    } else {
        $password = "";
    }
    return $password;

}

function getlocalcid($route) {
    global $db;
    $sql = "SELECT DISTINCT args FROM extensions WHERE context = 'outrt-".$route."' AND (args LIKE 'dialout-trunk,%' OR args LIKE 'dialout-enum,%' OR args LIKE 'dialout-cti%' OR args LIKE 'dialout-enum-cti%') ORDER BY priority ";
    $results = $db->getOne($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }
    if (preg_match('/^.*,.*,.*,(\d+,)/', $results, $matches)) {
        $localcid = $matches[1];
    } else {
        $localcid = "";
    }
    return $localcid;

}

//get outbound routes for a given trunk
function gettrunkroutes($trunknum) {
    global $db;

    $sql = "SELECT DISTINCT SUBSTRING(context,7), priority FROM extensions WHERE context LIKE 'outrt-%' AND (args LIKE 'dialout-trunk,".$trunknum.",%' OR args LIKE 'dialout-enum,".$trunknum.",%' OR args LIKE 'dialout-cti,".$trunknum.",%' OR args LIKE 'dialout-enum-cti,".$trunknum.",%')ORDER BY context ";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }

    $routes = array();
    foreach ($results as $row) {
        $routes[$row[0]] = $row[1];
    }

    // array(routename=>priority)
    return $routes;
}

function gettrunktone($trunknum) {
    global $db;

    $sql = "SELECT description FROM simultone WHERE trunk_num LIKE 'OUT_".$trunknum."'";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }

    $routes = array();
    foreach ($results as $row) {
        $routes[$row[0]] = $row[1];
    }

    // array(routename=>priority)
    return $routes;
}

function addroute($name, $patterns, $trunks, $method, $pass, $ctisupport, $localcid) {
    global $db;

    $trunktech=array();

    //Retrieve each trunk tech for later lookup
    $sql="select * from globals WHERE variable LIKE 'OUT\\_%'";
        $result = $db->getAll($sql);
        if(DB::IsError($result)) {
        die($result->getMessage());
    }
    foreach($result as $tr) {
        $tech = strtok($tr[1], "/");
        $trunktech[$tr[0]]=$tech;
    }

     if ($method=="new")
    {
            $sql="select DISTINCT context FROM extensions WHERE context LIKE 'outrt-%' ORDER BY context";
            $routepriority = $db->getAll($sql);
            if(DB::IsError($result)) {
                    die($result->getMessage());
            }
            $order=setroutepriorityvalue(count($routepriority));

         $name = sprintf ("%s-%s",$order,$name);
    }
    $trunks = array_values($trunks); // probably already done, but it's important for our dialplan


    foreach ($patterns as $pattern) {

        if (false !== ($pos = strpos($pattern,"|"))) {
            // we have a | meaning to not pass the digits on
            // (ie, 9|NXXXXXX should use the pattern _9NXXXXXX but only pass NXXXXXX, not the leading 9)

            $pattern = str_replace("|","",$pattern); // remove all |'s
            $exten = "EXTEN:".$pos; // chop off leading digit
        } else {
            // we pass the full dialed number as-is
            $exten = "EXTEN";
        }

        if (!preg_match("/^[0-9*]+$/",$pattern)) {
            // note # is not here, as asterisk doesn't recoginize it as a normal digit, thus it requires _ pattern matching

            // it's not strictly digits, so it must have patterns, so prepend a _
            $pattern = "_".$pattern;
        }

        $first_trunk = 1;

        foreach ($trunks as $priority => $trunk) {
            $priority += 1; // since arrays are 0-based, but we want priorities to start at 1

            $sql = "INSERT INTO extensions (context, extension, priority, application, args) VALUES ";
            $sql .= "('outrt-".$name."', ";
            $sql .= "'".$pattern."', ";
            $sql .= "'".sprintf('%02s',$priority)."', ";
            $sql .= "'Macro', ";

            if ($first_trunk)
                $pass_str = $pass;
            else
                $pass_str = "";
                
            if ($trunktech[$trunk] == "ENUM") {
                if ($ctisupport == "1") {
                    $sql .= "'dialout-enum-cti,".substr($trunk,4).",\${".$exten."},".$localcid.",".$pass_str."'"; // cut off OUT_ from $trunk
                } else {
                        $sql .= "'dialout-enum,".substr($trunk,4).",\${".$exten."},".$localcid.",".$pass_str."'"; // cut off OUT_ from $trunk
                }
            } else if ($ctisupport == "1") {
                $sql .= "'dialout-cti,".substr($trunk,4).",\${".$exten."},".$localcid.",".$pass_str."'"; // cut off OUT_ from $trunk
            } else {
                $sql .= "'dialout-trunk,".substr($trunk,4).",\${".$exten."},".$localcid.",".$pass_str."'"; // cut off OUT_ from $trunk
            }

            $sql .= ")";

            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die($result->getMessage());
            }
            //To identify the first trunk in a pattern
            //so that passwords are in the first trunk in
            //each pattern
            $first_trunk = 0;
        }

	if ($ctisupport == "1") {

        $priority += 1;
        $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr) VALUES ";
        $sql .= "('outrt-".$name."', ";
        $sql .= "'".$pattern."', ";
        $sql .= "'".sprintf('%02s',$priority)."', ";
        $sql .= "'Macro', ";
        $sql .= "'outisbusy-cti', ";
        $sql .= "'No available circuits')";

	} else {

        $priority += 1;
        $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr) VALUES ";
        $sql .= "('outrt-".$name."', ";
        $sql .= "'".$pattern."', ";
        $sql .= "'".sprintf('%02s',$priority)."', ";
        $sql .= "'Macro', ";
        $sql .= "'outisbusy', ";
        $sql .= "'No available circuits')";

	}

        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage());
        }
    }


    // add an include=>outrt-$name  to [outbound-allroutes]:

    // we have to find the first available priority.. priority doesn't really matter for the include, but
    // there is a unique index on (context,extension,priority) so if we don't do this we can't put more than
    // one route in the outbound-allroutes context.
    $sql = "SELECT priority FROM extensions WHERE context = 'outbound-allroutes' AND extension = 'include'";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }
    $priorities = array();
    foreach ($results as $row) {
        $priorities[] = $row[0];
    }
    for ($priority = 1; in_array($priority, $priorities); $priority++);

    // $priority should now be the lowest available number

    $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ";
    $sql .= "('outbound-allroutes', ";
    $sql .= "'include', ";
    $sql .= "'".sprintf('%02s',$priority)."', ";
    $sql .= "'outrt-".$name."', ";
    $sql .= "'', ";
    $sql .= "'', ";
    $sql .= "'2')";

    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($priority.$result->getMessage());
    }

}

function deleteroute($name) {
    global $db;
    $sql = "DELETE FROM extensions WHERE context = 'outrt-".$name."'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }

    $sql = "DELETE FROM extensions WHERE context = 'outbound-allroutes' AND application = 'outrt-".$name."' ";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }

    return $result;
}

function renameRoute($oldname, $newname) {
    global $db;

    $route_prefix=substr($oldname,0,4);
    $newname=$route_prefix.$newname;
    $sql = "SELECT context FROM extensions WHERE context = 'outrt-".$newname."'";
    $results = $db->getAll($sql);
    if (count($results) > 0) {
        // there's already a route with this name
        return false;
    }

    $sql = "UPDATE extensions SET context = 'outrt-".$newname."' WHERE context = 'outrt-".$oldname."'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }
        $mypriority=sprintf("%d",$route_prefix);
    $sql = "UPDATE extensions SET application = 'outrt-".$newname."', priority = '$mypriority' WHERE context = 'outbound-allroutes' AND application = 'outrt-".$oldname."' ";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage());
    }

    return true;
}

function editroute($name, $patterns, $trunks, $pass, $ctisupport, $localcid) {
    deleteroute($name);
    addroute($name, $patterns, $trunks,"edit", $pass, $ctisupport, $localcid);
}

function getroute($route) {
    global $db;
     $sql = "SELECT DISTINCT args FROM extensions WHERE context = 'outrt-".$route."' AND args LIKE 'dialout-trunk,%' ORDER BY priority ";
     $results = $db->getAll($sql);
     if(DB::IsError($results)) {
         die($results->getMessage());
     }

     $trunks = array();
     foreach ($results as $row) {
         if (preg_match('/^dialout-trunk,(\d+)/', $row[0], $matches)) {
             // check in_array -- even though we did distinct
             // we still might get ${EXTEN} and ${EXTEN:1} if they used | to split a pattern
             if (!in_array("OUT_".$matches[1], $trunks)) {
                 $trunks[] = "OUT_".$matches[1];
             }
         }
     }
     return $trunks;
}
function setroutepriorityvalue2($key)
{
    $my_lookup=array();
    $x=0;
    for ($j=97;$j<100;$j++)
    {
        for ($i=97;$i<123;$i++)
        {
            $my_lookup[$x++] = sprintf("%c%c",$j,$i);
        }
    }
echo "my key is $key $my_lookup[$key]";
    return ($my_lookup[$key]);
}
function setroutepriorityvalue($key)
{
    $key=$key+1;
    if ($key<10)
        $prefix = sprintf("00%d",$key);
    else if ((9<$key)&&($key<100))
        $prefix = sprintf("0%d",$key);
    else if ($key>100)
        $prefix = sprintf("%d",$key);
    return ($prefix);
}
function setroutepriority($routepriority, $reporoutedirection, $reporoutekey)
{
    global $db;
    $counter=-1;
    foreach ($routepriority as $tresult)
    {
        $counter++;
        if (($counter==($reporoutekey-1)) && ($reporoutedirection=="up")) {
            // swap this one with the one before (move up)
            $temproute = $routepriority[$counter];
            $routepriority[ $counter ] = $routepriority[ $counter+1 ];
            $routepriority[ $counter+1 ] = $temproute;

        } else if (($counter==($reporoutekey)) && ($reporoutedirection=="down")) {
            // swap this one with the one after (move down)
            $temproute = $routepriority[ $counter+1 ];
            $routepriority[ $counter+1 ] = $routepriority[ $counter ];
            $routepriority[ $counter ] = $temproute;
        }
    }
    unset($temptrunk);
    $routepriority = array_values($routepriority); // resequence our numbers
    $counter=0;
    foreach ($routepriority as $tresult)
    {
        $order=setroutepriorityvalue($counter++);
        $sql = sprintf("Update extensions set context='outrt-%s-%s' WHERE context='outrt-%s'",$order,substr($tresult[0],4), $tresult[0]);
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage());
        }
    }
    // Delete and readd the outbound-allroutes entries
    $sql = "delete from  extensions WHERE context='outbound-allroutes'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
            die($result->getMessage().$sql);
    }
    $sql = "SELECT DISTINCT context FROM extensions WHERE context like 'outrt-%' ORDER BY context";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        die($results->getMessage());
    }

    $priority_loops=1;
    foreach ($results as $row) {
        $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ";
        $sql .= "('outbound-allroutes', ";
        $sql .= "'include', ";
        $sql .= "'".sprintf('%02s',$priority_loops++)."', ";
        $sql .= "'".$row[0]."', ";
        $sql .= "'', ";
        $sql .= "'', ";
        $sql .= "'2')";

        //$sql = sprintf("Update extensions set application='outrt-%s-%s' WHERE context='outbound-allroutes' and  application='outrt-%s'",$order,substr($tresult[0],4), $tresult[0]);
        $result = $db->query($sql);
        if(DB::IsError($result)) {
            die($result->getMessage(). $sql);
         }
    }
    $sql = "SELECT DISTINCT SUBSTRING(context,7) FROM extensions WHERE context LIKE 'outrt-%' ORDER BY context ";
        // we SUBSTRING() to remove "outrt-"
        $routepriority = $db->getAll($sql);
        if(DB::IsError($routepriority))
        {
                die($routepriority->getMessage());
        }
        return ($routepriority);
}




function parse_conf($filename, &$conf, &$section) {
    if (is_null($conf)) {
        $conf = array();
    }
    if (is_null($section)) {
        $section = "general";
    }

    if (file_exists($filename)) {
        $fd = fopen($filename, "r");
        while ($line = fgets($fd, 1024)) {
            if (preg_match("/^\s*([a-zA-Z0-9-_]+)\s*=\s*(.*?)\s*([;#].*)?$/",$line,$matches)) {
                // name = value
                // option line
                $conf[$section][ $matches[1] ] = $matches[2];
            } else if (preg_match("/^\s*\[(.+)\]/",$line,$matches)) {
                // section name
                $section = strtolower($matches[1]);
            } else if (preg_match("/^\s*#include\s+(.*)\s*([;#].*)?/",$line,$matches)) {
                // include another file

                if ($matches[1][0] == "/") {
                    // absolute path
                    $filename = $matches[1];
                } else {
                    // relative path
                    $filename =  dirname($filename)."/".$matches[1];
                }

                parse_conf($filename, $conf, $section);
            }
        }
    }
}

function readDialRulesFile() {
    global $localPrefixFile; // probably not the best way

    parse_conf($localPrefixFile, &$conf, &$section);

    return $conf;
}

function getDialRules($trunknum) {
    $conf = readDialRulesFile();
    if (isset($conf["trunk-".$trunknum])) {
        return $conf["trunk-".$trunknum];
    }
    return false;
}

function writeDialRulesFile($conf) {
    global $localPrefixFile; // probably not the best way

    $fd = fopen($localPrefixFile,"w");
    foreach ($conf as $section=>$values) {
        fwrite($fd, "[".$section."]\n");
        foreach ($values as $key=>$value) {
            fwrite($fd, $key."=".$value."\n");
        }
        fwrite($fd, "\n");
    }
    fclose($fd);
}

function addDialRules($trunknum, $dialrules) {
    $values = array();
    $i = 1;
    foreach ($dialrules as $rule) {
        $values["rule".$i++] = $rule;
    }

    $conf = readDialRulesFile();

    // rewrite for this trunk
    $conf["trunk-".$trunknum] = $values;

    writeDialRulesFile($conf);
}

function deleteDialRules($trunknum) {
    $conf = readDialRulesFile();

    // remove rules for this trunk
    unset($conf["trunk-".$trunknum]);

    writeDialRulesFile($conf);
}

function queue_list() {
    $sql = "SELECT id FROM queues";
    $results = sql($sql,"getAll");

    foreach($results as $result){
        if (checkRange($result[0])){
            $extens[] = array($result[0]);
        }
    }
    if (isset($extens)) {
        sort($extens);
        return $extens;
    } else {
        return null;
    }
}


function addqueue($account,$name,$password,$prefix,$goto,$agentannounce,$callerannounce,$members) {
    global $db;

    $devices = queue_list();
    if (is_array($devices)) {
        foreach($devices as $device) {
            if ($device[0] === $account) {
                    echo "<script>javascript:alert('"._("This Queue [").$device[0].("] is already in use")."');</script>";
                return false;
            }
        }
    }


    //add to extensions table
    if ($agentannounce != 'None')
        $agentannounce="custom/$agentannounce";
    else
        $agentannounce="";

    if ($callerannounce != 'None')
        $callerannounce="custom/$callerannounce";
    else
        $callerannounce="";

    $pri = 1;

    $addarray = array('ext-queues',$account,$pri++,'Answer',''.'','','0');
    addextensions($addarray);

    if ($prefix != "") {

        $addarray = array('ext-queues',$account,$pri++,'Set','CALLERID(name)='.$prefix.'${CALLERID(name)}','','0');

    } else {

            $addarray = array('ext-queues',$account,$pri++,'Set','CALLERID(number)=${CALLERID(num)}','','0');
            
    }

    addextensions($addarray);
    
    if ($_REQUEST['alertinfo'] != '') {
        $addarray = array('ext-queues',$account,$pri++,'Set','__ALERT_INFO='.str_replace(';', '\;', $_REQUEST['alertinfo']).'','','0');
        addextensions($addarray);
	}

    if ($_REQUEST['cwignore'] == '1') {
        $addarray = array('ext-queues',$account,$pri++,'Set','__CWIGNORE=true','','0');
        addextensions($addarray);
    }

    $addarray = array('ext-queues',$account,$pri++,'Set','MONITOR_FILENAME=/var/spool/asterisk/monitor/${STRFTIME(${EPOCH},,%Y%m%d-%H%M%S)}-QUEUE${EXTEN}-${CALLERID(number)}-^-${UNIQUEID}','','0');
    addextensions($addarray);
    if( $callerannounce != '' ) {
        $addarray = array('ext-queues',$account,$pri++,'Playback',$callerannounce,'','','0');
        addextensions($addarray);
    }
    if ($_REQUEST['rtone'] == '1') {
        $options = 'r';
        } else {
            $options = 't';
        }
    $addarray = array('ext-queues',$account,$pri++,'Queue',$account.'|'.$options.'||'.$agentannounce.'|'.$_REQUEST['maxwait'],$name,'0');
    addextensions($addarray);
    $addarray = array('ext-queues',$account.'*','1','Macro','agent-add,'.$account.','.$password,'','0');
    addextensions($addarray);
    $addarray = array('ext-queues',$account.'**','1','Macro','agent-del,'.$account,'','0');
    addextensions($addarray);

    $gotostr = setGoto($account,'ext-queues',$pri++,$goto,0);

    // now add to queues table
    $fields = array(
        array($account,'account',$account),
        array($account,'maxlen',($_REQUEST['maxlen'])?$_REQUEST['maxlen']:'0'),
        array($account,'joinempty',($_REQUEST['joinempty'])?$_REQUEST['joinempty']:'yes'),
        array($account,'leavewhenempty',($_REQUEST['leavewhenempty'])?$_REQUEST['leavewhenempty']:'no'),
        array($account,'strategy',($_REQUEST['strategy'])?$_REQUEST['strategy']:'ringall'),
        array($account,'timeout',($_REQUEST['timeout'])?$_REQUEST['timeout']:'15'),
        array($account,'retry',($_REQUEST['retry'])?$_REQUEST['retry']:'0'),
        array($account,'wrapuptime',($_REQUEST['wrapuptime'])?$_REQUEST['wrapuptime']:'0'),
        array($account,'agentannounce',($_REQUEST['agentannounce'])?$_REQUEST['agentannounce']:'None'),
        array($account,'callerannounce',($_REQUEST['callerannounce'])?$_REQUEST['callerannounce']:'None'),
        array($account,'announce-frequency',($_REQUEST['announcefreq'])?$_REQUEST['announcefreq']:'0'),
        array($account,'announce-holdtime',($_REQUEST['announceholdtime'])?$_REQUEST['announceholdtime']:'no'),
        array($account,'queue-youarenext',($_REQUEST['announceposition']=='no')?'':'queue-youarenext'),  //if no, play no sound
        array($account,'queue-thereare',($_REQUEST['announceposition']=='no')?'':'queue-thereare'),  //if no, play no sound
        array($account,'queue-callswaiting',($_REQUEST['announceposition']=='no')?'':'queue-callswaiting'),  //if no, play no sound
        array($account,'queue-thankyou',($_REQUEST['announcemenu']=='none')?'queue-thankyou':'custom/'.$_REQUEST['announcemenu']),  //if none, play default thankyou, else custom/aa
        array($account,'context',($_REQUEST['announcemenu']=='none')?'':$_REQUEST['announcemenu']),  //if not none, set context=aa
        array($account,'monitor-format',($_REQUEST['monitor-format'])?$_REQUEST['monitor-format']:''),
        array($account,'monitor-join','yes'),
        array($account,'prefix',$prefix),
        array($account,'maxwait',($_REQUEST['maxwait'])?$_REQUEST['maxwait']:''),
        array($account,'goto',$gotostr),
        array($account,'name',$name),
        array($account,'password',$password),
        array($account,'music',($_REQUEST['music'])?$_REQUEST['music']:'default'),
        array($account,'rtone',($_REQUEST['rtone'])?$_REQUEST['rtone']:'t'),
        array($account,'alertinfo',($_REQUEST['alertinfo'])?$_REQUEST['alertinfo']:''),
        array($account,'cwignore',($_REQUEST['cwignore'])?$_REQUEST['cwignore']:'0'),
        array($account,'eventwhencalled',($_REQUEST['eventwhencalled'])?$_REQUEST['eventwhencalled']:'no'),
        array($account,'eventmemberstatus',($_REQUEST['eventmemberstatusoff'])?$_REQUEST['eventmemberstatusoff']:'yes'),
        array($account,'reportholdtime',($_REQUEST['reportholdtime'])?$_REQUEST['reportholdtime']:'no'),
        array($account,'servicelevel',($_REQUEST['servicelevel'])?$_REQUEST['servicelevel']:'0'),
        array($account,'autopause',($_REQUEST['autopause'])?$_REQUEST['autopause']:''),
        array($account,'setinterfacevar',($_REQUEST['setinterfacevar'])?$_REQUEST['setinterfacevar']:''),
        array($account,'autofill',($_REQUEST['autofill'])?$_REQUEST['autofill']:'yes'),
        array($account,'announce-round-seconds',($_REQUEST['announceroundseconds'])?$_REQUEST['announceroundseconds']:'0'),
        array($account,'periodic-announce-frequency',($_REQUEST['periodicannouncefrequency'])?$_REQUEST['periodicannouncefrequency']:'0'),
        array($account,'weight',($_REQUEST['weight'])?$_REQUEST['weight']:'0'));

    //there can be multiple members
    if (isset($members)) {
        foreach ($members as $member) {
            $fields[] = array($account,'member',$member);
        }
    }

    $compiled = $db->prepare('INSERT INTO queues (id, keyword, data) values (?,?,?)');
    $result = $db->executeMultiple($compiled,$fields);
    if(DB::IsError($result)) {
//        die($result->getMessage()."<br><br>Error adding to Queues table");
        echo "<script>javascript:alert('"._("One or more Static Agents in this queue are already in use.\\nThe duplicates agents are automatically removed.\\nPlease click the RED bar for apply your changes.")."');</script>";
        return false;
    }

    return true;
}

function delqueue($account) {
    global $db;
    //delete from extensions table
    delextensions('ext-queues',$account);
    delextensions('ext-queues',$account.'*');
    delextensions('ext-queues',$account.'**');

    $sql = "DELETE FROM queues WHERE id = '$account'";
    $result = $db->query($sql);
    if(DB::IsError($result)) {
        die($result->getMessage().$sql);
    }

}

function getqueueinfo($account) {
    global $db;

        if ($account == "")
          {
            return array();
          }

    //get all the variables for the queue
    $sql = "SELECT keyword,data FROM queues WHERE id = '$account'";
    $results = $db->getAssoc($sql);

    //okay, but there can be multiple member variables ... do another select for them
    $sql = "SELECT data FROM queues WHERE id = '$account' AND keyword = 'member'";
    $results['member'] = $db->getCol($sql);

    //queues.php looks for 'announcemenu', which is the same a context
    $results['announcemenu'] = $results['context'];

    //if 'queue-youarenext=queue-youarenext', then assume we want to announce position
    if($results['queue-youarenext'] == 'queue-youarenext')
        $results['announce-position'] = 'yes';
    else
        $results['announce-position'] = 'no';

    //get CID Prefix
    $sql = "SELECT data FROM queues WHERE id = '$account' AND keyword = 'prefix'";
    $myresults = $db->getCol($sql);
    $results['prefix'] = $myresults[0];

    //get the maxwait
    $sql = "SELECT data FROM queues WHERE id = '$account' AND keyword = 'maxwait'";
    $myresults = $db->getCol($sql);
    $results['maxwait'] = $myresults[0];

    //get the name
    $sql = "SELECT data FROM queues WHERE id = '$account' AND keyword = 'name'";
    $myresults = $db->getCol($sql);
    $results['name'] = $myresults[0];

    //get the password
    $sql = "SELECT data FROM queues WHERE id = '$account' AND keyword = 'password'";
    $myresults = $db->getCol($sql);
    $results['password'] = $myresults[0];

    //get the goto
    $sql = "SELECT data FROM queues WHERE id = '$account' AND keyword = 'goto'";
    $myresults = $db->getCol($sql);
    $results['goto'] = $myresults[0];

    //get the callerannounce recording
    $sql = "SELECT data FROM queues WHERE id = '$account' AND keyword = 'callerannounce'";
    $myresults = $db->getCol($sql);
    $results['callerannounce'] = $myresults[0];

    //get the agentannounce recording
    $sql = "SELECT data FROM queues WHERE id = '$account' AND keyword = 'agentannounce'";
    $myresults = $db->getCol($sql);
    $results['agentannounce'] = $myresults[0];

    return $results;
}

// $formName is the name of the form we are drawing in
// $goto is the current goto destination setting
// $i is the destination set number (used when drawing multiple destination sets in a single form ie: digital receptionist)
function drawselects($formName,$goto,$i,$fixInc,$fixIncFax,$fixIncCallback,$fixIncCallbackExt,$fixMeetMe) {

    //query for exisiting aa_N contexts
    $unique_aas = getaas();
    //get unique extensions
    $extens = getextens();
    //get unique ring groups
    $gresults = getgroups();
    //get unique queues
    $queues = getqueues();
    //get unique meetmes
    $meetmes = getmeetmes();
    //get unique misc destinations
    $miscdest = getmiscdest();
    //check if a2billing database is enabled

    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $a2billing_check = $amp_conf["A2BENABLED"];
    
    if ($a2billing_check == "yes") {

        //get unique a2billing
        $extensa2b = getextensa2b();

    }

    if ($goto == "") {

        $goto = "ext-local";

    }

    if (isset($extens)) {
        //get voicemail
        $uservm = getVoicemail();
        $vmcontexts = array_keys($uservm);
        foreach ($extens as $thisext) {
            $extnum = $thisext[0];
            // search vm contexts for this extensions mailbox
            foreach ($vmcontexts as $vmcontext) {
                if(isset($uservm[$vmcontext][$extnum])){
                    $vmname = $uservm[$vmcontext][$extnum]['name'];
                    $vmboxes[] = array($extnum, '"' . $vmname . '" <' . $extnum . '>');
                }
            }
        }
    }

    $selectHtml = '<table border="0" cellpadding="1" cellspacing="2">';
    $selectHtml .=  '<tr><td><input type="hidden" name="goto'.$i.'" value="">';
    $selectHtml .=    '<input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="ivr" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'ivr\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'ivr\';" '.(strpos($goto,'aa_') === false ? '' : 'CHECKED=CHECKED').' /> '._("Digital Receptionist").': ';
    $selectHtml .=    '<select name="ivr'.$i.'">';

    if (isset($unique_aas)) {
        foreach ($unique_aas as $unique_aa) {
            $menu_id = $unique_aa[0];
            $menu_name = $unique_aa[1];
            $selectHtml .= '<option value="'.$menu_id.'" '.(strpos($goto,$menu_id) === false ? '' : 'SELECTED').'>'.($menu_name ? $menu_name : 'Menu ID'.$menu_id) . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="extension" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'extension\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'extension\';" '.(strpos($goto,'ext-local') === false ? '' : 'CHECKED=CHECKED').'/> '._("Extension").': ';
    $selectHtml .=    '<select name="extension'.$i.'">';

    if (isset($extens)) {
        foreach ($extens as $exten) {
            $selectHtml .= '<option value="'.$exten[0].'" '.(strpos($goto,$exten[0]) === false ? '' : 'SELECTED').'>'.$exten[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

// a2billing incoming extensions

    if ($a2billing_check == "yes") {

        $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="extensiona2b" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'extensiona2b\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'extensiona2b\';" '.(strpos($goto,'ext-local-a2b') === false ? '' : 'CHECKED=CHECKED').'/> '._("Extension A2B").': ';
        $selectHtml .=    '<select name="extensiona2b'.$i.'">';

        if (isset($extensa2b)) {
            foreach ($extensa2b as $exten) {
                $selectHtml .= '<option value="'.$exten[1].'" '.(strpos($goto,$exten[1]) === false ? '' : 'SELECTED').'>'.$exten[1] . '</option>';
            }
        }

        $selectHtml .=    '</select></td></tr>';
        
    }
//
    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="voicemail" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'voicemail\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'voicemail\';" '.(strpos($goto,'vm') === false ? '' : 'CHECKED=CHECKED').(strpos($goto,'ext-local,${VM_PREFIX}') === false ? '' : 'CHECKED=CHECKED').' /> '._("Voicemail").': ';
    $selectHtml .=    '<select name="voicemail'.$i.'">';

    if (isset($vmboxes)) {
        foreach ($vmboxes as $exten) {
            $selectHtml .= '<option value="'.$exten[0].'" '.(strpos($goto,$exten[0]) === false ? '' : 'SELECTED').'>'.$exten[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="miscdest" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'miscdest\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'miscdest\';" '.(strpos($goto,'ext-miscdests') === false ? '' : 'CHECKED=CHECKED').'/> '._("Misc Destinations").': ';
    $selectHtml .=    '<select name="miscdest'.$i.'">';

    if (isset($miscdest)) {
        foreach ($miscdest as $miscdests) {
            $selectHtml .= '<option value="'.$miscdests[0].'" '.(strpos($goto,$miscdests[0]) === false ? '' : 'SELECTED').'>'.$miscdests[0].':'.$miscdests[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="group" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'group\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'group\';" '.(strpos($goto,'ext-group') === false ? '' : 'CHECKED=CHECKED').' /> '._("Ring Group").': ';
    $selectHtml .=    '<select name="group'.$i.'">';

    if (isset($gresults)) {
        foreach ($gresults as $gresult) {
            $selectHtml .= '<option value="'.$gresult[0].'" '.(strpos( ','.$goto.',' , ','.$gresult[0].',' ) === false ? '' : 'SELECTED').'>'.$gresult[0].':'.$gresult[1].'</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="queue" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'queue\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'queue\';" '.(strpos($goto,'ext-queues') === false ? '' : 'CHECKED=CHECKED').' /> '._("Queue").': ';
    $selectHtml .=    '<select name="queue'.$i.'">';

    if (isset($queues)) {
        foreach ($queues as $queue) {
            $selectHtml .= '<option value="'.$queue[0].'" '.(strpos($goto,$queue[0]) === false ? '' : 'SELECTED').'>'.$queue[0].':'.$queue[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

        if ($fixMeetMe == 'fixMEETME') {

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="meetme" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'meetme\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'meetme\';" '.(strpos($goto,'ext-meetme') === false ? '' : 'CHECKED=CHECKED').' /> '._("Conferences").': ';
    $selectHtml .=    '<select name="meetme'.$i.'">';

    if (isset($meetmes)) {
        foreach ($meetmes as $meetme) {
            $selectHtml .= '<option value="'.$meetme[0].'" '.(strpos($goto,$meetme[0]) === false ? '' : 'SELECTED').'>'.$meetme[0].':'.$meetme[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    }

        if ($fixInc == 'fixINCOMING') {

    global $db;
    $sql = "SELECT * FROM globals";
    $incoming_desc = $db->getAll($sql);
    if(DB::IsError($incoming_desc)) {
       die($incoming_desc->getMessage());
    }

    foreach ($incoming_desc as $global) {
    ${trim($global[0])} = $global[1];
    }

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="from-pstn" '.(strpos($goto,'from-pstn') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .=    '&nbsp;Incoming Calls: ';
    $selectHtml .=    '<select name="from-pstn_args'.$i.'">';

    if ($INCOMING_DESC != '') {
    $selectHtml .= '<option value="from-pstn-timecheck,s,1" '.(strpos($goto,'from-pstn-timecheck,s,1') === false ? '' : 'SELECTED').'>#1: '.$INCOMING_DESC.'</option>';
    }
    if ($INCOMING_DESC_1 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_1,s,1" '.(strpos($goto,'from-pstn-timecheck_1,s,1') === false ? '' : 'SELECTED').'>#2: '.$INCOMING_DESC_1.'</option>';
    }
    if ($INCOMING_DESC_2 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_2,s,1" '.(strpos($goto,'from-pstn-timecheck_2,s,1') === false ? '' : 'SELECTED').'>#3: '.$INCOMING_DESC_2.'</option>';
    }
    if ($INCOMING_DESC_3 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_3,s,1" '.(strpos($goto,'from-pstn-timecheck_3,s,1') === false ? '' : 'SELECTED').'>#4: '.$INCOMING_DESC_3.'</option>';
    }
    if ($INCOMING_DESC_4 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_4,s,1" '.(strpos($goto,'from-pstn-timecheck_4,s,1') === false ? '' : 'SELECTED').'>#5: '.$INCOMING_DESC_4.'</option>';
    }
    if ($INCOMING_DESC_5 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_5,s,1" '.(strpos($goto,'from-pstn-timecheck_5,s,1') === false ? '' : 'SELECTED').'>#6: '.$INCOMING_DESC_5.'</option>';
    }
    $selectHtml .=    '</select></td></tr>';


        }

        if ($fixIncFax == 'fixFAX') {

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="fax" '.(strpos($goto,'fax') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .=    '<input name="fax_args'.$i.'" type="hidden" value="native-fax,s,1" />';
    $selectHtml .=    '&nbsp;Use <b>Native FAX</b> machine.</td></tr>';

        }

        if ($fixIncCallback == 'fixCALLBACK') {

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="callback" '.(strpos($goto,'callback') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .=    '<input name="callback_args'.$i.'" type="hidden" value="callback,s,1" />';
    $selectHtml .=    '&nbsp;Use <b>CallBack.</b></td></tr>';

        }

        if ($fixIncCallbackExt == 'fixCALLBACKEXT') {

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="callbackext" '.(strpos($goto,'callbackext') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .=    '<input name="callbackext_args'.$i.'" type="hidden" value="callbackext,s,1" />';
    $selectHtml .=    '&nbsp;Use <b>CallBack on Demand.</b></td></tr>';

        }

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="custom" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'custom\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'custom\';" '.(strpos($goto,'custom') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .= ' <a href="#" class="info">'._("Custom App<span>Uses Goto() to send caller to a custom context.<br><br>The context name <b>MUST</b> contain the word 'custom' and should be in the format custom-context , extension , priority. Example entry:<br><br><b>custom-myapp,s,1</b><br><br>The <b>[custom-myapp]</b> context would need to be created and included in extensions_custom.conf</span>").'</a>:';
    $selectHtml .=    '<input type="text" size="25" name="custom_args'.$i.'" value="'.(strpos($goto,'custom') === false ? '' : $goto).'" />';
    $selectHtml .=    '</td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="dial" '.(strpos($goto,'outbound-allroutes') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .=  ' <a href="#" class="info">'._("Custom Dial<span>You can include an extension on a remote system, or an external number (Outbound Routing must contain a valid route for external numbers)</span>").'</a>:';

    $hackstart = strpos($goto,',')+1;

    $selectHtml .=    '<input type="text" size="20" name="dial_args'.$i.'" value="'.(strpos($goto,'outbound-allroutes') === false ? '' : substr($goto,$hackstart,-2)).'" />';
    $selectHtml .=    '</td></tr>';


    $selectHtml .=    '</table>';

    return $selectHtml;
}

function extdrawselects($formName,$goto,$i,$jumpto) {

    //query for exisiting aa_N contexts
    $unique_aas = getaas();
    //get unique extensions
    $extens = getextens();
    //get unique ring groups
    $gresults = getgroups();
    //get unique queues
    $queues = getqueues();
    //get unique misc destinations
    $miscdest = getmiscdest();

    if ($goto == "") {

    $goto = "ext-local";

    }

    $selectHtml = '<table id="jumpto" border="0" cellpadding="1" cellspacing="2"';

    if ($jumpto == 2) {

         $htmljump = ' style="display:block;"';

    } else {

            $htmljump = ' style="display:none;"';

    }

    $selectHtml .= $htmljump.'>';

    $selectHtml .=  '<tr><td><input type="hidden" name="goto'.$i.'" value="">';
    $selectHtml .=    '<input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="ivr" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'ivr\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'ivr\';" '.(strpos($goto,'aa_') === false ? '' : 'CHECKED=CHECKED').' /> '._("Digital Receptionist").': ';
    $selectHtml .=    '<select name="ivr'.$i.'">';

    if (isset($unique_aas)) {
        foreach ($unique_aas as $unique_aa) {
            $menu_id = $unique_aa[0];
            $menu_name = $unique_aa[1];
            $selectHtml .= '<option value="'.$menu_id.'" '.(strpos($goto,$menu_id) === false ? '' : 'SELECTED').'>'.($menu_name ? $menu_name : 'Menu ID'.$menu_id) . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="extension" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'extension\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'extension\';" '.(strpos($goto,'ext-local') === false ? '' : 'CHECKED=CHECKED').'/> '._("Extension").': ';
    $selectHtml .=    '<select name="extension'.$i.'">';

    if (isset($extens)) {
        foreach ($extens as $exten) {
            $selectHtml .= '<option value="'.$exten[0].'" '.(strpos($goto,'ext-local,'.$exten[0]) === false ? '' : 'SELECTED').'>'.$exten[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="miscdest" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'miscdest\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'miscdest\';" '.(strpos($goto,'ext-miscdests') === false ? '' : 'CHECKED=CHECKED').'/> '._("Misc Dest").': ';
    $selectHtml .=    '<select name="miscdest'.$i.'">';

    if (isset($miscdest)) {
        foreach ($miscdest as $miscdests) {
            $selectHtml .= '<option value="'.$miscdests[0].'" '.(strpos($goto,$miscdests[0]) === false ? '' : 'SELECTED').'>'.$miscdests[0].':'.$miscdests[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="group" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'group\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'group\';" '.(strpos($goto,'ext-group') === false ? '' : 'CHECKED=CHECKED').' /> '._("Ring Group").': ';
    $selectHtml .=    '<select name="group'.$i.'">';

    if (isset($gresults)) {
        foreach ($gresults as $gresult) {
            $selectHtml .= '<option value="'.$gresult[0].'" '.(strpos( ','.$goto.',' , ','.$gresult[0].',' ) === false ? '' : 'SELECTED').'>'.$gresult[0].':'.$gresult[1].'</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="queue" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'queue\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'queue\';" '.(strpos($goto,'ext-queues') === false ? '' : 'CHECKED=CHECKED').' /> '._("Queue").': ';
    $selectHtml .=    '<select name="queue'.$i.'">';

    if (isset($queues)) {
        foreach ($queues as $queue) {
            $selectHtml .= '<option value="'.$queue[0].'" '.(strpos($goto,$queue[0]) === false ? '' : 'SELECTED').'>'.$queue[0].':'.$queue[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    global $db;
    $sql = "SELECT * FROM globals";
    $incoming_desc = $db->getAll($sql);
    if(DB::IsError($incoming_desc)) {
       die($incoming_desc->getMessage());
    }

    foreach ($incoming_desc as $global) {
    ${trim($global[0])} = $global[1];
}

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="from-pstn" '.(strpos($goto,'from-pstn') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .=    '&nbsp;Incoming Calls: ';
    $selectHtml .=    '<select name="from-pstn_args'.$i.'">';

    if ($INCOMING_DESC != '') {
    $selectHtml .= '<option value="from-pstn-timecheck,s,1" '.(strpos($goto,'from-pstn-timecheck,s,1') === false ? '' : 'SELECTED').'>#1: '.$INCOMING_DESC.'</option>';
    }
    if ($INCOMING_DESC_1 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_1,s,1" '.(strpos($goto,'from-pstn-timecheck_1,s,1') === false ? '' : 'SELECTED').'>#2: '.$INCOMING_DESC_1.'</option>';
    }
    if ($INCOMING_DESC_2 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_2,s,1" '.(strpos($goto,'from-pstn-timecheck_2,s,1') === false ? '' : 'SELECTED').'>#3: '.$INCOMING_DESC_2.'</option>';
    }
    if ($INCOMING_DESC_3 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_3,s,1" '.(strpos($goto,'from-pstn-timecheck_3,s,1') === false ? '' : 'SELECTED').'>#4: '.$INCOMING_DESC_3.'</option>';
    }
    if ($INCOMING_DESC_4 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_4,s,1" '.(strpos($goto,'from-pstn-timecheck_4,s,1') === false ? '' : 'SELECTED').'>#5: '.$INCOMING_DESC_4.'</option>';
    }
    if ($INCOMING_DESC_5 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_5,s,1" '.(strpos($goto,'from-pstn-timecheck_5,s,1') === false ? '' : 'SELECTED').'>#6: '.$INCOMING_DESC_5.'</option>';
    }
    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="custom" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'custom\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'custom\';" '.(strpos($goto,'custom') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .= ' <a href="#" class="info">'._("Custom App<span>Uses Goto() to send caller to a custom context.<br><br>The context name <b>MUST</b> contain the word 'custom' and should be in the format custom-context , extension , priority. Example entry:<br><br><b>custom-myapp,s,1</b><br><br>The <b>[custom-myapp]</b> context would need to be created and included in extensions_custom.conf</span>").'</a>:';
    $selectHtml .=    '<input type="text" size="25" name="custom_args'.$i.'" value="'.(strpos($goto,'custom') === false ? '' : $goto).'" />';
    $selectHtml .=    '</td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="dial" '.(strpos($goto,'outbound-allroutes') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .=  ' <a href="#" class="info">'._("Custom Dial<span>You can include an extension on a remote system, or an external number (Outbound Routing must contain a valid route for external numbers)</span>").'</a>:';

    $hackstart = strpos($goto,'allroutes,')+10;

    $selectHtml .=    '<input type="text" size="20" name="dial_args'.$i.'" value="'.(strpos($goto,'outbound-allroutes') === false ? '' : substr($goto,$hackstart,-2)).'" />';
    $selectHtml .=    '</td></tr>';


    $selectHtml .=    '</table>';

    return $selectHtml;
}

function autodrawselects($formName,$goto,$i,$jumpto) {

    //query for exisiting aa_N contexts
    $unique_aas = getaas();
    //get unique extensions
    $extens = getextens();
    //get unique ring groups
    $gresults = getgroups();
    //get unique queues
    $queues = getqueues();
    //get unique misc destinations
    $miscdest = getmiscdest();

    if ($goto == "") {

    $goto = "ext-local";

    }

    $selectHtml = '<table id="jumpto" bgcolor="#DDDDDD" border="0" cellpadding="1" cellspacing="2" style="display:none;">';
    $selectHtml .=  '<tr><td><input type="hidden" name="goto'.$i.'" value="">';
    $selectHtml .=    '<input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="ivr" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'ivr\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'ivr\';" '.(strpos($goto,'aa_') === false ? '' : 'CHECKED=CHECKED').' /> '._("Digital Receptionist").': ';
    $selectHtml .=    '<select name="ivr'.$i.'">';

    if (isset($unique_aas)) {
        foreach ($unique_aas as $unique_aa) {
            $menu_id = $unique_aa[0];
            $menu_name = $unique_aa[1];
            $selectHtml .= '<option value="'.$menu_id.'" '.(strpos($goto,$menu_id) === false ? '' : 'SELECTED').'>'.($menu_name ? $menu_name : 'Menu ID'.$menu_id) . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="extension" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'extension\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'extension\';" '.(strpos($goto,'ext-local') === false ? '' : 'CHECKED=CHECKED').'/> '._("Extension").': ';
    $selectHtml .=    '<select name="extension'.$i.'">';

    if (isset($extens)) {
        foreach ($extens as $exten) {
            $selectHtml .= '<option value="'.$exten[0].'" '.(strpos($goto,'ext-local,'.$exten[0]) === false ? '' : 'SELECTED').'>'.$exten[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="miscdest" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'miscdest\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'miscdest\';" '.(strpos($goto,'ext-miscdests') === false ? '' : 'CHECKED=CHECKED').'/> '._("Misc Dest").': ';
    $selectHtml .=    '<select name="miscdest'.$i.'">';

    if (isset($miscdest)) {
        foreach ($miscdest as $miscdests) {
            $selectHtml .= '<option value="'.$miscdests[0].'" '.(strpos($goto,$miscdests[0]) === false ? '' : 'SELECTED').'>'.$miscdests[0].':'.$miscdests[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="group" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'group\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'group\';" '.(strpos($goto,'ext-group') === false ? '' : 'CHECKED=CHECKED').' /> '._("Ring Group").': ';
    $selectHtml .=    '<select name="group'.$i.'">';

    if (isset($gresults)) {
        foreach ($gresults as $gresult) {
            $selectHtml .= '<option value="'.$gresult[0].'" '.(strpos( ','.$goto.',' , ','.$gresult[0].',' ) === false ? '' : 'SELECTED').'>'.$gresult[0].':'.$gresult[1].'</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="queue" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'queue\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'queue\';" '.(strpos($goto,'ext-queues') === false ? '' : 'CHECKED=CHECKED').' /> '._("Queue").': ';
    $selectHtml .=    '<select name="queue'.$i.'">';

    if (isset($queues)) {
        foreach ($queues as $queue) {
            $selectHtml .= '<option value="'.$queue[0].'" '.(strpos($goto,$queue[0]) === false ? '' : 'SELECTED').'>'.$queue[0].':'.$queue[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    global $db;
    $sql = "SELECT * FROM globals";
    $incoming_desc = $db->getAll($sql);
    if(DB::IsError($incoming_desc)) {
       die($incoming_desc->getMessage());
    }

    foreach ($incoming_desc as $global) {
    ${trim($global[0])} = $global[1];
}

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="from-pstn" '.(strpos($goto,'from-pstn') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .=    '&nbsp;Incoming Calls: ';
    $selectHtml .=    '<select name="from-pstn_args'.$i.'">';

    if ($INCOMING_DESC != '') {
    $selectHtml .= '<option value="from-pstn-timecheck,s,1" '.(strpos($goto,'from-pstn-timecheck,s,1') === false ? '' : 'SELECTED').'>#1: '.$INCOMING_DESC.'</option>';
    }
    if ($INCOMING_DESC_1 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_1,s,1" '.(strpos($goto,'from-pstn-timecheck_1,s,1') === false ? '' : 'SELECTED').'>#2: '.$INCOMING_DESC_1.'</option>';
    }
    if ($INCOMING_DESC_2 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_2,s,1" '.(strpos($goto,'from-pstn-timecheck_2,s,1') === false ? '' : 'SELECTED').'>#3: '.$INCOMING_DESC_2.'</option>';
    }
    if ($INCOMING_DESC_3 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_3,s,1" '.(strpos($goto,'from-pstn-timecheck_3,s,1') === false ? '' : 'SELECTED').'>#4: '.$INCOMING_DESC_3.'</option>';
    }
    if ($INCOMING_DESC_4 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_4,s,1" '.(strpos($goto,'from-pstn-timecheck_4,s,1') === false ? '' : 'SELECTED').'>#5: '.$INCOMING_DESC_4.'</option>';
    }
    if ($INCOMING_DESC_5 != '') {
    $selectHtml .= '<option value="from-pstn-timecheck_5,s,1" '.(strpos($goto,'from-pstn-timecheck_5,s,1') === false ? '' : 'SELECTED').'>#6: '.$INCOMING_DESC_5.'</option>';
    }
    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="custom" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'custom\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'custom\';" '.(strpos($goto,'custom') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .= ' <a href="#" class="info">'._("Custom App<span>Uses Goto() to send caller to a custom context.<br><br>The context name <b>MUST</b> contain the word 'custom' and should be in the format custom-context , extension , priority. Example entry:<br><br><b>custom-myapp,s,1</b><br><br>The <b>[custom-myapp]</b> context would need to be created and included in extensions_custom.conf</span>").'</a>:';
    $selectHtml .=    '<input type="text" size="25" name="custom_args'.$i.'" value="'.(strpos($goto,'custom') === false ? '' : $goto).'" />';
    $selectHtml .=    '</td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="dial" '.(strpos($goto,'outbound-allroutes') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .=  ' <a href="#" class="info">'._("Custom Dial<span>You can include an extension on a remote system, or an external number (Outbound Routing must contain a valid route for external numbers)</span>").'</a>:';

    $hackstart = strpos($goto,'allroutes,')+10;

    $selectHtml .=    '<input type="text" size="20" name="dial_args'.$i.'" value="'.(strpos($goto,'outbound-allroutes') === false ? '' : substr($goto,$hackstart,-2)).'" />';
    $selectHtml .=    '</td></tr>';


    $selectHtml .=    '</table>';

    return $selectHtml;
}

function ivrdrawselects($formName,$goto,$i) {

    $unique_aas = getaas();
    $extens = getextens();
    $gresults = getgroups();
    $queues = getqueues();
    $miscdest = getmiscdest();

    if (isset($extens)) {
        //get voicemail
        $uservm = getVoicemail();
        $vmcontexts = array_keys($uservm);
        foreach ($extens as $thisext) {
            $extnum = $thisext[0];
            // search vm contexts for this extensions mailbox
            foreach ($vmcontexts as $vmcontext) {
                if(isset($uservm[$vmcontext][$extnum])){
                    $vmname = $uservm[$vmcontext][$extnum]['name'];
                    $vmboxes[] = array($extnum, '"' . $vmname . '" <' . $extnum . '>');
                }
            }
        }
    }

    $selectHtml = '<table border="0" cellpadding="1" cellspacing="2" id="loopdestination"';

    if ($goto == "") {    $selectHtml .= 'style="display:none;"';    }

    $selectHtml .= '>';

    $selectHtml .=  '<tr><td><h5>Set Destination:</h5></td></tr>';

    $selectHtml .=  '<tr><td></td></tr>';

    $selectHtml .=  '<tr><td><input type="hidden" name="goto'.$i.'" value="">';

    $selectHtml .=    '<input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="ivr" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'ivr\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'ivr\';" '.(strpos($goto,'aa_') === false ? '' : 'CHECKED=CHECKED').' /> '._("Digital Receptionist").': ';
    $selectHtml .=    '<select name="ivr'.$i.'">';

    if (isset($unique_aas)) {
        foreach ($unique_aas as $unique_aa) {
            $menu_id = $unique_aa[0];
            $menu_name = $unique_aa[1];
            $selectHtml .= '<option value="'.$menu_id.'" '.(strpos($goto,$menu_id) === false ? '' : 'SELECTED').'>'.($menu_name ? $menu_name : 'Menu ID'.$menu_id) . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="extension" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'extension\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'extension\';" '.(strpos($goto,'ext-local') === false ? '' : 'CHECKED=CHECKED').'/> '._("Extension").': ';
    $selectHtml .=    '<select name="extension'.$i.'">';

    if (isset($extens)) {
        foreach ($extens as $exten) {
            $selectHtml .= '<option value="'.$exten[0].'" '.(strpos($goto,$exten[0]) === false ? '' : 'SELECTED').'>'.$exten[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="voicemail" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'voicemail\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'voicemail\';" '.(strpos($goto,'vm') === false ? '' : 'CHECKED=CHECKED').(strpos($goto,'ext-local,${VM_PREFIX}') === false ? '' : 'CHECKED=CHECKED').' /> '._("Voicemail").': ';
    $selectHtml .=    '<select name="voicemail'.$i.'">';

    if (isset($vmboxes)) {
        foreach ($vmboxes as $exten) {
            $selectHtml .= '<option value="'.$exten[0].'" '.(strpos($goto,$exten[0]) === false ? '' : 'SELECTED').'>'.$exten[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="group" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'group\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'group\';" '.(strpos($goto,'ext-group') === false ? '' : 'CHECKED=CHECKED').' /> '._("Ring Group").': ';
    $selectHtml .=    '<select name="group'.$i.'">';

    if (isset($gresults)) {
        foreach ($gresults as $gresult) {
            $selectHtml .= '<option value="'.$gresult[0].'" '.(strpos( ','.$goto.',' , ','.$gresult[0].',' ) === false ? '' : 'SELECTED').'>'.$gresult[0].':'.$gresult[1].'</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="queue" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'queue\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'queue\';" '.(strpos($goto,'ext-queues') === false ? '' : 'CHECKED=CHECKED').' /> '._("Queue").': ';
    $selectHtml .=    '<select name="queue'.$i.'">';

    if (isset($queues)) {
        foreach ($queues as $queue) {
            $selectHtml .= '<option value="'.$queue[0].'" '.(strpos($goto,$queue[0]) === false ? '' : 'SELECTED').'>'.$queue[0].':'.$queue[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="miscdest" onclick="javascript:document.'.$formName.'.goto'.$i.'.value=\'miscdest\';" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.'.$formName.'.goto'.$i.'.value=\'miscdest\';" '.(strpos($goto,'ext-miscdests') === false ? '' : 'CHECKED=CHECKED').'/> '._("Misc Destinations").': ';
    $selectHtml .=    '<select name="miscdest'.$i.'">';

    if (isset($miscdest)) {
        foreach ($miscdest as $miscdests) {
            $selectHtml .= '<option value="'.$miscdests[0].'" '.(strpos($goto,$miscdests[0]) === false ? '' : 'SELECTED').'>'.$miscdests[0].':'.$miscdests[1] . '</option>';
        }
    }

    $selectHtml .=    '</select></td></tr>';

    $selectHtml .=    '<tr><td><input type="radio" onFocus="this.blur()" name="goto_indicate'.$i.'" value="dial" '.(strpos($goto,'outbound-allroutes') === false ? '' : 'CHECKED=CHECKED').' />';
    $selectHtml .=  ' <a href="#" class="info">'._("Custom Dial<span>You can include an extension on a remote system, or an external number (Outbound Routing must contain a valid route for external numbers)</span>").'</a>:';

    $hackstart = strpos($goto,',')+1;

    $selectHtml .=    '<input type="text" size="20" name="dial_args'.$i.'" value="'.(strpos($goto,'outbound-allroutes') === false ? '' : substr($goto,$hackstart,-2)).'" />';
    $selectHtml .=    '</td></tr>';

    $selectHtml .=    '</table>';

    return $selectHtml;
}

function setGotoJumpTo($goto,$i) {
    if ($goto == 'extension') {
        $args = 'ext-local,'.$_REQUEST['extension'.$i].',1';
    }
    elseif ($goto == 'voicemail') {
        $args = 'vm,'.$_REQUEST['voicemail'.$i];
    }
    elseif ($goto == 'ivr') {
        $args = $_REQUEST['ivr'.$i].',s,1';
    }
    elseif ($goto == 'group') {
        $args = 'ext-group,'.$_REQUEST['group'.$i].',1';
    }
    elseif ($goto == 'custom') {
        $args = $_REQUEST['custom_args'.$i];
    }
    elseif ($goto == 'queue') {
        $args = 'ext-queues,'.$_REQUEST['queue'.$i].',1';
    }
    elseif ($goto == 'fax') {
        $args = $_REQUEST['fax_args'.$i];
    }
    elseif ($goto == 'from-pstn') {
        $args = $_REQUEST['from-pstn_args'.$i];
    }
    elseif ($goto == 'dial') {
        $args = 'outbound-allroutes,'.$_REQUEST['dial_args'.$i].',1';
    }
    elseif ($goto == 'callback') {
        $args = $_REQUEST['callback_args'.$i];
    }
    elseif ($goto == 'callbackext') {
        $args = $_REQUEST['callbackext_args'.$i];
    }
    elseif ($goto == 'meetme') {
        $args = 'ext-meetme,'.$_REQUEST['meetme'.$i].',1';
    }
    elseif ($goto == 'miscdest') {
        $args = 'ext-miscdests,'.$_REQUEST['miscdest'.$i].',1';
    }
    if ($goto == 'extensiona2b') {
        $args = 'ext-local-a2b,'.$_REQUEST['extensiona2b'.$i].',1';
    }
    return $args;
}

function setGoto($account,$context,$priority,$goto,$i) {  //preforms logic for setting goto destinations
    if ($goto == 'extension') {
        $args = 'ext-local,'.$_REQUEST['extension'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    elseif ($goto == 'voicemail') {
        $args = 'vm,'.$_REQUEST['voicemail'.$i];
        $addarray = array($context,$account,$priority,'Macro',$args.',DIRECTDIAL','','0');
        addextensions($addarray);
    }
    elseif ($goto == 'ivr') {
        $args = $_REQUEST['ivr'.$i].',s,1';
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    elseif ($goto == 'group') {
        $args = 'ext-group,'.$_REQUEST['group'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    elseif ($goto == 'custom') {
        $args = $_REQUEST['custom_args'.$i];
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    elseif ($goto == 'queue') {
        $args = 'ext-queues,'.$_REQUEST['queue'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    elseif ($goto == 'fax') {
        $args = $_REQUEST['fax_args'.$i];
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    elseif ($goto == 'from-pstn') {
        $args = $_REQUEST['from-pstn_args'.$i];
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    elseif ($goto == 'dial') {
        $args = 'outbound-allroutes,'.$_REQUEST['dial_args'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    elseif ($goto == 'callback') {
        $args = $_REQUEST['callback_args'.$i];
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    elseif ($goto == 'callbackext') {
        $args = $_REQUEST['callbackext_args'.$i];
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    elseif ($goto == 'meetme') {
        $args = 'ext-meetme,'.$_REQUEST['meetme'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    elseif ($goto == 'miscdest') {
        $args = 'ext-miscdests,'.$_REQUEST['miscdest'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    elseif ($goto == 'extensiona2b') {
        $args = 'ext-local-a2b,'.$_REQUEST['extensiona2b'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,'','0');
        addextensions($addarray);
    }
    return $args;
}

function setGotoMiscDest($account,$context,$priority,$goto,$i,$description) {  //preforms logic for setting goto destinations
    if ($goto == 'extension') {
        $args = 'ext-local,'.$_REQUEST['extension'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,$description,'0');
        addextensions($addarray);
    }
    elseif ($goto == 'voicemail') {
        $args = 'vm,'.$_REQUEST['voicemail'.$i];
        $addarray = array($context,$account,$priority,'Macro',$args.',DIRECTDIAL',$description,'0');
        addextensions($addarray);
    }
    elseif ($goto == 'ivr') {
        $args = $_REQUEST['ivr'.$i].',s,1';
        $addarray = array($context,$account,$priority,'Goto',$args,$description,'0');
        addextensions($addarray);
    }
    elseif ($goto == 'group') {
        $args = 'ext-group,'.$_REQUEST['group'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,$description,'0');
        addextensions($addarray);
    }
    elseif ($goto == 'custom') {
        $args = $_REQUEST['custom_args'.$i];
        $addarray = array($context,$account,$priority,'Goto',$args,$description,'0');
        addextensions($addarray);
    }
    elseif ($goto == 'queue') {
        $args = 'ext-queues,'.$_REQUEST['queue'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,$description,'0');
        addextensions($addarray);
    }
    elseif ($goto == 'fax') {
        $args = $_REQUEST['fax_args'.$i];
        $addarray = array($context,$account,$priority,'Goto',$args,$description,'0');
        addextensions($addarray);
    }
    elseif ($goto == 'from-pstn') {
        $args = $_REQUEST['from-pstn_args'.$i];
        $addarray = array($context,$account,$priority,'Goto',$args,$description,'0');
        addextensions($addarray);
    }
    elseif ($goto == 'dial') {
        $args = 'outbound-allroutes,'.$_REQUEST['dial_args'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,$description,'0');
        addextensions($addarray);
    }
    elseif ($goto == 'callback') {
        $args = $_REQUEST['callback_args'.$i];
        $addarray = array($context,$account,$priority,'Goto',$args,$description,'0');
        addextensions($addarray);

        }
    elseif ($goto == 'callbackext') {
        $args = $_REQUEST['callbackext_args'.$i];
        $addarray = array($context,$account,$priority,'Goto',$args,$description,'0');
        addextensions($addarray);

        }
    elseif ($goto == 'meetme') {
        $args = 'ext-meetme,'.$_REQUEST['meetme'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,$description,'0');
        addextensions($addarray);
    }
    elseif ($goto == 'extensiona2b') {
        $args = 'ext-local-a2b,'.$_REQUEST['extensiona2b'.$i].',1';
        $addarray = array($context,$account,$priority,'Goto',$args,$description,'0');
        addextensions($addarray);
    }    

    return $args;
}


// the old drawselects stuff above builds the select forms using abbreviated goto names..
// setGoto then translates these into a full goto string, which is used in the dialplan.
// terrible, I know.  New functionality, like Inbound Routing, stores the "full goto" string in it's table
// This function just returns what the full goto is supposed to be. (will bo obsolete in AMP2).
function buildActualGoto($requestarray,$i) {
    switch($requestarray['goto'.$i]) {
        case 'extension':
            return 'ext-local,'.$requestarray['extension'.$i].',1';
        break;
        case 'voicemail':
            return 'ext-local,${VM_PREFIX}'.$requestarray['voicemail'.$i].',1';
        break;
        case 'ivr':
            return $requestarray['ivr'.$i].',s,1';
        break;
        case 'group':
            return 'ext-group,'.$requestarray['group'.$i].',1';
        break;
        case 'custom':
            return $requestarray['custom_args'.$i];
        break;
        case 'queue':
            return 'ext-queues,'.$requestarray['queue'.$i].',1';
        break;
        case 'fax':
            return $requestarray['fax_args'.$i];
        break;
        case 'from-pstn':
            return $requestarray['from-pstn_args'.$i];
        break;
        case 'dial':
            return 'outbound-allroutes,'.$requestarray['dial_args'.$i].',1';
        break;
        case 'callback':
            return $requestarray['callback_args'.$i];
        break;
        case 'callbackext':
            return $requestarray['callbackext_args'.$i];
        break;
        case 'meetme':
            return 'ext-meetme,'.$requestarray['meetme'.$i].',1';
        break;
        case 'miscdest':
            return 'ext-miscdests,'.$requestarray['miscdest'.$i].',1';
        break;
        case 'extensiona2b':
            return 'ext-local-a2b,'.$requestarray['extensiona2b'.$i].',1';
        break;
        default:
            return $requestarray['goto'.$i];
        break;
        }
}

//get args for specified exten and priority - primarily used to grab goto destination
function getargs($exten,$priority,$context) {
    global $db;
    $sql = "SELECT args FROM extensions WHERE extension = '".$exten."' AND priority = '".$priority."' AND context = '".$context."'";
    list($args) = $db->getRow($sql);
    return $args;
}

function group_list() {
    $sql = "SELECT extension FROM extensions WHERE context = 'ext-group'";
    $results = sql($sql,"getAll");

    foreach($results as $result){
        if (checkRange($result[0])){
            $extens[] = array($result[0]);
        }
    }
    if (isset($extens)) {
        sort($extens);
        return $extens;
    } else {
        return null;
    }
}

function addgroup($account,$grplist,$grpstrategy,$grptime,$grppre,$goto,$callerannounce,$alertinfo,$ringing,$description) {
    global $db;

    $devices = group_list();
    if (is_array($devices)) {
        foreach($devices as $device) {
            if ($device[0] === $account) {
                    echo "<script>javascript:alert('"._("This RingGroup [").$device[0].("] is already in use")."');</script>";
                return false;
            }
        }
    }


        if($ringing == 'Ring' || empty($ringing) ) {
            $ringing = '${DIAL_OPTIONS}';
        } else {
            // We need the DIAL_OPTIONS variable
            $sops = sql("SELECT value from globals where variable='DIAL_OPTIONS'", "getRow");
            $ringing = "m(".$ringing.")".str_replace('r', '', $sops[0]);
        }



    $addarray = array('ext-group',$account,'1','Macro','rg-group,'.$grpstrategy.','.$grptime.','.$grppre.','.$grplist.','.$callerannounce.','.$alertinfo.','.$ringing,''.$description.'','0');
    addextensions($addarray);

    setGoto($account,'ext-group','2',$goto,0);

    return true;
}

/** Recursively read voicemail.conf (and any included files)
 * This function is called by getVoicemailConf()
 */
function parse_voicemailconf($filename, &$vmconf, &$section) {
    if (is_null($vmconf)) {
        $vmconf = array();
    }
    if (is_null($section)) {
        $section = "default";
    }

    if (file_exists($filename)) {
        $fd = fopen($filename, "r");
        while ($line = fgets($fd, 1024)) {
            if (preg_match("/^\s*(\d+)\s*=>\s*(\d*),(.*),(.*),(.*),(.*)\s*([;#].*)?/",$line,$matches)) {
                // "mailbox=>password,name,email,pager,options"
                // this is a voicemail line
                $vmconf[$section][ $matches[1] ] = array("mailbox"=>$matches[1],
                                    "pwd"=>$matches[2],
                                    "name"=>$matches[3],
                                    "email"=>$matches[4],
                                    "pager"=>$matches[5],
                                    "options"=>array(),
                                    );

                // parse options
                //output($matches);
                foreach (explode("|",$matches[6]) as $opt) {
                    $temp = explode("=",$opt);
                    //output($temp);
                    if (isset($temp[1])) {
                        list($key,$value) = $temp;
                        $vmconf[$section][ $matches[1] ]["options"][$key] = $value;
                    }
                }
            } else if (preg_match("/^\s*(\d+)\s*=>\s*dup,(.*)\s*([;#].*)?/",$line,$matches)) {
                // "mailbox=>dup,name"
                // duplace name line
                $vmconf[$section][ $matches[1] ]["dups"][] = $matches[2];
            } else if (preg_match("/^\s*#include\s+(.*)\s*([;#].*)?/",$line,$matches)) {
                // include another file

                if ($matches[1][0] == "/") {
                    // absolute path
                    $filename = $matches[1];
                } else {
                    // relative path
                    $filename =  dirname($filename)."/".$matches[1];
                }

                parse_voicemailconf($filename, $vmconf, $section);

            } else if (preg_match("/^\s*\[(.+)\]/",$line,$matches)) {
                // section name
                $section = strtolower($matches[1]);
            } else if (preg_match("/^\s*([a-zA-Z0-9-_]+)\s*=\s*(.*?)\s*([;#].*)?$/",$line,$matches)) {
                // name = value
                // option line
                $vmconf[$section][ $matches[1] ] = $matches[2];
            }
        }
        fclose($fd);
    }
}

function getVoicemail() {
    $vmconf = null;
    $section = null;

    // yes, this is hardcoded.. is this a bad thing?
    parse_voicemailconf("/etc/asterisk/voicemail.conf", $vmconf, $section);

    return $vmconf;
}

/** Write the voicemail.conf file
 * This is called by saveVoicemail()
 * It's important to make a copy of $vmconf before passing it. Since this is a recursive function, has to
 * pass by reference. At the same time, it removes entries as it writes them to the file, so if you don't have
 * a copy, by the time it's done $vmconf will be empty.
*/
function write_voicemailconf($filename, &$vmconf, &$section, $iteration = 0) {
    if ($iteration == 0) {
        $section = null;
    }

    $output = array();

    if (file_exists($filename)) {
        $fd = fopen($filename, "r");
        while ($line = fgets($fd, 1024)) {
            if (preg_match("/^(\s*)(\d+)(\s*)=>(\s*)(\d*),(.*),(.*),(.*),(.*)(\s*[;#].*)?$/",$line,$matches)) {
                // "mailbox=>password,name,email,pager,options"
                // this is a voicemail line
                // make sure we have something as a comment
                if (!isset($matches[10])) {
                    $matches[10] = "";
                }

                // $matches[1] [3] and [4] are to preserve indents/whitespace, we add these back in

                if (isset($vmconf[$section][ $matches[2] ])) {
                    // we have this one loaded
                    // repopulate from our version
                    $temp = & $vmconf[$section][ $matches[2] ];

                    $options = array();
                    foreach ($temp["options"] as $key=>$value) {
                        $options[] = $key."=".$value;
                    }

                    $output[] = $matches[1].$temp["mailbox"].$matches[3]."=>".$matches[4].$temp["pwd"].",".$temp["name"].",".$temp["email"].",".$temp["pager"].",". implode("|",$options).$matches[10];

                    // remove this one from $vmconf
                    unset($vmconf[$section][ $matches[2] ]);
                } else {
                    // we don't know about this mailbox, so it must be deleted
                    // (and hopefully not JUST added since we did read_voiceamilconf)

                    // do nothing
                }

            } else if (preg_match("/^(\s*)(\d+)(\s*)=>(\s*)dup,(.*)(\s*[;#].*)?$/",$line,$matches)) {
                // "mailbox=>dup,name"
                // duplace name line
                // leave it as-is (for now)
                $output[] = $line;
            } else if (preg_match("/^(\s*)#include(\s+)(.*)(\s*[;#].*)?$/",$line,$matches)) {
                // include another file
                // make sure we have something as a comment
                if (!isset($matches[4])) {
                    $matches[4] = "";
                }

                if ($matches[3][0] == "/") {
                    // absolute path
                    $include_filename = $matches[3];
                } else {
                    // relative path
                    $include_filename =  dirname($filename)."/".$matches[3];
                }

                $output[] = $matches[1]."#include".$matches[2].$matches[3].$matches[4];
                write_voicemailconf($include_filename, $vmconf, $section, $iteration+1);

            } else if (preg_match("/^(\s*)\[(.+)\](\s*[;#].*)?$/",$line,$matches)) {
                // section name
                // make sure we have something as a comment
                if (!isset($matches[3])) {
                    $matches[3] = "";
                }

                // check if this is the first run (section is null)
                if ($section !== null) {
                    // we need to add any new entries here, before the section changes
                    if (isset($vmconf[$section])){  //need this, or we get an error if we unset the last items in this section - should probably automatically remove the section/context from voicemail.conf
                        foreach ($vmconf[$section] as $key=>$value) {
                            if (is_array($value)) {
                                // mailbox line

                                $temp = & $vmconf[$section][ $key ];

                                $options = array();
                                foreach ($temp["options"] as $key1=>$value) {
                                    $options[] = $key1."=".$value;
                                }

                                $output[] = $temp["mailbox"]." => ".$temp["pwd"].",".$temp["name"].",".$temp["email"].",".$temp["pager"].",". implode("|",$options);

                                // remove this one from $vmconf
                                unset($vmconf[$section][ $key ]);

                            } else {
                                // option line

                                $output[] = $key."=".$vmconf[$section][ $key ];

                                // remove this one from $vmconf
                                unset($vmconf[$section][ $key ]);
                            }
                        }
                    }
                }

                $section = strtolower($matches[2]);
                $output[] = $matches[1]."[".$section."]".$matches[3];
                $existing_sections[] = $section; //remember that this section exists

            } else if (preg_match("/^(\s*)([a-zA-Z0-9-_]+)(\s*)=(\s*)(.*?)(\s*[;#].*)?$/",$line,$matches)) {
                // name = value
                // option line
                // make sure we have something as a comment
                if (!isset($matches[6])) {
                    $matches[6] = "";
                }

                if (isset($vmconf[$section][ $matches[2] ])) {
                    $output[] = $matches[1].$matches[2].$matches[3]."=".$matches[4].$vmconf[$section][ $matches[2] ].$matches[6];

                    // remove this one from $vmconf
                    unset($vmconf[$section][ $matches[2] ]);
                }
                // else it's been deleted, so we don't write it in

            } else {

                $output[] = str_replace(array("\n","\r"),"",$line); // str_replace so we don't double-space
            }
        }

        if ($iteration == 0) {

            foreach (array_keys($vmconf) as $section) {
                if (!in_array($section,$existing_sections))  // If this is a new section, write the context label
                    $output[] = "[".$section."]";
                foreach ($vmconf[$section] as $key=>$value) {
                    if (is_array($value)) {
                        // mailbox line

                        $temp = & $vmconf[$section][ $key ];

                        $options = array();
                        foreach ($temp["options"] as $key=>$value) {
                            $options[] = $key."=".$value;
                        }

                        $output[] = $temp["mailbox"]." => ".$temp["pwd"].",".$temp["name"].",".$temp["email"].",".$temp["pager"].",". implode("|",$options);

                        // remove this one from $vmconf
                        unset($vmconf[$section][ $key ]);

                    } else {
                        // option line

                        $output[] = $key."=".$vmconf[$section][ $key ];

                        // remove this one from $vmconf
                        unset($vmconf[$section][$key ]);
                    }
                }
            }
        }

        fclose($fd);

        if ($fd = fopen($filename, "w")) {
            fwrite($fd, implode("\n",$output)."\n");
            fclose($fd);
        }

    }
}

function saveVoicemail($vmconf) {
    // yes, this is hardcoded.. is this a bad thing?
    write_voicemailconf("/etc/asterisk/voicemail.conf", $vmconf, $section);
}

function getsystemrecordings($path) {
    $i = 0;
    $arraycount = 0;

    if (is_dir($path)){
        if ($handle = opendir($path)){
            while (false !== ($file = readdir($handle))){
                if (($file != ".") && ($file != "..") && ($file != "CVS") && (strpos($file, "aa_") === FALSE)    )
                {
                    $file_parts=explode(".",$file);
                    $filearray[($i++)] = $file_parts[0];
                }
            }
        closedir($handle);
        }

    }
    if (isset($filearray)) {
        sort($filearray);
        return ($filearray);
    } else {
        return null;
    }

}

function getmusiccategory($path) {
    $i = 0;
    $arraycount = 0;

    if (is_dir($path)){
        if ($handle = opendir($path)){
            while (false !== ($file = readdir($handle))){
                if ( ($file != ".") && ($file != "..") && ($file != "CVS")  )
                {
                    if (is_dir("$path/$file"))
                        $filearray[($i++)] = "$file";
                }
            }
        closedir($handle);
        }
    }
    if (isset($filearray)) {
        sort($filearray);
        return ($filearray);
    } else {
        return null;
    }

}

function rmdirr($dirname)
{
    // Sanity check
    if (!file_exists($dirname)) {
        return false;
    }

    // Simple delete for a file
    if (is_file($dirname)) {
        return unlink($dirname);
    }

    // Loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Recurse
        rmdirr("$dirname/$entry");
    }

    // Clean up
    $dir->close();
    return rmdir($dirname);
}
function backuptableexists() {
        global $db;

        $sql ="CREATE TABLE IF NOT EXISTS `Backup` (";
                $sql.="`Name` varchar(50) default NULL,";
                $sql.="`Voicemail` varchar(50) default NULL,";
                $sql.="`Recordings` varchar(50) default NULL,";
                $sql.="`Configurations` varchar(50) default NULL,";
                $sql.="`CDR` varchar(55) default NULL,";
                $sql.="`FOP` varchar(50) default NULL,";
                $sql.="`Minutes` varchar(50) default NULL,";
                $sql.="`Hours` varchar(50) default NULL,";
                $sql.="`Days` varchar(50) default NULL,";
                $sql.="`Months` varchar(50) default NULL,";
                $sql.="`Weekdays` varchar(50) default NULL,";
                $sql.="`Command` varchar(200) default NULL,";
                $sql.="`Method` varchar(50) default NULL,";
                $sql.="`ID` int(11) NOT NULL auto_increment,";
                $sql.="PRIMARY KEY  (ID))";
        $results = $db->query($sql);
}

function setrecordingstatus($extension, $direction, $state) {
    require_once('common/php-asmanager.php');
    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $hosts =split(',',$amp_conf['MANAGERHOSTS']);
    foreach ($hosts as $host) {
        $astman = new AGI_AsteriskManager();
        if ($res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {
            if ($direction=="In"){
                if ($state=="Always")
                    $astman->database_put("RECORD-IN",$extension,"ENABLED");
                else
                        $astman->database_del("RECORD-IN",$extension);
            } else if ($direction=="Out") {
                if ($state=="Always")
                    $astman->database_put("RECORD-OUT",$extension,"ENABLED");
                else
                        $astman->database_del("RECORD-OUT",$extension);
            }
            $astman->disconnect();
        } else {
                echo "<h3>Cannot connect to Asterisk Manager $host with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]."</h3>This module requires access to the Asterisk Manager.  Please ensure Asterisk is running and access to the manager is available.</div>";
                exit;
        }
    }
}

function setnocallstatus($extension, $state, $name) {
    require_once('common/php-asmanager.php');
    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $hosts =split(',',$amp_conf['MANAGERHOSTS']);
    foreach ($hosts as $host) {
        $astman = new AGI_AsteriskManager();
        if ($res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {
            if ($state!="") {
                $astman->database_put($name,$extension,$state);
            } else {
                    $astman->database_del($name,$extension);
            }
            $astman->disconnect();
        } else {
                echo "<h3>Cannot connect to Asterisk Manager $host with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]."</h3>This module requires access to the Asterisk Manager.  Please ensure Asterisk is running and access to the manager is available.</div>";
                exit;
        }
    }
}

function deleteastdb($extension) {
    require_once('common/php-asmanager.php');
    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $hosts =split(',',$amp_conf['MANAGERHOSTS']);
    foreach ($hosts as $host) {
        $astman = new AGI_AsteriskManager();
        if ($res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {
            if ($extension) {
                $astman->database_del("ALLOWCALL",$extension);
                $astman->database_del("NOCALL",$extension);
                $astman->database_del("CW",$extension);
                $astman->database_del("CALLTRACE",$extension);
                $astman->database_del("CF",$extension);
                $astman->database_del("CFB",$extension);
                $astman->database_del("CFU",$extension);
                $astman->database_del("SEG",$extension);
                $astman->database_del("DND",$extension);
                $astman->database_del("RECORD-IN",$extension);
                $astman->database_del("RECORD-OUT",$extension);
                $astman->database_del("SIP/Registry",$extension);
                $astman->database_del("IAX/Registry",$extension);
                $astman->database_del("ROBCHECK",$extension);
                $astman->disconnect();
            }
        } else {
                echo "<h3>Cannot connect to Asterisk Manager $host with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]."</h3>This module requires access to the Asterisk Manager.  Please ensure Asterisk is running and access to the manager is available.</div>";
                exit;
        }
    }
}

function conferences_list() {
    $results = sql("SELECT exten,description FROM meetme","getAll",DB_FETCHMODE_ASSOC);
    foreach($results as $result){
    $extens[] = array($result['exten'],$result['description']);
    }
    return $extens;
}

function conferences_get($account){
    //get all the variables for the meetme
    $results = sql("SELECT exten,options,userpin,adminpin,description,language FROM meetme WHERE exten = '$account'","getRow",DB_FETCHMODE_ASSOC);
    return $results;
}

function conferences_del($account){
    $results = sql("DELETE FROM meetme WHERE exten = \"$account\"","query");
}

function conferences_add($account,$name,$userpin,$adminpin,$language,$options){
    $results = sql("INSERT INTO meetme (exten,description,userpin,adminpin,language,options) values (\"$account\",\"$name\",\"$userpin\",\"$adminpin\",\"$language\",\"$options\")");
}

function speeddial_list() {
    $results = sql("SELECT id,speednr,name,telnr,permission FROM speednr ORDER BY speednr ASC","getAll",DB_FETCHMODE_ASSOC);
    foreach($results as $result){
    $extens[] = array($result['speednr'],$result['name'],$result['permission']);
    }
    return $extens;
}

function speeddial_get($speednr){
    //get all the variables for the meetme
    $results = sql("SELECT id,speednr,name,telnr,permission FROM speednr WHERE speednr = '$speednr'","getRow",DB_FETCHMODE_ASSOC);
    return $results;
}

function speeddial_del($speednr){
    $results = sql("DELETE FROM speednr WHERE speednr = \"$speednr\"","query");
}

function speeddial_check_add() {
    $sql = "SELECT speednr FROM speednr";
    $results = sql($sql,"getAll");

    foreach($results as $result){
        if (checkRange($result[0])){
            $extens[] = array($result[0]);
        }
    }
    if (isset($extens)) {
        sort($extens);
        return $extens;
    } else {
        return null;
    }
}

function speeddial_add($speednr,$name,$telnr,$permission){
    $devices = speeddial_check_add();
    if (is_array($devices)) {
        foreach($devices as $device) {
            if ($device[0] === $speednr) {
                    echo "<script>javascript:alert('"._("This SpeedDial Number [").$device[0].("] is already in use")."');</script>";
                return false;
            }
        }
    }

    $results = sql("INSERT INTO speednr (speednr,name,telnr,permission) values (\"$speednr\",\"$name\",\"$telnr\",\"$permission\")");
    return true;
}

function speeddial_edit($speednr,$name,$telnr,$permission){
    $results = sql("UPDATE speednr SET speednr=\"$speednr\", name=\"$name\", telnr=\"$telnr\", permission=\"$permission\" where speednr=\"$speednr\"");
}

function manager_gen_conf() {
    $file = "/tmp/manager_additional_".rand().".conf";
    $content = "";
    $managers = manager_list();
    if (is_array($managers)) {
        foreach ($managers as $manager) {
            $res = manager_get($manager['name']);
            $content .= "[".$res['name']."]\n";
            $content .= "secret = ".$res['secret']."\n";
            $tmp = explode("&", $res['deny']);
            foreach ($tmp as $item) {
                $content .= "deny=$item\n";
            }
            $tmp = explode("&", $res['permit']);
            foreach ($tmp as $item) {
                $content .= "permit=$item\n";
            }
            $content .= "read = ".$res['read']."\n";
            $content .= "write = ".$res['write']."\n";
            $content .= "\n";
        }
    }
    $fd = fopen($file, "w");
    fwrite($fd, $content);
    fclose($fd);
    if (!rename($file, "/etc/asterisk/manager_additional.conf")) {
        echo "<script>javascript:alert('"._("Error writing the manager additional file.")."');</script>";
    }
}

// Get the manager list
function manager_list() {
    global $db;
    $sql = "SELECT name FROM manager ORDER BY name";
    $res = $db->getAll($sql, DB_FETCHMODE_ASSOC);
    if(DB::IsError($res)) {
        return null;
    }
    return $res;
}

// Get manager infos
function manager_get($p_name) {
    global $db;
    $sql = "SELECT name,secret,deny,permit,`read`,`write` FROM manager WHERE name = '$p_name'";
    $res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
    return $res;
}

// Used to set the correct values for the html checkboxes
function manager_format_out($p_tab) {
    $res['name'] = $p_tab['name'];
    $res['secret'] = $p_tab['secret'];
    $res['deny'] = $p_tab['deny'];
    $res['permit'] = $p_tab['permit'];

    $tmp = explode(',', $p_tab['read']);
    foreach($tmp as $item) {
        $res['r'.$item] = true;
    }

    $tmp = explode(',', $p_tab['write']);
    foreach($tmp as $item) {
        $res['w'.$item] = true;
    }

    return $res;
}

// Delete a manager
function manager_del($p_name) {
    $results = sql("DELETE FROM manager WHERE name = \"$p_name\"","query");
}

function manager_format_in($p_tab) {
    if (isset($p_tab['rsystem']))
        $res['read'] .= "system,";
    if (isset($p_tab['rcall']))
        $res['read'] .= "call,";
    if (isset($p_tab['rlog']))
        $res['read'] .= "log,";
    if (isset($p_tab['rverbose']))
        $res['read'] .= "verbose,";
    if (isset($p_tab['rcommand']))
        $res['read'] .= "command,";
    if (isset($p_tab['ragent']))
        $res['read'] .= "agent,";
    if (isset($p_tab['ruser']))
        $res['read'] .= "user";

    if (isset($p_tab['wsystem']))
        $res['write'] .= "system,";
    if (isset($p_tab['wcall']))
        $res['write'] .= "call,";
    if (isset($p_tab['wlog']))
        $res['write'] .= "log,";
    if (isset($p_tab['wverbose']))
        $res['write'] .= "verbose,";
    if (isset($p_tab['wcommand']))
        $res['write'] .= "command,";
    if (isset($p_tab['wagent']))
        $res['write'] .= "agent,";
    if (isset($p_tab['wuser']))
        $res['write'] .= "user";

    return $res;
}

// Add a manager
function manager_add($p_name, $p_secret, $p_deny, $p_permit, $p_read, $p_write) {
    $managers = manager_list();
    if (is_array($managers)) {
        foreach ($managers as $manager) {
            if ($manager['name'] === $p_name) {
                echo "<script>javascript:alert('"._("This manager already exists")."');</script>";
                return false;
            }
        }
    }
    $results = sql("INSERT INTO manager set name='$p_name' , secret='$p_secret' , deny='$p_deny' , permit='$p_permit' , `read`='$p_read' , `write`='$p_write'");
}

function phpagiconf_gen_conf() {
    global $active_modules;

    $file = "/tmp/phpagi_".rand().".conf";
    $data = phpagiconf_get();
    $content = "[phpagi]\n";
    $content .= "debug=".($data['debug']?'true':'false')."\n";
    $content .= "error_handler=".($data['error_handler']?'true':'false')."\n";
    $content .= "admin=".$data['err_email']."\n";
    $content .= "hostname=".$data['hostname']."\n";
    $content .= "tempdir=".$data['tempdir']."\n\n";
    $content .= "[asmanager]\n";
    $content .= "server=".$data['asman_server']."\n";
    $content .= "port=".$data['asman_port']."\n";
    $content .= "username=".$data['asman_user']."\n";
    $content .= "secret=".$data['asman_secret']."\n\n";
    $content .= "[fastagi]\n";
    $content .= "setuid=".($data['setuid']?'true':'false')."\n";
    $content .= "basedir=".$data['basedir']."\n\n";
    $content .= "[festival]\n";
    $content .= "text2wave=".$data['festival_text2wave']."\n\n";
    $content .= "[cepstral]\n";
    $content .= "swift=".$data['cepstral_swift']."\n";
    $content .= "voice=".$data['cepstral_voice']."\n";

    $fd = fopen($file, "w");
    fwrite($fd, $content);
    fclose($fd);
    if (!rename($file, "/etc/asterisk/phpagi.conf")) {
        echo "<script>javascript:alert('"._("Error writing the phpagi.conf file.")."');</script>";
    }
}

function phpagiconf_get() {
    global $db;
    $sql = "SELECT * FROM phpagiconf";
    $res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
    return $res;
}

function phpagiconf_update($p_id, $p_debug, $p_error_handler, $p_err_email, $p_hostname, $p_tempdir, $p_festival_text2wave, $p_asman_server, $p_asman_port, $p_asmanager, $p_cepstral_swift, $p_cepstral_voice, $p_setuid, $p_basedir) {
    $asmanager = split('/', $p_asmanager);
    $results = sql("UPDATE phpagiconf SET `debug`=$p_debug, error_handler=$p_error_handler, err_email='$p_err_email', hostname='$p_hostname', tempdir='$p_tempdir', festival_text2wave='$p_festival_text2wave', asman_server='$p_asman_server', asman_port=$p_asman_port, asman_user='".$asmanager[0]."', asman_secret='".$asmanager[1]."', cepstral_swift='$p_cepstral_swift', cepstral_voice='$p_cepstral_voice', setuid=$p_setuid, basedir='$p_basedir' where phpagiid=$p_id");
}

function phpagiconf_add($p_debug, $p_error_handler, $p_err_email, $p_hostname, $p_tempdir, $p_festival_text2wave, $p_asman_server, $p_asman_port, $p_asmanager, $p_cepstral_swift, $p_cepstral_voice, $p_setuid, $p_basedir) {
    $asmanager = split('/', $p_asmanager);
    $results = sql("INSERT INTO phpagiconf SET `debug`=$p_debug, error_handler=$p_error_handler, err_email='$p_err_email', hostname='$p_hostname', tempdir='$p_tempdir', festival_text2wave='$p_festival_text2wave', asman_server='$p_asman_server', asman_port=$p_asman_port, asman_user='".$asmanager[0]."', asman_secret='".$asmanager[1]."', cepstral_swift='$p_cepstral_swift', cepstral_voice='$p_cepstral_voice', setuid=$p_setuid, basedir='$p_basedir'");
}

function customerdb_list(){
    $sql = "SELECT id, name FROM customerdb";
    $results= sql($sql, "getAll");

    foreach($results as $result){
        $customers[] = array($result[0],$result[1]);
    }
    return $customers;
}

function customerdb_get($extdisplay){
    $sql="SELECT * FROM customerdb where id=$extdisplay";
    $results=sql($sql, "getRow", DB_FETCHMODE_ASSOC);
    return $results;
}

function customerdb_add($name, $addr1, $addr2, $city, $state, $zip, $sip, $did, $device, $ip, $serial, $account, $email, $username, $password){
    $sql="INSERT INTO customerdb (name, addr1, addr2, city, state, zip, sip, did, device, ip, serial, account, email, username, password) values ('$name', '$addr1', '$addr2', '$city', '$state', '$zip', '$sip', '$did', '$device', '$ip', '$serial', '$account', '$email', '$username', '$password')";
    sql($sql);
}

function customerdb_del($extdisplay){
    $sql="DELETE FROM customerdb where id=$extdisplay";
    sql($sql);
}

function customerdb_edit($extdisplay, $name, $addr1, $addr2, $city, $state, $zip, $sip, $did, $device, $ip, $serial, $account, $email, $username, $password){
    $sql="UPDATE customerdb set name='$name' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set addr1='$addr1' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set addr2='$addr2' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set city='$city' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set state='$state' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set zip='$zip' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set sip='$sip' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set did='$did' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set device='$device' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set serial='$serial' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set ip='$ip' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set account='$account' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set email='$email' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set username='$username' where id='$extdisplay'";
    sql($sql);
    $sql="UPDATE customerdb set password='$password' where id='$extdisplay'";
    sql($sql);
}

function customerdb_getsip(){
    $sql="SELECT id,data FROM sip WHERE keyword = 'callerid' ORDER BY id";
    $results=sql($sql, "getAll");
    return $results;
}

function customerdb_getdid(){
    $sql="SELECT extension FROM incoming WHERE extension != '' ORDER BY extension";
    $results=sql($sql, "getAll");
    return $results;
}

// draw list for users and devices with paging
function drawListMenu($results, $skip, $dispnum, $extdisplay, $description, $mode) {
    $perpage=20;

    $skipped = 0;
    $index = 0;
    if ($skip == "") $skip = 0;
     echo "<li><a id=\"".($extdisplay=='' ? 'current':'')."\" href=\"config.php?mode=".$mode."&display=".$dispnum."\" onFocus=\"this.blur()\">"._("Add")." ".$description."</a></li>";

    if (isset($results)) {

            foreach ($results AS $key=>$result) {
                if ($index >= $perpage) {
                    $shownext= 1;
                    break;
                    }
                if ($skipped<$skip && $skip!= 0) {
                    $skipped= $skipped + 1;
                    continue;
                    }
                $index= $index + 1;

                echo "<li><a id=\"".($extdisplay==$result[0] ? 'current':'')."\" href=\"config.php?mode=".$mode."&display=".$dispnum."&extdisplay={$result[0]}&skip={$skip}\" onFocus=\"this.blur()\">{$result[1]} <{$result[0]}></a></li>";

     }
    }

     if ($index >= $perpage) {

     print "<li><center>";

     }

     if ($skip) {

         $prevskip= $skip - $perpage;
         if ($prevskip<0) $prevskip= 0;
         $prevtag_pre= "<a onFocus='this.blur()' href='?mode=".$mode."&display=".$dispnum."&skip=$prevskip'>[PREVIOUS]</a>";
         print "$prevtag_pre";
         }
         if (isset($shownext)) {

             $nextskip= $skip + $index;
             if ($prevtag_pre) $prevtag .= " | ";
             print "$prevtag <a onFocus='this.blur()' href='?mode=".$mode."&display=".$dispnum."&skip=$nextskip'>[NEXT]</a>";
             }
         elseif ($skip) {
             print "$prevtag";
      }

     print "</center></li>";

}

// this function simply makes a connection to the asterisk manager, and should be called by modules that require it (ie: dbput/dbget)
function checkAstMan() {
    require_once('common/php-asmanager.php');
    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $hosts =split(',',$amp_conf['MANAGERHOSTS']);
    foreach ($hosts as $host) {
    $astman = new AGI_AsteriskManager();
        if ($res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {

        } else {
            echo "<h3>Cannot connect to Asterisk Manager $host with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]."</h3>This module requires access to the Asterisk Manager.  Please ensure Asterisk is running and access to the manager is available.</div>";
            exit;
        }
    }
    return $astman->disconnect();
}

function checkVoiceMailManager($extdisplay) {
    require_once('common/php-asmanager.php');
    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $hosts =split(',',$amp_conf['MANAGERHOSTS']);
    foreach ($hosts as $host) {
        $astman = new AGI_AsteriskManager();
        if ($res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {

                $existingvm = $astman->database_get("SEG",$extdisplay."@default");

                 if ($existingvm == "NO") {
                        $vmphone="disabledbyphone";
                }
                $astman->disconnect();

        } else {
            echo "<h3>Cannot connect to Asterisk Manager $host with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]."</h3>This module requires access to the Asterisk Manager.  Please ensure Asterisk is running and access to the manager is available.</div>";
            exit;
        }
    }
    return $vmphone;
}

function checkRecordingINManager($extdisplay) {
    require_once('common/php-asmanager.php');
    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $hosts =split(',',$amp_conf['MANAGERHOSTS']);
    foreach ($hosts as $host) {
        $astman = new AGI_AsteriskManager();
        if ($res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {

                $existingrecording = $astman->database_get("RECORD-IN",$extdisplay);
                if ($existingrecording == "ENABLED") {
                        $record_in="Always";
                } else {
                        $record_in="Never";
                }

                $astman->disconnect();

        } else {
            echo "<h3>Cannot connect to Asterisk Manager $host with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]."</h3>This module requires access to the Asterisk Manager.  Please ensure Asterisk is running and access to the manager is available.</div>";
            exit;
        }
    }
    return $record_in;
}


function checkRecordingOUTManager($extdisplay) {
    require_once('common/php-asmanager.php');
    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $hosts =split(',',$amp_conf['MANAGERHOSTS']);
    foreach ($hosts as $host) {
        $astman = new AGI_AsteriskManager();
        if ($res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {

                $existingrecording = $astman->database_get("RECORD-OUT",$extdisplay);
                if ($existingrecording == "ENABLED") {
                        $record_out="Always";
                } else {
                        $record_out="Never";
                }

                $astman->disconnect();

        } else {
            echo "<h3>Cannot connect to Asterisk Manager $host with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]."</h3>This module requires access to the Asterisk Manager.  Please ensure Asterisk is running and access to the manager is available.</div>";
            exit;
        }
    }
    return $record_out;
}

// sql text formatting -- couldn't see that one was available already
function sql_formattext($txt) {
    if (isset($txt)) {
        $fmt = str_replace("'", "''", $txt);
        $fmt = "'" . $fmt . "'";
    } else {
        $fmt = 'null';
    }

    return $fmt;
}

function miscdests_list($context) {
    $results = sql("SELECT extension,descr FROM extensions WHERE context = '$context' ORDER BY extension","getAll",DB_FETCHMODE_ASSOC);
    foreach($results as $result){
        $extens[] = array($result['extension'],$result['descr']);
    }

    if (isset($extens)) {
        return $extens;
    } else {
        return null;
    }
}

function miscdests_get($id,$context){
    $results = sql("SELECT extension,descr FROM extensions WHERE extension = $id AND context = '$context'","getRow",DB_FETCHMODE_ASSOC);
    return $results;
}

function miscdests_del($id,$context){
    $results = sql("DELETE FROM extensions WHERE extension = $id AND context = '$context'","query");
}


function miscd_list() {
    $sql = "SELECT extension FROM extensions WHERE context = 'ext-miscdests'";
    $results = sql($sql,"getAll");

    foreach($results as $result){
        if (checkRange($result[0])){
            $extens[] = array($result[0]);
        }
    }
    if (isset($extens)) {
        sort($extens);
        return $extens;
    } else {
        return null;
    }
}

function miscdests_add($destdial,$context,$goto,$description) {
    global $db;

    $devices = miscd_list();
    if (is_array($devices)) {
        foreach($devices as $device) {
            if ($device[0] === $destdial) {
                    echo "<script>javascript:alert('"._("This Misc Destination [").$device[0].("] is already in use")."');</script>";
                return false;
            }
        }
    }

    setGotoMiscDest($destdial,$context,'1',$goto,0,$description);

    return true;
}


function ctiuser_list() {
    global $db2;
    $sql = "SELECT user_id,username,login,permission FROM cti_users ORDER BY username";
    $res = $db2->getAll($sql, DB_FETCHMODE_ASSOC);
    if(DB::IsError($res)) {
        return null;
    }
    return $res;
}

function ctiuser_del($user_id) {
    global $db2;
    $sql = "DELETE FROM cti_users WHERE user_id = '$user_id'";
    $res = $db2->query($sql);
    if(DB::IsError($res)) {
        die($res->getMessage().$sql);
    }
    return $res;
}

function ctiuser_add($username, $password, $trunk, $login, $permission) {
    global $db2;
    $managers = ctiuser_list();
    if (is_array($managers)) {
        foreach ($managers as $manager) {
            if ($manager['login'] === $login) {
                echo "<script>javascript:alert('"._("This Cti User is already exists")."');</script>";
                return false;
            }
        }
    }

    $sql = "INSERT INTO cti_users (username, password, trunk, login, permission) VALUES ('".$username."', '".($password)."', '".$trunk."', '".$login."', '".$permission."')";
    $res = $db2->query($sql);
    if(DB::IsError($res)) {
        die($res->getMessage().$sql);
    }
    return $res;
}

function ctiuser_get($user_id) {
    global $db2;
    $sql = "SELECT username,`password`,trunk,login,permission FROM cti_users WHERE user_id = '$user_id'";
    $res = $db2->getRow($sql, DB_FETCHMODE_ASSOC);
    if(DB::IsError($res)) {
        return null;
    }
    return $res;
}

function ctiuser_format_out($p_tab) {
    $res['username'] = $p_tab['username'];
    $res['password'] = $p_tab['password'];
    $res['trunk'] = $p_tab['trunk'];
    $res['login'] = $p_tab['login'];
    $res['permission'] = $p_tab['permission'];
    return $res;
}

function ReadForceIncomingHours($incoming) {
    require_once('common/php-asmanager.php');
    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $hosts =split(',',$amp_conf['MANAGERHOSTS']);
    foreach ($hosts as $host) {
        $astman = new AGI_AsteriskManager();
        if ($res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {

                $existincoming = $astman->database_get("IN_OVERRIDE_".$incoming,"INCOMING");
                if ($existincoming) {
                        $incoming=$existincoming;
                } else {
                        $incoming="none";
                }

                $astman->disconnect();

        } else {
            echo "<h3>Cannot connect to Asterisk Manager $host with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]."</h3>This module requires access to the Asterisk Manager.  Please ensure Asterisk is running and access to the manager is available.</div>";
            exit;
        }
    }
    return $incoming;
}

function WriteForceIncomingHours($incoming,$incomingvalue) {
    require_once('common/php-asmanager.php');
    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $hosts =split(',',$amp_conf['MANAGERHOSTS']);
    foreach ($hosts as $host) {
        $astman = new AGI_AsteriskManager();
        if ($res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {

                if ($incomingvalue == "none") {
                    $astman->database_del("IN_OVERRIDE_".$incoming,"INCOMING");                
                }            
                if ($incomingvalue == "forceafthours") {
                    $astman->database_put("IN_OVERRIDE_".$incoming,"INCOMING",$incomingvalue);                
                } 

                $astman->disconnect();

        } else {
            echo "<h3>Cannot connect to Asterisk Manager $host with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]."</h3>This module requires access to the Asterisk Manager.  Please ensure Asterisk is running and access to the manager is available.</div>";
            exit;
        }
    }
}

function setcwstatus($account,$incomingvalue,$mode) {
    require_once('common/php-asmanager.php');
    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $hosts =split(',',$amp_conf['MANAGERHOSTS']);
    foreach ($hosts as $host) {
        $astman = new AGI_AsteriskManager();
        if ($res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {

            if ($mode == "write") {

                if ($incomingvalue == "Never") {
                    $astman->database_del("CW",$account);
                }            
                if ($incomingvalue == "Always") {
                    $astman->database_put("CW",$account,"ENABLED");
                } 
             $cwstatus = null;
             
             }
             
             if ($mode == "read") {
                        
                $existcwstatus = $astman->database_get("CW",$account);
                if ($existcwstatus) {
                    $cwstatus="Always";
                } else {
                    $cwstatus="Never";
                }
             }

             $astman->disconnect();

        } else {
            echo "<h3>Cannot connect to Asterisk Manager $host with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]."</h3>This module requires access to the Asterisk Manager.  Please ensure Asterisk is running and access to the manager is available.</div>";
            exit;
        }
    }
    return $cwstatus;
}

function setrobstatus($account,$incomingvalue,$mode) {
    require_once('common/php-asmanager.php');
    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $hosts =split(',',$amp_conf['MANAGERHOSTS']);
    foreach ($hosts as $host) {
        $astman = new AGI_AsteriskManager();
        if ($res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {

            if ($mode == "write") {

                if ($incomingvalue == "Never") {
                    $astman->database_del("ROBCHECK",$account);                
                }            
                if ($incomingvalue == "Always") {
                    $astman->database_put("ROBCHECK",$account,"ENABLED");                
                }

             $robstatus = null;
             
             }
             
             if ($mode == "read") {
                        
                $existrobstatus = $astman->database_get("ROBCHECK",$account);
                if ($existrobstatus) {
                    $robstatus="Always";
                } else {
                    $robstatus="Never";
                }
             }
             
                $astman->disconnect();

        } else {
            echo "<h3>Cannot connect to Asterisk Manager $host with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]."</h3>This module requires access to the Asterisk Manager.  Please ensure Asterisk is running and access to the manager is available.</div>";
            exit;
        }
    }
    return $robstatus;
}

?>