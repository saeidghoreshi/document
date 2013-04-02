var methodModel_='SysMethod';
 

var cl = 'Spectrum.grids.methods';
if(!App.dom.definedExt(cl)){
Ext.define(cl,
{
    extend: 'Ext.spectrumgrids', //base class
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
 	controller_id:-1,
 	controller_name:'',
 	role_id:-1,
 	refresh:function()
 	{
 		this.loading=true;
		this.getStore().proxy.extraParams.contr_id=this.controller_id;
		this.getStore().proxy.extraParams.role_id =this.role_id;
		this.getStore().load();///
 	},
    constructor     : function(config)
    {   
    	//first define custom paramters for spectrumgrids
		config.searchBar=false;
		config.bottomPaginator=false;
		
    	if(!config.dockedItems) config.dockedItems=[];//nothing there, but script breaks without this line
    	
		//now manage actual parameters
		
		if(!config.id)
		{//randomize id if no id given
			config.id='all_methods_grid__';
		}
		 
		if(Ext.getCmp(config.id))//if id is given, check if it has been already used
		{
			Ext.getCmp(config.id).destroy();
		}
		config.collapsible=false;
  
		config.store =Ext.create( 'Ext.data.Store',
    	{
    		model       :methodModel_
    		,autoDestroy:false,
    		 autoSync :false,
            autoLoad    :false,
 
            proxy       : 
            {   
                type        : 'rest',
                url         : (config.url==null)?'index.php/permissions/json_get_methods/'+App.TOKEN:config.url,
                reader      : //json_get_rolemethods
                {
                        type            : 'json'//, 
                },
                extraParams :{contr_id:-1,role_id:-1} 
            }    
            ,listeners:
            {
				load:{scope:this,fn:function(store,g,opt)
				{
					this.loading=true;
 
					var i,row, sel=new Array();
					for(i=0;i<store.getCount();i++)
					{
						row=store.getAt(i); 
						if(row.get('is_allowed')=='t'||row.get('is_allowed')=='true')
							sel.push(row);
						//else false so do nothing
 
					}
					//select this array of records
					//http://docs.sencha.com/ext-js/4-0/#!/api/Ext.selection.CheckboxModel-method-select
					 
					this.getSelectionModel().select(sel,true,true);
 					//loading is copmlete
					this.loading=false;//!!!!! important
					
				}}
            }
	    });
		config.tbar=
		[
			//'In Controller:',
			
			{
				xtype:'button'
				,menu:[]
				,title:'ctr'
				,id:'meth_ctr_selector'
				,text:'Select Controller'
			}
				
		];
		config.bbar=
		[

			{
				xtype:'button'
				,text:''
				,scope:this
				,iconCls:'fugue_information-balloon'
				,tooltip:'Register "view" files for help toolbar'
				,handler:function()
				{
					if(!this.controller_id||this.controller_id<0){return;}
		            
		            var f=Ext.create('Spectrum.forms.help_manager',{controller_id:this.controller_id} );
		            var w=Ext.create('Spectrum.windows.help_manager',{items:f});
		            w.show();
				}
			}
			,{
				xtype:'button'
				,text:''
				,scope:this
				,iconCls:'bug'
				,tooltip:'Register icons with system.sys_icons'
				,handler:function()
				{
					Ext.MessageBox.prompt('Enter icon name, or csv','Be exact, its case sensitive.  Duplicates are handled.'
					,function(btn,text)
					{
						var post='sys_icons='+escape(text);
						var url='index.php/endeavor/post_system_icons/'+App.TOKEN;
						var h=function(o){console.log(o.responseText);};
						YAHOO.util.Connect.asyncRequest('POST',url,{success:h,failure:h},post);
					});
				}
			}
		]; 
        if(config.title==null)config.title='Methods';
        
        config.selModel=Ext.create('Ext.selection.CheckboxModel',
        {
        	mode:"MULTI",
        	enableKeyNav :false,
        	checkOnly :true
 
        });

        config.columns=
        [
            {text   : 'Method',width:150,sortable : true,dataIndex: 'method_name'}
        ];

        this.callParent(arguments);
        this.get_controllers();
	}
	,get_controllers:function()
    {
		YAHOO.util.Connect.asyncRequest('GET','index.php/permissions/json_get_controllers/'+App.TOKEN,
        {scope:this,success:function(o)
        {
			var name,id,contr = YAHOO.lang.JSON.parse(o.responseText);  
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;
 
				this.controller_name=name;
				this.controller_id=id;
			     this.refresh(); 
        		Ext.getCmp('meth_ctr_selector').setText(name);
			};
 
 

	        var ctr_menu = new Array();
	        for(i in contr)if(contr[i])
	        {         
	            name = contr[i]['controller_name']; 
	            id   = contr[i]['controller_id']; 
        		ctr_menu.push({text:name,value:id,handler:itemClick,scope:this});
 
	        }      
	        //
			Ext.getCmp('meth_ctr_selector').menu=Ext.create('Ext.menu.Menu',{items:ctr_menu}); 
			
        }},'')
    }
})}