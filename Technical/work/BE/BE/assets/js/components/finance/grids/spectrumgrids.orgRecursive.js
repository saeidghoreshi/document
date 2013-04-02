Ext.define('Ext.spectrumgrids.orgRecursive',    
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
        ,Tracker                :[]
        ,constructor : function(config) 
        {   
            var me=this;
            if(config.columns==null)
                config.columns  =
                [    
                    {
                        text        : "Name",
                        dataIndex   : 'org_name',
                        flex        : 1
                    }
                    ,{
                        text        : "Amount",
                        dataIndex   : 'amount',
                        width       : 150,
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
                {
                        id      : 'back_id',
                        iconCls : '',
                        xtype   : 'button',
                        text    : 'Back',
                        pressed : true,
                        hidden  : true,
                        tooltip : 'Back',
                        handler : function()
                        {                                              
                            var lastParentEntityId=me.Tracker[me.Tracker.length-2];                    
                            
                            me.getStore().getProxy().extraParams["parentEntityId"] = lastParentEntityId;
                            me.getStore().load({params:{parentEntityId:lastParentEntityId}});
                            me.Tracker.splice(me.Tracker.length-1);
                            
                            if(me.Tracker.length==0)
                                Ext.getCmp("back_id").hide();  
                            Ext.getCmp("forward_id").show();
                        }
                },            
                {
                        id      : 'forward_id',
                        iconCls : '',
                        xtype   : 'button',
                        text    : 'Forward',
                        pressed : true,
                        tooltip : 'Forward',
                        handler : function()
                        {
                            if(me.getSelectionModel().getSelection().length==0 || me.getSelectionModel().getSelection().length>1)
                            {
                                Ext.MessageBox.alert({title:"Error",msg:"Please Select one Organization to Proceed", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                            }
                            var record=me.getSelectionModel().getSelection()[0].data;
                            if(parseInt(record.org_type_id)==3)
                                Ext.getCmp("forward_id").hide();
                            Ext.getCmp("back_id").show();
                            
                            me.getStore().load({params:{parentEntityId:record.entity_id}});
                            me.Tracker.push(record.entity_id);
                        }
                },
                {
                     xtype       : 'combo',
                     name        : 'country_abbr',
                     id          : 'country_abbr',
                     labelStyle  : 'font-weight:bold',   
                     allowBlank  : true,
                     mode        : 'local',
                     forceSelection: false,
                     editable    : false,
                     displayField: 'country_name',
                     valueField  : 'country_id',
                     queryMode   : 'local',
                     store       : config.countryStore,
                     flex        : 1,
                     emptyText   : 'Country',
                     listeners   :
                     {
                         change:function( _this, newValue, oldValue)
                         {   
                             config.regionStore.load({params:{country_id:newValue}}) ;
                               
                             var curParentEntityId=me.Tracker[me.Tracker.length-1];                    
                             me.getStore().load({params:{country_id:newValue,parentEntityId:curParentEntityId}});
                         }
                     }
                },
                {
                     xtype       : 'combo',
                     name        : 'region_abbr',
                     id          : 'region_abbr',
                     labelStyle  : 'font-weight:bold',   
                     width       : 150,
                     allowBlank  : true,
                     mode        : 'local',
                     forceSelection: false,
                     editable    : false,
                     emptyText   : 'Region',
                     displayField: 'region_name',
                     valueField  : 'region_id',
                     queryMode   : 'local',
                     store       : config.regionStore,
                     margins     :'0 0 0 5',
                     listeners   :
                     {
                         change:function( _this, newValue, oldValue)
                         {   
                             var curParentEntityId=me.Tracker[me.Tracker.length-1];                    
                             me.getStore().load({params:{region_id:newValue,parentEntityId:curParentEntityId}});
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
            
            if(config.fields==null)     config.fields       = ['entity_id','org_type_id','org_name','amount'];
            if(config.sorters==null)    config.sorters      = config.fields;
            if(config.pageSize==null)   config.pageSize     = 100;
            if(config.url==null)        config.url          = "VOID";
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
            if(config.height==null)     config.height       = 100;
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
