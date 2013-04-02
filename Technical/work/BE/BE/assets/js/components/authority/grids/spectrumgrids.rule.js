Ext.define('Ext.spectrumgrids.rule',    
{
        extend: 'Ext.spectrumgrids', 
        initComponent: function(config) 
        {       
            this.callParent(arguments);
        },
        
         override_edit          :null
        ,override_selectiochange:null
        ,override_itemdblclick  :null
        ,override_collapse      :null
        ,override_expand        :null
        
        ,config                 :null
        ,constructor : function(config) 
        {                         
            var me=this;
            config.columns  =
            [   
                {
                    text    : 'Rule Details',
                    columns :
                    [
                        {
                            text        : "Rule Name"
                            ,dataIndex  : 'rule_type_name'
                            ,width      :100
                            
                        }
                        ,{
                            text        : "Rule Code"
                            ,dataIndex  : 'rule_type_code'
                            ,width      :100
                            
                        }
                        ,{
                            text        : "Value"
                            ,dataIndex  : 'rule_value'
                            ,width      :100
                        }
                    ]
                }
                ,{
                    text        : "Status"
                    ,xtype      :'templatecolumn'
                    ,tpl        :'<div style=text-align:center;>{[values.motion_status_id == "1" ? "<img src='+config.imageBaseUrl+'hourglass.png>" :(values.motion_status_id  == "2" ? "<img src='+config.imageBaseUrl+'tick.png>" : (values.motion_status_id == "3" ? "<img src='+config.imageBaseUrl+'delete.png>" :"<img src='+config.imageBaseUrl+'lightning.png>"))]}'      
                    ,width      : 70
                }
                ,{
                    text        : "Created By"
                    ,dataIndex  : 'created_by_name'
                    ,flex       :1     
                }                                
                
            ];
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
            
            var motionStatus    =new simpleStoreClass().make(['type_id','type_name'],"index.php/finance/json_get_motion_status/"+App.TOKEN+'/',{test:true});
            
            config.topItems.push
            (
                //  FILTER BASED ON MOTION-RULE STATUS
                {  
                    xtype       : 'combo',
                    labelStyle  : 'font-weight:bold;padding:0',
                    name        : 'motion_status_id',
                    emptyText   : '',
                    fieldLabel  : 'Motion Status',
                    labelWidth  : 100,    
                    forceSelection: false,
                    editable    : false,
                    displayField: 'type_name',
                    valueField  : 'type_id',
                    queryMode   : 'local',     
                    allowBlank  : false,
                    store       : motionStatus,
                    listeners   :
                    {
                        change:function(_this,selected_id)
                        {             
                            me.getStore().proxy.extraParams.motion_status_id=selected_id;
                            me.getStore().load();
                        }                        
                        
                    }    
                }
            );
            
            config.bottomRItems.push
            (
                 //make motion request
                 {
                        iconCls : 'application_form_add',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Apply a motion',
                        handler : function()
                        {
                            var _config=
                            {
                                width   : 400,
                                height  : 280,
                                bottomItems:   
                                [
                                    '->'
                                    ,{   
                                         xtype   :"button",
                                         text    :"Send",
                                         iconCls :'table_save',
                                         pressed :true,
                                         width   :70,
                                         tooltip :'Send Your Motion',
                                         handler :function()
                                         {                     
                                             
                                             if (form.getForm().isValid()) 
                                             {   
                                                 var post={}
                                                 post=form.getForm().getValues();
                                                 form.getForm().submit({
                                                     url     : 'index.php/finance/json_new_motion_rule/TOKEN:'+App.TOKEN,
                                                     waitMsg : 'Processing ...',
                                                     params  : post,
                                                     success : function(form, action)
                                                     {
                                                         var res=YAHOO.lang.JSON.parse(action.response.responseText);
                                                         if(res.result=="1")
                                                         {
                                                             win.Hide();
                                                             Ext.MessageBox.alert(
                                                             {
                                                             	 title:"Status",
                                                             	 msg:"Request Sent Successfully", 
                                                             	 icon: Ext.Msg.INFO,
                                                             	 buttons: Ext.MessageBox.OK
                                                             });
                                                             me.getStore().load();
                                                         }           
                                                         if(res.result=="-1")
                                                         {
                                                             win.Hide();
                                                             Ext.MessageBox.alert(
                                                             {
                                                             	 title:"Cannot create motion",
                                                             	 msg:"Still one pending Rule Motion exists in system, it must be resolved first before we create another.", 
                                                             	 //icon: Ext.Msg.ERROR,
                                                             	 buttons: Ext.MessageBox.OK
                                                             });
                                                         } 
                                                         if(res.result=="-2")
                                                         {
                                                             win.Hide();
                                                             Ext.MessageBox.alert(
                                                             {
                                                             	 title:"Cannot create motion",
                                                             	 msg:"Cannot require more votes than there are existing people with Signing Authority.", 
                                                             	// icon: Ext.Msg.ERROR,
                                                             	 buttons: Ext.MessageBox.OK
                                                             });
                                                         }
                                                     },
                                                     failure: function(form, action){App.error.xhr(action.response);}
                                                 }); 
                                             
                                             }   
                                         }                          
                                    }
                                ]
                            }                              
                            var form=financeForms.form_rule_motion(config.rule_type_store);
                            var final_form=new Ext.spectrumforms.mixer(_config,[form],['form']);
                            
                            var win_cnf=
                            {
                                    title       : '',
                                    final_form  : final_form
                            }
                            var win=new Ext.spectrumwindow.authority(win_cnf);
                            win.show();
                        }
                    }
            );   
            config.bottomLItems.push
            (                 
            );
                                                                   
            if(config.fields==null)     config.fields       = ['rule_id','rule_type_name','rule_type_code','rule_value','created_by','created_by_name','created_on_display','motion_status_id','motion_status_name'];
            if(config.sorters==null)    config.sorters      = null;
            if(config.pageSize==null)   config.pageSize     = 100;
            if(config.url==null)        config.url          = "/index.php/finance/json_get_rules/TOKEN:"+App.TOKEN;
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
        }                       
        
         
        ,afterRender: function() 
        {  
            var me=this;
            if(!this.override_edit)
            {   
                this.on("edit",function(e)
                {   
                }); 
                
            }
            if(!this.override_selectionchange)                              
            {   
                this.on("selectionchange",function(sm,records)
                {   
                });
            }
            if(!this.override_collapse)                              
            {   
                this.on("collapse",function(){});
            }
            if(!this.override_expand)
            {   
                this.on("expand",function(){});
            }                                 
            this.callParent(arguments);         
        }
});
