
var wst = 'Spectrum.windows.standings';
if(!App.dom.definedExt(wst)){
Ext.define(wst,
{
	extend: 'Ext.spectrumwindows', 
	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
    	if(!config.id)config.id='createstnd_form_window';
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title) config.title= 'Standings Details';

		config.width=600;
		config.height= 230;
		this.callParent(arguments);
		
		//config.items passed automatically
	}
});}
