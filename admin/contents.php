<?php
# MediaLinks - Administration contents
# $Id: contents.php,v 1.1 2007/11/25 04:04:55 nobu Exp $

include '../../../include/cp_header.php';
include_once '../functions.php';
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';

// system default fields count (see oninstall.php $fields)
define('FIELD_OFFSET', 9);
$keywords = new KeyWords();

$op = isset($_GET['op'])?$_GET['op']:'';

if (isset($_POST['dels'])) {
    redirect_result(contents_delete());
} elseif (!empty($_POST['op'])) {	// list operation
    $res = contents_update_weight();
    $op = $_POST['op'];
    switch ($op) {
    case 'del':
	break;
    case 'conf':
    case 'hide':
	$res = contents_update_status($op=='conf'?'N':'X');
    default:
	redirect_result($res);
    }
}

xoops_cp_header();
include "mymenu.php";

switch ($op) {
case 'del':
    contents_delete_form();
    break;

default:
    echo "<h2>"._AM_CONTENTS_ADMIN."</h2>\n";
    contents_list();
    break;
}
xoops_cp_footer();

function contents_list() {
    global $xoopsDB, $xoopsModuleConfig, $keywords, $status_sel;

    $res = $xoopsDB->query("SELECT count(mid) FROM ".MAIN);
    list($n) = $xoopsDB->fetchRow($res);
    $start = isset($_GET['start'])?intval($_GET['start']):0;
    $max = $xoopsModuleConfig['max_rows'];
    $nav = new XoopsPageNav($n, $max, $start, "start", "op=list");
    echo "<style>
.wait td { background-color: #fdd; padding: 5px; }
.del td { background-color: #ccf; padding: 5px; }
</style>\n";
    echo "<table width='100%'>\n";
    echo "<tr><td>"._AM_COUNT.' '.$n."</td><td>";
    if ($n>$max) echo _AM_PAGE.' '.$nav->renderNav();
    echo "</td><td align='center'><a href='../entry.php'>"._AM_CONTENTS_NEW."</a></td><td align='right'>[<a href='summary.php?export=csv&type=c'>"._AM_EXPORT_FILE."</a>]</td></tr>\n";
    echo "</table>\n";
    echo "<form method='POST'>\n";
    echo "<table cellspacing='1' padding='5' class='outer'>\n";
    $header = array('', _AM_TITLE, _AM_MTIME, _AM_POSTER, _AM_HITS,
		    _AM_STATUS, _AM_SORT_ORDER, _AM_OPERATION);

    echo "<tr><th>".join("</th><th>", $header)."</th></tr>\n";
    $res = $xoopsDB->query("SELECT * FROM ".MAIN." ORDER BY status,mtime DESC", $max, $start);
    $wn = $n = 0;
    while ($data = $xoopsDB->fetchArray($res)) {
	$mid = $data['mid'];
	$uid = $data['poster'];
	$poster = "<a href='".XOOPS_URL."/userinfo.php?uid=$uid'>".XoopsUser::getUnameFromId($uid)."</a>";
	$w = $data['weight'];
	$weight = "<input size='2' name='weight[$mid]' value='$w'/>".
	    "<input type='hidden' name='prev[$mid]' value='$w'/>";
	$op = "<a href='../entry.php?mid=$mid'>"._EDIT."</a>";
	$status = $data['status'];
	$ck = '';
	switch ($status) {
	case 'W': $bg = 'wait'; $wn++; $ck=' checked'; break;
	case 'X': $bg = 'del'; break;
	case 'N': $bg = $n++%2?'even':'odd'; break;
	}
	$check = "<input type='checkbox' name='check[$mid]' value='$mid'$ck/>";
	if (isset($status_sel[$status])) $status = $status_sel[$status];
	echo "<tr class='$bg'><td align='center'>$check</td>".
	    "<td><a href='../detail.php?mid=$mid'>".
	    htmlspecialchars($data['title'])."</a></td><td>".
	    formatTimestamp($data['mtime'], 'm')."</td><td>".
	    "$poster</td><td align='right'>".$data['hits'].
	    "</td><td align='center'>$status</td>".
	    "<td>$weight</td><td>$op</td></tr>";
    }
    echo "</table>\n";
    $op = new XoopsFormSelect('', 'op');
    $op->addOption('none','--');
    if ($wn) {
	$op->addOption('conf',_AM_OP_CONF);
	$op->setValue('conf');
    }
    $op->addOption('hide',_AM_OP_HIDE);
    $op->addOption('del',_AM_OP_DEL);
    echo "<div>".$op->render();
    echo " &nbsp; <input type='submit' value='"._SUBMIT."'/></div>\n";
    echo "</form>\n";
}

function contents_update_weight() {
    global $xoopsDB;
    $prev =& $_POST['prev'];
    foreach ($_POST['weight'] as $mid => $w) {
	$w = intval($w);
	if ($w != $prev[$mid]) {
	    $xoopsDB->query("UPDATE ".MAIN." SET weight=$w WHERE mid=$mid");
	}
    }
}

function contents_update_status($stat) {
    global $xoopsDB;
    $mids = array();
    foreach ($_POST['check'] as $v) {
	$mids[] = intval($v);
    }
    $set = join(',', $mids);
    return $xoopsDB->query("UPDATE ".MAIN." SET status='$stat' WHERE mid IN ($set)");
}

function contents_delete_form() {
    global $xoopsDB, $xoopsModule;
    $dels = array();
    foreach ($_POST['check'] as $v) {
	$dels[] = intval($v);
    }
    $modid = $xoopsModule->getVar('mid');
    $res = $xoopsDB->query("SELECT mid,title,poster, ctime,
count(com_id) ccom, count(linkid) clink
FROM ".MAIN." LEFT JOIN ".$xoopsDB->prefix('xoopscomments')."
   ON com_itemid=mid AND com_modid=$modid LEFT JOIN ".ATTACH." ON midref=mid
WHERE mid IN (".join(',',$dels).") GROUP BY mid");
    echo "<form method='POST'>\n";
    echo "<div class='confirmMsg'>\n";
    echo "<h2>"._AM_CONTENTS_DEL."</h2>";
    while ($data = $xoopsDB->fetchArray($res)) {
	$mid = $data['mid'];
	echo "<div>".htmlspecialchars($data['title']).
	    " (".formatTimestamp($data['ctime'], 'm').' '.
	    _AM_POSTER.' '.XoopsUser::getUnameFromId($data['poster']).') ';
	if ($data['ccom']) echo ' &nbsp; '._AM_COMMENT_COUNT.': '.$data['ccom'];
	if ($data['clink']) echo ' &nbsp; '._AM_ATTACH_COUNT.': '.$data['clink'];
	echo "<input type='hidden' name='dels[$mid]' value='$mid'/></div>\n";
    }
    echo "<p><input type='submit' value='"._DELETE."'/></p>";
    echo "</div>\n";
    echo "</form>\n";
}

function contents_delete() {
    global $xoopsDB, $xoopsModule;
    $dels = array();
    foreach ($_POST['dels'] as $v) {
	$dels[] = intval($v);
    }
    $delset = join(',',$dels);
    $res = $xoopsDB->query("DELETE FROM ".MAIN." WHERE mid IN ($delset)");
    if ($res) {
	$xoopsDB->query("DELETE FROM ".ATTACH." WHERE midref IN ($delset)");
	$xoopsDB->query("DELETE FROM ".RELAY." WHERE midref IN ($delset)");
	$xoopsDB->query("DELETE FROM ".$xoopsDB->prefix('xoopscomments')." WHERE com_modid=".$xoopsModule->getVar('mid')." AND com_itemid IN  ($delset)");
	foreach ($dels as $mid) {
	    $dir = get_upload_path($mid);
	    if (is_dir($dir)) {
		$dh = opendir($dir);
		while ($file = readdir($dh)) {
		    if ($file!='.' && $file != '..') unlink("$dir/$file");
		}
		closedir($dh);
		rmdir($dir);
	    }
	}
    }
    return $res;
}

function redirect_result($ret, $dest='contents.php') {
    if ($ret) {
	redirect_header($dest, 1, _AM_DBUPDATED);
    } else {
	redirect_header($dest, 3, _AM_DBUPDATE_FAIL);
    }
    exit;
}
?>