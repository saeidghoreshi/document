
var astfrm='Spectrum.forms.prize.assets';
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
	    if (!form.isValid()) {return;}

		var form_action=1;
        form.submit(
        {
            scope:this,
            url: 'index.php/prize/post_upload_image/'+App.TOKEN,
            waitMsg: 'Uploading file...',
            success: function(form,action)
            {
                var w=Ext.getCmp(this.window_id);
                if(w) 
                    w.hide();//if its in a window, close the window
                else
                {
                    //otherwise , confirm the success and reset form
                    Ext.MessageBox.alert("Upload success."," Confirmation number: "+action.result.asset_id);
                    form.reset();
				}
            }
            ,failure:function(form,action)
            {
                App.error.xhr(action);
               // Ext.MessageBox.alert("Error:",action.result.asset_id);
                //form.reset();
            }
        });
	},
    constructor     : function(config)
    {  
    	this.window_id=config.window_id;
		if(!config.prize_id)config.prize_id=-1;
		var id='c_assetsprzzs_form';//+Math.random();
    	if(!config.id)
    		config.id='assetreateses_form_';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		this.prize_id=config.prize_id;
		config.centerAll=true;
		//config.width=580;
		//config.height= 230;
	    config.fieldDefaults = {labelWidth: 125,msgTarget: 'side',autoFitErrors: false};
	    config.defaults = {anchor: '100%'};
	    config.items =
	    [
			{xtype:'hidden',name:'prize_id',value:config.prize_id}
			,{xtype:'hidden',name:'MAX_FILE_SIZE',value:51200000}//!important
	        ,{
                xtype: 'fileuploadfield',
                id: 'prize_assetimage',
                fieldLabel:'Image',
                name: 'filepath',
                emptyText: '',
                 // fieldLabel: 'Default Image',
                iconCls:'picture',
                buttonText: 'Browse'
            }
            ,{
            	xtype: 'combobox'
            	, width:55
            	,  fieldLabel:'Thumbnail Type'
            	, name:'thumb_type'
            	,value:'crop'
            	,  typeAhead: true
                ,store: 
                [
	                ['crop','Crop image (Zoom in for a square image from the center)'],
	                ['fill','Fill background (Zoom out and make a square image)']
                ]//value is first, display is second
               , allowBlank:false
            }

	    ];
	    if(!config.bottomItems)config.bottomItems=new Array();
	    config.bottomItems.push(
		    '->',
 			{text   : 'Save',scope:this,iconCls:'disk',handler: function() 
	        {
	            this.save();
	        }}        
	    );

        this.callParent(arguments);  
	}

	
	
});}

