var fst = 'Spectrum.forms.divisions';
if(!App.dom.definedExt(fst)){
Ext.define(fst,
{

    extend: 'Ext.spectrumforms',     //extend: 'Ext.form.Panel', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    season_id:-1,
    division_id:-1,
    window_id:null,
    constructor     : function(config)
    {  
    	this.window_id=config.window_id;
		 config.centerAll=true;
		 
    	if(!config.id){config.id='c_divisions_form'};
    	if(config.season_id)  {this.season_id=config.season_id;}
    	if(config.division_id){this.division_id=config.division_id;}
    	var wildcard=[];
    	if(config.wildcard){wildcard=config.wildcard;}

    	if(Ext.getCmp(config.id)){Ext.getCmp(config.id).destroy();}
		 config.fieldDefaults={labelWidth: 125,msgTarget: 'side',autoFitErrors: false};
		 
	       // defaults: {anchor: '100%'},
	    config.items=
	    [
		   // {xtype: 'hidden',name : 'rank_type_id',value:-1}
		    { xtype: 'hidden',name : 'season_id',  value:this.season_id}
		    ,{xtype: 'hidden',name : 'division_id',value:-1}
		    //,{xtype: 'textfield',name : 'parent_rank_type_id',hidden:true,value:-1}
	        ,{
	        	xtype: 'textfield',
	        	name : 'division_name',   
	        	fieldLabel: 'Name',
	        	emptyText:'New Division',
	        	value:'',
	        	allowBlank: true
	        }
	        ,{	
	        	xtype:'combo' ,
	        	tooltip         : 'Pool',
		        //emptyText       : 'Wildcard for',
		        fieldLabel: 'Pool Division',
		        mode            : 'local',
		        triggerAction   : 'all',
		        forceSelection  : true,
		        editable        : false, 
		        name:'only_teams',
		        value:'t',
		        displayField    : 'only_teams_display',
                valueField      : 'only_teams_id',
                queryMode       : 'local',
		        store:Ext.create('Ext.data.Store', 
                {
                    fields : ['only_teams_id', 'only_teams_display']
                    ,data   :[  { only_teams_id:'t',only_teams_display:'Yes, this will contain teams'}
                        		,{only_teams_id:'f',only_teams_display:'No, only other subdivisions'}  ]
                }) 
            }
	    ];
	    config.bottomItems=
	    [
		    '->',
 			{text   : 'Save',scope:this,handler: function() 
	        {
	            var form = this.getForm();                      
	            var data=[];
	            //post all data
	            Ext.iterate(form.getValues(), function(key, value) 
	            {
					if(key=='division_name')
						value=escape(value);
	                data.push(key+"="+value);
	            }, this);
	            var post = data.join("&");
	             
	            var url='index.php/divisions/post_create_division/'+App.TOKEN;
	            var callback={failure:App.error.xhr,scope:this,success:function(o)
	            {
	                var r=o.responseText;
					if(isNaN(r)||r<=0)
					{
						if(r==-100)
						{
							Ext.MessageBox.show({title:'Cannot change type :',msg:'This division contains teams',
								icon:Ext.MessageBox.ERROR,buttons:Ext.MessageBox.OK});
							
							return;
						}
						else if(r==-200)
						{
							Ext.MessageBox.show({title:'Cannot change type :',msg:'This division contains subdivisions',
								icon:Ext.MessageBox.ERROR,buttons:Ext.MessageBox.OK});
							return;	
						}
						
						Ext.MessageBox.show({title:'Could not create :',msg:r,icon:Ext.MessageBox.ERROR,buttons:Ext.MessageBox.OK});
					}
					else
					{
						//Ext.MessageBox.show({title:'Success',msg:'',icon:Ext.MessageBox.INFO,buttons:Ext.MessageBox.OK});
 
						if(Ext.getCmp(this.window_id))Ext.getCmp(this.window_id).hide();
					}
					
	            }};

	            YAHOO.util.Connect.asyncRequest('POST',url,callback,post);

	        }}
	            
	                
	    ];
	         
        this.callParent(arguments);    	    	
	}
	
	
	
});

}
