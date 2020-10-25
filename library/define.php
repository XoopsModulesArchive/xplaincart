<?php
if (file_exists('../../mainfile.php')) {
    require_once dirname(__DIR__, 2) . '/mainfile.php';
}		//xplaincart level
elseif (file_exists('../../../mainfile.php')) {
    require_once dirname(__DIR__, 3) . '/mainfile.php';
}	//library,include,admin level
elseif (file_exists('../../../../mainfile.php')) {
    require_once dirname(__DIR__, 4) . '/mainfile.php';
}	//admin/category,admin/user,include/paypal level
//elseif(file_exists('../../../../../mainfile.php'))
//	require_once dirname(__DIR__, 4) . '/../mainfile.php';
define('WEB_ROOT', str_replace([$_SERVER['DOCUMENT_ROOT'], 'library/define.php'], '', str_replace('\\', '/', __FILE__)));
define('SRV_ROOT', str_replace('library/define.php', '', str_replace('\\', '/', __FILE__)));
define('BASE_IMAGE_DIR', WEB_ROOT . 'images/');
define('CATEGORY_IMAGE_DIR', BASE_IMAGE_DIR . 'category/');
define('PRODUCT_IMAGE_DIR', BASE_IMAGE_DIR . 'product/');
define('ABS_CAT_DIR', SRV_ROOT . 'images/category/');
define('ABS_PD_DIR', SRV_ROOT . 'images/product/');
// all category image width must not
// exceed 75 pixels
define('MAX_CATEGORY_IMAGE_WIDTH', 75);
// do we need to limit the product image width?
// setting this value to 'true' is recommended
define('LIMIT_PRODUCT_WIDTH', true);
// maximum width for all product image
define('MAX_PRODUCT_IMAGE_WIDTH', 300);
// the width for product thumbnail
define('THUMBNAIL_WIDTH', 75);
define('PREFIX', XOOPS_DB_PREFIX . '_plain_');		//to use when access tables
