Ext.define('Ext.spectrumgrids.customfields',    
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
        ,season_id              :null
        ,a_o_t_id               :null
        
        ,constructor : function(config) 
        {   
            var me=this;  
            if(config.columns == null)                           
                config.columns  =
                [    
                    {
                        text        : "Field Title",
                        dataIndex   :'field_title',
                        editor      :
                        {
                            xtype       :'textfield' ,
                            allowBlank  : false
                        },
                        flex       : 1
                    }
                    ,{
                        text        : "Applies To",
                        xtype       :'templatecolumn',
                        tpl         :'<div style="text-align:left;color:green;">{appliesto}</div>',
                        flex        : 1
                    }
                ];
            //Fresh top/bottom components list
            if(config.topItems==null)     config.topItems=[];
            if(config.bottomLItems==null) config.bottomLItems=[];
            if(config.bottomRItems==null) config.bottomRItems=[];
            
            
            config.topItems.push
            (
                     {
                         id     :'btn_season_filter'+config.generator,
                         text   :"Select a Season ...",
                         pressed:true,
                         menu   :[],
                         iconCls:'bullet_arrow_down',
                         flex   :1
                     }
            ); 
            config.bottomLItems.push
            (
                {
                        id      : "customfields_add_btn",
                        iconCls : 'add',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Add New ',
                        handler : function()
                        {
                             var form   =registrationForms.form_custom_form();
                             var _config=
                             {
                                    width   : 400,
                                    height  : 100,
                                    bottomItems:   
                                    [
                                        '->'
                                        ,{   
                                             xtype   :"button",
                                             text    :"Save",
                                             iconCls :'table_save',
                                             pressed :true,
                                             tooltip :'',
                                             handler :function()
                                             {  
                                                if(final_form.getForm().isValid())
                                                {             
                                                    var values=final_form.getForm().getValues();
                                                    
                                                    var post={}
                                                    post["season_id"]           =me.season_id;
                                                    post["slave_org_type_id"]   =values["slave_org_type_id"];
                                                    post["field_title"]         =values["field_title"];
                                                    
                                                    var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                                    Ext.Ajax.request(
                                                    {
                                                        url     : 'index.php/teams/json_add_customfield/TOKEN:'+App.TOKEN,
                                                        params  : post,
                                                        success : function(response)
                                                        {
                                                             box.hide();                                                                
                                                             var res=YAHOO.lang.JSON.parse(response.responseText);
                                                             if(res.result=="1")
                                                             {                                              
                                                                 Ext.MessageBox.alert({title:"Status",msg:"Custom Filed Named <b>'"+post["field_title"]+"'</b> Saved Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                                                 win.Hide();
                                                                 me.getStore().load({params:{season_id:me.season_id}});
                                                             }
                                                        }
                                                    });    
                                                }      
                                             }                          
                                        }
                                    ]
                             }
                                       
                             var final_form=new Ext.spectrumforms.mixer(_config,[form],['form']);
                             
                             var win_cnf=
                             {
                                title       : 'Custom Fields',
                                final_form  : final_form
                             }
                             var win=new Ext.spectrumwindow.registration(win_cnf);
                             win.show(); 
                        }
                } 
            );       
            config.bottomRItems.push
            (
                {
                        id      : "customfields_del_btn",
                        iconCls : 'delete',
                        xtype   : 'button',
                        text    : '',
                        tooltip : 'Delete Field',
                        handler : function()
                        {
                            if(me.getSelectionModel().getSelection().length==0)
                            {
                                Ext.MessageBox.alert({title:"Error",msg:"Please Select a Field", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                                return;
                            }
                            var record=me.getSelectionModel().getSelection()[0].data;
                            
                            var post={}
                            post["season_id"]   =me.season_id;
                            post["field_id"]    =record.field_id;
                            
                            var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                            Ext.Ajax.request(
                            {
                                url     : 'index.php/teams/json_delete_customfield/TOKEN:'+App.TOKEN,
                                params  : post,
                                success : function(response)
                                {
                                     box.hide();
                                     var res=YAHOO.lang.JSON.parse(response.responseText);
                                     if(res.result=="1")
                                     {
                                         Ext.MessageBox.alert({title:"Status",msg:"Field Named <b>'"+record["field_title"]+"'</b> Removed Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                         me.getStore().load({params:{season_id:me.season_id}});
                                     } 
                                }
                            });    
                        }
                } 
            ); 
              
            config.autoLoad             =false;
            if(config.fields==null)     config.fields       = ['field_id','master_entity_id','slave_org_type_id','field_title','season_id','appliesto','field_value'];
            if(config.sorters==null)    config.sorters      = config.fields;
            if(config.pageSize==null)   config.pageSize     = 100000;
            if(config.url==null)        config.url          = 'index.php/teams/json_get_customfields_by_season/TOKEN:'+App.TOKEN;
            if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
            if(config.width==null)      config.width        = '100%';
            if(config.height==null)     config.height       = 350;
            if(config.groupField==null) config.groupField   = "";
            
            this.override_edit          =config.override_edit;
            this.override_selectiochange=config.override_selectionchange;
            this.override_itemdblclick  =config.override_itemdblclick;
            this.override_collapse      =config.override_collapse;
            this.override_expand        =config.override_expand;
            
            me.buildSeasonFilter();
            
            this.config=config;
            this.callParent(arguments); 
            
            var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
            Ext.Ajax.request(
            {
                url: 'index.php/permissions/json_get_active_org_and_type/TOKEN:'+App.TOKEN,
                params: {test:'test'},
                success: function(response)
                {
                    box.hide()
                    var res=YAHOO.lang.JSON.parse(response.responseText);
                    me.a_o_t_id    =res.result.org_type_id;
                }
            }); 
        },
        afterRender: function() 
        {  
            var me=this;
            
            if(!me.override_edit)
            {   
                me.on("edit",function(e)
                {
                    var record=e.record.data;
                    var post={}
                    post["field_id"]    =record.field_id;
                    post["field_title"] =record.field_title;
                    
                    var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                    Ext.Ajax.request(
                    {
                            url     : 'index.php/teams/json_update_custom_field_title/'+App.TOKEN,
                            params  : post,
                            success : function(response)
                            {
                                 box.hide();
                                 var res=YAHOO.lang.JSON.parse(response.responseText);
                                 if(res.result=="1")
                                 {
                                     Ext.MessageBox.alert({title:"Status",msg:"Selected Custom Field Successfully Renamed to <b>'"+record.field_title+"'</b>", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                                     e.record.commit();
                                 }                     
                                 else 
                                     e.record.reject();
                            }
                    });    
                }); 
            }                                       
            
            if(!this.override_selectionchange)                              
            {   
                this.on("selectionchange",function(sm,records)
                {   
                    if(records ==undefined || records =='undefined' || records.length==0)return false;
                    var rec=records[0].raw;
    
                });
            }
             
            this.callParent(arguments);         
        }
        ,buildSeasonFilter:function()
        {
            var me=this;
            
            var season_url='index.php/season/json_active_league_seasons/'+App.TOKEN;
            YAHOO.util.Connect.asyncRequest('GET',season_url,{scope:this,success:function(o)
            {
                var name;
                var id;
                var seasons=YAHOO.lang.JSON.parse(o.responseText).root;
        
                var seasons_filter=[];
                var itemClick=function(o,e)
                {
                    var name = o.text;
                    var id   = o.value;

                    me.season_id=id;
                    Ext.getCmp('btn_season_filter'+me.config.generator).setText(name);
                    
                    me.getStore().load({params:{season_id:me.season_id}});
                };
                
                seasons_filter.push({text:'Unassigned',value:-1,handler:itemClick,scope:this,iconCls:'layout'});
                
                var icon,foundActive=false;                
                for(var i=0;i<seasons.length;i++)
                {
                    name=seasons[i]['season_name']+" : "+seasons[i]['display_start']+" - "+seasons[i]['display_end'];
                    id  =seasons[i]['season_id'];
                    icon='';
                    
                    if(seasons[i]['isactive']=='t')
                    {
                        icon='tick';
                        foundActive={text:name,value:id};
                    }
                    else
                        icon='cross';
                        
                    seasons_filter.push({text:name,value:id,handler:itemClick,scope:this,iconCls:icon});
                }
                Ext.getCmp('btn_season_filter'+me.config.generator).menu=Ext.create('Spectrum.btn_menu',{items:seasons_filter});
                if(foundActive)
                {
                    me.season_id=foundActive.value;
                    Ext.getCmp('btn_season_filter'+me.config.generator).setText(foundActive.text);
                    //Load based on Default season
                    me.getStore().load({params:{season_id:me.season_id}});
                }
            }}); 
        }
});
