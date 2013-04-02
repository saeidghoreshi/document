<?
 
 
if($currency_id == 1) $currency='CDN';// TOOD: support others
if(isset($theme) && !$theme) $theme = "0001";
//var dump comign from json_get_cc_final_form 
?>
<style type="text/css">
                                                 
<?=file_get_contents('http://devbrad.global.playerspectrum.com/spectrum/views/themes/theme'.$theme.'/css/structure.css');?>
<?=file_get_contents('http://devbrad.global.playerspectrum.com/spectrum/views/themes/theme'.$theme.'/css/form.css');?>

body{ background-color:#FFFFFF; }

div.input span{ margin-bottom:-4px; }
span.green{ color:#148525; }

</style>
<html>
<body>

<div class="pageContainer regContainer">

<? if($result=='true'): ?>

    <p>
    
    <span class="green">Congratulations!</span> You have successfully completed your payment of 
    <span class="green">$<?=$amt?> <?=$currency?></span>! 
    <?if(!isset($email) || !$email):?>
	    We've emailed you receipt of this payment, based on the email saved to your Spectrum account. 
	<?else:?>
    	We've emailed you receipt of this payment to <b>'<?=@$email?>'</b>.
    <?endif;?>
    
    </p> 

    <p>If you don't receive an email within 24 hours, please contact technical support 
    (info at the bottom of this screen) to receive a new receipt. Please check your trash bin, and 
    junk folder before contacting technical support.</p>

<? else: ?>

    Payment failed.  Your card rejected a payment of <span class="green">$<?=$amt?> <?=$currency?></span>.  We were given the following reason: 
    
    <p><?=$failure?></p>
     
    
<? endif; ?>
</div>



</body>
</html>
