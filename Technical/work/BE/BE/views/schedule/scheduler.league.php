
<div id="sch-loading"></div>

<div class="window hidden" id="scheduler">


    <div id="scheduler-navigation">
    	<h1><span id='span-h1-sname'>New Schedule</span></h1>
    	<ul class="bold-nav" id="bold-nav-main">
    		<li class="optional">
    			<a href="#" id="sched-file">File</a>
    			<ul>
    				<li class="active">  <a href="#" id="sched-file-save">  Save Schedule</a></li>
    				<li class="">        <a href="#" id="sched-file-load">  Load Schedule</a></li><?//to and from session table?>
    				<li class="hidden">        <a href="#" id="sched-file-import">Import Schedule</a></li><?//csv,xml,etc?>
    				<li class=""><a href="#" id="sched-file-export">        Export Schedule</a></li><?//csv,xml,json,...?>
    			</ul>
    		</li>
    		<li class="active optional">
    			<a href="#" id="sched-welcome">Welcome</a>
    			<ul>
    				<li class="active">  <a href="#" id="sched-welcome-intro">Introduction</a></li>
    				<li class="disabled"><a href="#" id="sched-welcome-name"> Schedule Name</a></li>
    				<!--<li class="disabled"><a href="#" id="sched-welcome-progress">Progress</a></li>-->
    			</ul>
    		</li>
    		<li class="disabled">
    			<a href="#" id="sched-season">Season</a>
    			<ul>
    				<li class='active'><a href="#" id="sched-season-select">Select a Season</a></li>
    			</ul>
    		</li>
    		<li class="disabled">
    			<a href="#" id="sched-fields">Fields</a>
    			<ul>
    				<li class="active">  <a href="#" id='sched-fields-lists'> Create and Edit Lists</a></li>
    				<li class="disabled"><a href="#"id='sched-fields-times'>  Select Times</a></li>
    				<li class="disabled"><a href="#"id='sched-fields-dates'>  Select Dates</a></li>
    				<li class="disabled"><a href="#" id='sched-fields-assign'>Assign Fields</a></li>
    				<li class="optional"><a href="#" id='sched-fields-review'>Review Selections</a></li>
    			</ul>
    		</li>
    		<li class="disabled">
    			<a href="#" id="sched-divisions">Divisions</a>
    			<ul>
    				<li class="active"><a href="#" id="sched-divisions-select">Select Divisions</a></li>
    			</ul>
    		</li>
     		<li class="disabled">
    			<a href="#" id="sched-match">Matches</a>
    			<ul>
    				<li class="active"><a href="#" id="sched-match-create">Create Matches</a></li>
    			</ul>
    		</li>   		
    		

    		<li class="disabled">
    			<a href="#" id="sched-rules">Rules</a>
    			<ul>
    				<li class="active">  <a href="#" id="sched-rules-global">  Global Rules</a></li>
    				<li class="">        <a href="#" id="sched-rules-date">    Date List Rules</a></li>
    				<li class="disabled"><a href="#" id="sched-rules-div">     Division Rules</a></li>
    				<li class="disabled"><a href="#" id="sched-rules-conflict">Conflicts</a></li>
    			</ul>
    		</li>
    		
    		<li class="disabled optional">
    			<a href="#" id="sched-teams">Team Exceptions</a>
    			<ul>
    				<li class="active">  <a href="#" id="sched-teams-manage">Manage Team Exceptions</a></li>
    				<li class='disabled'><a href="#" id="sched-teams-add">   Add Team Exception</a></li>
    			</ul>
    		</li>
    		<li class="disabled optional">
    			<a href="#" id="sched-extra">Extra Games</a>
    			<ul>
    				<li class="active"><a href="#" id="sched-extra">Create games manually</a></li>
    			</ul>
    		</li>
	
    		<li class="disabled">
    			<a href="#" id="sched-create">Create</a>
    			<ul>
    				<li class="active">  <a href="#" id="sched-create-generate">Generate Schedule</a></li>
    				<li class="disabled"><a href="#" id="sched-create-view">    View Schedule</a></li>
    				<li class="disabled"><a href="#" id="sched-create-save">Save</a></li>
    				<li class='disabled'><a href="#" id="sched-create-publish"> Publish</a></li>
    			</ul>
    		</li>
    		<li class="disabled optional">
    			<a href="#" id="sched-review">Review</a>
    			<ul>
    				<li class="active">  <a href="#" id="sched-review-audit"> Audit Reports</a></li>
    				<li class='disabled'><a href="#" id="sched-review-conflicts">Conflict Report</a></li>
    				
    			</ul>
    		</li>
    	</ul>
    </div>
    
    
    <div id="tab-scheduler" class="yui-navset" >
        <ul class="yui-nav hidden" id='s-tab-bar'> <!--HIDDEN GOES HERE!!!!!!!!!!!!!!! -->
            <!-- <li class="selected"><a href="#"><em>Leagues</em></a></li>-->
            <!-- <li class="disabled"><a href="#"><em>Method</em></a></li>--> 
            <li class="selected"><a href="#tab0"><em>Welcome</em></a></li>
            <li class=""><a href="#tab1" ><em>Seasons</em></a></li>
            <li class=""><a href="#tab2" ><em>Venues and Contracts</em></a></li>
            <li class=""><a href="#tab3" ><em>Rules</em></a></li>
            <li class=""><a href="#tab4" ><em>Divisions</em></a></li>
            <li class=""><a href="#tab5" ><em>Division Rules</em></a></li>
            <li class=""><a href="#tab6" ><em>Venue Preferences</em></a></li>
            <li class=""><a href="#tab7" ><em>Matches</em></a></li>
            <li class=""><a href="#tab8" ><em>Team Exceptions</em></a></li>
            <li class=""><a href="#tab9" ><em>Schedule</em></a></li>
            <li class=""><a href="#tab10"><em>Audit</em></a></li>
            <li         ><a href="#tab11"><em>Extra Games</em></a></li>
            <li         ><a href="#tab12"><em>File</em></a></li>
            <li         ><a href="#tab13"><em>help</em></a></li>
           
        </ul>            
        <div class="yui-content">
            <!--  <div><?//=$leagues?></div>-->
            <!--   <div><?//=$method?></div>-->
            <div><?=$welcome?></div><?#0?>
            <div><?=$seasons?></div><?#1?>
            <div><?=$venues?></div><?#2?>
            <div><?=$rules?></div><?#3?>
            <div><?=$divisions?></div><?#4?>
            <div><?=$divrules?></div><?#5?>
            <div><?=$venueprefs?></div><?#6?>
            <div><?=$matches?></div><?#7?>
            <div><?=$teams?></div><?#8?>
            <div><?=$scheduletab?></div><?#9?>
            <div><?=$audit?></div><?#10?>
            <div><?=$extra?></div><?#11?>
            <div><?=$file?></div><?#12?>
            <div><?=$help?></div><?#13?>
            
        </div>
    </div>
    
    
</div>