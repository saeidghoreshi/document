var w_model='Warehouse';
if(!App.dom.definedExt(w_model)){
Ext.define(w_model, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
		{name: 'warehouse_name',      type: 'string'},
		{name: 'warehouse_id',      type: 'int'},
		{name: 'entity_org_id',      type: 'int'},
		{name: 'warehouse_desc',      type: 'string'},
		{name: 'is_default',      type: 'string'}

	]
});}