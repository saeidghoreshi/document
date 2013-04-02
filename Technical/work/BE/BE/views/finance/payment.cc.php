<style type="text/css">
                                                 
<?=file_get_contents('http://devbrad.global.playerspectrum.com/spectrum/views/themes/theme0001/css/structure.css');?>
<?=file_get_contents('http://devbrad.global.playerspectrum.com/spectrum/views/themes/theme0001/css/form.css');?>

body{ background-color:#FFFFFF; }

div.input span{ margin-bottom:-4px; }
div#amount{ font-size:14px; }
span#amt_number{ color:#148525; }
span#amt_desc{ font-size:10px; color:#666666;}

</style>
<html>

<body> 
<form action="/index.php/finance/json_pay_direct_deposit_cc/TOKEN:<?=$login_token?>" method="post" name='cc_payment_form'>

<input type='hidden' name='payment_token' value='<?=$paymentToken?>'/>
<input type='hidden' name='mode' value='<?=$mode?>'/>


<table class="form">
<tr>
    <td width="10%"></td><td width="10%"></td>
    <td width="10%"></td><td width="10%"></td>
    <td width="10%"></td><td width="10%"></td>
    <td width="10%"></td><td width="10%"></td>
    <td width="10%"></td><td width="10%"></td>
</tr>
<tr>
    <td colspan="5">
        <div class="input">
            <span>Credit Card Number</span>
            <div><input type="text" id="cardnumber" name="cardnumber" autocomplete="off" value=""/></div>
        </div>
    </td>
    <td colspan="5">
        <div class="input">
            <span>Name On Card</span>
            <div><input type="text" id="cardname" name="cardname" autocomplete="off" value="" /></div>
        </div>
    </td>
</tr>
<tr>
    <td colspan="2">
        <div class="input">
            <span>CVV</span>
            <div><input type="text" id="cvv" name="cvv" autocomplete="off" MAXLENGTH=6 /></div>
        </div>
    </td>
    <td colspan="2">
        <div class="input">
            <span>Card</span>
            <div>
                <select name="payment_type_id"> 
                    <option  value="3">Visa</option>
                    <option  value="4">Master</option>
                </select> 
            </div>
        </div>
    </td>
    <td colspan="3">
        <div class="input">
            <span>Exp. Month</span>
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
        </div>
    </td>
    <td colspan="3">
        <div class="input">
            <span>Exp. Year</span>
            <div>
                <select id="expiryyear"  name="expiryyear"> 
                    <option  value=""></option>
                    <?for($y=0,$x=date("Y"); $y<12; $y++, $x++):?>
                        <option  value="<?=substr($x,2)?>"><?=$x?></option>
                    <?endfor;?>
                </select> 
            </div>
        </div>
    </td>
</tr>
<tr>
    <td colspan="10">
        <div class="input">
            <span>Cardholder's Address</span>
            <div ><input type="text" name="street" /></div>
        </div>
    </td>
</tr>
<tr>
    <td colspan="3">
        <div class="input">
            <span>City</span>
            <div ><input type="text" name="city" /></div>
        </div>
    </td>
    <td colspan="2">
        <div class="input">
            <span>Country</span>
            <div>
                <select name="country"> 
                    <option  value="CA">Canada</option>
                </select> 
            </div>
        </div>
    </td>
    <td colspan="3">
        <div class="input">
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
        </div>
    </td>
    <td colspan="2">
        <div class="input">
            <span>Postal</span>
            <div><input type="text" name="postalcode" /></div>
        </div>
    </td>
</tr>               
</table>

<table class="form" cellspacing="0" cellpadding="0">
<tr>
    <td width="60%">
        <div id="amount">
<?/*
            Amount to be charged: <span id="amt_number">$<?=$params["amount"]?> <?=$params["currency_type_name"]?></span><br/>
 
*/?>
        	<?if(!isset($currency_abbrev)||!$currency_abbrev) $currency_abbrev='CDN';?>
            Amount to be charged: <span id="amt_number">$<?=$amount?> <?=$currency_abbrev?></span><br/>

            <span id="amt_desc">This amount will be charged to your credit card when you click 'Submit Payment'</span>
        </div>
    </td>
    <td>
        <div class="ctrBtn ctrRight">
            <a href="javascript:if(checkForm())document.cc_payment_form.submit();" class="btnReg">Submit Payment</a>
        </div>
    </td>
</tr>
</table>

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
    if(document.getElementById("cvv").value.length > 6)
    {
        alert('CVV Too Large');
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


</body>
</html>