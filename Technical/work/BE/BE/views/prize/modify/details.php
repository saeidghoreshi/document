<div id="prize-action-report"></div>

<form name="frmDetails" id="frmDetailsId">
<input type="hidden" id="prize_id" name="details[prize_id]"/>
<input type="hidden" id="category_id" name="details[category_id]"/>
<div id="insert-inventory-item">
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="35%" colspan="2">
			<div id="price-name" class="form-field">
				<div id="prize-name-label">Prize Name</div>
				<div id="prize-name-input" class="input">
					<input type="text" id="name" name="details[name]" class="req" title="Prize Name"/>
				</div>
			</div>
		</td>
		<td width="70%" rowspan="4">
			<div id="price-desc" class="form-field">
				<textarea id="rte-inventory-details" name="details[description]"></textarea>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">

				<div id="prize-category-label">Category</div>
				<div id="prize-category-contain"  > <div id='btn-c-dropdown'></div> </div>

		</td>
	</tr>
	<tr>
		<td>
			<div id="prize-sku" class="form-field">
				<div id="prize-sku-label">Prize SKU</div>
				<div id="prize-sku-input" class="input">
					<input type="text" id="sku" name="details[sku]" class="caps"/>
				</div>
			</div>
		</td>
		<td>
			<div id="price-upc" class="form-field">
				<div id="prize-upc-label">Prize UPC</div>
				<div id="prize-upc-input" class="input">
					<input type="text" id="upc" name="details[upc]"/>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div id="price-img" class="form-field">
				<div id="prize-img-label">Prize Image</div>
				<div id="prize-img-input" class="input">
					<input type="file" id="img" name="image"/>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<div id="price-delete" class="form-field">
				<div id="prize-delete-input">
					<input type="checkbox" id="delete" name="details[delete]"/> Delete this prize.<em>Please Note: This will only remove the prize from the primary list, not completly delete the item. Only an administrator may recover a deleted item.</em>
				</div>
			</div>
		</td>
	</tr>
	</table>
</div>
</form>
