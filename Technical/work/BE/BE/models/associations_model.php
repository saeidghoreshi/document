<?php
require_once('./endeavor/models/org_model.php');
class Associations_model extends Org_model
{
    
    public function __construct()
    {
        parent::__construct();
        //$this->DB = $this->load->database('default', true);
        //$this->update_user_activity();
    }
    
    public function org_id($asn_id)
    {
		return $this->db->query("SELECT org_id FROM public.association WHERE association_id=?",$asn_id)->first_row()->org_id;
    }
 
    
    
  
    public function save_league_season_reg($season_name,$form_name,$start_date,$end_date,$deposit_due,$total_due)
    {
        return $this->db->query("select * from public.save_league_season_reg(?,?,?,?,?,?,?);"
            ,array($season_name,$form_name,$start_date,$end_date,$deposit_due,$total_due,14))->result_array();
    }
 
    public function league_season_reg()
    {
        return $this->db->query("select * from public.league l INNER JOIN public.season  s ON s.league_id = l.league_id ;")->result_array();    
    }
    public function new_form($form_name)
    {
        return $this->db->query("select * from public.new_form(?,?);",array($form_name,14))->result_array();
    }
    public function form_field_type_list()
    {
        //return $this->db->query("select * from public.form_field_type_list();")->result_array();    
    }
    public function form_field_value_type_list()
    {
       // return $this->db->query("select * from public.form_field_value_type_list();")->result_array();    
    }
    public function get_seasons()
    {
        return $this->db->query("select * from public.season")->result_array();
    }
    public function get_forms()
    {
        //return $this->db->query("select * from public.get_forms();")->result_array();
    }
    public function add_form_field($form_id,$field_name,$field_code,$field_type,$field_required,$field_value_type,$field_value_strict)
    {
        return $this->db->query("select * from public.add_form_field(?,?,?,?,?,?,?,?);",array($form_id,$field_name,$field_code,$field_type,$field_required,$field_value_type,$field_value_strict,14))->result_array();    
    }
    public function get_form_fields($form_id)
    {
       // return $this->db->query("select * from public.get_form_fields(?);",array($form_id))->result_array();
    }
    public function association_list_per_owner($owner_id)
    {
       return $this->db->query("select * from public.association_list_per_owner(?);",array($owner_id))->result_array();
    }
    public function get_league_season()
    {
       return $this->db->query("select * from public.get_league_season();",array())->result_array();
    }
    
    public function get_association_byorg($org_id)
    {
    	$params=func_get_args();
		$sql="        SELECT asn.association_id,asn.association_name,asn.website 
        				FROM public.association asn INNER JOIN public.entity_org eo 
        				ON eo.org_id = asn.org_id AND eo.org_id = ? ";
        return $q=$this->db->query($sql,$params)->result_array();
    }
    
    
    public function get_associations($parent_org_id=1)//default to system entity id if not given 
    {
    	$params=func_get_args();
    	$where='';
    	$search=$this->input->get_post('query');//from grid search bar. may not exist
    	if($search)
    	{
			$params[]=$search;
			$where = "WHERE a.association_name LIKE %||lower(?)||%";
    	}
        $sql="
        SELECT asn.association_id,asn.association_name,asn.org_id ,eo.entity_id,
        				asn.website,
						a.address_street,
						a.address_lat,
						a.address_id,
						a.address_lon,
						a.address_city,  
						(SELECT luc.country_abbr FROM lu_address_country luc WHERE luc.country_id=a.address_country ) AS country_abbr,
						a.address_country, 
						a.address_region,     
						(SELECT lur.region_abbr FROM lu_address_region lur WHERE lur.region_id=a.address_region     ) AS region_abbr,                        
						lup.postal_value 

		, (SELECT COUNT(*) FROM public.entity_org lo 
			INNER JOIN public.entity_relationship er 
		ON lo.entity_id = er.child_id AND er.parent_id=eo.entity_id and lo.org_type=3 and lo.deleted_flag=FALSE 
   INNER JOIN public.league l ON l.org_id = lo.org_id AND l.league_type=1 ) as league_count
   
		, (SELECT COUNT(*) FROM public.entity_org lo 
			INNER JOIN public.entity_relationship er 
		ON lo.entity_id = er.child_id AND er.parent_id=eo.entity_id and lo.org_type=3 and lo.deleted_flag=FALSE 
   INNER JOIN public.league l ON l.org_id = lo.org_id AND l.league_type=2 ) as tourn_count 

		, (SELECT COUNT(*) FROM public.entity_org lo 
			INNER JOIN public.entity_relationship er 
		ON lo.entity_id = er.child_id AND er.parent_id=eo.entity_id and lo.org_type=3 and lo.deleted_flag=FALSE 
  INNER JOIN public.entity_relationship ert ON ert.parent_id = lo.entity_id 
	INNER JOIN public.entity_org tmo ON ert.child_id = tmo.entity_id AND tmo.deleted_flag=FALSE) as team_count

 		, (SELECT COUNT(DISTINCT person_id) FROM public.entity_org lo 
			INNER JOIN public.entity_relationship er 
		ON lo.entity_id = er.child_id AND er.parent_id=eo.entity_id and lo.org_type=3 and lo.deleted_flag=FALSE 
  INNER JOIN public.entity_relationship ert ON ert.parent_id = lo.entity_id 
	INNER JOIN public.entity_org tmo ON ert.child_id = tmo.entity_id AND tmo.deleted_flag=FALSE 
INNER JOIN public.team t ON tmo.org_id=t.team_id AND t.deleted_flag=false 
INNER JOIN public.team_roster tr ON tr.team_id = t.team_id 
INNER JOIN public.roster_person rp ON rp.team_roster_id = tr.team_roster_id) as player_count 

		,(SELECT COUNT(DISTINCT person_id) FROM permissions.assignment an 
 							INNER JOIN permissions.user u ON an.user_id=u.user_id AND an.org_id=asn.org_id  
 							AND an.deleted_flag='f' AND u.deleted_flag='f'  ) AS user_count 

 							
		FROM public.association asn 
		INNER JOIN public.entity_org eo ON eo.org_id = asn.org_id 
		INNER JOIN public.entity_relationship pr ON pr.child_id = eo.entity_id 
		INNER JOIN public.entity_org eop ON eop.entity_id =  pr.parent_id AND eop.org_id=? 

		LEFT JOIN public.entity_address ea ON ea.entity_id = eo.entity_id AND ea.is_active = 't'
			LEFT OUTER JOIN  public.address a                    ON ea.address_id= a.address_id   
			LEFT OUTER JOIN  public.lu_address_postal lup        ON lup.postal_id= a.address_postal 

		WHERE asn.deleted_flag=false AND eo.deleted_flag=FALSE  
		".$where."
		";
		
		
		
        return $q=$this->db->query($sql,$params)->result_array();
    }
    
    
    
    public function new_association($asn_name,$parent_entity_id,$org,$creator,$website)
    {
    	$params=func_get_args();
        return $this->db->query('SELECT public.new_association(?,?,?,?,?)', $params)->first_row()->new_association;
        
    }
    public function update_association($assn_id,$asn_name,$website,$user)
    {
    	$params=func_get_args();
        return $this->db->query('SELECT public.update_association(?,?,?,?)', $params)->first_row()->update_association;
    }
    
    public function delete_assoc($assn_id,$user) 
    {
    	$params=func_get_args();
        return $this->db->query('SELECT public.delete_assoc(?,?)', $params)->first_row()->delete_assoc;
    }
    
    
    
    
    
    
    
    public function json_gettournaments_list() 
    {
        $q=$this->db->query("select entity_id,org_name from entity_org eo inner join lu_org_type eot on eo.org_type=eot.type_id where eot.type_id=4");
        return $q->result_array();
    }
    public function json_getleagues_list() 
    {
        $q=$this->db->query("select entity_id,org_name from entity_org eo inner join lu_org_type eot on eo.org_type=eot.type_id where eot.type_id=3");
        return $q->result_array();
    }
        public function association_list()
    {//org type 2 is association
       return $this->db->query("SELECT org_name, org_id FROM public.entity_org o 
       							WHERE o.org_type = 2 
       							AND o.deleted_flag = 'f' ")->result_array();
    }
    
    
    public function json_getleagues($parent_id) 
    {
        $sql="select org_id,entity_id,org_name,org_type  
                from entity_org eo              
                inner join entity_relationship er on eo.entity_id=er.child_id         
                where er.parent_id=".$parent_id." and org_type=3";
        $q=$this->db->query($sql);
        return $q->result_array();
    }
    public function json_gettournaments($parent_id) 
    {
       $sql="select org_id,entity_id,org_name,org_type  
                from entity_org eo              
                inner join entity_relationship er on eo.entity_id=er.child_id         
                where er.parent_id =".$parent_id." and org_type=4";
        $q=$this->db->query($sql);
        return $q->result_array();
    }
    public function json_getteams($parent_league_name,$parent_tournament_name) 
    {
        
        $sql="select org_id,entity_id,org_name,org_type  
                from entity_org eo              
                inner join entity_relationship er on eo.entity_id=er.child_id         
                where (er.parent_id in (select entity_id from entity_org where org_name='".$parent_league_name."' or org_name='".$parent_tournament_name."') and org_type=6)";
                
        $q=$this->db->query($sql);
        return $q->result_array();
    }

    
    
    
    
    public function league_season_assign($season_name,$league_id,$sdate,$edate)
    {
        return $this->db->query('SELECT public.league_season_assign(?,?,?,?)', array($season_name,$league_id,date($sdate),date($edate)))->result_array();
    }
    
    
  
    

    //Manage League
    public function get_complete_league_info($league_id,$a_u_id,$a_o_id)
    {
        $result=$this->db->query('SELECT * from public.get_complete_league_info(?,?,?)', array($league_id,$a_u_id,$a_o_id))->result_array();
        return $result;
    }
    public function get_entity_addresses($entity_id)
    {
        $search_criteria=$this->input->get_post('query');

        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //-------------------------------------------------------
        $result=$this->db->query(
        "
        select * from public.get_address_by_entity(?)
        where 
        (
            lower(address_type_name) like '%'||lower(?)||'%' 
            or  lower(street) like '%'||lower(?)||'%' 
            or  lower(city) like '%'||lower(?)||'%' 
            or  lower(region_abbr) like '%'||lower(?)||'%' 
            or  lower(region_name) like '%'||lower(?)||'%' 
            or  lower(country_abbr) like '%'||lower(?)||'%' 
            or  lower(country_name) like '%'||lower(?)||'%' 
            or  lower(postal_name) like '%'||lower(?)||'%' 
        ) 
        ", array($entity_id,$search_criteria,$search_criteria,$search_criteria,$search_criteria,$search_criteria,$search_criteria,$search_criteria,$search_criteria))->result_array();
        return $result;    
    }
    public function update_entity_address($entity_id,$address_type_id,$street,$city,$region,$country,$postalcode)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //-------------------------------------------------------                                                                                                 
        return $this->db->query('SELECT * from public.update_address(?,?,?,?,?,?,?,?,?)', array(-1,$street,$city,$region,$country,$postalcode,$entity_id,$address_type_id,$a_u_id,$a_o_id))->result_array();
    }
    public function delete_entity_address($entity_id,$address_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        //-------------------------------------------------------                                                                                                 
        return $this->db->query('SELECT * from public.delete_entity_address(?,?,?,?)', array($entity_id,$address_id,$a_u_id,$a_o_id))->result_array();    
    }
    /**
    * this is intended to ignore all deleted flags
    * is used for unparking checks
    * 
    * @param mixed $league_id
    */
    public function get_league_url($league_id)
    {
		$sql="SELECT u.url from public.url u 
				INNER JOIN public.entity_org o ON o.entity_id =u.entity_id 
				INNER JOIN public.league l ON l.org_id = o.org_id 
				AND l.league_id= ?";
		$r=$this->db->query($sql,$league_id)->result_array();
		$url = ($r && count($r)) ?  $r[0]['url'] :  '';
		
		return $url;
    }
    
    public function update_league($league_id,$leaguename,$websiteprefix,$domainname)   
    {
 
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------------

        $r=$this->db->query('SELECT public.update_league(?,?,?,?,?,?)', 
        	array($league_id,$leaguename,$websiteprefix,$domainname,$a_u_id,$a_o_id))->first_row()->update_league; 
        //parking now moved to controller
 
        return $r;    
    } 
    public function update_league_name($league_id,$league_name_new,$a_u_id,$a_o_id)
    {                                                        
    	$params=func_get_args();
        return $this->db->query('SELECT * from public.update_league_name(?,?,?,?)', $params)->first_row()->update_league_name; 
         
    }
    public function delete_league($league_id,$a_u_id,$a_o_id)
    {                                                        
    	$params=func_get_args();
        $url=$this->db->query('SELECT * from public.delete_league(?,?,?)', $params)->first_row()->delete_league;             
        
        if($url!='-1') 
        {
        	$url=$this->get_league_url($league_id);
             $this->domain_unpark($url);
            return 1;   
        }        
        return  -1;
    }
 
 	/**
 	* @deprecated
 	* use person or permissions or org model
 	* 
 	* @param mixed $a_u_id
 	* @param mixed $a_o_id
 	*/
    public function get_users($a_u_id,$a_o_id)
    {
        $result=$this->db->query('SELECT * from permissions.get_users(?,?);',array($a_u_id,$a_o_id))->result_array();
        return $result;                
    }  
    /**
    * @deprecated
    * USE LEAGUES MODEL
    * 
    * @param mixed $search_criteria
    * @param mixed $active
    * @param mixed $a_u_id
    * @param mixed $a_o_id
    */
    public function get_leagues($search_criteria,$active,$a_u_id,$a_o_id)
    {                                  
   	 	//stored procedure did not work , got 'invalid syntax for integer'''       , moved it here
         
    	$sql="SELECT  league_id , league_name ,o.entity_id ,o.org_id, lut.type_name, lut.type_id 
				,url
				,public.get_address_by_entity_id_commabased(o.entity_id::INT , ? ::INT , ? ::INT) AS address
				,public.get_league_users_count(l.league_id) AS league_users_count
				,o.org_logo
        FROM league l 
				inner join public.entity_org o on o.org_id=l.org_id  AND l.deleted_flag=false
				INNER JOIN public.entity_relationship er ON o.entity_id = er.child_id 
				INNER JOIN public.entity_org op ON op.org_id=? AND op.entity_id=er.parent_id 
				INNER JOIN public.lu_league_type lut ON lut.type_id = l.league_type 
				LEFT OUTER join public.url u on u.entity_id=o.entity_id 
        WHERE   lower(league_name) like '%'||lower(?)||'%' 
		order by league_name ASC";                      
    
        //$data=$this->db->query("select * from public.get_leagues(?,?,?,?);",array($search_criteria,$active,$a_u_id,$a_o_id))->result_array();
        $data=$this->db->query($sql,array($a_u_id,$a_o_id,$a_o_id,$search_criteria))->result_array();
        
        foreach($data as $i=>$v)
        {
            if(!isset($v["league_users_count"]) ||$v["league_users_count"]==0)
                $data[$i]["league_users_count_image"]="<img src='assets/images/error.png' />";
            else $data[$i]["league_users_count_image"]=$v["league_users_count"];
        }
        return $data;            
    }
        /**
    * @deprecated
    * USE LEAGUES MODEL
    * or person model or org_model or permissions model
    * 
    * @param mixed $search_criteria
    * @param mixed $active
    * @param mixed $a_u_id
    * @param mixed $a_o_id
    */
    public function get_league_users($search_criteria,$league_id,$a_u_id,$a_o_id)
    {                                                                    
        return $this->db->query("select *,person_lname|| ', ' ||person_fname as name from permissions.get_league_users(?,?,?,?);",array($search_criteria,$league_id,$a_u_id,$a_o_id))->result_array();
    }
    public function createnewleague($leaguename,$websiteprefix,$domainname)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $sql="SELECT * from public.new_league(?,?,?,?,?,?,?,?)";
        
        $LEFT=12;
        $RIGHT=11;
        $ARBT=13;
        //modify these values to change default league locations.
        //do not use other values
        $loc_calendar=$LEFT;
        $loc_standings=$RIGHT;
        $loc_articlelist=$LEFT;
        
        
        $params  = array($leaguename,$websiteprefix,$domainname,$a_u_id,$a_o_id,$loc_calendar,$loc_standings,$loc_articlelist) ;
        $result=array();
        //returns the org_id of the new league, or a negative number on fail
        return $this->db->query($sql,$params)->first_row()->new_league; 

    } 
    
    
    /*CPANEL Functions*/
    public function domain_park($fulldomain,$subdomain='global')
    {
        $module="Park";
        $func="park";
        $args="<args><domain>{$fulldomain}</domain><topdomain>{$subdomain}</topdomain></args>";
        $result_park=$this->request_cpanel_v2($module,$func,$args);
        
        if( strstr($result_park,'was successfully parked on top of'))
        return true;
        else return false;
        //return $result_park;
    }   
    public function domain_unpark($fulldomain)
    {
        $module="Park";
        $func="unpark";
        $args="<args><domain>{$fulldomain}</domain></args>";
        $result_park=$this->request_cpanel_v2($module,$func,$args);
        
        return $result_park;
    }   
    public function create_subdomain($subdomain,$ref_domain,$rootfolder)
    {
        $module="SubDomain";
        $func="addsubdomain";
        $args="<args>{$subdomain}</args><args>{$ref_domain}</args><args>0</args><args>0</args><args>/public_html/{$rootfolder}</args>";
        $result=$this->request_cpanel_v1($module,$func,$args);
        return $result;
    }
    public function changeper($realname,$filename)
    {
        $module="Fileman";
        $func="changeperm";
        $args="<args>/home/wwwssi/public_html/global/$realname/</args>".
        "<args>$filename</args>".
        "<args>4</args>".
        "<args>2</args>".
        "<args>1</args>".
        "<args>4</args>".
        "<args>2</args>".
        "<args>1</args>".
        "<args>4</args>".
        "<args>2</args>".
        "<args>1</args>".
        "<args>1</args>"
        ;
        return $this->request_cpanel_v1($module,$func,$args);
        
    }    
                                            
    public function request_cpanel_v1($module,$func,$args)
    {
        $uname = "root";
        $pwd = "j1135#weep";
        $query = "https://endeavor1.servillianhosting.com:2087/xml-api/cpanel?user=spectrum&xmlin=".
        "<cpanelaction>".
        "<module>".$module."</module><func>".$func."</func><apiversion>1</apiversion>".$args.
        "</cpanelaction>"
        ;
        $curl = curl_init();        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);    
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);     
        curl_setopt($curl, CURLOPT_HEADER,0);            
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);    
        
        
        $header[0] = "Authorization: Basic " . base64_encode($uname.":".$pwd) . "\n\r";
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);  
        curl_setopt($curl, CURLOPT_URL, $query);            
        
        $result = curl_exec($curl);
        if ($result == false) error_log("curl_exec threw error \"" . curl_error($curl) . "\" for $query");    
        curl_close($curl);
        return $result;
    }
    public function request_cpanel_v2($module,$func,$args)
    {
        
        $uname = "root";
        $pwd = "j1135#weep";
        $query = "https://endeavor1.servillianhosting.com:2087/xml-api/cpanel?user=spectrum&xmlin=". 
        "<cpanelaction>".
        "<module>".$module."</module><func>".$func."</func>".$args.
        "</cpanelaction>"
        ;
        $curl = curl_init();        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);    
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);     
        curl_setopt($curl, CURLOPT_HEADER,0);            
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);    
        
        
        $header[0] = "Authorization: Basic " . base64_encode($uname.":".$pwd) . "\n\r";
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);  
        curl_setopt($curl, CURLOPT_URL, $query);            
        
        $result = curl_exec($curl);
        if ($result == false) error_log("curl_exec threw error \"" . curl_error($curl) . "\" for $query");    
        curl_close($curl);
        return $result;
    }
    /*CPANEL Functions*/
}
    
  
?>
