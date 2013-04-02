<table width=100%>
<tr>
    <td width=50%>
                <div id="League-mr-teams-btn-DIV-active" class="form-field">
                    <div id="League-mr-teams-btn-active"> </div>
                </div>
    </td>
</tr>
<tr>
    <td >
    <div class="datatable" >
    <div id="League-dt-pag-rosters-active"></div>
    <div id="League-dt-rosters-active"></div>
    </div>
    </td>
</tr>
</table>               
<select id="League-mr-teams-btn-menu-active" > 
    <?foreach($team_list as $v):?>
    <?='<option  value="'.$v['team_id'].'">'.$v['team_name'].'</option>'?>
    <?endforeach;?>
</select> 

