<?php
require_once __DIR__ . '/library/define.php';
require_once __DIR__ . '/library/cat_func_class.php';	//for function getChildCategories
require_once __DIR__ . '/library/init_class.php';			//for getPagingLink,getPagingQuery
require_once __DIR__ . '/library/database_class.php';
$cat_func = new cat_func_class();
$db = new database_class();
$init = new init_class();
$shopConfig = $init->getShopConfig();
$productsPerRow = 1;
$rows = $productsPerRow;
$productsPerPage = 4;
$cols = $productsPerPage / $productsPerRow;
//$productList    = getProductList($catId);
$children = array_merge([$catId], $cat_func->getChildCategories(null, $catId));
$children = ' (' . implode(', ', $children) . ')';
    $sql = 'SELECT pd_id, pd_name,pd_title,pd_description, pd_price, pd_thumbnail, pd_qty, c.cat_id
		FROM ' . PREFIX . 'product pd, ' . PREFIX . "category c
		WHERE pd.cat_id = c.cat_id AND pd.cat_id IN $children 
		ORDER BY pd_name";
$result = $db->dbQuery($init->getPagingQuery($sql, $productsPerPage));
$pagingLink = $init->getPagingLink($sql, $productsPerPage, "c=$catId");
$numProduct = $db->dbNumRows($result);
// the product images are arranged in a table. to make sure
// each image gets equal space set the cell width here
$columnWidth = (int)(100 / $productsPerRow);
if ($numProduct > 0) {
    $i = 0;

    $data = [];

    while (false !== ($row = $db->dbFetchAssoc($result))) {
        extract($row);

        if ($pd_thumbnail) {
            $pd_thumbnail = PRODUCT_IMAGE_DIR . $pd_thumbnail;
        } else {
            $pd_thumbnail = BASE_IMAGE_DIR . 'no-image-small.png';
        }

        // format how we display the price

        $pd_price = $init->displayAmount($pd_price);

        // if the product is no longer in stock, tell the customer

        if ($pd_qty <= 0) {
            $out_of_stock = '<br>Out Of Stock';
        } else {
            $out_of_stock = '';
        }

        $data[] = ['picture_width' => $columnWidth . '%', 'url' => $_SERVER['PHP_SELF'] . "?c=$catId&p=$pd_id", 'pd_thumbnail' => $pd_thumbnail, 'pd_name' => $pd_name, 'pd_price' => $pd_price, 'out_of_stock' => $out_of_stock, 'pd_title' => $pd_title, 'pd_description' => $pd_description];

        $i += 1;
    }
}
$xoopsTpl->assign('numProduct', $numProduct);
$xoopsTpl->assign('pdList_data', $data);
$xoopsTpl->assign('pagingLink', $pagingLink);
$xoopsTpl->assign('rows', $rows);
$xoopsTpl->assign('cols', $cols);
$xoopsTpl->assign('columnWidth', $columnWidth);
$xoopsTpl->assign('currency', $shopConfig['currency_code']);
