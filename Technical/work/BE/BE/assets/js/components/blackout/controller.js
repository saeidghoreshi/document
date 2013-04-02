
o_blackouts=
{
	
	blackout:
	{
		
		grid:function()
		{
			var Blackout_grid = Ext.create('Ext.spectrumgrids.blackout',
			{
			    renderTo        : "leagues_blackout",
			    title           : 'Roster Blackout Dates',
			    collapsible     :false//it defaults to true.
			});        
			Blackout_grid.show();	
		}	
		
		
	}

}
//nothing to do in this controller.  just initialize the grid and we are done .  


o_blackouts.blackout.grid();


