var szwindow='Spectrum.windows.sizes';
if(!App.dom.definedExt(szwindow)){
Ext.define(szwindow,
{
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
    	
    	if(!config.id)
    		config.id='prsize_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Size Details';
			
		config.width=600;
		config.height= 150;
		this.callParent(arguments); 
 
	}
});}
