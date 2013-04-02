<div id='st-sd-tab'>

<h3>Include games between the following divisions</h3>

<table>

<tr>
	<td width='50%' valign='top'>
		<div class="datatable" >
			<div id="dt-ex-subdiv"></div>
			<div id='dt-ex-subdiv-pag'></div>
		</div>

	</td>
	<td width='50%' valign="top">
	<fieldset>
		<legend>Division Options</legend>

		 <div id='btn-group-div' class="yui-buttongroup">
			 <input id="internal-div" type="radio" name='radio-xdiv' value='Internal Division Games Only'/>  
			 <input id="external-div" type="radio" name='radio-xdiv' value='All Games' checked="checked"/>  
		 </div>
		 <br />
		 <input id="leadcut-div" type="text" value='0' size="2"/> Remove the top teams from each division;
		 
		 
		 <br />
		 Based on 
		 <select id='parent-rank-division'>

		 <?
		
		 foreach($p_ranks as $rank):?>
		 	<option value=<?=$rank['rank_type_id']?>><?=$rank['rank_name']?></option>
		 <?endforeach;?>
		 </select>
		 <div class="hidden" id="trailers">
 			Exclude Trailers?<input id="trailcut-div" type="text" value='0' size="2"/> Remove this many teams from the bottom of each division from the ranking? 
		</div>

	 </fieldset>
	</td>
</tr>


</table>




 
 
 </div>
