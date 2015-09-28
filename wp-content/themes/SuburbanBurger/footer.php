<footer class="container-fluid footer_bg">
    <div class="container">
        <div class="row">
            <div class="col-md-4 fot_cont">
                <ul class="list-inline fot_cont_nav">
                    <li><span class="fot_phone"></span><a href="<?php echo (get_field('site_phone_no', 'option') <> '') ? 'tel:'.get_field('site_phone_no', 'option') : '#';?>"><?php echo get_field('site_phone_no', 'option'); ?></a></li>
                    <li><span class="fot_mail"></span><a href="<?php echo (get_field('site_email_address', 'option') <> '') ? 'mailto:'.get_field('site_email_address', 'option') : '#';?>"><?php echo get_field('site_email_address', 'option'); ?></a></li>
                </ul>
                <p><?php echo get_field('site_copyright_text', 'option'); ?></p>
            </div>
            <div class="col-md-6 fot_time">
                <?php $weekly_timeline =  get_field('weekly_timeline', 'option'); ?>
                <ul class="list-inline col-md-6">
                    <?php $count = 0 ?>
                    <?php foreach($weekly_timeline as $tl) : $count++;?>
                    <li><i class="glyphicon glyphicon-time"></i>&ensp; <?php echo $tl['week_day']; ?> - <?php echo ($tl['opening_time'] <> '') ? $tl['opening_time'] : 'Closed'; ?></li>
                    <?php if($count == 4){break;} ?>
                    <?php endforeach; ?>
                </ul>
                <ul class="list-inline col-md-6">
                    <?php $count = 0 ?>
                    <?php foreach($weekly_timeline as $tl) : $count++;?>
                    <?php if($count < 5){continue;} ?>
                    <li><i class="glyphicon glyphicon-time"></i>&ensp; <?php echo $tl['week_day']; ?> - <?php echo ($tl['opening_time'] <> '') ? $tl['opening_time'] : 'Closed'; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-2 ">
                <ul class="list-inline pull-right fot_socal_nav">
                    <li><a href="<?php echo (get_field('facebook_url', 'option') <> '') ? get_field('facebook_url', 'option') : '#';?>" target="_blank"><i class="sprite-facebook" title="facebook"></i></a></li>
                    <li><a href="<?php echo (get_field('twitter_url', 'option') <> '') ? get_field('twitter_url', 'option') : '#';?>" target="_blank"><i class="sprite-twitter" title="twitter"></i></a></li>
                    <li><a href="<?php echo (get_field('instagram_url', 'option') <> '') ? get_field('instagram_url', 'option') : '#';?>" target="_blank"><i class="sprite-instagram" title="instagram"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
        </body>
</html>    



