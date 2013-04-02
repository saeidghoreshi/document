
var bfw = 'Spectrum.windows.help_manager';
if(!App.dom.definedExt(bfw)){
Ext.define(bfw,
{
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) {this.callParent(arguments);},
    constructor     : function(config)
    {  
    	if(!config.id)
    		config.id='hp_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Help View';

		config.width=400;
		config.height= 200;
		//config.items passed automatically
	
		this.callParent(arguments);

	}
});}

