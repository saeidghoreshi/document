var c_standings= function(){this.construct();};
c_standings.prototype=
{
	construct:function()
	{
		Ext.QuickTips.init(); 
	},	
	
	standings:
	{
		grid_id:'grid_id_standings',
		get:function()
		{
			
			
		},
		grid:function()
		{
			//application_view_list
			var buttons=
			[
				{xtype:'button',iconCls:'chart_bar',tooltip:'Calculate Current Standings',handler:function()
				{
					var grid=Ext.getCmp(o_standings.standings.grid_id);
					var rows=grid.getSelectionModel().getSelection();
					if(!rows.length){return;}
					o_standings.standings.hide();
					
					o_standings.calculated.rank_type_id=rows[0].get('rank_type_id');
					o_standings.calculated.season_id   =grid.season_id;
					o_standings.calculated.get();
					o_standings.calculated.show();
					
				}}
				,'-'
				,{xtype:'button',iconCls:'application_view_list',tooltip:'Statistics Used',handler:function()
				{
					var grid=Ext.getCmp(o_standings.standings.grid_id);
					var rows=grid.getSelectionModel().getSelection();
					if(!rows.length){return;}
					
					o_standings.standings.hide();
					
					o_standings.statistics.rank_type_id=rows[0].get('rank_type_id');
					o_standings.statistics.get();
					o_standings.statistics.show();
 
				}
						
				}
				,{xtype:'button',iconCls:'layers',tooltip:'Division and Wildcard Options',handler:function()
				{
					var grid=Ext.getCmp(o_standings.standings.grid_id);
					var rows=grid.getSelectionModel().getSelection();
					if(!rows.length){return;}
					
					o_standings.standings.hide();
					var div=rows[0].get('rank_type_id');
					//.log('got from row: ',div);
					o_standings.divisions.rank_type_id=div;
					o_standings.divisions.season_id   =grid.season_id;
					o_standings.divisions.get();
					o_standings.divisions.show();
				}	}
			];
			
			var g=Ext.create('Spectrum.grids.standings',{renderTo:'grid-standings',id:o_standings.standings.grid_id
					,bbar:buttons});
			
			g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
				o_standings.statistics.hide();
				o_standings.divisions.hide();
				o_standings.calculated.hide();
			});
		},
		show:function()
		{
			Ext.getCmp(o_standings.standings.grid_id).expand();
		},
		hide:function()
		{
			Ext.getCmp(o_standings.standings.grid_id).collapse();
		}
	},
	
	statistics:
	{
		grid_id:'stats_grid',
		rank_type_id:null,
		get:function()
		{
			var gr=Ext.getCmp(o_standings.statistics.grid_id);
			gr.rank_type_id=o_standings.statistics.rank_type_id;
			gr.refresh();			
		},
		grid:function()
		{
			
			var g=Ext.create('Spectrum.grids.statistics',{renderTo:'grid-statistics',id:o_standings.statistics.grid_id});
			g.on('collapse',function()
			{
				//o_standings.statistics.hide();
				o_standings.standings.show();
			});
			g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});
		},
		show:function()
		{
			//.
			if(Ext.getCmp(o_standings.statistics.grid_id))
			{
				Ext.getCmp(o_standings.statistics.grid_id).expand();
				Ext.getCmp(o_standings.statistics.grid_id).show();
			}
			Ext.get('ctr-grid-statistics').removeCls('dghidden');
			 
		},
		hide:function()
		{
			Ext.get('ctr-grid-statistics').addCls('dghidden'); 
			if(Ext.getCmp(o_standings.statistics.grid_id))
				Ext.getCmp(o_standings.statistics.grid_id).hide();
		}
		
	},
	
	
	divisions:
	{
		left_grid_id:'wildcard_games_grid',
		right_grid_id:'wildcard_teams_grid',
		rank_type_id:null,
		season_id:null,
		get:function()
		{
			//pass in new rank type ids
			Ext.getCmp(o_standings.divisions.left_grid_id ).rank_type_id=o_standings.divisions.rank_type_id;
			Ext.getCmp(o_standings.divisions.left_grid_id ).season_id   =o_standings.divisions.season_id
			;
			Ext.getCmp(o_standings.divisions.right_grid_id).rank_type_id=o_standings.divisions.rank_type_id;
			Ext.getCmp(o_standings.divisions.right_grid_id).season_id   =o_standings.divisions.season_id;
			//refresh data based on this
			Ext.getCmp(o_standings.divisions.left_grid_id ).refresh();
			Ext.getCmp(o_standings.divisions.right_grid_id).refresh();
		},
		grid:function()
		{ 
			Ext.create('Spectrum.grids.wildcard_games',{id:o_standings.divisions.left_grid_id
			,renderTo:'grid-game-divs'
			,rank_type_id:o_standings.divisions.rank_type_id
			,season_id:   o_standings.divisions.season_id});
			 
			Ext.create('Spectrum.grids.wildcard_teams',{id:o_standings.divisions.right_grid_id
			,renderTo:'grid-team-divs'
			,rank_type_id:o_standings.divisions.rank_type_id
			,season_id:   o_standings.divisions.season_id});
			 
		},
		show:function()
		{ 
			Ext.get('ctr-grid-wildcard').removeCls('dghidden');
			Ext.getCmp(o_standings.divisions.left_grid_id).show();
			Ext.getCmp(o_standings.divisions.right_grid_id).show();
		},
		hide:function()
		{
			Ext.get('ctr-grid-wildcard').addCls('dghidden'); 
			Ext.getCmp(o_standings.divisions.left_grid_id).hide();
			
			Ext.getCmp(o_standings.divisions.right_grid_id).hide();
		}
	}

	
	,calculated:
	{
		grid_id:'_calc_standings_team_grid',
		season_id:'',
		rank_type_id:'',
		get:function()
		{
			var g=Ext.getCmp(o_standings.calculated.grid_id);
			//update ids
			g.season_id   =o_standings.calculated.season_id;
			Ext.getCmp(o_standings.calculated.grid_id).buildDivisionsMenu();//need to remake this based on new season id
			g.rank_type_id=o_standings.calculated.rank_type_id;
			//then refresh data
			g.refresh();
		}
		,grid:function()
		{
			var g=Ext.create('Spectrum.grids.calculated',{renderTo:'grid-calculated',id:o_standings.calculated.grid_id});
			g.on('collapse',function()
			{
				o_standings.calculated.hide();
				o_standings.standings.show();
			});
			g.on('expand',function()
			{
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
			});
		},
		
		show:function()
		{
			//unhide and expand if possible
			if(Ext.getCmp(o_standings.calculated.grid_id))
			{
				
				Ext.getCmp(o_standings.calculated.grid_id).expand();
				Ext.getCmp(o_standings.calculated.grid_id).show();
			}
				
				
			Ext.get('ctr-grid-calculated').removeCls('dghidden');
		},
		hide:function()
		{
			Ext.get('ctr-grid-calculated').addCls('dghidden'); 
			Ext.getCmp(o_standings.calculated.grid_id).hide();
		}
		
		
	}
	

}


o_standings=new c_standings();
 
o_standings.standings.grid();
 
o_standings.statistics.grid();
 
o_standings.divisions.grid();
 
o_standings.calculated.grid();
 