
<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td width="50%" valign="top">
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th>Payment Made To</th></tr>
			<tr>
				<td>
					<? echo ($to['orgname']) ? $to['orgname']  : '&nbsp;'; ?><br/>
					<? echo ($to['fname'] and $to['lname']) ? $to['lname'].", ".$to['fname'] : '&nbsp;'; ?><br/>
					<?=$to['address']?><br/>
					<?=$to['city']?>, <?=$to['region']?><br/>
					<?=$to['country']?><br/>
					<?=$to['code']?>
				</td>
			</tr>
		</table>
	</td>
	<td width="50%" valign="top">
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th>Payment Made By</th></tr>
			<tr>
				<td>
					<? echo ($from['orgname']) ? $from['orgname']  : '&nbsp;'; ?><br/>
					<? echo ($from['fname'] and $from['lname']) ? $from['lname'].", ".$from['fname'] : '&nbsp;'; ?><br/>
					<?=$from['address']?><br/>
					<?=$from['city']?>, <?=$from['region']?><br/>
					<?=$from['country']?><br/>
					<?=$from['code']?>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="2">
		<table class="recipient" cellpadding="4" cellspacing="1">
			
			<tr><th colspan="2">Payment Details</th></tr>
			<!-- Payment Details -->
			<tr><td><b>Spectrum Transaction ID</b></td>	<td><?=$payment['trans_id']?></td></tr>
			
			<!-- Payment Information -->
			<tr><th colspan="2">Transaction Information</th></tr>
			<tr><td width="200"><b>Payment Type</b></td><td><?=$payment['type']?></td></tr>
			<tr><td><b>Spectrum Transaction ID</b></td>	<td><?=$payment['trans_id']?></td></tr>
			<tr><td><b>Payment Time</b></td>			<td><?=$payment['time']?></td></tr>
			
			<!-- Interac Online -->
			<? if($payment['typeid']==2): ?>
				<tr><td><b>Tag</b></td>					<td><?=$payment['io_tag']?></td></tr>
				<tr><td><b>Authorization Number</b></td><td><?=$payment['io_authnum']?></td></tr>
			<? endif; ?>
			
			<!-- Visa / Mastercard -->
			<? if(in_array($payment['typeid'],array(3,4))): ?>
				<tr><td><b>Order Number</b></td>		<td><?=$payment['cc_order']?></td></tr>
				<tr><td><b>Reference Number</b></td>	<td><?=$payment['cc_txref']?></td></tr>
				<tr><td><b>Status</b></td>				<td><?=$payment['statusmsg']?></td></tr>
			<? endif; ?>
			
			<!-- Payment made on Invoice -->
			<? if($payment['type']=='invoice'): ?>
			<tr><th colspan="2">Invoice Details</th></tr>
			<tr><td><b>Invoice for</b></td>	<td><?=$invoice['title']?></td></tr>
			<tr><td><b>Spectrum Invoice Number</b></td>	<td><?=$invoice['number']?></td></tr>
			<tr><td><b>Custom Invoice Number</b></td>	<td><?=$invoice['custom_no']?></td></tr>
			<tr><td><b>Issued</b></td>					<td><?=$invoice['issued']?></td></tr>
			<tr><td><b>Payment Due</b></td>				<td><?=$invoice['due']?></td></tr>
			<tr><td><b>Currency</b></td>				<td><?=$invoice['currency']?></td></tr>
			<? endif; ?>
			
		</table>
	</td>
</tr>

</table>

