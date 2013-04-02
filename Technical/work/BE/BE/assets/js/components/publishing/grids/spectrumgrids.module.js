if(!App.dom.definedExt('Ext.spectrumgrids.module')){
Ext.define('Ext.spectrumgrids.module',    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    },

    
    ordering_handler:function()
    {                
        var store=this.getStore();
        
        //Order Array
        var order_array=new Array();
        //wmid array : these are the primary keys
        var w_m_id_array=new Array();
 
        for(var i =0;i<store.data.items.length;i++)
        {
            order_array.push(i+1);
            
			w_m_id_array.push(store.data.items[i].data.w_m_id);
			 
        }
        
  
        Ext.Ajax.request(
        {
            url     : "/index.php/websites/json_update_websiteModules_ordering/TOKEN:"+App.TOKEN
            ,params  : 
            {
                w_m_id_array       :YAHOO.lang.JSON.stringify(w_m_id_array), 
                order_array    :YAHOO.lang.JSON.stringify(order_array)
            }
            ,scope:this
            ,success : function(response)
            { 
            	var _location_id=Ext.getCmp('type_id'+this.config.generator).getValue();  
                this.getStore().load({params:{location_id:_location_id}});
            }
            ,failure:App.error.xhr
        });        
    },


    constructor : function(config) 
    {                        

        var me=this;
        config.title='Website Add-Ons';
        config.columns  =
        [    
             {
                text        : "Module",
                dataIndex   : 'module_name',
                flex        :1
            }
        ];
        //Fresh top/bottom components list
        if(config.topItems==null)     config.topItems=[];
        if(config.bottomLItems==null) config.bottomLItems=[];
        if(config.bottomRItems==null) config.bottomRItems=[];
        
        config.topItems.push
        (
            {  
                    id          : 'type_id'+config.generator,
                    xtype       : 'combo',
                    labelStyle  : 'font-weight:bold;padding:0',
                    name        : 'type_id',
                    emptyText   : '',
                    fieldLabel  : '',
                    labelWidth  : 100,    
                    forceSelection: false,
                    editable    : false,
                    displayField: 'type_name',
                    valueField  : 'type_id',
                    queryMode   : 'local',     
                    allowBlank  : false,
                    store       : config.locationStore,
                    width       : 120,
                    listeners   : 
                    {
                        change: 
                        {
                            fn: function(conf,selected_id)
                            {
                                me.getStore().load({params:{location_id:selected_id}});
                            },
                            scope: this, 
                            buffer:500                  
                        }       
                    }
            }
        );
        config.bottomLItems.push                                                                      
        (
            //manage module  //Add remove
            {
                id      : 'spectrumgrids_module_add_btn'+config.generator,
                xtype   : 'button',
                iconCls : 'fugue_puzzle--pencil',
                text    : '',
                tooltip : 'Manage Location Add-Ons',
                handler : config.addremove_handler
            }
            ,//Save Ordering
            {
                id      : 'spectrumgrids_module_orders_btn'+config.generator,
                xtype   : 'button',
                iconCls : 'table_link',
                text    : '',
                tooltip : 'Save Add-On Order',
                scope:this,
                handler : this.ordering_handler 
            }
           // ,'-'
            //,'Drag and drop rows to change module order'
        );
        
        config.bottomRItems.push
        (
            //play/pause
            {
                id      : 'spectrumgrids_module_enable_btn'+config.generator,
                xtype   : 'button',
                iconCls : 'control_play_blue',
                text    : '',
                tooltip : '',
                hidden  : true,
                handler: function()
                {   
                     var _location_id=Ext.getCmp('type_id'+config.generator).getValue();
                     var _w_m_id=me.getSelectionModel().getSelection()[0].data.w_m_id;
                      
                     Ext.Ajax.request(
                     {
	                        url     : 'index.php/websites/json_play_pause_selectedWebsiteModule/'+App.TOKEN,
	                        params  : 
	                        {
	                            w_m_id      :_w_m_id
	                        },
	                        success : function(o)
	                        { 
	                             var res=YAHOO.lang.JSON.parse(o.responseText);
	                             if(res.result=="1")
	                             {
	                                 me.getStore().load({params:{location_id :_location_id}});
	                                 Ext.getCmp("spectrumgrids_module_enable_btn"+config.generator).hide();
	                             }
	                        },
	                        failure: App.error.xhr
                    });
                }
            }
            ,//Options
            {
                id      : 'spectrumgrids_module_options_btn'+config.generator,
                xtype   : 'button',
                iconCls : 'cog',
                text    : '',
                tooltip : 'Options',
                hidden: true,//removed by request
                handler:  function()
                {
                        var _module_id=me.getSelectionModel().getSelection()[0].data.module_id;
                        var opts_grid=me.load_opts_grid(_module_id);
                        var config=
                        {
                            width   :400,
                            height  :275,
                            bottomItems:   
                            [
                            '->'
                            ,{   
                                 xtype   :"button",
                                 text    :"Save",
                                 iconCls :'disk',
                                 pressed :true,
                                 width   :70,
                                 tooltip :'Save selected Options',
                                 handler :function()
                                 {  
                                     var _w_m_id=me.getSelectionModel().getSelection()[0].data.w_m_id;
                                     var selecteds=opts_grid.getSelectionModel().getSelection();
                                     var selected_ids='';
                                     for (var i=0;i<selecteds.length;i++)
                                            selected_ids+=selecteds[i].data.m_opt_id+',';
                                     selected_ids=selected_ids.substring(0,selected_ids.length-1);
                                      
                                     Ext.Ajax.request(
                                     {
                                            url     : 'index.php/websites/json_update_selectedOpts/'+App.TOKEN,
                                            params  : {selected_ids:selected_ids,w_m_id:_w_m_id},
                                            success : function(o)
                                            { 
                                                 var res=YAHOO.lang.JSON.parse(o.responseText);
                                                 if(res.result=="1")
                                                 {
                                                     Ext.MessageBox.alert({title:"Status",msg:"Options Updated successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                     win.Hide();
                                                 }
                                            },
                                            failure: App.error.xhr
                                    });
                                 }                                                                
                            }
                            ]
                        }
                        var final_form=new Ext.spectrumforms.mixer(config,[opts_grid],['grid']);
                        var win_cnf=
                        {
                            title       : 'Managers List',
                            final_form  : final_form
                        }
                        var win=new Ext.spectrumwindow.publishing(win_cnf);
                        win.show();
                }
            }
            
            

        );   
        
        config.collapsible          =true;//changed for module assets
        config.autoLoad             =false;
        if(config.fields==null)     config.fields       = ['module_id','w_m_id','module_name','isfree','w_p_id','w_p_name','w_p_alias','isactive','w_m_opt_count'];
        if(config.sorters==null)    config.sorters      = null;
        if(config.pageSize==null)   config.pageSize     = 100;
        if(config.url==null)        config.url          = "void"
        if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
        if(config.width==null)      config.width        = '100%';
         
        if(config.groupField==null) config.groupField   = "";
        
        //add listeners
        if(!config.listeners)
        config.listeners={};
        
        if(!config.override_selectionchange)
        	config.listeners.selectionchange=function(sm,records)
            {
                var selected = me.getSelectionModel().getSelection();
                if( typeof selected[0]=='undefined'|| selected[0]==null)return false;
                
                var record=selected[0].data;
                if(record.isactive=='t')
                {
                    Ext.getCmp("spectrumgrids_module_enable_btn"+me.config.generator).setIconCls("control_pause");
                    Ext.getCmp("spectrumgrids_module_enable_btn"+me.config.generator).setTooltip("Deactivate Selected Add-On");
                    Ext.getCmp("spectrumgrids_module_enable_btn"+me.config.generator).show();
                }
                    
                else
                {
                    Ext.getCmp("spectrumgrids_module_enable_btn"+me.config.generator).setIconCls("control_play_blue");
                    Ext.getCmp("spectrumgrids_module_enable_btn"+me.config.generator).setTooltip("Activate Selected Add-On");
                    Ext.getCmp("spectrumgrids_module_enable_btn"+me.config.generator).show();
                }                                                          
                
  
                var show=(record.w_m_opt_count!=0)?true:false; 
                Ext.getCmp("spectrumgrids_module_options_btn"+me.config.generator).setDisabled(!show);
            };
                
        if(!config.override_edit)
            config.listeners.edit=function()
            {                   
                var selected = me.getSelectionModel().getSelection();                                                            
                var selected_WMid=selected[0].data.w_m_id;
                var w_p_alias=selected[0].data.w_p_alias;
                
                var template_id=Ext.getCmp('type_id'+me.config.generator).getValue();
                 
                Ext.Ajax.request
                ({
                     url     : 'index.php/websites/json_update_org_moduel_pos/'+App.TOKEN,
                     params  : 
                     {
                         selected_WMid  :selected_WMid,
                         w_p_alias      :w_p_alias
                     },
                     success : function(o)
                     { 
                         var res=YAHOO.lang.JSON.parse(o.responseText);
                         if(res.result=="1")
                         {    
                             me.getSelectionModel().getSelection()[0].commit()
                             me.getStore().load({params:{template_id:template_id}});
                         }
                     },
                     failure: App.error.xhr
                });
            };        
 
        this.config=config;
        this.callParent(arguments); 
    }                       
 
        ,opts_grid :null
        ,load_opts_grid :function(_module_id)
        {  
            var me=this;                  
            var _generator=Math.random(); 
            
            var config=
            {
                generator       : _generator,
                owner           : me,
                
                columns         :
                [    
                    {
                        text: "Option"
                        ,dataIndex: 'opt_name'
                        ,flex:1
                    }                
                ],
                title           : '',
                extraParamsMore : {module_id:_module_id},
                collapsible     : false,
                
                url             : 'index.php/websites/json_get_moduleOpts/'+App.TOKEN,
                pageSize        :6,
                frame           :false,
                selModel        : Ext.create('Ext.selection.CheckboxModel', 
                                {
                                    mode:'MULTI'//,
                                   // listeners: {selectionchange: function(sm, selections) {}}
                                }),
                                
                //customized Components

                searchBar       :true
 
            }
            me.opts_grid = Ext.create('Ext.spectrumgrids.moduleOpts',config);
            me.opts_grid.getStore().on("load",function()
            {
                for (var i=0;i<me.opts_grid.getStore().getCount();i++)
                {
                    var rec=me.opts_grid.getStore().getAt(i);
                    if(rec.data.w_m_id!=null)
                        me.opts_grid.getSelectionModel().selectRange(i, i, true);
                        //grid.getSelectionModel().select(rec,true,false);    
                }
            })
            return me.opts_grid ;
        }
});}
