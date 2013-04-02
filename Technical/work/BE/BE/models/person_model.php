<?php

require_once('./endeavor/models/entity_model.php');
class Person_model extends Entity_model
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_people($search=false)
    {//TODO: also get primary address and email here
    //>?? and phone number 
    	$where='';//if we are searching, dont restrict by this
    	if(!$search) {$where =" AND  ".userorg_can_view('p')." = TRUE  "; }
        $sql = "SELECT      p.person_id, 
                            p.person_fname, 
                            p.person_lname, 
                            p.person_birthdate, 
                            p.person_gender,  
                            a.address_street,
                            a.address_city,  
                            a.address_country,  (SELECT luc.country_abbr FROM lu_address_country luc WHERE a.address_country = luc.country_id) AS country_abbr,
                            a.address_region,   (SELECT lur.region_abbr  FROM lu_address_region  lur WHERE a.address_region  = lur.region_id)  AS region_abbr,    
                            lup.postal_value ,
                            cm1.value AS email,
                            cm2.value AS p_home, 
                            cm3.value AS p_work,
                            u.user_id,u.login,
                            cm4.value AS p_mobile  
                FROM        public.entity_person p 
                LEFT OUTER JOIN  public.entity_address ea            ON ea.entity_id = p.entity_id   AND ea.is_active = TRUE 
                LEFT OUTER JOIN  public.address a                    ON ea.address_id= a.address_id   AND    a.deleted_flag = FALSE 
                LEFT OUTER JOIN  public.lu_address_postal lup        ON lup.postal_id= a.address_postal 
                LEFT OUTER JOIN  public.entity_contact ec1           ON ec1.entity_id = p.entity_id          AND ec1.contact_type = 1    AND   ec1.is_active = TRUE 
                LEFT OUTER JOIN  public.contact_method cm1           ON cm1.contact_method_id = ec1.contact_method_id 
                LEFT OUTER JOIN  public.entity_contact ec2           ON ec2.entity_id = p.entity_id          AND ec2.contact_type = 2    AND   ec2.is_active = TRUE 
                LEFT OUTER JOIN  public.contact_method cm2           ON cm2.contact_method_id = ec2.contact_method_id  
                LEFT OUTER JOIN  public.entity_contact ec3           ON ec3.entity_id = p.entity_id          AND ec3.contact_type = 3    AND   ec3.is_active = TRUE 
                LEFT OUTER JOIN  public.contact_method cm3           ON cm3.contact_method_id = ec3.contact_method_id  
                LEFT OUTER JOIN  public.entity_contact ec4           ON ec4.entity_id = p.entity_id          AND ec4.contact_type = 4    AND   ec4.is_active = TRUE 
                LEFT OUTER JOIN  public.contact_method cm4           ON cm4.contact_method_id = ec4.contact_method_id 
                LEFT OUTER JOIN permissions.user u ON u.person_id = p.person_id 
                WHERE    p.deleted_flag = FALSE ".$where."
                    ";
        //    
        return $this->db->query($sql)->result_array();
    }
	public function get_person($person_id)
    {
		$sql = $this->sql->getQuery('permissions.get_person.sql');
		return $this->db->query($sql,$person_id)->result_array();        
    }
	
	/**
	* updates existing person or returns -1 if id not found
	* FOR ROSTER PERSON ONLY!!!!!!!!!!!!!!!!!!!!!!!!!
	* 
	* @param int $person_id
	* @param mixed $first
	* @param mixed $last
	* @param date $bd
	* @param int $mod_by
	*/
    public function update_person($person_id,$first_name,$last_name,$birthdate,$person_gender,$start_date)
    {                  
    	
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        $allowed= $this->db->query("SELECT * FROM public.entity_person p WHERE p.person_id=? AND ".userorg_can_update('p'),$person_id)->result_array();
        if(!count($allowed)) return false;//not allowed
        //------------------------------------------------------------------------
        return $this->db->query("select * from public.update_person(?,?,?,?,?,?,?,?) ",array($person_id,$first_name,$last_name,$birthdate,$person_gender,$start_date,$a_u_id,$a_o_id))->first_row()->update_person;
    }
	public function update_entity_person($person_id,$first,$last,$bd,$gender,$mod_by)
	{
		if($person_id && $person_id > 0)
    	{
			//check if allowed first
			$allowed= $this->db->query("SELECT * FROM public.entity_person p WHERE p.person_id=? AND ".userorg_can_update('p'),$person_id)->result_array();
	        if(!count($allowed)) return false;//not allowed
    	}
    	
		
		
		$params=array($person_id,$first,$last,$bd,$gender,$mod_by);
		
		
		$sql = "SELECT public.update_entity_person(?,?,?,?,?,?)";		
		return $this->db->query($sql,$params)->first_row()->update_entity_person;
	}
	public function update_person_fname($person_id,$first,$user)
	{
		$params=array($person_id,$first);
		$sql = "SELECT public.update_person_first_name(?,?)";
		
		return $this->db->query( $sql,$params)->first_row()->update_person_first_name;
	}
	public function update_person_lname($person_id,$last)
	{
		$params=array($person_id,$last);
		$sql = "SELECT public.update_person_last_name(?,?)";
		return $this->db->query( $sql,$params)->first_row()->update_person_last_name;
	}
	public function update_person_birthdate($person_id,$date)
	{
		$params=array($person_id,$date);
		$sql = "SELECT public.update_person_birthdate(?,?)";
		return $this->db->query( $sql,$params)->first_row()->update_person_birthdate;
	}
	/**
	* person_id is -1 if we want to create a new person, otherwise update existing
	* 
	* @param mixed $creator
	* @param mixed $person_id
	* @param mixed $fname
	* @param mixed $lname
	* @param mixed $gender
	* @param mixed $birthdate
	* @param mixed $owner
	*/
	public function insert_person($creator,$person_id,$fname,$lname,$gender,$birthdate,$owner,$front_end=null)
    {
    	/*if($person_id && $person_id > 0&&$front_end!=true &&$front_end!='t')
    	{
			//check if allowed first

			$allowed= $this->db->query("SELECT * FROM public.entity_person p WHERE p.person_id=? AND ".userorg_can_update('p'),$person_id)->result_array();
			
 			
	        if(!count($allowed)) return $person_id;//not allowed
    	}*/
    	
        $params = array($creator,$person_id,$fname,$lname,$gender,$birthdate,$owner);
        
        $sql = "SELECT public.insert_person(?,?,?,?,?,?,?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->insert_person;
    
    }
	
	public function get_entity_by_person($person_id)
    {
        $sql = "SELECT entity_id FROM public.entity_person WHERE person_id = ?";
        return $this->db->query($sql,$person_id)->result_array();  
    }
    public function get_entity_id_by_person($person_id)
    {
        $sql = "SELECT entity_id FROM public.entity_person WHERE person_id = ?";
        return $this->db->query($sql,$person_id)->first_row()->entity_id;  
    }
    
  public function _format_people_extgrid($result) 
    {
    	$long_fmt="F j, Y; g:i a";
        $bd_fmt="F j, Y";
        $plain='Y/m/d';
		foreach($result as &$user)
        {
			//format dates
			if(@$user['last_login_date'])
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
    
    
    
    
    public function count_users_with_this_email_active($email,$person_id=null)
    {
    	//anybody that is outside of this user
    	$params = array($email);
    	
    	
    	
		$sql="		select count(*) AS total
		from public.entity_contact ec 
		inner join public.lu_contact_type ct on ct.type_id=ec.contact_type               AND ec.is_active=true 
		inner join public.contact_method cm on cm.contact_method_id=ec.contact_method_id AND cm.deleted_flag=FALSE 
		inner join public.entity_person p on p.entity_id=ec.entity_id              AND p.deleted_flag=FALSE 
		 
		where (ct.type_id=1 OR ct.type_id=2 ) 
		 
		and cm.value= ?";
		
		if($person_id && $person_id > 0)
		{
			$params[]=$person_id;
			$sql.=" AND p.person_id != ?";
			
		}
		return $this->db->query( $sql,$params)->first_row()->total;   
        
		
    }
                                  
}

?>
