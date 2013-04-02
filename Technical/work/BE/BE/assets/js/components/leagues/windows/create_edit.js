var lgw="Spectrum.windows.league";
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
    		config.id='leaguecreate_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'New League';
			
		config.width = 382;
		if(!config.height)config.height= 200;
		//config.items passed automatically
		this.callParent(arguments);    	
	}   
});}	