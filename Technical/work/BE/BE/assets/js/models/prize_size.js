var size_model='PrizeSize';
if(!App.dom.definedExt(size_model)){
Ext.define(size_model, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
		{name: 'size_name',      type: 'string'},
		{name: 'size_abbr',      type: 'string'},
		{name: 'size_id',      type: 'int'},
		{name: 'lu_size_id',      type: 'string'},
		{name: 'prize_id',      type: 'int'}//,
	]
});}
    