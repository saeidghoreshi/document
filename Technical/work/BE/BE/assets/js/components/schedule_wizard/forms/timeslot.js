var gclass='Spectrum.form.timeslot';
if(!App.dom.definedExt(gclass))//{    
Ext.define(gclass,
{ 
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    game_length:90,
	constructor     : function(config)
    {  
 		this.window_id=config.window_id;
 		this.game_length=config.game_length;
 		if(Ext.getCmp(config.id))Ext.getCmp(config.id).destroy();
 		config.title= '';
		config.autoHeight= true;
			 //	 ,resizable:false
		config.bodyPadding=10;
		config.width= '100%';
			   // layout: 'column',    // Specifies that the items will now be arranged in columns
		config.fieldDefaults= {labelAlign: 'left',msgTarget: 'side'};
		config.defaults=      {anchor: '100%'};
		config.items=		//form on thelefft
		[
		
			{  
				xtype:'textfield'
				,hidden:true
				,value:-1
				,	name:'dateset_pk'	
			}
			,{  
				xtype:'textfield'
				,hidden:true
				,value:"FF0000"
				,	name:'hexcol'
				,id:'hexcol_form_field'	
			}
			,{ 
				xtype:'textfield'
				,fieldLabel:'Name'
				,name:'set_name'	
				,allowBlank:true
			}
					//,{ xtype:'textfield',fieldLabel:'Name',name:'dateset_name'	}					
			,{ 
				xtype:'timefield'
				,name:'start_time'
				,value:''
				,increment:5
				,allowBlank:false
				,fieldLabel:"Start",
				listeners:
				{
					blur:function(o)
					{

						//var game_length = o_sch_wizard.rules_global.record['len_minutes'];
						var glen=this.up('form').game_length ;
						 //.log( glen);
						var oStart = o.value;
						oStart.setMinutes( oStart.getMinutes()+glen );//TODO: use actual game time saved in rules
						 
						var minute=oStart.getMinutes();
						var hour = oStart.getHours();
						var ap = "AM";
						if (hour   > 11) { ap = "PM";             }
						if (hour   > 12) { hour = hour - 12;      }
						if (hour   == 0) { hour = 12;             }
						var timeString = hour +
							            ':' +
							            minute +
							            //':' +
							           // second +
							            " " +
							            ap;
							            
							            //.log(timeString);
 
						Ext.getCmp('schwiz_field_end_time').setValue(timeString);
					}
				}
			}
			,{ 
				xtype:'timefield'
				,name:'end_time'  
				,id:'schwiz_field_end_time'
				,value:''
				,increment:5
				,allowBlank:false
				,fieldLabel:"End"
			}
 
			
		
		];
				
		config.bottomItems=
		[
			'->',
			{
				text   : 'Save'
				,id:'timeslot_save'
				,iconCls:'disk'
				,scope:this
				,handler: function() 
				{
 					this.save();					                     
				}
			}
		];
				 
    	this.callParent(arguments);
	}//end constructor
	
	,save:function()
	{
		var form = this.getForm();
		 if (!form.isValid()) return;
		 var form_data=new Array();
		 Ext.Ajax.request(
		 {
			 url:'index.php/schedule/post_dateset_info/'+App.TOKEN
			 ,params:form.getValues()
			 ,scope:this
			 ,success:function(o)
			{
				var r=o.responseText;
				 
				//Ext.getCmp('timeslot_window').hide();	
				if(this.window_id)Ext.getCmp(this.window_id).hide();
				else
					this.up('window').hide();
				//
			}
			,failure:App.error.xhr
		 });
 
	}
	
});