<?php

add_action('admin_menu', 'add_aurel_hotel_admin_in_menu');

function current_user_can_manage_hotel() {
	global $current_user;
	$users_can_access = explode(',', get_option('ah_users_can_access'));
	for ($i=0; $i<sizeof($users_can_access); $i++) {
		$users_can_access[$i] = trim($users_can_access[$i]);
	}
  get_currentuserinfo();
  return in_array($current_user->user_login, $users_can_access);
}

function add_aurel_hotel_admin_in_menu() {
	if (current_user_can('edit_theme_options') || current_user_can_manage_hotel() ) {
		$page = add_menu_page('Booking System', 'Booking System', 'read', 'aurel_hotel_admin', 'aurel_hotel_admin_display_launch');
		add_action('admin_print_styles-' . $page, 'aurel_hotel_admin_init');
	}
}

function aurel_hotel_admin_init() {
	wp_enqueue_style('ah-style', get_template_directory_uri().'/aurelien-hotel/aurelien-hotel.css');
	wp_enqueue_style('ah-date', get_template_directory_uri().'/aurelien-hotel/jquery.datepick.css');
	
	wp_enqueue_script('ap-jq-ui', get_template_directory_uri().'/aurelien-panel/js/jquery-ui-1.8.18.custom.min.js');
	wp_enqueue_script('ah-date', get_template_directory_uri().'/js/jquery.datepick.min.js');
	wp_enqueue_script('ah-script', get_template_directory_uri().'/aurelien-hotel/aurelien-hotel.js');
}

function aurel_hotel_admin_display_launch() {
	require_once( get_template_directory() . '/aurelien-hotel/admin_display.php' );
	aurel_hotel_admin_display();
}

function register_aurel_hotel_settings() {
	register_setting( 'ah-settings-group', 'ah_form_id' );
	register_setting( 'ah-settings-group', 'ah_room_type_names' );
	register_setting( 'ah-settings-group', 'ah_room_type_slugs' );
	register_setting( 'ah-settings-group', 'ah_calendars_data' );
	register_setting( 'ah-settings-group', 'ah_date_picker_options' );
	register_setting( 'ah-settings-group', 'ah_date_format' );
	register_setting( 'ah-settings-group', 'ah_date_picker_lang' );
	register_setting( 'ah-settings-group', 'ah_users_can_access' );
	register_setting( 'ah-settings-group', 'ah_date_picker_default_lang' );
	register_setting( 'ah-settings-group', 'ah_mail_from' );
	register_setting( 'ah-settings-group', 'ah_mail_subject' );
	register_setting( 'ah-settings-group', 'ah_mail_confirmation_content' );
	register_setting( 'ah-settings-group', 'ah_mail_refusal_content' );
	register_setting( 'ah-settings-group', 'ah_confirmation_mail' );
	register_setting( 'ah-settings-group', 'ah_rejection_mail' );
	
}

add_action( 'admin_init', 'register_aurel_hotel_settings' );

function save_reservation( $form ) {
	
	if ( in_array($form->id, explode(',', get_option('ah_form_id'))) ) {
		$reservations = get_option('ah_reservations');
		$data = '';
		$mail = '';
		foreach ( $form->posted_data as $data_key => $data_value ) {
			if ( $data_key{0} != '_' ) {
				$data .= '<br/>' . ucfirst($data_key) . ': <b>' . $data_value . '</b>';
			}
			if ( is_string($data_value) && is_email($data_value) && ($mail == '') ) {
				$mail = $data_value;
			}
		}
		$data = substr( $data, 5);
		$reservations[] = array('status' => 'pending', 'received_on' => substr(current_time('mysql'),0,10), 'mail' => $mail, 'data' => $data);
		update_option( 'ah_reservations', $reservations );
	}
	
}

add_action('wpcf7_before_send_mail', 'save_reservation');

function ah_confirm_resa() {
	if ( wp_verify_nonce( $_POST['nonce'], 'ah_nonce_resa_action' ) && (current_user_can('edit_theme_options') || current_user_can_manage_hotel()) ) {
		$resas = get_option( 'ah_reservations' );
		$resa = $resas[$_POST['id']];
		$error = false;
		if ( $_POST['send_mail'] == 1 ) {
			$headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n";
			$mail_from = get_option( 'ah_mail_from' );
			if ( $mail_from != '' ) {
				$headers .= 'From: ' . $mail_from . "\r\n";
			}
			if ( wp_mail( $resa['mail'], get_option( 'ah_mail_subject' ), str_replace('[booking-data]', $resa['data'], $_POST['message']), $headers ) ) {
				echo( 'E-mail sent. ' );
			} else {				
				echo( 'Error. E-mail not sent. ' );
				global $phpmailer;
				echo( $phpmailer->ErrorInfo );
				$error = true;
			}
		} 
		if ( !$error ) {
			$resa['status'] = 'confirmed';
			$resas[$_POST['id']] = $resa;
			update_option( 'ah_reservations', $resas );
			echo( 'Database updated.' );
		}
	}
	die();
}

function ah_reject_resa() {
	if ( wp_verify_nonce( $_POST['nonce'], 'ah_nonce_resa_action' ) && (current_user_can('edit_theme_options') || current_user_can_manage_hotel()) ) {
		$resas = get_option( 'ah_reservations' );
		$resa = $resas[$_POST['id']];
		$error = false;
		if ( $_POST['send_mail'] == 1 ) {
			$headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n";
			$mail_from = get_option( 'ah_mail_from' );
			if ( $mail_from != '' ) {
				$headers .= 'From: ' . $mail_from . "\r\n";
			}
			if ( wp_mail( $resa['mail'], get_option( 'ah_mail_subject' ), str_replace('[booking-data]', $resa['data'], $_POST['message']), $headers ) ) {
				echo( 'E-mail sent. ' );
			} else {
				echo( 'Error. E-mail not sent. ' );
				global $phpmailer;
				echo( $phpmailer->ErrorInfo );
				$error = true;
			}
		} 
		if ( !$error ) {
			$resa['status'] = 'rejected';
			$resas[$_POST['id']] = $resa;
			update_option( 'ah_reservations', $resas );
			echo( 'Database updated.' );
		}
	}
	die();
}

function ah_delete_resa() {
	if ( wp_verify_nonce( $_POST['nonce'], 'ah_nonce_resa_action' ) && (current_user_can('edit_theme_options') || current_user_can_manage_hotel()) ) {
		$ids = explode(',', $_POST['ids']);
		$resas = get_option( 'ah_reservations' );
		foreach ( $ids as $id ) {
			unset($resas[$id]);
		}
		$resas = array_values($resas);
		update_option( 'ah_reservations', $resas );
		echo( 'Deleted.' );
	}
	die();
}

function ah_save_data() {
	if ( wp_verify_nonce( $_POST['nonce'], 'ah_nonce_resa_action' ) && (current_user_can('edit_theme_options') || current_user_can_manage_hotel()) ) {
		update_option( 'ah_calendars_data', stripslashes($_POST['ah_calendars_data']) );
		$options_to_save = array('ah_confirmation_mail', 'ah_rejection_mail', 'ah_mail_confirmation_content', 'ah_mail_refusal_content', 'ah_mail_from', 'ah_mail_subject');
		update_option( 'ah_confirmation_mail', $_POST['ah_confirmation_mail'] );
		foreach( $options_to_save as $op ) {
			update_option($op, $_POST[$op]);
		}		
		echo( '<p>Data saved.</p>' );
	}
	die();
}

add_action('wp_ajax_ah_confirm_resa', 'ah_confirm_resa');
add_action('wp_ajax_ah_reject_resa', 'ah_reject_resa');
add_action('wp_ajax_ah_delete_resa', 'ah_delete_resa');
add_action('wp_ajax_ah_save_data', 'ah_save_data');
?>