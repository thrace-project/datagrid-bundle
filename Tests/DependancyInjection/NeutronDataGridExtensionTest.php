<?php
namespace Thrace\DataGridBundle\Tests\DependancyInjection;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

use Thrace\DataGridBundle\DependencyInjection\ThraceDataGridExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ThraceDataGridExtensionTest extends BaseTestCase
{
    public function testDefault ()
    {
        $container = new ContainerBuilder();
        $loader = new ThraceDataGridExtension();
        $loader->load(array(array()), $container);
        
        $this->assertTrue($container->hasDefinition('thrace_data_grid.provider'));
        
        $this->assertTrue($container->hasDefinition('thrace_data_grid.factory.datagrid'));
        
        $this->assertTrue($container->hasDefinition('thrace_data_grid.doctrine.orm.handler.datagrid'));
        
        $this->assertTrue($container->hasAlias('thrace_data_grid.handler.datagrid'));  
        
        $this->assertTrue($container->hasParameter('thrace_data_grid.translation_domain'));
                
        $this->assertTrue($container->hasDefinition('thrace_data_grid.twig.extension.datagrid'));
        
        $this->assertTrue($container->getDefinition('thrace_data_grid.twig.extension.datagrid')
             ->hasTag('twig.extension')
        );
    
    }
}