<?php
ob_start();
require_once dirname(__DIR__, 2) . '/mainfile.php';
require_once __DIR__ . '/library/define.php';
require_once __DIR__ . '/library/init_class.php';
require_once __DIR__ . '/library/cart_func_class.php';
class plaincart_index_class
{
    public function plaincart()
    {
        global $xoopsTpl;

        global $xoopsModuleConfig;

        $init = new init_class();	//trim POST,GET construction
        $shopConfig = $init->getShopConfig();		//for footer only and for troubleshoot values display in the footer
        static $pageTitle;

        $_SESSION['shop_return_url'] = $_SERVER['REQUEST_URI'];

        $catId = (isset($_GET['c']) && '1' != $_GET['c']) ? $_GET['c'] : 0;

        $pdId = (isset($_GET['p']) && '' != $_GET['p']) ? $_GET['p'] : 0;

        if ($pdId) {
            $pageTitle = 'Product Detail Page';
        } elseif ($catId) {
            $pageTitle = 'Product List Page';
        } else {
            $pageTitle = 'Category List Page';
        }

        $xoopsTpl->assign('pdId', $pdId);

        $xoopsTpl->assign('catId', $catId);

        ////////////////header

        require __DIR__ . '/include/header.php';

        ////////////////top

        require __DIR__ . '/include/top.php';

        ////////////////leftNav

        require __DIR__ . '/include/leftNav.php';

        ////////////////content

        if ($pdId) {
            ////////////pd_detail

            //if(isset($_GET['action']))

            //	if($_GET['action']=='add'){

            //		$cart_func=new cart_func_class;

            //		$cart_func->addToCart();

            //}

            require __DIR__ . '/include/pd_detail.php';
        } elseif ($catId) {
            ////////////pd_list

            require __DIR__ . '/include/pd_list.php';
        } else {
            ////////////cat_list

            require __DIR__ . '/include/cat_list.php';
        }

        ////////////////miniCart

        require __DIR__ . '/include/miniCart.php';

        ////////////////footer

        require __DIR__ . '/include/footer.php';
    }
}
$xoopsOption['template_main'] = 'plaincart_index.html';
require XOOPS_ROOT_PATH . '/header.php';
$plaincart_index = new plaincart_index_class();
$plaincart_index->plaincart();
require XOOPS_ROOT_PATH . '/footer.php';
ob_end_flush();
