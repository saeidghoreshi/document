
var wclass = 'Spectrum.windows.teams_managers';
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
    		config.id='tmangs_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Team Managers';
			

		config.width=630;
		config.height= 550;
		//config.items passed automatically
	
		this.callParent(arguments); 

	}
});}