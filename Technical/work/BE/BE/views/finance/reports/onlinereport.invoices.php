<!--INVOICE & PAYMENTS-->

<table style="width: 100%;font-size:10px;font-family: verdana;padding: 2;">
<?

$cur_i_trans_num=null;
foreach($invoicesPayments as $v)
{
    if($cur_i_trans_num==null || $v["i_trans_num"]!=$cur_i_trans_num)
    {
        $cur_i_trans_num=$v["i_trans_num"];
        echo "<tr><td colspan=10 style='background-color:yellow'><b>{$v["i_trans_num"]} [{$v["invoice_title"]}]  [{$v["invoice_status_name"]}]</b></td></tr>";
?>
        <tr style="font-weight: bold;">
        <td>
        Title
        </td>
        <td>
        Issuer
        </td>
        <td>
        Receiver
        </td>
        <td>
        Custom Number
        </td>
        <td>
        Total 
        </td>
        <td>
        Paid
        </td>
        <td>
        Oweing
        </td>
        <td>
        Status
        </td>
        <td>
        Currency
        </td>
        <td>
        Number
        </td>
        </tr>
        
        <tr>
        <td>
        <?=$v["invoice_title"]?>
        </td>
        <td>
        <?=$v["invoice_master_ename"]?>
        </td>
        <td>
        <?=$v["invoice_slave_ename"]?>
        </td>
        <td>
        <?=$v["custom_invoice_number"]?>
        </td>
        <td>
        <?=$v["invoice_amount"]?>
        </td>
        <td>
        <?=$v["invoice_paid"]?>
        </td>
        <td>
        <?=$v["invoice_owing"]?>
        </td>
        <td>
        <?=$v["invoice_status_name"]?>
        </td>
        <td>
        <?=$v["currency_type_name"]?>
        </td>
        <td>
        <?=$v["invoice_number"]?>
        </td>
        </tr>
<?
    }
    
    echo "<tr><td colspan=10 style='background-color:teal;color:white'>Payment  [<b>".$v["payment_status_name"]."</b>]</td></tr>";
    echo "<tr><td colspan=10 style='background-color:#f2f2f2'>";
    
    echo date('Y-m-d H:i',time($v["created_on"]));
    echo '<br/>';
    echo '<b> From :</b>'   .$v["invoice_slave_ename"];
    echo '<br/>';                
    echo '<b> To :</b>'     .$v["invoice_master_ename"];
    echo '<br/>';
    echo '<b>Amount : </b>' .$v["amount"].' '.$v["currency_type_name"];
    echo '<br/>';
    echo '<b>Type : </b>'   .$v["payment_type_name"];
    echo '<br/>';            
    
    if(intval($v["payment_type_id"])==2)
    {
        echo '<b>Tag : </b>'   .$v["transaction_tag"];
        echo '<br/>';
        echo '<b>Auth Number: </b>'   .$v["authorization_num"];
    }
    if(intval($v["payment_type_id"])==3 || intval($v["payment_type_id"])==4)
    {
        echo '<b>TxRef: </b>'   .$v["txrefnum"];
        echo '<br/>';
        echo '<b>Order ID: </b>'   .$v["orderid"];
    }
       
    echo "</td></tr>";
}
?>
</table>
  