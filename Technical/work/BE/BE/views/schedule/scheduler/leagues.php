<div class="remove-tab-padding">
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top"><img src="/assets/images/scheduler/girl-fielding-ball.png"/></td>
	<td width="100%" valign="top" class="padwrap">
		
		<div id="leagues-tab">
			<div id="league-question" class="hidden"> 
				<h1>Leagues</h1>
				
				<p>There are two types of league schedules:</p>
				<ul>
					<li>Every game will include 2 teams from the same league.</li>
					<li>A Game may include 2 teams from the same league, or one team from 2 different leagues.</li>
				</ul>
				
				<p>How many leagues will your schedule include?</p>
				
				<center>
				    <div class="btn">
				        <button type="button" id="btn-league-single" title="we do not play other leagues">Only One League</button>
				        <button type="button" id="btn-league-mult" title="we will play some games with another league">Several Leagues</button>
				    </div>
				</center>
			</div>
			
			<div class="datatable">
			    <div id="dt-leagues"></div>
			</div>

			<div class="btnset">
			    <div class="btn" style="float:left" id="dt-pag-leagues"></div>
			    <div id="league-all-input" class="btn hidden" align="center">
			        <input type="button" id="btn-league-all" value="Select All"/>
			    </div>
			    <div id="league-none-input" class="btn hidden" align="center">
			        <input type="button" id="btn-league-none" value="Deselect All"/>
			    </div>
			</div>

		</div>
		
	</td>
</tr>
</table>
</div>