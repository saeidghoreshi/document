var grulesform='Spectrum.form.sch_rules';
if(!App.dom.definedExt(grulesform))//{    
Ext.define(grulesform,
{ 
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
 
 	save:function()
	{
		var form = this.getForm();
 
		if (!form.isValid()) 
		{
			 Ext.MessageBox.alert('Incomplete','Enter all required fields');
			 return;
		}		
		 
		Ext.Ajax.request(
		{
			scope:this
			,method:"POST"
			,params:form.getValues()
			,url:'index.php/schedule/post_global_rules/'+App.TOKEN
			,success:function(o)
			{ 
 				this.up('window').hide();
			}
			,failure:App.error.xhr
		});
		 
		
	},
	
	getitems:function(record)
	{
		//var small_wd=200;
		//var wd=310;
		//var lab_wd=200;
		var w=150;
        //console.warn('bad controller reference use loadRecord and disabled a different way');
		var items= [				//min games  var model_id='WizardRules';
			{
				xtype : 'fieldcontainer',   
				fieldLabel:"Min games/team",
				width:'100%', 
				layout:'hbox',  
				defaults: {hideLabel: true },
				items:
				[	
				
			 		 { 
			 			 xtype:'numberfield',
			 			 name:'min',
			 			 id:'field_global_min_games',
			 			 minValue:0,
			 			 fieldLabel:'',
			 			 allowBlank:true,
			 			 disabled:record['min_disabled'],
			 			 value:   record['min']
			 		 },
			    	{
			    		xtype:'button',
			    		iconCls:'',
			    		id:'enforce_min_btn',
			    		enableToggle: true,
			    		text:'Unlimited' ,
			    		pressed:record['min_disabled'],
			    		toggleHandler:function(btn,checked)
						{
							if(checked)
							{
								//Ext.getCmp('enforce_min_btn').setText('Minimum');
								Ext.getCmp('field_global_min_games').setDisabled(true);
								Ext.getCmp('field_global_min_games').setValue('');
							}
							else
							{
								Ext.getCmp('field_global_min_games').setDisabled(false);
								//Ext.getCmp('enforce_min_btn').setText('Unlimited');
							}
						}
					}
				]}
			//max games
			,{
				xtype : 'fieldcontainer',   
				fieldLabel:"Max games/team",
				width:'100%', 
				layout:'hbox',    
				defaults: {hideLabel: true       }
			 , items:
			 [	
				{ 
			 		 xtype:'numberfield',
			 		 name:'max',
			 		 id:'field_global_max_games',
			 		 minValue:0,
			 		 fieldLabel:'',
			 		 allowBlank:true,
			 		disabled:record['max_disabled'],
			 		value:   record['max']
			 	 }
			    ,{
			    	xtype:'button',
			    	iconCls:'',
			    	id:'enforce_max_btn',
			    	enableToggle: true,
			    	text:'Unlimited' ,
			    	pressed:record['max_disabled'],
					toggleHandler:function(btn,checked)
					{
						if(checked)
						{
							Ext.getCmp('field_global_max_games').setDisabled(true);
							Ext.getCmp('field_global_max_games').setValue('');
							//Ext.getCmp('enforce_max_btn').setText('Maximum');
						}
						else
						{
							Ext.getCmp('field_global_max_games').setDisabled(false);
							//Ext.getCmp('enforce_max_btn').setText('Unlimited');
						}
					}
				}
			]
		 }
			//length
			 ,{ 
				xtype:'timefield',
				width:w,
				name : 'len', 
				fieldLabel:"Game Length",
				format:'H:i',
				increment:5, 
				minValue: "0:30", 
				maxValue:"4:00",
				allowBlank:false,
				value:record['len']
			 }
			 //warmup
			 ,{
				xtype:'timefield',
				width:w,
				name : 'warmup', 
				fieldLabel:"Warmup"  ,
				value:"0:00",
				format:'H:i',
				increment:5, 
				minValue: "0:00", 
				maxValue:"2:00",
				allowBlank:false,
				value:record['warmup']
			 }
			 //teardown
			 ,{
				 xtype:'timefield',
				 width:w,
				 name : 'teardown', 
				 fieldLabel:"Teardown",
				 value:"0:00",
				 format:'H:i'  ,
				 increment:5, 
				 minValue: "0:00", 
				 maxValue:"2:00",
				 allowBlank:false,
				 value:record['teardown']
			 }
			 //buffer
			 ,{
				 xtype:'timefield', 
				 width:w,
				 name : 'min_btw', 
				 fieldLabel:"Team Buffer",
				 value:"0:00",
				 format:'H:i',
				 increment:5, 
				 minValue: "0:00", 
				 maxValue:"4:00",
				 allowBlank:true,
				 value:record['min_btw']
			 }
			 //maxbuffer DEPRECIATED
			 ,{
				 xtype:'timefield', 
				 name : 'max_btw', 
				 hidden:true,
				 fieldLabel:"Team Buffer Max",
				 value:"0:00",
				 format:'H:i' ,
				 increment:5, 
				 minValue: "0:00", 
				 maxValue:"4:00",
				 allowBlank:true,
				 value:record['max_btw']
			 }	
				
			 //venue distance
			 ,{
				 xtype:'numberfield'
				 ,name:'venue_distance'
				 ,fieldLabel:'Max Back-to-Back distance'
				 ,minValue:0
				 ,maxValue:99999
				 ,step:10
				 ,value:record['venue_distance']
				 
			 }
			 //checkbox for facility switching
			 ,{
				 xtype:'checkbox'
				 ,name:'facility_lock'
				 ,fieldLabel:'Keep Teams in Same Facility'
				 ,inputValue:'t'//default would be 'on' if we dont say this
				// ,value:o_sch_wizard.rules_global.record['facility_lock']
				 ,checked:record['facility_lock']
			 }
			 //display field about distances
			 ,{
				 xtype:'displayfield'                      //  in between
				 ,value:'<i>Two diamonds back to back can be up to 50 meters apart.</i>'
			 }
					
 
		];
		return items;
	},
	constructor     : function(config)
    {  	
		var model_id='WizardRules';
	 	if(Ext.getCmp(config.id))Ext.getCmp(config.id).destroy();
		config.defaults=      {anchor: '100%',labelWidth:160};
		config.items=this.getitems(config.record);
		config.bottomItems=
		[
			'->',
			{
				iconCls:'disk'
				,text:'Save'
				,scope:this
				,cls:'x-btn-default-small'
				,handler:function(o)
				{
					
					this.save();
				}
			}
		];
 
    	this.callParent(arguments);
	}//end constructor
	
}); 		
 
 