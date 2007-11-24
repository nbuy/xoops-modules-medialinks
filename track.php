<?php
# Medialinks tracking attachment
# $Id: track.php,v 1.3 2007/11/24 09:49:13 nobu Exp $

include "../../mainfile.php";
include_once "perm.php";
include_once XOOPS_ROOT_PATH.'/class/template.php';

$lid = isset($_GET['lid'])?intval($_GET['lid']):0;
$mydirname = basename(dirname(__FILE__));
if ($lid) {
    $res = $xoopsDB->query("SELECT url,name,midref FROM ".
	$xoopsDB->prefix('medialinks_attach')." WHERE linkid=".$lid);
}
if (!$lid || !$res || $xoopsDB->getRowsNum($res)==0) {
    redirect_header('index.php', 3, _NOPERM);
    exit;
}
list($url, $name, $mid) = $xoopsDB->fetchRow($res);
$xoopsDB->queryF("UPDATE ".$xoopsDB->prefix('medialinks_attach')." SET hits=hits+1 WHERE linkid=".$lid);

if (!preg_match('/^(\w+:)?\//', $url)) {
    $url = get_upload_url($mid)."/$url";
}

if (preg_match('/\\.flv$/i', $url)) $url=XOOPS_URL."/modules/$mydirname/flvplayer.swf?file=$url";

if (XOOPS_USE_MULTIBYTES && function_exists('mb_convert_encoding')) {
    function enc($text, $code='SJIS') {
	return mb_convert_encoding($text, $code, _CHARSET);
    }
} else {
    function enc($text) {
	return $text;
    }
}

$tpl = new XoopsTpl();
$xoopsConfig['generator'] = $mydirname.' '.$xoopsModule->getVar('version');
$tpl->assign('config', $xoopsConfig);
$tpl->assign('builddate', formatTimestamp(time(), 'rss'));

if (preg_match('/(iPod|iTunes)/', $name)) {
    $cache_id = "$mydirname/track.php?lid=$lid";
    header("Content-Type: text/xml; charset=UTF-8");
    header("Content-Disposition:attachment;filename=\"$mydirname.xml\"");
    header("Cache-Control: public");
    header("Pragma: public");
    
    $tpl->xoops_setCaching(2);
    $tpl->xoops_setCacheTime(3600);
    if (true || !$tpl->is_cached('db:medialinks_rss.xml', $cache_id)) {
	include_once 'functions.php';
	$content = new MediaContent($mid);
	$tpl->assign('pubdate', formatTimestamp($content->getVar('mtime'), 'rss'));
	$item = $content->dispVars();
	$item['url'] = $url;
	$item['item_title'] = htmlspecialchars($name);
	if (preg_match('/\\.mov$/i', $url)) $type = 'video/quicktime';
	elseif (preg_match('/\\.(m4v|mp4)$/i', $url)) $type='video/mp4';
	elseif (preg_match('/\\.(wmv|avi)$/i', $url)) $type='video/x-msvideo';
	$item['mimetype'] = $type;
	$item['pubdate'] = formatTimestamp($content->getVar('mtime'), 'rss');
	$tpl->assign('items', array($item));
	$tpl->assign('builddate', formatTimestamp(time(), 'rss'));
    }
    $tpl->template_dir = XOOPS_ROOT_PATH."/modules/$mydirname/templates";
    echo enc($tpl->fetch('db:medialinks_rss.xml', $cache_id), 'UTF-8');
} elseif (preg_match('/\\.(avi|wmv)$/i', $url)) {
    $tpl->assign('url', $url);
    $tpl->assign('mydirname', $mydirname);
    $tpl->assign('title', $name);
    $tpl->assign('mid', $mid);

    header("Content-Type: video/x-ms-asf; charset=Shift_JIS");
    header("Content-Disposition:attachment;filename=\"$mydirname.asx\"");
    header("Cache-Control: public");
    header("Pragma: public");
    echo enc($tpl->fetch('db:medialinks_track.asx'));
} elseif (preg_match('/\\.mov$/i', $url)) {
    $tpl->assign('url', $url);

    header("Content-Type: application/x-quicktimeplayer");
    header("Content-Disposition:attachment;filename=\"$mydirname.qtl\"");
    header("Cache-Control: public");
    header("Pragma: public");
    echo enc($tpl->fetch('db:medialinks_track.qtl'), 'UTF-8');
} else {
    header("Location: ".$url);
}
?>