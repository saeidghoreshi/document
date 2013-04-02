<?php

require_once('./endeavor/controllers/endeavor.php');
class Season extends Endeavor
{
	///// VARIABLES //////////////////////////////////////////////////////////////
	
	/**
	* @var Season_model
	*/
	public $season_model;
	
	/**
	* @var Registration_model
	*/
	public $registration_model;
	
	/**
	* @var Leagues_model
	*/
	public $leagues_model;
	/**
	* @var teams_model
	*/
	public $teams_model;
	
	/**
	* 
	* 
	* @var schedule_model
	*/
	public $schedule_model;
	
	/**
	* @var divisions_model
	*/
	public $divisions_model; 
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('season_model');
		$this->load->model('registration_model');
		$this->load->model('leagues_model');
		$this->load->model('schedule_model');
		$this->load->model('teams_model');
		$this->load->model('divisions_model');
		$this->load->library('result');
		$this->load->library('input');
	}
	
	///// PRIVATE / PROTECTED //////////////////////////////////////////////////////////////
	
	private function load_window()
	{
	    $this->load->library('window');
	    $this->window->set_css_path("/assets/css/season/");
	    $this->window->set_js_path("/assets/js/components/season/");
	}
	
	///// WINDOW //////////////////////////////////////////////////////////////

	public function window_manage()
	{   
		$this->load->library('window');
	    $this->window->set_css_path("/assets/css/season/");
	    $this->window->set_js_path("/assets/js/models/");
	    $this->window->add_js('team.js');
	    $this->window->add_js('season.js');
	    
	    //first get components from team
	    $this->window->set_js_path("/assets/js/components/teams/");
	    //these are for drag-drop division assignment
	    $this->window->add_js('grids/team_basic.js');
	    $this->window->add_js('grids/team_grouped.js');
	    //grid for incoming registrations
	    $this->window->add_js('grids/registrations.js');
	    
	    //move to seasons components
	    $this->window->set_js_path("/assets/js/components/seasons/");
        
        //Forms
	    $this->window->add_js('forms/create_edit.js');
        $this->window->add_js('forms/forms.js');
        //Windows
	    $this->window->add_js('windows/create_edit.js');
        $this->window->add_js('windows/spectrumwindow.registration.js');
        
        //Grids                                   
        $this->window->add_js('grids/seasons.js');
        $this->window->add_js('grids/spectrumgrids.customfields.js');

	    $this->window->add_js('controller.js');
	    
	    //HTML
	    $this->window->set_header('Seasons');
	    
        $this->window->set_body($this->load->view('season/manage/main.php',null,true));
	    $this->window->json();
	}
	
	///// JSON //////////////////////////////////////////////////////////////
	public function json_registration_collect_status()
	{
		$this->result->json($this->season_model->get_registration_collect_status());
	}
	
	public function json_registrations()
	{
		$season_id = (int)$this->input->post('season_id');
		
		$result=$this->season_model->get_season_registrations($season_id);
		$fmt="F j, Y";
		foreach($result as &$s)
		{
			$s['effective_range_start']=date($fmt,strtotime($s['effective_range_start']));
			$s['effective_range_end'  ]=date($fmt,strtotime($s['effective_range_end'  ]));	
			$s['is_enabled'  ] = ($s['is_enabled'  ] =='t');		//parse boolean
		}
		//$this->result->json($result);
        //$this->result->json_pag($result);
        $this->result->json_pag($result);
	}

	
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  splited by these functions below  ~~~~~~~~~~~~~~~
    public function json_get_unassigned_teams_by_season()
    {
        $season_id=(int)$this->input->get_post("season_id");
        $result=$this->teams_model->get_unassigned_teams_by_season($season_id);
        //if(!$result)$result=array();//should never happen, but it did once
        $this->result->json_pag($result);
    }
    /**
    * this INCLUDes divisions with zsero teams
    * adds a null team record for that case
    * 
    */
    public function json_get_assigned_teams_by_season()
    {                             
        $season_id=(int)$this->input->get_post("season_id"); 
        //get teams that are assigned
        $result=$this->teams_model->get_season_teams($season_id,null);//
        
        //find out which divs are used
        $used_divisions=array();
        foreach($result as &$r)
        {
        	//make sure used_ has uniques, only once eacy
        	$divid=$r['division_id'];
        	$r['division_fullname']=$r['division_name'];
			if(!in_array($divid,$used_divisions))$used_divisions[]=$divid;
        }
        
        //get UNused / empty divisions
        $divs=$this->divisions_model->get_concated_names($season_id,true);//TRUE for json
         
        foreach($divs as $div)
        {
        	$div_id=$div['division_id']; 
        	//if we found a division that hasnt been used yet, that is, a div that has no teams, we sitll
        	//want to show it here
			if(!in_array($div_id,$used_divisions))
			{
				$div['season_id']=$season_id;
				$div['team_id']='';
				$div['team_name']='';
        		$div['division_fullname']=$div['division_name'];
				$result[]=$div;//array('division_id'=>$div_id,'division_name'=>$div_name,)
				
			}
        }
        
        
        $this->result->json_pag($result);
    }
    public function json_update_teams_division_assignment()
    {
        $new_assignment_combination=$this->input->get_post('new_assignment_combination');
        $season_id=(int)$this->input->get_post("season_id");
        $result=$this->teams_model->update_teams_division_assignment($new_assignment_combination,$season_id);
          $this->result->success("1");                                                                      
        //echo json_encode(array("success"=>"true","result"=>"1"));
    }   

    //create and update are comign from the same season, so use the same method
    public function json_new_season()
    {    
        $season_id=(int)$this->input->get_post("season_id");
        
    	//never access $_GET or POST directly, use input class
        $season_name=$this->input->get_post("season_name");
        $start_date=$this->input->get_post("effective_range_start");
        $end_date=$this->input->get_post("effective_range_end");
        
        
        $s= strtotime($start_date);
		$e= strtotime($end_date);
		if($s>$e)//if start is after end, swap them
		{
			$swap=$start_date;
			$start_date=$end_date;
			$end_date=$swap;			
		}
		$swap=null;
		//format dates for postgres
		$plain='Y-m-d';
		$start_date =date($plain,strtotime($start_date));
		$end_date   =date($plain,strtotime($end_date));
		
		
        
        
        $isactive=$this->input->get_post("isactive_display");
		//posting the image html does not work and is bad anyway so try this
        if($isactive===false || $isactive=='f'||$isactive=='false' ||$isactive=='Disabled'|| $isactive=='Inactive' ) $isactive='f';
        else $isactive='t';
        
        if($this->input->get_post("isactive")=='on')$isactive='t';//override for checkbox form, if it exists
        
        $reg_needed = $this->input->get_post("reg_needed");
        if($reg_needed===true || $reg_needed=='true'||$reg_needed=='t' || $reg_needed=='on') $reg_needed='t';
    	else $reg_needed='f';
        $reg_start_date = $this->input->get_post("reg_range_start");
        $reg_end_date = $this->input->get_post("reg_range_end");
        
        if(!$reg_start_date)$reg_start_date=null;
        if(!$reg_end_date)$reg_end_date=null;
        if($reg_end_date != null && $reg_end_date != null)
        {
			//if dates exist swap and format
	       
	        $s= strtotime($reg_start_date);
			$e= strtotime($reg_end_date);
			if($s>$e)//if start is after end, swap them
			{
				$swap          =$reg_start_date;
				$reg_start_date=$reg_end_date;
				$reg_end_date=$swap;			
			}
			
		}
		//also check dates seperately for formatting
		if($reg_start_date)  $reg_start_date =date($plain,strtotime($reg_start_date));
		if($reg_end_date)    $reg_end_date   =date($plain,strtotime($reg_end_date));
		
        $reg_deposit_status = $this->input->get_post("deposit_status");
        if(!$reg_deposit_status)$reg_deposit_status=null;
        $reg_deposit_amount=(float)$this->input->get_post("deposit_amount");
        if(!$reg_deposit_amount)$reg_deposit_amount=null;
        $reg_fees_status=$this->input->get_post("fees_status");
        if(!$reg_fees_status)$reg_fees_status=null;
        $reg_fees_amount=(float)$this->input->get_post("fees_amount");
        if(!$reg_fees_amount)$reg_fees_amount=null;
		
		if(!$season_id || $season_id==-1)
        {
	        $result=$this->season_model->new_season($season_name,$start_date,$end_date,$isactive
	            ,$reg_needed,$reg_start_date,$reg_end_date,$reg_deposit_status,$reg_deposit_amount,$reg_fees_status,$reg_fees_amount   );			
        }
        else
        {
        	$isenabled=$this->input->get_post('is_enabled_display');
        	if($isenabled===false) {$isenabled=$reg_needed;}
        	
        	if($isenabled == 'Enabled' || $isenabled=='t' || $isenabled=='true'||$reg_needed=='on') $isenabled='t';
        	else $isenabled='f';
        	//echo "update to ".$reg_start_date." ".$reg_end_date." ".$isenabled.;
			$result=$this->season_model->update_season($season_id,$season_name,$start_date,$end_date,$isactive
	            ,$isenabled,$isenabled,$reg_start_date,$reg_end_date,$reg_deposit_status,$reg_deposit_amount."",$reg_fees_status,$reg_fees_amount.""   );
        }
        
        // (*)CHECK WHETHER NEW/UPDATE SEASON CONFIG OPENS REGISTRATION OR NOT, IF YES THEN SEND SEND NOTIFICATION EMAIL TO PEOPLE WAITING FOR THAT AND INACTIVATE RECORDS
        $result =$this->season_model->get_registration_notification_perople_list($season_id);
        
        $waitingUserList='';
        foreach($result as $v)
        {
            $waitingUserList.=($v["id"].',');
            
            $data=array
            (
                 'name'           =>$v["name"]
                ,'email'          =>$v["email"]
                ,'leagueName'     =>$v["league_name"]
                ,'reg_start_date' =>$v["start_date"]
                ,'reg_end_date'   =>$v["end_date"]
            );
            
                            
            $subject = $data["leagueName"] . ' Registration Opened';
            $message = $this->load->view("emails/registrationOpeningNotification.php",$data,true);
            $headers = "From: {$data["email"]}" . "\r\n" .$message . "\r\n"  .'X-Mailer: PHP/' . phpversion();
            mail($data["email"],$subject,$headers);
        }
        if($waitingUserList!='')
        {
           $waitingUserList=substr($waitingUserLst,0,strlen($waitingUserList)-1);
           $this->season_model->inactivate_waiting_people_list($waitingUserList); 
        }
           
        /*  END (*) */

       $this->result->success($result);
    }
	//unlike above, here we get either one or the other seperately, not both
	
	
	public function json_active_league_seasons()
	{                                      
        $a_o_id = $this->permissions_model->get_active_org();
		$league_id=$this->leagues_model->get_league_from_org($a_o_id);
        $seasons=$this->season_model->get_seasons($league_id);
        
		$fancy="F j, Y";
		$plain="Y/m/d";
		foreach($seasons as $i=>&$s)
		{
			$seasons[$i]['_effective_range_start']=$s['effective_range_start'];
			$seasons[$i]['_effective_range_end'  ]=$s['effective_range_end'  ];
            $seasons[$i]['_reg_range_start']       =$s['reg_range_start'];
            $seasons[$i]['_reg_range_end'  ]       =$s['reg_range_end'  ];
            //add if checks: if null or emtpy string, date format puts out unix epoch 1969 whne we want blank
            if($s['effective_range_start'])
            {
				$s['effective_range_start']=date($plain,strtotime($s['effective_range_start']));
				$s['display_start']        =date($fancy,strtotime($s['effective_range_start']));
            }
            else 	$s['display_start']='';
            
            if($s['effective_range_end'  ])
            {
				$s['effective_range_end'  ]=date($plain,strtotime($s['effective_range_end'  ]));
				$s['display_end'  ]        =date($fancy,strtotime($s['effective_range_end'  ]));
            }
            else 	$s['display_end'  ]='';

			if($s['reg_range_start'])
				$s['reg_range_start']      =date($plain,strtotime($s['reg_range_start']));
			if($s['reg_range_end'  ])
				$s['reg_range_end'  ]      =date($plain,strtotime($s['reg_range_end'  ]));
            
            //combine all into full display
            $s['season_name_dates'] = $s['season_name']." : ".$s['display_start']." - ".$s['display_end'  ];
            
			if($s['isactive']=='t')
			{
				$s['isactive']=true;
				$s['isactive_icon']='bullet_green';
				$s['isactive_display']="Active";
			}
			else
			{
				$s['isactive']=false;
				$s['isactive_icon']='bullet_yellow';
				$s['isactive_display']="Inactive";
			}
			
            if($s['is_enabled']=='t')
            {
            	$s['reg_needed']=true;
				$s['is_enabled_icon']='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/tick.png />';
				$s['is_enabled_display']="Enabled";
            }
            else
            {
				$s['reg_needed']=false;
				$s['is_enabled_icon']='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/cross.png />';
				$s['is_enabled_display']="Disabled";
            }

		}     
        if($this->input->get_post('combobox'))
        	$this->result->json($seasons);//regular json encode
        else
        	$this->result->json_pag($seasons);     //encode with root, totalCount, etc for grid paginator
	}
    public function json_active_league_seasons_store()
    {                                      
        $a_o_id = $this->permissions_model->get_active_org();
        $league_id=$this->leagues_model->get_league_from_org($a_o_id);
        $seasons=$this->season_model->get_seasons($league_id);
        
        $fancy="F j, Y";
        $plain="Y/m/d";
        foreach($seasons as $i=>&$s)
        {
            $seasons[$i]['_effective_range_start']=$s['effective_range_start'];
            $seasons[$i]['_effective_range_end'  ]=$s['effective_range_end'  ];
            $seasons[$i]['_reg_range_start']       =$s['reg_range_start'];
            $seasons[$i]['_reg_range_end'  ]       =$s['reg_range_end'  ];
            //add if checks: if null or emtpy string, date format puts out unix epoch 1969 whne we want blank
            if($s['effective_range_start'])
            {
                $s['effective_range_start']=date($plain,strtotime($s['effective_range_start']));
                $s['display_start']        =date($fancy,strtotime($s['effective_range_start']));
            }
            else     $s['display_start']='';
            
            if($s['effective_range_end'  ])
            {
                $s['effective_range_end'  ]=date($plain,strtotime($s['effective_range_end'  ]));
                $s['display_end'  ]        =date($fancy,strtotime($s['effective_range_end'  ]));
            }
            else     $s['display_end'  ]='';

            if($s['reg_range_start'])
                $s['reg_range_start']      =date($plain,strtotime($s['reg_range_start']));
            if($s['reg_range_end'  ])
                $s['reg_range_end'  ]      =date($plain,strtotime($s['reg_range_end'  ]));
            
            if($s['isactive']=='t')
            {
                $s['isactive_icon']='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/tick.png />';
                $s['isactive_display']="Active";
            }
            else
            {
                $s['isactive_icon']='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/cross.png />';
                $s['isactive_display']="Inactive";
            }
            
            
            
            if($s['is_enabled']=='t')
            {
                $s['is_enabled_icon']='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/tick.png />';
                $s['is_enabled_display']="Enabled";
            }
            else
            {
                $s['is_enabled_icon']='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/cross.png />';
                $s['is_enabled_display']="Disabled";
            }                        
        }     
        
        $this->result->json_pag_store($seasons);     //already has been done in constructor://$this->load->library('result');
    }
	
	
	private function _format_seasons_array($seasons)
	{
			$fancy="F j, Y";
		foreach($seasons as &$s)
		{
			
			if($s['effective_range_start']) 
            	$s['effective_range_start']=date($fancy,strtotime($s['effective_range_start']));
            if($s['effective_range_end'])	
           		$s['effective_range_end'  ]=date($fancy,strtotime($s['effective_range_end'  ]));
            
			if($s['isactive']=='t')
			{
				$s['isactive_icon']='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/tick.png />';
				$s['iconCls']='tick';
			}
            else 
            {
				$s['isactive_icon']='<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/cross.png />';
				$s['iconCls']='cross';
            }

		}
		return $seasons;
	}
	
	/**
	* for given team id, get all seasons that the team can be assigned to
	* do not show seasons it is ALREADY assigned to
	* 
	*/
	public function json_seasons_available_forteam()
	{
        $org     =$this->permissions_model->get_active_org();
        $league_id  =$this->leagues_model->get_league_from_org($org);
        
		$team_id = (int)$this->input->get_post('team_id');
		$seasons=$this->season_model->get_seasons_available_forteam($league_id,$team_id);
		
		$seasons = $this->_format_seasons_array($seasons);

		$this->result->json($seasons);
		
		
	}
	
	/**
	* is like the opposite of 'json_seasons_available_forteam', gets seasons that
	* ARE assigend to team.  assumes active org is a team and 
	* uses that
	* 
	*/
	public function json_seasons_assignedto_ao_team()
	{
		$team_org=$this->permissions_model->get_active_org();
		$team_id=$this->teams_model->get_team_id_byorg($team_org);
		
		$seasons=$this->season_model->get_seasons_assigned_to_team($team_id);
		//get_seasons_assigned_to_team
		
		$seasons = $this->_format_seasons_array($seasons);
		$this->result->json($seasons);
		
	}
    public function json_get_all_seasons()
    {                                      
        $a_o_id     =$this->permissions_model->get_active_org();
        $league_id  =$this->leagues_model->get_league_from_org($a_o_id);
        $seasons    =$this->season_model->get_seasons($league_id);
        
        $categorized_seasons=array();
        foreach($seasons as $i=>$v)
        {
            //format change in season_name
            $seasons[$i]["_season_name"]=
            '<span style="font-weight: bold">'.
            date('F d Y',strtotime($v["effective_range_start"])).' - '.date('F d Y',strtotime($v["effective_range_end"]))
            .'</span>'
            .'  ('.$seasons[$i]["season_name"].')'
            ;
                        
            if($v["isactive"]=='t')                                                     $categorized_seasons["active"][]=$seasons[$i];
            if(date($v["effective_range_start"])>date('Y-m-d') && $v["isactive"]=='f')  $categorized_seasons["notstarted"][]=$seasons[$i];
            if(date($v["effective_range_end"]  )<date('Y-m-d') && $v["isactive"]=='f')  $categorized_seasons["finished"][]=$seasons[$i];
        }                                                                                                                               
        
        $this->result->json($categorized_seasons);     
    }
    public function json_get_active_season()
    {                                      
        $a_o_id= $this->permissions_model->get_active_org();
        $league_id=$this->leagues_model->get_league_from_org($a_o_id);
        
        $season=$this->season_model->get_active_season($league_id);
        
        $season_split= explode(',',$season[0]["get_active_season"]);
        
        $season_id              =$season_split[0];
        $season_name            =$season_split[1];
        $effective_range_start  =$season_split[2];
        $effective_range_end    =$season_split[3];
        
        $season_name=
            '<span style="font-weight: bold">'.
            date('F d Y',strtotime($effective_range_start)).' - '.date('F d Y',strtotime($effective_range_end))
            .'</span>'
            .'  ('.$season_name.')'
            ;
                                  
        //returns back sooner started and not yet finished season
        $this->result->json(array('active_season_id'=>$season_id,"active_season_name"=>$season_name));
    }
	public function json_active_seasons()
	{
		$org = $this->permissions_model->get_active_org();
		$league_id=$this->leagues_model->get_league_from_org($org);
		$fancy="F j, Y";
		$plain="Y/m/d";
		$seasons=$this->season_model->get_active_seasons($league_id);
		foreach($seasons as &$s)
		{
			$s['effective_range_start']=date($plain,strtotime($s['effective_range_start']));
			$s['effective_range_end'  ]=date($plain,strtotime($s['effective_range_end'  ]));
			$s['display_start']        =date($fancy,strtotime($s['effective_range_start']));
			$s['display_end'  ]        =date($fancy,strtotime($s['effective_range_end'  ]));
		}
	    $this->result->json($seasons);
	   
	}
	///// HTML //////////////////////////////////////////////////////////////
	
	///// POSTS //////////////////////////////////////////////////////////////
	


	public function post_delete_season()
	{
		
		$user=$this->permissions_model->get_active_user();
		$season_id = (int)$this->input->get_post('season_id');
		
		
		$count_schedules=$this->schedule_model->count_schedules_in_season($season_id);
		
		$s = ($count_schedules== 1 ? '' : 's');
		if($count_schedules > 0)
		{
			$r= "We found ".$count_schedules.
			" schedule".$s." that have been created in this season, which must be deleted first. "
			." Alternatively, you can set this season to Inactive.";
		}
		else
		{
			$r= $this->season_model->delete($season_id,$user);
		}
		$this->result->success($r);
	}
}

?>
