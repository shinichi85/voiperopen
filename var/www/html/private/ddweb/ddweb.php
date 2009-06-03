<?php 

// Coding by XAD of Nightfall
// Copyright by SpheraIT
// GPL Source

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

$wScript = '/var/www/html/private/ddweb/retrieve_ddns.pl';

require_once('db_connect.php');

$action = $_REQUEST['action'];

if ($action == 'saveddns') {
	$globalfields = array(array($_REQUEST['daemon'],'daemon'),
						array($_REQUEST['syslog'],'syslog'),
						array($_REQUEST['protocol'],'protocol'),
						array($_REQUEST['use'],'use'),
						array($_REQUEST['server'],'server'),
						array($_REQUEST['login'],'login'),
						array($_REQUEST['password'],'password'),
						array($_REQUEST['host'],'host'),
						array($_REQUEST['mail'],'mail'),
						array($_REQUEST['mail-failure'],'mail-failure'),
						);

	$compiled = $db->prepare('UPDATE ddns SET value = ? WHERE variable = ?');
	$result = $db->executeMultiple($compiled,$globalfields);
	if(DB::IsError($result)) {
		echo $action.'<br>';
		die($result->getMessage());
	}
	
		exec($wScript);
}
	
$sql = "SELECT * FROM ddns";
$globals = $db->getAll($sql);
if(DB::IsError($globals)) {
die($globals->getMessage());
}


foreach ($globals as $global) {

	$mailfailure = ${trim($global[0])} = $global[1];

	}

?>

<HTML><HEAD>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<link href="mainstyle.css" rel="stylesheet" type="text/css">
<title>DynamicDNS (DDClient) WebEditor</title>
</HEAD>

<BODY text="#000000" vLink="#000000" aLink="#000000" link="#000000" bgColor="#eeeeee" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<form name="general" action="ddweb.php" method="post">
<input type="hidden" name="action" value="saveddns"/>

<table width="100%" border="0" cellspacing="3" cellpadding="0">
  <tr>
    <td width="25%"><b><?php echo _("Update DDns")?>:</b></td>
    <td width="75%"><input name="daemon" type="text" id="daemon" value="<?php  echo $daemon?>" size="5" maxlength="5"></td>
  </tr>
  <tr>
    <td><b><?php echo _("Syslog")?>:</b></td>
    <td><select name="syslog" size="1">
      <option value="yes"><?php echo _("Yes")?></option>
      <option value="no" <? if ($syslog == 'no') { echo "selected"; } ?>><?php echo _("No")?></option>
    </select></td>
  </tr>
  <tr>
    <td><b><?php echo _("Protocol")?>:</b></td>
    <td><input name="protocol" type="text" id="protocol" value="<?php  echo $protocol?>" size="20" maxlength="50"></td>
  </tr>
  <tr>
    <td><b><?php echo _("IP")?>:</b></td>
    <td><input name="use" type="text" id="use" value="<?php  echo $use?>" size="60" maxlength="100"></td>
  </tr>
  <tr>
    <td><b><?php echo _("DDns server")?>:</b></td>
    <td><input name="server" type="text" id="server" value="<?php  echo $server?>" size="20" maxlength="50"></td>
  </tr>
  <tr>
    <td><b><?php echo _("Login")?>:</b></td>
    <td><input name="login" type="text" id="login" value="<?php  echo $login?>" size="20" maxlength="50"></td>
  </tr>
  <tr>
    <td><b><?php echo _("Password")?>:</b></td>
    <td><input name="password" type="password" id="password" value="<?php  echo $password?>" size="20" maxlength="50"></td>
  </tr>
  <tr>
    <td><b><?php echo _("Host DDns")?>:</b></td>
    <td><input name="host" type="text" id="host" value="<?php  echo $host?>" size="30" maxlength="50"></td>
  </tr>
    <tr>
    <td><b><?php echo _("Email if Update")?>:</b></td>
    <td><input name="mail" type="text" id="mail" value="<?php  echo $mail?>" size="30" maxlength="50"></td>
  </tr>
    <tr>
    <td><b><?php echo _("Email if Fail")?>:</b></td>
    <td><input name="mail-failure" type="text" id="mail-failure" value="<?php  echo $mailfailure?>" size="30" maxlength="50"></td>
  </tr>  
   <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><div align="left">* <?php echo _("Read the documentation before configuring this service.")?></div></td>
          <td><div align="right"><input name="Submit" type="submit" value="<?php echo _("Salva")?>"></div></td>
        </tr>
      </table>
    </td>
  </tr>
  </table>
</form>
</BODY></HTML>