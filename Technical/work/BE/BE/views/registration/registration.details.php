<table width="100%">
<strong>Team and Season</strong>
<tr>
<td>
    <div  class="form-field" ><div >Season</div><div class="input"><?=$season_list?></div></div> 
</td> 

<td>
    <div  class="form-field">
                    <div >Team name</div>
                    <div class="input">
                    <input type="text" name="team_name" id="team_name" />                             
                    </div>
    </div> 
</td> 
<td >
    <div  class="form-field">
                    <div >Team Calibre</div>
                    <div class="input">
                    <select id="team_calibre" name="team_calibre">
                    <option value="High Competitive">High Competitive</option>
                    <option value="Competitive">Competitive</option>
                    <option value="Low Competitive">Low Competitive</option>
                    <option value="High Intermediate">High Intermediate</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Low Intermediate">Low Intermediate</option>
                    <option value="High Recreation">High Recreation</option>
                    <option value="Recreation">Recreation</option>
                    <option value="Low Recreation">Low Recreation</option>
                    <option value="Beginner">Beginner</option>
                </select>
                    </div>
    </div> 

</td>
</tr>                   
<strong>Manager</strong>
<tr>
<td >
    <div  class="form-field">
                    <div >First name</div>
                    <div class="input">
                    <input type="text" name="manager_firstname" id="manager_firstname" />                             
                    </div>
    </div> 
</td> 
<td >
    <div  class="form-field">
                    <div >Last name</div>
                    <div class="input">
                    <input type="text" name="manager_lastname" id="manager_lastname" />                             
                    </div>
    </div> 

</td>
<td>
    <div  class="form-field">
                    <div >Primary Phone</div>
                    <div class="input">
                    <input type="text" name="manager_primaryphone" id="manager_primaryphone"  />                             
                    </div>
    </div> 
      
</td>

</tr>
<tr>
<td >
    <div  class="form-field">
                    <div >Secondary Phone</div>
                    <div class="input">
                    <input type="text" name="manager_secondaryphone" id="manager_secondaryphone"  />                    
                    </div>
    </div> 

</td>
<td >
    <div  class="form-field">
                    <div >Email</div>
                    <div class="input">
                    <input type="text" name="manager_email" id="manager_email"  />                    
                    </div>
    </div> 

</td>
<td  >
     <div  class="form-field">
                    <div >Gender</div>
                    <div class="input">
                        <select id="manager_gender" name="manager_gender" > 
                        <option  value="0"></option>
                        <option  value="m">Male</option>
                        <option  value="f">Female</option>
                        </select> 
                    </div>
    </div>

</td>
<td >
    <div  class="form-field">
                    <div >Username</div>
                    <div class="input">
                    <input type="text" name="manager_username" id="manager_username"  />
                    </div>
    </div> 

</td>
</tr>
<strong>Operating Address</strong>
<tr>
<td >
    <div  class="form-field">
                    <div >Unit</div>
                    <div class="input">
                    <input type="text" name="operating_unit" id="operating_unit"  />
                    </div>
    </div> 
</td>
<td colspan="2">
    <div  class="form-field">
                    <div >Address</div>
                    <div class="input">
                    <input type="text" name="operating_address" id="operating_address"  />
                    </div>
    </div> 
</td>

</tr>
<tr>
<td >
    <div  class="form-field">
                    <div >City</div>
                    <div class="input">
                    <input type="text" name="operating_city" id="operating_city"  />
                    </div>
    </div> 
</td>
<td>
    <div class="form-field">                  
                    <div id="">Province</div>
                    <div id="" class="input">
                        <select id="operating_province" name="operating_province" > 
                        <option  value=""></option>
                        <option  value="BC">BC</option>
                        <option  value="AB">AB</option>
                        <option  value="SK">SK</option>
                        <option  value="MB">MB</option>
                        <option  value="ON">ON</option>
                        <option  value="QC">QC</option>
                        <option  value="NB">NB</option>
                        <option  value="NL">NL</option>
                        <option  value="NT">NT</option>
                        <option  value="NS">NS</option>
                        <option  value="PE">PE</option>
                        <option  value="YT">YT</option>
                        <option  value="ID">ID</option>
                        <option  value="NU">NU</option>

                        </select>             
                    </div>
    </div>                                                                      
</td> 
<td >
    <div  class="form-field">
                    <div >Country</div>
                    <div class="input">
                        <select id="operating_country" name="operating_country"> 
                        <option  value="0"></option>
                        <option  value="USA">USA</option>
                        <option  value="CANADA">CANADA</option>
                        </select> 
                    </div>
    </div>

</td>
<td >
    <div  class="form-field">
                    <div >Postal Code</div>
                    <div class="input">
                    <input type="text" name="operating_postalcode" id="operating_postalcode"  />
                    </div>
    </div> 
</td>
</tr>
<strong>Shipping Address</strong>
<tr>
<td >
    <div  class="form-field">
                    <div >Unit</div>
                    <div class="input">
                    <input type="text" name="shipping_unit" id="shipping_unit"  />
                    </div>
    </div> 
</td>
<td colspan="2">
    <div  class="form-field">
                    <div >Address</div>
                    <div class="input">
                    <input type="text" name="shipping_address" id="shipping_address"  />
                    </div>
    </div> 
</td>

</tr>
<tr>
<td >
    <div  class="form-field">
                    <div >City</div>
                    <div class="input">
                    <input type="text" name="shipping_city" id="shipping_city"  />
                    </div>
    </div> 
</td>
<td>
    <div class="form-field">                  
                    <div id="">Province</div>
                    <div id="" class="input">
                        <select id="shipping_province" name="shipping_province" > 
                        <option  value=""></option>
                        <option  value="BC">BC</option>
                        <option  value="AB">AB</option>
                        <option  value="SK">SK</option>
                        <option  value="MB">MB</option>
                        <option  value="ON">ON</option>
                        <option  value="QC">QC</option>
                        <option  value="NB">NB</option>
                        <option  value="NL">NL</option>
                        <option  value="NT">NT</option>
                        <option  value="NS">NS</option>
                        <option  value="PE">PE</option>
                        <option  value="YT">YT</option>
                        <option  value="ID">ID</option>
                        <option  value="NU">NU</option>

                        </select>             
                    </div>
    </div>                                                                      
</td> 
<td >
    <div  class="form-field">
                    <div >Country</div>
                    <div  class="input">
                        <select id="shipping_country" name="shipping_country"> 
                        <option  value=""></option>
                        <option  value="USA">USA</option>
                        <option  value="CANADA">CANADA</option>
                        </select> 
                    </div>
    </div>

</td>
<td >
    <div  class="form-field">
                    <div >Postal Code</div>
                    <div class="input">
                    <input type="text" name="shipping_postalcode" id="shipping_postalcode"  />
                    </div>
    </div> 
</td>
</tr>



</table>

 