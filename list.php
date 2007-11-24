<?php
# Medialinks view a page
# $Id: list.php,v 1.2 2007/11/24 09:49:13 nobu Exp $

include "../../mainfile.php";
include_once "functions.php";
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';

include XOOPS_ROOT_PATH."/header.php";
$xoopsOption['template_main'] = 'medialinks_list.html';

$content = new MediaContent();
$order = 'ctime DESC';
$cond = "status='N'";
$isadmin = is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'));
$acl = "";
if (isset($_GET['uid'])) {
    $cond .= ' AND poster='.intval($_GET['uid']);
}
$keyid = isset($_GET['keyid'])?intval($_GET['keyid']):0;
$keypath = $content->keys_path($keyid, 0, true);
set_ml_breadcrumbs($keypath);
if (!empty($_GET['keyid'])) {
    $order = 'weight,mid';
    $keyid = intval($_GET['keyid']);
    $xoopsTpl->assign('keypath', $keypath);

    $res = $xoopsDB->query("SELECT midref FROM ".RELAY." WHERE keyref=".$keyid);
    $mids = array();
    while (list($mid) = $xoopsDB->fetchRow($res)) {
	$mids[] = $mid;
    }
    $cond .= " AND mid IN (".join(',',$mids).")";
}
if (!$isadmin) {
    $uid = is_object($xoopsUser)?$xoopsUser->getVar('uid'):0;
    $acl = "LEFT JOIN ".ACLS." ON amid=mid AND auid=".$uid;
    $cond .= " AND (nacl=0 OR auid>0)";
}

$res = $xoopsDB->query("SELECT count(mid) FROM ".MAIN." $acl WHERE $cond");
list($n) = $xoopsDB->fetchRow($res);
$start = isset($_GET['start'])?intval($_GET['start']):0;
$max = $xoopsModuleConfig['max_list'];
$res = $xoopsDB->query("SELECT * FROM ".MAIN." $acl WHERE $cond ORDER BY $order", $max, $start);
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