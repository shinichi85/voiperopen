<?php
// Copyright (C) 2004 Coalescent Systems Inc. (info@coalescentsystems.ca)
// Copyright (c) 2005 SpheraIT

?>

<script language="Javascript">

    function checkSearchExt(theForm) {

            theForm.submit();
    }

</script>

<?

$dispnum = 3;

$searchext = isset($_REQUEST['searchext'])?$_REQUEST['searchext']:'';

$skip = $_REQUEST['skip'];
$perpage = $_REQUEST['perpage'];
if ($skip == "") $skip = 0;
if ($perpage == "") $perpage = 30;
$skipped = 0;
$index = 0;
$pagerz= 0;

function getSip_sib($searchext) {
    global $db;
    sipexists();
    $sql = "SELECT id,data FROM sip WHERE keyword = 'callerid' AND data LIKE '%".$searchext."%' ORDER BY id";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    foreach ($results as $result) {
        $result[] = 'sip';
        $sip[] = $result;
    }
    return $sip;
}

function getIax_sib($searchext) {
    global $db;
    iaxexists();
    $sql = "SELECT id,data FROM iax WHERE keyword = 'callerid' AND data LIKE '%".$searchext."%' ORDER BY id";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    foreach ($results as $result) {
        $result[] = 'iax';
        $iax[] = $result;
    }
    return $iax;
}

function getZap_sib($searchext) {
    global $db;
    zapexists();
    $sql = "SELECT id,data FROM zap WHERE keyword = 'callerid' AND data LIKE '%".$searchext."%' ORDER BY id";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    foreach ($results as $result) {
        $result[] = 'zap';
        $zap[] = $result;
    }
    return $zap;
}


function getextens_sib($searchext) {
    $sip = getSip_sib($searchext);
    $iax = getIax_sib($searchext);
    $zap= getZap_sib($searchext);
    $results = array_merge((array) $sip,(array) $iax,(array) $zap);
    foreach($results as $result){
        if (checkRange($result[0])){
            $extens[] = array($result[0],$result[1],$result[2]);
        }
    }
    if (isset($extens)) sort($extens);
    return $extens;
}

$results = getextens_sib($searchext);
$counter = count($results);
?>
</div>

<div class="xnav" style="width:620px;">
<li><br><form name="searcher" action="config.php?mode=pbx&display=20" method="post">&nbsp;<?php echo _("Search extension")?>: <input size="23" type="text" name="searchext" value="<? echo $searchext ?>">&nbsp;<input name="Submit" type="button" value="Search" onclick="checkSearchExt(searcher)">&nbsp;<input value="Reset" name="clear" type="reset">&nbsp;<b>Found</b> <? echo $counter; ?> <b>Extensions</b></form><br></li>

<?php

if (isset($results)) {

        foreach ($results AS $result) {
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

        echo "<li><a href=\"config.php?mode=pbx&display=".$dispnum."&extdisplay={$result[0]}&skip=$skip\" onFocus=\"this.blur()\">&nbsp;{$result[1]} [".strtoupper($result[2])."]</a></li>";

    }
} else {

        echo "<li><center>No EXTENSIONS Found.</center></li>";

        }

if  ($pagerz == 1){

    print "<li><center><div class='paging'>";
}

    if ($skip) {

        $prevskip= $skip - $perpage;
        if ($prevskip<0) $prevskip= 0;
        $prevtag_pre= "<a onFocus='this.blur()' href='?mode=pbx&display=20&skip=$prevskip'>[PREVIOUS]</a>";
        print "$prevtag_pre";
        }
        if (isset($shownext)) {


            $nextskip= $skip + $index;
            if ($prevtag_pre) $prevtag .= " | ";
                        print "$prevtag <a onFocus='this.blur()' href='?mode=pbx&display=20&skip=$nextskip'>[NEXT]</a>";
            }

            print "</div></center></li>";
?>
</div>
