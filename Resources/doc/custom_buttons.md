Custom buttons
==============

Custom Buttons are a way to define your own button and action in the pager. 
You have to selecta row in order to redirect to another page!

***Usage:***

```php
<?php
namespace AppBundle\DataGrid;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Translation\TranslatorInterface;

use Thrace\DataGridBundle\DataGrid\DataGridFactoryInterface;

use Thrace\DataGridBundle\DataGrid\CustomButton;

class UserManagementBuilder
{

	// .....

    public function build ()
    {
        
        $dataGrid = $this->factory->createDataGrid(self::IDENTIFIER);
        $dataGrid
			// .....
            ->addCustomButton(new CustomButton('unique_name', array(
                'title' => 'button title',
                'caption' => 'button label',
                'buttonIcon' => 'ui-icon-info',
                'position' => 'last',
                'uri' => 'http://example.com/{id}'
            )))
        ;

        return $dataGrid;
    }
    
    // .....

}
```

A button will be appended in the datagrid pager.

**Notice:** You can use placeholders like *{id}* to generate the proper uri.

There are additional methods for futher manipulations: *DataGridInterface::hasCustomButtom*, *DataGridInterface::getCustomButtom*,
*DataGridInterface::getCustomButtoms*, *DataGridInterface::setCustomButtoms*

[For more information go to jqgrid documentation](http://www.trirand.com/jqgridwiki/doku.php?id=wiki:custom_buttons)

[back to home](index.md)
