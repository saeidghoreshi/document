if(!websiteForms) var websiteForms={};
    
websiteForms.form_article_details=function(selectFileHandler,article_types_store)
    {   
         var me=this;
         var form=new Ext.spectrumforms(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                           // bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                                                    
                            fieldDefaults   : 
                            {
                                labelAlign  : 'top',
                                labelWidth  : 100,
                                labelStyle  : 'font-weight:bold'
                            },      
                            items: 
                            [
                                //title
                                {                        
                                    xtype       : 'textfield',
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    name        : 'article_title',
                                    emptyText   : 'Title',
                                    fieldLabel  : 'Title',
                                    labelWidth  : 150,          
                                    allowBlank  : false
                                }
                                //(un)publish Dates and type  and auto link?
                                ,{
                                    xtype       : 'fieldcontainer',
                                    fieldLabel  : '',
                                    combineErrors: true,
                                    msgTarget   : 'side',          
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    layout      : 'hbox',        
                                    fieldDefaults: {labelAlign: 'top'},
                                    items:
                                    [
                                         {                        
                                            xtype       : 'datefield',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'publish_date',
                                            emptyText   : 'Publish Date',
                                            fieldLabel  : 'Publish Date',
                                            labelWidth  : 150,          
                                            allowBlank  : false,
                                            format      : 'Y/m/d'
                                        }
                                        ,{                        
                                            xtype       : 'datefield',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'unpublish_date',
                                            emptyText   : 'Unpublish Date',
                                            fieldLabel  : 'Unpublish Date',
                                            labelWidth  : 150,          
                                            allowBlank  : true,
                                            format      : 'Y/m/d',
                                            margins     :'0 0 0 10'
                                        }        
                                        ,{        
                                            id          : 'create_form_article_details_article_type_combo',                
                                            xtype       : 'combo',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'article_type_id',
                                            emptyText   : 'Type',
                                            fieldLabel  : 'Type',
                                            labelWidth  : 150,    
                                            forceSelection: false,
                                            editable    : false,
                                            displayField: 'name',
                                            valueField  : 'id',
                                            queryMode   : 'local',     
                                            allowBlank  : false,
                                            store       : article_types_store,
                                            margins     : '0 0 0 10',
                                            listeners   :
                                            {
                                                buffer:100,
                                                change:function()
                                                {
                                                    if(Ext.getCmp("create_form_article_details_article_type_combo").getValue()=="3")
                                                        Ext.getCmp("create_form_article_details_autolink").setDisabled(false);
                                                    else
                                                        Ext.getCmp("create_form_article_details_autolink").setDisabled(true);
                                                }
                                            }
                                        }
                                        //Autolink
                                       ,{
                                            id          : "create_form_article_details_autolink",
                                            xtype       : 'checkbox',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'autolink',
                                            fieldLabel  : 'Auto link ?',
                                            // listeners   :{change:function(){}},                      
                                            disabled    : true,
                                            margins     : '0 0 0 10'
                                       }        
                                    ]
                               }
                                //Intro
                                ,{                        
                                    xtype       : 'textfield',
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    name        : 'article_intro',
                                    emptyText   : 'Introduction',
                                    fieldLabel  : 'Introduction',
                                    labelWidth  : 150,          
                                    allowBlank  : false
                                }
                                //Content
                                ,{
                                    id          : "form_article_details_editor",
                                    xtype       : 'htmleditor',
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    name        : 'article_content',
                                    emptyText   : '',
                                    fieldLabel  : 'Content',
                                    labelWidth  : 150,          
                                    allowBlank  : false,
                                    
                                    width       : "100%",
                                    height      : 360,
                                    enableColors: false,
                                    enableAlignments: false
                                }
                                //select images  and create auto link for "paged" article
                                ,{
                                    xtype           : 'fieldcontainer',
                                    fieldLabel      : '',
                                    combineErrors   : true,
                                    labelStyle      : 'font-weight:bold;padding:0',
                                    layout          : 'hbox',        
                                    fieldDefaults   : {labelAlign: 'top'},
                                    items:
                                    [
                                        {
                                            xtype       : 'button',
                                            text        : '',
                                            iconCls     : 'photos',
                                            width       : 20,
                                            handler     : selectFileHandler
                                        }
                                    ]
                                }   
                            ]
         });                                                          
         return  form;
    };
          
websiteForms.formLinkDetails=function(pagedArticlesStore,linkTypeStore)
    {   
        var me=this;
        var form=new Ext.spectrumforms(
        {   
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            //bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                                                    
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [   
                                //title
                                {                        
                                    xtype       : 'textfield',
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    name        : 'title',
                                    emptyText   : '',
                                    fieldLabel  : 'Title',
                                    labelWidth  : 150,          
                                    autoWidth   : true,
                                    allowBlank  : false
                                }
                                //type (page/url) and related component
                                ,{
                                    xtype       : 'fieldcontainer',
                                    fieldLabel  : '',
                                    combineErrors: true,
                                    msgTarget   : 'side',          
                                    labelStyle  : 'font-weight:bold;padding:0',
                                    layout      : 'hbox',        
                                    fieldDefaults: {labelAlign: 'top'},
                                    items       :
                                    [
                                        {   
                                            id          : 'form_link_details_type_id',                    
                                            xtype       : 'combo',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'type_id',
                                            emptyText   : '',
                                            fieldLabel  : 'Link Type',
                                            labelWidth  : 150,    
                                            forceSelection: false,
                                            editable    : false,
                                            displayField: 'type_name',
                                            valueField  : 'type_id',
                                            queryMode   : 'local',     
                                            autoWidth   : true,
                                            allowBlank  : false,
                                            triggerAction: 'all',
                                            store       : linkTypeStore,
                                            listeners: 
                                            {
                                                change: 
                                                {
                                                    fn: function(conf,selected_id)
                                                    {
                                                        if(selected_id==2/*Page*/)
                                                        {
                                                            Ext.getCmp("form_link_details_article_id").show();
                                                            Ext.getCmp("form_link_details_url").hide();
                                                        }
                                                        else
                                                        {
                                                            Ext.getCmp("form_link_details_article_id").hide();
                                                            Ext.getCmp("form_link_details_url").show();
                                                        }
                                                        
                                                    },
                                                    buffer:200                  
                                                }
                                            }
                                        } 
                                        
                                        ,{              
                                            id          : 'form_link_details_url',          
                                            xtype       : 'textfield',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'url',
                                            emptyText   : 'http://',
                                            fieldLabel  : 'URL',
                                            labelWidth  : 130,          
                                            autoWidth   : true,
                                            allowBlank  : true,
                                            margins     : '0 0 0 10'
                                        }

                                        ,{  
                                            id          : 'form_link_details_article_id',                                          
                                            xtype       : 'combo',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'article_id',
                                            emptyText   : '',
                                            fieldLabel  : 'Paged Articles',
                                            labelWidth  : 130,    
                                            forceSelection: false,
                                            editable    : false,
                                            displayField: 'type_name',
                                            valueField  : 'type_id',
                                            queryMode   : 'local',     
                                            autoWidth   : true,
                                            allowBlank  : true,
                                            store       : pagedArticlesStore,
                                            margins     : '0 0 0 10',
                                            hidden      :true
                                       }    
                                    ]
                               }

                            ]
         }
        );                     
        return  form;
    };

    
    