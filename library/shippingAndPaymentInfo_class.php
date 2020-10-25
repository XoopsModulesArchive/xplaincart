<?php
require_once dirname(__DIR__, 2) . '/mainfile.php';
require_once __DIR__ . '/define.php';
//require_once SMARTY_DIR.'Smarty.class.php';
class shippingAndPaymentInfo_class
{
    public function shippingAndPaymentInfo_display()
    {
        $xoopsOption['template_main'] = 'shippingAndPaymentInfo.html';

        //require XOOPS_ROOT_PATH.'/header.php';

        $errorMessage = '&nbsp;';

        $xoopsTpl->assign('errorMessage', $errorMessage);

        $xoopsTpl->assign('self', $_SERVER['PHP_SELF']);

        //return $this->fetch('shippingAndPaymentInfo.html');
        //require_once XOOPS_ROOT_PATH.'footer.php';
    }
}
