<div id="games-tab">

<input type='hidden' value='-1' id='home_id'>
<input type='hidden' value='-1' id='away_id'>


<div id="games-input">
<h1>Create Games</h1>
<table width="100%">
		<tr>
			<td width="10%"></td><td width="10%"></td><td width="10%"></td><td width="10%"></td>
			<td width="10%"></td><td width="10%"></td><td width="10%"></td>
			<td width="10%"></td><td width="10%"></td><td width="10%"></td>
		</tr>    
		<tr>
			<td colspan='1'>
				<div class='btn'><button  id="btn-games-pickdate">Date</button> </div>
			</td>
			<td colspan='2'>
				<span id='current-date'>None</span>
			</td>
			<td colspan='4'><span class='label'>Start Time</span>
				<span class="btn">
                <select id="mnl-start-hr">
					<option value='1'>1</option>
					<option value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
					<option value='5'>5</option>
					<option value='6'>6</option>
					<option  SELECTED value='7'>7</option>
					<option value='8'>8</option>
					<option value='9'>9</option>
					<option value='10'>10</option>
					<option value='11'>11</option>
					<option value='12'>12</option>
				</select></span>
				<b> : </b>
				<span class='btn'>
				<select id="mnl-start-min">
					<option  SELECTED value='00'>00</option>
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
				</select>     </span>    
				<span class='btn'>    
                <select id="mnl-start-ampm">
                    <option SELECTED value="pm">pm</option>
                    <option value="am">am</option>
                </select>
                </span>
			</td>
			<td colspan='3'>
				<span class='label'>Game Length</span>
				<span class="btn">
                <select id="mnl-length-hr">
					<option SELECTED value='1'>1</option>
					<option  value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
				</select></span>
				<b> : </b>
				<span class='btn'>
				<select id="mnl-length-min">
					<option  SELECTED value='00'>00</option>
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
				</select>     </span>    
			</td>
		</tr>
		<tr> 
			<td colspan='3'>
				<div  id='mnl-homediv-dd'></div>
			</td>
			<td colspan='3'>
				<div id='mnl-awaydiv-dd'></div>
			</td>
			<td colspan='4'>
				<div id='mnl-fac-dd'></div>
			</td>			
		</tr>
		<tr>
			<td colspan='3'>
				<div id='mnl-ht-contain'></div>
			</td>
			<td colspan='3'>
				<div id='mnl-at-contain'></div>
			</td>		
			<td colspan='4'>
				<div id='mnl-ven-contain'></div>
			</td>
		</tr>

		</table>

		
  
<div class="btnset">

	<div class="left btn"> 
		<b>Status:</b> 
		<span id="games-status">Ready</span>
	</div>  
	<div class="btn"><button  id="btn-games-create">Create Game</button></div>

</div>
		
		
</div>



<div id="games-cal" class="hidden" >




	<h2 >Pick a date</h2>
	<div id="div-calendar"></div>




</div>



<div id='games-table'>

	<div class="datatable" id="dt-allgames"></div>

	<div class="btnset" >
		<div class="btn left"><button disabled="disabled" id="btn-view-del">Delete Game</button></div>

		<div class="btn" id="dt-pag-allgames"></div>

	</div>


</div>

</div>