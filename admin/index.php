<?php
# MediaLinks - Administration
# $Id: index.php,v 1.7 2007/11/25 04:04:55 nobu Exp $

include '../../../include/cp_header.php';
include_once '../functions.php';
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';

$op = isset($_GET['op'])?$_GET['op']:'';

// altsys support
if( ! empty( $_GET['lib'] ) ) {
    global $mydirpath;
    $mydirpath = dirname(dirname(__FILE__));
    $mydirname = basename($mydirpath);
    // common libs (eg. altsys)
    $lib = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $_GET['lib'] ) ;
    $page = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , @$_GET['page'] ) ;
    
    if( file_exists( XOOPS_TRUST_PATH.'/libs/'.$lib.'/'.$page.'.php' ) ) {
	include XOOPS_TRUST_PATH.'/libs/'.$lib.'/'.$page.'.php' ;
	} else if( file_exists( XOOPS_TRUST_PATH.'/libs/'.$lib.'/index.php' ) ) {
	include XOOPS_TRUST_PATH.'/libs/'.$lib.'/index.php' ;
    } else {
	die( 'wrong request' ) ;
    }
    exit;
}

xoops_cp_header();
include "mymenu.php";

switch ($op) {
case 'about':
    display_lang_file('help.html');
}
xoops_cp_footer();

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