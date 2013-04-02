<form action="/index.php/finance/json_pay_direct_deposit_cc" method="post" name='cc_payment_form'>
<?
echo '<pre>';
echo var_dump($_REQUEST);
echo '</pre>';
?>
<table width="100%" style="font-family:verdana;font-size:10px;font-weight: bold;">
<tr>
    <td colspan="6" style="color: red;">
    <h3>Credit Card Information</h3>
    </td>
</tr>
<tr>
    <td colspan="2">
        <span>Name (As it appears on the card)</span>
        <div><input type="text" id="cardname" name="cardname" autocomplete="off" value="" /></div>
    </td>
    <td colspan="2">
        <span>Credit Card Number</span>
        <div><input type="text" id="cardnumber" name="cardnumber" autocomplete="off" value=""/></div>
        
    </td>
    <td colspan="2">
    </td>
</tr>
<tr>
    <td colspan="2">
        <span>CVV</span>
        <div><input type="text" id="cvv" name="cvv" autocomplete="off" /></div>
    </td>
    <td colspan="2">
        <span>Card Type</span>
        <div>
            <select name="payment_type_id"> 
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
                <select id="expirymonth"  name="expirymonth"> 
                    <option  value=""></option>
                    <option  value="01">January</option>
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
                <select id="expiryyear"  name="expiryyear"> 
                    <option  value=""></option>
                    <?for($y=0,$x=date("Y"); $y<12; $y++, $x++):?>
                        <option  value="<?=substr($x,2)?>"><?=$x?></option>
                    <?endfor;?>
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
            <option  value="BC">British Columbia</option>        
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
</tr>
<tr>
    <td colspan="4">
            <span>Street</span>
            <div ><input type="text" name="street" /></div>
    </td>
</tr>
<tr>
    <td colspan="2">
            <span>PostalCode</span>
            <div><input type="text" name="postalcode" /></div>
    </td>
</tr>               




<!--Submit button-->
<tr>
    <td colspan="4">
    <hr>
    <div class="button" id="submit_btn"><a onclick="if(checkForm())document.cc_payment_form.submit();"><span>Submit Payment</span></a></div>
    </td>
</tr>
</table>

<!--build hidden parems to be posted lated -->
<?foreach($params as $i=>$v):?>
<input type="hidden" name="<?=$i?>" value="<?=$v?>">
<?endforeach;?>

</form>









<script type="text/javascript">
function checkForm()
{
    if(document.getElementById("cardname").value=='')
    {
        alert('Card name Empty');
        return false;
    }    
    if(document.getElementById("cardnumber").value=='')
    {
        alert('Card Number Empty');
        return false;
    }
    if(document.getElementById("cvv").value=='')
    {
        alert('CVV Empty');
        return false;
    }
    if(document.getElementById("expirymonth").value=='')
    {
        alert('Expiry Month Empty');
        return false;
    }
    if(document.getElementById("expiryyear").value=='')
    {
        alert('Expiry Year Empty');
        return false;
    }
    return true;
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
    -webkit-border-radius:0px;
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
    -webkit-border-radius:0px;   
    width       : 170
}                                                 

select
{
    background  :#fff; 
    border      : 1px solid #ccc;
    padding     :3px 2px; 
    -webkit-border-radius:0px;   
    width       : 170
}                                                 

</style>
