var facilitiesFormsClass= function(){this.construct();};
facilitiesFormsClass.prototype=
{
    construct:function()
    {
                             
    },       
    //Venue Form
    formVenueDetails:function()
    {   
        
         var me=this;
         
         var form=new Ext.form.Panel(
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
                                            xtype       : 'textfield',
                                            name        : 'venue_name',
                                            fieldLabel  : 'Venue Name',
                                            labelWidth  : 150,
                                            autoWidth   : true,
                                            allowBlank  : false
                                },
                                {
                                            xtype       : 'fieldcontainer',
                                            fieldLabel  : '',
                                            combineErrors: true,
                                            msgTarget   : 'side',          
                                            labelStyle  : 'font-weight:bold;padding:0',
                                            layout      : 'hbox',        
                                            fieldDefaults: {labelAlign: 'top'},
                                            items       :
                                            [
                                                {                        
                                                    xtype           : 'combo',
                                                    name            : 'venue_type_id',
                                                    width           : 100,
                                                    queryMode       : 'remote',
                                                    forceSelection  : true,
                                                    editable        : false,
                                                    fieldLabel      : 'Venue Type',
                                                    labelWidth      : 220,
                                                    labelStyle      : 'font-weight:bold',   
                                                    displayField    : 'lu_descr',
                                                    valueField      : 'venue_type',
                                                    allowBlank      : false,
                                                    typeAhead       : true,
                                                    store           : Ext.create('Ext.data.Store', 
                                                    {
                                                        fields      : ['venue_type', 'lu_descr'],
                                                        data        : [
                                                                            {venue_type:'1',lu_descr:'Diamond'}
                                                                            ,{venue_type:'2',lu_descr:'Field'}  
                                                                            ,{venue_type:'3',lu_descr:'Indoor'} 
                                                                      ]
                                                    }),
                                                    grow        :true
                                                }
                                            ]
                                       }
                            ]
         });                                                          
         return  form;
    },
    //Facility Form
    formFacilityDetails:function(search_handler,countryStore,regionStore)
    {   
        
         var me=this;
         
         var form=new Ext.form.Panel(
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
                               
                               //General
                               {                                                                   
                                            xtype       : 'textfield',
                                            name        : 'facility_name',
                                            fieldLabel  : 'Facility Name',
                                            labelWidth  : 150,
                                            autoWidth   : true,
                                            allowBlank  : false
                               },
                               //Addressing
                               {
                                            xtype           : 'fieldcontainer',
                                            fieldLabel      : '',
                                            combineErrors   : true,
                                            msgTarget       : 'side',          
                                            labelStyle      : 'font-weight:bold;padding:0',
                                            layout          : 'hbox',        
                                            fieldDefaults   : {labelAlign: 'top'},
                                            items           :
                                            [
                                                {
                                                    xtype       : 'combo',
                                                    name        : 'country_abbr',
                                                    id          : 'country_abbr',
                                                    fieldLabel  : 'Country',
                                                    labelWidth  : 70,
                                                    labelStyle  : 'font-weight:bold',   
                                                    allowBlank  : false,
                                                    mode        : 'local',
                                                    forceSelection: false,
                                                    editable    : false,
                                                    displayField: 'country_name',
                                                    valueField  : 'country_id',
                                                    queryMode   : 'local',
                                                    store       : countryStore,
                                                    flex        :1,
                                                    listeners   :
                                                    {
                                                        change:function( _this, newValue, oldValue)
                                                        {                 
                                                            regionStore.load({params:{country_id:newValue}}) ;
                                                        }
                                                    }
                                               },
                                               {
                                                    xtype       : 'combo',
                                                    name        : 'region_abbr',
                                                    id          : 'region_abbr',
                                                    fieldLabel  : 'State/Province',
                                                    labelWidth  : 70,
                                                    labelStyle  : 'font-weight:bold',   
                                                    width       : 120,
                                                    allowBlank  : false,
                                                    mode        : 'local',
                                                    forceSelection: false,
                                                    editable    : false,
                                                    displayField: 'region_name',
                                                    valueField  : 'region_id',
                                                    queryMode   : 'local',
                                                    store       : regionStore,
                                                    margins     :'0 0 0 5'
                                               },
                                               {
                                                    xtype       : 'textfield',
                                                    name        : 'address_city',
                                                    id          : 'address_city',
                                                    fieldLabel  : 'City',
                                                    labelWidth  : 70,
                                                    labelStyle  : 'font-weight:bold',   
                                                    flex        : 1,
                                                    allowBlank  : false,
                                                    margins     : '0 0 0 5'
                                               }                                       
                                            ]
                                       },
                               {
                                            xtype           : 'fieldcontainer',
                                            fieldLabel      : '',
                                            combineErrors   : true,
                                            msgTarget       : 'side',          
                                            labelStyle      : 'font-weight:bold;padding:0',
                                            layout          : 'hbox',        
                                            fieldDefaults   : {labelAlign: 'top'},
                                            items           :
                                            [
                                                {
                                                    xtype       : 'textfield',
                                                    name        : 'address_street',
                                                    id          : 'address_street',
                                                    fieldLabel  : 'Street',
                                                    labelStyle  : 'font-weight:bold',   
                                                    labelWidth  : 70,
                                                    width       : 200,
                                                    allowBlank  : false
                                                },
                                                {
                                                    xtype       : 'textfield',
                                                    name        : 'postal_value',
                                                    id          : 'postal_value',
                                                    fieldLabel  : 'Postal Code',
                                                    labelWidth  : 70,
                                                    labelStyle  : 'font-weight:bold',   
                                                    flex        :1,
                                                    allowBlank  : true,
                                                    margins     :'0 0 0 5'
                                                }                                        
                                            ]
                                       },
                               //Find Adderss On GMAP
                               {
                                    xtype   :"button",
                                    text    :"Find Address above",
                                    iconCls :'magnifier',
                                    pressed :true,
                                    width   :100,
                                    tooltip :'Serach at th Map',
                                    handler :search_handler
                                }      
                            ]
         });                                                          
         return  form;
    }
}
var facilitiesForms=new facilitiesFormsClass();
