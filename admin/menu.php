<?php
# Medialinks - define admin menus
# $Id: menu.php,v 1.2 2006/07/13 08:36:14 nobu Exp $

$adminmenu[]=array('title' => _MI_MEDIALINKS_ADLIST,
		   'link' => "admin/index.php");
$adminmenu[]=array('title' => _MI_MEDIALINKS_ADKEYS,
		   'link' => "admin/index.php?op=keys");
$adminmenu[]=array('title' => _MI_MEDIALINKS_ADFIELDS,
		   'link' => "admin/index.php?op=fields");
$adminmenu[]=array('title' => _MI_MEDIALINKS_SUMMARY,
		   'link' => 'admin/summary.php');
$adminmenu[]=array('title' => _MI_MEDIALINKS_ABOUT,
		   'link' => 'admin/index.php?op=about');

?>