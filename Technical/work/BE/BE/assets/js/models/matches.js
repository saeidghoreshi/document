var model_id='WizardMatches';
if(!App.dom.definedExt(model_id)){
Ext.define(model_id, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
		{name: 'first_div_name',      type: 'string'},
		{name: 'match_pk',      type: 'string'},
		{name: 'f_total_teams',      type: 'string'},
		{name: 'second_div_name',      type: 'string'},
		{name: 's_total_teams',      type: 'string'},
	   {name: 'match_rounds',      type: 'string'},
	   {name: 'enforce_rounds',      type: 'string'},
	   {name: 'date_count',      type: 'string'},
	   {name: 'est_games',      type: 'string'},
	   {name: 'games_per_round',      type: 'string'},
	   {name: 'enforce_dates',      type: 'string'}  
	]
});}

