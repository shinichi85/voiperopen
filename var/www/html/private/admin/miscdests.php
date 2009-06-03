<?php /* $Id: $ */
//Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
//
//This program is free software; you can redistribute it and/or
//modify it under the terms of the GNU General Public License
//as published by the Free Software Foundation; either version 2
//of the License, or (at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.


isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action='';
isset($_REQUEST['id'])?$extdisplay = $_REQUEST['id']:$extdisplay='';
isset($_REQUEST['goto0'])?$goto = $_REQUEST['goto0']:$goto='';
isset($_REQUEST['destdial'])?$destdial = $_REQUEST['destdial']:$destdial='';
isset($_REQUEST['description'])?$description = $_REQUEST['description']:$description='';

$wScript1 = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_extensions_from_mysql.pl';

$dispnum = 19;
$context = "ext-miscdests";
$skip = $_REQUEST['skip'];
$perpage = $_REQUEST['perpage'];
if ($skip == "") $skip = 0;
if ($perpage == "") $perpage = 30;
$skipped = 0;
$index = 0;
$pagerz = 0;

switch ($action) {
	case "add":

		$errmiscdests = miscdests_add($destdial,$context,$goto,$description);

			if ($errmiscdests != false) {
				
				exec($wScript1);
				needreload();

			}
	break;
	case "delete":
		miscdests_del($extdisplay,$context);
		exec($wScript1);
		needreload();
	break;
	case "edit":
		miscdests_del($extdisplay,$context);
		miscdests_add($destdial,$context,$goto,$description);
		exec($wScript1);
		needreload();
	break;
}

$miscdests = miscdests_list($context);

?>

</div>

<div class="rnav" style="width:225px;">
    <li><a id="<?php echo ($extdisplay=='' ? 'current':'') ?>" href="config.php?mode=pbx&display=<?php echo urlencode($dispnum)?>" onFocus="this.blur()"><?php echo _("Add Misc Destination")?></a></li>
<?php

if (isset($miscdests)) {

        foreach ($miscdests AS $key=>$result) {
            if ($index >= $perpage) {
                $shownext= 1;
				$pagerz=1;
                break;
                }
            if ($skipped<$skip && $skip!= 0) {
                $skipped= $skipped + 1;
				$pagerz=1;
                continue;
                }
            $index= $index + 1;
	
				echo "<li><a id=\"".($extdisplay==$result[0] ? 'current':'')."\" title=\"$result[1]\" href=\"config.php?mode=pbx&display=".urlencode($dispnum)."&id=".urlencode($result[0])."&skip=$skip\" onFocus=\"this.blur()\">".(substr($result[1],0,22))." <{$result[0]}></a></li>";
	}
}

if	($pagerz == 1){

    print "<li><center><div class='paging'>";
}

	if ($skip) {

	    $prevskip= $skip - $perpage;
	    if ($prevskip<0) $prevskip= 0;
	    $prevtag_pre= "<a onFocus='this.blur()' href='?mode=pbx&display=".$dispnum."&skip=$prevskip'>[PREVIOUS]</a>";
	    print "$prevtag_pre";
	    }
    	if (isset($shownext)) {

    	    $nextskip= $skip + $index;
    	    if ($prevtag_pre) $prevtag .= " | ";
    	    print "$prevtag <a onFocus='this.blur()' href='?mode=pbx&display=".$dispnum."&skip=$nextskip'>[NEXT]</a>";
    	    }

            print "</div></center></li>";
?>
</div>


<div class="content">
<?php
if ($action == 'delete') {
	echo '<br><h3>'._("Misc Destination").' '.$extdisplay.' '._("deleted").'!</h3><br><br><br><br><br><br><br><br>';
} else {
	if ($extdisplay){ 

		$thisMiscDest = miscdests_get($extdisplay,$context);
		$extension = "";
		$descr = "";
		extract($thisMiscDest);
	}

	$delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=delete';
?>

	
<?php		if ($extdisplay){ ?>
	<h3><?php echo _("Misc Destination:")." ". $description; ?></h3>
	<p><a href="<?php echo $delURL ?>" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete Misc Destination")?> '<?php echo $descr; ?>'</a></p>
<?php		} else { ?>
	<h3><?php echo _("Add Misc Destination:"); ?></h3>
<?php		}
?>
	<form autocomplete="off" name="editMD" action="<?php $_SERVER['PHP_SELF'].'&mode=pbx' ?>" method="post" onsubmit="return editMD_onsubmit();">
	<input type="hidden" name="display" value="<?php echo $dispnum?>">
	<input type="hidden" name="action" value="<?php echo ($extdisplay ? 'edit' : 'add') ?>">
	<table>
	<tr><td colspan="2"><h5><?php echo ($extdisplay ? _("Edit Misc Destination") : _("Add Misc Destination")) ?></h5></td></tr>
<?php		if ($extdisplay){ ?>
		<tr><td><input type="hidden" name="id" value="<?php echo $extdisplay; ?>"></td></tr>
<?php		} ?>
	<tr>
		<td><a href="#" class="info"><?php echo _("Description:")?><span><?php echo _("Give this Misc Destination a brief name to help you identify it.")?></span></a></td>
		<td><input type="text" name="description" size="30" value="<?php echo (isset($descr) ? $descr : ''); ?>"></td>
	</tr>
	<tr>
		<td><a href="#" class="info"><?php echo _("Extension:")?><span><?php echo _("Enter the digits to dial for this Misc Destination.<br>Valid Range are from:<br>100 to 899<br>1000 to 8999<br>10000 to 89999")?></span></a></td>
		<td>
			<input type="text" maxlength="5" size="5" name="destdial" value="<?php echo (isset($extension) ? $extension : ''); ?>">&nbsp;&nbsp;
		</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2">

<?php 
			$goto = getargs($extension,1,'ext-miscdests');
			echo drawselects('editMD',isset($goto)?$goto:null,0,'fixINCOMING','','','fixCALLBACKEXT','fixMEETME');

?>

			</td></tr>
		<tr><td colspan="2"><br><h6><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>"></h6>
		</td>
	</tr>
	</table>
<script language="javascript">
<!--

var theForm = document.editMD;

if (theForm.description.value == "") {
	theForm.description.focus();
} else {
	theForm.destdial.focus();
}

function editMD_onsubmit()
{
	var msgInvalidDescription = "<?php echo _('Please enter a valid Description'); ?>";
	var msgInvalidDial = "<?php echo _('Please enter a valid Dial string'); ?>";
	var bad = "false";
	
	defaultEmptyOK = false;
	if (!isAlphanumeric(theForm.description.value))
		return warnInvalid(theForm.description, msgInvalidDescription);

	// go thru text and remove the {} bits so we only check the actual dial digits
	var fldText = theForm.destdial.value;
	var chkText = "";
	
	if ( (fldText.indexOf("{") > -1) && (fldText.indexOf("}") > -1) ) { // has one or more sets of {mod:fc}
		
		var inbraces = false;
		for (var i=0; i<fldText.length; i++) {
			if ( (fldText.charAt(i) == "{") && (inbraces == false) ) {
				inbraces = true;
			} else if ( (fldText.charAt(i) == "}") && (inbraces == true) ) {
				inbraces = false;
			} else if ( inbraces == false ) {
				chkText += fldText.charAt(i);
			}
		}
		
		// if there is nothing in chkText but something in fldText
		// then the field must contain a featurecode only, therefore
		// there really is something in thre!
		if ( (chkText == "") & (fldText != "") )
			chkText = "0";
			
	} else {
		chkText = fldText;
	}
	// now do the check using the chkText var made above
	if (!isDialDigits(chkText))
		return warnInvalid(theForm.destdial, msgInvalidDial);
	
	var whichitem = 0;
		
    $dialgoto = theForm.dial_args0.value;
	while (whichitem < theForm.goto_indicate0.length) {
		if (theForm.goto_indicate0[whichitem].checked) {
			theForm.goto0.value=theForm.goto_indicate0[whichitem].value;
		}
		whichitem++;
	}
	
	var gotoType = theForm.elements[ "goto0" ].value;
	if (gotoType == 'custom') {
		var gotoVal = theForm.elements[ "custom_args0"].value;
		if (gotoVal.indexOf('custom') == -1) {
			bad = "true";
			<?php echo "alert('"._("Custom Goto contexts must contain the string \"custom\".  ie: custom-app,s,1")."')"?>;
		}
	}
	
		if (gotoType == 'dial') {	
        if ($dialgoto == "") {

                bad="true";
				<?php echo "alert('"._("Custom number to dial must not be blank")."')"?>;

        } else if (!$dialgoto.match('^[0-9]+$')) {
		
                bad="true";
				<?php echo "alert('"._("Custom number to dial only contain numbers")."')"?>;

        }	
	}	
	
		if (bad == "false") {
			return true;
	} else {
	return false;
}
}



function deleteCheck(f2) {

	cancel = false;
	ok = true;

	if (confirm("Are you sure to delete this Misc Destinations?"))
  		return ! cancel;
	else
  		return ! ok;
}

//-->
</script>
	</form>
<?php		
}
?>
