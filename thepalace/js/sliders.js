/* Copyright AurelienD http://themeforest.net/user/AurelienD?ref=AurelienD */

function full_width_slider(autoplay, pause_time, transition_speed) {
	
	var total_slides = 0,
		current_slide = 0,
		next_slide = 0,
		processing = false,
		timer_slider = 0,
		first_slide_done = false;
		
	total_slides = jQuery('.full-width-slide').length;
	current_slide = total_slides - 1;
	
	if (total_slides <= 1) {
		jQuery('#slider-button-left, #slider-button-right').css('display', 'none');
	}
	
	if ( jQuery('.full-width-slide img').length == 0 ) {
		fadein_next_slide();
	} else {
		jQuery('#full-width-slider').addClass('loading');
		var timer_launch_slider = setTimeout(fadein_first_slide, 8000); //launch the fadein_next_slide function anyway. See caveats of load function: http://api.jquery.com/load-event/
		jQuery('<img />').attr('src', jQuery('.full-width-slide:first-child img').attr('src')).load(function () {
			window.clearTimeout(timer_launch_slider);
			fadein_first_slide();
		});
	}

	function set_processing_to_false() {
		processing = false;
	}
	
	function fadein_first_slide() {
		if ( !first_slide_done ) {
			first_slide_done = true;
			fadein_next_slide();
		}
	}
	
	function fadein_next_slide() {
		jQuery('.full-width-slide').css({'z-index': '0'});
		jQuery('.full-width-slide').eq(current_slide).css({'z-index': '4'});
		jQuery('.full-width-slide').eq(next_slide).css('display', 'none').css({'z-index': '5'}).fadeIn(transition_speed);
		if ( jQuery('.full-width-slide').eq(next_slide).find('.full-width-slide-caption').length != 0 ) {
			setTimeout(fadein_caption, transition_speed / 1.5);
		} 
		if (autoplay) {
			timer_slider = setTimeout(display_next_slide, pause_time);
		}
		setTimeout(set_processing_to_false, transition_speed * 1.5);
		current_slide = next_slide;
	}
	
	function fadeout_caption() {
		jQuery('#fws-caption').animate({left: '-50px', opacity: 0}, transition_speed / 1.5);
	}
	
	function fadein_caption() {
		jQuery('#fws-caption').html(jQuery('.full-width-slide').eq(next_slide).find('.full-width-slide-caption').html());
		jQuery('#fws-caption').animate({'left': '0px', opacity: 1}, transition_speed / 1.5);
	}
	
	function display_next_slide() {
		if (!processing) {
			window.clearTimeout(timer_slider);
			processing = true;
			if (current_slide + 1 === total_slides) {
				next_slide = 0;
			} else {
				next_slide = current_slide + 1;
			}
			if (jQuery('.full-width-slide').eq(current_slide).find('.full-width-slide-caption').length != 0) {
				fadeout_caption();
			} 
			fadein_next_slide();
		}
	}
	
	function display_previous_slide() {
		if (!processing) {
			window.clearTimeout(timer_slider);
			processing = true;
			if (current_slide === 0) {
				next_slide = total_slides - 1;
			} else {
				next_slide = current_slide - 1;
			}
			if (jQuery('.full-width-slide').eq(current_slide).find('.full-width-slide-caption').length != 0) {
				fadeout_caption();
			} 
			fadein_next_slide();
		}
	}
	
	jQuery('#slider-button-left').click(function() {
		display_previous_slide();
		return false;
	});
	
	jQuery('#slider-button-right').click(function() {
		display_next_slide();
		return false;
	});
	
}

function full_screen_slider(the_slider) {

	var jQ = jQuery.noConflict();
	
	// image resolutions:
	// 1600 x 900, 1200 x 675 -> desktops and laptops
	// 1024 x 768, 768 x 1024 -> ipad (landscape / portrait)
	// 480 x 320, 320 x 480 -> mobile (landscape / portrait)
	// landscape and portrait resolutions from bigger to smaller
	landscape_resolutions = [
		{'width': 1600, 'height': 900 },
		{'width': 1200, 'height': 675 },
		{'width': 800, 'height': 600 },
		{'width': 480,  'height': 320 }
	],
	portrait_resolutions = [
		{'width': 768,  'height': 1024 },
		{'width': 320,  'height': 480 }
	];
	
	function debouncer( func , timeout ) {
		var timeoutID , timeout = timeout || 50;
		return function () {
			var scope = this , args = arguments;
			clearTimeout( timeoutID );
			timeoutID = setTimeout( function () {
				func.apply( scope , Array.prototype.slice.call( args ) );
			} , timeout );
		}
	}
	
	function get_slider_img_resolution() {
		var the_slider_resolution = {'width': jQ(the_slider).width(), 'height': jQ(the_slider).height()},
			available_resolutions = [],
			longest_side = '';
			
		if ( the_slider_resolution.width > the_slider_resolution.height ) {
			available_resolutions = landscape_resolutions;
			longest_side = 'width';
		} else {
			available_resolutions = portrait_resolutions;
			longest_side = 'height';
		}
		
		var chosen_resolution = available_resolutions[0];
		for (var i=0; i<available_resolutions.length; i++) {
			if ( the_slider_resolution[longest_side] <= available_resolutions[i][longest_side] ) {
				chosen_resolution = available_resolutions[i];
			}
		}
		return chosen_resolution;
	}
	
	function load_img(n_slide) {
		if ( (n_slide > 0) && (n_slide <= number_of_slides) ) {
			var sr = slider_img_resolution.width + 'x' + slider_img_resolution.height;
				current_url = jQ(the_slider + ' .full-screen-slide').eq(n_slide-1).find('img').attr('scr'),
				new_url = img_urls[n_slide-1][sr];
			if ( new_url != current_url ) {
				jQ('<img style="position: absolute; visibility: hidden;" />').attr('src', img_urls[n_slide-1][sr] ).load(function() {
					jQ(the_slider + ' .full-screen-slide').eq(n_slide-1).prepend(jQ(this));
					jQ(this).css({'display': 'none', 'visibility':'visible'});
					resize_imgs();
					jQ(this).fadeIn(function() {
						if ( jQ(the_slider + ' .full-screen-slide').eq(n_slide-1).find('img').length > 1 ) {
							jQ(the_slider + ' .full-screen-slide').eq(n_slide-1).find('img').eq(1).remove();
						}
						if ( loading_first_image && (current_slide == 1) ) {
							loading_first_image = false;
							processing = false;
							setTimeout(display_caption, 500);
							if ( autoplay ) {
								timer_slider = setTimeout( right_intent, pause_time );
							}
						}
					});
				});
			}
		}
	}
	
	function load_img_around_current_slide() {
		if ( current_slide == 1 ) {
			load_img(2);
			load_img(number_of_slides);
		} else if ( current_slide == number_of_slides ) {
			load_img(number_of_slides-1);
			load_img(1);
		} else {
			load_img(current_slide-1);
			load_img(current_slide+1);
		}	
	}
	
	function resize_slides() {
		var ws = jQ(the_slider).width(),
			hs = jQ(the_slider).height();
		
		jQ(the_slider + ' .full-screen-slide').css({ 'width': ws + 'px', 'height': hs + 'px' });
		jQ(the_slider + ' #full-screen-slider-mover').css('left', -ws * (current_slide-1) + 'px');
	}

	function resize_imgs() {
		var ws = jQ(the_slider).width(),
			hs = jQ(the_slider).height(),
			ratio_slider = ws / hs;
		
		jQ(the_slider + ' img').each(function() {
			var img = jQ(this),
				ratio_img = jQ(this).width() / jQ(this).height();
			if ( ratio_slider < ratio_img ) {
				img.css({'height': '100%', 'width': 'auto'});
				img.css({'left': '50%', 'margin-left': -Math.floor(img.width()/2) + 'px', 'margin-top': 0,  'top': 0});		
			} else {
				img.css({'height': 'auto', 'width': '100%'});
				img.css({'left': 0, 'margin-left': 0, 'margin-top': -Math.floor(img.height()/2) + 'px', 'top': '50%'});
			}
		});
	}
	
	function slider_buttons_position() {
		if ( jQ(the_slider + ' #fss-caption').height() == 60 ) {
			jQ('.fss-button-left, .fss-button-right').css('bottom','150px');
		} else {
			jQ('.fss-button-left, .fss-button-right').css('bottom','120px');
		}
	}
	
	function hide_caption() {
		jQ(the_slider + ' #fss-caption').animate({'right': '-100px', 'opacity': 0 }, animation_speed);
	}
	
	function display_caption() {
		if ( captions[current_slide-1] != '') {
			jQ(the_slider + ' #fss-caption').html(captions[current_slide-1]);
			jQ(the_slider + ' #fss-caption').animate({'right': '10px', 'opacity': 1 }, animation_speed);
			slider_buttons_position();
		} else {
			jQ('.fss-button-left, .fss-button-right').css('bottom','50px');
		}
	}
	
	function after_animation() {
		setTimeout(display_caption, animation_speed/2);
		processing = false;
	}
	
	function go_to_left() {
		current_slide--;
		jQ(the_slider + ' #full-screen-slider-mover').animate({'left': '+=' + jQ(the_slider).width() + 'px'}, animation_speed, after_animation);
	}
	
	function go_to_right() {
		current_slide++;
		jQ(the_slider + ' #full-screen-slider-mover').animate({'left': '-=' + jQ(the_slider).width() + 'px'}, animation_speed, after_animation);
	}	
	
	function go_to_first() {
		current_slide = 1;
		jQ(the_slider + ' #full-screen-slider-mover').animate({'left': 0}, animation_speed, after_animation);
	}	
	
	function go_to_last() {
		current_slide = number_of_slides;
		jQ(the_slider + ' #full-screen-slider-mover').animate({'left': '-=' + jQ(the_slider).width()*(number_of_slides-1) + 'px'}, animation_speed, after_animation);
	}
	
	function update_position_indicator() {
		jQ(the_slider + ' #position-indicator').html(current_slide + ' / ' + number_of_slides);
	}
	
	function before_move() {
		processing = true;
		clearTimeout(timer_slider);
		hide_caption();
	}
	
	function after_move() {
		update_position_indicator();
		load_img_around_current_slide();
		if ( autoplay ) {
			timer_slider = setTimeout( right_intent, pause_time );
		}
	}
	
	function left_intent() {
		if ( !processing ) {
			before_move();
			if ( current_slide > 1 ) { 
				go_to_left();
			} else {
				go_to_last();
			}
			after_move();
		}
	}
	
	function right_intent() {
		if ( !processing ) { 
			before_move();
			if ( current_slide < number_of_slides ) {
				go_to_right();
				load_img(current_slide + 1);
			} else {
				go_to_first();
			}
			after_move();
		}	
	}
	
	jQ(the_slider + ' .fss-button-left').click(function() {
		left_intent();
		return false;
	});	
	
	jQ(the_slider + ' .fss-button-right').click(function() {
		right_intent();
		return false;
	});
	
	jQ(window).resize(debouncer(function(e) {
		var new_resolution = get_slider_img_resolution();
		if ( (new_resolution.width != slider_img_resolution.width) || (new_resolution.height != slider_img_resolution.height) ) {
			slider_img_resolution = new_resolution;
			load_img(current_slide);
			load_img_around_current_slide();
		}
		resize_slides();
		resize_imgs();
		slider_buttons_position();
	}));
	
	function init() {
		number_of_slides = img_urls.length;
		for (var i=0;i<number_of_slides;i++) {
			jQ(the_slider + ' #full-screen-slider-mover').prepend('<div class="full-screen-slide"></div>')
		}
		resize_slides();
		load_img(current_slide);
		load_img(current_slide+1);
		load_img(number_of_slides);
		update_position_indicator();
		if ( autoplay ) {
			timer_slider = setTimeout( autoplay_nextslide, pause_time );
		}
	}

	var current_slide = 1,
		number_of_slides = 0,
		slider_img_resolution = get_slider_img_resolution(),
		timer_slider = 0,
		processing = true,
		loading_first_image = true,
		autoplay = jQ(the_slider).data('autoplay') == 'yes',
		animation_speed = jQ(the_slider).data('animation-speed'),
		pause_time = jQ(the_slider).data('pause-time'),
		img_urls = jQ(the_slider).data('img-urls'), 
		captions = jQ(the_slider).data('captions');
	
	init();
}

function full_width_slider_inside(the_slider) {

	var jQ = jQuery.noConflict(),
		current_slide = 1,
		processing = false,
		timer_slider = 0,
		number_of_slides = the_slider.find('img').length,
		animation_speed = the_slider.data('animation-speed'),
		pause_time = the_slider.data('pause-time'),
		autoplay = the_slider.data('autoplay') == 'yes',
		font_size = the_slider.find('.sc-fws-caption').css('font-size').replace('px','');
	
	function debouncer( func , timeout ) {
		var timeoutID , timeout = timeout || 50;
		return function () {
			var scope = this , args = arguments;
			clearTimeout( timeoutID );
			timeoutID = setTimeout( function () {
				func.apply( scope , Array.prototype.slice.call( args ) );
			} , timeout );
		}
	}	
	
	function hide_caption() {
		the_slider.find('.sc-fws-caption').animate({'left': '75px', 'opacity': 0 }, animation_speed);
	}
	
	function display_caption() {
		var c = the_slider.find('img').eq(current_slide-1).data('caption');
		if ( c != '' ) {
			the_slider.find('.sc-fws-caption').html(the_slider.find('img').eq(current_slide-1).data('caption')).animate({'left': '25px', 'opacity': 1 }, animation_speed);
		}
	}
	
	function go_prev() {
		the_slider.find('img').eq(current_slide-1).fadeOut(animation_speed, function() {
			processing = false;
			display_caption();
		});
		current_slide--;	
	}
	
	function go_next() {
		the_slider.find('img').eq(current_slide).fadeIn(animation_speed, function() {
			processing = false;
			display_caption();
		});
		current_slide++;
	}
	
	function go_first() {
		for (var i=1;i<number_of_slides-1;i++) {
			the_slider.find('img').eq(i).css('display','none');
		}
		the_slider.find('img').eq(number_of_slides-1).fadeOut(animation_speed, function() {
			processing = false;
			display_caption();
		});
		current_slide = 1;
	}
	
	function go_last() {
		the_slider.find('img').eq(number_of_slides-1).fadeIn(animation_speed, function() {
			the_slider.find('img').css('display','block');
			processing = false;
			display_caption();
		});
		current_slide = number_of_slides;
	}
	
	function prev_intent() {
		clearTimeout(timer_slider);
		hide_caption();
		if ( current_slide > 1 ) { 
			go_prev();
		} else {
			go_last();
		}
		if ( autoplay ) {
			timer_slider = setTimeout( next_intent, pause_time + animation_speed);
		}
	}
	
	function next_intent() {
		clearTimeout(timer_slider);
		hide_caption();
		if ( current_slide < number_of_slides ) {
			go_next();
		} else {
			go_first();
		}
		if ( autoplay ) {
			timer_slider = setTimeout( next_intent, pause_time + animation_speed);
		}
	}

	the_slider.find('.sc-fws-button-left').click(function() {
		if ( !processing ) {
			processing = true;
			prev_intent();
		}
		return false;
	});

	the_slider.find('.sc-fws-button-right').click(function() {
		if ( !processing ) {
			processing = true;
			next_intent();
		}
		return false;
	});
	
	function init() {
		jQ('<img />').attr('src', the_slider.find('img').eq(0).attr('src')).load(function() {
			the_slider.find('img').eq(0).animate({'opacity':1}, animation_speed, display_caption);
			if ( autoplay ) {
				timer_slider = setTimeout( next_intent, pause_time );
			}
		});
		
		jQ(window).resize(debouncer(function(e) {
			var w = the_slider.width();
			var s = Math.floor( (w/982)*font_size ) + 2;
			s = s + 'px';
			the_slider.find('.sc-fws-caption').css({'font-size': s, 'line-height': s});
		})).resize();
	}
	
	init();
}

function slider_type_a(the_slider) {

	var jQ = jQuery.noConflict();
	var cs = 0;
	var processing = false;
	var timer_slider = 0;
	
	jQ(window).load(function(){
		var h = the_slider.find('.slider-content-slide').eq(0).height();
		the_slider.find('.slider-content-second-container').height(h);
	});

	if ( the_slider.find('.slider-content-slide').length > 1 ){
		the_slider.find('.slider-content-button-left-disabled').css('display', 'block');
		the_slider.find('.slider-content-button-right').css('display', 'block');
	}
	
	function set_timer() {
		if ( the_slider.data('autoplay') != 'no' ) {
			timer_slider = setInterval(display_next_slide, the_slider.data('autoplay'));
		}
	}
	set_timer();
	
	function display_next_slide() {
		if ( !processing ) {
			if ( cs < the_slider.find('.slider-content-slide').length - 1 ) {
				go_to_right();
			} else {
				go_to_first();
			}
		}
	}
	
	function go_to_first() {
		processing = true;
		clearTimeout(timer_slider);
		the_slider.find('.slider-content-button-right-disabled').css('display', 'none');
		the_slider.find('.slider-content-button-right').css('display', 'block');	
		the_slider.find('.slider-content-button-left').css('display', 'none');
		the_slider.find('.slider-content-button-left-disabled').css('display', 'block');	
		the_slider.find('.slider-content-slide').css('display', 'block');
		var w = the_slider.find('.slider-content-slide').width();
		w = w + 30;
		w = w * (the_slider.find('.slider-content-slide').length - 1);
		the_slider.find('.slider-content-third-container').css({left: '-' + w + 'px'});
		the_slider.find('.slider-content-third-container').animate({left: 0}, function() {
			cs = 0;
			processing = false;
			set_timer();
		});
		var h = the_slider.find('.slider-content-slide').eq(0).height();
		the_slider.find('.slider-content-second-container').animate({height: h + 'px'});
	}
	
	function go_to_left() {
		processing = true;
		clearTimeout(timer_slider);
		the_slider.find('.slider-content-button-right-disabled').css('display', 'none');
		the_slider.find('.slider-content-button-right').css('display', 'block');	
		if ( cs == 1 ) {
			the_slider.find('.slider-content-button-left').css('display', 'none');
			the_slider.find('.slider-content-button-left-disabled').css('display', 'block');
		}	
		var w = the_slider.find('.slider-content-slide').width();
		w = w + 30;
		the_slider.find('.slider-content-slide').eq(cs-1).css('display', 'block');
		the_slider.find('.slider-content-third-container').css({left: '-' + w + 'px'});
		the_slider.find('.slider-content-third-container').animate({left: 0}, function() {
			cs--;
			processing = false;
			set_timer();
		});
		var h = the_slider.find('.slider-content-slide').eq(cs-1).height();
		the_slider.find('.slider-content-second-container').animate({height: h + 'px'});
	}
	
	function go_to_right() {
		processing = true;
		clearTimeout(timer_slider);
		the_slider.find('.slider-content-button-left-disabled').css('display', 'none');
		the_slider.find('.slider-content-button-left').css('display', 'block');
		if ( cs + 1 == the_slider.find('.slider-content-slide').length - 1 ) {
			the_slider.find('.slider-content-button-right').css('display', 'none');
			the_slider.find('.slider-content-button-right-disabled').css('display', 'block');
		}
		var w = the_slider.find('.slider-content-slide').width();
		w = w + 30;
		the_slider.find('.slider-content-third-container').animate({left: '-=' + w}, function() {
			the_slider.find('.slider-content-slide').eq(cs).css('display', 'none');
			the_slider.find('.slider-content-third-container').css({left: 0});
			cs++;
			processing = false;
			set_timer();
		});
		var h = the_slider.find('.slider-content-slide').eq(cs+1).height();
		the_slider.find('.slider-content-second-container').animate({height: h + 'px'});
	}
	
	
	the_slider.find('.slider-content-button-left').click(function() {
		if ( !processing ) {
			go_to_left();
		}
		return false;
	});
	
	the_slider.find('.slider-content-button-right').click(function() {
		if ( !processing ) {
			go_to_right();
		}
		return false;
	});
	
	jQ(window).resize(function() {
		var h = the_slider.find('.slider-content-slide').eq(cs).height();
		the_slider.find('.slider-content-second-container').css({height: h + 'px'});
	});

}

function slider_type_b(the_slider) {

	var jQ = jQuery.noConflict();
	var cs = 0;
	var processing = false;
	var timer_slider = 0;
	
	function get_cols_num() {
		var w = the_slider.find('.slider-content-slide').width();
		if ( ( w == 373 ) || ( w == 282 ) ) {
			return 2;
		} else {
			return 1;
		}
	}

	function set_timer() {
		if ( the_slider.data('autoplay') != 'no' ) {
			timer_slider = setInterval(display_next_slide, the_slider.data('autoplay'));
		}
	}
	set_timer();
	
	function display_next_slide() {
		if ( !processing ) {
			if ( cs < the_slider.find('.slider-content-slide').length - get_cols_num() ) {
				go_to_right();
			} else {
				go_to_first();
			}
		}
	}	
	
	jQ(window).load(function(){
		set_height(0,'no-anim');
	});
	
	function set_buttons(cs) {
		if  ( ((get_cols_num() == 2) && (the_slider.find('.slider-content-slide').length <= 2)) || ((get_cols_num() == 1) && (the_slider.find('.slider-content-slide').length <= 1)) ) {
			the_slider.find('.slider-content-button-left-disabled').css('display', 'none');
			the_slider.find('.slider-content-button-right-disabled').css('display', 'none');
			the_slider.find('.slider-content-button-left').css('display', 'none');
			the_slider.find('.slider-content-button-right').css('display', 'none');
			return;
		}
		the_slider.find('.slider-content-button-left').css('display', 'block');
		the_slider.find('.slider-content-button-right').css('display', 'block');
		the_slider.find('.slider-content-button-left-disabled').css('display', 'none');
		the_slider.find('.slider-content-button-right-disabled').css('display', 'none');
		if ( cs == 0 ) {
			the_slider.find('.slider-content-button-left-disabled').css('display', 'block');
			the_slider.find('.slider-content-button-left').css('display', 'none');
			return;
		}
		if ( (cs == the_slider.find('.slider-content-slide').length - 1) || ((get_cols_num() == 2) && (cs == the_slider.find('.slider-content-slide').length - 2)) ) {
			the_slider.find('.slider-content-button-right-disabled').css('display', 'block');
			the_slider.find('.slider-content-button-right').css('display', 'none');
			return;
		}
	}
	set_buttons(0);
	
	function set_height(cs,a) {
		var h = the_slider.find('.slider-content-slide').eq(cs).height();
		if ( get_cols_num() == 2 ) {
			var h2 = the_slider.find('.slider-content-slide').eq(cs+1).height();
			if ( h2 > h ) {
				h = h2;
			}
		}
		h = h + 20 + 2; //padding + borders
		if (a == 'anim') {
			the_slider.find('.slider-content-second-container').animate({height: h + 'px'});
		} else {
			the_slider.find('.slider-content-second-container').css({height: h + 'px'});
		}
	}
	
	function go_to_first() {
		processing = true;
		clearTimeout(timer_slider);
		set_buttons(0);
		set_height(0,'anim');
		var w = the_slider.find('.slider-content-slide').width();
		w = w + 40 + 2 + 40; //padding + borders + margin
		w = w * (the_slider.find('.slider-content-slide').length - get_cols_num());
		the_slider.find('.slider-content-slide').css('display', 'block');
		the_slider.find('.slider-content-third-container').css({left: '-' + w + 'px'});
		the_slider.find('.slider-content-third-container').animate({left: 0}, function() {
			cs = 0;
			set_timer();
			processing = false;
		});		
	}
	
	function go_to_left() {
		processing = true;
		clearTimeout(timer_slider);
		set_buttons(cs-1);
		set_height(cs-1,'anim');
		var w = the_slider.find('.slider-content-slide').width();
		w = w + 40 + 2 + 40; //padding + borders + margin
		the_slider.find('.slider-content-slide').eq(cs-1).css('display', 'block');
		the_slider.find('.slider-content-third-container').css({left: '-' + w + 'px'});
		the_slider.find('.slider-content-third-container').animate({left: 0}, function() {
			cs--;
			set_timer();
			processing = false;
		});	
	}
	
	function go_to_right() {
		processing = true;	
		clearTimeout(timer_slider);
		set_buttons(cs+1);
		set_height(cs+1,'anim');		
		var w = the_slider.find('.slider-content-slide').width();
		w = w + 40 + 2 + 40; //padding + borders + margin
		the_slider.find('.slider-content-third-container').animate({left: '-=' + w}, function() {
			the_slider.find('.slider-content-slide').eq(cs).css('display', 'none');
			the_slider.find('.slider-content-third-container').css({left: 0});
			cs++;
			set_timer();
			processing = false;
		});	
	}
	
	the_slider.find('.slider-content-button-left').click(function() {
		if ( !processing ) {
			go_to_left();
		}
		return false;
	});
	
	the_slider.find('.slider-content-button-right').click(function() {
		if ( !processing ) {
			go_to_right();
		}
		return false;
	});
	
	jQ(window).resize(function() {
		set_buttons(cs);
		if ( get_cols_num() == 2 ) {
			var n = the_slider.find('.slider-content-slide').length;
			if ( cs == n - 1 ) {
				the_slider.find('.slider-content-slide').eq(n-1).css('display', 'block');
				the_slider.find('.slider-content-slide').eq(n-2).css('display', 'block');
				cs--;
			}
		}
		set_height(cs,'no-anim');
	});
}

function gallery_ap() {

	var jQ = jQuery.noConflict();

	jQ('.gallery-ap .gallery-slider a:first-child').addClass('gallery-current-thumb');
	jQ('.gallery-ap .gallery-slider a').hover(
		function () {
			jQ(this).find('img').animate({'opacity': 1});;
		},
		function () {
			if ( !jQ(this).hasClass('gallery-current-thumb') ) {
				jQ(this).find('img').animate({'opacity': 0.3});
			}
		}
	);
		
	jQ('.gallery-slide a').click(function() {
		if ( !jQ(this).hasClass('gallery-current-thumb') ) {
			jQ('.gallery-current-thumb').find('img').animate({'opacity': 0.3});
		}
		jQ('.gallery-ap .gallery-slider a').removeClass('gallery-current-thumb');
		jQ(this).addClass('gallery-current-thumb');
		var g = jQ(this).parents('.gallery-ap').find('.gallery-big-image');
		var light_box_all = '';
		if ( jQ(this).parents('.gallery-ap').find('.gallery-ap-light-box').length > 0 ) {
			light_box_all = jQ(this).parents('.gallery-ap').find('.gallery-ap-light-box').data('light-box-all');
		}
		var h = g.height();
		g.css('height', h + 'px');
		g.html('');
		var s = jQ(this).attr('href');
		var full_size_src = jQ(this).data('full-size-src');
		jQ('<img class="img-gallery-ap-full-size" />').attr('src', s).load(function() {
			jQ(this).hide();
			if ( full_size_src != '' ) {
				g.html('<a href="' + full_size_src + '" class="gallery-ap-light-box" data-light-box-all="' + light_box_all + '"></a>');
				g.find('a').html(jQ(this));
			} else {
				g.html(jQ(this));
			}
			jQ(this).fadeIn();
			h = jQ(this).height();
			g.animate({'height': h + 'px'});
		});
		return false;
	});
	
	jQ('.gallery-ap').on('click','.gallery-ap-light-box', function() {
		if ( jQ(this).data('light-box-all') == 'yes' ) {
			var p = [jQ(this).attr('href')];
			jQ('.gallery-slider a').each(function() {
				var n = jQ(this).data('full-size-src');
				if ( jQ.inArray(n,p) < 0 ) {
					p.push(n);
				}
			});
			jQ.prettyPhoto.open(p);
		} else {
			jQ.prettyPhoto.open(jQ(this).attr('href'));
		}
		return false;
	});
	
	function slide_amount(gs) {
		var w = gs.parents('.gallery-ap').find('.gallery-big-image').width();
		var thumbs_width = gs.find('img').width();
		var thumbs_margin = parseInt(gs.find('img').css('margin-right').replace('px',''));
		var visible_thumbs_number = parseInt((w + thumbs_margin)/(thumbs_width + thumbs_margin));
		return visible_thumbs_number * (thumbs_margin + thumbs_width);
	}
	
	function go_to_left(gs) {
		gs.data('processing', 'yes');
		gs.find('.gallery-slide').animate({'left': '+=' + slide_amount(gs) + 'px'}, function() {
			set_buttons();
			gs.data('processing', 'no');
		});	
	}
	
	function go_to_right(gs) {
		gs.data('processing', 'yes');
		gs.find('.gallery-slide').animate({'left': '-=' + slide_amount(gs) + 'px'}, function() {
			set_buttons();
			gs.data('processing', 'no');
		});
	}
	
	jQ('.gallery-ap .slider-content-button-left').click(function() {
		var gs = jQ(this).parents('.gallery-ap').find('.gallery-slider');
		if ( gs.data('processing') == 'no' ) {
			go_to_left(gs);
		}
		return false;
	});
	
	jQ('.gallery-ap .slider-content-button-right').click(function() {
		var gs = jQ(this).parents('.gallery-ap').find('.gallery-slider');
		if ( gs.data('processing') == 'no' ) {
			go_to_right(gs);
		}
		return false;
	});
	
	function set_buttons() {
		jQ('.gallery-ap').each(function() {
			if ( jQ(this).find('.gallery-slide').position().left == 0 ) {
				jQ(this).find('.slider-content-button-left-disabled').css('display','block');
				jQ(this).find('.slider-content-button-left').css('display','none');
				
			} else {
				jQ(this).find('.slider-content-button-left-disabled').css('display','none');
				jQ(this).find('.slider-content-button-left').css('display','block');
			}
			var l = jQ(this).find('.gallery-slide').position().left;
			var wi = jQ(this).find('.gallery-big-image').width();
			var thumbs_number = jQ(this).find('.gallery-slide img').length;
			var thumbs_width = jQ(this).find('.gallery-slide img').width();
			var thumbs_margin = parseInt(jQ(this).find('.gallery-slide img').css('margin-right').replace('px',''));
			var wt = thumbs_number * (thumbs_width + thumbs_margin) - thumbs_margin;
			if ( wt + l < wi + 1 ) {
				jQ(this).find('.slider-content-button-right-disabled').css('display','block');
				jQ(this).find('.slider-content-button-right').css('display','none');
			} else {
				jQ(this).find('.slider-content-button-right-disabled').css('display','none');
				jQ(this).find('.slider-content-button-right').css('display','block');
			}
		});
	}
	
	set_buttons();
	
	function debouncer( func , timeout ) {
		var timeoutID , timeout = timeout || 200;
		return function () {
			var scope = this , args = arguments;
			clearTimeout( timeoutID );
			timeoutID = setTimeout( function () {
				func.apply( scope , Array.prototype.slice.call( args ) );
			} , timeout );
		}
	}
	
	function after_resize() {
		jQ('.gallery-ap').each(function() {
			var gallery_ap = jQ(this);
			var w = gallery_ap.find('.gallery-big-image').width();
			var h = gallery_ap.find('.gallery-big-image img').height();
			if ( h != 0 ) {
				gallery_ap.find('.gallery-big-image').animate({'height': h + 'px'});
			}
			var nb_thumbs = gallery_ap.find('.gallery-slider img').length;
			var thumbs_width = gallery_ap.find('.gallery-slider img').width();
			var thumbs_margin = parseInt(gallery_ap.find('.gallery-slider img').css('margin-right').replace('px',''));
			var w2 = nb_thumbs * (thumbs_width + thumbs_margin) - thumbs_margin;
			if ( w2 > w ) {
				gallery_ap.find('.gallery-controls').slideDown();
			} else {
				gallery_ap.find('.gallery-controls').slideUp();
			}
		});
		jQ('.gallery-ap .gallery-slide').animate({'left': 0}, function() {
			jQ(this).css({'left': 0});
			set_buttons();
		});
	}
	
	after_resize();
	
	jQ(window).resize(debouncer(function(e) {
		after_resize();
	}));
}

/* Copyright AurelienD http://themeforest.net/user/AurelienD?ref=AurelienD */