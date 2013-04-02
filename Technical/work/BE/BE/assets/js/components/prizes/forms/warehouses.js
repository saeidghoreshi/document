
var whouseform='Spectrum.forms.warehouses';
if(!App.dom.definedExt(whouseform)){
Ext.define(whouseform,
{
    
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    save:function()
    {
		var form = this.getForm(); 
		if (!form.isValid()) {return;}
		                         
		var data=[];
		//post all data
		var valid=true;
		Ext.iterate(form.getValues(), function(key, value) 
		{
			if(key=='warehouse_name' && value.split(' ').join('')==''){valid=false;}
			value=escape(value);
		    data.push(key+"="+value);
		});
		if(!valid)
		{
		    Ext.MessageBox.alert('Could not create','Enter a valid Name');
		    return;
		}
		var post = data.join("&");
		//.log(post);
		var url='index.php/prize/post_new_warehouse/'+App.TOKEN;
		var callback={scope:this,failure:App.error.xhr,success:function(o)
		{
		    var r=o.responseText;
			if(isNaN(r)||r<=0)
			{
				Ext.MessageBox.show({title:'Could not create :',msg:r,icon:Ext.MessageBox.ERROR,buttons:Ext.MessageBox.OK});
			}
			else
			{
				//close window if we have a valid pointer to it
				if(Ext.getCmp(this.window_id))
				{
				Ext.getCmp(this.window_id).hide();
					
				}
				else
				{
					Ext.MessageBox.show({title:'Success',msg:'Create more, or close the window',
							icon:Ext.MessageBox.INFO,buttons:Ext.MessageBox.OK});
					this.getForm().reset();
				}
			}
			
		}};
		YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
 
	
    },
    constructor     : function(config)
    {  
		var id='c_warehouses_form';//+Math.random();
		this.window_id=config.window_id;
    	if(!config.id){config.id=id};
    	if(Ext.getCmp(config.id)){ Ext.getCmp(config.id).destroy(); }
    	
 		config.centerAll=true;//handle most formatting
 		
	    config.fieldDefaults= {labelWidth: 115,msgTarget: 'side',autoFitErrors: false};
	   // config.defaults= {anchor: '100%'};
	    config.items=
	    [
		    {xtype: 'hidden',name : 'warehouse_id',value:-1} 
	        ,{
	        	xtype: 'textfield'
	        	,name : 'warehouse_name'
	        	,   fieldLabel: 'Name'
	        	,value:''
	        	,allowBlank: false
	        }
	        ,{
	        	xtype: 'textareafield'
	        	,name : 'warehouse_desc'
	        	,   fieldLabel: 'Description'
	        	,value:''
	        	,allowBlank: true
	        }
	    ];
	    if(!config.bottomItems)config.bottomItems=new Array();
	    config.bottomItems.push(
		    '->',
 			{
 				text   : 'Save'
 				,iconCls:'disk'
 				,scope:this
 				,handler: function() 
		        { 
		        	this.save();	
				}
		          
		    }        
	    );
	        
		 
	
	
	    	
        this.callParent(arguments);    	    	
	}
	
	
	
});}

