<?php
require_once('endeavor.php');   
class Registration extends Endeavor
{

	/**
	* @var registration_model
	*/
	public $registration_model;
	  /**
  	  * 
  	  * 
  	  * @var leagues_model
  	  */
  	  public $leagues_model;
  	  	/**
	* @var season_model
	*/
	public $season_model;
    public function __construct()
    {
        //parent::Controller();    
        parent::__construct();       
        $this->load->model('registration_model');
        $this->load->model('permissions_model');
        $this->load->model('leagues_model');
        $this->load->model('season_model');
        $this->load->library('page');
        $this->load->library('input');   
		$this->load->library('result');
    }
    
    private function load_window()
    {
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/registration/");
        $this->window->set_js_path("/assets/js/registration/");   
    }
    
    public function window_manageregistration($id=false)
    {
        $d["season_list"]=$this->build_season_list();
        $data["teams_list"]=$this->load->view('registration/registration.list.php',null,true);
        $data["details"]=$this->load->view('registration/registration.details.php',$d,true);
        
        $this->load_window();
        $this->window->add_css('');
        $this->window->add_js('class.registration.js');
        $this->window->set_header('Manage Registration');
        $this->window->set_body($this->load->view('registration/registration.main.php',$data,true));
        $this->window->set_footer($this->load->view('registration/registration.footer.php',null,true));
        //if($id) $this->window->set_id($id);
        $this->window->json();
    }
    
    public function json_approve_teams()
    {
        //--------------------------------------------------
        $creator = $this->permissions_model->get_active_user();
        $owned_by= $this->permissions_model->get_active_org();
        //--------------------------------------------------
        $selected_team_ids=$this->input->post("selected_team_ids");
        $this->registration_model->approve_teams($selected_team_ids,$creator,$owned_by);
    } 
    
    public function json_get_team_underprocess()
    {
        $a_org= $this->permissions_model->get_active_org();
        $result = $this->registration_model->team_underprocess($a_org);
        $this->result->json($result);
    }
    
    public function json_get_season_registrations()
    {
        $season_id=$this->input->get_post('season_id');
		$result = $this->registration_model->get_season_registrations($season_id);
		$this->result->json_pag($result);
    }
    public function json_approvediscard_registration()
    {
        $approved_status=$this->input->get_post('approved_display');
        
        //$result=$this->registration_model->approvediscard_registration();
        //echo json_encode(array("success"=>true,"result"=>$result));
    }
    

    
    public function build_season_list()
    {
        $res=$this->registration_model->get_registration_params();
        
        $c='<select id="registration-season-selection-list" name="season-selection-list" >';
        $i=0;
        foreach($res as $v)
        {
            $c.="<option value=\"{$v["season_id"]}\">{$v["season_name"]}";
            $i=$i+1;
        }
            
        $c.='</select>';
        return $c;
    }
    
    public function json_approveTeams()
    {
        $team_entity_ids=$this->input->get_post("team_entity_ids");
        $result=$this->registration_model->approveTeams($team_entity_ids);
        $this->result->success(   $result[0]["approveTeams"]);                     
    }
    public function json_getRegisteredTeamManagerInfo()
    {
        $season_id      =$this->input->get_post("season_id");
        $team_entity_id =$this->input->get_post("team_entity_id");
        $team_org_id    =$this->input->get_post("team_org_id");
        
        //get General Info
        $teamManagerInfo    =$this->registration_model->getTeamManagerInfo($team_org_id);
        $page='<h3>General Info</h3>';
        $page.='<table width=100% style="font-size:12px">';
        foreach($teamManagerInfo as $v)
        {
            $page.='<tr>';    
            $page.='<td>';
            $page.='First Name';
            $page.='</td>';
            $page.='<td>';
            $page.=$v["person_fname"];            
            $page.='</td>';   
            $page.='</tr>';
            
            $page.='<tr>'; 
            $page.='<td>';
            $page.='Last Name';
            $page.='</td>';   
            $page.='<td>';
            $page.=$v["person_lname"];
            $page.='</td>';   
            $page.='</tr>';
            
            $page.='<tr>'; 
            $page.='<td>';
            $page.='Gender';
            $page.='</td>';   
            $page.='<td>';
            $page.=$v["person_gender"];            
            $page.='</td>';   
            $page.='</tr>';
            
            $page.='<tr>'; 
            $page.='<td>';
            $page.='Email';
            $page.='</td>';   
            $page.='<td>';
            $page.=$v["email"];            
            $page.='</td>';   
            $page.='</tr>';
            
            $page.='<tr>'; 
            $page.='<td>';
            $page.='Primary Phone';
            $page.='</td>';   
            $page.='<td>';
            $page.=$v["home_phone"];            
            $page.='</td>';   
            $page.='</tr>';
            
            
            $page.='<tr>'; 
            $page.='<td>';
            $page.='Secondary Phone';
            $page.='</td>';   
            $page.='<td>';
            $page.=$v["work_phone"];            
            $page.='</td>';   
            $page.='</tr>';
        }  
        $page.='</table>';
        
        //Get Custom Form
        /*$teamInfo   =$this->registration_model->getRegisteredTeamInfo($season_id,$team_entity_id);
        $page.='<h3>CustomFields</h3>';
        $page.='<table width=100%  style="font-size:12px">';
        foreach($result as $v)
        {
            $page.='<tr>';
            
            $page.='<td>';
            $page.=$v["field_title"];            
            $page.='</td>';
            
            $page.='<td>';
            $page.=$v["field_value"];            
            $page.='</td>';
            
            $page.='</tr>';
        }  
        $page.='</table>';
        */
        echo $page;
    }
    public function json_getRegisteredTeamInfo()
    {
        $season_id      =$this->input->get_post("season_id");
        $team_entity_id =$this->input->get_post("team_entity_id");
        $team_org_id    =$this->input->get_post("team_org_id");
        
        $teamInfo   =$this->registration_model->getRegisteredTeamInfo($season_id,$team_entity_id);
        $this->result->json_pag($teamInfo);
    }
    
    /*********************** EMAIL RECEIVER METHODS ***********************
    * These methods receive the data from a curl method and call the email methods
    * in this controller
    */
    
    /**
    * These function receive the instructions from the frontend website for sending emails
    * They may only be accessed through curl
    */
    
    /**
    * receives post of information to be handed to emailTeamRegistration
    * its the confirmation email for the league manager
    * 
    */
    public function postEmailTeamRegistration()
    {
    	echo "curl worked to postEmailTeamRegistration";
        $reginfo         = $this->input->post('reginfo');
 
    	 $to = $reginfo['person_email'];
       	if(!$to ) $to = $reginfo['email'];
       	 
       	 
       	 //fetch the managers apssword
		$reginfo['password'] = null;
		$userData = $this->permissions_model->get_user($reginfo['manager_user_id']);
		$this->load->library('encrypt');
		if(isset($userData[0]['password']))
	        $reginfo['password'] = $this->encrypt->decode($userData[0]['password'],SSI_ENC_KEY);
		 
       	 
       	 $reginfo['is_manager'] = true;
		$reginfo['subject'] = 'Spectrum Team Registration Confirmation';
		$body = $this->load->view('emails/teamRegistrationConfirmationToManager', $reginfo, true);
 
		$this->load->library('email');
		$this->email->to( $to);
		$this->email->subject($reginfo['subject']);
		$this->email->message($body);
		$this->email->send(false);
		
		
		
		echo "SENT TO manager ".$to;
		
		
    	 $active_user_id  = $reginfo['active_user_id '];
    	 if($active_user_id != $reginfo['manager_user_id'])
    	 {
			 //if manager and active user are DIFF PPL
    	 	 $to  = $reginfo['active_email'];
       		 $reginfo['is_manager'] = false;
       		 $reginfo['login'] = null;
       		 $reginfo['password'] = null;
			 //do it again
			 
			 $body = $this->load->view('emails/teamRegistrationConfirmationToManager', $reginfo, true);
  
			$this->email->to( $to);
			$this->email->subject($reginfo['subject']);
			$this->email->message($body);
			$this->email->send(false);
			
			
    	 }
		
		
		
		
    }
    
    /**
    * receives post of information to be handed to emailPlayerregistartion
    */
    public function postEmailPlayerRegistration()
    {
       // var_dump($_POST);
        
        $reginfo         = $this->input->post('reginfo');
        $destination     = $this->input->post('destination');
        $test             =@$this->input->post('test');
        if(!$test) $test = false;
        
        $this->emailPlayerRegistration($reginfo, $destination, $test);
    }
    
    
    /**
    * frontend will curl to this function to send email invitations
    * 
    * will save into season_player_invitation table
    * and also send the emails for eacn hone
    * 
    * post data needs invites : json encoded array of name,email
    * season_id
    * team_id
    * manager_user_id
    * 
    */
    public function postEmailInvitationByEmail()
    {  
    	$test_post = $this->input->get_post("invites");
 
 		//decode special characters
    	$test_decode = htmlspecialchars_decode($test_post);
 		//decode the json
    	$invites = json_decode($test_decode,true);
    	 
    	$season_id = (int) $this->input->get_post('season_id');    	
    	$team_id   = (int) $this->input->get_post('team_id');    	
    	$manager_user_id = (int) $this->input->get_post('manager_user_id');
    	
    	
    	$data = array();
    	//get team data
    	$team = $this->teams_model->get_team_details($team_id);
    	$data['team_name'] = $team[0]['team_name'];
    	$team_entity = $team[0]['entity_id'];
    	//season data
    	$sn = $this->season_model->get_season_data($season_id);
    	$data['season_name'] = $sn[0]['season_name'];
    	$league_id = (int)$sn[0]['league_id'];
    	
    	//league data
    	$lg = $this->leagues_model->get_league_info($league_id);
    	$data['league_name'] = $lg[0]['league_name'];
    	$data['url'] = 'http://'.$lg[0]['url'];
    	
    	//sender (team manager) data
    	$sender = $this->permissions_model->get_user($manager_user_id);
    	$data['person_fname'] = $sender[0]['person_fname'];
    	$data['person_lname'] = $sender[0]['person_lname'];
    	
    	//GET:
    	
        $this->load->library('email');
        
    	$subject = 'Spectrum Invitation By Manager';
    	 
    	foreach($invites as $inv)
    	{
    		$dest_name  = $inv['name'];
    		$dest_email = $inv['email'];
    		$data['invitee_name'] = $dest_name;//overwrite name each time. otherwise data is all the same
    		
	        $body = $this->load->view('emails/playerInvite', $data, true);
	        
    		$this->email->to( $dest_email );
	        $this->email->subject($subject);
	        $this->email->message($body);
	        $this->email->send();
	         
	        $inserted= $this->registration_model->insert_email_invitation($season_id,$team_entity,$dest_name,$dest_email);		
	        var_dump($inserted);	
    	} 
    }

    public function postTeamManagerCredentials()
    {
        $info   = $this->input->post('info');
        $email  = $this->input->post('email');
        $this->emailTeamManagerCredentials($info,$email);
    }
    
    /*********************** EMAIL CONFIRMATION METHODS ***********************
    * These methods must remain private methods
    */
    
    /**
    * Email team summary information after team signup. Includes all
    * information entered during team registration. This does not replace
    * the player registration if the team manager is also a player.
    * 
    * @param mixed $team TeamID or array of items to be inserted into the email
    * @param string $destination manager, league
    * 
    */
    private function emailTeamRegistration($reginfo, $destination, $test=false)
    {
		// If $player is numeric, it is the player id that the registration 
		// will be sent out for. Gather the saved information for player 
		// and construct array $player
		if(is_numeric($reginfo))
		{
			
		}
		
		//construct data
		foreach($reginfo as $k=>$v) $data[$k] = $v;
		
		switch($destination)
		{
			case "manager":
				// Team Manager Email
				$data['subject'] = 'Spectrum Team Registration Confirmation';
				$body = $this->load->view('emails/teamRegistrationConfirmationToManager', $data, true);
				$this->load->library('email');
				$this->email->to( $reginfo['manager']['email'] );
				$this->email->subject($data['subject']);
				$this->email->message($body);
				$this->email->send($test);
				break;
			
			case "league":
				// Amalgamate team manager emails
				$emails = array();
				foreach($reginfo['exec'] as $exec) $emails[] = $exec['email'];
				
				// League Email
				$data['subject'] = 'Spectrum Team Registration Notification';
				$body = $this->load->view('emails/teamRegistrationConfirmationToLeague', $data, true);
				$this->load->library('email');
				$this->email->to( $emails );
				$this->email->subject($data['subject']);
				$this->email->message($body);
				$this->email->send($test);
				break;
		}
    }
    
    /**
    * This email will be sent to the user after they have completed
    * registering for a team. It will send back all of the information
    * they provided during registration.
    * 
    * @param mixed $player PlayerID or array of items to be inserted into the email
    * @param string $destination player, manager, or league
    * @param string $test email test mode
    * 
    */
    private function emailPlayerRegistration($player, $destination, $test=false)
    {
		// If $player is numeric, it is the player id that the registration 
		// will be sent out for. Gather the saved information for player 
		// and construct array $player
		if(is_numeric($player))
		{
			
		}
		
		//construct data
		foreach($player as $k=>$v) $data[$k] = $v;
		
		// send email by destination
		switch($destination)
		{
			case "player":
				// PLAYER EMAIL
				$data['subject']    = 'Spectrum Player Registration Confirmation';
				$body               = $this->load->view('emails/playerRegistrationConfirmationToPlayer', $data, true);
				$this->load->library('email');
				$this->email->to( $player['info']['email'] );
				$this->email->subject($data['subject']);
				$this->email->message($body);
				$this->email->send($test);
				break;
				
			case "manager":
				// Amalgamate team manager emails
				$emails = array();
				foreach($player['manager'] as $manager) $emails[] = $manager['email'];
				
				// TEAM MANAGER EMAIL
				$data['subject']    = 'Spectrum Player Registration Notification';
				$body               = $this->load->view('emails/playerRegistrationConfirmationToManager', $data, true);
				$this->load->library('email');
				$this->email->to( $emails );
                $this->email->subject($data['subject']);
				$this->email->message($body);
				$this->email->send($test);
				break;
			
			case "league":
				// Amalgamate team manager emails
				$emails = array();
				foreach($player['exec'] as $exec) $emails[] = $exec['email'];
			
				// LEAGUE EMAIL
				$data['subject']    = 'Spectrum Player Registration Notification';
				$body               = $this->load->view('emails/playerRegistrationConfirmationToLeague', $data, true);
				$this->load->library('email');
				$this->email->to( $emails );
				$this->email->subject($data['subject']);
				$this->email->message($body);
				$this->email->send($test);
				break;
		}
    }
    
    private function emailInvitationByEmail($info,$emails)
    {
    	

    }

    private function emailTeamManagerCredentials($info,$email)
    {
        $data['subject']    = 'Your Spectrum Login Credentials';
        $body               = $this->load->view('emails/userCredentialsToManager', $info, true);
        $this->load->library('email');
        $this->email->to($email);
        $this->email->subject($data['subject']);
        $this->email->message($body);
        $this->email->send();
    }
}
