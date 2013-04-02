if(!App.dom.definedExt('Ext.spectrumgrids.blackout')){
Ext.define('Ext.spectrumgrids.blackout',    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
         this.callParent(arguments);
    }
    ,refresh:function()
    { 
	    this.getStore().proxy.extraParams.season_id=this.season_id ;
		this.getStore().load();
		
    }
    ,constructor : function(config) 
    {   
        config.columns= 
        [
        	{
                dataIndex   : 'bo_user_desc'
                , header    : 'Description'
                , sortable  : true
                ,flex:1
            }, 
            {
                dataIndex   : 'bo_start_date'
                , header    : 'Start Date'
                ,renderer: Ext.util.Format.dateRenderer('M j, Y')
                , sortable  : true
                ,width:120
            },                
            {
                dataIndex   : 'bo_end_date'
                , header    : 'End Date'
                ,renderer: Ext.util.Format.dateRenderer('M j, Y')
                , sortable  : true
                ,width:120
            },
            {
                dataIndex   : 'bo_type_name'
                , header    : 'Type'
                , sortable  : true
                ,width:150
            }
        ];
        config.dockedItems =
        [
            {
                dock: 'top',
                xtype: 'toolbar',
                items:[{xtype:'button',menu:[],id:'blackout_season_btn',text:'Select a Season to Begin'}]
            } 
            //Bottom Action toolbar
            ,{
	            dock: 'bottom',
	            xtype: 'toolbar',

	            items: 
	            [
	                {
                		id: 'blackout_new_btn',
                		iconCls     : 'add',
                		xtype       : 'button',
                		text        : '',
                		scope:this,
                		tooltip     : 'Create New Blackout',
                		handler: function()
		                {
		                    if(!this.season_id || this.season_id==-1)
		                    {
								Ext.MessageBox.alert('Select a Season first','Each set of blackout dates is attached to one Season');
								return;
		                    }
		                    var window_id='blackout_formwindow__';
		                    if(Ext.getCmp(window_id)) Ext.getCmp(window_id).destroy();
		                    
		                    
		                    var form_bo_details=   Ext.create('Spectrum.forms.blackout',{season_id:this.season_id,window_id:window_id});
							var win_bo_details= Ext.create('Spectrum.windows.blackout', 
		                    {
                    			id:window_id,
		                        title       :"Create Blackout",
		                        items       :form_bo_details
		                    });     
		                    win_bo_details.on('hide',function(){this.refresh();},this);                   
		                    win_bo_details.show();  
			            }   
		        }
			        ,'->'
			        ,{
		                id          : 'blackout_edit_btn',
		                iconCls     : 'pencil',
		                xtype       : 'button',
		                scope:this,
		                text        : '',
		                tooltip     : 'Edit Blackout',
		                handler: function()
		                {
                			var sel=this.getSelectionModel().getSelection();
		                    if(sel.length==0)
		                    {

		                        return;
		                    }
		                    
		                    var window_id='edit_blackout_window_';
		                    if(Ext.getCmp(window_id)) Ext.getCmp(window_id).destroy();
		                    var record=sel[0];
		                    var form_bo_details=   Ext.create('Spectrum.forms.blackout',{season_id:this.season_id,window_id:window_id});
		 
		                    form_bo_details.loadRecord(record);
		                    var win_bo_details= Ext.create('Spectrum.windows.blackout', 
		                    {
                    			id:window_id,
		                        title       :"Update Blackout",
		                        items       :form_bo_details
		                        
		                    });     
		                    win_bo_details.on('hide',function(){this.refresh();},this);                     
		                    win_bo_details.show(); 

		                }}
		            ,'-'
		            ,{
	                id          : 'blackout_delete_btn',
	                iconCls     : 'delete',
	                xtype       : 'button',
	                text        : '',
	                tooltip     : 'Delete Blackout',
	                scope:this,
	                handler: function()
	                {
		                var sel=this.getSelectionModel().getSelection();
				        if(sel.length==0)
				        { 
				            return;
				        }

	                  
	                  Ext.MessageBox.confirm('Delete Blackout Dates', "Are you sure?  This cannot be reversed.", function(answer)
	                  {     
	                      if(answer!="yes" && answer!='ok'){ return;}
 
		                  var post={};
		                  post['bo_id']=sel[0].get('bo_id');//. 
	                       
	                      Ext.Ajax.request(
	                      {
	                          url: 'index.php/leagues/json_delete_blackout/'+App.TOKEN,
	                          params: post,scope:this,
	                          
	                          success: this.refresh
	                          /*
	                          {
	                              
	                               var res=YAHOO.lang.JSON.parse(response.responseText);
	                               if(res.result=="1")
	                               {
	                                   this.refresh();   
	                               }
	                          }*/
	                          ,failure:App.error.xhr
	                      });    
	                      
	                  },this);          
	                }
	        }
		        ]
	    	} 
        ];     
 		if(!this.season_id) this.season_id=-1;
        if(config.pageSize==null)   config.pageSize     = 100;
        if(config.url==null)        config.url          = "/index.php/leagues/json_get_blackouts/"+App.TOKEN;
 		config.store =Ext.create( 'Ext.data.Store',
    	{
    		model:"Blackout",autoDestroy:false,autoSync :false,autoLoad :true,remoteSort:false,pageSize:config.pageSize ,
            proxy: 
            {   
            	type: 'rest',url: config.url,
                reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
                extraParams:{season_id:this.season_id}
            }    

	    });
 
 		//we want search and pager
		config.bottomPaginator =false;
		config.searchBar       =true;
 
		
        this.callParent(arguments); 

        this.init_seasons_list();
    }, 
    season_id:-1,          
    season_name:'Select a Season',          
	init_seasons_list:function()
	{
     	//now make the season button menu
     	var season_url='index.php/season/json_active_league_seasons/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('GET',season_url,{scope:this,success:function(o)
		{
			var name;
            var id;
            var seasons=YAHOO.lang.JSON.parse(o.responseText).root;
			
			var seasons_filter=new Array();	
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;
 
				this.season_id=id;
		         this.refresh(); 
        		Ext.getCmp('blackout_season_btn').setText(name);
			};
			//one item for no season
			//seasons_filter.push({text:'Unassigned',value:-1,handler:itemClick,scope:this,iconCls:'layout'});
			var icon,foundActive=false;				
			for(i in seasons) if(seasons[i])
			{
				name=seasons[i]['season_name']+" : "+seasons[i]['display_start']+" - "+seasons[i]['display_end'];
				id  =seasons[i]['season_id'];
				if(!id) continue;
				icon=seasons[i]['isactive_icon'];
				
				if(seasons[i]['isactive']=='t'||seasons[i]['isactive']=='true'||seasons[i]['isactive']===true)
				{ 
					foundActive={text:name,value:id};
				}
 
        		seasons_filter.push({text:name,value:id,handler:itemClick,scope:this,iconCls:icon});
			}
			Ext.getCmp('blackout_season_btn').menu=Ext.create('Spectrum.btn_menu',{items:seasons_filter});
			if(foundActive)//select one of them by default 
			{
				//itemClick(foundActive,null);//fireEvent simulated ,and its scope independent
				this.season_id=foundActive.value;
				 this.store.proxy.extraParams.season_id=this.season_id ;
				this.getStore().load();
        		Ext.getCmp('blackout_season_btn').setText(foundActive.text);
			}
		}});
		}
});
}