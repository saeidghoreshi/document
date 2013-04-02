<!--TRANSACTIONS-->

<table style="width: 100%;font-size:10px;font-family: verdana;padding: 2;">
<?

$cur_trans_num=null;
foreach($transactions as $v)
{
    if($cur_trans_num==null || $v["trans_num"]!=$cur_trans_num)
    {
        $cur_trans_num=$v["trans_num"];
        echo "<tr><td colspan=4 style='background-color:yellow'><b>{$v["trans_num"]}</b></td></tr>";
        foreach($payments as $p)
        {
            if($p["trans_num"]==$cur_trans_num)
            {
                
                echo "<tr><td colspan=4 style='background-color:#f2f2f2'>";
                
                echo date('Y-m-d H:i',time($p["created_on"]));
                echo '<br/>';
                echo '<b> From :</b>'   .$p["master_entity_name"];
                echo '<br/>';                
                echo '<b> To :</b>'     .$p["slave_entity_name"];
                echo '<br/>';
                echo '<b>Amount : </b>' .$p["amount"].' '.$p["currency_type_name"];
                echo '<br/>';
                echo '<b>Type : </b>'   .$p["payment_type_name"];
                echo '<br/>';
                
                if(intval($p["payment_type_id"])==2)
                {
                    echo '<b>Tag : </b>'   .$p["transaction_tag"];
                    echo '<br/>';
                    echo '<b>Auth Number: </b>'   .$p["authorization_num"];
                }
                if(intval($p["payment_type_id"])==3 || intval($p["payment_type_id"])==4)
                {
                    echo '<b>TxRef: </b>'   .$p["txrefnum"];
                    echo '<br/>';
                    echo '<b>Order ID: </b>'   .$p["orderid"];
                }
                   
                echo "</td></tr>";
                
                break;
            }   
        }
    }
    echo "<tr>";
    echo "<td>";
    echo $v["account_code"];
    echo "</td>";
    echo "<td>";
    echo $v["trans_amount"];
    echo "</td>";
    echo "<td>";
    echo $v["trans_datetime"];
    echo "</td>";
    echo "<td>";
    echo $v["trans_amount"];
    echo "</td>";
    
    echo "</tr>";                 
}
?>
</table>
  