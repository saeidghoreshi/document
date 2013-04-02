
var wincl='Spectrum.windows.categories';
if(!App.dom.definedExt(wincl)){
Ext.define(wincl,
{
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
    	if(!config.id) config.id='cctg_form_window';//
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title) config.title= 'Category Details';
			
		config.width=600;
		config.height= 170;
		this.callParent(arguments); 
	}
});}

