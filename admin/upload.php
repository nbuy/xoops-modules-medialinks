<?php
# uploads folder maintainace
# $Id: upload.php,v 1.3 2009/12/13 11:25:00 nobu Exp $

include '../../../include/cp_header.php';

$myts =& MyTextSanitizer::getInstance();

$uppath = $xoopsModuleConfig['upload_path'];
if (preg_match('/^\//', $uppath)) {
    define('UPLOAD_BASE', $uppath);
    define('UPLOAD_URL', XOOPS_URL."/$uppath");
} else {
    define('UPLOAD_BASE', XOOPS_UPLOAD_PATH."/$uppath");
    define('UPLOAD_URL', XOOPS_UPLOAD_URL."/$uppath");
}
if (isset($_POST['dels'])) {
    $n = 0;
    foreach ($_POST['dels'] as $del) {
	$rmfile = UPLOAD_BASE.$del;
	if (is_dir($rmfile)) {
	    if (!@rmdir($rmfile)) $n++;
	} else {
	    if (!@unlink($rmfile)) $n++;
	}
    }
    $dir = dirname($del);
    if ($n) {
	redirect_header(location_folder($dir,false), 3, _AM_FILE_ERROR);
	exit;
    }
    location_folder($dir);
} elseif (isset($_POST['upfile'])) {
    upload_store();
} elseif (isset($_POST['mkdir'])) {
    $dir = filename_filter($myts->stripSlashesGPC($_POST['dir']));
    $newdir = filename_filter("$dir/".$myts->stripSlashesGPC($_POST['mkdir']));
    mkdir(UPLOAD_BASE.$newdir);
    location_folder($dir);
} elseif (isset($_POST['move'])) {
    $old = $myts->stripSlashesGPC($_POST['file']);
    $new = preg_replace('/\/*$/','',filename_filter($myts->stripSlashesGPC($_POST['dir'])).'/'.
	$myts->stripSlashesGPC($_POST['new']));
    $dir = dirname($old);
    if (file_exists(UPLOAD_BASE.$new)) {
	redirect_header(location_folder($dir,false), 3, _AM_FILE_DUP_ERROR);
	exit;
    }
    if ($old!=$new && !@rename(UPLOAD_BASE.$old, UPLOAD_BASE.$new)) {
	redirect_header(location_folder($dir,false), 3, _AM_FILE_ERROR);
	exit;
    }
    location_folder($dir);
}

xoops_cp_header();
include "mymenu.php";

echo "<h2>"._AM_UPLOAD_TITLE."</h2>";

// special files
$sysfile = array('/blank.gif','/index.html');

if (isset($_GET['ren'])) {
    $file = filename_filter($myts->stripSlashesGPC($_GET['ren']));
    rename_form($file);
} else {
    $dir = isset($_GET['dir'])?filename_filter($myts->stripSlashesGPC($_GET['dir'])):'';
    if (!is_dir(UPLOAD_BASE)) mkdir(UPLOAD_BASE);
    folder_list($dir);
    upload_form($dir);
    mkdir_form($dir);
}

xoops_cp_footer();

function unit_kiro($v) {
    if ($v>10240*1024) return round($v/1024/1024)."G";
    if ($v>10240) return round($v/1024)."M";
    return $v."K";
}
function folder_list($dir) {
    global $xoopsModuleConfig, $xoopsDB, $sysfile, $uppath;
    if (empty($dir)) {
	$fp = popen('df -k .', 'r');
	fgets($fp);		// skip header
	$ln = preg_split('/\s+/', fgets($fp));
	pclose($fp);
	$max = $ln[1];		// allocate size
	$used = $ln[2];		// used size
	$avail = $ln[3];		// unused size
	$percent = $ln[4];		// capacity
	$info = _AM_DISK_USE." ".unit_kiro($used)."/".unit_kiro($max)._AM_FILE_UNIT." ($percent)";
    } else $info = "";
    //if (!$size) $size = update_diskuse($size);
    echo "<table width='100%'><tr><td>".folder_ancker($dir)."</td><td align='right'>$info</td></tr></table>\n";
    $path = UPLOAD_BASE."$dir";
    $dh = opendir($path);
    $dirs = $files = array();
    while ($file = readdir($dh)) {
	if ($file == '.' || $file == '..') continue;
	if (is_dir("$path/$file")) $dirs[] = $file;
	else $files[] = $file;
    }
    closedir($dh);
    sort($dirs);
    sort($files);
    if ($files || $dirs) {
	$delinp = "<input type='checkbox' name='dels[]' value='%s'/>";
	echo "<form action='upload.php' method='post' name='folder'>\n";
	echo "<table cellspacing='1' border='0' class='outer'>";
	echo "<tr><th align='center'><input type='checkbox' name='rmall' id='rmall' onClick=\"xoopsCheckAll('folder', 'rmall')\"/></th><th colspan='2'>".
	    _AM_FILE_NAME."</th><th>"._AM_FILE_TYPE."</th><th>"._AM_FILE_SIZE.
	    "</th><th>"._AM_FILE_DATE."</td><th>"._AM_FILE_OPERATION."</td></tr>\n";
	$n = 0;
	$folder = "<img src='dir.png' alt='[ ]'/>";
	foreach ($dirs as $file) {
	    $bg = $n++%2?'even':'odd';
	    $chdir = "$dir/$file";
	    $anc = "[<a href='upload.php?dir=$chdir'>$file</a>]";
	    $stat = stat(UPLOAD_BASE.$chdir);
	    $size = $stat['size'];
	    $mtime = formatTimestamp($stat['mtime']);
	    $del = sprintf($delinp, $chdir);
	    $op = preg_match('/^\/\d+$/', $chdir)?"":"<a href='upload.php?ren=$chdir'>"._AM_FILE_MV."</a>";
	    echo "<tr class='$bg'><td align='center'>$del".
		"</td><td>$folder</td><td>$anc</td><td>"._AM_FILE_DIR.
		"</td><td align='right'>$size</td><td>$mtime</td><td>$op</td></tr>\n";
	}
	// well known usage filename
	// -- user_avatar
	$used = array();
	$res=$xoopsDB->query("SELECT uid,uname,user_avatar FROM ".
			     $xoopsDB->prefix('users').
			     " WHERE user_avatar<>'blank.gif'");
	while (list($id,$name,$key) = $xoopsDB->fetchRow($res)) {
	    $used[$key] = "user_avatar,$id,$name";
	}
	// -- ranks
	$res=$xoopsDB->query("SELECT rank_id,rank_title,rank_image FROM ".
			     $xoopsDB->prefix('ranks'));
	while (list($id,$name,$key) = $xoopsDB->fetchRow($res)) {
	    $used[$key] = "rank,$id,$name";
	}
	// -- smailes
	$res=$xoopsDB->query("SELECT id,emotion,smile_url FROM ".
			     $xoopsDB->prefix('smiles'));
	echo $xoopsDB->error();
	while (list($id,$name,$key) = $xoopsDB->fetchRow($res)) {
	    $used[$key] = "smile,$id,$name";
	}
	// -- avatar
	$res=$xoopsDB->query("SELECT avatar_id,avatar_name,avatar_file FROM ".
			       $xoopsDB->prefix('avatar'));
	while (list($id,$name,$key) = $xoopsDB->fetchRow($res)) {
	    $used[$key] = "avatar,$id,$name";
	}
	// -- image manager
	$res=$xoopsDB->query("SELECT image_id,image_nicename,image_name FROM ".
			     $xoopsDB->prefix('image'));
	while (list($id,$name,$key) = $xoopsDB->fetchRow($res)) {
	    $used[$key] = "image,$id,$name";
	}
	
	// -- medialinks attachment
	$key = preg_replace('/^\/+/', '', $dir);
	$mid = intval($key);
	if ($mid) {
	    $res = $xoopsDB->query($x="SELECT linkid,url,name,ltype FROM ".$xoopsDB->prefix('medialinks_attach')." WHERE midref=$mid");
	    while (list($id,$file,$name,$ty) = $xoopsDB->fetchRow($res)) {
		if (!preg_match('/^(\w+:)?\//', $file)) {
		    $ty = (strtolower($ty)=='a'?"attach":"media");
		    $used["$uppath/$key/$file"] = "$ty,$id,$name";
		}
	    }
	}

	$types = array('system'=>_AM_TYPE_SYSTEM, 'avatar'=>_AM_TYPE_AVATAR,
		       'smile'=>_AM_TYPE_SMILE, 'image'=>_AM_TYPE_IMAGE,
		       'rank'=>_AM_TYPE_RANK,
		       'attach'=>_AM_TYPE_ATTACH, 'media'=>_AM_TYPE_MEDIA, 
		       'user_avatar'=>_AM_TYPE_USER_AVATAR);
	foreach ($files as $file) {
	    $bg = $n++%2?'even':'odd';
	    $myfile = "$dir/$file";
	    $stat = stat(UPLOAD_BASE.$myfile);
	    $mtime = formatTimestamp($stat['mtime']);
	    $size = $stat['size'];
	    $del = sprintf($delinp, $myfile);
	    $type = $icon = '';
	    $key = $uppath.$myfile;
	    if (in_array($myfile, $sysfile)) {
		$type = 'system';
	    } elseif (isset($used[$key])) {
		list($type, $id, $name) = preg_split('/,/', $used[$key], 3);
		$uname = htmlspecialchars($name);
		if ($type == 'user_avatar') {
		    $icon = "<a href='".XOOPS_URL."/userinfo.php?uid=$uid'><img src='".UPLOAD_URL."/$key' width='18' title='$uname' alt='$uname'/></a>";
		} elseif ($type != 'image') {
		    $icon = "<img src='".UPLOAD_URL."/$key' width='18' title='$uname' alt='$uname'/>";
		}
	    }
	    $anc = "<a href='".UPLOAD_URL."$myfile' target='preview'>$file</a>";
	    $op = "<a href='upload.php?ren=$myfile'>"._AM_FILE_MV."</a>";
	    if (isset($types[$type])) {
		$type = $types[$type];
		$op = $del = '';
	    }
	    echo "<tr class='$bg'><td align='center'>$del</td><td>$icon</td><td>$anc</td><td>$type</td><td align='right'>$size</td><td>$mtime</td><td>$op</td></tr>\n";
	}
	echo "</table>\n";
	echo _AM_FILE_REMOVE." <input type='submit' value='"._DELETE."'/>\n";
	echo "</form>\n";
    } else {
	echo _AM_NOFILE;
    }
}

function upload_form($dir="") {
    $size = min(ini_get('upload_max_filesize'), ini_get('post_max_size'));
    echo "<fieldset>\n";
    echo "<legend>"._AM_UPLOAD_FILE."</legend>";
    echo "<form name='upload_form' action='upload.php' method='post' enctype='multipart/form-data'>\n";
    echo "<input type='hidden' name='dir' value='".htmlspecialchars($dir)."'/>\n";
    echo "<input type='file' name='file' size='30'/>\n";
    echo "<input type='submit' name='upfile' value='"._AM_UPLOAD_SUBMIT."'/>\n";
    echo " &nbsp; <input type='checkbox' name='expand' value='1'/> "._AM_UPLOAD_EXPANDZIP;
    if ($size) {
	echo "<input type='hidden' name='MAX_FILE_SIZE' value='$size'/>\n";
	echo ' &nbsp; '._AM_FILE_MAXSIZE.' '.$size._AM_FILE_UNIT;
    }
    echo "</form>\n";
    echo "</fieldset>\n";
}

function upload_store() {
    global $myts;
    $dir = isset($_POST['dir'])?filename_filter($myts->stripSlashesGPC($_POST['dir'])):"";
    $expand = isset($_POST['expand'])?intval($_POST['expand']):0;
    foreach($_FILES as $file) {
	$name = $file['name'];
	$path = UPLOAD_BASE.$dir."/$name";
	if (file_exists($path)) {
	    redirect_header(location_folder($dir, false), 3, _AM_FILE_DUP_ERROR);
	    exit;
	}
	$tmp = $file["tmp_name"];
	$stat = stat($tmp);
	move_uploaded_file($tmp, $path);
	if ($expand) {
	    chdir(dirname($path));
	    if (preg_match('/\\.zip$/i', $name)) {
		system("unzip -q '$name'");
		unlink($path);
	    } elseif (preg_match('/\\.(tgz|tar\\.gz)$/i', $name)) {
		system("tar xfz '$name'");
		unlink($path);
	    }
	}
    }
    location_folder($dir);
}

function mkdir_form($dir="") {
    echo "<fieldset>\n";
    echo "<legend>"._AM_FILE_MKDIR."</legend>";
    echo "<form name='mkdir_form' action='upload.php' method='post'>\n";
    echo "<input type='hidden' name='dir' value='$dir'/>\n";
    echo "<input name='mkdir' size='30'/>\n";
    echo "<input type='submit' value='"._AM_FILE_SUBMIT."'/>\n";
    echo "</form>\n";
    echo "</fieldset>\n";
}

function rename_form($file) {
    if (!preg_match('/^\//', $file)) $file = "/$file";

    $dirs = find_dir();
    array_unshift($dirs, '/');
    $cur = dirname($file);
    $select_dir = "<select name='dir'>\n";
    foreach ($dirs as $dir) {
	$ck = ($dir==$cur)?' selected':'';
	$select_dir .= "<option$ck>$dir</option>\n";
    }
    $select_dir .= "</select>\n";

    echo "<fieldset>\n";
    echo "<legend>"._AM_FILE_MOVE."</legend>";
    echo "<form name='rename_form' action='upload.php' method='post'>\n";
    echo "<div>"._AM_FILE_NAME." $file</div>";
    echo "<input type='hidden' name='file' value='".$file."'/>\n";
    echo "<p>"._AM_FILE_NEWNAME." ".$select_dir.
	"<input name='new' size='15' value='".basename($file)."'/>\n</p>";
    echo "<input type='submit' name='move' value='"._GO."'/>\n";
    echo "</form>\n";
    echo "</fieldset>\n";
}

function filename_filter($name) {
    // remove injection garbage
    $pat = array('@/\\.\\.?|\\.\\.?/|[^\\w\d~/\_\-\.\#\x80-\xff]@','/\/+/');
    $rep = array('', '/');
    echo "<div>$name: ".preg_replace($pat,$rep,$name)."</div>";
    return preg_replace($pat,$rep,$name);
}

function find_dir($dir='') {
    $dh = opendir(UPLOAD_BASE.$dir);
    $dirs = array();
    while ($file = readdir($dh)) {
	if ($file == '.' || $file == '..') continue;
	if (preg_match('/^\.#|(\.bak|\.orig|~|~\d*)$/', $file)) continue;
	$path = "$dir/$file";
	if (is_dir(UPLOAD_BASE.$path)) {
	    if ($file == "CVS") continue;
	    $dirs[] = $path;
	    $dirs = array_merge($dirs, find_dir($path));
	}
    }
    closedir($dh);
    sort($dirs);
    return $dirs;
}

function folder_ancker($dir) {
    $anc = _AM_FILE_DIR." <a href='upload.php'>".basename(UPLOAD_BASE)."</a>/";
    $p = '';
    if ($dir=='.' || $dir=='') return $anc;
    $cur = basename($dir);
    foreach (preg_split('/\//', $dir) as $d) {
	if (empty($d)) continue;
	$p .= $d;
	if ($cur == $d) $anc .= "<em>$d</em>/";
	else $anc .= "<a href='upload.php?dir=$p'>$d</a>/";
	$p .= '/';
    }
    return $anc;
}
function location_folder($dir="", $go=true) {
    $dirname = basename(dirname(dirname(__FILE__)));
    $url = XOOPS_URL."/modules/$dirname/admin/upload.php".
	(($dir!='' && $dir!='/' && $dir!='.')? "?dir=$dir":'');
    if ($go) {
	header('Location: '.$url);
	exit;
    }
    return $url;
}
?>