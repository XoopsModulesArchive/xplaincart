<?php
require_once __DIR__ . '/library/define.php';
require_once __DIR__ . '/library/database_class.php';
$db = new database_class();
$pageTitle = '';
if (isset($_GET['p']) && (int)$_GET['p'] > 0) {
    $pdId = (int)$_GET['p'];

    $sql = 'SELECT pd_name
			FROM ' . PREFIX . "product
			WHERE pd_id = $pdId";

    $result = $db->dbQuery($sql);

    $row = $db->dbFetchAssoc($result);

    $pageTitle = $row['pd_name'];
} elseif (isset($_GET['c']) && (int)$_GET['c'] > 0) {
    $catId = (int)$_GET['c'];

    $sql = 'SELECT cat_name
	        FROM ' . PREFIX . "category
			WHERE cat_id = $catId";

    $result = $db->dbQuery($sql);

    $row = $db->dbFetchAssoc($result);

    $pageTitle = $row['cat_name'];
}
$xoopsTpl->assign('title', 'My Online Shop');
$xoopsTpl->assign('pageTitle', $pageTitle);
$xoopsTpl->assign('account', '');
$xoopsTpl->assign('shopping_cart', '');
$xoopsTpl->assign('checkout_shipping', '');
