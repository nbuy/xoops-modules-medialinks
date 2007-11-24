<?php
# medialinks user side language resources
# $Id: main.php,v 1.2 2007/11/24 09:49:14 nobu Exp $

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
define("_MD_DATE","Date");
define("_MD_URL", "URL");
define("_MD_HITS", "Hits");
define("_MD_TITLE", "Title");
define("_MD_WEIGHT", "Order");
define("_MD_NEW", "New");
define("_MD_SAVE", "Save");
define("_MD_REQUIRE_INPUT", "Require following fields");
define("_MD_PREVIEW", "Preview");
define("_MD_DBUPDATED", "Saved in Database");
define('_MD_DBUPDATE_DEL', 'The content deleted');
define("_MD_DBUPDATE_FAIL", "Fail to save in database");
define('_MD_CONTENT_STYLE','Display Style');
define("_MD_CONTENT_DELETE", "Delete This content");
define("_MD_NOTIFY_SUBJECT", "New content - {TITLE}");

define("_MD_CONTENTS_NEW", "New Contents");

// add in 2.0
// entry.php
define("_MD_CONTENT_USERACL", "Access Control");
define("_MD_USERACL_PUBLIC", "Public Access");
define("_MD_USERACL_ADDUSER", "Add Users");
define("_MD_USERACL_ADDUSER_DESC", "UID or Uname with crlf delimter");
define("_MD_USERACL_UNAME", "Uname");
define("_MD_USERACL_WRITABLE", "Writable");
// uploads.php
define("_MD_UPLOADS_PANEL", "Upload Panel");
define("_MD_UPLOADS_MAXSIZE", "Limit size");
define("_MD_UPLOADS_UNITBYTE", "Bytes");
define("_MD_UPLOADS_UPFILE", "Upload File");
define("_MD_UPLOADS_CONVERT_FLV", "Convert FLV format");
define("_MD_UPLOADS_EXT", "File formats");
define("_MD_UPLOADS_ERROR_EXT", " Not allow uploaded file format");
define("_MD_UPLOADS_ERROR_FAIL", "File Upload Error");
define("_MD_UPLOADS_ERROR_SIZE", "Uploaded file is empty");
define("_MD_UPLOADS_WAITING", "File Uploading...");
define("_MD_CONVERT_FAIL", "Format Converting Error");

global $weekname, $monthname, $edit_style, $status_sel;

// Localization Calender Select Widget
//define("_MD_CAL_MONTH", "%u");
define("_MD_CAL_BUTTON", "Calender");
define("_MD_CAL_MONDAY_FIRST", false);
//$weekname = array('Sun'=>'Sunday', 'Mon'=>'Monday','Tue'=>'Tuesday',
//  'Wed'=>'Wednesday', 'Thu'=>'Thursday','Fri'=>'Friday', 'Sat'=>'Saturday');

//$monthname = array();
//for ($i=1; $i<=12; $i++) {
//    $monthname[$i] = sprintf(_MD_CAL_MONTH, $i);
//}
$edit_style=array('h'=>"expand [bb] tags only",
		  'b'=>"New line makes tag &lt;br&gt;",
		  'n'=>"disable HTML tags");
$status_sel=array('W'=>'waiting',
		  'N'=>'display',
		  'X'=>'nodisplay');
?>