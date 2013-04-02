<table width=100%>
<tr>
    <td width=50%>
                <div id="League-mt-teams-btn-DIV-createnew" class="form-field">
                    <div id="League-mt-teams-btn-createnew"> </div>
                </div>
    </td>
</tr>
<tr>
    <td width=50%>
                <div id="League-mt-teams-btn-DIV-role-type" class="form-field">
                    <div id="League-mt-teams-btn-role-type"> </div>
                </div>
    </td>
</tr>
<tr> 
    <td width=50%>
                <div id="League-mt-teams-btn-DIV" class="form-field">
                    <div id="League-mt-teams-btn"> </div>
                </div>
    </td>
</tr>
<tr>
     <td width=50%>
                <div id="League-mt-fname-DIV" class="form-field">
                    <div id="League-mt-fname-label">First Name</div>
                    <div id="League-mt-fname-input" class="input">
                        <input type="text" id="League-mt-fname"  />
                    </div>
                </div>
    </td>
    <td width=50%>
                <div id="League-mt-lname-DIV" class="form-field">
                    <div id="League-mt-lname-label">Last Name</div>
                    <div id="League-mt-lname-input" class="input">
                        <input type="text" id="League-mt-lname"  />
                    </div>
                </div>
    </td>
</tr>
<tr>
     <td width=50%>
                <div id="League-mt-homef-DIV" class="form-field">
                    <div id="League-mt-homef-label">Home Phone</div>
                    <div id="League-mt-homef-input" class="input">
                        <input type="text" id="League-mt-homef"  />
                    </div>
                </div>
    </td>
    <td width=50%>
                <div id="League-mt-workf-DIV" class="form-field">
                    <div id="League-mt-workf-label">Work Phone</div>
                    <div id="League-mt-workf-input" class="input">
                        <input type="text" id="League-mt-workf"  />
                    </div>
                </div>
    </td>
</tr>
<tr>
     <td width=50%>
                <div id="League-mt-cellf-DIV" class="form-field">
                    <div id="League-mt-cellf-label">Cell Phone</div>
                    <div id="League-mt-cellf-input" class="input">
                        <input type="text" id="League-mt-cellf"  />
                    </div>
                </div>
    </td>
    <td width=50%>
                
                    <div id="League-mt-addr-label">Address</div>
                    <div id="League-mt-addr"></div>
                
    </td>
</tr>
<tr>
     <td width=50%>
                <div id="League-mt-email-DIV" class="form-field">
                    <div id="League-mt-email-label">Email</div>
                    <div id="League-mt-email-input" class="input">
                        <input type="text" id="League-mt-email"  />
                    </div>
                </div>
    </td>       
    <td width=50%>
                
                <div id="League-mt-bdate-label">Birth Date</div>
                <div id="League-mt-bdate1"></div>
    </td>       
</tr>
<tr>
    <td width=50%>
                <div id="League-mt-gender-label">Gender</div>
                <input name="League-mt-gender-g" id=League-mt-gender-m type="radio" value=M checked>Male</input> 
                <input name="League-mt-gender-g" id=League-mt-gender-f type="radio" value=F>Female</input>  
    </td>
    
</tr>

</table>
<select id="League-mt-addr-menu"  > 
    <?foreach($addr_list as $v):?>
    <?='<option  value="'.$v['addr_name'].'">'.$v['addr_name'].'</option>'?>
    <?endforeach;?>
</select> 
<select id="League-mt-teams-btn-menu-createnew" > 
    <?foreach($team_list as $v):?>
    <?='<option  value="'.$v['team_id'].'">'.$v['team_name'].'</option>'?>
    <?endforeach;?>
</select> 
<select id="League-mt-teams-btn-menu-role-type" > 
    <?foreach($role_type_list as $v):?>
    <?='<option  value="'.$v['type_id'].'">'.$v['type_name'].'</option>'?>
    <?endforeach;?>
</select> 
