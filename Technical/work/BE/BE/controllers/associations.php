<?php
 require_once('endeavor.php');   
  class Associations extends Endeavor
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
	* @var associations_model
	*/
	public $associations_model;
	/**
	* 
	* 
	* @var websites_model
	*/
	public $websites_model;
			
	/**
	* 
	* 
	* @var permissions_model
	*/
	public $permissions_model;			
	/**
	* 
	* 
	* @var leagues_model
	*/
	public $leagues_model;
    function __construct()
    {
        parent::Controller();
        $this->load->model('endeavor_model');
        $this->load->model('person_model');
        $this->load->model('entity_model');
        $this->load->model('org_model');
        $this->load->model('associations_model');
        $this->load->model('permissions_model');
        $this->load->model('websites_model');
        $this->load->model('leagues_model');
        $this->load->library('page');
        $this->load->library('input');   
        $this->load->library('images');
        $this->load->library('result');   
    }
    private function load_window()
    {
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/inventory/");
        $this->window->set_js_path("/assets/js2/leagues/");   
    }
    ###{"Order":[1,0,0,0],"Shipping":[0,0,0,1]} 
   /* public function window_manageassociations($id=false)
    {
        $associations["ass_list"]= $this->associations_model->json_getassociations();   
        $associations["league_list"]= $this->associations_model->json_getleagues_list();   
        $associations["tournament_list"]= $this->associations_model->json_gettournaments_list();   
        
        $data['associations']=$this->load->view('associations/associations.associations.list.php',null,true);               
        $data['leagues']=$this->load->view('associations/associations.associations.leagues.php',$associations,true);               
        $data['tournaments']=$this->load->view('associations/associations.associations.tournaments.php',$associations,true);               
        $data['teams']=$this->load->view('associations/associations.associations.teams.php',$associations,true);               
        
        $this->load_window();
        $this->window->add_css('');
        $this->window->add_js('class.associations.associations.js');
        
         
        $this->window->set_header('Manage associations');
        $this->window->set_body($this->load->view('associations/associations.associations.main.php',$data,true));
        $this->window->set_footer($this->load->view('associations/associations.associations.footer.php',null,true));
        //if($id) $this->window->set_id($id);
        $this->window->json();
    }
    
*/
    
    public function json_get_entity_addresses()
    {
        $entity_id=$this->input->get_post("entity_id");
        $result= $this->associations_model->get_entity_addresses($entity_id);
        $this->result->json_pag($result);    
    }

   
    public function json_delete_entity_address()
    {
        $entity_id=$this->input->get_post("entity_id");
        $address_id=$this->input->get_post("address_id");

        
        $result= $this->associations_model->delete_entity_address($entity_id,$address_id);
        $this->result->success(  $result[0]["delete_entity_address"]);
    }
    public function json_update_entity_address()
    {
    	$org =$this->permissions_model->get_active_org();
    	$user=$this->permissions_model->get_active_user();
        $entity_id          =$this->input->get_post("entity_id");
        $address_type_id    =$this->input->get_post("address_type");
        $street             =$this->input->get_post("street");
        $city               =$this->input->get_post("city");
        $region             =$this->input->get_post("region");
        $country            =$this->input->get_post("country");
        $postalcode         =$this->input->get_post("postalcode");
        
        $result= $this->associations_model->update_entity_address($entity_id,$address_type_id,$street,$city,$region,$country,$postalcode);
        $this->result->success(  $result[0]["update_address"]);
        //echo $result;
    }
    public function json_get_complete_league_info($league_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //-------------------------------------------------------
        $result= $this->associations_model->get_complete_league_info($league_id,$a_u_id,$a_o_id);
        $this->result->json($result);
    }

    
    /**
    * used for both create AND update of a league.
    * this is a must
    * 
    */
    public function json_createnewleague()
    {   
    	$URL_IN_USE_ERROR=-5;
    	
    	$league_id      = (int)$this->input->get_post("league_id");
        $leaguename     =$this->input->get_post("league_name");
        $websiteprefix  =$this->input->get_post("websiteprefix");
        
        //replace illegal characters in prefix:
        $not_allowed=array('.',"\\","/",':','?','&','=');

		$websiteprefix=str_replace($not_allowed,'',$websiteprefix);
      
        $domainname     =$this->input->get_post("domainname");
        
        $new_url = $websiteprefix.'.'.$domainname;
		$result=array();
        if($league_id && $league_id!= -1)
        {        	
        	//find existing url
        	$old_url = $this->associations_model->get_league_url($league_id);
        	if($old_url != $new_url)
        	{
				//then see if we need to  change parking (with cpanel)
		        $count_url_in_use = $this->websites_model->count_url_in_use($new_url);
				if($count_url_in_use > 0)
		        {
		        	//if this happens, dont update the league at all
		        	$result['park']=$URL_IN_USE_ERROR;
					$this->result->failure($result);
					return;
		        }
				
	            $result['unpark']=$this->associations_model->domain_unpark($old_url);  
	            $result['park']  =$this->associations_model->domain_park($new_url);
			}
			
	        /*
				
				
        	}
	        $split_url = explode('.',$old_url);
	        
    		$old_prefix=array_shift($split_url);
    		$old_domain=implode('.',$splut_url);
    		*/
        	
        	//update existing
        	//will park and unpark domains for us
			$result['update']=$this->associations_model->update_league($league_id,$leaguename,$websiteprefix,$domainname);   
			$new_org_id = $this->leagues_model->get_org_from_league($league_id);	
			$result['edit']=$new_org_id;
        }
        else
        {
	        $count_url_in_use = $this->websites_model->count_url_in_use($new_url);
	        if($count_url_in_use > 0)
	        {
	        	//if this happens, dont even make the league at all
		        $result['park']=$URL_IN_USE_ERROR;
				$this->result->failure($result);
				return;
	        }
        	//create league
	        $result['create']= $this->associations_model->createnewleague($leaguename,$websiteprefix,$domainname);
	        
	        $new_org_id = $result["create"];
	        $debug=true;
	        
			$result['park']  =$this->associations_model->domain_park($new_url);
			
	        $today=date("Y-m-d");
	        
	        //now post the default welcome article from association
	        $content=$this->load->view("articles/default_league.php",null,true);
	        $this->websites_model->new_article("Welcome to Spectrum",$today,null,2,$content,$content,'f',$new_org_id);
	        
	        //depreciated: form slot for league email
	       // $email=$this->input->get_post('email');
		}
		
		$file_id = "file_upload";//the name of the field in the form 
        
		//for both create and update: process image file if it exists and is a valid image
    	if($this->images->type_is_valid_image($file_id))
	    {
	    	//only if its a valid image .  so do not upload .txt, .php,.exe, etc
        	//on success, the upload function returns the modified file name
			$result['upload']= $this->org_model->upload($file_id,ORG_LOGO_BASEPATH,$new_org_id);
			if($result['upload'])//which we then save in the entity_org table
				{$result['upload_id']=$this->org_model->update_entity_org_logo($new_org_id,$result['upload']);}
        }
        else $result['upload']=false;
 
        $this->result->success($result); 
    }
 
    public function json_delete_league()
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //-------------------------------------------------------
        $league_id=(int)$this->input->get_post("league_id");
        
        
        if($league_id)
        {
			
	        
	        $result= $this->associations_model->delete_league($league_id,$a_u_id,$a_o_id);
	        $this->result->json($result);        
    
    	
    	
    	}
    	else
    	{
			
	        $league_id_array=json_decode($this->input->get_post("league_id_array"));
	        $r=array();
			foreach($league_id_array as $league_id)
			{
				$r[]=$this->associations_model->delete_league($league_id,$a_u_id,$a_o_id);
			}
			
			$this->result->json($r);
    	}	
    	
    	
	}
    public function json_update_league_name()
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //-------------------------------------------------------
        $league_id=$this->input->get_post("league_id");
        $league_name_new=$this->input->get_post("league_name_new");
        
        $result= $this->associations_model->update_league_name($league_id,$league_name_new,$a_u_id,$a_o_id);
        $this->result->json($result);    
    }

                                  
    public function json_get_users()
    {                                                 
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //-------------------------------------------------------
        $result= $this->associations_model->get_users($a_u_id,$a_o_id);
        $this->result->json($result);    
    }
    public function json_get_users_2()
    {                                                 
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //-------------------------------------------------------
        $result= $this->associations_model->get_users($a_u_id,$a_o_id);
        $this->result->json_pag($result);
    }
    /**
    * DEPRECIATED
    * @deprec true
    * use league controller versoin
    * 
    */
    public function json_get_leagues()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        
        //-------------------------------------------------------
        
        $search_criteria= $this->input->get_post("query");
        if(!$search_criteria)$search_criteria='';
        $active= $this->input->get_post("active");  //sends true or false whether or not check expiry date  OR all whech passes the condition
        if(!$active) $active='all';
        //-------------------------------------------------------
        $data=$this->associations_model->get_leagues($search_criteria,$active,$a_u_id,$a_o_id);
        $this->result->json_pag($data);
    }
    public function json_get_league_users()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //-------------------------------------------------------          
        $search_criteria='';
        $search_criteria= $this->input->get_post("query");
        $league_id= $this->input->get_post("league_id");  
        //-------------------------------------------------------
        $data=$this->associations_model->get_league_users($search_criteria,$league_id,$a_u_id,$a_o_id);
        $this->result->json_pag($data);
    }
 

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    //manage assoc window
    public function window_createassociation($id=false)
    {   
        
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/");
        
        
        
        $this->window->set_js_path("/assets/js/models/");  
        $this->window->add_js('assoc.js'); //load data models before grid
         
        $this->window->add_js('users.js');   
        $this->window->add_js('user_role.js');  
        $this->window->add_js('currency.js');  
        $this->window->add_js('domain.js');  
		
		//finance components
        $this->window->set_js_path("/assets/js/components/finance/"); 
        $this->window->add_js('grids/currency.js'); 
        $this->window->add_js('forms/currency.js'); 
        $this->window->add_js('windows/currency.js'); 
       
                //files for org users from user componnet
        $this->window->set_js_path("/assets/js/components/users/"); 
       
        $this->window->add_js('forms/user_person.js');     
        $this->window->add_js('forms/org_roles.js');     
        $this->window->add_js('windows/users.js');     
        $this->window->add_js('windows/roles.js');     
        $this->window->add_js('grids/org_roles.js'); 
        $this->window->add_js('grids/org_users.js'); 
        
        //now the main assoc files
        $this->window->set_js_path("/assets/js/components/association/");   
         
        $this->window->add_js('forms/domain.js'); 
        $this->window->add_js('forms/createedit_assoc.js'); 
        $this->window->add_js('windows/domain.js');  
        $this->window->add_js('windows/createedit_assoc.js');  
        $this->window->add_js('grids/assoc.js'); 
        $this->window->add_js('grids/domain.js'); 
        $this->window->add_js('controller.js');
        $this->window->set_header('Manage Associations');
        $this->window->set_body($this->load->view('associations/manage.php',null,true));
        
        $this->window->json();
    }
    
    public function window_help($id=false)
    {                         
        $this->load_window();
        $this->window->add_css('');
        $this->window->add_js('class.associations.help.js'); 
        $this->window->set_header('Help');
        $this->window->set_body($this->load->view('associations/associations.associations.help.php',null,true));
        $this->window->set_footer('');
        //if($id) $this->window->set_id($id);
        $this->window->json();
    }   
  

    public function Email($from,$to,$cc,$bcc,$subject,$message,$assoc_name,$sender)
    {
        //depreciated
    } 
    
    //Get Functions
    public function json_getassociations()
    {
    	//get all assoc inside/owned by this parent org
        $result= $this->associations_model->get_associations($this->permissions_model->get_active_org());  
        echo $this->result->json_pag($result);
    }
    public function json_getleagues_list()
    {
        $result= $this->associations_model->json_getleagues_list();   
        $this->result->json($result);
    }
    public function json_gettournaments_list()
    {
        $result= $this->associations_model->json_gettournaments_list();   
        $this->result->json($result);
    }  
    public function json_getleagues()
    {
        $parent_id=$this->input->get('parent_id');
        $result= $this->associations_model->json_getleagues($parent_id);  
        $this->result->json($result);
    }
    public function json_gettournaments()
    {
        
        $parent_id=$this->input->get('parent_id');
        $result= $this->associations_model->json_gettournaments($parent_id);   
        $this->result->json($result);
    }
    public function json_getteams()
    {
       $parent_league_name=$this->input->get('parent_league_name');
       $parent_tournament_name=$this->input->get('parent_tournament_name');
       
       $result= $this->associations_model->json_getteams($parent_league_name,$parent_tournament_name);
        
       $this->result->json($result);
        
    }
    public function get_loginpage()
    {
        $result['result']=$this->load->view('associations/associations.associations.loginpage.php',null,true);               
        $this->result->json($result);
    }
    
    
    public function json_new_association()
    {
        $asn_name=$this->input->get_post("association_name");
        $website=$this->input->get_post("website");
        if(!$website)$website=null;//change zero or false to null for db
        $asn_id  =(int)$this->input->get_post("association_id");
 
        $result=array();
        $creator = (int)$this->permissions_model->get_active_user();
        $org = (int)$this->permissions_model->get_active_org();
        $parent_entity_id=$this->org_model->get_entity_id_from_org($org);
        if(!$asn_id || $asn_id<0)
        {
        	$new_org_id= $this->associations_model->new_association($asn_name,$parent_entity_id,$org,$creator,$website);
        	//assign this association tothe defalut domain, currenlty is 'playerspectrum.com'
        	$this->org_model->insert_domain($new_org_id,DEFAULT_LEAGUE_DOMAIN,$creator,$org);
		}
        else 
        {
        	$new_org_id= $this->associations_model->update_association($asn_id,$asn_name,$website,$creator);
		}
        
        $result['save']=$new_org_id;
        //activate org, which means GETSTARTED is skipped completely.
        //comment out this line to ACTIVATE the getstarted screen
        $this->permissions_model->set_active_org_valid('t',$new_org_id);
        
        $entity_id = $this->org_model->get_entity_id_from_org($new_org_id);
        //this next part of code copy/pasted from createnewleague
        //TODO: make some sort of org_logo_process function to avoid code duplication
        $file_id = "file_upload";//the name of the field in the form 
        
        //$new_org_id=$this->associations_model->org_id($asn_id);
        
        
		//either way, do the image file if create or update
    	if($this->images->type_is_valid_image($file_id))
	    {//only if its a valid image .  so do not upload .txt, .php,.exe, etc
 
        	//on success, the upload function returns the modified file name
			$result['upload']= $this->org_model->upload($file_id,ORG_LOGO_BASEPATH,$new_org_id);
			 
			
			if($result['upload'])//which we then save in the entity_org table
			{ 
				$this->org_model->update_entity_org_logo($new_org_id,$result['upload']);
			}
        }
        
        //assoc address
        $street=$this->input->get_post('address_street');
        $postal_code = $this->input->get_post('postal_value');
        $city = $this->input->get_post('address_city');
        $region = $this->input->get_post('region_abbr');
        if($region=='null') $region=null;
        $country_abbr = $this->input->get_post('country_abbr');
        if($country_abbr=='null') $country_abbr=null;
        
        $country_id=null;$canada=1;$home=$this->input->get_post('address_type');
        if($country_abbr == 'CA' ||$country_abbr=='Canada') $country_id=$canada;
        
        $region_id=null;
        if($region)$region_id=$this->person_model->get_region_id($region);
        $addr_type=2;//type == 2 for home address, from lu_address_type
        
        $postal_id=null;
        if($postal_code != null)
            $postal_id = $this->permissions_model->get_postal_id($postal_code);
        
        if($street != null || $city != null || $country_id != null || $region != null || $postal_id!= null)
        $address_id = $this->entity_model->insert_entity_address($creator,$entity_id,
        		$street,$city,$region_id,$country_id,$postal_id,$addr_type,$org);
        
        
        $this->result->success($result);
        
    }
    
    public function post_delete_assoc()
    {
		
        $asn_id  =(int)$this->input->get_post("association_id");
        $user    =(int)$this->permissions_model->get_active_user();
        echo $this->associations_model->delete_assoc($asn_id,$user);
    }
    
    
    
    
    
    public function json_league_season_assign()
    {
        $season_name=$this->input->post("season_name");
        $league_id=$this->input->post("league_id");
        $sdate=$this->input->post("sdate");
        $edate=$this->input->post("edate");
        
        $this->result->json($this->associations_model->league_season_assign($season_name,$league_id,$sdate,$edate));
    }
    public function json_league_season_reg()
    {
        $this->result->json($this->associations_model->league_season_reg());
    }
    public function json_get_league_season()
    {
        $this->result->json($this->associations_model->get_league_season());
    }
    public function json_runregistrationprocess()
    {
        $this->result->json($this->associations_model->runregistrationprocess());
    }
    public function json_new_form()
    {
        $form_name=$this->input->POST("form_name");
        $this->result->json($this->associations_model->new_form($form_name));
          
    }
    public function json_get_associations()
    {
        $this->result->json( $this->associations_model->get_associations());    
    }
    public function json_get_form_fields()
    {
        $form_id=$this->input->get("form_id");
        $this->result->json($this->associations_model->get_form_fields($form_id));
    }
    public function json_add_form_field()
    {
        $form_id=$this->input->post("form_id");
        $field_name=$this->input->post("field_name");
        $field_code=$this->input->post("field_code");
        $field_type=$this->input->post("field_type");
        $field_required=$this->input->post("field_required");
        $field_value_type=$this->input->post("field_value_type");
        $field_value_strict=$this->input->post("field_value_strict");
        $this->result->json($this->associations_model->add_form_field($form_id,$field_name,$field_code,$field_type,$field_required,$field_value_type,$field_value_strict));       
        
    
    }
    public function json_save_league_season_reg()
    {
       $season_name = $this->input->post("season_name");
       $form_name   = $this->input->post("form_name");
       $start_date  = $this->input->post("start_date");
       $end_date    = $this->input->post("end_date");
       $deposit_due = $this->input->post("deposit_due");
       $total_due   = $this->input->post("total_due");
       $this->result->json($this->associations_model->save_league_season_reg($season_name,$form_name,$start_date,$end_date,$deposit_due,$total_due));       
    }
    public function json_form_management()
    {
        $this->result->json($this->associations_model->form_management(1));
    }
   
    public function json_get_deposit_params()
    {
        $funcname=$this->input->post("funcname");
        $org_id=$this->input->post("org_id");
        
    }
    
    
 
    public function json_owned_domains()
    {
    	$org_id=(int)$this->input->get_post("org_id");
    	//$org_id=$this->associations_model->org_id($assc_id);
		$this->result->json( $this->org_model->getDomainNames($org_id));
    }
	/**
	* keyword new may not be insert. if domain exists already , will only
	* assign to this org.
	* otherwise will create
	* @author sam
	* 
	* 
	*/
	public function post_new_domain()
	{
    	//$a_id=(int)$this->input->get_post("association_id");
    	$org_id=$this->input->get_post("org_id");
    	 
    	$domain=$this->input->get_post("domain");
    	$u=$this->permissions_model->get_active_user();
    	$org=$this->permissions_model->get_active_org();
		$result= $this->org_model->insert_domain($org_id,$domain,$u,$org);
		$this->result->success($result); 
	}
	
	public function post_delete_domain()
	{
    	$id=(int)$this->input->get_post("id");
    	$org_id=(int)$this->input->get_post("org_id");
 
    	 
		echo $this->org_model->delete_domain($id,$org_id);
	}
	
	public function post_domain_valid()
	{
    	$id=(int)$this->input->get_post("id");
    	$act=$this->input->get_post("is_active");
		
		$act = ($act=='t') ? 'f' : 't';
		
		echo $this->org_model->update_domain_active($id,$act);
	}
	
    
}

