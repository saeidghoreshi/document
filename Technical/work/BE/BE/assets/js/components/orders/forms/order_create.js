

var pd="Spectrum.forms.order";
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

		
		if(!config.id){config.id='order_details_form_';}

	   
	    if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		config.title='';
		config.resizable=false;
		config.autoHeight  = true;
		config.fieldDefaults= {labelWidth: 125,msgTarget: 'side',autoFitErrors: false,width:standard_width};
		//
		//defaults: {anchor: '100%'},
		config.items= 
		[
			{xtype: 'textfield',name : 'order_desc',fieldLabel: 'Order Description',emptyText:'New Order',value:'',allowBlank: true}
			,{xtype: 'datefield',width:standard_width,name : 'requested_date',fieldLabel: 'Requested By',allowBlank: true}
			
		];
		var save_btn=
		{text   : 'Save',iconCls:'disk',cls:"x-btn-default-small",handler: function(o)
	    {
			var url='index.php/prize/post_create_order/'+App.TOKEN;
			var form = this.up('form').getForm();
	        var form_data=new Array();
	        var rec={};
	        Ext.iterate(form.getValues(), function(key, value) 
	        {
	        	form_data.push(key+"="+escape(value));
	        }, this);

	        var post= form_data.join('&');
	        var callback={scope:this,failure:App.error.xhr,success:function(o)
	        {
	            var r=o.responseText;
	            if(isNaN(r)||r<0)
	            {
					App.error.xhr(o);					
	            }
	            else
	            {
					//Ext.MessageBox.alert('Success','Order number '+r+' created.');

					if(Ext.getCmp('create_order_window')) Ext.getCmp('create_order_window').hide();
					else if(Ext.getCmp('order_form_window')) Ext.getCmp('order_form_window').hide();
					else if(Ext.getCmp(this.window_id)) Ext.getCmp(this.window_id).hide();
					else 
					{
						var form = this.up('form').getForm();
						if(Ext.getCmp(form.window_id)) Ext.getCmp(form.window_id).hide();
					}
	            }
				
	        }};
	        YAHOO.util.Connect.asyncRequest('POST',url,callback,post);    
			
	    }};  
	    if(!config.dockedItems)config.dockedItems=new Array();	                
		config.dockedItems.push(
	    
	        {dock: 'bottom',xtype: 'toolbar',
	            items:["->",save_btn]
			}
		);
	
		this.callParent(arguments); 
		//.info('Spectrum.forms.person_details created');
	}
	
	
});	}
