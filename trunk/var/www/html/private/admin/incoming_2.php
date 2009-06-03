<?php

//Copyright (C) 2005-2006 SpheraIT
// http://www.programmingtalk.com/archive/index.php/t-14997.html

?>

<?php

    $giorni= array('mon' => 'lunedi',
                    'tue' => 'martedi',
                    'wed' => 'mercoledi',
                    'thu' => 'giovedi',
                    'fri' => 'venerdi',
                    'sat' => 'sabato',
                    'sun' => 'domenica');

    $mesi = array('jan' => 'gennaio',
                    'feb' => 'febbraio',
                    'mar' => 'marzo',
                    'apr' => 'aprile',
                    'may' => 'maggio',
                    'jun' => 'giugno',
                    'jul' => 'luglio',
                    'aug' => 'agosto',
                    'sep' => 'settembre',
                    'oct' => 'ottobre',
                    'nov' => 'novembre',
                    'dec' => 'dicembre');

function process_interval($ora_inizio, $minuti_inizio, $giorno_inizio, $mese_inizio, $ora_fine, $minuti_fine, $giorno_fine, $mese_fine, $ore_tutti, $giorni_tutti, $mesi_tutti) {
        //init variabili
        $day = array('mon','tue','wed','thu','fri','sat','sun');
        $month = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');

        //valido solo per null, andrebbe validato anche per valori consentiti

        if($ore_tutti != 1) {
            if($ora_inizio == NULL)    return NULL;
            if($minuti_inizio == NULL) return NULL;
            if($ora_fine == NULL) return NULL;
            if($minuti_fine == NULL) return NULL;
        }

        if($giorni_tutti != 1) {
            if($giorno_inizio == NULL) return NULL;
            if($giorno_fine == NULL) return NULL;
        }

        if($mesi_tutti != 1) {
            if($mese_inizio == NULL) return NULL;
            if($mese_fine == NULL)    return NULL;
        }

        //le ore devono essere nella stessa giornata

        if(($ora_inizio*60+$minuti_inizio) > ($ora_fine*60+$minuti_fine)) return NULL;

        //i giorni devono essere nella stessa settimana

        if(array_search($giorno_inizio, $day) > array_search($giorno_fine, $day)) return NULL;

        //i mesi devono essere nello stesso anno

        if(array_search($mese_inizio, $month) > array_search($mese_fine, $month)) return NULL;

        if($ore_tutti == 1) {
            $time_interval = '*';
        }

        else $time_interval = $ora_inizio.':'.$minuti_inizio.'-'.$ora_fine.':'.$minuti_fine;

        if($giorni_tutti == 1) {
            $day_interval = '*';
        }

        else $day_interval = $giorno_inizio.'-'.$giorno_fine;

        if($mesi_tutti == 1) {
            $month_interval = '*';
        }

        else $month_interval = $mese_inizio.'-'.$mese_fine;

        return array($time_interval, $day_interval, $month_interval);
    }


$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_extensions_from_mysql.pl';
$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';

$dispnum = 10;


if ($action == 'editglobals') {


for($i=0;$i<4;$i++) {
$interval[] = process_interval($_REQUEST['ora_inizio'][$i],
                            $_REQUEST['minuti_inizio'][$i],
                            $_REQUEST['giorno_inizio'][$i],
                            $_REQUEST['mese_inizio'][$i],
                            $_REQUEST['ora_fine'][$i],
                            $_REQUEST['minuti_fine'][$i],
                            $_REQUEST['giorno_fine'][$i],
                            $_REQUEST['mese_fine'][$i],
                            $_REQUEST['ore_tutti'][$i],
                            $_REQUEST['giorni_tutti'][$i],
                            $_REQUEST['mesi_tutti'][$i]);

}

$globalfields = array(array($_REQUEST['INCOMING'],'INCOMING_2'),
                        array($interval[0][0],'REGTIME_2'),
                        array($interval[0][1],'REGDAYS_2'),
                        array($interval[0][2],'REGMONTHS_2'),
                        array($interval[1][0],'REGTIME2_2'),
                        array($interval[1][1],'REGDAYS2_2'),
                        array($interval[1][2],'REGMONTHS2_2'),
                        array($interval[2][0],'REGTIME3_2'),
                        array($interval[2][1],'REGDAYS3_2'),
                        array($interval[2][2],'REGMONTHS3_2'),
                        array($interval[3][0],'REGTIME4_2'),
                        array($interval[3][1],'REGDAYS4_2'),
                        array($interval[3][2],'REGMONTHS4_2'),
                        array($_REQUEST['INCOMING_DESC'],'INCOMING_DESC_2'),
                        array($_REQUEST['AFTER_INCOMING'],'AFTER_INCOMING_2'),
                        array($_REQUEST['HOLIDAY_INCOMING'],'HOLIDAY_INCOMING_2'),
                        array($_REQUEST['IN_OVERRIDE'],'IN_OVERRIDE_2'));

    $compiled = $db->prepare('UPDATE globals SET value = ? WHERE variable = ?');
    $result = $db->executeMultiple($compiled,$globalfields);
    if(DB::IsError($result)) {
        echo $action.'<br>';
        die($result->getMessage());
    }

    exec($wScript);
    needreload();

}

$sql = "SELECT * FROM globals";
$globals = $db->getAll($sql);
if(DB::IsError($globals)) {
die($globals->getMessage());
}

foreach ($globals as $global) {
    ${trim($global[0])} = $global[1];
}

    $ReadForceIncomingHours = ReadForceIncomingHours(3);
    
    if ($ReadForceIncomingHours != "none") {
    
    $WarningMsg = "This Incoming Call is in conflict with the Special Estension *60.<br>Please don't use both at the same time. SUBMIT CHANGES is Disabled.";

    } else {
    
        $WarningMsg = "&nbsp;";
        
    }

$unique_aas = getaas();
$extens = getextens();
$gresults = getgroups();
$queues = getqueues();
$miscs = getmiscdest();

echo "<h3>"._("Incoming Calls <font class=\"contextcolor\">#3</font> (context: <font class=\"contextcolor\"> from-trunk-2</font>):")."</h3>";
?>
<table width="99%" border="0" cellpadding="1" cellspacing="2">
<form name="incoming" action="config.php?mode=pbx&amp;display=<?php echo urlencode($dispnum)?>" method="post">
<tr><td>
<center><h5><a href="config.php?display=9&mode=pbx">Incoming #1</a> - <a href="config.php?display=17&mode=pbx">Incoming #2</a> - <a href="config.php?display=10&mode=pbx">Incoming #3</a> - <a href="config.php?display=13&mode=pbx">Incoming #4</a> - <a href="config.php?display=15&mode=pbx">Incoming #5</a> - <a href="config.php?display=5&mode=pbx">Incoming #6</a></h5></center>
</td></tr>
<tr><td><b><center class="Warning"><?php echo $WarningMsg; ?></center></b></td></tr>
<tr><td>
<input type="hidden" name="display" value="<?php echo $dispnum?>"/>
<input type="hidden" name="action" value="editglobals"/>
<h5><?php echo _("Send Incoming Calls from the PSTN to:")?></h5>
</td></tr>

<tr><td bgcolor="#EEEEEE" align="right">
    <?php echo _("From:")?>
    &nbsp;&nbsp;
    <b><?php echo _("times")?></b>
    <select name="ora_inizio[]"><option></option>
<?php

        for($i=0;$i<24;$i++) {

            echo '<option value="'.$i.'" ';

            if(strlen($REGTIME_2) > 1) {
                $fixora = preg_split('/[:-]/',$REGTIME_2);
                if($fixora[0] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.$i."</option>";
        }
?>

    </select>:<select name="minuti_inizio[]"><option></option>
<?php

        for($i=0;$i<60;$i+=5) {

            echo '<option value="'.sprintf('%02s',$i).'" ';

            if(strlen($REGTIME_2) > 1) {
                $fixminuti = preg_split('/[:-]/',$REGTIME_2);
                if($fixminuti[1] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.sprintf('%02s',$i)."</option>";
        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("days")?></b>
    <select name="giorno_inizio[]"><option></option>

<?php
        foreach($giorni as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGDAYS_2,0,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";

        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("months")?></b>
    <select name="mese_inizio[]"><option></option>

<?php
        foreach($mesi as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGMONTHS_2,0,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";

        }
?>
    </select>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

</td></tr>

<tr><td bgcolor="#EEEEEE" align="right">
    <?php echo _("To:")?>
    &nbsp;&nbsp;
    <b><?php echo _("times")?></b>

    <select name="ora_fine[]"><option></option>

<?php

        for($i=0;$i<24;$i++) {

            echo '<option value="'.$i.'" ';

            if(strlen($REGTIME_2) > 1) {
                $fixora = preg_split('/[:-]/',$REGTIME_2);
                if($fixora[2] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.$i."</option>";
        }
?>

    </select>:<select name="minuti_fine[]"><option></option>

<?php

        for($i=0;$i<60;$i+=5) {

            echo '<option value="'.sprintf('%02s',$i).'" ';

            if(strlen($REGTIME_2) > 1) {
                $fixminuti = preg_split('/[:-]/',$REGTIME_2);
                if($fixminuti[3] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.sprintf('%02s',$i)."</option>";
        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("days")?></b>
    <select name="giorno_fine[]"><option></option>

<?php
        foreach($giorni as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGDAYS_2,4,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";
        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("months")?></b>
    <select name="mese_fine[]"><option></option>

<?php
        foreach($mesi as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGMONTHS_2,4,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";
        }
?>
    </select>

    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    </td></tr>

<tr><td bgcolor="#EEEEEE">
    <table width="99%" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td width="20%">&nbsp;</td>

<?php
    echo '<td width="25%">All Hours <input onFocus="this.blur()" type="checkbox" name="ore_tutti[0]" value="1" '.($REGTIME_2 == '*' ? 'CHECKED' : '').'>'."</td>";
    echo '<td width="25%">All Days <input onFocus="this.blur()" type="checkbox" name="giorni_tutti[0]" value="1" '.($REGDAYS_2 == '*' ? 'CHECKED' : '').'>'."</td>";
    echo '<td width="30%">All Months <input onFocus="this.blur()" type="checkbox" name="mesi_tutti[0]" value="1" '.($REGMONTHS_2 == '*' ? 'CHECKED' : '').'>'."</td>";
?>
    </tr>
    </table>
</td></tr>

<tr><td></td></tr>

<tr><td bgcolor="#DDDDDD" align="right">
    <?php echo _("From:")?>
    &nbsp;&nbsp;
    <b><?php echo _("times")?></b>
    <select name="ora_inizio[]"><option></option>
<?php

        for($i=0;$i<24;$i++) {

            echo '<option value="'.$i.'" ';

            if(strlen($REGTIME2_2) > 1) {
                $fixora = preg_split('/[:-]/',$REGTIME2_2);
                if($fixora[0] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.$i."</option>";
        }
?>
    </select>:<select name="minuti_inizio[]"><option></option>
<?php

        for($i=0;$i<60;$i+=5) {

            echo '<option value="'.sprintf('%02s',$i).'" ';

            if(strlen($REGTIME2_2) > 1) {
                $fixminuti = preg_split('/[:-]/',$REGTIME2_2);
                if($fixminuti[1] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.sprintf('%02s',$i)."</option>";
        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("days")?></b>
    <select name="giorno_inizio[]"><option></option>

<?php
        foreach($giorni as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGDAYS2_2,0,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";

        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("months")?></b>
    <select name="mese_inizio[]"><option></option>

<?php
        foreach($mesi as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGMONTHS2_2,0,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";

        }
?>
    </select>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

</td></tr>

<tr><td bgcolor="#DDDDDD" align="right">
    <?php echo _("To:")?>
    &nbsp;&nbsp;
    <b><?php echo _("times")?></b>

    <select name="ora_fine[]"><option></option>

<?php

        for($i=0;$i<24;$i++) {

            echo '<option value="'.$i.'" ';

            if(strlen($REGTIME2_2) > 1) {
                $fixora = preg_split('/[:-]/',$REGTIME2_2);
                if($fixora[2] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.$i."</option>";
        }
?>

    </select>:<select name="minuti_fine[]"><option></option>

<?php

        for($i=0;$i<60;$i+=5) {

            echo '<option value="'.sprintf('%02s',$i).'" ';

            if(strlen($REGTIME2_2) > 1) {
                $fixminuti = preg_split('/[:-]/',$REGTIME2_2);
                if($fixminuti[3] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.sprintf('%02s',$i)."</option>";
        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("days")?></b>
    <select name="giorno_fine[]"><option></option>

<?php
        foreach($giorni as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGDAYS2_2,4,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";
        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("months")?></b>
    <select name="mese_fine[]"><option></option>

<?php
        foreach($mesi as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGMONTHS2_2,4,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";
        }
?>
    </select>

    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    </td></tr>

<tr><td bgcolor="#DDDDDD">
    <table width="99%" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td width="20%">&nbsp;</td>

<?php
    echo '<td width="25%">All Hours <input onFocus="this.blur()" type="checkbox" name="ore_tutti[1]" value="1" '.($REGTIME2_2 == '*' ? 'CHECKED' : '').'>'."</td>";
    echo '<td width="25%">All Days <input onFocus="this.blur()" type="checkbox" name="giorni_tutti[1]" value="1" '.($REGDAYS2_2 == '*' ? 'CHECKED' : '').'>'."</td>";
    echo '<td width="30%">All Months <input onFocus="this.blur()" type="checkbox" name="mesi_tutti[1]" value="1" '.($REGMONTHS2_2 == '*' ? 'CHECKED' : '').'>'."</td>";
?>
    </tr>
    </table>
</td></tr>

<tr><td></td></tr>

<tr><td bgcolor="#EEEEEE" align="right">
    <?php echo _("From:")?>
    &nbsp;&nbsp;
    <b><?php echo _("times")?></b>
    <select name="ora_inizio[]"><option></option>
<?php

        for($i=0;$i<24;$i++) {

            echo '<option value="'.$i.'" ';

            if(strlen($REGTIME3_2) > 1) {
                $fixora = preg_split('/[:-]/',$REGTIME3_2);
                if($fixora[0] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.$i."</option>";
        }
?>

    </select>:<select name="minuti_inizio[]"><option></option>
<?php

        for($i=0;$i<60;$i+=5) {

            echo '<option value="'.sprintf('%02s',$i).'" ';

            if(strlen($REGTIME3_2) > 1) {
                $fixminuti = preg_split('/[:-]/',$REGTIME3_2);
                if($fixminuti[1] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.sprintf('%02s',$i)."</option>";
        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("days")?></b>
    <select name="giorno_inizio[]"><option></option>

<?php
        foreach($giorni as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGDAYS3_2,0,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";

        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("months")?></b>
    <select name="mese_inizio[]"><option></option>

<?php
        foreach($mesi as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGMONTHS3_2,0,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";

        }
?>
    </select>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

</td></tr>

<tr><td bgcolor="#EEEEEE" align="right">
    <?php echo _("To:")?>
    &nbsp;&nbsp;
    <b><?php echo _("times")?></b>

    <select name="ora_fine[]"><option></option>

<?php

        for($i=0;$i<24;$i++) {

            echo '<option value="'.$i.'" ';

            if(strlen($REGTIME3_2) > 1) {
                $fixora = preg_split('/[:-]/',$REGTIME3_2);
                if($fixora[2] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.$i."</option>";
        }
?>

    </select>:<select name="minuti_fine[]"><option></option>

<?php

        for($i=0;$i<60;$i+=5) {

            echo '<option value="'.sprintf('%02s',$i).'" ';

            if(strlen($REGTIME3_2) > 1) {
                $fixminuti = preg_split('/[:-]/',$REGTIME3_2);
                if($fixminuti[3] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.sprintf('%02s',$i)."</option>";
        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("days")?></b>
    <select name="giorno_fine[]"><option></option>

<?php
        foreach($giorni as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGDAYS3_2,4,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";
        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("months")?></b>
    <select name="mese_fine[]"><option></option>

<?php
        foreach($mesi as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGMONTHS3_2,4,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";
        }
?>
    </select>

    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    </td></tr>

<tr><td bgcolor="#EEEEEE">
    <table width="99%" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td width="20%">&nbsp;</td>

<?php
    echo '<td width="25%">All Hours <input onFocus="this.blur()" type="checkbox" name="ore_tutti[2]" value="1" '.($REGTIME3_2 == '*' ? 'CHECKED' : '').'>'."</td>";
    echo '<td width="25%">All Days <input onFocus="this.blur()" type="checkbox" name="giorni_tutti[2]" value="1" '.($REGDAYS3_2 == '*' ? 'CHECKED' : '').'>'."</td>";
    echo '<td width="30%">All Months <input onFocus="this.blur()" type="checkbox" name="mesi_tutti[2]" value="1" '.($REGMONTHS3_2 == '*' ? 'CHECKED' : '').'>'."</td>";
?>
    </tr>
    </table>
</td></tr>

<tr><td></td></tr>

<tr><td bgcolor="#DDDDDD" align="right">
    <?php echo _("From:")?>
    &nbsp;&nbsp;
    <b><?php echo _("times")?></b>
    <select name="ora_inizio[]"><option></option>
<?php

        for($i=0;$i<24;$i++) {

            echo '<option value="'.$i.'" ';

            if(strlen($REGTIME4_2) > 1) {
                $fixora = preg_split('/[:-]/',$REGTIME4_2);
                if($fixora[0] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.$i."</option>";
        }
?>

    </select>:<select name="minuti_inizio[]"><option></option>
<?php

        for($i=0;$i<60;$i+=5) {

            echo '<option value="'.sprintf('%02s',$i).'" ';

            if(strlen($REGTIME4_2) > 1) {
                $fixminuti = preg_split('/[:-]/',$REGTIME4_2);
                if($fixminuti[1] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.sprintf('%02s',$i)."</option>";
        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("days")?></b>
    <select name="giorno_inizio[]"><option></option>

<?php
        foreach($giorni as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGDAYS4_2,0,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";

        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("months")?></b>
    <select name="mese_inizio[]"><option></option>

<?php
        foreach($mesi as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGMONTHS4_2,0,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";

        }
?>
    </select>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

</td></tr>

<tr><td bgcolor="#DDDDDD" align="right">
    <?php echo _("To:")?>
    &nbsp;&nbsp;
    <b><?php echo _("times")?></b>

    <select name="ora_fine[]"><option></option>

<?php

        for($i=0;$i<24;$i++) {

            echo '<option value="'.$i.'" ';

            if(strlen($REGTIME4_2) > 1) {
                $fixora = preg_split('/[:-]/',$REGTIME4_2);
                if($fixora[2] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.$i."</option>";
        }
?>

    </select>:<select name="minuti_fine[]"><option></option>

<?php

        for($i=0;$i<60;$i+=5) {

            echo '<option value="'.sprintf('%02s',$i).'" ';

            if(strlen($REGTIME4_2) > 1) {
                $fixminuti = preg_split('/[:-]/',$REGTIME4_2);
                if($fixminuti[3] == $i) {
                    echo 'SELECTED';
                }
            }

            echo '>'.sprintf('%02s',$i)."</option>";
        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("days")?></b>
    <select name="giorno_fine[]"><option></option>

<?php
        foreach($giorni as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGDAYS4_2,4,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";
        }
?>

    </select>
    &nbsp;&nbsp;&nbsp;
    <b><?php echo _("months")?></b>
    <select name="mese_fine[]"><option></option>

<?php
        foreach($mesi as $k => $v) {

            echo '<option value="'.$k.'" '.(substr($REGMONTHS4_2,4,3) == $k ? 'SELECTED' : '').'>'.$v."</option>";
        }
?>
    </select>

    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    </td></tr>

<tr><td bgcolor="#DDDDDD">
    <table width="99%" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td width="20%">&nbsp;</td>

<?php
    echo '<td width="25%">All Hours <input onFocus="this.blur()" type="checkbox" name="ore_tutti[3]" value="1" '.($REGTIME4_2 == '*' ? 'CHECKED' : '').'>'."</td>";
    echo '<td width="25%">All Days <input onFocus="this.blur()" type="checkbox" name="giorni_tutti[3]" value="1" '.($REGDAYS4_2 == '*' ? 'CHECKED' : '').'>'."</td>";
    echo '<td width="30%">All Months <input onFocus="this.blur()" type="checkbox" name="mesi_tutti[3]" value="1" '.($REGMONTHS4_2 == '*' ? 'CHECKED' : '').'>'."</td>";
?>
    </tr>
    </table>
</td></tr>

<tr><td>&nbsp;</td></tr>
</table>
<table width="99%" border="0" cellpadding="1" cellspacing="2">
<tr><td><h5><?php echo _("incoming call name:")?></h5></td></tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Description:")?><span><?php echo _("Give this Incoming Calls a brief name to help you identify it.")?></span></a>
        <input type="text" name="INCOMING_DESC" size="20" maxlength="20" value="<?php echo (isset($INCOMING_DESC_2) ? htmlentities($INCOMING_DESC_2) : ''); ?>"></td>
    </tr>
<tr><td>&nbsp;</td></tr>
</table>
<table width="99%" border="0" cellpadding="1" cellspacing="2">
<tr><td><h5><?php echo _("regular hours:")?></h5><IMG src="images/plus.gif" alt=[x] id=pic1 style="CURSOR: pointer" title="Show/Hide" onclick="incoming_div_switch(1)"></td></tr>
</table>
<DIV id=pe1 style="DISPLAY: none;">
<table width="99%" border="0" cellpadding="1" cellspacing="2">
<tr><td>
    <input type="radio" onFocus="this.blur()" name="in_indicate" value="ivr" onclick="javascript:document.incoming.INCOMING.value=document.incoming.INCOMING_IVR.options[document.incoming.INCOMING_IVR.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.INCOMING.value=document.incoming.INCOMING_IVR.options[document.incoming.INCOMING_IVR.options.selectedIndex].value;" <?php  echo strpos($INCOMING_2,'aa_') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Digital Receptionist:")?>
    <input type="hidden" name="INCOMING" value="<?php  echo $INCOMING_2; ?>">
    <select name="INCOMING_IVR" onchange="javascript:if (document.incoming.in_indicate[0].checked) document.incoming.INCOMING.value=document.incoming.INCOMING_IVR.options[document.incoming.INCOMING_IVR.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.in_indicate[0].checked) document.incoming.INCOMING.value=document.incoming.INCOMING_IVR.options[document.incoming.INCOMING_IVR.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($unique_aas)) {
        foreach ($unique_aas as $unique_aa) {
            $menu_num = substr($unique_aa[0],3);
            $menu_name = $unique_aa[1];
            echo '<option value="aa_'.$menu_num.'" '.($INCOMING_2 == 'aa_'.$menu_num ? 'SELECTED' : '').'>'.($menu_name ? $menu_name : _("Menu #").$menu_num)."</option>\n";
        }
    }
?>
    </select></td></tr>
<tr><td>
    <input type="radio" onFocus="this.blur()" name="in_indicate" value="extension" onclick="javascript:document.incoming.INCOMING.value=document.incoming.INCOMING_EXTEN.options[document.incoming.INCOMING_EXTEN.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.INCOMING.value=document.incoming.INCOMING_EXTEN.options[document.incoming.INCOMING_EXTEN.options.selectedIndex].value;"  <?php  echo strpos($INCOMING_2,'EXT') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Extension:")?>
    <select name="INCOMING_EXTEN" onchange="javascript:if (document.incoming.in_indicate[1].checked) document.incoming.INCOMING.value=document.incoming.INCOMING_EXTEN.options[document.incoming.INCOMING_EXTEN.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.in_indicate[1].checked) document.incoming.INCOMING.value=document.incoming.INCOMING_EXTEN.options[document.incoming.INCOMING_EXTEN.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($extens)) {
        foreach ($extens as $exten) {
            echo '<option value="EXT-'.$exten[0].'" '.($INCOMING_2 == 'EXT-'.$exten[0] ? 'SELECTED' : '').'>'.$exten[1]."</option>\n";
        }
    }
?>
    </select></td></tr>
<tr><td>
    <input type="radio" onFocus="this.blur()" name="in_indicate" value="group" onclick="javascript:document.incoming.INCOMING.value=document.incoming.INCOMING_GRP.options[document.incoming.INCOMING_GRP.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.INCOMING.value=document.incoming.INCOMING_GRP.options[document.incoming.INCOMING_GRP.options.selectedIndex].value;" <?php  echo strpos($INCOMING_2,'GR') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Ring Group:")?>
    <select name="INCOMING_GRP" onchange="javascript:if (document.incoming.in_indicate[2].checked) document.incoming.INCOMING.value=document.incoming.INCOMING_GRP.options[document.incoming.INCOMING_GRP.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.in_indicate[2].checked) document.incoming.INCOMING.value=document.incoming.INCOMING_GRP.options[document.incoming.INCOMING_GRP.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($gresults)) {
        foreach ($gresults as $gresult) {
            echo '<option value="GRP-'.$gresult[0].'" '.($INCOMING_2 == 'GRP-'.$gresult[0] ? 'SELECTED' : '').'>'.$gresult[0].':'.$gresult[1]."</option>\n";
        }
    }
?>
    </select></td></tr>
<tr><td>
    <input type="radio" onFocus="this.blur()" name="in_indicate" value="queue" onclick="javascript:document.incoming.INCOMING.value=document.incoming.INCOMING_QUEUE.options[document.incoming.INCOMING_QUEUE.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.INCOMING.value=document.incoming.INCOMING_QUEUE.options[document.incoming.INCOMING_QUEUE.options.selectedIndex].value;" <?php  echo strpos($INCOMING_2,'QUE') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Queue:")?>
    <select name="INCOMING_QUEUE" onchange="javascript:if (document.incoming.in_indicate[3].checked) document.incoming.INCOMING.value=document.incoming.INCOMING_QUEUE.options[document.incoming.INCOMING_QUEUE.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.in_indicate[3].checked) document.incoming.INCOMING.value=document.incoming.INCOMING_QUEUE.options[document.incoming.INCOMING_QUEUE.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($queues)) {
        foreach ($queues as $queue) {
            echo '<option value="QUE-'.$queue[0].'" '.($INCOMING_2 == 'QUE-'.$queue[0] ? 'SELECTED' : '').'>'.$queue[0].':'.$queue[1]."</option>\n";
        }
    }
?>
    </select></td></tr>

<tr><td>
    <input type="radio" onFocus="this.blur()" name="in_indicate" value="miscdest" onclick="javascript:document.incoming.INCOMING.value=document.incoming.INCOMING_MISC.options[document.incoming.INCOMING_MISC.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.INCOMING.value=document.incoming.INCOMING_MISC.options[document.incoming.INCOMING_MISC.options.selectedIndex].value;" <?php  echo strpos($INCOMING_2,'MIS') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Misc Destinations:")?>
    <select name="INCOMING_MISC" onchange="javascript:if (document.incoming.in_indicate[4].checked) document.incoming.INCOMING.value=document.incoming.INCOMING_MISC.options[document.incoming.INCOMING_MISC.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.in_indicate[4].checked) document.incoming.INCOMING.value=document.incoming.INCOMING_MISC.options[document.incoming.INCOMING_MISC.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($miscs)) {
        foreach ($miscs as $misc) {
            echo '<option value="MIS-'.$misc[0].'" '.($INCOMING_2 == 'MIS-'.$misc[0] ? 'SELECTED' : '').'>'.$misc[0].':'.$misc[1]."</option>\n";
        }
    }
?>
    </select></td></tr>

<tr><td>
    <input type="radio" onFocus="this.blur()" name="in_indicate" value="dialext"<?php  echo strpos($INCOMING_2,'DIALEXT') === false ? '' : 'CHECKED=CHECKED';?>/> <a href="#" class="info">Custom number to Dial<span>You can include an extension on a remote system, or an external number (Outbound Routing must contain a valid route for external numbers)</span></a>:
    <input type="text" size="20" name="INCOMING_DIALEXT" onchange="javascript:if (document.incoming.in_indicate[5].checked) document.incoming.INCOMING.value='DIALEXT-'+[document.incoming.INCOMING_DIALEXT.value];"
    <?php
            echo 'value="'.(strpos($INCOMING_2,'DIALEXT') === false ? '' : substr($INCOMING_2,8)).'"/>';
    ?>

</td></tr>
<tr><td>&nbsp;</td></tr>
</table>
</DIV>
<table width="99%" border="0" cellpadding="1" cellspacing="2">
<tr><td><h5><?php echo _("after hours:")?></h5><IMG src="images/plus.gif" alt=[x] id=pic2 style="CURSOR: pointer" title="Show/Hide" onclick="incoming_div_switch(2)"></td></tr>
</table>
<DIV id=pe2 style="DISPLAY: none;">
<table width="99%" border="0" cellpadding="1" cellspacing="2">
<tr><td>
    <input type="radio" onFocus="this.blur()" name="after_in_indicate" value="ivr" onclick="javascript:document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_IVR.options[document.incoming.AFTER_INCOMING_IVR.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_IVR.options[document.incoming.AFTER_INCOMING_IVR.options.selectedIndex].value;" <?php  echo strpos($AFTER_INCOMING_2,'aa_') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Digital Receptionist:")?>
    <input type="hidden" name="AFTER_INCOMING" value="<?php  echo $AFTER_INCOMING_2; ?>">
    <select name="AFTER_INCOMING_IVR" onchange="javascript:if (document.incoming.after_in_indicate[0].checked) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_IVR.options[document.incoming.AFTER_INCOMING_IVR.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.after_in_indicate[0].checked) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_IVR.options[document.incoming.AFTER_INCOMING_IVR.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($unique_aas)) {
        foreach ($unique_aas as $unique_aa) {
            $menu_num = substr($unique_aa[0],3);
            $menu_name = $unique_aa[1];
            echo '<option value="aa_'.$menu_num.'" '.($AFTER_INCOMING_2 == 'aa_'.$menu_num ? 'SELECTED' : '').'>'.($menu_name ? $menu_name : _("Menu #").$menu_num)."</option>\n";
        }
    }
?>
    </select></td></tr>
<tr><td>
    <input type="radio" onFocus="this.blur()" name="after_in_indicate" value="extension" onclick="javascript:document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_EXTEN.options[document.incoming.AFTER_INCOMING_EXTEN.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_EXTEN.options[document.incoming.AFTER_INCOMING_EXTEN.options.selectedIndex].value;" <?php  echo strpos($AFTER_INCOMING_2,'EXT') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Extension:")?>
    <select name="AFTER_INCOMING_EXTEN" onchange="javascript:if (document.incoming.after_in_indicate[1].checked) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_EXTEN.options[document.incoming.AFTER_INCOMING_EXTEN.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.after_in_indicate[1].checked) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_EXTEN.options[document.incoming.AFTER_INCOMING_EXTEN.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($extens)) {
        foreach ($extens as $exten) {
            echo '<option value="EXT-'.$exten[0].'" '.($AFTER_INCOMING_2 == 'EXT-'.$exten[0] ? 'SELECTED' : '').'>'.$exten[1]."</option>\n";
        }
    }
?>
    </select></td></tr>
<tr><td>
    <input type="radio" onFocus="this.blur()" name="after_in_indicate" value="group" onclick="javascript:document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_GRP.options[document.incoming.AFTER_INCOMING_GRP.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_GRP.options[document.incoming.AFTER_INCOMING_GRP.options.selectedIndex].value;" <?php  echo strpos($AFTER_INCOMING_2,'GR') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Ring Group:")?>
    <select name="AFTER_INCOMING_GRP" onchange="javascript:if (document.incoming.after_in_indicate[2].checked) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_GRP.options[document.incoming.AFTER_INCOMING_GRP.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.after_in_indicate[2].checked) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_GRP.options[document.incoming.AFTER_INCOMING_GRP.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($gresults)) {
        foreach ($gresults as $gresult) {
            echo '<option value="GRP-'.$gresult[0].'" '.($AFTER_INCOMING_2 == 'GRP-'.$gresult[0] ? 'SELECTED' : '').'>'.$gresult[0].':'.$gresult[1]."</option>\n";
        }
    }
?>
    </select></td></tr>
<tr><td>
    <input type="radio" onFocus="this.blur()" name="after_in_indicate" value="queue" onclick="javascript:document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_QUEUE.options[document.incoming.AFTER_INCOMING_QUEUE.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_QUEUE.options[document.incoming.AFTER_INCOMING_QUEUE.options.selectedIndex].value;" <?php  echo strpos($AFTER_INCOMING_2,'QUE') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Queue:")?>
    <select name="AFTER_INCOMING_QUEUE" onchange="javascript:if (document.incoming.after_in_indicate[3].checked) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_QUEUE.options[document.incoming.AFTER_INCOMING_QUEUE.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.after_in_indicate[3].checked) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_QUEUE.options[document.incoming.AFTER_INCOMING_QUEUE.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($queues)) {
        foreach ($queues as $queue) {
            echo '<option value="QUE-'.$queue[0].'" '.($AFTER_INCOMING_2 == 'QUE-'.$queue[0] ? 'SELECTED' : '').'>'.$queue[0].':'.$queue[1]."</option>\n";
        }
    }
?>
    </select></td></tr>

<tr><td>
    <input type="radio" onFocus="this.blur()" name="after_in_indicate" value="miscdest" onclick="javascript:document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_MISC.options[document.incoming.AFTER_INCOMING_MISC.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_MISC.options[document.incoming.AFTER_INCOMING_MISC.options.selectedIndex].value;" <?php  echo strpos($AFTER_INCOMING_2,'MIS') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Misc Destinations:")?>
    <select name="AFTER_INCOMING_MISC" onchange="javascript:if (document.incoming.after_in_indicate[4].checked) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_MISC.options[document.incoming.AFTER_INCOMING_MISC.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.after_in_indicate[4].checked) document.incoming.AFTER_INCOMING.value=document.incoming.AFTER_INCOMING_MISC.options[document.incoming.AFTER_INCOMING_MISC.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($miscs)) {
        foreach ($miscs as $misc) {
            echo '<option value="MIS-'.$misc[0].'" '.($AFTER_INCOMING_2 == 'MIS-'.$misc[0] ? 'SELECTED' : '').'>'.$misc[0].':'.$misc[1]."</option>\n";
        }
    }
?>
    </select></td></tr>

<tr><td>
    <input type="radio" onFocus="this.blur()" name="after_in_indicate" value="dialext"<?php  echo strpos($AFTER_INCOMING_2,'DIALEXT') === false ? '' : 'CHECKED=CHECKED';?>/> <a href="#" class="info">Custom number to Dial<span>You can include an extension on a remote system, or an external number (Outbound Routing must contain a valid route for external numbers)</span></a>:
    <input type="text" size="20" name="AFTER_INCOMING_DIALEXT" onchange="javascript:if (document.incoming.after_in_indicate[5].checked) document.incoming.AFTER_INCOMING.value='DIALEXT-'+[document.incoming.AFTER_INCOMING_DIALEXT.value];"
    <?php
            echo 'value="'.(strpos($AFTER_INCOMING_2,'DIALEXT') === false ? '' : substr($AFTER_INCOMING_2,8)).'"/>';
    ?>

</td></tr>

<tr><td>
    <input type="radio" onFocus="this.blur()" name="after_in_indicate" value="dialfax" onclick="javascript:if (document.incoming.after_in_indicate[6].checked) document.incoming.AFTER_INCOMING.value=[document.incoming.AFTER_INCOMING_DIALFAX.value];"<?php  echo strpos($AFTER_INCOMING_2,'DIALFAX-666') === false ? '' : 'CHECKED=CHECKED';?>/> <a href="#" class="info">Dial native Fax<span>Dial into a fax machine (You MUST configure the email to fax settings in General Settings section)</span></a>
    <input type="hidden" name="AFTER_INCOMING_DIALFAX" value="DIALFAX-666"/>


</td></tr>
<tr><td>&nbsp;</td></tr>
</table>
</div>
<table width="99%" border="0" cellpadding="1" cellspacing="2">
<tr><td><h5><?php echo _("holiday:")?></h5><IMG src="images/plus.gif" alt=[x] id=pic3 style="CURSOR: pointer" title="Show/Hide" onclick="incoming_div_switch(3)"></td></tr>
</table>
<DIV id=pe3 style="DISPLAY: none;">
<table width="99%" border="0" cellpadding="1" cellspacing="2">
<tr><td>
    <input type="radio" onFocus="this.blur()" name="holiday_in_indicate" value="ivr" onclick="javascript:document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_IVR.options[document.incoming.HOLIDAY_INCOMING_IVR.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_IVR.options[document.incoming.HOLIDAY_INCOMING_IVR.options.selectedIndex].value;" <?php  echo strpos($HOLIDAY_INCOMING_1,'aa_') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Digital Receptionist:")?>
    <input type="hidden" name="HOLIDAY_INCOMING" value="<?php  echo $HOLIDAY_INCOMING_2; ?>">
    <select name="HOLIDAY_INCOMING_IVR" onchange="javascript:if (document.incoming.holiday_in_indicate[0].checked) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_IVR.options[document.incoming.HOLIDAY_INCOMING_IVR.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.holiday_in_indicate[0].checked) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_IVR.options[document.incoming.HOLIDAY_INCOMING_IVR.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($unique_aas)) {
        foreach ($unique_aas as $unique_aa) {
            $menu_num = substr($unique_aa[0],3);
            $menu_name = $unique_aa[1];
            echo '<option value="aa_'.$menu_num.'" '.($HOLIDAY_INCOMING_2 == 'aa_'.$menu_num ? 'SELECTED' : '').'>'.($menu_name ? $menu_name : _("Menu #").$menu_num)."</option>\n";
        }
    }
?>
    </select></td></tr>
<tr><td>
    <input type="radio" onFocus="this.blur()" name="holiday_in_indicate" value="extension" onclick="javascript:document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_EXTEN.options[document.incoming.HOLIDAY_INCOMING_EXTEN.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_EXTEN.options[document.incoming.HOLIDAY_INCOMING_EXTEN.options.selectedIndex].value;" <?php  echo strpos($HOLIDAY_INCOMING_2,'EXT') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Extension:")?>
    <select name="HOLIDAY_INCOMING_EXTEN" onchange="javascript:if (document.incoming.holiday_in_indicate[1].checked) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_EXTEN.options[document.incoming.HOLIDAY_INCOMING_EXTEN.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.holiday_in_indicate[1].checked) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_EXTEN.options[document.incoming.HOLIDAY_INCOMING_EXTEN.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($extens)) {
        foreach ($extens as $exten) {
            echo '<option value="EXT-'.$exten[0].'" '.($HOLIDAY_INCOMING_2 == 'EXT-'.$exten[0] ? 'SELECTED' : '').'>'.$exten[1]."</option>\n";
        }
    }
?>
    </select></td></tr>
<tr><td>
    <input type="radio" onFocus="this.blur()" name="holiday_in_indicate" value="group" onclick="javascript:document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_GRP.options[document.incoming.HOLIDAY_INCOMING_GRP.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_GRP.options[document.incoming.HOLIDAY_INCOMING_GRP.options.selectedIndex].value;" <?php  echo strpos($HOLIDAY_INCOMING_2,'GR') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Ring Group:")?>
    <select name="HOLIDAY_INCOMING_GRP" onchange="javascript:if (document.incoming.holiday_in_indicate[2].checked) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_GRP.options[document.incoming.HOLIDAY_INCOMING_GRP.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.holiday_in_indicate[2].checked) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_GRP.options[document.incoming.HOLIDAY_INCOMING_GRP.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($gresults)) {
        foreach ($gresults as $gresult) {
            echo '<option value="GRP-'.$gresult[0].'" '.($HOLIDAY_INCOMING_2 == 'GRP-'.$gresult[0] ? 'SELECTED' : '').'>'.$gresult[0].':'.$gresult[1]."</option>\n";
            }
    }
?>
    </select></td></tr>
<tr><td>
    <input type="radio" onFocus="this.blur()" name="holiday_in_indicate" value="queue" onclick="javascript:document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_QUEUE.options[document.incoming.HOLIDAY_INCOMING_QUEUE.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_QUEUE.options[document.incoming.HOLIDAY_INCOMING_QUEUE.options.selectedIndex].value;" <?php  echo strpos($HOLIDAY_INCOMING_2,'QUE') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Queue:")?>
    <select name="HOLIDAY_INCOMING_QUEUE" onchange="javascript:if (document.incoming.holiday_in_indicate[3].checked) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_QUEUE.options[document.incoming.HOLIDAY_INCOMING_QUEUE.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.holiday_in_indicate[3].checked) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_QUEUE.options[document.incoming.HOLIDAY_INCOMING_QUEUE.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($queues)) {
        foreach ($queues as $queue) {
            echo '<option value="QUE-'.$queue[0].'" '.($HOLIDAY_INCOMING_2 == 'QUE-'.$queue[0] ? 'SELECTED' : '').'>'.$queue[0].':'.$queue[1]."</option>\n";
        }
    }
?>
    </select></td></tr>

<tr><td>
    <input type="radio" onFocus="this.blur()" name="holiday_in_indicate" value="miscdest" onclick="javascript:document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_MISC.options[document.incoming.HOLIDAY_INCOMING_MISC.options.selectedIndex].value;" onkeypress="javascript:if (event.keyCode == 0 || (document.all && event.keyCode == 13)) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_MISC.options[document.incoming.HOLIDAY_INCOMING_MISC.options.selectedIndex].value;" <?php  echo strpos($HOLIDAY_INCOMING_2,'MIS') === false ? '' : 'CHECKED=CHECKED';?>/> <?php echo _("Misc Destinations:")?>
    <select name="HOLIDAY_INCOMING_MISC" onchange="javascript:if (document.incoming.holiday_in_indicate[4].checked) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_MISC.options[document.incoming.HOLIDAY_INCOMING_MISC.options.selectedIndex].value;" onkeypress="javascript:setTimeout('if (document.incoming.holiday_in_indicate[4].checked) document.incoming.HOLIDAY_INCOMING.value=document.incoming.HOLIDAY_INCOMING_MISC.options[document.incoming.HOLIDAY_INCOMING_MISC.options.selectedIndex].value;', 100);"/>
<?php
    if (isset($miscs)) {
        foreach ($miscs as $misc) {
            echo '<option value="MIS-'.$misc[0].'" '.($HOLIDAY_INCOMING_2 == 'MIS-'.$misc[0] ? 'SELECTED' : '').'>'.$misc[0].':'.$misc[1]."</option>\n";
        }
    }
?>
    </select></td></tr>

<tr><td>
    <input type="radio" onFocus="this.blur()" name="holiday_in_indicate" value="dialext"<?php  echo strpos($HOLIDAY_INCOMING_2,'DIALEXT') === false ? '' : 'CHECKED=CHECKED';?>/> <a href="#" class="info">Custom number to Dial<span>You can include an extension on a remote system, or an external number (Outbound Routing must contain a valid route for external numbers)</span></a>:
    <input type="text" size="20" name="HOLIDAY_INCOMING_DIALEXT" onchange="javascript:if (document.incoming.holiday_in_indicate[5].checked) document.incoming.HOLIDAY_INCOMING.value='DIALEXT-'+[document.incoming.HOLIDAY_INCOMING_DIALEXT.value];"
    <?php
            echo 'value="'.(strpos($HOLIDAY_INCOMING_2,'DIALEXT') === false ? '' : substr($HOLIDAY_INCOMING_2,8)).'"/>';
    ?>

</td></tr>

<tr><td>
    <input type="radio" onFocus="this.blur()" name="holiday_in_indicate" value="dialfax" onclick="javascript:if (document.incoming.holiday_in_indicate[6].checked) document.incoming.HOLIDAY_INCOMING.value=[document.incoming.HOLIDAY_INCOMING_DIALFAX.value];"<?php  echo strpos($HOLIDAY_INCOMING,'DIALFAX-666') === false ? '' : 'CHECKED=CHECKED';?>/> <a href="#" class="info">Dial native Fax<span>Dial into a fax machine (You MUST configure the email to fax settings in General Settings section)</span></a>
    <input type="hidden" name="HOLIDAY_INCOMING_DIALFAX" value="DIALFAX-666"/>


</td></tr>
<tr><td>&nbsp;</td></tr>
</table>
</div>
<table width="99%" border="0" cellpadding="1" cellspacing="2">
<tr><td>
<h5><?php echo _("Override Incoming Calls Settings")?></h5>
</td></tr>

<?
    if ($ReadForceIncomingHours != "none") {

        $IN_OVERRIDE_2 = $ReadForceIncomingHours;

    }
?>

<tr><td>
    <input type="radio" onFocus="this.blur()" name="IN_OVERRIDE" value="none" <?php  echo $IN_OVERRIDE_2 == 'none' ? 'CHECKED=CHECKED' : 'CHECKED' ?>> <?php echo _("No override (obey the above settings). Day & Night *60")?><br>
</td></tr>
<tr><td>
    <input type="radio" onFocus="this.blur()" name="IN_OVERRIDE" value="forcereghours"<?php  echo $IN_OVERRIDE_2 == 'forcereghours' ? 'CHECKED=CHECKED' : '' ?>> <a href="#" class="info"><?php echo _("Force regular hours")?><span><?php echo _("Select this box if you would like to force the above regular hours setting to always take effect.<br><br>  This is useful for occasions when your office needs to remain open after-hours. (ie: open late on Thursday, or open all day on Sunday). Day & Night *60")?></span></a><br>
</td></tr>
<tr><td>
    <input type="radio" onFocus="this.blur()" name="IN_OVERRIDE" value="forceafthours"<?php  echo $IN_OVERRIDE_2 == 'forceafthours' ? 'CHECKED=CHECKED' : '' ?>> <a href="#" class="info"><?php echo _("Force after hours")?><span><?php echo _("Select this box if you would like to force the above after hours setting to always take effect.<br><br>  This is useful for holidays that fall in the 'regular hours' range above (ie: a holiday Monday). Day & Night *60")?></span></a>
</td></tr>
<tr><td>
    <input type="radio" onFocus="this.blur()" name="IN_OVERRIDE" value="forceholiday"<?php  echo $IN_OVERRIDE_2 == 'forceholiday' ? 'CHECKED=CHECKED' : '' ?>> <a href="#" class="info"><?php echo _("Force holiday")?><span><?php echo _("Select this box if you would like to force the above after hours setting to always take effect.). Day & Night *60")?></span></a>
</td></tr>

    <tr><td>&nbsp;</td></tr>

<?
    if ($ReadForceIncomingHours != "none") {
?>
            <tr><td><h6><input name="Submit" type="button" value="<?php echo _("Submit Changes")?>" onclick="checkIncoming(incoming)" disabled></h6></td></tr>
<?
    } else {
?>
            <tr><td><h6><input name="Submit" type="button" value="<?php echo _("Submit Changes")?>" onclick="checkIncoming(incoming)"></h6></td></tr>
<?
    }
?>
</form>
</table>
