<?php

class Home extends Controller {

    public $f;
    function Home()
    {
        parent::Controller();    
        $this->load->model('permissions_model');
        $this->load->model('endeavor_model');
    	$this->load->library('result');
        
        //$this->load->plugin('facebook_pi');
        //TODO: shouldnt these be defined in CONSTANTS.php ?
        $config = array(
            'appId'  => '232419156814974',
            'secret' => '3758d3bb5a78134c8813997070808f2a',
            'cookie' => true,
            'fileUpload' => true,
            );
 
        $this->f=$this->load->library('Facebook', $config);
    }

    function index()
    {                   
        $this->f=$this->facebook; 
        $user = $this->f->getUser();
        
        if ($user) {
            // Proceed knowing you have a logged in user who's authenticated.
            $user_profile = $this->f->api('/me');
            if(isset($user_profile["id"]))
            {
				$this->load->view('login/fblogin',$user_profile);
            }
                
            $logoutUrl = $this->f->getLogoutUrl();
        } 
        else 
        {
			$loginUrl = $this->f->getLoginUrl(array('scope'  =>'friends_groups,publish_stream,read_friendlists,email,user_photos,read_stream,user_about_me,user_birthday,user_checkins,user_groups,user_location,user_photo_video_tags,user_status'));
			$data = array('loginUrl' => $loginUrl);
			$this->load->view('login/loading',$data);
        } 
    } 
    
    public function getFacebookObject()
    {
        $this->f=$this->facebook; 
        
        $user_profile   = $this->f->api('/me');
        $userGroups     = $this->f->api('/me/groups');
        
        //$feed=$this->f->api('/me/feed/','post', array('access_token'=>$this->f->getAccessToken(),"message"=>'test'));
        //$groupFeed=$this->f->api('189483694430107/feed/','post', array('access_token'=>$this->f->getAccessToken(),"message"=>'Test Group Wall  Post','picture'=>'http://playerspectrum.com/templates/spectrum/images/spectrum_tiny.png'));
        
        $this->result->json(array_merge($user_profile,$userGroups ) )  ;
    }
    public function json_get_facebook_friends()
    {
        $fb_id=$this->input->get_post('fb_id');                             
        $result=$this->permissions_model->get_facebook_friends($fb_id);    
        $this->result->json_pag($result);
    }
    public function json_adjustFBFriendsGroups()
    {   
        $fb_id          =$this->input->get_post('fb_id');
        
        $this->f        =$this->facebook;            
        $friends        =$this->f->api('/me/friends');
        $groups         =$this->f->api('/me/groups');
                  
        $friendsList='';
        foreach($friends["data"] as $v)
            $friendsList.=$v["id"].','.$v["name"].'---';         
        if($friendsList!='')$friendsList=$friendsList.substr(0,strlen($friendsList)-3);
        
        $groupsList='';
        foreach($groups["data"] as $v)
            $groupsList.=$v["id"].','.$v["name"].'---';         
        if($groupsList!='')$groupsList=$groupsList.substr(0,strlen($groupsList)-3);
                                                       
        $this->endeavor_model->adjust_fb_friends($fb_id,$friendsList);
        $this->endeavor_model->adjust_fb_groups($fb_id,$groupsList);
        
        $this->result->success("1");                
    }
    public function check_facebook_login_status()
    {       
        //$this->f        =$this->facebook;                  
                                                                               
        if(strtotime($_SESSION['login_time'])+1  <   strtotime((string)date('Y-m-d H:i:s')))
        {
            //Logout from facebook not working
        }       
    }       
}
  
?>
