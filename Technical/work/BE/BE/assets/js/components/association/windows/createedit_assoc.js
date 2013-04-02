
var bfw = 'Spectrum.windows.assoc';
if(!App.dom.definedExt(bfw)){
Ext.define(bfw,
{
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) {this.callParent(arguments);},
    constructor     : function(config)
    {  
    	if(!config.id)
    		config.id='assc_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'New Association';

		config.width=400;
		config.height= 440;
		//config.items passed automatically
	
		this.callParent(arguments);

	}
});}

