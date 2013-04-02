<?php
/*
TODO: all filters work except for venue and then show all.should be remade to use session data instead. made filtered games null?.>? also disable 
date filter when venue or team is active 
for example, hitting team filter then date filter, and now we cant show all

and possibly add radio buttons simliar to elsewhere, radio => show one
also use innerhtml for txt-sch-date
*/
?>
<script language='javascript'>

</script> 

<!-- -------hidden fields for selected dropdown item---- -->
<input type="hidden" id="s-filter-id" value="" />
<input type="hidden" id="s-filter-name" value="" />
<input type="hidden" id="s-filter-type" value="" />

<input type="hidden" id="timer-label" value="" />
<input type="hidden" id="timer-ms" value="" />
<input type="hidden" id="timer-mnt" value="" />
<input type="hidden" id="timer-sec" value="" />



<div id="schedule-tab">
<div id='schedule-tab-status'>

	<h1>Status:</h1>

	<table width='100%' >
	<tr>
		<td width='5%'></td><td width='5%'></td>
		
		<td width='10%'></td><td width='10%'></td>
		<td width='10%'></td><td width='10%'></td>
		<td width='10%'></td><td width='10%'></td>
		<td width='10%'></td><td width='10%'></td>
		
		<td width='5%'></td><td width='5%'></td>			
	</tr>
	<tr>	
		<td colspan='1'>
			<img src= '/assets/images/date/time.png'   />
		</td>
		
		<td style='padding-bottom:5px' colspan='4'>
			<span class='label'>Step Name</span>
		</td>
		<td colspan='5'>
			<span class='label'>Result Name</span>
		</td>
		<td colspan='1'>
			<span class='label'>#</span>
		</td>
		
		<td colspan='1'>
			<img src= '/assets/images/help.png'   />
		</td>
		
	</tr>
	<tr>
		<td colspan='1'>
			<span id='span-games-timer'></span>
		</td>
		<td style='padding-bottom:5px' colspan='4'>
			
				Creating Matches

		</td>
		<td colspan='5'>
			<span id='label-games-valid' class='hidden'>
				Number of Games Created
			</span>
			<span id='label-games-error' class='hidden'>
				Number of games ignored
			</span>
		</td>
		<td colspan='1'>
			<span id="span-games-data"></span>
		</td>
		<td colspan='1'>
			<span id='span-games-progress' class='hidden'>
				<img src= '/assets/images/ajax-loader.gif'   />
			</span>
			<span id='span-games-complete' class='hidden'>
				<img src= '/assets/images/accept.png'   />
			</span>
			<span id='span-games-warning'     class='hidden'>
				<img src= '/assets/images/error.png'     />
			</span>
			<span id='span-games-fail'     class='hidden'>
				<img src= '/assets/images/delete.png'     />
			</span>
		</td>	
	</tr>
	<tr>
		<td colspan='1'>
			<span id='span-balance-timer'></span>
		</td>
		<td style='padding-bottom:5px'colspan='4'>
			Balancing home away
		</td>
		<td colspan='5'>
			<span id='label-balance-valid' class='hidden'>
				Number of imbalanced teams
			</span>
			<span id='label-balance-error' class='hidden'>
				Largest Imbalance
			</span>
		</td>
		<td colspan='1'>
			<span id="span-balance-data"></span>
		</td>
		<td colspan='1'>
			<span id='span-balance-progress' class='hidden'>
				<img src= '/assets/images/ajax-loader.gif'   />
			</span>
			<span id='span-balance-complete' class='hidden'>
				<img src= '/assets/images/accept.png'   />
			</span>
			<span id='span-balance-warning'     class='hidden'>
				<img src= '/assets/images/error.png'     />
			</span>
			<span id='span-balance-fail'     class='hidden'>
				<img src= '/assets/images/delete.png'     />
			</span>
		</td>	
	</tr>
	<tr>
		<td colspan='1'>
			<span id='span-timeslots-timer'></span>
		</td>
		<td style='padding-bottom:5px' colspan='4'>
			Creating Timeslots
		</td>
		<td colspan='5'>
			<span id='label-timeslots-valid' class='hidden'>
				Number of timeslots created
			</span>
			<span id='label-timeslots-error' class='hidden'>
				Minimum number of extra timeslots needed
			</span>
		</td>
		<td colspan='1'>
			<span id="span-timeslots-data"></span>
		</td>
		<td colspan='1'>
			<span id='span-timeslots-progress' class='hidden'>
				<img src= '/assets/images/ajax-loader.gif'   />
			</span>
			<span id='span-timeslots-complete' class='hidden'>
				<img src= '/assets/images/accept.png'   />
			</span>
			<span id='span-timeslots-warning'     class='hidden'>
				<img src= '/assets/images/error.png'     />
			</span>
			<span id='span-timeslots-fail'     class='hidden'>
				<img src= '/assets/images/delete.png'     />
			</span>
		</td>	
	</tr>
	<tr>
		<td colspan='1'>
			<span id='span-assign-timer'></span>
		</td>
		<td style='padding-bottom:5px' colspan='4'>
			Assigning timeslots to games
		</td>
		<td colspan='5'>
			<span id='label-assign-valid' class='hidden'>
				Number of games assigned
			</span>
			<span id='label-assign-error' class='hidden'>
				Number of unscheduled games
			</span>
		</td>
		<td colspan='1'>
			<span id="span-assign-data"></span>
		</td>
		<td colspan='1'>
			<span id='span-assign-progress' class='hidden'>
				<img src= '/assets/images/ajax-loader.gif'   />
			</span>
			<span id='span-assign-complete' class='hidden'>
				<img src= '/assets/images/accept.png'   />
			</span>
			<span id='span-assign-warning'     class='hidden'>
				<img src= '/assets/images/error.png'     />
			</span>
			<span id='span-assign-fail'     class='hidden'>
				<img src= '/assets/images/delete.png'     />
			</span>
		</td>	
	</tr>
	<tr>
		<td colspan='1'>
			<span id='span-rules-timer'></span>
		</td>
		<td style='padding-bottom:5px' colspan='4'>
					
				Checking rules
			
		</td>
		<td colspan='5'>
			<span id='label-rules-valid' class='hidden'>	
				Games Added
			</span>			
			<span id='label-rules-error' class='hidden'>
				Rules Violated
			</span>
		</td>
		<td colspan='1'>
			<span id="span-rules-data"></span>
		</td>
		<td colspan='1'>
			<span id='span-rules-progress' class='hidden'>
				<img src= '/assets/images/ajax-loader.gif'   />
			</span>
			<span id='span-rules-complete' class='hidden'>
				<img src= '/assets/images/accept.png'   />
			</span>
			<span id='span-rules-warning'     class='hidden'>
				<img src= '/assets/images/error.png'     />
			</span>
			<span id='span-rules-fail'     class='hidden'>
				<img src= '/assets/images/delete.png'     />
			</span>
		</td>	
	</tr>
		
	</table>
	<br />
	<div class='btnset'>
		
		<div class='btn left'>
			<button id='btn-s-reset'>Start Over</button>
		</div>
		
		<div class='btn'>
			<button id='btn-s-step'>Next Step</button>	
		</div>
		<div class='btn'>
			<button id="btn-s-allsteps"> Create Schedule</button>
		</div>
			
	</div>
	

</div>
<div id='schedule-tab-view' class='hidden'>

	
	<div id="abovebuttons" class="hidden btnset">

	    <div class="btn" id="ven-filter"  >
	        <div id="m-ven-filter"></div>  
	    </div>   
	    
	    <div class="btn" id="team-filter"  >
	        <div id="m-team-filter"></div>  
	    </div>   
	    
	    

		<div class="btn"> <input type="button" id="btn-sch-filter" value="Show Filters" /> </div>

		<div class="btn"> <input type="button" id="btn-sch-bydate" value="Date Filter" /> </div>


	    <div  class="btn" ><input type="button" id="btn-sch-prev" value="<"/></div>

	    <div class="btn">  
    		<input id="txt-sch-date" type="text" disabled="disabled" value="All"  style="text-align:center"  />
	    </div>
	    
	    <div  class="btn" ><input type="button" id="btn-sch-next" value=">" /> </div>



	</div>



	<div class="datatable">

	    <div id="dt-sch"></div>
	</div>


	<div id="belowbuttons" class="btnset">

	   <div id="dt-pag-sch" class="btn" style="float:left" ></div> 
	        
	    <div id="sch-cl" class="btn hidden" ><input type="button" id="btn-sch-clear" value="Show All"/></div>

	    
	    <div class="btn"  >
	        <button  id="btn-sch-publish" >Publish</button>
	    </div>    
	    <div class="seperator"> </div>
	    <div class="btn"  >
	        <button  id="btn-sch-save" >Save</button>
	    </div>

	</div>


				<div  id="current-label" class="hidden"><!-- un hide if error exists TODO: make spans with messages, not txt -->
				<p>
				Error Messages:  
				<span id="s-error"   ></span>
				</p>
				</div>

</div>















</div>