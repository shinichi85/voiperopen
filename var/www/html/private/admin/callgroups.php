<?php /* $Id: callgroups.php,v 1.23 2005/06/20 13:44:45 ronhartmann Exp $ */
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
?>

<script language="JavaScript">

function deleteCheck(f2) {

	cancel = false;
	ok = true;

	if (confirm("Are you sure to delete this RingGroup?"))
  		return ! cancel;
	else
  		return ! ok;
}

</script>

<?php

$wScript1 = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_extensions_from_mysql.pl';

$dispnum = 4;
$skip = $_REQUEST['skip'];
$perpage = $_REQUEST['perpage'];
if ($skip == "") $skip = 0;
if ($perpage == "") $perpage = 30;
$skipped = 0;
$index = 0;
$pagerz = 0;

isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action='';
isset($_REQUEST['extdisplay'])?$extdisplay=$_REQUEST['extdisplay']:$extdisplay='';
isset($_REQUEST['goto0'])?$goto = $_REQUEST['goto0']:$goto='';
isset($_REQUEST['account'])?$account = $_REQUEST['account']:$account='';
isset($_REQUEST['name'])?$name = $_REQUEST['name']:$name='';
isset($_REQUEST['grptime'])?$grptime = $_REQUEST['grptime']:$grptime='';
isset($_REQUEST['grppre'])?$grppre = $_REQUEST['grppre']:$grppre='';
isset($_REQUEST['callerannounce'])?$callerannounce = $_REQUEST['callerannounce']:$callerannounce='';
isset($_REQUEST['strategy'])?$strategy = $_REQUEST['strategy']:$strategy='';
isset($_REQUEST['alertinfo'])?$alertinfo = $_REQUEST['alertinfo']:$alertinfo='';
isset($_REQUEST['ringing'])?$ringing = $_REQUEST['ringing']:$ringing='';
isset($_REQUEST['description'])?$description = $_REQUEST['description']:$description='';

if (isset($_REQUEST["grplist"])) {
	$grplist = explode("\n",$_REQUEST["grplist"]);

	if (!$grplist) {
		$grplist = null;
	}

	foreach (array_keys($grplist) as $key) {

		$grplist[$key] = trim($grplist[$key]);
		$grplist[$key] = preg_replace("/[^0-9#*]/", "", $grplist[$key]);

		if ($grplist[$key] == "") unset($grplist[$key]);
	}

	$grplist = array_values(array_unique($grplist));
}

	if ($action == 'addGRP') {

		$errgroup = addgroup($account,implode("-",$grplist),$strategy,$grptime,$grppre,$goto,$callerannounce,$alertinfo,$ringing,$description);

			if ($errgroup != false) {

				exec($wScript1);
				needreload();
			}
	}

	if ($action == 'delGRP') {
		delextensions('ext-group',ltrim($extdisplay,'GRP-'));

		exec($wScript1);
		needreload();
	}

	if ($action == 'edtGRP') {

		delextensions('ext-group',$account);
		addgroup($account,implode("-",$grplist),$strategy,$grptime,$grppre,$goto,$callerannounce,$alertinfo,$ringing,$description);

		exec($wScript1);
		needreload();

	}
?>
</div>

<div class="rnav" style="width:190px;">
    <li><a id="<?php  echo ($extdisplay=='' ? 'current':'') ?>" href="config.php?mode=pbx&display=<?php echo urlencode($dispnum)?>" onFocus="this.blur()"><?php echo _("Add Ring Group")?></a></li>
<?php

$gresults = getgroups();

if (isset($gresults)) {

        foreach ($gresults AS $key=>$result) {
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
	
            		echo "<li><a id=\"".($extdisplay=='GRP-'.$result[0] ? 'current':'')."\" href=\"config.php?mode=pbx&display=".urlencode($dispnum)."&extdisplay=".urlencode("GRP-".$result[0])."&skip=$skip\" onFocus=\"this.blur()\">{$result[0]}:{$result[1]}</a></li>";
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

		if ($action == 'delGRP') {
			echo '<br><h3>Group '.ltrim($extdisplay,'GRP-').' deleted!</h3><br>';
		} else {


			if (!isset($grptime) || !isset($grppre) || !isset($grplist)) {
				if (!getgroupinfo(ltrim($extdisplay,'GRP-'), $strategy,  $grptime, $grppre, $grplist, $callerannounce, $alertinfo, $ringing, $description)) {

				}
			}

			if (!is_array($grplist)) {

				$grplist = explode("-",$grplist);
			}

			$delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&mode=pbx&action=delGRP';
	?>
			<h3><?php echo _("Ring Group")?>: <?php  echo ltrim($extdisplay,'GRP-'); ?></h3>
<?php 		if ($extdisplay){ ?>
			<p><a href="<?php  echo $delURL ?>" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete Group")?> <?php  echo ltrim($extdisplay,'GRP-'); ?></a></p>
<?php 		} ?>
			<form name="editGRP" action="<?php  $_SERVER['PHP_SELF'].'&mode=pbx' ?>" method="post">
			<input type="hidden" name="display" value="<?php echo $dispnum?>">
			<input type="hidden" name="action" value="">
			<table>
			<tr><td colspan="2"><h5><?php  echo ($extdisplay ? _("Edit Ring Group") : _("Add Ring Group")) ?></h5></td></tr>
			<tr>
<?php 		if ($extdisplay){ ?>

				<input size="5" type="hidden" name="account" value="<?php  echo ltrim($extdisplay,'GRP-'); ?>">
<?php 		} else { ?>
				<td><a href="#" class="info"><?php echo _("Group Number")?><span><?php echo _("The number users will dial to ring extensions in this ring group")?></span></a>:</td>
				<td><input size="5" type="text" name="account" value="<?php  echo $gresult[0] + 1; ?>"></td>
<?php 		} ?>
			</tr>
			<tr>
				<td><a href="#" class="info"><?php echo _("Ring Strategy")?><span>
					<b><?php echo _("ringall")?></b>:  <?php echo _("ring all available channels until one answers (default)")?><br>
					<b><?php echo _("hunt")?></b>: <?php echo _("take turns ringing each available extension")?><br>
					<b><?php echo _("memoryhunt")?></b>: <?php echo _("ring first extension in the list, then ring the 1st and 2nd extension, then ring 1st 2nd and 3rd extension in the list.... etc.")?><br>
				</span>
				</a>:</td>
				<td>&nbsp;&nbsp;<select name="strategy"/>
					<?php
						$default = (isset($strategy) ? $strategy : 'ringall');
						$items = array('ringall','hunt','memoryhunt');
						foreach ($items as $item) {
							echo '<option value="'.$item.'" '.($default == $item ? 'SELECTED' : '').'>'.$item."</option>\n";
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top"><a href="#" class="info"><?php echo _("Extension List")?><span><?php echo _("List extensions to ring, one per line.<br>You can include an extension on a remote system, or an external number by suffixing a number with a pound (#).  ex:  2448089# would dial 2448089 on the appropriate trunk (see Outbound Routing).")?><br></span></a>:</td>
				<td valign="top">&nbsp;
<?php
		$rows = count($grplist)+1;
		($rows < 5) ? 5 : (($rows > 20) ? 20 : $rows);
		if (count($grplist) == 1) {	$rows = "4"; }
?>
					<textarea id="grplist" cols="20" rows="<?php  echo $rows ?>" name="grplist"><?php echo implode("\n",$grplist);?></textarea><br>

<?              
if (ae_detect_ie()) {

?>                              <input type="submit" width="160" style="width:160px" style="font-size:10px;" value="<?php echo _("Clean & Remove duplicates")?>" />
            
<? } else { ?>

                <input type="submit" width="186" style="width:186px" style="font-size:10px;" value="<?php echo _("Clean & Remove duplicates")?>" /><? } ?>
				</td>
			</tr>

			<tr><td colspan="2"><br><h5><?php echo _("Ring Group Options")?></h5></td></tr>

			<tr>
				<td><a href="#" class="info"><?php echo _("Description")?><span><?php echo _('Give this Ring Group a brief name to help you identify it')?></span></a>:</td>
				<td><input size="20" maxlength="20" type="text" name="description" value="<?php  echo $description ?>"></td>
			</tr>
			<tr>
				<td><a href="#" class="info"><?php echo _("CID name prefix")?><span><?php echo _('You can optionally prefix the Caller ID name when ringing extensions in this group. ie: If you prefix with "Sales:", a call from John Doe would display as "Sales:John Doe" on the extensions that ring.')?></span></a>:</td>
				<td><input size="25" type="text" name="grppre" value="<?php  echo $grppre ?>"></td>
			</tr>
			<tr>
				<td><a href="#" class="info"><?php echo _("Alert Info")?><span><?php echo _('ALERT_INFO can be used for distinctive ring with SIP devices.')?></span></a>:</td>
				<td><input type="text" name="alertinfo" size="25" value="<?php echo ($alertinfo)?$alertinfo:'' ?>"></td>
			</tr>
	<tr>
		<td><a href="#" class="info"><?php echo _("Caller Announcement")?><span><?php echo _("Announcement played to the caller prior to joining the group<br> Example: \"this call may be monitored for quality assurance purposes\".<br>To add additional recordings please use the \"System Recordings\" MENU to the left")?></span></a>:</td>
		<td>&nbsp;&nbsp;<select name="callerannounce"/>
			<?php
				$tresults = getsystemrecordings("/var/lib/asterisk/sounds/custom");
				$default = (isset($callerannounce) ? $callerannounce : None);
				echo '<option value="">'._("None");
				if (isset($tresults)) {
					foreach ($tresults as $tresult) {
						echo '<option value="'.$tresult.'" '.($tresult == $default ? 'SELECTED' : '').'>'.$tresult."</option>\n";
					}
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td><a href="#" class="info"><?php echo _("Play Music On Hold")?><span><?php echo _("If you select a Music on Hold class to play, instead of 'Ring', they will hear that instead of Ringing while they are waiting for someone to pick up.")?></span></a>:</td>
		<td>&nbsp;&nbsp;<select name="ringing"/>

<?php
				if ($ringing == "${DIAL_OPTIONS}") {
				echo '<option value="Ring" SELECTED>'._("Ring").'</option>\n';
				} else {
					echo '<option value="Ring">'._("Ring").'</option>\n';
				}

				if (strpos($ringing, '(default)') == true) {
				echo '<option value="default" SELECTED>'._("Default MOH").'</option>\n';
				} else {
					echo '<option value="default">'._("Default MOH").'</option>\n';
				}

				$tresults = getmusiccategory("/var/lib/asterisk/mohmp3");
				if (isset($tresults)) {
					foreach ($tresults as $tresult) {
						$searchvalue="$tresult";
						echo '<option value="'.$tresult.'" '.(strpos($ringing, 'm('.$tresult.')') == $searchvalue ? 'SELECTED' : '').'>'.$tresult.'</option>\n';
					}
				}


			?>
			</select>
		</td>
	</tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Ring Time:")?><span><?php echo _("The number of seconds an phone can ring before we consider it a timeout. Max 60 Seconds.")?></span></a></td>
        <td>&nbsp;
            <select name="grptime"/>
            <?php
				$default = ($grptime ? $grptime : 20);
				for ($i=1; $i <= 60; $i++) {
					echo '<option value="'.$i.'" '.($i == $default ? 'SELECTED' : '').'>'.timeString($i,true).'</option>';
				}
            ?>
            </select>
        </td>
    </tr>

			<tr><td colspan="2"><br><h5><?php echo _("Destination if no answer")?></h5></td></tr>

<?php
			$goto = getargs(ltrim($extdisplay,'GRP-'),2,'ext-group');
?>
			<tr><td colspan="2">

<?php			echo drawselects('editGRP',$goto,0,'fixINCOMING','','','','');?>

			</td></tr>

			<tr>
			<td colspan="2"><br><h6><input name="Submit" type="button" value="Submit Changes" onclick="checkGRP(editGRP, <?php  echo ($extdisplay ? "'edtGRP'" : "'addGRP'") ?>);"></h6></td>
			</tr>

			</table>
			</form>
<?php
		}
?>
