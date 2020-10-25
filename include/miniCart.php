<?php
require_once __DIR__ . '/library/define.php';
require_once __DIR__ . '/library/cart_func_class.php';
$cart_func = new cart_func_class();
$init = new init_class();
$shopConfig = $init->getShopConfig();
$cartContent = $cart_func->getCartContent();
$numItem = count($cartContent);
$subtotal = 0;
$shippingCost = 0;
$total = 0;
if ($numItem > 0) {
    $subTotal = 0;

    $data = [];

    for ($i = 0; $i < $numItem; $i++) {
        extract($cartContent[$i]);

        $subTotal += $pd_price * $ct_qty;

        $data[] = ['pd_name' => "$ct_qty x $pd_name", 'url' => "index.php?c=$cat_id&p=$pd_id", 'pd_subtotal' => $init->displayAmount($pd_price * $ct_qty)];
    }

    $subtotal = $init->displayAmount($subTotal);

    $shippingCost = $init->displayAmount($init->shopConfig['shippingCost']);

    $total = $init->displayAmount($subTotal + $init->shopConfig['shippingCost']);
}
$xoopsTpl->assign('minicart_data', $data);
$xoopsTpl->assign('numItem', $numItem);
$xoopsTpl->assign('subtotal', $subtotal);
$xoopsTpl->assign('shippingCost', $shippingCost);
$xoopsTpl->assign('total', $total);
