var wst = 'Spectrum.windows.blackout';
if(!App.dom.definedExt(wst)){
Ext.define(wst,
{
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
    	//like most windows, we only need id, title, width, and height.
    	//using all other defaults from base class
    	if(!config.id)config.id='blackout_formwindow__';
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title) config.title= 'Create Blackout';
			

		config.width =415;
		config.height= 180;
		this.callParent(arguments);
	}
});}