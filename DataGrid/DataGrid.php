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
 * Default implementation of the DataGridInterface
 *
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 */
class DataGrid implements DataGridInterface  
{

    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $driver;

    /**
     * @var string
     */
    protected $caption;

    /**
     * @var boolean
     */
    protected $hideGrid = false;

    /**
     * @var boolean
     */
    protected $hiddenGrid = false;

    /**
     * @var integer
     */
    protected $height = 150;

    /**
     * @var boolean
     */
    protected $autoWidth = true;
    
    /**
     * @var boolean
     */
    protected $forceFit = false;
    
    /**
     *  @var boolean
     */
    protected $shrinkToFit = true;

    /**
     * @var Array<string>
     */
    protected $colNames = array();

    /**
     * @var Array
     */
    protected $colModel = array();
    
    /**
     * @var array
     */
    protected $data;

    /**
     * @var Object
     */
    protected $qb;

    /**
     * @var boolean
     */
    protected $pagerEnabled = true;

    /**
     * @var boolean
     */
    protected $viewRecordsEnabled = true;

    /**
     * @var integer
     */
    protected $rowNum = 10;

    /**
     * @var Array<integer>
     */
    protected $rowList = array(10, 20, 30, 50);

    /**
     * @var string
     */
    protected $sortname;

    /**
     * @var string
     */
    protected $sortorder = 'asc';

    /**
     * @var boolean
     */
    protected $groupingEnabled = false;

    /**
     * @var Array
     */
    protected $groupingViewOptions = array();

    /**
     * @var boolean
     */
    protected $rowNumbersEnabled = false;

    /**
     * @var boolean
     */
    protected $sortableEnabled = false;
    
    /**
     * @var array | null
     */
    protected $searchOptions;

    /**
     * @var boolean
     */
    protected $searchBtnEnabled = false;

    /**
     * @var boolean 
     */
    protected $addBtnEnabled = false;

    /**
     * @var string
     */
    protected $addBtnUri;

    /**
     * @var boolean
     */
    protected $editBtnEnabled = false;

    /**
     * @var string
     */
    protected $editBtnUri;

    /**
     * @var string
     */
    protected $deleteBtnEnabled = false;

    /**
     * @var string
     */
    protected $deleteBtnUri = null;
    
    /**
     * @var boolean
     */
    protected $multiselect = false;

    /**
     * @var boolean
     */
    protected $massActionsEnabled = false;

    /**
     * @var Array
     */
    protected $massActions = array();
    
    /**
     * @var Array
     */
    protected $massActionLabels = array();

    /**
     * @var array
     */
    protected $dependentDataGrids = array();

    /**
     * @var boolean
     */
    protected $dependentGrid = false;
    
    /**
     * @var string | null
     */
    protected $treeName;
    
    /**
     * @var boolean
     */
    protected $multiSelectSortableEnabled = false;
    
    /**
     * @var string
     */
    protected $multiSelectSortableColumn;
    
    /**
     * @var array
     */
    protected $customButtons = array();
    
    /**
     * @var array|null
     */
    protected $postData = null;

    /**
     * Constructor
     * 
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = (string) $name;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setName()
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::setDriver()
     */
    public function setDriver($driver)
    {
        $availableDrivers = array(DataGridInterface::ARRAY_DRIVER, DataGridInterface::ORM_DRIVER);
        
        if (!in_array($driver, $availableDrivers)){
            throw new \InvalidArgumentException(sprintf('Driver %s is not available.', $driver));
        }
        
        $this->driver = $driver;
        
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::getDriver()
     */
    public function getDriver()
    {
        if (null === $this->driver){
            return DataGridInterface::ORM_DRIVER;
        }
        return $this->driver;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getCaption()
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setCaption()
     */
    public function setCaption($caption)
    {
        $this->caption = (string) $caption;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getHideGrid()
     */
    public function getHideGrid()
    {
        return $this->hideGrid;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setHideGrid()
     */
    public function setHideGrid($bool)
    {
        $this->hideGrid = (bool) $bool;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setHiddenGrid()
     */
    public function setHiddenGrid($bool)
    {
        $this->hiddenGrid = (bool) $bool;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getHiddenGrid()
     */
    public function getHiddenGrid()
    {
        return $this->hiddenGrid;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getHeight()
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setHeight()
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getAutoWidth()
     */
    public function getAutoWidth()
    {
        return $this->autoWidth;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setAutoWidth()
     */
    public function setAutoWidth($bool)
    {
        $this->autoWidth = (bool) $bool;

        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setForceFit()
     */
    public function setForceFit($bool)
    {
        $this->forceFit = (bool) $bool;
        
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getForceFit()
     */
    public function getForceFit()
    {
        return $this->forceFit;
    }
    
    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setShrinkToFit()
     */
    public function setShrinkToFit($bool)
    {
        $this->shrinkToFit = (bool) $bool;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getShrinkToFit()
     */
    public function getShrinkToFit()
    {
        return $this->shrinkToFit;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setColNames()
     */
    public function setColNames(array $colNames)
    {
        $this->colNames = $colNames;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getColNames()
     */
    public function getColNames()
    {
        if (count($this->colNames) != count($this->colModel)) {
            throw new \InvalidArgumentException('The number of colNames is not equal to the number of colModel');
        } 
        return $this->colNames;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setColModel()
     */
    public function setColModel(array $colModels)
    {
        foreach ($colModels as $colModel){
            if (!isset($colModel['name']) || !isset($colModel['index'])){
                throw new \LogicException('ColModel option "name" or "index" is not set.');
            }
            
            $this->colModel[] = $colModel;
        }
        
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getColModel()
     */
    public function getColModel()
    {
        if (count($this->colNames) != count($this->colModel)) {
            throw new \InvalidArgumentException('The number of colNames is not equal to the number of colModel');
        } 

        return $this->colModel;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::setData()
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;  
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::getData()
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setQueryBuilder()
     */
    public function setQueryBuilder($qb)
    {
        $this->qb = $qb;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getQueryBuilder()
     */
    public function getQueryBuilder()
    {
        return $this->qb;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::enablePager()
     */
    public function enablePager($bool)
    {
        $this->pagerEnabled = (bool) $bool;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::isPagerEnabled()
     */
    public function isPagerEnabled()
    {
        return $this->pagerEnabled;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::isViewRecordsEnabled()
     */
    public function isViewRecordsEnabled()
    {
        return $this->viewRecordsEnabled;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::enableViewRecords()
     */
    public function enableViewRecords($bool)
    {
        $this->viewRecordsEnabled = (bool) $bool;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getRowNum()
     */
    public function getRowNum()
    {
        return $this->rowNum;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setRowNum()
     */
    public function setRowNum($rowNum)
    {
        $this->rowNum = (int) $rowNum;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setRowList()
     */
    public function setRowList(Array $rowList)
    {
        $this->rowList = $rowList;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getRowList()
     */
    public function getRowList()
    {
        return $this->rowList;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setSortName()
     */
    public function setSortName($sortname)
    {
        $this->sortname = $sortname;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getSortName()
     */
    public function getSortName()
    {
        return $this->sortname;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setSortOrder()
     */
    public function setSortOrder($sortorder)
    {
        $sortorder = strtolower($sortorder);
        if (!in_array($sortorder, array('asc', 'desc'))) {
            throw new \InvalidArgumentException(sprintf('Provided argument "%s" is invalid. Use (asc | desc)', $sortorder));
        }

        $this->sortorder = $sortorder;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getSortOrder()
     */
    public function getSortOrder()
    {
        return $this->sortorder;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::isGroupingEnabled()
     */
    public function isGroupingEnabled()
    {
        return $this->groupingEnabled;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::enableGrouping()
     */
    public function enableGrouping($bool)
    {
        $this->groupingEnabled = (bool) $bool;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setGroupingViewOptions()
     */
    public function setGroupingViewOptions(array $groupingViewOptions)
    {
        $this->groupingViewOptions = $groupingViewOptions;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getGroupingView()
     */
    public function getGroupingViewOptions()
    {
        return $this->groupingViewOptions;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::enableRowNumbers()
     */
    public function enableRowNumbers($bool)
    {
        $this->rowNumbersEnabled = (bool) $bool;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::isRowNumbersEnabled()
     */
    public function isRowNumbersEnabled()
    {
        return $this->rowNumbersEnabled;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::enableSortable()
     */
    public function enableSortable($bool)
    {
        $this->sortableEnabled = (bool) $bool;
        
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::isSortableEnabled()
     */
    public function isSortableEnabled()
    {
        return $this->sortableEnabled;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::setSearchOptions()
     */
    public function setSearchOptions(array $searchOptions)
    {
        $availableOptions = array('eq', 'ne', 'lt', 'le', 'gt', 'ge', 'bw', 'bn', 'ew', 'en', 'cn', 'nc');
        if (count(array_diff($searchOptions, $availableOptions)) > 0){
            throw new \InvalidArgumentException(sprintf('Invalid search options %s', json_encode($searchOptions)));
        }
        
        $this->searchOptions = $searchOptions;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::getSearchOptions()
     */
    public function getSearchOptions()
    {
        if ($this->searchOptions){
            return $this->searchOptions;
        }
        
        return array('eq', 'ne', 'lt', 'le', 'gt', 'ge', 'bw', 'bn', 'ew', 'en', 'cn', 'nc');
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::enableSearchButton()
     */
    public function enableSearchButton($bool)
    {
        $this->searchBtnEnabled = (bool) $bool;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::isSearchButtonEnabled()
     */
    public function isSearchButtonEnabled()
    {
        return $this->searchBtnEnabled;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::enableAddButton()
     */
    public function enableAddButton($bool)
    {
        $this->addBtnEnabled = (bool) $bool;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::isAddButtonEnabled()
     */
    public function isAddButtonEnabled()
    {
        return $this->addBtnEnabled;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setAddBtnUri()
     */
    public function setAddBtnUri($uri)
    {
        $this->addBtnUri = (string) $uri;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getAddBtnUri()
     */
    public function getAddBtnUri()
    {
        return $this->addBtnUri;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::enableEditButton()
     */
    public function enableEditButton($bool)
    {
        $this->editBtnEnabled = (bool) $bool;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::isEditButtonEnabled()
     */
    public function isEditButtonEnabled()
    {
        return $this->editBtnEnabled;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setEditBtnUri()
     */
    public function setEditBtnUri($uri)
    {
        $this->editBtnUri = (string) $uri;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getEditBtnUri()
     */
    public function getEditBtnUri()
    {
        return $this->editBtnUri;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::enableDeleteButton()
     */
    public function enableDeleteButton($bool)
    {
        $this->deleteBtnEnabled = (bool) $bool;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::isDeleteButtonEnabled()
     */
    public function isDeleteButtonEnabled()
    {
        return $this->deleteBtnEnabled;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setDeleteBtnUri()
     */
    public function setDeleteBtnUri($uri)
    {
        $this->deleteBtnUri = (string) $uri;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getDeleteBtnUri()
     */
    public function getDeleteBtnUri()
    {
        return $this->deleteBtnUri;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::enableMultiSelect()
     */
    public function enableMultiSelect($bool)
    {
        $this->multiselect = (bool) $bool;
        
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::isMultiSelectEnabled()
     */
    public function isMultiSelectEnabled()
    {
        return $this->multiselect;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::enableMassActions()
     */
    public function enableMassActions($bool)
    {
        $this->massActionsEnabled = (bool) $bool;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::isMassActionsEnabled()
     */
    public function isMassActionsEnabled()
    {
        return $this->massActionsEnabled;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setMassActions()
     */
    public function setMassActions(array $massActions)
    {
        $this->massActions = $massActions;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getActions()
     */
    public function getMassActions()
    {
        return $this->massActions;
    }

    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::setDependentDataGrids()
     */
    public function setDependentDataGrids(array $dependentDataGrids)
    {
        $this->dependentDataGrids = $dependentDataGrids;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::getDependentDataGrids()
     */
    public function getDependentDataGrids()
    {
        return $this->dependentDataGrids;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setAsDependentGrid()
     */
    public function setAsDependentGrid($bool)
    {
        $this->dependentGrid = (bool) $bool;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::isDependentGrid()
     */
    public function isDependentGrid()
    {
        return $this->dependentGrid;
    }
    
    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setTreeName()
     */
    public function setTreeName($treeName)
    {
        $this->treeName = (string) $treeName;
        
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getTreeName()
     */
    public function getTreeName()
    {
        return $this->treeName;
    }
    
    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::enableMultiSelectSortable()
     */
    public function enableMultiSelectSortable($bool)
    {
        $this->multiSelectSortableEnabled = (bool) $bool;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::isMultiSelectSortableEnabled()
     */
    public function isMultiSelectSortableEnabled()
    {
        return $this->multiSelectSortableEnabled;
    }
    
    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::setMultiSelectSortableColumn()
     */
    public function setMultiSelectSortableColumn($column)
    {
        $this->multiSelectSortableColumn = (string) $column;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see Thrace\DataGridBundle\DataGrid.DataGridInterface::getMultiSelectSortableColumn()
     */
    public function getMultiSelectSortableColumn()
    {
        return $this->multiSelectSortableColumn;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::hasCustomButton()
     */
    public function hasCustomButton($name)
    {
        return isset($this->customButtons[$name]);
    }

    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::addCustomButton()
     */
    public function addCustomButton(CustomButton $customButton)
    {
        $this->customButtons[$customButton->getName()] = $customButton;
    
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::setCustomButtons($customButtons)
     */
    public function setCustomButtons(array $customButtons)
    {
        $this->customButtons = array();
        
        foreach ($customButtons as $customButton){
            $this->addCustomButton($customButton);
        }
    
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::getCustomButton()
     */
    public function getCustomButton($name)
    {
        if (!$this->hasCustomButton($name)){
            throw new \InvalidArgumentException(
                sprintf('CustomButton: "%s" does not exist in the stack', $name)
            );
        }
    
        return $this->customButtons[$name];
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::getCustomButtons()
     */
    public function getCustomButtons()
    {
        return $this->customButtons;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::getCustomButtonsAsOptions()
     */
    public function getCustomButtonsAsOptions()
    {
        $options = array();
        
        foreach ($this->getCustomButtons() as $customButton){
            $options[$customButton->getName()] = $customButton->getOptions();
        }
        
        return $options;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::setPostData()
     */
    public function setPostData(array $postData)
    {
        $this->postData = $postData;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridInterface::getPostData()
     */
    public function getPostData()
    {
        return $this->postData;
    }

    /**
     * Exports this datagrid to an array
     * 
     * @return Array
     */
    public function exportOptions()
    {
        $this->normalizeOptions();
        
        $data['name'] = $this->getName();
        $data['driver'] = $this->getDriver();
        $data['data'] = $this->getData();
        $data['caption'] = $this->getCaption();
        $data['hideGrid'] = $this->getHideGrid();
        $data['hiddenGrid'] = $this->getHiddenGrid();
        $data['height'] = $this->getHeight();
        $data['autoWidth'] = $this->getAutoWidth();
        $data['forceFit'] = $this->getForceFit();
        $data['shrinkToFit'] = $this->getShrinkToFit();
        $data['colNames'] = array_values($this->getColNames());
        $data['colModel'] = $this->getColModel();
        $data['pagerEnabled'] = $this->isPagerEnabled();
        $data['viewRecordsEnabled'] = $this->isViewRecordsEnabled();
        $data['rowNum'] = $this->getRowNum();
        $data['rowList'] = $this->getRowList();
        $data['sortname'] = $this->getSortName();
        $data['sortorder'] = $this->getSortOrder();
        $data['groupingEnabled'] = $this->isGroupingEnabled();
        $data['groupingViewOptions'] = $this->getGroupingViewOptions();
        $data['rowNumbersEnabled'] = $this->isRowNumbersEnabled();
        $data['sortableEnabled'] = $this->isSortableEnabled();
        $data['searchOptions'] = $this->getSearchOptions();
        $data['searchBtnEnabled'] = $this->isSearchButtonEnabled();
        $data['addBtnEnabled'] = $this->isAddButtonEnabled();
        $data['addBtnUri'] = $this->getAddBtnUri();
        $data['editBtnEnabled'] = $this->isEditButtonEnabled();
        $data['editBtnUri'] = $this->getEditBtnUri();
        $data['deleteBtnEnabled'] = $this->isDeleteButtonEnabled();
        $data['deleteBtnUri'] = $this->getDeleteBtnUri();
        $data['multiselect'] = $this->isMultiSelectEnabled();
        $data['massActionsEnabled'] = $this->isMassActionsEnabled();
        $data['massActions'] = $this->getMassActions();
        $data['dependentDataGrids'] = $this->getDependentDataGrids();
        $data['dependentGrid'] = $this->isDependentGrid();
        $data['treeName'] = $this->getTreeName();
        $data['multiSelectSortableEnabled'] = $this->isMultiSelectSortableEnabled();
        $data['multiSelectSortableColumn'] = $this->getMultiSelectSortableColumn();
        $data['customButtons'] = $this->getCustomButtonsAsOptions();
        $data['postData'] = $this->getPostData();
      
        return $data;
    }
    
    protected function normalizeOptions()
    {
        if ($this->isSortableEnabled()){
            $this->enableSearchButton(false);
        }
        
        if ($this->isMassActionsEnabled()){
            $this->enableMultiSelect(true);
        }
        
        if ($this->isMultiSelectSortableEnabled()){
            $this->enableMultiSelect(false);
        }
    }
}