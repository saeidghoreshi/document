if(!App.dom.definedExt('Ext.spectrumgrids.banner')){
Ext.define('Ext.spectrumgrids.banner',    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    }

    ,constructor : function(config) 
    {                                                                      
        var me=this;
        config.columns  =
        [    
             {
                text        : "Banner"
                ,dataIndex  : 'banner_name'
                ,width      :200
                ,editor : 
                {
                    xtype:'textfield',
                    allowBlank: false
                }
            }                
            ,{
                text        : "Client"
                ,dataIndex  : 'client_name'
                ,width      : 150
            }                             
            ,{
                text        : "# Clicks"
                ,dataIndex  : 'clicks'
                ,width      : 80
            }
            ,{
                text        : "# Views"
                ,dataIndex  : 'views'
                ,width      : 80
            }
            ,{
                text        : "banner Type"
                ,dataIndex  : 'banner_type_name'
                ,flex       :1
            }                             
        ];
        
        //Fresh top/bottom components list
        if(config.topItems==null)     config.topItems       =[];
        if(config.bottomLItems==null) config.bottomLItems   =[];
        if(config.bottomRItems==null) config.bottomRItems   =[];
        
        config.topItems.push
        (
            {
                        //id              : 'spectrumgrids_banner_clients_combo',
                        xtype           : 'combo',
                        tooltip         : 'Client',
                        emptyText       : 'Select Client',
                        triggerAction   : 'all',
                        forceSelection  : false,
                        editable        : false,
                        name            : 'client_list',
                        displayField    : 'co_org_name',
                        valueField      : 'co_org_id',
                        queryMode       : 'local',
                        store           : config.client_with_all_store,
                        listeners: 
                        {
                             change: 
                             {
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
            //new Banner
            {
                    id      :"spectrumgrids_banner_newbanner_btn",
                    xtype   : 'button',
                    iconCls : 'add',
                    text    : '',
                    tooltip : 'Create New Banner',
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
                                    handler :function()
                                    {
                                        if (!formBannerDetailsFinal.getForm().isValid()) {return;}
                                           
                                        formBannerDetailsFinal.getForm().submit(
                                        {
                                            url     : 'index.php/websites/json_new_banner/'+App.TOKEN,
                                            params  : {},
                                            success: function(form, action)
                                            {
                                                var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                if(res.result=="-1")
                                                {
                                                    Ext.MessageBox.alert({title:"Upload Rejected",msg:"Advertisement size does not match", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});    
                                                    return;
                                                }
                                                if(res.result=="-2")
                                                {
                                                    Ext.MessageBox.alert({title:"Upload Rejected",msg:"This is not a valid image file", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});    
                                                    return;
                                                }
                                                Ext.MessageBox.alert({title:"Status",msg:"Advertisement Uploaded successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                
                                                formBannerDetailsWin.Hide();
                                                config.client_with_all_store.load();
                                                me.getStore().load();
                                            },
                                            failure: function(form, action){App.error.xhr(action.response);}
                                        });
                                                
                                    }                          
                                }
                            ]                   
                        }       
                        
                                           
                        //Logic *******************
                        var formBannerDetails1      =websiteForms.formBannerDetails1(config.client_store,config.size_store);
                        var formBannerDetails2      =websiteForms.formBannerDetails2(config.client_store,config.size_store);
                        var formBannerDetailsFinal  =new Ext.spectrumforms.mixer(formBannerDetailsConfig,[formBannerDetails1,formBannerDetails2],['form','form']);
                        
                        var formBannerDetailsWinConfig=
                        {
                            title       : 'New Advertisement',
                            final_form  : formBannerDetailsFinal
                        }
                        var formBannerDetailsWin=new Ext.spectrumwindow.advertising(formBannerDetailsWinConfig);
                        formBannerDetailsWin.show();
                    }   
            }            
        ); 
        
        config.bottomRItems.push
        (
            //Preview Advertisement in shadowbox
            {
                 xtype    : 'button',
                 text     : '',
                 iconCls  : 'image',
                 tooltip  : 'Preview Banner',
                 handler  : function()
                 {   
                      if(me.getSelectionModel().getSelection().length==0)  
                      {
                      	  //THIS IS NOT AN ERROR
                          return;
                      }
                      var record          =me.getSelectionModel().getSelection()[0].data;
                      websiteForms.show_image_on_shadow('uploaded/banner-assets/'+record.banner_filename,record.banner_script,record.banner_type_name,'('+record.banner_name+') Advertisement Preview',record.size_w,record.size_h);
                 }
             },
            //Edit
            {
                xtype   : 'button',
                iconCls :'pencil',
                text    : '',
                tooltip: 'Modify Selected Banner',
                handler: function()
                {   
                    var formBannerDetailsConfig=
                    {
                        width   :800,
                        height  :500,
                        bottomItems:
                        [
                            {
                                xtype   :"button",
                                iconCls : 'image',
                                text    : '',
                                tooltip : 'Advertisement Preview',
                                handler:function()
                                {
                                    var record          =me.getSelectionModel().getSelection()[0].data;                                                                                                             
                                    websiteForms.show_image_on_shadow('uploaded/banner-assets/'
                                    	+record.banner_filename,record.banner_script,record.banner_type_name,
                                    	'('+record.banner_name+') Banner Preview',record.size_w,record.size_h);
                                }
                            }
                            ,'Leave the File Upload field blank if you want to keep the file'
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
                                       
                                       var post =formBannerDetailsFinal.getForm().getValues();
                                                                                
                                       if (formBannerDetailsFinal.getForm().isValid()) 
                                       { 
                                           var _banner_id   = me.getSelectionModel().getSelection()[0].data.banner_id; 
                                           post["banner_id"]=_banner_id;
                                           
                                           formBannerDetailsFinal.getForm().submit(
                                           {                         
                                           	   //TODO: create and update should use the same PHP function
                                           	   //also, SAVE BUTTON should be part of the form, not part of the grid!!
                                                    url     : 'index.php/websites/json_update_banner/'+App.TOKEN,
                                                     
                                                    params  : post,
                                                    success : function(form, action)
                                                    {
                                                        var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                        if(res.result=="-1")
                                                        {
                                                            Ext.MessageBox.alert({title:"Upload Rejected",msg:"Advertisement size does not match", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});    
                                                            return;
                                                        }
                                                        Ext.MessageBox.alert({title:"Status",msg:"Advertisement Uploaded successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                        formBannerDetailsWin.Hide();
                                                        config.client_with_all_store.load();
                                                        me.getStore().load();
                                                    },
                                                    failure: function(form, action){App.error.xhr(action.response);}
                                           });
                                       }
                                    }                          
                            }
                        ]                   
                    }
                    
                    //Logic *******************
                    if(me.getSelectionModel().getSelection().length==0)
                    { 
                        return;
                    }
                    var banner_details          =me.getSelectionModel().getSelection()[0].data; 
                    
                    var formBannerDetails1      =websiteForms.formBannerDetails1(config.client_store,config.size_store,banner_details.banner_type_id);
                    var formBannerDetails2      =websiteForms.formBannerDetails2(config.client_store,config.size_store,banner_details.banner_type_id);
                    var formBannerDetailsFinal  =new Ext.spectrumforms.mixer(formBannerDetailsConfig,[formBannerDetails1,formBannerDetails2],['form','form']);
                    
                    var formBannerDetailsWinConfig=
                    {
                        title       : 'Edit Advertisement ('+banner_details.banner_name+')',
                        final_form  : formBannerDetailsFinal
                    }
                    var formBannerDetailsWin=new Ext.spectrumwindow.advertising(formBannerDetailsWinConfig);
                    formBannerDetailsWin.show();
                       
                    //Load record    
                    formBannerDetailsFinal.loadRecord(Ext.ModelManager.create(
                    {
                        'banner_id'         : banner_details.banner_id
                        ,'banner_name'      : banner_details.banner_name
                        ,'client_list'      : banner_details.client_org_id
                        ,'size_list'        : banner_details.banner_size_id
                        ,'script'           : banner_details.banner_script.replace(/[\\]/gi,'')
                        ,'clickurl'         : banner_details.clickurl
                    }, "model_"+config.generator));
                }
                },
            //Delete
            {
                    xtype       : 'button',
                    iconCls     : 'delete',
                    text        : '',
                    tooltip     : 'Remvoe Selected Banner',
                    handler     : function()
                    {           
                        if(me.getSelectionModel().getSelection().length==0)
                        {
 
                            return;
                        }
                        post={}
                        post['banner_id']=me.getSelectionModel().getSelection()[0].data.banner_id;
                        
                        Ext.MessageBox.confirm('Delete Action',
                        	 "Are You Sure ? (Advertisement would be Removed Itself and from Related Campaigns)", 
                        	 function(answer)
                        {     
                            if(answer=="yes")
                            { 
                                Ext.Ajax.request({
                                    url     : 'index.php/websites/json_delete_banner/'+App.TOKEN,
                                    params  : post,
                                    success : function(response){
                                        var res=YAHOO.lang.JSON.parse(response.responseText);
                                         if(res.result=="1")
                                         {
                                              
                                             Ext.MessageBox.alert({title:"Status",msg:"Advertisement Deleted successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                             config.client_with_all_store.load();
                                             me.getStore().load();   
                                         }
                                    }
                                    ,failure: function(form, action){App.error.xhr(action);}
                                });    
                            }
                        });        
                    }
            }
        );
                                                
        config.title                                    = 'Advertisements'; 
        if(config.fields==null)     config.fields       = ['banner_id','banner_name','banner_filename','client_org_id','client_name','banner_type_id','banner_type_name','banner_script','banner_size_id','size_w','size_h','clickurl','name','clicks','views','isactive'];
        if(config.sorters==null)    config.sorters      = config.fields;
        if(config.pageSize==null)   config.pageSize     = 100;
        if(config.url==null)        config.url          = "/index.php/websites/json_get_org_banners/TOKEN:"+App.TOKEN;
        if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
        if(config.width==null)      config.width        = '100%';
        if(config.height==null)     config.height       = 400;
        if(config.groupField==null) config.groupField   = "client_name";
        
        this.override_edit          =config.override_edit;
        this.override_selectiochange=config.override_selectionchange;
        this.override_itemdblclick  =config.override_itemdblclick;
        this.override_collapse      =config.override_collapse;
        this.override_expand        =config.override_expand;
        
        this.config=config;
        this.callParent(arguments); 
    }                       
});}
