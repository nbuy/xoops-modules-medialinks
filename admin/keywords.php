<?php
# MediaLinks - Administration
# $Id: keywords.php,v 1.1 2007/11/25 04:04:55 nobu Exp $

include '../../../include/cp_header.php';
include_once '../functions.php';
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';

$keywords = new KeyWords();

$op = isset($_GET['op'])?$_GET['op']:'';

if (isset($_POST['keys'])) {
    $myts =& MyTextSanitizer::getInstance();
    $keys = array('name', 'weight', 'description');
    $nodetype = intval($_POST['nodetype']);
    $par = intval($_POST['parent']);
    if ($par == 0) $nodetype = 1; // only for category
    $vals = array('nodetype'=>$nodetype,
		  'parent'=>$par,
		  'relay'=>($nodetype!=1)?0:intval($_POST['relay']));
    foreach ($keys as $k) {
	$vals[$k] = $xoopsDB->quoteString($myts->stripSlashesGPC($_POST[$k]));
    }
    $keyid = intval($_POST['keyid']);
    redirect_result($keywords->insert($keyid, $vals));
} elseif (isset($_POST['delkey'])) {
    $keyid = intval($_POST['keyid']);
    redirect_result($keywords->delete($keyid));
}

xoops_cp_header();
include "mymenu.php";

$keyid = isset($_GET['keyid'])?intval($_GET['keyid']):0;

switch ($op) {
default:
    echo "<h2>"._AM_KEYWORDS_ADMIN."</h2>\n";
    echo "<style>
.level1 li { margin-left: 2em; list-style: disc; }
.level2 li { margin-left: 3em; list-style: circle; }
.level3 li { margin-left: 4em; list-style: none; }
</style>";
    keywords_list($keywords->getTree());
    echo "<hr/>";
    keyword_form();
    break;
case 'editkey':
    keyword_form($keyid);
    break;
case 'delkey':
    del_keyword_form($keyid);
    break;
}
xoops_cp_footer();

function keywords_list($keys, $level=0) {
    if (empty($keys)) return;
    echo "<ul class='level$level'>\n";
    foreach ($keys as $key) {
	$id = $key['keyid'];
	$name = $key['name'];
	if ($key['relay']) {
	    global $keywords;
	    $relkey = $keywords->get($key['relay']);
	    $name .= '/'.$relkey['name'];
	}
	switch ($key['nodetype']) {
	case 1: $name = "[$name]"; break;
	case 2: $name = "$name*"; break;
	}
	$w = $key['weight'];
	if (!isset($key['child'])) {
	    $admin = "[ <a href='?op=delkey&keyid=$id'>"._DELETE."</a> ]";
	} else {
	    $admin = "";
	}
	echo "<li><a href='?op=editkey&keyid=$id'>".
	    htmlspecialchars($name)."</a> ($w) $admin</li>\n";
	if (isset($key['child'])) keywords_list($key['child'], $level+1);
    }
    echo "</ul>\n";
}    

function keyword_form($keyid=0) {
    global $keywords, $nodetypes_select;
    if ($keyid) {
	$vals = $keywords->get($keyid);
    } else {
	$vals = array('keyid'=>0, 'name'=>'', 'parent'=>0, 'relay'=>0,
		      'weight'=>1, 'nodetype'=>2, 'description'=>'');
    }
    $form = new XoopsThemeForm($keyid?_AM_KEYWORDS_EDIT:_AM_KEYWORDS_NEW,
			       'KeysForm' , 'keywords.php');
    $form->addElement(new XoopsFormHidden('keyid', $keyid));
    $form->addElement(new XoopsFormText(_AM_KEYWORDS_NAME, 'name', 40, 128, $vals['name']));
    $node_select = new XoopsFormRadio(_AM_KEYWORDS_NODETYPE, 'nodetype');
    $node_select->addOptionArray($nodetypes_select);
    $node_select->setValue($vals['nodetype']);
    $form->addElement($node_select);
    $parent_keys = new KeyFormSelect(_AM_KEYWORDS_PARENT, 'parent');
    $keys = $keywords->getKeys(array(0,1));
    array_unshift($keys, array('keyid'=>'', 'name'=>_AM_KEY_NONE));
    if ($keyid) unset($keys["key$keyid"]); // remove self
    $parent_keys->addOptions($keys);
    $parent_keys->setValue($vals['parent']);
    $form->addElement($parent_keys);
    $relay_keys = new KeyFormSelect(_AM_KEYWORDS_RELAY, 'relay');
    $relays = $keywords->getKeys(array(0,2));
    array_unshift($relays, array('keyid'=>'', 'name'=>_AM_KEY_NONE));
    $relay_keys->addOptions($relays);
    $relay_keys->setValue($vals['relay']);
    $form->addElement($relay_keys);
    $form->addElement(new XoopsFormText(_AM_SORT_WEIGHT, 'weight', 4, 4, $vals['weight']));
    $form->addElement(new XoopsFormDhtmlTextArea(_AM_KEYWORDS_DESC, 'description' , $vals['description']));

    $form->addElement(new XoopsFormButton('' , 'keys', _SUBMIT, 'submit')) ;
    $form->display();
}

function del_keyword_form($keyid) {
    global $keywords;
    $key = $keywords->get($keyid);
    $form = new XoopsThemeForm(_AM_KEYWORDS_REMOVE, 'RemoveForm', 'keywords.php');
    $form->addElement(new XoopsFormLabel(_AM_KEYWORDS_NAME, $key['name']));
    $form->addElement(new XoopsFormLabel(_AM_KEYWORDS_DESC, $key['description']));
    $form->addElement(new XoopsFormLabel(_AM_KEYWORDS_COUNT, sprintf(_AM_KEYWORDS_PRINT, $key['name'], $keywords->count($keyid))));
    $form->addElement(new XoopsFormHidden('keyid', $keyid));
    $form->addElement(new XoopsFormButton('' , 'delkey', _DELETE, 'submit')) ;
    $form->display();
}

function redirect_result($ret, $dest='keywords.php') {
    if ($ret) {
	redirect_header($dest, 1, _AM_DBUPDATED);
    } else {
	redirect_header($dest, 3, _AM_DBUPDATE_FAIL);
    }
    exit;
}
?>