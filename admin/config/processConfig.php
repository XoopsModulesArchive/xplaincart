<?php
//require_once dirname(__DIR__, 2) . '/library/config.php';
//require_once dirname(__DIR__) . '/library/functions.php';
require_once dirname(__DIR__, 2) . '/library/define.php';
require_once dirname(__DIR__, 2) . '/library/init_class.php';
require_once dirname(__DIR__) . '/library/admin_func_class.php';
class process_config_class
{
    public function process_config()
    {
        $init = new init_class();

        //$shopConfig=$init->getShopConfig();

        $admin_func = new admin_func_class();

        //$db=new database_class;

        $admin_func->checkUser();

        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'modify':
                $this->modifyShopConfig();
                break;
            default:
                // if action is not defined or unknown
                // move to main page
                header('Cache-Control: no-cache, must-revalidate');
                header('Location: index.php');
        }
    }

    public function modifyShopConfig()
    {
        $db = new database_class();

        $shopName = $_POST['txtShopName'];

        $address = $_POST['mtxAddress'];

        $phone = $_POST['txtPhone'];

        $email = $_POST['txtEmail'];

        $shipping = $_POST['txtShippingCost'];

        $currency = $_POST['cboCurrency'];

        $sendEmail = $_POST['optSendEmail'];

        $sql = 'UPDATE ' . PREFIX . "shop_config
	            SET sc_name = '$shopName', sc_address = '$address', sc_phone = '$phone', sc_email = '$email',
				    sc_shipping_cost = $shipping, sc_currency = $currency, sc_order_email = '$sendEmail'";

        $db->dbQuery($sql);

        header('Cache-Control: no-cache, must-revalidate');

        header('Location: index.php');
    }
}
$process_config = new process_config_class();
$process_config->process_config();
