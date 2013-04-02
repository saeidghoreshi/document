<html>
<body>
<h3>Please Complete INTERAC Payment</h3>
  
    
    <form action=<?=$params["INTERAC_URL"]?> method="POST">
<?
      $x_login          = $params["INTERAC_LOGINID"];        // Take from Payment Page ID in Payment Pages interface
      $transaction_key  = $params["INTERAC_TRANSACTIONKEY"]; // Take from Payment Pages configuration interface
      
      
      $x_amount         =$params["x_amount"];
      $x_currency_code  =$params["x_currency_code"];
      
      srand(time());                                        // initialize random generator for x_fp_sequence
      $x_fp_sequence = rand(1000, 100000) + 123456;
      $x_fp_timestamp = time();                             // needs to be in UTC. Make sure webserver produces UTC
      $hmac_data = $x_login . "^" . $x_fp_sequence . "^" . $x_fp_timestamp . "^" . $x_amount. "^" . $x_currency_code;
      $x_fp_hash = hash_hmac('MD5', $hmac_data, $transaction_key);
      echo ('<input type="hidden" name="x_login" value="'           . $x_login          . '">' );
      echo ('<input type="hidden" name="x_amount" value="'          . $x_amount         . '">' );
      echo ('<input type="hidden" name="x_fp_sequence" value="'     . $x_fp_sequence    . '">' );
      echo ('<input type="hidden" name="x_fp_timestamp" value="'    . $x_fp_timestamp   . '">' );
      echo ('<input type="hidden" name="x_fp_hash" value="'         . $x_fp_hash        . '">' );
      echo ('<input type="hidden" name="x_currency_code" value="'   . $x_currency_code  . '">' );






?>

     <!--build hidden parems to be posted lated -->
     <input type="hidden" name="payment_type_id" value="2">
     <?foreach($params as $i=>$v):?>
     <input type="hidden" name="<?=$i?>" value="<?=$v?>">
     <?endforeach;?>
      
      
      
      
     <!-- switch between live/test payment system if live payment system is active -->
     <input type="hidden" name="x_test_request"    value="TRUE"/>                           
     <input type="hidden" name="x_show_form"       value="PAYMENT_FORM"/>                   
     <input type="submit"                          value="Make a Payment"/>
     
    </form>                                                                                  
  </body>
</html>
