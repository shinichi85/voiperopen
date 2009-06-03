<?

require_once('functions.php');

$extcmd=$_REQUEST['extcmd'];

?>

<HTML>
<HEAD><TITLE>Voiper Management Portal</TITLE>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<link href="home.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
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

function changeLang(lang) {
	document.cookie='lang='+lang;
	window.location.reload();
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
                <TD width="78%" bgColor=#FFFFFF><div align="right"><a href="index.php" target="_self" onFocus="this.blur()"><img src="images/logo_voiper.jpg" border="0"></a></div></TD>
                <TD width="22%" valign="top" bgColor=#FFFFFF><div align="right"><img src="images/logo_mix_small.png" border="0"></div></TD>
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
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT> </B><a href="public/index.php" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','1');" onMouseOut="changelayer_color('#f4f2f2','1');"><?php echo _("User Access")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="private/index.php" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','2');" onMouseOut="changelayer_color('#f4f2f2','2');"><?php echo _("Admin Access")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="info.php" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','8');" onMouseOut="changelayer_color('#f4f2f2','8');"><?php echo _("System Information")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="http://www.voiper.it/voipermanual" onFocus="this.blur()" target="_blank" onMouseOver="changelayer_color('#CCCCCC','3');" onMouseOut="changelayer_color('#f4f2f2','3');"><?php echo _("Download Documentation")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT> </B><a onFocus="this.blur()" onclick="window.open('extensions.php','Special Extensions','height=450,width=700,scrollbars=yes,toolbar=no,location=no,screenX=100,screenY=20,top=50,left=200')" href="#" onMouseOver="changelayer_color('#CCCCCC','5');" onMouseOut="changelayer_color('#f4f2f2','5');"><?php echo _("Special Extensions")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                     <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#333333>+</FONT></B> <a href="logout.php" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','7');" onMouseOut="changelayer_color('#f4f2f2','7');"><?php echo _("Logout VMP")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
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
                    <td><div id="1"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Pbx User Access.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="2"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Pbx Administrator Access.")?></font></div></td>
                    </tr>
                   <tr>
                    <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </tr>
                  <tr>
                    <td><div id="8"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Voiper version information.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="3"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Download Documentation from Voiper.it")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="5"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Voiper special Extensions.")?></font></div></td>
                    </tr>
                   <tr>
                    <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </tr>
                  <tr>
                    <td><div id="7"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Logout Voiper Management Portal.")?></font></div></td>
                    </tr>
                   <tr>
                    <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </tr>
                  <tr>
                    <td><div align="right"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">

<?php if (extension_loaded('gettext')) {?>
	<?php echo _("Language")?>:
	<select onchange="javascript:changeLang(this.value)">
         <option value="en_US" <? echo ($_COOKIE['lang']=="en_US" ? "selected" : "") ?> >English</option>
         <option value="it_IT" <? echo ($_COOKIE['lang']=="it_IT" ? "selected" : "") ?> >Italian</option>
    </select>
<?php } ?>
</font></div></td>
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
