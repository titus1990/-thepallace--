<?php
class AP_Social_Widget extends WP_Widget {
	private $ap_sw_defaults = array(
		'title' => '',
		'aim' => '',
		'apple' => '',
		'bebo' => '',
		'blogger' => '',
		'brightkite' => '',
		'cargo' => '',
		'delicious' => '',
		'designfloat' => '',
		'designmoo' => '',
		'deviantart' => '',
		'digg' => '',
		'digg_alt' => '',
		'dopplr' => '',
		'dribbble' => '',
		'email' => '',
		'ember' => '',
		'evernote' => '',
		'facebook' => '',
		'flickr' => '',
		'forrst' => '',
		'friendfeed' => '',
		'gamespot' => '',
		'google' => '',
		'google_plus' => '',
		'google_voice' => '',
		'google_wave' => '',
		'googletalk' => '',
		'gowalla' => '',
		'grooveshark' => '',
		'ilike' => '',
		'komodomedia_azure' => '',
		'komodomedia_wood' => '',
		'lastfm' => '',
		'linkedin' => '',
		'mixx' => '',
		'mobileme' => '',
		'mynameise' => '',
		'myspace' => '',
		'netvibes' => '',
		'newsvine' => '',
		'openid' => '',
		'orkut' => '',
		'pandora' => '',
		'paypal' => '',
		'picasa' => '',
		'pinboard' => '',
		'playstation' => '',
		'plurk' => '',
		'posterous' => '',
		'qik' => '',
		'rdio' => '',
		'readernaut' => '',
		'reddit' => '',
		'roboto' => '',
		'rss' => '',
		'sharethis' => '',
		'skype' => '',
		'slashdot' => '',
		'steam' => '',
		'stumbleupon' => '',
		'technorati' => '',
		'tumblr' => '',
		'twitter' => '',
		'viddler' => '',
		'vimeo' => '',
		'virb' => '',
		'windows' => '',
		'wordpress' => '',
		'xanga' => '',
		'xing' => '',
		'yahoo' => '',
		'yahoobuzz' => '',
		'yelp' => '',
		'youtube' => '',
		'zootool' => ''
	);

	

	function __construct() {
		$widget_ops = array( 'description' => 'Social icons linked to social websites' );
		parent::__construct('ap_social_widget', '[Theme] Social widget', $widget_ops);
	}



	function widget( $args, $instance ) {
		extract($args);
		if (empty($instance['title'])) {
			$title = '';
		} else {
			$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		}
		echo $before_widget;
		if ( $title != '' ) {

			echo $before_title . $title . $after_title;
		}
		echo('<ul class="ap-social-widget">');
		$instance = wp_parse_args( (array) $instance, $this->ap_sw_defaults );
		foreach ($this->ap_sw_defaults as $ap_sw_default_key => $ap_sw_default_value) {
			if ( ($ap_sw_default_key != 'title') && ($instance[$ap_sw_default_key] != '') ) {
			?>
				<li><a href="<?php echo( $instance[$ap_sw_default_key] ); ?>"><img src="<?php echo( get_template_directory_uri() . '/img/social-icons/' . $ap_sw_default_key . '_16.png' ); ?>" alt="" /></a></li>
			<?php
			}
		}
		echo('<li class="clear"></li></ul><div class="clear"></div>');
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, $this->ap_sw_defaults );
		$instance = $new_instance;
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->ap_sw_defaults );
		foreach ($this->ap_sw_defaults as $ap_sw_default_key => $ap_sw_default_value) {
			$ap_sw_label_output = $ap_sw_default_key;
			if ($ap_sw_label_output != 'title') {
				$ap_sw_label_output = $ap_sw_label_output . ' url';
			}
			$ap_sw_label_output = ucfirst($ap_sw_label_output) . ':';
		?>
			<p>
				<label for="<?php echo( $this->get_field_id($ap_sw_default_key) ); ?>"><?php echo($ap_sw_label_output); ?></label>
				<input class="widefat" id="<?php echo( $this->get_field_id($ap_sw_default_key) ); ?>" name="<?php echo( $this->get_field_name($ap_sw_default_key) ); ?>" type="text" value="<?php echo(esc_attr($instance[$ap_sw_default_key])); ?>" />
			</p>
		<?php
		}
	}
}

class AP_Category_Posts extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'description' => 'Display the posts of a category' );
		parent::__construct('ap_category_posts', '[Theme] Category Posts', $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		if (empty($instance['title'])) {
			$title = '';
		} else {
			$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		}

		if (empty($instance['category'])) {
			$category = 'uncategorized';
		} else {
			$category = $instance['category'];
		}

		if (empty($instance['max'])) {
			$max = '999';
		} else {
			$max = $instance['max'];
		}
		echo $before_widget;
		if ( $title != '' ) {
			echo $before_title . $title . $after_title;
		}
		$post_id = -1;
		if ( is_single() ) {
			global $wp_query;
			$post_id = $wp_query->post->ID;
		}
		query_posts(array('category_name' => $category, 'posts_per_page' => $max));
		if (have_posts()) :
			echo(' <ul>' );
			while (have_posts()) : the_post();
				$class = '';
				if ( get_the_ID() == $post_id ) {
					$class = ' class="current-menu-item" ';
				}
				echo( '<li' . $class . '><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>' );
			endwhile;
			echo(' </ul>' );
		endif;
		wp_reset_query();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = '';
		}
		if ( isset( $instance[ 'category' ] ) ) {
			$category = $instance[ 'category' ];
		} else {
			$category = '';
		}

		if ( isset( $instance[ 'max' ] ) ) {
			$max = $instance[ 'max' ];
		} else {
			$max = '';
		}
	?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo( esc_attr($title) ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>">Select a category:</label><br/>
			<select id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>">
			<?php
			$categories = get_categories( 'hide_empty=0' );
			$cat_options = array();
			foreach ($categories as $cat) {
				$cat_options[] = array( $cat->slug, $cat->name );
				if ( $cat->slug == $category ) {
					$cat_selected = 'selected';
				} else {
					$cat_selected = '';
				}
			?>
				<option value="<?php echo( $cat->slug ); ?>" <?php echo ( $cat_selected ); ?>><?php echo( $cat->name ); ?></option>
			<?php
			}
			?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'max' ); ?>">Maximum number of posts to show:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'max' ); ?>" name="<?php echo $this->get_field_name( 'max' ); ?>" type="text" value="<?php echo( esc_attr($max) ); ?>" />
		</p>
	<?php
	}
}

class AP_Gallery_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'description' => 'Display a gallery with images opening in a light-box' );
		parent::__construct('ap_gallery_widget', '[Theme] Gallery', $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		if (empty($instance['title'])) {
			$title = '';
		} else {
			$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		}
		if (empty($instance['images'])) {
			$images = '';
		} else {
			$images = $instance['images'];
		}
		if (empty($instance['link_text'])) {
			$link_text = '';
		} else {
			$link_text = $instance['link_text'];
		}
		if (empty($instance['link_url'])) {
			$link_url = '';
		} else {
			$link_url = $instance['link_url'];
		}
		if (empty($instance['link_parameters'])) {
			$link_parameters = '';
		} else {
			$link_parameters = $instance['link_parameters'];
		}

		echo $before_widget;
		if ( $title != '' ) {
			echo $before_title . $title . $after_title;
		}
		echo('<div class="ap-gallery-widget">');
		$attachments = explode(',',$images);
		global $wpdb;
		$gal_num = rand();
		$img_num = 1;
		foreach ($attachments as $attachment) {
			$attachment_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . trim($attachment) . "'" );
			$image_full_src = wp_get_attachment_image_src($attachment_id, 'full');
			echo( '<a rel="ap-light-box[wp-gallery-' . $gal_num . ']" href="' . $image_full_src[0] .'">' . wp_get_attachment_image($attachment_id, 'image-120x120') . '</a>' );
			if ( $img_num % 3 != 0 ) {
				echo( '<div class="gallery-margin"></div>' );
			}
			$img_num++;
		}
		echo('<div class="clear"></div>');
		if ( trim($link_text) != '' ) {
			echo('<a ' . $link_parameters . ' href="' . $link_url . '">' . $link_text . '</a>');
		}
		echo('</div>' );
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = '';
		}
		if ( isset( $instance[ 'images' ] ) ) {
			$images = $instance[ 'images' ];
		} else {
			$images = '';
		}		
		if ( isset( $instance[ 'link_text' ] ) ) {
			$link_text = $instance[ 'link_text' ];
		} else {
			$link_text = '';
		}		
		if ( isset( $instance[ 'link_url' ] ) ) {
			$link_url = $instance[ 'link_url' ];
		} else {
			$link_url = '';
		}
		if ( isset( $instance[ 'link_parameters' ] ) ) {
			$link_parameters = $instance[ 'link_parameters' ];
		} else {
			$link_parameters = '';
		}
		
		
	?>

	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo( esc_attr($title) ); ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'images' ); ?>">List of images (a comma-separated list of the images to be displayed, you need to specify the name of images as it appears in the media library):</label><br/>
		<textarea class="widefat" rows="3" id="<?php echo $this->get_field_id( 'images' ); ?>" name="<?php echo $this->get_field_name( 'images' ); ?>"><?php echo( esc_attr($images) ); ?></textarea>
	</p>
	
	<p>
		<label for="<?php echo $this->get_field_id( 'link_text' ); ?>">Link text (if you want to display a link below the gallery, enter the text of the link here):</label><br/>
		<input class="widefat" id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>" type="text" value="<?php echo( esc_attr($link_text) ); ?>" />
	</p>
	
	<p>
		<label for="<?php echo $this->get_field_id( 'link_url' ); ?>">Link url:</label><br/>
		<input class="widefat" id="<?php echo $this->get_field_id( 'link_url' ); ?>" name="<?php echo $this->get_field_name( 'link_url' ); ?>" type="text" value="<?php echo( esc_attr($link_url) ); ?>" />
	</p>
	
	<p>
		<label for="<?php echo $this->get_field_id( 'link_parameters' ); ?>">Link parameters:</label><br/>
		<input class="widefat" id="<?php echo $this->get_field_id( 'link_parameters' ); ?>" name="<?php echo $this->get_field_name( 'link_parameters' ); ?>" type="text" value="<?php echo( esc_attr($link_parameters) ); ?>" />
	</p>
	
	<?php
	}
}
?>