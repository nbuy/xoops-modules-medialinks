<?php
# medialinks common functions
# $Id: functions.php,v 1.12 2007/12/29 08:51:43 nobu Exp $

include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include_once "perm.php";

global $xoopsDB;
define('INDENT', '-');
define('ACLS', $xoopsDB->prefix('medialinks_access'));
define('KEYS', $xoopsDB->prefix('medialinks_keys'));
define('FIELDS', $xoopsDB->prefix('medialinks_fields'));
define('ATTACH', $xoopsDB->prefix('medialinks_attach'));
define('MAIN', $xoopsDB->prefix('medialinks'));
define('RELAY', $xoopsDB->prefix('medialinks_relation'));
define('MODULE_URL', XOOPS_URL."/modules/".basename(dirname(__FILE__)));

define('NODE_BOTH', 0);
define('NODE_CATEGORY', 1);
define('NODE_KEY', 2);

if(!function_exists("file_get_contents")) {
   function file_get_contents($filename) {
       $fp = fopen($filename, "rb");
       if (!$fp) return false;
       $contents = "";
       while (! feof($fp)) {
	   $contents .= fread($fp, 4096);
       }
       return $contents;
   }
}

class KeyWords {
    var $keys=array();
    var $all=array();

    function load() {
	global $xoopsDB;
	if ($this->keys) return true;
	$keys = array();
	$res = $xoopsDB->query('SELECT * FROM '.KEYS." ORDER BY parent,weight,keyid");
	if ($xoopsDB->getRowsNum($res)==0) return $keys;
	$all = array();
	while ($ent = $xoopsDB->fetchArray($res)) {
	    $id = $ent['keyid'];
	    $par = $ent['parent'];
	    $all[$id] = $ent;
	    if ($par && isset($all[$par])) {
		$all[$par]['child'][] =& $all[$id];
	    } else $keys[] =& $all[$id];
	}
	$this->keys = $keys;
	$this->all = $all;
    }

    function get($id=null) {
	if (empty($this->keys)) $this->load();
	return $id?@$this->all[$id]:$this->all;
    }

    function delete($id) {
	global $xoopsDB;
	$xoopsDB->query("DELETE FROM ".KEYS." WHERE keyid=".$id);
	$xoopsDB->query("DELETE FROM ".RELAY." WHERE keyref=".$id);
	unset($this->all[$id]);
	return true;
    }

    function insert($id, $vals) {
	global $xoopsDB;
	if ($id) {
	    foreach ($vals as $k=>$v) {
		$vals[$k] = "$k=$v";
	    }
	    return $xoopsDB->query('UPDATE '.KEYS.' SET '.join(',', $vals).' WHERE keyid='.$id);
	} else {
	    $keys = array_keys($vals);
	    return $xoopsDB->query('INSERT INTO '.KEYS.'('.join(',', $keys).') VALUES ('.join(',', $vals).')');
	}
    }
    function count($id) {
	global $xoopsDB;
	$res = $xoopsDB->query("SELECT count(keyref) FROM ".RELAY." WHERE keyref=".$id);
	list($n) = $xoopsDB->fetchRow($res);
	return $n;
    }

    function getTree() {
	if (empty($this->keys)) $this->load();
	return $this->keys;
    }

    function getPriKeysID() {
	$keys =& $this->getKeys(0, $this->keys[0]['child']);
	$ids = array();		// get depth first order
	$n = 0;
	foreach ($keys as $key) {
	    $id = $key['keyid'];
	    if ($key['level']) array_splice($ids, $n, 0, $id);
	    else {
		$ids[] = $id;
		$n = count($n)-1;
	    }
	}
	return $ids;
    }

    function getKeys($types=null, $root=null, $child=true, $level=0) {
	if ($types==null) $types = array(0,1,2);
	if ($root==null) {
	    if (empty($this->keys)) $this->load();
	    $root =& $this->keys;
	}
	$ret = array();
	foreach ($root as $key) {
	    if (in_array($key['nodetype'], $types) && $key['weight']) {
		$ret[] = array("keyid"=>$key['keyid'], 'name'=>$key['name'], 'level'=>$level);
	    }
	    if ($child && isset($key['child'])) {
		$temp = $this->getKeys($types, $key['child'], $child, $level+1);
		foreach ($temp as $v) {
		    $ret[] = $v;
		}
	    }
	}
	return $ret;
    }

    function keys_path($id, $find=0, $force=false) {
	global $keywords;
	if (!$id) return null;
	$key = $keywords->get($id);
	$cur = array();
	while ($key) {
	    if ($force || $key['nodetype']!=NODE_CATEGORY) {
		if ($key['relay']) {
		    $relay = $keywords->get($key['relay']);
		    $name = $relay['name'];
		} else {
		    $name = $key['name'];
		}
		$cur[] = array('keyid'=>$key['keyid'],
			       'name'=>htmlspecialchars($name));
	    }
	    if ($key['parent']) $key = $keywords->get($key['parent']);
	    elseif (!$find || $key['keyid']==$find) return array_reverse($cur);
	    else return null;
	}
    }

    function maxDepth($root) {
	if (empty($root)) return 0;
	$depth = 0;
	foreach ($root as $key) {
	    if (isset($key['child'])) {
		$depth = max($depth, $this->maxDepth($key['child']));
	    }
	}
	return $depth+($key['nodetype']!=NODE_CATEGORY?1:0);
    }
}

class KeyFormSelect extends XoopsFormSelect {
    function addOption($value, $name=null){
	if ( $name === null ) $name = $value;
	$this->_options[] = array('name'=>$name, 'value'=>$value);
    }

    function addOptions($options){
	if ( is_array($options) ) {
	    foreach ( $options as $v ) {
		if (!empty($v['level'])) {
		    $pre = str_repeat(INDENT, $v['level'])." ";
		} else $pre = "";
		$this->addOption($v['keyid'], $pre.$v['name']);
	    }
	}
    }
    
    function render(){
	$ret = "<select  size='".$this->getSize()."'".$this->getExtra()."";
	if ($this->isMultiple() != false) {
	    $ret .= " name='".$this->getName()."[]' id='".$this->getName()."[]' multiple='multiple'>\n";
	} else {
	    $ret .= " name='".$this->getName()."' id='".$this->getName()."'>\n";
	}
	foreach ( $this->getOptions() as $opt) {
	    $value = $opt['value'];
	    $name = $opt['name'];
	    $ret .= "<option value='".htmlspecialchars($value, ENT_QUOTES)."'";
	    if (count($this->getValue()) > 0 && in_array($value, $this->getValue())) {
		$ret .= " selected='selected'";
	    }
	    $ret .= ">".$name."</option>\n";
	}
	$ret .= "</select>";
	return $ret;
    }
}

class MediaSchema {
    var $fields = array();
    var $ref = array();
    function MediaSchema() {
	global $xoopsDB;
	$fields = &$this->fields;
	$ref = &$this->ref;
	$res = $xoopsDB->query('SELECT * FROM '.FIELDS.' ORDER BY weight,fid');
	while ($data = $xoopsDB->fetchArray($res)) {
	    $id = $data['fid'];
	    $fields[$id] = $data;
	    $ref[$data['name']] = &$fields[$id];
	}
    }

    function getField($name=null) {
	if (empty($name)) return $this->ref;
	elseif (preg_match('/^\d+$/',$name)) return $this->fields[$name];
	else return $this->ref[$name];
    }
}

//
// media content holder class
//
class MediaContent {
    var $vars = array('weight'=>1);
    var $dirty = array();
    var $keys = array();
    var $kdirty = false;
    var $attach = array();
    var $adirty = array();
    var $access = array();

    function MediaContent($id=0) {
	global $xoopsModuleConfig, $xoopsUser;
	$this->vars['status'] = $xoopsModuleConfig['postauth']?'W':'N';
	$this->vars['poster']=is_object($xoopsUser)?$xoopsUser->getVar('uid'):0;
	if ($id) $this->load($id);
    }

    function load($id, $sel=null) {
	global $xoopsDB, $xoopsUser, $xoopsModule;
	if (is_object($xoopsUser)) {
	    $uid = $xoopsUser->getVar('uid');
	    $isadmin = $xoopsUser->isAdmin($xoopsModule->getVar('mid'));
	    if ($isadmin) {
		$cond = "";
	    } else {
		$cond = " AND (status='N' OR (status='W' AND poster=$uid))";
	    }
	} else {
	    $cond = " AND status='N'";
	    $uid = 0;
	    $isadmin = false;
	}

	if ($id) {
	    $res = $xoopsDB->query("SELECT * FROM ".MAIN." WHERE mid=$id".$cond);
	} else {
	    $res = $sel;
	}
	if ($res) {
	    $vars = $xoopsDB->fetchArray($res);
	    // need check access control
	    if (!$isadmin && $vars['nacl']>0) {
		if (isset($vars['writable'])) {
		    if (empty($vars['writable'])) return false;
		} else {
		    $res = $xoopsDB->query("SELECT writable FROM ".ACLS." WHERE amid=$id AND auid=$uid");
		    if (!$res || $xoopsDB->getRowsNum($res)==0) return false;
		    list($vars['writable']) = $xoopsDB->fetchRow($res);
		}
	    }
	    $this->vars = $vars;
	    $id = $this->getVar('mid');
	    // load keywords
	    $res = $xoopsDB->query("SELECT keyref FROM ".RELAY." WHERE midref=".$id);
	    if ($res) {
		$keys = array();
		while (list($keyid) = $xoopsDB->fetchRow($res)) {
		    $keys[] = $keyid;
		}
		sort($keys);
		$this->keys = $keys;
		$this->kdirty = false;
	    }
	}
	if ($res) {
	    $res = $xoopsDB->query("SELECT * FROM ".ATTACH." WHERE midref=$id ORDER BY weight,linkid");
	    $attach = array();
	    while ($data = $xoopsDB->fetchArray($res)) {
		$attach[$data['linkid']] = $data;
	    }
	    $this->attach = $attach;
	    $this->adirty = array();
	}
	return $res;
    }

    function setVar($name, $value=null) {
	if (is_array($name)) {
	    foreach ($name as $k=>$v) {
		$this->setVar($k, $v);
	    }
	} else {
	    if (isset($this->vars[$name])) {
		if ($this->vars[$name] != $value) {
		    $this->vars[$name] = $value;
		    if (!in_array($name, $this->dirty)) {
			$this->dirty[] = $name;
		    }
		}
	    } else {
		$this->vars[$name] = $value;
		$this->dirty[] = $name;
	    }
	}
    }

    function getVar($name) {
	if (empty($name)) return $this->vars;
	else return @$this->vars[$name];
    }

    function setKeywords($keys) {
	sort($keys);
	$change = false;
	if (count($this->keys)!=count($keys)) $change = true;
	else {
	    foreach ($keys as $i=>$v) {
		if ($this->keys[$i] != $v) {
		    $change =true;
		    break;
		}
	    }
	}
	if ($change) {
	    $this->keys = $keys;
	    $this->kdirty = true;
	}
    }

    function getKeywords() {
	return $this->keys;
    }

    function getField($name=null) {
	global $mediaschema;
	if (empty($mediaschema)) {
	    $mediaschema = new MediaSchema();
	}
	return $mediaschema->getField($name);
    }

    function store() {
	global $xoopsDB, $xoopsUser, $xoopsModule;
	$vars = &$this->vars;
	$dirty = &$this->dirty;
	$notify = false;
	function dbquote($x) {
	    global $xoopsDB;
	    return $xoopsDB->quoteString($x);
	}
	if (empty($vars['mid'])) { // new entry
	    unset($vars['mid']);
	    $vars['ctime']=$vars['mtime']=time();
	    $fields = join(',', array_keys($vars));
	    $values = join(',', array_map('dbquote', $vars));
	    $res = $xoopsDB->query("INSERT INTO ".MAIN."($fields) VALUES($values)");
	    if ($res) {
		$notify = true;
		$vars['mid'] = $mid = $xoopsDB->getInsertId();
		$work = get_upload_path(0);
		if (is_dir($work)) rename($work, get_upload_path($mid));
		$this->dirty = array();
		if ($this->kdirty) {
		    $sql = "INSERT INTO ".RELAY."(keyref, midref) VALUES(%u, $mid)";
		    foreach ($this->keys as $k) {
			$xoopsDB->query(sprintf($sql, $k));
		    }
		}
	    }
	} else {		// update entry
	    $mid = $vars['mid'];
	    // something to change?
	    if (empty($dirty)&&!$this->kdirty&&!$this->adirty) return false;
	    $vars['mtime'] = time();
	    $dirty[] = 'mtime';
	    $sets = array();
	    foreach ($dirty as $k) {
		$sets[] = $k.'='.dbquote($vars[$k]);
	    }
	    $set = join(',', $sets);
	    $res = $xoopsDB->query("UPDATE ".MAIN." SET $set WHERE mid=".$mid);
	    if ($res) {
		$this->dirty = array();
		if ($this->kdirty) {
		    $xoopsDB->query("DELETE FROM ".RELAY." WHERE midref=$mid");
		    $sql = "INSERT INTO ".RELAY."(keyref, midref) VALUES(%u, $mid)";
		    foreach ($this->keys as $k) {
			$xoopsDB->query(sprintf($sql, $k));
		    }
		}
	    }
	}
	// save attachment
	if ($res) {
	    $adirty = &$this->adirty;
	    $attach = &$this->attach;
	    if (count($adirty)) {
		foreach ($adirty as $id) {
		    if ($id) {
			if (!$this->storeAttach($attach[$id])) return false;
		    } else {
			foreach ($attach as $k => $data) {
			    if (empty($data['keyid'])) {
				$res = $this->storeAttach($data);
				if ($res) {
				    unset($attach[$k]);
				    $data['keyid'] = $res;
				    $attach[$res]=$data;
				} else return false;
			    }
			}
		    }
		    if (!$res) return false;
		    unset($adirty[$id]);
		}
	    }
	    $tags = array('TITLE'=>$this->getVar('title'),
			  'POSTER'=>$xoopsUser->getVar('uname'),
			  'URL'=>XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/detail.php?mid='.$this->getVar('mid'));
	    $this->admin_notify(_MD_NOTIFY_SUBJECT, $tags);
	}
	return $mid;
    }

    function admin_notify($subj, $tags) {
	global $xoopsModule, $xoopsConfig, $xoopsModuleConfig, $xoopsUser;
	if (!$xoopsModuleConfig['notify_admin']) return false;
	$xoopsMailer =& getMailer();
	$xoopsMailer->useMail();
	$xoopsMailer->setSubject($subj);
	$tags['X_MODULE'] = $xoopsModule->getVar('name');
	$xoopsMailer->assign($tags);
	$tpl = 'notify_admin.tpl';
	$xoopsMailer->setTemplateDir(template_dir($tpl));
	$xoopsMailer->setTemplate($tpl);
	$member_handler =& xoops_gethandler('member');
	$users = $member_handler->getUsersByGroup($xoopsModuleConfig['notify_group'], true);
	$uid = $xoopsUser->getVar('uid');
	foreach ($users as $user) {
	    if ($user->getVar('uid') != $uid) {
		$xoopsMailer->setToUsers($user);
	    }
	}
	$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
	$xoopsMailer->setFromName($xoopsModule->getVar('name'));
	return $xoopsMailer->send();
    }

    function getAttach($type=null, $exp=false) {
	if ($type==null) return $this->attach;
	$id = intval($type);
	if ($id) return $this->attach[$id];
	$attach = array();
	$type = strtolower($type);
	$mid = $this->getVar('mid');
	foreach ($this->attach as $data) {
	    if ($exp) {
		if (empty($data['name'])) $data['name']=basename($data['url']);
		$data['url'] = get_upload_url($mid, $data['url']);
		if ($type=='m' && count($attach)==0 &&
		    file_exists(get_upload_path($mid, _ML_SHOTNAME))) {
		    $data['shotimg'] = get_upload_url($mid, _ML_SHOTNAME);
		}
	    }
	    if (strtolower($data['ltype']) == $type) $attach[] = $data;
	}
	return $attach;
    }

    function setAttach($data) {
	if (empty($data['linkid'])) {
	    $this->attach[]=$data;
	    $this->adirty[0] = 0;
	} else {
	    $id = intval($data['linkid']);
	    $cur = $this->getAttach($id);
	    $same = true;
	    foreach ($data as $k=>$v) {
		if ($cur[$k] != $v) {
		    $same = false;
		    break;
		}
	    }
	    if ($same) return false;
	    $this->attach[$id]=$data;
	    $this->adirty[$id] = $id;
	}
	return true;
    }

    function storeAttach($data) {
	global $xoopsDB;
	$sets = array(
	    'midref'=> $this->getVar("mid"),
	    'name' => $xoopsDB->quoteString($data['name']),
	    'url' => $xoopsDB->quoteString($data['url']),
	    'ltype' => $xoopsDB->quoteString($data['ltype']),
	    'weight' => intval($data['weight']));

	if (empty($data['linkid'])) {
	    $fields = join(',', array_keys($sets));
	    $values = join(',', $sets);
	    $res = $xoopsDB->query("INSERT INTO ".ATTACH."($fields) VALUES($values)");
	    if ($res) $res = $xoopsDB->getInsertId();
	} else {
	    $id = $data['linkid'];
	    foreach ($sets as $k=>$v) {
		$sets[$k] = "$k=$v";
	    }
	    $set = join(',', $sets);
	    $res = $xoopsDB->query("UPDATE ".ATTACH." SET $set WHERE linkid=".$id);
	    if ($res) $res = $id;
	}
	return $res;
    }

    function delAttach($id) {
	global $xoopsDB;
	if (!isset($this->attach[$id])) return false;
	$res = $xoopsDB->query("DELETE FROM ".ATTACH." WHERE linkid=".$id);
	if ($res) {
	    $file = get_upload_path($this->getVar('mid'), $this->attach[$id]['url']);
	    if ($file) unlink($file);
	    unset($this->attach[$id]);
	    $this->adirty[$id] = 0;
	}
	return $res;
    }

    function hits() {
	global $xoopsUser, $xoopsDB;
	if (is_object($xoopsUser)&&
	    $this->getVar('poster')==$xoopsUser->getVar('uid')) return;
	$xoopsDB->queryF("UPDATE ".MAIN." SET hits=hits+1 WHERE mid=".$this->getVar('mid'));
	$this->vars['hits']++;
    }

    function dispVars($admin=true, $exp=true) {
	global $xoopsUser, $xoopsModule, $xoopsConfig, $keywords;
	$myts =& MyTextSanitizer::getInstance();
	$keys = $this->getKeywords();
	$mod = is_object($xoopsModule)?$xoopsModule->getVar('mid'):0;
	$isadmin = $admin&& ((is_object($xoopsUser)&&$xoopsUser->isAdmin($mod))
			     || $this->getVar('writable')=='Y');

	$fields = array('mid'=> $mid = $this->getVar('mid'),
			'status' => $this->getVar('status'),
			'isadmin'=> $isadmin);

	foreach ($this->getField() as $k=>$field) {
	    if ($field['weight']==0 || $field['name']=='weight') continue;
	    $v = $this->getVar($k);
	    switch ($field['type']) {
	    case 'link':
		$v = $this->getAttach(substr($field['name'], 0, 1), $exp);
		break;
	    case 'keywords':
		if (preg_match('/\\[(\d+)\\]$/', $field['name'], $d)) {
		    $id = $d[1];
		    if (empty($keywords)) $keywords = new KeyWords();
		    $words = $this->find_keylist($id);
		    foreach ($words as $kw=>$v) {
			$words[$kw] = $v['name'];
		    }
		    $v = join(_MD_KEY_SEP,$words);
		} else {
		    $v = '';
		}
		break;
	    case 'text':
		switch ($this->getVar('style')) {
		case 'b': $html=1; $br=1; break;
		case 'h': $html=1; $br=0; break;
		case 'n': $html=0; $br=1; break;
		}
		$field = $myts->displayTarea($v, $html, 1, 1, 1, $br);
		break;
	    case 'timestamp':
		if (empty($v)) $v = time();
		$v = formatTimestamp($v);
		break;
	    case 'user':
		if ($v) {
		    if (is_object($xoopsUser)&&$xoopsUser->getVar('uid')==$v) {
			$isadmin=$admin;
		    }
		    $fields['isadmin'] = $isadmin;
		    $v = "<a href='".XOOPS_URL."/userinfo.php?uid=$v'>".XoopsUser::getUnameFromId($v)."</a>";
		} else {
		    $v = $xoopsConfig['anonymous'];
		}
		break;
	    case 'integer':
		break;
	    default:
		$v = htmlspecialchars($v);
	    }
	    if (is_array($field)) {
		$field['label'] = preg_replace('/\\*$/', '', $field['label']);
		if ($field['name'] == 'title') $field = htmlspecialchars($v);
		else $field['value'] = $v;
	    }
	    $fields[$k] = $field;
	}
	return $fields;
    }

    function find_keylist($find=0) {
	global $keywords;
	if (empty($keywords)) $keywords = new KeyWords();
	if (!$find) {
	    foreach ($keywords->getTree() as $key) {
		if ($key['weight']) {
		    $find = $key['keyid'];
		    break;
		}
	    }
	}
	if (!$find) return false;
	$words = $this->getKeywords();
	$keys = array();
	foreach ($words as $id) {
	    $cur = $keywords->keys_path($id, $find);
	    if (count($keys)<count($cur)) $keys = $cur;
	}
	return $keys;
    }

    function keys_path($id=0, $find=0, $force=false) {
	global $keywords;
	if (!$id) return $this->find_keylist();
	if (empty($keywords)) $keywords = new KeyWords();
	return $keywords->keys_path($id, $find, $force);
    }
}

function template_dir($file='') {
    global $xoopsConfig;
    $lang = $xoopsConfig['language'];
    $dir = dirname(__FILE__).'/language/%s/mail_template/%s';
    $path = sprintf($dir,$lang, $file);
    if (file_exists($path)) {
	$path = sprintf($dir,$lang, '');
    } else {
	$path = sprintf($dir,'english', '');
    }
    return $path;
}

function find_root_id($keyid, $dep = false) {
    global $keywords;
    $depth = 0;
    do {
	$key = $keywords->get($keyid);
	if (empty($key['parent'])) break;
	$depth++;
	$keyid = $key['parent'];
    } while (!empty($keyid));
    return $dep?$depth:$keyid;
}

function keys_expand($mid, $sep="-") {
    global $xoopsDB, $keywords;
    if (empty($keywords)) {
	require_once dirname(__FILE__)."/functions.php";
	$keywords = new Keywords();
    }
    $kres = $xoopsDB->query("SELECT keyref FROM ".RELAY." WHERE midref=".$mid);
    $keys = array();
    foreach ($keywords->getTree() as $key) { // roots order
	$keys[$key['keyid']] = array();
    }
    while (list($keyid)=$xoopsDB->fetchRow($kres)) {
	$root = find_root_id($keyid);
	$depth = find_root_id($keyid, true);
	$key = $keywords->get($keyid);
	if (!empty($key['keyid'])) $keys[$root][$depth]=$key['name'];
    }
    foreach ($keys as $id=>$vals) {
	if ($vals) $keys[$id] = join($sep, $vals);
	else unset($keys[$id]);
    }
    return $keys;
}

function set_ml_breadcrumbs($keypath=array(), $adds=array()) {
    global $xoopsModule, $xoopsTpl;
    $breadcrumbs = array();
    $breadcrumbs[] = array('url'=>MODULE_URL.'/',
			   'name'=>$xoopsModule->getVar('name'));
    if (empty($keypath)) return;
    foreach ($keypath as $key) {
	$breadcrumbs[]=array('url'=>MODULE_URL.'/index.php?keyid='.$key['keyid'],
			     'name'=>$key['name']);
    }
    $xoopsTpl->assign('xoops_breadcrumbs', array_merge($breadcrumbs, $adds));
}

function ml_mysubstr($text, $len) {
    if ($len==0 || strlen($text)<$len) return $text;
    if (XOOPS_USE_MULTIBYTES) {
	if (function_exists('mb_strcut')) {
	    return mb_strcut($text, 0, $len-1, _CHARSET)."...";
	}
    } else {
	return substr($text, 0, $len-1)."...";
    }
    return $text;
}

function ml_index_view($order, $trim, $keyid=0, $verb=0, $max=0, $start=0, $fmt='s') {
    global $xoopsDB, $xoopsUser, $keywords;

    $myts =& MyTextSanitizer::getInstance();
    $modpath = dirname(__FILE__);
    $dirname = basename($modpath);
    $modurl = XOOPS_URL."/modules/$dirname";

    $uid = is_object($xoopsUser)?$xoopsUser->getVar('uid'):0;

    $module_handler =& xoops_gethandler('module');
    $module =& $module_handler->getByDirname($dirname);
    $isadmin = $uid&&$xoopsUser->isAdmin($module->getVar('mid'));
    $cond = "status='N'";
    if ($keyid) {
	$kcond = 'keyref IN ('.join(',', array_map('intval', explode(',', $keyid))).')';
	$res = $xoopsDB->query("SELECT midref FROM ".RELAY." WHERE ".$kcond);
	$mids = array();
	while (list($mid) = $xoopsDB->fetchRow($res)) {
	    $mids[] = $mid;
	}
	$mids = array_unique($mids);
	if (count($mids)) $cond .= " AND mid IN (".join(',', $mids).")";
	else return array();
    }
    $acl = "";
    if (!$isadmin) {		// check access control
	$acl = " LEFT JOIN ".$xoopsDB->prefix('medialinks_access').
	    " ON amid=mid AND auid=$uid";
	$cond .= " AND (nacl=0 OR auid>0)";
    }
    $sql = " FROM ".$xoopsDB->prefix('medialinks')." $acl WHERE $cond";
    $result = $xoopsDB->query("SELECT count(mid) $sql");
    list($n) = $xoopsDB->fetchRow($result);
    $result = $xoopsDB->query("SELECT mid, title, description, ctime, mtime, poster, hits $sql ORDER BY $order", $max, $start);
    if (!$result || $xoopsDB->getRowsNum($result)==0) return null;

    $len = ($verb==0)?$trim:0; // only trim short style
    if ($verb==2) require_once $modpath."/screenshot.php";

    $dirname = basename(dirname(__FILE__));
    $modurl = XOOPS_URL."/modules/$dirname";
    $media = array('order'=>preg_replace('/[\s,].*$/', '', $order),
		   'count' => $n,
		   'verbose'=>$verb,
		   'dirname'=>$dirname,
		   'module_url'=>$modurl);
    $contents = array();
    while ($myrow = $xoopsDB->fetchArray($result)) {
	$myrow['title'] = ml_mysubstr($myrow['title'], $len);
	$desc = $myts->displayTarea($myrow["description"]);
	$myrow['description'] = $desc;
	$myrow['shortdesc'] = ml_mysubstr(strip_tags($desc), 50);
	$myrow['cdate'] = formatTimestamp($myrow['ctime'], $fmt);
	$myrow['mdate'] = formatTimestamp($myrow['mtime'], $fmt);
	$myrow['uname'] = XoopsUser::getUnameFromId($myrow['poster']);
	if ($verb==2) $myrow['image'] = ml_screenshot($myrow['mid']);

	// keywords expand
	$myrow['keywords'] = keys_expand($myrow['mid']);

	$contents[] = $myrow;
    }
    $media['contents'] = $contents;
    return $media;
}
?>