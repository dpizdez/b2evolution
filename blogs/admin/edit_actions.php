<?php
/**
 * Editing actions
 *
 * b2evolution - {@link http://b2evolution.net/}
 *
 * Performs one of the following:
 * - Insert new post
 * - Update existing post
 * - Publish existing post
 * - Delete existing post
 * - Update existing comment
 * - Delete existing comment
 * 
 * Released under GNU GPL License - http://b2evolution.net/about/license.html
 *
 * @copyright (c)2003-2004 by Francois PLANQUE - {@link http://fplanque.net/}
 *
 * @package admin
 */
require_once( dirname(__FILE__).'/_header.php' );

param( 'action', 'string', '' );
param( 'mode', 'string', '' );

param( 'edit_date', 'integer', 0 );
param( 'aa', 'integer', 2000 );
param( 'mm', 'integer', 1 );
param( 'jj', 'integer', 1 );
param( 'hh', 'integer', 20 );
param( 'mn', 'integer', 30 );
param( 'ss', 'integer', 0 );
$jj = ($jj > 31) ? 31 : $jj;
$hh = ($hh > 23) ? $hh - 24 : $hh;
$mn = ($mn > 59) ? $mn - 60 : $mn;
$ss = ($ss > 59) ? $ss - 60 : $ss;

// All statuses are allowed for acting on:
$show_statuses = array( 'published', 'protected', 'private', 'draft', 'deprecated' );

switch($action) 
{
		
	case 'post':
		/*
		 * --------------------------------------------------------------------
		 * INSERT POST & more
		 */
		param( 'post_category', 'integer', true );
		$blog = get_catblog($post_category); 
		get_blogparams();
	
		$title = T_('Adding new post...');
		require(dirname(__FILE__).'/_menutop.php');
		require(dirname(__FILE__).'/_menutop_end.php');
	
		param( 'post_status', 'string', 'published' );
		param( 'post_extracats', 'array', array() );
		// make sure main cat is in extracat list and there are no duplicates
		$post_extracats[] = $post_category;
		$post_extracats = array_unique( $post_extracats );
		// Check permission on statuses:
		$current_User->check_perm( 'cats_post_statuses', $post_status, true, $post_extracats );
	
		param( "post_autobr", 'integer', 0 );
		param( "post_pingback", 'integer', 0 );
		param( 'trackback_url', 'string' );
		$post_trackbacks = & $trackback_url;
		param( 'content', 'html' );
		param( 'post_title', 'html' );
		param( 'post_url', 'string' );
		param( 'post_comments', 'string',  'open' );		// 'open' or 'closed' or ...
		param( 'post_lang', 'string', $default_language );
	
		if( $edit_date && $current_User->check_perm( 'edit_timestamp' )) 
		{	// We use user date
			$post_date = date('Y-m-d H:i:s', mktime( $hh, $mn, $ss, $mm, $jj, $aa ) );
		}
		else
		{	// We use current time
			$post_date = date('Y-m-d H:i:s', $localtimenow);
		}
	
		// CHECK and FORMAT content
		$post_title = format_to_post($post_title,0,0);
		if( $error = validate_url( $post_url, $allowed_uri_scheme ) )
		{
			errors_add( T_('Supplied URL is invalid: ').$error );	
		}
		$content = format_to_post($content,$post_autobr,0);
	
		if( errors_display( T_('Cannot post, please correct these errors:'), 
				'[<a href="javascript:history.go(-1)">'.T_('Back to post editing').'</a>]' ) )
		{
			break;
		}
	
		echo "<div class=\"panelinfo\">\n";
		echo '<h3>', T_('Recording post...'), "</h3>\n";
	
		// Are we going to do the pings or not?
		$pingsdone = ( $post_status == 'published' ) ? true : false;
	
		// INSERT NEW POST INTO DB:
		$post_ID = bpost_create( $user_ID, $post_title, $content, $post_date, $post_category,	$post_extracats, $post_status, $post_lang, '',	$post_autobr, $pingsdone, $post_url, $post_comments ) or mysql_oops($query);
	
		if (isset($sleep_after_edit) && $sleep_after_edit > 0) 
		{
			echo '<p>', T_('Sleeping...'), "</p>\n";
			flush();
			sleep($sleep_after_edit);
		}
		echo '<p>', T_('Done.'), "</p>\n";
		echo "</div>\n";
	
		if( $post_status != 'published' )
		{
			echo "<div class=\"panelinfo\">\n";
			echo '<p>', T_('Post not publicly published: skipping trackback, pingback and blog pings...'), "</p>\n";
			echo "</div>\n";
		}
		else
		{	// We do all the pinging now!
			$blogparams = get_blogparams_by_ID( $blog );
			// trackback
			trackbacks( $post_trackbacks, $content, $post_title, $post_ID);
			// pingback
			pingback( $post_pingback, $content, $post_title, $post_url, $post_ID, $blogparams);
			pingb2evonet($blogparams, $post_ID, $post_title);
			pingWeblogs($blogparams);
			pingBlogs($blogparams);		
			pingTechnorati($blogparams);
		}
	
		param( 'mode', 'string', '' );
		switch($mode) 
		{
			case "sidebar":
				$location="b2sidebar.php?a=b&blog=$blog";
				break;
				
			default:
				$location="b2browse.php?blog=$blog";
				break;
		}
	
		echo '<p>', T_('Posting Done...'), '</p>';
		break;
	
	
	
	
	case 'editpost':
		/*
		 * --------------------------------------------------------------------
		 * UPDATE POST 
		 */
		param( "post_category", 'integer', true );
		$blog = get_catblog($post_category); 
		get_blogparams();
	
		$title = T_('Updating post...');
		require(dirname(__FILE__).'/_menutop.php');
		require(dirname(__FILE__).'/_menutop_end.php');
		
		param( 'post_status', 'string', 'published' );
		param( 'post_extracats', 'array', array() );
		// make sure main cat is in extracat list and there are no duplicates
		$post_extracats[] = $post_category;
		$post_extracats = array_unique( $post_extracats );
		// Check permission on statuses:
		$current_User->check_perm( 'cats_post_statuses', $post_status, true, $post_extracats );
	
		param( 'post_ID', 'integer', true );
		param( "post_autobr", 'integer', 0 );
		param( "post_pingback", 'integer', 0 );
		param( 'trackback_url', 'string' );
		$post_trackbacks = $trackback_url;
		param( 'content', 'html' );
		param( 'post_title', 'html' );
		param( 'post_url', 'string' );
		param( 'post_comments', 'string',  'open' );		// 'open' or 'closed' or ...
		param( 'post_lang', 'string', $default_language );
	
		$postdata = get_postdata($post_ID) or die(T_('Oops, no post with this ID.'));
		if( $edit_date && $current_User->check_perm( 'edit_timestamp' )) 
		{	// We use user date
			$post_date = date('Y-m-d H:i:s', mktime( $hh, $mn, $ss, $mm, $jj, $aa ) );
		}
		else
		{	// We use current time
			$post_date = $postdata['Date'];
		}
	
		// CHECK and FORMAT content	
		$post_title = format_to_post($post_title,0,0);
		if( $error = validate_url( $post_url, $allowed_uri_scheme ) )
		{
			errors_add( T_('Supplied URL is invalid: ').$error );	
		}
		$content = format_to_post($content,$post_autobr,0);
	
		if( errors_display( T_('Cannot update, please correct these errors:'), 
				'[<a href="javascript:history.go(-1)">'.T_('Back to post editing').'</a>]' ) )
		{
			break;
		}
	
		echo "<div class=\"panelinfo\">\n";
		echo "<h3>Updating post...</h3>\n";
	
		// We need to check the previous flags...
		$post_flags = $postdata['Flags'];
		if( in_array( 'pingsdone', $post_flags ) )
		{	// pings have been done before
			$pingsdone = true;
		}
		elseif( $post_status != 'published' )
		{	// still not publishing
			$pingsdone = false;
		}
		else
		{	// We'll be pinging now
			$pingsdone = true;
		}
	
		// UPDATE POST IN DB:
		bpost_update( $post_ID, $post_title, $content, $post_date, $post_category, $post_extracats, 	$post_status, $post_lang, '',	$post_autobr, $pingsdone, $post_url, $post_comments ) or mysql_oops($query);
	
		if (isset($sleep_after_edit) && $sleep_after_edit > 0) 
		{
			echo "<p>Sleeping...</p>\n";
			flush();
			sleep($sleep_after_edit);
		}
		echo '<p>', T_('Done.'), "</p>\n";
		echo "</div>\n";
	
		if( $post_status != 'published' )
		{
			echo "<div class=\"panelinfo\">\n";
			echo '<p>', T_('Post not publicly published: skipping trackback, pingback and blog pings...'), "</p>\n";
			echo "</div>\n";
		}
		else
		{	// We may do some pinging now!
			$blogparams = get_blogparams_by_ID( $blog );
	
			// trackback
			trackbacks( $post_trackbacks, $content,  $post_title, $post_ID );	
			// pingback
			pingback( $post_pingback, $content, $post_title, $post_url, $post_ID, $blogparams);
		
			// ping ?	
			if( in_array( 'pingsdone', $post_flags ) )
			{	// pings have been done before
				echo "<div class=\"panelinfo\">\n";
				echo '<p>', T_('Post had already pinged: skipping blog pings...'), "</p>\n";
				echo "</div>\n";
			}
			else
			{	// We'll ping now
				pingb2evonet( $blogparams, $post_ID, $post_title);
				pingWeblogs($blogparams);
				pingBlogs($blogparams);		
				pingTechnorati($blogparams);
			}
		}	
		
		echo '<p>', T_('Updating done...'), '</p>';
	
		$location="b2browse.php?blog=$blog";
		break;
	
	
	case 'publish':
		/*
		 * --------------------------------------------------------------------
		 * PUBLISH POST NOW
		 */
		param( 'post_ID', 'integer', true );
		$postdata = get_postdata($post_ID) or die(T_('Oops, no post with this ID.'));
		$post_cat =$postdata['Category'];
		$blog = get_catblog($post_cat); 
		get_blogparams();
	
		$title = T_('Updating post status...');
		require(dirname(__FILE__).'/_menutop.php');
		require(dirname(__FILE__).'/_menutop_end.php');
	
		$post_status = 'published';
		// Check permissions:
		/* TODO: Check extra categories!!! */
		$current_User->check_perm( 'blog_post_statuses', $post_status, true, $blog );
		$current_User->check_perm( 'edit_timestamp', 'any', true ) ;
	
		$post_date = date('Y-m-d H:i:s', $localtimenow);
		$post_title = $postdata['Title'];
		$post_url = $postdata['Url'];
		
		echo "<div class=\"panelinfo\">\n";
		echo "<h3>Updating post status...</h3>\n";
	
		// We need to check the previous flags...
		$post_flags = $postdata['Flags'];
		if( in_array( 'pingsdone', $post_flags ) )
		{	// pings have been done before
			$pingsdone = true;
		}
		elseif( $post_status != 'published' )
		{	// still not publishing
			$pingsdone = false;
		}
		else
		{	// We'll be pinging now
			$pingsdone = true;
		}
	
		// UPDATE POST IN DB:
		bpost_update_status( $post_ID, $post_status, $pingsdone, $post_date ) or mysql_oops($query);
	
		if (isset($sleep_after_edit) && $sleep_after_edit > 0) 
		{
			echo "<p>Sleeping...</p>\n";
			flush();
			sleep($sleep_after_edit);
		}
		echo '<p>', T_('Done.'), "</p>\n";
		echo "</div>\n";
	
		if( $post_status != 'published' )
		{
			echo "<div class=\"panelinfo\">\n";
			echo '<p>', T_('Post not publicly published: skipping trackback, pingback and blog pings...'), "</p>\n";
			echo "</div>\n";
		}
		else
		{	// We may do some pinging now!
			$blogparams = get_blogparams_by_ID( $blog );
	
			// ping ?	
			if( in_array( 'pingsdone', $post_flags ) )
			{	// pings have been done before
				echo "<div class=\"panelinfo\">\n";
				echo '<p>', T_('Post had already pinged: skipping blog pings...'), "</p>\n";
				echo "</div>\n";
			}
			else
			{	// We'll ping now
				pingb2evonet( $blogparams, $post_ID, $post_title);
				pingWeblogs($blogparams);
				pingBlogs($blogparams);		
				pingTechnorati($blogparams);
			}
		}	
		
		echo '<p>', T_('Updating done...'), '</p>';
	
		$location="b2browse.php?blog=$blog";
		break;
	
	
	case "delete":
		/*
		 * --------------------------------------------------------------------
		 * DELETE a post from db
		 */
		param( 'post', 'integer' );
		// echo $post;
		$postdata = get_postdata($post) or die(T_('Oops, no post with this ID!'));
		$blog = get_catblog($postdata['Category']); 
		get_blogparams();
	
		$title = T_('Deleting post...');
		require(dirname(__FILE__).'/_menutop.php');
		require(dirname(__FILE__).'/_menutop_end.php');
	
		// Check permission:
		$current_User->check_perm( 'blog_del_post', '', true, $blog );
	
		echo "<div class=\"panelinfo\">\n";
		echo '<h3>', T_('Deleting post...'), "</h3>\n";
	
		// DELETE POST FROM DB:
		bpost_delete( $post ) or mysql_oops($query);
	
		if (isset($sleep_after_edit) && $sleep_after_edit > 0) {
			echo '<p>', T_('Sleeping...'), "</p>\n";
			flush();
			sleep($sleep_after_edit);
		}
		echo '<p>', T_('Done.'), "</p>\n";
		echo "</div>\n";
	
		?>
		
		<p><?php echo T_('Deleting Done...') ?><p>
	
		<?php
			$location="b2browse.php?blog=$blog";
		break;
	
	
	
	
	case "editedcomment":
		/*
		 * --------------------------------------------------------------------
		 * UPDATE comment in db:
		 */
		param( 'comment_ID', 'integer', true );
		// echo $comment_ID;
		$edited_Comment = Comment_get_by_ID( $comment_ID );
		$comment_post_ID = $edited_Comment->get('post_ID');
		// echo $comment_post_ID;
		$postdata = get_postdata($comment_post_ID) or die(T_('Oops, no post with this ID.'));
		$blog = get_catblog($postdata['Category']); 
	
		// Check permission:
		$current_User->check_perm( 'blog_comments', '', true, $blog );
	
		param( 'newcomment_author', 'string', true );
		param( 'newcomment_author_email', 'string' );
		param( 'newcomment_author_url', 'string' );
		param( 'content', 'html' );
		param( "post_autobr", 'integer', ($comments_use_autobr == 'always')?1:0 );
	
	
		// CHECK and FORMAT content	
		if( $error = validate_url( $newcomment_author_url, $allowed_uri_scheme ) )
		{
			errors_add( T_('Supplied URL is invalid: ').$error );	
		}
		$content = format_to_post($content,$post_autobr,0); // We are faking this NOT to be a comment
	
		if( errors_display( T_('Cannot update comment, please correct these errors:'), 
				'[<a href="javascript:history.go(-1)">'.T_('Back to post editing').'</a>]' ) )
		{
			break;
		}
	
		$edited_Comment->set( 'content', $content );
		$edited_Comment->set( 'author', $newcomment_author );
		$edited_Comment->set( 'author_email', $newcomment_author_email );
		$edited_Comment->set( 'author_url', $newcomment_author_url );
	
		if( $edit_date && $current_User->check_perm( 'edit_timestamp' )) 
		{	// We use user date
			$edited_Comment->set( 'date', date('Y-m-d H:i:s', mktime( $hh, $mn, $ss, $mm, $jj, $aa ) ) );
		}
	
		$edited_Comment->dbupdate();	// Commit update to the DB
	
		header ("Location: b2browse.php?blog=$blog&p=$comment_post_ID&c=1#comments"); //?a=ec");
		exit();


	case "deletecomment":
		/*
		 * --------------------------------------------------------------------
		 * DELETE comment from db:
		 */
		param( 'comment_ID', 'integer', true );
		// echo $comment_ID;
		$edited_Comment = Comment_get_by_ID( $comment_ID );
		$comment_post_ID = $edited_Comment->get('post_ID');
		// echo $comment_post_ID;
		$postdata = get_postdata($comment_post_ID) or die(T_('Oops, no post with this ID.'));
		$blog = get_catblog($postdata['Category']); 
	
		// Check permission:
		$current_User->check_perm( 'blog_comments', '', true, $blog );
	
		// Delete from Db:
		$edited_Comment->dbdelete();
	
		header ("Location: b2browse.php?blog=$blog&p=$comment_post_ID&c=1#comments");
		exit();
	
	
	default:
		die( 'Unkown action!' );
}

if( ! errors() )
{
	if( empty( $mode ) )
	{	// Normal mode:
		?>
		<p><strong>[<a href="<?php echo $location ?>"><?php echo T_('Back to posts!') ?></a>]</strong></p>
		
		<p><?php echo T_('You may also want to generate static pages or view your blogs...') ?></p>
		<?php
		// List the blogs:
		require( dirname(__FILE__).'/_blogs_list.php'); 
	}
	else
	{	// Special mode:
	?>
		<p><strong>[<a href="b2edit.php?blog=<?php echo $blog ?>&amp;mode=<?php echo $mode ?>"><?php echo T_('New post') ?></a>]</strong></p>
	<?php
	}
}


require( dirname(__FILE__).'/_footer.php' ); 
?>
