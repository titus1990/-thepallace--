jQuery(document).ready(function(jQ){
	
	function suahm() {
		jQ('#ah-message, .ah-ajax-updated').slideUp(1000);
	}
	
	setTimeout(suahm, 3000);

	jQ('#ah-add-room-type').click(function() {
		if ( /^\w+$/.test( jQ('#ah-room-type-slug').val() ) ) {
			jQ('#ah-room-type-wrapper').append('<div class="ah-room-type"><a href="#" class="ah-room-type-remove"></a><p>Room type slug: <b class="ah-room-type-slug">' + jQ('#ah-room-type-slug').val() + '</b> - Room type name: <input type="text" class="ah-room-type-name" size="20" value="' + jQ('#ah-room-type-name').val() + '" /></p></div>');
			jQ('#ah-room-type-slug').val('');
			jQ('#ah-room-type-name').val('');
		} else {
			alert('The id can contain only letters, numbers, and underscores.');
		}
	});
	
	function init_room_type_remove() {
		jQ('body').on('click', '.ah-room-type-remove', function() {
			if (confirm('Delete this room type? (this will also delete all the data linked to this type of room)') ) {
				jQ(this).parent().slideUp().remove();
			}
			return false;
		});
	}
	init_room_type_remove();
	
	jQ('#ah-room-type-wrapper').sortable({
		axis: 'y'
	});
	
	var ahcdl = JSON.parse(jQ('#ah-calendars-data').val());
	
	jQ('.ah-calendar').datepick({
		monthsToShow: [2, 6],
		monthsToStep: 12, 
		prevText: 'Prev months', 
		nextText: 'Next months',
		changeMonth: false,
		firstDay: 1,
		useMouseWheel: false,
		multiSelect: 99999,
		dateFormat: jQ('#ah_date_format').val(),
		renderer: jQ.extend({}, jQ.datepick.defaultRenderer, 
        {picker: jQ.datepick.defaultRenderer.picker.replace(/\{link:today\}/, '{link:current}')})
	});
		
	jQ('.ah-calendar').each(function() {
		jQ(this).datepick('setDate', ahcdl[jQ(this).attr('id')]);
	});
	
	jQ('#select-room-type').change(function() {
		jQ('.ah-calendar').css('display', 'none');
		jQ('#calendar-' + jQ(this).val()).fadeIn(1000);
		jQ('#type-of-room-selected').html(jQ(this).val());
	});
	
	function prepare_calendars_data() {
		var ahcd = {};
		var date_format = jQ('#ah_date_format').val();
		jQ('.ah-calendar').each(function() {
			var ds = jQ(this).datepick('getDate');
			var dsa = new Array();
			for(var i=0;i<ds.length;i++) {
				dsa.push(jQ.datepick.formatDate(date_format,ds[i]));
			}
			ahcd[jQ(this).attr('id')] = dsa;
		});
		jQ('#ah-calendars-data').val(JSON.stringify(ahcd));	
	}
	
	jQ('#aurelien-hotel-form').submit(function() {
		prepare_calendars_data();
		var room_type_slugs = '';
		var room_type_names = '';
		jQ('.ah-room-type').each(function() {
			room_type_slugs += jQ(this).find('.ah-room-type-slug').html() + ',';
			room_type_names += jQ(this).find('.ah-room-type-name').val() + ',';
		});
		jQ('#ah_room_type_slugs').val(room_type_slugs.substring(0,room_type_slugs.length-1));
		jQ('#ah_room_type_names').val(room_type_names.substring(0,room_type_names.length-1));
	});
	
	var timer_fadeout_ajax_message = 'no_timer';
	
	function fadeout_ajax_message() {
		jQ('#ajax-resa-message').fadeOut();
	}
	
	jQ('.ah-resa-ok').click(function() {
		if ( timer_fadeout_ajax_message != 'no_timer' ) {
			window.clearTimeout(timer_fadeout_ajax_message);
		}
		var id = jQ(this).html();
		var send_mail = 1;
		if ( jQ('input:radio[name=ah_confirmation_mail]:checked').val() == 'yes' ) {
			jQ('#ajax-resa-message').html('<span class="ah-ajaxer">Sending mail and updating database...</span>');
		} else {
			jQ('#ajax-resa-message').html('<span class="ah-ajaxer">Updating database...</span>');
			send_mail = 0;
		}
		jQ('#ajax-resa-message').fadeIn();
		var data = {
			action: 'ah_confirm_resa',
			nonce: jQ('#ah_nonce_resa_action').val(),
			id: id,
			send_mail: send_mail,
			message: jQ('#ah_mail_confirmation_content').val()
		};
		jQ.post(ajaxurl, data, function(response) {
			if ( response.indexOf('Error') == -1 ) {
				id = parseInt(id) + 2;
				var current_tr = jQ('#ah-reservations-list').find('tr').eq(id);
				current_tr.find('.ah-confirmed').fadeIn();
				current_tr.find('.ah-pending').fadeOut();
				current_tr.find('.ah-resa-ok').fadeOut();
				current_tr.find('.ah-resa-ko').fadeOut();
			}
			jQ('#ajax-resa-message').html(response);
			timer_fadeout_ajax_message = setTimeout(fadeout_ajax_message, 3000);
		});
		return false;
	});
	
	jQ('.ah-resa-ko').click(function() {
		if ( timer_fadeout_ajax_message != 'no_timer' ) {
			window.clearTimeout(timer_fadeout_ajax_message);
		}
		var id = jQ(this).html();
		var send_mail = 1;
		if ( jQ('input:radio[name=ah_rejection_mail]:checked').val() == 'yes' ) {
			jQ('#ajax-resa-message').html('<span class="ah-ajaxer">Sending mail and updating database...</span>');
		} else {
			jQ('#ajax-resa-message').html('<span class="ah-ajaxer">Updating database...</span>');
			send_mail = 0;
		}
		jQ('#ajax-resa-message').fadeIn();
		var data = {
			action: 'ah_reject_resa',
			nonce: jQ('#ah_nonce_resa_action').val(),
			id: id,
			send_mail: send_mail,
			message: jQ('#ah_mail_refusal_content').val()
		};
		jQ.post(ajaxurl, data, function(response) {
			if ( response.indexOf('Error') == -1 ) {
				id = parseInt(id) + 2;
				var current_tr = jQ('#ah-reservations-list').find('tr').eq(id);
				current_tr.find('.ah-rejected').fadeIn();
				current_tr.find('.ah-pending').fadeOut();
				current_tr.find('.ah-resa-ok').fadeOut();
				current_tr.find('.ah-resa-ko').fadeOut();
			}
			jQ('#ajax-resa-message').html(response);
			timer_fadeout_ajax_message = setTimeout(fadeout_ajax_message, 3000);
		});
		return false;
	});
	
	jQ('.ah-resa-del').click(function() {
		if ( confirm('Delete the booking request ?') ) {
			var id = jQ(this).html(); 
			if ( timer_fadeout_ajax_message != 'no_timer' ) {
				window.clearTimeout(timer_fadeout_ajax_message);
			}
			jQ('#ajax-resa-message').html('<span class="ah-ajaxer">Deleting...</span>').fadeIn();
			var data = {
				action: 'ah_delete_resa',
				nonce: jQ('#ah_nonce_resa_action').val(),
				ids: id
			};
			jQ.post(ajaxurl, data, function(response) {
				jQ('#ajax-resa-message').html(response).fadeOut(0);
				jQ('#ah-reservations-list tbody tr').eq(id).fadeOut();
			});
		}
		return false;
	});
	
	jQ('#ah-delete-selected-resa').click(function() {
		if ( jQ('.ah-checkbox-resa:checked').length > 0 ) {
			if ( confirm('Delete the booking requests ?') ) {
				if ( timer_fadeout_ajax_message != 'no_timer' ) {
					window.clearTimeout(timer_fadeout_ajax_message);
				}
				jQ('#ajax-resa-message').html('<span class="ah-ajaxer">Deleting...</span>').fadeIn();
				var ids = '';
				jQ('.ah-checkbox-resa:checked').each(function() {
					ids = ids + ',' + jQ(this).val();
				});
				ids = ids.substring(1);
				var data = {
					action: 'ah_delete_resa',
					nonce: jQ('#ah_nonce_resa_action').val(),
					ids: ids
				};
				jQ.post(ajaxurl, data, function(response) {
					jQ('#ajax-resa-message').html(response).fadeOut(0);
					ids = ids.split(',');
					for (var i=0; i<ids.length; i++) {
						jQ('#ah-reservations-list tbody tr').eq(ids[i]).fadeOut();
					}
				});
			}
		} else {
			alert('No booking requests were selected.');
		}
		return false;
	});
	
	jQ('.ah-checkbox-resa-all').click(function() {
		if (jQ(this).is(':checked')) {
			jQ('.ah-checkbox-resa').prop('checked', true);
			jQ('.ah-checkbox-resa-all').prop('checked', true);
		} else {
			jQ('.ah-checkbox-resa').prop('checked', false);
			jQ('.ah-checkbox-resa-all').prop('checked', false);			
		}
	});
	
	jQ('.ah-ajax-save').click(function() {
		jQ('.ah-ajax-updated').html('<p>Saving data...</p>');
		jQ('.ah-ajax-updated').css('display','block');
		prepare_calendars_data();
		var data = {
			action: 'ah_save_data',
			nonce: jQ('#ah_nonce_resa_action').val(),
			ah_calendars_data: jQ('#ah-calendars-data').val(),
			ah_confirmation_mail: jQ('input[name=ah_confirmation_mail]:checked').val(),
			ah_rejection_mail: jQ('input[name=ah_rejection_mail]:checked').val(),
			ah_mail_confirmation_content: jQ('#ah_mail_confirmation_content').val(),
			ah_mail_refusal_content: jQ('#ah_mail_refusal_content').val(),
			ah_mail_from: jQ('#ah_mail_from').val(),
			ah_mail_subject: jQ('#ah_mail_subject').val()
		};
		jQ.post(ajaxurl, data, function(response) {
			jQ('.ah-ajax-updated').html(response);
			setTimeout(suahm, 3000);
		});
	});

});


/* Copyright AurelienD http://themeforest.net/user/AurelienD?ref=AurelienD */