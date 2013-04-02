
var extending = "Ext.form.field.ComboBox";
var my_class = "Spectrumwidget.combobox";
var my_xtype = 'scombo';
var alias='widget.'+my_xtype;
// widget is an internal namespace in Ext for extensions
//using alias:  now we can use xtype:'scombo'
//never put the 'widget.' inside the xtype, you will get 'namespace undefined' 

if(  !App.dom.definedExt(my_class))//dont define it twice
{
Ext.define(my_class,
{                            
    extend  :extending, 
    alias:alias,
    //alternateClassName:['widget.scombobox'],
    initComponent: function(config) {this.callParent(arguments);}, 
    
    constructor     : function(config)
    {    
		//if(typeof(config.editable) == 'undefined')
		config.editable =false;
		//config.readOnly=true;
		//config.autoScroll = true;
		config.typeAhead = true;
		//config.typeAheadDelay = 2;
		if(!config.fields)config.fields = new Array();
		config.fields.push(config.valueField,config.displayField);
		if(!config.extraParams) config.extraParams = {};//default to emtpy
    	if(!config.store)
    	{
    		config.store = Ext.create( 'Ext.data.Store',
    		{
    			autoDestroy:false
    			,autoSync :false
    			,autoLoad :true
    			,remoteSort:true 
    			,model:config.model//if no model is given (null/undefined)thats ok, this throws no errors
			    ,fields:config.fields
				,proxy: 
				{   
            		type: 'rest',
            		url:config.url, 
					extraParams:config.extraParams  
				}    
			});
		}
    	
    	
    	this.callParent(arguments); 
	}
    
    
}

);
	
}
	