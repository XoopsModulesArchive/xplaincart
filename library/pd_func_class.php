<?php
require_once __DIR__ . '/database_class.php';
class pd_func_class
{
    public function getProductDetail($pdId, $catId)
    {
        $db = new database_class();

        $_SESSION['shoppingReturnUrl'] = $_SERVER['REQUEST_URI'];

        // get the product information from database

        $sql = 'SELECT pd_name, pd_description, pd_price, pd_image, pd_qty
				FROM ' . PREFIX . "product
				WHERE pd_id = $pdId";

        $result = $db->dbQuery($sql);

        $row = $db->dbFetchAssoc($result);

        extract($row);

        $row['pd_description'] = nl2br($row['pd_description']);

        if ($row['pd_image']) {
            $row['pd_image'] = PRODUCT_IMAGE_DIR . $row['pd_image'];
        } else {
            $row['pd_image'] = BASE_IMAGE_DIR . 'no-image-large.png';
        }

        $row['cart_url'] = "cart.php?action=add&p=$pdId&c=$catId";

        //$row['cart_url'] = $_SERVER['PHP_SELF']."?action=add&p=$pdId";

        return $row;
    }
}
/*
    Get detail information of a product
*/
