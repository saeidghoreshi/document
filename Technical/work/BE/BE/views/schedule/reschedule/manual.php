<div id="manual-tab">

<div id="init-content">


<div class="btnset">
	<div class="btn" id='div-date-filter'></div>
	<div class="btn" id='div-team-filter'></div>
	
</div>

<div class="datatable">
<div id="dt-manage-games"></div>
</div>

<div class="btnset">
	<div class="btn left" id="dt-pag-manage-games"></div>
	<div class="btn"><button id="btn-manage-rainout">Rainout</button></div>
	<div class="btn"><button id="btn-manage-edit">Reschedule</button></div>
	
</div>

</div>
<div id="rainout-content" class="hidden">
<h3>You are about to set the following game as rained-out</h3>

<span id="rainout-gameinfo"></span>

<br />
<br />






<div class="datatable" id='dt-rainout'></div>




	<fieldset>
	<legend>Rained-out Games will be</legend>
	<input type='radio' name='rain-opt' id="rain-cancel" CHECKED/>Completely Cancelled (these games will no longer appear in any standings or schedule)<br/>
	<input type='radio' name='rain-opt' id="rain-reschedule"/>Rescheduled (each game must be rescheduled seperately)<br/>
	<input type='radio' name='rain-opt' id="rain-tie"/>Scored as a 0-0 Tie.  (A score of zero will be given to each team)<br/>
	<select id="rainout-type">
	This rainout happened to : <option value="only" SELECTED>Only this game</option>
	<option value="datevenue">Games on this day and Diamond</option>
	<option value="date">All games on this day</option>	
	</select>
	</fieldset>
	
	<div class="btn"><button id="btn-rainout-cancel">Cancel</button></div>
	<div class="btn"><button id="btn-rainout-cont">Continue</button></div>
</div>



</div>
