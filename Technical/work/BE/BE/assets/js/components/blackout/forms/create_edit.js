 var bkclass = 'Spectrum.forms.blackout';
if(!App.dom.definedExt(bkclass)){
Ext.define(bkclass,
{
	extend: 'Ext.spectrumforms', 
	initComponent: function(config) 
	{
		this.callParent(arguments);
	},

	constructor     : function(config)
	{  
		this.window_id=config.window_id;
		 config.centerAll   = true;
   
	                            
	    config.fieldDefaults= 
	    { 
			labelWidth  : 150  
	    };    

	    config.items=
	    [ 
	        {xtype:'hidden',name:'bo_id',value:-1},
	        {xtype:'hidden',name:'season_id',value:config.season_id},
	        {                        
	            xtype       : 'textfield', 
	            name        : 'bo_user_desc',
				value:'',
	            emptyText       : 'Roster Blackout',
	            fieldLabel  : 'Description',
 
	            allowBlank  : false
	        },
	         {                        
	            xtype       : 'datefield', 
	            name        : 'bo_start_date',
				value       : new Date(),//default to TODAY
				format      : 'Y-m-d',
	            fieldLabel  : 'Start Date',
	             
	            allowBlank  : false
	        }
	        ,{                        
	            xtype       : 'datefield', 

	            name        : 'bo_end_date',
				format      : 'Y-m-d',
	            fieldLabel  : 'End Date',
 
	            allowBlank  : false

	           // margins     :'0 0 0 5'
	        } 
	        ,{                        
	            xtype           : 'combo',
	            emptyText       : 'Blackout type',
	            name            : 'bo_type_id',

	            width           : 350,
	            mode            : 'local',
	            queryMode       : 'local',
	            forceSelection  : true,
	            editable        : false,

	            fieldLabel      :'Type', 
	            displayField    : 'bo_type_name',
	            valueField      : 'bo_type_id',
	            value           : 1,   //default value as not blank
	            allowBlank      : false,
	            store           : Ext.create('Ext.data.Store', 
                    			{
	                                fields: ['bo_type_id', 'bo_type_name'],
	                                data : [
	                                    {"bo_type_id":1, "bo_type_name":"Soft"},//use INT not string for ids, same as MODEL
	                                    {"bo_type_id":2, "bo_type_name":"Hard"}
	                                ]})
	        }                

	    ];
	    config.bottomItems  =   
	   [
		   "->"
		   ,{
		   	   xtype:"button",
		   	   text:"Save", 
		   	   scope:this,
		   	   handler:function()
			   {
 
				   var form=this.getForm();
			       if (!form.isValid()) {return;}
 
			       form.submit(
			       {
			       	   //it is actually create or update depends on pk
			           url: 'index.php/leagues/json_update_blackout/'+App.TOKEN,
 
			           scope:this,
			           
			           success: function(form, action)
			           {
			               var r=YAHOO.lang.JSON.parse(action.response.responseText);
 
			               if(r.result=='-1')Ext.MessageBox.alert('Problem','Start Date has conflict');
			               else if(r.result=='-2')Ext.MessageBox.alert('Problem','End Date has conflict');
			               else if(r.result=='-3')Ext.MessageBox.alert('Problem','There are other Dates defined between this range');
			               else
			               {
			               	   var w = Ext.getCmp(this.window_id);
							   if(w) w.hide();
							   else Ext.MessageBox.alert('Complete','Blackout Dates Saved');
							   
			               }
			           },
			           failure:function(form, action){ App.error.xhr(action.response);  }
			       });     
			       
			   }//end of handler
			}//end fo btn
       ];   
              
	                  

	                  
	                  
 		this.callParent(arguments); 
	}
});}