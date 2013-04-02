<input type="submit" id="btn_league" name="btn_league_button" value="Select an association"> 
<select id="btn_league_menu"  multiple> 
    
    <?foreach($ass_list as $v):?>
        <?='<option value='.$v['entity_id'].'> '.$v['org_name'].' </option>' ?> 
    <?endforeach;?>
</select> 
   
<div class="datatable" width=400px>
    <div id="dt-pag-leagues"></div>
    <div id="dt-leagues"></div>
</div>