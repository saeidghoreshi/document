<div id='st-sel-tab'>

<h1>Statistics for '<span id='current-rank-name'></span>'</h1>

<table width="100%" cellpadding="1" cellspacing="0">
<tr>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
</tr>

<tr>
	<td colspan='2'></td>
	<td colspan="2">
		<div class="form-field">
			<span class="label">Points per Win</span>
			<div class="input"><input type="text" id="pts-win" value="2"/></div>
		</div>
	</td>
	<td colspan="2">
		<div class="form-field">
			<span class="label">Points per Loss</span>
			<div class="input"><input type="text" id="pts-loss" value="0"/></div>
		</div>
	</td>
	<td colspan="2">
		<div class="form-field">
			<span class="label">Points per Tie</span>
			<div class="input"><input type="text" id="pts-tie" value="1"/></div>
		</div>
	</td>
	<td colspan='2'></td>
</tr>
<tr>
	<td colspan='10'>
	
		<fieldset>
		<legend><span class="label">Select which statistics will be used for the team ranking, and in what order of preference. </span> </legend>
	
		<div class='left'> 
		<select id='stat-options'>
		<?foreach($stats as $s):		?>
			<option value=<?=$s['stat_id']?>><?=$s['stat_name']?></option>		
		<?endforeach;?>
		</select>
		</div>

		<input type='radio' name='type_hth' id='use_global' checked/>
		<img src= '/assets/images/world.png' />
		Regular
		<input type='radio' name='type_hth' id='use_hth'/>
		<img src= '/assets/images/shape_align_bottom.png' />
		Head-to-Head 
		<button id='btn-rank-add'>Add</button>
		</fieldset>
	</td>

</tr>
<tr>
	<td colspan='10'>

		<div class="datatable">
			<div id="dt-statistics-rank"></div>
		</div>

	</td>
</tr>
</table>


</div>

