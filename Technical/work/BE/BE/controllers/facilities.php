<?php
require_once('endeavor.php');   
class Facilities extends Endeavor
{
	
	/**
	* 
	* 
	* @var facilities_model
	*/
	public $facilities_model;
	public $org_model;
    
    function __construct()
    {
        //parent::Controller();
        parent::__construct();
        $this->load->model('endeavor_model');
        $this->load->model('facilities_model');
        $this->load->model('permissions_model');
        $this->load->model('org_model');
        $this->load->library('page');
        $this->load->library('input');   
        $this->load->library('result');   
    }
    private function load_window()
    {
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/inventory/");
        $this->window->set_js_path("/assets/js/");   
    }
    //-------------------------------------------------------------------------
    public function window_managevenfac()
    {   
            
        $this->load_window();
        $this->window->set_body($this->load->view('facilities/facilities.main.php',null,true));
        $this->window->add_css('');                 
        
        
        $this->window->add_js('components/facilities/grids/spectrumgrids.facility.js');
        $this->window->add_js('components/facilities/grids/spectrumgrids.venue.js');
        
        $this->window->add_js('components/facilities/controller.js');
        $this->window->add_js('components/facilities/windows/spectrumwindow.facilities.js');
        $this->window->add_js('components/facilities/forms/forms.js');
        $this->window->add_js('components/facilities/toolbar.js');
                
        $this->window->set_header('Facilities');
        $this->window->json();        
    }                                                      
    //-------------------------------------   FACILITIES Functions
    public function json_get_managing_orgs()
    {
        $data= $this->facilities_model->get_managing_orgs();
        $this->result->json($data);
    }
    public function json_get_venuetype()
    {                                 
        $result=$this->facilities_model->get_venue_types();
        $this->result->json($result);                         
    }
    public function json_get_venuetype_store()
    {                                 
        $result=$this->facilities_model->get_venue_types();
        $this->result->json_pag_store($result);
    }
    public function json_get_facilities()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------   
        $dist       = (float)$this->input->get_post("dist");
        
        $result     = $this->facilities_model->get_facilities($dist);
        
        $i=0;
        $active_address=array();
        if( $dist>0)
        {
            $active_entity_id= $this->org_model->get_entity_from_org($a_o_id);
            $active_address  = $this->entity_model->get_entity_address($active_entity_id);
        }
        $final_result   =array();
        foreach($result as $r=>$v)
        {
            $result[$r]["icon"]     ="<img height=17px width=10px src='assets/images/Markers/marker".chr(65+$i).".png' />";
            $result[$r]["icon_char"]=chr(65+$i);
            $i++;
            if(!$dist || $dist==-1/*All*/ || $dist==-2/*My Facilities*/)
            {
            	$final_result[]=$result[$r];
                continue;
            }
            else
            {
                //check distance
                $f_lat = (float)$v['facility_latitude' ];
                $f_lng = (float)$v['facility_longitude'];
                $btw=0;
                if(count($active_address))//if org has  address
                {
                    $my_lat = (float)$active_address[0]['address_lat'];
                    $my_lng = (float)$active_address[0]['address_lon'];
                
                    if($my_lat && $my_lng)
                        $btw = $this->facilities_model->lat_long_distance_between_km($f_lat,$f_lng,$my_lat,$my_lng);
                }
                if($btw < $dist )
                    $final_result[]=$result[$r];
            } 
        }     
        $this->result->json_pag($final_result);
    }
    public function json_getEntityAddress()
    {
        $a_e_id     = $this->permissions_model->get_active_entity();
        $address    = $this->entity_model->get_entity_address($a_e_id);
        
        $this->result->success(  $address);
    }
    public function json_get_facilities_store()
    {
        $result=$this->facilities_model->get_facilities();  
        $i=0;
        foreach($result as $r=>$v)
        {
            $result[$r]["icon"]="<img height=17px width=10px src='assets/images/Markers/marker".chr(65+$i).".png' />";
            $result[$r]["icon_char"]=chr(65+$i);
            $i++;
        }        
        $this->result->json_pag_store($result);
    }
    
    public function post_save_update_facility()
    {
        $a_u_id= (int)$this->permissions_model->get_active_user(); 
        $a_o_id= (int)$this->permissions_model->get_active_org();
        //--------------------------------
        $facility_id        =$this->input->post("facility_id");
        $facility_name      =$this->input->post("facility_name");
        $street             =$this->input->post("address_street");
        $city               =$this->input->post("address_city");
        $region             =$this->input->post("region_abbr");
        $country            =$this->input->post("country_abbr");
        $postalcode         =$this->input->post("postal_value");
        $lat                =$this->input->post("facility_latitude");
        $lng                =$this->input->post("facility_longitude");
        
        $result=$this->facilities_model->update_facility($facility_id,$facility_name,$lat,$lng,$street,$city,$region,$country,$postalcode,$a_u_id,$a_o_id);
        $this->result->success(  $result[0]["update_facility"]);
    }
    public function json_update_facility_name()
    {
        $a_u_id= (int)$this->permissions_model->get_active_user(); 
        $a_o_id= (int)$this->permissions_model->get_active_org();
        //--------------------------------
        $facility_id        =$this->input->post("facility_id");
        $facility_name_new  =$this->input->post("facility_name_new");
        
        $result=$this->facilities_model->update_facility_name($facility_id,$facility_name_new,$a_u_id,$a_o_id);
        $this->result->success(  $result[0]["update_facility_name"]);
    }
    public function post_save_new_facility()
    {
        $a_u_id  = $this->permissions_model->get_active_user();
    	$a_o_id = $this->permissions_model->get_active_org();
        //--------------------------------
    	$facility_name      =$this->input->post("facility_name");
        $street             =$this->input->post("address_street");
        $city               =$this->input->post("address_city");
        $region             =$this->input->post("region_abbr");
        $country            =$this->input->post("country_abbr");
        $postalcode         =$this->input->post("postal_value");
        $lat                =$this->input->post("facility_latitude");
        $lng                =$this->input->post("facility_longitude");
                                                                   
        $result = $this->facilities_model->new_facility($facility_name,$lat,$lng,$street,$city,$region,$country,$postalcode,$a_u_id,$a_o_id);
        $this->result->success(  $result[0]["new_facility"]);
    }
    public function json_delete_facility()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //---------------------------------
        $facility_id=$this->input->get_post("facility_id");
        $result=$this->facilities_model->delete_facility($facility_id,$a_u_id,$a_o_id);
        $this->result->success(  $result[0]["delete_facility"]);
    }
    
    //-------------------------------------------------  VENUES Functions
    public function json_get_venues()
    {   
        $search_criteria=$this->input->get_post('query');
        //-------------------------------------------------------                            
        $facility_id=(int)$this->input->get_post('facility_id');
        if(!$facility_id)$facility_id=-1;
        
        $result=$this->facilities_model->get_venues($facility_id,$search_criteria);      
        for($i=0;$i<count($result);$i++)
        {
            $result[$i]["icon"]="<img height=17px width=10px src='assets/images/Markers/marker".chr(65+$i).".png' />";
            $result[$i]["icon_char"]=chr(65+$i);
        }         
        $this->result->json_pag($result); 
    }
    
    public function json_venues_by_fac()
    {
        $facility_id    =(int)$this->input->post("facility_id");
		$this->result->json($this->facilities_model->get_venues($facility_id,''));
		
    }
    public function post_save_new_venue()
    {
        $a_u_id = (int)$this->permissions_model->get_active_user(); 
        $a_o_id = (int)$this->permissions_model->get_active_org();
        //-----------------------------------
        $venue_name     =$this->input->post("venue_name");
        $venue_type_id  =$this->input->post("venue_type_id");
        $facility_id    =$this->input->post("facility_id");
        $lat            =$this->input->post("venue_latitude");
        $lng            =$this->input->post("venue_longitude");
        
        //if venue id is given, its an update, otherwise its a create
        $venue_id       =(int)$this->input->post("venue_id");

        if($venue_id)
        	$result=$this->facilities_model->update_venue($venue_id,$venue_name,$venue_type_id,$facility_id,$lat,$lng,$a_u_id,$a_o_id);
        else
        	$result=$this->facilities_model->new_venue($venue_name,$venue_type_id,$facility_id,$lat,$lng,$a_u_id,$a_o_id);
        $this->result->success(  $result);
    }
    public function json_update_venue_name()
    {
        $a_u_id= (int)$this->permissions_model->get_active_user(); 
        $a_o_id= (int)$this->permissions_model->get_active_org();
        //--------------------------------
        $venue_id        =(int)$this->input->post("venue_id");
        $venue_name_new  =rawurldecode($this->input->post("venue_name_new"));
        
        $result=$this->facilities_model->update_venue_name($venue_id,$venue_name_new,$a_u_id,$a_o_id);
        $this->result->success(  $result[0]["update_venue_name"]);
    }
    public function json_delete_venue()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //-----------------------------------
        $venue_id=(int)$this->input->get_post('venue_id');
        $result=$this->facilities_model->delete_venue($venue_id,$a_u_id,$a_o_id);
        $this->result->success(  $result);
    }
    /*
    public function post_save_update_venue()
    {
    	$a_u_id = (int)$this->permissions_model->get_active_user(); 
    	$a_o_id = (int)$this->permissions_model->get_active_org();
        //-----------------------------
        $venue_id       =$this->input->post("venue_id");
        $venue_name     =$this->input->post("venue_name");
        $venue_type_id  =$this->input->post("venue_type_id");
        $facility_id    =$this->input->post("facility_id");
        $lat            =$this->input->post("venue_latitude");
        $lng            =$this->input->post("venue_longitude");
        
        $result=$this->facilities_model->update_venue($venue_id,$venue_name,$venue_type_id,$facility_id,$lat,$lng,$a_u_id,$a_o_id);
        echo json_encode(array('success'=>true,'result'=>$result[0]["update_venue"]));
    }*/
    //------------------------------------------------------------------------------------------------------------
                                 
    
    public function json_fac_venues()
    {
        $this->result->json($this->facilities_model->get_fac_venues());
    }
     private function parse_googlemaps_address($str_address)
    {
        $delim=",";
        $array_temp = explode($delim,$str_address);
        
        $array_address = array();
        $sp=" ";
        $prov_postal = explode($sp,$array_temp[2]);
        
        $array_address['street']=$array_temp[0];
        $array_address['city']= str_replace(" ","",$array_temp[1]);
        $array_address['province']=$prov_postal[1];
        $array_address['postal_code']=$prov_postal[2]." ".$prov_postal[3];
        $array_address['country']=str_replace(" ","",$array_temp[3]);
        
        return $array_address;
    }
    public function post_save_new_org()
    {
        $a_u_id = (int)$this->permissions_model->get_active_user(); 
        $a_o_id = (int)$this->permissions_model->get_active_org();
        //----------------------------------------------------------
        $name = rawurldecode($this->input->post('name'));

        //LU_ORG_TYPE is 9 for this specific managing facility org
        echo $this->org_model->insert_entity_org($name,$a_o_id,$a_u_id,9);
    }
    public function json_get_region_store()
    {                              
        $country_id = $this->input->get_post("country_id");                                   
        $result     = $this->facilities_model->get_region_store($country_id);
        $this->result->json_pag_store($result);
    }
    public function json_get_country_store()
    {                                       
        $result     = $this->facilities_model->get_country_store();
        $this->result->json_pag_store($result);
    }
    
}
?>
