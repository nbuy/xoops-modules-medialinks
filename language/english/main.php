<?php
# medialinks user side language resources
# $Id: main.php,v 1.1 2006/07/22 05:36:41 nobu Exp $

define("_MD_CONTENT_EDIT", "Edit Content");
define("_MD_CONTENT_NEW", "Add New Content");

define("_MD_CONTENT_STATUS", "Status");
define("_MD_VIEW_LIST", "View List");
define("_MD_VIEW_TOP", "Top");
define("_MD_VIEW_PAGE", "Pages");
define("_MD_KEY_NONE", "*None*");
define("_MD_SEP", " :: ");
define("_MD_KEY_SEP", " - ");
define("_MD_COUNT", "Counts");
define("_MD_DEL", "Delete");
define("_MD_URL", "URL");
define("_MD_TITLE", "Title");
define("_MD_WEIGHT", "Order");
define("_MD_NEW", "New");
define("_MD_SAVE", "Save");
define("_MD_REQUIRE_INPUT", "Require following fields");
define("_MD_PREVIEW", "Preview");
define("_MD_DBUPDATED", "Saved in Database");
define("_MD_DBUPDATE_FAIL", "Fail to save in database");
define('_MD_CONTENT_STYLE','Display Style');
define("_MD_CONTENT_DELETE", "Delete This content");
define("_MD_NOTIFY_SUBJECT", "New content - {TITLE}");

global $weekname, $monthname, $edit_style, $status_sel;

// Localization Calender Select Widget
//define("_MD_CAL_MONTH", "%u");
//define("_MD_CAL_BUTTON", "Calender");
//define("_MD_CAL_MONDAY_FIRST", true);
//$weekname = array('Sun'=>'日', 'Mon'=>'月','Tue'=>'火', 'Wed'=>'水',
//		  'Thu'=>'木','Fri'=>'金', 'Sat'=>'土');

//$monthname = array();
//for ($i=1; $i<=12; $i++) {
//    $monthname[$i] = sprintf(_MD_CAL_MONTH, $i);
//}
$edit_style=array('h'=>"only XOOPS tags",
		  'b'=>"New line makes tag &lt;br&gt;",
		  'n'=>"disable HTML tags");
$status_sel=array('W'=>'waiting',
		  'N'=>'reserved',
		  'X'=>'refused');
?>