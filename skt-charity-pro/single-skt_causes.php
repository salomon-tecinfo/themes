<?php global $complete;?>
<?php get_header(); ?>

<div class="page_fullwidth_wrap layer_wrapper"> 
  <!--CUSTOM PAGE HEADER STARTS-->
  <?php get_template_part('sktframe/core','pageheader'); ?>
  <!--CUSTOM PAGE HEADER ENDS-->
  <div id="content">
    <div class="center"> 
      <!--POST START-->
      <div class="single_wrap no_sidebar">
        <div class="single_post">
          <?php if(have_posts()): ?>
          <?php while(have_posts()): ?>
          <?php the_post(); ?>
          <div <?php post_class(); ?> id="post-<?php the_ID(); ?>"> 
            
            <!--EDIT BUTTON START-->
            <?php if ( is_user_logged_in() && is_admin() ) { ?>
            <div class="edit_wrap"> <a href="<?php echo get_edit_post_link(); ?>">
              <?php _e('Edit','complete'); ?>
              </a> </div>
            <?php } ?>
            <!--EDIT BUTTON END--> 
            <!--POST START-->
            <div class="single_post_content"> 
              <!--POST CONTENT START-->
              <div class="thn_post_wrap">
                <h1 class="postitle entry-title">
                  <?php the_title(); ?>
                </h1>
                <?php
                   $custom = get_post_custom(get_the_ID());
                   $sktcause_donation_target = esc_html( get_post_meta( get_the_ID(), 'sktcause_donation_target', true ) );
				   $sktcause_donation_achieved = esc_html( get_post_meta( get_the_ID(), 'sktcause_donation_achieved', true ) );
                ?>
                <div class="fullcolumn-cause-right">
                  <div class="donated-info">
                    <?php if(!empty($sktcause_donation_target)) { ?>
                    <span class="caus-single-info">
                    <i class="fa fa-check-square-o" aria-hidden="true"></i> <?php echo $sktcause_donation_target; ?>
                    </span>
                    <?php } ?>                    
                    <?php if(!empty($sktcause_donation_achieved)) { ?>
                    <i class="fa fa-usd" aria-hidden="true"></i> <?php echo $sktcause_donation_achieved; ?>
                    <?php } ?>
                  </div>
                </div>
                <div class="spacecode" style="height:10px;"></div>
                <div class="cause-thumb">
                  <?php if ( has_post_thumbnail() ) { ?>
                  <?php the_post_thumbnail(array(320, 320), array( 'class' => 'alignleft' ) ); ?>
                  <?php } ?>
                </div>
                <?php the_content(); ?>
              </div>
              <div style="clear:both"></div>
              <!--POST CONTENT END--> 
            </div>
            <!--POST END--> 
          </div>
          <?php endwhile ?>
          <?php endif ?>
        </div>
      </div>
    </div>
    <!--center class END--> 
  </div>
  <!--#content END--> 
</div>
<!--layer_wrapper class END-->

<?php get_footer(); ?>
