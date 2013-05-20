<?php
namespace Thrace\DataGridBundle\Tests\DataGrid;

use Thrace\DataGridBundle\Tests\Stub\DataGridHandlerStub;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

use Symfony\Component\DependencyInjection\Container;

class DataGridHandlerTest extends BaseTestCase
{
    
    public function testGetDataGrid()
    {
        $dataGridHandler = new DataGridHandlerStub();
        
        $dataGridHandler->setDataGrid($this->getMock('Thrace\DataGridBundle\DataGrid\DataGridInterface'));
        
        $this->assertInstanceOf('Thrace\DataGridBundle\DataGrid\DataGridInterface', $dataGridHandler->getDataGrid());
    }
    
    public function testGetDataGridInvalidCall()
    {
        $dataGridHandler = new DataGridHandlerStub();
        
        $this->setExpectedException('LogicException');
        $dataGridHandler->getDataGrid();
    }
    
    public function testGetOptionsInvalidCall()
    {
        $dataGridHandler = new DataGridHandlerStub();
        
        $this->setExpectedException('LogicException');
        $dataGridHandler->getOptions();
    }
    
    public function testGetQueryInvalidCall()
    {
        $dataGridHandler = new DataGridHandlerStub();
        
        $this->setExpectedException('LogicException');
        $dataGridHandler->getQuery();
    }
    
    public function testGetDataInvalidCall()
    {        
        $dataGridHandler = new DataGridHandlerStub();
        
        $this->setExpectedException('LogicException');
        $dataGridHandler->getData();
    }
    
    public function testGetProcessedDataInvalidCall()
    {
        
        $dataGridHandler = new DataGridHandlerStub();
        
        $this->setExpectedException('LogicException');
        $dataGridHandler->getProcessedData();
    }
    
    public function testGetCountInvalidCall()
    {
        
        $dataGridHandler = new DataGridHandlerStub();
        
        $this->setExpectedException('LogicException');
        $dataGridHandler->getCount();
    }
    
    
    public function testBuildData()
    {
        $options = array(
            'page' => 1,
            'records' => 10
        );

        $dataGridHandler = new DataGridHandlerStub();
        $dataGridHandler
            ->setDispatcher($this->getMockEventDispatcher())
            ->setDataGrid($this->getMockDataGrid())
            ->resolveOptions($options)
            ->buildQuery()
            ->buildData()
        ;  
        
        $this->assertSame(2, $dataGridHandler->getCount());
        $this->assertInstanceOf('stdClass', $dataGridHandler->getQuery());
        $this->assertSame($dataGridHandler->getResult(), $dataGridHandler->getData());
        $this->assertNotEmpty($dataGridHandler->getProcessedData());
    }
    
    protected function getMockDataGrid()
    {
        $mock = $this->getMock('Thrace\DataGridBundle\DataGrid\DataGridInterface');
        
        $mock
            ->expects($this->exactly(2))
            ->method('getColModel')
            ->will($this->returnValue(array(array('index' => 'a.name', 'name' => 'name'))))
        ;
        
        return $mock;
    }

    protected function getMockEventDispatcher()
    {
        $mock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        
        $mock
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withAnyParameters()
        ;
        
        return $mock;
    }
      
}