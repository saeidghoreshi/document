
var dv_model_id='Domain';
if(!App.dom.definedExt(dv_model_id)){
Ext.define(dv_model_id, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'id',        type: 'int'},
	   {name: 'domain',  	type: 'string' }, 
	   {name: 'is_active',  	type: 'string' }, 
	   {name: 'league_count',  	type: 'int' }, 
	   {name: 'org_id',type: 'int'}
	   	
	]
});}



 