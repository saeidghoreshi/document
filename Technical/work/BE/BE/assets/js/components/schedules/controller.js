var c_schedules = function(){this.construct()};
c_schedules.prototype = 
{
	construct:function()
    {
 
        Ext.QuickTips.init();
    },

	schedules:
	{
		grid_id:'schedules_grid_manage',
		
		get:function()
		{
			//reload grid data
			Ext.getCmp(o_schedules.schedules.grid_id).refresh();
			
		},
		
		hide:function()
		{
 
			if(Ext.getCmp(o_schedules.schedules.grid_id))
				Ext.getCmp(o_schedules.schedules.grid_id).collapse();
 
		},
		
		show:function()
		{ 
			if(Ext.getCmp(o_schedules.schedules.grid_id))
				Ext.getCmp(o_schedules.schedules.grid_id).expand();
 
		},
		grid:function()
		{		
			
			var buttons=
			[				
				{tooltip:'View Games',disabled:false,id:'btn_sch_games',iconCls:'chart_organisation',handler:function()
	                {	              
	                	var rowsSelected = Ext.getCmp(o_schedules.schedules.grid_id).getSelectionModel().getSelection();
        				if(rowsSelected.length==0)
        				{  
        					//alert("error not  selected");
        					return;    
						}
						//pass data to games object
						o_schedules.games.schedule_id  =rowsSelected[0].get('schedule_id') ;
						o_schedules.games.season_id    =rowsSelected[0].get('season_id') ;
						o_schedules.games.schedule_name=rowsSelected[0].get('schedule_name') ;

	                	o_schedules.schedules.hide();
	                	//get games data
	                	o_schedules.games.get();
	                	o_schedules.games.show();//show comes after load!!
	                	
					}
				}
			];
			var g=Ext.create('Spectrum.grids.schedules',
			{
				renderTo:'dt-schedulemanage-grid',
				id:o_schedules.schedules.grid_id,
				height:App.MAX_GRID_HEIGHT,
				bbar:buttons
			});

			g.on('expand',function(o)
			{
				//.log('sch expand start');
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
				//.log('sch expand end');
				o_schedules.games.hide();
			});
			
			g.doLayout();
		}
	},

	games:
	{
		grid_id:'sch_grid_games',
		schedule_id:-1,
		season_id:-1,
		schedule_name:'',
		get:function()
		{
			var g=Ext.getCmp(o_schedules.games.grid_id);//.set_schedule_id(o_schedules.games.schedule_id);
			
			//pass in the new data
			g.schedule_id=o_schedules.games.schedule_id;//pass in the parameters
			g.season_id=o_schedules.games.season_id;
			//update title
			g.setTitle(o_schedules.games.schedule_name);
			//tell grid to refresh itself - loads data
			g.refresh();						
		},
		
		hide:function()
		{
 			//.log('games hide start');
			Ext.get('ctr-games-grid').addCls('dghidden'); 
			if(Ext.getCmp(o_schedules.games.grid_id))
				Ext.getCmp(o_schedules.games.grid_id).hide();
				
			
 			//.log('games hide end');
		},
		
		show:function()
		{
			
 			//.log('games show start');
			if(Ext.getCmp(o_schedules.games.grid_id))
			{
				Ext.getCmp(o_schedules.games.grid_id).show();
				Ext.getCmp(o_schedules.games.grid_id).expand();
			}

			Ext.get('ctr-games-grid').removeCls('dghidden'); 
 			//.log('games show end');
    		
		},
		
		grid:function()
		{
			//create grid with minimal input
			var grid=Ext.create('Spectrum.grids.games',
			{
				schedule_id:o_schedules.games.schedule_id,
				season_id:o_schedules.games.season_id,
				id:o_schedules.games.grid_id,
				height:300,
				renderTo:'dt-games-grid'
			});

			//custom event added
			grid.on('collapse', function()
    		{	
				//.log('games collapse start');
    			o_schedules.games.hide();
    			o_schedules.schedules.show();
				//.log('games collapse end');
    		});
    		grid.on('expand',function(o)
			{
				//.log('games expand start');
				this.setHeight(App.MAX_GRID_HEIGHT);
				this.doLayout();
				//.log('ganes expand end');
			});
		}
		
		
	}
	
}


var o_schedules = new c_schedules();

o_schedules.schedules.grid();//make grid
//o_schedules.schedules.get();
o_schedules.games.grid();
