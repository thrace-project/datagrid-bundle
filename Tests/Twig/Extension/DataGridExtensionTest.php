<?php
namespace Thrace\DataGridBundle\Tests\Twig\Extension;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

use Thrace\DataGridBundle\Twig\Extension\DataGridExtension;

use Symfony\Component\DependencyInjection\Container;

class DataGridExtensionTest extends  BaseTestCase
{
    
    public function testDataGridExtension()
    {
        
        $dataGridMock = 
            $this->getMock('Thrace\DataGridBundle\DataGrid\DataGridInterface');
        
        $templatingMock = 
            $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
                 ->disableOriginalConstructor()->getMock();
        
        $templatingMock
            ->expects($this->once())
            ->method('render')
            ->with('ThraceDataGridBundle:DataGrid:index.html.twig', array(
                'dataGrid' => $dataGridMock, 'translationDomain' => 'messages'
            ))
            ->will($this->returnValue('<table>test</table>'))
        ;
        
        $container = new Container();
        $container->setParameter('thrace_data_grid.translation_domain', 'messages');
        $container->set('templating', $templatingMock);
        
        $this->assertEquals(
            '<table>test</table>', 
            $this->getTemplate('{{ thrace_datagrid(dataGrid) }}', $container)
                ->render(array('dataGrid' => $dataGridMock))
        );
    }
    
    public function testDataGridExtensionWithString()
    {
        
        $dataGridMock = 
            $this->getMock('Thrace\DataGridBundle\DataGrid\DataGridInterface');
        
        $providerMock = $this->getMock('Thrace\DataGridBundle\DataGrid\Provider\DataGridProviderInterface');
        $providerMock
            ->expects($this->once())
            ->method('get')
            ->with('myDataGrid')
            ->will($this->returnValue($dataGridMock))
        ;
        
        $templatingMock = 
            $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
                 ->disableOriginalConstructor()->getMock();
        
        $templatingMock
            ->expects($this->once())
            ->method('render')
            ->with('ThraceDataGridBundle:DataGrid:index.html.twig', array(
                'dataGrid' => $dataGridMock, 'translationDomain' => 'messages'
            ))
            ->will($this->returnValue('<table>test</table>'))
        ;
        
        $container = new Container();
        $container->setParameter('thrace_data_grid.translation_domain', 'messages');
        $container->set('templating', $templatingMock);
        $container->set('thrace_data_grid.provider', $providerMock);
        
        $this->assertEquals(
            '<table>test</table>', 
            $this->getTemplate("{{ thrace_datagrid('myDataGrid') }}", $container)
                ->render(array())
        );
    }
    
    private function getTemplate($template, $container)
    {
        $loader = new \Twig_Loader_Array(array('index' => $template));
        $twig = new \Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $twig->addExtension(new DataGridExtension($container));
    
        return $twig->loadTemplate('index');
    }
}