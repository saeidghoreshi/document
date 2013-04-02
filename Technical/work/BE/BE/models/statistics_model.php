<?php

require_once('./endeavor/models/endeavor_model.php');
class Statistics_model extends Endeavor_model
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	
	public function insert_standings($team_id,$season_id,$rank,$rank_type_id)
	{
		$params=array($team_id,$season_id,$rank,$rank_type_id);
		$sql="SELECT schedule.insert_standings(?,?,?,?)";
		$query = $this->db->query($sql,$params);
        return $query->first_row()->insert_standings;  		
	}
	public function reset_standings($season_id,$rank_type_id)
	{
		$params=array($season_id,$rank_type_id);
		$sql="SELECT schedule.reset_standings(?,?)";
		$query = $this->db->query($sql,$params);
        return $query->first_row()->reset_standings; 
	}
	
	
	
	public function insert_statistics($team_id,$season_id,$stat_id,$value,$rank_type)
	{
		$params=array($team_id,$season_id,$stat_id,$value,$rank_type);
		$sql="SELECT schedule.insert_statistics(?,?,?,?,?)";
		$query = $this->db->query($sql,$params);
        return $query->first_row()->insert_statistics; 
	}
	
	
	public function get_rank_statistics($rank_type_id)
	{
		$params=array($rank_type_id);
		$sql="SELECT * FROM schedule.rank_statistics r 
			INNER JOIN schedule.lu_team_statistics lu ON lu.stat_id = r.stat_id  
		WHERE  r.rank_type_id = ? ORDER BY r.rank_order";//p.
		return $this->db->query($sql,$params)->result_array();
	}
	public function get_rank_statistics_used($rank_type_id)
	{
		$params=array($rank_type_id);
		$sql="SELECT * FROM schedule.rank_statistics r 
			INNER JOIN schedule.lu_team_statistics lu ON lu.stat_id = r.stat_id  
			WHERE  r.rank_type_id = ? 
			AND r.is_used = 't' 			
			ORDER BY r.rank_order ASC";//p.
		return $this->db->query($sql,$params)->result_array();
	}
	
	public function get_rank_display($rank_type_id)
	{
		$params=array($rank_type_id);
		$sql="SELECT * FROM schedule.rank_display r 
			INNER JOIN schedule.lu_team_statistics lu ON lu.stat_id = r.stat_id  
		WHERE  r.rank_type_id = ? ORDER BY r.rank_order";//p.
		return $this->db->query($sql,$params)->result_array();
	}
	
	public function get_rank_statistics_used_not_hth($rank_type_id)
	{
		$params=array($rank_type_id);
		$sql="SELECT * FROM schedule.rank_statistics r 
			INNER JOIN schedule.lu_team_statistics lu ON lu.stat_id = r.stat_id  
			WHERE  r.rank_type_id = ? 
			AND r.is_used = 't' 
			AND r.use_hth = 'f' 			
			ORDER BY r.rank_order";//p.
		return $this->db->query($sql,$params)->result_array();
	}
	
	public function get_rank_statistics_hth($rank_type_id)
	{
		$params=array($rank_type_id);
		$sql="SELECT * FROM schedule.rank_statistics r 
			INNER JOIN schedule.lu_team_statistics lu ON lu.stat_id = r.stat_id  
			WHERE  r.rank_type_id = ? 
			AND r.use_hth = 't' 			
			ORDER BY r.rank_order";//p.
		return $this->db->query($sql,$params)->result_array();
	}
	
	public function validate_game_result($game_result_id,$user,$valid='t')
    {//default to validate
    	$params = array($game_result_id,$user,$valid);
        $sql="SELECT schedule.validate_game_result(?,?,?)";
        $query= $this->db->query($sql,$params);
        $result=$query->first_row();
        return $result->validate_game_result;
    } 
    
    
    
    public function reject_result_submission_by_game($game_id,$user)
	{
		$params = array($game_id,$user);
        $sql="SELECT schedule.delete_result_submission_by_game(?,?)";
        $query= $this->db->query($sql,$params);
        $result=$query->first_row();
        return $result->delete_result_submission_by_game;
	}
	
	public function delete_result_submission($req_id,$user)
	{
		$params = array($req_id,$user);
        $sql="SELECT schedule.delete_result_submission(?,?)";
        $query= $this->db->query($sql,$params);
        $result=$query->first_row();
        return $result->delete_result_submission;
	}
	
	//gets season id and scheudle id
	public function get_game_result_data($game_result_id)
	{
		$sql="SELECT g.schedule_id , lss.season_id FROM schedule.game_result gr
			INNER JOIN schedule.game g ON g.game_id = gr.game_id AND gr.game_result_id = ?
			INNER JOIN schedule.league_season_schedule lss ON lss.schedule_id = g.schedule_id";
		return $this->db->query($sql,array($game_result_id))->result_array();	
	}
	
	
	
	
	/**
    * all games in this schedule
    * 
    * @param mixed $sch
    */
    public function get_valid_scores($sch)
    {               
        $sql = "SELECT x.game_result_id, x.game_id, x.home_score, x.away_score  ,
        					(SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id  
                            WHERE tg.team_ishome = 't' AND tg.game_id = x.game_id LIMIT 1) 
                            AS home_name,  
                             (SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id   
                             WHERE tg.team_ishome = 'f' AND tg.game_id = x.game_id LIMIT 1) 
                            AS away_name , g.game_date,v.venue_name,g.start_time
        		FROM schedule.game_result x 
        		INNER JOIN schedule.game g ON g.game_id = x.game_id AND    x.deleted_flag = 'f'  AND  g.deleted_flag = 'f'        AND x.is_valid='t' 	
        		INNER JOIN 	public.venue v on g.venue_id = v.venue_id 	
                WHERE  g.schedule_id = ?  
                ";        
        return $this->db->query($sql,$sch)->result_array();                        
    }
    
    
    /**
    * for this game
    * 
    * @param mixed $game_id
    */
    public function get_valid_scores_game($game_id)
    {               
        $sql = "SELECT x.game_result_id, x.game_id, x.home_score, x.away_score  ,
        					(SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id  
                            WHERE tg.team_ishome = 't' AND tg.game_id = x.game_id LIMIT 1) 
                            AS home_name,  
                             (SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id   
                             WHERE tg.team_ishome = 'f' AND tg.game_id = x.game_id LIMIT 1) 
                            AS away_name , g.game_date,v.venue_name,g.start_time
        		FROM schedule.game_result x 
        		INNER JOIN schedule.game g ON g.game_id = x.game_id AND    x.deleted_flag = 'f'  AND  g.deleted_flag = 'f'        AND x.is_valid='t' 	
        		INNER JOIN 	public.venue v on g.venue_id = v.venue_id 	
                WHERE  g.game_id = ?  
                ";        
        return $this->db->query($sql,$game_id)->result_array();                        
    }
    /**
    * like above but scores for all schedules in that season
    * 
    * @param mixed $season
    */
    public function get_valid_scores_season($season)
    {               
        $sql = "SELECT x.game_result_id, x.game_id, x.home_score, x.away_score  ,g.schedule_id,lss.league_id,
        					(SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id  
                            WHERE tg.team_ishome = 't' AND tg.game_id = x.game_id LIMIT 1) 
                            AS home_name,  
                             (SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id   
                             WHERE tg.team_ishome = 'f' AND tg.game_id = x.game_id LIMIT 1) 
                            AS away_name , g.game_date,v.venue_name,g.start_time
        		FROM schedule.game_result x 
        		INNER JOIN schedule.game g ON g.game_id = x.game_id AND    x.deleted_flag = 'f'  
        				AND  g.deleted_flag = 'f'        AND x.is_valid='t' 	
                INNER JOIN schedule.league_season_schedule lss ON lss.schedule_id=  g.schedule_id AND lss.season_id=?  
        		LEFT OUTER JOIN 	public.venue v on g.venue_id = v.venue_id 	
                ";        
        return $this->db->query($sql,$season)->result_array();                        
    }
    public function get_invalid_scores($sch)
    {               
        $sql = "SELECT x.game_result_id, x.game_id, x.home_score, x.away_score  ,
        					(SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id  
                            WHERE tg.team_ishome = 't' AND tg.game_id = x.game_id LIMIT 1) 
                            AS home_name,  
                             (SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
                            ON t.team_id = tg.team_id   
                             WHERE tg.team_ishome = 'f' AND tg.game_id = x.game_id LIMIT 1) 
                            AS away_name , g.game_date, v.venue_name,g.start_time
        		FROM schedule.game_result x 
        		INNER JOIN schedule.game g ON g.game_id = x.game_id AND    x.deleted_flag = 'f'  AND  g.deleted_flag = 'f'  AND x.is_valid='f'      	
        		INNER JOIN 	public.venue v on g.venue_id = v.venue_id 
                WHERE  g.schedule_id = ?  
                ";        
        return $this->db->query($sql,$sch)->result_array();                        
    }
    
    public function get_games_scores_count($sch)
    {
		$sql="SELECT g.game_id , g.game_date ,
						(SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
					                            ON t.team_id = tg.team_id  
					                            WHERE tg.team_ishome = 't' AND tg.game_id = g.game_id LIMIT 1) 
					                            AS home_name,  
					                             (SELECT t.team_name FROM public.team t INNER JOIN schedule.team_game tg 
					                            ON t.team_id = tg.team_id   
					                             WHERE tg.team_ishome = 'f' AND tg.game_id = g.game_id LIMIT 1) 
					                            AS away_name , g.game_date, ven.venue_name,g.start_time,
								(SELECT COUNT(*) FROM schedule.game_result r WHERE r.game_id = g.game_id AND  r.is_valid=FALSE AND r.deleted_flag=FALSE) as count_new , 
								(SELECT COUNT(*) FROM schedule.game_result v WHERE v.game_id = g.game_id AND  v.is_valid=TRUE  AND v.deleted_flag=FALSE) as count_valid  
				FROM  schedule.game g 
				INNER JOIN 	public.venue ven on g.venue_id = ven.venue_id AND g.schedule_id=?
				WHERE g.game_id IN(SELECT gr.game_id FROM  schedule.game_result gr WHERE gr.deleted_flag=FALSE and gr.is_valid=FALSE)   
				ORDER BY count_new DESC	";		
        return $this->db->query($sql,$sch)->result_array();   
    }
    
    
    /**
    * for one single game
    * 
    * @param mixed $game_id
    */
    public function get_result_sumbissions($game_id,$hide_discarded)
    {
    	$hide='';
    	if($hide_discarded){$hide=' AND r.result_status != 5 ';}
		$sql = "SELECT 		r.game_result_id, r.game_id, r.home_score, r.away_score 
							,r.form_name,r.form_email,r.form_date	
							,lu.status,lu.id,lu.icon
							,th.team_name AS home_name
							,ta.team_name AS away_name
		 		FROM 		schedule.game_result r 
		 		INNER JOIN  schedule.lu_game_result_status lu 
		 					ON lu.id = r.result_status
		 					AND 		r.game_id = ?   AND r.deleted_flag='f' ".$hide."
		 		INNER JOIN  schedule.team_game tgh ON tgh.game_id = r.game_id AND tgh.team_ishome = 't'
				INNER JOIN  public.team th ON th.team_id = tgh.team_id  
				INNER JOIN  schedule.team_game tga ON tga.game_id = r.game_id AND tga.team_ishome = 'f'
				INNER JOIN  public.team ta ON ta.team_id = tga.team_id  ";
        return $this->db->query($sql,$game_id)->result_array();   
    }
	/**
	* inserts a new game result. default is not valid
	* if valid is true, stored procedure will make sure its the only valid result for this game by updating others
	* 
	* @param mixed $hscore
	* @param mixed $ascore
	* @param mixed $gid
	* @param mixed $user
	* @param mixed $owner
	* @param mixed $valid
	*/
    public function insert_game_result($hscore,$ascore,$gid,$user,$owner,$user_name,$user_email, $valid='t')
    {
    	$params = array($hscore,$ascore,$gid,$user,$owner,$user_name,$user_email,$valid);
        $sql="SELECT schedule.insert_game_result(?,?,?,?,?,?,?,?)";
        $query= $this->db->query($sql,$params);
        $result=$query->first_row();
        return $result->insert_game_result;
    }
    
    public function unvalidate_all_scores($user,$game)
    {
    	$params = array($user,$game);
        $sql="SELECT schedule.unvalidate_all_scores(?,?)";
        return $this->db->query($sql,$params)->first_row()->unvalidate_all_scores;		
    }
    
    public function update_game_score($result_id,$ishome,$score,$user)
    {
		$params = array($result_id,$ishome,$score,$user);
        $sql="SELECT schedule.update_game_score(?,?,?,?)";
        $query= $this->db->query($sql,$params);
        $result=$query->first_row();
        return $result->update_game_score;
    }
    
    /**
	* get team rankings for this season
	* 
	* @param mixed $season_id
	*/
    public function get_season_standings($season_id)
    {
		$sql="  SELECT   	tss.rank , t.team_id, t.team_name  ,tss.season_id, 
							tss.rank_type_id, rt.rank_abbr, xref.division_id, 
							rt.rank_name 
				FROM 		schedule.team_season_standings  tss 
				INNER JOIN  public.team t				 			ON t.team_id = tss.team_id   
				INNER JOIN  schedule.lu_rank_type rt     			ON tss.rank_type_id = rt.rank_type_id  
														 			AND  tss.season_id = ?  
				INNER JOIN  schedule.xref_team_season_division xref ON xref.team_id = t.team_id 
																	AND xref.season_id = ? 
				ORDER BY	tss.rank ASC  ";
		return $this->db->query($sql,array($season_id,$season_id))->result_array();
    }
    
    /**
    * team rankings filtered by division
    * 
    * @param mixed $season_id
    * @param mixed $div_id
    */
    public function get_season_div_standings($season_id,$div_id)
    {
		$sql="  SELECT   tss.rank , t.team_id, t.team_name, xref.division_id  ,tss.season_id, 
						 tss.rank_type_id, rt.rank_abbr, 
						 rt.rank_name 
				FROM 	 schedule.team_season_standings  tss 
				INNER JOIN  public.team t				 ON t.team_id = tss.team_id 
											 			AND tss.season_id = ? 
				INNER JOIN  schedule.xref_team_season_division xref ON xref.team_id = t.team_id 
																	AND xref.season_id = ? 
																	AND xref.division_id = ?	
				INNER JOIN  schedule.lu_rank_type rt     ON tss.rank_type_id = rt.rank_type_id   
				ORDER BY tss.rank ASC  ";
		return $this->db->query($sql,array($season_id,$div_id,$season_id,$div_id))->result_array();
    }
    
    
    public function get_rank_types($league_id,$season_id)
    {
		$sql="SELECT * FROM schedule.league_season_standings r WHERE r.league_id = ? AND r.season_id=?  AND r.deleted_flag='f'";
		return $this->db->query($sql,array($league_id,$season_id))->result_array();						
    }
    public function get_root_rank_types($league_id,$season_id)
    {
		$sql="SELECT rank_name, rank_type_id FROM schedule.league_season_standings r
				 WHERE r.league_id = ? AND r.season_id=?  AND r.deleted_flag='f' AND r.parent_rank_type_id IS NULL";
		return $this->db->query($sql,array($league_id,$season_id))->result_array();						
    }    
    public function get_root_rank_types_season($season_id)
    {
		$sql="SELECT rank_name, rank_type_id FROM schedule.league_season_standings r
				 WHERE  r.season_id=?  AND r.deleted_flag='f' AND r.parent_rank_type_id IS NULL";
		return $this->db->query($sql,array($season_id))->result_array();						
    }
    public function get_rank_types_published($league_id)
    {
		$sql="SELECT * FROM schedule.league_season_standings r WHERE r.league_id = ? AND r.deleted_flag='f' AND r.is_published='t' ";
		return $this->db->query($sql,array($league_id))->result_array();						
    }
    
    public function get_rank_type_info($rank_type_id)
    {
		$sql="SELECT * FROM schedule.league_season_standings r WHERE r.rank_type_id = ? AND r.deleted_flag='f'";
		return $this->db->query($sql,array($rank_type_id))->result_array();						
    }
    
    
    /**
    * put your comment there...
    * 
    * @param mixed $league_id
    * @param mixed $name
    * @param mixed $user
    * @param mixed $owner
    * @param mixed $p
    */
    public function insert_rank_type($league_id,$name,$user,$owner,$season_id,$p=null)
    {
    	$params=array($league_id,$name,$user,$owner,$season_id,$p);//y
		$sql = "SELECT schedule.insert_rank_type(?,?,?,?,?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->insert_rank_type;
    }
    
    public function delete_rank_type($rank_type_id,$user)
    {
		$params=array($rank_type_id,$user);//y
		$sql = "SELECT schedule.delete_rank_type(?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->delete_rank_type;;         
    }
    /**
    * will toggle to opposite of current status
    * 
    * @param mixed $rank_id
    */
    public function publish_rank_type($rank_id)
	{
		$params=array($rank_id);
		$sql = "SELECT schedule.publish_rank_type(?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->publish_rank_type;	
	}
	
	
	/**
	* not allowed to update season
	* 
	* @param mixed $rank_type_id
	* @param mixed $stat_id
	* @param mixed $order
	* @param mixed $used
	*/
	public function update_rank_display($rank_type_id,$stat_id,$order,$used)
	{
		$params=array($rank_type_id,$stat_id,$order,$used);
		$sql = "SELECT schedule.update_rank_display(?,?,?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->update_rank_display;
	}
	

	
	/**
	* user_option true, for menu
	* 
	*/
	public function get_user_statistics()
	{
		$sql="SELECT * FROM schedule.lu_team_statistics WHERE user_option = 't' ORDER BY stat_id";
		return $this->db->query($sql)->result_array();
	}
	/**
	* all that are displayed user option or not
	* 
	*/
	public function get_display_statistics()
	{
		$sql="SELECT * FROM schedule.lu_team_statistics WHERE sort_class IS NOT NULL ORDER BY stat_id";//avoid HTH, its not a sortable stat
		return $this->db->query($sql)->result_array();
	}
	
	public function add_rank_statistics($stat_id,$rank_type_id,$hth)
	{
		$params=array($stat_id,$rank_type_id,$hth);
		$sql = "SELECT schedule.add_rank_statistics(?,?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->add_rank_statistics;
	}
	
	public function delete_rank_statistics($stat_id,$rank_type_id,$hth)
	{
		$params=array($stat_id,$rank_type_id,$hth);
		$sql = "SELECT schedule.delete_rank_statistics(?,?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->delete_rank_statistics;
	}
	
	public function update_used_rank_statistics($stat_id,$rank_type_id,$hth,$used,$order)
	{
		$params=array($stat_id,$rank_type_id,$hth,$used,$order);
		$sql = "SELECT schedule.update_used_rank_statistics(?,?,?,?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->update_used_rank_statistics;
	}
	
	public function update_rank_points($rank_type_id,$w,$l,$t,$name,$user,$p=null)
	{
		$params=array($rank_type_id,$w,$l,$t,$name,$user,$p);
		$sql = "SELECT schedule.update_rank_points(?,?,?,?,?,?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->update_rank_points;
	}

	public function update_rank_parent($rank_type_id,$parent)
	{
		$params=array($rank_type_id,$parent);
		$sql = "SELECT schedule.update_rank_parent(?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->update_rank_parent;
	}
	
	public function update_rank_order($rank_type_id,$stat_id,$hth,$order)
	{
		$params=array($rank_type_id,$stat_id,$hth,$order);
		$sql = "SELECT schedule.update_rank_order(?,?,?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->update_rank_order;
	}
 
	
	
	
	public function update_display_order($rank_type_id,$stat_id,$order)
	{
		$params=array($rank_type_id,$stat_id,$order);
		$sql = "SELECT schedule.update_display_order(?,?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->update_display_order;
		
	}
	public function get_rank_divisions($rank_type_id)
	{
		$sql="SELECT h_division_id ,a_division_id,is_used,rank_type_id 
				FROM schedule.rank_divisions rd WHERE rd.rank_type_id=? ORDER BY h_division_id ,a_division_id";
		return $this->db->query($sql,$rank_type_id)->result_array();		
	}
	
	public function insert_rank_divisions($rank,$hd,$ad,$used)
	{
		$params=array($rank,$hd,$ad,$used);
		$sql = "SELECT schedule.insert_rank_divisions(?,?,?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->insert_rank_divisions;
	}
	public function update_rank_wildcard($rank,$div,$wc)
	{
		$params=array($rank,$div,$wc);
		$sql = "SELECT schedule.update_rank_wildcard(?,?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->update_rank_wildcard;
	}
	public function get_rank_wildcard($season_id,$rank_type_id)
	{
		//do left outer join on pooldivisions
		$sql="SELECT     d.division_id, d.division_name , r.rank_type_id, r.wildcard_teams  
			FROM             public.league_division d 
			INNER JOIN public.season_division sd  ON sd.division_id = d.division_id AND sd.season_id=? 
						AND sd.deleted_flag=FALSE AND d.deleted_flag=FALSE AND d.only_teams=TRUE
			LEFT OUTER JOIN schedule.rank_wildcard r     ON              d.division_id = r.division_id 
			AND r.rank_type_id=?       ";
		return $this->db->query($sql,array($season_id,$rank_type_id))->result_array();	
	}
	public function get_rank_wildcard_div($rank_type_id,$div)
	{//do left outer join on pooldivisions
		$sql="SELECT     r.wildcard_teams  
			FROM        schedule.rank_wildcard r 
			WHERE            r.rank_type_id=?      AND r.division_id=? LIMIT 1";
		return $this->db->query($sql,array($rank_type_id,$div))->result_array();	
	}
	/**
	* a copy of this exists in global websites standings_model also
	* 
	* @param mixed $season_id
	* @param mixed $rank_type_id
	* @param mixed $sort_query
	*/
	public function get_standings_by_order($season_id,$rank_type_id)//for now just do gp
	{//AND  tsd.season_id=? AND t.deleted_flag=\'f\' 

		$sql='SELECT 	 t.team_id,  t.team_name, d.division_id, d.division_name , srt.rank_type_id ,sd.season_id,
				tss.rank,
				x1.value AS "GP",
				x2.value AS "W",
				x3.value AS "L" ,
				x4.value AS "PTS",
				x5.value AS "GB" ,
				x6.value AS "RF" ,
				x7.value AS "RA" ,
				x8.value AS "RD" ,
				x9.value AS "PCT" ,
				x10.value AS "T"	
			  
			  FROM 	public.team t	 
			  	INNER JOIN public.team_season ts ON ts.team_id = t.team_id AND ts.deleted_flag=FALSE 
			  	
				INNER JOIN public.team_season_division tsd ON tsd.team_season_id = ts.id  AND tsd.deleted_flag=FALSE 
				
				INNER JOIN public.season_division sd ON sd.id=tsd.season_division_id AND sd.deleted_flag=FALSE AND sd.season_id=? 

				INNER JOIN public.league_division d             ON d.division_id = sd.division_id   

				INNER JOIN  schedule.league_season_standings srt      ON d.league_id = srt.league_id AND srt.rank_type_id = ? 

				LEFT OUTER JOIN schedule.team_season_standings tss ON t.team_id=tss.team_id   AND tss.rank_type_id=srt.rank_type_id  			  

				LEFT OUTER  JOIN schedule.team_season_statistics x1 ON t.team_id = x1.team_id  AND x1.rank_type_id = srt.rank_type_id  AND x1.stat_id=1 
				LEFT OUTER  JOIN schedule.team_season_statistics x2 ON t.team_id = x2.team_id  AND x2.rank_type_id = srt.rank_type_id  AND x2.stat_id=2 
				LEFT OUTER  JOIN schedule.team_season_statistics x3 ON t.team_id = x3.team_id  AND x3.rank_type_id = srt.rank_type_id  AND x3.stat_id=3 
				LEFT OUTER  JOIN schedule.team_season_statistics x4 ON t.team_id = x4.team_id  AND x4.rank_type_id = srt.rank_type_id  AND x4.stat_id=4 
				LEFT OUTER  JOIN schedule.team_season_statistics x5 ON t.team_id = x5.team_id  AND x5.rank_type_id = srt.rank_type_id  AND x5.stat_id=5 
				LEFT OUTER  JOIN schedule.team_season_statistics x6 ON t.team_id = x6.team_id  AND x6.rank_type_id = srt.rank_type_id  AND x6.stat_id=6 
				LEFT OUTER  JOIN schedule.team_season_statistics x7 ON t.team_id = x7.team_id  AND x7.rank_type_id = srt.rank_type_id  AND x7.stat_id=7
				LEFT OUTER  JOIN schedule.team_season_statistics x8 ON t.team_id = x8.team_id  AND x8.rank_type_id = srt.rank_type_id  AND x8.stat_id=8 
				LEFT OUTER  JOIN schedule.team_season_statistics x9 ON t.team_id = x9.team_id  AND x9.rank_type_id = srt.rank_type_id  AND x9.stat_id=9 
			    LEFT OUTER JOIN schedule.team_season_statistics x10 ON t.team_id = x10.team_id AND x10.rank_type_id = srt.rank_type_id AND x10.stat_id=10 
				WHERE t.team_id IN  
					(
					SELECT tgm.team_id 
					FROM schedule.team_game tgm 
					INNER JOIN schedule.game gm ON gm.game_id = tgm.game_id AND gm.deleted_flag=FALSE 
					INNER JOIN schedule.schedule s ON s.schedule_id = gm.schedule_id  AND s.deleted_flag=FALSE 
					INNER JOIN schedule.league_season_schedule lss ON lss.schedule_id=s.schedule_id AND lss.season_id=?
					)				
					 
				ORDER BY  	 	tss.rank ASC 
				';//AND s.is_published=TRUE 
		
        return  $this->db->query($sql,array($season_id,$rank_type_id,$season_id))->result_array();
        
        
		//return $this->format_games_back($result);		

	}
	
	
	public function update_display_level($level,$rank_type_id)
	{
		$params=array($level,$rank_type_id);
		$sql = "SELECT schedule.update_display_level(?,?)";
        $query= $this->db->query($sql,$params);
        return $query->first_row()->update_display_level;
	}
	
	
	
	
	
	
	public function update_rank_order_force_top_season_stat($season,$top_stat_id=9)//default to win percentage
	{
		$rt=$this->get_root_rank_types_season($season);
		
		if(!count($rt)) return;
		
		
		
		
		$rt_id=$rt[0]['rank_type_id'];
		
		

		$this->update_used_rank_statistics($top_stat_id,$rt_id,'f','t',1);
		
		//top is win perc
		$above_win_pct=array();
		
		
		$stats=$this->get_rank_statistics($rt_id);
		
		
		//$win_pct_row=null;
		
		
		
		
		foreach($stats as $st)
		{
			$id=$st['stat_id'];
			//always ignore hth
			if($st['use_hth']=='f')
			{
				if( $id  != $top_stat_id)
					$above_win_pct[]=$st;
				else
				{
					//$win_pct_row=$st;
					break;
				}
				
			}
			
		}
		
		
		$this->update_rank_order($rt_id,$top_stat_id,'f',1);
		
		//$count_above = count($above_win_pct);
		
		//move 1st to second, second to third, etc
		foreach($above_win_pct as $ab)
		{
			
			$old=(int)$ab['rank_order'];
			$above_id=$ab['stat_id'];
			 
			if($above_id==$top_stat_id){continue;}//this line is redundant i know
			
			$this->update_rank_order($rt_id,$above_id,'f',$old+1);
			
		}
		
		//first place win perct at top, with is used true
		
		
		
		
		
		//public function update_rank_order($rank_type_id,$stat_id,$hth,$order)
		
		
		//	public function update_used_rank_statistics($stat_id,$rank_type_id,$hth,$used)
		
		
		
		
	}
	
}
