
<p>
	<br/><br/>
	<b><?=@$person_fname?> <?=@$person_lname?></b>;
</p>

<p>
	Welcome, and thank you for joining the Spectrum Online Sports Management System.
	Your league account for <em><b><?=@$league_name?></b></em> has been setup and is ready for use.  
	The username and password below will log you into Spectrum.
</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="4">Login Information</th></tr>
			<tr>
				<td width="15%"><b>Username</b></td>
				<td width="35%"><?=@$login?></td>
				<td width="15%"><b>Password</b></td>
				<td width="35%"><?=@$pass?></td>
			</tr>
		</table>
	</td>
</tr>
</table>

<p>You will now be able to log in to the Spectrum program and begin using all of the fantastic features including:</p>
<ul>
	<li><b>Online Registration</b> - no more typing, photocopying and phoning</li>
	<li><b>Payment</b> - collect teams fees online</li>
	<li><b>Rosters</b> - manage all of the league teams</li>
	<li><b>Scheduling</b> - make and post your league schedule online</li>
	<li><b>Information</b> - Writing and posting notices and updates to the league site</li>
	<li><b>Communication</b> - send league emails to all teams</li>
	<li><b>Standings</b> - up to date and on your site so that everyone can see them</li>
</ul>
<p>and much, much more!</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td>
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th colspan="2">Resources</th></tr>
			<tr>
				<td width="40%"><b>Login to Spectrum</b></td>
				<td width="60%"><a href="http://live.spectrum.servilliansolutionsinc.com">http://live.spectrum.servilliansolutionsinc.com</a></td>
			</tr>
			<tr>
				<td><b>Your League Website</b></td>
				<td><a href="http://<?=@$url?>">http://<?=@$url?></a></td>
			</tr>
			<tr>
				<td><b>Spectrum Tutorials</b></td>
				<td><a href="http://playerspectrum.com/index.php/2011-11-09-21-51-55/getting-started">http://www.playerspectrum.com</a></td>
			</tr>
			<tr><th colspan="2">Getting Started With Spectrum</th></tr>
			<tr>
				<td colspan="2">
					<p><b>1)</b> Login to Spectrum at the location above using the username and password provided above.</p>
					<p><b>2)</b> When you see the welcome page, click 'Next'.</p>
					<p><b>3)</b> Check your league name, website prefix, and domain name for accuracy. Click 'Next'.<br/><em>A website prefix
					is the portion before your domain name. For example, in http://spectrumleague.playerspectrum.com, the
					prefix is 'spectrumleague'. This prefix is unique to your league.</em></p>
					<p><b>4)</b> Complete your Organization Address and click 'Next'.</p>
					<p><b>5)</b> Confirm your 'Assigned Users'. At this point it should be just you, but you can add new people at
					this point if you wish. This can also be done at a later time. Click 'Next'.</p>
					<p><b>6)</b> Enter You League Banking Information. Click 'Next'<em>Your league banking information is collected so that you
					can deposit funds into your league bank account after collecting registration fees. This can be completed
					at a later time.</em></P>
					<p><b>7)</b> Verify all of the information in the League Summary is complete. Click 'Next'.</p>
					<p><b>8)</b> Read the terms and conditions. If you accept the terms and conditions, check the 'I accept' box 
					and then click 'Next'.</p>
					<p><b>9)</b> To view tutorials, click on the Spectrum Tutorials link in the message box.<p>
					<p><b>10)</b> Congratulations! You're league is ready and your website is live!</p>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>

<?/* if(isset($has_attached) && $has_attached): // if doc is attached, show this message?>
	<p>See the attached file for a quick step by step procedure of the first few things that will happen when you log in with Spectrum for the first time.</p>
<?endif;*/?>

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

<p><i>Please Note: Spectrum technical support cannot help you with association related items such as insurance or prizing.</i></p>