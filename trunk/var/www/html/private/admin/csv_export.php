<?php
// Copyright (C) 2005-2008 SpheraIT
?>

<?php
$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$display= 4;
?>

</div>

<div class="rnav">
    <li><a onFocus="this.blur()" href="export_local_extensions.php"><?php echo _("Export Local Extensions")?></a></li>
    <li><a onFocus="this.blur()" href="export_phonebook.php"><?php echo _("Export Phonebook")?></a></li>
    <li><a onFocus="this.blur()" href="export_speeddial.php"><?php echo _("Export Speeddial")?></a></li>
</div>

<div class="content">

<h3><?php echo _("Csv Export")?></h3>


