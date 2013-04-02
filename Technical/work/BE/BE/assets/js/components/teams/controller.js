
var c_teams= function(){this.construct();};
c_teams.prototype=
{
	construct:function()
	{

	 	Ext.QuickTips.init();                                                                              
	},  

	teams:
	{
		grid_id:'manage_teams_gridid',
		get:function()
		{
			Ext.getCmp(o_teams.teams.grid_id).refresh()
		},
		grid:function()
		{
			var bbar=
			[

				{
					tooltip:'Manage Selected Team\'s Roster'
					,id:'btn_team_roster'
					,iconCls:'fugue_document-table'
					,handler:function()
		            {
		                var g=Ext.getCmp(o_teams.teams.grid_id);
		                var rowsSelected = g.getSelectionModel().getSelection();
        				if(rowsSelected.length==0)  return;  
		                
						
						
						if(!g.season_id || g.season_id==-1)
						{
							Ext.MessageBox.alert('Roster does not exist ','Select a season first.');
							return;
						}
		                //pass ids to my rosters controller
		                o_teams.roster.season_id=g.season_id;
                		o_teams.roster.org_id =rowsSelected[0].get('org_id');
                		o_teams.roster.team_id=rowsSelected[0].get('team_id');
                		o_teams.roster.team_name=rowsSelected[0].get('team_name');
                		
	                   
	                   //get will refresh the data in the rosters grid
	                   o_teams.roster.get();
	                    
	                    
                		o_teams.teams.hide();
                		o_teams.roster.show();
						//get_roster
					}
				}
				,{
					tooltip:'Manage Selected Team\'s Users'
					,iconCls :'fugue_user--pencil'
					,id:'btn_team_users'
					,handler:function()
		            {
		                var rowsSelected = Ext.getCmp(o_teams.teams.grid_id).getSelectionModel().getSelection();
		                //var rowsSelected = grid.getSelectionModel().getSelection();
        				if(rowsSelected.length==0)  return;  
                		//o_teams.users.team_id=rowsSelected[0].get('team_id');
                		o_teams.users.org_id   =rowsSelected[0].get('org_id');
						o_teams.users.team_name=rowsSelected[0].get('team_name');
                		o_teams.users.get();
                		o_teams.teams.hide();
                		o_teams.users.show();

					}
				}	
				
			];
			var g=Ext.create('Spectrum.grids.teams',{renderTo: 'dt-teams-grid',id:o_teams.teams.grid_id,bbar:bbar});
			
			g.on('expand',function()
			{
				o_teams.users.hide();
				o_teams.roster.hide();

				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});

		},
		hide:function()
		{
			Ext.getCmp(o_teams.teams.grid_id).collapse();
		},
		
		show:function()
		{
			Ext.getCmp(o_teams.teams.grid_id).expand();
			
		}
	},
	
	
	roster:
	{
		org_id:-1,//since rosters uses both org id and team id
		team_id:-1,
		season_id:-1,
		grid_id:'manage_team_roster_gridid',
		team_name:'',
		get:function()
		{
			//this is an imported grid from ryans library
			var g=Ext.getCmp(o_teams.roster.grid_id);
			
			
			//this grid uses a proxy, no custom refresh like in sams components
			//so just change the params and reload
			
			//SB: roster grid is broken, will not accept deny or create people with out the org id of the team, so I have to pass it in here

			g.org_id = o_teams.roster.org_id;

			if(o_teams.roster.team_name)
				g.setTitle('Roster for '+o_teams.roster.team_name);
			
			g.team_id=o_teams.roster.team_id;
			g.season_id=o_teams.roster.season_id;
			g.store.proxy.extraParams.team_id   = o_teams.roster.team_id;
			g.store.proxy.extraParams.season_id = o_teams.roster.season_id;
			g.store.load();
		},
		grid:function()
		{
			var config=
	        {
        		org_id:null//i forget what this is for
	            ,renderTo        : 'dt-roster-grid'
	            ,title           : 'Roster'
	            
				,id:o_teams.roster.grid_id
				,season_id:o_teams.roster.season_id
	    		,team_id:o_teams.roster.team_id
	    		,collapsible:true
	        }
			//ryansObject.renderTo = '';//my render to acting as an argument that will be passed to Ext.create inside init
			if(Ext.getCmp(o_teams.roster.grid_id)) {Ext.getCmp(o_teams.roster.grid_id).destroy();}//in case grid does not take care of this which it should
			

        	var grid = Ext.create('Ext.spectrumgrids.rosterperson',config);
			
			grid.on('collapse',function()
			{
				o_teams.roster.hide();
				o_teams.teams.show();
			});
			grid.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});
 
		},
		hide:function()
		{
			Ext.get('ctr-roster-grid').addCls('dghidden');
			Ext.getCmp(o_teams.roster.grid_id).hide();	
		},
		
		show:function()
		{
			//if it exists , expand
			
			if(Ext.getCmp(o_teams.roster.grid_id))
			{
				Ext.getCmp(o_teams.roster.grid_id).expand();
				Ext.getCmp(o_teams.roster.grid_id).show();	
				
			}

			Ext.get('ctr-roster-grid').removeCls('dghidden'); 
			
		}		
		
	},
	
	users:
	{
		team_name:'',
		team_id:-1,
		org_id:-1,
		grid_id:'team_user_roles_gridid',
		get:function()
		{
			var g = Ext.getCmp(o_teams.users.grid_id);
			g.org_id =o_teams.users.org_id;
			g.refresh();
			if(o_teams.users.team_name)
				g.setTitle('Assigned Users for '+o_teams.users.team_name);
		},
		grid:function()
		{ 
			var g=Ext.create('Spectrum.grids.org_users',{renderTo:'dt-userroles-grid',id:o_teams.users.grid_id});
			
			g.on('collapse',function()
			{
				o_teams.users.hide();
				o_teams.teams.show();
			});
			g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});
		},
		hide:function()
		{ 
			
			Ext.get('ctr-userroles-grid').addCls('dghidden'); 
			Ext.getCmp(o_teams.users.grid_id).hide(); 

		},
		
		show:function()
		{
			if(Ext.getCmp(o_teams.users.grid_id))
				Ext.getCmp(o_teams.users.grid_id).expand();
			Ext.get('ctr-userroles-grid').removeCls('dghidden'); 
			Ext.getCmp(o_teams.users.grid_id).show(); 
			
		}		
		
	}
	
    
};
var o_teams=new c_teams();



o_teams.teams.grid();

o_teams.users.grid();
o_teams.roster.grid();

//make sure they start hidden
Ext.getCmp(o_teams.users.grid_id).hide(); 
Ext.getCmp(o_teams.roster.grid_id).hide(); 



