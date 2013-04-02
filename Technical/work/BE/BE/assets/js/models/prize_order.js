var poModel = 'PrizeOrder';
if(!App.dom.definedExt(poModel)){
Ext.define(poModel, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'created_by',      type: 'string'},
	   {name: 'created_name',      type: 'string'},
	   {name: 'created_on',  type: 'string'},
	   {name: 'org_name',  type: 'string'},
	   //{name: 'item_count',type:'string'},
	   //{name: 'est_total',  type: 'string'},
	   {name: 'order_status_id',  type: 'int'},
	   {name: 'status_name',  type: 'string'},
	   {name: 'order_desc',type:'string'},
	   {name: 'order_id',type:'int'},
	   {name: 'requested_date',type:'string'}
	]
});}