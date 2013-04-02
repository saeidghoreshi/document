var rgtClass='Ext.spectrumgrids.registeredteams';
if(!App.dom.definedExt(rgtClass)){
Ext.define(rgtClass,    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    }
    ,constructor : function(config) 
    {   
        var me=this;
        config.columns= 
        [
            {
                dataIndex   : 'team_name',
                header      : 'Team Name',
                sortable    : true,
                width       : 100
            },
            {
                dataIndex   : 'division_name',
                header      : 'Division',
                sortable    : true,
                flex        : 1
            }
        ];
        //Fresh top/bottom components list
        if(config.topItems==null)     config.topItems=[];
        if(config.bottomLItems==null) config.bottomLItems=[];
        if(config.bottomRItems==null) config.bottomRItems=[];
        
        config.selModel = Ext.create('Ext.selection.CheckboxModel');
        config.bottomLItems.push
        (
           /* 
           //a gr{
                iconCls : 'application_side_contract',
                xtype   : 'button',
                text    : '',
                tooltip : 'Return Back to Season',
                handler : function()
                {
                    oSeason.grid.expand();//a grid should never reference a controller, that is not allowed. 
                    //also, this button does not exist in ANY other component with multiple grids, why here only?
                }
            },*/
           
        ); 
        config.bottomRItems.push
        ( 
        	{
                iconCls : 'pencil',
                xtype   : 'button',
                text    : '',
                tooltip : 'View & Edit Details',
                handler : function()
                {
                    if(me.getSelectionModel().getSelection().length==0)
                    {
                        Ext.MessageBox.alert({title:"Nothing to Edit",msg:"Please Select a Record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        return;
                    }  
                    if(me.getSelectionModel().getSelection().length>1)
                    {
                        Ext.MessageBox.alert({title:"Only edit one at a time",msg:"Please Select only One Record to Edit", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        return;
                    }  
                    var record=me.getSelectionModel().getSelection()[0].data;
                    
                    var post={}
                    post["season_id"]       =oSeason.grid.getSelectionModel().getSelection()[0].data.season_id;
                    post["team_entity_id"]  =record.team_entity_id;
                    post["team_org_id"]     =record.org_id;
                   
                    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
                    //Build  customFilldsGrid
                    var cfConfig=
                    {
                        generator       : Math.random(),
                        owner           : this,
                        url             : "/index.php/registration/json_getRegisteredTeamInfo/TOKEN:"+App.TOKEN,
                        title           : 'Registration Custom Field Management',
                        height         :200,
                        columns         :
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
                                text        : "Value",
                                xtype       : 'templatecolumn',
                                tpl         : '<div style="text-align:left;color:green;">{field_value}</div>',
                                flex        : 1
                            }
                        ],
                        
                        extraParamsMore : post,
                        collapsible     : false,
                        
                        //customized Components
                        rowEditable     :true,
                        groupable       :true,
                        bottomPaginator :false,
                        searchBar       :false,    
                        //Function appendable or overridble
                        
                        override_edit           :false,
                        override_itemdblclick   :false,
                        override_selectionchange:false,
                        override_expand         :false,
                        override_collapse       :false
                        
                        
                    }
                    var cf= Ext.create('Ext.spectrumgrids.customfields',cfConfig);
                    
                    Ext.Ajax.request(
                    {
                        url     : "/index.php/registration/json_getRegisteredTeamManagerInfo/TOKEN:"+App.TOKEN,
                        params  : post,
                        success : function(response)
                        {
                                var html=response.responseText;
                                var conf=
                                {
                                        width       : 600,
                                        height      : 300,
                                        collapsible :false
                                }
                                var form        =registrationForms.form_registeredTeamsDetails(html);
                                var formFinal   =new Ext.spectrumforms.mixer(conf,[cf,form],['grid','form']);
                                var winConfig=
                                {
                                   title       : "Team : '"+record.team_name+"' Registration Details",
                                   final_form  : formFinal
                                }
                                var win=new Ext.spectrumwindow.registration(winConfig);
                                win.show();
                                
                                //Set Default
                                Ext.getCmp('btn_season_filter'+cfConfig.generator).setVisible(false);
                                Ext.getCmp('customfields_add_btn').setDisabled(true);
                                Ext.getCmp('customfields_del_btn').setDisabled(true);
                        }
                    });
                }
            },
            {
                iconCls : 'tick',
                xtype   : 'button',
                text    : '',
                tooltip : 'Approve',
                handler : function()
                {
                    if(me.getSelectionModel().getSelection().length==0)
                    {
                       // Ext.MessageBox.alert({title:"Nothing to Edit",msg:"Please Select a Record", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        return;
                    }  
                    var idsCommaBAsed='';
                    var records=me.getSelectionModel().getSelection();
                    for(var i in records)
                            idsCommaBAsed+=records[i].data.team_entity_id+',';
                            
                    if(idsCommaBAsed!='')idsCommaBAsed=idsCommaBAsed.substring(0,idsCommaBAsed.length-1);
                    
                    var post={};
                    post["team_entity_ids"]=idsCommaBAsed;
                     
                    Ext.Ajax.request(
                    {
                        url     : "/index.php/registration/json_approveTeams/"+App.TOKEN,
                        params  : post,
                        success : function(response)
                        {
                        	try{
                             var res=YAHOO.lang.JSON.parse(response.responseText);
							}catch(e)
							{
								App.error.xhr(response);
							}
                             var _season_id=oSeason.grid.getSelectionModel().getSelection()[0].data.season_id;
                             me.getStore().load({params:{season_id:_season_id}});
                             Ext.MessageBox.alert({title:"Status",msg:"Selected Teams Approved Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                        }
                        ,failure:App.error.xhr
                    });
                }
            }   
        ); 
                        
            
        if(config.fields==null)     config.fields       = ["org_id","team_name","team_entity_id","season_id","division_id","team_season_division_id","division_name"];
        if(config.sorters==null)    config.sorters      = config.fields;
        if(config.pageSize==null)   config.pageSize     = 100;
        if(config.url==null)        config.url          = 'index.php/registration/json_get_season_registrations/TOKEN:'+App.TOKEN;
        if(config.extraParams==null)config.extraParams  = {start:0,limit:config.pageSize};
        if(config.width==null)      config.width        = '100%';
        if(config.height==null)     config.height       = 300;
        if(config.groupField==null) config.groupField   = "division_name";
        
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
                var rows=me.getSelectionModel().getSelection();
                if(!rows.length)return;//added to avoid possible errors
                var record=rows[0].data;
                
                var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                Ext.Ajax.request(
                {
                    url: "index.php/registration/json_approvediscard_registration/TOKEN:"+App.TOKEN,
                    params: record,
                    success: function(response)
                    {
                         box.hide();
                         var res=YAHOO.lang.JSON.parse(response.responseText);
                         if(res.result=="1")
                         {          
                             me.getStore().load();   
                         }
                    }
                    ,failure:function(response)
                    {
                        App.error.xhr(response);
                        me.getStore().load();
                    }
                }); 
            });                  
        }
                               
        this.callParent(arguments);         
    }
});
}
