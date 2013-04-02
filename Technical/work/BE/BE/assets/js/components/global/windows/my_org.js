var lgw="Spectrum.windows.my_org";
if(!App.dom.definedExt(lgw)){
Ext.define(lgw,
{
	extend: 'Ext.spectrumwindows', 
	initComponent: function(config) {this.callParent(arguments);},
    constructor     : function(config)
    {  
     	if(!config.id) config.id='myorg_form_window';//+Math.random();
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		if(!config.title) config.title= 'My Organization';
		config.width = 382 + 200;//perfect width
		if(!config.height)config.height= 360;
		//config.items passed automatically
		//layout goes here
		config.layout='accordion';	
		 config.layoutConfig= 
		 {
	        // layout-specific configs go here
	        titleCollapse: true,
	        hideCollapseTool:true, 
	        animate: true
	    },
		
		config.dockedItems =
		{
			dock: 'bottom'
			,xtype: 'toolbar'
			,items:
			[
				'->'
				,{
					text: 'Save'
					,iconCls: "disk"
					,cls:'x-btn-default-small'
					,width:70
					,scope:this
					,handler: function()
					{
  					
            			for(i in App.dom.w_myOrg.items.items)
            			{ 
							var frm = App.dom.w_myOrg.items.items[i];
							if(frm && frm.url)//error checking. IE and check missing url
							frm.submit(
							//Ext.Ajax.request(
							{
								url:frm.url
								//,params:frm.getValues()
								,scope:this
								,failure:function(f,o){App.error.xhr(o);}
								,success:function(f,o)
								{ 
									//if(App.dom.w_myOrg)App.dom.w_myOrg.hide();
									//console.log(o); 
									 this.hide();
								}
							});
            			}

					}
				}
			]
		};//end of docked items
 
		this.callParent(arguments);    	
	}   
});}	
 
					
 