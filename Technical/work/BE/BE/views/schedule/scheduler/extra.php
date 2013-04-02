
<input type='hidden' id='home-div' value='-1' />
<input type='hidden' id='away-div' value='-1'/>

<div class="remove-tab-padding">
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top">    </td>
	<td width="100%" valign="top" class="padwrap">		                
		<div id="extra-tab">	
		
		<div id='extra-games'>
		
		<table width="100%">
		<tr>
			<td width="10%"></td><td width="10%"></td><td width="10%"></td><td width="10%"></td>
			<td width="10%"></td><td width="10%"></td><td width="10%"></td>
			<td width="10%"></td><td width="10%"></td><td width="10%"></td>
		</tr>    
		<tr>
			<td colspan='1'>
				<div class='btn'><button  id="btn-sg-pickdate">Date</button> </div>
			</td>
			<td colspan='2'>
				<span id='span-sg-date'>No Date</span>
			</td>
			<td colspan='4'><span class='label'>Start Time</span>
				<span class="btn">
                <select id="sg-start-hr">
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
				<select id="sg-start-min">
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
                <select id="sg-start-ampm">
                    <option SELECTED value="pm">pm</option>
                    <option value="am">am</option>
                </select>
                </span>
			</td>
			<td colspan='3'>
				<span class='label'>Game Length</span>
				<span class="btn">
                <select id="sg-length-hr">
					<option SELECTED value='1'>1</option>
					<option  value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
				</select></span>
				<b> : </b>
				<span class='btn'>
				<select id="sg-length-min">
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
				<div  id='s-homediv-dd'></div>
			</td>
			<td colspan='3'>
				<div id='s-awaydiv-dd'></div>
			</td>
			<td colspan='4'>
				<div id='s-fac-dd'></div>
			</td>			
		</tr>
		<tr>
			<td colspan='3'>
				<div id='s-ht-contain'></div>
			</td>
			<td colspan='3'>
				<div id='s-at-contain'></div>
			</td>		
			<td colspan='4'>
				<div id='s-ven-contain'></div>
			</td>
		</tr>
		<tr>
			<td colspan='10'>
			<div class='datatable'><div id='dt-sgames'></div></div>
			
			</td>
		</tr>
		<tr>
		<td colspan='10'>
		<div class='btnset'>
			<div class='btn' id='dt-sgames-pag'></div>
			<div class='btn'><button id='btn-sg-del'>Delete</button></div>
			<div class='btn'><button id='btn-sg-add'>Create Game</button></div>
		</div>
		</td>
		</tr>
		
		</table>

		</div>
		<div id='extra-cal' class='hidden'>
		
		<h2 >Pick a date</h2>
			<div id="div-sg-cal"></div>
		</div>
		
		

		
		
		
		
		
		
		
</div></td></tr></table></div>