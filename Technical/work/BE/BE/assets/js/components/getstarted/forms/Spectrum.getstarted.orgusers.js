//Spectrum.grids.org_users


var classname = "Spectrum.getstarted.orgusers";
if(!App.dom.definedExt(classname)){
Ext.define(classname,
{
	initComponent:function(config){},
	form:null,
	grid:null,
	//,url:'index.php/endeavor/post_org_address/'+App.TOKEN
	load:function()
	{
		YAHOO.util.Connect.asyncRequest('GET','index.php/permissions/json_active_org_type/'+App.TOKEN,
		{success:function(o)
		{
			var active_org=YAHOO.lang.JSON.parse(o.responseText);
			//.log(active_org);
			this.grid.org_type = active_org['org_type'];
			this.grid.org_id   = active_org['org_id'];
			//call the internal load function based on new values given above
			this.grid.refresh();

		},failure:App.error.xhr,scope:this})	
	},
	
	constructor:function(config)
	{
		this.grid = Ext.create('Spectrum.grids.org_users',{
			collapsible:false,
			renderTo:'grid-gs-orgusers',
			hideDates:true,
			rowEditable:false,//hide date columns
			width:355//,disabled:true
			,height:300 
		});
		
		
		//loaad active org into the grid, and load the records, if any
		this.load();
		
	}
	
});
	
}
App.GS.activeGrid[App.GS.slideEnum.USERS] = Ext.create(classname,{});
//.log('end of file gs orgusers');