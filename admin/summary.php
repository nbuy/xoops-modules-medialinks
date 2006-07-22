<?php
# MediaLinks - Summary of Access
# $Id: summary.php,v 1.4 2006/07/22 03:29:52 nobu Exp $

include '../../../include/cp_header.php';
include_once '../functions.php';
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';

$type = isset($_GET['type'])?$_GET['type']:'m';
if (isset($_GET['export']) && $_GET['export']=='csv') {
    if ($type=="c") contents_csv();
    else links_stat_csv($type);
    exit;
}

xoops_cp_header();
include "mymenu.php";
echo "<h2>"._AM_SUMMARY_TITLE."</h2>\n";

switch ($type) {
case 'm':
case 'a':
    links_stat($type);
    break;
}
xoops_cp_footer();

function sql_stat($type, $count=false, $order="hits DESC") {
    $t = $type=='a'?'a':'m';
    if ($count) {
	return "SELECT count(linkid) FROM ".ATTACH." WHERE ltype='$t'";
    } else {
	if (!empty($order)) $order = "ORDER BY $order";
	return "SELECT mid,title,name,url,a.hits FROM ".ATTACH." a ,".MAIN." m WHERE midref=mid AND ltype='$t' ".$order;
    }
}

function links_stat($type) {
    global $xoopsDB, $xoopsModuleConfig;
    $labs = array('m'=>array(_AM_LTYPE_MEDIA, 'm'),
		  'a'=>array(_AM_LTYPE_DOCUMENT, 'a'));

    echo "<div><em>"._AM_SUMMARY_TYPE."</em> ";
    foreach ($labs as $k=>$lab) {
	echo " &nbsp; ";
	if ($type==$k) {
	    echo "[<b>".$lab[0]."</b>]";
	} else {
	    echo "<a href='summary.php?type=".$lab[1]."'>".$lab[0]."</a>";
	}
    }
    echo "</div>\n";

    $res = $xoopsDB->query(sql_stat($type, true));
    list($count) = $xoopsDB->fetchRow($res);

    $start = isset($_GET['start'])?intval($_GET['start']):0;
    $max = $xoopsModuleConfig['max_rows'];
    $res = $xoopsDB->query(sql_stat($type), $max,$start);
    $nav = new XoopsPageNav($count, $max, $start, "start", 'type='.$type);
    echo "<table width='100%'>\n";
    echo "<tr><td>"._AM_COUNT." ".$count."</td><td>";
    if ($count>$max) echo ' &nbsp; '._AM_PAGE.' '.$nav->renderNav();
    echo "</td><td align='right'>[<a href='summary.php?type=$type&export=csv'>"._AM_EXPORT_FILE."</a>]</td></tr>\n";
    echo "</table>\n";
    echo "<table cellspacing='1' class='outer'>\n";
    $head = array(_AM_TITLE, _AM_LINKNAME, "URL", _AM_HITS);
    echo "<tr><th>".join("</th><th>", $head)."</th></tr>\n";
    $n = 0;
    $lmax = 35;
    while ($data = $xoopsDB->fetchArray($res)) {
	$bg = $n++%2?'even':'odd';
	$mid = $data['mid'];
	$url = htmlspecialchars($data['url']);
	$title = "<a href='../detail.php?mid=$mid'>".htmlspecialchars($data['title'])."</a>";
	$link = htmlspecialchars($data['name']);
	$hits = $data['hits'];
	if (strlen($url)<$lmax) $aname = $url;
	else $aname = '...'.substr($url, 3-$lmax);
	echo "<tr class='$bg'><td>$title</td><td>$link</td><td>".
	    "<a href='$url'>".htmlspecialchars($aname)."</a></td><td align='right'>$hits</td></tr>\n";
    }
    echo "</table>\n";
}

function quote_csv($text) {
    return '"'.preg_replace('/"/', '""', $text).'"';
}

function links_stat_csv($type) {
    global $xoopsDB;
    $res = $xoopsDB->query(sql_stat($type, false));
    $head = array('ID', _AM_TITLE, _AM_LINKNAME, 'URL', _AM_HITS);
    $buf = join(',',array_map('quote_csv', $head))."\n";
    $file = 'medialinks-$type'.formatTimestamp(time(), 'Ymd').'.csv';
    while ($data = $xoopsDB->fetchRow($res)) {
	$buf .= join(',',array_map('quote_csv', $data))."\n";
    }
    export_out($file, $buf);
}

// dump all contents in CSV
function contents_csv() {
    global $xoopsDB, $status_sel;
    $rec = split(',', 'mid,title,ctime,mtime,poster,hits,status');
    $res = $xoopsDB->query("SELECT name,label,type,weight FROM ".FIELDS." ORDER BY weight");
    $labels = array('mid'=>'ID', 'hits'=>_AM_HITS, 'status'=>_AM_STATUS);
    while (list($name, $label, $type, $w) = $xoopsDB->fetchRow($res)) {
	$labels[$name] = $w?$label:"[$label]";
	if ($type == 'link') continue;
	if (!in_array($name, $rec)) $rec[] = $name;
    }
    $head = array();
    foreach ($rec as $n) {
	$head[] = $labels[$n];
    }
    $buf = join(',',array_map('quote_csv', $head))."\n";
    $file = 'medialinks-'.formatTimestamp(time(), 'Ymd').'.csv';
    $res = $xoopsDB->query("SELECT * FROM ".MAIN." ORDER BY status,mid");
    while ($data = $xoopsDB->fetchArray($res)) {
	$row = array();
	foreach ($rec as $n) {
	    $keys = keys_expand($data['mid']);
	    if (preg_match('/\\[(\\d+)\\]$/', $n, $d)) {
		$v = isset($keys[$d[1]])?$keys[$d[1]]:'';
	    } else {
		$v = $data[$n];
	    }
	    switch ($n) {
	    case 'mtime':
	    case 'ctime':
		$row[] = formatTimestamp($v);
		break;
	    case 'poster':
		$row[] = XoopsUser::getUnameFromId($v);
		break;
	    case 'status':
		if (isset($status_sel[$v])) $v = $status_sel[$v];
	    default:
		$row[] = $v;
	    }
	}
	$buf .= join(',',array_map('quote_csv', $row))."\n";
    }
    export_out($file, $buf);
}

function export_out($file, $buf) {
    if (_CHARSET!=_AM_EXPORT_CHARSET&&function_exists("mb_convert_encoding")) {
	header('Content-Type: text/csv; Charset='._AM_EXPORT_CHARSET);
	$buf = mb_convert_encoding($buf, _AM_EXPORT_CHARSET, _CHARSET);
    } else {
	header('Content-Type: text/csv; Charset='._CHARSET);
    }
    header('Content-Disposition:attachment;filename="'.$file.'"');
    echo $buf;
}
?>