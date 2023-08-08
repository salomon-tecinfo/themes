<?php
/**
 * The Core Functions for SKT Charity Pro
 *
 * These core functions are the main feature of the complete.
 *
 * @package SKT Charity Pro
 * 
 * @since SKT Charity Pro 1.0
 */

//CONTENT WIDTH
function complete_content_width() {
	global $content_width;
	$full_width = is_page_template( 'page-fullwidth_template.php' );
	if ( $full_width ) {
		$content_width = 1100;
	}else {
		$content_width = 690;
	}
}
add_action( 'template_redirect', 'complete_content_width' );


//UPDATED: GET THE FIRST IMAGE
function complete_first_image() {
	if(is_404()){
		return;
	}
	global $wp_query;
/*	if( $wp_query->post_count <1){
		return;
	}*/
		global $post, $posts;
		$image_url = '';
		ob_start();
		ob_end_clean();
		if(preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches)){;
		$image_url = $matches [1] [0];
		}
	return $image_url;
}

//complete Site title
if ( ! function_exists( '_wp_render_title_tag' ) ) {
	function complete_wp_title( $title, $sep ) {
		global $paged, $page;
	
		if ( is_feed() )
			return $title;
	
		// Add the site name.
		$title .= get_bloginfo( 'name' );
		$sep ='|';
		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title = "$title $sep $site_description";
	
		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 )
			$title = "$title $sep " . sprintf( __( 'Page %s', 'complete' ), max( $paged, $page ) );
	
		return $title;
	}
	add_filter( 'wp_title', 'complete_wp_title', 10, 2 );
}


//Custom Excerpt Length
function complete_excerptlength_teaser($length) {
    return 20;
}
function complete_excerptlength_index($length) {
    return 12;
}
function complete_excerptmore($more) {
    return '...';
}

function complete_excerpt($length_callback='', $more_callback='') {
    if(function_exists($length_callback)){
        add_filter('excerpt_length', $length_callback);
    }
    if(function_exists($more_callback)){
        add_filter('excerpt_more', $more_callback);
    }
    $output = get_the_excerpt();
    $output = apply_filters('wptexturize', $output);
    $output = apply_filters('convert_chars', $output);
    $output = '<p>'.$output.'</p>';
    echo $output;
}

//hex to rgb function
function complete_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);
 
   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   return implode(",", $rgb); // returns the rgb values separated by commas
   //return $rgb; // returns an array with the rgb values
}

/*complete Color Sanitization*/
function complete_sanitize_hex( $color = '#FFFFFF', $hash = true ) {
		$color = trim( $color );
		$color = str_replace( '#', '', $color );
		if ( 3 == strlen( $color ) ) {
			$color = substr( $color, 0, 1 ) . substr( $color, 0, 1 ) . substr( $color, 1, 1 ) . substr( $color, 1, 1 ) . substr( $color, 2, 1 ) . substr( $color, 2, 1 );
		}

		$substr = array();
		for ( $i = 0; $i <= 5; $i++ ) {
			$default    = ( 0 == $i ) ? 'F' : ( $substr[$i-1] );
			$substr[$i] = substr( $color, $i, 1 );
			$substr[$i] = ( false === $substr[$i] || ! ctype_xdigit( $substr[$i] ) ) ? $default : $substr[$i];
		}
		$hex = implode( '', $substr );

		return ( ! $hash ) ? $hex : '#' . $hex;

}

// allow script & iframe tag within posts
function complete_allow_html( $allowedposttags ){
	global $allowedposttags;
    $allowedposttags['script'] = array(
        'type' => true,
        'src' => true,
        'height' => true,
        'width' => true,
    );
    $allowedposttags['form'] = array(
        'id' => true,
        'class' => true,
        'action' => true,
        'method' => true,
        'name' => true,
        'style' => true,
        'target' => true,
		'novalidate' => true,
    );
    $allowedposttags['input'] = array(
        'id' => true,
        'class' => true,
        'name' => true,
        'style' => true,
        'placeholder' => true,
		'tabindex' => true,
		'type' => true,
		'value' => true,
    );
    $allowedposttags['button'] = array(
        'id' => true,
        'class' => true,
        'name' => true,
        'style' => true,
		'tabindex' => true,
		'type' => true,
		'value' => true,
    );
	

    return $allowedposttags;
}
add_filter('wp_kses_allowed_html','complete_allow_html', 1);

//**Return an ID of an attachment by searching the database with the file URL (Inexpensive query)**//
function complete_attachment_id_by_url( $url ) {
	$parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

	$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
	$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

	if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
		return;
	}

	global $wpdb;
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );
	return $attachment[0];
}

//Get Image alt from image src
function complete_image_alt( $attachment ) {
	$imgid = complete_attachment_id_by_url($attachment);
	
	if($imgid){
		$imgaltraw = wp_prepare_attachment_for_js($imgid); 
		$imgalt = $imgaltraw['alt'];
		if(!empty($imgalt)){ $imgalt = 'alt="'.$imgaltraw['alt'].'"'; }
		
	}else{
		$imgalt = '';
	}
	
	return $imgalt;
}

// custom post type for Testimonials
function my_custom_post_testimonials() {
	$labels = array(
		'name'               => __( 'Testimonials','complete'),
		'singular_name'      => __( 'Testimonials','complete'),
		'add_new'            => __( 'Add Testimonials','complete'),
		'add_new_item'       => __( 'Add New Testimonial','complete'),
		'edit_item'          => __( 'Edit Testimonial','complete'),
		'new_item'           => __( 'New Testimonial','complete'),
		'all_items'          => __( 'All Testimonials','complete'),
		'view_item'          => __( 'View Testimonial','complete'),
		'search_items'       => __( 'Search Testimonial','complete'),
		'not_found'          => __( 'No Testimonial found','complete'),
		'not_found_in_trash' => __( 'No Testimonial found in the Trash','complete'), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Testimonials'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'Manage Testimonials',
		'public'        => true,
		'menu_icon'		=> 'dashicons-format-quote',
		'menu_position' => null,
		'supports'      => array( 'title', 'editor', 'thumbnail'),
		'has_archive'   => true,
		'exclude_from_search' => true,
	);
	register_post_type( 'testimonials', $args );	
}
add_action( 'init', 'my_custom_post_testimonials' );


// add meta box to testimonials
add_action( 'admin_init', 'my_testimonial_admin_function' );
function my_testimonial_admin_function() {
    add_meta_box( 'testimonial_meta_box',
        'Testimonial Info',
        'display_testimonial_meta_box',
        'testimonials', 'normal', 'high'
    );
}
// add meta box form to doctor
function display_testimonial_meta_box( $testimonial ) {
    // Retrieve current name of the Director and Movie Rating based on review ID
	$companyname = esc_html( get_post_meta( $testimonial->ID, 'companyname', true ) );  
    $possition = esc_html( get_post_meta( $testimonial->ID, 'possition', true ) ); 
	
    ?>
    <table width="100%">
        <tr>
            <td width="20%">Company Name </td>
            <td width="80%"><input size="80" type="text" name="companyname" value="<?php echo $companyname; ?>" /></td>
        </tr> 
        <tr>
            <td width="20%">Designation </td>
            <td width="80%"><input size="80" type="text" name="possition" value="<?php echo $possition; ?>" /></td>
        </tr>       
    </table>
    <?php    
}
// save testimonial meta box form data
add_action( 'save_post', 'add_testimonial_fields_function', 10, 2 );
function add_testimonial_fields_function( $testimonial_id, $testimonial ) {
    // Check post type for testimonials
    if ( $testimonial->post_type == 'testimonials' ) {
        // Store data in post meta table if present in post data
		if ( isset($_POST['companyname']) ) {
            update_post_meta( $testimonial_id, 'companyname', $_POST['companyname'] );
        } 
        if ( isset($_POST['possition']) ) {
            update_post_meta( $testimonial_id, 'possition', $_POST['possition'] );
        }       
    }
}

//[testimonials-rotator show=""]
//Testimonials function
function testimonials_rotator_output_func( $atts ){
   extract( shortcode_atts( array(
		'show' => '',
	), $atts ) );
	  extract( shortcode_atts( array( 'show' => $show,), $atts ) );	
	
	$testimonialoutput = '<div class="bxmain rota">
  <ul class="bxslider" style="display: none;">';
	wp_reset_query();
	 $args = array( 'post_type' => 'testimonials', 'posts_per_page' => $show, 'orderby' => 'date', 'order' => 'desc' );
	query_posts( $args );
	if ( have_posts() ) :
		while ( have_posts() ) : the_post();
		$companyname = esc_html( get_post_meta( get_the_ID(), 'companyname', true ) );
		$possition = esc_html( get_post_meta( get_the_ID(), 'possition', true ) );
			$testimonialoutput .= '
		   <li>
				<div class="testimonial-image">'.get_the_post_thumbnail( get_the_ID(), array(80,80) ).'</div>
				<div class="testimonilacontent">'.get_the_content().'</div>	
				<div class="testimonial-title"><h3>'.get_the_title().' <small>'.$companyname.' '.$possition.'</small></h3></div>
			</li>			  
			';
		endwhile;
		 $testimonialoutput .= '</ul></div>';
	  endif;  
	wp_reset_query();
	return $testimonialoutput;
}
add_shortcode( 'testimonials-rotator', 'testimonials_rotator_output_func' );

// Testimonial Box
// [testimonials-box col="3" show="3"]

function testimonials_box_func( $atts ) {
   extract( shortcode_atts( array(
		'col' => '3',
		'show' => '3',
	), $atts ) );
	  extract( shortcode_atts( array( 'show' => $show,), $atts ) ); $tstmnl = ''; wp_reset_query(); 

	$tstmnl = '<div class="testimonialrow">';
	$args = array( 'post_type' => 'testimonials', 'posts_per_page' => $show, 'orderby' => 'date', 'order' => 'desc' );
	query_posts( $args );
	$n = 0;
	if ( have_posts() ) {
		while ( have_posts() ) { 
			$n++;
			the_post();
			$companyname = esc_html( get_post_meta( get_the_ID(), 'companyname', true ) );
			$possition = esc_html( get_post_meta( get_the_ID(), 'possition', true ) );
 			
			if( $col == 1 ){
				$tstmnl .= '<div class="tstcols1';
			}elseif( $col == 2 ){
				$tstmnl .= '<div class="tstcols2';
			}elseif( $col == 3 ){
				$tstmnl .= '<div class="tstcols3';
			}elseif( $col == 4 ){
				$tstmnl .= '<div class="tstcols4';
			}
				$tstmnl .= '">';
				
                $tstmnl .= '<div class="testimonial-box"> 
					 <em>'.get_the_content().'</em>
                     </div>
                     <div class="testimonial-inforarea">
                     	<i class="fa fa-user"></i>
<h3>'.get_the_title().',</h3>('.$companyname.','.$possition.')
                     </div>
				';
                $tstmnl .= '</div>
				';
		}
	}else{
		$tstmnl .= '
				<div class="tstcols3"> 
					 <div class="testimonial-box">
						<em>Sed suscipit mauris nec mauris vulputate, a posuere libero ongue. Nam laoreet elit eu erat pulvinar, et efficitur nibh imod. Proin venenatis orci sit amet nisl finibus vehicula. Nam metus lorem, hendrerit quis ante eget lobortis eleneque. Aliquam in ullamcorper quam. Integer euismod ligula in mauris vehicula imperdiet.</em>
					 </div>
					 <div class="testimonial-inforarea">
						<i class="fa fa-user"></i><h3>John,</h3>(Company Name, CEO)
					 </div>
				</div>
				<div class="tstcols3"> 
					 <div class="testimonial-box">
						<em>Sed suscipit mauris nec mauris vulputate, a posuere libero ongue. Nam laoreet elit eu erat pulvinar, et efficitur nibh imod. Proin venenatis orci sit amet nisl finibus vehicula. Nam metus lorem, hendrerit quis ante eget lobortis eleneque. Aliquam in ullamcorper quam. Integer euismod ligula in mauris vehicula imperdiet.</em>
					 </div>
					 <div class="testimonial-inforarea">
						<i class="fa fa-user"></i><h3>Stefen,</h3>(Company Name, Sr.Manager)
					 </div>
				</div>
				<div class="tstcols3"> 
					 <div class="testimonial-box">
						<em>Sed suscipit mauris nec mauris vulputate, a posuere libero ongue. Nam laoreet elit eu erat pulvinar, et efficitur nibh imod. Proin venenatis orci sit amet nisl finibus vehicula. Nam metus lorem, hendrerit quis ante eget lobortis eleneque. Aliquam in ullamcorper quam. Integer euismod ligula in mauris vehicula imperdiet.</em>
					 </div>
					 <div class="testimonial-inforarea">
						<i class="fa fa-user"></i><h3>Sara,</h3>(Company Name, Developer)
					 </div>
				</div>								
				
		';
	}
	wp_reset_query();
	$tstmnl .= '</div>';
    return $tstmnl;
}
add_shortcode( 'testimonials-box', 'testimonials_box_func' );
//


//custom post type for Our Team
function my_custom_post_team() {
	$labels = array(
		'name'               => __( 'Our Team', 'complete' ),
		'singular_name'      => __( 'Our Team', 'complete' ),
		'add_new'            => __( 'Add New', 'complete' ),
		'add_new_item'       => __( 'Add New Team Member', 'complete' ),
		'edit_item'          => __( 'Edit Team Member', 'complete' ),
		'new_item'           => __( 'New Member', 'complete' ),
		'all_items'          => __( 'All Members', 'complete' ),
		'view_item'          => __( 'View Members', 'complete' ),
		'search_items'       => __( 'Search Team Members', 'complete' ),
		'not_found'          => __( 'No Team members found', 'complete' ),
		'not_found_in_trash' => __( 'No Team members found in the Trash', 'complete' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Our Team'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'Manage Team',
		'public'        => true,
		'menu_position' => null,
		'menu_icon'		=> 'dashicons-groups',
		'supports'      => array( 'title', 'editor', 'thumbnail' ),
		'rewrite' => array('slug' => 'our-team'),
		'has_archive'   => true,
		'exclude_from_search' => true,
	);
	register_post_type( 'team', $args );
}
add_action( 'init', 'my_custom_post_team' );

// add meta box to team
add_action( 'admin_init', 'my_team_admin_function' );
function my_team_admin_function() {
    add_meta_box( 'team_meta_box',
        'Member Info',
        'display_team_meta_box',
        'team', 'normal', 'high'
    );
}
// add meta box form to team
function display_team_meta_box( $team ) {
    // Retrieve current name of the Director and Movie Rating based on review ID
    $designation = esc_html( get_post_meta( $team->ID, 'designation', true ) );
	
    $facebook = get_post_meta( $team->ID, 'facebook', true );
	$facebooklink = esc_url( get_post_meta( $team->ID, 'facebooklink', true ) );

    $twitter = get_post_meta( $team->ID, 'twitter', true );
	$twitterlink = esc_url( get_post_meta( $team->ID, 'twitterlink', true ) );

	$googleplus = get_post_meta( $team->ID, 'googleplus', true );
	$googlepluslink = esc_url( get_post_meta( $team->ID, 'googlepluslink', true ) );	

    $linkedin = get_post_meta( $team->ID, 'linkedin', true );
	$linkedinlink = esc_url( get_post_meta( $team->ID, 'linkedinlink', true ) );

    $pinterest = get_post_meta( $team->ID, 'pinterest', true );
	$pinterestlink = get_post_meta( $team->ID, 'pinterestlink', true );
    ?>
    <table width="100%">
        <tr>
            <td width="20%">Designation </td>
            <td width="80%"><input type="text" name="designation" value="<?php echo $designation; ?>" /></td>
        </tr>
        <tr>
        	<td width="20%">&nbsp;</td>
            <td width="40%" style="padding:10px 0 5px 0;"><strong>Icon Name Eg: facebook</strong></td>
            <td width="40%" style="padding:10px 0 5px 0;"><strong>Social Link Eg: http://www.facebook.com/xyz</strong></td>
        </tr>        
        <tr>
            <td width="20%">Social link 1</td>
            <td width="40%"><input type="text" name="facebook" value="<?php echo $facebook; ?>" /></td>
            <td width="40%"><input style="width:500px;" type="text" name="facebooklink" value="<?php echo $facebooklink; ?>" /></td>
        </tr>
        <tr>
            <td width="20%">Social Link 2</td>
            <td width="40%"><input type="text" name="twitter" value="<?php echo $twitter; ?>" /></td>
            <td width="40%"><input style="width:500px;" type="text" name="twitterlink" value="<?php echo $twitterlink; ?>" /></td>
        </tr>
        <tr>
            <td width="20%">Social Link 3</td>
            <td width="40%"><input type="text" name="googleplus" value="<?php echo $googleplus; ?>" /></td>
            <td width="40%"><input style="width:500px;" type="text" name="googlepluslink" value="<?php echo $googlepluslink; ?>" /></td>
        </tr>
        <tr>
            <td width="20%">Social Link 4</td>
            <td width="40%"><input type="text" name="linkedin" value="<?php echo $linkedin; ?>" /></td>
            <td width="40%"><input style="width:500px;" type="text" name="linkedinlink" value="<?php echo $linkedinlink; ?>" /></td>
        </tr>        
        <tr>
            <td width="20%">Social Link 5</td>
            <td width="40%"><input type="text" name="pinterest" value="<?php echo $pinterest; ?>" /></td>
            <td width="40%"><input style="width:500px;" type="text" name="pinterestlink" value="<?php echo $pinterestlink; ?>" /></td>
        </tr>
        <tr>
        	<td width="100%" colspan="3"><label style="font-size:12px;"><strong>Note:</strong> Icon name should be in lowercase without space. More social icons can be found at: http://fortawesome.github.io/Font-Awesome/icons/</label> </td>
        </tr>
    </table>
    <?php    
}
// save team meta box form data
add_action( 'save_post', 'add_team_fields_function', 10, 2 );
function add_team_fields_function( $team_id, $team ) {
    // Check post type for testimonials
    if ( $team->post_type == 'team' ) {
        // Store data in post meta table if present in post data
        if ( isset($_POST['designation']) ) {
            update_post_meta( $team_id, 'designation', $_POST['designation'] );
        }
        if ( isset($_POST['facebook']) ) {
            update_post_meta( $team_id, 'facebook', $_POST['facebook'] );
        }
		if ( isset($_POST['facebooklink']) ) {
            update_post_meta( $team_id, 'facebooklink', $_POST['facebooklink'] );
        }
        if ( isset($_POST['twitter']) ) {
            update_post_meta( $team_id, 'twitter', $_POST['twitter'] );
        }
		if ( isset($_POST['twitterlink']) ) {
            update_post_meta( $team_id, 'twitterlink', $_POST['twitterlink'] );
        }
        if ( isset($_POST['googleplus']) ) {
            update_post_meta( $team_id, 'googleplus', $_POST['googleplus'] );
        }
		if ( isset($_POST['googlepluslink']) ) {
            update_post_meta( $team_id, 'googlepluslink', $_POST['googlepluslink'] );
        }		
        if ( isset($_POST['linkedin']) ) {
            update_post_meta( $team_id, 'linkedin', $_POST['linkedin'] );
        }
		if ( isset($_POST['linkedinlink']) ) {
            update_post_meta( $team_id, 'linkedinlink', $_POST['linkedinlink'] );
        }
		if ( isset($_POST['pinterest']) ) {
            update_post_meta( $team_id, 'pinterest', $_POST['pinterest'] );
        }
		if ( isset($_POST['pinterestlink']) ) {
            update_post_meta( $team_id, 'pinterestlink', $_POST['pinterestlink'] );
        }
    }
}

// Shortcode Our Team
// [ourteam col="4" show="4"]
function ourteam_func( $atts ) {
   extract( shortcode_atts( array(
		'col' => '4',
		'show' => '4',
		'excerptlength' => '25',
	), $atts ) );
	  extract( shortcode_atts( array( 'show' => $show,), $atts ) ); $ourtm = ''; wp_reset_query(); 

	$ourtm = '<div class="sectionrow">';
	$args = array( 'post_type' => 'team', 'posts_per_page' => $show, 'post__not_in' => get_option('sticky_posts'), 'orderby' => 'date', 'order' => 'desc' );
	query_posts( $args );
	$n = 0;
	if ( have_posts() ) {
		while ( have_posts() ) { 
			$n++;
			the_post();
			$designation = esc_html( get_post_meta( get_the_ID(), 'designation', true ) );
			
			$facebook = get_post_meta( get_the_ID(), 'facebook', true );
			$facebooklink = get_post_meta( get_the_ID(), 'facebooklink', true );
			
			$twitter = get_post_meta( get_the_ID(), 'twitter', true );
			$twitterlink = get_post_meta( get_the_ID(), 'twitterlink', true );
			
			$googleplus = get_post_meta( get_the_ID(), 'googleplus', true );
			$googlepluslink = get_post_meta( get_the_ID(), 'googlepluslink', true );
			
			$linkedin = get_post_meta( get_the_ID(), 'linkedin', true );
			$linkedinlink = get_post_meta( get_the_ID(), 'linkedinlink', true );
			
			$pinterest = get_post_meta( get_the_ID(), 'pinterest', true );
			$pinterestlink = get_post_meta( get_the_ID(), 'pinterestlink', true );			

			if( $col == 1 ){
				$ourtm .= '<div class="cols1 skt-team-box';
			}elseif( $col == 2 ){
				$ourtm .= '<div class="cols2 skt-team-box';
			}elseif( $col == 3 ){
				$ourtm .= '<div class="cols3 skt-team-box';
			}elseif( $col == 4 ){
				$ourtm .= '<div class="cols4 skt-team-box';
			}
			$ourtm .= '">';
			$ourtm .= ' 
			<div class="team-col">
				<div class="team-thumb"><a href="'.get_permalink().'" title="'.get_the_title().'">'.( (get_the_post_thumbnail( get_the_ID(), 'thumbnail') != '') ? get_the_post_thumbnail( get_the_ID(), 'full') : '<img src="'.get_template_directory_uri().'/images/team_thumb.jpg" />' ).'</a></div>';
                $ourtm .= '<div class="team-info-box">
                	<h3 class="team-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
					if( $designation != '' ){
                    $ourtm .= '<h4 class="team-designation">'.$designation.'</h4>';
					}
					$ourtm .= '<div class="social-icons">';
						if( $facebook != '' ){
                    	$ourtm .= '<a href="'.$facebooklink.'" title="'.$facebook.'" target="_blank"><i class="fa fa-'.$facebook.' fa-lg"></i></a>';
						}
						if( $twitter != '' ){
                    	$ourtm .= '<a href="'.$twitterlink.'" title="'.$twitter.'" target="_blank"><i class="fa fa-'.$twitter.' fa-lg"></i></a>';
						}
						if( $googleplus != '' ){
                    	$ourtm .= '<a href="'.$googlepluslink.'" title="'.$googleplus.'" target="_blank"><i class="fa fa-'.$googleplus.' fa-lg"></i></a>';
						}						
						if( $linkedin != '' ){
                    	$ourtm .= '<a href="'.$linkedinlink.'" title="'.$linkedin.'" target="_blank"><i class="fa fa-'.$linkedin.' fa-lg"></i></a>';
						}
						if( $pinterest != '' ){
                    	$ourtm .= '<a href="'.$pinterestlink.'" title="'.$pinterest.'" target="_blank"><i class="fa fa-'.$pinterest.' fa-lg"></i></a>';
						}
                $ourtm .= '</div></div></div></div>';
		}
	}
	wp_reset_query();
	$ourtm .= '</div>';
    return $ourtm;
}
add_shortcode( 'ourteam', 'ourteam_func' );

// Shortcode Services
/* [service pattern="boxpattern-1" icon="image.jpg" title="Title" go="fa-angle-right" url="#"]Description[/service] */
// add shortcode for service box
function servicebox($atts, $content = null){
		extract( shortcode_atts(array(
			'pattern' => 'pattern',
			'icon'  => 'icon',
			'title'  => 'title',
			'go'  => 'go',
			'url' => 'url',
		), $atts));
		
		return '
			 <a href="'.$url.'"><div class="servicebox '.$pattern.'">
			 	<div class="serviceboxbg">
					<img src="'.$icon.'">
					<h3>'.$title.'</h3>
					<p>'.$content.'</p>
					<div class="sktgo"><i class="fa '.$go.'" aria-hidden="true"></i>
</div>
				</div>
			 </div></a>	
		';
}
add_shortcode('service','servicebox');

//[clear]
function clear_func() {
	$clr = '<div class="clear"></div>';
	return $clr;
}
add_shortcode( 'clear', 'clear_func' );


//[space height="20"]
function space_shortcode_func($atts ) {
 extract( shortcode_atts( array(
  'height' => '20',
 ), $atts ) );
 $sptr = '<div class="spacecode" style="height:'.$height.'px;"></div>';
 return $sptr;
}
add_shortcode( 'space', 'space_shortcode_func' );


//custom post type for Our photogallery
add_action("admin_init", "admin_init");
function admin_init(){
	add_meta_box("video_file_url-meta", "Video File URL", "video_file_url", "photogallery", "normal", "low"); 
}

function video_file_url () {
	global $post;  
	$custom     = get_post_custom($post->ID);  
	$video_file_url  = isset ( $custom["video_file_url"][0] ) ? $custom["video_file_url"][0] : '';  ?> 
	<style>
	.amount_input { margin:0; padding:6px; width:80%; }
	</style>
	<table width="100%"> 
		<tr><td width="110">Video File URL : </td><td colspan="2"><input class="amount_input" type="text" name="video_file_url"  value="<?php echo $video_file_url; ?>"  /></td></tr> 
		<tr><td></td><td><strong>YouTube video url:</strong></td><td>http://www.youtube.com/watch?v=qqXi8WmQ_WM</td></tr> 
		<tr><td></td><td width="120"><strong>Vimeo video url:</strong></td><td>http://vimeo.com/8245346</td></tr> 
	</table>
	<?php
}

add_action('save_post', 'save_details');
function save_details(){
	global $post; 
	if ( isset($_POST["video_file_url"]) ) {
		update_post_meta($post->ID, "video_file_url", $_POST["video_file_url"]);
	} 
}

//custom post type for Our photogallery
function my_custom_post_photogallery() {
	$labels = array(
		'name'               => __( 'Photo Gallery','complete' ),
		'singular_name'      => __( 'Photo Gallery','complete' ),
		'add_new'            => __( 'Add New','complete' ),
		'add_new_item'       => __( 'Add New Image / Video','complete' ),
		'edit_item'          => __( 'Edit Image/Video','complete' ),
		'new_item'           => __( 'New Image/Video','complete' ),
		'all_items'          => __( 'All Images/Videos','complete' ),
		'view_item'          => __( 'View Image/Video','complete' ),
		'search_items'       => __( 'Search Images/Videos','complete' ),
		'not_found'          => __( 'No images/videos found','complete' ),
		'not_found_in_trash' => __( 'No images/videos found in the Trash','complete' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Photo Gallery'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'Manage Photo Gallery',
		'public'        => true,
		'menu_icon'		=> 'dashicons-format-image',
		'supports'      => array( 'title', 'thumbnail' ),
		'has_archive'   => true,
	);
	register_post_type( 'photogallery', $args );
}
add_action( 'init', 'my_custom_post_photogallery' );


//  register gallery taxonomy
register_taxonomy( "gallerycategory", 
	array("photogallery"), 
	array(
		"hierarchical" => true, 
		"label" => "Gallery Category", 
		"singular_label" => "Photo Gallery", 
		"rewrite" => true
	)
);

add_action("manage_posts_custom_column",  "photogallery_custom_columns");
add_filter("manage_edit-photogallery_columns", "photogallery_edit_columns");
function photogallery_edit_columns($columns){
	$columns = array(
		"cb" => '<input type="checkbox" />',
		"title" => "Gallery Title",
		"pcategory" => "Gallery Category",
		"view" => "Image",
		"date" => "Date",
	);
	return $columns;
}
function photogallery_custom_columns($column){
	global $post;
	switch ($column) {
		case "pcategory":
			echo get_the_term_list($post->ID, 'gallerycategory', '', ', ','');
		break;
		case "view":
			the_post_thumbnail('thumbnail');
		break;
		case "date":

		break;
	}
}

//[photogallery filter="true"]
function photogallery_shortcode_func( $atts ) {
	extract( shortcode_atts( array(
		'show' => -1,
		'filter' => 'true'
	), $atts ) );
	$pfStr = '';

	$pfStr .= '<div class="portfolio-content">';
	if( $filter == 'true' ){
		$pfStr .= '<ul class="portfolio-categ filter"><li class="all active"><a href="#">'.esc_html__('ALL').'</a></li>';
		$categories = get_categories( array('taxonomy' => 'gallerycategory') );
		foreach ($categories as $category) {
			$pfStr .= '<li class="cat-item-'.$category->slug.'"><a href="#" title="'.$category->name.'">'.$category->name.'</a></li>';
		}
		$pfStr .= '</ul>';
	}

	$pfStr .= '<ul class="portfolio-area">';
	$j=0;
	query_posts('post_type=photogallery&posts_per_page='.$show); 
	if ( have_posts() ) : while ( have_posts() ) : the_post(); 
	$j++;
		$videoUrl = get_post_meta( get_the_ID(), 'video_file_url', true);
		$imgSrc = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
		$terms = wp_get_post_terms( get_the_ID(), 'gallerycategory', array("fields" => "all"));
		$slugAr = array();
		foreach( $terms as $tv ){
			$slugAr[] = $tv->slug;
		}
		if ( $imgSrc[0]!='' ) {
			$imgUrl = $imgSrc[0];
		}else{
			$imgUrl = get_template_directory_uri().'/images/gallery_thumb.jpg';
		}
		$pfStr .= '<li data-id="id-'.$j.'" data-type="cat-item-'.implode(' ', $slugAr).'" class="portfolio-item2">
 <a class="image-zoom" href="'.( ($videoUrl) ? $videoUrl : $imgSrc[0] ).'" rel="prettyPhoto[gallery]" title="'.get_the_title().'"><div class="image-block"><h4 class="image-block-title">'.get_the_title().'</h4><img src="'.$imgSrc[0].'" alt="'.get_the_title().'" title="'.get_the_title().'"/></div></a>
            </li>
			';
		unset( $slugAr );
	endwhile; else: 
		$pfStr .= '<p>Sorry, photo gallery is empty.</p>';
	endif; 
	wp_reset_query();
	$pfStr .= '</ul>';
	$pfStr .= '<div class="clear"></div></div>';
	return $pfStr;
}
add_shortcode( 'photogallery', 'photogallery_shortcode_func' );

/// Gallery By Category Id

//[gallery catslug="cat-slug"]
function gallery_shortcode_func( $atts ) {
	extract( shortcode_atts( array(
		'catslug' => '',
	), $atts ) );
	$pfStr = '';

	$pfStr .= '<div class="portfolio-content">';
	$pfStr .= '<ul class="portfolio-area">';
	$j=0;
	query_posts('post_type=photogallery&posts_per_page=-1&gallerycategory='.$catslug); 
	if ( have_posts() ) : while ( have_posts() ) : the_post(); 
	$j++;
		$videoUrl = get_post_meta( get_the_ID(), 'video_file_url', true);
		$imgSrc = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
		$terms = wp_get_post_terms( get_the_ID(), 'gallerycategory', array("fields" => "all"));
		$slugAr = array();
		foreach( $terms as $tv ){
			$slugAr[] = $tv->slug;
		}
		if ( $imgSrc[0]!='' ) {
			$imgUrl = $imgSrc[0];
		}else{
			$imgUrl = get_template_directory_uri().'/images/gallery_thumb.jpg';
		}
		$pfStr .= '<li data-id="id-'.$j.'" data-type="cat-item-'.implode(' ', $slugAr).'" class="portfolio-item2">
 <a class="image-zoom" href="'.( ($videoUrl) ? $videoUrl : $imgSrc[0] ).'" rel="prettyPhoto[gallery]" title="'.get_the_title().'"><div><span class="image-block"><img src="'.$imgSrc[0].'" alt="'.get_the_title().'" title="'.get_the_title().'"/></span></div></a>
            </li>
			';
		unset( $slugAr );
	endwhile; else: 
		$pfStr .= '<p>Sorry, photo gallery is empty.</p>';
	endif; 
	wp_reset_query();
	$pfStr .= '</ul>';
	$pfStr .= '<div class="clear"></div></div>';
	return $pfStr;
}
add_shortcode( 'gallery', 'gallery_shortcode_func' );
/// Gallery By Category Id




//[gallery-carousel]
// Photo Gallery Carousel
function carousel_gallery_shortcode_func( $atts ){
   extract( shortcode_atts( array(
		'show' => -1,
	), $atts ) );
	  extract( shortcode_atts( array( 'show' => $show,), $atts ) );	
	
$carsl = '<div class="galcarosel">';
	wp_reset_query();
	 $args = array( 'post_type' => 'photogallery', 'posts_per_page' => $show, 'orderby' => 'date', 'order' => 'desc' );
	query_posts( $args );
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
		   <div class="galslide">
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
add_shortcode( 'gallery-carousel', 'carousel_gallery_shortcode_func' );

//[posts-style1 show="4" cat="1" excerptlength="24"] 
// Shortcode Post Block Style1

function post_style1_func( $atts ) {
	global $complete;
	global $authordata;
   extract( shortcode_atts( array(
		'show' => '4',
		'cat' => '1',
		'excerptlength' => '24',
	), $atts ) );

	$lbposts = '<div class="post_style1_area">';
	$args = array( 'posts_per_page' => $show, 'cat' => $cat, 'post__not_in' => get_option('sticky_posts'), 'orderby' => 'date', 'order' => 'desc' );
	query_posts( $args );
	
	if ( have_posts() ) {
		$n = 1;
		while ( have_posts() ) { 
			the_post();
			$marg_cls = ($n % 4) ? '' : 'no_margin_right';
			$marg_clr = ($n % 4) ? '' : '<div class="clear"></div>';
			$lbposts .= '<div class="post_block_style1 '.$marg_cls.'">'; 
			if ( has_post_thumbnail() ){ $lbposts .= '<div class="style1-post-thumb">'; }
			$lbposts .= '<a href="'.get_permalink().'" title="'.get_the_title().'">'.( (get_the_post_thumbnail( get_the_ID(), 'thumbnail') != '') ? get_the_post_thumbnail( get_the_ID(), 'full') : '' ).'</a>';
			if ( has_post_thumbnail() ){
			$lbposts .= '</div>';
			}
				$lbposts .= '<h3><a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></h3>
				<div class="post_block_style1_meta">
                	<span><a href="'.get_author_posts_url( $authordata->ID, $authordata->user_nicename ).'"><i class="fa fa-user fa-lg"></i> '.get_the_author().'</a></span><span><i class="fa fa-calendar"></i>
'.get_the_date('F j, Y').'</span>
                </div>
				<p>'.wp_trim_words( get_the_content(), $excerptlength ).'</p>
				<a class="sktmore" href="'.get_permalink().'">'.$complete['recentpost_block_button'].'</a> 
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
add_shortcode( 'posts-style1', 'post_style1_func' );

//[posts-style2 show="2" cat="1" excerptlength="24"] 
// Shortcode Post Block Style2

function post_style2_func( $atts ) {
	global $complete;
	global $authordata;
   extract( shortcode_atts( array(
		'show' => '2',
		'cat' => '1',
		'excerptlength' => '24',
	), $atts ) );

	$lbposts = '<div class="post_style2_area">';
	$args = array( 'posts_per_page' => $show, 'cat' => $cat, 'post__not_in' => get_option('sticky_posts'), 'orderby' => 'date', 'order' => 'desc' );
	query_posts( $args );
	
	if ( have_posts() ) {
		$n = 1;
		while ( have_posts() ) { 
			the_post();
			$marg_cls = ($n % 2) ? '' : 'no_margin_right';
			$marg_clr = ($n % 2) ? '' : '<div class="clear"></div>';
			$lbposts .= '<div class="post_block_style2 '.$marg_cls.'">'; 
						if ( has_post_thumbnail() ){
			$lbposts .= '<div class="style2-post-thumb">';
			}$lbposts .= '<a href="'.get_permalink().'" title="'.get_the_title().'">'.( (get_the_post_thumbnail( get_the_ID(), 'thumbnail') != '') ? get_the_post_thumbnail( get_the_ID(), 'full') : '' ).'</a>'; if ( has_post_thumbnail() ){ $lbposts .= '</div>'; } $lbposts .= '
				<h3><a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></h3>
				<div class="post_block_style2_meta">
                	<span><a href="'.get_author_posts_url( $authordata->ID, $authordata->user_nicename ).'"><i class="fa fa-user fa-lg"></i> '.get_the_author().'</a></span><span><i class="fa fa-calendar"></i>
'.get_the_date('F j, Y').'</span>
                </div>
				<p>'.wp_trim_words( get_the_content(), $excerptlength ).'</p>
				<a class="sktmore" href="'.get_permalink().'">'.$complete['recentpost_block_button'].'</a> 
				</div>'.$marg_clr.'';
				$n++;
		}
	}else{
		$lbposts .= '<p>Sorry! There are no post.</p>';
	}
	wp_reset_query();
	$lbposts .= '</div>';
    return $lbposts;
}
add_shortcode( 'posts-style2', 'post_style2_func' );



// Post Style 3
//[posts-style3 show="12" cat="1" excerptlength="24"] 
// Shortcode Post Block Style3

function post_style3_func( $atts ) {
	global $complete;
	global $authordata;
   extract( shortcode_atts( array(
		'show' => '2',
		'cat' => '1',
		'excerptlength' => '24',
	), $atts ) );

	$lbposts = '<div class="post_style3_area">';
	$args = array( 'posts_per_page' => $show, 'cat' => $cat, 'post__not_in' => get_option('sticky_posts'), 'orderby' => 'date', 'order' => 'desc' );
	query_posts( $args );
	
	if ( have_posts() ) {
		$n = 1;
		while ( have_posts() ) { 
			the_post();
			$marg_cls = ($n % 2) ? '' : 'no_margin_right';
			$marg_clr = ($n % 2) ? '' : '<div class="clear"></div>';
			$lbposts .= '<div class="post_block_style3 '.$marg_cls.'">'; 
			
			if(has_post_thumbnail() ){
			$lbposts .= '<div class="style3thumb"><a href="'.get_permalink().'" title="'.get_the_title().'">'.( (get_the_post_thumbnail( get_the_ID(), 'thumbnail') != '') ? get_the_post_thumbnail( get_the_ID(), 'full') : '' ).'</a></div>'; 
			}
					if(has_post_thumbnail() ){
					$lbposts .= '<div class="style3info">'; 
					}
					else{
					$lbposts .= '<div class="style3infonothumb">'; 	
					}
					$lbposts .= '<h3>'.get_the_title().'</h3>'; 
					$lbposts .= '<div class="shortdesc">'.wp_trim_words( get_the_content(), $excerptlength ).'</div>';
					$lbposts .= '<div class="shortmore"><a href="'.get_permalink().'">'.$complete['recentpost_block_button'].'</a></div>';
					$lbposts .= '</div></div>'.$marg_clr.'';
				$n++;
		}
	}else{
		$lbposts .= '<p>Sorry! There are no post.</p>';
	}
	wp_reset_query();
	$lbposts .= '</div>';
    return $lbposts;
}
add_shortcode( 'posts-style3', 'post_style3_func' );
// Post Style 3

// get post categories function
function getPostCategories(){
	$categories = get_the_category();
	$catOut = '';
	$separator = ', ';
	$catOutput = '';
	if($categories){
		foreach($categories as $category) {
			$catOutput .= '<a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s", 'skt-charm' ), $category->name ) ) . '">'.$category->cat_name.'</a>'.$separator;
		}
		$catOut = ''.trim($catOutput, $separator);
	}
	return $catOut;
}

//[posts-style4 show="4" cat="1" excerptlength="17" readmoretext="Read More"] 
// Shortcode Post Block Style4
function post_style4_func( $atts ) {
  global $complete;
  global $authordata;
   extract( shortcode_atts( array(
    'show' => '4',
    'cat' => '',
    'excerptlength' => '17',
	'readmoretext' => 'Read More',
  ), $atts ) );
  $lbposts = '<div class="post_style4_area">';
  $args = array( 'posts_per_page' => $show, 'cat' => $cat, 'post__not_in' => get_option('sticky_posts'), 'orderby' => 'date', 'order' => 'desc' );
  query_posts( $args ); 
  if ( have_posts() ) {
    $n = 1;
    while (have_posts() ) { 
      the_post();
      $marg_cls = ($n % 4) ? '' : 'no_margin_right';
      $marg_clr = ($n % 4) ? '' : '<div class="clear"></div>';
      $lbposts .= '<div class="post_block_style4 '.$marg_cls.'">';	  
	  if( has_post_thumbnail() ) {
		  $lbposts .= '<div class="style4-post-thumb"><span class="top-right"></span><a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_post_thumbnail( get_the_ID(), 'full').'</a><span class="bottom-left"></span></div>';
		} else {
		  $lbposts .= '<div class="style4-post-thumb"><a href="'.get_permalink().'" title="'.get_the_title().'"><img src="'.get_template_directory_uri()."/images/default-post-img.jpg".'"></a></div>';
		}	  
        $lbposts .= '<div class="style4-post-centent">
				<div class="post_block_style4_meta"><span>'.get_the_date('F j, Y').'</span><span>'.getPostCategories().'</span></div> 
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
add_shortcode( 'posts-style4', 'post_style4_func' ); 

//[posts-timeline show="4" cat="1" excerptlength="24"] 
// Shortcode Post Time Line

function post_timeline_func( $atts ) {
	global $complete;
	global $authordata;
   extract( shortcode_atts( array(
   		'show' => '4',
		'cat' => '1',
		'excerptlength' => '24',
	), $atts ) );

	$tmlposts = '<div class="timeline-container">
  <div class="timeline-row">
    <ul class="timeline-both-side">';
	$args = array( 'posts_per_page' => $show, 'cat' => $cat, 'post__not_in' => get_option('sticky_posts'), 'orderby' => 'date', 'order' => 'desc' );
	query_posts( $args );
	
	if ( have_posts() ) {
		$n = 1;
		while ( have_posts() ) { 
			the_post();
			$marg_cls = ($n % 2) ? '' : 'opposite-side';
			$tmlposts .= '<li class="'.$marg_cls.'">'; 
			$tmlposts .= '<div class="border-line"></div><div class="timeline-description">
			<div class="timeleft"><a href="'.get_permalink().'" title="'.get_the_title().'">'.( (get_the_post_thumbnail( get_the_ID(), 'thumbnail') != '') ? get_the_post_thumbnail( get_the_ID(), 'full') : '' ).'</a></div>'; 
			if ( has_post_thumbnail() ){$tmlposts .= '<div class="timeright">'; }else {$tmlposts .= '<div class="timerightfull">';}
			$tmlposts .= '<h3><a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></h3><div class="post_block_style1_meta">
                	<span><a href="'.get_author_posts_url( $authordata->ID, $authordata->user_nicename ).'"><i class="fa fa-user fa-lg"></i> '.get_the_author().'</a></span><span><i class="fa fa-calendar"></i>
'.get_the_date('F j, Y').'</span>
                </div><p>'.wp_trim_words( get_the_content(), $excerptlength ).'</p><a class="sktmore" href="'.get_permalink().'">'.$complete['recentpost_block_button'].'</a> </div>
			</div></li> ';
				$n++;
		}
	}else{
		$tmlposts .= '<p>Sorry! There are no posts.</p>';
	}
	wp_reset_query();
	$tmlposts .= '</ul>
  </div>
</div>';
    return $tmlposts;
}
add_shortcode( 'posts-timeline', 'post_timeline_func' );

//[posts-grid show="4" cat="1" excerptlength="24"] 
// Shortcode Post Grid

function post_grid_func( $atts ) {
	global $complete;
	global $authordata;
   extract( shortcode_atts( array(
   		'show' => '4',
		'cat' => '1',   
		'excerptlength' => '24',
	), $atts ) );
	
	$gridposts = '<div class="gridwrapper">
<div class="masonry">';
	$args = array( 'posts_per_page' => $show, 'cat' => $cat, 'post__not_in' => get_option('sticky_posts'), 'orderby' => 'date', 'order' => 'desc' );
	query_posts( $args );
	
	if ( have_posts() ) {
		$n = 1;
		while ( have_posts() ) { 
			the_post();
			$gridposts .= '<div class="griditem"><a href="'.get_permalink().'" title="'.get_the_title().'">'.( (get_the_post_thumbnail( get_the_ID(), 'thumbnail') != '') ? get_the_post_thumbnail( get_the_ID(), 'full') : '' ).'</a>
<h3><a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></h3>
<div class="post_block_style1_meta">
                	<span><a href="'.get_author_posts_url( $authordata->ID, $authordata->user_nicename ).'"><i class="fa fa-user fa-lg"></i> '.get_the_author().'</a></span><span><i class="fa fa-calendar"></i>
'.get_the_date('F j, Y').'</span>
                </div><p>'.wp_trim_words( get_the_content(), $excerptlength ).'</p><a class="sktmore" href="'.get_permalink().'">'.$complete['recentpost_block_button'].'</a></div>'; 
				$n++;
		}
	}else{
		$gridposts .= '<p>Sorry! There are no posts.</p>';
	}
	wp_reset_query();
	$gridposts .= '</div></div>';
    return $gridposts;
}
add_shortcode( 'posts-grid', 'post_grid_func' );

// [skill title="HTML" percent="80" bgcolor="#ff7400"]
// add shortcode for skills
function skills_func($skill_var){
	extract( shortcode_atts(array(
		'title' 	=> 'title',
		'percent'	=> 'percent',
		'bgcolor'	=> 'bgcolor',
	), $skill_var));
	
	return '<div class="skillbar clearfix " data-percent="'.$percent.'%">
			<div class="skillbar-title"><span>'.$title.'</span>'.$percent.'%</div>
			<div class="skill-bg"><div class="skillbar-bar" style="background:'.$bgcolor.'"></div></div>
			</div>';
}

add_shortcode('skill','skills_func');

// Shortcode Client
/*[client url="#" image="image"]*/
function clientbox($atts){
		extract( shortcode_atts(array(
			'url' => 'url',
			'image' => 'image'
		), $atts));
		
		return '
                <div class="clientbox">
                    <a href="'.$url.'" target="_blank"><img src="'.$image.'"/></a>
                </div>
		';
}
add_shortcode('client','clientbox');

// Shortcode Box Thumb
/*[boxthumb name="name" url="#" image="image" target="blank"]*/
function thumbbox($atts){
		extract( shortcode_atts(array(
			'url' => 'url',
			'image' => 'image',
			'target' => 'target',
			'name' => 'name'
		), $atts));
		
		return '
				<div class="thumb">
					<div class="boxthumb">
						<a href="'.$url.'" target="_'.$target.'"><img src="'.$image.'"/></a>
					</div>
					<div class="thmbname">'.$name.'</div>
				</div>
		';
}
add_shortcode('boxthumb','thumbbox');

// Social Icon Shortcodes
/*[social_area]
    [social icon="facebook" link="#"]
    [social icon="twitter" link="#"]
    [social icon="google-plus" link="#"]	
    [social icon="linkedin" link="#"]
    [social icon="pinterest" link="#"]
[/social_area]*/
function complete_social_area($atts,$content = null){
  return '<div class="social-icons">'.do_shortcode($content).'</div>';
 }
add_shortcode('social_area','complete_social_area');

function complete_social($atts){
 extract(shortcode_atts(array(
  'icon' => '',
  'link' => ''
 ),$atts));
  return '<a href="'.$link.'" target="_blank" class="fa fa-'.$icon.' fa-1x" title="'.$icon.'"></a>';
 }
add_shortcode('social','complete_social');

// Footer Posts

/*[footerposts show="3"]*/
function footerpost_func( $atts ){
	global $post;
   extract( shortcode_atts( array(
		'show' => '',
	), $atts ) );
	$postoutput = '';
	wp_reset_query();
	query_posts(  array( 'posts_per_page'=>$show, 'post__not_in' => get_option('sticky_posts') )  );
	$postoutput .='<div class="footer-blog-posts">
            	<ul>';
	if ( have_posts() ) :
		while ( have_posts() ) : the_post();
 			if ( has_post_thumbnail()) {
				$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'footerthumb' );
				$imgUrl = '<img src="'.$large_image_url[0].'"/>';
			}
			else
			{ 
				$imgUrl = '<img style="display:none;" src=""/>';
			}
			$postoutput .= '<li><a href="'.get_the_permalink().'">'.get_the_title().'</a></li>';
		endwhile;
	endif;
	wp_reset_query();
	$postoutput .= '</ul></div>';
	return $postoutput;
}
add_shortcode( 'footerposts', 'footerpost_func' );	

// Shortcode Flip Box
/*[flipbox fliptype="fliptype" frontimage="frontimage" fronttitle="fronttitle" frontdescription="frontdescription" backtitle="backtitle" backdescription="backdescription" backurl="backurl" backbutton="backbutton"]*/

function flip($atts){
		extract( shortcode_atts(array(
			'fliptype' => 'fliptype',
			'frontimage' => 'frontimage',
			'fronttitle' => 'fronttitle',
			'frontdescription' => 'frontdescription',
			'backtitle' => 'backtitle',
			'backdescription' => 'backdescription',
			'backurl' => 'backurl',
			'backbutton' => 'backbutton',
		), $atts));
		
		return '
		<div class="flipcard '.$fliptype.'">
    <div class="front">
      	<div class="frontimage"><img src="'.$frontimage.'"/></div>
        <h3>'.$fronttitle.'</h3>
        <p>'.$frontdescription.'</p>
    </div>
    <div class="back">
	  <h3>'.$backtitle.'</h3>
      <p style="margin-bottom:20px;">'.$backdescription.'</p>
	  <a style="color:#FFF;" href="'.$backurl.'" class="sktmore"> '.$backbutton.' </a>
    </div>
</div>';

}
add_shortcode('flipbox','flip');

// Shortcode Divider
/*[divider style="divider1"]*/

function dividerstyle($atts){
		extract( shortcode_atts(array(
			'style' => 'style',
		), $atts));
		
		return '
		<div class="'.$style.'"><span></span></div>
		';

}
add_shortcode('divider','dividerstyle');

// Shortcode Heading Divider
/*[headingdivider text="text"]*/

function dividerstyleheading($atts){
		extract( shortcode_atts(array(
			'text' => 'text',
		), $atts));
		
		return '
		 <div class="fusion-title"><h3>'.$text.'</h3><div class="title-sep-container"><div class="title-sep sep-double"></div></div></div>
		';

}
add_shortcode('headingdivider','dividerstyleheading');

// Shortcode Heading Seperator
/*[headingseperator text="text"]*/

function headingseperatorstyle($atts){
		extract( shortcode_atts(array(
			'text' => 'text',
		), $atts));
		
		return '
		 <div class="headingseperator"><h3>'.$text.'</h3></div>
		';

}
add_shortcode('headingseperator','headingseperatorstyle');

// Shortcode Center Title Seperator
/*[centertitle text="text" titlecolor="#ffffff" seperatorcolor="#ffffff"]*/

function titlesep($atts){
		extract( shortcode_atts(array(
			'text' => 'text',
			'titlecolor' => 'seperatorcolor',
			'seperatorcolor' => 'seperatorcolor',
		), $atts));
		
		return '
		<div class="center-title"><h2 style="color:'.$titlecolor.'">'.$text.'</h2><span style="border-bottom-color:'.$seperatorcolor.'"></span></div>
		';

}
add_shortcode('centertitle','titlesep');

// Shortcode Promobox1
/* [promobox1 bgcolor="#f7f7f7" topbordercolor="#00b965" otherbordercolor="#e8e6e6"]Description[/promobox1] */

function promo1($atts, $content = null){
		extract( shortcode_atts(array(
			'bgcolor'  => 'bgcolor',
			'topbordercolor'  => 'topbordercolor',
			'otherbordercolor'  => 'otherbordercolor',
		), $atts));
		
		return '
			 <div class="promo1" style="background-color:'.$bgcolor.'; border-color: '.$topbordercolor.' '.$otherbordercolor.' '.$otherbordercolor.';">
             	'.$content.'
             </div>	
		';
}
add_shortcode('promobox1','promo1');

// Shortcode Promobox2
/* [promobox2 bgcolor="#f7f7f7" leftbordercolor="#00b965" button="Hello Text" url="#"]Description[/promobox2] */

function promo2($atts, $content = null){
		extract( shortcode_atts(array(
			'bgcolor'  => 'bgcolor',
			'leftbordercolor'  => 'leftbordercolor',
			'button'  => 'button',
			'url'  => 'url',
		), $atts));
		
		return '
			 <div class="promo2" style="background-color:'.$bgcolor.'; border-left-color:'.$leftbordercolor.';">
             	<div class="promo-left">
             	'.$content.'
                </div>
                <div class="promo-right">
                <div class="sktmore"><a href="'.$url.'">'.$button.'</a></div>
                </div>
                <div class="clear"></div>
             </div>	
		';
}
add_shortcode('promobox2','promo2');

// Shortcode Promobox3
/* [promobox3 bgcolor="#f7f7f7" bottombordercolor="#00b965" button="Hello Text" url="#"]Description[/promobox3] */

function promo3($atts, $content = null){
		extract( shortcode_atts(array(
			'bgcolor'  => 'bgcolor',
			'bottombordercolor'  => 'bottombordercolor',
			'button'  => 'button',
			'url'  => 'url',
		), $atts));
		
		return '
			 <div class="promo3" style="background-color:'.$bgcolor.'; border-bottom-color:'.$bottombordercolor.';">
             	'.$content.'
                <div class="sktmore"><a href="'.$url.'">'.$button.'</a></div>
             </div>	
		';
}
add_shortcode('promobox3','promo3');

// Shortcode Promobox4
/* [promobox4 bgcolor="#f7f7f7" bordercolor="#00b965"]Description[/promobox4] */

function promo4($atts, $content = null){
		extract( shortcode_atts(array(
			'bgcolor'  => 'bgcolor',
			'bordercolor'  => 'bordercolor',
		), $atts));
		
		return '
			<div class="promo4" style="background-color:'.$bgcolor.'; border-color:'.$bordercolor.';">
            	'.$content.'
             </div>	
		';
}
add_shortcode('promobox4','promo4');

// Shortcode Promobox5
/* [promobox5 bgcolor="#f7f7f7" button="Hello Text" url="#"]Description[/promobox5] */

function promo5($atts, $content = null){
		extract( shortcode_atts(array(
			'bgcolor'  => 'bgcolor',
			'button'  => 'button',
			'url'  => 'url',
		), $atts));
		
		return '
			 <div class="promo5" style="background-color:'.$bgcolor.';">
             	'.$content.'
                <div class="sktmore"><a href="'.$url.'">'.$button.'</a></div>
             </div>	
		';
}
add_shortcode('promobox5','promo5');


// Shortcode Cols
/* [columns size="1"][/columns] */
function cols($atts, $content = null){
		extract( shortcode_atts(array(
			'size'  => 'size',
		), $atts));
		
		return '
			<div class="skt-columns-'.$size.'">'.do_shortcode($content).'</div>  	
		';
}
add_shortcode('columns','cols');

// Shortcode Features Left
/*[featuresleft url="#" title="title" description="description" ordernumber="ordernumber"]*/
function featuresboxleft($atts){
		extract( shortcode_atts(array(
			'url' => 'url',
			'title' => 'title',
			'description' => 'description',
			'ordernumber' => 'ordernumber'
		), $atts));
		
		return '
				<div class="left-fitbox">
				<a href="'.$url.'">
				<div class="left-fitleft">
					<div class="left-fit-title"><h3>'.$title.'</h3></div>
					<div class="left-fit-desc">'.$description.'</div>
				</div>
				<div class="left-fitright">'.$ordernumber.'</div>
				</a>
				</div>
				<div class="clear"></div>
		';
}
add_shortcode('featuresleft','featuresboxleft');

// Shortcode Thumb Box
/*[featurethumb url="#" image="image"]*/
function thumbnailbox($atts){
		extract( shortcode_atts(array(
			'url' => 'url',
			'image' => 'image'
		), $atts));
		
		return '
                <div class="featurethumb">
                    <a href="'.$url.'"><img src="'.$image.'"/></a>
                </div>
		';
}
add_shortcode('featurethumb','thumbnailbox');

// Shortcode Features Right
/*[featuresright url="#" title="title" description="description" ordernumber="ordernumber"]*/
function featuresboxright($atts){
		extract( shortcode_atts(array(
			'url' => 'url',
			'title' => 'title',
			'description' => 'description',
			'ordernumber' => 'ordernumber'
		), $atts));
		
		return '
				<div class="right-fitbox">
				<a href="'.$url.'">
				<div class="right-fitleft">'.$ordernumber.'</div>
				<div class="right-fitright">
				<div class="right-fit-title"><h3>'.$title.'</h3></div>
				<div class="right-fit-desc">'.$description.'</div></div>
				</a>
				</div>
				<div class="clear"></div>
		';
}
add_shortcode('featuresright','featuresboxright');

// Shortcode Blocks

/* [blocks icon="image.jpg" title="Title" readmoretext="Read More" url="#"]Description[/blocks] */

// add shortcode for blocks
function blocksbox($atts, $content = null){
		extract( shortcode_atts(array(
			'icon'  => 'icon',
			'title'  => 'title',
			'readmoretext'  => 'readmoretext',
			'url' => 'url',
		), $atts));
		
		return '
			 <div class="blocksbox">
							<div class="blockthumb"><img src="'.$icon.'" /></div>
							<div class="blocktitle">
								<h3>'.$title.'</h3>
							</div>
							<div class="blockdesc">'.$content.'</div>
							<div class="blockmore"><a href="'.$url.'">'.$readmoretext.'</a></div>
						</div>
		';
}
add_shortcode('blocks','blocksbox');

// Shortcode Square Box
/*[squarebox image="image" title="title" url="#" target="blank"]*/
function squareboxarea($atts){
		extract( shortcode_atts(array(
			'image' => 'image',
			'title' => 'title',
			'url' => 'url',
			'target' => 'target'
		), $atts));
		
		
		return '
				<a href="'.$url.'">
					<div class="squarebox">
					<div class="squareicon"><img src="'.$image.'" /></div>
					<div class="squaretitle">'.$title.'</div>
					</div>		
				 </a>
		';
}
add_shortcode('squarebox','squareboxarea');

// Shortcode Perfect Box
/*[perfectbox image="image" title="title" description="description" url="#" target="blank"]*/
function perfectboxarea($atts){
		extract( shortcode_atts(array(
			'image' => 'image',
			'title' => 'title',
			'description' => 'description',
			'url' => 'url',
			'target' => 'target'
		), $atts));
		
		
		return '
			 <div class="perfectbox">
						<a href="'.$url.'" target="_'.$target.'"><div class="perfectborder">
							 <div class="perf-thumb"><img src="'.$image.'"/></div>
							 <div class="perf-title"><h3>'.$title.'</h3></div>
							 <div class="perf-description">'.$description.'</div>	
						</div></a>
			 </div>
		';
}
add_shortcode('perfectbox','perfectboxarea');

// Shortcode Block Box
/*[blockbox url="url" image="image" title="title" titlecolor="titlecolor" target="self"]*/
function blockboxset($atts){
		extract( shortcode_atts(array(
		'url' => 'url',
		'target' => 'target',
		'titlecolor' => 'titlecolor',
		'image' => 'image',
		'title' => 'title',
		), $atts));	
		
		return '
			<div class="blockbox">
				<a href="'.$url.'" target="_'.$target.'">
					<div class="infoblockthumb"><img src="'.$image.'" /></div>
					<div class="infoblocktitle"><h4 style="color:'.$titlecolor.';">'.$title.'</h4></div>
				</a>
			</div>
		';
}
add_shortcode('blockbox','blockboxset');

// Footer Menu
/*[footermenu]*/
function foot_menu($atts, $content = null) {
	extract(shortcode_atts(array(  
		'menu'            => '', 
		'container'       => 'div', 
		'container_class' => '', 
		'container_id'    => '', 
		'menu_class'      => 'footmenu', 
		'menu_id'         => '',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'depth'           => 1,
		'walker'          => '',
		'theme_location'  => 'footer'), 
		$atts));
 
 
	return wp_nav_menu( array( 
		'menu'            => $menu, 
		'container'       => $container, 
		'container_class' => $container_class, 
		'container_id'    => $container_id, 
		'menu_class'      => $menu_class, 
		'menu_id'         => $menu_id,
		'echo'            => false,
		'fallback_cb'     => $fallback_cb,
		'before'          => $before,
		'after'           => $after,
		'link_before'     => $link_before,
		'link_after'      => $link_after,
		'depth'           => $depth,
		'walker'          => $walker,
		'theme_location'  => $theme_location));
}
//Create the shortcode
add_shortcode("footermenu", "foot_menu");



// Register Custom Post Type For Causes
function custom_post_type_sktcauses() {

	$labels = array(
		'name'                  => _x( 'SKT Causes', 'Post Type General Name', 'complete' ),
		'singular_name'         => _x( 'SKT Cause', 'Post Type Singular Name', 'complete' ),
		'menu_name'             => __( 'SKT Causes', 'complete' ),
		'name_admin_bar'        => __( 'SKT Cause', 'complete' ),
		'archives'              => __( 'Item Archives', 'complete' ),
		'attributes'            => __( 'Item Attributes', 'complete' ),
		'parent_item_colon'     => __( 'Parent Item:', 'complete' ),
		'all_items'             => __( 'All Items', 'complete' ),
		'add_new_item'          => __( 'Add New Item', 'complete' ),
		'add_new'               => __( 'Add New', 'complete' ),
		'new_item'              => __( 'New Item', 'complete' ),
		'edit_item'             => __( 'Edit Item', 'complete' ),
		'update_item'           => __( 'Update Item', 'complete' ),
		'view_item'             => __( 'View Item', 'complete' ),
		'view_items'            => __( 'View Items', 'complete' ),
		'search_items'          => __( 'Search Item', 'complete' ),
		'not_found'             => __( 'Not found', 'complete' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'complete' ),
		'featured_image'        => __( 'Featured Image', 'complete' ),
		'set_featured_image'    => __( 'Set featured image', 'complete' ),
		'remove_featured_image' => __( 'Remove featured image', 'complete' ),
		'use_featured_image'    => __( 'Use as featured image', 'complete' ),
		'insert_into_item'      => __( 'Insert into item', 'complete' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'complete' ),
		'items_list'            => __( 'Items list', 'complete' ),
		'items_list_navigation' => __( 'Items list navigation', 'complete' ),
		'filter_items_list'     => __( 'Filter items list', 'complete' ),
	);
	$rewrite = array(
		'slug'                  => 'cause',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( 'SKT Cause', 'complete' ),
		'description'           => __( 'Post type created for causes.', 'complete' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 30,
		'menu_icon'             => 'dashicons-smiley',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'rewrite'               => $rewrite,
		'capability_type'       => 'page',
	);
	register_post_type( 'skt_causes', $args );

}
add_action( 'init', 'custom_post_type_sktcauses', 0 );

// Register Meta Box For Causes
add_action( 'admin_init', 'register_metabox_sktcauses' );
function register_metabox_sktcauses() {
    add_meta_box( 'sktcauses_meta_box',
        'Cause Info',
        'display_metabox_sktcauses',
        'skt_causes', 'normal', 'high'
    );
}

// Display Meta Box For Causes
function display_metabox_sktcauses( $sktcause ) {
	$sktcause_donation_target = esc_html( get_post_meta( $sktcause->ID, 'sktcause_donation_target', true ) );
	$sktcause_donation_achieved = esc_html( get_post_meta( $sktcause->ID, 'sktcause_donation_achieved', true ) );
	$sktcause_donation_achieved_percent = esc_html( get_post_meta( $sktcause->ID, 'sktcause_donation_achieved_percent', true ) );
	
	$sktcause_btntext = esc_html( get_post_meta( $sktcause->ID, 'sktcause_btntext', true ) );
	$sktcause_btnlink = esc_html( get_post_meta( $sktcause->ID, 'sktcause_btnlink', true ) );
	$sktcause_btnlink_target = esc_html( get_post_meta( $sktcause->ID, 'sktcause_btnlink_target', true ) );
    ?>
    <table width="100%" class="sktcause-table">
    	<tr>
            <td width="20%" class="sktcause-table-title"><?php _e( 'Donation Text', 'complete' ); ?></td>
            <td width="80%"><input type="text" name="sktcause_donation_target" value="<?php echo $sktcause_donation_target; ?>" /></td>
        </tr>
        <tr>
            <td width="20%" class="sktcause-table-title"><?php _e( 'Donation Achieved', 'complete' ); ?></td>
            <td width="80%"><input type="text" name="sktcause_donation_achieved" value="<?php echo $sktcause_donation_achieved; ?>" /></td>
        </tr>
        <tr>
            <td width="20%" class="sktcause-table-title"><?php _e( 'Donation Achieved %', 'complete' ); ?></td>
            <td width="80%">
                <input type="range" min="1" max="100" name="sktcause_donation_achieved_percent" value="<?php echo $sktcause_donation_achieved_percent; ?>" id="sktcause_rating_range"/>
                <span id="sktcause_rating_output"><?php echo $sktcause_rating; ?></span>
                <script>
					var slider = document.getElementById("sktcause_rating_range");
					var output = document.getElementById("sktcause_rating_output");
					output.innerHTML = slider.value;
					slider.oninput = function() {
					  output.innerHTML = this.value;
					}
				</script>
            </td>
        </tr>
        <tr>
            <td width="20%" class="sktcause-table-title">Button Text </td>
            <td width="80%"><input type="text" name="sktcause_btntext" value="<?php echo $sktcause_btntext; ?>" /></td>
        </tr>
        <tr>
            <td width="20%" class="sktcause-table-title">Button Link </td>
            <td width="80%">
            	<input type="text" name="sktcause_btnlink" value="<?php echo $sktcause_btnlink; ?>" />
                <label id="sktcause_btnlink_target">Open in new tab? <input type="checkbox" name="sktcause_btnlink_target" value="yes" <?php if ($sktcause_btnlink_target == 'yes') { ?> checked="checked" <?php } ?>/></label>
            </td>
        </tr>
    </table>
    <?php    
}

// Save Meta Box For Causes
add_action( 'save_post', 'save_metabox_sktcauses', 10, 2 );
function save_metabox_sktcauses( $sktcause_id, $sktcause ) {
    // Check post type for coupons
    if ( $sktcause->post_type == 'skt_causes' ) {
        // Store data in post meta table if present in post data
		if ( isset($_POST['sktcause_donation_target']) ) {
            update_post_meta( $sktcause_id, 'sktcause_donation_target', $_POST['sktcause_donation_target'] );
        }
		if ( isset($_POST['sktcause_donation_achieved']) ) {
            update_post_meta( $sktcause_id, 'sktcause_donation_achieved', $_POST['sktcause_donation_achieved'] );
        }
		if ( isset($_POST['sktcause_donation_achieved_percent']) ) {
            update_post_meta( $sktcause_id, 'sktcause_donation_achieved_percent', $_POST['sktcause_donation_achieved_percent'] );
        }
		
		if ( isset($_POST['sktcause_btntext']) ) {
            update_post_meta( $sktcause_id, 'sktcause_btntext', $_POST['sktcause_btntext'] );
        }
		if ( isset($_POST['sktcause_btnlink']) ) {
            update_post_meta( $sktcause_id, 'sktcause_btnlink', $_POST['sktcause_btnlink'] );
        }
		if ( isset($_POST['sktcause_btnlink_target']) ) {
            update_post_meta( $sktcause_id, 'sktcause_btnlink_target', $_POST['sktcause_btnlink_target'] );
        } else {
			update_post_meta( $sktcause_id, 'sktcause_btnlink_target', 'no' );
		}
    }
}

// Shortcode Causes
// [causes col="3" show="3" excerptlength="10"]
function causes_func( $atts ) {
   extract( shortcode_atts( array(
		'col' => '3',
		'show' => '3',
		'excerptlength' => '10',
		'category' => ''
	), $atts ) );
	  extract( shortcode_atts( array( 'show' => $show,), $atts ) ); $ourtm = ''; wp_reset_query(); 

	$causes = '<div class="sectionrow">';
	$args = array( 'post_type' => 'skt_causes', 'posts_per_page' => $show, 'skt_causes_category' => $category );
	query_posts( $args );
	$n = 0;
	if ( have_posts() ) {
		while ( have_posts() ) { 
			$n++;
			the_post();
			
			$sktcause_donation_target = esc_html( get_post_meta( get_the_ID(), 'sktcause_donation_target', true ) );
			$sktcause_donation_achieved = esc_html( get_post_meta( get_the_ID(), 'sktcause_donation_achieved', true ) );
			$sktcause_donation_achieved_percent = esc_html( get_post_meta( get_the_ID(), 'sktcause_donation_achieved_percent', true ) );
			
			$sktcause_btntext = esc_html( get_post_meta( get_the_ID(), 'sktcause_btntext', true ) );
			$sktcause_btnlink = esc_html( get_post_meta( get_the_ID(), 'sktcause_btnlink', true ) );
			$sktcause_btnlink_target = esc_html( get_post_meta( get_the_ID(), 'sktcause_btnlink_target', true ) );	

			//$categories = get_categories( array('taxonomy' => 'skt_causes_category') );
			$categories = get_the_terms( get_the_ID(), 'skt_causes_category' );
			
			if( $col == 1 ){
				$causes .= '<div class="cols1 skt-cauese-box';
			}elseif( $col == 2 ){
				$causes .= '<div class="cols2 skt-causes-box';
			}elseif( $col == 3 ){
				$causes .= '<div class="cols3 skt-causes-box';
			}
				$causes .= '">';
 
			$causes .= ' 
			<div class="causes-thumb">';
		
			$causes .= '			
			<a href="'.get_permalink().'" title="'.get_the_title().'">'.( (get_the_post_thumbnail( get_the_ID(), 'thumbnail') != '') ? 
get_the_post_thumbnail( get_the_ID(), 'full') : '<img src="'.get_template_directory_uri().'/images/team_thumb.jpg" />' ).'</a>
            <div class="causes-skill">	
			<div class="skillbar" data-percent="'.$sktcause_donation_achieved_percent.'%">
				<div class="skillbar-bar">
					<div class="skill-bar-percent">'.$sktcause_donation_achieved_percent.'%</div>
				</div>
			</div>
			</div>';
            $causes .= '<div class="causes-info-box">
				<div class="causes-content">
				<h3 class="causes-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h3>				
			    <p class="causes-desc">'.wp_trim_words( get_the_content(), $excerptlength ).'</p>
				<div class="donated-info">
				<div class="cuase-goal">'.$sktcause_donation_target.'</div>
				<div class="cuase-raised">'.$sktcause_donation_achieved.'</div>											
				</div>
				<div class="causes-button">
				<a href="'.(($sktcause_btnlink!='') ? ''.$sktcause_btnlink.'' : ''.get_permalink().'' ).'" class="read-more" '.(($sktcause_btnlink_target=='yes') ? 'target="_blank"' : '' ).'>'.(($sktcause_btntext!='') ? ''.$sktcause_btntext.'' : 'Donate' ).'</a>
			    </div>
				<div class="clear"></div>
			    </div>';				
			    $causes .= '</div></div></div>';
	}
		}
	wp_reset_query();
	$causes .= '</div>';
    return $causes;

}
add_shortcode( 'causes', 'causes_func' );


// Event link 
// http://www.noeltock.com/web-design/wordpress/custom-post-types-events-pt1/
// 1. Custom Post Type Registration (Events)
add_action( 'init', 'create_event_postype' );
function create_event_postype() {
$labels = array(
    'name' => __('SKT Events', 'complete'),
    'singular_name' => __('SKT Event', 'complete'),
    'add_new' => __('Add New', 'complete'),
    'add_new_item' => __('Add New Event', 'complete'),
    'edit_item' => __('Edit Event', 'complete'),
    'new_item' => __('New Event', 'complete'),
    'view_item' => __('View Event', 'complete'),
    'search_items' => __('Search Events', 'complete'),
    'not_found' =>  __('No events found', 'complete'),
    'not_found_in_trash' => __('No events found in Trash', 'complete'),
    'parent_item_colon' => '',
);

$args = array(
    'label' => __('SKT Events'),
    'labels' => $labels,
    'public' => true,
    'can_export' => true,
    'show_ui' => true,
    '_builtin' => false,
    '_edit_link' => 'post.php?post=%d', // ?
    'capability_type' => 'post',
    'menu_icon'	=> 'dashicons-calendar',
    'hierarchical' => false,
    'rewrite' => array( "slug" => "sktevents" ),
    'supports'=> array('title', 'thumbnail', 'editor') ,
    'show_in_nav_menus' => true
);
register_post_type( 'skt_events', $args);
}

// 3. Show Columns

add_filter ("manage_edit-skt_events_columns", "skt_events_edit_columns");
add_action ("manage_posts_custom_column", "skt_events_custom_columns");

function skt_events_edit_columns($columns) {

    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => "Event",
        "skt_col_ev_date" => "Dates",
        "skt_col_ev_times" => "Times",
//        "skt_col_ev_thumb" => "Thumbnail",
//        "skt_col_ev_desc" => "Description",
        );
    return $columns;
}
function skt_events_custom_columns($column) {
    global $post;
    $custom = get_post_custom();
    switch ($column)
        {
            case "skt_col_ev_date":
                // - show dates -
                $startd = $custom["skt_events_startdate"][0];
                $endd = $custom["skt_events_enddate"][0];
                $startdate = date("F j, Y", $startd);
                $enddate = date("F j, Y", $endd);
                echo $startdate . '<br /><em>' . $enddate . '</em>';
            break;
            case "skt_col_ev_times":
                // - show times -
                $startt = $custom["skt_events_startdate"][0];
                $endt = $custom["skt_events_enddate"][0];
                $time_format = get_option('time_format');
                $starttime = date($time_format, $startt);
                $endtime = date($time_format, $endt);
                echo $starttime . ' - ' .$endtime;
            break;
            case "skt_col_ev_thumb":
                // - show thumb -
                $post_image_id = get_post_thumbnail_id(get_the_ID());
                if ($post_image_id) {
                    $thumbnail = wp_get_attachment_image_src( $post_image_id, 'post-thumbnail', false);
                    if ($thumbnail) (string)$thumbnail = $thumbnail[0];
                    echo '<img src="';
                    echo bloginfo('template_url');
                    echo '/timthumb/timthumb.php?src=';
                    echo $thumbnail;
                    echo '&h=60&w=60&zc=1" alt="" />';
                }
            break;
            case "skt_col_ev_desc";
                the_excerpt();
            break;
        }
}
// 4. Show Meta-Box
add_action( 'admin_init', 'skt_events_create' );
function skt_events_create() {
    add_meta_box('skt_events_meta', 'Event Info', 'skt_events_meta', 'skt_events');
}
function skt_events_meta () {
    // - grab data -
    global $post;
    $custom = get_post_custom($post->ID);
    $meta_sd = $custom["skt_events_startdate"][0];
    $meta_ed = $custom["skt_events_enddate"][0];
    $meta_st = $meta_sd;
    $meta_et = $meta_ed;
    // - grab wp time format -
    $date_format = get_option('date_format'); // Not required in my code
    $time_format = get_option('time_format');
	$skt_event_vanue = get_post_meta( $post->ID, 'skt_event_vanue', true );
	$skt_event_host = get_post_meta( $post->ID, 'skt_event_host', true );
	
	$skt_event_button = get_post_meta( $post->ID, 'skt_event_button', true );
	$skt_event_button_link = get_post_meta( $post->ID, 'skt_event_button_link', true );
	
	$skt_event_host_text = get_post_meta( $post->ID, 'skt_event_host_text', true );
	// - populate today if empty, 00:00 for time -
    if ($meta_sd == null) { $meta_sd = time(); $meta_ed = $meta_sd; $meta_st = 0; $meta_et = 0;}
    // - convert to pretty formats -
    $clean_sd = date("D, M d, Y", $meta_sd);
    $clean_ed = date("D, M d, Y", $meta_ed);
    $clean_st = date($time_format, $meta_st);
    $clean_et = date($time_format, $meta_et);
    // - security -
    echo '<input type="hidden" name="skt-events-nonce" id="skt-events-nonce" value="' .
    wp_create_nonce( 'skt-events-nonce' ) . '" />';
    // - output -
    ?>
<div class="skt-meta">
<ul>
<li><label>Start Date</label><input name="skt_events_startdate" class="tfdate" value="<?php echo $clean_sd; ?>" /></li>
<li><label>Start Time</label><input name="skt_events_starttime" id="timepicker" value="<?php echo $clean_st; ?>" /><em>Use 12h format</em></li>
<li><label>End Date</label><input name="skt_events_enddate" class="tfdate" value="<?php echo $clean_ed; ?>" /></li>
<li><label>End Time</label><input name="skt_events_endtime" id="timepickerend" value="<?php echo $clean_et; ?>" /><em>Use 12h format</em></li>

<li><label>Event Venue</label><input type="text" name="skt_event_vanue" id="skt_event_vanue" value="<?php echo $skt_event_vanue; ?>" /></li>
<li><label>Event Host Text</label><input type="text" name="skt_event_host_text" id="skt_event_host_text" value="<?php echo $skt_event_host_text; ?>" /></li>
<li><label>Event Host</label><input type="text" name="skt_event_host" id="skt_event_host" value="<?php echo $skt_event_host; ?>" /></li>

<li><label>Button</label><input type="text" name="skt_event_button" id="skt_event_button" value="<?php echo $skt_event_button; ?>" /></li>
<li><label>Button Link</label><input type="text" name="skt_event_button_link" id="skt_event_button_link" value="<?php echo $skt_event_button_link; ?>" /></li>
</ul>
</div>
    <?php
}
// 5. Save Data
add_action ('save_post', 'save_skt_events', 10, 2 );
function save_skt_events( ){
    global $post;
    // - still require nonce
    if ( !wp_verify_nonce( isset($_POST['skt-events-nonce']), 'skt-events-nonce' )) {
		 if( !is_object($post) ) 
        return $post->ID;
    }
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;
    // - convert back to unix & update post
    if(!isset($_POST["skt_events_startdate"])):
        return $post;
        endif;
        $updatestartd = strtotime ( $_POST["skt_events_startdate"] . $_POST["skt_events_starttime"] );
        update_post_meta($post->ID, "skt_events_startdate", $updatestartd );
    if(!isset($_POST["skt_events_enddate"])):
        return $post;
        endif;
        $updateendd = strtotime ( $_POST["skt_events_enddate"] . $_POST["skt_events_endtime"]);
        update_post_meta($post->ID, "skt_events_enddate", $updateendd );
		
    if(!isset($_POST["skt_event_vanue"])):
        return $post;
        endif;
        $eventupdate = $_POST["skt_event_vanue"];
        update_post_meta($post->ID, "skt_event_vanue", $eventupdate ); 
	
	if(!isset($_POST["skt_event_host"])):
        return $post;
        endif;
        $eventupdate = $_POST["skt_event_host"];
        update_post_meta($post->ID, "skt_event_host", $eventupdate );
		
	if(!isset($_POST["skt_event_button"])):
        return $post;
        endif;
        $eventupdate = $_POST["skt_event_button"];
        update_post_meta($post->ID, "skt_event_button", $eventupdate ); 
	
	if(!isset($_POST["skt_event_button_link"])):
        return $post;
        endif;
        $eventupdate = $_POST["skt_event_button_link"];
        update_post_meta($post->ID, "skt_event_button_link", $eventupdate ); 
		
	if(!isset($_POST["skt_event_host_text"])):
        return $post;
        endif;
        $eventupdate = $_POST["skt_event_host_text"];
        update_post_meta($post->ID, "skt_event_host_text", $eventupdate ); 	
	
}
 
 
// 6. Customize Update Messages
add_filter('post_updated_messages', 'events_updated_messages');
function events_updated_messages( $messages ) {
  global $post, $post_ID;
  $messages['skt_events'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Event updated. <a href="%s">View item</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Event updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Event restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Event published. <a href="%s">View event</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Event saved.'),
    8 => sprintf( __('Event submitted. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Event draft updated. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );
  return $messages;
}
// 7. JS Datepicker UI
function events_styles() {
    global $post_type;
    if( 'skt_events' != $post_type )
        return;
		wp_enqueue_style('ui-datepicker', get_template_directory_uri() . '/sktevents/css/jquery-ui-1.8.9.custom.css');
		wp_enqueue_style('ui-timepicki', get_template_directory_uri() . '/sktevents/css/timepicki.css');
}
function events_scripts() {
    global $post_type;
    if( 'skt_events' != $post_type )
    return;
	wp_enqueue_script('ui-datepicker', get_template_directory_uri() . '/sktevents/js/datepicker.js');
    wp_enqueue_script('custom_script', get_template_directory_uri() . '/sktevents/js/pubforce-admin.js', array('jquery'));
	wp_enqueue_script('ui-time', get_template_directory_uri() . '/sktevents/js/timepicki.js');
}
add_action( 'admin_print_styles-post.php', 'events_styles', 1000 );
add_action( 'admin_print_styles-post-new.php', 'events_styles', 1000 );
add_action( 'admin_print_scripts-post.php', 'events_scripts', 1000 );
add_action( 'admin_print_scripts-post-new.php', 'events_scripts', 1000 );

function skt_events_full ( $atts ) {
// - define arguments -
extract(shortcode_atts(array(
    'limit' => '', // # of events to show 
	'excerptlength' => '',
 ), $atts));
// ===== OUTPUT FUNCTION =====
ob_start();
// ===== LOOP: FULL EVENTS SECTION =====
// - hide events that are older than 6am today (because some parties go past your bedtime) -
$today6am = strtotime('today 6:00') + ( get_option( 'gmt_offset' ) * 3600 );
// - query -
global $wpdb;
$querystr = "
    SELECT *
    FROM $wpdb->posts wposts, $wpdb->postmeta metastart, $wpdb->postmeta metaend
    WHERE (wposts.ID = metastart.post_id AND wposts.ID = metaend.post_id)
    AND (metaend.meta_key = 'skt_events_enddate' AND metaend.meta_value > $today6am )
    AND metastart.meta_key = 'skt_events_enddate'
    AND wposts.post_type = 'skt_events'
    AND wposts.post_status = 'publish'
    ORDER BY metastart.meta_value ASC LIMIT $limit
 ";
$skt_events = $wpdb->get_results($querystr, OBJECT);
// - declare fresh day -
$daycheck = null;
// - loop -
?>
<div class="column-event-wrapper">
<?php
global $post;

if ($skt_events):
foreach ($skt_events as $post):
setup_postdata($post);
// - custom variables -
$custom = get_post_custom(get_the_ID());
$sd = $custom["skt_events_startdate"][0];
$ed = $custom["skt_events_enddate"][0];
// - determine if it's a new day -
// - local time format -
$time_format = get_option('time_format');
$stime = date($time_format, $sd);
$etime = date($time_format, $ed);
$skt_event_vanue = esc_html( get_post_meta( get_the_ID(), 'skt_event_vanue', true ) );
$skt_event_host = esc_html( get_post_meta( get_the_ID(), 'skt_event_host', true ) );

$skt_event_button = esc_html( get_post_meta( get_the_ID(), 'skt_event_button', true ) );
$skt_event_button_link = esc_html( get_post_meta( get_the_ID(), 'skt_event_button_link', true ) );

$skt_event_host_text = esc_html( get_post_meta( get_the_ID(), 'skt_event_host_text', true ) );

// - output - ?>

<div class="column-event">
<div class="event-content"> 
<div class="datebox">
<?php 
    $longdate = date("j", $sd	);	
    if ($daycheck == null) { echo ''.$longdate.''; }
    if ($daycheck != $longdate && $daycheck != null) { echo '' . $longdate.', '; }
?>
<span>	
<?php 
	$longdate = date("M", $sd);
    if ($daycheck == null) { echo ''.$longdate.''; }
    if ($daycheck != $longdate && $daycheck != null) { echo '' . $longdate.', '; }
?>
</span>
<?php
   /*$elongdate = date("M j", $ed);
   if ($daycheck == null) { echo ''.$elongdate.', &nbsp;'; }
   if ($daycheck != $elongdate && $daycheck != null) { echo ''.$elongdate.', '; }
	echo $etime; */?>    
</div>
<?php if(!empty($skt_event_host)) { ?>
	<div class="host-text"><?php echo $skt_event_host_text; ?></div>
    <h4 class="vanuetiemhost"><?php echo $skt_event_host; ?></h4>
<?php } ?>
<div class="clear"></div>
<a href="<?php the_permalink();?>" class="event-title"><h5><?php the_title(); ?></h5></a>
<?php //echo ( ($excerptlength!='') ? '<p>'.wp_trim_words( get_the_content(), $excerptlength, '').'</p>' : '' ); ?>
<?php if(!empty($skt_event_vanue)) { ?>
    <span class="vanuetiemhost place"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo $skt_event_vanue; ?></span>
<?php } ?>
</div>
<div class="clear"></div>
<?php if(!empty($skt_event_button)) { ?>
<div><a href="<?php echo $skt_event_button_link; ?>" class="event-btn event-read-more"><?php echo $skt_event_button; ?></a></div>
<?php } ?>
</div>
<?php
endforeach;
else :
echo '<div style="text-align:center;">Sorry, Event isn&rsquo;t available right now</div>';
endif;
?>
<?php wp_reset_query(); ?>
<div class="clear"></div>
</div>
<?php 
// ===== RETURN: FULL EVENTS SECTION =====
$output = ob_get_contents();
ob_end_clean();
return $output;
}
add_shortcode('skt-events-coll', 'skt_events_full'); // You can now call onto this shortcode with [tf-events-full limit='20']