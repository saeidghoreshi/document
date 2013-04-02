var formatDate=function(value)
 {
     if(value=='')return'';
	 return value ? Ext.Date.dateFormat(value, 'M d, Y') : null;
 }

 var mclass = 'OrgUserRole';
 

var gclass = 'Spectrum.grids.org_users';
if(!App.dom.definedExt(gclass)){
Ext.define(gclass,
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
 
        this.callParent(arguments);
    },
    org_id:-1,
	org_type:6,//default to team: can be changed anytime
    refresh:function()
    {
 
    	try{
		this.getStore().proxy.extraParams.org_id=this.org_id;//
		//this.store.loadPage(1);
        this.getStore().load();
        //refresh active orgs button in case aroles have been added or removed to ME
		App.AO.get();
		}catch (e){console.log(e);}
    },
    constructor     : function(config)
    {  
 
    	if(!config.generator) config.generator='';
    	if(!config.org_id) config.org_id=-1;
		if(config.org_id)this.org_id=config.org_id; 
		
    	if(config.org_type) this.org_type=config.org_type;
    	if(!config.id){config.id='orgusersgrid';}
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
    	
		
        
		//defaults, all set up so we can override
     	 if(typeof config.title       == 'undefined')  config.title= 'Assigned Users';
     	 if(typeof config.collapsible == 'undefined') config.collapsible=true;
     	 if(typeof config.rowEditable == 'undefined') config.rowEditable=true;
     	 if(typeof config.hideDates   == 'undefined') config.hideDates=false; 
     	 if(typeof config.useCheckbox   == 'undefined') config.useCheckbox=true; 
     	 if(typeof config.bottomPaginator == 'undefined') config.bottomPaginator=true; 
     	 if(typeof config.searchBar   == 'undefined') config.searchBar=false; 
     	 
     	 if(!config.width)config.width="100%";
      
		 if(!config.features) config.features=[];
		 //add grouping feature
    	  config.features.push(
	        Ext.create('Ext.grid.feature.Grouping',
			{
			    enableGroupingMenu: false,
			    enableNoGroups :false,
			    startCollapsed: false , 
			    groupHeaderTpl: '{[values.rows[0].person_lname]}, {[values.rows[0].person_fname]} ({rows.length})'
			})// , p.person_lname ||', '|| p.person_fname  AS full_name

	    );
	    if(config.useCheckbox  )config.selModel =Ext.create('Ext.selection.CheckboxModel', 
        {
           mode        :'MULTI'
        });
        // else leave selModel as default, or input
    	 config.store =Ext.create( 'Ext.data.Store',
    	 {
			remoteSort:false,
			model:mclass,
			groupField:"person_id",//needed for group feature
            proxy: 
            {   
            	type: 'rest',
            	
            	url: "index.php/permissions/json_org_users/"+App.TOKEN,
                reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
                extraParams:{org_id:config.org_id}
            }    
	     });
    	if(!config.listeners)config.listeners={};
 
		config.listeners.edit= function(e)
    	{
    		var assn_id=e.record.data['assignment_id'];
    		var st = escape(formatDate(e.record.data['effective_range_start']));
    		var en = escape(formatDate(e.record.data['effective_range_end']  ));
    		
    		var post='start_date='+st+"&end_date="+en+"&assn_id="+assn_id;

    		var url='index.php/permissions/post_update_assignment/'+App.TOKEN;
    		var callback={scope:this,success:function(o)
     		 {
     	 		 var r=o.responseText;
     	 		 if(isNaN(r) || r<0 )
     	 		 {
     	 	 		 App.error(o);
     	 	 		 e.record.reject();
     	 		 }
     	 		 else
     	 		 {
     	 		 	e.record.commit();
				 }				 
     		 },failure:App.error};
    		YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
    	}
	
			
	    config.columns=
	    [
	    	
	    	{
	    		text     : ''
	    		//,flex:1
	    		,		id:'person_fname'//ID IS NEEDED FOR GROUPING FEATRUE
	    		,hidden: true
	    		,dataIndex: 'person_fname'
			   // editor: { allowBlank: false }//plain string is default
			}		   
			,{
				text     : ''
				//,flex:1
				,	id:'person_lname'//ID IS NEEDED FOR GROUPING FEATRUE
				,hidden: true
				,dataIndex: 'person_lname'
			   // editor: { allowBlank: false }//plain string is default
			}		
			,{
				text     : ' Role'
				,flex:0.75
				,sortable : true
				,dataIndex: 'role_name'
			}	 
			,{
				text     : ' Start'
				,flex:0.125
				,sortable : true
				,dataIndex: 'effective_range_start'
				,hidden:config.hideDates
				,field: {xtype: 'datefield',format: 'Y/m/d'}
	           // renderer: o_teams.formatDate
			}	 
			,{
				text     : ' End'
				,flex:0.125
				,sortable : true
				,dataIndex: 'effective_range_end'
				,hidden:config.hideDates
			     ,field: {xtype: 'datefield',format: 'Y/m/d'}
	           // renderer: o_teams.formatDate
			   // editor: { allowBlank: false }//plain string is default
			}	 

		];			
			//,tbar:[]
		if(!config.dockedItems)config.dockedItems=new Array();
		config.dockedItems.push({dock: 'top',xtype: 'toolbar',//tbar
	        items:
	        [
	        	//top left filters would go here
	        	//need an empty one for search bar?
	        ]});
	        
		if(!config.bbar)config.bbar=[];
 		
        config.dockedItems.push(
		{dock: 'bottom',xtype: 'toolbar',items:
		[		
			{
				tooltip:'Assign a New User',
				iconCls :'fugue_user--plus',
				id:'btn_ur_adduser'+config.generator,
				scope:this,
				handler:function()
/*
	        {
				var form=Ext.create('Spectrum.forms.user_create',{id:'userform_'+Math.random()});
                //var user_cnf=
                
                var user_grid= Ext.create('Ext.spectrumgrids.user',
                {  
                       generator       : Math.random(),
                       owner           : this,

                       url             : "index.php/home/json_get_facebook_friends/"+App.TOKEN,
                       collapsible     : false,
                       height          : "262",
                       extraParamsMore :{fb_id:(App.facebookObject == null )?-1:App.facebookObject.id},
                       title           : '',
                       selModel        : Ext.create('Ext.selection.CheckboxModel', 
                       {
                           mode        :'SINGLE'
                       }),
                       topItems://sam: moved here so it doesnt look like a button , same lvl as next. for bill
                       [
                         '<img src=http://endeavor.servilliansolutionsinc.com/global_assets/social/facebook.png /> '
                      			+'Add from Facebook Friends' 
                       ],
                       form            :form,
 
                       collapsible     :false,
                       //customized Components
                       rowEditable     :false,
                       cellEditable    :false,
                       groupable       :false,
                       groupable       :false,
                       bottomPaginator :false,
                       searchBar       :true       
                });
                var mixer_id='user_mixer_form';
                if(Ext.getCmp(mixer_id)) Ext.getCmp(mixer_id).destroy();
                var _config=
                {
                	id:mixer_id,
                    width   : 800,
                    height  : 375

                }
                          
                var final_form=new Ext.spectrumforms.mixer(_config,[user_grid,form],['grid','form']);
                
                var window_id='create_user_vvv';
				if(Ext.getCmp('create_user_vvv')) Ext.getCmp('create_user_vvv').destroy();//avoid id conflcits that crassh
                var win_cnf=
                {
                   //title       : 'Recipient List',
                   final_form  : final_form,
                   id           :window_id 
                }                           
                var win=Ext.create('Ext.spectrumwindows',win_cnf);
                
                
                win.on('show',function()
                {
 					//this does nothing if we are not logged into facebook so default it to hidden
               		 if(App.facebookObject == null)Ext.getCmp('mixer_left').collapse();//iff no fb, colapse it
                });
                
                win.show();
                
                
                
                //Commented By ryan
				//var win=Ext.create('Spectrum.windows.user',{items:form});
*/
	            {
                    var finalFormGen=Math.random();
				    var form=Ext.create('Spectrum.forms.user_create',{id:'userform_'+Math.random(),finalFormGen:finalFormGen});//Added by Ryan
                    var user_cnf=
                    {  
                           generator       : Math.random(),
                           owner           : this,

                           url             : "index.php/home/json_get_facebook_friends/"+App.TOKEN,
                           collapsible     : false,
                           height          : "262",
                           extraParamsMore :{fb_id:(App.facebookObject == null )?-1:App.facebookObject.id},
                           title           : '',
                           selModel        : Ext.create('Ext.selection.CheckboxModel', 
                           {
                               mode        :'SINGLE'
                           }),
                           topItems://sam: moved here so it doesnt look like a button , same lvl as next. for bill
                           [
                             '<img src=http://endeavor.servilliansolutionsinc.com/global_assets/social/facebook.png /> '
                      			    +'Add from Facebook Friends' 
                           ],
                           form            :form,
     
                           collapsible     :false,
                           //customized Components
                           rowEditable     :false,
                           cellEditable    :false,
                           groupable       :false,
                           groupable       :false,
                           bottomPaginator :false,
                           searchBar       :true       
                    }
                    var user_grid= Ext.create('Ext.spectrumgrids.user',user_cnf);
                    var mixer_id='user_mixer_form';
                    if(Ext.getCmp(mixer_id)) Ext.getCmp(mixer_id).destroy();
                    
                    
                    var finalFormConfig=
                    {
                	    id      : mixer_id,
                        width   : 800,
                        height  : 400,
                        gen     : finalFormGen
                    }
                              
                    var finalForm=new Ext.spectrumforms.mixer(finalFormConfig,[user_grid,form],['grid','form']);
                    
                    var window_id='create_user_vvv';
				    if(Ext.getCmp('create_user_vvv')) Ext.getCmp('create_user_vvv').destroy();//avoid id conflcits that crassh
                    var win_cnf=
                    {
                       final_form  : finalForm,
                       id           :window_id 
                    }                           
                    var win=Ext.create('Ext.spectrumwindows',win_cnf);
                    
                    
                    win.on('show',function()
                    {
 					    //this does nothing if we are not logged into facebook so default it to hidden
               		     if(App.facebookObject == null)Ext.getCmp('mixer_left').collapse();//iff no fb, colapse it
                    });
                    
                    win.show();
                    
                    
                    
                    //Commented By ryan
				    //var win=Ext.create('Spectrum.windows.user',{items:form});


				    win.on('hide',function()
				    {
					    //var user_id=form.get_user_id();
					    var person_id=form.get_person_id();
					    var person_name=form.get_person_name();
					    
					    //copied from below button: they have similar jobs
					    if(!person_id || person_id<0) {  return;}
					    
					    var window_id='org_roles_window';
					    if(Ext.getCmp(window_id))Ext.getCmp(window_id).destroy();//redundant
        			    var f=Ext.create('Spectrum.forms.org_roles',
        			    {	org_id:this.org_id
        				    ,org_type:this.org_type,
        				    person_name:person_name,
        					    person_id:person_id
        					    ,window_id:window_id
        			    });
        			    var w=Ext.create('Spectrum.windows.org_roles',{items:f,id:window_id});//,title:this.org_name
        			    
        			    w.on('hide',function()
        			    { 
						    this.refresh();
        			    },this);    
        			    w.show();
        			    
        			     
				    },this)	;
				    win.show();
	        }}
	        ,{
	        	tooltip:"Assign Myself"
				,iconCls:'fugue_user-white'
				,scope:this
				,handler:function()
				{
					var f=Ext.create('Spectrum.forms.org_roles',
					{
						org_id:this.org_id
						,org_type:this.org_type
						,person_name:'You',
        				person_id:0 
        			});
        			f.url="index.php/permissions/post_create_assignment_foractive/"+App.TOKEN
        			f.user_id=0;
        			var w=Ext.create('Spectrum.windows.org_roles',{items:f});//,title:this.org_name
        			
	                w.show();
	               
	                f.on('hide',function()
	                {
	                    w.hide();
	                },this);    
	                w.on('hide',function()
	                {
	                    this.refresh();
	                },this);    
					
				}
	        }
			,'->'
			,{
				tooltip:'Manage Roles for Selected User',
				iconCls :'fugue_user-worker',
				disabled:false,
				scope:this,
				id:'btn_ur_addrole'+config.generator
				,handler:function()
		        {
		            var usersSelected = this.getSelectionModel().getSelection();
        			if(usersSelected.length==0)  return;  
        			
					var person_name = usersSelected[0].get('person_fname')+" "+usersSelected[0].get('person_lname');
        			var person_id = usersSelected[0].get('person_id');

        			//var org_type_team=6;//lu table
        			var f=Ext.create('Spectrum.forms.org_roles',{org_id:this.org_id,org_type:this.org_type,person_name:person_name,
        					person_id:person_id/*,user_id:user_id*/ });
        			var w=Ext.create('Spectrum.windows.org_roles',{items:f});//,title:this.org_name
        			
	                w.show();
	                
	                f.on('hide',function()
	                {
	                    w.hide();
	                },this);    
	                w.on('hide',function()
	                {
	                    this.refresh();
	                },this);    
				}
			}
			,{
				tooltip:'Unassign Selected Role',
				iconCls :'fugue_user--minus',
				disabled:false,
				scope:this,
				id:'btn_ur_delete'+config.generator
				,handler:function()
		        {
					var rowsSelected = this.getSelectionModel().getSelection();
        			if(rowsSelected.length==0)
        			{ 
        				
        				return;    
					}
					else if(rowsSelected.length==1)
					{
						var row=rowsSelected[0];
						var full_name=row.get('person_fname')+" "+row.get('person_lname');
						
						var role = row.get('role_name');
						
						var msg="Remove the role of '"+role+"' from '"+full_name+"' ?";
						Ext.MessageBox.confirm('Delete?', msg, function(btn_id)
		 				 {
		 					 if(btn_id!='yes')return;
		 					 var assn_id    = rowsSelected[0].get('assignment_id');
			                var url='index.php/permissions/post_delete_assignment/'+App.TOKEN;
			                var post='assn_id='+assn_id;
			                var callback={scope:this,success:function(o)
			                {
								this.refresh();
			                },failure:App.error.xhr};
			                YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
		 					 
						 },this);
						 
					}
					else
					{
						var msg="Remove '"+rowsSelected.length+"' roles from these users?  This cannot be undone.  Also, be careful not to remove "
							+"too many roles from yourself, you may not be able to log in again.";
						Ext.MessageBox.confirm('Delete?', msg, function(btn_id)
		 				 {
		 				 	 if(btn_id!='yes')return;
							//it follows that rowsSelected.length > 1
							var row,assn_ids=new Array();
							for(i in rowsSelected) if(rowsSelected[i])
							{
								row=rowsSelected[i];
								assn_ids.push(row.get('assignment_id'));
							}
							var url='index.php/permissions/post_delete_assignment/'+App.TOKEN;
				            var post='assn_id_array='+YAHOO.lang.JSON.stringify(assn_ids);
				            var callback={scope:this,success:function(o)
				            {
								this.refresh();
				            },failure:App.error.xhr};
				            YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
							
						},this);
						
					}
				}
			}	
				
			,'-'
				
				
			,config.bbar//any buttons passed into constructor go here
			,{
				tooltip:'Modify Selected User',
				iconCls :'fugue_user--pencil',
				id:'btn_user_edit_myorg'+config.generator,
				disabled:false,
				scope:this,
				handler:function()
				{
		            //get clicked record
		            
					//var grid_id=Ext.getCmp('_grid_id_input_').getValue();
		            var rows=this.getSelectionModel().getSelection();
					if(!rows.length)return;
					
					var row=rows[0];

		            var window_id='edit_org_user_form_window_';

		            var form=Ext.create('Spectrum.forms.user_edit',{window_id:window_id});
		            
		            
		            form.loadRecord(row);
		            
		            var win = Ext.create('Spectrum.windows.user', {items: form,id:window_id});
					win.on('hide',function(o)
					{
						this.refresh();
					},this);
					win.show();

				}
			}
				
			

  		]});//end of bbar
	    config.bbar=null;//do thos or else you get doubles
		config.pageSize=100;
		this.callParent(arguments);    	    	
	}
});
 }


     
