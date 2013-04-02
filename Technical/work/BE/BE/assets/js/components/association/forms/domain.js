   //org name
//default if an org has no fancy formvar 
var fnm = 'Spectrum.forms.domain';
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
		var id='c_assoc_form';//+Math.random();
    	if(!config.id){config.id=id};

    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		if(!config.title)config.title= '';
		this.window_id=config.window_id;
		config.width= '100%';
		//config.height=250;
	    config.fieldDefaults= {labelWidth: 105,autoFitErrors: false};
	     
	     
    	
	   // config.defaults= {anchor: '100%'};
	    config.items=
	    [
 
	        {
	        	xtype: 'hidden'
	        	,name : 'org_id'
	        	,allowBlank: false
	        	,value:config.org_id
	        },
	        {
	        	xtype: 'hidden'
	        	,name : 'id'
	        	,allowBlank: false
	        	,value:''
	        },
	        {
	        	xtype: 'textfield'
	        	,name : 'domain' 
	        	,fieldLabel: 'Domain Name'
	        	,flex:true
	        	,allowBlank: false
	        	,value:config.org_name
	        }
 
	    ];
	        
	    config.bottomItems=
	    [
	    	'->',
	    	{
	    		iconCls:'disk',
	    		text:"Save",
	    		scope:this,
	    		handler:function()
				{
					this.save();
				}
			}
	    ];
	        
	        	
        this.callParent(arguments);    	    	
	}
	,save:function()
	{
		var form=this.getForm();
 
		//form.submit(
		Ext.Ajax.request(
		{
			url:"index.php/associations/post_new_domain/"+App.TOKEN
			,scope:this
			,method:'POST'
			,disableCaching:true
			,params:form.getValues()
			,failure : function(o){App.error.xhr(o);	}
			,success : function(o)
			{
				var w=Ext.getCmp(this.window_id);
				if(w) w.hide();
				else Ext.MessageBox.alert("Success",'Domain saved.');
				
			}
		});
		
		
	}
	
	
});}

