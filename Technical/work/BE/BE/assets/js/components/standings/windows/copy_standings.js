var windis = 'Spectrum.windows.copy_standings';
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
    	if(!config.id)config.id='createstcopyplayform_window';
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title) config.title= 'Copy Standings';
		config.width=350;
		config.height= 150;
		this.callParent(arguments);
	}
});}

