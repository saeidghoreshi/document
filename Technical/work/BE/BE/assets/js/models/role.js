var orgrolesModel = 'OrgRolesAllowed';//define model if it does not exist yet
if(!App.dom.definedExt(orgrolesModel)){
Ext.define(orgrolesModel, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'role_name',      type: 'string'},
	   {name: 'role_id',      type: 'string'}
	]
});
}