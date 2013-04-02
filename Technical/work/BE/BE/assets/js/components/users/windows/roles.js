var rolewindow='Spectrum.windows.org_roles';
if(!App.dom.definedExt(rolewindow)){    
Ext.define(rolewindow,
{
    extend: 'Ext.spectrumwindows', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
    	if(!config.id){config.id='roles_form_fixed_window'+Math.random();}
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
    	if(config.title){config.title='Assign Roles';}
    	//config.width=400;
    	config.height=400;
 
    	this.callParent(arguments); 
	}
});}
