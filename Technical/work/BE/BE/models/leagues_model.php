<?php
require_once("./endeavor/models/org_model.php");   
class Leagues_model extends Org_model
{
    
    public function __construct()
    {
        parent::__construct();
        //$this->DB = $this->load->database('default', true);
        //$this->update_user_activity();

    }
    
    
    
    

    
    //****************************************    BLACKOUTS
    public function get_blackouts($season_id)
    {
        //$a_u_id= $this->permissions_model->get_active_user();
        //$a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------------
        $params=array($season_id);
        $criteria=(string)$this->input->get_post('query');
        $and='';
    	if($criteria)
    	{
    		//skip this check if no search given
			$params[]=$criteria;
			$params[]=$criteria;
			$params[]=$criteria;
			$params[]=$criteria;
			$and="            
			AND 
            (
                    lower(bo_start_date::varchar) like '%'||lower(?)||'%'  
              or    lower(bo_end_date::varchar) like '%'||lower(?)||'%'  
              or    lower(bt.type_name) like '%'||lower(?)||'%'  
              or    lower(bo_user_desc) like '%'||lower(?)||'%'  
            ) ";
    	}
        $sql = "
            SELECT bo_id , bo_start_date,bo_end_date,bo_user_desc
					,season_id
            	 	,bo_type_id,bt.type_name as bo_type_name 
            FROM       public.blackout bo 
            INNER JOIN public.lu_blackout_type bt on bt.type_id=bo.bo_type_id AND bo.deleted_flag=false 
            AND       bo.season_id=? 
        	".$and;
        $result= $this->db->query($sql, $params)->result_array();

        $extjs_fmt='Y-m-d';//strip out the 00:00:00 fromt he timestamp, basically
        foreach($result as &$r)
        {
			$r['bo_start_date'] = date($extjs_fmt,strtotime($r['bo_start_date']));
			$r['bo_end_date']   = date($extjs_fmt,strtotime($r['bo_end_date']  )); 
        }
        return $result;
    }
    public function new_blackout($season_id,$start_date,$end_date,$type_id,$desc)
    {
    	$params=func_get_args();
    	$params[]=(int)$this->permissions_model->get_active_user();
    	$params[]=(int)$this->permissions_model->get_active_org();
 
        //------------------------------------------------------------------------
        return $this->db->query("SELECT  public.new_blackout(?,?,?,?,?,?,?)",$params)->first_row()->new_blackout;
    }
    public function update_blackout($blackout_id,$start_date,$end_date,$type_id,$desc)
    {
    	$params=func_get_args();
    	$params[]=(int)$this->permissions_model->get_active_user();
    	$params[]=(int)$this->permissions_model->get_active_org();
        //------------------------------------------------------------------------
        return $this->db->query("SELECT  public.update_blackout(?,?,?,?,?,?,?)", $params)->first_row()->update_blackout;
    }
    public function delete_blackout($blackout_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------------
        return $this->db->query("SELECT * from public.delete_blackout(?,?,?)", array($blackout_id,$a_u_id,$a_o_id))->result_array();
    }
 
    
    public function get_league_details_from_org($org)
    {                                                                          
    	$sql="SELECT l.league_name, l.league_id,  u.url,  lt.type_name , lt.type_id 
    		FROM public.league l 
    		INNER JOIN public.entity_org o ON o.org_id = l.org_id AND l.org_id=?
    		INNER JOIN public.lu_league_type lt ON lt.type_id = l.league_type 
    		LEFT OUTER JOIN public.url u ON o.entity_id = u.entity_id AND ispark=TRUE 
";
		$result = $this->db->query(   $sql , array($org))->result_array();
		return $result;
    }
    
    
    //this is used in getstarted screen
    public function get_league_details_basic_from_org($org)
    {                                                                          
    	$sql="SELECT l.league_name, l.league_id,  u.url,  lt.type_name , lt.type_id 
    		FROM public.league l 
    		INNER JOIN public.entity_org o ON o.org_id = l.org_id AND l.org_id=?
    		INNER JOIN public.lu_league_type lt ON lt.type_id = l.league_type 
    		LEFT OUTER JOIN public.url u ON o.entity_id = u.entity_id AND ispark=TRUE 
    		";
		$result = $this->db->query(   $sql , array($org))->result_array();
		return $result;
    }
    //****************************************
    
    public function get_league_from_org($org)
    {   
        //org level                                                                         
		$result = $this->db->query("SELECT * from public.league WHERE org_id = ?", array($org))->result_array();
        if(count($result)!=0)return $result[0]["league_id"];
        //team-level  (looks up for parent league id)
        else 
        {
            $result=$this->db->query("SELECT * from public.league t where t.org_id=public.get_parent_org(?)", array($org))->result_array();  
            return $result[0]["league_id"];
        } 
    }
    public function get_org_from_league($league)
    {
		$sql = "SELECT org_id FROM public.league WHERE league_id = ?";
		$result = $this->db->query($sql, array($league));
		return $result->first_row()->org_id;
    }
    
   
    public function get_league_list($owner_id)
    {   //?? not used anymore           
        $sql="select * from public.League_list_per_owner(?);";
        return $this->db->query($sql,array($owner_id))->result_array();
    }
    
    public function get_leagues_by_association($association_id)
    {              
        $sql="SELECT  league_id , league_name ,o.entity_id ,expiry_date,url,
        	    FROM league l 
			    INNER JOIN entity_org o on o.org_id=l.org_id 
			    INNER JOIN entity_relationship r on r.child_id=o.entity_id
			    WHERE r.parent_id=? ";
                                    
        return $this->db->query($sql,array($association_id))->result_array();
    }

    public function get_leagues_by_parentorg($org_id,$active=null)
    {
    	$search_criteria= $this->input->get_post("query");
    	$where='';
        $params=array($org_id,$org_id,$org_id);
        if($search_criteria)
        {
			$params[]=$search_criteria;
			$where="WHERE   lower(league_name) like '%'||lower(?)||'%'";
        }
        
        //if(!$active) $active='all';
		$sql="SELECT  league_id , league_name ,o.entity_id ,o.org_id, lut.type_name, lut.type_id 
				,url
				,public.get_address_by_entity_id_commabased(o.entity_id::INT , ? ::INT , ? ::INT) AS address
				,public.get_league_users_count(l.league_id) AS league_users_count
				
				,o.org_logo
				
				
				, (SELECT COUNT(*) FROM
   public.entity_relationship ert 
 
	INNER JOIN public.entity_org tmo ON ert.child_id= tmo.entity_id  AND ert.parent_id=o.entity_id AND tmo.deleted_flag=FALSE) as team_count

 		, (SELECT COUNT(DISTINCT person_id)  
 		FROM public.entity_relationship ert --ON ert.parent_id = o.entity_id 
	INNER JOIN public.entity_org tmo ON ert.child_id = tmo.entity_id AND ert.parent_id=o.entity_id AND tmo.deleted_flag=FALSE 
INNER JOIN public.team t ON tmo.org_id=t.team_id AND t.deleted_flag=false AND t.deleted_flag=false
INNER JOIN public.team_roster tr ON tr.team_id = t.team_id 
INNER JOIN public.roster_person rp ON rp.team_roster_id = tr.team_roster_id) as player_count 
				
				
        FROM league l 
				inner join public.entity_org o on o.org_id=l.org_id  AND l.deleted_flag=false
				INNER JOIN public.entity_relationship er ON o.entity_id = er.child_id 
				INNER JOIN public.entity_org op ON op.org_id=? AND op.entity_id=er.parent_id 
				INNER JOIN public.lu_league_type lut ON lut.type_id = l.league_type 
				LEFT OUTER join public.url u on u.entity_id=o.entity_id 
         ".$where."
		 ";                      
    
        //$data=$this->db->query("select * from public.get_leagues(?,?,?,?);",array($search_criteria,$active,$a_u_id,$a_o_id))->result_array();
        $data=$this->db->query($sql,$params)->result_array();
        
        foreach($data as $i=>$v)
        {
            if(!isset($v["league_users_count"]) ||$v["league_users_count"]==0)
                $data[$i]["league_users_count_image"]="<img src='assets/images/error.png' />";
            else $data[$i]["league_users_count_image"]=$v["league_users_count"];
            
            $data[$i]['websiteprefix']='';
            $data[$i]['domainname']='';
            $sep='.';
            if($data[$i]['url'])
            {
				
	            
	            $url_array = explode($sep,$data[$i]['url']);
	            $data[$i]['websiteprefix']=$url_array[0];
	            $data[$i]['domainname']=$url_array[1].$sep.$url_array[2];
			}
        }
        return $data; 
    }
    /*
    public function get_leagues()
    {              
        $sql = "SELECT  l.league_id, l.org_id, l.league_name,  
        		FROM 	public.league l 
        		WHERE	l.deleted_flag = 'f' 
        		AND 	l.league_type = 1 ";//lu_league_type == 1 is reg league
        
        
        return $this->db->query($sql)->result_array();

    }
    
    public function get_leagues()
    {
        $sql = "SELECT 		x.league_id, l.league_type,o.org_name AS league_name 
                FROM 		public.league x
                INNER JOIN 	public.entity_org o 
                ON          x.org_id = o.org_id
                WHERE       x.deleted_flag = 'f' 
                AND         o.deleted_flag = 'f'  ";// "AND ".USER_CAN_ACCESS." ";
        $result = $this->db->query($sql);
       // var_dump($this->db->last_query());
        return $result->result_array();
        
    }
    
    
    public function get_tournaments()
    {              
        $sql = "SELECT  l.league_id, l.org_id, l.league_name, l.league_type 
        		FROM 	public.league l 
        		WHERE	l.deleted_flag = 'f' 
        		AND 	l.league_type = 2 ";//lu_league_type == 2 is tournament
        
        
        return $this->db->query($sql)->result_array();
    }
    */

    
    public function json_get_addresses_list()
    {
        $sql="select address_id as id,address_street||', '||address_city||', '||r.region_abbr||', '||c.country_name||', '||p.postal_value  as name from public.address a ".
        " inner join lu_address_region as r on r.region_id=a.address_region ".
        " inner join lu_address_country as c on c.country_id=a.address_country ".
        " inner join lu_address_postal as p on p.postal_id=a.address_postal "            
        ;
        return $this->db->query($sql)->result_array();
    }
    public function json_get_organizations_list()
    {
        $sql="select org_id as id, org_name as name from public.entity_org";
        return $this->db->query($sql)->result_array();
        
    }
    public function json_get_leagues_name_list()
    {
        //$sql="select  league_name from public.league";
        $sql="select  league_id,league_name,db_dbname from public.league";
        return $this->db->query($sql)->result_array();
    }
    
    public function get_team_temp_list_by_season_id($season_id,$team_temp_id)
    {
        return $this->db->query("select * from get_team_temp_list_by_season_id(?,?)",array($season_id,$team_temp_id))->result_array();
    }
    public function get_teams_temp_list_by_season_id($season_id)
    {
        return $this->db->query("select * from get_teams_temp_list_by_season_id(?)",array($season_id))->result_array();
    }
    public function get_teams_reg_info_by_season_id($season_id,$team_temp_id)
    {
        $params='';
        $field_list= $this->db->query("select * from get_form_fields_reg(?);",array($season_id))->result_array();
        foreach($field_list as $i=>$v)
        {
            $params.="'<tr>";
            $params.="<div id=\'Mteam-reg-team-DIV \' class=\'form-field\'>";
            $params.= "<div id=\'Mteam-reg-team-label\'>".$v["field_name"]."</div>";
            $params.= "<div id=\'Mteam-reg-team-input\' class=\'input\'>";
            $params.= "<input disabled id=\'Mteam-reg-team-1\' name=\'Mteam-reg-team-1\' type=\'textbox\' value=\''||".$v["field_code"]."||'\'||\'/>";
            $params.= "</div>";
            $params.= "</div>";
            $params.="</tr> '||";
        }
            
        $params=substr($params,0,strlen($params)-2);
        
        return $this->db->query("select $params as col from season_league_reg_$season_id where team_id=$team_temp_id")->result_array();
    }
    

    /**
    * gets all teams owned by current league
    * 
    * @param mixed $org
    */
    public function get_league_teams($org)
    {
		$sql="SELECT t.team_name, t.team_id, t.org_id  
		 FROM 			public.team t
		 WHERE     t.deleted_flag='f' 	
		 AND     t.owned_by = ? ";
		return $this->db->query($sql,$org)->result_array();
    }
    
    public function get_league_teams_join($org)
    {
		
		$sql="SELECT t.team_name , t.team_id , x.division_id ,s.season_name , s.season_id ,d.division_name
			FROM public.team t 
			LEFT OUTER JOIN 		public.xref_team_season_division x ON x.team_id = t.team_id 
				LEFT OUTER JOIN public.season s ON s.season_id = x.season_id 
				LEFT OUTER JOIN public.league_division d ON d.division_id = x.division_id  
			WHERE     t.deleted_flag='f' 	
			 AND     t.owned_by = ?";
		return $this->db->query($sql,$org)->result_array();
    }
    
    public function get_league_teams_null_season($org)
    {
		$sql="SELECT t.team_name, t.team_id, t.org_id  
		 FROM 			public.team t 
		WHERE t.team_id NOT IN (SELECT team_id FROM xref_team_season_division)
		 AND     t.deleted_flag='f' 	
		 AND     t.owned_by = ? ";
		return $this->db->query($sql,$org)->result_array();
    }
    
    public function get_league_teams_by_season($org,$season)
    {
    	//return "givenorg$org,season,$season";
		$sql="SELECT t.team_name, t.team_id, t.org_id  , x.division_id ,d.division_name,x.season_id ,s.season_name
		 FROM 			public.team t 
		 INNER JOIN 		xref_team_season_division x ON t.team_id = x.team_id  AND     t.deleted_flag='f' 	
		 										                                 AND     t.owned_by = ?
		 INNER JOIN 	    public.league_division d    ON d.division_id = x.division_id  
		 INNER JOIN 		public.season s ON s.season_id = x.season_id AND x.season_id =? 
		  ";
		return $this->db->query($sql,array($org,$season))->result_array();
    }
    
    public function get_teams_with_exceptions($org)
    {
		$sql="SELECT    t.team_name, t.team_id, t.org_id  
		 FROM 			public.team t 
		 WHERE t.team_id IN (SELECT ex.team_id from schedule.team_date_exception ex 
		 					 WHERE ex.deleted_flag='f') 
		 AND            t.owned_by = ? 
		 ";
		return $this->db->query($sql,$org)->result_array();
    }
    /**
    * gets all teams owned by current league
    * with matching division
    * 
    * @param mixed $org
    * @param mixed $div_id
    
    public function get_league_div_teams($org,$div_id)
    {
		$sql="SELECT t.team_name, t.team_id, t.org_id, t.division_id , d.division_name 
		 FROM 			public.team t
		 INNER JOIN public.league_division d 				ON t.division_id = d.division_id 
															 AND t.deleted_flag='f' AND d.deleted_flag='f'	
															 AND t.owned_by = ?  AND t.division_id = ? ";
		return $this->db->query($sql,array($org,$div_id))->result_array();
    }
    
    */
    public function addr_list()
    {
       return $this->db->query("select * from addr_list();")->result_array();
    }
    //NEW

    public function get_activated_seasons()
    {
        return $this->db->query("select * from public.get_activated_seasons()")->result_array();                        
    }    

    public function activate_season($season_id)
    {
        return $q=$this->db->query("select public.activate_season(?)",array($season_id))->result_array();    
    }
    public function get_activated_season()
    {
        return $q=$this->db->query("select * from get_activated_season()")->result_array();    
    }
    public function approve_team($team_temp_id,$season_id)
    {
        return $q=$this->db->query("select public.approve_team(?,?,?)",array($team_temp_id,$season_id,14))->result_array();
    }
/*
    public function new_team($league_org,$t_name,$creator,$owner)
    {
        $q= $this->db->query("SELECT public.new_team (?,?,?,?)",array($league_org,$t_name,$creator,$owner));
    	return $q->first_row()->new_team;
    }*/


	public function get_league_info($leagueid)
    {

    	$sql="SELECT l.league_name, l.league_id,  u.url,  lt.type_name , lt.type_id 
    		FROM public.league l 
    		INNER JOIN public.entity_org o ON o.org_id = l.org_id AND l.league_id=?
    		INNER JOIN public.lu_league_type lt ON lt.type_id = l.league_type 
    		LEFT OUTER JOIN public.url u ON o.entity_id = u.entity_id AND ispark=TRUE 
";
        $result = $this->db->query($sql,$leagueid);
       // var_dump($this->db->last_query());
        return $result->result_array();
    }
    
    
    /**
    * DEPRECIATED
    * nothing to do with leagues
    * moved to permissions model
    * 
    */
    public function get_users()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //-------------------------------------------------------          
        $search_criteria=$this->input->get_post('query');
        
        $q=
        "
            select distinct(p.person_id) , u.user_id, person_fname , person_lname ,person_birthdate
                from public.entity_person p 
                inner join permissions.user u               on p.person_id=u.person_id AND u.deleted_flag=false AND p.deleted_flag=false
                inner join permissions.assignment a         on a.user_id=u.user_id AND a.deleted_flag=false  AND a.org_id=?  
                inner join permissions.lu_role r            on r.role_id=a.role_id
 
        ";                      
        $result= $this->db->query($q,array($a_o_id))->result_array();
        return $result;
            
    }
    
    public function send_welcome_email($league_id,$a_u_id,$a_o_id/*as Association org id*/)
    {
        
        
		
        $result=$this->db->query('
        	SELECT 	league_name,
        			m_firstname,
        			m_lastname,
        			url,
        			m_login,
        			m_password,
        			m_email,
                    l_address,
                    m_workf
        	from 	public.get_complete_league_info(?,?,?)'
        , array($league_id,$a_u_id,$a_o_id))->result_array();
        
        $data["fname"]              =$result[0]["m_firstname"];
        $data["lname"]              =$result[0]["m_lastname"];
        $data["user"]               =$result[0]["m_login"];
        
        $data["url"]                =$result[0]["url"];
        $data["m_email"]            =$result[0]["m_email"];
        $data["league"]             =$result[0]["league_name"];
        
        
        //Association Info
        $result=$this->db->query('SELECT * from   public.get_complete_assoc_info(?,?)', array($a_u_id,$a_o_id))->result_array();        
        $assoc_total_address=explode('^~^',$result[0]["a_address"]);
        
        $street         =explode('::',$assoc_total_address[0]);
        $city           =explode('::',$assoc_total_address[1]);
        $prov           =explode('::',$assoc_total_address[2]);
        $country        =explode('::',$assoc_total_address[3]);
        $postalcode     =explode('::',$assoc_total_address[4]);
        
        
        $data["sender"]["name"]     =$result[0]["org_name"];
        $data["sender"]["address"]  =$street[1];
        $data["sender"]["city"]     =$city[1];
        $data["sender"]["prov"]     =$prov[1];
        $data["sender"]["country"]  =$country[1];
        $data["sender"]["postal"]   =$postalcode[1];
        $data["sender"]["email"]    =$result[0]["a_email"];
        $data["sender"]["phone"]    =$result[0]["a_workf"];
                                      

        return $data;
    } 
    
    
    
    /**
    * get league id from org id
    * 
    * @param int $org
    * $return int league id
    */
    public function league_id_from_org($org)
    {
		$sql="SELECT league_id FROM public.league WHERE org_id=?";
		return $this->db->query($sql,array($org))->first_row()->league_id;
    }
    
}
    
  
?>
