 
if(!App.dom.definedExt('Spectrum.windows.signing_authority')){
Ext.define('Spectrum.windows.signing_authority',    
{
    extend: 'Ext.spectrumwindows', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    },              
 
    constructor : function(config) 
    {                 
		if(!config.id) config.id='sa_window_';
    	if(Ext.getCmp(config.id)) Ext.getCmp(config.id).destroy();
    	
        config.width       =250;
		config.height      =290;
        this.callParent(arguments); 
    }
        
});}
 