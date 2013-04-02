var wclass = 'Spectrum.windows.teams.edit';
if(!App.dom.definedExt(wclass)){
Ext.define(wclass,
{
	extend: 'Ext.spectrumwindows', 
	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
    	if(!config.id)
    		config.id='editteam_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Edit Team';
			

		config.width=400;
		config.height= 100;
		//config.items passed automatically
	
		this.callParent(arguments);

	}
});}