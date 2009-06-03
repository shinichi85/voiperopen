<?php /* $Id: ivr_action.php,v 1.12 2005/07/13 15:12:32 gregmac Exp $ */
//Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
//Copyright (C) 2005-2006 SpheraIT
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

<?php

isset($_REQUEST['menu_id'])?$menu_id = $_REQUEST['menu_id']:$menu_id='';
isset($_REQUEST['ivr_action'])?$ivr_action = $_REQUEST['ivr_action']:$ivr_action='';
isset($_REQUEST['extensionopt'])?$extensionopt = $_REQUEST['extensionopt']:$extensionopt='';


$wScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_extensions_from_mysql.pl';
$path_to_dir = "/var/lib/asterisk/sounds/custom/";
$dept = str_replace(' ','_',$_SESSION["user"]->_deptname);

switch($ivr_action) {
    case 'delete':

        $delsql = "DELETE FROM extensions WHERE context = '$menu_id'";
        $delres = $db->query($delsql);
        if(DB::IsError($delres)) {
           die('oops: '.$delres->getMessage());
        }

        if ($_REQUEST['map_display'] != 'no') {
            exec($wScript);
            $sql = "UPDATE admin SET value = 'true' WHERE variable = 'need_reload'";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
                die('oops: '.$delres->getMessage());
            }
        }

            $rmcmd="rm -f \"".$path_to_dir."/".$menu_id."\".wav";
            exec($rmcmd);

    break;

    case 'deleteopt':

        $delsql = "DELETE FROM extensions WHERE context = '$menu_id' AND extension = '$extensionopt'";
        $delres = $db->query($delsql);
        if(DB::IsError($delres)) {
           die('oops: '.$delres->getMessage());
        }

        if ($_REQUEST['map_display'] != 'no') {
            exec($wScript);
            $sql = "UPDATE admin SET value = 'true' WHERE variable = 'need_reload'";
            $result = $db->query($sql);
            if(DB::IsError($result)) {
               die('oops: '.$delres->getMessage());
            }
        }


    break;

    case 'write':

    $sql = "SELECT * FROM globals";
    $globals = $db->getAll($sql);
    if(DB::IsError($globals)) {
       die('oops: '.$delres->getMessage());
    }

    foreach ($globals as $global) {
        ${trim($global[0])} = $global[1];
    }

        $context = $menu_id;
        $extension = 's';

            if ($_REQUEST['extlocal-context'] != 'disabled') {

                $aa[] = array($context,'include','1','ext-local','','','2');
                $aa[] = array($context,'include','2','app-messagecenter','','','2');

            }

            if ($_REQUEST['custom-speeddial'] != 'disabled') {

                $aa[] = array($context,'include','3','custom-speeddial','','','2');
                $aa[] = array($context,'include','4','ext-miscdests','','','2');

            }

            if ($FAX_RX != 'disabled') {

                $aa[] = array($context,'fax','1','Goto','ext-fax,in_fax,1','','0');

            }

            if ($DIRECTORY != 'disabled') {

                $aa[] = array($context,'include','5','app-directory','','','2');

            }

            if ($FAX_RX != 'disabled') {

                $aa[] = array($context,'h','1','Hangup','','','0');

            }

            if ($_REQUEST['extlocal-context'] != 'disabled') {

                $aa[] = array($context,'i','1','Playback','invalid','','0');

                    if ($DIRECTORY == 'disabled') {

                        $aa[] = array($context,'i','2','Goto','s,7','','0');

                            } elseif ($DIRECTORY != 'disabled') {

                                $aa[] = array($context,'i','2','Goto','s,8','','0');
                            }

            } else {

                $aa[] = array($context,'i','1','Playback','ss-noservice','','0');

                    if ($DIRECTORY == 'disabled') {

                        $aa[] = array($context,'i','2','Goto','s,7','','0');

                            } elseif ($DIRECTORY != 'disabled') {

                                $aa[] = array($context,'i','2','Goto','s,8','','0');
                            }
            }

        $i=1;

        $aa[] = array($context,$extension,sprintf('%02s',$i++),'GotoIf','$["${DIALSTATUS}" = ""]?3','','0');
        $aa[] = array($context,$extension,sprintf('%02s',$i++),'GotoIf','$["${DIALSTATUS}" = "ANSWER"]?4','','0');
        $aa[] = array($context,$extension,sprintf('%02s',$i++),'Answer','','','0');
        $aa[] = array($context,$extension,sprintf('%02s',$i++),'Wait','1','','0');
        $aa[] = array($context,$extension,sprintf('%02s',$i++),'Set','LOOPED=1','','0');

                    if ($_REQUEST['loopdestinationcontext'] == 'hangup') {

                        $aa[] = array($context,$extension,sprintf('%02s',$i++),'GotoIf','$[${LOOPED} > '.$_REQUEST['loopmenu'].']?hang,1',$_REQUEST['loopmenu'],'0');

                    } else {

                        $loopgoto = $_REQUEST;
                        extract($loopgoto);
                        $loopdestination=buildActualGoto($loopgoto,999);
                        $aa[] = array($context,$extension,sprintf('%02s',$i++),'GotoIf','$[${LOOPED} > '.$_REQUEST['loopmenu'].']?'.$loopdestination.':',$_REQUEST['loopmenu'],'0');


                    }

            if ($DIRECTORY != 'disabled') {

                $aa[] = array($context,$extension,sprintf('%02s',$i++),'Set','DIR-CONTEXT='.$_REQUEST['dircontext'],'','0');

            }

            if ($_REQUEST['custom-speeddial'] != 'disabled' or $_REQUEST['extlocal-context'] != 'disabled') {

                $aa[] = array($context,$extension,sprintf('%02s',$i++),'Set','TIMEOUT(digit)=3',$_REQUEST['mname'],'0');
                $aa[] = array($context,$extension,sprintf('%02s',$i++),'Set','TIMEOUT(response)=3','','0');

                } else {

                        $aa[] = array($context,$extension,sprintf('%02s',$i++),'Set','TIMEOUT(digit)=1',$_REQUEST['mname'],'0');
                        $aa[] = array($context,$extension,sprintf('%02s',$i++),'Set','TIMEOUT(response)=1','','0');
                }

        $aa[] = array($context,$extension,sprintf('%02s',$i++),'Background','custom/'.$context,$_REQUEST['notes'],'0');

        $aa[] = array($context,'t','1','Set','LOOPED=$[${LOOPED} + 1]','','0');
        $aa[] = array($context,'t','2','Goto','s,6','','0');


                    if ($_REQUEST['loopdestinationcontext'] == 'hangup') {

                        $aa[] = array($context,'hang','1','Playback','vm-goodbye','','0');
                        $aa[] = array($context,'hang','2','Hangup','','','0');

                    }

        $compiled = $db->prepare('INSERT INTO extensions (context, extension, priority, application, args, descr, flags) values (?,?,?,?,?,?,?)');
        $result = $db->executeMultiple($compiled,$aa);
        if(DB::IsError($result)) {
            die($result->getMessage().'<br>context='.$context);
        }

        $ivr_num_options = $_REQUEST['ivr_num_options'];

        for ($i = 0; $i < $ivr_num_options; $i++) {

            $extension = $_REQUEST['ivr_option'.$i];

            if($extension == 't') {
                $sql = "DELETE FROM extensions WHERE context = '".$context."' AND extension = 't'";
                $result = $db->query($sql);
                if(DB::IsError($result)) {
                   die('oops: '.$delres->getMessage());
                }
            }
            $goto = $_REQUEST['goto'.$i];
            setGoto($extension,$context,'1',$goto,$i);
        }

        exec($wScript);

        if (!is_dir('/var/lib/asterisk/sounds/custom')) {
            if (!mkdir('/var/lib/asterisk/sounds/custom',0775))
                echo 'could not create /var/lib/asterisk/sounds/custom';
        }
        if (!copy('/var/lib/asterisk/sounds/'.$_REQUEST['cidnum'].'ivrrecording.wav','/var/lib/asterisk/sounds/custom/'.$context.'.wav'))
            echo 'error: could not copy or rename the voice recording - please contact support';

    needreload();

    break;
}

if (!isset($_REQUEST['map_display']) || $_REQUEST['map_display'] != 'no')
    include 'ivrmap.php';
?>
