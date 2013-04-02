<?php
require_once('endeavor.php');   
  class Person extends Endeavor
  {
	  /**
  	  * 
  	  * 
  	  * @var person_model
  	  */
  	  public $person_model;
  	  
  	  /**
  	  * 
  	  * 
  	  * @var Permissions_model
  	  */
  	  public $permissions_model;
  	  

    function __construct()
    {
        parent::Controller();
        $this->load->model('endeavor_model');
        //$this->load->model('teams_model');
        $this->load->model('person_model');
        $this->load->model('permissions_model');

        $this->load->library('page');
        $this->load->library('input');   
        $this->load->library('result');
    }
    
    
    public function post_search_people()
    {
        $fname=rawurldecode($this->input->post('fname'));
        $lname=rawurldecode($this->input->post('lname'));
        
        $people = $this->person_model->get_people(true);

        
        //search existing people records, and return all those with a similar name
        $results = array();
        $phonemes =5;
        //$prefix='a';
        // switching from soundex to metaphone
        foreach($people as $p)
        {
        	$p_first = $p['person_fname'];
        	$p_last = $p['person_lname'];
        	if(is_numeric($fname) || is_numeric($lname))
        	{//if, for some crazy reason, search is a number, just compare
        	//since otherwise metaphone gives weird results
        		if($fname == $p_first || $lname==$p_last)
					$results[]=$p;
        	}
        	else  
        	{
				if(is_numeric($p_first) || is_numeric($p_last)) continue;//dont soundex numbers
				
        		if(    metaphone($fname,$phonemes) == metaphone( $p_first,$phonemes)
		            || metaphone($lname,$phonemes) == metaphone($p_last,$phonemes)  ) 
	                  $results[]=$p;
				
			}
            
        }
        $used_ids=array();
        $final=array();
        foreach($results as $r)
        {
        	$pid=$r['person_id'];
			if(isset($used_ids[$pid])) continue;
			
			$final[]=$r;
			$used_ids[$pid] = true;
        }
        
        
        $this->result->json($this->_format_people_extgrid($final));
    }
    public function post_search_users()
    {
    	//this could be eitehr from get or post
        $name=rawurldecode($this->input->post('search_name'));
        //if(!$name)
        	//$name=rawurldecode($this->input->get('search_name'));
        	
        $this->result->json($this->_search_users($name));
	}
	
	private function _search_users($name)
	{
		
        //$lname=rawurldecode($this->input->post('lname'));
        //var_dump($name);
        $people = $this->permissions_model->get_people_are_users();
        
        if($name)
        {
	        $name_array=explode(' ',$name);
	        $fname="";
	        $lname="";
	        
	        
	        foreach($name_array as $i=>$word)
	        {
				if($i==0)
				{
					$fname=$word;
				}
				else
				{
					$lname.=$word." ";
				}
	        }
		}
		else
		{
			$fname=rawurldecode($this->input->post('fname'));
			$lname=rawurldecode($this->input->post('lname'));
			
		}
		if(!$lname){$lname=$fname;}//in case they ONLY typed in a LAST name but no first
        //search existing people records, and return all those with a similar name
        $results = array();
        $phonemes =5;
		
        // switching from soundex to metaphone
        foreach($people as $p)
        {
        	$p_first=$p['person_fname'];
        	$p_last =$p['person_lname'];
        	if(is_numeric($fname) || is_numeric($lname))
        	{//if, for some crazy reason, search is a number, just compare
        	//since otherwise metaphone gives weird results
        		if($fname == $p_first || $lname==$p_last)
        		{
					$results[]=$p;
				}
        	}
        	else  
        	{
				if(is_numeric($p_first) || is_numeric($p_last)) continue;//dont metaphone numbers
				
        		if(     metaphone($fname,$phonemes) == metaphone($p_first,$phonemes)
		             || metaphone($lname,$phonemes) == metaphone($p_last ,$phonemes)  ) 
		        {
	                  $results[]=$p;
				}
				
			}
            
        }
        
        return $this->_format_people_extgrid($results);
    }
    
    public function json_people_users()
    {
		$role_id=$this->input->get_post('role_id');
		$org_id =$this->input->get_post('org_id');
		if(!$role_id)
			{$role_id=-1;}
		if(!$org_id)
			{$org_id=-1;}
		//search bar contents is in query, if searched
		$query = @$this->input->get_post('query');
		if($query)
		{
			$result = $this->_search_users($query);
		}
		else
		{
    		$result= $this->permissions_model->get_people_are_users();
			$result= $this->_format_people_extgrid($result);
		}
		//important to do this AFTER query search
		$filtered=array();
		if($role_id>0 && $org_id>0)
		{
			foreach($result as $r)
			{
				$user_id=$r['user_id'];
				$assn = $this->permissions_model->search_assignments($role_id,$org_id,$user_id);
				if(count($assn)) $filtered[]=$r;
			}
		}
		else
		{ //all of them
			$filtered=$result;
		}
		//format for paginator, etc
         $this->result->json_pag($filtered);
    
	}
    
    
    
    
    
    /**
    * specific formatting of dates and phone numbers for sencha grids/forms
    * 
    * @param mixed $result
    */
    public function _format_people_extgrid($result) 
    {
    	return $this->person_model->_format_people_extgrid($result);
    	/*$long_fmt="F j, Y; g:i a";
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

		return $result;*/
    }
                                  


    public function json_person()
    {
		$person_id=(int)$this->input->post('person_id');
		$this->result->json($this->person_model->get_person($person_id));
    }
    /**
    * calls same database function as 'post_update_person' except it always acts on the 
    * active logged-in user, and has no email parameter
    * 
    */
    public function post_update_active_person()
    {
		$user=$this->permissions_model->get_active_user();
        
		$birthdate = rawurldecode($this->input->get_post('person_birthdate'));
		if(!$birthdate) $birthdate=null;
		else $birthdate = date("Y-m-d",strtotime($birthdate));
		$first     = rawurldecode($this->input->get_post('person_fname'));
		$last      = rawurldecode($this->input->get_post('person_lname'));
		$gender     = rawurldecode($this->input->get_post('person_gender'));
		
		$u=$this->permissions_model->get_personid_by_user($user);
		$person_id=$u[0]['person_id'];
		$r= $this->person_model->update_entity_person($person_id,$first,$last,$birthdate,$gender,$user);
    	$response = array('success'=>true,'result'=>$r);
    	$this->result->json($response);
	}
    /**
    * calls same database function as 'post_update_person' except it always acts on the 
    * active logged-in user, and has no email parameter
    * 
    */	
	public function post_update_active_person_addr()
    {
		$org=$this->permissions_model->get_active_org();
		$user=$this->permissions_model->get_active_user();
		
		$u=$this->permissions_model->get_personid_by_user($user);
		$person_id=$u[0]['person_id'];
		$entity_id=(int)$this->person_model->get_entity_id_by_person($person_id);
        
        $address_street=$this->input->get_post('address_street');
        $address_city=$this->input->get_post('address_city');
        $postal_value=$this->input->get_post('postal_value');
        $postal_id=null;
        if($postal_value) $postal_id=$this->person_model->get_postal_id($postal_value);
        
        $region_abbr=$this->input->get_post('region_abbr');
        $region_id=null;
        if($region_abbr) $region_id=$this->person_model->get_region_id($region_abbr);
        
        
        $country_abbr=$this->input->get_post('country_abbr');
        $country_id=null;$canada=1;
        $home=$this->input->get_post('address_type');
        if(!$home)$home=1;
        if($country_abbr == 'CA' ||$country_abbr=='Canada') $country_id=$canada;

		$r=$this->person_model->insert_entity_address($user,$entity_id,$address_street,$address_city,$region_id,$country_id,$postal_id,$home,$org);

    	$this->result->success($r);
	}
    public function post_update_person()
    {
		$user=$this->permissions_model->get_active_user();
		$org=$this->permissions_model->get_active_org();
        
		$birthdate = rawurldecode($this->input->get_post('person_birthdate'));
		$first     = rawurldecode($this->input->get_post('person_fname'));
		$last      = rawurldecode($this->input->get_post('person_lname'));
		$gender     = rawurldecode($this->input->get_post('person_gender'));
		$email     = rawurldecode($this->input->get_post('email'));
		$person_id =(int)$this->input->get_post('person_id');
		if(!$gender) $gender=null;
		
		$postgress_fmt="Y-m-d" ;
		//echo $birthdate;
		//convert empty string,etc  to null, otherwise format for postgres
		$birthdate = ( !$birthdate || $birthdate=='null'? null : date( $postgress_fmt,strtotime($birthdate)) );
		//echo "\n".$birthdate ; return;
		 $s=$this->person_model->update_entity_person($person_id,$first,$last,$birthdate,$gender,$user);
		//email is attached to entity id, not directly on person record, so we need entity id

		//will output 0 if value is found in db, 1 if updated, -1 for error, as usual
		if( $email != -1 )//can be empty string or null
		{
			$used = $this->person_model->count_users_with_this_email_active($email,$user);
			if($used>0)
			{
				echo "Email address is already in use by another user.";
				return;
			}
			
			$entity_id=(int)$this->person_model->get_entity_id_by_person($person_id);
			$email_type=1;//fromlu_entity_contact
		
			 $this->person_model->insert_entity_contact($user,$entity_id,$email_type,$email,$org);//
		}
		
		echo $s;
		//}
		//}
    }
    
    /**
    * FOR ROSTER PERSON ONLY!!! THIS IS WHY THERE IS AN END DATE.  WILL CAUSE PROBLEMS IF USED OUTSIDE ROSTERS
    * 
    */
    public function json_update_person()
    {
        $person_id      = $this->input->get_post("person_id");
        $birthdate      = $this->input->get_post("person_birthdate");
        $first_name     = $this->input->get_post("person_fname");
        $last_name      = $this->input->get_post("person_lname");
        $person_gender      = $this->input->get_post("person_gender");
		if(!$person_gender)$person_gender=null;
        $start_date     = $this->input->get_post("start_date");
                        
    	//sometimes the row editor does not post the value, only the display:
    	//ex: they changed from M to F, so F is display icon, but data M is posted -> is error
        $rowEditorGender = $this->input->get_post('person_gender_icon');               
 
        if($rowEditorGender)
        {
        	//since this is an html of an image, for example 
        	///global_assets/silk/female.png
        	//we strpos to see if a substring exists or not
			if(strpos($rowEditorGender,'/female')) $person_gender='F';
			
			if(strpos($rowEditorGender,'/male')  ) $person_gender='M';
        }        
        
        if(!$birthdate)$birthdate=null;//do not parse date if its empty
        else $birthdate=date('Y-m-d',strtotime($birthdate));                                           
         
        $result=$this->person_model->update_person($person_id,$first_name,$last_name,$birthdate,$person_gender,$start_date);
        if($result<0 || $result===false) 
        	$this->result->failure($result);
        else 
            $this->result->success($result);
  
    } 
	  
  	public function post_update_active_person_contact()
    {
		$org=$this->permissions_model->get_active_org();
		$user=$this->permissions_model->get_active_user();
		
		$u=$this->permissions_model->get_personid_by_user($user);
		$person_id=$u[0]['person_id'];
		$entity_id=(int)$this->person_model->get_entity_id_by_person($person_id);
        
        $email=$this->input->get_post('email');
        $ct_email=1;//from lu_contact_type
		$ct_home=2;
		$ct_work=3;
		$ct_mobile=4;
		$success='';
		if($email)
		{
			$used = $this->person_model->count_users_with_this_email_active($email,$user);
			if($used>0)
			{
				$success.= "Email address is already in use by another user.";
				
			}
			else
			{
				
				$success.= $this->person_model->insert_entity_contact($user,$entity_id,$ct_email,$email,$org);
			}
			
		}
		//for phone, need ALL three values of each type to be valid
		//they are stored as strings
		$form=array();
        $form['home-ac']   =$this->input->get_post('home-ac');
        $form['home-pre']  =$this->input->get_post('home-pre');
        $form['home-num']  =$this->input->get_post('home-num');

		if($form['home-ac'] && $form['home-pre']&&$form['home-num'])
		{
			$num=$form['home-ac'] . $form['home-pre'].$form['home-num'];
		}
		else
		{
			$num=null;
		}
		
		$success.= $this->person_model->insert_entity_contact($user,$entity_id,$ct_home,$num,$org);
		
		$form['mobile-ac']   =$this->input->get_post('mobile-ac');
        $form['mobile-pre']  =$this->input->get_post('mobile-pre');
        $form['mobile-num']  =$this->input->get_post('mobile-num');
		if($form['mobile-ac'] && $form['mobile-pre']&&$form['mobile-num'])
		{
			$num=$form['mobile-ac'] . $form['mobile-pre'].$form['mobile-num'];
		}
		else
		{
			$num=null;
		}
		$success.= $this->person_model->insert_entity_contact($user,$entity_id,$ct_mobile,$num,$org);
		
		$form['work-ac']   =$this->input->get_post('work-ac');
        $form['work-pre']  =$this->input->get_post('work-pre');
        $form['work-num']  =$this->input->get_post('work-num');
        $form['work-ext']  =$this->input->get_post('work-ext');
		if($form['work-ac'] && $form['work-pre']&&$form['work-num'])//ext is optional
		{
			$num=$form['work-ac'] . $form['work-pre'].$form['work-num'].$form['work-ext'];
		}
		else
		{
			$num=null;			
		}
		$success.= $this->person_model->insert_entity_contact($user,$entity_id,$ct_work,$num,$org);

    	$response = array('success'=>true,'result'=>$success);
    	$this->result->json($response);
	}
	
	/**
	* @deprecated 
	* USE post_insert_update_person_new
	* 
	*/
	public function post_insert_update_person()
	{
		
		$success=array();
		$user=$this->permissions_model->get_active_user();
		$org=$this->permissions_model->get_active_org();
		$form=json_decode(rawurldecode($this->input->post('form_data')),true);
		//var_dump($form);
		foreach($form as $key=>$val)
		{
			if($val=='')
				$form[$key]=null;
		}
		if(!isset($form['person_id']) || !$form['person_id'] || $form['person_id']==null)
			$p_id=-1;
		else
			$p_id=$form['person_id'];
		//this isnt used but hey
		if(!isset($form['user_id']) || !$form['user_id'] || $form['user_id']==null)
			$u_id=-1;
		else
			$u_id=$form['user_id'];
		//these two are from dropdowns, with null as blank option
		if(   !isset($form['person_gender']) ||!$form['person_gender']  ||  $form['person_gender']=='null' )
			$form['person_gender']=null;
			
			
		if(!$form['person_fname']) $form['person_fname']=" ";
		if(!$form['person_lname']) $form['person_lname']=" ";
			
		//echo $form['person_gender'];
		if(!isset($form['region_abbr'])|| !$form['region_abbr']||$form['region_abbr']=='null')
			$form['region_abbr']=null;
		if(!isset($form['person_birthdate']) || $form['person_birthdate']=='null' )
			$form['person_birthdate']=null;
		
		
		
		$success['email_user'] = null;
			
		$person_id= $this->person_model->insert_person($user,$p_id,$form['person_fname'],$form['person_lname'],$form['person_gender'],$form['person_birthdate'],$org);
		$success['person_id']=$person_id;
		
		$entity_id=(int)$this->person_model->get_entity_id_by_person($person_id);
		if($form['postal_value'])
			$postal_id=$this->person_model->get_postal_id($form['postal_value']);
		else
			$postal_id=null;
		if($form['region_abbr'])
			$region_id=$this->person_model->get_region_id($form['region_abbr']);
		else
			$region_id=null;
		//if they are all null, do not insert
		$canada = 1;
		$home=$this->input->get_post('address_type');
		if(!$home)$home=1;
		 
		if($form['address_city']||$form['address_street'] || $postal_id|| $region_id)
		{
			$success['address']= $this->person_model->insert_entity_address($user,$entity_id,$form['address_street'],$form['address_city'],$region_id,$canada,$postal_id,$home,$org);
		}
		$ct_email=1;//from lu_contact_type
		$ct_home=2;
		$ct_work=3;
		$ct_mobile=4;
		$success['email']='';
		if($form['email'])
		{
 
				$success['email']= $this->person_model->insert_entity_contact($user,$entity_id,$ct_email,$form['email'],$org);
				if($success['email']==-5)
				{
					
				 	$success['email'] ='Email address is already in use by a different person named ';
				 	
					 $who = $this->person_model->who_has_this_email($form['email']);
					// var_dump($who);
				 	 $success['email'] .=  '"'. @$who[0]['person_fname']." ".@$who[0]['person_lname'].'"';
				 	 $success['email'] .= '.  Is this who you are trying to add?';
				 	 
				 	 $success['email_user'] = $who[0];
				}
		 
			
			
		}
		//for phone, need ALL three values of each type to be valid
		//they are stored as strings
		
		if($form['home-ac'] && $form['home-pre']&&$form['home-num'])
		{
			$num=$form['home-ac'] . $form['home-pre'].$form['home-num'];
		}
		else
		{
			$num=null;
		}
		$success['home_phone']= $this->person_model->insert_entity_contact($user,$entity_id,$ct_home,$num,$org);
		
		if($form['mobile-ac'] && $form['mobile-pre']&&$form['mobile-num'])
		{
			$num=$form['mobile-ac'] . $form['mobile-pre'].$form['mobile-num'];
		}
		else
		{
			$num=null;
		}
		$success['mobile_phone']= $this->person_model->insert_entity_contact($user,$entity_id,$ct_mobile,$num,$org);
		if($form['work-ac'] && $form['work-pre']&&$form['work-num'])//ext is optional
		{
			$num=$form['work-ac'] . $form['work-pre'].$form['work-num'].$form['work-ext'];
		}
		else
		{
			$num=null;			
		}
		$success['work_phone']= $this->person_model->insert_entity_contact($user,$entity_id,$ct_work,$num,$org);
		
		
		$success['user_login']=0;
		//if password is blank do not change it. just keep the old one
		//echo $form['login'].",".$form['pass'];
		if($form['login']&&$form['pass'])//if either is not given, leave the existing user/password alone
		{
			$this->load->library('encrypt');
			$enc=$pass = $this->encrypt->encode($form['pass'],SSI_ENC_KEY);
			$default_org=null;
			//todo: if they try to user a username already in use, no mesage is passed to user
			
			if($u_id&&$u_id != -1)
			{
				$success['user_login']= $this->permissions_model->update_login_password($u_id,$form['login'],$enc,$user);
			}
			else
			{
				$success['user_login']= $this->permissions_model->insert_user($user,$person_id,$default_org,$form['login'],$enc,$org);	
			}
				
		}

		$success['user_id']=$this->permissions_model->get_user_id_int_by_person($success['person_id']);
		
		//echo $success['user_login'];
		$this->result->json($success);
	}  
	
	public function post_insert_update_person_new()
	{
		$this->load->library('result');
		  
 		//init variables
		$success=array();
		$FOUND_ERROR=false;//no errors found yet
		 
		//decide if it is from front end, if pass is encrypted or not
		$front_end = $this->input->get_post('front_end');
		$is_encrypted = $this->input->get_post('is_encrypted');
		if($front_end && $front_end =='t')
		{
			$SYSTEM=1; 
			$this->permissions_model->set_active_org($SYSTEM);
			$this->permissions_model->set_active_user($SYSTEM);
		}

		$user = $this->permissions_model->get_active_user();
		$org  = $this->permissions_model->get_active_org();
	
		/**************************get all post input data************************************ */
		$login = rawurldecode($this->input->get_post('login'));
		$pass = rawurldecode($this->input->get_post('pass'));
		$birthdate = rawurldecode($this->input->get_post('person_birthdate'));
		$first     = rawurldecode($this->input->get_post('person_fname'));
		$last      = rawurldecode($this->input->get_post('person_lname'));
		$gender     = rawurldecode($this->input->get_post('person_gender'));
		$email     = rawurldecode($this->input->get_post('email'));
		$region_abbr     = rawurldecode($this->input->get_post('region_abbr'));
		$postal_value     = rawurldecode($this->input->get_post('postal_value'));
		$home = $this->input->get_post('address_type');
		$address_city     = rawurldecode($this->input->get_post('address_city'));
		$address_street     = rawurldecode($this->input->get_post('address_street'));
		//user 'form' to store phone numbrs
		$form=array();
		$form['home-ac']     = rawurldecode($this->input->get_post('home-ac'));
		$form['home-num']     = rawurldecode($this->input->get_post('home-num'));
		$form['home-pre']     = rawurldecode($this->input->get_post('home-pre'));
		$form['mobile-ac']     = rawurldecode($this->input->get_post('mobile-ac'));
		$form['mobile-num']     = rawurldecode($this->input->get_post('mobile-num'));
		$form['mobile-pre']     = rawurldecode($this->input->get_post('mobile-pre'));
		$form['mobile-ac']     = rawurldecode($this->input->get_post('mobile-ac'));
		$form['work-num']     = rawurldecode($this->input->get_post('work-num'));
		$form['work-pre']     = rawurldecode($this->input->get_post('work-pre'));
		$form['work-ext']     = rawurldecode($this->input->get_post('work-ext'));
		$p_id =(int)$this->input->get_post('person_id');
		$u_id =(int)$this->input->get_post('user_id');
		
		/***************handle nulls and general data formatting******************************/
		if(!$p_id )$p_id=-1;
 
		//this isnt used but hey
		if(!$u_id || $u_id < 0)$u_id=-1;
 
		//these two are from dropdowns, with null as blank option
		if(  !$gender )$gender =null;
		
		if(!$birthdate)$birthdate=null;	
		if(!$first) $first=" ";
		if(!$last) $last=" ";
			 
		if(!isset($form['region_abbr'])|| !$form['region_abbr']||$form['region_abbr']=='null')
			$form['region_abbr']=null;
		if(!isset($form['person_birthdate']) || $form['person_birthdate']=='null' )
			$form['person_birthdate']=null;
			
		/************entity_person *****************************************************************/
		 
		$person_id= $this->person_model->insert_person($user,$p_id,$first,$last,$gender,$birthdate,$org,$front_end);
		$success['person_id']=$person_id;
		
		/************entity address*********************************************************/
		//get entity id of newly created person
		$entity_id=(int)$this->person_model->get_entity_id_by_person($person_id);
		if($postal_value)
			$postal_id=$this->person_model->get_postal_id($postal_value);
		else
			$postal_id=null;
		if($region_abbr)
			$region_id=$this->person_model->get_region_id($region_abbr);
		else
			$region_id=null;
		$canada=1;
		if(!$home)$home=1;
		//if they are all null, do not insert
		if($address_city||$address_street|| $postal_id|| $region_id)
		{
			$success['address']= $this->person_model->insert_entity_address($user,$entity_id,$address_street,$address_city,$region_id,$canada,$postal_id,$home,$org);
		}
		else $success['address']=null;
		
		/***************entity contacts (email phone)****************************************/
		
		$ct_email=1;//from lu_contact_type
		$ct_home=2;
		$ct_work=3;
		$ct_mobile=4;
		$success['email']='';
		if($email)
		{
 			// this function returns -5 if the email is in use, or -1 if entity_id not found
			$success['email']= $this->person_model->insert_entity_contact($user,$entity_id,$ct_email,$email,$org);
 			if($success['email']<0)$FOUND_ERROR=true;
			
		}
		//for phone, need ALL three values of each type to be valid
		//they are stored as strings
		
		if($form['home-ac'] && $form['home-pre']&&$form['home-num'])
		{
			$num=$form['home-ac'] . $form['home-pre'].$form['home-num'];
		}
		else
		{
			$num=null;
		}
		$success['home_phone']= $this->person_model->insert_entity_contact($user,$entity_id,$ct_home,$num,$org);
		
		if($form['mobile-ac'] && $form['mobile-pre']&&$form['mobile-num'])
		{
			$num=$form['mobile-ac'] . $form['mobile-pre'].$form['mobile-num'];
		}
		else
		{
			$num=null;
		}
		$success['mobile_phone']= $this->person_model->insert_entity_contact($user,$entity_id,$ct_mobile,$num,$org);
		if($form['work-ac'] && $form['work-pre']&&$form['work-num'])//ext is optional
		{
			$num=$form['work-ac'] . $form['work-pre'].$form['work-num'].$form['work-ext'];
		}
		else
		{
			$num=null;			
		}
		$success['work_phone']= $this->person_model->insert_entity_contact($user,$entity_id,$ct_work,$num,$org);
		
		/***********************  user login password *************/
		$success['user_login']=0;
 
		if($FOUND_ERROR==false && $login&&$pass)//if either is not given, leave the existing user/password alone
		{
			$this->load->library('encrypt');
			
			if(!$is_encrypted && $is_encrypted !='t')//if not encrypted already (like from form)
			{ 
				if(strlen($pass) < 6)
				{
					$FOUND_ERROR=true;
					$success['user_login'] = -4;//error code for pwrod not strong enough
				}
				else
					$pass = $this->encrypt->encode($pass,SSI_ENC_KEY);
				
			} 
			$default_org=null;
 			
			
			if($FOUND_ERROR==false)
			{
				
				if($u_id && $u_id != -1)
				{
					//update 
					$success['user_login']= $this->permissions_model->update_login_password($u_id,$login,$pass,$user);
				}
				else
				{
					//insert 
					$success['user_login']= $this->permissions_model->insert_user($user,$person_id,$default_org,$login,$pass,$org);	
				}
			}
			
				
		}

		$success['user_id']=$this->permissions_model->get_user_id_int_by_person($person_id);
		 
		 /*********************** done work, handle result output*********/
		$this->result->json($success);
	}  
	
	
	public function get_user()
	{ 
 
		//get the user id posted
    	$user_id = $this->input->get_post('user_id');
		$user    = $this->permissions_model->get_user($user_id);
		
		$format =  $this->input->get_post('format');
		
		//if we are asekd to format it using this method, then do it and also get first row only
		if($format)  
		{
			$user = $this->_format_people_extgrid($user);
			if(count($user)) $user = $user[0];
		}
		
		$this->result->json($user);
	}
	
	public function get_person()
    {
    	$person_id=$this->input->get_post('person_id');
        $userid = $this->permissions_model->get_userid_by_person($person_id);
        
        
        
        
        $users=$this->person_model->_format_people_extgrid($this->permissions_model->get_user($userid[0]['user_id']));
        
		
        $phonetypes=array('mobile','home','work');

		// 'display_address'
		if(count($users)) { $user=$users[0];
		foreach($phonetypes as $type)
		{
			
		
			$user[$type.'_display']="";
			if($user[$type.'-ac'] && $user[$type.'-pre'] && $user[$type.'-num'])
			$user[$type.'_display'] = $user[$type.'-ac'] ."-".$user[$type.'-pre'] ."-". $user[$type.'-num'];
		 
		}
		$user['full_name'] = $user['person_fname']." ".$user['person_lname'];
		}
		else $user=array();
		
		$this->result->json($user);
    }
	  
  }
?>
