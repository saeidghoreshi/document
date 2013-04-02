
<? //hidden class added so that we can hide the dom until after the tabview is initialized ?>
<div class="window" id="ship-window">
    <div id="tab-ship" class="yui-navset">
        <ul class="yui-nav">
            <li class="selected"><a href="#tab0"><em>Ready to be Picked</em></a></li>
            <li ><a href="#tab1" class="disabled"><em>Details</em></a></li>           
            <li ><a href="#tab2"><em>Ready for Shipping</em></a></li>   
            <li ><a href="#tab3"><em>Completed</em></a></li>   
            
        </ul>            
        <div class="yui-content">
            <div><?=$orders?></div>
             <div><?=$details?></div>           
			 <div><?=$picked?></div>  
			 <div><?=$completed?></div>  
			  
        </div>
    </div>
</div>