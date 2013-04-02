var timeslotModel='SchTimeslot';


if(!App.dom.definedExt(timeslotModel)){
Ext.define(timeslotModel, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'dateset_pk',    type: 'string'},
	   {name: 'hexcol',        type: 'string'},
	   {name: 'set_name',      type: 'string'},
	   {name: 'hexcol',        type: 'string'},
	   {name: 'venue_count',   type: 'string'},
	   {name: 'date_count',          type: 'int'},
	   {name: 'rules_array',          type: 'string'},
	   {name: 'date_array',          type: 'string'},
	   {name: 'venue_array',          type: 'string'},
	   {name: 'est_games',          type: 'int'},
	   {name: 'start_time',           type: 'date',dateFormat:'g:i A' },//,dateFormat: 'H:i' 
	   {name: 'end_time',             type: 'date',dateFormat:'g:i A' }
 
	]
});}