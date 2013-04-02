var cmodel = 'OrderItem';
if(!App.dom.definedExt(cmodel)){
Ext.define(cmodel, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'size_name',      type: 'string'},
	   {name: 'custom_group_by',      type: 'string'},
	   {name: 'qty',      type: 'string'},
	   {name: 'available',      type: 'string'},
	   {name: 'reserved',      type: 'string'},
	   {name: 'name',  type: 'string'},
	   {name: 'currency_type_id',type:'string'},
	   {name: 'currency_abbrev',type:'string'},
	   {name: 'price',  type: 'string'},
	   {name: 'order_id',  type: 'int'},
	   {name: 'size_id',  type: 'int'},
	   {name: 'prize_id',  type: 'int'},
	   {name: 'category_id',  type: 'int'},
	   {name: 'category_name',  type: 'string'},
	   {name: 'sku',  type: 'string'},
	   {name: 'upc',  type: 'string'},
	   {name: 'item_id',  type: 'int'}

	]
});}