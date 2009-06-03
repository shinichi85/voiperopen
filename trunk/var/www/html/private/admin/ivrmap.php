<?php

//Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
//Copyright (C) 2005-2009 SpheraIT

?>

<script language="JavaScript">

function deleteCheck(f2) {

    cancel = false;
    ok = true;

    if (confirm("Are you sure to delete this ivr menu'?"))
          return ! cancel;
    else
          return ! ok;
}

function deleteCheck2(f2) {

    cancel = false;
    ok = true;

    if (confirm("Are you sure to delete this ivr option?"))
          return ! cancel;
    else
          return ! ok;
}

var imagepath = "images/";

function xswitch(listID) {
  if(listID.style.display=="none") {
    listID.style.display="";
  } else {
    listID.style.display="none";
  }
}
function icoswitch(bid) {
  icoID = document.getElementById('pic'+bid);
  if(icoID.src.indexOf("minus") != -1) {
    icoID.src = imagepath+"plus.gif";
  } else {
    icoID.src = imagepath+"minus.gif";
  }
}
function xyzswitch(bid) {
    xswitch(document.getElementById('pe'+bid));
    icoswitch(bid);
}


</script>

<?php

$explode_var = 0;

$unique_aas = getaas();
if (count($unique_aas) > 0) {
?>

    <h4><?php echo _("Voice Menu Map")?></h4>

<?php

    if (!isset($menu_num)) {
        $menu_num = 0;
    }

    foreach ($unique_aas as $unique_aa) {
        $menus[] = array($unique_aa[0],$unique_aa[1]);

        if (!empty($dept)) {
            $num = (int) substr(strrchr($unique_aa[0],"_"),1);
            if ($num > $menu_num) $menu_num = $num;
        }
        else if (substr($unique_aa[0],0,3) == 'aa_') {
            $num = (int) substr(strrchr($unique_aa[0],"_"),1);
            if ($num > $menu_num) $menu_num = $num;
        }
    }

    foreach ($menus as $menu)
    {
		$showIvrNumbers = sprintf('%02s',substr(strrchr($menu[0],"_"),1));

?>

<table width="99%" border="0" cellpadding="1" cellspacing="1">

    <ul>
            <IMG src="images/plus.gif" alt=[x] id=pic<? echo $explode_var; ?> style="CURSOR: pointer" title="Show/Hide" onclick="xyzswitch('<? echo $explode_var; ?>');">
        <li>
            <span style="float:right;text-align:right;">
                &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id=<?php  echo $menu[0] ?>&ivr_action=edit"><?php echo _("Modify this Menu")?></a><br>
                &bull; <a onFocus="this.blur()" href="download_recording/<?php  echo $menu[0] ?>.wav" target="_blank"><?php echo _("Download Audio")?></a>
                &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id=<?php  echo $menu[0] ?>&ivr_action=delete" onClick="return deleteCheck(this);"><?php echo _("Delete")?></a>
            </span>
            <?php echo _("Menu")?>: <b>[<?php echo $showIvrNumbers ?>]</b> <?php echo strtoupper($menu[1])?><br>
        </li>
    <ul>

<DIV id=pe<? echo $explode_var; ?> style="DISPLAY: none">
<br>
<?php

        $extlocal = "Disabled";
        $speeddial = "Disabled";
        $loopdestination = "Hangup";
        $aalines = aainfo($menu[0]);

        foreach ($aalines as $aaline) {
            $extension = $aaline[1];
            $application = $aaline[3];
            $args = explode(',',$aaline[4]);
            $argslen = count($args);

            if ($application == 'Macro' && $args[0] == 'exten-vm') {
                    echo '<li>'._("dialing").' '.$extension.' <b>'._("dials extension #").$args[2].'</b> &bull; <a onFocus="this.blur()" href="" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && $args[0] == 'ext-local') {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("dials extension #").$args[1].'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Macro' && $args[0] == 'vm') {
                    echo '<li>'._("dialing").' '.$extension.' <b>'._("sends to voicemail box #").$args[1].'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && !(strpos($args[0],'aa_') === false)) {
                    echo '<li>'._("dialing").' '.$extension.' <b>'._("goes to Menu").' ('.$args[0].')</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
                    $menu_request[] = $args[0];
            }
            elseif ($application == 'Goto' && !(strpos($args[0],'ext-group') === false)) {
                    echo '<li>'._("dialing").' '.$extension.' <b>'._("dials group #").$args[1].'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Background') {
                    $description = $aaline[5];
            }
            elseif ($application == 'GotoIf' && !(strpos($args[0],'LOOPED') === false)) {
                    $loopmenu = $aaline[5];


                    if ($application == 'GotoIf' && !(strpos($args[0],'ext-local') === false)) {
                        $loopdestination = 'dials extension #'.$args[1];
                    }

                    if ($application == 'GotoIf' && !(strpos($args[0],'ext-local') === false) && !(strpos($args[1],'VM_PREFIX') === false)) {
                        $loopdestination = 'sends to voicemail box #'.substr($args[1],12,10);
                    }

                    if ($application == 'GotoIf' && !(strpos($args[0],'ext-group') === false)) {
                        $loopdestination = 'dials group #'.$args[1];
                    }

                    if ($application == 'GotoIf' && !(strpos($args[0],'ext-queues') === false)) {
                        $loopdestination = 'goes to Queue #'.$args[1];
                    }

                    if ($application == 'GotoIf' && !(strpos($args[0],'ext-miscdests') === false)) {
                        $loopdestination = 'goes to Misc Dest #'.$args[1];
                    }

                    if ($application == 'GotoIf' && !(strpos($args[0],'outbound-allroutes') === false)) {
                        $loopdestination = 'dial a Custom Extension #'.$args[1];
                    }
                    if ($application == 'GotoIf' && !(strpos($args[0],'aa_') === false)) {
                        $loopdestination = 'goes to Menu ('.substr($args[0],17).')';
                    }

            }
            elseif ($extension == 'include' && $application == 'ext-local') {
                    $extlocal = "Enabled";
            }

            elseif ($extension == 'include' && $application == 'custom-speeddial') {
                    $speeddial = "Enabled";
            }

            elseif ($application == 'Goto' && !(strpos($args[0],'custom') === false)) {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("goes to").' '.$args[0].','.$args[1].','.$args[2].'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && !(strpos($args[0],'ext-queues') === false)) {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("goes to Queue #").$args[1].'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && $args[0] == 'native-fax') {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("goes to Native Fax").'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && $args[0] == 'from-pstn-timecheck') {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("goes to Incoming Calls #1").'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && $args[0] == 'from-pstn-timecheck_1') {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("goes to Incoming Calls #2").'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && $args[0] == 'from-pstn-timecheck_2') {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("goes to Incoming Calls #3").'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && $args[0] == 'from-pstn-timecheck_3') {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("goes to Incoming Calls #4").'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && $args[0] == 'from-pstn-timecheck_4') {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("goes to Incoming Calls #5").'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && $args[0] == 'from-pstn-timecheck_5') {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("goes to Incoming Calls #6").'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && $args[0] == 'outbound-allroutes') {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("dial a Custom Extension #").''.$args[1].'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && $args[0] == 'callbackext') {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("goes to Callback on Demand").'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && !(strpos($args[0],'ext-meetme') === false)) {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("goes to Conferences #").$args[1].'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
            elseif ($application == 'Goto' && !(strpos($args[0],'ext-miscdests') === false)) {
                echo '<li>'._("dialing").' '.$extension.' <b>'._("dial a Misc Destination #").$args[1].'</b> &bull; <a onFocus="this.blur()" href="config.php?mode=pbx&display=2&menu_id='.$menu[0].'&extensionopt='.$extension.'&ivr_action=deleteopt" onClick="return deleteCheck2(this);">Delete</a>';
            }
        }
?>
            <h5><?php echo _("Menu")?> [<?php echo $showIvrNumbers ?>]: <?php echo $menu[1]?> Special Options:</h5>
            <?php echo _("Menu notes:")?> <b><i><?php echo $description; ?></i></b><br>
            <?php echo _("Direct dial to Extension & Voicemail:")?> <b><i><?php echo $extlocal; ?></i></b><br>
            <?php echo _("Direct dial to Speeddial/Forward:")?> <b><i><?php echo $speeddial; ?></i></b><br>
            <?php echo _("Loop Timeout Goto:")?> <b><i><?php echo $loopdestination; ?></i></b><br>
            <?php echo _("Loop menu':")?> <b><i><?php echo $loopmenu; ?></i></b>

    <h6></h6>
    </DIV>
    </ul></ul>
</table>
<?php
    $explode_var++;
    }
?>
<table width="99%" border="0" cellpadding="1" cellspacing="1">
<?php
    echo '<ul><li>'._("Would you like to create another Menu?").'<ul><br><li><a href="config.php?mode=pbx&display=2&menu_id='.$dept.'aa_'.++$menu_num.'" onFocus="this.blur()">'._("Create a new Voice Menu").'</a></ul>';
?>
    </table>
<?php
} else {
    include 'ivr.php';
    }
?>
