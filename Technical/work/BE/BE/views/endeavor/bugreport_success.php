
<!-- Displayed when a bug or feature is submitted. created for Spectrum 2.1  -->
<h2>
Thank you <?=$data['full_name']?>, your feedback is appreciated.  
</h2>
<p>
The 
<?$typestr= $data['is_bug'] ? 'bug report' : 'feature request'?>
 has been recieved by Spectrum on <?=$data['date']?>
<?if(isset($data['email'])) echo ' from '.$data['email'];//display email if they gave one?>
 and will be addressed.



</p>


<?/*other fields in 'data' that are not used include: 
ip
login
context
user_id
org_id 
*/?>