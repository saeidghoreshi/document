
var classname = "Spectrum.getstarted.personaldetails";
if(!App.dom.definedExt(classname)){
	
Ext.define(classname,
{

	initComponent:function(config){},
	load:function()
	{
		YAHOO.util.Connect.asyncRequest('GET','index.php/permissions/active_user_record/'+App.TOKEN,
		{success:function(o)
		{
			var data=YAHOO.lang.JSON.parse(o.responseText);
			//cannot just load a plain object,  EXT needs a MODEL
			var user = Ext.ModelManager.create(data[0], 'User');
	        this.form.loadRecord(user);
		},failure:App.error.xhr,scope:this})	
	},
	form:null,
	url:'index.php/person/post_update_active_person/'+App.TOKEN,//using existing function, used in usersgrid roweditor & others, so becareful if you touch it
	constructor:function(config)
	{
		this.form = Ext.create('Spectrum.forms.person_details',{
			bodyStyle:'border:0px; padding:0px; background-color:transparent',
			renderTo:'frm-gs-personaldetails',
			width:355,
			height:195
		});
		this.load();
	}
	
});
	
}

App.GS.activeForm[App.GS.slideEnum.PERSON] = Ext.create(classname,{});
