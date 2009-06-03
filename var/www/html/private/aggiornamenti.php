<?

require_once('functions.php');

$extcmd=$_REQUEST['extcmd'];

?>

<HTML>
<HEAD><TITLE>Voiper Updater</TITLE>
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
                <TD bgColor=#FFFFFF><div align="left"><FONT face="Verdana, Arial, Helvetica, sans-serif" size=1><?php echo _("Welcome")?> <b><? echo getenv('REMOTE_USER'); ?></b> (<?php echo _("Administrator")?>)</font></div></TD>
             </TR>
              <TR>
                <TD bgColor=#FFFFFF><div align="center"><a href="aggiornamenti.php" onFocus="this.blur()" target="_self"><img src="images/logo_voiper.jpg" border="0"></a></div></TD>
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
                        color=#1E5118>+</FONT></B> <a onFocus="this.blur()" onclick="window.open('update/update.php','updatesoftware','height=350,width=600,scrollbars=no,toolbar=no,location=no,screenX=100,screenY=20,top=50,left=200')" href="#" onMouseOver="changelayer_color('#CCCCCC','11');" onMouseOut="changelayer_color('#f4f2f2','11');"><?php echo _("Voiper Updater")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="http://www.voiper.it/voiperupdate" onFocus="this.blur()" target="_blank" onMouseOver="changelayer_color('#CCCCCC','4');" onMouseOut="changelayer_color('#f4f2f2','4');"><?php echo _("Download Update")?></a></FONT></TD>
                    </TR>
                     <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#1E5118>+</FONT></B> <a href="aggiornamenti.php?extcmd=update" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','6');" onMouseOut="changelayer_color('#f4f2f2','6');"><?php echo _("Check for Updates")?></a></FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                    </TR>
                    <TR>
                      <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2><B><FONT
                        color=#333333>+</FONT></B> <a href="index.php" onFocus="this.blur()" target="_self" onMouseOver="changelayer_color('#CCCCCC','13');" onMouseOut="changelayer_color('#f4f2f2','13');"><?php echo _("Main menu")?></a></FONT></TD>
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
                    <td><div id="11"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Pbx Voiper web updater.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="4"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Download from Voiper.it the software update.")?></font></div></td>
                    </tr>
                  <tr>
                    <td><div id="6"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Check for available Updates.")?></font></div></td>
                    </tr>
                  <tr>
                    <TD><FONT face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;</FONT></TD>
                   </tr>
                  <tr>
                    <td><div id="13"><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?php echo _("Back to Administrator Menu.")?></font></div></td>
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

<?
switch($extcmd) {

    default:
    break;

    case 'update';

	$sites = 'www.voiper.it';
    $sock = fsockopen( $sites, 80, $errno, $errstr, 3 );

    if ( ! $sock ) {
        print "<br><TABLE cellspacing='0' cellpadding='3' width='400' border='0'><TBODY><TR>";
        print "<td><div align='center'><font color='#FF0000' size='1' face='Verdana, Arial, Helvetica, sans-serif'><b>";?><? echo _("Internet connection is unavailable.")?><? print"</b><br><font color='#000000'>";?><? echo _("Please try again later.")?><? print"</font></font></div></td>";
        print "</TBODY></TABLE>";
    } else {
            $ver_voiper_pbx = file_parser("/etc/ver_voiper");
            $ver_vcti_pbx = file_parser("/etc/ver_vcti");
            exec('cd /tmp ; /usr/bin/wget http://www.voiper.it/it/update/version2.txt');
            exec('cd /tmp ; /usr/bin/wget http://www.voiper.it/it/update/version_vcti.txt');                    
            $ver_voiper_www = file_parser("/tmp/version2.txt");
            $ver_vcti_www = file_parser("/tmp/version_vcti.txt");
            exec('rm -f /tmp/version2.txt /tmp/version_vcti.txt');
            $version_voiper_www = $ver_voiper_www["version"];
            $version_voiper_pbx = $ver_voiper_pbx["version"];
            $version_vcti_www = $ver_vcti_www["version"];
            $version_vcti_pbx = $ver_vcti_pbx["version"];

            if ("$version_voiper_www" > "$version_voiper_pbx") {
                print "<br>";
                print "<TABLE cellspacing='0' cellpadding='3' width='450' border='0'><TBODY><TR>";
                print "<td><div align='center'><font color='#000000' size='1' face='Verdana, Arial, Helvetica, sans-serif'>";?><? echo _("A new version of Voiper Software is available.");?><? print"<br>";?><? echo _("Click on Download Update for update your Voiper to the version");?><? print " ".$version_voiper_www."</font></div></td>";
                print "</TBODY></TABLE>";
            } else {
                    print "<br>";
                    print "<TABLE cellspacing='0' cellpadding='3' width='450' border='0'><TBODY><TR>";
                    print "<td><div align='center'><font color='#000000' size='1' face='Verdana, Arial, Helvetica, sans-serif'>";?><? echo _("Voiper already has the latest version of Software.");?><? print"</font></div></td>";
                    print "</TBODY></TABLE>";
            }

            if ("$version_vcti_www" > "$version_vcti_pbx") {
                print "<TABLE cellspacing='0' cellpadding='3' width='450' border='0'><TBODY><TR>";
                print "<td><div align='center'><font color='#000000' size='1' face='Verdana, Arial, Helvetica, sans-serif'>";?><? echo _("A new version of VCTI Software is available.");?><? print"<br>";?><? echo _("Click on Download Update for update your VCTI to the version");?><? print " ".$version_vcti_www."</font></div></td>";
                print "</TBODY></TABLE>";
            } else {
                    print "<TABLE cellspacing='0' cellpadding='3' width='450' border='0'><TBODY><TR>";
                    print "<td><div align='center'><font color='#000000' size='1' face='Verdana, Arial, Helvetica, sans-serif'>";?><? echo _("VCTI already has the latest version of Software.");?><? print"</font></div></td>";
                    print "</TBODY></TABLE>";
            }
    }
    break;
}

require_once('status.php'); 

?>
</DIV>
</BODY></HTML>
