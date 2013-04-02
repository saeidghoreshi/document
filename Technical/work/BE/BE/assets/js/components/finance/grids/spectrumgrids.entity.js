Ext.define('Ext.spectrumgrids.entity',    
{
        extend: 'Ext.spectrumgrids', 
        initComponent: function(config) 
        {       
            this.callParent(arguments);
        }
        
        ,override_edit          :null
        ,override_selectiochange:null
        ,override_itemdblclick  :null
        ,override_collapse      :null
        ,override_expand        :null,
        
        config                  :null,
        a_o_t_id                :null,
        a_o_id                  :null,
        a_o_name                :null,
        
        
        constructor : function(config) 
        {   
            var me=this;
            if(config.columns==null)
                config.columns  =
                [    
                    {
                        text        : "Name",
                        dataIndex   : 'entity_name',
                        flex        :1
                    },
                    {
                        text        : "Custom Invoice Number",
                        dataIndex   : "custom_empty",
                        flex        : 1,
                        editor      : 
                        {
                            xtype       :'textfield',
                            allowBlank  : true
                        }
                    }
                ];
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
            
            
            config.topItems.push
            (
                //Filter By levels [Org Type]
                {
                     id          : 'transaction_level_filter',
                     xtype       : 'combo',
                     name        : 'transaction_level_filter',
                     fieldLabel  : '',
                     labelWidth  : 50,
                     width       : 150,
                     allowBlank  : true,
                     mode        : 'local',
                     forceSelection: true,
                     editable    : false,
                     displayField: 'org_type_name',
                     valueField  : 'org_type_id',
                     queryMode   : 'local',
                     labelStyle  : 'font-weight:bold',
                     store       : config.orgLevelStore,
                     listeners   :
                     {
                        buffer:100,
                        change:function(obj,selected_id)
                        {
                            me.getStore().proxy.extraParams.target_org_type_id=selected_id;
                            me.getStore().load();
                        }
                     }                                          
                }
            );
            config.bottomLItems.push
            (

            );
            config.bottomRItems.push
            (

            );
            
            if(config.fields==null)     config.fields       = ['entity_id','entity_name','custom_empty','parentorg'];
            if(config.sorters==null)    config.sorters      = config.fields;
            if(config.pageSize==null)   config.pageSize     = 1000;
            if(config.url==null)        config.url          = "VOID";
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
            if(config.height==null)     config.height       = 100;
            if(config.groupField==null) config.groupField   = "parentorg";
            
            
            this.override_edit          =config.override_edit;
            this.override_selectiochange=config.override_selectionchange;
            this.override_itemdblclick  =config.override_itemdblclick;
            this.override_collapse      =config.override_collapse;
            this.override_expand        =config.override_expand;
            
            
            //Get Org and Org Type
            var box = Ext.MessageBox.wait('Please wait while processing ...', 'Loading'); 
            Ext.Ajax.request(
            {
                url     : 'index.php/permissions/json_get_active_org_and_type/TOKEN:'+App.TOKEN,
                params  : {test:'test'},
                success : function(response)
                {
                    box.hide();
                    var res=YAHOO.lang.JSON.parse(response.responseText);
                    me.a_o_t_id    =res.result.org_type_id;
                    me.a_o_id      =res.result.org_id;
                    me.a_o_name    =res.result.org_name;
                }
            }); 
            //Get Org and Org Type END 
            
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
                    me.getSelectionModel().getSelection()[0].commit(); 
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
