<?php
global $rule, $ap_options, $display_sidebar;
$display_sidebar = false;

if ( is_single() || is_page() ) {
	$content_template = 'singular';
} else {
	$content_template = 'multiple';
}
if ( ($rule['layout'] == 'full-width') || ($rule['layout'] == 'three-cols') ) {
?>


<!-- begin content -->
<?php
get_template_part( 'content', $content_template ); 
?>

<!-- end content -->

<?php
} else if ( $rule['layout'] == 'one-col-left-sidebar' ) {
	$display_sidebar = true;
	if ($ap_options['ap_sidebars_texture']['val'] == 'yes' ) {
		$class1 = 'textured-area textured-area-top-left';
		$class2 = 'textured-area-content';
	} else {
		$class1 = 'sidebar-left';
		$class2 = 'sidebar-content';
	}
?>
<!-- begin sidebar -->
<div class="five columns alpha <?php echo( $class1 ); ?>">
<div class="<?php echo( $class2 ); ?>" >
<?php dynamic_sidebar( $rule['sidebar'] ); ?>
</div>
</div>
<!-- end sidebar -->

<!-- begin content -->
<div class="eleven columns omega">
<?php get_template_part( 'content', $content_template ); ?>
</div>
<!-- end content -->

<?php
} elseif ( $rule['layout'] == 'one-col-right-sidebar' ) {
	$display_sidebar = true;
	if ($ap_options['ap_sidebars_texture']['val'] == 'yes' ) {
		$class1 = ' textured-area textured-area-top-right';
		$class2 = ' textured-area-content';
	} else {
		$class1 = 'sidebar-right';
		$class2 = 'sidebar-content';
	}
?>
<!-- begin content -->
<div class="eleven columns alpha">
<?php get_template_part( 'content', $content_template ); ?>
</div>
<!-- end content -->

<!-- begin sidebar -->
<div class="five columns omega <?php echo( $class1 ); ?>">
<div class="<?php echo( $class2 ); ?>" >
<?php dynamic_sidebar( $rule['sidebar'] ); ?>
</div>
</div>
<!-- end sidebar -->

<?php
}
?>