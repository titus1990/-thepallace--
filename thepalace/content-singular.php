<?php
global $display_sidebar, $rule, $ap_options, $palace_title;

if ( have_posts() ) : 
while (have_posts()) : the_post();

if ( $ap_options['ap_display_edit_link']['val'] == 'yes' ) {
	edit_post_link(__('Edit', 'thepalace'), '<p class="post-edit">', '</p>');
}
if ( ($ap_options['ap_title_location']['val'] == 'content') && ($palace_title != '') ) {
	echo('<h1>');
	_e( $palace_title );
	echo('</h1>');
	echo('<hr>');
}
$first_line = get_post_meta(get_the_ID(), 'pm-first-line', true);
if ( $first_line != '' ) {
	echo( '<p class="post-first-line">' . __($first_line) . '</p>' );
}
if ( has_post_thumbnail() && (get_post_meta(get_the_ID(), 'pm-display-feature-image', true) != 'dont-display-feature-image') ) {
	$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
	echo( '<p class="post-thumbnail"><a class="scale-with-grid" href="' . $full_image_url[0] . '" rel="ap-light-box">' );
	if ($display_sidebar) {
		the_post_thumbnail( 'image-630x210' );
	} else {
		the_post_thumbnail( 'image-width-930' );
	}
	echo( '</a></p>' );
} 
the_content();
get_template_part( 'meta' );
wp_link_pages();

if ( $rule['display_comments'] == 'yes' ) {
	comments_template();
}

endwhile;
endif;
?>