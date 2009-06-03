<?php
// Copyright (C) 2005-2008 SpheraIT
require_once('./classes/phonebook/Datasource.php');
require_once('./classes/phonebook/PhoneEntry.php');
require_once('./classes/phonebook/PhoneEntryDAO.php');

$ds = new Datasource($amp_conf['PHONEBOOKDBHOST'],
                     $amp_conf['PHONEBOOKDBNAME'],
                     $amp_conf['PHONEBOOKDBUSER'],
                     $amp_conf['PHONEBOOKDBPASS']);
$dao = new PhoneEntryDAO();
//echo "<pre>"; print_r($dao->readAll($ds));
//echo "<pre>"; print_r($amp_conf);

?>

<script language="JavaScript">

function deleteCheck(f2) {

    cancel = false;
    ok = true;

    if (confirm("Are you sure to delete this phonebook entry?"))
          return ! cancel;
    else
          return ! ok;
}

</script>

<?php

isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action='';
isset($_REQUEST['phdisplay'])?$phdisplay=$_REQUEST['phdisplay']:$phdisplay='';

$skip = $_REQUEST['skip'];
$perpage = $_REQUEST['perpage'];
if ($skip == "") $skip = 0;
if ($perpage == "") $perpage = 30;
$skipped = 0;
$index = 0;
$pagerz = 0;
$dispnum = 6;

switch ($action) {
    case "add":
        $phonebookEntry = new PhoneEntry();
        $phonebookEntry->setNumber($_REQUEST['number']);//search only by number
        //search if number exist
        $phonebookEntrySearched = $dao->search($ds,$phonebookEntry);
        if (count($phonebookEntrySearched)>0) {
          $phoneduplicate = "This number is already in use!";
        } else {
          $phonebookEntry->setDescription($_REQUEST['description']);
          $dao->save($ds,$phonebookEntry);
        }
    break;
    case "delete":
        $phonebookEntry = new PhoneEntry();
        $phonebookEntry->setID($phdisplay);
        $dao->delete($ds,$phonebookEntry);
    break;
    case "edit":
        $phonebookEntry = new PhoneEntry();
        $phonebookEntry->setID($phdisplay);
        //how search if number exist?
        $phonebookEntry->setNumber($_REQUEST['number']);
        $phonebookEntry->setDescription($_REQUEST['description']);
        $dao->save($ds,$phonebookEntry);
    break;
}

?>

</div>

<div class="rnav" style="width:225px;">
    <li><a id="<?php echo ($phdisplay=='' ? 'current':'') ?>" href="config.php?mode=tools&amp;display=<?php echo urlencode($dispnum)?>"><?php echo _("Add phonebook")?></a></li>
<?php

$results = $dao->readAll($ds);

if (count($results)>0) {
        foreach ($results as $r) {
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

            echo "<li><a title=\"".$r->getDescription() ." (".$r->getNumber().")\" id=\"".$r->getID()."\" href=\"config.php?mode=tools&amp;display=".urlencode($dispnum)."&phdisplay={$r->getID()}&skip=$skip\" onFocus=\"this.blur()\">"."".(substr($r->getDescription(),0,30)).""."</a></li>";
        }//foreach
}

if  ($pagerz == 1){

    print "<li><center><div class='paging'>";
}

    if ($skip) {

        $prevskip= $skip - $perpage;
        if ($prevskip<0) $prevskip= 0;
        $prevtag_pre= "<a onFocus='this.blur()' href='?mode=tools&display=".$dispnum."&skip=$prevskip'>[PREVIOUS]</a>";
        print "$prevtag_pre";
        }
        if (isset($shownext)) {

            $nextskip= $skip + $index;
            if ($prevtag_pre) $prevtag .= " | ";
            print "$prevtag <a onFocus='this.blur()' href='?mode=tools&display=".$dispnum."&skip=$nextskip'>[NEXT]</a>";
            }

            print "</div></center></li>";
?>


</div>

<div class="content">
<?php
if ($action == 'delete') {
    echo '<h3>'._("Phonebook:").' '.$description.' '._("deleted").'!</h3><br><br><br><br><br><br><br><br>';
} else {
    if ($phdisplay){
        $phonebookEntry = $dao->read($ds,$phdisplay);
        $number = $phonebookEntry->getNumber();
        $description = $phonebookEntry->getDescription();
    }

    $delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=delete';
?>


<?php       if ($phdisplay){ ?>
    <h3><?php echo _("Phonebook:")." ". ($description ? $description : 'New entry') ?></h3>
    <p><a href="<?php echo $delURL ?>" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete phonebook entry.")?></a></p>
<?php       } else { ?>
    <h3><?php echo _("Phonebook: New entry"); ?></h3>
<?php       }
?>
    <form autocomplete="off" name="phonebook" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkConf(this);">
    <input type="hidden" name="display" value="<?php echo $dispnum?>">
    <input type="hidden" name="action" value="<?php echo ($phdisplay ? 'edit' : 'add') ?>">
    <table>
    <tr><td colspan="2"><h5><?php echo ($phdisplay ? _("Edit phonebook") : _("Add phonebook")) ?></h5></td></tr>
    <tr><td colspan="2"><font color='#FF0000'><?php echo ($phoneduplicate) ?></font></td></tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Number:")?><span><?php echo _("Phone number.")?></span></a></td>
        <td><input size="20" type="text" name="number" value="<?php echo (isset($number) ? $number : ''); ?>"></td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Description:")?><span><?php echo _("Entry description.")?></span></a></td>
        <td><input size="25" type="text" name="description" value="<?php echo (isset($description) ? $description : ''); ?>"></td>
    </tr>

    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
        </td>
    </tr>

    <tr>
        <td colspan="2"><br><h6><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>"></h6></td>
    </tr>
    </table>
    </form>
<?php
}
?>

<script language="javascript">

function checkConf()
{
    var errNumber = "<?php echo _('The number cannot be empty.'); ?>";
    var errDescription = "<?php echo _('The description cannot be empty.'); ?>";
    var theForm = document.phonebook;

    defaultEmptyOK = false;
    if (theForm.number.value.length == 0) {
        warnInvalid(theForm.number, errNumber);
        return false;
    }
    if (theForm.description.value.length == 0) {
        warnInvalid(theForm.description, errDescription);
        return false;
    }
    return true;
}


</script>
