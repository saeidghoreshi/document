if(!App.dom.definedExt('Ext.spectrumgrids.user')){
Ext.define('Ext.spectrumgrids.user',    
{
    extend: 'Ext.spectrumgrids', 
    initComponent: function(config) 
    {       
        this.callParent(arguments);
    }
 
    ,constructor : function(config) 
    {   
 
        config.columns  =
        [    
            { dataIndex: 'person_lname', flex:1,text      : 'Last Name',  sortable : true  }		
            ,{dataIndex: 'person_fname', flex:1,text      : 'First Name',  sortable : true  }		
 
            //,{dataIndex : 'role_name', width:80, text: 'Roles'}
        ];
 
                                  
         config.fields       = ['user_id','person_id','person_fname','person_lname'/*,'role_id','role_name'*/];
         config.sorters      = config.fields;

         if(!config.url) 
            config.url          = "index.php/permissions/json_org_users_distinct/"+App.TOKEN;
        // config.extraParams  = {start:0,limit:config.pageSize};
         if(!config.width)config.width        = '100%';
 
  	
  		if(!config.listeners)
  		config.listeners={};
  		if(!config.override_selectionchange)
  		config.listeners.selectionchange=function(sm,records)
            {   
                if(typeof records =='undefined' || records.length==0)return false;
                var rec=records[0].raw;
                
                
                
                if(Ext.getCmp("fname")) Ext.getCmp("fname").setValue(rec.person_fname);// this object 'fname' does not exist in this grid
                if(Ext.getCmp("lname")) Ext.getCmp("lname").setValue(rec.person_lname);//but im nto deleting it because it may cause problems somewhere
                //if that field is used on one specific instance of this grid, the listener should be added in teh contstructor of that specific object
                //not in base class
            };
  		
  		
  	
        this.callParent(arguments); 
        
    }
     
});}