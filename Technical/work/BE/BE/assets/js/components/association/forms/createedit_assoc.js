//org name
//default if an org has no fancy formvar 
var fnm = 'Spectrum.forms.assoc';
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
    	if(!this.url) this.url="index.php/associations/json_new_association/"+App.TOKEN;
		var id='c_assoc_form';//+Math.random();
    	if(!config.id){config.id=id};

    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		if(!config.title)config.title= '';
		this.window_id=config.window_id;
		
		config.centerAll=true;
		//config.width= '100%';
		//config.height=250;
	    config.fieldDefaults= {labelWidth: 105,autoFitErrors: false};
	    config.labelAlign = 'top';

	    
    	if(typeof config.hide_upload == 'undefined') {config.hide_upload=false;}
    	if(typeof config.hide_addr == 'undefined') {config.hide_addr=false;}
    	if(typeof config.hide_save == 'undefined') {config.hide_save=false;}
    	
	   // config.defaults= {anchor: '100%'};
	   var matching_width=310;// in order to match width from the address form we are dropping in 
	    if(config.hide_addr ) style={border:0};
	    else style={padding:'20px'};
	    config.items=
	    [
	        {
	        	xtype: 'hidden'
	        	,name : 'org_id'
	        	,allowBlank: false
	        	,value:config.org_id
	        }
	        ,{
	        	xtype: 'hidden'
	        	,name : 'association_id'
	        	,allowBlank: false
	        	,value:config.org_id
	        }
	        ,{
	    		xtype:'fieldset',
	    		title:'',
			    collapsible: false,
			    style:style,
			    items:
				[
		        {
	        		xtype: 'textfield'
	        		,name : 'association_name' 
	        		,fieldLabel: 'Name'
	        		
	        		,allowBlank: false
	        		,hidden:false//??
	        		,value:config.org_name 
	        		,width:matching_width
		        }
		        ,{
	        		xtype: 'textfield'
	        		,name : 'website' 
	        		,fieldLabel: 'Your Website'

	        		,allowBlank: true
	        		,width:matching_width
		        }
	           ,{                    
	                xtype       : 'filefield'
	               , name        : 'file_upload'
	               , fieldLabel   : 'Your Logo '
	               
	               , emptyText   : 'Upload Logo  '
	               , hidden:     config.hide_upload  
	               , allowBlank  : true
	                ,width:matching_width
	            }
	            ]
			}

	    ];
	    if(config.hide_addr==false)
	    {
	    	if(Ext.getCmp('my_assoc_address__'))Ext.getCmp('my_assoc_address__').destroy();
	    	//load the address form into a field set that mathces the above set. looks nice!
			config.items.push(			
			{
	    		xtype:'fieldset',
	    		title:'Address',
	    		hidden:config.hide_addr,
			    collapsible: true,
				items:Ext.create('Spectrum.forms.person_address',{ id:'my_assoc_address__'}) 
			});
	    }
	        
	    config.bottomItems=
	    [
	    	'->'
	    	,{
	    		iconCls:'disk',
	    		text:"Save",
	    		scope:this,
	    		hidden:config.hide_save,
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
				//ajax requset not valid for images	
		form.submit(
		//Ext.Ajax.request(
		{
			url:this.url
			,scope:this
			,method:'POST'
			,params:form.getValues()
			,disableCaching:true
			,failure : function(a,o) {App.error.xhr(o);}
			,success : function(a,o)
			{
				var w=Ext.getCmp(this.window_id);
				if(w) w.hide();
				else Ext.MessageBox.alert("Success",'Association saved.');
				
			}
		})
		
	}
	
});}

