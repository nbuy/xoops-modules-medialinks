<?php
# MediaLinks - register default feilds info
# $Id: oninstall.php,v 1.1 2006/07/12 16:27:25 nobu Exp $
global $xoopsDB;

$fields = array(
    'title'	=> _MI_CONTENT_TITLE,
    'description'=>_MI_CONTENT_DESC,
    'ctime'	=> _MI_CONTENT_POSTD,
    'mtime'	=> _MI_CONTENT_MODIFY,
    'weight'	=> _MI_CONTENT_WEIGHT,
    'poster'	=> _MI_CONTENT_POSTER,
    'media'	=> _MI_CONTENT_MEDIA,
    'attach'	=> _MI_CONTENT_ATTACH,
    'hits'	=> _MI_CONTENT_HITS);

$deftypes = array(
    'title'=>'varchar(128)', 'description'=>'text',
    'ctime'=>'timestamp', 'mtime'=>'timestamp',
    'poster'=>'user', 'media'=>'link','attach'=>'link');

$sql = "INSERT INTO ".$xoopsDB->prefix('medialinks_fields')."(name, label, type, def) VALUES(%s,%s,%s,%s)";

foreach ($fields as $k=>$v) {
    $type = isset($deftypes[$k])?$deftypes[$k]:'integer';
    $w = ($k == 'weight')?1:"";
    $res = $xoopsDB->query(sprintf($sql,
				   $xoopsDB->quoteString($k),
				   $xoopsDB->quoteString($v),
				   $xoopsDB->quoteString($type),
				   $xoopsDB->quoteString($w)));
}
?>