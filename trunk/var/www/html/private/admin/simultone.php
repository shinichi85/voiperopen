<?php

//Copyright (C) 2005 SpheraIT

?>

<script language="Javascript">

	function addNewSimulTone(){
		document.location = "config.php?display=16&action=addnew";
	}

	function checkSimulTone(theForm) {
		var count = theForm.count.value;
		var error = 0;
		var speednrs = new Array(count);
		for ( var i = 0 ; i < count ; i++ ) {
			var speedfield = "simul_num" + i;
			var trunknum = "trunk_num" + i;
			var desc = "description" + i;
			checkbox = eval('"delete"+i');
			var speednr = document.getElementById(speedfield).value;
			var trunknumnr = document.getElementById(trunknum).value;
			var descnr = document.getElementById(desc).value;
			if (document.getElementById(checkbox).checked==true) {
				speednrs.push(speednr);
			} else if (!isInteger(speednr)) {
				alert('Please enter a valid Extension Number.');
				error = 1;
				break;
			} else if (trunknumnr == "") {
				alert('Please enter a valid Trunk.');
				error = 1;
				break;
			} else if (!isAlphanumeric(descnr)) {
				alert('Please enter a valid Description Name.');
				error = 1;
				break;
				} else if (FNArrayGetSearchLinearRowI(speednr, speednrs) > 0) {
				alert('The following extension already exists: '+speednr+'.');
				error = 1;
				break;
			} else {
				error = 0;
			}
			speednrs.push(speednr);
		}

		if(error == 0) {
			theForm.submit();
		}
	}

	function FNArrayGetSearchLinearRowI( searchS, arraySA ) {
		var I = 0;
		var minI = 0;
		var maxI = arraySA.length - 1;
		//
		var s = "";
		//
		var foundB = false;
		//
		I = minI - 1;
		while ( ( I <= maxI ) && ( !( foundB ) ) ) {
			I = I + 1;
			s = arraySA[ I ];
			foundB = ( searchS == s );
		}
		if ( foundB ) {
			return( I );
		}
		else {
			return( -1000 );
		}
	}



</script>
<?php

$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_simultone_from_mysql.pl';

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';

$dispnum = 16;

if ($action == 'addnew') {
	$sqlins = "INSERT INTO simultone (trunk_num, simul_num, description) VALUES ('$trunk_num', '$simul_num', '$description')";
	$resins =& $db->query($sqlins);
	if (DB::isError($resins)) {
	    die($resins->getMessage());
	}

}

if ($action == 'editglobals') {

	$count = $_POST["count"];

	for($i = 0; $i < $count; $i++) {

		$trunk_num =  $_POST["trunk_num".$i];
		$simul_num =  $_POST["simul_num".$i];
		$description =  $_POST["description".$i];
		$id =  $_POST["id".$i];
		$del = intval($_POST["delete".$i]);

		if($del) $sql="delete from simultone where id = '$id'";
		else $sql = "UPDATE simultone SET trunk_num='$trunk_num', simul_num='$simul_num', description='$description' WHERE id='$id'";
		$res =& $db->query($sql);
		if (DB::isError($res)) {
		    die($res->getMessage());
		}
	}

	unset($id);
	unset($trunk_num);
	unset($simul_num);
	unset($description);
	unset($del);

	exec($wScript);
	needreload();
}

$sql = "SELECT * FROM globals";
$globals = $db->getAll($sql);
if(DB::IsError($globals)) {
die($globals->getMessage());
}

foreach (gettrunks() as $temp) {
	$trunks[trim($temp[0])] = trim($temp[1]);
}

$sql = "SELECT * FROM simultone ORDER BY simul_num ASC";
$results = $db->getAll($sql);
if(DB::IsError($results)) {
die($results->getMessage());
}

$count = 0;
foreach ($results as $result) {
	$trunk_num[$count] = $result[1];
	$simul_num[$count] = $result[2];
	$description[$count] = $result[3];
	$id[$count] = $result[0];
	$count++;
}
?>


<form name="simultone" action="config.php?mode=pbx" method="post">
<input type="hidden" name="display" value="<?php echo $dispnum?>"/>
<input type="hidden" name="action" value="editglobals"/>
<input type="hidden" name="count" value="<?php echo $count; ?>"/>
<h3><?php echo _("Trunk Tone Simulator:")?></h3>
<p>
<table border="0" cellspacing="0" cellpadding="3">

<?php

	if ($count > 0) {

?>

  <tr>
	<td align="left"><b><?php echo _("Extension:")?></b></td>
	<td align="center"><b><?php echo _("Trunk:")?></b></td>
	<td align="center"><b><?php echo _("Description:")?></b></td>
	<td align="center"><b><?php echo _("Delete")?></b></td>
  </tr>

<?php
	$key = 0;
	for($i = 0; $i < $count; $i++) {

	echo "<tr>";
	echo "<td colspan=\"4\"><input type=\"hidden\" name=\"id$i\" value=\"".$id[$i]."\"></td>";
	echo "</tr>";
  	echo "<tr>";
	echo "<td><input type=\"text\" size=\"1\" maxlength=\"2\" id=\"simul_num$i\" name=\"simul_num$i\" value=\"".$simul_num[$i]."\"></td>";

?>
    <td><select <?php echo "id=\"trunk_num$i\" name=\"trunk_num$i\"" ?>
				>
				<option value="" SELECTED></option>
				<?php
				foreach ($trunks as $name=>$display) {
					echo "<option id=\"trunk".$key."\" value=\"".$name."\" ".($name == "$trunk_num[$i]" ? "selected" : "").">".(strpos($display,'AMP:')===0 ? substr($display,4) : $display)."</option>";
				}
				?>
				</select></td>
<?php

	echo "<td><input type=\"text\" size=\"30\" maxlength=\"30\" id=\"description$i\" name=\"description$i\" value=\"".$description[$i]."\"></td>";
	echo "<td>&nbsp;&nbsp;<input type=\"checkbox\" id=\"delete$i\" name=\"delete$i\" value=\"1\" onFocus=\"this.blur()\"></td>";
	echo "</tr>";

$key += 1;
$name = "";
	 }

	 } else {
	 ?>

	 <tr><td align="center"><b><?php echo _("No Trunk Tone Extensions found.")?></b></td></tr>

	 <?php
	}
?>

</table>
</p>

<h6>
<input name="addnew" type="button" value="<?php echo _("Add new")?>" onclick="addNewSimulTone()" onFocus="this.blur()"><input name="Submit" type="button" value="<?php echo _("Submit Changes")?>" onclick="checkSimulTone(simultone)" onFocus="this.blur()">
</h6>
</form>
