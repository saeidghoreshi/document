<title>Spectrum | Schedule Preview</title>
<?$hdr = null;?>
<table width='100%' border='<?=$border?>'>
	<?foreach($games as $g): if($g['timeslot']<0) continue;?>
	<?if(!$hdr || $hdr != $g[$group_by])://if the next item has a different value than current header
		//then spit out new header
		$hdr = $g[$group_by];
	?>
	<tr>
		<th colspan='6'><?=$hdr?></th>
	</tr>
	<tr>
		<td >Home</td>
		<td >Away</td>
		<td >Venue</td>
		<td >Date</td>
		<td >Time</td>

	</tr>
	<?endif;//otherwise keep printing games?>
	<tr>
 
		<td><?=$g['home_name']?></td>
		<td><?=$g['away_name']?></td>
		<td><?=$g['venue_name']?></td>
		<td><?=$g['game_date']?></td>
		<td><?=$g['display_start_time']?></td>
	
	
	</tr>
	<?endforeach;?>



</table>
