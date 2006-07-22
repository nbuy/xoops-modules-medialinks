<?php
# medialinks module information
# $Id: modinfo.php,v 1.1 2006/07/22 05:36:41 nobu Exp $

define("_MI_MEDIALINKS_NAME", "Media Contents");
define("_MI_MEDIALINKS_DESC", "Display Media Contents And additional information");

define("_MI_MEDIALINKS_NEW", "New entry");
define("_MI_MEDIALINKS_LIST", "List contents");

// admin menus
define("_MI_MEDIALINKS_ADLIST", "Conents Management");
define("_MI_MEDIALINKS_ADKEYS", "Keywords Management");
define("_MI_MEDIALINKS_ADFIELDS", "Fields Management");
define("_MI_MEDIALINKS_SUMMARY", "Access Summary");
define("_MI_MEDIALINKS_ABOUT", "About MediaLinks");

// templates
define("_MI_MEDIALINKS_ITEM_TPL", "Each Display contents");
define("_MI_MEDIALINKS_INDEX_TPL", "Keywords Indexes");
define("_MI_MEDIALINKS_ENTRY_TPL", "Contents Entry form");
define("_MI_MEDIALINKS_DETAIL_TPL", "Display Detail contents");
define("_MI_MEDIALINKS_LIST_TPL", "Multiple contents display");
define("_MI_MEDIALINKS_OPERATE_TPL", "Maniplate a contents");
define("_MI_MEDIALINKS_RSS_TPL", "RSS style (for iTunes)");
define("_MI_MEDIALINKS_ASX_TPL", "for WMV(AVI) style application link");
define("_MI_MEDIALINKS_QTL_TPL", "QuickTime style application link");

// Config
define("_MI_MEDIALINKS_POSTGRP", "Group for allow post");
define("_MI_MEDIALINKS_POSTGRP_DSC", "Permit for exit and post contents except administrator");
define("_MI_MEDIALINKS_MAXLIST","Maximum count in List");
define("_MI_MEDIALINKS_MAXLIST_DSC","List display page displayed contents less than this settings");
define("_MI_MEDIALINKS_MAXROWS","Maximum count in Rows");
define("_MI_MEDIALINKS_MAXROWS_DSC","Limit of Title entry listings");
define("_MI_MEDIALINKS_NOTIFYAD","Notify Mail to admin group");
define("_MI_MEDIALINKS_NOTIFYAD_DC","When add new contents notify to contents admin group");
define("_MI_MEDIALINKS_NOTIFYGRP","Contents admin group");
define("_MI_MEDIALINKS_NOTIFYGRP_D","Setting Group that contents administration");
define("_MI_MEDIALINKS_POSTAUTH","Need Approve");
define("_MI_MEDIALINKS_POSTAUTH_DC","New contenets need approve by contents admin");
define("_MI_MEDIALINKS_COMMENT","Allow comments");
define("_MI_MEDIALINKS_COMMENT_DSC","Contents can be comment by user");

// Notifications
define('_MI_MEDIALINKS_GLOBAL_NOTIFY', 'Global');
define('_MI_MEDIALINKS_GLOBAL_NOTIFY_DESC', 'Notification in MediaLinks');
define('_MI_MEDIALINKS_KEYWORD_NOTIFY', 'Current Keywords');
define('_MI_MEDIALINKS_KEYWORD_NOTIFY_DESC', 'Notification by current keyword');
define('_MI_MEDIALINKS_CONTENT_NOTIFY', 'Current Content');
define('_MI_MEDIALINKS_CONTENT_NOTIFY_DESC', 'Notification by current content');
define('_MI_MEDIALINKS_NEWPOST_SUBJECT', 'New contents - {TITLE}');
define('_MI_MEDIALINKS_NEWPOST_NOTIFY', 'New contents');
define('_MI_MEDIALINKS_NEWPOST_NOTIFY_CAP', 'Notification when add new contents');
define('_MI_MEDIALINKS_COMMENT_NOTIFY', 'Comments');
define('_MI_MEDIALINKS_COMMENT_NOTIFY_CAP', 'Notification when post comments');
define('_MI_MEDIALINKS_COMMENT_SUBJECT', 'New comments - {TITLE}');

// Blocks
define("_MI_MEDIALINKS_BLOCK_NEW", "New media conents");
define("_MI_MEDIALINKS_BLOCK_NEW_DESC", "New regsitered ordered");
define("_MI_MEDIALINKS_BLOCK_TOP", "Populer media contents");
define("_MI_MEDIALINKS_BLOCK_TOP_DESC", "Order by hits");
define("_MI_MEDIALINKS_BLOCK_MODIFY", "Updated media contents");
define("_MI_MEDIALINKS_BLOCK_MODIFY_DESC", "New updating order");

// oninstall.php
define("_MI_CONTENT_TITLE", "Title*");
define("_MI_CONTENT_DESC", "Description");
define("_MI_CONTENT_POSTD", "Post Date");
define("_MI_CONTENT_MODIFY", "Last Update");
define("_MI_CONTENT_WEIGHT", "Order");
define("_MI_CONTENT_POSTER", "Poster");
define("_MI_CONTENT_HITS", "Hits");
define("_MI_CONTENT_MEDIA", "Media");
define("_MI_CONTENT_ATTACH", "Attachments");
?>