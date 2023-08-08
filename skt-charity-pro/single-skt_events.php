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
					$daycheck = '';
					$custom = get_post_custom(get_the_ID());
					$sd = $custom["skt_events_startdate"][0];
					$ed = $custom["skt_events_enddate"][0];
					$time_format = get_option('time_format');
					$stime = date($time_format, $sd);
					$etime = date($time_format, $ed);
					$skt_event_vanue = esc_html( get_post_meta( get_the_ID(), 'skt_event_vanue', true ) );
					$skt_event_host = esc_html( get_post_meta( get_the_ID(), 'skt_event_host', true ) );
					$skt_event_button = esc_html( get_post_meta( get_the_ID(), 'skt_event_button', true ) );
					$skt_event_button_link = esc_html( get_post_meta( get_the_ID(), 'skt_event_button_link', true ) );
				?>
                <div class="fullcolumn-event-right">
                  <div class="vanuetiemhost">
                    <div class="vanue-tiem-host">
                      <i class="fa fa-calendar"></i> <?php $longdate = date("j F Y", $sd); echo  $longdate;?>
                    </div>
                  </div>
                  <?php if(!empty($skt_event_vanue)) { ?>
                  <div class="vanuetiemhost"> 
                    <div class="vanue-tiem-host">
					  <i class="fa fa-map-marker"></i> <?php echo $skt_event_vanue; ?> 
                    </div>
                  </div>
                  <?php } ?>
                  <div class="vanuetiemhost"> <i class="fa fa-clock-o"></i>
                  <?php 
					$longdate = date("D j", $sd);
					if ($daycheck == null) { echo ''.$longdate.', &nbsp;'; }
					if ($daycheck != $longdate && $daycheck != null) { echo '' . $longdate.', '; }
					echo $stime; 
				  ?>
                  <?php _e(' - ','complete'); ?>
                  <?php
					$elongdate = date("M j", $ed);
					if ($daycheck == null) { echo ''.$elongdate.', &nbsp;'; }
					if ($daycheck != $elongdate && $daycheck != null) { echo ''.$elongdate.', '; }
					echo $etime; 
				  ?>
                  </div>
                  <?php if(!empty($skt_event_host)) { ?>
                  <div class="vanuetiemhost">
                    <div class="vanue-tiem-host">
					  <i class="fa fa-user"></i> <?php echo $skt_event_host; ?>
                    </div>
                  </div>
                  <?php } ?>                  
                </div>
                <div class="spacecode" style="height:10px;"></div>
                <?php if ( has_post_thumbnail() ) { ?>
                <?php the_post_thumbnail(array(320, 320), array( 'class' => 'alignleft' ) ); ?>
                <?php } ?>
                <?php the_content(); ?>
              </div>
              <div style="clear:both"></div>
              <?php if(!empty($skt_event_button)) { ?>
					<div class="single-btn"><a href="<?php echo $skt_event_button_link; ?>" class="event-btn event-read-more"><?php echo $skt_event_button; ?></a></div>
				  <?php } ?>
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