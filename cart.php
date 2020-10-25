<?php
require_once dirname(__DIR__, 2) . '/mainfile.php';
require_once __DIR__ . '/library/define.php';
require_once __DIR__ . '/library/init_class.php';
require_once __DIR__ . '/library/cart_func_class.php';
class cart_class
{
    public function cart_display()
    {
        global $xoopsTpl;

        $init = new init_class();

        $shopConfig = $init->getShopConfig();	//for shipping cost

        $action = (isset($_GET['action']) && '' != $_GET['action']) ? $_GET['action'] : 'view';

        $cart_func = new cart_func_class();

        switch ($action) {
            case 'add':
                $cart_func->addToCart();
                break;
            case 'update':
                $cart_func->updateCart();
                break;
            case 'delete':
                $cart_func->deleteFromCart();
                break;
            case 'view':
        }

        $cartContent = $cart_func->getCartContent();

        $numItem = count($cartContent);

        $xoopsTpl->assign('numItem', count($cartContent));

        $pageTitle = 'Shopping Cart';

        //require_once __DIR__ . '/include/header.php';

        // show the error message ( if we have any )

        $init->displayError();

        if ($numItem > 0) {
            $data = [];

            $subTotal = 0;

            for ($i = 0; $i < $numItem; $i++) {
                extract($cartContent[$i]);

                $productUrl = "index.php?c=$cat_id&p=$pd_id";

                $subTotal += $pd_price * $ct_qty;

                $data[] = ['pd_url' => $productUrl, 'pd_thumbnail' => $pd_thumbnail, 'pd_name' => $pd_name, 'pd_price' => $pd_price, 'ct_qty' => $ct_qty, 'ct_id' => $ct_id, 'pd_id' => $pd_id, 'pd_subtotal' => $init->displayAmount($pd_price * $ct_qty)];
            }

            ////////////header

            require __DIR__ . '/include/header.php';

            ////////////content

            $xoopsTpl->assign('data', $data);

            $xoopsTpl->assign('subTotal', $init->displayAmount($subTotal));

            $xoopsTpl->assign('shippingCost', $init->displayAmount($shopConfig['shippingCost']));

            $xoopsTpl->assign('total', $init->displayAmount($subTotal + $shopConfig['shippingCost']));

            $xoopsTpl->assign('shoppingReturnUrl', $_SESSION['shop_return_url'] ?? 'index.php');

            ////////////footer

            require __DIR__ . '/include/footer.php';
        }
    }
}
ob_start();
$xoopsOption['template_main'] = 'cart.html';
require XOOPS_ROOT_PATH . '/header.php';
$cart_show = new cart_class();
$cart_show->cart_display();
require XOOPS_ROOT_PATH . '/footer.php';
ob_end_flush();
