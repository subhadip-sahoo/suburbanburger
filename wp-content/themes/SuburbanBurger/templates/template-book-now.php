<?php
/* Template Name: Book Now */
session_start();
//echo '<pre>';
//print_r($_SESSION['cart']);
get_header();
?>
<!--Banner_start-->
<div class="inner_page">
<!--  <div class="container-fluid inner_banner">
      <div class="row"> <img src="<?php echo get_template_directory_uri(); ?>/images/inner_banner.png" alt="" title="" class="img-responsive"/> </div>
  </div>
-->  <!--Banner_End-->
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
        <div class="col-md-2">
            <div id="item-menu">
                <h3>Skip to...</h3>
                <nav>
                    <ul class="nav tabs skip_nav" id="prod-cat">
                    <?php $categories = get_categories(array('post_type' => 'product', 'taxonomy' => 'item-cat', 'hide_empty' => 1, 'order' => 'ASC', 'orderby' => 'id')); ?>
                    <?php 
                        foreach($categories as $category) :  
                            $not_in_stock = get_field('not_in_stock', 'item-cat_'.$category->term_id);
                            if(!empty($not_in_stock) && $not_in_stock[0] == 1): 
                                continue;
                            endif;
                    ?>
                        <li><a href="#<?php echo $category->slug; ?>"><span><?php echo $category->name; ?></span></a></li>
                    <?php endforeach; ?>
                    </ul>
                </nav>
            </div>
        </div>
        <div id="pord-container" class="col-md-6">
            <?php 
                foreach($categories as $category) : 
                    $not_in_stock = get_field('not_in_stock', 'item-cat_'.$category->term_id);
                    if(!empty($not_in_stock) && $not_in_stock[0] == 1): 
                        continue;
                    endif;
            ?>
            <div id="<?php echo $category->slug; ?>">
                <h3><?php echo $category->name; ?></h3>
                <?php 
                    query_posts(array(
                        'post_type' => 'product',
                        'orderby' => 'ASC',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'item-cat',
                                'field' => 'term_id',
                                'terms' => array($category->term_id)
                            )
                        )
                    )); 
                    if(have_posts()) :
                        while(have_posts()) :
                            the_post();
                            $not_in_stock = get_field('not_in_stock');
                            if(!empty($not_in_stock) && $not_in_stock[0] == 1): 
                                continue;
                            endif;
                ?>
                <div class="skip_well"> 
                    <div class="product-figure">
                        <?php if(has_post_thumbnail()) : ?>
                             <?php the_post_thumbnail('menu-image'); ?>
                        <?php endif; ?>
                    </div>
                    <h4 class="text-danger"><?php the_title(); ?></h4>
                    <p><?php the_content(); ?></p>
                    <form class="form-horizontal" name="add-to-cart-form" id="add-prod-<?php echo get_the_ID(); ?>">
                        <?php if($category->term_id == 11) : ?>
                        <div class="form-group">
                            <input type="checkbox" name="burger_note_<?php echo get_the_ID(); ?>" id="burger_note_<?php echo get_the_ID(); ?>" value="Make mine a Naked Burger" /> Make mine a Naked Burger
                        </div>
                        <?php endif; ?>
                        <?php 
                            if(get_field('product_type') == 'variable') : 
                                $variable_attribute = get_field('variable_attribute');
                                $count = 0;
                                if(is_array($variable_attribute) && !empty($variable_attribute)) : 
                                    foreach ($variable_attribute as $va): $count++;
                                        $attribute_id = get_the_ID().'/'.$count;
                        ?>
                            <div class="form-group">
                                <label><?php echo $va['attribute_name']; ?></label>
                                <div class="col-sm-offset-7 col-sm-5"> <span class="snack_pro_price"><strong> $<?php echo number_format($va['price'],2,'.',''); ?></strong></span>
                                    <button type="submit" class="btn btn-success defalt_button pull-right btn-add-to-cart" data-product="<?php echo $attribute_id; ?>" data-quantity="1"><?php echo(isset($_SESSION['cart'][$attribute_id])) ? 'ADDED' : 'ADD'; ?></button>
                                </div>
                            </div>
                        <?php endforeach; ?> 
                        <?php endif; ?>
                        <?php elseif(get_field('product_type') == 'linked'): ?>
                        <div class="form-group">
                            <div class="col-sm-offset-7 col-sm-5"> <span class="snack_pro_price"><strong> $<?php echo number_format(get_field('price'),2,'.',''); ?></strong></span>
                                <button type="submit" class="btn btn-success defalt_button pull-right btn-add-to-cart" data-product="<?php echo get_the_ID(); ?>" data-quantity="1"><?php echo(isset($_SESSION['cart'][get_the_ID()])) ? 'ADDED' : 'ADD'; ?></button>
                            </div>
                            <p>Add per item:</p>
                        </div>
                        <?php 
                            $variable_attribute = get_field('variable_attribute');
                                $count = 0;
                                if(is_array($variable_attribute) && !empty($variable_attribute)) : 
                                    foreach ($variable_attribute as $va) : $count++;
                                        $attribute_id = get_the_ID().'/'.$count;
                        ?>
                                <div class="form-group">
                                    <label><?php echo $va['attribute_name']; ?></label>
                                    <div class="col-sm-offset-7 col-sm-5"> <span class="snack_pro_price"><strong> $<?php echo number_format($va['price'],2,'.',''); ?></strong></span>
                                        <button type="submit" class="btn btn-success defalt_button pull-right btn-add-to-cart linked" data-product="<?php echo $attribute_id; ?>" data-quantity="1" <?php echo(isset($_SESSION['cart'][get_the_ID()])) ? '' : 'disabled'; ?>><?php echo(isset($_SESSION['cart'][$attribute_id])) ? 'ADDED' : 'ADD'; ?></button>
                                    </div>
                                </div>
                        <?php endforeach; ?> 
                        <?php endif; ?>
                        <?php elseif(get_field('product_type') == 'combo'): ?>
                        <div class="form-group kids_meal_book" > 
                            <p class="kids_meal_tytle"><!--<input type="radio" name="your_choice" value="Your Choice" checked="checked">--><span>Your choice</span></p>
                            <div class="radio-wrapper kids_meal_radio">
                                <input type="radio" name="your_choice_value" value="Mini Cheese Wagyu" checked="checked"><span> Mini Cheese Wagyu</span>
                                <input type="radio" name="your_choice_value" value="Cheesy Mac Bites"><span> Cheesy Mac Bites</span>
                                <input type="radio" name="your_choice_value" value="Kids Nachos"><span> Bites or  Kids Nachos</span>
                                <input type="radio" name="your_choice_value" value="Sweet Buffalo Wings"> <span>Sweet Buffalo Wings</span>
                            </div>
                            
                           <p class="kids_meal_tytle"><!--<input type="radio" name="chips" value="Chips" checked="checked"> -->Chips</p>
                            <div class="radio-wrapper kids_meal_radio">
                                <!--<input type="radio" name="chips_value" value="Suburban Chips" checked="checked"><span> Suburban Chips</span>-->
                            </div>
                            
                            <p class="kids_meal_tytle"><!--<input type="radio" name="chips" value="Chips" checked="checked"> -->Please Choose one</p>
                            <p class="sub_kids_meal_tytle"><input type="radio" name="only_one" value="Drinks only" checked="checked"> Soft Drinks</p>
                            <div class="radio-wrapper sel-only-one sub_kids_meal_radio">
                                <input type="radio" name="only_one_value" value="Coke" checked="checked"> <span>Coke</span>
                                <input type="radio" name="only_one_value" value="Diet Coke"><span> Diet Coke</span>
                                <input type="radio" name="only_one_value" value="Coke Zero"><span> Coke Zero</span>
                                <input type="radio" name="only_one_value" value="Fanta"> <span>Fanta</span>
                                <input type="radio" name="only_one_value" value="Fanta Lift"><span> Fanta Lift</span>
                                <input type="radio" name="only_one_value" value="Sprite"><span> Sprite</span>
                                <input type="radio" name="only_one_value" value="Water"><span> Water</span>
                            </div>

                           <p class="sub_kids_meal_tytle"><input type="radio" name="only_one" value="Flavored milk"> Flavored milk</p>
                            <div class="radio-wrapper sel-only-one sub_kids_meal_radio">
                                <input type="radio" name="only_one_value" value="Chocolate" disabled="disabled"><span> Chocolate</span>
                                <input type="radio" name="only_one_value" value="Strawberry" disabled="disabled"> <span>Strawberry</span>
                                <input type="radio" name="only_one_value" value="Peppermint" disabled="disabled"> <span>Peppermint</span>
                                <input type="radio" name="only_one_value" value="Pineapple" disabled="disabled"><span> Pineapple</span>
                            </div>

                            <p class="sub_kids_meal_tytle"><input type="radio" name="only_one" value="Juice"> Juice</p>
                            <div class="radio-wrapper sel-only-one kids_meal_radio">
                                <input type="radio" name="only_one_value" value="Apple" disabled="disabled"> <span>Apple</span>
                                <input type="radio" name="only_one_value" value="Apple Blackcurrant" disabled="disabled"> <span>Apple Blackcurrant</span>
                                <input type="radio" name="only_one_value" value="Sky Juice Water" disabled="disabled"> <span>Sky Juice (Water)</span>
                            </div>

<!--                           <p class="kids_meal_tytle"><input type="radio" name="ice_cream" value="Ice Cream" checked="checked"> Ice Cream</p>
                            <div class="radio-wrapper kids_meal_radio">
                                <input type="radio" name="ice_cream_value" value="Topping Chocolate" checked="checked"><span> Topping Chocolate</span>
                                <input type="radio" name="ice_cream_value" value="Strawberry"> <span>Strawberry</span>
                                <input type="radio" name="ice_cream_value" value="Spearmint"><span> Spearmint</span>
                                <input type="radio" name="ice_cream_value" value="Pineapple"> <span>Pineapple</span>
                                <input type="radio" name="ice_cream_value" value="Plain Vanilla"><span> Plain Vanilla</span>
                            </div>-->
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-7 col-sm-5"> <span class="snack_pro_price"><strong> $<?php echo number_format(get_field('price'),2,'.',''); ?></strong></span>
                                <button type="submit" class="btn btn-success defalt_button pull-right btn-add-to-cart" data-product="<?php echo get_the_ID(); ?>" data-quantity="1" data-product-type="combo"><?php echo(isset($_SESSION['cart'][get_the_ID()])) ? 'ADDED' : 'ADD'; ?></button>
                            </div>
                        </div>
                        <?php elseif(get_field('product_type') == 'single'): ?>
                        <div class="form-group">
                            <div class="col-sm-offset-7 col-sm-5"> <span class="snack_pro_price"><strong> $<?php echo number_format(get_field('price'),2,'.',''); ?></strong></span>
                                <button type="submit" class="btn btn-success defalt_button pull-right btn-add-to-cart" data-product="<?php echo get_the_ID(); ?>" data-quantity="1"><?php echo(isset($_SESSION['cart'][get_the_ID()])) ? 'ADDED' : 'ADD'; ?></button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
          
        <div class="col-md-4">
            <div class="cart-item-box">
                <h3>Your order <div class="for_pickup text-center pull-right">For Pickup</div></h3>
                <div class="skip_well">
                    <form name="cart" action="<?php echo href(CHECKOUT); ?>" method="POST">
                       <div class="row">
                       <div class="col-md-6"><div class="form-group">
                            <label for="Deliveryinput">Requested for:</label>
                            <select name="pickup_time" class="form-control select-requested" id="order_timing">
                                <?php echo get_order_timing_display(); ?>
                            </select>
                        </div></div>
                       <div class="col-md-6"><div class="form-group">
                            <label for="registration_no">Car Rego. No: <a href="#" data-toggle="tooltip" title="We require a Car Rego number to identify your order at the Slide Thru (Drive Thru).">Why?</a></label>
                            <input type="text" name="rege_no" id="rege_no" value="<?php echo (isset($_SESSION['rege_no'])) ? $_SESSION['rege_no'] : ''; ?>" class="form-control" autocomplete="off"/>
                        </div></div>
                       
                         <div class="col-md-12"><div class="form-group cart-products" id="cart-products_list">
                        <?php 
                            $cart_details = print_cart(); 
                            echo $cart_details['html'];
                        ?>
                        </div></div>
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