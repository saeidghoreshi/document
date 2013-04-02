

var pd="Spectrum.forms.order_upgrade";
if(!App.dom.definedExt(pd)){
Ext.define(pd,
{
	//extend: 'Ext.form.Panel', 
	extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    window_id:null,    
    constructor     : function(config)
    {        
		var standard_width=400;
		if(config.window_id) this.window_id=config.window_id;
	   
	    if(!config.id)
    		config.id='order_upgrade_form_';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
    	
		config.title='';
		config.resizable=false;
		config.autoHeight  = true;
		config.fieldDefaults= {labelWidth: 125,msgTarget: 'side',autoFitErrors: false,width:standard_width};
		//
		//defaults: {anchor: '100%'},
		config.items= 
		[
		//two hidden fields
			{
				xtype: 'hidden'
				,name : 'order_id'
				,value:'-1'
				,allowBlank: true
			}
			,{
				xtype: 'hidden'
				,name : 'order_status_id'
				,fieldLabel: 'Order Description'
				,value:'New Order'
				,allowBlank: true
			}
			//two display fields
			,{
				xtype: 'displayfield'
				,name : 'order_desc'
				,fieldLabel: 'Order'
				,value:''
				,allowBlank: true
			}
			,{
				xtype: 'displayfield'
				,name : 'status_name'
				,fieldLabel: 'Current Status'
				,allowBlank: true
			}
			//the upgrade options combo
			,{ 
				xtype: 'scombo'
				,name : 'status_id'
				,fieldLabel: 'New Status'
				,allowBlank: false
				,url:'index.php/prize/json_role_order_status/'+App.TOKEN
				,extraParams:{status:config.status}
				/*
				,store: Ext.create( 'Ext.data.Store',
    			{
    				autoDestroy:false,autoSync :false,autoLoad :true,remoteSort:true ,
			        fields:
			        [
			            'status_id',   //numeric value is the key
			            'status_name' //the text value is the value
			        ]
			        ,proxy: 
			        {   
            			type: 'rest',url: 'index.php/prize/json_role_order_status/'+App.TOKEN,
			            //reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
			            extraParams:{status:config.status}
			        }    
			    })*/
				,valueField  :'status_id'
    			,displayField:'status_name'
			 }
		];
		var save_btn=
		{text   : 'Save',iconCls:'disk',handler: function(o)
	    {
			
			//.log('saving order...');
		    var form = this.up('form').getForm();
			 //dont check valid:  some things can be blank
			//if (!form.isValid()) {alert("??invalid");return;}
					                 

	        var post=new Array();
			Ext.iterate(form.getValues(), function(key, value){ post.push(key+"="+escape(value)); });
			post=post.join('&');
			//.log(post);
		    var url='index.php/prize/post_update_orderstatus/'+App.TOKEN;

		    var callback={scope:this,failure:App.error.xhr,success:function(o)
		    {
				//.log(o);
				var r=o.responseText;
				if(isNaN(r) || r<0)
					{App.error.xhr(o);}
				else
					{Ext.MessageBox.alert('Complete','Save Completed');}
				
		    }};
		    YAHOO.util.Connect.asyncRequest('POST',url,callback,post);

	    }};  	                
		config.dockedItems=
	    [
	        {dock: 'bottom',xtype: 'toolbar',
	            items:["->",save_btn]
			}
		];
	
		this.callParent(arguments); 

	}
	
	
});	}