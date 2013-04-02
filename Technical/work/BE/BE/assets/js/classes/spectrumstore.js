var simpleStoreClass= function(){this.construct();};
simpleStoreClass.prototype=
{
     construct:function(){}
     ,make:function(_fields,_url,_extraParams)
     {
        var gen=Math.random();
        Ext.define('model_'+gen,{extend: 'Ext.data.Model',fields: _fields});
        var store= Ext.create('Ext.data.Store', 
        {   
                model       : 'model_'+gen,
                autoDestroy : true,
                autoLoad    : true,
                autoSync    : false,
                proxy       : {
                                type        : 'rest',
                                url         : _url,
                                extraParams : _extraParams,
                                reader      : 
                                {
                                    type            : 'json',
                                    root            : 'root'
                                }
                              }    
        });               
        return store;
     }
     
}           
