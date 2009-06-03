<?php 

// Davide.Gustin by SpheraIT

if (extension_loaded('gettext')) {
	if (isset($_COOKIE['lang'])) {
		setlocale(LC_MESSAGES,  $_COOKIE['lang']);
//    setcookie("lang",  $_COOKIE['lang'], time()+2592000);
  } else {
			setlocale(LC_MESSAGES,  'en_US');
	}
	bindtextdomain('main','../../i18n');
  textdomain('main');
}

function file_parser_serial_pn($filename) {
        $file = file($filename);
        $conf = trim($file[0], "\n");
        if ($conf == "0103") {
            $conf = true;
        } else {
                $conf = false;
        }
        return $conf;
}

function file_parser($filename) {
    $file = file($filename);
    if (is_array($file)) {
        foreach ($file as $line) {
            if (preg_match("/^\s*([a-zA-Z0-9]+)=([a-zA-Z0-9 .&-@=_<>\"\']+)\s*$/",$line,$matches)) {
                $conf[ $matches[1] ] = $matches[2];
            }
        }
    } else {
        die("Missing ($filename)...cannot continue");
    }

    return $conf;
}

$action = $_REQUEST['action'];
    
if ($action == 'save') {
	
    $sysnetwork = file_parser("/etc/sysconfig/network");
	$hostname=$_REQUEST['hostname'];
	$ip = $_REQUEST['ip'];
	$netmask = $_REQUEST['netmask'];
	$gateway = $_REQUEST['gateway'];
	isset($_REQUEST['haip'])?$haip=$_REQUEST['haip']:$haip="off";
	$gatewayswitch = $_REQUEST['gatewayswitch'];


	exec("sudo /var/www/html/private/networkconfig/networkconfig.sh \"$hostname\" \"$ip\" \"$netmask\" \"$gateway\" \"$haip\" \"$gatewayswitch\"");

	unset($hostname);
	unset($ip);
	unset($netmask);
	unset($gateway);
	unset($haip);
	unset($gatewayswitch);
	
}
    $clustercheck = file_parser_serial_pn("/etc/voiper_pn");
    $ifcfg = file_parser("/etc/sysconfig/network-scripts/ifcfg-eth0");
    $sysnetwork = file_parser("/etc/sysconfig/network");

    if ($clustercheck) {
        $ifcfgha = file_parser("/etc/sysconfig/network-scripts/ifcfg-eth0:0");
    	$haip = $ifcfgha["IPADDR"];
        $hostname = $sysnetwork["HOSTNAME"];
    } else {
            $hostname = $sysnetwork["HOSTNAME"];
    }

	$netmask = $ifcfg["NETMASK"];
	$gateway = $sysnetwork["GATEWAY"];
	$ip = $ifcfg["IPADDR"];
    $gatewayswitch = "0";

    if ($gateway == "") {
        $gateway = $ifcfg["GATEWAY"];
        $gatewayswitch = "1";
    }

?>

<HTML><HEAD>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<link href="mainstyle.css" rel="stylesheet" type="text/css">
<title>Voiper IP WebConfig</title>

<SCRIPT language="javascript" type="text/javascript">
<!--

var errfound = false;

function error(elem, text) {
	if (errfound) return;
	window.alert(text);
	elem.select();
	elem.focus();
	errfound = true;
}

function isValidIPAddress(form,ipaddr) {
   var re = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
   if (re.test(ipaddr)) {
      var parts = ipaddr.split(".");
      if (parseInt(parseFloat(parts[0])) == 0) { error(form,"<?php echo _("The IP Address is not corrected.\\nPlease write a correct ip Address. (format: nnn.nnn.nnn.nnn where nnn is a number from 0 to 255)")?>"); }
      for (var i=0; i<parts.length; i++) {
         if (parseInt(parseFloat(parts[i])) > 255) { error(form,"<?php echo _("The IP Address is not corrected.\\nPlease write a correct ip Address. (format: nnn.nnn.nnn.nnn where nnn is a number from 0 to 255)")?>"); }
      }
   } else {
      error(form,"<?php echo _("The IP Address is not corrected.\\nPlease write a correct ip Address. (format: nnn.nnn.nnn.nnn where nnn is a number from 0 to 255)")?>");
   }
}

function network_start(f2) {

	cancel = false;
	ok = true;

	if (confirm("<?php echo _("Are you sure to save the new Network Configuration?\\nTo apply the changes you must reboot the pbx.")?>"))
  		return ! cancel;
	else
  		return ! ok;
}
  		
function NetCheck(form) {

	errfound = false;

    isValidIPAddress(form.ip,form.ip.value);
    isValidIPAddress(form.netmask,form.netmask.value);
    isValidIPAddress(form.gateway,form.gateway.value);

<? if ($clustercheck) { ?>

    isValidIPAddress(form.haip,form.haip.value);

<? } else { ?>

	if (form.hostname.value == "") {
	   error(form.hostname,"<?php echo _("Perfavore digita l'hostname.")?>");
       form.hostname.focus();
    }

<? } ?>

  	return ! errfound;
}
// -->
</SCRIPT>

</HEAD>

<BODY text="#000000" vLink="#000000" aLink="#000000" link="#000000" bgColor="#eeeeee" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<form name="network" action="networkconfig.php" method="post" onSubmit="return NetCheck(this);">
<input type="hidden" name="action" value="save"/>
<input type="hidden" name="gatewayswitch" value="<?php echo $gatewayswitch?>"/>

<? if ($clustercheck) { ?>

    <input type="hidden" name="hostname" value="<?php echo $hostname?>"/>

<? } ?>

<table width="100%" border="0" cellspacing="3" cellpadding="0">
    <tr>
    <td colspan="2">&nbsp;</td>
    </tr>

	<tr>
    <td width="55%"><b><?php echo _("Hostname")?>:</b></td>
    <td width="45%"><input name="hostname" type="text" id="hostname" value="<?php echo $hostname?>" size="35" maxlength="50"
    
<? if ($clustercheck) { ?>

    disabled

<? } ?>

    ></td>
	</tr>

	<tr>
    <td width="55%"><b><?php echo _("IP Address (eth0)")?>:</b></td>
    <td width="45%"><input name="ip" type="text" id="ip" value="<?php echo $ip?>" size="35" maxlength="50"></td>
	</tr>

<? if ($clustercheck) { ?>

	<tr>
    <td width="55%"><b><?php echo _("Heartbeat (eth0:0)")?>:</b></td>
    <td width="45%"><input name="haip" type="text" id="haip" value="<?php echo $haip?>" size="35" maxlength="50"></td>
	</tr>

<? } ?>

	<tr>
    <td width="55%"><b><?php echo _("Netmask (eth0)")?>:</b></td>
    <td width="45%"><input name="netmask" type="text" id="netmask" value="<?php echo $netmask?>" size="35" maxlength="50"></td>
	</tr>

	<tr>
    <td width="55%"><b><?php echo _("Gateway")?>:</b></td>
    <td width="45%"><input name="gateway" type="text" id="gateway" value="<?php echo $gateway?>" size="35" maxlength="50"></td>
	</tr>

	<tr>
	<td colspan="2"><div align="right"><input name="Submit" type="submit" value="Salva" onClick="return network_start(this);"></div></td>
	<tr>
	<td colspan="2">* <?php echo _("For advance-configuration please use Webmin.")?></td>
	</tr>
	</tr>

  </table>
</form>
</BODY></HTML>
