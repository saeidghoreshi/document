
var dv_model_id='Currency';
if(!App.dom.definedExt(dv_model_id)){
Ext.define(dv_model_id, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'type_id',        type: 'int'},
	   {name: 'type_code',  	type: 'string' },
	   {name: 'currency_abbrev',type: 'string'},
	   {name: 'type_descr',     type: 'string'},
	   {name: 'owner_entity_id',type: 'int'},
	   {name: 'entity_id',		type: 'int'},
	   {name: 'html_character', type: 'string'},
	   {name: 'icon',           type: 'string'}
	   	
	]
});}



 