
<? $tLoginUrl = "{$info['regurl']}/index.php/registration/index"; ?>

<? $names = array(); foreach($exec as $v) $names[] = $v['name']; $names = implode(", ",$names); ?>
<p><br/><br/>Attention: <b><?=$names?></b>;</p>

<p>The following team has registered for the season '<b><?=$info['season']?></b>'.</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="2">Registration Details</th></tr>
			<tr>
				<td><b>Team Name</b></td>
				<td><?=$info['team']?></td>
			</tr>
			<tr>
				<td><b>Season</b></td>
				<td><?=$info['season']?></td>
			</tr>
			<tr>
				<td><b>Division</b></td>
				<td><?=$info['division']?></td>
			</tr>
			<tr>
				<td><b>Registration Time</b></td>
				<td><?=$info['time']?></td>
			</tr>
			<tr>
				<td><b>Player Invitations Sent</b></td>
				<td><?=count($invited)?></td>
			</tr>
		</table>
	</td>
</tr>
</table>

<p>This is the information entered for the Team Manager</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="4">Team Manager</th></tr>
			<tr>
				<td width="15%"><b>First Name</b></td>
				<td width="35%"><?=$manager['fname']?></td>
				<td width="15%"><b>Last Name</b></td>
				<td width="35%"><?=$manager['lname']?></td>
			</tr>
			<tr>
				<td><b>Gender</b></td>
				<td><?=($manager['gender']=="M")?"Male":"Female"?></td>
				<td><b>Birthdate</b></td>
				<td><?=$manager['birthdate']?></td>
			</tr>
			<tr>
				<td><b>Phone 1</b></td>
				<td><?=$manager['phone1']?></td>
				<td><b>Phone 2</b></td>
				<td><?=$manager['phone2']?></td>
			</tr>
			<tr>
				<td><b>Email</b></td>
				<td colspan="3"><?=$manager['email']?></td>
			</tr>
			<tr>
				<td><b>Address</b></td>
				<td colspan="3"><?=$manager['address']?></td>
			</tr>
			<tr>
				<td><b>City</b></td>
				<td><?=$manager['city']?></td>
				<td><b>Region</b></td>
				<td><?=$manager['region']?></td>
			</tr>
			<tr>
				<td><b>Country</b></td>
				<td><?=$manager['country']?></td>
				<td><b>Code</b></td>
				<td><?=$manager['code']?></td>
			</tr>
		</table>
	</td>
</tr>
</table>

<? if(isset($customfields['league'])): ?>

<p>Here is additional information you requested.</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">

			<tr><th colspan="2">League Requested Information</th></tr>
			<? foreach($customfields['league'] as $cf):?>
			<tr>
				<td width="35%"><b><?=$cf['field']?></b></td>
				<td width="65%"><?=$cf['value']?></td>
			</tr>
			<? endforeach; ?>
			
		</table>
	</td>
</tr>
</table>

<? endif; ?>