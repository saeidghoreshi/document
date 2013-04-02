var gclass='Spectrum.grids.wizard_games';
if(!App.dom.definedExt(gclass))//{    
Ext.define(gclass,
{ 
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
 
	constructor     : function(config)
    {  		
		config.collapsible= false;
		config.margin='0 0 0 0';
		config.padding=0;
		config.bodyStyle='padding-right:0px; padding-top:0px';
		
		
		config.store= Ext.create('Ext.data.Store',{
					fields:[
						'home_name'
						,'away_name'
						,'venue_name'
						,'display_start_time'
						,{name:'game_date',type:'date'}
						,'timeslot'  
						,'g_index'
					]
					,data:[]
		    	});
		config.stateful=true;
		config.width="100%";
		config.height= 300;
		//height: '90%',
		config.columns=
		[
		   {dataIndex:'home_name',flex:1,  text:'Home'  }
		   ,{dataIndex:'away_name',flex:1,  text:'Away'  }
		   ,{dataIndex:'venue_name',flex:1,  text:'Venue'  }
		   ,{dataIndex:'game_date',width:125, text:'Date' 
			    ,renderer: Ext.util.Format.dateRenderer('M j, Y') }

		   ,{dataIndex:'display_start_time',width:75,  text:'At'  } 

		   //Remove these for live: these are great columns for debugging. so leave them as comments!
		    //  ,{dataIndex:'timeslot',width:45,  text:'ts'  } 
	  	  //    ,{dataIndex:'g_index',width:45,  text:'g'  } 
 
		];
		var buttons=[];
		if(config.bbar)
		{
			/// the 'Generate Schedule' button is passed by controller
			buttons=config.bbar;
			config.bbar=null;
			buttons.push('');//like a seperator
		}
		
		config.bbar=
		[
           	 buttons		     
		     ,{
		         //http://docs.sencha.com/ext-js/4-0/#!/api/Ext.button.Split
				 xtype:'splitbutton'
				 ,text:'Preview Schedule Sorted by : Date'
				 ,value:'d'
				 ,cls:'x-btn-default-small'
				 ,id:'sb_view_html_btn'
				 ,tooltip:"Make sure pop-ups are enabled on your browser"
				 ,menu:
				 [
					{
						text:'Preview Schedule Sorted by : Team'
						,value:'t'
						,handler:function(o)
						{
							var me = Ext.getCmp('sb_view_html_btn');
							me.value=this.value;
							me.setText(this.text);

						}
					}
					,{
						text:'Preview Schedule Sorted by : Date'
						,value:'d'
						,handler:function(o)
						{
							var me = Ext.getCmp('sb_view_html_btn');
							me.value=this.value;
							me.setText(this.text);
						}
					}
					,{
						text:'Preview Schedule Sorted by : Venue'
						,value:'v'
						,handler:function(o)
						{
							var me = Ext.getCmp('sb_view_html_btn');
							me.value=this.value;
							me.setText(this.text);
						}
					}
				 ]
				 ,handler:function(o)
				 { 
				 	 var post={};
					 post.sort=Ext.getCmp('sb_view_html_btn').value;
  
					 
					 Ext.Ajax.request(
					 {
						 success:function(o)
						 { 
						 	 var w=window.open('','_blank');
							 w.document.write(o.responseText);
						 } 
						 ,failure:App.error.xhr
						 ,url:'index.php/schedule/html_session_schedule/'+App.TOKEN
						 ,params:post
					 });

				 }
				 
		     }
		 ];
    	this.callParent(arguments);
	}//end constructor
	
}); 		
 
 