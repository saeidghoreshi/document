<?
 
if(!isset($logo)) $logo = "assets/images/spectrum.png";//default logo if none given
if(!isset($org_type)) $org_type=-1;//should never happen
if(!isset($returner) || !$returner) $returner='f';//default to 'its my first time here', otherwise 'welcome back'
  
 
?>

<div class="frm-item">
	<div class="frm-container">
		<h1>Welcome</h1>
		<div class="frm-content">
			<?//the gs_ classes do nothing right now, but added for future styling (sam)?>
			<div class='gs_header'>
				<p>Welcome 
				<?if($returner):?> back <?endif;?>
				to
 
				<img src='<?=$logo?>' height="38" />
				, the complete Online Sports Management System. </p>
			</div>
			
			 
			<div class='gs_content'>
			<?switch($org_type):
			
				case ORG_TYPE_ASSOC:?>
				
					<?if(!$returner):?>
						<p>
						Congratulations on your purchase of Spectrum, 
						Following the next steps will guide you through
						The input of all of your Association information 
						to properly set up your Spectrum system and 
						personal/personnel access.
						</p>
					<?else:?>					
						<p>
						Welcome back.   Please fill out your
						Association information so that Spectrum can
						use it to set up your Association access.
						</p>				
					<?endif;?>
					
				<? break;?>
				<? case ORG_TYPE_LEAGUE:?>
					<?if(!$returner):?>
						<p>
 						Congratulations on Joining Spectrum, 
						Following the next steps will guide you through
						The input of all of your League information 
						to properly set up your Spectrum system and 
						personal/personnel access.

						</p>
					<?else:?>					
						<p>
						 Welcome back.   Please fill out your
						 League information so that Spectrum can
						 use it to set up your personal/personnel access.
						</p>				
					<?endif;?>
				
				
				<?break;?>
				<? case ORG_TYPE_TEAM:?>
					<?if(!$returner):?>
					<p>
					    Congratulations on Joining Spectrum, 
						Following the next steps will guide you through
						The input of all of your Team information 
						to properly set up your Spectrum system and 
						personal access.

					</p>
					<?else:?>					
					<p>
					 	Welcome back.   Please fill out your
						Team information so that Spectrum can
						use it to set up your personal access.

					</p>	
					<?endif;?>				
				
				<?break;?>
				<?default://should never happen ,but show something just in case?>
					Please fill out all  
					information so that Spectrum can
					use it to set up your personal access.

				<?break;?>
				
			<?endswitch;?>
			</div>
			
			
			
			
			
			
		</div>
	</div>
</div>