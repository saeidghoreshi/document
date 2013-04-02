var lgw="Spectrum.windows.league_wide";
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
    		config.id='leaguemodify_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'League Details';
			
		config.width = 582;
		config.height= 340;
		//config.items passed automatically
		this.callParent(arguments);    	
	}   
});}	