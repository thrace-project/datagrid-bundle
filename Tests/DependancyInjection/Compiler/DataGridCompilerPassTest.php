<?php
namespace Thrace\DataGridBundle\Tests\DependancyInjection\Compiler;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

use Thrace\DataGridBundle\DependencyInjection\Compiler\DataGridCompilerPass;

class DataGridCompilerPassTest extends BaseTestCase
{
 
    public function testProcessWithoutProviderDefinition()
    {
        $dataGridPass = new DataGridCompilerPass();
        
        $this->assertNull(
            $dataGridPass->process($this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder'))
        );
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testProcessWithEmptyAlias()
    {
        $containerBuilderMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilderMock
            ->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('thrace_data_grid.datagrid'))
            ->will($this->returnValue(array('id' => array('tag1' => array('alias' => '')))));
    
        $dataGridPass = new DataGridCompilerPass();
        $dataGridPass->process($containerBuilderMock);
    }
    
    public function testProcessWithAlias()
    {
        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        
        $definitionMock->expects($this->once())
            ->method('replaceArgument')
            ->with($this->equalTo(1), $this->equalTo(array('test_alias' => 'id')));
    
        $containerBuilderMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilderMock
            ->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        
        $containerBuilderMock
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('thrace_data_grid.datagrid'))
            ->will($this->returnValue(array('id' => array('tag1' => array('alias' => 'test_alias')))));
        
        $containerBuilderMock
            ->expects($this->once())
            ->method('getDefinition')
            ->with($this->equalTo('thrace_data_grid.provider'))
            ->will($this->returnValue($definitionMock));
    
        $dataGridPass = new DataGridCompilerPass();
        $dataGridPass->process($containerBuilderMock);
    }
}