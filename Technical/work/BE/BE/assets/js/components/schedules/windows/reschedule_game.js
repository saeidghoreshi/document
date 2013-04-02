var gwe = 'Spectrum.windows.game.edit';
if(!App.dom.definedExt(gwe)){
Ext.define(gwe,
{
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  	
    	if(!config.id)
    		config.id='reschedule_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Reschedule this Game';

		config.width=400;
		config.height= 400;
		//config.items passed automatically

		this.callParent(arguments); 
	}
});}

