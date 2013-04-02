var g_model_id='GameForResults';
if(!App.dom.definedExt(g_model_id)){
Ext.define(g_model_id, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
		{name: 'home_name',      type: 'string'},
		{name: 'home_id',      type: 'int'},
		{name: 'away_name',      type: 'string'},
		{name: 'away_id',      type: 'int'},
	   {name: 'game_id',      type: 'int'},
	   {name: 'game_date',      type: 'string'},
	   {name: 'start_time',      type: 'string'},
	   {name: 'end_time',      type: 'string'},
	   {name: 'venue_id',  type: 'int'},
	   {name: 'schedule_id',  type: 'int'},
	   {name: 'venue_name',  type: 'string'},
	   //IMPORTANT: if scores are type:int, 
	   //then it converts null and empty string to ZERO, which we do not want!!!
	   //INT is semi-enforced using maskRe:.... inside the editor
	   //as well as (int) cast on server side
	   {name: 'csv_status',      type: 'string'},
	   {name:'home_score',type:'string'},
	   {name:'away_score',type:'string'}
	  // {name: 'team_id',  type: 'int'}
	]
});}