<? 
$invoice['issued'] 	= date("l F jS, Y g:i a", strtotime($invoice['issued']));
$invoice['due'] 	= date("l F jS, Y", strtotime($invoice['due']));
?>

<p><br/><br/>Attention <b><?=$to['fname']?> <?=$to['lname']?></b>;</p>

<p><b><?=$from['orgname']?></b> has sent you the following invoice:</p>

<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td width="50%" valign="top">
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th>Invoice Prepared For</th></tr>
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
			<tr><th>Invoice Prepared By</th></tr>
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
			<tr><th colspan="2">Invoice Details</th></tr>
			<tr><td width="200"><b>Invoice for</b></td><td><?=$invoice['title']?></td></tr>
			<tr><td><b>Details</b></td><td><?=$invoice['descr']?></td></tr>
			<tr><td><b>Spectrum Invoice Number</b></td><td><?=$invoice['number']?></td></tr>
			<tr><td><b>Custom Invoice Number</b></td><td><?=$invoice['custom_no']?></td></tr>
			<tr><td><b>Issued</b></td><td><?=$invoice['issued']?></td></tr>
			<tr><td><b>Payment Due</b></td><td><?=$invoice['due']?></td></tr>
			<tr><td><b>Currency</b></td><td><?=$invoice['currency']?></td></tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="2">
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr>
				<th>Invoice Item</th>
				<th>Price</th>
				<th>Qty</th>
				<th>Total</th>
			</tr>
			
			<? foreach($items as $item): ?>
			<tr>
				<td width="400"><?=$item['item']?></td>
				<td align="right"><?=number_format($item['price'],2)?></td>
				<td align="center"><?=$item['qty']?></td>
				<td align="right" width="70"><?=number_format($item['total'],2)?></td>
			</tr>
			<? endforeach; ?>
		</table>
	</td>
</tr>

<? 
//rowspan number for comments box
$rowspan = count(@$totals['tax']) + 5;
?>
<tr>
	<td colspan="2">
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr>
				<td rowspan="<?=$rowspan?>" width="350">
					<?=$invoice['comments']?>
				</td>
				<th colspan="3">Summary</th>
			</tr>
			<tr>
				<td><b>Subtotal</b></td>
				<td align="center"></td>
				<td align="right"><?=number_format($totals['subtotal'],2)?></td>
			</tr>
			
            <?
            ?>
			<? 
            if(isset($totals['tax']))
            foreach($totals['tax'] as $name=>$tax): ?>
			<tr>
				<td><?=$name?></td>
				<td align="center"><?=number_format($tax['rate'],2)?> %</td>
				<td align="right"><?=number_format($tax['amt'],2)?></td>
			</tr>
			<? endforeach; ?>
			
			<tr>
				<td><b>Total</b></td>
				<td align="center"></td>
				<td align="right" width="75">$ <?=number_format($totals['total'],2)?></td>
			</tr>
			<tr>
				<td><b>Paid</b></td>
				<td align="center"></td>
				<td align="right" width="75"><?=number_format($totals['paid'],2)?></td>
			</tr>
			<tr>
				<td><b>Owing</b></td>
				<td align="center"></td>
				<td align="right" width="75">$ <?=number_format($totals['owing'],2)?></td>
			</tr>
		</table>
	</td>
</tr>
</table>
