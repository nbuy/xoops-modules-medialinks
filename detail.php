<?php
# Medialinks view a page
# $Id: detail.php,v 1.1 2006/07/12 16:27:25 nobu Exp $

include "../../mainfile.php";
include_once "functions.php";

$mid = isset($_GET['mid'])?intval($_GET['mid']):0;
if (!$mid) {
    redirect_header('index.php', 3, _NOPERM);
    exit;
}
$content = new MediaContent($mid);
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
if (!in_array($keyid, $content->getKeywords())) $keyid=0;
$xoopsTpl->assign('keypath', $content->keys_path($keyid, 0, true));
if ($xoopsModuleConfig['use_comment']) {
    include XOOPS_ROOT_PATH.'/include/comment_view.php';
}
include XOOPS_ROOT_PATH."/footer.php";
?>