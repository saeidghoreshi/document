<div id='new-team-info'>

<h1>Create New Team Wizard</h1>
<ul class="checklist">
	<li>Welcome to the Create Team Wizard.  To create teams, please have the name of each team ready.</li>
	<li>You can create a user account for the team manager right here for each team.  If the manager is already in our system, we will search existing users for you.</li>
	<li>If you do know know the team manager, this can be created and assigned later in the 'Manage Users' window.</li>
	<li>After you are done here, do not forget to make sure that all teams register for the current season, are assigned to the correct division, 
	and have been assigned a Team Roster.</li>
	<li>If you are adding yourself, or someone who is already logged in, as a team manager, they may have to log out and log in again in order to see this new 
	team in their Active Organization dropdown menu button.</li>
	<li>When you are ready to proceed, click the button below.</li>
</ul>

<div align='center'><button id='btn-team-ready'>I am Ready</button></div>


</div>
<div id='new-team-content' class='hidden'>


<table width="100%" cellpadding="1" cellspacing="0">
<tr>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
</tr>
<tr>

<td colspan='1'>
<span class="label">Team Name:  </span>
</td> 
<td colspan='9'>
<div  class="input"><input type="text" id="League-ct-name" value="My Team Name"  /></div>
</td>
</tr>

</table>


<div id="manager-info" class="hidden">
<table width="100%" cellpadding="1" cellspacing="0">
<tr>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
	<td width="10%"></td>
</tr>
<tr>
<td colspan="2">
				<div class="form-field">
					<span class="label">First Name *</span>
					<div class="input"><input type="text" id="man-fname"/></div>
				</div>
			</td>
			<td colspan="3">
				<div class="form-field">
					<span class="label">Middle and Last Name *</span>
					<div class="input"><input type="text" id="man-lname"/></div>
				</div>
			</td>
			<td colspan="5">
				<div class="form-field">
					<span class="label">Address</span>
					<div class="input"><input type="text" id="man-address"/></div>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="5">
				<div class="form-field">
					<span class="label">Email</span>
					<div class="input"><input type="text" id="man-email"/></div>
				</div>
			</td>
			<td colspan="3">
				<div class="form-field">
					<span class="label">City</span>
					<div class="input"><input type="text" id="man-city"/></div>
				</div>
			</td>
			<td colspan="2"> 
				<div class="form-field">
					<span class="label">Region</span>
					<div class="input">
						<select id="man-region">
							<option value='none' selected="selected"></option>
							<option value='1'>BC</option>
							<option value='2'>AB</option>
							<option value='3'>SK</option>
                            <option value='4'>MB</option>
                            <option value='5'>ON</option>
                            <option value='6'>QC</option>
                            <option value='7'>NB</option>
                            <option value='8'>NL</option>
                            <option value='9'>NT</option>
                            <option value='10'>NS</option>
                            <option value='11'>NU</option>
                            <option value='12'>PE</option>
                            <option value='13'>YT</option>
						</select>
					</div>
				</div>
			</td>
		</tr>
		<tr>	
			<td colspan="1"> 
				<div class="form-field">
					<span class="label">Gender</span>
					<div class="input">
						<select id="man-gender">
							<option value='none' selected="selected"></option>
							<option value='m'>M</option>
							<option value='f'>F</option>
						</select>
					</div>
				</div>
			</td>
			<td colspan="4">
				<div class="form-field">
					<span class="label">Birthdate</span>
					<div class="input"><input type="text" id="man-birthdate"/></div>
				</div>
			</td>
			<td colspan="3"> 
				<div class="form-field">
					<span class="label">Country</span>
					<div class="input">
						<select id="man-country">
							<option value='none' selected="selected"></option>
							<option value='1'>Canada</option>
							<option value='2'>USA</option>
							
						</select>
					</div>
				</div>
			</td>
			<td colspan="2">
				<div class="form-field">
					<span class="label">Postal Code</span>
					<div class="input"><input type="text" id="man-code"/></div>
				</div>
			</td>
		</tr>
        <tr>
            <td colspan="3">
                <div class="form-field">
                    <span class="label">Home Phone</span>
                    <div class="input"><input type="text" id="man-home"/></div>
                </div>
            </td>                        
            <td colspan="3">
                <div class="form-field">
                    <span class="label">Mobile Phone</span>
                    <div class="input"><input type="text" id="man-mobile"/></div>
                </div>
            </td>
            <td colspan="3">
                <div class="form-field">
                    <span class="label">Work Phone</span>
                    <div class="input"><input type="text" id="man-work"/></div>
                </div>
            </td>
            <td colspan="1">
                <div class="form-field">
                    <span class="label">Ext</span>
                    <div class="input"><input type="text" id="man-ext"/></div>
                </div>
            </td>
        
        </tr>
        <tr>
				    <td colspan="4">
						<div class="form-field">
							<span class="label">Login *</span>
							<div class="input"><input type="text" id="man-login" />
						</div>					
					</td>
					<td colspan="3">
						<div class="form-field">
							<span class="label">Password *</span>
							<div class="input"> <input type="password" id="man-password" /></div>
						</div>
					</td>
					<td colspan="3">
						<div class="form-field">
							<span class="label">Confirm Password *</span>
							<div class="input"><input type="password" id="man-confirm" /></div>
						</div>
					</td>
				</tr>
		<tr>
		<td colspan="10">
				<div id="manradio-hide" class="hidden">
					<span class="label hidden" id="create-problem"></span>
					<fieldset>
						<legend>Are you creating a new person in the system?</legend>
						<input type="radio" id="manradio-no" name="search" value="no" checked/>
						<label for="manradio-no">This is a new person not shown below</label><br/>
						<input type="radio" id="manradio-yes" name="search" value="yes" />
						<label for="manradio-yes">I will select an existing person from the table below</label>
					</fieldset>
				
                </div>
			</td>
		</tr>
		<tr>
			<td colspan="10">
				    <div class="datatable"><div id="dt-mansearch"></div></div>
				    <div id="dt-pag-mansearch"></div>
			</td>
		</tr>
</table>

</div>



</div>