<?php

/**
* Main endeaovr controller
* 
* @name Endeavor
* @author Bradley Holbrook
* @since Oct 2010
* 
*/
class Endeavor extends Controller 
{
	  	  /**
  	  * 
  	  * 
  	  * @var teams_model
  	  */
  	  public $teams_model;
		/**
	* 
	* 
	* @var associations_model
	*/
	public $associations_model;
	/**
	* @var Endeavor_model
	*/
	public $endeavor_model;
	
	/**
	* @var Page
	*/
	public $page;
	
	/**
	* @var Window
	*/
	public $window;
	
	/**
	* @var Input
	*/
	public $input;
	
	/**
	* @var Permissions_model
	*/
	public $permissions_model;
	
	/**
	* @var entity_model
	*/
	public $entity_model;
	
	/**
	* @var org_model
	*/
	public $org_model;
	
	/**
	* @var Finance_model
	*/
	public $finance_model;
	
	public function __construct()
	{
		parent::Controller();
		$this->load->model('endeavor_model');
		$this->load->model('associations_model');
		$this->load->model('teams_model');
		$this->load->model('permissions_model');
        $this->load->model('finance_model');
		$this->load->model('entity_model');
		$this->load->model('entity_model');
		$this->load->model('org_model');
		$this->load->library('page');
		$this->load->library('input');
		$this->load->library('result');
        
         
	}
	
	private function load_window()
	{
		$this->load->library('window');
		$this->window->set_css_path("/assets/css/");
		$this->window->set_js_path("/assets/js/system/");
	}
    
	public function index()
	{                 
        unset($_SESSION);
		session_destroy();
		$data['menubar'] = "";//$this->get_menubar();
		$this->load->view('spectrum2', $data);
	}
	
	public function html_menubar()
	{
		//echo $this->get_menubar();
	}
	
	public function get_menubar()
	{
		$user = $this->permissions_model->get_active_user();
		$org = $this->permissions_model->get_active_org();
		$links = $this->endeavor_model->get_menu(false,$user,$org);
		$this->result->json($links);
	}
	
	public function html_welcome()
	{
		$orgid  = $this->permissions_model->get_active_org(); 
		$type   = $this->org_model->get_org_org_type($orgid);
		$org    = $this->org_model->get_org($orgid);
		
		switch($type)
		{
			case 1: $view = "spectrum"; break;
			case 2: $view = "association"; break;
			case 3: $view = "league"; break;
			case 4: $view = "tournament"; break;
			//case 5: $view = "umpireassociation"; break;
			case 6: $view = "team"; break;
			//case 7: $view = "city"; break;
			//case 8: $view = "general"; break;
			//case 9: $view = "facility"; break;
		}
		                                                                
        $this->load->library('window');
		$this->window->add_js('/assets/js/components/welcome/class.welcome.js');
		$this->window->add_js("/assets/js/components/welcome/spectrum.welcome.$view.js");
		$this->window->set_header('Welcome');
		$this->window->set_body($this->load->view("welcome/$view",$org,true));
		$this->window->set_footer('');
		$this->window->json();
	}
	
	public function get_css_file($cached=true)
	{
		
		$data = array('rules'=>array());
		
		//list of icons
		$icons = array();
		$used = $this->endeavor_model->get_used_icons();
		foreach($used as $use) $icons[] = $use['icon'];
		
		//directories to read
		$dirs = array();
		$dirs[] = array("../global_assets/social/","social_"); 
		$dirs[] = array("../global_assets/silk-disabled/","","-disabled"); 
		$dirs[] = array("../global_assets/silk/");
		$dirs[] = array("../global_assets/flags/","fflag_");
		$dirs[] = array("../global_assets/fugue/","fugue_");
		$dirs[] = array("../global_assets/sweetie/","sweet_");
		$dirs[] = array("../global_assets/stl_icons/");

		foreach($dirs as $mydir)
		{
			$d = dir($mydir[0]);
			$path = str_replace("../","http://endeavor.servilliansolutionsinc.com/",$mydir[0]);
			while($entry = $d->read()) { 
				if ($entry!= "." && $entry!= "..")
				{ 
					list($class,$ext) = explode(".",$entry);
					
					// must be png
					if($ext!='png') continue;
					
					//set full classname
					$class = @$mydir[1].$class.@$mydir[2];
					
					//make sure we're using the icon'
					if(in_array($class, $icons))
					{
						$data['rules'][] = array(
							'entry'=>$entry,
							'path'=>$path,
							'class'=>$class
						);
					} 
				}
			} 
			$d->close();
		}
		
		$this->load->view('icon_class_list',$data);
	}
	
	/***************************************************/
	/************* AUTH REQUIRED METHODS ***************/
	/***************************************************/
	
	public function window_authrequired()
	{
		$data['body'] = array();
		$data['footer'] = array();
		$this->load->library('window');
		$this->window->set_header('Authentication Required');
		$this->window->set_body($this->load->view('endeavor/authrequired',null,true));
		$this->window->set_footer('');
		$this->window->json();
	}
	
	public function json_authrequired()
	{
		$response = array('status'=>'AUTHERROR','html'=>$this->load->view('endeavor/authrequired',null,true));
		$this->result->json($response);
	}
	
	public function html_authrequired()
	{
		$this->load->view('endeavor/authrequired');
	}
	
	/***************************************************/
	/*********** END AUTH REQUIRED METHODS *************/
	/***************************************************/
	/*
	public function window_maintenance()
	{//probably not used anymore check permissions controller
		$data['body'] = array();
		$data['footer'] = array();
		$this->load_window();
		$this->window->add_js('class.system.maintenance.js');
        
        $this->window->add_css('maintenance.css');
		$this->window->set_header('Endeavor Method / Controller Maintenance');
		$this->window->set_body($this->load->view('endeavor/maintenance',$data['body'],true));
		$this->window->set_footer($this->load->view('endeavor/maintenance.footer.php',$data['footer'],true));
		$this->window->json();
	}
	
	public function window_about()
	{
		$data['body'] = array();
		$data['footer'] = array();
		$this->load_window();
		$this->window->add_js('class.about.js');
		$this->window->set_header('About Endeavor');
		$this->window->set_body($this->load->view('endeavor/about',$data['body'],true));
		$this->window->set_footer($this->load->view('endeavor/about.footer.php',$data['footer'],true));
		$this->window->json();
	}
   */
	
	public function post_maintenance()
	{//shouldnt ber used 
		echo $this->endeavor_model->process_controllers();
	}
    
    public function get_roles()
	{
        $this->result->json($this->permissions_model->get_parentroles());
    }
    
    public function get_controllers()
    {
        $this->result->json($this->permissions_model->get_controllers());
    }
    /**
    * depreciated
    * 
    */
    public function get_groups()
    {
        $c= $this->input->post('controller');
        
        $this->result->json($this->permissions_model->get_groups($c));
    }
    /**
    * depreciated
    * 
    */
    public function get_rolegroup()
    {//not used
        $methodgroup= $this->input->post('methodgroup');
        $role= $this->input->post('role');
        
        $this->result->json($this->permissions_model->get_rolegroup($methodgroup,$role));
    }
    /**
    * depreciated
    * 
    */
    public function post_updaterolegroup()
    {
        $perm = json_decode( $this->input->post('perm'));
        $groups= json_decode($this->input->post('groups'));
        $role= $this->input->post('role');

        $user = $this->permissions_model->get_active_user();
        $results= array();
        
        foreach($groups as $group)
        {
            $results[] =$this->permissions_model->update_rolegroup($perm,$group,$role,$user);  
        }
        echo $results;
    }
    
    public function get_active_roles()
    {
        $userid = $this->permissions_model->get_active_user();
        $this->result->json($this->permissions_model->get_user_assignments($userid));
    }
    
    public function get_active_orgs()
    {
        $userid = $this->permissions_model->get_active_user();
        $all_assigns = $this->permissions_model->get_user_assignments($userid);
        $used_orgs = array();
        $orgs = array();
        foreach($all_assigns as $a)
        {
        	$id = $a['org_id'];
			if(isset($used_orgs[$id])) continue;
			
			$used_orgs[$id] = true;
			if($a['default_org_id'] == $id) $a['image']  = 'star';//hard coded to show default as this
			//$src = "http://endeavor.servilliansolutionsinc.com/global_assets/silk/".$a['image'] .".png ";
			//$a['org_image'] = "<img src='$src' />";
			$a['org_image'] = $a['image'] .".png ";
			$orgs[] = $a;
        }
        $this->result->json($orgs);
    }
    
    
   
   /**
   * @return 	json echo's json to the screen as output
   * 
   */
    public function json_getactive_org_logo()
    {
        $a_o_id = $this->permissions_model->get_active_org();
        $logo   = $this->org_model->get_org_logo($a_o_id);    
        echo $logo;
    }
    	/**
	* Displays the panel information on the right hand side below the links
	* 
	* @name		html_panel
	* @author 	Bradley Holbrook
	* @access	Public
	* 
	* @lastedit	2011-11-02 10:49AM BAH Coding Cleanup
	* 
	* 
	*/
	public function html_panel()
	{
		//default data
		$data = array(
			'url'           =>false,
			'default_org_id'=>false,
			'user_name'     =>false,
			'user_id'       =>false,
			'org_name'      =>false,
			'accounts'      =>array()
		);
		
		//declarations
		$userid = $this->permissions_model->get_active_user();
		$orgid  = $this->permissions_model->get_active_org();
		$entity = $this->org_model->get_entity_id_from_org($orgid);
		$url_data=$this->entity_model->get_entity_url($entity);
		$roles  = $this->permissions_model->get_user_assignments($userid);
		$data['logo']	= $this->org_model->get_org_logo($orgid);
        $fb_id  = $this->permissions_model->get_active_fb_id();
        
 
        
        $role_names=array();
        
        foreach($roles as $r)
        {
			if($r['org_id']==$orgid)
				$role_names[]=$r['role_name'];
        }
        // var_dump($role_names);
        //FB_ID
        $data['fb_id'] = $fb_id;
        $data['role_names'] = $role_names;
 
		//get username
		if($userid != null && $userid != '' && $userid > 0)
        {
            $userData = $this->permissions_model->get_user_data($userid);
            if(count($userData)>0)
            {
	            $userName = $userData[0]['person_fname'] ." ". $userData[0]['person_lname'];
	            $data['user_name'] = $userName;
	            $data['default_org_id'] = $userData[0]['default_org_id'];
			}
        }
        $data['user_id'] = $userid;
        
        //get active org name
        foreach($roles as $role)
		{
			$id = $role['org_id'];
			$name = $role['org_name'];
			if($id == $orgid) $data['org_name'] = $role['org_name'];
		}
        
        //add url
		if(isset($url_data[0]['url']))
		{
        	$url=$url_data[0]['url'];
        	$data['url'] = $url;
		}
        
		//get account information from finance model
        $data['accounts'] = $this->finance_model->get_org_available_fund();
        
		// load view
		$this->load->view('endeavor/panel',$data);
				
	}
	/**
	* takes in controller id(int)
	* method name or id
	* and name of help view file
	* attaches them in system.sys_help
	* 
	*/
    public function post_ctr_mth_help()
    {
		$this->load->library('result');
		$controller=$this->input->get_post('controller_id');
		if(!$controller || $controller<0)
		{
			$this->result->failure('controller not found');
			 
			return;
		}
		$method    =$this->input->get_post('method_id');
		$data    =$this->input->get_post('view_filename');
		//handles either method id or name
		if(!$method)
		{
			$method = $this->input->get_post('method_name');
			$method=$this->permissions_model->get_method_id_by_name($method,$controller);
			if(!isset($method[0])) 
			{ 
				$this->result->failure("Method not found in given controller");
				return;
			}
			$method = $method[0]['method_id'];
		}
		$this->result->success( $this->endeavor_model->insert_help($controller,$method,$data) );
    }
    
    /**
    * @author sam
    * 
    * takes the method name for one window (tab) and 
    * returnsthe data for the help bar
    * mimics what a database would do
    * for now it is just SWITCH statement
    * 
    * @param mixed $controller
    * @param mixed $window_method
    */
    public function get_window_help_data($controller,$window_method)
    {
    	$iconIdx='iconCls';
		$dataIdx='data';
		
    	$key=$controller.'/'.$window_method;
		switch($key)
		{
			case 'permissions/window_my_org'://Users
				
				$help_data[]=array($iconIdx=>'fugue_user--plus',$dataIdx=>'Add a new or existing user');
				$help_data[]=array($iconIdx=>'fugue_user-white',$dataIdx=>'Add myself');
				$help_data[]=array($iconIdx=>'fugue_user-worker',$dataIdx=>'Add extra roles');
				$help_data[]=array($iconIdx=>'fugue_user--minus',$dataIdx=>'Delete Role');
				$help_data[]=array($iconIdx=>'fugue_user--pencil',$dataIdx=>'Edit user');
				
				
			
			break;
 			case 'facilities/window_managevenfac'://Facilities
 			
 			
 			
 			break;
 			case 'finance/window_manage_invoices':
 			
 			
 			break;
 
			
			case 'finance/window_manage_authority':
			
			break;
			case 'finance/window_manage_reports':
			
			break;
			case 'finance/window_manage_transaction_payment':
			
			break;
			case 'finance/window_manage_setup':
			
			break;
 
			case 'websites/window_managepublishing':
			
			break;
			case 'websites/window_manageadvertising':
			
			break;
			case 'prize/window_manageorders':
			
			break;
			case 'prize/window_manage':
			
			break;
			case 'season/window_manage':
			
			break;
			case 'divisions/window_managedivisions':
			
			break;
			case 'teams/window_manage':
			
			break;
			case 'schedule/window_league_schedule':
			
			break;
			case 'schedule/window_manage':
			
			break;
			case 'games/window_results':
			
			break;
			case 'statistics/window_managestandings':
			
			break;
			case 'leagues/window_manage_blackouts':
			
			break;
			case 'teams/window_managerosters':
			
			break;
			case '':
			
			break;
			case '':
			
			break;
			case '':
			
			break;
			
		}
    }
    
    /**
    * based on controller and method id, this gets help file for it
    * triggered by class.spectrum.js method updateHelp, which 
    * in turn was triggered by active tab change event in the viewport 
    * 
    */
    public function html_help()
    {
		$controller=$this->input->post('cname');
		$method    =$this->input->post('mname');
		//get ids from names
		//$use_default=false;
		$ctr = $this->permissions_model->get_controller_id_by_name($controller);
		if(!isset($ctr[0])) 
		{
			//these wont be found on the welcome screen, for example
			//$use_default=true;
			return;
		}
		$DEFAULT='default.php';
		$c_id = $ctr[0]['controller_id'] ;
		$meth = $this->permissions_model->get_method_id_by_name($method,$c_id);
		if(!isset($meth[0])) {return;}
		$m_id = $meth[0]['method_id'];
		//get the help file data
		$file_data = $this->endeavor_model->get_help($c_id,$m_id);
		
		$help = (count($file_data)==0 
			|| !isset($file_data[0]['data'])//if not defined or no file exists
			|| !@file_exists(APPPATH."views/help/".$file_data[0]['data']) )
			? $DEFAULT//a default view if no help file is found
			: $file_data[0]['data'] ;//use the file saved in db
		
		//to change the file above, go to maintenance and use the help form (middle table, bottom left)
		//tehy are stored in system.sys_help table
 		
 		IF($help === $DEFAULT)
 		{
			//if we are loading default view, then check these hardcoded guys 
			//EVENTUALLY, these data values would possibly be stored in database
			//attached to this window method OR this help screen,
			//put there by some bulider
			
			
			//the array varaibles - in future would be column names
			$iconIdx='iconCls';
			$dataIdx='data';
			
			$data['iconIdx']=$iconIdx;
			$data['dataIdx']=$dataIdx;
			 
			$help_data=array();
			
			
			//mimics a database call, for now this method is just a SWITCH statement
			$data['help_data']=$this->get_window_help_data($controller,$method);
			
			$data['right']=array();//data for right panel?
			
 		}
 		
 		
		 
		$data['hidden']=array('controller_id'=>$m_id,'method_id'=>$m_id,'method_name'=>$controller.'/'.$method);//not really used
		
		$this->load->view('/help/'.$help,$data);
    }
    
    /**
    * get all data for active org
    * 
    * includes address, url, name, ids, everything
    * 
    * @access public
    * @author Sam Bassett
    * @return json array data
    * 
    * 
    * 
    */
    public function json_active_org_full_details()
    {
		$org = $this->permissions_model->get_active_org();
    	$SHIPPING = '1';
    	$OPERATIONAL = '2';
		$this->load->model('entity_model');
		$this->load->model('org_model');
        $result_org=$this->org_model->get_org_details_and_url($org);
        
        $entity_id = $result_org['entity_id'];
        
        $org_type = $result_org['org_type'];
        switch($org_type)
        {
			case ORG_TYPE_ASSOC:
				//need assoc id, name, website, etc
				$asn_data = $this->associations_model->get_association_byorg($org);
				$asn_data = (count($asn_data)==0) ? array() : $asn_data[0];
        		$result_org=array_merge($result_org,$asn_data);
			 
			break;
			case ORG_TYPE_LEAGUE://league id
			
				$this->load->model('leagues_model');
				$result_org['league_id']=$this->leagues_model->league_id_from_org($org);
			
			break;
			case ORG_TYPE_TEAM:
			//get team id
				
				$result_org['team_name'] = $result_org['org_name'];
				$result_org['team_id'] = $this->teams_model->get_team_id_byorg($org);
			break;
        }
        ///get team league and assoc id
        $result_addr=array();//so array merge will not cuase errors if  empty
        $ship = $this->entity_model->get_entity_address($entity_id,null,$SHIPPING);
        $oper = $this->entity_model->get_entity_address($entity_id,null,$OPERATIONAL);
        $ship = (count($ship)) ? $ship[0] : array();
        $oper = (count($oper)) ? $oper[0] : array();
        
        //for emtpy records, they must at least have their type od, for saving later
        if(!isset($ship['address_type'])) $ship['address_type'] = $SHIPPING;
        if(!isset($oper['address_type'])) $oper['address_type'] = $OPERATIONAL;
        $result_addr['ship'] = $ship;
        $result_addr['oper'] = $oper;
  
        $result = array_merge($result_org,$result_addr);
        $this->result->json($result);
    }
    /**
    * builds a menu t oselect based on address id
    * @author sam
    * @since nov 24 2011
    * 
    */
    public function json_org_address_menu()
    {
		$org=$this->input->get_post('org_id');
		if(!$org)$org=$this->permissions_model->get_active_org();
		$ent=$this->org_model->get_entity_id_from_org($org);
		
		
		$addrs=$this->entity_model->get_entity_address($ent);
		
		$menu=array();
		foreach($addrs as $a)
		{
			if(!$a['address_street'] || !$a['country_abbr']) continue;//empty address
			
			$name = $a['address_street']." ".$a['country_abbr']." ".$a['postal_value'];
			$menu[]=array('name'=>$name,'value'=>(int)$a['address_id']);
			
			
			
		}
		$this->result->json($menu);
    }
    /**
    * gets org adddress based on address_id
    * if no org_id posted, defaults to active org
    * 
    * @author sam
    * @access public
    * @since nov24 2011
    * 
    */
    public function json_org_single_address()
    {
		$org=(int)$this->input->get_post('org_id');
		$address_id=(int)$this->input->get_post('address_id');
		if(!$org)$org=$this->permissions_model->get_active_org();
		$ent=$this->org_model->get_entity_id_from_org($org);
		$a=$this->entity_model->get_entity_address($ent,$address_id);
		$a = count($a)==0 ? array() : $a[0];
		$this->result->json($a);
		
    }
    public function json_getDomainNames()
    {
        $result= $this->org_model->getDomainNames();    
        $this->result->json_pag_store($result);
    }
        
	/**
	* updated to allow org_id to be posted. default is still active org
	* for a persons address, see person controller. similar functoin, 
	* and we areally should reconcile them
	*/
	public function post_org_address()
    {
    	$owner=$this->permissions_model->get_active_org();
    	
        $org=(int)$this->input->get_post('org_id');
        if(!$org || $org<0)  $org = $owner;
        	
        
		$user=$this->permissions_model->get_active_user();
		 
		$entity_id=(int)$this->org_model->get_entity_id_from_org($org);
        
        
        
        
        $address_id=(int)$this->input->get_post('address_id');
        $street=$this->input->get_post('address_street');
        $city=$this->input->get_post('address_city');
        $postal_value=$this->input->get_post('postal_value');
       
        $region_abbr=$this->input->get_post('region_abbr');
         if($region_abbr=='null') $region_abbr=null;
      
        $country_abbr=$this->input->get_post('country_abbr');
         if($country_abbr=='null') $country_abbr=null;
        $type=(int)$this->input->get_post('address_type');
 
        
 		if($street || $city || $postal_value || $country_abbr )
 		{//do not save if Alll.ll are blank
 		
 			//this wlil insert OR update
			$address_id = $this->entity_model->update_address($address_id,$street,$city,$region_abbr,
					$country_abbr,$postal_value,$entity_id,$type,$user,$owner);

 		 
		}
 
	 //get lat long of New updated address, and save it
		$lat=null;
		$lng=null;
        $curl_latlong = $this->entity_model->get_entity_lat_long($entity_id,$address_id);
        
        if(is_array($curl_latlong)&&$curl_latlong['success']===true &&$curl_latlong['lat'] &&$curl_latlong['lng'])
        {
        	//if the curl worked, then get the latlong
        	$lat=$curl_latlong['lat'];
        	$lng=$curl_latlong['lng'];
        	$this->entity_model->update_entity_lat_lon($address_id,$lat,$lng);
			
        }

    	$response = array('success'=>true,'result'=>$address_id);
    	$this->result->json($response);
    }
    /**
    * update org name by passing id from ajax
    * 
    * modified to also take org ogo, if it exists
    * 
    * 
    * @author Sam Bassett
    * @access public
    * 
    */
    public function post_org_name()
    {
		
        $org_name=$this->input->get_post('org_name');
        $org_id=$this->input->get_post('org_id');
		//$org_type = $this->org_model->get_org_type($org_id);
		
        $user=$this->permissions_model->get_active_user();
        $result['success']=(bool)$this->org_model->update_org_name($org_id,$org_name,$user);
        
  
		//either way, do the image file if create or update
		
		//file_upload
		$file_id = "file_upload";//the name of the field in the form 
        $this->load->library('images');
    	if($this->images->type_is_valid_image($file_id))
	    {//only if its a valid image .  so do not upload .txt, .php,.exe, etc
	    
        	//on success, the upload function returns the modified file name
			$result['upload']= $this->org_model->upload($file_id,ORG_LOGO_BASEPATH,$org_id);
			if($result['upload'])//which we then save in the entity_org table
				{$this->org_model->update_entity_org_logo($org_id,$result['upload']);}
        }
        $this->result->json($result);
    }
 
    
    //FACEBOOK
    
    public function json_adjust_fb_friends()
    {
        $fb_friends=$this->input->get_post('fb_friends');
        $fb_id=$this->input->get_post('fb_id');
        
        $result=$this->endeavor_model->adjust_fb_friends($fb_id,$fb_friends);
        $this->result->success(  $result[0]["adjust_fb_friends"]);                    
    }
    public function json_get_facebook_friends()
    {
        $fb_id=$this->input->get_post('fb_id');                             
        $result=$this->permissions_model->get_facebook_friends($fb_id);    
        $this->result->json_pag($result);
    }
    
    
    //SYS MENU COMPONENT
    /**
    * @author   Ryan
    * @access   Public
    */
    public function window_manage_sysmenu()
    {
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/");
        $this->window->set_js_path("/assets/js/");
        
        $this->window->add_css('');
                                                
        //Grids and Treeviews
        $this->window->add_js('components/maintenance/grids/spectrumtreeviews.sysmenu.js');
        $this->window->add_js('components/maintenance/grids/spectrumgrids.roles.js');
        
        //Forms and Windows
        $this->window->add_js('components/maintenance/forms/forms.js');
        $this->window->add_js('components/maintenance/windows/spectrumwindow.maintenance.js');
        
        
        $this->window->add_js('components/maintenance/controller2.js'); 
        $this->window->add_js('components/maintenance/toolbar.js');
        
        $this->window->set_header('Manage System Menu');
        $this->window->set_body($this->load->view('endeavor/maintenance.main.php',null,true));
        $this->window->json();
    }
    public function json_get_sysmenus()
    {                    
        $target     =array();
        $Plot       =array();
        $this->source   =$this->endeavor_model->get_sysmenus();
        
        if($this->source == null)
            $this->source=array();
        else
            $this->source=$this->A_Sort($this->source,'order');
            
        foreach($this->source as $i=>$v)               
            $Plot[$i]=array($v["parent"],$v["id"]/*First 2 Compulsory*/,$v["menu_active"]);
            
        foreach($this->source as $i=>$v)
        {
            if ($v["parent"]==null)
            {  
                if($v["menu_active"]=='f')
                    $target[]=array_merge($v,array('iconCls'=>"bullet_red","leaf"=>false,"expanded"=>true,"checked"=>false));
                else
                    $target[]=array_merge($v,array('iconCls'=>"bullet_green","leaf"=>false,"expanded"=>true,"checked"=>true));
                             
                $this->Rec($target[count($target)-1]);
            }                                                  
        }  
        
        echo   json_encode(array("root"=>"","children"=>$target,"Plot"=>$Plot)) ;
    }
    private function A_Sort($a,$subkey) 
    {
        foreach($a as $k=>$v) $b[$k] = strtolower($v[$subkey]);
        asort($b);
        foreach($b as $key=>$val) $c[] = $a[$key];
        return $c;
    }
    public $source=array();             
    public function Rec(&$parent)
    {           
        $has_child=false;             
        foreach($this->source as $i=>$v)
        if($v["parent"]==$parent["id"])
        {
                $has_child=true;
                if($v["menu_active"]=='f')
                    $parent["children"][]=array_merge($v,array('iconCls'=>"bullet_red"  ,"leaf"=>false  ,"expanded"=>true,"checked"=>false));
                else
                {
                    if($parent["menu_active"]=='f')
                        $parent["children"][]=array_merge($v,array('iconCls'=>"bullet_yellow","leaf"=>false  ,"expanded"=>true,"checked"=>true));
                    else
                        $parent["children"][]=array_merge($v,array('iconCls'=>"bullet_green","leaf"=>false  ,"expanded"=>true,"checked"=>true));
                        
                }
                    
                
                $ret_has_child=$this->Rec($parent["children"][count($parent["children"])-1]);
                
                //collapse ones with no child
                if($ret_has_child==false)$parent["children"][count($parent["children"])-1]["expanded"]=false;
        }       
        
        if($has_child==false)//make a fake rec for leaf node
        {          
            $parent["children"][]               =array("title"=>'');
            $parent["children"][0]["leaf"]      =true;
            $parent["children"][0]["iconCls"]   ='transparent';
        }    
        return $has_child;                       
    }
    public function json_updateSysMenuOrdering()
    {
        /*
        *  complex_str  combination of comma based [parent  - id - active]
        */                                                  
        $complexStr     =$this->input->get_post("complex_str");
        $result         = $this->endeavor_model->updateSysMenuOrdering($complexStr);
        $this->result->success(   $result[0]["updatesysmenuordering"]);
    }
    public function json_getMenuTypeItem()
    {
        $result=$this->endeavor_model->getMenuTypeItem();
        $this->result->json_pag($result);    
    }
    public function json_getRoleMenu()
    {
        $menu_id        = $this->input->get_post("menu_id");
        
        $result         = $this->endeavor_model->getRoleMenu($menu_id);
        $this->result->json_pag($result);
    }
    public function json_getMenuAuthStore()
    {
        $result=$this->endeavor_model->getMenuAuthStore();
        $this->result->json_pag_store($result);    
    }
    public function json_deleteSelectedMenuItemsRoles()
    {
        $menurole_ids   = $this->input->get_post("menurole_ids");
        
        $result         = $this->endeavor_model->deleteSelectedMenuItemsRoles($menurole_ids);
        $this->result->success(   $result[0]["deleteSelectedMenuItemsRoles"]);    
    }
    public function json_addSelectedMenuItemsRoles()
    {
        $menuroleauth_ids= $this->input->get_post("menuroleauth_ids");
        
        $result         = $this->endeavor_model->addSelectedMenuItemsRoles($menuroleauth_ids);
        $this->result->success(    $result[0]["addSelectedMenuItemsRoles"]);    
    }
    public function json_updateMenuItem()
    {
        $menu_id        = $this->input->get_post("menu_id");
        $menuItemLabel  = $this->input->get_post("menuItemLabel");
        $menuItemType   = $this->input->get_post("menuItemType");
        
        $menu_group     = $this->input->get_post("menu_group");
        $menu_default   = $this->input->get_post("menu_default");
        $menu_rowspan   = $this->input->get_post("menu_rowspan");
        $menu_colspan   = $this->input->get_post("menu_colspan");
        $menu_image     = $this->input->get_post("menu_image");
        
        $result         = $this->endeavor_model->updateMenuItem($menu_id,$menuItemLabel,$menuItemType,$menu_group,$menu_default,$menu_rowspan,$menu_colspan,$menu_image);
        $this->result->success( $result[0]["updateMenuItem"]);
    }
    public function json_getMenuWindowType()
    {
        $menu_id        = $this->input->get_post("menu_id");
        
        $result=$this->endeavor_model->getMenuWindowType($menu_id);
        $this->result->json_pag_store($result);        
    }
    public function json_updateControllerWindow()
    {
        $menu_id            = $this->input->get_post("menu_id");
        $window_controller  = $this->input->get_post("window_controller");
        $window_method      = $this->input->get_post("window_method");
        $window_id          = $this->input->get_post("window_id");
        $new_old_cb         = $this->input->get_post("new_old_cb");
        
        $result=$this->endeavor_model->updateControllerWindow($menu_id,$window_controller,$window_method,$window_id,$new_old_cb);
        $this->result->success(  $result[0]["updateControllerWindow"]);
    }
    public function json_updateExternalLink()
    {
        $menu_id        = $this->input->get_post("menu_id");
        $link_href      = $this->input->get_post("link_href");
        
        $result=$this->endeavor_model->updateExternalLink($menu_id,$link_href);
        $this->result->success(  $result[0]["updateExternalLink"]);    
    }
    public function json_saveNewMenuItem()
    {
        $menu_label = $this->input->get_post("menu_label");
        
        $result=$this->endeavor_model->saveNewMenuItem($menu_label);
        $this->result->success(  $result[0]["saveNewMenuItem"]);        
    }
    public function json_deleteMenuItem()
    {
        $menu_id = $this->input->get_post("menu_id");
        
        $result=$this->endeavor_model->deleteMenuItem($menu_id);
        $this->result->success(  $result[0]["deleteMenuItem"]);
    }
    
    
    
    
    
    public function post_system_icons()
    {
		$icon_csv = explode(',',rawurldecode($this->input->get_post('sys_icons')) );
		$success=array();
		foreach($icon_csv as $icon)
		{
			$icon=str_replace(' ','',$icon);//first strip out all whitespace
			if($icon!=='')//if not empty string
				$success[$icon]= $this->endeavor_model->handle_icon($icon);
		}
		
		 $this->result->success($success);
		
    }
    

}
