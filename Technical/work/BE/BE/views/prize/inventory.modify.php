<?
/**
* This form is used for both the creation and modification of an item.
* @author Bradley Holbrook
*/
?>

<? //hidden class added so that we can hide the dom until after the tabview is initialized ?>
<div id="prize-loading"></div>

<div class="window hidden" id="inventory-modify">
	<div id="selected_prize_sku">Selected: <span id="current_sku">None</span></div>
	<div id="tab-inventory-modify" class="yui-navset">
	    <ul class="yui-nav">
	        <li class="selected"><a href="#tab0"><em>Prizes</em></a></li>
	        <li class="disabled"><a href="#tab1"><em>Categories</em></a></li>
	        <li class="disabled"><a href="#tab2"><em>Warehouses</em></a></li>
	        <li class="disabled"><a href="#tab3"><em>Details</em></a></li>
	        <li class="disabled"><a href="#tab4"><em>Inventory</em></a></li>
	        <li class="disabled"><a href="#tab5"><em>Prices</em></a></li>
	        <!--<li class="disabled"><a href="#tab6"><em>Media</em></a></li>-->
	        
	    </ul>            
	    <div class="yui-content">
	        <div><?=$prizes?></div>
	        <div><?=$categories?></div>
	        <div><?=$warehouses?></div>
	        <div><?=$details?></div>
	        <div><?=$inventory?></div>
	        <div><?=$prices?></div>
	        <!--<div><?=$media?></div>-->
	    </div>
	</div>
</div>
