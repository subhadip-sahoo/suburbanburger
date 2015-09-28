<?php
global $wp_query;
get_header();
?>
<!--Banner_start-->
<div class="inner_page">
    <div class="container-fluid inner_banner">
        <div class="row">
            <img src="<?php echo get_template_directory_uri(); ?>/images/inner_banner.png" alt="" title="" class="img-responsive"/>
        </div>
    </div>
    <!--Banner_End-->
    <div class="container-fluid inner_section_bg">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="inner_page_title"><?php the_title(); ?></h1>
                </div>
            </div>
            <!--section_first_start-->
            <?php 
                query_posts(array(
                    'post_type' => 'menu',
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'menu-cat',
                            'field' => 'term_id',
                            'terms' => array(4)
                        )
                    )
                )); 
                if(have_posts()) :
            ?>
            <section class="sect_one">
                <h2>STARTERS</h2>
                <?php 
                    while(have_posts()) : the_post();
                        $add_menu_items = get_field('add_menu_items'); 
                        $total_prod = count($add_menu_items); 
                        $count = 0; 
                        foreach($add_menu_items as $mi) : $count++;
                            $remove_bar = (($total_prod - 2) < $count) ? 'last_bor' : '';
                            $class = ($count % 2 == 0)? 'sect_one_right' : 'sect_one_left';
                            if($count % 2 <> 0): 
                ?>
                <div class="row ">
                <?php endif; ?>
                    <div class="col-md-6 <?php echo $class; ?> <?php echo $remove_bar; ?>">
                        <div class="row ">
                            <?php if(has_post_thumbnail( $mi->ID )) : ?>
                            <div class="produt_img col-md-3">
                                <?php echo get_the_post_thumbnail($mi->ID, 'menu-image'); ?>
                            </div>
                            <?php endif; ?>
                            <div class="produt_detal col-md-6">
                                <h5><?php echo $mi->post_title; ?></h5>
                                <p><?php echo $mi->post_excerpt; ?></p>
                            </div>
                            <div class="produt_price col-md-3">
                                <h5>Price</h5>
                                <p class="price">$<?php echo get_field('price', $mi->ID, TRUE); ?></p>
                            </div>
                        </div>
                    </div>
                <?php if($count % 2 == 0): ?>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
                <?php endwhile; ?>
                <?php wp_reset_query(); ?>
            </section>
            <?php endif; ?>
            <!--section_first_end-->
            <!--section_secont_start-->
            <?php 
                query_posts(array(
                    'post_type' => 'menu',
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'menu-cat',
                            'field' => 'term_id',
                            'terms' => array(5)
                        )
                    )
                )); 
                if(have_posts()) :
            ?>
            <section class="sect_two">
                <h2>fries</h2>
                <div class="row">
                    <?php 
                    while(have_posts()) : the_post();
                        $add_menu_items = get_field('add_menu_items'); 
                        $total_prod = count($add_menu_items); 
                        $count = 0; 
                        foreach($add_menu_items as $mi) : $count++;
                    ?>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5">
                                <?php if(has_post_thumbnail( $mi->ID )) : ?>
                                <div class="left_box">
                                    <?php echo get_the_post_thumbnail($mi->ID, 'fire-image', array('class' => 'img-responsive')); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-7 right_box">
                                <h5><?php echo $mi->post_title; ?></h5>
                                <p><span>$<?php echo number_format(get_field('price', $mi->ID, TRUE), 2, '.',''); ?> </span><?php echo $mi->post_excerpt; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php endwhile; ?>
                <?php wp_reset_query(); ?>
                </div>
            </section>
            <?php endif; ?>
            <!--section_secont_end-->
            <!--section_thrid_start-->
            <?php 
                query_posts(array(
                    'post_type' => 'menu',
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'menu-cat',
                            'field' => 'term_id',
                            'terms' => array(6)
                        )
                    )
                )); 
                if(have_posts()) :
            ?>
            <section class="sect_one sect_three">
                <h2>BURGERS</h2>
                <?php 
                    while(have_posts()) : the_post();
                        $add_menu_items = get_field('add_menu_items'); 
                        $total_prod = count($add_menu_items); 
                        $count = 0; 
                        foreach($add_menu_items as $mi) : $count++;
                            $remove_bar = (($total_prod - 2) < $count) ? 'last_bor' : '';
                            $class = ($count % 2 == 0)? 'sect_one_right' : 'sect_one_left';
                            if($count % 2 <> 0): 
                ?>
                <div class="row ">
                <?php endif; ?>
                    <div class="col-md-6 <?php echo $class; ?> <?php echo $remove_bar; ?>">
                        <div class="row ">
                            <?php if(has_post_thumbnail( $mi->ID )) : ?>
                            <div class="produt_img col-md-3">
                                <?php echo get_the_post_thumbnail($mi->ID, 'menu-image'); ?>
                            </div>
                            <?php endif; ?>
                            <div class="produt_detal col-md-6">
                                <h5><?php echo $mi->post_title; ?></h5>
                                <?php echo $mi->post_content; ?>
                            </div>
                            <div class="produt_price col-md-3">
                                <h5>Price</h5>
                                <p class="price">$<?php echo get_field('price', $mi->ID, TRUE); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php if($count % 2 == 0): ?>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
                <?php endwhile; ?>
                <?php wp_reset_query(); ?>
            <div class="col-md-12">
                <p class="text-center noted">NOTE - All burgers will have a customise option of either naked(no bun) or a gluten free roll</p>
            </div>
        </section>
        <?php endif; ?>
        <!--section_thrid_end-->
        <!--section_forth_start-->
        <section class="sect_one sect_foth">
            <h2>sliders</h2>
            <div class="row ">
                <div class="col-md-4 sect_one_left">
                    <div class="row">
                        <div class="produt_detal col-md-9">
                            <h5>Squiddy </h5>
                            <p class="pro_det">Suburban Waghu Bacon Burger. Lettuce, Tomato, BBQ Mayo, Mozzarella Cheese</p>
                        </div>
                        <div class="produt_price col-md-3">
                            <h5>Price <span class="price">$10</span></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 sect_one_right sect_one_middle">
                    <div class="row">
                        <div class="produt_detal col-md-9">
                            <h5>Squiddy </h5>
                            <p class="pro_det">Suburban Waghu Bacon Burger. Lettuce, Tomato, BBQ Mayo, Mozzarella Cheese</p>
                        </div>
                        <div class="produt_price col-md-3">
                            <h5>Price <span class="price">$10</span></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 sect_one_right">
                    <div class="row">
                        <div class="produt_detal col-md-9">
                            <h5>Squiddy </h5>
                            <p class="pro_det">Suburban Waghu Bacon Burger. Lettuce, Tomato, BBQ Mayo, Mozzarella Cheese</p>
                        </div>
                        <div class="produt_price col-md-3">
                            <h5>Price <span class="price">$10</span></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="col-md-4 sect_one_left last_bor">
                    <div class="row">
                        <div class="produt_detal col-md-9">
                            <h5>Squiddy </h5>
                            <p class="pro_det">Suburban Waghu Bacon Burger. Lettuce, Tomato, BBQ Mayo, Mozzarella Cheese</p>
                        </div>
                        <div class="produt_price col-md-3">
                            <h5>Price <span class="price">$10</span></h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 sect_one_right sect_one_middle last_bor">
                    <div class="row">
                        <div class="produt_detal col-md-9">
                            <h5>Squiddy </h5>
                            <p class="pro_det">Suburban Waghu Bacon Burger. Lettuce, Tomato, BBQ Mayo, Mozzarella Cheese</p>
                        </div>
                        <div class="produt_price col-md-3">
                            <h5>Price <span class="price">$10</span></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 sect_one_right last_bor">
                    <div class="row">
                        <div class="produt_detal col-md-9">
                            <h5>Squiddy </h5>
                            <p class="pro_det">Suburban Waghu Bacon Burger. Lettuce, Tomato, BBQ Mayo, Mozzarella Cheese</p>
                        </div>
                        <div class="produt_price col-md-3">
                            <h5>Price <span class="price">$10</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--section_forth_end-->
    </div>
</div>
</div>
<!--Section_one_End-->
<div class="container-fluid last_section_bg_contaer">
<div class="container">
<h2 class="last_secti">Slider Deals</h2>

<div class="last_section_bg">

<section class="sect_one sect_three">
<div class="row">
<div class="col-md-4">
<ul class="last_section_nav">
    <li>Choose your own mix</li>
    <li>Mixed Dozed Sampler <br> 
    <span>$50</span> House choice including large chips</li>
    <li>KIDS MEAL<span> $10</span><br>
    Slider / Chips / Drink / Ice Cream</li>
</ul>

</div>
<div class="col-md-4">
<ul class="last_section_nav">
<li>Russian Roulette Box<br>
<span>$50</span> House Choice including 2 Sliders <br>
from Hell and Large Chips</li>
<li>Mini Cheese Box<br>
<span>$50</span> Including large chips </li>
<li>&emsp;</li>

</ul>
</div>
<div class="col-md-4 ">
<ul class="last_section_nav last__se_nav">
<li>Choose Your Own Box 12<br>
for <span>$50</span> Including Large Chips</li>
<li>KIDS MEAL<span> $10</span></li>
</ul>
</div>
</div>
</section>
</div>
</div>
</div>
<?php get_footer(); ?>