<div class="remove-tab-padding">
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top"><img src="/assets/images/scheduler/girl-fielding-ball.png"/></td>
	<td width="100%" valign="top" class="padwrap">
		<div id="step1">
			<h1>Select a Prize Category</h1>
			
			<p>Select a prize category to display the prizes in that category. The (number) to the right of the category name
			signifies how many different prizes are available in that category.</p>
			
			<div id="ctr-prize-categories">
				<?foreach($categories as $category):?>
					<div class="category">
						<a href="javascript:PrizeList.show_prizes(<?=$category['category_id']?>)">
							<?=$category['category_name']?> (<?=$category['prizes']?>)
						</a>
					</div>
				<?endforeach;?>
			</div>
			
		</div>
	</td>
</tr>
</table>
</div>
