var orgrolesModel = 'SysMethod';//define model if it does not exist yet
if(!App.dom.definedExt(orgrolesModel)){
Ext.define(orgrolesModel, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'controller_id',      type: 'string'},
	   {name: 'method_name',      type: 'string'},
	   {name: 'is_allowed',      type: 'string'},
	   {name: 'method_id',      type: 'string'}
	]
});
}
 