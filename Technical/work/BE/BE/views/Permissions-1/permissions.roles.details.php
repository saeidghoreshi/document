<table width=100% cellpadding="1" cellspacing="0"> 
<tr>
   <td width="33%">
            <div id="MR-role-name" class="form-field">
                <div id="MR-role-name-label">Name</div>
                <div id="MR-role-name-input" class="input">
                    <input type="text" id="MR-name" />
                </div>
            </div>
   </td>
   <td width="20%">
            <div id="MR-role-limit" class="form-field">
                <div id="MR-role-limit-label">Limit Location</div>
               
                    <input align=left type="radio" name="MR-limit-group"  onclick="javascript:YAHOO.util.Dom.get('MR-dt-locations-list-div').style.display='block';" id=MR-limit-yes>Yes</input>
                    <input align=left type="radio" name="MR-limit-group"  onclick="javascript:YAHOO.util.Dom.get('MR-dt-locations-list-div').style.display='none';" id=MR-limit-no>No</input>
               
            </div>
   </td>
   <td width="47%">
            <div id="MR-role-parent" class="form-field">
                <div id="MR-role-parent-label">Parent</div>
                <div id="MR-role-parent-select" class="input">
                    <select  id="MR-parent" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
                         
                         <?foreach($rolesname as $v):?>  
                         <?='<option value='.$v['role_id'].'> '.$v['name'].' </option>' ?> 
                         <?endforeach;?>  
                        
                    </select>
                </div>
            </div>
   </td>
</tr>
</table>
<div id="MR-delete-role-div">
<input type="checkbox" id="MR-delete-role" />Delete this role.Please Note: This will only remove the role from the primary list, not completly delete the item. Only an administrator may recover a deleted item.
</div>   
<table width="100%" cellpadding="1" cellspacing="0">    
<div id=MR-dt-locations-list-div class="datatable">
    <div align="center" id="MR-dt-pag-locations-list"></div>
    <div id="MR-dt-locations-list"></div>
</div>
</table>



