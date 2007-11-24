/*
     一覧のキーワードのサーチの書きかけ断片
*/
define("_AM_SEARCH_KEYS", "キーワード");
define("_AM_SEARCH_SUBMIT", "絞り込む");

    // keywords form
    echo "<form>";
    echo "<b>"._AM_SEARCH_KEYS."</b> ";
    $keys = array();
    foreach ($_GET['key'] as $v) {
	if (empty($v)) $keys[] = $v;
    }
    foreach ($keywords->getTree() as $key) {
	$keys = $keywords->getKeys(array(0,2), $key['child']);
	$obj = new KeyFormSelect('', 'key['.$key['keyid'].']');
	array_unshift($keys, array('keyid'=>'', 'name'=>_AM_KEY_NONE));
	$obj->addOptions($keys);
	echo htmlspecialchars($key['name']).' '.$obj->render()." &nbsp; ";
    }
    echo "<input type='submit' value='"._AM_SEARCH_SUBMIT."'/>";
    echo "</form>";

