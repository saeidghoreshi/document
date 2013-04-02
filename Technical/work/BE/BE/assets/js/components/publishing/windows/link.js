var wst = 'Spectrum.windows.link';
if(!App.dom.definedExt(wst)){
Ext.define(wst,
{
	extend: 'Ext.spectrumwindows', 

	initComponent: function(config) {this.callParent(arguments);},
    constructor     : function(config)
    {  
    	//like most windows, we only need id, title, width, and height.
    	//using all other defaults from base class
    	if(!config.id)config.id='creatdv_link_window';
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title) config.title= 'Link Details';
			

		//config.width =470;
		config.height= 230;
		this.callParent(arguments);
	}
});}