<?php /* Template Name: Home */ ?>
<?php session_start(); ?>
<?php get_header(); ?>
<?php query_posts(array('post_type' => 'sliders', 'posts_per_page' => -1)); ?>
<?php if(have_posts()) :?>
<div class="main_banner">
    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner" role="listbox">
            <?php $first = 0; ?>
            <?php while(have_posts()) : the_post();?>
            <?php if(has_post_thumbnail()) : $first++;?>
            <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full'); ?>
            <div class="main_banner_img item <?php echo ($first == 1) ? 'active' : ''; ?>" style="background-image:url(<?php echo $image[0]; ?>)"> 
                <?php 
                    $attr = array(
                        'class' => "img-responsive",
                        'alt'   => trim( strip_tags( $wp_postmeta->_wp_attachment_image_alt ) )
                    );
                    the_post_thumbnail('full', $attr);
                ?>
            </div>
            <?php endif; ?>
            <?php endwhile; ?>
            <?php wp_reset_query(); ?>
        </div>
    </div>
</div>
<?php endif; ?>
<!--Banner_End-->
<!--Section_one_start-->
<?php $home_blocks = get_field('home_blocks'); ?>
<?php if(!empty($home_blocks)) : ?>
<div class="container-fluid section_one">
    <div class="container">
        <div class="row">
            <?php foreach ($home_blocks as $block) : ?>
            <div class="col-md-3 col-sm-6">
                <div class="home_box">
                    <h4><?php echo $block['block_title']; ?></h4>
                    <center> <a href="<?php echo ($block['block_url'] <> '') ? $block['block_url'] : '#'; ?>"><img src="<?php echo $block['block_icon']; ?>" class="img-responsive" alt=""/></a> </center>
                  
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<!--Section_one_End-->  
<section class="mobile-container">
    <header class="mobile-header">
        <figure class="text-center">
           <center> <a href="<?php echo home_url(); ?>"><img src="<?php header_image(); ?>" alt="" title="logo" class="img-responsive mobilelogo"/></a></center>
        </figure>
    </header>
    <div class="mobile-blocks" style="background-image:url(<?php echo get_template_directory_uri(); ?>/images/home_banner.jpg);">
        <article class="mobile-menu-area">
            <ul class="mobiUL clearfix">
                <li class="mobiLi"><a href="<?php echo home_url(); ?>/menu/">
                    <figure class="mobi-fig">
                     <img src="<?php bloginfo('template_directory'); ?>/images/mobile_menu_logo.png" alt="" width="50" height="41" class="img-responsive">
                        
                    </figure>
                    <span class="mobi-title">Menu</span>
                </a></li>
                <li class="mobiLi"><a href="<?php echo home_url(); ?>/book-now/">
                    <figure class="mobi-fig">
                    
                     <img src="<?php bloginfo('template_directory'); ?>/images/mobile_menu_preorder_img.png" alt="" width="50" height="41" class="img-responsive">
                   
                     
                    </figure>
                    <span class="mobi-title">PRE ORDER</span>
                </a></li>
                <li class="mobiLi"><a href="<?php echo home_url(); ?>/about-us/">
                    <figure class="mobi-fig">
                        <img src="<?php echo site_url() ;?>/wp-content/uploads/2015/07/about_us_icon.png" alt="" width="66" height="69" class="img-responsive">
                    </figure>
                    <span class="mobi-title">ABOUT US</span>
                </a></li>
                <li class="mobiLi"><a href="<?php echo home_url(); ?>/contact-us/">
                    <figure class="mobi-fig">
                        <img src="<?php echo site_url() ;?>/wp-content/uploads/2015/07/contact_icon.png" alt="" width="66" height="69" class="img-responsive">
                    </figure>
                    <span class="mobi-title">Contact Us</span>
                </a></li>
                <li class="mobiLi"><a href="<?php echo home_url(); ?>/functions/">
                    <figure class="mobi-fig">
                        <img src="<?php bloginfo('template_directory'); ?>/images/mobile_menu_function.png" alt="" width="50" height="41" class="img-responsive">
                    </figure>
                    <span class="mobi-title">Functions</span>
                </a></li>
                <li class="mobiLi"><a href="<?php echo home_url(); ?>/store-menu/">
                    <figure class="mobi-fig">
                        <img src="<?php bloginfo('template_directory'); ?>/images/mobile_menu_icon.png" alt="" width="50" height="41" class="img-responsive">
                    </figure>
                    <span class="mobi-title">Store menu</span>
                </a></li>
                <li class="mobiLi"><a href="<?php echo home_url(); ?>/take-away-menu/">
                    <figure class="mobi-fig">
                        <img src="<?php bloginfo('template_directory'); ?>/images/mobile_menu_tekaway.png" alt="" width="50" height="41" class="img-responsive">
                    </figure>
                    <span class="mobi-title">Takeaway menu</span>
                </a></li>
                <li class="mobiLi"><a href="<?php echo home_url(); ?>/contact-us/">
                    <figure class="mobi-fig">
                        <img src="<?php bloginfo('template_directory'); ?>/images/mobile_menu_email.png" alt="" width="50" height="41" class="img-responsive">
                    </figure>
                    <span class="mobi-title">Email Us</span>
                </a></li>
                <li class="mobiLi"><a href="<?php echo home_url(); ?>" class="fullsite-link">
                    <figure class="mobi-fig">
                        <img src="<?php bloginfo('template_directory'); ?>/images/mobile_menu_site.png" alt="" width="50" height="41" class="img-responsive">
                    </figure>
                    <span class="mobi-title">Full Site</span>
                </a></li>
            </ul>
        </article>
    </div>
</section>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        jQuery('.fullsite-link').click(function(event) {
            event.preventDefault();
            jQuery('.mobile-container,.header_bg,.footer_bg,.section_one,.main_banner').addClass('backfullsite');
        });    
    });
</script>     
<?php get_footer(); ?>