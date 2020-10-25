<?php
require_once __DIR__ . '/library/define.php';
require_once __DIR__ . '/library/init_class.php';	//for function getProductDetail
require_once __DIR__ . '/library/pd_func_class.php';	//for function displayAmount
$init = new init_class();
$shopConfig = $init->getShopConfig();
$pd_func = new pd_func_class();
$product = $pd_func->getProductDetail($pdId, $catId);
// we have $pd_name, $pd_price, $pd_description, $pd_image, $cart_url
extract($product);
$pd_name = $pd_name;
$pd_price = $pd_price;
$pd_description = $pd_description;
$pd_image = $pd_image;
$cart_url = $cart_url;
$pd_qty = $pd_qty;
$xoopsTpl->assign('pd_name', $pd_name);
$xoopsTpl->assign('pd_title', $pd_title);
$xoopsTpl->assign('pd_price', $init->displayAmount($pd_price));
$xoopsTpl->assign('pd_description', $pd_description);
$xoopsTpl->assign('pd_image', $pd_image);
$xoopsTpl->assign('cart_url', $cart_url);
$xoopsTpl->assign('pd_qty', $pd_qty);
$xoopsTpl->assign('currency', $shopConfig['currency_code']);

