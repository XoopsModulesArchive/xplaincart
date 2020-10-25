<?php
require_once __DIR__ . '/define.php';
require_once __DIR__ . '/database_class.php';
require_once __DIR__ . '/init_class.php';
class cart_func_class
{
    public function addToCart()	//used in productDetail.html template
    {
        $db = new database_class();

        // make sure the product id exist

        if (isset($_GET['p']) && (int)$_GET['p'] > 0) {
            $productId = (int)$_GET['p'];

            if (isset($_GET['c']) && (int)$_GET['c'] > 0) {
                $catId = (int)$_GET['c'];
            }
        } else {
            header('Location: index.php');
        }

        // does the product exist ?

        $sql = 'SELECT pd_id, pd_qty
		        FROM ' . PREFIX . "product
				WHERE pd_id = $productId";

        $result = $db->dbQuery($sql);

        if (1 != $db->dbNumRows($result)) {
            // the product doesn't exist

            header('Location:index.php');
        } else {
            // how many of this product we

            // have in stock

            $row = $db->dbFetchAssoc($result);

            $currentStock = $row['pd_qty'];

            if (0 == $currentStock) {
                // we no longer have this product in stock

                // show the error message

                $init = new init_class();

                $init->setError('The product you requested is no longer in stock');

                header('Location:index.hpp');

                exit;
            }
        }

        // current session id

        $sid = session_id();

        // check if the product is already

        // in cart table for this session

        $sql = 'SELECT pd_id
		        FROM ' . PREFIX . "cart
				WHERE pd_id = $productId AND ct_session_id = '$sid'";

        $result = $db->dbQuery($sql);

        if (0 == $db->dbNumRows($result)) {
            // put the product in cart table

            $sql = 'INSERT INTO ' . PREFIX . "cart (pd_id, ct_qty, ct_session_id, ct_date)
					VALUES ($productId, 1, '$sid', NOW())";

            $result = $db->dbQuery($sql);

            if (!$result) {
                die('Invalid query: ' . $GLOBALS['xoopsDB']->error());
            }
        } else {
            // update product quantity in cart table

            $sql = 'UPDATE ' . PREFIX . "cart 
			        SET ct_qty = ct_qty + 1
					WHERE ct_session_id = '$sid' AND pd_id = $productId";

            $result = $db->dbQuery($sql);
        }

        //		// an extra job for us here is to remove abandoned carts.

        //		// right now the best option is to call this function here

        $this->deleteAbandonedCart();

        header('Location:' . $_SESSION['shop_return_url']);

        //header('Location:index.php'.'?p='.$productId.'&c='.$catId);
    }

    /*
        Get all item in current session
        from shopping cart table
    */

    public function getCartContent()
    {
        $db = new database_class();

        $cartContent = [];

        $sid = session_id();

        $sql = 'SELECT ct_id, ct.pd_id, ct_qty, pd_name, pd_price, pd_thumbnail, pd.cat_id
				FROM ' . PREFIX . 'cart ct, ' . PREFIX . 'product pd, ' . PREFIX . "category cat
				WHERE ct_session_id = '$sid' AND ct.pd_id = pd.pd_id AND cat.cat_id = pd.cat_id";

        $result = $db->dbQuery($sql);

        while (false !== ($row = $db->dbFetchAssoc($result))) {
            if ($row['pd_thumbnail']) {
                $row['pd_thumbnail'] = PRODUCT_IMAGE_DIR . $row['pd_thumbnail'];
            } else {
                $row['pd_thumbnail'] = BASE_IMAGE_DIR . 'no-image-small.png';
            }

            $cartContent[] = $row;
        }

        return $cartContent;
    }

    /*
        Remove an item from the cart
    */

    public function deleteFromCart($cartId = 0)
    {
        $db = new database_class();

        if (!$cartId && isset($_GET['cid']) && (int)$_GET['cid'] > 0) {
            $cartId = (int)$_GET['cid'];
        }

        if ($cartId) {
            $sql = 'DELETE FROM ' . PREFIX . "cart
					 WHERE ct_id = $cartId";

            $result = $db->dbQuery($sql);
        }

        header('Location:' . $_SESSION['shop_return_url']);
    }

    /*
        Update item quantity in shopping cart
    */

    public function updateCart()
    {
        $db = new database_class();

        $init = new init_class();

        $cartId = $_POST['hidCartId'];

        $productId = $_POST['hidProductId'];

        $itemQty = $_POST['txtQty'];

        $numItem = count($itemQty);

        $numDeleted = 0;

        $notice = '';

        for ($i = 0; $i < $numItem; $i++) {
            $newQty = (int)$itemQty[$i];

            if ($newQty < 1) {
                // remove this item from shopping cart

                $this->deleteFromCart($cartId[$i]);

                $numDeleted += 1;
            } else {
                // check current stock

                $sql = 'SELECT pd_name, pd_qty
				        FROM ' . PREFIX . "product 
						WHERE pd_id = {$productId[$i]}";

                $result = $db->dbQuery($sql);

                $row = $db->dbFetchAssoc($result);

                if ($newQty > $row['pd_qty']) {
                    // we only have this much in stock

                    $newQty = $row['pd_qty'];

                    // if the customer put more than

                    // we have in stock, give a notice

                    if ($row['pd_qty'] > 0) {
                        $init->setError('The quantity you have requested is more than we currently have in stock. The number available is indicated in the &quot;Quantity&quot; box. ');
                    } else {
                        // the product is no longer in stock

                        $init->setError('Sorry, but the product you want (' . $row['pd_name'] . ') is no longer in stock');

                        // remove this item from shopping cart

                        $this->deleteFromCart($cartId[$i]);

                        $numDeleted += 1;
                    }
                }

                // update product quantity

                $sql = 'UPDATE ' . PREFIX . "cart
						SET ct_qty = $newQty
						WHERE ct_id = {$cartId[$i]}";

                $result = $db->dbQuery($sql);
            }
        }

        if ($numDeleted == $numItem) {
            // if all item deleted return to the last page that

            // the customer visited before going to shopping cart

            header("Location: $returnUrl" . $_SESSION['shop_return_url']);
        } else {
            header('Location:' . $_SESSION['shop_return_url']);
        }

        exit;
    }

    public function isCartEmpty()
    {
        $db = new database_class();

        $isEmpty = false;

        $sid = session_id();

        $sql = 'SELECT ct_id
				FROM ' . PREFIX . "cart ct
				WHERE ct_session_id = '$sid'";

        $result = $db->dbQuery($sql);

        if (0 == $db->dbNumRows($result)) {
            $isEmpty = true;
        }

        return $isEmpty;
    }

    /*
        Delete all cart entries older than one day
    */

    public function deleteAbandonedCart()
    {
        $db = new database_class();

        $yesterday = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));

        $sql = 'DELETE FROM ' . PREFIX . "cart
		        WHERE ct_date < '$yesterday'";

        $result = $db->dbQuery($sql);
    }
}
