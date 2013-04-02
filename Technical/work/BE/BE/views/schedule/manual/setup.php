


<div id="setup-tab" >



<p>This tool allows you to insert a schedule into the system that has already been created on paper.</p>

<p>For each game, you will need to know both teams, and the date, time, and location.</p>


<p>Finally, once all the games are entered, we can save and publish this schedule to the website of any existing league</p>


<table class="bigtable" >

<tr>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
<td width="10%"></td>
</tr>
<tr>

<td class="  cell" colspan='5' > 
	<span class="label">Give this schedule a Name: </span>  
	<input id="txt-name" value="New Manual Schedule" type="text" />
</td>

<td class="cell" colspan='5' >

	<div class="label">Select an active season for this schedule: </div>

</td>

</tr><tr>

<td colspan='5'>  
</td>

<td colspan='5'>
    <div id="contain-season"></div>
</td>
</tr>

<tr>

	<td colspan='5'><div class="right label">Status: </div></td>
    <td colspan='5'><span id="span-ready">Ready</span>
    	<span class="hidden" id="span-waiting"><img src="/assets/images/ajax-loader.gif" /></span>
    	<span class="hidden" id="span-saved">Save complete.  Would you like to publish to the league website?</span>
    	<span class="hidden" id="span-published">Publish success</span>
    	<span class="hidden" id="span-error">Permissions or database connection error found.  
    	Make sure you still have the correct 
    	league selected in the panel.</span>
    </td>

</tr>
</table>


<div class="hidden right label btn" id="div-publish-btn"><button id='btn-man-publish'>Publish</button></div>


</div>
