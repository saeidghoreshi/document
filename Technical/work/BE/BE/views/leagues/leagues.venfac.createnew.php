<table width="100%" cellpadding="1" cellspacing="0">    
<tr>
    <td>
    <div id="VF_createnew_map1" style="width: 100%; height: 230px;"></div>
    </td>
</tr>
<tr>
    <td>
    <input align=left type="radio" name="VF-select-group"  onclick="javascript:YAHOO.util.Dom.get('VF_venue_div').style.display='block';YAHOO.util.Dom.get('VF_facility_div').style.display='none';" id=VF_venue checked="checked">New Venue</input>
    <input align=left type="radio" name="VF-select-group"  onclick="javascript:YAHOO.util.Dom.get('VF_venue_div').style.display='none';YAHOO.util.Dom.get('VF_facility_div').style.display='block';" id=VF_facility>New Facility</input>
    </td>
</tr>
<tr>
    <td>
    <div id="VF_venue_div"  >
    <table>
    <tr>
        <td>
             <div id="VF-venue-name" class="form-field">
                <div id="VF-venue-name-label">Venue Name</div>
                <div id="VF-venue-name-input" class="input">
                    <input type="text" id="VF-name" />
                </div>
            </div>
        </td>
        <td>
        <div id="VF-venue-type" class="form-field">
                <div id="VF-venue-type-label">Venue Type</div>
                <div id="VF-venue-type-select" class="input">
                    <select  id="VF-type" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
                         
                         <?foreach($venue_types as $v):?>  
                         <?='<option value='.$v['id'].'> '.$v['name'].' </option>' ?> 
                         <?endforeach;?>  
                        
                    </select>
                </div>
        </div>
        </td>
        <td>
        <div id="VF-venue-facility" class="form-field">
                <div id="VF-venue-facility-label">Facility</div>
                <div id="VF-venue-facility-select" class="input">
                    <select  id="VF-facility" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
                         
                         <?foreach($facilities_list as $v):?>  
                         <?='<option value='.$v['id'].'> '.$v['name'].' </option>' ?> 
                         <?endforeach;?>  
                        
                    </select>
                </div>
        </div>
        </td>
    </tr>
    </table>
    </div>
    
    </td>
   
</tr>
<tr>
     <td>
     <div id="VF_facility_div"  style="display: none;">
     <table>
    <tr>
        <td>
             <div id="VF-facility-name" class="form-field">
                <div id="VF-facility-name-label">Facility Name</div>
                <div id="VF-facility-name-input" class="input">
                    <input type="text" id="VF-name" />
                </div>
            </div>
        </td>
        <td>
        <div id="VF-venue-type" class="form-field">
                <div id="VF-facility-org-label">Organization Type</div>
                <div id="VF-facility-org-select" class="input">
                    <select  id="VF-org" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
                         
                         <?foreach($organizations as $v):?>  
                         <?='<option value='.$v['id'].'> '.$v['name'].' </option>' ?> 
                         <?endforeach;?>  
                        
                    </select>
                </div>
        </div>
        </td>
        <td>
        <div id="VF-facility-address" class="form-field">
                <div id="VF-facility-address-label">Address</div>
                <div id="VF-facility-address-select" class="input">
                    <select  id="VF-address" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
                         
                         <?foreach($addresses as $v):?>  
                         <?='<option value='.$v['id'].'> '.$v['name'].' </option>' ?> 
                         <?endforeach;?>  
                        
                    </select>
                </div>
        </div>
        </td>
    </tr>
    </table> 
     </div>
    
    </td>
</tr>
<tr>
    <td>
    <div id=VF_createnew_save></div>
    </td>

</tr>
</table>