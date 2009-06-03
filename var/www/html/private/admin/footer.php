<?php /* $Id: footer.php,v 1.12 2005/04/06 02:48:14 rcourtna Exp $ */
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

	require_once('common/db_connect.php');
    require_once('common/php-asmanager.php');

	$sql = "SELECT value FROM admin WHERE variable = 'need_reload'";

	$need_reload = $db->getRow($sql);
	if(DB::IsError($need_reload)) {
		die($need_reload->getMessage());
	}
?>
<?php

if (isset($_REQUEST['clk_reload'])) {

	if (isset($amp_conf["POST_RELOAD"]))
	{
		echo "
            <style>
                .clsWait        { position: absolute; top:65px; width: 788px; text-align:center; border: 1px solid red; background-color:#f0d0d0; display: block; font-weight: bold }
                .clsWaitFinishOK{ position: absolute; top:65px; width: 788px; text-align:center; border: 1px solid blue; background-color:#d0d0f0; display: block; }
                .clsHidden      { display: none }
            </style>
		";
		echo "<div id='idWaitBanner' class='clsWait'> Please wait while applying configuration</div>";

		if (!isset($amp_conf["POST_RELOAD_DEBUG"]) ||
		    (($amp_conf["POST_RELOAD_DEBUG"]!="1") &&
		     ($amp_conf["POST_RELOAD_DEBUG"]!="true"))
		   )
			echo "<div style='display:none'>";

		echo "Executing post apply script <b>".$amp_conf["POST_RELOAD"]."</b><pre>";
		system( $amp_conf["POST_RELOAD"] );
		echo "</pre>";

		if (!isset($amp_conf["POST_RELOAD_DEBUG"]) ||
		    (($amp_conf["POST_RELOAD_DEBUG"]!="1") &&
		     ($amp_conf["POST_RELOAD_DEBUG"]!="true"))
		    )
			echo "</div><br>";

 		echo "
			<script>
				function hideWaitBanner()
				{
					document.getElementById('idWaitBanner').className = 'clsHidden';
				}

				document.getElementById('idWaitBanner').innerHTML = 'Configuration applied';
				document.getElementById('idWaitBanner').className = 'clsWaitFinishOK';
				setTimeout('hideWaitBanner()',3000);
			</script>
		";
	}

    $amp_conf = parse_amportal_conf("/etc/amportal.conf");
    $hosts =split(',',$amp_conf['MANAGERHOSTS']);
    foreach ($hosts as $host) {
        $astman = new AGI_AsteriskManager();
        if ($res = $astman->connect($host, $amp_conf["AMPMGRUSER"] , $amp_conf["AMPMGRPASS"])) {
            $astman->command("reload");
            $astman->disconnect();
        } else {
                echo "<h3>Cannot connect to Asterisk Manager $host with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]."</h3>This module requires access to the Asterisk Manager.  Please ensure Asterisk is running and access to the manager is available.</div>";
                exit;
        }
    }

	$wOpBounce = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'bounce_op.sh';
	exec($wOpBounce.'>/dev/null');

	$sql = "UPDATE admin SET value = 'false' WHERE variable = 'need_reload'";
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die($result->getMessage());
	}
	$need_reload[0] = 'false';
}

	if ($need_reload[0] == 'true') {
		if (isset($_REQUEST['display'])) {

	?>
	<div class="inyourface"><a href="<?php  echo $_SERVER["PHP_SELF"]?>?display=<?php  echo $_REQUEST['display'] ?>&mode=<?php  echo $_REQUEST['mode'] ?>&skip=<?php  echo $_REQUEST['skip'] ?>&clk_reload=true" onFocus="this.blur()"><?php echo _("You have made changes - when finished, click here to APPLY them") ?></a></div>
	<?php } else { ?>
	<div class="inyourface"><a href="<?php  echo $_SERVER["PHP_SELF"]?>?clk_reload=true"><?php echo _("You have made changes - when finished, click here to APPLY them") ?></a></div>
	<?php
		}
	}

?>
<span class="footer" style="text-align:center;"><a target="_blank" href="http://www.voiper.it">Voiper</a> Management Portal (VMP). Customized version of the project Open-Source <a target="_blank" href="http://sourceforge.net/projects/amportal">AMP</a><br><br></span>
</div>
</body>
</html>
