<?php
    /* Template Name: My Account */
    session_start();
    global $wp_query, $user_ID;
    if(!$user_ID){
        wp_safe_redirect(home_url());
        exit();
    }
    $userdata = get_userdata($user_ID);
    get_header();     
    get_template_part( 'page', 'banner' );
    
?>
<div class="container-fluid menupage_footer">
  <div class="container">
      <div class="bs-callout bs-callout-success row">
        <div class="col-md-6 genra_page">
          <?php while(have_posts()): the_post();?>
          <h4><?php the_content(); ?></h4>
          <?php endwhile; ?>
          <ul class="payment-details payment_nav">
            <li>
              <label>Name :</label>
              <?php echo $userdata->display_name;?></li>
            <li>
              <label>Email Address :</label>
              <?php echo $userdata->user_email;?></li>
            <li>
              <label>Contact Number :</label>
              <?php echo get_user_meta($user_ID, 'contact_number', TRUE);?></li>
          </ul>
        </div>
      </div>
  </div>
</div>
<?php get_footer(); ?>
