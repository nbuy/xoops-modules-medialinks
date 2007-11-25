<?php
# MediaLinks - Administration fields in contents
# $Id: fields.php,v 1.1 2007/11/25 04:04:55 nobu Exp $

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

if (isset($_POST['field'])) {
    redirect_result(field_update());
} elseif (isset($_POST['delfield'])) {
    $fid = intval($_POST['fid']);
    redirect_result(fields_delete($fid));
    exit;
}

xoops_cp_header();
include "mymenu.php";

switch ($op) {
case 'fields':
default:
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
}
xoops_cp_footer();

function redirect_result($ret, $dest='fields.php') {
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
			       'FieldForm' , 'fields.php');
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
    if (!in_array($fname, array('ctime','mtime','hits', 'poster'))) {
	if ($type=='link') {
	    $form->addElement(new XoopsFormText(_AM_FIELDS_NUMBER, 'def', 4, 4, $vals['def']));
	} else {
	    $form->addElement(new XoopsFormText(_AM_FIELDS_DEF, 'def', 40, 60, $vals['def']));
	}
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
	$op = "<a href='?op=fields&fid=$fid'>"._EDIT."</a>";
	if (preg_match('/^add/', $name) || $data['type']=='keywords') {
	    $op .= " | <a href='?op=delfield&fid=$fid'>"._DELETE."</a>";
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
		if (empty($label)) {
		    $values[0] = $xoopsDB->quoteString($key['name']);
		}
		if (empty($label) || $key['name']==$label) {
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
    global $xoopsDB, $all_types;
    $mediaschema = new MediaSchema();
    $field = $mediaschema->getField($fid);
    if (empty($field)) return;
    $fname = $field['name'];
    $form = new XoopsThemeForm(_AM_FIELDS_DELETE, 'RemoveForm', 'fields.php');
    $form->addElement(new XoopsFormLabel(_AM_FIELDS_LABEL, $field['label']));
    $form->addElement(new XoopsFormLabel(_AM_FIELDS_NAME, $fname));
    $type = preg_replace('/\\((\d+)\\)/', '', $field['type']);
    $form->addElement(new XoopsFormLabel(_AM_FIELDS_TYPE, $all_types[$type]));
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
?>