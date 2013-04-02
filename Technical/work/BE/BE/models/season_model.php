<?php

require_once("endeavor_model.php");
class Season_model extends Endeavor_model
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	* update season
	* 
	*/
    public function update_season($season_id,$season_name,$start_date,$end_date,$isactive,$reg_needed,$isenabled,$reg_start_date,$reg_end_date,$reg_deposit_status,$reg_deposit_amount,$reg_fees_status,$reg_fees_amount)
    {                                                   
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------------   
        $sql="SELECT  public.update_season(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $params=array($season_id,$season_name,$start_date,$end_date,$isactive
            ,$reg_needed,$isenabled,$reg_start_date,$reg_end_date,$reg_deposit_status,$reg_deposit_amount,$reg_fees_status,$reg_fees_amount  
            ,$a_u_id,$a_o_id);   
            
                               
        return $this->db->query( $sql ,$params)->first_row()->update_season;
    }

	public function get_registration_notification_perople_list($season_id)
    {
        $q=
        "
        select rnl.*,r.effective_range_start as start_date,r.effective_range_end as end_date ,eo.org_name as league_name
            from    public.league l
            inner   join public.season s                            on l.league_id=s.league_id
            inner   join public.registration r                      on r.season_id=s.season_id
            inner   join public.registration_notification_list rnl  on r.id=rnl.registration_id
            inner   join public.entity_org eo                       on eo.org_id=l.org_id                            
            
            where   
                s.season_id=?
                and (r.effective_range_start    is null or current_date>=r.effective_range_start::date)
                and (r.effective_range_end      is null or current_date<=r.effective_range_end::date)
                and is_active=true
        ";
        return $this->db->query($q ,array($season_id))->result_array();
    }
    public function inactivate_waiting_people_list($waitingUserList)
    {
        $q="update public.registration_notification_list set is_active=false where id in( select regexp_split_to_table(?, ',' )::integer)";
        return $this->db->query($q ,array($waitingUserList))->result_array();
    }
		
	/**
	* Create a season
	* with a registration attached
	*/
	public function new_season($season_name,$start_date,$end_date,$isactive,$reg_needed,$reg_start_date,$reg_end_date,$reg_deposit_status,$reg_deposit_amount,$reg_fees_status,$reg_fees_amount)
    {                                                   
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        
        //------------------------------------------------------------------------   
        $sql="SELECT  public.new_season(?,?,?,?,?,?,?,?,?,?,?,?,?)" ;
        $params =    array($season_name,$start_date,$end_date,$isactive
            ,$reg_needed,$reg_start_date,$reg_end_date,$reg_deposit_status,$reg_deposit_amount,$reg_fees_status,$reg_fees_amount   
            ,$a_u_id,$a_o_id) ;
        return $this->db->query($sql ,$params)->first_row()->new_season;
    }
	

	
	public function delete($season_id,$user)
	{
		$result = $this->db->query('SELECT public.season_delete(?,?)', array($season_id,$user));
		return $result->first_row()->season_delete;
	}
	
	/**
	* get ALL seasons for this given league, regardless of dates
	* 
	* @param mixed $league_id
	*/
	public function get_seasons($league_id)
    {
        $search_criteria =(string) $this->input->get_post('query');

        //-----------------------------------------------------------------
        $sql = "SELECT       s.season_id, s.season_name, x.league_id, x.league_name,  
                             s.effective_range_start, s.effective_range_end ,s.isactive
                             , ( SELECT COUNT(*) FROM schedule.league_season_schedule lss 
							     INNER JOIN schedule.schedule sch ON sch.schedule_id = lss.schedule_id AND sch.deleted_flag='f' 
								 AND lss.league_id=x.league_id AND lss.season_id=s.season_id 
								)     AS schedule_count,
							 r.is_enabled, r.effective_range_start as reg_range_start, r.effective_range_end as reg_range_end, r.deposit_status,r.deposit_amount,r.fees_status,r.fees_amount
                FROM         public.league x    
                INNER JOIN   public.season s 
                	ON       x.league_id = s.league_id 
                LEFT OUTER JOIN public.registration r
                	ON		 r.season_id = s.season_id
                WHERE        x.league_id = ? 
                AND          s.deleted_flag = 'f' 
                AND          x.deleted_flag = 'f'    
                and 
                (
                    lower(s.season_name) like '%'||lower(?)||'%'
                )
                ORDER BY s.effective_range_start DESC 
                ";//  AND          ".USER_CAN_ACCESS." ";
        return $this->db->query($sql,array($league_id,$search_criteria))->result_array();
    }
    
    /**
    * get all seasons for this league, that this team is NOT assigned to
    * 
    * @param mixed $league_id
    * @param mixed $team_id
    */
    public function get_seasons_available_forteam($league_id,$team_id)
    {
		//same basic query as this->get_seasons , but withtout search, and with teamid check
        $sql = "SELECT       s.season_id, s.season_name, x.league_id, x.league_name,  
                             s.effective_range_start, s.effective_range_end ,s.isactive
                             , ( SELECT COUNT(*) FROM schedule.league_season_schedule lss 
							     INNER JOIN schedule.schedule sch ON sch.schedule_id = lss.schedule_id AND sch.deleted_flag='f' 
								 AND lss.league_id=x.league_id AND lss.season_id=s.season_id 
								)     AS schedule_count,
							 r.is_enabled, r.effective_range_start as reg_range_start, r.effective_range_end as reg_range_end, 
							 r.deposit_status,r.deposit_amount,r.fees_status,r.fees_amount
                FROM         public.league x    
                INNER JOIN   public.season s 
                	ON       x.league_id = s.league_id AND          s.deleted_flag = 'f' AND          x.deleted_flag = 'f'  
                										AND x.league_id = ? 
                LEFT OUTER JOIN public.registration r
                	ON		 r.season_id = s.season_id
                WHERE        
                s.season_id NOT IN (SELECT ts.season_id FROM public.team_season ts WHERE ts.team_id = ? AND ts.deleted_flag=FALSE)
                ORDER BY s.effective_range_start DESC
                ";//  AND          ".USER_CAN_ACCESS." ";
        return $this->db->query($sql,array($league_id,$team_id))->result_array();
    }
    
    
   	public function get_seasons_assigned_to_team($team_id)
    {

		//same basic query as this->get_seasons , but withtout search, and with teamid check
        $sql = "SELECT       s.season_id, s.season_name,? AS team_id,
                             s.effective_range_start, s.effective_range_end ,s.isactive 
                FROM          public.season s 
                WHERE        s.deleted_flag = 'f' 
                AND 
                s.season_id  IN (SELECT ts.season_id FROM public.team_season ts WHERE ts.team_id = ? AND ts.deleted_flag=FALSE)
                ORDER BY s.effective_range_start DESC
                ";//  
        return $this->db->query($sql,array($team_id,$team_id))->result_array();
    }
    
    public function get_active_seasons($league_id)
    {
        $sql = "SELECT       s.season_id, s.season_name,   
                             s.effective_range_start, s.effective_range_end ,s.isactive
                FROM         public.season s 
                WHERE        s.league_id = ? 
                AND          s.deleted_flag = 'f' 
                AND          s.isactive='t'   ";//  AND          ".USER_CAN_ACCESS." ";
        return $this->db->query($sql,$league_id)->result_array();
        
    }
    /**
    * get data on this season
    * 
    * @param mixed $season_id
    */
    public function get_season_data($season_id)
    {
		$sql = "SELECT       s.season_id, s.season_name, x.league_id, x.league_name,  
                             s.effective_range_start, s.effective_range_end
                FROM         public.league x
                INNER JOIN   public.season s 
                	ON       s.league_id = x.league_id 
                WHERE        s.season_id = ? 
                AND          s.deleted_flag = 'f' 
                AND          x.deleted_flag = 'f'   LIMIT 1 ";//  AND          ".USER_CAN_ACCESS." ";
        return $this->db->query($sql,$season_id)->result_array();
    }
    public function get_registration_collect_status()
    {
		$sql="SELECT * FROM public.lu_registration_collect_status";
    	return $this->db->query($sql)->result_array();
	}
    public function get_active_season($league_id)
    {                                   
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------
        return $this->db->query("select * from public.get_active_season(?,?,?);",array($league_id,$a_u_id,$a_o_id))->result_array();    
    }
    /**
    * get all registrations for this season
    * 
    * @param mixed $season_id
    */
    public function get_season_registrations($season_id)
    {
		$sql = " SELECT r.id , r.season_id, r.is_enabled, r.deposit_status, lus.status AS deposit_label		
				,r.deposit_amount, r.fees_status, lur.status AS fees_label,
				 r.fees_amount, r.effective_range_start, r.effective_range_end
				 FROM public.registration r   
				 INNER JOIN public.lu_registration_collect_status as lus ON lus.id = r.deposit_status AND r.season_id = ?  
				 INNER JOIN public.lu_registration_collect_status as lur ON lur.id = r.fees_status ";
								
        return $this->db->query($sql,$season_id)->result_array();
    }
    
    
    
    /**
    * update record or return -1
    * 
    * @param str $name
    * @param ts $start
    * @param ts $end
    * @param bool $active
    * @param int $sid
    
    public function update_season($name,$start,$end,$active,$sid)
    {
		if(!$this->userorg_can_update()) return false;
		$params = array($name,$start,$end,$active,$sid);
		$result = $this->db->query('SELECT public.update_season(?,?,?,?,?)', $params);
		return $result->first_row()->update_season;		
    }*/
    
    
    /**
    * insert OR update, and return lastval
    * 
    * @param int $r_id
    * @param int $season
    * @param bool $enabled
    * @param float $deposit
    * @param int $d_status
    * @param float $fees
    * @param int $f_status
    * @param ts $start
    * @param ts $end
    */
    public function update_season_registration($r_id , $season , $enabled , $deposit , $d_status , $fees , $f_status , $start , $end )
    {
		if(!$this->userorg_can_update()) return false;
		$params = array($r_id , $season , $enabled , $deposit , $d_status , $fees , $f_status , $start , $end);
		$result = $this->db->query('SELECT public.update_season_registration(?,?,?,?,?,?,?,?,?)', $params);
		return $result->first_row()->update_season_registration;		
    }
    
    
}

?>
