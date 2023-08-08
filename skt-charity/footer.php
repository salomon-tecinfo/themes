<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package SKT Charity
 */
?>
<div id="footer-wrapper">
    	 <div class="container">
            <?php if (is_active_sidebar( 'fc-1' ) ) : ?>
            <div class="cols-3 widget-column-1">  
              <div class="columnfix">	
              <?php dynamic_sidebar( 'fc-1' ); ?>
              </div>
            </div><!--end .widget-column-1-->                  
    		<?php endif; ?> 

			<?php if (is_active_sidebar( 'fc-2' ) ) : ?>
            <div class="cols-3 widget-column-2">  
            	<div class="columnfix">
            <?php dynamic_sidebar( 'fc-2' ); ?>
            	</div>
            </div><!--end .widget-column-2-->
            <?php endif; ?> 

			<?php if (is_active_sidebar( 'fc-3' ) ) : ?>    
            <div class="cols-3 widget-column-3">  
            	<div class="columnfix">
            <?php dynamic_sidebar( 'fc-3' ); ?>
            	</div>
            </div><!--end .widget-column-3-->
			<?php endif; ?> 
           <div class="clear"></div>
        </div>
         <!--end .container-->
        <div class="copyright-wrapper">
        <div class="container">
            	<div class="copyright-txt"><?php esc_html_e(date('Y'));?> <?php bloginfo('name'); ?>. <?php esc_html_e('All Rights Reserved', 'skt-charity');?></div>
                <div class="design-by"><?php echo esc_html('SKT Charity');?></div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
<?php wp_footer(); ?>
</body>
</html>