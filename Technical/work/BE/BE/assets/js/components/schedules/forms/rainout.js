
var frain = 'Schedule.form.rainout';
if(!App.dom.definedExt(frain)){
Ext.define(frain,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
       
    constructor     : function(config)
    {   
		Ext.define('search_games', 
		{
			extend: 'Ext.data.Model',
			fields: 
			[
			   {name: 'home_name',      type: 'string'},
			   {name: 'home_score',      type: 'string'},
			   {name: 'away_name',      type: 'string'},
			   {name: 'away_score',      type: 'string'},
			   {name: 'venue_name',      type: 'string'},
			   {name: 'game_date',      type: 'string'},
			   {name: 'start_time',      type: 'string'},
			   {name: 'game_id',  type: 'int'}
			]
		});
  		this.window_id =config.window_id;
		var empty=new Array();
		var sgames_store = Ext.create( 'Ext.data.Store',//ArrayStore
		{
			// destroy the store if the grid is destroyed
			remoteSort:false,
			//pageSize:30,
			autoDestroy: false,
			model:'search_games',
			proxy:false,
			data: empty
		}); 

					 
		var cb = Ext.create('Ext.selection.CheckboxModel');
		 //give it a random id, unless an id is given
		 
		 if(!config.id){ config.id='form_rainout_';}
		 
		 
		 
		 //config= {id:id,title: '',autoHeight: true,resizable:false,bodyPadding: 10,width: '100%',
		 config.bodyPadding=10;
		 config.width='100%';
		 
		 config.defaults= {anchor: '100%'};
		 config.items=
		[
			{xtype:'displayfield',	name:'schedule_name',	fieldLabel:'Schedule',	id:'form_rain_sch_name'	}
			,{xtype:'textfield',hidden:true,	name:'schedule_id',		value:null,id:'form_rain_sch_id'	}
		    ,{xtype : 'fieldcontainer',layout:'hbox',
			    items:
			    [
		    	{	xtype:'datefield',format: 'Y/m/d',	maxValue:new Date(),name:'rainout_search_date' ,id:'form_rain_search_date',fieldLabel:"Search",allowBlank:false,
		    		
		    		listeners:{select: function(combo, value)
					{

						var d=value ? Ext.Date.dateFormat(value, 'Y-m-d') : null;
						
						//.log(d);
						if(d==null)return;
						var s=Ext.getCmp('form_rain_sch_id').getValue();//
						var url='index.php/games/post_search_games/'+App.TOKEN;
						var post="search_date="+d+"&schedule_id="+s;
						YAHOO.util.Connect.asyncRequest('POST',url,{success:function(o)
						{
							var i,g,games=YAHOO.lang.JSON.parse(o.responseText);
							Ext.getCmp('dt-rain-games').store.loadData(games,false);
							if(!games.length)
							{
								Ext.MessageBox.alert({title:'No Games Found',msg:'Try a different date',icon:Ext.MessageBox.INFO});
							}
						}},post);
		    		}}
		    	}

		    	]
			}//end of hbox
			
			,{xtype:'grid',id : 'dt-rain-games',store:sgames_store,height: 220,title:'Games Found',selModel:cb,
				//columnLines: true,
				columns: 
				[
					{text   : 'Home',flex: 1,sortable : true,dataIndex: 'home_name'}
					,{text   : 'Score',width:50,sortable : true,dataIndex: 'home_score'}
					,{text   : 'Away',flex: 1,sortable : true,dataIndex: 'away_name'}
					,{text   : 'Score',width:50,sortable : true,dataIndex: 'away_score'}
					,{text   : 'At',flex: 1,sortable : true,dataIndex: 'venue_name'}
					,{text   : 'On',flex: 1,sortable : true,dataIndex: 'game_date'}
					,{text   : 'Time',flex: 1,sortable : true,dataIndex: 'start_time'}
				]
				,bbar:['Hold "Shift" or "Ctrl" to select multiple games']
			}

			
			//toggle buttons
			,{xtype:'textfield',hidden:true,name:'rainout_type',id:'form_rainout_type'}
			,{xtype : 'fieldcontainer',   fieldLabel:"", layout:'hbox',  width:'100%',flex:1,  defaults: {hideLabel: true},
			items://hbox == horizontal group
			[		
			    /*{xtype:'displayfield',value:'Select one',width:75}
				,*/{xtype:'button',toggleGroup:'rainout_radio',iconCls:'delete',enableToggle: true,text:'Cancel these Games' 
				,toggleHandler:function(btn,checked)
				{
					if(checked)
					{	Ext.getCmp('form_rainout_type').setValue('c');						}
					else
					{	Ext.getCmp('form_rainout_type').setValue(-1);						}
				}}
				,{xtype:'button',toggleGroup:'rainout_radio',iconCls:'award_star_silver_3',enableToggle: true,text:'Tie these games as 0 - 0' 
				,toggleHandler:function(btn,checked)
				{
					if(checked)
					{	Ext.getCmp('form_rainout_type').setValue('t');						}
					else
					{	Ext.getCmp('form_rainout_type').setValue(-1);						}
					
				}}
				,{xtype:'button',toggleGroup:'rainout_radio',iconCls:'clock_red',enableToggle: true,text:'Postpone these games' 
				,toggleHandler:function(btn,checked)
				{
					if(checked)
					{	Ext.getCmp('form_rainout_type').setValue('p');						}
					else
					{	Ext.getCmp('form_rainout_type').setValue(-1);						}
				}}
			]}//end of hbox

		];
		 config.bottomItems=
		[
			'->'
			,{text   : 'Save',id:'btn_save_rainout', scope:this,handler: function() 
			{
				 var form = this.getForm();
				 if (!form.isValid()) 
				 {
					 Ext.MessageBox.alert({title:'Incomplete',msg:'Enter all required fields',icon:Ext.MessageBox.WARNING});
					 return;
				 }					                       
	        	var form_data=new Array();
				Ext.iterate(form.getValues(), function(key, value) 
				{
					if(key!='rainout_search_date' && key !='schedule_name')
					{
						//value = Ext.Date.dateFormat(value, 'Y-m-d') ;
						if(key=='rainout_type')
						if(!value || value==-1)
						{
							Ext.MessageBox.alert('Incomplete','Select a rainout option');
							return;
						}
					    form_data.push(key+"="+value);//c,r,t,p
					}
				}, this);
				var post= form_data.join('&');
				
				var checked = Ext.getCmp('dt-rain-games').getSelectionModel();//.getSelected();
				var records=checked.getSelection();
				var game_ids=new Array();

				for(i in records)
				{
					game_ids.push(records[i].data.game_id);
				}
				if(!game_ids.length)
				{
					Ext.MessageBox.alert('Incomplete','No Games Selected');
					return;
				}
				
				post=post+"&game_ids="+YAHOO.lang.JSON.stringify(game_ids);
				var url='index.php/schedule/post_rainout/'+App.TOKEN;
				var callback={scope:this,failure:App.error.xhr,success:function(o)
				{
					
					var r=o.responseText;

					
					if(isNaN(r)||r<=0)
					{			
						//Ext.MessageBox.show({title:'Could not complete the rainout',msg:r,icon:Ext.MessageBox.WARNING});					
						Ext.MessageBox.alert('Could not complete the rainout',r);					
					}
					else
					{							
						//Ext.MessageBox.show({title:'Success',msg:'Rainout completed successfully',icon:Ext.MessageBox.OK});
						Ext.MessageBox.alert('Success','Rainout completed successfully');
						
						if(this.window_id && Ext.getCmp(this.window_id))  Ext.getCmp(this.window_id).hide();
					}
					
				}};
				
				YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
			}}
		];

         this.callParent(arguments); 
        }
});}