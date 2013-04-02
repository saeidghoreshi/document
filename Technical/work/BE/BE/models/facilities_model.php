
<?php
require_once('./endeavor/models/entity_model.php');
class Facilities_model extends Entity_model 
{   
	public function __construct()
	{
		parent::__construct();
	}
	public function get_fac_venues()
    {
        $sql="  SELECT		 v.venue_id,v.facility_id,t.lu_descr , v.venue_type,v.venue_longitude,
         			  v.venue_latitude,v.venue_name,f.facility_name
         FROM 		  public.venue  v    		 
         INNER JOIN   lu_venue_type t   ON t.lu_id=v.venue_type  
         INNER JOIN   public.facility f ON v.facility_id=f.facility_id AND  v.deleted_flag='f' 	  
         ORDER BY 	  v.venue_name";        
        return $this->db->query($sql)->result_array();
    } 
    public function get_fac_entity_id($facility_id)
    {
		$sql = "SELECT entity_id FROM public.facility WHERE facility_id = ?";
		$result = $this->db->query($sql, array($facility_id));
		return $result->first_row()->entity_id;
    }

    
    //----------------------------------------------------------------------------------------------------------
    public function get_venue_types()
    {
        $sql="select lu_id ,lu_descr ,lu_id as venue_type,lu_descr  from public.lu_venue_type";
        return $this->db->query($sql)->result_array();
    }
    public function get_managing_orgs()
    {
        $sql = "SELECT entity_id, org_id, org_name FROM public.entity_org WHERE org_type = 9 AND deleted_flag='f' ";
        return $this->db->query($sql)->result_array();
    }
    //------------------------------------  FACILITIES Functions                                                                          
    public function get_facilities($dist=false)
    {     
        $a_u_id= $this->permissions_model->get_active_user();
    	$sql="
            SELECT 
            f.facility_id,
            f.facility_name,
            f.facility_longitude,
            f.facility_latitude,
            e.org_name,
            
            ea.address_id, a.address_street,
            a.address_city,
            r.region_id,
            r.region_abbr,
            c.country_id,
            c.country_name,
            lup.postal_value,
            
            f.entity_id, 
            '' icon,'' as icon_char ,
            public.get_venues_count(f.facility_id) venues_count,
            f.owned_by,
            f.created_by


            FROM public.facility as f  
                left JOIN public.entity_org e                        ON f.owned_by    =e.org_id 
                LEFT OUTER JOIN  public.entity_address ea            ON ea.entity_id = f.entity_id      and ea.is_active = 't'    
                LEFT OUTER JOIN  public.address a                    ON ea.address_id= a.address_id   and a.deleted_flag = 'f'
                LEFT OUTER JOIN  public.lu_address_postal lup        ON lup.postal_id= a.address_postal 
                LEFT OUTER JOIN  public.lu_address_region r          ON a.address_region= r.region_id 
                LEFT OUTER JOIN  public.lu_address_country c         ON a.address_country= c.country_id  
            WHERE     
                f.deleted_flag=false
        ";
        if(intval($dist) == -2)$sql.=" and f.created_by=".$a_u_id;
                                                         
        $search_criteria    =$this->input->get_post('query');
        if($search_criteria) 
                            $sql.= "AND lower(facility_name) like '%'||lower('{$search_criteria}')||'%' ";
        
        $result= $this->db->query($sql)->result_array();
        return $result;
    }                                                                       
    public function get_facilities_me($search_criteria,$a_u_id,$a_o_id)
    {
        return $this->db->query("select * from public.get_facilities_me(?,?,?,?,?) where lower(facility_name) like '%'||lower(?)||'%' or lower(address) like '%'||lower(?)||'%'",array($a_u_id,$a_o_id,'facilities','json_getfacilities','1',$search_criteria,$search_criteria))->result_array();
    }                                                                       
    public function new_facility($facility_name,$lat,$lng,$street,$city,$province,$country,$postalcode,$a_u_id,$a_o_id)
    {                              
        $result=$this->db->query("select * from public.new_facility(?,?,?,?,?,?,?,?,?,?)",array($facility_name,$lat,$lng,$street,$city,$province,$country,$postalcode,$a_u_id,$a_o_id))->result_array();
        return $result;
    }
    public function update_facility($facility_id,$facility_name,$lat,$lng,$street,$city,$province,$country,$postalcode,$a_u_id,$a_o_id)
    {
        $result=$this->db->query("select public.update_facility(?,?,?,?,?,?,?,?,?,?,?)",array($facility_id,$facility_name,$lat,$lng,$street,$city,$province,$country,$postalcode,$a_u_id,$a_o_id))->result_array();
        return $result;
    }
    public function update_facility_name($facility_id,$facility_name_new,$a_u_id,$a_o_id)
    {
        $res=$this->db->query("select * from public.update_facility_name(?,?,?,?)",array($facility_id,$facility_name_new,$a_u_id,$a_o_id))->result_array();
        return $res;
    }
    public function delete_facility($facility_id,$a_u_id,$a_o_id)
    {
        $result=$this->db->query("select public.delete_facility(?,?,?,?,?,?);",array($facility_id,$a_u_id,$a_o_id,"facilities","json_delete_facility",2))->result_array();
        return $result;
    }
    //-----------------------------------  VENUES Functions 
    
    public function get_venues($facility_id,$search_criteria)
    {                           
        $q="select * from public.get_venues(?) ";                           
        
        return $this->db->query($q,array($facility_id,$search_criteria))->result_array();
    }
       /**
    * @author sam
    * @return int facility_id
    * 
    * @param mixed $venue_id
    */
    public function get_fac_id_from_venue($venue_id)
    {
		$sql=" SELECT facility_id FROM public.venue WHERE venue_id=?";
		return $this->db->query($sql, array($venue_id))->first_row()->facility_id;		
    }
    /**
    * @author sam
    * jan 24 2011
    * get data for this specific venue. 
    * used by schedule wziard
    * 
    * @param mixed $venue_id
    */
    public function get_venue($venue_id)
    {
		$sql="SELECT	v.facility_id 
						,v.venue_longitude
         			  ,v.venue_latitude
         			  ,v.venue_name
         			  ,f.facility_name
         FROM 		  public.venue  v    		 
         
         INNER JOIN   public.facility f ON v.facility_id=f.facility_id   AND v.venue_id=?
         ";
        $r= $this->db->query($sql,array($venue_id))->result_array();
         if(!is_array($r) || count($r)==0) return array();
         //return first_row() if it exist
         return $r[0];
    }
    
    
    public function new_venue($venue_name,$venue_type_id,$facility_id,$lat,$lng,$a_u_id,$a_o_id)
    {                                                                                   
        $result=$this->db->query("select * from public.new_venue (?,?,?,?,?,?,?)",
        array($venue_name,$venue_type_id,$facility_id,$lat,$lng,$a_u_id,$a_o_id))->first_row()->new_venue;
        return $result;
    } 
    public function update_venue($venue_id,$venue_name,$venue_type_id,$facility_id,$lat,$lng,$a_u_id,$a_o_id)
    {          
        $result=$this->db->query("select * from public.update_venue (?,?,?,?,?,?,?,?)"
        ,array($venue_id,$venue_name,$venue_type_id,$facility_id,$lat,$lng,$a_u_id,$a_o_id))->first_row()->update_venue;
        return $result;
    } 
    public function delete_venue($venue_id,$a_u_id,$a_o_id)
    {
        return $this->db->query("select public.delete_venue(?,?,?,?,?,?);",
        						array($venue_id,$a_u_id,$a_o_id,"facilities","json_delete_venue",2))->first_row()->delete_venue;
   
    }
    public function update_venue_name($venue_id,$venue_name_new,$a_u_id,$a_o_id)
    {
        $result=$this->db->query("select * from public.update_venue_name(?,?,?,?)",array($venue_id,$venue_name_new,$a_u_id,$a_o_id))->result_array();
        return $result;
    }
    //----------------------------------------------------------------------------------------------------------
    
    
    
    
    
    
    
    
    
	/**
	* get stats of this venue
	* 
	* @param mixed $venue_id
	*/
    public function get_venuestats($venue_id)
    {
        $sql= "SELECT        x.venue_id, x.venue_name, att.venue_attribute, val.venue_value 
                FROM         public.venue x 
                INNER JOIN schedule.venue_attribute_value val    ON val.venue_id = x.venue_id 
                												 AND  x.venue_id = ? 
                												 AND x.deleted_flag = 'f' AND    val.deleted_flag = 'f' 
                INNER JOIN   schedule.lu_venue_attribute att        ON           val.venue_attribute_id = att.attribute_id  ";// AND ".USER_CAN_ACCESS."  ";        
        return $this->db->query($sql,$venue_id)->result_array();                
    }
    
    
    public function get_venue_name($venue_id)
    {
        $sql= "SELECT        x.venue_id, x.venue_name 
                FROM         public.venue x 
                where   x.venue_id = ?
      ";// AND ".USER_CAN_ACCESS."  ";        
        return $this->db->query($sql,$venue_id)->result_array();                
    }
    public function get_venue_details($venue_id)
    {
        $sql= "SELECT		 v.venue_id,v.facility_id , v.venue_name,f.facility_name
	         FROM 		  public.venue  v    		 
	         INNER JOIN   public.facility f ON v.facility_id=f.facility_id AND  v.deleted_flag='f'  AND v.venue_id=?	  
	         LIMIT 1";// AND ".USER_CAN_ACCESS."  ";        
        return $this->db->query($sql,$venue_id)->result_array();                
    }

    /**
    * switched to Haversine forumla, august 16 2011 
    * http://www.movable-type.co.uk/scripts/gis-faq-5.1.html
    * http://en.wikipedia.org/wiki/Haversine_formula
    *
    * OLD METHOD WAS: spherical law of cosines
    * input assumed to be in DEGREES
    * 
    * http://en.wikipedia.org/wiki/Great-circle_distance
    * http://en.wikipedia.org/wiki/Central_angle
    * http://en.wikipedia.org/wiki/Spherical_law_of_cosines
    * @param float $lat1
    * @param float $lon1
    * @param float $lat2
    * @param float $lon2
    */
    public function lat_long_distance_between_km( $lat1,  $lon1, $lat2, $lon2)
    {
    	$this->load->library('scheduler');
    	return $this->scheduler->lat_long_distance_between_km($lat1,  $lon1, $lat2, $lon2);/*
    	//radius is different depending on closeness to equator or poles, WE USE 
    	//an average (quadtratic mean // root mean square) , its good enough.
    	//more accurate way would be to estimate this based on the lat/lon given, using polar coordinates
    	$approx_radius_earth=6372.8;
    	
    	//echo "$lat1,  $lon1, $lat2, $lon2.\n";
    	
		//php trig functions MUST take arguments IN RADIANS
    	$deltalon = deg2rad($lon2 - $lon1);
		$deltalat = deg2rad($lat2 - $lat1);
		$lat1=deg2rad($lat1);
    	$lon1=deg2rad($lon1);
    	$lat2=deg2rad($lat2);
    	$lon2=deg2rad($lon2);
    	
    	//calculate the angle between the two rays
		$angle = pow(sin(  $deltalat/2  ),  2 )+ cos($lat1) * cos($lat2) * pow(sin($deltalon/2) ,2);
		
		//The min(1,..)  minimizes possible errors if the two points are very nearly antipodal
		//using this method, we only have an error of around 2 km
		$arc   = 2*asin( min(1,sqrt($angle))  );
		//arc length found using radius of earth
	    
		$arc_length_km = $approx_radius_earth * $arc;
	
		//echo "$lat1,  $lon1, $lat2, $lon2";var_dump($arc_length_km);
		return $arc_length_km;*/
    }
    public function get_region_store($country_id)
    {                                           
        //region_abbr replaced with region_name
        $result=$this->db->query("select region_id,/*region_name*/region_abbr region_name from public.lu_address_region  where country_id=? order by country_id,region_abbr ",array($country_id))->result_array();
        return $result;
    }
    public function get_country_store()
    {                                           
        $result=$this->db->query("select country_id,country_name from public.lu_address_country order by country_id")->result_array();
        return $result;
    }
}

