if(!App.dom.definedExt('Ext.spectrumgrids.article')){
Ext.define('Ext.spectrumgrids.article',    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    }
    
    ,article_types_store     :null
    ,constructor : function(config) 
    {   
        var me=this;              
        me.article_types_store =new simpleStoreClass().make(['id','name'],"index.php/websites/json_get_article_types/TOKEN:"+App.TOKEN+'/',{test:"test"});
        
        config.title='Website Articles';
        config.columns  =
        [    
             {
                text        : "Title"
                ,dataIndex  : 'article_title'
                ,width      :200
                ,editor: 
                {
                    xtype       :'textfield'
                    ,allowBlank  : false
                }
            }
            ,{
                text        : "Type"
                ,dataIndex  : 'article_type_name'
                , width     : 200
                , editor: { 
                     xtype          : 'combo'
                    , allowBlank    : false
                    , editable      : false
                    , valueField    : 'name'
                    , displayField  : 'name'  
                    , queryMode     : 'local'
                    , store         : config.article_types_store
                }
            }
            ,{
                text        : "Post Date"
                , dataIndex : 'created_on_display'
                , width     :100
                , format    : 'Y/m/d'
            }
            ,{
                  text      : "Modify Date"
                , dataIndex : 'modified_on_display'
                , width     :100
                , format    : 'Y/m/d'
            }   
            ,{
                text        : "Publish Date"
                , dataIndex : 'publish_date_display'
                , width     :100
                , format    : 'Y/m/d'
                , field     : { xtype: 'datefield', format: 'Y/m/d', allowBlank: true}
            }
            ,{
                  text      : "Unpublish Date"
                , dataIndex : 'unpublish_date_display'
                , width     :100
                , format    : 'Y/m/d'
                , field     : { xtype: 'datefield', format: 'Y/m/d', allowBlank: true}
            }
            ,{
                text        : "# of Views"
                ,dataIndex  : 'views'
                ,flex      : 1
            }
        ];
        //Fresh top/bottom components list
        if(config.topItems==null)     config.topItems=[];
        if(config.bottomLItems==null) config.bottomLItems=[];
        if(config.bottomRItems==null) config.bottomRItems=[];
        
               
        config.bottomLItems.push
        (
            //Create Article
            {
                id          : 'spectrumgrids_article_create_btn',
                iconCls     : 'fugue_blog--plus',
                xtype       : 'button',
                text        : '',
                tooltip     : 'Create New Article',
                handler     : function()
                {
                    var selectFileHandler=function()
                    {
                        var dv= Ext.create('Ext.spectrumdataviews.imagePicker',
                        {
                            generator       : Math.random(),
                            title           : '',
                            url             : "index.php/websites/json_get_articleImages/"+App.TOKEN,
                            uploadhandler   : function()
                            {          
                                     
                                if(Ext.getCmp("spectrumdataview_template_file_comp").getValue()=='')
                                {
                                    Ext.MessageBox.alert({title:"Cannot Upload",msg:"Please Select a File to Upload", 
                                            icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                    return;
                                }
                                final_form_newArticleIR.getForm().submit(
                                {
                                       url      : '/index.php/websites/json_upload_article_image/'+App.TOKEN,
                 
                                       success  : function(form, action)
                                       {
                                            console.log(action.response.responseText);
                                            dv.dataview.getStore().load();
                                            Ext.getCmp("spectrumdataview_template_file_comp").setValue('');
                                            Ext.getCmp("spectrumdataview_template_file_comp").setRawValue('');
                                       },
                                       failure: function(form, action)
                                       {
                                       	   console.log(action.response.responseText);return;//for now
										   try
										   {
											   var res=YAHOO.lang.JSON.parse(action.response.responseText);
											   
										   }
										   catch(e)
										   {
											   App.error.xhr(action.response);
											   return;
										   }
										   Ext.MessageBox.alert('Cannot Upload',res.result);
										   
                                       }
                                });                                                       
                            }
                        }); 
                        var config_newArticleIR=
                        {
                            width   :450,
                            height  :400,
                            bottomItems:
                            [
                                '->'
                                ,{
                                    id      : '',
                                    iconCls : 'disk',
                                    text    : 'Apply',
                                    cls     : 'x-btn-default-small',
                                    xtype   : 'button',
                                    tooltip : 'Apply to the Content',
                                    handler : function()
                                    {
                                    	//do not apply if its empty
                                    	if(Ext.getCmp("spectrumdataview_template_url_comp") && Ext.getCmp("spectrumdataview_template_url_comp").getValue())
                                    	{
											 Ext.getCmp("form_article_details_editor").setValue
	                                       (
	                                           Ext.getCmp("form_article_details_editor").getValue()+
	                                           '<img src='+Ext.getCmp("spectrumdataview_template_url_comp").getValue()+' >'
	                                       ); 
                                    	}   
                                    	
                                    	var w = this.up('window');
                                    	if(w && typeof w.hide=='function') w.hide();                                   
                                    }
                                }
                            ]    
                        }
                        var final_form_newArticleIR=new Ext.spectrumforms.mixer(config_newArticleIR,[dv],['grid']);
                        var win_ir=
                        {
                            title       : 'Pick from Saved Photos',
                            final_form  : final_form_newArticleIR
                        }
                        var win_ir=new Ext.spectrumwindow.publishing(win_ir);
                        win_ir.show();    
                    }
                    var formNewArticleConfig=
                    {
                    	//these dont work if user is in windowed mode, smaller than screen
                       // width   : 0.7 * parseInt(screen.availWidth)/2,
                       // height  : 0.7 * parseInt(screen.availWidth),
                        width   : 500,
                        height  : 600,
                        resizable:true,
                        bottomItems:   
                        [
                            '->'
                            ,{   
                                 xtype   :"button",
                                 text    :"Save",
                                 iconCls :'table_save',
                                 pressed :true,
                                 tooltip :'Save',
                                 handler :function()
                                 {       
                                    if (!formNewArticleForm.getForm().isValid()) {return false;}
                                    
                                    var post                        = formNewArticleForm.getForm().getValues();
                                    post["article_content_revised"] = post["article_content"].replace(/"/gi,'');
                                    
                                    var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                    Ext.Ajax.request(
                                    {
                                        url     : 'index.php/websites/json_new_article/'+App.TOKEN,
                                        params  : post,
                                        success : function(o)
                                        {
                                            box.hide();
                                            var res=YAHOO.lang.JSON.parse(o.responseText);
                                            if(res.result=="1")Ext.MessageBox.alert({title:"Status",msg:"Article <b>"+post["article_title"]+"</b> Created Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                            me.getStore().load();
                                            
                                            formNewArticleWin.Hide()
                                            Ext.getCmp("spectrumgrids_article_publish_btn").hide();
                                    },
                                    failure: App.error.xhr
                                    });
                                 }                          
                            }
                        ]
                    }          
                    var formNewArticleForm      =websiteForms.form_article_details(selectFileHandler,me.article_types_store);
                    
                    
                    var formNewArticleFinalForm =new Ext.spectrumforms.mixer(formNewArticleConfig,[formNewArticleForm],['form']);
                    var formNewArticleWinConfig =
                    {
                        title       : 'Create New Article',
                        final_form  : formNewArticleFinalForm
                    }
                    var formNewArticleWin=new Ext.spectrumwindow.publishing(formNewArticleWinConfig);
                    formNewArticleWin.show();
                }   
            }
        );
            
        config.bottomRItems.push
        (
             //Un/Publish
             {
                id      : "spectrumgrids_article_publish_btn",
                iconCls : 'fugue_blog--arrow', //fugue_blog--minus
                xtype   : 'button',
                hidden  :false,
                tooltip : 'Publish Selected Article',
                handler : function()
                {                                                                   
                    if(me.getSelectionModel().getSelection().length==0)
                    {
                       // Ext.MessageBox.alert({title:"Error",msg:"Please Select an Article", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        return;
                    }
                    var record=me.getSelectionModel().getSelection()[0].data;
                   
                   // var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                    Ext.Ajax.request(
                    {
                        url     : 'index.php/websites/json_publish_or_unpublish_article/'+App.TOKEN+'/',
                        params  : record,
                        scope   : this,
                        success : function(o)
                        {
                         //   box.hide();
                            var res=YAHOO.lang.JSON.parse(o.responseText);
                            if(res.result=="1")Ext.MessageBox.alert({title:"Status",msg:"Article Published Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                            if(res.result=="2")Ext.MessageBox.alert({title:"Status",msg:"Article UnPublished Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                            me.getStore().load();
                            Ext.getCmp("spectrumgrids_article_publish_btn").hide();
                    },
                    failure: App.error.xhr
                    });    
                }
             }
             ,'-'
             //Edit
             ,{
                id      : 'spectrumgrids_article_edit_btn',
                iconCls : 'fugue_blog--pencil',
                xtype   : 'button',
                tooltip : 'Modify Selected Article',
                handler : function()
                {
                    if(me.getSelectionModel().getSelection().length==0)
                    {
                       Ext.MessageBox.alert({title:"Cannot Modify",msg:"Please Select an Article", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK}); 
                       return;
                    }
                    
                    var record  =me.getSelectionModel().getSelection()[0].data;
                    
                    var selectFileHandler=function()
                    {
                        var dv= Ext.create('Ext.spectrumdataviews.imagePicker',
                        {
                            generator       : Math.random(),
                            title           : '',
                            url             : "index.php/websites/json_get_articleImages/"+App.TOKEN,
                            uploadhandler   : function()
                            {          
                                     
                                if(Ext.getCmp("spectrumdataview_template_file_comp").getValue()=='')
                                {
                                    Ext.MessageBox.alert({title:"Cannot upload",msg:"Please Select a File to Upload", 
                                            icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                    return;
                                }
                                final_form_newArticleIR.getForm().submit(
                                {
                                       url      : '/index.php/websites/json_upload_article_image/'+App.TOKEN,
                 
                                       success  : function(form, action)
                                       {
                                            dv.dataview.getStore().load();
                                            Ext.getCmp("spectrumdataview_template_file_comp").setValue('');
                                            Ext.getCmp("spectrumdataview_template_file_comp").setRawValue('');
                                       },
                                       failure: function(form, action){alert(YAHOO.lang.dump(action.response));}
                                });                                                       
                            }
                        }); 
                        var config_newArticleIR=
                        {
                            width   :450,
                            height  :350,
                            bottomItems:
                            [
                                '->'
                                ,{
                                    id      : '',
                                    iconCls : 'disk',
                                    text    : 'Apply',
                                    cls:'x-btn-default-small',
                                    xtype   : 'button',
                                    tooltip : 'Apply to the Content',
                                    handler : function()
                                    {
                 
                                      // do not apply if nothing selected - only if non empty
                                      if(Ext.getCmp("spectrumdataview_template_url_comp") && Ext.getCmp("spectrumdataview_template_url_comp").getValue())
                                       {
                                       	   Ext.getCmp("form_article_details_editor").setValue
	                                       (
	                                           Ext.getCmp("form_article_details_editor").getValue()+
	                                           '<img src='+Ext.getCmp("spectrumdataview_template_url_comp").getValue()+' >'
	                                       );  
	                                   }
                                       
                                       
                                    	var w = this.up('window');
                                    	if(w && typeof w.hide=='function') w.hide();      
                                    }
                                }
                            ]    
                        }
                        var final_form_newArticleIR=new Ext.spectrumforms.mixer(config_newArticleIR,[dv],['grid']);
                        var win_ir=
                        {
                            title       : 'Pick from Saved Photos',
                            final_form  : final_form_newArticleIR
                        }
                        var win_ir=new Ext.spectrumwindow.publishing(win_ir);
                        win_ir.show();    
                    }
                    var formEditArticleConfig=
                    {
                        //these dont work if user is in windowed mode, smaller than screen
                       // width   : 0.7 * parseInt(screen.availWidth)/2,
                       // height  : 0.7 * parseInt(screen.availWidth),
                        width   : 500,
                        height  : 600,
                        bottomItems:   
                        [
                            '->'
                            ,{   
                                 xtype   :"button",
                                 text    :"Update",
                                 iconCls :'table_save',
                                 pressed :true,
                                 tooltip :'Update',
                                 handler :function()
                                 {       
                                    if(me.getSelectionModel().getSelection().length==0) 
                                    {
                                        Ext.MessageBox.alert({title:"Cannot Update",msg:"Please Select an Article", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                        return;
                                    }
                                    var record  = me.getSelectionModel().getSelection()[0].data;
                                     
                                    if (!formEditArticleForm.getForm().isValid()) {return false;}
                                    
                                    var post                        = formEditArticleForm.getForm().getValues();
                                    post["article_content_revised"] = post["article_content"].replace(/"/gi,'');
                                    post["article_id"]              = record.article_id;
                                    
                                    var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                    Ext.Ajax.request(
                                    {
                                        url     : 'index.php/websites/json_update_article/'+App.TOKEN,
                                        params  : post,
                                        success : function(o)
                                        {
                                            box.hide();
                                            var res=YAHOO.lang.JSON.parse(o.responseText);
                                            if(res.result=="1")Ext.MessageBox.alert({title:"Status",msg:"Article <b>"+post["article_title"]+"</b> Updated Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                            me.getStore().load();
                                            
                                            formEditArticleWin.Hide()
                                            
                                            Ext.getCmp("spectrumgrids_article_publish_btn").hide();
                                    },
                                    failure: App.error.xhr
                                    });
                                 }                          
                            }
                        ]
                    }          
                    var formEditArticleForm     =websiteForms.form_article_details(selectFileHandler,me.article_types_store);
                    
                    
                    var formEditArticleFinal    =new Ext.spectrumforms.mixer(formEditArticleConfig,[formEditArticleForm],['form']);
                    var formEditArticleWinConfig =
                    {
                        title       : "Update Article <b>"+record.article_title+"</b>",
                        final_form  : formEditArticleFinal
                    }
                    var formEditArticleWin      =new Ext.spectrumwindow.publishing(formEditArticleWinConfig);
                    formEditArticleWin.show();                                                                            
                      
                    //Load record
                    var Gen=Math.random(); 
                    Ext.define('model_'+Gen,{extend: 'Ext.data.Model',fields: ['article_title','publish_date','unpublish_date','article_type_id','article_intro','article_content' ]});
                    formEditArticleForm.loadRecord(Ext.ModelManager.create(
                    {
                          'article_title'      : record.article_title
                          ,'publish_date'       : record.publish_date_display
                          ,'unpublish_date'     : record.unpublish_date_display
                          ,'article_type_id'    : record.article_type_id
                          ,'article_intro'      : record.article_intro
                          ,'article_content'    : record.article_content
                    }, "model_"+Gen));           
                }
                }
              
             //Delete
             ,{
                id      : "spectrumgrids_article_delete_btn",
                iconCls : 'fugue_minus-button',
                xtype   : 'button',
                tooltip : 'Remove Selected Article',
                handler : function()
                {
                    if(me.getSelectionModel().getSelection().length==0)
                    {
                        Ext.MessageBox.alert({title:"Cannot Remove",msg:"Please Select an Article", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        return;
                    }
                    var post={}
                    post['article_id']=me.getSelectionModel().getSelection()[0].data.article_id;
                    Ext.MessageBox.confirm('Delete Action', "Are you sure ?", function(answer)
                    {     
                        if(answer=="yes")
                        {
                            var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                            Ext.Ajax.request(
                            {
                                url     : 'index.php/websites/json_delete_article/TOKEN:'+App.TOKEN,
                                params  : post,
                                success : function(o)
                                {
                                     box.hide();
                                     var res=YAHOO.lang.JSON.parse(o.responseText);
                                     if(res.result=="1")
                                     {          
                                         Ext.MessageBox.alert({title:"Status",msg:"Article Seleted Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                         me.getStore().load();   
                                     }
                                },
                                failure: function(o){alert(YAHOO.lang.dump(o.responseText));}
                            });    
                        }
                    }); 
                }
             }
        );    
            
            
        if(config.fields==null)     config.fields       = ['article_id','article_title','article_intro','article_type_id','article_type_name','article_content','publish_date','publish_date_display','unpublish_date','unpublish_date_display','views','created_by','created_by_name','modified_by','modified_by_name','created_on_display','modified_on_display'];
        if(config.sorters==null)    config.sorters      = config.fields;
        if(config.pageSize==null)   config.pageSize     = 100;
        if(config.url==null)        config.url          = "/index.php/websites/json_get_articles/"+App.TOKEN;
        if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
        if(config.width==null)      config.width        = '100%';
 
        if(config.groupField==null) config.groupField   = "";
        
        this.override_edit          =config.override_edit;
        this.override_selectiochange=config.override_selectionchange;
        this.override_itemdblclick  =config.override_itemdblclick;
        this.override_collapse      =config.override_collapse;
        this.override_expand        =config.override_expand;
        
        this.config=config;
        this.callParent(arguments); 
    },
        afterRender: function() 
        {  
            var me=this;
            if(!this.override_edit)
            {   
                this.on("edit",function(e)
                {
                    var record=me.getSelectionModel().getSelection()[0].data;
                    var post=record;
                    post["publish_date"]    =record["publish_date_display"];
                    post["unpublish_date"]  =record["unpublish_date_display"];
                    post["article_id"]      =record["article_id"];
                    post["article_title"]   =record["article_title"];
                    post["article_type_name"]=record["article_type_name"];
                    post["article_content_revised"] =post["article_content"].replace(/"/gi,'');//same fix that form uses
                     
                    Ext.Ajax.request(
                    {
                        url     : 'index.php/websites/json_update_article/'+App.TOKEN,
                        params  : post,
                        success : function(o)
                        { 
                            var res=YAHOO.lang.JSON.parse(o.responseText);
                            if(res.result=="1")
                            {
                                me.getStore().load();   
                                Ext.getCmp("spectrumgrids_article_publish_btn").hide();
                            }
                    },
                    failure: App.error.xhr
                    });    
                }); 
                
            }
            if(!this.override_selectionchange)                              
            {   
                this.on("selectionchange",function(sm,records)
                {
                	 var sel = me.getSelectionModel().getSelection();
                    if(!sel.length || typeof sel[0]=='undefined' || sel[0]==null)return false;
                    
                    var record=sel[0].data;
                       
                    var p_date=new Date(record.publish_date_display);
                    p_date=new Date(p_date.getFullYear(),p_date.getMonth(),p_date.getDate());   
                    var u_date=new Date(record.publish_date_display);
                    u_date=new Date(u_date.getFullYear(),u_date.getMonth(),u_date.getDate());   
                    var now=new Date();
                    now=new Date(now.getFullYear(),now.getMonth(),now.getDate());   
                                          
                    if((p_date<=now && u_date>=now) || record.unpublish_date_display==null)
                    {
                        Ext.getCmp("spectrumgrids_article_publish_btn").setIconCls("fugue_blog--minus");
                        Ext.getCmp("spectrumgrids_article_publish_btn").setTooltip("Unpublish Selected Article");
                        Ext.getCmp("spectrumgrids_article_publish_btn").show();
                    }
                        
                    else
                    {
                        Ext.getCmp("spectrumgrids_article_publish_btn").setIconCls("fugue_blog--arrow");
                        Ext.getCmp("spectrumgrids_article_publish_btn").setTooltip("Publish Selected Article");
                        Ext.getCmp("spectrumgrids_article_publish_btn").show();
                    }                                                          
                });
            }
                          
            this.callParent(arguments);         
        }
});}









//depreciated code:

/*
                        var handler_addImages=function()
                        {
                             
                            var gen=Math.random(); 
                            var config_dv=
                            {
                                    generator       : Math.random(),
                                    title           : '',
                                    url             : "index.php/websites/json_get_articleImages/TOKEN:"+App.TOKEN,
                                    uploadhandler   : function()
                                    {  
                                       _final_form.getForm().submit(
                                       {
                                                  url: '/index.php/websites/json_upload_article_image/TOKEN:'+App.TOKEN,
                                                  waitMsg: 'Uploading...',
                                                  params: {test:true},
                                                  success: function(form, action)
                                                  {
                                                      var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                      dv.store.load();
                                                      Ext.getCmp("spectrumdataview_template_file_comp").setRawValue('');
                                                  },
                                                  failure: function(form, action){alert(YAHOO.lang.dump(action.response));}
                                       });                                                       
                                    }
                            }
                            var dv= Ext.create('Ext.spectrumdataviews.imagePicker',config_dv);   
                            var config_form=
                            {
                                    width   :450,
                                    height  :350,
                                    bottomItems:
                                    [
                                        '->'
                                        ,{
                                            id      : '',
                                            iconCls : 'table_save',
                                            text    : 'Apply',
                                            xtype   : 'button',
                                            tooltip : 'Apply to the Content',
                                            handler : function()
                                            {
                                               var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status'); 
                                               Ext.getCmp("form_article_details_editor").setValue
                                               (
                                                   Ext.getCmp("form_article_details_editor").getValue()+
                                                   "<img src="+Ext.getCmp("spectrumdataview_template_url_comp").getValue()+" />"
                                               ); 
                                               //Fake Delay
                                               setTimeout(function(){box.hide();},200);
                                            }
                                        }
                                    ]    
                            }
                            
                            var _final_form=new Ext.spectrumforms.mixer(config_form,[dv]);
                            var left    = 100;
                            var top     = parseInt((screen.availHeight/2)   - 450/2);
                            var win_ir=
                            {
                                x           :left,
                                y           :top,
                                title       : 'Pick from Saved Photos',
                                final_form  : _final_form
                            }
                            var win_ir=new Ext.spectrumwindow.publishing(win_ir);
                            win_ir.show();
                            
                        }
                        
                        var form=websiteForms.create_form_article_details(handler_addImages,config.article_types_store);
                        var config_editArticle=
                        {
                            width   :450,
                            height  :450,
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
                                                    var values=form.getForm().getValues();
                                                    if(new Date(values["publish_date"])>new Date(values["unpublish_date"]))
                                                    {
                                                        Ext.MessageBox.alert({title:"Error",msg:"Publish Date is greater than Unpublish date", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                                        return;
                                                    }
                                                    var post =form.getForm().getValues();
                                                    post["article_content_revised"] =post["article_content"].replace(/"/gi,'');
                                                    post["article_id"]      =record.article_id;
                                                    
                                                    if (form.getForm().isValid()) 
                                                    {   
                                                        form.getForm().submit({
                                                            url: 'index.php/websites/json_update_article/TOKEN:'+App.TOKEN,
                                                            //waitMsg: 'Processing...',
                                                            params: post,
                                                            success: function(form, action)
                                                            {
                                                                var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                                if(res.result=="1")
                                                                {
                                                                    Ext.MessageBox.alert({title:"Status",msg:"Article Updated Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                    win.Hide();
                                                                    me.getStore().load();
                                                                }
                                                                
                                                            },
                                                            failure: function(form, action){alert(YAHOO.lang.dump(action.response));}
                                                        });
                                                    }            
                                                }                          
                                        }
                            ]                   
                        }                         
                        
                        
                        var final_form=new Ext.spectrumforms.mixer(config_editArticle,[form]);
                        var win_cnf=
                        {
                            title       : 'Edit Article('+record.article_title+')',
                            final_form  : final_form
                        }
                        var win=new Ext.spectrumwindow.publishing(win_cnf);
                        win.show();
                        */
