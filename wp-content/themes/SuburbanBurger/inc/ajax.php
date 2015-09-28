<?php
add_action('wp_ajax_login_trigger', 'login_trigger');
add_action('wp_ajax_nopriv_login_trigger', 'login_trigger');
add_action('wp_ajax_registration_triggred', 'registration_triggred');
add_action('wp_ajax_nopriv_registration_triggred', 'registration_triggred');
add_action('wp_ajax_forgotPassword_trigerred', 'forgotPassword_trigerred');
add_action('wp_ajax_nopriv_forgotPassword_trigerred', 'forgotPassword_trigerred');
add_action('wp_ajax_resetPassword_trigerred', 'resetPassword_trigerred');
add_action('wp_ajax_nopriv_resetPassword_trigerred', 'resetPassword_trigerred');
add_action('wp_ajax_add_to_cart', 'add_to_cart');
add_action('wp_ajax_nopriv_add_to_cart', 'add_to_cart');
add_action('wp_ajax_update_cart', 'update_cart');
add_action('wp_ajax_nopriv_update_cart', 'update_cart');
add_action('wp_ajax_session_cart', 'session_cart');
add_action('wp_ajax_nopriv_session_cart', 'session_cart');
add_action('wp_ajax_get_order_timing', 'get_order_timing');
//add_action('wp_ajax_nopriv_get_order_timing', 'get_order_timing');

function login_trigger(){
    session_start();
    global $user_ID;
    $err_msg = '';
    $war_msg = '';
    $info_msg = '';
    $suc_msg = '';
    if(empty($_POST['user_login'])){
        $err_msg = 'Username is required.';
    }
    else if(empty($_POST['user_pass'])){
        $err_msg = 'Password is required.';
    }
    $remember = (isset($_POST['rememberme']) && $_POST['rememberme'] == 'on') ? TRUE : FALSE;
    if($err_msg == ''){
        $creds = array();
        $creds['user_login'] =  esc_sql($_POST['user_login']);
        $creds['user_password'] =  esc_sql($_POST['user_pass']);
        $creds['remember'] =  $remember;
        $user = wp_signon( $creds, FALSE);
        if ( is_wp_error($user) ) {
            if(isset($user->errors['invalid_username'])){
                $war_msg = "Invalid username. ";
            }else if(isset($user->errors['incorrect_password'])){
                $war_msg = "Incorrect password.";
            }
            else if(isset($user->errors['verification_failed'])){
                $war_msg = $user->errors['verification_failed'][0];
            }else if(isset($user->errors['account_expired'])){
                $war_msg = $user->errors['account_expired'][0];
            }else{
                $war_msg = 'Username / password does not match.';
            }
        }
        else {
            $suc_msg = 'Login successfull.';
        }
    }
    if(!empty($err_msg)){
        echo json_encode(array('type' => 'error', 'message' => $err_msg));
    }
    if(!empty($war_msg)){
        echo json_encode(array('type' => 'warning', 'message' => $war_msg));
    }
    if(!empty($suc_msg)){
        echo json_encode(array('type' => 'success', 'message' => $suc_msg));
    }
    die();
}

function registration_triggred(){
    $err_msg = '';
    $war_msg = '';
    $info_msg = '';
    $suc_msg = '';
    if(empty($_POST['first_name'])){
        $err_msg = 'First name is required.';
    }
    else if(empty($_POST['last_name'])){
        $err_msg = 'Last name is required.';
    }
    else if(empty($_POST['user_email'])){
        $err_msg = 'Email address is required.';
    }
    else if(!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL) === TRUE){
        $err_msg = 'Please enter a valid email address';
    }
    else if(empty($_POST['contact_number'])){
        $err_msg = 'Contact number is required.';
    }
    else if(!is_numeric($_POST['contact_number'])){
        $err_msg = 'Only digits are accepted.';
    }
    else if(empty($_POST['user_pass'])){
        $err_msg = 'Password is required.';
    }
    else if($_POST['user_pass'] <> $_POST['con_password']){
        $err_msg = 'Password does not match.';
    }
    if($err_msg == ''){
        $userinfo = array(
            'user_login' => esc_sql($_POST['first_name'].time()),
            'user_pass'  => esc_sql($_POST['user_pass']),
            'user_email' => esc_sql($_POST['user_email']),
            'display_name' => esc_sql($_POST['first_name'].' '.$_POST['last_name'])
        );
        $ID = wp_insert_user($userinfo);
        if ( is_wp_error($ID) ) {
            if(array_key_exists('existing_user_email', $ID->errors)){
                $war_msg = 'Sorry, email address already exists. Please try another one.';
            }else if(array_key_exists('existing_user_login', $ID->errors)){
                $war_msg = 'Sorry, username already exists. Please try another one.';
            }else{
                $war_msg = 'Sorry, username / email address already exists. Please try another one.';
            }
        }
        if($war_msg == ''){
            $userdata = array(
                'ID' => $ID,
                'user_pass' => esc_sql($_POST['user_pass']),
            );
            $new_user_id = wp_update_user( $userdata );
            if ( is_wp_error($new_user_id) ) {
                $err_msg = 'Registration failed. Please try again later.';
            }else{
                $userdata = get_userdata($new_user_id);
                $user_email = $userdata->user_email;
                $display_name = $userdata->display_name;
                $msg = '<p>Username: <strong>'.$userdata->user_email.'</strong></p>';
                $msg .= '<p>Password: <strong>'.esc_sql($_POST['user_pass']).'</strong></p><br/>';
                
                /* Mail to user */
                $from = get_option('admin_email');
                $from_name_blog = get_option('blogname');
                $headers = "From: $from_name <$from>\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                $subject = "Thank you for register in ".$from_name_blog;
                $msg_user = "Hi {$display_name},<br/><br/>";
                $msg_user .= "<p>Your ragistration has been successfully completed. Please find the credentials below.</p>";
                $msg_user .= $msg;
                $msg_user .= "Best Regards<br/>$from_name_blog Team";
                
                wp_mail($user_email, $subject, $msg_user, $headers);
                
                /* Mail to admin */
                $msg = '<p>Full Name: <strong>'.$userdata->display_name.'</strong></p>';
                $msg .= '<p>Email Address: <strong>'.$userdata->user_email.'</strong></p>';
                $msg .= '<p>Contact Number: <strong>'.get_user_meta($new_user_id, 'contact_number', TRUE).'</strong></p>';
                $to = get_option('admin_email') ;
                $from = $user_email;
                $from_name = $display_name;
                $headers = "From: $from_name <$from>\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                $subject = "New user registration".$from_name_blog;
                $msg_admin = "<p>Someone has register in ".$from_name_blog.". Please find the details below.</p>";
                $msg_admin .= $msg;
                
                wp_mail($to, $subject, $msg_admin, $headers);
                
                update_user_meta($new_user_id,'first_name', esc_sql($_POST['first_name']));
                update_user_meta($new_user_id,'last_name', esc_sql($_POST['last_name']));
                update_user_meta($new_user_id,'contact_number', esc_sql($_POST['contact_number']));
                
                $suc_msg = 'Your registration has been successfully completed';
            }
        }
    }
    if(!empty($err_msg)){
        echo json_encode(array('type' => 'error', 'message' => $err_msg));
    }
    if(!empty($war_msg)){
        echo json_encode(array('type' => 'warning', 'message' => $war_msg));
    }
    if(!empty($suc_msg)){
        echo json_encode(array('type' => 'success', 'message' => $suc_msg));
    }
    die();
}

function forgotPassword_trigerred(){
    session_start();
    $err_msg = '';
    $war_msg ='';
    $suc_msg = '';
    $info_msg = '';
    global $wpdb, $user_ID;
    $user_logs = esc_sql($_POST['user_log']);
    if(empty($user_logs)) { 
        $err_msg = "Email address should not be empty.";										
    }
    else if(filter_var($user_logs, FILTER_VALIDATE_EMAIL) === FALSE){
        $err_msg = "Please enter a valid email.";
    }
    if($err_msg == '') {
        $user_log = $wpdb->escape(trim($_POST['user_log']));					
        $user_data = get_user_by('email',$user_log);
        if(!$user_data) { 
            $err_msg ='Invalid Email Address';														
        }else if(in_array('administrator', $user_data->roles)){
            $war_msg = 'Restricted email address!!';
        }

        if($err_msg == '' && $war_msg == ''){													
            $_SESSION['um'] = $user_log;
            $info_msg = 'Please reset password.';							
        }							
    }
    if(!empty($err_msg)){
        echo json_encode(array('type' => 'error', 'message' => $err_msg));
    }
    if(!empty($war_msg)){
        echo json_encode(array('type' => 'warning', 'message' => $war_msg));
    }
    if(!empty($suc_msg)){
        echo json_encode(array('type' => 'success', 'message' => $suc_msg));
    }
    if(!empty($info_msg)){
        echo json_encode(array('type' => 'info', 'message' => $info_msg));
    }
    die();
}

function resetPassword_trigerred(){
    session_start();
    global $wpdb, $user_ID ;
    $err_msg = '';
    $war_msg = '';
    $suc_msg = '';
    $info_msg = '';
    $new_pass= esc_sql($_POST['new_pass']);
    $con_pass= esc_sql($_POST['con_pass']);
    if(empty($new_pass)) { 
        $err_msg .= "Please Enter New Password.<br/>";
    }
    else if($new_pass != $con_pass){
        $war_msg .= "Password does not match";
    }
    else if(!isset($_SESSION['um'])){
        $err_msg = 'You have recently changed your password. To reset again please go back to forgot password again.';
    }
    if($err_msg == '' && $war_msg == ''){
        $user = get_user_by('email', $_SESSION['um']);
        $update = wp_update_user( array ( 'ID' => $user->ID, 'user_pass' => $new_pass) ) ;
        unset($_SESSION['um']);
        if($update){
            $suc_msg = 'Your password has been successfully reset.';
        }
    }
    if(!empty($err_msg)){
        echo json_encode(array('type' => 'error', 'message' => $err_msg));
    }
    if(!empty($war_msg)){
        echo json_encode(array('type' => 'warning', 'message' => $war_msg));
    }
    if(!empty($suc_msg)){
        echo json_encode(array('type' => 'success', 'message' => $suc_msg));
    }
    if(!empty($info_msg)){
        echo json_encode(array('type' => 'info', 'message' => $info_msg));
    }
    die();
}

function print_cart(){
    //session_start();
    $html = '';
    $linked_product = FALSE;
    if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])){
        $sub_total = 0;
        foreach ($_SESSION['cart'] as $id => $qty) {
            $attribute = FALSE;
            $linked_prod_class = '';
            $linked_prod_attr_class = '';
            $prodid = explode('/', $id);
            if(get_field('product_type', $id, TRUE) == 'linked'){
                $linked_product = TRUE;
                $linked_prod_class = 'linked_product_'.$id;
            }
            if(get_field('product_type', $prodid[0], TRUE) == 'linked'){
                $linked_prod_attr_class = 'linked_product_attribute_'.$prodid[0];
            }
            $item = get_post($prodid[0]);
            $price = get_field('price', $prodid[0], TRUE);
            if(count($prodid) == 2){
                $attribute = get_product_attribute($prodid[0], $prodid[1]);
                $price = $attribute['price'];
            }
            $sub_total = $sub_total + ($price * $qty);
            $html .= '<div class="cart-items">';
            $html .= '<p>'.$item->post_title.'</p>';
            
            if(is_array($attribute) && !empty($attribute)):
                $html .= '<ul class="specialDescLine '.$linked_prod_attr_class.'">';
                $html .= '<li>'.$attribute['attribute_name'].'</li>';
                $html .= '</ul>';
            endif;
            if(get_field('product_type', $id, TRUE) == 'combo'){
                $html .= '<ul class="combo">';
                $html .= '<li>Your Chioce : '.$_SESSION['combo']['your_choice_value'].'</li>';
                $html .= '<li>Chips : '.$_SESSION['combo']['chips_value'].'</li>';
                $html .= '<li>'.$_SESSION['combo']['only_one'].' : '.$_SESSION['combo']['only_one_value'].'</li>';
                $html .= '</ul>';
            }
            if(isset($_SESSION['burger_note'])){
                if(isset($_SESSION['burger_note'][$id])){
                    $html .= '<ul class="specialDescLine"><li>'.$_SESSION['burger_note'][$id].'</li></ul>';
                }
            }
            $html .= '<input type="number" min="1" name="qty" class="qty" value="'.$qty.'" step="1" size="4"/>';
            $html .= "<span>$".number_format($price, 2, '.', '')."</span>";
            $html .= '<button type="button" name="update_cart" class="update_cart" data-id="'.$id.'">Update</button>';
            $html .= '<a href="javascript:void(0);" data-id="'.$id.'" class="remove_product '.$linked_prod_class.'">X</a>';
            $html .= '</div>';
        }
        $discount = 0;
        $tax = 0;
        $total = ($sub_total - $discount) + $tax;
        return array(
            'html' => $html, 
            'sub_total' => number_format($sub_total, 2, '.', ''), 
            'total' => number_format($total, 2, '.', ''),
            'product_count' => count($_SESSION['cart']),
            'is_linked_product_in_cart' => $linked_product
        );
    }else{
        $html .= '<div class="cart-items">';
        $html .= '<p>Cart is empty!</p>';
        $html .= '</div>';
    }
    return array('html' => $html, 'sub_total' => '0.00', 'total' => '0.00', 'product_count' => 0, 'is_linked_product_in_cart' => $linked_product);
}

function update_cart(){
    session_start();
    if(isset($_REQUEST['request']) && $_REQUEST['request'] == 'cancel'){
        if(isset($_SESSION['cart'])){
            $cart = $_SESSION['cart'];
            unset($cart[$_REQUEST['product_id']]);
            $_SESSION['cart'] = $cart;
        }
        if(isset($_SESSION['burger_note'])){
            if(isset($_SESSION['burger_note'][$_REQUEST['product_id']])){
                unset($_SESSION['burger_note'][$_REQUEST['product_id']]);
            }
        }
    }
    if(isset($_REQUEST['request']) && $_REQUEST['request'] == 'update'){
        if(isset($_SESSION['cart'])){
            $cart = $_SESSION['cart'];
            $cart[$_REQUEST['product_id']] = $_REQUEST['quantity'];
            $_SESSION['cart'] = $cart;
        }
    }
    echo json_encode(print_cart());
    die();
}

function add_to_cart(){
    session_start();
    if(isset($_SESSION['cart'])){
        $cart = $_SESSION['cart'];
    }else{
        $cart = array();
    }
    if(isset($cart[$_REQUEST['product_id']])){
        $quantity = $cart[$_REQUEST['product_id']] + $_REQUEST['quantity'];
    }else{
        $quantity = $_REQUEST['quantity'];
    }
    $cart[$_REQUEST['product_id']] = $quantity;
    $_SESSION['cart'] = $cart;
    if($_REQUEST['combo_data'] <> NULL){
        parse_str($_REQUEST['combo_data'], $dataArray);
        $_SESSION['combo'] = array(
            'your_choice_value' => $dataArray['your_choice_value'],
            'chips_value' => 'Chips',
            'only_one' => $dataArray['only_one'],
            'only_one_value' => $dataArray['only_one_value']
        );
    }
    if($_REQUEST['burger_note'] <> NULL){
        if(isset($_SESSION['burger_note'])){
            $burger_note = $_SESSION['burger_note'];
        }else{
            $burger_note = array();
        }
        $burger_note[$_REQUEST['product_id']] = $_REQUEST['burger_note'];
        $_SESSION['burger_note'] = $burger_note;
    }
    echo json_encode(print_cart());
    die();
}

function paypal_cart_items(){
    session_start();
    $html = '';
    if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])){
        $sub_total = 0;
        $count = 0;
        foreach ($_SESSION['cart'] as $id => $qty) {
            $count++;
            $attribute = FALSE;
            $prodid = explode('/', $id);
            $item = get_post($prodid[0]);
            $price = get_field('price', $prodid[0], TRUE);
            if(count($prodid) == 2){
                $attribute = get_product_attribute($prodid[0], $prodid[1]);
                $price = $attribute['price'];
            }
            $sub_total = $sub_total + ($price * $qty);
            
            if(is_array($attribute) && !empty($attribute)){
                if($attribute['product_type'] == 'variable'){
                    $html .= '<input type="hidden" name="item_name_'.$count.'" value="'.$item->post_title.' :: '.$attribute['attribute_name'].'">';
                }else if($attribute['product_type'] == 'linked'){
                    $html .= '<input type="hidden" name="item_name_'.$count.'" value="'.$item->post_title.' + '.$attribute['attribute_name'].'">';
                }
            }else{
                $html .= '<input type="hidden" name="item_name_'.$count.'" value="'.$item->post_title.'">';
                if(get_field('product_type', $id, TRUE) == 'combo'){
                    $html .= '<input type="hidden" name="on0_'.$count.'" value="Your Chioce">';
                    $html .= '<input type="hidden" name="os0_'.$count.'" value="'.$_SESSION['combo']['your_choice_value'].'">';
                    $html .= '<input type="hidden" name="on1_'.$count.'" value="Chips">';
                    $html .= '<input type="hidden" name="os1_'.$count.'" value="'.$_SESSION['combo']['chips_value'].'">';
                    $html .= '<input type="hidden" name="on2_'.$count.'" value="'.$_SESSION['combo']['only_one'].'">';
                    $html .= '<input type="hidden" name="os2_'.$count.'" value="'.$_SESSION['combo']['only_one_value'].'">';
                }
            }
            if(isset($_SESSION['burger_note'])){
                if(isset($_SESSION['burger_note'][$id])){
                    $html .= '<input type="hidden" name="on3_'.$count.'" value="Additional Note">';
                    $html .= '<input type="hidden" name="os3_'.$count.'" value="'.$_SESSION['burger_note'][$id].'">';
                }
            }
            $html .= '<input type="hidden" name="quantity_'.$count.'" value="'.$qty.'">';
            $html .= '<input type="hidden" name="amount_'.$count.'" value="'.$price.'">';
            $html .= '<input type="hidden" name="item_number_'.$count.'" value="'.$id.'">';
        }
    }
    return $html;
}

function session_cart(){
    session_start();
    global $wpdb, $user_ID;
    $tbl_cart = $wpdb->prefix.'cart';
    $cart_details = print_cart();
    $cart = array(
        'id_user' => $user_ID,
        'price' => $cart_details['total'],
        'item_count' => $cart_details['product_count'],
        'id_session' => session_id() 
    );
    $wpdb->insert($tbl_cart, $cart);
    echo 'success';
    die();
}

function get_product_attribute($pid, $aid){
    $product_type = get_field('product_type', $pid, TRUE);
    $attribute = array(
        'product_type' => $product_type
    );
    $variable_attribute = get_field('variable_attribute', $pid, TRUE);
    if(is_array($variable_attribute) && !empty($variable_attribute)) : 
        $count = 0;
        foreach ($variable_attribute as $va) :
            $count++;
            if($count == $aid){
                $attribute['attribute_name'] = $va['attribute_name'];
                $attribute['price'] = $va['price'];
                break;
            }
       endforeach;
    endif; 
    return $attribute;
}

function get_order_timing(){
    //session_start();
    $today = date('l');
    $order_place = get_field('order', 'option');
    $default_sel = (isset($_SESSION['pickup_time']) && $_SESSION['pickup_time'] == 'ASAP please!') ? 'selected="selected"' : '';
    $option = '<option value="ASAP please!" '.$default_sel.'>ASAP please!</option>';
    if(!empty($order_place)){
        foreach($order_place as $ord){
            if(in_array($today, $ord['week_day'])){                
                foreach ($ord['delivery_time'] as $ord_deli) {
                    $start_time = strtotime($ord_deli['start_time']);
                    $i = strtotime($ord_deli['start_time']);
                    $end_time = strtotime($ord_deli['end_time']);
                    $time_slot = $ord_deli['time_slot'];
                    $current_time = strtotime('now');
                    while($i <= $end_time){
                        $selected = (isset($_SESSION['pickup_time']) && $_SESSION['pickup_time'] == 'Around '.date('h:i A', $i)) ? 'selected="selected"' : '';
                        if($current_time > $i){
                            $i = strtotime('+'.$time_slot, $i);
                            continue;
                        }
                        $option .= "<option value='Around ".date('h:i A', $i)."' $selected>Around ".date('h:i A', $i)."</option>";
                        $i = strtotime('+'.$time_slot, $i);
                    }
                }
            }
        }
    }
    echo $option;
    die();
}

function get_order_timing_display(){
    //session_start();
    $today = date('l');
    $order_place = get_field('order', 'option');
    $default_sel = (isset($_SESSION['pickup_time']) && $_SESSION['pickup_time'] == 'ASAP please!') ? 'selected="selected"' : '';
    $option = '<option value="ASAP please!" '.$default_sel.'>ASAP please!</option>';
    if(!empty($order_place)){
        foreach($order_place as $ord){
            if(in_array($today, $ord['week_day'])){                
                foreach ($ord['delivery_time'] as $ord_deli) {
                    $start_time = strtotime($ord_deli['start_time']);
                    $i = strtotime($ord_deli['start_time']);
                    $end_time = strtotime($ord_deli['end_time']);
                    $time_slot = $ord_deli['time_slot'];
                    $current_time = strtotime('now');
                    while($i <= $end_time){
                        $selected = (isset($_SESSION['pickup_time']) && $_SESSION['pickup_time'] == 'Around '.date('h:i A', $i)) ? 'selected="selected"' : '';
                        if($current_time > $i){
                            $i = strtotime('+'.$time_slot, $i);
                            continue;
                        }
                        $option .= "<option value='Around ".date('h:i A', $i)."' $selected>Around ".date('h:i A', $i)."</option>";
                        $i = strtotime('+'.$time_slot, $i);
                    }
                }
            }
        }
    }
    echo $option;
}

function is_today_closed(){
    $today = date('l');
    $order_place = get_field('order', 'option');
    if(!empty($order_place)){
        foreach($order_place as $ord){
            if(in_array($today, $ord['week_day'])){                
                return FALSE;
            }else{
                return TRUE;
            }
        }
    }
    return TRUE;
}