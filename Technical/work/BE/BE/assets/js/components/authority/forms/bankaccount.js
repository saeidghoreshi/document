var bankFormClass='Spectrum.forms.bankaccount';
if(!App.dom.definedExt(bankFormClass)){//very important workaround for IE and sometimes chrome
Ext.define(bankFormClass,
{                            
	extend: 'Ext.spectrumforms', 
	initComponent: function(config) 
	{
		this.callParent(arguments);
	}, 

	constructor     : function(config)
	{ 
    	if(typeof config.allowBlank=='undefined') {config.allowBlank=false;}
    	if(typeof config.hideDesc  =='undefined') {config.hideDesc=false;}
    	 config.fieldDefaults={labelWidth: 150};
    	//config.layout =  {type: 'vbox',align: 'stretch'};
		config.centerAll=true;
		config.items=
		[
        	//id: used for edit in get started screen, other places will ignore it
        	{xtype:'hidden',name:'bankaccount_id',value:-1,allowBlank:true}
        	
        	,{
			   xtype:'container'
			   ,html:'The following information can be obtained from a blank cheque.  Do not use spaces or '
			   +'dashes when you enter these numbers.  '
			   +'<p><a href="javascript:void(0)" onclick="bankExample();">Click here for an example.</a></p>'
			   +'<br/>'
			  
			}
		    //name
		     ,{                        
		        xtype       : 'textfield',

		        name        : 'bankaccount_name',//fixed this for you, this is the name given in the json_get_bankaccounts
		        emptyText   : '',
		        fieldLabel  : 'Name on the Account',
 
		        allowBlank  : config.allowBlank
		    }
		    //Trans - ins

		    ,{              
		        xtype       : 'textfield',
		        name        : 'transit',
		        emptyText   : '12345',
		        fieldLabel  : 'Transit Number',
		 		maxLength:5,
		 		minLength:5,
		 		 enforceMaxLength :true,
		        allowBlank  : config.allowBlank
		    }
		    ,{              
		        xtype       : 'textfield',
		        name        : 'institution',
		        emptyText   : '123',
		        fieldLabel  : 'Institution Number',
		 		minLength:3,
		 		maxLength:3,
		 		 enforceMaxLength :true,
		        allowBlank  : config.allowBlank
 
		    }

		   //account - bank
		    ,{
		  
		        xtype       : 'textfield',
		        name        : 'account',
		        emptyText   : '112233445566',
		        fieldLabel  : 'Account Number',
		 		minLength:5,
		 		maxLength:15,
		 		 enforceMaxLength :true,	
		        allowBlank  : config.allowBlank
		    }
		    ,{  
		        xtype       : 'combo',
		        name        : 'bankname',
		        emptyText   : '',
		        fieldLabel  : 'Bank',  
		        forceSelection: false,
		        editable    : false,
		        displayField: 'name',
		        valueField  : 'id',
		        queryMode   : 'local',     

		        allowBlank  : config.allowBlank,
		        store       : Ext.create('Ext.data.Store', 
					{
					    fields: ['id', 'name'],
					    data : 
					    [
					        {"id":"rbc", "name":"Royal Bank of Canada (RBC)"},
					        {"id":"td", "name":"Toronto Dominion (TD)"},
					        {"id":"scotia", "name":"Scotiabank"},
					        {'id':'cibc','name':'CIBC'},
					        {'id':'bm','name':'Bank of Montreal (BMO)'},
					        {'id':'o','name':'Other'}
					    ]
					})
		    }

		   //Description
		    
		   
		   ,{              
		        xtype       : 'textarea',

		        name        : 'description',
		        emptyText   : '',
		        fieldLabel  : 'Description',
 
		        flex        : 1,            
		        allowBlank  : true,//is only for motion, not for bank account itself
		        hidden      :config.hideDesc   
		       // margins     : '0 0 0 10'//somehow this makes it further right than everything else
		   }
		];//end of items
		
		if(config.hideDesc==false)
        config.bottomItems=   
        [
            '->'
            ,{   
                 xtype   :"button",
                 text    :"Save",
                 iconCls :'disk',
				 scope:this,
			
                 handler :function()
                 {                     
                     this.save();

                 }   
                                         
            }
        ];

		this.callParent(arguments); 
	}
 	,save:function()
	{
		var form=this.getForm();
		if ( !form.isValid() ) {return;}
		//form.submit(
		Ext.Ajax.request(
         {
             url     : 'index.php/finance/json_new_motion_bankaccount_add/'+App.TOKEN,
             //waitMsg : 'Processing ...',
             params  : form.getValues(),
             method:'POST',
			 disableCaching:true,
			 failure : App.error.xhr,
			 scope:this,
             success : function( action)
             {
                 var res=YAHOO.lang.JSON.parse(action.responseText);
                 if(res.result=="1")
                 {
                     Ext.MessageBox.alert({title:"Status",msg:"Request Sent Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});
 
                 }           
                 if(res.result=="-1")
                 { 
                     Ext.MessageBox.alert({title:"Cannot Process",msg:"Still one pending bankaccount Motion in system", icon: Ext.Msg.ERROR,buttons: Ext.MessageBox.OK});
                 }
                 var win = this.up('window');
                 if(win)
                 {//if the form is in a window - it might not be 
	                 win.hide();
	                 win.destroy();
				 }
             }
         }); 
	}
});  }    