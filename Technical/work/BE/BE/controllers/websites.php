<?php
 require_once('endeavor.php');   
  class Websites extends Endeavor
  {
  	  
  	  	 /**
  	  * 
  	  * 
  	  * @var websites_model
  	  */
  	  public $websites_model;  
  	  
  	  
    function __construct()
    {
        parent::Controller();
        $this->load->model('endeavor_model');
        $this->load->model('websites_model');
        $this->load->model('associations_model');
        $this->load->model('permissions_model');
        $this->load->model('leagues_model');
                               
        
        $this->load->library('page');
        $this->load->library('input');   
        $this->load->library('result');   
    }
    private function load_window()
    {
        $this->load->library('window');
        $this->window->set_css_path("/assets/css/inventory/");
        $this->window->set_js_path("/assets/js/");   
    }
    //Registration  guidelines
    public function window_registration_guidelines()
    {
        $this->load_window();
        $this->window->add_css('');
        $this->window->add_js('class.websites.registrationguidelines.js');
        $this->window->set_header('Manage Registration Guidlines');
        $this->window->set_body($this->load->view('websites/websites.registrationguidelines.main.php',null,true));
        $this->window->set_footer($this->load->view('websites/websites.registrationguidelines.footer.php',null,true));
        //if($id) $this->window->set_id($id);
        $this->window->json();        
    }
    public function json_get_registration_guidelines()
    {
        $result=$this->websites_model->get_registration_guidelines();
        $this->result->json($result);
    }
    public function json_save_registration_guidelines()
    {
        $col=$this->input->get_post("col");
        $content=$this->input->get_post("content");
        
        $result=$this->websites_model->save_registration_guidelines($col,$content);
        $this->result->json($result);        
    }
    
    //Link Ordering
    public function window_linkordering()
    {        
        $this->load_window();
        $this->window->add_css('');
        $this->window->add_js('class.websites.linkordering.js');
        $this->window->set_header('Manage Link Ordering');
        $this->window->set_body($this->load->view('websites/websites.linkordering.php',null,true));
        $this->window->set_footer($this->load->view('websites/websites.linkordering.footer.php',null,true));
        //if($id) $this->window->set_id($id);
        $this->window->json();    
    }
    public function json_get_org_links_root()
    {       
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------                                    
        $result=$this->websites_model->get_org_links_root($a_o_id);
        $this->result->json($result);
    }
    public function json_get_org_links_sub($token/*Garbeg*/,$root_link_id)
    {
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------                                    
        $result=$this->websites_model->get_org_links_sub($a_o_id,$root_link_id);
        $this->result->json($result);
    }
    public function json_save_links_order()
    {
        $link_ids=$this->input->get_post("link_ids");
        $orders=$this->input->get_post("orders");
        $visibles=$this->input->get_post("visibles");
        
        $result=$this->websites_model->save_links_order($link_ids,$orders,$visibles);
        $this->result->json($result);
    }
    //Create websites Dynamic Content and Meny Links
    public function window_createdynamiccontent()
    {
        $data["dynamiccontent_topmenu"]= $this->build_root_menu_ds();
        
        $this->load_window();
        $this->window->add_css('');
        $this->window->add_js('class.websites.dynamiccontent.js');
        $this->window->set_header('Manage Dynamic Content');
        $this->window->set_body($this->load->view('websites/websites.dynamiccontent.php',$data,true));
        $this->window->set_footer($this->load->view('websites/websites.dynamiccontent.footer.php',null,true));
        //if($id) $this->window->set_id($id);
        $this->window->json();    
    }
    public function build_root_menu_ds()
    {
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------------------------------------
        
        $result=$this->websites_model->get_org_links_root($a_o_id);     
        $ds='<select id="dynamiccontent-rootmenu-combo">';
        foreach($result as $v)
            $ds.="<option value=\"{$v["link_id"]}\" />{$v["link_name"]}";
        $ds.='</select>';
        return $ds;
    }
    public function json_save_dynamiccontent()
    {
        $rootid=$this->input->get_post("rootid");
        $content=$this->input->get_post("content"); 
        $linkname=$this->input->get_post("linkname"); 
        $this->websites_model->save_dynamiccontent($rootid,$content,$linkname);
    }
    //Manage Advertising   ************************************************************************************************************ 
    
    //Campaign Banner
    public function window_manageadvertising($id=false)
    {   
        //--------------------------------------------------
        $a_o_id= $this->permissions_model->get_active_org();
        $a_u_id= $this->permissions_model->get_active_user();
        $this->load_window();
        
        $this->window->add_js('components/advertising/grids/spectrumgrids.client.js');
        $this->window->add_js('components/advertising/grids/spectrumgrids.limits.js');
        $this->window->add_js('components/advertising/grids/spectrumgrids.banner.js');
        $this->window->add_js('components/advertising/grids/spectrumtreeviews.campaign.js');
        
        $this->window->add_js('components/advertising/windows/spectrumwindow.advertising.js');                                                           
        $this->window->add_js('components/advertising/forms/forms.js');                                                   
        $this->window->add_js('components/advertising/controller.js');
        $this->window->add_js('components/advertising/toolbar.js');
         
        $this->window->set_header('Advertisements');
        $this->window->set_body($this->load->view('websites/websites.advertisement.php',null,true));
        $this->window->json();
    }
    public function json_get_campaignbanners()
    {                    
        $result=$this->websites_model->get_campaignbanners();
        
        $cur=array();
        $temp_roots=array();
        $temp_children=array();
        $data=array();
        foreach($result as $i=>$v)
        {
               if($v["campaign_id"]!=$cur)
               {
                   $cur=$v["campaign_id"];
                   
                   if($temp_roots!=null)$data[]=array_merge($temp_roots,array("children"=> $temp_children)) ;
                   $temp_children=null;                                                                      
                   if($v["banner_name"]!=null)
                   {
                        $temp_roots=array(
                            "campaign_banner_id"=>$v["campaign_banner_id"]
                            ,"campaign_id"=>$v["campaign_id"]
                            ,"campaign_name"=>$v["campaign_name"]
                            ,"start_date"=>($v["start_date"]!=null)?date('Y/m/d',strtotime($v["start_date"])):null
                            ,"end_date"=>($v["end_date"]!=null)?date('Y/m/d',strtotime($v["end_date"])):null
                            ,"iconCls"=> ($v["campaign_paused"]=='t')?"bullet_red":"bullet_green"
                            
                            //,"banner_id"=>$v["banner_id"]
                            //,"banner_name"=>$v["banner_name"]
                            ,"client_org_id"=>$v["client_org_id"]
                            ,"banner_size_id"=>$v["banner_size_id"]
                            ,"clickurl"     =>$v["clickurl"]
                            ,"banner_script"=>base64_decode($v["banner_script"])
                            ,"banner_filename"=>$v["banner_filename"]
                            
                            ,"paused"=>$v["campaign_paused"]
                            );
                        $temp_children[]=array(
                            "campaign_banner_id"=>$v["campaign_banner_id"]
                            ,"campaign_id"=>$v["campaign_id"]
                            //,"campaign_name"=>$v["banner_name"]
                            ,"start_date"=>($v["start_date"]!=null)?date('Y/m/d',strtotime($v["start_date"])):null
                            ,"end_date"=>($v["end_date"]!=null)?date('Y/m/d',strtotime($v["end_date"])):null
                            ,"iconCls"=> ($v["paused"]=='t')?"bullet_red":"bullet_green"
                            
                            ,"banner_id"=>$v["banner_id"]
                            ,"banner_name"=>$v["banner_name"]
                            ,"client_org_id"=>$v["client_org_id"]
                            ,"banner_size_id"=>$v["banner_size_id"]
                            ,"clickurl"=>$v["clickurl"]
                            ,"banner_script"=>base64_decode($v["banner_script"])
                            ,"banner_type_id"=>$v["banner_type_id"]
                            ,"banner_type_name"=>$v["banner_type_name"]
                            ,"banner_filename"=>$v["banner_filename"]
                            
                            ,"views"=>$v["views"]
                            ,"clicks"=>$v["clicks"]
                            
                            ,"size_w"=>$v["size_w"]
                            ,"size_h"=>$v["size_h"]
                            
                            
                            ,"paused"=>$v["paused"]
                            ,'leaf'=>true
                            );
                   }
                   else
                        $temp_roots=array(
                            "campaign_banner_id"=>$v["campaign_banner_id"]
                            ,"campaign_id"=>$v["campaign_id"]
                            ,"campaign_name"=>$v["campaign_name"]
                            ,"start_date"=>($v["start_date"]!=null)?date('Y/m/d',strtotime($v["start_date"])):null
                            ,"end_date"=>($v["end_date"]!=null)?date('Y/m/d',strtotime($v["end_date"])):null
                            ,"iconCls"=> ($v["campaign_paused"]=='t')?"bullet_red":"bullet_green"
                            
                            //,"banner_id"=>$v["banner_id"]
                            //,"banner_name"=>$v["banner_name"]
                            ,"client_org_id"=>$v["client_org_id"]
                            ,"banner_size_id"=>$v["banner_size_id"]
                            ,"clickurl"=>$v["clickurl"]
                            ,"banner_script"=>base64_decode($v["banner_script"])
                            ,"banner_filename"=>$v["banner_filename"]
                            
                            ,"paused"=>$v["campaign_paused"]
                            ,'leaf'=>true);
               }
               else
                    $temp_children[]=array(
                            "campaign_banner_id"=>$v["campaign_banner_id"]
                            ,"campaign_id"=>$v["campaign_id"]
                            //,"campaign_name"=>$v["campaign_name"]
                            ,"start_date"=>($v["start_date"]!=null)?date('Y/m/d',strtotime($v["start_date"])):null
                            ,"end_date"=>($v["end_date"]!=null)?date('Y/m/d',strtotime($v["end_date"])):null
                            ,"iconCls"=> ($v["paused"]=='t')?"bullet_red":"bullet_green"
                            
                            ,"banner_id"=>$v["banner_id"]
                            ,"banner_name"=>$v["banner_name"]
                            ,"client_org_id"=>$v["client_org_id"]
                            ,"banner_size_id"=>$v["banner_size_id"]
                            ,"clickurl"=>$v["clickurl"]
                            ,"banner_script"=>base64_decode($v["banner_script"])
                            ,"banner_type_id"=>$v["banner_type_id"]
                            ,"banner_type_name"=>$v["banner_type_name"]
                            ,"banner_filename"=>$v["banner_filename"]
                            
                            ,"views"=>$v["views"]
                            ,"clicks"=>$v["clicks"]
                            
                            ,"size_w"=>$v["size_w"]
                            ,"size_h"=>$v["size_h"]
                            
                            ,"paused"=>$v["paused"]
                            ,'leaf'=>true);
               
        }    
        
        
        $data[]=array_merge($temp_roots,array("children"=> $temp_children)) ;
        $final["children"]= ($data);
        
        $this->result->json($final) ; 
    }
    public function json_get_campaignsandbanners()
    {                    
        $result=$this->websites_model->get_campaignbanners();
        $this->result->json_pag($result);        
    }
    public function json_get_campaign_banners_by_campid()
    {
        $campaign_id=$this->input->get_post("campaign_id");                     
        $result=$this->websites_model->get_campaign_banners_by_campid($campaign_id);
        $this->result->json_pag($result);        
    }
    
    //Banners
    public function json_get_org_banners()
    {
        $result=$this->websites_model->get_org_banners();    
        foreach($result as $i=>$v)    
        {                 
            $result[$i]["banner_script"]=base64_decode($v['banner_script']);
        }
        $this->result->json_pag($result);        
    } 
    
    //Campaign
    public function json_new_campaign()
    {
        $campaign_name=$this->input->get_post("campaign_name");
        $start_date=$this->input->get_post("start_date");
        $end_date=$this->input->get_post("end_date");
        $result=$this->websites_model->new_campaign($campaign_name,$start_date,$end_date);
        $this->result->success(1);
    }
    public function json_update_campaign()
    {
        $campaign_id    =$this->input->get_post("campaign_id");
        $campaign_name  =$this->input->get_post("campaign_name");
        $start_date     =$this->input->get_post("start_date");
        $end_date       =$this->input->get_post("end_date");
        $result         =$this->websites_model->update_campaign($campaign_id,$campaign_name,$start_date,$end_date);
        
        $this->result->success(1);
    }
    public function json_delete_campaign()
    {
        $campaign_id=$this->input->get_post("campaign_id");
        $result=$this->websites_model->delete_campaign($campaign_id);
        $this->result->success($result[0]["delete_campaign"]);
    }
    public function json_pause_play_campaign()
    {
        $pause_or_play=$this->input->get_post("pause_or_play");
        $campaign_id=$this->input->get_post("campaign_id");
        $result=$this->websites_model->pause_or_play_campaign($campaign_id,$pause_or_play);
        $this->result->success($result[0]["pause_or_play_campaign"]);
    }
    public function json_pause_play_campaignbanner()
    {
        $cb_id          =$this->input->get_post("cb_id");
        $pause_or_play  =$this->input->get_post("pause_or_play");
        $result=$this->websites_model->pause_play_campaignbanner($cb_id,$pause_or_play);
        $this->result->json($result[0]["pause_play_campaignbanner"]);
    }
    public function json_get_clients()
    {
        $result=$this->websites_model->get_clients();
        $this->result->json_pag($result);
    }
    public function json_get_clients_2()
    {
    	
        $result=$this->websites_model->get_clients($_REQUEST["with_allclient"]);
        
        if(isset($_REQUEST["with_allclient"]))                          
            if($_REQUEST["with_allclient"]=='true')
            {
                $result[]=array("co_org_id"=>"-1","co_org_name"=>"All");
                sort($result);
            }
        $this->result->json_pag_store($result);
    }
    public function json_get_positions()
    {
        $result=$this->websites_model->get_positions();
        $this->result->json_pag_store($result);    
    }
    public function json_get_clients_assignedto_campaigns()
    {   
        $campaign_id=$this->input->get_post("campaign_id");                                              
        $result=$this->websites_model->get_clients_assignedto_campaigns($campaign_id);
        
        if(isset($_REQUEST["with_allclient"]))                          
            if($_REQUEST["with_allclient"]=='true')
                $result[]=array("co_org_id"=>"-1","co_org_name"=>"All");
                
        $this->result->json_pag_store($result);
    }
    public function json_get_sizes()
    {
        $this->result->json($this->websites_model->get_sizes());
    }
    public function json_get_sizes_2()
    {
        $result=$this->websites_model->get_sizes();
         $this->result->json_pag_store($result);
    }
    public function json_new_banner()
    {
    	
        $banner_name=$this->input->get_post("banner_name");        
        $client_list=$this->input->get_post("client_list");
        $file_id = "file_upload";//the name of the field in the form 
        //SB: updates for task 1543
        
        if($this->input->get_post('script_file')!="1")
        {
        	//then an image was given, not a script
        	
        	$this->load->library('images');
            $file    =$_FILES[$file_id];
            //SB: updates for task 1638
            
            $size_list  =$this->input->get_post("size_list");
            $clickurl   =$this->input->get_post("clickurl");        
            
            
            list($width, $height, $type, $attr) =$this->images->get_image_size($file_id); 
            //$file_name_array                    =explode(".",$this->images->get_name($file_id));
            $extension                          =$this->images->get_extension($file_id);
            $changed_file_name                  ='B-'.date("Y-m-d-G-i-s").".".$extension;
            
            
            if($extension=="swf" || $extension=="flv")$mime="flash";
            if($extension=="js")$mime="script";
            else
            {
				//make sure its a valid image file
				if(!$this->images->type_is_valid_image($file_id))
	            {
					$this->result->success('-2');//error code for INVALID IMAGE FILE
					return;
            	}	
            	else  $mime="image";   	
			}
             
                       
            $result=$this->websites_model->new_banner($client_list,$banner_name,$changed_file_name,$mime,'',$size_list,$width, $height,$clickurl);
            $result=$result[0]["new_banner"];  
            if($result!="-1")
			{
				$this->load->library('ftp');
                $this->ftp->upload($this->images->tmp_name($file_id), "uploaded/banner-assets/".$changed_file_name);
            }                        
        }
        else
        {
            $script       =base64_encode($this->input->get_post("script"));
            
            $result=$this->websites_model->new_banner_only_script($client_list,$banner_name,$script);
            $result=$result[0]["new_banner_only_script"];  
        }
        
         $this->result->success($result);
    } 
    /**
    * TODO;
    * json_new_banner and this function should really be the same function. there is too much copy and paste between them.
    * 
    * Just have one function, and if banner_id exists: must be update. otherwise create new.  All the validatoin logic and everything else 
    * is exactly the same
    * 
    */
    public function json_update_banner()
    {
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------
        if(isset($_REQUEST["size_list"]))$size_list  =$this->input->get_post("size_list");
        if(isset($_FILES["file_upload"]))$file       =$_FILES["file_upload"];
        
        $banner_id  =$this->input->get_post("banner_id");        
        $banner_name=$this->input->get_post("banner_name");        
        $client_list=$this->input->get_post("client_list");
        
        $result=null;
        if($_REQUEST["script_file"]!="1")
        {
            $clickurl   =$_REQUEST["clickurl"];
            if(isset($_FILES["file_upload"])==true)
            {
                list($width, $height, $type, $attr) = getimagesize($file["tmp_name"]);    
                
                $file_name_array=explode(".",$file["name"]);
                $extension=$file_name_array[count($file_name_array)-1];
                $changed_file_name='B-'.date("Y-m-d-G-i-s").".".$extension;
                
                if($extension=="swf" || $extension=="flv")$mime="flash";
                if($extension=="js")$mime="script";
                else $mime="image";    
                //database
                $result=$this->websites_model->update_banner_withfilereplace($banner_id,$client_list,$banner_name,$changed_file_name,$mime,'',$size_list,$width, $height,$clickurl);
                $result=$result[0]["update_banner_withfilereplace"];  
                if($result!="-1")
                {
                    //upload 
					
					$this->load->library('ftp');
                    $this->ftp->upload($file["tmp_name"], "uploaded/banner-assets/".$changed_file_name);
                }                        
            }
            else //No need to replace banner file
            {
                $result=$this->websites_model->update_banner_withoutfilereplace($banner_id,$client_list,$banner_name,'',$clickurl);
                $result=$result[0]["update_banner_withoutfilereplace"];
            }     
        }
        else
        {
            $script =base64_encode($this->input->get_post("script"));
            $result=$this->websites_model->update_banner_only_script($banner_id,$client_list,$banner_name,$script);
            $result=$result[0]["update_banner_only_script"];
                                           
        }
        
        $this->result->success($result);   
    }
    public function json_delete_banner()
    {
        $banner_id  =$this->input->post("banner_id");
        
        $result=$this->websites_model->delete_banner($banner_id);
        $this->result->success($result[0]["delete_banner"]);
    }
    public function json_assign_banner_campaign_pos()
    {
        $campaign_id    =$this->input->get_post("campaign_id");
        $banner_id      =$this->input->get_post("banner_id");
        $banner_size_id =$this->input->get_post("banner_size_id");
        $pos_id         =$this->input->get_post("pos_id");
        $max_views      =$this->input->get_post("max_views");
        $max_clicks     =$this->input->get_post("max_clicks");
        
        //2147483647 is max integer number in postgres
        if($max_views   ==''    || $max_views   =='Maximum' || intval($max_views)   >2147483646)$max_views=2147483646;
        if($max_clicks  ==''    || $max_clicks  =='Maximum' || intval($max_clicks)  >2147483646)$max_clicks=2147483646;
        
        $result=$this->websites_model->assign_banner_campaign_pos($campaign_id,$banner_id,$pos_id,$banner_size_id,$max_clicks,$max_views);
        $this->result->success($result[0]["assign_banner_campaign_pos"]);
    }
    public function json_get_limits_selected_link()
    {
        $cb_id=$this->input->get_post("cb_id");
        $result=$this->websites_model->get_limits_selected_link($cb_id);
        $this->result->json($result);
    }
    //*****************************************************  LIMITS QUERIES 
    public function json_get_all_associations_forlimits()
    {
        $cb_id=$this->input->get_post("cb_id");        
        $result=$this->websites_model->get_all_associations_forlimits($cb_id);
        $this->result->json_pag($result);            
    }
    public function json_get_all_countries_forlimits()
    {
        $cb_id=$this->input->get_post("cb_id");      
        $result=$this->websites_model->get_all_countries_forlimits($cb_id);
        $this->result->json_pag($result);
    }
    public function json_get_all_regions_forlimits()
    {
        $cb_id=$this->input->get_post("cb_id");
        $country_id =$this->input->get_post("id");
        $result     =$this->websites_model->get_all_regions_forlimits($cb_id,$country_id);
        $this->result->json_pag($result);
    }
    public function json_get_all_cities_forlimits()
    {
        $cb_id=$this->input->get_post("cb_id");
        $region_id =$this->input->get_post("id");
         
        $result=$this->websites_model->get_all_cities_forlimits($cb_id,$region_id);
        $this->result->json_pag($result);
    }
    public function json_get_all_leagues_forlimits()
    {
        $cb_id=$this->input->get_post("cb_id");
        $city_id=$this->input->get_post("id");
        $result=$this->websites_model->get_all_leagues_forlimits($cb_id,$city_id);
        $this->result->json_pag($result);
    }
     public function json_assign_associations_to_cb()
    {
        $ids=$this->input->get_post("ids");
        $cb_id=$this->input->get_post("cb_id");
        
        $result=$this->websites_model->assign_associations_to_cb($cb_id,$ids);
        $this->result->json($result[0]["assign_associations_to_cb"]);
    }
    public function json_assign_countries_to_cb()
    {
        $ids=$this->input->get_post("ids");
        $cb_id=$this->input->get_post("cb_id");
        
        $result=$this->websites_model->assign_countries_to_cb($cb_id,$ids);
        $this->result->json($result[0]["assign_countries_to_cb"]);
    }
    public function json_assign_regions_to_cb()
    {
        $ids=$this->input->get_post("ids");
        $cb_id=$this->input->get_post("cb_id");
        
        $result=$this->websites_model->assign_regions_to_cb($cb_id,$ids);
        $this->result->json($result[0]["assign_regions_to_cb"]);
    }
    public function json_assign_cities_to_cb()
    {
        $ids=$this->input->get_post("ids");
        $cb_id=$this->input->get_post("cb_id");
        
        $result=$this->websites_model->assign_cities_to_cb($cb_id,$ids);
        $this->result->json($result[0]["assign_cities_to_cb"]);
    }
    public function json_assign_leagues_to_cb()
    {
        $ids=$this->input->get_post("ids");
        $cb_id=$this->input->get_post("cb_id");
        $result=$this->websites_model->assign_leagues_to_cb($cb_id,$ids);
        $this->result->json($result[0]["assign_leagues_to_cb"]);
    }
    
    //Clients
    public function json_new_client()
    {                                                                                             
        $client_name    =$this->input->post("client_name");
        $fname          =$this->input->post("first_name");
        $lname          =$this->input->post("last_name");
        $area_code      =$this->input->post("area_code");
        $phone_first3   =$this->input->post("phone_first3");
        $phone_last4    =$this->input->post("phone_last4");
        $email          =$this->input->post("client_email");
        //first make sure that max length is satisfied
        
        //this error checking can be done with regex on client side,but do it here also.
        if(strlen($area_code)>3)//3 is normal, 
        {
			$area_code=(int)substr($area_code,0,3);
        }
        if(strlen($phone_first3)>3)//3 is normal, 
        {
			$phone_first3=(int)substr($phone_first3,0,3);
        }
        if(strlen($phone_last4)>4)//
        {
			$phone_last4=(int)substr($phone_last4,0,4);
        }
        $phone          =$area_code.'-'.$phone_first3.'-'.$phone_last4;
        
        $res=$this->websites_model->new_client($client_name ,$fname , $lname , $phone , $email );
 
        $this->result->success($res[0]["new_client"]);
    }
    public function json_update_client()
    {
        $client_org_id  =$this->input->post("co_org_id");
        $person_id      =$this->input->post("person_id");
        
        $client_name    =$this->input->post("client_name");
        $fname          =$this->input->post("first_name");
        $lname          =$this->input->post("last_name");
        $area_code      =$this->input->post("area_code");
        $phone_first3   =$this->input->post("phone_first3");
        $phone_last4    =$this->input->post("phone_last4");
        $email          =$this->input->post("client_email");
        
        $phone          =$area_code.'-'.$phone_first3.'-'.$phone_last4;
        
        $result=$this->websites_model->update_client($client_org_id,$person_id,$client_name ,$fname , $lname , $phone , $email );
        $this->result->success($result[0]["update_client"]);
    }
    public function json_delete_client()
    {
        $client_org_id  =$this->input->post("co_org_id");
        
        $result=$this->websites_model->delete_client($client_org_id);
        $this->result->success($result[0]["delete_client"]);
    }
    
    
    //Manage publishing   *******************************************************************************************************
    public function window_managepublishing()
    {                        
               
        $this->load_window();
        
        
        
        $this->window->add_js('components/publishing/grids/spectrumgrids.article.js');
        $this->window->add_js('components/publishing/grids/spectrumtreeviews.link.js');
        $this->window->add_js('components/publishing/grids/spectrumdataviews.template.js');
        $this->window->add_js('components/publishing/grids/spectrumdataviews.imagePicker.js');
        $this->window->add_js('components/publishing/grids/spectrumgrids.module.js');
        $this->window->add_js('components/publishing/grids/spectrumgrids.moduleOpts.js');
        $this->window->add_js('components/publishing/grids/spectrumdataviews.module.js');
        //new module asset grid, with a data model and form
        $this->window->add_js('models/webasset.js');
        $this->window->add_js('components/publishing/grids/module_assets.js');
        $this->window->add_js('components/publishing/forms/module_assets.js');
        $this->window->add_js('components/publishing/windows/module_assets.js');
        
		//CSS for images module view
        $this->window->set_css_path("/assets/css/prizes/");
		$this->window->add_css('DataView.css');
        
        $this->window->add_js('components/publishing/windows/spectrumwindow.publishing.js');                                                           
        $this->window->add_js('components/publishing/forms/forms.js');
        $this->window->add_js('components/publishing/forms/link.js');
        $this->window->add_js('components/publishing/windows/link.js');
        
        $this->window->add_js('components/publishing/controller.js');
        $this->window->add_js('components/publishing/toolbar.js');
         
        $this->window->set_header('Publishing');
        $this->window->set_body($this->load->view('websites/websites.publishing.php',null,true));
        $this->window->json();
    }
    public function window_quickpost()
    {                        
        $a_u_id= $this->permissions_model->get_active_user();
        $a_o_id= $this->permissions_model->get_active_org();
        //--------------------------------------------------                 
        $this->window->add_js('components/publishing/grids/spectrumgrids.article.js');
        $this->window->add_js('components/publishing/grids/spectrumtreeviews.link.js');
        $this->window->add_js('components/publishing/grids/spectrumdataviews.template.js');
        $this->window->add_js('components/publishing/grids/spectrumdataviews.imagePicker.js');
        $this->window->add_js('components/publishing/grids/spectrumgrids.module.js');
        $this->window->add_js('components/publishing/grids/spectrumgrids.moduleOpts.js');
        $this->window->add_js('components/publishing/grids/spectrumdataviews.module.js');
        
        $this->window->add_js('components/publishing/forms/forms.js');                                      
        $this->window->add_js('components/publishing/controller.js'); 
        $this->window->add_js('components/publishing/toolbar.js');
        
         
        $this->window->set_header('Quick Post');
        $this->window->set_body($this->load->view('websites/websites.publishing.php',null,true));
        $this->window->json();
    }
    public function json_get_articles()
    {
        $result=$this->websites_model->get_articles();   
        foreach($result as $i=>$v)
        {
            $result[$i]["created_on_display"]       =($result[$i]["created_on"]!=null)?date('Y/m/d',strtotime($v['created_on'])):null;
            $result[$i]["modified_on_display"]      =($result[$i]["modified_on"]!=null)?date('Y/m/d',strtotime($v['modified_on'])):null;
            $result[$i]["publish_date_display"]     =($result[$i]["publish_date"]!=null)?date('Y/m/d',strtotime($v['publish_date'])):null;
            $result[$i]["unpublish_date_display"]   =($result[$i]["unpublish_date"]!=null)?date('Y/m/d',strtotime($v['unpublish_date'])):null;
            
            //$result[$i]["article_content"]          =base64_decode($result[$i]["article_content"]);
        } 
        $this->result->json_pag($result); 
    }                              
    public function json_new_article()                  
    {
        if(isset($_REQUEST["autolink"]))$auto_link ='true';
        else $auto_link='false';
        
        $article_title      =$this->input->get_post('article_title');
        $publish_date       =$this->input->get_post('publish_date');
        $unpublish_date     =$this->input->get_post('unpublish_date');
        if(!$unpublish_date) 
            $unpublish_date=null;
            
        $article_type_id    =$this->input->get_post('article_type_id');
        $article_intro      =$this->input->get_post('article_intro');
        //$article_content    =base64_encode($this->input->get_post('article_content_revised'));
        $article_content    =$this->input->get_post('article_content_revised');
        
        $result = $this->websites_model->new_article($article_title,$publish_date,$unpublish_date,$article_type_id,$article_intro,$article_content,$auto_link);
        $this->result->success($result[0]["new_article"]);
    }
    /**
    * @author Ryan
    * modified nov 16 2011 (SB)
    * default to todays date if publish date is null, also 
    * parse empty string to 'null' for UN-publish date. 
    * changed to timestamp format of Y-m-d for postgres
    * 
    */
    public function json_update_article()
    {
        $article_id         =$this->input->get_post('article_id');
        $article_title      =$this->input->get_post('article_title');
        $publish_date       =$this->input->get_post('publish_date');
        
        if(!$publish_date) 
            $publish_date=date('Y-m-d');
        else               
            $publish_date=date('Y-m-d',strtotime($publish_date));
            
        $unpublish_date     =$this->input->get_post('unpublish_date');
        if(!$unpublish_date) 
            $unpublish_date=null;
        else                 
            $unpublish_date=date('Y-m-d',strtotime($unpublish_date));
            
        $article_type_id    =$this->input->get_post('article_type_id');
        $article_intro      =$this->input->get_post('article_intro');
        //$article_content    =base64_encode($this->input->get_post('article_content_revised'));   	 
        $article_content    =$this->input->get_post('article_content_revised');        
         
        $result = $this->websites_model->update_article($article_id,$article_title,$publish_date,$unpublish_date,$article_type_id,$article_intro,$article_content);
        $this->result->success($result[0]["update_article"]);
    }
    /**
    * this is broken, it causes database errors, so i stopped using (SB) as ot nov 16 2011
    * instead I just use "json_update_article" , it does the same thing anyway
    * @deprecated true
    * 
    * 
    */
    public function json_update_article_row_edit()
    {
        $article_id         =$this->input->get_post('article_id');
        $article_title      =$this->input->get_post('article_title');
        $publish_date       =$this->input->get_post('publish_date');
        $unpublish_date     =$this->input->get_post('unpublish_date');
        $article_type_name  =$this->input->get_post("article_type_name");
        
        $result = $this->websites_model->update_article_row_edit($article_id,$article_title,$publish_date,$unpublish_date,$article_type_name);
        $this->result->success($result[0]["update_article_row_edit"]);
    }
    public function json_delete_article()
    {
        $article_id         =$this->input->get_post('article_id');
        $result = $this->websites_model->delete_article($article_id);
        $this->result->success($result[0]["delete_article"]);
    }
    public function json_publish_or_unpublish_article()
    {
        $article_id         =$this->input->get_post('article_id');
        $result = $this->websites_model->publish_or_unpublish_article($article_id);
        $this->result->success($result[0]["publish_or_unpublish_article"]);    
    }
    public function json_upload_article_image()
    {
        $file_id = "newfile";//the name of the field in the form 
       // 
        
        //SB: updates for task 1545 : fixed to allow only images files. not .php or .js or xls or others.
        //now uses image library and upgraded this-> upload function
 
        $this->load->library('images');
       if($this->images->type_is_valid_image($file_id))
       {     
       	   $file=$_FILES["newfile"];
	        $date=date("Y-m-d-G-i-s");           
	        $img_filename =             $date.'-'.$file["name"];                                           
	        $result=$this->websites_model->upload_article_image($img_filename);
	        
	        
	        $this->load->library('ftp');  
	        $this->ftp->upload($file['tmp_name'],"uploaded/article-assets/files/large/".$img_filename);
	        $this->result->success($result[0]["upload_article_image"].'::'.$img_filename); 
	    }  
	    else
	    {
			$this->result->failure('Invalid file type, this is not an image file.'); 
	    }
    }  
    public function json_get_article_types()
    {                  
        $org_type_id=$this->permissions_model->get_active_org_type(); 
        
        $data=array();
        if($org_type_id==2 || $org_type_id==1/*SSI or Association*/)      
            $data[]=array("id"=>"1","name"=>"Announcements");
            
        if($org_type_id==3/*League*/)      
        {
            $data[]=array("id"=>"1","name"=>"Announcements");
            $data[]=array("id"=>"2","name"=>"Front Page Announcements");
            $data[]=array("id"=>"3","name"=>"Custom Page");  
        }  
        $this->result->json_pag_store($data);
    } 
    public function json_get_articleImages()
    {
        $result=$this->websites_model->get_articleImages();   
        $this->result->json_pag($result);
    } 
    //links
    public function json_get_links()
    {
        $a_o_id         = $this->permissions_model->get_active_org();
        $org_type_id    = $this->permissions_model->get_active_org_type(); 
        $link_type_id   = $this->input->get_post('link_type_id');
        
        $target         =array();
        $relation_array =array();
        
        $this->source=$this->websites_model->get_links($link_type_id);
        if($this->source == null)
        	$this->source=array();//parse to empty, avoids php errors 
        else
        	$this->source=$this->A_Sort($this->source,'order');
        foreach($this->source as $i=>$v)
            $relation_array[$i]=array($v["link_parent_id"],$v["link_id"]);
            
        foreach($this->source as $i=>$v)
        {
            if ($v["link_parent_id"]==null)
            {                   
                if($v["isactive"]=='f')
                    $target[]=array_merge($v,array('iconCls'=>"bullet_red","leaf"=>false,"expanded"=>true));
                else
                    $target[]=array_merge($v,array('iconCls'=>"bullet_green","leaf"=>false,"expanded"=>true));
                    
                $this->Rec($target[count($target)-1]);
            }                                                  
        }    
        $this->result->json(array("root"=>"","children"=>$target,"source"=>$relation_array,"org_type"=>$org_type_id,"org_id"=>$a_o_id)) ;
    }
    private function A_Sort($a,$subkey) 
    {
        foreach($a as $k=>$v) $b[$k] = strtolower($v[$subkey]);
        asort($b);
        foreach($b as $key=>$val) $c[] = $a[$key];
        return $c;
    }
    public $source=array();             
    public function Rec(&$parent)
    {           
        $has_child=false;             
        foreach($this->source as $i=>$v)
        if($v["link_parent_id"]==$parent["link_id"])
        {
                $has_child=true;
                if($v["isactive"]=='f')
                    $parent["children"][]=array_merge($v,array('iconCls'=>"bullet_red","leaf"=>false,"expanded"=>true));
                else
                    $parent["children"][]=array_merge($v,array('iconCls'=>"bullet_green","leaf"=>false,"expanded"=>true));
                                    
                $ret_has_child=$this->Rec($parent["children"][count($parent["children"])-1]);
                
                //collapse ones with no child
                if($ret_has_child==false)$parent["children"][count($parent["children"])-1]["expanded"]=false;
        }       
        
        if($has_child==false)//make a fake rec for leaf node
        {          
            $parent["children"][]=array("title"=>'');
            $parent["children"][0]["leaf"]=true;
            $parent["children"][0]["iconCls"]='transparent';
        }    
        return $has_child;                       
    }
    public function json_update_links_ordering()
    {
        $link_parent_combo  =$this->input->get_post("link_parent_combo");
                            
        $result = $this->websites_model->update_links_ordering($link_parent_combo);
        $this->result->success($result[0]["update_links_ordering"]);
    }
    public function json_new_link()
    {
 	    $title        =$this->input->get_post("title");
        $type_id      =$this->input->get_post("type_id");
        
        $article_id   =$this->input->get_post("article_id");
        $url          =$this->input->get_post("url");
        
        if(!$article_id)$article_id ='';
        if(!$url)       $url        ='';
         
        $result = $this->websites_model->new_link($title ,$type_id , $url ,$article_id ,'true');
        $this->result->success($result[0]["new_link"]);
    }
    public function json_update_link()
    {
		$article_id     =$this->input->get_post("article_id");
		$url            =$this->input->get_post("url");
         
        $title        =$this->input->get_post("title");
        $type_id      =$this->input->get_post("type_id");
        $link_id      =$this->input->get_post("link_id");
        
        if(!$article_id)$article_id ='';
        if(!$url)       $url        ='';
         
        $result = $this->websites_model->update_link($link_id,$title ,$type_id , $url ,$article_id ,'true');
       $this->result->success($result[0]["update_link"]);        
    }
    public function json_delete_link()
    {
        $link_id      =(int)$this->input->get_post("link_id");
        
        $result = $this->websites_model->delete_link($link_id);
        $this->result->success($result[0]["delete_link"]);                
    }
    public function json_get_paged_articles()
    {
        $result = $this->websites_model->get_paged_articles();
        $this->result->json_pag_store($result);  
    }
    public function json_hide_or_unhide_link()
    {
        $link_id    =$this->input->get_post("link_id");
        
        $result     = $this->websites_model->hide_or_unhide_link($link_id);
        
      $this->result->success($result[0]["hide_or_unhide_link"]);
    }
    public function json_get_linkTypes()
    {                         
        $result     = $this->websites_model->get_linkTypes();
        $this->result->json_pag_store($result);
    }                         
    //templates
    public function json_get_org_template_list()
    {
        $result = $this->websites_model->get_org_template_list();
        $this->result->json_pag_store($result);      
    }
    public function json_get_templates()
    {
        $result=$this->websites_model->get_templates();   
        foreach($result as $i=>$v)
        {
            $result[$i]["created_on_display"]       =($result[$i]["created_on"]!=null)?date('Y/m/d',strtotime($v['created_on'])):null;
            $result[$i]["modified_on_display"]      =($result[$i]["modified_on"]!=null)?date('Y/m/d',strtotime($v['modified_on'])):null;
            $result[$i]["template_logo"]            =($result[$i]["template_logo"]!=null)?'uploaded/templates/logo/'.$result[$i]["template_logo"]:'uploaded/templates/logo/default.png';
        } 
        $this->result->json_pag($result);
    }
    public function json_activate_template()
    {
        $template_id=$this->input->get_post("id");
        $result = $this->websites_model->activate_template($template_id);
        $this->result->success($result[0]["activate_template"]);                
    }
    
    //Modules
    public function json_get_websiteModules()
    {
        $w_p_id     =$this->input->get_post("location_id");
        
        $result=$this->websites_model->get_websiteModules($w_p_id);
        foreach($result as $i=>$v)
            $result[$i]["module_icon"]=($result[$i]["module_icon"]!=null)
            	?'uploaded/modules/logo/'.$result[$i]["module_icon"]
            	:'uploaded/modules/logo/default.png';
        $this->result->json_pag($result);    
    }
    
    
    public function json_get_modules()//for activation window
    {
        $w_p_id=$this->input->get_post("location_id");
        
        $result=$this->websites_model->get_modules($w_p_id);
        foreach($result as $i=>$v)
            $result[$i]["module_icon"]            =($result[$i]["module_icon"]!=null)?'uploaded/modules/logo/'.$result[$i]["module_icon"]:'uploaded/modules/logo/default.png';
        $this->result->json_pag($result);
    }
    
    public function json_module_assets()
    {
        $w_m_id=(int)$this->input->get_post("w_m_id");
		$org_id=$this->permissions_model->get_active_org();
		
		$assets=$this->websites_model->get_module_assets($w_m_id);
		 $this->result->json($assets);
    }
    
    public function post_delete_module_assets()
    {
        $moduleasset_id=$this->input->get_post("module_asset_id");
		//$org_id=$this->permissions_model->get_active_org();
		$user=$this->permissions_model->get_active_user();
        
		
		$this->result->success( $this->websites_model->delete_module_asset($moduleasset_id,$user));
    }
    /**
    * change the url that the given asset id is attached to
    * 
    */
    public function post_asset_url()
    {
		$user=$this->permissions_model->get_active_user();
		$ma_id = (int)$this->input->get_post('module_asset_id');
		$url = rawurldecode($this->input->get_post('url'));
		$this->result->success($this->websites_model->update_asset_url($ma_id,$url,$user));
    }
    /**
    * upload an asset (currently only images enabled)
    * along with a url string
    * save them in assets table for given website module
    * that the image will be attached to
    * 
    */
    public function post_module_asset()
    {
		$creator = $this->permissions_model->get_active_user();
		$owner   = $this->permissions_model->get_active_org();
		$wm_id = (int)$this->input->get_post('w_m_id');
		$url = trim(rawurldecode($this->input->get_post('url')));//trim off whitespace
	 	
	 	//if url is blank, make it null
	 	if(!$url  ||$url=='http://')$url=null;
	 	
	 	if(!$wm_id)
	 	{
			$this->result->failure('No Website Module Given');
			return;//stop here
	 	}
	 	 
		$file_id='filepath';//form field of file 
        $this->load->library('images');
		//
		if(!$this->images->type_is_valid_image($file_id) )
		{
			//invalid file
			$this->result->failure('Invalid file, it is either too large, or it is not an image.  '
				//."<br> File Name: ".$_FILES[$file_id]['tmp_name']
				."<br> File Type: ".$_FILES[$file_id]['type']);
			 
		}
		else
		{
			//upload insert , create thumbnail, all that good stuff
			$asset_id = $this->websites_model->insert_module_asset($_FILES[$file_id], $wm_id,$url, $creator,$owner);
			 
			 $this->result->success($asset_id);
		 
		}
		 
	}
	
	/**
	* swap the display_order of the two assets
	* if display order is currently null, assign it (for merging with live system)
	* 
	*/
	public function post_swap_assets()
	{
		$user=(int)$this->permissions_model->get_active_user();
        $first_asset  =(int)$this->input->get_post("first_asset");
        $second_asset =(int)$this->input->get_post("second_asset");
		$w_m_id=(int)$this->input->get_post("w_m_id");//the one theya re both assigend to
		$assets=$this->websites_model->get_module_assets($w_m_id);
		$null_counter=1;
 		$r='';
		foreach($assets as $a)
		{
			$ord=(int)$a['display_order'];
			$asset_id=(int)$a['module_asset_id'];
			
			if(!$ord || $ord==null)//for reverse compat with live sites, live images have no order yet so all are null
			{
				$r.=$this->websites_model->update_asset_order($asset_id,$null_counter,$user);
				$ord=$null_counter;
			}
			$null_counter++;
			//now for the real work
			if($asset_id==$first_asset) $first_order= $ord;
			if($asset_id==$second_asset) $second_order= $ord;
			
			
		}
		$r.=$this->websites_model->update_asset_order($first_asset ,$second_order,$user);
		$r.=$this->websites_model->update_asset_order($second_asset,$first_order ,$user);
		
		
		
		$this->result->success($r);
	}
    public function json_update_org_moduel_pos()
    {
        $selected_WMid  =$this->input->get_post("selected_WMid");
        $w_p_alias      =$this->input->get_post("w_p_alias");
        
        $result=$this->websites_model->update_org_moduel_pos($selected_WMid,$w_p_alias);   
        $this->result->success($result[0]["update_org_moduel_pos"]);
    }
    public function json_get_location_pos()
    {
        $result=$this->websites_model->get_location_pos();
        $this->result->json_pag_store($result);
    }
    public function json_delete_selectedModules()
    {
        $selected_WMids =$this->input->get_post("selected_WMids");
        
        $result=$this->websites_model->delete_selectedModules($selected_WMids);   
        $this->result->success($result[0]["delete_selectedModules"]);
    }
    public function json_add_selectedModules()
    {
        $selected_Mids  =$this->input->get_post("selected_Mids");
        $w_p_id         =$this->input->get_post("location_id");
        
        $result=$this->websites_model->add_selectedModules($selected_Mids,$w_p_id);
        $this->result->success($result[0]["add_selectedModules"]);
    }
    public function json_get_moduleOpts()
    {                                               
        $module_id=$this->input->get_post("module_id");                                         
        $result=$this->websites_model->get_moduleOpts($module_id);
        $this->result->json_pag($result);
    }
    public function json_update_selectedOpts()
    {          
        $w_m_id=$this->input->get_post("w_m_id");
        $selected_ids=$this->input->get_post("selected_ids");
        $result=$this->websites_model->update_selectedOpts($w_m_id,$selected_ids);
        $this->result->success($result[0]["update_selectedOpts"]);
    }
    public function json_play_pause_selectedWebsiteModule()
    {
        $w_m_id=$this->input->get_post("w_m_id");
        $result=$this->websites_model->play_pause_selectedWebsiteModule($w_m_id);
        $this->result->success($result[0]["play_pause_selectedWebsiteModule"]);
    }
    

    public function json_update_websiteModules_ordering()
    {
        $w_m_id_array      =json_decode($this->input->get_post("w_m_id_array")); 
        $order_array   =json_decode($this->input->get_post("order_array"));    
        $w_p_id       =$this->input->get_post("_location_id");    
 
        //w e have to assume all orders are the same length
        $res=array();
        foreach($order_array as $i=>$order)
        {
			$w_m_id = $w_m_id_array[$i]; 
			$res[]=$this->websites_model->update_website_module_order($w_m_id,$order);
        }
        $this->result->success($res);
    }













                    
  }
