<?php
  //reschedule this game
?>

<div id="edit-tab">
<div id="edit-main-content">


    <div class="datatable"><div id="dt-edit-game"></div></div>
    
    

    

    <div class="btnset" id="edit-nav">
    	<div class="btn"><button id="btn-edit-ts">Find a timeslot in this schedule</button></div>
        <div class="btn"><button id="btn-edit-swap">Swap with another game </button></div>
        <div class="btn"><button id="btn-edit-enter">Enter a new date and time</button></div>
    </div>
	<hr />

</div>
<div id="ts-content" class="hidden">

	<span id='ts-none' class="hidden">No timeslots exist</span>
	
	
	<div class='datatable' id="dt-viewts"></div>
	
	<div class='btnset'>
	<div class="btn" id="dt-viewts-pag"></div>
	<div class='btn left' style="float:left;"><button id="btn-save-ts">Assign Timeslot</button></div>
	</div>
</div>

<div id="swap-content" class="hidden">
 


    <div class="datatable"><div id="dt-allgames"></div></div>
    
	<div class='btnset'>
		<div class='btn' id="dt-pag-allgames"></div>

		<div class="btn"style="float:left;"><button id="btn-swap-resc"> Swap Games </button></div>
	</div>


</div>
<div id="edit-content" class="hidden">

<fieldset>
<legend>Enter new Date and Time</legend>
Game Time  <select id="new-time-hr">
<option value='1'>1</option>
<option value='2'>2</option>
<option value='3'>3</option>
<option value='4'>4</option>
<option value='5'>5</option>
<option value='6'>6</option>
<option value='7'>7</option>
<option value='8'>8</option>
<option value='9'>9</option>
<option value='10'>10</option>
<option value='11'>11</option>
<option value='12'>12</option>
</select> <b> : </b> <select id="new-time-min">
<option value='00'>00</option>
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
<input id="new-am" name="st-ampm" type="radio"> am
<input id="new-pm" name="st-ampm" type="radio" CHECKED> pm
<br />


Game ends at <select id="end-time-hr">
<option value='1'>1</option>
<option value='2'>2</option>
<option value='3'>3</option>
<option value='4'>4</option>
<option value='5'>5</option>
<option value='6'>6</option>
<option value='7'>7</option>
<option value='8'>8</option>
<option value='9'>9</option>
<option value='10'>10</option>
<option value='11'>11</option>
<option value='12'>12</option>
</select> <b> : </b> <select id="end-time-min">
<option value='00'>00</option>
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
<input id="end-am" name="et-ampm" type="radio"> am
<input id="end-pm" name="et-ampm" type="radio" CHECKED> pm


<br />
Game Date <input id="new-date" type="text" />
<br />

Select Venue: <div id="new-ven-contain"><div id="btn-new-ven"></div></div>
<br />
<button id="btn-resc-newslot">Save Changes</button> 
* <em>We will check for conflicts with existing games</em>
</fieldset>
</div>
<span class="hidden" id="upd-waiting">Saving...<img src="/assets/images/ajax-loader.gif" /></span>
<span class="hidden" id="upd-success">Update Success!</span>
<span class="hidden" id="upd-conflict">Time conflict with existing game.</span>
<span class="hidden" id="upd-problem">Permissions or Database error.</span>

</div>



