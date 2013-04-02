<div id="audit-tab">


<div class="btnset" id="audit-type">


	<div class="btn">
		<button id="btn-audit-team" >Team</button>
	</div>

	<div class="btn">
		<button id="btn-audit-venue" >Venue</button>
	</div>
	<div class="btn">
		<button id="btn-audit-date" >Date</button>
	</div>

	<div class="btn">
		<button id="btn-audit-join" >Team-Venue</button>
	</div>

	<div class="btn">
		<button id="btn-audit-td" >Team-Date</button>
	</div>
	
	<div class="btn">
		<button id="btn-audit-team-match" >Team-Matchups</button>
	</div>

</div>
<div id="team-content" class="hidden" >


	<h1>Number of home and away games played by each team.</h1>


	<div class="datatable">
	<div id="dt-audit" ></div>
	<div id="dt-pag-audit"></div>
	</div>

	
</div>
<div id="venue-content" class="hidden" >


	<h1>Number of games played at each location.</h1>

	<div class="datatable">
	<div id="dt-v-stats" ></div>
	<div id="dt-pag-vstats"></div>
	</div>

	
	
</div>
<div id="date-content" class="hidden" >

	<h1>Number of games played on each date.</h1>
	<div id="dt-date-stats" ></div>
	<div id="dt-pag-date-stats"></div>

	
</div>
<div id="join-content" class='hidden'>

	<h1>Number of games played by each team at each location.</h1>
	<div id="dt-join" ></div>
	<div id="dt-pag-join"></div>
	
	
	
</div>
<div id='td-join-content' class='hidden'>


	<h1>Number of games played by each team on each date.</h1>
	<div id="dt-td-join" ></div>
	<div id="dt-pag-td-join"></div>	


</div>
<div id='team-match-content' class='hidden'>

	<h1>Number of games played between any two teams.</h1>
	<div id="dt-match-join" ></div>
	<div id="dt-pag-match-join"></div>		
	

</div>


<br />

		<div class='hidden'><button id="btn-audit-windows" >DEBUG CSV and HTML NEW WINDOWS</button></div>

</div>

<div id='conflict-report' class='hidden'>

	<h1>Conflict Reports</h1>
	
		<button id="btn-conflict-games" class='hidden' >Games Report</button>

	<div id='dt-conflict-report' class='datatable'></div>
	<div id='dt-conflict-report-pag'></div>

</div>