var winclass='Spectrum.windows.order_upgrade';
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
    		config.id='orderupgrade_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Order Upgrade';
			

		config.width=440;
		config.height= 160;
		//config.items passed automatically
	
		this.callParent(arguments);     

	}   
	
});
}
