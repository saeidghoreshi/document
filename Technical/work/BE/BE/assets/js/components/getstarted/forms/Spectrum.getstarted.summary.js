
var gs_model='SummaryGS';
if(!App.dom.definedExt(gs_model)){
Ext.define( gs_model,
{
	extend: 'Ext.data.Model',
	fields: 
	[
		//org
	   {name: 'org_name',      type: 'string'},
	   {name: 'org_id',      type: 'int'},
	   {name: 'type_id',  type: 'string'},
	   {name: 'type_name',  type: 'string'},
	   //league
	   {name: 'domainname',  type: 'string'},
	   {name: 'websiteprefix',  type: 'string'},
	   {name: 'url',  type: 'string'},
	   //address
	   
	   {name: 'org_address',      type: 'string'},	           
	   {name: 'org_address_city',      type: 'string'},	           
	   {name: 'org_address_country',   type: 'string'},	           
	   {name: 'org_address_region',    type: 'string'},	           
	   {name: 'org_address_street',    type: 'string'},	           
	   {name: 'org_country_abbr',      type: 'string'},	           
	   {name: 'org_region_abbr',      type: 'string'},	           
	   
	   
	   //bank
	   {name: 'bankaccount_name',      type: 'string'},
	   {name: 'bankaccount_id',        type: 'int'},
	   {name: 'type_id',  type: 'string'},
	   {name: 'transit',  type: 'string'},
	   {name: 'institution',  type: 'string'},//dont make this an integer, or load record WILL NOT WORK with the combobox in the edit/create form
	   {name: 'account',  type: 'string'},
	   {name: 'bankname',  type: 'string'},
	   
	   //user pseron model
	   {name: 'person_fname',      type: 'string'},
	   {name: 'person_lname',      type: 'string'},
	   {name: 'person_name',      type: 'string'},
	   {name: 'person_gender',     type: 'string'},
	   {name: 'postal_value',      type: 'string'},
	   {name: 'login',      	   type: 'string'},
	   {name: 'email',      	   type: 'string'},	           
	   {name: 'address',      	   type: 'string'},	           
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
	   {name: 'person_id',  type: 'int'},
	   
	   
	   //totals:
	            
	   {name: 'total_exec',   type: 'string'},	           
	   {name: 'total_other',    type: 'string'},	           
	   {name: 'total_people',    type: 'string'},	           
	   {name: 'total_sa',      type: 'string'}
	   
	]
});}


var edit_goto=function()
{
	//jump to the given slide
	var val=this.initialConfig.value;
	if(val>0)
		App.GS.jumpTo(this.initialConfig.value);
	
}
var sfrm = "Spectrum.forms.gs_summary";
if(!App.dom.definedExt(sfrm)){
Ext.define(sfrm,
{
	extend: 'Ext.spectrumforms', 
	initComponent: function(config) 
	{
	    this.callParent(arguments);
	},
	constructor:function(config)
	{
		config.height=300;
		var inner_height=165;
		config.autoScroll=false;
		if(!config.width) config.width=355;
		var half=Math.floor(config.width/2);
		//.log(half);
		config.items=
		[
        	{xtype : 'fieldcontainer',layout:'hbox',height:inner_height,

        	// layout : {align: 'middle',pack:'center', padding: 10},
        	items:
			[ 
			    {xtype:'form',width:half,defaults: {anchor: '100%'},height:inner_height,
			    title:'Your Info',id:'_gs_name_bar_',
			    tbar:[ {xtype:'button',text:'',iconCls:'pencil',value:App.GS.slideEnum.PERSON,handler:edit_goto}],
			    items:
			    [
			        //{xtype:'displayfield',name:'person_name',value:'person_name'}
			        {xtype:'displayfield',/*fieldLabel:'Login',*/name:'login',value:''}
			        ,{xtype:'displayfield',name:'email',value:''}
			        ,{xtype:'displayfield',name:'address',value:''}
			       
			    ]}
			    
			    ,{xtype:'form',width:half,defaults: {anchor: '100%'},height:inner_height,
			    title:'Your Users',
			    
			    tbar:[ {xtype:'button',text:'',iconCls:'pencil',value:App.GS.slideEnum.USERS,handler:edit_goto}],
			    items:
			    [
			        //{xtype:'displayfield',value:'USERS'}
			        {xtype:'displayfield',fieldLabel:'Total',value:'',name:'total_people'}
			        ,{xtype:'displayfield',fieldLabel:'Executives',value:'',name:'total_exec'}
			        ,{xtype:'displayfield',fieldLabel:'Signers',value:'',name:'total_sa'}
			        ,{xtype:'displayfield',fieldLabel:'Other Roles',value:'',name:'total_other'}

			    ]}
			    
			]} 
            ,{xtype : 'fieldcontainer',layout:'hbox',height:inner_height,
            
            items:
			[ 
			    {xtype:'form',width:half,defaults: {anchor: '100%'},height:inner_height,
			    title:'Your Org',id:'_gs_org_bar_',
			    tbar:[ {xtype:'button',text:'',iconCls:'pencil',value:App.GS.slideEnum.ORDETAILS,handler:edit_goto}],
			    items:
			    [
			        //{xtype:'displayfield',value:'org_name',name:'org_name'}
			        {xtype:'displayfield',value:'',name:'url'}
			        ,{xtype:'displayfield',value:'',name:'org_address'}
			       // ,{xtype:'button',text:'edit',iconCls:'pencil',value:'o',handler:edit_goto,width:50}
			    ]}
			    ,{xtype:'form',width:half,defaults: {anchor: '100%'},height:180,
			    title:'Account',
			    tbar:[ {xtype:'button',text:'',iconCls:'pencil',value:App.GS.slideEnum.BANK,handler:edit_goto}],
			    items:
			    [
			        {xtype:'displayfield',value:'',name:'bankaccount_name'}
			        ,{xtype:'displayfield',value:'',name:'account'}
			        ,{xtype:'displayfield',value:'',name:'bankname'}
			        //,{xtype:'button',text:'edit',iconCls:'pencil',value:'b',handler:edit_goto,align:'right'}
			        //,{xtype:'displayfield',value:'bankname',name:''}
			       // ,{xtype:'displayfield',value:'',name:''}
			    ]}
			]}	
		];

		
		this.callParent(arguments);     
	}//end of Constructor
});//end of Ext.define
}

var classname = "Spectrum.getstarted.summary";
if(!App.dom.definedExt(classname)){
Ext.define(classname,
{
	initComponent:function(config){},
	form:null,
	
	load:function()
	{
		YAHOO.util.Connect.asyncRequest('GET','index.php/getstarted/json_build_summary/'+App.TOKEN,
		{success:function(o)
		{
			var data=YAHOO.lang.JSON.parse(o.responseText);

			//.log(data);
			
			//cannot just load a plain object,  EXT needs a MODEL
			var modelData = Ext.ModelManager.create(data, 'SummaryGS');
	        this.form.loadRecord(modelData);
	        //.log('afterloadrecord');
	        //some data is in form title, not in a named field
	        //.log('settitle:'+data.person_name)
	        Ext.getCmp('_gs_name_bar_').setTitle(data.person_name);
	        //.log('settitle:'+data.org_name)
	        Ext.getCmp('_gs_org_bar_').setTitle(data.org_name);
		},failure:App.error.xhr,scope:this})	
		
	},
	
	//url:'index.php/person/post_update_active_person_addr/'+App.TOKEN,//the url for form.submit
	constructor:function(config)
	{
		//.log('create summary:');
		this.form = Ext.create('Spectrum.forms.gs_summary',{
			bodyStyle:'border:0px; padding:0px; background-color:transparent',
			renderTo:'frm-gs-summary',
			width:355//,disabled:true 
		});
		
		//.log(this.form);
		this.load();
		
		//this.form.disable();//to stop user using 'tab' key to mess up animations
	}
	
});
	
}
App.GS.activeForm[App.GS.slideEnum.SUMMARY] = Ext.create(classname,{});
