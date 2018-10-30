<?php
/**
 * coral-snowy Theme Customizer
 *
 * @package coral-snowy
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */

 //--------- Sanitize
function coral_snowy_sanitize_yesno($setting){ if ( '0'===$setting || '1'===$setting ) return $setting; return '1';}
function coral_snowy_sanitize_voffset($setting){ if (is_numeric($setting) && $setting>=0) return $setting; return 25;}
function coral_snowy_sanitize_voffset2($setting){ if (is_numeric($setting)) return $setting; return 0;}
function coral_snowy_sanitize_logoheight($setting){ if (is_numeric($setting) && $setting>=40 && $setting<=300) return $setting; return 100;}
function coral_snowy_sanitize_size($setting){ if (is_numeric($setting) && $setting>=0) return $setting; return 0;}
function coral_snowy_sanitize_typography( $input ) {
		$valid = array( 	"Default font" => "Default font",
							"Arial, Helvetica, sans-serif" => "Arial, Helvetica, sans-serif",
							"'Arial Black', Gadget, sans-serif" => "'Arial Black', Gadget, sans-serif",
							"'Helvetica Neue', Helvetica, Arial, sans-serif" => "'Helvetica Neue', Helvetica, Arial, sans-serif",
							"'Comic Sans MS', cursive, sans-serif" => "'Comic Sans MS', cursive, sans-serif",
							"Impact, Charcoal, sans-serif" => "Impact, Charcoal, sans-serif",
							"'Lucida Sans Unicode', 'Lucida Grande', sans-serif" => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
							"Tahoma, Geneva, sans-serif" => "Tahoma, Geneva, sans-serif",
							"'Trebuchet MS', Helvetica, sans-serif" => "'Trebuchet MS', Helvetica, sans-serif",
							"Verdana, Geneva, sans-serif" => "Verdana, Geneva, sans-serif",
							"Georgia, serif" => "Georgia, serif",
							"'Palatino Linotype', 'Book Antiqua', Palatino, serif" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
							"'Times New Roman', Times, serif" => "'Times New Roman', Times, serif",
							"'Courier New', Courier, monospace" => "'Courier New', Courier, monospace",
							"'Lucida Console', Monaco, monospace" => "'Lucida Console', Monaco, monospace"
		);
 
    if ( array_key_exists( $input, $valid ) ) {
        return $input;
    } else {
        return "Default font";
    }
}
function coral_snowy_sanitize_pausetime($setting){ if (is_numeric($setting) && $setting>=0) return $setting; return 5000;}
function coral_snowy_sanitize_animspeed($setting){ if (is_numeric($setting) && $setting>=0) return $setting; return 500;}
function coral_snowy_sanitize_effect( $input ) {
    $valid = array(
				'fade' => 'fade',
				'fold' => 'fold',
				'random' => 'random',
				'sliceDown' => 'sliceDown',
				'sliceDownLeft' => 'sliceDownLeft',
				'sliceDownLeft' => 'sliceDownLeft',
				'sliceUp' => 'sliceUp',
				'sliceUpLeft' => 'sliceUpLeft',
				'sliceUpDown' => 'sliceUpDown',
				'sliceUpDownLeft' => 'sliceUpDownLeft',
				'slideInRight' => 'slideInRight',
				'slideInLeft' => 'slideInLeft',
				'boxRandom' => 'boxRandom',
				'boxRain' => 'boxRain',
				'boxRainReverse' => 'boxRainReverse',
				'boxRainGrow' => 'boxRainGrow',
				'boxRainGrowReverse' => 'boxRainGrowReverse',
    );
 
    if ( array_key_exists( $input, $valid ) ) {
        return $input;
    } else {
        return '';
    }
}
function coral_snowy_sanitize_checkbox( $input ) {
		if ( $input == '1' ) {
			return '1';
		} else {
			return '';
		}
}
function coral_snowy_sanitize_radio( $input ) {
    $valid = array(
        '1' => __( 'Yes', 'coral-snowy' ),
		'0' => __( 'No, I want to display my own images', 'coral-snowy' ),
    );
 
    if ( array_key_exists( $input, $valid ) ) {
        return $input;
    } else {
        return '';
    }
}


//---------- Controls
if ( class_exists( 'WP_Customize_Control' ) ) {
    class Coral_Snowy_Text_Description_Control extends WP_Customize_Control {
        public $description;

	    public function render_content() {
			?>
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <input type="text" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
            </label>
            <p class="description more-top"><?php echo ( $this->description ); ?></p>
			<?php
        }
    }
}
// function coral_snowy_customize_controls_print_styles() {
// 	wp_enqueue_style( 'coral_snowy_customizer_css', get_template_directory_uri() . '/css/customizer.css' );
// }
// add_action( 'customize_controls_print_styles', 'coral_snowy_customize_controls_print_styles' );

function coral_snowy_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	
	$wp_customize->selective_refresh->add_partial( 'blogname', array(
		'selector' => '.site-title a',
		'render_callback' => 'coral_snowy_customize_partial_blogname',
	) );
	$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
		'selector' => '.site-description',
		'render_callback' => 'coral_snowy_customize_partial_blogdescription',
	) );
	
// Site title section panel ------------------------------------------------------
		$wp_customize->add_section( 'title_tagline', array(
			'title' => __( 'Logo, Site Title, Tagline, Site icon', 'coral-snowy' ),
			'priority' => 20,
		) );
		$wp_customize->add_setting( 'top_text' , array(
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		));
		$wp_customize->add_control( new Coral_Snowy_Text_Description_Control( $wp_customize, 'top_text_control', array(
			'label' 			=> __( 'Text in the top white lane', 'coral-snowy' ),
			'section' 			=> 'title_tagline',
			'settings' 			=> 'top_text',
			'priority' 			=> 6,
		) ) );	
		$choices =  array(
			'10' => '10%',
			'15' => '15%',
			'20' => '20%',
			'25' => '25%',
			'30' => '30%',
			'33' => '33%',
			'35' => '35%',
			'40' => '40%',
			'45' => '45%',
			'50' => '50%',
		);
		$wp_customize->add_setting( 'coral_snowy_logowidth_setting', array(
			'default' => '35',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( 'coral_snowy_logowidth_control', array(
			'label' => __( 'Max. width of the logo image or logo text (site title and tagline):', 'coral-snowy' ),
			'description' => __( 'This also affects the place width of the social icons and the search form.', 'coral-snowy' ),
			'section' => 'title_tagline',
			'settings' => 'coral_snowy_logowidth_setting',
			'priority' => 8,
			'type' => 'select',
			'choices' => $choices,
		) );	

		$wp_customize->add_setting( 'coral_snowy_logoheight_setting' , array(
			'default'           => 100,
            'sanitize_callback' => 'coral_snowy_sanitize_logoheight',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( 'coral_snowy_logoheight_control', array(
			'label' 			=> __( 'Max. height of the logo image in px', 'coral-snowy' ),
			'section' 			=> 'title_tagline',
			'settings' 			=> 'coral_snowy_logoheight_setting',
			'priority' 			=> 9,
			'type' => 'number',
			'input_attrs' => array(
				'min' => 40,
				'max' => 300,
				'step' => 1,
			),
		) );	
		$wp_customize->add_setting( 'blogname', array(
			'default'    => get_option( 'blogname' ),
			'type'       => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( 'blogname', array(
			'label'      => __( 'Site Title', 'coral-snowy' ),
			'description' => __( 'This is displayed only if you do not upload an image logo', 'coral-snowy' ),
			'section'    => 'title_tagline',
			'priority' => 10,
		) );

		$wp_customize->add_setting( 'blogdescription', array(
			'default'    => get_option( 'blogdescription' ),
			'type'       => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( 'blogdescription', array(
			'label'      => __( 'Tagline', 'coral-snowy' ),
			'description' => __( 'This is displayed only if you do not upload an image logo', 'coral-snowy' ),
			'section'    => 'title_tagline',
			'priority' => 20,
		) );

		$wp_customize->add_setting( 'coral_snowy_titleoffset_setting' , array(
			'default'           => 15,
            'sanitize_callback' => 'coral_snowy_sanitize_voffset2',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( 'coral_snowy_titleoffset__control', array(
			'label' 			=> __( 'Vertical position (margin-top) of the site title in px', 'coral-snowy' ),
			'section' 			=> 'title_tagline',
			'settings' 			=> 'coral_snowy_titleoffset_setting',
			'priority' 			=> 58,
			'type' => 'number',
			'input_attrs' => array(
				'min' => -100,
				'max' => 100,
				'step' => 1,
			),
		) );	
		$wp_customize->add_setting( 'coral_snowy_taglineoffset_setting' , array(
			'default'           => -5,
            'sanitize_callback' => 'coral_snowy_sanitize_voffset2',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( 'coral_snowy_taglineoffset__control', array(
			'label' 			=> __( 'Vertical position (margin-top) of the tagline in px', 'coral-snowy' ),
			'section' 			=> 'title_tagline',
			'settings' 			=> 'coral_snowy_taglineoffset_setting',
			'priority' 			=> 59,
			'type' => 'number',
			'input_attrs' => array(
				'min' => -100,
				'max' => 100,
				'step' => 1,
			),
		) );	

// Layout section panel ------------------------------------------------------
		$wp_customize->add_section( 'coral_snowy_layout_section', array(
			'title' => __( 'Layout', 'coral-snowy' ),
			'priority' => 27,
		) );

		$wp_customize->add_setting( 'coral_snowy_socialoffset_setting' , array(
			'default'           => '28',
            'sanitize_callback' => 'coral_snowy_sanitize_voffset2',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( 'coral_snowy_socialoffset_control', array(
			'label' 			=> __( 'Vertical position (margin-top) of the social icons in px', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_layout_section',
			'settings' 			=> 'coral_snowy_socialoffset_setting',
			'priority' 			=> 39,
			'type' => 'number',
			'input_attrs' => array(
				'min' => -100,
				'max' => 100,
				'step' => 1,
			),
		) );	
		$wp_customize->add_setting( 'coral_snowy_showsearch_setting', array(
			'default' => '1',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'coral_snowy_sanitize_yesno',
		) );

		$wp_customize->add_control( 'coral_snowy_showsearch_control', array(
			'label' => __( 'Display search form?', 'coral-snowy' ),
			'section' => 'coral_snowy_layout_section',
			'settings' => 'coral_snowy_showsearch_setting',
			'priority' => 40,
			'type' => 'select',
			'choices' => array(
				'1' => __( 'Yes', 'coral-snowy' ),
				'0' => __( 'No', 'coral-snowy' ),
			),
		) );
		$choices2 =  array(
			'10' => '10%',
			'15' => '15%',
			'20' => '20%',
			'25' => '25%',
			'30' => '30%',
			'33' => '33%',
			'35' => '35%',
			'40' => '40%',
			'45' => '45%',
			'50' => '50%',
			'55' => '55%',
			'60' => '60%',
			'65' => '65%',
			'66' => '66%',
			'70' => '70%',
			'75' => '75%',
			'80' => '80%',
			'85' => '85%',
			'90' => '90%',
			'95' => '95%',
			'100' => '100%',
		);		
		$wp_customize->add_setting( 'coral_snowy_searchwidth_setting', array(
			'default' => '40',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( 'coral_snowy_searchwidth_control', array(
			'label' => __( 'Width of the search form:', 'coral-snowy' ),
			'description' => __( 'Percentage of the place on the side of the logo text. (Here the right side is 100%, but leave enough place for the social icons!)', 'coral-snowy' ),
			'section' => 'coral_snowy_layout_section',
			'settings' => 'coral_snowy_searchwidth_setting',
			'priority' => 42,
			'type' => 'select',
			'choices' => $choices2,
		) );
		$wp_customize->add_setting( 'coral_snowy_searchoffset_setting' , array(
			'default'           => '25',
            'sanitize_callback' => 'coral_snowy_sanitize_voffset2',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( 'coral_snowy_searchoffset_control', array(
			'label' 			=> __( 'Vertical position (margin-top) of the search box in px', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_layout_section',
			'settings' 			=> 'coral_snowy_searchoffset_setting',
			'priority' 			=> 44,
			'type' => 'number',
			'input_attrs' => array(
				'min' => -100,
				'max' => 100,
				'step' => 1,
			),
		) );	

		$choices1 =  array(
			'10' => '10%',
			'15' => '15%',
			'20' => '20%',
			'25' => '25%',
			'30' => '30%',
			'33' => '33%',
			'35' => '35%',
			'40' => '40%',
			'45' => '45%',
			'50' => '50%',
			'55' => '55%',
			'60' => '60%',
			'65' => '65%',
			'66' => '66%',
			'70' => '70%',
		);
		$wp_customize->add_setting( 'coral_snowy_sidebarwidth_setting', array(
			'default' => '30',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( 'coral_snowy_sidebarwidth_control', array(
			'label' => __( 'Sidebar width:', 'coral-snowy' ),
			'section' => 'coral_snowy_layout_section',
			'settings' => 'coral_snowy_sidebarwidth_setting',
			'priority' => 46,
			'type' => 'select',
			'choices' => $choices1,
		) );		

// Typography
		$wp_customize->add_section( 'coral_snowy_typography_section', array(
			'title' 			=> __( 'Typography', 'coral-snowy' ),
			'priority'			=> 32,
			'description' => __( 'Here you can set up the typography with basic web safe fonts', 'coral-snowy' ),
		) );
		$typoarr = array( 	"Default font" => "Default font",
							"Arial, Helvetica, sans-serif" => "Arial, Helvetica, sans-serif",
							"'Arial Black', Gadget, sans-serif" => "'Arial Black', Gadget, sans-serif",
							"'Helvetica Neue', Helvetica, Arial, sans-serif" => "'Helvetica Neue', Helvetica, Arial, sans-serif",
							"'Comic Sans MS', cursive, sans-serif" => "'Comic Sans MS', cursive, sans-serif",
							"Impact, Charcoal, sans-serif" => "Impact, Charcoal, sans-serif",
							"'Lucida Sans Unicode', 'Lucida Grande', sans-serif" => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
							"Tahoma, Geneva, sans-serif" => "Tahoma, Geneva, sans-serif",
							"'Trebuchet MS', Helvetica, sans-serif" => "'Trebuchet MS', Helvetica, sans-serif",
							"Verdana, Geneva, sans-serif" => "Verdana, Geneva, sans-serif",
							"Georgia, serif" => "Georgia, serif",
							"'Palatino Linotype', 'Book Antiqua', Palatino, serif" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
							"'Times New Roman', Times, serif" => "'Times New Roman', Times, serif",
							"'Courier New', Courier, monospace" => "'Courier New', Courier, monospace",
							"'Lucida Console', Monaco, monospace" => "'Lucida Console', Monaco, monospace"
		);
		$wp_customize->add_setting( 'title_font_setting', array(
			'default'        => 'Default font',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'coral_snowy_sanitize_typography',
		) );
		$wp_customize->add_control( 'title_font_control', array(
			'label'   			=> __('Site title font','coral-snowy'),
			'section' 			=> 'coral_snowy_typography_section',
			'settings' 			=> 'title_font_setting',
			'type'    			=> 'select',
			'priority'        	=> 5,
			'choices'    		=> $typoarr,
		) );

		$wp_customize->add_setting( 'coral_snowy_titlesize_setting', array(
			'default' => '34',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( 'coral_snowy_titlesize_control', array(
			'label' => __( 'Site title fontsize in px:', 'coral-snowy' ),
			'section' => 'coral_snowy_typography_section',
			'settings' => 'coral_snowy_titlesize_setting',
			'priority' => 10,
			'type' => 'number',
			'input_attrs' => array(
				'min' => 8,
				'max' => 80,
				'step' => 1,
			),
		) );	
		$wp_customize->add_setting( 'tagline_font_setting', array(
			'default'        => 'Default font',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'coral_snowy_sanitize_typography',
		) );
		$wp_customize->add_control( 'tagline_font_control', array(
			'label'   			=> __('Tagline font','coral-snowy'),
			'section' 			=> 'coral_snowy_typography_section',
			'settings' 			=> 'tagline_font_setting',
			'type'    			=> 'select',
			'priority'        	=> 15,
			'choices'    		=> $typoarr,
		) );
		$wp_customize->add_setting( 'coral_snowy_taglinesize_setting', array(
			'default' => '14',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( 'coral_snowy_taglinesize_control', array(
			'label' => __( 'Tagline fontsize in px:', 'coral-snowy' ),
			'section' => 'coral_snowy_typography_section',
			'settings' => 'coral_snowy_taglinesize_setting',
			'priority' => 20,
			'type' => 'number',
			'input_attrs' => array(
				'min' => 8,
				'max' => 80,
				'step' => 1,
			),
		) );	
		$wp_customize->add_setting( 'body_font_setting', array(
			'default'        => 'Default font',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'coral_snowy_sanitize_typography',
		) );
		$wp_customize->add_control( 'body_font_control', array(
			'label'   			=> __('Body font','coral-snowy'),
			'section' 			=> 'coral_snowy_typography_section',
			'settings' 			=> 'body_font_setting',
			'type'    			=> 'select',
			'priority'        	=> 25,
			'choices'    		=> $typoarr,
		) );

		$wp_customize->add_setting( 'body_fontsize_setting', array(
			'default' => '14',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( 'body_fontsize_control', array(
			'label' => __( 'Body fontsize in px:', 'coral-snowy' ),
			'section' => 'coral_snowy_typography_section',
			'settings' => 'body_fontsize_setting',
			'priority' => 30,
			'type' => 'number',
			'input_attrs' => array(
				'min' => 8,
				'max' => 30,
				'step' => 1,
			),
		) );
		$wp_customize->add_setting( 'heading_font_setting', array(
			'default'        => 'Default font',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'coral_snowy_sanitize_typography',
		) );
		$wp_customize->add_control( 'heading_font_control', array(
			'label'   			=> __('Heading font','coral-snowy'),
			'description' 		=> __( 'The h1, h2... fontsizes are based on the body fontsize', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_typography_section',
			'settings' 			=> 'heading_font_setting',
			'type'    			=> 'select',
			'priority'        	=> 35,
			'choices'    		=> $typoarr,
		) );


// Slider section panel
		$wp_customize->add_section( 'coral_snowy_slider_section', array(
			'title' 			=> __( 'Slideshow', 'coral-snowy' ),
			'priority'			=> 35,
			'description' => __( 'Upload at least 1020px wide images with the same aspect ratio!', 'coral-snowy' ),
		) );
		$wp_customize->add_setting( 'front_page_setting', array(
            'default'        	=> '1',
			'transport'         => 'refresh',
			'sanitize_callback' => 'coral_snowy_sanitize_checkbox',
        ) );
        $wp_customize->add_control( 'front_page_control', array(
            'label'   			=> __( 'Display slideshow on frontpage', 'coral-snowy' ),
            'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'front_page_setting',
            'type'    			=> 'checkbox',
            'priority' 			=> 3
        ) );
		$wp_customize->add_setting( 'posts_page_setting', array(
            'default'        	=> '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'coral_snowy_sanitize_checkbox',
        ) );
        $wp_customize->add_control( 'posts_page_control', array(
            'label'   			=> __( 'Display slideshow on blog page', 'coral-snowy'),
            'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'posts_page_setting',
            'type'    			=> 'checkbox',
            'priority' 			=> 4
        ) );
		$wp_customize->add_setting( 'allpages', array(
            'default'        	=> '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'coral_snowy_sanitize_checkbox',
        ) );
        $wp_customize->add_control( 'allpages_control', array(
            'label'   			=> __( 'Always display the slideshow', 'coral-snowy'),
            'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'allpages',
            'type'    			=> 'checkbox',
            'priority' 			=> 5
        ) );
		$wp_customize->add_setting( 'post_id_setting' , array(
			'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( new Coral_Snowy_Text_Description_Control( $wp_customize, 'post_id_control', array(
			'label' 			=> __( 'Comma separated IDs of posts/pages for which you need the slideshow (e.g. 1, 23, 54).', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'post_id_setting',
			'priority' 			=> 6,
		) ) );	
		//-----------------------------------------------
		$wp_customize->add_setting( 'slider_effect_setting', array(
			'default'        => 'fade',
			'sanitize_callback' => 'coral_snowy_sanitize_effect',
		) );
		
		$wp_customize->add_control( 'slider_effect_control', array(
			'label'   			=> __('Slideshow effect','coral-snowy'),
			'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'slider_effect_setting',
			'type'    			=> 'select',
			'priority'        	=> 7,
			'choices'    		=> array(
				'fade' => 'fade',
				'fold' => 'fold',
				'random' => 'random',
				'sliceDown' => 'sliceDown',
				'sliceDownLeft' => 'sliceDownLeft',
				'sliceDownLeft' => 'sliceDownLeft',
				'sliceUp' => 'sliceUp',
				'sliceUpLeft' => 'sliceUpLeft',
				'sliceUpDown' => 'sliceUpDown',
				'sliceUpDownLeft' => 'sliceUpDownLeft',
				'slideInRight' => 'slideInRight',
				'slideInLeft' => 'slideInLeft',
				'boxRandom' => 'boxRandom',
				'boxRain' => 'boxRain',
				'boxRainReverse' => 'boxRainReverse',
				'boxRainGrow' => 'boxRainGrow',
				'boxRainGrowReverse' => 'boxRainGrowReverse',
			),
		) );
		$wp_customize->add_setting( 'slide_animspeed_setting' , array(
			'default'           => '500',
            'sanitize_callback' => 'coral_snowy_sanitize_animspeed',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( new Coral_Snowy_Text_Description_Control( $wp_customize, 'slide_animspeed_control', array(
			'label' 			=> __( 'Animation speed (mS)', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'slide_animspeed_setting',
			'priority' 			=> 8,
		) ) );	
		$wp_customize->add_setting( 'slide_pausetime_setting' , array(
			'default'           => '5000',
            'sanitize_callback' => 'coral_snowy_sanitize_pausetime',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( new Coral_Snowy_Text_Description_Control( $wp_customize, 'slide_pausetime_control', array(
			'label' 			=> __( 'Pause time (mS)', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'slide_pausetime_setting',
			'priority' 			=> 9,
		) ) );	
		// ----------------------------------------------
		$total_slides = 20;
		for ($iSlide=1;$iSlide<$total_slides+1;$iSlide++) {
			if($iSlide == 1)
				$slider_image = get_template_directory_uri() . '/images/flower-bw.jpg';
			else if($iSlide == 2)
				$slider_image = get_template_directory_uri() . '/images/flower.jpg';
			else
				$slider_image = '0';
			
			$wp_customize->add_setting( 'slide_title'.$iSlide , array(
				'default'        	=> '0',
				'transport'         => 'refresh',
				'sanitize_callback' => 'absint',
			));
			$wp_customize->add_control( 'slide_title_control'.$iSlide, array(
				'label' 			=> __( 'Slide title '.$iSlide, 'coral-snowy' ),
				'description' 		=> __( 'Choose a page for the slide title, to which the title is also linked', 'coral-snowy' ),
				'section' 			=> 'coral_snowy_slider_section',
				'settings' 			=> 'slide_title'.$iSlide,
				'type' 				=> 'dropdown-pages',
				'priority' 			=> $iSlide*10,
			) );
			
			$wp_customize->add_setting( 'slide_text_title'.$iSlide , array(
				'default'        	=> '',
				//'transport'         => 'refresh',
				//'sanitize_callback' => 'absint',
			));
			$wp_customize->add_control( 'slide_text_title_control'.$iSlide, array(
				'label' 			=> __( 'Slide text title '.$iSlide, 'coral-snowy' ),
				'description' 		=> __( 'Input title to show as caption', 'coral-snowy' ),
				'section' 			=> 'coral_snowy_slider_section',
				'settings' 			=> 'slide_text_title'.$iSlide,
				//'type' 				=> 'dropdown-pages',
				'priority' 			=> ($iSlide*10) + 6,
			) );

			//$slider_image = get_template_directory_uri() . '/images/flower-bw.jpg';		
			$wp_customize->add_setting( 'slider_image'.$iSlide, array(
				'default'           => $slider_image,
				'transport'         => 'refresh',
				'sanitize_callback' => 'esc_url_raw',
			) );
			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'slider_image_control'.$iSlide, array(
				'label'        		=> __( 'Slider image '.$iSlide, 'coral-snowy' ),
				'section' 			=> 'coral_snowy_slider_section',
				'settings' 			=> 'slider_image'.$iSlide,
				'priority' 			=> ($iSlide*10) + 7,
			) ) );
		}
		/*$wp_customize->add_setting( 'slide_title1' , array(
			'default'        	=> '0',
			'transport'         => 'refresh',
			'sanitize_callback' => 'absint',
		));
		$wp_customize->add_control( 'slide_title_control1', array(
			'label' 			=> __( 'Slide title 1', 'coral-snowy' ),
			'description' 		=> __( 'Choose a page for the slide title, to which the title is also linked', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'slide_title1',
			'type' 				=> 'dropdown-pages',
			'priority' 			=> 10,
		) );

		$slider_image = get_template_directory_uri() . '/images/flower-bw.jpg';		
		$wp_customize->add_setting( 'slider_image1', array(
			'default'           => $slider_image,
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'slider_image_control1', array(
			'label'        		=> __( 'Slider image 1', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'slider_image1',
			'priority' 			=> 16,
		) ) );
		// ----------------------------------------------
		$wp_customize->add_setting( 'slide_title2' , array(
			'default'        	=> '0',
			'transport'         => 'refresh',
			'sanitize_callback' => 'absint',
		));
		$wp_customize->add_control( 'slide_title_control2', array(
			'label' 			=> __( 'Slide title 2', 'coral-snowy' ),
			'description' 		=> __( 'Choose a page for the slide title, to which the title is also linked', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'slide_title2',
			'type' 				=> 'dropdown-pages',
			'priority' 			=> 20,
		) );
		
		$slider_image = get_template_directory_uri() . '/images/flower.jpg';
		$wp_customize->add_setting( 'slider_image2', array(
			'default'           => $slider_image,
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'slider_image_control2', array(
			'label'        		=> __( 'Slider image 2', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'slider_image2',
			'priority' 			=> 26,
		) ) );
		// ----------------------------------------------
		$wp_customize->add_setting( 'slide_title3' , array(
			'default'        	=> '0',
			'transport'         => 'refresh',
			'sanitize_callback' => 'absint',
		));
		$wp_customize->add_control( 'slide_title_control3', array(
			'label' 			=> __( 'Slide title 3', 'coral-snowy' ),
			'description' 		=> __( 'Choose a page for the slide title, to which the title is also linked', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'slide_title3',
			'type' 				=> 'dropdown-pages',
			'priority' 			=> 30,
		) );
		
		$wp_customize->add_setting( 'slider_image3', array(
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'slider_image_control3', array(
			'label'        		=> __( 'Slider image 3', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'slider_image3',
			'priority' 			=> 36,
		) ) );
		// ----------------------------------------------
		$wp_customize->add_setting( 'slide_title4' , array(
			'default'        	=> '0',
			'transport'         => 'refresh',
			'sanitize_callback' => 'absint',
		));
		$wp_customize->add_control( 'slide_title_control4', array(
			'label' 			=> __( 'Slide title 4', 'coral-snowy' ),
			'description' 		=> __( 'Choose a page for the slide title, to which the title is also linked', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'slide_title4',
			'type' 				=> 'dropdown-pages',
			'priority' 			=> 40,
		) );
		
		$wp_customize->add_setting( 'slider_image4', array(
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'slider_image_control4', array(
			'label'        		=> __( 'Slider image 4', 'coral-snowy' ),
			'section' 			=> 'coral_snowy_slider_section',
			'settings' 			=> 'slider_image4',
			'priority' 			=> 46,
		) ) );*/
		// ----------------------------------------------
// Color section panel
		$wp_customize->add_section( 'colors', array(
			'title'          => __( 'Colors', 'coral-snowy' ),
			'priority'       => 40,
		) );		
		$wp_customize->add_setting( 'title_color_setting', array(
			'default'        => '000000',
			'capability' => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_hex_color_no_hash',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'title_color_control', array(
			'label'   => __( 'Site title color', 'coral-snowy' ),
			'section' => 'colors',
			'settings' => 'title_color_setting',
			'priority' => 4,
		) ) );
		$wp_customize->add_setting( 'tagline_color_setting', array(
			'default'        => '000000',
			'capability' => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_hex_color_no_hash',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tagline_color_control', array(
			'label'   => __( 'Tagline Color', 'coral-snowy' ),
			'section' => 'colors',
			'settings' => 'tagline_color_setting',
			'priority' => 6,
		) ) );
		$wp_customize->add_setting( 'background_color', array(
			'default'        => 'fafafa',
			'capability' => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_hex_color_no_hash',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
			'label'   => __( 'Background Color', 'coral-snowy' ),
			'section' => 'colors',
			'settings' => 'background_color',
			'priority' => 8,
		) ) );
}
add_action( 'customize_register', 'coral_snowy_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function coral_snowy_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function coral_snowy_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function coral_snowy_customize_preview_js() {
	wp_enqueue_script( 'coral_snowy_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'coral_snowy_customize_preview_js' );
