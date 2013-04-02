var formatStatus=function(input)
{
	var spl=input.split(",");
	var text=spl[0];
	var icon=spl[1];
	var s_id=spl[2];
	var img_src="<img src='http://endeavor.servilliansolutionsinc.com/global_assets/silk/"
				+icon+".png'/>";
	return img_src+text;
		
}
 var gr_model_id='GameValidate';
var gridclass='Spectrum.grids.validate_results';
if(!App.dom.definedExt(gridclass)){ 
Ext.define(gridclass,
{
   // extend: 'Ext.grid.Panel', 
    extend: 'Ext.spectrumgrids',  
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
	game_id:null,
	discarded:true,
    set_game_id:function(i)
    {
		this.game_id=i;

    }, 
	get_game_id:function()
    {
		return this.game_id;
    },
    //TODO: set and gets for the status booleans then post ids
    refresh:function()
    {
 
    	var post='game_id='+this.game_id+"&hide_discarded="+this.discarded;//+"&status_ids="+YAHOO.lang.JSON.stringify(this.status_ids);
    	 
    	var url='index.php/games/json_game_results/'+App.TOKEN;
    	YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
    	{
			var results=YAHOO.lang.JSON.parse(o.responseText);
			
			this.store.loadData(results);
			
			//if(!games_found.length){Ext.MessageBox.alert('No submissions found','');}
			
    	}},post);
    	//get games by season
		
    },
	scoreLen:3,
    constructor     : function(config)
    { 
 
        var id='results_grid__';//+Math.random();//default id that can overwrite
        if(!config.id){config.id=id;}
        if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();//.log('destroy gamesgrid='+id);
        var buttons=config.bbar;
        var renderTo=config.renderTo;
        }
        config.store= Ext.create( 'Ext.data.Store',
		{
			groupField:'display_header',
			remoteSort:false,
	        autoDestroy: false,
	        model:gr_model_id,
	        data: []//games
	    });   
	    

	   // config= {renderTo: renderTo,id:id,
	   config.collapsible= false;//,store: g_store,
	    //stateful: true,
	    config.width="100%";
	     
	    config.features=
	    [
	        Ext.create('Ext.grid.feature.Grouping',
			{
			    enableGroupingMenu: false,
			  // groupHeaderTpl: '{name}, ({rows.length} Submission{[values.rows.length > 1 ? "s" : ""]})'
			    groupHeaderTpl: '{name}'
			})

	    ];
	    config.title='Results';

		config.columns=
	    [

			{text     : 'Entered By',flex:.5,		           sortable : true,dataIndex: 'form_name'    }	  
			,{text     : 'Date',flex:.25,		           sortable : true,dataIndex: 'display_date'    }	  
			,{text     : 'Status',flex:.25,		       sortable : true,dataIndex: 'csv_status'  ,renderer:formatStatus  }	 

		];
		config.dockedItems =
        [
            {
                dock    : 'top',
                xtype   : 'toolbar',
                items   : 
				[
									
					{
						xtype:'button',
						iconCls:'',
						id:'check_hide_discarded',
						iconCls:'stop',
						enableToggle: true,
						text:'Hide Discarded' 
						 ,pressed:this.discarded,scope:this
						 ,toggleHandler:function(btn,checked)
						  {

							this.discarded=checked;
							
							this.refresh();
						}
					}
				
				]
            }
            ,{
                dock    : 'bottom',
                xtype   : 'toolbar',
                items   : 	
                [

		
					{xtype:'displayfield', value: 'Home:',width:70, id:'lbl_validate_home_input'}
					,{xtype:'textfield' ,  value:'',      width:30,id:'validate_home_input',maskRe:/\d/,maxLength:this.scoreLen}
					,{xtype:'displayfield', value:'Away:',width:70,id:'lbl_validate_away_input'}
					,{xtype:'textfield' ,   value:'',     width:30,id:'validate_away_input',maskRe:/\d/,maxLength:this.scoreLen}

					,{xtype:'button',id:'btn_input_validate',iconCls:'add',tooltip:'Input score',scope:this,handler:function(o)
					{
						if(!this.game_id||this.game_id<0) return;//no game / row selected
						var h=Ext.getCmp('validate_home_input').getValue();
						var a=Ext.getCmp('validate_away_input').getValue();
						if(isNaN(a)||isNaN(h)||a.length>this.scoreLen || h.length>this.scoreLen)//check valid
							{return;}
						var post="game_id="+this.game_id+'&home_score='+h+"&away_score="+a;
						var url ='index.php/statistics/post_valid_score/'+App.TOKEN;
						//.log(post);
						YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
						{
							this.refresh();
						}},post);
					}}
					,'->'
					//,'-'
					,{xtype:'button',id:'btn_res_validate',iconCls:'tick',tooltip:'Validate this result',scope:this,handler:function(o)
					{
				var rows=this.getSelectionModel().getSelection();	
				
				if(!rows.length){return;}
				var game_result_id=rows[0].get('game_result_id');
				var game_id=       rows[0].get('game_id');
				var post='game_result_id='+game_result_id+"&game_id="+game_id;
				var url='index.php/statistics/post_save_results/'+App.TOKEN;
				//.log(post);
				YAHOO.util.Connect.asyncRequest('POST',url,{scope:this,success:function(o)
				{
					this.refresh();
				}},post);
			}}
			
		        ]
			}
		];
 
			
			
		
    
    
        this.callParent(arguments);
	}
	
	
}); 

}