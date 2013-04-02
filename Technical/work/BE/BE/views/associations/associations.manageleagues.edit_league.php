<table width='100%'>
<!-- leaguename section-->
<tr>
<td width='10%'></td><td width='10%'></td>
<td width='10%'></td><td width='10%'></td>
<td width='10%'></td><td width='10%'></td>
<td width='10%'></td><td width='10%'></td>
<td width='10%'></td><td width='10%'></td>
</tr>
<tr>
<td colspan="10">
    <div class="form-field">
        <span class="label">League name</span>
        <div class="input"> <input disabled="disabled" type="text" id="ml-details-leaguename" /></div>
    </div>
</td>
</tr>
<tr>
<td colspan="4">
    <div class="form-field">
        <span class="label">Website Prefix</span>
        <div class="input"> <input disabled="disabled" type="text" id="ml-details-websiteprefix" onkeyup="manage_leagues.accept_domain(this.value);" /></div>
    </div>
</td>
<td>
    <div class="form-field">
        <div class="label">Domain Name</div>
        <div id="MLeagues-domainlist"></div>
    </div>
</td>                                      
<td align="left">
    <div style="text-align: left;" id="MLeagues-domain-accept"></div>
</td>         

</tr>
<tr>
<td colspan="10">
    <div class="form-field">
        <span class="label">Address</span>
        <div class="input"> <input type="text" id="ml-details-lga-address" /></div>
    </div>
</td>
         
</tr>
<tr>
<td colspan='4'>
    <div class="form-field">
        <span class="label">City</span>
        <div class="input"> <input type="text" id="ml-details-lga-city" /></div>
    </div>
</td> 
<td colspan='2'>
    <div class="form-field">
        <span class="label">Region</span>
        <div class="input">       
        <select id="ml-details-lga-region">
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
<td colspan='2'>
    <div class="form-field">
        <span class="label">Country</span>
        <div class="input">               
        <select id="ml-details-lga-country">
        <option value=''></option>
        <option value='CA'>CANADA</option>
        <option value='USA'>USA</option>
        </select>
        </div>
    </div>
</td>
<td colspan='2'>
    <div class="form-field">
        <span class="label">PostalCode</span>
        <div class="input"> <input type="text" id="ml-details-lga-postalcode" /></div>
    </div>
</td>          
</tr>
    
</table>
 

<select id="MLeagues-domainlist-menu"> 
<option value='nsalive.com'>nsalive.com</option>
<option value='nsalive2.com'>nsalive2.com</option>
</select>
