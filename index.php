<?php
# Medialinks index page
# $Id: index.php,v 1.1 2006/07/12 16:27:25 nobu Exp $

include "../../mainfile.php";
include_once "functions.php";
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';

$keywords = new KeyWords();

include XOOPS_ROOT_PATH."/header.php";

$xoopsOption['template_main'] = 'medialinks_index.html';

$keyid = isset($_GET['keyid'])?intval($_GET['keyid']):0;
$xoopsTpl->assign('keyid', $keyid);
$index = array();

if($keyid) {
    $key = $keywords->get($keyid);
    $xoopsTpl->assign('keypath', $keywords->keys_path($keyid, 0, true));
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
    
    $xoopsTpl->assign('xoops_pagetitle', htmlspecialchars($xoopsModule->getVar('name').' ['.$key['name'].']'));
    $myts =& MyTextSanitizer::getInstance();
    $xoopsTpl->assign('keyname', htmlspecialchars($key['name']));
    $xoopsTpl->assign('keydesc', $myts->displayTarea($key['description']));
 } else {
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
    $res = $xoopsDB->query("SELECT keyref, count(midref) FROM ".RELAY.
			   ",".MAIN." WHERE keyref IN (".join(',',$kids).
			   ") AND midref=mid AND status='N' GROUP BY keyref");
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

$xoopsTpl->assign('index', $index);

if ($keyid) {
    $res = $xoopsDB->query("SELECT midref FROM ".RELAY." WHERE keyref=".$keyid);
    $mids = array();
    while (list($mid) = $xoopsDB->fetchRow($res)) {
	$mids[] = $mid;
    }
    $mids = array_unique($mids);
    if ($mids) {
	$start = isset($_GET['start'])?intval($_GET['start']):0;
	$max = $xoopsModuleConfig['max_rows'];
	$cond = " WHERE mid IN (".join(',', $mids).") AND status='N'";
	$res = $xoopsDB->query("SELECT count(mid) FROM ".MAIN.$cond);
	list($n) = $xoopsDB->fetchRow($res);
	$nav = new XoopsPageNav($n, $max, $start, "start", "keyid=".$keyid);
	if ($n>$max) $xoopsTpl->assign('pagenav', $nav->renderNav());
	$res = $xoopsDB->query("SELECT mid,title,ctime FROM ".MAIN.$cond." ORDER BY weight, mid");
	$n = $xoopsDB->getRowsNum($res);
    } else {
	$n = 0;
    }
    $xoopsTpl->assign('count', $n);

    $lists = array();
    while (list($mid, $title, $ctime) = $xoopsDB->fetchRow($res)) {
	$lists[] = array('mid'=>$mid,
			 'title'=> htmlspecialchars($title),
			 'date' => formatTimestamp($ctime, "m"));
    }
    $xoopsTpl->assign('lists', $lists);
}

include XOOPS_ROOT_PATH."/footer.php";
?>