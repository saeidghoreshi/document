<table width="100%">
<tr>

    <td width="100%">
        
        <!-- Season Dropdown Container -->
        <div id="League-mr-season-btn"></div>
        <select id="League-mr-teams-btn-menu"> 
		    <?foreach($team_list as $v):?>
		    <?='<option  value="'.$v['team_id'].'">Team: '.$v['team_name'].'</option>'?>
		    <?endforeach;?>
		</select>
		
		<!-- Team Dropdown Container -->
		<div id="League-mr-teams-btn"></div>
		<select id="League-mr-season-btn-menu" > 
		    <?foreach($season_list as $v):?>
		    <?='<option  value="'.$v['season_id'].'">Season: '.$v['season_name'].'</option>'?>
		    <?endforeach;?>
		</select>
		
    </td>
    <td nowrap="nowrap"><input type="radio" name="League-mr-view-isactive-radio" id="League-mr-view-isactive-yes-radio" checked/> Active</td>
    <td nowrap="nowrap"><input type="radio"  name="League-mr-view-isactive-radio" id="League-mr-view-isactive-no-radio" /> Inactive</td>
    <td nowrap="nowrap"><div id="League-mr-view-btn"></div></td>
</tr>
<tr>
    <td colspan="4">
	    <div id="ctr-roster-default">
	    	<p>Please select a season and a team to display the roster. Thank you.</p>
	    </div>
	    <div id="ctr-dt-roster" class="datatable">
		    <div id="League-roster-persons-dt"></div>
		    <div class="dt-pag" id="League-roster-persons-dt-pag"></div>
		    <div class="btnset">
	    		<button id="League-roster-persons-edit">Edit Player</button>
	    		<button id="League-mr-new-person">Add Player</button>
		    </div>
	    </div>
    </td>
</tr>
</table>
