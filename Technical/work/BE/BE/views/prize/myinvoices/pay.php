
<style type="text/css">
.right
{
    text-align:right;
}
.center
{
    text-align:center;
}

</style>


<div class="remove-tab-padding">
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
    
    <td width="100%" valign="top" class="padwrap">
        <div id="pay-content">
            
            
            <table width="100%"><tr>
            
            <td class="right" ><span class="label"> Owing : </span></td>
            <td><span id = "pay-owing"></span></td></tr>
            
            
            <tr><td class="right" ><span class="label">Payment Ammount : </span></td>
            <td> <input type="text" id="pay-amt" maxlength="15" value="0.00"/> </td></tr>
            
            <tr><td class="right" ><span class="label">Description : </span> </td>
            <td> <input type="text" id="pay-desc"  value=""/></td></tr>
            
            <tr><td class="right" ><span class="label">Payment Type : </span> </td>
            <td><div id="pay-select"><select id="pay-select">
                <option id='ld' SELECTED>League Dollars</option>
                <option id='cdn'>CDN Dollars</option>
            </select></div></td></tr>
            
            <tr><td class="right" ><span class="label">Account Info : </span> </td>
            <td><div id="account-input">?????????</div></td></tr>
            
            <tr><td><div class="center btn"><button id="pay-submit">Submit Payment</button></div></td>
            <td><span class="label" id="pay-status">Ready</span></td></tr>
            
            </table>
            
            
            
            
            
            
        </div>
    </td>
</tr>
</table>
</div>
