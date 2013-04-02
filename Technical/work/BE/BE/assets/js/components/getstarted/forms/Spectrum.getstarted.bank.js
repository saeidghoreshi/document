var bank_model_id='GsBankModel';
//need a model for loadRecord
if(!App.dom.definedExt(bank_model_id)){
Ext.define( bank_model_id,
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'bankaccount_name',      type: 'string'},
	   {name: 'bankaccount_id',        type: 'int'},
	   {name: 'type_id',  type: 'string'},
	   {name: 'transit',  type: 'string'},
	   {name: 'institution',  type: 'string'},//dont make this a n integer, or load record WILL NOT WORK with the combobox in the edit/create form
	   {name: 'account',  type: 'string'},
	   {name: 'bankname',  type: 'string'}
	]
});}

var classname = "Spectrum.getstarted.bank";
if(!App.dom.definedExt(classname)){
Ext.define(classname,
{
	initComponent:function(config){},
	form:null,
	
	//url:'index.php/finance/post_bank_skipmotion/'+App.TOKEN,
	url:'index.php/finance/json_new_motion_bankaccount_add/'+App.TOKEN,
	load:function()
	{
		
		YAHOO.util.Connect.asyncRequest('GET','index.php/finance/json_get_bankaccounts/'+App.TOKEN,
		{success:function(o)
		{
			var data=YAHOO.lang.JSON.parse(o.responseText);
			//cannot just load a plain object,  EXT needs a MODEL
			
			if(typeof data.root != 'undefined') {data=data.root;}//skip paginator data

			if(!data.length) {return;}

			var record = Ext.ModelManager.create(data[0], 'GsBankModel');//this is not a user, but all fields are the same so its ok
			
	        this.form.loadRecord(record);
		},failure:App.error.xhr,scope:this})	
		
	},
	
	constructor:function(config)
	{
		this.form = financeForms.form_bankaccount_add_motion(
		{
			renderTo:'frm-gs-bankaccount'
			,autoHeight:false
			,autoScroll:false
			,height:250
			,allowBlank:false
			,hideDesc:true
		});

		this.form.disable();//to stop user using 'tab' key to mess up animations
		this.load();
	}
	
});
	
}
App.GS.activeForm[App.GS.slideEnum.BANK] = Ext.create(classname,{});


