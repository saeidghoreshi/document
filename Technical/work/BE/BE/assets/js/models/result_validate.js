 var gr_model_id='GameValidate';
if(!App.dom.definedExt(gr_model_id))
{Ext.define(gr_model_id, 
{
	extend: 'Ext.data.Model',
	fields: 
	[  
		
	   {name: 'game_result_id',      type: 'int'},
		{name: 'home_name',      type: 'string'},
		{name: 'away_name',      type: 'string'},
		{name: 'display_home',      type: 'string'},
		{name: 'display_away',      type: 'string'},
		{name: 'display_header',      type: 'string'},
		{name: 'display_date',      type: 'string'},
		{name: 'form_date',      type: 'string'},
		{name: 'form_email',      type: 'string'},
		{name: 'form_name',      type: 'string'},
		{name: 'status',      type: 'string'},
		{name: 'id',      type: 'string'},
		{name: 'csv_status',      type: 'string'},
		{name:'home_score',type:'string'},
	   {name:'away_score',type:'string'}
	]
	
	
});}