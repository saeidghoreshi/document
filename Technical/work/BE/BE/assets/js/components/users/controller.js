
var org_id=Ext.get('hidd_org_id').dom.value;
var org_type=Ext.get('hidd_org_type').dom.value;

var generator=Math.random();
//simply create the grid.
var grid=Ext.create('Spectrum.grids.org_users',
{
	renderTo:'dg_my_org_users'
	,collapsible:false
	,searchBar:true//default for this is false
	,org_id:org_id
	,org_type:org_type
	,generator:generator,id:'myorg_grid_'
});

//and make sure data will load

grid.getStore().load();