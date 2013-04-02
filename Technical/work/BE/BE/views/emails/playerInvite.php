
<? if(!strstr($season_name,'season')) $season_name.=' season'?>
<? if(!strstr($league_name,'league')) $season_name.=' league'?>
<p>
	<br/><br/>
	<b><?=@$invitee_name?></b>,
</p>


<?=@$person_fname?> <?=@$person_lname?></b> of team <b><?=@$team_name?></b> has registered a team in the 
   <b><?=@$season_name?></b> 
   in the <b><?=@$league_name?></b> .
   
   You have been invited to signup for the team <b><?=@$team_name?></b> online 
 on the <a href='<?=@$url?>' target='_blank'> <b><?=@$league_name?></b> website </a>.  
 Use this link to go 
 to the team <b><?=@$team_name?></b> registration page and complete your registration. 
 You will also be given an option to pay your team dues.

 Thank you,
<p><?=PRODUCT_NAME?></p>

<br/><br/>

<p>If you have any questions regarding Spectrum, or you need help getting started, our support department is here to help!

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="3">Spectrum Technical Support Options</th></tr>
			<tr>
				<td width="15%"><b>Email</b></td>
				<td width="45%">24 / 7 / 365</td>
				<td width="40%"> <a href="mailto:<?=SUPPORT_EMAIL?>"> <?=SUPPORT_EMAIL?>  </a> </td>
			</tr>
			<tr>
				<td><b>Phone</b></td>
				<td>
					Monday - Friday, 8:30AM - 4:30PM<br/>
					excl. BC statuatory holidays
				</td>
				<td> <?=SUPPORT_PHONE?>  </td>
			</tr>
		</table>
	</td>
</tr>
</table>


