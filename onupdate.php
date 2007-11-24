<?php
# medialinks module onUpdate proceeding.
# $Id: onupdate.php,v 1.1 2007/11/24 09:49:13 nobu Exp $

global $xoopsDB;

define('MAIN', $xoopsDB->prefix('medialinks'));
define('ACLS', $xoopsDB->prefix('medialinks_access'));

// addional field in 2.0
add_field(MAIN, 'nacl', "INT DEFAULT 0 NOT NULL", 'hits');

$xoopsDB->query('SELECT * FROM '.ACLS, 1);
if ($xoopsDB->errno()) { // check exists
    $msgs[] = "Update Database...";
    $msgs[] = "&nbsp;&nbsp; Add new table: <b>medialinks_access</b>";
    $xoopsDB->query("CREATE TABLE ".ACLS." (
    auid	integer NOT NULL default '0',
    amid	integer NOT NULL default '0',
    writable	ENUM('Y', 'N') NOT NULL default 'N',
    KEY (auid, amid)
)");
}

// reset nacl value
$xoopsDB->query('UPDATE '.MAIN." SET nacl=0");
$res = $xoopsDB->query('SELECT mid,count(auid) FROM '.MAIN.', '.ACLS.' WHERE mid=amid GROUP BY mid');
$n = $xoopsDB->getRowsNum($res);
$msgs[] = "Access control entry in Database ($n)";
while (list($mid, $nacl) = $xoopsDB->fetchRow($res)) {
    $xoopsDB->query("UPDATE ".MAIN." SET nacl=$nacl WHERE mid=$mid");
}

function add_field($table, $field, $type, $after) {
    global $xoopsDB;
    $res = $xoopsDB->query("SELECT $field FROM $table", 1);
    if (empty($res) && $xoopsDB->errno()) { // check exists
	if ($after) $after = "AFTER $after";
	$res = $xoopsDB->query("ALTER TABLE $table ADD $field $type $after");
    } else return false;
    if (!$res) {
	echo "<div class='errorMsg'>".$xoopsDB->errno()."</div>\n";
    }
    return $res;
}
?>