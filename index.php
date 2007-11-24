<?php
# Medialinks index page
# $Id: index.php,v 1.2 2007/11/24 09:49:13 nobu Exp $

include "../../mainfile.php";
include_once "functions.php";
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';

$keywords = new KeyWords();
$isadmin = is_object($xoopsUser)&&$xoopsUser->isAdmin($xoopsModule->getVar('mid'));

include XOOPS_ROOT_PATH."/header.php";

$xoopsOption['template_main'] = 'medialinks_index.html';

$keyid = isset($_GET['keyid'])?intval($_GET['keyid']):0;
$xoopsTpl->assign('keyid', $keyid);

$keypath = $keywords->keys_path($keyid, 0, true);
$xoopsTpl->assign('keypath', $keypath);
set_ml_breadcrumbs($keypath);
$xoopsTpl->assign(get_keyword_index($keyid, $isadmin));

$start = isset($_GET['start'])?intval($_GET['start']):0;
$max = $xoopsModuleConfig['max_rows'];
$verb = intval($GLOBALS['mlModuleConfig']['list_style']);
$media = ml_index_view($keyid?'weight, mid':'ctime DESC', 0, $keyid, $verb, $max, $start);
$xoopsTpl->assign('media', $media);
$n = $media['count'];
$nav = new XoopsPageNav($n, $max, $start, "start", "keyid=".$keyid);
if ($n>$max) $xoopsTpl->assign('pagenav', $nav->renderNav());

include XOOPS_ROOT_PATH."/footer.php";
exit;

function get_keyword_index($keyid, $isadmin=false) {
    global $xoopsDB, $xoopsModule, $xoopsUser, $keywords;
    $result = array();
    if ($keyid) {
	$key = $keywords->get($keyid);
	if (isset($key['child'])) {
	    $tree = $key['child'];
	    $leaf = true;		// check no more nest
	    foreach ($tree as $child) {
		if (isset($child['child'])) {
		    $leaf = false;
		    break;
		}
	    }
	    if ($leaf) $tree = array($key);
	} else {
	    $tree = array();
	}
    
	$result['xoops_pagetitle'] = htmlspecialchars($xoopsModule->getVar('name').' ['.$key['name'].']');
	$myts =& MyTextSanitizer::getInstance();
	$result['keyname'] = htmlspecialchars($key['name']);
	$result['keydesc'] = $myts->displayTarea($key['description']);
    } else {
	$result['keyname'] = _MD_CONTENTS_NEW;
	$tree = $keywords->getTree();
    }
    $index = array();
    foreach ($tree as $key) {
	$cid = $key['keyid'];
	$childs = array();
	$kids = array();
	if ($key['nodetype']!=1) $kids[] = $cid;
	if (isset($key['child'])) {
	    foreach ($key['child'] as $child) {
		if ($child['relay']) {
		    $relay = $keywords->get($child['relay']);
		    $name = $relay['name'];
		} else {
		    $name = $child['name'];
		}
		$childs[] = array('keyid'=> $kids[] = $child['keyid'],
				  'type'=>$child['nodetype']!=1,
				  'name'=>htmlspecialchars($name));
	    }
	}
	if ($isadmin) {
	    $res = $xoopsDB->query("SELECT keyref, count(midref) FROM ".RELAY.
				   ",".MAIN.
				   " WHERE keyref IN (".join(',',$kids).") AND midref=mid".
				   " AND status='N' GROUP BY keyref");
	} else {
	    $uid = is_object($xoopsUser)?$xoopsUser->getVar('uid'):0;
	    $res = $xoopsDB->query("SELECT keyref, count(midref) FROM ".RELAY.
				   ",".MAIN." LEFT JOIN ".ACLS." ON amid=mid AND auid=$uid".
				   " WHERE keyref IN (".join(',',$kids).") AND midref=mid".
				   " AND status='N' AND (nacl=0 OR auid>0) GROUP BY keyref");
	}
	$count=array();
	while (list($id, $n) = $xoopsDB->fetchRow($res)) {
	    $count[$id] = $n;
	}
	foreach ($childs as $k => $v) {
	    $id = $v['keyid'];
	    $childs[$k]['count']=isset($count[$id])?$count[$id]:($v['type']?0:'');
	}
	if (!empty($key['relay'])) {
	    $relay = $keywords->get($child['relay']);
	    $key['name'] = $relay['name'];
	}

	if (empty($key['relay'])) {
	    $name = $key['name'];
	} else {
	    $relay = $keywords->get($key['relay']);
	    $name = $relay['name'];
	}

	if ($key['nodetype']!=1) {
	    $name .= ' ('.(isset($count[$cid])?$count[$cid]:0).')';
	}
	$head = array('name'=>$name, 'keyid'=>$cid);
	$index[] = array('head'=>$head, 'childs'=>$childs);
    }
    $result['index'] = $index;
    return $result;
}

function get_contents_list($keyid, $isadmin=false) {
    global $xoopsDB, $xoopsModuleConfig, $xoopsUser;
    $res = $xoopsDB->query("SELECT midref FROM ".RELAY." WHERE keyref=".$keyid);
    $mids = array();
    while (list($mid) = $xoopsDB->fetchRow($res)) {
	$mids[] = $mid;
    }
    $mids = array_unique($mids);
    $start = isset($_GET['start'])?intval($_GET['start']):0;
    $max = $xoopsModuleConfig['max_rows'];
    $cond = " WHERE status='N'";
    $order= "weight, mid";
    $jtbl = MAIN;
    if (count($mids)) $cond .= " AND mid IN (".join(',', $mids).")";
    elseif ($keyid) $cond .= " AND 0";
    else $order = "ctime DESC";
    if (!$isadmin) {
	$uid = is_object($xoopsUser)?$xoopsUser->getVar('uid'):0;
	$jtbl .= " LEFT JOIN ".ACLS." ON amid=mid AND auid=".$uid;
	$cond .= " AND (nacl=0 OR auid>0)";
    }

    $res = $xoopsDB->query("SELECT count(mid) FROM $jtbl".$cond);
    list($n) = $xoopsDB->fetchRow($res);
    $res = $xoopsDB->query("SELECT mid,title,ctime FROM $jtbl $cond ORDER BY $order", $max, $start);

    $lists = array();
    while (list($mid, $title, $ctime) = $xoopsDB->fetchRow($res)) {
	$lists[] = array('mid'=>$mid,
			 'title'=> htmlspecialchars($title),
			 'date' => formatTimestamp($ctime, "m"));
    }
    return array('count'=>$n, 'lists'=>$lists);
}
?>