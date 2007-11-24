<?php
# Medialinks - blocksupport
# $Id: medialinks_block.php,v 1.6 2007/11/24 09:49:14 nobu Exp $

include_once dirname(dirname(__FILE__))."/functions.php";
// options: [0] order, [1] lines, [2] strlen

function b_medialinks_show($options) {
    return ml_index_view($options[0]." DESC", $options[2], $options[3], $options[4], $options[1], 0, _BLOCK_MEDIALINKS_DFMT);
}

function b_ml_select($name, $opts, $def) {
    $buf = "<select name='$name'>\n";
    foreach ($opts as $val=>$label) {
	$sel = ($val == $def)?' selected="selected"':'';
	$buf .= "<option value='$val'$sel>$label</option>\n";
    }
    $buf .= "</select>";
    return $buf;
}

function b_medialinks_edit($options) {
    $order_items = array('ctime'=>_BLOCK_SORT_CTIME,
			 'mtime'=>_BLOCK_SORT_MTIME,
			 'hits' =>_BLOCK_SORT_HITS);
    return _BLOCK_SORT_ORDER." ".b_ml_select('options[0]', $order_items, $options[0])."<br/>\n".
	_BLOCK_MEDIALINKS_LINES." <input name='options[1]' size='5' value='".$options[1]."'/><br />\n".
	_BLOCK_MEDIALINKS_TRIM." <input name='options[2]' size='5' value='".$options[2]."'/><br />\n".
	_BLOCK_MEDIALINKS_CATS." <input name='options[3]' value='".$options[3]."'/><br />\n".
	_BLOCK_MEDIALINKS_STYLE." ".b_ml_select('options[4]', explode(',',_BLOCK_MEDIALINKS_TYPES), $options[4]);
}
?>