<?php
require_once '../../../../wp-blog-header.php';
require_once untrailingslashit(get_template_directory()) . '/paypal/ipn_functions.php';
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
    $keyval = explode ('=', $keyval);
    if (count($keyval) == 2)
        $myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
    $get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
    if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
            $value = urlencode(stripslashes($value));
    } else {
            $value = urlencode($value);
    }
    $req .= "&$key=$value";
}
$paypal_url = (get_field('paypal_environment', 'option') == 'sandbox')?'https://www.sandbox.paypal.com/cgi-bin/webscr':'https://www.paypal.com/cgi-bin/webscr';
$ch = curl_init($paypal_url);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

if (!($res = curl_exec($ch))){
    curl_close($ch);
    exit();
}
curl_close($ch);
if (strcmp ($res, "VERIFIED") == 0) {    
    global $wpdb;
    $data = array();
    foreach ($_POST as $key => $value) {
        $data = array(
            'id_user' => $_POST['custom'],
            'ipn_key' => $key,
            'ipn_value' => $value
        );
        $wpdb->insert('wp_paypal_ipn', $data);
    }
    wp_mail('subhadip.sahoo@businessprodesigns.com', $_POST['txn_type'], print_r($_POST, TRUE));
    
    switch ($_POST['txn_type']){
        case "subscr_signup":
            wp_mail('subhadip.sahoo@businessprodesigns.com', 'subscr_signup', print_r($_POST, TRUE));
            func_subscr_signup($_POST);
            break;
        case "subscr_payment":
            wp_mail('subhadip.sahoo@businessprodesigns.com', 'subscr_payment', print_r($_POST, TRUE));
            func_subscr_payment($_POST);
            break;
        case "subscr_cancel":
            wp_mail('subhadip.sahoo@businessprodesigns.com', 'subscr_cancel', print_r($_POST, TRUE));
            func_subscr_cancel($_POST);
            break;
        case "subscr_failed":
            wp_mail('subhadip.sahoo@businessprodesigns.com', 'subscr_failed', print_r($_POST, TRUE));
            //func_subscr_failed($_POST);
            break;
        case "subscr_modify":
            wp_mail('subhadip.sahoo@businessprodesigns.com', 'subscr_modify', print_r($_POST, TRUE));
            //func_subscr_modify($_POST);
            break;
        case "subscr_eot":
            wp_mail('subhadip.sahoo@businessprodesigns.com', 'subscr_eot', print_r($_POST, TRUE));
            //func_subscr_eot($_POST);
            break;
        case "web_accept":
            wp_mail('subhadip.sahoo@businessprodesigns.com', 'web_accept', print_r($_POST, TRUE));
            //func_web_accept($_POST);
            break;
        case "cart":
            wp_mail('subhadip.sahoo@businessprodesigns.com', 'cart', print_r($_POST, TRUE));
            func_cart($_POST);
            break;
        default :
            break;
    } 
} 
else if (strcmp ($res, "INVALID") == 0) {
	
}
?>