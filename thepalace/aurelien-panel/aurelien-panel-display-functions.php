<?php
function aurel_panel_display_input_text( $key, $option_val ) {
	echo ( '<p><input type="text" id="' . $key . '" name="' . $key . '" value="' . htmlentities($option_val) . '" size="15" /></p>' . "\n" );
}

function aurel_panel_display_input_text_w50p( $key, $option_val ) {
	echo ( '<p><input type="text" id="' . $key . '" name="' . $key . '" class="ap-width-50p" value="' . htmlentities($option_val) . '" /></p>' . "\n" );
}

function aurel_panel_display_textarea( $key, $option_val ) {
	echo( '<p><textarea id="' . $key . '" name="' . $key . '" cols="10" rows="10">' . htmlentities($option_val) . '</textarea></p>' . "\n" );
}

function aurel_panel_display_radio( $key, $radio_options, $option_val, $more = '' ) {
	echo('<p>');
	foreach ( $radio_options as $radio_option ) {
		echo ( '<input type="radio" id="' . $key . '_' . $radio_option . '" name="' . $key . '" value="' . $radio_option . '" ' . $more );
		if ( $radio_option == $option_val ) {
			echo ( ' checked ' );
		}
		echo ( ' /> ');
		echo ( '<label for="' . $key . '_' . $radio_option . '">' . ucfirst($radio_option) . '</label>' );
		echo( '&nbsp;&nbsp;&nbsp;&nbsp;' );
	} 
	echo ( "</p>\n" );
}

function aurel_panel_display_check_boxes( $key, $check_boxes_options, $option_val ) {
	foreach ( $check_boxes_options as $check_box_option ) {
		echo( '<p class="ap-float-left ap-width-20p">' );
		echo ( '<input type="checkbox" id="' . $key . '_' . $check_box_option . '" name="' . $key . '[]" value="' . $check_box_option . '"');
		if ( in_array( $check_box_option, $option_val )) {
			echo ( ' checked ' );
		}
		echo ( ' /> ');
		echo ( '<label for="' . $key . '_' . $check_box_option . '">' . ucfirst($check_box_option) . '</label>' );
		echo( '</p>' );
	} 
	echo( "<div class=\"clear\"></div>\n" );
}

function aurel_panel_display_select( $key, $select_options, $option_val, $more = '' ) {
	echo(' <p><select id="' . $key . '" name="' . $key . '" ' . $more . '>' );
	foreach ( $select_options as $select_option ) {
		echo( '<option value="' . $select_option . '"' );
		if ( $select_option == $option_val ) {
			echo( 'selected ' );
		}
		echo('>');
		echo( ucfirst($select_option) );
		echo( '</option>' );
	}
	echo ( "</select></p>\n" );
}

function aurel_panel_display_select_advanced( $key, $class, $select_options, $option_val ) {
	echo(' <p><select id="' . $key . '" name="' . $key . '" class="' . $class . '">' );
	foreach ( $select_options as $select_option ) {
		echo( '<option value="' . $select_option[0] . '"' );
		if ( $select_option[0] == $option_val ) {
			echo( 'selected ' );
		}
		echo('>');
		echo( $select_option[1] );
		echo( '</option>' );
	}
	echo ( "</select></p>\n" );
}

function aurel_panel_display_slider_selector( $key, $option_val ) {
	global $ap_options;
	$ap_global_slider_manager = $ap_options['ap_global_slider_manager']['val'];
	$slider_names = array(array('no-slider','Do not display a slider'));
	foreach ( $ap_global_slider_manager as $slider ) {
		$slider_names[] = array($slider['name'], $slider['name']);
	}
	if ( sizeof($slider_names) == 1 ) {
		$slider_names = array(array('no-slider','No slider created yet'));
	}
	aurel_panel_display_select_advanced( $key, 'ap-slider-selector', $slider_names, $option_val );
}

function aurel_panel_display_sidebar_selector( $key, $option_val, $for_post_meta=false ) {
	global $ap_options;
?>
	<p>
		<select id="<?php echo( $key ); ?>" name="<?php echo( $key ); ?>" class="ap-sidebar-selector">
			<?php
			if ( $for_post_meta ) {
			?>
			<option value="default" <?php if ($option_val == 'default') { echo( 'selected ' ); } ?>>Don't override default theme option</option>
			<?php
			}
			?>
			<option value="default_sidebar" <?php if ($option_val == 'default_sidebar') { echo( 'selected ' ); } ?>>Default sidebar</option>
			<?php
			$sidebars = $ap_options['ap_widget_areas_manager']['val'];
			foreach ($sidebars as $s) {
			?>
			<option value="<?php echo( $s ); ?>" <?php if ($option_val == $s ) { echo( 'selected ' ); } ?>><?php echo( ucfirst($s) ); ?></option>
			<?php } ?>
		</select>
	</p>
<?php
}

function aurel_panel_display_color_picker( $key, $option_val ) {
	echo ( '<p><input type="text" id="' . $key . '" name="' . $key . '" value="' . $option_val . '" size="10" class="aurel-panel-color-picker" /></p>' . "\n");
}

function aurel_panel_display_image_upload( $key, $option_val ) {
	echo( '<p><input type="text" id="' . $key . '" name="' . $key . '" value="' . $option_val . '" class="ap-width-75p" />&nbsp;&nbsp;<input type="button" class="button aurel-panel-upload-button" value="Select image" /></p>' . "\n");
}

function aurel_panel_display_sub_section( $name ) {
	echo( "<h3 class=\"aurel-panel-sub-section\">$name</h3>\n" );
}
?>