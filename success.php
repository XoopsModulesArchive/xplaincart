<?php
require_once dirname(__DIR__, 2) . '/mainfile.php';
require_once __DIR__ . '/library/define.php';
require_once __DIR__ . '/library/init_class.php';	//include database_class.php
require_once XOOPS_ROOT_PATH . '/class/mail/phpmailer/class.phpmailer.php';
require_once XOOPS_ROOT_PATH . '/class/mail/phpmailer/class.smtp.php';
class success_class
{
    public $pageTitle;

    public function success_display()
    {
        global $xoopsTpl;

        global $xoopsModuleConfig;

        $init = new init_class();

        $shopConfig = $init->getShopConfig();

        if (!isset($_SESSION['orderId'])) {
            header('Location: ' . WEB_ROOT);

            exit;
        }

        $this->pageTitle = 'Checkout Completed Successfully';

        //assume that our client already pay the bill but the Paypal IPN might still process the order

        //so cann't check the database right now.

        // send notification email

        if ('y' == $shopConfig['sendOrderEmail']) {
            $mail = new PHPMailer();

            $mail->IsSMTP();

            $mail->SMTPAuth = false;

            $mail->Host = '127.0.0.1';	//localhost some machine doesn't recognize localhost,must use 127.0.0.1

            $mail->AddAddress($shopConfig['email']);

            $mail->From = $shopConfig['email'];

            $mail->FromName = 'postmaster';

            $mail->WordWrap = 50;

            $mail->Subject = '[New Order] ' . $_SESSION['orderId'];

            $mail->AltBody = '';

            $mail->Body = "You have a new order. Check the order detail here \n http://" . $_SERVER['HTTP_HOST'] . WEB_ROOT . 'admin/order/index.php?view=detail&oid=' . $_SESSION['orderId'];

            if (!$mail->Send()) {
                $mail_response = 'Mail message could not be sent.<p>';

                $mail_response .= 'Mailer Error:' . $mail->ErrorInfo;
            } else {
                $mail_response = 'Email has been sent successfully to ' . $shopConfig['email'];
            }
        } else {
            $mail_response = 'Email not been sent due to email is disabled.';
        }

        unset($_SESSION['orderId']);

        ////////////header

        require __DIR__ . '/include/header.php';

        ////////////////content

        $xoopsTpl->assign('mail_response', $mail_response);

        ////////////////footer

        require __DIR__ . '/include/footer.php';
    }
}
// if no order id defined in the session
// redirect to main page
$xoopsOption['template_main'] = 'success.html';
require XOOPS_ROOT_PATH . '/header.php';
$success = new success_class();
$success->success_display();
require XOOPS_ROOT_PATH . '/footer.php';
?>
//<?php
//// read the post from PayPal system and add 'cmd'
//$req = 'cmd=_notify-validate';
//
//foreach ($_POST as $key => $value) {
//$value = urlencode(stripslashes($value));
//$req .= "&$key=$value";
//}
//
//// post back to PayPal system to validate
//$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
//$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
//$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
//$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
//
//
//
//if (!$fp) {
//// HTTP ERROR
//} else {
//fputs ($fp, $header . $req);
//while (!feof($fp)) {
//$res = fgets ($fp, 1024);
//if (strcmp ($res, "VERIFIED") == 0) {
//// check the payment_status is Completed
//// check that txn_id has not been previously processed
//// check that receiver_email is your Primary PayPal email
//// check that payment_amount/payment_currency are correct
//// process payment
//
//
//// echo the response
//echo "The response from IPN was: <b>" .$res ."</b><br><br>";
//
////loop through the $_POST array and print all vars to the screen.
//
//foreach($_POST as $key => $value){
//
//        echo $key." = ". $value."<br>";
//
//
//
//}
//
//
//}
//else if (strcmp ($res, "INVALID") == 0) {
//// log for manual investigation
//
//// echo the response
//echo "The response from IPN was: <b>" .$res ."</b>";
//
//  }
//
//}
//fclose ($fp);
//}
//?>


