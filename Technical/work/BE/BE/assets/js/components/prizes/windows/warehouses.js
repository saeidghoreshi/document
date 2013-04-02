
var warehousewindow='Spectrum.windows.warehouses';
if(!App.dom.definedExt(warehousewindow)){
Ext.define(warehousewindow,
{
	extend: 'Ext.spectrumwindows', 
	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
    	if(!config.id)
    		config.id='cwarehouses_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Warehouse Details';
			
		config.width=600;
		config.height= 180;
		this.callParent(arguments); 
		
	}
});}
