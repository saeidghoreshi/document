   //org name
//default if an org has no fancy formvar 
var fnm = 'Spectrum.forms.help_manager';
if(!App.dom.definedExt(fnm)){
Ext.define(fnm,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
	save:function()
	{
		var form=this.getForm();
 
		form.submit(
		{
			url:"index.php/endeavor/post_ctr_mth_help/"+App.TOKEN
			,scope:this
			,failure : function(f, action){App.error.xhr(action.response);	}
			,success : function(f,a)
			{ 
				var w=this.up('window');//Ext.getCmp(this.window_id);
				if(w) w.hide();
				else Ext.MessageBox.alert("Success",'Help data saved.');
				
			}
		});
	},
    constructor     : function(config)
    {  
    	config.centerAll=true;
		var id='c_hpf_form';//+Math.random();
    	if(!config.id){config.id=id};
		
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		if(!config.title)config.title= '';
		this.window_id=config.window_id;
		 
		//config.height=250;
	    config.fieldDefaults = {labelWidth: 150,autoFitErrors: false};
	     
	     
    	
	   // config.defaults= {anchor: '100%'};
	    config.items=
	    [
 
	        {//display fields are not posted!
	        	xtype: 'displayfield' 
	        	,fieldLabel: 'controller_id'
	        	,value:config.controller_id
	        }
			,{//also have hidden one
	        	xtype: 'hidden'
	        	,name : 'controller_id' 
	        	,allowBlank: false
	        	,value:config.controller_id
	        }
	        ,{
	        	xtype: 'textfield'
	        	,name : 'method_name' 
	        	,fieldLabel: 'Name of window method'
				,value:'window_'
	        	,allowBlank: false
	        }
	        ,{
	        	xtype: 'textfield'
	        	,name : 'view_filename' 
	        	,fieldLabel: 'Name of view file'
	        	,allowBlank: false
	        }
	        ,{
	        	xtype: 'displayfield'
	        	//,name : 'view_filename' 
	        	
	        	,value: 'All help views are in views/help. Any window_ method with no view file attached will load help/default.php '
	        //	,allowBlank: false
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
	
	
	
});}

