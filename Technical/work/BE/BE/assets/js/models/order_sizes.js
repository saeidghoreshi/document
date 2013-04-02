var OSmodel = 'OrderPrizeSizes';
if(!App.dom.definedExt(OSmodel)){
Ext.define(OSmodel, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'size_name',      type: 'string'},
	   {name: 'qty',      		type: 'int'},
	   {name: 'total_stock',    type: 'int'},
	   {name: 'size_abbr',  	type: 'string'},
	   //{name: 'currency_abbrev',type: 'string'},
	   //{name: 'currency_id',	type: 'string'},
	   {name: 'cur1_price',  	type: 'string'},
	   {name: 'cur1_total',  	type: 'string'},
	   {name: 'cur1_qty',  		type: 'int'},
	   
	   {name: 'cur2_price',  	type: 'string'},
	   {name: 'cur2_total',  	type: 'string'},
	   {name: 'cur2_qty',  		type: 'int'},
	   {name: 'cur3_price',  	type: 'string'},
	   {name: 'cur3_total',  	type: 'string'},
	   {name: 'cur3_qty',  		type: 'int'},
	   {name: 'order_id',  		type: 'int'},
	   {name: 'size_id',  		type: 'int'},
	   {name: 'prize_id',  		type: 'int'}

	]
});}