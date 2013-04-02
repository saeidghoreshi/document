var model_name = 'Schedules';
if(!App.dom.definedExt(model_name)){
Ext.define(model_name, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'schedule_name',      type: 'string'},
	   {name: 'season_name',      type: 'string'},
	   {name: 'is_published',      type: 'string'},
	   //{name: 'is_published_label',      type: 'string'},
	   {name: 'schedule_id',  type: 'int'},
	   {name: 'league_id',  type: 'int'},
	   {name: 'total_games',  type: 'int'},
	   {name: 'valid_count',  type: 'int'},//number of games with a valid score saved
	   {name: 'season_id',  type: 'int'}
	]
});}