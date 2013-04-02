<div id="team-window">




<table cellpadding="1" cellspacing="0">     
<tr>
    <td><input type="button" id="btn_team1" name="btn_team1_button" value="Select a league"> </td>
    <td><input type="button" id="btn_team2" name="btn_team2_button" value="Select a tournament"> </td>
</tr>
</table>                                              

<select id="btn_team1_menu"  multiple> 
    
    <?foreach($league_list as $v):?>
        <?='<option value='.$v['org_id'].'> '.$v['league_name'].' </option>' ?> 
    <?endforeach;?>
</select> 
<select id="btn_team2_menu"  multiple > 
    
    <?foreach($tournament_list as $v):?>
        <?='<option value='.$v['org_id'].'> '.$v['league_name'].' </option>' ?> 
    <?endforeach;?>
</select> 
   

   
   
   </div>