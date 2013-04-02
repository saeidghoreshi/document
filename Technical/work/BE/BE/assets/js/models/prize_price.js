var price_model='PrizePrice';

if(!App.dom.definedExt(price_model)){
Ext.define(price_model, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
		{name: 'size_name',      type: 'string'},
		{name: 'size_abbr',      type: 'string'},
		{name: 'price',      type: 'string'},
		{name: 'is_active',      type: 'string'},
		{name: 'size_id',      type: 'int'}
		
	]
});}
