<?php
# MediaLinks - Administration
# $Id: index.php,v 1.3 2006/07/13 08:36:14 nobu Exp $

include '../../../include/cp_header.php';
include_once '../functions.php';
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';

// system default fields count (see oninstall.php $fields)
define('FIELD_OFFSET', 9);
$keywords = new KeyWords();
$field_types = array('varchar'	=> _AM_TYPE_STRING,
		     'integer'	=> _AM_TYPE_INTEGER,
		     'date'	=> _AM_TYPE_DATE,
		     'text'	=> _AM_TYPE_TEXT);
$all_types = $field_types;
$all_types['keywords']	= _AM_TYPE_KEYWORD;
$all_types['link'] = _AM_TYPE_LINK;
$all_types['timestamp'] = _AM_TYPE_TIMESTAMP;
$all_types['user'] = _AM_TYPE_UID;

$op = isset($_GET['op'])?$_GET['op']:'';

if (isset($_POST['keys'])) {
    $myts =& MyTextSanitizer::getInstance();
    $keys = array('name', 'weight', 'description');
    $nodetype = intval($_POST['nodetype']);
    if ($nodetype != 1) $rel = 0; // only for category
    $vals = array('nodetype'=>$nodetype,
		  'parent'=>intval($_POST['parent']),
		  'relay'=>($nodetype!=1)?0:intval($_POST['relay']));
    foreach ($keys as $k) {
	$vals[$k] = $xoopsDB->quoteString($myts->stripSlashesGPC($_POST[$k]));
    }
    $keyid = intval($_POST['keyid']);
    redirect_result($keywords->insert($keyid, $vals), 'index.php?op=keys');
} elseif (isset($_POST['delkey'])) {
    $keyid = intval($_POST['keyid']);
    redirect_result($keywords->delete($keyid), 'index.php?op=keys');
} elseif (isset($_POST['field'])) {
    redirect_result(field_update(), 'index.php?op=fields');
    
} elseif (isset($_POST['delfield'])) {
    $fid = intval($_POST['fid']);
    redirect_result(fields_delete($fid), 'index.php?op=fields');
    exit;
} elseif (isset($_POST['dels'])) {
    redirect_result(contents_delete(), 'index.php?op=list');
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
	redirect_result($res, 'index.php');
    }
}


xoops_cp_header();
include "mymenu.php";

$keyid = isset($_GET['keyid'])?intval($_GET['keyid']):0;

switch ($op) {
case 'list':
default:
    echo "<h2>"._AM_CONTENTS_ADMIN."</h2>\n";
    contents_list();
    break;
case 'del':
    contents_delete_form();
    break;
case 'keys':
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

case 'fields':
    $fid = (isset($_GET['fid'])?$_GET['fid']:0);
    $mediaschema = new MediaSchema();
    if (!$fid) {
	fields_list();
	echo "<hr/>";
    }
    field_form($fid);
    break;
case 'delfield':
    delfield_form($_GET['fid']);
    break;

case 'about':
    display_lang_file('help.html');
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
    }
    return $res;
}

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
	    $admin = "[ <a href='index.php?op=delkey&keyid=$id'>"._DELETE."</a> ]";
	} else {
	    $admin = "";
	}
	echo "<li><a href='index.php?op=editkey&keyid=$id'>".
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
			       'KeysForm' , 'index.php');
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
    $form = new XoopsThemeForm(_AM_KEYWORDS_REMOVE, 'RemoveForm', 'index.php');
    $form->addElement(new XoopsFormLabel(_AM_KEYWORDS_NAME, $key['name']));
    $form->addElement(new XoopsFormLabel(_AM_KEYWORDS_DESC, $key['description']));
    $form->addElement(new XoopsFormLabel(_AM_KEYWORDS_COUNT, sprintf(_AM_KEYWORDS_PRINT, $key['name'], $keywords->count($keyid))));
    $form->addElement(new XoopsFormHidden('keyid', $keyid));
    $form->addElement(new XoopsFormButton('' , 'delkey', _DELETE, 'submit')) ;
    $form->display();
}

function redirect_result($ret, $dest='index.php') {
    if ($ret) {
	redirect_header($dest, 1, _AM_DBUPDATED);
    } else {
	redirect_header($dest, 3, _AM_DBUPDATE_FAIL);
    }
    exit;
}

function field_form($fid=0) {
    global $mediaschema, $field_types, $all_types;
    if ($fid) {
	$vals = $mediaschema->getField($fid);
    } else {
	$vals = array('fid'=>0,'name'=>'','label'=>'', 'def'=>'',
		      'type'=>'varchar(0)', 'weight'=>1);
    }
    if (preg_match('/^(\w+)\\((\d+)\\)/', $vals['type'], $d)) {
	$type = $d[1];
	$size = $d[2];
    } else {
	$type = $vals['type'];
	$size = 0;
    }
    $form = new XoopsThemeForm($fid?_AM_FIELDS_EDIT:_AM_FIELDS_NEW,
			       'FieldForm' , 'index.php');
    $form->addElement(new XoopsFormHidden('fid', $vals['fid']));
    $form->addElement(new XoopsFormText(_AM_FIELDS_LABEL, 'label', 40, 40, $vals['label']));
    $fname = $vals['name'];
    if (empty($fname) || preg_match('/^add/', $fname)) {
	$types = $field_types;
	if (empty($fname)) {
	    $types['keywords'] = _AM_TYPE_KEYWORD;
	} else {
	    $form->addElement(new XoopsFormLabel(_AM_FIELDS_NAME, $fname));
	}
	$type_select = new XoopsFormRadio(_AM_FIELDS_TYPE, 'type', $type);
	$type_select->addOptionArray($types);
	$form->addElement($type_select);
	$form->addElement(new XoopsFormText(_AM_FIELDS_SIZE, 'size', 4, 4, $size?$size:40));
    } else {
	$form->addElement(new XoopsFormLabel(_AM_FIELDS_NAME, $fname));
	$form->addElement(new XoopsFormLabel(_AM_FIELDS_TYPE, $all_types[$type].($size?" ($size)":'')));
	
    }
    if (!in_array($fname, array('ctime','mtime','hits', 'poster')) &&
	$type!='link') {
	$form->addElement(new XoopsFormText(_AM_FIELDS_DEF, 'def', 40, 60, $vals['def']));
    }
    $form->addElement(new XoopsFormText(_AM_SORT_WEIGHT, 'weight', 4, 4, $vals['weight']));
    $form->addElement(new XoopsFormButton('' , 'field', _SUBMIT, 'submit')) ;
    $form->display();
}

function fields_list() {
    global $mediaschema, $all_types;
    
    echo "<table class='outer' cellspacing='1' cellpadding='4'>\n";
    echo "<tr><th>"._AM_FIELDS_LABEL."</th><th>"._AM_FIELDS_NAME."</th><th>".
	_AM_FIELDS_TYPE."</th><th>"._AM_SORT_WEIGHT."</th><th>".
	_AM_FIELDS_DEF."</th><th>"._AM_FIELDS_OPERATION."</th></tr>\n";
    $n = 0;
    foreach ($mediaschema->getField() as $data) {
	$type = $data['type'];
	$size = 0;
	if (preg_match('/^(\w+)\\((\d+)\\)/', $type, $d)) {
	    $type = $d[1];
	    $size = $d[2];
	}
	if (isset($all_types[$type])) $type = $all_types[$type];
	if ($size) $type .= " ($size)";
	$bg = $n++%2?'even':'odd';
	$fid = $data['fid'];
	$name = htmlspecialchars($data['name']);
	$def = htmlspecialchars($data['def']);
	$op = "<a href='index.php?op=fields&fid=$fid'>"._EDIT."</a>";
	if (preg_match('/^add/', $name) || $data['type']=='keywords') {
	    $op .= " | <a href='index.php?op=delfield&fid=$fid'>"._DELETE."</a>";
	}
	echo "<tr class='$bg'><td class='head'>".htmlspecialchars($data['label']).
	    "</td><td>$name</td><td>$type</td><td align='center'>".$data['weight'].
	    "</td><td>$def</td><td>$op</td></tr>\n";
    }
    if ($n) echo "</table>\n";
}

function field_update() {
    global $xoopsDB, $keywords;
    $myts =& MyTextSanitizer::getInstance();
    $fid = intval($_POST['fid']);
    $label = $myts->stripSlashesGPC($_POST['label']);
    $weight = intval($_POST['weight']);
    if (isset($_POST['type'])) {
	$type = $myts->stripSlashesGPC($_POST['type']);
	$size = intval($_POST['size']);
	if ($type == 'varchar') $type = $type."($size)";
    }
    if (isset($_POST['def'])) $def = $myts->stripSlashesGPC($_POST['def']);
    else $def='';
    if ($fid) {			// update
	$res = $xoopsDB->query('SELECT * FROM '.FIELDS." WHERE fid=".$fid);
	if (!$res || $xoopsDB->getRowsNum($res)==0) return false;
	$field = $xoopsDB->fetchArray($res);
	$update = array();
	if ($field['label']!=$label) $update[] =  'label='.$xoopsDB->quoteString($label);
	if ($field['weight']!=$weight) $update[] =  'weight='.$weight;
	if ($field['def']!=$def) $update[] =  'def='.$xoopsDB->quoteString($def);
	if ($update) {
	    $res = $xoopsDB->query($x="UPDATE ".FIELDS." SET ".join(',', $update)." WHERE fid=".$fid);
	} else {
	    $res = true;
	}
	if ($res&&$fid>FIELD_OFFSET&&isset($type)&&$field['type']!=$type) {
	    $name = $field['name'];
	    $res = $xoopsDB->query('ALTER TABLE '.MAIN." CHANGE COLUMN $name $name $type");
	    if (!$res) return false;
	    $res = $xoopsDB->query('UPDATE '.FIELDS." SET type=".$xoopsDB->quoteString($type)." WHERE fid=".$fid);
	}
	return $res;
    } else {			// new
	$fields = array("label", "type", "def", "weight");
	$values = array($xoopsDB->quoteString($label),
			$xoopsDB->quoteString($type),
			$xoopsDB->quoteString($def), $weight);

	if ($type == 'keywords') {
	    $keys = array();
	    if (empty($name)) {
		$res = $xoopsDB->query("SELECT name FROM ".FIELDS." WHERE name like 'keywords[%'");
		while (list($name)=$xoopsDB->fetchRow($res)) {
		    $keys[] = intval(preg_replace('/^keywords\\[/', '', $name));
		}
		sort($keys);
	    }
	    $name = '';
	    $first = 0;
	    foreach ($keywords->getTree() as $key) {
		$id = $key['keyid'];
		if (in_array($id, $keys)) continue; // already use
		if (empty($label) || $key['name']==$label) {
		    $label = $key['name'];
		    $name = "keywords[$id]";
		    break;
		}
		if (!$first) $first = $id;
	    }
	    if (empty($name)) {
		if (!$first) return false;	// no more keywords
		$name = "keywords[$first]";
	    }
	    $fields[] = "name";
	    $values[] = $xoopsDB->quoteString($name);
	}
	$res = $xoopsDB->query("INSERT INTO ".FIELDS."(".join(',',$fields).")
 VALUES(".join(',',$values).")");
	if ($res) {
	    $fid = $xoopsDB->getInsertId();
	    if ($type == 'keywords') return $fid;
	    $name = sprintf('add%02d', $fid-FIELD_OFFSET);
	    $xoopsDB->query("ALTER TABLE ".MAIN." ADD COLUMN $name $type");
	    $xoopsDB->query("UPDATE ".FIELDS." SET name='$name' WHERE fid=$fid");
	    return true;
	}
    }
    return false;
}

function fields_delete($fid) {
    global $xoopsDB;
    $res = $xoopsDB->query("SELECT name FROM ".FIELDS." WHERE fid=$fid");
    if (!$res) return false;
    list($name) = $xoopsDB->fetchRow($res);
    if (preg_match('/^add/', $name)) {
	$res = $xoopsDB->query("ALTER TABLE ".MAIN." DROP COLUMN $name");
	if (!$res) return false;
    }
    return $xoopsDB->query("DELETE FROM ".FIELDS." WHERE fid=$fid");
}

function delfield_form($fid) {
    global $xoopsDB, $field_types;
    $mediaschema = new MediaSchema();
    $field = $mediaschema->getField($fid);
    if (empty($field)) return;
    $fname = $field['name'];
    $form = new XoopsThemeForm(_AM_FIELDS_DELETE, 'RemoveForm', 'index.php');
    $form->addElement(new XoopsFormLabel(_AM_FIELDS_LABEL, $field['label']));
    $type = preg_replace('/\\((\d+)\\)/', '', $field['type']);
    $form->addElement(new XoopsFormLabel(_AM_FIELDS_NAME, $fname));
    $form->addElement(new XoopsFormLabel(_AM_FIELDS_TYPE, $field_types[$type]));
    if (preg_match('/^add/', $fname)) {
	$res = $xoopsDB->query("SELECT count(mid) FROM ".MAIN." WHERE $fname IS NOT NULL AND $fname<>''");
	list($count) = $xoopsDB->fetchRow($res);
	if ($count) {
	    $count = "<div class='confirmMsg'>".sprintf(_AM_FIELDS_COUNT_NOTICE, $count)."</div>";
	}
	$form->addElement(new XoopsFormLabel(_AM_FIELDS_COUNT, $count));
    }
    $form->addElement(new XoopsFormHidden('fid', $field['fid']));
    $form->addElement(new XoopsFormButton('' , 'delfield', _DELETE, 'submit')) ;
    $form->display();
}

function display_lang_file($file, $link='') {
    global $xoopsConfig;
    if (empty($link)) {
	$link = preg_replace('/&?file=[^&]*|\?$/', '', $_SERVER['REQUEST_URI']);
	$link .= preg_match('/\?/', $link)?'&':'?';
	$link .= 'file=';
    }
    $file = preg_replace('/^\/+/','',preg_replace('/\/?\\.\\.?\/|\/+/', '/', $file));
    $lang = "language/".$xoopsConfig['language'];
    $help = "../$lang/$file";
    if (!file_exists($help)) {
	$lang = 'language/english';
	$help = "../$lang/$file";
    }
    $content = file_get_contents($help);
    list($h, $b) = preg_split('/<\/?body>/', $content);
    if (empty($b)) $b =& $content;

    // link image
    // need quote! (sence has protocol)
    // follow only 1 level depth folder
    $pat = array('/\ssrc=\'([^#][^\':]*)\'/',
		 '/\ssrc="([^#][^":]*)"/',
		 '/\shref=\'([^#][^\':]*)\'/',
		 '/\shref="([^#][^\':]*)"/');
    $rep = array(" src='../$lang/\$1'",
		 " src=\"../$lang/\$1\"",
		 " href='$link\$1'",
		 " href=\"$link\$1\"");
    echo preg_replace($pat, $rep, $b);
}
?>