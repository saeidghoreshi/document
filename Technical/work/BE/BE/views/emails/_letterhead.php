<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>

<base href="http://dev.global.playerspectrum.com"/>

<style>
<?=$css?>
</style>

</head>
<body>

<table class="main" cellpadding="5" cellspacing="0">
<tr>
	<td width="100%">
		<table class="container" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<table class="header" cellpadding="0" cellspacing="0">
					<tbody>
					<tr class="tmpl">
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
						<td width="30"></td>
					</tr>
					<tr>
						<td rowspan="5" colspan="8" height="135" id="logo">
							<img src="assets/images/spectrum_logo_r3_offblue.png" width="179" height="135" align="left"/>
						</td>
						<td colspan="12" class="hdr_spacer">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4"></td>
						<td colspan="7" id="hdr_title">Spectrum Customer Service</td>
						<td colspan="1"></td>
					</tr>
					<tr>
						<td colspan="1"></td>
						<td colspan="11" id="hdr_email"><a href="mailto:  <?=SUPPORT_EMAIL?>   "><span> <?=SUPPORT_EMAIL?> </span></a></td>
					</tr>
 
					<tr>
						<td colspan="2"></td>
						<td colspan="5"  id="hdr_phone">  <?=SUPPORT_PHONE?> </td>
						<td colspan="6"></td>
					</tr>
					 
					<tr>
						<td colspan="12" class="hdr_spacer">&nbsp;</td>
					</tr>
					<tr class="tmpl">
						<td colspan="20"></td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td class="subject"><?=@$subject?></td>
		</tr>
		<tr>
			<td class="content">
                <?=$body?>
			</td>
		</tr>
		<tr>
			<td class="footer">
			
			</td>
		</tr>
		</table>
		
		<table class="legal" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<center><p>&copy; Servillian Solutions Inc 2010 - <?=date('Y')?></p></center>
				<p>
					This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed. 
					If you have received this email in error, please notify the system manager. This message contains confidential information and is intended only for the 
					individual named. If you are not the named addressee, you should not disseminate, distribute or copy this email. Please notify the sender immediately by 
					email if you have received this email by mistake and delete this email from your system. If you are not the intended recipient, you are notified that 
					disclosing, copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited.
				</p>
			</td>
		</tr>
		</table>
		
	</td>
</tr>
</table>

</body>
</html>
