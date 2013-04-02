<div id='rules-tab' class="remove-tab-padding">
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td width="100%" valign="top" class="padwrap">
	
	<div id='rules-global-content'>
		<h1><span class='label'>Global Rules</span></h1>
		<br/>
		<table id='global-table'>
		<tr>
			<th width="290" align="left" >
				Number of games played per team
			</th>

			<th width="25">#</th>

		</tr>
		<tr>
			<td>
				Minimum # Games
			</td>
			<td>
		 		<input type="text" maxlength="2"style="width:29px"id="g-all-min-perteam" value='0'/>
			
			</td>
		</tr>
		<tr>
			<td>
				Maximum # Games
			</td>
			<td>
		 		<input type='text' maxlength="2"style="width:29px"id="g-all-max-perteam" />
			
			</td>	
		</tr>	
		</table>
	</div>
	<div id='rules-date-content' class='hidden'>
		<h1><span class='label'>Date List Rules</span></h1>
		
		<table id='set-table' >
		<tr>
			<td colspan='4' align="right">
				<div class='btnset'>
					<div class='btn left'>
						<span class='label hidden'>For Dates in:</span>
					</div>
					<div class='btn left' id='rules-contain-dd'></div>
					<div class='btn'>
						<button id='btn-rules-copy'>Copy to all Dates</button>
					<div>
				</div>
			</td>
		</tr>
		
		<tr>
			<th width="250" align="left">Rule</th>
			<th width="25">H</th>
			<th width="5">:</th>
			<th width="25">MM</th>
		</tr>
		<tr>
			<td>Game Length</td>
			<td>
				<select id="gamelength-hr">
					<option value='0'>0</option>
					<option SELECTED value='1'>1</option>
					<option value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
				</select>
			</td>
			<td>
				 <b> : </b> 
			</td>
			<td>
				<select id="gamelength-min">
					<option SELECTED value='00'>00</option>
					<option value='05'>05</option>
					<option value='10'>10</option>
					<option value='15'>15</option>
					<option value='20'>20</option>
					<option value='25'>25</option>
					<option value='30'>30</option>
					<option value='35'>35</option>
					<option value='40'>40</option>
					<option value='45'>45</option>
					<option value='50'>50</option>
					<option value='55'>55</option>
				</select>
			</td>
		
			
		</tr>
		<tr>
			<td>Pre-Game Setup Time</td>
			<td>
				<select id="warmup-hr">
					<option SELECTED value='0'>0</option>
					<option value='1'>1</option>
					<option value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
				</select>
			</td>
			<td>
				 <b> : </b> 
			</td>
			<td>
				<select id="warmup-min">
					<option SELECTED value='00'>00</option>
					<option value='05'>05</option>
					<option value='10'>10</option>
					<option value='15'>15</option>
					<option value='20'>20</option>
					<option value='25'>25</option>
					<option value='30'>30</option>
					<option value='35'>35</option>
					<option value='40'>40</option>
					<option value='45'>45</option>
					<option value='50'>50</option>
					<option value='55'>55</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Post-Game Teardown Time</td>
			<td>
				<select id="cooldown-hr">
					<option SELECTED value='0'>0</option>
					<option value='1'>1</option>
					<option value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
				</select>
			</td>
			<td>
				 <b> : </b> 
			</td>
			<td>
				<select id="cooldown-min">
					<option SELECTED value='00'>00</option>
					<option value='05'>05</option>
					<option value='10'>10</option>
					<option value='15'>15</option>
					<option value='20'>20</option>
					<option value='25'>25</option>
					<option value='30'>30</option>
					<option value='35'>35</option>
					<option value='40'>40</option>
					<option value='45'>45</option>
					<option value='50'>50</option>
					<option value='55'>55</option>
				</select>
			</td>			
		</tr>
		<tr>
			<td>Min Time Between</td>
			<td>
				<select id="min-btw-hr">
					<option SELECTED value='0'>0</option>
					<option value='1'>1</option>
					<option value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
				</select>
			</td>
			<td>
				 <b> : </b> 
			</td>
			<td>
				<select id="min-btw-min">
					<option SELECTED value='00'>00</option>
					<option value='05'>05</option>
					<option value='10'>10</option>
					<option value='15'>15</option>
					<option value='20'>20</option>
					<option value='25'>25</option>
					<option value='30'>30</option>
					<option value='35'>35</option>
					<option value='40'>40</option>
					<option value='45'>45</option>
					<option value='50'>50</option>
					<option value='55'>55</option>
				</select>
			</td>
		
		</tr>
		<tr>
			<td>Max Time Between</td>
			<td>
				<select id="max-btw-hr">
					<option SELECTED value='0'>0</option>
					<option value='1'>1</option>
					<option value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
				</select>
			</td>
			<td>
				 <b> : </b> 
			</td>
			<td>
				<select id="max-btw-min">
					<option SELECTED value='00'>00</option>
					<option value='05'>05</option>
					<option value='10'>10</option>
					<option value='15'>15</option>
					<option value='20'>20</option>
					<option value='25'>25</option>
					<option value='30'>30</option>
					<option value='35'>35</option>
					<option value='40'>40</option>
					<option value='45'>45</option>
					<option value='50'>50</option>
					<option value='55'>55</option>
				</select>
			</td>
		
		</tr>
		</table>
	
	</div>
	<div id='rules-div-content' class='hidden'>
		<h1>
		<span class='label'>Division Rules for Dates in: <span id='rules-current-dateset'></span></span>
		</h1>
		<table>
		<tr>
			<td colspan='4' align="right">
				<span class='left label hidden'>For Division:</span>
				<div id='rules-div-contain'></div>
				<input type='hidden' value='-1' id='rules-division-id'/>
			</td>
		</tr>
		<tr>
			<th width="250" align="left">Number of games played</th>
			<td width="25"></td>
			<td width="5"></td>
			<th width="25">#</th>
		</tr>
		<tr>
			<td><span>Minimum # Per Day </span></td>
			<td></td>
			<td></td>
			<td><input type="text" maxlength="2"style="width:29px"id="min-perteam" value='0'/></td>
		</tr>
		<tr>
			<td><span>Maximum # Per Day</span></td>
			<td></td>
			<td></td>
			<td><input type='text' maxlength="2"style="width:29px"id="max-perteam" /></td>
		</tr>
		<tr>
			<td><span>Minimum # in List </span></td>
			<td></td>
			<td></td>
			<td><input type="text" maxlength="2"style="width:29px"id="min-set" value='0'/></td>
		</tr>
		<tr>
			<td><span>Maximum # in List</span></td>
			<td></td>
			<td></td>
			<td><input type='text' maxlength="2"style="width:29px"id="max-set" /></td>
		</tr>
		<tr>
			<td colspan='4' align="right">
				<div class='btn'><button id='btn-rules-copydiv'>Copy to all Divisions</button></div>		
			</td>
		</tr>
		</table>
	
	</div>
	<div id='rules-conflict-content' class='hidden'>
	
	
	<h1>Conflict Report</h1>
	
	
	<div id='dt-rules-conflicts' class='datatable'></div>
	<div id='dt-rules-conflicts-pag'></div>
	
	</div>
		
		
		

					
		
	
	
	
	
	
	
	
	
	
	
	
	
	
	</td>
</tr>
</table>
</div>
