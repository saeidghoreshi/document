var frain = 'Schedule.form.timeslot_template';
if(!App.dom.definedExt(frain)){
Ext.define(frain,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
       
    weekdays_array:[['1','Mon'],['2','Tues'],['3','Wed'],['4','Thurs'],['5','Fri'],['6','Sat'],['7','Sun']],
    slot_type:[['1','Single Headers'],['2','Double Headers'],['3','Triple Headers']],
    start_default:"6:00 PM",
    w_type:150,
    w_start:80,
    w_check:350,
    
    start_full:"9:00 AM",
    end_full:"6:00 PM",
    lblStyle:{'font-weight': 'bold','text-align':'right'},
    innerPadding:'0 5 15 0',
    constructor     : function(config)
    {    
    	if(!config.id)config.id='templates_form';
    	this.window_id=config.window_id;
    		//frame: true,
    	config.title= '';
    	config.autoHeight= true;
    	config.resizable=false;
    	config.bodyPadding=10;
    	config.width= '100%'; 
		config.fieldDefaults= {labelAlign: 'left',msgTarget: 'side'};
		config.defaults=      {anchor: '100%'};
				
		config.items=			//form on thelefft
		[
			{//section for titles
				xtype : 'fieldcontainer',   
				fieldLabel:"",
				width:'100%', 
				layout:'hbox',    
				defaults: {   anchor:'100%',       hideLabel: true     ,fieldStyle:this.lblStyle },
				 
				items:
				[
					{
						xtype:'displayfield',
						value:'Type',
						width:this.w_type
					}
					,{
						xtype:'displayfield',
						padding:this.innerPadding,
						value:"Start",
						width:this.w_start
					}
					,{
						xtype:'displayfield',
						value:"Sun",
						width:(this.w_check/7)
					}
					,{
						xtype:'displayfield',
						value:"Mon",
						width:(this.w_check/7)
					}
					,{
						xtype:'displayfield',
						value:"Tues",
						width:(this.w_check/7)
					}
					,{
						xtype:'displayfield',
						value:"Wed",
						width:(this.w_check/7)
					}
					,{
						xtype:'displayfield',
						value:"Thurs",
						width:(this.w_check/7)
					}
					,{
						xtype:'displayfield',
						value:"Fri",
						width:(this.w_check/7)-3//compensate for 'thurs' being such a long name'
					}
					,{
						xtype:'displayfield',
						value:"Sat",
						width:(this.w_check/7)
					}
				]
				
			}
			,{
				xtype : 'fieldcontainer',   
				fieldLabel:"",
				width:'100%', 
				layout:'hbox',    
				defaults: {          hideLabel: true       },
				items:
				[
				
					//{ xtype:'numberfield',fieldLabel:"" ,name:'single_headers',minValue:0,value:1,allowBlank:true}
					{
						xtype:'combobox',
						fieldLabel:"",
						name:'first_slot_type',
						value:'1',
						store:  this.slot_type,
						width:this.w_type,
						typeAhead: true, 
						allowBlank:true
					}
					,{
						padding:this.innerPadding,
						xtype:'timefield',
						width:this.w_start,
						name : 'first_slot_start', 
						fieldLabel:"",
				 		increment:5, 
				 		minValue: "0:30 AM", 
				 		maxValue:"11:30 PM",
				 		allowBlank:false,
				 		value:this.start_default
				 	}
					,{
					    xtype: 'checkboxgroup',

					   width:this.w_check,
		                hideLabel:true,
		                labelSeparator:'',
		                labelAlign:'top',
		                defaults:{inputValue:'t'},
					    items: 
					    [
					        {/* boxLabel : 'Sun', boxLabelAlign:'before' , hideLabel:true,*/name: 'first_u' },
					        {/* boxLabel : 'Mon', boxLabelAlign:'before',*/ name: 'first_m' },
					        {/* boxLabel : 'Tues',boxLabelAlign:'before',*/ name: 'first_t' },
					        {/* boxLabel : 'Wed', boxLabelAlign:'before',*/ name: 'first_w' },
					        {/* boxLabel : 'Thur',boxLabelAlign:'before',*/ name: 'first_r' },
					        {/* boxLabel : 'Fri', boxLabelAlign:'before',*/ name: 'first_f' },
					        {/* boxLabel : 'Sat', boxLabelAlign:'before', */ name: 'first_s' }
					    ]
					}
 	 	
				]
			}
			,{
				xtype : 'fieldcontainer',   
				fieldLabel:"",
				width:'100%', 
				layout:'hbox',    
				defaults: {          hideLabel: true       },
				items:
				[
				
					//{ xtype:'numberfield',fieldLabel:"" ,name:'single_headers',minValue:0,value:1,allowBlank:true}
					{
						xtype:'combobox',fieldLabel:"",name:'second_slot_type',value:'2',
						store:  this.slot_type
						,width:this.w_type
						,typeAhead: true, allowBlank:true
					}
					,{
						padding:this.innerPadding,
						xtype:'timefield',width:this.w_start,
						name : 'second_slot_start', fieldLabel:""
				 		,increment:5, minValue: "0:30 AM", maxValue:"11:30 PM",allowBlank:false
				 		,value:this.start_default
				 	}
					,{
					    xtype: 'checkboxgroup',

					   width:this.w_check,
		                hideLabel:true,
		                labelSeparator:'',
		                labelAlign:'top',
		                defaults:{inputValue:'t'},
					    items: 
					    [
					        {/* boxLabel : 'Sun', boxLabelAlign:'before' , hideLabel:true,*/name: 'second_u' },
					        {/* boxLabel : 'Mon', boxLabelAlign:'before',*/ name: 'second_m' },
					        {/* boxLabel : 'Tues',boxLabelAlign:'before',*/ name: 'second_t' },
					        {/* boxLabel : 'Wed', boxLabelAlign:'before',*/ name: 'second_w' },
					        {/* boxLabel : 'Thur',boxLabelAlign:'before',*/ name: 'second_r' },
					        {/* boxLabel : 'Fri', boxLabelAlign:'before',*/ name: 'second_f' },
					        {/* boxLabel : 'Sat', boxLabelAlign:'before', */ name: 'second_s' }
					    ]
					}
 	 	
				]
			}
			,{
				xtype : 'fieldcontainer',   
				fieldLabel:"",
				width:'100%', 
				layout:'hbox',    
				defaults: {          hideLabel: true       },
				items:
				[
				
					//{ xtype:'numberfield',fieldLabel:"" ,name:'single_headers',minValue:0,value:1,allowBlank:true}
					{
						xtype:'combobox',
						fieldLabel:"",
						name:'third_slot_type',
						value:'3',
						store:  this.slot_type
						,width:this.w_type
						,typeAhead: true, 
						allowBlank:true
					}
					,{
						padding:this.innerPadding,
						xtype:'timefield',
						width:this.w_start,
						name : 'third_slot_start', 
						fieldLabel:""
				 		,increment:5, 
				 		minValue: "0:30 AM", 
				 		maxValue:"11:30 PM",
				 		allowBlank:false
				 		,value:this.start_default
				 	}
					,{
					    xtype: 'checkboxgroup',

					   width:this.w_check,
		                hideLabel:true,
		                labelSeparator:'',
		                labelAlign:'top',
		                defaults:{inputValue:'t'},
					    items: 
					    [
					        {/* boxLabel : 'Sun', boxLabelAlign:'before' , hideLabel:true,*/name: 'third_u' },
					        {/* boxLabel : 'Mon', boxLabelAlign:'before',*/ name: 'third_m' },
					        {/* boxLabel : 'Tues',boxLabelAlign:'before',*/ name: 'third_t' },
					        {/* boxLabel : 'Wed', boxLabelAlign:'before',*/ name: 'third_w' },
					        {/* boxLabel : 'Thur',boxLabelAlign:'before',*/ name: 'third_r' },
					        {/* boxLabel : 'Fri', boxLabelAlign:'before',*/ name: 'third_f' },
					        {/* boxLabel : 'Sat', boxLabelAlign:'before', */ name: 'third_s' }
					    ]
					}
 	 	
				]
			}
			,{
				xtype : 'fieldcontainer',   
				fieldLabel:"",
				width:'100%', 
				labelAlign : 'left',
				layout:'hbox',    
				//defaults: {          hideLabel: true       },
				items:
				[
			
				//{ xtype:'numberfield',fieldLabel:'',name:'double_headers',minValue:0,value:0,allowBlank:true}
					{ 
						xtype:'numberfield',
						fieldLabel:'All Day',
						 labelAlign : 'left',
						name:'full_count',
						minValue:0,
						value:0,
						width:this.w_type,
						labelWidth:(this.w_type/2),//should be less than full widht, is subset
						allowBlank:true
					}
 					,{
						xtype:'timefield',
						width:this.w_start,
						name : 'full_slot_start', 
						padding:this.innerPadding,
						fieldLabel:"",
				 		increment:5,
				 		 minValue: "0:30 AM", 
				 		 maxValue:"11:30 PM",
				 		 allowBlank:false,
				 		value:this.start_full
				 	}
				 	,{
						xtype:'timefield',
						width:this.w_start,
						name : 'full_slot_end', 
						fieldLabel:"",
						//labelWidth:(this.w_type/3),//should be less than full widht, is subset
						// labelAlign : 'right',
						labelStyle:this.lblStyle,
				 		increment:5,
				 		 minValue: "0:30 AM", 
				 		 maxValue:"11:30 PM",
				 		 allowBlank:false,
				 		value:this.end_full
				 	}
				]
			}
		 
		];
		config.bottomItems=
		[
			'->',
			{
				text   : 'Create'
				,id:'template_save'
				,iconCls:'disk'
				,cls:'x-btn-default-small'
				,scope:this
				,handler: function() 
				{
					
					this.save();
				}
			}
		];
 
         this.callParent(arguments); 
	}
	,save:function()
	{
		var form = this.getForm();
		Ext.Ajax.request({
			params:form.getValues()
			,url:'index.php/schedule/post_dateset_templates/'+App.TOKEN
			,scope:this
			,failure:App.error.xhr
			,success:function(o)
			{
				//var r=o.responseText;
				 
				Ext.getCmp(this.window_id).hide();	
				//if(o_sch_wizard&&o_sch_wizard.timeslots)o_sch_wizard.timeslots.get();
			}
	
		});
 
		
	}
	
	
});}