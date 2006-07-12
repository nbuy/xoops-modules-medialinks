<?php
# Medialinks view a page
# $Id: list.php,v 1.1 2006/07/12 16:27:25 nobu Exp $

include "../../mainfile.php";
include_once "functions.php";
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';

include XOOPS_ROOT_PATH."/header.php";
$xoopsOption['template_main'] = 'medialinks_list.html';

$content = new MediaContent();
$order = 'mtime DESC';
$cond = "status='n'";
if (isset($_GET['uid'])) {
    $cond .= ' AND poster='.intval($_GET['uid']);
}
if (!empty($_GET['keyid'])) {
    $order = 'weight,mid';
    $keyid = intval($_GET['keyid']);
    $xoopsTpl->assign('keypath', $content->keys_path(intval($_GET['keyid'])));

    $res = $xoopsDB->query("SELECT midref FROM ".$xoopsDB->prefix('medialinks_relation')." WHERE keyref=".$keyid);
    $mids = array();
    while (list($mid) = $xoopsDB->fetchRow($res)) {
	$mids[] = $mid;
    }
    $cond .= "AND mid IN (".join(',',$mids).")";
}
$res = $xoopsDB->query("SELECT count(mid) FROM ".$xoopsDB->prefix('medialinks')." WHERE $cond");
list($n) = $xoopsDB->fetchRow($res);
$start = isset($_GET['start'])?intval($_GET['start']):0;
$max = $xoopsModuleConfig['max_list'];
$res = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix('medialinks').
		       " WHERE $cond ORDER BY $order", $max, $start);
$nav = new XoopsPageNav($n, $max, $start, "start");
if ($n>$max) $xoopsTpl->assign('pagenav',$nav->renderNav());

$list = array();
while ($content->load(0, $res)) {
    $fields = $content->dispVars();
    $fields['detail'] = "detail.php?mid=".$content->getVar('mid');
    $list[] = $fields;
}
$xoopsTpl->assign('list', $list);

include XOOPS_ROOT_PATH."/footer.php";
?>