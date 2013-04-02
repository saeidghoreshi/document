<?php
require_once('endeavor.php');
class Divisions extends Endeavor
{
	/**
	* @var Permissions_model
	*/
	public $permissions_model;
	
	/**
	* @var Leagues_model
	*/
	public $leagues_model;
	
	/**
	* @var Schedule_model
	*/
	public $schedule_model;
	
	/**
	* 
	* @var entity_model
	*/
	public $entity_model;
	
	/**
	* 
	* @var divisions_model
	*/
	public $divisions_model;
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
	* @var statistics_model
	*/
	public $statistics_model;
	
	public function __construct()
	{
	    parent::Controller();
	    $this->load->model('endeavor_model');
	    $this->load->model('statistics_model');
	    $this->load->model('games_model');
	    $this->load->model('divisions_model');
	    $this->load->model('teams_model');
	    $this->load->model('permissions_model');
	    $this->load->model('leagues_model');
	    // $this->load->model('schedule_model');
	    $this->load->model('entity_model');
	    $this->load->library('page');
	    $this->load->library('input');
		$this->load->library('result');
	    
	}
	
	private function load_window()
	{
	    $this->load->library('window');
	    $this->window->set_js_path("/assets/js/components/divisions/");	    
	    $this->window->set_css_path("/assets/css/");
	}
	
	
	public function window_managedivisions()
	{   
	    $this->load_window();

	    $this->window->add_js('../../models/division.js'); 

	    $this->window->add_js('forms/create_edit.js'); 
        $this->window->add_js('forms/division_fees.js'); 
	    
	    $this->window->add_js('windows/create_edit.js'); 
        $this->window->add_js('windows/division_fees.js'); 
	    
	      
	    $this->window->add_js('grids/treepanel.divisions.js');   
	               
	                        
	    $this->window->add_js('controller.js');                       
	    //$this->window->add_js('toolbar.js');
	    
	    $this->window->set_header('Manage Divisions');
	    $this->window->set_body($this->load->view('divisions/manage/main.php',null,true));
	    //$this->window->set_footer($this->load->view('leagues/leagues.divisions.footer.php',$data['footer'],true));
	    $this->window->json();
	}
	
	
	
	
	
	/**
	* only gets PARENT DIVISIONS
	* 
	*/
	public function json_getdivisions()
	{
		$org = $this->permissions_model->get_active_org();        
		
		$league=$this->leagues_model->get_league_from_org($org);
	    $dv = $this->divisions_model->get_parent_divisions($league);
		foreach($dv as &$row)
			$row['only_teams'] = ($row['only_teams'] =='t') ? 'Yes' : "No";
	    $this->result->json($dv);
	}
	public function json_pool_divisions()
    {
		$org = $this->permissions_model->get_active_org();        
    	$league=$this->leagues_model->get_league_from_org($org);
        $dv = $this->divisions_model->get_pool_divisions($league);
        foreach($dv as $i=>$row)
        	$dv[$i]['div_used']=true;
        $this->result->json($dv);
    }

	public function json_getdivisions_by_parent()
	{
	    $parent_id=$this->input->post("parent_id");
	    $season_id=$this->input->post("season_id");

	    $dv=$this->divisions_model->get_sub_divisions_tc($parent_id,$season_id);
		foreach($dv as &$row)
			$row['only_teams'] = ($row['only_teams'] =='t') ? 'Yes' : "No";
	    $this->result->json($dv);       
	}

	public function post_update_division()
	{
		$div_id   = $this->input->post('div_id');
		$div_name = rawurldecode($this->input->post('div_name'));
		//$only_teams= $this->input->post('only_teams');
		//$only_teams = ($only_teams == 'No') ? 'f' : 't';
		//var_dump($div_id);var_dump($div_name);var_dump($only_teams);return;
		
		echo $this->divisions_model->update_division($div_id,$div_name);
	}

	/**
	* assign a team to a division within a season 
	* 
	*/
	public function post_team_div()
	{
	    $mod = $this->permissions_model->get_active_user();
	    $org=$this->permissions_model->get_active_org();
		$season_id = (int)$this->input->get_post('season_id');
		//current
		$team_id   = (int)$this->input->get_post('team_id');	
		$div_id    = (int)$this->input->get_post('division_id');
		
		//move to
		$new_div_id    = (int)$this->input->get_post('new_division_id');
		$swap_team_id  = (int)$this->input->get_post('swap_team_id');	
		
		$results=array();
		
		$keep_clear = $this->input->get_post('keep_clear');// 'k' or 'c' 
		$swap_keep_clear = $this->input->get_post('swap_keep_clear');// 'k' or 'c' 
		
		
		$input_date = $this->input->get_post('input_date');// 'k' or 'c' 
		
		
		$force_win_perc = $this->input->get_post('force_win_perc');// 'k' or 'c' 
		
		//do this at the end
		if($force_win_perc && $force_win_perc=='t' || $force_win_perc=='true') $force_win_perc=true;
		else $force_win_perc=false;
		
		
		//$gather_swap_games= ($swap_team_id && $swap_team_id!= -1 &&$swap_keep_clear=='c' );
		//$swap_games=array();
		//currently team is in div, and swap_team is in new_div (or is empty)
		if($keep_clear=='c')
		{ 
			//special case: clear / exhib markings ...
			//echo "clear todo";
			$all_games=$this->games_model->get_season_team_games($season_id,$team_id,$input_date);
			
			foreach($all_games as $g)
			{
				$g_id=$g['game_id'];

				$results[]= $this->games_model->add_team_game_exc($team_id,$g_id);
			}
		}
		//else keep so do nothing
		
		
		//so we assign team_id to new_div_id
		
		 $success= $this->divisions_model->update_team_div($team_id,$new_div_id,$season_id,$mod,$org);
		
		
		if($swap_team_id && $swap_team_id!= -1)
		{
			
			if($swap_keep_clear=='c')
			{
				//clear old records
				//echo "clear todo";
				
				$swap_games= $this->games_model->get_season_team_games($season_id,$swap_team_id,$input_date);
				foreach($swap_games as $g)
				{
					$g_id=$g['game_id'];
					$h_id=$g['home_id'];
					$a_id=$g['away_id'];
					
								
					//add exceptions for this team, and its games
				    if($a_id == $swap_team_id || $h_id==$swap_team_id)
					{
						$results[]= $this->games_model->add_team_game_exc($swap_team_id,$g_id);
					}
				}

			}
			
			//put this team in the original div
			 $success=$success* $this->divisions_model->update_team_div($swap_team_id,$div_id,$season_id,$mod,$org);	
		}
		//else keep 
		
		//if($div_id =='null' || $div_id==''|| !$div_id || !is_numeric($div_id)) $div_id=null;
		
		//echo $this->divisions_model->update_team_div($team_id,$div_id,$season_id,$mod,$org);
		
		 echo /*$success.",".*/count($results);
		 
		 
		 
		// echo $force_win_perc;
		 if($force_win_perc)
		 { 
		 	 
		 	 
		 	 //default is win percentage
			 $this->statistics_model->update_rank_order_force_top_season_stat($season_id);
		 }
	}
	public function post_delete_team_div()
	{
	    $mod = $this->permissions_model->get_active_user();
		$div_id = $this->input->post('division_id');
		$team_id = $this->input->post('team_id');	
		$season_id = $this->input->post('season_id');
		
		//if($div_id =='null' || $div_id==''|| !$div_id || !is_numeric($div_id)) $div_id=null;
			
		echo $this->divisions_model->delete_team_div($team_id,$div_id,$season_id);
	}
	
	public function post_create_division()
	{
	    $user=$this->permissions_model->get_active_user();
		
		//$league_id = $league_result[0]['league_id'];
	    
	    
	    $parent_id=$this->input->post("parent_id");//might be null
	    if(!$parent_id ||$parent_id=='null'||$parent_id=='undefined')$parent_id=null;
	    $division_name=rawurldecode($this->input->post("division_name"));
	    $only_teams=$this->input->post("only_teams");
	    
	    
	    if( $only_teams == 'true'||$only_teams=='Yes' || $only_teams=='t' || $only_teams===true) 
	        $only_teams ='t'   ;
	    else 
	        $only_teams ='f';
	        
	        
	    
	    $div_id=(int)$this->input->post("division_id");//might be null    
	    if(!$div_id || $div_id<0)//if -1 or not given, create new
	    {
	    	//need league and season for create
	    
	    	$org=$this->permissions_model->get_active_org();
			$league_id = $this->leagues_model->get_league_from_org($org);
		    $season_id=(int)$this->input->post("season_id");//    	
		    if(!$season_id)
		    {
				echo -1;
				return;
		    }
	    	$div_id= $this->divisions_model->insert_division($user,$org,$parent_id,$division_name,$league_id,$only_teams);	
		    echo $this->divisions_model->insert_division_season($div_id,$season_id,$user,$org);
		}
	    else//should be valid id
	    {
	    
	    	echo $this->divisions_model->update_division($div_id,$division_name,$only_teams,$user);
	    }
                               
	}
	public function post_delete_division()
	{
		$user = $this->permissions_model->get_active_user();
		
	    $division_id = json_decode($this->input->post("division_id"));
		$season_id=(int)$this->input->post("season_id");//    	

		$success= $this->divisions_model->delete_division($division_id,$season_id,$user);
		
		//if echo is NaN, then js will know that error has occured
		
		if($success == -2)
		{
			echo "Cannot delete this division yet, delete subdivisions first.";
		} 
		else if($success==-3)
		{
			echo "Cannot delete this division, there are scheduled games for some teams in this division";
		}
		else if($success==-1)
		{
			echo "Division or season not found.";//this should never happen in theory, but its possible 
		}
 		else		
 		{
			echo $success;
 		}
	}
	/**
	* get teams with no division
	* 
	*/
	public function json_unassigned_teams()
	{
		$org=$this->permissions_model->get_active_org();
		$league_id = (int)$this->leagues_model->get_league_from_org($org);
		$season_id = (int)$this->input->post('season_id');
		$teams=$this->teams_model->get_season_teams($season_id);
		$count_teams=count($teams);
		if($count_teams>0) {echo "Cannot delete: ".$count_teams." teams are assigned in this season";return;}
		
		$this->result->json($this->divisions_model->get_unassigned_teams($league_id,$season_id));
	}
	
	public function json_season_div_teams()
	{
		$season_id = (int)$this->input->post('season_id'  );
		$div_id    = (int)$this->input->post('division_id');
		$this->result->json($this->divisions_model->get_season_div_teams($season_id,$div_id));
	}
	
	public function json_season_divisions()
	{
		$season_id = (int)$this->input->post('season_id');
		
		$divs=$this->divisions_model->get_season_divisions($season_id) ;
		//now count teams in sub division for the top lvl
		
		foreach($divs as &$d)//by reference is important here
		{
			//not the parent of this division, but tis the parent id of any subdivs that it has(of course)
			$parent_id=$d['division_id'];
        	
        	$pools = array();
			$divteam_count=$d['team_count'];		
			$this->recursive_pool_subdivisions($parent_id,$season_id,&$pools);	
        	
        	//$result = $this->divisions_model->get_sub_divisions_tc($parent_id,$season_id);
        	foreach($pools as &$row)
	        {
				$divteam_count += $row['team_count'];
	        }
	        //now we know the sum of all teams in all subdivisions
	        //if this is a pool, no subdivisions, it will just be number of teams (or zero)
	        $d['divteam_count']=$divteam_count;
		}
		
		$this->result->json($divs);
	}
	
	public function json_season_divisions_treepanel()
	{
		$season_id = (int)$this->input->get_post('season_id');
		
		$divs=$this->divisions_model->get_season_root_divisions($season_id) ;
		
		
		$this->_format_divisions_tree_recursive($divs,$season_id);
		
		$panel['children'] = $divs;
		$panel['division_name']='ROOT';
		$panel['leaf']=false;
		$panel['division_id']=false;
		$panel['total_teams']='';
		$panel['only_teams']='';
		//$panel['$panel']='';
		//var_dump($divs);
		$this->result->json($panel);
	}
	/**
	* takes in array of divs
	* 
	* @param mixed $divs
	*/
	private function _format_divisions_tree_recursive(&$root_divs,$season_id)
    {
		//root=array();
		//$root['children']=array();
		//$floating_leaves=array();//this stores leaf notes that have not found their parent id yet, doen in second loop
		
		//the tree only has two levels, otherwise we would need recursive loop. (a wildcard standing has a parent, but not allowed any underi t)
		foreach($root_divs as &$r)
		{
			$r['leaf']=($r['only_teams']=='t');//flag for grid display, only teams means is a leaf
			$n = $r['division_name'];
			$divid=$r['division_id'];
			//echo "\nprocess:$divid, ".$n;
			
			$c=$this->divisions_model->get_division_recursive_counts($divid,$season_id);
			//var_dump($c);
			if(!isset($r['total_teams'])) $r['total_teams']=0;
			$r['total_teams'] += $c['total_teams'];
			
			if(!$r['leaf'])
			{
				//if not a leaf, look for sub
				
				$subs=$this->divisions_model->get_sub_divisions_tc($divid,$season_id);
				
				//echo "not a leaf, #subs=".count($subs);var_dump($subs);
				
				
				$this->_format_divisions_tree_recursive($subs,$season_id);
				$r['children']=$subs;
				$r['iconCls']='fugue_sitemap-application-blue';
				//get_division_recursive_counts
			}
			else
			{
				$r['iconCls']='users';
			}
			
		}
    }
	/**
	* copypasted from schedule controler
	* 
	* @param mixed $div_id
	* @param mixed $season_id
	* @param mixed $result
	*/
	private function recursive_pool_subdivisions($div_id,$season_id,&$result)
    {
    	//echo "recursive div_id: ".$div_id." so far we have: ".count($result);    	
		$rows = $this->divisions_model->get_sub_divisions_tc($div_id,$season_id);
		foreach($rows as $row)
        {
			if($row['only_teams']=='t')
				$result[]=$row;
			else
				$this->recursive_pool_subdivisions($row['division_id'],$season_id,&$result);

        }
    }
    
    
    public function json_sorted_divisions()
    {
		$season_id = (int)$this->input->get_post('season_id');
		//$org=$this->permissions_model->get_active_org();
		//$league=$this->leagues_model->get_league_from_org($org);
		$all_pretty_divisions=$this->divisions_model->get_formatted_indented_divisions($season_id) ;
		$this->result->json($all_pretty_divisions);
    }
	public function json_season_schedule_divisions()
	{
		$season_id = (int)$this->input->post('season_id');
		$sch_id    = (int)$this->input->post('schedule_id');
		$org=$this->permissions_model->get_active_org();
		$league=$this->leagues_model->get_league_from_org($org);
		$all_pretty_divisions=$this->divisions_model->get_formatted_indented_divisions($season_id) ;
		//echo "we were given season $season_id and schedule $sch_id\n";
		//echo "total formatted:";
		//var_dump($all_pretty_divisions);
		
		$used_divisions=$this->divisions_model->get_season_divisions_have_games($season_id,$sch_id);
		//echo "total with games:";
		//var_dump($used_divisions);
		$return=array();
		//nwo just remove all of them that do not have games!!
		foreach($all_pretty_divisions as $test_me)
		{
			$this_id=$test_me['division_id'];
			$found=false;
			//echo "search for teams with divid $this_id\n";
			// but only consider 
			
			if($test_me['only_teams']=='f')
				$return[]=$test_me;//if only-teams == 'f' always include it
			else
			foreach($used_divisions as $has_games)
			{
				if($this_id==$has_games['division_id'] )
				{
					//echo "insertign the following";
					//var_dump($has_games);
					$return[]=$test_me;
					$found=true;
				}
				if($found) break;
			}
			
		}
		$this->result->json($return);
	}
	
	
	public function json_concated_names()
	{
		$season_id=(int)$this->input->get_post('season_id');
		//$org=$this->permissions_model->get_active_org();
		//$league_id = $this->leagues_model->get_league_from_org($org);
		//$season_id = $this->input->post('season_id');
		$d=$this->divisions_model->get_concated_names($season_id,true);
		$this->result->json($d);
	}
	
	public function json_concated_matched()
	{
		$org=$this->permissions_model->get_active_org();
		$league_id = $this->leagues_model->get_league_from_org($org);
		$divs=$this->divisions_model->get_concated_names($league_id,true);
		$sep=' vs. ';
		$match=array();
		//must avoid doubles/mirrormatch duplicates
		
		
		$used_pairs=array();
		foreach($divs as $h)
		foreach($divs as $a)
		{
			$m=array();
			$csv=$h['division_id'  ]."," .$a['division_id'] ;
			$mirror=$a['division_id'  ]."," .$h['division_id'] ;//opposite for test
			if(in_array($csv,$used_pairs) || in_array($mirror,$used_pairs)) { continue; }//already used this match
			
			$m['division_match']  =$h['division_name'].$sep.$a['division_name'];
			$m['csv_division_ids']= $csv ;
			$used_pairs[]=$csv;
			$match[]=$m;
		}
		$this->result->json($match);
	}
	
	/**
	* used for standings
	* do not edit, if you need to use this again copy and remake
	* 
	*/
	public function json_build_level_menu()
	{
		$org=$this->permissions_model->get_active_org();
		
		$season=(int)$this->input->get_post('season_id');
		//$league_id = $this->leagues_model->get_league_from_org($org);
		$roots=$this->divisions_model->get_parent_divisions($season);
		$divs=$this->divisions_model->get_concated_names($season,true);
		$menu=array();

		$level_one['display_level']=1;
		$level_one['lbl_level']='Level One, For Example '.$roots[0]['division_name'];
		//$level_two=$divs[0];
		$menu[]=$level_one;
		if(count($divs))//if subdivisions exist
		{
			$level_two['display_level']=2;
			$level_two['lbl_level']='Level Two, For Example '.$divs[0]['division_name'];
			$menu[]=$level_two;
		}
		echo json_encode($menu);
	}
	
	/**
	* copy a division from one season to another
	* (currently is sent by drag drop in version 2.0 of  gui but thats irrelevant)
	* @author sam
	* 
	*/
	public function post_move_division()
	{
		$user=$this->permissions_model->get_active_user();
		$dest_season_id     =(int)$this->input->get_post('dest_season_id');
		$from_season_id     =(int)$this->input->get_post('from_season_id');
		
		$type	= $this->input->get_post('type');
		$m_division_id =(int)$this->input->get_post('m_division_id');//the one being moved
		$np_division_id=(int)$this->input->get_post('np_division_id');//the proposed new parent div that the 'moved' one will be underneath
		
		if($type=='append')
		{
			if($dest_season_id==$from_season_id)
			{
				echo $this->divisions_model->update_division_parent($m_division_id,$np_division_id,$user);
			}	
			else
			{
				echo $this->_copy_division_to_season($m_division_id,$np_division_id,$from_season_id,$dest_season_id);				
			}				
		}
		else if($type='after')
		{
			echo 0;	
		}
		else if($type='before')
		{
			echo 0;			
		}		
	}
	
	/**
	* copy $copyme_division_id div, plus all its subdivs, to the new season
	* 
	* @param mixed $copyme_division_id
	* @param mixed $parent_division_id
	* @param mixed $from_season_id
	* @param mixed $dest_season_id
	*/
	private function _copy_division_to_season($copyme_division_id,$parent_division_id,$from_season_id,$dest_season_id)
	{
		$user=$this->permissions_model->get_active_user();
		$org =$this->permissions_model->get_active_org();	
		$league_id = $this->leagues_model->get_league_from_org($org);
		
		$subs=array();
		$copyme_div = $this->divisions_model->get_division($copyme_division_id);
		
		//r.deposit_amount, r.fees_amount 
		
		$copyme_div=$copyme_div[0];

		$this->divisions_model->get_recursive_subdivisions($copyme_division_id,&$subs);
		
		
		//var_dump($copyme_div);
		
		
		$div_id_conversion=array();
		//return;
		if(!$parent_division_id) $parent_division_id=null;
		$first_parent_id = $this->divisions_model->insert_division($user,$org,$parent_division_id,$copyme_div['division_name'],
												$league_id,$copyme_div['only_teams']);	
												
		$this->divisions_model->insert_division_season($first_parent_id,$dest_season_id,$user,$org);
		
		
		$div_id_conversion[$copyme_division_id] = $first_parent_id;
		$this->_copy_division_fees($copyme_division_id,$from_season_id,$first_parent_id,$dest_season_id);
		
		//var_dump($subs);
		
		foreach($subs as &$subdiv)
		{			
			$old_parent = $subdiv['parent_division_id'];
			$new_parent = $div_id_conversion[$old_parent];
			
			$new_id = $this->divisions_model->insert_division($user,$org,$new_parent,$subdiv['division_name'],
													$league_id,$subdiv['only_teams']);	
													
			$this->divisions_model->insert_division_season($new_id,$dest_season_id,$user,$org);	
			
			
			$this->_copy_division_fees($subdiv['division_id'],$from_season_id,$new_id,$dest_season_id);
		
			$div_id_conversion[$subdiv['division_id']] = $new_id;//not used yet
			
		}
		
		//$this->divisions_model->insert_division_season()
		
	}	
	private function _copy_division_fees($from_div_id,$from_season_id,$to_div_id,$to_season_id)
	{
		$from = $this->divisions_model->get_season_division_reg($from_season_id,$from_div_id);
		if(!count($from)) return;
		$from=$from[0];
		
		if(!$from['deposit_amount']) $from['deposit_amount']=0;	
		if(!$from['fees_amount']   ) $from['fees_amount']   =0;		
		
		
		
		return $this->divisions_model->update_season_division_custom_rates($to_season_id,$to_div_id,
					$from['deposit_amount'],$from['fees_amount']);    
	}
    public function json_update_season_division_custom_rates()
    {
        $season_id      =(int)$this->input->get_post("season_id");
        $division_id    =(int)$this->input->get_post("division_id");
        
        $deposit_amount =(float)$this->input->get_post("deposit_amount");
        if($deposit_amount<0 ) $deposit_amount=0;
        $fees_amount    =(float)$this->input->get_post("fees_amount");
        if($fees_amount<0 ) $fees_amount=0;
        
        
        $result=$this->divisions_model->update_season_division_custom_rates($season_id,$division_id,$deposit_amount,$fees_amount);    
        $this->result->success(    $result[0]["update_season_division_custom_rates"]);                    
    }
}
?>
