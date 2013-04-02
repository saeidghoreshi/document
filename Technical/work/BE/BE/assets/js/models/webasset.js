var wmodelid='WebsiteAsset';
if(!App.dom.definedExt(wmodelid)){
Ext.define( wmodelid,
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'module_asset_id',      type: 'string'},
	   {name: 'org_id',      type: 'string'},
	   {name: 'module_id',      type: 'int'},
	   {name: 'display_order',      type: 'int'},
	   {name: 'url',      type: 'string'},
	   {name: 'text',  type: 'string'},
	   {name: 'file',  type: 'string'}

	]
});}