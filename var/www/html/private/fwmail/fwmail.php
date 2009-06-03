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

$wScript = 'sudo /var/www/html/private/fwmail/retrieve_fwmail.pl';

require_once('db_connect.php');

$action = $_REQUEST['action'];

if ($action == 'savemail') {
	$globalfields = array(array($_REQUEST['MailTo'],'MailTo'),);

	$compiled = $db->prepare('UPDATE fwmail SET value = ? WHERE variable = ?');
	$result = $db->executeMultiple($compiled,$globalfields);
	if(DB::IsError($result)) {
		echo $action.'<br>';
		die($result->getMessage());
	}
	
	exec($wScript);

	}
	
$sql = "SELECT * FROM fwmail";
$globals = $db->getAll($sql);
if(DB::IsError($globals)) {
die($globals->getMessage());
}

foreach ($globals as $global) {
	${trim($global[0])} = $global[1];	
}

?>

<HTML><HEAD>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<link href="mainstyle.css" rel="stylesheet" type="text/css">
<title>Root email WebEditor</title>
</HEAD>

<BODY text="#000000" vLink="#000000" aLink="#000000" link="#000000" bgColor="#eeeeee" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<form name="general" action="fwmail.php" method="post">
<input type="hidden" name="action" value="savemail"/>

<table width="100%" border="0" cellspacing="3" cellpadding="0">
   <tr>
    <td colspan="3"><?php echo _("Most email that the system sends are redirected to the root mailbox.")?><br><br><?php echo _("If you have several Server to administer is not good to read the mail of root on each server. One thing is forward all root mail to a mail account that you regularly check with your favorite client.")?></td>
  </tr>
   <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%"><b>Email:</b></td>
    <td width="75%"><input name="MailTo" type="text" id="MailTo" value="<?php  echo $MailTo?>" size="30" maxlength="50"></td>
    <td><div align="right"><input name="Submit" type="submit" value="<?php echo _("Salva")?>"></div></td>
	</tr>

  </table>
</form>
</BODY></HTML>