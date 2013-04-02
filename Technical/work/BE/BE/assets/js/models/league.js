
var league_model_id='LeagueModel';
//this model may already exist, from league grid or something, but try and define it here anyway
if(!App.dom.definedExt(league_model_id)){ 
Ext.define( league_model_id,
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'league_name',      type: 'string'},
	   {name: 'league_users_count_image',      type: 'string'},
	   {name: 'league_id',      type: 'int'},
	   {name: 'team_count',      type: 'int'},
	   {name: 'league_users_count',      type: 'int'},
	   {name: 'player_count',      type: 'int'},
	   {name: 'org_id',      type: 'int'},
	   {name: 'org_logo',      type: 'int'},
	   {name: 'entity_id',      type: 'int'},
	   {name: 'type_id',  type: 'string'},
	   {name: 'type_name',  type: 'string'},
	   {name: 'domainname',  type: 'string'},
	   //dont make this an integer, or load record WILL NOT WORK with the combobox in the edit/create form
	   {name: 'websiteprefix',  type: 'string'},
	   {name: 'expiry_date',  type: 'string'},
	   {name: 'url',  type: 'string'}
	   
	   /*            if(config.fields==null)     config.fields       = ['league_id','league_name','org_id','entity_id','expiry_date','url',
            'type_name','type_id','address','league_users_count','league_users_count_image','org_logo'];*/
	]
});}

