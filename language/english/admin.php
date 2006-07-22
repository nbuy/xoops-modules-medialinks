<?php
# medialinks admin language resources
# $Id: admin.php,v 1.1 2006/07/22 05:36:41 nobu Exp $

define("_AM_CONTENTS_ADMIN", "Media Contents Management");
define("_AM_CONTENTS_DEL", "Delete contents");
define("_AM_CONTENTS_NEW", "Add new contents");
define("_AM_ATTACH_COUNT", "Media Links/Attachment Documents");
define("_AM_COMMENT_COUNT", "Comments");
define("_AM_PAGE", "Pages");
define("_AM_TITLE", "Title");
define("_AM_CTIME", "Create Date");
define("_AM_MTIME", "Last Update");
define("_AM_POSTER", "Poster");
define("_AM_OPERATION", "Operation");
define("_AM_SORT_ORDER", "Order");
define("_AM_OP_CONF", "Approval");
define("_AM_OP_HIDE", "Hidden");
define("_AM_OP_DEL", "Delete");

define("_AM_STATUS", "Status");
define("_AM_STAT_W_WAIT", "Waiting");
define("_AM_STAT_N_NORMAL", "Display");
define("_AM_STAT_X_UNUSED", "Hide");

define("_AM_KEYWORDS_ADMIN", "Keywords Management");
define("_AM_KEYWORDS_EDIT", "Edit Keyword");
define("_AM_KEYWORDS_NEW", "Add New Keyword");
define("_AM_KEYWORDS_NAME", "Name");
define("_AM_KEYWORDS_PARENT", "Parent keyword");
define("_AM_KEYWORDS_RELAY", "Relay keyword <span class='fontSmall'>(* only for category)</span>");
define("_AM_KEYWORDS_DESC", "Comments");
define("_AM_KEYWORDS_REMOVE", "Delete Keyword");
define("_AM_KEYWORDS_NODETYPE", "Keyword Types");
define("_AM_KEYWORDS_COUNT", "Usage this keyword");
define("_AM_KEYWORDS_PRINT", "Keyword "%s" reference from %u contents");

define("_AM_SORT_WEIGHT", "Display Order <span class='fontSmall'>(0=invisible)</span>");
define("_AM_KEY_NONE", "None");
define("_AM_NODE_BOTH", "Category and/or Key");
define("_AM_NODE_CATEGORY", "Category");
define("_AM_NODE_KEY", "Key");

define("_AM_FIELDS_EDIT", "Edit Contents Fields");
define("_AM_FIELDS_NEW", "Add New Field");
define("_AM_FIELDS_NAME", "Name");
define("_AM_FIELDS_LABEL", "Label");
define("_AM_FIELDS_TYPE", "Type");
define("_AM_FIELDS_DEF", "Default");
define("_AM_FIELDS_SIZE", "string length");
define("_AM_FIELDS_OPERATION", "Operation");
define("_AM_FIELDS_DELETE", "Delete of field");
define("_AM_FIELDS_COUNT", "Use this field");
define("_AM_FIELDS_COUNT_NOTICE", "%u data exists");
define("_AM_TYPE_STRING", "String");
define("_AM_TYPE_INTEGER", "Integer");
define("_AM_TYPE_DATE", "Date");
define("_AM_TYPE_TIMESTAMP", "Time");
define("_AM_TYPE_UID", "User ID");
define("_AM_TYPE_TEXT", "Text");
define("_AM_TYPE_KEYWORD", "Keyword");
define("_AM_TYPE_LINK", "Link");

global $nodetypes, $status_sel;

$nodetypes_select =
    array(0=>_AM_NODE_BOTH,
	  1=>_AM_NODE_CATEGORY,
	  2=>_AM_NODE_KEY);

$status_sel=
    array('W'=>_AM_STAT_W_WAIT,
	  'N'=>_AM_STAT_N_NORMAL,
	  'X'=>_AM_STAT_X_UNUSED);

define("_AM_DBUPDATED", "Update Database");
define("_AM_DBUPDATE_FAIL", "Update Failer");

// summary.php
define("_AM_SUMMARY_TITLE", "Summary of access counts");
define("_AM_SUMMARY_TYPE", "Display Type");
define("_AM_LTYPE_MEDIA", "Medias");
define("_AM_LTYPE_DOCUMENT", "Attachments");
define("_AM_LINKNAME", "Document Name");
define("_AM_HITS", "Hits");
define("_AM_COUNT", "Counts");
define("_AM_EXPORT_CHARSET", "UTF-8");
define("_AM_EXPORT_FILE", "CSV Output");
?>