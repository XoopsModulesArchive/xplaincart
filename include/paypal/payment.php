<?php
require_once __DIR__ . '/library/define.php';
require_once __DIR__ . '/library/init_class.php';
if (!isset($orderId)) {
    exit;
}
$init = new init_class();
$shopConfig = $init->getShopConfig();
require_once __DIR__ . '/paypal.inc.php';
if (isset($xoopsModuleConfig['ItemName'])) {
    $paypal['item_name'] = $xoopsModuleConfig['ItemName'];
} else {
    $paypal['item_name'] = 'PlainCart Purchase';
}
//$paypal['item_name'] = "PlainCart Purchase";
$paypal['invoice'] = $orderId;
$paypal['amount'] = $orderAmount;
?>
<center>
<p>&nbsp;</p>
<p><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="333333">Processing 
      Transaction . . . OrderId = <?echo $orderId;?></font></p>
</center>
<form action="<?php echo $paypal['url']; ?>" method="post" name="frmPaypal" id="frmPaypal">
	<input type="hidden" name="amount" value="<?php echo $paypal['amount']; ?>">
	<input type="hidden" name="invoice" value="<?php echo $paypal['invoice']; ?>">
	<input type="hidden" name="item_name" value="<?php echo $paypal['item_name']; ?>">
	<input type="hidden" name="business" value="<?php echo $paypal['business']; ?>"> 
	<input type="hidden" name="cmd" value="<?php echo $paypal['cmd']; ?>"> 
	<input type="hidden" name="return" value="<?php echo  $paypal['site_url'] . $paypal['success_url']; ?>">
	<input type="hidden" name="cancel_return" value="<?php echo $paypal['site_url'] . $paypal['cancel_url']; ?>">
	<input type="hidden" name="notify_url" value="<?php echo  $paypal['site_url'] . $paypal['notify_url']; ?>">
	<input type="hidden" name="rm" value="<?php echo $paypal['return_method']; ?>">
	<input type="hidden" name="currency_code" value="<?php echo $paypal['currency_code']; ?>">
	<input type="hidden" name="lc" value="<?php echo $paypal['lc']; ?>">
	<input type="hidden" name="bn" value="<?php echo $paypal['bn']; ?>">
	<input type="hidden" name="no_shipping" value="<?php echo $paypal['display_shipping_address']; ?>">
</form>
<script language="JavaScript" type="text/javascript">
	window.onload=function() {
	window.document.frmPaypal.submit();
}
</script>
<?php
