<?php
require_once('endeavor.php');
class Games extends Endeavor
{
	/**
	* 
	* @var schedule_model
	*/
    public $schedule_model;
	/**
    * 
    * @var statistics_model
    */
    public $statistics_model;
    /**
    * 
    * @var teams_model
    */
    public $teams_model;
    /**
    * 
    * @var games_model
    */
    public $games_model;
	/**
	* 
	* 
	* @var leagues_model
	*/
    public $leagues_model;
	
	
	public function __construct()
    {
        parent::__construct();
        $this->load->model('schedule_model');
        $this->load->model('leagues_model');
        $this->load->model('facilities_model');
        $this->load->model('divisions_model');
        $this->load->model('season_model');
        $this->load->model('teams_model');
        $this->load->model('statistics_model');
        $this->load->model('games_model');
		$this->load->library('result');
    }
	
	public function post_search_games()
	{
    	$date    = $this->input->post('search_date');
		$s_date = date('Y-m-d',strtotime($date));
    	$s   = (int)$this->input->post('schedule_id');
    	$this->result->json($this->games_model->get_games_by_sch_date($s,$s_date));
    	
	}
	
	public function post_swap_games()
    {    	
		$user 		= $this->permissions_model->get_active_user();    	
		$org 		= $this->permissions_model->get_active_org();  
		
        $first  = (int)$this->input->post('game_id');
        $second = (int)$this->input->post('swap_game_id');  
        if($first==$second)return;           
    	$request_id=null;
        $user_note   = $this->input->post('note');            
        if(!$user_note || $user_note=='null') $user_note=null;
        //return 'not done yet';
        
        $f_game=$this->games_model->get_game($first);
        $s_game=$this->games_model->get_game($second);
        
        $f=$f_game[0];
        $s=$s_game[0];
        
        //update the second game so back up its data
        $backlog_game_id=$this->games_model->insert_game($s['venue_id']
														   ,$s['schedule_id']
														   ,$s['game_date']
														   ,$s['start_time']
														   ,$s['end_time']
														   ,$user,$org	  );
														   
		$this->games_model->delete_game($backlog_game_id,$user);
		//now all the POST data can overwrite this game.  keeps game id consistent
		$this->games_model->update_game($second,$f['start_time'],$f['end_time'],$f['game_date'],$user,$f['venue_id']);
		echo $this->games_model->insert_game_backlog($second,$backlog_game_id,$first,$request_id,$user_note);	
		//now backlog the changes
		//first null: not swapping two games. request id is null if not a request
		//should echo true on success
           
        //now do the same for the second game, giving it the details of the first
        $backlog_game_id=$this->games_model->insert_game($f['venue_id']
														   ,$f['schedule_id']
														   ,$f['game_date']
														   ,$f['start_time']
														   ,$f['end_time']
														   ,$user,$org	  );
														   
		$this->games_model->delete_game($backlog_game_id,$user);//now first game has timeslot for second
		$this->games_model->update_game($first,$s['start_time'],$s['end_time'],$s['game_date'],$user,$s['venue_id']);
		//now backlog the changes
		//first null: not swapping two games. request id is null if not a request
		//should echo true on success
		echo $this->games_model->insert_game_backlog($first,$backlog_game_id,$second,$request_id,$user_note);	

        //echo $this->schedule_model->update_swap_games($first,$second,$note);
    }
    
    public function post_swap_timeslot()
    {
    	$user  = (int)$this->permissions_model->get_active_user();
    	$gid   = (int)$this->input->post('game_id');
        $ts_id = (int)$this->input->post('ts_id');
		$results=array();
		//first get data for this game. 
		$g=$this->games_model->get_game($gid);
		$game=$g[0];
		
		//then insert it as a new record in timeslots, since this will now be free
		//unless the game was un-assigned		
		if($game['game_date']!=null&&$game['start_time']!=null&&$game['end_time']!=null&&$game['venue_id']!=null)
			$results[]= $this->schedule_model->insert_timeslot($game['schedule_id'],$game['game_date'],
						$game['start_time'],$game['end_time'],$game['venue_id']);
						
		$t=$this->schedule_model->get_timeslot($ts_id);
		$ts=$t[0];
		//finally, get data for this timeslot and update_game to the new time/venue info
		$results[]=$this->games_model->update_game($gid,
						$ts['start_time'],$ts['end_time'],$ts['game_date'],$user,$ts['venue_id']);
		//$game_id,$start_time,$end_time,$date,$user,$vid)
		$this->result->json($results);
    }
    
    
    
    public function post_swap_teams()
    {
		$user  = (int)$this->permissions_model->get_active_user();
    	$gid   = (int)$this->input->post('game_id');
    	
    	//not allowed to if any valid scores exist
    	$scores=$this->statistics_model->get_valid_scores_game($gid);
		if(count($scores)  > 0) 
		{
			echo "Cannot swap teams, game has already been played and scored.";
			return;			
		}
		
		echo $this->games_model->update_swap_teams($gid,$user);
    }
    /**
    * try and assign new ts for existing game
    * 
    */
    public function post_update_game()  
    {
		$user 		= $this->permissions_model->get_active_user();    	
		$org 		= $this->permissions_model->get_active_org();    	
		$game_id 	= (int)$this->input->post('game_id');
		//so do not (int) here, or else it will be zero, we want null for this case
		$request_id = $this->input->post('request_id');//if not given will be false
		if(!$request_id || $request_id=='null') $request_id=null;
		$game_record= $this->games_model->get_game($game_id);
		$home_id=$game_record[0]['home_id'];
		$away_id=$game_record[0]['away_id'];
		//$schedule_id= $this->input->post('schedule_id');
		//$old_venue_id   = (int)$this->input->post('venue_id');
		$venue_id   = (int)$this->input->post('new_venue_id');
		if(!$venue_id)$venue_id=null;
		$oldstart=$game_record[0]['start_time'];
		$oldend  =$game_record[0]['end_time'];
		$olddate =$game_record[0]['game_date'];
		
		$gamelength_min = round(abs(strtotime($olddate." ".$oldend) - strtotime($olddate." ".$oldstart  )   )/60,2  );
		//echo "game length is ".$gamelength_min;
		$hr    = $this->input->post('new_hour');
		$min   = (int)$this->input->post('new_min');
		if(strlen($min)==0)$min = '0';
		if(strlen($min)==1)$min = '0'.$min;
		$ap   = $this->input->post('new_ampm');
		if(!$ap)$ap='pm';
		$new_start=$hr.":".$min." ".$ap;
		
		$start_time = $this->timeMerToAstro($new_start);
		//echo "start converted from ".$new_start." to ".$start_time;
		$end_time   = $this->addTime($start_time,$gamelength_min);
		
		//echo "calcualted end time is ".$end_time;
		if($end_time==-1)return;
		$date  		= date( 'Y-m-d' , strtotime($this->input->post('input_date')) );

		$user_note  = rawurldecode($this->input->post('note'));
		if(!$user_note||$user_note=='')$user_note=null;
		
		if($this->is_team_busy($home_id,$date,$start_time,$end_time))
		{
			echo "Error: Home team is busy";
			return;			
		}
		else if($this->is_team_busy($away_id,$date,$start_time,$end_time))
		{
			echo "Error: Away team is busy";
			return;			
		}
		else if($venue_id!=null && $this->is_timeslot_used($venue_id,$date,$start_time,$end_time))
		{
			echo "Error: Venue is busy";
			return;			
		}
		else
		{
			//echo "about to insert";return;
			//create a deleted game that stores the OLD timeslot. we will backlog these changes below
			$backlog_game_id=$this->games_model->insert_game($game_record[0]['venue_id']
															   ,$game_record[0]['schedule_id']
															   ,$game_record[0]['game_date']
															   ,$game_record[0]['start_time']
															   ,$game_record[0]['end_time']
															   ,$user,$org	  );
			$this->games_model->delete_game($backlog_game_id,$user);
			//now all the POST data can overwrite this game.  keeps game id consistent
			$this->games_model->update_game($game_id,$start_time,$end_time,$date,$user,$venue_id);
			//now backlog the changes
			//first null: not swapping two games. request id is null if not a request
			//should echo true on success
			echo $this->games_model->insert_game_backlog($game_id,$backlog_game_id,null,$request_id,$user_note);		
		}
    }
    
    
    public function post_unschedule_game()
    {		
		$user 		= $this->permissions_model->get_active_user();    	
		$org 		= $this->permissions_model->get_active_org();    
		$game_id 	= (int)$this->input->post('game_id');
		//two optional data items
		$user_note  = rawurldecode($this->input->post('note'));
		if(!$user_note||$user_note=='')$user_note=null;
		//so do not (int) here, or else it will be zero, we want null for this case
		$request_id = $this->input->post('request_id');//if not given will be false
		if(!$request_id || $request_id=='null') $request_id=null;
		
		//first make a backlog game
		$game_record= $this->games_model->get_game($game_id);
		
		$backlog_game_id=$this->games_model->insert_game($game_record[0]['venue_id']
														   ,$game_record[0]['schedule_id']
														   ,$game_record[0]['game_date']
														   ,$game_record[0]['start_time']
														   ,$game_record[0]['end_time']
														   ,$user,$org	  );
		/*													   
		$this->schedule_model->insert_timeslot($game_record[0]['schedule_id'],$game_record[0]['game_date'],
						$game_record[0]['start_time'],$game_record[0]['end_time'],$game_record[0]['venue_id']);
						*/
		$this->games_model->delete_game($backlog_game_id,$user);
		
		$this->games_model->update_game($game_id,null,null,null,$user,null);
		echo $this->games_model->insert_game_backlog($game_id,$backlog_game_id,null,$request_id,$user_note);		
    }
    
    
    private function timeslots_overlap($date,$start,$end,$o_start,$o_end)
	{
		if(    strtotime($date .' '. $o_start  )   
            <= strtotime($date .' '. $start) //if it starts before or at the same starttime
            && strtotime($date .' '. $start )     
             < strtotime($date .' '. $o_end))
        {
            return true;//found a conflict         
        }//else check if game starts in between the used timeslot
        else if(  strtotime($date .' '. $o_start  )   
                < strtotime($date .' '. $end) 
               && strtotime($date .' '. $end )     
               <= strtotime($date .' '. $o_end))
        {
            return true;//found a conflict    
        }
        else
        {
			return false;
        }                                   
	}    
	
	//team id check, over all venues for this slot
    private function is_team_busy($team_id,$date,$start,$end)
    {
    	$ymd_date=date('Y-m-d',strtotime($date));
    	$games=$this->games_model->get_games_by_team_date($team_id,$ymd_date);
    	foreach($games as $game)
    	{			
			$usedstart = $game['start_time'];
            $usedend   = $game['end_time']  ;
			if($this->timeslots_overlap($date,$start,$end,$usedstart,$usedend))
			{
				return true;//we found a conflict so stop looking
			}
        	//else echo "No conflict found yet, so keep checking the rest\n";                                    
		}
		//loop is over, no problems found
		return false;
    
    }
    //venue id check, all games/teams for thisslot 
    private function is_timeslot_used($venue_id,$date,$start,$end)
    {
    	$ymd_date=date('Y-m-d',strtotime($date));
    	$games=$this->games_model->get_games_by_venue_date($venue_id,$ymd_date);

    	foreach($games as $game)
    	{			
			$usedstart = $game['start_time'];
            $usedend   = $game['end_time']  ;
			if($this->timeslots_overlap($date,$start,$end,$usedstart,$usedend))
			{
				return true;//we found a conflict so stop looking
			}
        	//else echo "No conflict found yet, so keep checking the rest\n";                                    
		}
		//loop is over, no problems found
		return false;
    }
	
	
	public function json_games_by_venue_date()
	{
		$date=$this->input->post('search_date');
		$venue_id=(int)$this->input->post('venue_id');
    	$ymd_date=date('Y-m-d',strtotime($date));
		$this->result->json($this->games_model->get_games_by_venue_date($venue_id,$ymd_date));
	}
	
	 public function json_getgames()
    {
        $schedule = $this->input->post('sch');
        $games = $this->games_model->get_games($schedule);
        $fmt="F j, Y";
        foreach($games as &$g)
        	$g['game_date']=date($fmt,strtotime($g['game_date']));
        $this->result->json($games);   
        
    }
    
    public function json_games_scores()
    {
        $schedule = $this->input->get_post('schedule_id');
        $games = $this->games_model->get_games_scores($schedule);
        $fancy="F j, Y";
        $time = "g:i a";
        $plain="Y/m/d";
        //format dates if they exist
        foreach($games as &$g)
        {
        	if($g['game_date'])
        	{
        		$g['game_date']       =date($plain,strtotime($g['game_date']));				
        		$g['fancy_game_date'] =date($fancy,strtotime($g['game_date']));	//depreciated			
        	}//otherweise leave it as null	
        	if($g['start_time'])
        	{
        		$g['start_time']      =date($time,strtotime($g['start_time']));
			}			
		}
        echo $this->result->json_pag($games);   
        
    }
    
    public function json_game_results()
    {
		$game_id = (int)$this->input->post('game_id');
		$hide_discarded = $this->input->post('hide_discarded');
		if($hide_discarded===false || $hide_discarded=='false' || $hide_discarded=='f')
		{
			$hide_discarded=false;
		}
		else
		{
			$hide_discarded=true;
		}
		$results=$this->statistics_model->get_result_sumbissions($game_id,$hide_discarded);
		$fancy='M j, Y g:i A';
		foreach($results as &$r)
		{
			$r['display_home']=    $r['home_name']." ".$r['home_score']."";
			$r['display_away']=", ".$r['away_name']." ".$r['away_score']."";
			$r['display_header']=$r['display_home']." ".$r['display_away'];
			if($r['form_date'])//else leave it blank
				{$r['display_date'] =date($fancy,strtotime($r['form_date']));}
			$r['csv_status']=$r['status'].",".$r['icon'].",".$r['id'];
		}
		
		$this->result->json($results);
    }
    public function json_lu_game_result_status()
	{
		$this->result->json($this->games_model->get_lu_game_result_status());
	}
    
	public function json_past_season_games()
    {
		$season_id = (int)$this->input->post('season_id');
		$hours_ago = (int)$this->input->post('hours');
		
		$status_array = json_decode($this->input->post('status_ids'));
		
		if(is_array($status_array))
		{
			$used_game_ids=array();
			$games=array();
			//first get all games based on the given stat ids
			/*
			$query_array=array();
			foreach($status_array as $id)
			{	//ex: status array says get new, variant, but NOT conflicted
				if($id>0)
				{
					$query_array[]=$id;
				}
			}
			*/
			//$r['csv_status']=$r['status'].",".$r['icon'].",".$r['id'];
			//$m_games=array();
			$past_games=$this->games_model->get_past_season_games($season_id,true);
			foreach($past_games as $g)
			{
				$sev=$this->games_model->get_game_severity_data($g['game_id']);
				if(!count($sev)){continue;}
				//now, is this one taht we want
				$in_array=false;
				foreach($sev as $lu)
				{
					if(in_array($lu['id'],$status_array))
					{
						$in_array=true;
						break;
					}
				}
				
				if(!$in_array){continue;}
				//so yes, this one was checked in filter
				
				//the top result always has the max severity, so just use that one
				$r=$sev[0];
				
				$g['csv_status']=$r['status'].",".$r['icon'].",".$r['id'];
				$g_id=$g['game_id'];
				if(!in_array($g_id,$used_game_ids))//avoid dupicating the same game for both reasons
				{
					$games[]=$g;
					$used_game_ids[]=$g_id;
				}
			}
			//next get games with zero submissions: two categories based on _hours given
			//-1 means games finished less than $hours ago, -2 means games finished MORE THAN $hours ago: 
			$less_than = in_array(-1,$status_array);
			$more_than = in_array(-2,$status_array);

			if($less_than||$more_than)//do we have either? if so then get unscored gaems and sort by comparing to TODAY
			{
				$now=time();//timestamp for today. no need for strototime here
				$unscored_games=$this->games_model->get_past_season_games_no_score($season_id);
				foreach($unscored_games as $game)
				{
					$game_end=$game['game_date']." ".$game['end_time'];
					$diff_hours = abs(($now-strtotime($game_end))/3600);//number of seconds in an hour is 3600= 60*60
					//echo "today vs $game_end is ".$diff_hours."\n";
					$game_id=$game['game_id'];
					if(!in_array($game_id,$used_game_ids) && $less_than && $diff_hours < $hours_ago)
					{
						$game['csv_status']= 'Unsubmitted'.",".'shading'.",".-1;
						$games[]=$game;
						$used_game_ids[]=$game_id;
					}
					if(!in_array($game_id,$used_game_ids) && $more_than && $diff_hours > $hours_ago)
					{
						$game['csv_status']= 'Late Scores'.",".'hourglass'.",".-2;
						$games[]=$game;
						$used_game_ids[]= $game_id;
					}
				}
			}
			//$games=array_merge($games,  $m_games  );merge not used anymore, check for uniuqe game ids, and also other date hours tests
			//unset($m_games);		
		}
		else
		{
			$games=$this->games_model->get_past_season_games($season_id);
		}
		$fancy='M j, Y g:i A';
		foreach($games as &$g)
		{
			$g['display_date']=date($fancy,strtotime($g['game_date']." ".$g['start_time']));
		}
		$this->result->json($games);
    }
    public function json_games_by_team()
    {
		$schedule_id = (int)$this->input->post('schedule_id');
		$team_id     = (int)$this->input->post('team_id');
		$return=array();
		$games = $this->games_model->get_games($schedule_id);
		$fmt="F j, Y";
		foreach($games as $g)
		{
			if($g['home_id'] == $team_id ||$g['away_id'] == $team_id)
			{
				$g['game_date']=date($fmt,strtotime($g['game_date']));
				$return[]=$g;
			}	
		}
		$this->result->json($return);
    }
    /**
    * used for validation on delete/swap divison, to see if it is allowed
    * delete team uses teh games model function internally, but not this json
    * season_id is optional
    */
    public function json_count_team_games()
    {
    	$team_id     = (int)$this->input->post('team_id');
    	$season_id   = (int)$this->input->post('season_id');
    	if(!$season_id)
			$games = $this->games_model->count_games_by_team($team_id);
		else
			$games = $this->games_model->count_games_by_season_team($team_id,$season_id);
			
		$this->result->json($games);
    }
    
    
    public function json_games_by_date()
    {
		$schedule_id  = (int)$this->input->post('schedule_id');
		$date = rawurldecode($this->input->post('date'));
		$ts_date = strtotime($date);
		$return=array();
		$games = $this->games_model->get_games($schedule_id);
		$fmt="F j, Y";
		foreach($games as $g)
		{
			if(strtotime($g['game_date']) == $ts_date)
			{
				$g['game_date']=date($fmt,strtotime($g['game_date']));
				$return[]=$g;
			}
		}
		$this->result->json($return);
    }
    
    public function json_getgame()
    {
        $game = $this->input->post('game');
        $result = $this->games_model->get_game($game);
        $this->result->json($result);   
    }
    
    public function post_create()
    {
		$user 		= $this->permissions_model->get_active_user();    	
		$org 		= $this->permissions_model->get_active_org();   
		
		$date       = rawurldecode($this->input->post('game_date'));
		$start_time = rawurldecode($this->input->post('start_time'));
		$end_time   = rawurldecode($this->input->post('end_time'));
		$schedule_id=(int)$this->input->post('schedule_id');
		$venue_id   =(int)$this->input->post('venue_id');
		$home_id    =(int)$this->input->post('home_id');
		$away_id    =(int)$this->input->post('away_id');
    	$ymd_date   =date('Y-m-d',strtotime($date));
		
		if($home_id==$away_id)
		{
			echo "Conflict: Team cannot play itself";
			return;	
		}
		if($this->is_team_busy($home_id,$ymd_date,$start_time,$end_time))
		{
			echo "Conflict: Home team is already scheduled on this date and time.";
			return;			
		}
		else if($this->is_team_busy($away_id,$ymd_date,$start_time,$end_time))
		{
			echo "Conflict: Away team is already scheduled on this date and time.";
			return;			
		}
		else if($venue_id!=null && $this->is_timeslot_used($venue_id,$ymd_date,$start_time,$end_time))
		{
			echo "Conflict: Venue is already scheduled at this date and time.";
			return;			
		}
		else
		{
			//echo " now create game"	;
			$game_id= $this->games_model->insert_game($venue_id,$schedule_id,$ymd_date,$start_time,$end_time,$user,$org);
			$this->games_model->insert_teamgame($game_id,$home_id,true);
			$this->games_model->insert_teamgame($game_id,$away_id,false);
			echo $game_id;
		}
    }
    
    public function post_delete()
    {
		$user=$this->permissions_model->get_active_org();
		$game_id=(int)$this->input->post('game_id');
		echo $this->games_model->delete_game($game_id,$user);	
    }
    
    private function load_window()
    {
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/");
        $this->window->set_js_path("/assets/js/components/results/");
    }
    
    public function window_results()
    {
		$this->load_window();
		$this->window->set_header('Game Results');
        $this->window->set_body($this->load->view('games/results/validate.php',null,true));
        
	    $this->window->add_js('../../models/results.js'); 
	    $this->window->add_js('../../models/result_validate.js'); 
        
		$this->window->add_js('grids/results.js');
		$this->window->add_js('grids/validate.js');

		$this->window->add_js('controller.js');
		$this->window->json();
    }
    
    
    
    
    
    
    
    
    
    
    
    //copied froms schedule controler
    
        /**
    * input is of the form h:MM am||pm
    * converts to HH:MM
    * ex 2:45 pm => 14:45
    * @param string $inputTime
    */
    private function timeMerToAstro($inputTime)
    {//explode in php works like split in javascript
    	
        $arr = explode(' ',$inputTime);
        $oldTime = $arr[0];
        $ampm = $arr[1];
        $hhmm = explode(':',$oldTime);
        $hour=$hhmm[0];
        $min = $hhmm[1];
        if($ampm != 'pm' && $ampm != 'PM' )//if am or not specified
        {//then am
        	
            return $oldTime; //12 pm stays as 12
		}
        elseif($hour == 12 || $hour == '12')
        {//then 12:xx pm
			return $hour.':'.$min; //12 pm stays as 12 
        }    
        //otherwise its between 0:00 and 11:59 am
        $hour = 12+(int)$hour;
        $newTime = $hour.':'.$min;
        return $newTime;
    }
        
    /**
    * time is H:MM or HH:MM astronomical time
	* the other is a number of minutes
	* ex: (13:30, 45) would result in 14:15
	* ignores problem if new hours is > 24, must be dealt with after return
	* or guarantee it will never happen , ie. compare it to some valid end_time,then it wont matter
    * 
    * @param string $time
    * @param int $newmin
    */
    private function addTime($time , $newmin)
    {
       // echo "addTime $time with min $newmin \n";
        $error = -1;
        if(strlen($time) == 5)
        {
            $hour = (int)substr($time,0,2);
            $min  = (int)substr($time,3,2);//skip over the ':' at position 2
        }
        else if(strlen($time) == 4)
        {
            $hour = (int)substr($time,0,1);
            $min  = (int)substr($time,2,2);//skip the ':' at position 1            
        }
        else return $error;//error
        $newmin = (int) $newmin;//just in case it was a string
        $min = $min + $newmin;// just add them, and then
        // loop to convert overflow minutes to hours       
        $done=false; 
        while(!$done)
        {
            if($min >= 60)
            {
                $min = $min-60;
                $hour++;                
            }
            else
                $done=true;     //stops iff min \in [0,60)       
        }   
        if($min < 10) //then buffer with an extra zero
            return $hour . ':0' . $min;
        else
            return $hour . ':' . $min;              
    }//end addTime function
    
    
    
    
}
?>
