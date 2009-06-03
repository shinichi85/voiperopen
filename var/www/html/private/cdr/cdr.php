<?php /* $Id: cdr.php,v 1.4 2005/03/13 20:34:03 rcourtna Exp $ */

session_start();

echo '<center><a href="/private/index.php">Homepage Voiper</a></center>';

function cdrpage_getpost_ifset($test_vars)
{
	if (!is_array($test_vars)) {
		$test_vars = array($test_vars);
	}
	foreach($test_vars as $test_var) { 
		if (isset($_POST[$test_var])) { 
			global $$test_var;
			$$test_var = $_POST[$test_var]; 
		} elseif (isset($_GET[$test_var])) {
			global $$test_var; 
			$$test_var = $_GET[$test_var];
		}
	}
}

cdrpage_getpost_ifset(array('s', 't'));


$array = array ("INTRO", "CDR REPORT", "CALLS COMPARE", "MONTHLY TRAFFIC","DAILY LOAD", "CONTACT");
$s = $s ? $s : 0;
$section="section$s$t";

$racine=$PHP_SELF;
$update = "03 March 2005";


$paypal="NOK"; //OK || NOK
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>		
		<title>Asterisk CDR</title>
		<meta http-equiv="Content-Type" content="text/html">
		<link rel="stylesheet" type="text/css" media="print" href="css/print.css">
		<SCRIPT LANGUAGE="JavaScript" SRC="encrypt.js"></SCRIPT>
		<style type="text/css" media="screen">
			@import url("css/layout.css");
			@import url("css/content.css");
			@import url("css/docbook.css");
		</style>
		<meta name="MSSmartTagsPreventParsing" content="TRUE">
	</head>
	<body>
	
	<?php if ($section=="section0"){?>

<h1>
 <center>ASTERISK : CDR ANALYSER</center>
</h1>
						<h3>Call data collection</h3>
						<p>Regardless of their size, most telephone PBX (public branch exchange) and PMS (property management systems)
						output <b>Call Detail Records (CDR)</b>. Generally, these get created at the end of a call but on some phone systems
						the data is available during the call. This data is output from the phone system by a serial link known as the
						Station Message Detail Recording port (SMDR). <b>Some of the details included in call records are: Time, Date, Call
						Duration, Number dialed, Caller ID information, Extension, Line/trunk location, Cost, Call completion status.</b><br>
						<br>
						Call detail records, both local and long distance, can be used for usage verification, billing reconciliation,
						network management and to monitor telephone usage to determine volume of phone usage, as well as abuse of the system. 
						CDR's aid in the planning for future telecommunications needs. <br>
						<br>
						Control with CDR analysis:
						<ul>

							<li>review all CDR's for accuracy 
							<li>verify usage 
							<li>resolve discrepancies with vendors
							<li>disconnect unused service 
							<li>terminate leases on unused equipment 
							<li>deter or detect fraud
							<li>etc ...
						</ul>

<?php }elseif ($section=="section1"){?>

	<?php require("call-log.php");?>


<?php }elseif ($section=="section2"){?>

	<?php require("call-comp.php");?>


<?php }elseif ($section=="section3"){?>

	<?php require("call-last-month.php");?>

<?php }elseif ($section=="section4"){?>

	<?php require("call-daily-load.php");?>


<?php }elseif ($section=="section5"){?>
		<h1>Contact</h1>        		
        <table width="90%">
          
		  <tr> 
            <td>
				<h3>Arezqui Bela&iuml;d <br> <i>Barcelona - Belgium</i></h3>				
				<br>
				<a href='javascript:bite("3721 945 4728 2762 3565 3554 2008 1380 654 3721 3554 4468 3007 3877 4828 654",5123,2981)'>Click to email me</a>
				<br><br><i>Feel free to send me your suggestions to improve the application ;)</i>
            </td>
          </tr>          
          
        </table>
		<br><br><em><strong>Last update:</strong></em> <?php echo $update?><br>


<?php }else{?>
	<h1>Coming soon ...</h1>
   
<?php }?>

		
		<br><br><br><br><br><br>
		</div>

			<div class="fedora-corner-br">&nbsp;</div>
			<div class="fedora-corner-bl">&nbsp;</div>
		</div>
		<!-- content END -->
		
		<!-- footer BEGIN -->
		<div id="fedora-footer">

			<br>			
		</div>
		<!-- footer END -->
	</body>
</html>
