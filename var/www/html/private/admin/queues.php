<?php
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

<script language="JavaScript">

function deleteCheck(f2) {

    cancel = false;
    ok = true;

    if (confirm("Are you sure to delete this Queue?"))
          return ! cancel;
    else
          return ! ok;
}

</script>

<?php

$wScript1 = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_extensions_from_mysql.pl';
$wScript2 = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_queues_from_mysql.pl';
$wOpScript = rtrim($_SERVER['SCRIPT_FILENAME'],$currentFile).'retrieve_op_conf_from_mysql.pl';

$dispnum = 11;
$skip = $_REQUEST['skip'];
$perpage = $_REQUEST['perpage'];
if ($skip == "") $skip = 0;
if ($perpage == "") $perpage = 30;
$skipped = 0;
$index = 0;
$pagerz = 0;

isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action='';
isset($_REQUEST['extdisplay'])?$extdisplay=$_REQUEST['extdisplay']:$extdisplay='';
isset($_REQUEST['account'])?$account = $_REQUEST['account']:$account='';
isset($_REQUEST['name'])?$name = $_REQUEST['name']:$name='';
isset($_REQUEST['password'])?$password = $_REQUEST['password']:$password='';
isset($_REQUEST['agentannounce'])?$agentannounce = $_REQUEST['agentannounce']:$agentannounce='';
isset($_REQUEST['prefix'])?$prefix = $_REQUEST['prefix']:$prefix='';
isset($_REQUEST['callerannounce'])?$callerannounce = $_REQUEST['callerannounce']:$callerannounce='';
isset($_REQUEST['goto0'])?$goto = $_REQUEST['goto0']:$goto='';
$maxwait = isset($_REQUEST['maxwait'])?$_REQUEST['maxwait']:'';

if (isset($_REQUEST["members"])) {
        $members = explode("\n",$_REQUEST["members"]);

        if (!$members) {
            $members = null;
        }

        foreach (array_keys($members) as $key) {
            //trim it
            $members[$key] = trim($members[$key]);

            // check if an agent (starts with a or A)

            if (strtoupper(substr($members[$key],0,1)) == "A") {
                // remove invalid chars
                $members[$key] = "A".preg_replace("/[^0-9#\,*]/", "", $members[$key]);
                $agent = 1;
            } else {
                // remove invalid chars
                $members[$key] = preg_replace("/[^0-9#\,*]/", "", $members[$key]);
                $agent = 0;
            }

            $penalty_pos = strrpos($members[$key], ",");
            if ( $penalty_pos === false ) {
                    $penalty_val = 0;
            } else {
                    $penalty_val = substr($members[$key], $penalty_pos+1); // get penalty
                    $members[$key] = substr($members[$key],0,$penalty_pos); // clean up ext
                    $members[$key] = preg_replace("/[^0-9#*]/", "", $members[$key]); //clean out other ,'s
                    $penalty_val = preg_replace("/[^0-9*]/", "", $penalty_val); // get rid of #'s if there
                    $penalty_val = ($penalty_val == "") ? 0 : $penalty_val;
            }

            // remove blanks // prefix with the channel
            if (empty($members[$key]))
                unset($members[$key]);
            elseif ($agent) {
                $members[$key] = "Agent/".ltrim($members[$key],"aA").",".$penalty_val;
            } else {
                $members[$key] = "Local/".$members[$key]."@from-internal/n,".$penalty_val;
            }
        }

        // check for duplicates, and re-sequence
        // $members = array_values(array_unique($members));
    }


switch ($action) {
case "add":
            $errqueue = addqueue($account,$name,$password,$prefix,$goto,$agentannounce,$callerannounce,$members);

            if ($errqueue != false) {
                exec($wScript1);
                exec($wScript2);
                exec($wOpScript);
                needreload();
            }
break;
case "delete":
            delqueue($extdisplay);
            exec($wScript1);
            exec($wScript2);
            exec($wOpScript);
            needreload();
break;
case "edit":
            delqueue($account);
            $errqueue = addqueue($account,$name,$password,$prefix,$goto,$agentannounce,$callerannounce,$members);

            if ($errqueue != false) {
                exec($wScript1);
                exec($wScript2);
                exec($wOpScript);
            }
            needreload();
break;
}

$queues = getqueues();

?>
</div>

<div class="rnav" style="width:190px;">
    <li><a id="<?php echo ($extdisplay=='' ? 'current':'') ?>" href="config.php?mode=pbx&display=<?php echo $dispnum?>" onFocus="this.blur()"><?php echo _("Add Queue")?></a></li>
<?php
if (isset($queues)) {

        foreach ($queues AS $key=>$result) {
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
	
        echo "<li><a id=\"".($extdisplay==$result[0] ? 'current':'')."\" href=\"config.php?mode=pbx&display=".$dispnum."&extdisplay={$result[0]}&skip=$skip\" onFocus=\"this.blur()\">{$result[0]}:{$result[1]}</a></li>";
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
if ($action == 'delete') {
    echo '<br><h3>Queue '.$extdisplay.' Deleted!</h3>';
} else {
    $member = array();

    $thisQ = getqueueinfo($extdisplay);
    extract($thisQ);

    $delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&mode=pbx&action=delete';
?>

<?php if ($extdisplay != '') { ?>
    <h3><?php echo _("Queue:")." ". $extdisplay; ?></h3>
<?php } else { ?>
    <h3><?php echo _("Add Queue:"); ?></h3>
<?php } ?>

    <?php        if ($extdisplay){ ?>
    <p><a href="<?php echo $delURL ?>" onFocus="this.blur()" onClick="return deleteCheck(this);"><?php echo _("Delete Queue")?> <?php echo $extdisplay; ?></a></p>
<?php        } ?>
    <form autocomplete="off" name="editQ" action="<?php $_SERVER['PHP_SELF'].'&mode=pbx' ?>" method="post">
    <input type="hidden" name="display" value="<?php echo $dispnum?>">
    <input type="hidden" name="action" value="">
    <table>
    <tr><td colspan="2"><h5><?php echo ($extdisplay ? _("Edit Queue") : _("Add Queue")) ?></h5></td></tr>
    <tr>
<?php        if ($extdisplay){ ?>
        <input type="hidden" name="account" value="<?php echo $extdisplay; ?>">
<?php        } else { ?>
        <td><a href="#" class="info"><?php echo _("Queue number:")?><span><?php echo _("Use this number to dial into the queue, or transfer callers to this number to put them into the queue.<br><br>Agents will dial this queue number plus * to log onto the queue, and this queue number plus ** to log out of the queue.<br><br>For example, if the queue number is 123:<br><br><b>123* = log in<br>123** = log out</b>")?></span></a></td>
        <td><input size="20" type="text" name="account" value=""></td>
<?php        } ?>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Queue name:")?><span><?php echo _("Give this queue a brief name to help you identify it.")?></span></a></td>
        <td><input size="20" maxlength="20" type="text" name="name" value="<?php echo (isset($name) ? $name : ''); ?>"></td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Queue password:")?><span><?php echo _("You can require agents to enter a password before they can log in to this queue.<br><br>This setting is optional.")?></span></a></td>
        <td><input size="20" type="password" name="password" value="<?php echo (isset($password) ? $password : ''); ?>"></td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("CID name prefix:")?><span><?php echo _("You can optionally prefix the Caller ID name of callers to the queue. ie: If you prefix with \"Sales:\", a call from John Doe would display as \"Sales:John Doe\" on the extensions that ring.")?></span></a></td>
        <td><input size="20" type="text" name="prefix" value="<?php echo (isset($prefix) ? $prefix : ''); ?>"></td>
    </tr>
		<td><a href="#" class="info"><?php echo _("Alert Info")?><span><?php echo _("ALERT_INFO can be used for distinctive ring with SIP devices.")?></span></a>:</td>
		<td><input type="text" name="alertinfo" size="20" value="<?php echo (isset($alertinfo)?$alertinfo:'') ?>"></td>
	</tr>
    <tr>
        <td valign="top"><a href="#" class="info"><?php echo _("Static agents") ?>:<span><?php echo _("Static agents are extensions that are assumed to always be on the queue.  Static agents do not need to 'log in' to the queue, and cannot 'log out' of the queue.<br><br>List extensions to ring, one per line.<br><br>You can include an extension on a remote system, or an external number (Outbound Routing must contain a valid route for external numbers).<br><br>You can list agents defined in agents.conf by preceding the agent number with A, so agent 4002 would be listed as A4002. This is experimental and not supported. There are known issues, such as the inability for an agents.conf agent to do subsequent transfers to voicemail<br><br>In all cases, you can put a \",\" after the agent followed by a penalty value. Use penalties at your own risk, they are very broken in asterisk.)") ?><br></span></a></td>
        <td valign="top">&nbsp;
            <textarea id="members" cols="20" rows="<?php  $rows = count($member)+1; echo (($rows < 5) ? 5 : (($rows > 20) ? 20 : $rows) ); ?>" name="members"><?php foreach ($member as $mem) { $premem = ""; if (substr($mem,0,5) == "Agent") {$premem = "A";}; $mem = $premem.rtrim(ltrim(strstr($mem,"/"),"/"),"@from-internal");echo substr($mem,0,(strpos($mem,"@")!==false?strpos($mem,"@"):strpos($mem,","))).substr($mem,strrpos($mem, ","))."\n"; }?></textarea><br>


<?              
if (ae_detect_ie()) {

?>                              <input type="submit" width="160" style="width:160px" style="font-size:10px;" value="<?php echo _("Clean & Remove duplicates")?>" />
            
<? } else { ?>

                <input type="submit" width="186" style="width:186px" style="font-size:10px;" value="<?php echo _("Clean & Remove duplicates")?>" /><? } ?>



        </td>
    </tr>

    <tr><td colspan="2"><br><h5><?php echo _("Queue Options")?></h5></td></tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Agent Announcement:")?><span><?php echo _("Announcement played to the Agent prior to bridging in the caller <br><br> Example: \"the Following call is from the Sales Queue\" or \"This call is from the Technical Support Queue\".<br><br>To add additional recordings please use the \"System Recordings\" MENU to the left")?></span></a></td>
        <td>&nbsp;
            <select name="agentannounce"/>
            <?php
                $tresults = getsystemrecordings("/var/lib/asterisk/sounds/custom");
                $default = (isset($agentannounce) ? $agentannounce : 'None');
                echo '<option value="None">'._("None");
                if (isset($tresults)) {
                    foreach ($tresults as $tresult) {
                        echo '<option value="'.$tresult.'" '.($tresult == $default ? 'SELECTED' : '').'>'.$tresult.'</option>\n';
                    }
                }
            ?>

            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Hold Music Category:")?><span><?php echo _("Music (or Commercial) played to the caller while they wait in line for an available agent.<br><br>  This music is defined in the \"On Hold Music\" Menu to the left.")?></span></a></td>
        <td>&nbsp;
            <select name="music"/>
            <?php
                $tresults = getmusiccategory("/var/lib/asterisk/mohmp3");
                $default = (isset($music) ? $music : 'default');
                echo '<option value="default">'._("Default");
                if (isset($tresults)) {
                    foreach ($tresults as $tresult) {
                        $searchvalue="$tresult";
                        echo '<option value="'.$tresult.'" '.($searchvalue == $default ? 'SELECTED' : '').'>'.$tresult.'</option>\n';
                    }
                }
            ?>
            </select>
        </td>
    </tr>
<tr>
        <td><a href="#" class="info"><?php echo _("Ringing tone instead of MOH:")?><span><?php echo _("Enabling this option make callers hear a ringing tone instead of Music on Hold.<br/>If this option is enabled, settings of the previous drop down are ignored.")?></span></a></td>
        <td>
            <input name="rtone" type="checkbox" value="1" <?php echo (isset($rtone) && $rtone == 1 ? 'checked' : ''); ?> />
        </td>
</tr
    <tr>
        <td><a href="#" class="info"><?php echo _("Max wait time:")?><span><?php echo _("The maximum number of seconds a caller can wait in a queue before being pulled out.  (0 for unlimited).")?></span></a></td>
        <td>&nbsp;
            <select name="maxwait"/>
            <?php
				$default = (isset($maxwait) ? $maxwait : 0);
				for ($i=0; $i < 60; $i+=10) {
					if ($i == 0)
						echo '<option value="">'._("Unlimited").'</option>';
					else
						echo '<option value="'.$i.'"'.($i == $maxwait ? ' SELECTED' : '').'>'.timeString($i,true).'</option>';
				}
				for ($i=60; $i < 300; $i+=30) {
					echo '<option value="'.$i.'"'.($i == $maxwait ? ' SELECTED' : '').'>'.timeString($i,true).'</option>';
				}
				for ($i=300; $i < 1200; $i+=60) {
					echo '<option value="'.$i.'"'.($i == $maxwait ? ' SELECTED' : '').'>'.timeString($i,true).'</option>';
				}
				for ($i=1200; $i <= 3600; $i+=300) {
					echo '<option value="'.$i.'"'.($i == $maxwait ? ' SELECTED' : '').'>'.timeString($i,true).'</option>';
				}
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Max callers:")?><span><?php echo _("Maximum number of people waiting in the queue (0 for unlimited)")?></span></a></td>
        <td>&nbsp;
            <select name="maxlen"/>
            <?php
                $default = (isset($maxlen) ? $maxlen : 0);
                for ($i=0; $i <= 50; $i++) {
                    if ($i == 0)
                            echo '<option value="">'._("Unlimited").'</option>';
                                else
                                echo '<option value="'.$i.'" '.($i == $maxlen ? 'SELECTED' : '').'>'.$i.'</option>\n';
                }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Join empty:")?><span><?php echo _("If you wish to allow callers to join queues that currently to be joined, set this to yes")?></span></a></td>
        <td>&nbsp;
            <select name="joinempty"/>
            <?php
				$default = (isset($joinempty) ? $joinempty : 'yes');
				$items = array('yes'=>_("Yes"),'strict'=>_("Strict"),'no'=>_("No"));
				foreach ($items as $item=>$val) {
					echo '<option value="'.$item.'" '. ($default == $item ? 'SELECTED' : '').'>'.$val;
				}
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Leave when empty:")?><span><?php echo _("If you wish to remove callers from the queue if there are no agents present, set this to yes")?></span></a></td>
        <td>&nbsp;
            <select name="leavewhenempty"/>
            <?php
				$default = (isset($leavewhenempty) ? $leavewhenempty : 'no');
				$items = array('yes'=>_("Yes"),'strict'=>_("Strict"),'no'=>_("No"));
				foreach ($items as $item=>$val) {
					echo '<option value="'.$item.'" '. ($default == $item ? 'SELECTED' : '').'>'.$val;
				}
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <a href="#" class="info"><?php echo _("Ring strategy:")?>
                <span>
                    <b><?php echo _("ringall")?></b>:  <?php echo _("ring all available agents until one answers (default)")?><br>
                    <b><?php echo _("roundrobin")?></b>: <?php echo _("take turns ringing each available agent")?><br>
                    <b><?php echo _("leastrecent")?></b>: <?php echo _("ring agent which was least recently called by this queue")?><br>
                    <b><?php echo _("fewestcalls")?></b>: <?php echo _("ring the agent with fewest completed calls from this queue")?><br>
                    <b><?php echo _("random")?></b>: <?php echo _("ring random agent")?><br>
                    <b><?php echo _("rrmemory")?></b>: <?php echo _("round robin with memory, remember where we left off last ring pass")?><br>
                </span>
            </a>
        </td>
        <td>&nbsp;
            <select name="strategy"/>
            <?php
                $default = (isset($strategy) ? $strategy : 'ringall');
                $items = array('ringall','roundrobin','leastrecent','fewestcalls','random','rrmemory');
                foreach ($items as $item) {
                    echo '<option value="'.$item.'" '.($default == $item ? 'SELECTED' : '').'>'.$item.'</option>\n';
                }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Agent timeout:")?><span><?php echo _("The number of seconds an agent's phone can ring before we consider it a timeout. Unlimited or other timeout values may still be limited by system ringtime or individual extension defaults.")?></span></a></td>
        <td>&nbsp;
            <select name="timeout"/>
            <?php
				$default = (isset($timeout) ? $timeout : 15);
				echo '<option value="0" '.(0 == $default ? 'SELECTED' : '').'>'."Unlimited".'</option>';
				for ($i=1; $i <= 60; $i++) {
					echo '<option value="'.$i.'" '.($i == $default ? 'SELECTED' : '').'>'.timeString($i,true).'</option>\n';
				}
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Retry:")?><span><?php echo _("The number of seconds we wait before trying all the phones again")?></span></a></td>
        <td>&nbsp;
            <select name="retry"/>
            <?php
                $default = (isset($retry) ? $retry : 0);
                for ($i=0; $i <= 20; $i++) {
                    echo '<option value="'.$i.'" '.($i == $default ? 'SELECTED' : '').'>'.timeString($i,true).'</option>\n';
                }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Wrap-up-time:")?><span><?php echo _("After a successful call, how many seconds to wait before sending a potentially free member another call (default is 0, or no delay)")?></span></a></td>
        <td>&nbsp;
            <select name="wrapuptime"/>
            <?php
                $default = (isset($wrapuptime) ? $wrapuptime : 0);
                for ($i=0; $i <= 60; $i++) {
                    echo '<option value="'.$i.'" '.($i == $default ? 'SELECTED' : '').'>'.timeString($i,true).'</option>\n';
                }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Call recording:")?><span><?php echo _("Incoming calls to agents can be recorded. (saved to /var/spool/asterisk/monitor")?></span></a></td>
        <td>&nbsp;
            <select name="monitor-format"/>
            <?php
                $default = (empty($thisQ['monitor-format']) ? "no" : $thisQ['monitor-format']);
                echo '<option value="wav49" '.($default == "wav49" ? 'SELECTED' : '').'>'._("wav49").'</option>\n';
                echo '<option value="wav" '.($default == "wav" ? 'SELECTED' : '').'>'._("wav").'</option>\n';
                echo '<option value="gsm" '.($default == "gsm" ? 'SELECTED' : '').'>'._("gsm").'</option>\n';
                echo '<option value="" '.($default == "no" ? 'SELECTED' : '').'>'._("No").'</option>\n';
            ?>
            </select>
        </td>
    </tr>
       <tr>
           <td><a href="#" class="info"><?php echo _("Event when called:")?><span><?php echo _("When this option is set to YES, the following manager events will be generated: AgentCalled, AgentDump, AgentConnect and AgentComplete.")?></span></a></td>
           <td>&nbsp;
               <select name="eventwhencalled"/>
               <?php
                   $default = (isset($eventwhencalled) ? $eventwhencalled : 'no');
                   $items = array('yes'=>_("Yes"),'no'=>_("No"),'vars'=>_("Vars"));
                   foreach ($items as $item=>$val) {
                       echo '<option value="'.$item.'" '. ($default == $item ? 'SELECTED' : '').'>'.$val.'</option>\n';
                   }
               ?>
               </select>
           </td>
       </tr>
       <tr>
           <td><a href="#" class="info"><?php echo _("Member status off:")?><span><?php echo _("When if this is option is set to NO, the following manager event will be generated: QueueMemberStatus")?></span></a></td>
           <td>&nbsp;
               <select name="eventmemberstatusoff"/>
               <?php
                   $default = (isset($eventmemberstatusoff) ? $eventmemberstatusoff : 'yes');
                   $items = array('yes'=>_("Yes"),'no'=>_("No"));
                   foreach ($items as $item=>$val) {
                       echo '<option value="'.$item.'" '. ($default == $item ? 'SELECTED' : '').'>'.$val.'</option>\n';
                   }
               ?>
               </select>
           </td>
       </tr>
       <tr>
           <td><a href="#" class="info"><?php echo _("Report Hold Time:")?><span><?php echo _("If you wish to report the caller's hold time to the member before they are connected to the caller, set this to yes.")?></span></a></td>
           <td>&nbsp;
               <select name="reportholdtime"/>
               <?php
                   $default = (isset($reportholdtime) ? $reportholdtime : 'no');
                   $items = array('yes'=>_("Yes"),'no'=>_("No"));
                   foreach ($items as $item=>$val) {
                       echo '<option value="'.$item.'" '. ($default == $item ? 'SELECTED' : '').'>'.$val.'</option>\n';
                   }
               ?>
               </select>
           </td>
       </tr>

	<tr>
		<td><a href="#" class="info"><?php echo _("Skip Busy Agents:")?><span><?php echo _("When set to Yes, agents who are on an occupied phone will be skipped as if the line were returning busy. This means that Call Waiting or multi-line phones will not be presented with the call and in the various hunt style ring strategies, the next agent will be attempted.")?></span></a></td>
		<td>&nbsp;
			<select name="cwignore">
			<?php
				$default = (isset($cwignore) ? $cwignore : 'no');
				$items = array('1'=>_("Yes"),'0'=>_("No"));
				foreach ($items as $item=>$val) {
					echo '<option value="'.$item.'" '. ($default == $item ? 'SELECTED' : '').'>'.$val;
				}
			?>
			</select>
		</td>
	</tr>

	<tr>
		<td><a href="#" class="info"><?php echo _("Auto-Pause:")?><span><?php echo _("Autopause will pause a queue member if they fail to answer a call.")?></span></a></td>
		<td>&nbsp;
			<select name="autopause">
			<?php
				$default = (isset($autopause) ? $autopause : 'no');
				$items = array('yes'=>_("Yes"),'no'=>_("No"));
				foreach ($items as $item=>$val) {
					echo '<option value="'.$item.'" '. ($default == $item ? 'SELECTED' : '').'>'.$val;
				}
			?>
			</select>
		</td>
	</tr>

	<tr>
		<td><a href="#" class="info"><?php echo _("Set Interface var:")?><span><?php echo _("If set to yes, just prior to the caller being bridged with a queue member the MEMBERINTERFACE variable will be set with the interface name (eg. Agent/1234) of the queue member that was chosen and is now connected to be bridged with the caller.")?></span></a></td>
		<td>&nbsp;
			<select name="setinterfacevar">
			<?php
				$default = (isset($setinterfacevar) ? $setinterfacevar : 'no');
				$items = array('yes'=>_("Yes"),'no'=>_("No"));
				foreach ($items as $item=>$val) {
					echo '<option value="'.$item.'" '. ($default == $item ? 'SELECTED' : '').'>'.$val;
				}
			?>
			</select>
		</td>
	</tr>

	<tr>
		<td><a href="#" class="info"><?php echo _("AutoFill Behavior:")?><span><?php echo _("The old/current behavior of the queue has a serial type behavior in that the queue will make all waiting callers wait in the queue even if there is more than one available member ready to take calls until the head caller is connected with the member they were trying to get to. The next waiting caller in line then becomes the head caller, and they are then connected with the next available member and all available members and waiting callers waits while this happens. The new behavior, enabled by setting autofill=yes makes sure that when the waiting callers are connecting with available members in a parallel fashion until there are no more available members or no more waiting callers. This is probably more along the lines of how a queue should work and in most cases, you will want to enable this behavior. If you do not specify or comment out this option, it will default to no to keep backward compatibility with the old behavior.")?></span></a></td>
		<td>&nbsp;
			<select name="autofill">
			<?php
				$default = (isset($autofill) ? $autofill : 'yes');
				$items = array('yes'=>_("Yes"),'no'=>_("No"));
				foreach ($items as $item=>$val) {
					echo '<option value="'.$item.'" '. ($default == $item ? 'SELECTED' : '').'>'.$val;
				}
			?>
			</select>
		</td>
	</tr>

    <tr>
        <td><a href="#" class="info"><?php echo _("Service Level:")?><span><?php echo _("Settings for service level (default 0) Used for service level statistics (calls answered within service level time frame).")?></span></a></td>
        <td><input size="5" type="text" name="servicelevel" value="<?php echo (isset($servicelevel) ? $servicelevel : '0'); ?>"></td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Weight:")?><span><?php echo _("Weight of queue - when compared to other queues, higher weights get first shot at available channels when the same channel is included in more than one queue.")?></span></a></td>
        <td><input size="5" type="text" name="weight" value="<?php echo (isset($weight) ? $weight : '0'); ?>"></td>
    </tr>
    <tr><td colspan="2"><br><h5><?php echo _("Caller Announcements")?></h5></td></tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Caller Announcement:")?><span><?php echo _("Announcement played to the caller prior to joining the queue <br><br> Example: \"this call may be monitored for quality assurance purposes\".<br><br>To add additional recordings please use the \"System Recordings\" MENU to the left")?></span></a></td>
        <td>&nbsp;
            <select name="callerannounce"/>
            <?php
                $tresults = getsystemrecordings("/var/lib/asterisk/sounds/custom");
                $default = (isset($callerannounce) ? $callerannounce : 'None');
                echo '<option value="None">'._("None");
                if (isset($tresults)) {
                    foreach ($tresults as $tresult) {
                        echo '<option value="'.$tresult.'" '.($tresult == $default ? 'SELECTED' : '').'>'.$tresult.'</option>\n';
                    }
                }
            ?>
            </select>
        </td>
    </tr>

    <tr>
        <td><a href="#" class="info"><?php echo _("Frequency:")?><span><?php echo _("How often to announce queue position, estimated holdtime, and/or voice menu to the caller (0 to Disable Announcements).")?></span></a></td>
        <td>&nbsp;
            <select name="announcefreq"/>
            <?php
                $default = (isset($thisQ['announce-frequency']) ? $thisQ['announce-frequency'] : 0);
                for ($i=0; $i <= 1200; $i+=15) {
                    echo '<option value="'.$i.'" '.($i == $default ? 'SELECTED' : '').'>'.timeString($i,true).'</option>\n';
                }
            ?>
            </select>
        </td>
    </tr>

    <tr>
        <td><a href="#" class="info"><?php echo _("Periodic Announce Frequency:")?><span><?php echo _("How often to make any periodic announcement.")?></span></a></td>
        <td>&nbsp;
            <select name="periodicannouncefrequency"/>
            <?php
                $default = (isset($thisQ['periodic-announce-frequency']) ? $thisQ['periodic-announce-frequency'] : 0);
                for ($i=0; $i <= 120; $i+=10) {
                    echo '<option value="'.$i.'" '.($i == $default ? 'SELECTED' : '').'>'.timeString($i,true).'</option>\n';
                }
            ?>
            </select>
        </td>
    </tr>

    <tr>
        <td><a href="#" class="info"><?php echo _("Announce Round Seconds:")?><span><?php echo _("What's the rounding time for the seconds? If this is non-zero, then we announce the seconds as well as the minutes rounded to this value.")?></span></a></td>
        <td>&nbsp;
            <select name="announceroundseconds"/>
            <?php
                $default = (isset($thisQ['announce-round-seconds']) ? $thisQ['announce-round-seconds'] : 0);
                for ($i=0; $i <= 60; $i+=5) {
                    echo '<option value="'.$i.'" '.($i == $default ? 'SELECTED' : '').'>'.timeString($i,true).'</option>\n';
                }
            ?>
            </select>
        </td>
    </tr>

    <tr>
        <td><a href="#" class="info"><?php echo _("Announce Position:")?><span><?php echo _("Announce position of caller in the queue?")?></span></a></td>
        <td>&nbsp;
            <select name="announceposition"/>
            <?php
                $default = (isset($thisQ['announce-position']) ? $thisQ['announce-position'] : "no");
                    echo '<option value=yes '.($default == "yes" ? 'SELECTED' : '').'>'._("Yes").'</option>\n';
                    echo '<option value=no '.($default == "no" ? 'SELECTED' : '').'>'._("No").'</option>\n';
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Announce Hold Time:")?><span><?php echo _("Should we include estimated hold time in position announcements?  Either yes, no, or only once; hold time will not be announced if <1 minute")?> </span></a></td>
        <td>&nbsp;
            <select name="announceholdtime">
            <?php
                $default = (isset($thisQ['announce-holdtime']) ? $thisQ['announce-holdtime'] : "no");
                echo '<option value=yes '.($default == "yes" ? 'SELECTED' : '').'>'._("Yes").'</option>\n';
                echo '<option value=no '.($default == "no" ? 'SELECTED' : '').'>'._("No").'</option>\n';
                echo '<option value=once '.($default == "once" ? 'SELECTED' : '').'>'._("Once").'</option>\n';
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><a href="#" class="info"><?php echo _("Voice Menu:")?><span> <?php echo _("After announcing Position and/or Hold Time, you can optionally present an existing Digital Receptionist Voice Menu.<br><br>This voicemenu must only contain single-digit 'dialed options'.")?> </span></a></td>
        <td>&nbsp;
            <select name="announcemenu">
            <?php
            $default = (isset($announcemenu) ? $announcemenu : "none");

            echo '<option value=none '.($default == "none" ? 'SELECTED' : '').'>'._("None").'</option>\n';

            $unique_aas = getaas();

            if (isset($unique_aas)) {
                foreach ($unique_aas as $unique_aa) {
                    $menu_id = $unique_aa[0];
                    $menu_name = $unique_aa[1];
                    echo '<option value="'.$menu_id.'" '.(strpos($default,$menu_id) === false ? '' : 'SELECTED').'>'.($menu_name ? $menu_name : 'Menu ID'.$menu_id).'</option>\n';
                }
            }

            ?>
            </select>
        </td>
    </tr>

    <tr><td colspan="2"><br><h5><?php echo _("Fail Over Destination")?></h5></td></tr>

    <tr><td colspan="2"><?php echo drawselects('editQ',$goto,0,'fixINCOMING','','','','');?></td></tr>

    <tr>
        <td colspan="2"><br><h6><input name="Submit" type="button" value="<?php echo _("Submit Changes")?>" onclick="checkQ(editQ, <?php  echo ($extdisplay ? "'edit'" : "'add'") ?>);"></h6></td>

        </tr>
        </table>
    </form>
<?php
}
?>
