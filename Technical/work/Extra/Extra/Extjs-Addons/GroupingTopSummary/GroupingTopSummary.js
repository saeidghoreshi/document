/**
 * @author Nicolas BUI nicolas.bui@gmail.com
 */
Ext.define('Ext.ux.grid.feature.GroupingTopSummary', {
  extend:'Ext.grid.feature.GroupingSummary',
  alias:'feature.groupingtopsummary',

  // false to disactivate table summary
  showTableSummary:true,

  /**
   * provide feature teplate
   * @param values
   * @param parent
   * @param x
   */
  getFeatureTpl:function (values, parent) {
    var me = this;
    return [
      '<tpl if="typeof rows !== \'undefined\'">',
      // group row tpl
        '<tr class="' + Ext.baseCSSPrefix + 'grid-group-hd ' + (me.startCollapsed ? me.hdCollapsedCls : '') + ' {hdCollapsedCls}">',
        // column to display group name + collapse/expand tools
          '<td class="' + Ext.baseCSSPrefix + 'grid-cell ' + Ext.baseCSSPrefix + 'grid-cell-first">',
            '<div class="' + Ext.baseCSSPrefix + 'grid-cell-inner">',
              '<div class="' + Ext.baseCSSPrefix + 'grid-group-title">{collapsed}',
                me.groupHeaderTpl,
              '</div>',
            '</div>',
          '</td>',
          // append
          '{[this.printSummaryRow(xindex)]}',
        '</tr>',
        // this is the rowbody
        '<tr id="{viewId}-gp-{name}" class="' + Ext.baseCSSPrefix + 'grid-group-body ' + (me.startCollapsed ? me.collapsedCls : '') + ' {collapsedCls}">',
          '<td colspan="' + parent.columns.length + '">{[this.recurse(values)]}</td>',
        '</tr>',
      '</tpl>'
    ].join('');
  },

  /**
   * override default method to
   * @param index
   */
  getPrintData:function (index) {
    var me = this,
      columns = me.view.headerCt.getColumnsForTpl(),
      i = 0,
      length = columns.length,
      data = me.callParent(arguments),
      name = me.summaryGroups[index - 1].name,
      active = me.summaryData[name];
    for (; i < length; i++) {
      data[i].value = active[data[i].columnId];
    }
    return data;
  },

  /**
   * print a summary row data for a column
   * @param index
   */
  printSummaryRow:function (index) {
    var me = this;
	// template
	var inner = [
      '<tpl for="columns">',
        '<tpl if="xindex &gt; 1">',
          '<td class="{cls} ' + Ext.baseCSSPrefix + 'grid-cell ' + Ext.baseCSSPrefix + 'grid-cell-{columnId} {{id}-modified} {{id}-tdCls} {[this.firstOrLastCls(xindex, xcount)]}" {{id}-tdAttr}>',
            '<div unselectable="on" class="' + Ext.baseCSSPrefix + 'grid-cell-inner ' + Ext.baseCSSPrefix + 'unselectable ' + me.totalSummaryColumnCls + '" style="{{id}-style}; text-align: {align};">',
              '<div class="' + Ext.baseCSSPrefix + 'grid-group-title-content">',
                '{gridSummaryValue}',
              '</div>',
            '</div>',
          '</td>',
        '</tpl>',
      '</tpl>'
    ].join('')
    // prepare data
    var data = me.getPrintData(index);
    // build data
	inner = Ext.create('Ext.XTemplate', inner, {
      firstOrLastCls:Ext.view.TableChunker.firstOrLastCls
    });
    return inner.applyTemplate({
      columns:data
    });
  }
});