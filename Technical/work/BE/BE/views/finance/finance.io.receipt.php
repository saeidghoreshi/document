<div style="width: 80%;padding: 20 20 20 20; margin: 10 10 10 10;font-family: verdana;font-size: 11; font-weight: bold;border: 1px solid red;">
<?
if($result=='true')
{
    echo 'Congragulation.<br/><br/>payment made Successfully. Please Check Your Email for Receipt';
    //CHECKING EXTERNAL INTERAC TRANSACTION AUTHENCITY
    //DECIDE ON PAYMENT SERVER
    $INTERACRESPONSEKEY =INTERAC_RESPONSEKEY;
    $INTERACLOGINID     =INTERAC_LOGINID;
    //CHECKING PAYMENT AUTHENCITY 
    if(strpos($amount,'.')=='')
        $amount     =$amount.'.00';
    $return_hash    =md5($INTERACRESPONSEKEY.$INTERACLOGINID.$trans_tag.$amount);
    if($return_hash==$x_MD5_Hash)
        echo '<h2>Transaction Occured Secure</h2>';
    //PRINTING RESULTS
    echo  $return_hash  .'<br>';
    echo  $x_MD5_Hash   .'<br>';
    echo '<pre>';
    echo $x_response_reason_text.'<br>';
    echo base64_decode($exact_ctr);
    echo '</pre>';
}   
else
    echo 'Difficulty for Payment .[ '.$result.']<br/>Close the Payment Page and Try again';
    
?>
</div>
