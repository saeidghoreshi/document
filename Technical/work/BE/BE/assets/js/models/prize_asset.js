var st_model_id='PrizeAsset';
if(!App.dom.definedExt(st_model_id)){
Ext.define( st_model_id,
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'filepath',      type: 'string'},
	   {name: 'thumb_filepath',      type: 'string'},
	   {name: 'asset_id',      type: 'int'},
	   {name: 'prize_id',      type: 'int'},
	   {name: 'description',  type: 'string'},
	   {name: 'created_on',  type: 'string'},
	   {name: 'is_default',  type: 'string'},
	   {name: 'is_default_display',  type: 'string'}

	]
});}