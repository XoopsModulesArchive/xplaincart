<?php
require_once dirname(__DIR__, 3) . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/include/cp_header.php';
require_once dirname(__DIR__) . '/library/define.php';
require_once dirname(__DIR__) . '/library/init_class.php';
require_once __DIR__ . '/library/admin_func_class.php';
class login_class
{
    public function login_display()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        require_once XOOPS_ROOT_PATH . '/class/template.php';

        $xoopsTpl = new XoopsTpl();

        $init = new init_class();

        //$shopConfig=$init->getShopConfig();

        $admin_func = new admin_func_class();

        $errorMessage = '&nbsp;';

        if (isset($_POST['txtUserName'])) {
            $result = $admin_func->doLogin();

            if ('' != $result) {
                $errorMessage = $result;
            }
        }

        $xoopsTpl->assign('css_dir', WEB_ROOT . 'admin/include/admin.css');

        $xoopsTpl->assign('$errorMessage', $errorMessage);

        $xoopsTpl->assign('plaincart', WEB_ROOT . 'index.php');

        $xoopsTpl->display('db:admin_login.html');
    }
}
//$xoopsOption['template_main']='admin_login.html';
xoops_cp_header();
$login = new login_class();
$login->login_display();
xoops_cp_footer();
