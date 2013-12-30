<?php
namespace Thrace\DataGridBundle\Tests\DataGrid\DataGridFactoryTest;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

class DataGridFactoryTest extends BaseTestCase
{

    public function testCreateDataGrid()
    {
        $factory = new \Thrace\DataGridBundle\DataGrid\DataGridFactory();
        $dataGrid = $factory->createDataGrid('test');
        
        $this->assertInstanceOf('\Thrace\DataGridBundle\DataGrid\DataGridInterface', $dataGrid);
        $this->assertSame('test', $dataGrid->getName());

    }   
}
