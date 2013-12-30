/**
* Initialization of jqgrid
* 
* 
* @author Nikolay Georgiev
* @version 1.0
*/
jQuery(document).ready(function(){  
    jQuery('.thrace-datagrid').each(function(){
        /** getting grid id */
        var datagridId = jQuery(this).attr('id');
        
        /** getting grid name */
        var gridName = datagridId.replace('thrace-datagrid-', '');
        
        /** Getting options of a grid by id */
        var options = jQuery(this).data('options'); 
        
        var evaluateFn = function(options){
            jQuery.each(options, function(k,v){
                if(typeof(v) == 'string' && isNaN(v)){
                    if(v.match('^function')){
                        eval('options.'+ k +' = ' + v);
                    }
                } else if(jQuery.isPlainObject(v) || jQuery.isArray(v)){
                    evaluateFn(v);
                }
            });
        };
        
        evaluateFn(options); 
        
        if(options.postData == null){
        	options.postData = {};
        }
        
        var datatype = 'json';
        
        if(options.driver === 'array'){
            datatype = 'local';
        }
        
        var pager = null; 
        var selectAll = false; 
        var selectedRow = null;
        
        if(options.pagerEnabled == true){
            pager = '#' + datagridId + '-pager';
        }
        
        var jqgrid = jQuery('#' + datagridId).jqGrid({
            beforeSelectRow: function (rowid, e) {
                
                var isChecked = false;
                
                if(jQuery(e.target).is(':input')){
                    isChecked = jQuery(e.target).is(':checked');
                } else {
                    isChecked = (jQuery("tr#"+rowid+".jqgrow > td > input.cbox", jQuery(this)).is(':checked') === false);
                }

                var beforeRowSelect = jQuery.Event('thrace_datagrid.beforeRowSelect');
                beforeRowSelect.gridId =  jQuery(this).attr('id');
                beforeRowSelect.id = rowid;
                beforeRowSelect.isChecked = isChecked;
                jQuery('#thrace-datagrid-container-' + gridName).next()
                    .trigger(beforeRowSelect);
                
                
                return true;
                
            },
            onSelectRow: function(aRowids, status){ 
                         
                /** Applying logic for mass actions  */
                selectAll = false;
                
                selectedRow = jQuery(this).getRowData(aRowids);
                
                toggleMassActionBtns(jQuery(this), datagridId);

                /** Connecting master with dependent grids. */	
                var ids = aRowids;

                jQuery.each(options.dependentDataGrids, function(idx, grid){
                	var postData = options.postData;
                	postData.masterGridRowId = ids;
                    var subGridId = 'thrace-datagrid-' + grid;  
                    jQuery("#" + subGridId).jqGrid('setGridParam',{
                        postData: postData
                    })
                    .trigger('reloadGrid');
                });
            
                return true;
            },

            onSelectAll: function(aRowids, status){
              
                var onSelectAll = jQuery.Event('thrace_datagrid.onSelectAll');
                onSelectAll.gridId = jQuery(this).attr('id');
                onSelectAll.status = status;
                onSelectAll.ids = aRowids;
                jQuery('#thrace-datagrid-container-' + gridName).next()
                    .trigger(onSelectAll);
              
                /** Applying logic for mass actions  */
                selectAll = false;
                toggleMassActionBtns(jQuery(this), datagridId);
                
                return true;
            },
            
            gridComplete: function(){
                
                var gridComplete = jQuery.Event('thrace_datagrid.gridComplete');
                gridComplete.gridId =  jQuery(this).attr('id');
                jQuery('#thrace-datagrid-container-' + gridName).next()
                    .trigger(gridComplete);
                
                // Activates multi select sortable element
                if(options.multiSelectSortableEnabled == true){                      
                   
                    var sortableElement = jQuery('#' + datagridId)
                        .closest('.thrace-grid')
                        .next('.multi-select-sortable');
                    
                    jQuery('#' + datagridId + ' .ui-row-ltr').draggable({
                        revert:     false,
                        stack: '#' + sortableElement.attr('id'),
                        zIndex: 2700,
                        appendTo:   '#' + sortableElement.attr('id'),
                        cursor:     "move",
                        cursorAt:   {
                            top: 15,
                            left: 25
                        },
                        'helper': function(){
                            var rowId = jQuery(this).attr('id');
                            var cellValue = jQuery('#' + datagridId).jqGrid('getCell', rowId, options.multiSelectSortableColumn);

                            return jQuery('<div class="ui-state-default multi-select-element">'+  cellValue +'</div>').data('referenceId', rowId);
                        },
                        start: function(event, ui) {
                            jQuery(this).parent().fadeTo('fast', 0.5);
                        },
                        stop: function(event, ui) {
                            jQuery(this).parent().fadeTo(0, 1);
                        },
                        connectToSortable: '#' + sortableElement.attr('id')
                    });

                    jQuery('#' + sortableElement.attr('id')).fadeIn();
                
                }
            },
            
            url: options.data_url,
            editurl: options.row_action_url,
            datatype: datatype,
            mtype: 'post',
            postData: options.postData,
            hidegrid: options.hideGrid,
            hiddengrid: options.hiddenGrid,
            autowidth: options.autoWidth,
            height: options.height,
            colNames:options.colNames,
            colModel:options.colModel,
            caption: options.caption,
            sortname: options.sortname,
            sortorder: options.sortorder,
            rowNum: options.rowNum,
            rowList: options.rowList,
            viewrecords: options.viewRecordsEnabled,
            pager: pager,
            grouping: options.groupingEnabled,
            groupingView: options.groupingViewOptions,
            rownumbers: options.rowNumbersEnabled,
            multiselect: options.multiselect,
            toolbar: [options.massActionsEnabled, "top"],
            forceFit: options.forceFit,
            shrinkToFit: options.shrinkToFit,
            scroll:options.sortableEnabled,
            gridview: true
        });
        
        // Loading local data
        if(options.driver === 'array'){
            jQuery.each(options.data, function(k,v){
                jqgrid.jqGrid('addRowData',v.id, v);
            });
        }
        

        /** initializing pager */
        var navGrid = 
            jqgrid.jqGrid('navGrid', pager, 
            { 
                search:options.searchBtnEnabled, 
                edit:options.editBtnEnabled,
                add:options.addBtnEnabled,
                del:options.deleteBtnEnabled,               
            },
            {
            	width: 600, // fix twitter-bootstrap
                afterSubmit : function(response, postdata){
                    if(response.status != 200){
                        alert('Server error: ' + response.status);
                        return false;
                    }

                    serverResponse = jQuery.parseJSON(response.responseText);
                    success = serverResponse.success;
                    message = buildErrorMessages(serverResponse.errors);
                    id = serverResponse.id;

                    if(options.driver === 'array' && success){
                        jqgrid.jqGrid('setRowData',id ,serverResponse.data);
                    }

                    return [success,message,id];
                },
                
            },
            {
            	width: 600, // fix twitter-bootstrap
            	afterSubmit : function(response, postdata){
            		
            		if(response.status != 200){
                            alert('Server error: ' + response.status);
                            return false;
            		}
            		
            		serverResponse = jQuery.parseJSON(response.responseText);
            		success = serverResponse.success;
            		message = buildErrorMessages(serverResponse.errors);
            		id = serverResponse.id;
            		
            		if(options.driver === 'array' && success){
                            jqgrid.jqGrid('addRowData',id ,serverResponse.data);
                        }
            		
            		return [success,message,id];
            	},
            	
            },
            {},
            {
                sopt:options.searchOptions, 
                multipleSearch:true, 
                multipleGroup:false
            }
        );

        /** Binding to jstree event */
        jQuery('body').bind('thrace.tree.event.complete', function(e){
            if(options.treeName == e.name){
                jqgrid.trigger("reloadGrid");
            }
        	
        });
    
        // custom buttons
        addCustomButtons(jqgrid, navGrid, pager, options);

        /** Initializing mass actions */
        if(options.massActionsEnabled){

        	/** Appending html to the top panel */
            jQuery('#t_' + datagridId).append(
                "<div class='thrace-massaction' style='float:left; margin-left:5px'>" +
                "<input class='toogle-massaction' type='button'  value='"+ options.massActionLabels.toogle_select_all +"' style='height:20px;font-size:-3'/>" +
                "</div>" +
                "<div style='float:right; margin-right:15px'>" +
                "<span>"+ options.massActionLabels.with_selected +": </span> " +
                "<select class='mass-action' disabled='true' style='padding:0px;height:20px;margin-bottom:0px'>" +
                buildSelectOptions(options.massActions, options.massActionLabels.select_action) +
                "</select>" +
                "<input class='submit-action' type='button' disabled='true' value='Submit' style='height:20px;line-height:10px;margin-left:5px;font-size:-3'/>" +
                "</div>"
            );

            /** Assigning click event to deselect-all button */
            jQuery('#t_' + datagridId + ' .toogle-massaction').click(function(){ 
                jQuery('#cb_' + datagridId).trigger('click');
                selectAll = jQuery('#cb_' + datagridId).is(':checked');
                return false;
            });

            /** Toggle select-all and deselect-all buttons */
            jQuery('#t_' + datagridId + ' .mass-action').change(function(){
                if(jQuery(this).val()){
                    jQuery(this).next().attr('disabled', false);
                } else {
                    jQuery(this).next().attr('disabled', true);
                }
            });

            /** Disable select and deselect button when search button is clicked */
            jQuery('#search_' + datagridId).click(function(){ 
                jQuery('#cb_' + datagridId).attr("checked", false);
                jQuery('#cb_' + datagridId).trigger('click');
                jQuery('#cb_' + datagridId).attr("checked", false);
                selectAll = false;
			
            });

            /** Executes selected mass action */
            jQuery('#t_' + datagridId + ' .submit-action').click( function() { 
                var action = jQuery(this).prev().val();
                var ids = jqgrid.jqGrid('getGridParam','selarrrow');
                if(ids.length){ 
                    var postData = jqgrid.jqGrid('getGridParam','postData');
                	
                    jQuery.post(
            		options.mass_action_url, 
                    {
                        'action' : action, 
                        'ids': ids, 
                        'selectAll' : selectAll,
                        '_search': postData._search,
                        'filters': postData.filters
                    }, function(response){
                        jqgrid.trigger("reloadGrid");
                        jQuery('#t_' + datagridId + ' .mass-action').attr('disabled', true);
                        jQuery('#t_' + datagridId + ' .submit-action').attr('disabled', true);
                        var massActionEvent = jQuery.Event('thrace_datagrid.event.massAction');
                        massActionEvent.name = gridName;
                        massActionEvent.action = action;
                        massActionEvent.response = response;
                        jQuery("body").trigger(massActionEvent);
                    });
                }
            });

        }

        /** Initializing sortable */
        if(options.sortableEnabled){
            jqgrid.jqGrid('sortableRows', {
                update: function(event, ui) {
                    var row_id = ui.item.attr('id');
                    var row_position = jqgrid.getInd(row_id, false);
                  
                    jQuery.post(
                        options.sortable_url, 
                        {
                            'row_id': row_id,
                            'row_position': row_position
                        }, 
                        function(response){
                            if(options.driver !== 'array'){
                                jqgrid.trigger("reloadGrid");
                                var sortableEvent = jQuery.Event('thrace_datagrid.event.sortable');

                                sortableEvent.name = gridName;
                                sortableEvent.rowId = row_id;
                                sortableEvent.rowPosition = row_position;
                                sortableEvent.response = response;
                                jQuery("body").trigger(sortableEvent);
                            }                      	
                        }
                    );
                }
            });

        }

        /** Overwriting add button */
        if(options.addBtnEnabled && options.addBtnUri){
            jQuery('#add_' + datagridId).unbind('click').click(function(){
                window.location = decodeURIComponent(options.addBtnUri);
            });
        }

        /** Overwriting edit button */
        if(options.editBtnEnabled && options.editBtnUri){
            var edit = jQuery('#edit_' + datagridId).clone(true); 
            jQuery('#edit_' + datagridId).unbind('click').click(function(){
            	
            	var id = jqgrid.jqGrid('getGridParam','selrow');

                if(id != null && selectedRow != null){ 
                    var rowData = jQuery.extend({'id': id}, selectedRow);
                    var editUri = decodeURIComponent(options.editBtnUri);
	            	
                    jQuery.each(rowData, function(k,v){
                        editUri = editUri.replace('{' + k + '}', v);
                    });
	            	
                    window.location = editUri;
                    
                } else {
                    edit.click();
                }
            });
        }
	
        /** Overwriting delete button */
        if(options.deleteBtnEnabled && options.deleteBtnUri){
            var del = jQuery('#del_' + datagridId).clone(true); 
            jQuery('#del_' + datagridId).unbind('click').click(function(){
            	var id = jqgrid.jqGrid('getGridParam','selrow');
            	if(id != null && selectedRow != null){ 
                    
                    var rowData = jQuery.extend({'id': id}, selectedRow);
                    var deleteUri = decodeURIComponent(options.deleteBtnUri);
                    
                    jQuery.each(rowData, function(k,v){
                        deleteUri = deleteUri.replace('{' + k + '}', v);
                    });
	            	
                    window.location = deleteUri;
                } else {
                    del.click();
                }
            });
        }
    });
});

/**
 * Building Select options for mass action
 * 
 * @param massActions
 * @param label (select_action)
 * @returns {String}
 */
function buildSelectOptions (massActions, label){

    var html = '<option value="">'+ label +'</option>';
    jQuery.each(massActions, function(key, value){
        html += '<option value="'+ key +'">'+ value +'</option>'; 
    });

    return html;
}

/**
 * Toggle mass action buttons
 * 
 * @param grid
 * @param datagridId
 */
function toggleMassActionBtns(grid, datagridId)
{
    var status = grid.jqGrid('getGridParam','selarrrow').length ? false : true;
    var massAction = jQuery('#t_' + datagridId + ' .mass-action');
    massAction.attr('disabled', status);
    if(status || !massAction.val()){
        jQuery('#t_' + datagridId + ' .submit-action').attr('disabled', true);
    } else {
        jQuery('#t_' + datagridId + ' .submit-action').attr('disabled', false);
    }
}

/**
 * Building error messages for manipulating data
 * 
 * @param errors
 * @returns {String}
 */
function buildErrorMessages(errors)
{
    var html = '<ul>';
	jQuery.each(errors, function(key, value){
        html += '<li>' + value + '</li>';
    });

    html += '</ul>';

    return html;
}

/**
 * Building custom buttons
 * 
 * @param jqgrid
 * @param navGrid
 * @param pager
 * @param options
 */
function addCustomButtons(jqgrid, navGrid, pager, options)
{   

    jQuery.each(options.customButtons, function(k,v){
        
        navGrid.navButtonAdd(pager,{
            caption:v.caption, 
            title:v.title, 
            buttonicon: v.buttonicon, 
            onClickButton: function(){ 
                var id = jqgrid.jqGrid('getGridParam','selrow');
                selectedRow = jqgrid.getRowData(id);

                if(id != null && selectedRow != null){ 
                    var rowData = jQuery.extend({'id': id}, selectedRow);
                    var uri = decodeURIComponent(v.uri);

                    jQuery.each(rowData, function(k,v){
                        uri = uri.replace('{' + k + '}', v);
                    });

                    window.location = uri;

                } else {
                    jQuery('<div></div>').html(options.custom_button_dlg_body).dialog({ 
                        'title' : options.custom_button_dlg_title,
                        'modal' : true,
                    });
                }

                return false;
            }, 
            position:v.position
        });
        
    });

}
