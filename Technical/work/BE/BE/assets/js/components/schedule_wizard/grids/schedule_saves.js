 
var gsaves='Spectrum.grids.schedule_saves';
if(!App.dom.definedExt(gsaves))//{    
Ext.define(gsaves,
{ 
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    refresh:function()
    {
    	this.getStore().load();
	},
    constructor     : function(config)
    {   
 
		config.height= App.MAX_GRID_HEIGHT;
		
		config.title='Schedules in Progress';

	    config.columns= 
	    [
	        {text   : 'Name',        flex: 1,    sortable : true,dataIndex: 'schedule_name'}
	        ,{text   : 'Season',        flex: 1, sortable : true,dataIndex: 'season_name'}
	        ,{text  : 'Created By',  flex:1 , sortable : true,dataIndex: 'created_name'}
	        ,{text  : 'Created On',  width:130,   sortable : true,dataIndex: 'created_on'}
	        ,{text  : 'Last Updated',width:130     ,sortable : true,dataIndex: 'modified_on'}

	     ]  ;
	     
	     var buttons=[];
	     if(config.bbar)
	     {
	     	 buttons=config.bbar;
			 config.bbar=null;
	     	 if(buttons.length)      //add a seperator if more than zreo given
	     	 	buttons.push('-');
	     }
	     
	     config.bbar=
	     [
		     '->'
		     ,buttons//buttons passed in from constructor
            
             ,{
             	xtype:'button'
             	,text:''
             	,iconCls:'delete'
             	,id:"btn_start_form_delete"
             	,tooltip:'Delete Save'
             	,disabled:true
             	,scope:this
             	,handler:function(o,e)
				{	 
					var rows=this.getSelectionModel().getSelection();
					if(!rows.length){return;}
					
					Ext.MessageBox.confirm('Delete?','You will lose all progress in this schedule wizard file if it is deleted.',function(btn_id)
					{
						if(btn_id != 'yes' && btn_id != 'ok') {return;}
						
						var rows=this.getSelectionModel().getSelection();
						var row=rows[0];
						 //use ext instead of yui connect
						Ext.Ajax.request(
						{
							success:function(o)
							{
								this.refresh();
							}
							,failure:App.error.xhr
							,url:'index.php/schedule/post_file_delete/'+App.TOKEN
							,params:{session_id:row.get('session_id')}
							,scope:this
							
						});
 
					},this);
             	}
            }
		 ];
	    config.listeners=
	    {
			selectionchange:function(sm, selectedRecord) 
    		{
				if (selectedRecord.length) 
				{
			    	//enable row button
		    		Ext.getCmp('btn_start_form_load').setDisabled(false);
		    		Ext.getCmp('btn_start_form_delete').setDisabled(false); 
				}
			}
			
	    };
 		//store only needs to know the URL and the model
    	config.store=Ext.create('Spectrum.store',{url:'index.php/schedule/post_file_get/'+App.TOKEN,model:'SchedSaveSession'});
        this.callParent(arguments);
	}//end constructor

}); //}




