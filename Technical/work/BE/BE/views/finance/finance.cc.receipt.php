<div style="width: 80%;padding: 20 20 20 20; margin: 10 10 10 10;font-family: verdana;font-size: 11; font-weight: bold;border: 1px solid red;">
<?
if($result=='true') 
    echo 'Congragulation.<br/><br/>payment made Successfully. Please Check Your Email or Receipt';
else
    echo 'Difficulty for Payment .[ '.$result.']<br/>Close the Payment Page and Try again';
    
?>  
</div>
