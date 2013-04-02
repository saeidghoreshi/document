
<? //hidden class added so that we can hide the dom until after the tabview is initialized ?>
<div class="window" id="myorder-window">
    <div id="tab-myorders" class="yui-navset">
        <ul class="yui-nav">
            <li class="selected"><a href="#tab0"><em>View Order</em></a></li>
            <li ><a href="#tab1"><em>Order Items</em></a></li>
            
            
        </ul>            
        <div class="yui-content">
            <div><?=$view?></div>
            <div><?=$orderitems?></div>
        </div>
    </div>
</div>