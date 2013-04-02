var winclass='Spectrum.windows.order';
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
    		config.id='order_form_window';//+ 
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Order Details';

		config.width=440;
		config.height= 150;
		//config.items passed automatically
	
		this.callParent(arguments); 
	}   
});
}
