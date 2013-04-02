

var formStandings = 'Spectrum.forms.standings';
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
    window_id:null,
    constructor     : function(config)
    {  
    	if(!config.id){config.id = 'c_standings_form'};
    	
    	this.window_id=config.window_id;
    	//.log('i am in window '+config.window_id);
    	if(config.season_id){this.season_id=config.season_id;}
    	var wildcard=[];
    	if(config.wildcard){wildcard=config.wildcard;}
		
		
		
		
    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		 config.title= '';
		 config.autoHeight= true;//,resizable:false,
		 config.bodyPadding= 10;
		 config.width= 600;
		 config.height=250;
		 //config.hidden=false;
	     config.fieldDefaults= {labelWidth: 125,msgTarget: 'side',autoFitErrors: false};
	     config.defaults= {anchor: '100%'};
	     config.items=
	     [
		    {xtype: 'hidden',name : 'rank_type_id',value:-1}
		    ,{xtype: 'hidden',name : 'season_id',value:this.season_id}
		    //,{xtype: 'textfield',name : 'parent_rank_type_id',hidden:true,value:-1}
	        ,{xtype: 'textfield',flex : 1,name : 'rank_name',   fieldLabel: 'Name',value:'League Standings',allowBlank: false}
	        ,{xtype: 'textfield',flex : 1,name : 'pts_per_win', fieldLabel: 'Points per Win', allowBlank: false,value:2}
	        ,{xtype: 'textfield',flex : 1,name : 'pts_per_loss',fieldLabel: 'Points per Loss',allowBlank: false,value:0}
	        ,{xtype: 'textfield',flex : 1,name : 'pts_per_tie', fieldLabel: 'Points per Tie', allowBlank: false,value:1}
	        ,{xtype:'combo' ,
	        		tooltip         : 'Wildcard',
		            emptyText       : 'Wildcard for',
		            mode            : 'local',
		            triggerAction   : 'all',
		            forceSelection  : false,
		            editable        : false, 
		            name:'parent_rank_type_id',
		            id:'combo_parent_rank_type_id',
		            displayField    : 'rank_name',
                    valueField      : 'rank_type_id',
                    queryMode       : 'local',
		            store:Ext.create('Ext.data.Store', 
                    {
                        fields : ['rank_name', 'rank_type_id']
                        ,data   :wildcard
                    }) 
            }
	    ];
 
		 config.bottomItems=
		[

	    	'->'
 			,{
 				text   : 'save',
 				scope:this,
 				handler: function() 
	        {
	            // .up is magic
	            var form = this.getForm();
	            if (!form.isValid()) {return;}
	                                    
	            var data=[];
	            //post all data
	            Ext.iterate(form.getValues(), function(key, value) 
	            {
					if(key=='rank_name')
						value=escape(value);
	                data.push(key+"="+value);
	            }, this);
	            var post = data.join("&");
	            var url='index.php/statistics/post_rank_type/'+App.TOKEN;
	            var callback={failure:App.error.xhr,scope:this,success:function(o)
	            {
	                var r=o.responseText;
					if(isNaN(r)||r<=0)
					{
						Ext.MessageBox.show({title:'Could not create :',msg:r,icon:Ext.MessageBox.ERROR,buttons:Ext.MessageBox.OK});
					}
					else
					{
						
						var w = Ext.getCmp(this.window_id);
						
						if(w) Ext.getCmp(this.window_id).hide();//hide window if possible
						else Ext.MessageBox.alert('Success','Standings saved.');//if we dont have pointer to window then dialog box
						
					}
	            }};
	            YAHOO.util.Connect.asyncRequest('POST',url,callback,post);

	        }}        
	    ];

        this.callParent(arguments);    	    	
	}
	
	
	
});

}