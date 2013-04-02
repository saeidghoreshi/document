var winclass='Spectrum.windows.order_prizes';
if(!App.dom.definedExt(winclass)){
Ext.define(winclass,
{
	extend: 'Ext.spectrumwindows', 
	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
     	if(!config.id)
    		config.id='orderprizes_form_window';//
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Order Sizes';
			
		config.width=850;
		config.height= 440;
		//config.items passed automatically
		this.callParent(arguments);    	
	}   
});}
