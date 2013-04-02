
var fclass = 'Spectrum.forms.teams.edit';
if(!App.dom.definedExt(fclass)){
Ext.define(fclass,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    
    constructor     : function(config)
    {  
    	//this.grid_id=config.grid_id;
    	//this.season_id=config.season_id;
    	
		if(!config.id)config.id='editteam_form';//+Math.random();
 
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
 
		 //{id:id,title: '',autoHeight: true,resizable:false,bodyPadding: 10,width: 600,//height:250,
	     config.fieldDefaults={labelWidth: 125,msgTarget: 'side',autoFitErrors: false};
	     config.defaults= {anchor: '100%'};
	     config.items=
	    [
		    {xtype: 'hidden',name : 'team_id',value:-1}
	        ,{xtype: 'textfield',flex : 1,name : 'team_name',fieldLabel: 'Team Name',allowBlank: false}
	    ];
	    
	    if(!config.url)config.url="index.php/teams/post_team_name/"+App.TOKEN;
	    this.url=config.url;
	    config.bottomItems=
	    [
	    	'->'
	    	,{
				iconCls:'disk'
				,text:'save'
				,scope:this
				,handler:function()
				{ 
					var form=this.getForm();
		            var data=[];
		            Ext.iterate(form.getValues(), function(key, value) 
		            {
						if(key=='team_name')
							value=escape(value);
		                data.push(key+"="+value);
		            }, this);
		            //data.push('season_id='+this.season_id);
		            var post = data.join("&");
 
     				  
     				 var callback={scope:this,success:function(o)
     				 {
 				 		var w=this.up('window');
 				 		if(w)w.hide();
 				 		else Ext.MessageBox('Success','Team Edited');
 						 
					 					 
     				 },failure:App.error.xhr};
	 				 YAHOO.util.Connect.asyncRequest('POST',this.url,callback,post);
					
				}
	    	}
	    ];
	    
	    
        this.callParent(arguments);
	}
});}