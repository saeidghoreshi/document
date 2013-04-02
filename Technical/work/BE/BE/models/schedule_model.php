<?php
require_once('./endeavor/models/endeavor_model.php');
class Schedule_model extends Endeavor_model
{
	/**
	* return an integer for the number of schedules on this season
	* 
	* @param mixed $season_id
	*/
	public function count_schedules_in_season($season_id)
	{
		$sql="SELECT COUNT(*) AS sch_count FROM schedule.league_season_schedule lss INNER JOIN schedule.schedule s 
ON lss.schedule_id=s.schedule_id AND lss.season_id = ? AND s.deleted_flag=FALSE ";
		
		
		return $this->db->query($sql,$season_id)->first_row()->sch_count;	
	}
	
	
  	/**
  	* get games and scores for all schedules in this league
  	* 
  	* @param mixed $league_id
  	*/
  	public function get_season_schedule_game_join($league_id)
  	{
  		//AND s.schedule_type_id==1 for Finalized, meaning  not incomplete wizard file, not a tournament
		$sql="SELECT s.schedule_name, s.is_published , s.schedule_id , sn.season_id , lss.league_id , sn.season_name 
				,(SELECT COUNT(*) FROM schedule.game_result gr1 
					INNER JOIN schedule.game  g1 ON g1.game_id = gr1.game_id WHERE  g1.game_id IN (SELECT tg1.game_id FROM schedule.team_game tg1)
					AND g1.schedule_id = s.schedule_id AND gr1.is_valid='t' AND gr1.deleted_flag='f') AS valid_count 
				,(SELECT COUNT(*) FROM schedule.game_result gr2 
					INNER JOIN schedule.game  g2 ON g2.game_id = gr2.game_id WHERE  g2.game_id IN (SELECT tg2.game_id FROM schedule.team_game tg2)
					AND g2.schedule_id = s.schedule_id AND gr2.is_valid='f' AND gr2.deleted_flag='f') AS not_valid_count
				,(SELECT COUNT(*) FROM schedule.game g3 WHERE  g3.game_id IN (SELECT tg3.game_id FROM schedule.team_game tg3)
					AND g3.schedule_id = s.schedule_id AND g3.deleted_flag='f' ) AS total_games 
				,(SELECT COUNT(*) FROM schedule.schedule_timeslot st WHERE st.schedule_id = s.schedule_id ) AS free_timeslots
			FROM schedule.schedule s 
			INNER JOIN schedule.league_season_schedule lss ON lss.schedule_id=s.schedule_id AND s.deleted_flag='f' AND lss.league_id=? 
						AND s.schedule_type_id = 1
			INNER JOIN public.season sn ON sn.season_id = lss.season_id 	";	
		
        return $this->db->query($sql,$league_id)->result_array();			
  	}
  	/**
  	*  
  	* 
  	* @param mixed $league_id
  	* @param mixed $season_id
  	*/
    public function get_schedulebyseason($league_id,$season_id)
    {
    	$params =array($league_id,$season_id);
        $sql = "SELECT      x.schedule_name, x.schedule_id, lss.season_id, lss.league_id  
                FROM        schedule.schedule x 
                INNER JOIN  schedule.league_season_schedule lss     ON          x.schedule_id = lss.schedule_id 
                AND         x.deleted_flag = 'f' 
                AND         lss.league_id = ?  
                AND         lss.season_id = ?  ";//  AND         ".USER_CAN_ACCESS." ";        
        return $this->db->query($sql,$params)->result_array();				
    }
    
    
    /**
    *  
    * 
    * @param mixed $league
    */
    public function get_leagueschedules($league)
    {//
        $sql = "SELECT      x.schedule_name, x.schedule_id, lss.season_id, lss.league_id  
                FROM        schedule.schedule x 
                INNER JOIN  schedule.league_season_schedule lss 
                ON          x.schedule_id = lss.schedule_id 
                WHERE       x.deleted_flag = 'f' 
                AND         lss.league_id = ?   ";//  AND         ".USER_CAN_ACCESS." ";
        
        return $this->db->query($sql,$league)->result_array();
    }
    

 
	
    
    /**
    * insert into xref table
    * 
    * @param mixed $leagueid
    * @param mixed $seasonid
    * @param mixed $scheduleid
    */
    public function insert_lss($leagueid,$seasonid,$scheduleid)
    {
        $params = array($leagueid,$seasonid,$scheduleid);
        $sql = 'SELECT schedule.insert_lss(?,?,?)';
        $query= $this->db->query($sql,$params);
        $result=$query->first_row();
        $id = (int)$result->insert_lss;
        return $id;        
    }
    
	/**
	* create new schedule with no games yet
	* type 1 == finalized
	* type 2==toyurnament
	* type 3 = wizard safe file_incomplete
	* 
	* @param mixed $name
	* @param mixed $user
	* @param mixed $owner
	*/
    public function insert_schedule($name,$user,$owner,$is_published,$lu_type=1)
    {
    	$details = array($name,$user,$owner,$is_published,$lu_type);
        $sql = 'SELECT schedule.insert_schedule(?,?,?,?,?)';
        return $this->db->query($sql,$details)->first_row()->insert_schedule;
    }

    
   
    
    
    /**
    * A timeslot for a schedule is a slot that was not assigned a game upon creation.  
    * Any contracted times that are not used by the wizard are stored here.  
    * When a game is re-scheduled, its old timeslot will be placed here, it will possibly 
    * be assigned one of these (hence it will be deleted).  A timeslot should never be in this table 
    * if it is being used by a game.  all parameters are NON NULL 
    * 
    * @param mixed $sid
    * @param mixed $date
    * @param mixed $stime
    * @param mixed $etime
    * @param mixed $vid
    * @return mixed
    */
    public function insert_timeslot($sid,$date,$stime,$etime,$vid)
    {//assume the order of $details is as follows:
		$params=array($sid,$date,$stime,$etime,$vid);
        $sql = 'SELECT schedule.insert_timeslot(?,?,?,?,?)';
        $query = $this->db->query($sql,$params);
        $result= $query->first_row();
        return  (int)$result->insert_timeslot;        
    }
    /**
    * Get timeslots for given schedule (which by definition do not have a game)
    * 
    * @param mixed $sid
    */
    
    
    
    
    public function get_timeslots($sid)
    {
		$sql = "SELECT 	t.timeslot_id, t.game_date, t.start_time, t.end_time, t.venue_id , v.venue_name, 
							v.facility_id 
				FROM 	schedule.schedule_timeslot t 
				INNER JOIN public.venue v ON t.venue_id = v.venue_id 
				WHERE 	schedule_id = ?";
		return $this->db->query($sql,$sid)->result_array();
    }
    
    /**
    * get single timeslot by id
    * 
    * @param mixed $tid
    */
    public function get_timeslot($tid)
    {
		$sql = "SELECT 	t.timeslot_id, t.game_date, t.start_time, t.end_time, t.venue_id , v.venue_name, 
							v.facility_id 
				FROM 	schedule.schedule_timeslot t 
				INNER JOIN public.venue v ON t.venue_id = v.venue_id 
				WHERE 	timeslot_id = ?";
		return $this->db->query($sql,$tid)->result_array();
    }
 
    
    
 
    
    
    
    /**
    * put your comment there...
    * 
    * @param mixed $leagues
    * @param string $data
    * @param mixed $id
    */
    public function update_publish_schedule($schedule_id,$is_published)
    {
    	$details = array($schedule_id,$is_published);
        $sql = 'SELECT schedule.update_publish_schedule(?,?)';
        $query = $this->db->query($sql,$details);
        return $query->first_row()->update_publish_schedule;
	}
	/**
	* where status is from lu_schedule_type
	* 
	* @param mixed $schedule_id
	* @param mixed $status
	*/
	public function update_schedule_type($schedule_id,$status)
	{
		$details = array($schedule_id,$status);
        $sql = 'SELECT schedule.update_schedule_type(?,?)';
        $query = $this->db->query($sql,$details);
        return $query->first_row()->update_schedule_type;
	}
    public function update_schedule_name($schedule_id,$name)
	{
		$details = array($schedule_id,$name);
        $sql = 'SELECT schedule.update_schedule_name(?,?)';
        $query = $this->db->query($sql,$details);
        return $query->first_row()->update_schedule_name;
	}
	
	public function delete_schedule($schedule_id,$user)
	{
		$details = array($schedule_id,$user);
        $sql = 'SELECT schedule.delete_schedule(?,?)';
        $query = $this->db->query($sql,$details);
        return $query->first_row()->delete_schedule;
	}
    public function get_league_info($leagueid)
    {

        $sql = "SELECT       x.league_id,  
        				     db_server, db_port, db_username, db_password, db_dbname 
                FROM         public.league x 
                WHERE        x.league_id = ? ";
        $result = $this->db->query($sql,$leagueid);
       // var_dump($this->db->last_query());
        return $result->result_array();
    }
 
	
	
    /**
    * for all games in a given schedule
    * 
    * @param mixed $schedule_id
    */
    public function get_result_sch_sumbissions($schedule_id)
    {
    	
		$sql = "SELECT 		r.game_result_id, r.game_id, r.home_score, r.away_score 
		 		FROM 		schedule.game_result r  
		 		INNER JOIN  schedule.game g              ON g.game_id = r.game_id   
		 		WHERE 		g.schedule_id = ? ";
		 		
        return $this->db->query($sql,$schedule_id)->result_array();   
		
    }
    
    
    public function get_reschedule_requests($game_id)
    {
		$sql = "SELECT 		r.request_id, r.request_date, r.request_context, r.game_id, r.org_id 
		 		FROM 		schedule.reschedule_request r  
		 		WHERE 		g.game_id = ? ";
		 		
        return $this->db->query($sql,$game_id)->result_array();   
		
    }
    
    public function get_sch_reschedule_requests($schedule_id)
    {
		$sql = "SELECT 		r.request_id, r.request_date, r.request_context, r.game_id, r.org_id ,r.desirable_datetime,g.schedule_id
		 		FROM 		schedule.reschedule_request r  
		 		INNER JOIN  schedule.game g              ON g.game_id = r.game_id   
		 		AND 		g.schedule_id = ? ";
		 		
		 		
		 		
        return $this->db->query($sql,$schedule_id)->result_array();
    }
    
    
    public function delete_reschedule_request($req_id)
	{
		$params = array($req_id);
        $sql="SELECT schedule.delete_reschedule_request(?)";
        $query= $this->db->query($sql,$params);
        $result=$query->first_row();
        return $result->delete_reschedule_request;
	}
    
 

    
    
    public function s_update_current_season($id,$s,$e)
    {
    	if(!$id) return false;
		$fmt='Y/m/d';
		$_SESSION['season_id']=$id;
		$_SESSION['season_start']=date($fmt,strtotime($s));
		$_SESSION['season_end']=date($fmt,strtotime($e));
		return true;
    }
    public function s_get_current_season()
    {
		
		if(!isset($_SESSION['season_id']))//null should mean false
			return array();
		
		return array('season_id'  =>$_SESSION['season_id'],
					'season_start'=>$_SESSION['season_start'],
					'season_end'  =>$_SESSION['season_end']
					);		
		
    }
    
    public function s_get_schedule_id()
    {
		return $_SESSION['schedule_id'];
    }
    public function s_set_schedule_id($schedule_id)
    {
		$_SESSION['schedule_id'] = $schedule_id;
    }
    
    /**
    * if it exists already, DO NOToverwrite
    * 
    * @param mixed $set_name
	* @return bool
    */
    public function s_insert_dateset($set_name,$start,$end,$hexcol='FF0000')
    {
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			$_SESSION['scheduleDateSets']=array();
				
		if(!isset($_SESSION['dateset_pk_seq']))
			$_SESSION['dateset_pk_seq']=1;
			
		$new_pk=$_SESSION['dateset_pk_seq'];
		$_SESSION['dateset_pk_seq']++;	
		$_SESSION['scheduleDateSets'][$new_pk]=array();
		$_SESSION['scheduleDateSets'][$new_pk]['date_array']=array();
		$_SESSION['scheduleDateSets'][$new_pk]['venue_array']=array();
		$_SESSION['scheduleDateSets'][$new_pk]['rules_array']=array();
		$_SESSION['scheduleDateSets'][$new_pk]['rules_array']['is_active']='f';//false because its empty
		$_SESSION['scheduleDateSets'][$new_pk]['dateset_pk']=$new_pk;
		//$_SESSION['scheduleDateSets'][$new_pk]['timeslot_count']=0;
		$_SESSION['scheduleDateSets'][$new_pk]['set_name']=$set_name;
		$_SESSION['scheduleDateSets'][$new_pk]['start_time']=$start;
		$_SESSION['scheduleDateSets'][$new_pk]['end_time']=$end;
		$_SESSION['scheduleDateSets'][$new_pk]['hexcol']=$hexcol;
		return $new_pk;
    }
    
    
    /**
    * change the name of the set
    * 
    * @param int $dateset_pk
    * @param varchar $set_name
    */
    public function s_update_dateset($dateset_pk,$set_name,$start,$end,$hexcol)
    {
		if(!array_key_exists('scheduleDateSets',$_SESSION)) return false;
		if(!isset($_SESSION['scheduleDateSets'][$dateset_pk])) return false;
		
		$_SESSION['scheduleDateSets'][$dateset_pk]['set_name']=$set_name;
		$_SESSION['scheduleDateSets'][$dateset_pk]['start_time']=$start;
		$_SESSION['scheduleDateSets'][$dateset_pk]['end_time']=$end;
		if($hexcol)
			$_SESSION['scheduleDateSets'][$dateset_pk]['hexcol']=$hexcol;
		//else echo'no hex given';
		return true;
		
    }
    
	 
    
    
    
    /**
    * put your comment there...
    * 
    * @param int $dateset_pk
	* @return bool
    */
    public function s_delete_dateset($dateset_pk)
    {
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			return false;
		else
			unset($_SESSION['scheduleDateSets'][$dateset_pk]);
		return true;
    }
    /**
    * keep pk inexing
	* @return array
    */
    public function s_get_datesets_indexed()
	{
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			return array();
			
		 
 
		return $_SESSION['scheduleDateSets'];//return as  indexed by pk, 
	}
    /**
    * get all
    * flatten and remove pk indexing
	* @return array
    */
    public function s_get_datesets()
	{
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			return array();
			
		
		$result=$_SESSION['scheduleDateSets'];
		$return=array();
		foreach($result as $record)
		{
			$record['date_count'] =count($record['date_array']);
			$record['venue_count']=count($record['venue_array']);
			$return[]=$record;
		}
		
		
		return $return;//return as NOT indexed by pk, but as flat record set
	}
	
	
	
	
	/**
	* moved here from schedule  controller
	* used by sch wizard. it process datesets and udpates them with info from database, and other computations
	* 
	*/
	public function get_dateset_summary()
	{
		 
		$date_sets = $this->s_get_datesets();
		$rows = array();
		$found_venues=array();
		$this->load->model('facilities_model');
		foreach($date_sets as $set)
		{ 
			if($set['venue_count']==0 || $set['date_count']==0) continue;
			
			$set_name   = $set['set_name'];
			$dateset_pk = $set['dateset_pk'];
			//echo "current set".$set_name."\n";
			//library is already loaded by controller
			$s 			 = $set['start_time'];//$this->scheduler->timeMerToAstro( $set['start_time'] ); 
			$e 			 = $set['end_time'];//$this->scheduler->timeMerToAstro($set['end_time']); 
			//echo "current slot_name is:".$slot_name."\n";
			 
			//echo "proccess ts=".$ts."\n";
			$venues = $this->s_get_dateset_venues($dateset_pk);
			$dates  = $this->s_get_dateset_dates( $dateset_pk);
			
			foreach($dates as $date)
			{	 foreach($venues as $venue_id)
				{					
 					//find data for this venue if not found in prv loop
					if(!isset($found_venues[$venue_id]))
 				   		$found_venues[$venue_id] =$this->facilities_model->get_venue($venue_id);
 				   		 
					
					$rows[] = array( 
									'venue_id'   => $venue_id , 
									'facility_id'   => $found_venues[$venue_id]['facility_id'] , 
									'facility_name' => $found_venues[$venue_id]['facility_name'] , 
									 'venue_name'   => $found_venues[$venue_id]['venue_name'] ,									 
									 'venue_longitude'   => $found_venues[$venue_id]['venue_longitude'] ,									 
									 'venue_latitude'   => $found_venues[$venue_id]['venue_latitude'] ,									 
									// 'date'	 	  => $date,									 
									 //'sortdate'   => date('Ymd',strtotime($date)),
									 'game_date'  => $date,// 
									 'start_time' => $s,
									 'end_time'   => $e,
 
									 'set_name'   => $set_name,
									 //'slot_name'  => $slot_name,
									// 'timeslot_pk'=>$timeslot_pk,
									 'dateset_pk' =>$dateset_pk,
									);					
				}
			}
					
		}
 
		unset($found_venues);
 		//done pre processing it
		return $rows;
	}
 
 

	public function s_insert_dateset_dates($dateset_pk,$date_array)
	{
		//if it doesnt exist:
		//$ts=$start."-".$end;
		//echo "s_insert_dateset_dates was given ".$set_name."    ".$slot_name."\n";////SLOT NAME UNDEFINED ERROR
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			return false;
		if(!array_key_exists($dateset_pk,$_SESSION['scheduleDateSets']))
			return false;
 
			
		$_SESSION['scheduleDateSets'][$dateset_pk]['date_array']=$date_array;
		
		
		
		//$_SESSION['scheduleDateSets'][$set_name][  'timeslot_array'][$timeslot_pk]['date_count']=count($date_array);
		return true;
	}

	
	public function s_get_dateset_dates($dateset_pk)
	{
		//if it doesnt exist:
		//$ts=$start."-".$end;
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			return array();
		if(!array_key_exists($dateset_pk,$_SESSION['scheduleDateSets']))
			return array();

		return $_SESSION['scheduleDateSets'][$dateset_pk]['date_array'];
		
	}
	
	/**
	* all rules attached to a single date set
	* 
	* @param mixed $dateset_pk
	* @param mixed $w
	* @param mixed $t
	* @param mixed $active
	* @param mixed $bmax
	* @param mixed $bmin
	* @param mixed $min_slot
	* @param mixed $max_slot
	* @param mixed $min_day
	* @param mixed $max_day
	*/
	public function s_insert_ds_rules($dateset_pk,$w,$t,$active,$bmax="0:00",$bmin="0:00",$min_slot=0,$max_slot=0,$min_day=0,$max_day=0)
	{
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			return false;
		if(!array_key_exists($dateset_pk,$_SESSION['scheduleDateSets']))
			return false;
		if(!isset($_SESSION['scheduleDateSets'][$dateset_pk]['rules_array']))
			$_SESSION['scheduleDateSets'][$dateset_pk]['rules_array']=array();
			
		
		$_SESSION['scheduleDateSets'][$dateset_pk]['rules_array']['is_active']   = $active;
		$_SESSION['scheduleDateSets'][$dateset_pk]['rules_array']['ds_warmup']   = $w;
		$_SESSION['scheduleDateSets'][$dateset_pk]['rules_array']['ds_teardown'] = $t;
		$_SESSION['scheduleDateSets'][$dateset_pk]['rules_array']['ds_max_btw']  = $bmax;
		$_SESSION['scheduleDateSets'][$dateset_pk]['rules_array']['ds_min_btw']  = $bmin;
		
		$_SESSION['scheduleDateSets'][$dateset_pk]['rules_array']['min_slot'] = $min_slot;
		$_SESSION['scheduleDateSets'][$dateset_pk]['rules_array']['max_slot'] = $max_slot;
		$_SESSION['scheduleDateSets'][$dateset_pk]['rules_array']['min_day']  = $min_day;
		$_SESSION['scheduleDateSets'][$dateset_pk]['rules_array']['max_day']  = $max_day;
		
		
		return true;
		
		//$_SESSION['scheduleDateSets'][$new_pk]['rules_array']=array();
	}
	/**
	* takes every 'rules_array' index from each dateset 
	* and flattens them together into one ds rules array
	* 
	*/
	public function s_get_ds_rules_arrays()
	{
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			return array();
 
		$rules=$_SESSION['scheduleDateSets'];
		$keyed_rules=array();
		foreach($rules as $dateset_pk=>$all_rules)
		{
			$keyed_rules[$dateset_pk]=$all_rules['rules_array'];
		}
		return $keyed_rules;
	}
	public function s_get_ds_rules($dateset_pk)
	{
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			return array();
		if(!array_key_exists($dateset_pk,$_SESSION['scheduleDateSets']))
			return array();

		return $_SESSION['scheduleDateSets'][$dateset_pk]['rules_array'];
		
	}
 
	
	/**
	* get all dates used OUTSIDE given dateset  
	*   (over all timeslots in those sets )
	* 	-batched together in a flat array
	* 
	* @param str $set_name
	* @return array
	*/
	
	public function s_get_ds_dates_used_external($dateset_pk)
	{
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			return array();

		$result=array();
		foreach($_SESSION['scheduleDateSets'] as $ds_pk=>$record)
		{		
			if($ds_pk != $dateset_pk)//skip
			{

				$result= array_merge($result,$record['date_array']);	
			}
		}
		return $result;
	}
	
	/**
	* add new venues, no doubles, no overwrite
	* 
	* @param str $timeslot_pk
	* @param array $venue_array
	* @param bool $is_random
	*/
	public function s_insert_dateset_venues($dateset_pk,$venue_array,$is_random=false)
	{
		//if it doesnt exist:
		//$ts=$start."-".$end;
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			return false;
		if(!array_key_exists($dateset_pk,$_SESSION['scheduleDateSets']))
			return false;
			
		//$_SESSION['scheduleDateSets'][$dateset_pk]['venue_array'] =$venue_array;
		$saved=$_SESSION['scheduleDateSets'][$dateset_pk]['venue_array'];
		foreach($venue_array as $venue_id)
		{
			if(!in_array($venue_id,$saved))
			{
				$saved[]=$venue_id;
			}
			//else already saved it
		}
		
		$_SESSION['scheduleDateSets'][$dateset_pk]['venue_array']=$saved;
		
 
		//$_SESSION['scheduleDateSets'][$dateset_pk]['timeslot_array'][$timeslot_pk]['venue_random']=$is_random;
		return true;
	}
	public function s_delete_dateset_venues($dateset_pk,$venue_array)
	{
		//if it doesnt exist:
		//$ts=$start."-".$end;
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			return false;
		if(!array_key_exists($dateset_pk,$_SESSION['scheduleDateSets']))
			return false;
			
		//$_SESSION['scheduleDateSets'][$dateset_pk]['venue_array'] =$venue_array;
		$saved=$_SESSION['scheduleDateSets'][$dateset_pk]['venue_array'];
		$keep=array();
		foreach($saved as $venue_id)
		{
			//i like this method better than unset / array_values combo
			if(!in_array($venue_id,$venue_array))
			{//we were not told to delete this one, so it can stay
				$keep[]=$venue_id;
			}
			//else do not keep it
		}
		
		$_SESSION['scheduleDateSets'][$dateset_pk]['venue_array']=$keep;
		
 
		return true;
	}
	/**
	* getall
	* 
	* @param mixed $set_name
	* @param mixed $start
	* @param mixed $end
	* @param mixed $date_array
	* @return array
	*/
	public function s_get_dateset_venues($dateset_pk)
	{
		//if it doesnt exist:
		//$ts=$start."-".$end;
		if(!array_key_exists('scheduleDateSets',$_SESSION))
			return array();
		if(!array_key_exists($dateset_pk,$_SESSION['scheduleDateSets']))
			return array();/*
		if(!array_key_exists($timeslot_pk,$_SESSION['scheduleDateSets'][$dateset_pk]['venue_array']))
			return array();*/
			
			
		return $_SESSION['scheduleDateSets'][$dateset_pk]['venue_array'];
		
	}
	
	
	/**
	* overwrites it
	* has been calculated by matching dates/venues/etc
	* 
	* @param mixed $summary
	*/
	public function s_save_ds_summary($summary)
	{
		$_SESSION['scheduleDateSummary']=$summary;
		return $_SESSION['scheduleDateSummary'];
	}
	
	
	/**
	* put your comment there...
	* 
	*/
	public function s_get_ds_summary()
	{
		if(!array_key_exists('scheduleDateSummary',$_SESSION))
			return array();
		else return $_SESSION['scheduleDateSummary'];
			
		
	}
	/**
	* filter
	* 
	* @param mixed $summary
	* @param mixed $venue_id
	* @return mixed
	*/
	public function s_get_ds_summary_by_venue($venue_id)
	{
		if(!array_key_exists('scheduleDateSummary',$_SESSION))
			return array();

		$table = $_SESSION['scheduleDateSummary'];//['summary'];
		$filtered=array();
		
		foreach($table as $row)
		{
			if($row['venue_id'] == $venue_id)
				$filtered[]=$row;	
		}
		return $filtered;
	}
	
	/**
	* valid date is YYYYMMDD string, not fancy
	* 
	* @param mixed $summary
	* @param mixed $date
	* @return mixed
	*/
	public function s_get_ds_summary_by_date($date)
	{
		if(!array_key_exists('scheduleDateSummary',$_SESSION))
			return array();
			
		$table = $_SESSION['scheduleDateSummary'];
		$filtered=array();

		foreach($table as $row)
		{
			if($row['sortdate'] == $date)
				$filtered[]=$row;

		}	
		return $filtered;	
	}
	
	
	/**
	* 
	* 
	* @param array $rules_array
	* @param str $set_name
	*/

 
	/**
	* DEFAULT rules array,
	* 
	* @param array $default
	*/
	public function s_update_global_default($default)
	{
		if(!array_key_exists('s_global_rules',$_SESSION))
			$_SESSION['s_global_rules']=array();
		$_SESSION['s_global_rules']['default']=$default;
		return true;
	}

	/**
	* 
	* 
	* @param mixed $min
	* @param mixed $max
	* @param mixed $length
	* @param mixed $w
	* @param mixed $t
	* @param mixed $maxb
	* @param mixed $minb
	*/
	public function s_update_global_rules($min,$max,$length,$w,$t,$maxb,$minb,$venue_distance,$facility_lock)
	{
		if(!array_key_exists('s_global_rules',$_SESSION)) $_SESSION['s_global_rules']=array();
			
		$_SESSION['s_global_rules']['global']['min']=$min;
		$_SESSION['s_global_rules']['global']['max']=$max;
		$_SESSION['s_global_rules']['global']['len']=$length;
		$_SESSION['s_global_rules']['global']['warmup']=$w;
		$_SESSION['s_global_rules']['global']['teardown']=$t;
		$_SESSION['s_global_rules']['global']['max_btw']=$maxb;
		$_SESSION['s_global_rules']['global']['min_btw']=$minb;
		$_SESSION['s_global_rules']['global']['venue_distance']=$venue_distance;
		$_SESSION['s_global_rules']['global']['facility_lock']=$facility_lock;
		
		
		//$_SESSION['s_global_rules']['default']=$default;//in case there is a dateset with no rules saved, use this
		return true;
		
	}
	public function s_get_global_rules()
	{
		if(!array_key_exists('s_global_rules',$_SESSION))
			$_SESSION['s_global_rules']=array();
		if(!array_key_exists('global',$_SESSION['s_global_rules']))
		{
			$_SESSION['s_global_rules']['global']=array();//if nothing then just make defaults
			//if doesnt exist, then just set a default
			$_SESSION['s_global_rules']['global']['min']=0;
			$_SESSION['s_global_rules']['global']['warmup']=0;
			$_SESSION['s_global_rules']['global']['teardown']=0;
			$_SESSION['s_global_rules']['global']['max_btw']=0;
			$_SESSION['s_global_rules']['global']['min_btw']=0;
			$_SESSION['s_global_rules']['global']['max']='';
			$_SESSION['s_global_rules']['global']['len']='1:00';
			$_SESSION['s_global_rules']['global']['venue_distance']=500;
			$_SESSION['s_global_rules']['global']['facility_lock']=false;
		}
		return $_SESSION['s_global_rules']['global'];
	}
	
	
	public function s_get_global_default_rules()
	{
		if(!array_key_exists('s_global_rules',$_SESSION))
			return false;
		if(!array_key_exists('default',$_SESSION['s_global_rules']))
			return false;
		return $_SESSION['s_global_rules']['default'];
		
	}
	
	
	public function s_update_match_data($match_pk,$er,$ed,$mr)
 	{
 		if(!isset($_SESSION['scheduleMatches']))////is set returns false if set to nul
			$_SESSION['scheduleMatches']=array();
		foreach($_SESSION['scheduleMatches'] as &$saved)
		{
			if($saved['match_pk']==$match_pk)
			{
				//$return_pk = $row['match_pk'];
				$saved['match_rounds']=$mr;
				//TODO calculate match_games
				$saved['enforce_rounds']=$er;
				$saved['enforce_dates']=$ed;
				//$saved['first_div_id']=$first;
				//$saved['second_div_id']=$second;
				return $match_pk;
			}
			
		}
		return false;
 	}
 	
 
	public function s_insert_match($f_id,$s_id,$er='f',$ed='f',$mr=0,$dates=null)
	{
		if(!isset($_SESSION['match_pk_seq']))//cannot be null
			$_SESSION['match_pk_seq']=1;
		if(!isset($_SESSION['scheduleMatches']))////is set returns false if set to nul
			$_SESSION['scheduleMatches']=array();
			
		if($dates==null){$dates=array();};
		$pk = $_SESSION['match_pk_seq'];
		$_SESSION['match_pk_seq']++;
		$row=array( //'first_div_name'   => $f_name,
					//'second_div_name' => $s_name,
					'first_div_id'    => $f_id,
					'second_div_id'   => $s_id,
					//'first_div_teams' => $f_teams,
					//'second_div_teams'=> $s_teams,
					//'match_games'     => $mg,
					'match_rounds'    => $mr,
					'enforce_rounds'  => $er,
					'enforce_dates'   => $ed,
					'match_pk'        => $pk ,
					'date_array'      => $dates );
		$_SESSION['scheduleMatches'][]=$row;
		return true;//done
		
	}
	
	 
	
	
	
	public function s_delete_match($match_pk)
	{
		if(!$match_pk||$match_pk == -1) return true;//doesnt exist win!
		if(!array_key_exists('scheduleMatches',$_SESSION))
			return false;

		foreach($_SESSION['scheduleMatches'] as $i=>$row)
		{
			if( $row['match_pk']==$match_pk)//found it
			{
				unset($_SESSION['scheduleMatches'][$i]);
				$_SESSION['scheduleMatches'] = array_values($_SESSION['scheduleMatches']);//reset indexes
				return true;
			}
		}
		return false;
	}
	/**
	* get all matches. for all pks
	* also update count
	* @return array
	*/
	public function s_get_match_set()
	{
		if(!isset($_SESSION['scheduleMatches']))
			return array();
		foreach($_SESSION['scheduleMatches'] as &$row)
		{
			$row['date_count']=count($row['date_array']);
		}
		return $_SESSION['scheduleMatches'];
		
	}
	/**
	* get array of 'division_id's without duplicates
	* taht rae used in matches
	* 
	*/
	public function s_get_unique_match_divids()
	{
		$matches=$this->s_get_match_set();
		$return=array();
		$used=array();
		foreach($matches as $m)		
		{
			$fid = $m['first_div_id'];
			$sid = $m['second_div_id'];
			if(!isset($used[$fid])) 
			{
				$used[$fid]=true;
				$return[]=$fid;
			}
			if(!isset($used[$sid])) 
			{
				$used[$sid]=true;
				$return[]=$sid;
			}
		}
		return $return;
	}
	/**
	* @author sam
	* input: flat array of division_ids
	* 
	* output: array indexed by div id, eahc of which is array of team ids
	* 
	* @param mixed $divs
	*/
	public function team_list_for_div_array($divs,$season_id)
	{
		$this->load->model('divisions_model');
		$return=array();
		foreach($divs as $division_id)
		{			
	        $result=$this->divisions_model->get_division($division_id);
	       // 
	        if($result[0]['only_teams'] == 't')
	        {
				$return[$division_id]=$this->divisions_model->get_season_div_teams($season_id,$division_id);
	        }
	        else
	        {
				$return[$division_id]=$this->divisions_model->get_subdiv_teams($division_id,$season_id);	
	        }
	        
	       //$return[$division_id]['division_name']= 
		}
		return $return;
		
	}
	
	
	
	/**
	* re calcualate number of teams per division, div name, games per round, and est games
	* for all matches saved in session, and also update them
	* to be called every time we display them, and once pre schedule gen
	* @access private
	* @author sam basestt
	* 
	*/
	public function _recalc_match_stats()
	{
		$this->load->model('divisions_model');
		$matches = $this->s_get_match_set();
		 
		$season = $this->s_get_current_season();
		if(!isset($season['season_id'])){   return;}//if nothing is there
		$season_id = $season['season_id'];
		
		foreach($matches as &$m)
		{
			$fid = $m['first_div_id'];
			//$m['first_div_name']=$sorted[$fid]['division_name'];
			$m['first_div_name']=$this->divisions_model->get_division_extended_name($fid);
			
			$counts=$this->divisions_model->get_division_recursive_counts($fid,$season_id);
			//$counts['total_teams'] = $counts['divteam_count']+$counts['team_count'];//covers both cases
			//sub_count
 
			$m['f_total_teams']=$counts['total_teams'] ;
			$m['f_divteam_count']=$counts['divteam_count'];
			$m['f_team_count']=$counts['team_count'];
			$m['f_sub_count']=$counts['sub_count'];
			
			$sid = $m['second_div_id'];
			//$m['second_div_name']=$sorted[$sid]['division_name'];
			$m['second_div_name']=$this->divisions_model->get_division_extended_name($sid);
			$counts=$this->divisions_model->get_division_recursive_counts($sid,$season_id);
			$m['s_total_teams']=$counts['total_teams'] ;
			$m['s_divteam_count']=$counts['divteam_count'];
			$m['s_team_count']=$counts['team_count'];
			$m['s_sub_count']=$counts['sub_count'];

			
			$m['date_count']=count($m['date_array']);
			if($m['enforce_rounds'] == 't' || $m['enforce_rounds']=='true')
			{
				
				// if same division, team will not play tiself so one less to acount for in second set
				//either size of complete graph, or size of complete bipartite graph
				if($m['first_div_id']==$m['second_div_id'])
				{
					$m['games_per_round'] =  $m['s_total_teams'] * ($m['f_total_teams']-1) /2;
					
				}
 				else
 				{
 					//different divs
					$m['games_per_round'] = $m['s_total_teams'] * $m['f_total_teams'];
 				}
				 
				  
				$m['est_games'] =  $m['games_per_round'] *$m['match_rounds'] ;
			}
			else
			{
				//not enforced so unlim
				$m['est_games'] = "&#8734; ";//infinity
				// for other symbols see http://www.alanwood.net/demos/symbol.html
			}
		}
		$this->s_overwrite_matches($matches);//update it to include new data
	}
	
	/**
	* replace magches with given matches
	* 
	* @param mixed $matches
	*/
	public function s_overwrite_matches($matches)
	{
		$_SESSION['scheduleMatches']=$matches;
	}
	/**
	* get dates for this match
	* 
	* @param mixed $match_pk
	* @return mixed
	*/
	public function s_get_match_dates($match_pk)
	{
		if(!isset($_SESSION['scheduleMatches']) || !is_array($_SESSION['scheduleMatches'])) {return array();}
		foreach($_SESSION['scheduleMatches'] as &$row)
		{
			if($row['match_pk']==$match_pk)
			{
				return $row['date_array'];
			}
		}
		return array();
	}
	public function s_get_match_dates_stamps($match_pk)
	{
		if(!isset($_SESSION['scheduleMatches']) || !is_array($_SESSION['scheduleMatches'])) {return array();}
		$row = $this->s_get_match_by_pk($match_pk);
		return isset($row['date_stamps']) ?$row['date_stamps']:array();
 
	}
	public function s_save_match_dates($match_pk,$dates)
	{
		if(!isset($_SESSION['scheduleMatches']) || !is_array($_SESSION['scheduleMatches'])) {return false;}
		
		foreach($_SESSION['scheduleMatches'] as &$row)
		{
			//on save, preprocess into timestamps
			$stamps=array();
			foreach($dates as $d)
			{
				$s = strtotime($d);
				$stamps[$s]=$s;//self indexign to avoid in array and void loops
			}
			if($row['match_pk']==$match_pk)
			{
				$row['date_array']=$dates;
				$row['date_stamps']=$stamps;
				
				return true;
			}
		}
		return false;
	}
	/**
	* schedule has been audited, this is associatve array of audit tables
	* save them all
	* 
	* @param mixed $audits
	*/
	public function s_save_audits($audits)
	{
		foreach($audits as $type=>$a)
	    {
			$_SESSION[$type]=$a;
	    }
	    
	}
	/**
	* get a single division match by id
	* 
	* @param mixed $match_pk
	*/
	public function s_get_match_by_pk($match_pk)
	{
		if(!isset($_SESSION['scheduleMatches']))
			return false;

		
		foreach( $_SESSION['scheduleMatches'] as $row)
		{

			if($row['match_pk'] == $match_pk)
				return $row;			
		}
		return false;
	}
	
	
	
	/**
	* filter by div
	* 
	* @param int $div_id
	* @return array
	*/
	public function s_get_match_filter_division($div_id)
	{
		if(!isset($_SESSION['scheduleMatches']))
			return array();

		
		
		
		$matches= $_SESSION['scheduleMatches'];
		$result = array();
		foreach($matches as $match)
		{
			if($match['first_div_id'] == $div_id || $match['second_div_id'] == $div_id)
				$result[]=$match;
			
		}
		return $result;
	}
	
	/**
	* is random
	* @return boolean
	* @deprecated
	*/
	public function s_get_match_random()
	{
		if(!array_key_exists('scheduleMatches',$_SESSION))
			return false;
		
		if(!array_key_exists('is_random',$_SESSION['scheduleMatches']))
			return $_SESSION['scheduleMatches']['is_random'];
		
		return false;
		
	}
	
	 
	public function sort_divisions_format($divs=null)
	{
		if($divs==null) $divs=$this->s_get_divisions();
		
		//echo "these are what we have saved::";
		//var_dump($divs);
		$formatted=$_SESSION['sch_div_id_to_name'];
		
		//$used_ids=array();
		//var_dump($divs);
		/*
		foreach($divs as $div_id=>$used)
			if($used)
				$used_ids[]=$div_id;
				*/
		$return=array();
		//var_dump($formatted);
		foreach($formatted as $div)
		{
			$id=$div['division_id'];
			if(isset($divs[$id]) && $divs[$id])
			{
				$div['menu_name']=$div['division_name'];
				
				//echo "found fancy name".$div['division_name']."\n";
				$return[]=$div;
				
			}
			
		}
		//var_dump($return);
		return $return;
	}
	
	public function clear_file_schedule_session()
	{
		
		$session_keys =array(
				'scheduleMatches',
				'match_pk_seq',
				'scheduleDSRules',
				's_global_rules',
				'scheduleDateSummary',
				'scheduleDateSets',
				'season_id',
				'season_start',
				'season_end',
				//'random_matches',
				'dateSetNames',
				'global_rules',
				'rawTimeslots',
				'default_rules',
				'matchesTable',
				'createdGames',
				'timeslots',
				'teamList',
				'teamName',
				'teamDates',
				'teamStats',
				'dailyGames',
				'homeGames',
				'awayGames',				
				'setGames',
				'venueCount',				
				'venueName',
				'floatingGames',		
				'currentTimeslots',
				'currentSchedule',		
				'gameStats',
				'dateStats',
				'teamVenueJoin',
				'teamDateJoin',
				'scheduleName',
				'schedule_id',
				'schedule_data',	
				'schedule_name',
				'scheduleDivisions',
				'dateset_pk_seq',
				'timeslot_pk_seq'
				
						
			);	

		foreach($session_keys as $key)
		{
			unset($_SESSION[$key]);
		}		
		return true;
	}
	/**
	* decode from json FIRST
	* will load all into _session
	* 
	* @param array $data
	*/
	public function load_file_schedule_session($data)
	{
		
		$session_keys =
		array(
				'scheduleMatches',
				'match_pk_seq',
				'scheduleDSRules',
				's_global_rules',
				'scheduleDateSummary',
				'scheduleDateSets',
				'season_id',
				'season_start',
				'season_end',
				//'random_matches',
				'dateSetNames',
				'global_rules',
				'rawTimeslots',
				'default_rules',
				'matchesTable',
				'createdGames',
				'timeslots',
				'teamList',
				'teamName',
				'teamDates',
				'teamStats',
				'dailyGames',
				'homeGames',
				'awayGames',				
				'setGames',
				'venueCount',				
				'venueName',
				'floatingGames',		
				'currentTimeslots',
				'currentSchedule',		
				'gameStats',
				'dateStats',
				'teamVenueJoin',
				'teamDateJoin',
				'scheduleName',
				'schedule_id',
				'schedule_data',	
				'schedule_name',
				'scheduleDivisions',
				'dateset_pk_seq',
				'timeslot_pk_seq'
						
			);	
			/*$_SESSION['season_id']=$id;
		$_SESSION['season_start']=$s;
		$_SESSION['season_end']=$e;
			*/
		foreach($session_keys as $key)
		{
			if(array_key_exists($key,$data))//even if its null, still save it
				$_SESSION[$key]=$data[$key];
			//else
				//$_SESSION[$key]=array();	
		}		
		
	}
 	/**
 	* 	 user note, private flag, etc
	* 
	* if $session_id==null for create new, otherwise its update json
	* $schedule_id defaults to null, itsnot needed for update
 	* 
 	* @param mixed $memo
 	* @param mixed $p
 	* @param mixed $user
 	* @param mixed $owner
 	* @param mixed $session_id
 	* @param mixed $schedule_id
 	*/
	public function file_schedule_session($memo,$p,$user,$owner,$session_id,$schedule_id)
	{
 
		//now loop on used keys
		$session_keys =array
			(
				'scheduleMatches',
				'match_pk_seq',
				'scheduleDSRules',
				's_global_rules',
				'scheduleDateSummary',
				'scheduleDateSets',
				'season_id',
				'season_start',
				'season_end',
				//'random_matches',
				'dateSetNames',
				'global_rules',
				'rawTimeslots',
				'default_rules',
				'matchesTable',
				'createdGames',
				'timeslots',
				'teamList',
				'teamName',
				'teamDates',
				'teamStats',
				'dailyGames',
				'homeGames',
				'awayGames',				
				'setGames',
				'venueCount',				
				'venueName',
				'floatingGames',		
				'currentTimeslots',
				'currentSchedule',		
				'gameStats',
				'dateStats',
				'teamVenueJoin',
				'teamDateJoin',
				'scheduleName',
				'schedule_id',
				'schedule_data',	
				'schedule_name',
				'scheduleDivisions',
				'dateset_pk_seq',
				'timeslot_pk_seq'
						
			);	

			
		$for_json=array();
		foreach($session_keys as $key)
		{
			if(isset($_SESSION[$key]))//isset is false for null or undefined, true for empty array
				$for_json[$key]=$_SESSION[$key];
			//else
				//$for_json[$key]=null;
				
				
			
		}	
		
		$json = json_encode($for_json);
		
		if($session_id==null)
		{
			$params = array($json,$memo,$p,$user,$owner,$schedule_id);
			$sql="SELECT schedule.insert_schedule_session(?,?,?,?,?,?)";
	        $query= $this->db->query($sql,$params);
	        $result=$query->first_row();
	        return $result->insert_schedule_session;
		}
		else
		{
			$params = array($json,$memo,$p,$user,$session_id);
			$sql="SELECT schedule.update_schedule_session(?,?,?,?,?)";
	        $query= $this->db->query($sql,$params);
	        $result=$query->first_row();
	        return $result->update_schedule_session;
			
		}

	}
	

	
	/**
	* get all public saves for this org
	* and private saves for this user
	* 
	* @param int $user_id
	* @param int $owner
	*/
	public function get_file_sessions($user_id,$owner,$type=3)
	{
		$sql="	SELECT  	s.created_on, s.modified_on, s.session_id, s.user_memo ,s.created_by,  
							p.person_fname||' '||p.person_lname AS created_name, sch.schedule_name,json_data
				FROM		schedule.schedule_session s 
				INNER JOIN  permissions.user u                  ON s.created_by = u.user_id AND s.deleted_flag = 'f' 
				INNER JOIN  public.entity_person p              ON u.person_id = p.person_id	
				INNER JOIN  schedule.schedule sch ON sch.schedule_id = s.schedule_id AND sch.schedule_type_id = ?    
				WHERE       s.owned_by = ? 
				AND        ( s.is_private = 'f' OR  s.created_by = ?  )  
				AND        s.deleted_flag = 'f'
				ORDER BY   s.modified_on DESC";
		$sess= $this->db->query($sql,array($type,$owner,$user_id) )->result_array();		
		
		$this->load->model('season_model');
		foreach($sess as &$sf)
		{
			$data = json_decode($sf['json_data'],true);// save everythign into session data
			
			if(!isset($data['season_id'])) {continue;}
			$sn_id = $data['season_id'];
			$season=$this->season_model->get_season_data($sn_id);
			if(count($season))
			{
				$plain='Y/m/d';
				$s=$season[0];
				$sf['season_id']     =$s['season_id'];
				$sf['season_name']   =$s['season_name'];
				//$sf['season_start']=date($plain,strtotime($s['effective_range_start']) );
				//$sf['season_end']  =date($plain,strtotime($s['effective_range_end']) );
				
			}
			unset($sf['json_data']);
		}
		
		
		return $sess;
	}

	
	
	/**
	* delete the given sesion schedule
	* 
	* @param mixed $session_id
	* @param mixed $user_id
	*/
	public function delete_file_session($session_id,$user_id)
	{
		$params = array($session_id,$user_id);
		$sql="SELECT schedule.delete_schedule_session(?,?)";
	    $query= $this->db->query($sql,$params);
	    $result=$query->first_row();
	    return $result->delete_schedule_session;
		
	}
	/**
	* get the saved session schedule based on the given session id
	* 
	* @param mixed $s_id
	*/
	public function load_file_session($s_id)
	{ 
		$sql = "SELECT s.json_data , s.user_memo, s.is_private, sch.schedule_name, sch.schedule_id
				FROM schedule.schedule_session s  
				INNER JOIN  schedule.schedule sch ON sch.schedule_id = s.schedule_id  
				WHERE s.session_id = ?";
		
		return $this->db->query($sql,array($s_id) )->result_array();	
		
		
	}

	/**
	* for each venue in the schedule, check owner facility, return ALL venues for those facilitys
	* 
	* @param mixed $sch
	*/
	public function get_schedule_venues_fac($sch)
	{
		$sql="SELECT v.venue_id, v.venue_name, v.facility_id  , f.facility_name 
				FROM public.venue v 
				INNER JOIN public.facility f ON f.facility_id = v.facility_id  
				WHERE v.facility_id IN 
				(SELECT DISTINCT gv.facility_id FROM schedule.game g 
				INNER JOIN public.venue gv ON gv.venue_id = g.venue_id AND g.schedule_id = ?)";
		return $this->db->query($sql,array($sch) )->result_array();	
		
	}
	
	
	
	
	/**
	* safely get the given key from _SESSION data
	* or return an empty array if it does not exist
	* 
	* @param mixed $session
	* @return mixed
	*/
	private function _get_session_array($session)
	{
		return (isset($_SESSION[$session])) ? $_SESSION[$session] : array();
	}
	
	
	/**
	* gets the current generated schedule as saved in the session
	* 
	*/
	public function s_current_session_schedule()
	{
		return $this->_get_session_array('currentSchedule');
		
	}
	/**
	* gets the audit schedule table
	* 
	*/
	public function s_overwrite_session_schedule($sch)
	{
		$_SESSION['currentSchedule']=$sch;
	}
	/**
	* gets the audit schedule table
	* 
	*/
	public function s_schedule_stats()
	{
		return $this->_get_session_array('gameStats');
	}
	
	/**
	* gets the audit schedule table
	* 
	*/
	public function s_schedule_vstats()
	{
		return $this->_get_session_array('venueStats');
 
	}
	/**
	* gets the audit schedule table
	* 
	*/
	public function s_team_venue()
	{
		return $this->_get_session_array('teamVenueJoin');
		
	}
	/**
	* gets the audit schedule table
	* 
	*/
	public function s_team_date()
	{
		return $this->_get_session_array('teamDateJoin');
		
	}
	/**
	* gets the audit schedule table
	* 
	*/
	public function s_schedule_matchstats()
	{
		return $this->_get_session_array('teamMatchups');
		
	}
	/**
	* gets the audit schedule table
	* 
	*/
	public function s_schedule_datestats()
	{
		return $this->_get_session_array('dateStats');
		
	}
	 /**
	* gets the audit schedule table
	* 
	*/
	public function s_audit_venue_distances()
	{
		return $this->_get_session_array('auditVenueDist');
		
	}
	
	/**
	* gets the audit schedule table
	* 
	*/
	public function s_audit_div()
	{
		return $this->_get_session_array('div_match_table');
	}
	/**
	* gets the audit schedule table
	* 
	*/
	public function s_audit_div_date()
	{
		return $this->_get_session_array('div_date_table');
		
	}
	
	/**
	* gets the audit schedule table
	* 
	*/
	public function s_audit_div_venue()
	{
		return $this->_get_session_array('div_venue_table');
		
	}
	/**
	* gets the audit schedule table
	* 
	*/
	public function s_audit_missing()
	{
		return $this->_get_session_array('missing_games_table');
		
	}
}




?>
