<?php
session_start();
require_once('functions.php');

$amp_conf = parse_amportal_conf("/etc/amportal.conf");

require_once('common/db_connect.php');

sipexists();
iaxexists();
zapexists();
backuptableexists();

if (isset($_REQUEST['display'])) {
    $display=$_REQUEST['display'];
} else {
    $display='';
}

if (isset($_REQUEST['mode'])) {
    $mode=$_REQUEST['mode'];
} else {
    $mode='pbx';
}

include 'header.php';

$pbx_sections = array(
        9=>_("Incoming Calls"),
        3=>_("Extensions"),
        20=>_("Extensions Search"),
        21=>_("Extensions Auto"),
        4=>_("Ring Groups"),
        11=>_("Queues"),
        2=>_("Digital Receptionist"),
        6=>_("Trunks"),
        7=>_("Inbound Routing"),
        8=>_("Outbound Routing"),
        1=>_("On Hold Music"),
        12=>_("System Recordings"),
        22=>_("Conferences"),
        14=>_("Speeddial/Forward"),
        19=>_("Misc Destinations"),
        16=>_("Trunk Tone")
    );

$settings_sections = array(
        1=>_("General Settings"),
        2=>_("Sip Settings"),
        3=>_("Iax Settings"),
        6=>_("VM Settings"),
        7=>_("Feature Settings"),
        4=>_("Manager Settings"),
        5=>_("PHPAgi Settings"),
        8=>_("Cti Settings"),
        9=>_("BI4Data Settings")
    );

$file_sections = array(
        1=>_("Recorded Files"),
        2=>_("Fax Files")
    );

$tools_sections = array(
        1=>_("Backup & Restore"),
        2=>_("Cti Users"),
        6=>_("Phonebook"),
        3=>_("Csv Import"),
        4=>_("Csv Export"),
        5=>_("Asterisk Info")
    );

if ($mode == 'pbx' ) {

    echo "<table width=\"100%\" cellspacing='0' cellpadding='0'><tr><td>";

echo "<div class=\"nav\">";

foreach ($pbx_sections as $key=>$value) {

        echo "<li><a id=\"".(($display==$key) ? 'current':'')."\" href=\"config.php?display=".$key."&mode=".$mode."\" onFocus=\"this.blur()\">+ "._($value)."</a></li>";

}

echo "</div>";

}

if ($mode == 'settings' ) {

    echo "<table width=\"100%\" cellspacing='0' cellpadding='0'><tr><td>";

echo "<div class=\"nav\">";

foreach ($settings_sections as $key=>$value) {

        echo "<li><a id=\"".(($display==$key) ? 'current':'')."\" href=\"config.php?display=".$key."&mode=".$mode."\" onFocus=\"this.blur()\">+ "._($value)."</a></li>";

}

echo "</div>";

}

if ($mode == 'file' ) {

    echo "<table width=\"100%\" cellspacing='0' cellpadding='0'><tr><td>";

echo "<div class=\"nav\">";

foreach ($file_sections as $key=>$value) {

        echo "<li><a id=\"".(($display==$key) ? 'current':'')."\" href=\"config.php?display=".$key."&mode=".$mode."\" onFocus=\"this.blur()\">+ "._($value)."</a></li>";

}

echo "</div>";

}

if ($mode == 'tools' ) {

    echo "<table width=\"100%\" cellspacing='0' cellpadding='0'><tr><td>";

echo "<div class=\"nav\">";

foreach ($tools_sections as $key=>$value) {

        echo "<li><a id=\"".(($display==$key) ? 'current':'')."\" href=\"config.php?display=".$key."&mode=".$mode."\" onFocus=\"this.blur()\">+ "._($value)."</a></li>";

}

echo "</div>";

}

?>

<div class="content">

<?php

if ($mode == 'pbx' ) {

switch($display) {
    default:
        include 'extensions.php';
    break;
    case '1':
        include 'music.php';
    break;
    case '2':
        echo "<h3>"._("Digital Receptionist:")."</h3>";

        if ((empty($_REQUEST['menu_id'])) || ($_REQUEST['ivr_action'] == 'delete' or $_REQUEST['ivr_action'] == 'deleteopt'))
            include 'ivr_action.php';
        else
            include 'ivr.php';
    break;
    case '3':
        include 'extensions.php';
    break;
    case '4':
        include 'callgroups.php';
    break;
    case '6':
        include 'trunks.php';
    break;
    case '7':
        include 'did.php';
    break;
    case '8':
        include 'routing.php';
    break;
    case '9':
        include 'incoming.php';
    break;
    case '11':
        include 'queues.php';
    break;
    case '12':
        include 'recordings.php';
    break;
    case '14':
        include 'speeddial.php';
    break;
    case '16':
        include 'simultone.php';
    break;
    case '17':
        include 'incoming_1.php';
    break;
    case '20':
        include 'extensions_search.php';
    break;
    case '22':
        include 'conferences.php';
    break;
    case '21':
        include 'extensions_generator.php';
    break;
    case '19':
        include 'miscdests.php';
    break;
    case '10':
        include 'incoming_2.php';
    break;
    case '13':
        include 'incoming_3.php';
    break;
    case '15':
        include 'incoming_4.php';
    break;
    case '5':
        include 'incoming_5.php';
    break;
    }
}

if ($mode == 'settings' ) {

switch($display) {
    default:
        include 'general.php';
    break;
    case '1':
        include 'general.php';
    break;
    case '2':
        include 'natconfig.php';
    break;
    case '3':
        include 'iaxconfig.php';
    break;
    case '6':
        include 'vmconfig.php';
    break;
    case '7':
        include 'featureconfig.php';
    break;
    case '4':
        include 'manager.php';
    break;
    case '5':
        include 'phpagiconf.php';
    break;
    case '8':
        include 'ctisettings.php';
    break;
    case '9':
        include 'cdrpush.php';
    break;
    }
}

if ($mode == 'file' ) {

switch($display) {
    default:
        include 'download_record.php';
    break;
    case '1':
        include 'download_record.php';
    break;
    case '2':
        include 'download_fax.php';
    break;
    }
}

if ($mode == 'tools' ) {

switch($display) {
    default:
        include 'backup.php';
    break;
    case '1':
        include 'backup.php';
    break;
    case '2':
        include 'ctiusers.php';
    break;
    case '3':
        include 'csv_import.php';
    break;
    case '4':
        include 'csv_export.php';
    break;
    case '5':
        include 'asteriskinfo.php';
    break;
    case '6':
        include 'phonebook.php';
    break;
    }
}

?>

<br></div></td></tr></table>

<?php echo str_repeat("<br />", 2);?>
<?php include 'footer.php' ?>
