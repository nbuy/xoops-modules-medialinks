<?php
# Medialinks - define admin menus
# $Id: menu.php,v 1.4 2007/11/25 04:04:55 nobu Exp $

$adminmenu[]=array('title' => _MI_MEDIALINKS_ADLIST,
		   'link' => "admin/contents.php");
$adminmenu[]=array('title' => _MI_MEDIALINKS_ADKEYS,
		   'link' => "admin/keywords.php");
$adminmenu[]=array('title' => _MI_MEDIALINKS_ADFIELDS,
		   'link' => "admin/fields.php");
$adminmenu[]=array('title' => _MI_MEDIALINKS_ADUPLOADS,
		   'link' => "admin/upload.php");
$adminmenu[]=array('title' => _MI_MEDIALINKS_SUMMARY,
		   'link' => 'admin/summary.php');
$adminmenu[]=array('title' => _MI_MEDIALINKS_ABOUT,
		   'link' => 'admin/index.php?op=about');

$adminmenu4altsys[]=
    array('title' => _MD_A_MYMENU_MYTPLSADMIN,
	  'link' => 'admin/index.php?mode=admin&lib=altsys&page=mytplsadmin');
$adminmenu4altsys[]=
    array('title' => _MD_A_MYMENU_MYBLOCKSADMIN,
	  'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin');
$adminmenu4altsys[]=
    array('title' => _MD_A_MYMENU_MYPREFERENCES,
	  'link' => 'admin/index.php?mode=admin&lib=altsys&page=mypreferences');
?>