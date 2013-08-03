<?php
function display_ah_room_types() {
?>
<h3>Room types</h3>
<div id="ah-room-type-wrapper">
<?php 
$rtslugs = get_option('ah_room_type_slugs');
$rtnames = get_option('ah_room_type_names');
$i = 0;
if ( $rtslugs ) {
	$rtslugs = explode(',', $rtslugs);
	$rtnames = explode(',', $rtnames);
	foreach ($rtslugs as $slug) {
		echo('<div class="ah-room-type"><a href="#" class="ah-room-type-remove"></a><p>Room type slug: <b class="ah-room-type-slug">' . $slug . '</b> - Room type name: <input type="text" class="ah-room-type-name" size="20" value="' . $rtnames[$i] . '" /></p></div>');
		$i++;
	}
}
?>
</div>
<p>
Id: <input id="ah-room-type-slug" type="text" size="20" value="" />&nbsp;&nbsp;&nbsp;
Name: <input id="ah-room-type-name" type="text" size="20" value="" />&nbsp;&nbsp;&nbsp;
<input type="button" id="ah-add-room-type" class="button" value="Add room type" />
</p>
<input type="hidden" id="ah_room_type_slugs" name="ah_room_type_slugs" value="" />
<input type="hidden" id="ah_room_type_names" name="ah_room_type_names" value="" />
<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

<hr/>
<?php
}

function display_ah_rooms_availability($is_admin) {
$select_room_type_options = '';
$divs_room_type_availability = '';
$room_types = get_option('ah_room_type_slugs');
if ($room_types != '' ) {
	$room_types = explode(',', $room_types);
	$first_type_of_room = $room_types[0];
	foreach($room_types as $t) {
		$t = trim($t);
		$select_room_type_options .= '<option value="' . $t . '">' . $t . '</option>';
		$divs_room_type_availability .= '<div id="calendar-' . $t . '" class="ah-calendar"></div>';
	}
?>

<h3>Rooms availability</h3>
<p>Select room type
<select id="select-room-type">
	<?php echo( $select_room_type_options ); ?>
</select>
</p>

<p>
Select the unavaible dates for the type of room <b id="type-of-room-selected"><?php echo( $first_type_of_room ); ?></b>:
</p>

<div id="ah-calendars">
	<?php 
	echo( $divs_room_type_availability ); 
	$calendars_data = get_option('ah_calendars_data');
	if ( $calendars_data == '' ) {
		$calendars_data = '[]';
	}
	?>
	<input id="ah-calendars-data" name="ah_calendars_data" type="hidden" value="<?php echo( htmlspecialchars($calendars_data) ); ?>" />
</div>

<?php if ( $is_admin ) { ?>
<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
<?php } else { ?>
<p>
	<input type="button" class="button ah-ajax-save" value="Save Changes" />
</p>
<div class="ah-ajax-updated"></div>
<?php
}
} else {
?>

<p>At least one type of room should be defined to access the availability calendars.</p>
<input id="ah-calendars-data" name="ah_calendars_data" type="hidden" value="[]" />

<?php
}
?>

<hr/>

<?php
}

function display_ah_resa() {
?>

<div id="ah-resa">

<div id="ajax-resa-message" class="ah-updated"></div>

<h3>Booking requests</h3>

<table id="ah-reservations-list" class="wp-list-table widefat" cellspacing="0">
	
	<thead>
		<tr>
			<th><input type="checkbox" class="ah-checkbox-resa-all"/></th>
			<th>Status</th>
			<th>Received on</th>
			<th>Booking data</th>
			<th>Actions</th>
		</tr>
	</thead>
	
	<tfoot>
		<tr>
			<th width="5%"><input type="checkbox" class="ah-checkbox-resa-all"/></th>
			<th width="7%">Status</th>
			<th width="10%">Received on</th>
			<th width="63%">Booking data</th>
			<th width="15%">Actions</th>
		</tr>
	</tfoot>
	
	<tbody>
		<?php 
		$resas = get_option( 'ah_reservations' );
		if ( !$resas ) {
			$resas = array();
		}
		$i = 0;
		foreach( $resas as $resa ) {
		?>
		<tr>
			<td>&nbsp;&nbsp;<input type="checkbox" class="ah-checkbox-resa" value="<?php echo( $i ); ?>"/></td>
			<td>
				<div class="ah-status">
				<?php if ( $resa['status'] == 'confirmed' ) { ?>
					<span class="ah-confirmed">confirmed</span>
				<?php } elseif ( $resa['status'] == 'rejected' ) { ?>
					<span class="ah-rejected">rejected</span>
				<?php } else { ?>
					<span style="display: none" class="ah-confirmed">confirmed</span>
					<span style="display: none" class="ah-rejected">rejected</span>
					<span class="ah-pending">pending</span>
				<?php } ?>
				</div>
			</td>
			<td>
			<?php if ( isset($resa ['received_on']) ) {	echo( $resa ['received_on'] ); } ?>
			</td>
			<td><?php echo( str_replace('<br/>', '&nbsp;&nbsp;-&nbsp;&nbsp;', $resa['data']) ); ?></td>
			<td>
				<?php if ( $resa['status'] == 'confirmed' ) { ?>
					<a href="#" class="ah-resa-del" title="Delete"><?php echo( $i ); ?></a>
				<?php } elseif ( $resa['status'] == 'rejected' ) { ?>
					<a href="#" class="ah-resa-del" title="Delete"><?php echo( $i ); ?></a>
				<?php } else { ?>
					<a href="#" class="ah-resa-ok" title="Confirm"><?php echo( $i ); ?></a>
					<a href="#" class="ah-resa-ko" title="Reject"><?php echo( $i ); ?></a>
					<a href="#" class="ah-resa-del" title="Delete"><?php echo( $i ); ?></a>
				<?php } ?>
			</td>
		</tr>
		<?php
			$i++;
		}
		?>
		
	</tbody>
	
</table>

<p style="margin-bottom: 20px;">
<a id="ah-delete-selected-resa" href="#">Delete selected</a>
</p>

</div>

<hr/>

<?php
}

function display_ah_request_replies($is_admin) {
$ah_confirmation_mail_yes_checked = 'checked';
$ah_confirmation_mail_no_checked = '';
$ah_rejection_mail_yes_checked = 'checked';
$ah_rejection_mail_no_checked = '';
if ( get_option('ah_confirmation_mail') == 'no' ) {
	$ah_confirmation_mail_yes_checked = '';
	$ah_confirmation_mail_no_checked = 'checked';
}
if ( get_option('ah_rejection_mail') == 'no' ) {
	$ah_rejection_mail_yes_checked = '';
	$ah_rejection_mail_no_checked = 'checked';
}

?>
<h3>Request replies</h3>
<table class="form-table">
<tr>
	<th>Send automatically an e-mail when confirming a request</th>
	<td>
		<input type="radio" id="ah_confirmation_mail_yes" name="ah_confirmation_mail" value="yes" <?php echo( $ah_confirmation_mail_yes_checked ); ?>> <label for="ah_confirmation_mail_yes">Yes</label>&nbsp;&nbsp;&nbsp;
		<input type="radio" id="ah_confirmation_mail_no" name="ah_confirmation_mail" value="no" <?php echo( $ah_confirmation_mail_no_checked ); ?>> <label for="ah_confirmation_mail_no">No</label>
	</td>
</tr>
<tr>
	<th>Send automatically an e-mail when rejecting a request</th>
	<td>
		<input type="radio" id="ah_rejection_mail_yes" name="ah_rejection_mail" value="yes" <?php echo( $ah_rejection_mail_yes_checked ); ?>> <label for="ah_rejection_mail_yes">Yes</label>&nbsp;&nbsp;&nbsp;
		<input type="radio" id="ah_rejection_mail_no" name="ah_rejection_mail" value="no" <?php echo( $ah_rejection_mail_no_checked ); ?>> <label for="ah_rejection_mail_no">No</label>
	</td>
</tr>

	<th><label for="ah_mail_confirmation_content">Confirmation message</label></th>
	<td><textarea class="widefat" name="ah_mail_confirmation_content" id="ah_mail_confirmation_content" rows="10" cols="30"><?php echo( get_option('ah_mail_confirmation_content') ); ?></textarea><br />
	<span class="description">This is the message which is sent when you confirm a reservation. The [booking-data] shortcode is used for displaying the booking data in the mail.</span></td>
</tr>
<tr>
	<th><label for="ah_mail_refusal_content">Refusal message</label></th>
	<td><textarea class="widefat" name="ah_mail_refusal_content" id="ah_mail_refusal_content" rows="10" cols="30"><?php echo( get_option('ah_mail_refusal_content') ); ?></textarea><br />
	<span class="description">This is the message which is sent when you reject a reservation.</span></td>
</tr>
<tr>
	<th><label for="ah_mail_from">E-mail address</label></th>
	<td><input type="text" name="ah_mail_from" id="ah_mail_from" value="<?php echo( get_option('ah_mail_from') ); ?>" size="60" /><span class="description">&nbsp;&nbsp;This is the address the recipient will see in the <b>from</b> field (insert a complete e-mail address eg. Name &lt;contact@the-palace.com&gt;).</span></td>
</tr>
<tr>
	<th><label for="ah_mail_subject">Subject message</label></th>
	<td><input type="text" name="ah_mail_subject" id="ah_mail_subject" value="<?php echo( get_option('ah_mail_subject') ); ?>" size="60" /><span class="description">&nbsp;&nbsp;This is the subject the recipient will see in the <b>subject</b> field.</span></td>
</tr>
</table>

<?php if ($is_admin) { ?>
<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
<?php } else { ?>
<p>
	<input type="button" class="ah-ajax-save button" value="Save Changes" />
</p>
<div class="ah-ajax-updated"><p>Options updated.</p></div>
<?php } ?>

<hr/>

<?php
}

function aurel_hotel_admin_display() {
	if ( current_user_can('edit_theme_options')) {
?>

<div class="wrap">
<h2>Booking System</h2>

<form id="aurelien-hotel-form" method="post" action="options.php">
<?php
settings_fields( 'ah-settings-group' );
wp_nonce_field( 'ah_nonce_resa_action', 'ah_nonce_resa_action' );

if ( isset($_GET['settings-updated']) ) {
?>
<div id="ah-message" class="ah-updated"><p>Options updated.</p></div>
<?php
}
?>

<hr/>

<h3>Form id</h3>
<p>Enter the id of the form which is used for sending a request of a reservation (if you have several forms separate ids with commas).</p>
<p><input type="text" name="ah_form_id" value="<?php echo get_option('ah_form_id'); ?>" /></p>

<hr/>

<?php 
display_ah_room_types();
display_ah_rooms_availability(true);
display_ah_resa(); 
display_ah_request_replies(true);
?>

<h3>Booking system access</h3>
<p>Enter the username of people who can access the booking system (separate username with commas).</p>
<p><input type="text" name="ah_users_can_access" value="<?php echo get_option('ah_users_can_access'); ?>" /></p>

<h3>Date picker options</h3>
<p>Select a default date format.</p>
<p>
	<?php $current_date_format = get_option('ah_date_format'); ?>
	<select id="ah_date_format" name="ah_date_format">
		<option value="dd-mm-yyyy" <?php if ( $current_date_format == 'dd-mm-yyyy' ) { echo( 'selected' ); }; ?>>dd-mm-yyyy</option>
		<option value="mm-dd-yyyy" <?php if ( $current_date_format == 'mm-dd-yyyy' ) { echo( 'selected' ); }; ?>>mm-dd-yyyy</option>
		<option value="yyyy-mm-dd" <?php if ( $current_date_format == 'yyyy-mm-dd' ) { echo( 'selected' ); }; ?>>yyyy-mm-dd</option>
	</select>
<p>
<?php
$datepick_langs = array(
	'English/US' => 'en-US',
	'Afrikaans' => 'af',
	'Albanian' => 'sq',
	'Amharic' => 'am',
	'Arabic' => 'ar',
	'Arabic/Algeria' => 'ar-DZ',
	'Arabic/Egypt' => 'ar-EG',
	'Armenian' => 'hy',
	'Azerbaijani' => 'az',
	'Basque' => 'eu',
	'Bosnian' => 'bs',
	'Bulgarian' => 'bg',
	'Catalan' => 'ca',
	'Chinese Hong Kong' => 'zh-HK',
	'Chinese Simplified' => 'zh-CN',
	'Chinese Traditional' => 'zh-TW',
	'Croatian' => 'hr',
	'Czech' => 'cs',
	'Danish' => 'da',
	'Dutch' => 'nl',
	'Dutch/Belgian' => 'nl-BE',
	'English/Australia' => 'en-AU',
	'English/New Zealand' => 'en-NZ',
	'English/UK' => 'en-GB',
	'Esperanto' => 'eo',
	'Estonian' => 'et',
	'Faroese' => 'fo',
	'Farsi/Persian' => 'fa',
	'Finnish' => 'fi',
	'French' => 'fr',
	'French/Swiss' => 'fr-CH',
	'Galician' => 'gl',
	'Georgian' => 'ka',
	'German' => 'de',
	'German/Swiss' => 'de-CH',
	'Greek' => 'el',
	'Gujarati' => 'gu',
	'Hebrew' => 'he',
	'Hungarian' => 'hu',
	'Icelandic' => 'is',
	'Indonesian' => 'id',
	'Italian' => 'it',
	'Japanese' => 'ja',
	'Khmer' => 'km',
	'Korean' => 'ko',
	'Latvian' => 'lv',
	'Lithuanian' => 'lt',
	'Macedonian' => 'mk',
	'Malayalam' => 'ml',
	'Malaysian' => 'ms',
	'Maltese' => 'mt',
	'Montenegrin' => 'me',
	'Montenegrin' => 'me-ME',
	'Norwegian' => 'no',
	'Polish' => 'pl',
	'Portuguese/Brazil' => 'pt-BR',
	'Romanian' => 'ro',
	'Romansh' => 'rm',
	'Russian' => 'ru',
	'Serbian' => 'sr',
	'Serbian' => 'sr-SR',
	'Slovak' => 'sk',
	'Slovenian' => 'sl',
	'Spanish' => 'es',
	'Spanish/Argentina' => 'es-AR',
	'Spanish/Peru' => 'es-PE',
	'Swedish' => 'sv',
	'Tamil' => 'ta',
	'Thai' => 'th',
	'Turkish' => 'tr',
	'Ukrainian' => 'uk',
	'Urdu' => 'ur',
	'Vietnamese' => 'vi'
);
?>
<p>Select languages you want to load for the date picker (language codes separed with commas, eg: <b>fr,de</b>)</p>
<p>
<input type="text" name="ah_date_picker_lang" size="100" value="<?php echo get_option('ah_date_picker_lang'); ?>" />
</p>

<p>Select default language</p>
<p>
	<select name="ah_date_picker_default_lang">
		<?php 
		$current_lang = get_option( 'ah_date_picker_default_lang' );
		foreach( $datepick_langs as $key => $value ) { 
			if ( $value == $current_lang ) {
				$selected = ' selected';
			} else {
				$selected ='';
			}
		?>
		<option value="<?php echo( $value ); ?>"<?php echo( $selected ); ?>><?php echo( $key ); ?></option>
		<?php
		}
		?>
	</select>
</p>

<p>Date picker options (advanced settings).</p>
<p>
<input type="text" name="ah_date_picker_options" size="100" value="<?php echo get_option('ah_date_picker_options'); ?>" />
</p>

<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>

</div><!-- end .wrap -->
<?php
	} else if ( current_user_can_manage_hotel() ) {
?>
<div class="wrap">
<h2>Booking System</h2>

<form id="aurelien-hotel-form" method="post" action="options.php">
<?php
settings_fields( 'ah-settings-group' );
wp_nonce_field( 'ah_nonce_resa_action', 'ah_nonce_resa_action' );
echo('<input id="ah_date_format" type="hidden" value="' . get_option('ah_date_format') . '" />');
display_ah_resa();
display_ah_rooms_availability(false);
display_ah_request_replies(false);
?>
</form> 

</div><!-- end .wrap -->
<?php	
	}
}
?>