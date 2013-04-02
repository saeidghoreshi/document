
 

var classname = "Spectrum.getstarted.leaguedetails";
if(!App.dom.definedExt(classname)){
Ext.define(classname,
{
	initComponent:function(config){},
	form:null,
	
	url:'index.php/associations/json_createnewleague/'+App.TOKEN,
	load:function()
	{
		YAHOO.util.Connect.asyncRequest('GET','index.php/getstarted/json_active_league_details/'+App.TOKEN,
		{success:function(o)
		{
			try{
			var data=YAHOO.lang.JSON.parse(o.responseText);
			//cannot just load a plain object,  EXT needs a MODEL
			}
			catch(e)
			{
				App.error.xhr(o);
			}			
			if(!data.length) {return;}

			var lg = Ext.ModelManager.create(data[0], 'LeagueModel');//this is not a user, but all fields are the same so its ok
			
	        this.form.loadRecord(lg);
		},failure:App.error.xhr,scope:this})	
	},
	
	constructor:function(config)
	{ 
		this.domainListStore    =//needed for all forms
        		new simpleStoreClass().make(['id','domain'],"index.php/endeavor/json_getDomainNames/"+App.TOKEN);  
		 
		this.form = Ext.create('Spectrum.forms.league',{
			bodyStyle:'border:0px; padding:0px; background-color:transparent',
			renderTo:'frm-gs-leaguedetails',
			domainListStore :this.domainListStore,
			save_btn:false,
			hide_upload:true,//remove the forms button, we are doing our own save thing here
			hide_email:true,
			width:355,height:210
		});
		 
		this.domainListStore.on('load',this.load,this);
		//this.load();
		
		this.form.disable();//to stop user using 'tab' key to mess up animations
	}
	
});
	
}
App.GS.activeForm[App.GS.slideEnum.ORGDETAILS] = Ext.create(classname,{});