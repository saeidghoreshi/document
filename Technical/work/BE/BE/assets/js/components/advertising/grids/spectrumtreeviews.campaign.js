if(!App.dom.definedExt('Ext.spectrumtreeviews.campaign')){
Ext.define('Ext.spectrumtreeviews.campaign',    
{
    extend: 'Ext.spectrumtreeviews', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    }
    
    ,config                 :null
    ,org_type               :null
    ,ssi_assoc              :null //Boolean
    ,ssi_or_assoc           :null //String
    
    ,client_with_all_store  :null
    ,constructor : function(config) 
    {                 
        var me=this;
        config.title='Manage Advertisement Campaigns';
        
        /* 
            (1)get Pure Client Store and Add one-time All Record  
        */
        me.client_with_all_store =new simpleStoreClass().make(['co_org_id','co_org_name'],"index.php/websites/json_get_clients_2/TOKEN:"+App.TOKEN       ,{with_allclient:true});
        var oneTimeAddFunction=function()
        {
            me.client_with_all_store.add({co_org_id:'-1',co_org_name:'All'});    
            me.client_with_all_store.removeListener('load', oneTimeAddFunction);            
        }
        me.client_with_all_store.on("load",oneTimeAddFunction);
        /*END (1)*/
        
        
        config.columns  =
        [
            {
                xtype       : 'treecolumn', //this is so we know which column will show the tree
                text        : 'Campaign',
                width       : 200,
                sortable    : true,
                dataIndex   : 'campaign_name'
            }
            ,{
                text        : 'banner',
                flex        :1,
                sortable    : true,
                dataIndex   : 'banner_name'     
            }
            ,{
                text        : "Clicks#"
                ,dataIndex  : 'clicks'
                ,width      : 80
            }
            ,{
                text        : "Views#"
                ,dataIndex  : 'views'
                ,width      :80
            }
        ];   
        if(config.topItems==null)     config.topItems=[];
        if(config.bottomLItems==null) config.bottomLItems=[];
        if(config.bottomRItems==null) config.bottomRItems=[];
        config.topItems.push
        (
             {
                  xtype           : 'combo',
                  tooltip         : 'Client',
                  emptyText       : 'Select Client',
                  mode            : 'local',
                  triggerAction   : 'all',
                  forceSelection  : false,
                  editable        : false,
                  name            : 'client_list',
                  displayField    : 'co_org_name',
                  valueField      : 'co_org_id',
                  queryMode       : 'local',
                  typeAhead       : true,
                  store           : config.client_store,
                  listeners       : {
                       change: {
                           fn: function(conf,selected_id)
                           {
                               me.getStore().load({params:{client_org_id:selected_id}});
                           },
                           scope: this, 
                           buffer:500                  
                       }
                   }
              }
        );
        
        config.bottomLItems.push
        (
             //Create new Campaign   
             {
                 xtype      : 'button',
                 iconCls    : 'fugue_chart--plus',
                 text       : '',
                 tooltip    : 'Create New Campaign',
                 handler    : function()
                 {
                     var formCampaignDetailsConfig=
                     {
                         width  :400,
                         height :150,
                         bottomItems:   
                         [
                             "->"
                             ,{
                                 xtype    :"button",
                                 iconCls  :"table_save",
                                 text     :"Save",
                                 tooltip  :"Save  campaign",
                                 pressed  :true,
                                 handler:function()
                                 {
                                     if (formCampaignDetails.getForm().isValid()) 
                                     {
                                         var values =   formCampaignDetails.getForm().getValues();                                                           
                                         var post   =   values;
                                         
                                         var start_date=new Date(values["start_date"]);
                                         var end_date  =new Date(values["end_date"]);
                                         
                                         if(start_date>end_date)
                                         {                                                                       
                                             Ext.MessageBox.alert({title:"Invalid Dates",msg:"Start Date is greater that End Date", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                             return;
                                         }
                                          
                                         Ext.Ajax.request(
                                         {
                                               url      : "index.php/websites/json_new_campaign/TOKEN:"+App.TOKEN,
                                               params   : post,
                                               success  : function(response)
                                               {
                                                    var res=YAHOO.lang.JSON.parse(response.responseText);
                                                    if(res.result=="1")
                                                    { 
                                                        Ext.MessageBox.alert({title:"Status",msg:"Campaign Named "+values["campaign_name"]+" Created Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                        campaignManage.init();
                                                        formCampaignDetailsWin.Hide();
                                                    }
                                               }
                                         });
                                     }
                                         
                                 }                          
                             }
                         ]
                     }

                     var formCampaignDetails        =websiteForms.formCampaignDetails();
                     var formCampaignDetailsFinal   =new Ext.spectrumforms.mixer(formCampaignDetailsConfig,[formCampaignDetails],['form']);
                     var formCampaignDetailsWinConfig=
                     {
                        title       : 'Create New Campaign',
                        final_form  : formCampaignDetailsFinal
                     }
                     var formCampaignDetailsWin     =new Ext.spectrumwindow.advertising(formCampaignDetailsWinConfig);
                     formCampaignDetailsWin.show();
                 }   
             }        
        );
        
        config.bottomRItems.push
        (
             //Edit regions   *********************  Association /SSI  Level
             {
                 xtype   : 'button',
                 iconCls : 'fugue_target--pencil',
                 text    : '',
                 id      : 'advertisement_manage_edit_limits_tree',
                 tooltip : 'Target Campaign Regions',
                 handler : function()
                 {
                     if(me.getSelectionModel().getSelection().length==0 || me.getSelectionModel().getSelection()[0].data.banner_id=='')
                     {                                                                           
                        Ext.MessageBox.alert({title:"Status",msg:"Please select a [Campaign-Banner]", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                        return;
                     }  
                     var _campaign_banner_id=me.getSelectionModel().getSelection()[0].data.campaign_banner_id;
                     //Decision
                     var regionAccessGrid;
                     if(me.ssi_or_assoc=='ssi')
                        regionAccessGrid=me.show_associations_win(_campaign_banner_id);
                     else 
                        regionAccessGrid=me.show_countries_win(0,_campaign_banner_id);                       
                 }
             },   
             //Play/pause
             {
                 id      : 'campaign_list_pause_button',
                 iconCls : 'control_play_blue',
                 xtype   : 'button',
                 text    : '',
                 tooltip : 'Activate Selected Campaign',
                 hidden  :true, 
                 handler : function()
                 {
                     if(me.getSelectionModel().getSelection().length==0)
                     { 
                         return;
                     }
                     var campaign_details   =me.getSelectionModel().getSelection()[0].data;
                     if(campaign_details.banner_id=='')//pause/play action for campaign
                     {
                         var post={}
                         post["campaign_id"]    =campaign_details.campaign_id;
                         post["pause_or_play"]  =((campaign_details.paused=='f')?'t':'f')
                         
                         var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                         Ext.Ajax.request(
                         {
                            url      : "index.php/websites/json_pause_play_campaign/"+App.TOKEN,
                            params   : post,
                            success  : function(response)
                            {
                                 var res=YAHOO.lang.JSON.parse(response.responseText);
                                 if(res.result==1)
                                 {   
                                      Ext.getCmp("campaign_list_pause_button").hide();    
                                      me.getStore().load();                     
                                 }
                                 if(res.result==-1)Ext.MessageBox.show({title:"Unable to pause",msg:"Some regions already set up", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});    
                                                   
                                 box.hide();
                            }
                         });
                     }
                     else
                     {
                         var post={}
                         post["cb_id"]          =campaign_details.campaign_banner_id;
                         post["pause_or_play"]  =((campaign_details.paused=='f')?'t':'f')
                         
                         Ext.Ajax.request(
                         {
                            url      : "index.php/websites/json_pause_play_campaignbanner/"+App.TOKEN,
                            params   : post,
                            success  : function(response)
                            {                        
                                me.getStore().load();
                            }
                         });                     
                     }                           
                 }
             }
             //Edit Campaign Banners  
             ,{
                 xtype      : 'button',
                 text       : '',
                 iconCls    : 'layout_edit',
                 tooltip    : 'Manage Campaign Advertisements',
                 handler: function()
                 {
                       if(me.getSelectionModel().getSelection().length==0 
                       || !me.getSelectionModel().getSelection()[0].data.campaign_id )//if empty record)
                       {
                       	   //THIS IS NOT AN ERROR 
                           return;
                       }
                       if(me.getSelectionModel().getSelection()[0].data.campaign_name=='')
                       {  
                       	   //THIS IS NOT AN ERROR 
                          return; 
                       }
                       if(me.getSelectionModel().getSelection()[0].data.banner_id=='')
                       {
                          me.editCampaignBanners();
                          return;    
                       }         
                 }
             }
             //Edit Campaign AND Banner    
             ,'-'  
             ,{
                 xtype  : 'button',
                 text   : '',
                 iconCls: 'fugue_chart--pencil',
                 tooltip: 'Modify Selected Campaign',
                 handler: function()
                 {
                       if(me.getSelectionModel().getSelection().length==0
                       || !me.getSelectionModel().getSelection()[0].data.campaign_id )//if empty record
                       {
                       	   Ext.MessageBox.alert({title:"Status",msg:"Please Select a Record", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                           return;
                       }
 
                       if(me.getSelectionModel().getSelection()[0].data.campaign_name=='')
                       {  
                          me.editBanner();
                          return; 
                       }
                       if(me.getSelectionModel().getSelection()[0].data.banner_id=='')
                       {
                          me.editCampaign();
                          return;    
                       }         
                 }
             }
             //Delete Campaign    
             ,{
                   xtype    : 'button',
                   text     : '',
                   iconCls  : 'fugue_minus-button',
                   tooltip  : 'Remove Selected Campaign',
                   handler  : function()
                   {   
                        if(me.getSelectionModel().getSelection().length==0
                        || !me.getSelectionModel().getSelection()[0].data.campaign_id )//if empty record
                        {
                       	       Ext.MessageBox.alert({title:"Status",msg:"Please Select a Record", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                               return;
                        }
                        if(me.getSelectionModel().getSelection()[0].data.banner_id!='')
                        {
                       	       Ext.MessageBox.alert({title:"Status",msg:"Please Select a Campaign to Delete", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                               return;                                                
                        }
                       post={}
                       post['campaign_id']=me.getSelectionModel().getSelection()[0].data.campaign_id;
                       
                       Ext.MessageBox.confirm('Delete Action', "Are you sure ? (Camaign would not be shown after deletion)", function(answer)
                       {     
                           if(answer=="yes")
                           {
                               Ext.Ajax.request({
                                   url: 'index.php/websites/json_delete_campaign/TOKEN:'+App.TOKEN,
                                   params: post,
                                   success: function(response)
                                   {
                                       var res=YAHOO.lang.JSON.parse(response.responseText);
                                        if(res.result=="1")
                                        {
                                            Ext.MessageBox.alert({title:"Status",msg:"Banner Deleted successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                            me.getStore().load();   
                                        }
                                   }
                               });    
                           }
                       });        
                   }
               }
        );                                   
        
        if(config.fields==null)     config.fields       = 
        [
            'campaign_banner_id','campaign_id','campaign_name',
            'start_date',
            'end_date',
            'iconCls',

            'banner_id',
            'banner_name',
            'client_org_id',
            'banner_size_id',
            'clickurl',
            'banner_script',
            'banner_type_id',
            'banner_type_name',
            'banner_filename',
            'clicks',
            'views',
            'size_w',
            'size_h',
                            
            'paused'                                 
        ];
        if(config.url==null)        config.url          = "/index.php/websites/json_get_campaignbanners/TOKEN:"+App.TOKEN ;
        if(config.width==null)      config.width        = '100%';
 
        
        /*
            (2)getting Current Org Config
        */
        var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');  
        Ext.Ajax.request
        (
        {
                url     : 'index.php/permissions/json_get_active_org_and_type/TOKEN:'+App.TOKEN,
                params  : {test:'test'},
                success : function(response)
                {
                    var res=YAHOO.lang.JSON.parse(response.responseText);
                    if(res.result.org_type_id=="2" )//Association
                    {                               
                            me.ssi_assoc=true;
                            me.ssi_or_assoc="assoc";
                    }
                    else
                    {
                        if(res.result.org_type_id=="1" )//SSI
                        {
                            me.ssi_assoc=true;
                            me.ssi_or_assoc='ssi';
                        }
                        else me.ssi_assoc=false;    
                    }
                    
                    
                    Ext.getCmp("advertisement_manage_edit_limits_tree").setVisible(me.ssi_assoc);
                    box.hide();
                }
        });
        /*END (2)*/
        
        
        this.config=config;
        this.callParent(arguments); 
 },
     afterRender: function() 
     {  
            var me=this;
            if(!this.override_edit)
            {   
                this.on("selectionchange",function(e)
                {
                    var node = me.getSelectionModel().getSelection()[0].data;   
                    Ext.getCmp("campaign_list_pause_button").show();
                    
                    if(node.paused=='t')
                    {
						
                    Ext.getCmp("campaign_list_pause_button").setIconCls("control_play_blue");
                    Ext.getCmp("campaign_list_pause_button").setTooltip("Activate Selected Campaign");
					}
                    else 
                    {
						
                    Ext.getCmp("campaign_list_pause_button").setIconCls("control_pause");
                    Ext.getCmp("campaign_list_pause_button").setTooltip("Deactivate Selected Campaign");
					}                    
                }); 
                
            }
            
            this.callParent(arguments);         
     },
     editCampaign:function()
     {
         var me=this;
         var gen=Math.random();
            
             var campaignDetailsRecord  =me.getSelectionModel().getSelection()[0].data;
             //Get the client list ready for next step combo
             
             var formCampaignDetailsConfig=
             {
                 width  :400,
                 height :150,
                 bottomItems:   
                 [
                     "->"
                     ,{
                             xtype    :"button",
                             iconCls  :"table_save",
                             tooltip  :"Update Campaign",
                             text     :"Update",
                             pressed  :true,
                             handler:function()
                             {                                          
                                 if (formCampaignDetails.getForm().isValid()) 
                                 {   
                                     var post   =formCampaignDetails.getForm().getValues();
                                     if(new Date(post["start_date"])>new Date(post["end_date"]))
                                     {
                                     	 //TODO: just have php  swap the dates for the user
                                         Ext.MessageBox.alert({title:"Incomplete",msg:"Start date is greater that end date", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                         return;
                                     }                   
                                     post["campaign_id"]    =campaignDetailsRecord.campaign_id;
                                     
                                  
                                     Ext.Ajax.request(
                                     {
                                          url     : "index.php/websites/json_update_campaign/TOKEN:"+App.TOKEN,
                                          params  : post,
                                          success : function(response)
                                          {
                                               var res=YAHOO.lang.JSON.parse(response.responseText);
                                               if(res.result=="1")
                                               {
                                                     me.getStore().load();
                                                     formCampaignDetailsWin.Hide();          
                                               }
                                          }
                                      });                                   
                                 }                           
                             }                          
                     }
                 ]      
             }
             var formCampaignDetails        =websiteForms.formCampaignDetails();
             var formCampaignDetailsFinal   =new Ext.spectrumforms.mixer(formCampaignDetailsConfig,[formCampaignDetails],['form']);
                                                                                    
             var formCampaignDetailsWinConfig=
             {
                 title       : 'Update Campaign ('+campaignDetailsRecord.campaign_name+')',
                 final_form  : formCampaignDetailsFinal
             }
             var formCampaignDetailsWin=new Ext.spectrumwindow.advertising(formCampaignDetailsWinConfig);
             formCampaignDetailsWin.show();
             
             //Load record
             Ext.define('Model_'+gen, {extend: 'Ext.data.Model'});
             formCampaignDetails.loadRecord(Ext.ModelManager.create(
             {
                 'campaign_name'     : campaignDetailsRecord.campaign_name
                 ,'start_date'       : campaignDetailsRecord.start_date
                 ,'end_date'         : campaignDetailsRecord.end_date
             }, "Model_"+gen));
       
     },                      
     editCampaignBanners:function()
     {
            var me=this;
            var gen=Math.random();
            
            var campaign_details=me.getSelectionModel().getSelection()[0].data;
            //Get the client list ready for next step combo
            var record  =me.getSelectionModel().getSelection()[0];
            var gridConfig=
            {
                    bottomLItems :
                    [
                         //Create new banner (new one)
                         {
                               xtype   : 'button',
                               iconCls : 'add',
                               text    : '',
                               tooltip : 'Create New Banner*',
                               handler: function()
                               {
                                   var formBannerDetailsConfig=
                                   {
                                       width   :800,
                                       height  :500,
                                       bottomItems:   
                                               [
                                                   "->"
                                                   ,{
                                                           xtype   :"button",
                                                           iconCls :'table_save',
                                                           text    :"Save",
                                                           pressed :true,
                                                           handler:function()
                                                           {
                                                               var values   =formBannerDetailsFinal.getForm().getValues();
                                                               
                                                               if (formBannerDetailsFinal.getForm().isValid()) 
                                                               {   
                                                                   formBannerDetailsFinal.getForm().submit(
                                                                   {
                                                                       url: 'index.php/websites/json_new_banner/'+App.TOKEN,
                                                                       waitMsg: 'Processing...',
                                                                       params: {},
                                                                       success: function(form, action)
                                                                       {
                                                                           var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                                           if(res.result=="-1")
                                                                           {
                                                                           	   Ext.MessageBox.alert({title:"Wrong Size",msg:"Banner size does not match", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});    
                                                                               return;
                                                                           }
                                                                           Ext.MessageBox.alert({title:"Status",msg:"Banner uploaded successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                           formBannerDetailsWin.Hide();
                                                                           me.client_with_all_store.load();
                                                                           grid.getStore().load();
                                                                           //(New functionality) Load position window
                                                                           me.show_campaign_banner_position(me);
                                                                       },
                                                                       failure: function(form, action){alert(YAHOO.lang.dump(action.response));}
                                                                   });
                                                               }            
                                                           }                          
                                                   }
                                               ]                   
                                   }                          
                                   //Logic *******************
                                   var formBannerDetails1        =websiteForms.formBannerDetails1(me.client_with_all_store,me.size_store);
                                   var formBannerDetails2        =websiteForms.formBannerDetails2(me.client_with_all_store,me.size_store);
                                   var formBannerDetailsFinal   =new Ext.spectrumforms.mixer(formBannerDetailsConfig,[formBannerDetails1,formBannerDetails2],['form','form']);
                                   var formBannerDetailsWinConfig=
                                   {
                                       title       : 'New Advertisement',
                                       final_form  : formBannerDetailsFinal
                                   }
                                   var formBannerDetailsWin     =new Ext.spectrumwindow.advertising(formBannerDetailsWinConfig);
                                   formBannerDetailsWin.show();
                               }   
                         }
                         //Add from banner List
                         ,{
                            xtype    : 'button',
                            iconCls  : 'block-share',
                            text     : '',
                            tooltip  : 'Add banner from list',       
                            handler  : function()
                            {   
                                me.show_campaign_banner_position(grid); 
                            }  
                         }
                         //Assign Regions      ************************************
                         ,{
                              xtype   : 'button',
                              text    : '',
                              iconCls : 'fugue_target--pencil',
                              id      : "advertisement_manage_edit_limits_campaignbanners",
                              tooltip : 'Assign Regions',       
                              handler : function()
                              {   
                                  if(grid.getSelectionModel().getSelection().length==0)
                                  {                                              
                                      Ext.MessageBox.alert({title:"Status",msg:"Please Select a Campaign Banner", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                      return;
                                  }  
                                  var _campaign_banner_id=grid.getSelectionModel().getSelection()[0].data.campaign_banner_id;
                                  //Decision
                                  var g;
                                  var grid_config=   
                                  {
                                       width       :800,
                                       height      :375
                                  }
                                    
                                  if(me.ssi_or_assoc=='ssi')g=me.show_associations_win(_campaign_banner_id);
                                  else g=me.show_countries_win(0,_campaign_banner_id);                       
                                  
                                  //var grid_countries=advertisement_manage.show_countries_win(_campaign_banner_id);
                              } 
                        }
                    ] 
            }                                                                                       
            //--End    
            var grid    =me.buildBannerGrid("index.php/websites/json_get_campaign_banners_by_campid/TOKEN:"+App.TOKEN,'',{campaign_id:campaign_details.campaign_id},gridConfig);
            
            Ext.getCmp("spectrumgrids_banner_newbanner_btn").hide();
            
            var formConfig=   
            {
                width       :800,
                height      :375
            }
            var formFinal       =new Ext.spectrumforms.mixer(formConfig,[grid],['grid']);
            var formWinConfig   =
            {
               title       : 'Banners Assigned to Campaign Named ('+record.data.campaign_name + ')',
               final_form  : formFinal
            }
            var formWin=new Ext.spectrumwindow.advertising(formWinConfig);
            formWin.show();
            
            Ext.getCmp("advertisement_manage_edit_limits_campaignbanners").setVisible(me.ssi_assoc); 
     },                      
     editBanner:function()
     {
         var me=this;

         var formBannerDetailsConfig=
         {
             width  :800,
             height :500,
             bottomItems:   
             [
                 {
                         xtype   :"button",
                         iconCls : 'image',
                         text    : '',
                         tooltip : 'Banner Preview',
                         handler :function()
                         {
                             var record          =me.getSelectionModel().getSelection()[0].data;
                             
                             websiteForms.show_image_on_shadow('uploaded/banner-assets/'+record.banner_filename,record.banner_script,record.banner_type_name,'('+record.banner_name+') Banner Preview',record.size_w,record.size_h);
                         }
                 }
                 ,'Leave the File Upload field blank if you wanna keep the file'
                 ,"->"
                 ,{
                          xtype   :"button",
                          iconCls :'table_save',
                          text    :"Update",
                          pressed :true,
                          handler :function()
                          {
                             if(Ext.getCmp("file_upload").getValue()=='')
                             {
                                 Ext.getCmp("file_upload").setDisabled(true);
                                 Ext.getCmp("size_list").setDisabled(true);
                             }
                             
                             if (formBannerDetailsFinal.getForm().isValid()) 
                             { 
                                 var _banner_id= me.getSelectionModel().getSelection()[0].data.banner_id; 
                                 formBannerDetailsFinal.getForm().submit(
                                 {                         
                                          url     : 'index.php/websites/json_update_banner/TOKEN:'+App.TOKEN,
                                          waitMsg : 'Processing ...',
                                          params  : {banner_id:_banner_id},
                                          success : function(form, action)
                                          {
                                              var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                              if(res.result=="-1")
                                              {
                                                  Ext.MessageBox.alert({title:"Error",msg:"Banner size does not match", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});    
                                                  return;
                                              }
                                              Ext.MessageBox.alert({title:"Status",msg:"Banner uploaded successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                              formBannerDetailsWin.Hide();
                                              me.getStore().load();
                                          },
                                          failure: function(form, action){alert(YAHOO.lang.dump(action));}
                                 });
                             }
                          }                          
                  }
             ]                   
         }
         
         //Logic *******************
         var banner_details         =me.getSelectionModel().getSelection()[0].data; 
         var formBannerDetails1      =websiteForms.formBannerDetails1(me.client_with_all_store,me.size_store,banner_details.banner_type_id);
         var formBannerDetails2      =websiteForms.formBannerDetails2(me.client_with_all_store,me.size_store,banner_details.banner_type_id);
         var formBannerDetailsFinal =new Ext.spectrumforms.mixer(formBannerDetailsConfig,[formBannerDetails1,formBannerDetails2],['form','form']);
         
         var formBannerDetailsWinConfig=
         {
             title       : 'Edit Advertisement',
             final_form  : formBannerDetailsFinal
         }
         var formBannerDetailsWin=new Ext.spectrumwindow.advertising(formBannerDetailsWinConfig);
         formBannerDetailsWin.show();
         
         //Load record      
         
         var gen=Math.random();
         Ext.define('model_'+gen, {extend: 'Ext.data.Model'});
         formBannerDetailsFinal.loadRecord(Ext.ModelManager.create(
         {
             'banner_id'         : banner_details.banner_id
             ,'banner_name'      : banner_details.banner_name
             ,'client_list'      : banner_details.client_org_id
             ,'size_list'        : banner_details.banner_size_id
             ,'clickurl'         : banner_details.clickurl   
             ,'script'           : banner_details.banner_script.replace(/[\\]/gi,'')
         }, "model_"+gen));       
     },
     
     //Ryan Maybe needs changes .......
     buildBannerGrid:function(parUrl,parTitle,parExtraParamsMore,parConfig)
     {  
         var me=this;                       
         var bannerConfig=
         {
                generator               : Math.random(),
                owner                   : me,
                
                title                   : parTitle,
                extraParamsMore         : parExtraParamsMore,
                collapsible             : false,
                
                url                     : parUrl,
                client_store            : me.client_with_all_store,
                size_store              : me.size_store,
                client_with_all_store   : me.client_with_all_store,
                bottomLItems            : (parConfig)?parConfig.bottomLItems:[],
                fields                  : ['banner_id','banner_name','banner_filename','client_org_id','client_name','banner_type_id','banner_type_name','banner_script','banner_size_id','size_w','size_h','clickurl','name','clicks','views','isactive','campaign_banner_id'],
                frame                   :false,
                
                //customized Components
                rowEditable     :false,
                groupable       :true,
                bottomPaginator :true,
                searchBar       :true,    
                //Function appendable or overridble
                
                override_edit           :false,
                override_itemdblclick   :false,
                override_selectionchange:false,
                override_expand         :false,
                override_collapse       :false   
         }
         var grid = Ext.create('Ext.spectrumgrids.banner',bannerConfig);
         
         return grid;
     }, 
     show_campaign_banner_position:function(owner) 
     {
         var me                     =this;
         var gridConfig             =
         {
             width  :800,
             height :300
         }
         var myBannersGrid          =me.buildBannerGrid("index.php/websites/json_get_org_banners/TOKEN:"+App.TOKEN,'My Banners',gridConfig);
         
         var positions_store        =new simpleStoreClass().make(['type_id','type_name'],"index.php/websites/json_get_positions/TOKEN:"+App.TOKEN,{test:true});
         var formBannerPosition     =websiteForms.formBannerPosition(positions_store);
         
         
         //Building Final Form
         var formFinalConfig        =
         {
                              width         :800,
                              height        :550,
                              
                              formStyle     :'vertical2',
                              collapsible   :false,
                              gen           :Math.random(),
                              
                              bottomItems   :   
                              [   
                                    "->"  
                                    ,{
                                                  xtype      :"button",
                                                  text       :"Assign",
                                                  pressed    :true,
                                                  iconCls    :"table_save",
                                                  tooltip    :"Assign banner/position to campaign",
                                                  
                                                  handler:function()
                                                  {                                                      
                                                      if(myBannersGrid.getSelectionModel().getSelection().length==0)
                                                      {                                
                                                          Ext.MessageBox.alert({title:"Status",msg:"Cannot Save without a banner", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                          return;
                                                      }
                                                      var bannerRecord=myBannersGrid.getSelectionModel().getSelection()[0];
                                                      
                                                      var form               =formFinal.getForm();
                                                      var post               =form.getValues();
                                                      post["banner_id"]      =bannerRecord.data.banner_id;
                                                      post["banner_size_id"] =bannerRecord.data.banner_size_id;
                                                      post["campaign_id"]    =me.getSelectionModel().getSelection()[0].data.campaign_id;
                                                      
                                                      if (form.isValid()) 
                                                      {   
                                                          if(isNaN(post["max_views"]) || isNaN(post["max_clicks"]))
                                                          {
                                                          	  Ext.MessageBox.alert({title:"Status",msg:"Max Numbers must be numbers", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                              return;
                                                          }
                                                          form.submit(
                                                          {
                                                              url       : 'index.php/websites/json_assign_banner_campaign_pos/'+App.TOKEN,
                                                              waitMsg   : 'Processing...',
                                                              params    : post,
                                                              success   : function(form, action)
                                                              {
                                                                  var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                                  if(res.result=='-1')
                                                                  {                                                                                                                                          
                                                                      Ext.MessageBox.alert({title:"Status",msg:"Predefined banner dimensions does not match selected position. Please select another position", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                      return;
                                                                  }
                                                                  formWin.Hide();
                                                                  owner.getStore().load();
                                                                  campaignManage.init();
                                                              },
                                                              failure: function(form, action){App.error.xhr(action.response);}
                                                          });
                                                      }            
                                                  }                          
                                          }
                              ]
         }
         var formFinal              =new Ext.spectrumforms.mixer(formFinalConfig,[myBannersGrid,formBannerPosition],['grid','form']);
         var formWinConfig          =
         {
            title       : '',
            final_form  : formFinal
         }
         var formWin                =new Ext.spectrumwindow.advertising(formWinConfig);
         formWin.show();
     },
     
   
     //Regions Restriction Access usage by ssi and association****************************************************************************************************************************
     window_list    :[],
     show_associations_win:function(_cb_id)
     {
         var me=this;
         
         var formConfig=
         {
             width  :400,
             height :510,
             bottomItems:   
             [
                 {
                         xtype    :"button",
                         tooltip  :"List Countries",
                         iconCls  :"zoom",
                         handler:function()
                         {
                            if(g.getSelectionModel().getSelection().length!=1)
                            {                                            
                                Ext.MessageBox.alert({title:"Cannot List",msg:"For browsing regions only one country need to be selected", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                            }
                            
                            var assoc_id=g.getSelectionModel().getSelection()[0].data.id;
                            var g1=me.show_countries_win(assoc_id,_cb_id);
                         }                          
                 }
                 ,"-"
                 ,{
                         xtype  :"button",
                         tooltip:"Close All",
                         iconCls:"stop",
                         handler:function()
                         {
                             for(var i=0;i<me.window_list.length;i++)
                                me.window_list[i].hide();
                         }                          
                 }
                 ,"->"
                 ,{
                         xtype  :"button",
                         text   :"Save",
                         iconCls:"table_save",
                         pressed:true,
                         handler:function()
                         {
                             if(g.getSelectionModel().getSelection().length==0)
                             { 
                                 return;
                             }      
                             var _ids='';
                             var selecteds=g.getSelectionModel().getSelection();
                             for(var i=0;i<selecteds.length;i++)_ids+=selecteds[i].data.id+',';
                             _ids=_ids.substring(0,_ids.length-1);
                                                                                                                                
                             
                             var callback ={success:function(o)
                             {
                                //me.window_list[me.window_list.length-1].hide(); 
                                //me.window_list.splice(me.window_list.length-1,1);
                                for(var i=0;i<me.window_list.length;i++)me.window_list[i].Hide();
                             },scope:this}  
                             var post="cb_id="+_cb_id+"&ids="+_ids;
                             YAHOO.util.Connect.asyncRequest("POST","index.php/websites/json_assign_associations_to_cb/TOKEN:"+App.TOKEN,callback,post)
                         }                          
                 }
             ]
         }
         
         var g          =me.load_limits_grid("association",0,_cb_id);
         var formFinal  =new Ext.spectrumforms.mixer(formConfig,[g],['grid']);
         var formWinConfig=
         {
            title       : '',
            final_form  : formFinal
         }
         var formWin=new Ext.spectrumwindow.advertising(formWinConfig);
         formWin.show();
         me.window_list.push(formWin);
         
         return g;
     },
     show_countries_win:function(assoc_id,_cb_id)
     {
         var me=this;
         
         var formConfig=
         {
             width      :400,
             height     :510,
             bottomItems:   
             [
                 {
                         xtype    :"button",
                         tooltip  :"List Regions",
                         iconCls  :"zoom",
                         handler:function()
                         {
                            if(g.getSelectionModel().getSelection().length!=1)
                            {                                                                                                     
                                Ext.MessageBox.alert({title:"Cannot List",msg:"For browsing regions only one country need to be selected", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                            }
                            var g1=me.show_regions_win(g.getSelectionModel().getSelection()[0].data.id,_cb_id);
                         }                          
                 }
                 ,"-"
                 ,{
                         xtype  :"button",
                         tooltip:"Close All",
                         iconCls:"stop",
                         handler:function()
                         {
                             for(var i=0;i<me.window_list.length;i++)me.window_list[i].Hide();
                         }                          
                 }
                 ,"->"
                 ,{
                         xtype  :"button",
                         text   :"Save",
                         iconCls:"table_save",
                         pressed:true,
                         handler:function()
                         {
                             if(g.getSelectionModel().getSelection().length==0)
                             {
                                 Ext.MessageBox.alert({title:"Cannot Save",msg:"Please select a record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                 return;
                             }      
                             var _ids='';
                             var selecteds=g.getSelectionModel().getSelection();
                             for(var i=0;i<selecteds.length;i++)_ids+=selecteds[i].data.id+',';
                             _ids=_ids.substring(0,_ids.length-1);
                                                                                                                                
                             
                             var callback ={success:function(o)
                             {
                                //me.window_list[me.window_list.length-1].hide(); 
                                //me.window_list.splice(me.window_list.length-1,1);
                                for(var i=0;i<me.window_list.length;i++)me.window_list[i].Hide();
                             },scope:this}  
                             var post="cb_id="+_cb_id+"&ids="+_ids;
                             YAHOO.util.Connect.asyncRequest("POST","index.php/websites/json_assign_countries_to_cb/TOKEN:"+App.TOKEN,callback,post)
                         }                          
                 }
             ]
         }
         
         
         var g                  =me.load_limits_grid("country",assoc_id/*no matter for assoc but for ssi level*/,_cb_id);
         var formFinal          =new Ext.spectrumforms.mixer(formConfig,[g],['grid']); 
         var formFinalWinConfig  =
         {
                                title       : '',
                                final_form  : formFinal
         }
         var formFinalWin        =new Ext.spectrumwindow.advertising(formFinalWinConfig);
         formFinalWin.show();
         me.window_list.push(formFinalWin);
         
         return g;
     },
     show_regions_win:function(country_id,_cb_id)
     {
         var me=this;
         
         var config=
         {
             width  :400,
             height :510,
             bottomItems:   
             [
                 {
                         xtype    :"button",
                         tooltip  :"List Cities",
                         iconCls  :"zoom",
                         handler:function()
                         {
                            if(g.getSelectionModel().getSelection().length!=1)
                            {                                                                                                   
                                Ext.MessageBox.alert({title:"Cannot List",msg:"For browsing cities only one region need to be selected", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                            }
                            me.show_cities_win(g.getSelectionModel().getSelection()[0].data.id,_cb_id);
                         }                          
                 }
                 ,"-"
                 ,{
                         xtype  :"button",
                         tooltip:"Close All",
                         iconCls:"stop",
                         handler:function()
                         {
                             for(var i=0;i<me.window_list.length;i++)me.window_list[i].Hide();
                         }                          
                 }
                 ,"->"
                 ,{
                         xtype  :"button",
                         text   :"Save",
                         tooltip:"Save",
                         iconCls:"table_save",
                         pressed:true,
                         handler:function()
                         {
                             if(g.getSelectionModel().getSelection().length==0)
                             {
                                 Ext.MessageBox.alert({title:"Cannot Save",msg:"Please select a record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                 return;
                             }
                             var _ids='';
                             var selecteds=g.getSelectionModel().getSelection();
                             for(var i=0;i<selecteds.length;i++)_ids+=selecteds[i].data.id+',';
                             _ids=_ids.substring(0,_ids.length-1);
                                                                                                                                
                             
                             var callback ={success:function(o)
                             {
                                 //me.window_list[me.window_list.length-1].hide(); 
                                 //me.window_list.splice(me.window_list.length-1,1);
                                 for(var i=0;i<me.window_list.length;i++)me.window_list[i].Hide();
                             },scope:this}
                             var post="cb_id="+_cb_id+"&ids="+_ids;
                             YAHOO.util.Connect.asyncRequest("POST","index.php/websites/json_assign_regions_to_cb/TOKEN:"+App.TOKEN,callback,post)
                         }                          
                 }
             ]
         }
         var g          =me.load_limits_grid("region",country_id,_cb_id);
         var final_form =new Ext.spectrumforms.mixer(config,[g],['grid']); 
         var win_cnf    =
         {
            title       : '',
            final_form  : final_form
         }
         var win        =new Ext.spectrumwindow.advertising(win_cnf);
         win.show();
         me.window_list.push(win);                    
         return g;
     },
     show_cities_win:function(region_id,_cb_id)
     {
         var me=this;
         
         var config=
         {
             width      :400,
             height     :510,
             bottomItems:   
             [
                 {
                         xtype    :"button",
                         tooltip  :"List Leagues",
                         iconCls  :"zoom",
                         handler:function()
                         {
                            if(g.getSelectionModel().getSelection().length!=1)
                            {                                            
                                Ext.MessageBox.alert({title:"Cannot List",msg:"For browsing leagues only one city need to be selected", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                            }
                            me.show_leagues_win(g.getSelectionModel().getSelection()[0].data.id,_cb_id);
                         }                          
                 }
                 ,"-"
                 ,{
                         xtype  :"button",
                         tooltip:"Close All",
                         iconCls:"stop",
                         handler:function()
                         {
                             for(var i=0;i<me.window_list.length;i++)me.window_list[i].Hide();
                         }                          
                 }
                 ,"->"
                 ,{
                         xtype  :"button",
                         text   :"Save",
                         iconCls:"table_save",
                         pressed:true,
                         handler:function()
                         {
                             if(g.getSelectionModel().getSelection().length==0)
                             {
                                 Ext.MessageBox.alert({title:"Cannot Save",msg:"Please select a record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                 return;
                             }
                             var _ids='';
                             var selecteds=g.getSelectionModel().getSelection();
                             for(var i=0;i<selecteds.length;i++)_ids+=selecteds[i].data.id+',';
                             _ids=_ids.substring(0,_ids.length-1);
                                                                                                                                
                             
                             var callback ={success:function(o)
                             {
                                 //me.window_list[me.window_list.length-1].hide(); 
                                 //me.window_list.splice(me.window_list.length-1,1);
                                 for(var i=0;i<me.window_list.length;i++)me.window_list[i].Hide();
                             },scope:this}
                             var post="cb_id="+_cb_id+"&ids="+_ids;
                             YAHOO.util.Connect.asyncRequest("POST","index.php/websites/json_assign_cities_to_cb/TOKEN:"+App.TOKEN,callback,post)
                         }                          
                 }
             ]   
         }
         var g=me.load_limits_grid("city",region_id,_cb_id);
         var final_form=new Ext.spectrumforms.mixer(config,[g],['grid']); 
         var win_cnf=
         {
            title       : '',
            final_form  : final_form
         }
         var win=new Ext.spectrumwindow.advertising(win_cnf);
         win.show();
         
         me.window_list.push(win);                   
         return g;
     },
     show_leagues_win:function(city_id,_cb_id)
     {
         var me=this;
         
         var config=
         {
             width   :400
             ,height :510
             ,bottomItems:   
                         [
                             {
                                     xtype  :"button",
                                     tooltip:"Close All",
                                     iconCls:"stop",
                                     handler:function()
                                     {
                                         for(var i=0;i<me.window_list.length;i++)me.window_list[i].Hide();
                                     }                          
                             }
                             ,"->"
                             ,{
                                     xtype  :"button",
                                     text   :"Save",
                                     iconCls:"table_save",
                                     pressed:true,
                                     handler:function()
                                     {
                                         if(g.getSelectionModel().getSelection().length==0)
                                         {
                                             Ext.MessageBox.alert({title:"Cannot Save",msg:"Please select a record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                             return;
                                         }
                                         var _ids='';
                                         var selecteds=g.getSelectionModel().getSelection();
                                         for(var i=0;i<selecteds.length;i++)_ids+=selecteds[i].data.id+',';
                                         _ids=_ids.substring(0,_ids.length-1);
                                         
                                         var post={};
                                         post["cb_id"]  = _cb_id;
                                         post["ids"]    = _ids;
                                         
                                         var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                         Ext.Ajax.request(
                                         {
                                               url: "index.php/websites/json_assign_leagues_to_cb/TOKEN:"+App.TOKEN,
                                               params: post,
                                               success: function(response)
                                               {                                                                     
                                                    box.hide();
                                                    for(var i=0;i<me.window_list.length;i++)me.window_list[i].Hide();
                                               }
                                         });    
                                     }                          
                             }
                         ]   
         }
         var g=me.load_limits_grid("league",city_id,_cb_id);
         var final_form=new Ext.spectrumforms.mixer(config,[g],['grid']); 
         var win_cnf=
         {
            title       : '',
            final_form  : final_form
         }
         var win=new Ext.spectrumwindow.advertising(win_cnf);
         win.show();
         me.window_list.push(win);                   
         return g;
     },
     load_limits_grid:function(limit_type,query_id,_cb_id)
     { 
     
        var me=this;                  
        var _generator=Math.random(); 
        
        var _url='';
        if(limit_type=='association')   _method='json_get_all_associations_forlimits';
        if(limit_type=='country')       _method='json_get_all_countries_forlimits';
        if(limit_type=='region')        _method='json_get_all_regions_forlimits';
        if(limit_type=='city')          _method='json_get_all_cities_forlimits';
        if(limit_type=='league')        _method='json_get_all_leagues_forlimits';
        
        var config=
        {
            generator       : _generator,
            owner           : me,
            
            title           : '',
            extraParamsMore : {id:query_id,cb_id:_cb_id},
            collapsible     :false,
            frame           :false,
            url             :"index.php/websites/"+_method+"/TOKEN:"+App.TOKEN,
            pageSize        :1000,
            
            selModel        : Ext.create('Ext.selection.CheckboxModel', 
                                {
                                    mode:'MULTI',
                                    listeners: {
                                        selectionchange: function(sm, selections) 
                                        {
                                            //if(selections.length!=0)
                                             //   alert(YAHOO.lang.dump(selections[0].data));
                                        }
                                    }
                                }),
                        
            
            //customized Components
            rowEditable     :false,
            groupable       :false,
            bottomPaginator :true,
            searchBar       :true
            //Function appendable or overridble
             
            
            
        }
        //Fresh top/bottom components list
        if(config.topItems==null)     config.topItems=[];
        if(config.bottomLItems==null) config.bottomLItems=[];
        if(config.bottomRItems==null) config.bottomRItems=[];
        
        config.topItems.push
        (
            {
                xtype       : 'button',
                text        : 'Show Saved Settings',
                iconCls     : 'magnifier',
                width       : 150,
                pressed     : true,
                fieldLabel  : '',
                labelWidth  : 50,
                handler:function()
                {
                    var callback={success:function(o)
                    {
                        var res=YAHOO.lang.JSON.parse(o.responseText);
                        if(res.length==0)
                        {                       
                            Ext.MessageBox.alert({title:"Alert",msg:"No Saved Regions found", icon: Ext.Msg.WARNING,buttons: Ext.MessageBox.OK});
                            return;
                        }
                        var module  =res[0].module;
                        var query_id=res[0].query_id;
                        
                        if(module=='association') me.show_associations_win(_cb_id);
                        if(module=='countries') me.show_countries_win(query_id,_cb_id);
                        if(module=='regions')   me.show_regions_win(query_id,_cb_id);
                        if(module=='cities')    me.show_cities_win(query_id,_cb_id);
                        if(module=='leagues')   me.show_leagues_win(query_id,_cb_id);
                    },scope:this}
                    YAHOO.util.Connect.asyncRequest("POST","index.php/websites/json_get_limits_selected_link/TOKEN:"+App.TOKEN,callback,"cb_id="+_cb_id);
                }                                   
            }                
        );
            
        
        var grid= Ext.create('Ext.spectrumgrids.limits',config);
        //check per selected s
        grid.getStore().on('load', function()
        {
            for (var i=0;i<grid.getStore().getCount();i++)
            {
                var rec=grid.getStore().getAt(i);
                if(rec.data.checked=='true')
                    //g1.getSelectionModel().selectRange(1, 1, true);
                    grid.getSelectionModel().select(rec,true,false);    
            }
        });
        return grid; 
     }
});}
