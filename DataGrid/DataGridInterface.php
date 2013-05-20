<?php
/*
 * This file is part of ThraceDataGridBundle
 *
 * (c) Nikolay Georgiev <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\DataGridBundle\DataGrid;

/**
 * Interface implemented by a DataGrid class.
 *
 * This interace defines all options for jqgrid
 *
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 */

interface DataGridInterface 
{

    const ORM_DRIVER = 'orm';
    
    const ARRAY_DRIVER = 'array';
    
    /**
     * Set name of datagrid
     * Must be unique
     *
     * Provides a fluent interface
     *
     * @param string $name            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setName ($name);

    /**
     * Get name of datagrid
     *
     * @return string
     */
    public function getName ();
    
    /**
     * Sets driver
     * 
     * Provides a fluent interface
     * 
     * @param string $driver
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     * @throws \InvalidArgumentException
     */
    public function setDriver($driver);
    
    /**
     * Get driver
     * 
     * @return string
     */
    public function getDriver();

    /**
     * Set the caption of jqgrid
     *
     * Provides a fluent interface
     *
     * @param string $caption            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setCaption ($caption);

    /**
     * Gets the caption of jqgrid
     *
     * @return string
     */
    public function getCaption ();

    /**
     * Set hidegrid option of jqgrid
     *
     * Provides a fluent interface
     *
     * @param boolean $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setHideGrid ($bool);

    /**
     * Gets hidegrid option of jqgrid
     *
     * Provides a fluent interface
     *
     * @return bool
     */
    public function getHideGrid ();

    /**
     * Sets hiddengrid option
     *
     * Provides a fluent interface
     *
     * @param boolean $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setHiddenGrid ($bool);

    /**
     * Gets hiddengrid option
     *
     * @return boolean
     */
    public function getHiddenGrid ();

    /**
     * Set height (px) option of jqgrid
     *
     * Provides a fluent interface
     *
     * @param integer | string (50%) | auto $height
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setHeight ($height);

    /**
     * Gets height option of jqgrid
     *
     * @return integer | string (50%) | auto
     */
    public function getHeight ();

    /**
     * Set autowidth option of jqgrid
     *
     * Provides a fluent interface
     *
     * @param boolean $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setAutoWidth ($bool);

    /**
     * Gets autowidth option of jqgrid
     *
     * Defaults to false
     *
     * @return boolean
     */
    public function getAutoWidth ();
    
    /**
     * Sets forceFit option
     * 
     * Provides a fluent interface
     * 
     * @param boolean $bool
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setForceFit($bool);
    
    /**
     * Gets forceFit option
     * 
     * Defaults to false
     * 
     * @return boolean
     */
    public function getForceFit();
    
    /**
     * Sets shrinkToFit option
     * 
     * Provides a fluent interface
     * 
     * @param boolean $bool
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setShrinkToFit($bool);
    
    /**
     * Gets shrinkToFit option
     * 
     * Defaults to false
     * 
     * @return boolean
     */
    public function getShrinkToFit();

    /**
     * Set colNames option of jqgrid
     *
     * Provides a fluent interface
     *
     * @param Array<string>
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setColNames (array $colNames);

    /**
     * Gets colNames option of jqgrid
     *
     * @return Array<string>
     * @throws \InvalidArgumentException
     */
    public function getColNames ();

    /**
     * Set colModel option of jqgrid
     * Because it is intended to be used with Doctrine colname should be
     * provided like this:
     * Ex: u.username
     *
     * Provides a fluent interface
     *
     * @param Array
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     * @throws \LogicException
     */
    public function setColModel (Array $colModel);

    /**
     * Get colModel option of jqgrid
     *
     * @return Array
     * @throws \InvalidArgumentException
     */
    public function getColModel ();
    
    /**
     * Sets array data
     * 
     * Provides a fluent interface
     *
     * @param array
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setData(array $data);
    
    /**
     * Gets array data
     * 
     * @return array
     */
    public function getData();

    /**
     * Set QueryBuilder
     * used later to manipulate query
     *
     *
     * Provides a fluent interface
     *
     * @param Object
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setQueryBuilder ($qb);

    /**
     * Get QueryBuilder
     *
     * @return Object
     */
    public function getQueryBuilder ();

    /**
     * Enables jqgrid pager
     *
     *
     * Provides a fluent interface
     *
     * @param bool $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function enablePager ($bool);

    /**
     * Returns if pager is enabled
     *
     * @return bool;
     */
    public function isPagerEnabled ();

    /**
     * Enables jqgrid viewrecords option
     *
     *
     * Provides a fluent interface
     *
     * @param bool $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function enableViewRecords ($bool);

    /**
     * Returns if viewRecords are enabled
     *
     * Default to false
     *
     * @return bool
     */
    public function isViewRecordsEnabled ();

    /**
     * Set pager rowNum option of jqgrid
     *
     * Provides a fluent interface
     *
     * @param integer
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setRowNum ($rowNum);

    /**
     * Get pager rowNum option of jqgrid
     * By default it return 10
     *
     * @return integer
     */
    public function getRowNum ();

    /**
     * Set pager rowList option of jqgrid
     *
     * Provides a fluent interface
     *
     * @param Array<integer>
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setRowList (array $rowList);

    /**
     * Gets pager rowList option of jqgrid
     *
     * By default it return [10,20,30,50]
     *
     * @return Array<integer>
     */
    public function getRowList ();

    /**
     * Set default sortname option of jqgrid
     * Ex: u.username
     *
     * Provides a fluent interface
     *
     * @param string
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     * @throws \InvalidArgumentException
     */
    public function setSortName ($sortname);

    /**
     * Gets default sortname option of jqgrid
     *
     * @return string
     */
    public function getSortName ();

    /**
     * Set default sortorder option of jqgrid
     *
     * Provides a fluent interface
     *
     * @param string
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     * @throws \InvalidArgumentException
     */
    public function setSortOrder ($sortorder);

    /**
     * Gets default sortorder option of jqgrid
     *
     * By default returns asc
     *
     * Provides a fluent interface
     *
     * @return string asc | desc
     *        
     */
    public function getSortOrder ();

    /**
     * Returns if groiping is enabled
     *
     * @return boolean
     */
    public function isGroupingEnabled ();

    /**
     * Enables jqgrid grouping option
     *
     * @param boolean $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function enableGrouping ($bool);

    /**
     * Sets jqgrid grouping option
     *
     * Provides a fluent interface
     *
     * @param Array<string> $groupingViewOptions            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setGroupingViewOptions (Array $groupingViewOptions);

    /**
     * Gets jqgrid grouping option
     *
     * @return Array<string>
     */
    public function getGroupingViewOptions ();

    /**
     * Enables jqgrid rownumbers
     *
     * Provides a fluent interface
     *
     * @param boolean $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function enableRowNumbers ($bool);

    /**
     * Checks if rownumbers are enabled
     *
     * @return boolean
     */
    public function isRowNumbersEnabled ();

    /**
     * Enables sortable for jqgrid
     *
     * Provides a fluent interface
     *
     * @param boolean $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     * @throws \RuntimeException
     */
    public function enableSortable ($bool);

    /**
     * Checks if sortable is enabled
     *
     * @return boolean
     */
    public function isSortableEnabled ();
    
    /**
     * Sets soptions for jqgrid
     *
     * Provides a fluent interface
     *
     * @param array $searchOptions          
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     * @throws \InvalidArgumentException
     */
    public function setSearchOptions(array $searchOptions);
    
    /**
     * Gets soptions for jqgrid
     * 
     * @return array
     */
    public function getSearchOptions();

    /**
     * Enables search button
     *
     * Provides a fluent interface
     *
     * @param boolean $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function enableSearchButton ($bool);

    /**
     * Checks if search is enabled
     *
     * @return boolean
     */
    public function isSearchButtonEnabled ();

    /**
     * Enables add button
     *
     * Provides a fluent interface
     *
     * @param boolean $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function enableAddButton ($bool);

    /**
     * Check if add button is enabled
     *
     * @return boolean
     */
    public function isAddButtonEnabled ();

    /**
     * Sets add button uri
     *
     * Provides a fluent interface
     *
     * @param string $uri            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setAddBtnUri ($uri);

    /**
     * Gets add button uri
     *
     * @return string
     */
    public function getAddBtnUri ();

    /**
     * Enables edit button
     *
     * Provides a fluent interface
     *
     * @param boolean $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function enableEditButton ($bool);

    /**
     * Checks id edit button is enabled
     *
     * @return boolean
     */
    public function isEditButtonEnabled ();

    /**
     * Sets edit button uri
     *
     * Provides a fluent interface
     *
     * @param string $uri            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setEditBtnUri ($uri);

    /**
     * Gets edit route
     *
     * @return string
     */
    public function getEditBtnUri ();

    /**
     * Enabled delete button
     *
     * Provides a fluent interface
     *
     * @param boolean $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function enableDeleteButton ($bool);

    /**
     * Checks if delete button is enabled
     *
     * @return boolean
     */
    public function isDeleteButtonEnabled ();

    /**
     * Sets detele button uri
     *
     * Provides a fluent interface
     *
     * @param string $route            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setDeleteBtnUri ($uri);

    /**
     * Gets delete button uri
     *
     * @return string
     */
    public function getDeleteBtnUri ();
    
    /**
     * Enables jqgrid multi select option
     * 
     * Provides a fluent interface
     * 
     * @param boolean $bool
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function enableMultiSelect($bool);
    
    /**
     * Check if multi select is enabled
     * 
     * @return boolean
     */
    public function isMultiSelectEnabled();

    /**
     * Enables mass actions and multi select.
     *
     * Provides a fluent interface
     *
     * @param boolean $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function enableMassActions ($bool);

    /**
     * Checks if mass actions are enabled. 
     *
     * @return bolean
     */
    public function isMassActionsEnabled ();


    /**
     * Sets datagrid action and clears all existing ones
     *
     * Provides a fluent interface
     *
     * @param array<arrayMap> $massActions            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     * 
     */
    public function setMassActions (array $massActions);

    /**
     * Returns all registered actions
     *
     * @return Array
     */
    public function getMassActions ();
    
    /**
     * Sets the datagrid as a dependent grid
     *
     * Provides a fluent interface
     *
     * @param boolean $bool            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setAsDependentGrid ($bool);

    /**
     * Checks if grid is dependent
     *
     * return boolean
     */
    public function isDependentGrid ();


    /**
     * Sets dependent grids to master grid
     * and clears all existing ones
     *
     * Provides a fluent interface
     *
     * @param array $dependentDataGrids            
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     * 
     */
    public function setDependentDataGrids (array $dependentDataGrids);

    /**
     * Retrieves all dependent grids that belong to master grid
     *
     * @return array<string>
     */
    public function getDependentDataGrids ();
    
    /**
     * Sets jstree name
     * When change is made to jstree then jqgrid will trigger reload method
     * 
     * Provides a fluent interface
     * 
     * @param string $treeName
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setTreeName($treeName);
    
    /**
     * Gets jstree name
     * 
     * @return string
     */
    public function getTreeName();
    
    /**
     * Enables multi select sortable form type
     *   
     * Provides a fluent interface
     * 
     * @param boolean $bool
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function enableMultiSelectSortable($bool);
    
    /**
     * Checks if multi select sortable form type is enabled
     * 
     * @return boolean
     */
    public function isMultiSelectSortableEnabled();
    
    /**
     * Set which column to display on multi select element
     * 
     * Provides a fluent interface
     * 
     * @param string $column
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setMultiSelectSortableColumn($column);
    
    /**
     * Gets multi select element column
     * 
     * @return string
     */
    public function getMultiSelectSortableColumn();
    
    /**
     * 
     * Checks if button exists in the stack
     * 
     * @param string $name
     * @return boolean
     */
    public function hasCustomButton($name);
    
    /**
     * Adds custom button
     * 
     * Provides a fluent interface
     * 
     * @param CustomButton $customButton
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function addCustomButton(CustomButton $customButton);
    
    /**
     * Sets custom buttons
     * 
     * Provides a fluent interface
     * 
     * @param array $customButtons
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    public function setCustomButtons(array $customButtons);
    
    /**
     * Gets custom buttom by name
     * 
     * @param string $name
     * @return \Thrace\DataGridBundle\DataGrid\CustomButton
     * @throws \InvalidArgumentException
     */
    public function getCustomButton($name);
    
    /**
     * Gets an array of custom buttons
     * 
     * @return <Array> CustomButton
     */
    public function getCustomButtons();
    
    /**
     * Exports all custom button options as an array of arrays
     * 
     * @return array
     */
    public function getCustomButtonsAsOptions();

}