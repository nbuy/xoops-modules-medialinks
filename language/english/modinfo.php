<?php
# medialinks module information
# $Id: modinfo.php,v 1.7 2009/12/13 11:25:00 nobu Exp $

define("_MI_MEDIALINKS_NAME", "Media Contents");
define("_MI_MEDIALINKS_DESC", "Display Media Contents And additional information");

define("_MI_MEDIALINKS_NEW", "New entry");
define("_MI_MEDIALINKS_LIST", "List contents");

// admin menus
define("_MI_MEDIALINKS_ADLIST", "Conents");
define("_MI_MEDIALINKS_ADKEYS", "Keywords");
define("_MI_MEDIALINKS_ADFIELDS", "Fields");
define("_MI_MEDIALINKS_SUMMARY", "Access Count");
define("_MI_MEDIALINKS_ADUPLOADS", "Uploads");
define("_MI_MEDIALINKS_ABOUT", "About MediaLinks");

// templates
define("_MI_MEDIALINKS_ITEM_TPL", "Each Display contents");
define("_MI_MEDIALINKS_INDEX_TPL", "Keywords Indexes");
define("_MI_MEDIALINKS_ENTRY_TPL", "Contents Entry form");
define("_MI_MEDIALINKS_DETAIL_TPL", "Display Detail contents");
define("_MI_MEDIALINKS_LIST_TPL", "Multiple contents display");
define("_MI_MEDIALINKS_LISTSHOTS_TPL", "List with screenshots");
define("_MI_MEDIALINKS_OPERATE_TPL", "Maniplate a contents");
define("_MI_MEDIALINKS_UPLOADS_TPL", "Upload Panel");
define("_MI_MEDIALINKS_COMMENT_TPL", "External comment template (d3forum)");
define("_MI_MEDIALINKS_RSS_TPL", "RSS style (for iTunes)");
define("_MI_MEDIALINKS_ASX_TPL", "for WMV(AVI) style application link");
define("_MI_MEDIALINKS_QTL_TPL", "QuickTime style application link");

// Config
define("_MI_MEDIALINKS_POSTGRP", "Group for allow post");
define("_MI_MEDIALINKS_POSTGRP_DSC", "Permit for exit and post contents except administrator");
define("_MI_MEDIALINKS_MAXLIST","Maximum count in List");
define("_MI_MEDIALINKS_MAXLIST_DSC","List display page displayed contents less than this settings");
define("_MI_MEDIALINKS_LISTSTYLE","Style of Display List");
define("_MI_MEDIALINKS_LISTSTYLE_DSC","Set index page contents list display style");
define("_MI_MEDIALINKS_STYLE_SHORT", "Simple");
define("_MI_MEDIALINKS_STYLE_VERB",  "Verbose");
define("_MI_MEDIALINKS_STYLE_SHOTS", "Screenshots");
define("_MI_MEDIALINKS_MAXROWS","Maximum count in Rows");
define("_MI_MEDIALINKS_MAXROWS_DSC","Limit of Title entry listings");
define("_MI_MEDIALINKS_NOTIFYAD","Notify Mail to admin group");
define("_MI_MEDIALINKS_NOTIFYAD_DC","When add new contents notify to contents admin group");
define("_MI_MEDIALINKS_NOTIFYGRP","Contents admin group");
define("_MI_MEDIALINKS_NOTIFYGRP_D","Setting Group that contents administration");
define("_MI_MEDIALINKS_POSTAUTH","Need Approve");
define("_MI_MEDIALINKS_POSTAUTH_DC","New contenets need approve by contents admin");
define("_MI_MEDIALINKS_USERACL","User Access Control");
define("_MI_MEDIALINKS_USERACL_DSC","Allow user access control for each content");
define("_MI_MEDIALINKS_USERUPLOAD","Upload Groups");
define("_MI_MEDIALINKS_USERUP_DSC","Allow groups to upload media files");
define("_MI_MEDIALINKS_UPLOADPATH","Uploads folder");
define("_MI_MEDIALINKS_UPPATH_DSC","Set folder for upload file to store. When set relative path, The root assume XOOPS_UPLOAD_PATH (/uploads).");
define("_MI_MEDIALINKS_UPLOADEXT","Uploadable Extentions");
define("_MI_MEDIALINKS_UPEXT_DSC","Set allow upload file extentions. Extention delmiter is '|'.");
define("_MI_MEDIALINKS_CMDPATH","Command search path");
define("_MI_MEDIALINKS_CMDPATH_DC","Setting external command (using ffmpeg, convert, etc) search path when not exist default path. (example: <tt>/usr/local/bin:/usr/bin</tt>)");
define("_MI_MEDIALINKS_CONVOPTS","FLV convert options");
define("_MI_MEDIALINKS_COPTS_DC","Set ffmpeg options when use movie file convert to FLV formats. (example: <tt>-mbd rd -flags +trell -cmp 2 -subcmp 2 -g 100 -pass 1/2</tt>)");
define("_MI_MEDIALINKS_SHOTOPTS","Thumbnail image options");
define("_MI_MEDIALINKS_SOPTS_DC","Option argument for 'ffmpeg' to make screenshots. (e.g. <tt>-ss 5</tt> take screenshot 5 seconds after)");
define("_MI_MEDIALINKS_D3FORUMID","d3forum ID");
define("_MI_MEDIALINKS_D3ID_DESC","Set forumid when using comment with 'd3forum' module. When empty this option, using XOOPS comments. Example: <tt>1,key1=2,key3=3</tt> (keyXX mean keywords ID in medialinks, determin last match if multiple keywords. only numeric term post otherwise)");

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

// for altsys
if (!defined('_MD_A_MYMENU_MYTPLSADMIN')) {
    define('_MD_A_MYMENU_MYTPLSADMIN','Templates');
    define('_MD_A_MYMENU_MYBLOCKSADMIN','Blocks/Permissions');
    define('_MD_A_MYMENU_MYPREFERENCES','Preferences');
}
?>
