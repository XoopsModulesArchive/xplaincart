<?php
require_once dirname(__DIR__, 2) . '/library/define.php';		//use client define
require_once XOOPS_ROOT_PATH . '/include/cp_header.php';
require_once dirname(__DIR__, 2) . '/library/init_class.php';	//use client init_class
require_once dirname(__DIR__) . '/library/admin_func_class.php';	 //admin function
class index_class
{
    public function index_display()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        require_once XOOPS_ROOT_PATH . '/class/template.php';

        $xoopsTpl = new XoopsTpl();

        $init = new init_class();

        $shopConfig = $init->getShopConfig();

        $admin_func = new admin_func_class();

        $db = new database_class();

        $_SESSION['login_return_url'] = $_SERVER['REQUEST_URI'];

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
                if (isset($_GET['catId']) && (int)$_GET['catId'] >= 0) {
                    $catId = (int)$_GET['catId'];

                    $queryString = "&catId=$catId";
                } else {
                    $catId = 0;

                    $queryString = '';
                }
                // for paging
                // how many rows to show per page
                $rowsPerPage = 5;
                $sql = 'SELECT cat_id, cat_parent_id, cat_name,cat_title, cat_description, cat_image
						FROM ' . PREFIX . "category
						WHERE cat_parent_id = $catId
						ORDER BY cat_name";
                $xoopsTpl->assign('pagingLink', $init->getPagingLink($sql, $rowsPerPage));
                $result = $db->dbQuery($init->getPagingQuery($sql, $rowsPerPage));
                $cat_parent_id = 0;
                $have_cat = $db->dbNumRows($result);
                $xoopsTpl->assign('have_cat', $have_cat);
                if ($have_cat > 0) {
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

                        if (0 == $cat_parent_id) {
                            $cat_name = "<a href=\"index.php?catId=$cat_id\">$cat_name</a>";
                        }

                        if ($cat_image) {
                            $cat_image = CATEGORY_IMAGE_DIR . $cat_image;
                        } else {
                            $cat_image = BASE_IMAGE_DIR . 'no-image-small.png';
                        }

                        $data[] = ['class' => $class, 'cat_name' => $cat_name, 'cat_title' => nl2br($cat_title), 'cat_description' => nl2br($cat_description), 'cat_id' => $cat_id, 'cat_image' => $cat_image];
                    } // end while
                } else {
                    $data[] = ['class' => '', 'cat_name' => '', 'cat_title' => '', 'cat_description' => '', 'cat_id' => '', 'cat_image' => ''];
                }
                $xoopsTpl->assign('data', $data);
                $xoopsTpl->assign('catId', $catId);
                $pageTitle = 'Shop Admin Control Panel - View Category';
                break;
            case 'add':
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                $parentId = (isset($_GET['parentId']) && $_GET['parentId'] > 0) ? $_GET['parentId'] : 0;
                $xoopsTpl->assign('parentId', $parentId);
                $pageTitle = 'Shop Admin Control Panel - Add Category';
                break;
            case 'modify':
                $db = new database_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                // make sure a category id exists
                if (isset($_GET['catId']) && (int)$_GET['catId'] > 0) {
                    $catId = (int)$_GET['catId'];
                } else {
                    header('Location:index.php');
                }
                $sql = 'SELECT cat_id, cat_name,cat_title, cat_description, cat_image
						FROM ' . PREFIX . "category
						WHERE cat_id = $catId";
                $result = $db->dbQuery($sql);
                $row = $db->dbFetchAssoc($result);
                extract($row);
                if ('' == $cat_image) {
                    $haveimage = false;
                } else {
                    $haveimage = true;
                }
                $xoopsTpl->assign('haveimage', $haveimage);
                $xoopsTpl->assign('catId', $catId);
                $xoopsTpl->assign('cat_name', $cat_name);
                $xoopsTpl->assign('cat_title', $cat_title);
                $xoopsTpl->assign('cat_description', $cat_description);
                $xoopsTpl->assign('cat_image', CATEGORY_IMAGE_DIR . $cat_image);
                $pageTitle = 'Shop Admin Control Panel - Modify Category';
                break;
            default:
                $init = new init_class();
                $db = new database_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                if (isset($_GET['catId']) && (int)$_GET['catId'] >= 0) {
                    $catId = (int)$_GET['catId'];

                    $queryString = "&catId=$catId";
                } else {
                    $catId = 0;

                    $queryString = '';
                }
                // for paging
                // how many rows to show per page
                $rowsPerPage = 5;
                $sql = 'SELECT cat_id, cat_parent_id, cat_name,cat_title, cat_description, cat_image
						FROM ' . PREFIX . "category
						WHERE cat_parent_id = $catId
						ORDER BY cat_name";
                $xoopsTpl->assign('pagingLink', $init->getPagingLink($sql, $rowsPerPage));
                $result = $db->dbQuery($init->getPagingQuery($sql, $rowsPerPage));
                $cat_parent_id = 0;
                $have_cat = $db->dbNumRows($result);
                $xoopsTpl->assign('have_cat', $have_cat);
                if ($have_cat > 0) {
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

                        if (0 == $cat_parent_id) {
                            $cat_name = "<a href=\"index.php?catId=$cat_id\">$cat_name</a>";
                        }

                        if ($cat_image) {
                            $cat_image = CATEGORY_IMAGE_DIR . $cat_image;
                        } else {
                            $cat_image = BASE_IMAGE_DIR . 'no-image-small.png';
                        }

                        $data[] = ['class' => $class, 'cat_name' => $cat_name, 'cat_title' => nl2br($cat_title), 'cat_description' => nl2br($cat_description), 'cat_id' => $cat_id, 'cat_image' => $cat_image];
                    } // end while
                } else {
                    $data[] = ['class' => '', 'cat_name' => '', 'cat_title' => '', 'cat_description' => '', 'cat_id' => '', 'cat_image' => ''];
                }
                $xoopsTpl->assign('data', $data);
                $xoopsTpl->assign('catId', $catId);
                $pageTitle = 'Shop Admin Control Panel - View Category';
        }

        $xoopsTpl->assign('script', '<script language="JavaScript" type="text/javascript" src=' . WEB_ROOT . 'admin/library/category.js></script>');

        $xoopsTpl->assign('css_dir', WEB_ROOT . 'admin/include/admin.css');

        $xoopsTpl->assign('pageTitle', 'Shop Admin');

        $xoopsTpl->assign('banner_top', WEB_ROOT . 'admin/include/banner-top.gif');

        $xoopsTpl->assign('home', WEB_ROOT . 'admin/');

        $xoopsTpl->assign('category', WEB_ROOT . 'admin/category/');

        $xoopsTpl->assign('product', WEB_ROOT . 'admin/product/');

        $xoopsTpl->assign('order', WEB_ROOT . 'admin/order/?status=Paid');

        $xoopsTpl->assign('config', WEB_ROOT . 'admin/config/');

        $xoopsTpl->assign('user', WEB_ROOT . 'admin/user/');

        $xoopsTpl->assign('logout', '../index.php?logout');

        $xoopsTpl->display('db:admin_cat_index.html');
    }
}
//$xoopsOption['template_main']='admin_cat_index.html';
xoops_cp_header();
$index = new index_class();
$index->index_display();
xoops_cp_footer();
