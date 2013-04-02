var wg_model = 'Wildcard_games';//+Math.random();
if(!App.dom.definedExt(wg_model)){
Ext.define(wg_model, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'division_match',      type: 'string'}
	   ,{name: 'is_used',    type: 'string'}
	   ,{name: 'h_division_id',    type: 'string'}
	   ,{name: 'a_division_id',    type: 'string'}
	]
});}
//else console.info('Model Exists'+wg_model);


var gridWcGames='Spectrum.grids.wildcard_games';
if(!App.dom.definedExt(gridWcGames)){
Ext.define(gridWcGames,
{
	//extend:'Ext.spectrumgrids',
	extend: 'Ext.grid.Panel', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    rank_type_id:-1,
    loading:true,
    refresh:function()
    {
 
    	this.loading=true;
		var url='index.php/statistics/json_rank_divisions/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
		{
			var r=YAHOO.lang.JSON.parse(o.responseText) ;
 
			this.store.loadData(r); 
			
			 var total=this.store.count();
			var keepExisting=true, suppressEvent=false;
			
			for(i=0;i<total;i++)
			{
				var used=this.store.getAt(i).get('is_used');
				 
				
				if(used ===true || used==='true'||used=='t')
				{
					 
					this.getSelectionModel().select(i,keepExisting,suppressEvent);
				}
				else
				{
					 
					this.getSelectionModel().deselect(i,keepExisting,suppressEvent);
				}
			}
			
			this.loading=false;
			
		}},'rank_type_id='+this.rank_type_id+"&season_id="+this.season_id);
    },
    saveCheckbox:function(rank_type_id,h_division_id,a_division_id,sel)//depreciated
    {
		var post='rank_type_id='+rank_type_id+'&h_division_id='+h_division_id+'&a_division_id='+a_division_id+'&sel='+sel;
 
		var url='index.php/statistics/post_rank_divisions/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
		{	
 
			//this.refresh();
		}},post);
		
    },
 
    season_id:-1,
    constructor     : function(config)
    {  
    	this.season_id=config.season_id;
    	var id='grid_stats_wc_'+Math.random();
    	var renderTo=config.renderTo;
    	//var buttons=[];
    	//if(config.bbar){buttons=config.bbar;}
		if(config.id){id=config.id;}
		if(Ext.getCmp(id)){Ext.getCmp(id).destroy();}
		this.rank_type_id=config.rank_type_id;
		
		config=
		{
    		store:Ext.create( 'Ext.data.Store',{remoteSort:false,model:wg_model})
    		//,style:'.x-column-header-checkbox {display:none;} '
			,title: 'Games Included',renderTo: renderTo,id:id,collapsible: false,stateful: true,width:"100%",height: 300
    		,selModel:Ext.create('Ext.selection.CheckboxModel',
    		 {
    		 	 checkOnly :true,
    		 	 injectCheckbox:'first',
    		  //header:false,//.x-column-header-checkbox {display:none;} 
    		 	 mode:'MULTI'
    	 		 ,listeners:
    	 		 {

		 			 select :{scope:this, fn:  function(sm,selections)
		 			 {
		 			 	 if(this.loading){return;}
  
		 				 var sel='t';
		 			 	 var rank_type_id=selections.data['rank_type_id'];
		 				 var h_division_id=selections.data['h_division_id'];
		 				 var a_division_id=selections.data['a_division_id'];
		 				 selections.data['is_used']=sel;
		 				  
						 
						 this.saveCheckbox(rank_type_id,h_division_id,a_division_id,sel);

		 			 }}
					 ,deselect:{scope:this,fn:function(sm,selections)
					 {

		 			 	 if(this.loading){return;}
 
		 			 	 //.log('single DEselect');
		 				 var sel='f';
		 			 	 var rank_type_id=selections.data['rank_type_id'];
		 				 var h_division_id=selections.data['h_division_id'];
		 				 var a_division_id=selections.data['a_division_id'];
		 				 selections.data['is_used']=sel;
 
						 this.saveCheckbox(rank_type_id,h_division_id,a_division_id,sel);

					 }}
				 
				 
				 }
			 
			 })
    		,columns: 
	        [
	        	{dataIndex:'division_match',text:'Match',flex:1}
	        	//,{dataIndex:'csv_division_ids',text:'Match',hidden:true,width:60}
	        	
	        ]
	        ,bbar:
		    [
		    
		    	{tooltip:'Internal Division games only',iconCls :'compress',id:'btn_wildcard_inter',
					scope:this, handler:function()
	                {
	                	var url='index.php/statistics/post_rank_divisions_internal/'+App.TOKEN;
	                	var post='rank_type_id='+this.rank_type_id;
						YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
						{
							
							this.refresh();
						}},post);	

	                }
				}
		    ]
		    
		}

    	this.callParent(arguments);	
    	
	}
	
});}



var w_t_model='Wildcard_teams';//+Math.random();
if(!App.dom.definedExt(w_t_model)){
Ext.define(w_t_model, 
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'division_name',       type: 'string'},
	   {name: 'long_division_name',       type: 'string'},
	   {name: 'division_id',         type: 'int'},
	   {name: 'wildcard_teams',      type: 'int'}
	]
});}
//else console.info('Model Exists'+w_t_model);

var gridWcTeams='Spectrum.grids.wildcard_teams';
if(!App.dom.definedExt(gridWcTeams)){
Ext.define(gridWcTeams,
{
	//extend:'Ext.spectrumgrids',
	extend: 'Ext.grid.Panel', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    refresh:function()
    {
		
		var url='index.php/statistics/json_rank_wildcard/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
		{
			this.store.loadData(YAHOO.lang.JSON.parse(o.responseText))
		}},'rank_type_id='+this.rank_type_id+"&season_id="+this.season_id);
    },
    rank_type_id:null,
    season_id:-1,
    constructor     : function(config)
    {     	
    	var id='grid_stats_teamwc_';//
    	var renderTo=config.renderTo;
    	//var buttons=[];
    	//if(config.bbar){buttons=config.bbar;}
		if(config.id){id=config.id;}
		if(Ext.getCmp(id)){Ext.getCmp(id).destroy();}
		this.rank_type_id=config.rank_type_id;
		this.season_id=config.season_id;
		config=
		{
    		
    		store:Ext.create( 'Ext.data.Store',{remoteSort:false,model:w_t_model})
			,title: 'Teams Included',
			renderTo: renderTo,
			id:id,collapsible: false,
			stateful: true,
			width:"100%",
			height: 300
			,plugins: 
			[
				Ext.create('Ext.grid.plugin.RowEditing', {clicksToEdit: 2,clicksToMoveEditor: 1,autoCancel: false})
			]

			 
			 ,listeners:
			 {
				 edit:{scope:this,fn:function(e)
				 {
 
					 var values=new Array();
    				for(key in e.record.data)
    				{
    					if(key!='division_name'&&key!='long_division_name') 			
							{values.push(key+"="+escape(e.record.data[key]));}
    				}
    				values.push('rank_type_id='+this.rank_type_id);
    				
    				var post=values.join("&");

    				var url='index.php/statistics/post_rank_wildcard/'+App.TOKEN;
    				var callback={scope:this,success:function(o)
     				 {
     	 				 var r=o.responseText;
     	 				 if(isNaN(r) || r<0 )
     	 				 {
     	 	 				 App.error.xhr(o);
     	 	 				 e.record.reject();
     	 				 }
     	 				 else
     	 				 {
     	 		 			e.record.commit();
						 }				 
     				 },failure:App.error.xhr};
    				YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
					 
				 }}
			 }
    		,columns: 
	        [
	        	{dataIndex:'long_division_name',text:'Division',sortable:true,flex:1}
	        	//,{dataIndex:'division_id',text:'',hidden:true}
	        	,{dataIndex:'wildcard_teams',text:'Remove Leaders',width:120,
	        		editor: { allowBlank: true ,maskRe:/\d/ }
	        	}
	        	
	        ]
	        ,bbar:
		    [
		    	'#'
		    	,{xtype:'textfield',width:50,id:'txt_set_all_wildcard',maskRe:/\d/ }
		    	,{tooltip:'Set all wildcard to #',iconCls :'pencil_go',id:'btn_wildcardteams_inter',
					scope:this, handler:function()
	                {
							var input=Ext.getCmp('txt_set_all_wildcard').getValue();
							if(input.split(' ').join('')=='' || isNaN(input)|| parseInt(input)<0)
							{
								Ext.getCmp('txt_set_all_wildcard').setValue('')
								return;
							}
							var post='rank_type_id='+this.rank_type_id+'&wildcard_teams='+input;
							var url='index.php/statistics/post_rank_wildcard_all/'+App.TOKEN;
							var callback={scope:this,success:function(o)
							{
								var r=o.responseText;
								//.log(r);
								this.refresh();
							}};
							YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
							//.log(post);
							//.log('TODO');
						
	                }
				}
		    	//,'->'

		    ]
		    
		}
		

        this.callParent(arguments);

	}
	
});}

