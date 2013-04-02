<table width=100%>
<tr>
     <td >
                <div class="form-field">
                    <div >First Name</div>
                    <div class="input">
                        <input type="text" id="Team-mr-fname"  />
                    </div>
                </div>
    </td>
    <td >
                <div class="form-field">
                    <div >Last Name</div>
                    <div class="input">
                        <input type="text" id="Team-mr-lname"  />
                    </div>
                </div>
    </td>
</tr>

<tr>
    
    <td >
                <div class="form-field">                
                <div >Birth Year</div>
                <div class="input">
                <select id="Team-mr-byear" name="Team-mr-byear">
                    <option value=""></option>
                    <?for($i=2005;$i>1940;$i--):?><option value="<?=$i?>"><?=$i?></option><?endfor;?>
                </select>
                </div>
                </div>
    </td>
    <td>
                <div class="form-field">                
                <div >Birth Month</div>
                <div class="input"><select id="Team-mr-bmonth" name="Team-mr-bmonth">
                <option  value=""></option>
                <option  value="1">Jan</option>
                <option  value="2">Feb</option>
                <option  value="3">Mar</option>
                <option  value="4">Apr</option>
                <option  value="5">May</option>
                <option  value="6">Jun</option>
                <option  value="7">Jul</option>
                <option  value="8">Aug</option>
                <option  value="9">Sep</option>
                <option  value="10">Oct</option>
                <option  value="11">Nov</option>
                <option  value="12">Dec</option>
                </select></div>
                </div>
    </td>
    <td>
                <div class="form-field">                
                <div >Birth day</div>
                <div class="input">
                <select id="Team-mr-bday" name="Team-mr-bday">
                    <option value=""></option>
                    <?for($i=1;$i<=31;$i++):?><option value="<?=$i?>"><?=$i?></option><?endfor;?>
                </select>
                </select>
                </div>
                </div>
    </td>       
    <td width=200px>
                <div id="Team-mr-gender-label">Gender</div>
                <div class="input">
                <select id="Team-mr-gender">
                <option value=""></option>
                <option value="m">Male</option>
                <option value="f">Female</option>
                </select>
                </div>
    </td>
    
</tr>
<tr>
     <td >
                <div class="form-field">
                    <div >Home Phone</div>
                    <div class="input">
                        <input type="text" id="Team-mr-homef"  />

                    </div>
                </div>
    </td>
    <td >
                <div class="form-field">
                    <div >Work Phone</div>
                    <div class="input">
                        <input type="text" id="Team-mr-workf"  />

                    </div>
                </div>
    </td>
     <td >
                <div class="form-field">
                    <div >Cell Phone</div>
                    <div class="input">
                        <input type="text" id="Team-mr-cellf"  />

                    </div>
                </div>
    </td>
     <td >
                <div class="form-field">
                    <div >Email</div>
                    <div class="input">
                        <input type="text" id="Team-mr-email"  />

                    </div>
                </div>
    </td>       
</tr>
<tr>
    
    <td >
                
               <div class="form-field">
               <div >Address</div>
               <div class="input">
                   <input type="text" id="Team-mr-address"  />
               </div>
               </div>
    </td>
    <td>
               <div class="form-field">
               <div >City</div>
               <div class="input">
                   <input type="text" id="Team-mr-city"  />
               </div>             
               </div>
    </td>
    <td>
                <div class="form-field">
               <div >Province</div>
               <div class="input">
                    <select id="Team-mr-province" name="Team-mr-province" > 
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
                               
    <td>
               <div class="form-field">
               <div >Country</div>
               <div class="input">
                   <select id="Team-mr-country" name="Team-mr-country">
                   <option value=""></option>
                   <option value="CA">CANADA</option>
                   <option value="USA">USA</option>
                   </select>                             
               </div>
               </div>
    </td>
    <td>
               <div class="form-field">
               <div >Postal Code</div>
               <div class="input">
                   <input type="text" id="Team-mr-postal"  />
               </div>             
               </div>
    </td>
</tr>

</table>
