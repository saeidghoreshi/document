<?php
require_once('endeavor.php');
class Schedule extends Endeavor
{
	private $debug=false; //  if($this->debug) echo "";
	
	/**
	* 
	* @var scheduler
	*/
    public $scheduler;
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
    
    /**
    * put your comment there...
    * 
    * @var season_model
    */
    public $season_model;
    /**
    * 
    * 
    * @var facilities_model
    */
    public $facilities_model;
    /**
    * 
    * 
    * @var divisions_model
    */
    public $divisions_model;
    
 
 
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
		$this->load->library('scheduler');
    }
    
   // Window functions // // // // // // // // // // // // //
    private function load_window()
    {
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/");
        $this->window->set_js_path("/assets/js/components/schedules/");
    }
    
    public function window_league_schedule()
    {
        $this->load->library('window');
        //$this->window->set_css_path("/assets/css/");
        
		//load venues and facilities components first
		
        $this->window->set_js_path("/assets/js/components/facilities/");
          
        //not sure where these will end up
        $this->window->add_js('forms/forms.js');
        $this->window->add_js('windows/spectrumwindow.facilities.js');
        
        $this->window->add_js('grids/spectrumgrids.facility.js'); 
        $this->window->add_js('grids/spectrumgrids.venue.js');   
        
        ///models for grids
        $this->window->set_js_path("/assets/js/models/");
        $this->window->add_js('schedule_saves.js'); 
        $this->window->add_js('timeslots.js'); 
        $this->window->add_js('matches.js'); 
        $this->window->add_js('sch_rules.js'); 
         
        //now the wizard controller
        $this->window->set_js_path("/assets/js/components/schedule_wizard/");
        
        $this->window->add_js('forms/sch_rules.js'); 
        $this->window->add_js('forms/timeslot_template.js'); 
        $this->window->add_js('forms/timeslot_rules.js'); 
        $this->window->add_js('forms/timeslot.js'); 
        $this->window->add_js('forms/new_schedule.js'); 
        $this->window->add_js('forms/finalize.js'); 
        $this->window->add_js('forms/create_divmatch.js'); 
        $this->window->add_js('grids/schedule_saves.js'); 
        $this->window->add_js('grids/wizard_games.js'); 
        $this->window->add_js('grids/div_match.js'); 
        $this->window->add_js('grids/timeslots.js'); 
        $this->window->add_js('controller.js'); 
        $this->window->add_js('toolbar.js'); 
        //HTML
        $this->window->set_header('Scheduler');
        $this->window->set_body($this->load->view('schedule/wizard/wizard.main.php',null,true));

        $this->window->json();   
    }
    
    public function window_tournament_schedule()
    {
        
    }
        
    public function window_playoff_schedule()
    {
        $data['body'] = array();
        $data['body']['setup']     = $this->load->view('schedule/playoff/setup',null,true);
        $data['body']['standings'] = $this->load->view('schedule/playoff/standings',null,true);
        $data['body']['games'] = $this->load->view('schedule/playoff/games',null,true);
        $data['body']['wizard'] = $this->load->view('schedule/playoff/wizard',null,true);
        $data['footer'] = array();
        $this->load_window();
        $this->window->add_css('schedule.css');
        $this->window->add_js('class.schedule.playoff.js');
       
        $this->window->set_header('Playoff Schedule');
        $this->window->set_body($this->load->view('schedule/playoff/main.php',$data['body'],true));
        $this->window->set_footer($this->load->view('schedule/playoff/footer.php',$data['footer'],true));
       // if($id) $this->window->set_id($id);
        $this->window->json();    
    }

    //added for sencha 2.0
    public function window_manage()
    {
    	$this->load->library('window');
        //$this->window->set_css_path("/assets/css/");
        
        //data models
        $this->window->set_js_path("/assets/js/models/");
        $this->window->add_js("schedule.js");
        $this->window->add_js("game.js");
        
        //compoent files
        $this->window->set_js_path("/assets/js/components/schedules/");

        //load the forms and windows
        
        $this->window->add_js('forms/rainout.js');
        $this->window->add_js('windows/rainout.js');
        $this->window->add_js('forms/create_game.js');
        $this->window->add_js('windows/create_game.js');
        $this->window->add_js('forms/reschedule_game.js');
        $this->window->add_js('windows/reschedule_game.js');
        
        //grids
        $this->window->add_js('grids/schedules.js');
        $this->window->add_js('grids/games.js');
        
        //load the main object
        
        $this->window->add_js('controller.js');
        
        //HTML
        $this->window->set_header('Schedules');
		
        $this->window->set_body($this->load->view('schedule/manage/main.php',nulL,true));
        
        $this->window->json();  
    }

    public function json_schedulebyseason()
    {
    	$season_id = $this->input->post('season_id');
		//first find active league id
		$org = $this->permissions_model->get_active_org();

		$league_id = $this->leagues_model->get_league_from_org($org);
		$sch = $this->schedule_model->get_schedulebyseason($league_id,$season_id);
		$this->result->json($sch);
    }

    public function json_getleagueschedules()
    {
        $league = $this->input->post('league');
        if( $league =='' || $league == null) return;
        
        $schedules = $this->schedule_model->get_leagueschedules($league);
        $this->result->json($schedules);
    }
    
    public function json_season_schedule_game_join()
    {
		$org=$this->permissions_model->get_active_org();
		$l = $this->leagues_model->get_league_from_org($org);
		$res=$this->schedule_model->get_season_schedule_game_join($l);
 
		 $this->result->json_pag($res  );
    }

    public function json_get_resc()
    {
        $sch = $this->input->post('schedule_id');

        $results = $this->schedule_model->get_sch_reschedule_requests($sch);
        //since request_date is a timestmap, we split into date and time
        foreach($results as &$row)
        {
			$row['day'] = date('F j, Y',strtotime($row['desirable_datetime']));
			$row['time'] = date('g:i a',strtotime($row['desirable_datetime']));
			
        }
        	//if(array_key_exists('is_rainout',$row))
        		//$row['is_rainout'] = ($row['is_rainout'] == 't' ? 'Yes' : 'No');
        
        $this->result->json($results);
        
    }
    
    public function post_delete_resc()
    {
		$resc_id = (int)$this->input->post('request_id');
		
		$this->result->json($this->schedule_model->delete_reschedule_request($resc_id));
    }

    
    /**
    * this should not even be here!!
    * 
    */
    public function json_venuecontracts()
    {
    	$vf = $this->facilities_model->get_fac_venues();
        $this->result->json($vf   );
    }    
    
    public function json_venuestats()
    {
        $stats = $this->schedule_model->get_venuestats();    
        $this->result->json($stats);    
    }
    
    
    /**
    * cannot be moved to divisions controller, since we handle parent id in a specific way
    * 
    * 
    */
    public function json_sub_divisions()
    { 
    	$parent_id = $this->input->post('parent_id');
    	$season_id = $this->input->post('season_id');
    	$org=$this->permissions_model->get_active_org();
    	$league_id=$this->leagues_model->get_league_from_org($org);
		if($parent_id !=0 && $parent_id != '0')
        	$result = $this->divisions_model->get_sub_divisions_tc($parent_id,$season_id);
        else
        	$result = $this->divisions_model->get_parent_divisions_tc($league_id,$season_id);

        //now count the teams in each one
        foreach($result as &$row)	
        {        
			if($row['only_teams'] == 'f')
			{
				$pools = array();
				$divteam_count=0;		
				$this->divisions_model->recursive_pool_subdivisions($row['division_id'],$season_id,&$pools);	
				foreach($pools as $div)
				{
					$local = (int)$div['team_count'];
					$divteam_count+=$local;	
				}
				$row['divteam_count']=$divteam_count;				
			}			
			else $row['divteam_count'] = 0;
			$row['total_teams'] = $row['divteam_count']+$row['team_count'];//covers both cases
		}
 	
        $this->result->json($result);
    }
    /**
    * cannot be moved to divisions controller bc of customied hanlding
    * 
    */
    public function json_pool_sub_divisions()
    { 
    	$parent_id = $this->input->post('parent_id');
    	$season_id = $this->input->post('season_id');
    	//$org=$this->permissions_model->get_active_org();
    	//$league_id=$this->leagues_model->get_league_from_org($org);
    	
		//if($parent_id !=0 && $parent_id != '0')
		$result=array();
		
		//what if THIS division already has_teams => then return itself
		$this_div = $this->divisions_model->get_division_tc($parent_id,$season_id);
		
		if($this_div[0]['only_teams']=='t')
			$result = $this_div;
		else		
			$this->divisions_model->recursive_pool_subdivisions($parent_id,$season_id,&$result);	
	
        //$result = $this->divisions_model->get_sub_divisions_tc($parent_id,$season_id);
        
        foreach($result as &$row)
        {
			$row['total_teams'] = $row['divteam_count']+$row['team_count'];
        }
 	
        $this->result->json($result);
    }
 
    public function json_schedule_data()
    {
       //get current schedule from session data
        $data = $this->schedule_model->s_current_session_schedule();
 
        $this->result->json($data );     
    }

    public function json_schedule_stats()
    {
        $stats=$this->schedule_model->s_schedule_stats();        
        
        $this->result->json($stats);            
    }   
  
    public function json_schedule_vstats()
    {
        $stats=$this->schedule_model->s_schedule_vstats();
        
        $this->result->json($stats);            
    }
    
    public function json_schedule_join()
    {
        
        $stats=$this->schedule_model->s_team_venue(); 
        $this->result->json($stats);
    }
    
    public function json_schedule_teamdate()
    {
		
        $stats=$this->schedule_model->s_team_date();
		$this->result->json($stats);
    }
    
    public function json_schedule_matchstats()
    {
        $stats=$this->schedule_model->s_schedule_matchstats();
		$this->result->json($stats);
    }
    public function json_schedule_datestats()
    {
        $stats=$this->schedule_model->s_schedule_datestats();
		$this->result->json($stats);
    }

    public function json_audit_venue_distances()
    {
        $stats=$this->schedule_model->s_audit_venue_distances();
		$this->result->json($stats);				
    }
    
    public function json_audit_div()
	{ 
        $stats=$this->schedule_model->s_audit_div();
		$this->result->json($stats);
	}
	public function json_audit_div_date()
	{ 
		//if(isset($_SESSION['div_date_table']))

        $stats=$this->schedule_model->s_audit_div_date();
		$this->result->json($stats);
	}
	
	
	
	public function json_audit_div_venue()
	{ 
		
        $stats=$this->schedule_model->s_audit_div_venue();
		$this->result->json($stats);
	}
	public function json_audit_missing()
	{
		$d=$this->schedule_model->s_audit_missing();
		$this->result->json($d);
	}
   public function post_save_schedule()
   {
 
		//the name was POSTed using an ajax call
 
		//now save to database using the model
 
		$user=$this->permissions_model->get_active_user();
		$org = $this->permissions_model->get_active_org();
		$league_id = $this->leagues_model->get_league_from_org($org);
		$schedule_id=(int)$this->input->post('schedule_id');
		$schedule_name=rawurldecode($this->input->post('schedule_name'));
		$season_id=(int)$this->input->post('season_id');
		$is_published=$this->input->post('is_published');
		
 
		//make sure season is assigned to league -> that they did not switch orgs before save
		$season = $this->season_model->get_season_data($season_id);
		//var_dump($season);
		if(count($season)==0 || $season[0]['league_id'] != $league_id)
		{
			echo "Error: season ".$season_id." assigned to your current active organization".$league_id.", cannot save. \n";
			return;
		}
		
		//$schedule_id = $this->schedule_model->insert_schedule($name,$user,$org,'f');
		
		echo $this->schedule_model->insert_lss($league_id,$season_id,$schedule_id);
		
	
		//$scheduleid = $this->schedule_model->insert_schedule($_SESSION['currentSchedule'],$name,$league_id,$season_id);
		
		//$this->schedule_model->insert_lss($league_id,$season_id,$scheduleid);
		$schedule = $this->schedule_model->s_current_session_schedule();
		echo $this->save_schedule($schedule,$schedule_id);
        //$_SESSION['schedule_id']=$schedule_id;//save this for publish
       
        //schedule is saved;;:: also save any empty timeslots
        $free_ts=$_SESSION['currentTimeslots'];

		foreach($free_ts as $ts)
		{//if venue id is not valid, make it null
			$vid=$ts['venue_id'];
			if( !is_numeric($vid)  )	$vid=null;
			else if (intval($vid) <= 0) $vid=null;
			if($vid!=null && isset($ts['sortdate']) && $ts['sortdate']!=null )
				 $this->schedule_model->insert_timeslot($schedule_id,$ts['sortdate'],$ts['start'],$ts['end'],$vid);
		}
		echo $this->schedule_model->update_schedule_name($schedule_id,$schedule_name);
		
		
		if($is_published===true || $is_published=='true'||$is_published=='t')
		{
			$this->schedule_model->update_publish_schedule($schedule_id,'t');
		}
   } 
   
   /**
   * takes an array of games, and a schedule id that exists in db
   * and inserts every /scheduled/ game into database
   * depends on specific associative array keys of array
   * 
   * last: it will update the type of the schedule from pending to complete
   * 
   * @param mixed $schedule
   * @param mixed $schedule_id
   */
   private function save_schedule($schedule,$schedule_id)
   {
		$org = $this->permissions_model->get_active_org();     
		$user= $this->permissions_model->get_active_user(); 
	    foreach($schedule as $i=>$game)
		{
			$homeid = (int)$schedule[$i]['home_id'];
			$awayid = (int)$schedule[$i]['away_id'];
			if(isset($schedule[$i]['game_date']))
				$date   = $schedule[$i]['game_date'];
			else $date=null;
			$start  = $schedule[$i]['start_time'];
			$end    = $schedule[$i]['end_time'];
			$vid    = (int) $schedule[$i]['venue_id'];

			if(!$start) {$start=null; continue;}
			if(!$end)   {$end=null; continue;}
			//we need the result of this which is the game_id from the sequence of the newly created game 

			//the  strtotime check should cover everything, the rest are probably redundant checks... leave them in anyway
			//prior to PHP 5.1, strtotime returned -1 on fail. newer versions return false, so ill just check both
			if(strtotime($date)===false||strtotime($date)===-1|| $date == '' || $date == "Not Scheduled" || $date == -1)
			{
				$date=null;
				continue;
			}//date is allowed to be null
			
			if( $vid == -1)
			{
				$vid=null;
				continue;
			}
					
			$gameid = $this->games_model->insert_game($vid,$schedule_id,$date,$start,$end,$user,$org);
				
			$home_team_code=1;//boolean values basically
			 $this->games_model->insert_teamgame($gameid,$homeid,$home_team_code);//1 for home

			$away_team_code=0;
			 $this->games_model->insert_teamgame($gameid,$awayid,$away_team_code);//zero for away
		}
		$finalized=1;//from lu_schedule_status table, type 3 was an incomplete file, 1 is finalized.
		echo $this->schedule_model->update_schedule_type($schedule_id,$finalized);
       //return $schedule_id;
   }
    
    // FORM POST ////////////////////////////////////////////////////////////////////
       
    public function post_schedule_manual()
    {
        //POST variables are:
        $schedule = json_decode(  $this->input->post('schedule') ,true);
        $name = rawurldecode( $this->input->post('name') );
        $org = $this->permissions_model->get_active_org();     
        $user= $this->permissions_model->get_active_user();   
    	$league_id=$this->leagues_model->get_league_from_org($org);

        $season_id = json_decode($this->input->post('season'));
        
        //convert game length+start time to end_time
        foreach ($schedule as &$game)
        {
            if($game['date'] == '')
                $game['date'] = 'None Assigned';
            else
            	$game['date'] = date('Y-m-d',strtotime($game['date']));
            $game['start_time'] = $this->scheduler->timeMerToAstro($game['start_time']);
            $game['end_time']   = $this->scheduler->addTime($game['start_time'],$game['game_length']);
            
        	//echo $game['start_time']." ".$game['end_time'] ."  len was ".$game['game_length'];

        }
        //return;
        //create record of schedule
        $schedule_id = $this->schedule_model->insert_schedule(  $name,$user,$org,'f');
        //attach it to season and league
        $this->schedule_model->insert_lss($league_id,$season_id,$schedule_id);
		
		//add games to schedule
        $this->save_schedule($schedule,$schedule_id);//save this for publish

        echo $schedule_id;
    }   
    
     
	public function json_timeslots()
	{
		$schedule_id = $this->input->post('schedule_id');
		$r = $this->schedule_model->get_timeslots($schedule_id);
		
		$this->result->json($r);
	}
   
    
    /**
    * should be renamed to something like "remove_timeslots_from_similar_games"
    * 
    */
    public function json_get_similar_games()
    {
    	$user=$this->permissions_model->get_active_user();
		$type = $this->input->post('type');// is either 'date' or 'datevenue' or 'only'
		$game_id = (int)$this->input->post('game_id');
		$schedule_id =(int) $this->input->post('schedule_id');
		
		$selected_game = $this->games_model->get_game($game_id);
		
		$selected_game=$selected_game[0];
		$schedule = $this->games_model->get_games($schedule_id);
		
		$return = array();
		$return[]=$this->games_model->update_game($game_id,null,null,null,$user,null);
		//$game_id,$start_time,$end_time,$date,$user,$vid)
		$selected_game;//always get selected game
		
		if($type == 'only')
		{//then we are done
			$this->result->json($return_games);
			return;
		}
		foreach($schedule as $sch_game)
		if($sch_game['game_id']!=$game_id)
		{
			$match = false;
			if($type=='datevenue' && $selected_game['venue_id']==$sch_game['venue_id'])
				$match = true;
			//always compare dates
			if ($selected_game['game_date']==$sch_game['game_date'])
				$match = true;		
			else $match = false;//put match back to false, since it must satisfy BOTH
			if($match)
				$return[]=$this->games_model->update_game($sch_game['game_id'],null,null,null,$user,null);	
		}
		$this->result->json($return);
    }
    /**
    * from rainout form: 
    * will handle all the game ids given in the specified way
    * whether its cancel, postpone, et 
    * 
    */
    public function post_rainout()
    {
    	$user =(int)$this->permissions_model->get_active_user();
    	$owner=(int)$this->permissions_model->get_active_org();
		$type = $this->input->post('rainout_type');
		$schedule_id =(int) $this->input->post('schedule_id');
		$game_ids =json_decode( $this->input->post('game_ids'));
		
		foreach($game_ids as $game_id)
		{
			
			//echo $game_id;
			
			switch($type)
			{//c,r,t,p
				case 'c':
				 
				
					echo $this->games_model->delete_game($game_id,$user);
				
				break;
				case 't':
 
					echo $this->statistics_model->insert_game_result(0,0,$game_id,$user,$owner,null,null,'t');//is valid
				break;			
				case 'p':
 
					echo $this->games_model->update_game($game_id,null,null,null,$user,null);//not jujts timeslot, but erase venue as well
				break;
				default:
					echo 'Post Error: No Rainout Type found';
					return;
				break;
			}
			
		} 
    }
 
    private function check_existing_gameconflict($game_id,$gamestart,$gameend,$gamedate,$schedule_id='',$c_venue)
    {
		//$error = false;//asume no conflict until one found
		$current_game = $this->games_model->get_game($game_id);
		$schedule_id=$current_game[0]['schedule_id'];
		$games = $this->games_model->get_games($schedule_id);
		
		if(count($games)==0 || count($current_game)==0)  return false;

		$current_game=$current_game[0];
		$c_home=$current_game['home_id'];
		$c_away=$current_game['away_id'];
		//$c_venue=$current_game['venue_id'];#consider NEW input venue, not existing one (maybe same maybe not)
		foreach($games as $g=>$game)
		{
			if($game['game_id'] == $game_id) continue;//skip this game
           		
            $homeid   = $game["home_id"];
            $awayid   = $game["away_id"];
            $venue 	  = $game['venue_id'];
            //if different teams AND different venues, no possible conflict
            if($c_home != $homeid && $c_away != $awayid &&
               $c_away != $homeid && $c_home != $awayid && $c_venue != $venue)  continue;
          	
          	//otherwise, conflict exists iff dates match and times overlap      
                  
            $usedstart = $game['start_time'];
            $usedend   = $game['end_time']  ;
			$useddate  = $game['game_date'];
			//TODO !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            if(    strtotime($useddate .' '. $usedstart  )   
                <= strtotime($gamedate .' '. $gamestart) //if it starts before or at the same starttime
                && strtotime($gamedate .' '. $gamestart )     
                 < strtotime($useddate .' '. $usedend))
            {   //if new game ends in between the used timeslot
                return true;//found a conflict         
            }//else check if game starts in between the used timeslot
            else if(  strtotime($useddate .' '. $usedstart  )   
                    < strtotime($gamedate .' '. $gameend) 
                   && strtotime($gamedate .' '. $gameend )     
                   <= strtotime($useddate .' '. $usedend))
            {
                //echo "bottom Yes\n";
                return true;//found a conflict    
            }
        	//else echo "No\n";                                    
		}
		//loop is over, no problems found
		return false;
    }
      
	public function post_schedule_publish()
	{
		if(isset($_SESSION['schedule_id']))
			$id = $_SESSION['schedule_id'];
		else
			return -1;	
		echo $this->schedule_model->update_publish_schedule($id,'t');
		return;
   }
   
   public function post_republish_schedule()
   {
   	  // echo "Publish disabled for now\n";return;
		////post sch id and league from active org   
		$schedule_id=(int)$this->input->post('schedule_id');
		$pub=$this->input->post('pub');
		//echo $schedule_id.":".$pub;
		echo $this->schedule_model->update_publish_schedule($schedule_id,$pub);
   }
   
   
   
   private function make_pretty_schedule($id,$with_ids=false)
   {
	   if(!$id || $id=='null'||$id==null||$id=='')
   			$sch=$_SESSION['createdGames'];
   		else
	    	$sch=$this->games_model->get_games($id);
	   
	   return 'test pdf stuff';
	   $data='';
	   $sep="\t\t";
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
   }
   
  
	
	private function parse_csv_to_schedule($csv,$name,$season_id,$published='f')
	{
		if(!$season_id) 
		{
			return false;
		}
		$user= $this->permissions_model->get_active_user();
		$org = $this->permissions_model->get_active_org();
		$league_id = $this->leagues_model->get_league_from_org($org);
		
		$eol="\n"; // was \r\n
		$c=",";
		//echo "!!! season $season_id , league $league_id \n";
		$lines = explode($eol,$csv);

		$schedule_id=$this->schedule_model->insert_schedule($name,$user,$org,$published);
		$this->schedule_model->insert_lss($league_id,$season_id,$schedule_id);
	
		$game_ids=array();
		$postgres_fmt='Y-m-d';
		foreach($lines as $line)
		{
			if(!$line) return;
			
			list($home_id,$away_id,$ven_id,$date,$start,$end)= explode($c,$line);	
			//echo $line;//. "  becomes Game: h $home_id , a $away_id ,vid $ven_id   , $date , $start ,$end\n";
			$date=date($postgres_fmt,strtotime($date));
			$game_id =  $this->games_model->insert_game($ven_id,$schedule_id,$date,$start,$end,$user,$org);
			$game_ids[]=$game_id;
			$this->games_model->insert_teamgame($game_id,$home_id,1);
			$this->games_model->insert_teamgame($game_id,$away_id,0);		
		}
		
		//return $game_ids;
		return $schedule_id;
	}
	
	public function import_schedule_csv()
	{
		$season_id=(int)$this->input->get_post('season_id');
		$csv =$this->input->get_post('csv');
		$name=$this->input->get_post('name');
		echo $this->parse_csv_to_schedule($csv,$name,$season_id);
	}
	
	
	
	
	public function export_schedule_csv()
	{
		$schedule_id =$_SESSION['schedule_id'];
		$csv=$this->scheduler->make_csv_schedule($this->games_model->get_games($id));
		
		if(isset($_SESSION['schedule_name']))
			$name=$_SESSION['schedule_name'];
		else
			$name="New_Schedule";
		$file_name = $name.".csv";
		//$csv ="W,T,F,".$schedule_id.",after,".$get;
		// fix for IE catching or PHP bug issue:: from php.net comments
		header("Pragma: public");
		header("Pragma: no-cache");//for safari?
		//header("Expires: 0");//for safari?
		header("Expires: 0"); // set expiration time
		//header("Cache-Control: must-rYAHOO.lang.JSON.parseidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		//header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		//header("Content-type: application/octet-stream");
		header('Content-type: text/csv');

		header("Content-Disposition: attachment; filename=\"".$file_name."\"");
		//($schedule_id);
		echo $csv; 
	}
	
	public function export_schedule_pdf()
	{
		$s=' s = test';
		header("Pragma: public");
		header("Pragma: no-cache");//for safari?
		//header("Expires: 0");//for safari?
		header("Expires: 0"); // set expiration time
		
		if(isset($_SESSION['schedule_name']))
			$name=$_SESSION['schedule_name'];
		else
			$name="New_Schedule";
		$file_name = $name.".pdf";
		header('Content-type: application/pdf');
		
		header("Content-Disposition: attachment; filename=\"".$file_name."\"");
		
		
		 try {
	    $p = new PDFlib();

	    /*  open new PDF file; insert a file name to create the PDF on disk */
	    if ($p->begin_document("", "") == 0) {
	        die("Error: " . $p->get_errmsg());
	    }

	    $p->set_info("Creator", "");
	    $p->set_info("Author", "");
	    $p->set_info("Title", $name );

	    $p->begin_page_ext(595, 842, "");

	    $font = $p->load_font("Helvetica-Bold", "winansi", "");

	    $p->setfont($font, 24.0);
	    $p->set_text_pos(50, 700);
	    $p->show("List of ...:");

    	$p->show($s);
		
		
	    $p->continue_text("(says PHP)");
	    $p->end_page_ext("");
	    //////BEGIN SECOND PAGE
$p->begin_page_ext(595, 842, "");

	    $font = $p->load_font("Helvetica-Bold", "winansi", "");

	    $p->setfont($font, 24.0);
	    $p->set_text_pos(50, 700);
	    $p->show("PAGE 2...:");

    	$p->show($s);
		
		
	    $p->continue_text("(says PHP)");
	    $p->end_page_ext("");
	    //////
	    $p->end_document("");

	    $buf = $p->get_buffer();
	    $len = strlen($buf);



	   // print $buf;
	    
	    echo $buf;//echo buff works!:!:!!
			    
	}
	catch (PDFlibException $e) {
	    die("PDFlib exception occurred in hello sample:\n" .
	    "[" . $e->get_errnum() . "] " . $e->get_apiname() . ": " .
	    $e->get_errmsg() . "\n");
	}
	catch (Exception $e) {
	    die($e);
	}
	$p = 0;
		//echo $s;
	}

	public function post_playoff_data()
	{
		$type=$this->input->post('type');
		$rounds=(int)$this->input->post('rounds');
		$season_id=(int)$this->input->post('season_id');
		//$group_div=$this->input->post('group_div');
		//$group_div = ($group_div == 't') ? true : false;
		$team_ids=json_decode($this->input->post('team_ids'));
		$this->result->json($this->create_playoff($type,$rounds,$season_id,$team_ids));
	}

	private function create_playoff($type,$rounds,$season_id,$team_ids)
	{
		if($type=='rr')//round robin. otherwise it is elim for elimination
		{	
			$teams = array();

			
			foreach($team_ids as $id=>$name)
			{//if the team id is selected

			
			
				if($name)//if its not false or empty string
					$teams[]= array('team_id'=>    $id,
									 'team_name'=> $name);
			}				
			//} 
			$games= array();
			$tc = count($teams);	
			for($i=0;$i<$tc;$i++)
				for($j=$i;$j<$tc;$j++)
					for($g=0;$g<$rounds;$g++)
					{
						if( $teams[$i]['team_id'] != $teams[$j]['team_id'])
							$games[]=array('home_id'=>$teams[$i]['team_id'],    'away_id'=>$teams[$j]['team_id'],
									     'home_name'=>$teams[$i]['team_name'],'away_name'=>$teams[$j]['team_name']);
					}
			//echo "RR games created:  ".count($games);		
			return $games;
		}//END OF ROUND ROBIN SCHEUDLE
		else if($type=='elim')
		{
			echo "TODO: elimination\n";return array();
			//echo "group by division: ".$group_div;
			$teams_by_div =array();
			foreach($data as $row)
			{
				$select= $row->select;
				$div_id =$row->division_id;
				
				$standings = $this->schedule_model->get_season_div_standings($season_id,$div_id);
				$count_standings = count($standings);
				//no number means use all
				if($select==''||$select==null|| !is_numeric($select)) $select = $count_standings;
				//otherwise go up to selected, if there are enough teams
				$limit = min($select,$count_standings);
				//echo "use # selected".$select. "or a reduced limit ".$limit."\n";
				if($limit!=0)
					$teams_by_div[$div_id]=array();			
				for($i=0;$i<$limit;$i++)
					$teams_by_div[$div_id][]=array('team_id'=>$standings[$i]['team_id'],
							      	             'team_name'=>$standings[$i]['team_name']); //for elim
			}
			$games = array();
	
	
			
			$num_divs = count($teams_by_div);
			
			
			echo "TODO: elim sch, where ".$rounds." losses means yorue out,#divs= ".$num_divs;
			//var_dump($teams_by_div);
			
			
			
			//if num_divs == 1, then great just go down this list and make the elim tourn. 
			
		}//end of elim 
		
		
	}
	
	

	

	//this probably isnt used anymore, mostly thisis saved in post_create_incomplete in 2.0 
	public function post_current_season()
	{
		$id=$this->input->post('season_id');
		$s =$this->input->post('season_start');
		$e =$this->input->post('season_end');
		

		echo $this->schedule_model->s_update_current_season($id,$s,$e);
		
	}
	public function json_current_season()
	{
		$this->result->json($this->schedule_model->s_get_current_season());
	}

	
	public function json_match_datesets()
	{
		$match_pk = (int)$this->input->post('match_pk');
		
		//echo "json_match_datesets = ".$match_pk;
		if($match_pk<=0)
		{
			//this is error, pk >= 1 allowed only
			$this->result->json(array());
			
		}
		else
			$this->result->json($this->schedule_model->s_get_match_ds(($match_pk)));
	}
	public function post_update_match_dateset()
	{
		$match_pk = (int)$this->input->post('match_pk');
		$is_used  = $this->input->post('is_used');
		$dateset_pk = (int)$this->input->post('dateset_pk');
		//this should insert or update the true/false	
		echo $this->schedule_model->s_update_match_ds($match_pk,$dateset_pk,$is_used);
	}
	public function post_save_match()
	{
		//create new
 
		$f_id = (int)         $this->input->post('first_div_id');
		$s_id = (int)         $this->input->post('second_div_id');
		//$mg   = (int)         $this->input->post('match_games');
		$mr   = (float)       $this->input->post('match_rounds');
		//$pk   = (int)         $this->input->post('match_pk');
		$ed   = $this->input->post('enforce_dates');
		$er   = $this->input->post('enforce_rounds');
		if($f_id <=0|| $s_id<=0 ) 
		{
			echo -1;
			return;
		}
		echo $this->schedule_model->s_insert_match($f_id,$s_id,$er,$ed,$mr);//return new or existing pk
	}
	
	public function post_match_dates()
	{
		$pk   = (int)$this->input->post('match_pk');
		$dates=json_decode($this->input->post('date_array'));
		
		echo $this->schedule_model->s_save_match_dates($pk,$dates);
	}
	
	public function json_match_dates()
	{
		$pk   = (int)$this->input->post('match_pk');
		
		$this->result->json($this->schedule_model->s_get_match_dates($pk));
	}
	public function post_update_match()
	{
		$pk   = (int)$this->input->post('match_pk');
		$match_rounds   = (int)$this->input->post('match_rounds');
		$ed   = $this->input->post('enforce_dates');
		$er   = $this->input->post('enforce_rounds');
 
		echo $this->schedule_model->s_update_match_data($pk,$er,$ed,$match_rounds);
	}


	public function post_delete_match()
	{
		$match_pk = $this->input->post('match_pk');

		echo $this->schedule_model->s_delete_match($match_pk);
	}
	
	public function json_matches()
	{
		$this->schedule_model->_recalc_match_stats();
		
		$this->result->json($this->schedule_model->s_get_match_set());	
	}
	
	public function post_date_sets()
	{
		$set_name  = rawurldecode($this->input->post('set_name'));
		$slot_name = rawurldecode($this->input->post('slot_name'));
 
		$data = json_decode(rawurldecode($this->input->post('data')));
		$type = $this->input->post('type');
		if($type=='d')
		{  
			//echo "DATE data to ".$set_name."\n";
			$date_array=array();
			foreach($data as $date)
			{  
				$raw_date = substr($date,0,10);
				$fancy_date = date('D M d, Y',strtotime($raw_date));
				$date_array[]=$fancy_date;	
			}
			$total=count($date_array);
			//bool to int
			$success=(int) $this->schedule_model->s_insert_dateset_dates($set_name,$slot_name,$date_array); 	
			echo $total*$success;
		}
		else if($type=='v')
		{
			$rnd = $this->input->post('r');
			if($rnd || $rnd=='true'||$rnd==1)
				$rnd='t';
			else
				$rnd='f';
			$venue_array=array();
			foreach($data as $oVenue)
			{//venue id, name, and ??rank given
				$venue_array[]= array(  'venue_id'  =>$oVenue->venue_id,
										'venue_name'=>$oVenue->venue_name,
										'venue_rank'=>$oVenue->venue_rank, 
										'is_used'   =>$oVenue->is_used );
			}
			$total= count($venue_array);
			//bool to int
			$success=(int) $this->schedule_model->s_insert_dateset_venues($set_name,$slot_name,$venue_array,$rnd);						 
			echo $total*$success;
		}
		else echo '-100';
	}
 
	public function post_dateset_rules()
	{
		$dateset_pk = (int)$this->input->post('dateset_pk');
		$w  = rawurldecode( $this->input->post('ds_warmup'));
		$t  = rawurldecode($this->input->post('ds_teardown'));
		
		$ds_max_btw  = rawurldecode( $this->input->post('ds_max_btw'));
		$ds_min_btw  = rawurldecode($this->input->post('ds_min_btw'));	
		
		
		$min_slot = (int)$this->input->post('min_slot');
		$max_slot = (int)$this->input->post('max_slot');
		$min_day  = (int)$this->input->post('min_day');
		$max_day  = (int)$this->input->post('max_day');
		if(!$min_slot) $min_slot="";
		if(!$max_slot) $max_slot="";
		if(!$min_day) $min_day="";
		if(!$max_day) $max_day="";
			
		if($max_day && $min_day)
		{
			if($max_day < $min_day)
			{
				//swap
				$swap = $min_day;
				$min_day = $max_day;
				$max_day = $swap;
			}
		}	
		if($max_slot && $min_slot)
		{
			if($max_slot < $min_slot)
			{
				//swap
				$swap = $min_slot;
				$min_slot = $max_slot;
				$max_slot = $swap;
			}
		}		
		if(!$ds_max_btw) $ds_max_btw = "0:00";
		if(!$ds_min_btw) $ds_min_btw = "0:00";//defaults
		$active=$this->input->post('is_active');
		if(!$active) $active='f';
		
		echo $this->schedule_model->s_insert_ds_rules($dateset_pk,$w,$t,$active,$ds_max_btw,$ds_min_btw,$min_slot,$max_slot,$min_day,$max_day);
	}
	
	public function json_dateset_rules()
	{
		$dateset_pk = (int)$this->input->post('dateset_pk');
		$rules=$this->schedule_model->s_get_ds_rules($dateset_pk);
		if($rules['is_active']=='t')$rules['is_active']=true;
		else $rules['is_active']=false;
		$this->result->json($rules);	
	}
	
	/**
	* save (create or update) global rules for schedule
	* 
	*/
	public function post_global_rules()
	{
		$g_min=(int)$this->input->post('min');//global counts
		$g_max=(int)$this->input->post('max');			
		$len=rawurldecode($this->input->post('len'));			
		$w=rawurldecode($this->input->post('warmup'));			
		$t=rawurldecode($this->input->post('teardown'));	
		$team_buf_max=rawurldecode($this->input->post('max_btw'));
		$team_buf_min=rawurldecode($this->input->post('min_btw'));		
		
		$venue_distance = (int)$this->input->get_post('venue_distance');
		$facility_lock = $this->input->get_post('facility_lock');
		
		$facility_lock = ($facility_lock=='t' || $facility_lock=='true'||$facility_lock=='on') ? true : false;//redundant but for readability
		
		
		
		
		if(!$team_buf_max) $team_buf_max="0:00";
		if(!$team_buf_min) $team_buf_min="0:00";//defaults
		
		if($g_max < $g_min && $g_max!=0)
		{//swap of course
			$swap =$g_max;
			$g_max=$g_min;
			$g_min=$swap;
		}
		echo $this->schedule_model->s_update_global_rules($g_min,$g_max,$len,$w,$t,$team_buf_max,$team_buf_min
				,$venue_distance,$facility_lock);//save default
	}
	
	
	/**
	* get rules for active schedule
	* 
	*/
	public function json_global_rules()
	{		
		$g=$this->schedule_model->s_get_global_rules();
		$g['max_disabled'] = ($g['max']==0);//if zero, disabled==true
		$g['min_disabled'] = ($g['min']==0);//else, enabled is ok
		
		$g['len_minutes'] = $this->scheduler->timeToMinutes($g['len']);
		
		$this->result->json($g);
	}
 
 
 	/**
 	* create or update single dateset 
 	* 
 	*/
	public function post_dateset_info()
	{
		$set_name = rawurldecode($this->input->post('set_name'));
		$start    = rawurldecode($this->input->post('start_time'));
		$end      = rawurldecode($this->input->post('end_time'));
		$hex      = rawurldecode($this->input->post('hexcol'));
		$dateset_pk =(int) $this->input->post('dateset_pk');
		if(!$set_name) $set_name='No Name';
		if(!$dateset_pk || $dateset_pk==-1)
		{
			//create 
			echo $this->schedule_model->s_insert_dateset($set_name,$start,$end,$hex);
		}
		else
		{ 
			echo $this->schedule_model->s_update_dateset($dateset_pk,$set_name,$start,$end,$hex);
			//update
		}
	}
	/**
	* from create templates form
	* will create multilpe datesets based on days of week, season id, and 
	* the rules
	* uses the function:
	* _create_ds_template
	* 
	*/
	public function post_dateset_templates()
	{
		$row_types=array("first_","second_","third_",'full_');
		$weekday_types=array('u','m','t','w','r','f','s');
		 $this->scheduler->load_globalRules($this->schedule_model->s_get_global_rules());
		$this->scheduler->season = $this->schedule_model->s_get_current_season();
		//create a bunch for each row
		foreach($row_types as $prefix)
		{
			$weekdays=array();
			$end=null;
			if($prefix == "full_")
			{
				//then type doesnt exist as input parameter
				$type=4;
				$full_count = (int) $this->input->get_post('full_count');
				if($full_count==0)continue;//create zero of them
				$end= rawurldecode($this->input->get_post($prefix.'slot_end'));
				
			}
			else
			{
				$type = $this->input->get_post($prefix.'slot_type');
				$full_count=1;//create one copy of this only
				foreach($weekday_types as $wd)
				{
					if($this->input->get_post($prefix.$wd))
						$weekdays[]=$wd;
				}
				if(count($weekdays)==0) continue;//zero days, so do not create one
			}
			
			//everything has a start
			$start= rawurldecode($this->input->get_post($prefix.'slot_start'));
			
			$sets=$this->scheduler->create_ds_template($type,$weekdays,$start,$end,$full_count);
			
			foreach($sets as $new_set)
			{
				$new_pk= $this->schedule_model->s_insert_dateset($new_set[0],$new_set[1],$new_set[2],$new_set[3]);
				$this->schedule_model->s_insert_dateset_dates($new_pk,$new_set[4]);
			}
			
		}
 
	}
 
	/**
	* delete dateset by id
	* 
	*/
	public function post_dateset_delete()
	{
		$dateset_pk = (int)$this->input->post('dateset_pk');
		echo $this->schedule_model->s_delete_dateset($dateset_pk);
	}
	/**
	* get all datesets
	* also estimate games
	* 
	*/
	public function json_datesets()
	{		
		$ds=$this->schedule_model->s_get_datesets() ;
		$this->result->json($this->_estimate_dateset_games($ds));
	}
	/**
	* @author Sam
	* @access private
	* 
	* Computes estimated number of games based on all rules and dateset data
	* 
	* @return array of rules
	*  
	* @param mixed $ds
	*/
	private function _estimate_dateset_games($ds)
	{
		//get global rules every time
		//in case tehy change dsince last time
		$global=$this->schedule_model->s_get_global_rules();
		$len=$global['len'];
		$len_minutes=$this->scheduler->timeToMinutes($len);
		
		foreach($ds as &$set)
		{
			$s=$set['start_time'];
			$e=$set['end_time']; 
			$s=$this->scheduler->timeToMinutes($this->scheduler->timeMerToAstro($s));
			$e=$this->scheduler->timeToMinutes($this->scheduler->timeMerToAstro($e));
			
			$minutes=$e-$s; 
			
			$games=floor( ($minutes)/$len_minutes );
			$set['est_games']=$games * $set['venue_count']*$set['date_count'];
		}
		return $ds;
		
	}
	
	public function json_ds_dates()
	{		
		$dateset_pk  = (int)$this->input->post('dateset_pk');
		//$timeslot_pk = (int)$this->input->post('timeslot_pk');

		$this->result->json($this->schedule_model->s_get_dateset_dates($dateset_pk));
	}


	public function json_ds_dates_used_external()
	{
		$dateset_pk  = (int)$this->input->post('dateset_pk');
		
		$this->result->json($this->schedule_model->s_get_ds_dates_used_external($dateset_pk));
		
	}
	

	
 
	
	/**
	* 
	* count number of existing and number of valid fields
	*/
	public function get_dateset_summary_status()
	{
		$table = $this->schedule_model->s_get_ds_summary();//just get, do not calculate again
		$result=array();
		$result['conflicts']=0;
		$result['total']=count($table);
		foreach($table as $row)
		{
			$result['conflicts']+=$row['conflict'];
		}
		$this->result->json($result);
	}
 
	
	/**
	* return all summary records with matching venue id
	* 
	*/
	public function json_dateset_venuefilter()
	{
		$venue_id = $this->input->post('venue_id');
		$filtered=$this->schedule_model->s_get_ds_summary_by_venue($venue_id);
		$this->result->json($filtered);
	}
	public function json_dateset_filter()
	{
		$date = $this->input->post('date');
	
		$filtered= $this->schedule_model->s_get_ds_summary_by_date($date);

		$this->result->json($filtered);
	}
 
 
 
	/**
	* creates a new schedule 
	* of type incomplete in database
	* 
	*/
	public function post_file_save()
	{
		$name=rawurldecode($this->input->post('name'));
		$memo=rawurldecode($this->input->post('memo'));
		$season_id=$this->input->post('season_id');
		$p   =$this->input->post('p');
 
		
		//if($p=='t') $p=true; else $p=false;
		if(!$memo|| $memo=='null') $memo='Auto-Save';
		
		$user= $this->permissions_model->get_active_user();
		$owner=$this->permissions_model->get_active_org();
		
		if(!$p)$p='f';
		
		echo $this->schedule_model->file_schedule_session($memo,$p,$user,$owner,null,$name,$season_id);//schedule_id==null:: create new record
	}
	public function post_file_update()
	{
		//$private   =$this->input->post('p');
		$private = 'f';
		$memo = rawurldecode($this->input->post('memo'));
		$session_id=$this->input->post('session_id');
		
		if(!$memo|| $memo=='null') $memo='Auto-Save';
		
		$user  = $this->permissions_model->get_active_user();
		$owner = $this->permissions_model->get_active_org();
		
		echo $this->schedule_model->file_schedule_session($memo,$private,$user,$owner,$session_id,$this->schedule_model->s_get_schedule_id());
	}
	
	public function post_file_load()
	{
		$session_id   =(int)$this->input->post('session_id');
		$user= $this->permissions_model->get_active_user();
		$owner=$this->permissions_model->get_active_org();
		
		$result = $this->schedule_model->load_file_session($session_id);
 
		if( count($result)>0 &&  isset($result[0]['json_data']))
		{
			$json = $result[0]['json_data'];
			
			
			$data = json_decode($json,true);// save everythign into session data
			
			
			$sn_id = $data['season_id'];
			//echo "$sn_id";
			$season = $this->season_model->get_season_data($sn_id);
			if(count($season))
			{
				$plain='Y/m/d';
				$s=$season[0];
				$data['season_id']   =$s['season_id'];
				$data['season_start']=date($plain,strtotime($s['effective_range_start']) );
				$data['season_end']  =date($plain,strtotime($s['effective_range_end']) );
				
			}
			
	 
			$this->schedule_model->load_file_schedule_session($data);
			//$this->schedule_model->s_update_current_season($data['season_id'],$data['season_start'],$data['season_end']);
			//var_dump($this->schedule_model->s_get_current_season());
			$return = array();
			
			$return['season_id']     = $data['season_id'];
			$return['season_start']  = $data['season_start'];
			$return['season_end']    = $data['season_end'];
			$return['schedule_name'] = $result[0]['schedule_name'];//from json
			$return['schedule_id']   = $result[0]['schedule_id'];//from json
			$return['user_memo']     = $result[0]['user_memo'];//from the record
			$return['is_private']    = $result[0]['is_private'];
			
			//return everything to js->not all is neeed..?
			
		}
		else
		{ //if a problem: empty array
			$return=array();
		}
		$this->result->json($return);//is 
		
	}
	public function post_file_delete()
	{
		$s_id = (int)$this->input->post('session_id');
		$user = (int)$this->permissions_model->get_active_user();
		
		echo $this->schedule_model->delete_file_session($s_id,$user);
	}
	public function post_file_get()
	{
		$user= $this->permissions_model->get_active_user();
		$owner=$this->permissions_model->get_active_org();
		
		$saves=$this->schedule_model->get_file_sessions($user,$owner);
		$short_fmt='F j, Y';
		$long_fmt='F j, Y g:i A';
		foreach($saves as &$s)
		{
			$s['created_on'] =date($short_fmt,strtotime($s['created_on']));	
			$s['modified_on']=date($long_fmt, strtotime($s['modified_on']));	
		}
		$this->result->json($saves);
	}
	

	public function post_file_clear()
	{
		echo $this->schedule_model->clear_file_schedule_session();
	}
	



    
    private function prepare_for_display()
    { 
    	//grab schedule from s_ session data
    	$sch=$this->schedule_model->s_current_session_schedule();
		$games=array(); 
		foreach($sch as $g)
		{
			if($g['timeslot']<0){continue;}
			
			if(!isset($g['venue_name'])) $g['venue_name']='';
			if(isset($g['game_date']) && $g['game_date'] && strtotime($g['game_date']) !== false && $g['game_date'] != "Not Scheduled")
			{
				$games[]=$g;
			}
		}
		//save the same thing back into the session
		$this->schedule_model->s_overwrite_session_schedule($games);
		
		
    }
	/**
	* create a schedule, then return it as json
	* heavy use of scheduler library
	* 
	*/
	public function post_schedule_data()
	{
        //$o = json_decode(rawurldecode($this->input->post('data')),true);
        $this->_create_schedule();   
          
        flush();
        echo "#*#"; 
        //after create, spit it back out
        $this->prepare_for_display();
        
        $this->result->json($this->scheduler->getWarnings());
        echo "#*#"; 
        $this->result->json($this->schedule_model->s_current_session_schedule());
    } 
    
    /**
    * output the createdGames session schedule
    * with the given sort format
    * into the view
    * 
    */
    public function html_session_schedule()
    {
    	//preformat the existing session schedule, same as what happens in teh 'generate schedule' button
    	$this->prepare_for_display();
		
		$sch=$this->schedule_model->s_current_session_schedule();
		
		$sort=$this->input->get_post('sort');
		//sort the schedule by input type
		$sch = $this->scheduler->sort_schedule_by($sch,$sort);
		
		if($sort == 'd' || $sort=='date') $sch = $this->scheduler->format_schedule_dates($sch);
		
		$groups = array('d'=>'game_date','v'=>'venue_name','t'=>'home_name');
		//pass to view
		$data = array();
		$data['games']=$sch;
		$data['group_by'] = $groups[$sort];
		$data['border']=1;//border on or off, zero is off
		echo $this->load->view('schedule/simple_html.php',$data,true);
    }
 
    

    /**
    * MAIN algorithm for season/league scheduler
	* get schedule data and pass to functions 
	* in library in order
	* 
	* 
	*/
    private function _create_schedule($o=null)
    {  
    	/******************** load initial data into scheduler library *************************/
    	$this->scheduler->tsRules = $this->schedule_model->s_get_ds_rules_arrays();
  	    $used_divids=$this->schedule_model->s_get_unique_match_divids();
  	    
		$this->schedule_model->_recalc_match_stats();
	    $matches=$this->schedule_model->s_get_match_set();
 
    	$season = $this->schedule_model->s_get_current_season();
	    $this->scheduler->teamDates=array();
	    $this->scheduler->teamList = $this->schedule_model->team_list_for_div_array($used_divids,$season['season_id']);
	    $this->scheduler->teamDivId = array();
	    
	    # TODO: move this to some sort of importTeams function in teh scheduler lib
	    foreach($this->scheduler->teamList as $div=>$tm_array)
	    {
	     	foreach($tm_array as $tm)
		     {
	     		 $teamid=$tm['team_id'];
	     		  
				 $this->scheduler->teamDivId[$teamid]=$div;
				 
			    if(!isset($this->scheduler->teamDates[$teamid]))
					$this->scheduler->teamDates[$teamid] = $this->teams_model->get_team_exceptions($teamid);
				 
		     }
	    }
	     $divNames = array();
	     foreach($used_divids as $divid)
	     {
			 $divNames[$divid] = $this->divisions_model->get_division_extended_name($divid);
	     }
	     $this->scheduler->divNames = $divNames;
 
    	$this->scheduler->load_globalRules($this->schedule_model->s_get_global_rules());
 
    	$summary = $this->schedule_model->get_dateset_summary();
 
 
 		
	    /*********** data pre processsing functions ***********************************/ 
 		// thius is where we pre-examine rules and everything
	    $this->scheduler->pre_process_matches($matches);
	    
 
    	//we have finished loading all data
    	//call the first major scheduler functions
	    $num_created = $this->scheduler->create_timeslots($summary);
 
       	$this->scheduler->sort_timeslots_by_stamp_ven();
  
        //populate distance matrix 
        $this->scheduler->pre_calculate_venue_distance();
        
		 
	    
		$this->scheduler->pre_process_game_timeslot_cmp();
	    
	    
	    /*********** main scheduling  algorithms ****************************/
 
		$m = $this->scheduler->create_matches($num_created);    
		
		
		
 		$this->scheduler->validate_timeslot_rules($this->schedule_model->s_get_datesets_indexed()); 
 		
		
		
		//always balance themn
	          
	    $this->scheduler->balance_homeaway();
		  
 
   		$num_unassigned=$this->scheduler->assign_games_to_timeslots(); 
   		//calculates audit statistics
   		
   		//post processing step to validate. may create cames or modify things, so
   		//statistics MUST come after
   		 
	    /*********** Post processsing and Validation *********************************************/
	    
   		//create will validate as it goes, but also validate at the end to be sure
        $this->scheduler->validate_schedule();
   		 
   		//caluclate audits, and save them using model
	    $this->schedule_model->s_save_audits( $this->scheduler->audit_schedule());
 
    }//end create_schedule
    
   
    public function post_rename()
    {
		$s_id = (int)$this->input->post('schedule_id');
		$name = rawurldecode($this->input->post('name'));
		echo $this->schedule_model->update_schedule_name($s_id,$name);
		
		$pub=$this->input->post('pub');//one character either 't' or 'f'
		if($pub)
		{
			echo $this->schedule_model->update_publish_schedule($s_id,$pub);
		}

    }
    
    public function post_delete()
    {
		$s_id = (int)$this->input->post('schedule_id');
		$user=$this->permissions_model->get_active_user();
		echo $this->schedule_model->delete_schedule($s_id,$user);		
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
			if($this->scheduler->timeslots_overlap($date,$start,$end,$usedstart,$usedend))
			{
				return true;//we found a conflict so stop looking
			}
        	//else echo "No conflict found yet, so keep checking the rest\n";                                    
		}
		//loop is over, no problems found
		return false;
    	
		
    }
 
 
	
    public function json_schedule_venues_fac()
    {
		$sch=(int)$this->input->post('schedule_id');
		$this->result->json($this->schedule_model->get_schedule_venues_fac($sch));
    }
     
    
    public function post_create_incomplete()
    {
    	$user=$this->permissions_model->get_active_user();
    	$org =$this->permissions_model->get_active_org();
		$season_id=(int)$this->input->post('season_id');
		$game_length=rawurldecode($this->input->post('len'));
		$wu=rawurldecode($this->input->post('warmup'));
		$td=rawurldecode($this->input->post('teardown'));
		
		
		$team_buf_max=rawurldecode($this->input->post('max_btw'));
		$team_buf_min=rawurldecode($this->input->post('min_btw'));
		
		
		$gmin=(int)$this->input->post('min');
		$gmax=(int)$this->input->post('max');
		
		$venue_distance = (int)$this->input->get_post('venue_distance');
		$facility_lock = $this->input->get_post('facility_lock');
		
		$facility_lock = ($facility_lock=='t' || $facility_lock=='true'||$facility_lock=='on') ? true : false;//redundant but for readability
		
		
		$sch_name=rawurldecode($this->input->post('schedule_name'));
		if(!$sch_name) $sch_name="My Schedule";
		$wizard_type = 3;
		$published='f';
		$schedule_id = $this->schedule_model->insert_schedule($sch_name,$user,$org,$published,$wizard_type);
		$this->schedule_model->s_set_schedule_id($schedule_id);
		$season = $this->season_model->get_season_data($season_id);
		
		//create all default data here. currently in session data (any method with s_  )
		$this->schedule_model->s_update_current_season($season_id,$season[0]['effective_range_start'],$season[0]['effective_range_end']);
		
		$this->schedule_model->s_update_global_rules($gmin,$gmax,$game_length,$wu,$td,$team_buf_max,$team_buf_min
				,$venue_distance,$facility_lock);
		
		//$_SESSION['season_id']=$season_id;
		$this->_setup_default_matches_table($season_id);
		
		//returns session id
		echo $this->schedule_model->file_schedule_session('post_create_incomplete','f',$user,$org,null,$schedule_id);
		
    }
    /**
    * convenience method: for new schedules, will grab all existing divisions
    * and add matches for them by default
    * 
    * @param mixed $season_id
    */
    private function _setup_default_matches_table($season_id)
    {
    	$divs=$this->divisions_model->get_parent_divisions($season_id);
 
    	foreach($divs as $div)
		{
			$this->schedule_model->s_insert_match($div['division_id'],$div['division_id']);
			
		}
    }
    
    
    public function post_ds_dates()
	{
		$pk  = (int)$this->input->post('dateset_pk');
		$date_array = json_decode($this->input->post('date_array'));

		echo $this->schedule_model->s_insert_dateset_dates($pk,$date_array); 	
	}
	
	/**
	* save set of venues to this dateset
	* 
	*/
	public function post_ds_venues()
    {
		$pk  = (int)$this->input->post('dateset_pk');
		//$facility_id  = (int)$this->input->post('facility_id');
		$v_array = json_decode($this->input->post('venue_array'));
		
		$this->schedule_model->s_insert_dateset_venues($pk,$v_array);//array of ids
    }
    
    /**
    * delete given dateset from active sched
    * 
    */
    public function post_delete_ds_venues()
    {
		$pk  = (int)$this->input->post('dateset_pk');
		$v_array = json_decode($this->input->post('venue_array'));
		
		
		
		$this->schedule_model->s_delete_dateset_venues($pk,$v_array);//array of ids
    }
    
    /**
    * get the venues saved in the given dateset
    * append with facility namne
    * 
    */
    public function json_ds_venues()
	{
		$dateset_pk  = (int)$this->input->post('dateset_pk');
		//$timeslot_pk = (int)$this->input->post('timeslot_pk');
		$v_ids=$this->schedule_model->s_get_dateset_venues($dateset_pk);
		$result=array();
		foreach($v_ids as $id)
		{
			$v = $this->facilities_model->get_venue_details($id);
			$v = $v[0];
			$v['long_name'] = $v['facility_name']." : ".$v['venue_name'];
			$result[] = $v;
		}
		$this->result->json($result);
	}
	
	
	
	public function json_dateset_overflow()
	{
		$rules = $this->schedule_model->s_get_global_rules();
		$ts    = $this->schedule_model->s_get_datesets();
		$errors = $this->scheduler->compare_game_datesets_overflow($ts,$rules['len'],$rules['warmup'],$rules['teardown']);
		$this->result->json($errors);
	}
	
	
  
}// ends class Schedule 

