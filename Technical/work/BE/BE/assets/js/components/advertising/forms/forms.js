if(!websiteForms)var websiteForms={};
                    

websiteForms.formCampaignDetails=function()
    {    
         var form_campaign_details=new Ext.spectrumforms(
         {   
            centerAll   : true,
            height      : 500,

                                    
            fieldDefaults: { 
                labelWidth: 100
            },      
            items: 
            [
        
                {
                    xtype       : 'textfield',
                    name        : 'campaign_name',
                    fieldLabel  : 'Title',
                    labelWidth  : 150,          
                    autoWidth   : true,
                    allowBlank  : false
               },

                {                        
                    xtype       : 'datefield',
                    format      : 'Y/m/d', 
                    id          : "start_date",
                    name        : 'start_date',
                    fieldLabel  : 'Start Date',
                    labelWidth  : 150,          
                     
                    allowBlank  : false
                }         
                ,{                        
                    xtype       : 'datefield',
                    format      : 'Y/m/d', 
                    id          : "end_date",
                    name        : 'end_date',
                    fieldLabel  : 'End Date',
                    labelWidth  : 150, 
                    allowBlank  : false
                }

            ]
         });
         return  form_campaign_details;
    };                                         
websiteForms.formBannerDetails1=function(client_store,size_store,banner_type_id,newClientHandler)
{   
         
         var me=this;
         var form_banner_details=new Ext.spectrumforms({   

                            autowidth   : true,
                            autoHeight  : true,
                            fileUpload  : true,

                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                                                    
                            fieldDefaults: {
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },
                            items:
                            [
                                {
                                    xtype       : 'fieldset',
                                    title       : 'Advertisement Details',
                                    collapsible : false,
                                    margin      : '3 3 3 3 ',
                                    defaults: 
                                    {
                                        labelWidth  : 89,
                                        anchor      : '100%',
                                        labelAlign  : 'top',
                                        layout      : 
                                        {
                                            type: 'hbox',
                                            defaultMargins: {top: 5, right: 5, bottom: 5, left: 5}
                                        }    
                                    },
                                    items:
                                    [
                                        {                        
                                            xtype       : 'textfield',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'banner_name',
                                            fieldLabel  : 'Banner Name',
                                            labelWidth  : 100,          
                                            flex        : 1,
                                            allowBlank  : false,
                                            margin      : '0 5 0 0'
                                        },
                                        {
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
                                                   xtype       : 'combo',
                                                   id          : 'form_banner_details_client_list',
                                                   name        : 'client_list',
                                                   fieldLabel  : 'Client',
                                                   labelWidth  : 70,
                                                   labelStyle  : 'font-weight:bold',   
                                                   flex        : 1,
                                                   allowBlank  : false,
                                                   mode        : 'local',
                                                   forceSelection: false,
                                                   editable    : false,
                                                   displayField: 'co_org_name',
                                                   valueField  : 'co_org_id',
                                                   queryMode   : 'local',
                                                   typeAhead   : true,
                                                   store       : client_store,
                                                   margin      : '0 5 0 0'
                                                },
                                                {
                                                   xtype    : 'button',
                                                   text     : 'New Client',
                                                   margin   : '10 0 0 0',
                                                   handler  : function()
                                                   {
                                                       websiteForms.load_win_create_new_client();
                                                   }
                                                }   
                                            ]
                                        }
                                    ]
                                }
                            ]
                            
                            });
         return  form_banner_details;
    };

websiteForms.formBannerDetails2=function(client_store,size_store,banner_type_id)
{   
         
         var me=this;
         var form_banner_details=new Ext.spectrumforms({   

                            autowidth   : true,
                            autoHeight  : true,
                            fileUpload  : true,

                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                                                    
                            fieldDefaults: {
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },
                            items:
                            [
                                {
                                    xtype       : 'fieldset',
                                    title       : 'More Details',
                                    collapsible : false,
                                    margin      : '0 0 0 0',
                                    defaults: 
                                    {
                                        labelWidth  : 89,
                                        anchor      : '100%',
                                        labelAlign  : 'top',
                                        layout      : 
                                        {
                                            type: 'hbox',
                                            defaultMargins: {top: 0, right: 0, bottom: 0, left: 0}
                                        }    
                                    },
                                    items:
                                    [
                                       {
                                            xtype       : 'radiogroup',
                                            fieldLabel  : '',
                                            labelAlign  : 'top',
                                            labelWidth  : 70,
                                            columns     : 2,
                                            vertical    : false,
                                            items       :
                                            [
                                                {
                                                   checked     : (banner_type_id==3)?true:false,         
                                                   boxLabel    : 'Script',
                                                   name        : 'script_file',
                                                   inputValue  : '1',
                                                   listeners   :
                                                   { 
                                                       change: 
                                                       {
                                                           fn: function(a,newValue,oldValue)
                                                           {       
                                                               Ext.getCmp('size_list').setDisabled(newValue);
                                                               Ext.getCmp('file_upload').setDisabled(newValue);
                                                               Ext.getCmp('clickurl').setDisabled(newValue);
                                                               Ext.getCmp('script').setDisabled(!newValue);
                                                           }
                                                       }
                                                   }
                                               } 
                                               ,{       
                                                   checked     : (banner_type_id!=3)?true:false,         
                                                   boxLabel    : 'File',
                                                   name        : 'script_file',
                                                   inputValue  : '2',
                                                   listeners   : 
                                                   {
                                                       change: 
                                                       {
                                                           fn: function(a,newValue,oldValue)
                                                           {                                            
                                                               Ext.getCmp('size_list').setDisabled(!newValue);
                                                               Ext.getCmp('file_upload').setDisabled(!newValue);
                                                               Ext.getCmp('clickurl').setDisabled(!newValue);
                                                               Ext.getCmp('script').setDisabled(newValue);
                                                           }
                                                       }
                                                   }
                                               }    
                                            ]
                                       },
                                       {
                                            xtype       : 'combo',
                                            id          : "size_list",
                                            name        : 'size_list',
                                            fieldLabel  : 'banner Size',
                                            labelWidth  : 70,
                                            labelStyle  : 'font-weight:bold',   
                                            flex        : 1,
                                            allowBlank  : false,
                                            mode        : 'local',
                                            forceSelection: false,
                                            editable    : false,
                                            displayField: 'size_name',
                                            valueField  : 'size_id',
                                            queryMode   : 'local',
                                            store       : size_store,
                                            margins     :'0 0 0 5',
                                            disabled      :(banner_type_id!=3)?false:true
                                            
                                       },
                                       {                        
                                            xtype       : 'filefield',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'file_upload',
                                            id          : 'file_upload',
                                            emptyText   : 'Select banner file ',
                                            fieldLabel  : '',
                                            labelWidth  : 150,          
                                            flex        :1,
                                            allowBlank  : false,
                                            disabled    :(banner_type_id!=3)?false:true
                                            
                                       },
                                       {             
                                            xtype       : 'textfield',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'clickurl',
                                            id          : 'clickurl',
                                            emptyText   : 'http://',
                                            fieldLabel  : 'Click Url',
                                            labelWidth  : 150,          
                                            flex        : 1,
                                            allowBlank  : true,
                                            disabled      :(banner_type_id!=3)?false:true
                                       },         
                                       {                        
                                                     xtype       : 'textareafield',
                                                     labelStyle  : 'font-weight:bold;padding:0',
                                                     name        : 'script',
                                                     id          : 'script',
                                                     fieldLabel  : 'Script',
                                                     labelWidth  : 150,          
                                                     flex        : 1,
                                                     height      : 250,
                                                     allowBlank  : false,
                                                     margins     : '0 0 0 5',
                                                     disabled    :(banner_type_id==3)?false:true
                                                }
                                    ]
                                }
                            ]
                            
                            });
         return  form_banner_details;
    };
websiteForms.formBannerPosition=function(positions_store)
{
        var me=this;
        var formBannerPosition=new Ext.spectrumforms({   

                            autowidth   : true,
                            autoHeight  : true,
                            fileUpload  : true,

                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                                                    
                            fieldDefaults: {
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },
                            items:
                            [    
                                {
                                                  xtype       : 'combo',
                                                  width       : 300,
                                                  name        : 'pos_id',
                                                  //id          : 'pos_id',
                                                  fieldLabel  : 'Desirable Position',
                                                  labelWidth  : 120,
                                                  labelStyle  : 'font-weight:bold',   
                                                  allowBlank  : false,
                                                  mode        : 'local',
                                                  forceSelection: false,
                                                  editable    : false,
                                                  displayField: 'type_name',
                                                  valueField  : 'type_id',
                                                  queryMode   : 'local',
                                                  store       : positions_store,      
                                                  margins     :'0 0 0 5'
                                } 
                                ,{
                                                  xtype       : 'textfield',
                                                  width       : 300,
                                                  name        : 'max_views',
                                                  fieldLabel  : 'Max(Views#)',
                                                  labelWidth  : 120,
                                                  labelStyle  : 'font-weight:bold',   
                                                  allowBlank  : true,
                                                  mode        : 'local',
                                                  forceSelection: false,
                                                  editable    : false,
                                                  displayField: 'name',
                                                  valueField  : 'value',
                                                  queryMode   : 'local',
                                                  margins     : '0 0 0 5',
                                                  emptyText   : 'Maximum'
                                } 
                                ,{
                                                  xtype       : 'textfield',
                                                  width       : 300,
                                                  name        : 'max_clicks',
                                                  fieldLabel  : 'Max(Clicks#)',
                                                  labelWidth  : 120,
                                                  labelStyle  : 'font-weight:bold',   
                                                  allowBlank  : true,
                                                  mode        : 'local',
                                                  forceSelection: false,
                                                  editable    : false,
                                                  displayField: 'name',
                                                  valueField  : 'value',
                                                  queryMode   : 'local',
                                                  margins     : '0 0 0 5',
                                                  emptyText   : 'Maximum'
                                 }
                            ]
        });
        return formBannerPosition;
}
                                          

//embeded newclient window inside form_banner-details
websiteForms.load_win_create_new_client=function()
    {
        var formClientDetailsConfig=
        {
            width   : 400,
            height  : 260,
            bottomItems:   
            [
                "->"
                ,{
                        xtype   :"button",
                        text    :"Save",
                        iconCls :'table_save',
                        pressed :true,
                        width   :70,
                        tooltip :'Save Client',
                        handler :function()
                        {
                            var form    =formClientDetails.getForm();
                            if (form.isValid()) 
                            {   
                                form.submit(
                                {
                                    url     : 'index.php/websites/json_new_client/TOKEN:'+App.TOKEN,
                                    params  : form.getValues(),
                                    success : function(form, action)
                                    {
                                        var res             =YAHOO.lang.JSON.parse(action.response.responseText);
                                        var resultSplitted  =res.result.split(',');
                                        
                                        if(resultSplitted[0]=="1")
                                        {
                                            Ext.getCmp("form_banner_details_client_list").store.load();
                                            Ext.getCmp("form_banner_details_client_list").store.on("load",function()
                                            {                         
                                                var index   =Ext.getCmp("form_banner_details_client_list").store.find( 'co_org_name', formClientDetailsFinal.getForm().getValues()["client_name"], 0, true, false, false);
                                                var rec     =Ext.getCmp("form_banner_details_client_list").store.getAt(index);
                                                Ext.getCmp("form_banner_details_client_list").select(rec);
                                            }); 
                                           formClientDetailsWin.Hide();
                                        }           
                                    },
                                    failure: function(form, action){alert(action.response.responseText);}
                                });
                            }   
                        }                          
                }
            ]
        }          
        var formClientDetails       =websiteForms.formClientDetails();
        var formClientDetailsFinal  =new Ext.spectrumforms.mixer(formClientDetailsConfig,[formClientDetails],['form']);
        var formClientDetailsWinConfig=
        {
            title       : 'New Client',
            final_form  : formClientDetailsFinal
        }
 
        var formClientDetailsWin=new Ext.spectrumwindow.advertising(formClientDetailsWinConfig);
        formClientDetailsWin.show();
    };
websiteForms.formClientDetails=function()
    {   
         var me=this;
         var form_client_details=new Ext.spectrumforms(
 
 		{   
                autowidth   : true,
                autoHeight  : true,
                
                layout      : {type: 'vbox',align: 'stretch'},
                 
                border      : false,
                                        
                fieldDefaults: {
                    labelAlign: 'top',
                    labelWidth: 100
                },      
                items: 
                [
 
                    {                        
                        xtype       : 'textfield',
                        labelStyle  : 'font-weight:bold;padding:0',
                        name        : 'client_name',
                        emptyText   : 'Company name',
                        fieldLabel  : 'Company Name',
                        labelWidth  : 150,          
                        allowBlank  : false
                    }
                    ,{
                        xtype       : 'fieldcontainer',
                        fieldLabel  : 'Contact Person Name',
                        combineErrors: true,
                        msgTarget   : 'side',          
                        labelStyle  : 'font-weight:bold;padding:0',
                        layout      : 'hbox',        
                        fieldDefaults: {labelAlign: 'top'},
                        items:
                        [
                             {                        
                                xtype       : 'textfield',
                                labelStyle  : 'font-weight:bold;padding:0',
                                name        : 'first_name',
                                emptyText   : 'First Name',
                                fieldLabel  : '',
                                labelWidth  : 150,          
                                flex        :1,
                                allowBlank  : false
                            }
                            ,{                        
                                xtype       : 'textfield',
                                labelStyle  : 'font-weight:bold;padding:0',
                                name        : 'last_name',
                                emptyText   : 'Last Name',
                                fieldLabel  : '',
                                labelWidth  : 150,          
                                flex        :1,
                                allowBlank  : false,
                                margins     :'0 0 0 5'
                            }        
                        ]
                   }
                   ,{
                        xtype       : 'fieldcontainer',
                        fieldLabel  : 'Contact Phone',
                        combineErrors: true,
                        msgTarget   : 'side',          
                        labelStyle  : 'font-weight:bold;padding:0',
                        layout      : 'hbox',        
                        fieldDefaults: {labelAlign: 'top'},
                        items:
                        [
                            {                        
                                xtype       : 'textfield',
                                labelStyle  : 'font-weight:bold;padding:0',
                                name        : 'area_code',
                                emptyText   : 'Area',
                                maskRe:/\d/  ,
                                maxLength:3,
                                fieldLabel  : '',
                                labelWidth  : 150,          
                                width       : 50,
                                allowBlank  : false ,
                                margins     : '0 0 0 5'   
                            }
                            ,{xtype: 'displayfield', value: '-' ,margins     : '0 0 0 5'   }
                            ,{                        
                                xtype       : 'textfield',
                                labelStyle  : 'font-weight:bold;padding:0',
                                name        : 'phone_first3',
                                fieldLabel  : '',
                                labelWidth  : 150,          
                                width       : 50,
                                allowBlank  : false,
                                maskRe      :/\d/  ,
                                maxLength   :3,
                                margins     : '0 0 0 5'   
                            }        
                            ,{xtype: 'displayfield', value: '-' ,margins     : '0 0 0 5'   }
                            ,{                        
                                xtype       : 'textfield',
                                labelStyle  : 'font-weight:bold;padding:0',
                                name        : 'phone_last4',
                                maskRe      :/\d/  ,
                                maxLength   :4,
                                fieldLabel  : '',
                                labelWidth  : 150,          
                                width       : 70,
                                allowBlank  : false,
                                margins     : '0 0 0 5'
                            }        
                        ]
                   } 
                   ,{                        
                        xtype       : 'textfield',
                        labelStyle  : 'font-weight:bold;padding:0',
                        name        : 'client_email',
                        emptyText   : 'Contact Person Email',
                        fieldLabel  : 'Email',
                        labelWidth  : 150,          
                        allowBlank  : false,
                        vtype       :"email"
                    }
                ]
 
});
 
         return  form_client_details;
    };
websiteForms.show_image_on_shadow=function(url,script,file_type,_title,_width,_height)
    {         
        var html_tag;
         if(file_type=='flash')
            html_tag='<embed src="'+url+'" quality="high" width="'+_width+'" height="'+_height+'" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>'
         if(file_type=='image')
            html_tag='<img src="'+url+'" style="width:'+_width+' height:'+_height+'"" />';
         html_tag+=script.replace(/[\\]/gi,'');;
            
        Shadowbox.open
        ({
            content :   html_tag,
            player  :   "html",
            title   :   _title,
            width   :   parseInt(_width)+1,
            height  :   parseInt(_height)+2
        });           
    };
