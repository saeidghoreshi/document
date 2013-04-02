<?
/**
* This form is used for both the creation and modification of an item.
* @author Bradley Holbrook
*/
?>

<? //hidden class added so that we can hide the dom until after the tabview is initialized ?>
<div class="window hidden" id="order-create">
	<div id="tab-order-create" class="yui-navset">
	    <ul class="yui-nav">
	        <li class="selected"><a href="#tab1"><em>Create Order</em></a></li>
	        <li><a href="#tab2"><em>Prizes</em></a></li>
	        <li><a href="#tab3"><em>Summary</em></a></li>
	        <li><a href="#tab4"><em>Checkout</em></a></li>
	    </ul>            
	    <div class="yui-content">
	        <div><?=$form?></div>
	        <div><?=$prizes?></div>
	        <div><?=$summary?></div>
	        <div><?=$checkout?></div>
	    </div>
	</div>
</div>