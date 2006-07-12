<?php
# Medialinks - define admin menus
# $Id: menu.php,v 1.1 2006/07/12 16:27:25 nobu Exp $

$adminmenu[]=array('title' => _MI_MEDIALINKS_ADLIST,
		   'link' => "admin/index.php?op=list");
$adminmenu[]=array('title' => _MI_MEDIALINKS_ADKEYS,
		   'link' => "admin/index.php?op=keys");
$adminmenu[]=array('title' => _MI_MEDIALINKS_ADFIELDS,
		   'link' => "admin/index.php?op=fields");
$adminmenu[]=array('title' => _MI_MEDIALINKS_SUMMARY,
		   'link' => 'admin/summary.php');
$adminmenu[]=array('title' => _MI_MEDIALINKS_NEW,
		   'link' => 'entry.php');

?>