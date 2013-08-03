<?php
global $ap_options, $rule;

if ( $rule['fss'] != 'yes' ) {
	if ( $ap_options['ap_footer_location']['val'] == 'inside' ) {
?>

<!-- begin footer inside #main-container -->
<div class="clear"></div>

<footer class="textured-area textured-area-bottom-full-width">

<!-- begin #footer-content -->
<div id="footer-content" class="textured-area-content">

<!-- begin .four.columns -->
<div class="four columns alpha">
<?php dynamic_sidebar('footer_1'); ?>
</div>
<!-- end .four.columns -->

<!-- begin .four.columns -->
<div class="four columns">
<?php dynamic_sidebar('footer_2'); ?>
</div>
<!-- end .four.columns -->

<!-- begin .four.columns -->
<div class="four columns">
<?php dynamic_sidebar('footer_3'); ?>
</div>
<!-- end .four.columns -->

<!-- begin .four.columns -->
<div class="four columns omega">
<?php dynamic_sidebar('footer_4'); ?>
</div>
<!-- end .four.columns -->

<div class="clear"></div>

<?php if ( $ap_options['ap_footer_copyright']['val'] != '' ) { ?>
<div id="footer-copyright">
<?php _e( $ap_options['ap_footer_copyright']['val'] ); ?>
</div>
<?php } ?>

</div>
<!-- end #footer-content -->

</footer>
<!-- end footer inside #main-container-->

<?php
	}
?>

</div>
<!-- end .sixteen.columns -->

</div>
<!-- end .container -->

</div>
<!-- end #main-container -->

<?php if ( $rule['footer-image'] != '' ) { ?>
<div id="body-footer-transition"></div>
<?php } ?>

<?php if ( $ap_options['ap_footer_location']['val'] == 'below' ) { ?>

<!-- begin footer below #main-container -->
<footer class="below-main-container">

<!-- begin #footer-content -->
<div id="footer-content" class="container">

<!-- begin .four.columns -->
<div class="four columns">
<?php dynamic_sidebar('footer_1'); ?>
</div>
<!-- end .four.columns -->

<!-- begin .four.columns -->
<div class="four columns">
<?php dynamic_sidebar('footer_2'); ?>
</div>
<!-- end .four.columns -->

<!-- begin .four.columns -->
<div class="four columns">
<?php dynamic_sidebar('footer_3'); ?>
</div>
<!-- end .four.columns -->

<!-- begin .four.columns -->
<div class="four columns">
<?php dynamic_sidebar('footer_4'); ?>
</div>
<!-- end .four.columns -->

<?php if ( $ap_options['ap_footer_copyright']['val'] != '' ) { ?>
<div id="footer-copyright" class="sixteen columns">
<?php _e( $ap_options['ap_footer_copyright']['val'] ); ?>
</div>
<?php } ?>

</div>
<!-- end #footer-content -->

<?php if ( $rule['footer-image'] != '' ) { ?>
<div id="footer-image">
	<img src="<?php echo( fImg::resize( $rule['footer-image'], 1000, (int)$ap_options['ap_footer_min_height']['val'], true ) ); ?>" alt="" />
</div>
<div id="footer-mask"></div>
<?php } ?>

</footer>
<!-- end footer below #main-container -->

<?php 
}
?>

<?php 
if ( $ap_options['ap_footer_location']['val'] == 'inside' ) {
	if ( $rule['footer-image'] != '' ) { 
?>
<div id="footer-image-container">
	<div id="footer-image">
		<img src="<?php echo( fImg::resize( $rule['footer-image'], 1000, (int)$ap_options['ap_footer_min_height']['val'], true ) ); ?>" alt="" />
	</div>
	<div id="footer-mask"></div>
</div>
<?php 
	} else { 
?>
<div id="footer-image-container"></div>
<?php
	} 
}

}
echo( $ap_options['ap_custom_footer_code']['val'] ); 
wp_footer(); 
?>

</body>
</html>