Ext.define('Ext.spectrumviewport.viewBase2',    
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
                     height     : "15%",
                     items      : 
                     [
                         
                     ]
                 },                        
                 {
                      region     : 'center',
                      layout     :'border',
                      height     : "85%", 
                      items      : 
                      [
                          {
                                  id      : (config.generator.toString()+'-leftPanel'),
                                  region  : 'center',
                                  width   : "100%",   
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
                                  width      : '100%', 
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
