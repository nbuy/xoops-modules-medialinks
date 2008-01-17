<?php
# medialinks admin language resources
# $Id: admin.php,v 1.6 2008/01/17 09:13:14 nobu Exp $

define("_AM_CONTENTS_ADMIN", "コンテンツの管理");
define("_AM_CONTENTS_DEL", "コンテンツを削除します");
define("_AM_CONTENTS_NEW", "コンテンツの新規作成");
define("_AM_ATTACH_COUNT", "リンク/添付文書数");
define("_AM_COMMENT_COUNT", "コメントの数");
define("_AM_PAGE", "ページ");
define("_AM_TITLE", "タイトル");
define("_AM_CTIME", "作成日時");
define("_AM_MTIME", "更新日時");
define("_AM_POSTER", "作成者");
define("_AM_OPERATION", "操作");
define("_AM_SORT_ORDER", "表示順");
define("_AM_OP_CONF", "掲載承認する");
define("_AM_OP_HIDE", "非表示にする");
define("_AM_OP_DEL", "削除する");

define("_AM_STATUS", "掲載状態");
define("_AM_STAT_W_WAIT", "承認待");
define("_AM_STAT_N_NORMAL", "掲載中");
define("_AM_STAT_X_UNUSED", "非掲載");

define("_AM_KEYWORDS_ADMIN", "キーワードの管理");
define("_AM_KEYWORDS_EDIT", "キーワード編集");
define("_AM_KEYWORDS_NEW", "新規キーワード");
define("_AM_KEYWORDS_NAME", "名称");
define("_AM_KEYWORDS_PARENT", "上位キーワード");
define("_AM_KEYWORDS_RELAY", "連携キー <span class='fontSmall'>(※カテゴリのみで有効)</span>");
define("_AM_KEYWORDS_DESC", "コメント");
define("_AM_KEYWORDS_REMOVE", "キーワードの削除");
define("_AM_KEYWORDS_NODETYPE", "キーの使い分け");
define("_AM_KEYWORDS_COUNT", "キーワードの使用数");
define("_AM_KEYWORDS_PRINT", "キーワード「%s」は %u 個使用されています");

define("_AM_SORT_WEIGHT", "表示順 <span class='fontSmall'>(0=非表示)</span>");
define("_AM_KEY_NONE", "なし");
define("_AM_NODE_BOTH", "カテゴリ＋キーワード");
define("_AM_NODE_CATEGORY", "カテゴリのみ");
define("_AM_NODE_KEY", "キーワードのみ");

define("_AM_FIELDS_EDIT", "フィールド編集");
define("_AM_FIELDS_NEW", "新規フィールド");
define("_AM_FIELDS_NAME", "名前");
define("_AM_FIELDS_LABEL", "名称");
define("_AM_FIELDS_TYPE", "データ型");
define("_AM_FIELDS_DEF", "既定値");
define("_AM_FIELDS_NUMBER", "フィールドの数");
define("_AM_FIELDS_SIZE", "文字列長");
define("_AM_FIELDS_OPERATION", "操作");
define("_AM_FIELDS_DELETE", "フィールドの削除");
define("_AM_FIELDS_COUNT", "フィールドの利用数");
define("_AM_FIELDS_COUNT_NOTICE", "%u件のデータが存在します");
define("_AM_TYPE_STRING", "文字列");
define("_AM_TYPE_INTEGER", "整数");
define("_AM_TYPE_DATE", "日付");
define("_AM_TYPE_TIMESTAMP", "時刻");
define("_AM_TYPE_UID", "ユーザID");
define("_AM_TYPE_TEXT", "テキスト");
define("_AM_TYPE_KEYWORD", "キーワード");
define("_AM_TYPE_LINK", "リンク");

global $nodetypes_select, $status_sel;

$nodetypes_select =
    array(0=>_AM_NODE_BOTH,
	  1=>_AM_NODE_CATEGORY,
	  2=>_AM_NODE_KEY);

$status_sel=
    array('W'=>_AM_STAT_W_WAIT,
	  'N'=>_AM_STAT_N_NORMAL,
	  'X'=>_AM_STAT_X_UNUSED);

define("_AM_DBUPDATED", "更新しました");
define("_AM_DBUPDATE_FAIL", "更新に失敗しました");

// summary.php
define("_AM_SUMMARY_TITLE", "アクセス数");
define("_AM_SUMMARY_TYPE", "表示タイプ");
define("_AM_LTYPE_MEDIA", "メディア");
define("_AM_LTYPE_DOCUMENT", "添付ファイル");
define("_AM_LINKNAME", "文書名");
define("_AM_HITS", "参照数");
define("_AM_COUNT", "件数");
define("_AM_EXPORT_CHARSET", "UTF-8");
define("_AM_EXPORT_FILE", "CSVファイル出力");

include_once dirname(__FILE__)."/upload.php";
?>