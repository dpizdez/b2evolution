<?php
/**
 * This is the main/default page template.
 *
 * For a quick explanation of b2evo 2.0 skins, please start here:
 * {@link http://manual.b2evolution.net/Skins_2.0}
 *
 * It is used to display the blog when no specific page template is available to handle the request.
 *
 * @package evoskins
 * @subpackage photoblog
 *
 * @version $Id$
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

// This is the main template; it may be used to display very different things.
// Do inits depending on current $disp:
skin_init( $disp );


// -------------------------- HTML HEADER INCLUDED HERE --------------------------
require $skins_path.'_html_header.inc.php';
// Note: You can customize the default HTML header by copying the
// _html_header.inc.php file into the current skin folder.
// -------------------------------- END OF HEADER --------------------------------
?>

<div class="PageTop">
	<?php
		// Display container and contents:
		$Skin->container( NT_('Page Top'), array(
				// The following params will be used as defaults for widgets included in this container:
				'block_start' => '<div class="$wi_class$">',
				'block_end' => '</div>',
				'block_display_title' => false,
				'list_start' => '<ul>',
				'list_end' => '</ul>',
				'item_start' => '<li>',
				'item_end' => '</li>',
			) );
	?>
</div>

<div class="pageHeader">

	<div class="floatright">
		<a href="<?php $Blog->disp( 'url', 'raw' ) ?>"><?php echo T_('Recently') ?></a>
		|
		<a href="<?php $Blog->disp( 'arcdirurl', 'raw' ) ?>"><?php echo T_('Index') ?></a>
		|
		<a href="<?php $Blog->disp( 'catdirurl', 'raw' ) ?>"><?php echo T_('Albums') ?></a>
		<?php
			user_login_link( ' | ', ' ' );
			user_register_link( ' | ', ' ' );
		?>
	</div>
	
	<h1 id="pageTitle"><a href="<?php $Blog->disp( 'url', 'raw' ) ?>"><?php $Blog->disp( 'name', 'htmlbody' ) ?></a></h1>

</div>
<div class="bPosts">
	
<!-- =================================== START OF MAIN AREA =================================== -->

	<?php
	// ------------------------- MESSAGES GENERATED FROM ACTIONS -------------------------
	$Messages->disp( '<div class="action_messages">', '</div>' );
	// --------------------------------- END OF MESSAGES ---------------------------------
	?>
	

	<?php
		if( isset($MainList) )
		{ // Links to list pages:
			$MainList->page_links( '<div class="nav_right">', '</div>', '$next$ $prev$', array(
				'prev_text' => '<img src="img/prev.gif" width="29" height="29" alt="'.T_('Previous').'" title="'.T_('Previous').'" />',
				'next_text' => '<img src="img/next.gif" width="29" height="29" alt="'.T_('Next').'" title="'.T_('Next').'" />',
				'no_prev_text' => '',
				'no_next_text' => '<img src="'.$rsc_url.'/img/blank.gif" width="29" height="29" alt="" class="no_nav" />',

			) );
		}
	?>


	<?php
		if( isset($MainList) )
		{ // Links to previous and next post in single post mode:
			echo '<div class="nav_right">';
			$MainList->next_item_link( '', ' ', '<img src="img/next.gif" width="29" height="29" alt="'.T_('Next').'" title="'.T_('Next').'" />', '<img src="'.$rsc_url.'/img/blank.gif" width="29" height="29" alt="" class="no_nav" />' );
			$MainList->prev_item_link( '', '', '<img src="img/prev.gif" width="29" height="29" alt="'.T_('Previous').'" title="'.T_('Previous').'" />' );
			echo '</div>';
		}
	?>


	<?php
	// ------------------------- TITLE FOR THE CURRENT REQUEST -------------------------
	request_title( '<h2>', '</h2>', ' - ', 'htmlbody', array(
				'arcdir_text' => T_('Index'),
				'catdir_text' => T_('Albums'),
				'category_text' => T_('Album').': ',
				'categories_text' => T_('Albums').': ',
		 ), false, '<h2>&nbsp;</h2>' );
	// ------------------------------ END OF REQUEST TITLE -----------------------------
	?>



	<?php
	// ------------------------------------ START OF POSTS ----------------------------------------
	if( isset($MainList) ) $MainList->display_if_empty(); // Display message if no post

	if( isset($MainList) ) while( $Item = & $MainList->get_item() )
	{
	?>
	
	<div class="bPost bPost<?php $Item->status( 'raw' ) ?>" lang="<?php $Item->lang() ?>">
		<?php
			locale_temp_switch( $Item->locale ); // Temporarily switch to post locale
			$Item->anchor(); // Anchor for permalinks to refer to
		?>


		<?php
			// Display images that are linked to this post:
			$Item->images( array(
					'before' =>              '<div class="bImages">',
					'before_image' =>        '<div class="image_block">',
					'before_image_legend' => '<div class="image_legend">',
					'after_image_legend' =>  '</div>',
					'after_image' =>         '</div>',
					'after' =>               '</div>',
					'image_size' =>          'fit-720x500'
				) );
		?>


		<div class="bDetails">

			<div class="bSmallHead">

				<?php $Item->feedback_link( 'feedbacks', '<div class="action_right">', '</div>',
								get_icon( 'nocomment' ), get_icon( 'comments' ), get_icon( 'comments' ),
								'#', 'published', true ) // Link to comments ?>

				<div class="action_right"><?php $Item->permanent_link( T_('Permalink'), '#' ); ?></div>

				<?php $Item->edit_link( '<div class="action_right">', '</div>', T_('Edit...'), T_('Edit title/description...') ) // Link to backoffice for editing ?>

				<h3 class="bTitle"><?php $Item->title(); ?></h3>
				<span class="timestamp"><?php $Item->issue_date( locale_datefmt().' H:i' ); ?></span>

			</div>

			<?php
				// ---------------------- POST CONTENT INCLUDED HERE ----------------------
				require $skins_path.'_item_content.inc.php';
				// Note: You can customize the default item feedback by copying the generic
				// /skins/_item_feedback.inc.php file into the current skin folder.
				// -------------------------- END OF POST CONTENT -------------------------
			?>

			<div class="bSmallPrint">
			<?php
					echo T_('Albums'), ': ';
					$Item->categories();
				?>
			</div>
		</div>

		<?php
			// ------------------ FEEDBACK (COMMENTS/TRACKBACKS) INCLUDED HERE ------------------
			require $skins_path.'_item_feedback.inc.php';
			// Note: You can customize the default item feedback by copying the generic
			// /skins/_item_feedback.inc.php file into the current skin folder.
			// ---------------------- END OF FEEDBACK (COMMENTS/TRACKBACKS) ---------------------
		?>

		<?php
			locale_restore_previous();	// Restore previous locale (Blog locale)
		?>
	</div>
	<?php
	} // ---------------------------------- END OF POSTS ------------------------------------

?>
	

	<?php
	// -------------- START OF INCLUDES FOR LATEST COMMENTS, MY PROFILE, ETC. --------------
	// Note: you can customize any of the sub templates included here by
	// copying the matching php file into your skin directory.
	// Call the dispatcher:
	require $skins_path.'_dispatch.inc.php';
	// --------------- END OF INCLUDES FOR LATEST COMMENTS, MY PROFILE, ETC. ---------------

?>
	
</div>


<?php
// ------------------------- BODY FOOTER INCLUDED HERE --------------------------
require $skins_path.'_body_footer.inc.php';
// Note: You can customize the default BODY footer by copying the
// _body_footer.inc.php file into the current skin folder.
// ------------------------------- END OF FOOTER --------------------------------
?>


<?php
// ------------------------- HTML FOOTER INCLUDED HERE --------------------------
require $skins_path.'_html_footer.inc.php';
// Note: You can customize the default HTML footer by copying the
// _html_footer.inc.php file into the current skin folder.
// ------------------------------- END OF FOOTER --------------------------------
?>