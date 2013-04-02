<?php
require_once('./endeavor/models/endeavor_model.php');
class Games_model extends Endeavor_model
{
	public function __construct()
	{
		parent::__construct();
	}
	public function count_games_by_team($team_id)
	{
		$sql="SELECT COUNT(*) AS count FROM schedule.game g INNER JOIN schedule.schedule s 
			ON s.schedule_id = g.schedule_id AND g.deleted_flag='f' AND s.deleted_flag='f' 
			AND ? IN (SELECT tg.team_id FROM schedule.team_game tg WHERE tg.game_id = g.game_id )";
		
		return $this->db->query($sql,$team_id)->result_array();		
	}
	public function count_games_by_season_team($team_id,$season_id)
	{
		$params=array($team_id,$season_id);
		$sql="SELECT COUNT(*) AS count FROM schedule.game g 
			INNER JOIN schedule.schedule s  
					ON s.schedule_id = g.schedule_id AND g.deleted_flag='f' AND s.deleted_flag='f'   
					AND ? IN (SELECT tg.team_id FROM schedule.team_game tg WHERE tg.game_id = g.game_id )
			INNER JOIN schedule.league_season_schedule lss ON lss.schedule_id=s.schedule_id AND lss.season_id=? 
			 ";
		
		return $this->db->query($sql,$params)->result_array();		
	}
	public function get_games_by_venue_date($venue_id,$date)
    {
    	$params=array($venue_id,$date);
		$sql="SELECT g.game_id, g.game_date , g.start_time, g.end_time, g.schedule_id  ,ht.team_id AS home_id, ht.team_name AS home_name
													,at.team_id AS away_id, at.team_name AS away_name, v.venue_name, v.venue_id 
				FROM schedule.game g 
				INNER JOIN schedule.schedule s 
							ON s.schedule_id = g.schedule_id AND s.deleted_flag='f' 
							AND g.venue_id = ? 
							AND g.game_date = ?
							AND g.deleted_flag = 'f' 
				INNER JOIN public.venue v ON v.venue_id=g.venue_id 
				INNER JOIN schedule.team_game h   ON h.game_id=g.game_id AND h.team_ishome='t'  
				INNER JOIN public.team ht         ON h.team_id = ht.team_id 
				INNER JOIN schedule.team_game a   ON a.game_id=g.game_id AND a.team_ishome='f' 
				INNER JOIN public.team at         ON a.team_id = at.team_id  ";
		return $this->db->query($sql,$params)->result_array();		
    }
    /**
    * get all gaames for this team id and date, regardless of sch/league/season/venue/etc
    * 
    * @param mixed $team_id
    * @param mixed $date
    */
    public function get_games_by_team_date($team_id,$date)
    {
    	$params=array($team_id,$date);
		$sql="SELECT g.venue_id,g.game_id, g.game_date , g.start_time, g.end_time, g.schedule_id  ,tg.team_id 
				FROM schedule.game g 
				INNER JOIN schedule.schedule s 
				ON s.schedule_id = g.schedule_id 
				AND s.deleted_flag='f' 
				AND g.deleted_flag = 'f' 
				INNER JOIN schedule.team_game tg 
				ON tg.game_id = g.game_id 
				AND tg.team_id = ?
				AND g.game_date = ? ";
		return $this->db->query($sql,$params)->result_array();		
    }
    public function get_games_by_sch_date($s,$date)
    {
    	$params=array($s,$date);
		$sql="SELECT g.game_id, g.game_date , g.start_time, g.end_time ,ht.team_name AS home_name, at.team_name AS away_name ,v.venue_name,gr.home_score,gr.away_score
				FROM schedule.game g 
				INNER JOIN schedule.schedule s 
					ON s.schedule_id = g.schedule_id 
					AND s.deleted_flag='f' 
					AND g.deleted_flag = 'f' 
					AND s.schedule_id=? 
					AND g.game_date = ? 	
				INNER JOIN schedule.team_game h   ON h.game_id=g.game_id AND h.team_ishome='t'  
				INNER JOIN public.team ht         ON h.team_id = ht.team_id 
				INNER JOIN schedule.team_game a   ON a.game_id=g.game_id AND a.team_ishome='f' 
				INNER JOIN public.team at         ON a.team_id = at.team_id 
				INNER JOIN public.venue v         ON v.venue_id=g.venue_id 
				LEFT OUTER JOIN schedule.game_result gr ON gr.game_id = g.game_id AND gr.is_valid='t' AND         gr.deleted_flag = 'f'   ";
		return $this->db->query($sql,$params)->result_array();		
    }
    
        /**
    * get all gaames for this venue id and date, regardless of sch/league/season/team/etc
    * 
    * @param mixed $team_id
    * @param mixed $date
    */

    /**
    * also shows valid scores, or blank if none (left join)
    * 
    * @param mixed $schedule
    */
    public function get_games_scores($schedule)
    {
    	$sql="SELECT      ht.team_name   AS home_name, ht.team_id AS home_id , 
						  at.team_name AS away_name,   at.team_id AS away_id ,
						  v.venue_name , v.venue_id ,
						  x.game_date,  x.venue_id, x.game_id, x.start_time,x.end_time, 
						  gr.home_score,gr.away_score,x.schedule_id, s.schedule_name, lss.season_id, lss.league_id 
                FROM       schedule.game x  
				INNER JOIN schedule.team_game h   ON h.game_id=x.game_id AND h.team_ishome='t'  AND x.schedule_id = ? AND x.deleted_flag='f' 
				INNER JOIN public.team ht         ON h.team_id = ht.team_id 
				INNER JOIN schedule.team_game a   ON a.game_id=x.game_id AND a.team_ishome='f' 
				INNER JOIN public.team at         ON a.team_id = at.team_id 
				INNER JOIN schedule.schedule s ON x.schedule_id = s.schedule_id 
				INNER JOIN schedule.league_season_schedule lss ON lss.schedule_id=s.schedule_id 
				LEFT OUTER JOIN public.venue v         ON v.venue_id=x.venue_id 
				LEFT OUTER JOIN schedule.game_result gr ON gr.game_id = x.game_id AND gr.is_valid='t' AND         gr.deleted_flag = 'f'   
				ORDER BY x.game_date ASC ";
// x.game_date
        return $this->db->query($sql,$schedule)->result_array();
    }
    /***
    * Get all games for given schedule
    * 
    * @param mixed $schedule
    */
    public function get_games($schedule)
    {
        $sql = "SELECT      (SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id  WHERE tg.team_ishome = 't' AND tg.game_id = x.game_id LIMIT 1) 
                            AS home_name, 
                             (SELECT t.team_id FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id  WHERE tg.team_ishome = 't' AND tg.game_id = x.game_id LIMIT 1) 
                            AS home_id, 
                             (SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id    WHERE tg.team_ishome = 'f' AND tg.game_id = x.game_id LIMIT 1) 
                            AS away_name, 
                            (SELECT t.team_id FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id    WHERE tg.team_ishome = 'f' AND tg.game_id = x.game_id LIMIT 1) 
                            AS away_id, 
                            x.game_date,  x.venue_id, x.game_id, x.start_time,x.end_time,
                            (SELECT v.venue_name FROM public.venue v WHERE v.venue_id = x.venue_id LIMIT 1) 
                            AS venue_name  
                FROM        schedule.game x  
                WHERE       x.schedule_id = ? 
                AND         x.deleted_flag = 'f'   
                ORDER BY    x.game_date  ASC";// AND         ".USER_CAN_ACCESS." ";
        return $this->db->query($sql,$schedule)->result_array();
    }
    
    public function get_games_by_season($season)
    {
        $sql = "SELECT      (SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id  WHERE tg.team_ishome = 't' AND tg.game_id = x.game_id LIMIT 1) 
                            AS home_name, 
                             (SELECT t.team_id FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id  WHERE tg.team_ishome = 't' AND tg.game_id = x.game_id LIMIT 1) 
                            AS home_id, 
                             (SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id    WHERE tg.team_ishome = 'f' AND tg.game_id = x.game_id LIMIT 1) 
                            AS away_name, 
                            (SELECT t.team_id FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id    WHERE tg.team_ishome = 'f' AND tg.game_id = x.game_id LIMIT 1) 
                            AS away_id, 
                            x.game_date,  x.venue_id, x.game_id, x.start_time,x.end_time,
                            (SELECT v.venue_name FROM public.venue v WHERE v.venue_id = x.venue_id LIMIT 1) 
                            AS venue_name  
                FROM        schedule.game x  
                INNER JOIN schedule.league_season_schedule lss ON lss.schedule_id = x.schedule_id 
                AND         lss.season_id=? 
                AND         x.deleted_flag = 'f'   ";// AND         ".USER_CAN_ACCESS." "                 ORDER BY    x.game_date  ASC;
        return $this->db->query($sql,$season)->result_array();
    }
    
    
    
    public function get_season_team_valid_games($season,$team)
    {
    	$params=array($season,$team);
        $sql = "SELECT      ht.team_name   AS home_name, ht.team_id AS home_id , 
						  at.team_name AS away_name,   at.team_id AS away_id ,
						  v.venue_name , v.venue_id ,
						  x.game_date,  x.venue_id, x.game_id, x.start_time,x.end_time, 
						  gr.home_score,gr.away_score,x.schedule_id, s.schedule_name, lss.season_id, lss.league_id 
                FROM       schedule.game x  
				INNER JOIN schedule.team_game h   ON h.game_id=x.game_id AND h.team_ishome='t'  AND   x.deleted_flag='f' 
				INNER JOIN public.team ht         ON h.team_id = ht.team_id 
				INNER JOIN schedule.team_game a   ON a.game_id=x.game_id AND a.team_ishome='f' 
				INNER JOIN public.team at         ON a.team_id = at.team_id 
				INNER JOIN schedule.schedule s ON x.schedule_id = s.schedule_id 
				INNER JOIN schedule.league_season_schedule lss ON lss.schedule_id=s.schedule_id AND lss.season_id=?
				LEFT OUTER JOIN public.venue v         ON v.venue_id=x.venue_id 
				LEFT OUTER JOIN schedule.game_result gr ON gr.game_id = x.game_id    
				
				
				WHERE   gr.is_valid='t' AND         gr.deleted_flag = 'f' 
				AND 
				
				(
				   ht.team_id = ? 
				OR at.team_id=?
				)
				ORDER BY x.game_date ASC   ";// AND         ".USER_CAN_ACCESS." "                 ORDER BY    x.game_date  ASC;
        return $this->db->query($sql,$params)->result_array();
    }
     public function get_season_team_games($season,$team,$date=null)
    {
    	
    	$params=array($season,$team,$team);
    	$w='';
    	if($date)
		{
			$params[]=date('Y-m-d',strtotime($date));
			$w=" AND x.game_date < ?";
		}
        $sql = "SELECT      ht.team_name   AS home_name, ht.team_id AS home_id , 
						  at.team_name AS away_name,   at.team_id AS away_id ,
						  v.venue_name , v.venue_id ,
						  x.game_date,  x.venue_id, x.game_id, x.start_time,x.end_time, 
						  gr.home_score,gr.away_score,x.schedule_id, s.schedule_name, lss.season_id, lss.league_id 
                FROM       schedule.game x  
				INNER JOIN schedule.team_game h   ON h.game_id=x.game_id AND h.team_ishome='t'  AND   x.deleted_flag='f' 
				INNER JOIN public.team ht         ON h.team_id = ht.team_id 
				INNER JOIN schedule.team_game a   ON a.game_id=x.game_id AND a.team_ishome='f' 
				INNER JOIN public.team at         ON a.team_id = at.team_id 
				INNER JOIN schedule.schedule s ON x.schedule_id = s.schedule_id 
				INNER JOIN schedule.league_season_schedule lss ON lss.schedule_id=s.schedule_id AND lss.season_id=?
				LEFT OUTER JOIN public.venue v         ON v.venue_id=x.venue_id 
				LEFT OUTER JOIN schedule.game_result gr ON gr.game_id = x.game_id    
				
				
				WHERE          gr.deleted_flag = 'f' 
				 
				
				AND (
				   ht.team_id = ? 
				OR at.team_id=?
				)
				".$w."
				ORDER BY x.game_date ASC   ";// AND         ".USER_CAN_ACCESS." "                 ORDER BY    x.game_date  ASC;
				
				

				
        return $this->db->query($sql,$params)->result_array();
    }   
    /**
	* get data on one single game based on game id
	* 
	* @param mixed $gameid
	*/
    public function get_game($gameid)
    {//
        $sql = "SELECT      (SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id  
                            WHERE tg.team_ishome = 't' AND tg.game_id = x.game_id LIMIT 1) 
                            AS home_name, 
                             (SELECT t.team_id FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id 
                            WHERE tg.team_ishome = 't' AND tg.game_id = x.game_id LIMIT 1) 
                            AS home_id, 
                             (SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id   
                             WHERE tg.team_ishome = 'f' AND tg.game_id = x.game_id LIMIT 1) 
                            AS away_name, 
                            (SELECT t.team_id FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id   
                             WHERE tg.team_ishome = 'f' AND tg.game_id = x.game_id LIMIT 1) 
                            AS away_id, 
                            x.game_date,  x.venue_id, x.game_id, x.start_time, x.end_time,
                            (SELECT v.venue_name FROM public.venue v WHERE v.venue_id = x.venue_id LIMIT 1) 
                            AS venue_name , x.schedule_id
                FROM        schedule.game x  
                WHERE       x.game_id = ? 
                AND         x.deleted_flag = 'f'  LIMIT 1";//   AND         ".USER_CAN_ACCESS." LIMIT 1 ";
        return $this->db->query($sql,$gameid)->result_array();        
    }
    
    
    
    /**
    * gets season id and schedule id for this game
    * 
    * @param mixed $game_id
    */
    public function get_game_ids($game_id)
    {
		$sql="SELECT lss.season_id, lss.schedule_id, lss.league_id  FROM schedule.league_season_schedule lss INNER JOIN schedule.schedule s ON s.schedule_id=lss.schedule_id 
INNER JOIN schedule.game g ON g.schedule_id=s.schedule_id AND g.game_id=?  ";
        return $this->db->query($sql,$game_id)->result_array();   
    }
    
    
    public function delete_game($gameid,$user)
    {
        $sql = 'SELECT schedule.delete_game(?,?)';//on
        $query = $this->db->query($sql,array($gameid,$user));
        $result= $query->first_row();
        return  (int)$result->delete_game;        
    }

	
	public function get_past_games_no_score($sch)
    {
		$sql="SELECT g.game_id, g.game_date , '' as home_score, '' as away_score, g.start_time,
						 (SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
						                            ON t.team_id = tg.team_id  
						                            WHERE tg.team_ishome = 't' AND tg.game_id = g.game_id LIMIT 1) 
						                            AS home_name, 
						(SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
						                            ON t.team_id = tg.team_id   
						                             WHERE tg.team_ishome = 'f' AND tg.game_id = g.game_id LIMIT 1) 
						                            AS away_name
						,(SELECT v.venue_name FROM public.venue v WHERE v.venue_id = g.venue_id LIMIT 1) 
						                            AS venue_name
				FROM schedule.game g 
				WHERE g.game_date < LOCALTIMESTAMP 
				AND g.game_id NOT IN (SELECT r.game_id FROM schedule.game_result r WHERE r.is_valid = 't') 
				AND g.schedule_id =? AND g.deleted_flag='f' 
				ORDER BY g.game_date ";

        return $this->db->query($sql,$sch)->result_array();   
    }
    public function get_past_season_games_no_score($season_id)
    {
		$sql="SELECT g.game_id, g.game_date , '' as home_score, '' as away_score, g.start_time,g.end_time
						 ,(SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
						                            ON t.team_id = tg.team_id  
						                            WHERE tg.team_ishome = 't' AND tg.game_id = g.game_id LIMIT 1) 
						                            AS home_name, 
						(SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
						                            ON t.team_id = tg.team_id   
						                             WHERE tg.team_ishome = 'f' AND tg.game_id = g.game_id LIMIT 1) 
						                            AS away_name
						,(SELECT v.venue_name FROM public.venue v WHERE v.venue_id = g.venue_id LIMIT 1) 
						                            AS venue_name
				FROM schedule.game g 
				INNER JOIN schedule.league_season_schedule lss ON lss.schedule_id=g.schedule_id AND lss.season_id=? AND g.deleted_flag='f' 
				WHERE g.game_date < LOCALTIMESTAMP 
				AND g.game_id NOT IN (SELECT r.game_id FROM schedule.game_result r WHERE r.is_valid = 't') 
				ORDER BY g.game_date ";

        return $this->db->query($sql,$season_id)->result_array();   
    }
    /**
    * if past == false, will get FUTURE games, default to past games 
    * status optional integer from lu_ table
    * 
    * @param mixed $season_id
    * @param mixed $past
    * @param mixed $status
    */    
     public function get_past_season_games( $season_id,  $past=true)//,$query_array=false
    {
    	$compare='<';
    	if(!$past){$compare='>';} 
    	//$status_clause='';
    	//convert to int for securityTHIS IS BAD METHOD, not used anymore, instead get_game_result_ids
    	/*
    	$first=true;
    	if($query_array && count($query_array))
    	{
    		$status_clause='AND (';
    		foreach($query_array as $status)
    		{
    			if($first===false){$status_clause.=" OR ";}//or only goes BETWEEN them, no extra leading or trailing
    			
				$status=(int)$status;//cast to an integer
    			$status_clause.="  sub.result_status=$status ";//careful here: onlly safe if its an integer NOT string
				$first=false;
    		}
    		$status_clause.=")";
    	}*/
    	//must be exactly one valid result per game                       
		$sql="SELECT g.game_id, g.game_date ,  g.start_time,v.venue_name ,gr.home_score,gr.away_score,gr.is_valid
							,th.team_name AS home_name,ta.team_name AS away_name
							
			FROM schedule.game g 
			INNER JOIN schedule.schedule s ON g.schedule_id=s.schedule_id AND s.deleted_flag=FALSE AND g.deleted_flag=FALSE 
				 AND g.game_date ".$compare." LOCALTIMESTAMP 
			INNER JOIN schedule.league_season_schedule lss ON lss.schedule_id =s.schedule_id AND lss.season_id=?
			INNER JOIN public.venue v ON  v.venue_id = g.venue_id 

			INNER JOIN  schedule.team_game tgh ON tgh.game_id = g.game_id AND tgh.team_ishome = 't'
			INNER JOIN public.team th ON th.team_id = tgh.team_id  
				
			INNER JOIN  schedule.team_game tga ON tga.game_id = g.game_id AND tga.team_ishome = 'f'
			INNER JOIN public.team ta ON ta.team_id = tga.team_id  				
			LEFT OUTER JOIN schedule.game_result gr ON  g.game_id=gr.game_id  	
			WHERE g.game_id IN (SELECT sub.game_id FROM schedule.game_result sub WHERE sub.deleted_flag='f'   )		 
			AND  gr.is_valid=TRUE  AND gr.deleted_flag=FALSE 			
			ORDER BY g.game_date, th.team_name ";
			//WHERE  g.game_id NOT IN (SELECT r.game_id FROM schedule.game_result r WHERE r.is_valid = 't') 
        return $this->db->query($sql,$season_id)->result_array();   
    }
    /**
    * gets max severity
    * 
    * @param mixed $game_id
    */
    public function get_game_severity_data($game_id)
    {
		$sql="SELECT r.game_result_id,lu.id,lu.status,lu.severity,lu.icon FROM schedule.game_result r 
			INNER JOIN schedule.lu_game_result_status lu ON lu.id = r.result_status 
			WHERE r.game_id = ? AND lu.severity IS NOT NULL ORDER BY lu.severity DESC  ";
		return $this->db->query($sql,$game_id)->result_array();
    }
    //get the lookup table
    public function get_lu_game_result_status()
    {
		return $this->db->query("SELECT id,status,icon FROM schedule.lu_game_result_status WHERE visible = TRUE ORDER BY visible_order")->result_array();   
    }
     
    /**
    * create one new game
    * 
    * @param mixed $vid
    * @param mixed $scheduleid
    * @param mixed $date
    * @param mixed $start
    * @param mixed $end
    * @param mixed $userid
    * @param mixed $owner
    * @param mixed $g_num
    */
    public function insert_game($vid,$scheduleid,$date,$start,$end,$userid,$owner,$g_num=null)
    {//assume the order of $details is as follows:
    //venue_id,$scheduleid,$date,$start,$end,$userid);
    	$details = array($vid,$scheduleid,$date,$start,$end,$userid,$owner,$g_num);
        $sql = 'SELECT schedule.insert_game(?,?,?,?,?,?,?,?)';
        $query = $this->db->query($sql,$details);
        $result= $query->first_row();
        $gameid =  (int)$result->insert_game;        
        return $gameid;
    }
    
        /**
    * xref between game and teams
    * 
    * @param mixed $details
    */
    public function insert_teamgame($gameid,$teamid,$ishome)
    {
    	$details=array($gameid,$teamid,$ishome);
        $sql = 'SELECT schedule.insert_teamgame(?,?,?)';
        $query = $this->db->query($sql,$details);
        $result= $query->first_row();
        return $result->insert_teamgame;   
    }
    
    
    
    /**
    * Updates game info
    * 
    * @param mixed $game_id
    * @param mixed $start_time
    * @param mixed $end_time
    * @param mixed $date
    * @param mixed $user
    */
     public function update_game_timeslot($game_id,$start_time,$end_time,$date,$user)
    {
    	$params = array($game_id,$start_time,$end_time,$date,$user);
        $sql = 'SELECT schedule.update_game_timeslot(?,?,?,?,?)';
        $query= $this->db->query($sql,$params);
        $result=$query->first_row();
        return $result->update_game_timeslot;		
		
    }
    
    
    public function update_swap_teams($game_id,$user)
    {
    	$params = array($game_id,$user);
		$sql='SELECT schedule.update_swap_teams(?,?)';
		return $this->db->query($sql,$params)->first_row()->update_swap_teams;
    }
    
    
    
    /**
    * Similar to update_game_timeslot, but also updates venue id
    * 
    * @param mixed $game_id
    * @param mixed $start_time
    * @param mixed $end_time
    * @param mixed $date
    * @param mixed $user
    */
    public function update_game($game_id,$start_time,$end_time,$date,$user,$vid)
    {
    	$params = array($game_id,$start_time,$end_time,$date,$user,$vid);
        $sql = 'SELECT schedule.update_game(?,?,?,?,?,?)';
        $query= $this->db->query($sql,$params);
        $result=$query->first_row();
        return $result->update_game;		
		
    }
	public function insert_game_backlog($act,$backlog,$swap,$request,$note)
	{
		$params=array($act,$backlog,$swap,$request,$note);
		$sql="SELECT schedule.insert_game_backlog(?,?,?,?,?)";
		return $this->db->query($sql,$params)->first_row()->insert_game_backlog;
	}
    
    
    public function add_team_game_exc($team,$game)
    {
		$params=array($team,$game);
		$sql="SELECT schedule.add_team_game_exc(?,?)";
		return $this->db->query($sql,$params)->first_row()->add_team_game_exc;
    }
    //no season id in this table, this is weird
    public function count_team_game_exc($team,$season)
    {
		$params=array($team);// ,$season);
		$sql="SELECT COUNT(*) as count FROM schedule.team_game_exception 
		WHERE team_id=? "; //AND season_id=?
		return $this->db->query($sql,$params)->first_row()->count;
    }	
}
?>
