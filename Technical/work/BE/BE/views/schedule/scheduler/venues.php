
<!-- -------hidden fields for selected dropdown item---- -->
<input type="hidden" id="filter-id"     value="" />
<input type="hidden" id="filter-name"   value="" />
<input type="hidden" id="filter-type"   value="" />
<input type="hidden" id="venue-current" value="" />
<input type="hidden" id="v-temp-rank"   value="0" />

<input type="hidden" id="v-prompt-text" value="Enter a descriptive name for this set of dates.  (For example, 'July Weekdays', or 'Mini-Weekend')."/>
<div class="remove-tab-padding">
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top"></td>
	<td width="100%" valign="top" class="padwrap">

		<div id="venues-info" class='hidden'>
			<h1>Select Venues</h1>
			
			<p>
			Before we can create a schedule, we need to insert the diamonds that you have available to use.
			Often, this will be the contract that your municipality has issued to you. If you do not have
			any issued diamonds, you may enter dates and diamonds that you wish to use.
			</p>
			
			<p>Before you continue, please have the following ready:</p>
			
			<ul class="checklist">
				<li>A list of dates that will be booked, or several seperate lists.</li>
				<li>The dates that you wish to use, or have been issued.</li>
				<li>The Diamonds that you wish to use, or have been issued.</li>
				<li>The times that each venue has been booked, on each day.</li>
			</ul>
			<p>Each list of dates can be assigned a different group of venues, as well of a ranking for 
			which venue is most desirable in that date list.</p>
			<br/>
			<p>When you are ready, click "Save & Continue" from the bottom right of this window.</p>
		</div>		
		<div id='venues-content-lists'>
		<h1>Manage Lists of Dates</h1> 
		
			<div class='btnset'>
	            <div id="venue-rs" class="btn" >
                	<button id="btn-venue-dateset">Create New List</button>
	            </div>
            </div>
            
            <div class='datatable'>
            	<div class='left btn' id='dt-datelists-pag'></div>
                <div id='dt-datelists'></div>
            </div>
            
            
            <div class='btnset'>
	            
	            <div class='btn left'>
	                <button id='btn-datelist-name' disabled="disabled">Change Name</button>
	            </div>
	            <div class='btn'>
	                <button id='btn-datelist-edit' disabled="disabled">Manage Timeslots</button>
	            </div>
            
            </div>
		
		</div>	
		<div id="venues-content-times" class="hidden" >
			<h1>Timeslots For <span id='current-dateset-times'>-1</span></h1>
			

			
			<fieldset>
			<legend>Timeslot Data</legend>

			<table width='100%'>
			<tr>
				<td align='right'>
					<span class='label'>Name:</span>
				</td>
				<td>
					<input type='text' id='txt-timeslot-name'/>
				</td>
			</tr>	
			<tr>
				<td align='right'>
					<span class='label'>From:</span>
				</td>
				<td>
            		<select id="start-hr">
						<option value='1'>1</option>
						<option value='2'>2</option>
						<option value='3'>3</option>
						<option value='4'>4</option>
						<option value='5'>5</option>
						<option SELECTED value='6'>6</option>
						<option value='7'>7</option>
						<option value='8'>8</option>
						<option value='9'>9</option>
						<option value='10'>10</option>
						<option value='11'>11</option>
						<option value='12'>12</option>
					</select> 
					<b> : </b> 
					<select id="start-min">
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
	                <select id="start-ampm">
		                <option SELECTED value="pm">pm</option>
		                <option value="am">am</option>
	                </select>

				</td>
			</tr>
			<tr>
				<td align='right'>
                	<span class='label'>To:</span>				
				</td>
				<td>
	                <select id="end-hr">
						<option value='1'>1</option>
						<option value='2'>2</option>
						<option value='3'>3</option>
						<option value='4'>4</option>
						<option value='5'>5</option>
						<option value='6'>6</option>
						<option value='7'>7</option>
						<option value='8'>8</option>
						<option value='9' SELECTED>9</option>
						<option value='10'>10</option>
						<option value='11'>11</option>
						<option value='12'>12</option>
					</select> 
					<b> : </b> 
					<select id="end-min">
						<option value='00' SELECTED>00</option>
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
	                <select id="end-ampm">
	                    <option value="pm" SELECTED>pm</option>
	                    <option value="am">am</option>
	                </select>
				</td>
				</tr>
				<tr>
					<td>
					</td>
					<td>
						<button id='btn-create-timeslot'>Create Timeslot</button>
					</td>
			</tr>

			</table>		
			
			</fieldset>      

                
            <br />
                

			<div class='datatable'>
				<div class='left btn' id='dt-timeslots-pag'></div>
                <div id='dt-timeslots'></div>
            </div>
            <div class='btnset'>
	            
	            <div class='btn left'>
	                <button id='btn-timeslots-name' disabled="disabled">Change Name</button>
	            </div>
	            <div class='btn'>
	                <button id='btn-timeslots-back'>Save and Return</button>
	            </div>
	            <div class='btn'>
	                <button id='btn-timeslots-edit-dates' disabled="disabled" >Edit Dates</button>
	            </div>
	            <div class='btn'>
	                <button id='btn-timeslots-edit-fields' disabled="disabled" >Edit Fields</button>
	            </div>
            </div>
            
            
		</div>			
		<div id="venues-content-calendars" class="hidden"  >
		      
            <div class='btnset'>
				<div class='left btn'>
					<h1>Dates For: <span id='current-dateset-select'>-1</span>:<span id='current-timeslot'></span> </h1>   
				</div>
				
                <div class='btn'>
                	<span class='label'>From: </span>
                	<span id='span-ds-from'></span>
                </div>
                <div class='btn'>
                	<span class='label'>To: </span>
                	<span id='span-ds-to'></span>
                </div>                 
                   	            	                
            </div>
            
            
			
			<div id='v-cal-loading-msg'>
				<br/>
				<br/>
				<hr />
				<span class='label'>Calendar loading, please wait...</span> 
				<img src='/assets/images/ajax-loader.gif' />
				<hr />
			</div>
			<div id='v-cal-contain' class='hidden'>
       			<div style='border:none;' id="multi-cal"></div>
       			
       			<div class='btnset'>
       				
       			  <div class='btn'>
		                <button id='btn-dates-back' >Cancel</button>
		            </div>
       				<div class='btn'>
		                <button id='btn-dates-save-back' >Save & Return</button>
		            </div>
		            <div class='btn'>
		                <button id='btn-dates-save-venues'>Save & Assign Venues</button>
		            </div>
       			
       			
       			</div>
       			
       			
       		</div>
       		
       		
		</div>
		
		<div id="venues-content" class="hidden" >
			<h1>Assignments For: <span id='current-dateset-assign'>-1</span></h1>
			
			<div id="filters" class="btnset">
				<div class='btn' id='div-facility-filter'></div>
				<div class='btn'>
					<span class='label'>From: </span>
					<span id='span-vn-from'></span>
				</div>
                <div class='btn'>
            		<span class='label'>To: </span>
            		<span id='span-vn-to'></span>
            	</div>
			
				<div class='btn' >
					<input type='checkbox' id='venues-random'/> 
				</div>	
 
			</div>
			<div class='datatable'<? //style="margin-top:5px;margin-bottom:5px;outline:0;width:auto;"?>>
				<div id="dt-venues"></div>
			</div>
			
			<div class='btnset'>
       			
       			<div class='btn'>
       				<div id='dt-venues-pag'></div>
       			</div>	
       				
       			<div class='btn'>
		            <button id='btn-venues-back'>Cancel</button>
		        </div>
       			<div class='btn'>
		            <button id='btn-venues-save-back'>Save & Return</button>
		        </div>
		        <div class='btn'>
		            <button id='btn-venues-save-dates'>Save & Assign Dates</button>
		        </div>
       		
       		
       		</div>
			

		</div>
        <div id="venues-audit" class="hidden">
        	<div class=''><h1>Review</h1></div>  
        	<div class='btnset'>
			    
			   <div class='btn hidden' id='dt-filter-contain'>
					<div id='btn-dt-filter'></div>
				</div>
			   <div class='btn'>
            		<div id="vfil-buttongroup" class="yui-buttongroup">
            		    <input type='radio' name='vfil-yuiradio' id='btn-vfil-date' value='Filter by Date'/>
            		    <input type='radio' name='vfil-yuiradio' id='btn-vfil-all'  value='Show all' CHECKED/>
						<input type='radio' name='vfil-yuiradio' id='btn-vfil-venue'value='Filter by Venue'/>	
            		</div>
               </div>
               <div class='btn hidden' id='va-filter-contain'>
					<div id='btn-va-filter'></div>
				</div>
            </div>
            
            <div  style="margin-top:5px;margin-bottom:5px;outline:0;width:auto;">
            	<div id="dt-vaudit"></div>
            </div>

        </div>
		
	</td>
    

</tr>

</table>
</div>
