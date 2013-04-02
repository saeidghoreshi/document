
var wclass = 'Spectrum.windows.teams.create';
if(!App.dom.definedExt(wclass)){
Ext.define(wclass,
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
    		config.id='createteam_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Create New Team';
			

		config.width=630;
		config.height= 400;
		//config.items passed automatically
	
		this.callParent(arguments); 

	}
});}