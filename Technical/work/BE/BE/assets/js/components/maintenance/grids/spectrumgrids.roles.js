Ext.define('Ext.spectrumgrids.roles',    
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
            var authStore       =new simpleStoreClass().make(['type_id','type_name'],"index.php/endeavor/json_getMenuAuthStore/TOKEN:"+App.TOKEN+'/',{test:true});                               
            config.columns  =
            [    
                {
                    text        : "Role Name",
                    dataIndex   : "role_name"
                },
                {
                    text        :"View Auth",
                    dataIndex   :"view_auth_name",
                    editor      : 
                    { 
                        xtype         : 'combo',
                        allowBlank    : false,
                        editable      : false,
                        valueField    : 'type_name',
                        displayField  : 'type_name',  
                        queryMode     : 'local',
                        store         : authStore
                    }                                                                           
                },
                {
                    text        :"Update Auth",
                    dataIndex   :"update_auth_name",
                    editor      : 
                    { 
                        xtype         : 'combo',
                        allowBlank    : false,
                        editable      : false,
                        valueField    : 'type_name',
                        displayField  : 'type_name',  
                        queryMode     : 'local',
                        store         : authStore
                    }
                },
                {
                    text        : "Status",
                    xtype       : 'templatecolumn',
                    tpl         : '{[values.alreadyassigned == "1" ? "<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/tick.png />" : ""]}',
                    flex        : 1
                }
            ];
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
            
            config.topItems.push
            (
                
            ); 
            config.bottomLItems.push
            (
                 //Delete Selecteds
                 {
                     xtype      : 'button',
                     iconCls    : 'delete',
                     text       : 'Delete Selected',
                     tooltip    : 'Delete Selected Role Assignments',
                     handler    : function()
                     { 
                         if(me.getSelectionModel().getSelection().length==0)
                         {
                             Ext.MessageBox.alert({title:"Error",msg:"Please Select at Least one Assigned Assignment", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                             return;
                         }
                         Ext.MessageBox.confirm('Delete Selected', "Are You Sure ?", function(answer)
                         {     
                            var records =me.getSelectionModel().getSelection();
                            if(answer=="yes")
                            {
                                var post={};
                                post["menurole_ids"] = '';
                                
                                for(var i in records)
                                    if(records[i].data.alreadyassigned==1)
                                        post["menurole_ids"]+=records[i].data.menu_id+','+records[i].data.role_id+'-';
                                                                 
                                if(post["menurole_ids"] == '')
                                {
                                    Ext.MessageBox.alert({title:"Error",msg:"None Of Selected Records had been Already Assignd", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                    me.getStore().load();                                           
                                    return;
                                } 
                                post["menurole_ids"] =  post["menurole_ids"].substring(0,post["menurole_ids"].length-1);
                                
                                Ext.Ajax.request(
                                {
                                    url     : "/index.php/endeavor/json_deleteSelectedMenuItemsRoles/TOKEN:"+App.TOKEN ,
                                    params  : post,
                                    success : function(o)
                                    {
                                        var res    =YAHOO.lang.JSON.parse(o.responseText);
                                        if(res.result=="1")
                                        {
                                            me.getStore().load();   
                                            Ext.MessageBox.alert({title:"Status",msg:"Selected Assignments Added Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                        }
                                    },
                                    failure: function(o){alert(YAHOO.lang.dump(o.responseText));}
                                });         
                            }
                         });
                     }   
                 }  
            );       
            config.bottomRItems.push
            (
                //Add Selecteds
                 {
                     xtype      : 'button',
                     iconCls    : 'add',
                     text       : 'Add Selected',
                     tooltip    : 'Add Selected Role Assignments',
                     handler    : function()
                     { 
                         if(me.getSelectionModel().getSelection().length==0)
                         {
                             Ext.MessageBox.alert({title:"Error",msg:"Please Select at Least one UnAssigned Assignment", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                             return;
                         }
                         Ext.MessageBox.confirm('Add Selected', "Are You Sure ?", function(answer)
                         {   
                            var mainRec =   me.owner.getSelectionModel().getSelection()[0].data;
                            var records =   me.getSelectionModel().getSelection();
                            
                            if(answer=="yes")
                            {
                                var post                = {};
                                post["menuroleauth_ids"]= '';
                                
                                for(var i in records)     
                                    if(records[i].data.alreadyassigned==0)
                                    {      
                                        if(records[i].data.view_auth_name==null || records[i].data.update_auth_name==null)
                                        {
                                            Ext.MessageBox.alert({title:"Error",msg:"Please Authentication Correct Assignment", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                            return;
                                        }
                                        post["menuroleauth_ids"]+=mainRec.id+','+records[i].data.role_id+','+records[i].data.view_auth_name+','+records[i].data.update_auth_name+'-';
                                    }
                                        
                                                                 
                                if(post["menuroleauth_ids"] == '')
                                {
                                    Ext.MessageBox.alert({title:"Error",msg:"Please Select and Fill in Unselected Records had been Already Assignd", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                    me.getStore().load();
                                    return;
                                } 
                                post["menuroleauth_ids"] =  post["menuroleauth_ids"].substring(0,post["menuroleauth_ids"].length-1);
                                
                                Ext.Ajax.request(
                                {
                                    url     : "/index.php/endeavor/json_addSelectedMenuItemsRoles/TOKEN:"+App.TOKEN ,
                                    params  : post,
                                    success : function(o)
                                    {
                                        var res    =YAHOO.lang.JSON.parse(o.responseText);
                                        if(res.result=="1")
                                        {
                                            me.getStore().load();   
                                            Ext.MessageBox.alert({title:"Status",msg:"Selected Assignments Added Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                        }
                                    },
                                    failure: function(o){alert(YAHOO.lang.dump(o.responseText));}
                                });         
                            }
                         });   
                     }   
                 }  
            ); 
            
            if(config.fields==null)     config.fields       = ['role_id','role_name','menu_id','view_auth_id','update_auth_id','view_auth_name','update_auth_name','status','alreadyassigned'];
            if(config.sorters==null)    config.sorters      = null;
            if(config.pageSize==null)   config.pageSize     = 1000;
            if(config.url==null)        config.url          = "void";
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
            if(config.height==null)     config.height       = '100%';
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
                    e.record.commit();
                }); 
            }
            if(!this.override_selectionchange)                              
            {   
                this.on("selectionchange",function(sm,records)
                {   
                    if(records ==undefined || records =='undefined' || records.length==0)return false;
                    var rec=records[0].raw;                               
                });                                                       
                this.on("itemclick",function(sm,records)
                {   
                    //var rec=me.getSelectionModel().getSelection()[0].data;
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
