<?php 
	/*
	 * This is the template that displays stats for a blog
	 *
	 * This file is not meant to be called directly.
	 * It is meant to be called by an include in the _main.php template.
	 * To display the stats, you should call a stub AND pass the right parameters
	 * For example: /blogs/index.php?disp=stats
	 */
	if(substr(basename($_SERVER['SCRIPT_FILENAME']),0,1)=='_')
		die("Please, do not access this page directly.");
	
	if( ($disp == 'stats') || ($stats) ) 
	{ ?>
	
	<div class="statbloc"><h3>Last referers:</h3>
	<?php refererList(10,'global',1,1,'no','',($blog>1)?$blog:''); ?>
	<ul>
		<?php while($row_stats = mysql_fetch_array($res_stats)){  ?>
			<li><a href="<?php stats_referer() ?>"><?php stats_basedomain() ?></a></li>
		<?php } // End stat loop ?>
	</ul>
	</div>
	
	<div class="statbloc">
	<h3>Top referers:</h3>
	<?php refererList(10,'global',0,0,'no','baseDomain',($blog>1)?$blog:'',false); ?>
	<ol>
		<?php while($row_stats = mysql_fetch_array($res_stats)){  ?>
			<li><a href="<?php stats_referer() ?>"><?php stats_basedomain() ?></a></li>
		<?php } // End stat loop ?>
	</ol>
	</div>
	
	<div class="statbloc" style="clear: left;">
	<h3>Last refering searches:</h3>
	<?php refererList(20,'global',1,1,'search','',($blog>1)?$blog:''); ?>
	<ul>
		<?php while($row_stats = mysql_fetch_array($res_stats)){  ?>
			<li><a href="<?php stats_referer() ?>"><?php stats_search_keywords() ?></a></li>
		<?php } // End stat loop ?>
	</ul>
	</div>
	
	<div class="statbloc">
	<h3>Top refering engines:</h3>
	<?php refererList(10,'global',0,0,'search','baseDomain',($blog>1)?$blog:'',true); ?>
	<table class="invisible">
		<?php while($row_stats = mysql_fetch_array($res_stats)){  ?>
			<tr>
				<td class="right">&#8226;</td>
				<td><a href="<?php stats_referer() ?>"><?php stats_basedomain() ?></a></td>
				<td class="right"><?php stats_hit_percent() ?></td>
			</tr>
		<?php } // End stat loop ?>
	</table>
	</div>
	
	<div class="statbloc">
	<h3>Top Indexing Robots:</h3>
	<?php refererList(10,'global',0,0,'robot','hit_user_agent',($blog>1)?$blog:'',true,true); ?>
	<table class="invisible">
		<?php while($row_stats = mysql_fetch_array($res_stats)){  ?>
			<tr>
				<td class="right">&#8226;</td>
				<td><?php stats_referer("<a href=\"", "\">") ?><?php stats_user_agent('robots') ?><?php stats_referer("", "</a>", false) ?></td>
				<td class="right"><?php stats_hit_percent() ?></td>
			</tr>
		<?php } // End stat loop ?>
	</table>
	</div>
	
	
	<div class="statbloc">
	<h3>Top Aggregators:</h3>
	<?php refererList(10,'global',0,0,'rss','hit_user_agent',($blog>1)?$blog:'',true,true); ?>
	<table class="invisible">
		<?php while($row_stats = mysql_fetch_array($res_stats)){  ?>
			<tr>
				<td class="right">&#8226;</td>
				<td><?php stats_user_agent('robots,aggregators') ?> </td>
				<td class="right"><?php stats_hit_percent() ?></td>
			</tr>
		<?php } // End stat loop ?>
	</table>
	</div>
	
	<div class="clear"></div>
		
	<?php
	}
?>