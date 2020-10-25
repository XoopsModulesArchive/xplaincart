<?php
require_once dirname(__DIR__, 2) . '/mainfile.php';
require_once __DIR__ . '/library/define.php';
require_once __DIR__ . '/library/init_class.php';
class error_class
{
    public $pageTitle;

    public function error_display()
    {
        global $xoopsTpl;

        global $xoopsModuleConfig;

        $init = new init_class();

        $shopConfig = $init->getShopConfig();

        ////////////header

        require __DIR__ . '/include/header.php';

        ////////////////content

        ////////////footer

        require __DIR__ . '/include/footer.php';
    }
}
$xoopsOption['template_main'] = 'error.html';
require XOOPS_ROOT_PATH . '/header.php';
$error = new error_class();
$error->error_display();
require XOOPS_ROOT_PATH . '/footer.php';
