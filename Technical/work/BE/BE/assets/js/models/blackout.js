
var dv_model_id='Blackout';
if(!App.dom.definedExt(dv_model_id)){
Ext.define(dv_model_id, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'bo_id',       type: 'int'},
	   {name: 'bo_type_id',  type: 'int' },
	   {name: 'season_id',           type: 'int'},
	   {name: 'bo_start_date',		 type: 'date', dateFormat: 'Y-m-d'},
   	   {name: 'bo_end_date',         type: 'date', dateFormat: 'Y-m-d'},
       {name: 'bo_type_name',  type: 'string' },
       {name: 'bo_user_desc',  type: 'string' }
	]
});}



 