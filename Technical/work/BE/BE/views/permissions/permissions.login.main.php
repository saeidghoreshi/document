<input type="hidden" id='L-user-ip' value='<?=$_SERVER['REMOTE_ADDR']?>' />
<table width="100%" cellpadding="0" cellspacing="0">  
<tr>
	<td valign="top"><img src="/assets/images/login/key.png"/></td>
	<td width="100%" valign="top" class="padwrap">
	<div class="hidden" id="full-login-window">
		<div id="frm-login">
			<div id="L-user-name" class="form-field">
				<div id="L-user-username">User Name</div>
				<div id="L-user-username-input" class="input">
					<input type="text" id="L-username" />
				</div>
			</div>
			<div id="L-user-password" class="form-field">
				<div id="L-user-password">Password</div>
				<div id="L-user-password" class="input">
					<input type="password" id="L-password" />
				</div>
			</div>
			<center>
				<div  id="L-btn-div"><button id='L-btn'>Sign In</button></div>
				<p style="font-size:8pt;">
					<a href="javascript:o_login.show_get_password()">I Forgot My Password</a><br/>
					<a href="javascript:o_login.show_get_username()">I Forgot My Username</a>
				</p>
			</center>
		</div>
		<div id="frm-forgot-password" class="hidden">
			<div id="" class="form-field">
				<div id="">User Name</div>
				<div id="L-user-username2-input" class="input">
					<input type="text" id="L-username2" />
				</div>
			</div>
			<div id="" class="form-field">
				<div id="">Email</div>
				<div id="L-user-email" class="input">
					<input type="text" id="L-email" />
				</div>
			</div>
			<center>
				<div id="btn-getpassword"></div><!--retrive password button-->
				<p style="font-size:8pt;">
					<a href="javascript:o_login.show_get_username()">I Forgot My Username</a><br/>
					<a href="javascript:o_login.show_login()">Return to Login</a>
				</p>
			</center>
		</div>
		<div id="frm-forgot-username" class="hidden">
			<div id="" class="form-field">
				<div id="">Email</div>
				<div id="L-user-email2" class="input">
					<input type="text" id="L-email2" />
				</div>
			</div>
			<center>
				<div id="btn-getusername">Retrieve Username</div>
				<p style="font-size:8pt;">
					<a href="javascript:o_login.show_get_password()">I Forgot My Password</a><br/>
					<a href="javascript:o_login.show_login()">Return to Login</a>
				</p>
			</center>
		</div>
		<div id="frm-show-message" class="hidden">
			<div style="color:#CC0000;"><span id="L-alert"></span></div>
			<center>
				<p style="font-size:8pt;">
					<a href="javascript:o_login.show_get_username()">I Forgot My Username</a><br/>
					<a href="javascript:o_login.show_get_password()">I Forgot My Password</a><br/>
					<a href="javascript:o_login.show_login()">Return to Login</a>
				</p>
			</center>
		</div>
		
		
		
		</div>
	</td>
</tr>
</table>  

