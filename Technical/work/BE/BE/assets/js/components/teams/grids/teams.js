
var teamModel = 'Team';

var gridTeamsCls = 'Spectrum.grids.teams';
if(!App.dom.definedExt(gridTeamsCls)){
Ext.define(gridTeamsCls,
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },

    season_id:-1,
    refresh:function()
    {
    	this.store.proxy.extraParams.season_id=this.season_id;
    	this.store.loadPage(1);
    	return;

    },
    constructor     : function(config)
    {  
    	//.log('grid.teams constructor');
    	config.searchBar=true;
    	config.bottomPaginator=true;
    	config.rowEditable=true;
    	//Ext.create('Ext.selection.RowModel',{mode:"MULTI"})
    	config.selModel = Ext.create('Ext.selection.CheckboxModel', {mode:'MULTI'});
    	var id='grid_teams_manage';
		if(!config.id){config.id=id;}
		if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		this.id=config.id;
		
     	config.store =Ext.create( 'Ext.data.Store',
    	{
    		groupField:'parent_division_name',//needed for groupfeature
			remoteSort:false,pageSize:100 ,
			model:teamModel,autoDestroy:true
  			//moved the proxy to the model.

	    });
     	
		config.features= 
		[Ext.create('Ext.grid.feature.Grouping',
	    {//group by
	        groupHeaderTpl: '{name}'
	    })];
	    

		config.title ='Teams';//
		config.collapsible= true;
		
		config.height=350;
		config.width="100%";//
		
		config.listeners=
		{/*
			selectionchange: function(sm, selectedRecord) 
    		{
				if (!selectedRecord.length) {return;}
				
				if(selectedRecord.length ==1)
				{
					

     				 var row_buttons=['btn_team_delete','btn_team_email','btn_team_users','btn_team_roster','btn_team_division','btn_team_managers'];
     				 var btn_id;
     				 for(i in row_buttons)
     				 {//toggle the disabled property of each
						 btn_id=row_buttons[i];
						 if(Ext.getCmp(btn_id)) Ext.getCmp(btn_id).setDisabled(false);			 
     				 }		 
	             //   config.old_team_name=selectedRecord[0].get('team_name');// depreciated. also config does not exist in this scope/context

		    	
				}
					
		    	if(selectedRecord.length >1)
				{	
					var row_buttons=['btn_team_email','btn_team_users','btn_team_roster','btn_team_division','btn_team_managers' ];
     				 var btn_id;
     				 for(i in row_buttons)
     				 {//toggle the disabled property of each
						 btn_id=row_buttons[i];
						 if(Ext.getCmp(btn_id)) Ext.getCmp(btn_id).setDisabled(true);			 
     				 }		 

				}
				
		    },*/
    		edit: function(e)
    		{
    			var name=e.record.data['team_name'];
    			var id = e.record.data['team_id'];

     			 if(!name || name==this.team_name || name.split(' ').join('')=='') 
     			 {
     	 			 e.record.reject();//opposite of commit
     				 return;
				 }
				 
     			 var post="team_id="+id+"&team_name="+escape(name);
     			 var url="index.php/teams/post_team_name/"+App.TOKEN;
     			 var callback={scope:this,success:function(o)
     			 {
 
     	 		 		e.record.commit();
					 				 
     			 },failure:App.error.xhr};
	 			 YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
    		}		
		};

	    config.columns=
	    [
        	{text     : 'Team Name',flex:1,sortable : true,dataIndex: 'team_name',editor: { allowBlank: false }}		    
			,{flex : 1,dataIndex : 'division_name',text: 'Division'}
          //  ,{dataIndex : 'manager_name',width:150,text: 'Manager'}
           // ,{dataIndex : 'manager_phones',width:150,text: 'Phones'}
            //,{dataIndex : 'manager_email',width:250,text: 'Email'}
			,{dataIndex : 'roster_count',width:80,text: 'Players'}
            ,{dataIndex : 'user_count',width:80,text: 'Users'}
		];
		if(typeof config.dockedItems=='undefined')
		{
			config.dockedItems=new Array();
		}
		config.dockedItems.push(
		{dock: 'top',xtype: 'toolbar',//tbar
	        items:
	        [
	        	//filters go here if any
	        	{id:'btn_season_filter',text:"Select a Season to view Teams",menu:[]}
	        ]
		});
		var bbar=[];
     	if(config.bbar){bbar=config.bbar};
     	config.bbar=null;
		var new_bbar=
		[
	
			{tooltip:'Create New Team',
			iconCls :'fugue_users--plus',
			id:'btn_team_add',
			scope:this,
			handler:function()
	        {	
			
				var f=Ext.create('Spectrum.forms.teams.create',{grid_id:this.id,season_id:this.season_id});
				var w=Ext.create('Spectrum.windows.teams.create',{items:f});
				w.show();
				w.on('hide',function()
				{
					this.refresh();
				},this);
	        }}
			,{xtype:'hidden',id:'_grid_teams_internal_id_',value:id}
			,{xtype:'hidden',id:'_grid_teams_season_id_',value:-1}
			,'-'
			,"Double click a row to edit"
			,'->'
			,bbar//buttons given to create, from a controller 
			,'-'
			
			,{
				tooltip:'Assign Selected Team(s) to Season',
 
				iconCls:'seasons_add',
				scope:this,
				id:'btn_team_season',
				handler:function()
				{
					var rowsSelected = this.getSelectionModel().getSelection();
        			if(rowsSelected.length==0)  return;    
        			  
        			
        			var row,team_ids_array=new Array();
        			for(i in rowsSelected)if(rowsSelected[i] && typeof(rowsSelected[i].get)=='function' )
        			{
	 
						row=rowsSelected[i];
						team_ids_array.push(row.get('team_id'));
						
        			}
        			 
        			
     				var window_id = 'season_team_form_window'
     				var f = Ext.create('Spectrum.forms.teams.season',
     				{	
     					/*team_id:team_id,*/
     					window_id:window_id,
     					team_ids_array:team_ids_array
     				});
					//load this team data into the form, then put it in the window
					
					f.loadRecord(row);//doesnt matter which row, this is just to get season id in there mostly
					
					var w = Ext.create('Spectrum.windows.teams.season',{items:f,id:window_id});
					w.on('hide',function(){this.refresh();},this);//refresh grid on hide
     			
					w.show();
     				
				}
			}
			,{
				tooltip:'Remove Selected Team(s) from Season',
				iconCls :'seasons_minus',
				scope:this,
				handler:function()
		        {	
	        		//todo
	        		if(!this.season_id||this.season_id<0){return;}
	        		 var rowsSelected = this.getSelectionModel().getSelection();
        			if(rowsSelected.length==0)  {return; }   
        			  var msg='This may affect all published schedules and standings.  Remove anyway?';
        			 Ext.MessageBox.confirm('Remove from season?', msg, function(btn_id)
		 			 {
		 				 if(btn_id!='yes')return;
		 				 
        				var row,team_ids_array=new Array();
        				for(i=0;i<rowsSelected.length;i++)// in rowsSelected)//if(rowsSelected[i] && typeof(rowsSelected[i].get)=='function')
        				{
							team_ids_array.push(rowsSelected[i].get('team_id'));
        				}
	        			var post="team_ids="+YAHOO.lang.JSON.stringify(team_ids_array)+"&season_id="+this.season_id;
	  
	        			var url='index.php/teams/post_remove_teams_season/'+App.TOKEN;
	        			
	        			YAHOO.util.Connect.asyncRequest('POST',url,
	        			{
							scope:this
							,failure:App.error.xhr
							,success:function(o)
							{
								try
								{
									var success=YAHOO.lang.JSON.parse(o.responseText);
									this.refresh();
								}
								catch(e){App.error.xhr(o);}//catches php ,database,permissions, other errors
							}
	        			},post); 
		 				 
					 },this);
		        }
		    }
			,{
				tooltip:'Move the team to a new division',
				iconCls:'fugue_category--pencil',
				scope:this,
				id:'btn_team_division',
				handler:function()
				{
					//var grid_id=Ext.getCmp('_grid_teams_internal_id_').getValue();
    				//var grid=Ext.getCmp(grid_id);
		            var rowsSelected = this.getSelectionModel().getSelection();
        			if(rowsSelected.length==0)  return;    
        			var row=rowsSelected[0];
        			
        			var season_id=row.get('season_id');
					if(!season_id)
					{
						Ext.MessageBox.alert('Cannot swap the teams existing division.',
							'Division assignments are set up through the Seasons screen');
						return;
					}
        			//now check if team is playing any games for THIS season, if so cannot move
     				var team_id = row.get('team_id');
 
						
					
					var division_id = row.get('division_id');
					//.log(division_id);
					var f = Ext.create('Spectrum.forms.teams.division',{season_id:season_id,division_id:division_id});
					//load this team data into the form, then put it in the window
					f.loadRecord(row);
					var w = Ext.create('Spectrum.windows.teams.division',{items:f});
					
					w.show();
					w.on('hide',function(){this.refresh();},this);//refresh grid on hide
						
     				//}},post);
				}
			}
			,{
				tooltip:'Send Welcome Email to Selected Team(s)'
				,iconCls :'fugue_mail-send'
				//,disabled:true
				,scope:this
				,id:'btn_team_email',
				handler:function()
		        {
		             //var grid_id=Ext.getCmp('_grid_teams_internal_id_').getValue();
    				 //var grid=Ext.getCmp(grid_id);
		             var rowsSelected = this.getSelectionModel().getSelection();
        			 if(rowsSelected.length==0)  return;    
     				 var post="team_id="+rowsSelected[0].get('team_id');
     				 var url="index.php/teams/send_welcome_email/"+App.TOKEN;
     				 var callback={scope:this,success:function(o)
     				 {
						 Ext.MessageBox.alert('Result',o.responseText);
     				 }};
					 
	 				 YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
				}
			}	
			,{
				tooltip:'View Selected Team\'s Contacts'
				,iconCls :'fugue_card-address'
				//,disabled:true
				,scope:this
				,id:'btn_team_managers'
				,handler:function()
				{
					var rowsSelected = this.getSelectionModel().getSelection();
        			if(rowsSelected.length==0)  return;    
        			var row=rowsSelected[0];
     				var team_id = row.get('team_id');
     				
					var post='team_id='+team_id;
	 
					var f = Ext.create('Spectrum.forms.teams_managers',{team_id:team_id});
					
					var win = Ext.create('Spectrum.windows.teams_managers',{items:f});

					win.setTitle(row.get('team_name'));
					win.show();
				}
			}
			,'-'
			,{
				tooltip:'Modify Selected Team',
				iconCls :'seasons_edit',
				//iconCls :'pencil',
				scope:this,
				//hidden:true,
				handler:function()
		        {	
	        		//todo
	        		var rowsSelected = this.getSelectionModel().getSelection();
		            if(rowsSelected.length==0){return;}
		            var rec=rowsSelected[0];
		             
		            var form=Ext.create('Spectrum.forms.teams.edit',{});
 
		            form.loadRecord(rec);
		             
		            var win=Ext.create('Spectrum.windows.teams.edit',{items:form});
		             
		            win.on('hide',this.refresh,this);
		            win.show();
		            //Ext.MessageBox.prompt('Enter new Team Name')
		        }
		    }

			,{
				tooltip:'Remove Selected Team(s)',
				iconCls :'fugue_minus-button',
				//disabled:true,
				id:'btn_team_delete',
				scope:this,
				handler:function()
		        {		
		             
		            var rowsSelected = this.getSelectionModel().getSelection();
		            if(rowsSelected.length==0){return;}
        			if(rowsSelected.length==1)   
					{
						
				      		
        			 
						var team_id  =rowsSelected[0].get('team_id');
						var team_name=rowsSelected[0].get('team_name');
						  
        				 var msg="Are you sure you want to delete team \""+team_name+"\" ?";
        				 
		 				 Ext.MessageBox.confirm('Delete?', msg, function(btn_id)
		 				 {
		 					 if(btn_id!='yes')return;
     						 var post="team_id="+team_id;
     						 var url="index.php/teams/post_delete/"+App.TOKEN;
     						 var callback={failure:App.error.xhr,scope:this,success:function(o)
     						 {
     	 						 var r=o.responseText;
	  
			 					this.store.remove(rowsSelected[0]);
 							 
     						 }};
			 
	 						 YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
	 					 },this);
	 					 
	 				 
					}
					else
					{
						//multiple teams are selected
						
        				 var msg="Are you sure you want to delete "+rowsSelected.length+" teams?  This cannot be reversed.  Note that "
        			 			+"Spectrum will not allow teams that have active games in the current season to be deleted";
		 				 Ext.MessageBox.confirm('Delete?', msg, function(btn_id)
		 				 {
		 				 	 if(btn_id !='yes' && btn_id !='ok'){return;}
		 			 		 var team_ids_array=[];
							 for(i=0;i<rowsSelected.length;i++)// in rowsSelected)//if(rowsSelected[i] && typeof(rowsSelected[i].get)=='function')
        					 {
								team_ids_array.push(rowsSelected[i].get('team_id'));
        					 }
							   
							 var post="team_id_array="+YAHOO.lang.JSON.stringify(team_ids_array);	        			 
 
							 var url="index.php/teams/post_delete_multiple/"+App.TOKEN;
     						// var callback=
		 
	 						 YAHOO.util.Connect.asyncRequest('POST',url,
	 						 {
	 						 	 failure:App.error.xhr
	 						 	 ,scope:this
	 						 	 ,success:function(o)
     							 {
     	 							 var r=o.responseText;
									  
			 						 this.refresh();
     							 }
     						 },post);
		 				 },this);

					}
				}
			}
		]
		config.dockedItems.push({dock: 'bottom',xtype: 'toolbar',items: new_bbar});
 
     	this.callParent(arguments); 
     	this.buildSeasonFilter();
	}
	,buildSeasonFilter:function()
	{
     	//now make the season button menu
     	var season_url='index.php/season/json_active_league_seasons/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('GET',season_url,{scope:this,success:function(o)
		{
			var name;
            var id;
            var seasons=YAHOO.lang.JSON.parse(o.responseText).root;
	
			var seasons_filter=new Array();	
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;

				this.season_id=id;
				this.refresh();
        		Ext.getCmp('btn_season_filter').setText(name);
			};
			//one item for no season
			seasons_filter.push({text:'Unassigned',value:-1,handler:itemClick,scope:this,iconCls:'fugue_na'});
			var icon,foundActive=false;				
			for(i in seasons) if(seasons[i]['season_id'])
			{
 
				name='<b>Season:</b> '+seasons[i]['season_name']+" : "+seasons[i]['display_start']+" - "+seasons[i]['display_end'];
				id  =seasons[i]['season_id'];
				icon=seasons[i]['isactive_icon'];
				
				if(seasons[i]['isactive']=='t'||seasons[i]['isactive']=='true'||seasons[i]['isactive']===true)
				{
 
					foundActive={text:name,value:id};
				}
 
        		seasons_filter.push({text:name,value:id,handler:itemClick,scope:this,iconCls:icon});
			}
			Ext.getCmp('btn_season_filter').menu=Ext.create('Spectrum.btn_menu',{items:seasons_filter});
			if(foundActive)//select one of them by default 
			{
				//itemClick(foundActive,null);//fireEvent simulated ,and its scope independent
				this.season_id=foundActive.value;
				this.refresh();
				
        		Ext.getCmp('btn_season_filter').setText(foundActive.text);
			}
		}});
		
		 	
	}
	
	
});

}


