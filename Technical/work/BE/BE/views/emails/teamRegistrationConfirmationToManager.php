
<? $tLoginUrl = "{$league_url}/index.php/registration/index"; ?>
 
<p><br/><br/><b><?=$person_fname?> <?=$person_lname?></b>;</p>

<p>Thank you for the registration of your team '<b><?=$team_name?></b>' in the '<b><?=$league_name?></b>'. Below is a copy of your registration
information. Please note that this does not serve as a receipt of payment. If you have completed a payment or deposit, you can expect your receipt to 
be emailed separately.</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="2">Registration Details</th></tr>
			<tr>
				<td><b>Registration Time</b></td>
				<td><?=$time?></td>
			</tr>
			<tr>
				<td><b>Team Name</b></td>
				<td><?=$team_name?></td>
			</tr>
			<tr>
				<td><b>League</b></td>
				<td><?=$league_name?></td>
			</tr>
			<tr>
				<td><b>League Season</b></td>
				<td><?=$season_name?></td>
			</tr>
			<tr>
				<td><b>League Division</b></td>
				<td><?=$division_name?></td>
			</tr>
			<tr>
				<td><b>League Website</b></td>
				<td><?=$league_url?></td>
			</tr>
			<tr>
				<td><b>Association</b></td>
				<td><?=$association_name?></td>
			</tr>
		</table>
	</td>
</tr>
</table>

<p>This is the information you entered as the Team Manager</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="4">Team Manager</th></tr>
			<tr>
				<td width="15%"><b>First Name</b></td>
				<td width="35%"><?=$person_fname?></td>
				<td width="15%"><b>Last Name</b></td>
				<td width="35%"><?=$person_lname?></td>
			</tr>
			<tr>
				<td><b>Gender</b></td>
				<td>
					<?=($person_gender=="M")?"Male":'';?>
					<?=($person_gender=="F")?"Female":'';?></td>
					<!-- frontend form no longer has this field
				<td><b>Birthdate</b></td>
				<td><?=$person_birthdate?></td>-->
			</tr>
			<tr>
				<td><b>Home Phone</b></td>
				<td>
					<?=@$p_home?>
					<?=@$person_homephone?>
				
				</td>
				<td><b>Work Phone</b></td>
				<td>
					<?=@$p_work?>
					<?=@$person_workphone?>
				
				</td>
				<td><b>Mobile Phone</b></td>
				<td>
					<?=@$p_mobile?>
					<?=@$person_mobilephone?>
				
				</td>
			</tr>
			<tr>
				<td><b>Email</b></td>
				<td colspan="3"><?=$email?></td>
			</tr>
			<tr>
				<td><b>Address</b></td>
				<td colspan="3"><?=$shipping_address?></td>
			</tr>
			<tr>
				<td><b>City</b></td>
				<td><?=@$shipping_city?></td>
				<td><b>Region</b></td>
				<td><?=@$shipping_province_name?></td>
			</tr>
			<tr>
				<td><b>Country</b></td>
				<td><?=@$shipping_country_name?></td>
				<td><b>Postal Code</b></td>
				<td><?=@$shipping_postalcode?></td>
			</tr>
		</table>
	</td>
</tr>
</table>

<?if($is_manager):?>
<p>If you wish to login and edit your existing registration(s), you can login to team registration.</p>
<p><b>Login Page:</b> <a href="<?=$tLoginUrl?>"><?=$tLoginUrl?></a></p>
<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="4">Login Information</th></tr>
			<tr>
				<td width="15%"><b>Username</b></td>
				<td width="35%"><?=$login?></td>
				<td width="15%"><b>Password</b></td>
				<td width="35%">
				<?if(isset($password) && $password):?>
					<?=$password?>
				<?else:?>
				 If you have forgotten your password, we can find it, just use the "Forgot my Password" button on the Login Page. 
				 <?endif;?>
				 </td>
			</tr>
		</table>
	</td>
</tr>
</table>
<?endif;?>

<?    if(isset($invited) && is_array($invited) && count($invited)>0):?>
<p>The following people have been sent invitations to register on your team:</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
		<tr>
			<th width="40%">Name</th>
			<th width="60%">Email</th>
		</tr>
        <?  foreach($invited as $invite): ?>
		<tr>
			<td><b><?=$invite['name']?></b></td>
            <td><?=$invite['email']?></td>
		</tr>
		<? endforeach; ?>
        
		</table>
	</td>
</tr>
</table>
<?endif;?>
<? if(isset($customfields) && is_array($customfields) && count($customfields)>0): ?>

<p>This information was requested by the league. We have sent this information along with your team registration to the league.</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">

			<tr><th colspan="2">League Requested Information</th></tr>
			<? foreach($customfields as $cf):?>
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