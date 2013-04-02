//org name
//default if an org has no fancy formvar 
var fnm = 'Spectrum.forms.basic_org';
if(!App.dom.definedExt(fnm)){
Ext.define(fnm,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },

    constructor     : function(config)
    {  
		var id='c_basicorgs_form';//+Math.random();
    	if(!config.id){config.id=id};

    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		if(!config.title)config.title= '';
		
		config.width= 300;
		config.height=250;
	    config.fieldDefaults= {labelWidth: 70,autoFitErrors: false};
	    config.labelAlign= 'top';
	    
    	if(typeof config.hide_upload == 'undefined') {config.hide_upload=false;}
    	
	    config.defaults= {anchor: '100%'};
	    config.items=
	    [
	        {
	        	xtype: 'hidden'
	        	,name : 'org_id'
	        	,allowBlank: false
	        	,value:config.org_id
	        },
	        {
	        	xtype: 'textfield'
	        	,name : 'org_name'
	        	,id:'id_title_org_name'
	        	,fieldLabel: 'Name'
	        	,allowBlank: false
	        	,value:config.org_name
	        }
           ,{  
               // id          : 'file_upload',                      
                xtype       : 'filefield',
                
                name        : 'file_upload',
                emptyText   : 'Upload Logo  ',
                fieldLabel  : '',
                //labelWidth  : 150,          
                //flex        :1,
                width:320,   
                hidden:     config.hide_upload,//creator of object can hide this
                allowBlank  : true
            }
	    
	    ];
	        
	        
	        
	        
	        	
        this.callParent(arguments);    	    	
	}
	
	
	
});}

