var gclass='Spectrum.form.sch_finalize';
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
 		if(Ext.getCmp(config.id))Ext.getCmp(config.id).destroy();
		
		config.centerAll= true;
 
		config.fieldDefaults={labelAlign: 'left',msgTarget: 'side'};
		config.items=
		[
		    //two hidden fields
		    {
		    	xtype:'hidden'
		    	,name:'schedule_id'
		    	,value:o_sch_wizard.loaded.schedule_id
		    }
		    ,{
		    	xtype:'hidden'
		    	,name:'season_id'
		    	,value:o_sch_wizard.loaded.season_id
		    }
		    //input and display fields
		    ,{
		    	xtype:'textfield'
		    	,name:'schedule_name'
		    	,fieldLabel:'Schedule Name'
		    	,value:o_sch_wizard.loaded.schedule_name
		    }
		    ,{
		    	xtype:'displayfield'
		    	,name:'total_games'
		    	,fieldLabel:'Scheduled games'
		    	,value:o_sch_wizard.loaded.total_games
		    }
			,{
				xtype:'checkbox'
				,fieldLabel:'Published'
				,tooltip:'making it visible right away on the league website'
				,name:'is_published'
				,inputValue:'t'//default was 'on'
				,checked:false
			}
		];//end of items
		config.bottomItems=
		[
			'->',
			{
		    	xtype:'button'
		    	,id:'final_save_btn'
		    	, text:'Save Final Version and Remove from Wizard'
		    	, iconCls:'disk'
		    	,cls:"x-btn-default-small"
		    	,scope:this
		    	,handler:function(o)
		    	{
		    		this.save();
		    	}
		    }
		];//end of bottomitems
		
     	this.callParent(arguments);
	}
	,save:function()
	{
		var form = this.getForm();
		if (!form.isValid()) 
		{
			 Ext.MessageBox.alert('Form Incomplete','Enter all required fields');
			 return;
		}					                       
 
		Ext.Ajax.request(
		{
			scope:this
			,method:'POST'
			,url:'index.php/schedule/post_save_schedule/'+App.TOKEN
			,params:form.getValues()
			,success:function(o)
			{
				var r=o.responseText;
				if(isNaN(r) || r<=0)
				{
					Ext.MessageBox.show({title:"Cannot Save",msg:'Saving problem:'+r,icon:Ext.MessageBox.ERROR,buttons:Ext.MessageBox.OK});
				}
				else
				{
					Ext.MessageBox.show({title:"Success",msg:'Schedule Saved. ',icon:Ext.MessageBox.INFO,buttons:Ext.MessageBox.OK});
					Ext.getCmp('final_save_btn').setDisabled(true);
					
					Ext.getCmp(this.window_id).hide();//hide THIS window
					App.closeActiveTab();//close activetab
				}
			}
			,failure:App.error.xhr
		});
		
	}
});

 