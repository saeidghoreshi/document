
var fclass = 'Spectrum.forms.teams.division';
if(!App.dom.definedExt(fclass)){
Ext.define(fclass,
{
    
    extend: 'Ext.spectrumforms', //extend: 'Ext.form.Panel', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    
    constructor     : function(config)
    {  
    	this.division_id=config.division_id;
    	Ext.QuickTips.init();
		var id='divwindowteam_form';//+Math.random();
    	if(!config.id){config.id=id};
    	config.bodyPadding=5;
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		 
		// {id:id,title: '',autoHeight: true,resizable:false,bodyPadding: 10,width: 600,//height:250,
		this.season_id=config.season_id;
	    config.fieldDefaults= {labelWidth: 125,msgTarget: 'side',autoFitErrors: false}
	    
	    config.defaults= {anchor: '100%'};
	    config.items=
	    [
		    {xtype: 'hidden',name : 'team_id',id:'field_team_id',value:-1}
		   , {xtype: 'hidden',name : 'swap_team_id',id:'form_new_team_id',value:-1}
		    ,{xtype: 'hidden',name : 'season_id',value:-1}
		    ,{xtype: 'hidden',name : 'new_division_id',value:-1,id:'form_new_division_id'}//id so its changed by dropdown menu
		    ,{xtype: 'hidden',name : 'division_id',value:-1}//id of current team
		    
	        ,{xtype: 'displayfield',flex : 1,name : 'team_name',fieldLabel: 'Team'}
	        ,{xtype: 'displayfield',flex : 1,name : 'division_name',fieldLabel: 'Current Division'}
	       //  ,{xtype: 'displayfield',flex : 1,name : 'season_name',fieldLabel: ''}
	       
	       ,{xtype:'fieldcontainer',fieldLabel:'New Division',layout:'hbox',items:[
				{xtype:'button',menu:[],id:'btn_new_div_select',text:"Select a division",fieldLabel:'test'}
			]}
			

			,{xtype:'fieldcontainer',fieldLabel:'Handle Record',layout:'hbox',items:
				[
					{xtype:'radiofield',flex:1,boxLabel:'Keep Record',name:'keep_clear',
						id:'self_keep_clear_k',inputValue:'k',checked:true
						,tooltip:'testing'}
					,{xtype:'radiofield',flex:1,boxLabel:'Clear Record',name:'keep_clear',
						id:'self_keep_clear_c',inputValue:'c',checked:false
						,tooltip:'another tt'}			
				]
			}
			
		   ,{xtype:'fieldcontainer',fieldLabel:'Swap with team',layout:'hbox',items:[
				{xtype:'button',menu:[],id:'btn_swap_team_select',text:this.blankTeam,disabled:true}
			]}
			,{xtype:'fieldcontainer',fieldLabel:'Handle Record',layout:'hbox',items:
				[
					{xtype:'radiofield',flex:1,boxLabel:'Keep Record',name:'swap_keep_clear',
						id:'radio_keep_clear_k',inputValue:'k',checked:true,disabled:true
						,tooltip:'testing'}
					,{xtype:'radiofield',flex:1,boxLabel:'Clear Record',name:'swap_keep_clear',
						id:'radio_keep_clear_c',inputValue:'c',checked:false,disabled:true
						,tooltip:'another tt'}			
				]
			}
			,{xtype: 'datefield',anchor: '100%',
		        fieldLabel: 'Cutoff Date',name: 'input_date',
		      	format:'F d, Y',altFormats: 'Y-m-d',
		        value: new Date() ,
		        maxValue: new Date()  // limited to the current date or prior
		    }
	        
	        //,{xtype:'grid',id:''}
	    ];
	    config.bottomItems=
	    [
			'->',
 			{text   : 'Save',handler: function() 
	        {
	            var form = this.up('form').getForm();
	            if (!form.isValid()) {return;}
	                                     
	            var data=[];
	            Ext.iterate(form.getValues(), function(key, value) 
	            {
					
					value=escape(value);
	                data.push(key+"="+value);
	            }, this);
	            
	            Ext.MessageBox.confirm('Success','Some games are now being ignored in the standings.  Would you like to move "Win Percentage" to  '
					+'the highest rank?  This will keep your standings consistent with the new team records',function(btn_id)
				{
					var force_win_perc='t';
					if(btn_id!='yes' && btn_id!= 'ok') {force_win_perc='f';}
					data.push('force_win_perc='+force_win_perc)
					
					 
		            var post = data.join("&"); 
							
							
							
							
							
		            //will create, or change name
		            var url='index.php/divisions/post_team_div/'+App.TOKEN;
		           
		            var callback={scope:this,failure:App.error.xhr,success:function(o)
		            {
		                var r=o.responseText;
						if(isNaN(r)){App.error.xhr(o);}

						this.up('window').hide(); 
		            }};
		            YAHOO.util.Connect.asyncRequest('POST',url,callback,post);
				},this);
				
	        }}        
	    ];

	    	
        this.callParent(arguments);    	
        //now build divisions menu
		this.divMenu(config.season_id);
	}//end constructor
	,divMenu:function(season_id)
	{
 
		var post='season_id='+season_id;
		var divurl='index.php/divisions/json_concated_names/'+App.TOKEN;
		YAHOO.util.Connect.asyncRequest('POST',divurl,{failure:App.error.xhr,scope:this,success:function(o)
		{
			var name, id,divs=YAHOO.lang.JSON.parse(o.responseText);
				
			var _filter=new Array();	
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;
				//save name and id
        		Ext.getCmp('btn_new_div_select').setText(name);
        		Ext.getCmp('form_new_division_id').setValue(id);
        		//reset current team selection
        		Ext.getCmp('btn_swap_team_select').setText(this.blankTeam);
        		Ext.getCmp('form_new_team_id').setValue(-1); 
        		
				Ext.getCmp('radio_keep_clear_k').setDisabled(true);
				Ext.getCmp('radio_keep_clear_c').setDisabled(true);
        		//get teams for this new div selection
        		this.buildTeamsMenu(id);

			};
			divs.push({division_id:0,division_name:"-Unassigned-"});
			for(i in divs)if(divs[i] && divs[i]['division_id'])
			{	
				id  =divs[i]['division_id'];
				if(id==this.division_id) {continue;}//do not swap into existing div
				
				name=divs[i]['division_name'];//+" : "+seasons[i]['display_start']+" - "+seasons[i]['display_end'];
				
        		_filter.push({text:name,value:id,scope:this,handler:itemClick});
			}
			Ext.getCmp('btn_new_div_select').menu=Ext.create('Spectrum.btn_menu',{items:_filter});


		}},post);    	
	}
	,blankTeam:"(Move and do not swap)"
	,buildTeamsMenu:function(div_id)
	{
		 
		
		var post='season_id='+this.season_id+"&division_id="+div_id;
		
		 
		
		var url='index.php/divisions/json_season_div_teams/'+App.TOKEN;
		
		
		YAHOO.util.Connect.asyncRequest('POST',url,{failure:App.error.xhr,scope:this,success:function(o)
		{
			var name, id,divs=YAHOO.lang.JSON.parse(o.responseText);
				
			var _filter=new Array();	
			var itemClick=function(o,e)
			{
        		var name = o.text;
				var id   = o.value;
				//save name and id
        		Ext.getCmp('btn_swap_team_select').setText(name);
        		Ext.getCmp('form_new_team_id').setValue(id); 

				Ext.getCmp('radio_keep_clear_k').setDisabled(false);
				Ext.getCmp('radio_keep_clear_c').setDisabled(false);
			};
			divs.push({team_id:-1,team_name:this.blankTeam});
			for(i in divs) if(divs[i] && divs[i]['team_id'])
			{	
				id  =divs[i]['team_id'];
				//if(id==this.division_id) {continue;}//do not swap into existing div
				
				name=divs[i]['team_name'];//+" : "+seasons[i]['display_start']+" - "+seasons[i]['display_end'];
				
        		_filter.push({text:name,value:id,scope:this,handler:itemClick});
			}
			Ext.getCmp('btn_swap_team_select').setDisabled(false);
			Ext.getCmp('btn_swap_team_select').menu=Ext.create('Spectrum.btn_menu',{items:_filter});
			
		}},post);    			
		//get all teams with this assignment
		
		
	}
	
});
}