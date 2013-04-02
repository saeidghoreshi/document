var lgw="Spectrum.windows.season";
if(!App.dom.definedExt(lgw)){
Ext.define(lgw,
{
	extend: 'Ext.spectrumwindows', 
	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
     	if(!config.id)
    		config.id='seasoncreate_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Season Details';
			
		config.width = 370;
		config.height= 410;
		//config.items passed automatically
		this.callParent(arguments);    	
	}   
});}	
