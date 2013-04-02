<?php

class Contentgallery extends Controller
{
    public $dc;
    public function __construct()
    {
        parent::Controller();           
        $this->load->model('contentgallery_model');
        $this->load->model('permissions_model');
        
        $this->load->library('page');
        $this->load->library('input');
        $this->load->library('result');
    }
    
    private function load_window()
    {
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/");
        $this->window->set_js_path("/assets/js/");
    }
    
    public function window_manage_photoGallery($id=false)
    {       
        $this->load_window();                                 
        $this->window->add_css('');
                
        
        //COMPONENTS
        $this->window->add_js('components/contentGallery/photoGallery/components/spectrumviewport.viewBase1.js');
        $this->window->add_js('components/contentGallery/photoGallery/components/spectrumviewport.viewBase2.js');
        $this->window->add_js('components/contentGallery/photoGallery/components/spectrumviewport.viewBase3.js');
        
        //depreciated
        //$this->window->add_js('components/contentGallery/photoGallery/components/spectrumgrids.photoGallery.js');
                                                                    
        //FORMS AND WINDOWS
        $this->window->add_js('components/contentGallery/photoGallery/forms/forms.js');
        $this->window->add_js('components/contentGallery/photoGallery/windows/spectrumwindow.photoGallery.js');
        
        //CONTROLLER
        $this->window->add_js('components/contentGallery/photoGallery/controller.js'); 
        $this->window->add_js('components/contentGallery/photoGallery/toolbar.js');
        
        
        $this->window->set_header('Manage Photo Gallery');
        $this->window->set_body($this->load->view('contentGallery/photoGallery/photoGallery.main.php',null,true));
        $this->window->json();
    }
    
    public function json_get_photoGalleries()
    {     
        $orgId              = $this->permissions_model->get_active_org();
        $target_status_id   =$this->input->get_post("target_status_id");
                                    
        if($target_status_id==false)$target_status_id=-1;
        
        
        $result             =$this->contentgallery_model->get_photoGalleries($target_status_id,$orgId);
        foreach($result as $i=>$v)
        {   
            $result[$i]["header"]       = '<b>'.$result[$i]["content_name"].'</b>';
            $result[$i]["footer"]       = '<i><small>'.$result[$i]["content_status_name"].'</small></i>';
            $result[$i]["url"]          = "uploaded/content-gallery/photo/active/{$v['file_name']}";
            $result[$i]["thumbnail"]    = "uploaded/content-gallery/photo/thumbnail/thumb_{$v['file_name']}";
            
            //$result[$i]["content_description"]  ="{$v["content_name"]}<br/>{$v["content_description"]}";
            //$result[$i]["file_name"]    ="<img width='100% height='100%' src={$url} />";
        }
        
        switch($target_status_id)
        {
            case(1):
                $groupName="Pending";
            break;
            case(2):
                $groupName="Paused";    
            break;
            case(3):
                $groupName="Played";    
            break;
            case(4):
                $groupName="Deleted";    
            break;
            case(5):
                $groupName="Rejected";    
            break;               
            default:
                $groupName="All";                
        }
        
        $data=array
        (
            "groupname" =>$groupName,
            "result"     =>$result
        );                        
        echo json_encode($data);
    }
    
    public function json_get_content_actions_history()
    {
        $content_id =$this->input->get_post("content_id");
        $result = $this->contentgallery_model->get_content_actions_history($content_id);
        
        foreach($result as $i=>$v)
        {
            $result[$i]["datetime"]             = date('Y-m-d h:i:s',strtotime($result[$i]["datetime"]));
            $result[$i]["content_action_name"]  = $result[$i]["content_action_name"].' '. $result[$i]["acc_type_name"];
        }
        
            
        
        echo json_encode($result);
    }
    
    public function json_upload_photo()
    {
       $file                = $_FILES["photogallery_upload"];
       $content_name        = $this->input->get_post("photogallery_name");
       $content_description = $this->input->get_post("photogallery_description");
       
       $entityId            = $this->permissions_model->get_active_entity();
       $orgId               = $this->permissions_model->get_active_org();
       $userId              = $this->permissions_model->get_active_user();
       
       $newFileName         = $this->contentgallery_model->upload_photo($file);
       
       //CREATE CONTENT 
       $newContent          =$this->contentgallery_model->create_content($entityId,$orgId,$content_name,$content_description,$newFileName);
       
       //TAKE AN ACTION
       $content_id          =$newContent[0]["create_content"];
       $this->contentgallery_model->create_content_action_ticket($content_id , null , 1/*UPLOAD*/, 1/*PENDING*/ , $userId  , $orgId , $content_description);
       
       $this->result->success(1); 
    }
    
    public function json_get_action_status()
    {
        $result         =$this->contentgallery_model->get_action_status();
        $this->result->json_pag($result); 
    }
    
    public function json_get_acc_types()
    {                                                
        $content_id     =$this->input->get_post("content_id");
        $result         = $this->contentgallery_model->get_acc_types();
        
        
        $check          =$this->contentgallery_model->check_content_reported_as_xxx_before($content_id);
        
        if(count($check)>=1)
            $result[0]["type_name"].=' (Already Used)';
        
                            
        $this->result->json_pag_store($result); 
    }
    
    //ACTIONS
    public function json_delete_content()
    {        
        $content_id     =$this->input->get_post("content_id");
        $file_name      =$this->input->get_post("file_name");
        
        $orgId          = $this->permissions_model->get_active_org();
        $userId         = $this->permissions_model->get_active_user();
        
        //TAKE AN ACTION
        $this->contentgallery_model->create_content_action_ticket($content_id , null , 6/*DELETE*/, 4/*DELETED*/ , $userId  , $orgId , "Delete Content");
        
        //ALSO MOVE FILES TO INACTIVE DIRECTORY
        $this->load->library('ftp');                                  
        $paths="uploaded/content-gallery/photo/active/".$file_name;
        $patht="uploaded/content-gallery/photo/inactive/".$file_name;
        rename($paths, $patht);
        $this->ftp->close();
        
        $this->result->success(1); 
    }
    
    public function json_approve_content()
    {
        $orgId               = $this->permissions_model->get_active_org();
        $userId              = $this->permissions_model->get_active_user();
        
        $content_id         =$this->input->get_post("content_id");                       
        
        //TAKE ACTION
        $this->contentgallery_model->create_content_action_ticket($content_id , null , 2/*APPROVE*/, 1/*PENDING*/ , $userId , $orgId , 'Approved');
        
        if($orgId==1)
            $this->ssi_approve_content($content_id);                
        else
            $this->playorpause_content($content_id);
            
        $this->result->success(1); 
    }
    
    public function json_playorpause_content()
    {
        $content_id         =$this->input->get_post("content_id");                       
        $playorpause        =$this->input->get_post("playorpause");
        
        $playorpauseStat    =(($playorpause==0)?2/*Paused*/:3/*Played*/);
        $playorpauseAction  =(($this->input->get_post("playorpause")==0)?3/*Pause*/:4/*Play*/);
        $playorpauseDescr   =(($this->input->get_post("playorpause")==0)?"Pause":"Play");
        
        $orgId               = $this->permissions_model->get_active_org();
        $userId              = $this->permissions_model->get_active_user();
        
        //TAKE AN ACTION
        $this->playorpause_content($content_id);
        $this->result->success(1); 
    }
    
    public function playorpause_content($content_id)
    {
        $playorpause        =$this->input->get_post("playorpause");
        
        $playorpauseStat    =(($playorpause==0)?2/*Paused*/:3/*Played*/);
        $playorpauseAction  =(($this->input->get_post("playorpause")==0)?3/*Pause*/:4/*Play*/);
        $playorpauseDescr   =(($this->input->get_post("playorpause")==0)?"Pause":"Play");
        
        $orgId               = $this->permissions_model->get_active_org();
        $userId              = $this->permissions_model->get_active_user();
        
        //TAKE AN ACTION
        $this->contentgallery_model->create_content_action_ticket($content_id , null , $playorpauseAction/*PLAY OR PAUSE*/, $playorpauseStat/*PLAYED/PAUSED*/ , $userId , $orgId , $playorpauseDescr);
    }
    
    public function ssi_approve_content($content_id)
    {
        $orgId               = $this->permissions_model->get_active_org();
        $userId              = $this->permissions_model->get_active_user();
        
        //TAKE AN ACTION
        $this->contentgallery_model->create_content_action_ticket($content_id , 2 /*#2 ACC*/ , 7/*SEMD TO ORG*/, 1/*PENDING*/ , $userId , $orgId , 'SSI Approved 2');
    }
    
    public function json_reject_content()
    {
        $content_id     =$this->input->get_post("content_id");
        
        $orgId          = $this->permissions_model->get_active_org();
        $userId         = $this->permissions_model->get_active_user();
        
        //TAKE AN ACTION
        $this->contentgallery_model->create_content_action_ticket($content_id , null , 5/*REJECT*/, 5/*REJECTED*/ , $userId , $orgId , "Reject Content");
       
        //CHECK ORG TYPE IF SSI THEN TAKE ANOTHER DELETE ACTION AND NITIFY ORGS AND APPLY CHARGES
        if($orgId==1)
        {
            //DELETE ACTION BY SSI
            $this->contentgallery_model->create_content_action_ticket($content_id , null , 6/*Delete*/, 4/*Deleted*/ , $userId , $orgId , "Delete Content");
            
            //SEND NOTIFICATION TO ORG
            $contentInfo   =$this->contentgallery_model->get_content_actions_history($content_id);
            $emails        =$this->contentgallery_model->emailToWebsiteModeratorsRoleUsers($contentInfo[0]["org_id"]);
            foreach($emails as $v)
            {
                $to      = $v["email"];
                $subject = 'SSI REJECTED AND DELETED YOUR CONTENT WITH ID #'.$content_id;
                $message = 'SSI REJECTED AND DELETED YOUR CONTENT WITH ID #'.$content_id;
                $headers = 'From: noreply@servilliansolutionsinc.com' . "\r\n" .'X-Mailer: PHP/' . phpversion();
                mail($to, $subject, $message, $headers);
            }
            
            //APPLY FEES
            
        }
       
        $this->result->success(1); 
    }
    
    public function json_report_content()
    {
        $content_id     =$this->input->get_post("content_id");
        $acc_type_id    =$this->input->get_post("acc_type_id");
        
        //IF ALREADY FILED FOR #1 XXX
        $check          =$this->contentgallery_model->check_content_reported_as_xxx_before($content_id);
        
                         
        if($acc_type_id==1 && count($check)>=1)
        {
            $this->result->success(-1); 
            return;
        }
            
        
        
        if($acc_type_id==1)
            $action_type_id=8;
        else 
            $action_type_id=7;
        
        $orgId               = $this->permissions_model->get_active_org();
        $userId              = $this->permissions_model->get_active_user();
        
        //TAKE AN ACTION
        $this->contentgallery_model->create_content_action_ticket($content_id , $acc_type_id , $action_type_id/*SEND TO SSI OR ORG*/, 1/*PENDING*/ , $userId , $orgId , 'Flag Content');
                                          
        if($action_type_id==7/*SEND TO ORG*/)
        {
            //SEND NOTIFICATION TO ORG
            $emails=$this->contentgallery_model->emailToWebsiteModeratorsRoleUsers($orgId);
            foreach($emails as $v)
            {
                $to      = $v["email"];
                $subject = 'NEW REPORT WITH ID #'.$content_id;
                $message = 'NEW REPORT AND DELETED YOUR CONTENT WITH ID #'.$content_id;
                $headers = 'From: noreply@servilliansolutionsinc.com' . "\r\n" .'X-Mailer: PHP/' . phpversion();
                mail($to, $subject, $message, $headers);
            }
        }
        
        $this->result->success(1); 
    } 
              
}

