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

$wScript = '/var/www/html/private/logwatch/retrieve_logwatch.pl';

require_once('db_connect.php');

$action = $_REQUEST['action'];

if ($action == 'savelogwatch') {
	$globalfields = array(array($_REQUEST['MailTo'],'MailTo'),
						array($_REQUEST['LogDir'],'LogDir'),
						array($_REQUEST['TmpDir'],'TmpDir'),
						array($_REQUEST['Print'],'Print'),
						array($_REQUEST['UseMkTemp'],'UseMkTemp'),
						array($_REQUEST['MkTemp'],'MkTemp'),
						array($_REQUEST['Range'],'Range'),
						array($_REQUEST['Detail'],'Detail'),
						array($_REQUEST['Service'],'Service'),
						array($_REQUEST['mailer'],'mailer'),
						array($_REQUEST['Archives'],'Archives'),
						);

	$compiled = $db->prepare('UPDATE logwatch SET value = ? WHERE variable = ?');
	$result = $db->executeMultiple($compiled,$globalfields);
	if(DB::IsError($result)) {
		echo $action.'<br>';
		die($result->getMessage());
	}
	
		exec($wScript);
}
	
$sql = "SELECT * FROM logwatch";
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
<title>LogWatch Service WebEditor</title>
</HEAD>

<BODY text="#000000" vLink="#000000" aLink="#000000" link="#000000" bgColor="#eeeeee" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<form name="general" action="logwatch.php" method="post">
<input type="hidden" name="action" value="savelogwatch"/>

<table width="100%" border="0" cellspacing="3" cellpadding="0">
  <tr>
    <td width="25%"><b><?php echo _("Email for Log")?>:</b></td>
    <td width="75%"><input name="MailTo" type="text" id="MailTo" value="<?php  echo $MailTo?>" size="30" maxlength="50"></td>
  </tr>
  <tr>
    <td><b><?php echo _("Log Directory")?>:</b></td>
    <td><input name="LogDir" type="text" id="LogDir" value="<?php  echo $LogDir?>" size="20" maxlength="50"></td>
  </tr>
  <tr>
    <td><b><?php echo _("Temp Log")?>:</b></td>
    <td><input name="TmpDir" type="text" id="TmpDir" value="<?php  echo $TmpDir?>" size="20" maxlength="100"></td>
  </tr>
  <tr>
    <td><b><?php echo _("Console Output")?>:</b></td>
    <td><select name="Print" size="1">
      <option value="yes"><?php echo _("Si")?></option>
      <option value="no" <? if ($Print == 'no') { echo "selected"; } ?>><?php echo _("No")?></option>
    </select></td>
  </tr>
  <tr>
    <td><b><?php echo _("MkTemp Support")?>:</b></td>
    <td><select name="UseMkTemp" size="1">
      <option value="yes"><?php echo _("Si")?></option>
      <option value="no" <? if ($UseMkTemp == 'no') { echo "selected"; } ?>><?php echo _("No")?></option>
    </select></td>
  </tr>
  <tr>
    <td><b><?php echo _("Directory MKTemp")?>:</b></td>
    <td><input name="MkTemp" type="text" id="MkTemp" value="<?php  echo $MkTemp?>" size="20" maxlength="50"></td>
  </tr>
  <tr>
    <td><b><?php echo _("Range")?>:</b></td>
    <td><select name="Range">
    <option value="Yesterday" <? if ($Range == 'Yesterday') { echo "selected"; } ?>><?php echo _("Yesterday")?></option>
        <option value="Today" <? if ($Range == 'Today') { echo "selected"; } ?>><?php echo _("Today")?></option>
		<option value="All" <? if ($Range == 'All') { echo "selected"; } ?>><?php echo _("All")?></option>
    </select></td>
  </tr>
  <tr>
    <td><b><?php echo _("Log Detail")?>:</b></td>
    <td><select name="Detail">
    <option value="Low" <? if ($Detail == 'Low') { echo "selected"; } ?>><?php echo _("Low")?></option>
        <option value="Med" <? if ($Detail == 'Med') { echo "selected"; } ?>><?php echo _("Medium")?></option>
        <option value="High" <? if ($Detail == 'High') { echo "selected"; } ?>><?php echo _("High")?></option>
    </select></td>	
 </tr>
    <tr>
    <td><b><?php echo _("Sevices")?>:</b></td>
    <td><input name="Service" type="text" id="Service" value="<?php  echo $Service?>" size="30" maxlength="50"></td>
  </tr>
    <tr>
    <td><b><?php echo _("Sendmail Path")?>:</b></td>
    <td><input name="mailer" type="text" id="mailer" value="<?php  echo $mailer?>" size="20" maxlength="50"></td>
  </tr>
  <tr>
    <td><b><?php echo _("Compress old Log")?>:</b></td>
    <td><select name="Archives" size="1">
      <option value="yes"><?php echo _("Si")?></option>
      <option value="no" <? if ($Archives == 'no') { echo "selected"; } ?>><?php echo _("No")?></option>
    </select></td>
  </tr>
   <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><div align="left">* <?php echo _("Please read the documentation before configuring this service.")?></div></td>
          <td><div align="right"><input name="Submit" type="submit" value="<?php echo _("Salva")?>"></div></td>
        </tr>
      </table>
    </td>
  </tr>
  </table>
</form>
</BODY></HTML>