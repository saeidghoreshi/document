var gclass='Spectrum.form.timeslot_rules';
if(!App.dom.definedExt(gclass))//{    
Ext.define(gclass,
{ 
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
 
	constructor     : function(config)
    {  
    	
	 	if(Ext.getCmp(config.id))Ext.getCmp(config.id).destroy();
 		config.centerAll=true;
 		 
		config.items=
		[
		  {
			  xtype:'hidden',
			  name : 'dateset_pk', 
			  fieldLabel:""    ,
			  value:config.record.dateset_pk
		  }
		 ,{
			 xtype:'timefield',
			 name : 'ds_warmup', 
			 fieldLabel:"Warmup"    ,
			 value:config.record.ds_warmup,
			 format:'H:i',
			 increment:5, 
			 minValue: "0:00", 
			 maxValue:"2:00",
			 allowBlank:false
		 }
		 ,{
			 xtype:'timefield',
			 name : 'ds_teardown', 
			 fieldLabel:"Teardown",
			 value:config.record.ds_teardown,
			 format:'H:i',
			 increment:5, 
			 minValue: "0:00", 
			 maxValue: "2:00",
			 allowBlank:false
		 }
		 
		 ,{
			 xtype:'timefield',
			 name : 'ds_min_btw', 
			 fieldLabel:"Team Buffer"    ,
			 value:config.record.ds_min_btw,
			 format:'H:i',
			 increment:5, 
			 minValue:"0:00", 
			 maxValue:"2:00",
			 allowBlank:false
		 }
		 ,{
			 xtype:'timefield',
			 hidden:true,
			 name : 'ds_max_btw', 
			 fieldLabel:"Team Buffer Min",
			 value:config.record.ds_max_btw,
			 format:'H:i',
			 increment:5, 
			 minValue: "0:00", 
			 maxValue: "2:00",
			 allowBlank:false
		 }
		 ,{ 
		    xtype:"numberfield",
		    name : 'min_day', 
		    fieldLabel: 'Min Per Day', 
		    minValue: 0, 
		    value : config.record.min_day,
		    step: 1
		 } 
		 ,{ 
		    xtype:"numberfield",
		    name : 'max_day', 
		    fieldLabel: 'Max Per Day', 
		    minValue: 0, 
		    value : config.record.max_day,
		    step: 1
		 } 
		 ,{ 
		    xtype:"numberfield",
		    name : 'min_slot', 
		    fieldLabel: 'Minimum', 
		    minValue: 0, 
		    value : config.record.min_slot,
		    step: 1
		 } 
		 ,{ 
		    xtype:"numberfield",
		    name : 'max_slot', 
		    fieldLabel: 'Maximum', 
		    minValue: 0, 
		    value : config.record.max_slot,
		    step: 1
		 } 
		 ,{
			 xtype:'checkbox',
			 fieldLabel:'Apply Rules',
			 name:'is_active',
			 inputValue:'t',
			 checked:config.record.is_active
		 }
	  ];
	  config.bottomItems=
		[
			'->',
			{
				iconCls:'disk'
				,text:'Save'
				,scope:this
				,handler:function(o)
				{
					
					this.save();
				}
			}
		]	
		
    	
    	
    	
    	this.callParent(arguments);
	}//end constructor
	,save:function()
	{
 		Ext.Ajax.request(
 		{
			url:'index.php/schedule/post_dateset_rules/'+App.TOKEN
			,params:this.getForm().getValues()
			,scope:this
			,success:function(o)
			{
				var w = this.up('window');
				w.hide();
				w.destroy();
			}
			,failure:App.error.xhr
			
 		});
 
		
	}
	
});