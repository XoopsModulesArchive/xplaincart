<?php
require_once dirname(__DIR__, 2) . '/library/define.php';
require_once XOOPS_ROOT_PATH . '/include/cp_header.php';
require_once dirname(__DIR__, 2) . '/library/init_class.php';
require_once dirname(__DIR__) . '/library/admin_func_class.php';
class product_index_class
{
    public function product_index_display()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        require_once XOOPS_ROOT_PATH . '/class/template.php';

        $xoopsTpl = new XoopsTpl();

        $init = new init_class();

        $_SESSION['login_return_url'] = $_SERVER['REQUEST_URI'];

        $admin_func = new admin_func_class();

        $admin_func->checkUser();

        $view = (isset($_GET['view']) && '' != $_GET['view']) ? $_GET['view'] : '';

        $xoopsTpl->assign('view', $view);

        switch ($view) {
            case 'list':
                $init = new init_class();
                $db = new database_class();
                $admin_func = new admin_func_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                if (isset($_GET['catId']) && (int)$_GET['catId'] > 0) {
                    $catId = (int)$_GET['catId'];

                    $sql2 = " AND p.cat_id = $catId";

                    $queryString = "catId=$catId";
                } else {
                    $catId = 0;

                    $sql2 = '';

                    $queryString = '';
                }
                // for paging
                // how many rows to show per page
                $rowsPerPage = 5;
                $sql = 'SELECT pd_id, c.cat_id, cat_name, pd_name,pd_title, pd_thumbnail
				        FROM ' . PREFIX . 'product p, ' . PREFIX . "category c
						WHERE p.cat_id = c.cat_id $sql2
						ORDER BY pd_name";
                $pagingLink = $init->getPagingLink($sql, $rowsPerPage, $queryString);
                $result = $db->dbQuery($init->getPagingQuery($sql, $rowsPerPage));
                $categoryList = $admin_func->buildCategoryOptions($catId);
                $parentId = 0;
                $haveProduct = $db->dbNumRows($result);
                if ($haveProduct > 0) {
                    $data = [];

                    $i = 0;

                    while ($row = $db->dbFetchAssoc($result)) {
                        extract($row);

                        if ($pd_thumbnail) {
                            $pd_thumbnail = WEB_ROOT . 'images/product/' . $pd_thumbnail;
                        } else {
                            $pd_thumbnail = WEB_ROOT . 'images/no-image-small.png';
                        }

                        if ($i % 2) {
                            $class = 'row1';
                        } else {
                            $class = 'row2';
                        }

                        $i += 1;

                        $data[] = ['class' => $class, 'pd_name' => $pd_name, 'pd_title' => $pd_title, 'pd_thumbnail' => $pd_thumbnail, 'cat_name' => $cat_name, 'pd_id' => $pd_id, 'catId' => $catId];
                    }	//while

                    $xoopsTpl->assign('haveProduct', $haveProduct);

                    $xoopsTpl->assign('categoryList', $categoryList);

                    $xoopsTpl->assign('catId', $catId);

                    $xoopsTpl->assign('pagingLink', $pagingLink);

                    $xoopsTpl->assign('data', $data);
                } else {
                    $xoopsTpl->assign('categoryList', $categoryList);	//still show category
                }
                $pageTitle = 'Shop Admin Control Panel - View Product';
                break;
            case 'add':
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                $catId = (isset($_GET['catId']) && $_GET['catId'] > 0) ? $_GET['catId'] : 0;
                $admin_func = new admin_func_class();
                $categoryList = $admin_func->buildCategoryOptions($catId);
                $xoopsTpl->assign('categoryList', $categoryList);
                $pageTitle = 'Shop Admin Control Panel - Add Product';
                break;
            case 'modify':
                $init = new init_class();
                $db = new database_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                // make sure a product id exists
                if (isset($_GET['productId']) && $_GET['productId'] > 0) {
                    $productId = $_GET['productId'];
                } else {
                    // redirect to index.php if product id is not present

                    header('Location: index.php');
                }
                // get product info
                $sql = 'SELECT pd.cat_id, pd_name,pd_title, pd_description, pd_price, pd_qty, pd_image, pd_thumbnail
				        FROM ' . PREFIX . 'product pd, ' . PREFIX . "category cat
						WHERE pd.pd_id = $productId AND pd.cat_id = cat.cat_id";
                //$result = $GLOBALS['xoopsDB']->queryF($sql) || die('Cannot get product. ' . $GLOBALS['xoopsDB']->error());
                $result = $db->dbQuery($sql);
                //$row    = $GLOBALS['xoopsDB']->fetchArray($result);
                $row = $db->dbFetchAssoc($result);
                extract($row);
                // get category list
                $sql = 'SELECT cat_id, cat_parent_id, cat_name
				        FROM ' . PREFIX . 'category
						ORDER BY cat_id';
                $result = $db->dbQuery($sql) || die('Cannot get Product. ' . $GLOBALS['xoopsDB']->error());
                $categories = [];
                while ($row = $db->dbFetchArray($result)) {
                    [$id, $parentId, $name] = $row;

                    if (0 == $parentId) {
                        $categories[$id] = ['name' => $name, 'children' => []];
                    } else {
                        $categories[$parentId]['children'][] = ['id' => $id, 'name' => $name];
                    }
                }	//while
                //echo '<pre>'; print_r($categories); echo '</pre>'; exit;
                // build combo box options
                $list = '';
                foreach ($categories as $key => $value) {
                    $name = $value['name'];

                    $children = $value['children'];

                    $list .= "<optgroup label=\"$name\">";

                    foreach ($children as $child) {
                        $list .= "<option value=\"{$child['id']}\"";

                        if ($child['id'] == $cat_id) {
                            $list .= ' selected';
                        }

                        $list .= ">{$child['name']}</option>";
                    }

                    $list .= '</optgroup>';
                }	//foreach
                if ('' == $pd_thumbnail) {
                    $haveimage = false;
                } else {
                    $haveimage = true;
                }
                $xoopsTpl->assign('haveimage', $haveimage);
                $xoopsTpl->assign('productId', $productId);
                $xoopsTpl->assign('list', $list);
                $xoopsTpl->assign('pd_name', $pd_name);
                $xoopsTpl->assign('pd_title', $pd_title);
                $xoopsTpl->assign('pd_description', $pd_description);
                $xoopsTpl->assign('pd_price', $pd_price);
                $xoopsTpl->assign('pd_qty', $pd_qty);
                $xoopsTpl->assign('pd_thumbnail', PRODUCT_IMAGE_DIR . $pd_thumbnail);
                $pageTitle = 'Shop Admin Control Panel - Modify Product';
                break;
            case 'detail':
                $init = new init_class();
                $db = new database_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                // make sure a product id exists
                if (isset($_GET['productId']) && $_GET['productId'] > 0) {
                    $productId = $_GET['productId'];
                } else {
                    // redirect to index.php if product id is not present

                    header('Location: index.php');
                }
                $sql = 'SELECT cat_name, pd_name,pd_title, pd_description, pd_price, pd_qty, pd_image
				        FROM ' . PREFIX . 'product pd, ' . PREFIX . "category cat
						WHERE pd.pd_id = $productId AND pd.cat_id = cat.cat_id";
                //$result = $GLOBALS['xoopsDB']->queryF($sql) || die('Cannot get product. ' . $GLOBALS['xoopsDB']->error());
                $result = $db->dbQuery($sql);
                //$row = $GLOBALS['xoopsDB']->fetchArray($result);
                $row = $db->dbFetchAssoc($result);
                extract($row);
                if ($pd_image) {
                    $pd_image = WEB_ROOT . 'images/product/' . $pd_image;
                } else {
                    $pd_image = WEB_ROOT . 'images/no-image-large.png';
                }
                $xoopsTpl->assign('cat_name', $cat_name);
                $xoopsTpl->assign('pd_name', $pd_name);
                $xoopsTpl->assign('pd_title', $pd_title);
                $xoopsTpl->assign('pd_description', nl2br($pd_description));
                $xoopsTpl->assign('pd_price', number_format($pd_price, 2));
                $xoopsTpl->assign('pd_qty', number_format($pd_qty));
                $xoopsTpl->assign('pd_image', $pd_image);
                $xoopsTpl->assign('productId', $productId);
                $pageTitle = 'Shop Admin Control Panel - View Product Detail';
                break;
            default:
                $init = new init_class();
                $db = new database_class();
                $admin_func = new admin_func_class();
                if (!defined('WEB_ROOT')) {
                    exit;
                }
                if (isset($_GET['catId']) && (int)$_GET['catId'] > 0) {
                    $catId = (int)$_GET['catId'];

                    $sql2 = " AND p.cat_id = $catId";

                    $queryString = "catId=$catId";
                } else {
                    $catId = 0;

                    $sql2 = '';

                    $queryString = '';
                }
                // for paging
                // how many rows to show per page
                $rowsPerPage = 5;
                $sql = 'SELECT pd_id, c.cat_id, cat_name, pd_name,pd_title, pd_thumbnail
				        FROM ' . PREFIX . 'product p, ' . PREFIX . "category c
						WHERE p.cat_id = c.cat_id $sql2
						ORDER BY pd_name";
                $pagingLink = $init->getPagingLink($sql, $rowsPerPage, $queryString);
                $result = $db->dbQuery($init->getPagingQuery($sql, $rowsPerPage));
                $categoryList = $admin_func->buildCategoryOptions($catId);
                $parentId = 0;
                $haveProduct = $db->dbNumRows($result);
                if ($haveProduct > 0) {
                    $data = [];

                    $i = 0;

                    while ($row = $db->dbFetchAssoc($result)) {
                        extract($row);

                        if ($pd_thumbnail) {
                            $pd_thumbnail = WEB_ROOT . 'images/product/' . $pd_thumbnail;
                        } else {
                            $pd_thumbnail = WEB_ROOT . 'images/no-image-small.png';
                        }

                        if ($i % 2) {
                            $class = 'row1';
                        } else {
                            $class = 'row2';
                        }

                        $i += 1;

                        $data[] = ['class' => $class, 'pd_name' => $pd_name, 'pd_title' => $pd_title, 'pd_thumbnail' => $pd_thumbnail, 'cat_name' => $cat_name, 'pd_id' => $pd_id, 'catId' => $catId];
                    }	//while

                    $xoopsTpl->assign('haveProduct', $haveProduct);

                    $xoopsTpl->assign('categoryList', $categoryList);

                    $xoopsTpl->assign('catId', $catId);

                    $xoopsTpl->assign('pagingLink', $pagingLink);

                    $xoopsTpl->assign('data', $data);

                //return $this->fetch('product_list.html');
                } else {
                    $xoopsTpl->assign('categoryList', $categoryList);	//still show category
                    //return $this->fetch('product_list.html');	//return blank
                }
                $pageTitle = 'Shop Admin Control Panel - View Product';
                break;
        }

        //require_once dirname(__DIR__) . '/include/template.php';

        $xoopsTpl->assign('script', '<script language="JavaScript" type="text/javascript" src="' . WEB_ROOT . 'admin/library/product.js"></script>');

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

        $xoopsTpl->display('db:admin_product_index.html');
    }
}
//$xoopsOption['template_main']='admin_product_index.html';
xoops_cp_header();
$product_index = new product_index_class();
$product_index->product_index_display();
xoops_cp_footer();
