<?php
    function func_cart($arr_ipn){
        global $wpdb;
        $tbl_order = $wpdb->prefix.'order_details';
        $tbl_payment = $wpdb->prefix.'orders';
        $tbl_cart = $wpdb->prefix.'cart';
        $custom = base64_decode($arr_ipn['custom']);
        $custom = unserialize($custom);
        $user_id = $custom['user_id'];
        $user_email = get_the_author_meta('user_email', $user_id);
        $display_name = get_the_author_meta('display_name', $user_id);
        $pickup_time = $custom['pickup_time'];
        $rege_no = $custom['rege_no'];
        $cus_note = $custom['cus_note'];
        $session_id = $custom['session_id'];
        $query = "SELECT * FROM $tbl_cart WHERE id_cart = (SELECT MAX(id_cart) FROM $tbl_cart WHERE id_user = $user_id AND id_session = '$session_id')";
        $result = $wpdb->get_row($query);
        $msg = '';
        if($arr_ipn['mc_gross'] == $result->price){
            // do the stuff here
            $wpdb->update($tbl_cart, array('is_order' => 1), array('id_cart' => $result->id_cart));
            $order_query = "SELECT IFNULL(MAX(id),0)+1 AS id FROM $tbl_payment";
            $order_result = $wpdb->get_row($order_query);
            $cus_order_id = 'SUB-C'.$user_id.'-O'.$order_result->id;
            $data_orders = array(
                'id_user' => $user_id,
                'cus_order_id' => $cus_order_id,
                'transaction_id' => $arr_ipn['txn_id'],
                'transaction_amount' => $arr_ipn['mc_gross'],
                'currency' => $arr_ipn['mc_currency'],
                'payment_date' => date(DATETIME_DATABASE_FORMAT),
            );
            
            /* Start message */
            $customer = get_userdata($user_id);
            $msg .= '<h2>Customer Details</h2>';
            $msg .= "<p>Customer Name: ".$customer->display_name."</p>";
            $msg .= "<p>Customer Email Address: ".$customer->user_email."</p>";
            $msg .= "<p>Customer phone number: ".get_user_meta($user_id, 'contact_number', TRUE)."</p><br/>";
            $msg .= '<h2>Payment Details</h2>';
            $msg .= "<p>Transaction ID: ".$arr_ipn['txn_id']."</p>";
            $msg .= "<p>Transaction amount: ".$arr_ipn['mc_gross']." ".$arr_ipn['mc_currency']."</p>";
            $msg .= "<p>Payment Date: ".$arr_ipn['payment_date']."</p>";
            $msg .= "<h2>Order Details</h2>";
            $msg .= "<p>Pickup Time: <storng>$pickup_time</storng></p>";
            $msg .= "<p>Rege. No: <storng>$rege_no</storng></p>";
            $msg .= "<p>Note: <storng>$cus_note</storng></p><br/>";
            $msg .= "<table style='border: 1px solid #ccc; padding: 10px; text-align: center;'>";
            $msg .= "<tr>";
            $msg .= "<th style='border: 1px solid #ccc; padding: 10px; text-align: center;'>Item Name</th>";
            $msg .= "<th style='border: 1px solid #ccc; padding: 10px; text-align: center;'>Quantity</th>";
            $msg .= "<th style='border: 1px solid #ccc; padding: 10px; text-align: center;'>Price (AUD)</th>";
            $msg .= "</tr>";
            $wpdb->insert($tbl_payment, $data_orders);
            $item_count = $result->item_count;
            for($i = 1; $i <= $item_count; $i++){
                $item_id = explode('/', $arr_ipn['item_number'.$i]);
                $data_order_details = array(
                    'item_id' => $item_id[0],
                    'item_attribute_id' => $item_id[1],
                    'quantity' => $arr_ipn['quantity'.$i],
                    'cus_order_id' => $cus_order_id
                );
                $wpdb->insert($tbl_order, $data_order_details);
                $msg .= "<tr>";
                $msg .= "<td style='border: 1px solid #ccc; padding: 10px; text-align: center;'>".$arr_ipn['item_name'.$i];
                if(isset($arr_ipn['option_name1_'.$i])){
                    $msg .= '<ul class="combo">';
                    for($j = 1; $j <= 3; $j++){
                        if(!isset($arr_ipn['option_name'.$j.'_'.$i])){
                            continue;
                        }
                        $msg .= '<li>'.$arr_ipn['option_name'.$j.'_'.$i].' : '.$arr_ipn['option_selection'.$j.'_'.$i].'</li>';
                    }
                    $msg .= '</ul>';
                }
                $j = 1;
                $msg .= "</td>";
                $msg .= "<td style='border: 1px solid #ccc; padding: 10px; text-align: center;'>".$arr_ipn['quantity'.$i].'</td>';
                $msg .= '<td style="border: 1px solid #ccc; padding: 10px; text-align: right;">$'. $arr_ipn['mc_gross_'.$i] . '</td>';
                $msg .= "</tr>";
            } 
            $msg .= '<tr>';
            $msg .= '<td style="border: 1px solid #ccc; padding: 10px; text-align: right;" colspan="2">Subtotal</td>';
            $msg .= '<td style="border: 1px solid #ccc; padding: 10px; text-align: rigth;">$'. $arr_ipn['mc_gross'] . ' AUD</td>';
            $msg .= '</tr>';
            $msg .= '<tr>';
            $msg .= '<td style="border: 1px solid #ccc; padding: 10px; text-align: right;" colspan="2">Total</td>';
            $msg .= '<td style="border: 1px solid #ccc; padding: 10px; text-align: rigth;">$'. $arr_ipn['mc_gross'] . ' AUD</td>';
            $msg .= '</tr>';
            $msg .= "</table>";
            /* End message */
            
            $to = (get_field('online_order_email_address', 'option') <> '') ? get_field('online_order_email_address', 'option') : get_option('admin_email');
            $from = $user_email;
            $from_name = $display_name;
            $headers = "From: $from_name <$from>\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $subject = "{$display_name} has placed an order from Sub Urban Burger.";
            $msg_admin = "Please find the payment and order details below.<br/><br/>";
            $msg_admin .= $msg;
            
            if(wp_mail($to, $subject, $msg_admin, $headers)){
                $from = get_option('admin_email');
                $from_name = get_option('blogname');
                $headers = "From: $from_name <$from>\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                $subject = "Your order has been successfully placed.";
                $msg_user = "Hi {$display_name},<br/><br/>";
                $msg_user .= "Thank you for your order. Your payment and order details are as follows.<br/><br/>";
                $msg_user .= $msg;
                $msg_user .= "<br/>";
                $msg_user .= "Best Regards<br/>$from_name Team";

                wp_mail($user_email, $subject, $msg_user, $headers);
            }
        }else{
            $from = get_option('admin_email');
            $from_name = get_option('blogname');
            $headers = "From: $from_name <$from>\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $subject = "Your order has not been placed.";
            $msg = "Hi {$display_name},<br/><br/>";
            $msg .= "Your payment is not successfully completed. This seems to be total amount and transction amount are not same.<br/>";
            $msg .= "Your payment details are as follows.<br/>";
            $msg .= "<br/>";
            $msg .= "Transaction ID: ".$arr_ipn['txn_id']."<br/>";
            $msg .= "Transaction amount: ".$arr_ipn['mc_gross']." ".$arr_ipn['mc_currency']."<br/>";
            $msg .= "Payment Date: ".$arr_ipn['payment_date']."<br/>";
            $msg .= "If you have any query please contact to administrator.<br/><br/>";
            $msg .= "Best Regards<br/>$from_name Team";
            wp_mail( $user_email, $subject, $msg, $headers );
        }
    }
    
    function func_web_accept($arr_ipn){
        global $wpdb;
        $user_id = $arr_ipn['custom'];
        $product_id = $arr_ipn['item_number'];
        $product_value = str_replace('$', '', get_field('package_price', $product_id, TRUE));
        $results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = '".$user_id."'");
        if($wpdb->num_rows == 1){
            foreach ($results as $result) {
                $user_email = $result->user_email;
                $user_display_name = $result->display_name;
            }
        }
        if($arr_ipn['mc_gross'] == $product_value){
            update_user_meta($user_id,'is_paid_user', 1);
            $data_arr = array(
                'id_user' => $user_id,
                'id_package' => $product_id,
                'payment_date' => date('Y-m-d H:i:s')
            );
            $wpdb->insert('wp_user_payment_paypal', $data_arr);
            //******  A mail has been thrown after executing this code ************** //
            $from = get_option('admin_email');
            $from_name = get_option('blogname');
            $headers = "From: $from_name <$from>\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $subject = "Your payment has been successful completed.";
            $msg = "Dear {$user_display_name}<br/><br/>";
            $msg .= "Thank you for making payment. Your payment details are as follows.<br/>";
            $msg .= "<br/>";
            $msg .= "Package Name: ".$arr_ipn['item_name']."<br/>";
            $msg .= "Transaction ID: ".$arr_ipn['txn_id']."<br/>Transaction amount: ".$arr_ipn['mc_gross']." ".$arr_ipn['mc_currency']."<br/>";
            $msg .= "Payment Date: ".$arr_ipn['payment_date']."<br/>";
            $msg .= "PayPal Email Address: ".$arr_ipn['payer_email']."<br/><br/>";
            $msg .= "Best Regards<br/>$from_name Team";
            wp_mail( $user_email, $subject, $msg, $headers );
            
            // *********** Mail to admin ************ //
            $to = get_option('admin_email');
            $from = $user_email;
            $from_name = $user_display_name;
            $headers = "From: $from_name <$from>\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $subject = $user_display_name. " has made payment for ".$arr_ipn['item_name'];
            $msg = "Please find the details below.<br/><br/>";
            $msg .= "Package Name: ".$arr_ipn['item_name']."<br/>";
            $msg .= "Transaction ID: ".$arr_ipn['txn_id']."<br/>Transaction amount: ".$arr_ipn['mc_gross']." ".$arr_ipn['mc_currency']."<br/>";
            $msg .= "Payment Date: ".$arr_ipn['payment_date']."<br/>";
            $msg .= "PayPal Email Address: ".$arr_ipn['payer_email']."<br/><br/>";
            
            wp_mail( $to, $subject, $msg, $headers );
            
        }else{
            $from = get_option('admin_email');
            $from_name = "Red Tuition";
            $headers = "From: $from_name <$from>\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $subject = "There is an errot regarding transaction";
            $msg = "Dear {$user_display_name}<br/><br/>";
            $msg .= "Your payment is not successfully completed. This seems to be item value and transction amount are not same.<br/>";
            $msg .= "Your payment details are as follows.<br/>";
            $msg .= "<br/>";
            $msg .= "Package Name: ".$arr_ipn['item_name']."<br/>";
            $msg .= "Transaction ID: ".$arr_ipn['txn_id']."<br/>Transaction amount: ".$arr_ipn['mc_gross']." ".$arr_ipn['mc_currency']."<br/>";
            $msg .= "Payment Date: ".$arr_ipn['payment_date']."<br/>";
            $msg .= "PayPal Email Address: ".$arr_ipn['payer_email']."<br/><br/>";
            $msg .= "If you have any query please contact to administrator.<br/><br/>";
            $msg .= "Best Regards<br/>$from_name Team";
            wp_mail( $user_email, $subject, $msg, $headers );
        }
    }
    
    function func_subscr_signup($arr_ipn){
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = '".$arr_ipn['custom']."'");
        if($wpdb->num_rows == 1){
            foreach ($results as $result) {
                $user_email = $result->user_email;
                $display_name = $result->display_name;
            }
        }
        //******  A mail has been thrown after executing this code ************** //
        $from = get_option('admin_email');
        $from_name = get_option('blogname');
        $headers = "From: $from_name <$from>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $subject = "Your subscription has been successful.";
        $msg = "Dear $display_name,<br/><br/>";
        $msg .= "Thank you for subscription.<br/>Your subscription details are as follows<br/>";
        $msg .= "PayPal Email Address: ".$arr_ipn['payer_email']."<br/>";
        $msg .= "Subscription ID: ".$arr_ipn['subscr_id']."<br/>";
        $msg .= "Subscription Date: ".$arr_ipn['subscr_date']."<br/>"; // date('jS M, Y h:i a')
        $msg .= "You will receive another email regarding payment status shortly.<br/><br/>";
        $msg .= "Best regards,<br/>$from_name admin";
        
        wp_mail( $user_email, $subject, $msg, $headers );
    }

    function func_subscr_payment($arr_ipn){
        global $wpdb;
        $data_arr = array(
            'id_user' => $arr_ipn['custom'],
            'id_package' => $arr_ipn['item_number'],
            'transaction_id' => $arr_ipn['txn_id'],
            'transaction_amount' => $arr_ipn['mc_gross']." ".$arr_ipn['mc_currency'],
            'subscription_id' => $arr_ipn['subscr_id'],
            'paypal_email' => $arr_ipn['payer_email'],
            'payment_date' => date('Y-m-d H:i:s')
        );
        $wpdb->insert('wp_user_payment', $data_arr);
        $results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = '".$arr_ipn['custom']."'");
        if($wpdb->num_rows == 1){
            foreach ($results as $result) {
                $user_email = $result->user_email;
                $display_name = $result->display_name;
            }
        }
        // *********** Mail to admin ************ //
        $to = get_option('admin_email');
        $from = $user_email;
        $from_name = $display_name;
        $headers = "From: $from_name <$from>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $subject = $display_name. " has made payment for ".$arr_ipn['item_name'];
        $msg = "Please find the details below.<br/><br/>";
        $msg .= "Transaction ID: ".$arr_ipn['txn_id']."<br/>";
        $msg .= "Transaction amount: ".$arr_ipn['mc_gross']." ".$arr_ipn['mc_currency']."<br/>";
        $msg .= "Payment Date: ".date('jS M, Y h:i a')."<br/>";
        $msg .= "Subscription ID: ".$arr_ipn['subscr_id']."<br/>";
        $msg .= "PayPal Email Address: ".$arr_ipn['payer_email']."<br/><br/>";

        wp_mail( $to, $subject, $msg, $headers );
        
        if(get_field('price', $arr_ipn['item_number'], true) == $arr_ipn['mc_gross']){
            update_user_meta($new_user_id,'account_status', 1);
            
            if(strtotime(get_user_meta($arr_ipn['custom'], 'account_expiry', TRUE)) < strtotime('now')){
                update_user_meta($arr_ipn['custom'], 'account_expiry', date('Y-m-d H:i:s', strtotime('+1 day')));
            }
            else if(strtotime(get_user_meta($arr_ipn['custom'], 'account_expiry', TRUE)) >= strtotime('now')){
                $previous_expiry_date = get_user_meta($arr_ipn['custom'], 'account_expiry', TRUE);
                update_user_meta($arr_ipn['custom'], 'account_expiry', date('Y-m-d H:i:s', strtotime($previous_expiry_date.'+1 day')));
            }
            
            $userdata  = get_userdata( $arr_ipn['custom'] );
            if(implode(', ', $userdata->roles) != 'advertiser'){
                $role = new WP_User( $arr_ipn['custom'] );
                $role->add_role( 'advertiser' );
            }
            
            $from = get_option('admin_email');
            $from_name = get_option('blogname');
            $headers = "From: $from_name <$from>\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $subject = "Your payment has been successful completed.";
            $msg = "Dear $display_name,<br/><br/>";
            $msg .= "Thanks for your payment.";
            $msg .= "Your payment details are as follows<br/>";
            $msg .= "Transaction ID: ".$arr_ipn['txn_id']."<br/>";
            $msg .= "Transaction amount: ".$arr_ipn['mc_gross']." ".$arr_ipn['mc_currency']."<br/>";
            $msg .= "Payment Date: ".$arr_ipn['payment_date']."<br/>"; // date('jS M, Y h:i a')
            $msg .= "Subscription ID: ".$arr_ipn['subscr_id']."<br/>";
            $msg .= "PayPal Email Address: ".$arr_ipn['payer_email']."<br/><br/>";
            $msg .= "Best regards,<br/>$from_name admin";
            
            wp_mail( $user_email, $subject, $msg, $headers );
        }
    }

    function func_subscr_cancel($arr_ipn){
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = '".$arr_ipn['custom']."'");
        $userdata = get_userdata($arr_ipn['custom']);
        if($wpdb->num_rows == 1){
            foreach ($results as $result) {
                $user_email = $result->user_email;
                $display_name = $result->display_name;
            }
        }
        // *********** Mail to admin ************ //
        $to = get_option('admin_email');
        $from = $user_email;
        $from_name = $display_name;
        $headers = "From: $from_name <$from>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $subject = $display_name. " has canceled subscription for ".$arr_ipn['item_name'];
        $msg = "Please find the details below.<br/><br/>";
        $msg .= "Name: ".$userdata->first_name." ".$userdata->last_name."<br/>";
        $msg .= "Subscription ID: ".$arr_ipn['subscr_id']."<br/>";
        $msg .= "PayPal Email Address: ".$arr_ipn['payer_email']."<br/><br/>";
        
        wp_mail( $to, $subject, $msg, $headers );
        
        // ***************** Mail to user **************** //
        
        $from = get_option('admin_email');
        $from_name = get_option('blogname');
        $headers = "From: $from_name <$from>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $subject = "Your subscription has been successfully canceled for ".$arr_ipn['item_name'];
        $msg = "Dear $display_name,<br/><br/>";
        $msg .= "You have successfully canceled subscription for ".$arr_ipn['item_name']."<br/>";
        $msg .= "Please find the details below.<br/>";
        $msg .= "Subscription ID: ".$arr_ipn['subscr_id']."<br/>";
        $msg .= "Your membership account will expire on <strong>".date('jS M, Y', strtotime(get_user_meta($arr_ipn['custom'], 'account_expiry', TRUE)))."</strong><br/><br/>";
        $msg .= "If you want to subscribe again, please ";
        $msg .= "<a href='".site_url()."/make-payment/?auth=$userdata->ID&key=$userdata->user_activation_key'>click here</a><br/><br/>";
        $msg .= "Best regards,<br/>$from_name admin";
        
        wp_mail( $user_email, $subject, $msg, $headers  );
    }
    function func_subscr_failed($arr_ipn){
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = '".$arr_ipn['custom']."'");
        $userdata = get_userdata($arr_ipn['custom']);
        if($wpdb->num_rows == 1){
            foreach ($results as $result) {
                $user_email = $result->user_email;
                $display_name = $result->display_name;
            }
        }
        // *********** Mail to admin ************ //
        $to = get_option('admin_email');
        $from = $user_email;
        $from_name = $display_name;
        $headers = "From: $from_name <$from>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $subject = $display_name. " has tried to subscribe ".$arr_ipn['item_name'];
        $msg = "Please find the details below.<br/><br/>";
        $msg .= "Name: ".$userdata->first_name." ".$userdata->last_name."<br/>";
        $msg .= "PayPal Email Address: ".$arr_ipn['payer_email']."<br/><br/>";
        
        wp_mail( $to, $subject, $msg, $headers );
        
        // ***************** Mail to user **************** //
        
        $from = get_option('admin_email');
        $from_name = get_option('blogname');
        $headers = "From: $from_name <$from>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $subject = "Your subscription has not been successfull for ".$arr_ipn['item_name'];
        $msg = "Dear $display_name,<br/><br/>";
        $msg .= "Your subscription has not been successfull for ".$arr_ipn['item_name']."<br/>";
        $msg .= "Please find the details below.<br/>";
        $msg .= "Your membership account will expire on <strong>".date('jS M, Y', strtotime(get_user_meta($arr_ipn['custom'], 'account_expiry', TRUE)))."</strong><br/><br/>";
        $msg .= "If you want to subscribe again, please ";
        $msg .= "<a href='".site_url()."/make-payment/?auth=$userdata->ID&key=$userdata->user_activation_key'>click here</a><br/><br/>";
        $msg .= "Best regards,<br/>$from_name admin";
        
        wp_mail( $user_email, $subject, $msg, $headers );
    }
    function func_subscr_modify($arr_ipn){
        global $wpdb;
    }
    function func_subscr_eot($arr_ipn){
        global $wpdb;
    }
?>