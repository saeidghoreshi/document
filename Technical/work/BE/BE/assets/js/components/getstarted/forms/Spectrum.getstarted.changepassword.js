var debug=false;



var classname = "Spectrum.getstarted.changepassword";
if(!App.dom.definedExt(classname)){
Ext.define(classname,
{
	initComponent:function(config){},
	form:null,
	load:function()
	{
		YAHOO.util.Connect.asyncRequest('GET','index.php/permissions/active_user_record/'+App.TOKEN,
		{success:function(o)
		{
			var data=YAHOO.lang.JSON.parse(o.responseText);
			//cannot just load a plain object,  EXT needs a MODEL
			var user = Ext.ModelManager.create(data[0], 'User');
	        this.form.loadRecord(user);
		},failure:App.error.xhr,scope:this});	
	},
	
	url:'index.php/permissions/post_update_active_userpass/'+App.TOKEN,//for submit
	constructor:function(config)
	{
		this.form = Ext.create('Spectrum.forms.user_login',
		{
			allowBlank:false,//this forces the change
			bodyStyle:'border:0px; padding:0px; background-color:transparent',
			renderTo:'frm-gs-changepassword',
			width:355
		});
		this.load();
	}

});}

App.GS.activeForm[App.GS.slideEnum.PASSWORD] = Ext.create(classname,{});