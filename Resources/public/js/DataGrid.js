(function($) {
     window.Thrace = window.Thrace || {};
     window.Thrace.DataGrid = window.Thrace.DataGrid || {};
     
     window.Thrace.DataGrid = Backbone.View.extend({
        configs: null,
        grid: null,
        pager: null,
        navGrid: null,
        render: function() {   

            if(this.$el.length === 0){
                return;
            }
            
            this.initConfigs();
            
            this.$el.append('<table id="'+ this.configs.datagrid_id +'"></table>');
            this.$el.append('<div id="'+ this.configs.pager +'"></div>');
            
            this.initGrid(this.configs);
            this.initNavGrid(this.configs);
            
            if(this.configs.enableFilterToolbar){
                this.initToolbar(this.configs);
            }    
        },
        initConfigs: function(){
            var dataConfigs = (this.$el.data('configs') === undefined) ? {} : this.$el.data('configs');
            
            dataConfigs.datagrid_id = "jggrid-"+ this.$el.attr('id');
            dataConfigs.pager =  "pager-"+ this.$el.attr('id');
            dataConfigs.toolbar =  "toolbar-"+ this.$el.attr('id');
            dataConfigs.uid =  "jggrid-"+ this.$el.attr('id') + $('meta[name="current_url_hash"]').attr('content');
            var postdata = $.cookie(dataConfigs.uid) != undefined ? JSON.parse($.cookie(dataConfigs.uid)) : {};

            var defaultConfigs =  {
                postData: postdata,
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
                    sopt: ['cn', 'ne', 'lt', 'le', 'gt', 'ge', 'bw', 'bn', 'ew', 'en', 'eq', 'nc'], 
                    multipleSearch:true, 
                    multipleGroup:false
                },
                addParams: {},
                editParams: {},
                deleteParams: {},
                customButtons: {},
                enableFilterToolbar: false,
                filterToolbarParams: {},
                beforeRequest: function() {

                    var grid = $('#' + dataConfigs.datagrid_id);
                    var postdata = grid.jqGrid('getGridParam', 'postData');

                    if(postdata.filters != undefined ){
                        postdata._search = true;
                    }

                    $.cookie(dataConfigs.uid, JSON.stringify(postdata));

                    return [true,''];
                }
            };

            this.configs = _.extend(defaultConfigs, dataConfigs);

            this.setConfigs(this.configs, this);

            var alertMessage = $('meta[name="datagrid_filter_msg"]').attr('content');

            if(postdata.filters != undefined && postdata.filters.length > 2 && alertMessage != 'undefined'){
                $(alertMessage).insertBefore('#' + this.$el.attr('id'));

            }

            if(postdata.sidx != undefined){
                this.configs.sortname = postdata.sidx;
            }

            if(postdata.sord != undefined){
                this.configs.sortorder = postdata.sord;
            }

            if(postdata.page != undefined){
                this.configs.page = parseInt(postdata.page);
            }

            if(postdata.page != undefined){
                this.configs.rowNum = parseInt(postdata.rows);
            }
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
        initToolbar: function(configs){
            configs.filterToolbarParams.stringResult = true;
            this.grid.jqGrid('filterToolbar', configs.filterToolbarParams);
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
                    }
                };

                navGrid.navButtonAdd(pager, _.extend(defaultConfigs, configs)); 
            });
        },
        destroy: function(){
            this.$el.empty();
        }
    });
})(jQuery);
