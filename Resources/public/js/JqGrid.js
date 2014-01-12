define([
    'jquery', 
    'underscore', 
    'backbone', 
], function($, _, Backbone) {

    return Backbone.View.extend({
        defaultConfigs: {
            datatype: 'json',
            viewrecords: true,
            sortorder: 'asc', 
            autowidth: true,
            gridview: true,
            rowList: [10,20,30,50],
            rowNum: 10,
            pgbuttons:true,
            forceFit: true,
            shrinkToFit: true,
            scroll: false,
            enableSearchBtn: false,
            enableAddBtn: false,
            enableEditBtn: false,
            enableDeleteBtn: false,
            viewParams: {},
            searchParams: {
                sopt: ['eq', 'ne', 'lt', 'le', 'gt', 'ge', 'bw', 'bn', 'ew', 'en', 'cn', 'nc'], 
                multipleSearch:true, 
                multipleGroup:false
            },
            addParams: {},
            editParams: {},
            deleteParams: {},
            customButtons: {},
        },
        configs: {},
        datagrid: null,
        pager: null,
        navGrid: null,
        setConfigs: null,
        render: function() {   
            this.initConfigs();
            
            this.$el.append('<table id="'+ this.configs.datagrid_id +'"></table>');
            this.$el.append('<div id="'+ this.configs.pager +'"></div>');

            this.initGrid(this.configs);
            this.initNavGrid(this.configs);
        },
        initConfigs: function(){
            var dataConfigs = this.$el.data('configs');
            dataConfigs.datagrid_id = "jggrid-"+ this.$el.attr('id');
            dataConfigs.pager =  "pager-"+ this.$el.attr('id');
            
            this.configs = _.extend(this.defaultConfigs, dataConfigs);
            this.setConfigs(this.configs);
        },
        initGrid: function(configs){
            this.grid = $('#' + configs.datagrid_id);
            this.pager = $('#' + configs.pager);
            this.grid.jqGrid(configs);
        },
        initNavGrid: function(configs){
            this.navGrid = this.grid.jqGrid('navGrid','#' + configs.pager,
                {
                    search:configs.enableSearchBtn, 
                    edit:configs.enableEditBtn, 
                    add:configs.enableAddBtn, 
                    del:configs.enableDeleteBtn
                },
                configs.editParams,
                configs.addParams,
                configs.deleteParams,
                configs.searchParams,
                configs.viewParams
            );
    
            this.customButtons(this.grid, this.navGrid,  this.pager, configs.customButtons);
            
        },
        customButtons: function(grid, navGrid, pager, customButtons){
            _.each(customButtons, function(configs){
                var defaultConfigs = {
                    icon: 'ui-icon-document',
                    position: 'last',
                    onClickButton: function(e){ 
                        var id = grid.jqGrid('getGridParam','selrow');
                        if(id !== null){ 
                            var selectedRow = _.extend({'id': id}, grid.getRowData(id)); 
                            var url = _.isFunction(configs.url) ? configs.url(selectedRow) : configs.url;            
                            window.location = decodeURIComponent(url);
                        } else {
                            window.alert('Please select row.');
                        }
                    }, 
                };

                navGrid.navButtonAdd(pager, _.extend(defaultConfigs, configs)); 
            });
        },
        destroy: function(){
            this.$el.empty();
        },
    });
});