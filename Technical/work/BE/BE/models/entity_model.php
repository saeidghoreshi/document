<?php
require_once('./endeavor/models/endeavor_model.php');
require_once('./endeavor/models/entityfinance_model.php');
class Entity_model extends Endeavor_model
{
	
	private $ext = array();
	
	///// MAGIC METHODS ///////////////////////////////////////////////
	
	public function __construct()
	{
		parent::__construct();
		$this->ext[] = new EntityFinance_model();
	}
	
	/**
	* Looks for additional classes used for extending. Called magically.
	* 
	* @param mixed $method
	* @param mixed $params
	* @return mixed
	*/
	public function __call($method, $params)
	{
		foreach($this->ext as $class)
		{
		   if (method_exists($class, $method))
		   {
			  return call_user_func_array(array($class, $method), $params);
		   }
		}
	}
	
	///// ENTITY METHODS ///////////////////////////////////////////////

    
    /**
    * @author sam
    * 
    * updated on nov 24 to take optional address id parameter, will filter to only that address
    * otherwise if not given, will get all for the ent
    * 
    * @param mixed $entity_id
    * @param mixed $addr_id
    * @return array
    */
    public function get_entity_address( $entity_id,$addr_id=null,$addr_type=null)
	{
		$params=array($entity_id);
		$sql="SELECT    ea.entity_id ,
						ea.address_type,
						at.type_name,
						ea.address_id, 
						ea.entity_address_id,
						a.address_street,
						a.address_lat,
						a.address_id,
						a.address_lon,
						a.address_city,   
						a.address_country, 
						a.address_region, 
						luc.country_abbr ,  
						lur.region_abbr, 
						lup.postal_value ,
						cm1.value AS email,
						cm2.value AS p_home, 
						cm3.value AS p_work,
						cm4.value AS p_mobile  
			FROM        public.entity_address ea       
			INNER JOIN public.lu_address_type at ON at.type_id = ea.address_type
			LEFT OUTER JOIN  public.address a                    ON ea.address_id= a.address_id   			
			LEFT OUTER JOIN  public.lu_address_postal lup        ON lup.postal_id= a.address_postal 
 			LEFT OUTER JOIN  lu_address_country luc ON luc.country_id=a.address_country 
			LEFT OUTER JOIN  lu_address_region lur  ON lur.region_id=a.address_region
			LEFT OUTER JOIN  public.entity_contact ec1           ON ec1.entity_id = ea.entity_id          AND ec1.contact_type = 1   
			LEFT OUTER JOIN  public.contact_method cm1           ON cm1.contact_method_id = ec1.contact_method_id  AND   ec1.is_active = 't'
			LEFT OUTER JOIN  public.entity_contact ec2           ON ec2.entity_id = ea.entity_id          AND ec2.contact_type = 2 
			LEFT OUTER JOIN  public.contact_method cm2           ON cm2.contact_method_id = ec2.contact_method_id  AND   ec2.is_active = 't'
			LEFT OUTER JOIN  public.entity_contact ec3           ON ec3.entity_id = ea.entity_id          AND ec3.contact_type = 3 
			LEFT OUTER JOIN  public.contact_method cm3           ON cm3.contact_method_id = ec3.contact_method_id  AND   ec3.is_active = 't'
			LEFT OUTER JOIN  public.entity_contact ec4           ON ec4.entity_id = ea.entity_id          AND ec4.contact_type = 4 
			LEFT OUTER JOIN  public.contact_method cm4           ON cm4.contact_method_id = ec4.contact_method_id  AND   ec4.is_active = 't' 
			WHERE ea.entity_id = ?  AND    a.deleted_flag = 'f'  AND ea.is_active = 't' ";// LIMIT 1
		
		if($addr_id)
		{
			$params[]=$addr_id;
			$sql.= 'AND ea.address_id=?';
		}
		if($addr_type)
		{
			$params[]=$addr_type;
			$sql.= 'AND at.type_id=?';
		}
        return $this->db->query($sql,$params)->result_array();
	}
	/**
	* CURL to google maps geocode api
	* 
	* Built based on 
	* http://www.cssbakery.com/2010/10/google-geocoding-from-php-with-curl.html
	* http://code.google.com/apis/maps/documentation/geocoding/
	* 
	* was the contents of get_entity_lat_long, but breaking it down into
	* multiple smaller cases that all just  call this
	* 
	* @author Sam Basset 
	* @uses CURL to maps.googleapis.com 
	* @returns array
	* @param int $entity_id
	*/
	public function curl_address_lat_long($street,$city,$ctry,$reg,$postal)
	{
		$result=array('lat'=>null,'lng'=>null,'status'=>null,'success'=>false);
		if(!$postal || !$street) 
		{
			$result['status']='INCOMPLETE_ADDRESS';
			return $result;
		}
		//$key=GOOGLEAPIKEY; # not used
		$full_address=$street." ".$city." ".$reg." ".$city." ".$postal;
		$geocodeURL="http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($full_address)."&sensor=false";
		$ch = curl_init($geocodeURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$curl_exec_result   = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
 
		if ($httpCode == 200) 
		{
			$geocode = json_decode($curl_exec_result);
			if(count($geocode->results))
			{							
				$result['lat'] = $geocode->results[0]->geometry->location->lat;
				$result['lng'] = $geocode->results[0]->geometry->location->lng; 
				$result['status'] = $geocode->status;//on success, this should return "OK" 
				if($result['status']=='OK')
				{
					$result['success']=true;				
				}
			}
			else
			{
				$result['status'] = "EMPTY_RESULTS_FAIL";
			}
		} 
		else 
		{
			$result['status'] = "HTTP_FAIL_$httpCode";
		}
		return $result;
	}

	/**
	* takes an entity id and uses a CURL to find 
	* lat long of the first address found for that entity. 
	* if entity has multiple addresses and you need a specific one,  
	*   pass the address id as second argument
	* 
	* @param int $entity_id
	* @param int $address_id 
	*/
	public function get_entity_lat_long( $entity_id,$address_id=null)
	{
		$result=array('lat'=>null,'lng'=>null,'status'=>null,'success'=>false);
		$entity_id=(int)$entity_id;
		//at most one record returned
		$address=$this->get_entity_address($entity_id,$address_id);
		if(count($address)== 0 ) 
		{
			$result['status']='NO_ADDRESS_FOUND';
			return $result;
		}
		$address=$address[0];
		$street=$address['address_street'];
		$city  =$address['address_city'];
		$ctry  =$address['address_country'];
		$reg   =$address['address_region'];
		$postal=$address['postal_value'];
		//country/city arent enough, we need minimum these
		return $this->curl_address_lat_long($street,$city,$ctry,$reg,$postal);
	}
	
 
	
	public function update_entity_lat_lon($address_id,$lat,$lng)
	{
		$params=func_get_args();
		$sql= "SELECT public.update_entity_lat_lon(?,?,?)";
		return  $this->db->query( $sql,$params)->first_row()->update_entity_lat_lon;   
	}
	/*CREATE OR REPLACE FUNCTION "public"."update_entity_lat_lon"(_id int8, _lat float8, _lon float8)
  RETURNS "pg_catalog"."int4" AS $BODY$
BEGIN -- update_entity_lat_lon

	UPDATE "public".address  
	SET address_lat = _lat , 
			address_lon = _lon  
	WHERE address_id = _id;

	IF NOT FOUND THEN RETURN -1; END IF;


RETURN 1;

END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE COST 100
;

*/
	
	
	
	
    /**
    *  either returns id of existing postal code, 
    *  or creates new lu and returns its id
    * 
    * @param mixed $postal_value
    */
    public function get_postal_id($postal_value)
    {
        $params = array($postal_value);
        $sql = "SELECT public.get_postal_id(?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->get_postal_id;        
    }

    /**
    * return country id if exists
    * or creates new country 
    * 
    * @param mixed $country
    */
    public function get_country_id($country)
    {
        $params = array($country);
        $sql = "SELECT public.get_country_id(?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->get_country_id;        
    }
    
    /**
    * gets id of given region
    * or inserts it and also returns id
    * 
    * @param mixed $region
    */
    public function get_region_id($region)
    {
        $params = array($region);
        $sql = "SELECT public.get_region_id(?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->get_region_id;        
    }
    
    public function insert_entity_address($creator,$entity_id,$street,$city,$region_id,$country_id,$postal_id,$type,$owned_by)
    {
        $params =func_get_args();
        $sql = "SELECT public.insert_entity_address(?,?,?,?,?,?,?,?,?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->insert_entity_address;           
    }
    
    /**
    * insert OR update an address, depending if address id is there or not
    * returns address id
    * 
    * @param mixed $address_id
    * @param mixed $street
    * @param mixed $city
    * @param mixed $region
    * @param mixed $country
    * @param mixed $postalcode
    * @param mixed $entity_id
    * @param mixed $address_type_id
    * @param mixed $user
    * @param mixed $org
    */
    public function update_address($address_id,$street,$city,$region,$country,$postalcode,$entity_id,$address_type_id,$user,$org)
    {
        $params =func_get_args();
        $sql = "SELECT public.update_address(?,?,?,?,?,?,?,?,?,?)";
 
        return $this->db->query( $sql,$params)->first_row()->update_address;           
    }
    public function insert_entity_contact($creator,$entity_id,$type,$value,$owned_by)
    {
        $params = array($creator,$entity_id,$type,$value,$owned_by);
        $sql = "SELECT public.insert_entity_contact(?,?,?,?,?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
       // var_dump($this->db->last_query());
        return $result->insert_entity_contact;  
        
    }
    
    
    /**
    * return user person information for whoever is 
    * assigned to this email
    * 
    * @param mixed $email
    */
    public function who_has_this_email($email)
    {
		$sql="	SELECT p.person_fname,p.person_lname ,p.person_id,p.entity_id,u.user_id 
		from public.entity_contact ec 
		inner join public.lu_contact_type ct on ct.type_id=ec.contact_type AND ec.is_active=true 
		inner join public.contact_method cm on cm.contact_method_id=ec.contact_method_id AND cm.deleted_flag=FALSE --dont wory about deleted emails,etc
		inner join public.entity_person p on p.entity_id=ec.entity_id                    AND p.deleted_flag=false
		inner join  permissions.user u on u.person_id=p.person_id AND u.deleted_flag=false --AND p.entity_id != _entity_id --only check people besides this one,
		where  cm.value= ?";
		return $this->db->query($sql,$email)->result_array();
    }
    
    public function get_entity_contact($entity_id,$type)
    {
		$params=array($entity_id,$type);
		$sql="SELECT value  FROM public.entity_contact ec INNER JOIN public.entity e ON e.entity_id=ec.entity_id 
						AND e.entity_id=? AND ec.contact_type=? 
			INNER JOIN public.contact_method cm ON cm.contact_method_id=ec.contact_method_id AND ec.is_active='t' ";
		return $this->db->query($sql,$params)->result_array();
		
    }
    
    
    public function delete_entity_contact($entity_id,$type,$user)
    {
		$params=array($entity_id,$type);
		$query = $this->db->query( $sql,$params);  
		$sql = "SELECT public.delete_entity_contact(?,?)"; 
        $result  = $query->first_row();
        return $result->insert_entity_contact;  
    }
    public function get_orgtypes()
    {
        $sql = "SELECT type_id, type_name FROM public.lu_org_type ORDER BY type_id";
        return $this->db->query($sql)->result_array();
    }
    
    public function get_org_bytype($typeid)
    {
        $sql = "SELECT      o.org_name, 
                            o.org_id, 
                            o.entity_id, 
                            o.org_type 
                FROM        public.entity_org o 
                WHERE       o.org_type = ? 
                AND         o.deleted_flag = 'f'
                AND			".userorg_can_view('o')." = TRUE
                ORDER BY    o.org_name";
        return $this->db->query($sql,$typeid)->result_array();
        
    }
        /**
    * get an entity id given an org id
    * 
    * @param mixed $person_id
    */
    public function get_entity_by_org($org_id)
    {
        $sql = "SELECT entity_id FROM public.entity_org WHERE org_id = ?";
        return $this->db->query($sql,$org_id)->result_array();  
    }
    
    public function get_entity_org_details($org_id)
    {
		$sql = "SELECT * FROM public.entity_org WHERE org_id = ? LIMIT 1";
        return $this->db->query($sql,$org_id)->result_array();
    }
    
    public function get_entity_org_children($entity_id)
    {
    	$sql = $this->sql->getQuery('public.get_entity_org_children.sql');		
        return $this->db->query($sql,$entity_id)->result_array();
    }
    
    
    public function get_entity_url($e)
    {
    	$sql="SELECT u.url, u.theme_id FROM public.url u WHERE u.entity_id=?";
        $result = $this->db->query($sql,$e);
        return $result->result_array();		
    }
    
    
    public function get_entity_parent($entity_id)
    {
		$sql="SELECT er.parent_id FROM public.entity_relationship er WHERE er.child_id=?";
        $result = $this->db->query($sql,$entity_id);
        return $result->result_array();	
    }
    
    
}

?>
