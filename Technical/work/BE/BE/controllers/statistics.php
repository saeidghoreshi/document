<?php
require_once('endeavor.php');
class Statistics extends Endeavor
{
	
	/**
	* 
	* @var schedule_model
	*/
    public $schedule_model;
    
    /**
	* 
	* @var games_model
	*/
    public $games_model;//important, do not change this
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
	* 
	* @var leagues_model
	*/
    public $leagues_model;
    
    /**
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
        $this->load->model('games_model');
        $this->load->model('leagues_model');
        $this->load->model('facilities_model');
        $this->load->model('divisions_model');
        $this->load->model('season_model');
        $this->load->model('teams_model');
        $this->load->model('statistics_model');
		$this->load->library('result');
    }
    
    public function json_user_statistics()
    {
		$this->result->json($this->statistics_model->get_user_statistics());
    }
    public function json_display_statistics()
    {
		$this->result->json($this->statistics_model->get_display_statistics());
    }
   // Window functions // // // // // // // // // // // // //
    private function load_window()
    {
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/");
        $this->window->set_js_path("/assets/js/components/standings/");
    }
    
    public function window_managestandings()
    {
        $this->load_window();

        $this->window->add_js('forms/standings.js');
        $this->window->add_js('forms/copy_standings.js');
        $this->window->add_js('forms/display.js');
        $this->window->add_js('windows/copy_standings.js');
        $this->window->add_js('windows/standings.js');
        $this->window->add_js('windows/display.js');
        
        $this->window->add_js('grids/standings.js');
        $this->window->add_js('grids/statistics.js');
        $this->window->add_js('grids/wildcard.js');
        $this->window->add_js('grids/calculated.js');
        
        $this->window->add_js('controller.js');
        
        $this->window->set_header('Manage Standings');
        $this->window->set_body($this->load->view('statistics/standings.php',null,true));

        $this->window->json();
    }
    
    

    
        
    /**
    * recalculate and get based on input
    * 
    */
    public function json_scheduleresults()
    {
		$season_id   = $this->input->post('season_id');
		$schedule_id = $this->input->post('schedule_id');
		/*
		$game_divs	 = json_decode($this->input->post('game_divs'));
		//$rank_divs	 = json_decode($this->input->post('rank_divs'));
		$winPts		 = (float)$this->input->post('winPts');
		$lossPts	 = (float)$this->input->post('lossPts');
		$tiePts	     = (float)$this->input->post('tiePts');
		$hth	     = $this->input->post('hth');
		$same_div_only=$this->input->post('same_div_only');
		$leadCut     = (int)$this->input->post('leadCut');*/
		
		$rank_type_id= (int)$this->input->post('rank_type_id');
		
		//$_SESSION['sch_statistics_wildcard']=$leadCut;//??is used??zero means nope, none cut so no wildcard
		
		$org =$this->permissions_model->get_active_org();
		
		/*
		if($insert_default == 't')
		{//$stat_ids = json_decode($this->input->post('stats'));	
		//default to win percentage if nothing
		//if($stat_ids == "" || $stat_ids==null ||  $stat_ids=='null') $stat_ids= array('win_perc');
		
		
		//$insert_default = $this->input->post('insert_default');
			$user=$this->permissions_model->get_active_user();
			$owner=$this->permissions_model->get_active_org();
			
			$rank_stats=array();
			foreach($stat_ids as $i=>$stat)
			{
				$rank_stats[$i+1]=$stat;//make sure nothing has index zero, so reflects actual numeric rank, which makes zero===false
			}
			$calc_pts      = (int) array_search('calc_pts',      $rank_stats,true);//returns false=>zero if not found
			$win_perc      = (int) array_search('win_perc',      $rank_stats,true);//returns false=>zero if not found
			$rf            = (int) array_search('points_scored', $rank_stats,true);//returns false=>zero if not found
			$ra            = (int) array_search('points_against',$rank_stats,true);//returns false=>zero if not found
			$rd            = (int) array_search('run_diff',      $rank_stats,true);//returns false=>zero if not found
			$wins          = (int) array_search('total_wins',    $rank_stats,true);//returns false=>zero if not found
			$losses        = (int) array_search('total_losses',  $rank_stats,true);//returns false=>zero if not found
			//echo "$wc ; abnd hth $hth savedd?###";
			
			$csv_game='';
			foreach($game_divs as $id)
				$csv_game.=$id.",";
			//$csv_rank='';
			//foreach($rank_divs as $id)
			//	$csv_rank.=$id.",";
			
			echo $this->statistics_model->delete_league_pref($rank_type_id);
			
			echo $this->statistics_model->insert_league_preferences($rank_type_id,$user,$owner,$winPts,$lossPts,$tiePts,
			$leadCut,$win_perc,$calc_pts,$rf,$ra,$rd,$wins,$losses,$hth,$csv_game,$same_div_only);

			echo "if -11 or 11 then saved,rank type $rank_type_id   ###";
			$this->result->json(array());
		
			return;
		}
		$hth = 	($hth == 't' );//convert to boolean	
		$same_div_only = 	($same_div_only == 't' );
		
		*/
		
		$result = json_encode($this->calculate_stats($rank_type_id,$season_id,$org)); 
		echo "###".$result;
    }
    public function json_calculate_division()
    {
		$season_id   = (int)$this->input->post('season_id');
		
		$rank_type_id= (int)$this->input->post('rank_type_id');
		$division_id = (int)$this->input->post('division_id');

		//$org =$this->permissions_model->get_active_org();
		
		
		//$result = json_encode($this->calculate_stats($rank_type_id,$season_id,$org));//this is triggered by the _internal version
		$this->calculate_stats_internal($rank_type_id,$season_id);
		//yes the order of arguments is reveresed in these two functions, confusing right? dont want to fix it in 
		//case it breaks stuff
		echo "\nAFTER internal";
		$result=$this->statistics_model->get_standings_by_order($season_id,$rank_type_id); 
		echo "\nfound stats".count($result);
		$wildcard=$this->statistics_model->get_rank_wildcard_div($rank_type_id,$division_id);
		echo "\nfound wc".count($wildcard);
		$wc=0;
		if(count($wildcard))//if a record was found, consider how many to cut
		{
			$wc=$wildcard[0]['wildcard_teams'];
		}
		$filter=array();
		foreach($result as $row)
		{
			if($row['division_id']==$division_id)
			{
				if($wc<=0)
					{$filter[]=$row;}
				else//hide for wildcard
					{$wc--;}
			}
		}
		
		echo "###".json_encode($filter);
    }
    public function json_calculate()
    {
		$season_id   = (int)$this->input->get_post('season_id');
		
		$rank_type_id= (int)$this->input->get_post('rank_type_id');

		//$org =$this->permissions_model->get_active_org();
		
		
		//$result = json_encode($this->calculate_stats($rank_type_id,$season_id,$org));//this is triggered by the _internal version
		$this->calculate_stats_internal($rank_type_id,$season_id);
		//yes the order of arguments is reveresed in these two functions, confusing right? im too lazy to fix it
		$result=$this->statistics_model->get_standings_by_order($season_id,$rank_type_id); 
		$this->format_decimals(&$result);
		echo "###".json_encode($result);
    }
    
    
    private function format_decimals(&$result,$precision=3)//default to 3
    {
		$is_int =array('team_id','division_id','season_id','GP','W','L','T','rank','rank_type_id','RF','RA','RD');//these are integers, 
		foreach($result as &$r)
		{
			
			foreach($r as $id=>$v) if(is_numeric($v))
			{
				echo $id.",";
				if(in_array($id,$is_int))
				{
					$r[$id]=(int)$v;
				}
				else
				{					
					$r[$id]=number_format($v,$precision,'.','');
				}
			}
		}
		
    }
    
    /**
    * this mirrors the action of json_scheduleresults and json_calculate, and calls calculate_stats followed by inserts, but 
    * uses internal league pref data only. triggered by new game results being verified
    * 
    */
    private function calculate_stats_internal($rank_type_id,$season_id)
    {

		$user=$this->permissions_model->get_active_user();
		$owner=$this->permissions_model->get_active_org();
		$league_id = $this->leagues_model->get_league_from_org($owner);

		echo $this->statistics_model->reset_standings($season_id,$rank_type_id);//moved reset above calculate
		
		
		$stats = $this->calculate_stats($rank_type_id,$season_id,$owner);
		//$st_type=$prefs['rank_type_id'];
		//echo"RESET STANDINGS RETURNED THIS::";
		//echo" found this many ".count($stats); 
		foreach($stats as $data)
		{
			//echo "_internal will insert:";
			$team_id=$data['team_id'];
			echo $team_id;
			if(!$team_id)
			{
				//echo "null team id error: var_dump=  ";
				//var_dump($data);
				continue;
			}


			if(!$data['gb'] || $data['gb']=="-"){	$data['gb']=0;}//keep it numeric in database, display characters come later
				
			echo $this->statistics_model->insert_standings($team_id,$season_id,$data['rank'],$rank_type_id);
			
			echo $this->statistics_model->insert_statistics($team_id,$season_id,1,$data['games_played'],$rank_type_id);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,2,$data['total_wins'],$rank_type_id);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,3,$data['total_losses'],$rank_type_id);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,4,$data['calc_pts'],$rank_type_id);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,5,$data['gb'],$rank_type_id);
			
			echo $this->statistics_model->insert_statistics($team_id,$season_id,6, $data['points_scored'],$rank_type_id);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,7, $data['points_against'],$rank_type_id);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,8, $data['run_diff'],$rank_type_id);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,9, $data['win_perc'],$rank_type_id);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,10,$data['total_ties'],$rank_type_id);
		}
    }
    
    /**
    * the meat function
    * this builds stats for evrything in the season
    * @author SB
    * 
    * @param mixed $rank_type_id
    * @param mixed $season_id
    * @param mixed $league_e_org_id
    * @return mixed
    */
    private function calculate_stats($rank_type_id,$season_id,$league_e_org_id)
    {
    	$basic=$this->statistics_model->get_rank_type_info($rank_type_id)	;//this has  points per WLT 
    	//echo "calculate_stats season=".$season_id;
		//$lossPts;		//$tiePts //	$lossPts	
		$winPts= $basic[0]['pts_per_win'];
		$lossPts=$basic[0]['pts_per_loss'];
		$tiePts= $basic[0]['pts_per_tie'];
		$divs = $this->statistics_model->get_rank_divisions($rank_type_id);//has wildcard_count, samedivs only, csv 
		$game_divs=array();
		foreach($divs as $d)
		{
			if($d['is_used']=='t' ||$d['is_used']=='true'||$d['is_used']===true )
			{
				$game_divs[]=array($d['h_division_id'],$d['a_division_id']);
			}
			//if not used dont store it
		}
		
		//$game_divs=explode(",",$divs[0]['csv_division_ids']);
		//$same_div_only = ($divs[0]['same_divs_only']=='t');//not used anymore, now stored in division pairs, not a global lockout

		//$leadCut = (int)$divs[0]['wildcard_count'];


		$rawScores = $this->statistics_model->get_valid_scores_season($season_id);

		$rawGames  = $this->games_model->get_games_by_season($season_id);
		
		$teamInfo  = $this->leagues_model->get_league_teams($league_e_org_id);
		$team_to_div = array();
		
		foreach($teamInfo as &$rec)
		{
			//var_dumP($rec['team_id']);
			$t=$rec['team_id'];
			$t_div = $this->divisions_model->get_team_division($t,$season_id);
			//var_dump($t_div);
			if(count($t_div)>0)
			{
				//$d=
				$team_to_div[$t] = $t_div[0]['division_id'];//$same_div_only
				//echo "team $t has div ".$t_div[0]['division_id']."\n";
			}
			else
			{
				//echo "warning: team division not found for team_id=$t \n";
				$team_to_div[$t]=-1;
				unset($rec);
			}
			 
				
		}

		
		$stats = array();
		$games = array();
		$headToHead=array();
		//echo "rawGames count = ".count($rawGames);
		foreach($rawGames as $g)
		{//index games by game id 
			$id = $g['game_id'];
			$games[ $id ] = $g;		
			$h=$g['home_id'];
			$a=$g['away_id'];
			if(!$id||!$h||!$a){continue;}//if any are null skip
			//echo "game id $id home $h away $a\n";
			//init empty stats records for each team
			if(!isset($team_to_div[$h])){$team_to_div[$h]=-1;}
			if(!isset($team_to_div[$a])){$team_to_div[$a]=-1;}
			if(!array_key_exists($g['home_id'] ,$stats )  )
				$stats[$h] = array("points_scored"=>0,"points_against"=>0,"win_perc"=>0,"total_wins"=>0,
											  "total_losses"=>0,"total_ties"=>0,"games_played"=>0,
											  "team_name"=>$g['home_name'],'team_id'=>$g['home_id'],'division_id'=>$team_to_div[$h],
											  "rank"=>0,'run_diff'=>0,"calc_pts"=>0,"gb"=>0);			
			if(!array_key_exists($g['away_id'] ,$stats )  )
				$stats[$a] = array("points_scored"=>0,"points_against"=>0,"win_perc"=>0,"total_wins"=>0,
											  "total_losses"=>0,"total_ties"=>0,"games_played"=>0,
											  "team_name"=>$g['away_name'],'team_id'=>$g['away_id'],'division_id'=>$team_to_div[$a],
											  "rank"=>0,'run_diff'=>0,'calc_pts'=>0,"gb"=>0);				
			$headToHead[$g['away_id']][$g['home_id']]['total_wins'] 	= 0;
			$headToHead[$g['home_id']][$g['away_id']]['total_wins'] 	= 0;
			$headToHead[$g['away_id']][$g['home_id']]['total_losses'] 	= 0;
			$headToHead[$g['home_id']][$g['away_id']]['total_losses'] 	= 0;	
			$headToHead[$g['away_id']][$g['home_id']]['calc_pts'] 		= 0;
			$headToHead[$g['home_id']][$g['away_id']]['calc_pts'] 		= 0;		
			$headToHead[$g['away_id']][$g['home_id']]['points_scored']  = 0;
			$headToHead[$g['home_id']][$g['away_id']]['points_scored']  = 0;	
			$headToHead[$g['away_id']][$g['home_id']]['points_against'] = 0;
			$headToHead[$g['home_id']][$g['away_id']]['points_against'] = 0;
			$headToHead[$g['away_id']][$g['home_id']]['total_ties'] 	= 0;//these two will be same
			$headToHead[$g['home_id']][$g['away_id']]['total_ties'] 	= 0;	
			$headToHead[$g['away_id']][$g['home_id']]['win_perc'] 		= 0;
			$headToHead[$g['home_id']][$g['away_id']]['win_perc']		= 0;
			$headToHead[$g['away_id']][$g['home_id']]['run_diff'] 		= 0;
			$headToHead[$g['home_id']][$g['away_id']]['run_diff'] 		= 0;
			$headToHead[$g['away_id']][$g['home_id']]['games_played'] 	= 0;
			$headToHead[$g['home_id']][$g['away_id']]['games_played'] 	= 0;
				
		}
		
		//echo "rawScores count = ".count($rawScores);
		foreach($rawScores as $result)
		{			
			//echo "var_dump NEW RESULT TO PROcESS\n";
 
			////TODO::  games_model count_team_game_exc 
 
			$game_id 	= $result['game_id'];			
 
			$home_score =(int)$result['home_score'];
			$away_score =(int)$result['away_score'];
			$home_id 	= $games[$game_id]['home_id'];
			$away_id 	= $games[$game_id]['away_id'];
			
			
			$home_exception = $this->games_model->count_team_game_exc($home_id,$season_id);
			
			$do_save_home = ($home_exception==0);// if no exceptions, than save is true (0 is true)
			
			$away_exception = $this->games_model->count_team_game_exc($away_id,$season_id);
			
			$do_save_away = ($away_exception==0);// zero is true, no reasons to skip
			
			//$do_save_hth = ($do_save_home && $do_save_away);
			 
			if($home_id == null || $away_id == null || $away_id == $home_id) continue; 
			$homediv = isset($team_to_div[$home_id]) ? $team_to_div[$home_id] : -1;
			$awaydiv = isset($team_to_div[$away_id]) ? $team_to_div[$away_id] : -1;
			//echo "game id $game_id home $home_id div $homediv away $away_id div $awaydiv  is scored at $home_score - $away_score\n";
			//echo "team $home_id div $homediv scored $home_score home VS \n";
			//echo "team $away_id div $awaydiv scored $away_score away\n";
			//skip this game and goto top of loop again if one of the following conditions happens:
			//if this division was selected (checkbox == yes)
			
			//test $game_divs in 2.0 method
			$use_them=false;
			foreach($game_divs as $div_array)
			{
				
				if(in_array($homediv , $div_array) && in_array($awaydiv,$div_array))
				{
					$use_them=true;
					break;
				}
			}
			if(!$use_them)
			{
				//echo "!!!!!!!!use_them is false$homediv, $awaydiv\n";
				continue;
			}
			//echo "!!!!!!!!test array assignments $homediv,$awaydiv, has passed the game_divs test, now move on to calculate \n";
			
			//if(!in_array($homediv , $game_divs) || !in_array($awaydiv,$game_divs)) continue;//1.0 method depreciated
			//or if user flagged 'same div only', and this is a cross-division game
			//if($same_div_only && $homediv != $awaydiv) continue;//in 2.0 $same_div_only flag doesnt exist
			//or if this game is somehow invalid, and has zero or one teams assigned to it
				//return $games[$game_id]."==id of Invalid Game, with null team";
			
			//next just compute  all basic stats, both globally and H2H  
			
		   //	if($do_save_away)   if($do_save_home)   if($do_save_hth)
			
			if($do_save_home){
			
			$stats[$home_id]['points_scored'] += $home_score;
			$stats[$home_id]['points_against']+= $away_score;
			
			$stats[$home_id]['run_diff']+= ($home_score - $away_score);
			
			$stats[$home_id]['games_played']++;
			
			$headToHead[$home_id][$away_id]['games_played']++;
			
			$headToHead[$home_id][$away_id]['points_against'] += $away_score;
			
			$headToHead[$home_id][$away_id]['points_scored']  += $home_score;	
			$headToHead[$home_id][$away_id]['run_diff'] += ($home_score - $away_score);		
			}
			//splitting into home and away sections
			
			$stats[$away_id]['points_scored'] += $away_score;		
			
			if($do_save_away){
				
			$stats[$away_id]['points_against']+= $home_score;
			
			$stats[$away_id]['run_diff']+= ($away_score - $home_score);
			
			$stats[$away_id]['games_played']++;
						
						
			$headToHead[$away_id][$home_id]['points_scored']  += $away_score;
						
			$headToHead[$away_id][$home_id]['points_against'] += $home_score;
					
			$headToHead[$away_id][$home_id]['run_diff'] += ($away_score - $home_score);
 
			$headToHead[$away_id][$home_id]['games_played']++;
			
			}
			
			if($home_score < $away_score)
			{//away team wins		
				//echo " away wins ";		
				if($do_save_home){
				$stats[$home_id]['total_losses']++;
				$stats[$home_id]['calc_pts'] = $stats[$home_id]['calc_pts']+ $lossPts;		//$tiePts //	$lossPts	
				$headToHead[$home_id][$away_id]['total_losses']++;
				$headToHead[$home_id][$away_id]['calc_pts']+=$lossPts;
				}
				
				if($do_save_away){
				
				
				$stats[$away_id]['total_wins']++;
				$stats[$away_id]['calc_pts'] = $stats[$away_id]['calc_pts']+ $winPts;
				
				
				$headToHead[$away_id][$home_id]['total_wins']++;
				$headToHead[$away_id][$home_id]['calc_pts']+= $winPts;
				
				}
				
			}
			else if($home_score > $away_score)
			{
				//echo "home team wins	";
				
				if($do_save_home){
				$stats[$home_id]['total_wins']++;	
				$stats[$home_id]['calc_pts'] = $stats[$home_id]['calc_pts']+ $winPts;	
				
				$headToHead[$home_id][$away_id]['total_wins']++;
				$headToHead[$home_id][$away_id]['calc_pts']+=$winPts;
				}
				
				if($do_save_away){
				$stats[$away_id]['total_losses']++;
				$stats[$away_id]['calc_pts'] = $stats[$away_id]['calc_pts']+ $lossPts;	
				
				$headToHead[$away_id][$home_id]['total_losses']++;		
				$headToHead[$away_id][$home_id]['calc_pts']+= $lossPts;
				//echo "home wins\n";
				}
			}
			else
			{//it was a tie
				
				if($do_save_home){	
				$stats[$home_id]['total_ties']++;	
 
				$stats[$home_id]['calc_pts'] = $stats[$home_id]['calc_pts']+ $tiePts;	
				$headToHead[$home_id][$away_id]['total_ties']++;
				$headToHead[$home_id][$away_id]['calc_pts']+=$tiePts;
				}
				
				if($do_save_away){
				$stats[$away_id]['total_ties']++;
				$stats[$away_id]['calc_pts'] = $stats[$away_id]['calc_pts']+ $tiePts;	
				
				$headToHead[$away_id][$home_id]['total_ties']++;
				$headToHead[$away_id][$home_id]['calc_pts']+=$tiePts;
				//echo "tie  ";		
				}			
			}
			
			//calc win percentage whetrher do_away was set or not - at worst this will be zero
			$headToHead[$away_id][$home_id]['win_perc'] = 
					$headToHead[$away_id][$home_id]['total_wins']/$headToHead[$away_id][$home_id]['games_played'];
			
					
					
			$headToHead[$home_id][$away_id]['win_perc']=
					$headToHead[$home_id][$away_id]['total_wins']/$headToHead[$home_id][$away_id]['games_played'];						
					
		}//end for scores loop
		
		//echo "win percentage next";
		//calculate win percentage
		$num_decimals = 3;
		$dec_point = ".";
		$thous_sep="";
		$sorted = array();
		$none_played=array();
		//calc win percentage
		foreach($stats as $id=>$s)
		{
			if($stats[$id]['games_played'] != 0 )//&& in_array($team_to_div[$id],$rank_divs))//rank_divs not used
			{
				$percent = $stats[$id]['total_wins'] / $stats[$id]['games_played'] ;
			}		
			else
			{
				$percent=0;
				//has played zero games
				
			}
			$stats[$id]['win_perc'] = number_format($percent,$num_decimals,$dec_point,$thous_sep);
			if($stats[$id]['games_played'])
				$sorted[]=$stats[$id];	//NEW include teams even if zero games are played
			else
				$none_played[]=$stats[$id];
		}
		//basic stats are all done		

		
		
		
		$leadCut=0;
		//echo "TODO get  leadCut on a per_div basis ";
		//$basic_stats = $this->statistics_model->get_rank_statistics_used_not_hth($rank_type_id);
		$stat_ids=array();
		$init_rank=true;
		$all_stats = $this->statistics_model->get_rank_statistics_used($rank_type_id);
		$debug=true;
		foreach($all_stats as $currrent_stat)
		{
			//var_dump($currrent_stat);
			if($currrent_stat['is_used'] =='f') continue;
			
			$stat_index = $currrent_stat['internal_index'];
			$stat_sort  = $currrent_stat['sort_class'];
			$stat_hth   = $currrent_stat['use_hth'] == 't' ? true : false;
			if($debug)
			{	
				//echo "sort by ".$stat_index." ".$stat_sort." HTH == ".$currrent_stat['use_hth'] ."\n";
			}
			if($stat_hth)			
			{
				// lets check all ranks for ties, after sorting by rank
				//$sorted = $this->array_orderby($sorted,$sort_args_rank[0],$sort_args_rank[1]);	
				//this copies the games into ranksort, will have to remember to copy them back or w/e
				unset($rankSort);
				$rankSort=array();
				foreach($sorted as $i=>$row)
					$rankSort[$row['rank']][] = $i;
				
				
				$type=$stat_index;
				//calculate head to head rank
				foreach($rankSort as $rank => $index_list)
				{

				
				if(count($index_list) > 1) // if == 1, then only one team has this rank, no ties, do nothing
				{//otherwise SOME teams share the same rank			
					$numTms = count($index_list);	
					if($debug )echo "\n  : H2H $type :".$numTms." teams are tied at at rank  ".$rank."\n\n";
					//$subGroup = array();//consider this subgroup of teams as the whole batch
					$rankInc = $numTms;//was zero, but this is wrong
					$addRank=array();
					$diGraph = array();
					foreach($index_list as $outer=>$index)
					{
						$team_id = $sorted[$index]['team_id'];
						if($debug) echo "CHECK HTH $team_id  ".$sorted[$index]['team_name'];
						
						if(!array_key_exists($index,$addRank)) $addRank[$index]=-1;
						if(!array_key_exists($index,$diGraph)) $diGraph[$index] =array();
						foreach($index_list as $inner=>$other_index)
						if($team_id != $sorted[$other_index]['team_id'] && $inner>$outer)
						{//double loop, except dont compare a team to itself
						
							$other_team_id = $sorted[$other_index]['team_id'];
							
							if($debug) echo "      vs $other_team_id  :".$sorted[$other_index]['team_name'];
							
							if(!array_key_exists($other_index,$addRank)) $addRank[$other_index]=-1;
							if(!array_key_exists($other_index,$diGraph)) $diGraph[$other_index] =array();
							//echo "\n compare team ".$index." to other team ". $other_index."\n";
							if(!array_key_exists($other_team_id ,$headToHead[$team_id] ))
							{
								if($debug) echo "these teams have never played each other\n";
							}
							else
							{
								$stillTied = true;
								
								//foreach($stat_hth_ids as $type)
								//{
								
									
								if(!isset($headToHead[$team_id][$other_team_id][$type])) $headToHead[$team_id][$other_team_id][$type]=0;
								if(!isset($headToHead[$other_team_id][$team_id][$type])) $headToHead[$other_team_id][$team_id][$type]=0;
								if($stillTied)
								{	
										if($debug)
										{
											//if( ($team_id== 361 || $team_id == 356)
											//&&($other_team_id== 361 || $other_team_id == 356) )
											//{
												
												echo $headToHead[$team_id][$other_team_id][$type]." vs ".$headToHead[$other_team_id][$team_id][$type]."\n";
											//}
										}
										
									//these are the 'minimal' types. as in:smaller is good
									if($type == "total_losses" || $type == "points_against")
									{
										if($headToHead[$team_id][$other_team_id][$type]<$headToHead[$other_team_id][$team_id][$type])
										{
											$stillTied=false;  // echo $other_index." beats ".$index;
											$diGraph[$other_index][] = $index;
											if($addRank[$other_index]==-1) $addRank[$other_index] =0;									
											$addRank[$index]+=$addRank[$other_index]+$rankInc;
										}
										else if($headToHead[$team_id][$other_team_id][$type]>$headToHead[$other_team_id][$team_id][$type])
										{
											$stillTied=false;  // echo $index." beats ".$other_index;
											$diGraph[$index][] = $other_index;
											if($addRank[$index]==-1) $addRank[$index] =0;									
											$addRank[$other_index]+=$addRank[$index]+$rankInc;
										}		
										//else echo $index."  tied with ".$other_index;
												
									}
									else
									{
										//maximal type: as in: larger is good. ex: wins, run diff

										if($headToHead[$team_id][$other_team_id][$type]>$headToHead[$other_team_id][$team_id][$type])
										{
											$stillTied=false; // echo $index." beats ".$other_index;
											$diGraph[$index][] = $other_index;
											if($addRank[$index]==-1) $addRank[$index]  =0;		
											
											
																		
											$addRank[$other_index]+=$addRank[$index]+$rankInc;//minus one plus two equals 1
										}
										else if($headToHead[$team_id][$other_team_id][$type]<$headToHead[$other_team_id][$team_id][$type])
										{
											$stillTied=false;  //echo $other_index." beats ".$index;
											$diGraph[$other_index][] = $index;
											if($addRank[$other_index]==-1) $addRank[$other_index] =0;
											$addRank[$index]+=$addRank[$other_index]+$rankInc;
										}	
										//else echo $index."  tied with ".$other_index;
										
									}		
												
								}//endif  $stillTied
								if(!$stillTied){$rankInc--;	}
								
								
								if($debug )
								if( $addRank[$index]!=-1 || $addRank[$other_index]!=-1)
								{

										
									//if( $team_id== 361 || $team_id == 356 )
									//{
									//	echo "main  team $team_id  rankSort willgo ".$sorted[$index]['rank'] ." plus ".$addRank[$index]." \n";
									//	echo "othr  team $other_team_id  rankSort willgo ".$sorted[$other_index]['rank'] ." plus ".$addRank[$other_index]." \n";
										
										//var_dump($sorted[$i]);
									//}
										
									
								}

							}//we are done comparing these two teams
							//else echo "they did not play each other \n";															
						}	//done inner loop		
						

										
						
								
						
															
					}	//done outer loop
					
					$numCycles=0;
					//echo "\n!!use dfs on constructed diGraph \n";
					////($diGraph);
					$exists = array_keys($diGraph);
					$used=array();
					foreach($exists as $first)
					if(!isset($used[$first]) || !$used[$first]) 
					{
						$vis=array();
						$path=array();
						$has_cycle = $this->dfs_find_cycle($diGraph,$first,&$vis,&$path,&$used);
						if($has_cycle && count($path)>1)//of course, if only one node, cycle is trivial
						{
							if($debug)
							{
								//echo "the following rock-paper-scissors cycle was found (indices of sorted given) \n"; 
								var_dump($path);	
							}
							
							$numCycles++;
						}			
					//	else		echo "no cycle found, when starting with $first \n";
					}
					
					//if no cycles found
					if($numCycles==0)
					{
						//TODO: how about we check whethner each team is in the cycle or not
						//if its in the cycle, of course keep that group teh same, otherwise skip it
						//if($debug) echo "after Digraph cycles::: is updating rank now:";
						foreach($addRank as $idx => $add)				
						if($add != -1)
							$sorted[$idx]['rank'] += $add;
					}
					else
					{
						if($debug)echo "found at least $numCycles circular-tied teams that h2h cannot fix.  rank not updated\n";
						
						
					}
					
										
				} 
				
				}//done rankSort

				
			}
			else
			{

			//next we update rank, based on this global sorting we just did, now that everything is sorted (H2H tiebreakers cant be done till rank is assigned) 
	
				if($init_rank)
				{
					$init_rank=false;
					echo "initial sort is ".$stat_index;
					//note that initiall all ranks were at zero, so first time thru we have to set it up
					if($stat_sort == "ASC" )
						$SORT=SORT_ASC;
					else
						$SORT=SORT_DESC;	
					$sorted = $this->array_orderby($sorted,$stat_index,$SORT);
					
					$rankSort = array();
					$lastRank = 0;
					$countSame=1;
					$max_rank_used=1;
					$numTeams = count($sorted);
					for($i=0;$i<$numTeams;$i++)
					{
						//and also format the percentages
						//$sorted[$i]['win_perc'] = $sorted[$i]['win_perc'] .' %';
						
						
						$tid=$sorted[$i]['team_id'];

						//echo "lastrank ++ becomse $lastRank\n";		
						$rankSort[$lastRank] = array();//initialize the array to sort by rank
						if($sorted[$i]['games_played']==0)
						{
							$sorted[$i]['rank']=null;//temporary fix
							continue;
						}
						$tied = true;
						$lastRank++;	
						if($i>0)
						{
							//foreach($stat_ids as $type)
							$tied = $tied &&($sorted[$i][$stat_index] == $sorted[$i-1][$stat_index]);
							if($tied)
							{//yes these two teams are tied
								if($lastRank>1)
				    				$lastRank--;
							    
							    $countSame++;
							    //$rankTies[]= array("rank"=>$lastRank,"index"=>$i,"prev"=>$i-1);
							}
							else
							{//not tied
								if($countSame>1)
								{//if 2 or more were tied in teh past, reset the counter	
									//minus 1 to under the last $countSame++;	
									$lastRank+=$countSame-1;
									//echo "current rank is$lastRank adnd we restting countSame now, it was $countSame \n";
									$countSame=1;
								}	
											
							}
						}	
						if($sorted[$i]['games_played']!=0)
						{
							$sorted[$i]['rank']=$lastRank;	
							//echo "save rank $lastRank \n ";	
							$max_rank_used=$lastRank;
						}
											
					}
					
					
					foreach($sorted as $i=>$row)
					{
						if($sorted[$i]['rank']==null)
							$sorted[$i]['rank']=$max_rank_used+1;
					}	
					
				}
				else
				{
					if($debug)echo " now just update rank with what has been changed ";
					unset($rankSort);
					$rankSort=array();
					foreach($sorted as $i=>$row)
						$rankSort[$row['rank']][] = $i;
				
					$type=$stat_index;
					//calculate head to head rank
					unset($index_list);
					unset($rank);
					foreach($rankSort as $rank => $index_list)
					{
						
						//replaced by beta stuff
						if(count($index_list) > 1) // if == 1 or 0, then only one team has this rank, no ties, do nothing
						{//otherwise SOME teams share the same rank			
							$numTms = count($index_list);	
							if($debug )echo "\n  $numTms teams are tied at at rank  ".$rank."\n\n";

							//$subGroup = array();//consider this subgroup of teams as the whole batch
							
							$addRank=array();
							unset($i);
							unset($team_id);
							
							$miniSort=array();
							foreach($index_list as $i)
							{
								$st_value= $sorted[$i][$stat_index];
								$i=(string)"".$i;//work around to force array to be associative, not numeric
								$miniSort[$i] =$st_value;
								//if($debug) echo " $team_id  ".$sorted[$index]['team_name'];
							}//end foreach indexlist
							
							if($debug) var_dump($miniSort);
							
							if($stat_sort == "DESC" )
							{   //reverse 
								arsort($miniSort);
							}
							else
							{
								asort($miniSort);
							}
							//$to_add=$numTms-1;
							
							$prev_index=null;
							if($debug && $rank==11 ) {var_dump($miniSort);}
							$groupByAdd=array();
							foreach($miniSort as $s_index=>$val)
							{
								if(!isset($groupByAdd[$val])) $groupByAdd[$val]=array();
								
								$groupByAdd[$val][]=$s_index;
								
							}
							$to_add=0;
							foreach($groupByAdd as $val=>$t_array)
							{
								echo "at ".$val." we have ".count($t_array)."\n";
								foreach($t_array as $idx)
								{
									$sorted[$idx]['rank'] += $to_add;
								}			
								$to_add+=count($t_array);				
							}
							
						}//end if count
						
						
						
						/*//old one here?
						if(count($index_list) > 1) // if == 1 or 0, then only one team has this rank, no ties, do nothing
						{//otherwise SOME teams share the same rank			
							$numTms = count($index_list);	
							if($debug )echo "\n  $numTms teams are tied at at rank  ".$rank."\n\n";

							//$subGroup = array();//consider this subgroup of teams as the whole batch
							
							$addRank=array();
							unset($i);
							unset($team_id);
							
							$miniSort=array();
							foreach($index_list as $i)
							{
								
								
								//$team_id = $sorted[$i]['team_id'];
								$st_value= $sorted[$i][$stat_index];
								
								$i=(string)"".$i;//work around to force array to be associative, not numeric
								
								$miniSort[$i] =$st_value;
								//if($debug) echo " $team_id  ".$sorted[$index]['team_name'];
							}//end foreach indexlist
							
							//if($debug) var_dump($miniSort);
							
							
							if($stat_sort == "DESC" )
							{
								//reverse 
								arsort($miniSort);
								
							}
							else
							{
								asort($miniSort);
							}
							$to_add=1;
							$prev_index=null;
							//if($debug) var_dump($miniSort);
							foreach($miniSort as $s_index=>$val)
							{
								if($prev_index==null)
									$prev_index=$s_index;//grab the first team
								else
								{
									//echo 'prev index is not null';
									//compare each to previous
									$prev_val=$miniSort[$prev_index];
									$winner=null;$other=null;
									$prev_is_winner=null;
									if($val      >  $prev_val)
									{
										if($stat_sort == "DESC" )
										{
											$prev_is_winner = true;
											//$winner = $s_index;
											//$other  = $prev_val;
										}
										else
										{
											$prev_is_winner = false;
											
										}
									}
									else if($val <  $prev_val)
									{
										if($stat_sort == "DESC" )
										{
											$prev_is_winner = false;
										}
										else
										{
											$prev_is_winner = true;
										}
									}
									//else they are equal so do nothing
									
									if($prev_is_winner===true)
									{
										$winner = $prev_index;
										$other  = $s_index;
										
									}
									else if($prev_is_winner===false)
									{
										$winner = $s_index;
										$other  = $prev_index;
									}
									
									//else it is null so do nothing
									if($debug)echo "$winner ,$other ";
									if($winner !== null)
									{
										//$sorted[$other]['rank'] +=$to_add;//nothing happnes to other
										//$to_add++;
										$sorted[$winner]['rank']+=$to_add;
										$to_add++;
									}
									
									
									
								}
								echo "sorted $s_index was $val  ";
								
								
								
							}
							
						
						}//end if count
					*/
					}//end ranksort foreach
				}//end else branch of init_rank
				
				
				
				
				
				
			}//end else branch--if global
			

		}
			
		

		//old one:
		/*
		//$sorted = $this->array_orderby($sorted,'games_played',SORT_DESC);	//this just forces ppl wit hzero games plaed to teh bottom	
		$hhIndex=-1;
		$sort_args=array();
		$sort_args[]=$sorted;		
		foreach($stat_ids as $i=>$key)
		{
			//echo "sort by ".$key;
			$sort_args[]=$key;
			if($key == "total_losses" || $key == "points_against")
				$sort_args[]=SORT_ASC;
			else
				$sort_args[]=SORT_DESC;		
								
			$sorted = call_user_func_array(array($this,'array_orderby'),$sort_args);
		}
		foreach($none_played as $temp)
		{
			$sorted[]=$temp;//zero played at bottom. needed to push here to get rank and games back calculated
		}
		
		
		
		
		
		
		//next we assign rank, now that everything is sorted (H2H tiebreakers cant be done till rank is assigned)

		$fixedCounter=0;
		$tieCounter  =0;
		//$numTeams = count($sorted);
		//var_dump($sorted);
		echo "rank sort incoming \n";
		foreach($sorted as $i=>$team)
		{		
			$fixedCounter++;

			$tied = true;
			//$lastRank++;	
			if($i==0)//so i==0
			{
				$sorted[$i]['rank']=$fixedCounter;	//starts at 1
				$tieCounter=$fixedCounter;//starts at one
				//echo "save rank $lastRank \n ";	
				//$max_rank_used=$lastRank;
			}
			if($i>0)
			{
				foreach($stat_ids as $type)
				{
					$tied = $tied &&($sorted[$i][$type] == $sorted[$i-1][$type]);//check if everything is tied or not, do H2H tiebreakers later
				}
				if($tied)
				{//yes these two teams are tied in all possible ways
					$sorted[$i]['rank']=$tieCounter;	//dont use current rank. ex: will go 1 2 3 3 5 
					
				}
				else
				{//not tied
					$sorted[$i]['rank']=$fixedCounter;	//regular ranks, 1 2 3 4 etc
					$tieCounter=$fixedCounter;//starts at one
					
				}
			}	
			
								
		}
		echo "ranks look like this:";
		foreach($sorted as $i=>$row)
		{
			echo $sorted[$i]['rank'].",";

		}

		echo "\nanother rank sort index list";
		foreach($sorted as $index => $team)
		{
			if($index==0){continue;}
			//set up variables
			$rankInc = 0;
			$addRank=array();
			$diGraph = array();

			if(!array_key_exists($index,$addRank)) $addRank[$index]=-1;
			if(!array_key_exists($index,$diGraph)) $diGraph[$index] =array();
				
			//take two teams and compare them
			$other_index=$index-1;
			if($sorted[$index]['rank'] !=$sorted[$other_index]['rank'])
			{
				echo "not tied, moving on ";
				continue;
			}
			echo "found a tie at rank ".$sorted[$index]['rank'];
			$team_id = $sorted[$index]['team_id'];
			$other_team_id = $sorted[$other_index]['team_id'];

			if(!array_key_exists($other_index,$addRank)) $addRank[$other_index]=-1;
			if(!array_key_exists($other_index,$diGraph)) $diGraph[$other_index] =array();
					//echo "\n compare team ".$index." to other team ". $other_index."\n";
			if(array_key_exists($other_team_id ,$headToHead[$team_id] ))
			{
				$stillTied = true;//try to search until this is false
				
				foreach($stat_hth_ids as $type)
				if($stillTied)
				{	//echo " \n   H2H testing ".$type."\n";
					//var_dump($headToHead[$team_id][$other_team_id]);
					if($type == "total_losses" || $type == "points_against")//DESC types
					{
						if($headToHead[$team_id][$other_team_id][$type]<$headToHead[$other_team_id][$team_id][$type])
						{
							$sorted[$other_index]['rank']++;
							$stillTied=false;  // echo $other_index." beats ".$index;
							$diGraph[$other_index][] = $index;
							if($addRank[$other_index]==-1) $addRank[$other_index] =0;									
							$addRank[$index]+=$addRank[$other_index]+2;
						}
						else if($headToHead[$team_id][$other_team_id][$type]>$headToHead[$other_team_id][$team_id][$type])
						{
							$sorted[$index]['rank']++;
							$stillTied=false;  // echo $index." beats ".$other_index;
							$diGraph[$index][] = $other_index;
							if($addRank[$index]==-1) $addRank[$index] =0;									
							$addRank[$other_index]+=$addRank[$index]+2;
						}		
						else echo $index."  stilltied with ".$other_index;
						$rankInc++;		
					}
					else//ASC types
					{
						if($headToHead[$team_id][$other_team_id][$type]>$headToHead[$other_team_id][$team_id][$type])
						{
							$sorted[$index]['rank']++;
							$stillTied=false; // echo $index." beats ".$other_index;
							$diGraph[$index][] = $other_index;
							if($addRank[$index]==-1) $addRank[$index]  =0;									
							$addRank[$other_index]+=$addRank[$index]+2;
						}
						else if($headToHead[$team_id][$other_team_id][$type]<$headToHead[$other_team_id][$team_id][$type])
						{
							$sorted[$other_index]['rank']++;
							$stillTied=false;  //echo $other_index." beats ".$index;
							$diGraph[$other_index][] = $index;
							if($addRank[$other_index]==-1) $addRank[$other_index] =0;
							$addRank[$index]+=$addRank[$other_index]+2;
						}	
						else echo $index."  stilltied with ".$other_index;
						$rankInc++;
					}						
				}//endif for $stillTied
			}//we are done comparing these two teams
					//else echo "they did not play each other \n";															
				//}	//done inner loop											
			//}	//done outer loop
			//
			$numCycles=0;
			//echo "\n!!use dfs on constructed diGraph \n";
			//var_dump($diGraph);
			$exists = array_keys($diGraph);
			$used=array();
			foreach($exists as $first)
			if(!isset($used[$first]) || !$used[$first]) 
			{
				$vis=array();
				$path=array();
				$has_cycle = $this->dfs_find_cycle($diGraph,$first,&$vis,&$path,&$used);
				if($has_cycle)
				{
					echo "the following rock-paper-scissors cycle was found (indices of sorted given) \n"; 
					var_dump($path);	
					$numCycles++;
				}			
			//	else		echo "no cycle found, when starting with $first \n";
			}
			//if no cycles found
		
		} //done rankSort
		//now ALL h2h fixing is done, it may or may not have fixed all ties...		
		//re sort, so they are arranged by rank, since addRank may have unsorted them
		
		*/
		$sorted = $this->array_orderby($sorted,'rank',SORT_ASC);

		//echo "sortying by rank is done;;;;;leadCut stuff commented out\n";
		//now use $leadCut to remove the top #teams per division
		//do not use for each so we are sure it is forced to go in rank order
		//echo "lead cut testing is below\n";
		//var_dump($leadCut);
		
		/*
		if(false)//dont worry about this here, only on websites global
		if($leadCut != 0) 
		{
			$divLeadersFound=array();
			foreach($game_divs as $divid)
				$divLeadersFound[$divid] = $leadCut;
			$newSorted=array();
			$numRemoved=0;
			$sc=count($sorted);
			//DO NOT USE FOREACH, it is important to in order from highest rank to lowest
			for($i=0;$i<$sc;$i++)
			{
				//rank changes since teams above this one have gone
				//$sorted[$i]['']
				$sorted[$i]['rank'] -= $numRemoved;
				$tm_id = $sorted[$i]['team_id'];
				if(!isset($team_to_div[$tm_id]))
				{
					echo"teamtodiv nto found\n";continue;
				}
				$divid = $team_to_div[$tm_id];
				if(isset($divLeadersFound[$divid])&& $divLeadersFound[$divid] > 0)
				{
					//echo "found teamid $tm_id best team in divid $divid \n";
					$divLeadersFound[$divid]--;
					$numRemoved++;					
				}
				else $newSorted[] = $sorted[$i];									
			}
			//done removals, copy back into sorted
			$sorted=$newSorted;
		}
		*/
		//now calculate games back based on this final ranking
		
		//$lead=0;
		//this games back method is global
		echo 'before GB '.count($sorted);
		$this->calc_games_back(&$sorted);
		echo 'after GB '.count($sorted);
		/*
		if($debug)
		{
			echo "AFTER calc_games_back";
			foreach($sorted as $s)
			{
				echo "\n".$s['gb']." ".$s['win_perc']." ".$s['rank'];
			}
		}*/
		//var_dump($sorted[0]);
		return $sorted;
    }
	
	private function calc_games_back(&$sorted)
	{
		//echo "\n next do games back \n";
		
		$div_leader_index=array();
		foreach($sorted as $i =>$row)		
		{
			$div_id=$row['division_id'];
			
			if(!isset($div_leader_index[$div_id])  )
			{
				$div_leader_index[$div_id]=$i;//location of the leader of this division in global sort
				//echo "assigned leader of division ".$div_id." as team_id=".$row['team_id']." \n";
			}
			$lead=$div_leader_index[$div_id];
			
			//echo "processing team ".$row['team_id']." in division $div_id \n";
			if($i == $lead)// || $sorted[$i]['games_played']==0) //update: calculate if zero played
				{$sorted[$i]['gb'] = "0";}//this is the leader
			else 
			{
				$sorted[$i]['gb'] = ( ( $sorted[$lead]['total_wins']  -   $sorted[$i]['total_wins']    	)  
			                    + 	  ( $sorted[$i]['total_losses']   -   $sorted[$lead]['total_losses']     )  )/2 //correct to swap i and lead for losses only
			                    +     ( $sorted[$lead]['total_ties']  -   $sorted[$i]['total_ties'])/4  ;//update: tie worth 0.25
			}
		}
	}
	
    private function dfs_find_cycle($dg,$x,$visited,$path,$used)
    {
    	$path[]=$x;
    	//echo " visiting ".$x." \n";
    	$cycle = false;
		$visited[$x]=true;
		$used[$x]=true;
		if(count($dg[$x]) == 0) 
		{
			$p = array_pop($path);
			//echo "  found a leaf, so we popped $p\n";
			return false;
		}
		foreach($dg[$x] as $y)
		{
			//echo "moving from $x to $y \n";
			if(isset($visited[$y]) && $visited[$y]) return true;
			else
				$cycle = ($cycle || $this->dfs_find_cycle($dg,$y,&$visited,&$path,&$used));
		}
		return $cycle;
    }
		/* **from comments of http://php.net/manual/en/function.array-multisort.php (jimpoz at jimpoz dot com)*/
	private function array_orderby()
	{
	    $args = func_get_args();
	    $data = array_shift($args);
	    foreach ($args as $n => $field) 
	    {
	        if (is_string($field)) 
	        {
	            $tmp = array();
	            foreach ($data as $key => $row)
	                $tmp[$key] = $row[$field];
	            $args[$n] = $tmp;
	        }
	    }
	    $args[] = &$data;
	    call_user_func_array('array_multisort', $args);
	    return array_pop($args);
	}
	/*
	example:

	The sorted array is now in the return value of the function instead of being passed by reference.

	<?php
	$data[] = array('volume' => 67, 'edition' => 2);
	$data[] = array('volume' => 86, 'edition' => 1);
	$data[] = array('volume' => 85, 'edition' => 6);
	$data[] = array('volume' => 98, 'edition' => 2);
	$data[] = array('volume' => 86, 'edition' => 6);
	$data[] = array('volume' => 67, 'edition' => 7);

	// Pass the array, followed by the column names and sort flags
	$sorted = array_orderby($data, 'volume', SORT_DESC, 'edition', SORT_ASC);
			
		
	*/


	
    public function post_save_standings()
 	{
 		$rank_type=(int) $this->input->post('rank_type_id');//not lu_ table anymore
 		echo $rank_type;
 		//echo "type:".$st_type;	
		$table = rawurldecode($this->input->post('table'));
		//var_dump($table);
		$table = json_decode($table,true);
		//var_dump($table);
		$season_id = (int)$this->input->post('season_id');
		//var_dump($season_id);
		if(!$season_id) 
		{
			echo -1;
			return;			
		}
		echo $this->statistics_model->reset_standings($season_id,$rank_type);//clear out
		//var_dump($table);		
		foreach($table as $row)
		{
			$data=$row['_oData'];//only needed because of YUI table format
			
			if(!$data['gb'] || $data['gb']=="-")
				$data['gb']=0;
				
			$team_id=$data['team_id'];
			echo $this->statistics_model->insert_standings($team_id,$season_id,$data['rank'],              $rank_type);			
			//all magic numbers from lu_team_statisics
			echo $this->statistics_model->insert_statistics($team_id,$season_id,1,$data['games_played'],   $rank_type);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,2,$data['total_wins'],     $rank_type);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,3,$data['total_losses']  , $rank_type);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,4,$data['calc_pts'],       $rank_type);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,5,$data['gb'],             $rank_type);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,6, $data['points_scored'], $rank_type);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,7, $data['points_against'],$rank_type);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,8, $data['run_diff'],      $rank_type);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,9, $data['win_perc'],      $rank_type);
			echo $this->statistics_model->insert_statistics($team_id,$season_id,10,$data['total_ties'],    $rank_type);
						
		}		
		
		echo $this->statistics_model->publish_rank_type($rank_type);		
 	}
 	
 	
 	public function json_league_preferences()
    {
    	$type=(int)$this->input->post('rank_type_id');
    	$table=$this->statistics_model->get_rank_statistics($type);
		foreach($table as &$r)
		{
			$r['use_hth'] = ($r['use_hth']=='t' );//convert to boolean from 't' // 'f' 
			$r['is_used'] = ($r['is_used']=='t' );//convert to boolean from 't' // 'f' 			
			$r['use_hth_image'] = $r['use_hth'];  //copy of image to pares into image
			
		}
			
		$this->result->json($table);
    }
    
    public function json_root_rank_types()
    {
		$org=$this->permissions_model->get_active_org();
		$league_id=$this->leagues_model->get_league_from_org($org);
		
		$season_id   =(int)$this->input->post('season_id');
		$ranks=$this->statistics_model->get_root_rank_types($league_id,$season_id);
		
		$this->result->json($ranks);
		
    }
    public function json_rank_types()
    {
		$season_id   =(int)$this->input->post('season_id');
		$org=      $this->permissions_model->get_active_org();
		$league_id=$this->leagues_model->get_league_from_org($org);
		
		$ranks=    $this->statistics_model->get_rank_types($league_id,$season_id);
		
		$this->result->json($ranks);
		
    }
    /**
    * specifically formatted for ext js TreePanel
    * 
    */
    public function json_rank_types_treepanel()
    {
		//$season_id   =(int)$this->input->post('season_id'); 
		//never access $  _  REQUEST or post directly!
		$season_id   =(int)$this->input->get_post('season_id');//get_post === $  _REQUEST
		
		$org=$this->permissions_model->get_active_org();
		$league_id=$this->leagues_model->get_league_from_org($org);
		
		$ranks=$this->statistics_model->get_rank_types($league_id,$season_id);
		//var_dump($ranks);
		
		
		
		$this->result->json($this->_format_ranks_treepanel($ranks));
		
    }
    private function _format_ranks_treepanel($ranks)
    {
		$root=array();
		$root['children']=array();
		$floating_leaves=array();//this stores leaf notes that have not found their parent id yet, doen in second loop
		
		//the tree only has two levels, otherwise we would need recursive loop. (a wildcard standing has a parent, but not allowed any underi t)
		foreach($ranks as $r)
		{
				
			if($r['parent_rank_type_id']==null)
			{
				$r['children']=array();
				$r['leaf']=false;//flag for grid
				$root['children'][]=$r;//this is a root node, no parent
			}
			else
			{
				$floating_leaves[]=$r;//this is a leaf, MUST attach to a root node later
			}
			
		}
		//very innefficient second loop with nested search
		foreach($floating_leaves as $l)
		{
			$find_id=$l['parent_rank_type_id'];
			//hunt for it
			foreach($root['children'] as &$p)
			{
				if($p['rank_type_id'] == $find_id)
				{
					$l['leaf']=true;//must flag as leaf for grid, so it wont show empty child container
					$p['children'][]=$l;
					break;
				}
			}
		}
		return $root;
    }
    public function post_delete_rank_type()
    {
    	$user=$this->permissions_model->get_active_user();
    	$type=(int)$this->input->post('rank_type_id');		
		echo $this->statistics_model->delete_rank_type($type,$user);
    }
    
    public function post_publish_rank_type()
    {
    	
    	$type=(int)$this->input->post('rank_type_id');		
		
		echo $this->statistics_model->publish_rank_type($type);
		
    }
    public function post_rank_type()
    {
    	//should handle both insert and update, they use the same form
		$org=$this->permissions_model->get_active_org();
		$user=$this->permissions_model->get_active_user();
		$league_id=$this->leagues_model->get_league_from_org($org);
		$name=rawurldecode($this->input->post('rank_name'));
		$w=$this->input->post('pts_per_win');
		$l=$this->input->post('pts_per_loss');
		$t=$this->input->post('pts_per_tie');
		
		
		$rank_type_id=(int)$this->input->post('rank_type_id');
		$season_id   =(int)$this->input->post('season_id');
		$parent      =(int)$this->input->post('parent_rank_type_id');
		
		if(!$parent || $parent == $rank_type_id){$parent=null;}//cannot be parent of yourself: set to null in this case
		
		if(!$season_id || $season_id<0)
		{
			echo -1;
			return;
		}
		
		if(!$rank_type_id||$rank_type_id==-1)
		{
			//create new
			$rank_type_id=$this->statistics_model->insert_rank_type($league_id,$name,$user,$org,$season_id,$parent);
			$this->_insert_default_league_standings($rank_type_id,$league_id,$season_id);
		}
		//update existing, also triggered after create
		$this->statistics_model->update_rank_points($rank_type_id,$w,$l,$t,$name,$user,$parent);
		echo $rank_type_id;
    }
    public function post_copy_rank_type()
    {
		$rank_type_id=(int)$this->input->post('rank_type_id');
		$season_id   =(int)$this->input->post('season_id');
		
		
				
		if(!$season_id || $season_id<0)
		{//season is required
			echo -1;
			return;
		}
		$user=$this->permissions_model->get_active_user();
		$org =$this->permissions_model->get_active_org();
		
		
		//make a duplicate of this rank, and add it to this season
		
		//get all basic info including parent
		$standings = $this->statistics_model->get_rank_type_info($rank_type_id);
		
		//stats displayed
		$display=$this->statistics_model->get_rank_display($rank_type_id);
		//stats used
		$used=$this->statistics_model->get_rank_statistics($rank_type_id);
		
		$standings=$standings[0];
		if(!$standings['parent_rank_type_id'] ||$standings['parent_rank_type_id']=='null')$standings['parent_rank_type_id']=null;
		
				
		//first create new one for this OTHER season
		$new_rt_id=$this->statistics_model->insert_rank_type($standings['league_id'],$standings['rank_name'],$user,$org,$season_id
						,$standings['parent_rank_type_id']);
		if($standings['is_published']=='t'||$standings['is_published']=='true')
		{
			$this->statistics_model->publish_rank_type($new_rt_id);
		}		
		echo $this->statistics_model->update_rank_points($new_rt_id,$standings['pts_per_win'],$standings['pts_per_loss']
			,$standings['pts_per_tie'],$standings['rank_name'],$user);
		$this->_insert_default_league_standings($new_rt_id,$standings['league_id'],$season_id);				
		
		echo "new id is=".$new_rt_id."\n";
		
		
		// step 2; loop on display and call echo $this->statistics_model->update_rank_display($rank,$s,$o,$u);
		foreach($display as $stat)
		{
			$s=$stat['stat_id'];
			$o=$stat['rank_order'];
			$u=$stat['is_used'];
			
			echo $this->statistics_model->update_rank_display($new_rt_id,$s,$o,$u);						
		}
		
		foreach($used as $st)
		{
			$stat_id=$st['stat_id'];
			$or=$st['rank_order'];
			$hth=$st['use_hth'];
			$isused=$st['is_used'];
			echo $this->statistics_model->update_used_rank_statistics($stat_id,$new_rt_id,$hth,$isused,$or);
		}
		
	
		
		
		
		
		
		
		
		
		
    }
    private function _insert_default_league_standings($rank_type_id,$league_id,$season_id)
    {
    	//for calculation rankings
    	//to rank_statistics
    	$hth='f';
		$default_stats=array(9,4,2,8,3,6,7);
		foreach($default_stats as $stat_id)
		{
			$this->statistics_model->add_rank_statistics($stat_id,$rank_type_id,$hth);
		}
		$hth='t';
		foreach($default_stats as $stat_id)
		{
			$this->statistics_model->add_rank_statistics($stat_id,$rank_type_id,$hth);
		}
		//defaults for rank_display
		$used='t';
		$display_stats=array(1,2,3,10,4,5,6,7,8,9);
		$i=1;
		foreach($display_stats as $stat_id)
		{
			$this->statistics_model->update_rank_display($rank_type_id,$stat_id,$i,$used);
			$i++;
		}
		//next  is rank_divisions
		
		$matches=$this->divisions_model->get_concated_matched($season_id);
		
		foreach($matches as $m)
		{
			//we assume/know that no mirror/duplicates exist, by divisions_model
			$hdiv=$m['h_division_id'];
			$adiv=$m['a_division_id'];
			$is_used='t';//default is use all
			
			$this->statistics_model->insert_rank_divisions($rank_type_id,$hdiv,$adiv,$is_used);						
		}
		
		
		
		// rank_wildcard has no defaults, the left outer join will show null->zero, and save can update
    }
    public function json_rank_display()
    {
    	
    	$rank_type_id=(int)$this->input->post('rank_type_id');
    	$this->result->json($this->statistics_model->get_rank_display($rank_type_id));
		
    }
    public function post_league_stat()
    {
    	$rank_type_id=(int)$this->input->post('rank_type_id');
    	$stat_id=(int)$this->input->post('stat_id');
    	$hth=$this->input->post('hth');
		echo $this->statistics_model->add_rank_statistics($stat_id,$rank_type_id,$hth);
		
    }
    
    
    public function post_delete_league_stat()
    {
		$rank_type_id=(int)$this->input->post('rank_type_id');
    	$stat_id=(int)$this->input->post('stat_id');
    	$hth=$this->input->post('hth');
		echo $this->statistics_model->delete_rank_statistics($stat_id,$rank_type_id,$hth);
    }
    /**
    * for a type and divisoin, this is the wildcard number
    * 
    */
    public function post_rank_wildcard()
    {
		$rank_type_id=(int)$this->input->post('rank_type_id');
		$division_id=(int)$this->input->post('division_id');
		$wc=(int)$this->input->post('wildcard_teams');
		
		echo $this->statistics_model->update_rank_wildcard($rank_type_id,$division_id,$wc);
		
    }
    /**
    * same as above but for all pool-divisions
    * 
    */
    public function post_rank_wildcard_all()
    {
		$org=$this->permissions_model->get_active_org();
		$league_id = $this->leagues_model->get_league_from_org($org);
		$rank_type_id=(int)$this->input->post('rank_type_id');
		$divs=$this->divisions_model->get_pool_divisions($league_id);
		$wc=(int)$this->input->post('wildcard_teams');
		if($wc<0) {$wc=0;}
		foreach($divs as $div)
		{
			$division_id=$div['division_id'];
			echo $this->statistics_model->update_rank_wildcard($rank_type_id,$division_id,$wc);
			
		}
    }
    

    public function post_used_rank_statistics()
    {
		$rank_type_id=(int)$this->input->post('rank_type_id');
    	$stat_id=(int)$this->input->post('stat_id');
    	$hth=$this->input->post('hth');
    	if(!$hth){$hth='f';}
		$used=$this->input->post('is_used');
		$rank_order=$this->input->post('rank_order');
		echo $this->statistics_model->update_used_rank_statistics($stat_id,$rank_type_id,$hth,$used,$rank_order);
    }
    public function post_rank_points()
    {
    	$rank_type_id=(int)$this->input->post('rank_type_id');
    	$w=(float)$this->input->post('w');
    	$l=(float)$this->input->post('l');
    	$t=(float)$this->input->post('t');
    	
		echo $this->statistics_model->update_rank_points($rank_type_id,$w,$l,$t);
		
    }
    
    
    public function post_swap_order()
    {
    	$rank_type_id  = (int)$this->input->post('rank_type_id');
    	
    	$above_stat_id = (int)$this->input->post('above_stat_id');
    	$below_stat_id = (int)$this->input->post('below_stat_id');
    	
    	$above_use_hth = $this->input->post('above_use_hth');
    	$below_use_hth = $this->input->post('below_use_hth');
		
		
    	$above_rank    = (int)$this->input->post('above_rank');
    	$below_rank    = (int)$this->input->post('below_rank');
    	
    	echo $this->statistics_model->update_rank_order($rank_type_id,$above_stat_id,$above_use_hth,$below_rank);
    	echo $this->statistics_model->update_rank_order($rank_type_id,$below_stat_id,$below_use_hth,$above_rank);
    }
    
    
    
    
    
    
    
    public function post_swap_display_order()
    {
		$rank_type_id  = (int)$this->input->post('rank_type_id');
    	
    	$above_stat_id = (int)$this->input->post('above_stat_id');
    	$below_stat_id = (int)$this->input->post('below_stat_id');
    	
    	$above_rank    = (int)$this->input->post('above_rank');
    	$below_rank    = (int)$this->input->post('below_rank');
    	
    	echo $this->statistics_model->update_display_order($rank_type_id,$above_stat_id,$below_rank);
    	echo $this->statistics_model->update_display_order($rank_type_id,$below_stat_id,$above_rank);
		
    }
    public function post_rank_display()
    {
    	$rank = (int)$this->input->post('rank_type_id');
		
		$s  = (int)$this->input->post('stat_id');
		$u  = $this->input->post('is_used');
		$o  = $this->input->post('rank_order');
		echo $this->statistics_model->update_rank_display($rank,$s,$o,$u);
		
    }
    /*
    public function json_division_options()
    {
    	$rank_type_id  = (int)$this->input->post('rank_type_id');
    	$r=$this->statistics_model->get_rank_divisions($rank_type_id);

		$this->result->json($r);		
    }*/
    /*
    public function post_division_options()
    {		
    	$parent=(int)$this->input->post('parent');
    	$rank = (int)$this->input->post('rank_type_id');
		$csv  = $this->input->post('csv_divs');
    	$wc   = (int)$this->input->post('wildcard_count');
    	$same = $this->input->post('same_divs_only');
		echo $this->statistics_model->insert_rank_divisions($rank,$csv,$wc,$same);
		echo $this->statistics_model->update_rank_parent($rank,$parent);
		
    }*/
    
    
    //game result management
    
    public function json_get_scores()
    {
        //get VALID scores
        $sch = $this->input->post('sch');
        
        $scores=$this->statistics_model->get_valid_scores($sch);
        $fmt='F j, g:i a';
        foreach($scores as &$r)
		{
			//var_dump($r);
			$r['display_time'] = date($fmt,strtotime($r['game_date']." ".$r['start_time']) );
		}
        echo  json_encode($scores);
    }
    
    public function post_delete_requests()
    {
		//delete all requests for this game
        $game = $this->input->post('game');
        $user=$this->permissions_model->get_active_user();
        $res = $this->statistics_model->delete_result_submission_by_game($game,$user);

        $this->result->json($res);
 
    }
    public function json_get_requests()
    {
		//for all games
        $sch = (int)$this->input->get_post('schedule_id');
        
        $requests = $this->statistics_model->get_invalid_scores($sch);
		$fmt='F j, g:i a';
        foreach($requests as &$r)
		{
			//var_dump($r);
			$r['display_time'] = date($fmt,strtotime($r['game_date']." ".$r['start_time']) );
		}
        $this->result->json($requests);
 
    }
    /**
    * thisis specific to YUI grouped tables 
    * 
    */
    public function json_get_game_requests()
    {
		//for a single game
        $game = $this->input->post('game');
        //get requested scores
        $requests = $this->statistics_model->get_result_sumbissions($game);
        $sortbyscore=array();
        $delim="-";
        $game_ids=array();
        foreach($requests as $r)
		{
			$scoreIndex=$r['home_score'].$delim.$r['away_score'];
			//group by score, and csv the result-ids
			$game_ids[$scoreIndex]=$r['game_id'];
			if(!isset($sortbyscore[$scoreIndex]))
				$sortbyscore[$scoreIndex]=$r['game_result_id'];
			else
				$sortbyscore[$scoreIndex].=','.$r['game_result_id'];						
		}
		$return=array();
		foreach($sortbyscore as $s=>$csv)
		{
			list($h,$a)=explode($delim,$s);
			$ids=explode(',',$csv);
			
			$return[]=array('home_score'=>$h,'away_score'=>$a,'copies'=>count($ids),'csv_result_ids'=>$csv,'game_id'=>$game_ids[$s]);
		}
        $this->result->json($return);
    }
    public function post_save_results()
    {  
        $game_result_id = (int)$this->input->post('game_result_id');
        //echo "VALIDATE $game_result_id\n";
        $gid = $this->input->post('game_id');
        $user=$this->permissions_model->get_active_user();

        //first reject all of them, before we approve the given one
        $user=$this->permissions_model->get_active_user();
        echo $this->statistics_model->reject_result_submission_by_game($gid,$user);
        
        
        $owner = $this->permissions_model->get_active_org(); 
        $league_id=$this->leagues_model->get_league_from_org($owner);
        echo $this->statistics_model->validate_game_result($game_result_id,$user);
        
        
        //trigger this calculation very time
        $details = $this->statistics_model->get_game_result_data($game_result_id);
        $sch_id = $details[0]['schedule_id'];
        $season = $details[0]['season_id'];
        //recalcualte stats if anything is published
        
        
        $ranks = $this->statistics_model->get_rank_types_published($league_id);
        foreach($ranks as $r)
        	echo $this->calculate_stats_internal($r['rank_type_id'],$season);
           
       //echo $this->schedule_model->validate_game_result($game_result_id,$gid);                
    }

    public function post_reject_score()
    {
        $user=$this->permissions_model->get_active_user();
		$score_id = (int)$this->input->post('game_result_id');
	
		$this->result->json($this->statistics_model->delete_result_submission($score_id,$user));
		
		
    }
    
    public function post_unvalidate_score()
    {
        $user=(int)$this->permissions_model->get_active_user();
		$score_id = (int)$this->input->post('game_result_id');
		
		echo $this->statistics_model->validate_game_result($score_id,$user,'f');
		
		
    }
    
    
    public function json_games_scores_count()
    {		
        $schedule_id = (int)$this->input->post('schedule_id');
        $games=$this->statistics_model->get_games_scores_count($schedule_id);
        $fmt='F j, g:i a';
        foreach($games as &$r)
		{
			$r['display_time'] = date($fmt,strtotime($r['game_date']." ".$r['start_time']) );
		}
        
		$this->result->json($games);
    }
    
    public function post_update_game_score()
    {
    	$user=$this->permissions_model->get_active_user();
		$score_id = (int)$this->input->post('game_result_id');
		$score = (int)$this->input->post('score_value');
		$type = $this->input->post('type');
		
		$is_home = ($type=='home_score' ? 't' : 'f');
		echo $this->statistics_model->update_game_score($score_id,$is_home,$score,$user);
		
    }
    
    //create new score that is valid by default. stored procedure will make sure its the only valid one
    //we dont want to update an existing submission, if editing an old record, just make the old record invalid
    public function post_valid_score()
    {    	
		$game_id=(int)$this->input->post('game_id');		
		if(!$game_id || $game_id<0)
		{
			echo -1;
			return;
		}
		$user=$this->permissions_model->get_active_user();
		$owner=$this->permissions_model->get_active_org();
		$h=(int)$this->input->post('home_score');
		$a=(int)$this->input->post('away_score');
		if($h<0) $h=0;
		if($a<0) $a=0;
		$user_record=$this->permissions_model->get_user($user);
		$user_name=$user_record[0]['person_fname']." ".$user_record[0]['person_lname'];
		$user_email=$user_record[0]['email'];
		$game_result_id= $this->statistics_model->insert_game_result($h,$a,$game_id,$user,$owner,$user_name,$user_email);
		
        $league_id=$this->leagues_model->get_league_from_org($owner);
        //echo 'game result id='.$game_result_id."_____";
        $this->_update_standings($league_id,$game_id);
		
		echo $game_result_id;
		
		
    }
    
    public function post_delete_valid_scores()
    {     
    	//erase all valid scores for thisgame- wlel the one and only  
		$user =$this->permissions_model->get_active_user();
		$owner=$this->permissions_model->get_active_org();
        $league_id=$this->leagues_model->get_league_from_org($owner);
		
		$game_id=(int)$this->input->post('game_id');	
    	echo $this->statistics_model->unvalidate_all_scores($user,$game_id);
    	$this->_update_standings($league_id,$game_id);
	}
    
    
    private function _update_standings($league_id,$game_id)
    {
    	$details=$this->games_model->get_game_ids($game_id);
		//$details = $this->statistics_model->get_game_result_data($game_result_id);
		if(!isset($details[0])) 
		{
			echo "update standings error";return -1;	
		}
		$sch_id = $details[0]['schedule_id'];
        $season = $details[0]['season_id'];
        //recalcualte stats if anything is published
        
        
        $ranks = $this->statistics_model->get_rank_types_published($league_id);
        foreach($ranks as $k)
        {
        	$this->calculate_stats_internal($k['rank_type_id'],$season);
		}
    }
    
    public function post_new_results_array()
    {
    	//probably not used anymore,this is from YUI table
		$results=json_decode($this->input->post('results'),true);
		$user=$this->permissions_model->get_active_user();
		$owner=$this->permissions_model->get_active_org();
        $league_id=$this->leagues_model->get_league_from_org($owner);
		
		foreach($results as $r)
		{
			
			$game_result_id=$this->statistics_model->insert_game_result($r['home_score'],$r['away_score'],$r['game_id'],$user,$owner,$r['is_valid']);
		}
		
		//just do this once after ALlll are done
        
        $this->_update_standings($league_id,$game_result_id);
        /*
		$details = $this->statistics_model->get_game_result_data($game_result_id);
        $sch_id = $details[0]['schedule_id'];
        $season = $details[0]['season_id'];
        //recalcualte stats if anything is published
        
        
        $ranks = $this->statistics_model->get_rank_types_published($league_id);
        foreach($ranks as $k)
        {
        	echo $this->calculate_stats_internal($sch_id,$k['rank_type_id'],$season);
		}*/
		echo $game_result_id;
    }
    
    public function json_past_games_no_score()
    {		
        $schedule_id = (int)$this->input->post('schedule_id');
		$games=$this->schedule_model->get_past_games_no_score($schedule_id);
		$fmt='F j, g:i a';
		foreach($games as &$r)
		{
			$r['display_time'] = date($fmt,strtotime($r['game_date']." ".$r['start_time']) );
		}
		
		$this->result->json($games);
		
    }
    
    public function json_rank_wildcard()
    {
		//$org=$this->permissions_model->get_active_org();
		//$league_id = $this->leagues_model->get_league_from_org($org);
		$season_id=$this->input->get_post('season_id');
		$divs=$this->divisions_model->get_concated_names($season_id);
		//var_dump($divs);
		$rank_type_id=(int)$this->input->post('rank_type_id');
		$table=$this->statistics_model->get_rank_wildcard($season_id,$rank_type_id);
		foreach($table as &$row)
		{
			//var_dump($row);
			
			if(!$row['rank_type_id']){$row['rank_type_id']=$rank_type_id;}//left outer join might have this as null, so fix it
			$row_id=$row['division_id'];
			$row['long_division_name']=$divs[$row_id];
			
		}
		$this->result->json($table);
    }
    
    public function json_rank_divisions()
    {
		//$owner=$this->permissions_model->get_active_org();
        //$league_id=$this->leagues_model->get_league_from_org($owner);
    	$rank_type_id  = (int)$this->input->post('rank_type_id');
    	$season_id  = (int)$this->input->post('season_id');
    	$rd=$this->statistics_model->get_rank_divisions($rank_type_id);
    	
    	$names=$this->divisions_model->get_concated_names($season_id);
    	//format names together
    	
    	$sep=' vs. ';
    	$still_exist=array();
    	foreach($rd as &$r)
    	{
    		$h=$r['h_division_id'];
    		$a=$r['a_division_id'];
    		if(!isset($names[$h] ) || !isset($names[$a])) {continue;}//if division has been deleted, skip it
			$r['division_match']=$names[$h].$sep.$names[$a];
			$still_exist[]=$r;
    	}
    	
    	$this->result->json($still_exist);
    }
    public function post_rank_divisions()
    {
		
    	$rank_type_id  = (int)$this->input->post('rank_type_id');
    	$hd  = (int)$this->input->post('h_division_id');
    	$ad  = (int)$this->input->post('a_division_id');
    	$sel  = $this->input->post('sel');
    	echo $this->statistics_model->insert_rank_divisions($rank_type_id,$hd,$ad,$sel);
    	
    }
    
    
    public function post_rank_divisions_internal()
    {
    	$rank_type_id  = (int)$this->input->get_post('rank_type_id');
    	//very similar to _insert_default_l
    	
    	$matches=$this->statistics_model->get_rank_divisions($rank_type_id);
		echo $rank_type_id;
		foreach($matches as $m)
		{
 
			$hdiv=$m['h_division_id'];
			$adiv=$m['a_division_id'];
			//echo "$hdiv==$adiv";
			if($hdiv==$adiv) // use internal matches
				{$this->statistics_model->insert_rank_divisions($rank_type_id,$hdiv,$adiv,'t');	}		
			else //but not any others
				{$this->statistics_model->insert_rank_divisions($rank_type_id,$hdiv,$adiv,'f'); }	
		}
		
		
    }
    
    
    public function post_update_display_level()
    {
		
    	$rank_type_id  = (int)$this->input->post('rank_type_id');
    	$display_level = (int)$this->input->post('display_level');
    	echo $this->statistics_model->update_display_level($display_level,$rank_type_id);
		
    }
    
}
?>
