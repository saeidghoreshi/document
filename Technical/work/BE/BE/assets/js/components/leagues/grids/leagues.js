var leagueGrid='Ext.spectrumgrids.leagues';// a must have to fix bugs
if(!App.dom.definedExt(leagueGrid)){
Ext.define(leagueGrid,    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    }
    ,config                 :null
    ,refresh:function()
    {
		this.getStore().load();
    }
    
    ,constructor : function(config) 
    {   
        this.domainListStore    =//needed for all forms
        		new simpleStoreClass().make(['id','domain'],"index.php/endeavor/json_getDomainNames/"+App.TOKEN+'/');  
        var numeric_width=60;//keep them all the same size   
        
     	 if(typeof config.useCheckbox   == 'undefined') config.useCheckbox=true; 
     	 if(config.useCheckbox  )config.selModel =Ext.create('Ext.selection.CheckboxModel', 
         {
           		mode        :'MULTI'
 
        });
     	 
        if(!config.id)config.id='my_league_grid__';//+Math.random();
        if(Ext.getCmp(config.id))Ext.getCmp(config.id).destroy();//in case id is passed in, and it already exists
                var league_model_id='LeagueModel';

        config.store= Ext.create( 'Ext.data.Store',
		{
			model:league_model_id,autoDestroy:false,autoSync :false,autoLoad :true,remoteSort:false,pageSize:100 ,
		    proxy: 
		    {   
            	type: 'rest',url: 'index.php/leagues/json_get_leagues/'+App.TOKEN,
		        reader: {type: 'json',root: 'root',totalProperty: 'totalCount'}
		      //  extraParams:{}
		    }  
		}); 
        if(config.width==null)      config.width        = '100%';
  
        
        config.columns  =
        [    
             {
                text: "League Name"
                ,dataIndex: 'league_name'
                ,flex:0.7//70% of remainng: flex is a percentage
                ,editor: 
                {
                    xtype:'textfield',
                    allowBlank: false
                }
            }
            ,{
            	text: 'Website'
            	,dataIndex : 'url'  
            	,flex:0.3//thirty percent
            	
            }
            ,{
            	text: 'Teams'
            	,dataIndex : 'team_count'  
            	,width:numeric_width
            	
            }
            ,{
            	text: 'Players'
            	,dataIndex : 'player_count'
            	,width:numeric_width
			}
            ,{
                text: "Users"
                ,dataIndex: 'league_users_count_image'
                ,width:numeric_width
            }          
   
        ];
        //Fresh top/bottom components list
        if(config.topItems==null)     config.topItems=[];
        if(config.bottomLItems==null) config.bottomLItems=[];
        if(config.bottomRItems==null) config.bottomRItems=[];
            /*
            config.topItems.push
            (
                //active btn
                {
                    id          : "btn_spectrumgrids_leagues_active",
                    xtype       : 'button',              
                    text        : 'Active',
                    tooltip     : 'Show Active Records',                               
                    enableToggle:true,
                    
                    toggleHandler: function(button,pressed)
                    {          
                        Ext.getCmp('btn_spectrumgrids_leagues_active').addCls("x-btn-default-toolbar-small-pressed");
                        Ext.getCmp('btn_spectrumgrids_leagues_notactive').removeCls("x-btn-default-toolbar-small-pressed");
                        Ext.getCmp('btn_spectrumgrids_leagues_all').removeCls("x-btn-default-toolbar-small-pressed");
                        me.getStore().load({params:{active:'true'}});
                    }
                }
                ,'-'
                //Not active btn
                ,{
                    id          : "btn_spectrumgrids_leagues_notactive",
                    xtype       : 'button',
                    text        : 'Not Active',
                    tooltip     : 'Show Not Active Records',
                    enableToggle:true,
                    
                    toggleHandler: function(button,pressed)
                    {                                                                
                        Ext.getCmp('btn_spectrumgrids_leagues_active').removeCls("x-btn-default-toolbar-small-pressed");
                        Ext.getCmp('btn_spectrumgrids_leagues_notactive').addCls("x-btn-default-toolbar-small-pressed");
                        Ext.getCmp('btn_spectrumgrids_leagues_all').removeCls("x-btn-default-toolbar-small-pressed");
                        me.getStore().load({params:{active:'false'}});
                    }
                }
                ,'-'
                //All btn
                ,{
                        id          : "btn_spectrumgrids_leagues_all",
                        xtype       : 'button',
                        text        : 'All',
                        pressed     : true,
                        enableToggle: true,
                        tooltip     : 'Show All records',
                        toggleHandler: function(button,pressed)
                        {
                            Ext.getCmp('btn_spectrumgrids_leagues_active').removeCls("x-btn-default-toolbar-small-pressed");
                            Ext.getCmp('btn_spectrumgrids_leagues_notactive').removeCls("x-btn-default-toolbar-small-pressed");
                            Ext.getCmp('btn_spectrumgrids_leagues_all').addCls("x-btn-default-toolbar-small-pressed");
                            me.getStore().load({params:{active:'all'}});
                        },       
                }
            );
            */
   
        config.bottomLItems.push
        (
            {
                iconCls :'fugue_briefcase--plus',
                xtype   : 'button',
                text    : '',
                tooltip : 'Create New League',
                scope:this,
                handler : function()
                {
                    //Spectrum.windows.league
                    //build main form                      
                    var window_id='_create_leaguewindow_';
                    
                    var frm=Ext.create('Spectrum.forms.league',
                    {
                        window_id       :window_id,
                        domainListStore :this.domainListStore
                    });
                    var win=Ext.create('Spectrum.windows.league',{id:window_id,items:frm});  
                        
                    win.show();
                    win.on('hide',function(){this.store.load();},this);    
                        
                    //var form=App.forms.create_form_league_details(me.config,'');
                }
            }
        );
        if(!config.bbar)config.bbar=[];
        config.bottomRItems.push
        (
             //Email
             {
 
                iconCls :'fugue_mail-send',
                xtype: 'button',
                text: '',
                scope:this,
                tooltip: 'Send Welcome Email',
                handler: function()
                {
                    var selection = this.getSelectionModel().getSelection()
                    if(selection.length==0)
                    {
                        //Ext.MessageBox.alert({title:"Error",msg:"Please select a league", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                        return;
                    }
                    var _league_id=selection[0].get('league_id');

                    var callback=
                    {	
                        failure:App.error.xhr,
                        scope:this,
                        success:function(o)
                        {
                           this.refresh();
                            var r=o.responseText;
                            Ext.MessageBox.alert({title:"Status",msg:r, icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
                        }
                    }
                    YAHOO.util.Connect.asyncRequest('POST','index.php/leagues/post_send_welcome_email/'+App.TOKEN,callback,'league_id='+_league_id);
 
                    
                }}
             
             //users
             ,config.bbar
             
             ,'-'
             //Edit
             ,{
             	 iconCls :'fugue_briefcase--pencil',
             	 xtype   : 'button',
             	 text    : '',
             	 scope:this,
             	 tooltip : 'Modify Selected League',
             	 handler : function()
	            {
	                var selected=this.getSelectionModel().getSelection();
	                if(selected.length==0)
	                {
	                    return;
	                }
	                var record=selected[0];//
	                var window_id='edit_leaguewindow__';
	                var form=Ext.create('Spectrum.forms.league',
                    {
                        window_id       :window_id,
                        domainListStore :this.domainListStore
                    });  
 
	                form.loadRecord(record);
	                var win=Ext.create('Spectrum.windows.league',{id:window_id,items:form,hide_email:true,title:'Edit League'});  
	                 
	                win.on('hide',function(){this.store.load();},this);    
	                win.show();
	                 
	            }}
             
             //delete
             ,{
             	 iconCls :'fugue_minus-button',
             	 xtype: 'button',
             	 text: '',
             	 tooltip: 'Remove Selected League',
             	 scope:this,
             	 handler: function()
                {   
                    var recs=this.getSelectionModel().getSelection();
                    if(recs.length==0)
                    {
                       // Ext.MessageBox.alert({title:"Warning",msg:"No League Record Selected", icon: Ext.Msg.WARNING,buttons: Ext.MessageBox.OK});
                        return;
                    }
                    else if(recs.length==1)
                    { 
	                    var record=recs[0];
	                    var msg= "Are you sure?  The league "+record.get('league_name')+" will be deleted forever!";
	                    Ext.MessageBox.show({title:'Delete?',icon: Ext.MessageBox.QUESTION,msg:msg,scope:this,buttons: Ext.Msg.YESNO,fn:function(btn_id)
						{
							if(btn_id=='ok'||btn_id=='yes')
							{
								var callback={failure:App.error.xhr,success:function(o)
	                            {
	                                this.refresh();
	                            },scope:this};
	                            YAHOO.util.Connect.asyncRequest('POST','index.php/associations/json_delete_league/'+App.TOKEN,callback
	                            ,'league_id='+record.get('league_id'));
								
							}
						}});
					}
					else
					{   //recs.length > 1
					
						var msg= "Are you sure?  All "+recs.length+" leagues will be deleted forever, including all their invoices, standings, websites, and teams!"
						 +"  Remember, you can always remove users from each league, or un-publish announcements and schedules as an alternative.";
	                    Ext.MessageBox.show({title:'Delete?',icon: Ext.MessageBox.QUESTION,msg:msg,scope:this,buttons: Ext.Msg.YESNO,fn:function(btn_id)
						{
							if(btn_id=='ok'||btn_id=='yes')
							{
								var league_id_array= new Array();
								
								for(i in recs) if(recs[i])
								{
									league_id_array.push(recs[i].get('league_id'));
									
								}
								 
								var callback={failure:App.error.xhr,success:function(o)
	                            {
	                                this.refresh();
	                            },scope:this};
	                            
	                            YAHOO.util.Connect.asyncRequest('POST','index.php/associations/json_delete_league/'+App.TOKEN,callback
	                            ,'league_id_array='+YAHOO.lang.JSON.stringify(league_id_array));
	                            
	                            
	                            
								
							}
						}});
						
						
						
						
						
					}
					
                }
            }
        );   
        config.bbar=null;

        if(!config.listeners)config.listeners={};
        if(!this.override_edit)
        {   
            //if user did not override our edit, then apply it
            config.listeners.edit=
            {
            	scope:this,
            	fn:function(e)
	            {
	                //e.record.data//e.record.data[e.field]
	                var record=e.record.data;
	                var callback=
	                {
	                	success:function(o)
		                {
		                     e.record.commit(); 
		                     //e.record.reject(); 
		                },failure:App.error.xhr
		                ,scope:this
	                }
	                YAHOO.util.Connect.asyncRequest("post",'index.php/associations/json_update_league_name/'+App.TOKEN
	                ,callback
	                ,"league_id="+record.league_id+"&league_name_new="+record.league_name);
				}
	        }; 
            
        }
 
        
        
        if(typeof config.collapsible == 'undefined')config.collapsible =true;
        if(typeof config.rowEditable == 'undefined')config.rowEditable =true; 
        if(typeof config.bottomPaginator == 'undefined')config.bottomPaginator =true;
        if(typeof config.searchBar == 'undefined')config.searchBar =true;
 
        this.callParent(arguments);
    }                       
        
 
});
}
