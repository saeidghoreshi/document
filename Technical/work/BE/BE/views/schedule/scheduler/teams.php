<div class="remove-tab-padding">
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td width="100%" valign="top" class="padwrap">
		
        <div id="teams-info" class='hidden'>
            <h1>Team Exceptions</h1>
            
            <p>
                <b>Are there any teams that cannot play on specific dates?</b><br/>
                All dates selected with the venues will be considered for scheduled games, 
                but some teams may have more restricted availability than others.
            </p>
            
            <center>
                <button type="button" id="btn-tm-no" title="" class="default">No</button>
                <button type="button" id="btn-tm-yes" title="">Yes</button>
            </center>
        </div>
		<div id="teams-content">
		<h1>Teams</h1>

		<div class='btnset'>
			<div id="team-contain" class="left btn"> <div id="team-dropdown"></div></div>
			<div id="add-input"  class="left btn"><input type="button" id="btn-tm-add" value="Create Exception"/></div>
			<div id='team-buttongroup' class='btn yui-buttongroup'>
				<input type='radio' name='team-yuiradio' id='btn-teams-radio-all' 
							value='All teams' CHECKED />	
				<input type='radio' name='team-yuiradio' id='btn-teams-radio-filter' 
							value='Teams with Exceptions'/>	
			</div>
		
		</div>
		<h1>Exceptions</h1>
		<p>No games will be scheduled for the selected team during the following time periods.</p>        
		<!--
		<div class="btnset"> 
			<div class="btn"> <input type="button" id="btn-tm-cal" value="Show Calendar"/></div>
		    <div class="btn"><input type="text" id="txt-tm-start"  value="" disabled="disabled"/></div>
		    <div class="btn"><input type="button" id="btn-tm-start" value="Start Date"/></div>
		    <div class="btn"><input type="text" id="txt-tm-end"  value="" disabled="disabled"/></div>
		    <div class="btn"><input type="button" id="btn-tm-end" value="End Date"/></div>                          
		    
		</div>
-->
		<div class="datatable">
		    <div id="dt-td"></div>
		</div>

		<div class="btnset"> 
		
		    <div style="float:left" class="btn" id="dt-pag-td"></div>
		    <div id="cnf-input" class="btn hidden" ><input type="button" id="btn-tm-savedates" value="Save New Dates"/></div>
            
		    <div id="view-input" class="btn hidden" ><input type="button" id="btn-tm-delete" value="Delete Row"/></div>
		    
		</div>

		
		<!--
        <div id="tm-cal" ></div>
		            <div id="tm-all" align="center" >
		                <input type="radio" name="restriction" id="tm-allday" checked="checked"/> All Day
		                <input type="radio" name="restriction" id="tm-between" /> Between:
		            </div>
		           
		             <div id="start-input" class="input">
		             <input type="text" maxlength="2" id="tm-start-time" size="15" onfocus=" this.value=''" value="12"/>
		             </div>

		             <select name="tm-start-style" id="tm-start-style">
		             <option value="am">am</option>
		             <option value="pm">pm</option>
		             </select>


		        <td width="8%" align="center" >
		            and 
		        </td>
		        <td width="15%">
		              <div id="end-input" class="input"><input type="text" maxlength="2" id="tm-end-time" size="15" onfocus=" this.value=''" value="12"/></div>
		        </td>
		        <td align="left">
		             <select name="tm-end-style" id="tm-end-style">
		             <option value="am">am</option>
		             <option value="pm">pm</option>
		             </select>
		        </td>
		        -->
		</div>
		<div id='teams-create-ex' class='hidden'>
			
			<input type='hidden' id='current-team-id'  value='-1'>
			
			<fieldset><legend><span class='label'>New Exception</span></legend>
			<table width="100%">
		    <tr>
				<td width="10%"></td><td width="10%"></td><td width="10%"></td><td width="10%"></td>
				<td width="10%"></td><td width="10%"></td><td width="10%"></td>
				<td width="10%"></td><td width="10%"></td><td width="10%"></td>
			</tr>    
			<tr>
				<td colspan='2'><span class='right label'>Team Name:</span></td>
				<td colspan='8'><span id='current-team-name'></span></td>
			</tr>
			<tr> 
				<td colspan='2'><span class='right label'>Description: </span></td>
				<td colspan='8'><div class='form-field input'> <input type='text' id='new-td-desc' />   </div></td>			
			</tr>
			<tr> 
				<td colspan='2'><div class='right label'><button id='btn-new-td-start'>Start Date: </button></div></td>
				<td colspan='4'> <span id='new-td-start'> </span>   </td>
				
			</tr>			
			<tr>
				<td colspan='2'><div class='right label'><button class='right label' id='btn-new-td-end'>End Date: </button></div></td>
				<td colspan='4'> <span id='new-td-end'> </span>   </td>		
			</tr>
			<tr>
				<td colspan='2'>
					<span class='right label'>Time: </span>
				</td>
				<td colspan='4'>
						
					<div id='teamnew-buttongroup' class='btn yui-buttongroup hidden'>
						<input type='radio' name='teamnew-yuiradio' id='btn-teams-newall' 
									value='All Day'  />	
						<input type='radio' name='teamnew-yuiradio' id='btn-teams-newbtw' 
									value='Between:' CHECKED />	
					</div>

				<div id='div-new-start'>
				<select id="new-start-hr">
					<option value='1'>1</option>
					<option value='2'>2</option>
					<option value='3'>3</option>
					<option  SELECTED value='4'>4</option>
					<option value='5'>5</option>
					<option value='6'>6</option>
					<option value='7'>7</option>
					<option value='8'>8</option>
					<option value='9'>9</option>
					<option value='10'>10</option>
					<option value='11'>11</option>
					<option value='12'>12</option>
				</select> 
				<b> : </b> 
				<select id="new-start-min">
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
				</select>                
                <select id="new-start-ampm">
                    <option SELECTED value="pm">pm</option>
                    <option value="am">am</option>
                </select>
				</div>
				</td>
				<td colspan='4'>
				<div id='div-new-end'>
				<select id="new-end-hr">
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
				</select> <b> : </b> <select id="new-end-min">
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
				</select>                
                <select id="new-end-ampm">
                    <option SELECTED value="pm">pm</option>
                    <option value="am">am</option>
                </select>
				</div>
				</td>
			
			</tr>
			
    		</table>
			</fieldset>
		</div>
		<div id='teams-calendar' class='hidden'>
		
		
			<div id='div-teams-cal'></div>
		
		
		
		
		</div>
	</td>
</tr>
</table>
</div>    
    
    
