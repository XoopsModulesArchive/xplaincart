<?php
require_once dirname(__DIR__, 2) . '/library/define.php';
require_once XOOPS_ROOT_PATH . '/include/cp_header.php';
require_once dirname(__DIR__, 2) . '/library/init_class.php';
require_once dirname(__DIR__) . '/library/admin_func_class.php';
class config_index_class
{
    public function config_index_display()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        require_once XOOPS_ROOT_PATH . '/class/template.php';

        $xoopsTpl = new XoopsTpl();

        $init = new init_class();

        $admin_func = new admin_func_class();

        $admin_func->checkUser();

        $view = (isset($_GET['view']) && '' != $_GET['view']) ? $_GET['view'] : '';

        switch ($view) {
            default:
                $db = new database_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                // get current configuration
                $sql = 'SELECT sc_name, sc_address, sc_phone, sc_email, sc_shipping_cost, sc_currency, sc_order_email
				        FROM ' . PREFIX . 'shop_config';
                $result = $db->dbQuery($sql);
                // extract the shop config fetched from database
                // make sure we query return a row
                if ($db->dbNumRows($result) > 0) {
                    extract($db->dbFetchAssoc($result));
                } else {
                    // since the query didn't return any row ( maybe because you don't run plaincart.sql as is )

                    // we just set blank values for all variables

                    $sc_name = $sc_address = $sc_phone = $sc_email = $sc_shipping_cost = $sc_currency = '';

                    $sc_order_email = 'y';
                }
                // get available currencies
                $sql = 'SELECT cy_id, cy_code
				        FROM ' . PREFIX . 'currency
						ORDER BY cy_code';
                $result = $db->dbQuery($sql);
                $currency = '';
                while (false !== ($row = $db->dbFetchAssoc($result))) {
                    extract($row);

                    $currency .= "<option value=\"$cy_id\"";

                    if ($cy_id == $sc_currency) {
                        $currency .= ' selected';
                    }

                    $currency .= ">$cy_code</option>\r\n";
                }
                $xoopsTpl->assign('currency', $currency);
                $xoopsTpl->assign('sc_name', $sc_name);
                $xoopsTpl->assign('sc_address', $sc_address);
                $xoopsTpl->assign('sc_phone', $sc_phone);
                $xoopsTpl->assign('sc_email', $sc_email);
                $xoopsTpl->assign('sc_shipping_cost', $sc_shipping_cost);
                $xoopsTpl->assign('sc_order_email_yes', 'y' == $sc_order_email ? 'checked' : '');
                $xoopsTpl->assign('sc_order_email_no', 'n' == $sc_order_email ? 'checked' : '');
                $pageTitle = 'Shop Admin Control Panel - Shop Configuration';
        }

        $xoopsTpl->assign('script', '<script language="JavaScript" type="text/javascript" src="' . WEB_ROOT . 'admin/library/shop.js"></script>');

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

        $xoopsTpl->display('db:admin_config_index.html');
    }
}
//$xoopsOption['template_main']='admin_config_index.html';
xoops_cp_header();
$config_index = new config_index_class();
$config_index->config_index_display();
xoops_cp_footer();
