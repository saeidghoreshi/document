<?php                                 
require_once('./endeavor/models/endeavor_model.php');  
class Contentgallery_model  extends Endeavor_model
{
    
    public function get_photoGalleries($target_status_id,$orgId)
    {
        
        $q  =
        "
                select 
                cah.acc_id,cah.content_action_id as  content_action_id,ca.type_name as content_action_name
                ,cah.content_status_id,s.type_name as content_status_name
                ,cah.user_id,cah.org_id,cah.description as action_history_description
                ,cah.datetime

                ,c.content_id as id,c.content_name,c.file_name,c.owned_by,c.description as content_description
                ,ct.type_id as content_type_id , ct.type_name as content_type_name
                ,eo.entity_id
                        
                from gallery.content c
                inner join gallery.view_content_last_action     vcla    on  vcla.content_id   =c.content_id
                inner join gallery.content_action_history       cah     on  vcla.id           =cah.id
                                
                inner join gallery.lu_content_type              ct      on  ct.type_id        =c.content_type_id
                inner join public.entity_org                    eo      on  eo.org_id         =c.owned_by

                inner join gallery.lu_action_status             s       on  s.type_id         =cah.content_status_id
                inner join gallery.lu_content_action            ca      on  ca.type_id        =cah.content_action_id
                
                where true
                
                        
        ";
        if($target_status_id!=-1)
            $q.=" and cah.content_status_id=".$target_status_id;
            
        /* ALL LASTACTIONSTATUS=SENDTOSSI AND DEADLINE PASSED */
        if($orgId==1)
            //$q.=" and cah.content_action_id=8 and current_timestamp - cah.datetime > interval '24 hour'";
            $q.=" and cah.content_action_id=8 ";
            
        /* ALL RECORD OWNED BY ORG AND LASTACTIONSTATUS != SENDTOSSI*/
        if($orgId!=1)
            $q.=" and c.owned_by=".$orgId." and cah.content_action_id!=8";
            
        $q.=" order by cah.datetime desc";
            
        return $this->db->query($q)->result_array();  
    }
    public function get_content_actions_history($content_id)
    {
        $q=
        "
            select content_action_name,content_status_name 
            , case when ep.person_fname is null then '' else ep.person_fname end as person_fname
            , case when ep.person_lname is null then '' else ep.person_lname end as person_lname
            
            , eo.org_id,eo.org_name ,h.datetime , case when acc.type_name is null then '' else '('||acc.type_name||')' end as acc_type_name
            from gallery.view_contents_actions_history h

            inner join permissions.user         u   on u.user_id=h.user_id
            left  join public.entity_person     ep  on ep.person_id=u.person_id

            inner join public.entity_org        eo  on eo.org_id=h.org_id
            left join gallery.lu_acc                         acc on acc.type_id=h.acc_id
                  
            where content_id=?
            order by h.id desc
        ";    
        return $this->db->query($q,array($content_id))->result_array();
    }
    public function upload_photo($file)
    {
        //UPOAD FILE TO PHYSICAL BRANCH AND SAVE RECORD IN DATABASE
        //IF NO FTP ACCOUNT SPECIFIED THEN LOCAL FTP WILL BE THE DEFAULT
        $this->load->library('ftp');                               
                            
        //UPLOAD NEW FILE
        list($width, $height, $type)  = getimagesize($file["tmp_name"]);
        $extension       = explode('/',$file["type"]);
        $newFileName     = uniqid().'.'.$extension[1];
        
        $imgCopy1  =$file["tmp_name"];
        $imgCopy2  =$file["tmp_name"];
        
        //SAVE AN ACTIVE FILE               
        $path="uploaded/content-gallery/photo/active/".$newFileName;
        $this->ftp->upload($imgCopy1, $path);
        
        //SAVE A THUMBNAIL FILE 
        $tmp_path="uploaded/content-gallery/photo/thumbnail/thumb_".$newFileName;
        $this->ftp->upload($imgCopy2, $tmp_path);
        
        
        $this->ftp->close();
        
        $this->load->library('images'); 
        $oImage =$this->images->path_to_image($tmp_path);
        $thumb  =$this->images->crop($oImage,20,20);
                            
        
        
        return $newFileName;
    }
    public function get_action_status()
    {   
        return $this->db->query("select * from gallery.lu_action_status")->result_array();
    }
    public function get_acc_types()
    {
        return $this->db->query("select type_id,type_name from gallery.lu_acc acc")->result_array();
    }
    public function create_content($entityId,$orgId,$content_name,$content_description,$newFileName)
    {                           
        return $this->db->query('select * from "gallery"."create_content"(?,?,?,?,?)',array($content_name,'1',$newFileName,$orgId,$content_description))->result_array();
    }
    public function create_content_action_ticket($content_id,$acc_id,$content_action_id,$content_status_id,$user_id,$org_id,$description )
    {
        return $this->db->query("select * from gallery.create_content_action_ticket(?,?,?,?,?,?,?);",array($content_id,$acc_id,$content_action_id,$content_status_id,$user_id,$org_id,$description))->result_array();
    }
    public function check_content_reported_as_xxx_before($content_id)
    {
        return $this->db->query("select 1 from gallery.view_contents_actions_history where content_id=? and acc_id=1",array($content_id))->result_array();
    }
    
    
    public function emailToWebsiteModeratorsRoleUsers($orgId)
    {
        $websiteModeratorRoleId=7;
        $q=
        "
            select distinct cm.value as email
            from permissions.user u
            inner join permissions.assignment a on a.user_id=u.user_id
            inner join permissions.lu_role r on r.role_id=a.role_id
            inner join public.entity_person ep on ep.person_id=u.person_id
            left join public.entity_contact ec on ec.entity_id=ep.entity_id
            left join public.contact_method cm on cm.contact_method_id=ec.contact_method_id

            where org_id    =?
            and r.role_id   =?
            and ec.is_active=true
            and a.isactive  =true
            and ec.contact_method_id in (1,5)
        ";
        return $this->db->query($q,array($orgId,$websiteModeratorRoleId))->result_array();        
    }
    
    
}