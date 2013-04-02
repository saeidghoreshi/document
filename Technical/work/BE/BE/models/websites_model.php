<?php   
require_once('./endeavor/models/endeavor_model.php');
class Websites_model extends Endeavor_model
{                             
    public function __construct()
    {
        parent::__construct();
    }
    
    //Registration Guidelines
    public function get_registration_guidelines()
    {
        return $this->db->query("select * from  WEBSITE.slaves_get_registration_firstpage()")->result_array();
    }
    public function save_registration_guidelines($col,$content)
    {
        return $this->db->query("select * from  WEBSITE.slaves_save_registration_guidelines(?,?)",array($col,$content))->result_array();
    }
    //Create Dynamic Content
    public function save_dynamiccontent($rootid,$content,$linkname)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("select website.add_dynamic_content(?,?,?,?,?);",array($content,$linkname,($rootid=='null')?null:$rootid,$a_o_id,$a_u_id))->result_array();            
    }
    public function get_org_links_root($org_id)
    {
        return $this->db->query("select link_id,link_name,link_order,visible, 
        '<img src=assets/images/dev/arrow_down.png />' as arrow_down,'<img src=assets/images/dev/arrow_up.png />' as arrow_up,
        '<input id=cb_'||link_id||' type=\"checkbox\" '||case when (visible='true') then ' checked' else '' end||' onchange=\"linkordering.save_root_order();\" />' as box_visible
        from website.slaves_get_org_links(?) where link_parent_id is NULL;",array($org_id))->result_array();
    }
    public function get_org_links_sub($org_id,$root_link_id)
    {
        return $this->db->query("select link_id,link_name ,link_order,visible,
        '<img src=assets/images/dev/arrow_down.png />' as arrow_down,'<img src=assets/images/dev/arrow_up.png />' as arrow_up,
        '<input id=cb_'||link_id||' type=\"checkbox\" '||case when (visible='true') then ' checked' else '' end||' onchange=\"linkordering.save_sub_order();\" />' as box_visible
        from website.slaves_get_org_links(?) where link_parent_id is not NULL and link_parent_id=?;",array($org_id,$root_link_id))->result_array();
    }
    
    
    
    
    
    
    public function get_clients()
    {
        $search_criteria='';
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];
        
        return $this->db->query("select * from website.get_clients() where 
           lower(co_org_name) like '%'||lower(?)||'%' 
        or lower(person_name) like '%'||lower(?)||'%'
        or lower(person_email) like '%'||lower(?)||'%'
        or lower(person_phone) like '%'||lower(?)||'%'
        ",array($search_criteria,$search_criteria,$search_criteria,$search_criteria))->result_array();   
    }
    public function get_positions()
    {
        $q=
        "
            select w_p_id as type_id,w_p_alias || ' (' ||s.size_w ||' x '||s.size_h||')' as type_name  
                from website.lu_website_pos w_p
                left join website.size s on s.size_id=w_p.size_id
                where w_p_type_id=1
                order by s.size_w desc
        ";
        return $this->db->query($q)->result_array();       
    }
    public function get_clients_assignedto_campaigns($campaign_id)
    {
        return $this->db->query(
        "
        select distinct cb.campaign_id ,co_org_id, co_org_name  
            from website.get_clients() c 
            inner join website.banner            b   on b.client_org_id=c.co_org_id
            inner join website.campaign_banner   cb  on b.banner_id=cb.banner_id

        where exists 
        (
                select 1 
                from website.banner b
                inner join website.campaign_banner   cb  on b.banner_id=cb.banner_id
                where c.co_org_id=b.client_org_id    
        )
        and cb.deleted_flag=false and cb.campaign_id={$campaign_id}
        
        ")->result_array();   
    }
    public function get_sizes()
    {
        return $this->db->query("select * from website.get_sizes()")->result_array();   
    }
    
    //Link Ordering
    public function save_links_order($link_ids,$orders,$visibles)
    {
        $link_ids_arr   =explode(',',$link_ids);
        $orders_arr     =explode(',',$orders);
        $visibles_arr   =explode(',',$visibles);
        
        for($i=0;$i<count($orders_arr);$i=$i+1)                                                                              
            $this->db->query("select website.slaves_save_link_order(?,?,?);",array($link_ids_arr[$i],$orders_arr[$i],$visibles_arr[$i]));
    }
    
    //Advertising ------------------------------------------------------*******************************************************
    //Campaign banners
    public function get_campaignbanners()
    {
        $search_criteria='';
        $extra_where='';
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];
        if(isset($_REQUEST["client_org_id"]) && $_REQUEST["client_org_id"] != '-1')$extra_where="and client_org_id=".$_REQUEST["client_org_id"];
        
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        
        //Update Camapaign and CB which Expired
        $update_query=
        "
            update website.campaign set paused=true where end_date::date<current_date;
            update website.campaign_banner cb set paused=true where exists (select 1 from website.campaign c where c.campaign_id=cb.campaign_id and end_date::date<current_date);
            update website.campaign set paused=false where end_date::date>=current_date;
            update website.campaign_banner cb set paused=false where not exists (select 1 from website.campaign c where c.campaign_id=cb.campaign_id and end_date::date<current_date);
        ";
        $this->db->query($update_query);
        
        
        $q="select 
            c.campaign_id,c.campaign_name,c.start_date,c.end_date,c.paused as campaign_paused
            ,b.banner_id,b.banner_name,b.banner_filename,b.client_org_id,o.org_name as client_name,b.banner_type_id,bt.type_name as  banner_type_name,b.banner_script,b.size_id as banner_size_id,s.size_w,s.size_h
            ,pos.w_p_id ,pos.w_p_alias as  name        
            ,b.clickurl,cb.paused,cb.campaign_banner_id,b.clicks,b.views

            from website.campaign c
            left join website.campaign_banner cb on cb.campaign_id=c.campaign_id
            left join website.banner                 b  on cb.banner_id=b.banner_id
            left join public.entity_org                 o  on o.org_id=b.client_org_id
            left join website.lu_banner_type         bt on bt.type_id=b.banner_type_id
            left join website.size                   s  on s.size_id=b.size_id
            left join website.lu_website_pos         pos on pos.w_p_id=cb.pos_id

            where (b.deleted_flag=FALSE  or b.deleted_flag is null)
            and  (c.deleted_flag=FALSE  or c.deleted_flag is null)    
            --and cb.views<=cb.max_views 
            --and cb.clicks<cb.max_clicks 
            and c.owned_by=?
            and 
            (
                    lower(campaign_name) like '%'||lower(?)||'%'
                or  lower(banner_name) like '%'||lower(?)||'%'
            )
            ".
            $extra_where
            ."
            order by c.campaign_id
            "  ;
        return $this->db->query($q,array($a_o_id,$search_criteria,$search_criteria))->result_array();
    }
    public function get_campaign_banners_by_campid($campaign_id)
    {
        $search_criteria        ='';
        $extra_where            ='';
        $whereCheckCampaignId   ='';
        
        if(intval($campaign_id)!=-1)
            $whereCheckCampaignId=" and c.campaign_id={$campaign_id}";
        
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];
        if(isset($_REQUEST["client_org_id"]) && $_REQUEST["client_org_id"] != '-1')$extra_where="and client_org_id=".$_REQUEST["client_org_id"];
        $q="select 
            c.campaign_id,c.campaign_name,c.start_date,c.end_date
            ,b.banner_id,b.banner_name,b.banner_filename,b.client_org_id,o.org_name as client_name,b.banner_type_id,bt.type_name as  banner_type_name,b.banner_script,b.size_id as banner_size_id,s.size_w,s.size_h
            ,pos.w_p_id ,pos.w_p_alias as  name        
            ,b.clickurl,cb.paused,cb.campaign_banner_id  ,b.clicks,b.views

            from website.campaign c
            inner join website.campaign_banner cb    on cb.campaign_id   =c.campaign_id
            inner join website.banner          b     on cb.banner_id     =b.banner_id
            inner join public.entity_org       o     on o.org_id         =b.client_org_id
            inner join website.lu_banner_type  bt    on bt.type_id       =b.banner_type_id
            inner join website.size            s     on s.size_id        =b.size_id
            inner join website.lu_website_pos  pos   on pos.w_p_id       =cb.pos_id

            where 
            --and cb.views<=cb.max_views 
            --and cb.clicks<cb.max_clicks 
            (b.deleted_flag=false       or    b.deleted_flag is null)     
            and (
                    lower(campaign_name)    like '%'||lower(?)||'%'  
                or  lower(banner_name)      like '%'||lower(?)||'%'  
                or  lower(o.org_name)       like '%'||lower(?)||'%'   
                or  lower(w_p_alias)        like '%'||lower(?)||'%'  
                or  lower(clickurl)         like '%'||lower(?)||'%'  
                )
            ".$extra_where
            .$whereCheckCampaignId
            ."order by c.campaign_id"  ;
        return $this->db->query($q,array($search_criteria,$search_criteria,$search_criteria,$search_criteria,$search_criteria))->result_array();
    }
    public function get_org_banners()
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $search_criteria='';
        $extra_where    ='';
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];
        if(isset($_REQUEST["client_org_id"]) && $_REQUEST["client_org_id"] != '-1')$extra_where="and client_org_id=".$_REQUEST["client_org_id"];

        $q="select 
            b.banner_id,b.banner_name,b.banner_filename,b.client_org_id,o.org_name as client_name,b.banner_type_id,bt.type_name as  banner_type_name,b.banner_script,b.size_id as banner_size_id,s.size_w,s.size_h
            ,s.size_w||'X'||s.size_h as  name  
            ,b.clickurl,b.clicks,b.views,b.isactive

            from website.banner b
            left join public.entity_org                   o  on o.org_id=b.client_org_id
            left join website.lu_banner_type     bt on bt.type_id=b.banner_type_id
            left join website.size                     s  on s.size_id=b.size_id

            where (b.deleted_flag=FALSE  or b.deleted_flag is null)    AND b.owned_by=?
            and 
            (
                    lower(banner_name) like '%'||lower(?)||'%' 
                    or  lower(o.org_name) like '%'||lower(?)||'%'   
                    or  lower(clickurl) like '%'||lower(?)||'%' 
            )
            ".
            $extra_where
            ."
            order by b.banner_id "  ;
        return $this->db->query($q,array($a_o_id,$search_criteria,$search_criteria,$search_criteria))->result_array();
    }
    public function new_campaign($campaign_name,$start_date,$end_date)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $this->db->query('select * from website."new_campaign"(?,?,?,?,?);',array($campaign_name,$start_date,$end_date,$a_u_id,$a_o_id));      
        return 1;
    }
    public function update_campaign($campaign_id,$campaign_name,$start_date,$end_date)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        
        $this->db->query('select * from website."update_campaign"(?,?,?,?,?)',array($campaign_id,$campaign_name,$start_date,$end_date,$a_o_id));      
    }
    public function delete_campaign($campaign_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website.delete_campaign (?,?,?);'
            ,array($campaign_id,$a_u_id,$a_o_id))->result_array();      
    }
    public function pause_or_play_campaign($campaign_id,$pause_or_play)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website.pause_or_play_campaign(?,?);',array($pause_or_play,$campaign_id))->result_array();      
    }
    public function pause_play_campaignbanner($cb_id,$pause_or_play)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $this->db->query('update website.campaign_banner cb set paused=? where campaign_banner_id=?
            and ( 
                   exists(select 1 from website.cb_association where cb_id=cb.campaign_banner_id) 
                or exists(select 1 from website.cb_country where cb_id=cb.campaign_banner_id) 
                or exists(select 1 from website.cb_region where cb_id=cb.campaign_banner_id) 
                or exists(select 1 from website.cb_city where cb_id=cb.campaign_banner_id) 
                or exists(select 1 from website.cb_league where cb_id=cb.campaign_banner_id) 
                )'
            ,array($pause_or_play,$cb_id));//->result_array();      
            return 1;
    }
    public function new_banner($client_list,$banner_name,$changed_file_name,$mime,$script,$size_list,$width, $height,$clickurl)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from "website"."new_banner"(?,?,?,?,?,?,?,?,?,?,?);'
            ,array($client_list,$banner_name,$changed_file_name,$mime,$script,$size_list,$width,$height,$clickurl,$a_u_id,$a_o_id))->result_array();
    }
    public function new_banner_only_script($client_list,$banner_name,$script)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from "website"."new_banner_only_script"(?,?,?,?,?);'
            ,array($client_list,$banner_name,$script,$a_u_id,$a_o_id))->result_array();    
    }
    
    public function update_banner_withfilereplace($banner_id,$client_list,$banner_name,$changed_file_name,$mime,$script,$size_list,$width, $height,$clickurl)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from "website"."update_banner_withfilereplace"(?,?,?,?,?,?,?,?,?,?,?,?);'
            ,array($banner_id,$client_list,$banner_name,$changed_file_name,$mime,$script,$size_list,$width,$height,$clickurl,$a_u_id,$a_o_id))->result_array();
    }
    public function update_banner_withoutfilereplace($banner_id,$client_list,$banner_name,$script,$clickurl)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from "website"."update_banner_withoutfilereplace"(?,?,?,?,?,?,?);'
            ,array($banner_id,$client_list,$banner_name,$script,$clickurl,$a_u_id,$a_o_id))->result_array();
    }
    public function update_banner_only_script($banner_id,$client_list,$banner_name,$script)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from "website"."update_banner_only_script"(?,?,?,?,?,?);'
            ,array($banner_id,$client_list,$banner_name,$script,$a_u_id,$a_o_id))->result_array();    
    }
    public function assign_banner_campaign_pos($campaign_id,$banner_id,$pos_name,$banner_size_id,$max_clicks,$max_views)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        
        //----------------------------------------------------------------------
        return $this->db->query('select * from "website"."assign_banner_campaign_pos"(?,?,?,?,?,?,?,?);'
            ,array($campaign_id,$banner_id,$pos_name,$banner_size_id,$max_clicks,$max_views,$a_u_id,$a_o_id))->result_array();    
    }                                                                       
    public function new_client($client_name ,$fname , $lname , $phone , $email )
    { 
    	$a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------      
        return $this->db->query("select * from website.new_client(?,?,?,?,?,?,?)",array($client_name ,$fname , $lname , $phone , $email, $a_u_id,$a_o_id))->result_array();
    }
    public function update_client($client_org_id,$person_id,$client_name ,$fname , $lname , $phone , $email )
    { 
        $a_u_id = $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------      
        return $this->db->query("select * from website.update_client(?,?,?,?,?,?,?,?,?)",array($client_org_id,$person_id,$client_name ,$fname , $lname , $phone , $email , $a_u_id , $a_o_id ))->result_array();
    }
    public function delete_client($client_org_id )
    { 
        $a_u_id = $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------      
        return $this->db->query("select * from website.delete_client(?,?,?)",array($client_org_id, $a_u_id , $a_o_id ))->result_array();
    }
    public function delete_banner($banner_id)
    { 
        $a_u_id = $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------      
        return $this->db->query("select * from website.delete_banner(?,?,?)",array($banner_id, $a_u_id , $a_o_id ))->result_array();
    }
    //************************Limits regions
    public function get_all_associations_forlimits($cb_id)
    {
        return $this->db->query(
        "
        select distinct entity_id as id , org_name as name  ,cba.cb_id ,case when (cb_id is null)then 'false' else 'true' end as checked
            from public.entity_org eo
            left join website.cb_association cba on cba.assoc_id=eo.entity_id and cba.cb_id=?
            where org_type=2 and deleted_flag=false
        ",array($cb_id))->result_array();     
    }
    public function get_all_countries_forlimits($cb_id)
    {   
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $search_criteria='';
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];
        
        $q="
            select distinct countries.country_id as id , country_name as name ,cbc.cb_id ,case when (cb_id is null)then 'false' else 'true' end as checked
            from public.entity_org                        eo
                    left join public.address                        a                  on a.address_country=(select country_id from finance.get_org_addressing_info(1))/*ssi*/
                    left join public.lu_address_country  countries  on a.address_country=countries.country_id
                    left join website.cb_country                  cbc                 on countries.country_id=cbc.country_id and  cb_id=?
            where eo.org_type=2
            and
            (
                lower(country_name) like '%'||lower(?)||'%'                             
            )
        ";
        return $this->db->query($q,array($cb_id,$search_criteria))->result_array();
    }
    public function get_all_regions_forlimits($cb_id,$country_id)
    {
        $search_criteria='';
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];
        $q="
        select regions.region_id as id , region_name as name ,cb_id ,case when (cb_id is null)then 'false' else 'true' end as checked
        from  public.lu_address_region  regions
        left join  website.cb_region as cbr on regions.region_id=cbr.region_id and cb_id={$cb_id}
        where country_id={$country_id} 
        and 
        (
            lower(region_name) like '%'||lower(?)||'%'
        )
        ";
        return $this->db->query($q,array($search_criteria))->result_array(); 
    }
    public function get_all_cities_forlimits($cb_id,$region_id)
    {
        $search_criteria='';
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];
        $q="                                                             
        select cities.city_id as id , city_name as name ,cb_id ,case when (cb_id is null)then 'false' else 'true' end as checked
        from  public.lu_address_city  cities
        left join  website.cb_city as cbc on cities.city_id=cbc.city_id  and cb_id={$cb_id}
        where 
        region_id={$region_id}
        and 
        (
            lower(city_name) like '%'||lower(?)||'%'
        )
        ";
        return $this->db->query($q,array($search_criteria))->result_array(); 
    }
    public function get_all_leagues_forlimits($cb_id,$city_id)
    {
        $search_criteria='';
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];
        $q="
            select distinct league.league_id as id , league_name as name ,cb_id ,case when (cb_id is null)then 'false' else 'true' end as checked
            from public.league league
                left join website.cb_league     cbl     on league.league_id=cbl.league_id 
                left join public.entity_org     eo      on eo.org_id=league.org_id
                left join public.entity_address ea      on ea.entity_id=eo.entity_id
                left join public.address        a       on a.address_id=ea.address_id       and a.address_city_id=(select city_id from finance.get_org_addressing_info(league.org_id))
        where league.deleted_flag=false 
        and a.address_city_id={$city_id}
        and 
        (
            lower(league_name) like '%'||lower(?)||'%'
        )
        ";
        return $this->db->query($q,array($search_criteria))->result_array();      
    }
    public function get_limits_selected_link($cb_id)
    {                                                     
        return $this->db->query("select * from website.get_limits_selected_link(?)",array($cb_id))->result_array();          
    }
    public function assign_associations_to_cb($cb_id,$ids)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website.assign_associations_to_cb(?,?,?,?)',array($cb_id,$ids,$a_u_id,$a_o_id))->result_array();      
    }
    public function assign_countries_to_cb($cb_id,$ids)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website.assign_countries_to_cb(?,?,?,?)',array($cb_id,$ids,$a_u_id,$a_o_id))->result_array();      
    }
    public function assign_regions_to_cb($cb_id,$ids)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website.assign_regions_to_cb(?,?,?,?)',array($cb_id,$ids,$a_u_id,$a_o_id))->result_array();      
    }
    public function assign_cities_to_cb($cb_id,$ids)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website.assign_cities_to_cb(?,?,?,?)',array($cb_id,$ids,$a_u_id,$a_o_id))->result_array();      
    }
    public function assign_leagues_to_cb($cb_id,$ids)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website.assign_leagues_to_cb(?,?,?,?)',array($cb_id,$ids,$a_u_id,$a_o_id))->result_array();      
    }
    
    
    
    public function count_url_in_use($url)
    {
    	$params=func_get_args();
		$sql="SELECT COUNT(*) AS count FROM public.url WHERE url = ?";
        return $this->db->query($sql,$params)->first_row()->count;    
		
    }
    
    
    
    
    
    
    //Uploading function
    /**
    * @deprecated, this is not called anywhere. also it does not use CI->FTP
    * do not use it
    * 
    *
    public function upload_file($file_dim)
    {   
        $dim=explode('X',$file_dim);                      
        $w=trim($dim[0]);
        $h=trim($dim[1]);
        
        $all_banner_names='';         
        //Getting file info and uploading to server and returning filenames in comma based form
        $file=$_FILES["websites-adv-upload-file1"];
        //check file dimention
        list($width, $height, $type, $attr) = getimagesize($file["tmp_name"]); 
        if($w!=$width){echo var_dump("Width is not ".$w);return -1;}
        if($h!=$height){echo var_dump("Height is not ".$h);return -1;}
        //check file dimention END                
        if($file["tmp_name"]=='')continue;
        
        $banner_name='B-'.date("Y-m-d-G-i-s-".(string)(1));
        $file_name_array=explode(".",$file["name"]);
        $extension=$file_name_array[count($file_name_array)-1];
        $changed_file_name=$banner_name.".$extension";
        $all_banner_names.=$changed_file_name.',';
        
        move_uploaded_file($file["tmp_name"],"assets/UploadedFiles/" .$changed_file_name);
        if($file["error"]>0) return "Error Happend";
        
        while(!file_exists("assets/UploadedFiles/$changed_file_name"));
        //1
        ///*
        $this->ftp_files(
        "assets/UploadedFiles/$changed_file_name"
        ,"uploaded/banner-assets/$changed_file_name"
        ,'global.playerspectrum.com'
        ,"ryanglobal@playerspectrum.com"
        ,"p0$31d0n"
        ,"binary");                             
        
        //2
        $this->ftp_files(
        "assets/UploadedFiles/$changed_file_name"
        ,"UploadedFiles/Banners/$changed_file_name"
        ,'website.playerspectrum.com'
        ,"ryan@playerspectrum.com"
        ,"p0$31d0n"
        ,"binary");                             
            
        return substr($all_banner_names,0,strlen($all_banner_names)-1);
    }*/
    /**
    * @deprecated
    * do not use this
    * 
    * @param mixed $_local_file
    * @param mixed $_remote_file
    * @param mixed $_remotehost
    * @param mixed $_username
    * @param mixed $_password
    * @param mixed $_mode
    *
    public function ftp_files($_local_file,$_remote_file,$_remotehost,$_username,$_password,$_mode="ascii")
    {               
        $conn_id = ftp_connect($_remotehost, 21) or die ("Cannot connect to host");
        ftp_login($conn_id, $_username, $_password) or die("Cannot login");
        if($_mode=="ascii")$upload = ftp_put($conn_id, $_remote_file, $_local_file, FTP_ASCII);
        if($_mode=="binary")$upload = ftp_put($conn_id, $_remote_file, $_local_file, FTP_BINARY);
        ftp_close($conn_id);
        return "\n$_local_file Uploaded";
    }*/
    
    
    //PUBLISHING  ************************************************************************************
    public function get_articles()
    {
        $search_criteria='';
        if($this->input->get_post('query'))$search_criteria=$this->input->get_post('query');

        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $q=
        "
         select 
            article_id,article_title,article_intro,article_type_id,t.type_name as article_type_name,article_content,views

            ,publish_date        ,to_char(publish_date,'YYYY/mm/dd') as publish_date_display
            ,unpublish_date    ,to_char(unpublish_date,'YYYY/mm/dd') as unpublish_date_display

            ,a.created_by ,p1.person_fname||', '||p1.person_lname as created_by_name
            ,a.modified_by,p2.person_fname||', '||p2.person_lname as modified_by_name
            
            ,a.created_on
            ,a.modified_on
            
            
            from website.article a 
            LEFT OUTER join website.lu_article_type t on t.type_id=a.article_type_id
            LEFT OUTER join public.entity_person p1 on p1.entity_id=a.created_by
            LEFT OUTER join public.entity_person p2 on p2.entity_id=a.modified_by
            where a.owned_by=?
            and 
            (
                    lower(article_title) like '%'||lower(?)||'%'          
                or  lower(article_intro) like '%'||lower(?)||'%'          
                or  lower(t.type_name) like '%'||lower(?)||'%'          
                or  lower(article_content) like '%'||lower(?)||'%'          
            )
            and a.deleted_flag=false
        ";
        return $this->db->query($q,array($a_o_id,$search_criteria,$search_criteria,$search_criteria,$search_criteria))->result_array();
    }
    public function new_article($article_title,$publish_date,$unpublish_date,$article_type,$article_intro,$article_content,$auto_link,$org_id=null)
    {
    	
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        if($org_id) $a_o_id = $org_id;//if org id given, use that instead
        //----------------------------------------------------------------------
        return $this->db->query("select * from website.new_article(?,?,?,?,?,?,?,?,?);",array($article_title,$publish_date,$unpublish_date,$article_type,$article_intro,$article_content,$auto_link,$a_u_id,$a_o_id))->result_array();
    }
    public function update_article($article_id,$article_title,$publish_date,$unpublish_date,$article_type,$article_intro,$article_content)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("select * from website.update_article(?,?,?,?,?,?,?,?,?);",array($article_id,$article_title,$publish_date,$unpublish_date,$article_type,$article_intro,$article_content,$a_u_id,$a_o_id))->result_array();    
    }
    public function update_article_row_edit($article_id,$article_title,$publish_date,$unpublish_date,$article_type_name)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("select * from website.update_article_row_edit(?,?,?,?,?,?,?);",array($article_id,$article_title,$publish_date,$unpublish_date,$article_type_name,$a_u_id,$a_o_id))->result_array();    
    }
    
    public function delete_article($article_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("select * from website.delete_article(?,?,?);",array($article_id,$a_u_id,$a_o_id))->result_array();    
    }
    public function publish_or_unpublish_article($article_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("select * from website.publish_or_unpublish_article(?,?,?);",array($article_id,$a_u_id,$a_o_id))->result_array();    
    }
    public function upload_article_image($name)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("select * from website.upload_article_image(?,?,?);",array($name,$a_u_id,$a_o_id))->result_array();                
    }
    public function get_articleImages()
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $q=
        "
         select 
            file_name as id,'uploaded/article-assets/files/large/' ||file_name||'.'||file_ext as template_image, file_name||'.'||file_ext as template_name , '' as template_title
                              
            ,t.created_by ,p1.person_fname||', '||p1.person_lname as created_by_name
            ,t.modified_by,p2.person_fname||', '||p2.person_lname as modified_by_name
            
            ,t.created_on
            ,t.modified_on
            
            
            from website.article_files t 
            inner join public.entity_person p1 on p1.entity_id=t.created_by
            inner join public.entity_person p2 on p2.entity_id=t.created_by
            where t.owned_by=?
            and t.deleted_flag=false
        ";
        return $this->db->query($q,array($a_o_id))->result_array();    
    }
    //Links
    public function get_linkTypes()
    {
        $a_e_id= $this->permissions_model->get_active_entity();
        //----------------------------------------------------------------------
        $q=
        "
            select type_id,type_name 
            from website.lu_link_type
            where case when (type_name!='System' and '1'!=?) or ('1'=?) then true else false end 
            ";
        return $this->db->query($q,array($a_e_id,$a_e_id))->result_array();        
    }
    public function get_links($link_type_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $search_criteria='';
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];
        
        $filter='';
        if(intval($link_type_id)!=0)
            $filter=' And link_type_id='.$link_type_id;
            
        $q=
        "
            select      
            l.link_id,
            l.link_name,
            l.link_url,
            l.link_article_id,
            l.link_parent_id,
            l.link_type_id,
            l.owned_by,
            wl.link_parent_id,
            wl.org_id,
            wl.isactive ,
            wl.link_order as order,
            lt.type_name as link_type_name
            
            from     website.link l 
            left join website.website_link  wl  on wl.link_id    =l.link_id     and wl.org_id =?  --and  wl.isactive =true
            left join website.lu_link_type  lt  on lt.type_id    =l.link_type_id 
                        
            where  
            l.visible=true
            and 
            (
                    l.owned_by=?                        --already               set for me
                or  l.owned_by=1                        --[System link]         Not Set For Me 
                or  l.owned_by=public.get_parent_org(?) --[Assocition Links]    Not Set For Me 
            )
            and 
            (
                lower(link_name) like '%'||lower(?)||'%'  
            )
        ";
        $q.=$filter;
        
        return $this->db->query($q,array($a_o_id,$a_o_id,$a_o_id,$search_criteria))->result_array();
    }
    public function update_links_ordering($link_parent_combo)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("select * from website.update_links_ordering(?,?)",array($link_parent_combo,$a_o_id))->result_array();    
    }  
    public function get_paged_articles()
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $q=
        "
         select article_id as type_id , article_title as type_name 
            from website.article as a
            where a.owned_by=?
                and a.deleted_flag=false
                and article_type_id=3
        ";
        return $this->db->query($q,array($a_o_id))->result_array();    
    }
    public function new_link($title ,$type_id , $url ,$article_id ,$isactive)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("select * from website.new_link(?,?,?,?,?,?,?);",array($title ,$type_id , $url ,$article_id,$isactive,$a_u_id,$a_o_id))->result_array();    
    }
    public function update_link($link_id,$title ,$type_id , $url ,$article_id ,$isactive)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("select * from website.update_link(?,?,?,?,?,?,?,?);",array($link_id,$title ,$type_id , $url ,$article_id,$isactive,$a_u_id,$a_o_id))->result_array();    
    }
    public function delete_link($link_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("select * from website.delete_link(?,?,?);",array($link_id,$a_u_id,$a_o_id))->result_array();    
    }
    public function hide_or_unhide_link($link_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("select * from website.hide_or_unhide_link(?,?,?);",array($link_id,$a_u_id,$a_o_id))->result_array();        
    }
    //templates
    public function get_org_template_list()
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $q=
        "
         select template_id as type_id ,template_title  as type_name 
            from website.template
        ";
        return $this->db->query($q)->result_array();
    }
    public function get_templates()
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        //save url in session
        $this->permissions_model->get_org_website_url();
        
        $q=
        "
         select 
            t.template_id as id,t.template_id,template_title,template_logo,isfree,isavailable,wt.isactive
                              
            ,t.created_by ,p1.person_fname||', '||p1.person_lname as created_by_name
            ,t.modified_by,p2.person_fname||', '||p2.person_lname as modified_by_name
            
            ,t.created_on
            ,t.modified_on
            ,? as website_url
            
            from website.template t 
            left  join website.website_template wt on wt.template_id=t.template_id and org_id=?
            
            left join public.entity_person p1 on p1.entity_id=t.created_by
            left join public.entity_person p2 on p2.entity_id=t.created_by
            where t.owned_by=1
            and t.deleted_flag=false 
        ";
                          
        return $this->db->query($q,array($_SESSION["url"],$a_o_id))->result_array();
    }
    public function activate_template($template_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query("select * from website.activate_template(?,?,?);",array($template_id,$a_u_id,$a_o_id))->result_array();            
    }
    //Modules
    public function get_websiteModules($w_p_id)
    {
        $search_criteria='';
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];
        
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $q=
        "
            select * from website.\"get_selected_modules\" (?,?) X
            where 
            (
                lower(X.module_name) like '%'||lower(?)||'%'
            )          
        ";
        return $this->db->query($q,array($w_p_id,$a_o_id,$search_criteria))->result_array();
    }
    public function get_modules($w_p_id)  //for activation window
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $q=
        "
            select  
             m.module_id as id
            ,m.module_id 
            ,m.module_name
            ,module_icon
            ,isfree,w_m_id,wp.w_p_id,wp.w_p_name,wp.w_p_alias,wm.isactive , website.get_website_module_opt_count(w_m_id) as w_m_opt_count
            ,wm.order
            from website.lu_module  m     
                left  join  website.website_module   wm    on wm.module_id=m.module_id AND wm.org_id=?   and w_p_id =?
                left  join  website.lu_website_pos   wp    on wp.w_p_id = wm.w_p_id    AND wm.org_id=?
        ";
        if($w_p_id!=13)$q.=" where canbe_standalone=false";
        return $this->db->query($q,array($a_o_id,$w_p_id,$a_o_id))->result_array();
    }
    public function update_org_moduel_pos($selected_WMid,$w_p_alias)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website."update_org_moduel_pos"(?,?,?,?);',array($selected_WMid,$w_p_alias,$a_u_id,$a_o_id))->result_array();
    }
    public function get_location_pos()
    {
        $q=
        "
                select w_p_id as type_id,w_p_alias as type_name  
                from website.lu_website_pos w_p
                left join website.size s on s.size_id=w_p.size_id
                where w_p_type_id=2
                order by s.size_w desc
        ";
        return $this->db->query($q)->result_array();                    
    }
    public function add_selectedModules($selected_Mids,$w_p_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website."add_selectedModules"(?,?,?,?);',array($selected_Mids,$w_p_id,$a_u_id,$a_o_id))->result_array();            
    } 
    public function delete_selectedModules($selected_WMids)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website."delete_selectedModules"(?,?,?);',array($selected_WMids,$a_u_id,$a_o_id))->result_array();            
    }
    public function get_moduleOpts($module_id)
    {
        $search_criteria='';
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];

        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        $q=
        "
             select wmo.w_m_id,mo.m_opt_id,mo.opt_name,mo.opt_value
                from website.lu_module_opt mo 
                left join website.website_module_opt wmo         on wmo.m_opt_id=mo.m_opt_id and mo.module_id=?
                where  module_id=?
                and lower(mo.opt_name) like '%'||lower(?)||'%'
        ";
        return $this->db->query($q,array($module_id,$module_id,$search_criteria))->result_array();                
    }
    public function update_selectedOpts($w_m_id,$selected_ids)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website."update_selectedOpts"(?,?,?,?);',array($w_m_id,$selected_ids,$a_u_id,$a_o_id))->result_array();            
    }
    public function play_pause_selectedWebsiteModule($w_m_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website."play_pause_selectedWebsiteModule"(?,?,?);',array($w_m_id,$a_u_id,$a_o_id))->result_array();                
    }
    
        
    /**
    * @deprecated instead of updating ,this function actually
    * deletes all of them and tries to insert them again. Somehow it fails to re insert, and we are lest with nothing
    * 
    * intsead use update_website_module_order once for each item. 
    * 
    */
    public function update_websiteModules_ordering($wmIds,$mIds,$wmOrders,$w_p_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //----------------------------------------------------------------------
        return $this->db->query('select * from website."update_websiteModules_ordering"(?,?,?,?);',array($wmIds,$mIds,$wmOrders,$w_p_id))->result_array();                    
    }
    /**
    * for the given module. set the order to the given number
    * 
    * @param mixed $w_m_id
    * @param mixed $order
    */
    public function update_website_module_order($w_m_id,$order)
    {
		$params=func_get_args();
		$sql="SELECT website.update_website_module_order(?,?)";
		return $this->db->query($sql,$params)->first_row()->update_website_module_order;
    }
    
    
    
	public function delete_module_asset($module_asset_id,$user)
	{
		$params=func_get_args();
		$sql="SELECT website.delete_module_asset(?,?)";
		return $this->db->query($sql,$params)->first_row()->delete_module_asset;
	}
	/**
	* enter url as  and module asset id to replace
    * old url with given one
	* 
	* @param mixed $ma_id
	* @param mixed $url
	* @param mixed $user
	*/
    public function update_asset_url($ma_id,$url,$user)
    {
		$params=func_get_args();
		$sql="SELECT website.update_asset_url(?,?,?)";
		return $this->db->query($sql,$params)->first_row()->update_asset_url;
    }
    
    
    public function update_asset_order($ma_id,$order,$user)
    {
		$params=func_get_args();
		$sql="SELECT website.update_asset_order(?,?,?)";
		return $this->db->query($sql,$params)->first_row()->update_asset_order;
    }
    
    
    /**
    * insert new asset
    * will use FTP library to upload image
    * then save the path and generated filename in database
    * 
    * @param mixed $file
    * @param mixed $wm_id
    * @param mixed $url
    * @param mixed $user
    * @param mixed $owner
    */
    public function insert_module_asset($file,$wm_id,$url,$user,$owner)
    {
    	
    	$this->load->library('ftp');   
 
		$today=date('YmdHis');

		$filepath = "uploaded/modules/".$today.'-'.$wm_id.'-'.$file['name'];//example:  /20110609-94-softball.png
 		$filepath=str_replace(' ','',$filepath);//replace whitespace
		$this->ftp->upload($file["tmp_name"], $filepath);
 
    	
		$params=array($wm_id,$url,$filepath,$user,$owner);
		$sql="SELECT website.insert_module_asset(?,?,?,?,?)";
		return $this->db->query($sql,$params)->first_row()->insert_module_asset;
    }
    /**
    * get all assets for the given module id
    * 
    * @param mixed $w_m_id
    */
    public function get_module_assets($w_m_id)
    {
		$params=func_get_args();
		$sql="SELECT a.module_asset_id, a.w_m_id, a.url, a.filepath 
					,m.org_id,m.module_id 
					,a.display_order
			FROM website.website_module_asset a 
			INNER JOIN website.website_module m ON m.w_m_id = a.w_m_id AND a.w_m_id=?  
			 AND a.deleted_flag=FALSE
			 ORDER BY a.display_order ASC";
		return $this->db->query($sql,$params)->result_array();
    }
    
    
}
