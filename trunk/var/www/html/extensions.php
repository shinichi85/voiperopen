<?

require_once('functions.php');

$extcmd=$_REQUEST['extcmd'];

?>

<html>
<head>
<title><?php echo _("Voiper Extensions")?></title>
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="1" cellpadding="0" cellspacing="3" bordercolor="#000000">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="3" bordercolor="#000000">
      <tr bgcolor="#E1E1E1">
        <td colspan="2"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Voiper Pbx Special Extensions")?>:</font></div></td>
        </tr>
      <tr>
        <td bgcolor="#000000" colspan="2"></td>
        </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*43</font></strong></td>
        <td bgcolor="#FF9966"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Echo test.")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*60</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Enable / Disable Service Day-Night (Overwrites settings set with the VMP)")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*61</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Enable Automatic recall service (Busy / Not Available / Voice mail)")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td bgcolor="#FFCC66"><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*62</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Disable Automatic recall service (Busy / Not Available / Voice mail)")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*63</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Monitoring a call incoming / outgoing on Channels Bridge. (password required)")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td bgcolor="#FFCC66"><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*64 + <?php echo _("extension")?></font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Enable call transfer from extension not Available.")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td bgcolor="#FF9966"><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*65</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Disable call transfer from Extension not Available.")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td bgcolor="#FFCC66"><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*66</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Say the extension number of the phone you are using")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*67</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Enables call recording inbound / outbound in file format. (Overwrites settings set with the VMP)")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*68</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Disables call recording inbound / outbound in file format. (Overwrites settings set with the VMP)")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*69</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Say the last received caller id")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*70</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Enable call waiting.")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*71</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Disable call waiting.")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*72  + <?php echo _("extension")?></font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Enables call forward towards specified extension.")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*73</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Disables call forward.")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*72</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Enables call forward with Wizard.")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*73 + <?php echo _("extension")?></font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Disable call forward on specified extension.")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*74</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Enables Voice mail. (Overwrites settings set with the VMP)")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*75</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Disables Voice mail. (Overwrites settings set with the VMP)")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*76</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Time now.")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*77</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Records a message. (Useful to customize IVR messages)")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*78</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Enables Do not disturb.")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*79</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Disables Do not disturb.")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*90 + <?php echo _("extension")?></font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Enables call forward on busy.")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*91</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Disables call forward on busy.")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*92</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Monitoring a call inbound / outbound on ZAP channel. (password required)")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*93</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("It works as * 92 but with Scanner mode (# for next channel - * to exit).")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*94</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Wakeup service.")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*95</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Monitoring a call inbound / outbound (ZAP) digiting an extension.")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*97</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Personal Voice mail.")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*98</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Central Voice mail.")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*98 +<?php echo _("extension")?></font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Personal Voice Mail related to the selected extension.")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*99</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Listening to the message Recorded with the * 77 function")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*411</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Company directory")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">* + <?php echo _("extension")?></font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Leave a direct message to the voicemail extension.")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif"># + <?php echo _("extension")?></font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("the # with a extension make the Blind Transfer (During a call in progress).")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*8</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Performs PickUP Group (see configuration PickupGroup / CallGroup in Extensions).")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*9</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Performs recording of the call in progress in advanced mode.")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">** + <?php echo _("extension")?></font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Performs  a call pickup (Compatible with Grandstream GXP2000 blf).")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif"># + 70</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Call parking.")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*2</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Attended Transfer")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*1</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Recording of the call in progress")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("queue")?> + *</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Dynamic agent login to queue")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("queue")?> + **</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Agent logout from queue")?></font></td>
      </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">71 ... 79</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Parking Area . Dial the extension to pickup the parked call.")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td colspan="2">&nbsp;</td>
        </tr>
      <tr bgcolor="#FF9966">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">7771 ... 7776</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Emulates incoming call (Incoming # 1 ... # 6)")?></font></td>
      </tr>
      <tr bgcolor="#FFCC66">
        <td><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">666</font></strong></td>
        <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Local extension to Receiving FAX. (Only CODEC: Alaw / ULAW)")?></font></td>
      </tr>
      <tr>
        <td bgcolor="#000000"colspan="2"></td>
        </tr>
      <tr bgcolor="#E1E1E1">
        <td colspan="2"><div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo _("Please note: Use 'the: SEND' key on ip phones to dial or send commands (could be '#' on Grandstream phones)")?></font></div></td>
        </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
