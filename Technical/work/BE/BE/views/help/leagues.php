<?php
  
  $help_data = array(
  	array('iconCls'=>'group', 'data'=>'HelpFileData')
  	,array('iconCls'=>'group','data'=> 'more Data')
  	,array('iconCls'=>'group', 'data'=>'more Data2')
  
  );
  
  
?>


<table class='helpTable' width='100%'>
<tr>
<td width='10%'></td>
<td width='90%'></td>
</tr>

<?foreach($help_data as $help):?>
	<?$iconCls=$help['iconCls'];?>
	<?$data=$help['data'];?>
	<tr>
		<td>
		<?=$iconCls ?> icon is:
			<span class='x-btn-icon <?=$iconCls?>'></span>
			<span  class="group"></span>
		</td>
		<td>
			data is:
			<span><?=$data;?></span>
		</td>
	
	</tr>



<?endforeach;?>

</table>