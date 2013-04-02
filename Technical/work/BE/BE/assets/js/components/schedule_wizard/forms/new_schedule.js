var gclass='Spectrum.form.new_schedule';
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
		var fixed=150;
    	config.force_border=true;
		
		config.width= '100%';
		config.height=App.MAX_GRID_HEIGHT;
		config.fieldDefaults= {labelAlign: 'left',msgTarget: 'side'};
		config.defaults=      {anchor: '100%',labelWidth:160};
		var end_items=[];
		if(config.items)
		{
			end_items=config.items;
			config.items=null;
		}
		config.items=		//form on thelefft
		[
			{ 
				xtype:'textfield',
				name:'schedule_name',
				id:'start_form_schedule_name',
				fieldLabel:"Schedule Name",
				emptyText:"My Schedule",
				width:fixed,
				labelWidth:170,
				allowBlank:true
			}
			//,{xtype:'textfield',name:'schedule_id'  ,id:'start_form_schedule_id'  ,fieldLabel:"",hidden:true} 
			
			,{
				xtype: 'scombo'// spectrum extension of 'combobox' 
				,id:'start_form_season_id'
				,name : 'season_id'
				//,readOnly:true//test:!
				,fieldLabel: 'Season'
				,allowBlank: false
				,labelWidth:170
				
				//the store is created for us using url						 
				,url:'index.php/season/json_active_league_seasons/'+App.TOKEN
				,extraParams:{combobox:1}//to flag as avoiding root

				,valueField  :'season_id'
    			,displayField:'season_name_dates'
			 }
 			 ,end_items
	 
  
		];
		config.bottomItems=
	    [	
	         '->',             
	         { 
	             xtype:'button'
	            , text:'Save and Create'
	             ,cls:'x-btn-default-small'
	             ,iconCls:'disk'
	             ,id:"btn_start_form_ready"
	             ,disabled:true
	             ,scope:this
	            , handler:function(o,e)
				{
					this.save();
				}
				
			}
		];
		this.fireSuccess=config.fireSuccess;
     	this.callParent(arguments);
 
	}//end constructor
	
	
	,save:function()
	{
		var form = this.getForm();
 
		var values=form.getValues();
		 
		if(!values.season_id<0)
		{
			Ext.MessageBox.alert('Incomplete','Enter all required information, including selecting a Season');
			return;
		}
 
		Ext.Ajax.request(
		{
			scope:this
			,url:'index.php/schedule/post_create_incomplete/'+App.TOKEN
			,params:values
			,success:function(o)
			{
				if(Ext.getCmp('btn_start_form_season'))Ext.getCmp('btn_start_form_season').setDisabled(true);//canot change season anymore its saved nwo
				var  r=o.responseText;
				
				//the controller passes this function to simulate an event fired on success
				if(typeof this.fireSuccess=='function')this.fireSuccess(r);
				
			}
			,failure:App.error.xhr
		});
		
	}
}); 
 