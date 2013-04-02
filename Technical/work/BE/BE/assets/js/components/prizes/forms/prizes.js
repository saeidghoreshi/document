

var prfrm='Spectrum.forms.prizes';
if(!App.dom.definedExt(prfrm)){
Ext.define(prfrm,
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

        form.submit(
        {
            url: 'index.php/prize/post_prizedetails/'+App.TOKEN,
 			scope:this,
            success: function(form,action)
			{
                var w=Ext.getCmp(this.window_id);
                if(w) w.hide();
                else  
                {
                    Ext.MessageBox.alert('Result',action.result.uploaded);
                    form.reset();
				}
            }
            ,failure:function(form,action)
            {
				App.error.xhr(action.result);
                //Ext.MessageBox.alert('Prize may not have been saved',action.result.uploaded);
               // form.reset();
				
            }
        });

	},
    constructor     : function(config)
    {  
    	this.window_id=config.window_id;/*
    	var categories=[];
    	if(config.categories){categories=config.categories;}*/

		var id='c_prizes_form';//
    	if(!config.id){config.id=id};

    	if(Ext.getCmp(config.id)){ Ext.getCmp(config.id).destroy(); } 
		//config.bodyStyle   = 'padding: 0px; background-color: #DFE8F6';
		
	    config.fieldDefaults={labelWidth: 125,msgTarget: 'side',autoFitErrors: false};
	    config.centerAll=true;
	    //config.defaults= {anchor: '100%'};
	    var c_id=-1;
	    if(config.record) c_id=config.record.get('category_id');
	    config.items=
	    [
		    {xtype: 'hidden',name : 'prize_id',value:-1}
		    //,{xtype: 'hidden',id:'hid_category_id',name : 'category_id',value:-1}
		    //,{xtype: 'button',name:'category_name',flex : 1,text:'Select Category',id:"p_combo_category",menu:[]}
		    
		    ,{
                xtype: 'scombo',
                id:'p_combo_category',
                fieldLabel: 'Category',
                //store: categories,
                //				editable:false,
                //value:category_id,//this is error: shows 'not found' text
               // forceSelection :true,
               url:'index.php/prize/json_getcategories/'+App.TOKEN,
               extraParams:{combobox:1},//to flag as avoiding root
               /*
                store:Ext.create('Ext.data.Store', 
                {
                    fields: ['category_id', 'category_name'],
                    data :categories // 
                }),*/
                valueField: 'category_id',
                //hiddenName:'category_id',
                displayField: 'category_name',
                name: 'category_id',
                //triggerAction: 'all', // without this the combo won't load from the store, despite the autoload!  
              // typeAhead: true,         
               // queryMode: 'local',
               value:c_id,
                valueNotFoundText: 'Select a category...',
                emptyText: 'Select a category...'
            }
	        ,{
	        	xtype: 'textfield'
	        	//,flex : 1
	        	,name : 'name'
	        	,   fieldLabel: 'Name'
	        	,value:''
	        	,allowBlank: false
	        }
	        ,{
	        	xtype: 'textareafield'
	        	//,flex : 1
	        	,name : 'description'
	        	,   fieldLabel: 'Description'
	        	,value:''
	        	,allowBlank: true
	        }
	        ,{
	        	xtype: 'textfield'
	        	//,flex : 1
	        	,name : 'sku'
	        	,   fieldLabel: 'SKU'
	        	,value:''
	        	,allowBlank: true
	        }
	        ,{
	        	xtype: 'textfield'
	        	//,flex : 1
	        	,name : 'upc'
	        	,   fieldLabel: 'UPC',value:'',allowBlank: true}
	        
	        ,{
                xtype: 'fileuploadfield',
                id: 'prize_image',
                name: 'prize_image',
                emptyText: 'Select an image to upload',
                fieldLabel: 'Default Image',
                iconCls:'picture',
                buttonText: 'Browse'
            }
	    ]
	     if(!config.bottomItems)config.bottomItems=new Array();
	    config.bottomItems.push(
	    	'->',
 			{text   : 'Save',scope:this,iconCls:'disk',handler: function() 
			{
	        	this.save();
			}
	        }        
	    );

        this.callParent(arguments); 
		
		if(config.record){this.loadRecord(config.record);}
		
	}
	
	/**
	* override the loadRecord method such taht it waits for the combo boxes to load
	
	
	,loadRec:function(args)
	{
		console.log(args);
		this.on('render',function()
		{
			//the combobox does not exist until render
			Ext.getCmp('p_combo_category').getStore().on('load',function()
			{
				//when data is loaded
				console.log(this);
				this.loadRecord(args);
				this.render();
			},this);
			
		},this);
		
		
	}*/

});}

