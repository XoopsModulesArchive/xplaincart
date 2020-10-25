<?php
//require_once dirname(__DIR__, 2) . '/library/config.php';
//require_once dirname(__DIR__) . '/library/functions.php';
require_once dirname(__DIR__, 2) . '/library/define.php';
require_once dirname(__DIR__, 2) . '/library/init_class.php';
require_once dirname(__DIR__) . '/library/admin_func_class.php';
class process_order_class
{
    public function process_order()
    {
        $init = new init_class();

        $admin_func = new admin_func_class();

        $admin_func->checkUser();

        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'modify':
                $this->modifyOrder();
                break;
            default:
                // if action is not defined or unknown
                // move to main category page
                header('Location: index.php');
        }
    }

    public function modifyOrder()
    {
        $db = new database_class();

        if (!isset($_GET['oid']) || (int)$_GET['oid'] <= 0
            || !isset($_GET['status']) || '' == $_GET['status']) {
            header('Location: index.php');
        }

        $orderId = (int)$_GET['oid'];

        $status = $_GET['status'];

        $sql = 'UPDATE ' . PREFIX . "order
	            SET od_status = '$status', od_last_update = NOW()
	            WHERE od_id = $orderId";

        $db->dbQuery($sql);

        header("Location: index.php?view=list&status=$status");
    }
}
$process_order = new process_order_class();
$process_order->process_order();
