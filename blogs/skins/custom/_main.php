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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php
	bloginfo('name', 'htmlhead');
	single_cat_title( ' - ', 'htmlhead' );
	single_month_title( ' - ', 'htmlhead' );
	single_post_title( ' - ', 'htmlhead' );
	switch( $disp )
	{
		case 'comments': echo " - Last comments"; break;
		case 'stats': echo " - Statistics"; break;
		case 'arcdir': echo " - Archive Directory"; break;
	}
?>
</title>
<base href="<?php skinbase(); // Base URL for this skin. You need this to fix relative links! ?>" />
<meta name="description" content="<?php bloginfo('shortdesc', 'htmlhead'); ?>" />
<meta name="keywords" content="<?php bloginfo('keywords', 'htmlhead'); ?>" />
<link rel="alternate" type="text/xml" title="RDF" href="<?php bloginfo('rdf_url', 'raw'); ?>" />
<link rel="alternate" type="text/xml" title="RSS" href="<?php bloginfo('rss2_url', 'raw'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url', 'raw'); ?>" />
<link rel="stylesheet" href="custom.css" type="text/css" />
</head>
<body>
<div class="pageHeader">

<?php // ---------------------------------- START OF BLOG LIST ----------------------------------
for( $curr_blog_ID=blog_list_start('stub'); 
			$curr_blog_ID!=false; 
			 $curr_blog_ID=blog_list_next('stub') ) 
{ # by uncommenting the following lines you can hide some blogs
	// if( $curr_blog_ID == 1 ) continue; // Hide blog 1...
	// if( $curr_blog_ID == 2 ) continue; // Hide blog 2...
if( $curr_blog_ID == $blog ) { // This is the blog being displayed on this page ?>
<a href="<?php blog_list_iteminfo('blogurl', 'raw') ?>" class="BlogButtonCurr"><?php blog_list_iteminfo('shortname', 'htmlbody') ?></a>
<?php } else { // This is another blog ?>
<a href="<?php blog_list_iteminfo('blogurl') ?>" class="BlogButton"><?php blog_list_iteminfo('shortname', 'htmlbody') ?></a>
<?php } // End of testing which blog is being displayed 
} // --------------------------------- END OF BLOG LIST --------------------------------- ?>

<h1 id="pageTitle"><?php bloginfo('name', 'htmlbody') ?></h1>

<div class="pageSubTitle"><?php bloginfo('tagline', 'htmlbody') ?></div>

</div>

<div class="bPosts">
<h2><?php
	single_cat_title( " Category: ", 'htmlbody' );
	single_month_title( " Archives for: ", 'htmlbody' );
	single_post_title( " Post details: ", 'htmlbody' );
	switch( $disp )
	{
		case 'comments': echo "Last comments"; break;
		case 'stats': echo "Statistics"; break;
		case 'arcdir': echo "Archive Directory"; break;
	}
?></h2>

<!-- =================================== START OF MAIN AREA =================================== -->

<?php	// ------------------------------------ START OF POSTS ----------------------------------------
	if( isset($MainList) ) while( $MainList->get_item() )
	{
		the_date("d.m.y","<h2>","</h2>");
	?>
	<div class="bPost" lang="<?php the_lang() ?>">
		<?php permalink_anchor(); ?>
		<div class="bSmallHead">
		<a href="<?php permalink_link() ?>" title="Permanent link to full entry"><img src="img/icon_minipost.gif" alt="Permalink" width="12" height="9" class="middle" /></a>
		<?php the_time() ?>, Categories: <?php the_categories( "Browse category", "<strong>", "</strong>", "", "", "<em>", "</em>" ) ?>
		</div>
		<h3 class="bTitle"><?php the_title(); ?></h3>
		<div class="bText">
		  <?php the_content("=> Read more!",0,'','<p>[More:]</p>'); ?>
		  <?php link_pages("<br />Pages: ","<br />","number") ?>
		</div>
		<div class="bSmallPrint">
		<a href="<?php permalink_link() ?>#comments" title="Display comments / Leave a comment"><?php comments_number("Leave a comment...", "1 comment", "% comments") ?></a>
		-
		<a href="<?php permalink_link() ?>#trackbacks" title="Display trackbacks / Get trackback address for this post"><?php trackback_number("TrackBack (0)", "TrackBack (1)", "TrackBack (%)") ?></a>
		<?php trackback_rdf() // trackback autodiscovery information ?>
		-
		<a href="<?php permalink_link() ?>#comments" title="Display pingbacks"><?php pingback_number("PingBack (0)", "PingBack (1)", "PingBack (%)") ?></a>
		-
		<a href="<?php permalink_link() ?>" title="Permanent link to full entry">Permalink</a>
		<?php if( $debug==1 ) echo "- $querycount queries so far"; ?>
		</div>
		<?php	// ---------------- START OF INCLUDE FOR COMMENTS, TRACKBACK, PINGBACK, ETC. ----------------
		$disp_comments = 1;					// Display the comments if requested
		$disp_comment_form = 1;			// Display the comments form if comments requested
		$disp_trackbacks = 1;				// Display the trackbacks if requested

		$disp_trackback_url = 1;		// Display the trackbal URL if trackbacks requested
		$disp_pingbacks = 1;				// Display the pingbacks if requested
		include( dirname(__FILE__)."/_feedback.php");
		// ------------------- END OF INCLUDE FOR COMMENTS, TRACKBACK, PINGBACK, ETC. ------------------- ?>
	</div>
<?php } // ---------------------------------- END OF POSTS ------------------------------------ ?> 

	<p class="center"><strong><?php posts_nav_link(); ?></strong></p>

<?php // ---------------- START OF INCLUDES FOR LAST COMMENTS, STATS ETC. ----------------

	// this includes the last comments if requested:
	include( dirname(__FILE__)."/_lastcomments.php");

	// this includes the statistics if requested:
	include( dirname(__FILE__)."/_stats.php");

	// this includes the archive directory if requested
	include( dirname(__FILE__)."/_arcdir.php");

// ------------------- END OF INCLUDES FOR LAST COMMENTS, STATS ETC. ------------------- ?>
</div>
<!-- =================================== START OF SIDEBAR =================================== -->

<div class="bSideBar">

	<div class="bSideItem">
	  <h3><?php bloginfo('name', 'htmlbody') ?></h3>
	  <p><?php bloginfo('longdesc', 'htmlbody'); ?></p>
		<p class="center"><strong><?php posts_nav_link(); ?></strong></p>
		<!--?php next_post(); // activate this if you want a link to the next post in single page mode ?-->
		<!--?php previous_post(); // activate this if you want a link to the previous post in single page mode ?-->
		<ul>
	  	<li><a href="<?php bloginfo('staticurl', 'raw') ?>"><strong>Recently</strong></a> <span class="dimmed">(cached)</span></li>
	  	<li><a href="<?php bloginfo('dynurl', 'raw') ?>"><strong>Recently</strong></a> <span class="dimmed">(no cache)</span></li>
		</ul>
		<?php	// -------------------------- CALENDAR INCLUDED HERE -----------------------------
			include( dirname(__FILE__)."/_calendar.php"); 
			// -------------------------------- END OF CALENDAR ---------------------------------- ?>
		<ul>
	  	<li><a href="<?php bloginfo('lastcommentsurl') ?>"><strong>Last comments</strong></a></li>
	  	<li><a href="<?php bloginfo('blogstatsurl') ?>"><strong>Some viewing statistics</strong></a></li>
		</ul>
	</div>
	
	<div class="bSideItem">
    <h3 class="sideItemTitle">Search</h3>
		<form name="SearchForm" method="get" class="search" action="<?php bloginfo('blogurl') ?>">
			<input type="text" name="s" size="30" value="<?php echo $s ?>" class="SearchField" /><br />
			<input type="radio" name="sentence" value="AND" id="sentAND" <?php if( $sentence=='AND' ) echo 'checked ' ?>/><label for="sentAND">All Words</label>
			<input type="radio" name="sentence" value="OR" id="sentOR" <?php if( $sentence=='OR' ) echo 'checked ' ?>/><label for="sentOR">Some Word</label>
			<input type="radio" name="sentence" value="sentence" id="sentence" <?php if( $sentence=='sentence' ) echo 'checked ' ?>/><label for="sentence">Sentence</label>
			<input type="submit" name="submit" value="Search" />
			<input type="reset" value="Reset" />
		</form>
	</div>

	<div class="bSideItem">
		<h3>Categories</h3>
		<form action="<?php bloginfo('blogurl', 'raw') ?>" method="get">
		<?php	// -------------------------- CATEGORIES INCLUDED HERE -----------------------------
			include( dirname(__FILE__)."/_categories.php"); 
			// -------------------------------- END OF CATEGORIES ---------------------------------- ?>
		<br />
		<input type="submit" value="Get selection" />
		<input type="reset" />
		</form>
	</div>

	<div class="bSideItem">
    <h3>Archives</h3>
    <ul>
			<?php	// -------------------------- ARCHIVES INCLUDED HERE -----------------------------
				include( dirname(__FILE__)."/_archives.php"); 
				// -------------------------------- END OF ARCHIVES ---------------------------------- ?>
				<li><a href="<?php bloginfo('blogurl') ?>?disp=arcdir">more...</a></li>
	  </ul>
  </div>

	<div class="bSideItem">
    <h3>Choose skin</h3>
		<ul>
			<?php // ---------------------------------- START OF SKIN LIST ----------------------------------
			for( skin_list_start(); skin_list_next(); ) { ?>
				<li><a href="<?php skin_change_url() ?>"><?php skin_list_iteminfo( 'name', 'htmlbody' ) ?></a></li>
			<?php } // --------------------------------- END OF SKIN LIST --------------------------------- ?>
		</ul>
	</div>

	<?php if (! $stats) 
	{ ?>
	
	<div class="bSideItem">
		<h3>Recent Referers</h3>
			<?php refererList(5,'global',0,0,'no','',($blog>1)?$blog:''); ?>
	  	<ul>
				<?php while($row_stats = mysql_fetch_array($res_stats)){  ?>
					<li><a href="<?php stats_referer() ?>"><?php stats_basedomain() ?></a></li>
				<?php } // End stat loop ?>
				<li><a href="<?php bloginfo('blogstatsurl') ?>">more...</a></li>
			</ul>
		<br />
		<h3>Top Referers</h3>
			<?php refererList(5,'global',0,0,'no','baseDomain',($blog>1)?$blog:''); ?>
	   	<ul>
				<?php while($row_stats = mysql_fetch_array($res_stats)){  ?>
					<li><a href="<?php stats_referer() ?>"><?php stats_basedomain() ?></a></li>
				<?php } // End stat loop ?>
				<li><a href="<?php bloginfo('blogstatsurl') ?>">more...</a></li>
			</ul>
	</div>

	<?php } ?>


	<div class="bSideItem">
    <h3>Blogroll</h3>
		<?php	// -------------------------- BLOGROLL INCLUDED HERE -----------------------------
			include( dirname(__FILE__)."/_blogroll.php"); 
			// -------------------------------- END OF BLOGROLL ---------------------------------- ?>
	</div>

	<div class="bSideItem">
    <h3>Misc</h3>
		<ul>  
			<li><a href="<?php echo $pathserver?>/b2login.php">Login</a> </li>
			<li><a href="<?php echo $pathserver?>/b2register.php">Register</a></li>
		</ul>	
	</div>

	<div class="bSideItem">
    <h3>Syndicate this blog <img src="../../img/xml.gif" alt="XML" width="36" height="14" class="middle" /></h3>

      <ul>
        <li><a href="<?php bloginfo('rss_url', 'raw'); ?>">RSS 0.92 (Userland)</a></li>
        <li><a href="<?php bloginfo('rdf_url', 'raw'); ?>">RSS 1.0 (RDF)</a></li>
        <li><a href="<?php bloginfo('rss2_url', 'raw'); ?>">RSS 2.0 (Userland)</a></li>
      </ul>
      <p><a href="http://www.xml.com/pub/a/2002/12/18/dive-into-xml.html" title="xml.com - External - English">What
        is RSS?</a> by Mark Pilgrim</p>

	</div>

	<p class="center">powered by<br />
	<a href="http://b2evolution.net/" title="b2evolution home"><img src="../../img/b2evolution_button.png" alt="b2evolution" width="80" height="15" border="0" class="middle" /></a></p>

</div>

<p class="baseline"> <a href="http://validator.w3.org/check/referer"><img style="border:0;width:88px;height:31px" src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0!" class="middle" /></a> 
  <a href="http://jigsaw.w3.org/css-validator/"><img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss" alt="Valid CSS!" class="middle" /></a>&nbsp;
<?php
	log_hit();	// log the hit on this page
	if ($debug==1)
	{
		echo "Totals: $result_num_rows posts - $querycount queries - ".number_format(timer_stop(),3)." seconds";
	}
?>
</p>
</body>
</html>
