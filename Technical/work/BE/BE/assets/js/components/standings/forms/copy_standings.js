

var formStandings = 'Spectrum.forms.copy_standings';
if(!App.dom.definedExt(formStandings)){
Ext.define(formStandings,
{
    //extend: 'Ext.form.Panel', 
    extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    season_id:-1,
    copy_rank_id:-1,
    window_id:null,
    init_season_menu:function()
	{
		var season_url='index.php/season/json_active_league_seasons/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('GET',season_url,{scope:this,success:function(o)
		{
			var name, icon,id,seasons=YAHOO.lang.JSON.parse(o.responseText);
			seasons=seasons['root'];//skip paginator stuff
			//this.seasons_menu=new Array();	
			var seasons_filter=new Array();	
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;

				Ext.getCmp('hidden_copystdseason').setValue(id);
        		Ext.getCmp('stnd_copyseason').setText(name);
        		this.refresh_standings_menu(); 
			};
			//one item for no season
			//seasons_filter.push({text:'Unassigned',value:-1,handler:itemClick,scope:this});
 	
			for(i in seasons) if(seasons[i]) //IE9 for in bug
			{
				name=seasons[i]['season_name']+" : "+seasons[i]['display_start']+" - "+seasons[i]['display_end'];
				id  =seasons[i]['season_id'];
				if(id==this.season_id) {continue;}
				icon='';
				if(seasons[i]['isactive']=='t')
				{
					icon='tick'; 
				}
				else icon='cross';
        		seasons_filter.push({text:name,value:id,handler:itemClick,scope:this,iconCls:icon});
			}
			Ext.getCmp('stnd_copyseason').menu=Ext.create('Spectrum.btn_menu',{items:seasons_filter});
 
		//this.display_teams(null);//render gthe grid with empty data
		}});
	},
	refresh_standings_menu:function()
	{
		var post='season_id='+Ext.getCmp('hidden_copystdseason').getValue();
		
		var url='index.php/statistics/json_rank_types_treepanel/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
		{
			var name,id,st = YAHOO.lang.JSON.parse(o.responseText);
			 
			var filter=new Array();	
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;
				
				this.copy_rank_id=id;
        		Ext.getCmp('stnd_copyselect').setText(name);
 				
			};
 			if(st['children'])st=st['children'];//skip treepanel format
			for(i in st) if(st[i]) //IE9 for in bug
			{
				name=st[i]['rank_name'];
				id  =st[i]['rank_type_id']; 
 
        		filter.push({text:name,value:id,handler:itemClick,scope:this});
			}
						
			
			Ext.getCmp('stnd_copyselect').menu=Ext.create('Spectrum.btn_menu',{items:filter});
			Ext.getCmp('stnd_copyselect').setDisabled(false);
			
		}},post);
		
	},
    constructor     : function(config)
    {  
    	if(!config.id){config.id = 'c_standings_form'};
    	
    	this.window_id=config.window_id; 
    	//avoid this season id 
    	if(config.season_id){this.season_id=config.season_id;} 
		
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		 config.title= '';
		 config.autoHeight= true;//,resizable:false,
		 config.bodyPadding= 10;
		// config.width= 600;
		 //config.height=250;
		 //config.hidden=false;
	     config.fieldDefaults= {labelWidth: 125,msgTarget: 'side',autoFitErrors: false};
	     config.defaults= {anchor: '100%'};
	     config.items=
	     [
		     {name:'season_id',xtype:'hidden',value:0,id:'hidden_copystdseason'},
		   {
		   	   xtype : 'fieldcontainer',   
				fieldLabel:"From Season",
				width:'100%', 
				layout:'hbox',  
				items:[ {
						xtype:'button',
						fieldLabel:'',
						text:"Select a Season",
						width:180,
						id:'stnd_copyseason',
						menu:[]
					}]
			}
			,{
		   	   xtype : 'fieldcontainer',   
				fieldLabel:"Standings",
				width:'100%', 
				layout:'hbox',  
				items:[ {
						xtype:'button',
						disabled:true,
						fieldLabel:'',
						text:"Select one",
						width:180,
						id:'stnd_copyselect',
						menu:[]
					}]
			}
	    ];
	    config.bbar=
	    [
	    	'->'
 			,{text   : 'Copy',iconCls:'disk',scope:this,cls:'x-btn-default-small',handler: function() 
	        {
 				var form=this.getForm();
 				
 				
 				var post='season_id='+this.season_id+"&rank_type_id="+this.copy_rank_id;
 				 
 				var url='index.php/statistics/post_copy_rank_type/'+App.TOKEN;
 				YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
 				{
					
					//console.log(o.responseText);
					var w=Ext.getCmp(this.window_id);
					if(w)w.hide();
					else Ext.MessageBox.alert('Success','Copy Complete');
					
 				},failure:App.error.xhr},post);
 				//form.submit

	        }}        
	    ];

        this.callParent(arguments);  
        this.init_season_menu();  	    	
	}
	
	
	
});

}