Dependent datagrids
===================

If you have one-to-many or many-to-many association between entities then you will need dependent datagrids to display data.
 
Let's assume we have entities *User* and *Order* and association between them is one-to-many bidirectional.
When you click on a *user row* then *Order datagrid* will load all orders belonging to the selected user.
The example uses entities and datagrids created in the previous example [click here](index.md#first-datagrid)

***User datagrid example:***

```php
<?php
namespace AppBundle\DataGrid;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Translation\TranslatorInterface;

use Thrace\DataGridBundle\DataGrid\DataGridFactoryInterface;

class UserManagementBuilder
{

	// .....

    public function build ()
    {
        
        $dataGrid = $this->factory->createDataGrid(self::IDENTIFIER);
        $dataGrid
			// .....
            ->setDependentDataGrids(array('order_management'))
        ;

        return $dataGrid;
    }
    
    // .....

}
```

### Order datagrid example

```php
<?php
namespace AppBundle\DataGrid;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Translation\TranslatorInterface;

use Thrace\DataGridBundle\DataGrid\DataGridFactoryInterface;

class OrderManagementBuilder
{

    const IDENTIFIER = 'order_management';
    
    protected $factory;
    
    protected $translator;
    
    protected $router;
    
    protected $em;


    public function __construct (DataGridFactoryInterface $factory, TranslatorInterface $translator, RouterInterface $router, 
             EntityManager $em)
    {
        $this->factory = $factory;
        $this->translator = $translator;
        $this->router = $router;
        $this->em = $em;
    }

    public function build ()
    {
        
        $dataGrid = $this->factory->createDataGrid(self::IDENTIFIER);
        $dataGrid
            ->setCaption($this->translator->trans('order_management_datagrid.caption'))
            ->setColNames(array(
                $this->translator->trans('column.identifier'), 
                $this->translator->trans('column.total'), 

            ))
            ->setColModel(array(
                array(
                    'name' => 'identifier', 'index' => 'o.identifier', 'width' => 200,
                    'align' => 'left', 'sortable' => true, 'search' => true,
                ), 
                array(
                    'name' => 'total', 'index' => 'o.total', 'width' => 200,
                    'align' => 'left', 'sortable' => true, 'search' => true, 
                    'formatter' => 'currency'
                ),

            ))
            ->setQueryBuilder($this->getQueryBuilder())
            ->enableSearchButton(true)
            ->setAsDependentGrid(true)
        ;

        return $dataGrid;
    }


    protected function getQueryBuilder()
    {
        $qb = $this->em->getRepository('AppBundle:Order')->createQueryBuilder('o');
        $qb
            ->select('o.id, o.identifier, o.total, o')
            ->join('o.user', 'u')
            ->where('u.id = :masterGridRowId')
            ->groupBy('o.id')
        ;
        
        return $qb;
    }
}
```

**Note:** You have to register the datagrid. For more information click [here](index.md#first-datagrid)

In the master datagrid we set dependent datagrids with method *DataGridInterface::setDependentDataGrids(array('name of the grid'))*. You can set multiple datagrids.

In the dependent datagrid we use method *DataGridInterface::setAsDependentGrid(true)*. A parameter *:masterGridRowId* is added to query builder.

**Important:** Do not forget to select *id* in the beginning and the *root alias* in the end of select statement.

**Tip:** If you have many dependent datagrids on one page then enable these options *DataGridInterface::setHideGrid(true)* and *DataGridInterface::setHiddenGrid(true)*.
Now all datagrids will be displayed closed.

Only things left are to render both grids in the twig template for more information how to do it click [here](https://github.com/thrace-project/datagrid-bundle/blob/master/Resources/doc/index.md#rendering-datagrid).

You see how easy is to create dependent datagrids.

[Back to home page](index.md)


 