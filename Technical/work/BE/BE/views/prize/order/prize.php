<table class="prize">
<tr>
	<td colspan="3">
		<div class="title"><?=$prize['name']?></div>
		<div id="order-action-report"></div>
	</td>
</tr>
<tr>
	<td valign="top" width="100">
		<img src="<?=$prize['image']?>" height="100"/>
	</td>
	<td valign="top">
		<div class="description"><?=$prize['description']?></div>
		<div class="info">
			<p>To add items to your order, click the corresponding 'Order' row on your right of the size(s) you wish to order. 
			Enter in the number of items you require and then press enter. When complete, click the 'Add Items to Order' button 
			at the bottom of this screen.</p>
		</div>
	</td>
	<td valign="top" width="200">
		<div class="datatable">
			<div id="dt-prize-inventory"></div>
		</div>
	</td>
</tr>
</table>