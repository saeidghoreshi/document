
var classname = "Spectrum.getstarted.orgaddress";
if(!App.dom.definedExt(classname)){
	
Ext.define(classname,
{
	initComponent:function(config){},
	form:null
	
	,url:'index.php/endeavor/post_org_address/'+App.TOKEN
	,load:function()
	{
		YAHOO.util.Connect.asyncRequest('GET','index.php/getstarted/json_activeorg_addresses/'+App.TOKEN,
		{success:function(o)
		{
			var data=YAHOO.lang.JSON.parse(o.responseText);
			//cannot just load a plain object,  EXT needs a MODEL
			var user = Ext.ModelManager.create(data[0], 'User');//this is not a user, but all fields are the same so its ok
	        this.form.loadRecord(user);
		},failure:App.error.xhr,scope:this})	
	},
	
	constructor:function(config)
	{
		var combo_id='address_type';
		this.form = Ext.create('Spectrum.forms.person_address',{//person and org have the same address
			ext_address_type_id:combo_id,
			bodyStyle:'border:0px; padding:0px; background-color:transparent',
			renderTo:'frm-gs-orgaddress',
			id:'getstarted_org_address',//added id so it wont conflict with person address form
			width:355//,disabled:true 
		});
		this.load();
		
		Ext.getCmp(combo_id).on('keydown',function(f,e,o)
		{  
			console.log('keydown event ');
			
			if(e.getCharCode() == e.TAB)
			{
				
				console.log('stopEvent  ');
				e.stopEvent();
				return false;
			}
			//
			// on enter key, perform the login action
			return false;

		});
		
		/*
		Ext.getCmp(combo_id).on('keypress',function(f,e,o)
		{  
			console.log('keypress event false');
			//console.log(e);
			// on enter key, perform the login action
			return false;

		});
		
		Ext.getCmp(combo_id).on('keyup',function(f,e,o)
		{  
			console.log('keyup event false');
			//console.log(e);
			// on enter key, perform the login action
			return false;

		});*/
		this.form.disable();//to stop user using 'tab' key to mess up animations
	}
	
});
	
}
App.GS.activeForm[App.GS.slideEnum.ORGADDRESS] = Ext.create(classname,{});