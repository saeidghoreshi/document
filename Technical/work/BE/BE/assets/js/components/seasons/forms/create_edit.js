
var lgf="Spectrum.forms.season";
if(!App.dom.definedExt(lgf)){
Ext.define(lgf,
{
	extend: 'Ext.spectrumforms', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    window_id:null,           
    constructor     : function(config)
    {        
    	//config.bodyPadding= bodyPadding;
		//config.border=false;
		config.centerAll=true;
		this.window_id=config.window_id;
        var collection=Ext.create('Ext.data.Store', 
        {
		    fields: ['id', 'desc'],
		    data : [
		        {"id":"1", "desc":"Required"},
		        {"id":"2", "desc":"Requested"},
		        {"id":"3", "desc":"None"}
	    ]});
        config.fieldDefaults={labelWidth:160};
    
		config.items= 
        [
        
        	{
        		//used for edit
        		xtype:'hidden',
        		name:'season_id',
        		value:-1
        	}
        	,{//regular section:
                xtype:'fieldset',
 
                collapsible:false,
 
                
                autoHeight:true,
                autoWidth:true,
                
            //    title:'Enable Registration',
            items:[
             {                        
                xtype       : 'textfield',
 
                name        : 'season_name',
                emptyText   : 'New Season',
                fieldLabel  : 'Season Name',

                allowBlank  : false
            }
 
             ,{                        
                xtype       : 'datefield',

                name        : 'effective_range_start',
                format:'Y/m/d',
                emptyText   : 'Start Date',
                fieldLabel  : 'Start Date',

                allowBlank  : false
            }
            ,{                        
                xtype       : 'datefield',
         
                name        : 'effective_range_end',
                format:'Y/m/d',
                emptyText   : 'End Date',
                fieldLabel  : 'End Date',
                allowBlank  : false
                
            }        
               
           
             ,{                        
                xtype       : 'checkbox', 
                name        : 'isactive',
                value:true,
                fieldLabel  : 'Season is Active'
 
            }    
		]}    
            /* ,{                        
                xtype       : 'checkbox', 
                name        : '',
                id:          'ff',///probbaly depreciated, but left it in just in case
                fieldLabel  : 'Registration Enabled'
 
            }  */ 
            
 			,{//registration section:
                xtype:'fieldset',
                id:'ff', 
                checkboxName:'reg_needed',//same as name:... if this was a regular cmp
                collapsible:true,
                checkboxToggle: true,
               // collapsed: false,
                
                autoHeight:true,
                autoWidth:true,
                
                title:'Enable Registration',
 
                items:
                [
                	{
			            xtype: 'datefield', 
			            fieldLabel: 'Registration Opens'
			           , name: 'reg_range_start'//,flex:1
			            ,format:'Y/m/d'
			            ,value:new Date()
			        }
			        ,{
			            xtype: 'datefield', 
			            fieldLabel: 'Registration Closes', 
			            name: 'reg_range_end'//,flex:1,margins:'0 0 0 5'
			            ,format:'Y/m/d'
			        }

		       
		            , {
		                xtype: 'combo', 
		                fieldLabel: 'Registration Deposit', 
		                name: 'deposit_status', 
		                store:collection, 
		                queryMode: 'local', 
		                displayField: 'desc', 
		                valueField: 'id'//,flex:1
		            }
		            ,{ 
		                xtype:"numberfield",
		                name : 'deposit_amount', 
		                fieldLabel: 'Deposit Amount', 
		                minValue: 0, 
		                step: 25
		            } 
		            ,{
		                xtype: 'combo', 
		                fieldLabel: 'League Fees', 
		                name: 'fees_status',
		                store:collection, 
		                queryMode: 'local', 
		                displayField: 'desc', 
		                valueField: 'id'// 
		            }
		            ,{
		                xtype:"numberfield",
		                name : 'fees_amount', 
		                fieldLabel: 'League Fees Amount', 
		                minValue: 0, 
		                step: 25//,flex:1,margins:'0 0 0 5'
		            }
		            
                ]
                
			}

      
        ];//end of items
 
         config.bottomItems=
	     [
	         "->" ,
	         { 
	             text:"Save",
 
	             scope:this, 
	             handler     :function()
	            {            
                	var form=this.getForm();
	                if (!form.isValid()) {return;}
	                form.submit({
	                     url: 'index.php/season/json_new_season/'+App.TOKEN,
	                     //waitMsg: 'Processing Request...',
	                     scope:this,
	                     failure:function(f,a){App.error.xhr(a.response);},
	                     params: {reg_needed:Ext.getCmp("ff").checkboxCmp.checked ? 't' : 'f'},
	                     success: function(form, action)
	                     {
	                     	 try
	                     	 {
	                          	var r=YAHOO.lang.JSON.parse(action.response.responseText).result;
 
								 if(this.window_id && Ext.getCmp(this.window_id))
									Ext.getCmp(this.window_id).hide();
		                        
							 }
							 catch(e)
	                         {
	                         	 //i n case JSON.parse fails
	                         	 //which would mean either permissions or PHP error thrown
	                            App.error.xhr(action.response);
								 
	                         }
	                     }
	                 });
	            }//end of save button                          
	         }
	     ];          
		this.callParent(arguments); 
	}
});}


  
         