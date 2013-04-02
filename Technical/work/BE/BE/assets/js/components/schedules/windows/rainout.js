var wrain = 'Schedule.windows.rainout';
if(!App.dom.definedExt(wrain)){
Ext.define(wrain,
{

	extend: 'Ext.spectrumwindows', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },

    constructor     : function(config)
    { 
    	if(!config.id)
    		config.id='rainout_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Rainout Games';
			

		config.width=700;
		config.height= 400;
		//config.items passed automatically

        this.callParent(arguments);
	}
});}