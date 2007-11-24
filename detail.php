<?php
# Medialinks view a page
# $Id: detail.php,v 1.2 2007/11/24 09:49:13 nobu Exp $

include "../../mainfile.php";
include_once "functions.php";

$mid = isset($_GET['mid'])?intval($_GET['mid']):0;
if (!$mid) {
    redirect_header('index.php', 3, _NOPERM);
    exit;
}
$content = new MediaContent($mid);
if ($content->getVar('mid')!=$mid) {
    redirect_header('index.php', 3, _NOPERM);
    exit;
}
$content->hits();

if ($content->getVar('status')!='N') { // deleted content view only admin
    if (!is_object($xoopsUser) ||
	!($xoopsUser->isAdmin($xoopsModule->getVar('mid')) ||
	  ($content->getVar('status')=='W' && $content->getVar('poster')==$xoopsUser->getVar('uid')))) {
	redirect_header('index.php', 3, _NOPERM);
	exit;
    }
}
include XOOPS_ROOT_PATH."/header.php";
$xoopsOption['template_main'] = 'medialinks_detail.html';

$xoopsTpl->assign('xoops_pagetitle', htmlspecialchars($xoopsModule->getVar('name')._MD_SEP.$content->getVar('title')));
$xoopsTpl->assign('fields', $content->dispVars());
$keyid = isset($_GET['keyid'])?intval($_GET['keyid']):0;
$keys =& $content->getKeywords();
if (!in_array($keyid, $keys)) $keyid=$keys[0];
$keypath = $content->keys_path($keyid, 0, true);
$xoopsTpl->assign('keypath', $keypath);
set_ml_breadcrumbs($keypath, array(array(
		   'url'=>MODULE_URL.'/detail.php?mid='.$mid,
		   'name'=>htmlspecialchars($content->getVar('title')))));
$conf = $xoopsModuleConfig['d3forumid'];
if ($conf) {
    $d3id = 0;
    foreach (explode(',', $conf) as $id) {
	if (preg_match('/^\d+$/', $id)) {
	    if ($d3id==0) $d3id = $id;
	} elseif (preg_match('/^key(\d+)=(\d+)$/', $id, $d)) {
	    if (in_array($d[1], $keys)) $d3id = $d[2];
	}
    }
    if ($d3id) $xoopsTpl->assign('d3forum_id', $d3id);
}

include XOOPS_ROOT_PATH.'/include/comment_view.php';
include XOOPS_ROOT_PATH."/footer.php";
?>