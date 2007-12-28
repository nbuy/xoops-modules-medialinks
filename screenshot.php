<?php
# make video screenshot/thumbnail image
# $Id: screenshot.php,v 1.2 2007/12/28 08:39:08 nobu Exp $

require_once dirname(__FILE__)."/perm.php";

function ml_screenshot($mid) {
    global $ml_shotopts;
    $path = get_upload_path($mid, "00-shot.jpg");
    if (!file_exists($path)) {
	global $xoopsDB;
	$res = $xoopsDB->query("SELECT url FROM ".$xoopsDB->prefix('medialinks_attach')." WHERE midref=$mid AND ltype='m' ORDER BY weight,linkid", 1);
	list($url) = $xoopsDB->fetchRow($res);
	if (preg_match('/^(\w+:)\//', $url)) {
	    // get temporarily for screenshot
	    $vfile = get_upload_path($mid, basename($url));
	    if (!file_exists($vfile)) { // already get?
		if (!is_dir(dirname($vfile))) mkdir(dirname($vfile));
		system("wget -q -O '$vfile' '$url'");
		$temp = $vfile;
	    }
	} else {
	    $vfile = get_upload_path($mid, $url);
	}
	if (file_exists($vfile)) {
	    global $mlModuleConfig; // NOTE: this script refer from blocks
	    $opts = $mlModuleConfig['shotopts'];
	    $cmdpath=$mlModuleConfig['cmdpath'];
	    if (!empty($cmdpath)) putenv("PATH=$cmdpath");
	    if (preg_match(_VIDEO_EXT, $vfile)) {
		system("ffmpeg -i \"$vfile\" $opts -vcodec mjpeg -vframes 1 -an -f rawvideo -y \"$path\"");
	    } elseif (preg_match(_IMAGE_EXT, $vfile)) {
		system("convert \"$vfile\" -resize 320 \"$path\"");
	    }
	    if (filesize($path)==0) unlink($path);
	}
    }
    if (!file_exists($path)) {
	$mydirname = basename(dirname(__FILE__));
	return XOOPS_URL."/modules/$mydirname/images/nodata.png";
    }
    if (!empty($temp) && file_exists($temp)) unlink($temp);
    return get_upload_url($mid, "00-shot.jpg");
}
?>
