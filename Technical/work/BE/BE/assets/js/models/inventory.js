var inventory_model='Inventory';
if(!App.dom.definedExt(inventory_model)){
Ext.define(inventory_model, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
		{name: 'name',      type: 'string'},
		{name: 'quantity',      type: 'string'},
		{name: 'quantity_change',      type: 'int'},
		{name: 'inventory_id',      type: 'int'},
		{name: 'prize_id',      type: 'int'},
		//{name: 'warehouse_id',      type: 'int'},
		{name: 'size_id',      type: 'int'},
		{name: 'size_name',      type: 'string'},
		{name: 'size_abbr',      type: 'string'}
		//{name: 'prize_count',      type: 'string'},
	]
});}