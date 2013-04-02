

var teamModel = 'Team';

if(!App.dom.definedExt(teamModel)){
Ext.define(teamModel, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'team_name',      type: 'string'},
	   {name: 'division_name',      type: 'string'},
	   {name: 'division_id',  type: 'int'},
	   {name: 'parent_division_id',  type: 'string'},
	   {name: 'parent_division_name',  type: 'string'},
	   {name: 'org_id',  type: 'int'},
	   {name: 'team_id',  type: 'int'},
       {name: 'season_id',  type: 'int'},
	   {name: 'roster_count',  type: 'string'},
	   {name: 'manager_name',  type: 'string'},
	   {name: 'manager_phones',  type: 'string'},
	   {name: 'manager_user_id',  type: 'string'},
	   {name: 'manager_person_id',  type: 'string'},
	   {name: 'manager_email',  type: 'string'},
	   {name: 'user_count',  type: 'int'}
	]
	,
    proxy: 
    {   
        type: 'rest',url: 'index.php/teams/json_season_teams/'+App.TOKEN,
        reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
        extraParams:{season_id:-1}
    }  
});}