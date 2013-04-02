var Season= function(){this.construct();};
Season.prototype=
{
     grid               :null, 
     grid_teamassignment:null,
     
     grid_reg:null,  
     grid_assigned:null,
     grid_unassigned:null,
     construct:function()
     {      
         this.init(); 
     }, 
     init:function()
     {
        var _generator=Math.random(); 
        
        var config=
        {
            generator       : _generator,
            pageSize        : 100,
            renderTo        : "ctr-dg-seasons",
            title           : 'Seasons',
            extraParamsMore : {},
            collapsible     :true,
            
            //add the buttons to the grid that link the different girds together
            bbar:[
            {
                id:'btn-season-registration'
                , disabled:true

                , iconCls: 'fugue_clipboard-users'
                ,tooltip: 'Manage Team Registrations'
                ,handler : function()
                {
                    var selected=oSeason.grid.getSelectionModel().getSelection();
                    if(selected.length==0) return;
                    var sel=selected[0];
                    
                    var g1=oSeason.init_reg(sel.get('season_id'));
                    
                    g1.show();
                    oSeason.grid.collapse();
                }
	        },
            {
                id:'btn-season-assign'
                , disabled:true
                , iconCls: 'fugue_category-users'
                ,tooltip: 'Assign Teams to Divisions'
                ,handler : function()
                {
                	var selected=oSeason.grid.getSelectionModel().getSelection();
                    if(selected.length==0) return;
                    oSeason.show_assignment();
                    oSeason.grid.collapse();
                }
            }
        ]};
     		
                
        this.grid = Ext.create('Ext.spectrumgrids.season',config);

        this.grid.on("expand",function()
        {
            if(oSeason.grid_reg)       oSeason.grid_reg.hide();
            if(oSeason.form_assignment)oSeason.form_assignment.hide()
            
            this.setHeight(App.MAX_GRID_HEIGHT);
			this.doLayout();
        });
        this.grid.on("selectionchange",function()
        {         
            Ext.getCmp('btn-season-registration').enable();

            // season edit buttons
            Ext.getCmp('btn-season-assign').enable();
            Ext.getCmp('btn-season-edit').enable();
            Ext.getCmp('btn-season-delete').enable();
        });
            
        
     },
        
     init_reg:function(_season_id)
     {
        var me=this; 
        var _generator=Math.random(); 
        
        var config=
        {
            generator       : _generator,
            owner           : me,
            flex:1,
            pageSize        : 100,
            renderTo        : "ctr-dg-registrations",
            title           : 'Registration',
            extraParamsMore : {season_id:_season_id},
            collapsible     :true,
            
           
            //customized Components
            rowEditable     :true,
            groupable       :true,
            bottomPaginator :true,
            searchBar       :true
            
        }
            
        me.grid_reg= Ext.create('Ext.spectrumgrids.registeredteams',config);

        me.grid_reg.on("expand",function()
        {
			this.setHeight(App.MAX_GRID_HEIGHT);
			this.doLayout();
			
        });           
        me.grid_reg.on("collapse",function()
        {
            me.grid_reg.hide();
            me.grid.show();            
            me.grid.expand();
        });           
        return me.grid_reg;
     },
     
     
     form_assignment:null,
     show_assignment:function()
     {
        var me=this;
        var records = me.grid.getSelectionModel().getSelection();
        if(!records.length){return;}
         var _season_id= records[0].data.season_id;
         oSeason.init_unassigned(_season_id); 
         oSeason.init_assigned(_season_id); 

       //  oSeason.form_assignment=new Ext.spectrumforms.mixer(config,[oSeason.grid_unassigned,oSeason.grid_assigned],['grid','grid']);
         
         //mixer has strange looking borders in IE for this componnet; now using form with
         //column layout.  Sam, jan 18th, 2012
  
         //oSeason.form_assignment=Ext.create('Ext.spectrumforms.mixer',
         oSeason.form_assignment=Ext.create('Ext.spectrumforms',
          {
         	 
             renderTo       :'ctr-dg-teamassignment-mixer'
             ,force_border:true
             ,layout: {
				type: 'hbox',
				//pack: 'start',
				align: 'stretch'
			}
             
             
             ,collapsible   :true
             ,height        :340
             ,width         :"100%"
 
             ,title:'Assign Teams to Divisions'
             ,items:
             [oSeason.grid_unassigned	,oSeason.grid_assigned   	]
             ,bottomItems:   
             [
                 "->"
                  ,{
                     xtype  :"button",
                     text   :"Reset",
                     width  :100,
                     //pressed:true,
                     cls:'x-btn-default-small',
                     handler:function()
                     {
                         me.grid_unassigned.getStore().load();
                         me.grid_assigned.getStore().load();
                     }
                     }
                     ,{
                         xtype  :"button",
                         text   :"Save",
                         width  :70,
                         iconCls:'disk',
                         //pressed:true,
                         cls:'x-btn-default-small',
                         handler:function()
                         {
                             /*
                             var teams_info='';
                             for (var i=0;i<me.grid_unassigned.getStore().getCount();i++)
                             {
                                 var data=me.grid_unassigned.getStore().data.items[i].data;
                                 teams_info+='['+data.team_id+'-'+data.team_name+'],'; 
                             }             
                             */
                             //-------------
                             var _season_id=me.grid.getSelectionModel().getSelection()[0].data.season_id;
                             var info_str='';
                             for (var i=0;i<me.grid_assigned.getStore().getCount();i++)
                             {
                                 var data=me.grid_assigned.getStore().data.items[i].data;
                                 if(data.team_id!=0 && data.division_name == "<img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/new.png' />")
                                    info_str+=data.team_id+','+_season_id+','+data.division_id+'-'; 
                             }               
                             info_str=info_str.substring(0,info_str.length-1);
                                
                             //*******************************************************
                             Ext.MessageBox.confirm('Update Team Divisions', "Save new division assignments.  Are you sure?"
                             , function(answer)
                              {     
                                  if(answer!="yes"){return;}
                                  
                                  var post={}
                                  if(!info_str || info_str.length==0){return;}
                                  post["new_assignment_combination"]=info_str;
                                  post["season_id"]=_season_id;
                                  
                                  var box = Ext.MessageBox.wait('Please wait while processing ...', 'Status');
                                  Ext.Ajax.request(
                                  {
                                      url: 'index.php/season/json_update_teams_division_assignment/'+App.TOKEN,
                                      params: post,
                                      failure:function(r){box.hide();App.error.xhr(r);},
                                      success: function(response)
                                      {
                                           box.hide();
                                           try{
                                           var res=YAHOO.lang.JSON.parse(response.responseText);
                                          // if(res.result=="1")
                                           //{
                                             //  Ext.MessageBox.alert('Success','Assignment Updated successfully');
                                               me.grid_unassigned.getStore().load();   
                                               me.grid_assigned.getStore().load();   
                                         //  }
                                          // else App.error.xhr(response);
										   }
										   catch(e){
											   //json.parse failed: must be an error
											   App.error.xhr(r);
										   }
                                      }
                                  });    
                                  
                              }); 
                         }
                     }
                    
                 ]                                      
             
         });
         
         oSeason.form_assignment.on("expand",function()
         {
            oSeason.form_assignment.doComponentLayout();               
            oSeason.grid_unassigned.doComponentLayout('100%','100%',true);
            oSeason.grid_assigned.doComponentLayout('100%','100%',true);                                                            
         });           
         me.form_assignment.on("collapse",function()
         {
            oSeason.form_assignment.doComponentLayout();               
            oSeason.grid_unassigned.doComponentLayout('100%','100%',true);
            oSeason.grid_assigned.doComponentLayout('100%','100%',true);
            
            oSeason.form_assignment.hide();//hide this
            //me.form_assignment.destroy();////
         	oSeason.grid.expand();  //show seasons 
            
         });
        
     },
 
     
     /**
     * LEFT SIDE
     */
     init_unassigned:function(_season_id)
     {
        var me=this;
        var _generator=Math.random(); 
 
        var config=

            
        oSeason.grid_unassigned= Ext.create('Ext.spectrumgrids.team',
        {
            generator       : _generator,
            owner           : me,
            flex:1,
            pageSize        : 100,
            //renderTo        : "ctr-dg-teamassignment1",
            title           : 'Unassigned',
            extraParamsMore : {season_id:_season_id},
            collapsible     :false,
         //   height:300,
           // width           :"50%",
            url             :'index.php/season/json_get_unassigned_teams_by_season/'+App.TOKEN,
            viewConfig: {
                plugins: {
                    ptype: 'gridviewdragdrop',
                    dragGroup: 'firstGridDDGroup',
                    dropGroup: 'secondGridDDGroup'
                },
                listeners: 
                {
 
                    beforedrop: function(node, data, dropRec,dropPosition) 
                    {  
                    	//this is where we would 'unassign' the team 
                    	
                    	//returning false to cancel.
                    	return false;
                    	
					}
                }
            },
           
            //customized Components
            rowEditable     :false,
            groupable       :false,
            bottomPaginator :false,
            searchBar       :true 
        });
 
     },
     init_assigned:function(_season_id)
     {
        var me=this;
        var _generator=Math.random(); 
 
        oSeason.grid_assigned= Ext.create('Ext.spectrumgrids.teamdivision',
        {
            generator       : _generator,
            owner           : me,
            
            //flex:1,
           // width:300,
           flex:1,
           // width           :"50%",
            pageSize        : 100,
            //renderTo        : "ctr-dg-teamassignment2",
            title           : 'Assigned',
            extraParamsMore : {season_id:_season_id},
            collapsible     :false,
            //with_counter    :false,
            
            url             :'index.php/season/json_get_assigned_teams_by_season/'+App.TOKEN,
            viewConfig: {
                plugins: {
                    ptype: 'gridviewdragdrop',
                    dragGroup: 'secondGridDDGroup',
                    dropGroup: 'firstGridDDGroup'
                },
                listeners: {
                    drop: function(node, data, dropRec,dropPosition) 
                    {
                        //me.grid_assigned.groupingFeature.startCollapsed=true;
                    }
                    ,beforedrop: function(node, data, dropRec,dropPosition) 
                    {     
                    	//UNDEFINED IS NOT A RESERVED WORD
                        if(dropRec == 'undefined' || typeof dropRec == 'undefined')
                        {
                            //alert('unable to add , not division specified');
                            return false;
                        }
                        //var record=node.dragData.records[0].data;/dropRec.data
                        var record=oSeason.grid_unassigned.getSelectionModel().getSelection()[0].data;
                        /*var r = Ext.ModelManager.create({
                                team_id         : record.team_id,
                                team_name       : record.team_name,
                                division_id     : record.division_id,
                                division_name   : record.division_name
                        }, 'model_'+_generator);
                        me.grid_assigned.getStore().insert(dropRec, r); */
                        
                        
                        oSeason.grid_assigned.getStore().loadData([
                        {
                                team_id         : record.team_id,
                                team_name       : record.team_name,//+"<img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/new.png' />",
                                division_id     : dropRec.data.division_id,
                                division_name     : "<img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/new.png' />",
                                //is_new:'t',
                                //dropRec.data.division_fullname,
                                division_fullname   : dropRec.data.division_fullname
                                
                        }],true);    
                       
                        //delete null record
                        var grid_unassigned_selected_index=oSeason.grid_unassigned.getStore().indexOf(me.grid_unassigned.getSelectionModel().getSelection()[0]);
                        oSeason.grid_unassigned.getStore().removeAt(grid_unassigned_selected_index);
                        
                        //me.grid_assigned.rowEditing.startEdit(0,0);
                        //me.grid_assigned.cellEditing.startEditByPosition({row: 0, column: 0});
                        return false;    
                    }   
                }
            },
           
            //customized Components
            rowEditable     :false,
            groupable       :true,
            bottomPaginator :false,
            searchBar       :true  
        });
      
     }

} 
var oSeason = new Season();    
