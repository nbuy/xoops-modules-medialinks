<?php
# Medialinks - contents page entry and edit
# $Id: entry.php,v 1.5 2007/11/24 09:49:13 nobu Exp $

include "../../mainfile.php";
include_once "functions.php";
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/calendar.php';

$mid = isset($_REQUEST['mid'])?intval($_REQUEST['mid']):0;
if (!is_object($xoopsUser) || !check_access($mid)) {
    redirect_header('index.php', 3, _NOPERM);
    exit;
}

if (isset($_POST['save'])) {
    $pmid = intval($_POST['mid']);
    $content = new MediaContent($pmid);
    $stat = $content->getVar('status');
    $content = store_entry($content);
    $acls = ml_parse_acl();
    if ($content->getVar('nacl') != count($acls)) {
	$content->setVar('nacl', count($acls));
    }
    $mid = $content->store();
    if ($mid==0 && $pmid) {	// no updates
	$preacl = ml_get_acl($pmid, true);
	if (count($acls)==count($preacl) && count($acls)>0) {
	    foreach ($acls as $acl) { // check change acl
		$uid = $acl['uid'];
		if (!isset($preacl[$uid]) || $preacl[$uid]!=$acl['writable']) {
		    $mid = $pmid; // need update acl
		    break;
		}
	    }
	} else $mid = $pmid;
    }
    if ($mid) {
	$xoopsDB->query("DELETE FROM ".ACLS." WHERE amid=$mid");
	if (count($acls)) {
	    foreach ($acls as $acl) {
		$uid = $acl['uid'];
		$wr = $xoopsDB->quoteString($acl['writable']);
		$xoopsDB->query("INSERT INTO ".ACLS."(auid, amid, writable) VALUES($uid,$mid,$wr)");
	    }
	}
    }
    if ($stat=='W' && $content->getVar('status')=='N') {
	$tags = array(
	    'TITLE'=>$content->getVar('title'),
	    'URL'=>MODULE_URL."/detail.php?mid=".$mid);
	$notification_handler =& xoops_gethandler('notification');
	$notification_handler->triggerEvent('global', 0, 'new', $tags);
	foreach ($content->getKeywords() as $id) {
	    $tags['URL'] = MODULE_URL."/detail.php?mid=".$mid."&keyid=".$id;
	    $notification_handler->triggerEvent('keyword', $id, 'new', $tags);
	}
    }
    if ($mid) {
	redirect_header('detail.php?mid='.$mid, 1, _MD_DBUPDATED);
    } else {
	redirect_header('detail.php?mid='.$pmid, 3, _MD_DBUPDATE_FAIL);
	/*
	$err = array_pop($xoopsDB->logger->queries);
	echo "<div style='color:#008'>".$err['sql']."</div>";
	echo "<div style='color:#800'>".$err['error']."</div>";
	*/
    }
    exit;
}

include XOOPS_ROOT_PATH."/header.php";
$xoopsOption['template_main'] = 'medialinks_entry.html';

// calender JavaScript setup
if (isset($weekname)) $xoopsTpl->assign('weekname', $weekname);
if (isset($monthname)) $xoopsTpl->assign('monthname', $monthname);
$y = formatTimestamp(time(), 'Y');
$xoopsTpl->assign('calrange', array($y-10,$y+10));

$title = $mid?_MD_CONTENT_EDIT:_MD_CONTENT_NEW;

$xoopsTpl->assign('xoops_pagetitle', htmlspecialchars($xoopsModule->getVar('name')._MD_SEP.$title));
$xoopsTpl->assign('lang_title', htmlspecialchars($title));

$preview = isset($_POST['preview']);
if ($preview) {
    $content = new MediaContent(intval($_POST['mid']));
    $content = store_entry($content);
    $xoopsTpl->assign('fields', $content->dispVars(false));
} else {
    $content = new MediaContent($mid);
}

$isadmin = $xoopsUser->isAdmin($xoopsModule->getVar('mid'));
if ($mid && $content->getVar('status')!='N') { // deleted content view only admin
    if (!$isadmin || ($content->getVar('status')=='W' && $content->getVar('poster')==$xoopsUser->getVar('uid'))) {
	redirect_header('index.php', 3, _NOPERM);
	exit;
    }
}

$form = array('mid'=>$mid);
$nop = $mid?array():array('mtime', 'ctime', 'poster', 'hits');
keywords_widget($keywidget, $relays, $roots);

function squote($x) { return '"'.addslashes($x).'"'; }
$dests = array();
foreach ($relays as $k => $v) {
    $rk = find_root_id($v['keyid']);
    $rt = $v['root'];
    if (empty($dests[$rk])) $dests[$rk] = array($rt);
    else if (!in_array($rt, $dests[$rk])) $dests[$rk][] = $rt;
    
    $relays[$k]['values'] = "new Array(".join(',', $v['values']).")";
    $relays[$k]['labels'] = "new Array(".join(',', array_map('squote', $v['labels'])).")";
}
foreach ($dests as $k => $v) {
    $dests[$k] = "new Array(".join(',',$v).",0)";
}

$xoopsTpl->assign('relays', $relays);
$xoopsTpl->assign('dests', $dests);
$xoopsTpl->assign('initid', "new Array(".($roots?join(',',$roots):"").")");

$linkseq = 0;
$require = array();
foreach ($content->getField() as $k=>$field) {
    if (in_array($k, $nop)) continue;
    if ($mid || $preview) $v = $content->getVar($k);
    else $v = $field['def'];
    $type=$field['type'];
    if (preg_match('/^(\w+)\\((\d+)\\)/', $type, $d)) {
	$type = $d[1];
	$size = $d[2];
    } else {
	$size = 0;
    }
    $field['type'] = $type;
    $field['size'] = $size;
    switch ($type) {
    case 'link':
	$v = array();
	foreach ($content->getAttach(substr($field['name'], 0, 1)) as $data) {
	    $v[$linkseq++] = $data;
	}
	$max = $field['def']==""?3:intval($field['def']);
	$lp = max($max-count($v),1);
	$blank = array('linkid'=>0, 'url'=>'', 'name'=>'', 'weight'=>1);
	for ($i=0; $i<$lp; $i++) {
	    $v[$linkseq++] = $blank;
	}
	break;
    case 'keywords':
	if (preg_match('/\[(\d+)\]$/', $field['name'], $d)) {
	    $field['input'] = $keywidget[$d[1]];
	} else {
	    $field['input'] = '*NONE*';
	}
	break;
    case 'timestamp':
	$field['input'] = formatTimestamp($v);
	break;
    case 'user':
	if ($v) {
	    $field['input'] = "<a href='".XOOPS_URL."/userinfo.php?uid=$v'>".XoopsUser::getUnameFromId($v)."</a>";
	} else {
	    $v = $xoopsConfig['anonymous'];
	}
	break;
    case 'integer':
	if ($field['name']=='hits') $field['input']=$v;
	break;
    case 'date':
	if (empty($v)) $v=formatTimestamp(time(), 'Y-m-d');
	break;
    case 'text':
	$text = new XoopsFormDhtmlTextArea('', $field['name'], $v, 10, 50);
	$field['input'] = $text->render();
	if ($field['name'] == 'description') {
	    $cur = $content->getVar('style');
	    if (empty($cur)) $cur = 'b';
	    $style = new XoopsFormSelect('', 'style', $cur);
	    $style->addOptionArray($edit_style);
	    $field['option'] = "<br/>"._MD_CONTENT_STYLE." ".$style->render();
	}
    }
    if (is_array($field)) $field['value'] = $v;
    if (preg_match('/\\*$/', $field['label'])) {
	$rname = $field['name'];
	$require[$rname] = preg_replace('/\\*$/', '', $field['label']);
    }
    if ($field['weight']) $form[$k] = $field;
}

// status setting only by admin
if ($isadmin) {
    $status = new XoopsFormSelect('', 'status', $content->getVar('status'));
    $status->addOptionArray($status_sel);
    $form['status'] = array(
	'name'=>'status', 'label'=>_MD_CONTENT_STATUS,
	'input'=>$status->render());
}

$xoopsTpl->assign('form', $form);
$xoopsTpl->assign('check', $require);
$xoopsTpl->assign('is_uploads', check_groups('user_upload'));

if ($isadmin || !empty($xoopsModuleConfig['user_acl'])) {
    $acls = $preview?ml_parse_acl():ml_get_acl($mid);
    $xoopsTpl->assign('acls', $acls);
}
$xoopsTpl->assign('form', $form);

include XOOPS_ROOT_PATH."/footer.php";

function store_entry(&$content) {
    $myts =& MyTextSanitizer::getInstance();
    $nop = array('mtime', 'ctime', 'poster', 'hits');
    $keys = array();
    foreach ($_POST['keywords'] as $v) {
	$keys = array_merge($keys, explode(',', $v));
    }
    $content->setKeywords($keys);
    foreach ($content->getField() as $field) {
	$name = $field['name'];
	switch($field['type']) {
	case 'link':
	    $dels =& $_POST['dellink'];
	    $lid =& $_POST['linkid'];
	    $lurl =& $_POST['linkurl'];
	    $lname =& $_POST['linkname'];
	    $lgrp =& $_POST['linkgrp'];
	    $lord =& $_POST['linkord'];
	    foreach ($lid as $i=>$id) {
		if ($lgrp[$i]!=$name) continue;
		if(isset($dels[$i])) {
		    $content->delAttach($id);
		    continue;
		}
		if ($lname[$i]=="" &&
		    ($lurl[$i]=="" || $lurl[$i]==$field['def'])) continue;
		$link = array('linkid'=>$id, 'url'=>$lurl[$i],
			      'ltype'=>substr($name, 0, 1),
			      'name'=>$lname[$i],'weight'=>$lord[$i]);
		$content->setAttach($link);
	    }
	    break;
	case 'keywords':
	    break;
	default:
	    if (!in_array($name, $nop) && isset($_POST[$name])) {
		$content->setVar($name, $myts->stripSlashesGPC($_POST[$name]));
	    }
	}
    }
    // others settings
    $others = array('style');
    global $xoopsUser, $xoopsModule;
    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) $others[] = 'status';
    foreach ($others as $name) {
	if (isset($_POST[$name])) {
	    $content->setVar($name, $myts->stripSlashesGPC($_POST[$name]));
	}
    }
    return $content;
}

function key_expand($keys) {
    $ret = array();
    foreach ($keys as $key) {
	$id = ($key['nodetype']!=NODE_CATEGORY)?$key['keyid']:"";
	$name = $key['name'];
	if ($ret && isset($key['child'])) {
	    $ret[] = array("keyid"=>"", "name"=>"");
	}
	if ($id) {
	    $ret[] = array('keyid'=>$id, 'name'=>$name);
	} elseif (isset($key['relay'])) {
	    global $keywords;
	    $relay = $keywords->get($key['relay']);
	    $name = $relay['name'];
	}
	if (isset($key['child'])) {
	    foreach (key_expand($key['child']) as $exp) {
		$ret[] = array('keyid'=> ($id?$id.",":"").$exp['keyid'],
			       'name' => $name.' - '.$exp['name']);
	    }
	}
    }
    return $ret;
}

// find out all keyid all keywords tree
function find_child_id($key) {
    $ret = array();
    if ($key['nodetype']!=NODE_CATEGORY) $ret[] = $key['keyid'];
    if (!empty($key['child'])) {
	foreach ($key['child'] as $sub) {
	    $ret = array_merge($ret, find_child_id($sub));
	}
    }
    return $ret;
}

function keywords_widget(&$ret, &$relays, &$roots) {
    global $content, $keywords;
    $keywords = new KeyWords();
    $tree = $keywords->getTree();
    if (empty($tree)) return null;
    $keys = $content->getKeywords();
    $ret = array();
    $relays = array();
    $all = $keywords->get();
    foreach ($all as $key) {
	if (!empty($key['relay'])) {
	    $root = $key;
	    while ($root['parent']) {
		$root = $all[$root['parent']];
	    }
	    $relays[$key['keyid']]=array('keyid'=>$key['relay'],
					 'root'=>$root['keyid'],
					 'values'=>array(), 'labels'=>array());
	}
    }
    $roots = array();
    foreach ($tree as $key) {	// makes toplevel widget
	$keyid = $key['keyid'];
	$roots[] = $keyid;
	$name = "keywords[$keyid]";
	$words = new KeyFormSelect('', $name);
	$words->addOption('', _MD_KEY_NONE);
	$opts = key_expand($key['child']);
	$words->addOptions($opts);
	$find = null;
	foreach ($opts as $opt) {
	    $oid = $opt['keyid'];
	    $check = true;
	    if (empty($oid)) continue;
	    foreach (explode(',',$oid) as $id) {
		if (!in_array($id, $keys)) $check = false;
		$pid = $all[$id]['parent'];
		if (isset($relays[$pid])) {
		    $relays[$pid]['values'][] = $opt['keyid'];
		    $relays[$pid]['labels'][] = $opt['name'];
		}
	    }
	    if ($check) $find = $oid;
	}
	if ($find) $words->setValue($find);
	$words->setExtra('onchange="changeKeyword(this);"');
	$ret[$key['keyid']] = $words->render();
    }
    return $ret;
}

function ml_parse_acl() {
    global $xoopsDB;

    $wrs = $ids = array();
    $unames = array();
    if (isset($_POST['acl'])) {
	foreach ($_POST['acl'] as $uid) {
	    $ids[] = intval($uid);
	}
    }
    if (isset($_POST['aclw'])) {
	foreach ($_POST['aclw'] as $uid) {
	    $wrs[] = intval($uid);
	}
    }
    if (isset($_POST['addacl'])) {
	foreach(explode("\n", $_POST['addacl']) as $uid) {
	    $uid = trim($uid);
	    if (preg_match('/^\d+$/', $uid)) {
		$ids[] = intval($uid);
	    } elseif(!empty($uid)) {
		$unames[] = $xoopsDB->quoteString($uid);
	    }
	}
    }
    if (count($ids)==0 && count($unames)==0) return array();
    $cond = count($ids)?"uid IN (".join(',', $ids).")":"";
    if (count($unames)) {
	if ($cond) $cond .= " OR ";
	$cond .= "uname IN (".join(',', $unames).")";
    }
    $res = $xoopsDB->query("SELECT uid,uname,name,user_avatar FROM ".$xoopsDB->prefix('users')." WHERE level>0 AND ($cond) ORDER BY uname");
    $acls = array();
    while ($user = $xoopsDB->fetchArray($res)) {
	$user['writable'] = in_array($user['uid'], $wrs)?'Y':'N';
	foreach (array('uname','name','user_avatar') as $k) {
	    $user[$k] = htmlspecialchars($user[$k]);
	}
	$acls[] = $user;
    }
    return $acls;
}

function ml_get_acl($mid, $short=false) {
    global $xoopsDB;

    $res = $xoopsDB->query("SELECT uid,uname,name,user_avatar,writable FROM ".ACLS.", ".$xoopsDB->prefix('users')." WHERE auid=uid AND level>0 AND amid=$mid ORDER BY uname");
    $acls = array();
    while ($user = $xoopsDB->fetchArray($res)) {
	if ($short) {
	    $acls[$user['uid']] = $user['writable'];
	} else {
	    foreach (array('uname','name','user_avatar') as $k) {
		$user[$k] = htmlspecialchars($user[$k]);
	    }
	    $acls[] = $user;
	}
    }
    return $acls;
}
?>