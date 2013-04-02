Ext.define('Ext.spectrumviewport.viewBase1',    
{
        extend: 'Ext.Panel', 
        initComponent: function(config) 
        {       
            this.callParent(arguments);
        }
        
        ,config                 :null
        ,constructor : function(config) 
        {                                                                      
            var me=this;
            
            config.layout   ='border';
            config.bodyStyle='background:#f2f2f2; padding:10px;border:0px';
            config.items    =
            [             
                 {
                     id         : (config.generator.toString()+'-northPanel'),
                     region     : 'north',
                     //collapsible: true,  
                     //split      : true,  
                     height     : "10%",
                     items      : 
                     [
                         
                     ]
                 },                        
                 {
                      region     : 'center',
                      layout     :'border',
                      height     : "90%", 
                      items      : 
                      [
                          {
                                  id      : (config.generator.toString()+'-leftPanel'),
                                  region  : 'center',
                                  width   : "65%",   
                                  items   : 
                                  [
                                      
                                  ]
                             },
                             {
                                  id         : (config.generator.toString()+'-rightPanel'),  
                                  region     : 'east',
                                  collapsible: false, 
                                  split      : true, 
                                  title      : '',
                                  width      : '35%', 
                                  items: 
                                  [      
                                  ]
                              }       
                      ]
                 }
            ];
            
            
            this.config=config;
            this.callParent(arguments);                                                           
        },
        getSectionsId:function(sectionName)
        {
            var me=this;
            
            if(sectionName=="north")
                return me.config.generator.toString()+'-northPanel';  
            if(sectionName=="east")
                return me.config.generator.toString()+'-rightPanel';  
            if(sectionName=="west")
                return me.config.generator.toString()+'-leftPanel';  
        },
        
        afterRender: function() 
        {  
            var me=this;
            this.callParent(arguments);         
        }                                                           
});                                           
