<?php
/* Template Name: Thank you */
    session_start();
    global $wp_query, $user_ID;
    if(!$user_ID){
        wp_safe_redirect(href(BOOK_NOW));
        exit();
    }
    if(!isset($_SESSION['payflow'])){
        wp_safe_redirect(href(BOOK_NOW));
        exit();
    }
    get_header();     
    get_template_part( 'page', 'banner' );
    
?>

<div class="container-fluid menupage_footer">
  <div class="container">
  
      <div class="bs-callout bs-callout-success row">
        <div class="col-md-6 genra_page">
          <?php while(have_posts()): the_post();?>
          <h4>
            <?php the_content(); ?>
          </h4>
          <?php endwhile; ?>
          <ul class="payment-details payment_nav">
            <li>
              <label>PNREF :</label>
              <?php echo $_SESSION['payflow']['PNREF'];?></li>
            <li>
              <label>Transaction Amount :</label>
              <?php echo ($_SESSION['payflow']['AMT']);?>&nbsp;AUD</li>
            <li>
              <label>AUTHCODE :</label>
              <?php echo $_SESSION['payflow']['AUTHCODE'];?></li>
            <li>
              <label>PPREF :</label>
              <?php echo $_SESSION['payflow']['PPREF'];?></li>
            <li>
              <label>CORRELATIONID :</label>
              <?php echo $_SESSION['payflow']['CORRELATIONID'];?></li>
            <li>
              <label>Status :</label>
              <?php echo strtoupper(strtolower($_SESSION['payflow']['RESPMSG']));?></li>
          </ul>
        </div>
        <div class="col-md-6 yes_pay"><img src="<?php echo get_template_directory_uri(); ?>/images/yes_pay.png" alt="" title=""/></div>
      </div>
  </div>
</div>
<?php unset($_SESSION['payflow']); ?>
<?php get_footer(); ?>
