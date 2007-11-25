<?php
# contents upload script
# $Id: uploads.php,v 1.3 2007/11/25 07:39:49 nobu Exp $

include "../../mainfile.php";
include_once "perm.php";
require_once XOOPS_ROOT_PATH.'/class/template.php';

// check access
$mid = isset($_REQUEST['mid'])?intval($_REQUEST['mid']):0;
if (!is_object($xoopsUser) || !check_access($mid) || !check_groups('user_upload')) die(_NOPERM);

$myts =& MyTextSanitizer::getInstance();

if (isset($_POST['a'])) {
    $a = intval($_POST['a']);
    $errors = array();
    $file = ml_image_uploads_file($mid, 'imagefile', !empty($_POST['conv']));
    if ($file) {
	echo "<html>
<head>
<script language='javascript'>
var dom=opener.xoopsGetElementById('linkurl[$a]');
dom.value = '$file';
close();
</script></head>
</html>";
    } else {
	echo "<html>
<head>
<title>Error in Upload file</title>
</head>
<body><p style='color:red; font: large bold;'>".join("<br/>", $errors)."</p>
<div style='text-align:center'><button onClick='window.close();'>Close</button></div></body>
</html>";
    }
    exit;
}

$a = $myts->stripSlashesGPC($_GET['a']);
include XOOPS_ROOT_PATH."/header.php";
$xoopsTpl->assign(array('target'=>$a, 'mid'=>$mid));
$xoopsTpl->assign('max_filesize', ini_get('upload_max_filesize'));
$xoopsTpl->assign('upload_ext', preg_replace('/\|/', ' ', $xoopsModuleConfig['upload_ext']));
echo $xoopsTpl->fetch('db:medialinks_uploads.html');

function size_format($n) {
    $mega = 1048576;
    $kiro = 1024;
    if ($n>($mega*10)) {		// over 10 Mega
	return sprintf("%dM", $n/$mega);
    } elseif ($n>($kiro*10)) {
	return sprintf("%dK", $n/$kiro);
    }
    return $n;
}

function ml_image_uploads_file($mid, $field="imagefile", $flv=false) {
    global $xoopsModuleConfig, $errors;
    if (empty($_FILES[$field])) {
	$errors[] = _MD_UPLOADS_ERROR_FAIL;
	return false;
    }
    $upfile =& $_FILES[$field];
    if (empty($upfile['size'])) {
	$errors[] = _MD_UPLOADS_ERROR_SIZE;
	return false;
    }
    //global $upload_types;
    //if (!in_array($upfile['type'], $upload_types)) return false;
    $fname = $upfile['name'];
    if (!preg_match('/\\.('.$xoopsModuleConfig['upload_ext'].')$/i', $fname)) {
	$errors[] = _MD_UPLOADS_ERROR_EXT;
	return false;
    }
    $path = get_upload_path($mid);
    if (!preg_match('/^\//', $path)) $path = XOOPS_UPLOAD_PATH."/".$path;
    if (!is_dir(dirname($path))) mkdir(dirname($path));
    if (!is_dir($path)) mkdir($path);
    $temp = $upfile['tmp_name'];
    if ($flv && preg_match(_VIDEO_EXT, $fname, $d)) {
	$ext = $d[1];
	rename($temp, $temp.".".$ext);
	$temp .= ".".$ext;	// set original extention for converter
	$fname = preg_replace("/\\.$ext$/", '.flv', $fname);
	$tofile = "$path/$fname";
	$base = dirname(__FILE__);
	$ret = convert_flv($temp, $tofile);
	unlink($temp);
	if (!$ret) {
	    $errors[] = _MD_CONVERT_FAIL;
	    return false;
	}
    } else {
	$tofile = "$path/$fname";
	if (!move_uploaded_file($temp, $tofile)) {
	    $errors[] = _MD_UPLOADS_ERROR_FAIL;
	    return false;
	}
    }
    return $fname;
}

function convert_flv($src, $dest) {
    $logfile = tempnam('/tmp', 'ff2pass');
    $opts="-mbd rd -flags +trell -cmp 2 -subcmp 2 -g 100 -pass 1/2 -passlogfile $logfile";
    $qsrc = addslashes($src);
    $qdest = addslashes($dest);

    system("ffmpeg -i '$qsrc' $opts -y '$qdest'");
    if (!file_exists($dest) || filesize($dest)==0) {
	// fall-back no audio
	system("ffmpeg -i '$qsrc' $opts -an -y '$qdest'");
    }
    system("rm -f \"$logfile\"*");
    if (filesize($dest)==0) unlink($dest);
    return file_exists($dest);
}
?>
