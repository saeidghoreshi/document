

var fcat='Spectrum.forms.categories';
if(!App.dom.definedExt(fcat)){
Ext.define(fcat,
{
    extend: 'Ext.spectrumforms',  
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config)
    {  
    	this.window_id=config.window_id;
		var id='c_ctg_form';//+Math.random();
    	if(!config.id){config.id=id};

    	if(Ext.getCmp(config.id)){ Ext.getCmp(config.id).destroy(); } 
		 //config=
		//{  
		// id:id,title: '',autoHeight: true,resizable:false,bodyPadding: 10,
		config.width= 550;//,//
		height:210,
		config.fieldDefaults= {labelWidth: 125,msgTarget: 'side',autoFitErrors: false};

		config.defaults= {anchor: '100%'};
		config.items=
	    [
		    {
		    	xtype: 'hidden',
		    	name : 'category_id',
		    	value:-1
		    }
		    //,{xtype: 'textfield',name : 'parent_rank_type_id',hidden:true,value:-1}
	        ,{
	        	xtype: 'textfield',
	        	flex : 1,
	        	name : 'category_name',   
	        	fieldLabel: 'Name',
	        	emptyText:'New Category',
	        	value:'',
	        	allowBlank: false
	        }
	        ,{
	        	xtype: 'textareafield',
	        	flex : 1,
	        	name : 'category_desc',
	        	emptyText:'Describe the kinds of items that will be in this category'  , 
	        	fieldLabel: 'Description',
	        	value:'',
	        	allowBlank: true
	        }
	    ]
	    config.bottomItems=
	    [
	    	'->',
 			{
 				xtype:'button',
 				text   : 'Save',
 				scope:this,
 				iconCls:'disk',
 				handler: function() 
		        {
		            // .up is magic
		           // var form = this.up('form').getForm();
		            var form = this.getForm();
		            if (!form.isValid()) {return;}
		                                     
		            var data=[];
		            //post all data
		            var valid=true;
		            Ext.iterate(form.getValues(), function(key, value) 
		            {
						if(key=='category_name' && value.split(' ').join('')==''){valid=false;}
						value=escape(value);
		                data.push(key+"="+value);
		            });
		            if(!valid)
		            {
		                Ext.MessageBox.alert('Could not create','Enter a valid Name');
		                return;
		            }
		            var post = data.join("&");
		            
		            var url='index.php/prize/post_new_category/'+App.TOKEN;
		            var callback={scope:this,failure:App.error.xhr,success:function(o)
		            {
		                var r=o.responseText;
						if(isNaN(r)||r<=0)
						{
							Ext.MessageBox.show({title:'Could not create :',msg:r,icon:Ext.MessageBox.ERROR,buttons:Ext.MessageBox.OK});
						}
						else
						{

							if(Ext.getCmp(this.window_id))
							{
								
						 		Ext.getCmp(this.window_id).hide();//hide when done
								
							}
							else
							{//if no valid pointer/id to window:
								Ext.MessageBox.show({title:'Success',msg:
										'Create more, or close the window',icon:Ext.MessageBox.INFO,buttons:Ext.MessageBox.OK});
								this.getForm().reset();
								
							}
						}
		            }};

		            YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
	   
		        }
		    }        
	    ];

        this.callParent(arguments);    	    	
	}
	
	
	
});

}
