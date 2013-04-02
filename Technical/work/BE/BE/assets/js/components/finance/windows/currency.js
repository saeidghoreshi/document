
var bfw = 'Spectrum.windows.currency';
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
			config.title= 'New Currency';

		config.width=320;
		config.height= 230;
		//config.items passed automatically
	
		this.callParent(arguments);

	}
});}

