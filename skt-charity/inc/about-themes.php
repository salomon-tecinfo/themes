<?php
/**
 * SKT Charity About Theme
 *
 * @package SKT Charity
 */
 
//about theme info
add_action( 'admin_menu', 'skt_charity_abouttheme' );
function skt_charity_abouttheme() {    	
	add_theme_page( esc_html__('About Theme', 'skt-charity'), esc_html__('About Theme', 'skt-charity'), 'edit_theme_options', 'skt_charity_guide', 'skt_charity_mostrar_guide');   
} 
//guidline for about theme
function skt_charity_mostrar_guide() { 
	//custom function about theme customizer
	$return = add_query_arg( array()) ;
?>
<div class="wrapper-info">
	<div class="col-left">
   		   <div class="col-left-area">
			  <?php esc_attr_e('Theme Information', 'skt-charity'); ?>
		   </div>
          <p><?php esc_html_e('SKT Charity is a charity WordPress theme which is meant for donation, NGO, Churches, fundraising and non profit websites. It can also be used for business, corporate, agency and portfolio as well as personal and blogging websites. It is compatible with WooCommerce, Contact form 7 and Nextgen gallery among others and is multilingual and translation ready. It is simple, adaptable, flexible and responsive theme. It is multipurpose template and comes with a ready to import Elementor template plugin as add on which allows to import 150+ design templates for making use in home and other inner pages.','skt-charity'); ?></p>
		  <a href="<?php echo esc_url(SKT_PRO_THEME_URL); ?>"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/free-vs-pro.png" alt="" /></a>
	</div><!-- .col-left -->
	<div class="col-right">			
			<div class="centerbold">
				<hr />
				<a href="<?php echo esc_url(SKT_LIVE_DEMO); ?>" target="_blank"><?php esc_html_e('Live Demo', 'skt-charity'); ?></a> | 
				<a href="<?php echo esc_url(SKT_PRO_THEME_URL); ?>"><?php esc_html_e('Buy Pro', 'skt-charity'); ?></a> | 
				<a href="<?php echo esc_url(SKT_THEME_DOC); ?>" target="_blank"><?php esc_html_e('Documentation', 'skt-charity'); ?></a>
                <div class="space5"></div>
				<hr />                
                <a href="<?php echo esc_url(SKT_THEME_URL); ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/sktskill.jpg" alt="" /></a>
			</div>		
	</div><!-- .col-right -->
</div><!-- .wrapper-info -->
<?php } ?>