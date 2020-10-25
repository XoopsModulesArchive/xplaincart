<?php
require_once dirname(__DIR__, 2) . '/mainfile.php';
require_once __DIR__ . '/define.php';
require_once __DIR__ . '/init_class.php';
require_once __DIR__ . '/cart_func_class.php';
//require_once SMARTY_DIR.'Smarty.class.php';
class checkoutConfirmation_class
{
    public function checkoutConfirmation_display()
    {
        $init = new init_class();

        $shopConfig = $init->getShopConfig();

        $cart_func = new cart_func_class();

        if (!defined('WEB_ROOT')
            || !isset($_GET['step']) || 2 != (int)$_GET['step']
            || \Xmf\Request::getString('HTTP_REFERER', '', 'SERVER') != 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?step=1') {
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

            $data[] = ['product' => '$ct_qty x $pd_name', 'pd_price' => $init->displayAmount($pd_price), 'pd_subtotal' => $init->displayAmount($ct_qty * $pd_price)];
        }

        $xoopsOption['template_main'] = 'checkoutConfirmation.html';

        //require XOOPS_ROOT_PATH.'/header.php';

        $xoopsTpl->assign('subTotal', $subTotal);

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

        //return $this->fetch('checkoutConfirmation.html');
        //require_once XOOPS_ROOT_PATH.'/footer.php';
    }
}
