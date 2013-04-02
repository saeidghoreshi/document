<div class="remove-tab-padding">
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top"><img src="/assets/images/sidebar_169x465/season.jpg"/></td>
	<td width="100%" valign="top" class="padwrap">
	
		<h1>Season Details</h1>
		
		<form id="frm-season-create">
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
			<td colspan="4"></td>
			<td colspan="6"><center><i>Sample Date Format: May 13, <?=date('Y')?></i></center></td>
		</tr>
		<tr>
			<td colspan="4">
				<div class="form-field">
					<span class="label"><b>Season Name</b></span>
					<div class="input"><input type="text" id="season-name" class="req" name="season[name]" title="Season Name"/></div>
				</div>
			</td>
			<td colspan="3">
				<div class="form-field">
					<span class="label"><b>Open Date</b></span>
					<div class="input"><input type="text" id="season-open" class="req" name="season[open]" title="Season Open Date"/></div>
				</div>
			</td>
			<td colspan="3">
				<div class="form-field">
					<span class="label"><b>Close Date</b></span>
					<div class="input"><input type="text" id="season-close" class="req" name="season[close]" title="Season Close Date"/></div>
				</div>
			</td>
		</tr>
		</table>
		<br/>
		
		<h1>Registration</h1>
		
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
			<td colspan="4">
				<div class="form-field">
					<label for='registration'><input type="checkbox" id="registration" name="registration[online]"/>Enable Online Registration?</label></div>
				</div>
			</td>
			<td colspan="3">
				<div class="form-field">
					<span class="label"><b>Open Date</b></span>
					<div class="input"><input type="text" id="registration-open" class="" name="registration[open]" title="Registration Open Date"/></div>
				</div>
			</td>
			<td colspan="3">
				<div class="form-field">
					<span class="label"><b>Close Date</b></span>
					<div class="input"><input type="text" id="registration-close" class="" name="registration[close]" title="Registration Close Date"/></div>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2"> 
				<div class="form-field">
					<span class="label"><b>Deposit</b></span>
					<div class="input">
						<select id="registration-deposit-collect" class="" name="registration[deposit_collect]" title="Deposit">
							<option value='' selected="selected"></option>
							<? foreach($collections as $row): ?>
							<option value='<?=$row->id?>'><?=$row->status?></option>
							<? endforeach; ?>
						</select>
					</div>
				</div>
			</td>
			<td colspan="3">
				<div class="form-field">
					<span class="label"><b>Deposit Amount</b></span>
					<div class="input"><input type="text" class="currency" id="registration-deposit-amount" class="" name="registration[deposit_amount]" title="Deposit Amount"/></div>
				</div>
			</td>
			<td colspan="2"> 
				<div class="form-field">
					<span class="label"><b>League Fees</b></span>
					<div class="input">
						<select id="registration-league-collect" class="" name="registration[league_collect]" title="League Fees">
							<option value='' selected="selected"></option>
							<? foreach($collections as $row): ?>
							<option value='<?=$row->id?>'><?=$row->status?></option>
							<? endforeach; ?>
						</select>
					</div>
				</div>
			</td>
			<td colspan="3">
				<div class="form-field">
					<span class="label"><b>Total League Fees</b></span>
					<div class="input"><input type="text" class="currency" id="registration-league-amount" name="registration[league_amount]" title="Total League Fees"/></div>
				</div>
			</td>
		</tr>
		</table>
		</form>	
		
	</td>
</tr>
</table>
</div>