var dataViewSliderPhotoGalleryClass= function(config){this.construct(config);};
dataViewSliderPhotoGalleryClass.prototype=
{                       
    dataViewSlider  :null,  
    moduleId        :null,
    selectionMode   :null,
    selectedIds     :[],
    
    baseViewPort    :null,
    owner           :null,
    
    //all params need to be in config
    config          :null,
    
    datasource             :
    {
        url             :null,
        successFunction :null,   
        proxy           :null
    },
    
    
    /*TEMPATE OBJECT NEEDEED TO BE PROVIDED BY CALLER*/
    /*itemConfig      :
    {
        id          :null,
        thumbnail   :null,
        largeImage  :null,
        header      :null,
        footer      :null
    },
    */
    
    construct       :function(config)
    {
        var me          =this;
        me.moduleId     =config.moduleId;    
        me.baseViewPort =config.baseViewPort;
        me.owner        =config.owner;
        
        me.config       =config;
    },
    init:function(url)
    {   
        var me=this;      
        
        me.datasource.url=url;
        me.datasource.successFunction=function(data) 
        {                                                
            Ext.getDom(me.moduleId).innerHTML='';
            
            var resultTotal =$.parseJSON( data ) ;
            var result      =resultTotal.result;
            var grupName    =resultTotal.groupname;
            
            me.dataViewSlider=new dataViewSliderClass(); 
            me.dataViewSlider.setGrouping(true,null);
            
            
            var dataMapping=[];
            
            for(var i=0;i<result.length;i++)
            {                                                                                   
                dataMapping.push(
                {                                                                                    
                    handler     :function(content_name,url){photoGalleryManage.showPreview(content_name,url);}
                });
            }                                                                                                
            me.dataViewSlider.initiate(me.moduleId,6,4,result,dataMapping,grupName);
           
            
            
            /*me.applyFilter(5    ,"id"   , "hide");
            me.applyFilter(12   ,"id"   , "hide");
            me.applyFilter(1    ,"id"   , "hide");
            
            setTimeout(function()
            {
                me.applyFilter(5    ,"id"   , "show");
                me.applyFilter(12   ,"id"   , "show");
            },1000)
            */
                              
            /*
            $( "#CONTAINER-"+me.moduleId).css("width",500);    
            $( "#"+me.moduleId ).animate({"marginLeft": "+=500px"}, "slow");
            */
            
            //SETTING PROPERTIES
            me.setSelctionMode("single"); 
            me.setItemSelectionEffect("Explode");
            
            me.assignEventAndStyle();
            
            //ADDING FEATURE TO COMPONENT
            //CONTET MENU DISABLED
            //me.dataViewSlider.buildContextMenuOverComponent(me.moduleId,me.moduleId+'contextMenu',"Items");
        }
        me.datasource.proxy={};
        
        me.load();
    },
    assignEventAndStyle:function()
    {
        var me=this;
        $(document).ready(function()          
        {  
            //CELL SELECTION
            $('.div-row-cell').each(function(index) 
            {   
                //ITERATE ONLY THROUGH THIS MODULE'S CLASSE
                if($(this).hasClass(me.moduleId)==false) return;
                
                var cellId=$(this).attr('id');
                
                //LEFT CLICK EVENT
                $(this).click(function()
                {         
                    //BLINK TWICE TWICE
                     for(j=0;j<2;j++) 
                        $(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
                        
                        
                     var hadBeenSelected=$('#'+cellId+'-I0').hasClass("Item-selected");
                     me.adjustSelection();
                     
                     if(hadBeenSelected)
                       $('#'+cellId+'-I0').removeClass("Item-selected");
                     else
                       $('#'+cellId+'-I0').addClass("Item-selected");    
                       
                     
                     
                     /*$('#'+itemId).animate(
                     {
                        opacity : 0.25, 
                        specialEasing: 
                        {
                          width: 'linear',
                          height: 'easeOutBounce'
                        }
                     }, 1000, function(){});
                     */
                     
                     
                     //PRINT MY SELECTION
                     me.getSelection();
                     
                     var recentlySelectedItem=me.getRecordByHtmlId(cellId);
                     if( typeof(recentlySelectedItem) == 'undefined')return;
                     var itemRealId=recentlySelectedItem.itemRealId;
                     
                     //CALL OWNER EVENT FOR HANDLING BUTTONS
                     me.config.owner.handleButtons(recentlySelectedItem.data);
                         
                     
                     //ACTION
                     var parentRightId   =me.baseViewPort.getSectionsId("east");
                     var rPanelEl        =Ext.get(parentRightId);
                        
                     var post={}
                     post["content_id"]  =itemRealId;
                    
                     Ext.Ajax.request(
                     {
                         url     : 'index.php/contentgallery/json_get_content_actions_history/TOKEN:'+App.TOKEN,
                         params  : post,
                         success : function(response)
                         {                                                             
                                var result=YAHOO.lang.JSON.parse(response.responseText);
                                    
                                var history='<div style="height:380px;width:500px;overflow:scroll">';
                                for(var i=0;i<result.length;i++)
                                    history+='<div style="border-bottom:1px red dashed;font-size:11px;padding:3px;width:100%;">'+'<b>'+result[i].content_action_name+'</b> [ '+result[i].content_status_name +' ]<br/> By : '+ result[i].person_fname+', '+result[i].person_lname+' ('+result[i].org_name+') <br/>'+result[i].datetime+'</div><br/>';
                                history+='</div>';
                                                                
                                Ext.getCmp('itemDetails').setValue(history);
                                
                                rPanelEl.animate(
                                {
                                    to: 
                                    {
                                        opacity: 0.5
                                    }
                                });
                                                
                                rPanelEl.hide().show({duration: 300}).frame();
                         }
                     });    
                });
            });   
            
            //HEADER COLLAPSE/EXPAND
            $('.header').each(function(index) 
            {   
                //ITERATE ONLY THROUGH THIS MODULE'S CLASSE
                if($(this).hasClass(me.moduleId)==false) return;
                
                
                var headerId=$(this).attr('id');
                
                $(this).click(function()
                {              
                
                    var groupIndex=$("#"+headerId+'-index').attr("value");
                    var tbl=$('#'+me.moduleId+'-MT'/*+groupIndex*/);
                    if(tbl.hasClass('show'))
                    {                                    
                        tbl.slideUp("slow",function(){});
                        tbl.removeClass("show");
                        tbl.addClass("hide");
                                                
                        $(this).removeClass("expand");
                        $(this).addClass("collapse");    
                    }
                    else
                    {
                        tbl.slideDown("slow",function(){});
                        tbl.addClass("show");
                        tbl.removeClass("hide");
                            
                        $(this).removeClass("collapse");
                        $(this).addClass("expand");
                    }
                });
            });   
            
            //CELL Hover
            $('.div-row-cell').each(function(index) 
            {   
                //ITERATE ONLY THROUGH THIS MODULE'S CLASSE
                
                if($(this).hasClass(me.moduleId)==false) return;
                var item2=$(this).attr('id')+'-Mask';
                
                
                $(this).mouseover(function()
                {          
                    $('#'+item2).css("display","block");
                });
                $(this).mouseout(function()
                {          
                    $('#'+item2).css("display","none");
                });
                
            });   
            
        });    
    },
    
    setSelctionMode:function(selectionMode)
    {
        var me=this;
        me.selectionMode=selectionMode;
    },
    getSelection:function()
    {
        var me=this;
        
        me.selectedIds=[];
        
        $('.Item-selected').each(function(index) 
        { 
            //ITERATE ONLY THROUGH THIS MODULE'S CLASSE
            if($(this).hasClass(me.moduleId)==false) return;
                     
            var indexId     =($(this).attr("id")).substring(0,$(this).attr("id").length-3)+"-index";
            var index       =$('#'+indexId).attr("value");
            
            var cellId      =($(this).attr("id")).substring(0,$(this).attr("id").length-3)+"-realId";
            var cellRealId  =$("#"+cellId).attr('value');
                             
            
            me.selectedIds.push(
            {
                "itemId"    :cellId,
                "itemRealId":cellRealId, 
                "index"     :index,
                "data"      :me.dataViewSlider.DATA[index]
            });
        });       
    },
    getRecordByRealId:function(itemRealId)
    {
        var me=this;
        
        me.getSelection();
        for(var i=0;i<me.selectedIds.length;i++)
            if(me.selectedIds[i].itemRealId==itemRealId)
                return me.selectedIds[i];
    },
    getRecordByHtmlId:function(itemHtmlId)
    {
        var me=this;
        
                                                    
        me.getSelection();
        for(var i=0;i<me.selectedIds.length;i++)
            if(me.selectedIds[i].itemId==itemHtmlId+'-realId')
                return me.selectedIds[i];
    },
    adjustSelection:function()
    {
        var me=this;
        if(me.selectionMode=='single')
        {
           $('.div-cell-item0').each(function(index) 
           {             
               var itemId=$(this).attr('id');
               $('#'+itemId).removeClass("Item-selected");
           });
           
        }                   
    },
    setSelection:function()
    {
        
    },
    setItemSelectionEffect:function(effectName)
    {
        var me=this;
        me.itemSelectionEffect=effectName;
    },
    setRowStyle:function()
    {
        
    },
    load:function(proxy)
    {
        var me=this;
        
        $(document).ready(function()
        {
            $.ajax(
            {
                type    : 'POST',
                url     : me.datasource.url,
                success : me.datasource.successFunction,
                data    : proxy 
            });            
        });             
    },
    applyFilter:function(value,field,mode)
    {
        var me=this;
        if(field=="id")
        $(document).ready(function()
        {                                                  
            $('.div-row-cell').each(function(index) 
            {             
                var cellId=$(this).attr('id');
                
                
                if($('#'+cellId+'-I0-realId').attr("value") == value)
                {
                    if(mode=='hide')
                        $(this).hide("slow");
                    else
                        $(this).show("slow");
                }
                    
            });   
        }); 
    },
    getRowId    :function(tnumrowIndex)
    {
        var me=this;
        return me.moduleId+'-T'+tnum+'-R'+rowIndex;
    },
    getCellId   :function(tnum,rowIndex,cellIndex)
    {
        var me=this;
        return me.moduleId+'-T'+tnum+'-R'+rowIndex+'-C'+cellIndex;
    },
    getItemId   :function(tnum,rowIndex,cellIndex,itemIndex)
    {
        var me=this;
        return me.moduleId+'-T'+tnum+'-R'+rowIndex+'-C'+cellIndex+'-I'+itemIndex;
    },
    getRealItemId:function(rowIndex,cellIndex,itemIndex)
    {
        var me=this;
        return me.getItemId(rowIndex,cellIndex,itemIndex)+'-realId';
    },
    getRealItemIdByItemId:function(itemId)
    {
        var me=this;
        return itemId+'-realId';
    }
    
}            
