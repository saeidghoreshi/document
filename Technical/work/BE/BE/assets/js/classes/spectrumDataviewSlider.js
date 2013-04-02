var dataViewSliderClass= function(moduleId,rowCount,colCount,DATA){this.construct(moduleId,rowCount,colCount,DATA);};
dataViewSliderClass.prototype=
{           
     construct:function(moduleId,rowCount,colCount,DATA)
     {
         var me=this;
     }, 
    
     sectionLimit       : 8,
     sectionsCount      : null,
     sectionLimitCounter: 0,
     groupable          : null,
     DATA               : null,
     
     initiate:function(moduleId,rowCount,colCount,DATA,dataMapping,groupname)
     {                         
         var me     =this;
         me.DATA    =DATA;
         me.sectionsCount=DATA.length/me.sectionLimit+1;
         
                        
         $(document).ready(function()
         {
                if(me.groupable==true)
                {
                    //CREATE HEADER
                    $('#'+moduleId).append("<div id="+moduleId+"-H"+'0'+"></div>");
                    $('#'+moduleId).append("<input type='hidden' id="+moduleId+"-H"+'0'+"-index value='"+'0'+"' />");
                    var header   =$('#'+moduleId+"-H"+'0');
                    var headerId =header.attr("id");
                    //IDENTIFIER
                    header.addClass(moduleId);
                    header.addClass("header");
                    header.addClass("expand");
                    header.html(groupname);    
                }        
                 
                //CREATE MAIN TABLE
                $('#'+moduleId).append("<div id="+moduleId+"-MT"+"></div>");
                var mainTable   =$('#'+moduleId+"-MT");
                var mainTableId =mainTable.attr("id");
                //IDENTIFIER
                mainTable.addClass(moduleId);
                mainTable.css("display","table");
                mainTable.css("width","100%");
                mainTable.addClass("show");
                
                //CREATE MAIN TABLE ROW
                mainTable.append("<div id="+mainTableId+"-R"+"></div>");
                var mainTableRow   =$('#'+mainTableId+"-R");
                var mainTableRowId =mainTableRow.attr("id");
                //IDENTIFIER
                mainTableRow.addClass(moduleId);
                mainTableRow.addClass(moduleId);
                mainTableRow.css("display","table-row");
                mainTableRow.css("width","100%");
                
                
                
                //DEFINE DIV SECTION
                for(sc=0;sc<me.sectionsCount;sc++)
                {
                    me.sectionLimitCounter  =0;
                    
                    //CREATE MAIN TABLE ROW CELL
                    mainTableRow.append("<div id="+mainTableRowId+"-C"+sc+"></div>");
                    var mainTableCell   =$('#'+mainTableRowId+"-C"+sc);
                    var mainTableCellId =mainTableCell.attr("id");
                    //IDENTIFIER
                    mainTableCell.addClass(moduleId);
                    mainTableCell.css("display","table-cell");
                    
                    
                    
                    //CREATE SECTION
                    mainTableCell.append("<div id="+mainTableCellId+"-S"+sc+"-T></div>");
                    var divSectionId    =mainTableCellId+"-S"+sc+"-T";
                    var divSection      =$("#"+divSectionId);
                    divSection.css("margin","5px");
                    
                    //IDENTIFIER
                    divSection.addClass(moduleId);
                    divSection.addClass("div-table");
                    divSection.css("display","table");
                    divSection.css("width","200px");      
                    
                     
                    
                    //Build Rows and Columns
                    for(var r=0;r<rowCount;r++)
                    {
                        if(me.sectionLimitCounter==me.sectionLimit)break;
                        
                        //CREATE TABLE ROW
                        divSection.append("<div id='"+divSectionId+"-R"+r+"'></div>");
                        var tableRow    =$('#'+divSectionId+'-R'+r);
                        var tableRowId  =tableRow.attr("id");
                        
                        tableRow.addClass(moduleId);
                        tableRow.addClass("div-table-row");
                                
                        tableRow.css("display","table-row")
                        tableRow.css("width","100%");      
                        
                        
                        for(var c=0;c<colCount;c++)
                        {                                                               
                            if(me.sectionLimitCounter==me.sectionLimit)break;
                            me.sectionLimitCounter++;
                                                          
                            //CREATE CELL
                            tableRow.append("<div id='"+tableRowId+"-C"+c+"'></div>")
                            var tableCell=$('#'+tableRowId+'-C'+c);
                            var tableCellId  =tableCell.attr("id");
                            
                            //Identifier
                            tableCell.addClass(moduleId);
                            tableCell.addClass("div-row-cell");
                            
                            tableCell.css("display","table-cell");
                            tableCell.css("padding","3px");
                            tableCell.css("width",100/colCount+"%");
                                
                            //BUILD INTERIOR ITEMS
                            var itemOffset  =sc*me.sectionLimit;
                            if(DATA.length>=r*colCount+(c+1) + itemOffset)
                            {
                                //CREATE ITEM 0
                                tableCell.append("<div id='"+tableCellId+"-I0'></div>");
                                var Item    =$('#'+tableCellId+'-I0');
                                var ItemId  =Item.attr("id");
                                
                                //IDENTIFIER
                                Item.addClass(moduleId);
                                Item.addClass("div-cell-item0");
                                Item.addClass("Items"); 
                                 
                                //SELECT WHICH DATASOURCE ITEM NEED TO BE BOUNDED
                                var header  ='';
                                var footer  ='';
                                if(typeof DATA[r*colCount+0 + itemOffset].header != "undefined")header="<div>"+DATA[r*colCount+c + itemOffset].header +"</div>";
                                if(typeof DATA[r*colCount+0 + itemOffset].footer != "undefined")footer="<div>"+DATA[r*colCount+c + itemOffset].footer +"</div>";
                                
                                var HTML    =header + "<img width=70 height=70 src="+DATA[r*colCount+c + itemOffset].thumbnail+" />" + footer ;
                                Item.html(HTML);
                                            
                                                                                                                                                 
                                //CREATE ITEM 1
                                tableCell.append("<input type='hidden' id='"+tableCellId+"-realId' value='"+DATA[r*colCount+c + itemOffset].id+"' />")
                                
                                //CREATE ITEM 2
                                tableCell.append("<div id='"+tableCellId+"-Mask'></div>");
                                var ItemMask    =$('#'+tableCellId+'-Mask');
                                var ItemMaskId  =ItemMask.attr("id");
                                
                                //IDENTIFIER
                                ItemMask.addClass(moduleId);
                                ItemMask.addClass("div-cell-itemMask");
                                ItemMask.addClass("ItemMask");
                                
                                
                                //CREATE ITEM 3
                                tableCell.append("<input type='hidden' id='"+tableCellId+"-index' value='"+parseInt(r*colCount+c + itemOffset)+"' />")
                                   
                                //ITEM 2 BUTTON1
                                ItemMask.append("<img id='"+tableCellId+"-I1' src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/monitor.png />");
                                $("#"+tableCellId+'-I1').click(function()
                                {
                                    var id=($(this).attr("id")).substring(0,$(this).attr("id").length-3)+"-index";
                                    var index=$("#"+id).attr("value");
                                    dataMapping[0].handler(DATA[index].conmtent_name,DATA[index].url) ;
                                });
                                
                                /*                                                                               
                                //ITEM 2 BUTTON2
                                ItemMask.append("<img id='"+tableCellId+"-I2' src=http://endeavor.servilliansolutionsinc.com/global_assets/silk/error.png />");
                                $("#"+tableCellId+"-I2").click(dataMapping[r*colCount+0 + itemOffset].handler(dataMapping[r*colCount+0 + itemOffset].content_name,dataMapping[r*colCount+0 + itemOffset].url));
                                */
                                
                                
                            }
                            
                            if(me.sectionLimitCounter%colCount==0)
                                break;                     
                        }
                     }         
                }
                 
                
                
                
                
                
                
                me.applyCss(moduleId,mainTable);
             });
     },
                                                  
     applyCss:function(moduleId,table)
     {
             //DEFINE DISABLED NAVIGATOR
             $('#'+moduleId).append("<div id='"+moduleId+"-navRight'></div>");
             var navRight=$('#'+moduleId+"-navRight")
             navRight.addClass("Msk");
             navRight.addClass("MskRight");
             
             $('#'+moduleId).append("<div id='"+moduleId+"-navLeft'></div>");
             var navLeft  =$('#'+moduleId+"-navLeft");
             navLeft.addClass("Msk");
             navLeft.addClass("MskLeft");
             
             //DEFINE NAVIGATOR ACTIONS
             //Over Navigator Object
             $('#'+moduleId+"-navLeft").mouseover(function()
             {
                navLeft.addClass("Mskhover");
                navRight.addClass("Mskhover");
             });
             $('#'+moduleId+"-navRight").mouseover(function()
             {          
                navLeft.addClass("Mskhover");
                navRight.addClass("Mskhover");
             });
             
             //Over Table Object
             $('#'+moduleId).mouseover(function()
             {
                navLeft.addClass("Mskhover");
                navRight.addClass("Mskhover");
                
                navRight.css("top"  ,$('#'+moduleId+'-MT').position().top+($('#'+moduleId+'-MT').height()/2)-20);
                navLeft.css("top"   ,$('#'+moduleId+'-MT').position().top+($('#'+moduleId+'-MT').height()/2)-20);
                
                     
                navRight.css("left" ,$('#'+moduleId).position().left+495+'px');
                navLeft.css("left"  ,$('#'+moduleId).position().left+20 +'px');
             });
             $('#'+moduleId).mouseout(function()
             {          
                navLeft.removeClass("Mskhover");
                navRight.removeClass("Mskhover");
             });
             
             
             
             $('#'+moduleId+"-navRight").click(function()
             {     
                //table.animate({ marginLeft: "100%"} , 1000);
                table.animate({"marginLeft": "-=555px"}, "slow");
             });
             $('#'+moduleId+"-navLeft").click(function()
             {     
                //$('#'+moduleId+"-T0").animate({ marginRight: "0%"} , 1000);
                table.animate({"marginLeft": "+=555px"}, "slow");
             });  
     },
     
     //PROPERTIES
     setGrouping:function(groupable,groupField)
     {
        var me      =this;
        me.groupable=groupable;
        if(me.groupable==true)
            me.groupField   =groupField;
        else                               
            me.groupField   =null;             
     },
     
     //CONTEXT MENU BUILDER
     contextMenuBuilder:function(mainContainerID,elementArray)
     {                                  
         /*
            Element Array is a a flat array which holds elment name which is correspond to className
            item '-' in element array ,eans seperator
         */
         var me=this;
         
         $(document).ready(function()
         {
                //CREATE ul
                $('body').append("<ul id='"+mainContainerID+"-ul'></ul>");
                var ul   =$('#'+mainContainerID+'-ul');
                var ulId =ul.attr("id");
                ul.addClass("contextMenu");
                
                for(var i=0;i<elementArray.length;i++)
                {
                    //CREATE li
                    ul.append("<li id='"+ulId+"-li-"+i+"'></li>");
                    var li    =$('#'+ulId+'-li-'+i);
                    var liId  =li.attr("id");
                    li.addClass(elementArray[i]);
                    
                    if(elementArray[i]=='-')
                        li.addClass("separator");
                        
                    //CREATE a
                    //NOTE : HREF:#ACTIONNAME
                    if(elementArray[i]!='-')
                        li.append("<a href='#"+elementArray[i]+"'>"+elementArray[i]+"</a>");
                }
         });  
     },
     
     buildContextMenuOverComponent:function(componentElId,contextMenuElId,className)
     {          
        var me=this;
        YAHOO.util.Event.onAvailable(componentElId,function()
        {       
                $(document).ready(function()          
                {            
                        me.contextMenuBuilder(contextMenuElId,['Edit','-','Copy','-','Paste']);
                        //ADDING CONTEXT MENU OVER ELEMENTS W/ BASED ON CLASS NAME 
                        YAHOO.util.Event.onAvailable(contextMenuElId+'-ul',function()
                        {
                            $('.'+className).each(function(index) 
                            {          
                                    var itemMe=this;
                                    $(this).contextMenu
                                    (
                                        {
                                            menu: contextMenuElId+'-ul'
                                        },
                                        function(action, el, pos) 
                                        {
                                            alert(
                                                "Action:"       + action                + "\n\n" +
                                                "Element ID: "  + $(itemMe).attr("id")+ "\n\n" +
                                                "X: "           + pos.x                 + "  Y: " + pos.y   + " (relative to element)\n\n" +
                                                "X: "           + pos.docX              + "  Y: " + pos.docY+ " (relative to document)"
                                                );
                                        }
                                    );
                            });
                        });
                });    
        });                                      
     }
}


            
