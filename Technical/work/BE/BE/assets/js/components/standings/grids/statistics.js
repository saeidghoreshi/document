var _gen=Math.random();
var stat_model_id='Statistics';//+_gen;
if(!App.dom.definedExt(stat_model_id)){
Ext.define(stat_model_id, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'stat_name',      type: 'string'},
	   {name: 'stat_abbr',      type: 'string'},
	   {name: 'internal_index',      type: 'string'},
	   {name: 'description',      type: 'string'},
	   {name: 'use_hth',      type: 'string'},
	   {name: 'is_used',      type: 'string'},
	   //{name: 'is_published',      type: 'string'},
	   {name: 'rank_type_id',  type: 'int'},
	   {name: 'rank_order',  type: 'int'},
	   {name: 'stat_id',  type: 'int'}

	]
});}
//else console.info('model exists '+stat_model_id);

var formatHH=function(bool)
{
	if(bool==='true' || bool===true || bool==='t')
	{
		return 'H2H';
	}
	else
	{
		return 'Regular';
	}
}

var statsgridpanel='Spectrum.grids.statistics';
if(!App.dom.definedExt(statsgridpanel)){
Ext.define(statsgridpanel,
{
	extend: 'Ext.grid.Panel', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    rank_type_id:null,
    _swapRows:function(above,row)
    {
	
		
		var url = 'index.php/statistics/post_swap_order/'+App.TOKEN;
		var callback={scope:this,success:function(o)
		{
			this.refresh();
		}};

		above_hth=above.get('use_hth');

		row_hth=row.get('use_hth');
			
		//..this.dtRanking.disable();
		var post='rank_type_id='+this.rank_type_id+
		"&above_stat_id="+above.get('stat_id')+
		"&above_use_hth="+above_hth+
		"&above_rank="+  above.get('rank_order')+
		"&below_stat_id="+row.get('stat_id')+
		"&below_use_hth="+row_hth+
		"&below_rank="+ row.get('rank_order');

		YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
		
    },
    
    loading:false,
    refresh:function()
    {
    	
    	
    	YAHOO.util.Connect.asyncRequest('POST','index.php/statistics/json_league_preferences/'+App.TOKEN,{scope:this,success:function(o)
    	{
			this.store.loadData(YAHOO.lang.JSON.parse(o.responseText));
			//update selection model based on data
			//http://www.sencha.com/forum/showthread.php?79842-Ext.grid.CheckboxSelectionModel-check-or-uncheck-in-run-time
			var total=this.store.count();
			var keepExisting=true, suppressEvent=true;
			this.loading=true;
			//.log('load event complete, start select loop');
			for(i=0;i<total;i++)
			{
				//.log('select At ',i);
				var used=this.store.getAt(i).get('is_used');
				//.log(used);
				if(used ===true || used==='true'||used=='t')
				{
					
					this.getSelectionModel().select(i,keepExisting,suppressEvent);
				}

			}
			this.loading=false;
			
    	}},'rank_type_id='+this.rank_type_id);
    	
	},
	checkStat:function(rank_type_id,stat_id,is_used,hth,ord)
	{
		if(hth===false || hth=='false' || hth=='f')
			{hth='f';}
		else
			{hth='t';}
		var post="&rank_type_id="+rank_type_id+"&stat_id="+stat_id+"&is_used="+is_used+'&hth='+hth+"&rank_order="+ord;
		var url='index.php/statistics/post_used_rank_statistics/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function()
		{
			this.refresh();	
			
		}},post);
	},
	constructor     : function(config)
    {  
    	var id='grid_stats_'+Math.random();
    	var renderTo=config.renderTo;
    	var buttons=[];
    	if(config.bbar){buttons=config.bbar;}
		if(config.id){id=config.id;}
		if(Ext.getCmp(id)){Ext.getCmp(id).destroy();}
		
		
		config=
		{
			selModel:Ext.create('Ext.selection.CheckboxModel',
    		 {
    		 	 checkOnly :true,injectCheckbox:'last',mode:'MULTI'
    	 		 ,listeners:
    	 		 {
					 //selectionchange:function(sm,selections){ },
		 			 select :{scope:this, fn:  function(sm,selections)
		 			 {
 
		 			 		 if(this.loading){return;}
		 			 		 var rank_type_id=selections.data['rank_type_id'];
		 			 		 var stat_id=selections.data['stat_id'];
		 			 		 var hth=selections.data['use_hth'];
		 			 		 var sel='t';//forced true
 
							 this.checkStat(rank_type_id,stat_id,sel,hth,selections.data['rank_order']);
		 			 }}
					 ,deselect:{scope:this,fn:function(sm,selections)
					 {
		 			 		  
		 			 		 if(this.loading){return;}
							 var rank_type_id=selections.data['rank_type_id'];
		 			 		 var stat_id=selections.data['stat_id'];
		 			 		 var hth=selections.data['use_hth'];
		 			 		 var sel='f';//forced false
							  
							 this.checkStat(rank_type_id,stat_id,sel,hth,selections.data['rank_order']);
					 }}
				 }
			 
			 })
			,store:Ext.create( 'Ext.data.Store',{remoteSort:false,model:stat_model_id})
			,title: 'Statistics',renderTo: renderTo,id:id,collapsible: true,stateful: true,width:"100%",height: 300,
 
			columns: 
	        [
	        	{dataIndex:'rank_order',text:'Order',width:60}
			   // ,{dataIndex : 'is_used',text: 'Used',width:60//,xtype:'checkcolumn'//this xtype is broken use selModel:
			  //  }
			    
        		,{
        			dataIndex: 'stat_name',
        			text     : 'Stat' 
        		}		    
			    ,{
			    	dataIndex : 'description',
			    	text: 'Description',
			    	flex:1
			    }
			    ,{
			    	dataIndex : 'use_hth',
			    	text: 'Comparison',
			    	width:60, 
			    	sortable:false,
			    	renderer:formatHH
			    }
			    ,{
			    	xtype:'actioncolumn',
			    	width:50,
			    items:
			    [
			    	{tooltip:'Up',iconCls:'arrow_up',icon:'/assets/images/dev/arrow_up.png',scope:this ,handler:function(grid, rowIndex, colIndex)
			    	{
			    		
						var row=grid.getStore().getAt(rowIndex);
						if( rowIndex > 0)
						{											
							var above = grid.getStore().getAt(rowIndex-1);//one up
							this._swapRows(above,row);
						}
					 
						
						
						
			    	}}
			    	,{tooltip:'Down',iconCls:'arrow_down',icon:'/assets/images/dev/arrow_down.png',scope:this ,handler:function(grid, rowIndex, colIndex)
			    	{
						//if(col_key == "downbtn" && row_index < this.dtRanking.getRecordSet().getRecords().length-1)
						//var row=
						var above=grid.getStore().getAt(rowIndex);//above is the clicked row
						var row = grid.getStore().getAt(rowIndex+1);	//one down
						if(row)
						{
							this._swapRows(above,row);
						}
					//	else{console.log('invalid move');}
			    	}}
			    ]}
			    
		    ]
		    ,bbar:
		    [
		    /*
		    	{tooltip:'Add statistics',iconCls :'add',id:'btn_stats_add',
					scope:this, handler:function()
	                {
							
							
						var f=Ext.create('Spectrum.forms.standings',{});
						var w=Ext.create('Spectrum.windows.standings',{items:f});
						w.show();
						w.on('hide',function(){this.refresh();},this);
						
	                }
				}
		    	,'->'
*/
		    ]
		    
		};
		
    	
    	
        this.callParent(arguments);
	}
	
});}



//.log('!!grids.statitsics loaded');