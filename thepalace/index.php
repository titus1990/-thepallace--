<?php
global $rule;
get_header();

if ( is_page() || is_single() ) {
	if ( $rule['fss'] != 'yes' ) {
		get_template_part( 'container' );
	}
} else {
	get_template_part( 'container' );
}

get_footer();
?>