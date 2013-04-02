
var calc_model_id='Calculated';//+Math.random();
if(!App.dom.definedExt(calc_model_id))
{Ext.define( calc_model_id,
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'team_name',      type: 'string'},
	   {name: 'team_id',      type: 'string'},
	   {name: 'division_id',      type: 'string'},
	   {name: 'division_name',      type: 'string'},
	   {name: 'rank',      type: 'string'},
	   {name: 'GP',      type: 'string'},
	   {name: 'W',      type: 'string'},
	   {name: 'L',      type: 'string'},
	   {name: 'T',      type: 'string'},
	   {name: 'PTS',  type: 'string'},
	   {name: 'GB',  type: 'string'},
	   {name: 'RF',  type: 'string'},
	   {name: 'RA',  type: 'string'},
	   {name: 'RD',  type: 'string'},
	   {name: 'PCT',  type: 'string'},//,
	   {name: 'rank',  type: 'string'}
	]
});}
//else{	console.info('Model Exists: ',calc_model_id);}

var statcalGrid='Spectrum.grids.calculated';
if(!App.dom.definedExt(statcalGrid)){
Ext.define(statcalGrid,
{
	extend: 'Ext.grid.Panel', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    rank_type_id:null,
    season_id:null,
    division_id:-1,
    refresh:function()
    {
    	var post='rank_type_id='+this.rank_type_id+'&season_id='+this.season_id+"&division_id="+this.division_id;
    	if(this.division_id==-1)
       		{var url='index.php/statistics/json_calculate/'+App.TOKEN;}
       	else
       		{var url='index.php/statistics/json_calculate_division/'+App.TOKEN;}
        YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
        {
			 var raw = o.responseText.split('###');
			 var debug=raw[0];
 
			 var json=raw[1];
			 
			 var r=YAHOO.lang.JSON.parse(json);
			 this.store.loadData(r,false);	
			 
			 
			 if(r.length==0)
			 {
				 var title='No Standings Found';
				 var msg='For standings, we need at least one published schedule in this season.';
				 Ext.MessageBox.alert(title,msg);
				 
				 
			 }
			
        }},post);
	},
	constructor     : function(config)
    {
    
        var id='grid_calc_stats_'+Math.random();
    	var renderTo=config.renderTo;
    	var buttons=[];
    	if(config.bbar){buttons=config.bbar;}
		if(config.id){id=config.id;}
		if(Ext.getCmp(id)){Ext.getCmp(id).destroy();}
		this.rank_type_id=config.rank_type_id;
		this.season_id   =config.season_id;
		var xs=30;//extra small. for integers
		var sw=60;//small.  for decimals
		//override: make all the same
		xs=sw;
		config=
		{
			store:Ext.create( 'Ext.data.Store',{remoteSort:false,model:calc_model_id})
			,title: 'Current Team Standings',
			renderTo: renderTo,
			id:id
			,collapsible: true
			,stateful: true
			,width:"100%"
			,height: 300
			,columns: 
	        [
			    	    //un-comment these three top rows for debug
	        	//{dataIndex:'rank_order',text:'Order',width:60}
        	  // {dataIndex: 'team_id', text:'team_id DEBUG',      width:sw   },
        	  // {dataIndex: 'division_id', text:'division_id DEBUG',      width:sw   },
        	   {dataIndex: 'rank', text:'#',      width:xs   },
        	   {dataIndex: 'team_name',text: 'Team',flex:1 }	,
        	   {dataIndex: 'division_name',text: 'Division',flex:1 }	,
        	   {dataIndex: 'GB',  text: 'GB',       width:sw },
			   {dataIndex: 'GP',  text: 'GP',       width:xs },
			   {dataIndex: 'W',   text: 'W',        width:xs },
			   {dataIndex: 'L',   text: 'L',        width:xs },
			   {dataIndex: 'T',   text: 'T',        width:xs },
			   {dataIndex: 'PTS', text: 'PTS',      width:sw },

			   {dataIndex: 'RF',  text: 'RF',       width:xs },
			   {dataIndex: 'RA',  text: 'RA',       width:xs },
			   {dataIndex: 'RD',  text: 'RD',       width:xs },
			   {dataIndex: 'PCT', text: 'PCT',      width:sw }
			]
			    //,{dataIndex : 'description',text: 'Description',flex:1}
			   // ,{dataIndex : 'use_hth',text: 'Comparison',width:60, sortable:false,renderer:formatHH}

			    
			,dockedItems:
			[
				{dock: 'top',xtype: 'toolbar',
                    items:
                    [
                        {xtype:'button',menu:[],text:'All Divisions',value:-1,id:'standings_calc_division_filter'}
                    ]
				}
				,{dock: 'bottom',xtype: 'toolbar',
                    items:
                    [
                    	//{}
                    ]
                    
				}
                        	
                        	
			]
		    
		};
 
        this.callParent(arguments);
        this.buildDivisionsMenu();
	}	
	,buildDivisionsMenu:function()
	{
 
		var url='index.php/divisions/json_concated_names/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
		{
			var name,id,divs=YAHOO.lang.JSON.parse(o.responseText);
			//.log(divs);
			var divSelect=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;
        		Ext.getCmp('standings_calc_division_filter').setText(name);

				this.division_id=id;
				this.refresh();
			};
			var div_menu=new Array();
			for(i in divs)
			{
				if(!divs[i]) continue;//IE fix
				 
				id  =divs[i]['division_id'  ];
				name=divs[i]['division_name'];

				div_menu.push({text:name,value:id,handler:divSelect,scope:this});
			}
			div_menu.push({text:'All Divisions',value:-1,handler:divSelect,scope:this}); 
			Ext.getCmp('standings_calc_division_filter').menu = Ext.create('Spectrum.btn_menu',{items:div_menu});
		}},"season_id="+this.season_id);	
		
	}
	
});}



