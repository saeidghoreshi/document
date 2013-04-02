var	formatDate=function(value)
 {
	return value ? Ext.Date.dateFormat(value, 'M d, Y') : null;
 }


//var url_search='index.php/person/post_search_users/' +App.TOKEN;
		// grid_id:'',
		

var gclass='Spectrum.grids.user';
if(!App.dom.definedExt(gclass)){
Ext.define(gclass,
{
	extend: 'Ext.spectrumgrids', 
	//extend: 'Ext.grid.Panel', 
	initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    refresh:function()
    {
		this.store.loadPage(1);
    },
	constructor: function(config)
    {  
	    if(!config.url) config.url="index.php/person/json_people_users/"+App.TOKEN;;//default URL if none is passed in
    	
    	
    	
		config.loadMask=false;
    	//first define custom paramters for spectrumgrids
    	config.searchBar=true;
		config.bottomPaginator=true;
		config.rowEditable=true;
		//config.loadMask=false;
		
	    var id='users_grid_main';//+Math.random();//if no id given, use a random one, to avoid conflicts!!!!
	    
	    if(!config.id){config.id=id;};//otherwise use the input
	    if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
	    
	    
	    ///////////////////////////////		//buttons
	    if(!config.title)config.title= 'Current Users';
	    //,renderTo: input_renderTo,id:input_id,collapsible: true
	      //  ,store: user_store,stateful: true,frame: true,remoteSort:true,
	    config.width="100%";
	    // 
	    config.collapsible=true;
	   
        var model_name="User";
    	config.store= Ext.create( 'Ext.data.Store',
    	{
    		model:model_name,autoDestroy:false,autoSync :false,
            autoLoad :true,
            remoteSort:true,pageSize:100 ,
            proxy: 
            {   type: 'rest',url: config.url,
                reader: {type: 'json',root: 'root',totalProperty: 'totalCount'},
                extraParams:{role_id:-1,org_id:-1}
            }    

	    });
	    config.store.on('load',function(e)
	    {
			
			var bool=true;//disabled true
	     	 var row_buttons=['btn_user_edit','btn_user_delete'];//btn_user_roles_view
     		 var btn_id;
     		 for(i in row_buttons)
     		 {//toggle the disabled property of each
				 btn_id=row_buttons[i];
				 if(Ext.getCmp(btn_id))
				 	Ext.getCmp(btn_id).setDisabled(bool);			 
     		 }	
	    });

		var btn_create={scope:this,tooltip:'Create New User',iconCls :'add',id:'btn_user_add', handler:function()
		{
			var window_id='create_user_form_window_';
		    //if(Ext.getCmp('user_form_window_'))
		       // {Ext.getCmp('user_form_window_').destroy();}
		    
            //var u_form = Ext.create('Spectrum.forms.user.create',{});depreciated
            
            
            var u_form = Ext.create('Spectrum.forms.user_create',{window_id:window_id});
            //u_form.doLayout();//this might not be needed
            
            var win = Ext.create('Spectrum.windows.user', {items:u_form,window_id:window_id} );
			win.on('hide',function(o)
			{
				// similar to store.load();, except it also sets paginator to page 1
				this.refresh(1);
			},this);
			
			win.show();
            
		}}	
		var sep="-";//seperator
		var helper='Double Click Row to Edit';
		var go_right='->';
						
		var btn_edit={tooltip:'Edit User',iconCls :'pencil',id:'btn_user_edit',disabled:true,scope:this,handler:function()
		{
            //get clicked record
            
			//var grid_id=Ext.getCmp('_grid_id_input_').getValue();
            var rows=this.getSelectionModel().getSelection();
			if(!rows.length)return;
			
			var row=rows[0];

            var window_id='edit_user_form_window_';
            //var form=Ext.create('Spectrum.forms.user.edit',{});DEPREDCIATED
            
            var form=Ext.create('Spectrum.forms.user_edit',{window_id:window_id});
            form.loadRecord(row);
            
            var win = Ext.create('Spectrum.windows.user', {items: form,window_id:window_id});
			win.on('hide',function(o)
			{
				//.log('hide event todo: refresh load');
					this.store.loadPage(1);
				//o_users.users.get();
			},this);
			win.show();

			
		}}
		var btn_del={scope:this,tooltip:'Delete User',iconCls :'delete',disabled:true,id:'btn_user_delete',handler:function()
	    {
            var rows=this.getSelectionModel().getSelection();
			if(!rows.length)return;
            var rec=rows[0];
         	var name=rec.get('person_fname')+" "+rec.get('person_lname');
         	var msg="Are you sure you want to delete user \""+name +"\" permanently?  ";
         	Ext.MessageBox.show({title:'Delete?',msg:msg,
	            buttons: Ext.Msg.YESNO,
	            scope:this,//scope important
	            fn: function(btn_id)
		 		{
		 			if(btn_id!='yes' )return;

					 var callback  ={scope:this,success:function(o)
					 {
						 if(o.responseText=='1')
						 {//it worked
						    Ext.MessageBox.alert('Success','User Deleted');
						    
							this.store.loadPage(1);//we need scope
							// o_users.get_users();
						 }
						 else
						 {
							App.error.xhr(o);
						 }
					 },
					 failure:App.error.xhr
					 };
					 
					  var post = "user_id="+rec.get('user_id');
					  var url = 'index.php/permissions/post_delete_user/'+App.TOKEN; 

					  YAHOO.util.Connect.asyncRequest('POST',url , callback,post);
				 }});//end of messagebox
			 
	    }}
  
		var bbar=[];
		bbar.push(btn_create);
		bbar.push(sep);
		bbar.push(helper);
		bbar.push(go_right);
		var input_bbar=[];

		if(config.bbar){input_bbar=config.bbar;}
		config.bbar=null;
		for(i in input_bbar) 
		{
			if(input_bbar[i].iconCls)//for IE: only add if button is not empty object
				bbar.push(input_bbar[i]);
		}        


		bbar.push(btn_edit);
		bbar.push(sep);
		bbar.push(btn_del);
	   if(!config.dockedItems)config.dockedItems=new Array();
	    config.dockedItems.push( 
	    {dock: 'top',xtype: 'toolbar',//tbar
	        items: 
	        [  
	            //removed my search to use built in spectrumgrids search intsead
	            //{xtype:'displayfield',value:"Active Org users in: "},
	           // {xtype:'button',menu:[],text:'All Users',id:'user_org_filter'}
	           // ,{xtype:'button',menu:[],text:'All Orgs',id:'user_org_specific_filter',disabled:true}
			    
		    ]
		});
		config.dockedItems.push( {dock: 'bottom',xtype: 'toolbar',items: bbar});

	    
	    
	    /////////////////////////////////////////all the rest

	  
	    
			//height: '90%',
		config.listeners=
		{
			selectionchange:function(sm, selectedRecord)
			{
				//.log('SELECTION CHANGE GLOBAL');	

				if (selectedRecord.length) 
				{
		    		//var rec = selectedRecord[0];
					var bool=false;//disabled true
	     			 var row_buttons=['btn_user_edit','btn_user_delete'];//btn_user_roles_view
     				 var btn_id;
     				 for(i in row_buttons)
     				 {//toggle the disabled property of each
						 btn_id=row_buttons[i];
						 if(Ext.getCmp(btn_id))
				 			Ext.getCmp(btn_id).setDisabled(bool);			 
     				 }		
				}
			}
			,edit:function(e)
			{
				// rs.editing_user=e.record;//backup for commit on success
	 			var person=[];
    			for(i in e.record.fields.items)
    			{
					var col  = e.record.fields.items[i].name;
					var data = e.record.data[col];
					//if(col=='person_birthdate')
						//data = formatDate(data);
					//else
					 if(isNaN(data))
						data=escape(data);
					person.push(col+"="+data);
    			}

    			var post = person.join("&"); 

    			var url="index.php/person/post_update_person/"+App.TOKEN;
    			var callback={failure:App.error.xhr,success:function(o)
    			{
    				var r=o.responseText;
    				//if message is a negative number, or not a number at all, then a problem has happened
    				if(isNaN(r) || r<=0)
    				{
    					App.error.xhr(o); 
    					return; 
    				}
    				
					e.record.commit();
					//o_users.editing_user=null;
    			},failure:App.error.xhr,scope:this};
    			YAHOO.util.Connect.asyncRequest('POST',url,callback,post);	
			}
			
		};

	       // stateId: 'userGrid',//??
	    //viewConfig: {stripeRows: true},
	        
	    config.columns=
	    [
        	{  header:'Names',columns:
				[//with row editor regex
					{text     : 'First',width:150,sortable : true,dataIndex: 'person_fname',
		                editor: { allowBlank: false ,maskRe: /^[a-zA-Z]/}
		            },
		            {text     : 'Last',width:150,sortable : true,dataIndex: 'person_lname',
		                editor: { allowBlank: false ,maskRe: /^[a-zA-Z]/}
		        	}
		        ]				
        	}       
        	,{text     : 'Email',flex:1,sortable : true,dataIndex: 'email',//renderer: this.formatEmptySpaces,
            	editor: { allowBlank: true,  vtype: 'email'  }
		    }  	      
        	,{header:"Username",dataIndex:'login',width:150}     
        	 
            ,{header     : 'Birthdate',width    : 95,hidden:true,//hidden by request
            	sortable : true,dataIndex: 'person_birthdate'
                //field: { xtype: 'datefield',     format: 'Y/m/d' },
                //renderer: this.formatDate
            }
            
            ,{text     : 'Last Activity',width    : 150,sortable : true,dataIndex: 'last_login_date'}
            

        ];//end of columns
        
        

    	this.callParent(arguments); 
    	//.log('users/grid.js created, after callparent')
    	//this.buildFilters();
	}   
	/*
	
	
	//filters moved to the specific instance of the class
	//not in base class here
	
	,getOrgs:function(role_id)
	 {
	     var post = "role_id="+role_id;
	     var callback ={scope:this,success:function(o)
	     {
			 var orgs = YAHOO.lang.JSON.parse(o.responseText);
			var menu=new Array();		
			orgs.push({org_name:'All',org_id:-1});
			for(i in orgs)
			{
				menu.push({text:orgs[i]['org_name'],value:orgs[i]['org_id'],scope:this,handler:function(o,e)//iconCls:'tick', 
				{
					//data for selected item								
					var name = o.text;
					var id   = o.value;

					Ext.getCmp('user_org_specific_filter').setText(name);
					
        			this.store.proxy.extraParams.org_id=id;
        			this.store.loadPage(1);
				}});			
        		 
			}	
			var org_btn=Ext.getCmp('user_org_specific_filter');//btn_org_select_form	
			org_btn.setDisabled(false);	
			
			org_btn.menu=new Ext.menu.Menu({items:menu,width:200,height:200,maxHeight:400,minHeight:50,resizable :true});//,maxHeight:200
			//.log(org_btn.menu);
	     }};
	     YAHOO.util.Connect.asyncRequest('POST', 'index.php/permissions/json_orgs_by_role/' +App.TOKEN    ,callback,   post);
	 }
	,buildFilters:function()
	{
		var url='index.php/permissions/json_allowed_roles_by_org/'+App.TOKEN;
		
		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
		{
			var name,id,roles=YAHOO.lang.JSON.parse(o.responseText);
			
			var _filter=new Array();	
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;

        		Ext.getCmp('user_org_filter').setText(name);

        		this.store.proxy.extraParams.role_id=id;
        		this.store.proxy.extraParams.org_id=-1;//reset this
        		Ext.getCmp('user_org_specific_filter').setText('All');
        		Ext.getCmp('user_org_specific_filter').setDisabled(true);	
        		
        		if(id == -1)//if all users then refresh right away
        			this.store.loadPage(1);
        		this.getOrgs(id);// setup other filter
			};
			//one item for no season
			//var foundActive=false;		
			roles.push({role_name:'All Users',role_id:-1});		
			for(i in roles)
			{
				name=roles[i]['role_name'];//+" : "+seasons[i]['display_start']+" - "+seasons[i]['display_end'];
				id  =roles[i]['role_id'];

        		_filter.push({text:name,value:id,scope:this,handler:itemClick});
			}
			Ext.getCmp('user_org_filter').menu=Ext.create('Ext.menu.Menu',{items:_filter,width:200,height:100,maxHeight:400,minHeight:50,resizable :true});


		}},'inherit=t');//inherit so also show those below this
		
	}
	
*/

});

}
