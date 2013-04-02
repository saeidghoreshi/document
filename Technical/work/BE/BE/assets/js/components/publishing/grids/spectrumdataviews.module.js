if(!App.dom.definedExt('Ext.spectrumdataviews.module')){
Ext.define('Ext.spectrumdataviews.module',    
{
    extend: 'Ext.spectrumdataviews', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    }           
 
    ,constructor : function(config) 
    {            
    	                                                      
        var me=this;
        if(config.fields==null)     config.fields       = ['id','module_id','w_m_id','module_name','isfree','w_p_id',
        											'w_p_name','w_p_alias','isactive','w_m_opt_count','module_icon'];
        if(config.url==null)        config.url          = "";
        if(config.width==null)      config.width        = '100%';
        if(config.height==null)     config.height       = 300;
        if(config.tpl==null)        config.tpl          = 
        Ext.create('Ext.XTemplate',                     
                '<tpl for=".">',
                    '<div class="phone  {[values.w_m_id == null ? "" : "star"]} ">',
                        (!Ext.isIE6? '<img width="74" height="74" src="{[values.module_icon]}" />' 
                        :'<div style="width:84px;height:84px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader'
                        		+'(src=\'{[values.module_icon]}\',sizingMethod=\'scale\')"></div>'),
                         
                         '<strong>{module_name}</strong>',
                    '</div>',
                '</tpl>'
          ); 
        
        //Fresh top/bottom components list
        if(config.topItems==null)     config.topItems=[];
        if(config.bottomLItems==null) config.bottomLItems=[];
        if(config.bottomRItems==null) config.bottomRItems=[];
        
        config.topItems.push
        (   
             "My Modules List"
        );
 
        config.bottomRItems.push
        (
             {
                id      : "spectrumdataviews_module_purchase_btn",
                xtype   : 'button',
                iconCls : 'cart_add',
                text    : '',
                tooltip : 'Purchase',
                disabled:true,
                handler: function()
                {  
                    Ext.MessageBox.show({title:"Status",msg:"Purchase not ready", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});                                                                                        
                }   
            }
            ,{
                id      : "spectrumdataviews_module_preview_btn",
                xtype   : 'button',
                iconCls : 'magnifier',
                text    : '',
                tooltip : 'Large Preview',
                disabled:true,
                handler: function()
                {  
                Ext.MessageBox.show({title:"Status",msg:"Preview not ready", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});                                                                                        
                }   
            }
            ,{
               id      : "spectrumdataviews_module_delete_btn",
               xtype   : 'button',
               iconCls : 'delete',
               text    : '',
               tooltip : 'Delete Selcted Modules',
               disabled:true,
               handler: function()
               {                                                  
                   if(config.dataview.getSelectionModel().getSelection().length==0)
                   {
                      // Ext.MessageBox.show({title:"Cannot Delete",msg:"Please select a module", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                       return;
                   }
                   var selectedModules=config.dataview.getSelectionModel().getSelection();
                   var selected_WMids='';
                   for (var i in selectedModules)       
                       if(selectedModules[i].data.w_m_id!=null)
                           selected_WMids+=selectedModules[i].data.w_m_id+',';
                   selected_WMids=selected_WMids.substring(0,selected_WMids.length-1);
                   if(selected_WMids=='')
                   {
                       Ext.MessageBox.show({title:"Cannot Delete",msg:"Please select assigned Module ", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                       return;
                   }
                   Ext.MessageBox.confirm('Delete Action', "Selected modules and related option settings would be removed .Are you sure ?", function(answer)
                   {     
                           if(answer=="yes")
                           {    
                               var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');    
                               Ext.Ajax.request(
                               {
                                   url     : 'index.php/websites/json_delete_selectedModules/TOKEN:'+App.TOKEN,
                                   params  : 
                                   {
                                        selected_WMids  :selected_WMids
                                   },
                                   success : function(o)
                                   {
                                       box.hide();
                                       var res=YAHOO.lang.JSON.parse(o.responseText);
                                       if(res.result=="1")
                                       {  
                                            var _location_id=Ext.getCmp('type_id'+config.owner.generator).getValue();                              
                                            me.dataview.getStore().load();
                                            config.owner.getStore().load({params:{location_id:_location_id}});  
                                       }
                                   },
                                   failure: function(o){alert(YAHOO.lang.dump(o.responseText));}
                               }); 
                           }
                   });
               }   
            }
            ,{
                id      : "spectrumdataviews_module_activate_btn",
                xtype   : 'button',
                iconCls : 'add',
                text    : '',
                tooltip : 'Activate',
                disabled:true,
                handler: function()
                {  
                    if(config.dataview.getSelectionModel().getSelection().length==0)
                    {
                        //Ext.MessageBox.show({title:"Cannot Activate",msg:"Please select a module", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        return;
                    }
                    var selectedModules=config.dataview.getSelectionModel().getSelection();
                    var selected_Mids='';
                    for (var i in selectedModules)
                    {                                    
                        if(selectedModules[i].data.w_m_id==null)
                            selected_Mids+=selectedModules[i].data.id+',';
                    }                              
                    if(selected_Mids=='')
                    {
                        Ext.MessageBox.show({title:"Cannot Activate",msg:"Please select Unassigned Module ", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        return;
                    }    
                    selected_Mids=selected_Mids.substring(0,selected_Mids.length-1);
                    var _location_id=Ext.getCmp('type_id'+config.owner.generator).getValue();   
                                               
                    //var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                    Ext.Ajax.request(
                    {
                        url     : 'index.php/websites/json_add_selectedModules/'+App.TOKEN,
                        params  : 
                        {
                            selected_Mids   :selected_Mids,
                            location_id     :_location_id
                        },
                        success : function(o)
                        {
                           // box.hide();
                            var res=YAHOO.lang.JSON.parse(o.responseText);
                            if(res.result=="1")
                            {   
                                me.dataview.getStore().load();
                                config.owner.getStore().load({params:{location_id:_location_id}}); 
                            }
                        },
                        failure: App.error.xhr
                    
                    }); 
                }   
            }
        );
       
        if(config.clickselectEvent==null)
        config.clickselectEvent=
        {                 
            selectionchange: function(dataview, selections)
            {                                
                if(selections.length==0)return;
                var record=selections[0].data;   
                
                Ext.getCmp("spectrumdataviews_module_purchase_btn").setDisabled(false);
                Ext.getCmp("spectrumdataviews_module_preview_btn").setDisabled(false);
                Ext.getCmp("spectrumdataviews_module_activate_btn").setDisabled(false);
                Ext.getCmp("spectrumdataviews_module_delete_btn").setDisabled(false);
                                                                                                              },
            itemclick: function(dataview, selections)
            {                                  
                if(selections.length==0)return;           
                var record=selections.data;      
                Ext.getCmp("spectrumdataviews_module_purchase_btn").setDisabled(false);
                Ext.getCmp("spectrumdataviews_module_preview_btn").setDisabled(false);
                Ext.getCmp("spectrumdataviews_module_activate_btn").setDisabled(false);
                Ext.getCmp("spectrumdataviews_module_delete_btn").setDisabled(false);
            }
        }
        this.config=config;
        this.callParent(arguments); 
    }
 
});}