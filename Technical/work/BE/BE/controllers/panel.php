<?php

class Panel extends Controller
{
	
	public function index()
	{
		$response = array();
		$response['email'] 		= $this->html_email();
		$response['notices'] 	= $this->html_email();
		$response['alert'] 		= $this->html_email();
	}
	
	private function html_email()
	{
		$data = array();
		return $this->load->view('panel/email',$data,true);
	}
	
	private function html_notices()
	{
		$data = array();
		return $this->load->view('panel/notices',$data,true);
	}
	
	private function html_alerts()
	{
		$data = array();
		return $this->load->view('panel/alerts',$data,true);
	}
	
	public function html_panel()
    {
     echo "test panel controller";
    }
    
    public function get_active_roles()
    {
        $userid = $this->permissions_model->get_active_user();
        echo json_encode($this->permissions_model->get_user_assignments($userid));
    }
    
     
     /*   echo 'test';
      //  exit();
        $this->load->model('permissions_model');
        $this->window->set_css_path("/assets/css/");
        $this->window->set_js_path("/assets/js/system/");
        
        $userid = $this->permissions_model->get_active_user();
        
        $data = array();

        $roles =  $this->permissions_model->get_user_assignments($userid);

        $menuLabel = 'Role Type:';
        
        
        
        
        //TODO: get user name, based on this user id, and then display name instead of user
        $userName = $userid;
        
        $data['user'] = $userName;
        
        
        
         //sample data for name
        
         foreach($roles as &$role)
         {
             $role['role_name'] = "nameof".$role['role_id'];
         }      
        
        $data['roles'] = $roles;
        //TODO: get these values from the persons account , possibly based on currently selected role..?
        $data['accounts'] = array(
            array('lbl'=>'CDN Funds','amt'=>'1906.23'),
            array('lbl'=>'NSA Dollars','amt'=>'0.01'));
        $data['menuLabel']=     $menuLabel;
        $data['menu']='';
        $this->load->view('endeavor/panel',$data);
    }
    
    */
}




