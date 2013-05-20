<?php
namespace Thrace\DataGridBundle\Tests\Controller;

use Thrace\DataGridBundle\DataGrid\DataGridInterface;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\Container;

use Thrace\DataGridBundle\Controller\DataGridController;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

class DataGridControllerTest extends BaseTestCase
{

    public function testGetDataGrid()
    {

        $mockRequest = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()->getMock();
        
        $mockRequest
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(true))
        ;
        
        $container = new Container();
        $container->set('request', $mockRequest);
        $container->set('thrace_data_grid.provider', $this->getMockProvider());
        
        $controller = new DataGridController();
        $controller->setContainer($container);

        $result = $controller->getDataGrid('test');
        
        $this->assertInstanceOf('Thrace\DataGridBundle\DataGrid\DataGridInterface', $result);
  
    }
    
    public function testGetDataGridWithInvalidRequest()
    {

        $mockRequest = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()->getMock();
        
        $mockRequest
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(false))
        ;
        
        $container = new Container();
        $container->set('request', $mockRequest);

        
        $controller = new DataGridController();
        $controller->setContainer($container);
        
        $this->setExpectedException('RuntimeException');
        $result = $controller->getDataGrid('test');
  
    }
    
    public function testGetDataGridWithInvalidName()
    {
        $mockRequest = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()->getMock();
        
        $mockRequest
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(true))
        ;
        
        $mockProvider = $this->getMock('Thrace\DataGridBundle\DataGrid\Provider\DataGridProviderInterface');
        
        $mockProvider
            ->expects($this->once())
            ->method('has')
            ->will($this->returnValue(false))
        ;
        
        $container = new Container();
        $container->set('request', $mockRequest);
        $container->set('thrace_data_grid.provider', $mockProvider);
        
        $controller = new DataGridController();
        $controller->setContainer($container);
        
        $this->setExpectedException('InvalidArgumentException');
        $result = $controller->getDataGrid('invalid');
  
    }
    
    public function testDataAction()
    {
   
        $container = new Container();
        $container->set('request', $this->getMockRequest());
        $container->set('thrace_data_grid.provider', $this->getMockProvider());
        $container->set('thrace_data_grid.handler.datagrid', $this->getMockDataGridHandler());
        
        
        $controller = new DataGridController();
        $controller->setContainer($container);
        $result = $controller->dataAction('test');
        
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        $this->assertSame(json_encode(array('key' => 'processedData')), $result->getContent());   
    }
    
    public function testSortableAction()
    {
        $mockRequest = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()->getMock();
        
        $mockRequest
            ->expects($this->at(0))
            ->method('get')
            ->with('row_id')
            ->will($this->returnValue(1))
        ;
        
        $mockRequest
            ->expects($this->at(1))
            ->method('get')
            ->with('row_position')
            ->will($this->returnValue(11))
        ;
        
        $container = new Container();
        $container->set('request', $mockRequest);
        $container->set('event_dispatcher', $this->getMockEventDispatcher());
        
        $controller = new DataGridController();
        $controller->setContainer($container);
        
        $result = $controller->sortableAction('test');
        
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        
        $this->assertSame(json_encode(array()), $result->getContent());    
    }
    
    public function testMassAction()
    {
         
        $container = new Container();
        $container->set('request', $this->getMockRequestForMassActionTest());
        $container->set('event_dispatcher', $this->getMockEventDispatcher());
        $container->set('thrace_data_grid.provider', $this->getMockProvider());
        $container->set('thrace_data_grid.handler.datagrid', $this->getMockDataGridHandlerForMassActionTest());
    
        $controller = new DataGridController();
        $controller->setContainer($container);
        $result = $controller->massAction('test'); 
    
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        $this->assertSame(json_encode(array()), $result->getContent()); 
    }
    
    public function testMassActionWithInvalidAction()
    {
         
        $mockRequest = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()->getMock();
        
        $mockRequest
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(true))
        ;
        
        $mockRequest
            ->expects($this->once())
            ->method('get')
            ->with('action')
            ->will($this->returnValue(null))
        ;
        
        $container = new Container();
        $container->set('request', $mockRequest);
        $container->set('thrace_data_grid.provider', $this->getMockProvider());

        $controller = new DataGridController();
        $controller->setContainer($container);
        $this->setExpectedException('InvalidArgumentException');
        $controller->massAction('test'); 
 
    }
    
    public function testRowAction()
    {
        $mockRequest = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()->getMock();
        
        $mockRequest
            ->expects($this->at(0))
            ->method('get')
            ->with('oper')
            ->will($this->returnValue('add'))
        ;
        
        $mockRequest
            ->expects($this->at(1))
            ->method('get')
            ->with('id')
            ->will($this->returnValue(1))
        ;
        
        $container = new Container();
        $container->set('request', $mockRequest);
        $container->set('event_dispatcher', $this->getMockEventDispatcher());
        
        $controller = new DataGridController();
        $controller->setContainer($container);
        
        $result = $controller->rowAction('test');
        
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        
        $this->assertSame(
            json_encode(array('errors' => array(), 'success' => false, 'id' => 1, 'data' => array())), 
            $result->getContent()
        );
        
    }
        
    public function testRowActionWithInvalidOperator()
    {
        $mockRequest = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()->getMock();
        
        $mockRequest
            ->expects($this->once())
            ->method('get')
            ->with('oper')
            ->will($this->returnValue('invalid'))
        ;

        
        $container = new Container();
        $container->set('request', $mockRequest);

        
        $controller = new DataGridController();
        $controller->setContainer($container);
        
        $this->setExpectedException('InvalidArgumentException');
        $controller->rowAction('test');
 
    }
        
    protected function getMockRequest()
    {
        $mock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()->getMock();
        
        $mock
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(true))
        ;
        
        $mock
            ->expects($this->at(1))
            ->method('get')
            ->with('page')
            ->will($this->returnValue(1))
        ;
        
        $mock
            ->expects($this->at(2))
            ->method('get')
            ->with('sidx')
            ->will($this->returnValue(false))
        ;
        
        $mock
            ->expects($this->at(3))
            ->method('get')
            ->with('sord')
            ->will($this->returnValue('ASC'))
        ;
        
        $mock
            ->expects($this->at(4))
            ->method('get')
            ->with('rows')
            ->will($this->returnValue(10))
        ;
        
        $mock
            ->expects($this->at(5))
            ->method('get')
            ->with('_search')
            ->will($this->returnValue('false'))
        ;
        
        $mock
            ->expects($this->at(6))
            ->method('get')
            ->with('filters')
            ->will($this->returnValue('{}'))
        ;
        
        $mock
            ->expects($this->at(7))
            ->method('get')
            ->with('masterGridRowId')
            ->will($this->returnValue(0))
        ;
        
     
        return $mock;
    }
        
    protected function getMockRequestForMassActionTest()
    {
        $mock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()->getMock();
        
        $mock
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(true))
        ;
        
        $mock
            ->expects($this->at(1))
            ->method('get')
            ->with('action')
            ->will($this->returnValue('someAction'))
        ;
        
        $mock
            ->expects($this->at(2))
            ->method('get')
            ->with('page')
            ->will($this->returnValue(1))
        ;
        
        $mock
            ->expects($this->at(3))
            ->method('get')
            ->with('sidx')
            ->will($this->returnValue(false))
        ;
        
        $mock
            ->expects($this->at(4))
            ->method('get')
            ->with('sord')
            ->will($this->returnValue('ASC'))
        ;
        
        $mock
            ->expects($this->at(5))
            ->method('get')
            ->with('rows')
            ->will($this->returnValue(10))
        ;
        
        $mock
            ->expects($this->at(6))
            ->method('get')
            ->with('_search')
            ->will($this->returnValue('false'))
        ;
        
        $mock
            ->expects($this->at(7))
            ->method('get')
            ->with('filters')
            ->will($this->returnValue('{}'))
        ;
        
        $mock
            ->expects($this->at(8))
            ->method('get')
            ->with('masterGridRowId')
            ->will($this->returnValue(0))
        ;
        

        $mock
            ->expects($this->at(9))
            ->method('get')
            ->with('ids')
            ->will($this->returnValue(array(1,2,3)))
        ;
        
        
        $mock
            ->expects($this->at(10))
            ->method('get')
            ->with('selectAll')
            ->will($this->returnValue(true))
        ;
       
        return $mock;
    }
    
    protected function getMockProvider()
    {
        $mock = $this->getMock('Thrace\DataGridBundle\DataGrid\Provider\DataGridProviderInterface');
        
        $mock
            ->expects($this->once())
            ->method('has')
            ->will($this->returnValue(true))
        ;
        
        $mock
            ->expects($this->once())
            ->method('get')
            ->with('test')
            ->will($this->returnValue($this->getMock('Thrace\DataGridBundle\DataGrid\DataGridInterface')))
        ;
        
        $mock
            ->expects($this->once())
            ->method('get')
            ->with('test')
            ->will($this->returnValue($this->getMock('Thrace\DataGridBundle\DataGrid\DataGridInterface')))
        ;
        
        
        return $mock;
    }
    
    protected function getMockDataGridHandler()
    {
        $mock = $this->getMock('Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface');
        
        $mock
            ->expects($this->once())
            ->method('setDataGrid')
            ->with($this->getMock('Thrace\DataGridBundle\DataGrid\DataGridInterface'))
            ->will($this->returnSelf())
        ;
        
        $mock
            ->expects($this->once())
            ->method('resolveOptions')
            ->withAnyParameters()
            ->will($this->returnSelf())
        ;
        
        $mock
            ->expects($this->once())
            ->method('buildQuery')
            ->will($this->returnSelf())
        ;
        
        $mock
            ->expects($this->once())
            ->method('buildData')
            ->will($this->returnSelf())
        ;
        
        $mock
            ->expects($this->once())
            ->method('getProcessedData')
            ->will($this->returnValue(array('key' => 'processedData')))
        ;
        
        return $mock;
    }
    
    protected function getMockDataGridHandlerForMassActionTest()
    {
        $mock = $this->getMock('Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface');
        
        $mock
            ->expects($this->once())
            ->method('setDataGrid')
            ->with($this->getMock('Thrace\DataGridBundle\DataGrid\DataGridInterface'))
            ->will($this->returnSelf())
        ;
        
        $mock
            ->expects($this->once())
            ->method('resolveOptions')
            ->withAnyParameters()
            ->will($this->returnSelf())
        ;
        
        $mock
            ->expects($this->once())
            ->method('buildQuery')
            ->will($this->returnSelf())
        ;
        
        return $mock;
    }
    
    protected function getMockEventDispatcher()
    {
        $mock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        
        $mock
            ->expects($this->once())
            ->method('dispatch')
            ->withAnyParameters()
        ;
        
        return $mock;
    }
}