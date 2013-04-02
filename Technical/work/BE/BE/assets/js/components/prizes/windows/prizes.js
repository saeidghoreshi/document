var prwindow='Spectrum.windows.prizes';
if(!App.dom.definedExt(prwindow)){
Ext.define(prwindow,
{
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
    	if(!config.id)
    		config.id='ppwacreateses_form_window';//+
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Prize Details';
			
		config.width=600;
		config.height= 320;
		this.callParent(arguments); 
	}
});}

