
<? $pLoginUrl = "{$reginfo['regurl']}/index.php/registration/playerLogin"; ?>

<? $names = array(); foreach($manager as $v) $names[] = $v['name']; $names = implode(", ",$names); ?>

<p><br/><br/>Attention: <b><?=$names?></b>;</p>

<p>The following player has registered for your team '<b><?=$reginfo['team']?></b>'.</p>

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

<p>Here is additional information you requested.</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="2">Registration Details</th></tr>
			<tr>
				<td><b>Registration Time</b></td>
				<td><?=$reginfo['time']?></td>
			</tr>
			
			<? if(isset($customfields['team'])): ?>
			<tr><th colspan="2">Team Requested Information</th></tr>
			<? foreach($customfields['team'] as $cf):?>
			<tr>
				<td width="35%"><b><?=$cf['field_title']?></b></td>
				<td width="65%"><?=$cf['field_value']?></td>
			</tr>
			<? endforeach; ?>
			<? endif; ?>
		</table>
	</td>
</tr>
</table>

<? if(isset($customfields['league'])): ?>

<p>This information was requested by the <b><?=$reginfo['league']?></b>. We have sent this information along with the player registration to the league.</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">

			<tr><th colspan="2">League Requested Information</th></tr>
			<? foreach($customfields['league'] as $cf):?>
			<tr>
				<td width="35%"><b><?=$cf['field_title']?></b></td>
				<td width="65%"><?=$cf['field_value']?></td>
			</tr>
			<? endforeach; ?>
			
		</table>
	</td>
</tr>
</table>

<? endif; ?>