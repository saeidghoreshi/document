
var model_id = 'Assoc';

var assocGrid = 'Spectrum.grids.assoc';
if(!App.dom.definedExt(assocGrid)){
Ext.define(assocGrid,
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },

 	
    refresh:function()
    {
 
    	this.store.load();
 
    },
    constructor     : function(config)
    {  
    	 
    	config.searchBar=true;
    	config.bottomPaginator=true;
    	config.rowEditable=false;
		config.collapsible= true;
    	
    	var id='_grid_assoc_manage_';
		if(!config.id){config.id=id;}
		if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		
		this.id=config.id;
 
		config.store= Ext.create( 'Ext.data.Store',
		{
			model:model_id,autoDestroy:false,autoSync :false,autoLoad :true,remoteSort:true,pageSize:100 ,
            proxy: 
            {   
            	type: 'rest',url: 'index.php/associations/json_getassociations/'+App.TOKEN,
                reader: {type: 'json',root: 'root',totalProperty: 'totalCount'}
              //  extraParams:{}
            }  
		});
		config.title ='Associations';//
		
		 
		config.width="100%";//
		
		config.listeners=
		{
 
    		edit: function(e)
    		{
    			//.log(e.record.data);
    			
    			var name=e.record.data['association_name'];
    			var id = e.record.data['association_id'];

     			 if(!name || name==this.team_name || name.split(' ').join('')=='') 
     			 {
     	 			 e.record.reject();//opposite of commit
     				 return;
				 }
				 
     			 var post="org_id="+id+"&org_name="+escape(name);
     			 var url="index.php/endeavor/post_org_name/"+App.TOKEN;
     			 var callback={scope:this,success:function(o)
     			 {
 
     	 		 	e.record.commit();
					 				 
     			 },failure:App.error.xhr};
	 			 YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
    		}		
		};

	    config.columns=
	    [
        	{ dataIndex: 'association_name',flex:1,text: 'Name',  sortable : true}		    
        	,{dataIndex: 'country_abbr', width:100,text      : 'Country',  sortable : true,  }		    
        	,{dataIndex: 'website'  ,    width:100,text      : 'Website',     sortable : true}		    
            ,{dataIndex : 'user_count',  width:80, text: 'Users'}
            ,{dataIndex : 'league_count',width:80, text: 'Leagues'}
            ,{dataIndex : 'tourn_count', width:80, text: 'Tournaments'}
            ,{dataIndex : 'team_count'  ,width:80, text: 'Teams'}
            ,{dataIndex : 'player_count',width:80, text: 'Players'}
		];
		if(typeof config.dockedItems=='undefined')
		{
			config.dockedItems=new Array();
		}
		config.dockedItems.push(
		{dock: 'top',xtype: 'toolbar',//tbar
	        items:
	        [
	        	//filters go here if any 
	        ]
		});
		var bbar=[];
     	if(config.bbar){bbar=config.bbar};
     	config.bbar=null;
		var new_bbar=
		[
			{tooltip:'Create New Association',
			iconCls :'fugue_briefcase--plus',
			scope:this,
			handler:function()
	        {	
 				var window_id = 'assoc_newwindow_';
				var f=Ext.create('Spectrum.forms.assoc',{window_id:window_id});
				var w=Ext.create('Spectrum.windows.assoc',{id:window_id,items:f});
				
				w.on('hide',function()
				{
					this.refresh();
				},this);
				w.show();
	        }}
			//,'-'
			//,"Double click a row to edit"
			,'->'
 			,bbar//btns from controller
 			,'-'
 			,{
 				tooltip:'Modify Selected Association',
 				iconCls:'fugue_briefcase--pencil',
 				scope:this,
 				handler:function()
 			{
				var rowsSelected = this.getSelectionModel().getSelection();
	            if(rowsSelected.length==0){return;}
	            
	            var rec=rowsSelected[0];
			   var window_id = 'assoc_editnewwindow_';
				var f=Ext.create('Spectrum.forms.assoc',{window_id:window_id});
				f.loadRecord(rec);
				var w=Ext.create('Spectrum.windows.assoc',{id:window_id,items:f,title:'Edit Association'});
				
				w.on('hide',function()
				{
					this.refresh();
				},this);
				w.show();
 
 			}}
			//,'-'
			,{tooltip:'Remove Selected Association',
			iconCls :'fugue_minus-button',
			disabled:false,
			scope:this,
			handler:function()
	        {		
 
	            var rowsSelected = this.getSelectionModel().getSelection();
	            if(rowsSelected.length==0){return;}
   
 
				var id  =rowsSelected[0].get('association_id');
				var name=rowsSelected[0].get('association_name');
				  
        		 var msg="Are you sure you want to delete \""+name+"\" ?";
		 		 Ext.MessageBox.confirm('Delete?', msg, function(btn_id)
		 		 {
		 			 if(btn_id!='yes'&&btn_id!='ok')return;
     				 var post="association_id="+id;
     				 var url="index.php/associations/post_delete_assoc/"+App.TOKEN;
     				 var callback={failure:App.error.xhr,scope:this,success:function(o)
     				 {
     	 				 var r=o.responseText;
						 if(isNaN(r)||r<0)
						 {
							 App.error.xhr(o);									 
						 }
						 else
						 {
			 				this.refresh();

							 //this.setDisabled_teamsGrid(true);
						 }								 
     				 }};
	 
	 				 YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
	 			 },this);
 		 
			  
			}}
		]
		config.dockedItems.push({dock: 'bottom',xtype: 'toolbar',items: new_bbar});
		 
		
     	this.callParent(arguments); 
 
	}
 
	
	
});

}


