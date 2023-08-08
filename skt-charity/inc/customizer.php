<?php
/**
 * SKT Charity Theme Customizer
 *
 * @package SKT Charity
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function skt_charity_customize_register( $wp_customize ) {
	
	//Add a class for titles
    class skt_charity_Info extends WP_Customize_Control {
        public $type = 'info';
        public $label = '';
        public function render_content() {
        ?>
			<h3><?php echo esc_html( $this->label ); ?></h3>
        <?php
        }
    }
	
	class WP_Customize_Textarea_Control extends WP_Customize_Control {
    public $type = 'textarea';
 
    public function render_content() {
        ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
            </label>
        <?php
    }
}
	
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	
	$wp_customize->add_setting('color_scheme',array(
			'default'	=> '#f25f43',
			'sanitize_callback'	=> 'sanitize_hex_color'
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'color_scheme',array(
			'label' => __('Color Scheme','skt-charity'),			
			'section' => 'colors',
			'settings' => 'color_scheme'
		))
	);
	
	$wp_customize->add_section('header_info',array(
			'title'	=> esc_html__('Header Info','skt-charity'),
			'description'	=> esc_html__('Manage Header Info From Widgets >> Header Left Widget, Header Right Widget','skt-charity'),			
			'priority'	=> null,
	));	
	
    $wp_customize->add_setting('skt_charity_options[header-info]', array(
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control( new skt_charity_Info( $wp_customize, 'header_info', array(
        'section' => 'header_info',
        'settings' => 'skt_charity_options[header-info]',
        'priority' => null
        ) )
    ); 	
	
	// Slider Section		
	$wp_customize->add_section(
        'slider_section',
        array(
            'title' => __('Slider Settings', 'skt-charity'),
            'priority' => null,
			'description'	=> __('Featured Image Size Should be 1400x765','skt-charity'),	
        )
		
    );
	
	$wp_customize->add_setting('page-setting7',array(
			'default' => '0',
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'skt_charity_sanitize_integer'
	));
	
	$wp_customize->add_control('page-setting7',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide one:','skt-charity'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting8',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'skt_charity_sanitize_integer'
	));
	
	$wp_customize->add_control('page-setting8',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide two:','skt-charity'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting9',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'skt_charity_sanitize_integer'
	));
	
	$wp_customize->add_control('page-setting9',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide three:','skt-charity'),
			'section'	=> 'slider_section'
	));	
	
	
	$wp_customize->add_setting('hide_slider',array(
			'default' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'edit_theme_options',
	));	 

	$wp_customize->add_control( 'hide_slider', array(
		   'settings' => 'hide_slider',
    	   'section'   => 'slider_section',
    	   'label'     => __('Uncheck This Option To Display Slider','skt-charity'),
    	   'type'      => 'checkbox'
     ));	
	
	// Slider Section
	
	// Home Three Boxes Section 	
	$wp_customize->add_section('section_second', array(
		'title'	=> __('Homepage Four Boxes Section','skt-charity'),		
		'description' => sprintf( __( 'Select Pages from the dropdown for homepage our donate services section.', 'skt-charity' )),	
		'priority'	=> null
	));	
	
	$wp_customize->add_setting('page-column1',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'skt_charity_sanitize_integer',
		));
 
	$wp_customize->add_control(	'page-column1',array('type' => 'dropdown-pages',
			'section' => 'section_second',
	));	
	
	
	$wp_customize->add_setting('page-column2',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'skt_charity_sanitize_integer',
		));
 
	$wp_customize->add_control(	'page-column2',array('type' => 'dropdown-pages',
			'section' => 'section_second',
	));
	
	$wp_customize->add_setting('page-column3',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'skt_charity_sanitize_integer',
		));
 
	$wp_customize->add_control(	'page-column3',array('type' => 'dropdown-pages',
			'section' => 'section_second',
	));	//end three column part
	
	$wp_customize->add_setting('page-column4',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'skt_charity_sanitize_integer',
		));
 
	$wp_customize->add_control(	'page-column4',array('type' => 'dropdown-pages',
			'section' => 'section_second',
	));
	
	$wp_customize->add_setting('hide_boxes',array(
			'default' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'edit_theme_options',
	));	 

	$wp_customize->add_control( 'hide_boxes', array(
		   'settings' => 'hide_boxes',
    	   'section'   => 'section_second',
    	   'label'     => __('Uncheck This Option To Display This Section','skt-charity'),
    	   'type'      => 'checkbox'
     ));	
	
	//end three column part
	
	// Home Welcome Section 	
	$wp_customize->add_section('section_first',array(
		'title'	=> __('Four Box Below Section','skt-charity'),
		'description'	=> __('Select Page from the dropdown for second section','skt-charity'),
		'priority'	=> null
	));
	
	$wp_customize->add_setting('page-setting1',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'skt_charity_sanitize_integer',
		));
 
	$wp_customize->add_control(	'page-setting1',array('type' => 'dropdown-pages',
			'section' => 'section_first',
	));
	
	$wp_customize->add_setting('hide_welcome',array(
			'default' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'edit_theme_options',
	));	 

	$wp_customize->add_control( 'hide_welcome', array(
		   'settings' => 'hide_welcome',
    	   'section'   => 'section_first',
    	   'label'     => __('Uncheck This Option To Display This Section','skt-charity'),
    	   'type'      => 'checkbox'
     ));	
	
	$wp_customize->add_section('footer_area',array(
			'title'	=> esc_html__('Footer Area','skt-charity'),
			'description'	=> esc_html__('Manage Footer From Widgets >> Footer Column 1, Footer Column 2, Footer Column 3','skt-charity'),			
			'priority'	=> null,
	));	
	
    $wp_customize->add_setting('skt_charity_options[footer-info]', array(
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control( new skt_charity_Info( $wp_customize, 'footer_area', array(
        'section' => 'footer_area',
        'settings' => 'skt_charity_options[footer-info]',
        'priority' => null
        ) )
    ); 

}
add_action( 'customize_register', 'skt_charity_customize_register' );

//Integer
function skt_charity_sanitize_integer( $input ) {
    if( is_numeric( $input ) ) {
        return intval( $input );
    }
}

function skt_charity_custom_css(){
		?>
        	<style type="text/css"> 
					a, .blog_lists h2 a:hover,
					#sidebar ul li a:hover,								
					.cols-4 ul li a:hover, .cols-4 ul li.current_page_item a,					
					.phone-no strong,					
					.sitenav ul li a:hover, .sitenav ul li.current_page_item a,					
					.logo h1 span,
					.headertop .left a:hover,
					.services-wrap .one_third h4,
					.cols-4 h5 span,
					.welcomewrap h2 span,
					.one_four_page:hover h4 a,
					.one_four_page:hover a.more,
					.blog_lists h3 a:hover			
					{ color:<?php echo esc_attr(get_theme_mod('color_scheme','#f25f43', 'skt-charity')); ?>;}
					 
					
					.pagination ul li .current, .pagination ul li a:hover, 
					#commentform input#submit:hover,					
					.nivo-controlNav a.active,				
					h3.widget-title,				
					.wpcf7 input[type='submit'],
					.headertop .right a,
					.services-wrap .one_third:hover,
					.widget-right,
					a.ReadMore,
					.donatebtn:hover, .search-form input[type='submit'], .current
					{ background-color:<?php echo esc_attr(get_theme_mod('color_scheme','#f25f43', 'skt-charity')); ?>;}
					
						
					.headerxx									
					{ border-color:<?php echo esc_attr(get_theme_mod('color_scheme','#f25f43', 'skt-charity')); ?>;}
					
			</style> 
<?php   
}
         
add_action('wp_head','skt_charity_custom_css');	

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function skt_charity_customize_preview_js() {
	wp_enqueue_script( 'skt_charity_customizer', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'skt_charity_customize_preview_js' );


function skt_charity_custom_customize_enqueue() {
	wp_enqueue_script( 'skt-charity-custom-customize', get_template_directory_uri() . '/js/custom.customize.js', array( 'jquery', 'customize-controls' ), false, true );
}
add_action( 'customize_controls_enqueue_scripts', 'skt_charity_custom_customize_enqueue' );