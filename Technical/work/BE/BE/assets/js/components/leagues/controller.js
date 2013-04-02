var c_leagues_manage= function(){this.construct();};
c_leagues_manage.prototype=
{
     grid               :null,       
     users_grid			:null,
     construct:function()
     {
 
     }, 
                              
     load_grid:function( )
     {  
	 	//create the grid
        leagues_manage.grid = Ext.create('Ext.spectrumgrids.leagues',
        {
            renderTo        : "leagues-manage-list",
            title           : 'Leagues',
            bbar:[
            {//pass in the user button
 				//button cannot exist in grid by default, since it references external objects (this controller, users grid, etc)
                iconCls :'fugue_user--pencil',
                xtype: 'button',
                text: '',
                tooltip: "Manage Selected League's Users",       
                handler: function()
                {   
                    //if(!leagues_manage || !leagues_manage.users_grid) {return;}//if it doesnt exist , dont break the script 
                	 
                    var selection = leagues_manage.grid.getSelectionModel().getSelection();
                    if(selection.length==0)
                    {
                        //Ext.MessageBox.alert({title:"Warning",msg:"No League Record Selected", icon: Ext.Msg.WARNING,buttons: Ext.MessageBox.OK});
                        return;
                    }
                    var record = selection[0].data;
                    //.log(record);
                    leagues_manage.grid.collapse();   
                    leagues_manage.users_grid.org_id=record.org_id;
                    leagues_manage.users_grid.refresh();      
                    leagues_manage.users_grid.setTitle('Users for '+record.league_name);
                    leagues_manage.users_grid.show();      
                    leagues_manage.users_grid.expand();      
                }
                    
             }]
            ,extraParamsMore : {active:'all'}
        });
 
        leagues_manage.grid.on("expand",function()
        {                 
            if(leagues_manage.users_grid)
            {
                leagues_manage.users_grid.hide();          
            }
        	this.setHeight(App.MAX_GRID_HEIGHT);
			this.doLayout();   
			this.getStore().load();//refresh on show  
        }); 
        leagues_manage.grid.on('itemmouseenter', function(view, record, HTMLElement , index, e, Object ) 
        {                                                                                                                     
           view.tip = Ext.create('Ext.tip.ToolTip', 
            {
                target: view.getEl(),
                delegate: view.itemSelector,
                trackMouse: true,
                anchor  :'right',
                listeners: 
                {
                    beforeshow: function(tip) 
                    {
                        var record=view.getRecord(tip.triggerElement).data;
                        if(record.league_users_count=='0')    
                            tip.update('<img src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/error.png />  No League Manager assigned ');
                        else 
                            return false;
                    }
                }
            });
        }); 
 
     },
     
     
     create_users_grid:function()
     {
		 this.users_grid =Ext.create('Spectrum.grids.org_users',{renderTo:'leagues-users-list',id:'league_users_',org_type:3});//org type 3 for league
		 this.users_grid.hide();
		 this.users_grid.on('collapse',function()
		 {
			leagues_manage.grid.expand();
			leagues_manage.grid.store.load();
			this.hide();
		});
		this.users_grid.on('expand',function()
		{ 
			this.setHeight(App.MAX_GRID_HEIGHT);
			this.doLayout();
		});
     }
} 
var leagues_manage=new c_leagues_manage();
//create both grids right away                                                                                      
 leagues_manage.load_grid();             
 leagues_manage.create_users_grid();                                
                                                       
                                                         