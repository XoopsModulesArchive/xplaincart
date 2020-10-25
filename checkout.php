<?php

use Xmf\Request;

require_once dirname(__DIR__, 2) . '/mainfile.php';
require_once __DIR__ . '/library/define.php';
require_once __DIR__ . '/library/init_class.php';
require_once __DIR__ . '/library/cart_func_class.php';
require_once __DIR__ . '/library/checkout_func_class.php';
class checkout_class
{
    public $orderId;

    public $orderAmount;

    public function checkout()
    {
        global $xoopsTpl;

        global $xoopsModuleConfig;

        $cart_func = new cart_func_class();

        $init = new init_class();

        $checkout_func = new checkout_func_class();

        $shopConfig = $init->getShopConfig();

        if ($cart_func->isCartEmpty()) {
            // the shopping cart is still empty

            // so checkout is not allowed

            header('Cache-Control: no-cache, must-revalidate');

            header('Location: cart.php');
        } elseif (isset($_GET['step']) && (int)$_GET['step'] > 0 && (int)$_GET['step'] <= 3) {
            $step = (int)$_GET['step'];

            $xoopsTpl->assign('step', $step);

            if (1 == $step) {
                //////////////////shippingAndPaymentInfo

                //////////////////header

                require __DIR__ . '/include/header.php';

                //////////////////content

                $errorMessage = '&nbsp;';

                $xoopsTpl->assign('errorMessage', $errorMessage);

                $xoopsTpl->assign('self', $_SERVER['PHP_SELF']);

                //////////////////footer

                require __DIR__ . '/include/footer.php';
            } elseif (2 == $step) {
                //////////////////checkoutConfirmation

                //////////////////header

                require __DIR__ . '/include/header.php';

                //////////////////content

                $init = new init_class();

                $shopConfig = $init->getShopConfig();

                $cart_func = new cart_func_class();

                if (!defined('WEB_ROOT')
                    || !isset($_GET['step']) || 2 != (int)$_GET['step']
                    || Request::getString('HTTP_REFERER', '', 'SERVER') != 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?step=1') {
                    exit;
                }

                $errorMessage = '&nbsp;';

                /*
                 Make sure all the required field exist is $_POST and the value is not empty
                 Note: txtShippingAddress2 and txtPaymentAddress2 are optional
                */

                $requiredField = ['txtShippingFirstName', 'txtShippingLastName', 'txtShippingAddress1', 'txtShippingPhone', 'txtShippingState',  'txtShippingCity', 'txtShippingPostalCode',
                               'txtPaymentFirstName', 'txtPaymentLastName', 'txtPaymentAddress1', 'txtPaymentPhone', 'txtPaymentState', 'txtPaymentCity', 'txtPaymentPostalCode', ];

                if (!$init->checkRequiredPost($requiredField)) {
                    $errorMessage = 'Input not complete';
                }

                $cartContent = $cart_func->getCartContent();

                $numItem = count($cartContent);

                $data = [];

                $subTotal = 0;

                for ($i = 0; $i < $numItem; $i++) {
                    extract($cartContent[$i]);

                    $subTotal += $pd_price * $ct_qty;

                    $data[] = ['product' => $ct_qty . ' x ' . $pd_name, 'pd_price' => $init->displayAmount($pd_price), 'pd_subtotal' => $init->displayAmount($ct_qty * $pd_price)];
                }

                $xoopsTpl->assign('subTotal', $init->displayAmount($subTotal));

                $xoopsTpl->assign('shippingCost', $init->displayAmount($shopConfig['shippingCost']));

                $xoopsTpl->assign('total', $init->displayAmount($shopConfig['shippingCost'] + $subTotal));

                $xoopsTpl->assign('data', $data);

                $xoopsTpl->assign('ShippingFirstName', $_POST['txtShippingFirstName']);

                $xoopsTpl->assign('ShippingLastName', $_POST['txtShippingLastName']);

                $xoopsTpl->assign('ShippingAddress1', $_POST['txtShippingAddress1']);

                $xoopsTpl->assign('ShippingAddress2', $_POST['txtShippingAddress2']);

                $xoopsTpl->assign('ShippingPhone', $_POST['txtShippingPhone']);

                $xoopsTpl->assign('ShippingState', $_POST['txtShippingState']);

                $xoopsTpl->assign('ShippingCity', $_POST['txtShippingCity']);

                $xoopsTpl->assign('ShippingPostalCode', $_POST['txtShippingPostalCode']);

                $xoopsTpl->assign('PaymentFirstName', $_POST['txtPaymentFirstName']);

                $xoopsTpl->assign('PaymentLastName', $_POST['txtPaymentLastName']);

                $xoopsTpl->assign('PaymentAddress1', $_POST['txtPaymentAddress1']);

                $xoopsTpl->assign('PaymentAddress2', $_POST['txtPaymentAddress2']);

                $xoopsTpl->assign('PaymentPhone', $_POST['txtPaymentPhone']);

                $xoopsTpl->assign('PaymentState', $_POST['txtPaymentState']);

                $xoopsTpl->assign('PaymentCity', $_POST['txtPaymentCity']);

                $xoopsTpl->assign('PaymentPostalCode', $_POST['txtPaymentPostalCode']);

                $xoopsTpl->assign('optPayment', $_POST['optPayment']);

                //////////////////footer

                require __DIR__ . '/include/footer.php';
            } elseif (3 == $step) {
                $orderId = $checkout_func->saveOrder();

                $this->orderId = $orderId;

                $orderAmount = $checkout_func->getOrderAmount($orderId);

                $this->orderAmount = $orderAmount;

                $_SESSION['orderId'] = $orderId;

                // our next action depends on the payment method

                // if the payment method is COD then show the

                // success page but when paypal is selected

                // send the order details to paypal

                if ('cod' == $_POST['hidPaymentMethod']) {
                    header('Cache-Control: no-cache, must-revalidate');

                    header('Location: success.php');

                    exit;
                }  

                require __DIR__ . '/include/header.php';

                require __DIR__ . '/include/paypal/payment.php';

                require __DIR__ . '/include/footer.php';
            }
        } else {
            // missing or invalid step number, just redirect

            header('Cache-Control: no-cache, must-revalidate');

            header('Location: index.php');
        }
    }
}
$xoopsOption['template_main'] = 'checkout_index.html';
require XOOPS_ROOT_PATH . '/header.php';
$checkout = new checkout_class();
$checkout->checkout();
require XOOPS_ROOT_PATH . '/footer.php';
