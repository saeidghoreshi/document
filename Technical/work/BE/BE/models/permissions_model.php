<?php
// new permissions schmea changes: sam: 
//use assignment table instead of user_role, among other things:
//method_group does not exist anymore
//lu_authentication is new, before these were stored direclty like role_view
//instead of method_controller we just have lu_controller
//user_type table does not exist: use role instead
//fixed stored procedures last

require_once('./endeavor/models/endeavor_model.php');

class Permissions_model extends Endeavor_model
{
	
	/**
	* @var Sql
	*/
	public $sql;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->library('sql');
        $this->load->library('result');  
		//$this->DB = $this->load->database('default', true);
		//$this->update_user_activity();
	}
	
	/**
	* Validates a user with token, current php session, connecting from IP address
	* against database and checks if the CI Controller/Method combo requires login,
	* if the token is still valid, and if the user has assigned permissions to 
	* access said Controller/Method combo.
	* 
	* @param mixed $c CI Controller Name
	* @param mixed $m CI Model Name
	* @param mixed $t Token
	* @param mixed $u User ID
	* @param mixed $o Org ID
	* @param mixed $s Current PHP Session ID
	* @param mixed $i Current IP Address
	*/
	public function is_userorg_allowed($c,$m,$t,$u,$o,$s,$i)
	{
		$query = $this->db->query('SELECT permissions.is_userorg_allowed (?, ?, ?, ?, ?, ?, ?)', array($c,$m,$t,$u,$o,$s,$i));
		$req = array_pop($query->first_row('array'));
		return ($req=='t') ? true : false;
	}

    /**
     * Checks to see if token is valid or not
     */
	public function is_token_valid($t,$s,$i)
    {
        $query = $this->db->query("SELECT permissions.is_token_valid(?,?,?,?)", array($t,$s,$i,0));
        $req = array_pop($query->first_row('array'));
        return ($req=='t') ? true : false;
    }

	/**
	* Generates a token for User on Session ID connecting from IP Address
	* 
	* @param mixed $u User ID
	* @param mixed $s Current PHP Session ID
	* @param mixed $i Current IP Address
	*/
	public function generate_token($u,$s,$i)
	{
		$t = "T".strtoupper(md5(uniqid()));
		$query = $this->db->query('SELECT permissions.create_token(?,?,?,?)', array($t,$u,$s,$i));
		return $t;
	}
	
	/**
	* Returns the active user id if it exists or null if it does not 
	* Read from Session for Active User
	*/
	public function get_active_user()
	{
		if(@$_SESSION['active_user']) return $_SESSION['active_user'];
		return null;
	}
    public function get_active_fb_id()
    {
        if(@$_SESSION['active_fb_id']) return $_SESSION['active_fb_id'];
        return null;
    }
	
	/**
	* Returns the active org id if it exists or null if it does not 
	* Read from Session for Active Organization
	*/
	public function get_active_org()
	{
        if(@$_SESSION['active_org']) return $_SESSION['active_org'];
		return null;
	}
    
    public function get_active_org_type()
    {
    	$sql="SELECT o.org_type FROM public.entity_org o 
    		  WHERE o.org_id = ? LIMIT 1";
        return $this->db->query($sql,array($this->get_active_org()))->first_row()->org_type;
    }
	public function get_active_user_org_roles()
	{
		$sql="SELECT role_id from permissions.assignment WHERE deleted_flag=false AND org_id=? AND user_id=? ";
		$p=array($this->get_active_org(),$this->get_active_user());
        $raw= $this->db->query($sql,$p)->result_array();
        
        $role_ids=array();
        foreach($raw as $r)
        {
			$role_ids[]=(int)$r['role_id'];
			
        }
        return $role_ids;
	}
    
    public function get_is_user_org_executive()
    {
		$roles=$this->get_active_user_org_roles();
		
		 
        //exec is defined as these roles
        ///assoc manager           league exec        team manager
        if(in_array(1,$roles) ||in_array(3,$roles)||in_array(4,$roles))
        	return true;
        else 
        	return false;
    }
    public function get_active_entity()
    {
        $sql="SELECT o.entity_id FROM public.entity_org o 
              WHERE o.org_id = ? LIMIT 1";
        return $this->db->query($sql,array($this->get_active_org()))->first_row()->entity_id;
    }
    
    public function get_active_org_and_type() //More efficient// not really, its about the same
    {
        $a_o_id=$this->get_active_org();
        $temp=$this->db->query("select * from public.get_org_type(?);",array($a_o_id))->result_array();
        
        $org_type_id_name=explode(',',$temp[0]["get_org_type"]);
        
        $org_type_id    =$org_type_id_name[0];
        $org_name       =$org_type_id_name[1];
        $org_f_tax      =$org_type_id_name[2];
        $org_p_tax      =$org_type_id_name[3];
        $org_l_tax      =$org_type_id_name[4];
        $org_entity_id  =$org_type_id_name[5];
        
        $temp=array_merge
        (
         array("org_f_tax"=>$org_f_tax)
        ,array("org_p_tax"=>$org_p_tax)
        ,array("org_l_tax"=>$org_l_tax)
        ,array("org_type_id"=>$org_type_id)
        ,array("org_name"=>$org_name)
        ,array("org_id"=>$a_o_id)
        ,array("entity_id"=>$org_entity_id)
        );
        return $temp;
    }
    public function get_org_website_url() 
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $q="SELECT url from public.url where entity_id=(select entity_id from public.entity_org where org_id=?)";
        $result=$this->db->query($q,array($a_o_id))->result_array();   
        $_SESSION["url"]=$result[0]["url"];
    }

    public function set_active_org($id)
    {
        $_SESSION['active_org'] = $id;
       
        return $_SESSION['active_org'];
    }
    
    public function save_default_org($userid,$orgid)
    {
        $params = array((int)$userid,(int)$orgid);
        $sql = "SELECT permissions.update_default_org(?,?)";
        $query = $this->db->query($sql,$params);   
        $result  = $query->first_row();
        return $result->update_default_org;
    }
    
    /**
    * @desc
    * @author Sam Bassett
    * @param mixed $userid
    */
    public function get_user_data($userid)
    {       
        $sql = $this->sql->getQuery('permissions.get_user_data.sql');
        return $this->db->query($sql,$userid)->result_array();
    }
    
	/**
	* @desc get all assigned roles for the given user, using NEW_PERMISSIONS schema
	* @author: sam.
	* @param mixed $userid
	*/
    public function get_user_assignments($userid)
    {               
        //$sql = $this->sql->getQuery('permissions.get_user_assignments.sql'/*,array(userorg_can_view('a')  )  */);
        //updated query file, but could not load it so copied here. moved where clauses to inner joins for speed
        //and added entity_org NOT deleted check 
        $sql="SELECT		 a.assignment_id
        	,a.role_id
        	,a.org_id AS org_id
        	,r.role_name
        	,o.org_name
        	,o.org_type
        	,ot.type_name
        	,a.effective_range_end
        	,a.effective_range_start
        	,u.default_org_id
        	,ot.image

			FROM       	permissions.user u

			INNER JOIN permissions.assignment a
			    ON a.user_id = u.user_id       AND         u.user_id = ?
			    AND a.deleted_flag = FALSE
			    AND	(CURRENT_TIMESTAMP < a.effective_range_end OR a.effective_range_end IS NULL)
				AND	(CURRENT_TIMESTAMP > a.effective_range_start OR a.effective_range_start IS NULL)
			
			INNER JOIN permissions.lu_role r
			    ON r.role_id = a.role_id

			INNER JOIN public.entity_org o
			    ON o.org_id = a.org_id AND o.deleted_flag=FALSE

			INNER JOIN public.lu_org_type ot
			    ON o.org_type = ot.type_id




			ORDER BY    org_type
			";
        $query  = $this->db->query($sql,array($userid));
        return $query->result_array();
    }
    
    /**
    * do not include expired roles
    * 
    * @param mixed $userid
    */
    public function get_user_assignments_not_expired($userid)
    {               
        $sql = $this->sql->getQuery('permissions.get_user_assignments.sql',array(userorg_can_view('a')));
        $query  = $this->db->query($sql,array($userid));
        return $query->result_array();
    }
    
    /**
    * include even if expired
    * 
    * @param mixed $userid 
    * 
    * skips signing auth
    */
    public function get_user_assignments_include_expired($userid)
    {               
        $sql ="SELECT		 
        	 a.assignment_id
        	,a.role_id
        	,a.org_id
        	,r.role_name
        	,o.org_name
        	,o.org_type
        	,a.effective_range_end
        	,a.effective_range_start
        	,u.default_org_id
        	,u.user_id
        	,ot.image

		FROM       	permissions.user u

		INNER JOIN permissions.assignment a
		    ON a.user_id = u.user_id
		    AND a.deleted_flag = FALSE

		INNER JOIN permissions.lu_role r
		    ON r.role_id = a.role_id AND r.role_id !=16

		INNER JOIN public.entity_org o
		    ON o.org_id = a.org_id  AND o.deleted_flag=FALSE 

		INNER JOIN public.lu_org_type ot
		    ON o.org_type = ot.type_id

		WHERE      	u.deleted_flag = FALSE
		AND         u.user_id = ? 
		AND ( ".userorg_can_view('a')."=TRUE  OR a.org_id=? )
		ORDER BY    org_type";//
        
        $org=$this->get_active_org();
        $query  = $this->db->query($sql,array($userid,$org));
        return $query->result_array();
    }
    
    public function delete_assignment($assn_id,$mod)
    {
        $params = array($assn_id,$mod);
        $sql = "SELECT permissions.delete_assignment(?,?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->delete_assignment;
    }
    
    public function set_active_user($id)
	{
		$_SESSION['active_user']= $id;
        $_SESSION['login_time'] =date('Y-m-d h:i:s');
	}
    public function set_active_fb_id($id)
    {
        $_SESSION['active_fb_id'] = $id;
    }
    
	
    /**
    * from entity_person as well as users
    * user types do not exist anymore
    */
    public function get_users() 
    {
           $sql = $this->sql->getQuery('permissions.get_users.sql',array(userorg_can_view('u'),userorg_can_view('p')));
           //var_dump($sql);
           return $this->db->query($sql)->result_array();
    }
    
    /**
    * only gets people that have a user account
    * 
    * @param mixed $peson_id
    */
    public function get_people_are_users($role_id=-1,$org_id=-1)
    {
    	//$t=userorg_can_view("u");
    	$org=$this->get_active_org();
    	//$params=array($org,$role_id);
        //;
        //$params=array();
        
        $filter='';
        $notzero='';
        $params=array($org);
        
        $role_filter ='';
        $org_filter='';
        /*
        if($role_id && $role_id != -1)
        {
			$role_filter=" AND af.role_id = ?  ";
			$params[]=$role_id;
			if($org_id != -1)
			{
				$org_filter= " AND  af.org_id = ? ";
				$params[]=$org_id;
			}
			
			$filter = " ( SELECT COUNT(*)  FROM permissions.assignment af WHERE af.user_id = u.user_id  
						".$role_filter." ".$org_filter."   AND deleted_flag='f')  AS filter_matches," ;
			$notzero='AND filter_matches <> 0';
			
        }
*/

		$sql = " SELECT    p.person_id, u.user_id,u.last_login_date,  u.login, u.owned_by,u.created_by,".$filter."
                            p.person_fname, 
                            p.person_lname, 
                            p.person_birthdate, 
                            p.person_gender,  
                            a.address_street,
                            a.address_city,  
                            a.address_country,  
                            (SELECT luc.country_abbr FROM lu_address_country luc WHERE a.address_country = luc.country_id) AS country_abbr,
                            a.address_region,   
                            (SELECT lur.region_abbr  FROM lu_address_region  lur WHERE a.address_region  = lur.region_id)  AS region_abbr,                 
                            lup.postal_value ,
                            cm1.value AS email,
                            cm2.value AS p_home, 
                            cm3.value AS p_work,
                            cm4.value AS p_mobile  
                FROM    permissions.user u    
                INNER JOIN   public.entity_person p 	
                ON  		p.person_id = u.person_id AND u.deleted_flag='f' AND p.deleted_flag = 'f'
                 AND u.person_id IS NOT NULL 
                LEFT OUTER JOIN  public.entity_address ea            ON ea.entity_id = p.entity_id   AND ea.is_active = 't' AND p.deleted_flag = 'f' 
                LEFT OUTER JOIN  public.address a                    ON ea.address_id= a.address_id   AND    a.deleted_flag = 'f' 
                LEFT OUTER JOIN  public.lu_address_postal lup        ON lup.postal_id= a.address_postal 
                LEFT OUTER JOIN  public.entity_contact ec1           ON ec1.entity_id = p.entity_id          AND ec1.contact_type = 1   AND   ec1.is_active = 't'
                LEFT OUTER JOIN  public.contact_method cm1           ON cm1.contact_method_id = ec1.contact_method_id  
                LEFT OUTER JOIN  public.entity_contact ec2           ON ec2.entity_id = p.entity_id          AND ec2.contact_type = 2   AND   ec2.is_active = 't'
                LEFT OUTER JOIN  public.contact_method cm2           ON cm2.contact_method_id = ec2.contact_method_id 
                LEFT OUTER JOIN  public.entity_contact ec3           ON ec3.entity_id = p.entity_id          AND ec3.contact_type = 3   AND   ec3.is_active = 't'
                LEFT OUTER JOIN  public.contact_method cm3           ON cm3.contact_method_id = ec3.contact_method_id  
                LEFT OUTER JOIN  public.entity_contact ec4           ON ec4.entity_id = p.entity_id          AND ec4.contact_type = 4   AND   ec4.is_active = 't'
                LEFT OUTER JOIN  public.contact_method cm4           ON cm4.contact_method_id = ec4.contact_method_id  
				WHERE 
				OR    u.user_id IN (SELECT aa.user_id FROM permissions.assignment aa WHERE  aa.org_id = ?  AND aa.deleted_flag='f'  	 )   
 ";//".$notzero." broken
 		$sorted_sql=$this->result->order_by($sql);
 		// var_dump($sorted_sql);
		return $this->db->query($sorted_sql,$params)->result_array();        
    }
    
    public function get_user($user_id)
    {
		$sql = " SELECT    p.person_id, u.user_id,u.last_login_date,  u.login, u.password,
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
                            cm4.value AS p_mobile  
                FROM    permissions.user u    
                INNER JOIN   public.entity_person p 	ON  		p.person_id = u.person_id AND u.user_id=?
                LEFT OUTER JOIN  public.entity_address ea            ON ea.entity_id = p.entity_id   AND ea.is_active = 't' AND p.deleted_flag = 'f' 
                LEFT OUTER JOIN  public.address a                    ON ea.address_id= a.address_id   AND    a.deleted_flag = 'f' 
                LEFT OUTER JOIN  public.lu_address_postal lup        ON lup.postal_id= a.address_postal 
                LEFT OUTER JOIN  public.entity_contact ec1           ON ec1.entity_id = p.entity_id          AND ec1.contact_type = 1   AND   ec1.is_active = 't'
                LEFT OUTER JOIN  public.contact_method cm1           ON cm1.contact_method_id = ec1.contact_method_id  
                LEFT OUTER JOIN  public.entity_contact ec2           ON ec2.entity_id = p.entity_id          AND ec2.contact_type = 2   AND   ec2.is_active = 't'
                LEFT OUTER JOIN  public.contact_method cm2           ON cm2.contact_method_id = ec2.contact_method_id 
                LEFT OUTER JOIN  public.entity_contact ec3           ON ec3.entity_id = p.entity_id          AND ec3.contact_type = 3   AND   ec3.is_active = 't'
                LEFT OUTER JOIN  public.contact_method cm3           ON cm3.contact_method_id = ec3.contact_method_id  
                LEFT OUTER JOIN  public.entity_contact ec4           ON ec4.entity_id = p.entity_id          AND ec4.contact_type = 4   AND   ec4.is_active = 't'
                LEFT OUTER JOIN  public.contact_method cm4           ON cm4.contact_method_id = ec4.contact_method_id  
                LIMIT 1
 "; 
		return $this->db->query($sql,$user_id)->result_array();
    }
   
    public function get_roles_by_orgtype($org_type_id)
    {
        $result=$this->db->query("select * from permissions.get_roles_by_orgtype(?);",array($org_type_id))->result_array();        
        return $result;
    }
    
    /**
    * which orgs can we assign to this role?
    * 
    * @param mixed $role_id
    */
    public function get_orgs_by_role($role_id)
    {
		$sql="SELECT eo.org_name, eo.org_id, eo.entity_id  FROM public.entity_org eo 
				WHERE eo.deleted_flag='f'
				 AND eo.org_type IN 
					(SELECT oa.org_type_id FROM permissions.lu_role lur 
					INNER JOIN permissions.lu_role_org_allowed oa ON oa.role_id =? )";
		return $this->db->query($sql,array($role_id))->result_array(); 			
    }
    
    /**
    * given a person id, get the logins for that person (should be only 0 or 1 result)
    * 
    * @param mixed $person_id
    */
    public function get_user_by_person($person_id)
    {
		$sql = $this->sql->getQuery('permissions.get_user_by_person.sql');
		return $this->db->query($sql,$person_id)->result_array();        
    }
    
    /**
    * given a person id, get the user_id for the login for that person (should be only 0 or 1 result)
    * 
    * @param mixed $person_id
    */
    public function get_userid_by_person($person_id)
    {
        $sql = "SELECT user_id FROM permissions.user WHERE person_id = ? LIMIT 1";
        return $this->db->query($sql,$person_id)->result_array();  
    }
    public function get_user_id_int_by_person($person_id)
    {
        $sql = "SELECT user_id FROM permissions.user WHERE person_id = ? LIMIT 1";
        $result= $this->db->query($sql,$person_id)->result_array();  
        
        if(!count($result) || !$result[0]['user_id']) $found= -1;
        else $found= (int)$result[0]['user_id'];
        
        return $found;
    }
    /**
    * given a user id, get the personid for the login for that person (should be only 0 or 1 result)
    * 
    * @param mixed $user_id
    */
    public function get_personid_by_user($user_id)
    {
        $sql = "SELECT person_id FROM permissions.user WHERE user_id = ? LIMIT 1";
        return $this->db->query($sql,$user_id)->result_array();  
    }
    /**
    * get an entity id given a person id
    * 
    * @param mixed $person_id
    */
    
    /**
    * 
    */
    public function get_lu_auth()
    {
        $sql = $this->sql->getQuery('permissions.get_lu_auth.sql');
        return $this->db->query($sql)->result_array();
    }
    
    /**
    * we do not get locatoin here, location only exists after a role is assigned
    */
    public function get_roles() 
    {
    
		$sql=  "SELECT r.role_id, r.role_name FROM permissions.lu_role r ORDER BY r.role_name ASC";
		return $this->db->query($sql)->result_array();
    }
    
    /**
    * all org types
    */
    public function get_orgtypes()
    {
        $sql = "SELECT type_id, type_name , image FROM public.lu_org_type ORDER BY type_id";
        return $this->db->query($sql)->result_array();
    }
    public function get_roles_by_userid($user_id)
    {       
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------                    
        $q="
            select distinct assignment_id,r.role_id,  r.role_name 
                from permissions.assignment a
                inner join permissions.lu_role r    on  r.role_id=a.role_id 
                                                    and a.user_id=? 
                                                    and a.org_id=?
                                                    and effective_range_start::timestamp<current_timestamp 
                                                    and (effective_range_end is null or effective_range_end >=current_timestamp)
                                                    and deleted_flag=false
        ";
        return $this->db->query($q,array($user_id,$a_o_id))->result_array();
    }
    public function get_unassignedroles_by_userid($user_id)
    {       
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------                    
        $q="
            select distinct assignment_id,r.role_id,  r.role_name 
                from permissions.assignment a
                right join permissions.lu_role r  
                        on r.role_id=a.role_id 
                        and  a.user_id=? 
                        and a.org_id=?
                        and effective_range_start::timestamp<current_timestamp 
                        and (effective_range_end is null or effective_range_end >=current_timestamp)
                where assignment_id is null
        ";
        return $this->db->query($q,array($user_id,$a_o_id))->result_array();
    }
    public function get_org_users_distinct($org_id=false)
    { 
        if(!$org_id)$org_id= $this->permissions_model->get_active_org();
 
        
        $q=
        "
            select distinct(p.person_id) , u.user_id, person_fname , person_lname ,person_birthdate
                from public.entity_person p 
                inner join permissions.user u               on p.person_id=u.person_id AND u.deleted_flag=false AND p.deleted_flag=false
                inner join permissions.assignment a         on a.user_id=u.user_id AND a.deleted_flag=false  AND a.org_id=?  
                inner join permissions.lu_role r            on r.role_id=a.role_id 
                 
        ";                      
        $result= $this->db->query($q,array($org_id))->result_array();
        return $result;
            
    }
    public function get_org_users($org_id,$remove_sa=true,$role_id=null)
    {
    	$params=array();

    	$sa_check='';
    	if($remove_sa) { $sa_check= "AND r.role_id !=16 ";}
    	if($role_id) 
    	{
    		$sa_check=" AND r.role_id =?";
    		$params[]=$role_id;
    	}    	
    	$params[]=$org_id;
    	$search = $this->input->get_post('query');
    	$where='';
    	if($search)
    	{
    		//from grid search bar
			$where = "WHERE lower(p.person_fname) LIKE '%'||lower(?)||'%'   
						OR  lower(p.person_lname) LIKE '%'||lower(?)||'%'   ";
			$params[]=$search;
			$params[]=$search;
    	}
    	
    	//putting DISTINCT on assignment_id does not work. so filter them after
		$sql="SELECT  asn.assignment_id
					, asn.role_id, asn.org_id, asn.user_id ,
			 		 asn.effective_range_end, asn.effective_range_start , 
			 		 u.last_login_date,
			 		 u.person_id ,p.person_fname, p.person_lname , r.role_name,u.login,u.password ,p.entity_id
			 		 , p.person_birthdate 
			 		  ,p.person_gender 
			 		 , cm1.value AS email,
                            cm2.value AS p_home, 
                            cm3.value AS p_work,
                            cm4.value AS p_mobile ,
                           a.address_street,
                            a.address_city,  
                            a.address_country,  
                            (SELECT luc.country_abbr FROM lu_address_country luc WHERE a.address_country = luc.country_id) AS country_abbr,
                            a.address_region,   
                            (SELECT lur.region_abbr  FROM lu_address_region  lur WHERE a.address_region  = lur.region_id)  AS region_abbr,                 
                            lup.postal_value 
			FROM permissions.assignment asn 
			INNER JOIN permissions.lu_role r ON asn.role_id = r.role_id  ".$sa_check." 
			INNER JOIN permissions.user u ON asn.user_id=u.user_id AND asn.org_id = ? AND asn.deleted_flag='f' AND u.deleted_flag='f' 
			INNER JOIN public.entity_person p ON u.person_id = p.person_id AND p.deleted_flag=FALSE 
			    LEFT OUTER JOIN  public.entity_address ea            ON ea.entity_id = p.entity_id   AND ea.is_active = 't' AND p.deleted_flag = 'f' 
	            LEFT OUTER JOIN  public.address a                    ON ea.address_id= a.address_id   AND    a.deleted_flag = 'f' 
	            LEFT OUTER JOIN  public.lu_address_postal lup        ON lup.postal_id= a.address_postal 
	            LEFT OUTER JOIN  public.entity_contact ec1           ON ec1.entity_id = p.entity_id          AND ec1.contact_type = 1   AND   ec1.is_active = 't'
	            LEFT OUTER JOIN  public.contact_method cm1           ON cm1.contact_method_id = ec1.contact_method_id  
	            LEFT OUTER JOIN  public.entity_contact ec2           ON ec2.entity_id = p.entity_id          AND ec2.contact_type = 2   AND   ec2.is_active = 't'
	            LEFT OUTER JOIN  public.contact_method cm2           ON cm2.contact_method_id = ec2.contact_method_id 
	            LEFT OUTER JOIN  public.entity_contact ec3           ON ec3.entity_id = p.entity_id          AND ec3.contact_type = 3   AND   ec3.is_active = 't'
	            LEFT OUTER JOIN  public.contact_method cm3           ON cm3.contact_method_id = ec3.contact_method_id  
	            LEFT OUTER JOIN  public.entity_contact ec4           ON ec4.entity_id = p.entity_id          AND ec4.contact_type = 4   AND   ec4.is_active = 't'
	            LEFT OUTER JOIN  public.contact_method cm4           ON cm4.contact_method_id = ec4.contact_method_id  
		
			  ".$where    ;
			  //var_dump($sql);
			  //var_dump($params);
        $records= $this->db->query($sql, $params)->result_array();	
        
        $used_asn_ids=array();
        
        $distinct=array();
        foreach($records as $r)
        {
        	$a_id=$r['assignment_id'];
			if(!isset($used_asn_ids[$a_id]))
			{
				$used_asn_ids[$a_id]=true;//now it is set
				$distinct[]=$r;
			}
			//else we already have it so continue
			
        }
        
        
        return $distinct;		
    }
    
    /**
    * org types that are allowed at least one role
    * if type given, then only types BELOW that one.
    * 
    * manually disabled is umpire assoc and tournament
    * 
    */
    public function get_orgtypes_have_roles($type=null)
    {
    	$params=array();
    	$where='';
    	if($type) 
    	{
			$where="AND t.type_id > ?";
			$params[]=$type;
    	}
        $sql = "SELECT t.type_id, type_name, image FROM public.lu_org_type t
			WHERE t.type_id IN (SELECT a.org_type_id FROM permissions.lu_role_org_allowed a)  
			AND t.type_id != 5 AND t.type_id != 4  
			".$where."
			ORDER BY type_id";
        return $this->db->query($sql,$params)->result_array();
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
    
    public function get_auth()
    {
        $sql = "SELECT * FROM permissions.lu_authentication";
        
        return $this->db->query($sql)->result_array();
        
    }
    
    public function get_role_method($role_id,$method_id)
    {
		$params = array($role_id,$method_id);
        $sql = "SELECT     COUNT(*) 
                FROM       permissions.role_method rm 
                WHERE      rm.role_id = ? 
                AND        rm.method_id = ? ";        
        return $this->db->query($sql,$params)->result_array();
    }
    
    public function get_controller_role_allowed($role_id,$ctr_id)
    {
		$params = func_get_args();
        $sql = "SELECT      m.method_name, m.method_id, m.controller_id   

				,(SELECT     COUNT(*) > 0 
	                FROM       permissions.role_method rm 
	                WHERE      rm.role_id = ? 
	                AND        rm.method_id = m.method_id) 
	            AS is_allowed  

                FROM        permissions.method m  
                WHERE       m.controller_id = ? 
                ORDER BY m.method_name ASC";        
        return $this->db->query($sql,$params)->result_array();
    }
    public function get_role_menu($role_id,$menu_id)
    {//not used yet get one or the other
        $params = array($role_id,$menu_id);
        $sql = "SELECT    (SELECT a.code FROM permissions.lu_authentication a WHERE a.auth_id = rm.view_auth_id) AS view, 
        			      (SELECT b.code FROM permissions.lu_authentication b WHERE b.auth_id = rm.update_auth_id) AS update,    
        			       rm.update_auth_id, rm.view_auth_id 
                FROM       permissions.role_menu rm 
                WHERE      rm.role_id = ? 
                AND        rm.menu_id = ? ";
        
        return $this->db->query($sql,$params)->result_array();
        
    }
    public function get_role_menu_parent($p,$role)
    {
		$sql="SELECT    s.id,
						? AS role_id,
						s.parent,
						s.menu_label, 
						s.menu_domid, 
						s.menu_group, 
						s.menu_order, 
						s.menu_type, 
						s.menu_active, 
						s.image 
						,va.code AS view_code
						,rmv.view_auth_id
						,ua.code AS update_code
						,rmu.update_auth_id
						
			FROM        system.sys_menu s 
			
			LEFT OUTER JOIN permissions.role_menu rmv ON rmv.menu_id = s.id AND rmv.role_id = ?
			LEFT OUTER JOIN permissions.lu_authentication va ON va.auth_id = rmv.view_auth_id 
			LEFT OUTER JOIN permissions.role_menu rmu ON rmu.menu_id = s.id AND rmu.role_id = ? 
			LEFT OUTER JOIN permissions.lu_authentication ua ON ua.auth_id = rmu.update_auth_id 
			
			WHERE       s.parent = ? 
			AND			s.menu_active = TRUE 
			ORDER BY    s.menu_label ASC";
        return $this->db->query($sql,array($role,$role,$role,$p))->result_array();
    }
    
    public function get_menu($parent)
    {//not used yet get one or the other
        $sql = "SELECT      s.id,
                            s.parent,
                            s.menu_label, 
                            s.menu_domid, 
                            s.menu_group, 
                            s.menu_order, 
                            s.menu_type, 
                            s.menu_active, 
                            s.image 
                FROM        system.sys_menu s
                WHERE       s.parent = ? 
                ORDER BY    menu_label ASC";
        
        return $this->db->query($sql,$parent)->result_array();
        
    }
    
    public function get_menubar()
    {//not used yet get one or the other
        $sql = "SELECT      s.id,
                            s.parent,
                            s.menu_label, 
                            s.menu_domid, 
                            s.menu_group, 
                            s.menu_order, 
                            s.menu_type, 
                            s.menu_active, 
                            s.image 
                FROM        system.sys_menu s                            
                WHERE       0 < (SELECT COUNT(*) FROM system.sys_menu sm WHERE   sm.parent = s.id )
                ORDER BY    menu_order";
                //select everything that has at least one item in its submenu
        
        return $this->db->query($sql)->result_array();
        
    }
    
    public function get_controllers()
    {
        
        $sql = "SELECT   c.controller_name, c.controller_id  
                FROM     permissions.lu_controller c  
                ORDER BY c.controller_name";

        return $this->db->query($sql)->result_array();
                
    }
    
    /**
    * also need controller id: method names are not unique
    * 
    * @param mixed $m_name
    * @param mixed $c_id
    */
    public function get_method_id_by_name($m_name,$c_id)
    {
		$sql="SELECT method_id FROM permissions.method m   WHERE m.method_name = ? AND m.controller_id = ? LIMIT 1";
        return $this->db->query($sql,array($m_name,$c_id))->result_array();
    }
    
    /**
    * controller names are unique
    * 
    * @param mixed $c_name
    */
    public function get_controller_id_by_name($c_name)
    {
		$sql="SELECT controller_id FROM permissions.lu_controller c   WHERE c.controller_name=? LIMIT 1";
        return $this->db->query($sql,$c_name)->result_array();
    }
 
    public function get_methods($controller)
    {// @author Sam
        //method groups do not exist anymore
        //instead get all methods for this controllerid
        $sql = "SELECT      m.method_name, m.method_id, m.controller_id   
                FROM        permissions.method m  
                WHERE       m.controller_id = ? ";
        
 
        return $this->db->query($sql,$controller)->result_array();
                
    }
 
    public function update_rolemenu($role,$menu,$view,$update)
    {
        $params = array($role,$menu,$view,$update);
        $sql = "SELECT permissions.update_role_menu(?,?,?,?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->update_role_menu;
    }
     /**
     * add  role from that method/controller pair
    * @access private
    * @author Sam Bassett
    * 
    * @param int $role
    * @param str $method
    * @param str $ctr
    */   
    public function update_rolemethod($role,$method,$ctr)
    {
        $params = array($role,$method,$ctr);
        $sql = "SELECT permissions.update_role_method(?,?,?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
    	//$this->_track_changes("permissions","handle_method_controller",array($method,$ctr));
        $this->_track_changes("permissions",'update_role_method',$role,$method,$ctr);
        
        return $result->update_role_method;
    }
    
    /**
    * remove role from that method/controller pair
    * @access private
    * @author Sam Bassett
    * 
    * @param int $role
    * @param str $method
    * @param str $ctr
    */
    public function delete_rolemethod($role,$method,$ctr)
    {
        $params = array($role,$method,$ctr);
        $sql = "SELECT permissions.delete_role_method(?,?,?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        
    	//$this->_track_changes("permissions","handle_method_controller",array($method,$ctr));
        $this->_track_changes("permissions",'delete_role_method',$role,$method,$ctr);
        
        return $result->delete_role_method;
        
        
        
    }
    
    public function json_getlocations()
    {//
        $q=$this->db->query("SELECT * from permissions.location  WHERE deleted_flag='f'");
        return $q->result_array();
    }
    
    public function json_get_users_roles($u_or_r,$name)
    {
        return null;
        //This is depreciated, with new permissions model
        //use get_user_assignments
        //or get_methods
        //
        if($u_or_r=='u')$u_or_r_sql=" and u.name='".$name."'";
        if($u_or_r=='r')$u_or_r_sql=" and r.name='".$name."'";
        
        $sql=
        "SELECT         user_role_id,u.user_id,r.role_id,u.name uname,r.name rname,'' AS del  
         FROM           permissions.user as u ".
        "INNER JOIN     permissions.user_role as ur 
        ON              ur.user_id=u.user_id 
        INNER JOIN      permissions.lu_role as r 
        ON              r.role_id=ur.role_id 
        WHERE           ur.deleted_flag='f' ".$u_or_r_sql;
        $q=$this->db->query($sql)->result_array();
        foreach($q as $i=>$v)
            $q[$i]['del']='<img src="http://dev.endeavor.servilliansolutionsinc.com/assets/images/delete.png" />';
        return $q;
    }
    
    public function json_get_roles_locations($r_or_l,$name)
    {//TODO: fix this with new permissions schema
        if($r_or_l=='r')$r_or_l_sql=" and r.name='".$name."'";
        if($r_or_l=='l')$r_or_l_sql=" and l.location_name='".$name."'";
        $sql=
        "SELECT     rl.role_location_id, r.role_id,r.name rolename,l.location_id,l.location_ip,l.location_name 
                    AS locationname,l.location_address,effective_range_start ,effective_range_end ,
                    '<input onclick=o_roles_locations.add_delete_index(
                                '|| rl.role_location_id ||') 
                                    type=checkbox id='|| rl.role_location_id ||' />' AS del 
        FROM        permissions.lu_role as r 
        INNER JOIN permissions.role_location as rl 
        ON          rl.role_id=r.role_id 
        INNER JOIN permissions.location as l 
        ON          l.location_id=rl.location_id  
        WHERE       l.deleted_flag='f' ".$r_or_l_sql;
        $q=$this->db->query($sql)->result_array();
        //foreach($q as $i=>$v)
        //    $q[$i]['del']='<img src="http://dev.endeavor.servilliansolutionsinc.com/assets/images/delete.png" />';
        return $q;
    }

    //update Functions
    public function json_update_users_roles($user_id,$role_id)
    {
        $query = $this->db->query('SELECT permissions.update_users_roles (?,?)', array($user_id,$role_id));        
    }
    
    public function json_update_roles_locations($role_id,$location_id,$sdate,$edate)
    {
        $query = $this->db->query('SELECT permissions.update_roles_locations (?,?,?,?)', array($role_id,$location_id,$sdate,$edate));        
    }
    
    public function json_update_roles_groups($role_id,$group_id)
    {
        $query = $this->db->query('SELECT permissions.update_roles_groups (?,?)', array($role_id,$group_id));        
    }   
                                                                         
    public function json_update_roles_groups_access($role_group_id,$item,$v)
    {
        $query = $this->db->query('SELECT permissions.update_roles_groups_access (?,?,?)', array(intval($role_group_id),$item,$v));        
    }
    
    public function json_update_role_location_sdate($date,$role_location_id)
    {
        $query = $this->db->query('SELECT permissions.update_roles_locations_dates (?,?,?)', array($date,$role_location_id,'s'));        
    }
    
    public function json_update_role_location_edate($date,$role_location_id)
    {
        $query = $this->db->query('SELECT permissions.update_roles_locations_dates (?,?,?)', array($date,$role_location_id,'e'));        
    }
    
    //Delete Functions
    public function json_delete_users_roles($user_role_id)
    {
        $query = $this->db->query('SELECT permissions.delete_users_roles (?)', array($user_role_id));        
    }
    
    public function json_delete_roles_locations_multiple($roles_locations_ids)
    {
        $A=split(",",$roles_locations_ids);
        for($i=0;i<count($A);$i++) 
            $this->db->query('SELECT permissions.delete_roles_locations(?)', array($A[$i]));            
        
    }
    
    public function json_delete_roles_groups($role_group_id)
    {
        $query = $this->db->query('SELECT permissions.delete_roles_groups (?)', array($role_group_id));        
    }
    
    public function delete_user($u_id,$mod)
    {
        $sql = 'SELECT permissions.delete_user(?,?)' ;
        $params = array($u_id,$mod);
        $query= $this->db->query($sql, $params);          
        $result  = $query->first_row();
        return $result->delete_user;
        /*
        BEGIN
    LOCK TABLE "permissions".user IN SHARE MODE;
    UPDATE "permissions".user 
    SET deleted_flag='t' , 
            deleted_by = _mod, 
            deleted_on = LOCALTIMESTAMP
    WHERE user_id = u_id;

    IF NOT FOUND THEN 
        RETURN -2;
    END IF;

    RETURN 1;
END

        */
    }
    
    public function json_delete_role($r_id)
    {
        $query = $this->db->query('SELECT permissions.delete_role(?)', array($r_id));        
    
    }
    
    public function json_delete_location($l_id)
    {
        $query = $this->db->query('SELECT permissions.delete_location(?)', array($l_id));        
    
    }
    
    //Create new Items
    public function json_save_or_new_location($location_id,$ip,$name,$address)                                                   
    {
        if($location_id!='') 
            $this->db->query('SELECT permissions.save_location (?,?,?,?)', array($location_id,$ip,$name,$address));        
        else    
            $this->db->query('SELECT permissions.new_location (?,?,?)', array($ip,$name,$address));        
    
    }
    
    /**
    * this stored procedure checks for a record with existing person id
    *	hence will not create duplicate acocunts for the same person, only noe userid per person
    *   hence if this is an"update password", we do not need user id
    * 
    * WILL RETURN -1 IF PERSON ID NOT FOUND
    * return -3 if user login already in use / not unique
    * 
    * @param mixed $creator
    * @param mixed $person_id
    * @param mixed $default_org
    * @param mixed $login
    * @param mixed $enc_password
    * @param mixed $owned_by
    */
    public function insert_user($creator,$person_id,$default_org,$login,$enc_password,$owned_by)
    {                                                                        

        $params = array($creator,$person_id,$default_org,$login,$enc_password,$owned_by);
        $sql = 'SELECT permissions.insert_user(?,?,?,?,?,?)';
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->insert_user;
    }
    /**
    * returns -3 if login name already in use
    * 
    * @param mixed $user_id
    * @param mixed $login
    * @param mixed $enc_password
    * @param mixed $modifiedby
    */
    public function update_login_password($user_id,$login,$enc_password,$modifiedby)
    {
		$params = array($user_id,$login,$enc_password,$modifiedby);
        $sql = 'SELECT permissions.update_login_password(?,?,?,?)';
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->update_login_password;
		
    }
    
    public function get_postal_id($postal_value)
    {
        //either returns id of existing postal code, 
        //or creates new lu and returns its id
        $params = array($postal_value);
        $sql = "SELECT public.get_postal_id(?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->get_postal_id;        
    }
    
    public function insert_entity_contact($creator,$entity_id,$type,$value,$owned_by)
    {
        $params = array($creator,$entity_id,$type,$value,$owned_by);
        $sql = "SELECT public.insert_entity_contact(?,?,?,?,?)";
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->insert_entity_contact;  
        
    }
    
    public function insert_assignment($creator,$user_id,$role_id,$org_id,$end_date,$owner)
    {//start date defualt is today
        $params = array($creator,$user_id,$role_id,$org_id,$end_date,$owner);
        $sql = 'SELECT permissions.insert_assignment(?,?,?,?,?,?)';
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->insert_assignment;
        
    }
    
    //update_assignment
    public function update_assignment($mod,$assn_id,$start_date,$end_date)
    {//TODO: this
        $params = array($mod,$assn_id,$start_date,$end_date);
        $sql = 'SELECT permissions.update_assignment(?,?,?,?)';
        $query = $this->db->query( $sql,$params);   
        $result  = $query->first_row();
        return $result->update_assignment;
        
    }
    
    public function search_assignments($role_id,$org_id,$user_id)
    {
        //four cases
        if($role_id == -1 && $org_id == -1)
        {   //get all for user
            $sql = "SELECT      a.effective_range_start, a.effective_range_end, a.org_id, a.role_id, a.assignment_id, 
                                o.org_name, r.role_name
                    FROM        permissions.assignment a 
                    INNER JOIN  public.entity_org o         ON o.org_id  = a.org_id  
                    INNER JOIN  permissions.lu_role r          ON r.role_id = a.role_id 
                    WHERE       a.deleted_flag = 'f'  AND o.deleted_flag=FALSE 
                    AND         a.user_id = ?             AND ".userorg_can_view('a')."=TRUE
                ";//"    GROUP BY a.assignment_id   ";
            return $this->db->query($sql,$user_id)->result_array();
        }        
        if($role_id == -1 && $org_id != -1)
        {            
            $sql = "SELECT      a.effective_range_start, a.effective_range_end, a.org_id, a.role_id, a.assignment_id, 
                                o.org_name, r.role_name
                    FROM        permissions.assignment a 
                    INNER JOIN  public.entity_org o         ON o.org_id  = a.org_id  
                    INNER JOIN  permissions.lu_role r          ON r.role_id = a.role_id 
                    WHERE       a.user_id = ? 
                    AND         a.org_id = ?  
                    AND         a.deleted_flag = 'f'  AND ".userorg_can_view('a')."=TRUE
                   ";// GROUP BY a.assignment_id ";
            return $this->db->query($sql,array($user_id,$org_id))->result_array();
        }
        if($role_id != -1 && $org_id == -1)
        {
            $sql = "SELECT      a.effective_range_start, 
                                a.effective_range_end, 
                                a.org_id, 
                                a.role_id, 
                                a.assignment_id, 
                                o.org_name, 
                                r.role_name
                    FROM        permissions.assignment a 
                    INNER JOIN  public.entity_org o         ON o.org_id  = a.org_id  
                    INNER JOIN  permissions.lu_role r          ON r.role_id = a.role_id 
                    WHERE       a.user_id = ?                     
                    AND         a.role_id = ?  
                    AND         a.deleted_flag = 'f'  AND ".userorg_can_view('a')."=TRUE
                   ";// GROUP BY a.assignment_id ";
            return $this->db->query($sql,array($user_id,$role_id))->result_array();
            
       }
        if($role_id != -1 && $org_id != -1)
        {//all for both
            $sql = "SELECT      a.effective_range_start, a.effective_range_end, a.org_id, a.role_id, a.assignment_id, 
                                o.org_name, r.role_name
                    FROM        permissions.assignment a 
                    INNER JOIN  public.entity_org o         ON o.org_id  = a.org_id  
                    INNER JOIN  permissions.lu_role r          ON r.role_id = a.role_id 
                    WHERE       a.user_id = ? 
                    AND         a.role_id = ?  
                    AND         a.org_id = ? 
                    AND         a.deleted_flag = 'f'         AND ".userorg_can_view('a')."=TRUE              
                  ";//  GROUP BY a.assignment_id ";
            return $this->db->query($sql,array($user_id,$role_id,$org_id))->result_array();

        }   
        
        // $params = ;
    }
    
    public function json_save_or_new_role($role_id,$name,$limit_location,$parent_role_id,$location_id,$sdate)   
    {
        $p_r_id;
        if($parent_role_id=='')
            $p_r_id=-1;
        else 
            $p_r_id=$parent_role_id;
        
        if($role_id!='') 
            $this->db->query('SELECT permissions.save_role (?,?,?,?,?)', 
                        array($role_id,$name,$limit_location,$p_r_id,'1'));        
        else    
            $this->db->query('SELECT permissions.new_role (?,?,?,?,?,?)', 
                        array($name,$limit_location,$p_r_id,'1',intval($location_id),$sdate));        
    }
    
    public function check_login($username,$password,$ip,$captcha_input=null,$fb_id,$fb_email)  
    {    
        $curlloginpass=$this->input->get_post("curlloginpass");
        
        //setting defaults
		$cap_required=false;
    	$userdata=array();
        $userdata['last_login_date']=null;
        $orgid=null;
		$org_type=null;
		$valid_org=null;
		$cap_valid=true;//assume we dont use it at first
		$fail_type=null;// there was no fail
		$token= null;
		$userid=null; 
        
    	//first insert a failed atteMPT, assuming this one fails for securty resasons, we will reset later if success
    	$fail_sql = "SELECT  permissions.insert_failed_login_ip(?)";
			
    	//first count how many times they have tried to log in from this ip recently - non deleted attempts
		$insert=$this->db->query($fail_sql,array($ip))->result_array();
		
		$number_of_login_tries_before_this=$this->db->query(
        		"SELECT COUNT(*) AS count from permissions.attempted_login WHERE ip=? AND deleted_flag=false"
        		,array($ip))->first_row()->count;
        if($number_of_login_tries_before_this >= FAILED_LOGINS_ALLOWED)
        {
			$cap_required=true;
			//echo "CAP REQ!!!!";
        }

        if($curlloginpass||$_SERVER["HTTP_USER_AGENT"]=="Spectrum/2.1")
        {
            $cap_required   =false;        
            $cap_valid      =true;
            $curlloginpass=true;
        }
        

        
    	if($cap_required) 
    	{
    		
			//test capcha first
			//if capcha not set, then maybe cookies not enabled?
			//echo md5($captcha_input) ."::".$_SESSION['md5_captcha_login'];
			if($captcha_input && isset($_SESSION['md5_captcha_login']) && md5($captcha_input) ==$_SESSION['md5_captcha_login'] )
			{
				//it is good, so now we can check user pass
				$cap_valid=true;//redundant but who cares
			}
			else
			{              
				$cap_valid=false;// important!!
			}
		}
        if($cap_valid)
        {
			//if cap valid or not required
		
			$this->load->library('encrypt');
		    // if(!isset($_SESSION['number_of_failed_logins']))$_SESSION['number_of_failed_logins']=0;
		    //get password and validate
		    $r=$this->db->query("select * from permissions.login_check_get_password(?);",array($username))->result_array();
		    //always returns one row so we are safe.returns null if user name not found
		    $_enc_password=$r[0]["login_check_get_password"];
		     //added null testso that blank login form will not pass this point
		    
		    $cap_required='f'; 
		        
           if(!$fb_id)
            {
                if(	$_enc_password==null)
		        {
				    //no password found for this user name, so it must be that no user exists
				    $token= -1;
		 		    $fail_type='user';//user name not found
		        }
		        else if( $password!=$this->encrypt->decode($_enc_password,SSI_ENC_KEY))
		        {
				    $token= -1;
		 		    $fail_type='pass';
		        }
          	}	
            else
            {
            	 
                if($username!=""   && $password!=$this->encrypt->decode($_enc_password,SSI_ENC_KEY))
                {
                	 
                    $token= -1;
                    $fail_type='pass';
                }
                 
            }
				
		}
		else
		{
			//cap input was badbad
            $token= -1; 
			$fail_type='cap';
		}
    	
    	//regardless of why we succeeded or failed, handle both cases here
    	
			//if login was valid, token would be null not -1
            
 
    	if($token!=-1)
    	{
			//so login was valid
 
    		//delete all attempts for this ip
    		$fail_sql = "SELECT  permissions.delete_failed_login_ip(?)";
			
			$del=$this->db->query($fail_sql,array($ip))->result_array();
    		
			
			//
			//name of this function is misleading, really it just saves their token and session information. 
			//sort of a 'check in' process for login. also it gets user and org ids 
			
            
            
                                         
            if($username=="" && $password=="") 
            {       
                $sql = "SELECT permissions.login_check(?,?,?,?,?,?,?,?)";
                $result = $this->db->query($sql,array('-1','-1',$ip,session_id(),md5(mktime(time())),$fb_id,'',''))->result_array();
            }
            else
            {
                if($fb_id)
                {
                    $sql = "SELECT permissions.login_check(?,?,?,?,?,?,?,?)";
                    $result = $this->db->query($sql,array($username,'',$ip,session_id(),md5(mktime(time())),$fb_id,'',''))->result_array();
                }
                else
                {
                    $sql = "SELECT permissions.login_check(?,?,?,?,?)";
                    $result = $this->db->query($sql,array($username,'',$ip,session_id(),md5(mktime(time()).$username)))->result_array();                        
                }
                
                
            }
            
	        $resulttext=$result[0]["login_check"];
	        
			//get token, user, org
            
			list($token,$userid,$orgid,$fb_id) = explode(":",$resulttext);
            
            
            if($curlloginpass!=true)
                if($orgid=='-1')$token=-1;
	        //activate user
			if($token!=-1)
            {
            	
                $this->set_active_user(($userid<1) ? null : $userid );
                
            	//make sure activeorg hasnot been deleted. if so, return new, oand update sesion, otherwise do nothing
		        $orgid= $this->_check_org_onlogin($orgid);	
            	
            	
            	
            	
                $this->set_active_org(($orgid <1) ? null : $orgid );
                $this->set_active_fb_id( $fb_id);
                
                //get user information
                $userdata = $this->get_user_data($userid);
                $userdata = array_pop($userdata);
                
                //measure user
                $valid_user = $this->get_active_user();
                //if($valid_user) $this->update_last_login_date($valid_user);//no do this lat
                // login was a success,  and if last login date is not null, then update it

                //if last login date IS null for a valid user, then wait and update it after the getstarted screen 
                if($userdata['last_login_date'])
                    {$this->update_last_login_date($valid_user);}
                    
                $valid_org=$this->is_active_org_valid();
                                      
                if($orgid )//this was checking valid_org: incorrect: if org is not valid we still need to know type
                    {$org_type=$this->get_active_org_type();}    
                                
            }
                          
        }
        //if failtype has not been set, maybe set it here
        if(!$fail_type&&$userid==null) $fail_type='user';
        
           if( !$fail_type&& $orgid<0) $fail_type='org';
        //create return string
         
        $return = array(
        		'token'			  =>$token
         		,'last_login_date'=>$userdata['last_login_date']
        		,'org_is_valid'	  =>$valid_org
        		,'org_type'		  =>$org_type
        		,'org_id'		  =>$orgid 
                ,'fb_id'          =>$fb_id
        		,'cap_required'	  =>$cap_required    
        		,'fail_type'      =>$fail_type 
                ,'user_id'        =>$userid
                ,'user_exec'	  =>$this->get_is_user_org_executive()
                ,'session_id'     =>session_id()
                );
        		//added org_id for get_started screen: SB Sept 21 2011
 
        		
  	
        $_SESSION['last_login_date'] = $userdata['last_login_date'];//used for GETSTARTED screen
		return $return;
    }
    
    
    private function _check_org_onlogin($org)
    {
		$sql='SELECT deleted_flag FROM public.entity_org WHERE org_id=?';
		$flag = $this->db->query($sql,$org)->first_row()->deleted_flag;	
		
		if($flag=='f')
			return $org;
			
		
		//otherwise this was deleted	
		//so find a new org thats not deleted
		//and set that org to the users default
		$activeuser=$this->get_active_user();
			
		//and order  by date assigned: oldest created to newest
		$sql="SELECT a.org_id FROM permissions.assignment a INNER JOIN public.entity_org o ON o.org_id=a.org_id AND o.deleted_flag=false AND a.user_id=? AND a.deleted_flag=FALSE 
		ORDER BY  a.created_on ASC";
		$orgs = $this->db->query($sql,$activeuser)->result_array();	
			
			
		//arbitrarily pick the first one 
		$org = (!$orgs || !count($orgs))? -1 : $orgs[0]['org_id'];
		
		
		if($org && $org>0)//KEY t oavoid fk errors on curl from frontend
			$this->save_default_org($activeuser,$org);
			
			
		return $org;
    }
    
    public function is_active_org_valid()
    {
        $org=$this->get_active_org();
        if($org==null)return false;
        
		$sql="SELECT is_valid FROM public.entity_org WHERE org_id=?";
        $query = $this->db->query($sql,$org)->first_row();	

        if($query->is_valid =='true'||$query->is_valid=='t'||$query->is_valid===true  ) return true;
        else return false;
    }
    /**
    * @author sam
    * validate an org
    * defaults to true and active org if none given
    * 
    * @param mixed $v
    * @param mixed $org
    */
    public function set_active_org_valid($v='t',$org=null)
    {
    	if($org==null)$org=$this->get_active_org();
    	$params=array($org,$v);
		$sql="SELECT permissions.set_active_org_valid(?,?)";
        $query = $this->db->query($sql,$params)->first_row();
    }
 
    /**
    * update the user password
    * 
    * @deprecated
    * 
    * now we just use retrieve_password
    * 
    * @param mixed $username
    * @param mixed $email
    * @param mixed $password_md5
    */
    public function reset_password($username,$email,$password_md5)
    {        
        $query = $this->db->query("SELECT permissions.reset_password(?,?,?)",array($username,$email,$password_md5));
        return $query->first_row()->reset_password;
    }
    
    
    /**
    * get the password for this username, as long as the email is linked to the user
    * 
    * @param mixed $username
    * @param mixed $email
    */
    public function retrieve_password($username,$email)
    {
        $query = $this->db->query("SELECT permissions.retrieve_password(?,?)",array($username,$email));
        return $query->first_row()->retrieve_password;
    }
    
    
    
    /**
    * find the username for this user based on email only
    * 
    * @param mixed $email
    * @return mixed
    */
    public function retrieve_username($email)
    {        
    	$sql="	
    	SELECT login  from permissions.user where user_id IN 
		(
			select user_id
			from public.entity_contact ec 
			inner join public.lu_contact_type ct on ct.type_id=ec.contact_type and ec.is_active=true
			inner join public.contact_method cm on cm.contact_method_id=ec.contact_method_id
			inner join public.entity_person p on p.entity_id=ec.entity_id
			inner join  permissions.user u on u.person_id=p.person_id
			where (ct.type_id=1 OR ct.type_id=2)
			and cm.value=?
		)";
        $result = $this->db->query($sql,array($email))->result_array();
        //var_dump($result);
        if(!count($result)) return null;
        else return $result[0]['login'];
        
    }
    /**
    * defaults to active user. also can take user id as input
    * 
    * @param mixed $user
    */
    public function update_last_login_date($user=null)
    {        
    	if(!$user) { $user=$this->permissions_model->get_active_user(); }
        $query = $this->db->query("SELECT permissions.update_last_login_date(?)",array($user));
        return $query->first_row()->update_last_login_date;
    }
    public function get_last_login_date($user=null)
    {        
    	if(!$user) { $user=$this->permissions_model->get_active_user(); }
        $query = $this->db->query("SELECT last_login_date FROM permissions.user WHERE user_id=? LIMIT 1",array($user));
        return @$query->first_row()->last_login_date;
    }
    /**
    * all roles except signing authority.  that must be done in a special way: get started screen
    * or motion/votes/etc.
    * 
    * @param mixed $org_type
    */
    public function get_allowed_roles_by_org($org_type)
    {
    	$sql="SELECT r.role_name, r.role_id  
    			FROM permissions.lu_role r 
    			INNER JOIN permissions.lu_role_org_allowed ro ON r.role_id = ro.role_id AND ro.org_type_id = ? 
    			WHERE r.role_id != 16
				ORDER BY role_name ";
		return $this->db->query($sql,$org_type)->result_array();
    }
    
    
    public function logout($user=false)
    {
    	if(!$user)
    		$user=$this->permissions_model->get_active_user();
    	$this->set_active_user(null);
    	$this->set_active_org(null);
    	$sql="SELECT permissions.logout(?)";
		return $this->db->query($sql,$user)->result_array();
    }
    
    
    //Facebook
    public function get_facebook_friends($fb_id)
    {
        $search_criteria='';
        if($this->input->get_post("query"))$search_criteria=$this->input->get_post("query");
        
        $q=
        "select fb_friend_id,fb_parent_id,fb_child_id as fb_id,fb_child_fname as person_fname,fb_child_lname as person_lname
            from   permissions.fb_friend fbf
            where fbf.fb_parent_id=?
            and 
            (       
                    lower( fb_child_fname::varchar) like '%'||lower(?)||'%'  
                or  lower( fb_child_lname::varchar) like '%'||lower(?)||'%'  
            )
        ";
        $result=$this->db->query($q,array($fb_id,$search_criteria,$search_criteria))->result_array();
        return $result;
    }                  
}

?>
