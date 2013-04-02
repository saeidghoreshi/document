
var classname = "Spectrum.getstarted.personalcontact";
if(!App.dom.definedExt(classname)){
	
Ext.define(classname,
{

	initComponent:function(config){},
	form:null,
	url:'index.php/person/post_update_active_person_contact/'+App.TOKEN,//url for submit
	load:function()
	{
		YAHOO.util.Connect.asyncRequest('GET','index.php/permissions/active_user_record/'+App.TOKEN,//url for load
		{success:function(o)
		{
			var data=YAHOO.lang.JSON.parse(o.responseText);
			//cannot just load a plain object,  EXT needs a MODEL
			var user = Ext.ModelManager.create(data[0], 'User');
	        this.form.loadRecord(user);
		},failure:App.error.xhr,scope:this})	
	},
	constructor:function(config)
	{
		this.form = Ext.create('Spectrum.forms.person_contact',{
			bodyStyle:'border:0px; padding:0px; background-color:transparent',
			renderTo:'frm-gs-personalcontact',
			width:355
			,height:300
		});
		this.load();
		
	}
	
});
	
}

App.GS.activeForm[App.GS.slideEnum.CONTACT] = Ext.create(classname,{});
