
var SchedSaved='SchedSaveSession';
if(!App.dom.definedExt(SchedSaved)){
Ext.define(SchedSaved, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
       {name: 'schedule_name',  type: 'string' },
       {name: 'created_name',  type: 'string' },
       {name: 'created_by',  type: 'int' },
       {name: 'created_on',  type: 'string' },
       {name: 'modified_on',  type: 'string' },
       {name: 'user_memo',  type: 'string' },
       {name: 'season_name',  type: 'string' },
       {name: 'season_id',  type: 'int' },
       {name: 'session_id',  type: 'int' }
	]
});}

  