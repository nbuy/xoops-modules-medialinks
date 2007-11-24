<?php
# Medialinks - module search
# $Id: search.inc.php,v 1.3 2007/11/24 09:49:14 nobu Exp $

function medialinks_search($queryarray, $andor, $limit, $offset, $userid){
	global $xoopsDB, $xoopsUser;
	$myts =& MyTextSanitizer::getInstance();
	$uid = is_object($xoopsUser)?$xoopsUser->getVar('uid'):0;
	$sql = "SELECT mid,poster,title,description,ctime,style FROM ".
		$xoopsDB->prefix("medialinks")." LEFT JOIN ".$xoopsDB->prefix('medialinks_access')." ON amid=mid AND auid=$uid WHERE status='N' AND (nacl=0 OR auid>0 OR poster=$uid)";
	if ( $userid != 0 ) {
		$sql .= " AND poster=".$userid." ";
	} 
	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $querryarray is really an array
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		// search text, varchar, date type fields in display
		$res = $xoopsDB->query("SELECT name FROM ".$xoopsDB->prefix("medialinks_fields")." WHERE weight>0 AND (type LIKE 'varchar%' OR type='text' OR type='date')");
		$flds = "";
		while (list($name) = $xoopsDB->fetchRow($res)) {
			$flds .= ($flds?", ' ', ":"").$name;
		}
		$sql .= " AND (";
		for($i=0;$i<$count;$i++){
			if ($i) $sql .= " $andor ";
			$sql .= "CONCAT($flds) LIKE '%$queryarray[$i]%'";
		}
		$sql .= ") ";
	}
	$sql .= " ORDER BY ctime DESC";
	$result = $xoopsDB->query($sql,$limit,$offset);
	$ret = array();
	$i = 0;
 	while($myrow = $xoopsDB->fetchArray($result)){
		//$ret[$i]['image'] = "images/forum.gif";
		$ret[$i]['link'] = "detail.php?mid=".$myrow['mid']."";
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