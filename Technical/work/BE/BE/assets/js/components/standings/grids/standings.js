
var st_model_id='Standings';//+_generator;
if(!App.dom.definedExt(st_model_id)){
Ext.define( st_model_id,
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'rank_name',      type: 'string'},
	   {name: 'is_published',      type: 'string'},
	   {name: 'season_id',      type: 'string'},
	   {name: 'rank_type_id',  type: 'int'},
	   {name: 'pts_per_win',  type: 'float'},
	   {name: 'pts_per_loss',  type: 'float'},
	   {name: 'pts_per_tie',  type: 'float'},
	   {name: 'display_level',  type: 'float'},
	   {name: 'parent_rank_type_id',  type: 'int'}
	]
});}
//else console.info('Model exists '+st_model_id);
var  formatBoolYN=function(value)
{
 return (value =='t'||value=='true'||value===true) ? 'Yes' : 'No';
}
 var st_grid_id='Spectrum.grids.standings';//+_generator;
if(!App.dom.definedExt(st_grid_id)){
Ext.define(st_grid_id,
{
	extend: 'Ext.tree.TreePanel', //will not work with spectrum grids
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    refresh:function()
    {
    	this.store.proxy.extraParams={season_id:this.season_id};//SEASON ID GOES HERE!!!11
    	this.store.load();
    	return;
    	
    	YAHOO.util.Connect.asyncRequest('GET','index.php/statistics/json_rank_types/'+App.TOKEN,{scope:this,success:function(o)
    	{
			this.store.loadData(YAHOO.lang.JSON.parse(o.responseText));
    	}});
    	
	},
	init_season_menu:function()
	{
		var season_url='index.php/season/json_active_league_seasons/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('GET',season_url,{scope:this,success:function(o)
		{
			var name, icon,id,seasons=YAHOO.lang.JSON.parse(o.responseText);
			seasons=seasons['root'];//skip paginator stuff
			//this.seasons_menu=new Array();	
			var seasons_filter=new Array();	
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;

				this.season_id=id;
        		Ext.getCmp('btn_standings_season').setText(name);
        		this.refresh();
			};
			//one item for no season
			//seasons_filter.push({text:'Unassigned',value:-1,handler:itemClick,scope:this});
			var foundActive=false;			
			for(i in seasons) if(seasons[i])
			{
				name=seasons[i]['season_name']+" : "+seasons[i]['display_start']+" - "+seasons[i]['display_end'];
				id  =seasons[i]['season_id'];
				icon=seasons[i]['isactive_icon'];
				if(seasons[i]['isactive']=='t'||seasons[i]['isactive']=='true'||seasons[i]['isactive']===true)
				{ 
					foundActive={text:name,value:id};
				} 
        		seasons_filter.push({text:name,value:id,handler:itemClick,scope:this,iconCls:icon});
			}
			Ext.getCmp('btn_standings_season').menu=Ext.create('Spectrum.btn_menu',{items:seasons_filter});
			if(foundActive)//select one of them by default 
			{
				//.log(foundActive);
				//itemClick(foundActive,null);//fireEvent simulated DOESN TWORK
				this.season_id=foundActive.value;
        		Ext.getCmp('btn_standings_season').setText(foundActive.text);
				this.refresh();
			}
		//this.display_teams(null);//render gthe grid with empty data
		}});
	},
	season_id:-1,
	constructor     : function(config)
    {  
    	var id='grid_standings_';
    	var renderTo=config.renderTo;
    	var buttons=[];
    	if(config.bbar){buttons=config.bbar;}
		if(config.id){id=config.id;}
		if(Ext.getCmp(id)){Ext.getCmp(id).destroy();}
		
		if(config.season_id)
		{
			this.season_id=config.season_id;
			
		}
		var proxy_sesaon_id =this.season_id;
		config=
		{
			rootVisible: false,
			store:Ext.create( 'Ext.data.TreeStore',//cannot inherit from spectrumgrids becuase it is not a grid its a treepanel
				{
					remoteSort:false,model:st_model_id,folderSort: true,
					proxy: 
					{
	                    type: 'rest',
	                    url: 'index.php/statistics/json_rank_types_treepanel/'+App.TOKEN
	                },
	                extraParams:{season_id: proxy_sesaon_id }
	                
				})
			,title: 'Standings',renderTo: renderTo,id:id,collapsible: true,stateful: true,width:"100%",height: 300

			,columns: 
	        [
        		{text     : 'Name',flex:1,sortable : true,dataIndex: 'rank_name',xtype: 'treecolumn' }		    
			    ,{dataIndex : 'is_published',text: 'Published',width:60,renderer:formatBoolYN}
		    ]
		    ,dockedItems:
		    [
		    {dock:'top', xtype:'toolbar',items://tbar
		    [
		    	{xtype:'button',id:'btn_standings_season',menu:[],text:'Select a Season'}
		    ]}
		    ,{dock:'bottom', xtype:'toolbar',items://bbar
		    [

		    	{
		    		tooltip:'Create new Standings',
		    		iconCls :'add',
		    		id:'btn_standings_add',
		    		scope:this, 
		    		handler:function()
	            {
 					if(!this.season_id || this.season_id<0)
 					{
						Ext.Msg.alert("Cannot create standings",'Select a season from the drop down menu.  If no seasons exist'
							+ ' then they need to be created first.');
						return;
 					}
					YAHOO.util.Connect.asyncRequest('POST','index.php/statistics/json_root_rank_types/'+App.TOKEN,
					{failure:App.error.xhr,scope:this,success:function(o)
					{
						
						var win_id='create_stn_window';
						var wc = YAHOO.lang.JSON.parse(o.responseText);
						 
						var f=Ext.create('Spectrum.forms.standings',{
							season_id:this.season_id
							,wildcard:wc
							,window_id:win_id});
						
 
						var w=Ext.create('Spectrum.windows.standings',{items:f,id:win_id});
 
						w.on('hide',function(){this.refresh();},this);
						
						w.show();
						
						//f.render();//render is not needed
						f.show(); 
					
						
					}},'season_id='+this.season_id)
	            }}
				,{
					tooltip:'Copy Standings from another Season',
					iconCls:'paste_plain',
					scope:this,
					handler:function()
				{
					if(!this.season_id || this.season_id<0)
 					{
						Ext.Msg.alert("Cannot create standings to the current season" 
								,'Select a season from the drop down menu.  If no seasons exist'
							+' then they need to be created first.');
						return;
 					}
					var window_id='thiscopystnwindow';
					
					var f=Ext.create('Spectrum.forms.copy_standings',{window_id:window_id,season_id:this.season_id});
					
					var w=Ext.create('Spectrum.windows.copy_standings',{id:window_id,items:f});
					
					
					 w.on('hide',this.refresh,this);
					 w.show();
				}}
		    	,'->'
		    	,buttons
		    	,{tooltip:'Display Settings',iconCls:'monitor',disabled:false,scope:this,id:'btn_standings_display',handler:function()
		    	{
					//form here
					var rows=this.getSelectionModel().getSelection();
	                if(!rows.length){return;}
	                //.log('display settings season',this.season_id);
					var f=Ext.create('Spectrum.forms.standings.display',{rank_type_id: rows[0].get('rank_type_id'),season_id:this.season_id
														                ,display_level:rows[0].get('display_level')});
					f.refresh();//for the grod contained in the form
					
					var w=Ext.create('Spectrum.windows.standings.display',{items:f});
					w.show();
		    	}}
		    	,{tooltip:'Toggle Published',iconCls:'tick',scope:this,handler:function()
				{
					//alert("TODO: incomplete @sam");
					var rows=this.getSelectionModel().getSelection();
	                if(!rows.length){return;}
	                var post='rank_type_id='+ rows[0].get('rank_type_id');
	                //.log(rank_id);
	                var callback={scope:this,failure:App.error.xhr,success:this.refresh};
	                var url='index.php/statistics/post_publish_rank_type/'+App.TOKEN;
	                YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
				}}
		    	,{tooltip:'Edit Details',iconCls:'pencil',disabled:false,scope:this,id:'btn_standings_edit',handler:function()
		    	{
		    		var rows=this.getSelectionModel().getSelection();
		    		//.log('we have'+rows.length);
		            if(!rows.length){return;}
		    		YAHOO.util.Connect.asyncRequest('POST','index.php/statistics/json_root_rank_types/'+App.TOKEN,
		    		{scope:this,success:function(o)
					{
						
						
						
						var rows=this.getSelectionModel().getSelection();
		               //first filter them out
		                var wc = YAHOO.lang.JSON.parse(o.responseText);
		                
		                var valid_wc=new Array();
		                var current_rt_id = rows[0].get('rank_type_id');
		                for(i in wc)
		                {
							if(wc[i])//IE9 error checking
							{
 
								if(wc[i]['rank_type_id'] != current_rt_id)//cannot be parent fo self
									valid_wc.push(wc[i]);
								//we also disalwo this on server side, but do not display it here as well
								
							}
		                }
		                var window_id='editstn_window_';
						var f=Ext.create('Spectrum.forms.standings',
						{
							wildcard:valid_wc
							,window_id:window_id
							//,season_id:this.season_id//season id is in model, so loadRecord covers this already
						});
					 
						f.loadRecord(rows[0]);
						var w=Ext.create('Spectrum.windows.standings',{items:f,id:window_id});
						w.on('hide',function(){this.refresh();},this);
						w.show();

					}},'season_id='+this.season_id)

		    	}}
		    	,'-'
				,{tooltip:'Delete Standings',iconCls :'delete',disabled:false,id:'btn_standings_delete',//
	                scope:this, handler:function()
	                {
	                	var rows=this.getSelectionModel().getSelection();
	                	if(!rows.length){return;}
	                	var msg='Remove standings : '+rows[0].get('rank_name');
	                	var rank_type_id=rows[0].get('rank_type_id');
	                	Ext.MessageBox.show({title:'Delete?',msg:msg,
	                	buttons: Ext.Msg.YESNO,
	                	scope:this,
	                	fn:function(btn_id)
	                	{

							if(btn_id!='yes'){return;}
							
							var post='rank_type_id='+rank_type_id;
							var url='index.php/statistics/post_delete_rank_type/'+App.TOKEN;
							YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
							{
								this.refresh();
							}},post);
							
	                	}});
	                	
	                	
					}
					
				}
		    
		  	]}
			]//end of dockedItems
		    
		};

        this.callParent(arguments);
        this.init_season_menu();
	}
	
});

}

//.log('!!grids.standings loaded');