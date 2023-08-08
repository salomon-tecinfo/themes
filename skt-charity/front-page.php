<?php
/**
 * The template for displaying home page.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package SKT Charity
 */
get_header(); 

$hideslide = get_theme_mod('hide_slider', 1);
$hideboxes = get_theme_mod('hide_boxes', 1);
$hidewlcm = get_theme_mod('hide_welcome', 1);

if (!is_home() && is_front_page()) { 
if( $hideslide == '') { 

$pages = array();// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
for($sld=7; $sld<10; $sld++) { 
	$mod = absint( get_theme_mod('page-setting'.$sld));
    if ( 'page-none-selected' != $mod ) {
      $pages[] = $mod;// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
    }	
} 
if( !empty($pages) ) :
$args = array(
      'posts_per_page' => 3,
      'post_type' => 'page',
      'post__in' => $pages,
      'orderby' => 'post__in'
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) :	
	$sld = 7;
?>
<section id="home_slider">
  <div class="slider-wrapper theme-default">
  	<div class="slider-overlay"></div>
    <div class="slider-shadow"></div>
    <div id="slider" class="nivoSlider">
		<?php
        $i = 0;
        while ( $query->have_posts() ) : $query->the_post();
          $i++;
          $skt_charity_slideno[] = $i;
          $skt_charity_slidetitle[] = get_the_title();
		  $skt_charity_slidedesc[] = get_the_excerpt();
          $skt_charity_slidelink[] = esc_url(get_permalink());
          ?>
          <img src="<?php the_post_thumbnail_url('full'); ?>" title="#slidecaption<?php echo esc_attr( $i ); ?>" />
          <?php
        $sld++;
        endwhile;
          ?>
    </div>
        <?php
        $k = 0;
        foreach( $skt_charity_slideno as $skt_charity_sln ){ ?>
    <div id="slidecaption<?php echo esc_attr( $skt_charity_sln ); ?>" class="nivo-html-caption">
      <div class="slide_info">
        <h2><?php echo esc_html($skt_charity_slidetitle[$k] ); ?></h2>
        <?php if(!empty($skt_charity_slidedesc[$k])){?>
        <p><?php echo esc_html($skt_charity_slidedesc[$k] ); ?></p>
        <?php } ?>
        <div class="clear"></div>
        <a class="slide_more" href="<?php echo esc_url($skt_charity_slidelink[$k] ); ?>">
          <?php esc_html_e('Read More', 'skt-charity');?>
          </a>
      </div>
    </div>
 	<?php $k++;
       wp_reset_postdata();
      } 
	  endif; endif; ?>
  </div>
  <div class="clear"></div>
</section>
<?php } }  
if (!is_home() && is_front_page()) { 
if( $hideboxes == '') { ?>
<div id="pagearea">
    <div class="container">                
            <?php for($fx=1; $fx<5; $fx++) {
				  if( get_theme_mod('page-column'.$fx,false) ) { 			
				  $queryvar = new wp_query('page_id='.get_theme_mod('page-column'.$fx,true));				
				  while( $queryvar->have_posts() ) : $queryvar->the_post(); ?> 
        	    <div class="one_four_page <?php if($fx % 4 == 0) { echo "last_column"; } ?>">
				 <div class="thumb_four_page">
                     <a href="<?php the_permalink(); ?>">				 
                      <?php if ( has_post_thumbnail() ) { ?>
                            <?php the_post_thumbnail( array(65,65,true));?>                        
                       <?php } ?>
                       </a>
                   </div>
                  <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                  <?php the_excerpt(); ?>
                 <a class="more" href="<?php the_permalink(); ?>"><?php esc_html_e('Donate Now!','skt-charity'); ?></a>
        	   </div>
             <?php endwhile;
			 wp_reset_postdata();
			 } } ?>
      <div class="clear"></div>
    </div><!-- .container -->
 </div><!-- #pagearea -->
<div class="clear"></div>
<?php } } 
	if(!is_home() && is_front_page()){ 
	if( $hidewlcm == '') {
?>
 <section id="wrapfirst">
            	<div class="container">
                    <div class="welcomewrap">
					<?php if( get_theme_mod('page-setting1')) { 
						  $queryvar = new WP_query('page_id='.get_theme_mod('page-setting1' ,true));  																					                          while( $queryvar->have_posts() ) : $queryvar->the_post();
					  	  the_post_thumbnail( array(300,300, true));?>      
                     <h2><?php the_title(); ?></h2> 
                     <?php the_content(); ?> 
                     <a class="donatebtn" href="<?php the_permalink(); ?>"><?php esc_html_e('Donate Now!','skt-charity'); ?></a>                     <div class="clear"></div>
                    <?php endwhile; } ?>
               </div><!-- welcomewrap-->
              <div class="clear"></div>
            </div><!-- container -->
       </section>
<?php }} ?>
<div class="container">
     <div class="page_content">
      <?php 
	if ( 'posts' == get_option( 'show_on_front' ) ) {
    ?>
    <section class="site-main">
      <div class="blog-post">
        <?php
                    if ( have_posts() ) :
                        // Start the Loop.
                        while ( have_posts() ) : the_post();
                            /*
                             * Include the post format-specific template for the content. If you want to
                             * use this in a child theme, then include a file called called content-___.php
                             * (where ___ is the post format) and that will be used instead.
                             */
                            get_template_part( 'content', get_post_format() );
                        endwhile;
                        // Previous/next post navigation.
						the_posts_pagination( array(
							'mid_size' => 2,
							'prev_text' => esc_html__( 'Back', 'skt-charity' ),
							'next_text' => esc_html__( 'Next', 'skt-charity' ),
						) );
                    else :
                        // If no content, include the "No posts found" template.
                         get_template_part( 'no-results', 'index' );
                    endif;
                    ?>
      </div>
      <!-- blog-post --> 
    </section>
    <?php
} else {
    ?>
	<section class="site-main">
      <div class="blog-post">
        <?php
                    if ( have_posts() ) :
                        // Start the Loop.
                        while ( have_posts() ) : the_post();
                            /*
                             * Include the post format-specific template for the content. If you want to
                             * use this in a child theme, then include a file called called content-___.php
                             * (where ___ is the post format) and that will be used instead.
                             */
							 ?>
                             <header class="entry-header">           
            				<h1><?php the_title(); ?></h1>
                    		</header>
                             <?php
                            the_content();
                        endwhile;
                        // Previous/next post navigation.
						the_posts_pagination( array(
							'mid_size' => 2,
							'prev_text' => esc_html__( 'Back', 'skt-charity' ),
							'next_text' => esc_html__( 'Next', 'skt-charity' ),
						) );
                    else :
                        // If no content, include the "No posts found" template.
                         get_template_part( 'no-results', 'index' );
                    endif;
                    ?>
      </div>
      <!-- blog-post --> 
    </section>
	<?php
}
	get_sidebar();?>
    <div class="clear"></div>
  </div><!-- site-aligner -->
</div><!-- content --> 
<?php get_footer(); ?>