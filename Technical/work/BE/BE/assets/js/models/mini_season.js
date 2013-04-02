var miniSeason = 'miniSeason';
if(!App.dom.definedExt(miniSeason)){
Ext.define(miniSeason, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'season_name',      type: 'string'},
	   {name: 'season_id',      type: 'string'},
	   {name: 'isactive_icon',  type: 'string'},
	   {name: 'isactive',  type: 'string'},
	   {name: 'effective_range_start',  type: 'string'},
	   {name: 'effective_range_end',  type: 'string'}
	]
});}