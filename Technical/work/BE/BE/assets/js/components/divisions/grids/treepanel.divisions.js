var  formatBoolYN=function(value)
{
	if(value =='t'||value=='true'||value===true)   return 'Yes';
	if(value =='f'||value=='false'||value===false) return 'No';
	return '';
}
var dv_model_id='Division';//name is from models/division.js;
 var st_grid_id='Spectrum.grids.divisions';
if(!App.dom.definedExt(st_grid_id)){
Ext.define(st_grid_id,
{
	extend: 'Ext.tree.Panel', //will not work with spectrum grids,cannot inherit from spectrumgrids becuase it is not a grid its a treepanel
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    //fugue_sitemap-application-blue for category
    //users for team
    
    refresh:function()
    {
    	this.store.proxy.extraParams={season_id:this.season_id};//
    	this.store.load();
	},

	season_id:-1,
	append_id:'',
	constructor     : function(config)
    {  
    	if(typeof config.division_primary == 'undefined') {config.division_primary=false;}
    	this.division_primary=config.division_primary;
    	if(this.division_primary) this.append_id='sec';
    	//var id='';
    	//var renderTo=config.renderTo;
    	var buttons=[];
    	if(config.bbar){buttons=config.bbar;}
		if(!config.id){config.id='grid_divisions';}
    	var renderTo=config.renderTo
		var ts_id ='treestore-'+config.id;
		if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		if(Ext.getCmp(ts_id)){Ext.getCmp(ts_id).destroy();}
		if(Ext.getCmp(config.id+this.append_id)){Ext.getCmp(config.id+this.append_id).destroy();}
		
		if(config.season_id)
		{
			this.season_id=config.season_id;
		}
		var proxy_sesaon_id =this.season_id;
		config.allowCopy=true;
		config.copy=true;
		config.appendOnly =true;
		if(!config.title)config.title= 'Divisions';
		//,renderTo: renderTo,id:id,stateful: true,
		config.width="100%";
		 config.height=App.MAX_GRID_HEIGHT;
		//workaround found in this thread http://www.sencha.com/forum/showthread.php?130680-TreeStore-supported-in-MVC&p=643402#post643402
		// user wcravens 
		config.root={};//WORKAROUND FOR getRootNode() is null , without this, in 
		config.rootVisible= true;
		config.collapsible= false;
		
	    config.useArrows= true;
	    
		config.store=Ext.create( 'Ext.data.TreeStore',//
		{
			id:ts_id,
			storeId:ts_id,
			remoteSort:false,model:dv_model_id,folderSort: true,sortOnLoad:false,
			
			proxy: 
			{
	            type: 'rest',
	            url: 'index.php/divisions/json_season_divisions_treepanel/'+App.TOKEN
	        },
	        extraParams:{season_id: proxy_sesaon_id }
	        ,listeners:
	        {
				load:{scope:this,fn:function()
				{
					//default is to load as collapsed: this keeps all folders open all the time
					this.expandAll();	
				}}
	        }
	        
		});
	
			//height: '90%',
			

		config.columns= 
	    [
        	{text     : 'Name',flex:1,sortable : false,dataIndex: 'division_name',xtype: 'treecolumn' }		    
			,{dataIndex : 'only_teams',text: 'Allows Teams',width:110,renderer:formatBoolYN}
			,{dataIndex : 'total_teams',text: '# Teams',width:110}
			
		];
		config.viewConfig= 
		{
		    plugins: {ptype: 'treeviewdragdrop'}	
		    ,listeners:
		    {
 
			    drop:{scope:this,fn:function(node, data, dropRec,dropPosition,o) 
				{
 

					if(!data.records.length){return;}//probably drop was out of bounds?
					
					var rec=data.records[0];
                    
					var from_season_id =  rec.get('season_id');
					
					var dest_season_id = this.season_id;
					
					var moved_division_id = rec.get('division_id');

					if(dropRec)
					{
						var target_division_id=dropRec.get('division_id');
					}
					else
					{
						//.log('droprec doesnt exist, quit now');
						return;//probably drop was out of bounds?
					}
					//try to make target as the parent division of moved
					
					if(!dest_season_id || dest_season_id==-1 ) 
					{
						Ext.MessageBox.alert('','Please select a season first.');
						if(Ext.getCmp('left_divs_grid'))  Ext.getCmp('left_divs_grid' ).refresh();
						if(Ext.getCmp('right_divs_grid')) Ext.getCmp('right_divs_grid').refresh();
						return;
					}
					//pass both season ids as well. make PHP do all the work
					var post = 'm_division_id='+moved_division_id+"&np_division_id="+target_division_id
								+"&type="+dropPosition+"&from_season_id="+from_season_id+"&dest_season_id="+dest_season_id;
					
					var url='index.php/divisions/post_move_division/'+App.TOKEN;
						
					YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,failure:App.error.xhr,success:function(o)
					{
						var r=o.responseText;
						if(isNaN(r))
						{
							App.error.xhr(o);
						}
						else
						{
							//this.refresh();	
							if(Ext.getCmp('left_divs_grid'))  Ext.getCmp('left_divs_grid' ).refresh();
							if(Ext.getCmp('right_divs_grid')) Ext.getCmp('right_divs_grid').refresh();
							//data.view.refresh();				
						}
					}},post);
				}}
			
			}
		};
	    
	    if(typeof config.dockedItems=='undefined')
	    config.dockedItems=new Array();
	    config.dockedItems.push(
	    {dock:'top', xtype:'toolbar',items://tbar
		[
		    {xtype:'button',id:'btn_mdiv_season'+this.append_id,menu:[],text:'Select a Season'}
		
		
		
		]});
		if(config.division_primary)//secondary does not have buttons
		{
		config.dockedItems.push(
	
		{dock:'bottom', xtype:'toolbar',items://bbar
		[

		    {tooltip:'Create new Division',iconCls :'fugue_category--plus'/*,id:'btn_divisions_add'*/,scope:this, handler:function()
	        {
	        	//if no season is selected, do not show create window, or else they wil see an SQL error on save.
	            if(!this.season_id || this.season_id==-1)
	            {
					Ext.MessageBox.alert('Cannot create yet','Each division is attached to one Season.  Please select a season first.');
					return;
	            }
	            
	            var w_id='create_div_window';
	            var f=Ext.create('Spectrum.forms.divisions',{season_id:this.season_id,window_id:w_id});
	            var w=Ext.create('Spectrum.windows.divisions',{items:f,id:w_id});
	            
				w.on('hide',function(){this.refresh();},this);
	            w.show();
	            
	        }}//
	       // ,'-'
	       // ,'Drag and drop divisions to move. '
	        ,'->'
	        ,{tooltip:'Modify Selected Division Registration Fees',iconCls:'coins_edit',scope:this,handler:function()
            {
            	var rows=this.getSelectionModel().getSelection();
				
				if(!rows.length)return;
				
                var record=rows[0];
                if(!record.get('division_id')||record.get('parent_division_id')==null || parseInt(record.get('parent_division_id'))!=0)
                {
                    Ext.MessageBox.alert({title:"Root divisions only.",
                    		msg:"Select a root Division to apply fees", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                    return;
                }
                var window_id ='div_window_ids';
                var form=Ext.create('Spectrum.forms.division_fees',
                {
                	window_id:window_id//give the form access to the window, so the form can hide the window
 
                });
                
                //moved save button to the form.  Why does the grid care how the form gets saved? 
                
                
				//load record into form before it renders
				//model is already defined
                form.loadRecord(record);      
 				
 				
 				//moved window height and dimenisons to the window definition.  this grid does not care how big or small the window is.
                var win=Ext.create('Spectrum.windows.division_fees',{id:window_id,items:form});//give the form to the window
                
				win.on('hide',function(){this.refresh();},this);//the save button is not allowed to access the grid. so load data must go here
                win.show();
            }} 
            ,'-'   
	        ,{tooltip:'Modify Selected Division',iconCls:'fugue_cateogry--pencil',scope:this,handler:function()
			{
				var rows=this.getSelectionModel().getSelection();
				
				if(!rows.length)return;
				
				if(!rows[0].get('division_id')) {return;}//if null record
				
				var w_id='create_div_window';
	            var f=Ext.create('Spectrum.forms.divisions',{season_id:this.season_id,window_id:w_id});
	            f.loadRecord(rows[0]);
	            var w=Ext.create('Spectrum.windows.divisions',{items:f,id:w_id,title:'Edit Division'});
	            
				w.on('hide',function(){this.refresh();},this);
	            w.show();
			}}
	      //  ,'-'
	        ,{tooltip:'Delete Division',iconCls:'fugue_minus-button',scope:this,handler:function()
			{
				var rows=this.getSelectionModel().getSelection();
				
				if(!rows.length)return;
				var r=rows[0];
				var division_name=r.get('division_name');
				if(!division_name){return;}
				var msg='Remove '+division_name+" from the selected season?";
				Ext.MessageBox.confirm('Delete?', msg, function(btn_id)
		 		 {
		 			 if(btn_id!='yes')return;
		 			 var rows=this.getSelectionModel().getSelection();
					
					 if(!rows.length)return;
					 var r=rows[0];
					
					 var division_id=r.get('division_id');
     				 var post="division_id="+division_id+"&season_id="+this.season_id;
     				 var url="index.php/divisions/post_delete_division/"+App.TOKEN;
     				 var callback={failure:App.error.xhr,scope:this,success:function(o)
     				 {
     	 				 var r=o.responseText;
						 if(isNaN(r)||r<0)
						 {
							 	
					 		 Ext.MessageBox.alert('Result',r);							 
						 }
						 else
						 {
			 				 this.refresh();
							 //this.setDisabled_teamsGrid(true);
						 }								 
     				 }};
	 
	 				 YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
	 			 },this);
				
	        }}

            
		
		]}//end of bottom dock
		);//
		
		}//end of if primary test
    	else
    	{
    		//otherwise: secondary
			config.dockedItems.push(
			{dock:'bottom', xtype:'toolbar',items://bbar
			[
				'->'
				,'Drag and Drop divisions from this panel to the panel on the left'
			]}
			);
    	}
        this.callParent(arguments);
        this.init_season_menu();
	},
	init_season_menu:function()
	{
		var season_url='index.php/season/json_active_league_seasons/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('GET',season_url,{scope:this,success:function(o)
		{
			var name, id,seasons=YAHOO.lang.JSON.parse(o.responseText);
			if(typeof seasons['root'] !='undefined')seasons=seasons['root'];//skip paginator stuff if it exists
			//this.seasons_menu=new Array();	
			var seasons_filter=new Array();	
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;

				this.season_id=id;
        		Ext.getCmp('btn_mdiv_season'+this.append_id).setText(name);
        		if(this.refresh)
					this.refresh();
			};
			//one item for no season
			//seasons_filter.push({text:'Unassigned',value:-1,handler:itemClick,scope:this});
			var defaultActive=true;
			if(!this.division_primary)//primary one gets an active season, otherwise gets an inactive
			{
				defaultActive=false;
			}
			var foundActive=false;			
			for(i in seasons)
			{
				name=seasons[i]['season_name']+" : "+seasons[i]['display_start']+" - "+seasons[i]['display_end'];
				id  =seasons[i]['season_id'];
				icon='';
				//do icon seperately from foundactive
				if(seasons[i]['isactive']==defaultActive)
				{
					foundActive={text:name,value:id};
				}

				icon=seasons[i]['isactive_icon'];

        		seasons_filter.push({text:name,value:id,handler:itemClick,scope:this,iconCls:icon});
			}
			Ext.getCmp('btn_mdiv_season'+this.append_id).menu=Ext.create('Spectrum.btn_menu',{items:seasons_filter});
			if(foundActive)//select one of them by default 
			{
				 
				//itemClick(foundActive,null);//fireEvent simulated DOESN TWORK
				this.season_id=foundActive.value;
        		Ext.getCmp('btn_mdiv_season'+this.append_id).setText(foundActive.text);
				this.refresh();
			}
		//this.display_teams(null);//render gthe grid with empty data
		}});
	}
	
});

}
