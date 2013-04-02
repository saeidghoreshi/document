var model_id='WizardRules';
if(!App.dom.definedExt(model_id)){
Ext.define(model_id, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
		{name: 'min',      type: 'string'},
		{name: 'max',      type: 'string'},
		{name: 'len',      type: 'string'},
		{name: 'warmup',      type: 'string'},
		{name: 'teardown',      type: 'string'},
	   {name: 'min_disabled',      type: 'string'},
	   {name: 'max_disabled',      type: 'string'},
	   {name: 'min_btw',      type: 'string'},
	   {name: 'max_btw',      type: 'string'},
	   {name: 'facility_lock',      type: 'string'},
	   {name: 'venue_distance',      type: 'string'}  
	]
});}

	 