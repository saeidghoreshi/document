  // use function post_module_asset 
  
  
var astfrm='Spectrum.forms.websites.module_assets';
if(!App.dom.definedExt(astfrm)){
Ext.define(astfrm,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
	save:function()
	{
		var form = this.getForm();
	  //  if (!form.isValid()) {return;}

 
        form.submit(
        {
            scope:this,
            url: 'index.php/websites/post_module_asset/'+App.TOKEN,
          //  waitMsg: 'Uploading file...',
            success: function(form,action)
            {
                var w=Ext.getCmp(this.window_id);
                if(w) 
                    w.hide();//if its in a window, close the window
                else
                {
                    //otherwise , confirm the success and reset form
                    Ext.MessageBox.alert("Upload success."," Confirmation number: ");
                    form.reset();
				}
            }
            ,failure:function(form,action)
            {
                App.error.xhr(action.response);
               // Ext.MessageBox.alert("Error:",action.result.asset_id);
                //form.reset();
            }
        });
	},
    constructor     : function(config)
    {  
    	this.window_id=config.window_id;
		 
 
    	if(!config.id)
    		config.id='c_assetwbsites_form';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
 
		
		
		config.centerAll=true;
 
	    config.fieldDefaults = {labelWidth: 125,msgTarget: 'side',autoFitErrors: false};
	    config.defaults = {anchor: '100%'};
	    config.items =
	    [
			{xtype:'hidden',name:'w_m_id',value:config.w_m_id}//hidden key to link the asset to the web_model
			,{xtype:'hidden',name:'MAX_FILE_SIZE',value:51200000}//!important
	        ,{
                xtype: 'fileuploadfield',
               // id: 'prize_assetimage',
                fieldLabel:'Image',
                name: 'filepath',
                emptyText: '',
                 // fieldLabel: 'Default Image',
                iconCls:'picture',
                buttonText: 'Browse'
            }

            ,{
            	xtype: 'textfield'
            	,name:'url'
            	,  fieldLabel:'Link (URL)'
               , allowBlank:true
              , emptyText:'http://'
            }

	    ];
	    if(!config.bottomItems)config.bottomItems=new Array();
	    config.bottomItems.push(
		    '->',
 			{
 				text   : 'Save'
 				,scope:this
 				,iconCls:'disk'
 				,handler: function() 
		        {
		            this.save();
		        }
		    }        
	    );

        this.callParent(arguments);  
	}

	
	
});}

