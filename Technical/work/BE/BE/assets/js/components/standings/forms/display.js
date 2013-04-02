

var fstmd = 'Spectrum.forms.standings.display';
if(!App.dom.definedExt(fstmd)){
Ext.define(fstmd,
{
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    rank_type_id:-1,
    season_id:-1,
    _swapRows:function(above,row)
    {
	
		var url = 'index.php/statistics/post_swap_display_order/'+App.TOKEN;
		var callback={scope:this,success:function(o)
		{
			this.refresh();
		}};

		//above_hth=above.get('use_hth');
		//row_hth=row.get('use_hth');
		//..this.dtRanking.disable();
		
		var post='rank_type_id='+this.rank_type_id+
		"&above_stat_id="+above.get('stat_id')+
		"&above_rank="+  above.get('rank_order')+
		"&below_stat_id="+row.get('stat_id')+
		"&below_rank="+ row.get('rank_order');

		YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
    },
    loading:false,
    refresh:function()
    {
		 var post='rank_type_id='+this.rank_type_id;
		 YAHOO.util.Connect.asyncRequest('POST','index.php/statistics/json_rank_display/'+App.TOKEN,{scope:this,success:function(o)
		 {
		 	 var r=YAHOO.lang.JSON.parse(o.responseText);
		 	 var g=Ext.getCmp('_grid-display-stats');
			 g.store.loadData(r); 
			 var total=g.store.count();
			var keepExisting=true, suppressEvent=true;
			this.loading=true;
			for(i=0;i<total;i++)
			{
				var used=g.store.getAt(i).get('is_used');
				
				if(used ===true || used==='true'||used=='t')
				{
					g.getSelectionModel().select(i,keepExisting,suppressEvent);
				}
				else
				{
					g.getSelectionModel().deselect(i,keepExisting,suppressEvent);
				}

			}
			this.loading=false;
		 }},  post  );
    },
    checkStat:function(rank_type_id,stat_id,is_used,rank_order,hth)
	{

		var post="&rank_type_id="+rank_type_id+"&stat_id="+stat_id+"&is_used="+is_used+"&rank_order="+rank_order;
		var url='index.php/statistics/post_rank_display/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function()
		{
			this.refresh();	
			
		}},post);
	},
	display_level:null,
    constructor     : function(config)
    {  
    	var season_id=config.season_id;
    	this.season_id=config.season_id;
		var id='c_standingsdisplay_form';//
    	if(config.id){id=config.id};
    	this.rank_type_id=config.rank_type_id;
    	if(Ext.getCmp(id)){Ext.getCmp(id).destroy();}
    	this.display_level=config.display_level;

    	var checkbox = Ext.create('Ext.selection.CheckboxModel',
    	 {
    	 	 checkOnly :true,injectCheckbox:'last',mode:'MULTI',
    	 	 listeners:
    	 	 {
				// selectionchange:function(sm,selections){ },
		 		 select:{scope:this, fn:function(sm,selections)
		 		 {
		 		 	 if(this.loading){return;}
					 var rank_type_id=selections.data['rank_type_id'];
		 			 var stat_id=selections.data['stat_id'];
		 			 var rank_order=selections.data['rank_order'];
		 			 var sel='t';
					 //.log('select event ',stat_id);
					 this.checkStat(rank_type_id,stat_id,sel,rank_order);
					 
		 		 }},
				 deselect:{scope:this, fn:function(sm,selections)
				 { 
		 		 	 if(this.loading){return;}
		 		 	 var rank_type_id=selections.data['rank_type_id'];
		 			 var stat_id=selections.data['stat_id'];
		 			 var rank_order=selections.data['rank_order'];
		 			 var sel='f';
					 //.log('sDEselect event ',stat_id);
					 this.checkStat(rank_type_id,stat_id,sel,rank_order);
				 }}
			 }
		 });
		
		 config=
		 {  
		 	 id:id,title: '',autoHeight: true,resizable:false,bodyPadding: 10,width: 600,//height:250,
	        fieldDefaults: {labelWidth: 125,msgTarget: 'side',autoFitErrors: false},
	        defaults: {anchor: '100%'},
	        items:
	        [
		        {xtype: 'displayfield',id:'_display_rank_type_id',name : 'rank_type_id',hidden:true,value:this.rank_type_id}
		        ,{xtype: 'displayfield',name : 'parent_rank_type_id',hidden:true,value:-1}
	        	,{xtype: 'displayfield',flex : 1,name : 'rank_name', hidden:true,  fieldLabel: 'Name',allowBlank: false}
	        	
	        	,{xtype: 'hidden',flex : 1,name : 'display_level',id:'current_display_level',   fieldLabel: 'Level',value:'1'}
	        	,{xtype: 'button',menu:[],   fieldLabel: 'Level',allowBlank: false,value:'1',id:'display_level_btn'}
	        	
	        	,{xtype:'grid',id : '_grid-display-stats',store:[],height: 330,title:'Statistics',selModel:checkbox,
	        		columns: 
	        		[
		                {text   : 'Name',flex: 1,sortable : true,dataIndex: 'stat_name'},
		                {text   : 'Abbrev.',flex: 1,sortable : true,dataIndex: 'stat_abbr'},
		                {text   : '',hidden : true,dataIndex: 'stat_id'}
		                ,{xtype:'actioncolumn',width:50,
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
							//	else{console.log('invalid move');}
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
								//else{console.log('invalid move');}
			    			}}
					    ]}
	                ]
				}
	        ]/*
	        ,buttons:
	        [

	        ]*/
	        
		 };
	
        this.callParent(arguments);   
        this.build_level_menu(season_id); 	    	
	}
	
	,build_level_menu:function(season_id)
	{
		var url ='index.php/divisions/json_build_level_menu/'+App.TOKEN;	
		var post='season_id='+season_id;
		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
		{
			var btn=new Array();
			
			var itemClick=function(o,e)
			{
        		Ext.getCmp('display_level_btn').setText(o.text);
        		//Ext.getCmp('current_display_level').setValue(id);
				//this form doesnt get saved, so we dump this value right away!
        		var post='display_level='+o.value+'&rank_type_id='+this.rank_type_id;
        		//.log(post);
        		YAHOO.util.Connect.asyncRequest('POST','index.php/statistics/post_update_display_level/'+App.TOKEN,{},post);
				//this.refresh();
			};
			var lvls=YAHOO.lang.JSON.parse(o.responseText);
			var start_label=false;
			for(i in lvls)
			{
				var name =lvls[i]['lbl_level'];
				var value=lvls[i]['display_level'];
				var oItem={text:name,value:value,handler:itemClick,scope:this} ;
        		btn.push(oItem );
        		//.log(this.display_level,'compare',value);
        		if(this.display_level==value)
        		{
					start_label=name;
					//.log("!!!found",name);
        		}
			}
			Ext.getCmp('display_level_btn').menu=Ext.create('Spectrum.btn_menu',{items:btn});
			if(start_label)
        		{Ext.getCmp('display_level_btn').setText(start_label);}
			
		}},post  );
	}
});
}

