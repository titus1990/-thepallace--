<?php
global $rule;
$display_date = in_array( 'date', $rule['meta'] );
$display_author = in_array( 'author', $rule['meta'] );
$display_categories = in_array( 'categories', $rule['meta'] );
$display_tags = in_array( 'tags', $rule['meta'] );
$display_comments_number = in_array( 'comments', $rule['meta'] );
$meta_displayed = false;
if ( $display_date || $display_author || $display_categories || $display_tags || $display_comments_number ) {
	echo( '<p class="post-meta">' );
	if ( $display_date ) {
		_e('Date: ', 'thepalace');
		echo( get_the_date() );
		$meta_displayed = true;
	}
	if ( $display_author ) {
		if ( $meta_displayed ) {
			echo('<span class="post-meta-sep">&nbsp;&nbsp;&#47;&#47;&nbsp;&nbsp;</span>');
		}
		_e('Author: ', 'thepalace');
		the_author_posts_link();
		$meta_displayed = true;
	}
	if ( $display_categories ) {
		$postcategories = get_the_category();
		if ( $postcategories ) {
			if ( $meta_displayed ) {
				echo('<span class="post-meta-sep">&nbsp;&nbsp;&#47;&#47;&nbsp;&nbsp;</span>');
			}
			if ( count($postcategories) == 1 ) {
				_e('Category: ', 'thepalace'); 
			} else {
				_e('Categories: ', 'thepalace'); 
			}
			the_category(', ');
			$meta_displayed = true;			
		}
	}
	if ( $display_tags ) {
		$posttags = get_the_tags();
		if ( $posttags ) {
			if ( $meta_displayed ) {
				echo('<span class="post-meta-sep">&nbsp;&nbsp;&#47;&#47;&nbsp;&nbsp;</span>');
			}
			if ( count($posttags) == 1 ) {
				_e('Tag: ', 'thepalace');
			} else {
				_e('Tags: ', 'thepalace');
			}
			the_tags('', ', ', '');
			$meta_displayed = true;
		}
	}
	if ( $display_comments_number && comments_open() && ! post_password_required() ) {
		if ( $meta_displayed ) {
			echo('<span class="post-meta-sep">&nbsp;&nbsp;&#47;&#47;&nbsp;&nbsp;</span>');
		}
		comments_popup_link(__('Add a comment', 'thepalace'), __('1 comment', 'thepalace'), __('% comments', 'thepalace'));
	}
	echo( '</p>' );
}
?>