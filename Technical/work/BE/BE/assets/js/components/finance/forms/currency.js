//org name
//default if an org has no fancy formvar 
var fnm = 'Spectrum.forms.currency';
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
		var id='c_assoccurr_form';//+Math.random();
    	if(!config.id){config.id=id};
    	
    	
    	config.centerAll=true;

    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		if(!config.title)config.title= '';
		this.window_id=config.window_id;
		  
	    config.fieldDefaults= {labelWidth: 80,autoFitErrors: false};
	  //  config.labelAlign = 'top';
	    
 
	    config.items=
	    [
	        {
	        	xtype: 'hidden'
	        	,name : 'type_id'
	        	,allowBlank: false 
	        },
	        {
	        	xtype: 'hidden'
	        	,name : 'owner_entity_id'
	        	,allowBlank: false 
	        	,value:config.entity_id
	        },
	        {
	        	xtype: 'textfield'
	        	,name : 'type_code'
	        	,fieldLabel: 'Name'
	        	,allowBlank: false
 
	        },
	        {
	        	xtype: 'textfield'
	        	,name : 'currency_abbrev' 
	        	,fieldLabel: 'Abbrev.'
 
	        	,allowBlank: false 
	        }
           ,{                    
                xtype       : 'textfield',
                name        : 'type_descr',
                fieldLabel   : 'Description',
 
                allowBlank  : false
            }
            ,{                    
                xtype       : 'textfield',
                name        : 'html_character',
                fieldLabel   : 'HTML Char',
                 
                value: '$',
                allowBlank  : false
            }
            ,{                    
                xtype       : 'textfield',
                name        : 'icon',
                fieldLabel   : 'Silk Icon',
                 
                value   : 'money', 
                allowBlank  : false
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
				}}
	    ];
	        
	        	
        this.callParent(arguments);    	    	
	}
	,save:function()
	{
		var form=this.getForm();
		if(!form.isValid()){return;}
		
		
		Ext.Ajax.request(
		{
			url:"index.php/finance/post_owned_currency/"+App.TOKEN
            ,scope:this
			,params:form.getValues()
			,method:'POST'
			,disableCaching:true
			,failure : App.error.xhr 
			,success : function(o)
			{
				var w=Ext.getCmp(this.window_id);
				if(w) w.hide();
				else Ext.MessageBox.alert("Success",'Currency saved.');
				
			}
		})
		
	}
	
	
	
});}

