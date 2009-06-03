<?php
// Copyright (C) 2005-2008 SpheraIT
?>

<script language="JavaScript">

function deleteCheck(f2) {

    cancel = false;
    ok = true;

    if (confirm("Are you sure to delete this CTI User?"))
          return ! cancel;
    else
          return ! ok;
}

</script>

<?php

isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action='';
isset($_REQUEST['ctidisplay'])?$ctidisplay=$_REQUEST['ctidisplay']:$ctidisplay='';

$skip = $_REQUEST['skip'];
$perpage = $_REQUEST['perpage'];
if ($skip == "") $skip = 0;
if ($perpage == "") $perpage = 30;
$skipped = 0;
$index = 0;
$pagerz = 0;
$dispnum = 2;

switch ($action) {
    case "add":
        ctiuser_add($_REQUEST['username'],$_REQUEST['password'],$_REQUEST['trunk'],$_REQUEST['login'],$_REQUEST['permission']);
    break;
    case "delete":
        ctiuser_del($ctidisplay);
    break;
    case "edit":
        ctiuser_del($ctidisplay);
        ctiuser_add($_REQUEST['username'],$_REQUEST['password'],$_REQUEST['trunk'],$_REQUEST['login'],$_REQUEST['permission']);
    break;
}

?>

</div>

<div class="rnav" style="width:225px;">
    <li><a id="<?php echo ($ctidisplay=='' ? 'current':'') ?>" href="config.php?mode=tools&amp;display=<?php echo urlencode($dispnum)?>"><?php echo _("Add Cti User")?></a></li>
<?php

$results = ctiuser_list();

if (isset($results)) {

        foreach ($results AS $key=>$result) {
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

            $cidname = explode('"',$result[1]);

        echo "<li><a title=\"".$result['username']."\" id=\"".$result['user_id']."\" href=\"config.php?mode=tools&amp;display=".urlencode($dispnum)."&ctidisplay={$result['user_id']}&skip=$skip\" onFocus=\"this.blur()\">".($result['permission'] == 0 ? '<i>':'')."".(substr($result['username'],0,22))."".($result['permission'] == 0 ? '</i>':'')."</a></li>";

    }
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
    echo '<h3>'._("CTI User:").' '.$username.' '._("deleted").'!</h3><br><br><br><br><br><br><br><br>';
} else {
    if ($ctidisplay){
        $ThisCtiUser = ctiuser_get($ctidisplay);
#        echo var_export($ThisCtiUser,true);
       extract(ctiuser_format_out($ThisCtiUser));
    }

    $delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=delete';
?>


<?php       if ($ctidisplay){ ?>
    <h3><?php echo _("CTI User:")." ". ($username ? $username : 'New User') ?></h3>
    <p><a href="<?php echo $delURL ?>" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete Cti User.")?></a></p>
<?php       } else { ?>
    <h3><?php echo _("CTI User: New User"); ?></h3>
<?php       }
?>
    <form autocomplete="off" name="editCtiUser" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkConf(this);">
    <input type="hidden" name="display" value="<?php echo $dispnum?>">
    <input type="hidden" name="action" value="<?php echo ($ctidisplay ? 'edit' : 'add') ?>">
    <table>
    <tr><td colspan="2"><h5><?php echo ($ctidisplay ? _("Edit Cti User") : _("Add Cti User")) ?></h5></td></tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Username:")?><span><?php echo _("Username of the CTI User.")?></span></a></td>
        <td><input size="36" type="text" name="username" value="<?php echo (isset($username) ? $username : ''); ?>"></td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Login:")?><span><?php echo _("Login Name.")?></span></a></td>
        <td><input size="25" type="text" name="login" value="<?php echo (isset($login) ? $login : ''); ?>"></td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Password:")?><span><?php echo _("Password for the CTI User.")?></span></a></td>
        <td><input size="25" type="password" name="password" value="<?php echo (isset($password) ? $password : ''); ?>"></td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Trunk:")?><span><?php echo _("Enable it Only if your phone is connected over IAX2/ZAP Channels.")?></span></a></td>
        <td>&nbsp;&nbsp;<select name="trunk"><option value="-1"><?php echo _("No"); ?></option>
    <option <?php if ($trunk == "0") echo "SELECTED "?>value="0"><?php echo _("Yes"); ?></option></select></td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Cdr:")?><span><?php echo _("Enable it Only if you want to login into the CDR Analyzer.")?></span></a></td>
        <td>&nbsp;&nbsp;<select name="permission"><option value="-1"><?php echo _("No"); ?></option>
    <option <?php if ($permission == "0") echo "SELECTED "?>value="0"><?php echo _("Yes"); ?></option></select></td>
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
<!--

function checkConf()
{
    var errUsername = "<?php echo _('The Username cannot be empty.'); ?>";
    var errPassword = "<?php echo _('The Password cannot be empty.'); ?>";
    var errLogin = "<?php echo _('The Login cannot be empty.'); ?>";
    var theForm = document.editCtiUser;

    defaultEmptyOK = false;
    if (theForm.username.value.length == 0)
        return warnInvalid(theForm.username, errUsername);
    if (theForm.password.value.length == 0)
        return warnInvalid(theForm.password, errPassword);
    if (theForm.login.value.length == 0)
        return warnInvalid(theForm.login, errLogin);
    return true;
}

//-->
</script>
