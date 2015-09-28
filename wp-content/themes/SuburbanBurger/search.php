<?php global $wp_query; ?>
<?php get_header(); ?>
<div class="container-fluid inner_banner">
    <div class="row"> 
        <img src="<?php echo get_template_directory_uri(); ?>/images/inner_banner.png" alt="" title="" class="img-responsive"/>
    </div>
</div>
<div class="container-fluid menupage_footer">
    <div class="container ">
        <div class="row">
            <div class="col-md-12">
                <h1 class="inner_page_title"><?php _e('Search result for: '.  get_search_query(), 'suburbanburger'); ?></h1>
            </div>
        </div>
        <div class="menupage-section clearfix menusection1">
            <!--<h2 class="menupage-section-title"><?php //echo $category->name; ?></h2>-->
            
            <article class="menupage-section-container clearfix">
            <?php 
                if(esc_attr($_REQUEST['s']) <> ''){
                query_posts(array(
                    'post_type' => 'product',
                    'posts_per_page' => -1,
                    's' => esc_attr($_REQUEST['s'])
                )); 
                if(have_posts()) :
                    while(have_posts()) : the_post();
            ?>
            <div class="menupageblog-blocks">
                <div class="menupageblog-blocks-inner clearfix">
                    <div class="menuproimg-figure">
                        <?php if(has_post_thumbnail()) : ?>
                             <?php the_post_thumbnail('menu-image'); ?>
                        <?php endif; ?>
                    </div>
                    <div class="menuprodet-description">
                        <h5 class="menuprodet-description-title"><?php the_title(); ?></h5>
                        <p><?php the_content(); ?></p>
                        <?php if(get_field('product_type') == 'variable') : 
                            $variable_attribute = get_field('variable_attribute');
                            if(is_array($variable_attribute) && !empty($variable_attribute)) : 
                                foreach ($variable_attribute as $va) :?>
                                    <p><?php echo $va['attribute_name']; ?></p>
                               <?php endforeach; ?> 
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="menupropric-price">
                        <h5 class="menuprodet-description-title">Price</h5>
                        <?php if(get_field('product_type') == 'variable') : 
                            $variable_attribute = get_field('variable_attribute');
                            if(is_array($variable_attribute) && !empty($variable_attribute)) : 
                                echo '<p class="price">&nbsp;</p>';
                                foreach ($variable_attribute as $va) :?>
                                    <p class="price">$<?php echo number_format($va['price'],2,'.',''); ?></p>
                               <?php endforeach; ?> 
                            <?php endif; ?>
                        <?php else: ?>
                        <p class="price">$<?php echo number_format(get_field('price'),2,'.',''); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php wp_reset_query(); ?>
            <?php else: ?>
                <p>No results found!</p>
            <?php endif; ?>
                <?php  }else{ ?>
                <p>No results found!</p>
                <?php  } ?>
            </article>
        </div>
    </div>
</div>
<?php get_footer(); ?>
