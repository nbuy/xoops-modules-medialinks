<?php
# Medialinks tracking attachment
# $Id: track.php,v 1.1 2006/07/12 16:27:25 nobu Exp $

include "../../mainfile.php";
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

if (XOOPS_USE_MULTIBYTES && function_exists('mb_convert_encoding')) {
    function enc($text, $code='SJIS') {
	return mb_convert_encoding($text, $code, _CHARSET);
    }
} else {
    function enc($text) {
	return $text;
    }
}

if (preg_match('/(iPod|iTunes)/', $name)) {
    include_once XOOPS_ROOT_PATH.'/class/template.php';
    $cache_id = "$mydirname/track.php?lid=$lid";
    header("Content-Type: text/xml; charset=UTF-8");
    header("Content-Disposition:attachment;filename=\"$mydirname.xml\"");
    header("Cache-Control: public");
    header("Pragma: public");
    
    $myts =& MyTextSanitizer::getInstance();
    $tpl = new XoopsTpl();
    $tpl->xoops_setCaching(2);
    $tpl->xoops_setCacheTime(3600);
    if (true || !$tpl->is_cached('db:medialinks_rss.xml', $cache_id)) {
	include_once 'functions.php';
	$xoopsConfig['generator'] = $mydirname.' '.$xoopsModule->getVar('version');
	$content = new MediaContent($mid);
	$tpl->assign('config', $xoopsConfig);
	$tpl->assign('pubdate', formatTimestamp($content->getVar('mtime'), 'rss'));
	$tpl->assign('builddate', formatTimestamp(time(), 'rss'));
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
    header("Content-Type: video/x-ms-asf; charset=Shift_JIS");
    header("Content-Disposition:attachment;filename=\"$mydirname.asx\"");
    header("Cache-Control: public");
    header("Pragma: public");
    $out = "<asx version=\"3.0\">\n".
	"  <Author>".$xoopsConfig['sitename']."</Author>\n".
	"  <title>".htmlspecialchars($name)."</title>\n".
	//"  <Copyright>John Smith Publishing Inc.</Copyright>\n".
	//"  <Abstract>Cafe-Music CD by John Smith and friends</Abstract>\n".
	"  <moreinfo href=\"".XOOPS_URL."/\"/>\n".
	"  <entry>\n".
	//"    <Author>John & Jane Smith</Author>\n".
	"    <title>".htmlspecialchars($name)."</title>\n".
	"    <moreinfo href=\"".XOOPS_URL."/modules/$mydirname/detail.php?mid=$mid\"/>\n".
	"    <ref href=\"".htmlspecialchars($url)."\"/>\n".
	"  </entry>\n".
	"</asx>";
    echo enc($out);
} elseif (preg_match('/\\.mov$/i', $url)) {
    header("Content-Type: application/x-quicktimeplayer");
    header("Content-Disposition:attachment;filename=\"$mydirname.qtl\"");
    header("Cache-Control: public");
    header("Pragma: public");
    echo '<?xml version="1.0"?>
<?quicktime type="application/x-quicktime-media-link"?>
<embed src="'.htmlspecialchars($url).'" />';
} else {
    header("Location: ".$url);
}
?>