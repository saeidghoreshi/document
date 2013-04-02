if(!App.dom.definedExt('Ext.googlemap')){
Ext.define('Ext.googlemap',
{
    extend          :'Ext.Panel',
    alias           :'widget.mapgoogle',

    map             :null, 
    selected_lat    :null,
    selected_lng    :null,                 
    config          :null,
    form_id         :null,
    initComponent   :function(config) 
    {
        this.callParent(arguments);
    },
    constructor     : function(config) 
    {                      
        this.config=config;   
        this.callParent(arguments); 
    }, 
    afterRender : function()
    {
        this.initialize();      
        
        this.callParent(arguments);  
    },
    afterComponentLayout : function(w, h)
    {                                   
        if (typeof this.map == 'object') {
            this.map.checkResize();
        }                       
        this.callParent(arguments);             
    },             
    initialize:function()
    {       
        var me=this;
        if (GBrowserIsCompatible()) 
        {                                                          
            this.map            = new google.maps.Map(this.body.dom); 
            this.map.geocoder   = new GClientGeocoder();     
            
            
            this.map.removeControl(new GScaleControl());
            this.map.addControl(new GOverviewMapControl());
            this.map.addControl(new GLargeMapControl3D());
            //this.map.addControl(new GSmallZoomControl3D());small pce of zoom  ctrl 2 ply
            
            
            //google search bar
            this.map.enableGoogleBar();
            //Hybrid view with labels ON
            this.map.addMapType(G_HYBRID_MAP);
            var mapControl = new GHierarchicalMapTypeControl();
            mapControl.clearRelationships();
            mapControl.addRelationship(G_HYBRID_MAP, "Labels", true);
            this.map.addControl(mapControl);
            this.map.setMapType(G_HYBRID_MAP);
            //this.map.setUIToDefault();
            
            var point=new GLatLng(this.config.longitude,this.config.latitude);
            this.map_make_point(this.config.longitude,this.config.latitude,this.config.icon_char,true);
            
            //Calling this function after address locating is vital
            this.map.setCenter(point, 13);
            
            
            if(this.config.enable_click==true) GEvent.addListener(this.map, "dblclick",function(overlay,latlng){me.map_click(overlay,latlng)});                            
        }
    } ,
   
    map_click:function(overlay,latlng) //Multipurpose
    {          
    	if(latlng !=null)
        {                             
          this.selected_lat=latlng.lat();
          this.selected_lng=latlng.lng();
          
          if(this.map!=null) this.map.clearOverlays();
          this.geolocation_special(latlng.lat(),latlng.lng());
        }                                                           
    },
    fields:null,
    geolocation_special:function(lat,lng)
    {     
        var me=this;
        var point=new GLatLng(lat,lng);
        if(!this.map) return;
        this.map.geocoder.getLocations(point,function(response)
        {   
            try
            {                                        
                if(typeof response.Placemark == 'undefined'){this.map.clearOverlays();return;}
                var addr            = response.Placemark[0].address;
                var addr_splitted   = addr.split(",");
        
                //me.create_info(point,me.info_tempelate_maker("",addr_splitted[0],"",addr_splitted[1]+', '+addr_splitted[2]+', '+addr_splitted[3]));
                marker = new GMarker(point);
                me.map.addOverlay(marker);  
                //me.map.setCenter(point, 13);
                                      
                
                var selected            ={};      
                var Address             =response.Placemark[0].address;
                var splitted            =Address.split(',');
                
                selected.street        =splitted[0];
                selected.city          =splitted[1].split(' ').join('')
                selected.countryName   =splitted[3].split(' ').join('');
                                                                                       
                var region_postal      =splitted[2].split(' ');
                
                     
                selected.region        =region_postal[1];
                selected.postalcode    =((typeof (region_postal[2]) != 'undefined')?region_postal[2]:'')+' '+((typeof (region_postal[3]) != 'undefined')?region_postal[3]:'');
                                                                                                                
                              
                selected.lat=response.Placemark[0].Point.coordinates[1];
                selected.lng=response.Placemark[0].Point.coordinates[0];
                
                //Owner Function to push for itself
                me.config.owner.transferData(selected);
                
                me.selected_lat=response.Placemark[0].Point.coordinates[1];
                me.selected_lng=response.Placemark[0].Point.coordinates[0];                   
            }
            catch(error){Ext.MessageBox.alert('Google Maps Error:','Difficulty Finding Address');}
        });   
    },                                                
    /*geolocation:function(lat,lng)
    {      
        var me=this;
        var point=new GLatLng(lat,lng);
        
        this.map.geocoder.getLocations(point,function(response)
        {                                           
            if(response.Placemark == undefined){me.map.clearOverlays();return;}
            var addr= response.Placemark[0].address;
            var addr_splitted=addr.split(",");
    
            //me.create_info(point,me.info_tempelate_maker("",addr_splitted[0],"",addr_splitted[1]+', '+addr_splitted[2]+', '+addr_splitted[3]));
            me.map.setCenter(point, 15);
        });   
    }, */  
    /*points_list:function(o)      //receives Json Data and create marker for all points[lat,lng]
    {    
         var ds=[];
         ds = YAHOO.lang.JSON.parse(o.responseText);
         
         for (var i = 0, len = ds.length; i < len; i++)                   
             if(ds[i].venue_latitude!=null && ds[i].venue_longitude!=null )
                this.map_make_point(ds[i].venue_longitude,ds[i].venue_latitude,ds[i].icon_char,'');
    }, */
    map_make_point:function(lng,lat,_char,tooltip)
    {           
    	 if(lat=='null'|| lat=='' || lng=='null' || lng=='' || lat==null || lng==null ||isNaN(lat) || isNaN(lng) )
         {
             //this.initialize();
             this.find_location("Canada",3);
             return; 
         }
                                                                
         var point = new GLatLng(lat,lng);
         var marker= this.create_marker(point,_char);              
         this.map.addOverlay(marker);
         
         //var location=google.loader.ClientLocation;
         //if(location==null)
         var location={latitude:lat,longitude:lng}
         
         
         if(tooltip==true) this.find_location(lat+","+lng,13);  
    },
       
    info_tempelate_maker:function()    //[Title , Value] Pair Sequence
    {
        var str="";                                                                                                               
        for(var i=0;i<arguments.length;i+=2) str+="<div>"+arguments[i]+"</div><div>"+arguments[i+1]+"</div>";
        return str;
    },
    create_marker:function (point,icon_char) 
    {                                                 
          var baseIcon = new GIcon(G_DEFAULT_ICON);
          var letteredIcon = new GIcon(baseIcon);
          letteredIcon.image = "http://www.google.com/mapfiles/marker" + icon_char + ".png";
          var marker = new GMarker(point,{icon:letteredIcon});
          return marker;
    },
    create_info:function(point,msg){this.map.openInfoWindowHtml(point,msg);},
    find_location:function(address,zoom) 
    {
        try
        {
        	var me=this;        
            if(!this.map)return;        
            this.map.geocoder.getLocations(address,function(response){me.find_location_response(response,me,zoom);} );
        }
        catch(error){Ext.MessageBox.alert('Google Maps Error:',error);}      
    },
    
    find_location_response:function(response,me,zoom)
    {                                                      
       me.map.clearOverlays();
       if (!response || response.Status.code != 200)
       {
           var error="Sorry, we were unable to geocode that address";
		   Ext.MessageBox.alert('Google Maps Error:',error);
       }
       else 
       {
         place  = response.Placemark[0];
         point  = new GLatLng(place.Point.coordinates[1],place.Point.coordinates[0]);
         marker = new GMarker(point);
         me.map.addOverlay(marker);  
         me.map.setCenter(point, zoom);
         //me.create_info(point,place.address + '<br>' +'<b>Country code:</b> ' + place.AddressDetails.Country.CountryNameCode);
         me.selected_lat=place.Point.coordinates[1];
         me.selected_lng=place.Point.coordinates[0];
       }
    }               
});}
