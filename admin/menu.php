<?php
# Medialinks - define admin menus
# $Id: menu.php,v 1.3 2007/11/24 09:49:14 nobu Exp $

$adminmenu[]=array('title' => _MI_MEDIALINKS_ADLIST,
		   'link' => "admin/index.php");
$adminmenu[]=array('title' => _MI_MEDIALINKS_ADKEYS,
		   'link' => "admin/index.php?op=keys");
$adminmenu[]=array('title' => _MI_MEDIALINKS_ADFIELDS,
		   'link' => "admin/index.php?op=fields");
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