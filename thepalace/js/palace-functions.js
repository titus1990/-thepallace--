/* Copyright AurelienD http://themeforest.net/user/AurelienD?ref=AurelienD */

jQuery(document).ready(function(jQ) {		
	
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
	
	if (!(jQ.browser.msie  && parseInt(jQ.browser.version, 10) === 8)) {		
		jQ(window).resize(function(){			
			if ( jQ('body').hasClass('has-layout-boxed') ) {				
				if( jQ(window).width() < 1024 ){				
					jQ('body').removeClass('layout-boxed');				
					jQ('body').addClass('layout-unboxed');			
				} else {
					jQ('body').addClass('layout-boxed');				
					jQ('body').removeClass('layout-unboxed');			
				}			
			}			
			if (jQ('ul.nav-mobile').hasClass('nav-main-expanded')) {				
				if( jQ(window).width() > 959 ){			
					jQ('#logo').css({'border-bottom-left-radius': '7px', 'border-bottom-right-radius': '7px'});		
				} else {			
					jQ('#logo').css({'border-radius': 0});			
				}			
			}		
		}).resize();	
	} else {		
		if ( jQ('body').hasClass('has-layout-boxed') ) {			
			jQ('body').addClass('layout-boxed');		
		} else {			
			jQ('body').addClass('layout-unboxed');	
		}		
		jQ(window).resize(function(){			
			if( jQ(window).width() < 1000 ){		
				jQ('header').addClass('ie8-header-small');		
			} else {			
				jQ('header').removeClass('ie8-header-small');		
			}		
		}).resize();	
	}		

	function nav_init() {		
		if (!(jQ.browser.msie  && parseInt(jQ.browser.version, 10) === 8 )) {
					
			var nav = jQ('nav').html();			
			nav = nav.replace('class="nav-standard"', 'class="nav-mobile"');			
			nav = nav.replace('id="', 'id="mobile-');		
			jQ('nav').append(nav);					
			jQ('nav>ul.nav-standard>li').each(function() {		
				if (jQ(this).children('ul').length > 0) { 				
					jQ(this).find('>a').append('&nbsp;&nbsp;<span class="nav-arrow-down">&#9660;</span>');		
				}		
			});		
			jQ('nav ul.nav-standard li ul li ul').parent().find('>a').append('<span class="nav-arrow-right">&#9658;</span>');					
			jQ('nav>ul.nav-mobile li').each(function() {		
				if (jQ(this).children('ul').length > 0) { 		
					jQ(this).append('<a class="nav-button-sub" href="#"><span class="nav-plus-small">+</span></a>');		
				}			
			});			

			function get_top_hidden_nav_mobile() {			
				return -400 - jQ('ul.nav-mobile').height();		
			}					

			jQ('ul.nav-mobile').css('top', get_top_hidden_nav_mobile());				
			jQ('#nav-button-main').click(function() {		
				var main_ul = jQ('ul.nav-mobile');			
				if (main_ul.hasClass('nav-main-expanded')) {			
					main_ul.removeClass('nav-main-expanded');			
					main_ul.animate({top: get_top_hidden_nav_mobile()});			
					jQ('#logo').css({'border-bottom-left-radius': '7px', 'border-bottom-right-radius': '7px'});			
					jQ('#nav-button-main .nav-arrow').rotate({animateTo:0});		
				} else {				
					main_ul.addClass('nav-main-expanded');				
					main_ul.animate({top: '-10px'}, function() {				
						jQ('#logo').css({'border-radius': 0});				
					});			
					jQ('#nav-button-main .nav-arrow').rotate({animateTo:180});		
				}		
			});				
			jQ('.nav-button-sub').click(function() {				
				var son_ul = jQ(this).parent().find('>ul');		
				if (son_ul.hasClass('nav-sub-expanded')) {			
					son_ul.removeClass('nav-sub-expanded');		
					son_ul.slideUp(function(){				
						jQ('nav ul.nav-mobile>li:last-child>a').css({'border-bottom-left-radius': '7px', 'border-bottom-right-radius': '7px'});		
					});			
					jQ(this).find('.nav-plus-small').rotate({animateTo:0, callback:function() {			
						jQ(this).html('+').removeClass('nav-minus-small');	
					}});		
				} else {			
					son_ul.addClass('nav-sub-expanded');			
					son_ul.slideDown();			
					if (jQ('nav ul.nav-mobile>li:last-child>ul').hasClass('nav-sub-expanded')) {			
						jQ('nav ul.nav-mobile>li:last-child>a').css({'border-radius': 0});			
					}				
					jQ(this).find('.nav-plus-small').rotate({animateTo:180, callback:function() {			
						jQ(this).html('&#8722;').addClass('nav-minus-small');		
					}});			
				}			
				return false;		
			});			
		}			
		jQ('nav ul.nav-standard').superfish({		
			delay: 600,			
			animation : {height:'show'}, 
			animationClose : {height:'hide'},		
			autoArrows:  false	
		});	
	}	
	nav_init();	
	
	if (jQ('#ap_fws_options').length > 0) {
		var fwso = JSON.parse(jQ('#ap_fws_options').val());
		if ( fwso['autoplay'] == 'yes' ) {
			ma = true;
		} else {
			ma = false;
		}
		full_width_slider(ma, fwso['pause_time'], fwso['animation_speed']);
	}

	if (jQ('#full-screen-slider').length > 0) {
		full_screen_slider('#full-screen-slider');
	}
	
	jQ('.sc-fws-container').each(function() {
		full_width_slider_inside(jQ(this));
	});
	
	jQ('.slider-type-a').each(function() {
		slider_type_a(jQ(this));
	});
	
	jQ('.slider-type-b').each(function() {
		slider_type_b(jQ(this));
	});
	
	if ( jQ('.gallery-ap').length > 0 ) {
		gallery_ap();
	}
	
	jQ('.ap-calendars-wrapper').each(function() {
		var cid = jQ(this).attr('id');
		var co = JSON.parse(jQ('#' + cid).find('input.ah-calendar-options').val());
		var adminDateFormat = co['dateFormat'];
		var ahcd = new Array();
		var sa = false;
		var lang = '';
		if ( jQ(this).find('.ah-calendars-data').length > 0 ) {
			ahcd = JSON.parse(jQ('#' + cid).find('.ah-calendars-data').val());
			sa = true;
		}
		if ( jQ(this).find('.ah-calendar-lang').length > 0 ) {
			lang = jQ('#' + cid).find('.ah-calendar-lang').val();
		} 
		if ( lang != '' ) {
			 co['dateFormat'] = jQ.datepick.regional[lang].dateFormat;
		}
		var iid = cid.substring('calendar-'.length);
		if ( jQ(this).hasClass('calendar-inline-no-input') ) {
			jQ('#' + iid).css('display', 'none');
		}
		if ( jQ(this).hasClass('calendar-pop-up') ) {
			jQ('#' + iid).after('<a class="calendar-button" href="#"></a>');
			jQ(this).css('display','none');
		}
		co['onSelect'] = function(dates) { 
			if (co['multiSelect'] == 0) {
				if ( dates.length ) {
					jQ('#' + iid).val(jQ.datepick.formatDate(co['dateFormat'],dates[0]));
					if (jQ(this).parent().hasClass('calendar-pop-up')) {
						jQ(this).parent().slideToggle();
					}
				}
			} else {
				if ( dates.length ) {
					var d = '';
					for(var i=0; i<dates.length; i++) {
						if ( i != 0 ) {
							d = d + ', ';
						}
						d = d + jQ.datepick.formatDate(co['dateFormat'],dates[i]);
					}
					jQ('#' + iid).val(d);
				} else {
					jQ('#' + iid).val(d);
				}
			}
		};
		jQ('#' + cid + ' .ap-calendar').each(function() {
			if ( sa ) {
				co['onDate'] = function(date) {
					var id = jQ(this).attr('id');
					var n = id.indexOf('-name-');
					id = id.substring(0, n);
					if ( jQ.inArray(jQ.datepick.formatDate(adminDateFormat,date), ahcd[id] ) > -1 ) {
						return { selectable: false };
					} else {
						return {};
					}
				};
			}
			if ( co['multiSelect'] == 0 ) {
				jQ(this).parent().find('.calendar-button-ok').css('display','none');
			}
			jQ(this).datepick();
			if ( lang != '' ) {
				jQ(this).datepick('option', jQ.datepick.regional[lang]);
			}
			jQ(this).datepick('option', co);
		});
		if ( jQ('#' + cid + ' .ah-calendar-type-to-show').length > 0 ) {
			jQ('#' + cid + ' .calendar-for-' + jQ('#' + cid + ' .ah-calendar-type-to-show').val()).css('display','block');
		} else {
			jQ('#' + cid + ' .ap-calendar').eq(0).css('display','block');
		}
	});
	
	jQ('.calendar-button').click(function() {
		var cid = jQ(this).parent().find('input').attr('id');
		jQ('#calendar-' + cid).slideToggle();
		return false;
	});
	
	jQ('.calendar-button-ok').click(function() {
		jQ(this).parent().parent().slideToggle();
		return false;
	});
	
	if (jQ('.ah-room-type-slugs-data').length > 0) {
		var rts = jQ('.ah-room-type-slugs-data').val().split(',');
		var rtn = jQ('.ah-room-type-names-data').val().split(',');
		var select_rt_content = '';
		for (var i=0; i<rts.length; i++) {
			select_rt_content += '<option value="' + rts[i] + '">' + rtn[i] + '</option>';
		}
		jQ('.room-type').html(select_rt_content);
	}
	jQ('.room-type').change(function() {
		if (jQ('.ap-calendars-wrapper').eq(0).find('.ap-calendar').length > 1) {
			jQ('.ap-calendar').css('display','none');
			jQ('.calendar-for-' + jQ(this).val()).fadeIn(1000);
		}
	});
	
	function init_google_map(map_coords) {
		var lat_lng = map_coords.split(',');
		var point = new google.maps.LatLng( lat_lng[0] , lat_lng[1] );
		var mapOptions = {
			zoom: 15,
			center: point,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
		var noPoi = [{
			featureType: "poi.business",
			stylers: [{ visibility: "off" }]  
		}];
		map.setOptions({styles: noPoi});
		var marker = new google.maps.Marker({
			position: point,
			map: map
		});
		jQ(window).unload(function() { 
			GUnload(); 
		});
	}

	if ( jQ('#map-lat-lng').length > 0 ) {
		init_google_map(jQ('#map-lat-lng').val());
	}
	
	if ( jQ('#background').length > 0 ) {
		function do_resize() {
			var img = jQ('#background img');
				ratio_img = img.width() / img.height(),
				ratio_win = jQ(window).width() / jQ(window).height();
			if ( ratio_win < ratio_img ) {
				img.css({'height': '100%', 'width': 'auto'});
				img.css({'left': '50%', 'margin-left': -Math.floor(img.width()/2) + 'px', 'margin-top': 0,  'top': 0});		
			} else {
				img.css({'height': 'auto', 'width': '100%'});
				img.css({'left': 0, 'margin-left': 0, 'margin-top': -Math.floor(img.height()/2) + 'px', 'top': '50%'});
			}			
		}
		jQ(window).resize(debouncer(function(e) {
			do_resize();
		}));
		jQ('<img />').attr('src', jQuery('#background img').attr('src')).load(function () {
			do_resize();
		});
	}
	
	if ( jQ('#footer-image').length == 0 ) {
		jQ('footer.below-main-container, #footer-image-container').css('background', 'none');
	}
});

/* Copyright AurelienD http://themeforest.net/user/AurelienD?ref=AurelienD */