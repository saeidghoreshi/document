<?php
require_once('./endeavor/models/entity_model.php');
class Org_model extends Entity_model
{
	
	public function __construct()
	{
		parent::__construct();
	}
	

	public function get_entity($org)
	{
		$sql = "SELECT entity_id FROM entity_org WHERE org_id = ?";
		$query = $this->db->query($sql, array($org));
		return $query->first_row();
	}
	

	public function get_entity_from_org($org)
	{
		$sql = "SELECT entity_id FROM entity_org WHERE org_id = ?";
		$query = $this->db->query($sql, array($org));
		return $query->first_row()->entity_id;
	}
	public function get_entity_id_from_org($org)
	{
		$sql = "SELECT entity_id FROM entity_org WHERE org_id = ?";
		$query = $this->db->query($sql, array($org));
		return $query->first_row()->entity_id;
	}
	public function insert_entity_org($name,$owner,$creator,$org_type)
	{
		$params=array($name,$owner,$creator,$org_type);
		$sql = "SELECT public.insert_entity_org(?,?,?,?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->insert_entity_org;
	}
	
	public function get_parent($org)
	{
		$sql = "SELECT 		parent.*
				FROM 		public.entity_org parent
				INNER JOIN	public.entity_relationship e
					ON		e.parent_id = parent.entity_id
				INNER JOIN	public.entity_org child 
					ON		e.child_id = child.entity_id
					AND		child.org_id = ?";
		$result = $this->db->query($sql, array($org));
		return $result->first_row('array');
	}
	public function get_parent_org_id($org_id)
	{
		$sql="SELECT p.org_id FROM public.entity_org p 
			INNER JOIN public.entity_relationship erp ON erp.parent_id=p.entity_id 
			INNER JOIN public.entity_org c ON c.entity_id = erp.child_id AND c.org_id=?";
		return $this->db->query($sql, array($org_id))->first_row()->org_id;
	}
	
	
	public function get_org($org)
	{
		$sql = "SELECT 		*
				FROM 		public.entity_org
				WHERE		org_id = ?";
		$result = $this->db->query($sql, array($org));
		return $result->first_row('array');
	}
	/**
	* updates org name based on id
	* modby is the user making the change
	* hits entity_org table, and if this is a team or assoc, it will 
	* also update those records as well
	* 
	* @param mixed $org
	* @param mixed $newname
	* @param mixed $modby
	*/
	public function update_org_name($org,$newname,$modby)
	{
		$params=array($org,$newname,$modby);
		
		$sql="SELECT public.update_org_name(?,?,?)";
		
		return $this->db->query($sql, $params)->first_row()->update_org_name;
	}
	public function get_org_type($org)
    {
    	$sql="SELECT o.org_type FROM public.entity_org o 
    		  WHERE o.org_id = ? LIMIT 1";
        return $this->db->query($sql,array($org))->first_row()->org_type;
    }
    
    
    
	public function get_org_details_and_url($org)
	{
		$sql = 
        "   
            SELECT o.org_name,o.org_id,o.entity_id,o.org_type,o.is_valid,u.url
    		FROM  public.entity_org o 
    		LEFT OUTER JOIN public.url u ON o.entity_id = u.entity_id AND ispark=TRUE 
			WHERE		org_id = ?
        ";
		$result= $this->db->query($sql, array($org))->result_array();
		 if(!count($result)) return null;
		 $result=$result[0];
		 
		 //lazy solution:
		 $result['team_name'] = $result['org_name'];
		 $result['league_name'] = $result['org_name'];
		 $result['association_name'] = $result['org_name'];
		// $result['association_name'] = $result['org_name'];
		 
	     //parse url into useful data
	     if($result['url'])
	     {
	     	 $d='.';
		   $exp= explode($d,$result['url']);
		   
		   //array_shift will remove element zero, and return it, and modify the array
		   $result['websiteprefix'] = array_shift($exp);
		   $result['domainname']    = implode($d,$exp);//implode combines the rest together
		 }
		 else
		 {
		 	 
		   $result['websiteprefix'] = null;
		   $result['domainname']    = null;//implode combines the rest together
		 }
		 return $result;
	}
    public function getDomainNames($org_id=false)
    {
    	if(!$org_id)
        	$org_id= $this->permissions_model->get_active_org();
        $sql=
        "
            select d.id,d.domain,d.owned_by ,d.is_active 
            from public.domain d 
            INNER JOIN public.org_domain od 
            ON od.org_id=? AND od.domain_id=d.id 
            AND d.deleted_flag=FALSE
 
        ";
       // echo $a_o_id;
        return $this->db->query($sql,array($org_id))->result_array();
    	/*
        $a_o_id= $this->permissions_model->get_active_org();
 		,(SELECT COUNT(*) FROM public.url u INNER JOIN public.entity_org eo ON eo.entity_id = u.entity_id 
			AND eo.org_id= --show all leagues owned by this association tha tuse it
			 AND eo.deleted_flag=FALSE 
			 AND u.url LIKE '%'|| domain ) as league_count
        $sql=
        "
            SELECT d.id,d.domain,d.association_id,d.is_active 
            FROM public.domain d 
			INNER JOIN 
			WHERE   d.deleted_flag=FALSE AND d.is_active=TRUE
 
        ";
        /*        		OR  d.association_id in       --domains owned by parent org, in case this is league
	            (
	                select  asn.association_id 
	                from public.association asn 
	                inner join entity_org aeo on aeo.org_id=asn.org_id
	                where asn.org_id= public.get_parent_org(?)
	            ) 
       // echo $a_o_id;
        return $this->db->query($sql,array($a_o_id,$a_o_id,$a_o_id))->result_array();*/
    }
	  /**
    * @author sam
    * @since dec 28 2011,moved here from assoc_model
    * org id is for link table
    * 
    * $own is for owned_by prm flag
    * 
    * @param mixed $a_org_id
    * @param mixed $domain
    * @param mixed $user
    * @param mixed $own
    */
    public function insert_domain($a_org_id,$domain,$user,$own)
    {
    	$params=func_get_args();
    	
		$sql="SELECT public.insert_domain(?,?,?,?)";
        return $this->db->query($sql,$params)->first_row()->insert_domain;
		
    }
    public function update_domain_active($dom_id,$val)
    {
		
    	$params=func_get_args();
    	
		$sql="SELECT public.update_domain_active(?,?)";
        return $this->db->query($sql,$params)->first_row()->update_domain_active;
    }
    /**
    * removes the domain from the given org id
    * domain itself is not deleted
    * 
    * @param mixed $id
    * @param mixed $org 
    */
    public function delete_domain($id,$org)
    {
    	$params=func_get_args();
    	
		$sql="SELECT public.delete_domain(?,?)";
        return $this->db->query($sql,$params)->first_row()->delete_domain;
    }
    
	
	public function update_entity_org_logo($org_id,$filename)
	{
		
		$params=array($org_id,$filename);
		$sql="SELECT update_entity_org_logo(?,?)";
		return $this->db->query($sql,$params)->first_row()->update_entity_org_logo;
	}
	
	
	
	
	
	
	
	
	
	public function get_org_logo($org_id)
	{
		//echo $org_id;
 
		$default_logo='assets/images/spectrum.png';

		
		$sql="SELECT org_logo FROM public.entity_org WHERE org_id=?";
        $result=$this->db->query($sql,array($org_id))->result_array();  
        if(count($result)==0 || !isset($result[0]['org_logo']) || $result[0]['org_logo']==null)  
        	$logo= $this->_recursive_parent_org_logo($org_id);//if this org has no logo: get recursive by parent
		else
			$logo = $result[0]['org_logo'];//this org has a logo: so grab it and were done
			
			
		//if STILL nothing found by recursive, go to default
		//otherwise , append the base image path, thats not stored in db
		$logo = ($logo == null) ? $default_logo : ORG_LOGO_BASEPATH."/".$logo;
 
		return $logo;
	}
	
	
	
	private function _recursive_parent_org_logo($org_id)
	{
		$sql="SELECT op.org_logo,op.org_id FROM public.entity_org op 
				INNER JOIN public.entity_relationship er ON op.entity_id = er.parent_id 
				INNER JOIN public.entity_org oc         ON oc.entity_id=er.child_id AND oc.org_id=?";
		
        $result=$this->db->query($sql,array($org_id))->result_array();   
        if(count($result)==0) return null;//this org does nto have a parent org, it is the top level
        
        if( !isset($result[0]['org_logo']) || $result[0]['org_logo']==null)//go deeper
			return $this->_recursive_parent_org_logo($result[0]['org_id']);//find logo of parent org
		else
			return $result[0]['org_logo'];//logo found
		
	}
	
	
	public function upload($file_index,$path,$extraTag='',$date=null)
    {
    	$this->load->library('images');
    	$file=$_FILES[$file_index];
    	if(!$date)$date=date("Y-m-d-G-i-s");
    	
        list($width, $height, $type, $attr) = $this->images->get_image_size($file_index);// getimagesize($file["tmp_name"]);    
        $extension =$this->images->get_extension($file_id);
        
        $changed_file_name=$date."-".$extraTag.".".$extension;
        
 
		$upload_location = $path."/".$changed_file_name;
 

        $this->load->library('ftp');   
         //upload to root
 
        $this->ftp->upload($this->images->tmp_name($file_index),$upload_location );
       // $this->ftp->close();      
        return $changed_file_name;
    }
	
	
	
	
	public function __call($method, $params)
	{
	   $prefixes = array('get_parent','get_org');
	   	
	   	//creates get parent magic functions
	   	foreach($prefixes as $prefix)
	   	{
			if(strstr($method,$prefix))
			{
				$results = $this->$prefix($params[0]);
				$key = str_replace($prefix.'_','',$method);
				if(count($results)==0)
				{
					return null;
				}
				elseif(array_key_exists($key,$results))
				{
					return $results[$key];
				}
				else
				{
					$msg = "magic function $method does not exist.\n Try:\n";
					foreach($results as $key=>$value) $msg .= "{$prefix}_{$key}\n";
					trigger_error($msg, E_USER_WARNING);
					return null;
				}
			}	
	   	}
	}
}

?>
