<?php
//Troubleshooting area
//if(isset($xoopsModuleConfig['Paypal'])){
//	$info="Paypal set value=".$xoopsModuleConfig['Paypal'];
//}else{
//	$info="Paypal no config pass through data.";
//}
//if(isset($xoopsModuleConfig['ItemName']))
//	$info=$info.$xoopsModuleConfig['ItemName'];
//else
//	$info=$info."No Item name";
//$xoopsTpl->assign('info',$info);
$xoopsTpl->assign('thisyear', date('Y'));
$xoopsTpl->assign('name', $shopConfig['name']);
$xoopsTpl->assign('address', $shopConfig['address']);
$xoopsTpl->assign('phone', $shopConfig['phone']);
$xoopsTpl->assign('email', $shopConfig['email']);
