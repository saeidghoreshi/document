var windis = 'Spectrum.windows.standings.display';
if(!App.dom.definedExt(windis)){
Ext.define(windis,
{
	//extend: 'Ext.window.Window', 
	extend: 'Ext.spectrumwindows',
	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
    	if(!config.id)config.id='createstnd_displayform_window';
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title) config.title= 'Display Options';
		config.width=630;
		config.height= 400;
		this.callParent(arguments);
	}
});}

