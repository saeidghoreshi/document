var gwc = 'Spectrum.windows.game.create';
if(!App.dom.definedExt(gwc)){
Ext.define(gwc,
{
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  	
		if(!config.id)
    		config.id='game_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Create a Game';
			

		config.width=700;
		config.height= 400;
		//config.items passed automatically
	
		this.callParent(arguments); 
	}
});}