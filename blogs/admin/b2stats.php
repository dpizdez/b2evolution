<?php
/**
 * This file implements the UI controller for browsing the hitlog stats.
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2003-2004 by Francois PLANQUE - {@link http://fplanque.net/}
 *
 * @package admin
 */

/**
 * Includes:
 */
require_once( dirname(__FILE__).'/_header.php' );
$admin_tab = 'stats';
$admin_pagetitle = T_('View Stats for Blog:');

param( 'action', 'string' );
param( 'tab', 'string', 'summary' );

require(dirname(__FILE__) . '/_menutop.php');
?>
<a href="b2stats.php?show=<?php echo $tab ?>&amp;blog=0" class="<?php echo ( 0 == $blog ) ? 'CurrentBlog' : 'OtherBlog' ?>"><?php echo T_('None') ?></a>
<?php
for( $curr_blog_ID=blog_list_start('stub');
			$curr_blog_ID!=false;
			 $curr_blog_ID=blog_list_next('stub') )
	{
		?>
		<a href="b2stats.php?show=<?php echo $tab ?>&amp;blog=<?php echo $curr_blog_ID ?>" class="<?php echo ( $curr_blog_ID == $blog ) ? 'CurrentBlog' : 'OtherBlog' ?>"><?php blog_list_iteminfo('shortname') ?></a>
	<?php
}
require( dirname(__FILE__) . '/_menutop_end.php' );

// Check permission:
$current_User->check_perm( 'stats', 'view', true );

switch( $action )
{
	case 'changetype':
		// Change the type of a hit:

		// Check permission:
		$current_User->check_perm( 'stats', 'edit', true );

		param( 'hit_ID', 'integer', true );	// Required!
		param( 'hit_type', 'string', true );	// Required!
		?>
		<div class="panelinfo">
			<p><?php printf( T_('Changing hit #%d type to: %s'), $hit_ID, $hit_type) ?></p>
			<?php
			hit_change_type( $hit_ID, $hit_type );
			?>
		</div>
		<?php
		break;

	case 'delete':
		// DELETE A HIT:

		// Check permission:
		$current_User->check_perm( 'stats', 'edit', true );

		param( 'hit_ID', 'integer', true );	// Required!
		?>
		<div class="panelinfo">
			<p><?php printf( T_('Deleting hit #%d...'), $hit_ID )?></p>
			<?php
			hit_delete( $hit_ID );
			?>
		</div>
		<?php
		break;

	case 'prune':
		// PRUNE hits for a certain date

		// Check permission:
		$current_User->check_perm( 'stats', 'edit', true );

		param( 'date', 'integer', true );	// Required!
		?>
		<div class="panelinfo">
			<p><?php printf( T_('Pruning hits for %s...'), date( locale_datefmt(), $date) ) ?></p>
			<?php
			hit_prune( $date );
			?>
		</div>
		<?php
		break;
}

// Display submenu:
require dirname(__FILE__).'/_submenu.inc.php';

switch( $tab )
{
	case 'summary':
		?>
		<h2><?php echo T_('Summary') ?>:</h2>
		<?php

		$sql = 'SELECT COUNT(*) AS hits, hit_ignore, YEAR(visitTime) AS year,
										MONTH(visitTime) AS month, DAYOFMONTH(visitTime) AS day 
							FROM T_hitlog ';
		if( $blog > 0 )
		{
			$sql .= ' WHERE hit_blog_ID = '.$blog;
		}
		$sql .= ' GROUP BY YEAR(visitTime), MONTH(visitTime),  DAYOFMONTH(visitTime), hit_ignore
						  ORDER BY YEAR(visitTime), MONTH(visitTime), DAYOFMONTH(visitTime)';
		$res_hits = $DB->get_results( $sql, ARRAY_A );


		/*
		 * Chart
		 */
		if( count($res_hits) )
	  {
			$last_date = 0;

			$chart [ 'chart_data' ][ 0 ][ 0 ] = "";
			$chart [ 'chart_data' ][ 1 ][ 0 ] = T_('Direct Accesses');
			$chart [ 'chart_data' ][ 2 ][ 0 ] = T_('Referers');
			$chart [ 'chart_data' ][ 3 ][ 0 ] = T_('Refering Searches');
			$chart [ 'chart_data' ][ 4 ][ 0 ] = T_('Syndication');
			$chart [ 'chart_data' ][ 5 ][ 0 ] = T_('Indexing Robots');
			$chart [ 'chart_data' ][ 6 ][ 0 ] = T_('Blacklisted');

			$col_mapping = array(
														'invalid' => 1,
														'no' => 2,
														'search' => 3,
														'rss' => 4,
														'robot' => 5,
														'blacklist' => 6
													);

	 		$count = 0;
			foreach( $res_hits as $row_stats )
			{
				$this_date = mktime( 0, 0, 0, $row_stats['month'], $row_stats['day'], $row_stats['year'] );
				if( $last_date != $this_date )
				{	// We just hit a new day, let's display the previous one:
						$last_date = $this_date;	// that'll be the next one
						$count ++;
						$chart [ 'chart_data' ][ 0 ][ $count ] = date( locale_datefmt(), $last_date );
						$chart [ 'chart_data' ][ 1 ][ $count ] = 0;
						$chart [ 'chart_data' ][ 2 ][ $count ] = 0;
						$chart [ 'chart_data' ][ 3 ][ $count ] = 0;
						$chart [ 'chart_data' ][ 4 ][ $count ] = 0;
						$chart [ 'chart_data' ][ 5 ][ $count ] = 0;
						$chart [ 'chart_data' ][ 6 ][ $count ] = 0;
				}
				$col = $col_mapping[$row_stats['hit_ignore']];
				$chart [ 'chart_data' ][$col][$count] = $row_stats['hits'];
			}

			$chart[ 'canvas_bg' ] = array (		'width'  => 780,
																				'height' => 400,
																				'color'  => 'efede0'
																		);

 			$chart[ 'chart_rect' ] = array (	'x'      => 50,
																				'y'      => 50,
																				'width'  => 700,
																				'height' => 250
																		);

 			$chart[ 'legend_rect' ] = array ( 'x'      => 50,
 																				'y'      => 365,
 																				'width'  => 700,
 																				'height' => 8,
 																				'margin' => 6
 																		);

			$chart[ 'draw_text' ] = array (
																			array ( 'color'    => '9e9286',
																							'alpha'    => 75,
																							'font'     => "arial",
																							'rotation' => 0,
																							'bold'     => true,
																							'size'     => 42,
																							'x'        => 50,
																							'y'        => 6,
																							'width'    => 700,
																							'height'   => 50,
																							'text'     => T_('Access summary'),
																							'h_align'  => "right",
																							'v_align'  => "bottom" )
			                        			);

			$chart[ 'chart_bg' ] = array (		'positive_color' => "ffffff",
																				// 'negative_color'  =>  string,
																				'positive_alpha' => 20,
																				// 'negative_alpha'  =>  int
																		);

 			$chart [ 'legend_bg' ] = array (  'bg_color'          =>  "ffffff",
																				'bg_alpha'          =>  20,
																				// 'border_color'      =>  "000000",
																				// 'border_alpha'      =>  100,
																				// 'border_thickness'  =>  1
			                              );

			$chart [ 'legend_label' ] = array(// 'layout'  =>  "horizontal",
			                                  // 'font'    =>  string,
	                                      // 'bold'    =>  boolean,
	                                      'size'    =>  10,
	                                      // 'color'   =>  string,
	                                      // 'alpha'   =>  int
	                                   );

			$chart[ 'chart_border' ] = array ('color'=>"000000",
																				'top_thickness'=>1,
																				'bottom_thickness'=>1,
																				'left_thickness'=>1,
																				'right_thickness'=>1
																		);

 			$chart[ 'chart_type' ] = 'stacked column';

			// $chart[ 'series_color' ] = array ( "4e627c", "c89341" );

			$chart[ 'series_gap' ] = array ( 'set_gap'=>0, 'bar_gap'=>0 );


			$chart[ 'axis_category' ] = array (
																				'font'  =>"arial",
																				'bold'  =>true,
																				'size'  =>11,
																				'color' =>'000000',
																				'alpha' =>75,
																				'orientation' => 'diagonal_up',
																				// 'skip'=>2
																			 );

			$chart[ 'axis_value' ] = array (	// 'font'   =>"arial",
																				// 'bold'   =>true,
																				'size'   => 11,
																				'color'  => '000000',
																				'alpha'  => 75,
																				'steps'  => 4,
																				'prefix' => "",
																				'suffix' => "",
																				'decimals'=> 0,
																				'separator'=> "",
																				'show_min'=> false );

			$chart [ 'chart_value' ] = array (
																				// 'prefix'         =>  string,
																		    // 'suffix'         =>  " views",
																		    // 'decimals'       =>  int,
																		    // 'separator'      =>  string,
																		    'position'       =>  "cursor",
																		    'hide_zero'      =>  true,
																		    // 'as_percentage'  =>  boolean,
																		    'font'           =>  "arial",
																		    'bold'           =>  true,
																		    'size'           =>  20,
																		    'color'          =>  "ffffff",
																		    'alpha'          =>  75
																	 		);

			echo '<div class="center">';
			DrawChart( $chart );
			echo '</div>';
    }


		/*
		 * Table:
		 */
		if( count($res_hits) )
	  {
	  $hits = array();
		$hits['no'] = 0;
		$hits['invalid'] = 0;
		// $hits['badchar'] = 0;			// Not used any longer
		$hits['blacklist'] = 0;
		$hits['rss'] = 0;
		$hits['robot'] = 0;
		$hits['search'] = 0;
		$last_date = 0;
		?>
		<table class="grouped" cellspacing="0">
	    <tr>
	  		<th class="firstcol"><?php echo T_('Date') ?></th>
	  		<th><?php echo T_('Direct Accesses') ?></th>
	  		<th><?php echo T_('Referers') // 'no' ?></th>
	  		<th><?php echo T_('Refering Searches') ?></th>
	  		<th><?php echo T_('Syndication') ?></th>
	  		<th><?php echo T_('Indexing Robots') ?></th>
	  		<th><?php echo T_('Blacklisted') ?></th>
	  		<th><?php echo T_('Total') ?></th>
	    </tr>
			<?php
			$count = 0;
			foreach( $res_hits as $row_stats )
			{
				$this_date = mktime( 0, 0, 0, $row_stats['month'], $row_stats['day'], $row_stats['year'] );
				if( $last_date == 0 ) $last_date = $this_date;	// that'll be the first one
				if( $last_date != $this_date )
				{	// We just hit a new day, let's display the previous one:
					?>
					<tr <?php if( $count%2 == 1 ) echo 'class="odd"'; ?>>
						<td class="firstcol"><?php if( $current_User->check_perm( 'spamblacklist', 'edit' ) )
							{ ?>
								<a href="b2stats.php?action=prune&amp;date=<?php echo $last_date ?>&amp;show=summary&amp;blog=<?php echo $blog ?>" title="<?php echo T_('Prune this date!') ?>"><img src="img/xross.gif" width="13" height="13" class="middle" alt="<?php echo /* TRANS: Abbrev. for Prune (stats) */ T_('Prune') ?>"  title="<?php echo T_('Prune hits for this date!') ?>" /></a>
							<?php
							}
							echo date( locale_datefmt(), $last_date ) ?>
						</td>
						<td class="right"><?php echo $hits['invalid'] ?></td>
						<td class="right"><?php echo $hits['no'] ?></td>
						<td class="right"><?php echo $hits['search'] ?></td>
						<td class="right"><?php echo $hits['rss'] ?></td>
						<td class="right"><?php echo $hits['robot'] ?></td>
						<td class="right"><?php echo $hits['blacklist'] ?></td>
						<td class="right"><?php echo array_sum($hits) ?></td>
					</tr>
					<?php
						$hits['no'] = 0;
						$hits['invalid'] = 0;
						$hits['blacklist'] = 0;
						$hits['rss'] = 0;
						$hits['robot'] = 0;
						$hits['search'] = 0;
						$last_date = $this_date;	// that'll be the next one
						$count ++;
				}
				$hits[$row_stats['hit_ignore']] = $row_stats['hits'];
			}

			if( $last_date != 0 )
			{	// We had a day pending:
				?>
				<tr <?php if( $count%2 == 1 ) echo 'class="odd"'; ?>>
					<td class="firstcol"><?php if( $current_User->check_perm( 'stats', 'edit' ) )
						{ ?>
						<a href="b2stats.php?action=prune&amp;date=<?php echo $this_date ?>&amp;show=summary&amp;blog=<?php echo $blog ?>" title="<?php echo T_('Prune hits for this date!') ?>"><img src="img/xross.gif" width="13" height="13" class="middle" alt="<?php echo /* TRANS: Abbrev. for Prune (stats) */ T_('Prune') ?>"  title="<?php echo T_('Prune hits for this date!') ?>" /></a>
						<?php
						}
						echo date( locale_datefmt(), $this_date ) ?>
					</td>
					<td class="right"><?php echo $hits['invalid'] ?></td>
					<td class="right"><?php echo $hits['no'] ?></td>
					<td class="right"><?php echo $hits['search'] ?></td>
					<td class="right"><?php echo $hits['rss'] ?></td>
					<td class="right"><?php echo $hits['robot'] ?></td>
					<td class="right"><?php echo $hits['blacklist'] ?></td>
					<td class="right"><?php echo array_sum($hits) ?></td>
				</tr>
			<?php } ?>
			</table>
	    <?php
    }
		break;

		case 'referers':
		?>
	<h2><?php echo T_('Last referers') ?>:</h2>
	<p><?php echo T_('These are hits from external web pages refering to this blog') ?>.</p>
	<?php refererList(40,'global',1,1,'no','',$blog);
  if( count( $res_stats ) )
  { ?>
	<table class="grouped" cellspacing="0">
		<?php
		$count = 0;
		foreach( $res_stats as $row_stats ) { ?>
		<tr <?php if( $count%2 == 1 ) echo 'class="odd"'; ?>>
			<td class="firstcol"><?php stats_time() ?></td>
			<td>
				<?php if( $current_User->check_perm( 'stats', 'edit' ) )
					{ ?>
					<a href="b2stats.php?action=delete&amp;hit_ID=<?php stats_hit_ID() ?>&amp;show=referers&amp;blog=<?php echo $blog ?>" title="<?php echo T_('Delete this hit!') ?>"><img src="img/xross.gif" width="13" height="13" class="middle" alt="<?php echo /* TRANS: Abbrev. for Delete (stats) */ T_('Del') ?>" title="<?php echo T_('Delete this hit!') ?>" /></a>
				<a href="b2stats.php?action=changetype&amp;hit_type=search&amp;hit_ID=<?php stats_hit_ID() ?>&amp;show=referers&amp;blog=<?php echo $blog ?>" title="<?php echo T_('Log as a search instead') ?>"><img src="img/magnifier.png" width="14" height="13" class="middle" alt="<?php echo /* TRANS: Abbrev. for "move to searches" (stats) */ T_('-&gt;S') ?>" title="<?php echo T_('Log as a search instead') ?>" /></a>
				<?php } ?>
				<a href="<?php stats_referer() ?>"><?php stats_basedomain() ?></a>
			</td>
			<?php if( $current_User->check_perm( 'spamblacklist', 'edit' ) )
			{ ?>
			<td><a href="b2antispam.php?action=ban&amp;keyword=<?php echo urlencode( stats_basedomain(false) ) ?>" title="<?php echo T_('Ban this domain!') ?>"><img src="img/noicon.gif" class="middle" alt="<?php echo /* TRANS: Abbrev. */ T_('Ban') ?>" title="<?php echo T_('Ban this domain!') ?>" /></a></td>
			<?php } ?>
			<td><?php stats_blog_name() ?></td>
			<td><a href="<?php stats_req_URI() ?>"><?php stats_req_URI() ?></a></td>
		</tr>
		<?php
		$count++;
		} // End stat loop ?>
	</table>
  <?php } ?>

	<h3><?php echo T_('Top referers') ?>:</h3>
	<?php refererList(30,'global',0,0,"'no'",'baseDomain',$blog,true);
  if( count( $res_stats ) )
  {
		$chart [ 'chart_data' ][ 0 ][ 0 ] = "";
		$chart [ 'chart_data' ][ 1 ][ 0 ] = T_('Top referers');

		$count = 0;
		foreach( $res_stats as $row_stats )
		{
			if( $count < 8 )
			{
				$count++;
				$chart [ 'chart_data' ][ 0 ][ $count ] = stats_basedomain( false );
			}
			else
			{
				$chart [ 'chart_data' ][ 0 ][ $count ] = T_('Others');
			}
			$chart [ 'chart_data' ][ 1 ][ $count ] = stats_hit_count( false );
		} // End stat loop

		$chart[ 'canvas_bg' ] = array (		'width'  => 780,
																			'height' => 350,
																			'color'  => 'efede0'
																	);

		$chart[ 'chart_rect' ] = array (	'x'      => 60,
																			'y'      => 50,
																			'width'  => 250,
																			'height' => 250
																	);

		$chart[ 'legend_rect' ] = array ( 'x'      => 400,
																			'y'      => 70,
																			'width'  => 340,
																			'height' => 230,
																			'margin' => 6
																	);

		$chart[ 'draw_text' ] = array (
																		array ( 'color'    => '9e9286',
																						'alpha'    => 75,
																						'font'     => "arial",
																						'rotation' => 0,
																						'bold'     => true,
																						'size'     => 42,
																						'x'        => 50,
																						'y'        => 6,
																						'width'    => 700,
																						'height'   => 50,
																						'text'     => T_('Top referers'),
																						'h_align'  => "right",
																						'v_align'  => "bottom" )
		                        			);

		$chart[ 'chart_bg' ] = array (		'positive_color' => "ffffff",
																			// 'negative_color'  =>  string,
																			'positive_alpha' => 20,
																			// 'negative_alpha'  =>  int
																	);

		$chart [ 'legend_bg' ] = array (  'bg_color'          =>  "ffffff",
																			'bg_alpha'          =>  20,
																			// 'border_color'      =>  "000000",
																			// 'border_alpha'      =>  100,
																			// 'border_thickness'  =>  1
		                              );

		$chart [ 'legend_label' ] = array(// 'layout'  =>  "horizontal",
		                                  // 'font'    =>  string,
                                      // 'bold'    =>  boolean,
                                      'size'    =>  10,
                                      // 'color'   =>  string,
                                      // 'alpha'   =>  int
                                   );

		/*$chart[ 'chart_border' ] = array ('color'=>"000000",
																			'top_thickness'=>1,
																			'bottom_thickness'=>1,
																			'left_thickness'=>1,
																			'right_thickness'=>1
																	);*/

		$chart[ 'chart_type' ] = 'pie';

		// $chart[ 'series_color' ] = array ( "4e627c", "c89341" );

		$chart [ 'series_explode' ] =  array ( 15 );

		/*$chart[ 'axis_category' ] = array (
																			'font'  =>"arial",
																			'bold'  =>true,
																			'size'  =>11,
																			'color' =>'000000',
																			'alpha' =>75,
																			'orientation' => 'diagonal_up',
																			// 'skip'=>2
																		 );*/

		/* $chart[ 'axis_value' ] = array (	// 'font'   =>"arial",
																			// 'bold'   =>true,
																			'size'   => 11,
																			'color'  => '000000',
																			'alpha'  => 75,
																			'steps'  => 4,
																			'prefix' => "",
																			'suffix' => "",
																			'decimals'=> 0,
																			'separator'=> "",
																			'show_min'=> false ); */

		$chart [ 'chart_value' ] = array (
																			// 'prefix'         =>  string,
																	    // 'suffix'         =>  " views",
																	    // 'decimals'       =>  int,
																	    // 'separator'      =>  string,
																	    'position'       =>  "outside",
																	    'hide_zero'      =>  true,
																	    'as_percentage'  =>  true,
																	    'font'           =>  "arial",
																	    'bold'           =>  true,
																	    'size'           =>  20,
																	    'color'          =>  "000000",
																	    'alpha'          =>  75
																 		);

		echo '<div class="center">';
		DrawChart( $chart );
		echo '</div>';

  ?>
	<table class="grouped" cellspacing="0">
		<?php
			$count = 0;
			foreach( $res_stats as $row_stats )
			{ ?>
			<tr <?php if( $count%2 == 1 ) echo 'class="odd"'; ?>>
				<td class="firstcol"><a href="<?php stats_referer() ?>"><?php stats_basedomain() ?></a></td>
				<?php if( $current_User->check_perm( 'spamblacklist', 'edit' ) )
				{ ?>
				<td><a href="b2antispam.php?action=ban&amp;keyword=<?php echo urlencode( stats_basedomain(false) ) ?>" title="<?php echo T_('Ban this domain!') ?>"><img src="img/noicon.gif" class="middle" alt="<?php echo /* TRANS: Abbrev. */ T_('Ban') ?>" title="<?php echo T_('Ban this domain!') ?>" /></a></td>
				<?php } ?>
				<td class="right"><?php stats_hit_count() ?></td>
				<td class="right"><?php stats_hit_percent() ?></td>
			</tr>
			<?php
			$count++;
		} // End stat loop ?>
	</table>
  <?php } ?>
	<p><?php echo T_('Total referers') ?>: <?php stats_total_hit_count() ?></p>

  <?php
		break;

		case 'refsearches':
			?>
	<h2><?php echo T_('Last refering searches') ?>:</h2>
	<p><?php echo T_('These are hits from people who came to this blog system through a search engine. (Search engines must be listed in /conf/_stats.php)') ?></p>
	<?php refererList(20,'global',1,1,"'search'",'',$blog);
  if( count( $res_stats ) )
  { ?>
	<table class="grouped" cellspacing="0">
		<?php
		$count = 0;
		foreach( $res_stats as $row_stats ) { ?>
		<tr <?php if( $count%2 == 1 ) echo 'class="odd"'; ?>>
			<td class="firstcol"><?php stats_time() ?></td>
			<td>
				<?php if( $current_User->check_perm( 'stats', 'edit' ) )
				{ ?>
				<a href="b2stats.php?action=delete&amp;hit_ID=<?php stats_hit_ID() ?>&amp;show=refsearches&amp;blog=<?php echo $blog ?>" title="<?php echo T_('Delete this hit!') ?>"><img src="img/xross.gif" width="13" height="13" class="middle" alt="<?php echo /* TRANS: Abbrev. for Delete (stats) */ T_('Del') ?>" /></a>
				<?php
				}
				stats_basedomain() ?></td>
			<td><a href="<?php stats_referer() ?>"><?php stats_search_keywords() ?></a></td>
			<td><?php stats_blog_name() ?></td>
			<td><a href="<?php stats_req_URI() ?>"><?php stats_req_URI() ?></a></td>
		</tr>
		<?php
		$count++;
		} // End stat loop ?>
	</table>
  <?php } ?>

	<h3><?php echo T_('Top refering search engines') ?>:</h3>
	<?php refererList(20,'global',0,0,"'search'",'baseDomain',$blog,true);
  if( count( $res_stats ) )
  { ?>
	<table class="grouped" cellspacing="0">
		<?php
		$count = 0;
		foreach( $res_stats as $row_stats ) { ?>
			<tr <?php if( $count%2 == 1 ) echo 'class="odd"'; ?>>
				<td class="firstcol"><a href="<?php stats_referer() ?>"><?php stats_basedomain() ?></a></td>
				<td class="right"><?php stats_hit_count() ?></td>
				<td class="right"><?php stats_hit_percent() ?></td>
			</tr>
		<?php
		$count++;
		} // End stat loop ?>
	</table>
  <?php } ?>

	<h3><?php echo T_('Top Indexing Robots') ?>:</h3>
	<p><?php echo T_('These are hits from automated robots like search engines\' indexing robots. (Robots must be listed in /conf/_stats.php)') ?></p>
	<?php refererList(20,'global',0,0,"'robot'",'hit_user_agent',$blog,true,true);
  if( count( $res_stats ) )
  { ?>
	<table class="grouped" cellspacing="0">
		<?php
		$count = 0;
		foreach( $res_stats as $row_stats ) { ?>
			<tr>
				<td class="firstcol"><?php stats_referer('<a href="', '">') ?><?php stats_user_agent( true ) ?><?php stats_referer('', '</a>', false) ?></td>
				<td class="right"><?php stats_hit_count() ?></td>
				<td class="right"><?php stats_hit_percent() ?></td>
			</tr>
		<?php
		$count++;
		} // End stat loop ?>
	</table>
  <?php
  }
		break;

		case 'syndication':
			?>
	<h2><?php echo T_('Top Aggregators') ?>:</h2>
	<p><?php echo T_('These are hits from RSS news aggregators. (Aggregators must be listed in /conf/_stats.php)') ?></p>
	<?php refererList(40, 'global', 0, 0, "'rss'", 'hit_user_agent', $blog, true, true);
  if( count( $res_stats ) )
  { ?>
	<table class="grouped" cellspacing="0">
		<?php
			$count = 0;
			foreach( $res_stats as $row_stats ) { ?>
			<tr <?php if( $count%2 == 1 ) echo 'class="odd"'; ?>>
				<td class="firstcol"><?php stats_user_agent( true ) ?></td>
				<td class="right"><?php stats_hit_count() ?></td>
				<td class="right"><?php stats_hit_percent() ?></td>
			</tr>
		<?php
		$count++;
		} // End stat loop ?>
	</table>
  <?php } ?>
	<p><?php echo T_('Total RSS hits') ?>: <?php stats_total_hit_count() ?></p>

  <?php
		break;

		case 'other':
		?>
	<h2><?php echo T_('Last direct accesses') ?>:</h2>
	<p><?php echo T_('These are hits from people who came to this blog system by direct access (either by typing the URL directly, or using a bookmark. Invalid (too short) referers are also listed here.)') ?></p>
	<?php refererList(10,'global',1,1,"'invalid'",'',$blog);
  if( count( $res_stats ) )
  { ?>
	<table class="grouped" cellspacing="0">
		<?php
		$count = 0;
		foreach( $res_stats as $row_stats ) { ?>
		<tr <?php if( $count%2 == 1 ) echo 'class="odd"'; ?>>
			<td class="firstcol"><?php stats_time() ?></td>
			<?php if( $current_User->check_perm( 'stats', 'edit' ) )
			{ ?>
			<td>
				<a href="b2stats.php?action=delete&amp;hit_ID=<?php stats_hit_ID() ?>&amp;show=other&amp;blog=<?php echo $blog ?>" title="<?php echo T_('Delete this hit!') ?>"><img src="img/xross.gif" width="13" height="13" class="middle" alt="<?php echo /* TRANS: Abbrev. for Delete (stats) */ T_('Del') ?>" /></a>
			</td>
			<?php } ?>
			<td><?php stats_blog_name() ?></td>
			<td><a href="<?php stats_req_URI() ?>"><?php stats_req_URI() ?></a></td>
		</tr>
		<?php
		$count++;
		} // End stat loop ?>
	</table>
  <?php
  }
		break;

		case 'useragents':
			?>
	<h2><?php echo T_('Top User Agents') ?>:</h2>
	<?php refererList(50,'global',0,0,"'no','invalid','badchar','blacklist','search'",'hit_user_agent',$blog,true,true);
  if( count( $res_stats ) )
  { ?>
	<table class="grouped" cellspacing="0">
		<?php
			$count = 0;
			foreach( $res_stats as $row_stats ) { ?>
			<tr <?php if( $count%2 == 1 ) echo 'class="odd"'; ?>>
				<td class="firstcol"><?php stats_user_agent( false ) ?></td>
				<td class="right"><?php stats_hit_count() ?></td>
				<td class="right"><?php stats_hit_percent() ?></td>
			</tr>
		<?php
		$count++;
		} // End stat loop ?>
	</table>
  <?php
  }
		break;
}

require dirname(__FILE__).'/_sub_end.inc.php';

require dirname(__FILE__).'/_footer.php';
?>