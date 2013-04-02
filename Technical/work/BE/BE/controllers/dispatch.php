<?php

class Dispatch extends Controller
{
	
	/**
	* @var Dispatch_model
	*/
	public $dispatch_model;
	/**
	* @var permissions_model
	*/
	public $permissions_model;
	/**
	* @var org_model
	*/
	public $org_model;
	public function __construct()
	{
		parent::Controller();
		$this->load->model('dispatch_model');
        $this->load->model('permissions_model');
        $this->load->model('org_model');
        $this->load->library('page');
        $this->load->library('input');  
		$this->load->library('result'); 
	}
    private function load_window()
    {
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/");
        $this->window->set_js_path("/assets/js/endeavor/");   
    }
 
    
    public function post_bugfeature()
    {
		$user = $this->permissions_model->get_active_user();
		$org  = $this->permissions_model->get_active_org();
		$user_data=$this->permissions_model->get_user($user);
		
		$login = "";
		$full_name="";
		if(count($user_data))
		{					
			$login = $user_data[0]['login'];
			$full_name=$user_data[0]['person_fname']." ".$user_data[0]['person_lname'];
		}
        $context= rawurldecode($this->input->post('context'));
        $email  = rawurldecode($this->input->post('email')); 
        $ip=$_SERVER['REMOTE_ADDR'];
        
        $is_bug= $this->input->post('is_bug');
  
        $today = date('Y-m-d g:i a');
 
 		$is_bug  = ($is_bug=='t' || $is_bug=='true' || $is_bug===true || $is_bug=='on');
 		$subject = ($is_bug) ? "Spectrum Bug report " : "Spectrum Feature request ";
 		
 		$subject .= "from: ".$full_name;
 		
  		$data=array('data'=>array('login'=>$login,'ip'=>$ip,'date'=>$today,"is_bug"=>$is_bug,'context'=>$context,
  					'user_id'=>$user,'org_id'=>$org,'full_name'=>$full_name));
 
  		$this->Email("noreply@servillian.com","request@servillian.timetask.com",null,null,$subject,$context);
  		
  		$this->load_window();
  		$html=$this->load->view('endeavor/bugreport_success.php',$data,true);
  		echo $html;
  		
  		/*
  		using XML curl is depreciated, now we use request@intervals
  
        
        if($is_bug )//??is giving on/off messages
        {
			$module_id=139244;//bug task
        }
        else
        {
			$module_id=140186;//hardcoded id of feature request task in the api
        }
       // $module_id = "<![CDATA[".$module_id."]>]";
        $new_line="\n";//<br/> doesnt work
        //finally, append context with user and org record
        $context.=$new_line."End of user input.  Internal statistics: ";
        $user_data = $this->permissions_model->get_user($user);
        $full_timestamp="F j, Y, g:i a";
        $strUser='Submitted By: ';
        $today = date($full_timestamp);
        if(!count($user_data))
        {
			$strUser.="User id not found: user_id=".$user;
        }
        else
        {
			$name=$user_data[0]['person_fname']." ".$user_data[0]['person_fname'];
			$email=$user_data[0]['email'];
			$strUser.="Name: ".$name.", user_id: ".$user." Timestamp of submission :".$today." ";
        }
        
        $context.=$new_line.$strUser;
        $strOrg="Active Org: ";
        $org_data = $this->org_model->get_entity_org_details($org);
        if(!count($org_data))
        {
			$strOrg.= "Org not find: org_id=".$org;
        }
        else
        {
			$strOrg.= $org_data[0]['org_name'].",  org_id=".$org;
        }
        $context.=$new_line.$strOrg;
        echo $this->dispatch_model->intervals_createtask($title,$context,$module_id);
        //$this->Email("noreply@servilliansolutionsinc.com","ryan@servillian.com","bholbrook@servillian.com","",$title,$context);
		//echo $status;
		*/
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
	
}

?>
