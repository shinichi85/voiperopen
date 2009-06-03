<?php

//Copyright (C) 2005 SpheraIT

?>

<script language="JavaScript">

function deleteCheck(f2) {

	cancel = false;
	ok = true;

	if (confirm("Are you sure to delete this recording?"))
  		return ! cancel;
	else
  		return ! ok;
}

function cambiacolore(questoid, colore)
 {document.getElementById(questoid).style.background=colore;}

</script>

<?php

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$display=1;
$path_to_dir = "/var/spool/asterisk/monitor";
$skip = $_REQUEST['skip'];
$perpage = $_REQUEST['perpage'];
if ($skip == "") $skip = 0;
if ($perpage == "") $perpage = 25;
$skipped = 0;
$index = 0;

function build_list()
{
	global $path_to_dir;
	$handle=opendir($path_to_dir) ;
	$extensions = array('.*');
	
	foreach ($extensions as $value)
		$pattern .= "$value|";
	
	$length = strlen($pattern);
	$length -= 1;
	$pattern = substr($pattern,0,$length);
	
	$i = 0;
	while (($file = readdir($handle))!==false) 
	{
		if ($file != "." && $file != "..") 
		{ 
		
			if(eregi($pattern,$file))
			{
				$file_array[$i] = $file;
				$i++;		
			}
		} 
	
	}
	closedir($handle); 
	return $file_array;
	
}

function draw_list($file_array, $path_to_dir, $perpage, $skip, $skipped, $index) 
{

	if ($file_array) {
        	rsort($file_array);
        	reset($file_array);


        foreach ($file_array AS $key=>$thisfile) {
            if ($index >= $perpage) {
                $shownext= 1;
                break;
                }
            if ($skipped<$skip && $skip!= 0) {
                $skipped= $skipped + 1;
                continue;
                }
            $index= $index + 1;

			$pos = strpos(strrev ($thisfile), '^');

			print "<tr id=righetta$index onmouseover=cambiacolore('righetta$index','#FDF1D5') onmouseout=cambiacolore('righetta$index','#FFFFFF')><td>";
			print "<div style=\"text-align:right;border: 1px solid;padding:3px;\">";
			print "<font style=\"float:left;margin-left:5px;\" color=\"#000000\">".substr($thisfile,6,2)."/".substr($thisfile,4,2)."/".substr($thisfile,0,4)."&nbsp;&nbsp;".substr($thisfile,9,2).":"."".substr($thisfile,11,2).":"."".substr($thisfile,13,2)."&nbsp;&nbsp;".substr($thisfile,16,-$pos-2)."</font>";
			print "<a style=\"margin-right:5px;\" onFocus=\"this.blur()\" href=\"/private/admin/download_rec/". $thisfile ."\" target=\"_blank\">"._("Download")."</a>";
			print "<a style=\"margin-right:5px;\" onFocus=\"this.blur()\" href=\"".$_SERVER['SCRIPT_NAME']."?display=1&mode=file&skip=$skip&del=".$thisfile."\" onClick=\"return deleteCheck(this);\">"._("Delete")."</a>";
			print "</div>";
			print "</td></tr>";
		}
	} else {
			print "<tr><td><br><br><center><b>Audio Recording folder is Empty.</b></center></td></tr>";
	}

			print "<tr><td>&nbsp;</td></tr>";

	if ($skip) {
	    $prevskip= $skip - $perpage;
	    if ($prevskip<0) $prevskip= 0;
	    $prevtag_pre= "<tr><td><a onFocus=\"this.blur()\" href='?display=1&mode=file&skip=$prevskip'>Previous Page</a>";
	    print "$prevtag_pre";
	    }
    	if (isset($shownext)) {
    	    $nextskip= $skip + $index;
    	    if ($prevtag_pre) {	$prevtag .= " | ";	} else { $prevtag .= "<tr><td>"; }
    	    print "$prevtag <a onFocus=\"this.blur()\" href='?display=1&mode=file&skip=$nextskip'>Next Page</a></td></tr>";
    	    }
    	elseif ($skip) {
    	    print "$prevtag";
        }
}

?>

<h3><?php echo _("Download Recorded Files:")?></h3>

<?php
	$file_array = build_list();
	$numf = count($file_array);

	if ($_REQUEST['del']) {

			$rmcmd="rm -f \"".$path_to_dir."/".$_REQUEST['del']."\"";
			exec($rmcmd);
			$file_array = build_list();
	}

	print "<table width=99% border=0 cellspacing=0 cellpadding=0><tr><td>";
	print "<h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DATE&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HOURS&nbsp;&nbsp;&nbsp;&nbsp;EXTENSION & CALLERID</h5>";
	print "</td></tr>";
	
	$file_array = build_list();
	draw_list($file_array, $path_to_dir, $perpage, $skip, $skipped, $index);
	
	print "</table>";
?>