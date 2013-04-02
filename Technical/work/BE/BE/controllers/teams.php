<?php
 require_once('endeavor.php');   
  class Teams extends Endeavor
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
  	  * @var Permissions_model
  	  */
  	  public $permissions_model;
  	  
  	  /**
  	  * 
  	  * 
  	  * @var season_model
  	  */
  	  public $season_model;
  	  /**
  	  * 
  	  * 
  	  * @var leagues_model
  	  */
  	  public $leagues_model;
  	  /**
  	  * 
  	  * 
  	  * @var games_model
  	  */
  	  public $games_model;
  	  /**
  	  * 
  	  * 
  	  * @var person_model
  	  */
  	  public $person_model;
    function __construct()
    {
        parent::Controller();
        $this->load->model('endeavor_model');
        $this->load->model('person_model');
        $this->load->model('season_model');
        $this->load->model('teams_model');
        $this->load->model('leagues_model');
        $this->load->model('permissions_model');
        $this->load->model('games_model');

        $this->load->library('page');
        $this->load->library('input');   
		$this->load->library('result');
    }
    private function load_window()
    {
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/inventory/");
        $this->window->set_js_path("/assets/js2/");   
    }
    public function json_get_active_org_and_type()
    {                                                               
        $result1=$this->teams_model->get_team_id();
        $result2=$this->permissions_model->get_active_org_and_type();
        
        //Combination of team_id , org_id , org_type_id
        if(count($result1)!=0) $team_id=$result1[0]["team_id"];
        else $team_id=0;
        
        $this->result->json(array_merge(array("team_id"=>$team_id),$result2));
        
    }                                                         
    //team-level
    public function window_managerosters()
    {   
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/");

        $this->window->set_js_path("/assets/js/models/"); 
        
        $this->window->add_js('users.js');   
        $this->window->add_js('user_role.js');   
        $this->window->add_js('role.js');   
               
        //need forms for persons
         $this->window->set_js_path("/assets/js/components/users/"); 
        
        $this->window->add_js('forms/user_person.js'); 
        $this->window->add_js('windows/users.js'); 

        //rosters grid and controller
        $this->window->set_js_path("/assets/js/components/rosters/"); 
        
        $this->window->add_js('grids/rosters.js');     
        $this->window->add_js('controller.js');
                                                                        
        $this->window->set_header('Roster');//window is only viewable by a team anyway
        $this->window->set_body($this->load->view('teams/manage/roster.php',null,true));
        
        $this->window->json();
    }
    
    //League-level
    public function window_manage()
    {                                                                                                          
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/");
        
        //data models
        $this->window->set_js_path("/assets/js/models/"); 
        
        $this->window->add_js('team.js');   
        $this->window->add_js('division.js');   
        $this->window->add_js('users.js');   
        $this->window->add_js('user_role.js');   
        $this->window->add_js('role.js');   
        
        //team components
       
        //files for org users from user componnet
        $this->window->set_js_path("/assets/js/components/users/"); 
       
        $this->window->add_js('forms/user_person.js');     
        $this->window->add_js('forms/org_roles.js');     
        $this->window->add_js('windows/users.js');     
        $this->window->add_js('windows/roles.js');     
        $this->window->add_js('grids/org_roles.js'); 
        $this->window->add_js('grids/org_users.js'); 
        
        //rosters files:
        
		$this->window->set_js_path("/assets/js/components/rosters/"); 
        
        $this->window->add_js('grids/rosters.js'); 
        
        //files for team
        $this->window->set_js_path("/assets/js/components/teams/"); 
        
        $this->window->add_js('forms/team_managers.js');   
        $this->window->add_js('windows/team_managers.js');   
        $this->window->add_js('forms/create_team.js');   
        $this->window->add_js('forms/edit_team.js');   
        $this->window->add_js('windows/create_team.js');   
        $this->window->add_js('windows/edit_team.js');   
        $this->window->add_js('forms/edit_division.js');     
        $this->window->add_js('windows/edit_division.js');     
        $this->window->add_js('forms/edit_season.js');     
        $this->window->add_js('windows/edit_season.js');   
        
        
		//grids

        $this->window->add_js('grids/teams.js');     
        
        //main class controller class for this component 
        $this->window->add_js('controller.js'); 
        
        $this->window->set_header('Teams');
        $this->window->set_body($this->load->view('teams/manage/main.php',null,true));
		$this->window->json();
    }
    public function json_get_customfields()
    {                                                                                                          
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/");
        
        $this->window->set_js_path("/assets/js/components/teams/"); 
        
        //Grids
        $this->window->add_js('grids/spectrumgrids.customfields.js');     
        //Forms
        $this->window->add_js('forms/forms.js'); 
        //windows
        $this->window->add_js('windows/spectrumwindow.teams2.js'); 
            
        
        $this->window->add_js('controller2.js'); 
        $this->window->add_js('toolbar.js'); 
        
        $this->window->set_header('Manage Custom Fields');
        $this->window->set_body($this->load->view('teams/manage/main.php',null,true));
        $this->window->json();
    }
    public function json_get_customfields_by_season()
    {
        $season_id=$this->input->get_post("season_id");
        
        $result=$this->teams_model->get_customfields_by_season($season_id) ;
        $this->result->json($result);
    }
    public function json_delete_customfield()
    {
        $season_id=$this->input->get_post("season_id");
        $field_id=$this->input->get_post("field_id");
        
        $result=$this->teams_model->delete_customfield($season_id,$field_id) ;
        $this->result->success(   $result[0]["delete_customfield"]);        
    }
    public function json_add_customfield()
    {
        $season_id                  =$this->input->get_post("season_id");
        $field_title                =$this->input->get_post("field_title");
        $slave_org_type_id          =$this->input->get_post("slave_org_type_id");
        $a_e_id                     = $this->permissions_model->get_active_entity();
        
        $result=$this->teams_model->add_customfield($a_e_id,$season_id,$field_title,$slave_org_type_id);
        $this->result->success(  $result[0]["add_customfield"]);        
    }
    public function json_update_custom_field_title()
    {
        $customFieldId      =$this->input->get_post("field_id");
        $customFieldTitle   =$this->input->get_post("field_title");
        
        $result=$this->teams_model->update_custom_field_title($customFieldId,$customFieldTitle);
        $this->result->success(  $result[0]["update_custom_field_title"]) ;            
    }
    public function json_get_rosters($team_id,$season_id,$active)
    {                                 
        $a_user= $this->permissions_model->get_active_user();
        $a_org = $this->permissions_model->get_active_org();
        //--------------------------------------------------
        $this->result->json($this->teams_model->get_rosters($team_id,$active,$season_id,$a_user,$a_org));
    }
    /**
    * Sam
    * 
    */
    public function json_get_roster_persons()
    {                                 
    	//get only a single roster based on team and season id
        $team_id    =(int)$this->input->get_post('team_id');
        $season_id  =(int)$this->input->get_post('season_id');
         
        
        $result=$this->teams_model->get_roster_persons($team_id,$season_id);
        
        $plain='Y/m/d';
        foreach($result as $i=>&$r)
        {
            $result[$i]['person_birthdate_display']=($r['person_birthdate']!='')?date($plain,strtotime($r['person_birthdate'])):'';
            $result[$i]['effective_range_start_display']=($r['effective_range_start']!='')?date($plain,strtotime($r['effective_range_start'])):'';
            $g_icon='-';
		    if($result[$i]['person_gender']=='M' || $result[$i]['person_gender']=='m')
                $g_icon='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/male.png />';
		    if($result[$i]['person_gender']=='F' || $result[$i]['person_gender']=='f')
                $g_icon='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/female.png />';
            $result[$i]['person_gender_icon']=$g_icon;
            if($r['status_id']==1)$status_icon='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/error.png />';
            else if($r['status_id']==2)$status_icon='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/tick.png  />';
            else if($r['status_id']==3)$status_icon='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/cross.png />';
            else $status_icon='';
            $result[$i]['status_icon']=$status_icon;
        }	 
        $this->result->json_pag($result);
    }
    
    
    public function html_team_season_roster()
    {
        $team_id    =(int)$this->input->get_post('team_id');
        $season_id  =(int)$this->input->get_post('season_id');
         $team_name='';
        $result=$this->teams_model->get_roster_persons($team_id,$season_id);
        if(count($result))
        {
			$team_name = $result[0]['team_name'];
        }
        
        echo "<h2>Spectrum Roster For: $team_name</h2>";
        $html="<table border='1' padding='1' width='100%'>";
        foreach($result as $r)
        {
	        $html.="<tr>";
	        $html.="<td>".$r['person_lname']."</td>";
	        $html.="<td>".$r['person_fname']."</td>";
	        $html.="<td>".$r['person_gender']."</td>";
	        $html.="<td>".$r['status_name']."</td>"; 
	     	   
	        
	        
			
			$html.="</tr>";
		}
		$html.="</table>";
		echo $html;
    }
    
    public function json_accept_roster_person()
    {
        $roster_person_id=(int)$this->input->get_post("roster_person_id");
        
        $result=$this->teams_model->accept_roster_person($roster_person_id);
        //For emailing
        $team_org_id=(int)$this->input->get_post('team_org_id');
        if(!$team_org_id) $team_org_id=$this->permissions_model->get_active_org();
        
        $rp_fname       =rawurldecode($this->input->get_post("person_fname"));
        $rp_lname       =rawurldecode($this->input->get_post("person_lname"));
        //
        /*if accept then do not email
        //Email to team managers
        $managers=$this->teams_model->_get_team_managers($team_org_id);
        foreach($managers as $i=>$v)
        {     
            $this->Email("no-reply@servillian.com",array(substr(1,strlen($v["email"])-1,$v["email"])),'','','team roster individual accepted'
            ,$rp_fname.' '.$rp_lname.' for designated team in this season Accepted .','test'); 
        } 
        */
        $this->result->success(  $result);
    }    
    public function json_decline_roster_person()
    {
        $roster_person_id   =(int)$this->input->get_post("roster_person_id");//SB: took away access to $ _REQUEST directly 
        //to make it safer vs sql injection and other things
        $comment            =rawurldecode($this->input->get_post('comment'));//always rawurldecode, from escape on js end
        if(!$comment) $comment="No Reason Given";
        $result=$this->teams_model->decline_roster_person($roster_person_id,$comment);
        //For emailing
        $team_org_id = (int) $this->input->get_post('team_org_id');
        if(!$team_org_id) $team_org_id=$this->permissions_model->get_active_org();
        
        $rp_fname       =rawurldecode($this->input->get_post("person_fname"));
        $rp_lname       =rawurldecode($this->input->get_post("person_lname"));
        //
        //Email to team managers
        $managers=$this->teams_model->_get_team_managers($team_org_id);
        /*//disabled for Sept 1st, since we have no fancy view for it
        foreach($managers as $i=>$v)
        {     
        	if($v["email"])
	            $this->Email("no-reply@servillian.com",array(substr(1,strlen($v["email"])-1,$v["email"])),'','','team roster individual Declineed'
	            ,$rp_fname.' '.$rp_lname.' for designated team in this season Declined .','test'); 
        } 
        */
        $this->result->success($result);
    }
    public function json_season_teams()
	{
		$season=(int)$this->input->get_post('season_id');
		
		$name   = $this->input->get_post('query');//from grid searchbar
		
		if(!$name) $name=null;
		
		
		if( $season && $season>0)		
		{
			$teams_asn = $this->teams_model->get_season_teams($season);
			 
			$teams_un = $this->teams_model->get_unassigned_teams_by_season($season);
			$teams=array_merge($teams_asn,$teams_un);
		}
		else
		{
			//season is -1
			$org=$this->permissions_model->get_active_org();

			$league_id=$this->leagues_model->get_league_from_org($org);
			$teams = $this->teams_model->get_unassigned_teams($league_id,$name);
			
		}	
		//in either case:
		//arbitrarily list one of the team managers for each team, if one exists
		foreach($teams as &$team)
		{
			$team['manager_name'] ="";
			$team['manager_phone']="";
			$managers = $this->teams_model->get_team_managers($team['team_id']);
			if(count($managers))//if one was found
			{
				$mg=$managers[0];//just take the first
				$team['manager_name'] =$mg['person_fname']." ".$mg['person_lname'];
				$team['manager_email'] =$mg['email'];
				$team['manager_phones']='';
				if($mg['p_home']) 
					{$team['manager_phones'].="H: ".$mg['p_home']." ";  }
				if($mg['p_mobile']) 
					{$team['manager_phones'].="C: ".$mg['p_mobile'];}
				
				//$team['manager_phones']=.", C: ".$mg['p_mobile'];//
				$team['manager_user_id']  =$mg['user_id'];
				$team['manager_person_id']=$mg['person_id'];
			}
			if(!isset($team['division_name']) )
			{
				$team['division_name']='-Unassigned-';
			} 
			if(!isset($team['parent_division_id'] ) || $team['parent_division_id']==null )
			{
				$team['parent_division_id']=null;
				$team['parent_division_name']=$team['division_name'];//theres only one level of division specified
			}
			
		}
		//$this->result->json($teams);
		 $this->result->json_pag($teams);
	}
	
    
    public function post_delete_roster_person()
    {
    	$rp_id=(int)$this->input->post('roster_person_id');
		$user=$this->permissions_model->get_active_user();
		echo $this->teams_model->delete_roster_person($rp_id,$user);
    }
    
    public function post_assign_roster_person()
    {
		
        $team_id  =(int)$this->input->post('team_id');
        $season_id=(int)$this->input->post('season_id');
        $person_id=(int)$this->input->post('person_id');
        
        $user= $this->permissions_model->get_active_user();
        $org = $this->permissions_model->get_active_org();
        
        $active_type=$this->permissions_model->get_active_org_type();
        //magic numbers from public.lu_ tables
        $is_league=3;
        $is_team=6;
        $is_pending=1;
        $is_approved=2;
        $is_declined=3;
        
        $bo_soft=1;
        $bo_hard=2;
        
		$status=$is_approved;//assume this unless a blackout says otherwise
        if($active_type == $is_team)
        {
			//if a team is making the change we must check blackout dates
			$today=strtotime('now');
			$bo = $this->leagues_model->get_blackouts($season_id);
			foreach($bo as $blackout)//bo_start_date,bo_end_date,bo_type_id
			{
				if($status == $is_declined) {continue;}//if we set it to declined (from hard) already, STOP NOW
				$bo_start = strtotime($bo['start_date']);
				$bo_end   = strtotime($bo['end_date'  ]);
				$bo_type= $bo['bo_type_id'];
				//if we found a problem, stop now
				if($bo_start < $today && $today < $bo_end)
				{
					if($bo_type == $bo_soft)
					{
						//if its soft, set status but keep looking, we may find a hard one that overlaps
						$status=$is_pending;
					}
					if($bo_type == $bo_hard)
					{
						//if its hard, stop now dont look for any more
						$status=$is_declined;
						break;
					}
				}
			}
        }

		echo $this->teams_model->assign_roster_person($team_id,$season_id,$person_id,$user,$org,$status);
    }
    public function json_get_roster_persons______($roster_id)
    {
        $a_user= $this->permissions_model->get_active_user();
        $a_org= $this->permissions_model->get_active_org();
        
        // get roster
        $roster = $this->teams_model->get_roster_persons($roster_id,$a_user,$a_org);
        
        //var_dump($roster);
        
        //format fields
        foreach($roster as $k=>$person)
        {
			//add gender image
			$gender = ($person['gender']=="M")?"male":"female";
			$person['person_gender_image'] = "<img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/$gender.png'/>";
			
			//format added/removed date
			$person['effective_range_start'] = date("M d/y h:iA",strtotime($person['effective_range_start']));
			$person['effective_range_end'] = ($person['effective_range_end']) ? date("M d/y h:iA",strtotime($person['effective_range_end'])) : "";
			
			//set age
			$person['person_age'] = floor((time() - strtotime($person['person_birthdate']))/31556926);
			
			$roster[$k] = $person;
        }
        
        $this->result->json($roster);
    }
    
    public function json_get_person($person_id)
    {                                         
        $this->result->json($this->teams_model->get_person($person_id));    
    }
    /*
    public function json_new_rosterperson()
    {
        $a_user= $this->permissions_model->get_active_user();
        $a_org= $this->permissions_model->get_active_org();
        //--------------------------------------------------
        $roster_id  =$_POST["roster_id"];
        $fname      =$_POST["fname"];
        $lname      =$_POST["lname"];
        $bdate      =$_POST["bdate"];
        $gender     =$_POST["gender"];
        $homef      =$_POST["homef"];
        $workf      =$_POST["workf"];
        $cellf      =$_POST["cellf"];
        $email      =$_POST["email"];
        $address    =$_POST["addr"];
        $city       =$_POST["city"];
        $province   =$_POST["province"];
        $country    =$_POST["country"];
        $postal     =$_POST["postal"];
        
        $res=$this->teams_model->new_rosterperson($roster_id,$fname,$lname,$bdate,$gender,$homef,$workf,$cellf,$email,$address,$city,$province,$country,$postal,$a_user,$a_org);
        $this->result->json($res);
    } 

    public function json_update_rosterperson()
    {
        $a_user= $this->permissions_model->get_active_user();
        $a_org= $this->permissions_model->get_active_org();
        //--------------------------------------------------
        $roster_id  =$_POST["roster_id"];
        $person_id  =$_POST["person_id"];
        $fname      =$_POST["fname"];
        $lname      =$_POST["lname"];
        $bdate      =$_POST["bdate"];
        $gender     =$_POST["gender"];
        $homef      =$_POST["homef"];
        $workf      =$_POST["workf"];
        $cellf      =$_POST["cellf"];
        $email      =$_POST["email"];
        $address    =$_POST["addr"];
        $city       =$_POST["city"];
        $province   =$_POST["province"];
        $country    =$_POST["country"];
        $postal     =$_POST["postal"];
        
        $res=$this->teams_model->update_rosterperson($roster_id,$person_id,$fname,$lname,$bdate,$gender,$homef,$workf,$cellf,$email,$address,$city,$province,$country,$postal,$a_user,$a_org);
        $this->result->json($res);    

    }   */
           
    /**
    * get exceptions for the given team id
    * 
    */
    public function json_team_exceptions()
    {
		$team_id =(int) $this->input->post('team_id');
		
		$this->result->json($this->teams_model->get_team_exceptions($team_id) );
		
    }




    public function json_team_managers_andcontact()
    {
		$team_id =(int) $this->input->get_post('team_id');
		 //echo $team_id;
		$org_id=(int)$this->teams_model->org_id_from_team($team_id);
		//echo $org_id;
		$hide_sa=true;
		$team_manager_role_id = 4;//permissions.lu_role 
		
		$r=$this->permissions_model->get_org_users($org_id,$hide_sa,$team_manager_role_id);
		$r=$this->person_model->_format_people_extgrid($r); 
		
        $phonetypes=array('mobile','home','work');
        
		foreach($r as &$user)
		{
			$user['full_name'] = $user['person_fname']." ".$user['person_lname'];
					 
			foreach($phonetypes as $type)
			{
				
			
				$user[$type.'_display']="";
				if($user[$type.'-ac'] && $user[$type.'-pre'] && $user[$type.'-num'])
				$user[$type.'_display'] = $user[$type.'-ac'] ."-".$user[$type.'-pre'] ."-". $user[$type.'-num'];
			 
			}
		}
		
		
		$this->result->json($r);
		
		
		
    }    
    /**
    * create one new team date exception
    * 
    */
    public function post_team_exception()
    {
		$user    	= (int)$this->permissions_model->get_active_user();
		$owner   	= (int)$this->permissions_model->get_active_org();
		$start_date = rawurldecode($this->input->post('start_date'));
		$end_date	= rawurldecode($this->input->post('end_date'));
		$start_time = rawurldecode($this->input->post('start_time'));
		$end_time	= rawurldecode($this->input->post('end_time'));
		$team_id = (int)$this->input->post('team_id');
		$desc 	 = rawurldecode($this->input->post('desc'));
		//var_dump($desc);
		if(!$desc || $desc=='' || $desc=='null') $desc=null;
		//var_dump($start_date);
		$start = $start_date." ".$start_time;
		$end   = $end_date  ." ".$end_time;
		
		
		if(strtotime($start) > strtotime($end))
		{
			//echo "start is later than end date, swap noww\n";
			$swap = $start;
			$start= $end;
			$end  = $swap;			
		}
		
		
		echo $this->teams_model->insert_team_exception($team_id,$desc,$start,$end,$user,$owner);
	}
	
    /**
    * IS THIS DEPRECIATED..>?
    * save an update or create new  team exception, based on post varaible 'data'
    * which is assumed to have a specific format
    */
    public function post_team_exceptions()
    {
		$data =  json_decode (rawurldecode($this->input->post('data')));
		$user    = $this->permissions_model->get_active_user();
		$owner   = $this->permissions_model->get_active_org();
		
		$results= array();
		
		foreach($data as $row)
		{
			//skip any empty rows
			if(!property_exists($row,'start_date')  && !property_exists($row,'end_date')) continue;
			
			$team_id = $row->team_id;
			
			$start   = $row->start_date ." ".$row->start_time . " ". $row->start_ampm;
			$end     = $row->end_date." ".$row->end_time . " ". $row->end_ampm;
			$desc    = $row->desc;
			//$date    = 
			
			if( $row->team_ex_id  == -1)	//new row		
				$results[]= $this->teams_model->insert_team_exception($team_id,$desc,$start,$end,$user,$owner);
			else //update existing row
				$results[] = $this->teams_model->update_team_exception($row->team_ex_id,$desc,$start,$end,$user);
		}
		$this->result->json($results);
    }
    
    /**
    * delete existing team exception, based on ex id 
    * NOT team id
    * 
    */
    public function post_delete_team_exception()
    {
    	$user    = $this->permissions_model->get_active_user();
		$team_ex_id	 = rawurldecode($this->input->post('team_ex_id')); 
		
		$result= $this->teams_model->delete_team_exception($team_ex_id,$user);
		
		echo $result;
		
    }
    
    
    
    public function post_delete_multiple()
    {
		$team_id_array= json_decode($this->input->post('team_id_array'));
		$returns=array();
		foreach($team_id_array as $t_id)
		{
		
			$returns[]=$this->delete_team_from_javascript($t_id);	
		}
		$this->result->json($returns);
    }
    
    
    
    
    public function post_delete()
    {
		$team_id= (int)$this->input->post('team_id');
		echo $this->delete_team_from_javascript($team_id);
		
    }
    /**
    * user told the system to delete this team.  may have been done within a batch of teams, or on its own
    * 
    * 
    * @param mixed $team_id
    * @return error messsage, or sql return code integer
    */
    private function delete_team_from_javascript($team_id)
    {
	    $user    = $this->permissions_model->get_active_user();
	//	$games = $this->games_model->count_games_by_team($team_id);
	//	$count = $games[0]['count'];
		//if($count == 0)
			//{
		return $this->teams_model->delete_team($team_id,$user);
			//}
		//else
			//{return "This team has  ".$count. " games saved in our system,
			// we cannot delete the team unless the games are deleted first.";}
    }
    public function post_create()
	{
	    $user = $this->permissions_model->get_active_user();
	    $owner   = $this->permissions_model->get_active_org();
	    $team_name= rawurldecode($this->input->post("team_name"));
	    $team_id  = (int)$this->input->post("team_id");
	    $season_id  = (int)$this->input->post("season_id");
	    //$s  = (int)$this->input->POST("season_id");
		$league_org=$owner;//league id thing
	    if(!$team_id || $team_id==-1)
	    {//create team
		    $team_id= $this->teams_model->new_team($league_org,$team_name,$user,$owner);
		    
	    }
	    else
	    {//team exists so update team name
			$this->teams_model->update_team_name($team_id,$user,$team_name);
	    }
	    
	    if($team_id&&$season_id && $season_id!=-1)
	    {
	    	
			$this->teams_model->update_team_season_assignment($team_id,$season_id,$user,$owner);    
		}

		echo $team_id;
	}
	
	
	public function json_activeorg_team()
	{
		$org=$this->permissions_model->get_active_org();
		$ao_team_id=$this->teams_model->get_team_id_byorg($org);//usees get_active_org internally
		$team      =$this->teams_model->get_team_details($ao_team_id);
		
		
		$this->result->json($team);
	}

    public function post_team_name()
    {
    	$user    = $this->permissions_model->get_active_user();
		$team_id = (int)$this->input->get_post('team_id');
		$new_name	 = rawurldecode($this->input->get_post('team_name')); 
		$res = $this->teams_model->update_team_name($team_id,$user,$new_name);
		$this->result->success($res);
    }
    /**
    * remove all teams from all seasons
    * 
    */
    public function post_remove_teams_season()
    {
		$team_ids   = json_decode($this->input->post('team_ids'));
		$season_id = (int)$this->input->post('season_id');
		if(!$season_id||$season_id<0)
		{
			 $this->result->json(array(-1));
			return;
		}
    	$user    = (int)$this->permissions_model->get_active_user();
		$suc=array();
		foreach($team_ids as $team_id)
		{
			$suc[]= $this->teams_model->delete_team_season_assignment($team_id,$season_id,$user);
		}
		 $this->result->json($suc);
    }
    /**
    * assign all teams to all seasons
    * 
    */
    public function post_assign_team_to_seasons()
    {
		$team_ids   = json_decode($this->input->post('team_ids'));
		$season_ids = json_decode($this->input->post('season_ids'));
		
    	$user    = $this->permissions_model->get_active_user();
	    $owner   = $this->permissions_model->get_active_org();

		foreach($season_ids as $season_id)
		{
			foreach($team_ids as $team_id)
			{
				echo $this->teams_model->update_team_season_assignment($team_id,$season_id,$user,$owner);
			}
		}
    }
    
    public function send_welcome_email()
    {
    	$team_id=(int)$this->input->post('team_id');
    	echo $this->send_team_welcome_email($team_id);
    }
    private function send_team_welcome_email($team_id)
    {
    	$this->load->library('encrypt');
        
    	$team=$this->teams_model->get_team_info_manager($team_id);
    	if(count($team) == 0)
    		return "No Manager Exists.";
    	//var_dump($team);
    	$team=$team[0];
    	$email=$team['email'];
    	$res=array('fname'=>$team['person_fname'] , 'lname'=>$team['person_lname'] , 'league'=>$team['league_name'],
    			    'team'=>$team['team_name'],      'user'=>$team['login'],'url'=>'','pass'=>$this->encrypt->decode($team['password'],SSI_ENC_KEY));
    	
    	
    	
    	//var_dump($team[0]);
    	
    	$org=$this->permissions_model->get_active_org();
    	$entity = $this->teams_model->get_entity_by_org($org);
    	$entity=$entity[0]['entity_id'];
    	$url=$this->teams_model->get_entity_url($entity);
    	if(count($url)==0)
    		$url==null;
    	else $url=  $url[0]['url'];
    	//echo $url;
		
		$res['url']="http://".$url;
			
		
    	//$org_id=(int)$team['org_id'];
    	
		//get manmager
		/*
		*keys needed::
		* $fname
		* $lname
		* $league
		* $team
		* $user
		* $pass
		* $url 
		* 
		*/

		$email = $team['email'];
	
		if(false)//override for testing
		{
			$email='sam@servillian.com';
			$email='lothrazar@hotmail.com';
		}

        
          $message=$this->load->view("emails/team.welcome.php",$res,true);
          $this->Email('noreply@servillian.com',array($email),'','','Welcome to Spectrum ',$message,$team['team_name']);  
          return "Sent to Team Managers email address : ".$team['email'];  		
    }
    /**
    * put your comment there...
    * 
    * @param mixed $from
    * @param array $to
    * @param mixed $cc
    * @param mixed $bcc
    * @param mixed $subject
    * @param mixed $message
    * @param mixed $name
    */
    public function Email($from,  $to,$cc,$bcc,$subject,$message,$name)
    {
        $this->load->library('email');
        $this->email->from($from);
        $this->email->to($to[0]);
        $this->email->cc($cc);
        $this->email->bcc($bcc);
        $this->email->subject($subject);
        $this->email->letterhead($message,$to,$name);
        $this->email->send();
    }
 }
                                    