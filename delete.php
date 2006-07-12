<?php
# Medialinks view a page
# $Id: delete.php,v 1.1 2006/07/12 16:27:25 nobu Exp $

include "../../mainfile.php";
include_once "functions.php";

if (isset($_POST['delete'])) {
    $mid = isset($_POST['mid'])?intval($_POST['mid']):0;
} else {
    $mid = isset($_GET['mid'])?intval($_GET['mid']):0;
}
if (!$mid || !is_object($xoopsUser)) {
    redirect_header('index.php', 3, _NOPERM);
    exit;
}

$content = new MediaContent($mid);
$mid = $content->getVar('mid');
if (empty($mid) || $content->getVar('status')=='X' ||
    ($content->getVar('poster')!=$xoopsUser->getVar('uid')&&
     $xoopsUser->isAdmin($xoopsModule->getVar('mid')))) {
    redirect_header('index.php', 3, _NOPERM);
    exit;
}
if (isset($_POST['delete'])) {
    $content->setVar('status', 'X');
    if ($content->store()) redirect_header('index.php', 1, _MD_DBUPDATED);
    else redirect_header('index.php', 2, _MD_DBUPDATE_FAIL);
}
include XOOPS_ROOT_PATH."/header.php";
$xoopsOption['template_main'] = 'medialinks_operate.html';

//$xoopsTpl->assign('xoops_pagetitle', htmlspecialchars($xoopsModule->getVar('name')._MD_SEP.$content->getVar('title')));
$xoopsTpl->assign('fields', $content->dispVars(false));

$button = "<input type='hidden' name='mid' value='$mid'/>\n".
    "<div>"._MD_CONTENT_DELETE."</div>\n".
    "<input name='delete' type='submit' value='"._DELETE."'/>\n";
$xoopsTpl->assign('button', $button);

include XOOPS_ROOT_PATH."/footer.php";
?>