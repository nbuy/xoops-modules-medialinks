<?php
# medialinks module information
# $Id: modinfo.php,v 1.1 2006/07/12 16:27:25 nobu Exp $

define("_MI_MEDIALINKS_NAME", "映像コンテンツ");
define("_MI_MEDIALINKS_DESC", "ビデオ映像などのコンテンツ情報を閲覧する");

define("_MI_MEDIALINKS_NEW", "新規作成");
define("_MI_MEDIALINKS_LIST", "一括表示");

// admin menus
define("_MI_MEDIALINKS_ADLIST", "コンテンツの管理");
define("_MI_MEDIALINKS_ADKEYS", "キーワードの管理");
define("_MI_MEDIALINKS_ADFIELDS", "フィールドの管理");
define("_MI_MEDIALINKS_SUMMARY", "アクセス数");

// templates
define("_MI_MEDIALINKS_ITEM_TPL", "コンテンツ表示形式");
define("_MI_MEDIALINKS_INDEX_TPL", "キーワード索引");
define("_MI_MEDIALINKS_ENTRY_TPL", "コンテンツ入力画面");
define("_MI_MEDIALINKS_DETAIL_TPL", "コンテンツの詳細表示");
define("_MI_MEDIALINKS_LIST_TPL", "コンテンツの一括表示");
define("_MI_MEDIALINKS_OPERATE_TPL", "コンテンツの操作");
define("_MI_MEDIALINKS_RSS_TPL", "RSS(iTunes) 形式の配信用");

// Config
define("_MI_MEDIALINKS_POSTGROUP", "登録を許可するグループ");
define("_MI_MEDIALINKS_POSTGROUP_DESC", "コンテンツの新規登録や投稿者による編集を許可するグループ");
define("_MI_MEDIALINKS_MAXLIST","一括表示で表示する件数");
define("_MI_MEDIALINKS_MAXLIST_DESC","画面に表示するコンテンツの最大数を指定する");
define("_MI_MEDIALINKS_MAXROWS","一覧表示で表示する行数");
define("_MI_MEDIALINKS_MAXROWS_DESC","タイトル一覧を表示するコンテンツの最大数を指定する");
define("_MI_MEDIALINKS_NOTIFYADMIN","管理者へメール通知行う");
define("_MI_MEDIALINKS_NOTIFYADMIN_DESC","新規登録などのメール通知を使用する");
define("_MI_MEDIALINKS_NOTIFYGROUP","管理者グループの指定");
define("_MI_MEDIALINKS__DESC","通知メールを受け取る管理者グループを指定する");
define("_MI_MEDIALINKS_POSTAUTH","コンテンツ掲載には承認が必要");
define("_MI_MEDIALINKS_POSTAUTH_DESC","新規登録されたコンテンツの掲載には管理者の承認を必要とする");
define("_MI_MEDIALINKS_COMMENT","コメント機能を有効にする");
define("_MI_MEDIALINKS_COMMENT_DESC","コンテンツに対するコメント機能を有効にする");

// Notifications
define('_MI_MEDIALINKS_GLOBAL_NOTIFY', 'モジュール全体');
define('_MI_MEDIALINKS_GLOBAL_NOTIFY_DESC', 'イベント案内モジュール全体における通知オプション');
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
define("_MI_CONTENT_TITLE", "表題");
define("_MI_CONTENT_DESC", "説明文");
define("_MI_CONTENT_POSTD", "作成日時");
define("_MI_CONTENT_MODIFY", "更新日時");
define("_MI_CONTENT_WEIGHT", "表示順");
define("_MI_CONTENT_POSTER", "作成者");
define("_MI_CONTENT_HITS", "アクセス数");
define("_MI_CONTENT_IMAGE", "参考画像");
define("_MI_CONTENT_MEDIA", "メディア");
define("_MI_CONTENT_ATTACH", "添付ファイル");
?>