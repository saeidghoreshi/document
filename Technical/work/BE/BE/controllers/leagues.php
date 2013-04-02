<?php
require_once('endeavor.php');   
class Leagues extends Endeavor
{

	/**
	* 
	* @var season_model
	*/
	public $season_model;
	/**
	* @var Permissions_model
	*/
	public $permissions_model;
	
	/**
	* @var Leagues_model
	*/
	public $leagues_model;
	
	/**
	* @var Schedule_model
	*/
	public $schedule_model;
	/**
	* @var Teams_model
	*/
	public $teams_model;
	
	/**
	* 
	* @var entity_model
	*/
	public $entity_model;
	
	/**
	* 
	* 
	* @var associations_model
	*/
	public $associations_model;
	
	public function __construct()
	{
	    parent::Controller();
	    $this->load->model('person_model');
	    $this->load->model('endeavor_model');
	    $this->load->model('associations_model');
	    $this->load->model('teams_model');
	    $this->load->model('permissions_model');
	    $this->load->model('leagues_model');
	    $this->load->model('schedule_model');
	    $this->load->model('season_model');
	    $this->load->model('entity_model');
	    $this->load->library('page');
	    $this->load->library('input');
		$this->load->library('result');
	    
	}
	
	private function load_window()
	{
	    $this->load->library('window');
	    //$this->window->set_css_path("/assets/css/inventory/");
	    $this->window->set_js_path("/assets/js/components/leagues/");
	}
	
	//DEPRECIATED. MOVED TO Seasons CLASS.
	//public function window_manage_seasons()
	
	
	//DEPRECIATED: MOVED TO divisions
	//public function window_managedivisions()
	/*public function window_createteam()
	{   
	    $data = array();

	    $this->load_window();
	    $this->window->add_css('');  
		//   $this->window->set_js_path("/assets/js/leagues/");
	    $this->window->add_js('class.leagues.new_team.js');
	    
	    $this->window->set_header('Create New Team');
	    $this->window->set_body($this->load->view('leagues/leagues.new_team.php',$data,true));
	    $this->window->set_footer($this->load->view('leagues/leagues.new_team.footer.php',null,true));
	    //if($id) $this->window->set_id($id);
	    $this->window->json();
	    
	}*/
	
    

    //************************************************************************************************  Blackout - at league level    

    public function window_manage_blackouts()
    {                                                                                                          
        $this->load->library('window');
	    //$this->window->set_css_path("/assets/css/");
        $this->window->add_js('/assets/js/models/blackout.js');//model needed for grid
	    $this->window->set_js_path("/assets/js/components/blackout/");
        
        $this->window->add_js('forms/create_edit.js');
        $this->window->add_js('windows/create_edit.js');
        
        $this->window->add_js('grids/blackout.js');
                                                           
        $this->window->add_js('controller.js');                                                           
                                                                                                                     
        $this->window->set_header('Manage Blackout');
        $this->window->set_body($this->load->view('leagues/blackout.php',null,true));
        $this->window->json();
    }
    public function json_get_blackouts()//League - level function
    {
        $season_id      =(int)$this->input->get_post('season_id');
        
        $result=$this->leagues_model->get_blackouts($season_id);

        $this->result->json_pag($result);
    }
    public function json_update_blackout()
    {
        
        $bo_id         =(int)$this->input->get_post('bo_id');      
        $season_id     =(int)$this->input->get_post('season_id');    
        $bo_type_id    =(int)$this->input->get_post('bo_type_id');     
        $bo_start_date =$this->input->get_post('bo_start_date');      
        $bo_end_date   =$this->input->get_post('bo_end_date');      
        $desc   =$this->input->get_post('bo_user_desc');      
        
        //if dates are out of order, swap them
        //we know they cannot be null already so strtotime is safe here
        $s=strtotime($bo_start_date);
        $e=strtotime($bo_end_date);
        if($e<$s)
        {
        	
			$swap=$s;
			$s=$e;
			$e=$swap;
        }
        $postgres='Y-m-d';//cast for postgres timestamp field
        $bo_start_date=date($postgres,$s);
        $bo_end_date  =date($postgres,$e);
        if(!$bo_id || $bo_id== -1)//if PK not given , or given as -1, then create
        {
			$result=$this->leagues_model->new_blackout($season_id,$bo_start_date,$bo_end_date,$bo_type_id,$desc);
        }
        else//otherwise PK exists, so update
        {
			$result=$this->leagues_model->update_blackout($bo_id,$bo_start_date,$bo_end_date,$bo_type_id,$desc);
        }
        
        $this->result->success(  $result) ;
    }
 
    public function json_delete_blackout()
    {
        $bo_id=$this->input->get_post('bo_id');
        $result=$this->leagues_model->delete_blackout($bo_id);
        $this->result->success(  $result[0]["delete_blackout"]) ;
    }
    //************************************************************************************************
	
 
 
	
	public function json_get_unprocessed_details_by_team_id()
	{
	    $team_id=$this->input->get_post("team_id");
	    $result=$this->leagues_model->get_unprocessed_details_by_team_id($team_id);
	    $this->result->json($result);
	}

	public function json_get_leagueteams()
	{
		$org =$this->permissions_model->get_active_org();
		$this->result->json($this->leagues_model->get_league_teams($org));
		
	}
	
	public function json_get_teams_with_exceptions()
	{
		$org =$this->permissions_model->get_active_org();
		$this->result->json($this->leagues_model->get_teams_with_exceptions($org));
		
	}
	
/*
	public function json_get_leaguedivteams()
	{
		$org =$this->permissions_model->get_active_org();
		$div_id = $this->input->post('division_id');
		$season_id=$this->input->post('season_id');
		echo json_encode($this->leagues_model->get_league_div_teams($org,$div_id,$season_id));
		
	}*/

	public function json_save_team_unprocessed_by_team_id()
	{
	    $team_id 	  = $this->input->get_post("team_id");
	    $team_name 	  = $this->input->get_post("team_name");
	    $team_calibre = $this->input->get_post("team_calibre");
	    $firstname 	  = $this->input->get_post("firstname");
	    $lastname 	  = $this->input->get_post("lastname");
	    $address 	  = $this->input->get_post("address");
	    $city 	   	  = $this->input->get_post("city");
	    $gender 	  = $this->input->get_post("gender");
	    $province 	  = $this->input->get_post("province");
	    $country 	  = $this->input->get_post("country");
	    $postalcode   = $this->input->get_post("postalcode");
	    $primaryphone = $this->input->get_post("primaryphone");
	    $secondaryphone=$this->input->get_post("secondaryphone");
	    $email 	      = $this->input->get_post("email");
	    $username 	  = $this->input->get_post("username");
	    
	    $result = $this->leagues_model->save_team_unprocessed_by_team_id($team_id,$team_name,$team_calibre,$firstname,$lastname,$address,$city,$gender,$province,$country,$postalcode,$primaryphone,$secondaryphone,$email,$username);
	    $this->result->json($result);
	}

	public function json_approve_team()
	{
	    $team_temp_id=$this->input->get_post("team_temp_id");
	    $season_id=$this->input->get_post("season_id");
	    $result=$this->leagues_model->approve_team($team_temp_id,$season_id); 
	    $this->Email("noreply@servilliansolutionsinc.com","ryan.goreshi@yahoo.com","","",$result,$result);
	    $this->result->json($result);
	}

	public function json_transfer_teams()
	{
	    $result=$this->leagues_model->transfer_teams();
	}

	public function json_activate_season()
	{
	    $season_id=$this->input->post("season_id");
	    $result=$this->leagues_model->activate_season($season_id);
	    $this->result->json($result);
	}

	public function json_get_activated_season()
	{                                   
	    $result=$this->leagues_model->get_activated_season();
	    $this->result->json($result);
	}

	public function json_get_teams_reg_info_by_season_id()
	{
	    $season_id=$this->input->get_post("season_id");
	    $team_temp_id=$this->input->get_post("team_temp_id");
	    
	    $result=$this->leagues_model->get_teams_reg_info_by_season_id($season_id,$team_temp_id);
	    $this->result->json($result);
	    
	}

	public function json_get_team_temp_list_by_season_id()//one team
	{
	    $season_id=$this->input->get_post("season_id");
	    $team_temp_id=$this->input->get_post("team_temp_id");
	    
	    $result=$this->leagues_model->get_team_temp_list_by_season_id($season_id,$team_temp_id);
	    $this->result->json($result);
	}

	public function json_get_teams_temp_list_by_season_id()
	{
	    $season_id=$this->input->get_post("season_id");
	    
	    $result=$this->leagues_model->get_teams_temp_list_by_season_id($season_id);
	    $this->result->json($result);
	}

	
	public function json_teams_by_season()
	{
		$org=$this->permissions_model->get_active_org();
	    $season_id=(int)$this->input->post("season_id");
		if(!$season_id || $season_id==-1)
			$teams= $this->leagues_model->get_league_teams_null_season($org);
		else
			$teams=$this->leagues_model->get_league_teams_by_season($org,$season_id);
			
		$this->result->json($teams);
	}
	/**
	* get seasons for the active org league
	* @deprecated use seasons model
	*/
	public function json_get_seasons()
	{
		 
		$org = $this->permissions_model->get_active_org();
		$league_id=$this->leagues_model->get_league_from_org($org);
	    $this->result->json($this->season_model->get_seasons($league_id));
	   
	}

	 public function json_get_leagues()
    {
        //$a_u_id= $this->permissions_model->get_active_user();
        $org_id= $this->permissions_model->get_active_org();
        
        //-----
        $data=$this->leagues_model->get_leagues_by_parentorg($org_id);
        $this->result->json_pag($data);
    }
	
    
    
    /**
	* use facilities ctr
	* @deprecated  
	*/
	public function json_get_venues()
	{
	    $facility_id=$this->input->get("facility_id");
	    $result=$this->leagues_model->json_get_venues($facility_id);
	    $this->result->json($result);
	}

    /**
	* use facilities controller
	* @deprecated  
	*/	
	public function json_get_venues_type_list()
	{
	    $result=$this->leagues_model->json_get_venues_type_list();
	    $this->result->json($result);
	}
    /**
	* use facilities  controller
	* @deprecated 
	*/
	public function json_getfacilities()
	{
	    $result=$this->leagues_model->json_getfacilities();
	    echo json_encode($result);
	}

	
	public function json_get_leagues_name_list()
	{
	    $result=$this->leagues_model->json_get_leagues_name_list();
	    $this->result->json($result);       
	}
    /**
	* use facilities model
	* @deprecated use teams controller
	*/
	public function json_get_team_underprocess()
	{
	    $result=$this->leagues_model->team_underprocess();
	    echo json_encode($result);           
	}
    /**
	* use teams controller
	* @deprecated  
	*/	
	public function json_approve_teams()
	{
	    //--------------------------------------------------
	    $creator = $this->permissions_model->get_active_user();
	    $owned_by= $this->permissions_model->get_active_org();
	    //--------------------------------------------------
	    $selected_team_ids=$this->input->post("selected_team_ids");
	    $this->leagues_model->approve_teams($selected_team_ids,$creator,$owned_by);
	} 

    /**
	* use teams controller
	* @deprecated  
	*/	
	public function post_new_team()
	{
	    $creator = $this->permissions_model->get_active_user();
	    $owner   = $this->permissions_model->get_active_org();
	    $t_name= rawurldecode($this->input->POST("team_name"));
	    $person_id=$this->input->POST("person_id");
	    
	    $league_org =$owner;//org id for the current league - since only leagues can create teams
	    
	    $team_id= $this->leagues_model->new_team($league_org,$t_name,$creator,$owner);
	    if(!$person_id||$person_id == ''||$person_id=='null'||$person_id==null)
	    {
			echo $team_id;//up to here works
			return;
	    }
	    
	    //otherwise, add assignment for this team manager
	    $user_results = $this->permissions_model->get_userid_by_person($person_id);
	    $user_id = $user_results[0]["user_id"];
		//type 4 is team manager, by lu_role
		//null is the end date
	    echo $this->permissions_model->insert_assignment($creator,$user_id,4,$team_id,null,$owner); 
	}

    
    
    
    
    
    
    /**
	* use teams controller
	* @deprecated  
	*/	
	public function post_new_team_anduser()
	{
		$creator = $this->permissions_model->get_active_user();
 		$owner   = $this->permissions_model->get_active_org();
 		$team_org =$owner;
		//similar to new_team_anduser
		$t_name= rawurldecode($this->input->POST("team_name"));
		//is actualy entity_org id for team
		$team_id= $this->leagues_model->new_team($team_org,$t_name,$creator,$owner);
		
		$fname   = $this->input->post('fname');

	    $lname   = $this->input->post('lname');
	   
	    $email   = rawurldecode($this->input->post('email'));
	    $street = rawurldecode($this->input->post('street'));
	    $login   = $this->input->post('login');
	    $pass    = rawurldecode($this->input->post('pass'));
	    $bdate   = rawurldecode($this->input->post('bdate'));
	    $gender  = $this->input->post('gender');
	    //$person_id=$this->input->post('person_id');
	  //  $default_org = $league_id;//$this->input->post('default_org');
	    
	    $postal_code = $this->input->post('postal');
	    $city = $this->input->post('city');
	    $region = $this->input->post('region');
	    $country = $this->input->post('country');
	    
	    $p_mobile = $this->input->post('p_mobile');
	    $p_work = $this->input->post('p_work');
	    $p_home = $this->input->post('p_home');
		
		$role_id =4;//lu_role for  team manager
 		echo $this->create_org_manager($creator,$fname,$lname,$gender,$bdate,$owner,
    		$street,$city,$region,$country,$postal_code,$email,$p_home,$p_work,$p_mobile,$login,$pass,$role_id,$team_id);
    }

    
    /**
	* use associations controller
	* 
	* actually, that assoc controller function should be moved here
	* @deprecated  
	*/	
	public function post_new_league()
	{
	    $creator = $this->permissions_model->get_active_user();
 		$owner   = $this->permissions_model->get_active_org();   
		//here we are just given the person_id of an existing user to attach to
		$subdomain=$this->input->POST("subdomain");//ex: concat_leaguename
	    $maindomain=$this->input->POST("maindomain");//ex:   nsalive.com

	    $leaguename=$this->input->POST("leaguename");
	    $person_id = $this->input->POST('person_id');
	    $assn_id = $owner;
	    $league_id = $this->create_league($creator,$owner,$leaguename,$subdomain,$maindomain,$assn_id);
	    
	    //now we just have to assign the role of league manager to this person and org in assignments
	    if($person_id)
	    {
	    $user_results = $this->permissions_model->get_userid_by_person($person_id);
	    $user_id = $user_results[0]["user_id"];
	    /*we do not need entity id for the person, but we could get it like this
	    $entity_result = $this->person_model->get_entity_by_person($person_id);
	    $entity_id = $entity_result[0]["entity_id"];  */
	    //role id == 3 is league manager, null is the expiry date
	    $l_org=$this->leagues_model->get_org_from_league($league_id);
	    echo $this->permissions_model->insert_assignment($creator,$user_id,3,$league_id,null,$owner);
		}
	}//$creator,$user_id,$role_id,$org_id,$end_date,$owner)
	  //_creator int4, _user_id int4, _role_id int4, _org_id int4, _end_date timestamp, _owned_by int4

	  
    /**
	* @deprecated  
	*/		
	public function post_new_league_anduser()
	{
	    $creator = $this->permissions_model->get_active_user();
 		$owner   = $this->permissions_model->get_active_org();    	
	    //here we also get the info to create a new user
	    $subdomain=$this->input->POST("subdomain");
	    $maindomain=$this->input->POST("maindomain");
	    $leaguename=$this->input->POST("leaguename");
	    
	    
	    $assn_id = $owner;
	    //$assn_id=$this->input->POST("assn_id");
	    
	    
	    //make league and entity records, etc
	    $league_id = $this->create_league($creator,$owner,$leaguename,$subdomain,$maindomain,$assn_id);
	    
	    
	    //copied most of the following from permissions controller
	    //$person_id = $this->input->POST('person_id');
	    $fname   = $this->input->post('fname');
	    $lname   = $this->input->post('lname');
	   
	    $email   = rawurldecode($this->input->post('email'));
	    $street = rawurldecode($this->input->post('street'));
	    $login   = $this->input->post('login');
	    $pass    = rawurldecode($this->input->post('pass'));
	    $bdate   = rawurldecode($this->input->post('bdate'));
	    $gender  = $this->input->post('gender');
	    //$person_id=$this->input->post('person_id');
	  //  $default_org = $league_id;//$this->input->post('default_org');
	    
	    $postal_code = $this->input->post('postal');
	    $city = $this->input->post('city');
	    $region = $this->input->post('region');
	    $country = $this->input->post('country');
	    
	    $p_mobile = $this->input->post('p_mobile');
	    $p_work = $this->input->post('p_work');
	    $p_home = $this->input->post('p_home');

 		$role_id =3;//lu_role == league exec
 		echo $this->create_org_manager($creator,$fname,$lname,$gender,$bdate,$owner,$street,
    		$city,$region,$country,$postal_code,$email,$p_home,$p_work,$p_mobile,$login,$pass,$role_id,$league_id);
	}

	private function create_org_manager($creator,$fname,$lname,$gender,$bdate,$owner,$street,
    		$city,$region,$country,$postal_code,$email,$p_home,$p_work,$p_mobile,$login,$pass,$role_id,$org_id)
	{
		 		//now we have to create a new user/person, and obtain person id and/or user id
 		if($city == ""  || $city == "null")   $city = null;
	    if($bdate == "" || $bdate == "null")  $bdate = null;
	    if($street == ""||$street == "null")   $street = null;    
	    if($email == "" ||$email == "null")   $email = null; 
	    if($p_home == ""||$p_home == "null")   $p_home = null;  
	    if($p_work == ""||$p_work == "null")   $p_work = null;  
	    if($p_mobile == ""||$p_mobile == "null")   $p_mobile = null;   
	    if($postal_code == "" || $postal_code == "null") $postal_code = null;
	    if($region == "" || $region == "null" || $region=="none") $region = null;
	    if($country == "" || $country == "null" || $country=="none") $country = null;
	    $gender=$this->input->post('gender');
	    if($gender == 'none' || $gender == '' || $gender == " " || $gender == "null")  $gender = null;
	        
	    if($creator == null || $creator == '' || $creator <=0)  return "not logged in";//
	    
	   // if($default_org == 'new' || $default_org == null || $default_org == '' || $default_org == "null")
	   //     $default_org = null;
	    //if($person_id == 'new' || $person_id == null || $person_id == '' || $person_id == "null")
	        $person_id = -1;
	     
	        //insert OR UPDATE person record, which returns the id here
	        //if person_id was given, it will update and return the same, other wise it returns new
	    $person_id = $this->person_model->insert_person($creator,$person_id,$fname,$lname,$gender,$bdate,$owner);        
	    
	    if($person_id <= 0) return $person_id;//if error
	    //so we have created a new person but we still need its entity id for contact and address
	    $entity_result = $this->person_model->get_entity_by_person($person_id);
	    
	    //it is an array;
	    $entity_id = $entity_result[0]["entity_id"];
	    //create or get postal id
	    $postal_id=null;
	    if($postal_code != null)
	        $postal_id = $this->permissions_model->get_postal_id($postal_code);
	    
	    if($street != null || $city != null || $country != null || $region != null || $postal_id!= null)
	    $address_id = $this->permissions_model->insert_entity_address($creator,$entity_id,
	    $street,$city,$region,$country,$postal_id,2,$owner);//type == 2 for home address, from lu_address_type
	    //1 would be shipping, etc
	    //the following are from lu_contact_type, for home, work, cellphone, email
	    if($email != null)
	         $this->permissions_model->insert_entity_contact($creator,$entity_id,1,$email,$owner);
	     if($p_home != null)
	         $this->permissions_model->insert_entity_contact($creator,$entity_id,2,$p_home,$owner);
	     if($p_work != null)
	         $this->permissions_model->insert_entity_contact($creator,$entity_id,3,$p_work,$owner);
	     if($p_mobile != null)
	         $this->permissions_model->insert_entity_contact($creator,$entity_id,4,$p_mobile,$owner);   

	   if($pass == null || $pass == "" || $pass == "undefined" || $pass == "null" || $pass == -1)
	       $pass = "-1";
	   else
	       $pass = md5($pass);

	       //passing league_id as the default org
	    $user_id =  $this->permissions_model->insert_user($creator,$person_id,$org_id,$login,$pass,$owner); 
 		
 		//now we just have to assign the role of league manager to this person and org in assignments
	    
	    //role_id == 3 is league manager, null is the expiry date
	    return $this->permissions_model->insert_assignment($creator,$user_id,$role_id,$org_id,null,$owner);
		
	}
    /**
	*  
	* @deprecated  
	*/	
	private function create_league($creator,$owner,$leaguename,$subdomain,$maindomain,$assn_id)
	{//assn_id is a entity_id, not entity_org_id
	    $league_id = $this->associations_model->insert_league($creator,$owner,$leaguename,$subdomain.".".$maindomain,'global.playerspectrum.com',$assn_id);

	   // echo $this->associations_model->create_park('global',$subdomain.".".$maindomain);
	   echo $subdomain.".".$maindomain;
	   echo $this->associations_model->domain_park($subdomain.".".$maindomain);
	    return $league_id ; 
	}
   
   	public function Email($from,$to,$cc,$bcc,$subject,$message)
	{
	    $this->load->library('email');
	    $this->email->from($from);
	    $this->email->to($to);
	    $this->email->cc($cc);
	    $this->email->bcc($bcc);
	    $this->email->subject($subject);
	    $this->email->message($message);
	    $this->email->send();
	}
   
   
   
   
   public function post_update_league_address()
	{
		$user = $this->permissions_model->get_active_user();	
 		$org   = $this->permissions_model->get_active_org();  
 		//post input
		$league_id = (int)$this->input->post('league_id');
		$l_org=$this->leagues_model->get_org_from_league($league_id);
		$entity_id=$this->leagues_model->get_entity_id_from_org($l_org);
		
		$country   =      $this->input->post('country');
		$region    =      $this->input->post('region');
		$address   = rawurldecode($this->input->post('address'));
		$city      = rawurldecode($this->input->post('city'));
		$postal    = rawurldecode($this->input->post('postal'));
		
		//insert // get ids of values
		if(!$postal) 
			$postal_id=null;
		else
			$postal_id=$this->leagues_model->get_postal_id($postal);
        $type_id=2;//lu_address_type table states 2==Home, 1==shipping
        if(!$region) 
			$region_id=null;
		else
        	$region_id = $this->leagues_model->get_region_id($region);
        if(!$country) 
			$country_id=null;
		else
        $country_id = $this->leagues_model->get_country_id($country);
        	
		echo $this->leagues_model->insert_entity_address($user,$entity_id,$address,$city,$region_id,$country_id,$postal_id,$type_id,$org);

	}
	public function json_league_address()
	{
		//input
		$league_id = (int)$this->input->post('league_id'); 
		//get ids
		$l_org=$this->leagues_model->get_org_from_league($league_id);
		$entity_id = (int)$this->leagues_model->get_entity_id_from_org($l_org);
		
 		echo json_encode($this->leagues_model->get_entity_address($entity_id));
		
		
	}
    /**
    * DEPRECIATED no users querys shouoldb e in leagues controllers
    * thisfunction has nothing to do with leagues at all. 
    * instead use  permissions/json_org_users
    * or similar functions in person or permissions controller
    * 
    */
   public function json_get_users()
   {
        $data=$this->leagues_model->get_users();
        $this->result->json_pag($data);
   }
   
   
   /**
   * @depreciated
   * use association/json_crweatenewleague
   * 
   */
   public function post_update_league()
    {
        $league_id      =$this->input->get_post("league_id");
        $leaguename     =$this->input->get_post("league_name");
        $websiteprefix  =$this->input->get_post("websiteprefix");
        $domainname     =$this->input->get_post("domainname");

        $result= $this->associations_model->update_league($league_id,$leaguename,$websiteprefix,$domainname);   
        
        if($_FILES["file_upload"]["name"]!='')
        {
        	$date=date("Y-m-d-G-i-s");
			$this->upload($_FILES["file_upload"],$date,$extraTag=$result,"uploaded/org-assets/logo");
        }
            
        echo json_encode(array("success"=>true,"result"=>$result));
    }
   
   public function post_send_welcome_email()
    {  
        //$a_o_id = $this->permissions_model->get_active_org();
        //$a_u_id = $this->permissions_model->get_active_user();
        //-------------------------------------------------------
        $league_id=$this->input->get_post("league_id");
        $league=$this->leagues_model->get_league_info($league_id);
		$league=$league[0];
        $league_org =$this->leagues_model->get_org_from_league($league_id);
        $role_league_executive=3;
        $users=$this->permissions_model->get_org_users($league_org,false,$role_league_executive);
        
        if(!count($users))
        {
			echo "No managers found.";
	        return;
        }
        
        //res=$this->leagues_model->get_managers_for_email($league_id,$a_u_id,$a_o_id);
       
        $this->load->library('email');
        $this->load->library('encrypt');
        $sent_to=array();
        $email_type=1;
        foreach($users as $user)
        {
        	$e_id = $user['entity_id'];
        	$email=$this->entity_model->get_entity_contact($e_id,$email_type);
        	if(!count($email)) continue;
        	
        	$user['email'] = $email[0]['value'];
        	
			$user["pass"]=$this->encrypt->decode($user["password"] ,SSI_ENC_KEY);
			
			$path_atch_file = "assets/docs/SpectrumFirstTimeAccessLogic.docx";
			//if(@fopen($path_atch_file))
			//{
				
		//	}
			//else echo "file not found".$path_atch_file;//TODO: remove this echo after testing
			
			$subj = "Welcome to Spectrum";
			$email_data = array_merge($league,$user);
			$email_data['subject']=$subj;
			$to=$user['email'];
			
			$this->email->attach($path_atch_file);
			$email_data['has_attached']=true;//flag to tell the view to display the 'see attached file' message
			
			$message = $this->load->view("emails/league.welcome.php",$email_data,true);
			$this->email->to($to);
	        $this->email->subject($subj);
	        $this->email->letterhead($message,$email_data,true);
	        $this->email->send();
			$sent_to[]=$to;
        }

        
        if(!count($sent_to)) 
        {
			echo "No email addresses found among assigned managers";			
			return;
        }
        echo "Emailing complete : ";//.$to."' .";
        
        echo implode(", ",$sent_to);
        
    }
    
    //Manage Leagues
    public function window_manage_leagues()
    {                                                                                                            
        $this->load->library('window');
	    //$this->window->set_css_path("/assets/css/inventory/");
	    
        
        $this->window->set_js_path("/assets/js/models/");          
        $this->window->add_js('users.js');   
        $this->window->add_js('user_role.js');   
        $this->window->add_js('role.js');   
           
        $this->window->set_js_path("/assets/js/components/leagues/");
        //main for league
        $this->window->add_js('forms/create_edit.js'  );     
        $this->window->add_js('windows/create_edit.js');  
        
        $this->window->add_js('grids/leagues.js');
        //$this->window->add_js('../grids/spectrumgrids.address.js');
                
        //org users
        $this->window->add_js('../users/forms/user_person.js');     
        $this->window->add_js('../users/forms/org_roles.js');     
        $this->window->add_js('../users/windows/users.js');     
        $this->window->add_js('../users/windows/roles.js');     
        $this->window->add_js('../users/grids/org_roles.js'); 
        $this->window->add_js('../users/grids/org_users.js'); 
        //$this->window->add_js('../permissions/form.org_roles.js'); 
        //$this->window->add_js('../teams/grid.users.js');    
               
        //$this->window->add_js('leagues.users.js');      
        
        //tab controller
        $this->window->add_js('controller.js');           
        $this->window->set_header('Manage Leagues');
        $this->window->set_body($this->load->view('leagues/manage.php',null,true));
        $this->window->json();
    }

}
