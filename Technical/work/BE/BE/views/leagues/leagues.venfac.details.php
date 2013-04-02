<?/*<
<div class="remove-tab-padding">
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top"><img src="/assets/images/sidebar_169x465/foul_pole.jpg"/></td>
	<td width="100%" valign="top" class="padwrap">

		<table width="100%" cellpadding="0" cellspacing="0">    
		<tr>
		    <td>
		    <div id="VF_createnew_map1" style="width: 100%; height: 200px;"></div>
		    </td>
		</tr>
		<tr>
		    <td>
		    <div id="VF-choice-div">
		    <input align=left type="radio" name="VF-select-group"  onclick="javascript:YAHOO.util.Dom.get('VF_venue_div').style.display='block';YAHOO.util.Dom.get('VF_facility_div').style.display='none';" id=VF_venue checked="checked">New Venue</input>
		    <input align=left type="radio" name="VF-select-group"  onclick="javascript:YAHOO.util.Dom.get('VF_venue_div').style.display='none';YAHOO.util.Dom.get('VF_facility_div').style.display='block';" id=VF_facility>New Facility</input>
		    </div>
		    
		    </td>
		</tr>
		<tr>
		    <td>
		    <div id="VF_venue_div"  >
		    <table width="100%" cellpadding="1" cellspacing="0">
		    <tr>
		        <td>
		             <div id="VF-venue-name-div" class="form-field">
		                <div id="VF-venue-name-label">Venue Name</div>
		                <div id="VF-venue-name-input" class="input">
		                    <input type="text" id="VF-venue-name" />
		                </div>
		            </div>
		        </td>
		        <td>
		        <div id="VF-venue-type-div" class="form-field">
		                <div id="VF-venue-type-label">Venue Type</div>
		                <div id="VF-venue-type-select" class="input">
		                    <select  id="VF-venue-type" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
		                         
		                         <?foreach($venue_types as $v):?>  
		                         <?='<option value='.$v['id'].'> '.$v['name'].' </option>' ?> 
		                         <?endforeach;?>  
		                        
		                    </select>
		                </div>
		        </div>
		        </td>
		        <td>
		        <div id="VF-venue-facility-div" class="form-field">
		                <div id="VF-venue-facility-label">Facility</div>
		                <div id="VF-venue-facility-select" class="input">
		                    <select  id="VF-venue-facility" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
		                         
		                         <?foreach($facilities_list as $v):?>  
		                         <?='<option value='.$v['id'].'> '.$v['name'].' </option>' ?> 
		                         <?endforeach;?>  
		                        
		                    </select>
		                </div>
		        </div>
		        </td>
		    </tr>
		    <tr>
		    <td>
		            <div id="VF-venue-latitude-div" class="form-field">
		                <div id="VF-venue-latitude-label">Latitude</div>
		                <div id="VF-venue-latitude-input" class="input">
		                    <input type="text" id="VF-venue-latitude" disabled="disabled" />
		                </div>
		            </div>
		    </td>
		    <td>
		            <div id="VF-facility-venue-div" class="form-field">
		                <div id="VF-facility-venue-label">Longitude</div>
		                <div id="VF-facility-venue-input" class="input">
		                    <input type="text" id="VF-venue-longitude" disabled="disabled" />
		                </div>
		            </div>
		    </td>
		    <td></td>
		    </tr>
		    </table>
		    </div>
		    
		    </td>
		   
		</tr>
		<tr>
		     <td>
		     <div id="VF_facility_div"  style="display: none;">
		     <table width="100%" cellpadding="1" cellspacing="0">
		    <tr>
		        <td>
		             <div id="VF-facility-name-div" class="form-field">
		                <div id="VF-facility-name-label">Facility Name</div>
		                <div id="VF-facility-name-input" class="input">
		                    <input type="text" id="VF-facility-name" />
		                </div>
		            </div>
		        </td>
		        <td>
		        <div id="VF-facility-org-div" class="form-field">
		                <div id="VF-facility-org-label">Organization Type</div>
		                <div id="VF-facility-org-select" class="input">
		                    <select  id="VF-facility-org" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
		                         
		                         <?foreach($organizations as $v):?>  
		                         <?='<option value='.$v['id'].'> '.$v['name'].' </option>' ?> 
		                         <?endforeach;?>  
		                        
		                    </select>
		                </div>
		        </div>
		        </td>
		        <td>
		        <div id="VF-facility-address-div" class="form-field">
		                <div id="VF-facility-address-label">Address</div>
		                <div id="VF-facility-address-select" class="input">
		                    <select  id="VF-facility-address" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
		                         
		                         <?foreach($addresses as $v):?>  
		                         <?='<option value='.$v['id'].'> '.$v['name'].' </option>' ?> 
		                         <?endforeach;?>  
		                        
		                    </select>
		                </div>
		        </div>
		        </td>
		    </tr>
		    <tr>
		    <td>
		            <div id="VF-facility-latitude-div" class="form-field">
		                <div id="VF-facility-latitude-label">Latitude</div>
		                <div id="VF-facility-latitude-input" class="input">
		                    <input type="text" id="VF-facility-latitude" disabled="disabled" />
		                </div>
		            </div>
		    </td>
		    <td>
		            <div id="VF-facility-longitude-div" class="form-field">
		                <div id="VF-facility-longitude-label">Longitude</div>
		                <div id="VF-facility-longitude-input" class="input">
		                    <input type="text" id="VF-facility-longitude" disabled="disabled" />
		                </div>
		            </div>
		    </td>
		    <td></td>
		    </tr>
		    </table> 
		     </div>
		    
		    </td>
		</tr>
		<tr>
		    <td>
		    <div id=VF_details_save></div>
		    </td>

		</tr>
		</table>
	</td>
======= */?>
<div class=window>
<table width="100%" cellpadding="1" cellspacing="0">    
<tr>
    <td>
    <div id="VF_createnew_map1" style="width: 100%; height: 200px;"></div>
    </td>
</tr>
<tr>
    <td>
    <div id="VF-choice-div">
    <input align=left type="radio" name="VF-select-group"  onclick="javascript:YAHOO.util.Dom.get('VF_venue_div').style.display='block';YAHOO.util.Dom.get('VF_facility_div').style.display='none';" id=VF_venue checked="checked">New Venue</input>
    <input align=left type="radio" name="VF-select-group"  onclick="javascript:YAHOO.util.Dom.get('VF_venue_div').style.display='none';YAHOO.util.Dom.get('VF_facility_div').style.display='block';" id=VF_facility>New Facility</input>
    </div>
    
    </td>
</tr>
<tr>
    <td>
    <div id="VF_venue_div"  >
    <table width="100%" cellpadding="1" cellspacing="0">
    <tr>
        <td>
             <div id="VF-venue-name-div" class="form-field">
                <div id="VF-venue-name-label">Venue Name</div>
                <div id="VF-venue-name-input" class="input">
                    <input type="text" id="VF-venue-name" />
                </div>
            </div>
        </td>
        <td>
        <div id="VF-venue-type-div" class="form-field">
                <div id="VF-venue-type-label">Venue Type</div>
                <div id="VF-venue-type-select" class="input">
                    <select  id="VF-venue-type" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
                         
                         <?foreach($venue_types as $v):?>  
                         <?='<option value='.$v['id'].'> '.$v['name'].' </option>' ?> 
                         <?endforeach;?>  
                        
                    </select>
                </div>
        </div>
        </td>
        <td>
        <div id="VF-venue-facility-div" class="form-field">
                <div id="VF-venue-facility-label">Facility</div>
                <div id="VF-venue-facility-select" class="input">
                    <select  id="VF-venue-facility" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
                         
                         <?foreach($facilities_list as $v):?>  
                         <?='<option value='.$v['id'].'> '.$v['name'].' </option>' ?> 
                         <?endforeach;?>  
                        
                    </select>
                </div>
        </div>
        </td>
    </tr>
    <tr>
    <td>
            <div id="VF-venue-latitude-div" class="form-field">
                <div id="VF-venue-latitude-label">Latitude</div>
                <div id="VF-venue-latitude-input" class="input">
                    <input type="text" id="VF-venue-latitude" disabled="disabled" />
                </div>
            </div>
    </td>
    <td>
            <div id="VF-facility-venue-div" class="form-field">
                <div id="VF-facility-venue-label">Longitude</div>
                <div id="VF-facility-venue-input" class="input">
                    <input type="text" id="VF-venue-longitude" disabled="disabled" />
                </div>
            </div>
    </td>
    <td></td>
    </tr>
    </table>
    </div>
    
    </td>
   
</tr>
<tr>
     <td>
     <div id="VF_facility_div"  style="display: none;">
     <table width="100%" cellpadding="1" cellspacing="0">
    <tr>
        <td>
             <div id="VF-facility-name-div" class="form-field">
                <div id="VF-facility-name-label">Facility Name</div>
                <div id="VF-facility-name-input" class="input">
                    <input type="text" id="VF-facility-name" />
                </div>
            </div>
        </td>
        <td>
        <div id="VF-facility-org-div" class="form-field">
                <div id="VF-facility-org-label">Organization Type</div>
                <div id="VF-facility-org-select" class="input">
                    <select  id="VF-facility-org" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
                         
                         <?foreach($organizations as $v):?>  
                         <?='<option value='.$v['id'].'> '.$v['name'].' </option>' ?> 
                         <?endforeach;?>  
                        
                    </select>
                </div>
        </div>
        </td>
        <td>
        <div id="VF-facility-address-div" class="form-field">
                <div id="VF-facility-address-label">Address</div>
                <div id="VF-facility-address-select" class="input">
                    <select  id="VF-facility-address" style="padding:2px; background-color:#FFFFFF;  border:1px solid #666666; width: 100%;" >
                         
                         <?foreach($addresses as $v):?>  
                         <?='<option value='.$v['id'].'> '.$v['name'].' </option>' ?> 
                         <?endforeach;?>  
                        
                    </select>
                </div>
        </div>
        </td>
    </tr>
    <tr>
    <td>
            <div id="VF-facility-latitude-div" class="form-field">
                <div id="VF-facility-latitude-label">Latitude</div>
                <div id="VF-facility-latitude-input" class="input">
                    <input type="text" id="VF-facility-latitude" disabled="disabled" />
                </div>
            </div>
    </td>
    <td>
            <div id="VF-facility-longitude-div" class="form-field">
                <div id="VF-facility-longitude-label">Longitude</div>
                <div id="VF-facility-longitude-input" class="input">
                    <input type="text" id="VF-facility-longitude" disabled="disabled" />
                </div>
            </div>
    </td>
    <td></td>
    </tr>
    </table> 
     </div>
    
    </td>
</tr>
<tr>
    <td>
    <div id='VF_details_save'></div>
    </td>

</tr>
</table>
</div>