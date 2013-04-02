 var fst = 'Spectrum.forms.division_fees';
if(!App.dom.definedExt(fst)){
Ext.define(fst,
{
    extend: 'Ext.spectrumforms',    // SB : changed how this works so it extends from spectrumforms , consistent look and feel inherited
    initComponent: function(config) {this.callParent(arguments);},
 
    constructor     : function(config)
    {  
    	 this.window_id  =config.window_id; //so we can hide the window on save
		 config.centerAll=true;
 
                                    
     /*    config.fieldDefaults= 
         {
 
                labelWidth: 100
 
        };*/
         config.items=
        [
            
			{xtype: 'hidden',name : 'season_id',value:-1}//so it is easy to use form.submit
			,{xtype: 'hidden',name : 'division_id',value:-1}

             ,{              
                 xtype       : 'numberfield',
                 minValue    : 0, 
                 step        : 25,
                 value       :0,//blankvalue 
                 name        : 'deposit_amount',
                 emptyText   : '',
                 fieldLabel  : 'Deposit Amount',
                 labelWidth  : 100,
                  
                 allowBlank  : true//,
                 
                // margins     : '0 10 0 10'
             }

             ,{              
                 xtype       : 'numberfield',
                 minValue    : 0, 
                 step        : 25,
                 value       :0,//blankvalue 
                 name        : 'fees_amount',
                 emptyText   : '',
                 fieldLabel  : 'Fees Amount',
                 labelWidth  : 100,
                  
                 allowBlank  : true//,
               //  margins     : '0 10 0 10'
             }

        ];
        config.bottomItems=
        [
		    '->',
            {   
                xtype   :"button",
                text    :"Save",
 
                scope:this,

                handler :function()
                {  
                    var form=this.getForm();
                   var values   =form.getValues(); 

                   if(!form.isValid()) {return;}
                    //use built in form submit function.  avoids using custom ajax and messing with field data manually
                    form.submit(
                    {
                        url     : 'index.php/divisions/json_update_season_division_custom_rates/'+App.TOKEN,
                        scope:this,
                        success : function(response)
                        {

                            var w=Ext.getCmp(this.window_id);
                            if(w) w.hide();
                            else                        //if a window exists hide it, otherwise confirm the success                
                                Ext.MessageBox.show({title:"Status",msg:"Custom Rates Setup Successfully", icon: Ext.Msg.INFO,buttons: Ext.MessageBox.OK});

                             
                        },
                        
					   failure:function(form, action){ App.error.xhr(action.response);  }
                    });    
                   
                }                          
    		}
    	];
		   
        this.callParent(arguments);    	    	
	}
	
	
	
});

}


    	
                                                  
 