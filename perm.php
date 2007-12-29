<?php
# check permition for edit
# $Id: perm.php,v 1.3 2007/12/29 08:51:43 nobu Exp $

if (!isset($GLOBALS['mlModuleConfig'])) {
    global $xoopsModule, $xoopsModuleConfig;
    $mydirname = basename(dirname(__FILE__));
    if (is_object($xoopsModule) && $xoopsModule->getVar('dirname')==$mydirname) {
	$GLOBALS['mlModuleConfig'] =& $xoopsModuleConfig;
    } else {
	$module_handler =& xoops_gethandler('module');
	$module =& $module_handler->getByDirname($mydirname);
	$config_handler =& xoops_gethandler('config');
	$GLOBALS['mlModuleConfig'] =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
    }
    define('_VIDEO_EXT', '/\.(avi|mov|wmv|mpe?[g4]|mkv|ogm|flv)$/i');
    define('_IMAGE_EXT', '/\.(jpe?g|png|gif)$/i');
    define('_ML_SHOTNAME', '00-shot.jpg');
}

function check_groups($prop) {
    global $xoopsUser;
    if (!is_object($xoopsUser)) return false;
    $grps = $xoopsUser->getGroups();
    foreach ($GLOBALS['mlModuleConfig'][$prop] as $gid) {
	if (in_array($gid, $grps)) return true;
    }
    return false;
}

function check_access($mid) {
    global $xoopsDB, $xoopsUser, $xoopsModule;
    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) return true;

    if ($mid) {
	$uid = $xoopsUser->getVar('uid');
	$res = $xoopsDB->query("SELECT mid FROM ".MAIN." LEFT JOIN ".ACLS." ON mid=amid AND auid=$uid WHERE mid=$mid AND (poster=$uid OR auid)");
	return $res && $xoopsDB->getRowsNum($res)>0;
    }
    return check_groups('post_group');
}

function get_upload_path($mid, $file="") {
    if (preg_match('/^(\w+:)?\//', $file)) return false; // absolute path setting
    $path = $GLOBALS['mlModuleConfig']['upload_path'];
    $dir = $mid?sprintf("%04d", $mid):"work".$GLOBALS['xoopsUser']->getVar('uid');
    $path .= "/$dir";
    if (!preg_match('/^\//', $path)) $path = XOOPS_UPLOAD_PATH."/$path";
    if ($file) $path .= "/$file";
    return $path;
}

function get_upload_url($mid, $url="") {
    if (preg_match('/^(\w+:)?\//', $url)) return $url; // absolute path setting
    $path = $GLOBALS['mlModuleConfig']['upload_path'];
    $dir = $mid?sprintf("%04d", $mid):"work".$GLOBALS['xoopsUser']->getVar('uid');
    $path .= "/$dir";
    if (!preg_match('/^\//', $path)) {
	$path = XOOPS_UPLOAD_URL."/$path";
    }
    if ($url) $path .= "/$url";
    return $path;
}
?>
