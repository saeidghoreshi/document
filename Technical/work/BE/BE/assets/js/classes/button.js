if(!App.dom.definedExt('Spectrum.button'))//very important workaround for IE and sometimes chrome
{
    Ext.define('Spectrum.button',   
    {     
	    extend: 'Ext.Button', 
        alias:'widget.spectrumbutton', // now we can use {xtype:'spectrumbutton'} 
        initComponent: function(config) 
        {
            this.callParent(arguments);
        }, 
        
        constructor     : function(config)
        {   
        	if(!config.cls)config.cls='x-btn-default-small';
        	config.pressed=false;
        	//http://docs.sencha.com/ext-js/4-0/#!/api/Ext.button.Button
        	
            this.callParent(arguments); 
        }
 
    });  
}                        
