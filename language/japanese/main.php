<?php
# medialinks user side language resources
# $Id: main.php,v 1.5 2007/11/24 09:49:14 nobu Exp $

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
define("_MD_DATE","日付");
define("_MD_URL", "URL");
define("_MD_HITS", "閲覧");
define("_MD_TITLE", "表題");
define("_MD_WEIGHT", "順");
define("_MD_NEW", "新規");
define("_MD_SAVE", "保存");
define("_MD_REQUIRE_INPUT", "下記の項目は必ず入力してください");
define("_MD_PREVIEW", "プレビュー");
define("_MD_DBUPDATED", "データを保存しました");
define("_MD_DBUPDATE_DEL", "コンテンツを削除しました");
define("_MD_DBUPDATE_FAIL", "データの保存に失敗しました");
define('_MD_CONTENT_STYLE','表示形式');
define("_MD_CONTENT_DELETE", "このコンテンツを削除します");
define("_MD_NOTIFY_SUBJECT", "新規コンテンツの登録 - {TITLE}");

define("_MD_CONTENTS_NEW", "新着映像");

// add in 2.0
// entry.php
define("_MD_CONTENT_USERACL", "個別アクセス権");
define("_MD_USERACL_PUBLIC", "公開コンテンツ");
define("_MD_USERACL_ADDUSER", "ユーザの追加");
define("_MD_USERACL_ADDUSER_DESC", "UID またはユーザ名を改行区切で指定");
define("_MD_USERACL_UNAME", "ユーザ名");
define("_MD_USERACL_WRITABLE", "更新");
// uploads.php
define("_MD_UPLOADS_PANEL", "アップロード");
define("_MD_UPLOADS_MAXSIZE", "サイズ制限");
define("_MD_UPLOADS_UNITBYTE", "バイト");
define("_MD_UPLOADS_UPFILE", "送信ファイル");
define("_MD_UPLOADS_CONVERT_FLV", "ビデオは FLV 形式に変換する");
define("_MD_UPLOADS_EXT", "ファイル形式");
define("_MD_UPLOADS_ERROR_EXT", "許可されないファイル形式");
define("_MD_UPLOADS_ERROR_FAIL", "アップロードでエラーが発生しました");
define("_MD_UPLOADS_ERROR_SIZE", "アップロードファイルが空です");
define("_MD_UPLOADS_WAITING", "ファイルをアップロード中...");
define("_MD_CONVERT_FAIL", "ファイル形式の変換でエラーが発生しました");

global $weekname, $monthname, $edit_style, $status_sel;

// Localization Calender Select Widget
define("_MD_CAL_MONTH", "%u月");
define("_MD_CAL_BUTTON", "カレンダ");
define("_MD_CAL_MONDAY_FIRST", false);
$weekname = array('Sun'=>'日', 'Mon'=>'月','Tue'=>'火', 'Wed'=>'水',
		  'Thu'=>'木','Fri'=>'金', 'Sat'=>'土');

$monthname = array();
for ($i=1; $i<=12; $i++) {
    $monthname[$i] = sprintf(_MD_CAL_MONTH, $i);
}
$edit_style=array('h'=>'[bb]タグのみ変換',
		  'b'=>'改行をタグ&lt;br&gt;に変換',
		  'n'=>'HTMLタグを無効にする');
$status_sel=array('W'=>'承認待',
		  'N'=>'掲載中',
		  'X'=>'非掲載');
?>