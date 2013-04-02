var rpClass='Ext.spectrumgrids.rosterperson';
if(!App.dom.definedExt(rpClass)){
Ext.define(rpClass,    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    },
    constructor : function(config) 
    {    
        var me=this;
        if(typeof config.hide_approve=='undefined') config.hide_approve=false;
        this.season_id=config.season_id;
        this.team_id=config.team_id; 
        config.columns= 
        [

            {
                dataIndex   : 'person_fname'
                , header    : 'First name'
                , sortable  : true
                , flex:0.5
                , editor: { allowBlank: false}

            },                
            {
                dataIndex   : 'person_lname'
                , header    : 'Last Name'
                , sortable  : true
                , flex:0.5
               , editor: { allowBlank: false}
            },
            {
                dataIndex   : 'person_birthdate_display'
                , header    : 'Birth Date'
                , sortable  : true
                , width:130
              , editor: { allowBlank: true}
                ,field: {xtype: 'datefield',format: 'Y/m/d'}
            },
            
            {
                dataIndex   : 'person_gender_icon'
                , header    : 'Gender'
                , sortable  : true
                , width      : 50
               , editor: { xtype: 'combobox', allowBlank: true
                   , store: [
                       ['<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/female.png />','Female']
                       ,['<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/male.png />','Male']]
                       ,editable:false}
            },
            {
                dataIndex   : 'effective_range_start_display'
                , header    : 'Date Added'
                , sortable  : true
                , width      : 150
                ,field: 
                {
                    xtype: 'datefield'
                    ,format: 'Y/m/d'
                }
            },
            {
                dataIndex   : 'status_icon'
                , header    : 'Status'
                , sortable  : true
                , width:100
                
            }
            
        ];
            
             
        if(config.fields==null)     config.fields       = ['team_roster_id','roster_person_id','person_id','entity_id','person_fname','person_lname','person_gender','person_gender_icon','person_birthdate','person_birthdate_display','status_id','status_name','status_icon','effective_range_start','effective_range_start_display','comment'];
        if(config.sorters==null)    config.sorters      = ['team_roster_id','roster_person_id','person_id','entity_id','person_fname','person_lname','person_gender','person_gender_icon','person_birthdate','person_birthdate_display','status_id','status_name','status_icon','effective_range_start','effective_range_start_display','comment'];
        if(config.pageSize==null)   config.pageSize     = 100;
        if(config.url==null)        config.url          = "/index.php/teams/json_get_roster_persons/"+App.TOKEN;
       
        config.extraParams = {season_id:this.season_id,team_id:this.team_id};
        config.extraParamsMore = {season_id:this.season_id,team_id:this.team_id};
        if(config.width==null)      config.width        = '100%';
 
		config.rowEditable     =true;
        if(typeof config.collapsible=='undefined')     config.collapsible=false;
        config.bottomPaginator =true;
        config.searchBar       =true;  
        if(!config.dockedItems){config.dockedItems=new Array();config.dockedItems.push({dock:'top',xtype:'toolbar',items:[]});}
        config.dockedItems.push(
		{
            dock: 'bottom',
            xtype: 'toolbar',
            items: 
            [

                 {iconCls :"fugue_user--plus",text: '',scope:this,tooltip: 'New roster person',     handler:function()
                {
                	 
                	if(!this.season_id || this.season_id<0)
                	{
						Ext.MessageBox.alert('Cannot Add',"Select a season first.  If no seasons appear in the upper-left "
							+'menu, ensure your team is registered for a season first.');
						return;
                	}
                	var window_id='roster_person_window_';
                    var f= Ext.create('Spectrum.forms.user_create',{login_optional:true,window_id:window_id});//people for roster do not need login
                    
                    var w=Ext.create('Spectrum.windows.user',{title:'Roster Person',items:f,id:window_id});
                    
                    //buttons:[{text:'Assign Person to Roster',iconCls:'tick',disabled:false,handler:function(o)
                    w.on('hide',function()
					{
 
						var person_id=Ext.getCmp('form_person_id').getValue();	

						 
						if(!person_id || person_id==-1)
						{
							//Ext.MessageBox.alert('Error',"Cannot do that yet, save the person details first  ");
							return;
						}
						var post='person_id='+person_id
							+'&season_id='+this.season_id
							+'&team_id='  +this.team_id;
						
						var url='index.php/teams/post_assign_roster_person/'+App.TOKEN;
						var callback={scope:this,failure:App.error.xhr,success:function(o)
						{
							try
	                        {
				              var res=YAHOO.lang.JSON.parse(o.responseText);
	                          this.getStore().load(); 
	                        }
							catch(e)
							{
								App.error.xhr(o);
							}

						}};
						YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
					},this);
                    w.show();
                }}
                 
                 ,'-'
                 ,{iconCls:'fugue_printer',scope:this,tooltip:'Print Active Roster',handler:function()
                 {
                 	 var url="index.php/teams/html_team_season_roster/"+App.TOKEN;

                 
                 	 var post="team_id="+this.team_id+"&season_id="+this.season_id;
 
                 	 
                 	 YAHOO.util.Connect.asyncRequest('POST',url,{failure:App.error.xhr,success:function(o)
                 	 {
						 var w=window.open('','_blank');
					  
						 w.document.write(o.responseText);
						 
                 	 }},post);
                 	 
					 
                 }}
                 
                 ,{iconCls:'fugue_printer--exclamation',scope:this,tooltip:'Print Complete Roster',handler:function()
                 {
                 	 console.log('sam todo;');
				 }}
                 , '->'
                 ,{iconCls :"fugue_tick",text: '',hidden:config.hide_approve,scope:this,tooltip: 'Accept Roster Change',      handler: function()
                    {   
                        var sel=this.getSelectionModel().getSelection();
                        if(sel.length==0)   
                        {
                            //Ext.MessageBox.alert('Error','No record selcted');//SB: changed to Ext alerts
                            return;
                        }
                        var record=sel[0].data;

                          
                         // record["roster_person_id"]=record.roster_person_id;
                     
                          record.team_org_id=record.org_id;//SB: fixed it, above line was null
                          record.person_fname=escape(record.person_fname);
                          record.person_lname=escape(record.person_lname);//SB: always escape otherwise the url might fail
                            
  
                          Ext.Ajax.request(
                          {
                              url: 'index.php/teams/json_accept_roster_person/'+App.TOKEN,
                              method:"POST",
                              params: record,
                              failure:App.error.xhr,
                              scope:this,//SB: added fail state and scope
                              success: function(o)
                              {
 
                                   try
                            		{
			                            var res=YAHOO.lang.JSON.parse(o.responseText);
                            		    this.getStore().load(); 
                            		}
									catch(e)
									{
										App.error.xhr(o);
									}
                              }
                          });    
                    }
                }
                 ,{

                    iconCls :"fugue_cross",
                    text: '',hidden:config.hide_approve,
                    tooltip: 'Decline Roster Change',
                    scope:this,//sb: scope needed       
                    handler: function()
                    {                
                        var sel=this.getSelectionModel().getSelection();
                        if(sel.length==0)   
                        {
                            //Ext.MessageBox.alert('Error','No record selcted');//SB: changed to Ext alerts
                            return;
                        }
                        var record=sel[0].data;
                        record.team_org_id=record.org_id;//SB: fixed it, above line was null
                        record.person_fname=escape(record.person_fname);
                        record.person_lname=escape(record.person_lname);//SB: always escape otherwise the url might fail
                        record.comment='';
                        Ext.MessageBox.prompt('Enter a Reason',"",function(btn_id,text)
                        {
                        	if(btn_id != 'ok' && btn_id != 'yes') {return;}
                        	record.comment =escape(text);
                             
						    Ext.Ajax.request(
						    {
	                          url: 'index.php/teams/json_decline_roster_person/'+App.TOKEN,
	                          params: record,
	                          method:"POST",
	                          scope:this,
	                          failure:App.error.xhr,
	                          success: function(o)
	                          {
                                    
	                               try
                            		{
			                          var res=YAHOO.lang.JSON.parse(o.responseText);
                            		  this.getStore().load(); 
                            		}
									catch(e)
									{
										App.error.xhr(o);
									}
	                          }
	                       });    
                        },this,true,'No Reason Given'//this for scope, boolean for multiline input being true==turned on
						);
                                 
                    }
                }
                ,'-'//SB added for looking nice
                 ,{
                    // xtype: 'button',
                     iconCls :"fugue_minus-button",
                     text: '',scope:this,
                     tooltip: 'Delete Roster person',       
                     handler: function()
                     {   
                        var sel=this.getSelectionModel().getSelection();
                        if(sel.length==0)   
                        {
                            //Ext.MessageBox.alert('Error','No record selcted');//SB: changed to Ext alerts
                            return;
                        }
                        var record=sel[0].data;
                         var _roster_person_id=record.roster_person_id;
                         
                        
                         var full_name=record.person_fname+" "+record.person_lname;
                         var msg="Remove '"+full_name+"' from this roster?";
                         Ext.MessageBox.confirm('Delete?', msg, function(btn_id)
                         {
                            if(btn_id!='yes' && btn_id != 'ok'){return;}
                              
                            var post='roster_person_id='+_roster_person_id;
                            var url='index.php/teams/post_delete_roster_person/'+App.TOKEN;
                            var callback={scope:this,success:function(o)
                            {
                            	
                            	try
                            	{
		                          var res=YAHOO.lang.JSON.parse(o.responseText);
                            	  this.getStore().load(); 
                            	}
								catch(e)
								{
									App.error.xhr(o);
								}
                                  
                            },failure:App.error.xhr};
                            YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
                              
                         },this);
                     }
                 }
            ]
        	} 
         ) ;
            

        config.listeners=
        {
			edit:{scope:this,fn:function(e)
			{
                var sel=this.getSelectionModel().getSelection();
                if(sel.length==0)   
                {
                    //Ext.MessageBox.alert('Error','No record selcted');//SB: changed to Ext alerts
                    return;
                }
                var record=sel[0].data;
               // var _roster_person_id=record.roster_person_id;
                
                // Update roster person  from row editor

                 record.person_birthdate  =record.person_birthdate_display;
 
                 record.start_date        =record.effective_range_start_display;
                 
               //  var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                 Ext.Ajax.request(
                 {
                     url        : 'index.php/person/json_update_person/'+App.TOKEN,
                     params     : record,
                     method     :"POST",
                     scope      :this,
                     failure    :App.error.xhr,
                     success    : function(o)
                     {
                     //     box.hide();
                     	try{
                          var res=YAHOO.lang.JSON.parse(o.responseText);
                         // if(res.result=="1")
                          //{
                              this.getStore().load();   
                          //}
						}
						catch(e)
						{
							App.error.xhr(o);
						}
                          
                     }
                 });          
            }}  
        }
        
        this.callParent(arguments); 
        //.log(this.getStore());
    }                       
        

});
}

