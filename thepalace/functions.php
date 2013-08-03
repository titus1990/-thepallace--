<?php
/* begin aurelien-panel functions */
$ap_theme_name = 'thepalace';
$ap_message = '';

require_once( get_template_directory() . '/aurelien-panel/aurelien-panel-options.php' );

$ap_options_saved = get_option($ap_theme_name . '_theme_options');
foreach ( $ap_options as $key => $ap_option ) {
	if ( isset($ap_options_saved[$key]) ) {
		$ap_options[$key]['val'] = $ap_options_saved[$key]['val'];
	}
}

add_action('admin_menu', 'add_aurel_panel_in_menu');

function add_aurel_panel_in_menu() {
	global $ap_theme_name;
	$page = add_menu_page('Theme Options', 'Theme Options', 'edit_theme_options', 'theme_options', 'aurel_panel_launch_display');
	add_action('admin_print_styles-' . $page, 'ap_init');
}

if ( isset($_REQUEST['aurel-panel-action']) && ($_REQUEST['aurel-panel-action'] == 'activate-hotel') && check_admin_referer('aurelien-panel') && current_user_can('edit_theme_options') ) {
	$ap_options['ap_activate_hotel']['val'] = 'yes';
 	update_option( $ap_theme_name . '_theme_options', $ap_options );
	$ap_message = 'Booking system activated.';
}
	
if ( isset($_REQUEST['aurel-panel-action']) && ($_REQUEST['aurel-panel-action'] == 'deactivate-hotel') && check_admin_referer('aurelien-panel') && current_user_can('edit_theme_options') ) {
	$ap_options['ap_activate_hotel']['val'] = 'no'; 	
	update_option( $ap_theme_name . '_theme_options', $ap_options );
	$ap_message = 'Booking system deactivated.';
}

if ( $ap_options['ap_activate_hotel']['val'] == 'yes' ) {
	require_once( get_template_directory() . '/aurelien-hotel/main.php' );
}

function aurel_panel_launch_display() {
	require_once( get_template_directory() . '/aurelien-panel/aurelien-panel-display.php' );
	aurel_panel_display();
}

function ap_init() {
	
	global $ap_options, $ap_options_defaults, $ap_message, $ap_theme_name, $ap_dispatch;
	
	if ( isset($_REQUEST['aurel-panel-action']) && ($_REQUEST['aurel-panel-action'] == 'save') && check_admin_referer('aurelien-panel') && current_user_can('edit_theme_options') ) {
		$need_json_decode = array( 'widget-areas-manager', 'global-slider-manager', 'slider-manager', 'fsslider-manager' , 'lists-of-posts-manager', 'single-posts-manager', 'categories-manager' );
		foreach ( $ap_options as $key => $value ) {
			if ( isset($_REQUEST[$key]) ) {
				if ($ap_options[$key]['type'] == 'check-boxes') {
					$ap_options[$key]['val'] = $_REQUEST[$key];
				} else if ( in_array($ap_options[$key]['type'], $need_json_decode)) {
					$ap_options[$key]['val'] = json_decode(stripslashes($_REQUEST[$key]), true);
				} else {
					$ap_options[$key]['val'] = stripslashes(trim($_REQUEST[$key]));
				}
			}
		}
		foreach ( $ap_options as $key => $value ) {
			$ap_options_to_save[$key]['val'] = $value['val'];
		}
		update_option( $ap_theme_name . '_theme_options', $ap_options_to_save ); 
		die('Options saved.'); 
	}
	
	if ( isset($_REQUEST['aurel-panel-action']) && ($_REQUEST['aurel-panel-action'] == 'reset') && check_admin_referer('aurelien-panel') && current_user_can('edit_theme_options') ) {
		$ap_options = $ap_options_defaults;
		foreach ( $ap_options_defaults as $key => $value ) {
			$ap_options_to_save[$key]['val'] = $value['val'];
		}
		update_option( $ap_theme_name . '_theme_options', $ap_options_to_save );
		$ap_message = 'All options have been set to default.';
	}
	
	if ( isset($_REQUEST['aurel-panel-action']) && ($_REQUEST['aurel-panel-action'] == 'load_options') && check_admin_referer('aurelien-panel') && current_user_can('edit_theme_options') ) {
		
		if( !class_exists('WP_Http') ) {
			include_once( ABSPATH . WPINC. '/class-http.php' );
		}
		global $wpdb;
		$images_url = array(
			'image-1' => '', 
			'image-2' => '', 
			'image-3' => '', 
		);
		foreach ( $images_url as $image => $url ) {
			$attachement_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $image . "'" );
			if ( $attachement_id === NULL ) {
				$photo = new WP_Http();
				$photo = $photo->request( get_template_directory_uri() . '/img-demo/' . $image . '.jpg' );
				$attachment = wp_upload_bits( $image . '.jpg', null, $photo['body'], '2012/01' );
				$filetype = wp_check_filetype( basename( $attachment['file'] ), null );
				$postinfo = array(
					'post_mime_type'	=> $filetype['type'],
					'post_title'		=> $image,
					'post_content'	=> '',
					'post_status'	=> 'inherit'
				);
				$images_url[$image] = $attachment['url'];
				$attach_id = wp_insert_attachment( $postinfo, $attachment['file'] );
				if( !function_exists('wp_generate_attachment_data') ) {
					require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				}
				$attach_data = wp_generate_attachment_metadata( $attach_id, $attachment['file'] );
				wp_update_attachment_metadata( $attach_id,  $attach_data );
			} else {
				$images_url[$image] = wp_get_attachment_url($attachement_id);
			}
		}
		
		$ap_options_demo = $ap_options_defaults;
		
		$ap_options_demo['ap_global_slider_manager']['val'][0]['name'] = 'slider_a';
		$ap_options_demo['ap_global_slider_manager']['val'][0]['type'] = 'Full-width';
		$ap_options_demo['ap_global_slider_manager']['val'][0]['options'] = array(1000,6000,'yes',400);
		$ap_options_demo['ap_global_slider_manager']['val'][0]['slides'][0]['caption'] = 'Image 1';
		$ap_options_demo['ap_global_slider_manager']['val'][0]['slides'][0]['image_url'] = $images_url['image-1'];
		$ap_options_demo['ap_global_slider_manager']['val'][0]['slides'][1]['caption'] = 'Image 2';
		$ap_options_demo['ap_global_slider_manager']['val'][0]['slides'][1]['image_url'] = $images_url['image-2'];
		$ap_options_demo['ap_global_slider_manager']['val'][0]['slides'][2]['caption'] = 'Image 3';
		$ap_options_demo['ap_global_slider_manager']['val'][0]['slides'][2]['image_url'] = $images_url['image-3'];
		

		$ap_options_demo['ap_global_slider_manager']['val'][1]['name'] = 'slider_b';
		$ap_options_demo['ap_global_slider_manager']['val'][1]['type'] = 'Full-screen';		
		$ap_options_demo['ap_global_slider_manager']['val'][1]['options'] = array(1000,6000,'no');
		$ap_options_demo['ap_global_slider_manager']['val'][1]['slides'][0]['caption'] = 'Image 1';
		$ap_options_demo['ap_global_slider_manager']['val'][1]['slides'][0]['image_url'] = $images_url['image-1'];
		$ap_options_demo['ap_global_slider_manager']['val'][1]['slides'][1]['caption'] = 'Image 2';
		$ap_options_demo['ap_global_slider_manager']['val'][1]['slides'][1]['image_url'] = $images_url['image-2'];
		$ap_options_demo['ap_global_slider_manager']['val'][1]['slides'][2]['caption'] = 'Image 3';
		$ap_options_demo['ap_global_slider_manager']['val'][1]['slides'][2]['image_url'] = $images_url['image-3'];
			
		$ap_options_demo['ap_global_slider_manager']['val'][2]['name'] = 'slider_c';
		$ap_options_demo['ap_global_slider_manager']['val'][2]['type'] = 'Top-content';		
		$ap_options_demo['ap_global_slider_manager']['val'][2]['options'] = array(1000,6000,'yes',400);
		$ap_options_demo['ap_global_slider_manager']['val'][2]['slides'][0]['caption'] = 'Image 1';
		$ap_options_demo['ap_global_slider_manager']['val'][2]['slides'][0]['image_url'] = $images_url['image-1'];
		$ap_options_demo['ap_global_slider_manager']['val'][2]['slides'][1]['caption'] = 'Image 2';
		$ap_options_demo['ap_global_slider_manager']['val'][2]['slides'][1]['image_url'] = $images_url['image-2'];
		$ap_options_demo['ap_global_slider_manager']['val'][2]['slides'][2]['caption'] = 'Image 3';
		$ap_options_demo['ap_global_slider_manager']['val'][2]['slides'][2]['image_url'] = $images_url['image-3'];
	
		$ap_options_demo['ap_widget_areas_manager']['val'] = array('rooms','layouts','news_and_deals','features');
		
		$generic_category = array(
			'category' => '',
			'visible' => 'no',
			'layout' => '',
			'sidebar' => '',
			'display-title' => 'yes',
			'meta' => array('date','categories','tags'),
			'show-thumbnail' => 'yes',
			'thumbnail-links' => 'post',
			'content' => 'excerpt',
			'learn-more' => 'button',
			'header-image' => '',
			'slider' => 'no-slider',
			'background-image' => '',
			'footer-image' => '',
			'disable-comments' => 'yes'
		);
		$rooms_category = $generic_category;
		$news_and_deals_category = $generic_category;
		$portfolio_category = $generic_category;
		$layout_left_sidebar_category = $generic_category;
		$layout_right_sidebar_category = $generic_category;
		$layout_full_width_category = $generic_category;
		$layout_three_cols_category = $generic_category;
		
		$rooms_category['category'] = 'rooms';
		$rooms_category['layout'] = 'one-col-left-sidebar';
		$rooms_category['sidebar'] = 'rooms';
		$rooms_category['meta'] = array();
		
		$news_and_deals_category['category'] = 'news-and-deals';
		$news_and_deals_category['layout'] = 'one-col-right-sidebar';
		$news_and_deals_category['sidebar'] = 'news_and_deals';
		$news_and_deals_category['disable-comments'] = 'no';
		$news_and_deals_category['meta'] = array('date','categories','tags','comments');
		
		$portfolio_category['category'] = 'portfolio';
		$portfolio_category['layout'] = 'three-cols';
		$portfolio_category['meta'] = array();
		$portfolio_category['thumbnail-links'] = 'full-size image';
		$portfolio_category['content'] = 'none';
		$portfolio_category['learn-more'] = 'none';

		$layout_left_sidebar_category['category'] = 'layout-left-sidebar';
		$layout_left_sidebar_category['layout'] = 'one-col-left-sidebar';
		$layout_left_sidebar_category['sidebar'] = 'layouts';
		$layout_left_sidebar_category['meta'] = array();
		
		$layout_right_sidebar_category['category'] = 'layout-right-sidebar';
		$layout_right_sidebar_category['layout'] = 'one-col-right-sidebar';
		$layout_right_sidebar_category['sidebar'] = 'layouts';
		$layout_right_sidebar_category['meta'] = array();
		
		$layout_full_width_category['category'] = 'layout-full-width';
		$layout_full_width_category['layout'] = 'full-width';
		$layout_full_width_category['sidebar'] = 'layouts';
		$layout_full_width_category['meta'] = array();
		
		$layout_three_cols_category['category'] = 'layout-three-colunms';
		$layout_three_cols_category['layout'] = 'three-cols';
		$layout_three_cols_category['sidebar'] = 'layouts';
		$layout_three_cols_category['meta'] = array();
		
		$ap_options_demo['ap_categories_manager']['val'] = array($rooms_category,$news_and_deals_category,$portfolio_category,$layout_left_sidebar_category,$layout_right_sidebar_category,$layout_full_width_category,$layout_three_cols_category);
		
		$ap_options = $ap_options_demo;
		foreach ( $ap_options_demo as $key => $value ) {
			$ap_options_to_save[$key]['val'] = $value['val'];
		}
		update_option( $ap_theme_name . '_theme_options', $ap_options_to_save );
		
		update_option('ah_mail_confirmation_content', 
		"Hello,<br/><br/>\r\nThis is to confirm your booking in our hotel.<br/>\r\n" .
		"Please find below all the details of your booking:<br/>\r\n[booking-data]<br/><br/>\r\n" .
		"Regards,<br/><br/>\r\nThe hotel manager");
		
		update_option('ah_mail_refusal_content',
		"Hello,<br/><br/>\r\n" .
		"We unfortunately can not accept your booking request. Please phone our reservation team to arrange a new booking.<br/><br/>\r\n" .
		"Regards,<br/><br/>\r\n" .
		"The hotel manager.");
		
		update_option('ah_mail_subject', 'Your reservation' );
		
		update_option('ah_mail_from', 'Palace <info@the-palace.com>' );
		
		$ap_message = 'Demo options loaded.';
	}

	if ( isset($_REQUEST['aurel-panel-action']) && ($_REQUEST['aurel-panel-action'] == 'load_widgets') && check_admin_referer('aurelien-panel') && current_user_can('edit_theme_options') ) {
		
		$sidebars_widgets = array (
			'wp_inactive_widgets' => array(),
			'default_sidebar' => array(),
			'top' => array(),
			'footer_1' => array( 1 => 'nav_menu-0' ),
			'footer_2' => array( 1 => 'ap_category_posts-0', 2 => 'ap_category_posts-1' ),
			'footer_3' => array( 1 => 'text-2', 2 => 'ap_social_widget-2' ),
			'footer_4' => array( 1 => 'ap_gallery_widget-2' ),
			'rooms' => array( 1 => 'nav_menu-1', 2 => 'text-3' ),
			'double_room' => array( 1 => 'text-4', 2 => 'nav_menu-2', 3 => 'text-5' ),
			'layouts' => array( 1 => 'nav_menu-3'),
			'features' => array( 1 => 'nav_menu-4'),
			'news_and_deals' => array ( 1 => 'ap_category_posts-0', 2 => 'text-6' ),
			'array_version' => 3
		);
		$widget_nav_menu = array (
			0 => array (
				'title' => 'Navigation',
				'nav_menu' => 14
			),
			1 => array (
				'title' => 'Our rooms',
				'nav_menu' => 12
			),
			2 => array (
				'title' => 'Our rooms',
				'nav_menu' => 12
			),
			3 => array (
				'title' => 'Layout menu',
				'nav_menu' => 13
			),
			4 => array (
				'title' => 'Features menu',
				'nav_menu' => 11
			),
			'_multiwidget' => 1
		);
		$widget_ap_category_posts = array (
			0 => array (
				'title' => 'News and Deals',
				'category' => 'news-and-deals',
				'max' => ''
			),
			1 => array (
				'title' => 'Rooms',
				'category' => 'rooms',
				'max' => ''
			),
			2 => array (
				'title' => 'News and Deals',
				'category' => 'news-and-deals',
				'max' => ''
			),
			'_multiwidget' => 1
		);
		$widget_ap_social_widget = unserialize( 'a:2:{i:2;a:76:{s:5:"title";s:9:"Follow us ";s:3:"aim";s:0:"";s:5:"apple";s:0:"";s:4:"bebo";s:0:"";s:7:"blogger";s:0:"";s:10:"brightkite";s:0:"";s:5:"cargo";s:0:"";s:9:"delicious";s:0:"";s:11:"designfloat";s:0:"";s:9:"designmoo";s:0:"";s:10:"deviantart";s:0:"";s:4:"digg";s:0:"";s:8:"digg_alt";s:0:"";s:6:"dopplr";s:0:"";s:8:"dribbble";s:0:"";s:5:"email";s:0:"";s:5:"ember";s:0:"";s:8:"evernote";s:0:"";s:8:"facebook";s:19:"http://facebook.com";s:6:"flickr";s:0:"";s:6:"forrst";s:0:"";s:10:"friendfeed";s:0:"";s:8:"gamespot";s:0:"";s:6:"google";s:0:"";s:11:"google_plus";s:17:"http://google.com";s:12:"google_voice";s:0:"";s:11:"google_wave";s:0:"";s:10:"googletalk";s:0:"";s:7:"gowalla";s:0:"";s:11:"grooveshark";s:0:"";s:5:"ilike";s:0:"";s:17:"komodomedia_azure";s:0:"";s:16:"komodomedia_wood";s:0:"";s:6:"lastfm";s:0:"";s:8:"linkedin";s:0:"";s:4:"mixx";s:0:"";s:8:"mobileme";s:0:"";s:9:"mynameise";s:0:"";s:7:"myspace";s:0:"";s:8:"netvibes";s:0:"";s:8:"newsvine";s:0:"";s:6:"openid";s:0:"";s:5:"orkut";s:0:"";s:7:"pandora";s:0:"";s:6:"paypal";s:0:"";s:6:"picasa";s:0:"";s:8:"pinboard";s:0:"";s:11:"playstation";s:0:"";s:5:"plurk";s:0:"";s:9:"posterous";s:0:"";s:3:"qik";s:0:"";s:4:"rdio";s:0:"";s:10:"readernaut";s:0:"";s:6:"reddit";s:0:"";s:6:"roboto";s:0:"";s:3:"rss";s:0:"";s:9:"sharethis";s:0:"";s:5:"skype";s:0:"";s:8:"slashdot";s:0:"";s:5:"steam";s:0:"";s:11:"stumbleupon";s:0:"";s:10:"technorati";s:0:"";s:6:"tumblr";s:0:"";s:7:"twitter";s:18:"http://twitter.com";s:7:"viddler";s:0:"";s:5:"vimeo";s:0:"";s:4:"virb";s:0:"";s:7:"windows";s:0:"";s:9:"wordpress";s:0:"";s:5:"xanga";s:0:"";s:4:"xing";s:0:"";s:5:"yahoo";s:0:"";s:9:"yahoobuzz";s:0:"";s:4:"yelp";s:0:"";s:7:"youtube";s:18:"http://youtube.com";s:7:"zootool";s:0:"";}s:12:"_multiwidget";i:1;}' );
		$widget_text = array (
			2 => array (
				'title' => 'Contact details',
				'text' => 'The Palace Hotel<br/>St Stephens Green<br/>Dublin, Ireland<br/>Phone: +353 (0)87 9326 988',
				'filter' => true
			),
			3 => array (
				'title' => 'Book a room',
				'text' => '[contact-form-7 id="683" title="Reservation form sidebar B"]',
				'filter' => false
			),
			4 => array (
				'title' => 'Custom sidebar',
				'text' => 'This is a custom sidebar for the Double room.',
				'filter' => false
			),
			5 => array (
				'title' => 'Book a Double room',
				'text' => '[contact-form-7 id="1120" title="Reservation form Double room"]',
				'filter' => false
			),
			6 => array (
				'title' => 'Book a room',
				'text' => '[contact-form-7 id="683" title="Reservation form sidebar B"]',
				'filter' => false
			),
			'_multiwidget' => 1
		);
		$widget_ap_gallery_widget = array (
			2 => array (
				'title' => 'Gallery',
				'images' => 'image-1,image-2,image-3,image-2,image-3,image-1',
				'link_text' => '',
				'link_url' => '',
				'link_parameters' => ''
			),
			'_multiwidget' => 1
		);
		update_option('widget_nav_menu', $widget_nav_menu);
		update_option('widget_ap_social_widget', $widget_ap_social_widget);
		update_option('widget_ap_category_posts', $widget_ap_category_posts);
		update_option('widget_text', $widget_text);
		update_option('widget_ap_gallery_widget', $widget_ap_gallery_widget);
		
		update_option('sidebars_widgets', $sidebars_widgets);
		
		$ap_message = 'Demo widgets loaded.';
	}
	
	wp_enqueue_style('ap-style', get_template_directory_uri().'/aurelien-panel/css/aurelien-panel.css');
	wp_enqueue_style('ap-colorpicker-style', get_template_directory_uri().'/aurelien-panel/colorpicker/jquery.miniColors.css');
	wp_enqueue_style('ap-jq-ui-style', get_template_directory_uri().'/aurelien-panel/css/jq-ui/jquery-ui.css');
	wp_enqueue_style('thickbox');
	
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-resizable');
	wp_enqueue_script('ap-colorpicker', get_template_directory_uri().'/aurelien-panel/colorpicker/jquery.miniColors.min.js');
	wp_enqueue_script('ap-jqform', get_template_directory_uri().'/aurelien-panel/js/jquery.form.js');
	wp_enqueue_script('ap-cookies', get_template_directory_uri().'/aurelien-panel/js/jquery.gateau.js');
	wp_enqueue_script('ap-script', get_template_directory_uri().'/aurelien-panel/js/aurelien-panel.js');
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
}

add_filter( 'gettext', 'change_media_button_text', 10, 3 );
function change_media_button_text( $translation, $text, $domain ){
    if ( $text == 'Insert into Post' ) {
        return 'Use this image';
    }
    return $translation;
}

if (function_exists('qtrans_convertURL')) {
	add_filter('home_url', 'qtrans_convertURL');
}
/* end aurelien-panel functions */

/* begin widget areas */
function ap_widgets_area_init() {
	register_sidebar(
		array(
			'id' => 'default_sidebar', 
			'name' => 'Default sidebar', 
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">', 
			'after_title' => '</h3>'
		)
	); 
	register_sidebar(
		array(
			'id' => 'top', 
			'name' => 'Top area',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">', 
			'after_title' => '</h3>'
		)
	);
	register_sidebar(
		array(
			'id' => 'footer_1', 
			'name' => 'Footer first column', 
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">', 
			'after_title' => '</h3>'
		)
	);
	register_sidebar(
		array(
			'id' => 'footer_2', 
			'name' => 'Footer second column', 
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">', 
			'after_title' => '</h3>'
		)
	);
	register_sidebar(
		array(
			'id' => 'footer_3', 
			'name' => 'Footer third column', 
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">', 
			'after_title' => '</h3>'
		)
	);
	register_sidebar(
		array(
			'id' => 'footer_4', 
			'name' => 'Footer fourth column', 
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">', 
			'after_title' => '</h3>'
		)
	);
	global $ap_options;
	$ap_sidebars = $ap_options['ap_widget_areas_manager']['val'];
	foreach ($ap_sidebars as $ap_sidebar) {
		register_sidebar(
			array(
				'id' => $ap_sidebar, 
				'name' => $ap_sidebar, 
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h3 class="widget-title">', 
				'after_title' => '</h3>'
			)
		);
	}
}

add_action( 'widgets_init', 'ap_widgets_area_init' );
/* end widget areas */

/* begin custom widgets */
function ap_register_widgets() {
	require_once( get_template_directory() . '/aurelien-panel/aurelien-panel-widgets.php' );
	register_widget( 'AP_Social_Widget' );
	register_widget( 'AP_Category_Posts' );
	register_widget( 'AP_Gallery_Widget' );
}

add_action( 'widgets_init', 'ap_register_widgets' );
/*end custom widgets */

/* begin shortcodes */
function ap_sc_button($atts, $content = null) {
	extract(shortcode_atts(array(  
		'link' => '',
		'bgcolor' => '',
		'size' => '',
		'style' => '',
		'class' => '',
		'parameters' => ''
	), $atts));
	if ( $bgcolor != '' ) {
		$style = 'style="background-color: ' . $bgcolor . '; border-color: ' . $bgcolor . '; border-bottom-color: rgba(0,0,0,0.2); ' . $style . ';" '; 
	} else {
		$style = 'style="border-bottom-color: rgba(0,0,0,0.2); ' . $style . '" ';
	}	
	if ( $size == 'big' ) {
		$class .= ' palace-button-big';
	}
	return '<a ' . $parameters . ' class="palace-button ' . $class . '" ' . $style . 'href="' . $link . '">' . $content . '</a>';
}

function ap_sc_textured_top_area_full_width($atts, $content = null) {
	return '<div class="textured-area textured-area-top-full-width"><div class="textured-area-content">' . ap_remove_wpautop($content) . '</div></div>';
}

function ap_sc_textured_middle_area_full_width($atts, $content = null) {
	return '<div class="textured-area textured-area-middle-full-width"><div class="textured-area-content">' . ap_remove_wpautop($content) . '</div></div>';
}

function ap_sc_textured_bottom_area_full_width($atts, $content = null) {
	return '<div class="textured-area textured-area-bottom-full-width"><div class="textured-area-content">' . ap_remove_wpautop($content) . '</div></div>';
}

function ap_sc_textured_top_left_area($atts, $content = null) {
	return '<div class="textured-area textured-area-top-left one-third column alpha"><div class="textured-area-content">' . ap_remove_wpautop($content) . '</div></div>';
}

function ap_sc_textured_top_right_area($atts, $content = null) {
	return '<div class="textured-area textured-area-top-right one-third column omega"><div class="textured-area-content">' . ap_remove_wpautop($content) . '</div></div><div class="clear"></div>';
}

function ap_sc_gallery($atts) {
	extract(shortcode_atts(array(  
		'images_from_media_library' => '',
		'ids' => '',
		'link' => 'file',
		'columns' => 3,
		'orderby' => '',
		'margin_width' => 1
    ), $atts));
	
	function get_caption($id) {
		$post = get_post((int)$id);
		return $post->post_excerpt;
	}
	
	$gal_num = rand();
	$col_width = (100 - $margin_width * ( $columns - 1)) / $columns;
	$img_num_in_col = 1;
	$return_string = '<div class="gallery-wp">';
	if ( $images_from_media_library != '' ) {
		$image_names = explode(',', $images_from_media_library);
		$attachments_id = array();
		global $wpdb;
		foreach ( $image_names as $image_name ) {
			$attachments_id[] = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . trim($image_name) . "'" );
		}
	} else {
		$attachments_id = explode(',', $ids);
	}
	if ( $orderby == 'rand' ) {
		shuffle($attachments_id);
	}
	foreach ($attachments_id as $attachment_id) {
		$return_string .= '<div class="gallery-item" style="width: ' . $col_width . '%">';
		if ( $link == 'post' ) {
			$return_string .= wp_get_attachment_link($attachment_id, 'image-1000x562-r169', true, false);
		} else {
			$image_1000_562_src = fImg::resize( wp_get_attachment_url($attachment_id), 1000, 562, true );
			$return_string .= '<a rel="ap-light-box[wp-gallery-' . $gal_num . ']" href="' . wp_get_attachment_url($attachment_id) .'"><img src="' . $image_1000_562_src . '" alt="" /></a>';
		}
		$return_string .= '<p class="gallery-caption">' . get_caption($attachment_id) . '</p>';
		$return_string .= '</div>';
		if ( $img_num_in_col == $columns ) {
			$return_string .= '<div class="clear"></div>';
			$img_num_in_col = 1;
		} else {
			$return_string .= '<div class="gallery-margin" style="width: ' . $margin_width . '%"></div>';
			$img_num_in_col++;
		}
	}
	$return_string .= '<p class="clear"></p>';
	$return_string .= '</div>';
	return $return_string;
}

function ap_sc_thumbnails_gallery($atts) {
	extract(shortcode_atts(array(  
		'images_from_media_library' => '',
		'link_to_full_size' => 'no' //no, current, all
    ), $atts));
	$return_string = '<div class="gallery-ap">';
	global $wpdb;
	$attachements = explode(',',$images_from_media_library);
	$attachement_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . trim($attachements[0]) . "'" );
	$image_src = wp_get_attachment_image_src($attachement_id, 'image-width-930');
	if ( $link_to_full_size != 'no' ) {
		$link_begin = '<a href="' . wp_get_attachment_url($attachement_id) . '" class="gallery-ap-light-box" data-light-box-all="';
		if ( $link_to_full_size == 'all' ) {
			$link_begin .= 'yes';
		} else {
			$link_begin .= 'no';
		}
		$link_begin .= '">';
		$link_end = '</a>';
	} else {
		$link_begin = '';
		$link_end = '';
	}
	$return_string .= '<div class="gallery-big-image">' . $link_begin . '<img src="' . $image_src[0] . '" class="img-gallery-ap-full-size" />' . $link_end . '</div>';
	$return_string .= '<div class="gallery-slider" data-processing="no">';
	$return_string .= '<div class="gallery-slide">';
	foreach ($attachements as $attachement) {
		$attachement_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . trim($attachement) . "'" );
		$image_width_930_src = wp_get_attachment_image_src($attachement_id, 'image-width-930');
		if ( $link_to_full_size != 'no' ) {
			$image_full_size_src = wp_get_attachment_url($attachement_id);
		} else {
			$image_full_size_src = '';
		}
		$return_string .= '<a href="' . $image_width_930_src[0] .'" data-full-size-src="' . $image_full_size_src . '">' . wp_get_attachment_image($attachement_id , 'image-90x90') . '</a>';
	}
	$return_string .= '</div></div>';
	$return_string .= '<div class="gallery-controls"><a href="#" class="slider-content-button-left"></a><a href="#" class="slider-content-button-right"></a>';
	$return_string .= '<div class="slider-content-button-left-disabled"></div><div class="slider-content-button-right-disabled"></div></div>';
	$return_string .= '</div>';
	return $return_string;
}

function do_slider_content($atts, $type) {
	extract(shortcode_atts(array(  
        'width' => '',
		'category' => '',
		'images' => '',
		'images_from_media_library' => '',
		'image' => 'big', //none, big, small
		'image_link' => 'post', // none, post, full-size
		'title' => 'link', // none, link, raw
		'content' => 'content', // none, excerpt, content
		'link' => 'button', // none, link, button
		'max' => '999',
		'autoplay' => 'no' // no or value in ms
    ), $atts));
	$wrap_code_begin = '<div class="slider-' . $type . ' slider-content-first-container" data-autoplay="' . $autoplay . '"><a class="slider-content-button-left" href="#"></a><a class="slider-content-button-right" href="#"></a><div class="slider-content-button-left-disabled"></div><div class="slider-content-button-right-disabled"></div><div class="slider-content-second-container"><div class="slider-content-third-container">';
	$wrap_code_end = '</div></div></div>';
	$return_string = '';
	$slides = '';
	if ( $type == 'type-a' ) {
		$slide_width = ' ' . sc_width_to_class($width);
		if ( ($width == 'one-third') || ($width == 'one-fourth') ) {
			$image_size = 'image-420x150';
		} elseif ( $width == 'post-with-sidebar' ) {
			$image_size = 'image-630x210';
		} else {
			$image_size = 'image-930x310';
		}
	} else {
		$slide_width = '';
		$image_size = 'image-420x150';
	}
	if ( ($category == 'slider_images') || ($category == 'media-library') || ($images_from_media_library != '') || ($images != '') ) {
		if ( $images != '' ) {
			$attachements = explode(',',$images);
		} else {
			$attachements = explode(',',$images_from_media_library);
		}
		global $wpdb;
		$gal_num = rand();
		foreach ($attachements as $attachement) {
			$slides .= '<div class="slider-content-slide' . $slide_width . '">';
			$attachement_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . trim($attachement) . "' AND post_type = 'attachment'" );
			if ( $image_link == 'full-size' ) {
				$link_begin = '<a href="' . wp_get_attachment_url( $attachement_id , 'full') . '" rel="ap-light-box[' . $gal_num . ']">';
				$link_end = '</a>';
			} else {
				$link_begin = '';
				$link_end = '';
			}
			$slides .= '<p class="scale-with-grid">' . $link_begin . wp_get_attachment_image( $attachement_id , $image_size) . $link_end . '</p>';
			$att = get_page($attachement_id);
			$slides .= '<p>' . $att->post_excerpt . '</p>';
			$slides .= '</div>';
		}
	} else {
		query_posts(array('category_name' => $category, 'posts_per_page' => $max));
		if (have_posts()) :
			while (have_posts()) : the_post();
				$slides .= '<div class="slider-content-slide' . $slide_width . '">';
				if ( $image_link  == 'post' ) {
					$link_begin = '<a href="' . get_permalink() . '">';
					$link_end = '</a>';
				} elseif ( $image_link == 'full-size' ) {
					$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
					$link_begin = '<a href="' . $full_image_url[0] . '" rel="ap-light-box">';
					$link_end = '</a>';
				} else {
					$link_begin = '';
					$link_end = '';
				}
				if ( get_the_post_thumbnail( get_the_ID(), 'image-420x150' ) != '' ) {
					if ( $image == 'big' ) {
						$slides .= '<p class="scale-with-grid">' . $link_begin . get_the_post_thumbnail( get_the_ID(), $image_size ) . $link_end . '</p>';
					} elseif ( $image == 'small' ) {
						$slides .= '<p class="alignleft">' . $link_begin . get_the_post_thumbnail( get_the_ID(), 'image-90x90' ) . $link_end . '</p>';
					}
				}
				if ( $title == 'link' ) {
					$slides .= '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
				} elseif ( $title == 'raw' ) {
					$slides .= '<h3>' . get_the_title() . '</h3>';
				}
				if ( $content == 'excerpt' ) {
					$slides .= '<p>' . do_shortcode(get_the_excerpt()) . '</p>';
				} elseif ( $content == 'content' ) {
					$slides .= '<p>' . do_shortcode(get_the_content()) . '</p>';
				}
				if ( $link == 'link' ) {
					$slides .= '<p><a href="' . get_permalink() . '" rel="bookmark">' . __('Learn more &raquo;', 'thepalace') . '</a></p>';
				} elseif ( $link == 'button' ) {
					$slides .= '<p class="slider-content-slide-button"><a href="' . get_permalink() . '" class="palace-button" rel="bookmark">' . __('Learn more &raquo;', 'thepalace') . '</a></p>';
				}
				$slides .= '</div>';
			endwhile;
		endif;
		wp_reset_query();
	}
	return $wrap_code_begin . $slides . $wrap_code_end;
}

function ap_sc_slider_type_a($atts) {
	return do_slider_content($atts, 'type-a');
}

function ap_sc_slider_type_b($atts) {
	return do_slider_content($atts, 'type-b');
}

function ap_sc_slider_type_c($atts) {
	extract(shortcode_atts(array( 
		'name' => '',
		'position' => '', // set to top to display on top without padding
		'width' => '' // set to full to display a full width slider without margin
    ), $atts));
	$return_string = '';
	global $ap_options;
	$slider_data = array();
	foreach ( $ap_options['ap_global_slider_manager']['val'] as $slider ) {
		if ( $slider['name'] == $name ) {
			$slider_data = $slider;
			break;
		}
	}
	if ( sizeof($slider_data) == 0 ) {
		$return_string = 'The slider <b>' . $name . '</b> does not exist.';
	} else {
		$class = 'sc-fws-container';
		if ( $position == 'top' ) {
			$class .= ' position-top';
		} 
		if ( $width == 'full' ) {
			$class .= ' full-width';
		}
		$return_string .= '<div class="' . $class . '" data-animation-speed="' . $slider_data['options'][0] . '" data-pause-time="' . $slider_data['options'][1] . '" data-autoplay="' . $slider_data['options'][2] . '">';
		$p = 'style="position: relative; display: block; opacity: 0"';
		foreach( $slider_data['slides'] as $slide ) {
			$return_string .= '<img ' . $p . ' src="' . fImg::resize( $slide['image_url'], 1000, (int)$slider_data['options'][3], true ) . '" data-caption="' . __( $slide['caption'] ) . '" alt="" />';
			$p = '';
		}
		$return_string .= '<div class="sc-fws-borders"></div>';
		$return_string .= '<a href="#" class="sc-fws-button-left">&lsaquo;</a>';
		$return_string .= '<a href="#" class="sc-fws-button-right">&rsaquo;</a>';
		$return_string .= '<div class="sc-fws-caption"></div>';
		$return_string .= '</div>'; 
	}
	return $return_string;
}

function sc_add_clear_to_columns($width) {
	if ( substr($width, -5) == '-last' ) {
		return '<div class="clear"></div>';
	} else {
		return '';
	}
}

function sc_width_to_class($width) {
	$margin = '';
	if ( substr($width, -6) == '-first' ) {
		$margin = 'alpha';
		$width = str_replace('-first', '', $width);
	} elseif ( substr($width, -5) == '-last' ) {
		$margin = 'omega';
		$width = str_replace('-last', '', $width);
	}
	switch ($width) {
		case 'one-half': return 'eight columns ' . $margin; break;
		case 'one-third': return 'one-third column ' . $margin; break;
		case 'two-thirds': return 'two-thirds column ' . $margin; break;
		case 'one-fourth': return 'four columns ' . $margin; break;
		case 'three-fourth': return 'twelve columns ' . $margin; break;
		case 'post-with-sidebar': return 'eleven columns ' . $margin; break;
		case 'full': return 'sixteen columns alpha omega'; break;
	}
}
	
function ap_sc_column($atts, $content = null) {
	extract(shortcode_atts(array(  
        'width' => ''  
    ), $atts));
	return '<div class="' . sc_width_to_class($width) . '">' . ap_remove_wpautop($content) . '</div>' . sc_add_clear_to_columns($width);
}

function ap_sc_room_features($atts) {
	extract(shortcode_atts(array(
		'cols' => '1',
		'icons' => '',
		'labels' => ''
	), $atts));
	$cols = intval($cols);
	if ( ($cols < 1) || ($cols > 10) ) {
		return '<p>The number of columns in not correct (must be between 1 and 10).</p>';
	}
	$icons = explode(',', $icons);
	$labels = explode(',', $labels);
	if ( count($icons) != count($labels) ) {
		return '<p>Not the same number of icons and labels.</p>';
	}
	$col_width = intval(100 / $cols);
	$i = 0;
	$j = 0;
	$odd_or_even = 'odd';
	$content_table = '';
	foreach ( $icons as $icon ) {
		if ( $i == 0 ) {
			$content_table .= '<tr>';
		}
		$content_table .= '<td class="' . $odd_or_even . '" width="' . $col_width . '%"><span style="background-image:url(' . get_template_directory_uri(). '/img/feature-icons/' . $icon . ');">' . $labels[$j] . '</span></td>';
		$i++;
		$j++;
		if ( $odd_or_even == 'odd' ) {
			$odd_or_even = 'even';
		} else {
			$odd_or_even = 'odd';
		}
		if ( $i == $cols ) {
			$i = 0;
			$content_table .= '</tr>';
		}
	}
	return '<table class="table-room-features">' . $content_table . '</table>';
}

function ap_sc_google_map($atts) {
	extract(shortcode_atts(array(
		'width' => '100%',
		'height' => '350',
		'src' => ''
	), $atts));
	return '<iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$src.'&amp;output=embed"></iframe>';
}

function ap_sc_dropcap($atts) {
	extract(shortcode_atts(array(  
        'letter' => ''  
    ), $atts));
	if (strlen($letter) != 1) {
		return '';
	} else {
		return '<span class="dropcap">' . $letter . '</span>';
	}
}

function ap_sc_list($atts, $content = null) {
	extract(shortcode_atts(array(  
        'style' => ''  
    ), $atts));
	return '<div class="list-' . $style . '">' . $content . '</div>';
}

function ap_sc_hr() {
	return '<hr/>';
}

function ap_sc_br() {
	return '<br/>';
}

function ap_sc_font($atts, $content = null) {
	extract(shortcode_atts(array(  
        'size' => ''  
    ), $atts));
	return '<span style="line-height: 2; font-size:' . $size . '">' . ap_remove_wpautop($content) . '</span>';
}

function ap_sc_room_type_data() { //before 1.3 now integrated with the [calendar] shortcode
	/*$return_string = '<input type="hidden" id="ah-room-type-slugs-data" value="' . get_option('ah_room_type_slugs') .'" />';
	$return_string .= '<input type="hidden" id="ah-room-type-names-data" value="' . get_option('ah_room_type_names') .'" />';
	return $return_string;*/
	return '';
}

function calendar_options_string_to_array($s) {
	if ( trim($s) == '' ) {
		return array();
	}
	$return_array = array();
	$rules = explode(',', $s);
	foreach ( $rules as $rule ) {
		$key = trim(substr($rule, 0, strpos($rule, ':')));
		$value = trim(substr($rule, strpos($rule, ':') + 1));
		if ( ($value{0} == "'") || ($value{0} == '"') ) {
			$value = substr($value, 1, strlen($value) - 2);
		} elseif ( $value == 'true' ) {
			$value = true;
		} elseif ( $value == 'false' ) {
			$value = false;
		} else {
			$value = intval($value);
		}
		$return_array[$key] = $value;
	}
	return $return_array;
}

function ap_sc_calendar($atts) {
	extract(shortcode_atts(array(  
    'name' => '',
    'room_type' => '',
		'options' => '',
		'type' => 'inline', //inline, inline-no-input, pop-up
		'show_availability' => 'yes',
		'lang' => ''
    ), $atts));

	$return_string = '';
	
	$return_string .= '<input type="hidden" class="ah-room-type-slugs-data" value="' . get_option('ah_room_type_slugs') .'" />';
	$return_string .= '<input type="hidden" class="ah-room-type-names-data" value="' . get_option('ah_room_type_names') .'" />';
	
	if ( $type == 'pop-up' ) {
		$return_string .= '<div class="ap-calendars-wrapper-for-pop-up">';
	}
	
	$return_string .= '<div class="ap-calendars-wrapper calendar-' . $type . '" id="calendar-' . $name . '">';
	
	$calendar_advanced_options = get_option('ah_date_picker_options');
	$calendar_options = array();
	if ( $calendar_advanced_options == '' ) {
		$calendar_options['prevText'] = '&#9668;';
		$calendar_options['nextText'] = '&#9658;';
		$calendar_options['changeMonth'] = false;
		$calendar_options['firstDay'] = 1;
		$calendar_options['minDate'] = 0;
		$calendar_options['maxDate'] = 365;
		$calendar_options['multiSelect'] = 0;
		$calendar_options['dateFormat'] = get_option('ah_date_format');
		if ( $calendar_options['dateFormat'] == '' ) {
			$calendar_options['dateFormat'] = 'dd-mm-yyyy';
		}
		$calendar_options['onSelect'] = 'update_input_date';
	} else {
		$calendar_options = calendar_options_string_to_array($calendar_advanced_options);
	}
	$options = calendar_options_string_to_array($options);
	$calendar_options = array_merge($calendar_options, $options);
	$return_string .= '<input type="hidden" class="ah-calendar-options" value="' . htmlspecialchars(json_encode($calendar_options)) .'" />';
	
	if ( $type == 'pop-up' ) {
		$button_ok = '<a class="palace-button calendar-button-ok" href="#">OK</a>';
	} else {
		$button_ok = '';
	}
	
	if ( $lang != '' ) {
		 $return_string .= '<input type="hidden" class="ah-calendar-lang" value="' . $lang . '" />';
	}
	
	if ( $room_type != '' ) {
		 $return_string .= '<input type="hidden" class="ah-calendar-type-to-show" value="' . $room_type . '" />';
	}

	if ( $show_availability == 'yes' ) {
		$calendars_data = get_option('ah_calendars_data');
		if (!$calendars_data) {
			$calendars_data = '[]';
		}
		$return_string .= '<input class="ah-calendars-data" type="hidden" value="' . htmlspecialchars($calendars_data) . '" />';
		$room_types = explode(',', get_option('ah_room_type_slugs'));
		foreach ( $room_types as $rt ) {
			$return_string .= '<div id="calendar-' . $rt . '-name-' . $name . '" class="ap-calendar calendar-for-' . $rt . '"></div>';
		}
		$return_string .= 	'<p class="calendar-key">' .
							'<span class="calendar-key-selected-days">&#9604;</span>&nbsp;&nbsp;' .
							__('your selection', 'thepalace') . 
							'&nbsp;&nbsp;&nbsp;<span class="calendar-key-unavailable">&#9604;</span>&nbsp;&nbsp;' .
							__('unavailable', 'thepalace') . 
							$button_ok .
							'</p>';
	} else {
		$return_string .= '<div class="ap-calendar"></div>';
		$return_string .= 	'<p class="calendar-key">' .
							'<span class="calendar-key-selected-days">&#9604;</span>&nbsp;&nbsp;' . 
							__('your selection', 'thepalace') . 
							'</span>' .
							$button_ok .
							'</p>';
	}

	if ( $type == 'pop-up' ) {
		$return_string .= '</div>';
	}

	$return_string .= '</div>';	

	return $return_string;
}

function ap_register_shortcodes() {
	add_shortcode('button', 'ap_sc_button');
	add_shortcode('textured_top_area_full_width', 'ap_sc_textured_top_area_full_width');
	add_shortcode('textured_middle_area_full_width', 'ap_sc_textured_middle_area_full_width');
	add_shortcode('textured_bottom_area_full_width', 'ap_sc_textured_bottom_area_full_width');
	add_shortcode('textured_top_left_area', 'ap_sc_textured_top_left_area');
	add_shortcode('textured_top_right_area', 'ap_sc_textured_top_right_area');
	add_shortcode('slider_type_a', 'ap_sc_slider_type_a');
	add_shortcode('slider_type_b', 'ap_sc_slider_type_b');
	add_shortcode('full_width_slider', 'ap_sc_slider_type_c');
	add_shortcode('slider_type_c', 'ap_sc_slider_type_c');
	add_shortcode('theme_gallery', 'ap_sc_thumbnails_gallery');
	add_shortcode('thumbnails_gallery', 'ap_sc_thumbnails_gallery');
	add_shortcode('column', 'ap_sc_column');
	add_shortcode('room_features', 'ap_sc_room_features');
	add_shortcode('google_map', 'ap_sc_google_map');
	add_shortcode('dropcap', 'ap_sc_dropcap');
	add_shortcode('u_list', 'ap_sc_list');
	add_shortcode('hr', 'ap_sc_hr');
	add_shortcode('br', 'ap_sc_br');
	add_shortcode('font', 'ap_sc_font');
	add_shortcode('room_type_data', 'ap_sc_room_type_data');
	add_shortcode('calendar', 'ap_sc_calendar');
	
	global $ap_options;
	if ( $ap_options['ap_disable_wp_gallery']['val'] == 'yes' ) {
		remove_shortcode('gallery', 'gallery_shortcode');
		add_shortcode('gallery', 'ap_sc_gallery');
	}
	
}

add_action('init', 'ap_register_shortcodes');

function ap_remove_wpautop( $content ) {
    $content = do_shortcode( shortcode_unautop( $content ) );
    $content = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $content );
    return $content;
}

add_filter('widget_text', 'do_shortcode');
add_filter('the_excerpt', 'do_shortcode');
add_filter('get_the_excerpt', 'do_shortcode');

function aurel_hotel_wpcf7_form_elements( $form ) {
	$form = do_shortcode( shortcode_unautop( $form ));
	$form = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $form );
	return $form;
}

add_filter( 'wpcf7_form_elements', 'aurel_hotel_wpcf7_form_elements' );
/* end shortcodes */

/* begin custom fields */
function ap_custom_boxes() {
	add_meta_box( 'ap_custom_box_page_options_id', 'Page Options', 'ap_custom_box_page_options_launch_display', 'page', 'normal', 'high' );
	add_meta_box( 'ap_custom_box_post_options_id', 'Post Options', 'ap_custom_box_post_options_launch_display', 'post', 'normal', 'high' );
}

function ap_custom_box_page_options_launch_display( $post ) {
	ap_custom_box_page_post_options_display( 'page', $post->ID );
}

function ap_custom_box_post_options_launch_display( $post ) {
	ap_custom_box_page_post_options_display( 'post', $post->ID );
}

function ap_custom_box_page_post_options_display( $type, $postID ) {
	global $ap_options;
	require_once( get_template_directory() . '/aurelien-panel/aurelien-panel-display-functions.php' );
	$fields_desc = '';
	if ( $type == 'post' ) {
		$fields_desc = 'or in the category section ';
	}
?>
<p>
	First line:
</p>
<p>
	<input id="pm-first-line" name="pm-first-line" type="text" class="widefat" value="<?php echo( get_post_meta($postID, 'pm-first-line', true) ); ?>" />
</p>
<?php
$no_feature_image_checked = '';
if ( get_post_meta($postID, 'pm-display-feature-image', true) == 'dont-display-feature-image' ) {
	$no_feature_image_checked = 'checked';
}
$no_title_checked = '';
if ( get_post_meta($postID, 'pm-display-title', true) == 'dont-display-title' ) {
	$no_title_checked = 'checked';
}
?>
<p>
	Do not display the feature image in the content: <input type="checkbox" name="pm-display-feature-image" value="dont-display-feature-image" <?php echo( $no_feature_image_checked ); ?>/>
</p><p>
	Do not display the title: <input type="checkbox" name="pm-display-title" value="dont-display-title" <?php echo( $no_title_checked ); ?>/>
</p>
<p>
	Layout:
</p>
<?php 
$layout_options = array(
	array( 'default', 'Don\'t override default' ),
	array( 'full-width', 'Full width' ),
	array( 'one-col-left-sidebar', 'One column and left sidebar' ),
	array( 'one-col-right-sidebar', 'One column and right sidebar')
);
aurel_panel_display_select_advanced( 'pm-page-layout', 'pm-page-layout', $layout_options, get_post_meta($postID, 'pm-page-layout', true) );  
?>
<div id="pm-page-sidebar">
	<p>
		Sidebar name:
	</p>
	<?php aurel_panel_display_sidebar_selector( 'pm-sidebar-name', get_post_meta($postID, 'pm-sidebar-name', true), true ); ?>
</div>
<p>
	Header image (leave blank to display the default image set in the header section <?php echo( $fields_desc ); ?>of the theme options panel):
</p>
<?php 
aurel_panel_display_image_upload( 'pm-header-image', get_post_meta($postID, 'pm-header-image', true) ); 
?>
<p>
	- or display a slider: 
	<select name="pm-slider-name">
		<option value="no-slider">Choose a slider</option>
		<option value="remove-slider" <?php if ( get_post_meta($postID, 'pm-slider-name', true) == 'remove-slider' ) { echo( 'selected' ); } ?>>Do not display a slider</option>
		<?php
		foreach($ap_options['ap_global_slider_manager']['val'] as $slider) {
			if ( get_post_meta($postID, 'pm-slider-name', true) == $slider['name'] ) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			echo('<option ' . $selected . ' value="' . $slider['name'] . '">' . $slider['name'] . '</option>');
		}
		?>
	</select>
</p>
<p>
	- or display a header map (enter lat and lng coma-separated e.g: 53.337574, -6.259713) : <input id="pm-display-header-map" name="pm-display-header-map" type="text" value="<?php echo( get_post_meta($postID, 'pm-display-header-map', true) ); ?>" />
</p>
<p>
	Background image (leave blank to display the default background):
</p>
<?php aurel_panel_display_image_upload( 'pm-background-image', get_post_meta($postID, 'pm-background-image', true) ); ?>
<p>
	If you have set a background image you can choose between the following features:
</p>
<?php 
$tile_or_stretch = get_post_meta($postID, 'pm-background-stretch-or-tile', true);
if ( $tile_or_stretch == '' ) {
	$tile_or_stretch = 'stretch';
}
$fixed_or_scrollable = get_post_meta($postID, 'pm-background-fixed-or-scrollable', true);
if ( $fixed_or_scrollable == '' ) {
	$fixed_or_scrollable = 'fixed';
}
aurel_panel_display_radio( 'pm-background-stretch-or-tile', array('stretch','tile'), $tile_or_stretch);
aurel_panel_display_radio( 'pm-background-fixed-or-scrollable', array('fixed','scrollable'), $fixed_or_scrollable); 
?>
</p>
<p>
	Footer image (leave blank to display the default image set in the footer section <?php echo( $fields_desc ); ?>of the theme options panel):
</p>
<?php aurel_panel_display_image_upload( 'pm-footer-image', get_post_meta($postID, 'pm-footer-image', true) ); ?>
<p>
	Header minimum height (in px - this field will not have any effect for on sliders as their height is defined in the Theme Options panel - leave blank if you don't want to override the default height):
</p>
<p>
	<input id="pm-header-height" name="pm-header-height" type="text" value="<?php echo( get_post_meta($postID, 'pm-header-height', true) ); ?>" />
</p>
<p>
	Custom CSS:
</p>
<p>
	<textarea id="pm-custom-css" name="pm-custom-css" class="widefat"><?php echo( get_post_meta($postID, 'pm-custom-css', true) ); ?></textarea>
</p>


<input type="hidden" id="ap-custom-fields" name="ap-custom-fields" value="ap-custom-fields"/>
<?php
}

add_action( 'add_meta_boxes', 'ap_custom_boxes' );

function ap_custom_boxes_save_postdata( $post_id ) {
	$ap_custom_fields = array(
		'pm-page-layout',
		'pm-sidebar-name',
		'pm-header-image',
		'pm-background-image',
		'pm-background-stretch-or-tile',
		'pm-background-fixed-or-scrollable',
		'pm-slider-name',
		'pm-display-header-map',
		'pm-footer-image',
		'pm-first-line',
		'pm-display-feature-image',
		'pm-display-title',
		'pm-header-height',
		'pm-custom-css'
	);
	if (isset($_POST['ap-custom-fields'])) {
        foreach ($ap_custom_fields as $custom_field) {
			update_post_meta($post_id, $custom_field, trim(stripslashes($_POST[$custom_field])));
        }
    }
}

add_action( 'save_post', 'ap_custom_boxes_save_postdata' );
add_action( 'publish_post', 'ap_custom_boxes_save_postdata');

function custom_boxes_script() {
    wp_enqueue_script('custom-boxes-script', get_template_directory_uri().'/aurelien-panel/js/admin-custom-boxes.js', array('jquery'));
}

add_action('admin_print_scripts-post.php', 'custom_boxes_script');
add_action('admin_print_scripts-post-new.php', 'custom_boxes_script');

function custom_boxes_css() {
?>
<style type="text/css" media="screen">
.ap-width-75p {
	width: 75%;
}
</style>
<?php
}

add_action('admin_print_styles-post.php', 'custom_boxes_css');
add_action('admin_print_styles-post-new.php', 'custom_boxes_css');

function category_edit_custom_title( $tag ) {
	$title = '';
	$ap_category_titles = get_option( 'ap_category_titles' ); 
	if ( $ap_category_titles ) {
		if ( isset($ap_category_titles[$tag->term_id]) ) {
			$title = $ap_category_titles[$tag->term_id];
		}
	}
?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="custom_cat_title">Custom Category Title</label></th>
		<td>
			<input type="text" name="custom_cat_title" id="custom_cat_title" value="<?php echo( $title ); ?>" />
			<p class="description">Custom category title for category archive page.</p>
		</td>
	</tr>
<?php
}

add_action('edit_category_form_fields', 'category_edit_custom_title');

function category_add_custom_title() {
?>
	<div class="form-field">
		<label for="custom_cat_title">Custom Category Title</label>
		<input type="text" name="custom_cat_title" id="custom_cat_title" />
		<p>Custom category title for "category list of post" page.</p>
	</div>
<?php
}

add_action('category_add_form_fields', 'category_add_custom_title');

function update_category_custom_title($term_id) {
	if ( (isset($_POST['taxonomy'])) && ($_POST['taxonomy'] == 'category') ) {
		$ap_category_titles = get_option('ap_category_titles');
		$ap_category_titles[$term_id] = strip_tags($_POST['custom_cat_title']);
		update_option('ap_category_titles', $ap_category_titles);
	}
}

add_action('create_category', 'update_category_custom_title');
add_action('edited_category', 'update_category_custom_title');

function remove_category_custom_title($term_id) {
	if ( (isset($_POST['taxonomy'])) && ($_POST['taxonomy'] == 'category') ) {
		$ap_category_titles = get_option('ap_category_titles');
		unset($ap_category_titles[$term_id]);
		update_option('ap_category_titles', $ap_category_titles);
	}
}

add_action('deleted_term_taxonomy', 'remove_category_custom_title');

/* end custom fields */

/* begin menu */
function register_ap_menu() {
	register_nav_menu('ap_header_menu', 'Main Menu');
}

add_action('init', 'register_ap_menu');
/* end menu */

/* begin theme support - image size - language */
add_theme_support( 'automatic-feed-links' );

if ( ! isset( $content_width ) ) {
	$content_width = 930;
}
	
add_theme_support( 'post-thumbnails' ); 

add_image_size( 'image-90x90', 90, 90, true );
add_image_size( 'image-120x120', 120, 120, true );
add_image_size( 'image-420x150', 420, 150, true );
add_image_size( 'image-630x210', 630, 210, true );
add_image_size( 'image-930x310', 930, 310, true );
add_image_size( 'image-scale-with-grid', 930, 400, true );

add_image_size( 'image-width-930', 930, 9999 );

add_filter('image_size_names_choose', 'ap_image_sizes');
function ap_image_sizes($sizes) {
	$addsizes = array(
		'image-scale-with-grid' => 'Scale with grid'
	); 	
	$newsizes = array_merge($sizes, $addsizes);
	return $newsizes;
}

function load_freshizer() {
	require_once( get_template_directory() . '/freshizer/freshizer.php' );
}

add_action('wp_head', 'load_freshizer');

load_theme_textdomain( 'thepalace', get_template_directory() . '/lang' );
/* end theme support - image size - language */

/*begin aurel_comment */
function aurel_comment( $comment, $args, $depth ) {
	global $ap_options;
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

<!-- begin #comment-<?php comment_ID(); ?>.comment-wrapper  -->
<div id="comment-<?php comment_ID(); ?>" class="comment-wrapper">

<!-- begin .comment-author.vcard -->
<div class="comment-author vcard">
<?php echo get_avatar( $comment, 64 ); ?>
</div>
<!-- end .comment-author.vcard -->

<!-- begin .comment-meta.commentmetadata -->
<div class="comment-meta commentmetadata">
	<?php
	echo( __('Author: ', 'thepalace') . get_comment_author_link() . '&nbsp;&nbsp//&nbsp&nbsp' );
	echo( __('Date: ', 'thepalace') . get_comment_date() . '&nbsp;&nbsp//&nbsp&nbsp' );
	echo( __('Time: ', 'thepalace') . get_comment_time() );
	comment_reply_link( array_merge( $args, array( 'after' => '</span>', 'before' => '<span class="reply">&nbsp;&nbsp//&nbsp&nbsp', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) );
	if ( $ap_options['ap_display_edit_link']['val'] == 'yes' ) {
		edit_comment_link( __( 'Edit comment', 'thepalace' ), '&nbsp;&nbsp//&nbsp&nbsp' );
	}
?>
</div>
<!-- end .comment-meta.commentmetadata -->

<?php if ( $comment->comment_approved == '0' ) : ?>
<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'thepalace' ); ?></em>
<br />
<?php endif; ?>

<div class="comment-body"><?php comment_text(); ?></div>

<div class="clear"></div>

</div>
<!-- end #comment-<?php comment_ID(); ?>.comment-wrapper  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	
<li class="pingback">

<!-- begin .comment-wrapper -->
<div class="comment-wrapper">
<?php 
_e( 'Pingback: ', 'thepalace' );  
comment_author_link(); 
if ( $ap_options['ap_display_edit_link']['val'] == 'yes' ) {
	edit_comment_link( __( 'Edit comment', 'thepalace' ), '&nbsp;&nbsp//&nbsp&nbsp' );
}
?>
</div>
<!-- end .comment-wrapper -->
	
	<?php
			break;
	endswitch;
}
/* end aurel_comment */

/* begin custom css */
function palace_custom_css() {
	global $ap_options;
	foreach ($ap_options as $key => $ap_option) {
		$$key = $ap_option['val'];
	}
?>
<style type="text/css" media="screen">
/* generated by the palace_custom_css() function which is in the functions.php file */

body {
	background: <?php echo( $ap_bg_color ); ?>;
}

a, #wp-calendar a {
	color: <?php echo( $ap_links_color ); ?>;
}

h1, h2, h3, h4, h5, h6, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a, .blog-description {
	color: <?php echo( $ap_headings_color ); ?>;
}
<?php 
if ( $ap_headings_font != '' ) { 
	$pos = strpos($ap_headings_font, ':');
	if ($pos) {
		$ap_headings_font = substr($ap_headings_font, 0, $pos);
	}
?>
#logo, h1, h2, h3, h4, h5, h6, #fws-caption p, #full-screen-slider #fss-caption {
	font-family: "<?php echo( str_replace('+', ' ', $ap_headings_font) ); ?>", sans-serif;
}
<?php 
} 
?>

header.full-width-slider-small-height, .full-width-slider-small-height #full-width-slider {
	min-height: <?php echo( $ap_header_min_height ); ?>px;
}

<?php
foreach ( $ap_global_slider_manager as $slider ) {
	if ( $slider['type'] == 'Full-width' ) {
?>

header.full-width-slider-big-height.<?php echo( $slider['name'] ); ?>, .full-width-slider-big-height #full-width-slider.<?php echo( $slider['name'] ); ?> {
	min-height: <?php echo( $slider['options'][3] ); ?>px;
}

header.ie8-header-small.full-width-slider-big-height.<?php echo( $slider['name'] ); ?>, header.ie8-header-small.full-width-slider-big-height.<?php echo( $slider['name'] ); ?> #full-width-slider, header.ie8-header-small.full-width-slider-big-height.<?php echo( $slider['name'] ); ?> .full-width-slide img {	
	height: <?php echo( $slider['options'][3] ); ?>px;
}
<?php
	}
}
?>
	
@media only screen and (max-width: 1000px) {

<?php
foreach ( $ap_global_slider_manager as $slider ) {
	if ( $slider['type'] == 'Full-width' ) {
?>

	header.full-width-slider-big-height.<?php echo( $slider['name'] ); ?>, .full-width-slider-big-height #full-width-slider.<?php echo( $slider['name'] ); ?> {
		height: <?php echo( $slider['options'][3] ); ?>px;
	}
	
	.full-width-slider-big-height .full-width-slide img {
		height: <?php echo( $slider['options'][3] ); ?>px;
	}
	
<?php
	}
}
?>

	header.full-width-slider-small-height, .full-width-slider-small-height #full-width-slider {
		height: <?php echo( $ap_header_min_height ); ?>px;
	}


	.full-width-slider-small-height .full-width-slide img {
		height: <?php echo( $ap_header_min_height ); ?>px;
	}

}

header.ie8-header-small.full-width-slider-small-height, header.ie8-header-small.full-width-slider-small-height #full-width-slider, header.ie8-header-small.full-width-slider-small-height .full-width-slide img {	
	height: <?php echo( $ap_header_min_height ); ?>px;
}

header.full-width-header-map {
	height: <?php echo( $ap_header_min_height ); ?>px;
}

#map-canvas {
	height: <?php echo( $ap_header_min_height ); ?>px;
}

.sidebar-content a, .textured-area-content a {
	color: <?php echo( $ap_links_color_sidebar ); ?>;
}

#footer-content {
	color: <?php echo( $ap_text_color_footer ); ?>;
}

footer.below-main-container #footer-content h3 {
	border-bottom-color: <?php echo( $ap_text_color_footer ); ?>;
}

#footer-content a {
	color: <?php echo( $ap_links_color_footer ); ?>;
}

.sidebar-content h1, .sidebar-content h2, .sidebar-content h3, .sidebar-content h4, .sidebar-content h5, .sidebar-content h6,
.textured-area-content h1, .textured-area-content h2, .textured-area-content h3, .textured-area-content h4, .textured-area-content h5, .textured-area-content h6 {
	color: <?php echo( $ap_headings_color_sidebar ); ?>;
}

#footer-content h1, #footer-content h2, #footer-content h3, #footer-content h4, #footer-content h5, #footer-content h6 {
	color: <?php echo( $ap_headings_color_footer ); ?>;
}

a:hover, #wp-calendar a:hover, #footer-content a:hover, .sidebar-content .current-menu-item a, .textured-area-content .current-menu-item a {
	color: <?php echo( $ap_links_hover_color ); ?>
}

.texture-custom .textured-area {
	background: url(<?php echo( $ap_texture_custom ); ?>);
}

.palace-button, input[type="submit"], input#searchsubmit {
	background-color: <?php echo( $ap_buttons_color); ?>;
	border-color: <?php echo( $ap_buttons_color); ?>;
}

.datepick-cmd, .datepick-month td .datepick-selected {
	background-color: <?php echo( $ap_buttons_color); ?>;
}

.calendar-key-selected-days {
	color: <?php echo( $ap_buttons_color); ?>;
}

footer.below-main-container, #footer-image-container {
	background: <?php echo( $ap_bg_color ); ?>;
	min-height: <?php echo( $ap_footer_min_height ); ?>px;
}

<?php
$red = hexdec(substr($ap_bg_color,1,2));
$green = hexdec(substr($ap_bg_color,3,2));
$blue = hexdec(substr($ap_bg_color,5,2));
$rgb = '' . $red . ', ' . $green . ', ' . $blue;
?>

#body-footer-transition {
	background: -webkit-gradient(linear, left top, left bottom, from(rgba(<?php echo( $rgb ) ?>, 0)), to(<?php echo( $ap_bg_color ); ?>));
	background: -moz-linear-gradient(linear, left top, left bottom, from(rgba(<?php echo( $rgb ) ?>, 0)), to(<?php echo( $ap_bg_color ); ?>));
	background: -o-linear-gradient(linear, left top, left bottom, from(rgba(<?php echo( $rgb ) ?>, 0)), to(<?php echo( $ap_bg_color ); ?>));
	background: -ms-linear-gradient(linear, left top, left bottom, from(rgba(<?php echo( $rgb ) ?>, 0)), to(<?php echo( $ap_bg_color ); ?>));
}

#footer-mask {
	background: -webkit-gradient(linear, left top, left bottom, from(<?php echo( $ap_bg_color ); ?>), to(rgba(<?php echo( $rgb ) ?>, 0)));
	background: -moz-linear-gradient(top, <?php echo( $ap_bg_color ); ?>, rgba(<?php echo( $rgb ) ?>, 0));
	background: -o-linear-gradient(top, <?php echo( $ap_bg_color ); ?>, rgba(<?php echo( $rgb ) ?>, 0));
	background: -ms-linear-gradient(top, <?php echo( $ap_bg_color ); ?>, rgba(<?php echo( $rgb ) ?>, 0));
	filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='<?php echo( $ap_bg_color ); ?>', endColorstr='<?php echo( '#00' . substr($ap_bg_color,1,6) ); ?>');
}

@media only screen and (max-width: 1000px) {

#footer-image, #footer-image img, #footer-mask {
	height: <?php echo( $ap_footer_min_height ); ?>px;
}

}
</style>

<?php
}
add_action('wp_head','palace_custom_css');
/* end custom css */

/* begin load js scripts */
add_action('init', 'register_palace_scripts');
add_action('wp_footer', 'print_palace_scripts');

function register_palace_scripts() {
	wp_register_script( 'superfish', get_template_directory_uri() . '/js/superfish.js', array(), '1.0', true );
	wp_register_script( 'palace_sliders', get_template_directory_uri() . '/js/sliders.js', array(), '1.0', true );
	wp_register_script( 'palace_rotate', get_template_directory_uri() . '/js/jQueryRotateCompressed.2.1.js', array(), '1.0', true );
	wp_register_script( 'prettyphoto', get_template_directory_uri() . '/js/jquery.prettyphoto.js', array(), '1.0', true );
	wp_register_script( 'palace_calendar', get_template_directory_uri() . '/js/jquery.datepick.min.js', array(), '1.0', true );
	$dpl = get_option( 'ah_date_picker_lang' );
	if ($dpl != '') {
		$dpls = explode(',', $dpl);
		foreach( $dpls as $d ) {
			wp_register_script( 'palace_calendar_lang_' . $d, get_template_directory_uri() . '/js/datepicklang/jquery.datepick-' . $d . '.js', array(), '1.0', true );
		}
	}
	wp_register_script( 'palace_calendar_default_lang', get_template_directory_uri() . '/js/datepicklang/jquery.datepick-' . get_option( 'ah_date_picker_default_lang', 'en-US' ) . '.js', array(), '1.0', true );
	wp_register_script( 'palace_map', 'http://maps.googleapis.com/maps/api/js?sensor=false', array(), '1.0', true );	
	wp_register_script( 'palace_functions', get_template_directory_uri() . '/js/palace-functions.js', array(), '1.0', true );
}

function print_palace_scripts() {
	wp_print_scripts( 'superfish' );
	wp_print_scripts( 'palace_sliders' );
	wp_print_scripts( 'palace_rotate' );
	wp_print_scripts( 'prettyphoto' );
	wp_print_scripts( 'palace_calendar' );
	$dpl = get_option( 'ah_date_picker_lang' );
	if ($dpl != '') {
		$dpls = explode(',', $dpl);
		foreach( $dpls as $d ) {
			wp_print_scripts( 'palace_calendar_lang_' . $d );
			
		}
	}
	wp_print_scripts( 'palace_calendar_default_lang' );
	global $wp_query;
	if ( is_page() || is_single() ) {
		$post_id = $wp_query->post->ID;
		if ( get_post_meta($post_id, 'pm-display-header-map', true) != '' ) {
			wp_print_scripts( 'palace_map' );
		}
	}
	wp_print_scripts( 'palace_functions' );

}
/* end load js scripts */
?>
