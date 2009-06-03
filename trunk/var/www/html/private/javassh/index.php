<?php 

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

?>

<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Java SSH</title>
</head>
<body>

<p>
<div align="left"><img src="logo.gif" border="0"></div>
<b><FONT face="Verdana, Arial, Helvetica, sans-serif" size=4>MindTerm Java SSH</font></b></p>

<hr>
<ul>
	<li><FONT face="Verdana, Arial, Helvetica, sans-serif" size=1><?php echo _("On the Java shell please enter the ip of your PBX followed by: 222 (ES: 192.168.0.200:222)")?></font></li>
	<li><FONT face="Verdana, Arial, Helvetica, sans-serif" size=1><?php echo _("Please digit 'root' for the login and password described in the Voiper Manual.")?></font></li>
	<li><FONT face="Verdana, Arial, Helvetica, sans-serif" size=1><?php echo _("MindTerm is compatible with")?>:</font></li>
	<FONT face="Verdana, Arial, Helvetica, sans-serif" size=1><br>
Windows 95, 98, ME, NT, 2000, XP<br> 
Apple MacOS 7 o superiori.<br>
Linux.<br>
Solaris SPARC e x86.<br>
HP-UX.<br>
Nokia Communicator, PSION Netpad e Netbook.<br></font></ul>

<APPLET CODE="com.mindbright.application.MindTerm.class"
    ARCHIVE="mindterm.jar" WIDTH=0 HEIGHT=0>
    <PARAM NAME="cabinets" VALUE="mindterm.cab">
    <PARAM NAME="sepframe" value="true">
    <PARAM NAME="debug" value="false">
    <PARAM NAME="cursor-color" value="i_blue">
    <PARAM NAME="bg-color" value="black">
    <PARAM NAME="fg-color" value="i_white">
    <PARAM NAME="term-type" value="linux">
    <PARAM NAME="geometry" value="124x35">
    <PARAM NAME="save-lines" value="5000">
	</APPLET>
</body>
</html>
