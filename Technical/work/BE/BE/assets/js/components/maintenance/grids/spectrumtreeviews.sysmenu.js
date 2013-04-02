//if(App.dom.definedExt('Ext.spectrumtreeviews.sysmenu'))return;
Ext.define('Ext.spectrumtreeviews.sysmenu',    
{
    extend: 'Ext.spectrumtreeviews', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    }
    
    ,config                 :null
    ,menuItemType           :null
    
    ,constructor : function(config) 
    {                 
        var me=this;
        me.menuItemType    =new simpleStoreClass().make(['type_id','type_name'],"index.php/endeavor/json_getMenuTypeItem/TOKEN:"+App.TOKEN+'/',{test:true});   
        
        config.columns  =
        [
            {
                xtype       : 'treecolumn',
                text        : 'Menu Items Hirarchy',
                flex        : 1,
                dataIndex   : 'label'
            }
        ];
        
        if(config.topItems==null)     config.topItems=[];
        if(config.bottomLItems==null) config.bottomLItems=[];
        if(config.bottomRItems==null) config.bottomRItems=[];
        
        config.topItems.push
        (   
            //Filter
            {
                     xtype       : 'combo',
                     name        : 'filter_active',
                     fieldLabel  : '',
                     labelWidth  : 70,
                     width       : 100,
                     allowBlank  : true,
                     mode        : 'local',
                     forceSelection: true,
                     editable    : false,
                     displayField: 'type_name',
                     valueField  : 'type_id',
                     queryMode   : 'local',
                     labelStyle  : 'font-weight:bold',
                     store       : Ext.create('Ext.data.Store', 
                                    {
                                        fields  : ["type_id","type_name"],
                                        data    : 
                                        [
                                            {type_id: '0',    type_name: 'All'},
                                            {type_id: '1',    type_name: 'Active'}
                                        ]
                                    }),
                     listeners  :
                     {
                             buffer:200,
                             change:function(obj,selectedId)
                             {
                                 me.getStore().load(
                                {
                                    params:
                                    {
                                        filter_id:selectedId
                                    }
                                });    
                             }
                     }                                          
                }
        );
        
        config.bottomLItems.push
        (
             //Create New Link
             {
                 xtype      : 'button',
                 iconCls    : 'add',
                 text       : '',
                 tooltip    : 'Add Menu Item',
                 handler    : function()
                 {
                     
                     var formNewMenuItemConfig     =
                     {
                         width      :400,
                         height     :80,
                         bottomItems:   
                         [
                             "->"
                             ,{
                                 xtype    :"button",
                                 iconCls  :"table_save",
                                 text     :"Save",
                                 tooltip  :"Save Menu Item",
                                 pressed  :true,
                                 handler:function()
                                 {
                                    if(!formNewMenuItem.getForm().isValid())
                                    {
                                        Ext.MessageBox.alert({title:"Error",msg:"Please Fill in Menu Item Field", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                        return;
                                    }
                                    var values          = formNewMenuItem.getForm().getValues();
                                    var post            = {} 
                                    post["menu_label"]  = values["menu_label"];
                                    
                                    var box = Ext.MessageBox.wait('Processing ...', 'Performing Actions');
                                    Ext.Ajax.request(
                                    {
                                        url     : "/index.php/endeavor/json_saveNewMenuItem/TOKEN:"+App.TOKEN ,
                                        params  : post,
                                        success : function(o)
                                        {
                                            var res         =YAHOO.lang.JSON.parse(o.responseText);
                                            var newMenuId   =res.result;
                                            box.hide();
                                                 
                                            formNewMenuItemWin.Hide();
                                            
                                            me.getStore().load();
                                            var onLoadHandler=function()
                                            {       
                                                var node = me.getRootNode().findChild('id',newMenuId/*newly Created MenuId*/,true);
                                                me.getSelectionModel().select(node); 
                                                me.editMenuItem();                                                            
                                                me.getStore().removeListener('load', onLoadHandler);
                                                App.initMenu();
                                            }
                                            me.getStore().on("load",onLoadHandler);
                                    },
                                        failure: function(o){alert(YAHOO.lang.dump(o.responseText));}
                                    });         
                                 }                          
                             }
                         ]
                     }                              
                     var formNewMenuItem        =   maintenanceForms.formNewMenuItem();
                     var formNewMenuItemFinal   =   new Ext.spectrumforms.mixer(formNewMenuItemConfig,[formNewMenuItem],['form']);
                     var formNewMenuItemWinConfig  =
                     {
                        title       : 'Create New Menu Item',
                        final_form  : formNewMenuItemFinal
                     }
                     var formNewMenuItemWin     =   new Ext.spectrumwindow.maintenance(formNewMenuItemWinConfig);
                     formNewMenuItemWin.show();
                 }   
             }
        );
        
        config.bottomRItems.push
        (
             //Edit
             {
                 xtype      : 'button',
                 iconCls    : 'pencil',
                 text       : '',
                 tooltip    : 'Edit Menu Item',
                 handler    : function()
                 {
                    me.editMenuItem();    
                 }
             },
             //Save Ordering
             {
                 xtype      : 'button',
                 iconCls    : 'table_save',
                 text       : '',
                 tooltip    : 'Save Ordering',
                 handler    : function()
                 { 
                     Ext.MessageBox.confirm('Save New Menu Items Ordering', "<b>New Ordering will be effective</b> Are You Sure ?", function(answer)
                     {     
                        if(answer=="yes")
                        {
                            var post={};
                            post["complex_str"] = '';
                                         
                            for(var i=0;i<config.Plot.length;i++)
                                post["complex_str"] +=config.Plot[i].join(',')+'-';
                            post["complex_str"]     =post["complex_str"].substring(0,post["complex_str"].length-1);
                            
                            var box = Ext.MessageBox.wait('Processing ...', 'Performing Actions');
                            Ext.Ajax.request(
                            {
                                url     : "/index.php/endeavor/json_updateSysMenuOrdering/TOKEN:"+App.TOKEN ,
                                params  : post,
                                success : function(o)
                                {
                                    var res    =YAHOO.lang.JSON.parse(o.responseText);
                                    if(res.result=="1")
                                    {
                                         box.hide();
                                         maintenanceManage.getSysMenuList();
                                         App.initMenu();
                                    }
                                },
                                failure: function(o){alert(YAHOO.lang.dump(o.responseText));}
                            });         
                        }
                     });
                 }   
             },
             //Delete Link
             {
                 xtype      : 'button',
                 iconCls    : 'delete',
                 text       : '',
                 tooltip    : 'Delete Menu Item',
                 handler    : function()
                 {
                     if(me.getSelectionModel().getSelection().length==0)
                     {
                         Ext.MessageBox.alert({title:"Error",msg:"Please Select a Record to Delete", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                         return;
                     }
                     var record=me.getSelectionModel().getSelection()[0].data;
                     
                     Ext.MessageBox.confirm('Remove Action', "Remove  Munu Item Named <b>"+record.label+"</b> Are You Sure ?", function(answer)
                     {   
                        if(answer=="yes")
                        {
                            var post            = {} 
                            post["menu_id"]     = record.id;
                            
                            var box = Ext.MessageBox.wait('Processing ...', 'Performing Actions');
                            Ext.Ajax.request(
                            {
                                url     : "/index.php/endeavor/json_deleteMenuItem/TOKEN:"+App.TOKEN ,
                                params  : post,
                                success : function(o)
                                {
                                    box.hide();                            
                                    maintenanceManage.getSysMenuList();
                                    App.initMenu();
                                }                            
                            });
                        }
                     });
                 }
             }
        );                                   
        
        if(config.fields    ==null)     config.fields       = ['id','parent','label','menu_order','menu_type','menu_active','view_auth_id','update_auth_id' ,'menu_label','menu_group','menu_default','menu_rowspan','menu_colspan','image','window_id','link_href'];
        if(config.url       ==null)     config.url          = "/index.php/endeavor/json_get_sysmenus/TOKEN:"+App.TOKEN ;
        if(config.width     ==null)     config.width        = 100;
        if(config.height    ==null)     config.height       = 400;
        
        config.viewConfig   = 
        {
            plugins: 
            {
                ptype: 'treeviewdragdrop'
            },
            listeners: 
            {
                beforedrop  : function(node, data, dropRec, dropPosition) 
                {   
                    /*
                    *  config.Plot is a 2D array col(0) = parent & col(1) = id & col(2)= active
                    */
                    var dragData=me.getSelectionModel().getSelection()[0].data;
                    
                    if(dragData.label=='')     return false;       //Not able to drag fake record
                    if(dropRec.data.label=='') return false;       //Not able to drop (under-after-before) fake record
                    
                             
                    //Append To Parent (Not Ordered)
                    if(dropPosition=='append')
                    {   
                        var dragIndex               =me.indexOf_2D(config.Plot,1,dragData.id);
                        config.Plot[dragIndex][0]   =dropRec.data.id;
                    }
                    //Append To Sibling
                    else
                    {
                        //Get Target Parent_id
                        var dropIndex               =me.indexOf_2D(config.Plot,1,dropRec.data.id);
                        var targetParent_id         =config.Plot[dropIndex][0];
                        //replace drag node parent_id
                        var dragIndex               =me.indexOf_2D(config.Plot,1,dragData.id);
                        config.Plot[dragIndex][0]   =targetParent_id;
                                                                       
                        //Move(cut-paste) In the Right Place
                        if(dropPosition=='before')
                            config.Plot             =me.cutNpaste_in_array(config.Plot,dragIndex,dropIndex-1);
                        if(dropPosition=='after')
                            config.Plot             =me.cutNpaste_in_array(config.Plot,dragIndex,dropIndex);
                    }   
                    return true;
                },
                drop        : function(node, data, dropRec, dropPosition) 
                {  
                    //alert(YAHOO.lang.dump(config.Plot));
                }
            }
        };
        
        Ext.Ajax.request
        ({
            url     : "/index.php/endeavor/json_get_sysmenus/TOKEN:"+App.TOKEN ,
            params  : {test:'test'}, 
            success : function(response)
            {
                var result  =YAHOO.lang.JSON.parse(response.responseText);
                config.Plot =result.Plot;
            }
        });    
        
        this.config=config;
        this.callParent(arguments); 
     },
     afterRender: function() 
     {  
                var me=this;
                if(!this.override_edit)
                {   
                    this.on("itemclick",function(e)
                    {
                        /*var node = me.getSelectionModel().getSelection()[0].data;   
                        Ext.getCmp("campaign_list_pause_button").show();
                        
                        if(node.paused=='t')Ext.getCmp("campaign_list_pause_button").setIconCls("control_play_blue");
                        else Ext.getCmp("campaign_list_pause_button").setIconCls("control_pause_blue");                    
                        */
                    }); 
                }       
                if(!this.override_selectionchange)                              
                {   
                    this.on("itemclick",function(sm,records)
                    {   
                        var record                  =me.getSelectionModel().getSelection()[0].data;
                        var index                   =me.indexOf_2D(me.config.Plot,1,record.id);
                        me.config.Plot[index][2]    =record.checked;
                    }); 
                }
                this.callParent(arguments);         
     },
     indexOf_2D:function(a,searchIndex,value)
     {                 
        for(var i=0;i<a.length;i++)
            if(a[i][searchIndex]==value)
                return i;
     },
     cutNpaste_in_array:function(a,dragIndex,dropIndex)  //   [][][][]  [][]
     {
        var me          =this;
        var specificRow =a.splice(dragIndex,1);
        var left_side   =a.splice(0,dropIndex);
        var right_side  =a;
                    
        var result      =[];
        return result.concat(left_side,specificRow,right_side);                                                                  
     },
     editMenuItem:function()
     {
         var me=this;
         if(me.getSelectionModel().getSelection().length==0)
         {
             Ext.MessageBox.alert({title:"Error",msg:"Please Select Record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
             return;
         }
         var record                 =  me.getSelectionModel().getSelection()[0].data;
         
         //Build Roles Grid
         var rolesGridConfig=
         {  
                generator       : Math.random(),
                owner           : me,
                extraParamsMore : {menu_id:record.id},
                title           : 'System Roles',
                url             : "/index.php/endeavor/json_getRoleMenu/TOKEN:"+App.TOKEN,
                //customized Components
                rowEditable     :true,
                groupable       :false,
                bottomPaginator :false,
                searchBar       :true,            
                
                selModel        : Ext.create('Ext.selection.CheckboxModel', 
                {
                                mode        :'MULTI',
                                listeners   : {selectionchange: function(sm, selections) {}}
                })
         }
         var rolesGrid  = Ext.create('Ext.spectrumgrids.roles',rolesGridConfig);
         //Build Roles Grid END
         
         var formMenuItemConfig     =
         {
             width      :800,
             height     :500,
             collapsible:false,
             bottomItems:   
             [
                 "->"
                 ,{
                     xtype    :"button",
                     iconCls  :"table_save",
                     text     :"Update",
                     tooltip  :"Update Menu Item",
                     pressed  :true,
                     handler:function()
                     {
                         var record=me.getSelectionModel().getSelection()[0].data;
                         Ext.MessageBox.confirm('Save Information Action', "Are You Sure ?", function(answer)
                         {     
                                if(answer=="yes")
                                {
                                    var values  = formMenuItem.getForm().getValues();
                                    var post    = {};
                                    post["menu_id"]         = record.id;
                                    post["menuItemLabel"]   = values["menu_label"];
                                    post["menuItemType"]    = values["menu_type_id"];
                                    post["menu_group"]      = values["menu_group"];
                                    post["menu_default"]    = values["menu_default"];
                                    post["menu_rowspan"]    = values["menu_rowspan"];
                                    post["menu_colspan"]    = values["menu_colspan"];
                                    post["menu_image"]      = values["menu_image"];
                                    
                                    var box = Ext.MessageBox.wait('Processing ...', 'Performing Actions');
                                    Ext.Ajax.request(
                                    {
                                        url     : "/index.php/endeavor/json_updateMenuItem/TOKEN:"+App.TOKEN ,
                                        params  : post,
                                        success : function(o)
                                        {
                                            var res    =YAHOO.lang.JSON.parse(o.responseText);
                                            if(res.result=="1")
                                            {
                                                 box.hide();
                                                 me.getStore().load();   
                                                 formMenuItemWin.Hide();
                                                 App.initMenu();
                                            }
                                        },
                                        failure: function(o){alert(YAHOO.lang.dump(o.responseText));}
                                    });         
                                }
                             });        
                     }                          
                 }
             ]
         }
         var menuWindowType         =   new simpleStoreClass().make(['type_id','type_name'],"index.php/endeavor/json_getMenuWindowType/TOKEN:"+App.TOKEN+'/',{menu_id:record.id});
         
         var saveNewWCHandler         = function()
                                        {
                                                var record  =me.getSelectionModel().getSelection()[0].data;
                                                var values  = formMenuItem.getForm().getValues();
                                                var post    = {};
                                                post["menu_id"]             = record.id;
                                                post["window_controller"]   = values["window_controller"];
                                                post["window_method"]       = values["window_method"];
                                                post["window_id"]           = ((Ext.getCmp("new_old_cb").checked==true)?-1:Ext.getCmp("window_type_id").getValue());
                                                post["new_old_cb"]          = Ext.getCmp("new_old_cb").checked;
                                                                                                        
                                                var box = Ext.MessageBox.wait('Processing ...', 'Performing Actions');
                                                Ext.Ajax.request(
                                                {
                                                    url     : "/index.php/endeavor/json_updateControllerWindow/TOKEN:"+App.TOKEN ,
                                                    params  : post,
                                                    success : function(o)
                                                    {
                                                        var res    =YAHOO.lang.JSON.parse(o.responseText);
                                                        if(res.result=="1")
                                                        {
                                                             box.hide();
                                                             //menuWindowType.load();
                                                             Ext.MessageBox.alert({title:"Status",msg:"Window::Controller Updated Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                        }
                                                    },
                                                    failure: function(o){alert(YAHOO.lang.dump(o.responseText));}
                                                });                                                                       
                                         };
         var saveNewELHandler         = function()
                                        {
                                                var record  =me.getSelectionModel().getSelection()[0].data;
                                                var values  = formMenuItem.getForm().getValues();
                                                var post    = {};
                                                post["menu_id"]         =record.id;
                                                post["link_href"]       =values["link_href"];
                                                                                                        
                                                var box = Ext.MessageBox.wait('Processing ...', 'Performing Actions');
                                                Ext.Ajax.request(
                                                {
                                                    url     : "/index.php/endeavor/json_updateExternalLink/TOKEN:"+App.TOKEN ,
                                                    params  : post,
                                                    success : function(o)
                                                    {
                                                        var res    =YAHOO.lang.JSON.parse(o.responseText);
                                                        if(res.result=="1")
                                                        {
                                                             box.hide();
                                                             Ext.MessageBox.alert({title:"Status",msg:"External link Updated Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                        }
                                                    },
                                                    failure: function(o){alert(YAHOO.lang.dump(o.responseText));}
                                                });                                                                       
                                         };
         var formMenuItem           =   maintenanceForms.formMenuItem(me.menuItemType,menuWindowType,saveNewWCHandler,saveNewELHandler);
         var formMenuItemFinal      =   new Ext.spectrumforms.mixer(formMenuItemConfig,[formMenuItem,rolesGrid],['form','grid']);
         var formMenuItemWinConfig  =
         {
            title       : 'Edit Menu Item ('+record.label+')',
            final_form  : formMenuItemFinal
         }
         var formMenuItemWin        =   new Ext.spectrumwindow.maintenance(formMenuItemWinConfig);
         formMenuItemWin.show();
         formMenuItemWin.on("close",function()
         {
            maintenanceManage.getSysMenuList();
            App.initMenu();
         });
         
         //load Records           
         
         menuWindowType.on("load",function()
         {
           var Gen=Math.random();
           Ext.define('model_'+Gen,{extend: 'Ext.data.Model'});
           formMenuItem.loadRecord(Ext.ModelManager.create(
           {
               
                 'menu_label'     : record.label,
                 'menu_type_id'   : record.menu_type,
                 'menu_group'     : record.menu_group,
                 'menu_default'   : ((record.menu_default=='f')?true:false),
                 'menu_rowspan'   : record.menu_rowspan,
                 'menu_colspan'   : record.menu_colspan,
                 'menu_image'     : record.image,
                 'window_type_id' : record.window_id,
                 'link_href'      : record.link_href
                 
           },'model_'+Gen));
       });
     }   
     
});

