 var mclass = 'OrgUserRole';
 if(!App.dom.definedExt(mclass)){
Ext.define(mclass, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
//data for role and org
	   {name: 'role_name',      type: 'string'},

	   {name: 'effective_range_start', type: 'string'},//  type: 'date', dateFormat: 'Y/m/d'},
	   {name: 'effective_range_end',  type: 'string'},//    type: 'date', dateFormat: 'Y/m/d' },
	   {name: 'org_id',   type: 'int'},
	   {name: 'user_id',  type: 'int'},
	   {name: 'role_id',  type: 'int'},
	   
	   {name: 'assignment_id',  type: 'int'},
	   
	   //data for person
	   {name: 'person_fname',      type: 'string'},
	   {name: 'person_lname',      type: 'string'},
	   {name: 'person_gender',     type: 'string'},
	   {name: 'postal_value',      type: 'string'},
	   {name: 'login',      	   type: 'string'},
	   {name: 'email',      	   type: 'string'},	           
	   {name: 'address_city',      type: 'string'},	           
	   {name: 'address_country',   type: 'string'},	           
	   {name: 'address_region',    type: 'string'},	           
	   {name: 'address_street',    type: 'string'},	           
	   {name: 'country_abbr',      type: 'string'},	           
	   {name: 'region_abbr',      type: 'string'},	           
	   {name: 'home-pre',      type: 'string'},	           
	   {name: 'home-ac',      type: 'string'},	           
	   {name: 'home-num',      type: 'string'},	           
	   {name: 'mobile-num',      type: 'string'},	           
	   {name: 'mobile-ac',      type: 'string'},	           
	   {name: 'mobile-pre',      type: 'string'},	           
	   {name: 'work-num',      type: 'string'},	           
	   {name: 'work-ac',      type: 'string'},	           
	   {name: 'work-pre',      type: 'string'},	           
	   {name: 'work-ext',      type: 'string'},	           
	   {name: 'address_street',      type: 'string'},	           
	   {name: 'person_birthdate',  type: 'date', dateFormat: 'Y/m/d'},
	   {name: 'last_login_date',   type: 'string' },
	   {name: 'user_id',  type: 'int'},
	   {name: 'person_id',  type: 'int'}
	]
});}