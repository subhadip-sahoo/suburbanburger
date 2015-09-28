<?php
    session_start();
    get_header(); 
    get_template_part( 'page', 'banner' );
    while ( have_posts() ) : the_post();
        get_template_part( 'content', 'page' );
    endwhile;
    get_footer(); 
?>
