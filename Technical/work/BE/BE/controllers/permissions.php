<?php
 require_once('endeavor.php');   
  class Permissions extends Endeavor
  {
  	  
	  /**
  	  * 
  	  * 
  	  * @var person_model
  	  */
  	  public $person_model;
    function __construct()
    {
        parent::Controller();
        $this->load->model('person_model');
        $this->load->model('endeavor_model');
        $this->load->model('permissions_model');
        $this->load->library('page');
        $this->load->library('input');
        $this->load->library('result');
        $this->load->library('encrypt');
    }
    private function load_window()
    {
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/");
        $this->window->set_js_path("/assets/js/components/users/");
    }
    /**
    * @author Sam
    * loads maintenance window
    * 
    */
    public function window_maintenance()
    {
        //if(! 
        $this->endeavor_model->process_controllers();
       // ) return;//if it fails : NEW: do nothing, just go off how it was before
        
        $this->load->library('window');
        //$this->window->set_css_path("/assets/css/");
       // $this->window->add_css('maintenance.css');
        
        $this->window->set_js_path("/assets/js/models/");
        
        
        $this->window->add_js('role.js');
        $this->window->add_js('method.js');
        $this->window->add_js('menu.js');
        
        $this->window->set_js_path("/assets/js/components/maintenance/");
        
       //first windows and forms
        $this->window->add_js('forms/help_manager.js');
        $this->window->add_js('windows/help_manager.js');
        $this->window->add_js('forms/help_manager.js');
        $this->window->add_js('windows/help_manager.js');  
        //then grids
        $this->window->add_js('grids/all_roles.js');
        $this->window->add_js('grids/methods.js');
        $this->window->add_js('grids/menu_items.js');
        //controller last
        $this->window->add_js('controller.js');
         
        $this->window->set_header('Endeavor Menu / Method  Maintenance');
        $this->window->set_body($this->load->view('permissions/maintenance/maintenance.php',null,true)); 
        $this->window->json();
    }
    public function json_get_active_org_and_type()
    {
        $result=$this->permissions_model->get_active_org_and_type();
        $this->result->success($result);
        //echo json_encode(array("success"=>"true","result"=>$result));
    }
    
	public function logout()
	{
		echo $this->permissions_model->logout();
	}
 

	/**
	* @author Sam
	* main users window
	* 
	* returns html using a view
	*/
	public function window_my_org()
    {
    	$data['org_id']   = $this->permissions_model->get_active_org();
    	$data['org_type'] = $this->permissions_model->get_active_org_type();
    	//$data['org_type'] = $this->permissions_model->get_active_org_type();
    	
    	$this->load->library('window');
        $this->window->set_css_path("/assets/css/");
        
        $this->window->set_js_path("/assets/js/models/");
        $this->window->add_js('role.js');
        $this->window->add_js('user_role.js');
        $this->window->add_js('users.js');
        
        
        $this->window->set_js_path("/assets/js/components/users/");
        $this->window->add_js('grids/org_roles.js');
        
        $this->window->add_js('forms/org_roles.js');
        $this->window->add_js('forms/user_person.js');
        $this->window->add_js('windows/roles.js');
        $this->window->add_js('windows/users.js');
                
        $this->window->add_js('grids/org_users.js');
        $this->window->add_js('grids/user.js');
        
        $this->window->add_js('controller.js');
        
         
        
        $this->window->set_header('Users');
        $this->window->set_body($this->load->view('permissions/my_org.php',$data,true));
        //$this->window->set_footer($this->load->view('permissions/permissions.users.footer.php',$data['footer'],true));
        //if($id) $this->window->set_id($id);
        $this->window->json();
    }
    
    /**
    * @deprecated
    * 
    */
    public function window_login()
    {
        $this->load_window();
        // $this->window->add_css('inventory/modify.css');
        $this->window->add_js('class.permissions.login.js');
                

      

        
        //window and form 
        $this->window->set_header('Login');
        
        $this->window->set_body($this->load->view('permissions/permissions.login.main.php',null,true));
        $this->window->set_footer('Login Page');
        //if($id) $this->window->set_id($id);
        $this->window->json();
    }
    public function json_get_roles_by_orgtype()
    {
        $org_type_id=$this->input->post('org_type_id');
        $result= $this->permissions_model->get_roles_by_orgtype($org_type_id);
        $this->result->json($result);
    
    }
    
    public function post_active_org()
    {
        $id = $this->input->post('org_id');
        
        $this->permissions_model->set_active_org($id);
        
 
        $result['user_exec']=$this->permissions_model->get_is_user_org_executive();
         
        $result['org_id']   = $id;
        $result['user_id']   = $this->permissions_model->get_active_user();
        $result['org_type'] = $this->permissions_model->get_active_org_type();
        $result['org_is_valid']= $this->permissions_model->is_active_org_valid();
        $result['last_login_date']= $this->permissions_model->get_last_login_date();
         
        $this->result->json($result);
    }
    
    public function post_default_org()
    {
        $userid = $this->permissions_model->get_active_user();
        $orgid  = $this->permissions_model->get_active_org();
        //save default for dropdown

        echo $this->permissions_model->save_default_org($userid,$orgid);     
        
    }
    
    public function json_getusers()
    {
        $result= $this->permissions_model->get_people_are_users();
        
        $fmt="F j, Y; g:i a";
        $bd_fmt="F j, Y";
        $plain='Y/m/d';
        foreach($result as &$user)
        {
			//format dates
			if($user['last_login_date'])
				$user['last_login_date']=date($plain,strtotime($user['last_login_date']));
			if($user['person_birthdate'])
				$user['person_birthdate']=date($plain,strtotime($user['person_birthdate']));
			if($user['person_gender']) $user['person_gender']=strtoupper($user['person_gender']);
			else $user['person_gender']='null';
        }
        
        $this->result->json($result);
    }
    public function json_getroles()
    {
        $result= $this->permissions_model->get_roles();
        $this->result->json($result);
    }
    
    public function json_getluauth()
    {
        $this->result->json($this->permissions_model->get_lu_auth());        
    }
    
    public function json_get_rolemethods()
    {
        $role_id = (int)$this->input->get_post('role_id');
        
        $contr_id = (int)($this->input->get_post('contr_id'));
 
        
        $this->result->json($this->permissions_model->get_controller_role_allowed($role_id,$contr_id));
    }
    public function json_get_roles_by_userid()
    {
        $user_id =$this->input->get_post('user_id');
        
        $result= $this->permissions_model->get_roles_by_userid($user_id);
        $this->result->json_pag($result);    
    }
    public function json_get_unassignedroles_by_userid()
    {
        $user_id =$this->input->get_post('user_id');
        
        $result= $this->permissions_model->get_unassignedroles_by_userid($user_id);
        $this->result->json_pag($result);    
    }
    public function json_getmenu()
    {
        $parent = $this->input->get_post('parent');
        
        if($parent == null || $parent == "null" || $parent == "undefined" || $parent == '')
            $this->result->json($this->permissions_model->get_menubar());
        else    
            $this->result->json($this->permissions_model->get_menu($parent));
    }
    //get_role_menu
    public function json_get_rolemenu()
    {//TODO: mirror of rolemethods
        $role_id = (int)$this->input->get_post('role_id');
        $parent = (int)$this->input->get_post('parent');
        if(!$role_id||$role_id<0){ echo json_encode(array());return;}
 
 		$perms=$this->permissions_model->get_role_menu_parent($parent,$role_id);
 		foreach($perms as &$p)
 		{
			if(!$p['view_code']) $p['view_code']='NONE';//NONE for blank/defaults
			if(!$p['update_code']) $p['update_code']='NONE';//NONE for blank/defaults
			
			
			if(!$p['view_auth_id']) $p['view_auth_id']='5';//NONE for blank/defaults
			if(!$p['update_auth_id']) $p['update_auth_id']='5';//NONE for blank/defaults
 		}
        $this->result->json( $perms);
    }
    
    public function post_rolemenu()
    {
    	$role_id = (int)$this->input->get_post('role_id');
    	$menu_id = $this->input->get_post('id');//id for sys_menu table, should be menu_id but itws not
    	$view_code = $this->input->get_post('view_code');
    	$update_code = $this->input->get_post('update_code');
    	$code_to_id = array("INHERIT"=>1, "NONE"=>5,"OWN"=>4,"ALL"=>6,"ROLE"=>3,"ORG"=>2);
    	//js gives us the code, not the number
    	
    	if($role_id && $role_id>0)
    		echo $this->permissions_model->update_rolemenu($role_id,$menu_id,$code_to_id[$view_code],$code_to_id[$update_code]); 
    	else echo -5;
 
    }
    
    public function post_maintenance_filename()
    {		
    	$file = rawurldecode($this->input->post('file'));
		
		
		$_SESSION['maintenance_filename']=$file;
		echo $_SESSION['maintenance_filename'];	
    }
    
    public function post_rolemethod()
    {
    	$role_id = $this->input->get_post('role_id');
    	//$m=$this->input->post('method_id');
    	$controller_name = $this->input->get_post('controller_name');
    	$method_name=$this->input->get_post('method_name');
    	
    	echo $this->permissions_model->update_rolemethod($role_id,$method_name,$controller_name);  
    }
    public function post_delete_rolemethod()
    {
    	$role_id= $this->input->get_post('role_id');
    	//$m=$this->input->post('method_id');
    	
    	//$role = $this->input->post('role_name');
    	$method=$this->input->get_post('method_name');
    	$controller_name = $this->input->get_post('controller_name');
    	
    	echo $this->permissions_model->delete_rolemethod($role_id,$method,$controller_name); 
    }
    

    
    public function json_get_auth()
    {
        $result= $this->permissions_model->get_auth();
        $this->result->json($result);
        
    }
    
    public function json_get_controllers()
    {
        
        $this->result->json($this->permissions_model->get_controllers());
    }
    
    public function json_get_methods()
    {
        $contr_id = (int)$this->input->get_post('contr_id');
 
        $this->result->json($this->permissions_model->get_methods($contr_id));
    }
    
    
    public function json_get_orgtypes()
    {
        
        $result= $this->permissions_model->get_orgtypes_have_roles();
        $this->result->json($result);
        
    }
    
    public function json_get_orgtypes_below()
    {
        $type=$this->permissions_model->get_active_org_type();
        $result= $this->permissions_model->get_orgtypes_have_roles($type);
        $this->result->json($result);
        
    }
    public function json_get_orgtypes_have_roles()
    {
        
        $result= $this->permissions_model->get_orgtypes();
        $this->result->json($result);
        
    }
    
    public function json_get_org_bytype()
    {
        $type = $this->input->get_post('type');
        $result= $this->permissions_model->get_org_bytype($type);
        $this->result->json($result);
    }
    public function json_get_active_user_assignments()
    {
        
        $userid = $this->permissions_model->get_active_user();
        $this->result->json($this->permissions_model->get_user_assignments($userid));
    }
    /**
    * created for myUser App.menuHandlers
    * 
    */
    public function active_user_record()
    {
    
        $userid = $this->permissions_model->get_active_user();
        $this->result->json($this->_format_people_extgrid($this->permissions_model->get_user($userid)));
    }
    public function _format_people_extgrid($result) 
    {
    	$long_fmt="F j, Y; g:i a";
        $bd_fmt="F j, Y";
        $plain='Y/m/d';
		foreach($result as &$user)
        {
			//format dates
			if($user['last_login_date'])
				$user['last_login_date']=date($long_fmt,strtotime($user['last_login_date']));
			if($user['person_birthdate'])
				$user['person_birthdate']=date($plain,strtotime($user['person_birthdate']));
			if($user['person_gender']) $user['person_gender']=strtoupper($user['person_gender']);
			else $user['person_gender']='null';
			//split up phonenumbers for accuracy and display
			$phone_types=array('p_work'=>'work-','p_mobile'=>'mobile-','p_home'=>'home-');
			foreach($phone_types as $in=>$out)
			{
				$ac='';
				$pre='';
				$num='';
				$ext='';
				$input= ($user[$in]===null ? '' : $user[$in]);//avoid null problem
				$array=str_split($user[$in]);

				
				foreach($array as $c)
				{
					if(!is_numeric($c))continue;//ignore bad data
					
					if(strlen($ac)<3)//3 characters in area code
						$ac.=$c;
					else if(strlen($pre) < 3)//three more in prefix
						$pre.=$c;
					else if(strlen($num)<4)//next four in main number
						$num.=$c;
					else//extension
						$ext.=$c;
				}
				$user[$out.'ac']=$ac;
				$user[$out.'pre']=$pre;
				$user[$out.'num']=$num;
				$user[$out.'ext']=$ext;
				
				
			}

			
        }

		return $result;
    }
    public function json_get_user_roles()
    {
        
        $userid = (int)$this->input->post('user_id');
        $plain='Y/m/d';
        
        $roles=$this->permissions_model->get_user_assignments_include_expired($userid);
        foreach($roles as &$r)
        {
			$r['effective_range_start'] = ($r['effective_range_start']? date($plain,strtotime($r['effective_range_start'])) : null );
			$r['effective_range_end']   = ($r['effective_range_end']  ? date($plain,strtotime($r['effective_range_end']  )) : null );
			
        }
        $this->result->json($roles);
    }
    
    public function json_get_userbyperson()
    {
        
        $personid = (int) $this->input->post('person_id');
        $this->result->json($this->permissions_model->get_user_by_person($personid));
    }
    public function json_orgs_by_role()
    {	
    	$role_id= (int) $this->input->post('role_id');
		$this->result->json($this->permissions_model->get_orgs_by_role($role_id));
    }
 
    //Update Functions
    public function json_update_users_roles()
    {
        $user_id=$this->input->post('user_id');
        $role_id=$this->input->post('role_id');
        
        $this->permissions_model->json_update_users_roles($user_id,$role_id);
    }
    public function post_delete_user()
    {
        $mod = $this->permissions_model->get_active_user();
        $user_id = $this->input->post("user_id");
        
        
      
        
        echo $this->permissions_model->delete_user($user_id,$mod);
    }
    public function post_delete_assignment()
    {
        $mod = $this->permissions_model->get_active_user();
        $assn_id = $this->input->get_post("assn_id");
        if($assn_id)
        {
			//either a single one was deleted
        
        	echo $this->permissions_model->delete_assignment($assn_id,$mod);
		}
		else
		{
			//or an array was deleted, for example by selecting multiple rows
			//json_decode will undo the work of JSON.stringify
			$assn_id_array=json_decode($this->input->get_post("assn_id_array"));
			foreach($assn_id_array as $assn_id)
			{
				echo $this->permissions_model->delete_assignment($assn_id,$mod);
			}
		}
        
         
    }
 
    public function post_search_assignments()
    {
        $role_id = (int)$this->input->get_post('role_id');
        $org_id  = (int)$this->input->get_post('org_id');
        $user_id = (int)$this->input->get_post('user_id');
        
        $assn = $this->permissions_model->search_assignments($role_id,$org_id,$user_id);
        $plain='Y/m/d';
        
        foreach($assn as &$r)
        {
			$r['effective_range_start'] = ($r['effective_range_start']? date($plain,strtotime($r['effective_range_start'])) : null );
			$r['effective_range_end']   = ($r['effective_range_end']  ? date($plain,strtotime($r['effective_range_end']  )) : null );
			
        }
        
        echo $this->result->json_pag($assn);
    }

    
    public function post_update_user_person()
    {
        //this handles updates as well as inserts
        $creator = $this->permissions_model->get_active_user();
        $owned_by= $this->permissions_model->get_active_org();
        $fname   = $this->input->post('fname');
        $lname   = $this->input->post('lname');
       
        $email   = rawurldecode($this->input->post('email'));
        $street = rawurldecode($this->input->post('street'));
        $login   = $this->input->post('login');
        $pass    = rawurldecode($this->input->post('pass'));
        $bdate   = rawurldecode($this->input->post('bdate'));
        $person_id=$this->input->post('person_id');
        $default_org = $this->input->post('default_org');
        
        $postal_code = $this->input->post('postal');
        $city = $this->input->post('city');
        $region = $this->input->post('region');
        $country = $this->input->post('country');
        
        $p_mobile = $this->input->post('p_mobile');
        $p_work = $this->input->post('p_work');
        $p_home = $this->input->post('p_home');
        
        echo $this->_update_user_person($creator,$owned_by,$fname,$lname,$email,$street,$login,$pass,$bdate,$person_id,$default_org, 
      							$postal_code,$city,$region,$country,$p_mobile,$p_work,$p_home);
    } 
    
    private function _update_user_person($creator,$owned_by,$fname,$lname,$email,$street,$login,$pass,$bdate,$person_id,$default_org, 
      							$postal_code,$city,$region,$country,$p_mobile,$p_work,$p_home)
    {
        $this->load->library('encrypt');
		//TODO: phone numbers
        //var_dump($p_mobile.",".$p_work.",".$p_home); return;
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
            
        //if($creator == null || $creator == '' || $creator <=0)  return "not logged in";//
        
        if($default_org == 'new' || $default_org == null || $default_org == '' || $default_org == "null")
            $default_org = null;
        if($person_id == 'new' || $person_id == null || $person_id == '' || $person_id == "null")
            $person_id = -1;
         
            //insert OR UPDATE person record, which returns the id here
            //if person_id was given, it will update and return the same, other wise it returns new
            //echo "insert or update person";
        $person_id = $this->person_model->insert_person($creator,$person_id,$fname,$lname,$gender,$bdate,$owned_by);        
        
        if($person_id <= 0) return $person_id;//if error
        //echo "person id valid so continue";
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
        $street,$city,$region,$country,$postal_id,2,$owned_by);//type == 2 for home address, from lu_address_type
        //1 would be shipping, etc
        //the following are from lu_contact_type, for home, work, cellphone, email
        if($email != null)
             $this->permissions_model->insert_entity_contact($creator,$entity_id,1,$email,$owned_by);
         if($p_home != null)
             $this->permissions_model->insert_entity_contact($creator,$entity_id,2,$p_home,$owned_by);
         if($p_work != null)
             $this->permissions_model->insert_entity_contact($creator,$entity_id,3,$p_work,$owned_by);
         if($p_mobile != null)
             $this->permissions_model->insert_entity_contact($creator,$entity_id,4,$p_mobile,$owned_by);   

       if($pass == null || $pass == "" || $pass == "undefined" || $pass == "null" || $pass == -1)
           $pass = "-1";
       else
           $pass = $this->encrypt->encode($pass,SSI_ENC_KEY);
           //$pass = md5($pass);

        return $this->permissions_model->insert_user($creator,$person_id,$default_org,$login,$pass,$owned_by);  
         
		
		
    }
    /**
    * assigns given role and org to the active user
    * 
    */
    public function post_create_assignment_foractive()
    {
    	$user_id=$this->permissions_model->get_active_user();
		
        $creator = $this->permissions_model->get_active_user();
        $owner   = $this->permissions_model->get_active_org();
        $org_id    = $this->input->post('org_id');
        
		$end_date  = $this->input->post('date');
        
        if($end_date == '' || $end_date == "undefined" || $end_date == "null")
            $end_date = null;
            
		$role_ids=json_decode($this->input->post('role_ids'),true);
		foreach($role_ids as $role_id)
		{
        	echo $this->permissions_model->insert_assignment($creator,$user_id,$role_id,$org_id,$end_date,$owner);		
		}
		return;
	
    }
    public function post_create_assignment()
    {
        
        $role_id=(int)$this->input->post('role_id');
        $user_id=(int)$this->input->post('user_id');
        //we need user id, but sometiems person id is given instead: so handle this case
        $person_id=(int)$this->input->post('person_id');
        //var_dump($person_id);
        if(!$user_id || $user_id==-1)
        {
			$user_rec=$this->permissions_model->get_userid_by_person($person_id);
			if(count($user_rec)==0)
			{
				echo -1;
				return;
			}
			else
			{
				$user_id=$user_rec[0]['user_id'];
			}
        }
        $creator = $this->permissions_model->get_active_user();
        $owner   = $this->permissions_model->get_active_org();
        $org_id    = $this->input->post('org_id');
        if($org_id==="a") {$org_id=$owner;}//if flag says to use active org, then do so
        if(!$org_id) return false;
        $this->permissions_model->save_default_org($user_id,$org_id);
        $end_date  = $this->input->post('date');
        
        if($end_date == '' || $end_date == "undefined" || $end_date == "null")
            $end_date = null;
        
        if($role_id)//either a single role id was given
        {
        	echo $this->permissions_model->insert_assignment($creator,$user_id,$role_id,$org_id,$end_date,$owner);
        	return;
		}
		else
		{ //otherwise we should have an array of roles
			$role_ids=json_decode($this->input->post('role_ids'),true);
			foreach($role_ids as $role_id)
			{
        		echo $this->permissions_model->insert_assignment($creator,$user_id,$role_id,$org_id,$end_date,$owner);		
			}
			return;
		}
		echo -1;//if neither given, this is a failure
    }
    

    
    public function post_update_assignment()
    {
        $assn_id =(int)$this->input->post('assn_id');
        $start_date = rawurldecode($this->input->post('start_date'));
        $end_date   = rawurldecode($this->input->post('end_date'));
        $postgress_fmt="Y-m-d" ;
        if($end_date == '' || $end_date == "undefined" || $end_date == "null")
            $end_date = null;
        else
        	$end_date=date( $postgress_fmt,strtotime($end_date)); 
         if($start_date == '' || $start_date == "undefined" || $start_date == "null")
            $start_date = null;   
         else
        	$start_date=date( $postgress_fmt,strtotime($start_date));
            /*
        if($start_date && $end_date)
        if(strtotime($start_date) > strtotime($end_date))
        {
			//swap
			$x=$end_date;
			$end_date=$start_date;
			$start_date=$x;
        }*/
        $mod = $this->permissions_model->get_active_user();
        
        echo $this->permissions_model->update_assignment($mod,$assn_id,$start_date,$end_date);
    }
                              
    public function check_login()
    {          
        //load library
        $this->load->library('encrypt');

        //read input
        $username   =rawurldecode($this->input->post("username"));
        $password   =rawurldecode($this->input->post("password"));
        $ip         =rawurldecode($this->input->post("ip"));
        $fb_id      =$this->input->post("fb_id");
        //$cap_rquired=$this->input->post("captcha_is_required");
        //from captcha field. may be empty
		$cap_input = rawurldecode($this->input->post('captcha_input'));
		if(!$cap_input) $cap_input=null;
        
        //validate user
        //$return = $this->permissions_model->check_login($username,$password,$ip,$cap_input,$fb_id,$fb_email,$fbtoken);   
        $return = $this->permissions_model->check_login($username,$password,$ip,$cap_input,$fb_id,'');   
        $this->result->json($return);                                                                              
	}
        
        
    private function validate_captcha_input($input)
	{
		
		echo "$input == ".$_SESSION['captcha_login'];
		
		if($input == $_SESSION['captcha_login'])
		{
			echo 1;
		} 
		else
		{
			echo 0;
		}
	  
	  	
	}

    
    /**
    * primarily used from welcome screen
    * 
    */
    public function post_update_active_userpass()
    {
        $username=$this->input->post("login");
        $pass=$this->input->post("pass");
        $cfrm=$this->input->post("pass-cfrm");
        $user=$this->permissions_model->get_active_user();
        
		if($pass!=$cfrm)
		{
			$result='Error: Passwords do not match';
		}
		else
		{
			$encrypted = $this->encrypt->encode($pass,SSI_ENC_KEY);
			
			$s=$this->permissions_model->update_login_password($user,$username,$encrypted,$user);
			
			if($s<0)
			{
				$result= "The login name '".$username."' is already taken.  Please try another, or add some extra numbers to the end, for example, '".$username."123' ";
			}
			else if($s==0) {$result= "Error, active user not logged in, you may have to refresh the page";}
			else
			{
				$result= 1;
				
			}
		}
		
		$response=array('success'=>true,'result'=>$result);
		$this->result->json($response);
    }
    
    public function json_reset_password()
    {
        $email=$this->input->post("email");
        $username=$this->input->post("username");
        
        $password=date("siGdmY");
        //$password_md5=md5($password);
        $password_md5=$this->encrypt->encode($password,SSI_ENC_KEY);
        
        $result=$this->permissions_model->reset_password($username,$email,$password_md5);
        if($result[0]["reset_password"]==1)
        {
            $this->Email("noreply@servilliansolutionsinc.com",$email,'','','Password Recovery','Your new password is : '.$password);    
            echo 'Check your email for new pasword';
        }
        if($result[0]["reset_password"]==-1)
            echo 'Username provided is wrong';
        if($result[0]["reset_password"]==-2)
            echo 'Email provided is wrong'; 
    }
    
    /**
    * desc: This method is depreciated and replaced by post_retrieve_username
    */
    public function json_retrieve_username()
    {
        $email=$this->input->post("email");
        $result=$this->permissions_model->retrieve_username($email);

        if($result[0]["retrieve_username"]!=-1)
        {
            // Old method, switched to email object with letterhead
            //$this->Email("noreply@servilliansolutionsinc.com",$email,'','','Username Recovery','your username is : '.$result[0]["retrieve_username"]);    
            
            $data['subject']    = 'Spectrum Username Recovery';
            $info['login']      = $result[0]["retrieve_username"];
            $body               = $this->load->view('emails/userForgotUsername', $info, true);
            $this->load->library('email');
            $this->email->to($email);
            $this->email->subject($data['subject']);
            $this->email->message($body);
            $this->email->send();
            
            // Report back to user. This should be in a view and returned via JSON. >:(
            echo 'Check your email for your recoverd username';

        }
        else
            echo 'information provided is wrong';
    }
    
    public function Email($from,$to,$cc,$bcc,$subject,$message,$email_data=array())
    {
 
    	//$email_data[]=$message;//wtf
    	$this->load->library('email');
    	//$message = $this->load->view("emails/league.welcome.php",$email_data,true);
		$this->email->to($to);
	    $this->email->subject($subject);
	    $this->email->letterhead($message,$email_data,true);
	    $this->email->send(); 


    }
    
    
    
    public function json_active_org_type()
    {
		$org=array();
		$org['org_id']   = $this->permissions_model->get_active_org();
		$org['org_type'] = $this->permissions_model->get_active_org_type();
		
		$this->result->json($org);
    }
    

    public function json_allowed_roles_by_org()
    {
		$org_type= (int)$this->input->get_post('org_type');
		$inherit= $this->input->get_post('inherit');
		if(!$org_type )
		{//default to the active org, if none is given
			$org_type =$this->permissions_model->get_active_org_type();
		}
		$roles=$this->permissions_model->get_allowed_roles_by_org($org_type);
		if($inherit)
		{
			//if inherit is set, then also display roles for those org types 'below' this one
			$new=array();
			$type_system= 1;
			$type_assoc = 2;
			$type_leag  = 3;
			$type_team  = 6;
			switch($org_type)
			{
			case $type_system:
				//all three for system
				$new=$this->permissions_model->get_allowed_roles_by_org($type_assoc);
				$new= array_merge($new,$this->permissions_model->get_allowed_roles_by_org($type_leag));
				$new= array_merge($new,$this->permissions_model->get_allowed_roles_by_org($type_team));
				
			break;
			case $type_assoc:
				$new=$this->permissions_model->get_allowed_roles_by_org($type_leag);
			break;
			
			case $type_leag:
			
				$new=$this->permissions_model->get_allowed_roles_by_org($type_team);
			break;
			
			}
			//merge to return both groups
			if(count($new)) $roles=array_merge($new,$roles);
			
		}
		
		echo json_encode($roles);
		
    }
	
	
	
	
	
    //from lu_org_type: league==3, team==6 
    public function json_team_roles()
    {
		$lu_org_type=6;
		$this->result->json($this->permissions_model->get_allowed_roles_by_org($lu_org_type));
		
    }
    public function json_league_roles()
    {
    //from lu_org_type: league==3, team==6 
		
		$lu_org_type=3;
		$this->result->json($this->permissions_model->get_allowed_roles_by_org($lu_org_type));
    }
    
    public function json_association_roles()
    {
    //from lu_org_type: league==3, team==6 
		
		$lu_org_type=2;//assoc is 2
		$this->result->json($this->permissions_model->get_allowed_roles_by_org($lu_org_type));
    }
    
    public function get_active_roles()
    {
        $userid = $this->permissions_model->get_active_user();
        $this->result->json($this->permissions_model->get_user_assignments($userid));
    }
    
    //created a function permissions/json_org_users, that takes org_id=___  , 
    public function json_org_users()
    {
		$org_id =(int)$this->input->get_post('org_id');
		if(!$org_id) $org_id=$this->permissions_model->get_active_org();//default to active org if none posted
		$plain='Y/m/d';
		$r=$this->permissions_model->get_org_users($org_id);
		
		foreach($r as &$row)
		{//formatting needed for ext.js grid row editor. or convert to empty string if null date
			$s=$row['effective_range_start'];
			$row['effective_range_start'] = ($s? date($plain,strtotime($s)) : null);
			$e=$row['effective_range_end'];
			$row['effective_range_end']   = ($e? date($plain,strtotime($e)) : null);
		}
		
		$r=$this->person_model->_format_people_extgrid($r);
		//effective_range_start
		$this->result->json_pag($r);	//already has been done in constructor://$this->load->library('result');	
    }
        
    public function json_org_users_distinct()
    {
		
		$this->result->json($this->permissions_model->get_org_users_distinct());
    }    
        
    /**
    * find username and DEcrypted password
    * for user attached to this email
    * 
    */
    public function post_reset_password()
    {
        $email = rawurldecode($this->input->post("email"));
        $username = $this->input->post("username");
        
        $result=$this->permissions_model->retrieve_password($username,$email);
		
        if($result!= '-1' && $result!= '-2' && $result!=null)
        {
        	//decode password, then email it
        	$password = $this->encrypt->decode($result,SSI_ENC_KEY);
            $data['subject']    = 'Spectrum Password Found';
           // $info['login']      = $username;
            $info['password']   = $password;
            $body               = $this->load->view('emails/userForgotPassword', $info, true);
            $this->load->library('email');
            $this->email->to($email);
            $this->email->subject($data['subject']);
            $this->email->message($body);
            $this->email->send();
             echo 'Your password has been found and sent to the email you provided at '.$email.".  This may take a few minutes to appear.";
        }
        else if($result=='-1')
            echo 'The username you provided was not found: '.$username;
        else if($result=='-2')
            echo 'The email you provided appears to be invalid, or was not saved under your user account: '.$email; 
    }
    
    /**
    * find the username in the system attached to given email
    * 
    */
    public function post_retrieve_username()
    {
        $email = rawurldecode($this->input->post("email"));
        $result = $this->permissions_model->retrieve_username($email);
      
        if($result && $result != null &&  $result!=-1)
        {
            $data['subject']    = 'Spectrum Username Recovery';
            $info['login']      = $result;
            $body               = $this->load->view('emails/userForgotUsername', $info, true);
            $this->load->library('email');
            $this->email->to($email);
            $this->email->subject($data['subject']);
            $this->email->message($body);
            $this->email->send();
             
             echo 'Your username has been found and sent to the email you provided at '.$email.".  This may take a few minutes to appear.";
        }
        else
            echo 'The email you provided appears to be invalid, or was not saved to any Spectrum account: '.$email; 
    }
    
    //facebook API
    public function json_bypass_login()
    {
        //$result=$this->permissions_model->bypass_login();
        $this->result->json("1");
    }    
    
    
    /* Random password Generator*/
    

    public function json_genRandomString()
    {
        $passLen    =$this->input->get_post("pass_len");
        //basic string alll owercase
        $characters = "abcdefghijklmnopqrstuvwxyz";
        $characters.=strtoupper($characters);//add uppecase
        $characters.='0123456789';//add digits
        $string ="";
        $str_len = strlen($characters)-1;
        for ($p = 0; $p < $passLen; $p++) 
        {
            $string .= $characters[mt_rand(0,$str_len )];
        }
        $this->result->success($string);            
    }

 }