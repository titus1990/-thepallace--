/* Copyright AurelienD http://themeforest.net/user/AurelienD?ref=AurelienD */

/* 
1 - global 
	a) menu
	b) ajax save
	c) fade out notification message
	d) resizable panel
	e) color picker
	f) image upload
	g) background texture
2 - general page
3 - slider page
4 - widgets page
5 - posts rules
*/

jQuery(document).ready(function(jQ) {
  
	/* begin 1) global */
	
	/* begin a) menu */
	jQ('.aurel-panel-section').css('display', 'none');
	var v = '';
	var apm = 0;
	if (v = jQ.cookie('ap_cookie')) {
		var ap_cook = v.split(';');
		apm = ap_cook[4];
	}
	jQ('.aurel-panel-section').eq(apm).css('display', 'block');
	jQ('#aurel-panel-menu li').eq(apm).find('a').addClass('apm-active');
	
	jQ('#aurel-panel-menu a').click(function() {
		jQ('#aurel-panel-menu a').removeClass('apm-active');
		jQ(this).addClass('apm-active');
		jQ('.aurel-panel-section').css('display', 'none');
		jQ(jQ(this).attr('href')).css('display', 'block');
		jQ('#aurel-panel-content').prop({ scrollTop: 0 });
		var v = jQ('#aurel-panel-wrap').width() + ';' + jQ('#aurel-panel-content').width() + ';' + 
				jQ('#aurel-panel-wrap').height() + ';' + jQ('#aurel-panel-content').height() + ';' +
				jQ('.apm-active').parent().index();
		jQ.cookie('ap_cookie', v, { expires: 7 });
		return false;
	});
	/* end a) menu */
	
	/* begin b) ajax save */
	function befSub() {
		jQ('#aurel-panel-submit-result').html('');
		jQ('#aurel-panel-submit-result, #aurel-panel-submit-ajax-loader').css({display: 'block'});
	}
	
	function foapsr() {
		jQ('#aurel-panel-submit-result').fadeOut(1000);
	}
	
	function aftSub() {
		setTimeout(foapsr, 1500);
		jQ('#aurel-panel-submit-ajax-loader').css({display: 'none'});
	}
	
	jQ('#aurel-panel-button-save-options').click(function() {
		var apsm = new Array();
		jQ('.aurel-panel-slider').each(function() {
			var slider = {};
			slider['type'] = jQ(this).find('.aurel-panel-slider-type').html();
			slider['name'] = jQ(this).find('.aurel-panel-slider-name').html();
			var apo = new Array();
			jQ(this).find('.aurel-panel-slider-option').each(function() {
				apo.push(jQ(this).val());
			});
			slider['options'] = apo;
			var aps = new Array();
			jQ(this).find('.aurel-panel-slide').each(function() {
				var s = {};
				s['image_url'] = jQ(this).find('.aurel-panel-input-slide-image-url').val();
				s['caption'] = jQ(this).find('.aurel-panel-textarea-slide-caption').val();
				aps.push(s);
			});
			slider['slides'] = aps;
			apsm.push(slider);
		});
		jQ('#ap_global_slider_manager').val(JSON.stringify(apsm));
		var aps = new Array();
		jQ('#aurel-panel-slides .aurel-panel-slide').each(function() {
			var s = {};
			s['image_url'] = jQ(this).find('.aurel-panel-input-slide-image-url').val();
			s['caption'] = jQ(this).find('.aurel-panel-textarea-slide-caption').val();
			aps.push(s);
		});
		var apfss = new Array();
		jQ('#aurel-panel-fsslides .aurel-panel-slide').each(function() {
			var s = {};
			s['image_url'] = jQ(this).find('.aurel-panel-input-slide-image-url').val();
			s['caption'] = jQ(this).find('.aurel-panel-textarea-slide-caption').val();
			apfss.push(s);
		});
		jQ('#ap_slider_manager').val(JSON.stringify(aps));
		jQ('#ap_fsslider_manager').val(JSON.stringify(apfss));
		jQ('#ap_widget_areas_manager').val(JSON.stringify(apwa));
		var aplopr = new Array();
		aplopr = populate_array_with('#aurel-panel-lists-of-posts-rules');
		var apcr = new Array();
		apcr = populate_array_with('#aurel-panel-categories-rules');
		
		function populate_array_with(id) {
			a = new Array();
			jQ(id + ' .aurel-panel-posts-rule').each(function() {
				var r = {};
				var n = jQ(this).find('.ap-posts-rule-n').val();
				if ( id == '#aurel-panel-categories-rules' ) {
					r['category'] = jQ(this).find('.ap-posts-rule-category').val();
					if ( jQ(this).find('.aurel-panel-rule-toggle').hasClass('visible') ) {
						r['visible'] = 'yes';
					} else {
						r['visible'] = 'no';
					}
				}
				r['layout'] = jQ(this).find('.ap-posts-rule-layout').val();
				r['sidebar'] = jQ(this).find('.ap-sidebar-selector').val();
				r['display-title'] = jQ('input:radio[name=ap-posts-rule-' + n + '-display-title]:checked').val();
				var m = new Array();
				jQ('input[name="ap-posts-rule-' + n + '-meta[]"]:checked').each(function() {
				  m.push(jQ(this).val());
				});
				r['meta'] = m;
				r['show-thumbnail'] = jQ('input:radio[name=ap-posts-rule-' + n + '-show-thumbnail]:checked').val();
				r['thumbnail-links'] = jQ('input:radio[name=ap-posts-rule-' + n + '-thumbnail-links]:checked').val();
				r['content'] = jQ('input:radio[name=ap-posts-rule-' + n + '-content]:checked').val();
				r['learn-more'] = jQ('input:radio[name=ap-posts-rule-' + n + '-learn-more]:checked').val();
				r['header-image'] = jQ('#ap-posts-rule-' + n + '-header-image').val();
				r['slider'] = jQ('select[name=ap-posts-rule-' + n + '-slider]').val();
				r['background-image'] = jQ('#ap-posts-rule-' + n + '-background-image').val();
				r['background-stretch-or-tile'] = jQ('input:radio[name=ap-posts-rule-' + n + '-background-stretch-or-tile]:checked').val();
				r['background-fixed-or-scrollable'] = jQ('input:radio[name=ap-posts-rule-' + n + '-background-fixed-or-scrollable]:checked').val();
				r['background-texture'] = jQ('select[name=ap-posts-rule-' + n + '-background-texture]').val();
				r['footer-image'] = jQ('#ap-posts-rule-' + n + '-footer-image').val();
				r['disable-comments'] = jQ('input:radio[name=ap-posts-rule-' + n + '-disable-comments]:checked').val();
				r['custom-css'] = jQ('textarea[name=ap-posts-rule-' + n + '-custom-css]').val();
				a.push(r);
			});
			return a;
		}
		
		var apspr = new Array();
		jQ('#aurel-panel-single-posts-rules .aurel-panel-posts-rule').each(function() {
			var r = {};
			var n = jQ(this).find('.ap-posts-rule-n').val();
			r['layout'] = jQ(this).find('.ap-posts-rule-layout').val();
			r['sidebar'] = jQ(this).find('.ap-posts-rule-sidebar select').val();
			var m = new Array();
			jQ('input[name="ap-posts-rule-' + n + '-meta[]"]:checked').each(function() {
			  m.push(jQ(this).val());
			});
			r['meta'] = m;
			r['disable-comments'] = jQ('input:radio[name=ap-posts-rule-' + n + '-disable-comments]:checked').val();
			apspr.push(r);
		});
		
		jQ('#ap_lists_of_posts_manager').val(JSON.stringify(aplopr));
		jQ('#ap_single_posts_manager').val(JSON.stringify(apspr));
		jQ('#ap_categories_manager').val(JSON.stringify(apcr));
		
		jQ('#aurel-panel-form').ajaxSubmit({target: '#aurel-panel-submit-result', beforeSubmit: befSub, success: aftSub}); 
	});
	/* end b) ajax save */
	
	/* begin c) fade out notification message */
	function suapm() {
		jQ('.aurel-panel-message').slideUp(1000);
	}
	
	setTimeout(suapm, 3000);
	/* end c) fade out notification message */
	
	/* begin d) resizable panel */
	var v = '';
	if (v = jQ.cookie('ap_cookie')) {
		var ap_size = v.split(';');
		jQ('#aurel-panel-wrap').width(ap_size[0]);
		jQ('#aurel-panel-content').width(ap_size[1]);
		jQ('#aurel-panel-wrap').height(ap_size[2]);
		jQ('#aurel-panel-content').height(ap_size[3]);
	}
	
	jQ('#aurel-panel-wrap').resizable({
		alsoResize: jQ('#aurel-panel-content')
	}); 
	
	jQ('#aurel-panel-wrap').resize(function() {
		var v = jQ('#aurel-panel-wrap').width() + ';' + jQ('#aurel-panel-content').width() + ';' + jQ('#aurel-panel-wrap').height() + ';' + jQ('#aurel-panel-content').height();
		jQ.cookie('ap_cookie', v, { expires: 7 });
	});
	/* end d) resizable panel */
	
	/* begin e) color picker*/
	jQ('.aurel-panel-color-picker').miniColors();
	/* end e) color picker */
	
	/* begin f) image uplaod */
	function init_image_upload() {
		var input_upload = '';
		jQ('body').on('click', '.aurel-panel-upload-button', function() {
			input_upload = jQ(this).prev('input');
			tb_show('', 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true');
			return false;
		});
		window.send_to_editor = function(html) {
			imgurl = jQ('img',html).attr('src');
			input_upload.val(imgurl);
			if ( input_upload.attr('id') == 'ap_bg_image' ) {
				jQ('#ap_bg_texture').val('none');
			}
			tb_remove();
		}
	}
	init_image_upload();
	/* end f) image upload */
	
	/* begin g) background texture */
	var ap_template_directory_uri = jQ('#ap_template_directory_uri').val();
	jQ('#ap_bg_texture').change(function() {
		var t = jQ(this).val();
		if ( t != 'none' ) {
			jQ('#ap_bg_image').val(ap_template_directory_uri + '/img/textures/texture-' + t + '.png');
		} else {
			jQ('#ap_bg_image').val('');
		}
		jQ('#ap_bg_stretch_or_tile_tile').prop('checked', true);
	});
	jQ('#ap_bg_image').change(function() {
		jQ('#ap_bg_texture').val('none');
	});
	
	jQ('.ap-posts-rule-background-texture').change(function() {
		var t = jQ(this).val();
		var i = jQ(this).parent('p').prev('p').find('input[type=text]');
		var r = jQ(this).parent('p').next('p').find('input[type=radio]').eq(1);
		if ( t != 'none' ) {
			i.val(ap_template_directory_uri + '/img/textures/texture-' + t + '.png');
			r.prop('checked', true);
		} else {
			i.val('');
		}
	});
	/* end g) background texture */
	
	/* end 1) global */
	
	/* begin 2) general page */
	
	/* begin a) reset */
	jQ('#reset-ap-options').click(function() {
		if (confirm("Reset all options?")) {
			jQ('#aurel-panel-action').val('reset');
			jQ('#aurel-panel-form').submit();
		}
	});
	/* end a) reset */
	
	/* begin b) load */
	jQ('#load-demo-ap-options').click(function() {
		if (confirm("Installing the demo options will reset the current options (e.g. sliders, widget areas... will be deleted). Do you want to load the demo options?")) {
			jQ('#aurel-panel-action').val('load_options');
			jQ('#aurel-panel-form').submit();
		}
	});
	jQ('#load-demo-widgets-ap-options').click(function() {
		if (confirm("Installing the demo widgets will reset all your current sidebars. Do you want to load the widgets?")) {
			jQ('#aurel-panel-action').val('load_widgets');
			jQ('#aurel-panel-form').submit();
		}
	});
	/* end b) load */
	
	/* c) hotel admin */
	jQ('input[name="ap_activate_hotel"]').change(function() {
		if (jQ(this).val() == 'yes') {
			jQ('#aurel-panel-action').val('activate-hotel');
		} else {
	 		jQ('#aurel-panel-action').val('deactivate-hotel'); 	
		}
		jQ('#aurel-panel-form').submit();
	});
	/* end c) hotel admin */
	
	/* end 2) general page */
	
	/* begin 3) slider */
	function update_slider_list() {
		var select_slider_markup = '';
		var slider = {};
		if ( jQ('.aurel-panel-slider').length > 0 ) {
			select_slider_markup = '<option value="no-slider">Do not display a slider</option>';
			jQ('.aurel-panel-slider').each(function() {
				select_slider_markup += '<option value="' + jQ(this).find('.aurel-panel-slider-name').html() + '">' + jQ(this).find('.aurel-panel-slider-name').html() + '</option>';
			});
		} else {
			select_slider_markup = '<option value="no-slider">No slider created yet</option>';
		}
		jQ('.ap-slider-selector').each(function() {
			var select_val = jQ(this).val();
			jQ(this).html(select_slider_markup);
			jQ(this).val(select_val);
		});
	}
	
	jQ('#aurel-panel-add-slider').click(function() {
		if ( /^\w+$/.test( jQ('#aurel-panel-slider-name').val() ) ) {
			var slider_options_name = [];
			var slider_options_value = [];
			slider_options_name = ['Animation speed in ms (how long is the transition)', 'Pause time in ms (how long each slide is displayed)', 'Autoplay (yes/no)'];
			slider_options_value = ['1000', '6000', 'yes'];
			if ( jQ('#aurel-panel-slider-type').val() == 'Full-width' ) {
				slider_options_name.push('Minimum height (in px)');
				slider_options_value.push('400');
			}
			if ( jQ('#aurel-panel-slider-type').val() == 'Top-content' ) {
				slider_options_name.push('Maximum height (in px)');
				slider_options_value.push('400');
			}
			var slider_options = '';
			for (var i=0; i<slider_options_name.length; i++) {
				slider_options += slider_options_name[i] + '<br/><input class="aurel-panel-slider-option" type="text" value="' + slider_options_value[i] + '"/><br/><br/>';
			}
			jQ('#aurel-panel-sliders').prepend('' +
			'<div class="aurel-panel-slider">' + 
				'<a href="#" class="aurel-panel-slider-remove"></a>' + 
				'<p>Slider name: <strong class="aurel-panel-slider-name">' + jQ('#aurel-panel-slider-name').val() + '</strong></p>' +
				'<p>Slider type: <strong class="aurel-panel-slider-type">' + jQ('#aurel-panel-slider-type').val() + '</strong></p>' + 
				'<p>Slider options:<br/>' +
				slider_options +
				'</p>' +
				'<input type="button" class="button aurel-panel-add-slide-before" value="Add a slide" />' +
				'<div class="aurel-panel-slides"></div>' +
				'<input type="button" class="button aurel-panel-add-slide-after" value="Add a slide" />' +
			'</div>');
			jQ('#aurel-panel-slider-name').val('');
			update_slider_list();
		} else {
			alert('The slider name can contain only letters, numbers, and underscores.');
		}
	});
	
	jQ('body').on('click', '.aurel-panel-slider-remove', function() {
		if ( confirm('Delete the slider?') ) {
			jQ(this).parent().slideUp('normal', function() { 
				jQ(this).remove(); 
				update_slider_list();
			});
		}
		return false;
	});
	
	jQ('body').on('click', '.aurel-panel-slide-remove', function() {
		if ( confirm('Delete the slide?') ) {
			jQ(this).parent().slideUp('normal', function() { jQ(this).remove(); } );
			jQ('.aurel-panel-slides').sortable('refresh');
		}
		return false;
	});
	
	var slider_markup = '<div style="display: none" class="aurel-panel-slide">' + 
						'<a class="aurel-panel-slide-remove" href="#"></a>' +
						'<p>Image:<br/><input type="text" class="aurel-panel-input-slide-image-url" value="" />' +
						'<input type="button" class="button aurel-panel-upload-button" value="Select image" /></p>' +
						'<p>Caption:<br/><textarea class="aurel-panel-textarea-slide-caption" cols="10" rows="10"></textarea></p>' +
						'</div>';
			
	jQ('body').on('click', '.aurel-panel-add-slide-before', function() {
		//var st = jQ(this).parent().find('.aurel-panel-slider-type').html();
		jQ(this).parent().find('.aurel-panel-slides').prepend(slider_markup);
		jQ(this).parent().find('.aurel-panel-slide:first').slideDown();
		init_slides_sortable();
	});
	jQ('body').on('click', '.aurel-panel-add-slide-after', function() {
		//var st = jQ(this).parent().find('.aurel-panel-slider-type').html();
		jQ(this).parent().find('.aurel-panel-slides').append(slider_markup);
		jQ(this).parent().find('.aurel-panel-slide:last').slideDown();	
		init_slides_sortable();
	});
	
	function init_slides_sortable() {
		jQ('.aurel-panel-slides').sortable({
			axis: 'y',
			items: '.aurel-panel-slide'
		});
	}
	/* end 3) slider */
	
	/* begin 4) widget areas */
	var apwa = JSON.parse(jQ('#ap_widget_areas_manager').val());
	init_remove_widget_area_click();
	
	function update_sidebars() {
		var s_options = '<option value="default_sidebar">Default sidebar</option>';
		for (var i = 0; i < apwa.length; i++) {
			s_options = s_options + '<option value="' + apwa[i] + '">' + apwa[i].charAt(0).toUpperCase() + apwa[i].slice(1) + '</option>';
		}
		jQ('.ap-sidebar-selector').each(function() {
			var s = jQ(this).find('option:selected').val();
			jQ(this).html(s_options);
			jQ(this).find('option[value=' + s +']').attr('selected', 'selected');
		});
	}
	
	function init_remove_widget_area_click() {
		jQ('#aurel-panel-widget-areas').on('click', '.aurel-panel-widget-area-remove', function() {
			if ( confirm('Delete the widget area?') ) {
				var wan = jQ(this).parent().html();
				var ioa = wan.indexOf('<a class="aurel-panel-widget-area-remove"');
				wan = wan.substring(0, ioa);
				apwa.splice(jQ.inArray(wan,apwa), 1);
				update_sidebars();
				jQ(this).parent().slideUp('normal', function() { jQ(this).remove(); } );
			}
			return false;
		});
	}
	
	jQ('#aurel-panel-add-widget-area').click(function() {
		var wan = jQ('#aurel-panel-widget-area-name').val();
		if (wan == '') {
			alert('Please enter a name.');
			return false;
		}
		if (jQ.inArray(wan,apwa) > -1) {
			alert('A widget area named "' + wan + '" already exits.');
			return false;
		}
		jQ('#aurel-panel-widget-areas').prepend('<p style="display: none" class="aurel-panel-widget-area">' + wan + '<a class="aurel-panel-widget-area-remove" href="#"></a></p>');
		jQ('#aurel-panel-widget-areas').find('.aurel-panel-widget-area:first').slideDown();
		apwa.unshift(wan);
		update_sidebars();
		return false;
	});
	/* end 4) widget areas */
	
	/* 5) posts rules */
	jQ('body').on('change','.ap-posts-rule-layout', function() {
		if ( (jQ(this).val() == 'full-width') || (jQ(this).val() == 'three-cols') ) {
			jQ(this).parents('.aurel-panel-posts-rule').find('.ap-posts-rule-sidebar').slideUp();
		} else {
			jQ(this).parents('.aurel-panel-posts-rule').find('.ap-posts-rule-sidebar').slideDown();
		}
	});

	jQ('.ap-posts-rule-layout').each(function() {
		if ( (jQ(this).val() == 'full-width') || (jQ(this).val() == 'three-cols') ) {
			jQ(this).parents('.aurel-panel-posts-rule').find('.ap-posts-rule-sidebar').css('display', 'none');
		} else {
			jQ(this).parents('.aurel-panel-posts-rule').find('.ap-posts-rule-sidebar').css('display', 'block');
		}
	});
	
	jQ('body').on('change','.ap-posts-rule-show-thumbnail', function() {
		if ( jQ(this).parent().find('input:radio:checked').val() == 'yes' ) {
			jQ(this).parent().parent().find('.ap-posts-rule-thumbnail-links').slideDown();
		} else {
			jQ(this).parent().parent().find('.ap-posts-rule-thumbnail-links').slideUp();
		}
	});

	jQ('.ap-posts-rule-show-thumbnail').each(function() {
		if ( jQ(this).parent().find('input:radio:checked').val() == 'yes' ) {
			jQ(this).parent().parent().find('.ap-posts-rule-thumbnail-links').css('display', 'block');
		} else {
			jQ(this).parent().parent().find('.ap-posts-rule-thumbnail-links').css('display', 'none');
		}
	});
	
	jQ('body').on('click', '.aurel-panel-rule-remove', function() {
		if ( confirm('Delete the rule?') ) {
			jQ(this).parent().slideUp('normal', function() { jQ(this).remove(); } );
			jQ('#aurel-panel-categories-rules').sortable('refresh');
		}
		return false;
	});
	
	jQ('.aurel-panel-posts-rule-category').each(function() {
		if ( !jQ(this).find('.aurel-panel-rule-toggle').hasClass('visible') ) {
			jQ(this).find('.aurel-panel-rule-odd, .aurel-panel-rule-even').css('display', 'none');
		}
	});
	jQ('body').on('click', '.aurel-panel-rule-toggle', function() {
		if ( jQ(this).hasClass('visible') ) {
			jQ(this).removeClass('visible');
			jQ(this).parent().find('.aurel-panel-rule-odd, .aurel-panel-rule-even').slideUp();
		} else {
			jQ(this).parent().find('.aurel-panel-rule-odd, .aurel-panel-rule-even').slideDown();
			jQ(this).addClass('visible');
		}
		return false;
	});
	
	var cat_options = '';
	var c = JSON.parse(jQ('#ap_categories').val());
	for (var i = 0; i < c.length; i++) {
		cat_options = cat_options + '<option value="' + c[i].slug + '">' + c[i].name + '</option>';
	}
	
	var n_rule = 1000;
	
	function posts_rule(n) {	
		var s_options = '<option value="default_sidebar">Default sidebar</option>';
		for (var i = 0; i < apwa.length; i++) {
			s_options = s_options + '<option value="' + apwa[i] + '">' + apwa[i] + '</option>';
		}
		var pr = 
		'<div style="display: none" class="aurel-panel-posts-rule rule-sortable">' +
			'<input type="hidden" class="ap-posts-rule-n" value="' + n + '" />' +
			'<a class="aurel-panel-rule-remove" href="#"></a>' +
			'<a class="aurel-panel-rule-toggle visible" href="#"></a>' +
			'<div class="aurel-panel-rule-odd-cat">' +
				'Select a category: ' +
				'<p>' +
					'<select class="ap-posts-rule-category">' +
					cat_options +
					'</select>' +
				'</p>' +
			'</div>' +
			'<div class="aurel-panel-rule-even">' +
				'<p>' +
					'Layout you want to use: ' +
					'<p>' +
						'<select class="ap-posts-rule-layout">' +
							'<option value="full-width">Full Width</option>' +
							'<option value="one-col-right-sidebar">One column and right sidebar</option>' +
							'<option value="one-col-left-sidebar">One column and left sidebar</option>' +
							'<option value="three-cols">Three columns</option>' +
						'</select>' +
					'</p>' +
				'</p>' +
				'<div style="display: none" class="ap-posts-rule-sidebar">' +
					'<p>' +
						'Sidebar name: ' +
					'</p>' +
					'<p>' +
						'<select class="ap-sidebar-selector">' +
						s_options +
						'</select>' +
					'</p>' +
				'</div>' +
			'</div>' +
			'<div class="aurel-panel-rule-odd">' +
				'<p>' +
					'Display the title:' +
				'</p>' +
				'<p>' +
					'<input id="ap-posts-rule-' + n + '-display-title-yes" type="radio" name="ap-posts-rule-' + n + '-display-title" value="yes" checked /> <label for="ap-posts-rule-' + n + '-display-title-yes">Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;' +
					'<input id="ap-posts-rule-' + n + '-display-title-no" type="radio" name="ap-posts-rule-' + n + '-display-title" value="no" /> <label for="ap-posts-rule-' + n + '-display-title-no">No</label>' +
				'</p>' +
			'</div>' +
			'<div class="aurel-panel-rule-even">' +
				'<p>' +
					'Meta informations you want to display:' +
				'</p>' +
				'<p class="ap-float-left ap-width-20p">' +
					'<input type="checkbox" id="ap-posts-rule-' + n + '-meta-date" name="ap-posts-rule-' + n + '-meta[]" value="date" checked  /> ' +
					'<label for="ap-posts-rule-' + n + '-meta-date">Date</label>' +
				'</p>' + 
				'<p class="ap-float-left ap-width-20p">' +
					'<input type="checkbox" id="ap-posts-rule-' + n + '-meta-author" name="ap-posts-rule-' + n + '-meta[]" value="author" /> ' +
					'<label for="ap-posts-rule-' + n + '-meta-author">Author</label>' +
				'</p>' +
				'<p class="ap-float-left ap-width-20p">' +
					'<input type="checkbox" id="ap-posts-rule-' + n + '-meta-categories" name="ap-posts-rule-' + n + '-meta[]" value="categories" checked  /> ' +
					'<label for="ap-posts-rule-' + n + '-meta-categories">Categories</label>' +
				'</p>' +

				'<p class="ap-float-left ap-width-20p">' +
					'<input type="checkbox" id="ap-posts-rule-' + n + '-meta-tags" name="ap-posts-rule-' + n + '-meta[]" value="tags" checked  /> ' +
					'<label for="ap-posts-rule-' + n + '-meta-tags">Tags</label>' +
				'</p>' +
				'<p class="ap-float-left ap-width-20p">' +
					'<input type="checkbox" id="ap-posts-rule-' + n + '-meta-comments" name="ap-posts-rule-' + n + '-meta[]" value="comments" checked  /> ' +
					'<label for="ap-posts-rule-' + n + '-meta-comments">Comments</label>' +
				'</p>' +
				'<div class="clear"></div>' +
			'</div>' +
			'<div class="aurel-panel-rule-odd">' +
				'<p>' +
					'Show a thumbnail:' +
				'</p>' +
				'<p>' +
					'<input id="ap-posts-rule-' + n + '-show-thumbnail-yes" type="radio" name="ap-posts-rule-' + n + '-show-thumbnail" class="ap-posts-rule-show-thumbnail" value="yes" checked /> <label for="ap-posts-rule-' + n + '-show-thumbnail-yes">Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;' +
					'<input id="ap-posts-rule-' + n + '-show-thumbnail-no" type="radio" name="ap-posts-rule-' + n + '-show-thumbnail" class="ap-posts-rule-show-thumbnail" value="no" /> <label for="ap-posts-rule-' + n + '-show-thumbnail-no">No</label>' +
				'</p>' +
				'<div class="ap-posts-rule-thumbnail-links">' +
					'<p>' +
						'Thumbnail links to:' +
					'</p>' +
					'<p>' +
						'<input id="ap-posts-rule-' + n + '-thumbnail-links-post" type="radio" name="ap-posts-rule-' + n + '-thumbnail-links" value="post" checked /> <label for="ap-posts-rule-' + n + '-thumbnail-links-post">Post</label>&nbsp;&nbsp;&nbsp;&nbsp;' +
						'<input id="ap-posts-rule-' + n + '-thumbnail-links-image" type="radio" name="ap-posts-rule-' + n + '-thumbnail-links" value="full-size image" /> <label for="ap-posts-rule-' + n + '-thumbnail-links-image">Full-size image</label>&nbsp;&nbsp;&nbsp;&nbsp;' +
					'</p>' +
				'</div>' +
			'</div>' +
			'<div class="aurel-panel-rule-even">' +
				'<p>' +
					'Content you want to display:' +
				'</p>' +
				'<p>' +
					'<input id="ap-posts-rule-' + n + '-content-content" type="radio" name="ap-posts-rule-' + n + '-content" value="content" /> <label for="ap-posts-rule-' + n + '-content-content">Post content</label>&nbsp;&nbsp;&nbsp;&nbsp;' +
					'<input id="ap-posts-rule-' + n + '-content-excerpt" type="radio" name="ap-posts-rule-' + n + '-content" value="excerpt" checked /> <label for="ap-posts-rule-' + n + '-content-excerpt">Post excerpt</label>&nbsp;&nbsp;&nbsp;&nbsp;' +
					'<input id="ap-posts-rule-' + n + '-content-none" type="radio" name="ap-posts-rule-' + n + '-content" value="none" /> <label for="ap-posts-rule-' + n + '-content-none">No content</label>' +
				'</p>' +
			'</div>' +
			'<div class="aurel-panel-rule-odd">' +
				'<p>' +
					'"Learn more" link:' +
				'</p>' +
				'<p>' +
					'<input id="ap-posts-rule-' + n + '-learn-more-none" type="radio" name="ap-posts-rule-' + n + '-learn-more" value="none" /> <label for="ap-posts-rule-' + n + '-learn-more-none">None</label>&nbsp;&nbsp;&nbsp;&nbsp;' +
					'<input id="ap-posts-rule-' + n + '-learn-more-link" type="radio" name="ap-posts-rule-' + n + '-learn-more" value="link" /> <label for="ap-posts-rule-' + n + '-learn-more-link">Link</label>&nbsp;&nbsp;&nbsp;&nbsp;' +
					'<input id="ap-posts-rule-' + n + '-learn-more-button" type="radio" name="ap-posts-rule-' + n + '-learn-more" value="button" checked /> <label for="ap-posts-rule-' + n + '-learn-more-button">Button</label>' +
				'</p>' +
			'</div>' +
			'<div class="aurel-panel-rule-even">' +
				'<p>' +
					'Header image:' +
				'</p>' +
				'<p>' +
					'<input type="text" id="ap-posts-rule-' + n + '-header-image" class="ap-width-75p" value="" />&nbsp;' +
					'<input type="button" class="button aurel-panel-upload-button" value="Select image" />' +
				'</p>' +
				'<p>' +
					'- or choose a slider to be displayed as a header:' +
				'</p>' +
				'<p>' +
					'<select id="ap-posts-rule-' + n + '-slider" name="ap-posts-rule-' + n + '-slider" class="ap-posts-rule-slider ap-slider-selector"></select>' +
				'</p>' +
			'</div>' +
			'<div class="aurel-panel-rule-odd">' +
				'<p>' +
					'Background image:' +
				'</p>' +
				'<p>' +
					'<input type="text" id="ap-posts-rule-' + n + '-background-image" class="ap-width-75p" value="" />&nbsp;' +
					'<input type="button" class="button aurel-panel-upload-button" value="Select image" />' +
				'</p>' +
				'<p>' +
					'<input id="ap-posts-rule-' + n + '-background-stretch" type="radio" name="ap-posts-rule-' + n + '-background-stretch-or-tile" value="stretch" checked /> <label for="ap-posts-rule-' + n + '-background-stretch">Stretch</label>&nbsp;&nbsp;&nbsp;&nbsp;' +
					'<input id="ap-posts-rule-' + n + '-background-tile" type="radio" name="ap-posts-rule-' + n + '-background-stretch-or-tile" value="tile" /> <label for="ap-posts-rule-' + n + '-background-tile">Tile</label>' +
				'</p>' +
				'<p>' +
					'<input id="ap-posts-rule-' + n + '-background-fixed" type="radio" name="ap-posts-rule-' + n + '-background-fixed-or-scrollable" value="fixed" checked /> <label for="ap-posts-rule-' + n + '-background-fixed">Fixed</label>&nbsp;&nbsp;&nbsp;&nbsp;' +
					'<input id="ap-posts-rule-' + n + '-background-scrollable" type="radio" name="ap-posts-rule-' + n + '-background-fixed-or-scrollable" value="scrollable" /> <label for="ap-posts-rule-' + n + '-background-scrollable">Scrollable</label>' +
				'</p>' +
			'</div>' +			
			'<div class="aurel-panel-rule-even">' +
				'<p>' +
					'Footer image:' +
				'</p>' +
				'<p>' +
					'<input type="text" id="ap-posts-rule-' + n + '-footer-image" class="ap-width-75p" value="" />&nbsp;' +
					'<input type="button" class="button aurel-panel-upload-button" value="Select image" />' +
				'</p>' +
			'</div>' +
			'<div class="aurel-panel-rule-odd">' +
				'<p>' +
					'Disable comments:' +
				'</p>' +
				'<p>' +
					'<input id="ap-posts-rule-' + n + '-disable-comments-yes" type="radio" name="ap-posts-rule-' + n + '-disable-comments" value="yes" /> <label for="ap-posts-rule-' + n + '-disable-comments-yes">Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;' +
					'<input id="ap-posts-rule-' + n + '-disable-comments-no" type="radio" name="ap-posts-rule-' + n + '-disable-comments" value="no" checked /> <label for="ap-posts-rule-' + n + '-disable-comments-no">No</label>' +
				'</p>' +
			'</div>' +
			'<div class="aurel-panel-rule-even">' +
				'<p>' +
					'Custom CSS:' +
				'</p>' +
				'<p>' +
					'<textarea id="ap-posts-rule-' + n + '-custom-css" name="ap-posts-rule-' + n + '-custom-css" /></textarea>';
				'</p>' +
			'</div>' +
		'</div>';
		return pr;	
	}
	
	jQ('#aurel-panel-add-categories-rule').click(function() {
		jQ('#aurel-panel-categories-rules').append( posts_rule(n_rule) );
		jQ('#aurel-panel-categories-rules').find('.aurel-panel-posts-rule:last').slideDown();
		update_slider_list();
		n_rule++;
		jQ('#aurel-panel-categories-rules').sortable('refresh');
	});
	
	jQ('#aurel-panel-categories-rules').sortable({
		axis: 'y',
		items: '.rule-sortable' 
	});
	/* end 5) posts rules */
	
});

/* Copyright AurelienD http://themeforest.net/user/AurelienD?ref=AurelienD */