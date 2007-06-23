<?php
/**
 * This is the template that displays the contents for a post
 * (images, teaser, more link, body, etc...)
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template (or other templates)
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2003-2007 by Francois PLANQUE - {@link http://fplanque.net/}
 *
 * @package evoskins
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

if( file_exists( $ads_current_skin_path.'_item_content.inc.php' ) )
{	// The skin has a customized handler, use that one instead:
	require $ads_current_skin_path.'_item_content.inc.php';
	return;
}

// Display images that are linked to this post:
$Item->images( array(
		'before' =>              '<div class="bImages">',
		'before_image' =>        '<div class="image_block">',
		'before_image_legend' => '<div class="image_legend">',
		'after_image_legend' =>  '</div>',
		'after_image' =>         '</div>',
		'after' =>               '</div>',
		'image_size' =>          'fit-400x320'
	) );
?>

<div class="bText">
	<?php
		// Increment view count!
		$Item->count_view( false );

		// Display CONTENT:
		$Item->content_teaser( array(
				'before'      => '',
				'after'       => '',
			) );
		$Item->more_link();
		$Item->content_extension( array(
				'before'      => '',
				'after'       => '',
			) );

		// Links to post pages (for multipage posts):
		$Item->page_links( '<p class="right">'.T_('Pages:').' ', '</p>', ' &middot; ' );
	?>
</div>

<?php
/*
 * $Log$
 * Revision 1.1  2007/06/23 22:09:29  fplanque
 * feedback and item content templates.
 * Interim check-in before massive changes ahead.
 *
 */
?>
