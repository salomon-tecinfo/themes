<?php
/**
 * The index page of SKT Charity Pro
 *
 * Displays the home page elements.
 *
 * @package SKT Charity Pro
 *
 * @since SKT Charity Pro 1.0
 */
global $complete;
?>
<?php get_header(); ?>
<?php if ( !is_front_page() ) { ?>
<div class="fixed_site">
  <div class="fixed_wrap fixindex">
    <?php get_template_part('templates/post','layout4'); ?>
  </div>
</div>
<?php } //is_front_page ENDS ?>
<?php get_footer(); ?>