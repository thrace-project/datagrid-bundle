<?php
/*
 * This file is part of ThraceDataGridBundle
 *
 * (c) Nikolay Georgiev <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\DataGridBundle;

/**
 * This class describes all events in DataGridBundle
 *
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 */
class DataGridEvents
{    
    /**
     * Event is dispatched when mass action is triggered
     */
    const onMassAction = 'thrace_datagrid.onMassAction';
    
    /**
     * Event is dispatched when row position is changed
     */
    const onRowPositionChange = 'thrace_datagrid.onRowPositionChange';

    /**
     * Event is dispatched when adding new record
     */
    const onRowAdd = 'thrace_datagrid.onRowAdd';

    /**
     * Event is dispatched when editing record
     */
    const onRowEdit = 'thrace_datagrid.onRowEdit';

    /**
     * Event is dispatched when adding deleting record
     */
    const onRowDel = 'thrace_datagrid.onRowDel';
    
    /**
     * Event is dispatched after query is ready
     */
    const onQueryReady = 'thrace_datagrid.onQueryReady';
    
    /**
     * Event is dispatched right after data is ready
     */
    const onDataReady = 'thrace_datagrid.onDataReady';
}
