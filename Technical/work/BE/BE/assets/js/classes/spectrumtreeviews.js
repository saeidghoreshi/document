if(!App.dom.definedExt('Ext.spectrumtreeviews')){//workaround for IE and sometimes chrome
Ext.define('Ext.spectrumtreeviews',
{
        extend: 'Ext.tree.Panel', 
        initComponent: function(config) 
        {
            this.callParent(arguments);
        },
        state   :null,
        constructor     : function(config) 
        {   
            Ext.QuickTips.init();
            //added for intervals task 1445, max height for 1024 768 resolution. will do nothing if height is 
            //set to a valid number otherwise
            if(!config.height || config.height > App.MAX_GRID_HEIGHT) {config.height = App.MAX_GRID_HEIGHT;}
            
            var me=this;
            if(config.renderTo!=null)Ext.getDom(config.renderTo).innerHTML='';  
            Ext.define('model_'+config.generator,{extend: 'Ext.data.Model',fields: config.fields});
            
            config.width        = "100%";
            config.useArrows    = true;
            config.rootVisible  = false;
            config.multiSelect  = false;
            config.singleExpand = false;   
            config.autoSync     = true;
            
            config.floatable    = false;
            config.store        = Ext.create('Ext.data.TreeStore', 
            {
                model       : 'model_'+config.generator,
                proxy       : 
                {
                    type    : 'rest',
                    url     : config.url,
                    reader  : 
                            {
                                    type            : 'json',
                                    root            : ''
                            }
                },
                folderSort: true
            });
            
            
            if(config.viewConfig==null)
            config.viewConfig   = 
            {
                plugins: {
                    ptype: 'treeviewdragdrop'
                }
                ,listeners: 
                {
                    beforedrop: function(node, data, dropRec, dropPosition) 
                    {   
                        /*
                        *  config.relations_array is a 2D array col(0)=parent_guid_id and col(1)=guid_id 
                        */
                        var dragData=me.getSelectionModel().getSelection()[0].data;
                        
                        if(dragData.title=='')return false;     //Not able to drag fake record
                        if(dropRec.data.title=='')return false; //Not able to drop (under-after-before) fake record
                        
                                           
                        //Apply changes
                        if(dropPosition=='append')
                        {                                               
                            var index=me.indexOf_2D(config.relations_array,dragData.guid_id);
                            config.relations_array[index][0]=dropRec.data.guid_id;
                        }
                        else
                        {
                            //(1) Appending
                            //try
                            //{
                                var drop_index=me.indexOf_2D(config.relations_array,dropRec.data.guid_id)
                                
                                var edParent=config.relations_array[drop_index][0];
                                
                                var drag_index=me.indexOf_2D(config.relations_array,dragData.guid_id);
                                config.relations_array[drag_index][0]=edParent;
                                //(2) Ordering     
                                var newRow=config.relations_array.splice(drag_index,1);
                                config.relations_array=me.inject_in_array(config.relations_array,newRow,drop_index);                                
                            //}
                            //catch(e){}
                        }   
                                                                            
                        
                        return true;
                    }
                    ,drop: function(node, data, dropRec, dropPosition) 
                    {   
                        me.getStore().sort();
                        //me.getView().getStore().data.items;
                    }
                }
            };
            config.dockedItems =
            [
                {
                    dock: 'top',
                    xtype: 'toolbar',
                    items:config.topItems
                }
                ,{
                    dock: 'bottom',
                    xtype: 'toolbar',
                    items:[config.bottomLItems,'->',config.bottomRItems]
                }
            ]
            if(config.searchBar)
            {
                config.dockedItems[0].items.push('->');
                config.dockedItems[0].items.push(
                        {
                                width       : 200,
                                fieldLabel  : '',
                                labelWidth  : 50,
                                xtype       : 'searchfield',
                                store       : config.store ,
                                emptyText   : 'Search'
                        });    
            }        
             
            this.callParent(arguments); 
        },
        indexOf_2D:function(a,value)
        {                  
            for(var i=0;i<a.length;i++)
                if(a[i][1]==value)
                    return i;
        },
        inject_in_array:function(a,newRow,index)
        {
            var left_side   =a.splice(0,index);
            var right_side  =a.splice(0,a.length);
            var result=[];
            return result.concat(left_side,newRow,right_side);
        }                       
});}

