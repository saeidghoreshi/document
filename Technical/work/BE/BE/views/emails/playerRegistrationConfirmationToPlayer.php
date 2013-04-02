
<? $pLoginUrl = "{$reginfo['regurl']}/index.php/registration/playerLogin"; ?>

<p><br/><br/><b><?=$info['fname']?> <?=$info['lname']?></b>;</p>

<p>Thank you for your player registration for team <b><?=$reginfo['team']?></b> in the <b><?=$reginfo['league']?></b>. This is a copy
of your registration. Please note that this player registration confirmation is not a payment confirmation email, nor receipt of payment.
If a payment was required and you successfully completed a payment, a payment receipt will be sent to you separate from this email.</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="2">Registration Details</th></tr>
			<tr>
				<td><b>Registration Time</b></td>
				<td><?=$reginfo['time']?></td>
			</tr>
			<tr>
				<td><b>Registred to Team</b></td>
				<td><?=$reginfo['team']?></td>
			</tr>
			<tr>
				<td><b>Team in League</b></td>
				<td><?=$reginfo['league']?></td>
			</tr>
			<tr>
				<td><b>Team in Season</b></td>
				<td><?=$reginfo['season']?></td>
			</tr>
			<tr>
				<td><b>Team in Division</b></td>
				<td><?=$reginfo['division']?></td>
			</tr>
			<tr>
				<td><b>League Website</b></td>
				<td><?=$reginfo['regurl']?></td>
			</tr>
			<tr>
				<td><b>League in Association</b></td>
				<td><?=$reginfo['assoc']?></td>
			</tr>
		</table>
	</td>
</tr>
</table>

<p>This is your personal information as you entered it.</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="4">Player Registration</th></tr>
			<tr>
				<td width="15%"><b>First Name</b></td>
				<td width="35%"><?=$info['fname']?></td>
				<td width="15%"><b>Last Name</b></td>
				<td width="35%"><?=$info['lname']?></td>
			</tr>
			<tr>
				<td><b>Gender</b></td>
				<td><?=($info['gender']=="M")?"Male":"Female"?></td>
				<td><b>Birthdate</b></td>
				<td><?=$info['birthdate']?></td>
			</tr>
			<tr>
				<td><b>Phone 1</b></td>
				<td><?=$info['phone1']?></td>
				<td><b>Phone 2</b></td>
				<td><?=$info['phone2']?></td>
			</tr>
			<tr>
				<td><b>Email</b></td>
				<td colspan="3"><?=$info['email']?></td>
			</tr>
			<tr>
				<td><b>Address</b></td>
				<td colspan="3"><?=$info['address']['total']?></td>
			</tr>
			<tr>
				<td><b>City</b></td>
				<td><?=$info['address']['city']?></td>
				<td><b>Region</b></td>
				<td><?=$info['address']['region_name']?></td>
			</tr>
			<tr>
				<td><b>Country</b></td>
				<td><?=$info['address']['country_name']?></td>
				<td><b>Code</b></td>
				<td><?=$info['address']['postal_name']?></td>
			</tr>
		</table>
	</td>
</tr>

</table>


<p><b>Login Url:</b> <a href="<?=$pLoginUrl?>"><?=$pLoginUrl?></a></p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="4">Login Information</th></tr>
			<tr>
				<td width="15%"><b>Username</b></td>
				<td width="35%"><?=$user['username']?></td>
				<td width="15%"><b>Password</b></td>
				<td width="35%"><?=$user['password']?></td>
			</tr>
		</table>
	</td>
</tr>
</table>

<? if(isset($customfields['team']) or isset($customfields['league'])): ?>

<p>This information was requested by your team and/or league. We have sent this information along with your registration to their respective requesters.</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">

			<? if(isset($customfields['team'])): ?>
			<tr><th colspan="2">Team Requested Information</th></tr>
			<? foreach($customfields['team'] as $cf):?>
			<tr>
				<td width="35%"><b><?=$cf['field_title']?></b></td>
				<td width="65%"><?=$cf['field_value']?></td>
			</tr>
			<? endforeach; ?>
			<? endif; ?>

			<? if(isset($customfields['league'])): ?>
			<tr><th colspan="2">League Requested Information</th></tr>
			<? foreach($customfields['league'] as $cf):?>
			<tr>
				<td width="35%"><b><?=$cf['field_title']?></b></td>
				<td width="65%"><?=$cf['fieldvalue']?></td>
			</tr>
			<? endforeach; ?>
			<? endif; ?>
			
		</table>
	</td>
</tr>
</table>

<? endif; ?>