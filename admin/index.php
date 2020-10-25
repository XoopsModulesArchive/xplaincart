<?php
require_once dirname(__DIR__, 3) . '/mainfile.php';
////////////////////////!
require_once XOOPS_ROOT_PATH . '/include/cp_header.php';
////////////////////////^
require_once dirname(__DIR__) . '/library/define.php';
require_once dirname(__DIR__) . '/library/init_class.php';
require_once __DIR__ . '/library/admin_func_class.php';
class admin_index_class
{
    public function admin_index()
    {
        ////////////////!

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        require_once XOOPS_ROOT_PATH . '/class/template.php';

        $xoopsTpl = new XoopsTpl();

        ////////////////^

        if (!defined('WEB_ROOT')) {
            exit;
        }

        $init = new init_class();

        //$shopConfig=$init->getShopConfig();

        //$content = 'main.php';

        $xoopsTpl->assign('css_dir', WEB_ROOT . 'admin/include/admin.css');

        $xoopsTpl->assign('pageTitle', 'Shop Admin');

        $xoopsTpl->assign('script', '');

        $xoopsTpl->assign('banner_top', 'include/banner-top.gif');

        $xoopsTpl->assign('home', WEB_ROOT . 'admin/');

        $xoopsTpl->assign('category', WEB_ROOT . 'admin/category/');

        $xoopsTpl->assign('product', WEB_ROOT . 'admin/product/');

        $xoopsTpl->assign('order', WEB_ROOT . 'admin/order/?status=Paid');

        $xoopsTpl->assign('config', WEB_ROOT . 'admin/config/');

        $xoopsTpl->assign('user', WEB_ROOT . 'admin/user/');

        $xoopsTpl->assign('logout', 'index.php?logout');

        ////////////////!

        $xoopsTpl->display('db:admin_index.html');

        ////////////////^
    }
}
$admin_func = new admin_func_class();
$admin_func->checkUser();
////////////////////////!
//$xoopsOption['template_main']='admin_index.html';
xoops_cp_header();
////////////////////////^
$admin_index = new admin_index_class();
$admin_index->admin_index();
////////////////////////!
xoops_cp_footer();
////////////////////////^
