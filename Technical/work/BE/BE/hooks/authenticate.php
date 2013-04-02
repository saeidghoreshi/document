<?php

class Authenticate
{
	
	private $token;
	private $sessid;
	private $CI;
		
	public function auth_controller()
	{
		$this->prep();

		// is the request exempt from the permissions check?
		$exempt = $this->authExceptions(0);
		if($exempt) return true;

		// is the request coming from a curl?
		$curl = $this->authCurl();
		if($curl) return true;

		// is the token valid?
		$valid = $this->authToken();
		if(!$valid) $this->deny(1);

		##################################################################################
		################ You may now assume that the token passed is valid ###############
		##################################################################################

		// does the request only require a valid token?
		$exempt = $this->authExceptions(1);
		if($exempt) return true;

		// is the user allowed to view the requests controller / method combination
		$allowed = $this->isAllowed();
		if(!$allowed) $this->deny(2);

		##################################################################################
		#### You may now assume that the user is allowed to see the requested content ####
		##################################################################################

		// all checks passed
		return true;
		
	}

	private function prep()
	{
		global $token;
		global $USER_CAN_ACCESS;
		$this->CI =& get_instance();
		$URI =& load_class('URI');
		$uri = array_values($URI->rsegments);
		$RTR =& load_class('Router');
		
		// DONE 4 -o Bradley -c PRE RELEASE: Switch default for $c and $m values to CI values
		list($c,$m,$TOKEN,$PHPSESSID) = array($RTR->default_controller,'index','TOKEN:EMPTY_TOKEN','PHPSESSID:EMPTY_SESSION');
		
		switch (count($uri))
		{
			case 0: break;
			case 1: list($c) = $uri; break;
			case 2: list($c,$m) = $uri; break;
			case 3: list($c,$m,$TOKEN) = $uri; break;
			case 4: list($c,$m,$TOKEN,$PHPSESSID) = $uri; break;
			default: list($c,$m,$TOKEN,$PHPSESSID) = array_slice($uri,0,4); break;
		}
		
		// load permissions model
		$this->CI->load->model('permissions_model','permissions');
		$user = $this->CI->permissions->get_active_user();
        $org = $this->CI->permissions->get_active_org();
		
		//strip token keyword for comparrison
		if(substr($TOKEN,0,6)=="TOKEN:") $TOKEN = substr($TOKEN,6);

        //strip session id
        if(substr($PHPSESSID,0,10)=="PHPSESSID:") $PHPSESSID = substr($PHPSESSID,10);

        // save variables
        $this->token = $TOKEN;
        $this->sessid = $PHPSESSID;
		$this->accessVariables = array($c,$m,$TOKEN,$user,$org,session_id(),$_SERVER['REMOTE_ADDR']);

		// remove token so that we don't pass it to the controller
		if(substr(@$URI->rsegments[4],0,10)=="PHPSESSID:") unset($URI->rsegments[4]);
		if(substr(@$URI->rsegments[3],0,6)=="TOKEN:") unset($URI->rsegments[3]);

		//setup required items as global vars for helper functions
		$GLOBALS['gC'] = $c;
		$GLOBALS['gM'] = $m;
		$GLOBALS['gUser'] = $user;
		$GLOBALS['gOrg'] = $org;
	}

	private function deny($reason=0)
	{
		switch($reason)
		{
			
			case 0:
				$msg = 'You do not have the required permissions to use this feature. If you have not already tried, please re-login.';
				break;

			case 1:
				$msg = 'Your token is invalid. <b>';
				$msg .= $GLOBALS['gC']."</b> :: <b>".$GLOBALS['gM']."</b> requires a valid token.";
				$msg .= '<br/> Your Token: '.$this->token;
				$msg .= '<br/> Your Session: '.$this->sessid;
				break;

			case 2:
				$msg = 'You do not have the required permissions to use this feature. If you have not already tried, please re-login.';
				if(strstr($_SERVER['SERVER_NAME'],"endeavor.servilliansolutionsinc.com"))
				{
					list($c,$m,$t,$u,$o,$s,$i) = $this->accessVariables;
					$msg .= "<br><br><b><u>Problems Detected:</u></b><br>";
					if(empty($c)) $msg .= "No Controller Passed<br>";
					if(empty($m)) $msg .= "No Method Passed<br>";
					if($t=="EMPTY_TOKEN") $msg .= "No TOKEN Passed<br>";
					if(empty($u)) $msg .= "No Active User ID Found<br>";
					if(empty($o)) $msg .= "No Active Org ID Found<br>";
					if(empty($s)) $msg .= "No Session ID<br>";
					if(empty($i)) $msg .= "No IP Address<br>";
					$msg .= "Permissions Denied<br>";
					$msg .= "<br><b><u>Permissions Call Executed:</u></b><br>";
					$msg .= "SELECT permissions.is_userorg_allowed ('$c', '$m', '$t', $u , $o, '$s', '$i')";
				}
				break;
		}
		
		show_error($msg,401);
		exit();
	}

	private function authCurl()
	{
		if(strstr($_SERVER['HTTP_USER_AGENT'],'Spectrum'))
		{
			$ua = '';
			if(array_key_exists('endeavor-auth',@$_POST))
			{
	            $ua = pack('H*', $_POST['endeavor-auth']);
				$ua = base64_decode($ua);
				list($ua,$iv) = explode("||",$ua,2);
				$td = mcrypt_module_open('tripledes', '', 'ecb', '');
				$key = substr(md5(SSI_ENC_KEY),0,mcrypt_enc_get_key_size($td));
				mcrypt_generic_init($td, $key, $iv);
				$ua = mdecrypt_generic($td, $ua);
				mcrypt_generic_deinit($td);
				mcrypt_module_close($td);
				list($salt,$time) = explode("-",$ua);
				
				$now = time();
				if($salt=="SPECTRUMKEY" and $time<=$now and $time>=($now-90))
				{
					return true;
				}
			}
		}

		return false;
	}

	private function authToken()
	{
		// check with native session id
		$native = $this->CI->permissions->is_token_valid($this->token, session_id(), $_SERVER['REMOTE_ADDR']);
		if($native) return true;

		// check with passed session id
		$passed = ($this->sessid == "EMPTY_SESSION") ? FALSE : $this->CI->permissions->is_token_valid($this->token, $this->sessid, $_SERVER['REMOTE_ADDR']);
		if($passed) return true;

		return false;
	}

	private function authExceptions($type)
	{
		$c = $GLOBALS['gC'];
		$m = $GLOBALS['gM'];

		// 0 = no token required, 1 = token required
		$exceptions = ($type==1) ? $this->getPartialExceptions() : $this->getExceptions();

		//if($type==1) var_dump ($exceptions);

		// loop and check for controller match or controller / method pair
		foreach( $exceptions as $k=>$v )
		{
			//if($type==1) var_dump($k, $v);

			if( count($v)==1 and $v[0]==$c )
			{
				return true;
			}
			elseif( $v[0]==$c and $v[1]==$m )
			{
				 return true;
			}
		}

		// no exception found
		return false;
	}

	private function isAllowed()
	{
		list($c,$m,$t,$u,$o,$s,$r) = $this->accessVariables;
		return $this->CI->permissions->is_userorg_allowed($c,$m,$t,$u,$o,$s,$r);
	}

	/**
	 * List of controllers or controller / method combinations that do not require authentication
	 * @return [array]
	 */
	private function getExceptions()
	{
		// set list of exempt windows
		// array(controller, [method])
		return array(
			
			array('endeavor','index'),
			array('endeavor','get_menubar'),
			array('endeavor','html_panel'),
			array('endeavor','get_active_roles'),
			
			/**
			* These exceptions required for the login process and validation of a user
			*/
			array('permissions','window_login'),
			array('permissions','check_login'),
			array('permissions','logout'), 							//SB: added aug 29 for logout
			array('permissions','json_get_orgtypes_below'), 		//SB: added aug 29 for getstarted
			array('permissions','post_default_org'),
			array('permissions','post_active_org'),
            array('permissions','post_reset_password'),
            array('permissions','post_retrieve_username'),
			array('permissions','json_active_org_full_details'),
            array('permissions','json_bypass_login'),
            array('ads','get'),
            array('endeavor','json_getactive_org_logo'),

            /**
            * CSS file
            */
            array('endeavor','get_css_file'),
            
            //facebook
            array('home','index'),
            array('home','getFacebookObject'),
            array('home','json_adjust_fb_objects'),
            array('home','json_adjustFBFriendsGroups'),
            array('home','json_get_facebook_friends'),
            
            /**
            * Get started screen
            */
            array('getstarted'),//whole get started controller
            
            /**
            * The system controller has it's own security model
            */
            array('system'),

            array('finance','test_beanstream_chron'),
            array('permissions','json_get_active_org_and_type'),	//global, happens when an org changes, used for tab lockout 
            array('endeavor','html_welcome'),
            array('endeavor','html_help'),
            array('endeavor','json_getDomainNames'),
            array('endeavor','json_getMenuTypeItem'),
            array('endeavor','json_getRoleMenu'),
            array('endeavor','json_getMenuAuthStore'),
            array('endeavor','json_getMenuWindowType'),
            array('endeavor','json_saveNewMenuItem'),
            array('endeavor','json_deleteMenuItem'),
            array('teams','html_team_season_roster'),				//new window open, display only
            array('season','json_active_league_seasons_store'),
            array('finance','htmlGetCreditCardForm'),
            array('finance','json_pay_direct_CC_TEST'),             //will be Revoked By IFRAME not Cross Domain
            array('finance','json_get_io_payment_form'),
            array('finance','json_get_cc_payment_form'),
            array('finance','json_pay_direct_CC'),
            array('finance','json_pay_direct_DB'),
            array('finance','json_get_io_final_form'),
            array('finance','json_get_cc_final_form'),
            array('finance','json_pay_direct_invoice_cc'),
            array('finance','json_pay_direct_invoice_db'),
            array('finance','json_pay_direct_deposit_cc'),
            array('finance','json_pay_direct_deposit_db')
            
		);
	}

	/**
	 * 
	 */
	public function getPartialExceptions()
	{
		$arr = array(
            array('finance','htmlGetCreditCardFormFrontEnd')  
            ,array('endeavor','get_active_orgs')  
		);
		return $arr;
		
	}
	
}
