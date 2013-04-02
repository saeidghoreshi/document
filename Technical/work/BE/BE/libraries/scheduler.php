<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  
/**
* @author Sam
* custom CI library for scheduling
* used by Schedule controller
*/


class Scheduler
{
	
	private $CI;
	
	public function __construct()
	{ 
		//in case we need to load other libraries
		 
		$this->CI = &get_instance();
		
	}
	
	//start to move variables and other methods over
	//first list all variables used by create_timeslots 
	private $rawTimeslots=null;
	public $timeslots=null;
	private $globalRules;
	private $venueToFac=array();
	private $warnings =array();// added $this->warnings for task 1751
	private $usedFacIds=array();
	public $tsRules=array();
	public $createdGames=array();
 	private $countSlotsOnDate=array();
 	private $venueLatLon=array();
 	
 	private $teamFullySatisfied = array();//for each team id: start false; or go true if ALL mins are satisfied everywehre
	
	public $debug=false;
	
	/************************* utility mehods for sorting ********************************************/
	
	/**
    * @access private - eventually it will be private . for now public becuase 
    * scheduler is split between controller and library
    * @author Sam Bassett
    * @category schedule wizard
    * sorts by start_timestamp first, then venue_id second
    *
    * REQUIRES $this->timeslots set as the array as used by the wizard. uses: 
    * http://php.net/manual/en/function.array-multisort.php
    * 
    *  
    */
	public function sort_timeslots_by_stamp_ven($timeslots=false)
    {
    	if($timeslots) $this->timeslots=$timeslots;
    	
		$sort_bydate=array();
		$sort_byvenue=array();
		foreach($this->timeslots as $i=>$t)
		{
			$sort_bydate[$i]=$t['start_timestamp'];
			//if(!in_ array($t['venue_id'],$sort_byvenue))//no need to add same venue twice
			$sort_byvenue[$i]=$t['venue_id'];
			
		} 
		sort($sort_byvenue,SORT_ASC);//sort is maybe pointless..>?
		//array sort is the important part
		array_multisort($sort_bydate,SORT_ASC,$sort_byvenue,SORT_ASC,$this->timeslots);
		
		unset($sort_bydate);//probably pointless, as will be killed upon scope of method ending
		
		//if($this->debug) echo "_sort_timeslots_by_stamp completed";
		return $this->timeslots;
    }
	
	/**
 	* sort is a string: t, v, or d, for time venue date sorting
 	* schedule is just that, an array passed in. 
 	* you will probably pass this in from session or global
 	* 
 	* @param mixed $schedule
 	* @param mixed $sort
 	*/
    public function sort_schedule_by($schedule,$sort='d')
    { 
		switch($sort)
		{
			case 't':
				function cmp($a,$b)
				{
					$homecmp= strcmp($a['home_name'],$b['home_name']);
					//if home teams are the same, then compare away
					$bothcmp = ($homecmp==0) ?  strcmp($a['away_name'],$b['away_name']) : $homecmp; 
					return $bothcmp;					
				}
			break;
			case 'd':
				function cmp($a,$b)
				{
					return $a['start_timestamp'] > $b['start_timestamp'];
				}
			break;
			case 'v':
				function cmp($a,$b)
				{
					return strcmp($a['venue_name'],$b['venue_name']);
				}
			break;
			//easy to add more cases
		}
		
		//if one of the above cases happened
		if(function_exists('cmp'))   usort($schedule,'cmp');
		
		return $schedule;
    }
	
	/**
	* format and return the given schedule. last two arguments are optional and have useful defaults
	* fmt is the date format
	* idx is the index in the array of the string date
	* 
	* @param mixed $schedule
	* @param mixed $fmt
	* @param mixed $idx
	*/
	public function format_schedule_dates($schedule,$fmt='D M j, Y',$idx='game_date')
	{ 
		foreach($schedule as &$s) $s[$idx] = date($fmt,strtotime($s[$idx]));
		return $schedule;
	}
	 
	 /**
    * switched to Haversine forumla, august 16 2011 
    * http://www.movable-type.co.uk/scripts/gis-faq-5.1.html
    * http://en.wikipedia.org/wiki/Haversine_formula
    *
    * OLD METHOD WAS: spherical law of cosines
    * input assumed to be in DEGREES
    * 
    * http://en.wikipedia.org/wiki/Great-circle_distance
    * http://en.wikipedia.org/wiki/Central_angle
    * http://en.wikipedia.org/wiki/Spherical_law_of_cosines
    * this WAS located in facilities_model
    * @param float $lat1
    * @param float $lon1
    * @param float $lat2
    * @param float $lon2
    */
    public function lat_long_distance_between_km( $lat1,  $lon1, $lat2, $lon2)
    {
    	//radius is different depending on closeness to equator or poles, WE USE 
    	//an average (quadtratic mean // root mean square) , its good enough.
    	//more accurate way would be to estimate this based on the lat/lon given, using polar coordinates
    	$approx_radius_earth=6372.8;
    	
    	//echo "$lat1,  $lon1, $lat2, $lon2.\n";
    	
		//php trig functions MUST take arguments IN RADIANS
    	$deltalon = deg2rad($lon2 - $lon1);
		$deltalat = deg2rad($lat2 - $lat1);
		$lat1=deg2rad($lat1);
    	$lon1=deg2rad($lon1);
    	$lat2=deg2rad($lat2);
    	$lon2=deg2rad($lon2);
    	
    	//calculate the angle between the two rays
		$angle = pow(sin(  $deltalat/2  ),  2 )+ cos($lat1) * cos($lat2) * pow(sin($deltalon/2) ,2);
		
		//The min(1,..)  minimizes possible errors if the two points are very nearly antipodal
		//using this method, we only have an error of around 2 km
		$arc   = 2*asin( min(1,sqrt($angle))  );
		//arc length found using radius of earth
	    
		$arc_length_km = $approx_radius_earth * $arc;
	
		//echo "$lat1,  $lon1, $lat2, $lon2";var_dump($arc_length_km);
		return $arc_length_km;
    }
	
	/**
    * ASSUMES all inputs are timestamps, that is 
    * numbers output from strtotime
    * returns true if they overlap at all, otherwise false
    * 
    * if start == end, this is NOT an overlap
    * 
    * @param mixed $used_start
    * @param mixed $used_end
    * @param mixed $new_start
    * @param mixed $new_end
    */
    public function timestamps_overlap($used_start,$used_end,$new_start,$new_end)
    {
		if(     $used_start <= $new_start  
            &&  $new_start  <  $used_end  )
        {
            return true;//found a conflict         
        }//else check if game starts in between the used timeslot
        else if(  $used_start  < $new_end
               &&  $new_end    <=  $used_end  )
        {
            return true;//found a conflict    
        }
        //otherwise
		return false;
    }
	/**
    * all inputs rae strings
    * first is a date
    * all otehrs are TIME. 
    * it simply converts the strings to timestamps and uses 
    * existing timestamps_overlap
    * 
    * @param mixed $date
    * @param mixed $start
    * @param mixed $end
    * @param mixed $o_start
    * @param mixed $o_end
    */
    public function timeslots_overlap($date,$start,$end,$o_start,$o_end)
	{
		$a_start = strtotime($date .' '. $start  ) ;
		$a_end = strtotime($date .' '. $end  ) ;
		$b_start = strtotime($date .' '. $o_start  ) ;
		$b_end = strtotime($date .' '. $o_end  ) ;
		return $this->timestamps_overlap($a_start,$b_start,$b_start,$b_end);
                       
	}    
	
 
	
	/**
	* assumes both are STRINGS of the form H:MM or HH:MM
	* in astronomical time ( from 0:00 to 23:59), ignores any meridiem values (am/pm ignored)
	* ASSUMES also that these are valid, i.e., 77:77 is ok, not checked for errors
	* 
	* MAY NOT HAVE A LEADING ZERO
	* first check length to see if leading zero exists
	* 
	* @deprecated maybe : some places are using strtotime now intsead, but this works fine
	* 
	* FALSE IF inputs EQUAL, THIS IS CHECKING IF FIRST < SECOND STRICTLY
	* @param mixed $first
	* @param mixed $second
	*/
    public function earlierThan($first,$second)
    {        
        $error = -1;
        if(strlen($first) == 5)
        {
            $firsthour = substr($first,0,2);
            $firstmin  = substr($first,3,2);//skip over the ':' at position 2
        }
        else if(strlen($first) == 4)
        {
            $firsthour = substr($first,0,1);
            $firstmin  = substr($first,2,2);//skip the ':' at position 1            
        }
        else return $error;//error
        if(strlen($second) == 5)
        {
            $secondhour = substr($second,0,2);
            $secondmin  = substr($second,3,2);//skip over the ':' at position 2
        }
        else if(strlen($second) == 4)
        {
            $secondhour = substr($second,0,1);
            $secondmin  = substr($second,2,2);//skip the ':' at position 1            
        }
        else return $error;

        if($firsthour > $secondhour)//check hours first
            return false;
        if($firsthour < $secondhour)
            return true;
        else
        {   // so firsthour == secondhour
            if($firstmin < $secondmin)//check minutes
                return true;
            else
                return false;//if everything identical, return FALSE,
        }                        
    }//end earlierThan function
    
    
    
        /**
    * input is of the form h:MM am||pm
    * converts to HH:MM
    * ex 2:45 pm => 14:45
    * 
	* @deprecated maybe : some places are using strtotime now intsead, but this works fine
    * @param string $inputTime
    */
    public function timeMerToAstro($inputTime)
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
    * input is of the form h:MM 
    * converts to int
	* @deprecated maybe : some places are using strtotime now intsead, but this works fine
    * @param string $inputTime
    */
    public function timeToMinutes($inputTime)
    {
 
		$hhmm = explode(':',$inputTime);
		$hour= (int)$hhmm[0];
        $min = (int)$hhmm[1];
        return $hour*60+$min;
        
    }
        /**
    * converts time like h:MM to
    * integer of seconds. useful for adding timestamps returend by strtotime
    * @author sam bassett
    * @access public
    * pure util method
	* @deprecated maybe : some places are using strtotime now intsead, but this works fine
    * @returns ts int
    * @param mixed $inputTime
    */
    public function timeToSeconds($inputTime)
    {
		return $this->timeToMinutes($inputTime)*60;		
    }
    
    
    /**
    * given two strings of the form h:mm A
    * 
    * so for example ( 6:00 PM , 7:00 PM) 
    * this will return the number of minutes between them
    * 	ie 60.
    * 
    * 
    * @param mixed $start
    * @param mixed $end
    */
    public function timeBetween($start,$end)
    { 
		$s = $this->timeMerToAstro($start);
		$e = $this->timeMerToAstro($end);
		if($this->earlierThan($e,$s))
		{
			//swap if end comes first
			$temp = $s;
			$s = $e;
			$e = $temp;
		}
		//nwo compute
		return round(abs(strtotime($e) - strtotime($s)) / 60,1);
    }
    
    
      /**
    * time is H:MM or HH:MM astronomical time
	* the other is a number of minutes
	* ex: (13:30, 45) would result in 14:15
	* ignores problem if new hours is > 24, must be dealt with after return
	* or guarantee it will never happen , ie. compare it to some valid end_time,then it wont matter
    * 
	* @deprecated maybe : some places are using strtotime now intsead, but this works fine
    * @param string $time
    * @param int $newmin
    */
    public function addTime($time , $newmin)
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
    
    
    
    /******************** Load functions *****************/
    
    /**
    * load global rules. was loading from model
    * eventuallly add error checkign?
    * 
    * @param mixed $rules
    */
    public function load_globalRules($rules)
    { 
		$this->globalRules = $rules;//$this->schedule_model->s_get_global_rules();//save in global scope instead of session
    }
     
     
     private $teamTsPriority ;
     /**
     * must call this before main assign games timeslots
     * must call after validate_timeslot_rules which may modify the data used by isMinSatisfied
     * 
     * TRIGGERED by validate_timeslot_rules which satisfies the above
     * 
     * 
     */
     private function buildTeamTimeslotPriority()
     {
     	 $this->teamTsPriority = array();
     	 
     	 foreach($this->timeslots as $t_index=>$ts)
     	 { 
     	 	 //build a list for every team
     		 foreach($this->teamName as $teamid=>$teamname)
     		 {
     			 if(!isset($this->teamTsPriority[$teamid]))$this->teamTsPriority[$teamid] = array();
				 //add if this matchup would satisfy any minimum rule
				 if($this->isMinSatisfied($teamid,$t_index) == false)
				 {
				 //	 echo " add $t_index to team $teamid priority     ";
					 $this->teamTsPriority[$teamid][$t_index] = $t_index; //self index so easy to unset() later
				 }
 
     		 }
		 }
		  
     }
     
     /**
     * this team has been scheduled somewhere, update its 
     * priority list by removing any that arent high priority anymore
     * 
     */
     private function updateTeamTimeslotPriority($teamid)
     {
		 foreach($this->teamTsPriority[$teamid] as $t_index)
		 {
			 if($this->isMinSatisfied($teamid,$t_index ))
			 {
     	 		// echo "remove ts $t_index from team $teamid  ";
				 unset($this->teamTsPriority[$teamid][$t_index]);
			 }
		 }
     }
     
     /**
     * this timeslot is now in use, 
     * remove it from the priority list of 
     * ALL teams
     * 
     * @param mixed $t_index
     */
     private function removeTsPriorityAllTeams($t_index)
     {
     	 foreach($this->teamTsPriority as $teamid=>$list)
     	 {
     	 	 echo "remove ts $t_index from team $teamid  ";
			 if(isset($this->teamTsPriority[$teamid][$t_index]))unset($this->teamTsPriority[$teamid][$t_index]);
     	 }
		 
     }
     
     
     
    /**
    * see comments fo task 1751
    * 
    * here we ARE allowed to tweak user defined rules (we jsut dont save them outside of this scope
    *  
    */
    public function validate_timeslot_rules($dateset_grid)
    {
    	$this->debug = false;
		if($this->debug) echo "!! pre_process_rules !!\n";
 		
		$count_timeslots = count($this->timeslots);
		$count_games     = count($this->createdGames);
		$count_teams     = count($this->teamName);
 
		foreach($this->tsRules as $ts_id=>$tsRules)
		{ 
			if(!isset($tsRules['min_day']))  $tsRules['min_day']  = '';
			if(!isset($tsRules['min_slot'])) $tsRules['min_slot'] = '';
			
			if(  !$tsRules['min_day'] && !$tsRules['min_slot']) {continue ;}//if neither is set, skip
			
			
			//get data needed for both checks 
			$set_name  = $dateset_grid[$ts_id]['set_name'];
			$min_day   = $tsRules['min_day'];
			$min_slot  = $tsRules['min_slot'];
				
			$count_venues = count($dateset_grid[$ts_id]['venue_array']);
			$count_dates  = count($dateset_grid[$ts_id]['date_array']);
			$ts_start = $dateset_grid[$ts_id]['start_time'];
			$ts_end   = $dateset_grid[$ts_id]['end_time'];
			
			//compute estimates and totals
			$est_day_games = $this->estimate_games_between($ts_start,$ts_end);	
			$est_daily     = $est_day_games * $count_venues;	
				
			if($min_day) 
			{ 
				//remember two teams per game
				$min_games_day = floor($min_day * $count_teams / 2);
			
				if($this->debug) echo "($set_name) needs : $min_games_day games/day for all teams, have est $est_daily per day  \n";
				
				//if the min is more than estimate
				if ( $min_games_day > $est_daily)
				{
					$this->tsRules[$ts_id]['min_day'] = '';//we must ignore this rule, it cannot be satisfied
					$msg  = $set_name . " had a rule of Minimum $min_day games per team per day, which we must ignore. ";
					$msg .= "This schedule only has room for $est_daily on that slot".
					$this->_addWarning($msg);
				}
			 }
			 // else no min_day rule was set, so ok
			 if($min_slot) 
			 { 
			 	 $est_slot = $est_daily * $count_dates;
				 $min_games_slot = floor($min_slot * $count_teams / 2 );
				 if($this->debug) echo "($set_name) needs : $min_games_slot games overall for all teams, est $est_slot on THIS slot \n";
				 
			   	//if the min is more than estimate
				 if ( $min_games_slot > $est_slot)
				 {
					$this->tsRules[$ts_id]['min_slot'] = '';//we must ignore this rule, it cannot be satisfied
					$msg  = $set_name . " had a rule of Minimum $min_slot games for the whole timeslot, which we must ignore. ";
					$msg .= "This timeslot only has room for $est_slot overall.".
					$this->_addWarning($msg);
				}
			 } 
		}
    	$this->debug = false;
    	
    	$this->buildTeamTimeslotPriority();
    }
    
    /**
    * add a string to the warnings array
    * 
    * @param mixed $w
    */
    private function _addWarning($w)
    {
		$this->warnings[] = $w;
    }
    
    /**
    * return warnings
    * 
    */
    public function getWarnings()
    {
		return $this->warnings;
    }
    
    /**
    * using globalRules, and given start/end times for one given day
    * estimate the number of games that could fit here
    * 
    * assume they come in plain vanila form from user input , so 9:00 AM , 5:00 PM , etc
    * 
    * @param mixed $start
    * @param mixed $end
    */
    public function estimate_games_between($start,$end)
    {  
		$minutes_between = $this->timeBetween($start,$end);
		
		$len = $this->getFullSlotSize();

		return floor($minutes_between/$len);
    }
    
     
	/**
	* pre process matches, set up counting array for matches, gather into 
	* rawMatches
	* 
	* @param mixed $matches
	* @param mixed $shuffle
	*/
    public function pre_process_matches($matches,$shuffle=false)
    {
    	$this->teamGameRef=array();
  
		$this->countGamesOnMatch=array();
		
		if($shuffle){shuffle($matches);}//just to mix it up
 
        $this->rawMatches = array();
		foreach($matches as &$match)
        { 
			$match_pk = $match['match_pk'];
			$this->countGamesOnMatch[$match_pk]=0;
			 
	        $homediv  = $match['first_div_id'];
			$awaydiv  = $match['second_div_id'];
 
			$match['rounds_completed']=0;
			$match['enf_rounds_completed']=0;
			
        	$match['first_div_tc'] =count($this->teamList[$homediv]);
        	$match['second_div_tc']=count($this->teamList[$awaydiv]);
 
			$this->rawMatches[$match_pk]=$match;
		}
    }
    
    /**
	* optimizer
	* each matched-game has a match pk and thus (possibly) a list of allowed dates
	* if this is set, build allowed timeslot numbers into it also
	* 
	*/
    public function pre_process_game_timeslot_cmp()
	{
		$this->teamMustAvoidSlot=array();
		$this->gamesOnDate=array();
		 
 		$enforcable_matches=array();
 		foreach($this->rawMatches as $m_pk=>$m)
		{
			//if($this->debug)echo $m_pk." , enforcedates=".$this->rawMatches[$m_pk]['enforce_dates']."\n";
		
			$this->rawMatches[$m_pk]['c_timeslots_allowed'] = 0;
				
			if($this->rawMatches[$m_pk]['enforce_dates']=='f')
			{
				$this->rawMatches[$m_pk]['timeslots_allowed']=null;//for all allowed

			}
			else
			{
				$this->rawMatches[$m_pk]['timeslots_allowed']=array();
				$enforcable_matches[$m_pk]=$m_pk;
			}
				
		}// end of matches loop

 		if(!is_array($this->timeslots)) $this->timeslots=array();
		foreach($this->timeslots as $t_index=>$slot)
		{		
			$slot_stamp = $slot['date_timestamp'];

				
			foreach($this->rawMatches as $match_pk=>$match)
			{
				if($this->rawMatches[$match_pk]['timeslots_allowed']===null){continue;}
				
				
				if(isset($this->rawMatches[$match_pk]['date_stamps'][$slot_stamp]))
				{
					//then this exact date is flagged as 'allowed' by user for this match
					//make it so
					$this->rawMatches[$match_pk]['timeslots_allowed'][$t_index]=$t_index;//self indexed for optimizer
					$this->rawMatches[$match_pk]['c_timeslots_allowed'] = $this->rawMatches[$match_pk]['c_timeslots_allowed']+1;
					
					//used to optimize matches
					//if($this->debug)echo "added slot $t_index to match ".$match_pk;
				}
				
				
			}//end of matches loop

			
		}//end of timeslots loop
		
	}
    
    
     /**
     * given these two game idx
     * count the number of teams playing in both games
     * so zero means false: no teams in common - used for conflicts 
     * this ignores timestamp / scheduling
     * 
     * @param mixed $gidx_a
     * @param mixed $gidx_b
     */
    public function countTeamsInCommon($gidx_a,$gidx_b)
    {
		$a_homeid  = $this->createdGames[$gidx_a]['home_id'];
		$a_awayid  = $this->createdGames[$gidx_a]['away_id'];
		
		$b_homeid  = $this->createdGames[$gidx_b]['home_id'];
		$b_awayid  = $this->createdGames[$gidx_b]['away_id'];
		$count=0;
		if( // home team in first game is in the second game in any way
			$a_homeid == $b_homeid || 
			$a_homeid == $b_awayid  )
		{
			$count++;
		}
		if( // away team in first game is in the second game in any way
			$a_awayid == $b_homeid || 
			$a_awayid == $b_awayid  )
		{
			$count++;
		}
		return $count;
    }
    
    /******************************** Scheduler Algorithm fns ***********************************/
    
     /**
     * Jan 2012: mvoed here from schedule controller
     * 
    * create all empty timeslots basd on rawTimeslots
    * which are large chunks of time that must be broken down
    * each time a given day is full, go to next one
    * 
    * a created game will have:
    * timeslot of >= 0 : index of a location in timeslots array
    * timeslot of -1 means: to be assigned later
    * timeslot of -2 means: could not find a valid ts
    * timeslot of 'x'     : this game was added externally/manually ?? almost certainly depreciated
    */   
    public function create_timeslots($rawTimeslots)
    {
    	$this->rawTimeslots = $rawTimeslots;
 
        $rawSize = count($this->rawTimeslots); 
        
       // init variables
    	$this->venueToFac=array();
    	$this->usedFacIds=array();
		 
 	    $this->countSlotsOnDate=array();
 	    $this->venueLatLon=array();
 	    $this->timeslots=array();
       //global rules / variables 
       $global_rules=$this->globalRules;// just so we dont have to swap variables with search and replace
       
       //timeToMinutes
       $globalTimeslot=$this->timeToMinutes($global_rules['len']);
       $globalWarmup  =$this->timeToMinutes($global_rules['warmup']);
       $globalCooldown=$this->timeToMinutes($global_rules['teardown']);
       $globalminBtw=0;
       $globalmaxBtw=0;
       //$debug=true;//default rules not implemented yet in 2.0
 	   
       //return;
       //$order_index=0;
       // $this->venueOrder = array();
       $usedVenue=array();
       $t_index=0;
       $fmt_24='H:i';
       //loop on raw datesets to make timeslots
       for($r=0;$r< $rawSize ;$r++)
       {  
            // CAREFUL start and end times are like h:MM am/pm
            //dates are like tues feb 15, 2011
           $start      = $this->rawTimeslots[$r]['start_time'];
           $end        = $this->rawTimeslots[$r]['end_time'];
           //convert them
 		   $start 			 = date($fmt_24,strtotime($start)); 
 		   //$start 			 = $this->timeMerToAstro( $start ); 
 		  // $end 			 = $this->timeMerToAstro( $end );  
 		   $end 			 = date($fmt_24,strtotime($end)); 
           $date  	   = $this->rawTimeslots[$r]['game_date'];
           
           $set_name   = $this->rawTimeslots[$r]['set_name'];
           $dateset_pk = $this->rawTimeslots[$r]['dateset_pk'];  
           $set_name   = $this->rawTimeslots[$r]['set_name'];   
           $vid  	   = $this->rawTimeslots[$r]['venue_id'];
           $vname 	   = $this->rawTimeslots[$r]['venue_name'];
 		   
 		   
 		   //save bunch of data keyed to venue id
 		   if(!isset($this->venueToFac[$vid])) $this->venueToFac[$vid]=(int)$this->rawTimeslots[$r]['facility_id'];//$ven_data[''];
 		   if(!isset($this->venueName[$vid]))  $this->venueName[$vid] = $this->rawTimeslots[$r]['venue_name'];//$ven_data[''];
 		   
 		   if(!isset($this->venueLatLon[$vid]))
 		   {
 		   		$this->venueLatLon[$vid] = array();
 		   
			    $this->venueLatLon[$vid]['lat'] = $this->rawTimeslots[$r]['venue_latitude']; 
			    $this->venueLatLon[$vid]['lon'] = $this->rawTimeslots[$r]['venue_longitude']; 
 		   }
 
          // 
          
           $date_timestamp = strtotime($date);
           $sortdate = date("Y/m/d",$date_timestamp);
          // echo "Processing ".$date." from ".$set_name." pk $dateset_pk\n";
          
          //sb oct 31 2011: task 1211
          if(!isset($this->venueTotals))$this->venueTotals=array();
          if(!array_key_exists($vid,$this->venueTotals))  $this->venueTotals[$vid]=0;//init if doesnt exist yet
           		
          //name is a bit misleading ,since venueCoutn is really kind of a venue-team joint count
          if(!isset($this->venueCount)) $this->venueCount=array();
          
           if(!array_key_exists($vid,$this->venueCount)) $this->venueCount[$vid]=array();//init if doesnt exist yet
 
		   
		   $set_rules=false;
           if($set_rules&&array_key_exists('gamehr',$set_rules))
           {
           	  // echo "::use set rules ".$set_name;
			   $basicTimeslot = (int)$set_rules['gamemin']     +60*(int)$set_rules['gamehr'];
			   $basicWarmup   = (int)$set_rules['warmmin']     +60*(int)$set_rules['warmhr'];
       		   $basicCooldown = (int)$set_rules['coolmin']     +60*(int)$set_rules['coolhr'];
			   $minBtw        = (int)$set_rules['min-btw-min'] +60*(int)$set_rules['min-btw-hr'];
       		   $maxBtw        = (int)$set_rules['max-btw-min'] +60*(int)$set_rules['max-btw-hr'];
           }
           else
           {
           	   //echo "!#use glb default rules ".$set_name;
			   $basicTimeslot = $globalTimeslot;
			   $basicWarmup   = $globalWarmup;
			   $basicCooldown = $globalCooldown;
			   $minBtw 		  = $globalminBtw;
			   $maxBtw 		  = $globalmaxBtw;
           }
           if($basicTimeslot==0)
           {
			   echo "FATAL ERROR: basic game length is zero,even when we tried to use global\n";
			   $full=true;
			   return;
           }
           
          // echo  "--current b w c min max:".$basicTimeslot.','.$basicWarmup.','.$basicCooldown.','.$minBtw.','.$maxBtw."\n";
           $current = $this->addTime($start  ,$basicWarmup);///ex if timeslot is 5:00, and warmup is 0:10, then game starts at 5:10
           $c_end   = $this->addTime($current,$basicTimeslot);//then if game length of 1 hour means game ends at 6:10 BUT
           $next    = $this->addTime($c_end  ,$basicCooldown);//if cooldown is say 5min, NEXT game here cant start till 6:15
           $full=false;

          // echo "timeslot init:$start, ".$current.",".$c_end.",".$next.",$end  ";
           while(!$full)
           {               
               //would  the next timeslot start after the end of this day, if so its too 
               //late to put a game here
               //echo "try and create timeslot $date,from  $current , game would end at $next, today ends $end\n";
               if(!$this->earlierThan($end,$next))//true if equal
               { 
                   //so end of today comes after the end of current timeslot  
				   //  echo 'new timeslot created at'  .$t_index;
				   if(!isset($this->countSlotsOnDate[$date_timestamp])) {$this->countSlotsOnDate[$date_timestamp]=0;}
				   
				   $this->countSlotsOnDate[$date_timestamp]++;//for optimizer
				   //
				   $facid=$this->venueToFac[$vid];
				   
                   $this->timeslots[$t_index] = array
                   	( 
                   		'venue_id'	=> $vid, 
                   		'facility_id'=> $facid, 
                        'venue_name'=> $vname,     
                        'date'		=> $date , //THIS IS DEPRECIATED ALWAYS USE GAME_DATE
                        'game_date'	=> $sortdate ,
                        'date_timestamp'=>$date_timestamp,
                        'start'		=> $current,
                        'end'		=> $c_end,
                        'game_id'	=> -1 ,    //-1 means to be assigned later
                        'set_name'  => $set_name ,
                        'dateset_pk'=> $dateset_pk ,
                        'warmup'	=> $basicWarmup,  //int : minutes
                        'cooldown'	=> $basicCooldown, //int : minutes
                        'maxbtw'    => $minBtw,
                        'minbtw'    => $maxBtw,
                        'start_timestamp'=>strtotime($sortdate." ".$current),
                        'end_timestamp'  =>strtotime($sortdate." ".$c_end)
                    );                          
					//save the facility. might be an overrwrite, but doing isset isnt worth it
					$this->usedFacIds[$facid]=$facid;
					
					//if(!isset($this->tsidsByFacility[$facid]))$this->tsidsByFacility[$facid]=array();
					
					//$this->tsidsByFacility[$facid][]=$t_index;//save array of all ts ids on this facility
					
                   $current   = $this->addTime($next,$basicWarmup);
                   $c_end     = $this->addTime($current,$basicTimeslot);//
                   $next      = $this->addTime($c_end    ,$basicCooldown);
                   //$next      = $this->scheduler->addTime($next,   $time_used);
                   $t_index++;            
                                                  
               }
               else 
                    {$full=true;}//this venue with this date is full 
               
       	   }//end while    
       }//end for
        
       //return is not used as an index, it simulates a count()
       return $t_index; 
    }//end create_timeslots fn
    
    
     /**
    * @author Sam
    * for each pair of venues being used in this schedule, calculate the distance between them
    * uses facility_model calculation fn, that does not use db.
    * assumes venueLatLong is set up.
    * stores result in venueDistanceMatrix
    * should be used after create_timeslots, but before scheduling otherstuff
    * 
    */
    public function pre_calculate_venue_distance()
    {
    	$this->venueDistanceMatrix=array();
 
		//we could just do double loop on venueToFac[$vid], which would be O(n2)
		//instead lets flatten to a 0 indexed array of only vids, then the inner loop can be based off the outer index. should be O(n)+ O(nlogn) 
		
		$flat_vids=array_keys($this->venueToFac);
		 
		
		$flat_count=count($flat_vids);
		for($i=0;$i<$flat_count;$i++)
		{
			$i_vid = $flat_vids[$i];
			if(!isset($this->venueDistanceMatrix[$i_vid])) $this->venueDistanceMatrix[$i_vid]=array();
			$this->venueDistanceMatrix[$i_vid][$i_vid] = 0;//zero distance to itself. just planning ahead
			for($j=$i+1;$j<$flat_count;$j++)
			{
				$j_vid = $flat_vids[$j];
				
				$i_lat=$this->venueLatLon[$i_vid]['lat'];
				$i_lon=$this->venueLatLon[$i_vid]['lon'];
				
				$j_lat=$this->venueLatLon[$j_vid]['lat'];
				$j_lon=$this->venueLatLon[$j_vid]['lon'];
				
				if(!isset($this->venueDistanceMatrix[$j_vid])) $this->venueDistanceMatrix[$j_vid]=array();
				
				//since we have distance in km, convert it to meters . 1000 meters in a km
				$ij_distance= $this->lat_long_distance_between_km($i_lat,$i_lon,$j_lat,$j_lon) *1000;
				
				//save  so accessable from both . it is a symmetric matrix 
				//http://en.wikipedia.org/wiki/Symmetric_matrix
				$this->venueDistanceMatrix[$i_vid][$j_vid] = $ij_distance;
				$this->venueDistanceMatrix[$j_vid][$i_vid] = $ij_distance;
					
			}
		}
 
    }
    
    
    /**
    * takes in raw matches table from wizard
    * breaks it down and creates all GAMES for each matc
    * so perhaps this should be  called 'create_games_from_matches'
    * 
    * !important : it uses ->match_teams for every game
    * 
    * @param mixed $num_timeslots
    * @param mixed $shuffle
    */
    public function create_matches($num_timeslots,$shuffle=false)
    {

    	if(!$num_timeslots)return false;
 		$this->gamesOnFacility=array();
    	
		$breakloop=0;//in case of infinite loop
		$infin=999999;
		
		$allowed_attempts=5;
		$attempts=0;
 
		$games_created=0;
		$all_full=false;
		while ($all_full==false && $games_created < $num_timeslots )
		{
			//echo "\n top of while loop, $games_created made for $num_timeslots timeslots\n";
			$breakloop++;
			if($breakloop>$infin) 
			{
				echo 'matches breakloop fault';
				return -1*$breakloop;
			}
			$all_full=true;//assume at least one non full match
			//if all_full remains true, then we break the outer while loop
			//process matches table
			$count_before_matchloop = count($this->createdGames);
	        foreach($this->rawMatches as &$match)
	        {
        		
        		//echo "main loop on match pk "
			    
				//echo "create matches btw divs ".$homediv." , ".$awaydiv."\n";
		
		        $homediv  = $match['first_div_id'];
				$awaydiv  = $match['second_div_id'];
	            $rounds   = $match['match_rounds']; 
				$match_pk = $match['match_pk'];
				
				if($match['enforce_dates']=='t' && $match['timeslots_allowed'] !== null)	
				{
					//if dates are enforced then check how many timeslots we have
				  if(!isset($this->countGamesOnMatch[$match_pk])) $this->countGamesOnMatch[$match_pk]=0;//should never happen
 
					
					if($this->countGamesOnMatch[$match_pk] > $match['c_timeslots_allowed'])
					{
						/*
						if($this->debug){
							echo "!!! we have created more games than the match $match_pk allows: ";
							echo $this->countGamesOnMatch[$match_pk];
							echo " > ";
							echo  $match['c_timeslots_allowed'];
						}*/
						continue;//if more than we have room for in timeslots, then dont make any more matches here
					}
					//count how many timeslots are on this set of dates fuly by examining all dates
					
				}
 
				//echo "    given match_rounds ".$rounds."\n";
				
				//echo "all full is false this time";
				//to make games for teams, we will rotate one of the divisoisn
	           //find which division is smaller - never rotate the small division
	           $same_div_flag=false;
	           if($homediv == $awaydiv)
	           {         
	           	   $same_div_flag=true;
           		   //then it doesnt matter which is which
		   		  //echo 'div playing itself';
           		   $big_div_id = $homediv;
	               $sml_div_id = $awaydiv; 
           		   $big_div_teams=$this->teamList[$big_div_id];
	               $sml_div_teams=array_values($big_div_teams);
	               //array_values important to ensure taht these two 
	               //variables are not references to the same array!!!
	               
	               //except one possible problem if this division has only ONE team
           		   //then we should  skip
           		   if(count($big_div_teams)<=1) continue;
           		   
           		   
	           }//end if home==away
	           else
	           {//home and away are not the same division         
	              $hdivsize = count($this->teamList[$homediv]);
	              $adivsize = count($this->teamList[$awaydiv]);
	              //which one is bigger
	              
	              if($hdivsize >= $adivsize)
	              {
              		$big_div_id = $homediv;
              		$sml_div_id = $awaydiv;              	
				  }
	              else 
	              {
              		$big_div_id = $awaydiv;
              		$sml_div_id = $homediv;
	              }
			   }
			   //now the rest is the same regardless if a div is facing itself or not
	          // echo "div $big_div_id is bigger or equal: and $sml_div_id is smaller ";
	              //ALWAYS ROTATE THE BIGGER DIV
	              
	          //important: do array values to get a copy of array, if divs are the same we dont want same variable
	          
	          //if either division is empty, nothing to do
	          
 
	           
	          $sml_div_teams= array_values($this->teamList[$sml_div_id]);
			  $big_div_teams= array_values($this->teamList[$big_div_id]);
			  $copies=$match['rounds_completed']+1;
          	  if($big_div_id==$sml_div_id)	//Possibly rotate for the big one
          	  {
				//  echo "div plays itself so do ".$copies. " shifts";
				  while($copies>0)
				  {
					  $removed = array_shift($big_div_teams);
					  $big_div_teams[]=$removed;	  
					  $copies--;
				  }
				  //echo 'and bigggdiv is new first, see index zero: '.$big_div_teams[0]['team_id'];  echo "\n";     
				 // echo 'and smalldiv is unchanged, see index zero: '.$sml_div_teams[0]['team_id'];    
				 // echo "\n";      	  
			  }	
	          //another element of randomness
	          //because it shouldnt not matter which order these teams are in, this 
	          //is just a result of the database query or whatever . is totally arbitrary and should not 
	          //determine plays
	          if(count($big_div_teams)==0 || count($sml_div_teams)==0) continue;
			  if($shuffle)
	          {
				 shuffle($big_div_teams);
		          if($big_div_id!=$sml_div_id)//
          			shuffle($sml_div_teams); 
	          }
	 
 
			 if(  $match['enforce_rounds']=='t'&& $this->countGamesOnMatch[$match_pk]>  $match['match_rounds']*$match['games_per_round'])
			 { //$match['rounds_completed']
				if($this->debug)
				{
					echo "enf_rounds_completed:: this match #$match_pk is full with:games= ".$this->countGamesOnMatch[$match_pk]." > "
							.$match['match_rounds']." x ".$match['games_per_round'];
					echo "total games so far ".count($this->createdGames)."\n";
				}
				continue;
			 }
			 
			// else echo "not done yet, ";

			 
			  //process this match 
			  
			   $teamsused_thismatch=array();//reset this
			   $match_counter=0;
			  $all_full=false; 
			  foreach($sml_div_teams as $t=>$team)
			  {
			      //if($this->countGamesOnMatch[$match_pk] >= $match['est_games']) {break;}//done so stop cycling division
		      	  if(  $match['enforce_rounds']=='t'&& $this->countGamesOnMatch[$match_pk]>=$match['match_rounds']*$match['games_per_round'])
		      	  {
		      	  	//  $games_created++;
		      	  		if($this->debug) echo "one extra round complete, it was enforced";
		      	  	  $match['rounds_completed']++;
		      	  	  break;
		      	  }
				  $f_team_id = $big_div_teams[$t]['team_id'];
				  $s_team_id = $sml_div_teams[$t]['team_id'];
				 //  echo " found teams $f_team_id,$s_team_id";
				  
				  
				  //save names if we have to
				  if(!isset($this->teamName)){$this->teamName=array();}
				  
				  if(!isset($this->teamName[$f_team_id]))
				  {
					  $name= $big_div_teams[$t]['team_name'];
					  $this->teamName[$f_team_id] =$name;
					  
				  }
					 
				  if(!isset($this->teamName[$s_team_id]))
				  {
					  $name= $sml_div_teams[$t]['team_name'];
					  $this->teamName[$s_team_id] = $name;
					  
				  }
				  
				  if($f_team_id==$s_team_id){continue;}//skip this case ->only comes up if div plays itself- and do not count as a game
				  // A vs A : skip
				/*  if($same_div_flag)
				  {
					  //if a div plays itself
					  //we should only get half as many games as usual
					  //so A vs B: booked already but that means B vs A here has already hapened so skipit
				 
					  //in which ccase, flag the division as 'yes' it has been completed
					  if(isset($teamsused_thismatch[$f_team_id][$s_team_id]) && $teamsused_thismatch[$f_team_id][$s_team_id]) {   continue;}
					  if(isset($teamsused_thismatch[$s_team_id][$f_team_id]) && $teamsused_thismatch[$s_team_id][$f_team_id]) {   continue;}
				  }*/
				  
				 //match_teams will create a game and return game index, or false  if it failed
 				 
 				 //assume teams not satisfied, no scheduling has happened yet
 				 if(!isset($this->teamFullySatisfied[$f_team_id])) $this->teamFullySatisfied[$f_team_id] = false;
 				 if(!isset($this->teamFullySatisfied[$s_team_id])) $this->teamFullySatisfied[$s_team_id] = false;
 				 	
				  $new_game_index = $this->match_teams($f_team_id,$s_team_id,$match_pk); 
				  //we dont actually use the return value here, but from other places we do
				 // $games_created++;// count even if match teams fails, otherwise infinite loop ?? fixable?
				  
				  if($new_game_index!==false && $new_game_index>=0)
				  {
					  //if matched
					  
					  if(!isset($teamsused_thismatch[$f_team_id])) $teamsused_thismatch[$f_team_id]=array();
					  if(!isset($teamsused_thismatch[$s_team_id])) $teamsused_thismatch[$s_team_id]=array();
					  
					  $teamsused_thismatch[$f_team_id][$s_team_id]=true;//f has played s
					  $teamsused_thismatch[$s_team_id][$f_team_id]=true;//flag to say s does not need to play f, , used only if in same division
					  
					  if(!isset($this->countGamesOnMatch[$match_pk])) {$this->countGamesOnMatch[$match_pk]=0;}
					  
					  $this->countGamesOnMatch[$match_pk]++;//count this game
					//  echo "\ngame number ".$this->countGamesOnMatch[$match_pk]." is ";echo $this->teamName[$f_team_id];echo $this->teamName[$s_team_id];
					  $match_counter++;
					  $games_created++;
				  }
				//  else {echo "ERROR ON NEWGAMEINDEX ";var_dump($new_game_index);}
			  }//end foreach sml
			//  echo "\n so $match_pk so far has games=".$this->countGamesOnMatch[$match_pk]."but total is ".count($this->createdGames);
			  $match['rounds_completed']++;
			  $match['enf_rounds_completed'] = $match['enf_rounds_completed']+1;
			 // echo" one round completed for#$match_pk , increment round counter to new value  of: ".$match['enf_rounds_completed']."\n";
			 
			 if(   $big_div_id!=$sml_div_id)	//why cant we do this all the time?
          	  {
				  //echo 'rotate larger division';
				  $removed = array_shift($this->teamList[$big_div_id]);
				  $this->teamList[$big_div_id][]=$removed;	
			  }
				  //important for multiple rounds, so each team has a chance of playing each other team.			  
			 // } //else we are done so dont bother	  
			                     
	  
				if($games_created >= $num_timeslots)
				{
					if($this->debug)echo "made too many games and only partway thru matches table, stop early";
					break;//this breaks the for loopbut not the while loop - not directly
				}
				
				//TODO: compare games_created  
				
	        }//end second matches for loop
			
			if($count_before_matchloop == count($this->createdGames))
			{
				//echo "no new games added thru all table so end early";
				$attempts++;

				
				if($attempts > $allowed_attempts)$all_full=true;
				//if we processed each 'raw match' once but added zero games, we are done
			}
 			else $attempts=0;//some were added so start over
        }//end while loop
  			
		
	    
        return $games_created;
	}//end matches function
   
   /**
    *  check every $teamid participates in a game
    * try and swap games to balance it with other teams
    * MUST BE RUN BEFORE TIMESLOTS ARE ASSIGNED
    * 
    * @param mixed $dist
    */
    public function balance_homeaway($dist=0)
    {
    	//echo "balance_homeaway started\n";
       // $this->countUnbalance=0;
        $infin = 999999;//debug numbers for infinite loops
        $breakloop = 0;
        
        //$unbalanced=0;
       // $dist = 0;//how far from fair we can be
       // $teamCount = count($this->teamList);
        //$allDivs =  array(58,57,56,55,54,53,52);
        $allgames = count($this->createdGames);
        if(!is_array($this->teamList)) return;
        foreach($this->teamList as $divid => $list){
        $teamCount = count($this->teamList[$divid]);
        for($t=0;  $t< $teamCount; $t++)
        {
            // so $this->teamList[$divid] === $list, if that matters
            //all we needed was the $divid
			$breakloop=0;
			$infin=999999;
            $teamid = $this->teamList[$divid][$t]['team_id'];
            
            $balanced = false;
          //  $this->countUnbalance++; //assume this is  not balanced
            
           
           //this if isset is check is in multiple spots= may be a bit redundant, maybe not
           if(!isset($this->homeGames[$teamid] ))
                $this->homeGames[$teamid]=0;
            if(!isset($this->awayGames[$teamid]))
                $this->awayGames[$teamid]=0;

                    
           $index=0;//start at the begining to search for games, because this is a different team now
           $homeCount = $this->homeGames[$teamid];
           $awayCount = $this->awayGames[$teamid];
           $total = $homeCount + $awayCount;
            if($total!=0) {
            while(!$balanced)
            {   
            // echo "attempt to balance teamid $teamid\n";
                $breakloop++;
                if($breakloop>$infin)     //JUST IN CASE-  an infinite loop breaker
                {
                    //echo "balance_homeaway at $dist had abreakloop fail, could not balance \n";
                    return;
                }
                
                $fair = floor($total/2);
                               
              //  echo "fair $fair, total $total, homecount $homeCount, teamid $teamid\n";
                //also if this team plays zero games, ignore
                if($total!=0 && $homeCount > $fair  )
                {      //echo "too many home games\n                                     
                    $gameFound=false;       
                    //$unbalanced++;             
                    //in created games, find a game where $teamid is the home team
                    while(!$gameFound && $index < $allgames)
                    {                                           
                        $compare = $this->createdGames[$index]["home_id"];
                        if($compare == $teamid)
                            $gameFound=true;
                        else      
                        {                  
                            //check if this is playing as away team here?    
                            
                                                
                            //no do nothing for now
                            $index++;             
                        }
            
                    }//end while                    
                    if($gameFound)
                    {//if found, $index is valid, so  maybe swap
                        $newTeam = $this->createdGames[$index]["away_id"];
                        //is the new team fair or overloaded with home games-> no swap
                        $newHC = $this->homeGames[$newTeam];
                        $newFair= floor(($this->awayGames[$newTeam]+$newHC )/2);                        
                        if(  $newHC < $newFair +$dist) 
                            $this->swap_teams($index);
                       // else
                      //      echo "game found, but no swap\n";
                        //whether or not we swap, dont check this game again right now
                        $index++;//next time start here for gameFound while search loop 
                                           
                    }//endif game found
                    else//if nothing found to swap with, balanced as much as possible
                    {
                        //we have checked every game that $teamid participates in
                        //they are all balanced already
                        
                        //echo "ran out of games to check, set balance to true we gave up\n";
                        
                        //$unbalanced stays the same, its counted
                        $balanced=true;//but stop trying anyway
                        
                    }
                }
                else //if($home > $fair-$dist)
                {
                	//$unbalanced--;
                 /*   if($total!=0)
                    if($awayCount > $fair  )
                        echo "too many away games-do nothing\n";
                    else
                        echo "balance success\n";*/
                  //  $this->countUnbalance--;//need to count this on games not on teams, see below     
                    $balanced = true;    //it is already balanced from before 
                }   
                  
                  
                $homeCount = $this->homeGames[$teamid];
                $awayCount = $this->awayGames[$teamid];
                $total = $homeCount + $awayCount;                  
            }//endwhile                        
            }//end if total != 0                   
        }}//end for loop and foreach
 
        $homeSmall=array();
        $homeLarge=array();
        
        //first simple swaps are done, now check again, this time 
        //with more intensive deep_swap being used
        foreach($this->teamList as $divid => $list)
        {
        //foreach($this->allDivs as $divid )
        //{
           // if(isset($this->teamList[$divid]))
                $teamCount = count($this->teamList[$divid]);
           // else
           //     $teamCount=0;
            for($t=0;  $t< $teamCount; $t++)
            {
            $team = $this->teamList[$divid][$t]['team_id'];
            $home = $this->homeGames[$team];
            $away = $this->awayGames[$team];
            $total = $home+$away;
            
            if($total!=0)            
            if($home != floor($total/2) && $home != ceil($total/2) )
            {
                //create multiple entries in these arrays depending on distance from fair
                //ex, if fair == 13, and home == 15, put the team id in there twice
                //this is because we must swap two games for that team in order to reduce its home to 13
                if($home > $away)
                {
                    //if total is 25, , and home > 12.5
                    //it is fair to bring  $home down to 13
                    $fair =  ceil($total/2);                    
                    $dist = $home - $fair;
                    while($dist != 0)
                    {                        
                        $homeLarge[count($homeLarge)] = $team; 
                        $dist--;
                    }
                }
                else
                {
                    //if total is 25, and home < 12.5
                    //it is fair t obring home up to 12
                    $fair = floor($total/2);                    
                    $dist = $fair - $home;                    
                    while($dist != 0)
                    {                        
                        $homeSmall[count($homeSmall) ] = $team;                        
                        $dist--;
                    }
                }
            }//else perfect balance achieved,. or off by 1, which is fine
        }}//end for loop and foreach

        $failed=array();
        
        $sc = count($homeSmall) ;
        $lc = count($homeLarge);
        $min = min($sc,$lc);
        //careful they may not be the same length
        for($i=0;$i<$min;$i++)
        {
            $success = $this->deep_swap($homeSmall[$i],$homeLarge[$i] );
            if(!$success)//if this doesnt work
            {//do each seperately
                $this->deep_swap( $homeSmall[$i] ,'');
                $this->deep_swap('',$homeLarge[$i]);                
            }
        }
        if($sc>$lc)
            for($i=$lc;$i<$sc;$i++)
                $this->deep_swap( $homeSmall[$i] ,'');                
        if($sc<$lc)
            for($i=$sc;$i<$lc;$i++)
                $this->deep_swap('',$homeLarge[$i]);
            

        //just a test display loop 
 
        //echo "balance_homeaway() completed\n";
    }//end balance_homeaway
    
   
   /**
    * one index to timeslots, and one index to games
    * it assinngs them to each other
    * it ASSUMES that this is no conflict, must come after all 
    * verificiation . this is just a data handing fn
    * 
    * @param mixed $t_index
    * @param mixed $g_index
    */
    private function assign_game_timeslot($t_index,$g_index,$match_pk=false)
    {
 
    	
		if($t_index == -1 || $t_index===false || $t_index===''||$t_index===null)//must be triple, because zero index is ok
        {

            $this->createdGames[$g_index]['venue_id']   = -1;
            $this->createdGames[$g_index]['timeslot']   = -2;
            $this->createdGames[$g_index]['venue']      = 'None Available';
            //$this->createdGames[$g_index]['date']       = "Not Scheduled";
            $this->createdGames[$g_index]['game_date']       = false;
            $this->createdGames[$g_index]['start_time'] = '';
            $this->createdGames[$g_index]['end_time']   = '';
            
             //$this->match_count[$match_pk]++;//we tried and failed to find at imeslot for this match, count it anyway to indicate DONE
             //otherwise we will infintely look for a ts for this match over and over and never find one
             
             
            //$this->floatingGames stays the same
            return false;
        }
        else 
        {
             if(!$match_pk) $match_pk=  $this->createdGames[$g_index]['match_pk'];
               
             if(!isset($this->match_count[$match_pk])) $this->match_count[$match_pk] = 0;//index of -1 means not from a matchi
             $this->match_count[$match_pk]++;//successssss
             
            $venueid    = $this->timeslots[$t_index]['venue_id'];  //make sure we have correct optimal venueid      
            $facid      = $this->timeslots[$t_index]['facility_id'];          
            $date       = $this->timeslots[$t_index]['game_date'];
            $set_name   = $this->timeslots[$t_index]['set_name'];
            $dateset_pk = $this->timeslots[$t_index]['dateset_pk'];
            
            //save the game index in a few places
            if(!isset($this->gamesOnFacility[$facid])) $this->gamesOnFacility[$facid]=array();

            $this->gamesOnFacility[$facid][]=$g_index;//used for facility_lock checks
            
            $this->timeslots[$t_index]['game_id'] = $g_index;    //to skip over easily check if !=-1 
            $this->timeslots[$t_index]['g_index'] = $g_index;    //to skip over easily check if !=-1 
            //$set_name = 
            $home_team_id = $this->createdGames[$g_index]['home_id'];
            $homediv      = $this->createdGames[$g_index]["home_div"];
            $away_team_id = $this->createdGames[$g_index]['away_id'];
            $awaydiv      = $this->createdGames[$g_index]['away_div'];  
            $this->floatingGames--; //one more game assigned, so not floating  
            
            //all data gathered: handle assignment now
            
            // !! add trigger for game priority: task 1751 
            $this->updateTeamTimeslotPriority($home_team_id);
            $this->updateTeamTimeslotPriority($away_team_id);
            
            //teams were assigned to game already in create_matches()                  
            $this->createdGames[$g_index]['match_pk']   = $match_pk;        
            $this->createdGames[$g_index]['venue_id']   = $venueid;
            $this->createdGames[$g_index]['facility_id']= $facid;
            $this->createdGames[$g_index]['timeslot']   = $t_index;
            $this->createdGames[$g_index]['t_index']    = $t_index;
            $this->createdGames[$g_index]['venue_name'] = $this->timeslots[$t_index]['venue_name'];
            //$this->createdGames[$g_index]['date']       = $date;
            $this->createdGames[$g_index]['start_time'] = $this->timeslots[$t_index]['start'];
            $this->createdGames[$g_index]['end_time']   = $this->timeslots[$t_index]['end'];
            $this->createdGames[$g_index]['game_date']  = $this->timeslots[$t_index]['game_date'];
            
            $game_date_ts = $this->timeslots[$t_index]['date_timestamp'];
            $this->createdGames[$g_index]['start_timestamp']  = $this->timeslots[$t_index]['start_timestamp'];
            $this->createdGames[$g_index]['end_timestamp']    = $this->timeslots[$t_index]['end_timestamp'];
            $this->createdGames[$g_index]['date_timestamp']   = $game_date_ts;
            
            if(!isset($this->gamesOnDate[$game_date_ts]))  $this->gamesOnDate[$game_date_ts] = array();
            
            $this->gamesOnDate[$game_date_ts][]=$g_index;//needed for team buffer
            
            $this->createdGames[$g_index]['set_name']   = $set_name;
            //$this->timeslots[$t_index]['dateset_pk'];
            //count number of games for each set/team combo
 
            if(!array_key_exists($dateset_pk,$this->setGames))   $this->setGames[$dateset_pk] = array();
				
			if(!array_key_exists($home_team_id,$this->setGames[$dateset_pk]))  $this->setGames[$dateset_pk][$home_team_id]=0;
            
                
            if(!array_key_exists($away_team_id,$this->setGames[$dateset_pk]))  $this->setGames[$dateset_pk][$away_team_id]=0;	
			
			
			
			
			$this->setGames[$dateset_pk][$home_team_id]++;
            $this->setGames[$dateset_pk][$away_team_id]++;
                
                
                
           // echo "put game index $g_index onto both teams $home_team_id, $away_team_id";
			if(!array_key_exists($home_team_id,$this->teamGameRef	))  $this->teamGameRef	[$home_team_id]=array();
            
            $this->teamGameRef	[$home_team_id][]=$g_index;
            
			if(!array_key_exists($away_team_id,$this->teamGameRef	))  $this->teamGameRef	[$away_team_id]=array();
            
            $this->teamGameRef	[$away_team_id][]=$g_index;
                
            //update team rating based on venue pref match quality measured for each team 
            // echo "update team ratings\n";
            $homeid = $home_team_id;//in cased i missed one
            $awayid = $away_team_id;//and less chance of errors
            $rHome = $this->quality_measure($homeid,$homediv,$venueid); 
            $rAway = $this->quality_measure($awayid,$awaydiv,$venueid); 
            //if these teams havent been seen before, setup stats array for them
            if(!isset( $this->teamStats[$homeid]))  $this->teamStats[$homeid]=array();
            if(!isset( $this->teamStats[$awayid]))  $this->teamStats[$awayid]=array();
                
            if(isset($this->teamStats[$homeid]['rating'] ))
                $this->teamStats[$homeid]['rating'] = $this->teamStats[$homeid]['rating'] + $rHome;  
            else
                $this->teamStats[$homeid]['rating'] = $rHome;  
                 
            if(isset($this->teamStats[$awayid]['rating']))                
                $this->teamStats[$awayid]['rating'] = $this->teamStats[$awayid]['rating'] + $rAway;
            else
                $this->teamStats[$awayid]['rating'] = $rAway;
            
            $this->teamStats[$homeid][]=$t_index;//wtf is this for
            $this->teamStats[$awayid][]=$t_index;
            
            //move daily counter up by one for each team
            
            if(!array_key_exists($date,$this->dailyGames[$homeid]))$this->dailyGames[$homeid][$date]=0;
            if(!array_key_exists($date,$this->dailyGames[$awayid]))$this->dailyGames[$awayid][$date]=0;
            
            $this->dailyGames[$homeid][$date] = $this->dailyGames[$homeid][$date] +1;
            $this->dailyGames[$awayid][$date] = $this->dailyGames[$awayid][$date] +1;
            //count how many times each venue is used total

            if(isset($this->venueCount[$venueid]['total'] ))
                $this->venueCount[$venueid]['total'] = $this->venueCount[$venueid]['total']+1;
            else
                $this->venueCount[$venueid]['total']=1;
            // and how many times each team plays at this venue
            if(isset( $this->venueCount[$venueid][$homeid]  ))
                $this->venueCount[$venueid][$homeid]++;
            else
                $this->venueCount[$venueid][$homeid]=1;
            if(isset($this->venueCount[$venueid][$awayid]))   
                $this->venueCount[$venueid][$awayid]++;
            else
                $this->venueCount[$venueid][$awayid]=1;
         
            //!!! the homeGames and awayGames counters were created within create_matches(), 
 
            return true;               
        }//ends else branch for the if $t_index == -1            
	

    }
    
    
    /**
    * MAIN ALGORITHM
    * much of work pushed up to preprocessing steps for opmitizer
    * or inside sub functions called as a result of this.
    * 
    * happens AFTER all teams are paried up
    * and AFTER all timeslots are broken down into game slots
    * 
    * last step to make schedule
    * for each game , find a time slot for this game, 
    * that does not have conflicts
    * uses  find_timeslot  and assign_game_timeslot
    * 
    */
    public function assign_games_to_timeslots($shuffle=false)
    {
    	//echo "assign_games_to_timeslots!!!\n";
		$gamesCount = count($this->createdGames);
        // 
        
    	if($this->debug)echo "\nassign_games_to_timeslots for ".$gamesCount." total games";
        $this->floatingGames=$gamesCount;//number of games with no timeslot assigned
 
		if($shuffle) {shuffle($this->createdGames);}//if asked  
 
    	$nothing_changed=false;//depreciated variable
 
		
		//create ONE game for this match
		for($g_index= 0 ;$g_index<$gamesCount;$g_index++)
		{    
		    $assigned_this_loop=0;
 
			if($this->createdGames[$g_index]['timeslot'] != -1) continue;//-1 means it needs to be assigned -2 or other means its failed
  
		    //find a valid timeslot for this game,
		    $t_index = $this->find_timeslot($g_index); 
		      
		    //now we have an index of a timeslot, to match with index of a game: go
		    //even if find_tiemslot didnt find anything/returend failure, go here anyway to handle taht
			//this handles all the simple assignmetn and stats computations
		    $success = $this->assign_game_timeslot($t_index,$g_index);
		    $assigned_this_loop++; 
		}//endfor loop on games count   
 
		return $this->floatingGames;//n
    }
    
 	/**
 	* opposite of assign_
 	* this simply pulls apart the game and timeslot from each other
 	*  
 	* 
 	* @param mixed $t_index
 	* @param mixed $g_index
 	*/
    private function un_assign_game_timeslot($t_index,$g_index)
    {
    	//remove ts from game
        $this->createdGames[$g_index]['venue_id']   = -1;
        $this->createdGames[$g_index]['timeslot']   = -2;
        $this->createdGames[$g_index]['t_index']   = -2;
		$this->createdGames[$g_index]['venue']      = -1;
            //$this->createdGames[$g_index]['date']       = "Not Scheduled";
        $this->createdGames[$g_index]['game_date']       = false;
        $this->createdGames[$g_index]['start_time'] = '';
        $this->createdGames[$g_index]['end_time']   = '';
        
        
        //remove game from ts
        $this->timeslots[$t_index]['game_id']=-1;
        $this->timeslots[$t_index]['g_index']=-1;
    }
    
    
    
    
    /**
    * for the given team, and the given timeslot
    * determine if TSRULES minimums have been satisfied here
    * 
    * ex if dateset 'X' and date 'today' a team is playing 1 game on this day, and 2 on this X
    * but has min of 1 per day and 3 on this X
    * then return false, NOT all mins are satisfied, and therefore scheduling this team here WOULD get closer to
    * satisfying all the minimums
    * 
    * otherwise true: minimums are satisfied here
    * 
    * @param mixed $teamid
    * @param mixed $t_index
    */
    private function isMinSatisfied($teamid,$t_index)
    {
        $dspk = $this->timeslots[$t_index]['dateset_pk'];
		$ds_rules = $this->tsRules[$dspk];//$this->schedule_model->s_get_ds_rules($ds);		
		 if(!isset($this->dailyGames)) $this->dailyGames = array();
		 if(!isset($this->setGames  )) $this->setGames   = array();
		if($ds_rules['is_active'] === true || $ds_rules['is_active']=='t')
		{
			$d 		     = $this->timeslots[$t_index]['game_date'];
			$dateset_pk  = $this->timeslots[$t_index]['dateset_pk']; 
			
			if(!isset($this->dailyGames[$teamid][$d]))        $this->dailyGames[$teamid][$d]        = 0;
			if(!isset($this->setGames[$dateset_pk][$teamid])) $this->setGames[$dateset_pk][$teamid] = 0;
			
			$day_played = $this->dailyGames[$teamid][$d]; 
			
			$set_played = $this->setGames[$dateset_pk][$teamid]; 
				
			$min_day     = $ds_rules['min_day'];
			$min         = $ds_rules['min_slot'];
			if($min_day) 
			{
				//echo "isMinSatisfied $dspk day $day_played < $min_day ";
				if($day_played < $min_day  )
				{
					return false;
				}	
				
			}
			
			if($min)     
			{
				//echo "isMinSatisfied $dspk set $set_played < $min ";
				if($set_played < $min  )
				{
					return false;
				}
			}
			
			
			//if we havent sent back false yet, then either rule is blank (possibly from preprocess override)
			//or its satisfied
		}
		//else no rules, so of course satisfied
		
		return true;
    }
 
    
    
    
    
    /**
    * find a timeslot for a game that can be used by this match / these teams, without conflicts
    * returns id of that timeslot that will not conflict
    * upgrade: optimizer:
    * 
    * this g_index has a match pk 
    * 
    * so go we should have already preprocessed and determined if this game has date restrictions
    * if so, we will have a list of timeslot ids to loop on
    * if not we will go as normal
    * @param mixed $g_index : location of gamematch in createGame
    */
    private function find_timeslot($g_index)
    {
		 $r_date=false;$r_dateset_pk=false;//DEPRECIATED
  
		 //get basic info for this game
		 $homeid   = $this->createdGames[$g_index]['home_id'];
		 $awayid   = $this->createdGames[$g_index]['away_id']; 
		 $match_pk = $this->createdGames[$g_index]['match_pk'];
 
		 if($homeid==$awayid)
		 {
		 	 //should not happen unless create_matches breaks down. but if it does, dont book this game regardless
			if($this->debug)  echo "!#!# error home == away at game index $g_index  \n\n\n";
			 return -1;
		 }
 
 
        $valid_ts = array();
        $valid_len = 0;
 
        foreach($this->timeslots as $ts=>$slot)
        {
			//validate timeslot: so check all possible conflicts - teams, rules,etc. all in thismethod 
            if($this->is_game_timeslot_safe($ts,$g_index) == false) {  continue;}//invalid, so stop now
             
             
             //first check if any teams have a prioority list ing for 
             if(count($this->teamTsPriority[$homeid]))
             {
				 /// home team has some priority listings left
				 if(!isset($this->teamTsPriority[$homeid][$ts]))
				 {
					 //found a slot not in my priority list, im gona ignore it
					 continue;
				 }
             }
             if(count($this->teamTsPriority[$awayid]))
             {
				 //away team has some priority listings left
				 if(!isset($this->teamTsPriority[$awayid][$ts]))
				 {
					 //found a slot not in my priority list, im gona ignore it
					 continue;
				 }
             }
 
 
 
 
            //count data
			$venue_id = $this->timeslots[$ts]['venue_id'];
            
            $ven_usage = $this->venueCount[$venue_id][$homeid] + $this->venueCount[$venue_id][$awayid];
            
            if(  $ven_usage == 0 || $this->globalRules['facility_lock']===true )
            {	//if venue has never been used, we are done this is greedy optimal
				//or if we are told to skip candidate_slots deal
				//because if games are locked per facility, dont bother trying to optimize
 				
				$FOUND = $ts;  
			}
			else
			{
				//without this, its easy to get
				//one team with zero played on diamond x, but 22 played on diamond y, for example.
				$start_ts = $this->timeslots[$ts]['start_timestamp'];
				//it is already sorted by timestamp
				//look for other timestamps after this with the same start
				$r_candidate = array($ts);
				$ts_count = count($this->timeslots);
 				 
				$start_loop = $ts+1; 
				for($j=$start_loop;$j<$ts_count;$j++)
				{
					$j_start = $this->timeslots[$j]['start_timestamp']; 
					if($j_start == $start_ts   ) # TODO maybe also restrict so same datesetpk 
					{
						//if($g_index==1) echo "\ngame one has ANOTHER safe timeslot $j";
						 
						if($this->is_game_timeslot_safe($j,$g_index))
						{
							
							$r_candidate[]=$j;
							 
						}
					}
					else
					{
						break;//we know sorted so at this poitn we are past to next slot.
					}
				}
				 
				$min_usage=null;
				$min_usage_tsid=null;
				
				//now find the  candidate with minimal v_usage
				foreach($r_candidate as $ts_id)
				{
					$vid = $this->timeslots[$ts_id]['venue_id'];
					if(!isset($this->venueCount[$vid])) $this->venueCount[$vid]=array();
					if(!isset($this->venueCount[$vid][$homeid])) $this->venueCount[$vid][$homeid]=0;
					if(!isset($this->venueCount[$vid][$awayid])) $this->venueCount[$vid][$awayid]=0;
					if($min_usage_tsid===null)
					{
						//first one
						$min_usage_tsid = $ts_id ; 
						$min_usage = $this->venueCount[$vid][$homeid] + $this->venueCount[$vid][$awayid];
						continue;
					}
					$testme = $this->venueCount[$vid][$homeid] + $this->venueCount[$vid][$awayid];
					if($testme < $min_usage)
					{
						//found new optimal
						$min_usage=$testme;
						$min_usage_tsid=$ts_id;
					}
					 
					//ok but now consider the new minimum game balancing rules
 
				}
				$FOUND =  $min_usage_tsid;//may or may not be the same as $ts, the loop pointer
			}//else
          

          
          
          return $FOUND;
          
          
          
          
          
           /*
//THE FOLLOWING IS DEPRECIATED UNTIL QUALITY MEASURE AND VENUE STATS ARE IMPLEMENTED
$valid_ts[$valid_len]=$ts;//save this index
                    $valid_len++;
                       // echo "not over max_games limit, one valid timeslot found \n";
                        // success! found a valid timeslot for this match
                       //if venPrefs for these two divisions are empty
                       //then take first timeslot and run, stop lookign for more
                       //this makes it more efficient
                                        
                        //if no prefs anywhere    
                       // echo "TODO: here we consider venue order and rank... eventually\n";
                         
                        $homenull = ($this->venuePrefs == null || $this->venuePrefs[$homediv] == null 
                            || count($this->venuePrefs[$homediv]) == 0 );
                        $awaynull = ($this->venuePrefs == null|| $this->venuePrefs[$awaydiv] == null 
                            || count($this->venuePrefs[$awaydiv]) == 0 );
                        if( $homenull && $awaynull && 
                                !isset($this->divRules[$homediv])  && 
                                !isset($this->divRules[$awaydiv] ) )
                        //{
                            //in this case, just return it as soon as we know its in the right order
                          //  echo "shortcut timeslot\n";

                   // else  echo "almost valid timeslot found , but daily games over rules max !#\n";                                                        

                //else echo "conflict found\n";
                //if either one fails, not a valid timeslot, ignore and move along
                */
                
                                  
        }//end ts loop
        
        //if we did not find oen that is minimal overall, take the best out of all candidate timeslots
        
 
//THE FOLLOWING IS DEPRECIATED UNTIL QUALITY MEASURE AND VENUE STATS ARE IMPLEMENTED
        /*
        if($valid_len == 0)
            return -1;//to indicate no valid timeslots found          
            
        //at least one valid timeslot is found, so (greedy) find the optimal one
        //NOW EACH ENTRY OF VALID_TS IS AN INDEX for the array $timeslots
        
        $weekday_value = 2;//somewhat arbitrary
        //this way it is worth double of any given venue statistic
        
                
        $o_ts = $valid_ts[0];//start with first timeslot      
        $venueid = $this->timeslots[0]['venue_id'];//get rating for first venue
        $adate   = $this->timeslots[0]['date'];//
        $o_home = $this->quality_measure($homeid,$homediv,$venueid);  
        $o_away = $this->quality_measure($awayid,$awaydiv,$venueid);  
        $o_best = $o_home + $o_away;
        
        $atime = strtotime($adate);
        $wd    = date('D',$atime);
        
        $prefWeekday=0;//number of divs that preff this weekday
        //add one point for each div that is on a pref weekday
        if(isset($this->divRules[$homediv][$wd]) && $this->divRules[$homediv][$wd] == 'p')
            $prefWeekday-=$weekday_value;//smaller rating is better
        if(isset($this->divRules[$awaydiv][$wd]) && $this->divRules[$awaydiv][$wd] == 'p')
            $prefWeekday-=$weekday_value;
        $o_best += $prefWeekday;
        $o_wd = $wd;
       // echo "$o_wd initial ts so far rated obest $o_best\n";
        //var_dump($this->divRules[$homediv]);
       // var_dump($this->divRules[$awaydiv]);
         //if none at all are on a prefered weekday, this is irelevant
         //if at least one is on a prefered weekday, igonre all otehres that are NOT on such
      //  echo "found at least one valid timeslot\n";
        for($p=1;$p<$valid_len;$p++)//p for potential
        {
            
            //echo "testing $p\n";
            $new_ts = $valid_ts[$p];//only test valid timeslots
            $venueid = $this->timeslots[$new_ts]['venue_id'];
            
            //go to valid_ts[p] , check its rating for each team
            $r_home = $this->quality_measure($homeid,$homediv,$venueid);
            $r_away = $this->quality_measure($awayid,$awaydiv,$venueid);
            
            $adate   = $this->timeslots[$new_ts]['date'];
            $atime = strtotime($adate);
            $wd    = date('D',$atime);
            //$prefWeekday=0;//
            if(isset($this->divRules[$homediv][$wd]) && $this->divRules[$homediv][$wd] == 'p')
                $r_home-=$weekday_value;//otherwise, false or null
            if(isset($this->divRules[$awaydiv][$wd]) && $this->divRules[$awaydiv][$wd] == 'p')
                $r_away-=$weekday_value;//since smaller rating is better
            
            $o_new = $r_home + $r_away ;
           // if($wd == "Tue")
                
            //if we have found a better match                  
            if($o_new   < $o_best)
            {    
                $o_wd = $wd;//for debug mostly
                $o_home = $r_home;
                $o_away = $r_away;
                $o_best = $o_new;
                $o_ts   = $new_ts;//save new timeslot id  
                //echo "$o_wd new optimal timeslot is o_best, rated $o_best\n";                  
              //  echo "a timeslot on $wd NEW obest $o_best\n";
            }   //if same rating total; just keep previous                                 
        }//end potential for loop  
       // if($o_best != 0) 
           // echo "$o_wd ts found at end with rating $o_best \n";
       return $o_ts;// return the optimal timeslot   
       */
    } //end find timeslot function
    
    
    
    
    
    
        
    /**
    * RETURNS true if there is a conflict
    * FALSE means the given game date / times are valid to enter a game into
    * assumes gamestart and gameend are of the form h:mm or hh:mm
    * game date is something along the lines of Feb 1, 2011
    * 
    * @param int $teamid
    * @param int $divid
    * @param mixed $gamedate
    * @param mixed $gamestart
    * @param mixed $gameend
    */
    private function game_conflict($teamid,$divid,$gamedate,$gamestart,$gameend)
    {    
			
		//TODO: use these timestamps
		$new_start_ts = strtotime($gamedate." ".$gamestart);
        $new_end_ts =	strtotime($gamedate." ".$gameend);
        $new_date_ts = strtotime($gamedate);
		//$this->teams_model->get_team_exceptions($teamid)
       // if($this->debug) echo "game conflict start $teamid,$divid,$gamedate,$gamestart,$gameend  \n";
        if(isset($this->teamDates[$teamid]) && $this->teamDates[$teamid] != null)  
        foreach($this->teamDates[$teamid] as $gc =>$data)
        {            
        	//todo:?? preprocess these in the start, and save their strtotime timestamps before we get here
            $rangestart = $this->teamDates[$teamid][$gc]['effective_range_start'];
            $rangeend   = $this->teamDates[$teamid][$gc]['effective_range_end'];
            $daystart   = "";//$this->teamDates[$teamid][$gc]['starttime'];
            $dayend     = "";//$this->teamDates[$teamid][$gc]['endtime'];

            if(strtotime($rangestart) <= $new_date_ts
            && $new_date_ts           <= strtotime($rangeend))
            {
                //so game is in the valid range of dates

                // echo "  from teamDates return true \n";
                    return true;  //both empty means restriction is for all day  
         
            }//otherwise, no conflict here so do nothing, check the next one
        }//end for loop         
		//done checking team exceptions
        
        //otherwise check
       // echo "   skip teamStats rating   ";
		        if(false){
		        foreach($this->teamStats[$teamid] as $key => $t_index)
		        {   
		            if($t_index != 'rating' && $t_index != -1 && 
		                  $gamedate == $this->timeslots[$t_index]['game_date'] )
		            {
		                //so there is a used timeslot on the same date, by this team
		                      
		            }//END OUTER IF
		       }//END foreach
			   }
       if(!isset($this->teamGameRef[$teamid])) $this->teamGameRef[$teamid]=array();
       
      //if($this->debug) echo "  check existing games  , team $teamid is playing only ".count($this->teamGameRef[$teamid])."\n";
       //$games=count($this->createdGames);
       
	   foreach($this->teamGameRef[$teamid] as $g)
       {


           if($this->createdGames[$g]['timeslot'] == -1) {continue;}
         //  {//if this existing game has been assigned a timeslot
            $homeid   =   $this->createdGames[$g]["home_id"];
            $awayid   =   $this->createdGames[$g]["away_id"];
            if($teamid == $homeid || $teamid == $awayid)
            { 
                //and if this existing game has THIS current team in it
              // echo "found a game with an overlapping team\n";
                if(!isset($this->createdGames[$g]['game_date'])) continue;
                //$useddate =  $this->createdGames[$g]['game_date'];
                //$plain='Y/m/d';

                $useddate_compare=$this->createdGames[$g]['date_timestamp'];
                
            //  if($this->debug) echo "compare $useddate_compare == $gamedate_compare after we format\n";
                if($useddate_compare == $new_date_ts)
                {
                 //  echo "game $gamestart , $gameend is new withpossible conflict, overlaping team and date:\n";
				 	//if($this->debug)
				 	//	echo "game_conflict ".$this->createdGames[$g]['game_date']." $gamestart , $gameend is testing\n";
				 	 
                    $used_start_ts = $this->createdGames[$g]['start_timestamp'];
	                $used_end_ts   = $this->createdGames[$g]['end_timestamp'];

	                if($this->timestamps_overlap($used_start_ts,$used_end_ts,$new_start_ts ,$new_end_ts   )) 
	                	{return true;}//yes there is a conflict here
	                 
	                //else keep checking other places
 
                                                    
                }//end if same date                                
            } //end if teamid                                             
           //}                      
       }//end games for loop
       
       //echo "game_conflict will return false\n";

        return false;//if we made it through all that without fail, no conflict exists             
    }//end game_conflict function
     
     
         /**
    * we know these two games need a game created between them (with no timeslot)
    * just decide here which is home, and which is away
    * assume first team is home team, and check if we need to swap
    * if a team is playing itself, continue anyway, assume this is handled elsewhere (which it is)
    * @param mixed $homeid
    * @param mixed $homediv
    * @param mixed $awayid
    * @param mixed $awaydiv
    */
    private function match_teams($homeid,$awayid,$match_pk)
	{
        
        
        if($homeid == $awayid)
        {
          // echo "!!!!!!!!!!!!!!!!!match_teams was given the same team to play itself\n";
            return false;
        }
        //init data if not saved
 
        if(!isset($this->head_to_head[$homeid]))            $this->head_to_head[$homeid]=array();
        if(!isset($this->head_to_head[$homeid][$awayid])) 	$this->head_to_head[$homeid][$awayid]=0;	
         	
        if(!isset($this->head_to_head[$awayid]))            $this->head_to_head[$awayid]=array();
        if(!isset($this->head_to_head[$awayid][$homeid]))	$this->head_to_head[$awayid][$homeid]=0;
        
        if(!isset($this->homeGames[$awayid] ))$this->homeGames[$awayid]=0;
        if(!isset($this->homeGames[$homeid])) $this->homeGames[$homeid]=0;
        if(!isset($this->awayGames[$awayid] ))$this->awayGames[$awayid]=0;
        if(!isset($this->awayGames[$homeid])) $this->awayGames[$homeid]=0;
            
        
        $homediv 	= $this->teamDivId[$homeid];
        $awaydiv 	= $this->teamDivId[$awayid];
        $home_total = $this->homeGames[$homeid] +   $this->awayGames[$homeid];
        $away_total = $this->homeGames[$awayid] +   $this->awayGames[$awayid];
        
        $global =$this->globalRules;
        if($global['max'] !=0 && $global['max']!=''&&$match_pk !=-1 && $match_pk!=-1)//if so then compare played to global max
        if(  $home_total >= $global['max'] || $away_total >= $global['max']  )
        {
			//echo "match_teams $match_pk cannot exceeed global max ".$global['max'];
			
			if(!isset($this->matchOverflow[$match_pk]))
				$this->matchOverflow[$match_pk]=0;

			$this->matchOverflow[$match_pk]++;//one game failed to be created
			
			return false;
			
        }
 
        //check local balance first before global balance..  
        // so if never met or are balanced h2h, then add to minimal globly
        
        if($this->head_to_head[$homeid][$awayid] == $this->head_to_head[$awayid][$homeid])//if same here, do globally
        { 
        	//same so check global home and away games
	        if($this->homeGames[$awayid] < $this->homeGames[$homeid] )//so same team wont be home twice in row
	        {
	            //swap both team and div
	            $swap = $homediv;
	            $homediv = $awaydiv;
	            $awaydiv = $swap;
	            
	            $swap=$homeid;
	            $homeid=$awayid;
	            $awayid=$swap;
	        }
	        else if ($this->homeGames[$awayid] == $this->homeGames[$homeid] ) 
	        {
	            $homeTotal = $this->homeGames[$homeid] + $this->homeGames[$homeid];
	            $awayTotal = $this->awayGames[$awayid] + $this->awayGames[$awayid];
	            //if there is a tie, make the team with the fewest existing matches take the home slot
	            if($homeTotal > $awayTotal)
	            {
	                //swap both team and div
	                $swap = $homediv;
	                $homediv = $awaydiv;
	                $awaydiv = $swap;
	                
	                $swap=$homeid;
	                $homeid=$awayid;
	                $awayid=$swap;
	                
	            }//should leave more room for the other team
	            
	        }            //if both were zero, nothing changed
	        //else current home is smaller, so keep it here
		}
		else
		{
			//pick minimum locally
			if($this->head_to_head[$homeid][$awayid] > $this->head_to_head[$awayid][$homeid])
			{
				//current home has been a home team more times than current away -----swap
				//swap both team and div
	            $swap = $homediv;
	            $homediv = $awaydiv;
	            $awaydiv = $swap;
	            
	            $swap=$homeid;
	            $homeid=$awayid;
	            $awayid=$swap;
	            
			}
			//else no swap; stay the same
			
		}
        $homename  = $this->teamName[$homeid];
        $awayname  = $this->teamName[$awayid];// ### UNDEFINED INDEX =>
        $homecount = $this->homeGames[$homeid];
        $awaycount = $this->homeGames[$awayid];
        
        $this->head_to_head[$homeid][$awayid]++;
		//not the other way
 
       // echo "$homecount IS <=  $awaycount for hometeam: $homename, awayteam: $awayname \n";
    
	    	//$rec=$this->divisions_model->get_team_division($teamid,$this->season_id);
        //create game with empty timeslot 
        
        $new_game_index = count($this->createdGames);
        $this->createdGames[$new_game_index] =  array ( 
                        'home_div'  =>$homediv, 
                        'home_id'   =>$homeid, 
                        'home_name' =>$homename, 
                        'away_div'  =>$awaydiv, 
                        'away_id'   =>$awayid, 
                        'away_name' =>$awayname, 
                        'match_pk'  =>$match_pk,  
                        'g_index'=>$new_game_index,
                        'venue_id'  =>-1, 
                        'facility_id'  =>-1, 
                        'timeslot'=> -1, 'game_date'=>'', 'start_time'=>'', 'end_time'=>'',
                        'date_timestamp'=>'',
                        'start_timestamp'=>'',
                        'end_timestamp'=>''
                        );

        //count number of TOTAL home and away games, 
        $this->homeGames[$homeid] = $this->homeGames[$homeid] +1;
        $this->awayGames[$awayid] = $this->awayGames[$awayid] +1;
        
        
        // i think totalGames is never used
        if(!isset(  $this->totalGames[$homeid]  ))
	        $this->totalGames[$homeid]=0;
	        
	    if(!isset(  $this->totalGames[$awayid]  ))
	        $this->totalGames[$awayid]=0;
	    $this->totalGames[$homeid]++;
	    
	    $this->totalGames[$awayid]++;
        
        return $new_game_index;

         
        
    }//end match_teams fn

     /**
    * 
    * the main validation step
    * 
    * it calls all the validation sub functions
    * 
    * @access public
    * @author 
    * for use at end of scheduler wizard generation
    * 
    */
    public function validate_schedule()
    { 
    	  if($this->debug)echo "\nvalidate_schedule() start\n";
    	  
    	  //find out if we need to match any teams in new games
    	  
		 $this->_validate_missing_rule_games();
    	  
    	  //schedule any floating games, (whether from above function or not)
		 $this->_validate_unassigned_games();
		 
		 //this was the wrong place. lets take it out
		 // $this->_validate_missing_rule_games();
		 
		 $this->_validate_team_conflicts();
		 //check team balancing.
		 //$this->_validate_team_venue_balance();
		
    }
     /**
    * @author sam
    * @access private
    * 
    * validates that the 'minimum' rule is satisfied for all teams
    * and adds extra (unnassigned) games for those teams.
    * 
    * this should come after "_validate_unassigned_games" 
    * which may affect counts
    * 
    */
    private function _validate_missing_rule_games()
    {
        if($this->debug)echo "\nvalidate_missing_rule_games() start\n";
        
        
		$grules =$this->globalRules;

		$rule_min = (int)$grules['min'];
		//echo $rule_min;
		//foreach($this->teamStats)
		$team_count=array();
		
		foreach($this->createdGames as $g_index=>$g)
		{
			//if()
			if(!isset($team_count[$g['home_id']]))$team_count[$g['home_id']]=0;
			if(!isset($team_count[$g['away_id']]))$team_count[$g['away_id']]=0;
			$team_count[$g['home_id']] ++;
			$team_count[$g['away_id']] ++;
		}
		//convert count to 'difference'
		foreach($team_count as $team_id=>$ct)
		{
 
			$team_count[$team_id] = $rule_min-$ct;
			//echo "it needs ".$team_count[$team_id]." more games"."\n";
				//echo "\n".$this->teamName[$team_id]." other count method is ".($this->homeGames[$team_id] + $this->homeGames[$team_id]);
		}
		$teams_toschedule=array();
		$nothingleft=false;
		while(!$nothingleft)
		{
			//in case some teams have 3 or 4, go through this a few times to line up teh teams in a row
			$numfound=0;
			foreach($team_count as $team_id=>$ct)
			{
				if($ct>0)
				{
					$teams_toschedule[]=$team_id;
					$team_count[$team_id]--;
					$numfound++;
				}
			}
			if($numfound==0)$nothingleft=true;//means we are done
		}
		$loop = count($teams_toschedule);
		for($i=0;$i<$loop;$i+=2)//go up by two not just one
		{
			if(!isset($teams_toschedule[$i+1])) break;
			$first_team_id = $teams_toschedule[$i];
			$secnd_team_id = $teams_toschedule[$i+1];
			
			if($secnd_team_id == $first_team_id) {continue;}//team cannot play itself
			
			if($this->debug) echo "\n NEWVALIDATEGAME".$this->teamName[$first_team_id] .' vs '.$this->teamName[$secnd_team_id]."\n";
			
			//$g_index = count($this->createdGames);
			$r=$this->match_teams($first_team_id,$secnd_team_id,null);
 
			if($r)
				$this->createdGames[$r]['timeslot']=-2;//flag as unable to schedule for next phase of validate
			
 
			
		}
		
 
    }
    
    
    /**
    * @deprecated : because of venue distance rules. may be used again if there is an option to turn off 'venue distance' , but not likely
    * 
    * for now venue_balance is only the default handing during assignment, which is reasonable but not 
    * perfect
    * @access private
    * @author sam bassett
    * for use inside validation phase of scheduler wizard
    * 
    */
    private function _validate_team_venue_balance()
    {
    	return false;
    	if($this->globalRules['facility_lock']==true)
    	{
			if($this->debug)echo "_validate_team_venue_balance() cancelled, facility_lock is true";
			return;
    	}
    	$this->debug=false;
    	 
    	//first we need to break it down by start time, and examine all the games in that block.  
    	//they can/will trade venues when not optimized
    	
		$unique_times = $this->_unique_start_times();
		foreach($unique_times as $ut)
		{
			 
			$curr_games=$this->_games_on_start_ts($ut);
			
			//each game has two teams and each team has a count array of venues
			
			
			$curr_teams = $this->_team_ids_in_game_array($curr_games);
			$curr_team_usage =array();
			foreach($curr_teams as $tid)
			{
				//echo "calculate for $tid";
				$curr_team_usage[$tid] = $this->_count_venue_usage_forteam($tid);
			} 
			$avg_tot=0;
			$avg_cnt=0;
			foreach($curr_games as $cg)
			{
				if($cg['venue_id']<0) continue;
				$avg_tot+= $curr_team_usage[$cg['home_id']][$cg['venue_id']]+$curr_team_usage[$cg['away_id']][$cg['venue_id']];
				$avg_cnt+=2;//two teams per game
			}
			 
		    //	if(count($curr_team_usage)==0){echo "zero wtf";continue;}
		    $avg_team_usage=($avg_cnt==0)?0:$avg_tot/$avg_cnt;
			//$avg_team_usage_flr = floor($avg_tot/$avg_cnt);
			//$avg_team_usage_cil = ceil($avg_tot/$avg_cnt);
			//echo " avg_flr = $avg_team_usage_flr  ";
			$threshold = 1;
			//$safe_range= array($avg_team_usage_flr-$threshold,$avg_team_usage_flr,
				//	$avg_team_usage_cil,$avg_team_usage_cil+$threshold);//just roughly
			$safe_upper = $avg_team_usage+$threshold;
			$safe_lower = $avg_team_usage-$threshold;
			//TODO: threshold for $avg_team_usage_flr range could be calculated smarter
			$moveable_up=array();
			$moveable_down=array();
			foreach($curr_games as $g)
			{
				//do a quick survey through them
				if($g['venue_id'] <0) continue;
				$ht = $g['home_id'];
				$at = $g['away_id'];
				$vid= $g['venue_id'];
				$home_usage = $curr_team_usage[$ht][$vid];
				$away_usage = $curr_team_usage[$at][$vid];
				//arrays not accurete move to direct compare 
				if($home_usage < $safe_lower && $away_usage < $safe_lower)
				{
					//uses less than average on this venue, so could go higher
					$moveable_up[]=$g['g_index'];
					//echo " team at least in average range continue;\n";
				}
				else if($away_usage >$safe_lower && $away_usage> $safe_lower)
				{
					
					$moveable_down[]=$g['g_index'];
				}
 
					//else both teams within average range
				 
			}
			 
			if(count($moveable_up) >0 && count($moveable_down) >0 )
			{
				//if($this->debug) echo "found unbalance in venues taht we can probalby fix";
				//so there are two movable games
				$mg_a = $moveable_up[0];
				$mg_b = $moveable_down[0];
				$ven_game_a = $this->createdGames[$mg_a]['venue_id'];
				$ven_game_b = $this->createdGames[$mg_b]['venue_id'];
				if($ven_game_a != $ven_game_b) //probably will never happen as equal
				{
					//so swap games venueses
					//since one is mroe and one is less than average
					
					//if both were above, or both below,w e would not swap
					$this->_swap_game_venues($mg_a,$mg_b);	
				}
				
			}
			
		}
    }
    /**
    * also swaps timeslot id
    * assumes start/date/etc times are equal
    * used in validation step
    * careful using this that id doesnt break anything, there are no checks or anything
    * 
    * @access private
    * @author sam bassett
    * 
    * @param mixed $g_idx_first
    * @param mixed $g_idx_secnd
    */
    private function _swap_game_venues($g_idx_first,$g_idx_secnd)
    {
		$first_vid = $this->createdGames[$g_idx_first]['venue_id'];
		$first_ts  = $this->createdGames[$g_idx_first]['timeslot'];
		$secnd_vid = $this->createdGames[$g_idx_secnd]['venue_id'];
		$secnd_ts  = $this->createdGames[$g_idx_secnd]['timeslot'];
		//also swap timeslot ids to keep consistent. timeslots we already knwo are identical, same datetime so just this is diff
		$this->createdGames[$g_idx_first]['venue_id']=$secnd_vid;
		$this->createdGames[$g_idx_first]['timeslot']=$secnd_ts;
		$this->createdGames[$g_idx_secnd]['venue_id']=$first_vid;
		$this->createdGames[$g_idx_secnd]['timeslot']=$first_ts;
    }
    /**
    * @returns array indexed by venue_id=>count
    * 
    * @param mixed $team_id
    */
    private function _count_venue_usage_forteam($team_id)
    {
    	$vcount=array();
		foreach($this->createdGames as $g)
		{
			if($team_id != $g['home_id'] &&$team_id != $g['away_id'] )continue;
			
			$vid=$g['venue_id'];
			if(!$vid || $vid<0) continue;//skip if null or empty or -1
			
			if(!isset($vcount[$vid])) $vcount[$vid]=0;
			
			$vcount[$vid]++;
		}
		return $vcount;
    } 
    /**
    * @author sam bassett
    * @access private
    * 
    * changed to be self indexing, avoids is array usage
    * get all starting timestamps in the schedule without repetition
    * 
    */
    private function _unique_start_times()
    {
    	$times=array();
    	if(!is_array($this->createdGames)) $this->createdGames=array();
		foreach($this->createdGames as $g)
		{
			$ts = $g['start_timestamp'];
			$times[$ts]=$ts;
		}
		return $times;
		
    }
    
    /**
    * @access private
    * @author sam bassett
    * @returns array of games
    * 
    * used by validate_schedule() 
    * @param int $ds_timestamp
    */
    private function _games_on_date($ds_timestamp)
    {
		$games=array();
		foreach($this->createdGames as $idx=>$g)
		{
			if($g['date_timestamp'] == $ds_timestamp)
			{
				$g['g_index'] = $idx;
				$games[] = $g;
			} 
		}
		return $games;
    }
    /**
    * @access private
    * @author sam bassett
    * @returns array of games
    * 
    * used by validate_schedule() 
    * @param int $start_timestamp
    */
    private function _games_on_start_ts($start_timestamp)
    {
		$games=array();
		foreach($this->createdGames as $idx=>$g)
		{
			if($g['start_timestamp'] == $start_timestamp)
			{
				$g['g_index'] = $idx;
				$games[] = $g;
			} 
		}
		return $games;
    }

    /**
    * @author sam bassett
    * @access private
    * used ty validate_schedule()
    * DOES INCLUDE the input as part of output
    * return array of timeslot ids with the same start/end timestamps as the given timeslot
    * do not return a copy of input
    * of course it ignores venues
    * 
    * @param mixed $timeslot_index
    * @returns array
    */
    private function _unused_timeslots_on_date_time($timeslot_index)
    {
		$start = $this->timeslots[$timeslot_index]['start_timestamp'];
		$end   = $this->timeslots[$timeslot_index]['end_timestamp']; 
		 
		$slots=$this->_gather_unused_timeslots();
		
		$today_slots=array();
		
		foreach($slots as $tsid)
		{
			//if($s['t_index']==$timeslot_index) continue;
			$s = $this->timeslots[$tsid];
			if($s['start_timestamp'] == $start
			&& $s['end_timestamp']   == $end)
			{
				$today_slots[]=$tsid;
			}
			
		}
		
		return $today_slots;
    }
    /**
    * @access private
    * @author sam bassett
    * lists out all team ids, ignoring home/away, for the given games
    * used by validate_schedule()
    * 
    * return array will be self indexing to avoid using in _array
    * so arr[teamid] = teamid
    * 
    * to use isset
    * 
    * further, this avoids duplicates as it will just overwrite
    * 
    * @returns array of team ids
    * @param array $games
    */
    private function _team_ids_in_game_array($games)
    {
		$t_ids=array();
		
		foreach($games as $g)
		{
			$t_ids[$g['home_id']]=$g['home_id'];
			$t_ids[$g['away_id']]=$g['away_id'];
		}
		
		return $t_ids;
    }
     /**
     * find all games with no timeslot, but not ones with '-1' key
     * -2 means we tried and failed to assign this game 
     * 
     */
    private function _gather_unassigned_games()
    {
    	$floating=array();
    	if(!is_array($this->createdGames)) $this->createdGames=array();//in case of errrs - like if zero teams somethign was skipped
		foreach($this->createdGames as $i=>$g)
		{
			if($g['timeslot']==-2)
			{
				//$g['g_index']=$i;//self reference GEB
				$floating[]=$i;				
			}
		}
		return $floating;
    }
     /**
     * find all timeslots with no game
     * 
     */
    private function _gather_unused_timeslots()
    {
		$floating=array();
 
		foreach($this->timeslots as $i=>$t)
		{
			 
			if($t['game_id']==-1)
			{
				//$t['t_index']=$i;//self reference GEB
				$floating[]=$i;				
			}
		}
		return $floating;
		
    }
    
    /**
    * split game into chunked sub array groups,
    * each group is indexed by the VALUE of the given idx
    * 
    */
    private function _split_games_by_index($idx = 'start_timestamp')
    {
    	$found = array();
    	if(!is_array($this->createdGames)) $this->createdGames = array();//in case of errrs - like if zero teams somethign was skipped
		foreach($this->createdGames as $i=>$g)
		{
			$pk = $g[$idx];
			if(!isset($found[$pk])) $found[$pk] = array();
			
			$found[$pk][] = $g;
 
		}
		return $found;
    }
    
    //linked list of adjacent games
    private $llGamesAdj;
    //linked list of games in the same day , non adj
    private $llGamesNaDay;
    /**
    * build linked lists that take each game id and go to an array of other game ids
    * one list for adj,
    * one for non adj but same day
    * always going from before to after im tersm of date
    * 
    * builds global arrays:
    * llGamesAdj
    * llGamesNaDay
    * 
    * expensive in terms of running O(n^2)
    */
    private function buildLinkedListGamseAdj()
    {
    	$this->llGamesAdj=array();
    	$this->llGamesNaDay=array();
    	//for each game, we just check each otehr game.
    	$ignoreTeams=true;
		foreach($this->createdGames as $fg_idx=>$fg)
		{
			if(!isset($this->llGamesAdj[$fg_idx]))  $this->llGamesAdj[$fg_idx]  =array();
			if(!isset($this->llGamesNaDay[$fg_idx]))$this->llGamesNaDay[$fg_idx]=array();
 
			$fg_start = $fg['start_timestamp'];
			$fg_date = $fg['date_timestamp'];
			foreach($this->createdGames as $sg_idx=>$sg)
			{
				if($fg_idx==$sg_idx){continue;}//dont compare to itself
				
				$sg_start = $sg['start_timestamp'];
				$sg_date = $sg['date_timestamp'];
				if($fg_date == $sg_date &&  $fg_start < $sg_start)
				{
					//good they happen on the same date
					//AND  second game does indeed happen after the first game
					
					//now, find out if games are adjacent
					$fg_ts=$fg['timeslot'];
					$sg_ts=$sg['timeslot'];
					
					//in this case, we want the game overlap, but we dont care if teams overlap or not for now
					if($this->are_games_adjacent_and_teamoverlap($fg_idx,$fg_ts,$sg_idx,$sg_ts,$ignoreTeams))
					{
						$this->llGamesAdj[$fg_idx][]=$sg_idx;
						
					}
					else
					{
						///well the gaems arent adjacent, but they are on teh same day: put here
						$this->llGamesNaDay[$fg_idx][]=$sg_idx;
					}
					
					
				}
				
			}
			
			
		}
 
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
        
    /**
    * assume we cannot swap these teams direclty in any matchup between them
    * goal: find two games with some $alt team such that home and away are 
    *     alt     vs.      hSmall
    *     hLarge   vs.      alt
    * call swap_teams on both of these pairs. counts for team _alt_ are kept the same
    * finally: hLarge plays one less home game and hSmall plays one more home game 
    * @param int $hSmall is a teamid for a team that has too few home games 
    * @param int $hLarge is a teamid for a team that has too MANYhome games
    */
    private function deep_swap($hSmall,$hLarge)
    {
        //echo "start of deep_swap hSmall =$hSmall,hLarge=$hLarge, \n";
        $gCount = count($this->createdGames);
        
        if($hSmall === ''  )
        {
            //so $hLarge is a teamid such that it plays TOO MANY HOME GAMES
            
            //find a game where $hLarge is the hometeam, and 
            //for the otherTeam, away >= home. then swap
            for($d=0;$d<$gCount;$d++)
            {
            if( $hLarge == $this->createdGames[$d]['home_id'] )
            { 
                $otherTeam = $this->createdGames[$d]['away_id'];
                $home = $this->homeGames[$otherTeam];
                $away = $this->awayGames[$otherTeam];
                //$total = $home+$away;                                                
                if($away > $home)
                {
                   // echo "deep_swap success with hSmall empty string\n";
                    $this->swap_teams($d);
                    // so now $hLarge plays one less home game
                    //$otherTeam plays one more homegame
                    return true;
                }
            }//else keep looking
            }//end of for loop
            
            return false;// none found            
        }     //endif small is ''                   
        if($hLarge === ''  )
        {
            //mirror image of above case
            //$hSmall is a team that plays TOO FEW home games
            for($d=0;$d<$gCount;$d++)
            {
            if( $hSmall == $this->createdGames[$d]['away_id'] )
            { 
                $otherTeam = $this->createdGames[$d]['home_id'];
                $home = $this->homeGames[$otherTeam];
                $away = $this->awayGames[$otherTeam];
                //$total = $home+$away;                                                
                if($away < $home)
                {
                  //  echo "deep_swap success with hLarge empty string\n";
                    $this->swap_teams($d);
                    // so now $hLarge plays one less home game
                    //$otherTeam plays one more homegame
                    return true;
                }
            }//else keep looking
            }//end of for loop
            
            return false;// none found 
            
            
            return false;
        }//endif large is ''
            
        
        

        $foundAlts=array();
        $foundIndex=0;
        for($d=0;$d<$gCount;$d++)
        {
            if( $hLarge == $this->createdGames[$d]['home_id'] )
            {                                           
                $alt = $this->createdGames[$d]['away_id'];  
                $foundAlts[$foundIndex]['team']=$alt;
                $foundAlts[$foundIndex]['type']='away';
                $foundAlts[$foundIndex]['game']=$d;
                $foundIndex++;
            }
            else if( $hSmall ==  $this->createdGames[$d]['away_id'])
            {
                $alt = $this->createdGames[$d]['home_id'] ;
                
                $foundAlts[$foundIndex]['team'] = $alt;
                $foundAlts[$foundIndex]['type'] = 'home';
                $foundAlts[$foundIndex]['game'] = $d;
                $foundIndex++;
            }//if neither found, just keep going

        }//end for loop 
        //now all potential alts have been found
        
        if($foundIndex==0)
        {
           // echo "!!!!no alts found, deep swap failed\n";
            return false;
        }
        for($f=0;$f<$foundIndex;$f++)
        {//fix one alternate team
        //then loop through all games again
        //this time looking for the other half of the pair    
                 
            for($d=0;$d<$gCount;$d++)
            {
            if($foundAlts[$f]['type'] == 'home' )
            {
                $alt = $foundAlts[$f]['team'];
                
                //then find a game suchthat: both
                //alt == away_id
                //hlarge == homeid
                
                if($hLarge == $this->createdGames[$d]['home_id'] 
                   && $alt == $this->createdGames[$d]['away_id']  )
                {//success     
                    $gameid = $foundAlts[$f]['game'];
                  //  echo "attempt at games with $alt : $hSmall, and  $hLarge : $alt\n";               
                    $this->swap_teams($gameid);
                    $this->swap_teams($d);                    
                   // echo "deep swap success\n";        
                    return true;     //stop as soon as we find a match and swap it              
                }//else keep looking                                        
            }//ENDIF
            if($foundAlts[$f]['type'] == 'away' )
            {
                $alt = $foundAlts[$f]['team'];
                
                //then find a game suchthat:both
               // alt == homeid
               // hsmall == awayid
               
               if($hSmall == $this->createdGames[$d]['away_id'] 
                   && $alt == $this->createdGames[$d]['home_id']  )
                {//success
                    $gameid = $foundAlts[$f]['game'];
                   // echo "attempt at games with $alt : $hSmall, and  $hLarge : $alt\n";
                    $this->swap_teams($gameid);
                    $this->swap_teams($d);
                
                   // echo "deep swap success \n";        
                    return true;                                        
                }//else keep looking                                       
            }//ENDIF     
            }//end inner for loop                       
        }//end second for loop        
    
        //echo "!!!!!!!!!!nothing swapped in deep_swap!!!!!!!!!!\n";
        return false;
  
    }
    

    
    /**
    * @author sam bassett
    * @access private
    * determines yes or no if team has any games in this range
    * 
    * @param mixed $team_id
    * @param mixed $first_ts
    * @param mixed $second_ts
    * @param int $d_ts : the date of the game beingchecked
    * @return bool
    */
    private function are_any_team_games_in_tsrange($homeid,$awayid,$first_ts,$second_ts,$d_ts)
    {
    	if(!isset($this->teamMustAvoidSlot[$homeid])) $this->teamMustAvoidSlot[$homeid]=array();
    	if(!isset($this->teamMustAvoidSlot[$awayid])) $this->teamMustAvoidSlot[$awayid]=array();
 
    	
    	$found=false;
    	// echo "\nare_any_team_games_in_tsrange ";echo $this->teamName[$homeid];echo $this->teamName[$awayid]." ".date('Y-m-d',$d_ts). "\n"; 
    	 if(!isset($this->gamesOnDate[$d_ts]) ) 
    	 {
    	 	 //no games
    		 $this->gamesOnDate[$d_ts]=array();
			$found= false;
			 
    	 }
    	// echo count($this->gamesOnDate[$d_ts])." games exist today\n";
    	foreach($this->gamesOnDate[$d_ts] as $g_index)//used to loop on all games: works ok but too slow
		{ 
			$g=$this->createdGames[$g_index];
			$t_index=$g['timeslot'];
			//echo "t_index=".$t_index;
			if($t_index<0) continue;//only check booked games		
 
			//if($this->debug)echo $homeid.", $awayid vs".$g['home_id'].", ".$g['away_id']."";
			//only check games for this team. chnaged to direct, no in array
			if($homeid == $g['home_id'] || $homeid == $g['away_id']
		    || $awayid == $g['home_id'] || $awayid == $g['away_id']) 
		    {
				//echo "OK TEAM IS GOOD";
 
				$game_start = $g['start_timestamp'];
				$game_end   = $g['end_timestamp'];
				
				//oh no is overlap found
				if( $first_ts <=$game_start && $game_start< $second_ts)
					$found= true;
				
				if( $first_ts < $game_end && $game_end <= $second_ts)
					$found= true;
				
 
				if($found)
				{ //avoid this timeslot
				//echo "add team id to avoid";var_dump($team_id); var_dump($t_index);
					$this->teamMustAvoidSlot[$homeid][$t_index]=true;//true meansconflict found, avoid this slot. false means its ok, do not avoid	
					$this->teamMustAvoidSlot[$awayid][$t_index]=true;//true meansconflict found, avoid this slot. false means its ok, do not avoid	
					break; //shortcut
				}
			}
			//else echo "DO NOT MATCH";// since teams not playing in this game anyway
		}
 		
		return $found;
		
		
    }
        //given a timeslot, 
    
    
 
    /**
    * given the indices of two games from createdGames array in wizard
    * return true if the games are on the same day AND /adjacent/ (global rules time btw min)
    * AND if they have at least ONE team in common
    * 
    * idea is that ts_a is the timeslot for g_a, same for b
    * does not require thta the games are already scheduled, just that they could be
    * for example , this is often used where one game is scheduled already, 
    * but the other is not , and that is a proposed timesslot
    * 
    * false otherwise
    * 
    * @param mixed $gidx_a
    * @param mixed $gidx_b
    */
    private function are_games_adjacent_and_teamoverlap($gidx_a,$ts_a,$gidx_b,$ts_b,$ignoreTeamOverlap=false)
    {
 
		 
		if($this->timeslots[$ts_a]['date_timestamp'] == $this->timeslots[$ts_b]['date_timestamp'])
		{
			
			//they rae on the same date, check teams
			if(
				/*$a_homeid == $b_homeid || 
				$a_awayid == $b_awayid || 
				$a_homeid == $b_awayid || 
				$a_awayid == $b_homeid  */
				//if we are told to ignore teams, OR if they have at least one team in common
				$ignoreTeamOverlap || $this->countTeamsInCommon($gidx_a,$gidx_b) > 0
				
				 )
			{
				//now we return if they are adjacent
				//in terms of overlap of times
				//caluclate the rules times
				
				$this_startts = $this->timeslots[$ts_a]['start'];
				$this_endts   = $this->timeslots[$ts_a]['end'];
 
				$between=$this->globalRules['min_btw'];
				$between_min = $this->timeToMinutes($between);
				//stretch out this ts with teh betweent ime: then we c can compare exacts
				$this_startts = $this->addTime($this_startts,(-1)*$between_min);
				$this_endts   = $this->addTime($this_endts  ,$between_min);
				
				//if($this->debug) echo "  team overlap ";
				
				$cur_startts = $this->timeslots[$ts_b]['start'];
				$cur_endts   = $this->timeslots[$ts_b]['end'  ];
				
				// are they adjacent or not? 
				if(  $this_endts  == $cur_startts 
				  || $this_startts== $cur_endts   )
				{
					return true;
				}
				
			}
		}
		return false;
    }
     
    /**
    * 
    * return TRUE if theres a conflict
    * should only be called if facility_lock is set to true, then this will be called
    * 
    * first: get the facility of the given timeslot
    * then check all facs DIFFERENT from that facility
    * in each of those different facs, 
    * is there an adjacent game with a same team ? if yes, return true for this would cause a fac swap conflict
    * 
    * 
    * 
    * @param mixed $t_index
    * @param mixed $g_index
    */
    private function find_facility_lock_conflict($t_index,$g_index)
    {
		$facid   = $this->timeslots[$t_index]['facility_id'];
    	
  
		///for each OTHER facility than this one,
		foreach($this->usedFacIds as $otherfac)
		{
			if($otherfac == $facid){continue;}
 			//check the games on those other facs
			foreach($this->gamesOnFacility[$otherfac] as $gidx)
			{
				//cannot conflcit with myself , dont check
				if($gidx==$g_index) { continue; }
				
				//if($this->debug)echo " \ncmp to g$gidx ";
				
				$curr_ts=$this->createdGames[$gidx]['timeslot'];
				 
				if($curr_ts < 0 ){ continue; }//this is a floating game, ignore
 				
 				//now that we have a booked game to compare with, that is on a different timeslot, see if it has overlap:
 				
				//if the games are adjacent with a team overlap, then yes theres a convlict
				if($this->are_games_adjacent_and_teamoverlap($gidx,$curr_ts,$g_index,$t_index)) {return true;}
				//else keep searching
 
			}
		}
		//echo "fac lock TRUE\n";
		return false;
    }
    /**
    * @author Sam
    * find out if there is a conflict based on venue distance max rule
    *  if we try to schedule this game here
    * @param mixed $t_index
    * @param mixed $g_index
    */
    private function find_venue_distance_conflict($t_index,$g_index)
    {
 	
    	$vid = $this->timeslots[$t_index]['venue_id'];
    	
		foreach($this->createdGames as $gidx=>$g)
		{	
			$other_ts = $g['timeslot'];
			//if($this->debug)echo "???? CMP ? t$other_ts g$gidx";
			if($other_ts<0) {continue;}
			
			//if($this->debug)echo "   cmp g$gidx";
			if($this->are_games_adjacent_and_teamoverlap($g_index,$t_index,$gidx,$other_ts))
			{
				//ok they are adjacent now what
				if($this->are_venues_beyond_max_distance($vid,$g['venue_id']))
				{//yes, theyu are beyond max. this is a conflcit so stop now
				  // if($this->debug) echo "=TRUE(DONOTUSE)";
				   
					return true;
				}
				//else keep looking for other confl
			}
			
			
		}
		//if($this->debug) echo "=FALSE ( safe)";
		return false;
    }
	/**
	* @author Sam
	* returns true if distance between venues is > max
	* otherwise false 
	* uses venueDistanceMatrix and globalRules['venue_distance']
	* 
	* @param mixed $vid
	* @param mixed $other_vid
	* @return bool
	*/
    private function are_venues_beyond_max_distance($vid,$other_vid)
    {
    	if($this->debug)echo "\nare_venues_beyond_max_distance($vid,$other_vid): ";
		$max = $this->globalRules['venue_distance'];
		//$this->schedule_model->s_get_global_rules()
		$dist=$this->venueDistanceMatrix[$vid][$other_vid];
		if($this->debug)echo "return $dist > $max ";
		$beyond =   ($dist > $max) ? true : false;
		
		//we coudl just return the( > ) thing but this is more readable i think
		
		return $beyond;
		
    }
    
    

	/**
	* This is the MAIN function,   that 
	* avoids game conflicts
	* EVERY single time you want to assign a game to a timeslot, use this first
	* used along with find_timeslot very often
	*     
	* @param mixed $ts
	* @param mixed $g_index
	*/
    private function is_game_timeslot_safe($ts,$g_index,$check_in_use = true)
    {
    	$r_date=null;$r_dateset_pk=false;// depreciated varaibles
    	
    	//only check this if we are told to 
         if($check_in_use && $this->timeslots[$ts]['game_id']>=0) 
         {
         	  if($this->debug) echo "timeslot already used";
         	 return false;//a game is already sitting here
         }
 
		 $homeid = $this->createdGames[$g_index]['home_id'];
		 $awayid = $this->createdGames[$g_index]['away_id'];
		 
		 if($homeid==$awayid)
		 {
		 	 //should not happen unless create_matches breaks down. but if it does, dont book this game regardless
			 if($this->debug) echo "!#!# error home == away at game index $g_index  \n\n\n";
			 return false;
		 }
		 
		 /***********************did we avoid it before? then avoid it again*********/
		 
		 if(!isset($this->teamMustAvoidSlot[$awayid])) $this->teamMustAvoidSlot[$awayid]=array();
		 if(!isset($this->teamMustAvoidSlot[$homeid])) $this->teamMustAvoidSlot[$homeid]=array(); 
		 
		 if(!isset($this->teamMustAvoidSlot[$awayid][$ts])) $this->teamMustAvoidSlot[$awayid][$ts]=-1;
		 if(!isset($this->teamMustAvoidSlot[$homeid][$ts])) $this->teamMustAvoidSlot[$homeid][$ts]=-1;
		 
		 if($this->teamMustAvoidSlot[$homeid][$ts]===true || $this->teamMustAvoidSlot[$awayid][$ts]===true) 
		 {
 		  	if($this->debug)echo "\n\n!!!!!!!!!!!s!hortcut false from teamMustAvoidSlot ";
		 	 return false;
		 }
		 
		 
		 
		 $match_pk = $this->createdGames[$g_index]['match_pk'];
		 if($match_pk !== null && $this->rawMatches[$match_pk]['timeslots_allowed'] != null)
		 {
			 //means this timeslot is not allowed in this match, by user specificatoins
			 
			 if(!isset($this->rawMatches[$match_pk]['timeslots_allowed'][$ts])) 
			 {
			 	 if($this->debug) echo "timeslot match not allowed";
			 	 return false;
			 }
		 }
 
 		/*********************  Formatting, variables init  ************************/
 		
		 $ds = $this->timeslots[$ts]['dateset_pk'];
		  
		 $ds_rules=$this->tsRules[$ds];//$this->schedule_model->s_get_ds_rules($ds);
		 $gb_rules=$this->globalRules;//$this->schedule_model->s_get_global_rules();
		 
		 //so we have this buffer where each team cannot play doubles within another range
		 $buffer_max = ($ds_rules['is_active']=='f')? $gb_rules['max_btw'] : $ds_rules['ds_max_btw'];
		 if($buffer_max) $buffer_max=$this->timeToSeconds($buffer_max);
		 $buffer_min = ($ds_rules['is_active']=='f')? $gb_rules['min_btw'] : $ds_rules['ds_min_btw'];
		 if($buffer_min) $buffer_min=$this->timeToSeconds($buffer_min);
 
		 $homediv=$this->teamDivId[$homeid];
		 $awaydiv=$this->teamDivId[$awayid];
		// if(!is_numeric($homediv)) echo "??why is division null for team ".$homeid;
		 
        $d 			= $this->timeslots[$ts]['game_date'];
        $d_ts       = $this->timeslots[$ts]['date_timestamp'];//from strtotime
        $s 		    = $this->timeslots[$ts]['start'];
        $e 		    = $this->timeslots[$ts]['end']; 
        $set_name   = $this->timeslots[$ts]['set_name'];     
        $dateset_pk = $this->timeslots[$ts]['dateset_pk'];  
        $venue_id   = $this->timeslots[$ts]['venue_id'];  

        $start_ts   = $this->timeslots[$ts]['start_timestamp']; //both from strtotime
        $end_ts     = $this->timeslots[$ts]['end_timestamp']; 
           
        if(!isset($this->venueCount[$venue_id][$homeid]) ) $this->venueCount[$venue_id][$homeid]=0;
        if(!isset($this->venueCount[$venue_id][$awayid]) ) $this->venueCount[$venue_id][$awayid]=0;
        //min for both, just flat estimate
         
        if(!isset($this->dailyGames[$homeid][$d] ) ) $this->dailyGames[$homeid][$d] = 0 ;         
	              
	    if(!isset($this->dailyGames[$awayid][$d] ) ) $this->dailyGames[$awayid][$d] = 0;
	              
	    if(!isset($this->setGames[$dateset_pk]))          $this->setGames[$dateset_pk]=array();
                  
        if(!isset($this->setGames[$dateset_pk][$homeid])) $this->setGames[$dateset_pk][$homeid]=0;
                  
        if(!isset($this->setGames[$dateset_pk][$awayid])) $this->setGames[$dateset_pk][$awayid]=0;
                  
        /*************************** check mmax per day rules for this day & dateset ***************/          
        //$this->debug=true;
        if($ds_rules['is_active'] === true || $ds_rules['is_active'] == 't')
        {
			$home_played = $this->dailyGames[$homeid][$d];
			$away_played = $this->dailyGames[$awayid][$d];
			$max         = $ds_rules['max_day'];
			//echo "team ".$this->teamName[$homeid]." has played $home_played of $max on $d \n";
			
			//if either teams is over their max allowed for this day, flag timeslot as unsafe and return false
			if($max)
			{
				
				
				if($home_played >= $max)
				{
					$this->teamMustAvoidSlot[$homeid][$ts] = true;
					if($this->debug) echo "teamMustAvoidSlot1";
					return false;
				}
				if($away_played >= $max)
				{
					$this->teamMustAvoidSlot[$awayid][$ts] = true;
					if($this->debug) echo "teamMustAvoidSlot2";
					return false;
				}
			}
			//same deal with timeslot minmax's
			$home_set_played = $this->setGames[$dateset_pk][$homeid];
			$away_set_played = $this->setGames[$dateset_pk][$awayid];
			$max_set = $ds_rules['max_slot'];
			
			if($max_set)
			{
				
			
				if($home_set_played >= $max_set)
				{
					$this->teamMustAvoidSlot[$homeid][$ts] = true;
					if($this->debug) echo "teamMustAvoidSlot3";
					return false;
				}
				if($away_set_played >= $max_set)
				{
					$this->teamMustAvoidSlot[$awayid][$ts] = true;
					if($this->debug) echo "teamMustAvoidSlot4";
					return false;
				}
			}
			
        }
        
                  
                  
	    
	    /******************* facility / venue rules *****************/
	    
	    if($gb_rules['facility_lock']===true)
	    {
			 //if theres a conflict , this is NOT safe, return false
 			if($this->find_facility_lock_conflict($ts,$g_index))
 			{ 
 				
				if($this->debug) echo "facility_lock_conflict";
 				return false; 
 			
			}
			//otherwise no facloc conflict , keep looking for other types
	    }
	    // now check venue distance
	    
	    
	    //if conflict with adj venues distances false
	    
	    if($this->find_venue_distance_conflict($ts,$g_index)) 
	    {
	    	if($this->debug) echo "venue_distance_conflict";
	    	return false;
	    }
	    //else keep looking
	    
	    
		//****************** Timeslot/dateset Rules ***********************/
		
	    $teams=array($homeid,$awayid);
        if($r_dateset_pk && $r_dateset_pk!=$dateset_pk)
        {
 
	    	if($this->debug) echo "filter skip this dateset";
			return false;
        }
        if($r_date && $r_date!=$d)
        {   //r date might be null
			//if($this->debug)echo " !filter skip this dateset $r_date vs $d ";
			return false;
        }
        
        //if this timeslot is not used already:
 
		if($buffer_min !=0 )
		{
			//if($this->debug)echo "!!!using buffers now at  $buffer_min; seconds minimum,so check nearby games";
			$range_start = $start_ts-$buffer_min;
			$range_end = $end_ts+$buffer_min;
							
		
			if( $this->are_any_team_games_in_tsrange($homeid,$awayid,$range_start,$range_end,$d_ts) )
			{	  	
 
	    		if($this->debug) echo "failed are_any_team_games_in_tsrange";
 				return false;//we cannot break the buffer rule
			} 
			//else check other team or just keep going for other rules
			
			//if($this->debug)echo "are_any_team_games_in_tsrange: safe ";
			

		}
		//*********************  Match rules  **************************/
 
	     
	    
	    if($match_pk !== null)$match=$this->rawMatches[$match_pk]; 
	    else $match = array('enforce_dates'=>'f');//simulate
	    
	    if( $match['enforce_dates']=='t'||$match['enforce_dates']=='true')
	    {
 
			$in_array=false;
 
			//echo 'enforce dates was set, working on '.$d.' pk '.$match_pk;
			//stlil possible for a nulld ate set with enforce set TRUE. in which case turn undefined into empty set
			$allowed_stamps = isset($match['date_stamps']) ?  $match['date_stamps'] : array();
			
			$in_array=isset($allowed_stamps[$d_ts]);
 
		 
			if(!$in_array)//if not in _array
			{
 
	    		if($this->debug) echo "failed match enforce_dates";
				return false;
			}//else proceed to other conflict checks
			
	    }
	    else//$match['enforce_dates']=='f' or undefined
	    {
			//echo "enforce dates is false, moving on\n";
	    }

	    $homeconflict=false;
		$awayconflict=false;//assume good unless we find problem
		$divrules_active=false;//set false for ext js 2.0
		
		//*****************************Division Rules (depreciated)*********************/
		if($divrules_active)
		{
	    //get the rules arrays
            $home_rules = $this->schedule_model->s_get_div_rules($homediv,$dateset_pk);
            $away_rules = $this->schedule_model->s_get_div_rules($awaydiv,$dateset_pk);
            //FORMAT =array('minset'=>$minset,'maxset'=>$maxset,'minperteam'=>$minperteam,'maxperteam'=>$maxperteam);

			//now that we have rules ready, we check for conflicts
            
				        
			///first check PER DAY maximum

			$home_max=0;
	        if(array_key_exists('maxperteam',$home_rules))
                $home_max=$home_rules['maxperteam'];
	        if($home_max == '' || $home_max==null)
	            $home_max=0;
	        $away_max=0;   
	        if(array_key_exists('maxperteam',$away_rules))
                $away_max=$away_rules['maxperteam'];
	        if($away_max == '' || $away_max==null)
	            $away_max=0;    
	            
			$home_played = $this->dailyGames[$homeid][$d];
			$away_played = $this->dailyGames[$awayid][$d];

			if($home_max != 0 && $home_played != 0)
				//echo "team $homeid played $home_played vs MAX $home_max on $d from ds $dateset_pk div $homediv\n";
			if($away_max != 0 && $away_played != 0)
				//echo "team $awayid played $away_played vs MAX $away_max on $d from ds $dateset_pk div $awaydiv\n";
				
	        if(  $home_max != 0 && $home_played >= $home_max )                	
                $homeconflict=true;  
			
	        if(  $away_max != 0 && $away_played >= $away_max )         
	            $awayconflict=true;
	              
	        if($homeconflict || $awayconflict) continue;//skip to the next ts anytime we find a conflict
	        
	        //now check PER SET maximum (copy paste of per day check with minor changes) 

	        $home_max=0;
	        if(array_key_exists('maxset',$home_rules))
                $home_max=$home_rules['maxset'];
	        if($home_max == '' || $home_max==null)
	            $home_max=0;
	        $away_max=0;   
	        if(array_key_exists('maxset',$away_rules))
                $away_max=$away_rules['maxset'];
	        if($away_max == '' || $away_max==null)
	            $away_max=0;    
	        
			
			$home_played = $this->setGames[$dateset_pk][$homeid];
			$away_played = $this->setGames[$dateset_pk][$awayid];

	        if(  $home_max != 0 && $home_played >= $home_max )                	
                $homeconflict=true;  
			
	        if(  $away_max != 0 && $away_played >= $away_max )         
	            $awayconflict=true;
	            
	        if($homeconflict || $awayconflict) 
	        	{/*if($this->debug)echo "return false for game conflict 1";*/ return false;   }
        }
           //if no problem yet, next we check vs existing games
          // echo "check game_conflict\n";
          
          ///****************************Game Conflicts **********/
      if($check_in_use)
      {
      	  //new : only check if flag (defaults to true) is set
	        $homeconflict = $this->game_conflict($homeid,$homediv,$d,$s,$e);
	            
	        if($homeconflict || $awayconflict)
	        {
        		 
	    		if($this->debug) echo "failed HOME game_conflict";
        		 return false;
	        }
	                      
	        $awayconflict =  $this->game_conflict($awayid,$awaydiv,$d,$s,$e);  
	              
	        if($homeconflict || $awayconflict)
	        {
	    		if($this->debug) echo "failed AWAY game_conflict";
        		 
        		return false;    
	        }
	  }
   
		return true;
    }
    


    
    
    
    
        
    /**
    * argument is an index for the global createdGames array
    * this fn will swap the home team with the away team.
    * ignoring all time conflicts -- assume this is only run before timeslots are assigned
    * @param int $gameIndex
    */
    private function swap_teams($gameIndex)
    {

      //  echo "beforeswap"; 
        $homeid = $this->createdGames[$gameIndex]["home_id"];        
        $homename=$this->createdGames[$gameIndex]["home_name"];
        $homediv= $this->createdGames[$gameIndex]["home_div"];
        
        $awayid = $this->createdGames[$gameIndex]["away_id"];
        $awaydiv= $this->createdGames[$gameIndex]["away_div"];
        $awayname=$this->createdGames[$gameIndex]["away_name"];
        
        //make sure to change counters
        //the so called away team is gaining a home game and loosing an away game
        $this->homeGames[$awayid] = $this->homeGames[$awayid]+1;
        $this->awayGames[$awayid] = $this->awayGames[$awayid]-1;
        //opposite thing for the 'home team' will be playing away
        $this->homeGames[$homeid] = $this->homeGames[$homeid]-1;
        $this->awayGames[$homeid] = $this->awayGames[$homeid]+1;
 
        $this->createdGames[$gameIndex]["home_id"]   = $awayid;
        $this->createdGames[$gameIndex]["home_div"]  = $awaydiv;
        $this->createdGames[$gameIndex]["home_name"] = $awayname;
        
        $this->createdGames[$gameIndex]["away_id"]   = $homeid;
        $this->createdGames[$gameIndex]["away_div"]  = $homediv;
        $this->createdGames[$gameIndex]["away_name"] = $homename;
        //echo "swap teams success at game $gameIndex\n";
    }//end of swap_teams()
    	 
    	 
     /**
     * @deprecated not implemented yet
     * 
     * 
	 *          we want to return zero if any of the following happen
     *   1.this divid has NO PREFERENCES, taht is if this.venPrefs[divid] not exist or its count ==0;
     *    this is possible, many divisions may have no prefs
     *   2.this venue has no stats, taht is if this.venueStats[venueid] somehow undefined 
     *   empty, or length zero
     *   while 2. should not happen, assuming database is filled in, this is just good error checking
	 * 
	 * @param mixed $teamid
	 * @param mixed $divid
	 * @param mixed $venueid
	 * @return mixed
	 */
    private function quality_measure($teamid,$divid,$venueid)
    {
 

        if(!isset($this->venueStats[$venueid]) || is_null($this->venueStats[$venueid]))
             return 0;//if undefined for this venue
       // var_dump($this->venuePrefs[$divid]);
        if(!isset($this->venuePrefs[$divid]) || !isset($this->venuePrefs[$divid]['saved']) ||  !$this->venuePrefs[$divid]['saved'] )
            return 0; //array is defined but nothing is saved
        
        //positive is a good rating
        //negative is bad
        $pass = 1;
        $fail = -1;//these could change      
        //multilpliers will get either pass or fail, depending whether or not criteria is met
        $mulBench=0;
        $mulFence=0;
        $mulField=0;
        $mulLight=0;
        $mulSeat =0;
        
        //values that this div WANTS to see
        $valBench = $this->venuePrefs[$divid]['benchVal'];
        $valFence = (int) $this->venuePrefs[$divid]['fencYAHOO.lang.JSON.parse'];//number
        $valField = $this->venuePrefs[$divid]['fieldVal'];
        $valLight = $this->venuePrefs[$divid]['lightVal'];
        $valSeat  = (int) $this->venuePrefs[$divid]['seatVal'];//number
        
        $priBench = $this->venuePrefs[$divid]['benchPri'];
        $priFence = $this->venuePrefs[$divid]['fencePri'];
        $priField = $this->venuePrefs[$divid]['fieldPri'];
        $priLight = $this->venuePrefs[$divid]['lightPri'];
        $priSeat  = $this->venuePrefs[$divid]['seatPri'];

        //if priority blank , multiplier will stay at zero
        if($priBench != 'blank')
        {
            //bench is not nummeric, find out if the striings match
            if( $valBench == $this->venueStats[$venueid]['benchVal'])
                $mulBench = $pass;
            else
                $mulBench = $fail;                        
        }//if it was blank, multiplier stays zero        
        if($priFence != 'blank')
        {  //fence is numeric, so compare
            if( $valFence <= $this->venueStats[$venueid]['fencYAHOO.lang.JSON.parse'])
                $mulFence = $pass;
            else
                $mulFence = $fail;            
        }        
        if($priField != 'blank')
        {
            if( $valField == $this->venueStats[$venueid]['fieldVal'])
                $mulField = $pass;
            else
                $mulField = $fail;                        
        }        
        if($priLight!= 'blank')
        {// not nomeric, strings muts match
            if( $valLight == $this->venueStats[$venueid]['lightVal'])
                $mulLight = $pass;
            else
                $mulLight = $fail;                            
        }        
        if($priSeat != 'blank')
        {
            //numeric not string
            if( $valSeat <= $this->venueStats[$venueid]['seatVal'])
                $mulSeat = $pass;
            else
                $mulSeat = $fail;            
        }
    
        $rating = $this->qRating[$priBench]*$mulBench + 
                  $this->qRating[$priFence]*$mulFence + 
                  $this->qRating[$priField]*$mulField + 
                  $this->qRating[$priLight]*$mulLight + 
                  $this->qRating[$priSeat] *$mulSeat ;
                     
        //echo "found a team rating   $rating \n";
        return $rating;
    }//end function quality_measure
        
 
  	/**
  	* @access private
  	* @author sam bassett
  	* sub procedure for use inside the validation step of wizard
  	* attempts to assign games that were left out 
  	* during main alg
  	* 
  	*/
  	private function _validate_unassigned_games()
  	{
		$floating=$this->_gather_unassigned_games();
		
		if(count($floating) ==0) return;
		
		$open = $this->_gather_unused_timeslots();
 
		
		if($this->debug)
		{
			echo "\nvalidate_unassigned_games()";
			echo " Floating games: ".count($floating);

			echo " open ts: ".count($open)."\n";
		}


		//first try it without any extra shuffling
		//this will probably do nothing, as any game that can be added here
		//should have already beend one by the first run through, 
		//but check it anyway
		//level zero
		foreach($floating as $g_index)
    	{
    		//$g_index=$game['g_index']; 
    		
    		foreach($open as $t_index)
    		{
				//$t_index=$slot['t_index'];
				//if($this->debug)echo "\ntest an open slot at ".date('Y-m-d g:i a',$this->timeslots[$t_index]['start_timestamp']);
				if($this->is_game_timeslot_safe($t_index , $g_index))
				{
 
					$this->assign_game_timeslot($t_index,$g_index); 
				}
 
				//else just keep looking
    		}
    	}
    	
    	
    	if($this->debug)
    	{
			$new_floating=$this->_gather_unassigned_games();
			
			 
			
			$new_open = $this->_gather_unused_timeslots();
			echo "\nEND OF validate_unassigned_games()";
			echo " NEW Floating games: ".count($new_floating);

			echo "NEW open ts: ".count($new_open)."\n";
    	}
	}
		
	private $recursive = 30;	
	/**
	* avoid team conflcits after scheduling is complete and adds extra games eve nif it 
	* APPEARS that no slots are open. call this AFTER _validate_unassigned_games , which is the basic version
	* 
	* UPDATE: this appears to not work or have some missing cases nto handled: task 1774
	* 
	* 
	*/
	private function _validate_team_conflicts($loop=0)
	{
		$this->debug = false;
		if($loop >= $this->recursive)
		{
			if($this->_force_push_break)
			{
				//it was not completed with regular force_push , so try a different approach
				if($this->debug )echo "\n\n INIT double push\n";
			}
			else return;
		}
		//only debug final loop
		//if($loop == $this->recursive-1 + 1)$this->debug = true;//was -1
		
		if($this->debug)echo "\n************************************  loop = $loop;";
    	$floating = $this->_gather_unassigned_games();
		if(count($floating) ==0) return;
 
 		//for each start_timestamp, have 
 		 
 		$stamp_candidates = array();//array of all timestamps that have slots
		$stamp_free_teams = array();//a sub array of teams that are NOT playing on that timestamp
		$stamp_games      = array();//also array of the games saved to that ts
		 
		$open = $this->_gather_unused_timeslots(); 
    	foreach($open as $t_index)
    	{
			$start = $this->timeslots[$t_index]['start_timestamp'];
  
			$unbooked     = $this->_unused_timeslots_on_date_time($t_index);
			if(count($unbooked)==0)continue;
			 
			if(!isset($stamp_candidates[$start])) $stamp_candidates[$start] = array();
			if(!isset($stamp_free_teams[$start])) $stamp_free_teams[$start] = array();
			if(!isset($stamp_games[$start]))      $stamp_games[$start] = array();
			
			$stamp_candidates[$start][] = $t_index;
			
			$booked_games = $this->_games_on_start_ts($start);
			if($this->debug) echo "\n INITIAL  ".date("M d, Y  g:i a",$start)." with ".count($booked_games)."  games : ";//   ".count($unbooked)." open \n";
			 
			//so which teams prevented this game being added to this slot
 
			$booked_teams = $this->_team_ids_in_game_array($booked_games);
			foreach($this->teamName as $tid=>$tname)
			{
				//if this team is a a team in a BOOKED
				if(isset($booked_teams[$tid]))
				{
					if($this->debug)echo " $tname  , ";
				}
				else
				{
					if($this->debug)echo "<$tname> , ";
					$stamp_free_teams[$start][$tid] = $tid;
				}
				
			}
			foreach($booked_games as $bg)
			{
				$stamp_games[$start][$bg['g_index']] = $bg['g_index'];
			}

		 	//if($this->debug)echo "\nstamp_games has ".count($stamp_games[$start])." stamp_free_teams has ".count($stamp_free_teams[$start]);
 
		}
		//unsets are not Technically necessary, but i was accidentaly using these variables again thinking they are new but they had old values
		//so to be safe. also POSSIBLY a memory saver?
		unset($tname);
		unset($tid);
		unset($start);
		unset($t_index);
		unset($booked_teams);
		unset($unbooked);
		unset($current_game_teams);
		
		/*******************************    data gathering complete, start algorithm  *******************/
		
		
 		$pushed_games = null;
		if($this->debug) echo "\nFLOATING(".count($floating).")  ";
		foreach($floating as $f=>$g_index)
		{
			if($this->debug)echo "   <". $this->createdGames[$g_index]['home_name'].":".$this->createdGames[$g_index]['away_name'].">";
			
			$current_game_teams = $this->_team_ids_in_game_array(array($this->createdGames[$g_index]));
			foreach($stamp_candidates as $start_ts => $t_index_array)
			{
				$team_conflicts = 0;
				foreach($stamp_free_teams[$start_ts] as $tid)
				{
					 
					if(isset($current_game_teams[$tid]))  $team_conflicts++; 
 
				}
	 			//if($this->debug)var_dump($team_conflicts);
				if($team_conflicts == 2) 
				{
					
					if($this->debug)echo "VICTORY at LEVEL SIMPLE  !!! ";
    				$pushed_games = array();
    				$pushed_games['rm'] = array();
	 
	 
					$pushed_games['add'][] = array('t'=>$t_index_array,'g'=>$g_index);
	 
				}
			//else echo "simple fail so move on";
			
			}
		}
		
		/******************************************** Force it in phase: look for half matches ****************************/
 
		if($pushed_games == null) //foreach($floating as $f=>$g_index)
		{
			//echo "<". $this->createdGames[$g_index]['home_name']." : ".$this->createdGames[$g_index]['away_name']."> ";
			
			
			if($this->_force_push_break == false)
			{ 
				$pushed_games = $this->_force_push($loop,   $floating[0]                  ,$stamp_candidates,$stamp_free_teams,$stamp_games);	
			}
			else
			{
				$this->_force_push_break = false;
				
				if(count($floating) < 2) $pushed_games = false;
				else
				{
						//double push has two cases to check
						$pushed_games = $this->_double_push($loop,array($floating[0],$floating[1]),$stamp_candidates,$stamp_free_teams,$stamp_games,false);	
				    if($pushed_games === false )
						$pushed_games = $this->_double_push($loop,array($floating[0],$floating[1]),$stamp_candidates,$stamp_free_teams,$stamp_games,true);	
						//the two games play completely different roles in that algorithm, thats why theo rder matters: it doesnt loop on tehm
					if($pushed_games === false )
						$pushed_games = $this->_double_push($loop,array($floating[1],$floating[0]),$stamp_candidates,$stamp_free_teams,$stamp_games,false);	
					if($pushed_games === false )
						$pushed_games = $this->_double_push($loop,array($floating[1],$floating[0]),$stamp_candidates,$stamp_free_teams,$stamp_games,true);	
					
				}
			}
		}
				
		if($pushed_games)
		{
			//could have came from simple or not simple
			
			if($this->debug)echo 'force push win ! commit all transactoins'."\n\n";
			 

			 
			foreach($pushed_games['rm'] as $combo)
			{
				$t_index = $combo['t'];
				$game_id = $combo['g'];
				
				//echo "this->un_assign_game_timeslot($t_index,$game_id)";
				
				//for now just do first step: REAL solution is to always do this
				 

				$this->un_assign_game_timeslot($t_index,$game_id);
				  
			}
			foreach($pushed_games['add'] as $combo)
			{
				$t_index = $combo['t'];
				$game_id = $combo['g'];
				
				//new: should never be array, now we are using is timeslot safe ni the other end, and rejecting if unsafe
				//otherwise return the exact safe one
				$t_index = is_array($t_index) ? $t_index[0] : $t_index;
				//echo "assign_game_timeslot($t_index,$game_id)";
				$this->assign_game_timeslot($t_index,$game_id);
				 
			}
	 
 
		}	
	 
		
		
		$this->_validate_team_conflicts($loop+1);
  	}
  	
  	
  	
  	/**
  	* new phase for task 1774
  	* see _force_push
  	* 
  	* @param mixed $breakloop
  	* @param mixed $g_index_array
  	* @param mixed $stamp_candidates
  	* @param mixed $stamp_free_teams
  	* @param mixed $stamp_games
  	* @param mixed $swap
  	*/
  	private function _double_push($breakloop,$g_index_array,$stamp_candidates,$stamp_free_teams,$stamp_games,$swap=false)
  	{
  		 
  		if($this->debug )echo "\n _double_push\n";
  		//our two floating games
		$g_index_left = $g_index_array[0];
		#TEST ONLY: swap left H and left A
		$left_h  = $this->createdGames[$g_index_left]['home_id'];
		$left_a  = $this->createdGames[$g_index_left]['away_id'];
		
		
		$g_index_rght = $g_index_array[1];
		$right_h = $this->createdGames[$g_index_rght]['home_id'];
		$right_a = $this->createdGames[$g_index_rght]['away_id'];
		 
		if($this->debug )echo "LEFT <".$this->teamName[$left_h].$this->teamName[$left_a].">   RIGHT  <".$this->teamName[$right_h].$this->teamName[$right_a]."> \;n";
		
		
		$tm_need = null;
		$tm_need_pair = null;//look for specific teams
		$tm_find_pair = null;//look for specific teams
		$tm_stay = null;
		$gm_need = null;//the game for those teams
		/**************************  types to work on : game left for each slot, and game right for each slot*************/
		$stamps = array_keys($stamp_candidates);
		if(!$swap)
		{
			$start_ts = $stamps[0];
			$other_ts = $stamps[1];
		}
		else
		{
			$start_ts = $stamps[1];
			$other_ts = $stamps[0];
		}
		 
 			//first do left game
		 
		foreach($stamp_games[$start_ts] as $g_id)
		{
			$home = $this->createdGames[$g_id]['home_id'];
			$away = $this->createdGames[$g_id]['away_id'];
			 //is tehre a better way to do this? check all four crossways cases
			if($home == $left_h) 
			{
				$tm_need = $home;
				$tm_need_pair = $away;
				$gm_need = $g_id;
				$tm_stay = $left_a;
			}
			else if($away == $left_h) 
			{
				$tm_need = $away;
				$tm_need_pair = $home;
				$gm_need = $g_id;
				$tm_stay = $left_a;
			}
			else if($home == $left_a) 
			{
				$tm_need = $home;
				$tm_need_pair = $away;
				$gm_need = $g_id;
				$tm_stay = $left_h;
			}
			else if($away == $left_a) 
			{
				$tm_need = $away;
				$tm_need_pair = $home;
				$gm_need = $g_id;
				$tm_stay = $left_h;
			}
		}
		
		if($this->debug )echo "\nNEED($gm_need) <".$this->teamName[$tm_need].">   NEED_PAIR  <".$this->teamName[$tm_need_pair]."> STAY  <".$this->teamName[$tm_stay].">";
		foreach($stamp_free_teams[$start_ts] as $tid)
		{
			 if($tid != $tm_stay) $tm_find_pair = $tid;
		}
		if($this->debug )echo " FINDPAIR  <".$this->teamName[$tm_find_pair].">;\n";
		//which ever of 

		$ideal_one = null;
		$ideal_two = null;
		foreach($stamp_free_teams[$other_ts] as $tid)
		{
			 //if($tid != $tm_stay) $tm_find_pair = $tid;
			 if($ideal_one) $ideal_two = $tid;
			 else 			$ideal_one = $tid;
 
		}
		
		 
		if($this->debug )echo "IDEAL ONE: TWO   <".$this->teamName[$ideal_one]." :  ".$this->teamName[$ideal_two].">";
	 
		$spliced = $this->_split_games_by_index();
		 
		foreach($spliced as $start => $games) 
		{
			$found_ts = null;
			$found_gm_ideal = null;
			$found_gm_pair = null;
			if(!$start || $start < 0) {continue;}
			 
 
			$foundBoth = false;
			foreach($games as $g)
			{
				$this_game = $g['g_index']; 
				$this_ts   = $g['t_index']; 
				$hid = $g['home_id'];
				$aid = $g['away_id'];
				
				 if(( $hid == $ideal_one ||  $aid == $ideal_one  )&&
				    ( $hid == $ideal_two ||  $aid == $ideal_two  ))
				 {
					 
					 $found_gm_ideal = $this_game;
					 $found_ts_ideal = $this_ts;
					 if($this->debug)echo "foundideal ($found_gm_ideal)";
				 }
				 if(( $hid == $tm_need_pair ||  $aid == $tm_need_pair  )&&
				    ( $hid == $tm_find_pair ||  $aid == $tm_find_pair  ))
				 {
					 
					 $found_gm_pair = $this_game;
					 $found_ts_pair = $this_ts;
					 if($this->debug)echo "found_gm_pair ($found_gm_pair)";
				 }
				 $foundBoth = $found_gm_ideal && $found_gm_pair;
				 
				 if($foundBoth)
				{
					 
					break;
				}
 
			}
			if($foundBoth)break;
			
			  
		}
		 if(!$foundBoth)
		 {
			 return false;
		 }
		 else
		 {
 
			if($this->debug)echo "\n*********************** FOUNDBOTH  ".date("M d, Y  g:i a",$start);
			
			$old_gm_need = $this->createdGames[$gm_need]['t_index'];
			
			if(  !$this->is_game_timeslot_safe($found_ts_pair,$gm_need,false)   )       { return false;}
			if(  !$this->is_game_timeslot_safe($found_ts_ideal,$g_index_rght,false)   ) { return false;}
			if(  !$this->is_game_timeslot_safe($old_gm_need,$g_index_left ,false) )       { return false;}
			
 			//first check if all five 'add' 'timeslots are safe
 			
 			$new_ts_pair  = null;//LOOP ON THSE, FIND THE RIGHT ONE
 			$new_ts_ideal = null;
 			  
 			foreach($stamp_candidates [ $other_ts ] as $ts_test)
 			{ 
				if( $this->is_game_timeslot_safe($ts_test,$found_gm_ideal ,false) )   $new_ts_ideal = $ts_test;
 			}
 			foreach($stamp_candidates [ $start_ts ] as $ts_test)
 			{
				if( $this->is_game_timeslot_safe($ts_test,$found_gm_pair,false) ) {$new_ts_pair = $ts_test; continue;}
 			}
        
 			if(!$new_ts_pair ||  !$new_ts_ideal  )  {return false;}
    		$return = array();
    		$return['add'] = array();
    		$return['rm']  = array();
    		  
			
			$return['rm'][]  = array('t'=>$found_ts_pair ,'g'=>$found_gm_pair);
			$return['rm'][]  = array('t'=>$found_ts_ideal,'g'=>$found_gm_ideal);
			$return['rm'][]  = array('t'=>$old_gm_need,'g'=>$gm_need);
			
			$return['add'][] = array('t'=>$new_ts_pair,   'g'=>$found_gm_pair);//GOES TO START_TS
			$return['add'][] = array('t'=>$new_ts_ideal , 'g'=>$found_gm_ideal);//GOES TO OTHER_TS
			$return['add'][] = array('t'=>$found_ts_pair, 'g'=>$gm_need);//PAIR AND IDEAL ARE ON THE SAME DAY JUST DIFFERENT VENUES
			$return['add'][] = array('t'=>$found_ts_ideal,'g'=>$g_index_rght);//SO THEY COULD GO EITEHR WAY
			$return['add'][] = array('t'=>$old_gm_need,   'g'=>$g_index_left);//TO START_TS
			 
    		return $return;
		 }
 
		echo "\n\n";
  	}
  	
  	
  	
  	
  	
  	
	private $_force_push_break = false;
  	private function _force_push($breakloop,$g_index,$stamp_candidates,$stamp_free_teams,$stamp_games,$return=null,$dirty_stamp=null)
  	{ 
  		$this->debug = false;
  		if($breakloop > $this->recursive)
  		{
			if($this->debug )echo "BREAKLOOP FAILED";
			$this->_force_push_break = true;
			return false;
  		}
  		//var_dump($stamp_free_teams);
		$fl_game = $this->createdGames[$g_index];
		if($this->debug)
		{
			 echo "\n\n \$this->_force_push($g_index):  <". $fl_game['home_name']." : ".$fl_game['away_name'].">  ,dirty:".date("M d, Y  g:i a",$dirty_stamp)."\n";
 			 foreach($stamp_candidates as $start_ts => $t_index_array)
			{
 
				 
 				 echo "\nstamp_candidate   @ ".date("M d, Y  g:i a",$start_ts)."\n\n stamp_free_teams     [";
				foreach($stamp_free_teams[$start_ts] as $tid)
				{
					if($this->debug) echo $this->teamName[$tid].",";
				}
				echo "];\n\n stamp_games      [";
				foreach($stamp_games[$start_ts] as $g_id)
				{
					echo "<". $this->createdGames[$g_id]['home_name'].":";
					echo $this->createdGames[$g_id]['away_name'].">,";
				}
				echo "];\n";
			}
		}
  		 
		$current_game_teams = $this->_team_ids_in_game_array(array($fl_game));
    		
    	//if we do nto return false, we will return this array
    	//it tells which game commit transactions to acto n based on add/remove
    	if($return === null)
    	{ 	
    		$return = array();
    		$return['add'] = array();
    		$return['rm']  = array();
		}
		//for every floating game
    	//$allzero = true;
    	//visit each timeslot candidate once and look at playing with teams
		foreach($stamp_candidates as $start_ts => $t_index_array)
		{
			if($dirty_stamp == $start_ts)
			{
				if($this->debug) echo "\n\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!skip dirty_stamp\n\n";
				continue;
			}
			//count how many overlapping teams it has with the stamp_free_teams
			$team_conflicts = 0;
			$matching_team = null;
			$floating_team = null;
			 
			if($this->debug) echo "\n ...testing @ ".date("M d, Y  g:i a",$start_ts);
			foreach($stamp_free_teams[$start_ts] as $tid)
			{
				if($this->debug) echo "  <".$this->teamName[$tid].">";
				if(isset($current_game_teams[$tid])) 
				{
					$team_conflicts++;
					$matching_team = $tid;//if there are more than 1 team_conflicts, this wont matter if we overwrite, we arent using it
					//only if its 1 do we use matching_team
				}
				else
				{
					//as above, if team conflicts is not == 1 then we will enver use this
					//otherwise this has a unique value 
					$floating_team = $tid;
				}
			}
			
			if($this->debug) echo "; team_conflicts = $team_conflicts;";
			
			//if no overlap that means  both teams in this floating game are already booked in this slot, so 
			//there is little chance of managing this
			if($team_conflicts == 0) {continue;}
			//$allzero = false;
			if($team_conflicts == 2) 
			{
				/****************************   LEVEL 0 *******************/
				//perfect matching
				
				//then we win, hopefully this game can get assigned here
				//maybe it didnt because of some rules so check thsoe again before we try
 
				if($this->debug)echo "VICTORY at LEVEL 0  assuming we can find one  ";
				 
				foreach($t_index_array as $check_ts)
				{
					 
					if( $this->is_game_timeslot_safe($check_ts,$g_index,false)   ) 
					{ 
						# if it IS safe
						$return['add'][] = array('t'=>$check_ts,'g'=>$g_index);
						 
						return $return;	
					}
					
				}
  				 return false;
 		
			}
			//else check next level
			else if ($team_conflicts == 1)//could just use else here, but 1 is the only other option
			{
				/****************************   LEVEL 1 *******************/
				
				//now for the main algorithm
				//the matching_team is the team fromt he missing game taht is not booked at all on this timeslot
				//now we need to find conflicting_team
				if($this->debug)echo "\n--> matching_team ".$this->teamName[$matching_team];
				 
				// the array $current_game_teams has exactly 2 team ids in it, we need to find the one NOT equal to matching_team
				//but it is self indexed by id, not 0 , 1 , thats why we need array values
				$c_teams = array_values($current_game_teams);
				
				//just grab which ever team is different from matching_team
				$conflicting_team = ($c_teams[0] == $matching_team) ? $c_teams[1] : $c_teams[0];
				
				if($this->debug)echo ",conflicting_tm ".$this->teamName[$conflicting_team];
				
				//now , which game id did $conflicting_team come from,( and who was that teams opponent?)
				$pair_team = null;
				//go back to teh full list of game ids on this timestamp and look
				
				$moving_game_id =  null;
				 
				foreach($stamp_games[$start_ts] as $g_id)
				{
					$home = $this->createdGames[$g_id]['home_id'];
					$away = $this->createdGames[$g_id]['away_id'];
					
					if($conflicting_team == $home)
					{
						$pair_team = $away;
						$moving_game_id = $g_id;
						break;
					}
					if($conflicting_team == $away)
					{
						$pair_team = $home;
						$moving_game_id = $g_id;
						break;
					}
				}
				unset($g_id);
				if($this->debug)echo ",pair_team ".@$this->teamName[$pair_team];
			 
			 
				 
				//now take the two teams <conflicting , pair> and look for timeslot group  exactly those two teams open, where we can move the game there
 
				$moving_old_slot = @$this->createdGames[$moving_game_id]['t_index'];
				$moving_dirty    = @$this->createdGames[$moving_game_id]['start_timestamp'];
 
				if(!$moving_old_slot) {   continue;}
				if($this->debug)echo "\n add <".$this->createdGames[$g_index]['home_name'].":".$this->createdGames[$g_index]['away_name'].">   TO stamp_teams ";
				 if($this->debug)echo "\n REMOVE <".$this->createdGames[$moving_game_id]['home_name'].":".$this->createdGames[$moving_game_id]['away_name'].">   FROM stamp_teams ";
							
				 if($this->debug)echo "\n and from stamp_free_teamsadd team ".$this->teamName[$pair_team]." REMOVE team ".$this->teamName[$matching_team];				 
				$stamp_games[$start_ts][$g_index] = $g_index;
				unset($stamp_games[$start_ts][$moving_game_id]);
				
				$stamp_free_teams[$start_ts][$pair_team]=$pair_team;
 
				unset($stamp_free_teams[$start_ts][$matching_team]);//=$conflicting_team;
 				
 				//stop now if we arent allowed to move here
 				 
				if(  !$this->is_game_timeslot_safe($moving_old_slot,$g_index,false)   ) {    continue;}
				 
				$return['rm'][]  = array('t'=>$moving_old_slot,'g'=>$moving_game_id);
				
				$return['add'][] = array('t'=>$moving_old_slot,'g'=>$g_index);
				
				
				return $this->_force_push($breakloop+1,$moving_game_id,$stamp_candidates,$stamp_free_teams,$stamp_games,$return,$moving_dirty);		
 
				
			}
			
	
		}
 
		return false;
  	}
  	
    	
    /**
    * after games are all created, save stats and other info
    * to session for later calls
    * also format date and start time for nice display formats
    * 
    */
    public function audit_schedule()
    {
 		$audits=array();
        
        //$this->teamDivId
        //also store AVAILABLE timeslots
        $free_ts=array();
        if(!is_array($this->timeslots))
        {
			$this->timeslots=array();
			
        }
        foreach($this->timeslots as $ts)
        	if($ts['game_id']==-1)
        		$free_ts[]=$ts;
        	
        //instead we should loop through ACTUAL GAMES 
        
        //and hence recreate these arrays
        $audits['currentTimeslots'] = $free_ts;
        $stats = array();
        $row=0;
        
    	
       $teamMatchups=array();
       $full_fmt='D, M j Y';
       $short_fmt='M j, Y';
       $plain_fmt='Y-m-d';
       // $home_count=array();//hometeam->awayteam->count
       $away_count=array();//awayteam -> hometeam->count
		$div_match_count=array();
		$div_date_count=array();
		$div_venue_count=array();
		//reset these, before were used just for match balance
  		$this->awayGames=array();
        $this->homeGames=array();
        $date_stats = array();
        
        $venDistanceStats=array();
       // $venPairsUsed=array();
        
        /****************  venue distance stats ***************/
        $this->buildLinkedListGamseAdj();
 
 
    	$vvAdjGameCount=array();// for bb_games
    	$vvAdjTeamCount=array();// for bb_teams
    	$vvNajTeamCount=array();// for day_teams
    	foreach($this->llGamesAdj as $fg_idx => $afterGames)
    	{
    		//venue of first game
			$f_vid = $this->createdGames[$fg_idx]['venue_id'];
			if(!isset($vvAdjGameCount[$f_vid])) $vvAdjGameCount[$f_vid]=array();
			if(!isset($vvAdjTeamCount[$f_vid])) $vvAdjTeamCount[$f_vid]=array();
 
			//count how many games came after this one, that were adjacent but it depends on which venue they were on
			foreach($afterGames as $sg_idx)
			{
				//venue of second game
				$s_vid = $this->createdGames[$sg_idx]['venue_id'];
				
				if(!isset($vvAdjGameCount[$f_vid][$s_vid]))$vvAdjGameCount[$f_vid][$s_vid]=0;
				if(!isset($vvAdjTeamCount[$f_vid][$s_vid]))$vvAdjTeamCount[$f_vid][$s_vid]=0;
				//count up one more game that was adjacent
				$vvAdjGameCount[$f_vid][$s_vid]++;
				
				//this may be zero: but just tack on teh count of team overlap
				$vvAdjTeamCount[$f_vid][$s_vid] += $this->countTeamsInCommon($fg_idx,$sg_idx);
				
			}			
    	}
    	
    	//non adj, but same day
    	foreach($this->llGamesNaDay as $fg_idx => $afterGames)
    	{
			$f_vid = $this->createdGames[$fg_idx]['venue_id'];
			if(!isset($vvNajTeamCount[$f_vid])) $vvNajTeamCount[$f_vid]=array();
			foreach($afterGames as $sg_idx)
			{
				//venue of second game
				$s_vid = $this->createdGames[$sg_idx]['venue_id'];
				//so we have two games, one game is after the second, on same day and non adjacent
				if(!isset($vvNajTeamCount[$f_vid][$s_vid]))$vvNajTeamCount[$f_vid][$s_vid]=0;
				//starts at zero, now add the teams that had to move
				$vvNajTeamCount[$f_vid][$s_vid] += $this->countTeamsInCommon($fg_idx,$sg_idx);
				
			}
			
			
    	}
    	
    	//initializse the venue distance stats
        //make sure to avoid duplicate mirror records by always having
        //smallest id first
        foreach($this->venueDistanceMatrix as $first_vid=>$data)
        {
        	if(!isset($vvAdjTeamCount[$first_vid])) $vvAdjTeamCount[$first_vid]=array();
        	if(!isset($vvAdjGameCount[$first_vid])) $vvAdjGameCount[$first_vid]=array();
        	if(!isset($vvNajTeamCount[$first_vid])) $vvNajTeamCount[$first_vid]=array();
			foreach($data as $second_vid=>$distance)
			{
				//changed from one dec pt to zero: so round it off
				if(!isset($vvAdjTeamCount[$first_vid][$second_vid] )) $vvAdjTeamCount[$first_vid][$second_vid] = 0;
				if(!isset($vvAdjGameCount[$first_vid][$second_vid] )) $vvAdjGameCount[$first_vid][$second_vid] = 0;
				if(!isset($vvNajTeamCount[$first_vid][$second_vid] )) $vvNajTeamCount[$first_vid][$second_vid] = 0;
 				$distance=round($distance);
 				//if($distance != 0)
 					//$distance=number_format($distance,1,'.',',');
 				
				$venDistanceStats[]=array(
					'first_venue_id'=>$first_vid
					,'first_venue'=>$this->venueName[$first_vid]
					,'second_venue_id'=>$second_vid
					,'second_venue'=>$this->venueName[$second_vid]
					,'distance'=>$distance
					,'bb_teams'=>$vvAdjTeamCount[$first_vid][$second_vid] 
					,'bb_games'=>$vvAdjGameCount[$first_vid][$second_vid]
					,'day_teams'=>$vvNajTeamCount[$first_vid][$second_vid]
				); 
			}
        }
        
        $audits['auditVenueDist']=$venDistanceStats;
        
        
        
        /****************  main loop on createdGames, that calcs most other stats           *************/
        $missing_games=array();
        if(!is_array($this->createdGames )) $this->createdGames =array(); 
       
        foreach($this->createdGames as &$g)
        {
			if( $g['timeslot']<0) 
			{
				$missing_games[]=$g;
				continue;
			}
			
			//$vid= $g['venue_id'];
			$ht = $g['home_id']; 
			$at = $g['away_id']; 
			
			 
			
			
			if(!isset($this->homeGames[$ht]))
        		$this->homeGames[$ht]=0;//start at zero
        		
			$this->homeGames[$ht]++;
			
			if(!isset($this->awayGames[$at]))
        		$this->awayGames[$at]=0;
			$this->awayGames[$at]++;
			
			
			$date=$g['date_timestamp'];
			//div
 
       	   
       	   $time_fmt = 'g:i a';

       	   //$game['display_date']=date($full_fmt,$date);//deprec
		   if(!array_key_exists($date,$date_stats))
		   		{$date_stats[$date]=0;}

		   $date_stats[$date]++;
       	
       	   if(isset($g['start_time']) && $g['start_time'])//if  exists, not unassigned
       	   {
       	   	   $g['display_start_time']=date($time_fmt,$g['start_timestamp']  );
		   }
		   		//now count division stats
		
 
			
			$v_id = $g['venue_id'];
			//$m_pk = $g['match_pk'];
			
			$h_div=$g['home_div'];
			$a_div=$g['away_div'];
			
			
			if(!isset($div_date_count[$h_div]))
				$div_date_count[$h_div]=array();
			if(!isset($div_date_count[$a_div]))
				$div_date_count[$a_div]=array();
				
			if(!isset($div_date_count[$h_div][$date]))
				$div_date_count[$h_div][$date]=0;
			if(!isset($div_date_count[$date][$date]))
				$div_date_count[$a_div][$date]=0;	
				
			if(!isset($div_venue_count[$h_div]))
				$div_venue_count[$h_div]=array();
			if(!isset($div_venue_count[$a_div]))
				$div_venue_count[$a_div]=array();	
				
			if(!isset($div_venue_count[$h_div][$v_id]))
				$div_venue_count[$h_div][$v_id]=0;
			if(!isset($div_venue_count[$a_div][$v_id]))
				$div_venue_count[$a_div][$v_id]=0;
				
			
			if(!isset($div_match_count[$h_div]))
				$div_match_count[$h_div]=array();
			if(!isset($div_match_count[$a_div]))
				$div_match_count[$a_div]=array();
				
			
			if(!isset($div_match_count[$h_div][$a_div]))
				$div_match_count[$h_div][$a_div]=0;
			if(!isset($div_match_count[$a_div][$h_div]))
				$div_match_count[$a_div][$h_div]=0;
				
			$div_match_count[$h_div][$a_div]++;		
			$div_match_count[$a_div][$h_div]++;
			
			$div_date_count[$a_div][$date]++;
			$div_venue_count[$a_div][$v_id]++;
			$div_date_count[$h_div][$date]++;
			$div_venue_count[$h_div][$v_id]++;
			
        }
        
        if(!is_array($this->teamList)) $this->teamList=array();
        $audits['gameStats']=array();
        
        foreach($this->teamList as $division_id => $team_array){
        foreach($team_array as $team_info)
        {
        	$teamid=$team_info['team_id'];
        	//$div_name=
        	
        	if(!isset($this->homeGames[$teamid]))$this->homeGames[$teamid]=0;// 
        		
        	if(!isset($this->awayGames[$teamid]))$this->awayGames[$teamid]=0;//if no  games -> zero so its not blank
        	
        	$home = $this->homeGames[$teamid];
            $away = $this->awayGames[$teamid];
            $total= $home + $away;
            $diff = $home - $away;
            if($total !=0)
            {
                $stats[$row]=array( 'name'=> $this->teamName[$teamid], 
                                    'home'=> $home,
                                    'away'=> $away,
                                    'total'=>$total , 
                                    'id'   =>$teamid, //depreciated but left in
                                    'team_id'   =>$teamid, 
                                    'division_name'=>$this->divNames[$division_id],//=$this->divisions_model->get_division_extended_name($division_id);,
                                    'division_id'=>$division_id, 
                                    'diff'=>$diff
                                    );            
                $row++;
            }
        }}
        
        $audits['gameStats']       = $stats;

        //debug: final vardump statements
        $vstats = array();
        //$row=0;
        
        $teamVenueJoin=array();
        
 
        
        foreach($this->venueCount as $vid => $list)
        {//assumes there is no venueid zero
        	$vid=(int)$vid;
 
			
            if(   isset($this->venueCount[$vid]['total']))
            {
                $total = $this->venueCount[$vid]['total'];
                $vstats[] = array('venue_id'=>$vid, 'total'=>$total, 'venue_name' => $this->venueName[$vid] );
               // $row++;
            }
            
            //now loop for each teamid
            foreach($this->venueCount[$vid] as $teamid => $count)
            {
                if($teamid != 'total')
                {    
                    $teamVenueJoin[] = array( 'venue_id'=>$vid, 'team_id'=>$teamid, 
                      'team_name'=>$this->teamName[$teamid], 'venue_name'=> $this->venueName[$vid], 
                      'total'=>$count  );
                }      
            }                  
        }

        
        if($this->floatingGames != 0)
            $vstats[] = array( 'venue_id'=>-1, 'total'=>$this->floatingGames, 'venue_name' => "None Available" );
        //$floatingGames
        $audits['venueStats']=$vstats;

        $audits['teamVenueJoin']=$teamVenueJoin;
        
 
 
	   if(!is_array($this->teamName)) $this->teamName=array();
       foreach($this->teamName as $team_id =>$name)
       {
	   	    foreach($this->teamName as $other_id =>$other_name)
	   	    {		   		
	   	    	if($other_id==$team_id) continue;
	   	    	if(  !isset($this->head_to_head[$team_id][$other_id] )  )
	   	    		        $this->head_to_head[$team_id][$other_id] = 0;
	   	    		        
	   	    	if(  !isset($this->head_to_head[$other_id][$team_id] )  )
	   	    		        $this->head_to_head[$other_id][$team_id] = 0;
	   	    		 
	   	    	$total=$this->head_to_head[$team_id][$other_id]+$this->head_to_head[$other_id][$team_id];
	   	    	//include rows for team playing itself -that way we can ensure zeroes
	   	    	$teamMatchups[] = array
		   			(		   			
		   				'home_id'   =>$team_id,
		   				'home_name' =>$name,
		   				'away_id'   =>$other_id,
		   				'away_name' =>$other_name,
		   				'home_count'=>$this->head_to_head[$team_id][$other_id],
		   				'away_count'=>$this->head_to_head[$other_id][$team_id],
		   				'total'	    =>$total
		   			);
			}		   	
       }
       
		$num_decimals = 1;
		$dec_point = ".";
		$thous_sep="";

       foreach($teamMatchups as &$team_row)
       {
       	   if(!$team_row['total'] || $team_row['total']==0)
       	   {
			   $team_row['percent_home'] = '';
			   $team_row['percent_away'] = '';
       	   }
       	   else
       	   {
       		   $hp = 100*$team_row['home_count']/$team_row['total'];
       		   $ap = 100*$team_row['away_count']/$team_row['total'];
		   
			   $team_row['percent_home'] = number_format($hp,$num_decimals,$dec_point,$thous_sep );
			   $team_row['percent_away'] = number_format($ap,$num_decimals,$dec_point,$thous_sep );
		   }
       }

       $audits['teamMatchups']=$teamMatchups;
       $date_stats_table=array();
       foreach($date_stats as $date=>$total)
       {
       	   //echo $date;
       	   //$d=substr($date,0,4)."-".substr($date,4,2).'-'.substr($date,6,2);
       		$date_stats_table[] = array('date'=>date($plain_fmt,$date),'total'=>$total);
	   }
        
       $audits['dateStats']=$date_stats_table;
       
       
       $team_date_table=array();
       
       if(!is_array($this->dailyGames)) $this->dailyGames=array();
       foreach($this->dailyGames as $team_id => $data)
       {
		   foreach($data as $date=>$total)
		   {
			  // $d=substr($date,0,4)."-".substr($date,4,2).'-'.substr($date,6,2);
			  // echo $d;
			   $team_date_table[] = array(
			   		'team_id'  =>$team_id, 
			   		'team_name'=>$this->teamName[$team_id] , 
			   		'date'=>date($plain_fmt,strtotime($date)),
			   		'total'=>$total		
			   						);
		   }
		   
       }

		$audits['teamDateJoin']=$team_date_table;
		

		 
		
		$div_match_table=array();
		$div_date_table=array();
		$div_venue_table=array();
		foreach($div_date_count as $id=>$dcounts)
		{
			$div_name=$this->divNames[$id];
			
			foreach($dcounts as $datets=>$cnt)
			{
				$row=array();
				$row['division_id']=$id;
				$row['division_name']=$div_name;
				$row['date'] = date($plain_fmt,$datets);
				$row['total']=$cnt;
				
				$div_date_table[]=$row;
						
			}
			
		}
		$audits['div_date_table']=$div_date_table;
		foreach($div_venue_count as $id=>$vcounts)
		{
			$div_name=$this->divNames[$id];
			
			foreach($vcounts as $vid=>$cnt)
			{
				$row=array();
				$row['division_id']=$id;
				$row['division_name']=$div_name;
				$row['venue_id'] = $vid;
				$row['venue_name'] = $this->venueName[$vid];
				$row['total']=$cnt;
				
				$div_venue_table[]=$row;
						
			}
			
		}
		$audits['div_venue_table']=$div_venue_table;
		
		foreach($div_match_count as $hid=>$arr)
		{
			$h_div_name=$this->divNames[$hid];
			
			foreach($arr as $aid=>$cnt)
			{
				$row=array();
				$row['h_division_id']=$hid;
				$row['a_division_id']=$aid;
				$row['h_division_name']=$h_div_name;
				$row['a_division_name']=$this->divNames[$aid];
 
				$row['total']=$cnt;
				
				$div_match_table[]=$row;
						
			}
 
		}
		$audits['div_match_table']=$div_match_table;
		
		//missing games audit
		$missing_games_table=array();
		foreach($missing_games as $mg)
		{
			$row=array();
			$row['home_name']=$mg['home_name'];
			$row['home_id']=$mg['home_id'];
			$row['home_id']=$mg['home_id'];
			$row['away_name']=$mg['away_name'];
			$row['h_division_name']=$this->divNames[$mg['home_div']];
			$row['a_division_name']=$this->divNames[$mg['away_div']];
			$row['h_division_id']=$mg['home_div'];
			$row['a_division_id']=$mg['away_div'];
			$row['match_pk']=$mg['match_pk'];
			//$match = $this->schedule_model->s_get_match_by_pk($mg['match_pk']);
			
			$missing_games_table[]=$row;
		}
		$audits['missing_games_table']=$missing_games_table;
		
		$audits['currentSchedule'] = $this->createdGames;
		
		return $audits;
    }
    
    
    
    
    
    /**
    * returns a string that represents a 
    * csv of given schedule
    * 
    * not currently in use
    * 
    * @param mixed $sch
    * @param mixed $sep
    * @param mixed $with_ids
    */
    public function make_csv_schedule($sch,$sep=',',$with_ids=false)//
   {	   
 
	    
		$data = '';    
		$sortdate='';
		$date='';
		$eol="\r\n";//was unicode or whatever from \n
		foreach ($sch as $game)
		{
			
			$t = strtotime($game['game_date']);
			$date = date('D M d, Y',$t);
			
			
			$start = date("g:i a", strtotime($game['start_time']));
			$end = date("g:i a", strtotime($game['end_time']));
			
			
			$data .= 
				$game['home_name'].$sep.
				$game['away_name'].$sep.			
				$date .$sep.
				$start.$sep.
				$end.$sep.
				$game['venue_name'].$sep;
			if($with_ids)
			{
				$sortdate = date('Ymd',$t);
				$data .=
				$game['home_id'].$sep.
				$game['away_id'].$sep.
				$sortdate.$sep.
				$game['venue_id'].$sep.
				$game['game_id'];
			}
			$data .=$eol;
		}
		return $data;
	}
    
    
    /**
    * assumes globalRules and season are set as object variables
    * generates an array of datesets, eeach with date array, and returns them
    * used wit hthe ds template form.  on return, they should 
    * be inserted/saved as datesets which will then make the dateset_pk
    * 
    * @param mixed $type
    * @param mixed $weekdays
    * @param mixed $start
    * @param string $end
    * @param mixed $num
    * @return string
    */
    public function create_ds_template($type,$weekdays=null,$start='06:00 PM',$end=null,$num=1)
	{
		if(!$weekdays )$weekday=array();
 
		$global_length=$this->globalRules['len'];
		$g_length_minutes=(int) $this->timeToMinutes($global_length);
		$g_length_minutes+= $this->globalRules['warmup'];
		$g_length_minutes+= $this->globalRules['teardown'];
		$g_length_seconds = $g_length_minutes*60;
		$start_ts = strtotime($start);
		$fmt_time = "g:i A";
		$SINGLE=1;
		$DOUBLE=2;
		$TRIPLE=3;
		$FULL=4;
		switch($type)
		{
			case $SINGLE:
				$prefix='Single Headers';
				$hex='FF0000';
 
				$end_ts = $start_ts + $g_length_seconds;//one space for game
				$end = date($fmt_time,$end_ts);
				//$end = date($fmt_time,$start_ts);
			break;
			case $DOUBLE:
				$prefix='Double Headers';
				$hex='FFFF00';
 
				
				$end_ts = $start_ts + 2*$g_length_seconds;//two space for game
				$end = date($fmt_time,$end_ts);
			break;
			case $TRIPLE:
				$prefix='Triple Headers';
			
			
				$end_ts = $start_ts + 3*$g_length_seconds;//two space for game
				$end = date($fmt_time,$end_ts);
			break;
			case $FULL:
				$prefix='Full Day Group';
				$hex='00FFFF';
 				  //hardcoded default
				 if(!$end)$end  ='05:00 PM';
			
			break;
		}
    	//numeric values of weekday conforms to ISO-8601, compatible with date() in PHP 5.1.0 or higher
    	//sunday is 7, monday is 1, etc
		$weekday_types=array('u'=>7,'m'=>1,'t'=>2,'w'=>3,'r'=>4,'f'=>5,'s'=>6);
		
 		$sets=array();
 		
		for ($n=1;$n<=$num;$n++)
		{
			$name = $prefix;    
			$new_set=array($name,$start,$end,$hex);
			$dateArray=array();
			//gather all dates together
			foreach($weekdays as $weekday)
			{
				//for each weekday selected 
				$weekday = $weekday_types[strtolower($weekday)];//parse single character to ISO number for weekday
				
				$ts_start=strtotime($this->season['season_start']);
				$ts_end  =strtotime($this->season['season_end'  ]);
				$currDate=$ts_start;
				//loop on All days in the season
				do
				{
					if(date('N',$currDate)==$weekday)
					{
						//if correct weekday then save for dateset
			    		$dateArray[] = date( 'Y/m/d' , $currDate );
					}
					//saved or not, move to next day
					$currDate = strtotime( '+1 day' , $currDate );
					
				} 
				while( $currDate<=$ts_end );
 
			}
			//dates are gathered so addd dates to this set 
			$new_set[]=$dateArray;
			$sets[]=$new_set;
		} 
		return $sets;
	}
    
    
    
    /**
    * complute the full size of a game, including warmups etc
    * if args are empty, will get from ->globalRules
    * 
    * @return minutes
    * 
    * @param mixed $game_len
    * @param mixed $warmup
    * @param mixed $td
    */
    public function getFullSlotSize($game_len=null,$warmup=null,$td=null)
    { 
    	if(!$game_len)  $game_len = $this->globalRules['len'];
    	if(!$warmup) 	$warmup   = $this->globalRules['warmup'];
    	if(!$td) 		$td 	  = $this->globalRules['teardown'];
    	
		return $this->timeToMinutes($game_len) + $this->timeToMinutes($warmup) + $this->timeToMinutes($td);
    }
    
    
    //method to check on the fly
    //checkign user input, that is
    public function compare_game_datesets_overflow($datesets,$game_len,$warmup,$td)
    {
    	if(!count($datesets)) {return array();} 
 
    	$errors = array();
    	$minRequired = $this->getFullSlotSize($game_len,$warmup,$td);
 
    	foreach($datesets as $ds)
    	{
    		//var_dump($ds);
    		$ts_start = $ds['start_time'];
    		$ts_end   = $ds['end_time'];
    		
    		$space = $this->timeBetween($ts_start,$ts_end);
 
    		$spareTime = $space - $minRequired ;
    		if($spareTime < 0)
    		{
				$errors[] = array('spare'=>abs($spareTime),'set_name'=>$ds['set_name']);
    		}
		}
		return $errors;
    }
    
    
    
    
    
    
}
?>
