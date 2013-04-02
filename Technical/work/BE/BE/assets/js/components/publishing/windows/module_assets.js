var astwin='Spectrum.windows.websites.module_assets';
if(!App.dom.definedExt(astwin)){
Ext.define(astwin,
{
	extend: 'Ext.spectrumwindows',  

	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
    	if(!config.id) config.id='assetreateseform_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title) config.title= 'Module Asset';

		config.width=600;
		config.height= 120;

		this.callParent(arguments); 
	}
});}
