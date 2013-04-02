<input type="submit" id="btn_tournament" name="btn_tournament_button" value="Select an association"> 
<select id="btn_tournament_menu"  multiple> 
    
    <?foreach($ass_list as $v):?>
        <?='<option value='.$v['entity_id'].'> '.$v['org_name'].' </option>' ?> 
    <?endforeach;?>
</select> 
   
<div class="datatable" width=400px>
    <div id="dt-pag-tournaments"></div>
    <div id="dt-tournaments"></div>
</div>