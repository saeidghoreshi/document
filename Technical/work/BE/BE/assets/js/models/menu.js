var orgrolesModel = 'RoleMenu';//define model if it does not exist yet
if(!App.dom.definedExt(orgrolesModel)){
Ext.define(orgrolesModel, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'id',      type: 'string'},
	   {name: 'role_id',      type: 'string'},
	   {name: 'parent',      type: 'string'},
	   {name: 'menu_label',      type: 'string'},
	   {name: 'update_code',      type: 'string'},
	   {name: 'update_auth_id',      type: 'string'},
	   {name: 'view_code',      type: 'string'},
	   {name: 'view_auth_id',      type: 'string'}
	]
});
}

