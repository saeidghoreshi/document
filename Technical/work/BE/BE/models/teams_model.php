<?php
require_once('./endeavor/models/org_model.php');
class Teams_model extends Org_model
{
    
    public function __construct()
    {
        parent::__construct();
        //$this->DB = $this->load->database('default', true);
        //$this->update_user_activity();
    }
    
    public function team_list()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------
        return $this->db->query("select * from public.get_teams(?,?,?,?,?);",array($a_u_id,$a_o_id,'teams','window_managerosters',1))->result_array();
    }
    public function get_seasons_for_league_or_team()
    {
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------
        return $this->db->query("select * from public.get_seasons_for_league_or_team(?);",array($a_o_id))->result_array();                        
    }
    
    // Roster Functions
    public function _get_team_managers($team_org_id)
    {
        return $this->db->query("select * from public.get_org_managers_and_email(?);",array($team_org_id))->result_array();
    }
    public function get_rosters($team_id,$active,$season_id,$a_user,$a_org)
    {
        return $this->db->query("select * from public.get_rosters(?,?,?,?,?,?,?,?);",array($team_id,$active,$season_id,$a_user,$a_org,'teams','json_get_rosters',1))->result_array();
    }    
    public function get_roster_persons_________($roster_id,$a_user,$a_org)
    {
        return $this->db->query("select * from public.get_roster_persons(?,?,?,?,?,?);",array($roster_id,$a_user,$a_org,'teams','json_get_roster_persons',1))->result_array();
    }
    /**
    * each team has exaclty one roster per season
    * so just get all people for this combination
    * 
    * @param mixed $team_id
    * @param mixed $season_id
    */
    public function get_roster_persons($team_id,$season_id)
    {
    	$params=func_get_args();
        $query = (string)$this->input->get_post('query');//from ext grid searchbar
		$wh='';
		if($query)//it may not exist
		{
			$params[]=$query;
			$params[]=$query;//add twice for checking both names
		
			$wh = "AND  
	                (
	                        lower(p.person_fname) like '%'||lower(?)||'%'
	                    OR  lower(p.person_lname) like '%'||lower(?)||'%'
	                )   ";
		}
		$sql="SELECT    tr.team_id, tr.season_id, 
						t.org_id, t.team_name, 
						tr.team_roster_id, 
                        rp.person_id, rp.roster_person_id ,rp.effective_range_start,rp.status_id,stat.type_name status_name,rp.comment,
						p.person_fname, p.person_lname, p.person_birthdate, 
						p.person_gender, p.entity_id 
				FROM  public.roster_person rp 
				INNER JOIN public.team_roster tr ON tr.team_roster_id = rp.team_roster_id   
				INNER JOIN public.entity_person p ON rp.person_id = p.person_id 				
				INNER JOIN public.team t ON t.team_id = tr.team_id 
                LEFT OUTER JOIN public.lu_roster_person_status stat on stat.type_id=rp.status_id
                
                WHERE rp.deleted_flag='f'     AND p.deleted_flag=FALSE 
                AND tr.team_id=? AND tr.season_id=?
                ".$wh."  
				ORDER BY p.person_lname ASC 
                ";
		return $this->db->query($sql,$params )->result_array();		
    }
    public function accept_roster_person($roster_person_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------
        
        $sql="SELECT  public.accept_roster_person(?,?,?)";
        $params=array($roster_person_id,$a_u_id,$a_o_id);

        return $this->db->query($sql,$params)->first_row()->accept_roster_person;
    }
    public function decline_roster_person($roster_person_id,$comment)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        $sql="SELECT  public.decline_roster_person(?,?,?,?)";
        $params=array($roster_person_id,$comment,$a_u_id,$a_o_id);
        //--------------------------------------------------
        return $this->db->query($sql,$params)->first_row()->decline_roster_person;
    }
    
    //**********************
    
    public function delete_roster_person($rp_id,$user)
    {
    	$params=array($rp_id,$user);
    	$sql="SELECT public.delete_roster_person(?,?)";
		return $this->db->query($sql,$params)->first_row()->delete_roster_person;
    }
    public function assign_roster_person($team_id,$season_id,$person_id,$user,$org,$status)
    {
    	$params=array($team_id,$season_id,$person_id,$user,$org,$status);
    	$sql="SELECT public.assign_roster_person(?,?,?,?,?,?)";
		return $this->db->query($sql,$params)->first_row()->assign_roster_person;
    }
    public function new_rosterperson($roster_id,$fname,$lname,$bdate,$gender,$homef,$workf,$cellf,$email,$address,$city,$province,$country,$postal,$a_user,$a_org)
    {
        return $this->db->query("select public.new_roster_person(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);"
        ,array($roster_id,$fname,$lname,$bdate,$gender,$homef,$workf,$cellf,$email,$address,$city,$province,$country,$postal,$a_user,$a_org))->result_array();
    } 
    public function update_rosterperson($roster_id,$person_id,$fname,$lname,$bdate,$gender,$homef,$workf,$cellf,$email,$address,$city,$province,$country,$postal,$a_user,$a_org)
    {
        return $this->db->query("select public.update_roster_person(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);"
        ,array($roster_id,$person_id,$fname,$lname,$bdate,$gender,$homef,$workf,$cellf,$email,$address,$city,$province,$country,$postal,$a_user,$a_org))->result_array();
    }
    public function get_person($person_id)
    {
        return $this->db->query("select * from public.get_person(?);",array($person_id))->result_array();
    }   
    // END Roster Functions
    

    public function get_team_id()
    {
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------
        return $this->db->query("select team_id from public.team where org_id=?",array($a_o_id))->result_array();    
    }
    
    
    public function get_team_id_byorg($org_id)
    {
        return $this->db->query("SELECT team_id FROM public.team WHERE org_id=?",array($org_id))->first_row()->team_id;    
    }
    
    
    /**
    * get all data for team
    * 
    * updated feb 2012 to also return team org id and entity id
    * 
    * @param mixed $team_id
    */
    public function get_team_details($team_id)
    {
		$sql = "SELECT t.team_id,t.team_name , l.league_id, l.league_name , ot.org_id, ot.entity_id
				FROM  public.team t 
		       INNER JOIN public.entity_org ot ON t.org_id = ot.org_id AND t.team_id=?  
		       INNER JOIN public.entity_relationship er ON er.child_id=ot.entity_id 	
		       INNER JOIN public.entity_org op ON op.entity_id = er.parent_id 
		       INNER JOIN public.league l ON l.org_id = op.org_id  ";
		return $this->db->query($sql,array($team_id))->result_array(); 
    }
    
    //only if they have a manager
    public function get_team_info($team_id)
    {
		$sql="SELECT r.role_name ,u.person_id ,a.org_id ,u.login,u.password 
				, ep.person_fname,ep.person_lname,ep.person_gender,ep.person_birthdate,t.team_name,cm1.value AS email 
				FROM permissions.assignment a 
				INNER JOIN permissions.lu_role r 
					ON a.role_id = r.role_id AND r.role_id = 4  AND a.deleted_flag='f' 
				INNER JOIN permissions.user u ON u.user_id = a.user_id 
				INNER JOIN public.entity_person ep ON u.person_id = ep.person_id 
				INNER JOIN public.team t ON t.org_id = a.org_id AND t.team_id=?   
 				LEFT OUTER JOIN  public.entity_address ea            ON ea.entity_id = ep.entity_id   AND ea.is_active = 't' AND ep.deleted_flag = 'f' 
                LEFT OUTER JOIN  public.address ad                    ON ea.address_id= ad.address_id   AND    ad.deleted_flag = 'f' 
                LEFT OUTER JOIN  public.lu_address_postal lup        ON lup.postal_id= ad.address_postal
                LEFT OUTER JOIN  public.entity_contact ec1           ON ec1.entity_id = ep.entity_id          AND ec1.contact_type = 1   AND   ec1.is_active = 't' 
                LEFT OUTER JOIN  public.contact_method cm1           ON cm1.contact_method_id = ec1.contact_method_id  
                LEFT OUTER JOIN  public.entity_contact ec2           ON ec2.entity_id = ep.entity_id          AND ec2.contact_type = 2 AND   ec2.is_active = 't' 
                LEFT OUTER JOIN  public.contact_method cm2           ON cm2.contact_method_id = ec2.contact_method_id  
                LEFT OUTER JOIN  public.entity_contact ec3           ON ec3.entity_id = ep.entity_id          AND ec3.contact_type = 3 AND   ec3.is_active = 't' 
                LEFT OUTER JOIN  public.contact_method cm3           ON cm3.contact_method_id = ec3.contact_method_id  
                LEFT OUTER JOIN  public.entity_contact ec4           ON ec4.entity_id = ep.entity_id          AND ec4.contact_type = 4 AND   ec4.is_active = 't' 
                LEFT OUTER JOIN  public.contact_method cm4           ON cm4.contact_method_id = ec4.contact_method_id   
				WHERE a.effective_range_start < LOCALTIMESTAMP AND 
							(a.effective_range_end > LOCALTIMESTAMP OR a.effective_range_end IS NULL)  
";
        return $this->db->query($sql,array($team_id))->result_array(); 		
    }
    
    
    public function get_team_info_manager($team_id)
    {
		$sql="SELECT r.role_name ,u.person_id ,a.org_id ,u.login,u.password
					, ep.person_fname,ep.person_lname,ep.person_gender,ep.person_birthdate,t.team_name,cm1.value AS email 
					,eol.org_name AS league_name,eol.org_logo 
				FROM permissions.assignment a 
				INNER JOIN permissions.lu_role r
					ON a.role_id = r.role_id AND r.role_id = 4  AND a.deleted_flag='f' 
				INNER JOIN permissions.user u ON u.user_id = a.user_id 
				INNER JOIN public.entity_person ep ON u.person_id = ep.person_id 
				INNER JOIN public.team t ON t.org_id = a.org_id AND t.team_id=?   
				INNER JOIN public.entity_org eot ON eot.org_id = t.org_id 
				INNER JOIN public.entity_relationship er ON er.child_id = eot.entity_id 
				INNER JOIN public.entity_org eol ON eol.entity_id = er.parent_id 
				
 				LEFT OUTER JOIN  public.entity_address ea            ON ea.entity_id = ep.entity_id   AND ea.is_active = 't' AND ep.deleted_flag = 'f' 
                LEFT OUTER JOIN  public.address ad                    ON ea.address_id= ad.address_id   AND    ad.deleted_flag = 'f' 
                LEFT OUTER JOIN  public.lu_address_postal lup        ON lup.postal_id= ad.address_postal
                LEFT OUTER JOIN  public.entity_contact ec1           ON ec1.entity_id = ep.entity_id          AND ec1.contact_type = 1   AND   ec1.is_active = 't'
                LEFT OUTER JOIN  public.contact_method cm1           ON cm1.contact_method_id = ec1.contact_method_id  
                LEFT OUTER JOIN  public.entity_contact ec2           ON ec2.entity_id = ep.entity_id          AND ec2.contact_type = 2 AND   ec2.is_active = 't'
                LEFT OUTER JOIN  public.contact_method cm2           ON cm2.contact_method_id = ec2.contact_method_id  
                LEFT OUTER JOIN  public.entity_contact ec3           ON ec3.entity_id = ep.entity_id          AND ec3.contact_type = 3 AND   ec3.is_active = 't'
                LEFT OUTER JOIN  public.contact_method cm3           ON cm3.contact_method_id = ec3.contact_method_id  
                LEFT OUTER JOIN  public.entity_contact ec4           ON ec4.entity_id = ep.entity_id          AND ec4.contact_type = 4 AND   ec4.is_active = 't'
                LEFT OUTER JOIN  public.contact_method cm4           ON cm4.contact_method_id = ec4.contact_method_id   
				WHERE a.effective_range_start < LOCALTIMESTAMP AND (a.effective_range_end > LOCALTIMESTAMP OR a.effective_range_end IS NULL)  ";
		
		
        return $this->db->query($sql,array($team_id))->result_array(); 	
    }
    
    
    public function get_teams()
    {    
        $sql = "SELECT      x.team_id,  x.team_name, d.division_id,  d.division_name 
                FROM        public.team x 
                LEFT OUTER JOIN public.league_division d    ON x.division_id = d.division_id    AND d.deleted_flag = 'f'                            
                WHERE       x.deleted_flag = 'f' ORDER BY team_name
                 AND( t.team_status_id IS NULL OR t.team_status_id =1)";//  AND ".USER_CAN_ACCESS." ";  
                //LEFT OUTER JOIN  public.entity_org e        ON          e.org_id = x.org_id

        return $this->db->query($sql)->result_array();
    }
    
    
    
    public function get_season_teams($season,$name=null)
    {
    	$params=array($season,$season);
    	$where='';
    	if(!$name)
    	{
			$name = $this->input->get_post('query');
    	}
    	if($name)
    	{
			$where=" AND lower(t.team_name)  LIKE '%'||lower(?)||'%'  ";
			$params[]=$name;
    	}
    	
		$sql="SELECT t.team_id,t.org_id,t.team_name,ld.division_name,ld.division_id,ld.parent_division_id,ts.season_id  
				,pd.division_name as parent_division_name 
				,(  SELECT COUNT(rp.person_id) 
					FROM public.roster_person rp 
					INNER JOIN public.team_roster tr ON rp.team_roster_id=tr.team_roster_id 
							AND tr.team_id=t.team_id AND tr.season_id = ? AND rp.deleted_flag='f' ) 
				AS roster_count 
				,(SELECT COUNT(DISTINCT person_id) FROM permissions.assignment a 
 					INNER JOIN permissions.user u ON a.user_id=u.user_id AND a.org_id=t.org_id  
 					AND a.deleted_flag='f' AND u.deleted_flag='f'  )
 				AS user_count 
				FROM public.team t 
				INNER JOIN public.team_season ts ON ts.team_id = t.team_id      AND t.deleted_flag=FALSE 
				INNER JOIN public.entity_org ot ON ot.org_id=t.org_id AND ot.deleted_flag=FALSE                
				LEFT OUTER JOIN public.team_season_division tsd ON tsd.team_season_id = ts.id       
				LEFT OUTER JOIN public.season_division sd ON sd.id = tsd.season_division_id      
				   
				LEFT OUTER JOIN public.league_division ld ON ld.division_id = sd.division_id        
				LEFT OUTER JOIN public.league_division pd ON ld.parent_division_id = pd.division_id 
				WHERE t.deleted_flag=FALSE AND tsd.deleted_flag='f'  AND sd.deleted_flag='f' AND ts.deleted_flag='f' AND ts.season_id = ?  
				
				AND  ld.deleted_flag=FALSE 
				AND( t.team_status_id IS NULL OR t.team_status_id =1) 
				".$where."  
				ORDER BY t.team_name DESC";

        
		$teams= $this->db->query($sql,$params)->result_array();
		 
 
		 
		 
		 
		 return $teams;
    }
    /**
    * get all team managers, including contact info, for this team
    * 
    * @param mixed $team_id
    */
    public function get_team_managers($team_id)
    {
		$sql="SELECT t.team_id, t.org_id , t.team_name, u.user_id , u.person_id, p.person_fname, p.person_lname, p.entity_id ,
					(SELECT cm1.value FROM public.entity_contact ec1 INNER JOIN public.contact_method cm1 
					ON cm1.contact_method_id = ec1.contact_method_id  AND   ec1.is_active = 't'  AND ec1.contact_type = 1 AND ec1.entity_id =p.entity_id LIMIT 1   ) 
					AS email
					,(SELECT cm2.value  FROM public.entity_contact ec2 INNER JOIN public.contact_method cm2 
					ON cm2.contact_method_id = ec2.contact_method_id  AND   ec2.is_active = 't'  AND ec2.contact_type = 2 AND ec2.entity_id =p.entity_id LIMIT 1   ) 
					AS p_home
					,(SELECT cm3.value  FROM public.entity_contact ec3 INNER JOIN public.contact_method cm3 
					ON cm3.contact_method_id = ec3.contact_method_id  AND   ec3.is_active = 't'  AND ec3.contact_type = 3 AND ec3.entity_id =p.entity_id LIMIT 1   ) 
					AS p_work 
					,(SELECT cm4.value  FROM public.entity_contact ec4 INNER JOIN public.contact_method cm4 
					ON cm4.contact_method_id = ec4.contact_method_id  AND   ec4.is_active = 't'  AND ec4.contact_type = 4 AND ec4.entity_id =p.entity_id LIMIT 1   )
					 AS p_mobile 
			FROM public.team t 
			INNER JOIN permissions.assignment a ON a.org_id = t.org_id AND a.deleted_flag=FALSE 
			AND t.deleted_flag=FALSE  AND a.role_id = 4 --4 is lu_role for team manager 
			INNER JOIN permissions.user u ON a.user_id = u.user_id  
			INNER JOIN public.entity_person p ON p.person_id = u.person_id  
			WHERE t.team_id = ?";
		return $this->db->query($sql,$team_id)->result_array();
		
		
    }
    /**
    * teams for this league not assigned to any season
    * 
    */
    public function get_unassigned_teams($league_id,$name=null)
    {
    	$params=array($league_id,$name);
    	$where='';
    	
    	if($name)
    	{
			$where=" AND lower(t.team_name)  LIKE '%'||lower(?)||'%'  ";
    	}
    	$sql="SELECT t.team_id, t.org_id,t.team_name  , ot.entity_id , '-' AS roster_count 
    	            ,(SELECT COUNT(DISTINCT person_id) FROM permissions.assignment a 
 									INNER JOIN permissions.user u ON a.user_id=u.user_id AND a.org_id=t.org_id  
 									AND a.deleted_flag='f' AND u.deleted_flag='f'  ) 
 					AS user_count 
 									
    		  FROM public.team t  
    		  INNER JOIN public.entity_org ot ON ot.org_id=t.org_id AND t.deleted_flag=FALSE AND ot.deleted_flag=FALSE
			  WHERE   t.team_id NOT IN 
    		  		(SELECT ts.team_id FROM public.team_season ts  INNER JOIN 
    		  		public.season s ON s.season_id=ts.season_id  AND ts.deleted_flag=FALSE  AND s.deleted_flag=FALSE )
    		  	AND( t.team_status_id IS NULL OR t.team_status_id =1)				
	  		  AND ot.entity_id IN 
	  		  		( SELECT er.child_id FROM public.entity_relationship er 
								INNER JOIN public.entity_org ol ON er.parent_id=ol.entity_id 
								INNER JOIN public.league l ON l.org_id = ol.org_id AND l.league_id=?) 
			  ".$where."
    		ORDER BY t.team_name  ";
    		//INNER JOIN public.season_division sd ON d.season_division_id=sd.id AND sd.deleted_flag='f') 
    	
		return $this->db->query($sql,$params)->result_array();
    }
    /**
    * teams assigned to this season that are NOT assigned to any division
    * 
    * @param mixed $season_id
    */
    public function get_unassigned_teams_by_season($season_id)
    {
		$params=array($season_id,$season_id,$season_id);
        $criteria='';
        $query = $this->input->get_post('query');
        if($query)
        {
			$criteria = "AND lower(team_name) like '%'||lower(?)||'%'  ";
			$params[]=$query;
        }//=$query ;
  		$sql = "SELECT      	t.team_id
  								,t.org_id
        						,t.team_name
				,(  SELECT COUNT(rp.person_id) 
					FROM public.roster_person rp 
					INNER JOIN public.team_roster tr ON rp.team_roster_id=tr.team_roster_id 
							AND tr.team_id=t.team_id AND tr.season_id = ? AND rp.deleted_flag='f' ) 
				AS roster_count 
				,(SELECT COUNT(DISTINCT person_id) FROM permissions.assignment a 
 					INNER JOIN permissions.user u ON a.user_id=u.user_id AND a.org_id=t.org_id  
 					AND a.deleted_flag='f' AND u.deleted_flag='f'  )
 				AS user_count 
        						
                FROM        	public.team t
				INNER JOIN public.entity_org ot ON ot.org_id=t.org_id AND ot.deleted_flag=FALSE   AND t.deleted_flag=FALSE 
				INNER JOIN		public.team_season ts ON ts.team_id = t.team_id AND ts.season_id=? AND ts.deleted_flag=FALSE 
				WHERE           ts.id NOT IN 
						(SELECT tsd.team_season_id FROM public.team_season_division tsd 
						
						 INNER JOIN public.season_division sd ON tsd.season_division_id = sd.id 
						 
						 	AND tsd.deleted_flag=FALSE AND sd.deleted_flag=FALSE 
				 			AND sd.season_id=? 
				 			INNER JOIN public.league_division ld ON ld.division_id = sd.division_id   AND ld.deleted_flag=FALSE     )  
				 			
				".$criteria." 
				AND t.deleted_flag=FALSE 
				AND( t.team_status_id IS NULL OR t.team_status_id =1)
                ORDER BY 		team_name ASC";
           
        return $this->db->query($sql,$params)->result_array();
 
    }
 
 
    public function update_teams_division_assignment($new_assignment_combination/*   '1,2,3-4,5,6-7,8,9'  */,$season_id)
    {                                                   
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------------                         
        return $this->db->query("select * from public.update_teams_division_assignment(?,?,?,?)",array($new_assignment_combination,$season_id,$a_u_id,$a_o_id))->result_array();
    }

    
    /**
    * put your comment there...
    * 
    * @param mixed $team_id
    */
    public function get_team_exceptions($team_id)
    {
		$sql="SELECT  team_id, team_ex_id, ex_desc, effective_range_start, effective_range_end  
		
		FROM schedule.team_date_exception WHERE team_id = ? AND deleted_flag = 'f' ";
		return $this->db->query($sql,$team_id)->result_array();
    }
    
    /**
    * insert
    * 
    * @param mixed $team_id
    * @param mixed $desc
    * @param mixed $start
    * @param mixed $end
    * @param mixed $user
    * @param mixed $owner
    */
    public function insert_team_exception($team_id,$desc,$start,$end,$user,$owner)
    {
    	$params = array($team_id,$desc,$start,$end,$user,$owner);
    	
    	$sql = 'SELECT schedule.insert_team_exception(?,?,?,?,?,?)';
        $query = $this->db->query($sql,$params);
        $result= $query->first_row();
        return $result->insert_team_exception;   
		
    }
    
    
    /**
    * delete
    * 
    * @param mixed $team_ex_id
    * @param mixed $user
    */
    public function delete_team_exception($team_ex_id,$user)
    {
		$params = array($team_ex_id,$user);
    	
    	$sql = 'SELECT schedule.delete_team_exception(?,?)';
        $query = $this->db->query($sql,$params);
        $result= $query->first_row();
        return $result->delete_team_exception; 
		
    }
    /**
    * update
    * 
    * @param mixed $team_ex_id
    * @param mixed $desc
    * @param mixed $start
    * @param mixed $end
    * @param mixed $user
    */
    public function update_team_exception($team_ex_id,$desc,$start,$end,$user)
    {
		$params = array($team_ex_id,$desc,$start,$end,$user);
    	
    	$sql = 'SELECT schedule.update_team_exception(?,?,?,?,?)';
        $query = $this->db->query($sql,$params);
        $result= $query->first_row();
        return $result->update_team_exception;  
    }
    
    
    
    
    public function delete_team($team_id,$user)
    {
		$params = array($team_id,$user);
    	
    	$sql = 'SELECT public.delete_team(?,?)';
        $query = $this->db->query($sql,$params);
        $result= $query->first_row();
        return $result->delete_team;  
    }
    
    
    public function update_team_name($team_id,$user,$name)
    {
		$params = array($team_id,$user,$name);
    	
    	$sql = 'SELECT public.update_team_name(?,?,?)';
        $query = $this->db->query($sql,$params);
        $result= $query->first_row();
        return $result->update_team_name;  
    }
    
    public function delete_team_season_assignment($team_id,$season_id,$user)
    {
		$params = func_get_args();
    	
    	$sql = 'SELECT public.delete_team_season_assignment(?,?,?)';
        return $this->db->query($sql,$params)->first_row()->delete_team_season_assignment;
		
    }
    
    
    /*CREATE OR REPLACE FUNCTION "public"."delete_team_season_assignment"(_team_id int8, _season_id int8)
  RETURNS "pg_catalog"."int4" AS $BODY$BEGIN
	
	UPDATE "public".team_season 
	SET deleted_flag=TRUE, 
	deleted_by = creator, 
	deleted_on=LOCALTIMESTAMP 
	WHERE season_id = _season_id 
	AND   team_id   = _team_id  ;


RETURN 1;

END$BODY$
  LANGUAGE 'plpgsql' VOLATILE COST 100
;

*/
    public function update_team_season_assignment($team_id,$season_id,$user,$owner)
    {
		$params = func_get_args();
    	
    	$sql = 'SELECT public.update_team_season_assignment(?,?,?,?)';
        return $this->db->query($sql,$params)->first_row()->update_team_season_assignment;
    }
    
    public function new_team($league_org,$t_name,$creator,$owner)
    {
        $q= $this->db->query("SELECT public.new_team (?,?,?,?)",array($league_org,$t_name,$creator,$owner));
    	return $q->first_row()->new_team;
	}
	
	public function org_id_from_team($team_id)
	{
		$data= $this->db->query("SELECT org_id FROM public.team WHERE team_id=?",$team_id)->result_array();	
		if(count( $data )) return $data[0]['org_id'];  else return -1;//was a value found or not
		
	}
    public function get_customfields_by_season($season_id)
    {
        $a_e_id= $this->permissions_model->get_active_entity();
        $q="
        select cfd.field_id,cfd.master_entity_id,cfd.slave_org_type_id,cfd.field_title,cfd.season_id,case when cfd.slave_org_type_id=6 then 'Team Registration' else 'Player Registration' end as appliesto
            from public.customfields_def     cfd 

            where 
            cfd.deleted_flag=false
            and season_id=?
            and master_entity_id=?
        ";
        $result= $this->db->query($q,array($season_id,$a_e_id))->result_array();
        return $result;
    }
    public function delete_customfield($season_id,$field_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        
        $result= $this->db->query("select * from public.delete_customfield(?,?,?,?)",array($season_id,$field_id,$a_u_id,$a_o_id))->result_array();
        return $result;    
    }
    public function add_customfield($master_entity_id,$season_id,$field_title,$slave_org_type_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        
        $result= $this->db->query("select * from public.add_customfield(?,?,?,?,?,?)",array($master_entity_id,$season_id,$field_title,$slave_org_type_id,$a_u_id,$a_o_id))->result_array();
        return $result;        
    }
    public function update_custom_field_title($customFieldId,$customFieldTitle)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        
        $result= $this->db->query("select * from public.update_custom_field_title(?,?,?,?)",array($customFieldId,$customFieldTitle,$a_u_id,$a_o_id))->result_array();
        return $result;            
    }
}        
?>
