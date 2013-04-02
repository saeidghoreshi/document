 
var st_model_id='PrizeAsset';

var  formatIntYN=function(value)
 {
	 return (!value || value=='0') ? 'No' : 'Yes';
 }
 var grid_class='Spectrum.grids.prize.assets' ;
if(!App.dom.definedExt( grid_class)){
Ext.define(grid_class,
{
	extend: 'Ext.Panel', //not a grid 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    prize_id:-1,
    refresh:function()
    {
    	var post='prize_id='+this.prize_id;
    	
    	YAHOO.util.Connect.asyncRequest('POST','index.php/prize/json_prize_assets/'+App.TOKEN,{scope:this,success:function(o)
    	{
    		 
			var items=YAHOO.lang.JSON.parse(o.responseText);
			this.store.loadData(items);
			//var image_width = 130;//??75? or dymnaic?
			//IF YOU WANT HORIZONTAL SCROLLING NOT VERTICAL, DO THIS
		 // Ext.getCmp('id_for_resize_hack_scrollbars').setHeight(items.length/3 * image_width) ;
    	}},post);

	},

	constructor     : function(config)
    {   
    	this.prize_id=config.prize_id;
    	var id='prizeasset_dataview_grid_';
    	if(!config.id){config.id=id;}
    	var renderTo=config.renderTo;
    	
    	//var buttons=[];
    	//if(config.bbar){buttons=config.bbar;}
		//if(buttons.length)
		//	{buttons.push('-');}//if user gave any buttons, add a seperator after them
		if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		 var data = [  ];

        this.store=Ext.create( 'Ext.data.Store',{remoteSort:false,model:st_model_id,data:data});
         config.store=this.store;
		config.title= 'Images';
			//,renderTo: renderTo,
			 
		config.collapsible= true;
		config.frame= true;
		config.width=735;
		config.height= App.MAX_GRID_HEIGHT;
		config.layout='fit';
		config.autoHeight=true;
		config.autoScroll=true;///SCROLLBARS!!!!!!!!!
		config.items= Ext.create('Ext.view.View', 
			{
				id:'prize_asset_view_internal',
	            store: this.store,
	            tpl: [
	                '<tpl for=".">',
	                    '<div class="thumb-wrap" id="{filepath}">',// the rel=shadowbox does not work, moved to itemdblclick in listeners
	                      // '<div class="thumb"><a href="{filepath}" rel="shadowbox" title="{filepath}"><img src="{filepath}" title="{filepath}"></a></div>',
	                        '<div class="thumb"><img src="{thumb_filepath}" title="{filepath}"></div>',
	                        '<span class="x-editable">{name}</span>',
	                        '<span >{is_default_display}</span>',
	                        '<span >(Uploaded: {created_on})</span>',
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
	            emptyText: 'No images found',

	            prepareData: function(data) 
	            {
	                return data;
	            },
	            listeners: 
	            {
	            	//on double click, use shadow box to display image
	                itemdblclick:function(view,record,html,index,e)
	                {
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
	        });//end of View
	        

		   config.dockedItems=
		    [
		   
		    {dock:'bottom', xtype:'toolbar',items://bbar
		    [
		    	{
		    		tooltip:'Upload New Image',
		    		iconCls :'add',
		    		id:'btn_assets_add',
		    		scope:this, 
		    		handler:function()
		            {
	            		var window_id= 'btn_assets_add_window';
		                var f=Ext.create('Spectrum.forms.prize.assets',{ prize_id:this.prize_id,window_id:window_id});
		                 
		                var win=Ext.create('Spectrum.windows.prize.assets',{items:f,id:window_id});
		                 
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
		    	,{tooltip:'View Full Size',iconCls:'photo',hidden:true,scope:this,handler:function()
		    	{
		    		//this still works, it was just permanently hidden since it was 
		    		//replaced by SHADOWBOX
		    		//leaving code here in case shadowbox breaks, orw e need this for some other reason
		    		var rows=Ext.getCmp('prize_asset_view_internal').getSelectionModel().getSelection();
					if(!rows.length){return;}
		            var row=rows[0];
		            var source=row.get('filepath');
		            //.log(source);
					var w=Ext.create('Ext.window.Window',{
						title: 'Prize Asset',closable:true,resizable:true,
						closeAction:'hide',//default is destroy, we want to only hide
						width: 500,height: 500,x: 80,y: 90,
						plain: true,modal:true,headerPosition: 'top',layout: 'fit',
						items: 
						[
						{
						   html: '<img src="'+source+'">'
						}  
						]
					});
					w.show();
		    	}}		    	
		    	,{
		    		tooltip:'Make Default',
		    		iconCls:'tick',
		    		scope:this,
		    		handler:function()
		    		{
						var rows=Ext.getCmp('prize_asset_view_internal').getSelectionModel().getSelection();
						if(!rows.length){return;}
			            var row=rows[0];
			            var post='asset_id='+row.get('asset_id')+'&prize_id='+this.prize_id;
			            //.log(post);
			            var url='index.php/prize/post_assign_default_image/'+App.TOKEN;
			            var callback={scope:this,success:this.refresh};
			            YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
		    		}
		    	}
		    	,'-'
				,{
					tooltip:'Delete Image',
					iconCls :'delete',
					disabled:false,
					id:'btn_assets_delete',
	                scope:this, 
	                handler:function()
	                {
	                	var rows=Ext.getCmp('prize_asset_view_internal').getSelectionModel().getSelection();
	                	if(!rows.length){return;}
	                	var msg="Remove this image permanently from this prize?";
	                	Ext.MessageBox.confirm('Delete?',msg,function(btn_id)
						{
 							if(btn_id!='ok' && btn_id!='yes') {return;}//extjs defiens the btn_id values
 							
	                		var rows=Ext.getCmp('prize_asset_view_internal').getSelectionModel().getSelection();
	                		var row=rows[0];
	                		
	                		var post='asset_id='+row.get('asset_id');
	                		var url='index.php/prize/post_delete_asset/'+App.TOKEN;
				            var callback={scope:this,success:this.refresh};
				            YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
						},this);
					}
				}
		    
		  	]}
		   ];//end of dockedItems
		
        this.callParent(arguments);
		
		this.refresh();//auto load data on create
	}

	
});
}//