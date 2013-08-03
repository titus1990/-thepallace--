<?php 
global $ap_options, $rule, $palace_title;

if ( $ap_options['ap_title_location']['val'] == 'content' ) {
	if ( $palace_title == '' ) {
?>
<p class="blog-description"><?php bloginfo('description'); ?></p><hr>
<?php } else { ?>
<h1><?php _e( $palace_title ); ?></h1><hr>

<?php
	}
}

if ( is_category() && (category_description() != '')) {
	echo( '<div class="category-description"><h2>Have a look at our rooms!</h2>' );
	_e( category_description() );
	echo( '</div><hr>' );
}

$post_number = 0;
if ( have_posts() ) : 
while (have_posts()) : the_post();

if ( ($rule['layout'] == 'three-cols') && (($post_number % 3) == 0) ) {
?>
<!-- begin .one-third.column.alpha -->
<div class="one-third column alpha">

<?php
} elseif ( ($rule['layout'] == 'three-cols') && (($post_number % 3) == 1) ) {
?>
<!-- begin .one-third.column -->
<div class="one-third column">

<?php
} elseif ( ($rule['layout'] == 'three-cols') && (($post_number % 3) == 2) ) {
?>
<!-- begin .one-third.column.omega -->
<div class="one-third column omega">
<?php
}
?>

<!-- begin #post-<?php the_ID(); ?> -->
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php 
if ( $rule['display-title'] == 'yes' ) { 
?>
<h2><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
<?php 
} 

if ( $rule['show-thumbnail'] == 'yes' ) {
	if ( has_post_thumbnail() ) {
		if ( $rule['layout'] == 'full-width' ) {
			$thumb_size = 'image-930x310';
		} elseif ( $rule['layout'] == 'three-cols' ) {
			$thumb_size = 'image-420x150';
		} else {
			$thumb_size = 'image-630x210';
		}
		if ( $rule['thumbnail-links'] == 'post' ) {
			echo( '<p class="post-thumbnail"><a class="scale-with-grid" href="' . get_permalink() . '" >' );
			the_post_thumbnail( $thumb_size );
			echo( '</a></p>' );		
		} else {
			$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
			echo( '<p class="post-thumbnail"><a class="scale-with-grid" href="' . $full_image_url[0] . '" rel="ap-light-box[gallery]">' );
			the_post_thumbnail( $thumb_size );
			echo( '</a></p>' );
		}
	} 
}

if ( $ap_options['ap_display_edit_link']['val'] == 'yes' ) {
	edit_post_link(__('Edit', 'thepalace'), '<p class="post-edit">', '</p>');
}

if ( $rule['content'] == 'content' ) {
	the_content('');
} else if ( $rule['content'] == 'excerpt' ) {
	the_excerpt();
}

if ( $rule['learn-more'] != 'none' ) {
	$learn_more_class = '';
	if ( $rule['learn-more'] == 'button' ) {
		$learn_more_class = 'palace-button';
	}
?>

<p><a class="<?php echo( $learn_more_class ); ?>" href="<?php the_permalink() ?>" rel="bookmark"><?php _e( 'Learn more &raquo;', 'thepalace' ); ?></a></p>
<?php
}

get_template_part( 'meta' );
?>

</div>
<!-- end #post-<?php the_ID(); ?> -->

<?php 
if ( $rule['layout'] == 'three-cols' ) {
?>
</div>
<!-- end one-third.column-third -->

<?php
if ( ($post_number % 3) == 2 ) {
?>
<div class="clear"></div>

<?php
}
}
$post_number++;

endwhile;

if ( ($rule['layout'] == 'three-cols') && (($post_number % 3) != 0 ) ) {
?>
<div class="clear"></div>

<?php
}

global $wp_query, $paged;
$max_page = $wp_query->max_num_pages;
if ($max_page > 1) {
?>

<p class="pagination">
<?php
	if ( empty($paged) ) {
		$paged = 1;
	}
	echo ( '<span class="pagination-current">' . sprintf( __('Page %s of %s', 'thepalace'), $paged, $max_page ) . __(' - Go to page:', 'thepalace') . '</span> ' );
	for($i=1;$i<=$max_page;$i++) {
		if ($i != $paged) {
			echo('<a href="' . get_pagenum_link($i) . '">' . $i . '</a> ');
		}
	}
	echo(' <span class="clear"></span> ');
?>
</p>

<?php
};
else :
	if ( !is_404() ) {
		echo('<p>' . __('Sorry, but nothing matched your search criteria.', 'thepalace') . '</p>');
	}
endif;

if ( is_404() ) {
	echo( '<p>' . $ap_options['ap_404_message']['val'] . '</p>' );
}
?>