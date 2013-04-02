
var classname = "Spectrum.getstarted.personaladdress";
if(!App.dom.definedExt(classname)){
	
Ext.define(classname,
{
	initComponent:function(config){},
	form:null,
	
	
	load:function()
	{
		//.log('address load');
		YAHOO.util.Connect.asyncRequest('GET','index.php/permissions/active_user_record/'+App.TOKEN,
		{success:function(o)
		{
			var data=YAHOO.lang.JSON.parse(o.responseText);
			//cannot just load a plain object,  EXT needs a MODEL
			var user = Ext.ModelManager.create(data[0], 'User');
	        this.form.loadRecord(user);
		},failure:App.error.xhr,scope:this})	
	},
	
	url:'index.php/person/post_update_active_person_addr/'+App.TOKEN,//the url for form.submit
	constructor:function(config)
	{
 
		this.form = Ext.create('Spectrum.forms.person_address',{
			bodyStyle:'border:0px; padding:0px; background-color:transparent',
			renderTo:'frm-gs-personaladdress',
			width:355//,disabled:true 
		});
		this.load();
 
		this.form.disable();//to stop user using 'tab' key to mess up animations
		
 
	}
	
});
	
}
App.GS.activeForm[App.GS.slideEnum.ADDRESS] = Ext.create(classname,{});