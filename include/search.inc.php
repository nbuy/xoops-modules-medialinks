<?php
# Medialinks - module search
# $Id: search.inc.php,v 1.1 2006/07/12 16:27:26 nobu Exp $

function medialinks_search($queryarray, $andor, $limit, $offset, $userid){
	global $xoopsDB;
	$myts =& MyTextSanitizer::getInstance();
	$opt = $desc?", summary":"";
	$sql = "SELECT mid,poster,title,description,ctime,style FROM ".$xoopsDB->prefix("medialinks")." WHERE status='N'";
	if ( $userid != 0 ) {
		$sql .= " AND poster=".$userid." ";
	} 
	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $querryarray is really an array
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql .= " AND ((description LIKE '%$queryarray[0]%' OR title LIKE '%$queryarray[0]%')";
		for($i=1;$i<$count;$i++){
			$sql .= " $andor ";
			$sql .= "(description LIKE '%$queryarray[$i]%' OR title LIKE '%$queryarray[$i]%')";
		}
		$sql .= ") ";
	}
	$sql .= " ORDER BY ctime DESC";
	$result = $xoopsDB->query($sql,$limit,$offset);
	$ret = array();
	$i = 0;
 	while($myrow = $xoopsDB->fetchArray($result)){
		//$ret[$i]['image'] = "images/forum.gif";
		$ret[$i]['link'] = "detail.php?eid=".$myrow['mid']."";
		$ret[$i]['title'] = htmlspecialchars($myrow['title']);
		$ret[$i]['time'] = $myrow['ctime'];
		$ret[$i]['uid'] = $myrow['poster'];
		switch ($myrow['style']) {
		case 'b': $html=1; $br=1; break;
		case 'h': $html=1; $br=0; break;
		case 'n': $html=0; $br=1; break;
		}
		$ret[$i]['description'] = $myts->displayTarea($myrow['description'], $html, 1, 1, 1, $br);
		$i++;
	}
	return $ret;
}
?>