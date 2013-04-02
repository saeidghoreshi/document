<div style='position:relative!important; ' >

	<div id='_bar_root_container' style="z-index: 9999999!important; position:absolute!important; width:9000px ;top:-80;"  ><?#works at -32?>
		<?// reccomended tags are h3 and p?>
		<div class="error message" id="_bar_msg_err_" style='position:absolute'  >
	 
		</div>

		<div class="warning message" id="_bar_msg_warn_"style='position:absolute' >
	 
		</div>

		<div class="success message" id="_bar_msg_succ_"style='position:absolute' >
	 
		</div>
		<div class="info message" id="_bar_msg_info_"style='position:absolute' >
		     <h3>Loading...</h3> 
		</div>
	</div>
	 
	<div class='window ctr' id='win-scheduler-wizard' style='position:relative;padding-top:12px;'>


		 
		 
		<div class='wiz-screen-start' style="padding-bottom:16px" >
			<table width='100%' >
			<tr>
				<td width='50%'>
					<div style="padding-right:8px" id='wiz-start-form'></div><?#renderTo: for the start form?>
				</td>
				<td width='50%'>
					<div style="padding-left: 8px" id='wiz-load-grid'></div><?#renderTo: for the start grid?>
				</td>
			</tr>
			</table>
		   
		</div>
		<div id='wiz-screen-timeslots' class=' dghidden'>

			<table width='100%'>
			<tr>
				<td width='60%'>
					<div  id='wiz-dg-timeslots'></div>
				</td>
				<td width='40%'>
					<div   id='wiz-dg-games'></div>
				</td>
			</tr>
			</table>
				




		</div>
		<div id='wiz-screen-matches' class='dghidden'>


			<div class='ctr' id='wiz-dg-matches'></div>


		</div>
		<div id='wiz-screen-calendar' class='ctr dghidden'>

			

		</div>
		<div id='wiz-screen-venues' class='ctr dghidden'  ><!-- style='padding-top:-10px; margin-top:-10px;'-->


			<table class='ctr' width='100%'><tr>
				<td width='60%'> <div class='ctr' id='wiz-dg-venues'></div> </td>
				<td width='40%'> <div class='ctr' id='wiz-dg-vselected'></div></td>
			</tr></table>
		


		</div>
		<div id='wiz-screen-audit' class='ctr dghidden' >

			
			<div class='ctr' id='wiz-dg-audit'></div>
		
		
		</div>
		<div id='wiz-screen-finalize' class='ctr dghidden' >
			
			
			
			<div class='ctr' id='wiz-form-finalize'></div>
		
		
		
		
		</div>

	</div>

	</div>