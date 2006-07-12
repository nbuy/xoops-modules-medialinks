<?php
# Medialinks - blocksupport
# $Id: medialinks_block.php,v 1.1 2006/07/12 16:27:26 nobu Exp $

global $order_items;
$order_items = array('ctime'=>_BLOCK_SORT_CTIME,
		     'mtime'=>_BLOCK_SORT_MTIME,
		     'hits' =>_BLOCK_SORT_HITS);

// options: [0] order, [1] lines, [2] strlen, [3] verb
function b_medialinks_show($options) {
    global $xoopsDB, $xoopsUser, $order_items;
    $myts =& MyTextSanitizer::getInstance();
    $dirname = basename(dirname(dirname(__FILE__)));
    $modurl = XOOPS_URL."/modules/$dirname";

    $order = $options[0];
    if (!isset($order_items[$order])) $order = 'ctime';
    $lines = intval($options[1]);
    $len   = intval($options[2]);
    $sql = "SELECT mid, title, description, ctime, mtime, poster, hits FROM ".
	$xoopsDB->prefix('medialinks')." WHERE status='N' ORDER BY $order DESC";
    $result = $xoopsDB->query($sql, $lines);
    echo $xoopsDB->error();

    $block = array('order'=>$order,
		   'order_by'=>$order_items[$order],
		   'lang_more'=>_BLOCK_MEDIALINKS_MORE,
		   'verbose'=>$options[3],
		   'dirname'=>$dirname,
		   'module_url'=>$modurl);
    $contents = array();
    while ($myrow = $xoopsDB->fetchArray($result)) {
	$myrow['title'] = htmlspecialchars(b_mysubstr($myrow["title"], $len));
	$myrow['description'] = $myts->displayTarea($myrow["description"]);
	$myrow['cdate'] = formatTimestamp($myrow['ctime'], _BLOCK_MEDIALINKS_DFMT);
	$myrow['mdate'] = formatTimestamp($myrow['mtime'], _BLOCK_MEDIALINKS_DFMT);
	$myrow['uname'] = XoopsUser::getUnameFromId($myrow['poster']);
	$contents[] = $myrow;
    }
    $block['contents'] = $contents;
    return $block;
}

function b_medialinks_edit($options) {
    global $order_items;
    if ($options[0]) {
	$sel0=" checked";
	$sel1="";
    } else {
	$sel0="";
	$sel1=" checked";
    }
    $select = _BLOCK_SORT_ORDER." <select name='options[0]'>\n";
    foreach ($order_items as $k => $v) {
	$sel = ($options[0]==$k)?' selected':'';
	$select .= "<option value='$k'$sel>$v</option>\n";
    }
    $select .= "</select><br/>\n";
    if ($options[3]) {
	$sel0 = ' checked'; $sel1 = '';
    } else {
	$sel0 = ''; $sel1 = ' checked';
    }
    return $select.
	_BLOCK_MEDIALINKS_LINES."&nbsp;<input name='options[1]' value='".$options[1]."'/><br/>\n".
	
	_BLOCK_MEDIALINKS_TRIM."&nbsp;<input name='options[2]' value='".$options[2]."'/><br/>\n".
	_BLOCK_MEDIALINKS_VERB." &nbsp; <input type='radio' name='options[3]' value='1'$sel0/>"._YES." &nbsp; \n".
	"<input type='radio' name='options[3]' value='0'$sel1/>"._NO."<br/>\n";
}

function b_mysubstr($text, $len) {
    if (strlen($text)<$len) return $text;
    if (XOOPS_USE_MULTIBYTES) {
	if (function_exists('mb_strcut')) {
	    return mb_strcut($text, 0, $len-1, _CHARSET)."...";
	}
    } else {
	return substr($text, 0, $len-1)."...";
    }
    return $text;
}
?>