<table width=100%>
<tr>
    <td width=50%>
                <div id="League-mr-teams-btn-DIV-createnew" class="form-field">
                    <div id="League-mr-teams-btn-createnew"> </div>
                </div>
    </td>
</tr>
<tr>
    <td width=50%>
                <div id="League-mr-teams-btn-DIV-role-type" class="form-field">
                    <div id="League-mr-teams-btn-role-type"> </div>
                </div>
    </td>
</tr>
<tr> 
    <td width=50%>
                <div id="League-mr-teams-btn-DIV" class="form-field">
                    <div id="League-mr-teams-btn"> </div>
                </div>
    </td>
</tr>
<tr>
     <td width=50%>
                <div id="League-mr-fname-DIV" class="form-field">
                    <div id="League-mr-fname-label">First Name</div>
                    <div id="League-mr-fname-input" class="input">
                        <input type="text" id="League-mr-fname"  />
                    </div>
                </div>
    </td>
    <td width=50%>
                <div id="League-mr-lname-DIV" class="form-field">
                    <div id="League-mr-lname-label">Last Name</div>
                    <div id="League-mr-lname-input" class="input">
                        <input type="text" id="League-mr-lname"  />
                    </div>
                </div>
    </td>
</tr>
<tr>
     <td width=50%>
                <div id="League-mr-homef-DIV" class="form-field">
                    <div id="League-mr-homef-label">Home Phone</div>
                    <div id="League-mr-homef-input" class="input">
                        <input type="text" id="League-mr-homef"  />
                    </div>
                </div>
    </td>
    <td width=50%>
                <div id="League-mr-workf-DIV" class="form-field">
                    <div id="League-mr-workf-label">Work Phone</div>
                    <div id="League-mr-workf-input" class="input">
                        <input type="text" id="League-mr-workf"  />
                    </div>
                </div>
    </td>
</tr>
<tr>
     <td width=50%>
                <div id="League-mr-cellf-DIV" class="form-field">
                    <div id="League-mr-cellf-label">Cell Phone</div>
                    <div id="League-mr-cellf-input" class="input">
                        <input type="text" id="League-mr-cellf"  />
                    </div>
                </div>
    </td>
    <td width=50%>
                
                    <div id="League-mr-addr-label">Address</div>
                    <div id="League-mr-addr"></div>
                
    </td>
</tr>
<tr>
     <td width=50%>
                <div id="League-mr-email-DIV" class="form-field">
                    <div id="League-mr-email-label">Email</div>
                    <div id="League-mr-email-input" class="input">
                        <input type="text" id="League-mr-email"  />
                    </div>
                </div>
    </td>       
    <td width=50%>
                
                <div id="League-mr-bdate-label">Birth Date</div>
                <div id="League-mr-bdate1"></div>
    </td>       
</tr>
<tr>
    <td width=50%>
                <div id="League-mr-gender-label">Gender</div>
                <input name="League-mr-gender-g" id=League-mr-gender-m type="radio" value=M checked>Male</input> 
                <input name="League-mr-gender-g" id=League-mr-gender-f type="radio" value=F>Female</input>  
    </td>
    
</tr>

</table>
<select id="League-mr-addr-menu"  > 
    <?foreach($addr_list as $v):?>
    <?='<option  value="'.$v['addr_name'].'">'.$v['addr_name'].'</option>'?>
    <?endforeach;?>
</select> 
<select id="League-mr-teams-btn-menu-createnew" > 
    <?foreach($team_list as $v):?>
    <?='<option  value="'.$v['team_id'].'">'.$v['team_name'].'</option>'?>
    <?endforeach;?>
</select> 
<select id="League-mr-teams-btn-menu-role-type" > 
    <?foreach($role_type_list as $v):?>
    <?='<option  value="'.$v['type_id'].'">'.$v['type_name'].'</option>'?>
    <?endforeach;?>
</select> 
