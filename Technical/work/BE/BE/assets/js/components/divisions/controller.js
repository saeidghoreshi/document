var c_divisions= function(){this.construct();};
c_divisions.prototype=
{

	construct:function()
	{
		Ext.QuickTips.init();//activate button tooltips
	},
	
	//all the fun stuff here happens in the drag drop events, and the create forms. both come from grid
	divs_left:
	{
		grid_id:'left_divs_grid',
		grid:function()
		{
			var g=Ext.create('Spectrum.grids.divisions',{id:o_divisions.divs_left.grid_id,renderTo:'dt-left-divisions',
					division_primary:true});
			g.refresh();
		}
		
	},
	
	
	divs_right:
	{
		grid_id:'right_divs_grid',
		grid:function()
		{
			var g=Ext.create('Spectrum.grids.divisions',{id:o_divisions.divs_right.grid_id,renderTo:'dt-right-divisions',
					division_primary:false,title:'Divisions from previous seasons'});	
			
			g.refresh();	
		}
	}

}



var o_divisions=new c_divisions();

o_divisions.divs_left.grid();
o_divisions.divs_right.grid();