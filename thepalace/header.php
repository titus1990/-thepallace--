<?php
global $ap_options;
foreach ($ap_options as $key => $ap_option) {
	$$key = $ap_option['val'];
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php bloginfo('name'); ?><?php wp_title('-'); ?></title>

<link rel="stylesheet" type="text/css" media="screen" href="<?php echo( get_template_directory_uri() ); ?>/skeleton.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo( get_template_directory_uri() ); ?>/prettyphoto.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo( get_template_directory_uri() ); ?>/jquery.datepick.css" />
<link rel="stylesheet" type="text/css" media="only screen and (max-width:959px)" href="<?php echo( get_template_directory_uri() ); ?>/nav-narrow.css" />
<?php if ( $ap_headings_font != '' ) { ?>
<link href="http://fonts.googleapis.com/css?family=<?php echo( str_replace(' ', '+', $ap_headings_font )); ?>" rel="stylesheet" type="text/css">
<?php } ?>
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo( get_template_directory_uri() ); ?>/ie.css" />
<![endif]-->
<!--[if lt IE 9]>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo( get_template_directory_uri() ); ?>/ie8.css" />
<script src="<?php echo( get_template_directory_uri() ); ?>/js/html5shiv.js"></script>
<![endif]-->

<?php
if ($ap_favicon != '') {
?>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo($ap_favicon); ?>">
<link rel="icon" type="image/x-icon" href="<?php echo($ap_favicon); ?>">
<?php
}
?>

<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php 

wp_enqueue_script( 'jquery' );

if ( is_singular() && get_option( 'thread_comments' ) ) {
	wp_enqueue_script( 'comment-reply' );
}

global $wp_query, $rule, $palace_title;

$palace_title = '';
$display_a_title = true;
$slider_name = '';
$custom_css = '';
$header_height = '';

if ( ! (is_single() || is_page()) ) {
	$rule = $ap_lists_of_posts_manager[0];
}
if ( is_single() ) {
	$rule = $ap_single_posts_manager[0];
}
$rule['header-image'] = '';
$rule['slider'] = '';
$rule['background-image'] = '';
$rule['footer-image'] = '';		
$rule['fws'] = '';
$rule['fss'] = '';
$rule['tcs'] = '';
$rule['header-map'] = '';
	
if ( is_page() || is_single() ) {

	$post_id = $wp_query->post->ID;
	if ( get_post_meta($post_id, 'pm-display-title', true) != 'dont-display-title' ) {
		$palace_title = get_the_title( $post_id );
	} else {
		$display_a_title = false;
	}
	
	if ( is_page() ) {	
		$rule['layout'] = $ap_default_layout_for_pages;
		$rule['sidebar'] = $ap_sidebar_name_for_pages;
		$rule['meta'] = array();
		if ( ($ap_disable_comments_for_pages == 'no') && comments_open($post_id) ) {
			$rule['display_comments'] = 'yes';
		} else {
			$rule['display_comments'] = 'no';
		}
	}
	
	if ( is_single() ) {
		$post_categories = wp_get_post_categories( $post_id );
		$post_categories_slug = array();
		foreach ($post_categories as $post_cat ) {
			$full_cat = get_category($post_cat);
			$post_categories_slug[] = $full_cat->slug;
		}
		$cat_rules = $ap_categories_manager;
		foreach ( $cat_rules as $cat_rule ) {
			if ( in_array($cat_rule['category'], $post_categories_slug) ) {
				$rule = $cat_rule;
				break;
			}
		}
		$rule['fws'] = '';
		$rule['fss'] = '';
		$rule['tcs'] = '';
		$rule['header-map'] = '';

		if ( ($ap_single_posts_manager[0]['disable-comments'] == 'no') && ($rule['disable-comments'] == 'no') && comments_open($post_id) ) {
			$rule['display_comments'] = 'yes';
		} else {
			$rule['display_comments'] = 'no';
		}
	} 

	if ( get_post_meta($post_id, 'pm-header-image', true) != '' ) {
		$rule['header-image'] = get_post_meta($post_id, 'pm-header-image', true);
	}
	if ( $rule['header-image'] == '' ) {
		$slider_name = get_post_meta($post_id, 'pm-slider-name', true);
		if ( $slider_name == 'no-slider' ) {
			$slider_name = $rule['slider'];
		}
		if ( $slider_name == 'remove-slider' ) {
			$slider_name = 'no-slider';
		}
		$slider_data = array();
		if ( $slider_name != 'no-slider' ) {
			foreach ( $ap_global_slider_manager as $slider ) {
				if ( $slider['name'] == $slider_name ) {
					switch ( $slider['type'] ) {
						case 'Full-width' : $rule['fws'] = 'yes'; break;
						case 'Full-screen' : $rule['fss'] = 'yes'; break;
						case 'Top-content' : $rule['tcs'] = 'yes'; break;
					}
					$slider_data = $slider;
					break;
				}
			}
		}
	}
	if ( get_post_meta($post_id, 'pm-display-header-map', true) != '' ) {
		$rule['header-map'] = 'yes';
	}	
	if ( get_post_meta($post_id, 'pm-background-image', true) != '' ) {
		$rule['background-image'] = get_post_meta($post_id, 'pm-background-image', true);
		$rule['background-stretch-or-tile'] = get_post_meta($post_id, 'pm-background-stretch-or-tile', true);
		$rule['background-fixed-or-scrollable'] = get_post_meta($post_id, 'pm-background-fixed-or-scrollable', true);
	}
	if ( get_post_meta($post_id, 'pm-footer-image', true) != '' ) {
		$rule['footer-image'] = get_post_meta($post_id, 'pm-footer-image', true);
	}
	if ( (get_post_meta($post_id, 'pm-page-layout', true) != 'default') && (get_post_meta($post_id, 'pm-page-layout', true) != '') ) {
		$rule['layout'] = get_post_meta($post_id, 'pm-page-layout', true);
	}
	if ( (get_post_meta($post_id, 'pm-sidebar-name', true) != 'default') && (get_post_meta($post_id, 'pm-sidebar-name', true) != '') ) {
		$rule['sidebar'] = get_post_meta($post_id, 'pm-sidebar-name', true);
	}
	$custom_css = get_post_meta($post_id, 'pm-custom-css', true);
	$header_height = get_post_meta($post_id, 'pm-header-height', true);
	if ( $header_height != '' ) {
		$ap_header_min_height = $header_height;
	}

} elseif ( is_category() ) {
	
	$palace_title = single_cat_title('', false);
	$ap_category_titles = get_option('ap_category_titles');
	if ( isset($ap_category_titles[$cat]) && ($ap_category_titles[$cat] != '') ) {
		$palace_title = $ap_category_titles[$cat];
	}
	$cat_rules = $ap_categories_manager;
	$full_cat = get_category($cat);
	foreach ( $cat_rules as $cat_rule ) {
		if ( $cat_rule['category'] == $full_cat->slug ) {
			$rule = $cat_rule;
			break;
		}
	}
	if ( isset($rule['custom-css']) ) {
		$custom_css = $rule['custom-css'];
	}
	$rule['fws'] = '';
	$rule['fss'] = '';
	$rule['tcs'] = '';
	$rule['header-map'] = '';
	$slider_name = $rule['slider'];
	$slider_data = array();
	if ( $slider_name != 'no-slider' ) {
		foreach ( $ap_global_slider_manager as $slider ) {
			if ( $slider['name'] == $slider_name ) {
				switch ( $slider['type'] ) {
					case 'Full-width' : $rule['fws'] = 'yes'; break;
					case 'Full-screen' : $rule['fss'] = 'yes'; break;
					case 'Top-content' : $rule['tcs'] = 'yes'; break;
				}
				$slider_data = $slider;
				break;
			}
		}
	}
	
} elseif ( is_tag() ) {
	$palace_title = __( 'Tag Archives: ', 'thepalace' ) . single_tag_title( '', false );
} elseif ( is_day() ) {
	$palace_title = __( 'Daily Archives: ', 'thepalace' ) . get_the_date();
} elseif ( is_month() ) {
	$palace_title = __( 'Monthly Archives: ', 'thepalace' ) . get_the_date('F Y');
} elseif ( is_year() ) {
	$palace_title = __( 'Yearly Archives: ', 'thepalace' ) . get_the_date('Y');
} elseif ( is_author() ) {
	if ( have_posts() ) {
		the_post();
		$palace_title =  __( 'Author Archives: ', 'thepalace' ) . get_the_author();
		rewind_posts();
	} else {
		$palace_title = __( 'Author Archives', 'thepalace' );
	}
} elseif ( get_search_query() != '' ) {
	$palace_title = __( 'Search Results for: ', 'thepalace') . get_search_query();
} elseif ( is_404() ) {
	$palace_title = $ap_404_title;
}

if ( $rule['header-image'] == '' ) {
	$rule['header-image'] = $ap_header_image;
}
if ( ($rule['fws'] != 'yes') && ($rule['fss'] != 'yes')&& ($rule['tcs'] != 'yes') && ($ap_header_slider != 'no-slider') && ($rule['header-image'] == '') ) {
	$slider_name = $ap_header_slider;
	$slider_data = array();
	foreach ( $ap_global_slider_manager as $slider ) {
		if ( $slider['name'] == $slider_name ) {
			switch ( $slider['type'] ) {
				case 'Full-width' : $rule['fws'] = 'yes'; break;
				case 'Full-screen' : $rule['fss'] = 'yes'; break;
				case 'Top-content' : $rule['tcs'] = 'yes'; break;
			}
			$slider_data = $slider;
			break;
		}
	}
}

if ( $rule['background-image'] == '' ) {
	$rule['background-image'] = $ap_bg_image;
	$rule['background-stretch-or-tile'] = $ap_bg_stretch_or_tile;
	$rule['background-fixed-or-scrollable'] = $ap_bg_fixed_or_scrollable;
}
if ( $rule['footer-image'] == '' ) {
	$rule['footer-image'] = $ap_footer_image;
}
if ( $rule['header-map'] == 'yes' ) {
	$header_class = 'full-width-header-map';
	$map_coords = get_post_meta($post_id, 'pm-display-header-map', true);
	$rule['fws'] = 'no';
} else if ( $rule['fws'] == 'yes' ) {
	$header_class = 'full-width-slider-big-height';
} else if ( $rule['fss'] == 'yes' ) {
	$header_class = 'full-screen-slider';
} else {
	$header_class = 'full-width-slider-small-height';
}

if ( $rule['fws'] == 'yes' ) {
	$fws_options['animation_speed'] = $slider_data['options'][0];
	$fws_options['pause_time'] = $slider_data['options'][1];	
	$fws_options['autoplay'] = $slider_data['options'][2];
} else {
	$fws_options['animation_speed'] = $ap_title_animation_speed;
	$fws_options['pause_time'] = '0';
	$fws_options['autoplay'] = 'no';
}	

wp_head(); 
	
echo( $ap_custom_header_code ); 

if ( $header_height != '' ) {
?>
<style type="text/css">
header.full-width-slider-small-height, .full-width-slider-small-height #full-width-slider {
	min-height: <?php echo( $header_height ); ?>px;
}
@media only screen and (max-width: 1000px) {
	header.full-width-slider-small-height, .full-width-slider-small-height #full-width-slider, .full-width-slider-small-height .full-width-slide img {
		height: <?php echo( $header_height ); ?>px;
	}
}
header.ie8-header-small.full-width-slider-small-height, header.ie8-header-small.full-width-slider-small-height #full-width-slider, header.ie8-header-small.full-width-slider-small-height .full-width-slide img {	
	height: <?php echo( $header_height ); ?>px;
}
header.full-width-header-map, #map-canvas {
	height: <?php echo( $header_height ); ?>px;
}
</style>
<?php
}

if ( $custom_css != '' ) {
?>
<style type="text/css">
<?php echo( $custom_css ); ?>   
</style>
<?php
}

if ( $ap_texture_custom != '' ) {
	$ap_texture = 'custom';
}
$class = 'texture-' . $ap_texture;
if ( $ap_boxed_layout == 'yes' ) {
	$class .= ' has-layout-boxed';
} else {
	$class .= ' layout-unboxed';
}
if ( $rule['tcs'] == 'yes' ) {
	$class .= ' top-content-slider';
}
$style = '';
if ( $rule['background-stretch-or-tile'] == 'tile' ) {
	$style = 'style="background-image:url(' . $rule['background-image'] . ');';
	if ( $rule['background-fixed-or-scrollable'] == 'fixed' ) {
		$style .= 'background-attachment:fixed;"';
	} else {
		$style .= '"';
	}
}
?>
	
</head>

<body <?php body_class($class); ?> <?php echo( $style ); ?>>

<?php if ( ($rule['fss'] != 'yes') && ($rule['background-image'] != '') && ($rule['background-stretch-or-tile'] == 'stretch') ) { ?>
<div id="background">
<img src="<?php echo( $rule['background-image'] ); ?>" alt="" />
</div>
<?php } ?>

<!-- begin header -->
<header class="<?php echo( $header_class . ' ' . $slider_name); ?>">

<?php
if ( $rule['header-map'] == 'yes' ) {
?>

<input type="hidden" id="map-lat-lng" value="<?php echo( $map_coords ); ?>" />
<input id="ap_fws_options" type="hidden" value="<?php echo( htmlspecialchars(json_encode($fws_options)) ); ?>" />

<div id="map-canvas"></div>

<?php if ( ($ap_title_location == 'header') && ($palace_title != '') ) { ?>
<div class="full-width-slide">
<div class="full-width-slide-caption">
<h1><?php _e( $palace_title ); ?></h1>
</div>
</div>
<?php } ?>

<?php
} elseif ( $rule['fss'] == 'yes' ) {
?>
<!-- begin #full-screen-slider -->
<?php
	$slider_urls = array();
	$slider_captions = array();
	foreach( $slider_data['slides'] as $slide ) {
		$img_urls = array(
			'1600x900' => fImg::resize( $slide['image_url'], 1600, 900, true ), 
			'1200x675' => fImg::resize( $slide['image_url'], 1200, 675, true ),
			'800x600' => fImg::resize( $slide['image_url'], 800, 600, true ),
			'480x320' => fImg::resize( $slide['image_url'], 480, 320, true ),
			'768x1024' => fImg::resize( $slide['image_url'], 768, 1024, true ),
			'320x480' => fImg::resize( $slide['image_url'], 320, 480, true ),
		);
		$slider_urls[] = $img_urls;
		$slider_captions[] = __( $slide['caption'] );
	}
?>

<div id="full-screen-slider" data-animation-speed="<?php echo( $slider_data['options'][0] ); ?>" data-pause-time="<?php echo( $slider_data['options'][1] ); ?>" data-autoplay="<?php echo( $slider_data['options'][2] ); ?>" data-img-urls='<?php echo( json_encode($slider_urls) ); ?>' data-captions='<?php echo( json_encode($slider_captions) ); ?>'>
<div id="full-screen-slider-mover"></div>
<div id="fss-caption-container" class="container">
	<div class="sixteen columns">
		<div id="fss-caption"></div>
		<a href="#" class="fss-button-left">&lsaquo;</a>
		<a href="#" class="fss-button-right">&rsaquo;</a>
		<div id="position-indicator"></div>
	</div>
</div>
</div>
<!-- end #full-screen-slider -->

<?php
} else {
?>

<!-- begin #full-width-slider -->
<div id="full-width-slider" class="<?php echo( $slider_name ); ?>">

<?php 
if ( $rule['fws'] == 'yes' ) {
	foreach( $slider_data['slides'] as $slide ) {
?>
<div class="full-width-slide">
<img src="<?php echo ( fImg::resize( $slide['image_url'], $ap_header_img_width, (int)$slider_data['options'][3] * ($ap_header_img_width / 1000), true ) ); ?>" alt="" />
<?php if ( $slide['caption'] != '') { ?>
<div class="full-width-slide-caption">
<p><?php _e( $slide['caption'] ); ?></p>
</div>
<?php } ?>
</div>

<?php } ?>
<a id="slider-button-left" href="#"></a>
<a id="slider-button-right" href="#"></a>

<?php } else { ?>
<div class="full-width-slide">
<?php if ( $rule['header-image'] != '' ) { ?>
<img src="<?php echo ( fImg::resize( $rule['header-image'], $ap_header_img_width, (int)$ap_header_min_height * ($ap_header_img_width / 1000), true ) ); ?>" alt="" />
<?php } ?>
<?php if ( ($display_a_title) && ($ap_title_location == 'header') ) { ?>
<div class="full-width-slide-caption">
<?php 
if ( $palace_title == '' ) { 
?>
<p><?php bloginfo('description'); ?></p>
<?php
} else { 
	if ( is_front_page() ) {
?>
<p><?php _e( $palace_title ); ?></p>
<?php
	} else {
?>
<h1><?php _e( $palace_title ); ?></h1>
<?php 
	}
}	
?>
</div>
<?php } ?>
</div>
<?php 
}
?>
<input id="ap_fws_options" type="hidden" value="<?php echo( htmlspecialchars(json_encode($fws_options)) ); ?>" />
</div>
<!-- end #full-width-slider -->

<?php
} 
?>

<!-- begin #caption-container-full-width -->
<div id="fws-caption-container-full-width">

<!-- begin #caption-container-centered -->
<div class="container">

<!-- begin .sixteen.columns -->
<div class="sixteen columns">

<!-- begin #caption -->
<div id="fws-caption">
</div>
<!-- end #caption -->

</div>
<!-- end .sixteen-columns -->

</div>
<!-- end .container -->

</div>
<!-- end #caption-container-full-width -->

<!-- begin #logo-nav-container -->
<div id="logo-nav-container">

<!-- begin #logo-nav-container-centered -->
<div id="logo-nav-container-centered" class="container">
		
<!-- begin #logo -->
<?php
if ($ap_logo == '') {
	$ap_logo = get_bloginfo('name');
} else {
	$ap_logo = '<img src="' . $ap_logo . '" alt="' . get_bloginfo('name') . '" />';
}
$logo_link = '<a href="' . home_url() . '">' . $ap_logo . '</a>';
if ( is_front_page() ) {
?>
<h1 id="logo">
<?php echo( $logo_link ); ?>
<a id="nav-button-main" href="#"><img class="nav-arrow" src="<?php echo( get_template_directory_uri() ); ?>/img/nav-arrow.png"></a>
</h1>
<?php
} else {
?>
<div id="logo">
<?php echo( $logo_link ); ?>
<a id="nav-button-main" href="#"><img class="nav-arrow" src="<?php echo( get_template_directory_uri() ); ?>/img/nav-arrow.png"></a>
</div>
<?php
}
?>
<!-- end #logo -->

<?php
if (is_active_sidebar('top')) {
?>
<!-- begin #widget-area-top -->
<div id="widget-area-top-container"> 

<!-- begin #widget-area-top-second -->
<div id="widget-area-top-container-second">
<?php dynamic_sidebar('top'); ?>
</div>
<!-- end #widget-area-top-container-second -->

</div>
<!-- end #widget-area-top-container -->
<?php
}
?>

<!-- begin nav -->
<nav>
<?php
if (has_nav_menu('ap_header_menu')) {
wp_nav_menu(array('theme_location' => 'ap_header_menu', 'menu_class' => 'nav-standard', 'container' => false));
} else {
?>
<ul class="nav-standard">

<?php
wp_list_pages('title_li=');
?>
</ul>
<?php
}
?>
</nav>
<!-- end nav -->

</div>
<!-- end #logo-nav-container-centered -->

</div>
<!-- end #logo-nav-container -->

</header>
<!-- end header -->

<?php
if ( $rule['fss'] != 'yes' ) {
?>

<!-- begin #main-container -->
<div id="main-container">

<!-- begin .container -->
<div class="container">

<!-- begin .sixteen.colums -->
<div class="sixteen columns">

<?php
if ( $rule['tcs'] == 'yes' ) {
?>

<div class="sc-fws-container no-sc position-top full-width" data-animation-speed="<?php echo( $slider_data['options'][0] ); ?>" data-pause-time="<?php echo( $slider_data['options'][1] ); ?>" data-autoplay="<?php echo( $slider_data['options'][2] ); ?>">
	<?php
	$p = 'style="position: relative; display: block; opacity: 0"';
	foreach( $slider_data['slides'] as $slide ) {
	?>
	<img <?php echo( $p ); ?> src="<?php echo( fImg::resize($slide['image_url'], 1000, (int)$slider_data['options'][3], true) ); ?>" data-caption="<?php _e( $slide['caption'] ); ?>" alt="" />
	<?php
		$p = '';
	}
	?>
	<div class="sc-fws-borders"></div>
	<a href="#" class="sc-fws-button-left">&lsaquo;</a>
	<a href="#" class="sc-fws-button-right">&rsaquo;</a>
	<div class="sc-fws-caption"></div>
</div>

<?php
}
}
?>