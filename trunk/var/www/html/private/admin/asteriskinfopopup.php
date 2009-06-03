<?php

//Copyright (C) 2006 Astrogen LLC
//Copyright (C) 2007-2008 Gustin Davide - SpheraIT

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
    <title>AsteriskInfo Popup</title>
    <meta http-equiv="Content-Type" content="text/html">
    <link href="common/mainstyle.css" rel="stylesheet" type="text/css"> 
</head>
<body>

<?php 

require_once('common/php-asmanager.php');
require_once('functions.php');

$extdisplay = isset($_REQUEST['extdisplay'])?$_REQUEST['extdisplay']:'summary';

$arr_all = array(
    "Uptime" => "show uptime",
    "Active Channel(s)" => "core show channels",
    "Sip Channel(s)" => "sip show channels",
    "IAX2 Channel(s)" => "iax2 show channels",
    "Sip Registry" => "sip show registry",
    "Sip Peers" => "sip show peers",
    "IAX2 Registry" => "iax2 show registry",
    "IAX2 Peers" => "iax2 show peers",
    "Subscribe/Notify" => "core show hints",
    "Zaptel driver info" => "zap show channels",
    "Zaptel driver status" => "zap show status", 
    "Conference Info" => "meetme",
    "Queues Info" => "queue show",    
    "Voicemail users" => "voicemail show users",
);
$arr_registries = array(
    "Sip Registry" => "sip show registry",
    "IAX2 Registry" => "iax2 show registry",
);
$arr_channels = array(
    "Active Channel(s)" => "core show channels",
    "Sip Channel(s)" => "sip show channels",
    "IAX2 Channel(s)" => "iax2 show channels",
);
$arr_peers = array(
    "Sip Peers" => "sip show peers",
    "IAX2 Peers" => "iax2 show peers",
);
$arr_sip = array(
    "Sip Registry" => "sip show registry",
    "Sip Peers" => "sip show peers",
);
$arr_iax = array(
    "IAX2 Registry" => "iax2 show registry",
    "IAX2 Peers" => "iax2 show peers",
);
$arr_conferences = array(
    "Conference Info" => "meetme",
);
$arr_subscriptions = array(
    "Subscribe/Notify" => "core show hints"
);
$arr_voicemail = array(
    "Voicemail users" => "voicemail show users",
);
$arr_queues = array(
    "Queues Info" => "queue show",
);
$arr_database = array(
    "Database Info" => "database show",
);

$amp_conf = parse_amportal_conf("/etc/amportal.conf");
$host = $amp_conf['MANAGERHOSTS'];
$astman = new AGI_AsteriskManager();
$res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"]);
        
if ($res) {
//get version (1.4)
$response = $astman->send_request('Command', array('Command'=>'core show version'));
if (preg_match('/No such command/',$response['data'])) {
// get version (1.2)
$response = $astman->send_request('Command', array('Command'=>'show version'));
}
$verinfo = $response['data'];
} else {
// could not connect to asterisk manager, try console
$verinfo = exec('asterisk -V');
}

preg_match('/Asterisk (\d+(\.\d+)*)(-?(\S*))/', $verinfo, $matches);
$verinfo = $matches[1];

//    $arr_all["Uptime"]="core show uptime";
//    $arr_all["Active Channel(s)"]="core show channels";
//    $arr_all["Subscribe/Notify"]="core show hints";
//    $arr_all["Voicemail users"]="voicemail show users";
//    $arr_channels["Active Channel(s)"]="core show channels";
//    $arr_subscriptions["Subscribe/Notify"]="core show hints";
//    $arr_voicemail["Voicemail users"]="voicemail show users";

?>

<form name="asteriskinfo" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="display" value="4"/>
<input type="hidden" name="action" value="asteriskinfo"/>
<table>

<table bgcolor="#444444" width="100%" border="0" align="center" cellpadding="0" cellspacing="2">
<?php
if (!$res) {
?>
    <tr class="boxheader">
        <td colspan="2" align="center"><h3><?php echo _("ASTERISK MANAGER ERROR")?><hr></h3></td>
    </tr>
        <tr class="boxbody">
            <td>
            <table border="0" >
                <tr>
                    <td align="left">
                            <?php
                            echo "<br>The module was unable to connect to the asterisk manager.<br>Make sure Asterisk is running and your manager.conf settings are proper.<br><br>";
                            ?>
                    </td>
                </tr>
            </table>
            </td>
        </tr>
<?php
} else {
        $arr="arr_".$extdisplay;
        foreach ($$arr as $key => $value) {
?>
            <tr class="boxheader">
                <td colspan="2" align="center"><h3><?php echo _("$key")?><hr></h3></td>
            </tr>
            <tr class="boxbody">
                <td>
                <table border="0" >
                    <tr>
                        <td>
                            <pre>
                                <?php
                                $response = $astman->send_request('Command',array('Command'=>$value));
                                $new_value = $response['data'];
                                echo ltrim($new_value,'Privilege: Command');
                                ?>
                            </pre>
                        </td>
                    </tr>
                </table>
                </td>
            </tr>
<?php
}
}
?>
    </table>
<tr>
    <td colspan="2"><h6><input name="Submit" type="submit" value="<?php echo _("Refresh")?>"></h6></td>
</tr>
</table>

<script language="javascript">
<!--
var theForm = document.asteriskinfo;
//-->
</script>
</form>

<?php

function convertActiveChannel($sipChannel, $channel = NULL){
    if($channel == NULL){
        print_r($sipChannel);
        exit();
        $sipChannel_arr = explode(' ', $sipChannel[1]);
        if($sipChannel_arr[0] == 0){
            return 0;
        }else{
            return count($sipChannel_arr[0]);
        }
    }elseif($channel == 'IAX2'){
        $iaxChannel = $sipChannel;
    }
}

function getActiveChannel($channel_arr, $channelType = NULL){
    if(count($channel_arr) > 1){
        if($channelType == NULL || $channelType == 'SIP'){
            $sipChannel_arr = $channel_arr;
            $sipChannel_arrCount = count($sipChannel_arr);
            $sipChannel_string = $sipChannel_arr[$sipChannel_arrCount - 2];
            $sipChannel = explode(' ', $sipChannel_string);
            return $sipChannel[0];
        }elseif($channelType == 'IAX2'){
            $iax2Channel_arr = $channel_arr;
            $iax2Channel_arrCount = count($iax2Channel_arr);
            $iax2Channel_string = $iax2Channel_arr[$iax2Channel_arrCount - 2];
            $iax2Channel = explode(' ', $iax2Channel_string);
            return $iax2Channel[0];
        }
    }
}

function getRegistration($registration, $channelType = 'SIP'){
    if($channelType == NULL || $channelType == 'SIP'){
        $sipRegistration_arr = $registration;
        $sipRegistration_count = count($sipRegistration_arr);
        return $sipRegistration_count-3;
        
    }elseif($channelType == 'IAX2'){
        $iax2Registration_arr = $registration;
        $iax2Registration_count = count($iax2Registration_arr);
        return $iax2Registration_count-3;
    }
}

function getPeer($peer, $channelType = NULL){
    global $astver_major, $astver_minor;
    if(count($peer) > 1){   
        if($channelType == NULL || $channelType == 'SIP'){
            $sipPeer = $peer;
            $sipPeer_count = count($sipPeer);
            $sipPeerInfo_arr['sipPeer_count'] = $sipPeer_count -3;
            $sipPeerInfo_string = $sipPeer[$sipPeer_count -2];
            $sipPeerInfo_arr2 = explode('[',$sipPeerInfo_string);
            $sipPeerInfo_arr3 = explode(' ',$sipPeerInfo_arr2[1]);
            if($astver_major == 1 && $astver_minor >= 4){
                $sipPeerInfo_arr['online'] = $sipPeerInfo_arr3[1] + $sipPeerInfo_arr3[6];
                $sipPeerInfo_arr['offline'] = $sipPeerInfo_arr3[3] + $sipPeerInfo_arr3[8];
            }else{
                $sipPeerInfo_arr['online'] = $sipPeerInfo_arr3[0];
                $sipPeerInfo_arr['offline'] = $sipPeerInfo_arr3[3];
            }
            return $sipPeerInfo_arr;
            
        }elseif($channelType == 'IAX2'){
            $iax2Peer = $peer;
            $iax2Peer_count = count($iax2Peer);
            $iax2PeerInfo_arr['iax2Peer_count'] = $iax2Peer_count -3;
            $iax2PeerInfo_string = $iax2Peer[$iax2Peer_count -2];
            $iax2PeerInfo_arr2 = explode('[',$iax2PeerInfo_string);
            $iax2PeerInfo_arr3 = explode(' ',$iax2PeerInfo_arr2[1]);
            $iax2PeerInfo_arr['online'] = $iax2PeerInfo_arr3[0];
            $iax2PeerInfo_arr['offline'] = $iax2PeerInfo_arr3[2];
            $iax2PeerInfo_arr['unmonitored'] = $iax2PeerInfo_arr3[4];
            return $iax2PeerInfo_arr;
        }
    }
}
?>
</body>
</html>