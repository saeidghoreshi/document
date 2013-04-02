
var dv_model_id='Assoc';
if(!App.dom.definedExt(dv_model_id)){
Ext.define(dv_model_id, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'association_id',       type: 'int'},
	   {name: 'org_id',  		type: 'int' },
	   {name: 'entity_id',  		type: 'int' },
	   {name: 'parent_org_id',           type: 'int'},
	   {name: 'parent_assoc_id',           type: 'int'},
	   {name: 'user_count',           type: 'int'},
	   {name: 'league_count',           type: 'int'},
	   {name: 'team_count',           type: 'int'},
	   {name: 'player_count',           type: 'int'},
	   {name: 'tourn_count',           type: 'int'},
   	   {name: 'expiry_date',         type: 'date'/*, dateFormat: 'Y-m-d'*/},
       {name: 'association_name',  type: 'string' },
       {name: 'website',  type: 'string' },
       {name: 'address_street',  type: 'string' },
       {name: 'address_lat',  type: 'string' },
       {name: 'address_id',  type: 'string' },
       {name: 'address_lon',  type: 'string' },
       {name: 'address_city',  type: 'string' },
       {name: 'country_abbr',  type: 'string' },
       {name: 'address_country',  type: 'string' },
       {name: 'address_region',  type: 'string' },
       {name: 'region_abbr',  type: 'string' },
       {name: 'postal_value',  type: 'string' }
	]
});}



 