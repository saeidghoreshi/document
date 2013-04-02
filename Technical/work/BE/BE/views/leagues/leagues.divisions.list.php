<input type="hidden" id="D-hidden-links-id"/>
<input type="hidden" id="D-hidden-links-name"/>
<input type="hidden" id="D-hidden-links-current-id"/>

<div id='list-seasons'>
	<h1>Select a season.</h1>
	<h3>Teams can be assigned to a different division in each season.</h3>
	<div class='datatable' id='dt-md-seasons'></div>
	<div id='dt-md-seasons-pag'></div>



</div>
<div class='hidden' id='list-maincontent'>  

<table cellpadding="0" cellspacing="0" width="100%"  >
<tr valign="top" >
    <td width="20%">

    </td>
    <td width="80%" align="left"><div id="D-seq"></div></td>
</tr>
<tr valign="top">
    <td valign="top" align="left" colspan="2">
    
    <div class="datatable">
    <div align="center" id="D-dt-pag-divisions"></div>
    <div id="D-dt-divisions"></div>
    </div>
    
    </td>                              
</tr>
</table>          


<div id="D-divisions-extra"  >
<div class='btnset'>
 <div class="btn" style="float:left;"><button id ="btn-dman-subdiv">Show Subdivisions</button></div>
 <div class="btn" style="float:left;"><button id ="btn-dman-teams">Manage Teams</button></div>
 <div class="btn"> <button id ="btn-dman-edit">Save Changes</button></div>
<div class="btn"> <button id ="btn-dman-del" disabled="disabled">Delete Division</button></div>

</div>
<br/>
    <br />
         <fieldset><legend>New Division Information</legend>
            <div id="D-division-name-label">Name</div>
            <div id="D-division-name-input" class="input">
                <input type="text" id="D-division-name" />
            </div><br />
            <input type='radio' id='has-teams-yes' name='has-teams' />This division will only contain teams <br />
            <input type='radio' id='has-teams-no'  name='has-teams' CHECKED/>This division will only contain other divisions  <br />

         
         <button id ="btn-dman-add">Create Subdivision</button>
		 </fieldset>
</div>                        
</div>

