<div class="remove-tab-padding">
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top"><img class='hidden' src="/assets/images/sidebar_169x465/security.jpg"/></td>
	<td width="100%" valign="top" class="padwrap">
		<div id="create-tab">
		<h1>Personal Information</h1>
		
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
					<div class="input"><input type="text" id="user-fname"/></div>
				</div>
			</td>
			<td colspan="3">
				<div class="form-field">
					<span class="label">Middle and Last Name *</span>
					<div class="input"><input type="text" id="user-lname"/></div>
				</div>
			</td>
			<td colspan="5">
				<div class="form-field">
					<span class="label">Address</span>
					<div class="input"><input type="text" id="user-address"/></div>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="5">
				<div class="form-field">
					<span class="label">Email</span>
					<div class="input"><input type="text" id="user-email"/></div>
				</div>
			</td>
			<td colspan="3">
				<div class="form-field">
					<span class="label">City</span>
					<div class="input"><input type="text" id="user-city"/></div>
				</div>
			</td>
			<td colspan="2"> 
				<div class="form-field">
					<span class="label">Region</span>
					<div class="input">
						<select id="user-region">
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
						<select id="user-gender">
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
					<div class="input"><input type="text" id="user-birthdate"/></div>
				</div>
			</td>
			<td colspan="3"> 
				<div class="form-field">
					<span class="label">Country</span>
					<div class="input">
						<select id="user-country">
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
					<div class="input"><input type="text" id="user-code"/></div>
				</div>
			</td>
		</tr>
        <tr>
            <td colspan="3">
                <div class="form-field">
                    <span class="label">Home Phone</span>
                    <div class="input"><input type="text" id="phone-home"/></div>
                </div>
            </td>                        
            <td colspan="3">
                <div class="form-field">
                    <span class="label">Mobile Phone</span>
                    <div class="input"><input type="text" id="phone-mobile"/></div>
                </div>
            </td>
            <td colspan="3">
                <div class="form-field">
                    <span class="label">Work Phone</span>
                    <div class="input"><input type="text" id="phone-work"/></div>
                </div>
            </td>
            <td colspan="1">
                <div class="form-field">
                    <span class="label">Ext</span>
                    <div class="input"><input type="text" id="phone-ext"/></div>
                </div>
            </td>
        
        </tr>
		<tr>
			<td colspan="10">
				<div id="search-hide" class="hidden">
					<span class="label hidden" id="create-problem"></span>
					<fieldset>
						<legend>This person may already exist.</legend>
						<input type="radio" id="search-no" name="search" value="no" checked/>
						<label for="search-no">This is a new person not shown below</label><br/>
						<input type="radio" id="search-yes" name="search" value="yes" />
						<label for="search-yes">I will select an existing person from the table below</label>
					</fieldset>
				

				    <div class="datatable"><div id="dt-search"></div></div>
				    <div id="dt-pag-search"></div>
                </div>
			</td>
		</tr>
		</table>
		</div>

	</td>
</tr>
</table>
</div>

