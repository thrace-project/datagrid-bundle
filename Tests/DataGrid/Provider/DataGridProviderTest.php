<?php
namespace Thrace\DataGridBundle\Tests\DataGrid\Provider;

use Thrace\DataGridBundle\DataGrid\Provider\DataGridProvider;

class DataGridProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testHas()
    {
        $provider = new DataGridProvider(
            $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'), 
            array('first' => 'first', 'second' => 'dummy')
        );
        
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }
    
    public function testGetExistentDataGrid()
    {
        $dataGrid = $this->getMock('Thrace\DataGridBundle\DataGrid\DataGridInterface');
        
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->once())
            ->method('get')
            ->with('test')
            ->will($this->returnValue($dataGrid));
        
        $provider = new DataGridProvider($container, array('default' => 'test'));
        
        $this->assertSame($dataGrid, $provider->get('default'));
                
    }
    
    /**
     * @expectedException InvalidArgumentException 
     */
    public function testGetNonExistentDataGrid()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $provider = $provider = new DataGridProvider($container);
        $provider->get('non-existent');
    }
}
