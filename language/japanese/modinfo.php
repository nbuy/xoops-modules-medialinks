<?php
# medialinks module information
# $Id: modinfo.php,v 1.6 2007/11/24 09:49:14 nobu Exp $

define("_MI_MEDIALINKS_NAME", "映像コンテンツ");
define("_MI_MEDIALINKS_DESC", "ビデオ映像などのコンテンツ情報を閲覧する");

define("_MI_MEDIALINKS_NEW", "新規作成");
define("_MI_MEDIALINKS_LIST", "一括表示");

// admin menus
define("_MI_MEDIALINKS_ADLIST", "コンテンツ");
define("_MI_MEDIALINKS_ADKEYS", "キーワード");
define("_MI_MEDIALINKS_ADFIELDS", "フィールド");
define("_MI_MEDIALINKS_SUMMARY", "アクセス数");
define("_MI_MEDIALINKS_ADUPLOADS", "アップロード");
define("_MI_MEDIALINKS_ABOUT", "medialinksについて");

// templates
define("_MI_MEDIALINKS_ITEM_TPL", "コンテンツ表示形式");
define("_MI_MEDIALINKS_INDEX_TPL", "キーワード索引");
define("_MI_MEDIALINKS_ENTRY_TPL", "コンテンツ入力画面");
define("_MI_MEDIALINKS_DETAIL_TPL", "コンテンツの詳細表示");
define("_MI_MEDIALINKS_LIST_TPL", "コンテンツの一括表示");
define("_MI_MEDIALINKS_LISTSHOTS_TPL", "画面付きの一覧表示");
define("_MI_MEDIALINKS_OPERATE_TPL", "コンテンツの操作");
define("_MI_MEDIALINKS_UPLOADS_TPL", "アップロードパネル");
define("_MI_MEDIALINKS_RSS_TPL", "RSS(iTunes) 形式の配信用");
define("_MI_MEDIALINKS_ASX_TPL", "WMV(AVI) 形式のリンク起動用");
define("_MI_MEDIALINKS_QTL_TPL", "QuickTime 形式のリンク起動用");

// Config
define("_MI_MEDIALINKS_POSTGRP", "登録を許可するグループ");
define("_MI_MEDIALINKS_POSTGRP_DSC", "コンテンツの新規登録や作成者による編集を許可するグループを指定する");
define("_MI_MEDIALINKS_MAXLIST","一括表示で表示する件数");
define("_MI_MEDIALINKS_MAXLIST_DSC","一括表示で画面に並べるコンテンツの最大数を指定する");
define("_MI_MEDIALINKS_LISTSTYLE","一覧表示のスタイル");
define("_MI_MEDIALINKS_LISTSTYLE_DSC","一覧ページで表示するコンテンツの表示形式を指定する");
define("_MI_MEDIALINKS_STYLE_SHORT", "短く");
define("_MI_MEDIALINKS_STYLE_VERB",  "詳しく");
define("_MI_MEDIALINKS_STYLE_SHOTS", "画像付き");
define("_MI_MEDIALINKS_MAXROWS","一覧表示で表示する行数");
define("_MI_MEDIALINKS_MAXROWS_DSC","タイトル一覧を表示するコンテンツの最大数を指定する");
define("_MI_MEDIALINKS_NOTIFYAD","管理者へメール通知行う");
define("_MI_MEDIALINKS_NOTIFYAD_DC","新規にコンテンツが登録された場合の管理者グループへメール通知を行う");
define("_MI_MEDIALINKS_NOTIFYGRP","管理者グループの指定");
define("_MI_MEDIALINKS_NOTIFYGRP_D","通知メールを受け取る管理者グループを指定する");
define("_MI_MEDIALINKS_POSTAUTH","コンテンツ掲載には承認が必要");
define("_MI_MEDIALINKS_POSTAUTH_DC","新規登録されたコンテンツの掲載には管理者の承認を必要とする");
define("_MI_MEDIALINKS_USERACL","個別アクセス設定");
define("_MI_MEDIALINKS_USERACL_DSC","コンテンツ毎にユーザのアクセス権を設定可能にする");
define("_MI_MEDIALINKS_USERUPLOAD","アップロードの許可");
define("_MI_MEDIALINKS_USERUP_DSC","ユーザにメディアファイルのアップロードを許可する");
define("_MI_MEDIALINKS_UPLOADPATH","アップロードフォルダ");
define("_MI_MEDIALINKS_UPPATH_DSC","ファイルのアップロードする先を指定する。相対パスを指定した場合、XOOPS_UPLOAD_PATH (/uploads) を起点とする。");
define("_MI_MEDIALINKS_UPLOADEXT","アップロード拡張子");
define("_MI_MEDIALINKS_UPEXT_DSC","アップロードを許可するファイルの拡張子を '|' 区切りで指定する。");
define("_MI_MEDIALINKS_SHOTOPTS","サムネイル作成オプション");
define("_MI_MEDIALINKS_SOPTS_DC","サムネイル画像を作成する場合の ffmpeg に追加するオプションがあれば指定する。(例: <tt>-ss 5</tt> なら 5 秒後を取り出す)");
define("_MI_MEDIALINKS_D3FORUMID","d3forumのID指定");
define("_MI_MEDIALINKS_D3ID_DESC","コメントに d3forum を使う場合、対応するフォーラムのIDを指定する。空なら XOOPS のコメントシステムを使う。例: <tt>1,key1=2,key3=3</tt> (keyXXで対応するキーIDを示し、複数ある場合最後のもの有効。指定がない数字はそれ以外)");

// Notifications
define('_MI_MEDIALINKS_GLOBAL_NOTIFY', 'モジュール全体');
define('_MI_MEDIALINKS_GLOBAL_NOTIFY_DESC', '映像コンテンツ全体における通知オプション');
define('_MI_MEDIALINKS_KEYWORD_NOTIFY', '現在のキーワード');
define('_MI_MEDIALINKS_KEYWORD_NOTIFY_DESC', '指定のキーワードに関連する通知オプション');
define('_MI_MEDIALINKS_CONTENT_NOTIFY', '表示中のコンテンツ');
define('_MI_MEDIALINKS_CONTENT_NOTIFY_DESC', '指定のコンテンツに関連する通知オプション');
define('_MI_MEDIALINKS_NEWPOST_SUBJECT', '新規コンテンツ - {TITLE}');
define('_MI_MEDIALINKS_NEWPOST_NOTIFY', '新規コンテンツの登録');
define('_MI_MEDIALINKS_NEWPOST_NOTIFY_CAP', '新しいコンテンツが登録された場合に通知する');
define('_MI_MEDIALINKS_COMMENT_NOTIFY', 'コメント登録');
define('_MI_MEDIALINKS_COMMENT_NOTIFY_CAP', 'コンテンツにコメントが登録された場合に通知する');
define('_MI_MEDIALINKS_COMMENT_SUBJECT', 'コメント登録 - {TITLE}');

// Blocks
define("_MI_MEDIALINKS_BLOCK_NEW", "新着映像コンテンツ");
define("_MI_MEDIALINKS_BLOCK_NEW_DESC", "新しく登録された順に表示する");
define("_MI_MEDIALINKS_BLOCK_TOP", "人気映像コンテンツ");
define("_MI_MEDIALINKS_BLOCK_TOP_DESC", "ヒット数の多い映像コンテンツを表示する");
define("_MI_MEDIALINKS_BLOCK_MODIFY", "更新されたコンテンツ");
define("_MI_MEDIALINKS_BLOCK_MODIFY_DESC", "更新日付が新しい映像コンテンツを表示する");

// oninstall.php
define("_MI_CONTENT_TITLE", "表題*");
define("_MI_CONTENT_DESC", "説明文");
define("_MI_CONTENT_POSTD", "作成日時");
define("_MI_CONTENT_MODIFY", "更新日時");
define("_MI_CONTENT_WEIGHT", "表示順");
define("_MI_CONTENT_POSTER", "作成者");
define("_MI_CONTENT_HITS", "アクセス数");
define("_MI_CONTENT_MEDIA", "メディア");
define("_MI_CONTENT_ATTACH", "添付ファイル");

// for altsys 
if (!defined('_MD_A_MYMENU_MYTPLSADMIN')) {
    define('_MD_A_MYMENU_MYTPLSADMIN','テンプレート管理');
    define('_MD_A_MYMENU_MYBLOCKSADMIN','ブロック/アクセス管理');
    define('_MD_A_MYMENU_MYPREFERENCES','一般設定');
}
?>