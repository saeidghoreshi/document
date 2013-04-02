if(!App.dom.definedExt('Spectrum.btn_menu')){
Ext.define('Spectrum.btn_menu',
{
	//created to handle http://stackoverflow.com/questions/4254306/extjs-how-to-define-a-working-layout-for-a-custom-menu/8493939#8493939
	//and apply the configs across all components
	extend: 'Ext.menu.Menu', 
	alias:'widget.btn_menu', //now we can use  { xtype:'btn_menu'} anywhere
    initComponent: function(config) {this.callParent(arguments);},
    constructor     : function(config) 
    {     	
    	if(!config.item_height) config.item_height=30;//currently there is no reason to override this, so do not even apss it in
		config.forceLayout= true ;
		config.autoHeight=true;
		config.autoScroll =true;
    	
    	for(i in config.items)if(config.items[i])
    	{
			if(!config.items[i].height) config.items[i].height=config.item_height;
			if(!config.items[i].anchor) config.items[i].anchor='100%';
    	}
    	    
    	if(!config.listeners) config.listeners = {};
  
    	this.callParent(arguments);
	}
}
);}