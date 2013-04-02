<?php
require_once("endeavor_model.php");
class Registration_model extends Endeavor_model
{
	
	public function create($season, $open, $close, $deposit_collect, $deposit_amount, $fees_collect, $fees_amount)
	{
		if(!$this->userorg_can_update()) return false;
		$result = $this->db->query('SELECT public.registration_create(?,?,?,?,?,?,?)', array($season, $open, $close, $deposit_collect, $deposit_amount, $fees_collect, $fees_amount));
		return $result->first_row()->registration_create;
	}
	
	public function get_collection_statuses()
	{
		$sql = "SELECT id, status FROM public.lu_registration_collect_status";
		$query = $this->db->query($sql);
		return $query->result();
	}
	
    public function team_underprocess($a_o_id)
    {
        $q = $this->db->query("select *,manager_firstname||' '||manager_lastname as manager_name
        ,case when (approved='f') then '<input type=checkbox id=cb_'||team_id||' />' else '<img src=/assets/images/accept.png />' end as cb 
        from WEBSITE.slaves_get_registered_teams(?)",array($a_o_id));
        return $q->result_array();
    }
    
    public function get_season_registrations($season)
    {
    	$params=array($season);
    	$query = $this->input->get_post('query');
    	
    	$where='';
    	if($query)
    	{
			$where = " WHERE lower(team_name) like '%'||lower(?)||'%'";
			$params[]=$query;
    	}
        $q=
        "
            select t.org_id,t.team_name,teo.entity_id as team_entity_id,ts.season_id ,ld.division_id,tsd.id as team_season_division_id 
                ,case when ld.division_name is null then 'Unassigned' else ld.division_name end division_name
                
                FROM    public.team t 
                 
                inner join public.entity_org           teo      on teo.org_id=t.org_id                                               
                INNER JOIN public.team_season          ts       ON ts.team_id = t.team_id                     
                LEFT  JOIN public.team_season_division tsd      ON tsd.team_season_id = ts.id          AND ts.deleted_flag='f'
                LEFT  JOIN public.season_division      sd       On sd.id=tsd.season_division_id        AND tsd.deleted_flag='f'      AND sd.deleted_flag='f'
                LEFT  JOIN public.league_division      ld       ON ld.division_id = sd.division_id     AND ld.deleted_flag=FALSE 
                
                WHERE 
                t.deleted_flag=FALSE                     
                and team_status_id = 2
                AND ts.season_id = ?
                
                order by t.team_id
        ";
		$sql = $q.$where;
		return $this->db->query($sql, $params)->result_array();
    }
    
    public function approve_teams($selected_team_ids,$creator,$owned_by)
    {
        $selected_team_ids_array=explode(",",$selected_team_ids);
        foreach($selected_team_ids_array as $v)
            $this->db->query("select public.approve_team(?,?,?,?)",array($v,$owned_by,$creator,$owned_by));
    }
    
    public function save_team_unprocessed_by_team_id
    ($team_id,$team_name,$team_calibre,$manager_firstname,$manager_lastname,$manager_primaryphone,$manager_secondaryphone,$manager_email,$manager_gender        
    ,$manager_username,$operating_unit,$operating_address,$operating_city,$operating_province,$operating_country,$operating_postalcode,$shipping_attention    
    ,$shipping_co,$shipping_unit,$shipping_address,$shipping_city,$shipping_province,$shipping_country,$shipping_postalcode   
    )
    {
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------
        $this->db->query("select  public.save_team_unprocessed_by_team_id(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        ,array($team_id,$team_name,$team_calibre,$manager_firstname,$manager_lastname,$manager_primaryphone,$manager_secondaryphone,$manager_email,$manager_gender        
        ,$manager_username,$operating_unit,$operating_address,$operating_city,$operating_province,$operating_country,$operating_postalcode,$shipping_attention    
        ,$shipping_co,$shipping_unit,$shipping_address,$shipping_city,$shipping_province,$shipping_country,$shipping_postalcode,$a_o_id
        ));
    }
    
    public function get_registration_params()
    {
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------
        return $this->db->query("select * from  WEBSITE.slaves_get_registration_params(?)",array($a_o_id))->result_array();    
    }
    public function approveTeams($team_entity_ids)
    {
        $result=$this->db->query("select * from website.\"approveTeams\"(?)",array($team_entity_ids))->result_array();    
        return $result;    
    }
    public function getRegisteredTeamInfo($season_id,$team_entity_id)
    {
        $a_e_id= $this->permissions_model->get_active_entity();
        $q=
        "
            select 
                cfd.field_id,
                cfd.master_entity_id,
                cfd.slave_org_type_id,
                cfd.field_title,
                cf.entity_id,
                cf.field_value
                
                from public.customfields_def    cfd 
                left join public.customfields   cf     on cf.field_id=cfd.field_id and entity_id=?
                where 
                cfd.deleted_flag        =false
                and slave_org_type_id   =6
                and season_id           =?
                and master_entity_id    =?           
        ";
        $result=$this->db->query($q,array($team_entity_id,$season_id,$a_e_id))->result_array();    
        
        return $result;    
    }
    public function getTeamManagerInfo($team_org_id) 
    {   
        $q=
        "
            select  ep.person_id,ep.entity_id,person_fname,person_lname,person_gender,cm.value email,cm2.value home_phone,cm3.value work_phone ,lower(person_gender) as gender
            ,public.get_address_by_entity_id_commabased(ep.entity_id::int, 3, 3) address
                        ,a.user_id,a.org_id
            from permissions.assignment a 
            inner join permissions.user u      on u.user_id=a.user_id and role_id=4 and org_id=?
            inner join public.entity_person ep on u.person_id=ep.person_id

            left join public.entity_contact     ec on ec.entity_id=ep.entity_id
            left join public.lu_contact_type    ct on ct.type_id=ec.contact_type
            left join public.contact_method     cm on cm.contact_method_id=ec.contact_method_id

            left join public.entity_contact     ec2 on ec2.entity_id=ep.entity_id
            left join public.lu_contact_type    ct2 on ct2.type_id=ec.contact_type
            left join public.contact_method     cm2 on cm2.contact_method_id=ec2.contact_method_id

            left join public.entity_contact     ec3 on ec3.entity_id=ep.entity_id
            left join public.lu_contact_type    ct3 on ct3.type_id=ec.contact_type
            left join public.contact_method     cm3 on cm3.contact_method_id=ec3.contact_method_id
                       
            
            where 
            ec.contact_type=1 
            and ec2.contact_type=2 
            and ec3.contact_type=3  
            
            order by org_id
            limit 1
        ";
        $result=$this->db->query($q,array($team_org_id))->result_array();
        return $result;        
    }
    
    
    
    
    /**
    * save a record of player invitations sent by team registration
    * 
    * 
    * @param mixed $seasonid
    * @param mixed $teamentity
    * @param mixed $pname
    * @param mixed $pemail
    */
    public function insert_email_invitation($seasonid,$teamentity,$pname,$pemail)
    {
    	$params = func_get_args();
    	var_dump($params);
		$sql="SELECT public.insert_season_player_inv(?,?,?,?)";
		$r = $this->db->query($sql,$params)->first_row()->insert_season_player_inv;
		var_dump($this->db->last_query());
		return $r;
    }
    
}

?>
