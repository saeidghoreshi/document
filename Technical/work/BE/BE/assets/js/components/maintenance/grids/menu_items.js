var thismodel='RoleMenu';

var cl = 'Spectrum.grids.menu';
if(!App.dom.definedExt(cl)){
Ext.define(cl,
{
    extend: 'Ext.spectrumgrids', //base class
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
 
 	role_id:-1,
 	refresh:function()
 	{
 		this.loading=true; 
 		 
		if(!this.role_id||this.role_id<0){return;}
		this.getStore().proxy.extraParams.role_id =this.role_id;
		this.getStore().load();///
 	},
    constructor     : function(config)
    {  
 
		
    	if(!config.dockedItems) config.dockedItems=[];//nothing there, but script breaks without this line
    	
		//now manage actual parameters
		
		if(!config.id)
		{//randomize id if no id given
			config.id='rolemenu_grid__';
		}
		 
		if(Ext.getCmp(config.id))//if id is given, check if it has been already used
		{
			Ext.getCmp(config.id).destroy();
		}
		config.collapsible=false;
  
		config.store =Ext.create( 'Ext.data.Store',
    	{
    		model       :thismodel
    		,autoDestroy:false,
    		autoSync :false,
            autoLoad    :false,
 
            proxy       : 
            {   
                type        : 'rest',
                url         : (config.url==null)?'index.php/permissions/json_get_rolemenu/'+App.TOKEN:config.url,
                reader      :  
                {
                        type            : 'json'//, 
                },
                extraParams :{parent:0,role_id:-1} 
            }    
 
	    });
		config.tbar=
		[
			//'In Controller:',
			{xtype:'button',menu:[],title:'ctr',id:'menu_item_selector',text:'Select Parent Menu',flex:1}
			,{xtype:'displayfield',value:"VIEW",width:90}
			,{xtype:'displayfield',value:"UPDATE",width:90}
		];
		 
        if(config.title==null)config.title='Menu Items';
 
        config.columns=
        [
            {text   : 'Name',flex:1,sortable : true,dataIndex: 'menu_label'},
            {
            	text   : 'View',
            	width:90  ,sortable : true,
            	dataIndex: 'view_code'
	             ,editor: 
	             {
			        xtype: 'combobox',
			        typeAhead: true,
			        triggerAction: 'all',
			        selectOnTab: true,
			        store: 
			        [
			            ['INHERIT', 'INHERIT'],//1
			            ['ORG', 'ORG'],//2
			            ['ROLE', 'ROLE'],//3
			            ['OWN', 'OWN'],//4
			            ['NONE', 'NONE'],//5
			            ['ALL', 'ALL']//6
			        ],
			        lazyRender: true,
			        listClass: 'x-combo-list-small'
			    }
            },
            {
            	text   : 'Update',
            	width:90,
            	sortable : true,
            	dataIndex: 'update_code' 
            	,editor: 
	             {
			        xtype: 'combobox',
			        typeAhead: true,
			        triggerAction: 'all',
			        selectOnTab: true,
			        store: 
			        [
			            ['INHERIT', 'INHERIT'],//1
			            ['ORG', 'ORG'],//2
			            ['ROLE', 'ROLE'],//3
			            ['OWN', 'OWN'],//4
			            ['NONE', 'NONE'],//5
			            ['ALL', 'ALL']//6
			        ],
			        lazyRender: true,
			        listClass: 'x-combo-list-small'
			    }
			}
        ];
		config.rowEditable=true;
		if(!config.listeners)config.listeners={};
		
		config.listeners.edit=
		{scope:this,fn:function(e)
		{

			//post the row to save changes
			 Ext.Ajax.request(
		     {
		         url        : 'index.php/permissions/post_rolemenu/'+App.TOKEN,
		         params     : e.record.data,
		         method     :"POST",
		         //scope      :this,
		         failure    :App.error.xhr,
		         success    : function(o)
		         {
		              e.record.commit();
		               
		         }
		     });     
 
		}};
        this.callParent(arguments);
        this.get_menubar();
	}
	,get_menubar:function()
    {
		YAHOO.util.Connect.asyncRequest('GET','index.php/permissions/json_getmenu/'+App.TOKEN,
        {scope:this,success:function(o)
        {
			var name,id,items = YAHOO.lang.JSON.parse(o.responseText);  
			 
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;
 
 				 
 				 
				this.getStore().proxy.extraParams.parent=id;
				this.refresh();
        		Ext.getCmp('menu_item_selector').setText(name);
			};
 
 

	        var menu = new Array();
	        for(i in items)if(items[i])
	        {         
	            name = items[i]['menu_label']; 
	            id   = items[i]['id']; 
        		menu.push({text:name,value:id,handler:itemClick,scope:this});
 
	        }      
			Ext.getCmp('menu_item_selector').menu=Ext.create('Spectrum.btn_menu',{items:menu}); 
			
        }
        ,failure:App.error.xhr}
        ,'');
    }
})}
