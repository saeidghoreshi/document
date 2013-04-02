<table width=100%>
<tr>
    <td width=50%>
                <div id="League-mr-teams-btn-DIV-inactive" class="form-field">
                    <div id="League-mr-teams-btn-inactive"> </div>
                </div>
    </td>
</tr>
<tr>
    <td >
    <div class="datatable" >
    <div id="League-dt-pag-rosters-inactive"></div>
    <div id="League-dt-rosters-inactive"></div>
    </div>
    </td>
</tr>
</table>                     
<select id="League-mr-teams-btn-menu-inactive" > 
    <?foreach($team_list as $v):?>
    <?='<option  value="'.$v['team_id'].'">'.$v['team_name'].'</option>'?>
    <?endforeach;?>
</select> 

