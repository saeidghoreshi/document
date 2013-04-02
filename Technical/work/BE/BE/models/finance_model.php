<?php

require_once('./endeavor/models/endeavor_model.php');

class Finance_model extends Endeavor_model
{
	
	
	/**
	* @var beanstream
	*/
	public $beanstream;
	
	/**
	* currences OWNED BY the entity
	* 
	* @param mixed $entity_id
	*/
	public function get_entity_owned_currencies($entity_id)
	{
		$sql=
        "
            SELECT type_id,type_code,currency_abbrev,type_descr,html_character,
			    icon,owner_entity_id,owner_entity_id AS entity_id 
            FROM finance.lu_currency_type 
            WHERE owner_entity_id=?
            and deleted_flag=false";
		return $this->db->query($sql,$entity_id)->result_array();
	}
	public function update_lu_currency($type,$code,$abbr,$desc,$html,$icon,$owner,$lowerLevelsEntities)
	{
		$params=func_get_args();

		$sql="SELECT finance.update_lu_currency(?,?,?,?,?,?,?,?)";
        return $this->db->query($sql,$params)->result_array();   
        
	}
	/**
	* delete currency owned by a some entity
	* @author sam
	* 
	* @param mixed $type
	*/
	public function delete_currency($type)
	{
		
		return $this->db->query("SELECT finance.delete_currency(?)",$type)->first_row()->delete_currency;
	}
	
	public function get_currencies()
	{
		$sql = "SELECT type_id,type_code, currency_abbrev, type_descr, html_character FROM finance.lu_currency_type where deleted_flag=false";
		$query = $this->db->query($sql);
		return $query->result_array();
	} 
	/**
	* currencies that this entity can use
	* @author sam
	* used by prizes and orders
	* 
	* @param mixed $entity_id
	*/
	public function get_entity_and_parent_currencies($entity_id)
	{
		$params=array($entity_id,$entity_id);
		$sql = "SELECT lu.type_id,lu.type_code, lu.currency_abbrev, lu.type_descr, lu.html_character ,lu.icon
				FROM finance.lu_currency_type lu 
                WHERE 
                lu.deleted_flag=false
                and
                lu.type_id IN 
					(SELECT ec.currency_type_id FROM finance.entity_currency ec WHERE ec.entity_id = ? ) 
           		OR lu.type_id IN 
           			(SELECT pc.currency_type_id FROM finance.entity_currency pc INNER JOIN 
		   			public.entity_relationship er ON er.parent_id = pc.entity_id  
		   					AND er.child_id = ?   )  ";//get parent currencies if inheritance is allowed
		 
		$currencies= $this->db->query($sql,$params)->result_array();
		

		$non_dup=array();
		$used=array();
		foreach($currencies as $i=>$c) 
		{
			//avoid duplicates: ie. do not show CDN twice
			if(in_array($c['type_id'],$used)) continue;
			$used[]=	$c['type_id'];
			$c['is_active']=false;
			$non_dup[]=$c;
		}
		return $non_dup;
	}
	public function get_assigned_currencies($entity_id)
	{
		$sql = "SELECT currency_type_id ,currency_type_id AS type_id FROM finance.entity_currency  
		        WHERE entity_id = ? ORDER BY type_id ASC ";  
		$query = $this->db->query($sql,$entity_id);
		return $query->result_array();
	}
	public function get_entity_accounts($org, $type=1)
	{
		$sql = "SELECT 		ea.balance, ct.type_code, ct.currency_abbrev
				FROM		finance.entity_account ea
				INNER JOIN	finance.account a 			ON ea.account_id = a.account_id AND a.category_id = ?
				INNER JOIN	finance.lu_currency_type ct	ON ct.type_id = a.currency_type_id
				INNER JOIN	public.entity e				ON ea.entity_id = e.entity_id
				INNER JOIN	public.entity_org eo		ON eo.entity_id = e.entity_id AND eo.org_id = ?
                where ct.deleted_flag=false
                ";
		$query = $this->db->query($sql, array($type,$org));
		$result = $query->result_array();
		return $result;
	}
	public function get_org_wallet_balance($org, $currency=1)
	{
		$sql = "SELECT 		ea.balance 
				FROM		finance.entity_account ea 
				INNER JOIN	finance.account a 			ON ea.account_id = a.account_id AND a.category_id = 1 
				INNER JOIN	finance.lu_currency_type ct	ON ct.type_id = a.currency_type_id AND ct.type_id = ? 
				INNER JOIN	public.entity e				ON ea.entity_id = e.entity_id  
				INNER JOIN	public.entity_org eo		ON eo.entity_id = e.entity_id AND eo.org_id = ?
                and ct.deleted_flag=false
                "; 
		$query = $this->db->query($sql, array($currency,$org));
		$result = $query->result_array();
		
		//show sero balance on account that doesn't exist
		if(count($result)==0) return 0;
		
		return $result[0]['balance'];
	}
	public function get_bankaccount($entity)
	{
		$sql = "SELECT id, name, transit, institution, account, is_enabled FROM finance.bankaccount WHERE entity_id = ?";
		$query = $this->db->query($sql, array($entity));
		return $query->first_row('array');
	}
	public function update_bankaccount($bank_id ,$name,$transit,$institution,$account,$enabled,$bankname,$user)
    {
        $params=array($bank_id ,$name,$transit,$institution,$account,$enabled,$bankname,$user);
        return  $this->db->query('select * from  finance.update_bankaccount(?,?,?,?,?,?,?,?)',$params)->first_row()->update_bankaccount;
    }
    //Signing Authority
    public function get_motions($status_type_name)//pending - approved - rejected
    {
    	$params=func_get_args();
       // $a_u_id= $this->permissions_model->get_active_user();
        $params[]= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $search='';
        $search_criteria=$this->input->get_post("query");
        if($search_criteria)
        {
			$params[]=$search_criteria;
	        $params[]=$search_criteria;
	        $search=" AND 
	            (
	                    lower(mt.type_name) like '%'||lower(?)||'%'  
	                or  lower(description) like '%'||lower(?)||'%'  
	            )";
        }
 
        $q=
        "
            select m.motion_id,m.motion_type_id
            ,mt.type_name  as motion_type_name
            ,m.motion_status_id,ms.status_name as motion_status_name
            ,m.description
            ,m.created_by
            ,p.person_lname||', '||p.person_fname        as created_by_name
            ,finance.vote_count(m.motion_id ,'y') as    num_pos_votes_received
            ,finance.vote_count(m.motion_id ,'n') as    num_neg_votes_received
            ,r.rule_value as    num_votes_required
            ,action 
            
            from finance.motion m 
            inner join finance.lu_motion_status ms  on ms.status_id=m.motion_status_id AND ms.status_name=? AND m.deleted_flag=false
            inner join finance.lu_motion_type mt    on mt.type_id=m.motion_type_id
			INNER JOIN permissions.user u           ON u.user_id=m.created_by 
            INNER JOIN public.entity_person p       ON p.person_id=u.person_id
            INNER JOIN finance.rule r               ON r.owned_by = ? AND r.isactive=true AND r.rule_type_id=1
                
                
            --WHERE ".userorg_can_view('m') ."=true 
          
          ".$search."  
          ";       
        $result=$this->db->query($q,$params)->result_array();
        return $result;
    }
    public function get_bankaccounts($owner_org_id=false)
    {
        if(!$owner_org_id) $owner_org_id=$this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $search_criteria    =$this->input->get_post("query");
        if(!$search_criteria)$search_criteria='';
        $where              ='';
        
        $motion_status_id=$this->input->get_post("motion_status_id");
        if($motion_status_id && intval($motion_status_id)!=-1)
            $where .= ' and motion_status_id = '.$motion_status_id;
            
 		
        $approvedBA='';
        if($this->input->get_post("enabled")!='f')$approvedBA='and is_enabled=true';
            
        //fixed for task 1503  sam, linked ba entity_id to active org entity id
        $q=
        "
        select ba.id as bankaccount_id,
	        name as bankaccount_name,
	        transit ,
	        institution ,
	        bankname
	        ,account
	        ,ba.entity_id
	        ,is_enabled
            ,o1.org_id,o1.org_name
            ,ba.created_by
            ,p.person_lname||', '||p.person_fname        as created_by_name
            ,ba.created_on 
            ,m.motion_status_id
            ,ms.status_name as motion_status_name
            ,m.action
            
            from finance.bankaccount ba 
            inner join public.entity_org o1                         on ba.entity_id=o1.entity_id  AND o1.org_id=? 
            INNER JOIN permissions.user u ON u.user_id=ba.created_by 
            INNER JOIN public.entity_person p ON p.person_id=u.person_id 
            inner join finance.motion_bankaccount mb    on mb.bankaccount_id=ba.id
            inner join finance.motion            m      on mb.motion_id=m.motion_id
            inner join finance.lu_motion_status  ms     on ms.status_id=m.motion_status_id
            where    ba.deleted_flag=FALSE "
            .$approvedBA  
            .$where 
            ."
            and 
            (
                    lower(name)         like '%'||lower(?)||'%'  
                or  lower(transit)      like '%'||lower(?)||'%'  
                or  lower(institution)  like '%'||lower(?)||'%'  
                or  lower(bankname)     like '%'||lower(?)||'%'  
                or  lower(account)      like '%'||lower(?)||'%'  
            )
            
            "                                                                            
        ;   
        
        $result=$this->db->query($q,array($owner_org_id,$search_criteria,$search_criteria,$search_criteria,$search_criteria,$search_criteria))->result_array();
        return $result;
    }
    
    
    public function get_withdraws()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $where          ='';
        $search_criteria='';
        if($this->input->get_post("query"))$search_criteria=$this->input->get_post("query");
        if($this->input->get_post("isEnabled"))$isEnabled=$this->input->get_post("isEnabled");
        
        $motion_status_id=$this->input->get_post("motion_status_id");
        if($motion_status_id && intval($motion_status_id)!=-1)
            $where .= ' and motion_status_id = '.$motion_status_id;
            
        $eft_status_id=$this->input->get_post("eft_status_id");
        if($eft_status_id && intval($eft_status_id)!=-1)
            $where .= ' and eft_status_id = '.$eft_status_id;
            
        $q=
        "
        select w.id as withdraw_id,w.amount,w.description,w.fees,w.total
            ,ba.id as bankaccount_id,ba.name as bankaccount_name,transit ,institution ,bankname,account,ba.entity_id,is_enabled
            ,o1.org_id,o1.org_name
            ,w.created_by
            ,split_part( (permissions.get_user_info(w.created_by)), '$!$', 3)||' '||split_part( (permissions.get_user_info(w.created_by)), '$!$', 4) as created_by_name
            ,w.created_on  
            ,ms.status_name as motion_status_name
            ,m.action
            ,m.motion_id
            ,m.motion_status_id
            ,m.motion_type_id
            ,w.eft_status_id
            ,eft.status_name 
            ,w.result_code
            ,w.result_text 
            
            from finance.withdraw w
            inner join finance.bankaccount ba                     on ba.id = w.bankaccount_id AND w.owned_by=? AND w.deleted_flag = FALSE
            inner join public.entity_org o1                         on w.owned_by=o1.org_id 
            inner join finance.motion_withdraw     mw         on mw.withdraw_id=w.id
            inner join finance.motion           m          on mw.motion_id=m.motion_id
            inner join finance.lu_motion_status ms         on ms.status_id=m.motion_status_id
            INNER JOIN finance.lu_eft_status eft ON eft.status_id = w.eft_status_id 
           -- where finance.check_user_org_role_permission(?,?,?)
            where 
            (
                    lower(w.amount::varchar)    like '%'||lower(?)||'%'  
                or  lower(w.description)        like '%'||lower(?)||'%'  
                or  lower(w.fees::varchar)      like '%'||lower(?)||'%'  
                or  lower(w.total::varchar)     like '%'||lower(?)||'%'  
                or  lower(ba.name)              like '%'||lower(?)||'%'  
                or  lower(transit)              like '%'||lower(?)||'%'  
                or  lower(institution)          like '%'||lower(?)||'%'  
                or  lower(bankname)             like '%'||lower(?)||'%'  
                or  lower(account)              like '%'||lower(?)||'%'  
            )                              
        "
        .$where;
        
        $result=$this->db->query($q,array($a_o_id,$search_criteria,$search_criteria,$search_criteria,$search_criteria,$search_criteria,$search_criteria,$search_criteria,$search_criteria,$search_criteria))->result_array();
        return $result;
    }
    
    /**
    * get withdraw data based on a single withdraw id 
    * and also all the info on that attached bank account
    * 
    * will return either null or the first row, so no need to index by [0] on return
    * 
    * @param mixed $w_id
    */
    public function get_withdraw($w_id)
    {
    	$params = func_get_args();
		$sql = "SELECT 
			w.id AS withdraw_id
			, w.bankaccount_id,
			w.amount,
			w.description,
			w.fees,
			w.total,
			w.isactive,
			w.eft_status_id
			,b.entity_id
			,b.name
			,b.bankname
			,b.institution
			,b.transit
			,b.account  
			
			FROM finance.withdraw w 
			INNER JOIN finance.bankaccount b
						ON w.bankaccount_id = b.id  AND w.deleted_flag = FALSE AND b.deleted_flag = FALSE AND b.is_enabled = true 
						 AND  w.id = ?
			INNER JOIN public.entity e ON e.entity_id = b.entity_id  
			LIMIT 1   ";
		$result = $this->db->query($sql,$params)->result_array();
		if(count($result)) $result = $result[0];
		else $result = null;
		
		return $result;
    }
    
    /**
    * get the eft item id assigned to this withdraw
    * 
    * @param mixed $w_id
    */
    public function get_withdraw_eft_item_id($w_id)
    {
		$params = func_get_args();
		$sql = "SELECT eft_item_id
				FROM finance.eft_items  
				WHERE withdraw_id = ?";
		$result = $this->db->query($sql,$params)->result_array();
		if(count($result)) $result = $result[0]['eft_item_id'];
		else $result = null;
		
		return $result;
    }
    
    
    public function get_sa_assignments($role_id=16)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id=
        //------------------------------------------------------------------
        $a_o_id             =$this->permissions_model->get_active_org();
        
	    $search_criteria    =$this->input->get_post("query");
        if(!$search_criteria)$search_criteria='';
        $where              ='';
        
        $motion_status_id=$this->input->get_post("motion_status_id");
        if($motion_status_id && intval($motion_status_id)!=-1)
            $where .= ' and motion_status_id = '.$motion_status_id;
        
        $q=
        "select 
            distinct (u.person_id) ,a.assignment_id, u.user_id, 
            p.person_fname ,
            p.person_lname ,
            person_lname||', '||person_fname as person_name,
            person_birthdate , 
            r.role_id , 
            r.role_name,
            ma.created_by,
            ma.created_on


            ,m.motion_status_id
            ,ms.status_name as motion_status_name
            ,m.action
                        
            from public.entity_person p 
            inner join permissions.user u               on p.person_id  =u.person_id
            inner join permissions.assignment a         on a.user_id    =u.user_id              AND a.org_id=? AND a.deleted_flag=false 
            inner join permissions.lu_role r            on r.role_id    =a.role_id              AND a.role_id=16
            inner join finance.motion_assignment ma     on ma.assignment_id = a.assignment_id   and ma.motion_assignment_id=(select max(motion_assignment_id)from finance.motion_assignment ma2 where ma2.owned_by=ma.owned_by and ma.assignment_id=ma2.assignment_id)
            inner join finance.motion         m         on ma.motion_id =m.motion_id            AND ma.deleted_flag=false 
            inner join finance.lu_motion_status  ms     on ms.status_id =m.motion_status_id
            
            where 
            (
                        lower(person_fname) like '%'||lower(?)||'%'  
                    or  lower(person_lname) like '%'||lower(?)||'%'  
                    or  lower(r.role_name)  like '%'||lower(?)||'%'  
            )               
 
        ".$where;
        
        $result=$this->db->query($q,array($a_o_id,$search_criteria,$search_criteria,$search_criteria))->result_array();
        return $result;
    }
    public function get_rules()
    {
    	 $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $search_criteria    =$this->input->get_post("query");
        if(!$search_criteria)$search_criteria='';
        $where              ='';
        
        $motion_status_id=$this->input->get_post("motion_status_id");
        if($motion_status_id && intval($motion_status_id)!=-1)
            $where .= ' and motion_status_id = '.$motion_status_id;
        
        $q=
        "           
        select r.rule_id,rt.type_id as rule_type_id,r.rule_value ,rt.type_name as rule_type_name , rt.type_code rule_type_code
            ,r.created_by
             , p.person_lname||', '||p.person_fname        as created_by_name 
            ,m.motion_status_id
            ,ms.status_name as motion_status_name 
            
            from finance.lu_rule_type rt 
                                                                    
            inner  join finance.rule r             on rt.type_id=r.rule_type_id and r.rule_id = (select max(rule_id)from finance.rule r2 where r2.owned_by=r.owned_by and r2.rule_type_id=r.rule_type_id)
            left join finance.motion_rule          mr  on mr.rule_id=r.rule_id
            left join finance.motion               m   on mr.motion_id=m.motion_id
            left join finance.lu_motion_status     ms  on ms.status_id=m.motion_status_id

            left join permissions.user             u   ON u.user_id=r.created_by 
            left join public.entity_person         p   ON p.person_id=u.person_id 
                
            where 
            r.owned_by=?
            AND 
            (
                        lower(rule_value)   like '%'||lower(?)||'%'  
                    or  lower(rt.type_name) like '%'||lower(?)||'%'  
                    or  lower(rt.type_code) like '%'||lower(?)||'%'  
            ) ".
            $where
            ." 
            order by m.motion_id
        ";                    
        $result=$this->db->query($q,array($a_o_id,$search_criteria,$search_criteria,$search_criteria))->result_array();
        return $result;
    }
    public function get_rules_type()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select rt.type_id as rule_type_id,rt.type_name as rule_type_name from finance.lu_rule_type rt ")->result_array();
        return $result;
    }
    public function new_motion_vote($motion_id,$vote)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        return $this->db->query("select * from finance.new_motion_vote(?,?,?,?)",array($motion_id,$vote,$a_u_id,$a_o_id))->first_row()->new_motion_vote;
         
    }
    public function new_motion_bankaccount_add($name,$transit,$institution,$account,$bankname,$description)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.new_motion_bankaccount(?,?,?,?,?,?,?,?,?,?)",array(-1,$name,$transit,$institution,$account,$bankname,'Add',$description,$a_u_id,$a_o_id))->result_array();
        return $result;
    }
    public function new_bankaccount($name,$transit,$institution,$account,$bankname,$entity_id,$enabled,$creator,$owner)
    {
        $params=array($name,$transit,$institution,$account,$bankname,$entity_id,$enabled,$creator,$owner);
        
        $sql="select  finance.new_bankaccount(?,?,?,?,?,?,?,?,?)";
        
        return  $this->db->query($sql,$params)->first_row()->new_bankaccount;
    }
    public function new_motion_bankaccount_del($bankaccount_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.new_motion_bankaccount(?,?,?,?,?,?,?,?,?,?)",array($bankaccount_id,'', '','','','','Del','',$a_u_id,$a_o_id))->result_array();
        return $result;
    }
    public function new_motion_assignment_cancel($assignment_id,$description)
    {
    	
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
    	$params=array($assignment_id,-1,-1,$a_o_id,null,'Cancel',$description,$a_u_id,$a_o_id);
        $result=$this->db->query("select * from finance.new_motion_assignment(?,?,?,?,?,?,?,?,?)",$params)->result_array();
        return $result;
    }
    public function new_motion_assignment_add($user_id,$role_id)
    {
    	
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $params=array(-1,$user_id,$role_id,$a_o_id,null,'Add','',$a_u_id,$a_o_id);
    	
        $result=$this->db->query("select * from finance.new_motion_assignment(?,?,?,?,?,?,?,?,?)",$params)->result_array();
        return $result;
    }
    public function new_motion_assignment_del($assignment_id,$description)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
         
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.new_motion_assignment(?,?,?,?,?,?,?,?,?)",
        array($assignment_id,$a_u_id,-1,-1,null,'Del',$description,$a_u_id,$a_o_id))->result_array();
        return $result;
    }
    
    /**
    * CHAGNED TO RETURN MOTION ID
    * 
    * @param mixed $bankaccount_id
    * @param mixed $amount
    * @param mixed $fees
    * @param mixed $total
    * @param mixed $entity_id
    * @param mixed $description
    */
    public function new_motion_withdraw($bankaccount_id,$amount,$fees,$total,$entity_id,$description)
    {
    	$params = func_get_args();
        $params[]= $this->permissions_model->get_active_user();
        $params[]= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        return $this->db->query("select * from finance.new_motion_withdraw(?,?,?,?,?,?,?,?)",$params)->first_row()->new_motion_withdraw;
         
    }
    public function new_motion_rule($rule_value,$rule_type_id,$description)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.new_motion_rule(?,?,?,?,?)",array($rule_value,$rule_type_id,$description,$a_u_id,$a_o_id))->result_array();
        return $result;
    }
    public function reset_sa()
    {
       $result=$this->db->query("select * from finance.\"reset_sa\"();")->result_array();
       return $result;                                    
    }
    public function get_eftfees()
    {
       $result=$this->db->query("SELECT fee_rate,fee_amount FROM  finance.lu_finance_fee where fee_code='EFT'")->result_array();
       return $result;                                        
    }
    
    public function get_motion_status()
    {
        $result=$this->db->query("SELECT status_id type_id,status_name type_name FROM  finance.lu_motion_status")->result_array();
        return $result;                                            
    }
    public function get_eft_status()
    {
        $result=$this->db->query("SELECT status_id type_id,status_name type_name FROM  finance.lu_eft_status")->result_array();
        return $result;                                                
    }
    //**********************************************************************************************************************************
    //*****************ACCOUNTING*******************************************************************************************************
    //**********************************************************************************************************************************
    
    //*****************************************************************
    //*****************MAIN********************************************
    //*****************************************************************
    public function get_transaction_history($currency_type_id,$account_type_id,$txn_num,$action_id,$fromDate,$toDate)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $q=
        "   
            select 
                trans.transaction_id
                ,ac.account_id
                ,ct.category_id                 as category_type_id
                ,ct.category_descr              as category_type_name
                ,'('||lpad(ea.entity_id::varchar, 7, '0')||'-'||ac.account_number||') '|| AccOwner.org_name||' '||ct.category_descr as account_name
                ,cur.type_id                    as currency_type_id
                ,cur.currency_abbrev            as currency_type_name
                ,ea.entity_id
                ,payment_type_id
                ,pt.type_descr                  as payment_type_name
                ,transaction_type_id
                ,tt.type_code                   as transaction_type_name
                ,trans_amount
                ,trans_descr
                ,'[' || to_char(trans_datetime,'yyyy mon dd HH24:MI:SS')||']  ' || trans_descr  as transaction_description 
                
                  /*Debitor*/
                ,finance.get_master_eid_by_txn    (trans.transaction_id)    --as invoice_master_eid

                ,case   when ((transaction_type_id=14 OR transaction_type_id=15 OR transaction_type_id=16) and ct.category_id = 5)then 'Servillian Solutions Inc.'
                        else master.name
                end     as invoice_master_ename
                                
                        
                /*Creditor*/
                ,finance.get_slave_eid_by_txn     (trans.transaction_id)     

                ,case   when ((transaction_type_id=14 OR transaction_type_id=15 OR transaction_type_id=16) and ct.category_id = 4)then 'Servillian Solutions Inc.'
                        else slave.name     
                end     as invoice_slave_ename
                
                        
                ,balance
                ,case when trans_amount< 0      then -1*trans_amount   end as credit 
                ,case when trans_amount>=0      then +1*trans_amount   end as debit 
                ,case when 
                (
                        (ct.category_id  =5 or ct.category_id  =3) and finance.cumulative_balance(ac.account_id,trans.transaction_id)< 0 
                )                      
                then -1*finance.cumulative_balance(ac.account_id,trans.transaction_id)
                else +1*finance.cumulative_balance(ac.account_id,trans.transaction_id) 
                end                             as cumulative_balance
                      
                ,gl.type_id                     as gl_type_id
                ,ta.trans_num
                
                from finance.transaction as trans
                inner join finance.account ac               on ac.account_id    =trans.account_id
                inner join finance.entity_account ea        on ea.account_id    =ac.account_id
                inner join finance.lu_category_type ct      on ct.category_id   =ac.category_id
                inner join finance.lu_gl_type gl            on gl.type_id       =ct.type_id
                inner join finance.lu_currency_type cur     on cur.type_id      =ac.currency_type_id
                inner join public.entity_org o              on o.entity_id      =ea.entity_id
                inner join finance.lu_transaction_type tt   on tt.type_id       =trans.transaction_type_id
                inner join finance.lu_payment_type pt       on pt.type_id       =trans.payment_type_id
                
                inner join public.view_entities        master    on master.entity_id    =finance.get_master_eid_by_txn    (trans.transaction_id)
                inner join public.view_entities        slave     on slave.entity_id     =finance.get_slave_eid_by_txn    (trans.transaction_id)

                inner join public.entity_org AccOwner       on AccOwner.entity_id=ea.entity_id
                
                left  join finance.transaction_action ta    on ta.action_id     =trans.transaction_action_id 
                
                where 
                    o.org_id=?  
                and ac.currency_type_id=? 
                and cur.deleted_flag=false
        ";
        if($account_type_id!='false' && $account_type_id != null) 
            $q.=" and ct.category_id={$account_type_id}";

        if($txn_num!='false' && $txn_num != null && $txn_num!='') 
            $q.=" and ta.trans_num={$txn_num}";
            
        if($action_id!='No Filter' && $action_id != null && $action_id !='') 
            $q.=" and ta.action_id='{$action_id}'";
            
        if($fromDate!=''  && $toDate!='') 
        {
            $fromDate   =date('Y-m-d',strtotime($fromDate));
            $toDate     =date('Y-m-d',strtotime($toDate));
            $q.=" and trans.trans_datetime::date >='{$fromDate}'::date and trans.trans_datetime::date <='{$toDate}'::date";
        }
            

        $q.=' order by trans.transaction_id asc';
            
        $result=$this->db->query($q,array($a_o_id,$currency_type_id))->result_array();
        return $result;
	}
    public function get_my_transaction_types($a_e_id)
    {
        $q=
        "
            select distinct action_id  as type_id , action_name as  type_name 
                from finance.transaction_action     ta
                inner join finance.transaction      t       on t.transaction_action_id =ta.action_id
                inner join finance.account          ac      on ac.account_id    =t.account_id
                inner join finance.entity_account   ea      on ea.account_id    =ac.account_id
                where entity_id=?
                order by action_id  desc
        ";    
        $result=$this->db->query($q,array($a_e_id))->result_array();
        return $result;
    }
    public function get_org_available_fund()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        $a_e_id= $this->permissions_model->get_active_entity();
        //------------------------------------------------------------------
        $q=
        "   
            select			 
                 currency_type_id
                ,currency_abbrev
                ,type_descr as currency_name
                ,balance
                ,account_number
                
                from 			finance.entity_account      ea
                inner join 		finance.account             a   on      a.account_id=ea.account_id
                inner join 		finance.lu_currency_type    ct  on 	    ct.type_id=a.currency_type_id
                where 			
                            entity_id = ? 
            	and 		category_id=1 
            	and 		is_active=true
            	and 		currency_type_id in (select distinct currency_type_id from finance.entity_currency where entity_id=?)
                and         case when(entity_id=1)then ct.is_system=1 else true end
                
                and         ct.deleted_flag=false
        ";
        $result=$this->db->query($q,array($a_e_id,$a_e_id))->result_array();
        return $result;
    }
    public function get_unpaid_deposits()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        $a_e_id= $this->permissions_model->get_active_entity();
        //------------------------------------------------------------------
        $q=
        "
            select 
                 deposit_id
                ,amount
                ,master_entity_id
                ,master.org_name as master_entity_name
                ,slave_entity_id
                ,slave.org_name as slave_entity_name
                ,charge_type_id
                ,ct.type_code   as charge_type_name
                ,currency_type_id
                ,cur.currency_abbrev as currency_type_name
                ,status_id  as deposit_status_id
                ,type_name as deposit_status_name
                            
                from finance.deposit_required depreq
                inner join public.entity_org master         on master.entity_id=master_entity_id
                inner join public.entity_org slave          on slave.entity_id=slave_entity_id
                inner join finance.lu_charge_type ct        on ct.type_id=charge_type_id 
                inner join finance.lu_currency_type cur     on cur.type_id=currency_type_id
                inner join finance.lu_deposit_status status on status.type_id=depreq.status_id
                where master_entity_id=?
                and cur.deleted_flag=false
        "; 
        $result=$this->db->query($q,array($a_e_id))->result_array();
        return $result;
    }
    public function get_income_statement($currency_type_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $q=
        "   
            select 
                trans.transaction_id
                ,ac.account_id
                ,ct.category_id as category_type_id
                ,ct.category_descr as category_type_name
                ,ac.account_descr||' ('||ac.account_number||')' as account_name
                ,cur.type_id as currency_type_id
                ,cur.currency_abbrev as currency_type_name
                ,ea.entity_id
                ,payment_type_id
                ,pt.type_descr as payment_type_name
                ,transaction_type_id
                ,tt.type_code  as transaction_type_name
                ,trans_amount,trans_descr
                ,trans_datetime
                ,trans_descr    as transaction_description
                ,i.invoice_id
                ,eo.entity_id as invoice_master_eid
                ,eo.org_name as invoice_master_ename
                ,balance
                ,case when trans_amount<0 then -1*trans_amount  end as credit 
                ,case when trans_amount>=0 then trans_amount end as debit 
                ,case when (gl.type_id=2/*Assets*/) then +1*finance.cumulative_balance(ac.account_id,trans.transaction_id) 
                      else -1*finance.cumulative_balance(ac.account_id,trans.transaction_id) end 
                      as cumulative_balance
                
                from finance.transaction as trans
                inner join finance.account ac on ac.account_id=trans.account_id
                inner join finance.entity_account ea on ea.account_id=ac.account_id
                inner join finance.lu_category_type ct on ct.category_id=ac.category_id
                inner join finance.lu_gl_type gl on gl.type_id=ct.type_id
                inner join finance.lu_currency_type cur on cur.type_id=ac.currency_type_id
                inner join public.entity e on e.entity_id=ea.entity_id
                inner join public.entity_org o on o.entity_id=e.entity_id
                inner join finance.lu_transaction_type tt on tt.type_id=trans.transaction_type_id
                inner join finance.lu_payment_type pt on pt.type_id=trans.payment_type_id
                
                left  join finance.invoice_transaction it   on it.transaction_id=trans.transaction_id
                left  join finance.invoice i on it.invoice_id=i.invoice_id
                
                left join public.entity_org eo on eo.entity_id=i.invoice_master_eid 
                where o.org_id=?  and ac.currency_type_id=?
                
                --Income statement Specific
                and (ct.category_id=4 or ct.category_id=5) 
                and trans.transaction_id=
                (
                                    select max(transaction_id) 
                                    from finance.transaction as trans2
                                    inner join finance.account ac2              on ac2.account_id=trans2.account_id    
                                    inner join finance.lu_category_type ct2     on ct2.category_id=ac2.category_id
                                    where ct2.category_id=ac.category_id
                )
                and cur.deleted_flag=false
                order by trans.transaction_id asc
        ";
        $result=$this->db->query($q,array($a_o_id,$currency_type_id))->result_array();
        return $result;
    }
    public function get_balancesheet($currency_type_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $q=
        "   
            select 
                trans.transaction_id
                ,ac.account_id
                ,ct.category_id as category_type_id
                ,ct.category_descr as category_type_name
                ,ac.account_descr||' ('||ac.account_number||')' as account_name
                ,cur.type_id as currency_type_id
                ,cur.currency_abbrev as currency_type_name
                ,ea.entity_id
                ,payment_type_id
                ,pt.type_descr as payment_type_name
                ,transaction_type_id
                ,tt.type_code   as transaction_type_name
                ,trans_amount,trans_descr
                ,trans_datetime
                ,trans_descr    as transaction_description
                ,i.invoice_id
                ,eo.entity_id as invoice_master_eid
                ,eo.org_name as invoice_master_ename
                ,balance
                ,case when trans_amount<0 then -1*trans_amount  end as credit 
                ,case when trans_amount>=0 then trans_amount end as debit 
                ,case when (gl.type_id=2/*Assets*/) then +1*finance.cumulative_balance(ac.account_id,trans.transaction_id) 
                      else -1*finance.cumulative_balance(ac.account_id,trans.transaction_id) end 
                      as cumulative_balance
                
                ,gl.order 
                from finance.transaction as trans
                inner join finance.account ac on ac.account_id=trans.account_id
                inner join finance.entity_account ea on ea.account_id=ac.account_id
                inner join finance.lu_category_type ct on ct.category_id=ac.category_id
                inner join finance.lu_gl_type gl on gl.type_id=ct.type_id
                inner join finance.lu_currency_type cur on cur.type_id=ac.currency_type_id
                inner join public.entity e on e.entity_id=ea.entity_id
                inner join public.entity_org o on o.entity_id=e.entity_id
                inner join finance.lu_transaction_type tt on tt.type_id=trans.transaction_type_id
                inner join finance.lu_payment_type pt on pt.type_id=trans.payment_type_id
                
                left  join finance.invoice_transaction it   on it.transaction_id=trans.transaction_id
                left  join finance.invoice i on it.invoice_id=i.invoice_id
                    
                left join public.entity_org eo on eo.entity_id=i.invoice_master_eid 
                where o.org_id=?  and ac.currency_type_id=?
                --balance sheet specific
                and trans.transaction_id=
                (
                                    select max(transaction_id) 
                                    from finance.transaction as trans2
                                    inner join finance.account ac2              on ac2.account_id=trans2.account_id    
                                    inner join finance.lu_category_type ct2     on ct2.category_id=ac2.category_id
                                    where ct2.category_id=ac.category_id
                )
                and cur.deleted_flag=false
                order by gl.order asc,trans.transaction_id asc
        ";
        $result=$this->db->query($q,array($a_o_id,$currency_type_id))->result_array();
        return $result;
    }
    public function get_currencytypes()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        $a_e_id= $this->permissions_model->get_active_entity();
        //------------------------------------------------------------------
        $q=
        "
            select type_id,currency_abbrev,type_descr as type_name,owner_entity_id 
                from finance.lu_currency_type ct
                inner join finance.entity_currency ec on ec.currency_type_id=ct.type_id
                where 
                    ec.entity_id    =?
                and ct.deleted_flag    =false
        ";
        $result=$this->db->query($q,array($a_e_id))->result_array();
        return $result;    
    }
    public function get_currencyowntypes()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        $a_e_id= $this->permissions_model->get_active_entity();
        //------------------------------------------------------------------
        $q=
        "
            select type_id,currency_abbrev,type_descr as type_name,owner_entity_id 
                from finance.lu_currency_type ct
                inner join finance.entity_currency ec on ec.currency_type_id=ct.type_id
                where 
                    ec.entity_id    =?
                and owner_entity_id =?
                and ct.deleted_flag    =false
        ";
        $result=$this->db->query($q,array($a_e_id,$a_e_id))->result_array();
        return $result;    
    }
    public function get_account_types()
    {
        //list all accounts with non-zero balace for lising
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        $a_e_id= $this->permissions_model->get_active_entity();
        //------------------------------------------------------------------
        $q=
        "
            select ct.category_id type_id,category_descr as type_name
            from finance.entity_account         ea
            inner join finance.account          a   on a.account_id=ea.account_id
            inner join finance.lu_category_type ct  on a.category_id=ct.category_id
            where 
            balance != 0
            and ea.entity_id=? 
        ";
        $result=$this->db->query($q,array($a_e_id))->result_array();
        return $result;    
    }
    public function get_charge_types()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.get_charge_types(?,?)",array($a_u_id,$a_o_id))->result_array();
        return $result;    
    }
    public function new_charge_item($charge_code,$charge_descr)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.new_charge_item(?,?,?,?)",array($charge_code,$charge_descr,$a_u_id,$a_o_id))->result_array();
        return $result;        
    }
    public function getHierarchicalEntities($org_type_id,$target_org_type_id)
    {
        $a_u_id             = $this->permissions_model->get_active_user();
        $a_o_id             = $this->permissions_model->get_active_org();
        $org_type_id        = $this->permissions_model->get_active_org_type();
        //------------------------------------------------------------------
        $search_criteria='';
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];
                   

        //******************************************     //Parent is a SSI
        if(intval($org_type_id)==1)
        {
            if(intval($target_org_type_id)==1)          //Target Associations
            $q=
            "
                select 
                    'To Me' as ParentOrg ,
                    level1.entity_id,
                    level1.org_name AS entity_name,
                    '' as custom_empty
                    
                    from  public.entity_org                   level1            
                    
                          
                    where 
                        
                    level1.org_id           ={$a_o_id}
                    and 
                    (
                        lower(level1.org_name) like '%'||lower(?)||'%' 
                    ) 
                    
                    order   by  level1.entity_id 
            ";      
            if(intval($target_org_type_id)==2)  //Target Associations
            $q=
            "
                select 
                    level1.org_name as ParentOrg ,
                    level2.entity_id,
                    level2.org_name AS entity_name,
                    '' as custom_empty
                    
                    from  public.entity_org                   level1            
                    
                    inner join public.entity_relationship     level1ER      on level1.entity_id     =level1ER.parent_id
                    inner join public.entity_org              level2        on level2.entity_id     =level1ER.child_id
                          
                    where 
                            level1.org_type         =1      --SSI
                    and     level2.org_type         =2      --Target Assoc
                    and     level2.deleted_flag     =false
                    and     level1.org_id           ={$a_o_id}
                    and 
                    (
                        lower(level2.org_name) like '%'||lower(?)||'%' 
                    ) 
                    
                    order   by  level1.entity_id 
            ";      
            if(intval($target_org_type_id)==3)  //target Leagues
            $q=
            "
                select 
                    level1.org_name||' &raquo; '||level2.org_name as ParentOrg ,
                    level3.entity_id,
                    level3.org_name AS entity_name,
                    '' as custom_empty
                    
                    from  public.entity_org                   level1            
                    
                    inner join public.entity_relationship     level1ER      on level1.entity_id     =level1ER.parent_id
                    inner join public.entity_org              level2        on level2.entity_id     =level1ER.child_id

                    inner join public.entity_relationship     level2ER      on level2.entity_id     =level2ER.parent_id 
                    inner join public.entity_org              level3        on level3.entity_id     =level2ER.child_id
                          
                    where 
                            level1.org_type         =1      --SSI
                    and     level3.org_type         =3      --Target League
                    and     level3.deleted_flag     =false
                    and     level1.org_id           ={$a_o_id}
                    and 
                    (
                        lower(level3.org_name) like '%'||lower(?)||'%' 
                    ) 
                    
                    order   by  level1.entity_id ,level2.entity_id 
            ";      
            if(intval($target_org_type_id)==4)  //target Tournament
            $q=
            "
                select 
                    level1.org_name||' &raquo; '||level2.org_name as ParentOrg ,
                    level3.entity_id,
                    level3.org_name AS entity_name,
                    '' as custom_empty
                    
                    from  public.entity_org                   level1            
                    
                    inner join public.entity_relationship     level1ER      on level1.entity_id     =level1ER.parent_id
                    inner join public.entity_org              level2        on level2.entity_id     =level1ER.child_id

                    inner join public.entity_relationship     level2ER      on level2.entity_id     =level2ER.parent_id 
                    inner join public.entity_org              level3        on level3.entity_id     =level2ER.child_id
                          
                    where 
                            level1.org_type         =1      --SSI
                    and     level3.org_type         =4      --Target Tournament
                    and     level3.deleted_flag     =false
                    and     level1.org_id           ={$a_o_id}
                    and 
                    (
                        lower(level3.org_name) like '%'||lower(?)||'%' 
                    ) 
                    
                    order   by  level1.entity_id ,level2.entity_id 
            ";      
            if(intval($target_org_type_id)==6)  //Target Teams
            $q=
            "
                select 
                    level1.org_name||' &raquo; '||level2.org_name||' &raquo; '||level3.org_name    as ParentOrg ,
                    level4.entity_id,
                    level4.org_name AS entity_name,
                    '' as custom_empty
                    
                    from  public.entity_org                   level1            
                    
                    inner join public.entity_relationship     level1ER      on level1.entity_id     =level1ER.parent_id
                    inner join public.entity_org              level2        on level2.entity_id     =level1ER.child_id

                    inner join public.entity_relationship     level2ER      on level2.entity_id     =level2ER.parent_id 
                    inner join public.entity_org              level3        on level3.entity_id     =level2ER.child_id

                    inner join public.entity_relationship     level3ER      on level3.entity_id     =level3ER.parent_id 
                    inner join public.entity_org              level4        on level4.entity_id     =level3ER.child_id
                    
                    inner join public.team                    t             on t.org_id=level4.org_id

                    where 
                            level1.org_type         =1      --SSI
                    and     level4.org_type         =6      --Target Team
                    and     level4.deleted_flag     =false
                    and     t.team_status_id   =1
                    and     level1.org_id           ={$a_o_id}
                    
                    and 
                    (
                        lower(level4.org_name) like '%'||lower(?)||'%' 
                    ) 
                    order   by  level1.entity_id ,level2.entity_id ,level3.entity_id 
            ";                                                
            $result=$this->db->query($q,array($search_criteria))->result_array();
            return $result;            
        }                          
        //******************************************     //Parent is a Association
        if(intval($org_type_id)==2)
        {
            if(intval($target_org_type_id)==2)           //Target Leagues
            $q=
            "
                select 
                    'To ME' as ParentOrg ,
                    level1.entity_id,
                    level1.org_name AS entity_name,
                    '' as custom_empty
                    
                    from  public.entity_org                   level1            
                           
                    where 
                            level1.org_type         =2      --Association
                    and     level1.org_id           ={$a_o_id}
                    and 
                    (
                        lower(level1.org_name) like '%'||lower(?)||'%' 
                    ) 
                    
                    order   by  level1.entity_id 
            ";      
            if(intval($target_org_type_id)==3)           //Target Leagues
            $q=
            "
                select 
                    level1.org_name as ParentOrg ,
                    level2.entity_id,
                    level2.org_name AS entity_name,
                    '' as custom_empty
                    
                    from  public.entity_org                   level1            
                    
                    inner join public.entity_relationship     level1ER      on level1.entity_id     =level1ER.parent_id
                    inner join public.entity_org              level2        on level2.entity_id     =level1ER.child_id
                          
                    where 
                            level1.org_type         =2      --Association
                    and     level2.org_type         =3      --Target League
                    and     level2.deleted_flag     =false
                    and     level1.org_id           ={$a_o_id}
                    and 
                    (
                        lower(level2.org_name) like '%'||lower(?)||'%' 
                    ) 
                    
                    order   by  level1.entity_id 
            ";      
            if(intval($target_org_type_id)==4)           //target Tournaments
            $q=
            "
                select 
                    level1.org_name||' &raquo; '||level2.org_name as ParentOrg ,
                    level3.entity_id,
                    level3.org_name AS entity_name,
                    '' as custom_empty
                    
                    from  public.entity_org                   level1            
                    
                    inner join public.entity_relationship     level1ER      on level1.entity_id     =level1ER.parent_id
                    inner join public.entity_org              level2        on level2.entity_id     =level1ER.child_id

                    inner join public.entity_relationship     level2ER      on level2.entity_id     =level2ER.parent_id 
                    inner join public.entity_org              level3        on level3.entity_id     =level2ER.child_id
                          
                    where 
                            level1.org_type         =2      --Association
                    and     level3.org_type         =4      --Target Tournaments
                    and     level3.deleted_flag     =false
                    and     level1.org_id           ={$a_o_id}
                    and 
                    (
                        lower(level3.org_name) like '%'||lower(?)||'%' 
                    ) 
                    
                    order   by  level1.entity_id ,level2.entity_id 
            ";      
            if(intval($target_org_type_id)==6)              //target Team
            $q=
            "
                select 
                    level1.org_name||' &raquo; '||level2.org_name as ParentOrg ,
                    level3.entity_id,
                    level3.org_name AS entity_name,
                    '' as custom_empty
                    
                    from  public.entity_org                   level1            
                    
                    inner join public.entity_relationship     level1ER      on level1.entity_id     =level1ER.parent_id
                    inner join public.entity_org              level2        on level2.entity_id     =level1ER.child_id

                    inner join public.entity_relationship     level2ER      on level2.entity_id     =level2ER.parent_id 
                    inner join public.entity_org              level3        on level3.entity_id     =level2ER.child_id
                    
                    inner join public.team                    t             on t.org_id=level3.org_id
                          
                    where 
                            level1.org_type         =2      --Association
                    and     level3.org_type         =6      --Target Team
                    and     level3.deleted_flag     =false
                    and     t.team_status_id        =1
                    and     level1.org_id           ={$a_o_id}
                   
                    and 
                    (
                        lower(level3.org_name) like '%'||lower(?)||'%' 
                    ) 
                    order   by  level1.entity_id ,level2.entity_id 
            ";      
            $result=$this->db->query($q,array($search_criteria))->result_array();
            return $result;            
        }                          
        //******************************************     //Parent is a League
        if(intval($org_type_id)==3)
        {
            if(intval($target_org_type_id)==3)          //Target Tournament
            $q=
            "
                select 
                    'To Me' as ParentOrg ,
                    level1.entity_id,
                    level1.org_name AS entity_name,
                    '' as custom_empty
                    
                    from  public.entity_org                   level1            
                          
                    where 
                            level1.org_type         =3      --League
                    and     level1.org_id           ={$a_o_id}
                    and 
                    (
                        lower(level1.org_name) like '%'||lower(?)||'%' 
                    ) 
                    
                    order   by  level1.entity_id 
            ";      
            if(intval($target_org_type_id)==4)          //Target Tournament
            $q=
            "
                select 
                    level1.org_name as ParentOrg ,
                    level2.entity_id,
                    level2.org_name AS entity_name,
                    '' as custom_empty
                    
                    from  public.entity_org                   level1            
                    
                    inner join public.entity_relationship     level1ER      on level1.entity_id     =level1ER.parent_id
                    inner join public.entity_org              level2        on level2.entity_id     =level1ER.child_id
                          
                    where 
                            level1.org_type         =3      --League
                    and     level2.org_type         =4      --Target Tournament
                    and     level2.deleted_flag     =false
                    and     level1.org_id           ={$a_o_id}
                    and 
                    (
                        lower(level2.org_name) like '%'||lower(?)||'%' 
                    ) 
                    
                    order   by  level1.entity_id 
            ";      
            if(intval($target_org_type_id)==6)          //target Team
            $q=
            "
                select 
                    level1.org_name as ParentOrg ,
                    level2.entity_id,
                    level2.org_name AS entity_name,
                    '' as custom_empty
                    
                    from  public.entity_org                   level1            
                    
                    inner join public.entity_relationship     level1ER      on level1.entity_id     =level1ER.parent_id
                    inner join public.entity_org              level2        on level2.entity_id     =level1ER.child_id
                    
                    inner join public.team                    t             on t.org_id=level2.org_id
                    inner join public.team_season             ts            on ts.team_id=t.team_id
                          
                    where 
                            level1.org_type         =3      --League
                    and     level2.org_type         =6      --Target Team
                    and     level2.deleted_flag     =false
                    and     t.team_status_id        =1
                    and     level1.org_id           ={$a_o_id}
                    
                    and 
                    (
                        lower(level2.org_name) like '%'||lower(?)||'%' 
                    ) 
                     
                    order   by  level1.entity_id 
            ";      
                                    
            $result=$this->db->query($q,array($search_criteria))->result_array();
            return $result;            
        }                          
    }                                                       
    public function delete_charge_type($charge_type_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"delete_charge_type\"(?)",array($charge_type_id))->result_array();
        return $result;                        
    }
    public function getOrgAddresses($address_type_id)
    {
        $a_e_id= $this->permissions_model->get_active_entity();
        $q=
        "
            select  
            a.address_id,
            ea.address_type,
            AT.type_name,
            a.address_street,
            a.address_city, 
            r.region_abbr,
            r.region_name,
            c.country_id,
            c.country_abbr,
            c.country_name,
            p.postal_id,
            p.postal_value
            , 
            case when a.address_street is null then '' else a.address_street    end ||', '
            ||case when a.address_city is null then '' else a.address_city      end ||', '
            ||case when r.region_abbr  is null then '' else r.region_abbr       end ||', '
            ||case when c.country_name is null then '' else c.country_name      end ||', '
            ||case when p.postal_value is null then '' else p.postal_value      end  as address_name
            ,a.modified_on

            from public.address as a
            left join public.lu_address_region  r   on r.region_id  =a.address_region
            left join public.lu_address_country c   on c.country_id =a.address_country
            left join public.lu_address_postal  p   on p.postal_id  =a.address_postal
            
            inner   join public.entity_address  ea  on ea.address_id=a.address_id
            left    join public.lu_address_type AT  on at.type_id   =ea.address_type

            where 
            ea.entity_id=?
            and is_active=true
            and address_type=?
        ";
        $result =$this->db->query($q,array($a_e_id,$address_type_id))->result_array();
        return  $result;                        
    }
    public function getOrgEmails()
    {
        $a_o_id = $this->permissions_model->get_active_org();
        $q=
        "
            select value as email_name
            from 
            public.entity_contact ec
            inner join public.contact_method cm     on cm.contact_method_id     =ec.contact_method_id
            inner join public.lu_contact_type ct    on ct.type_id               =ec.contact_type
            where ec.entity_id in
            (
                select ep.entity_id
                from 
                public.entity_org o1 
                inner join permissions.assignment   a     on a.org_id       =o1.org_id
                inner join permissions.lu_role      r     on r.role_id      =a.role_id
                inner join permissions.user         u     on u.user_id      =a.user_id
                inner join public.entity_person     ep    on ep.person_id   =u.person_id

                where o1.org_id=? and (r.role_id=5 or r.role_name like '%'||'Manager'||'%')

            ) 
            and (ec.contact_type=1 or ec.contact_type=5)
            and value is not null
        ";
        $result =   $this->db->query($q,array($a_o_id))->result_array();
        return  $result;                        
    }
    public function reset_transactions()
    {
        $result=$this->db->query("select * from finance.\"reset_transactions\"();")->result_array();
        return $result;                                    
    }
    public function setup_entities_payment_plan($master_entity_id,$slave_entity_id,$charge_type_id,$amount,$currency_type_id)
    {
        $result=$this->db->query("select * from finance.setup_entities_payment_plan(?,?,?,?,?);",array($master_entity_id,$slave_entity_id,$charge_type_id,$amount,$currency_type_id))->result_array();
        return $result;                                        
    }
    public function get_user_addresses()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //----------------------------------------------------------------
        $result=$this->db->query("select * from public.\"get_address_by_user_id_commabased\"(?,?,?);",array($a_u_id,$a_u_id,$a_o_id))->result_array();
        return $result;                                
    }
    public function get_deposit_balance($master_entity_id ,$slave_entity_id ,$currency_type_id )
    {                                                                                
        $result=$this->db->query("select * from finance.\"get_deposit_balance\"(?,?,?)",array($master_entity_id ,$slave_entity_id ,$currency_type_id ))->result_array();
        return $result;                    
    }
    public function getEntityTaxes()
    {                               
        $a_e_id= $this->permissions_model->get_active_entity();
        $q=
        "
            select      t.rate,t.label,t.label_descr
            from finance.lu_taxrate   t
             
            where t.region_id =
            (
                select distinct ar.region_id 
                from public.entity_address              ea
                inner join  public.entity_org           eo  on eo.entity_id     =ea.entity_id
                inner join  public.address              a   on a.address_id     =ea.address_id
                inner join  public.lu_address_region    ar  on ar.region_id     =a.address_region
                where 
                    address_type in (1,2,3)
                and ea.entity_id   =?
                and address_region      is not null
                and is_active   =true
                --order by address_type asc
                limit 1
            )
            and (current_timestamp >= t.effective_range_start or t.effective_range_start is null )
            and t.isactive=true
        ";
        $result=$this->db->query($q,array($a_e_id))->result_array();
        return $result;
    }
    public function remainingTimeToEOD($payment_on)    //usage for CC and DB
    {
        $result=$this->db->query('select * from finance."remainingTimeToEOD"(?);',array($payment_on))->result_array();
        $cancel_type=$result[0]["remainingTimeToEOD"];
        //CALCULATE CANCELLATION TYPE BASED ON ONLINE SYSTEM REMAINING TIME CHECKING   USAGE ((((CHASE_CREDIT_CARD  AND INTERNAL))))
        if(intval($cancel_type)==2)
            $cancel_type="void";
        else
            $cancel_type="refund";
        //STILL MIGHT NOT BE ACCURATE BECAUSE OF TIME FRAME >> EXTERNAL CANCELLATION NEED TO BE HANDLED
        return $cancel_type;
    }
    public function getAllLowerLevelEntities()
    {
        $q=
        "
            select org_type,entity_id,org_name,er.parent_id as parent_entity_id
            from public.entity_org ceo
            inner join public.entity_relationship er on er.child_id=ceo.entity_id
                        
            where deleted_flag  =false
            and is_valid        =true
            order by org_type,entity_id
            
        ";    
        $result=$this->db->query($q)->result_array();   
        return $result;
    }
    //Batch Closing Run by CRON    [MUST BE DEPRECIATERD]
    public function handle_payments_eod()
    {
        $hourDiff=3;  
        $AM2        =date("Y-m-d H:i:s",strtotime((string)date('Y-m-d'))+2*60*60+$hourDiff*60*60);
        $AM2Nextday =date("Y-m-d H:i:s",strtotime((string)date('Y-m-d'))+2*60*60+24*60*60+$hourDiff*60*60);
           
        //fetch all payments with eod = false then decide which one need to be closed
        $result=$this->db->query("select payment_id,payment_type_id,created_on from finance.payment where eod=false")->result_array();
        
        $action='';
        $selected_payments='';
        
        foreach($result as $v)
        {
            $payment_datetime=$v["created_on"];
            if($payment_datetime<$AM2 && date('Y-m-d H:i:s')<$AM2)
                $action="void";
            if($payment_datetime>=$AM2  && $payment_datetime<$AM2Nextday  && date('Y-m-d H:i:s')>=$AM2 && date('Y-m-d H:i:s')<$AM2Nextday)
                $action="void";
            $action="refund";
                
            $selected_payments=$v["payment_id"].',';
        }
        $selected_payments=substr($selected_payments,0,strlen($selected_payments)-1);
        
        $result=$this->db->query("select * from finance.batch_close_payments(?);",array($selected_payments));
    }   
    
    //*****************************************************************
    //*****************INVOICE*****************************************
    //*****************************************************************
    
    public function get_invoices($invoice_type_id,$fromDate,$toDate,$dateTypeList)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $search_criteria='';
        if($this->input->get_post("query"))$search_criteria=$this->input->get_post("query");
        //------------------------------------------------------------------
        $q=
            "
                select  I.* , case when master.org_logo is null then '../../../assets/images/spectrum.png' else master.org_logo end as master_logo
                        ,ct.is_system as currency_is_system
                from finance.get_invoices(?,?,?,?,?,?) I
                inner join finance.lu_currency_type ct  on ct.type_id=I.currency_type_id
                inner join public.entity_org master     on master.org_id = I.invoice_master_oid
                where ct.deleted_flag=false
        ";
        $result=$this->db->query($q,array($invoice_type_id,(string)$fromDate,(string)$toDate,(string)$dateTypeList,$search_criteria,$a_o_id))->result_array();
            
        return $result;
    }
    public function get_pending_invoices()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $search_criteria='';
        if($this->input->get_post("query"))$search_criteria=$this->input->get_post("query");
        //------------------------------------------------------------------
        $q=
        "
        select 
            invoice_id
            ,o1.org_id as invoice_master_oid
            ,invoice_master_eid
            ,o1.org_name as invoice_master_ename
            ,o2.org_id as invoice_slave_oid
            ,invoice_slave_eid
            ,o2.org_name as invoice_slave_ename
            ,custom_invoice_number
            ,invoice_description
            ,invoice_amount
            ,case when (invoice_slave_eid is not  null)then invoice_paid  else null end as invoice_paid
            ,case when (invoice_slave_eid is not  null)then invoice_owing else null end as invoice_owing
            ,date_issued
            ,date_due
            ,invoice_status_id
            ,case 
                when (o1.org_id=?  and invoice_status_id=4) then 'Receiveable'
                when (o2.org_id=?  and invoice_status_id=4) then 'Payable'         
                else status_name
                end
                as invoice_status_name
            ,currency_type_id
            ,currency_abbrev as currency_type_name
            ,invoice_number
            ,i.created_by,split_part( (permissions.get_user_info(i.created_by)), '$!$', 3)||', '||split_part( (permissions.get_user_info(i.created_by)), '$!$', 4) as created_by_name,i.created_on
            ,i.created_on

            from finance.invoice i
            left    join public.entity_org o1                   on o1.entity_id=invoice_master_eid
            left    join public.entity_org o2                   on o2.entity_id=invoice_slave_eid
            inner   join finance.lu_invoice_status s            on s.status_id=i.invoice_status_id
            inner   join finance.lu_currency_type cur           on cur.type_id=i.currency_type_id                    
            where   true and i.deleted_flag=false 
            and     o1.org_id=?  and invoice_status_id=3--Pending 
            and 
            (
                    lower(custom_invoice_number::varchar) like '%'||lower(?)||'%' 
                or  lower(invoice_description) like '%'||lower(?)||'%' 
                or  lower(invoice_amount::varchar) like '%'||lower(?)||'%' 
                or  lower(invoice_paid::varchar) like '%'||lower(?)||'%' 
                or  lower(invoice_owing::varchar) like '%'||lower(?)||'%' 
                or  lower(date_issued::varchar) like '%'||lower(?)||'%' 
                or  lower(date_due::varchar) like '%'||lower(?)||'%' 
            )
            and cur.deleted_flag=false
        ";
        
        $result=$this->db->query($q,
            array(
                $a_o_id,
                $a_o_id,
                $a_o_id,
                $search_criteria,
                $search_criteria,
                $search_criteria,
                $search_criteria,
                $search_criteria,
                $search_criteria,
                $search_criteria
                ))->result_array();
        return $result;
    }
    public function get_draft_invoices()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        $a_e_id= $this->permissions_model->get_active_entity();
        //------------------------------------------------------------------
        $search_criteria='';
        if($this->input->get_post("query"))$search_criteria=$this->input->get_post("query");
        //------------------------------------------------------------------
        $q=
        "
        select 
            invoice_id
            ,o1.org_id as invoice_master_oid
            ,invoice_master_eid
            ,o1.org_name as invoice_master_ename
            /*,'----' as invoice_slave_oid
            ,'----' as invoice_slave_eid
            ,'----' as invoice_slave_ename
            ,'----' as custom_invoice_number
            */
            ,invoice_description
            ,invoice_amount
            /*,'----' as invoice_paid
            ,'----' as invoice_owing
            */
            ,date_issued
            ,date_due
            ,invoice_status_id
            ,'Draft' as invoice_status_name
            ,currency_type_id
            ,currency_abbrev as currency_type_name
            --,'----' as invoice_number
            ,i.created_by,split_part( (permissions.get_user_info(i.created_by)), '$!$', 3)||', '||split_part( (permissions.get_user_info(i.created_by)), '$!$', 4) as created_by_name,i.created_on
            ,i.created_on

            from finance.invoice_draft i
            left    join public.entity_org o1                   on o1.entity_id=invoice_master_eid
            inner   join finance.lu_invoice_status s            on s.status_id=i.invoice_status_id
            inner   join finance.lu_currency_type cur           on cur.type_id=i.currency_type_id                    
            where   true and i.deleted_flag=false  and s.status_id=1  
            and     invoice_master_eid=?
            and 
            (
                    lower(invoice_description) like '%'||lower(?)||'%' 
                or  lower(invoice_amount::varchar) like '%'||lower(?)||'%' 
                or  lower(date_issued::varchar) like '%'||lower(?)||'%' 
                or  lower(date_due::varchar) like '%'||lower(?)||'%' 
            )
            and cur.deleted_flag=false
        ";
        $result=$this->db->query($q,array(
        
        $a_e_id,
        $search_criteria,
        $search_criteria,
        $search_criteria,
        $search_criteria
        
        ))->result_array();
        return $result;
    }
    public function generate_invoice_draft($currency_type_id,$description,$title)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"generate_invoice_draft\"(?,?,?,?,?,?)",array($a_o_id, $description ,  $currency_type_id ,$title , $a_u_id , $a_o_id ))->result_array();
        return $result;        
    }
    public function update_invoice_draft($invoice_id,$currency_type_id,$description,$title)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"update_invoice_draft\"(?,?,?,?,?,?)",array($invoice_id , $description , $currency_type_id , $title , $a_u_id , $a_o_id ))->result_array();
        return $result;        
    }
    public function get_invoice_items($invoice_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $search_criteria='';
        if(isset($_REQUEST["query"]))$search_criteria=$_REQUEST["query"];

        $q=
        "
            select 
            invoice_item_id
            ,invoice_id
            ,charge_type_id
            ,ct.type_descr as charge_type_name
            ,invoice_item_description
            ,charge_price
            ,charge_cost
            ,quantity
            ,ii.isactive 
            ,ii.created_by
            ,split_part( (permissions.get_user_info(ii.created_by)), '$!$', 3)||', '||split_part( (permissions.get_user_info(ii.created_by)), '$!$', 4) as created_by_name
            ,ii.created_on
            ,charge_price * quantity as sub_amount
            
            from finance.invoice_item ii
            inner join finance.lu_charge_type ct on ct.type_id=ii.charge_type_id
            where ii.deleted_flag=false 
            and invoice_id=?    and charge_type_id!=1
            and 
            (
                lower(quantity::varchar) like '%'||lower(?)||'%' 
                or  lower(invoice_item_description::varchar) like '%'||lower(?)||'%' 
                or  lower(charge_price::varchar) like '%'||lower(?)||'%' 
                or  lower(charge_cost::varchar) like '%'||lower(?)||'%' 
            )
        ";
        $result=$this->db->query($q,array($invoice_id,$search_criteria,$search_criteria,$search_criteria,$search_criteria))->result_array();
        return $result;            
    }
    public function get_invoice_tax_details($invoice_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $q=
        "
            select invoice_item_description,it.amt as amt,tr.label,tr.label_descr,tr.rate
            from 
            finance.invoice_item            ii
            inner join finance.invoice      i   on i.invoice_id     =ii.invoice_id
            inner join finance.invoice_tax  it  on it.invoice_id    =i.invoice_id
            inner join finance.lu_taxrate   tr  on tr.id            =it.taxrate_id
                        
            where i.invoice_id=?
            and charge_type_id=1   
        ";
        $result=$this->db->query($q,array($invoice_id))->result_array();
        return $result;            
    }
    public function get_invoice_items_sum_noTax($invoice_id)
    {
        $q=
        "
            select sum(charge_price) 
            from finance.invoice_item   ii
            where ii.invoice_id=? and charge_type_id != 1
        ";
        $result=$this->db->query($q,array($invoice_id))->result_array();
        return $result;                
    }
    public function get_invoice_items_draft($invoice_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $q=
        "
            select 
            invoice_item_id
            ,invoice_id
            ,charge_type_id
            ,ct.type_descr as charge_type_name
            ,invoice_item_description
            ,charge_price
            ,charge_cost
            ,quantity
            ,ii.isactive 
            ,ii.created_by
            ,split_part( (permissions.get_user_info(ii.created_by)), '$!$', 3)||', '||split_part( (permissions.get_user_info(ii.created_by)), '$!$', 4) as created_by_name
            ,ii.created_on
            ,charge_price * quantity as sub_amount
            
            from finance.invoice_item_draft ii
            inner join finance.lu_charge_type ct on ct.type_id=ii.charge_type_id
            where 
            ii.deleted_flag=false 
            and invoice_id=?      
            and charge_type_id!=1
        ";
        $result=$this->db->query($q,array($invoice_id))->result_array();
        return $result;            
    }
    public function add_invoice_item($invoice_id,$charge_type_id,$invoice_item_description,$charge_price,$charge_cost,$quantity,$tax_applies)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"add_invoice_item\"(?,?,?,?,?,?,?,?,?,?)",array($invoice_id,$charge_type_id,$invoice_item_description,$charge_price,$charge_cost,$quantity,'true',$tax_applies, $a_u_id , $a_o_id ))->result_array();
        return $result;
    }
    public function delete_invoice_item($invoice_item_id,$tax_applies)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"delete_invoice_item\"(?,?,?,?)",array($invoice_item_id,$tax_applies, $a_u_id , $a_o_id ))->result_array();
        return $result;
    }
    public function add_invoice_item_draft($invoice_id,$charge_type_id,$invoice_item_description,$charge_price,$charge_cost,$quantity,$tax_applies)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"add_invoice_item_draft\"(?,?,?,?,?,?,?,?,?,?)",array($invoice_id,$charge_type_id,$invoice_item_description,$charge_price,$charge_cost,$quantity,'true',$tax_applies, $a_u_id , $a_o_id ))->result_array();
        return $result;
    }
    public function delete_invoice_item_draft($invoice_item_id,$tax_applies)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"delete_invoice_item_draft\"(?,?,?,?)",array($invoice_item_id,$tax_applies, $a_u_id , $a_o_id ))->result_array();
        return $result;
    }
    public function close_invoice($invoice_id,$recipient_eids,$recipient_custom_nums ,$description,$due_date,$just_save,$keep_draft_cb,$tax_applies)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $closeInvoicesResult=$this->db->query("select * from finance.\"close_invoices\"(?,?,?,?,?,?,?,?,?,?)",array($invoice_id,$due_date,$recipient_eids,$recipient_custom_nums ,$description,$just_save,$keep_draft_cb,$tax_applies, $a_u_id , $a_o_id ))->result_array();
        //GET LIST OF NEWLY-CREATED INVOICE IDS AND GENERATE INVOICE NOTIFICATION
        $this->EmailNotificationInvoice($closeInvoicesResult,$recipient_eids);
        
        foreach($closeInvoicesResult as $v)
            if(intval($v["close_invoices"])==-2)
                return array(array("close_invoices"=>-2));
        return array(array("close_invoices"=>1));
    }  
    public function delete_invoice($invoice_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"delete_invoice\"(?,?,?)",array($invoice_id, $a_u_id , $a_o_id ))->result_array();
        return $result;                
    }
    public function delete_draft_invoice($invoice_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"delete_draft_invoice\"(?,?,?)",array($invoice_id, $a_u_id , $a_o_id ))->result_array();
        return $result;                
    }
    public function apply_tax_invoice($invoice_id,$tax_applies)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"apply_tax_invoice\"(?,?,?,?)",array($invoice_id,$tax_applies, $a_u_id , $a_o_id ))->result_array();
        return $result;                    
    }
    public function apply_tax_invoice_draft($invoice_id,$tax_applies)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"apply_tax_invoice_draft\"(?,?,?,?)",array($invoice_id,$tax_applies, $a_u_id , $a_o_id ))->result_array();
        return $result;                    
    }                                                                                                     
    public function get_applied_tax_status_invoice($invoice_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"get_applied_tax_status_invoice\"(?)",array($invoice_id))->result_array();
        return $result;                        
    }
    public function get_applied_tax_status_invoice_draft($invoice_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"get_applied_tax_status_invoice_draft\"(?)",array($invoice_id))->result_array();
        return $result;                        
    }
    public function update_invoice_item($invoice_item_id,$charge_price,$charge_cost,$quantity)
    {
        $result=$this->db->query("select * from finance.\"update_invoice_item\"(?,?,?,?)",array($invoice_item_id,$charge_price,$charge_cost,$quantity))->result_array();
        return $result;                        
    }
    public function update_draftinvoice_item($invoice_item_id,$charge_price,$charge_cost,$quantity)
    {
        $result=$this->db->query("select * from finance.\"update_draftinvoice_item\"(?,?,?,?)",array($invoice_item_id,$charge_price,$charge_cost,$quantity))->result_array();
        return $result;                        
    }
    public function change_invoice_status($invoice_id,$invoice_status_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result         =$this->db->query("select * from finance.\"change_invoice_status\"(?,?,?,?)",array($invoice_id,$invoice_status_id, $a_u_id , $a_o_id ))->result_array();
        $currentInvoice =$this->getCurrentInvoice($invoice_id);
        $receiverEID    =$currentInvoice[0]["slave_entity_id"];
        $this->EmailNotificationInvoice(array(array("close_invoices"=>$invoice_id)),$receiverEID);
        return $result;                        
    }
    public function final_save_invoice($invoice_id,$issuerinfo_address,$issuerinfo_email,$invoice_comment)
    {
        $result=$this->db->query("select * from finance.final_save_invoice(?,?,?,?);",array($invoice_id,$issuerinfo_address,$issuerinfo_email,$invoice_comment))->result_array();
        return $result;
    }
    public function final_save_invoice_draft($invoice_id,$issuerinfo_address,$issuerinfo_email,$invoice_comment)
    {
        $result=$this->db->query("select * from finance.final_save_invoice_draft(?,?,?,?);",array($invoice_id,$issuerinfo_address,$issuerinfo_email,$invoice_comment))->result_array();
        return $result;
    }
    
    //*****************************************************************
    //*****************REPORTSS*****************************************
    //*****************************************************************

    //*****************************************************************
    //*****************PAYMENT*****************************************
    //*****************************************************************
    
    public function get_paymentHistory($fromdate,$todate,$payment_source=false)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_e_id= $this->permissions_model->get_active_entity();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        
        $payment_source_filter  ='';
        $date_range_filter      ='';
        //FILTERS   
        switch($payment_source)
        {
            case 'internal':
                $payment_source_filter=' and payment_type_id=1 ';
                break;
            case 'internal':
                $payment_source_filter=' and payment_type_id!=1 ';
                break;
            case false:
                $payment_source_filter=' ';
                break;
        }
        if($fromdate!='' && $todate!='')                                                
            $date_range_filter=" and p.created_on::date>='{$fromdate}'::date and  p.created_on::date<='{$todate}'::date ";
        if($fromdate=='' && $todate!='')                                                
            $date_range_filter=" and p.created_on::date<='{$todate}'::date ";
        if($fromdate!='' && $todate=='')                                                
            $date_range_filter=" and p.created_on::date>='{$fromdate}'::date ";
      
      
        $q=
        "   
            select distinct 
            p.created_by,
            to_char(p.created_on,'yyyy mon dd HH24:MI:SS') as created_on,
            p.modified_by,
            p.modified_on,
            p.deleted_flag,
            p.deleted_by,
            p.deleted_on,
            p.owned_by,
            owner.org_name      as owned_by_name,
            p.payment_id,
            amount,
            payment_status_id,
            ps.status_name      as payment_status_name,
            currency_type_id,
            ct.currency_abbrev  as currency_type_name,
            payment_type_id,                                                           
            ptype.type_code     as payment_type_name,

            transaction_tag,
            authorization_num,
            cardbrand_type,
            cardbrand_value,
            message_type_type,
            message_type_value,
            orderid,
            txrefnum,
            procstatus_type
            procstatus_value,
            approvalstatus_type,
            approvalstatus_value,
            respcode_type,
            respcode_value,
            authcode_type
            authcode_value,
            statusmsg_type,
            statusmsg_value,
            resptime,
            description,
            master_entity_id,
            master.org_name        as  master_entity_name,
            slave_entity_id,
            
            --Added by ryan to get player payment info
            case when (slave.org_name is not  null )then slave.org_name else ep.person_fname||', '||ep.person_lname end as slave_entity_name,
            
            pr.type_id     as reason_type_id,
            pr.type_name   as reason_type_name,
            
            case when payment_type_id=2 then transaction_tag||' [ '||authorization_num||' ]' else txrefnum end as ref_number,
            
            tp.trans_num
            
            from finance.payment                        p
            inner join finance.lu_payment_status        ps      on ps.status_id     =p.payment_status_id
            inner join finance.payment_transaction      pt      on pt.payment_id    =p.payment_id
            inner join finance.lu_currency_type         ct      on ct.type_id       =p.currency_type_id
            inner join finance.lu_payment_type          ptype   on ptype.type_id    =p.payment_type_id
            left join public.entity_org                 master  on master.entity_id =master_entity_id
            left join public.entity_org                 slave   on slave.entity_id  =slave_entity_id
            inner join public.entity_org                owner   on owner.org_id     =p.owned_by
            inner join finance.lu_payment_reason_type   pr      on pr.type_id       =reason_type_id
            
            inner join finance.txn_payment              tp      on tp.payment_id    =p.payment_id
            
            --Added by ryan to get player payment info
            left join public.entity_person              ep      on ep.entity_id     =slave_entity_id
            
            where 
            ct.deleted_flag=false
            "
            .$payment_source_filter
            .$date_range_filter
            ."
             order by p.payment_id desc,trans_num asc        
            ";
            
        $result=$this->db->query($q,array($a_e_id,$a_e_id))->result_array();
        return $result;
    }
    public function get_transactionHistory($fromdate,$todate)
    {
        $date_range_filter='';
        if($fromdate!='' && $todate!='')                                                
            $date_range_filter=" where p.created_on::date>='{$fromdate}'::date and  p.created_on::date<='{$todate}'::date ";
        if($fromdate=='' && $todate!='')                                                
            $date_range_filter=" where p.created_on::date<='{$todate}'::date ";
        if($fromdate!='' && $todate=='')                                                
            $date_range_filter=" where p.created_on::date>='{$fromdate}'::date ";
            
        $q=
        "
            select acc.account_code ,trans_amount,trans_datetime,trans_num
            from finance.transaction t 
            inner join finance.transaction_action   ta  on ta.action_id=t.transaction_action_id
            inner join finance.lu_transaction_type  tt  on tt.type_id=t.transaction_type_id
            inner join finance.account              acc on acc.account_id=t.account_id"
            .$date_range_filter.
            " order by trans_num
        ";
        $result=$this->db->query($q,array())->result_array();
        return $result;
    }
    public function get_invoicesPaymentHistory($fromDate,$toDate)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $search_criteria='';
        if($this->input->get_post("query"))$search_criteria=$this->input->get_post("query");
        //------------------------------------------------------------------
        $date_range_filter='';
        if($fromdate!='' && $todate!='')                                                
            $date_range_filter=" and p.created_on::date>='{$fromdate}'::date and  p.created_on::date<='{$todate}'::date ";
        if($fromdate=='' && $todate!='')                                                
            $date_range_filter=" and p.created_on::date<='{$todate}'::date ";
        if($fromdate!='' && $todate=='')                                                
            $date_range_filter=" and p.created_on::date>='{$fromdate}'::date ";
        $q=
        "
       select    
            i.invoice_id
            ,invoice_title
            ,o1.org_id              as invoice_master_oid
            ,invoice_master_eid
            ,o1.org_name            as invoice_master_ename
            ,o2.org_id              as invoice_slave_oid
            ,invoice_slave_eid
            ,o2.org_name            as invoice_slave_ename
            ,custom_invoice_number
            ,invoice_description
            ,invoice_comment
            ,issuerinfo_address
            ,issuerinfo_email    
            ,slaveinfo_address 
            ,slaveinfo_email
            ,invoice_amount
            ,case when (invoice_slave_eid is not  null)then invoice_paid  else null end as invoice_paid
            ,case when (invoice_slave_eid is not  null)then invoice_owing else null end as invoice_owing
            ,to_char( date_issued,'DD Mon YYYY')     as date_issued
            ,to_char( date_due,'DD Mon YYYY')        as date_due
            ,invoice_status_id
            ,s.status_name as invoice_status_name
            ,i.currency_type_id
            ,currency_abbrev      as currency_type_name
            ,invoice_number
            ,split_part( (permissions.get_user_info(i.created_by)), '$!$', 3)||', '||split_part( (permissions.get_user_info(i.created_by)), '$!$', 4) as created_by_name
            ,i.created_by
            ,i.created_on
            
            ,tp.trans_num p_trans_num
            ,ti.trans_num i_trans_num

            ,p.*
            ,pt.type_code   as payment_type_name
            ,ps.status_name as payment_status_name
            
            
            from    finance.invoice                 i
            left    join public.entity_org          o1          on o1.entity_id =i.invoice_master_eid
            left    join public.entity_org          o2          on o2.entity_id =i.invoice_slave_eid
            inner   join finance.lu_invoice_status  s           on s.status_id  =i.invoice_status_id
            inner   join finance.lu_currency_type   cur         on cur.type_id  =i.currency_type_id                    
            inner   join finance.payment_invoice    ip          on ip.invoice_id=i.invoice_id
            inner   join finance.payment            p           on ip.payment_id=p.payment_id
            
            inner join finance.txn_payment          tp          on tp.payment_id    =p.payment_id
            inner join finance.txn_invoice          ti          on ti.invoice_id    =i.invoice_id
            
            inner join finance.lu_payment_type      pt          on pt.type_id       =p.payment_type_id
            inner join finance.lu_payment_status    ps          on ps.status_id     =p.payment_status_id
            
            where   
            i.deleted_flag=false 
            and cur.deleted_flag=false"
            .$date_range_filter
        ;
        $result=$this->db->query($q)->result_array();
            
        return $result;
    }
    
    //*****************************************************************
    //*****************PAYMENT*****************************************
    //*****************************************************************
    
    public function get_payment_history()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_e_id= $this->permissions_model->get_active_entity();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $q=
        "   
            select distinct 
            p.created_by,
            to_char(p.created_on,'yyyy mon dd HH24:MI:SS') as created_on,
            p.modified_by,
            p.modified_on,
            p.deleted_flag,
            p.deleted_by,
            p.deleted_on,
            p.owned_by,
            owner.org_name      as owned_by_name,
            p.payment_id,
            amount,
            payment_status_id,
            ps.status_name      as payment_status_name,
            currency_type_id,
            ct.currency_abbrev  as currency_type_name,
            payment_type_id, 
            ptype.type_code     as payment_type_name,

            transaction_tag,
            authorization_num,
            cardbrand_type,
            cardbrand_value,
            message_type_type,
            message_type_value,
            orderid,
            txrefnum,
            procstatus_type
            procstatus_value,
            approvalstatus_type,
            approvalstatus_value,
            respcode_type,
            respcode_value,
            authcode_type
            authcode_value,
            statusmsg_type,
            statusmsg_value,
            resptime,
            description,
            master_entity_id,
            master.org_name        as  master_entity_name,
            slave_entity_id,
            
            --Added by ryan to get player payment info
            case when (slave.org_name is not  null )then slave.org_name else ep.person_fname||', '||ep.person_lname end as slave_entity_name,
            
            pr.type_id     as reason_type_id,
            pr.type_name   as reason_type_name,
            
            case when payment_type_id=2 then transaction_tag||' [ '||authorization_num||' ]' else txrefnum end as ref_number
            
            from finance.payment                        p
            inner join finance.lu_payment_status        ps      on ps.status_id     =p.payment_status_id
            inner join finance.payment_transaction      pt      on pt.payment_id    =p.payment_id
            inner join finance.lu_currency_type         ct      on ct.type_id       =p.currency_type_id
            inner join finance.lu_payment_type          ptype   on ptype.type_id    =p.payment_type_id
            left join public.entity_org                 master  on master.entity_id =master_entity_id
            left join public.entity_org                 slave   on slave.entity_id  =slave_entity_id
            inner join public.entity_org                owner   on owner.org_id     =p.owned_by
            inner join finance.lu_payment_reason_type   pr      on pr.type_id       =reason_type_id
            
            --Added by ryan to get player payment info
            left join public.entity_person              ep      on ep.entity_id     =slave_entity_id
            
            where 
            (
                        master.entity_id    =master_entity_id 
                or      slave.entity_id     =slave_entity_id
            )
            and  (slave_entity_id = ? or master_entity_id=?)
            and ct.deleted_flag=false
            
            order by p.payment_id desc        
            ";
        $result=$this->db->query($q,array($a_e_id,$a_e_id))->result_array();
        return $result;
    }
    //BACK-END
    public function pay_invoice_internal($invoice_id,$amount,$currency_type_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"pay_invoice_internal\"(?,?,?,?,?,?)",array($invoice_id,$amount,'INTERNAL PAYMENT',$currency_type_id, $a_u_id , $a_o_id ))->result_array();
        if(intval($result[0]["pay_invoice_internal"])>=1)
        {
            $cur_payment_id=$result[0]["pay_invoice_internal"];
            $this->EmailNotificationConfirmationInvoicePayment($invoice_id,$cur_payment_id,$amount);
        }
            
        return $result;                    
    }           
    public function pay_invoice_direct_db($params)
    {                                            
        $result=$this->db->query("select * from finance.\"pay_invoice_direct\"(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        ,array(
            $params["invoice_id"],$params["amount"],'',$params["currency_type_id"],$params["payment_type_id"],$params["auth_num"],$params["trans_tag"]
            ,'','','','','','','','','','','','','','','','',''
            ,$params["a_u_id_sent"], $params["a_o_id_sent"]
        ))->result_array();
       //STATUS=TRUE MEANS EXTERNAL PAYMENT WAS SUCCESSFULL NOT NECCESSARY SYS OPERATIONS 
       return array_merge(array("status"=>true),$result[0]);
    }
    public function pay_invoice_direct_cc($params)
    {                                    
        $MERCHANDID     =CHASE_MERCHANDID;
        $INDUSTRYTYPE   =CHASE_INDUSTRYTYPE;
        $TERMINALID     =CHASE_TERMINALID;
        $BIN            =CHASE_BIN;
        $type           ='AC';//Auth and Capture
        $xml ="
        <?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <Request> 
        <NewOrder >
            <IndustryType>{$INDUSTRYTYPE}</IndustryType>
            <MessageType>{$type}</MessageType>
            <BIN>{$BIN}</BIN>
            <MerchantID>{$MERCHANDID}</MerchantID>
            <TerminalID>{$TERMINALID}</TerminalID>
            <CardBrand></CardBrand>
            <AccountNum>{$params["cardnumber"]}</AccountNum>
            <Exp>{$params["expirymonth"]}{$params["expiryyear"]}</Exp>
            <CurrencyCode>124</CurrencyCode>
            <CurrencyExponent>2</CurrencyExponent>            
            <CardSecVal>{$params["cvv"]}</CardSecVal>
            <AVSzip>{$params["postalcode"]}</AVSzip>
            <AVSaddress1>{$params["street"]}</AVSaddress1>
            <AVSaddress2></AVSaddress2>
            <AVScity>{$params["city"]}</AVScity>
            <AVSstate>{$params["region"]}</AVSstate>
            <AVSphoneNum></AVSphoneNum>
            <AVSname>{$params["cardname"]}</AVSname>            
            <OrderID>".uniqid('spectrum-')."</OrderID>
            <Amount>".(string)($params["amount"]*100)."</Amount>
            <Comments>Invoice Payment</Comments>
        </NewOrder>
        </Request>
        ";  
        $ac=$this->curl_to_chase($xml);
        
        $ProcStatus     =$this->findChaseTag($ac,'ProcStatus');
        $StatusMsg      =$this->findChaseTag($ac,'StatusMsg');
        $ApprovalStatus =$this->findChaseTag($ac,'ApprovalStatus');
        
        if(intval($ProcStatus["value"])!=0 || $StatusMsg["value"]!='Approved' || intval($ApprovalStatus)!=1 )
            return array("status"=>false,'pay_invoice_direct'=>$StatusMsg["value"]);
        
                
        $cardbrand_type     =$ac[6]["type"];
        $cardbrand_value    =$ac[6]["value"];
        $message_type_type  =$ac[3]["type"];
        $message_type_value =$ac[3]["value"];
        $orderid            =$ac[8]["value"];
        $txrefnum           =$ac[9]["value"];
        $procstatus_type    =$ac[11]["type"];
        $procstatus_value   =$ac[11]["value"];
        $approvalstatus_type=$ac[12]["type"];
        $approvalstatus_value=$ac[12]["value"];
        $respcode_type      =$ac[13]["type"];
        $respcode_value     =$ac[13]["value"];
        $authcode_type      =$ac[16]["type"];
        $authcode_value     =$ac[16]["value"];
        $statusmsg_type     =$ac[19]["type"];
        $statusmsg_value    =$ac[19]["value"];
        $resptime           =$ac[28]["value"];
            
        $result=$this->db->query("select * from finance.\"pay_invoice_direct\"(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        ,array
        (
            $params["invoice_id"],$params["amount"],'CARD PAYMENT',$params["currency_type_id"],$params["payment_type_id"] ,'',''
            ,$cardbrand_type ,$cardbrand_value,$message_type_type,$message_type_value,$orderid ,$txrefnum ,$procstatus_type ,$procstatus_value ,$approvalstatus_type 
            ,$approvalstatus_value,$respcode_type ,$respcode_value ,$authcode_type ,$authcode_value ,$statusmsg_type ,$statusmsg_value,$resptime 
            ,$params["a_u_id_sent"], $params["a_o_id_sent"]
        ))->result_array();
        //STATUS=TRUE MEANS EXTERNAL PAYMENT WAS SUCCESSFULL NOT NECCESSARY SYS OPERATIONS
        return array_merge(array("status"=>true),$result[0]);
    }
    
    //FRONT-END
    public function pay_deposit_direct_db($params)
    {     
        
        if($params["mode"]=='P')
        {
            $result=$this->db->query("select * from finance.\"pay_deposit_direct_player\"(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            ,array
            (
                $params["master_entity_id"],$params["slave_entity_id"] ,$params["amount"] , $params["currency_type_id"] , $params["payment_type_id"] ,$params["auth_num"],$params["trans_tag"] 
                ,'','','','','','','','','','','','','','','','',''
            ))->result_array();    
            return array_merge(array("status"=>true),$result[0]);
        }
        
        if($params["mode"]=='T')
        {
            $result=$this->db->query("select * from finance.\"pay_deposit_direct\"(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            ,array
            (
                $params["master_entity_id"] ,$params["charge_type_id"] ,$params["amount"] , $params["currency_type_id"] , 'description', $params["slave_entity_id"], $params["payment_type_id"] ,$params["auth_num"],$params["trans_tag"] 
                ,'','','','','','','','','','','','','','','','',''
            ))->result_array();    
        }
        
        return array_merge(array("status"=>true),$result[0]);
    }
    
    /**
    * all of this XML should be moved to CHASE library
    * 
    * @param mixed $params
    * @return array
    */
    public function pay_deposit_direct_cc($params)
    {          
    	$params["amount_pennies"] =(int) (number_format($params["amount"],2) * 100 );//parse from string
 
        $MERCHANDID     =CHASE_MERCHANDID;
        $TERMINALID     =CHASE_TERMINALID;
        $INDUSTRYTYPE   =CHASE_INDUSTRYTYPE;
        $BIN            =CHASE_BIN;
        $type           ='AC';//Auth and Capture
        $xml ="
        <?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <Request> 
        <NewOrder >
            <IndustryType>{$INDUSTRYTYPE}</IndustryType>
            <MessageType>{$type}</MessageType>
            <BIN>{$BIN}</BIN>
            <MerchantID>{$MERCHANDID}</MerchantID>
            <TerminalID>{$TERMINALID}</TerminalID>
            <CardBrand></CardBrand>
            <AccountNum>{$params["cardnumber"]}</AccountNum>
            <Exp>{$params["expirymonth"]}{$params["expiryyear"]}</Exp>
            <CurrencyCode>124</CurrencyCode>
            <CurrencyExponent>2</CurrencyExponent>            
            <CardSecVal>{$params["cvv"]}</CardSecVal>
            <AVSzip>{$params["postalcode"]}</AVSzip>
            <AVSaddress1>{$params["street"]}</AVSaddress1>
            <AVSaddress2></AVSaddress2>
            <AVScity>{$params["city"]}</AVScity>
            <AVSstate>{$params["region"]}</AVSstate>
            <AVSphoneNum></AVSphoneNum>
            <AVSname>{$params["cardname"]}</AVSname>            
            <OrderID>".uniqid('spectrum-')."</OrderID>
            <Amount>".$params["amount_pennies"]."</Amount>
            <Comments>Deposit Payment</Comments>
        </NewOrder>
        </Request>
        ";  
        
        $ac = $this->curl_to_chase($xml);                     
        
        
        $ProcStatus     =$this->findChaseTag($ac,'ProcStatus');
        $StatusMsg      =$this->findChaseTag($ac,'StatusMsg');
        $ApprovalStatus =$this->findChaseTag($ac,'ApprovalStatus');
        
        if(intval($ProcStatus["value"])!=0 || $StatusMsg["value"]!='Approved' || intval($ApprovalStatus["value"])!=1)
        {
	        return array("status"=>false,'pay_deposit_direct'=>$StatusMsg["value"],'pay_deposit_direct_player'=>$StatusMsg["value"]);
		}
            
                
        $cardbrand_type     =$ac[6]["type"];
        $cardbrand_value    =$ac[6]["value"];
        $message_type_type  =$ac[3]["type"];
        $message_type_value =$ac[3]["value"];
        $orderid            =$ac[8]["value"];
        $txrefnum           =$ac[9]["value"];
        $procstatus_type    =$ac[11]["type"];
        $procstatus_value   =$ac[11]["value"];
        $approvalstatus_type=$ac[12]["type"];
        $approvalstatus_value=$ac[12]["value"];
        $respcode_type      =$ac[13]["type"];
        $respcode_value     =$ac[13]["value"];
        $authcode_type      =$ac[16]["type"];
        $authcode_value     =$ac[16]["value"];
        $statusmsg_type     =$ac[19]["type"];
        $statusmsg_value    =$ac[19]["value"];
        $resptime           =$ac[28]["value"];
         
        if($params["mode"]=='P')
        {     
            $result=$this->db->query("select * from finance.\"pay_deposit_direct_player\"(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            ,array
            (
                $params["master_entity_id"] ,$params["slave_entity_id"],$params["amount"], $params["currency_type_id"], $params["payment_type_id"],'',''
                ,$cardbrand_type ,$cardbrand_value,$message_type_type,$message_type_value,$orderid ,$txrefnum ,$procstatus_type ,$procstatus_value ,$approvalstatus_type 
                ,$approvalstatus_value,$respcode_type ,$respcode_value ,$authcode_type ,$authcode_value ,$statusmsg_type ,$statusmsg_value,$resptime
            ))->result_array();
            //EMAIL PAYMENT CONFIRMATION AND NOTIFICATION
            return array_merge(array("status"=>true),$result[0]);
        }
        //Team Deposit
        else
        {
            $result=$this->db->query("select * from finance.\"pay_deposit_direct\"(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            ,array
            (
                $params["master_entity_id"] ,$params["charge_type_id"] ,$params["amount"] , $params["currency_type_id"] , '', $params["slave_entity_id"], $params["payment_type_id"] ,'',''
                ,$cardbrand_type ,$cardbrand_value,$message_type_type,$message_type_value,$orderid ,$txrefnum ,$procstatus_type ,$procstatus_value ,$approvalstatus_type 
                ,$approvalstatus_value,$respcode_type ,$respcode_value ,$authcode_type ,$authcode_value ,$statusmsg_type ,$statusmsg_value,$resptime 
            ))->result_array();
            //EMAIL PAYMENT CONFIRMATION AND NOTIFICATION
            return array_merge(array("status"=>true),$result[0]);
        }                      
    }
    public function record_player_payment($master_entity_id ,$slave_entity_id,$amount ,$currency_type_id  ,$payment_type_id ,$season_id,$season_division_id,$payment_id)
    {
        $result=$this->db->query("select * from finance.\"record_player_payment\"(?,?,?,?,?,?,?,?)"
            ,array($master_entity_id ,$slave_entity_id,$amount ,$currency_type_id  ,$payment_type_id ,$season_id,$season_division_id,$payment_id))->result_array();
        return $result;        
    }
    
    /**
    * get the payment info for this payment token
    * 
    * @param mixed $paymentToken
    */
    public function getTempSavedDepositEntry($paymentToken)
    {
        $q      = 
        "
            SELECT * ,ep.entity_id FROM  finance.pending_payments p
			inner JOIN finance.lu_currency_type c   ON p.currency_type_id = c.type_id  
            inner JOIN permissions.user         u   ON p.user_id= u.user_id
            inner JOIN public.entity_person     ep  on ep.person_id=u.person_id
		    AND p.payment_token=?  
        ";
        $result = $this->db->query($q,array($paymentToken))->result_array();
        return $result;                
    }
    
    public function add_wallet_money($entity_id,$amount,$currency_type_id)
    {                                                                                                                                     
        $this->db->query("select * from finance.\"add_wallet_money\"(?,?,?)",array($entity_id,$amount,$currency_type_id));
    }
    public function add_wallet_moneys($entity_amount_s,$currency_type_id)
    {                                                                                                                                     
        $this->db->query("select * from finance.\"add_wallet_moneys\"(?,?)",array($entity_amount_s,$currency_type_id));
    }
    public function release_deposit_invoice($invoice_id,$amount)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        $result=$this->db->query("select * from finance.\"release_deposit_invoice\"(?,?)",array($invoice_id,$amount))->result_array();
        return $result;                    
    }
    public function assign_season_deposit($season_id,$deposit_id,$slave_entity_id,$master_entity_id,$amount)
    {
        $this->db->query("insert into public.season_deposit(season_id,deposit_id,slave_entity_id,master_entity_id,amount)values(?,?,?,?,?)"
        ,array($season_id,$deposit_id,$slave_entity_id,$master_entity_id,$amount));
    }
    
    /* GET SAVED PENDING PAYMENT BASED ON PAYMENT TOKEN*/
    public function get_pendingPaymentInfo($paymentToken)
    {
        $this->db->query("select * from finance.pending_payment where payment_token=?",array($paymentToken));    
    }
    //*****************************************************************
    //*****************EMAIL BUILDERS**********************************
    //*****************************************************************
    
    //ORGANIZATIONS INVOICE PAYMENT
    public function EmailNotificationConfirmationInvoicePayment($invoice_id,$payment_id,$amount,$a_u_id_sent='NONE',$a_o_id_sent='NONE',$a_e_id_sent='NONE')
    {
        //HELP
        /*
        $getOrgsAddresses           =$this->getOrgsAddresses($orgIdList);           //GROUPPED BY ORGID/[ADDRESS]/ADDRESSTYPE 
        $getUsersAddressesContacts  =$this->getUsersAddressesContacts($userIdList); //GROUPPED BY USERID/[ADDRESS]/ADDRESSTYPE & USERID/[CONTACT]/CONTACTTYPE
        $getOrgsUsersRoles          =$this->getOrgsUsersRoles($orgIdList);          //GROUPPED BY ORGID
        $getUsersRoles              =$this->getUsersRoles($userIdList);             //GROUPPED BY USERID
        $getUsersInfo               =$this->getUsersInfo($userIdList)               //GROUPPED BY USERID
        */
        //HELP
        
        /*
        
        (2-1)Team(OT=6)->League(OT=3) payment
        (2-1-1)Payment Receipt to Payer from SSI 
        (2-1-2)Payment Notification to all league Exec and treasurer(3,5) users from Payer
        
        (3-1)League(OT=3)->Assoc payment(OT=2)
        (3-1-1)Payment Receipt to Payer from SSI(ORG)
        (3-1-2)Payment Notification to all Assoc mgr and treasurer(1,5) users from Payer
        
        (4)Assoc(OT=2)->SSI payment(OT=1)
        (4-1-1)Payment Receipt to Payer from SSI(ORG)
        (4-1-2)Payment Notification to all SSI-SYSTEM (14) users from Payer
        
        */
        if($a_u_id_sent!='NONE')
        {
            $a_u_id     =$a_u_id_sent;
            $a_o_id     =$a_o_id_sent;
            $a_e_id     =$a_e_id_sent;
            
        }
        else
        {
            $a_u_id     = $this->permissions_model->get_active_user();
            $a_o_id     = $this->permissions_model->get_active_org();
            $a_e_id     = $this->permissions_model->get_active_entity();
            
        }
        
        
        
        //PAYMENT NOTIFICATIONS***************************************************
        //(2-1-2)/(3-1-2)/(4-1-2)
        $currentInvoicePayment  =$this->getCurrentInvoicePayment($invoice_id,$payment_id,$amount);
        //FROM
        $PAYERAddressesContacts =$this->getUsersAddressesContacts(array($a_u_id));
        $PAYERInfo              =$this->getUsersInfo(array($a_u_id));
        $invoiceIssuerOrgInfo   =$this->getOrgInfo(array($currentInvoicePayment[0]["master_org_id"]));
        $invoicePayerOrgInfo    =$this->getOrgInfo(array($currentInvoicePayment[0]["slave_org_id"]));
        
        //TO
        //FETCH USERID(S) BELONG TO ROLE LEAGUEEXECUTIVE & TREASURER (3,5)
        $currentOrgInfo         =$this->getOrgInfo(array($a_o_id));
        if(count($currentOrgInfo)==0)return;
        if($currentOrgInfo[0]["parent_org_id"]==null)return;
        $MASTERORGUSERS         =$this->getOrgsUsersRoles(array($currentOrgInfo[0]["parent_org_id"]));
        if(count($MASTERORGUSERS)==0)return;
        
        //DECIDE TO WHICH ROLE USERS THIS EMAIL NEED TO BE FORWARDED
        switch(intval($currentOrgInfo[0]["org_type"]))
        {
            case 2:
                $DESTROLES=array(14);   //SYSTEM                        ROLE
                break;
            case 3:
                $DESTROLES=array(1,5);  //ASSOC MANAGER & TREASURER     ROLES
                break;
            case 6:
                $DESTROLES=array(3,5);  //LEAGUE EXECUTIVE & TREASURER  ROLES
                break;              
        }  
                
        $managersList           =array();
        foreach($MASTERORGUSERS[$currentOrgInfo[0]["parent_org_id"]] as $i=>$v)
            foreach($DESTROLES as $n)
                if(intval($v["role_id"])==$n)
                {
                    $managersList[]=$v["user_id"];
                    continue;        
                }
        
        $MASTERMANAGERSAddressesContacts=$this->getUsersAddressesContacts($managersList);
        
        switch(intval($currentInvoicePayment[0]["payment_type_id"]))
        {
            case 1:
                $paymentMethod='SSI';
                break;
            case 2:
                $paymentMethod='IO';
                break;
            case 3:
                $paymentMethod='CC';
                break;
            case 4:
                $paymentMethod='CC';
                break;
        }
        $from = array(
            'orgname'   => @$invoiceIssuerOrgInfo[0]["org_name"],
            'fname'     => @$PAYERInfo[$a_u_id][0]["person_fname"],
            'lname'     => @$PAYERInfo[$a_u_id][0]["person_lname"],
            'address'   => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["address_street"],  //operating
            'city'      => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["address_city"],
            'region'    => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["region_name"],
            'country'   => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["country_name"],
            'code'      => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["postal_value"]
        );
        
        $payment = array(
            'type'          => 'invoice', //can be invoice or deposit
            'time'          => time(),
            'amount'        => $amount,
            'remaining'     => $currentInvoicePayment[0]["invoice_owing"],
            'method'        => $paymentMethod, //can be CC, IO, or SSI
            'feepct'        => @$currentInvoicePayment[1]["fee_rate"],       //FEE_RATE AT DATABASE
            'feetrans'      => @$currentInvoicePayment[1]["fee_amount"],     //FEE_AMT  AT DATABASE
            'feeamt'        => @$currentInvoicePayment[1]["tax_amt_max"]     //THIS VALUE WOULD BE THE MAXIMUM TAX APPLIED
        );
        $invoice = array(
            'number'        => $currentInvoicePayment[0]["invoice_number"],
            'custom_no'     => $currentInvoicePayment[0]["custom_invoice_number"],
            'issued'        => $currentInvoicePayment[0]["date_issued"],
            'due'           => $currentInvoicePayment[0]["date_due"],
            'currency'      => $currentInvoicePayment[0]["currency_type_name"],
            'title'         => $currentInvoicePayment[0]["invoice_title"],
        );
        $gateway = array(
            'type'          => $currentInvoicePayment[0]["payment_type_name"],
            'typeid'        => $currentInvoicePayment[0]["payment_type_id"],
            'trans_id'      => $currentInvoicePayment[0]["trans_num"],
            'io_tag'        => $currentInvoicePayment[0]["transaction_tag"],
            'io_authnum'    => $currentInvoicePayment[0]["authorization_num"],
            'cc_order'      => $currentInvoicePayment[0]["orderid"],
            'cc_txref'      => $currentInvoicePayment[0]["txrefnum"],
            'statusmsg'     => $currentInvoicePayment[0]["statusmsg"],
            'time'          => date('Y-m-d G:i:s',time($currentInvoicePayment[0]["created_on"]) ),
        );
        
        foreach($MASTERMANAGERSAddressesContacts as $i=>$v)
        {
            $TOUSER         = $this->getUsersInfo(array($i/*userId*/));
            
            $email          = @$v[$a_u_id]["contact"][1][0]["value"];
            $to = array(
                'orgname'   => @$currentOrgInfo[0]["org_name"],
                'fname'     => @$TOUSER[$i][0]["person_fname"],
                'lname'     => @$TOUSER[$i][0]["person_lname"],
                'address'   => @$v["address"][2][0]["address_street"],
                'city'      => @$v["address"][2][0]["address_street"],
                'region'    => @$v["address"][2][0]["address_city"],
                'country'   => @$v["address"][2][0]["region_name"],
                'code'      => @$v["address"][2][0]["postal_value"],
            );
            $POSTDATA       = array
            (
                'email'     => $email,
                'payment'   => $payment,
                'invoice'   => $invoice,
                'gateway'   => $gateway,
                'to'        => $to,
                'from'      => $from,
                'test'      => EMAILING_TEST_MODE
            );
            $this->emailPaymentNotification($POSTDATA["email"], $POSTDATA["invoice"], $POSTDATA["payment"], $POSTDATA["gateway"], $POSTDATA["to"], $POSTDATA["from"], $POSTDATA["test"],$POSTDATA);
        }
        
        //PAYMENT CONFIRMATION[RECIEPT]***************************************************
        //(2-1-1)/(3-1-1)/(4-1-1) 
        $invoiceItems           =$this->get_invoice_items($invoice_id);
        $invoiceItemsSumNoTax   =$this->get_invoice_items_sum_noTax($invoice_id);
        $invoiceTaxDetails      =$this->get_invoice_tax_details($invoice_id);
        foreach($invoiceTaxDetails as $i=>$v)
            $taxInfo[$v["label"]]=array("rate"=>$v["rate"],"amt"=>($v["amt"]));
                                                                         
        foreach($invoiceItems as $i=>$v)
        {
            $INVOICEITEMS[$i]["item"]   =$invoiceItems[$i]["charge_type_name"];
            $INVOICEITEMS[$i]["qty"]    =$invoiceItems[$i]["quantity"];
            $INVOICEITEMS[$i]["price"]  =$invoiceItems[$i]["charge_price"];
            $INVOICEITEMS[$i]["total"]  =$invoiceItems[$i]["sub_amount"];
        }
        $items = $INVOICEITEMS;
        $totals = array
        (
            'subtotal'  => @$invoiceItemsSumNoTax[0]["sum"],
            'tax'       => @$taxInfo,
            'total'     => $currentInvoicePayment[0]["invoice_amount"]
        );
        $to= array(
            'orgname'   => $invoicePayerOrgInfo[0]["org_name"]     ,
            'fname'     => $PAYERInfo[$a_u_id][0]["person_fname"]   ,
            'lname'     => $PAYERInfo[$a_u_id][0]["person_lname"]   ,
            'address'   => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["address_street"]  ,  //operating
            'city'      => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["address_city"]    ,
            'region'    => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["region_name"]     ,
            'country'   => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["country_name"]    ,
            'code'      => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["postal_value"]
        );
        //static setup
        $from = array(
            'orgname'   => 'Servillian Solutions Inc',
            'fname'     => false,
            'lname'     => false,
            'address'   => '6910 Pleasant Valley Road',
            'city'      => 'Vernon',
            'region'    => 'British Columbia',
            'country'   => 'Canada',
            'code'      => 'V1B 3T3'
        );
        $gateway = array(
            'type'      => $currentInvoicePayment[0]["payment_type_name"],
            'typeid'    => $currentInvoicePayment[0]["payment_type_id"],
            'trans_id'  => $currentInvoicePayment[0]["trans_num"],
            'io_tag'    => $currentInvoicePayment[0]["transaction_tag"],
            'io_authnum'=> $currentInvoicePayment[0]["authorization_num"],
            'cc_order'  => $currentInvoicePayment[0]["orderid"],
            'cc_txref'  => $currentInvoicePayment[0]["txrefnum"],
            'statusmsg' => $currentInvoicePayment[0]["statusmsg"],
            'time'      => date('Y-m-d G:i:s',time($currentInvoicePayment[0]["created_on"]) ),
        );
        
        foreach($PAYERAddressesContacts as $i=>$v)
        {
            $email          = @$v["contact"][1][0]["value"];
            $this->emailPaymentReceipt($email, $items, $totals, $gateway, $to, $from, EMAILING_TEST_MODE);
        }
        
    }       
    public function EmailNotificationInvoice($closeInvoicesResult,$recipient_eids)
    {
        //HELP
        /*
        $getOrgsAddresses           =$this->getOrgsAddresses($orgIdList);           //GROUPPED BY ORGID/[ADDRESS]/ADDRESSTYPE 
        $getUsersAddressesContacts  =$this->getUsersAddressesContacts($userIdList); //GROUPPED BY USERID/[ADDRESS]/ADDRESSTYPE & USERID/[CONTACT]/CONTACTTYPE
        $getOrgsUsersRoles          =$this->getOrgsUsersRoles($orgIdList);          //GROUPPED BY ORGID
        $getUsersRoles              =$this->getUsersRoles($userIdList);             //GROUPPED BY USERID
        $getUsersInfo               =$this->getUsersInfo($userIdList)               //GROUPPED BY USERID
        */
        //HELP  
        /*
        
        (2-1)Team(OT=6)->League(OT=3) payment
        (2-2)League->Team Invoice      invoice notification to team manager            from currentuser     ****

        
        (3-1)League(OT=3)->Assoc payment(OT=2)
        (3-2)Assoc->League Invoice    invoice notification to league exec & treasurer  from currentuser     ****
        
        (4-1)Assoc(OT=2)->SSI payment(OT=1)
        (4-2)SSI->Assoc Invoice       invoice notification to Assoc exec & treasurer   from currentuser     ****
        
        */
        
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        //GET CURRENT USER ADDRESSES AND CONTACTS
        $CurrentuserAddressesContacts   =$this->getUsersAddressesContacts(array($a_u_id)); //GROUPPED BY USERID/[ADDRESS]/ADDRESSTYPE & USERID/[CONTACT]/CONTACTTYPE
        //GET CURRENT USER INFO
        $currentUserInfo                =$this->getUsersInfo(array($a_u_id));
        
        
        $invoiceIdList='';
        foreach($closeInvoicesResult as $i=>$v) 
        {
            $invoiceIdList.=($v["close_invoices"].',');           
        }
            
            
        if(trim($invoiceIdList)!='')
            $invoiceIdList=substr($invoiceIdList,0,strlen($invoiceIdList)-1);
        $invoiceIdList=explode(',',$invoiceIdList);                          
        
        $currentOrgInfo         =$this->getOrgInfo(array($a_o_id));
        //DECIDE TO WHICH ROLE USERS THIS EMAIL NEED TO BE FORWARDED
        switch(intval($currentOrgInfo[0]["org_type"]))
        {
            case 1:
                $DESTROLES=array(1,5);  //ASSOCIATION MANAGER & TREASURER   ROLES
                break;
            case 2:
                $DESTROLES=array(3,5);  //LEAGUE EXECUTIVE & TRESURER       ROLES
                break;
            case 3:
                $DESTROLES=array(4);    //TEAM MANAGER                      ROLE
                break;
        } 
        //PASS ENTITIES_LIST AND ...
        $getEntitysOrgIds           =$this->getEntitysOrgIds($recipient_eids);
        $getEntitysOrgIdsCommabased ='';
        foreach($getEntitysOrgIds as $v)
            $getEntitysOrgIdsCommabased.=($v["org_id"].',');
        if(strlen(trim($getEntitysOrgIdsCommabased))!=0)
            $getEntitysOrgIdsCommabased=substr($getEntitysOrgIdsCommabased,0,strlen($getEntitysOrgIdsCommabased)-1);
            
        $orgIds =explode(',',$getEntitysOrgIdsCommabased);
        
        
        $counter=-1;
        foreach($orgIds as $s=>$h) //PER EACH ORG
        {
            $counter++;
            $getOrgsUsersRoles          =$this->getOrgsUsersRoles(array($h));    //GROUPPED BY ORGID
            
            
            $selectedUserList='';
            foreach($getOrgsUsersRoles as $i=>$v )
                foreach($v as $k=>$l )
                    if(in_array($l["role_id"],$DESTROLES))   
                        $selectedUserList.=($l["user_id"].',');
            if(trim($selectedUserList)!='')
                $selectedUserList=substr($selectedUserList,0,strlen($selectedUserList)-1);
              
            if(trim($selectedUserList)=='')continue;
            $getToUsersAddressesContacts  =$this->getUsersAddressesContacts(explode(',',$selectedUserList)); //GROUPPED BY USERID/[ADDRESS]/ADDRESSTYPE & USERID/[CONTACT]/CONTACTTYPE
            $getToUsersInfo               =$this->getUsersInfo(explode(',',$selectedUserList));              //GROUPPED BY USERID
            foreach($getToUsersAddressesContacts as $i=>$v)
            {
                $invoice_id     =$invoiceIdList[$counter];
                $currentInvoice =$this->getCurrentInvoice($invoice_id);
                                                                    
                
                //PAYMENT CONFIRMATION[RECIEPT]***************************************************
                //(2-1-1)/(3-1-1)/(4-1-1) 
                $invoiceItemsSumNoTax   =$this->get_invoice_items_sum_noTax($invoice_id);
                $invoiceTaxDetails      =$this->get_invoice_tax_details($invoice_id);
                foreach($invoiceTaxDetails as $q=>$p)
                    $taxInfo[$p["label"]]=array("rate"=>$p["rate"],"amt"=>($p["amt"]));
                
                $invoiceItems           =$this->get_invoice_items($invoice_id);  
                foreach($invoiceItems as $e=>$r)
                {
                    $INVOICEITEMS[$e]["item"]   =$invoiceItems[$e]["charge_type_name"];
                    $INVOICEITEMS[$e]["qty"]    =$invoiceItems[$e]["quantity"];
                    $INVOICEITEMS[$e]["price"]  =$invoiceItems[$e]["charge_price"];
                    $INVOICEITEMS[$e]["total"]  =$invoiceItems[$e]["sub_amount"];
                }
                $items = $INVOICEITEMS;
                
                $from = array(
                    'orgname'   => $currentOrgInfo[0]["org_name"],
                    'fname'     => $currentUserInfo[$a_u_id][0]["person_fname"],
                    'lname'     => $currentUserInfo[$a_u_id][0]["person_lname"],
                    'address'   => @$CurrentuserAddressesContacts[$a_u_id]["address"][0]["address_street"],
                    'city'      => @$CurrentuserAddressesContacts[$a_u_id]["address"][0]["address_city"],
                    'region'    => @$CurrentuserAddressesContacts[$a_u_id]["address"][0]["region_name"],
                    'country'   => @$CurrentuserAddressesContacts[$a_u_id]["address"][0]["country_name"],
                    'code'      => @$CurrentuserAddressesContacts[$a_u_id]["address"][0]["postal_value"],
                );
                
                $to= array(
                    'orgname'   => $currentInvoice[0]["master_org_name"]     ,
                    'fname'     => $getToUsersInfo[$a_u_id][0]["person_fname"]   ,
                    'lname'     => $getToUsersInfo[$a_u_id][0]["person_lname"]   ,
                    'address'   => @$v[$i]["address"][2][0]["address_street"]  ,  //operating
                    'city'      => @$v[$i]["address"][2][0]["address_city"]    ,
                    'region'    => @$v[$i]["address"][2][0]["region_name"]     ,
                    'country'   => @$v[$i]["address"][2][0]["country_name"]    ,
                    'code'      => @$v[$i]["address"][2][0]["postal_value"]
                );
                
                $email = $v["contact"][1][0]["value"];
                $items = $items;
                $totals = array(
                    'subtotal'  => @$invoiceItemsSumNoTax[0]["sum"],
                    'tax'       => @$taxInfo,
                    'total'     => $currentInvoice[0]["invoice_amount"],
                    'paid'      => $currentInvoice[0]["invoice_paid"],
                    'owing'     => $currentInvoice[0]["invoice_owing"]
                );
                
                $invoice = array(
                    'number'    => $currentInvoice[0]["invoice_number"],
                    'custom_no' => $currentInvoice[0]["custom_invoice_number"],
                    'issued'    => $currentInvoice[0]["date_issued"],
                    'due'       => $currentInvoice[0]["date_due"],
                    'currency'  => $currentInvoice[0]["currency_type_name"],
                    'title'     => $currentInvoice[0]["invoice_title"],
                    'descr'     => '',
                    'comments'  => $currentInvoice[0]["invoice_comment"]
                );
                
                
                $POSTDATA = array(
                    'email'     => $email,
                    'invoice'   => $invoice,
                    'items'     => $items,
                    'totals'    => $totals,
                    'to'        => $to,
                    'from'      => $from,
                    'test'      => EMAILING_TEST_MODE
                );                                                                             
                $this->emailInvoice($POSTDATA["email"],$POSTDATA["invoice"], $POSTDATA["items"], $POSTDATA["totals"], $POSTDATA["to"], $POSTDATA["from"], $POSTDATA["test"]);  
            }   
        }
    }
    //TEAM/PLAYER DEPOSIT PAYMENT
    public function EmailNotificationConfirmationDepositPayment($deposit_id,$payment_id,$amount,$a_u_id_sent='NONE',$a_o_id_sent='NONE',$a_e_id_sent='NONE',$a_person_eid_sent='NONE')
    {
 		//echo "EmailNotificationConfirmationDepositPayment";
        //HELP
        /*
        $getOrgsAddresses           =$this->getOrgsAddresses($orgIdList);           //GROUPPED BY ORGID/[ADDRESS]/ADDRESSTYPE 
        $getUsersAddressesContacts  =$this->getUsersAddressesContacts($userIdList); //GROUPPED BY USERID/[ADDRESS]/ADDRESSTYPE & USERID/[CONTACT]/CONTACTTYPE
        $getOrgsUsersRoles          =$this->getOrgsUsersRoles($orgIdList);          //GROUPPED BY ORGID
        $getUsersRoles              =$this->getUsersRoles($userIdList);             //GROUPPED BY USERID
        $getUsersInfo               =$this->getUsersInfo($userIdList)               //GROUPPED BY USERID
        */
        //HELP
        
        /*
        //REQUIRED HERE
        (1-1)Player->Team ==>Deposit Payment Receipt to Player  from SSI(org)& Notification to all Team     Mgr  (4)    RoleUsers from Payer
        (1-2)Team->league ==>Deposit Payment Receipt to Team    from SSI(org)& Notification to all League   Exec (3,5)  RoleUsers from Payer
        
        */
        $a_u_id         =$a_u_id_sent;   
        $a_person_eid   =$a_person_eid_sent;
        
        $a_o_id     =$a_o_id_sent;  //will be calculated
        $a_e_id     =$a_e_id_sent;
        
        
       // echo "  user id $a_u_id";
        //frontend uses only entity_id not org_id
        $orgs=$this->getEntitysOrgIds($a_e_id);
        if(count($orgs))$a_o_id=$orgs[0]["org_id"];
        else $a_o_id = false;
            
        //PAYMENT NOTIFICATIONS***************************************************
        //(1-1)/(1-2)
        $currentDepositPayment  =$this->getCurrentDepositPayment($deposit_id,$payment_id,$amount);
        $depositIssuerOrgInfo   =$this->getOrgInfo(array($currentDepositPayment[0]["master_org_id"]));
        
        //$a_o_id='' means from player
        if($a_o_id)
        {
            $PAYERAddressesContacts =$this->getUsersAddressesContacts(array($a_u_id));
            
            $PAYERInfo              =$this->getUsersInfo(array($a_u_id));
            $currentOrgInfo         =$this->getOrgInfo(array($a_o_id));
        }   
        else                                                           
        {
        	//echo "EMAIL PLAYER ($a_person_eid)";
            $PAYERAddressesContacts =$this->getUsersAddressesContactsByEntity(array($a_person_eid));
            $PAYERInfo              =$this->getUsersInfoByEntity(array($a_person_eid));
            $currentOrgInfo         =null;
            //var_dump($PAYERAddressesContacts);
        }
            
            
        $MASTERORGUSERS             =$this->getOrgsUsersRoles(array($currentDepositPayment[0]["master_org_id"]));            
        //if(count($MASTERORGUSERS)==0)return;
        //echo "count managers ".count($MASTERORGUSERS);
        //DECIDE TO WHICH ROLE USERS THIS EMAIL NEED TO BE FORWARDED
        
        if($currentOrgInfo!=null)
            $DESTROLES=array(3,5);  //LEAGUE EXECUTIVE & TREASURER  ROLES
        else 
            $DESTROLES=array(4);
            
                
        $managersList           =array();
        $MASTERMANAGERSAddressesContacts           =array();
        if(isset($MASTERORGUSERS[$currentDepositPayment[0]["master_org_id"]]))
        {
	        foreach($MASTERORGUSERS[$currentDepositPayment[0]["master_org_id"]] as $i=>$v)
	        {
	            foreach($DESTROLES as $n)
	            {
	                if(intval($v["role_id"])==$n)
	                {
	                    $managersList[]=$v["user_id"];
	                    continue;        
	                }
				}
			}
		}
        
        if(count($managersList))$MASTERMANAGERSAddressesContacts=$this->getUsersAddressesContacts($managersList);
        
        switch(intval($currentDepositPayment[0]["payment_type_id"]))
        {
            case 1:
                $paymentMethod='SSI';
                break;
            case 2:
                $paymentMethod='IO';
                break;
            case 3:
                $paymentMethod='CC';
                break;
            case 4:
                $paymentMethod='CC';
                break;
        }
        $from = array(
            'orgname'   => @$invoiceIssuerOrgInfo[0]["org_name"],
            'fname'     => @$PAYERInfo[$a_u_id][0]["person_fname"],
            'lname'     => @$PAYERInfo[$a_u_id][0]["person_lname"],
            'address'   => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["address_street"],  //operating
            'city'      => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["address_city"],
            'region'    => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["region_name"],
            'country'   => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["country_name"],
            'code'      => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["postal_value"]
        );
        
        
        $payment = array(
            'type'          => 'invoice', //can be invoice or deposit
            'time'          => time(),
            'amount'        => $amount,
            'remaining'     => $currentDepositPayment[0]["payment_amount"],
            'method'        => $paymentMethod, //can be CC, IO, or SSI
            'feepct'        => @$currentDepositPayment[1]["fee_rate"],       //FEE_RATE AT DATABASE
            'feetrans'      => @$currentDepositPayment[1]["fee_amount"],     //FEE_AMT  AT DATABASE
            'feeamt'        => @$currentDepositPayment[1]["tax_amt_max"]     //THIS VALUE WOULD BE THE MAXIMUM TAX APPLIED
        );
        $gateway = array(
            'type'          => $currentDepositPayment[0]["payment_type_name"],
            'typeid'        => $currentDepositPayment[0]["payment_type_id"],
            'trans_id'      => $currentDepositPayment[0]["trans_num"],
            'io_tag'        => $currentDepositPayment[0]["transaction_tag"],
            'io_authnum'    => $currentDepositPayment[0]["authorization_num"],
            'cc_order'      => $currentDepositPayment[0]["orderid"],
            'cc_txref'      => $currentDepositPayment[0]["txrefnum"],
            'statusmsg'     => $currentDepositPayment[0]["statusmsg"],
            'time'          => date('Y-m-d G:i:s',time($currentDepositPayment[0]["created_on"]) ),
        );
        if(count($managersList))
        foreach($MASTERMANAGERSAddressesContacts as $i=>$v)
        {
            $TOUSER         = $this->getUsersInfo(array($i/*userId*/));
            
            $email          = @$v[$a_u_id]["contact"][1][0]["value"];
            $to = array(
                'orgname'   => $currentOrgInfo[0]["org_name"],
                'fname'     => $TOUSER[$i][0]["person_fname"],
                'lname'     => $TOUSER[$i][0]["person_lname"],
                'address'   => @$v["address"][2][0]["address_street"],
                'city'      => @$v["address"][2][0]["address_street"],
                'region'    => @$v["address"][2][0]["address_city"],
                'country'   => @$v["address"][2][0]["region_name"],
                'code'      => @$v["address"][2][0]["postal_value"],
            );
            $POSTDATA       = array
            (
                'email'     => $email,
                'payment'   => $payment,
                'gateway'   => $gateway,
                'to'        => $to,
                'from'      => $from,
                'test'      => EMAILING_TEST_MODE
            );
            //$this->emaymentNotification($POSTDATA["email"], $POSTDATA["payment"], $POSTDATA["gateway"], $POSTDATA["to"], $POSTDATA["from"], $POSTDATA["test"],$POSTDATA);
        }
        
        //PAYMENT CONFIRMATION[RECIEPT]***************************************************
        //(2-1-1)/(3-1-1)/(4-1-1) 
        
        $items = null;
        $totals = array
        (
            'subtotal'  => null,
            'tax'       => null,
            'total'     => $currentDepositPayment[0]["payment_amount"]
        );
        $to= array(
            'orgname'   => $depositIssuerOrgInfo[0]["org_name"]     ,
            'fname'     => $PAYERInfo[$a_u_id][0]["person_fname"]   ,
            'lname'     => $PAYERInfo[$a_u_id][0]["person_lname"]   ,
            'address'   => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["address_street"]  ,  //operating
            'city'      => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["address_city"]    ,
            'region'    => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["region_name"]     ,
            'country'   => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["country_name"]    ,
            'code'      => @$PAYERAddressesContacts[$a_u_id]["address"][2][0]["postal_value"]
        );
        //static setup
        $from = array(
            'orgname'   => 'Servillian Solutions Inc',
            'fname'     => false,
            'lname'     => false,
            'address'   => '6910 Pleasant Valley Road',
            'city'      => 'Vernon',
            'region'    => 'British Columbia',
            'country'   => 'Canada',
            'code'      => 'V1B 3T3'
        );
        $gateway = array(
            'type'      => $currentDepositPayment[0]["payment_type_name"],
            'typeid'    => $currentDepositPayment[0]["payment_type_id"],
            'trans_id'  => $currentDepositPayment[0]["trans_num"],
            'io_tag'    => $currentDepositPayment[0]["transaction_tag"],
            'io_authnum'=> $currentDepositPayment[0]["authorization_num"],
            'cc_order'  => $currentDepositPayment[0]["orderid"],
            'cc_txref'  => $currentDepositPayment[0]["txrefnum"],
            'statusmsg' => $currentDepositPayment[0]["statusmsg"],
            'time'      => date('Y-m-d G:i:s',time($currentDepositPayment[0]["created_on"]) ),
        );
        
        
        //do not send tothe same address more than once
        $sent = array();
        foreach($PAYERAddressesContacts as $i=>$v)
        {
            $email          = @$v["contact"][1][0]["value"];
             if(isset($sent[$email])) {continue;}
 
            $this->emailPaymentReceipt($email, $items, $totals, $gateway, $to, $from, EMAILING_TEST_MODE);
            $sent[$email] = true;
        } 
        
    }       
    
    
    //*****************************************************************
    //**ADDRESS/CONTACT STRUCTURE BUILDER BASED-ON USERIDS & ORGIDS ***
    //*****************************************************************
    
    public function getOrgsAddresses($orgIdList)            //GROUPPED BY ORGID/[ADDRESS]/ADDRESSTYPE 
    {                                                
        $result         =array();
        //Addresses
        $q=
        '
            select eo.org_id,a.address_id,ea.address_type,AT.type_name,a.address_street,a.address_city, r.region_abbr,r.region_name,c.country_id,c.country_abbr,c.country_name,p.postal_id,p.postal_value
            ,a.modified_on

            from "public".address as a
            left join "public".lu_address_region    r     on r.region_id    =a.address_region
            left join "public".lu_address_country   c     on c.country_id   =a.address_country
            left join "public".lu_address_postal    p     on p.postal_id    =a.address_postal
            
            inner join "public".entity_address      ea    on ea.address_id  =a.address_id
            inner join public.entity_org            eo    on eo.entity_id   =ea.entity_id
            inner  join "public".lu_address_type    at    on at.type_id     =ea.address_type

            where    
            eo.org_id in (?)
            and ea.is_active=true
            order by org_id,ea.address_type    
        ';
        $data           =$this->db->query($q,array(implode(',',$orgIdList)))->result_array();
        $curOrgId       =null;
        $curAddressType =null;
        
        for($i = 0 ;$i<count($data);$i++)
        {
            if($curOrgId==null      || $data[$i]["org_id"]!=$curOrgId)
                $curOrgId       =$data[$i]["org_id"];
                
            if($curAddressType==null|| $data[$i]["address_type"]!=$curAddressType)
                $curAddressType=$data[$i]["address_type"];
                    
            $result[$curOrgId]["address"][$curAddressType][]=$data[$i];
        }
        //Contacts
        /*$q=
        "
            select org_id,contact_type,value as email
            from        public.entity_contact   ec
            inner join  public.contact_method   cm  on cm.contact_method_id=ec.contact_method_id
            inner join  public.entity_org       eo  on eo.entity_id=ec.entity_id
            
            where 
                    eo.org_id in (?)
            and     value is not null and value != ''
            order by org_id,contact_type    
        ";
        $data           =$this->db->query($q,array(implode(',',$orgIdList)))->result_array();
        $curOrgId       =null;
        $curContactType =null;
        for($i = 0 ;$i<count($data);$i++)
        {
            if($curOrgId==null      ||  $data[$i]["org_id"]!=$curOrgId)
                $curOrgId       =$data[$i]["org_id"];
            
            if($curContactType==null||  $data[$i]["contact_type"]!=$curContactType)
                $curContactType =$data[$i]["contact_type"];
            
            $result[$curOrgId]["contact"][$curContactType][]=$data[$i];
        } 
        */             
        return $result;        
    }
    public function getUsersAddressesContacts($userIdList)  //GROUPPED BY USERID/[ADDRESS]/ADDRESSTYPE & USERID/[CONTACT]/CONTACTTYPE
    {
        $result         =array();
        //Addresses
        $q=
        
        "    select u.user_id,a.address_id,ea.address_type,AT.type_name,a.address_street,a.address_city, r.region_abbr,r.region_name,c.country_id,c.country_abbr,c.country_name,p.postal_id,p.postal_value
            ,a.modified_on

            from public.address as a
            left join public.lu_address_region    r   on r.region_id      =a.address_region
            left join public.lu_address_country   c   on c.country_id     =a.address_country
            left join public.lu_address_postal    p   on p.postal_id      =a.address_postal
            
            inner join public.entity_address      ea  on ea.address_id    =a.address_id
            inner join public.entity_person         ep  on ep.entity_id     =ea.entity_id
            inner join permissions.user             u   on u.person_id      =ep.person_id
            inner  join public.lu_address_type    at  on at.type_id       =ea.address_type

            where    
            u.user_id in (.".implode(',',$userIdList).")
            and ea.is_active=true
            order by user_id,ea.address_type
        ";
        //$data           =$this->db->query($q,array(implode(',',$userIdList)))->result_array();
        $data           =$this->db->query($q)->result_array();
        
        $curUserId      =null;
        $curAddressType =null;
        
        for($i = 0 ;$i<count($data);$i++)
        {
            if($curUserId==null || $data[$i]["user_id"]!=$curUserId)
                $curUserId=$data[$i]["user_id"];
            if($curAddressType==null || $data[$i]["address_type"]!=$curAddressType)
                $curAddressType=$data[$i]["address_type"];
            
            $result[$curUserId]["address"][$curAddressType][]=$data[$i];
        }
        //Contacts
        $q=
        "
            select user_id,contact_type,value 
            from        public.entity_contact ec
            inner join  public.contact_method cm     on cm.contact_method_id=ec.contact_method_id
            inner join  public.entity_person  ep     on ep.entity_id=ec.entity_id
            inner join  permissions.user      u      on u.person_id=ep.person_id
            where 
                    u.user_id in (".implode(',',$userIdList).")
            and     value is not null and value != ''
            order by user_id,contact_type
        ";
        $data           =$this->db->query($q)->result_array();
        $curUserId      =null;
        $curContactType =null;
        for($i = 0 ;$i<count($data);$i++)
        {
            if($curUserId==null || $data[$i]["user_id"]!=$curUserId)
                $curUserId      =$data[$i]["user_id"];
                
            if($curContactType==null || intval($data[$i]["user_id"]["contact_type"])!=intval($curContactType))
                $curContactType =$data[$i]["contact_type"];                                                   
                
            
            $result[$curUserId]["contact"][$curContactType][]=$data[$i];
        }              
        return $result;
    }
    public function getUsersAddressesContactsByEntity($entityIdList)  //GROUPPED BY ENTITYID/[ADDRESS]/ADDRESSTYPE & ENTITYID/[CONTACT]/CONTACTTYPE
    {
        $result         =array();
        //Addresses
        $q=
        
        "    select EP.entity_id,a.address_id,ea.address_type,AT.type_name,a.address_street,a.address_city, r.region_abbr,r.region_name,c.country_id,c.country_abbr,c.country_name,p.postal_id,p.postal_value
            ,a.modified_on

            from public.address as a
            left join public.lu_address_region    r   on r.region_id      =a.address_region
            left join public.lu_address_country   c   on c.country_id     =a.address_country
            left join public.lu_address_postal    p   on p.postal_id      =a.address_postal
            
            inner join public.entity_address      ea  on ea.address_id    =a.address_id
            inner join public.entity_person         ep  on ep.entity_id     =ea.entity_id
            inner  join public.lu_address_type    at  on at.type_id       =ea.address_type

            where    
            ep.entity_id in (.".implode(',',$entityIdList).")
            and ea.is_active=true
            order by entity_id,ea.address_type
        ";
        //$data           =$this->db->query($q,array(implode(',',$userIdList)))->result_array();
        $data           =$this->db->query($q)->result_array();
        
        $curEntityId      =null;
        $curAddressType =null;
        
        for($i = 0 ;$i<count($data);$i++)
        {
            if($curEntityId==null || $data[$i]["entity_id"]!=$curEntityId)
                $curEntityId=$data[$i]["entity_id"];
            if($curAddressType==null || $data[$i]["address_type"]!=$curAddressType)
                $curAddressType=$data[$i]["address_type"];
            
            $result[$curEntityId]["address"][$curAddressType][]=$data[$i];
        }
        //Contacts
        $q=
        "
            select ec.entity_id,contact_type,value 
            from        public.entity_contact ec
            inner join  public.contact_method cm     on cm.contact_method_id=ec.contact_method_id
            inner join  public.entity_person  ep     on ep.entity_id=ec.entity_id
            where 
                    ec.entity_id in (".implode(',',$entityIdList).")
            and     value is not null and value != ''
            order by entity_id,contact_type
        ";
        $data           =$this->db->query($q)->result_array();
        $curEntityId    =null;
        $curContactType =null;
        for($i = 0 ;$i<count($data);$i++)
        {
            if($curEntityId==null || $data[$i]["entity_id"]!=$curEntityId)
                $curEntityId      =$data[$i]["entity_id"];
                
            if($curContactType==null || intval($data[$i]["entity_id"]["contact_type"])!=intval($curContactType))
                $curContactType =$data[$i]["contact_type"];                                                   
            
            $result[$curEntityId]["contact"][$curContactType][]=$data[$i];
        }              
        return $result;
    }
    public function getUsersRoles($userIdList)              //GROUPPED BY USERID
    {
        $result =array();
        $q=
        '
            select a.org_id,u.user_id,r.role_id,r.role_name  ,
            ,ep.person_id,ep.person_fname,ep.person_lname,ep.person_birthdate,ep.person_gender
            
            from        permissions."assignment" a
            inner join  permissions.user         u   on u.user_id    =a.user_id
            inner join  permissions.lu_role      r   on r.role_id    =a.role_id
            inner join  public.entity_person     ep  on ep.person_id =u.person_id

            where 
                CURRENT_TIMESTAMP>=a.effective_range_start
            and (CURRENT_TIMESTAMP<=a.effective_range_end or a.effective_range_end is null)
            and a.isactive=true
                
            and u.user_id in ('.implode(',',$userIdList).')
            order by org_id,user_id,role_id
        ';
        $data       =$this->db->query($q)->result_array();
        $curUserId  =null;
        for($i = 0 ;$i<count($data);$i++)
        {
            if($curUserId==null || $data[$i]["user_id"]!=$curUserId)
                $curUserId=$data[$i]["user_id"];
            $result[$curUserId][]=$data[$i];
        }
        return $result;
    }
    public function getOrgsUsersRoles($orgIdList)           //GROUPPED BY ORGID
    {
        $result =array();
        $q=
        '                   
            select a.org_id,u.user_id,r.role_id,r.role_name
            ,ep.person_id,ep.person_fname,ep.person_lname,ep.person_birthdate,ep.person_gender
            ,eo.org_name
            
            from        permissions."assignment" a
            inner join  permissions.user         u   on u.user_id    =a.user_id
            inner join  permissions.lu_role      r   on r.role_id    =a.role_id
            inner join  public.entity_person     ep  on ep.person_id =u.person_id
            inner join  public.entity_org        eo  on eo.org_id    =a.org_id   

            where 
                CURRENT_TIMESTAMP>=a.effective_range_start
            and (CURRENT_TIMESTAMP<=a.effective_range_end or a.effective_range_end is null)
            --and a.isactive=true
                
            and a.org_id in ('.implode(',',$orgIdList).')
            order by org_id,user_id,role_id
        ';
        $data       =$this->db->query($q)->result_array();
        $curOrgId   =null;
        for($i = 0 ;$i<count($data);$i++)
        {
            if($curOrgId==null || $data[$i]["org_id"]!=$curOrgId)
                $curOrgId=$data[$i]["org_id"];
            $result[$curOrgId][]=$data[$i];
        }
        return $result;
    }
    public function getUsersInfo($userIdList)               //GROUPPED BY USERID
    {
        $result =array();
        $q=
        '                   
            select u.user_id,ep.person_id,ep.person_fname,ep.person_lname,ep.person_birthdate,ep.person_gender
            from    permissions.user            u   
            inner join  public.entity_person    ep  on ep.person_id =u.person_id
            
            where 
            user_id in ('.implode(',',$userIdList).')
            order by user_id
        ';
        $data       =$this->db->query($q)->result_array();
        $curUserId  =null;
        for($i = 0 ;$i<count($data);$i++)
        {
            if($curUserId==null || $data[$i]["user_id"]!=$curUserId)
                $curUserId=$data[$i]["user_id"];
            $result[$curUserId][]=$data[$i];
        }
        return $result;
    }
    public function getUsersInfoByEntity($entityIdList)     //GROUPPED BY ENTITYID
    {
        $result =array();
        $q=
        '                   
            select ep.entity_id,ep.person_id,ep.person_fname,ep.person_lname,ep.person_birthdate,ep.person_gender
            from        public.entity_person    ep  
            
            where 
            entity_id in ('.implode(',',$entityIdList).')
            order by entity_id
        ';
        $data       =$this->db->query($q)->result_array();
        $curEntityId=null;
        for($i = 0 ;$i<count($data);$i++)
        {
            if($curEntityId==null || $data[$i]["entity_id"]!=$curEntityId)
                $curEntityId=$data[$i]["entity_id"];
            $result[$curEntityId][]=$data[$i];
        }
        return $result;
    }
    public function getOrgInfo($orgIdList)                  //GROUPPED BY ORGID
    {
        $result =array();
        $q=
        '                   
            select org_id,entity_id,org_type,org_logo,is_valid,org_name,public.get_parent_org(org_id::integer) as parent_org_id
            from public.entity_org       
            where 
            org_id in ('.implode(',',$orgIdList).')
            order by org_id
        ';
        return $this->db->query($q)->result_array();
    }
    public function getCurrentInvoicePayment($invoice_id,$payment_id,$amt)
    {
        $q=
        '
        select i.invoice_id,i.custom_invoice_number,i.invoice_amount,i.invoice_paid,i.invoice_owing,i.date_issued,i.date_due,i.currency_type_id,i.invoice_number,i.invoice_comment,invoice_title
        ,p.amount as payment_amount,p.transaction_tag,p.authorization_num,p.txrefnum,p.authcode_value,p.master_entity_id,p.slave_entity_id,p.statusmsg_value statusmsg,p.orderid,p.payment_type_id,p.created_on
        ,ct.type_code as currency_type_name,ct.type_descr as currency_type_descr
        ,master.org_id  master_org_id
        ,slave.org_id   slave_org_id
        ,pt.type_descr as payment_type_name
        ,finance."getPaymentTransactionNum"(p.payment_id) as trans_num
                
        from 
        finance.invoice i
        inner join finance.payment_invoice      pi       on pi.invoice_id   = i.invoice_id
        inner join finance.payment              p        on pi.payment_id   = p.payment_id
        inner join finance.lu_currency_type     ct       on ct.type_id      = i.currency_type_id
        inner join finance.lu_payment_type      pt       on pt.type_id      = p.payment_type_id
        inner join public.entity_org            master   on master.entity_id=p.master_entity_id
        inner join public.entity_org            slave    on slave.entity_id =p.slave_entity_id
        
        where 
            i.invoice_id=?
        and pi.payment_id=?
        and not exists (select 1 from finance.payment_cancellation pc where pc.payment_id=?)
        and i.deleted_flag=false   
        and ct.deleted_flag=false
        ';
        $data               =$this->db->query($q,array($invoice_id,$payment_id,$payment_id))->result_array();
        $payment_type_id    =intval($data[0]["payment_type_id"]);
        switch($payment_type_id)
        {
            case 1:
                $fee_code='SSI';
                break;
            case 2:
                $fee_code='INT';
                break;
            case 3:
                $fee_code='CC';
                break;
            case 4:
                $fee_code='CC';
                break;
        }
            
        $q=
        '
            select fee_code,fee_rate,fee_amount,fee_min_amount,fee_max_amount ,
            finance.apply_max_fees(fee_code, ?) as tax_amt_max
            
            from finance.lu_finance_fee
            where fee_code =?
        ';
        $dataFee    =$this->db->query($q,array($amt,$fee_code))->result_array();
        
        return array_merge($data,$dataFee);
    }
    public function getCurrentDepositPayment($deposit_id,$payment_id,$amt)
    {
        $q=
        '
        select     d.*
        ,p.amount as payment_amount,p.transaction_tag,p.authorization_num,p.txrefnum,p.authcode_value,p.master_entity_id,p.slave_entity_id,p.statusmsg_value statusmsg,p.orderid,p.payment_type_id,p.created_on
        ,ct.type_code as currency_type_name,ct.type_descr as currency_type_descr
        ,master.org_id  master_org_id
        --,slave.org_id   slave_org_id
        ,pt.type_descr as payment_type_name
        ,finance."getPaymentTransactionNum"(p.payment_id) as trans_num
                
        from 
        finance.deposit_required d
        inner join finance.deposit_payment             pd       on pd.deposit_id   = d.deposit_id
        inner join finance.payment              p        on pd.payment_id   = p.payment_id
        inner join finance.lu_currency_type     ct       on ct.type_id      = d.currency_type_id
        inner join finance.lu_payment_type      pt       on pt.type_id      = p.payment_type_id
        inner join public.entity_org            master   on master.entity_id=p.master_entity_id
        --might be person or org
        --inner join public.entity_org            slave    on slave.entity_id =p.slave_entity_id
        
        where 
            d.deposit_id=?
        and pd.payment_id=?
        and not exists (select 1 from finance.payment_cancellation pc where pc.payment_id=?)
        and d.status_id=2
        and ct.deleted_flag=false
        ';
        $data               =$this->db->query($q,array($deposit_id,$payment_id,$payment_id))->result_array();
        
        $payment_type_id    =intval($data[0]["payment_type_id"]);
        switch($payment_type_id)
        {
            case 1:
                $fee_code='SSI';
                break;
            case 2:
                $fee_code='INT';
                break;
            case 3:
                $fee_code='CC';
                break;
            case 4:
                $fee_code='CC';
                break;
        }
            
        $q=
        '
            select fee_code,fee_rate,fee_amount,fee_min_amount,fee_max_amount ,
            finance.apply_max_fees(fee_code, ?) as tax_amt_max
            
            from finance.lu_finance_fee
            where fee_code =?
        ';
        $dataFee    =$this->db->query($q,array($amt,$fee_code))->result_array();
        
        return array_merge($data,$dataFee);
    }
    public function getCurrentInvoice($invoice_id)
    {
        $q=
        "
        select i.invoice_id,i.custom_invoice_number,i.invoice_amount,i.invoice_paid,i.invoice_owing,i.date_issued,i.date_due,i.currency_type_id,i.invoice_number,i.invoice_comment,invoice_title
        ,ct.type_code as currency_type_name,ct.type_descr as currency_type_descr
        ,master.entity_id   master_entity_id
        ,slave.entity_id    slave_entity_id
        ,master.org_id      master_org_id
        ,slave.org_id       slave_org_id
        ,master.org_name    master_org_name
        ,slave.org_name     slave_org_name
                
        from 
        finance.invoice i
        inner join finance.lu_currency_type     ct       on ct.type_id      = i.currency_type_id
        inner join public.entity_org            master   on master.entity_id= i.invoice_master_eid
        inner join public.entity_org            slave    on slave.entity_id = i.invoice_slave_eid
        
        where 
            i.invoice_id=?
        and i.deleted_flag=false   
        and ct.deleted_flag=false
        ";
        $data               =$this->db->query($q,array($invoice_id))->result_array();
        return $data;
    }
    public function getEntitysOrgIds($entityIdsList)        //IN COMMA-BASED FORMAT
    {
        $q=
        "   select eo.org_id 
            from public.entity_org eo
            where eo.entity_id in 
            (    
                select regexp_split_to_table(?,',')::integer
            )
        ";
        $result=$this->db->query($q,array($entityIdsList))->result_array();            
        return $result;
    }
    
    
    //*****************************************************************
    //***********************CANCELLATION******************************
    //*****************************************************************
    
    public function invoicePaymentsCancellationPossibility($invoice_id)
    {
        $result=$this->db->query("select * from finance.\"invoicePaymentsCancellationPossibility\"(?)",array($invoice_id))->result_array();
        return $result;
    }
    public function cancel_payment($payment_id,$payment_type_id,$auth_num,$trans_tag,$TxRefNum,$OrderId,$amount,$payment_on)       //Goes to branches
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------
        try 
        {                          
            $this->db->trans_begin();
            //CALCULATE CANCELLATION TYPE BASED ON ONLINE SYSTEM REMAINING TIME CHECKING   USAGE ((((CHASE_CREDIT_CARD  AND INTERNAL))))
            $cancel_type=$this->remainingTimeToEOD($payment_on);          
                    
            $result=$this->db->query("select * from finance.\"cancel_payment\"(?,?);",array($payment_id,$cancel_type))->result_array();    
            
            if ($this->db->trans_status() === FALSE)
                $this->db->trans_rollback();        
            else
            {
                if(intval($result[0]["cancel_payment"])==1)
                {
                    if(intval($payment_type_id)==2)                                   //INTERAC
                    {
                        $this->Cancel_Payment_debit_External($auth_num,$trans_tag,$amount);
                        if((string)($this->input->get_post("Transaction_Approved"))=='false')
                        {
                            $this->db->trans_rollback();        
                            return array(array('cancel_payment'=>'-1'));
                        }    
                    }
                    if(intval($payment_type_id)==3 || intval($payment_type_id)==4)  //CREDIT CARD
                    {
                        $cancel=$this->Cancel_Payment_credit_External($TxRefNum,$OrderId,$amount,$cancel_type);
                        foreach($cancel as $v)
                            if($v["tag"]=='ProcStatus')
                                if($v["value"]!='0')
                                {
                                    $this->db->trans_rollback();        
                                    return array(array('cancel_payment'=>'-1'));
                                } 
                    }     
                    //IF ALL CANCELLATION REQUEST TO CHASE AND HOSTED-CHECKOUT HAVE BEEN SUCCESSFULL THEN COMMIT SYSTEM TRANSACTIONS                                  
                    $this->db->trans_commit();
                } 
                else $this->db->trans_rollback();
                return $result;                                
            }                                       
        } 
        catch (Exception $e) 
        {
            $this->db->trans_rollback();        
            
        }
    }
    public function cancel_invoice($invoice_id)
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //------------------------------------------------------------------
        /*try 
        {                          
            $this->db->trans_begin();                                                      
            $result=$this->db->query("select * from finance.\"cancel_invoice\"(?,?,?)",array($invoice_id,$a_u_id , $a_o_id ))->result_array();
            if(intval($result[0]["cancel_invoice"])==1)
            {
                //FETCH ALL CANCELLED-STATUS INVOICE PAYMENTS
                $q="select created_on,amount,transaction_tag,authorization_num,orderid,txrefnum ,payment_type_id,p.payment_id
                    from  finance.payment p
                    inner join finance.payment_invoice pi on pi.payment_id=p.payment_id
                    where invoice_id=?
                    and     (p.payment_status_id=2 or p.payment_status_id=6)
                    ";
                    
                $payments_list=$this->db->query($q,array($invoice_id))->result_array();  
                foreach($payments_list as $v)
                {
                    $payment_id     =$v["payment_id"];
                    $payment_type_id=$v["payment_type_id"];
                    $amount         =$v["amount"];    
                    $auth_num       =$v["authorization_num"];
                    $trans_tag      =$v["transaction_tag"];
                    $txrefnum       =$v["txrefnum"];
                    $orderid        =$v["orderid"];
                    $payment_on     =$v["created_on"];
                    
                    //CALCULATE CANCELLATION TYPE BASED ON ONLINE SYSTEM REMAINING TIME CHECKING   USAGE ((((CHASE_CREDIT_CARD  AND INTERNAL))))
                    $cancel_type=$this->remainingTimeToEOD($payment_on);          
                    
                    if(intval($payment_type_id)==2)                                 //INTERAC
                        $cancel=$this->Cancel_Payment_debit_External($auth_num,$trans_tag,$amount);
                    if(intval($payment_type_id)==3 || intval($payment_type_id)==4)  //CREDIT CARD
                        $cancel=$this->Cancel_Payment_credit_External($txrefnum,$orderid,$amount,$cancel_type);
                }
                //IF ALL CANCELLATION REQUEST TO CHASE AND HOSTED-CHECKOUT HAVE BEEN SUCCESSFULL THEN COMMIT SYSTEM TRANSACTIONS
                $this->db->trans_commit();
            }
            else $this->db->trans_rollback();                                                             
            
            return $result;
        }*/ 
        try 
        {                          
            //FETCH ALL CANCELLED-STATUS INVOICE PAYMENTS
            $q=
            "
                select created_on,amount,transaction_tag,authorization_num,orderid,txrefnum ,payment_type_id,p.payment_id
                from  finance.payment p
                inner join finance.payment_invoice pi on pi.payment_id=p.payment_id
                where 
                    invoice_id=?
                and p.payment_status_id=1
            ";
                
            $payments_list=$this->db->query($q,array($invoice_id))->result_array();  
            
            foreach($payments_list as $v)
            {
                $payment_id     =$v["payment_id"];
                $payment_type_id=$v["payment_type_id"];
                $amount         =$v["amount"];    
                $auth_num       =$v["authorization_num"];
                $trans_tag      =$v["transaction_tag"];
                $txrefnum       =$v["txrefnum"];
                $orderid        =$v["orderid"];
                $payment_on     =$v["created_on"];
                
                $result=$this->cancel_payment($payment_id,$payment_type_id,$auth_num,$trans_tag,$txrefnum,$orderid,$amount,$payment_on);
                if(intval($result[0]["cancel_payment"])<0) return array(array("cancel_invoice"=>'PAYMENT-CANCELLATION# '.$result[0]["cancel_payment"]));
                        
                //IF CURRENT PAYMENT CANCELLATION WAS UNSUCCESSFULL THEN STOP PROCCEDING, BUT OTHER PAYMENTS TO THIS POINT WERE SUCCESSFUL
                //AND SOME OF INVOICE PAYMENTS HAS BEEN CANCELLED NOT THE INVOICE TOTALLY
            }
            //IF ALL CANCELLATION REQUEST TO CHASE AND HOSTED-CHECKOUT HAVE BEEN SUCCESSFULL THEN COMMIT SYSTEM TRANSACTIONS
            //[SUB]DEPOSITS WILL BE ROLLBACKED AT THIS POINT
            $result=$this->db->query("select * from finance.\"cancel_invoice\"(?,?,?)",array($invoice_id,$a_u_id , $a_o_id ))->result_array();
            return $result;
        }
        catch (Exception $e) 
        {
            echo '<pre>';
            echo var_dump($e);
            echo '</pre>';
        }
    }
    public function Cancel_Payment_debit_External($auth_num,$trans_tag,$amount)                                        //Branch 1
    {                                                               
        $client = new SoapClient("https://api.demo.e-xact.com/vplug-in/transaction/rpc-enc/service.asmx");
                                                                  
        $request->ExactID               ='AD2070-01';
        $request->Password              ='j1135_weep'; 
        
        $request->Transaction_Type      ='35';
        $request->DollarAmount          =$amount;
        $request->SurchargeAmount       ='';
        $request->Card_Number           ='';
        $request->Transaction_Tag       =$trans_tag;
        $request->Track1                ='';
        $request->Track2                ='';
        $request->PAN                   ='';
        $request->Authorization_Num     =$auth_num;
        $request->Expiry_Date           ='';
        $request->CardHoldersName       ='Test';
        $request->VerificationStr1      ='';
        $request->VerificationStr2      ='';
        $request->CVD_Presence_Ind      ='';
        $request->ZipCode               ='';
        $request->Tax1Amount            ='';
        $request->Tax1Number            ='';
        $request->Tax2Amount            ='';
        $request->Tax2Number            ='';
        $request->Secure_AuthRequired   ='';
        $request->Secure_AuthResult     ='';
        $request->Ecommerce_Flag        ='';
        $request->XID                   ='';
        $request->CAVV                  ='';
        $request->CAVV_Algorithm        ='';
        $request->Reference_No          ='';
        $request->Customer_Ref          ='';
        $request->Reference_3           ='';
        $request->Language              ='';
        $request->Client_IP             ='';
        $request->Client_Email          ='';
        $request->User_Name             ='';
                                            
        $response = $client->SendAndCommit($request);
      
    }
    public function Cancel_Payment_credit_External($TxRefNum,$OrderId,$Amount,$action)                                 //Branch 2
    {                       
        $MERCHANDID  =CHASE_MERCHANDID;
        $BIN         =CHASE_BIN;
        $INDUSTRYTYPE=CHASE_INDUSTRYTYPE;
        $TERMINALID  =CHASE_TERMINALID;
        //VOID
        if($action=='void')
            $xml = 
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <Request>                   
            <Reversal>
                <TxRefNum>{$TxRefNum}</TxRefNum>
                <AdjustedAmt>".(string)($Amount*100)."</AdjustedAmt>
                <OrderID>{$OrderId}</OrderID>
                <BIN>{$BIN}</BIN>
                <MerchantID>{$MERCHANDID}</MerchantID>
                <TerminalID>{$TERMINALID}</TerminalID>
            </Reversal>
            </Request>";
        
        //Refund
        if($action=='refund')
            $xml=
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <Request>
            <NewOrder>
            <IndustryType>{$INDUSTRYTYPE}</IndustryType>
            <MessageType>R</MessageType>
            <BIN>{$BIN}</BIN>
            <MerchantID>{$MERCHANDID}</MerchantID>
            <TerminalID>{$TERMINALID}</TerminalID>
            <OrderID>{$OrderId}</OrderID>
            <Amount>".(string)($Amount*100)."</Amount>                                          
            <TxRefNum>{$TxRefNum}</TxRefNum>
            </NewOrder>
            </Request>";
        
        $cancel=$this->curl_to_chase($xml);
        return $cancel;
    }
    
    //*****************************************************************
    //**********************PAYMENT/CANCELLATION TEST CASE*************
    //*****************************************************************
    
    public function get_paymentListStore()
    {
        $q=
        "
            select txrefnum||','||orderId||','||amount  details ,description||' '||txrefnum as description 
            from finance.payment 
            where payment_type_id between 3 and 4        
            and description like 'Test%'
        ";
        $result=$this->db->query($q)->result_array();
        return $result;
    }
    public function cancel_EXT_payment_TEST($action,$txrefnum,$orderId,$amount)
    {
        $MERCHANDID =CHASE_MERCHANDID;
        $TERMINALID =CHASE_TERMINALID;
        $BIN        =CHASE_BIN;
        //VOID
        if($action=='void')
            $xml = 
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <Request>                   
            <Reversal>
                <TxRefNum>{$txrefnum}</TxRefNum>
                <AdjustedAmt>".(string)($amount*100)."</AdjustedAmt>
                <OrderID>{$orderId}</OrderID>
                <BIN>{$BIN}</BIN>
                <MerchantID>{$MERCHANDID}</MerchantID>
                <TerminalID>{$TERMINALID}</TerminalID>
            </Reversal>
            </Request>";
        
        //Refund
        if($action=='refund')
            $xml=
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <Request>
            <NewOrder>
                <IndustryType>{$INDUSTRYTYPE}</IndustryType>
                <MessageType>R</MessageType>
                <BIN>{$BIN}</BIN>
                <MerchantID>{$MERCHANDID}</MerchantID>
                <TerminalID>{$TERMINALID}</TerminalID>
                <OrderID>{$orderId}</OrderID>
                <Amount>".(string)($amount*100)."</Amount>                                          
                <TxRefNum>{$txrefnum}</TxRefNum>
            </NewOrder>
            </Request>";
        
        $result=$this->curl_to_chase($xml);
        
        echo '<pre>';
        echo var_dump($result);
        echo '</pre>';
        
        $ProcStatus =$this->findChaseTag($result,'ProcStatus');
        if($ProcStatus==null) return null;
        
        $StatusMsg=$this->findChaseTag($result,"StatusMsg");
        if(intval($ProcStatus["value"])==0)
            return "Approved";             
        else                               
            return $ProcStatus["value"] .'     '.$StatusMsg["value"];
    }
    public function pay_invoice_direct_cc_TEST($amount,$accountNumber,$SecCode,$AVS,$country)
    {                  
        $MERCHANDID =CHASE_MERCHANDID;
        $BIN        =CHASE_BIN;
        $TERMINALID =CHASE_TERMINALID;
        
        $xml        ="
        <?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <Request> 
        <NewOrder >
            <IndustryType>{$INDUSTRYTYPE}</IndustryType>
            <MessageType>AC</MessageType>
            <BIN>{$BIN}</BIN>
            <MerchantID>{$MERCHANDID}</MerchantID>
            <TerminalID>{$TERMINALID}</TerminalID>
            <CardBrand></CardBrand>
            <AccountNum>"   .$accountNumber."</AccountNum>
            <Exp>0112</Exp>
            <CurrencyCode>124</CurrencyCode>
            <CurrencyExponent>2</CurrencyExponent> 
                       
            <CardSecVal>"   .$SecCode."</CardSecVal>
            
            <AVSzip>"       .$AVS[0]."</AVSzip>
            <AVSaddress1>"  .$AVS[1]."</AVSaddress1>
            <AVSaddress2>            </AVSaddress2>
            <AVScity>"      .$AVS[2]."</AVScity>
            <AVSstate>"     .$AVS[3]."</AVSstate>
            <AVSphoneNum>"  .$AVS[4]."</AVSphoneNum>
            <AVSname>"      .$AVS[5]."</AVSname>            
            
            <OrderID>"      .uniqid('spectrum-')."</OrderID>
            
            <Amount>"       .(string)($amount*100)."</Amount>
            
            <Comments>Test Comment</Comments>
        </NewOrder>
        </Request>
        "; 
        $ac=$this->curl_to_chase($xml);
        
        //At all situations check Index Exists
        $txRefNum   =$this->findChaseTag($ac,'TxRefNum');
        $orderId    =$this->findChaseTag($ac,'OrderID');
        
        $ProcStatusTag  =$this->findChaseTag($ac,'ProcStatus');
        if($ProcStatusTag==null) return null;
                
        if(intval($ProcStatusTag["value"])!=0)
        {
            $StatusMsg    =$this->findChaseTag($ac,'StatusMsg');
                                
            return 'StatusMsg : '.$StatusMsg["value"];
        }
        else
        {
            $RespCode           =$this->findChaseTag($ac,'RespCode');
            if($RespCode==null) return null;    
            
            if(intval($RespCode["value"])=='00')
            {
                $CVV2RespCode   =$this->findChaseTag($ac,'CVV2RespCode');
                $AVSRespCode    =$this->findChaseTag($ac,'AVSRespCode');
                $AuthCode       =$this->findChaseTag($ac,'AuthCode');
                
                $this->savePaymentTest($amount,$orderId["value"],$txRefNum["value"]);
                return
                    'TxRefNum : '
                    .$txRefNum["value"]
                    .'<br/>'
                    
                    .'OrderId: '
                    .$orderId["value"]
                    .'<br/>'
                    
                    .'RespCode : '
                    .$RespCode["value"]
                    .'<br/>'
                    
                    .'CVV2 : ['
                    .@$CVV2RespCode["value"].'] '
                    .((isset($CVV2RespCode["value"]))?$this->checkCVV(trim($CVV2RespCode["value"])) .' ('.trim($CVV2RespCode["value"]).')'    :'CVV Approved')
                    .'<br/>'
                    
                    .'AVS : ['
                    .@$AVSRespCode["value"].'] '
                    .((isset($AVSRespCode["value"]))?$this->checkAVS(trim($AVSRespCode["value"]))   .' ('.trim($AVSRespCode["value"]).')'      :'AVS Approved')
                    .'<br/>'
                    
                    .'AuthCode : ['
                    .@$AuthCode["value"].'] ';
            }   
            else
            {
                $StatusMsg      =$this->findChaseTag($ac,'StatusMsg');
                $RespMsg        =$this->findChaseTag($ac,'RespMsg');
                 
                $this->savePaymentTest($amount,$orderId["value"],$txRefNum["value"]);
                return 
                    'TxRefNum : '
                    .$txRefNum["value"]
                    .'<br/>'
                    
                    .'OrderId: '
                    .$orderId["value"]
                    .'<br/>'
                    
                    .'RespCode : '
                    .$RespCode["value"]
                    .'<br/>'
                    
                    .'StatusMsg : '
                    .((isset($StatusMsg["value"]))?$StatusMsg["value"]:'')
                    .'<br/>'
                    
                    .'CVV2 : ['
                    .@$CVV2RespCode["value"].'] '
                    .((isset($CVV2RespCode["value"]))?$this->checkCVV(trim($CVV2RespCode["value"])) .' ('.trim($CVV2RespCode["value"]).')'    :'')
                    .'<br/>'
                    
                    .'AVS : ['
                    .@$AVSRespCode["value"].'] '
                    .((isset($AVSRespCode["value"]))?$this->checkAVS(trim($AVSRespCode["value"]))   .' ('.trim($AVSRespCode["value"]).')'      :'')
                    .'<br/>'
                    
                    .'AuthCode : ['
                    .@$AuthCode["value"].'] '
                    .'<br/>'
                    
                    .'RespMsg : '
                    .((isset($RespMsg["value"]))?$RespMsg["value"]:'');
                    
            }   
        }         
               
        return $ac;
    }
    private function savePaymentTest($amount,$orderId,$txRefNum)
    {             
        $q=
        "
            insert into finance.payment
            (
                created_by,modified_by,owned_by,
                amount,payment_status_id,currency_type_id,payment_type_id,transaction_tag,authorization_num,
                cardbrand_type,cardbrand_value,message_type_type,message_type_value,orderid,txrefnum,description
            )
            values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);
        ";
        $result=$this->db->query($q
        ,array
        (
            1,1,1,
            $amount,1,1,3,'','',
            1,1,1,1,$orderId,$txRefNum,
            'Test Case '.date("Y-m-d H:i")
            )
        );
    }
    private function checkCVV($index)
    {
        $cvvRespCode= 
        array
        (
            "M"=>"Match",
            "N"=>"No Match",
            "P"=>"Not Processed",
            "S"=>"Should have been present.",
            "U"=>"Issuer unable to process request.",
            "N"=>"Decline Tran"
        );
        
        return "CVV ".$cvvRespCode[$index];
    }
    private function checkAVS($index)
    {
        $avsRespCode= 
        array
        (
            "1"  =>   "No address supplied ",
            "2"  =>   "Bill-to address did not pass Auth Host edit checks ",
            "3"  =>   "AVS not performed", 
            "4"  =>    "Issuer does not participate in AVS ",
            "R"  =>   "Issuer does not participate in AVS", 
            "5"  =>  "Edit-error - AVS data is invalid " ,
            "6"  => "System unavailable or time-out "   ,
            "7"  =>"Address information unavailable "   ,
            "8"  =>   "Transaction Ineligible for AVS "  ,
            "9"  =>   "Zip Match/Zip4 Match/Locale match " ,
            "A"  =>   "Zip Match/Zip 4 Match/Locale no match ",
            "B"  =>   "Zip Match/Zip 4 no Match/Locale match " ,
            "C"  =>   "Zip Match/Zip 4 no Match/Locale no match ",
            "D"  =>   "Zip No Match/Zip 4 Match/Locale match "    ,
            "E"  =>   "Zip No Match/Zip 4 Match/Locale no match"   ,
            "F"  =>   "Zip No Match/Zip 4 No Match/Locale match "   ,
            "G"  =>   "No match at all "                             ,
            "H"  =>   "Zip Match/Locale match "                       ,
            "J"  =>   "Issuer does not participate in Global AVS "     ,
            "JA" =>    "International street address and postal match " ,
            "JB" =>   "International street address match. Postal code not verified. ",
            "JC" =>    "International street address and postal code not verified. "  ,
            "JD" =>   "International postal code match. Street address not verified. ",
            "M1" =>   "Merchant Override Decline " ,
            "M2" =>   "Cardholder name, billing address, and postal code matches ",
            "M3" =>  "Cardholder name and billing code matches " ,
            "M4" =>   "Cardholder name and billing address match " ,
            "M5" =>   "Cardholder name incorrect, billing address and postal code match ",
            "M6" =>   "Cardholder name incorrect, billing address matches " ,
            "M7" =>   "Cardholder name incorrect, billing address matches " ,
            "M8" =>   "Cardholder name, billing address and postal code are all incorrect ",
            "N3" =>   "Address matches, ZIP not verified " , 
            "N4" =>   "Address and ZIP code not verified due to incompatible formats ",
            "N5" =>   "Address and ZIP code match (International only) " ,
            "N6" =>   "Address not verified (International only) " ,
            "N7" =>   "ZIP matches, address not verified " ,
            "N8" =>   "Address and ZIP code match (International only) ",
            "N9" =>  "Address and ZIP code match (UK only) " ,
            "R"  =>  "Issuer does not participate in AVS " ,
            "UK" =>   "Unknown "                       ,
            "X"  =>  "Zip Match/Zip 4 Match/Address Match ",
            "Z"  =>  "Zip Match/Locale no match "         ,
            ""   => "Not applicable (non-Visa)"           
        );
        
        return "AVS ".@$avsRespCode[$index];
    }
    private function findChaseTag($ac,$tag)
    {
        foreach($ac as $v)
            if($v["tag"]==$tag)
                return $v;
        return null;
    }
    
    /**
    * TODO: use chase library and CURL library
    * 
    * @param mixed $xml
    */
    public function curl_to_chase($xml)
    {   
        $CURLOPT_URL    =CHASE_URL;                                                     
        $header         = "POST /AUTHORIZE HTTP/1.0\r\n";
        $header         .= "MIME-Version: 1.0\r\n";
        $header         .= "Content-type: application/PTI46\r\n";
        $header         .= "Content-length: ".strlen($xml)."\r\n";
        $header         .= "Content-transfer-encoding: text\r\n";
        $header         .= "Request-number: 1\r\n";
        $header         .= "Document-type: Request\r\n";
        $header         .= "Interface-Version: Test 1.4\r\n";
        $header         .= "Connection: close \r\n\r\n"; 
        $header         .= $xml;
                
        $ch             = curl_init();
        curl_setopt($ch, CURLOPT_URL            , $CURLOPT_URL);
        
        curl_setopt($ch, CURLOPT_HEADER         , false); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST  , $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);                    
        //curl_setopt($curl, CURLOPT_POST           , TRUE);
        //curl_setopt($curl, CURLOPT_POSTFIELDS     , $POSTDATA);
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION , TRUE); 
        //curl_setopt($curl, CURLOPT_COOKIEFILE     , 1);
        
        
        $response       = curl_exec($ch);        
        
        if (curl_errno($ch))
            echo curl_error($ch);
        else 
            curl_close($ch);
                              
        $xml_parser           = xml_parser_create();
        xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING   ,0);
        xml_parser_set_option($xml_parser,XML_OPTION_SKIP_WHITE     ,1);
        xml_parse_into_struct($xml_parser,$response                 , $vals,$index);
        xml_parser_free($xml_parser);
        
        return $vals;
        //*****************
        /*echo '<pre>';
        echo var_dump($xml_parser);
        echo '</pre>';
        
        echo '<pre>';
        echo var_dump( $response);
        echo '</pre>';
        
        echo '<pre>';
        echo var_dump($vals);
        echo '</pre>';
        */
    }
    
    
    
    
    //EMAILING
    /**
    * Sends an invoice email to the recipient of the invoice
    * 
    * @param string $email invoice recipient email
    * @param array $invoice array of invoice details
    * @param array $items array of items on the invoice
    * @param array $totals subtotal, taxes, total
    * @param mixed $to array of to person/org - or an entity id that we'll generate that information from
    * @param mixed $from array of to person/org - or an entity id that we'll generate that information from
    * @param bool $test false sends email, true changes recipient to test and outputs debug information
    */
    private function emailInvoice($email, $invoice, $items, $totals, $to, $from, $test=false)
    {
        $data = array(
            'subject'   => 'Invoice #'.$invoice['number'],
            'invoice'   => $invoice,
            'items'     => $items,
            'totals'    => $totals,
            'to'        => $to,
            'from'      => $from
        );
        $body = $this->load->view('emails/invoice', $data, true);
        
        // create and send email
        $this->load->library('email');
        $this->email->to( $email );
        $this->email->subject($data['subject']);
        $this->email->message($body);
        $this->email->send($test);
    }
    
    /**
    * put your comment there...
    * 
    * @param array $email notification recipients
    * @param array $payment payment information
    * @param array $invoice invoice information (if available)
    * @param array $gateway gateway information (if available)
    * @param array $to payment made to
    * @param array $from payment made by
    * @param bool $test false sends email, true changes recipient to test and outputs debug information
    */
    private function emailPaymentNotification($email,$invoice, $payment, $gateway, $to, $from, $test=false,$postdata=null)
    {
        $data = array(
            'subject'    => 'Payment Notification',
            'payment'    => $payment,
            'invoice'    => $invoice,
            'gateway'    => $gateway,
            'to'         => $to,
            'from'       => $from
        );
        
        
        $body = $this->load->view('emails/paymentNotification', $data, true);
        
        // create and send email
        $this->load->library('email');
        $this->email->to( $email );
        $this->email->subject($data['subject']);
        $this->email->message($body);
        $this->email->send($test);
        
                                                         
    }
    /**
    * This email will send a payment receipt for all payments that
    * can be made through the system.
    * 
    * @param mixed $email email address to send receipt to
    * @param array $items array of item arrays holding item name, qty, price and total cost
    * @param array $totals subtotal, tax, and total of all items
    * @param array $gateway all information provided in return from payment gateway
    * @param mixed $to name, address of recipient - if number assume entity_id and retrieve information from database
    * @param mixed $from name, address of charging party in transaction - if number assume entity_id and retrieve information from database (default is 1-SSI)
    */
    private function emailPaymentReceipt($email, $items, $totals, $gateway, $to, $from=1, $test=false)
    {
        
        $data = array(
            'subject'   => 'Spectrum Payment Receipt',
            'items'     => $items,
            'totals'    => $totals,
            'to'        => $to,
            'from'      => $from,
            'gateway'   => $gateway
        );
        $body = $this->load->view('emails/paymentReceipt', $data, true);
        
        $this->load->library('email');
        $this->email->to($email);
        $this->email->subject($data['subject']);
        $this->email->message($body);
        $this->email->send($test);
                                                         
    }
    
    //EMAILING END********************************
    
    /**
    * get all WITHDRAW transactions that happened
    * on or after DATE
    * used to create EFT file
    * also gets bank account and org name for each
    * ONLY WITH status lu_eft_status 
    * also inner join with eft_item , which was created at the same time
    * 1==pending
    * @author sam
    * @param mixed $date  
    */
    public function get_withdraw_unprocessed_recent($date)
    {
    	//4 is withdraw
    	$params = func_get_args(); 
		$sql = "SELECT 
			w.id AS withdraw_id
			, w.bankaccount_id,
			w.amount,
			w.description,
			w.fees,
			w.total,
			w.isactive,
			w.eft_status_id
			,b.entity_id
			,b.name
			,b.bankname
			,b.institution
			,b.transit
			,b.account
			,o.org_name
			,o.org_id
			,o.org_type
			,ei.remarks1 AS csv
			,ei.eft_item_id
			,ei.entity_id
			
			FROM finance.withdraw w 
			INNER JOIN finance.bankaccount b
			ON w.bankaccount_id = b.id  AND w.deleted_flag = FALSE AND b.deleted_flag = FALSE AND b.is_enabled = true 
			INNER JOIN public.entity e ON e.entity_id = b.entity_id 
			INNER JOIN finance.eft_items ei ON ei.withdraw_id = w.id AND ei.deleted_flag = FALSE
			LEFT OUTER JOIN public.entity_org o ON o.entity_id=e.entity_id  
			WHERE w.created_on > ? 
			AND w.eft_status_id = 1 AND ei.eft_id IS NULL ";//status 1 is 'pending' . is null means not put in a valid file yet
		
		$wth = $this->db->query($sql,$params)->result_array();
		//echo $this->db->last_query();
		
		return $wth;
    }
    
	 /**
	 * set this withdraw to the given status
	 * result text and code are given by the external api (like beanstream)
	 * they can be null
	 * 
	 * @param mixed $wid
	 * @param mixed $status
	 * @param mixed $rtext
	 * @param mixed $rcode
	 */
    public function update_withdraw_status($wid,$status,$rtext,$rcode)
    {
    	$params = func_get_args();
		$sql = "SELECT finance.update_withdraw_status(?,?,?,?)";
		return $this->db->query($sql,$params)->first_row()->update_withdraw_status;
    }
 
    /**
    * create a record for an eft file
    * header and footer can be null, the otehrs cannot
    * 
    * @param mixed $user
    * @param mixed $owner
    * @param mixed $filename
    * @param mixed $header
    * @param mixed $footer
    */
    public function insert_eft($user,$owner,$filename,$header,$footer) 
    {
    	$params = func_get_args();
    	$sql='SELECT finance.insert_eft(?,?,?,?,?)';
		return $this->db->query($sql,$params)->first_row()->insert_eft;
    }
    /**
    * update an eft record. batch id can be null or empty string
    * to flag that it should nto chnage or doesnt exist
    * status must be from finance.lu_eft_status
    * 
    * @param mixed $eftid
    * @param mixed $status
    */
    public function update_eft($eftid,$status)
    {
    	$params=func_get_args();
    	$sql='SELECT finance.update_eft(?,?)';
		return $this->db->query($sql,$params)->first_row()->update_eft;
    }
    
    /**
    * assign this eft_item record  (representing one remote transaction) 
    * to the eft record (representing one physical file to be sent)
    * 
    * @param mixed $eft_item_id
    * @param mixed $eft_id
    */
    public function update_eft_item_file($eft_id,$eft_item_id)
    { 
    	$params = func_get_args();
    	$sql='SELECT finance.update_eft_item_file(?,?)';
		return $this->db->query($sql,$params)->first_row()->update_eft_item_file;
    }
    
    public function update_eft_response($eftid,$code,$message,$batchid)
    {
    	$params = func_get_args();
    	$sql='SELECT finance.update_eft_response(?,?,?,?)';
		return $this->db->query($sql,$params)->first_row()->update_eft_response;
    }
    
    /**
    * insert row of eft file
    * eft_id CAN be null, but nothing else should be 
    * 
    * @param mixed $user
    * @param mixed $owner
    * @param mixed $eft_id
    * @param mixed $withdraw_id
    * @param mixed $csv
    * @param mixed $entity_id
    */
    public function insert_eft_item($user,$owner,$eft_id,$withdraw_id,$csv,$entity_id)
    {
    	$params = func_get_args();
    	$sql='SELECT finance.insert_eft_item(?,?,?,?,?,?)';
		return $this->db->query($sql,$params)->first_row()->insert_eft_item;
    }
    
    
    
    /**
    * get all eft batch_ids by status
    * default is status=2 : processed
    * which means sent up, but not reported yet
    * return a flat array of ids, not an array of records
    * return in Increasing order
    * 
    * @param mixed $status
    */
    public function get_eft_batchids($status=2)
    {
		$sql="SELECT batch_id FROM finance.eft WHERE  eft_status_id = ? AND eft.deleted_flag = FALSE ORDER BY batch_id ASC";
		$batchids = $this->db->query($sql,array($status))->result_array();
		$batch_ids_plain = array();
		foreach($batchids as $db_row)
		{
			$batch_ids_plain[] = (int)$db_row['batch_id'];
		}
		return $batch_ids_plain;
    }
    
    /**
    * given a batch id ,find out which eft file this is attached to 
    * 
    * @param mixed $batch_id
    */
    public function get_eftid_from_batchid($batch_id)
    {
		$sql = "SELECT eft_id FROM finance.eft WHERE batch_id = ?";
		$r = $this->db->query($sql,array($batch_id))->result_array();
		$eft = isset($r[0]) ? $r[0]['eft_id'] : null;//return only the id not the entire db result
		return $eft;		
    }
    
    
    public function get_eftitemid_from_withdrawid($w_id)
    {
		$sql = "SELECT eft_item_id FROM finance.eft_items WHERE withdraw_id = ?";
		$r = $this->db->query($sql,array($w_id))->result_array();
		$eft = isset($r[0]) ? $r[0]['eft_item_id'] : null;//return only the id not the entire db result
		return $eft;		
    }
    public function get_eft()
    {
		$sql="SELECT batch_id,eft_id,eft_status_id , batch_id, response_code, response_message  ,lu.status_name , file_name
				FROM finance.eft 		
				INNER JOIN finance.lu_eft_status lu ON lu.status_id = eft_status_id
				AND    eft.deleted_flag = FALSE 
				ORDER BY eft_id DESC";
		return $this->db->query($sql)->result_array();
		
    }
    
    /**
    * get eft by status
    * 
    * from lu_eft_status
    * 1 pending (file created, waiting for upload)
    * 2 processed (upload confirmed but no response gathered)
    * 3 cancelled (upload rejected)
    * 4 incomplete (something failed during batch/file creation before upload)
    * 
    * @param mixed $status
    */
    public function get_eft_by_status($status)
    {
		$sql = "SELECT batch_id,eft_id,eft_status_id , batch_id, response_code, response_message  ,lu.status_name , file_name
				FROM finance.eft 		
				INNER JOIN finance.lu_eft_status lu ON lu.status_id = eft_status_id AND eft_status_id = ?
				AND    eft.deleted_flag = FALSE ORDER BY eft_id ASC"; //order ascending so smaller numbers (older) first
		return $this->db->query($sql,$status)->result_array();
    }
    
    
    public function get_eft_items()
    {
		$sql="SELECT eft_id , eft_item_id , remarks1 , withdraw_id ,  entity_id 
				,w.amount  , w.eft_status_id , lu.status_name ,
				w.result_code AS w_result_code ,
				w.result_text AS w_result_text  
				FROM finance.eft_items  		i
			 	INNER JOIN finance.withdraw w ON w.id = i.withdraw_id 
				INNER JOIN finance.lu_eft_status lu ON lu.status_id = eft_status_id
				WHERE    i.deleted_flag = FALSE";
		return $this->db->query($sql)->result_array();
		
    }
    
    
    /**
    * get details on this motion assuming 
    * it is a withdraw motion, so
    * also get bank a
    * 
    * @param mixed $motion_id
    */
    public function get_motion_withdraw_details($motion_id)
    {
    	$params = func_get_args();
		$sql = "SELECT m.motion_id, 
			m.motion_type_id, 
			m.motion_status_id ,
			m.description,
			mw.withdraw_id, 
			w.amount, 
			w.fees ,
			w.total,  
			w.bankaccount_id, 
			b.entity_id  ,
			b.currency_type_id
			FROM finance.motion m 
			INNER JOIN finance.motion_withdraw mw ON m.motion_id = mw.motion_id AND m.motion_id = ? 
			INNER JOIN finance.withdraw w ON w.id = mw.withdraw_id 
			INNER JOIN finance.bankaccount b ON b.id = w.bankaccount_id";
		$r = $this->db->query($sql,$params)->result_array();
		$r = (count($r)) ? $r[0] : null;
 
		return $r;
    }
    
    
    public function get_entity_wallet_balance($entity,$currency)
    {
    	$params = func_get_args();
		$sql = "SELECT finance.get_entity_wallet_balance(?,?)";
		return $this->db->query($sql,$params)->first_row()->get_entity_wallet_balance;
    }
    
    
    
    
    
    
    
    /**
    * if a withdraw motion is approved, BUT 
    * there is not enough money, then handle that
    * issue right now
    * 
    * @param mixed $motion_id
    */
    public function handle_approved_withdraw_motion($motion_id,$user,$org)
	{
		//step 1: get motion, including its approved state
		$m = $this->get_motion_withdraw_details($motion_id);
		
		$type   = $m['motion_type_id'];
		$wid    = $m['withdraw_id'];
		$status = $m['motion_status_id'];
		$entity = $m['entity_id'];
		$curr   = $m['currency_type_id'];
		$wdesc  = $m['description'];
		$amount = $m['amount'];
		
		$TYPE_WITHDRAW = 2;
		$STATUS_WITHDRAW = 2;
		
		//type 2 is withdraw: status 2 is APPROVED
		//step 2: if motion is not type 2 (withdraw) , OR if motion is NOT approved, stop
 
		if($type != 2 || $status != 2) {return;}
		
		
		$CANCELLED = 3; //from finance.lu_eft_status
		
		//step 3: get_entity_wallet_balance . if balance < withdraw.amount , reject 
		$balance = $this->get_entity_wallet_balance($entity,$curr); 
		
		//IF 
		if($balance < $amount)
		{ 
			//set the withdraw status to cancelled
			$this->update_withdraw_status($wid,$CANCELLED,"Insufficient wallet funds",-1);
			//but do not cancell the motion, its approval is based ONLY on votes
			
		}
		else if (  $amount > BEANSTREAM_BATCH_MAX)
		{
			
			$this->update_withdraw_status($wid,$CANCELLED,"Exceeds Maximum per transaction limit of \$ ".BEANSTREAM_BATCH_MAX,-2);
		}
		else
		{
			//there IS enough, therefore create eft item AND do transactions right away
			 
			//echo "ok so apply item nad transacts";
			$w = $this->get_withdraw($wid);
			
			//make the line item data for the eft file, dont create the file yet
			$this->load->library('beanstream');			
			$this->beanstream->set_withdraw_index('withdraw_id');	
 
			$csv = $this->beanstream->make_eft_item_data($w['amount'],$w['institution'],$w['transit'],$w['account'],$w['withdraw_id'],$w['name']);
			
			//create the transactions for thiswithdraw 
			$eft_item_id = $this->insert_eft_item($user,$org,null,$wid,$csv,$w['entity_id']);			
			$this->make_eft($eft_item_id,$entity,$amount,$wdesc,$curr);			 
		} 
	}
    
    /**
    * the name of this function is a bit misleading, it
    * actually just creates the transactions FOR one single eft_item 
    * and adds to eft_transaction link table
    * 
    * it does not create any records in the eft or eft_item tables
    * but thisneeds to be called for every eft_item made 
    * 
    * 
    * amt should be the base amount the user puts in do NOT add fees
    * 
    * @author Sam (php side)
    * @author Ryan (sql stored procedure)
    * 
    * @param mixed $eftitemid
    * @param mixed $entityid
    * @param mixed $amt
    * @param mixed $desc
    * @param mixed $curr_type_id
    */
    public function make_eft($eftitemid , $entityid , $amt , $desc , $curr_type_id)
    {
    	$params = func_get_args();
		$sql = "SELECT finance.make_eft(?,?,?,?,?)";
		return $this->db->query($sql,$params)->first_row()->make_eft;
    }
    
    
    
    /**
    * this reverses the transactions attached to this eft_item record
    * 
    * @author Sam (php side)
    * @author Ryan (sql stored procedure)
    * 
    * @param mixed $eftitemid
    */
    public function make_eft_reverse($eftitemid)
    {
    	$params = func_get_args();
		$sql = "SELECT finance.make_eft_reverse(?)";
		return $this->db->query($sql,$params)->first_row()->make_eft_reverse;
    }
    
    /**
    * apply fees to entities based on this eft item id
    * 
    * @param mixed $eftitemid
    */
    public function make_eft_apply_fees($eftitemid)
    {
    	$params = func_get_args();
		$sql = "SELECT finance.make_eft_apply_fees(?)";
		return $this->db->query($sql,$params)->first_row()->make_eft_apply_fees;
    }
    
}                    
