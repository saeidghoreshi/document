if(!App.dom.definedExt('Spectrum.windows.division_fees')){//dont define it twice
Ext.define('Spectrum.windows.division_fees',    
{
    extend: 'Ext.spectrumwindows', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    },              
 
    constructor : function(config) 
    {                 
    	if(!config.id)config.id='divfees'+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}//destroy if it exists from before
		 
     	 config.width   = 300;
         config.height  = 190;//moved here from the grid. makes it more modular
         config.title       = 'Customized Season Registration Rates';
        this.callParent(arguments); 
    }
        
});
}