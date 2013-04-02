 if(!financeForms)var financeForms={};   //do not overwrite if already made by 'finance' component
  //else console.log('financeForms already exists, so just add functions to it do not overwrite');
var bankExample=function()
{
	Shadowbox.open(
	{
		content:    "/assets/images/cheque_sample.jpg",
		player:     "img",
		title:      ""
	});
} 		
    //BankAccount
    //config parameter NOT required , can be left blank
financeForms.form_bankaccount_add_motion=function(config)//why doesnt this component extend from spectrumforms?? how am i supposed to pass in a config object
    {   
    	if(!config) config={};//if not passed in at all, make it an empty object

		var bankFormClass='Spectrum.forms.bankaccount';                    

                                               
         return  Ext.create(bankFormClass,config);
    };
    
    
financeForms.form_bankaccount_del_motion=function()
    {   
         var me=this;
         var form=new Ext.spectrumforms(
         {   
                autowidth   : true,
                autoHeight  : true,
 
                items: 
                [
                    {              
                        xtype       : 'textarea',

                        name        : 'description',
                        emptyText   : '',
                        fieldLabel  : 'Description',
                        labelWidth  : 100,
                        flex        : 1,
                        allowBlank  : false
                    }   
                ]
         });                                                          
         return  form;
    };
    //Withdrawal
financeForms.form_withdraw_motion=function(fee_rate,fee_amount)
    {   
         var me=this;
         var form=new Ext.spectrumforms(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            border      : false,
                                                    
                            fieldDefaults: 
                            {
                                labelAlign: 'left',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                          
                                        {              
                                                    xtype       : 'displayfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    html        : 'Please Enter the amount you wish to withdraw'
                                        },  
                                        {              
                                                    xtype       : 'textfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'amount',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Amount',
                                                    labelWidth  : 100,
                                                    allowBlank  : false,
                                                    listeners  : 
                                                    {
                                                        change:function( _this, newValue, oldValue, eOpts )
                                                        {
                                                            var finalValue=parseFloat(newValue)+parseFloat(new Number(parseFloat(new Number(fee_rate).toFixed(4)*newValue)+parseFloat(new Number(fee_amount).toFixed(2))).toFixed(2));
                                                            var totalAmt=new Number(finalValue).toFixed(2);
                                                            Ext.getCmp("sa_eft_total").setValue(totalAmt);
                                                        }
                                                    }
                                                    
                                        },                                       
                                        {            
                                                    id          : 'sa_eft_amount'  ,
                                                    xtype       : 'displayfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'fees',
                                                    fieldLabel  : 'Fees',
                                                    labelWidth  : 100,
                                                    value       : '(rate)'+new Number(fee_rate).toFixed(4)+' (Amount) '+new Number(fee_amount).toFixed(2)
                                                    
                                        },
                                        {           
                                                    id          : "sa_eft_total",
                                                    xtype       : 'displayfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'total',
                                                    fieldLabel  : 'Total',
                                                    labelWidth  : 100,
                                                    allowBlank  : false
                                        },
                                        //Description
                                        {             
                                            xtype       : 'textarea',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'description',
                                            emptyText   : '',
                                            fieldLabel  : 'Description',
                                            labelWidth  : 100,
                                            flex        : 1,
                                            allowBlank  : false
                                        }
                            ]
         });                                                          
         return  form;
    };

    //Rule
financeForms.form_rule_motion=function(ruletypeStore)
    {   
        
         var me=this;
         
         var form=new Ext.spectrumforms(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                           // bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                                                    
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                {
                                    xtype: 'fieldset',
                                    title: 'Setup New Rule',
                                    collapsible: false,
                                    defaults: 
                                    {
                                        labelWidth: 89,
                                        anchor: '100%',
                                        anchor: '100%',
                                        layout: {
                                            type: 'hbox',
                                            defaultMargins: {top: 5, right: 5, bottom: 5, left: 5}
                                        }    
                                    },
                                    items:
                                    [
                                        //rule_type - rule_value
                                        {  
                                                    xtype       : 'combo',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'rule_type_id',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Rule Type',
                                                    labelWidth  : 150,    
                                                    forceSelection: false,
                                                    editable    : false,
                                                    displayField: 'rule_type_name',
                                                    valueField  : 'rule_type_id',
                                                    queryMode   : 'local',     
                                                    flex        : 1,
                                                    allowBlank  : false,
                                                    store       : ruletypeStore                                                
                                                }
                                        ,{              
                                                    xtype       : 'textfield',
                                                    labelStyle  : 'font-weight:bold;padding:0',
                                                    name        : 'rule_value',
                                                    emptyText   : '',
                                                    fieldLabel  : 'Rule Value',
                                                    labelWidth  : 100,
                                                    flex        : 1,
                                                    allowBlank  : false,
                                                    margins     : '0 0 0 10'
                                                }
                                        ,{              
                                            xtype       : 'textarea',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'description',
                                            emptyText   : '',
                                            fieldLabel  : 'Description',
                                            labelWidth  : 100,
                                            flex        : 1,
                                            allowBlank  : false,
                                            margins     : '0 0 0 10'
                                        }
                                    ]
                                }
                                
                            ]
         });                                                          
         return  form;
    };
    //Assignment
financeForms.form_assignment_del_motion=function()
    {   
         var me=this;
         var form=new Ext.spectrumforms(
         {   
                            autowidth   : true,
                            autoHeight  : true,
                            
                            layout      : {type: 'vbox',align: 'stretch'},
                            bodyStyle   : 'padding: 5px; background-color: #DFE8F6',
                            border      : false,
                                                    
                            fieldDefaults: {
                                labelAlign: 'top',
                                labelWidth: 100,
                                labelStyle: 'font-weight:bold'
                            },      
                            items: 
                            [
                                {
                                    xtype: 'fieldset',
                                    title: 'Description',
                                    collapsible: true,
                                    defaults: 
                                    {
                                        labelWidth: 89,
                                        anchor: '100%',
                                        layout: {
                                            type: 'hbox',
                                            defaultMargins: {top: 5, right: 5, bottom: 5, left: 5}
                                        }    
                                    },
                                    items:
                                    [
                                       //Description
                                       {              
                                            xtype       : 'textarea',
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            name        : 'description',
                                            emptyText   : '',
                                            fieldLabel  : 'Description',
                                            labelWidth  : 100,
                                            flex        : 1,
                                            allowBlank  : false
                                       }
                                    ]
                                }
                                
                            ]
         });                                                          
         return  form;
    };

    
