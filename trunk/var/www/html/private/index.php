<?

switch($_REQUEST['extcmd']) {

    default:
    break;

    case 'restart':
    exec ('scripts/killasterisk.sh');
    break;

    case 'reload':
    exec('perl scripts/asterisk-reload.pl');
    break;

    case 'shutdown_pbx':
    exec ('sudo /sbin/shutdown -h now');
    break;

    case 'reboot_pbx':
    exec ('sudo /sbin/shutdown -r now');
    break;

    case 'ssh_enable':
    exec ('sudo scripts/sshd-root_enabled.sh');
    break;

    case 'ssh_disable':
    exec ('sudo scripts/sshd-root_disabled.sh');
    break;
}

require_once('functions.php');

?>

<HTML>
<HEAD><TITLE>Voiper Administrator</TITLE>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<link href="home.css" rel="stylesheet" type="text/css">
<SCRIPT language="javascript" type="text/javascript">
<!--
function changelayer_color(newcolor,numerotab){
      if(document.layers){
 //thisbrowser="NN4";
document.layers[numerotab].bgColor=newcolor;
}
if(document.all){
            //thisbrowser="ie"
//  document.all.numerotab.style.backgroundColor=newcolor;
   document.getElementById(numerotab).style.backgroundColor=newcolor;
       }
 if(!document.all && document.getElementById){
     //thisbrowser="NN6";
   document.getElementById(numerotab).style.backgroundColor=newcolor;
 }
 }

function shutdown_pbx(f2) {

    cancel = false;
    ok = true;

    if (confirm("<?php echo _("Are you sure to switch off the pbx?")?>"))
        return ! cancel;
    else
        return ! ok;
}

function reboot_pbx(f2) {

    cancel = false;
    ok = true;

    if (confirm("<?php echo _("Are you sure to reboot the pbx?")?>"))
        return ! cancel;
    else
        return ! ok;
}

function ssh_enable(f2) {

    cancel = false;
    ok = true;

    if (confirm("<?php echo _("Are you sure to enable the root user for remote support?\\n\\nBefore enabling this option SSH service must be running.")?>"))
        return ! cancel;
    else
        return ! ok;
}

// -->
</SCRIPT>
</HEAD>

<BODY text="#333333" vLink="#000000" aLink="#000000" link="#000000" bgColor="#ffffff">

<DIV align=center>

  <TABLE cellSpacing=0 cellPadding=1 width=619 border=0>
  <TBODY>
  <TR bgColor=#000000>
    <TD width="100%">
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD width="100%" bgColor=white>
            <TABLE cellSpacing=2 cellPadding=0 width="100%" border=0>
              <TBODY>
              <TR>
                <TD bgColor=#FFFFFF><div align="left"><FONT face="Verdana, Arial, Helvetica, sans-serif" size=1><?php echo _("Welcome")?> <b><? echo getenv('REMOTE_USER'); ?></b> (<?php echo _("Administrator")?>)</font></div></TD>
             </TR>
              <TR>
                <TD bgColor=#FFFFFF><div align="center"><a href="index.php" onFocus="this.blur()" target="_self"><img src="images/logo_voiper.jpg" border="0"></a></div></TD>
             </TR></TBODY></TABLE></TD></TR>
        <TR>
          <TD height="1" width="100%"></TD></TR>
        <TR>
          <TD width="100%" bgColor=#eeeeee>
            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
              <TBODY>
              <TR>
                <TD vAlign=top width="31%" bgColor="#5cb148">
                  <TABLE cellSpacing="0" cellPadding="3" width="100%" border="0">
                    <TBODY>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="admin/config.php?display=3&mode=pbx" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','10');" onMouseOut="changelayer_color('#f4f2f2','10');"><?php echo _("Voiper Management")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="cdr/index.php" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','40');" onMouseOut="changelayer_color('#f4f2f2','40');"><?php echo _("Call Logs")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a onFocus="this.blur()" onclick="window.open('networkconfig/networkconfig.php','NetworkConfig','height=220,width=410,scrollbars=no,toolbar=no,location=no,screenX=100,screenY=20,top=50,left=200')" href="#" onMouseOver="changelayer_color('#CCCCCC','90');" onMouseOut="changelayer_color('#f4f2f2','90');"><?php echo _("Network Webconfig")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>
                    <? if ($sshd_conf["PermitRootLogin"] == "no") { ?>
                       <B><FONT color=#1E5118>+</FONT></B> <a href="index.php?extcmd=ssh_enable" onFocus="this.blur()" onClick="return ssh_enable(this);" target="_self" onMouseOver="changelayer_color('#CCCCCC','70');" onMouseOut="changelayer_color('#f4f2f2','70');"><?php echo _("Enable SSH user")?></a></FONT></TD>
                    <? } else { ?>
                       <B><FONT color=#1E5118>+</FONT></B> <a href="index.php?extcmd=ssh_disable" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','70');" onMouseOut="changelayer_color('#f4f2f2','70');"><?php echo _("Disable SSH user")?></a></FONT></TD>
                    <? } ?>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="phpconfig/phpconfig.php" target="_blank" onFocus="this.blur()" onMouseOver="changelayer_color('#CCCCCC','16');" onMouseOut="changelayer_color('#f4f2f2','16');"><?php echo _("HTML File Editor")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="scripts/asterisk-full-log.php" onFocus="this.blur()" target="_blank" onMouseOver="changelayer_color('#CCCCCC','4');" onMouseOut="changelayer_color('#f4f2f2','4');"><?php echo _("Asterisk Debug Logs")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="phpmyadmin" target="_blank" onFocus="this.blur()" onMouseOver="changelayer_color('#CCCCCC','5');" onMouseOut="changelayer_color('#f4f2f2','5');"><?php echo _("phpMyAdmin")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a onFocus="this.blur()" onclick="window.open('javassh/index.php','javaSSHclient','height=300,width=650,scrollbars=no,toolbar=no,location=no,screenX=100,screenY=20,top=50,left=200')" href="#" onMouseOver="changelayer_color('#CCCCCC','24');" onMouseOut="changelayer_color('#f4f2f2','24');"><?php echo _("Java SSH client")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="index.php?extcmd=reload" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','6');" onMouseOut="changelayer_color('#f4f2f2','6');"><?php echo _("Reload Asterisk")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="index.php?extcmd=restart" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','7');" onMouseOut="changelayer_color('#f4f2f2','7');"><?php echo _("Restart Asterisk Server")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a onFocus="this.blur()" onclick="window.open('fwmail/fwmail.php','ForwardRootMailwebEditor','height=180,width=500,scrollbars=no,toolbar=no,location=no,screenX=100,screenY=20,top=50,left=200')" href="#" onMouseOver="changelayer_color('#CCCCCC','19');" onMouseOut="changelayer_color('#f4f2f2','19');"><?php echo _("Redirect Root Email")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a onFocus="this.blur()" onclick="window.open('smtpconfig/smtpconfig.php','SmtpConfigMailwebEditor','height=220,width=410,scrollbars=no,toolbar=no,location=no,screenX=100,screenY=20,top=50,left=200')" href="#" onMouseOver="changelayer_color('#CCCCCC','55');" onMouseOut="changelayer_color('#f4f2f2','55');"><?php echo _("SMTP Webconfig")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a onFocus="this.blur()" onclick="window.open('htadmin/index.php','passwordeditor','height=350,width=640,scrollbars=yes,toolbar=no,location=no,screenX=100,screenY=20,top=50,left=200')" href="#" onMouseOver="changelayer_color('#CCCCCC','14');" onMouseOut="changelayer_color('#f4f2f2','14');"><?php echo _("Homepage Password")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="index.php?extcmd=shutdown_pbx" onFocus="this.blur()" target="_self" onClick="return shutdown_pbx(this);" onMouseOver="changelayer_color('#CCCCCC','12');" onMouseOut="changelayer_color('#f4f2f2','12');"><?php echo _("Shutdown")?></a> / <a href="index.php?extcmd=reboot_pbx" onFocus="this.blur()" target="_self" onClick="return reboot_pbx(this);" onMouseOver="changelayer_color('#CCCCCC','12');" onMouseOut="changelayer_color('#f4f2f2','12');"><?php echo _("Reboot")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="phpsysinfo" onFocus="this.blur()" target="_blank" onMouseOver="changelayer_color('#CCCCCC','30');" onMouseOut="changelayer_color('#f4f2f2','30');"><?php echo _("PhpSysInfo")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#333333>+</FONT></B> <a href="webmin.php" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','39');" onMouseOut="changelayer_color('#f4f2f2','39');"><?php echo _("Menu Webmin")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#333333>+</FONT></B> <a href="services.php" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','33');" onMouseOut="changelayer_color('#f4f2f2','33');"><?php echo _("Menu Services")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#333333>+</FONT></B> <a href="aggiornamenti.php" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','93');" onMouseOut="changelayer_color('#f4f2f2','93');"><?php echo _("Menu Update")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#333333>+</FONT></B> <a href="/index.php" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','13');" onMouseOut="changelayer_color('#f4f2f2','13');"><?php echo _("Main Menu")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                      </TBODY></TABLE></TD>
                <TD vAlign=top width="1%" background="images/frame1.gif"></TD>
                <TD vAlign=top width="67%" bgColor=#f4f2f2><table width="100%" border="0" cellspacing="0" cellpadding="3">
                  <tr>
                    <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </tr>
                  <tr>
                    <td><div id="10"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Voiper Management Portal.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="40"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Call Log details.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="90"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Network Parameters.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="70"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Enable / Disable SSH user for Remote Access.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="16"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Easy html editor for asterisk configuration files.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="4"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Asterisk: Maintenance log.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="5"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("mySql Server administration.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="24"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Shell SSH Java.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="6"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Force Asterisk configuration reload.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="7"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Restart asterisk Server.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="19"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Redirect all mails for user root.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="55"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("SMTP Webconfig.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="14"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Pbx User Manager.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="12"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Shutdown or Reboot of Voiper PBX.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="30"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("System Info.")?></font></div></td>
                    </tr>
                  <tr>
                    <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                   </tr>
                  <tr>
                    <td><div id="39"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Go to Webmin menu.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="33"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Go to Services menu.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="93"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Go to Update menu.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="13"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Back to main menu.")?></font></div></td>
                    </tr>
                  <tr>
                    <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                   </tr>
                </table></TD>
              </TR></TBODY></TABLE>
            <TABLE cellSpacing=0 width="100%" bgColor="#e97948" background="images/frame4.gif" border=0>
              <TBODY>
              <TR>
                <TD><div align="left"><FONT face="Verdana, Arial, Helvetica, sans-serif" color=#000000 size=2>Pbx Hostname: <? echo passthru('hostname'); ?></FONT></div></TD>
                <TD><div align="right"><FONT face="Verdana, Arial, Helvetica, sans-serif" color=#000000 size=2><? echo "online: ".uptime(); ?>&nbsp;</FONT></div></TD>
              </TR></TBODY></TABLE>
            <TABLE cellSpacing=0 width="100%" bgColor=#FFFFFF background="images/frame4.gif" border=0>
              <TBODY></TBODY></TABLE>
          </TR></TABLE></TR></TBODY></TABLE>

<? require_once('status.php'); ?>

</DIV>
</BODY></HTML>
