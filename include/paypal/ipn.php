<?php
require_once dirname(__DIR__, 2) . '/library/define.php';		//also include xoops mainfile.php
require_once dirname(__DIR__, 2) . '/library/init_class.php';	//also include database_class.php
class ipn_class
{
    public function ipn_task()
    {
        //$handle=fopen("ipn_test","w");

        //fwrite($handle,"This is a test.");

        //fclose($handle);

        // this page only process a POST from paypal website

        // so make sure that the one requesting this page comes

        // from paypal. we can do this by checking the remote address

        // the IP must begin with 66.135.197.

        if (false === mb_strpos($_SERVER['REMOTE_ADDR'], '66.135.197.')) {
            exit;
        }

        $init = new init_class();

        $shopConfig = $init->getShopConfig();

        require __DIR__ . '/paypal.inc.php';

        // repost the variables we get to paypal site

        // for validation purpose

        $result = fsockPost($paypal['url'], $_POST);

        //check the ipn result received back from paypal

        if (eregi('VERIFIED', $result)) {
            //require_once __DIR__ . '/library/define.php';

            // check that the invoice has not been previously processed

            $db = new database_class();

            $sql = 'SELECT od_status
	                FROM ' . PREFIX . "order
	                WHERE od_id = {$_POST['invoice']}";

            $result = $db->dbQuery($sql);

            // if no invoice with such number is found, exit

            if (0 == $db->dbNumRows($result)) {
                exit;
            }  

            $row = $db->dbFetchAssoc($result);

            // process this order only if the status is still 'New'

            if ('New' !== $row['od_status']) {
                exit;
            }  

            // check that the buyer sent the right amount of money

            $sql = 'SELECT SUM(pd_price * od_qty) AS subtotal
	                        FROM ' . PREFIX . 'order_item oi, ' . PREFIX . "product p
	                        WHERE oi.od_id = {$_POST['invoice']} AND oi.pd_id = p.pd_id
	                        GROUP by oi.od_id";

            $result = $db->dbQuery($sql);

            $row = $db->dbFetchAssoc($result);

            $subTotal = $row['subtotal'];

            $total = $subTotal + $shopConfig['shippingCost'];

            if ($_POST['payment_gross'] != $total) {
                exit;
            }  

            $invoice = $_POST['invoice'];

            $memo = $_POST['memo'];

            if (!get_magic_quotes_gpc()) {
                $memo = addslashes($memo);
            }

            // ok, so this order looks perfectly okay

            // now we can update the order status to 'Paid'

            // update the memo too

            $sql = 'UPDATE ' . PREFIX . "order
	                            SET od_status = 'Paid', od_memo = '$memo', od_last_update = NOW()
	                            WHERE od_id = $invoice";

            $db->dbQuery($sql);

        //echo "Done";
        } else {
            exit;
        }
    }
}	//end class
$ipn = new ipn_class();
$ipn->ipn_task();


