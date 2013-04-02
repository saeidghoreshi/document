var cat_model='PrizeCategory';
if(!App.dom.definedExt(cat_model)){
Ext.define(cat_model, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
		{name: 'category_name',      type: 'string'},
		{name: 'category_id',      type: 'int'},
		{name: 'org_id',      type: 'int'},
		{name: 'category_desc',      type: 'string'},
		{name: 'prize_count',      type: 'string'},

	]
});}