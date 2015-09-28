<?php
$api_endpoint = (get_field('paypal_environment', 'option') == 'sandbox')?'https://api-3t.sandbox.paypal.com/nvp':'https://api-3t.paypal.com/nvp';
$payflow_api_endpoint = (get_field('paypal_environment', 'option') == 'sandbox')?'https://pilot-payflowpro.paypal.com':'https://payflowpro.paypal.com';
define('PAYPAL_API_USERNAME', get_field('paypal_api_username', 'option'));
define('PAYPAL_API_PASSWORD', get_field('paypal_api_password', 'option'));
define('PAYPAL_API_SIGNATURE', get_field('paypal_api_signature', 'option'));
define('PAYFLOW_API_USER', get_field('payflow_api_user', 'option'));
define('PAYFLOW_API_PARTNER', get_field('payflow_api_partner', 'option'));
define('PAYFLOW_API_VENDOR', get_field('payflow_api_vendor', 'option'));
define('PAYFLOW_API_PASSWORD', get_field('payflow_api_password', 'option'));
define('PAYPAL_API_VERSION', '86.0');
define('PAYPAL_API_ENDPOINT', $api_endpoint);
define('PAYFLOW_API_ENDPOINT', $payflow_api_endpoint);

function NVPToArray($NVPString){
    $proArray = array();
    while(strlen($NVPString)){
        $keypos= strpos($NVPString,'=');
        $keyval = substr($NVPString,0,$keypos);
        $valuepos = strpos($NVPString,'&') ? strpos($NVPString,'&'): strlen($NVPString);
        $valval = substr($NVPString,$keypos+1,$valuepos-$keypos-1);
        $proArray[$keyval] = urldecode($valval);
        $NVPString = substr($NVPString,$valuepos+1,strlen($NVPString));
    }
    return $proArray;
}

function payWithCreditCard($data){
    global $wpdb;
    if(is_array($data)){
        $request_params = array(
            'METHOD' => 'DoDirectPayment', 
            'USER' => PAYPAL_API_USERNAME, 
            'PWD' => PAYPAL_API_PASSWORD, 
            'SIGNATURE' => PAYPAL_API_SIGNATURE, 
            'VERSION' => PAYPAL_API_VERSION, 
            'PAYMENTACTION' => 'Sale',                   
            'IPADDRESS' => $_SERVER['REMOTE_ADDR'],
            'CREDITCARDTYPE' => $data['CREDITCARDTYPE'], 
            'ACCT' => $data['ACCT'],                        
            'EXPDATE' => $data['EXPDATE'],           
            'CVV2' => $data['CVV2'], 
            'FIRSTNAME' => $data['FIRSTNAME'], 
            'LASTNAME' => $data['LASTNAME'], 
            'STREET' => $data['STREET'], 
            'CITY' => $data['CITY'], 
            'STATE' => $data['STATE'],                     
            'COUNTRYCODE' => $data['COUNTRYCODE'], 
            'ZIP' => $data['ZIP'],
            'AMT' => $data['AMT'], 
            'CURRENCYCODE' => 'AUD', 
            'DESC' => 'Testing Payments Pro'
        );
        
        $nvp_string = '';
        foreach($request_params as $var=>$val){
            $nvp_string .= '&'.$var.'='.urlencode($val);    
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, PAYPAL_API_ENDPOINT);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $nvp_string);

        $result = curl_exec($curl);     
        curl_close($curl);
        
        $nvp_response_array = parse_str($result);
        $response = NVPToArray($result);
        echo '<pre>';
        print_r($response);
    }
    
}

function payWithCreditCardPayflow($data){
    global $wpdb;
    if(is_array($data)){
        $request_params = array(
            'HOSTPORT' => 443, 
            'USER' => PAYFLOW_API_USER, 
            'VENDOR' => PAYFLOW_API_VENDOR, 
            'PARTNER' => PAYFLOW_API_PARTNER, 
            'PWD' => PAYFLOW_API_PASSWORD, 
            'TENDER' => 'C',                   
            'TRXTYPE' => 'S',
//            'CREDITCARDTYPE' => $data['CREDITCARDTYPE'], 
            'ACCT' => $data['ACCT'],                        
            'EXPDATE' => $data['EXPDATE'],           
            'CVV2' => $data['CVV2'], 
            'AMT' => $data['AMT'], 
            'CURRENCY' => 'AUD', 
        );
        
        $nvp_string = '';
        foreach($request_params as $var=>$val){
            $nvp_string .= '&'.$var.'='.urlencode($val);    
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, PAYFLOW_API_ENDPOINT);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $nvp_string);

        $result = curl_exec($curl);     
        curl_close($curl);
        
        $nvp_response_array = parse_str($result);
        $response = NVPToArray($result);
//        echo '<pre>';
//        print_r($response);
        return $response;
    }
    
}