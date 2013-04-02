<div class="panelItem">
<? 
if($logo==null|| $logo == -1)$logo='assets/images/spectrum.png';//this should never happen
//as we also checkf or that in the model, but just in case   
?>
<center><br/><img width='153' style="max-height:400px ;" src="<?=$logo?>"/><br/></center><br/>
</div>
<!-- IMPORTANT changed class 'item' to 'pitem' -->
<div class="panelItem">
	<!-- Logged In User item-->
	<div class="label">You are logged in as:</div>
    <div class="pitem">
    
        <table id="facebook">
        <tr>
        	<?if($fb_id!=null && $fb_id != -1):?>
	        <td>
	        	<a onclick="getFacebookObject();" style="border:0;cursor: pointer;">
	        		<img src='http://graph.facebook.com/<?=$fb_id?>/picture'  ></img>
	        	</a>
	        </td>
	        <?endif;?>
	        <td>
	        	<div class="pitem">
	        		<p><?=$user_name?></p>
	        	</div>
	        </td>
        </tr>
        </table>
 
    
    
	</div>
	<!-- Logged In Org & Website -->
	<div class="label">Current Organization:</div> 
	<div class="pitem">
		<?=$org_name?><br/>
		<?if($url):?>
			<? 
				$dev = "dev.global.playerspectrum.com/index.php/frontpage/index/?theme=theme0001&db=default&domain=".$url;
				$nav = (strstr($_SERVER['SERVER_NAME'],'.endeavor.')) ? $dev : $url;
				$url = strtolower($url);
			?>
			<a href='http://<?=$nav?>' target='_blank'>http://<?=$url?></a>
		<?endif;?>
	</div>
	<div class='label'>Role<? if(count($role_names )!=1) echo 's';?>:
	</div>
	<div class='pitem'>
		<?foreach ($role_names as $r):?>
			<?=$r;?><br/>
		<?endforeach;?>
	</div>
</div>

<div class="panelItem">
    <!--Finance  Info-->
    <div class="label">Available Funds</div>
    <div class="pitem">
	    <table id="available-funds">
	    <?foreach($accounts as $i=>$v):?>
	    
	    <?if(isset($v["balance"])) $v["balance"] = '$ '.number_format($v["balance"],2,'.',',');?>
	        <tr>
		        <td><?=@$v["currency_name"]?></td>
		        <td><?=@$v["balance"]?></td>
	        </tr>
	    <?endforeach;?>
	    </table>
    </div>
</div>


<!--
<div class="panelitem">
	<div class="label">Your Accounts</div>
	<div class="item">
		<table width='100%'><?
		foreach($accounts as $account)
		{
			$account = array_values($account);
			list($lbl,$amt) = $account;
			if($amt > 0)
			{
				$amt = number_format($amt,2,".",",");
				echo "<tr><td><b>$lbl</b></td><td align='right'>$amt</td></tr>";
			}
		}
		?></table>
	</div>
</div>
-->

<?/*
$loggedin = ($user != null && $user != '' && $user != -1) ? true : false;
$hide = ($loggedin) ? '' : 'hidden';
$title = ($loggedin) ? 'Logged in as '.$userName : 'Welcome to Endeavor! Please Log In.';
?>

<div class="blackbox" id="userbox1">
	<input type='hidden' id='login-panel-changed' value=''>
	<input type='hidden' id='default-org-panel' value='<?$default_org_id?>'>
	<div class="bb-full">
		<!-- ACTIVE ORG SELECTION -->
		<h3>Active Organization</h3>
		<button class='<?=$hide?>' id="btn-org-def" title="Set As Default Active Organization"></button>
		<span id="p-menu" class='<?=$hide?>'> 
		    <input type="button" id="select_button" name="menubutton2_button" value="Menu test"/>
		    <select id='select-menu'>
		    <? 
		        foreach($roles as $role)
		        {
		            $id = $role['org_id'];
		            $name=$role['org_name'];
		            if($id == $default_org_id)
		            	echo "<option value='$id' SELECTED >".$name."</option>";
		            else
		           		echo "<option value='$id' >".$name."</option>";
		            //also exists: role_id and role_name
		        }
		    ?>
		    </select>
		</span>
		
		<!-- AUTOCOMPLETE SEARCH -->
		<h3>Search for Active Organization</h3>
		<div id="p-auto" class='<?=$hide?>'>
		    <div id="myAutoComplete">
		        <input id="myInput" type="text"/>
		        <div id="myContainer"></div>
		    </div>

		    <input id="autoLabel" type="hidden" value="-1" /> 
		    <input id="autoValue" type="hidden" value="-1" /> 
		</div>
	</div>
	<div class="bb-cell bb-left"></div>
	<div class="bb-cell bb-middle">
		<!-- WELCOME NAME, OR WELCOME LOGIN -->
		<?="<h3>".$title."</h3>"?>
	</div>
	<div class="bb-cell bb-right"></div>
</div>

<div class="silverbox" id="userbox2">
	<!-- TOP OF SILVERBOX / ROUNDED CORNERS -->
	<div class="sb-rnd-left"><div class="sb-rnd-right"></div></div>
	
	<!-- CONTENT EXTENSION -->
	<div class="sb-left">
		<div class="sb-right">
			<div class="sb-content">
				
				<?if( isset($url)&& $url):?>
					<center ><a href='http://<?=$url?>' target='_blank'> <b id="lbl-active-org-name"></b> </a></center>
				<?else: ?>
					<center style="color:#BBBBBB;"><b id="lbl-active-org-name"></b></center>
				<?endif; ?>
				<!--
				<? if($loggedin): ?>
				<h4><span>Your Account</span></h4>
				<table width='100%'>
				<?
				foreach($accounts as $account)
				{
					$account = array_values($account);
					list($lbl,$amt) = $account;
					if($amt > 0)
					{
					    $amt = number_format($amt,2,".",",");
					    echo "<tr><td><b>$lbl</b></td><td align='right'>\$ $amt</td></tr>";
					}
				}
				?>
				</table>
				-->
				<!--
				<h4><span>Messages</span></h4>
				<p>Your Order #109092 has been shipped and should arrive on April 2, 2011.</p>
				
				<h4><span>Summary</span></h4>
				<table width='100%'>
				<?
				foreach($summary as $item)
				{
					$item = array_values($item);
					list($lbl,$amt) = $item;
					echo "<tr><td><b>$lbl</b></td><td align='right'>$amt</td></tr>";
				}
				?>
				</table>
				
				<? endif; ?>
				-->
				<h4><span>Support</span></h4>
				<table width='100%'>
				<tr><td><b>Phone</b></td><td align='right'>+1 (250) 309 - 7408</td></tr>
				<tr><td><b>Email</b></td><td align='right'><a href="mailto:beta@playerspectrum.com">beta@playerspectrum.com</a></td></tr>
				</table>
				
			</div>
		</div>
	</div>
	
	<!-- BOTTOM OF SILVERBOX / ROUNDED CORNERS -->
	<div class="sb-btm-left"><div class="sb-btm-right"></div></div>
</div>








<!--
<div class="userbox" id="userbox3" class=<?=$hide?>>
<?
if($loggedin)
{
    $table = "<table width='100%'>";
    foreach($accounts as $account)
    {
	    $account = array_values($account);
	    list($lbl,$amt) = $account;
	    if($amt > 0) $table.= "<tr><th>$lbl</th><td>$amt</td></tr>";
    }
    $table.="</table>";
    echo $table;
}
?>
</div>
--></div>
*/?>
