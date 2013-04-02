if(!App.dom.definedExt('Ext.spectrumgrids')){//workaround for IE and sometimes chrome
Ext.define('Ext.spectrumgrids',
{
    extend: 'Ext.grid.Panel', 
    initComponent: function(config) 
    {
        this.callParent(arguments);
    },
    rowEditing                      :null,
    groupingFeature                 :null,
       
        
    constructor     : function(config) 
    {   
            Ext.tip.QuickTipManager.init(); 
            Ext.QuickTips.init();
            Ext.ToolTip();
            
            //added for intervals task 1445, max height for 1024 768 resolution. will do nothing if height is 
            //set to a valid number otherwise
            if(!config.height || config.height > App.MAX_GRID_HEIGHT) {config.height = App.MAX_GRID_HEIGHT;}
            
            
            if(config.renderTo!=null)Ext.getDom(config.renderTo).innerHTML='';  
            //SB: these two  params, generator and fields, may not be given
            if(!config.generator) config.generator=Math.random();
            if(config.fields)Ext.define('model_'+config.generator,{extend: 'Ext.data.Model',fields: config.fields});
			
			if(!config.store)//store may be determined by sublcass , do not overwrite if it already exists
		        config.store= Ext.create('Ext.data.Store', 
		        {   
		            model       : 'model_'+config.generator,
		            autoDestroy : true,
		            autoSync    : false,//IF  autoSync    : false in many operations true causes inconsistancy
		            autoLoad    : (config.autoLoad==null)?true:config.autoLoad,
		            remoteSort  : config.remoteSort,
		            sorters     : config.sorters,
		            pageSize    : config.pageSize,
		            groupField  : config.groupField,
		            proxy       : {
		                            type        : 'rest',
		                            url         : config.url,
		                            extraParams : config.extraParamsMore,
		                            reader      : 
		                            {
		                                type            : 'json',
		                                root            : 'root',
		                                totalProperty   : 'totalCount'
		                            }
		                         }    
		        });    
			
            config.stateful         = true;                                                     
            if(config.frame         ==null)     config.frame            = false;  
            if(config.remoteSort    ==null)     config.remoteSort       = true;
            if(config.selModel      ==null)     config.selModel         = Ext.create('Ext.selection.RowModel');
            if(config.extraParamsMore && config.extraParams)
            {            
                for(var i=0;i<config.extraParamsMore.length;i++)
                    config.extraParams[i]   =config.extraParamsMore[i];
            }
            
            //Add Search and pagination bar to existing DockedIems
            if(!config.dockedItems)
            config.dockedItems=new Array();
            if(config.topItems)
	            config.dockedItems.push(
	            {
	                dock    : 'top',
	                xtype   : 'toolbar',
	                items   : config.topItems,
	                height  : 30
	            }
	            );
	        if(config.bottomLItems || config.bottomRItems)  
	        config.dockedItems.push(  
                {
                    dock    : 'bottom',
                    xtype   : 'toolbar',
                    items   : [config.bottomLItems,'->',config.bottomRItems],
                    height  : 30
                }
            );
            
            //Remove empty bars
            
            if(typeof config.bottomLItems !='undefined' && typeof config.bottomRItems!='undefined' )
                if(config.bottomLItems.length==0 && config.bottomRItems.length==0)config.dockedItems.splice(1);
            if(typeof config.topItems !='undefined' )
                if(config.topItems.length==0 && config.searchBar==false)config.dockedItems.splice(0);
            
            
            if(config.searchBar==true)
            {

                config.dockedItems[0].items.push('->');
                config.dockedItems[0].items.push(
                {
                    //id          : "searchBar"+Math.random(),//removed id for conflicts: IT WORKED: this fixed error from timetask# 1412 
                    width       : 200,
                    fieldLabel  : '',
                    labelWidth  : 50,
                    xtype       : 'searchfield',
                    store       : config.store ,
                    emptyText   : (config.searchEmptyText==null)?'Search':config.searchEmptyText
                });    
            }        
            
            
            //Bottom Paginator
            if(config.bottomPaginator)
            {   
                var temp;
                //we need to allow a paginator to be created, EVEN IF NO BOTTOM ITEMS are given
                //so first check if it exists first, because it might not exist
                if(config.dockedItems[1])
                {
                    temp=config.dockedItems[1];
	                config.dockedItems.splice(1,1);
				}
                config.dockedItems.push(
                {                                   
                    dock        : 'bottom',
                    xtype       : 'pagingtoolbar',
                    store       : config.store,
                    displayInfo : true,
                    displayMsg  : 'Results {0} - {1} of {2}',
                    emptyMsg    : 'No topics to display'
                });                
                //temp is (usually) the bar of buttons, so put it back above paginator, if it exists
                if(temp)config.dockedItems.push(temp);    
            }
            
            if(config.ExtraDocksTexts!=null)
            {                                       
                for(var i=0;i<config.ExtraDocksTexts.length;i++)
                {
                    config.dockedItems.push(
                    {   
                        id          : config.ExtraDocksTexts[i],
                        xtype       : 'financeToolbar',
                        dock        : 'bottom',
                        Text        : config.ExtraDocksTexts[i],
                        generator   : config.generator,
                        comp_name   : config.ExtraDocksTexts[i]
                    });                    
                }         
            }
            
            
                           
            
            //Enable RowEditing
            if(config.rowEditable)
            {
                this.rowEditing= Ext.create('Ext.grid.plugin.RowEditing',{clicksToMoveEditor: 1,autoCancel: false});
                config.plugins  =[this.rowEditing];
            }
            //Enable CellEditing
            if(config.cellEditable)
            {
                var cellEditing= Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1});
                config.plugins  =[cellEditing];
            }
            //Enable Grouping
            if(config.groupable)
            {   
                if(config.with_counter==null || config.with_counter==true)     
                    this.groupingFeature = Ext.create('Ext.grid.feature.Grouping',
                    {
                        //groupHeaderTpl: '<span style="background:yellow">({rows.length})</span> {name}'// Role{[values.rows.length > 1 ? "s" : ""]})
                        groupHeaderTpl: '{name}'
                        ,startCollapsed:(config.startCollapsed==null)?false:config.startCollapsed
                    });
                else
                    this.groupingFeature = Ext.create('Ext.grid.feature.Grouping',
                    {
                        //groupHeaderTpl: '{[name=="" ? "'+config.group_default_title+'" : name]} ({rows.length})'// Role{[values.rows.length > 1 ? "s" : ""]})
                        groupHeaderTpl: '{name}'// Role{[values.rows.length > 1 ? "s" : ""]})
                        ,startCollapsed:(config.startCollapsed==null)?false:config.startCollapsed
                    });
                config.features     =[this.groupingFeature];
            }
            this.callParent(arguments); 
        }  
});}

