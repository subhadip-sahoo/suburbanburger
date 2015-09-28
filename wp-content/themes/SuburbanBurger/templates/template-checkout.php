<?php
/* Template Name: Checkout */
session_start();
//echo '<pre>';
//print_r($_SESSION);
global $user_ID, $wp_query;
if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])) :
    wp_safe_redirect(href(BOOK_NOW));
    exit();
endif;
$err_msg = '';
$war_msg = '';
$suc_msg = '';
if(isset($_POST['pickup_time'])){
    $_SESSION['pickup_time'] = $_POST['pickup_time'];
}else if(!isset($_SESSION['pickup_time'])){
    $_SESSION['pickup_time'] = 'ASAP please!';
}
if(isset($_POST['rege_no'])){
    $_SESSION['rege_no'] = $_POST['rege_no'];
}else if(!isset($_SESSION['rege_no'])){
    $_SESSION['rege_no'] = '';
}
if(isset($_POST['cus_note'])){
    $_SESSION['cus_note'] = $_POST['cus_note'];
}else if(!isset($_SESSION['cus_note'])){
    $_SESSION['cus_note'] = '';
}
$cart_details = print_cart();
if($_POST['action'] == 'payWithCreditCard'){
    $data = array(
        'ACCT' => esc_attr($_POST['card_number']),
        'EXPDATE' => esc_attr($_POST['exp_month']).esc_attr($_POST['exp_year']),
        'CVV2' => esc_attr($_POST['cvv2']),
        'FIRSTNAME' => esc_attr($_POST['fname']),
        'LASTNAME' => esc_attr($_POST['lname']),
        'AMT' => esc_attr($cart_details['total'])
    );
    $response = payWithCreditCardPayflow($data);
    if($response['RESULT'] == 0){
        $_SESSION['payflow'] = array(
            'PNREF' => $response['PNREF'],
            'RESPMSG' => $response['RESPMSG'],
            'AUTHCODE' => $response['AUTHCODE'],
            'PPREF' => $response['PPREF'],
            'CORRELATIONID' => $response['CORRELATIONID'],
            'AMT' => esc_attr($cart_details['total']),
        );
        $tbl_order = $wpdb->prefix.'order_details';
        $tbl_payment = $wpdb->prefix.'orders';
        $tbl_cart = $wpdb->prefix.'cart';
        $user_id = $user_ID;
        $user_email = get_the_author_meta('user_email', $user_id);
        $display_name = get_the_author_meta('display_name', $user_id);
        $pickup_time = $_SESSION['pickup_time'];
        $rege_no = $_SESSION['rege_no'];
        $cus_note = $_SESSION['cus_note'];
        $session_id = session_id();
        $query = "SELECT * FROM $tbl_cart WHERE id_cart = (SELECT MAX(id_cart) FROM $tbl_cart WHERE id_user = $user_id AND id_session = '$session_id')";
        $result = $wpdb->get_row($query);
        
        $msg = '';
        if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])){
            $wpdb->update($tbl_cart, array('is_order' => 1), array('id_cart' => $result->id_cart));
            $order_query = "SELECT IFNULL(MAX(id),0)+1 AS id FROM $tbl_payment";
            $order_result = $wpdb->get_row($order_query);
            $cus_order_id = 'SUB-C'.$user_id.'-O'.$order_result->id;
            $data_orders = array(
                'id_user' => $user_id,
                'cus_order_id' => $cus_order_id,
                'transaction_id' => $response['PNREF'],
                'transaction_amount' => esc_attr($cart_details['total']),
                'currency' => 'AUD',
                'payment_date' => date(DATETIME_DATABASE_FORMAT),
            );
            $wpdb->insert($tbl_payment, $data_orders);
            
            $customer = get_userdata($user_ID);
            $msg .= '<h2>Customer Details</h2>';
            $msg .= "<p>Customer Name: ".$customer->display_name."</p>";
            $msg .= "<p>Customer Email Address: ".$customer->user_email."</p>";
            $msg .= "<p>Customer phone number: ".get_user_meta($user_ID, 'contact_number', TRUE)."</p><br/>";
            $msg .= '<h2>Payment Details</h2>';
            $msg .= "<p>PNREF: ".$response['PNREF']."</p>";
            $msg .= "<p>Transaction amount: ".esc_attr($cart_details['total'])." AUD</p>";
            $msg .= "<p>Payment Date: ".date(DATETIME_DATABASE_FORMAT)."</p><br/>";
            $msg .= '<h2>Order Details</h2>';  
            $msg .= "<p>Pickup Time: <strong>$pickup_time</strong></p>";
            $msg .= "<p>Rege. No: <strong>$rege_no</strong></p>";
            $msg .= "<p>Note: <strong>$cus_note</strong></p><br/>";
            $msg .= "<table style='border: 1px solid #ccc; padding: 10px; text-align: center;'>";
            $msg .= "<tr>";
            $msg .= "<th style='border: 1px solid #ccc; padding: 10px; text-align: center;'>Item Name</th>";
            $msg .= "<th style='border: 1px solid #ccc; padding: 10px; text-align: center;'>Quantity</th>";
            $msg .= "<th style='border: 1px solid #ccc; padding: 10px; text-align: center;'>Price (AUD)</th>";
            $msg .= "</tr>";
            
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
                $msg .= '<tr>';
                $msg .= '<td style="border: 1px solid #ccc; padding: 10px; text-align: center;">'.$item->post_title;

                if(is_array($attribute) && !empty($attribute)):
                    $msg .= '<ul class="specialDescLine '.$linked_prod_attr_class.'">';
                    $msg .= '<li>'.$attribute['attribute_name'].'</li>';
                    $msg .= '</ul>';
                endif;
                if(get_field('product_type', $id, TRUE) == 'combo'){
                    $msg .= '<ul class="combo">';
                    $msg .= '<li>Your Chioce : '.$_SESSION['combo']['your_choice_value'].'</li>';
                    $msg .= '<li>Chips : '.$_SESSION['combo']['chips_value'].'</li>';
                    $msg .= '<li>'.$_SESSION['combo']['only_one'].' : '.$_SESSION['combo']['only_one_value'].'</li>';
                    $msg .= '</ul>';
                }
                if(isset($_SESSION['burger_note'])){
                    if(isset($_SESSION['burger_note'][$id])){
                        $msg .= '<ul class="specialDescLine"><li>'.$_SESSION['burger_note'][$id].'</li></ul>';
                    }
                }
                $msg .= '</td>';
                $msg .= '<td style="border: 1px solid #ccc; padding: 10px; text-align: center;">'. $qty . '</td>';
                $msg .= '<td style="border: 1px solid #ccc; padding: 10px; text-align: right;">$'. number_format($price, 2, '.', '') . '</td>';
                $msg .= '</tr>';
                
            }
            $msg .= '<tr>';
            $msg .= '<td style="border: 1px solid #ccc; padding: 10px; text-align: right;" colspan="2">Subtotal</td>';
            $msg .= '<td style="border: 1px solid #ccc; padding: 10px; text-align: rigth;">$'. esc_attr($cart_details['total']) . ' AUD</td>';
            $msg .= '</tr>';
            $msg .= '<tr>';
            $msg .= '<td style="border: 1px solid #ccc; padding: 10px; text-align: right;" colspan="2">Total</td>';
            $msg .= '<td style="border: 1px solid #ccc; padding: 10px; text-align: rigth;">$'. esc_attr($cart_details['total']) . ' AUD</td>';
            $msg .= '</tr>';
            $msg .= "</table>";            
            
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
                $msg_user .= "<p>Thank you for your order. Your payment and order details are as follows.</p><br/>";
                $msg_user .= $msg;
                $msg_user .= "<br/>";
                $msg_user .= "Best Regards<br/>$from_name Team";

                wp_mail($user_email, $subject, $msg_user, $headers);
            }
            
            $success = TRUE;
        }
    }else{
        $err_msg = '<p>' . $response['RESPMSG'] . '</p>';
    }
}
get_header();
if($_POST['action'] == 'payWithCreditCard'){
    if(isset($success) && $success){
        unset($_POST);
?>
    <script type="text/javascript">
        jQuery(function(){
            HoldOn.open({
                theme:'sk-circle',
                message:"<p>Please be patient. You are being redirected...</p>"
            });
            setTimeout(function(){
                HoldOn.close();
                window.location.href = '<?php echo site_url('thank-you'); ?>';
            },13000);
        });
    </script>
<?php
    }
?>
<script type="text/javascript">
    jQuery(function(){
        jQuery('#pay_with_paypal').hide();
        jQuery('#checkout_label').hide();
        jQuery('#paypal_pro').show();
        jQuery('#paypal_pro_checkbox').prop('checked', true);
    });
</script>
<?php
} 
?>
<div class="inner_page">
  <div class="container-fluid inner_banner">
      <div class="row"> <img src="<?php echo get_template_directory_uri(); ?>/images/inner_banner.png" alt="" title="" class="img-responsive"/> </div>
  </div>
  <!--Banner_End-->
  <div class="container-fluid inner_section_bg">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
            <h1 class="inner_page_title"><?php the_title(); ?></h1>
        </div>
      </div>
    </div>
  </div>
</div>
<!--Section_one_End-->
<div class="container-fluid menupage_footer">
    <div class="container">
      <div class="row product_page">
        <div class="col-md-6">
          <h3>PAYMENT METHOD</h3>
            <div class="skip_well">
                <div class="row">
                    <div class="col-md-12">
                        <input type="radio" name="select_payment" value="paypal_standered" class="pull-left" checked/>
                       <span class="paypal_icon">paypal</span>
                        <input type="radio" name="select_payment" value="paypal_pro" id="paypal_pro_checkbox" /><span class="credit_card_icon">Pay with credit card</span>
                    </div>
                </div>
                <br>
                <p class="text-danger" id="checkout_label"><small>After clicking "Place Order" you will be taken to PayPal to complete payment.</small></p>
                <div class="row papal_pay">
                    <div class="col-md-4 col-md-offset-4">
                        <p class="pull-right"><strong>Total :&nbsp;<span class="text-success">$<?php echo $cart_details['total']; ?>&nbsp;AUD</span></strong></p>
                    </div>
                    <div class="col-md-4">
                        <?php if(!$user_ID) : ?>
                        <button class="btn btn-danger defalt_button pull-right" type="button" data-toggle="modal" data-target="#login-modal">Login to Pay</button>
                        <?php else: ?>
                        <?php $action = (get_field('paypal_environment', 'option') == 'sandbox')?'https://www.sandbox.paypal.com/cgi-bin/webscr':'https://www.paypal.com/cgi-bin/webscr'; ?>
                        <?php
                            $custom = array(
                                'user_id' => $user_ID,
                                'pickup_time' => $_SESSION['pickup_time'],
                                'rege_no' => $_SESSION['rege_no'],
                                'cus_note' => $_SESSION['cus_note'],
                                'session_id' => session_id()
                            );
                            $custom = serialize($custom);
                            $custom = base64_encode($custom);
                        ?>
                        <form action="<?php echo $action;?>" method="POST" name="pay_with_paypal" id="pay_with_paypal">
                            <input type="hidden" name="business" value="<?php echo get_field('paypal_email', 'option');?>">
                            <input type="hidden" name="cmd" value="_cart">
                            <input type="hidden" name="upload" value="1">
                            <input type="hidden" name="return" value="<?php echo href(BOOK_NOW);?>">
                            <input type="hidden" name="cancel_return" value="<?php echo href(BOOK_NOW);?>">
                            <input type="hidden" name="currency_code" value="AUD">
                            <input type="hidden" name="notify_url" value="<?php echo get_template_directory_uri();?>/paypal/ipn_listner.php">
                            <?php echo paypal_cart_items(); ?>
                            <input type="hidden" name="custom" value="<?php echo $custom;?>">
                            <button class="btn btn-success defalt_button pull-right" type="submit" id="place_order">Place Order</button>
                        </form>
                      <?php endif; ?>
                    </div>
                </div>
                <?php if($user_ID):?>
                <?php if(!empty($err_msg)) : ?>
                <div role="alert" class="alert alert-danger alert-dismissible">
                    <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span></button>
                    <?php echo $err_msg; ?>
                </div>
                <?php endif; ?>
                <form name="paypal_pro" id="paypal_pro" action="" method="POST" data-toggle="validator" role="form" style="display: none;">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-3">
                                <label>Name: </label>
                            </div>
                            <div class="col-xs-4">
                                <input type="text" name="fname" id="fname" placeholder="First name" value="<?php echo (isset($_POST['fname'])) ? $_POST['fname'] : ''; ?>" class="form-control" required data-error="This is required!" />
                            </div>
                            <div class="col-xs-4">
                                <input type="text" name="lname" id="lname" placeholder="Last name" value="<?php echo (isset($_POST['lname'])) ? $_POST['lname'] : ''; ?>" class="form-control" required data-error="This is required!" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-3">
                                <label>Card Number: </label>
                            </div>
                            <div class="col-xs-6">
                                <input type="text" name="card_number" id="card_number" value="" class="form-control" required data-error="This is required!"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-3">
                                <label>Expiry Date:</label>
                            </div>
                            <div class="col-xs-3">
                                <select name="exp_month" id="exp_month" class="form-control" required>
                                    <option value="">--Select--</option>
                                    <?php for($i = 1; $i <= 12; $i++): ?>
                                    <?php $i = ($i < 10) ? '0'.$i : $i; ?>
                                    <option value="<?php echo $i; ?>" <?php echo (isset($_POST['exp_month']) && $_POST['exp_month'] == $i) ? 'selected="selected"' : ''; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-xs-4">
                                <select name="exp_year" id="exp_year" class="form-control" required>
                                    <option value="">--Select--</option>
                                    <?php for($i = 2015; $i <= 2070; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo (isset($_POST['exp_year']) && $_POST['exp_year'] == $i) ? 'selected="selected"' : ''; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-3">
                                <label>CVV :</label>
                            </div>
                            <div class="col-xs-3">
                                <input type="text" name="cvv2" id="cvv2" value="<?php echo (isset($_POST['cvv2'])) ? $_POST['cvv2'] : ''; ?>" required class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="action" id="action" value="payWithCreditCard" />
                        <button class="btn btn-success defalt_button" type="submit" id="place_order_with_credit_card">Place Order</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-5 col-md-offset-1">
            <div>
                <h3>Your order   <div class="for_pickup text-center pull-right">For Pickup</div></h3>
                <div class="skip_well">
                    <form name="cart" action="<?php echo href(CHECKOUT); ?>" method="POST">
                      
                        <div class="form-group">
                            <label for="Deliveryinput">Requested for:</label>
                            <select name="pickup_time" class="form-control select-requested">
                                <?php echo get_order_timing_display(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="registration_no">Car Rego. No:</label>
                            <input type="text" name="rege_no" id="rege_no" value="<?php echo (isset($_SESSION['rege_no'])) ? $_SESSION['rege_no'] : ''; ?>" class="form-control" autocomplete="off"/>
                        </div>
                        <div class="form-group cart-products">
                        <?php 
                            $cart_details = print_cart(); 
                            echo $cart_details['html'];
                        ?>
                        </div>
                        <div class="row ordered-item">
                            <div class="col-md-9 order_left">Sub Total</div>
                            <div class="col-md-3 order_right"><strong><span class="sub_total">$<?php echo $cart_details['sub_total']; ?></span></strong></div>         
                        </div>
<!--                        <div class="row deliver-item">
                            <div class="col-md-9 order_left">Delivery Fee</div>
                            <div class="col-md-3 order_right"><span class="delivery_fee">$0.00</span></div>         
                        </div>-->

                        <div class="row deliver-total">
                            <div class="col-md-9 order_left text-danger"><strong>Total</strong></div>
                            <div class="col-md-3 order_right"><strong><span class="total">$<?php echo $cart_details['total']; ?></span></strong></div>         
                        </div>
                        <?php if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])) : ?>
                        <p class="text-center text-danger item_add cart-alert-msg">Please add at least 1 item to your order!</p>
                        <?php $disabled = true; ?>
                        <?php endif; ?>
                        <div class="form-group">
                            <label>Note:</label>
                            <textarea name="cus_note" class="form-control" id="cus_note"><?php echo (isset($_SESSION['cus_note'])) ? $_SESSION['cus_note'] : ''; ?></textarea>
                        </div>
                        <?php if(!is_today_closed()):?>
                        <div class="form-group">
                            <button class="btn btn-success defalt_pickup text-center proceed-to-checkout" type="submit" <?php echo (isset($disabled) && $disabled) ? 'disabled' : ''; ?>>CONTINUE</button>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
<?php get_footer(); ?>


