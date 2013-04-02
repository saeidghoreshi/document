var winclass='Spectrum.windows.user';
if(!App.dom.definedExt(winclass)){
Ext.define(winclass,
{
	//extend: 'Ext.window.Window', 
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {
        if(!config.id)
    		config.id='_user_form_window_';//+
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)config.title= 'User Details';

		config.width=705;
		config.height= 420;
		//config.items passed automatically
		this.callParent(arguments);        
	}   
});
}