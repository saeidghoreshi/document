<?php
require_once('./endeavor/models/endeavor_model.php');

class Divisions_model extends Endeavor_model
{
  
  	public function __construct()
	{
		parent::__construct();
	}
  	/**
  	* Get all subdivisions of the given division
  	* 
  	* @param mixed $parent_id
  	*/
  	public function get_sub_divisions($parent_id)
	{
		$sql = "SELECT   x.division_id, x.division_name, x.parent_division_id , x.only_teams 
                FROM     public.league_division x 
                WHERE    x.deleted_flag = 'f' 
                AND 	 x.parent_division_id = ? ";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,$parent_id)->result_array();
		
	}
	
	public function get_season_schedule_divisions_have_games($season,$schedule)
	{
		$sql = "SELECT 		d.division_id, parent_division_id, division_name, only_teams
				FROM 		public.league_division d
					
					WHERE d.division_id IN  
						(   SELECT sd.division_id FROM public.season_division sd 
							INNER JOIN public.team_season_division tsd ON tsd.season_division_id = sd.id AND sd.deleted_flag=FALSE and tsd.deleted_flag=FALSE 
							 			AND		sd.season_id = ? 
							 INNER JOIN public.team_season ts ON ts.id=tsd.team_season_id and ts.deleted_flag=FALSE 
							 INNER JOIN schedule.team_game tg ON tg.team_id = ts.team_id 
							INNER JOIN schedule.game g ON g.game_id = tg.game_id  AND  g.schedule_id = ? 	AND g.deleted_flag=FALSE 
						)
  				ORDER BY	d.seq
				";//INNER JOIN schedule.schedule s ON s.schedule_id = g.schedule_id   AND s.is_published = 't' AND s.deleted_flag='f' not needed
		$query = $this->db->query($sql, array($season,$schedule));
		return $query->result_array();
	}
	public function get_season_divisions_have_games($season)
	{
		$sql = "SELECT 		d.division_id, parent_division_id, division_name, only_teams
				FROM 		public.league_division d
					
					WHERE d.division_id IN  
						(    SELECT sd.division_id FROM public.season_division sd 
							INNER JOIN public.team_season_division tsd ON tsd.season_division_id = sd.id AND sd.deleted_flag=FALSE and tsd.deleted_flag=FALSE 
							 			AND		sd.season_id = ? 
							 INNER JOIN public.team_season ts ON ts.id=tsd.team_season_id and ts.deleted_flag=FALSE 
							 INNER JOIN schedule.team_game tg ON tg.team_id = ts.team_id 
							INNER JOIN schedule.game g ON g.game_id = tg.game_id   	AND g.deleted_flag=FALSE 
						)
  				ORDER BY	d.seq
				";//INNER JOIN schedule.schedule s ON s.schedule_id = g.schedule_id   AND s.is_published = 't' AND s.deleted_flag='f' not needed
		$query = $this->db->query($sql, array($season));
		return $query->result_array();
	}
	

	
	public function get_division_extended_name($division_id)
	{
		$sql="SELECT 		d.division_id, parent_division_id, division_name, only_teams
				FROM 		public.league_division d 
				WHERE d.deleted_flag='f' AND d.division_id=?";
		$result=$this->db->query($sql, array($division_id))->result_array();
		
		$div=$result[0];
		return $this->recursive_parent_div_name($div['division_name'],$div['division_id'],$div['parent_division_id']);
		
	}
		/**
	* takes in an array (DB result) of pool divs and formats/orderes nicely
	* 
	* @param mixed $divs
	*/
	
	private function recursive_parent_div_name(&$name,$div_id,$parent_id,$sep=' : ')
	{
		//echo $name;
		if($parent_id==null) return $name;
		$p=$this->get_division($parent_id);
		$name=$p[0]['division_name'] . $sep . $name;
		return $this->recursive_parent_div_name($name,$p[0]['division_id'],$p[0]['parent_division_id'],$sep);
		
	}
	
	public function get_formatted_indented_divisions($season_id)
	{
		$top_level = $this->get_parent_divisions($season_id);
		$table=array();//array_values($top_level);//copy array
		
		foreach($top_level as $div) 
		{
			$p=$div['division_id'];
			$depth=0;
			$this->recursive_indented_divisions($p,$table,&$depth);
			
			
		}

		return $table;
		
	}
	
	/**
	* lists out subdivisions of parent below it in given table, and tabbed over (characters added in front of name)
	* to use, call this on each division that has a null parent, passing in zero each time, and the same table by reference 
	* 
	* @param mixed $parent_id
	* @param mixed $table
	* @param mixed $depth
	*/
	private function recursive_indented_divisions($parent_id,&$table,&$depth,$indent_by=" - ")
	{
	   // echo "starting at dep.".$depth." for parent ".$parent_id."\n";
		$prefix='';
		$i=0;
		;//extra spaces get removed by json encode ..? so dash
		while($i < $depth)
		{
			$i++;
			$prefix.=$indent_by;
		}
		$depth++;
		$this_div =($this->get_division($parent_id));
		$this_div=$this_div[0];
		$this_div['division_name']=$prefix.$this_div['division_name'];
		$table[]=$this_div;
		
		$subs=$this->get_sub_divisions($parent_id);
		if(count($subs)==0)
		{
			$depth--;
			return;
		}		
		$total=count($subs);
			//$n=0;
		foreach($subs as $subdiv)
		{
			$id=$subdiv['division_id'];
			//echo "internal loop given ".$id." n=$n, there is a total of ".$total."\n";
			$this->recursive_indented_divisions($id,$table,$depth,$indent_by);
			//$n++;
		}	
		$depth--;//this is important!! 
	}
	
	/**
	* also includes a count of the number of teams
	* 
	* @param mixed $parent_id
	* @param mixed $season_id
	*/
	public function get_sub_divisions_tc($parent_id,$season_id)
	{
		$sql = "SELECT   x.division_id, x.division_name, x.parent_division_id ,? as season_id
		,(SELECT COUNT(*) FROM public.league_division s 
		WHERE s.parent_division_id = x.division_id AND s.deleted_flag='f') AS sub_count 
			 , x.only_teams , 
			 (SELECT COUNT(*) 
			 	FROM public.team_season_division  tsd 
			 	INNER JOIN public.season_division sd ON tsd.season_division_id = sd.id  AND tsd.deleted_flag=false
			 						AND sd.division_id = x.division_id AND sd.season_id = ? AND sd.deleted_flag=false
			 	 INNER JOIN public.team_season ts ON ts.id=tsd.team_season_id and ts.deleted_flag=FALSE 
			 	 INNER JOIN public.team t ON t.team_id=ts.team_id AND t.deleted_flag=false  AND t.team_status_id=1 ) AS team_count 
                FROM     public.league_division x 
                WHERE    x.deleted_flag = 'f' 
                AND 	 x.parent_division_id = ? ";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,array($season_id,$season_id,$parent_id))->result_array();
		
	}
	/**
	* also includes a count of number of teams assigned
	* 
	* @param mixed $league_id
	* @param mixed $season_id
	*/
	public function get_parent_divisions_tc($league_id,$season_id)
	{
		$sql = "SELECT   x.division_id, x.division_name, x.parent_division_id  , 
			(SELECT COUNT(*) FROM public.league_division s 
			WHERE s.parent_division_id = x.division_id AND s.deleted_flag='f') AS sub_count 
			,     x.only_teams , 
			  (SELECT COUNT(*) 
			 	FROM public.team_season_division  tsd 
			 	INNER JOIN public.season_division sd ON tsd.season_division_id = sd.id  AND tsd.deleted_flag=false
			 						AND sd.division_id = x.division_id AND sd.season_id = ? AND sd.deleted_flag=false
			 	 INNER JOIN public.team_season ts ON ts.id=tsd.team_season_id and ts.deleted_flag=FALSE 
			 	 INNER JOIN public.team t ON t.team_id=ts.team_id AND t.deleted_flag=false  AND t.team_status_id=1 ) AS team_count 
                FROM     public.league_division x 
                INNER JOIN public.season_division sd ON x.division_id=sd.division_id AND sd.season_id=?
                WHERE    x.deleted_flag = 'f' 
                AND 	 x.league_id = ?
                AND 	 x.parent_division_id  IS NULL ";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,array($season_id,$league_id))->result_array();
		
	}
	/**
	* Get divisions of the given league that have a null parent
	* 
	* @param mixed $league_id
	*/
	public function get_parent_divisions($season_id)
	{
		$sql = "SELECT   x.division_id, x.division_name, x.parent_division_id  , 
				(SELECT COUNT(*) FROM public.league_division s 
				WHERE s.parent_division_id = x.division_id AND s.deleted_flag='f' ) AS sub_count 
				,x.only_teams 
                FROM        public.league_division x 
				INNER JOIN public.season_division sd ON x.division_id=sd.division_id AND sd.season_id=?
                AND    x.deleted_flag = 'f'  AND sd.deleted_flag='f'
                AND 	 x.parent_division_id  IS NULL  
                ORDER BY x.seq";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,$season_id)->result_array();
		
	}
	/**
	* Get all divisions for a given league, regardless of parent id
	* 
	* @param mixed $league_id
	*/
	public function get_leaguedivisions($league_id)
    {
        $sql = "SELECT   x.division_id, x.division_name, x.parent_division_id , x.only_teams
                FROM     public.league_division x 
                WHERE    x.deleted_flag = 'f'  
                AND      x.league_id = ?";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,$league_id)->result_array();
    } 
    /**
    * get the division that the given team has been assigned to
    * in the season
    *  
    * DEFUNCT WITH XREF_NOT IN USE
    * @param int $team_id
    * @param int $season_id
    
    public function get_team_division($team_id,$season_id)
    {
		$sql = "SELECT      d.division_id, d.division_name,d.parent_division_id , d.only_teams
                FROM        public.league_division d 
                INNER JOIN  public.xref_team_season_division xref
                ON          xref.division_id = d.division_id 
                AND xref.team_id = ? 
                AND xref.season_id = ?
                AND d.deleted_flag = 'f' ";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,array($team_id,$season_id))->result_array();
    }*/
    /**
    * get the division that the given team has been assigned to
    * in the season
    * 
    * @param int $team_id
    * @param int $season_id
    */
    public function get_team_division($team_id,$season_id)
    {
		$sql = "SELECT d.division_id, d.division_name, d.only_teams,d.parent_division_id,ts.team_id , sd.season_id  
		
				 FROM public.team_season_division tsd 
				 INNER JOIN public.team_season ts ON ts.id=tsd.team_season_id AND ts.team_id=? AND ts.season_id=? 
				 				AND ts.deleted_flag=FALSE   AND tsd.deleted_flag=FALSE 
				INNER JOIN public.season_division sd ON tsd.season_division_id = sd.id AND sd.deleted_flag=FALSE
				INNER JOIN public.league_division d ON d.division_id = sd.division_id  
				 LIMIT 1";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,array($team_id,$season_id))->result_array();
    }
    
    
    /**
    * Get all divisions for given league that do allow teams to be assigned
    * 
    * @param mixed $league_id
    */
    public function get_pool_divisions($league_id)
    {
		$sql = "SELECT   x.division_id, x.division_name, x.parent_division_id , x.only_teams
                FROM     public.league_division x 
                WHERE    x.deleted_flag = 'f'  
                AND      x.league_id = ? 
                AND 	 x.only_teams = 't' ";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,$league_id)->result_array();
    }
  	
  	/**
  	* get the single division matching the given id
  	* 
  	* @param mixed $division_id
  	*/
  	public function get_division($division_id)
    {
        $sql = "SELECT      x.division_id, x.division_name, x.parent_division_id , x.only_teams
                FROM        public.league_division x                 
                WHERE       x.deleted_flag = 'f'  
                AND         x.division_id = ?";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,$division_id)->result_array();
    } 
    /**
    * gets division info AS well as registration fees saved for this season
    * if any 
    * if not they will be null from left outer
    * 
    * @param mixed $season_id
    * @param mixed $div_id
    */
    public function get_season_division_reg($season_id,$div_id)
    {
		$params=func_get_args();
		$sql="SELECT d.division_id, d.division_name, d.parent_division_id , d.only_teams , r.deposit_amount, r.fees_amount 
		      FROM public.league_division d 
		      INNER JOIN public.season_division sd ON sd.division_id = d.division_id AND sd.season_id=? AND d.division_id=? 
		      		AND sd.deleted_flag=FALSE 
		      LEFT OUTER JOIN public.season_division_registration r ON r.season_division_id = sd.id ";
       return $this->db->query($sql,$params)->result_array();
		      
    }
    public function get_division_name($division_id)
    {
        $sql = "SELECT       x.division_name 
                FROM        public.league_division x                 
                WHERE       x.division_id = ? 
                LIMIT 1";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,$division_id)->first_row()->division_name;
    } 
    /**
  	* get the single division matching the given id 
  	* including team and subdiv count
  	* 
  	* @param int $division_id
  	* @param int $season_id
  	*/
  	public function get_division_tc($division_id,$season_id)
    {
    	/*$sql = "SELECT   x.division_id, x.division_name, x.parent_division_id 
		,(SELECT COUNT(*) FROM public.league_division s 
		WHERE s.parent_division_id = x.division_id AND s.deleted_flag='f') AS sub_count 
			 , x.only_teams , 
			 (SELECT COUNT(*) FROM public.xref_team_season_division xref WHERE 
			 xref.division_id = x.division_id AND xref.season_id = ? ) AS team_count 
                FROM     public.league_division x 
                WHERE    x.deleted_flag = 'f' 
                AND 	 x.parent_division_id = ? ";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,array($season_id,$parent_id))->result_array();
    	*/
        $sql = "SELECT   x.division_id, 
        x.division_name, 
        x.parent_division_id ,
         x.only_teams,
         ? AS season_id 
        			,(SELECT COUNT(*) FROM public.league_division s 
						WHERE s.parent_division_id = x.division_id AND s.deleted_flag='f') AS sub_count 
						
        				, (SELECT COUNT(DISTINCT ts.team_id)  
			 	FROM public.team_season_division  tsd 
			 	INNER JOIN public.season_division sd ON tsd.season_division_id = sd.id  AND tsd.deleted_flag=false
			 						AND sd.division_id = x.division_id AND sd.season_id = ? AND sd.deleted_flag=false
			 	 INNER JOIN public.team_season ts ON ts.id=tsd.team_season_id and ts.deleted_flag=FALSE 
			 	 INNER JOIN public.team t ON t.team_id=ts.team_id AND t.deleted_flag=false  AND t.team_status_id=1  ) 
					AS team_count  
                FROM     public.league_division x 
                WHERE    x.deleted_flag = 'f'  
                AND      x.division_id = ? 
                LIMIT 1      ";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,array($season_id,$season_id,$division_id) )->result_array();
    } 
    public function recursive_pool_subdivisions($div_id,$season_id,&$result)
    {
    	//echo "recursive div_id: ".$div_id." so far we have: ".count($result);    	
		$rows = $this->get_sub_divisions_tc($div_id,$season_id);
		
		foreach($rows as $row)
        {
			if($row['only_teams']=='t')
				$result[]=$row;
			else
				$this->recursive_pool_subdivisions($row['division_id'],$season_id,&$result);

        }
    }   
    
    
    
 	public function get_recursive_subdivisions($div_id,&$result)
    {
    	//echo "recursive div_id: ".$div_id." so far we have: ".count($result);    	
		$rows = $this->get_sub_divisions($div_id);
		$result = array_merge($result,$rows);
		foreach($rows as $row)
        {
			$this->get_recursive_subdivisions($row['division_id'],&$result);
        }
    }   
       
    
     
    
    
    
    public function get_division_recursive_counts($division_id,$season_id)
    {
		$division=$this->get_division_tc($division_id,$season_id);
		$row=$division[0];
        $counts=array();
        $counts['sub_count'] =$row['sub_count'];
        $counts['team_count']=$row['team_count'];
		if($row['only_teams'] == 'f')
		{
			$pools = array();
			$divteam_count=0;		
			$this->recursive_pool_subdivisions($division_id,$season_id,&$pools);	
			foreach($pools as $div)
			{
				$local = (int)$div['team_count'];
				$divteam_count+=$local;	
			}
			$counts['divteam_count']=$divteam_count;				
		}			
		else $counts['divteam_count'] = 0;
		
		$counts['total_teams'] = $counts['divteam_count']+$counts['team_count'];//covers both cases
		
 	
        return $counts;
    }
    
    
    public function get_season_divisions_tc($season_id)
    {

        $sql = "SELECT   x.division_id, x.division_name, x.parent_division_id , x.only_teams
        			,(SELECT COUNT(*) FROM public.league_division s 
						WHERE s.parent_division_id = x.division_id AND s.deleted_flag='f') 
					AS sub_count 
        			, (SELECT COUNT(DISTINCT ts.team_id) FROM public.team_season_division  tsd 
			 				INNER JOIN public.season_division sd ON tsd.season_division_id = sd.id 
						 	AND sd.division_id = x.division_id AND sd.deleted_flag=FALSE
						 
						 INNER JOIN public.team_season ts ON ts.id=tsd.team_division_id 
						  AND sd.season_id = ? AND sd.deleted_flag=FALSE 
						   INNER JOIN public.team t ON t.team_id=ts.team_id AND t.deleted_flag=false  AND t.team_status_id=1  ) 
					AS team_count  
                FROM     public.league_division x 
                WHERE    x.deleted_flag = 'f'  
                      ";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,array($season_id) )->result_array();
    } 
    public function get_season_divisions($season_id)
    {
        $sql = "SELECT   x.division_id, x.division_name, x.parent_division_id , x.only_teams
                FROM     public.league_division x 
                INNER JOIN public.season_division sd ON x.division_id = sd.division_id AND sd.deleted_flag=FALSE AND sd.season_id=?
                AND    x.deleted_flag = FALSE 
                      ";// AND ".USER_CAN_ACCESS." ";        
       return $this->db->query($sql,array($season_id) )->result_array();
    } 
    public function get_season_root_divisions($season_id)
    {
        $sql = "SELECT   x.division_id, x.division_name, x.parent_division_id , x.only_teams, s.season_id
        			,(SELECT COUNT(*) FROM public.league_division s 
						WHERE s.parent_division_id = x.division_id AND s.deleted_flag='f') 
					AS sub_count 
        			, (SELECT COUNT(DISTINCT ts.team_id) FROM public.team_season_division  tsd 
			 				INNER JOIN public.season_division sd ON tsd.season_division_id = sd.id AND sd.deleted_flag=FALSE AND tsd.deleted_flag=FALSE
			 						AND sd.division_id = x.division_id 
			 				INNER JOIN public.team_season   ts ON tsd.team_season_id = ts.id AND ts.deleted_flag=FALSE 
						  			AND sd.season_id =?  
						  				 INNER JOIN public.team t ON t.team_id=ts.team_id AND t.deleted_flag=false  AND t.team_status_id=1  ) 
					AS team_count
                    
                    ,sdr.deposit_amount,fees_amount  
                FROM     public.league_division x 
                INNER JOIN public.season_division s ON s.division_id = x.division_id AND s.season_id=?
                left OUTER join public.season_division_registration sdr   on sdr.season_division_id=s.id
                WHERE    x.deleted_flag = 'f'  AND x.parent_division_id IS NULL 
                      ";// AND ".USER_CAN_ACCESS." ";       
       return $this->db->query($sql,array($season_id,$season_id) )->result_array();
    } 
  	/**
  	* Update division name, and only_teams flag
  	* 
  	* @param int $div_id
  	* @param string $div_name
  	*/
  	public function update_division($div_id,$div_name,$only_teams,$user)
    {
		$params = array($div_id,$div_name,$only_teams,$user);
		$q=$this->db->query("select public.update_division(?,?,?,?)",$params);
        return $q->first_row()->update_division;
    }
    
    
    public function update_division_parent($div_id,$parent_id,$user)
    {
		$params = array($div_id,$parent_id,$user);
		$q=$this->db->query("select public.update_division_parent(?,?,?)",$params);
        return $q->first_row()->update_division_parent;
    }
    
    public function insert_division_season($div_id,$season_id,$user,$owner)
    {
		$params = array($div_id,$season_id,$user,$owner);
		$q=$this->db->query("select public.insert_division_season(?,?,?,?)",$params);
        return $q->first_row()->insert_division_season;
    }
    
    /**
    * update team assignemnt to division
    * 
    * @param mixed $team_id
    * @param mixed $div_id
    * @param mixed $season_id
    */
    public function update_team_div($team_id,$div_id,$season_id,$creator,$owner)   //assign
    {
    	$params=array($team_id,$div_id,$season_id,$creator,$owner);
		$q=$this->db->query("select public.update_team_div(?,?,?,?,?)",$params);
		return $q->first_row()->update_team_div;
    }
    /**
    * delete the assignment within this season
    * 
    * @param mixed $team_id
    * @param mixed $div_id
    * @param mixed $season_id
    */
    public function delete_team_div($team_id,$div_id,$season_id)   //unassign
    {
		$q=$this->db->query("select public.delete_team_div(?,?,?)",array($team_id,$div_id,$season_id));
		return $q->first_row()->delete_team_div;
    }

    /**
    * will not delete if it has subdivisions or teams taht play games
    * needs season id to check both of tehse thigns
    * 
    * @param mixed $division_id
    * @param mixed $season_id
    * @param mixed $user
    */
    public function delete_division($division_id,$season_id,$user)
    {
		$params = func_get_args();
		$q=$this->db->query("select public.delete_division(?,?,?)",$params);
		return $q->first_row()->delete_division;
    }
 
    /**
    * create new division
    * 
    * @param mixed $user
    * @param mixed $org
    * @param mixed $parent_id
    * @param mixed $division_name
    * @param mixed $league_id
    * @param mixed $only_teams
    */
  	public function insert_division($user,$org,$parent_id,$division_name,$league_id,$only_teams)
    {
    	$params = array($user,$org,$parent_id,$division_name,$league_id,$only_teams);
        $q=$this->db->query("select public.insert_division(?,?,?,?,?,?)",$params);
        return $q->first_row()->insert_division;
    }
    
    
    /**
    * get all teams NOT assigned to any division , in the current season
    * 
    * @param int $division_id
    * @param int $season_id
    */
    public function get_unassigned_teams($league_id,$season_id)
    {
    	$params=array($league_id,$season_id);
		$sql="SELECT	  		t.team_name, t.team_id, t.org_id
			  FROM		  		public.team t  
			  INNER JOIN  		public.entity_org eo1           	ON   t.org_id = eo1.org_id 
			  												        AND  t.deleted_flag='f'  AND t.team_status_id=1
			  INNER JOIN  		public.entity_relationship er   	ON   eo1.entity_id = er.child_id
			  INNER JOIN  		public.league l 					ON   l.league_id = ? 
			  INNER JOIN  		public.entity_org eo2 			    ON   l.org_id = eo2.org_id 
			  													    AND  eo2.entity_id = er.parent_id 
			  LEFT OUTER JOIN   public.season s   				    ON   s.season_id = x.season_id 
			  														AND  x.season_id = ?  
			  WHERE 			x.team_id NOT IN (SELECT tsd.team_id FROM public.team_season_division tsd)		    ";
		return $this->db->query($sql,$params)->result_array();
    }
    
    /**
    * Get all teams assigned to the given division and season
    * 
    * @param mixed $season_id
    * @param mixed $div_id
    */
    public function get_season_div_teams($season_id,$div_id)
    {
    	$params=array($season_id,$div_id);
		$sql="SELECT 		t.team_name, t.team_id, t.org_id  ,sd.season_id,sd.division_id
			  FROM 			public.team t 
			  INNER JOIN    public.team_season ts ON ts.team_id = t.team_id AND ts.deleted_flag=FALSE  AND t.team_status_id=1
			  INNER JOIN 	public.team_season_division tsd 	 ON 	tsd.team_season_id = ts.id AND tsd.deleted_flag=FALSE 
			  INNER JOIN    public.season_division sd ON sd.id=tsd.season_division_id AND sd.deleted_flag=FALSE 
			  
			  AND 			sd.season_id = ? 
			  AND 			sd.division_id = ? 
			  AND           t.deleted_flag=FALSE ";
		return $this->db->query($sql,$params)->result_array();
    }
    /**
    * mostly copied from divisions model in gloalwebsites
    * 
    * @param mixed $league_id
    * @return string or array
    */
    public function get_concated_names($season_id,$json=false)
	{
		//$season = $this->season;
		$divisions = $this->get_season_divisions($season_id);
		
		//collect roots
		$roots = array();
		$divs = array();
		foreach($divisions as $div)
		{
			$divs[$div['division_id']] = $div;
			if($div['only_teams']=='t') $roots[] = $div;
		}
		
		//get names
		$names = array();
		foreach($roots as $div) $names[$div['division_id']] = $this->get_div_name($div,$divs);
		foreach($names as $key=>$name) $names[$key] = implode(' :: ',$name);
		if($json)
		{		
			$json=array();
			foreach($names as $id=>$name)
			{
				$d=array();
				$d['division_id'  ]=$id;
				$d['division_name']=$name;
				$json[]=$d;
			}
			return $json;
			
		}
		return $names;

	}
    private function get_div_name($div,$divs,$name=array())
	{
		$id = $div['parent_division_id'];
		if($div['parent_division_id']!=null && isset($divs[$id]))
		{
			$name = $this->get_div_name($divs[$id],$divs,$name);
		}
		$name[] = $div['division_name'];
		return $name;
	}
	public function get_concated_matched($season_id)
	{
		//	$org=$this->permissions_model->get_active_org();
		//$league_id = $this->leagues_model->get_league_from_org($org);
		//$season_id = $this->input->post('season_id');
		$divs=$this->get_concated_names($season_id,true);
		$sep=' vs. ';
		$match=array();
		
		//must avoid doubles/mirrormatch duplicates
		
		$used_pairs=array();
		foreach($divs as $h){
		foreach($divs as $a)
		{
			$m=array();
			$csv=   $h['division_id']."," .$a['division_id'] ;
			$mirror=$a['division_id']."," .$h['division_id'] ;//opposite for test
			if(in_array($csv,$used_pairs) || in_array($mirror,$used_pairs)) { continue; }//already used this match
			
			$m['division_match']  =$h['division_name'].$sep.$a['division_name'];
			
			$m['h_division_name']=$h['division_name'];
			$m['a_division_name']=$a['division_name'];
			$used_pairs[]=$csv;
			//$m['csv_division_ids']= $csv ;
			$m['h_division_id']=$h['division_id'];
			$m['a_division_id']=$a['division_id'];
			$used_pairs[]=$csv;
			$match[]=$m;
		}}
		return $match;
 
	}
    public function update_season_division_custom_rates($season_id,$division_id,$deposit_amount,$fees_amount)
    {
        $result=$this->db->query("select * from public.update_season_division_custom_rates(?,?,?,?)",array($season_id,$division_id,$deposit_amount,$fees_amount))->result_array();
        return $result;
        
    }
    

    /**
    * get all teams for this division, also searches subdivisions
    * WAS IN Schedule controller
    * 
    * @param mixed $div_id
    * @param mixed $season_id
    * @return array
    */
    private function get_subdiv_teams($div_id,$season_id)
    {
		$pools=array();
		$this->recursive_pool_subdivisions($div_id,$season_id,&$pools);
		$teams=array();
		foreach($pools as $pool)
		{
			$new_div_id = $pool['division_id'];
			$teams=array_merge($teams,$this->get_season_div_teams($season_id,$new_div_id));					
		}
		return $teams;		
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
?>
