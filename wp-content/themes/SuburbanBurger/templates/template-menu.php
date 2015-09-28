<?php
/* Template Name: Menu */
global $wp_query;
get_header();
?>
<div class="container-fluid inner_banner">
    <div class="row"> 
        <img src="<?php echo get_template_directory_uri(); ?>/images/inner_banner.png" alt="" title="" class="img-responsive"/>
    </div>
</div>
<div class="container-fluid menupage_footer">
    <div class="container ">
        <div class="row">
            <div class="col-md-12">
                <h1 class="inner_page_title"><?php the_title(); ?></h1>
            </div>
        </div>
        <?php $categories = get_categories(array('post_type' => 'menu', 'taxonomy' => 'menu-cat', 'hide_empty' => 0, 'orderby' => 'id', 'order' => 'ASC')); ?>
        <?php if(is_array($categories) && !empty($categories)) : ?>
        <?php $count = 0; ?>
        <?php foreach($categories as $category) : $count++;?>
        <div class="menupage-section clearfix menusection<?php echo $count; ?>">
            <h2 class="menupage-section-title"><?php echo $category->name; ?></h2>
            <article class="menupage-section-container clearfix">

            <?php 
                query_posts(array(
                    'post_type' => 'menu',
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'menu-cat',
                            'field' => 'term_id',
                            'terms' => array($category->term_id)
                        )
                    )
                )); 
                if(have_posts()) :
                    while(have_posts()) : the_post();
                        $add_menu_items = get_field('add_menu_items'); 
                        if(!empty($add_menu_items) && is_array($add_menu_items)):
                        foreach($add_menu_items as $mi) :
            ?>
            <div class="menupageblog-blocks">
                <div class="menupageblog-blocks-inner clearfix">
                    <div class="menuproimg-figure">
                        <?php if(has_post_thumbnail( $mi->ID )) : ?>
                             <?php echo get_the_post_thumbnail($mi->ID, 'menu-image'); ?>
                        <?php endif; ?>
                    </div>
                    <div class="menuprodet-description">
                        <h5 class="menuprodet-description-title"><?php echo $mi->post_title; ?></h5>
                        <?php if(get_field('product_type', $mi->ID, TRUE) == 'variable') : 
                            $variable_attribute = get_field('variable_attribute', $mi->ID, TRUE);
                            if(is_array($variable_attribute) && !empty($variable_attribute)) : 
                                foreach ($variable_attribute as $va) :?>
                                    <p><?php echo $va['attribute_name']; ?></p>
                               <?php endforeach; ?> 
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="menupropric-price">
                        <!--<h5 class="menuprodet-description-title">Price</h5>-->
                        <?php if(get_field('product_type', $mi->ID, TRUE) == 'variable') : 
                            $variable_attribute = get_field('variable_attribute', $mi->ID, TRUE);
                            if(is_array($variable_attribute) && !empty($variable_attribute)) : 
                                echo '<p class="price">&nbsp;</p>';
                                foreach ($variable_attribute as $va) :?>
                                    <p class="price">$<?php echo number_format($va['price'],2,'.',''); ?></p>
                               <?php endforeach; ?> 
                            <?php endif; ?>
                        <?php else: ?>
                        <p class="price">$<?php echo number_format(get_field('price', $mi->ID, TRUE),2,'.',''); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="menuprodet-description">
                         <p><?php echo $mi->post_content; ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php endwhile; ?>
            </article>
            <?php wp_reset_query(); ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>

