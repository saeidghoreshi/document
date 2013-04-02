
<div   id='welcome-tab' width="100%">

	
	<div id='welcome-intro' width="100%">
		<h1  >Welcome    </h1> 
		
		<p>Welcome to the Spectrum Scheduler. Before you get started, please ensure that you have read the tutorials located at
		<a href="http://www.playerspectrum.com/">http://www.playerspectrum.com/</a>. Use the same username and password provided
		to you for Spectrum to login and read the tutorials.</p>
		
		<p>Please ensure you have completed the following before continuing:</p>
		
		<ul class="checklist">
	        <li>Have you added your venues?</li>
	        <li>Have your created the season you wish to schedule for?</li>
	        <li>Have you added all of your teams, and placed them in their divisions?</li>
    	</ul>
	
	</div>
	<div id='welcome-name' class='hidden'>
	
	
	 <span  class='label' >Give this schedule a name:   </span>
	 	<input type="text" id="s-name" maxlength="30" value="New Schedule"/> 
	 

	
	
	
	</div>
	<div id='welcome-progress' class='hidden'>
	
		<table class='left' width='100%'>
		<tr>
		<td width='10%' align='center'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>		
		</tr>
		
		<tr>
			<td colspan='1'>
				<?//is complete or not flag => image checkboxes in this col?>
			</td>

			
			<td colspan='5'>Step Name</td>
			<td colspan='4'>Selected Info</td>
	
		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-name-complete' class='hidden'>
					<img src= '/assets/images/accept.png'   />
				</span>
				<span id='setup-name-todo'>
					<img src= '/assets/images/add.png'   />
				</span>
			</td>
			<td colspan='5'>
				<span class='label'>Schedule Name</span>
			</td>
			<td colspan='4'>
				<span id='setup-name-data'></span>
			</td>
		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-seasons-complete' class='hidden'>
					<img src= '/assets/images/accept.png'   />
				</span>
				<span id='setup-seasons-todo'>
					<img src= '/assets/images/add.png'   />
				</span>
			</td>
			<td colspan='5'>
				<span class='label'>Select a Season</span>
			</td>
			<td colspan='4'>
				<span id='setup-seasons-data'></span>
			</td>

		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-venues-complete' class='hidden'>
					<img src= '/assets/images/accept.png'   />
				</span>
				<span id='setup-venues-todo'>
					<img src= '/assets/images/add.png'   />
				</span>
			</td>
			<td colspan='5'>
				<span class='label'>Venues and Contracts</span>
			</td>
			<td colspan='4'>
				<span id='setup-venues-data'>0</span> Venue<span id='s-v-plural'>s</span> Booked
			</td>
	
		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-rules-complete' class='hidden'>
					<img src= '/assets/images/accept.png'   />
				</span>
				<span id='setup-rules-todo'>
					<img src= '/assets/images/add.png'   />
				</span>
			</td>
			<td colspan='5'>
				<span class='label'>Schedule Rules</span>
			</td>
			<td colspan='4'>
				<span id='setup-rules-data'></span>
			</td>

		
		</tr>

		<tr>
			<td colspan='1' align='center'>
				<span id='setup-divisions-complete' class='hidden'>
					<img src= '/assets/images/accept.png'   />
				</span>
				<span id='setup-divisions-todo'>
					<img src= '/assets/images/add.png'   />
				</span>
			</td>
			<td colspan='5'>
				<span class='label'>Select participating Divisions</span>
			</td>
			<td colspan='4'>
				<span id='setup-divisions-data'>0</span> Division<span id='s-d-plural'>s</span> Selected
			</td>
				
		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-matches-complete' class='hidden'>
					<img src= '/assets/images/accept.png'   />
				</span>
				<span id='setup-matches-todo'>
					<img src= '/assets/images/add.png'   />
				</span>
			</td>
			<td colspan='5'>
				<span class='label'>Create matches between divisions</span>
			</td>
			<td colspan='4'>
				<span id='setup-matches-data'>0</span> Match<span id='s-m-plural'>es</span> Created
			</td>
	
		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-teamex-complete' class='hidden'>
					<img src= '/assets/images/accept.png'   />
				</span>
				<span id='setup-teamex-todo'     class='hidden'>
					<img src= '/assets/images/add.png'   />
				</span>
			</td>
			<td colspan='5'>
				<span class='label'>Team Date Exceptions (Optional)</span>
			</td>
			<td colspan='4'>
				<span id='setup-teamex-data'></span>
			</td>

		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-extra-complete' class='hidden'>
					<img src= '/assets/images/accept.png'   />
				</span>
				<span id='setup-extra-todo'     class='hidden'>
					<img src= '/assets/images/add.png'   />
				</span>
			</td>
			<td colspan='5'>
				<span class='label'>Extra Games (Optional)</span>
			</td>
			<td colspan='4'>
				<span id='setup-extra-data'></span>
			</td>

		</tr>
		<tr>
			<td colspan='1' align='center'>
				
			</td>
			<td colspan='3'>
				<span class='label'>Create Schedule</span>
			</td>
			<td colspan='3'>
				
			</td>
			<td colspan='3'>

			</td>
		
		
		</tr>
		<tr>
		
		<td colspan='10' align='center'>
		
		</td>
		
		
		</tr>
		
		
		</table>
		
		

		
	
	
	</div>
	
	
	
	
</div>
