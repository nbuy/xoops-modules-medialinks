<?php
# medialinks user side language resources
# $Id: main.php,v 1.3 2006/07/20 06:38:20 nobu Exp $

define("_MD_CONTENT_EDIT", "コンテンツの編集");
define("_MD_CONTENT_NEW", "コンテンツの新規作成");

define("_MD_CONTENT_STATUS", "掲載状態");
define("_MD_VIEW_LIST", "一覧表示");
define("_MD_VIEW_TOP", "トップ");
define("_MD_VIEW_PAGE", "ページ");
define("_MD_KEY_NONE", "*なし*");
define("_MD_SEP", " :: ");
define("_MD_KEY_SEP", " - ");
define("_MD_COUNT", "件数");
define("_MD_DEL", "削除");
define("_MD_URL", "URL");
define("_MD_TITLE", "名称");
define("_MD_WEIGHT", "順");
define("_MD_NEW", "新規");
define("_MD_SAVE", "保存");
define("_MD_REQUIRE_INPUT", "下記の項目は必ず入力してください");
define("_MD_PREVIEW", "プレビュー");
define("_MD_DBUPDATED", "データを保存しました");
define("_MD_DBUPDATE_FAIL", "データの保存に失敗しました");
define('_MD_CONTENT_STYLE','表示形式');
define("_MD_CONTENT_DELETE", "このコンテンツを削除します");
define("_MD_NOTIFY_SUBJECT", "新規コンテンツの登録 - {TITLE}");

define("_MD_CAL_MONTH", "%u月");
define("_MD_CAL_BUTTON", "カレンダ");
define("_MD_CAL_MONDAY_FIRST", false);
global $weekname, $monthname, $edit_style, $status_sel;
$weekname = array('Sun'=>'日', 'Mon'=>'月','Tue'=>'火', 'Wed'=>'水',
		  'Thu'=>'木','Fri'=>'金', 'Sat'=>'土');

$monthname = array();
for ($i=1; $i<=12; $i++) {
    $monthname[$i] = sprintf(_MD_CAL_MONTH, $i);
}
$edit_style=array('h'=>'XOOPS タグのみ変換',
		  'b'=>'改行をタグ&lt;br&gt;に変換',
		  'n'=>'HTML タグを無効にする');
$status_sel=array('W'=>'承認待',
		  'N'=>'掲載中',
		  'X'=>'非掲載');
?>