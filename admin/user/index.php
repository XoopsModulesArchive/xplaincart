<?php
require_once dirname(__DIR__, 2) . '/library/define.php';
require_once XOOPS_ROOT_PATH . '/include/cp_header.php';
require_once dirname(__DIR__, 2) . '/library/init_class.php';	//include database_class inside
require_once dirname(__DIR__) . '/library/admin_func_class.php';
class user_index_class
{
    public function user_index_display()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        require_once XOOPS_ROOT_PATH . '/class/template.php';

        $xoopsTpl = new XoopsTpl();

        $init = new init_class();

        $admin_func = new admin_func_class();

        $_SESSION['login_return_url'] = $_SERVER['REQUEST_URI'];

        $admin_func->checkUser();

        $view = (isset($_GET['view']) && '' != $_GET['view']) ? $_GET['view'] : '';

        $xoopsTpl->assign('view', $view);

        switch ($view) {
            case 'list':
                $db = new database_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                $sql = 'SELECT user_id, user_name, user_regdate, user_last_login
				        FROM ' . PREFIX . 'user
						ORDER BY user_name';
                $result = $db->dbQuery($sql);
                $data = [];
                $i = 0;
                while ($row = $db->dbFetchAssoc($result)) {
                    extract($row);

                    if ($i % 2) {
                        $class = 'row1';
                    } else {
                        $class = 'row2';
                    }

                    $i += 1;

                    $data[] = ['class' => $class, 'user_name' => $user_name, 'user_regdate' => $user_regdate, 'user_last_login' => $user_last_login, 'user_id' => $user_id];
                }	//while
                $xoopsTpl->assign('data', $data);
                $pageTitle = 'Shop Admin Control Panel - View Users';
                break;
            case 'add':
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                $errorMessage = (isset($_GET['error']) && '' != $_GET['error']) ? $_GET['error'] : '&nbsp;';
                $xoopsTpl->assign('errorMessage', $errorMessage);
                $pageTitle = 'Shop Admin Control Panel - Add Users';
                break;
            case 'modify':
                $db = new database_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                if (isset($_GET['userId']) && (int)$_GET['userId'] > 0) {
                    $userId = (int)$_GET['userId'];
                } else {
                    header('Location: index.php');
                }
                $errorMessage = (isset($_GET['error']) && '' != $_GET['error']) ? $_GET['error'] : '&nbsp;';
                $sql = 'SELECT user_name
				        FROM ' . PREFIX . "user
				        WHERE user_id = $userId";
                $result = $db->dbQuery($sql);
                extract($db->dbFetchAssoc($result));
                $xoopsTpl->assign('errorMessage', $errorMessage);
                $xoopsTpl->assign('user_name', $user_name);
                $xoopsTpl->assign('userId', $userId);
                $pageTitle = 'Shop Admin Control Panel - Modify Users';
                break;
            default:
                $db = new database_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                $sql = 'SELECT user_id, user_name, user_regdate, user_last_login
				        FROM ' . PREFIX . 'user
						ORDER BY user_name';
                $result = $db->dbQuery($sql);
                $data = [];
                $i = 0;
                while ($row = $db->dbFetchAssoc($result)) {
                    extract($row);

                    if ($i % 2) {
                        $class = 'row1';
                    } else {
                        $class = 'row2';
                    }

                    $i += 1;

                    $data[] = ['class' => $class, 'user_name' => $user_name, 'user_regdate' => $user_regdate, 'user_last_login' => $user_last_login, 'user_id' => $user_id];
                }	//while
                $xoopsTpl->assign('data', $data);
                $pageTitle = 'Shop Admin Control Panel - View Users';
                break;
        }

        $xoopsTpl->assign('script', '<script language="JavaScript" type="text/javascript" src="' . WEB_ROOT . 'admin/library/user.js"></script>');

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

        //$xoopsTpl->assign('content',$content);

        $xoopsTpl->display('db:admin_user_index.html');
    }
}
//$xoopsOption['template_main']='admin_user_index.html';
xoops_cp_header();
$user_index = new user_index_class();
$user_index->user_index_display();
xoops_cp_footer();
