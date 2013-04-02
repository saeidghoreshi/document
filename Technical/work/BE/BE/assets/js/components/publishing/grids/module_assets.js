 
var st_model_id='WebsiteAsset';

 
 var grid_class='Spectrum.grids.websites.module_asset' ;
if(!App.dom.definedExt( grid_class)){
Ext.define(grid_class,
{
	extend: 'Ext.Panel', //not a grid 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
   // org_id:-1,//org id is always active org
    w_m_id:-1,
    refresh:function()
    {
    	var post='w_m_id='+this.w_m_id;
    	
    	YAHOO.util.Connect.asyncRequest('POST','index.php/websites/json_module_assets/'+App.TOKEN,{scope:this,success:function(o)
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
    	var id='webasset_dataview_grid_';
    	if(!config.id){config.id=id;} 
		if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		//if(Ext.getCmp('website_asset_view_internal')){Ext.getCmp('website_asset_view_internal').destroy();}
		
		 var data = [  ];

        this.store=Ext.create( 'Ext.data.Store',{remoteSort:false,model:st_model_id,data:data});
         config.store=this.store;
		config.title= 'Images';
 
		config.collapsible= true;
		config.frame= true;
		config.width=735;
		config.height= App.MAX_GRID_HEIGHT;
		config.layout='fit';
		config.autoHeight=true;
		config.autoScroll=true;///SCROLLBARS!!!!!!!!!
		config.items= Ext.create('Ext.view.View', 
			{
				id:'website_asset_view_internal',
	            store: this.store,
	            tpl: [
	                '<tpl for=".">',
	                    '<div class="thumb-wrap" id="{filepath}" >',// the rel=shadowbox does not work, moved to itemdblclick in listeners
	                      // '<div class="thumb"><a href="{filepath}" rel="shadowbox" title="{filepath}"><img src="{filepath}" title="{filepath}"></a></div>',
	                        '<div class="thumb"><img src="{filepath}" title="{url}"></div>',
	                        '<span class="x-editable">{url}</span>',
	                       // '<span >{is_default_display}</span>',
	                        //'<span >(Uploaded: {created_on})</span>',
	                    '</div>',
	                '</tpl>',
	                '<div class="x-clear"></div>'
	            ],
	            multiSelect: true,//was false
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
		    		tooltip:'Upload Image Link',
		    		iconCls :'add',
		    		id:'btn_webassets_add',
		    		scope:this, 
		    		handler:function()
		            {
	            		var window_id= 'btn_assets_add_window';
		                var f=Ext.create('Spectrum.forms.websites.module_assets',{ w_m_id:this.w_m_id,window_id:window_id});
		                 
		                var win=Ext.create('Spectrum.windows.websites.module_assets',{items:f,id:window_id});
		                 
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
 				,{
					tooltip:'Swap Image Order',
					iconCls :'arrow_right', 
	                scope:this, 
	                handler:function()
	                {
	                	var rows=Ext.getCmp('website_asset_view_internal').getSelectionModel().getSelection();
	                	if(!rows.length){return;}
	                	if(rows.length != 2 )
	                	{
							Ext.MessageBox.alert("Cannot Swap",'Please hold "ctrl" and click to select two images to swap');
							return;
	                	}
	                	var post={};
	                	post.first_asset=rows[0].get('module_asset_id');
	                	post.w_m_id=rows[0].get('w_m_id');//this is the same for both
	                	post.second_asset=rows[1].get('module_asset_id');
	                	Ext.Ajax.request({
							scope:this
							,method:'POST'
							,params:post
							,url:'index.php/websites/post_swap_assets/'+App.TOKEN
							,success:this.refresh
							,failure:App.error.xhr
                        });
	                	
					}
					
 				}
 				,{
					tooltip:'Change Image URL',
					iconCls :'pencil',
					disabled:false,
				//	id:'btn_webassets_delete',
	                scope:this, 
	                handler:function()
	                {
	                	var rows=Ext.getCmp('website_asset_view_internal').getSelectionModel().getSelection();
	                	if(!rows.length){return;}
	                	//instead of a form just use a simple prompt
	                	//its only one item
	                	var old = rows[0].get('url');
	                	var module_asset_id = rows[0].get('module_asset_id');
	                	
	                	Ext.MessageBox.prompt(
	                		'Update or Replace website link for this image'
	                		,''
	                		,function(btn_id,text)
	                        {
                        		if(btn_id != 'ok' && btn_id != 'yes') {return;}
                        		
                        		var post={};
                        		post.url=escape(text);
                        		post.module_asset_id=module_asset_id;
 
                        		Ext.Ajax.request({
									scope:this
									,method:'POST'
									,params:post
									,url:'index.php/websites/post_asset_url/'+App.TOKEN
									,success:this.refresh
									,failure:App.error.xhr
                        		});
	                		
							}
							,this
							,false
							,old//default value is current
						);
					}
				}
		    	,'-'
		    	
				,{
					tooltip:'Remove Image Link',
					iconCls :'delete',
					disabled:false,
				//	id:'btn_webassets_delete',
	                scope:this, 
	                handler:function()
	                {
	                	var rows=Ext.getCmp('website_asset_view_internal').getSelectionModel().getSelection();
	                	if(!rows.length){return;}
	                	var msg="Remove this image permanently?";
	                	Ext.MessageBox.confirm('Delete?',msg,function(btn_id)
						{
 							if(btn_id!='ok' && btn_id!='yes') {return;}//extjs defiens the btn_id values
 							
	                		var rows=Ext.getCmp('website_asset_view_internal').getSelectionModel().getSelection();
	                		var row=rows[0];
	                		
	                		var post='module_asset_id='+row.get('module_asset_id');
	                		var url='index.php/websites/post_delete_module_assets/'+App.TOKEN;
				            var callback={scope:this,success:this.refresh,failure:App.error.xhr};
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