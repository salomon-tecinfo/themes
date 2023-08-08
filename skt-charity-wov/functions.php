<?php

//[posts-style4 show="4" cat="1" excerptlength="17" readmoretext="Read More"] 
// Shortcode Post Block Style WOV
function post_style_wov_func( $atts ) {
  global $complete;
  global $authordata;
   extract( shortcode_atts( array(
    'show' => '4',
    'cat' => '',
    'excerptlength' => '17',
	'readmoretext' => 'Leer entrada',
  ), $atts ) );
  $lbposts = '<div class="post_style4_area">';
  $args = array( 'posts_per_page' => $show, 'cat' => $cat, 'post__not_in' => get_option('sticky_posts'), 'orderby' => 'date', 'order' => 'desc' );
  query_posts( $args ); 
  if ( have_posts() ) {
    $n = 1;
    while (have_posts() ) { 
      the_post();
      $post_author = "<a class='auth_meta' href=\"".get_author_posts_url( $authordata->ID, $authordata->user_nicename )."\">".get_the_author()."</a>";

      $marg_cls = ($n % 4) ? '' : 'no_margin_right';
      $marg_clr = ($n % 4) ? '' : '<div class="clear"></div>';
      $lbposts .= '<div class="post_block_style4 '.$marg_cls.'">';	  
	  if( has_post_thumbnail() ) {
		  $lbposts .= '<div class="style4-post-thumb"><span class="top-right"></span><a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_post_thumbnail( get_the_ID(), 'full').'</a><span class="bottom-left"></span></div>';
		} else {
		  $lbposts .= '<div class="style4-post-thumb"><a href="'.get_permalink().'" title="'.get_the_title().'"><img src="'.get_template_directory_uri()."/images/default-post-img.jpg".'"></a></div>';
		}	  
        $lbposts .= '<div class="style4-post-centent">
                        <div class="post_block_stylewov_meta">
                            <span><i class="fa-user-pen"></i>'.get_the_author().'</span>
                            <span><i class="fa-calendar"></i>'.get_the_date('F j, Y').'</span>
                            <span><i class="fa-th-list"></i>'.getPostCategories().'</span>
                        </div> 
                        <h3><a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></h3>	
                        <div class="postdesc"><p>'.wp_trim_words( get_the_content(), $excerptlength ).'</p></div>
                        <div class="readmore"><a href="'.get_permalink().'">'.$readmoretext.'</a></div>	
                        </div>
                    </div>'.$marg_clr.'';
        $n++;
    }
  }else{
    $lbposts .= '<p>Sorry! There are no posts.</p>';
  }
  wp_reset_query();
  $lbposts .= '</div>';
    return $lbposts;
}
add_shortcode( 'posts-style-wov', 'post_style_wov_func' ); 



//[gallery-wov-carousel]
// Photo Gallery Carousel
function carousel_gallery_wov_shortcode_func( $atts ){
  extract( shortcode_atts( array(
    'catslug' => '',    
   'show' => -1,
 ), $atts ) );

 extract( shortcode_atts( array( 'catslug' => $catslug, 'show' => $show,), $atts ) );	

$carsl = '<div class="galcarosel">';
 wp_reset_query();
  $args = array( 
            'gallerycategory'=> $catslug,
            'post_type' => 'photogallery', 
            'posts_per_page' => $show, 
            'orderby' => 'date', 
            'order' => 'desc' );

 query_posts( $args );
 //query_posts('post_type=photogallery&posts_per_page=-1&gallerycategory='.$catslug); 
 if ( have_posts() ) :
   while ( have_posts() ) : the_post();
   $caroimgSrc = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
   $carovideoUrl = get_post_meta( get_the_ID(), 'video_file_url', true);
         if ( $caroimgSrc[0]!='' ) {
     $imgUrl = $caroimgSrc[0];
   }else{
     $imgUrl = get_template_directory_uri().'/images/carousel-thumb.jpg';
   }
     $carsl .= '
      <div class="galslide wovslide">
        <a class="image-zoom" href="'.( ($carovideoUrl) ? $carovideoUrl : $caroimgSrc[0] ).'" rel="prettyPhoto[gallery]" title="'.get_the_title().'"><img src="'.$caroimgSrc[0].'" alt="'.get_the_title().'" title="'.get_the_title().'"/></a>
     </div>			  
     ';
   endwhile;
    $carsl .= '</div>';
 else:
   $carsl = ' 
   <div class="galcaroselno">Sorry, photo gallery is empty.</div>
    ';			
   endif;  
 wp_reset_query();
 return $carsl;
}
add_shortcode( 'gallery-carousel-wov', 'carousel_gallery_wov_shortcode_func' );

// Shortcode Acciones
// [acciones col="3" show="3" excerptlength="10"]
function acciones_func( $atts ) {
  extract( shortcode_atts( array(
   'col' => '4',
   'show' => '4',
   'excerptlength' => '30'
 ), $atts ) );
   extract( shortcode_atts( array( 'show' => $show,), $atts ) ); $ourtm = ''; wp_reset_query(); 

 $causes = '<div class="sectionrow">';
 $args = array( 'post_type' => 'acciones_de_impacto', 
                'posts_per_page' => $show,
                'orderby' => 'date', 
                'order' => 'desc' 
              );
 query_posts( $args );
 $n = 0;
 if ( have_posts() ) {
   while ( have_posts() ) { 
     $n++;
     the_post();
     
     //$sktcause_donation_target = esc_html( get_post_meta( get_the_ID(), 'sktcause_donation_target', true ) );

     //$categories = get_categories( array('taxonomy' => 'skt_causes_category') );
     $categories = ""; // = get_the_terms( get_the_ID(), 'skt_causes_category' );
     
     if( $col == 1 ){
       $causes .= '<div class="cols1 skt-cauese-box';
     }elseif( $col == 2 ){
       $causes .= '<div class="cols2 skt-causes-box';
     }elseif( $col == 3 ){
       $causes .= '<div class="cols3 skt-causes-box';
     }elseif( $col == 4 ){
       $causes .= '<div class="cols4 skt-causes-box';
     }
       $causes .= '">';

     $causes .= ' 
     <div class="causes-thumb">';
   
     $causes .= '			
     <a href="'.get_permalink().'" title="'.get_the_title().'">'.( (get_the_post_thumbnail( get_the_ID(), 'thumbnail') != '') ? 
get_the_post_thumbnail( get_the_ID(), 'full') : '<img src="'.get_template_directory_uri().'/images/team_thumb.jpg" />' ).'</a>';
           $causes .= '<div class="causes-info-box acciones-info-box">
       <div class="causes-content">
       <h3 class="causes-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h3>				
         <p class="causes-desc">'.wp_trim_words( get_the_content(), $excerptlength ).'</p>
       <div class="clear"></div>
         </div>';				
         $causes .= '</div></div></div>';
 }
   }
 wp_reset_query();
 $causes .= '</div>';
   return $causes;

}
add_shortcode( 'acciones', 'acciones_func' );