<?
function step()
{
	static $i = 1;
	return "step".$i++;
}
?>

<div id="createleague-loading"></div>
<div id="createleague">
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top"><img src="/assets/images/scheduler/girl-fielding-ball.png"/></td>
	<td width="100%" valign="top" class="padwrap">
		<div id=ASS-cl-alert></div>
		<div id="<?=step()?>">
			<h1>Create a New League</h1>
			
			<p>Welcome to the league creation utility. This utility will help you:</p>
			
			<ul>
				<li>Add a new league to your association</li>
				<li>Create a new league website</li>
				<li>Create a user for the league</li>
			</ul>
			
			<p>Before you begin, make sure you have these items ready:</p>
			
			<ul class="checklist">
				<li>The league name</li>

				<li>The desired league url prefix (i.e. myleague.nsalive.com)</li>
				<li>The name, email, birthdate, gender, address, and desired username and password for the default user</li>
				<li>The address to be associated with this league (this can be the same as the above users address)</li>

			</ul>
			
			<center>
			<button id="btn-createleague-continue1">Continue</button>
			</center>
		</div>
		<div class="hidden" id="<?=step()?>">
			<h1>New League</h1>
			
			<p>Please Enter a league name. This name will be used for invoices, reports, and any other identying
			documentation refering to this league. For long league names, consider abbreviating the 'Slopitch League'
			portion of the name into 'SPL' (i.e. Vernon Coed Slopitch League could be Vernon Coed SPL).</p>
			
			<table width="100%">
			<tr>
				<td width="20%"><div class="hidden"><b>Association:</b></div></td>
				<td><div class="hidden" id="ASS-cl-ass-menu"></div></td></tr>
			<tr>
				<td><b>League Name:</b></td>
				<td>
					<div class="form-field">
					    <div class="input">
					        <input type="text" id="cnl-leaguename"  onkeyup="CreateLeague.fillleaguename();"/>
					    </div>
					</div>
				</td>
			</tr>
			</table>
			
			<center>
			<button id="btn-createleague-continue2">Continue</button>
			</center>
			
			<div class="hidden">
			<select id="ASS_cl_ass_menu_list"  multiple> 
			   <?foreach($ass_list as $v):?>
			    <?='<option value="'.$v['org_id'].'">'.$v['org_name'].'</option>' ?>  
			    <?endforeach;?>
			</select>
			</div>
		</div>
		<div class="hidden" id="<?=step()?>">
			<h1>New League Website</h1>
			
			<p>With each new league, a website can be created for that league. Enter a URL prefix to create
			this website, or check 'Do not create a league website' to skip this step.</p>
			
			<table width="100%">
			<tr>
				<td width="20%"><b>Website Prefix:</b></td>
				<td>
					<div  class="form-field">
					    <div class="input">
					        <input type="text" id="cnl-domainname" onkeyup="CreateLeague.filldomainname();" />
					    </div>
					</div>
				</td>
			</tr>
			<tr>
				<td><b>Website Domain:</b></td>
				<td>
					<div id=ASS-btn-domains></div>
				</td>
			</tr>
			<tr>
				<td><b>Good Name?</b></td>
				<td><img id='ASS-cl-accept' src='assets/images/dev/deny.png'/></td>
			</tr>
			</table>
			
			<center><button id="btn-createleague-continue3">Continue</button></center>
			
			<div class="hidden">
			<select id="btn_domains_menu"  multiple> 
			    <option value='nsalive.com'>nsalive.com</option>
			    <option value='nsalive2.com'>nsalive2.com</option>
			</select>
			</div>
			
		</div>
		<div class="hidden" id="<?=step()?>">
			<h1>League User</h1>
				<p>Create a league manager, or search for users to assign, by first and last name.</p>
				
				
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
							<div class="input"><input type="text" id="cnl-fname"/></div>
						</div>
					</td>
					<td colspan="3">
						<div class="form-field">
							<span class="label">Middle and Last Name *</span>
							<div class="input"><input type="text" id="cnl-lname"/></div>
						</div>
					</td>
					<td colspan="5">
						<div class="form-field">
							<span class="label">Address</span>
							<div class="input"><input type="text" id="cnl-address"/></div>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<div class="form-field">
							<span class="label">Email</span>
							<div class="input"><input type="text" id="cnl-email"/></div>
						</div>
					</td>
					<td colspan="3">
						<div class="form-field">
							<span class="label">City</span>
							<div class="input"><input type="text" id="cnl-city"/></div>
						</div>
					</td>
					<td colspan="2"> 
						<div class="form-field">
							<span class="label">Region</span>
							<div class="input">
								<select id="cnl-region">
									<option value=''></option>
									<option value='BC'>BC</option>
									<option value='AB'>AB</option>
									<option value='SK'>SK</option>
			                        <option value='MB'>MB</option>
			                        <option value='ON'>ON</option>
			                        <option value='QC'>QC</option>
			                        <option value='NB'>NB</option>
			                        <option value='NL'>NL</option>
			                        <option value='NT'>NT</option>
			                        <option value='NS'>NS</option>
			                        <option value='NU'>NU</option>
			                        <option value='PE'>PE</option>
			                        <option value='YT'>YT</option>
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
								<select id="cnl-gender">
									<option value=''></option>
									<option value='m'>M</option>
									<option value='f'>F</option>
								</select>
							</div>
						</div>
					</td>
					<td colspan="4">

							<div class="form-field">

                            <span class="label">Birthdate</span>
                            <div id="cnl-birthdate"></div>

						
						</div>
					</td>
					<td colspan="3"> 
						<div class="form-field">
							<span class="label">Country</span>
							<div class="input">
								<select id="cnl-country">
									<option value='' ></option>
									<option value='CA'>Canada</option>
									<option value='USA'>USA</option>
									
								</select>
							</div>
						</div>
					</td>
					<td colspan="2">
						<div class="form-field">
							<span class="label">Postal Code</span>
							<div class="input"><input type="text" id="cnl-postalcode"/></div>
						</div>
					</td>
				</tr>
			    <tr>
			        <td colspan="3">
			            <div class="form-field">
			                <span class="label">Home Phone</span>
			                <div class="input"><input type="text" id="cnl-home"/></div>
			            </div>
			        </td>                        
			        <td colspan="3">
			            <div class="form-field">
			                <span class="label">Mobile Phone</span>
			                <div class="input"><input type="text" id="cnl-mobile"/></div>
			            </div>
			        </td>
			        <td colspan="3">
			            <div class="form-field">
			                <span class="label">Work Phone</span>
			                <div class="input"><input type="text" id="cnl-work"/></div>
			            </div>
			        </td>
			        <td colspan="1">
			            <div class="form-field">
			                <span class="label">Ext</span>
			                <div class="input"><input type="text" id="cnl-ext"/></div>
			            </div>
			        </td>
			    
			    </tr>
			    <tr>
				    <td colspan="4">
						<div class="form-field">
							<span class="label">Login *</span>
							<div class="input"><input type="text" id="cnl-login" />
						</div>					
					</td>
					<td colspan="3">
						<div class="form-field">
							<span class="label">Password *</span>
							<div class="input"> <input type="password" id="cnl-password" /></div>
						</div>
					</td>
					<td colspan="3">
						<div class="form-field">
							<span class="label">Confirm Password *</span>
							<div class="input"><input type="password" id="cnl-confpassword" /></div>
						</div>
					</td>
				</tr>
			    
			    
				<tr>
					<td colspan="10">
						
							<span class="label hidden" id="cnl-create-problem"></span>
							<fieldset class="hidden">
								<legend>This person may already exist.</legend>
								<input type="radio" id="cnl-search-no" name="search" value="no" checked/>
								<label for="search-no">This is a new person not shown below</label><br/>
								<input type="radio" id="cnl-search-yes" name="search" value="yes" />
								<label for="search-yes">I will select an existing person from the table below</label>
							</fieldset>
						
						<div id="cnl-search-hide" class="hidden">
							<div class="datatable"><div id="dt-search-clm"></div></div>
							<div id="dt-pag-search-clm"></div>
			            </div>
					</td>
				</tr>
				</table>
				
				<div class="btnset">
					<div class="btn"><button id="btn-clm-search">Search for Users</button></div>
					<div class="btn"><button id="btn-createleague-continue4">Continue</button></div>
			    </div>
			    



		</div>
		<div class="hidden" id="<?=step()?>">

			<h1>League Address</h1>
						<div class="form-field">
							<span class="label">Address</span>
							<div class="input"><input type="text" id="cnl-lga-address"/></div>
						</div>
						<div class="form-field">
							<span class="label">City</span>
							<div class="input"><input type="text" id="cnl-lga-city"/></div>
						</div>

						<div class="form-field">
							<span class="label">Region</span>
							<div class="input">
								<select id="cnl-lga-region">
									<option value=''></option>
									<option value='BC'>BC</option>
									<option value='AB'>AB</option>
									<option value='SK'>SK</option>
			                        <option value='MB'>MB</option>
			                        <option value='ON'>ON</option>
			                        <option value='QC'>QC</option>
			                        <option value='NB'>NB</option>
			                        <option value='NL'>NL</option>
			                        <option value='NT'>NT</option>
			                        <option value='NS'>NS</option>
			                        <option value='NU'>NU</option>
			                        <option value='PE'>PE</option>
			                        <option value='YT'>YT</option>
								</select>
							</div>
						</div>
						
		
						<div class="form-field">
							<span class="label">Country</span>
							<div class="input">
								<select id="cnl-lga-country">
									<option value=''></option>
									<option value='CA'>Canada</option>
									<option value='USA'>USA</option>
									
								</select>
							</div>
						</div>

						<div class="form-field">
							<span class="label">Postal Code</span>
							<div class="input"><input type="text" id="cnl-lga-postalcode"/></div>
						</div>
		
			<div class='btnset'>
			<div class="btn"><button id="btn-lga-copy">Copy Manager's Address</button></div>
			<div class="btn"><button id="btn-createleague-continue5">Continue</button></div>
			</div>
		</div>                                                            
		<div class="hidden" id="<?=step()?>">
			
			<h1>Summary</h1>
			
			<p>Please confirm the following and click finish to save. You can go back and edit any portion by
			clicking the 'edit' buttons beside any item.</p>
			
			<table width="100%">
			<tr><td colspan="2"><h2>League</h2></td></tr>


			<tr>
				<td width="20%"><b>League Name</b></td>
				<td id='ctr-leaguename'></td>
				<td width="5%"><a href="javascript:CreateLeague._changescreen({from:6,to:2})">Edit</a></td>

			</tr>
			<tr>
				<td width="20%"><b>Website URL</b></td>
				<td id='ctr-leaguewebsiteurl'></td>

				<td width="5%"><a href="javascript:CreateLeague._changescreen({from:6,to:3})">Edit</a></td>
			</tr>
			<tr>
				<td width="20%"><b>League Address</b></td>
				<td id='ctr-leagueaddr'></td>
				<td width="5%"><a href="javascript:CreateLeague._changescreen({from:6,to:4})">Edit</a></td>
			</tr>
			<tr><td colspan="2"><br/><h2>League Manager</h2></td></tr>
			<tr>
				<td width="20%"><b>Name</b></td>
				<td id='ctr-managename'></td>

				<td width="5%"><a href="javascript:CreateLeague._changescreen({from:6,to:5})">Edit</a></td>

			</tr>
			<tr>
			
				<td width="20%"><b>Email</b></td>
				<td id='ctr-manageemail'></td>
				<td width="5%"><a href="javascript:CreateLeague._changescreen({from:6,to:4})">Edit</a></td>

			</tr>
			</table>
			
			<center><button id="ASS-cl-btn-save">Finish</button></center>
			
		</div>
	</td>
</tr>
</table>
</div>