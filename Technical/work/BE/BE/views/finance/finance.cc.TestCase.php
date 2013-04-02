<form action="/index.php/finance/json_pay_direct_CC_TEST" method="post" name='cc_payment_form'>
<table width="100%" style="font-family:verdana;font-size:10px;font-weight: bold;">
<tr>
    <td colspan="2">
        <div><input type="checkbox" id="cbSwitchAmt" onchange="switchAmt();"/></div>
        <span>Amount</span>
        <!--$_REQUEST["_amount"]-->
        <!--div><input style="width: 100;" type="text" id="desirable_amount" name="desirable_amount" value="" <?if(!isset($_REQUEST["changable_amt"]) || $_REQUEST["changable_amt"]=='false') echo 'disabled';?>/></div-->
        <table width="100%">
        <tr>
            <td>
                <div><input style="width: 150;" id="desirable_amount2" type="text" name="desirable_amount"  disabled  /></div>
            </td>
            <td>
                <div>
                <select id="desirable_amount1" name="desirable_amount" style="width: 150;">
                        <option  value="40">Valid Amount  </option>
                        <option  value="300">300  </option>
                        <option  value="15500">15500</option>
                        <option  value="1">1    </option>
                        <option  value="2">2    </option>
                        <option  value="3">3    </option>
                        <option  value="4">4    </option>
                        <option  value="5">5    </option>
                        <option  value="6">6    </option>
                        <option  value="7">7    </option>
                        <option  value="8">8    </option>
                        <option  value="9">9    </option>
                        <option  value="10">10   </option>
                        <option  value="11">11   </option>
                        <option  value="12">12   </option>
                        <option  value="13">13   </option>
                        <option  value="14">14   </option>
                        <option  value="15">15   </option>
                        <option  value="16">16   </option>
                        <option  value="17">17   </option>
                        <option  value="18">18   </option>
                        <option  value="19">19   </option>
                        <option  value="20">20   </option>
                        <option  value="21">21   </option>
                        <option  value="22">22   </option>
                        <option  value="23">23   </option>
                        <option  value="24">24   </option>
                        <option  value="25">25   </option>
                        <option  value="59">59   </option>
                        <option  value="92">92   </option>
                        <option  value="93">93   </option>
                        <option  value="94">94   </option>
                        <option  value="97">97   </option>
                        <option  value="98">98   </option>
                        <option  value="99">99   </option>
                        <option  value="0.25">0.25 </option>
                </select>
                </div>
            </td>
        </tr>
        </table>
        
    </td>
    <td colspan="4">
    </td>
</tr>



<tr>
    <td colspan="6" style="color: red;">
    <hr>
    <h3>Credit Card Information</h3>
    </td>
</tr>
<tr>
    <td colspan="2">
        <span>Name (As it appears on the card)</span>
        <div><input type="text" name="cardname" value="Test Name" /></div>
    </td>
    <td colspan="2">
        <span>Credit Card Number</span>
        <div><input type="text" id="cardnumber"  name="cardnumber" value="4788250000028291" /></div>
        
    </td>
    <td colspan="2">
    </td>
</tr>
<tr>
    <td colspan="2">
        <span>CVV</span>
        <div>
        <select name="cvv" >
                <option  value=""></option>
                <option  value="111">111 Match</option>
                <option  value="222">222 No Match</option>
                <option  value="333">333 Not Prodcessed</option>
                <option  value="444">444 Should have been present</option>
                <option  value="555">555 Issuer unable to process request.</option>
                <option  value="666">None (666)</option>
                <option  value="777">777 Decline Tran</option>            
        </select> 
        </div>
        


    </td>
    <td colspan="2">
        <span>Card Type</span>
        <div>
            <select name="payment_type_id" onchange="onPaymentTypeChange(this.value);">
                <option  value="3">Visa</option>
                <option  value="4">Master</option>
            </select> 
        </div>
    </td>
</tr>
<tr>
    <td colspan="2">
            <span>Expiry Month</span>
            <div>
                <select  name="expirymonth"> 
                    <option  value=""></option>
                    <option  value="01" selected>January</option>
                    <option  value="02">February</option>
                    <option  value="03">March</option>
                    <option  value="04">April</option>
                    <option  value="05">May</option>
                    <option  value="06">June</option>
                    <option  value="07">July</option>
                    <option  value="08">August</option>
                    <option  value="09">September</option>
                    <option  value="10">October</option>
                    <option  value="11">November</option>
                    <option  value="12">December</option>
                </select>
            </div>
    </td>
    <td colspan="1">
            <span>Year</span>
            <div>
                <select  name="expiryyear"> 
                    <option  value="12" selected>2012</option>
                    <option  value="13">2013</option>
                    <option  value="14">2014</option>
                    <option  value="15">2015</option>
                    <option  value="16">2016</option>
                    <option  value="17">2017</option>
                </select> 
            </div>
        
    </td>
    
</tr>               

<!--Addressing-->
<tr>
    <td colspan="6" style="color: red;">
    <hr>
    <h3>Credit Card Holder Address</h3>
    </td>
</tr>

</tr>
<tr>
    <td colspan="2">
        <span>Country</span>
        <div>
        <select name="country"> 
            <option  value="CA">Canada</option>        
        </select> 
        </div>
    </td>
    <td colspan="2">
        <span>State/Province</span>
        <div>
            <select name="region"> 
                <option  value="BC">British Colombia</option>        
                <option  value="AB">Alberta</option>        
                <option  value="SK">Saskatchwan</option>        
                <option  value="MB">Monitoba</option>        
                <option  value="ON">Ontario</option>        
                <option  value="QC">Quebec</option>        
                <option  value="NB">New Brunswick</option>        
                <option  value="NL">Newfoundland and Labrador</option>        
                <option  value="NT">Northwest Territories</option>        
                <option  value="NS">Nova Scotia</option>        
                <option  value="NU">Yukon Territory</option>        
                <option  value="PE">Prince Edward Island</option>        
                <option  value="YT">Nunavut</option>        
            </select>                      
        </div>
    </td>
</tr>
<tr>
    <td colspan="2">
            <span>City</span>
            <div ><input type="text" name="city" /></div>
    </td>
    <td colspan="2">
            <span>Street</span>
            <div ><input type="text" name="street" /></div>
    </td>
</tr>
<tr>
    <td colspan="2">
            <span>PostalCode</span>
            <div>
            <select name="postalcode"> 
                <option  value=""></option>        
                <option  value="11111">11111 Zip No Match/Zip 4 No Match/Locale match </option>        
                <option  value="33333">33333 No match at all </option>        
                <option  value="44444">44444 System unavailable or time-out </option>        
                <option  value="55555">55555 Address information unavailable </option>        
                <option  value="66666">66666 Zip Match/Locale match </option>        
                <option  value="77777">77777 Zip Match/Zip 4 Match/Address Match  / Zip Match/Locale no match </option>        
                <option  value="88888">88888 Issuer does not participate in AVS </option>        
                <option  value="L6L2X9">L6L2X9</option>        
                
            </select>                      
            </div>
    </td>
    <td colspan="2">
            <span>Phone Number</span>
            <div><input type="text" name="phone" /></div>
    </td>
</tr>               





<!--Submit button-->
<tr>
    <td colspan="4">
    <hr>
    <div class="button" id="submit_btn"><a onclick="document.cc_payment_form.submit();"><span>Submit Payment</span></a></div>
    </td>
</tr>
</table>

<?foreach($_REQUEST as $i=>$v):?>
<input type="hidden" name="<?=$i?>" value="<?=$v?>">
<?endforeach;?>

<!--@ the case desirable_amount is disabled-->
<?if(!isset($_REQUEST["changable_amt"]) || $_REQUEST["changable_amt"]=='false'):?>
<input type="hidden" name="desirable_amount" value="<?=$_REQUEST["_amount"]?>">
<?endif;?> 




</form>

<script type="text/javascript">
function onPaymentTypeChange(cardTypeId)
{
    if(cardTypeId==3)/*VISA*/
        document.getElementById("cardnumber").value='4788250000028291';
    else
        document.getElementById("cardnumber").value='5454545454545454';
}
function switchAmt()
{
    var cbSwitchAmt=document.getElementById("cbSwitchAmt");
    if(cbSwitchAmt.checked==true)
    {
        document.getElementById("desirable_amount1").disabled=true;
        document.getElementById("desirable_amount2").disabled=false;
    }   
    else
    {
        document.getElementById("desirable_amount2").disabled=true;
        document.getElementById("desirable_amount1").disabled=false;
    }
}

</script>                       
<style type="text/css">
.button {
    display:inline; 
    list-style:none; 
    margin-right:2px;
    font-family: verdana;
    font-size: 10;
    float: right;
}

.button a {
    background:#fff; 
    border: 1px solid #ccc;
    padding:3px 10px; 
    -webkit-border-radius:10px;
}

.button a:hover {
    background:#5d9ddd;
    border:1px solid #2a7ecd;
    color:#fff;  
    -webkit-border-radius:10px; 
    cursor: pointer;
} 
hr {
  width: 100%;
  color: #DFE8F6;
  margin: 7 7 7 7;
}
input
{
    background:#fff; 
    border: 1px solid #ccc;
    padding:3px 10px; 
    -webkit-border-radius:10px;   
    width       : 170
}                                                 

select
{
    background  :#fff; 
    border      : 1px solid #ccc;
    padding     :3px 2px; 
    -webkit-border-radius:10px;   
    width       : 170
}                                                 

</style>
