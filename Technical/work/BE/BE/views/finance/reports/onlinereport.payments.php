<table style="width: 100%;font-size:10px;font-family: verdana;padding: 2;">
<?

$cur_trans_num=null;   
foreach($payments as $v)
{
    if($cur_trans_num==null || $v["trans_num"]!=$cur_trans_num)
    {
        $cur_trans_num=$v["trans_num"];
        echo "<tr><td colspan=10 style='background-color:yellow'><b>{$v["trans_num"]} </b></td></tr>";
?>      
<?
    }
    
    echo "<tr><td colspan=10 style='background-color:teal;color:white'>Payment  [<b>".$v["payment_status_name"]."</b>]</td></tr>";
    echo "<tr><td colspan=10 style='background-color:#f2f2f2'>";
    
    echo date('Y-m-d H:i',time($v["created_on"]));
    echo '<br/>';
    echo '<b> From :</b>'   .$v["slave_entity_name"];
    echo '<br/>';                
    echo '<b> To :</b>'     .$v["master_entity_name"];
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
  
