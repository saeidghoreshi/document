
if(!App.dom.definedExt('Ext.spectrumgrids.season')){//fixes lots of bugs
Ext.define('Ext.spectrumgrids.season',    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) {       this.callParent(arguments);}
    ,constructor : function(config) 
    {       
        //defaults:
        if(typeof config.collapsible=='undefined') config.collapsible=false;
        if(typeof config.rowEditable=='undefined') config.rowEditable=true;
        if(typeof config.bottomPaginator=='undefined') config.bottomPaginator=false;
        if(typeof config.searchBar=='undefined') config.searchBar=true;
        
        config.columns  =
        [
        {
            dataIndex: 'season_name'
            , header: 'Season Name'
            , sortable: true
            , flex: 1
            , editor: { allowBlank: false }
        },
        {
            header:'Season'
            , columns:
            [
                {
                    dataIndex: 'isactive_display'
                    , header: 'Active'
                    , width: 70
                    , editor: { xtype: 'combobox', editable:false,
                        store: [ ['Active' ,'Active']
                            	,['Inactive','Inactive']]}
                }
                ,{
                    dataIndex: 'effective_range_start'
                    , header: 'Start'
                    , sortable : true
                    , width: 90
                    , format: 'Y/m/d'
                    , field: { xtype: 'datefield', format: 'Y/m/d', allowBlank: false}
                },
                {
                    dataIndex: 'effective_range_end'
                    , header: 'End'
                    , sortable : true
                    , width: 90
                    , format: 'Y/m/d'
                    , field: { xtype: 'datefield', format: 'Y/m/d', allowBlank: false}
                }
            ]
        },
        {
            header:'Registration'
            , columns:
            [
                {
                    dataIndex: 'is_enabled_display'
                    , header: 'Enabled'
                    , width: 70
                    , editor: { 
                    	xtype: 'combobox', 
                    	editable:false,
                        store: [['Enabled','Enabled'],['Disabled','Disabled']]
                    }
                },
                {
                    dataIndex: 'reg_range_start'
                    , header: 'Start'
                    , sortable : true
                    , width: 90
                    , format: 'Y/m/d'
                    , field: { xtype: 'datefield', format: 'Y/m/d', allowBlank: true}
                },
                {
                    dataIndex: 'reg_range_end'
                    , header: 'End'
                    , sortable : true
                    , width: 90
                    , format: 'Y/m/d'
                    , field: { xtype: 'datefield', format: 'Y/m/d', allowBlank: true}
                }
            ]}
        ];
        //Fresh top/bottom components list
        if(!config.dockedItems)config.dockedItems=new Array();
        //top bar
		config.dockedItems.push(
		{dock: 'top',xtype: 'toolbar',//tbar
	        items://tbar
	        [
	        	//filters go here if any
	        ]
		});
       //bottom bar
       if(!config.bbar) config.bbar=[];
       else config.bbar.push('-');//if bbar WAS given, then add a seperator after those items
       config.dockedItems.push(
		{dock: 'bottom',xtype: 'toolbar',
			items://bbar
	        [
            {
                text: ''
                ,iconCls: 'seasons_add'
                ,tooltip: 'Create New Season'
                ,scope:this
                ,handler : function()
                {
                    var window_id='season_window_id';
                    var f=Ext.create('Spectrum.forms.season',{window_id:window_id});

                    var w=Ext.create('Spectrum.windows.season',{items:f,id:window_id});
                    
                    w.on('hide',function(){this.getStore().load();},this);
                    w.show();
                }
            }
            
			
            //Added by Ryan
            //Custom Fields Definition  (Add/Edit/Remove)
            ,{
                text    : '',
                iconCls : 'fugue_clipboard-text',
                tooltip : 'Manage Registration Form Fields',
                handler : function()
                {
                   //Build  customFilldsGrid
                   var cfg_config=
                   {
                        generator       : Math.random(),
                        owner           : this,
                        
                        title           : 'Registration Custom Field Management',
                        extraParamsMore : {},
                        collapsible     : false,
                        
                        //customized Components
                        rowEditable     :true,
                        groupable       :true,
                        bottomPaginator :false,
                        searchBar       :false,    
                        //Function appendable or overridble
                        
                        override_edit           :false,
                        override_itemdblclick   :false,
                        override_selectionchange:false,
                        override_expand         :false,
                        override_collapse       :false
                        
                        
                    }
                   var cfg= Ext.create('Ext.spectrumgrids.customfields',cfg_config);
                    
                   //End
                   
                   var _config=
                   {
                       width   : 400,
                       height  : 400,
                       bottomItems:   
                       [
                           '->'
                           ,{   
                                xtype   :"button",
                                text    :"Close",
                                tooltip :'Close',
                                handler :function()
                                {                     
                                    win.Hide();
                                }                          
                           }
                       ]
                   }                                                      
                   var final_form=new Ext.spectrumforms.mixer(_config,[cfg],['grid']);
                   var win_cnf=
                   {
                       title       : '',
                       final_form  : final_form
                   }
                   var win=new Ext.spectrumwindow.registration(win_cnf);
                   win.show();
                }
            }
            ,'-'
            ,'Double Click Row to Edit'
            ,'->'
            ,config.bbar//all buttons input by the controller go here
             
            ,{
                id:'btn-season-edit'
                , disabled:true
                , iconCls: 'seasons_edit'
               // , iconCls: 'pencil'
                ,tooltip: 'Modify Season Details'
                ,scope:this
                ,handler : function()
                {
                    var selected=this.getSelectionModel().getSelection();
                    if(selected.length==0) {return;}
                    var sel=selected[0];    

                    //.log(sel.data);
                    
                    //fixed model so no random model needed. see /js/models/season.js
                    var window_id='season_window_id';
                    var form_season_details=Ext.create('Spectrum.forms.season',{window_id:window_id});
 
                    //put data in window form and show
                    form_season_details.loadRecord(sel);
                    
                    var w=Ext.create('Spectrum.windows.season',{items:form_season_details,id:window_id});
	                w.on('hide',function(){this.getStore().load();},this);
	                w.show();
                }
            },
          
            {
                id:'btn-season-delete'
                , disabled:true
                ,scope:this
                , iconCls: 'fugue_minus-button'
                ,tooltip: 'Remove Season'
                ,handler : function()
                {
                    var selected=this.getSelectionModel().getSelection();
                    if(selected.length==0) return;
                    Ext.MessageBox.confirm('Delete this Season?',
                    		'Deleting this season is a permanent action. Are you sure?', 
                    		function(answer)
                    {     
                        if(answer=="yes")
                        {
 
                            
                            var post={};
                            post["season_id"]=selected[0].get('season_id');
                             
                            Ext.Ajax.request(
                            {
                                url: 'index.php/season/post_delete_season/TOKEN:'+App.TOKEN,
                                params: post,
                                scope:this,
                                failure:App.error.xhr,
                                success: function(response)
                                {
 
                                     var res=YAHOO.lang.JSON.parse(response.responseText);
                                     if(res.result=="1")
                                     {
                                         this.getStore().load();
                                     }
                                     else //if(res.result=="0")
                                     {
                                        Ext.MessageBox.alert('Cannot delete this season.',res.result);
 
                                     }
                                }
                            },this);    
                        }
                    },this); 
                }
            }      
     	]});
		config.bbar=null;//clear out the bbar or else it will get doublerendered by parent
 
         
        if(config.url==null)        config.url          = 'index.php/season/json_active_league_seasons/'+App.TOKEN;
        
 
         
 		config.store =Ext.create( 'Ext.data.Store',
    	{
    		model:"SeasonModel",
    		autoDestroy:false,
    		autoSync :false,
    		autoLoad :true,
    		remoteSort:false, 
            proxy: 
            {   
            	type: 'rest',url: config.url,
                reader: {type: 'json',root: 'root',totalProperty: 'totalCount'}
 
            }    

	    });
	    
        if(config.width==null)      config.width        = '100%';
 
		if(!config.listeners) config.listeners={};
        config.listeners.edit=
        {scope:this,fn:function(e)
			{
				e.record.commit(); 
				var data = e.record.data;
				//pass all the data
				//uses same php call as create season, or update season from the form.  all consistent.
                
               // var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                Ext.Ajax.request({
                    url: "index.php/season/json_new_season/"+App.TOKEN,//name is misleading, it is edit or udpate depending
                    //on if season id exists or not
                    params: data,
                    scope:this,
                    success: function(response)
                    {
                      //   box.hide();
                         this.getStore().load();  
                    }
                    ,failure:function(response)
                    {
                        //me.getStore().load();
                        App.error.xhr(response);
                    }
                },this); 
            }
		}//end of edit 
		//}//end of listeners

        this.callParent(arguments); 
    }                       
        
});
}
