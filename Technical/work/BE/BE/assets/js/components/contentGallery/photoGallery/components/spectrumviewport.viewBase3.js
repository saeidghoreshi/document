Ext.define('Ext.spectrumviewport.viewBase3',    
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
            config.bodyStyle='background:transparent; padding:10px;border:0px';
            config.items    =
            [             
                 {
                     id         : (config.generator.toString()+'-northPanel'),
                     region     : 'north',
                     //collapsible: true,  
                     //split      : true,  
                     height     : "0%",
                     items      : 
                     [
                         
                     ]
                 },                        
                 {
                      region     : 'center',
                      layout     :'border',
                      height     : "100%", 
                      items      : 
                      [
                          {
                                  id      : (config.generator.toString()+'-leftPanel'),
                                  region  : 'center',
                                  width   : "60%",   
                                  height  : 400,
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
                                  width      : '40%', 
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
