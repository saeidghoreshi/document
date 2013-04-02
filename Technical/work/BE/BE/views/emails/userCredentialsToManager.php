<p><br/><br/><b><?=$user['fname']?> <?=$user['lname']?></b>;</p>

<p>You have successfully created a username and password to login and manage your team registration. At any point you
can login at:<br/>
<a href="<?=$loginUrl?>"><?=$loginUrl?></a></p>

<p>When your team registration is complete and your team is approved, you can manage your roster and pay your completed
league fees by logging in at:<br/>
<a href="http://live.spectrum.servilliansolutionsinc.com">http://live.spectrum.servilliansolutionsinc.com</a></p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="4">User Information</th></tr>
			<tr>
				<td width="15%"><b>First Name</b></td>
				<td width="35%"><?=$user['fname']?></td>
				<td width="15%"><b>Last Name</b></td>
				<td width="35%"><?=$user['lname']?></td>
			</tr>
			<tr>
				<td><b>Username</b></td>
				<td><?=$user['username']?></td>
				<td><b>Password</b></td>
				<td><?=$user['password']?></td>
			</tr>
		</table>
	</td>
</tr>
</table>