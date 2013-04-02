
var bfw = 'Spectrum.windows.domain';
if(!App.dom.definedExt(bfw)){
Ext.define(bfw,
{
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) {this.callParent(arguments);},
    constructor     : function(config)
    {  
    	if(!config.id)
    		config.id='assc_dmmn_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'New Domain';

		config.width=300;
		config.height= 100;
		//config.items passed automatically
	
		this.callParent(arguments);

	}
});}

