  <!--ACCIONES DE IMPACTO TEMPLATE -->
<div id="content">
		<div class="center">
           <!--POST START-->
			<div class="no_sidebar">
				<div class="single_post">

                   <?php if(have_posts()): ?><?php while(have_posts()): ?><?php the_post(); ?>
                    <div <?php post_class(); ?> id="post-<?php the_ID(); ?>"> 
                        
                    <!--EDIT BUTTON START-->
						<?php if ( is_user_logged_in() && is_admin() ) { ?>
                            <div class="edit_wrap">
                            	<a href="<?php echo get_edit_post_link(); ?>">
                            		<?php _e('Edit','complete'); ?>
                                </a>
                            </div>
                        <?php } ?>
    				<!--EDIT BUTTON END-->
                    
                    <!--POST START-->
                        <div class="single_post_content">
                        
                            <h1 class="accion_de_impacto_title entry-title text-center"><?php the_title(); ?></h1>
                            
                            <!--POST CONTENT START-->
                                <div class="thn_post_wrap">

									<?php the_content(); ?>
                                    
                                </div>
                                	<div style="clear:both"></div>
                                <div class="thn_post_wrap wp_link_pages">
									<?php wp_link_pages('<p class="pages"><strong>'.__('Pages:', 'complete').'</strong> ', '</p>', 'number'); ?>
                                </div>
                            <!--POST CONTENT END-->
                            
                            
                            
                            <!--POST FOOTER START-->
                                <div class="post_foot">
                                    <div class="post_meta">
										 <?php if( has_tag() ) { ?>
                                             <div class="post_tag">
                                                 <div class="tag_list">
                                                   <?php if(get_the_tag_list()) {
    													echo get_the_tag_list('<ul><li><i class="fa-tag"></i>','</li><li><i class="fa-tag"></i>','</li></ul>');
													}
													?>
                                                 </div>
                                             </div>
                                         <?php } ?>
                                    </div>
                               </div>
                           <!--POST FOOTER END-->
                            
                        </div>
                    <!--POST END-->
                    </div>
                        
            <?php endwhile ?> 
       
            <?php endif ?>
            
				<?php if (!empty ($complete['post_nextprev_id']) || is_customize_preview()) { ?>
				<!--NEXT AND PREVIOUS POSTS START--> 
					<?php if ( get_post_status ( get_the_ID() ) !== 'private' ) { ?>
							<?php get_template_part('sktframe/core','nextprev'); ?>
                    <?php } ?>
                <!--NEXT AND PREVIOUS POSTS END-->          
                <?php }?>

			</div>
</div>
		</div><!--center class END-->
	</div>