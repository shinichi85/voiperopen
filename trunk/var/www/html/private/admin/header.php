<?php 

    $currentFile = $_SERVER["PHP_SELF"];
    $parts = Explode('/', $currentFile);
    $currentFile = $parts[count($parts) - 1];

    if (!extension_loaded('gettext')) {
           function _($str) {
                   return $str;
           }
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
    <title>Voiper Management Portal</title>
    <meta http-equiv="Content-Type" content="text/html">
    <link href="common/mainstyle.css" rel="stylesheet" type="text/css"> 
    <script src="common/script.js.php" type="text/javascript"></script>
    <script type="text/javascript">history.forward();</script> 
</head>

<?php
	if (extension_loaded('gettext')) {
	if (isset($_COOKIE['lang'])) {
		setlocale(LC_MESSAGES,  $_COOKIE['lang']);
	} else {
		setlocale(LC_MESSAGES,  'en_US');
	}
	bindtextdomain('amp','./i18n');
	textdomain('amp');
	}
?>

<body>
<div id="page">

<div class="header">
<table width="100%" border="0" cellpadding="0" cellspacing="1">
  <tr>
    <td align="left"><a href="/private/index.php" onFocus="this.blur()"><img src="images/<?php echo $amp_conf["AMPADMINLOGO"] ?>"/></a></td>
    <td align="right" valign="top"><a href="config.php?display=3&mode=pbx" onFocus="this.blur()"><?php if ($mode == "pbx") {?><font class="tabcolor"><?php } ?> &#8226; VMP Management&nbsp;</font></a>
    <li><a href="config.php?display=1&mode=settings" onFocus="this.blur()"><?php if ($mode == "settings") {?><font class="tabcolor"><?php } ?> &#8226; VMP Settings&nbsp;</font></a></li>
    <li><a href="config.php?display=1&mode=tools" onFocus="this.blur()"><?php if ($mode == "tools") {?><font class="tabcolor"><?php } ?> &#8226; VMP Tools&nbsp;</font></a></li>
    <li><a href="config.php?display=1&mode=file" onFocus="this.blur()"><?php if ($mode == "file") {?><font class="tabcolor"><?php } ?> &#8226; VMP Recording/Fax&nbsp;</font></a></li>
    <li><a href="/private/index.php" onFocus="this.blur()"> &#8226; Home</a></li></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right" class="hostname"><b class="hostname">Pbx Hostname:</b> <? echo passthru('hostname'); ?></td>
  </tr>
</table>
</div>
<br>
<div class="message"></div>