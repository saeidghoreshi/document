var c_SYSTEM_MAINTENANCE = function(){ this.construct(); }

c_SYSTEM_MAINTENANCE.prototype = 
{
btn:
    {
        close:null,
        save:null,
        del:null,
        
        menu_dropdown:null,
        
        contr_dropdown:null,
        
        role_dropdown:null,
    },
	//json_get_auth for mini dropdowns
	construct:function()
	{
		this.window_id = App.activeWindow;
		this.token = "TOKEN:"+App.TOKEN;
		this.load();
	},
	
	
	load:function()
	{
	    var required = ['yahoo','base','datatable','button','datasource','element','tabview','container','dom','event','menu','fonts'];
       //  var loadingBar = new utilLoadingBar('mt-loading');
        var loader = new YAHOO.util.YUILoader({
            require: required,
            base: App.loader.base,
            loadOptional: true,
            scope: this,
            filter:App.loader.filter,
            onSuccess:function(){ this.setup_file(); }, 
            scope:this
    
        }).insert();
	},

    
    setup_file:function()
    {
		var file=prompt("Enter the task number from intervals, or a short useful message that will prefix"
		+"  the maintenance file, along with a generated timestamp."
		,'0000');
		//.log(file);
		
		if(file.split(' ').join('')=='')
			this.setup_file();//start over if invalid
		
		var post='file='+file;
		
		var url='index.php/permissions/post_maintenance_filename/'+App.TOKEN;
		
		var callback={scope:this,failure:App.error.xhr,success:function(o){this.init();}};
		
		YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
    },
    
	waiting:null,
    ready:null,
    
    authSelect:null,
	init:function()
	{
	//now this is triggered only by filename save
		this.init_buttons();
        this.init_tabs();
        this.waiting = "<img src= '/assets/images/ajax-loader.gif' />";
        
        this.authSelect = "";
        
        this.ready = "Ready";
        
        
        //.log('init start');
        
        //this.get_roles();
        this.get_auth_types();
        
        this.get_menubar();
        
        this.get_roles();
        YAHOO.util.Dom.removeClass('mt-window','hidden');
        
        
        //post_maintenance
      /*  var url = 'index.php/endeavor/post_maintenance/'+this.token;
        YAHOO.util.Connect.asyncRequest('POST', url ,{success:function(o){} }, ''); */
        
	},
	
	mtTabs:null,
    menu_tab:0,
    method_tab:1,
    init_tabs:function()
    {

        this.mtTabs = new YAHOO.widget.TabView("tab-maintenance");
        this.mtTabs.on('activeIndexChange', this.on_tab_change, this, true);
        this.current_tab = 0;
        this.get_controllers();
    },
    
    firstMethod:true,
    on_tab_change:function(e)
    {
        this.current_tab = e.newValue; 
        if(this.current_tab == this.menu_tab) 
            this.btn.del.addClass('hidden');
        if(this.current_tab == this.method_tab) 
        {
            this.btn.del.removeClass('hidden');
            if(this.firstMethod)
            {
                
                this.firstMethod=false;
            }
        }
    },
    init_buttons:function()
    {/*
        this.btn.close = new YAHOO.widget.Button('btn-close-endeavor-maintenance');
        this.btn.close.on('click', this.close, this, true);
        
        this.btn.save=new YAHOO.widget.Button('btn-process-endeavor-maintenance');        
        this.btn.save.on('click', this.save, this, true);
        
        this.btn.del = new YAHOO.widget.Button('btn-delete-endeavor-maintenance');
        this.btn.del.on('click',this.del_rolemethod,this,true);
        this.btn.del.addClass('hidden');
        */
    },
    
    del_rolemethod:function()
    {
        if(this.selectedMethRole == null || this.selectedMethod == null) return;
        
        if(confirm("Are you sure that you want to delete every combination of role-menu pairs?"))
        {
            //.log('click delete');    
            
              YAHOO.util.Dom.get('meth-status').innerHTML = "Deleting  "+this.waiting;
          
             var i,rec,rows = this.dtMethRoles.getSelectedRows();
             var roles = new Array();        
             for(i in rows)
             {
                 rec = this.dtMethRoles.getRecord(rows[i]);
                 roles[roles.length] = rec.getData('role_id');
             }      
             var methods = new Array();
             rows = this.dtMethods.getSelectedRows();
             for(i in rows)
             {
                 rec = this.dtMethods.getRecord(rows[i]);
                 methods[methods.length] = rec.getData('method_id');
             }         
              
             roles=YAHOO.lang.JSON.stringify(roles);
             methods=JSON.stringify(methods);
            var post = "methods="+methods+"&roles="+roles ;
            //.log(post);
            
            
            
            var url = 'index.php/permissions/post_delete_rolemethod/'+this.token;
            YAHOO.util.Connect.asyncRequest('POST', url ,{success:this.delete_callback,scope:this}, post);  
        
        
        }
    },
    
    delete_callback:function(o)
    {
    	//.log(o.responseText);
       // var res = YAHOO.lang.JSON.parse(o.responseText);
        //res contains a 1 if existing record deleted, -2 if not found
       // var c = res.length
        
      //  YAHOO.util.Dom.get('meth-status').innerHTML = "Deleted at most "+c+" records";
        
    },
    
    get_controllers:function()
    {
        
        //json_get_controllers
        
        YAHOO.util.Connect.asyncRequest('GET','index.php/permissions/json_get_controllers/'+this.token,
                {success:this.display_contr,scope:this},''); 
        
    },
    controller_id:null,
    parse_ctr:[],
    display_contr:function(o)
    {
       var name,id,contr = YAHOO.lang.JSON.parse(o.responseText);  
        
        //.log(contr);
        
        var c_click =function (p_sType, p_aArgs, p_oItem) 
        {
            var sText =  p_oItem.cfg.getProperty("text");
			
			
            this.btn.contr_dropdown.set("label", sText);  
            this.controller_id = p_oItem.value;//save current for roles
	        this.controller_name =this.parse_ctr[this.controller_id];
            this.get_methods(p_oItem.value);
            YAHOO.util.Dom.get('meth-status').innerHTML = "Methods Loaded";
	        this.selectedMethRole=null;
	        this.dtMethRoles.unselectAllRows();
	        
 
	        //.log(this.controller_name );
        };
        var menu = new Array();
        for(i in contr)
        {         
            name = contr[i]['controller_name']; 
            id   = contr[i]['controller_id']; 
            this.parse_ctr[id]=name;//for batch sql file
            name = name.charAt(0).toUpperCase()+name.slice(1);
            while(name.length < 88)//TODO fix so int is somehow related to half the windows width
                name = name+" ";
            name = "<em style='font-style: normal; white-space: pre;' >"+name  +"</em>";

            menu[menu.length] =   { text   : name  ,  value  : id, 
                            onclick : {fn : c_click  , scope:this } };
        }       
         var sp = "";//for tabing in
        this.btn.contr_dropdown = new YAHOO.widget.Button({ type: "menu", label: 
        "<em style='font-style: normal; white-space: pre;'>"+sp+"Select Controller</em>", 
        id:"categorybutton",  name: "menu-ctrl", menu: menu,  container: 'contr-dropdown' });
        //YAHOO.util.Dom.get('span-status').innerHTML = 'Ready';
        
    },
    
    get_methods:function(contr_id)
	{
        //get all methods with matchign controller id
        YAHOO.util.Connect.asyncRequest('POST','index.php/permissions/json_get_methods/'+this.token,
                {success:this.display_methods,scope:this},'contr_id='+contr_id); 
        
        
    },
    
    dtMethods:null,
   // width:"27em",
    height:"31em",
    checkboxWidth:17,
    display_methods:function(o)
    {
        var methods = YAHOO.lang.JSON.parse(o.responseText);
        
        //.log(methods);

        var oColumnDefs = [
        {key:"does_exist", label:"", formatter:YAHOO.widget.DataTable.formatCheckbox,width:this.checkboxWidth},
        {key:"method_name",label:"Method"}
        ];
        var ds = new YAHOO.util.DataSource(methods);
        ds.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
        ds.responseSchema = { fields: ["method_id","method_name","controller_id"]};
        //(this.dtLeagues);

        this.dtMethods = new YAHOO.widget.ScrollingDataTable("dt-methods", oColumnDefs, ds, 
        {  height:this.height  }  );
        
        this.dtMethods.subscribe("rowMouseoverEvent", this.dtMethods.onEventHighlightRow); 
        this.dtMethods.subscribe("rowMouseoutEvent", this.dtMethods.onEventUnhighlightRow); 
       // this.dtMethods.subscribe("rowClickEvent", this.dtMethods.onEventSelectRow); 
      //  this.dtMethods.subscribe("rowSelectEvent", this.select_method,this,true);
        
       
        
        
         this.dtMethods.subscribe("checkboxClickEvent", function(oArgs)
           { 
         	 	if(this.selectedMethRole == null) 
         	 	{
         	 		YAHOO.util.Dom.get('meth-status').innerHTML = "Cannot save.";
         	 		return;
				}
	            var elCheckbox = oArgs.target; 
	            var save = elCheckbox.checked;
				
	            var rec = this.dtMethods.getRecord(oArgs.target);
	            var role_id= this.selectedMethRole.getData('role_id');
	            var role_name= this.selectedMethRole.getData('role_name');
	            var method_id= rec.getData('method_id');
	            var method_name= rec.getData('method_name');
	            var post =    "role_id="  +role_id  +"&method_id="  +method_id
	            			+"&controller_name="+this.controller_name+"&method_name="+method_name;
	            //.log(post);
	            if(save)
                	var url = 'index.php/permissions/post_rolemethod/'+this.token;
                else //delete
                	var url = 'index.php/permissions/post_delete_rolemethod/'+this.token;
                //post_delete_rolemethod
                //.log(post);
                YAHOO.util.Connect.asyncRequest('POST', url ,{success:this.save_callback,scope:this}, post);  
	            
	            YAHOO.util.Dom.get('meth-status').innerHTML = "Saving  "+this.waiting;
	        }, this ,true); 
        
        //  this.dtMethods.subscribe("rowDblclickEvent", this.dblclick_meth,this,true);
    },
    selectedMethod:null,
    select_method:function(row)
    {
        this.selectedMethod = row.record;
        
    },
    /*
	close:function()
	{
		App.windows[this.window_id].destroy();
	},*/
	
    save:function()
    {
        if(this.current_tab == this.menu_tab)
        {
            YAHOO.util.Dom.get('span-status').innerHTML = "Saving  "+this.waiting;
          
            if(this.selectedRole == null || this.selectedMenuItem == null) 
            {
                YAHOO.util.Dom.get('span-status').innerHTML = "Select Rows";
                return;
            }
          //  var role_id = this.selectedRole.getData('role_id');
          //  var menu_id = this.selectedMenuItem.getData('id');
            
            var perm = {  update_auth_id: YAHOO.util.Dom.get('s-update').value,
                          view_auth_id  : YAHOO.util.Dom.get('s-view'  ).value   };
              
             var i,rec,rows = this.dtRoles.getSelectedRows();
             var roles = new Array();        
             for(i in rows)
             {
                 rec = this.dtRoles.getRecord(rows[i]);
                 roles[roles.length] = rec.getData('role_id');
             }      
             var menuItems = new Array();
             rows = this.dtMenuItems.getSelectedRows();
             for(i in rows)
             {
                 rec = this.dtMenuItems.getRecord(rows[i]);
                 menuItems[menuItems.length] = rec.getData('id');//should be menu_id??
             }         
              
             perm = JSON.stringify(perm); 
             roles=JSON.stringify(roles);
             menuItems=JSON.stringify(menuItems);
            var post = 'perm='+perm+"&menuitems="+menuItems+"&roles="+roles ;
            //(post);
            
            var url = 'index.php/permissions/post_rolemenu/'+this.token;
             YAHOO.util.Connect.asyncRequest('POST', url ,
                    {success:this.save_callback,scope:this}, post);  
                    
        }
        else
        {
            YAHOO.util.Dom.get('meth-status').innerHTML = "Saving  "+this.waiting;
          
            if(this.selectedMethRole == null || this.selectedMethod == null) 
            {
                YAHOO.util.Dom.get('meth-status').innerHTML = "Select Rows";
                return;
            }
            //.log('save method assignments');

             var i,rec,rows = this.dtMethRoles.getSelectedRows();
             var roles = new Array();        
             for(i in rows)
             {
                 rec = this.dtMethRoles.getRecord(rows[i]);
                 roles[roles.length] = rec.getData('role_id');
             }      
             var methods = new Array();
             rows = this.dtMethods.getSelectedRows();
             for(i in rows)
             {
                 rec = this.dtMethods.getRecord(rows[i]);
                 methods[methods.length] = rec.getData('method_id');//sho??
             }         
              
             //perm = JSON.stringify(perm); 
             roles=JSON.stringify(roles);
             methods=JSON.stringify(methods);
            var post = "methods="+methods+"&roles="+roles ;
            //.log(post);
            
            
            
            var url = 'index.php/permissions/post_rolemethod/'+this.token;
            YAHOO.util.Connect.asyncRequest('POST', url ,{success:this.save_callback,scope:this}, post);  
        }
         
    },
    save_callback:function(o)
    {
    	//.log(o.responseText);
       // var res = YAHOO.lang.JSON.parse(o.responseText);
        //res contains a 1 if existing record updated, 0 if new record made
        //var c = res.length;
        
        if(this.current_tab == this.menu_tab)
            YAHOO.util.Dom.get('span-status').innerHTML = "Saved.";
        else 
            YAHOO.util.Dom.get('meth-status').innerHTML = "Checkbox Saved ";
        //(o);
        
    },
    
    
    get_auth_types:function()
    {
    	return;
        //.log('get auth');
        YAHOO.util.Dom.get('span-status').innerHTML = 'Auth types '+this.waiting;
        YAHOO.util.Connect.asyncRequest('GET','index.php/permissions/json_getluauth/'+this.token,
                {success:this.display_auth_types,scope:this},''); 
        
    },
    
    /*get_controllers:function()
    {
        YAHOO.util.Dom.get('span-status').innerHTML = 'Loading Controllers '+this.waiting;
          YAHOO.util.Connect.asyncRequest('POST','index.php/endeavor/get_controllers',
                {success:this.make_dropdown,scope:this},'');  
        
    },*/
    display_auth_types:function(o)
    {//.log('todo');
    
        return;
        //.log('disp auth');
        var i,option, types = YAHOO.lang.JSON.parse(o.responseText);
        //.log(types);
       
        var selectUpdate = "<select id='s-update'>";
        var selectView   = "<select id='s-view'>";
       var sel = "";
       for(i in types)
       {
           sel = "";
           //hard coded that 6 == all, the default view
           if(types[i]['auth_id'] == 6) 
                sel = "SELECTED";
           
           option = "<option value='"+types[i]['auth_id']+"' "+sel+"  >"+
                        types[i]['name']+" : "+types[i]['description']+"</option>";
           
            selectUpdate+= option;
            
            
            selectView += option;
       }
       selectUpdate += "</select>";
       selectView += "</select>";
       
       
       YAHOO.util.Dom.get('s-view-contain').innerHTML = selectView;
       YAHOO.util.Dom.get('s-update-contain').innerHTML = selectUpdate;
            /*
    <select id="s-view" class="bigtable" >
    <option  value="1">Inherit</option>
    <option value="2">Organization</option>
    <option value="3" >Role</option>
    <option value="4">Own</option>
    <option value="5">None</option>
    <option value="6" SELECTED>All</option>
    </select>
   */ 
   //.log(selectView);
    },
    
    
    
    get_menubar:function()
    {
        
        YAHOO.util.Dom.get('span-status').innerHTML = 'Loading Menu '+this.waiting;
        YAHOO.util.Connect.asyncRequest('POST','index.php/permissions/json_getmenu/'+this.token,
                {success:this.display_menubar,scope:this},'parent=null');     
                
                 
        
    },
    
    
    parent_menu_id:null,
    display_menubar:function(o)
    {// menu-dropdown
       
        var name,contr = YAHOO.lang.JSON.parse(o.responseText);
       
        var click =function (p_sType, p_aArgs, p_oItem) 
        {
            var sText =  p_oItem.cfg.getProperty("text");

            this.btn.menu_dropdown.set("label", sText);
            this.parent_menu_id=  p_oItem.value;//saved for later role use
            this.get_menu(p_oItem.value);
        };
        var menu = new Array();
        for(i in contr)
        {         
            name = contr[i]['menu_label']; 
            name = name.charAt(0).toUpperCase()+name.slice(1);
            while(name.length < 88)//TODO fix so int is somehow related to half the windows width
                name = name+" ";
            name = "<em style='font-style: normal; white-space: pre;' >"+name  +"</em>";

            menu[menu.length] =   { text   : name ,   value  : contr[i]['id'], 
                            onclick : {fn : click  , scope:this } };
        }       
         var sp = "";//for tabing in
        this.btn.menu_dropdown = new YAHOO.widget.Button({ type: "menu", label: 
        "<em style='font-style: normal; white-space: pre;'>"+sp+"Select Menu</em>", 
        id:"categorybutton",  name: "menu-ctr", menu: menu,  container: 'menu-dropdown' });
        YAHOO.util.Dom.get('span-status').innerHTML = 'Ready';
       
       
       
    },
    
    get_menu:function(parent)
    { 
        YAHOO.util.Dom.get('span-status').innerHTML = 'Loading Menu '+this.waiting;
        YAHOO.util.Connect.asyncRequest('POST','index.php/permissions/json_getmenu/'+this.token,
                {success:this.display_menu,scope:this},'parent='+parent);     
    },
    
    dtMenuItems:null,
    
    display_menu:function(o)
    {
         var items = YAHOO.lang.JSON.parse(o.responseText);
         //.log(items);    
         
        // var roles = YAHOO.lang.JSON.parse(o.responseText);
        var oColumnDefs = [
	        {key:"menu_label",    label:"Menu Item", minWidth:70},
	        {key:'view',  label:'View',width:30 ,editor: new YAHOO.widget.DropdownCellEditor({ 
                dropdownOptions:["NONE","OWN","ROLE","ORG","INHERIT","ALL"],
                                    disableBtns:true})},
	        {key:'update',label:'Update',width:30, editor: new YAHOO.widget.DropdownCellEditor({ 
                dropdownOptions:["NONE","OWN","ROLE","ORG","INHERIT","ALL"],
                                    disableBtns:true})},
        ];
        var ds = new YAHOO.util.DataSource(items);
        ds.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
        ds.responseSchema = { fields: ["menu_label","id","menu_active","menu_order","menu_type","parent"]};
        //(this.dtLeagues);
		
        this.dtMenuItems = new YAHOO.widget.ScrollingDataTable("dt-menuitems", oColumnDefs, ds, 
        { height:this.height} );
        this.dtMenuItems.subscribe("rowMouseoverEvent", this.dtMenuItems.onEventHighlightRow); 
        this.dtMenuItems.subscribe("rowMouseoutEvent", this.dtMenuItems.onEventUnhighlightRow); 
       // this.dtMenuItems.subscribe("rowClickEvent", this.dtMenuItems.onEventSelectRow); 
       // this.dtMenuItems.subscribe("rowSelectEvent", this.select_menuitem,this,true);
        this.dtMenuItems.subscribe("cellClickEvent",    this.dtMenuItems.onEventShowCellEditor); 
       // this.dtMenuItems.subscribe("rowDblclickEvent", this.dblclick_load,this,true); 

        this.dtMenuItems.subscribe("editorSaveEvent", function(oArgs)
           { 
	           if(this.selectedRole==null) return;
	           var oNewData = oArgs.newData;
	           var colKey = oArgs.editor.getColumn().getKey();
           	   //.log('dropdown save event becomes ',oNewData, ' need col ', colKey);           	
           	   //.log(oArgs);  
           	   var rec = oArgs.editor.getRecord();           	   
           	   var menu_id = rec.getData('id');           	   
           	   if(colKey == 'view')
           	   {//this column is view
				   var view = oNewData;				   
				   var update = rec.getData('update');
           	   }
           	   else if(colKey == 'update')
           	   {//this column is update
				   var update = oNewData;				   
				   var view = rec.getData('view');				   
           	   }           	   
           	   var role_id= this.selectedRole.getData('role_id');
           	   var post = "role_id="+role_id+"&menu_id="+menu_id+"&view="+view+"&update="+update;
           	   var url = 'index.php/permissions/post_rolemenu/'+this.token;
           	   //.log(post);
           	   YAHOO.util.Dom.get('span-status').innerHTML = "Saving:"+this.waiting;
           	   YAHOO.util.Connect.asyncRequest('POST', url ,{success:this.save_callback,scope:this}, post); 
           	   
         	 /*	if(this.selectedMethRole == null) 
         	 	{
         	 		YAHOO.util.Dom.get('meth-status').innerHTML = "Cannot save.";
         	 		return;
				}
	            var elCheckbox = oArgs.target; 
	            var save = elCheckbox.checked;
				
	            var rec = this.dtMethods.getRecord(oArgs.target);
	            var role_id= this.selectedMethRole.getData('role_id');
	            var method_id= rec.getData('method_id');
	            var post = "role_id="+role_id+"&method_id="+method_id;
	            if(save)
                	
                else //delete
                	var url = 'index.php/permissions/post_delete_rolemethod/'+this.token;
                //post_delete_rolemethod
                //.log(post);
                YAHOO.util.Connect.asyncRequest('POST', url ,{success:this.save_callback,scope:this}, post);  
	            
	            YAHOO.util.Dom.get('meth-status').innerHTML = "Saving  "+this.waiting;*/
	        }, this ,true); 
        YAHOO.util.Dom.get('span-status').innerHTML = "Ready";    
    },
    
    selectedMenuItem:null,
    select_menuitem:function(row)
    {
        
        this.selectedMenuItem = row.record;
    },
    
    get_roles:function()
    {
        YAHOO.util.Dom.get('span-status').innerHTML = 'Loading Roles '+this.waiting;
        YAHOO.util.Connect.asyncRequest('GET','index.php/permissions/json_getroles/'+this.token,
                {success:this.display_roles,scope:this},'');     
                
                 
        
    },
    dtRoles:null,
    dtMethRoles:null,
    display_roles:function(o)
    {
        var roles = YAHOO.lang.JSON.parse(o.responseText);
        var oColumnDefs = [
        {key:"role_name",label:"Role"}
        ];
        var ds = new YAHOO.util.DataSource(roles);
        ds.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
        ds.responseSchema = { fields: ["role_name","role_id"]};
        //(this.dtLeagues);

        this.dtRoles = new YAHOO.widget.ScrollingDataTable("dt-roles", oColumnDefs, ds,
         {height:this.height,selectionMode:"single"} );
         
        this.dtRoles.subscribe("rowMouseoverEvent", this.dtRoles.onEventHighlightRow); 
        this.dtRoles.subscribe("rowMouseoutEvent", this.dtRoles.onEventUnhighlightRow); 
        this.dtRoles.subscribe("rowClickEvent", this.dtRoles.onEventSelectRow); 
        this.dtRoles.subscribe("rowSelectEvent", this.select_role,this,true);
        
     //   this.dtRoles.subscribe("rowDblclickEvent", this.dblclick_load,this,true); 
        
        YAHOO.util.Dom.get('span-status').innerHTML = "Ready";    
        //dt-roles-m
        //similar table in second tab
        this.dtMethRoles=new YAHOO.widget.ScrollingDataTable("dt-roles-m", oColumnDefs, ds, 
                {width:"220px", height:this.height ,selectionMode:"single"} );//TODO: events 
        
        
        this.dtMethRoles.subscribe("rowMouseoverEvent", this.dtMethRoles.onEventHighlightRow); 
        this.dtMethRoles.subscribe("rowMouseoutEvent", this.dtMethRoles.onEventUnhighlightRow); 
        this.dtMethRoles.subscribe("rowClickEvent", this.dtMethRoles.onEventSelectRow); 
        this.dtMethRoles.subscribe("rowSelectEvent", this.select_meth_role,this,true);
        
       // this.dtMethRoles.subscribe("rowDblclickEvent", this.dblclick_meth,this,true);
    },
    
    selectedRole:null,
    select_role:function(row)
    {
        this.selectedRole = row.record; 
        if(this.parent_menu_id == null) return;//otherwise load permissions
        YAHOO.util.Dom.get('span-status').innerHTML = this.waiting;
       
       
        var recs = this.dtMenuItems.getRecordSet().getRecords();
        var menu_ids = new Array();
        
        for(i in recs)
        {
        	//.log(recs[i].getData());
			menu_ids[menu_ids.length]=recs[i].getData('id');
        }
        var role_id = this.selectedRole.getData('role_id');
        var post = "role_id="+role_id+"&menu_ids="+JSON.stringify(menu_ids) ;
        
        var url = "index.php/permissions/json_get_rolemenu/"+this.token;
        //.log(post); return;
        
        YAHOO.util.Connect.asyncRequest('POST', url ,
                {success:this.load_menu_dd,scope:this}, post);
       
    },
    selectedMethRole:null,
    select_meth_role:function(row)
    {
        this.selectedMethRole = row.record;
        
        if(this.controller_id == null) 
        {
        	YAHOO.util.Dom.get('meth-status').innerHTML = "";
			this.dtMethRoles.unselectAllRows();
			this.selectedMethRole=null;
			return;
        }
        YAHOO.util.Dom.get('meth-status').innerHTML = this.waiting;
        //otherwise, load 'does_exist' checkboxes for entire meth table
        
        var url = "index.php/permissions/json_get_rolemethods/"+this.token;
        var recs = this.dtMethods.getRecordSet().getRecords();
        var method_ids = new Array();
        
        for(i in recs)
        {
        	//.log(recs[i].getData());
			method_ids[method_ids.length]=recs[i].getData('method_id');
        }
        
        var role_id = this.selectedMethRole.getData('role_id');
        var post = "role_id="+role_id+"&method_ids="+JSON.stringify(method_ids) ;
        //.log(post);return;
        var callback = {scope:this, success:this.load_method_checks};
        YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
        
        
    },
    load_method_checks:function(o)
    {
    	//extra brackets avoid 'invalid label' json error
		var methodCheck = YAHOO.lang.JSON.parse(o.responseText);
		
        if(this.dtMethods == null) return;
        
       var id,flag,records = this.dtMethods.getRecordSet().getRecords();
       
       //(records);
       for (i in records) 
       {
            id = records[i].getData('method_id');
            if(methodCheck[id] == "0") flag="";
            else        flag="checked";
            this.dtMethods.getRecordSet().updateKey(records[i], "does_exist", flag); 
       }           
       this.dtMethods.refreshView();
                     		
		
		YAHOO.util.Dom.get('meth-status').innerHTML = "Loaded";
    },
    
    load_menu_dd:function(o)
    {
		var menuPrm = YAHOO.lang.JSON.parse(o.responseText);
		
        if(this.dtMenuItems == null) return;
        
       var id,flag,records = this.dtMenuItems.getRecordSet().getRecords();
       
       //(records);
       for (i in records) 
       {
            id = records[i].getData('id');
            //if(menuPrm[id] == "0") flag="";
            //else        flag="checked";
            this.dtMenuItems.getRecordSet().updateKey(records[i], "view", menuPrm[id].view); 
            this.dtMenuItems.getRecordSet().updateKey(records[i], "update", menuPrm[id].update); 
       }           
       this.dtMenuItems.refreshView();
                     		
		
		YAHOO.util.Dom.get('span-status').innerHTML = "Loaded";
		
		
    },
    
    dblclick_load:function()
    {return;
        if(this.selectedRole == null || this.selectedMenuItem == null) return;
        
        var role_id = this.selectedRole.getData('role_id');
        var menu_id = this.selectedMenuItem.getData('id');
        
        var post = "role_id="+role_id+"&menu_id="+menu_id;
        //.log(post);
        
        YAHOO.util.Connect.asyncRequest('POST','index.php/permissions/json_get_rolemenu/'+this.token,
                {success:this.load_success,scope:this}, post);
        
    },
    
    
    dblclick_meth:function()
    {
        //TODO: mirror of other
        
        if(this.selectedMethRole == null || this.selectedMethod == null) return;
        //.log('handle double click load');
        
    },
    
    load_success:function(o)
    {return;
        var data = YAHOO.lang.JSON.parse(o.responseText);
        //.log(data);
        //.log('stop using this');
        return;
        if(data.length == 0) 
        {
        
            YAHOO.util.Dom.get('s-view'  ).value = 6;//six means ALL
            YAHOO.util.Dom.get('s-update').value = 6;
            return;
        }
        //should be only one result
        YAHOO.util.Dom.get('s-view'  ).value = data[0].view_auth_id;
        YAHOO.util.Dom.get('s-update').value = data[0].update_auth_id;
        
    },
    
    
    
    

    
    
   /* 
    
    display_permissions:function(o)
    {
        var perm = YAHOO.lang.JSON.parse(o.responseText);
        //(perm);
        if(perm.length == 0)
        {
            YAHOO.util.Dom.get('span-status').innerHTML = "None exist";
            YAHOO.util.Dom.get('s-insert').value = 'all';  
            YAHOO.util.Dom.get('s-update').value = 'all';  
            YAHOO.util.Dom.get('s-view'  ).value = 'all';    
            YAHOO.util.Dom.get('s-del'   ).value = 'all';  
            
            
            return;
        }
        perm = perm[0];//since only one row exists at most for each,
        
        YAHOO.util.Dom.get('s-insert').value = perm['role_insert'];  
        YAHOO.util.Dom.get('s-update').value = perm['role_update'];  
        YAHOO.util.Dom.get('s-view'  ).value = perm['role_view'];  
        YAHOO.util.Dom.get('s-del'   ).value = perm['role_delete'];  
        
        
        YAHOO.util.Dom.get('span-status').innerHTML = "Loaded Permissions.";
    },*/
    
}

var Maintenance = new c_SYSTEM_MAINTENANCE();