var c_game_results = function(){this.construct()};
c_game_results.prototype = 
{
	construct:function()
    {
    	Ext.QuickTips.init();
    	//grids created at bottom of file, after object is constructed
    },

	
	games:
	{
		grid_id:'dg_season_games_grid',
		/*get:function()
		{
			//get handled by filter button
		},*/
		grid:function()
		{
			var g=Ext.create('Spectrum.grids.game_results',{renderTo:'dg-result-games',id:o_game_results.games.grid_id});
			g.on(  'selectionchange', function(sm, rows) 
    			{
    				if(!rows.length)return;
    				var row=rows[0];
    				var game_id=row.get('game_id');
    				
    				//.log(row);;
    				//.log('TODO: get submissions for game_id',game_id);
					o_game_results.results.game_id=game_id;
    				o_game_results.results.get();
    				//id:'lbl_validate_home_input'}
    				Ext.getCmp('lbl_validate_home_input').setValue(row.get('home_name'));
    				Ext.getCmp('lbl_validate_away_input').setValue(row.get('away_name'));
				}
			);
		}
		

		
		
	},
	
	
	
	results:
	{
		game_id:null,
		grid_id:'dg_results_grid',
		get:function()
		{
			var grid=Ext.getCmp(o_game_results.results.grid_id);
			grid.set_game_id(o_game_results.results.game_id);
			grid.refresh();
		},
		grid:function()
		{
			//.log('grade grid.results too..!!??');
			var rg=Ext.create('Spectrum.grids.validate_results',
				{renderTo:'dg-result-validate',id:o_game_results.results.grid_id});
			
		}
		/*
		,hide:function()
		{
			
			
		},
		show:function()
		{
			
			
		}*/
	}
	
}


var o_game_results= new c_game_results();
o_game_results.games.grid();
o_game_results.results.grid();