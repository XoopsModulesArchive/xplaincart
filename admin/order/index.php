<?php
require_once dirname(__DIR__, 2) . '/library/define.php';
require_once XOOPS_ROOT_PATH . '/include/cp_header.php';
require_once dirname(__DIR__, 2) . '/library/init_class.php';
require_once dirname(__DIR__) . '/library/admin_func_class.php';
class order_index_class
{
    public function order_index_display()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        require_once XOOPS_ROOT_PATH . '/class/template.php';

        $xoopsTpl = new XoopsTpl();

        $init = new init_class();	

        $admin_func = new admin_func_class();

        $_SESSION['login_return_url'] = $_SERVER['REQUEST_URI'];

        $admin_func = new admin_func_class();

        $admin_func->checkUser();

        $view = (isset($_GET['view']) && '' != $_GET['view']) ? $_GET['view'] : '';

        $xoopsTpl->assign('view', $view);

        switch ($view) {
            case 'list':
                $init = new init_class();
                $db = new database_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                if (isset($_GET['status']) && '' != $_GET['status']) {
                    $status = $_GET['status'];

                    $sql2 = " AND od_status = '$status'";

                    $queryString = "&status=$status";
                } else {
                    $status = '';

                    $sql2 = '';

                    $queryString = '';
                }
                // for paging
                // how many rows to show per page
                $rowsPerPage = 10;
                $sql = 'SELECT o.od_id, o.od_shipping_first_name, od_shipping_last_name, od_date, od_status,
				               SUM(pd_price * od_qty) + od_shipping_cost AS od_amount
					    FROM ' . PREFIX . 'order o, ' . PREFIX . 'order_item oi, ' . PREFIX . "product p 
						WHERE oi.pd_id = p.pd_id and o.od_id = oi.od_id $sql2
						GROUP BY od_id
						ORDER BY od_id DESC";
                $pagingLink = $init->getPagingLink($sql, $rowsPerPage, $queryString);
                $result = $db->dbQuery($init->getPagingQuery($sql, $rowsPerPage));
                $orderStatus = ['New', 'Paid', 'Shipped', 'Completed', 'Cancelled'];
                $orderOption = '';
                foreach ($orderStatus as $stat) {
                    $orderOption .= "<option value=\"$stat\"";

                    if ($stat == $status) {
                        $orderOption .= ' selected';
                    }

                    $orderOption .= ">$stat</option>\r\n";
                }
                $data = [];
                $parentId = 0;
                $haveOrder = $db->dbNumRows($result);
                if ($haveOrder > 0) {
                    $i = 0;

                    while ($row = $db->dbFetchAssoc($result)) {
                        extract($row);

                        $name = $od_shipping_first_name . ' ' . $od_shipping_last_name;

                        if ($i % 2) {
                            $class = 'row1';
                        } else {
                            $class = 'row2';
                        }

                        $i += 1;

                        $data[] = ['class' => $class, 'self' => $_SERVER['PHP_SELF'] . '?view=detail&oid=' . $od_id, 'od_id' => $od_id, 'name' => $name, 'od_amount' => $init->displayAmount($od_amount), 'od_date' => $od_date, 'od_status' => $od_status];
                    }

                    $xoopsTpl->assign('haveOrder', $haveOrder);

                    $xoopsTpl->assign('data', $data);

                    $xoopsTpl->assign('orderOption', $orderOption);

                    $xoopsTpl->assign('pagingLink', $pagingLink);
                } else {
                    $xoopsTpl->assign('orderOption', $orderOption);	//still show option
                }
                $pageTitle = 'Shop Admin Control Panel - View Orders';
                break;
            case 'detail':
                $db = new database_class();
                $init = new init_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                if (!isset($_GET['oid']) || (int)$_GET['oid'] <= 0) {
                    header('Location: index.php');
                }
                $orderId = (int)$_GET['oid'];
                // get ordered items
                $sql = 'SELECT pd_name, pd_price, od_qty
					    FROM ' . PREFIX . 'order_item oi, ' . PREFIX . "product p 
						WHERE oi.pd_id = p.pd_id and oi.od_id = $orderId
						ORDER BY od_id ASC";
                $result = $db->dbQuery($sql);
                $orderedItem = [];
                while (false !== ($row = $db->dbFetchAssoc($result))) {
                    $orderedItem[] = $row;
                }
                // get order information
                $sql = 'SELECT od_date, od_last_update, od_status, od_shipping_first_name, od_shipping_last_name, od_shipping_address1, od_shipping_address2, od_shipping_phone,		od_shipping_state, od_shipping_city, od_shipping_postal_code, od_shipping_cost, od_payment_first_name, od_payment_last_name, od_payment_address1, od_payment_address2,		od_payment_phone,od_payment_state, od_payment_city , od_payment_postal_code,od_memo FROM ' . PREFIX . "order WHERE od_id = $orderId";
                $result = $db->dbQuery($sql);
                extract($db->dbFetchAssoc($result));
                $orderStatus = ['New', 'Paid', 'Shipped', 'Completed', 'Cancelled'];
                $orderOption = '';
                foreach ($orderStatus as $status) {
                    $orderOption .= "<option value=\"$status\"";

                    if ($status == $od_status) {
                        $orderOption .= ' selected';
                    }

                    $orderOption .= ">$status</option>\r\n";
                }	//foreach
                $data = [];
                $numItem = count($orderedItem);
                $subTotal = 0;
                for ($i = 0; $i < $numItem; $i++) {
                    extract($orderedItem[$i]);

                    $subTotal += $pd_price * $od_qty;

                    $data[] = ['sub_pd_qty' => $od_qty . ' x ' . $pd_name, 'pd_price' => $init->displayAmount($pd_price), 'sub_pd_total' => $init->displayAmount($od_qty * $pd_price)];
                }	//for
                $xoopsTpl->assign('data', $data);
                $xoopsTpl->assign('orderId', $orderId);
                $xoopsTpl->assign('od_date', $od_date);
                $xoopsTpl->assign('od_last_update', $od_last_update);
                $xoopsTpl->assign('orderOption', $orderOption);
                $xoopsTpl->assign('subTotal', $init->displayAmount($subTotal));
                $xoopsTpl->assign('od_shipping_cost', $od_shipping_cost);
                $xoopsTpl->assign('total', $init->displayAmount($od_shipping_cost + $subTotal));
                $xoopsTpl->assign('od_shipping_first_name', $od_shipping_first_name);
                $xoopsTpl->assign('od_shipping_last_name', $od_shipping_last_name);
                $xoopsTpl->assign('od_shipping_address1', $od_shipping_address1);
                $xoopsTpl->assign('od_shipping_address2', $od_shipping_address2);
                $xoopsTpl->assign('od_shipping_phone', $od_shipping_phone);
                $xoopsTpl->assign('od_shipping_state', $od_shipping_state);
                $xoopsTpl->assign('od_shipping_city', $od_shipping_city);
                $xoopsTpl->assign('od_shipping_postal_code', $od_shipping_postal_code);
                $xoopsTpl->assign('od_payment_first_name', $od_payment_first_name);
                $xoopsTpl->assign('od_payment_last_name', $od_payment_last_name);
                $xoopsTpl->assign('od_payment_address1', $od_payment_address1);
                $xoopsTpl->assign('od_payment_address2', $od_payment_address2);
                $xoopsTpl->assign('od_payment_phone', $od_payment_phone);
                $xoopsTpl->assign('od_payment_state', $od_payment_state);
                $xoopsTpl->assign('od_payment_city', $od_payment_city);
                $xoopsTpl->assign('od_payment_postal_code', $od_payment_postal_code);
                $xoopsTpl->assign('od_memo', nl2br($od_memo));
                $pageTitle = 'Shop Admin Control Panel - Order Detail';
                break;
            //case 'modify' :
            //	modifyStatus();
                //$content 	= 'modify.php';
                //$pageTitle 	= 'Shop Admin Control Panel - Modify Orders';
                //break;
            default:
                $init = new init_class();
                $db = new database_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                if (isset($_GET['status']) && '' != $_GET['status']) {
                    $status = $_GET['status'];

                    $sql2 = " AND od_status = '$status'";

                    $queryString = "&status=$status";
                } else {
                    $status = '';

                    $sql2 = '';

                    $queryString = '';
                }
                // for paging
                // how many rows to show per page
                $rowsPerPage = 10;
                $sql = 'SELECT o.od_id, o.od_shipping_first_name, od_shipping_last_name, od_date, od_status,
				               SUM(pd_price * od_qty) + od_shipping_cost AS od_amount
					    FROM ' . PREFIX . 'order o, ' . PREFIX . 'order_item oi, ' . PREFIX . "product p 
						WHERE oi.pd_id = p.pd_id and o.od_id = oi.od_id $sql2
						GROUP BY od_id
						ORDER BY od_id DESC";
                $pagingLink = $init->getPagingLink($sql, $rowsPerPage, $queryString);
                $result = $db->dbQuery($init->getPagingQuery($sql, $rowsPerPage));
                $orderStatus = ['New', 'Paid', 'Shipped', 'Completed', 'Cancelled'];
                $orderOption = '';
                foreach ($orderStatus as $stat) {
                    $orderOption .= "<option value=\"$stat\"";

                    if ($stat == $status) {
                        $orderOption .= ' selected';
                    }

                    $orderOption .= ">$stat</option>\r\n";
                }
                $data = [];
                $parentId = 0;
                $haveOrder = $db->dbNumRows($result);
                if ($haveOrder > 0) {
                    $i = 0;

                    while ($row = $db->dbFetchAssoc($result)) {
                        extract($row);

                        $name = $od_shipping_first_name . ' ' . $od_shipping_last_name;

                        if ($i % 2) {
                            $class = 'row1';
                        } else {
                            $class = 'row2';
                        }

                        $i += 1;

                        $data[] = ['class' => $class, 'self' => $_SERVER['PHP_SELF'] . '?view=detail&oid=' . $od_id, 'od_id' => $od_id, 'name' => $name, 'od_amount' => $init->displayAmount($od_amount), 'od_date' => $od_date, 'od_status' => $od_status];
                    }

                    $xoopsTpl->assign('haveOrder', $haveOrder);

                    $xoopsTpl->assign('data', $data);

                    $xoopsTpl->assign('orderOption', $orderOption);

                    $xoopsTpl->assign('pagingLink', $pagingLink);
                } else {
                    $xoopsTpl->assign('orderOption', $orderOption);	//still show option
                }
                $pageTitle = 'Shop Admin Control Panel - View Orders';
                break;
        }

        $xoopsTpl->assign('script', '<script language="JavaScript" type="text/javascript" src="' . WEB_ROOT . 'admin/library/order.js"></script>');

        $xoopsTpl->assign('css_dir', WEB_ROOT . 'admin/include/admin.css');

        $xoopsTpl->assign('WEB_ROOT', WEB_ROOT);

        $xoopsTpl->assign('pageTitle', 'Shop Admin');

        $xoopsTpl->assign('banner_top', WEB_ROOT . 'admin/include/banner-top.gif');

        $xoopsTpl->assign('home', WEB_ROOT . 'admin/');

        $xoopsTpl->assign('category', WEB_ROOT . 'admin/category/');

        $xoopsTpl->assign('product', WEB_ROOT . 'admin/product/');

        $xoopsTpl->assign('order', WEB_ROOT . 'admin/order/?status=Paid');

        $xoopsTpl->assign('config', WEB_ROOT . 'admin/config/');

        $xoopsTpl->assign('user', WEB_ROOT . 'admin/user/');

        $xoopsTpl->assign('logout', '../index.php?logout');

        $xoopsTpl->display('db:admin_order_index.html');
    }
}
//$xoopsOption['template_main']='admin_order_index.html';
xoops_cp_header();
$order_index = new order_index_class();
$order_index->order_index_display();
xoops_cp_footer();
