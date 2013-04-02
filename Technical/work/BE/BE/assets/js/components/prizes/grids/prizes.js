//var _generator=Math.random();
var st_model_id='Prize';//+_generator;
if(!App.dom.definedExt(st_model_id)){
Ext.define( st_model_id,
{
	extend: 'Ext.data.Model',
	fields: 
	[
	   {name: 'name',      type: 'string'},
	   {name: 'short_name',  type: 'string'},
	   {name: 'prize_id',      type: 'int'},
	   {name: 'sku',  type: 'string'},
	   {name: 'upc',  type: 'string'},
	   {name: 'category_id',  type: 'string'},//dont make this a fucking integer, or load record WILL NOT WORK with the combobox in the edit/create form
	   {name: 'description',  type: 'string'},
	   {name: 'thumb_filepath',  type: 'string'},
	   {name: 'min_price',  type: 'string'},
	   {name: 'avg_price',  type: 'string'},
	   {name: 'filepath',  type: 'string'}

	]
});}
//else console.info('Model exists '+st_model_id);
var  formatBoolYN=function(value)
 {
	 return (value =='t'||value=='true'||value===true) ? 'Yes' : 'No';
 }
 var grid_class='Spectrum.grids.prizes' ;
if(!App.dom.definedExt( grid_class)){
Ext.define(grid_class,
{
	extend: 'Ext.Panel', //not a grid or form, just a plain panel.  
	//inside we will make a view which acts like a grid FOAIAP
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    category_id:-1,
    refresh:function()
    {
 
    	var post='category_id='+this.category_id;
    	
    	{YAHOO.util.Connect.asyncRequest('POST','index.php/prize/json_getprizes/'+App.TOKEN,{scope:this,success:function(o)
    	{
 
			var items=YAHOO.lang.JSON.parse(o.responseText);
			this.store.loadData(items);
			//var image_width = 130;//??75? or dymnaic?
			//IF YOU WANT HORIZONTAL SCROLLING NOT VERTICAL, DO THIS
		 // Ext.getCmp('id_for_resize_hack_scrollbars').setHeight(items.length/3 * image_width) ;
    	}},post);}
    	this.buildCategoryFilter();
	},
	/**
	* technically, its a Ext.view.View, aka a gridView (not gridpanel) but yeah same thign in meaning
	* this fn is to avoid having to clal something by id externally
	*/
	getGrid:function()
	{
		return Ext.getCmp('id_for_resize_hack_scrollbars');
	},
	constructor     : function(config)
    {  
    	var id='prize_dataview_grid_';
    	if(config.id){id=config.id;}
    	//var renderTo=config.renderTo;

		if(Ext.getCmp(id)){Ext.getCmp(id).destroy();}
		config.id=id;
		var hide_modify=false;
		if(typeof config.hide_modify != 'undefined')
			{hide_modify=config.hide_modify;}
		 var data = [];

        this.store=Ext.create( 'Ext.data.Store',{remoteSort:false,model:st_model_id,data:data});
        
         
		if(!config.title)config.title= 'Prizes';
		//,renderTo: renderTo,id:id,
		config.collapsible= true;
		config.frame= true;
		config.width=735;
		config.height= App.MAX_GRID_HEIGHT;
		
		config.layout='fit';
		config.autoHeight=true;
		config.autoScroll=true;
		///SCROLLBARS!!!!!!!!!
		config.items= Ext.create('Ext.view.View', 
		{
			id:'id_for_resize_hack_scrollbars',
	        store: this.store,
	        tpl: [
	            '<tpl for=".">',
	                '<div class="thumb-wrap" id="{filepath}">',
	                   '<div class="thumb"><img src="{thumb_filepath}" title="{filepath}"></div>',
	                    '<span class="x-editable">{short_name}</span>',
	                    '<span >From ${min_price}</span>',
	                    '<span >({total_stock} in stock)</span>',
	                '</div>',
	            '</tpl>',
	            '<div class="x-clear"></div>'
	        ],
	        multiSelect: false,
	        width:2000,
			height:155,
	        trackOver: true,
	        overItemCls: 'x-item-over',
	        itemSelector: 'div.thumb-wrap',
	        emptyText: 'No prizes to display',

	        prepareData: function(data) 
	        {
	            //not used currently
	           /* Ext.apply(data, {
	                shortName: Ext.util.Format.ellipsis(data.name, 15),
	                sizeString: Ext.util.Format.fileSize(data.size),
	                dateString: Ext.util.Format.date(data.lastmod, "m/d/Y g:i a")
	            });*/
	            return data;
	        },
	        listeners: 
	        {
	            selectionchange: function(dv, nodes )
	            { //not used currently
	                if(!nodes.length){return;}

	            }
	            , itemdblclick:function(view,record,html,index,e)
	            {
	                //same as prize.assets grid, open image in shadowbox
					var filepath=record.data['filepath'];
					if(filepath){
					Shadowbox.open(
					{
					    content:    filepath,
					    player:     "img",
					    title:      ""//,
					    //height:     450,
					   // width:      450
					});}
	            }
	        }
	    });

    	if(!config.bbar){config.bbar=[];}
		if(config.bbar.length)
			{config.bbar.push('-');}//if user gave any buttons, add a seperator after them

	   config.dockedItems=
		[
			{
			    dock:'top'
			    , xtype:'toolbar'
			    ,items://tbar
				[
		    		{
		    			xtype:'button'
		    			,id:'btn_cat_filter'
		    			,menu:[]//loaded after callParent 
		    			,text:'All Categories'
		    		}
				]
			}
		,{
		    dock:'bottom'
		    , xtype:'toolbar'
		    ,items://bbar
			[
		    	{
		    		tooltip:'Create New'
		    		,iconCls :'add'
		    		,id:'btn_prizes_add'
		    		,hidden:hide_modify
		    		,scope:this
		    		, handler:function()
		            {

						var window_id = 'newprize_win';
	                	var f  =Ext.create('Spectrum.forms.prizes',
	                	{
	                		window_id:window_id
	                		//, categories:categories['root']
	                	});//root to get around paginator stuff
	                	var win=Ext.create('Spectrum.windows.prizes',{items:f,id:window_id});
	                	
						win.on('hide',function(o)
						{
							if(typeof this.refresh == 'function')
							{
								this.refresh();
							}
						},this);//pass scope as grid
						win.show();		
		            }
				}
				,'-'
				,'Double click to view full-size image'
		    	,'->'
		    	,config.bbar
		    	
		    	,{
		    		tooltip:'Edit Details'
		    		,iconCls:'pencil'
		    		,disabled:false
		    		,scope:this
		    		,id:'btn_prizes_edit'
		    		,hidden:hide_modify
		    		,handler:function()
		    		{
 
						//var rows=Ext.getCmp('id_for_resize_hack_scrollbars').getSelectionModel().getSelection();
						var rows=this.getGrid().getSelectionModel().getSelection();
						if(!rows.length){return;}
				        var row=rows[0];
	                    
				        var window_id="prizewindow_edit_";//+Math.random();
				        //instead of loadRecord: pass record into form
				        var f=Ext.create('Spectrum.forms.prizes',{ window_id:window_id,record:row});
				        
				        //f.loadRecord(row);
				        
				        var win=Ext.create('Spectrum.windows.prizes',{items:f,id:window_id});
				        win.on('hide',function(o)
						{
							if(typeof this.refresh == 'function') this.refresh();
						},this);//pass scope as grid
						win.show();	
 
		    		}
		    		
		    	}
		    	,'-'
				,{
					tooltip:'Delete'
					,iconCls :'delete'
					,disabled:false
					,hidden:hide_modify
					,id:'btn_prizes_delete',//
		            scope:this
		            , handler:function()
		            {
	                	//var rows=Ext.getCmp('id_for_resize_hack_scrollbars').getSelectionModel().getSelection();
	                	var rows=this.getGrid().getSelectionModel().getSelection();
	                	if(!rows.length){return;}
	                	
	                	var row=rows[0];
	                	var name=row.get('name');
	                	var msg="This will delete Prize : "+name+" permanently, and remove all inventory and sizes for this prize.  "
	                	+"Instead you could disable the sizes for sale, or reduce the warehouse quantities to zero.  Continue with delete?";
	                	Ext.MessageBox.show({
	                		title:'Delete?',
	                		msg:msg,
	                		scope:this,
	                		 buttons: Ext.MessageBox.YESNO,
	                		fn:function(btn_id)
	                		{
								if(btn_id!='ok'&&btn_id!='yes'){return;}
								
	                			var rows=this.getGrid().getSelectionModel().getSelection();
	                			var row=rows[0];
	                			//.log(row);
	                			var post='prize_id='+row.get('prize_id');
	                			YAHOO.util.Connect.asyncRequest('POST','index.php/prize/post_delete_prize/'+App.TOKEN,
	                			{scope:this,failure:App.error.xhr,success:function(o)
	                			{
	                				var r=o.responseText;
	                				if(isNaN(r)||r<1 )
	                					{Ext.MessageBox.alert('Error:'+r);}
	                				else
	                					{this.refresh();}
	                				//Ext.getCmp('images-view').refresh();
	                			}},post)
	                		}});
					}
					
				}
			
			
		  	]
		}
		];//end of dockedItems
		
		config.bbar=null;//so we do not duplicate it
        this.callParent(arguments); 
		this.refresh();//auto load data on create
	}
	,buildCategoryFilter:function()
	{
 
		var url='index.php/prize/json_getcategories/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('GET',url,{scope:this,success:function(o)
		{
			var wh=YAHOO.lang.JSON.parse(o.responseText);
			if(typeof wh['root'] != 'undefined') wh=wh['root'];//skip over paginator data
			var itemClick=function(o,e)
			{
        		Ext.getCmp('btn_cat_filter').setText(o.text);
				this.category_id=o.value;
				this.refresh();
			};
			var filter=new Array();
			 
			for(i in wh) if(wh[i])
			{
				w=wh[i];
				if(w['prize_count']==0){continue;}
				name=w['category_name']+ " ("+w['prize_count']+")";
				id  =w['category_id'  ];
				//def =w['is_default'];
				filter.push({text:name,value:id,handler:itemClick,scope:this});
			}
			filter.push({text:'All Categories',value:-1,handler:itemClick,scope:this});
			Ext.getCmp('btn_cat_filter').menu=Ext.create('Spectrum.btn_menu',{items:filter});
		}});
	}
	
});
}//else console.info('Grid exists '+grid_class);


