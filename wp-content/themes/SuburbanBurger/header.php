<?php //session_start(); 
global $user_ID; cart_order_update();?>
<?php 
//echo '<pre>';
//print_r(test_time_slot());
//echo '</pre>'; 
//var_dump(strtotime('now'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('|', 'right', TRUE);?></title>
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/images/short_icon.png" type="images/png">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/ddscrollspydemo.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/HoldOn.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/bootstrap-theme.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/style.css">
    <?php wp_head(); ?>
</head>
<body onload="Captcha();" <?php body_class(); ?>>
    <header class="container-fluid header_bg">
        <div class="container">
            <div class="row">
                <div class="col-md-2">
                    <a href="<?php echo home_url(); ?>"><img src="<?php header_image(); ?>" alt="" title="logo" class="img-responsive logo"/></a>
                </div>
                <div class="col-md-10">
                    <ul class="list-inline pull-right login_nav">
                        <?php if($user_ID) : ?>
                        <?php $userdata = get_userdata($user_ID); ?>
                        <li><a href="<?php echo href(MY_ACCOUNT); ?>" title="My Account">Hi, <?php echo $userdata->first_name; ?></a></li>
                        <li><a href="<?php echo wp_logout_url(currentPageURL()); ?>">Logout</a></li>
                        <?php else: ?>
                        <li><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#login-modal">Login</a></li>
                        <li><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#regis-modal">Register</a></li>
                        <?php endif; ?>
                        <?php 
                            if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) :
                                $cart_product_count = count($_SESSION['cart']);
                            else:
                                $cart_product_count = 0;
                            endif;
                        ?>
                        <li><a href="#" id="my-cart" data-dismiss="modal" data-toggle="modal" data-target="#cart-modal">My Cart (<?php echo $cart_product_count; ?>)</a></li>
                    </ul>
                    <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">                        
                        <div class="modal-dialog">
                            <div class="loginmodal-container">
                                <button type="button" class="close close-ext" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h1>Login to Your Account</h1><br>
                                <div id="ajaxresponse"></div>
                                <form name="login_form" id="login_form" action="" method="POST">
                                    <input type="text" name="user_login" id="user_login" placeholder="Username">
                                    <input type="password" name="user_pass" id="user_pass" placeholder="Password">
                                    <input type="checkbox" name="rememberme" value="on"> Remember me
                                    <input type="hidden" name="action" id="action" value="login_trigger" />
                                    <input type="submit" name="login" class="login loginmodal-submit" value="Login">
                                </form>
                                <div class="login-help">
                                    <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#regis-modal">Register</a> - <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#fpwd-modal">Forgot Password</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="regis-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="loginmodal-container">
                                <button type="button" class="close close-ext" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h1>Please Register</h1>
                                <span>You will only need to register once</span>
                                <br>
                                <div id="ajaxresponsereg"></div>
                                <form name="reg_form" id="reg_form" action="" method="POST">
                                    <input type="text" name="first_name" id="first_name" placeholder="First Name">
                                    <input type="text" name="last_name" id="last_name" placeholder="Last Name">
                                    <input type="email" name="user_email" id="user_email" placeholder="Email Address">
                                    <input type="tel" name="contact_number" id="contact_number" placeholder="Contact Number">
                                    <input type="password" name="user_pass" id="user_pass" placeholder="Password">
                                    <input type="password" name="con_password" id="con_password" placeholder="Confirm Password">
                                    <input type="hidden" id="mainCaptcha" />
                                    <p id="DisplayCpatcha"></p>
                                    <input type="button" id="refresh" value="Refresh" onclick="Captcha();" />
                                    <input type="text" id="txtInput" placeholder="Security Code"/>
                                    <input type="hidden" name="action" id="action" value="registration_triggred" />
                                    <input type="submit" name="login" class="login loginmodal-submit" value="Register" onclick="return ValidCaptcha();">
                                </form>
                                <div class="login-help">
                                    <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#login-modal">Login</a> - <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#fpwd-modal">Forgot Password</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="fpwd-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="loginmodal-container">
                                <button type="button" class="close close-ext" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h1>Please enter your email</h1><br>
                                <div id="ajaxresponsefpwd"></div>
                                <form name="fpwd_form" id="fpwd_form" action="" method="POST">
                                    <input type="text" name="user_log" id="user_log" placeholder="Email Address">
                                    <input type="hidden" name="action" id="forgot" value="forgotPassword_trigerred" />
                                    <input type="submit" name="forgot_password" class="login loginmodal-submit" value="Submit">
                                </form>
                                <div class="login-help">
                                   <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#login-modal">Login</a> - <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#regis-modal">Register</a> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="rpwd-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="loginmodal-container">
                                <button type="button" class="close close-ext" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h1>Please enter new password</h1><br>
                                <div id="ajaxresponsereset"></div>
                                <form name="rpwd_form" id="rpwd_form" action="" method="POST">
                                    <input type="password" name="new_pass" id="new_pass" placeholder="New Password">
                                    <input type="password" name="con_pass" id="con_pass" placeholder="Confirm Password">
                                    <input type="hidden" name="action" id="reset_pass" value="resetPassword_trigerred" />
                                    <input type="submit" name="reset_password" class="login loginmodal-submit" value="Submit">
                                </form>
                                <div class="login-help">
                                   <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#login-modal">Login</a> - <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#regis-modal">Register</a> - <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#fpwd-modal">Forgot Password</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if(is_today_closed() && is_page(BOOK_NOW)):?>
                        <div class="modal fade" id="alertModalSub" tabindex="-1" role="dialog" aria-labelledby="SuburbanBurgerAlert!">
                            <div class="modal-dialog">
                                <div class="loginmodal-container">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Alert!</h4>
                                    </div>
                                    <div class="modal-body">
                                        <p>we are closed on Mondays!!!</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            jQuery(function(){
                                jQuery('#alertModalSub').modal();
                            });
                        </script>
                    <?php endif; ?>
                    <div class="modal fade" id="cart-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                                                    <div class="cart-item-box account_light_box">
                                 
                                <div class="skip_well your_order_box">
                               <h3>Your order <div class="for_pickup text-center pull-right">For Pickup</div></h3>
                                    <button type="button" class="close close_button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
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
<!--                                        <div class="row deliver-item">
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
                    <nav id="myNavbar" class="navbar navbar-default main_nav clearfix" role="navigation">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarCollapse">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        <div class="collapse navbar-collapse col-md-10 pull-right main_nav_cot" id="navbarCollapse">
                            <?php
                                $args = array(
                                    'theme_location'  => 'primary',
                                    'menu'            => '',
                                    'container'       => '',
                                    'container_class' => '',
                                    'container_id'    => '',
                                    'menu_class'      => 'menu',
                                    'menu_id'         => '',
                                    'echo'            => true,
                                    'fallback_cb'     => 'wp_page_menu',
                                    'before'          => '',
                                    'after'           => '',
                                    'link_before'     => '',
                                    'link_after'      => '',
                                    'items_wrap'      => '<ul id="menu-top-nav1" class="menu nav navbar-nav">%3$s</ul>',
                                    'depth'           => 0,
                                    'walker'          => new wp_bootstrap_navwalker(),
                                );
                                wp_nav_menu( $args );
                            ?>
                            <form id="custom-search-form" class="form-search form-horizontal pull-right" action="<?php echo home_url();?>" method="GET">
                                <div class="input-append span12">
                                    <input type="text" name="s" class="search-query mac-style" placeholder="Search" value="<?php echo get_search_query(); ?>">
                                    <button type="submit" class="btn"><i class="search_icon">search</i></button>
                                </div>
                            </form>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    

  <?php if(!is_page('book-now')) : ?>  
    <script type="text/javascript">
        jQuery(window).scroll(function () {
			if($( document ).width() >= 800){
				if (jQuery(window).scrollTop() > 5) {
					jQuery('.header_bg').css({top:'0px', position: 'fixed',width:'100%',padding:'1px'});
				}else{
					jQuery('.header_bg').removeAttr('style');
				}
			}
        });
    </script>
    <?php  endif; ?>
    
