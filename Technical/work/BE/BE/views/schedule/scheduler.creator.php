<?
/**
* ????NOT USED
*/
?>

<? //hidden class added so that we can hide the dom until after the tabview is initialized ?>
<div class="window hidden" id="scheduler">

    <div id="tab-scheduler" class="yui-navset">
        <ul class="yui-nav">
            <li class="selected"><a href="#tab0"><em>Setup</em></a></li>
            <li class="disabled"><a href="#tab1"><em>Venues</em></a></li>
            <li class="disabled"><a href="#tab2"><em>Rules</em></a></li>
            <li class="disabled"><a href="#tab3"><em>Divisions</em></a></li>
            <li class="disabled"><a href="#tab4"><em>Matches</em></a></li>
            <li class="disabled"><a href="#tab5"><em>Teams</em></a></li>
            <li class="disabled"><a href="#tab6"><em>Method</em></a></li>
        </ul>            
        <div class="yui-content">
            <div><?=$setup?></div>
            <div><?=$venues?></div>
            <div><?=$rules?></div>
            <div><?=$divisions?></div>
            <div><?=$matches?></div>
            <div><?=$teams?></div>
            <div><?=$method?></div>
        </div>
    </div>
</div>