<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div class="container">
 *
 * @package SKT Charity
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(''); ?>>
<?php do_action( 'wp_body_open' ); ?>
<?php if(is_active_sidebar('header-info-left') || is_active_sidebar('header-info-right') ) { ?>
  <div class="headertop">
     <div class="container">    
      	<?php if ( is_active_sidebar( 'header-info-left' ) ) : ?>
        <div class="widget-left">
              <?php dynamic_sidebar( 'header-info-left' ); ?>
       </div><!--widget-left-->
       <?php endif; ?>
        <?php if ( is_active_sidebar( 'header-info-right' ) ) : ?>
        <div class="widget-right">
              <?php dynamic_sidebar( 'header-info-right' ); ?>
       </div><!--widget-left-->
       <?php endif; ?><!--widget-right-->
         <div class="clear"></div>     
    </div>
  </div><!-- .end headertop -->
<?php } ?>
<?php
	$hideslide = get_theme_mod('hide_slider', '1');
?>  
  <div class="header">
        <div class="container">
            <div class="logo">
			<?php skt_charity_the_custom_logo(); ?>
            <h1><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
            <p><?php bloginfo('description'); ?></p>
            </div><!-- logo -->
            <div class="header_right"> 
             <div class="toggle">
                <a class="toggleMenu" href="<?php esc_url('#');?>"><?php esc_html_e('Menu','skt-charity'); ?></a>
             </div><!-- toggle --> 
             <div class="sitenav">
                    <?php wp_nav_menu(array('theme_location' => 'primary')); ?>
             </div><!-- site-nav -->
            <div class="clear"></div>
          </div><!-- header_right -->
          <div class="clear"></div>
        </div><!-- container -->
  </div><!--.header -->  