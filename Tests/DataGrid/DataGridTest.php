<?php
namespace Thrace\DataGridBundle\Tests\DataGrid;

use Thrace\DataGridBundle\DataGrid\DataGridInterface;

use Thrace\DataGridBundle\DataGrid\DataGridFactory;

class DataGridTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateDataGridWithEmptyParameters()
    {
        $dataGrid = $this->createDataGrid('name');
        $this->assertInstanceOf('\Thrace\DataGridBundle\DataGrid\DataGridInterface', $dataGrid);
    }
    
    public function testName()
    {
        $dataGrid = $this->createDataGrid('grid');
        $this->assertSame('grid', $dataGrid->getName());
        $dataGrid->setName('grid 2');
        $this->assertSame('grid 2', $dataGrid->getName());
        $this->assertArrayHasKey('name', $dataGrid->exportOptions());
        $this->assertContains('grid 2', $dataGrid->exportOptions());
    }
    
    
    public function testDriver()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertSame(DataGridInterface::ORM_DRIVER, $dataGrid->getDriver());
        
        $dataGrid->setDriver(DataGridInterface::ARRAY_DRIVER);
        $this->assertSame('array', $dataGrid->getDriver());
        $this->assertArrayHasKey('driver', $dataGrid->exportOptions());
        $this->assertContains('array', $dataGrid->exportOptions()); 
    }
    
    public function testDriverWithInvalidArgument()
    {
        $dataGrid = $this->createDataGrid();
        $this->setExpectedException('InvalidArgumentException');
        $dataGrid->setDriver('invalid');
      
    }
    
    public function testCaption()
    {
        $dataGrid = $this->createDataGrid();
        $dataGrid->setCaption('caption');
        $this->assertSame('caption', $dataGrid->getCaption());
        $this->assertContains('caption', $dataGrid->exportOptions());
    }
    
    public function testHideGrid()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertFalse($dataGrid->getHideGrid());
        $dataGrid->setHideGrid(true);
        $this->assertTrue($dataGrid->getHideGrid());
        $this->assertArrayHasKey('hideGrid', $dataGrid->exportOptions());
    }
    
    public function testHiddenGrid()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertFalse($dataGrid->getHiddenGrid());
        $dataGrid->setHiddenGrid(true);
        $this->assertTrue($dataGrid->getHiddenGrid());
        $this->assertArrayHasKey('hiddenGrid', $dataGrid->exportOptions());
    }
    
    public function testHeight()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertSame(150, $dataGrid->getHeight());
        $dataGrid->setHeight(100);
        $this->assertSame(100, $dataGrid->getHeight());
        $this->assertArrayHasKey('height', $dataGrid->exportOptions());
    }

    public function testAutoWidth()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertTrue($dataGrid->getAutoWidth());
        $dataGrid->setAutoWidth(false);
        $this->assertFalse($dataGrid->getAutoWidth());
        $this->assertArrayHasKey('autoWidth', $dataGrid->exportOptions());
    }

    public function testForceFit()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertFalse($dataGrid->getForceFit());
        $dataGrid->setForceFit(true);
        $this->assertTrue($dataGrid->getForceFit());
        $this->assertArrayHasKey('forceFit', $dataGrid->exportOptions());
    }

    public function testShrinkToFit()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertTrue($dataGrid->getShrinkToFit());
        $dataGrid->setShrinkToFit(false);
        $this->assertFalse($dataGrid->getShrinkToFit());
        $this->assertArrayHasKey('shrinkToFit', $dataGrid->exportOptions());
    }
    
    public function testColNamesAndColModel()
    {
        $colNames = array('col1', 'col2');
        
        $colModel = array(
            array('index' => 'a.col1', 'name' => 'col1'),
            array('index' => 'a.col2', 'name' => 'col2'),
        );
        
        $dataGrid = $this->createDataGrid();
        $dataGrid->setColNames($colNames);
        $dataGrid->setColModel($colModel);
        $this->assertSame($colNames, $dataGrid->getColNames());
        $this->assertSame($colModel, $dataGrid->getColModel());
        $this->assertArrayHasKey('colNames', $dataGrid->exportOptions());
        $this->assertArrayHasKey('colModel', $dataGrid->exportOptions());

    }
    
    public function testColNamesWithNotEqualNumberOfColumns()
    {
        $colNames = array('col1', 'col2');
        
        $colModel = array(
            array('index' => 'a.col1', 'name' => 'col1'),
        );
        
        $dataGrid = $this->createDataGrid();
        $dataGrid->setColNames($colNames);
        $dataGrid->setColModel($colModel);
        
        $this->setExpectedException('InvalidArgumentException');
        $dataGrid->getColNames();
        
    }
    
    public function testColModelWithNotEqualNumberOfColumns()
    {
        $colNames = array('col1', 'col2');
        
        $colModel = array(
            array('index' => 'a.col1', 'name' => 'col1'),
        );
        
        $dataGrid = $this->createDataGrid();
        $dataGrid->setColNames($colNames);
        $dataGrid->setColModel($colModel);
        
        $this->setExpectedException('InvalidArgumentException');
        
        $dataGrid->getColModel();
        
    }
    
    public function testColModelWithNotSetIndexOption()
    {
        $colNames = array('col1', 'col2');
        
        $colModel = array(
            array('invalid' => 'a.col1', 'name' => 'col1'),
        );
        
        $dataGrid = $this->createDataGrid();
        $this->setExpectedException('LogicException');
        $dataGrid->setColNames($colNames);
        $dataGrid->setColModel($colModel);
    }
    
    public function testData()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertEmpty($dataGrid->getData());
        $dataGrid->setData(array('data'));
        $this->assertSame(array('data'), $dataGrid->getData());
        $this->assertArrayHasKey('data', $dataGrid->exportOptions());
    }
    
    public function testSearchOptions()
    {
        $default = array('eq', 'ne', 'lt', 'le', 'gt', 'ge', 'bw', 'bn', 'ew', 'en', 'cn', 'nc');
        $dataGrid = $this->createDataGrid();
        $this->assertSame($default, $dataGrid->getSearchOptions());
        
        $dataGrid->setSearchOptions(array('eq', 'ge'));
        $this->assertSame(array('eq', 'ge'), $dataGrid->getSearchOptions());
        $this->assertArrayHasKey('searchOptions', $dataGrid->exportOptions());
    }
    
    public function testSearchOptionsWithInvalidArgument()
    {
        $dataGrid = $this->createDataGrid();
        $this->setExpectedException('InvalidArgumentException');
        $dataGrid->setSearchOptions(array('invalid'));      
    }

    public function testQueryBuilder()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertNull($dataGrid->getQueryBuilder());
        $dataGrid->setQueryBuilder(new \stdClass());
        $this->assertInstanceOf('stdClass', $dataGrid->getQueryBuilder());
    }
    
    
    public function testPager()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertTrue($dataGrid->isPagerEnabled());
        $dataGrid->enablePager(false);
        $this->assertFalse($dataGrid->isPagerEnabled());
        $this->assertArrayHasKey('pagerEnabled', $dataGrid->exportOptions());
    }
    
    public function testViewRecords()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertTrue($dataGrid->isViewRecordsEnabled());
        $dataGrid->enableViewRecords(false);
        $this->assertFalse($dataGrid->isViewRecordsEnabled());
        $this->assertArrayHasKey('viewRecordsEnabled', $dataGrid->exportOptions());
    }

    public function testRowNum()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertSame(10, $dataGrid->getRowNum());
        $dataGrid->setRowNum(11);
        $this->assertSame(11, $dataGrid->getRowNum());
        $this->assertArrayHasKey('rowNum', $dataGrid->exportOptions());
    }
    

    public function testRowList()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertSame(array(10, 20, 30, 50), $dataGrid->getRowList());
        $dataGrid->setRowList(array(11, 22));
        $this->assertContains(array(11, 22), $dataGrid->exportOptions());
        $this->assertArrayHasKey('rowList', $dataGrid->exportOptions());
    }
    
    
    public function testSortNameWithValidName()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertNull($dataGrid->getSortName());
        $dataGrid->setSortName('g.sortname');
        $this->assertSame('g.sortname', $dataGrid->getSortName());
        $this->assertArrayHasKey('sortname', $dataGrid->exportOptions());
    }
    
    public function testSortOrderWithInvalidArgument()
    {
        $this->setExpectedException('InvalidArgumentException');
        $dataGrid = $this->createDataGrid('grid');
        $dataGrid->setSortOrder('invalid');
    }
    

    public function testSortOrderWithValidArgument()
    {
        $dataGrid = $this->createDataGrid();
        $dataGrid->setSortOrder('desc');
        $this->assertSame('desc', $dataGrid->getSortOrder());
        $this->assertContains('desc', $dataGrid->exportOptions());
        $this->assertArrayHasKey('sortorder', $dataGrid->exportOptions());
    }
    

    public function testGrouping()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertFalse($dataGrid->isGroupingEnabled());
        $dataGrid->enableGrouping(true);
        $this->assertTrue($dataGrid->isGroupingEnabled());
        $this->assertArrayHasKey('groupingEnabled', $dataGrid->exportOptions());
    }
    

    public function testGroupingOptions()
    {
        $groupOptions = array('groupField' => 'g.grid', 'groupText' => 'some text');
        $dataGrid = $this->createDataGrid();
        $dataGrid->setGroupingViewOptions($groupOptions);
        $this->assertSame(
            $groupOptions, 
            $dataGrid->getGroupingViewOptions()
        );
        
        $this->assertContains('groupOptions', $dataGrid->exportOptions());
    }
    

    public function testRowNumbers()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertFalse($dataGrid->isRowNumbersEnabled());
        $dataGrid->enableRowNumbers(true);
        $this->assertTrue($dataGrid->isRowNumbersEnabled());
        $this->assertArrayHasKey('rowNumbersEnabled', $dataGrid->exportOptions());
    }

    
    public function testSortable()
    {
        $dataGrid = $this->createDataGrid();
        $dataGrid->enableSearchButton(true);
        $this->assertFalse($dataGrid->isSortableEnabled());
        $dataGrid->enableSortable(true);
        $this->assertTrue($dataGrid->isSortableEnabled());
        $this->assertArrayHasKey('sortableEnabled', $dataGrid->exportOptions());
        $this->assertFalse($dataGrid->isSearchButtonEnabled());
    }
    
    public function testSearchBtn()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertFalse($dataGrid->isSearchButtonEnabled());
        $dataGrid->enableSearchButton(true);
        $this->assertTrue($dataGrid->isSearchButtonEnabled());
        $this->assertArrayHasKey('searchBtnEnabled', $dataGrid->exportOptions());
    }
    
    public function testAddBtn()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertFalse($dataGrid->isAddButtonEnabled());
        $this->assertNull($dataGrid->getAddBtnUri());
        $dataGrid->enableAddButton(true);
        $dataGrid->setAddBtnUri('http://php.net');
        
        $this->assertTrue($dataGrid->isAddButtonEnabled());
        $this->assertSame('http://php.net', $dataGrid->getAddBtnUri());
        
        $this->assertArrayHasKey('addBtnEnabled', $dataGrid->exportOptions());
        $this->assertArrayHasKey('addBtnUri', $dataGrid->exportOptions());
    }
    
    public function testEditBtn()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertFalse($dataGrid->isEditButtonEnabled());
        $this->assertNull($dataGrid->getEditBtnUri());
        $dataGrid->enableEditButton(true);
        $dataGrid->setEditBtnUri('http://php.net');
        
        $this->assertTrue($dataGrid->isEditButtonEnabled());
        $this->assertSame('http://php.net', $dataGrid->getEditBtnUri());
        
        $this->assertArrayHasKey('editBtnEnabled', $dataGrid->exportOptions());
        $this->assertArrayHasKey('editBtnUri', $dataGrid->exportOptions());
    }
    
    public function testDeleteBtn()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertFalse($dataGrid->isDeleteButtonEnabled());
        $this->assertNull($dataGrid->getDeleteBtnUri());
        $dataGrid->enableDeleteButton(true);
        $dataGrid->setdeleteBtnUri('http://php.net');
        
        $this->assertTrue($dataGrid->isDeleteButtonEnabled());
        $this->assertSame('http://php.net', $dataGrid->getDeleteBtnUri());
        
        $this->assertArrayHasKey('deleteBtnEnabled', $dataGrid->exportOptions());
        $this->assertArrayHasKey('deleteBtnUri', $dataGrid->exportOptions());
    }
    
    public function testMultiSelect()
    {
        $dataGrid = $this->createDataGrid();
        $this->assertFalse($dataGrid->isMultiSelectEnabled());
        $dataGrid->enableMultiSelect(true);
        $this->assertTrue($dataGrid->isMultiSelectEnabled());
        $this->assertArrayHasKey('multiselect', $dataGrid->exportOptions());
    }

    public function testMassActions()
    {
        $massActions = array('s1' => 'label1', 's2' => 'label2');
        
        $dataGrid = $this->createDataGrid();
        $this->assertFalse($dataGrid->isMassActionsEnabled());
        $this->assertEmpty($dataGrid->getMassActions());
        
        $dataGrid->enableMassActions(true);
        $dataGrid->setMassActions($massActions);
        
        $this->assertTrue($dataGrid->isMassActionsEnabled());
        
        $this->assertSame(
            $massActions, 
            $dataGrid->getMassActions()
        );
        
        $this->assertArrayHasKey('massActionsEnabled', $dataGrid->exportOptions());
        $this->assertArrayHasKey('massActions', $dataGrid->exportOptions());
        $this->assertTrue($dataGrid->isMultiSelectEnabled());
    }
    
    
    public function testDependentGrid()
    {
        $grids =  array('grid1', 'grid2');
        $dataGrid = $this->createDataGrid();
        
        $this->assertFalse($dataGrid->isDependentGrid());
        $this->assertEmpty($dataGrid->getDependentDataGrids());
        
        $dataGrid->setAsDependentGrid(true);
        $dataGrid->setDependentDataGrids($grids);
        
        $this->assertSame($grids, $dataGrid->getDependentDataGrids());
        $this->assertTrue($dataGrid->isDependentGrid());

        $this->assertArrayHasKey('dependentDataGrids', $dataGrid->exportOptions());
        $this->assertArrayHasKey('dependentGrid', $dataGrid->exportOptions());
    }

    public function testTreeName()
    {
        $dataGrid = $this->createDataGrid();
        
        $this->assertNull($dataGrid->getTreeName());
        $dataGrid->setTreeName('tree');
        $this->assertSame('tree', $dataGrid->getTreeName());
        $this->assertArrayHasKey('treeName', $dataGrid->exportOptions());
    }

    public function testMultiSelectSortable()
    {
        $dataGrid = $this->createDataGrid();
        
        $this->assertFalse($dataGrid->isMultiSelectSortableEnabled());
        $this->assertNull($dataGrid->getMultiSelectSortableColumn());
        $dataGrid->enableMultiSelectSortable(true);
        $dataGrid->setMultiSelectSortableColumn('col');
        $this->assertTrue($dataGrid->isMultiSelectSortableEnabled());
        $this->assertSame('col', $dataGrid->getMultiSelectSortableColumn());
        $this->assertArrayHasKey('multiSelectSortableEnabled', $dataGrid->exportOptions());
        $this->assertArrayHasKey('multiSelectSortableColumn', $dataGrid->exportOptions());
    }

    public function testCustomButtonsDefault()
    {
        $dataGrid = $this->createDataGrid();
        $customButton = $this
            ->getMockBuilder('Thrace\DataGridBundle\DataGrid\CustomButton')
            ->disableOriginalConstructor()->getMock()
        ;
        
        $customButton
            ->expects($this->exactly(1))
            ->method('getName')
            ->will($this->returnValue('b'))
        ;
        
        $this->assertEmpty($dataGrid->getCustomButtons());
        $dataGrid->addCustomButton($customButton);
        $this->assertCount(1, $dataGrid->getCustomButtons());
        $this->assertTrue($dataGrid->hasCustomButton('b'));
        $this->assertInstanceOf('Thrace\DataGridBundle\DataGrid\CustomButton', $dataGrid->getCustomButton('b'));
    }
    
    public function testSetCustomButtons()
    {
        $dataGrid = $this->createDataGrid();
        $customButton = $this
            ->getMockBuilder('Thrace\DataGridBundle\DataGrid\CustomButton')
            ->disableOriginalConstructor()->getMock()
        ;
        
        $customButton
            ->expects($this->exactly(1))
            ->method('getName')
            ->will($this->returnValue('b'))
        ;
        
        $dataGrid->setCustomButtons(array($customButton));
        $this->assertCount(1, $dataGrid->getCustomButtons());
        $this->assertTrue($dataGrid->hasCustomButton('b'));
        $this->assertInstanceOf('Thrace\DataGridBundle\DataGrid\CustomButton', $dataGrid->getCustomButton('b'));
    }
    
    public function testGetCustomButtonsAsOptions()
    {
        $dataGrid = $this->createDataGrid();
        $customButton = $this
            ->getMockBuilder('Thrace\DataGridBundle\DataGrid\CustomButton')
            ->disableOriginalConstructor()->getMock()
        ;
        
        $customButton
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('b'))
        ;
        
        $customButton
            ->expects($this->exactly(1))
            ->method('getOptions')
            ->will($this->returnValue(array()))
        ;
        
        $dataGrid->addCustomButton($customButton);
        $opts = $dataGrid->exportOptions();
        $this->assertArrayHasKey('customButtons', $opts); 
        $this->assertSame(array('b' => array()), $opts['customButtons']);
    }

    public function testGetCustomButtonWithInvalidKey()
    {
        $dataGrid = $this->createDataGrid();
        
        $this->setExpectedException('InvalidArgumentException');
        $dataGrid->getCustomButton('not_valid_key');   
    }
    
    protected function createDataGrid($name = 'test')
    {
        $factory = new DataGridFactory();
        $dataGrid = $factory->createDataGrid($name);
        return $dataGrid;
    }
}