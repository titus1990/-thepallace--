<div id="comments">

<?php 
if ( post_password_required() ) : 
?>
<p><?php _e( 'This post is password protected. Enter the password to view any comments.', 'thepalace' ); ?></p>
</div><!-- end comments -->
<?php
	return;
endif;

if ( have_comments() ) : ?>
<h3 id="comments-title">
<?php
	printf( _n( '1 comment', '%1$s comments', get_comments_number(), 'thepalace' ), number_format_i18n( get_comments_number() ) );
?>
</h3>

<?php 
endif;

if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {
	echo( '<p>' );
	paginate_comments_links();
	echo( '</p>' );
}

echo( '<ul id="comments-list">' );
wp_list_comments( array( 'callback' => 'aurel_comment' ) );
echo( '</ul>' );

if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {
	echo( '<p>' );
	paginate_comments_links();
	echo( '</p>' );
}
$comments_args = array(
	'cancel_reply_link' =>  __( ' - Cancel reply', 'thepalace' ),
	// remove "Text or HTML to be displayed after the set of comment fields"
	'comment_notes_after' => ''
);
comment_form($comments_args); 
?>

</div><!-- end comments -->