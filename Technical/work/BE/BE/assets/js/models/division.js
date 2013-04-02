//define the model for division data. used by division forms and grids

var dv_model_id='Division';
if(!App.dom.definedExt(dv_model_id)){
Ext.define(dv_model_id, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'division_name',       type: 'string'},
	   {name: 'only_teams',       type: 'string'},
	   {name: 'division_id',         type: 'int'},
	   {name: 'season_id',         type: 'int'},
	   {name: 'total_teams',         type: 'string'},
	   {name: 'sub_count',         type: 'string'},
	   {name: 'parent_division_id',  type: 'int' },
       {name: 'deposit_amount',  type: 'float' },
       {name: 'fees_amount',  type: 'float' }
	]
});}