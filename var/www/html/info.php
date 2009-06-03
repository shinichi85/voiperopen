<?

require_once('functions.php');

?>

<HTML>
<HEAD><TITLE>Informazioni Sistema Voiper</TITLE>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<link href="home.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
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
// -->
</SCRIPT>
</HEAD>

<BODY text="#333333" vLink="#000000" aLink="#000000" link="#000000" bgColor="#ffffff" background="images/vgs.gif">

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
                <TD width="78%" bgColor=#FFFFFF><div align="right"><a href="info.php" target="_self" onFocus="this.blur()"><img src="images/logo_voiper.jpg" border="0"></a></div></TD>
                <TD width="22%" valign="top" bgColor=#FFFFFF><div align="right"><img src="/images/logo_mix_small.png" border="0"></div></TD>
              </TR></TBODY></TABLE></TD></TR>
        <TR>
          <TD width="100%" height="1"></TD></TR>
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
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
		            </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                     <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#333333>+</FONT> </B><a href="index.php" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','1');" onMouseOut="changelayer_color('#f4f2f2','1');"><?php echo _("Main menu")?></a></FONT></TD>
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
                    <td><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Pbx Model")?>: <font color="#FF0000"><? echo $serial_pn; ?></font></font></td>
                    </tr>
                  <tr>
                    <td><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Development state")?>: <font color="#FF0000"><? echo $ver_voiper["development"]; ?></font></font></td>
                  </tr>
                  <tr>
                    <td><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Voiper Management Portal (VMP)")?>: <font color="#FF0000"><? echo $ver_voiper["version"]; ?></font> <font color="#000000"><? echo $ver_voiper["data"]; ?></font></font></td>
                    </tr>
                  <tr>
                    <td><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Voiper CTI (VCTI)")?>: <font color="#FF0000"><? echo $ver_vcti["version"]; ?></font> <font color="#000000"><? echo $ver_vcti["data"]; ?></font></td>
                    </tr>
                  <tr>
                    <td><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Asterisk Core version")?>: <font color="#FF0000">v<? echo $ver_asterisk["Asterisk"]; ?></font></font></td>
                    </tr>
                  <tr>
                    <td><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Asterisk Zaptel version")?>: <font color="#FF0000">v<? echo $ver_zaptel; ?></font></font></td>
                    </tr>
                  <tr>
                    <td><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Asterisk LibPri version")?>: <font color="#FF0000">v<? echo $ver_libpri; ?></font></font></td>
                    </tr>
                  <tr>
                    <td><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Serial")?>: <font color="#FF0000"><? echo $serial; ?></font></font></td>
                    </tr>
                  <tr>
                    <td><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("MacAddress ETH0")?>: <font color="#FF0000"><? echo returnMacAddress(); ?></font></font></td>
                    </tr>
                  <tr>
                    <td><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("SSH Remote access")?>: <font color="#FF0000"><? if ($sshd_conf["PermitRootLogin"] == "yes") { echo _("Enabled"); } else { echo _("Disabled"); } ?></font></font></td>
                    </tr>
                   <tr>
                    <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </tr>
                  <tr>
                    <td><div id="1"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Back to main menu.")?></font></div></td>
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
          </TR></TBODY></TABLE></TR></TBODY></TABLE>
</DIV>
</BODY></HTML>
