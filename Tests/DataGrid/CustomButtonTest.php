<?php
namespace Thrace\DataGridBundle\Tests\DataGrid\DataGridFactoryTest;

use Thrace\DataGridBundle\DataGrid\CustomButton;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

class CustomButtonTest extends BaseTestCase
{

    public function testDefault()
    {
        $button = new CustomButton('b1', array(
            'title' => 'title',
            'caption' => 'caption',
            'buttonIcon' => 'icon',
            'position' => 'first',
            'uri' => 'some'       
        ));
        $buttonOptions = $button->getOptions();
        
        $this->assertSame('b1', $button->getName());
        $this->assertSame(array(
            'title' => 'title',
            'caption' => 'caption',
            'buttonicon' => 'icon',
            'position' => 'first',
            'uri' => 'some'    
        ), $buttonOptions);
    }   
    
    public function testInvalidOption()
    {
        $this->setExpectedException('InvalidArgumentException');
        $button = new CustomButton('b1', array('invalidOption' => 'xxx'));     
    }
    
    public function testInvalidPositionOption()
    {
        $this->setExpectedException('InvalidArgumentException');
        $button = new CustomButton('b1', array('position' => 'xxx'));
    }
}
