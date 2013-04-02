
var bfw = 'Spectrum.windows.bugfeature';
if(!App.dom.definedExt(bfw)){
Ext.define(bfw,
{
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) {this.callParent(arguments);},
    constructor     : function(config)
    {  
    	if(!config.id)
    		config.id='bugfeature_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title)
			config.title= 'Submit a bug or feature request';

		config.width=600;
		config.height= 340;
		//config.items passed automatically
	
		this.callParent(arguments);

	}
});}

