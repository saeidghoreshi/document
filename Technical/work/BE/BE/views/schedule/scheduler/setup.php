<div class="remove-tab-padding" id='setup-tab'>
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top"><!--<img src="/assets/images/scheduler/girl-fielding-ball.png"/>--></td>
	<td width="100%" valign="top" class="padwrap">
		
		<div id="setup-content">

		<table width='100%'>
		<tr>
		<td width='10%' align='center'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>
		<td width='10%'></td>		
		</tr>
		
		<tr>
			<td colspan='1'>
				<?//is complete or not flag => image checkboxes in this col?>
			</td>
			<td colspan='1'>
				
				<?//Navigation?>
			</td>
			
			<td colspan='4'>Step Name</td>
			<td colspan='3'>Selected Info</td>
			
			
			
			
		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-season-complete' class='hidden'>
					<img src= '/assets/images/dev/accept.png'   />
				</span>
				<span id='setup-season-todo'>
					<img src= '/assets/images/dev/deny.png'   />
				</span>
			</td>
			<th  rowspan='8'>
				<div id="setup-buttongroup" class="yui-buttongroup">
					<div class='btn'><input type='radio' name='setup-yuiradio' id='btn-setup-season' value='Go' CHECKED /></div>
					<div class='btn'><input type='radio' name='setup-yuiradio' id='btn-setup-venues'value='Go'/></div>
					<div class='btn'><input type='radio' name='setup-yuiradio' id='btn-setup-rules'value='Go'/></div>
					<div class='btn'><input type='radio' name='setup-yuiradio' id='btn-setup-divisions'value='Go'/></div>
					<div class='btn'><input type='radio' name='setup-yuiradio' id='btn-setup-matches'value='Go'/></div>
					<div class='btn'><input type='radio' name='setup-yuiradio' id='btn-setup-teamex'value='Go'/></div>
					<div class='btn'><input type='radio' name='setup-yuiradio' id='btn-setup-extra'value='Go'/></div>
					<div id="sch-input-create" class="btn hidden" >	
						<input type='radio' name='setup-yuiradio' id="btn-setup-create" value='Create'/>
					</div>	
				</div>		
			</th>
			
			<td colspan='3'>
				<span class='label'>Select a Season</span>
			</td>
			<td colspan='3'>
				<span id='setup-season-data'></span>
			</td>
			<td colspan='3'>
				<div class='btnset'>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-venues-complete' class='hidden'>
					<img src= '/assets/images/dev/accept.png'   />
				</span>
				<span id='setup-venues-todo'>
					<img src= '/assets/images/dev/deny.png'   />
				</span>
			</td>
			<td colspan='3'>
				<span class='label'>Venues and Contracts</span>
			</td>
			<td colspan='3'>
				<span id='setup-venues-data'>0</span> Venue<span id='s-v-plural'>s</span> Booked
			</td>
			<td colspan='3'>
				<div class='btnset'>
				</div>
			</td>
		
		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-rules-complete' class='hidden'>
					<img src= '/assets/images/dev/accept.png'   />
				</span>
				<span id='setup-rules-todo'>
					<img src= '/assets/images/dev/deny.png'   />
				</span>
			</td>
			<td colspan='3'>
				<span class='label'>Schedule Rules</span>
			</td>
			<td colspan='3'>
				<span id='setup-rules-data'></span>
			</td>
			<td colspan='3'>
				<div class='btnset'>
				</div>
			</td>
		
		</tr>

		<tr>
			<td colspan='1' align='center'>
				<span id='setup-divisions-complete' class='hidden'>
					<img src= '/assets/images/dev/accept.png'   />
				</span>
				<span id='setup-divisions-todo'>
					<img src= '/assets/images/dev/deny.png'   />
				</span>
			</td>
			<td colspan='3'>
				<span class='label'>Select participating Divisions</span>
			</td>
			<td colspan='3'>
				<span id='setup-divisions-data'>0</span> Division<span id='s-d-plural'>s</span> Selected
			</td>
			<td colspan='3'>
				<div class='btnset'>
				</div>
			</td>		
		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-matches-complete' class='hidden'>
					<img src= '/assets/images/dev/accept.png'   />
				</span>
				<span id='setup-matches-todo'>
					<img src= '/assets/images/dev/deny.png'   />
				</span>
			</td>
			<td colspan='3'>
				<span class='label'>Create matches between divisions</span>
			</td>
			<td colspan='3'>
				<span id='setup-matches-data'>0</span> Match<span id='s-m-plural'>es</span> Created
			</td>
			<td colspan='3'>
				<div class='btnset'>
				</div>
			</td>		
		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-teamex-complete' class='hidden'>
					<img src= '/assets/images/dev/accept.png'   />
				</span>
				<span id='setup-teamex-todo'     class='hidden'>
					<img src= '/assets/images/dev/deny.png'   />
				</span>
			</td>
			<td colspan='3'>
				<span class='label'>Team Date Exceptions (Optional)</span>
			</td>
			<td colspan='3'>
				<span id='setup-teamex-data'></span>
			</td>
			<td colspan='3'>
				<div class='btnset'>
				</div>
			</td>
		
		</tr>
		<tr>
			<td colspan='1' align='center'>
				<span id='setup-extra-complete' class='hidden'>
					<img src= '/assets/images/dev/accept.png'   />
				</span>
				<span id='setup-extra-todo'     class='hidden'>
					<img src= '/assets/images/dev/deny.png'   />
				</span>
			</td>
			<td colspan='3'>
				<span class='label'>Extra Games (Optional)</span>
			</td>
			<td colspan='3'>
				<span id='setup-extra-data'></span>
			</td>
			<td colspan='3'>
				<div class='btnset'>
				</div>
			</td>
		
		</tr>
		<tr>
			<td colspan='1' align='center'>
				
			</td>
			<td colspan='3'>
				<span class='label'>Create Schedule</span>
			</td>
			<td colspan='3'>
				
			</td>
			<td colspan='3'>

			</td>
		
		
		</tr>
		<tr>
		
		<td colspan='10' align='center'>
		
		</td>
		
		
		</tr>
		
		
		</table>
		
		
		</div>
		
	</td>
</tr>
</table>
</div>