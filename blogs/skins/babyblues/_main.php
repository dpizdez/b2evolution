<?php 
	/*
	 * This is the main template. It displays the blog.
	 *
	 * However this file is not meant to be called directly.
	 * It is ment to be called automagically by b2evolution.
	 * To display a blog, you should call a stub file instead, for example:
	 * /blogs/index.php or /blogs/blog_b.php
	 */
?>
<html>
<head>
<title>Baby Blues</title>
<base href="<?php skinbase(); // Base URL for this skin. You need this to fix relative links! ?>" />
<link rel="stylesheet" type="text/css" href="style.css">

<?php comments_popup_script(); ?>
</head>
<body>
<div id="wide">&nbsp;</div>
<div id="pic"><img src="babies.jpg"></div>
<div id="content"> 
<!-- // b2 loop start -->
 
<?php	// ------------------------------------- START OF POSTS -------------------------------------
	if( isset($MainList) ) while( $MainList->get_item() )
{
?>
<div class="date" align="right">	<?php the_date("","",""); ?> </div><br>
<?php permalink_anchor(); ?>
			<div class="title"><?php the_title(); ?></div>
<?php the_content(); ?>
		  <?php link_pages("<br />Pages: ","<br />","number") ?>

   <div class="posted">by <?php the_author() ?> at <a href="<?php permalink_link() ?>"><?php the_time() ?></a><br>
	<?php comments_popup_link("Comments (0)", "Comments (1)", "Comments (%)") ?> |
<?php trackback_popup_link("TrackBack (0)", "TrackBack (1)", "TrackBack (%)") ?> |
<?php pingback_popup_link("PingBack (0)", "PingBack (1)", "PingBack (%)") ?> |
<a href="?cat=<?php the_category_ID() ?>" title="category: <?php the_category() ?>"><?php the_category() ?></a>
<?php trackback_rdf() ?>
  </div>
      <?php
		// this includes the trackback url, comments, trackbacks, pingbacks and a form to add a new comment
		$disp_comments = 1;					// Display the comments if requested
		$disp_comment_form = 1;			// Display the comments form if comments requested
		$disp_trackbacks = 1;				// Display the trackbacks if requested
		$disp_trackback_url = 1;		// Display the trackbal URL if trackbacks requested
		$disp_pingbacks = 1;				// Display the pingbacks if requested
		include( dirname(__FILE__)."/_feedback.php");
?>

<p>
<?php } // ---------------------------------- END OF POSTS ------------------------------------ ?> 
</div>
<div id="side">
<div class="sidetitle" align="center">blogs</div>
<div class="sidebody">
<?php // ---------------------------------- START OF BLOG LIST ----------------------------------
for( $curr_blog_ID=blog_list_start('stub'); 
			$curr_blog_ID!=false; 
			 $curr_blog_ID=blog_list_next('stub') ) { ?>
<!-- Button start -->
<?php if( $curr_blog_ID == $blog ) { // This is the blog being displayed on this page ?>
<a href="<?php blog_list_iteminfo('blogurl') ?>" class="NavButton2Curr"><strong><?php blog_list_iteminfo('name') ?></span></strong></a><br />
<?php } else { // This is another blog ?>
<a href="<?php blog_list_iteminfo('blogurl') ?>" class="NavButton2"><?php blog_list_iteminfo('name') ?></a><br />
<?php } // End of testing which blog is being displayed ?>
<!-- Button end -->
<?php } // --------------------------------- END OF BLOG LIST --------------------------------- ?>
</div>
<div class="sidetitle" align="center">skin the site</div>
<div class="sidebody">
<?php // ---------------------------------- START OF SKIN LIST ----------------------------------
			for( skin_list_start(); skin_list_next(); ) { ?>
				<a href="<?php skin_change_url() ?>"><?php skin_list_iteminfo( 'name' ) ?></a><br>
			<?php } // --------------------------------- END OF SKIN LIST --------------------------------- ?>
	
</div>
<div class="sidetitle" align="center">archives</div>
<div class="sidebody">
<?php	// -------------------------- ARCHIVES INCLUDED HERE -----------------------------
				include( dirname(__FILE__)."/_archives.php"); 
				// -------------------------------- END OF ARCHIVES ---------------------------------- ?>
</div>

<div class="sidetitle" align="center">misc</div>
<div class="sidebody">
<a href="<?php echo $pathserver?>/b2login.php">login</a><br>
<a href="<?php echo $pathserver?>/b2register.php">register</a></div>	

<div class="sidetitle" align="center">credits</div>
<div class="sidebody">
design from <a href="http://lifeisadiaper.com" title="designed by Sabrina">Sabrina</a><br>
powered by <a href="http://b2evolution.net/"title="b2evolution home"><img src="../../img/b2evolution_button.png" width="80" height="15" class="middle" alt="b2evolution" border="0" /></a><br>
</div>

</div>

<?php // $blog=1;
	log_hit();	// log the hit on this page
	if ($debug==1)
	{
		echo "Totals: $result_num_rows posts - $querycount queries - ".number_format(timer_stop(),3)." seconds";
	}
?>

</body>
</html>