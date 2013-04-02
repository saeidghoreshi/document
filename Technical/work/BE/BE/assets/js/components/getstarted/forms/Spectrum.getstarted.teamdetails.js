
//  renderTo:'frm-gs-teamdetails'

var league_model_id='TeamLeagueModel';
//this model may already exist, from league grid or something, but try and define it here anyway
if(!App.dom.definedExt(league_model_id)){
Ext.define( league_model_id,
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'league_name',      type: 'string'},
	   {name: 'team_name',      type: 'string'},
	   {name: 'team_id',      type: 'string'},
	   {name: 'league_id',      type: 'int'}//,
	  // {name: 'type_id',  type: 'string'},
	  // {name: 'type_name',  type: 'string'},
	  // {name: 'domainname',  type: 'string'},//dont make this an integer, or load record WILL NOT WORK with the combobox in the edit/create form
	  // {name: 'websiteprefix',  type: 'string'},
	  // {name: 'url',  type: 'string'}
	]
});}

var classname = "Spectrum.getstarted.teamdetails";
if(!App.dom.definedExt(classname)){
Ext.define(classname,
{
	initComponent:function(config){},
	form:null,
	
	url:'index.php/teams/post_team_name/'+App.TOKEN,
	load:function()
	{
		YAHOO.util.Connect.asyncRequest('GET','index.php/teams/json_activeorg_team/'+App.TOKEN,
		{success:function(o)
		{
			var data=YAHOO.lang.JSON.parse(o.responseText);
			//cannot just load a plain object,  EXT needs a MODEL
			
			if(!data.length) {return;}

			var lg = Ext.ModelManager.create(data[0], 'TeamLeagueModel');//this is not a user, but all fields are the same so its ok
			
	        this.form.loadRecord(lg);
		},failure:App.error.xhr,scope:this})	
	},
	
	constructor:function(config)
	{
		//team form does not display league name the way we would want to, so 
		this.form = Ext.create('Ext.spectrumforms',{
			bodyStyle:'border:0px; padding:0px; background-color:transparent',
			renderTo:'frm-gs-teamdetails',
			fieldDefaults:
	        {
	            labelWidth: 110
	        },
			items:
			[
				{xtype:'hidden',name:'team_id'},
				{xtype:'hidden',name:'league_id'},
				{xtype:'displayfield',fieldLabel:'League',name:'league_name',width:355},
				{xtype:'textfield',fieldLabel:'Team Name',name:'team_name',width:355}
				
			],
			width:355,height:210
		});
		this.load();
		
		this.form.disable();//to stop user using 'tab' key to mess up animations
	}
	
});
	
}
App.GS.activeForm[App.GS.slideEnum.ORGDETAILS] = Ext.create(classname,{});