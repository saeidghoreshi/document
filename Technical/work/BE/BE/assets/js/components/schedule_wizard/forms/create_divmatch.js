var gclass='Spectrum.form.div_match';
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
    	config.layout= 'column' ;
 
 		config.width= '100%';
				  // Specifies that the items will now be arranged in columns
		config.fieldDefaults={labelAlign: 'left',msgTarget: 'side'};
 		config.defaults=     {anchor: '100%'};
 		
 		config.items=		//form on thelefft
		[
			{
				columnWidth: 0.4
				,margin: '0 0 0 4'
				,xtype: 'fieldcontainer'
				,title:'Details'
				,defaults: {labelWidth: 60}
			    ,items: 
			    [
			    //hidden data items
			        
					{  
						//xtype:'textfield'
						xtype:'hidden'
						//,hidden:true
						,value:0
						,	name:'first_div_id'
						,	id:'form_first_div_id'	
					}
					,{ 
						xtype:'hidden'
						//xtype:'textfield'
						//,hidden:true
						,value:0
						,	name:'second_div_id'
						,	id:'form_second_div_id'	
					}
					,{ 
						xtype:'numberfield'
						,value:1
						,	name:'match_rounds'
						,fieldLabel:'Rounds'	
						,minValue: 0
						,step: 1
						,allowBlank:true
					}
					,{
						xtype: 'combobox'
						, fieldLabel:"Enforce Rounds"
						,value:'t'
						,  name:'enforce_rounds'
						, typeAhead: true
						,  store: [['t','Yes'],['f','No']]
						,allowBlank:true
					}//
					,{
						xtype: 'combobox',
						 fieldLabel:"Enforce Dates"
						 , value:'f'
						 ,  name:'enforce_dates'
						 , typeAhead: true
						 ,  store: [['t','Yes'],['f','No']]
						 ,allowBlank:true
					} 
				]
			}
		
		,{
			columnWidth: 0.3
			,xtype: 'gridpanel'
			,store: []
			,title:''
			,id:'first_div_grid',
			height:300,columns: 
		    [
		        {text   : 'Division',flex: 1,sortable : true,dataIndex: 'division_name'}
		        ,{text   : '',hidden : true,dataIndex: 'division_id'}
			]
		}
		,{
			columnWidth: 0.3
			,xtype: 'gridpanel'
			,store: []
			,title:''
			,id:'second_div_grid'
			,height:300
			,columns: 
		    [
		        {text   : 'vs. Division',flex: 1,sortable : true,dataIndex: 'division_name'}
		        ,{text   : '',hidden : true,dataIndex: 'division_id'}

		     ]   
		}//end of grid veritcal panel
		];
 		config.bottomItems=
		[
			'->',
			{
				text   : 'Save'
				,id:'match_save'
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
     	this.get_divisions();
	}//end constructor
	
	,get_divisions:function()
	{
		var post={};
		console.warn('controller');
		post.season_id=o_sch_wizard.seasons.season_id;
		Ext.Ajax.request(
		{
			url:'index.php/divisions/json_sorted_divisions/'+App.TOKEN
			,method:"POST"
			,params:post
			,success:function(o)
			{
				var d=Ext.JSON.decode(o.responseText);
				Ext.getCmp('first_div_grid' ).store.loadData(d,false);
				Ext.getCmp('first_div_grid' ).on('selectionchange',function(sm,records)
				{
					if(!records.length){return;}
					Ext.getCmp('form_first_div_id').setValue(records[0].get('division_id'));
				});
				Ext.getCmp('second_div_grid').store.loadData(d,false);
				Ext.getCmp('second_div_grid').on('selectionchange',function(sm,records)
				{
					if(!records.length){return;}
					Ext.getCmp('form_second_div_id').setValue(records[0].get('division_id'));
				});
			}
			,failure:App.error.xhr
		});
	}
	,save:function()
	{
 
		Ext.Ajax.request(
		{
			scope:this,success:function(o)
			{
				var r=o.responseText;
	 
				if(isNaN(r) || r <= 0 )
				{
					Ext.MessageBox.alert('Problem Saving:',r);
				}
				else
				{ 
					if(this.window_id)Ext.getCmp(this.window_id).hide();
 
				}
			}
			,failure:App.error.xhr
			,url:"index.php/schedule/post_save_match/"+App.TOKEN
			,params:this.getForm().getValues()
		});
 
		
	}
});   	

 