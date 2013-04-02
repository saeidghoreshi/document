//used to swap the divisions of a team
var wsclass = 'Spectrum.windows.teams.season';
if(!App.dom.definedExt(wsclass)){
Ext.define(wsclass,
{
	//extend: 'Ext.window.Window', 
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
    	if(!config.id)
    		config.id='seasonteam_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Assign Team to Seasons';
			

		config.width=500;
		config.height= 450;
		//config.items passed automatically
	
		this.callParent(arguments);

	}
});}

