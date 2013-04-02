
<table class="receipt" cellpadding="1" cellspacing="5">
<tr>
	<td width="50%">
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th>Receipt Prepared For</th></tr>
			<tr>
				<td>
					<? if($to['orgname']) echo $to['orgname']."<br/>"; ?>
					<? if($to['fname'] and $to['lname']) echo $to['lname'].", ".$to['fname']."<br/>"; ?>
					<?=$to['address']?><br/>
					<?=$to['city']?>, <?=$to['region']?><br/>
					<?=$to['country']?><br/>
					<?=$to['code']?>
				</td>
			</tr>
		</table>
	</td>
	<td width="50%">
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr><th>Receipt Prepared By</th></tr>
			<tr>
				<td>
					<? if($from['orgname']) echo $from['orgname']."<br/>"; ?>
					<? if($from['fname'] and $from['lname']) echo $from['lname'].", ".$from['fname']."<br/>"; ?>
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
			<tr><td width="200"><b>Payment Type</b></td><td><?=$gateway['type']?></td></tr>
			<tr><td><b>Spectrum Transaction ID</b></td><td><?=$gateway['trans_id']?></td></tr>
			<tr><td><b>Payment Time</b></td><td><?=$gateway['time']?></td></tr>
			
			<? if($gateway['typeid']==2): ?>
				<tr><td><b>Tag</b></td><td><?=$gateway['io_tag']?></td></tr>
				<tr><td><b>Authorization Number</b></td><td><?=$gateway['io_authnum']?></td></tr>
			<? endif; ?>
			
			<? if(in_array($gateway['typeid'],array(3,4))): ?>
				<tr><td><b>Order Number</b></td><td><?=$gateway['cc_order']?></td></tr>
				<tr><td><b>Reference Number</b></td><td><?=$gateway['cc_txref']?></td></tr>
				<tr><td><b>Status</b></td><td><?=$gateway['statusmsg']?></td></tr>
			<? endif; ?>
		</table>
	</td>
</tr>
<tr>
	<td colspan="2">
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr>
				<th>Item Purchased</th>
				<th>Price</th>
				<th>Qty</th>
				<th>Total</th>
			</tr>
			
            <?if(isset($items)):?>
			<? foreach($items as $item): ?>
			<tr>
				<td width="400"><?=$item['item']?></td>
				<td align="right"><?=number_format($item['price'],2)?></td>
				<td align="center"><?=$item['qty']?></td>
				<td align="right" width="70"><?=number_format($item['total'],2)?></td>
			</tr>
			<? endforeach; ?>
            <?endif;?>
		</table>
	</td>
</tr>

<? 
//rowspan number for comments box
$rowspan = count(@$totals['tax']) + 3;
?>
<tr>
	<td colspan="2">
		<table class="recipient" cellpadding="4" cellspacing="1">
			<tr>
				<td rowspan="<?=$rowspan?>" width="350">
					<!-- Comments -->
					Thank you for your purchase!
				</td>
				<th colspan="3">Summary</th>
			</tr>
			<tr>
				<td><b>Subtotal</b></td>
				<td align="center"></td>
				<td align="right"><?=number_format($totals['subtotal'],2)?></td>
			</tr>
			
			<? 
            if(isset($totals['tax']))
            foreach(@$totals['tax'] as $name=>$tax): ?>
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
		</table>
	</td>
</tr>
</table>
