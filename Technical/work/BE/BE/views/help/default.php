 <span class='hidden dghidden' style='color: red; '><?echo @$hidden['method_name'];?></span>

<p> View all our tutorials at the <a target='_blank' href='http://playerspectrum.com/'>Spectrum</a> homepage.</p>


<?if(!isset($help_data) || count($help_data)==0):?>
	<p>
		No specific help file found for this screen.   To request one, send a new 
		feature request using the 'Bug or Feature' button.
	</p>
<?else:?>
	
	
	<table class='helpTable' >
	<tr>
	<td width='20%'></td>
	<td width='80%'></td>
	</tr>
	<?//print a row for each button ?>
	<?foreach($help_data as $help):?>
		
		<?$iconCls=$help[$iconIdx];//get the column names we should be using?>
		<?$data   =$help[$dataIdx];?>
		<tr>
			<td>
				<span class='helpIcon <?=$iconCls?>'></span>
			</td>
			<td>
				<span class='helpText'><?=$data?></span>
			</td>
		
		</tr>



	<?endforeach;?>

	</table>




<?endif;?>