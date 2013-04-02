<?php
  
?>
<table>

<!-- League manager section-->
<tr>
<td>
    <div class="form-field">
        <span class="label">First name</span>
        <div class="input"> <input type="text" id="ml-details-firstname" /></div>
    </div>
</td>
<td>
    <div class="form-field">
        <span class="label">Last Name</span>
        <div class="input"> <input type="text" id="ml-details-lastname" /></div>
    </div>
</td>
<td>
    <div class="form-field">
        <span class="label">Gender</span>
        <div class="input"> 
        <select id="ml-details-gender">
        <option value=''></option>
        <option value='m'>M</option>
        <option value='f'>F</option>
        </select>
        </div>
    </div>
</td>          
<td colspan="2">
    <div class="form-field">
        <span class="label">BirthDate</span>
        <div id="ml-details-birthdate"></div>
    </div>
</td>           
<tr/>
<tr>

<td>
    <div class="form-field">
        <span class="label">Login</span>
        <div class="input"> <input type="text" id="ml-details-username" /></div>
    </div>
</td>
<td>
    <div class="form-field">
        <span class="label">Password</span>
        <div class="input"> <input type="password" id="ml-details-password" /></div>
    </div>
</td>
<td>
    <div class="form-field">
        <span class="label">Confirm Password</span>
        <div class="input"> <input type="password" id="ml-details-confpassword" /></div>
    </div>

</td>          

</tr>
<tr>
<td colspan="2">
    <div class="form-field">
        <span class="label">Address</span>
        <div class="input"> <input type="text" id="ml-details-address" /></div>
    </div>
</td>  
<td>
    <div class="form-field">
        <span class="label">City</span>
        <div class="input"> <input type="text" id="ml-details-city" /></div>
    </div>
</td>
<td>
    <div class="form-field">
        <span class="label">Region</span>
        <div class="input">
        <select id="ml-details-region">
        <option value=''></option>
        <option value='BC'>BC</option>
        <option value='AB'>AB</option>
        <option value='SK'>SK</option>
        <option value='MB'>MB</option>
        <option value='ON'>ON</option>
        <option value='QC'>QC</option>
        <option value='NB'>NB</option>
        <option value='NL'>NL</option>
        <option value='NT'>NT</option>
        <option value='NS'>NS</option>
        <option value='NU'>NU</option>
        <option value='PE'>PE</option>
        <option value='YT'>YT</option>
        </select>
   </div>
    </div>
</td>          
     
<td>
    <div class="form-field">
        <span class="label">Country</span>
        <div class="input">           
        <select id="ml-details-country">
        <option value=''></option>
        <option value='CA'>CANADA</option>
        <option value='USA'>USA</option>
        </select>
        </div>
    </div>
</td>
<td>
    <div class="form-field">
        <span class="label">Postalcode</span>
        <div class="input"> <input type="text" id="ml-details-postalcode" /></div>
    </div>
</td>          
        
</tr>

<tr>
<td colspan="2">
    <div class="form-field">
        <span class="label">Email</span>
        <div class="input"> <input type="text" id="ml-details-email" /></div>
    </div>
</td>          

<td>
    <div class="form-field">
        <span class="label">Home Phone</span>
        <div class="input"> <input type="text" id="ml-details-homef" /></div>
    </div>
</td>          
<td>
    <div class="form-field">
        <span class="label">Cell Phone</span>
        <div class="input"> <input type="text" id="ml-details-cellf" /></div>
    </div>
</td>
<td>
    <div class="form-field">
        <span class="label">Work Phone</span>
        <div class="input"> <input type="text" id="ml-details-workf" /></div>
    </div>
</td>     
</tr>
<tr>
<td colspan="5">
    <div class="form-field">
        <span class="label">Select existing user as league manager from list ?</span>
        <input type="checkbox" id="ml-details-users-dt-viewable"  onclick="manage_leagues.handle_users();"/>
    </div>
    
    <div class="datatble" width="100%" id="ml-details-users-dt-div">
        <div id="ml-details-users-dt"></div>
        <div id="ml-details-users-dt-pag"></div>
    </div>
</td>     
</tr>
           
            
</table>
           
