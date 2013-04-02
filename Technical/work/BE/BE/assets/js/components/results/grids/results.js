var formatStatus=function(input)
{
	var spl=input.split(",");
	var text=spl[0];
	var icon=spl[1];
	var s_id=spl[2];
	var img_src="<img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/"+icon+".png'/>";
	return img_src+text;
		
}
var g_model_id='GameForResults';//from models/results.js
var gclass='Spectrum.grids.game_results';
if(!App.dom.definedExt(gclass)){    
Ext.define(gclass,
{
   // extend: 'Ext.grid.Panel', 
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
	season_id:null,
    set_season_id:function(i)
    {
		this.season_id=i;
 
    }, 
	get_season_id:function()
    {
		return this.season_id;
    },
 
    refresh:function()
    {
    	var post_stats=new Array();
    	for(id in this.status_ids)
    	{
			if(this.status_ids[id])
				{post_stats.push(id);}
    	}
    	var post='season_id='+this.season_id+"&status_ids="+YAHOO.lang.JSON.stringify(post_stats)+"&hours="+this.hours;
    	var url='index.php/games/json_past_season_games/'+App.TOKEN;
    	YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
    	{
			var games_found=YAHOO.lang.JSON.parse(o.responseText);
			
			this.store.loadData(games_found);
			
			//if(!games_found.length){Ext.MessageBox.alert('No submissions found','');}
			
    	}},post);
    	//get games by season
		
    },
    status_ids:[],
    hours:72,
    set_status:function(status,bool)
    {
		this.status_ids[status]=bool;	
    },
    constructor     : function(config)
    {  
        var id='games_grid__';//+Math.random();//default id that can overwrite
         
        if(!config.id){config.id=id;}
        if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy(); }
 
        var buttons=config.bbar; 
        
        config.store= Ext.create( 'Ext.data.Store',
		{
			groupField:'display_date',
			remoteSort:false,
	        autoDestroy: false,
	        model:g_model_id,
	        data: []//games
	    });   
	    var groupingFeature = Ext.create('Ext.grid.feature.Grouping',
	    {//game_date
	        //groupHeaderTpl: 'On: {name} ({rows.length} Game{[values.rows.length > 1 ? "s" : ""]})'
	        groupHeaderTpl: '{name}'
	    });
	    
        var w_score=40; 
	    config.collapsible=false;//, 
 
	    config.features= [groupingFeature];
	    config.title='Games';
        config.dockedItems =
        [
            {
                dock    : 'top',
                xtype   : 'toolbar',
                items   : 
				[
					{ xtype:'button',text:'Pick a Season', id:'btn_results_season_filter',menu:[]}
					,{xtype:'button',text:'Show',          id:'btn_results_status_filter',menu:[]}
				]
            }
            ,{
                dock    : 'bottom',
                xtype   : 'toolbar',
                items   : 	
                [
	                {xtype:'displayfield',value:"Select a game to view results",style:{ 'font-size':'12px'},height:23}
                ]		
            }
        ];
 
		config.columns= 
	    [
        	{text     : 'Home',flex:1,		sortable : true,dataIndex: 'home_name'}	
			,{text:'Score',sortable : true,width:w_score,	dataIndex: 'home_score'
			   // ,editor: { allowBlank: false ,maskRe:/\d/}
					
			}	
			,{text     : 'Away',flex:1,	     sortable : true,dataIndex: 'away_name'    }	   
			,{text:'Score',sortable : true,width:w_score,	 dataIndex: 'away_score'
			   // ,editor: { allowBlank: false ,maskRe:/\d/}
			}	 
			,{text     : 'Location',flex:1,sortable : true,  dataIndex: 'venue_name'    }	 
			,{text     : 'Status',flex:1,    sortable : true,dataIndex: 'csv_status' ,renderer:formatStatus   }	 
 
		];
			
        this.callParent(arguments);
		 
		this.buildSeasonsMenu();
	    this.buildStatusMenu();
	}
	,buildSeasonsMenu:function()
	{
		YAHOO.util.Connect.asyncRequest('GET','index.php/season/json_active_seasons/'+App.TOKEN,
		{scope:this,failure:App.error.xhr,success:function(o)
	    {
	    	var name,btn_lbl, id,seasons=YAHOO.lang.JSON.parse(o.responseText);
			//this.seasons_menu=new Array();	
			var seasons_filter=new Array();	
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;
        		Ext.getCmp('btn_results_season_filter').setText(o.btn_lbl);

				this.set_season_id(id);
				this.refresh();
			};
			//one item for no season
			
			//seasons_filter.push({text:'Unassigned',value:-1,handler:itemClick});
			var foundActive=false;			
			var active=null;
			for(i in seasons)
			{
				////////////////////////////
				icon=seasons[i]['isactive_icon'];
				name=seasons[i]['season_name']+" : "+seasons[i]['display_start']+" - "+seasons[i]['display_end'];
				id  =seasons[i]['season_id'];
				if(!id) continue;
				btn_lbl=seasons[i]['season_name'];
				var oItem={text:name,value:id,handler:itemClick,scope:this,btn_lbl:btn_lbl,iconCls:icon} ;
        		seasons_filter.push(oItem );
				if(foundActive==false && seasons[i]['isactive']=='t')
				{
					foundActive=true;
					active=oItem;
					
				}
			}
			Ext.getCmp('btn_results_season_filter').menu=Ext.create('Spectrum.btn_menu',{items:seasons_filter});
			if(foundActive)
			{
				 
				var o=active;//simulates the function call argument assignment
				var name = o.text;
				var id   = o.value;
        		Ext.getCmp('btn_results_season_filter').setText(o.btn_lbl);

				this.set_season_id(id);
				this.refresh();
			}
			
	    }});
	}
	,buildStatusMenu:function()
	{
		 ///////////////////checkbox menu
        YAHOO.util.Connect.asyncRequest('GET','index.php/games/json_lu_game_result_status/'+App.TOKEN,{scope:this,success:function(o)
        {
        	var img_src,icon,item,id,name,stat=YAHOO.lang.JSON.parse(o.responseText);
        	var stat_filter=new Array();
        	var onItemCheck=function (item, checked)
        	{
 
        		this.set_status(item.value,checked);
        		this.refresh();
        		
		    }
        	
        	for(i in stat)
        	{
        		id = stat[i]['id'];
        		name=stat[i]['status'];
        		icon=stat[i]['icon'];
        		//if an icon is given
        		if(icon)
        			{img_src="<img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/"+icon+".png'   />";}
        		else
        			{img_src='';}
        		//icon in front of name:
        		name=img_src+name;
        		this.status_ids[id]=true;
        		item= {text:name,value:id,checked:this.status_ids[id],checkHandler:onItemCheck,scope:this};
				stat_filter.push( item);
        	}
        	//these types are not in lu_ table db, since they are for 'no results exist'
        	
        	//one for games finished recently with no score
        	this.status_ids[-1]=false;
        	img_src="<img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/shading.png'   />";
        	var unsub={text:img_src+'Unsubmitted',value:-1,checked:this.status_ids[-1],checkHandler:onItemCheck,scope:this};
			stat_filter.push(unsub);
        	
        	//one for games finished long ago with no score 
        	this.status_ids[-2]=false;
        	img_src="<img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/hourglass.png'   />";
        	var late ={text:img_src+'Late Scores',value:-2,checked:this.status_ids[-2],checkHandler:onItemCheck,scope:this};
			stat_filter.push(late );
			
			
			Ext.getCmp('btn_results_status_filter').menu=Ext.create('Spectrum.btn_menu',{items:stat_filter});
			this.refresh();
        }});
	}
}); 

}  