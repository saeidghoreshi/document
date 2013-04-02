<?php

require_once('endeavor.php');
class Getstarted extends Endeavor
{
	
	//load template; preload next, current, and prev of frm and next, current of dyk
	
	public function index()
	{
		
		$active=array();
		$active['org_type']=$this->permissions_model->get_active_org_type(); 
		$active['returner']=($_SESSION['last_login_date'] == null ? false:true);
		$data = array(
			'dyk_current'	=>$this->load->view('getstarted/dyk/dyk1',null,true),
			'dyk_next'		=>$this->load->view('getstarted/dyk/dyk2',null,true),
			'frm_current'	=>$this->load->view('getstarted/page/01-welcome',$active,true),
		);
		
		$template = $this->load->view('getstarted/templates/framework',$data,true);
		
		$return = array('html'=>$template);
		
		$this->result->json($return);
	}
	
	private function getFormOutput($file, $dir, $js=false, $disable=false)
	{
		$data=array();
		$data['org_type']=$this->permissions_model->get_active_org_type(); 
		$data['returner']=($_SESSION['last_login_date'] == null ? false:true);
		
		if($file=="09-terms")
		{
			$data['document'] = strip_tags(file_get_contents(PUBDOC_SOURCE."terms_and_conditions.html"));
		}

		
		// var_dump($data);;
		$output = array();
		$output['HTML'] = $this->load->view("getstarted/page/$file",$data,true);
		$output['DIR'] = $dir;
		if($js)
		{
			if(!is_array($js)) $js = array($js);
			foreach($js as $file)
			{
				$file = $file."?".time();
				$output['JS'][] = (strstr($file,"/")) ? $file : "/assets/js/components/getstarted/forms/$file";
			} 
		}
		if($disable) $output['DISABLE'] = $disable;
		return $output;
	}

	
	public function getForm($frm,$dir)
	{
		
		$base_js_path = "/assets/js/components/";
		$base_gs_comp = "Spectrum.getstarted.";
		$org_type = $this->permissions_model->get_active_org_type();//current activeorg  type
		switch($frm)
		{
			default:
			case _GS_WELCOME:  $data = array('01-welcome',$base_js_path.'users/forms/user_person.js'
													     ,$base_js_path.'users/windows/users.js'
													     ,); break;//no JS needed
			case _GS_PASSWORD: $data = array('02-password'        ,array($base_gs_comp.'changepassword.js')); break;
			case _GS_BEFORE:   $data = array('03-beforeyoustart'); break;//no JS needed
			case _GS_PERSON:   $data = array('04a-personaldetails',array($base_js_path.'users/forms/user_person.js',$base_gs_comp.'personaldetails.js')); break;
			case _GS_ADDRESS:  $data = array('04b-personaladdress',array($base_js_path.'users/forms/user_person.js',$base_gs_comp.'personaladdress.js')); break;
			case _GS_CONTACT:  $data = array('04c-personalcontact',array($base_js_path.'users/forms/user_person.js',$base_gs_comp.'personalcontact.js')); break;
			// check here for  activeorgtype, to change league/team/assoc/etc
			case _GS_ORGDETAILS: 
				if($org_type == ORG_TYPE_ASSOC)
				{	//TODO: create/add JS for assoc
					$data = array('05a-assocdetails.php'  ,array(/*'/assets/js2/leagues/form.league.js'   ,*/ $base_gs_comp.'assocdetails.js')); 
				} 
				else if($org_type == ORG_TYPE_LEAGUE)
				{	//it works!
					$data = array('05b-leaguedetails'  ,array($base_js_path.'leagues/forms/create_edit.js'   ,$base_gs_comp.'leaguedetails.js')); 
				}
				else if($org_type == ORG_TYPE_TEAM)
				{   //TODO: add JS for teams
					$data = array('05c-teamdetails' ,array($base_gs_comp.'teamdetails.js')); 
				}
				
			break;
			case _GS_ORGADDRESS:  $data = array('05d-orgaddress'     ,array($base_js_path.'users/forms/user_person.js',$base_gs_comp.'orgaddress.js')); 
			break;
			case _GS_USERS:  $data = array('06-orgusers'        ,array(//also the model for the grid
															'assets/js/models/user_role.js'
															,'assets/js/models/users.js'
															,'assets/js/models/role.js'
															,$base_js_path.'users/grids/org_users.js'      
											                 ,$base_js_path.'users/grids/org_roles.js',  
											                 
														  	  $base_js_path.'users/forms/org_roles.js', 
														  	  $base_js_path.'users/windows/roles.js', 
														  	  $base_gs_comp.'orgusers.js') ); 
			break;
			case _GS_SIGN: $data = array('07a-signingauth',array($base_js_path.'authority/grids/spectrumgrids.sa.assignment.js'//these locations are guesses
																,$base_js_path.'finance/grids/forms.js'
																,$base_gs_comp.'sign.js')); 
			
			break;
			case _GS_BANK: $data = array('07b-bankaccount',array(
				$base_js_path.'authority/forms/forms.js'
				,$base_js_path.'authority/forms/bankaccount.js' 
				,$base_gs_comp.'bank.js'
			)); 
			
			break;
			case _GS_SUMMARY: $data = array('08a-summary',array($base_gs_comp.'summary.js')); 
			
			break;
			case _GS_PAYMENT: $data = array('08b-payment'); 
			
			break;
			case _GS_TERMS: $data = array('09-terms');
			
			break;
			case _GS_FINAL: $data = array('10-finish'); 
			
			break;
		}
		
		if($frm == 0) $disable = 'prev';
		//if($frm > 15) $disable = 'next';//we will just make this close everything
		
		$this->result->json($this->getFormOutput(@$data[0],$dir,@$data[1],@$disable));
	}
	
	public function getDYK($ad)
	{
		$ads = array();
		if($dir=opendir('./endeavor/views/getstarted/dyk/'))
		{
			while(false!==($file=readdir($dir)))
			{
				if(!in_array($file,array(".","..")))
				{
					$ads[] = $file;
				}
			}
		}
		$ads = array_reverse($ads);
		
		$file = $ads[($ad % count($ads))];
		$this->load->view("getstarted/dyk/$file");
	}
	
	
	private function load()
	{
		$this->load->model('person_model');
	    $this->load->model('endeavor_model');
	    $this->load->model('associations_model');
	    $this->load->model('finance_model');
	    $this->load->model('teams_model');
	    $this->load->model('permissions_model');
	    $this->load->model('leagues_model');
	    $this->load->model('schedule_model');
	    $this->load->model('season_model');
	    $this->load->model('entity_model');
		
	}
	public function json_build_summary()
	{
		$this->load();
	    

		$org_id  = $this->permissions_model->get_active_org();
		$org_type= $this->permissions_model->get_active_org_type();//current activeorg  type
		$entity_id=$this->org_model->get_entity_id_from_org($org_id);
		$user_id = $this->permissions_model->get_active_user();
		
		//for top left
		
    	$user=$this->permissions_model->get_user($user_id);
    	$user=$user[0];
    	$user['person_name'] = $user['person_fname']." ".$user['person_lname'];
    	$user['address'] = $user['address_street']." ".$user['address_city']." ". $user['region_abbr']." ".
    			$user['country_abbr']." ".$user['postal_value'];
    	//for bottom 
    	$org=array();
		if($org_type==ORG_TYPE_LEAGUE)
		{
			$org=$this->leagues_model->get_league_details_from_org($org_id);
			$org=$org[0];
			$org['org_name'] = $org['league_name'];
		}
		else
		{
			//for assoc and team
			$getorg=$this->org_model->get_org($org_id);
			if(count($getorg))
			{
				$org['org_name']=$getorg[0]['org_name'];
				$org['org_id']  =$org_id;
			}
		}
		//TODO: get team or assoc details here
    	
		
        $org_address= $this->entity_model->get_entity_address($entity_id);
        //if(count($org_address))
        if(count($org_address))
        {
	        $org_address=$org_address[0];
    		$org_address['address'] = $org_address['address_street']." ".$org_address['address_city']." ".
    				 $org_address['region_abbr']." ".$org_address['country_abbr']." ".$org_address['postal_value'];
	        $fmt_org_address=array();
	        $p='org_';//to distingusih from user address, etc
	        foreach($org_address as $key=>$val)
	        {
        		$p_key=$p.$key;
				$fmt_org_address[$p_key]=$val;
	        }
		}
    	$user['address'] = $user['address_street']." ".$user['address_city']." ". $user['region_abbr']." ".$user['country_abbr'];
        
        
        
        //for top right
        $roles=$this->permissions_model->get_org_users($org_id);
        $user_ids=array();
        $is_exec=array(1,3,4);//regardless of league/team/assoc, is same
        $is_sa=array(16);
        
        $roles_counts=array('total_people'=>0,'total_exec'=>0,'total_sa'=>0,'total_other'=>0);
        foreach($roles as $r)
        {
			$uid=$r['user_id'];
			if(!in_array($uid,$user_ids)) {$user_ids[]=$uid;}
			$rid=$r['role_id'];
			
			if(in_array($rid,$is_exec)) {$roles_counts['total_exec']++;}
			else if(in_array($rid,$is_sa)) {$roles_counts['total_sa']++;}
			else  {$roles_counts['total_other']++;}
        }
        
        $roles_counts['total_people'] = count($user_ids);
        //for bottom right
        
        //$signers=$this->finance_model->get_sa_assignments();

        $bank  =$this->finance_model->get_bankaccounts();
        if(count($bank))
        	$bank=$bank[0];
        $summary=array_merge((array)$user,(array)$org,(array)$fmt_org_address,$roles_counts,(array)$bank);
        
        $this->result->json($summary);
	}



	public function post_finish()
	{
		$this->load();
	    

		//update org and user, so welcome screen will not show next time
		echo $this->permissions_model->update_last_login_date();
		echo $this->permissions_model->set_active_org_valid();
	}
	   //created for get started screen
   public function json_active_league_details()
   {
   	   $this->load();
	   $org=$this->permissions_model->get_active_org();
	   $league=$this->leagues_model->get_league_details_basic_from_org($org);
	   if(count($league)>0 && $league[0]['url'] )
	   {
	   	   //find 'websiteprefix' and 'domainname'
	   	   $d='.';
		   $exp= explode($d,$league[0]['url']);
		   
		   //array_shift will remove element zero, and return it, and modify the array
		   $league[0]['websiteprefix'] = array_shift($exp);
		   $league[0]['domainname']    = implode($d,$exp);//implode combines the rest together
	   }
	   $this->result->json($league);
   }
   public function json_activeorg_addresses()
    {
    	$this->load();
    	$org=$this->permissions_model->get_active_org();
    	
        $entity_id=$this->org_model->get_entity_id_from_org($org);
        $result= $this->entity_model->get_entity_address($entity_id);
        $this->result->json($result);
    }
	
}

?>