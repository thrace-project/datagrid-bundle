Sortable rows
=============

In some cases you need to change positions of the rows in the datagrid and persist them to database.

Here is an example how you can do that:

### Step 1) Create entity *Address* .

```php
<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Address
{
    /**
     * @var integer 
     *
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string 
     *
     * @ORM\Column(type="string", name="street", length=255, nullable=true, unique=false)
     */
    protected $street;
    
    /**
     * @var string 
     *
     * @ORM\Column(type="string", name="city", length=255, nullable=true, unique=false)
     */
    protected $city;
    
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="position", length=10, nullable=false, unique=false)
     */
    protected $position;
    
    /**
     * @ORM\Column(name="sortable_category", type="string", length=128, nullable=true)
     */
    private $sortableCategory;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setStreet($street)
    {
        $this->street = $street;
    }
    
    public function getStreet()
    {
        return $this->street;
    }
    
    public function setCity($city)
    {
        $this->city = $city;
    }
    
    public function getCity()
    {
        return $this->city;
    }
    
    public function setPosition($position)
    {
        $this->position = $position;
    }
    
    public function getPosition()
    {
        return $this->position;
    }
    
    public function setSortableCategory($sortableCategory)
    {
        $this->sortableCategory = $sortableCategory;
    }
    
    public function getSortableCategory()
    {
        return $this->sortableCategory;
    }
    
}
```
**Note:** We created columns *position* and *sortableCategory*. We will need them later on.

**Note:** Update your database schema running the following command:

``` bash
$ php app/console doctrine:schema:update --force
```

### Step 2) Create datagrid.

``` php
<?php
namespace AppBundle\DataGrid;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Translation\TranslatorInterface;

use Thrace\DataGridBundle\DataGrid\DataGridFactoryInterface;

class AddressManagementBuilder
{

    const IDENTIFIER = 'address_management';
    
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
            ->setCaption($this->translator->trans('caption'))
            ->setColNames(array(
                $this->translator->trans('column.street'), 
                $this->translator->trans('column.city'), 

            ))
            ->setColModel(array(
                array(
                    'name' => 'street', 'index' => 'a.street', 'width' => 200,
                    'align' => 'left', 'sortable' => false
                ), 
                array(
                    'name' => 'city', 'index' => 'a.city', 'width' => 200,
                    'align' => 'left', 'sortable' => false
                ),

            ))
            ->setQueryBuilder($this->getQueryBuilder())
            ->enableSortable(true)
        ;

        return $dataGrid;
    }


    protected function getQueryBuilder()
    {
        $qb = $this->em->getRepository('AppBundle:Address')->createQueryBuilder('a');
        $qb
            ->select('a.id, a.street, a.city, a')
        ;
        
        return $qb;
    }
}
```

**Note:** We use method *DataGridInterface::enableSortable(true)*. Datagrid rows are sortable now.

### Step 3) Register and render the datagrid. 

For more information how to register and render datagrids click [here](https://github.com/thrace-project/datagrid-bundle/blob/master/Resources/doc/index.md)

When you load datagrid in browser you see that sorting, searching and pager are disabled. We just do not need them because we sort small amount of data otherwise it makes no sense to sort rows manually.

### Step 4) Create *AddressManagementDataGridRowPositionChangeListener* and register it in service container.

``` php
<?php
namespace AppBundle\DataGrid\EventListener;

use AppBundle\DataGrid\AddressManagementBuilder;

use Thrace\DataGridBundle\Event\RowPositionChangeEvent;

class AddressManagementDataGridRowPositionChangeListener
{
    
    public function onRowPositionChange(RowPositionChangeEvent $event)
    {   
        if ($event->getName() !== AddressManagementBuilder::IDENTIFIER){
            return;
        }

        $this->changePosition($event->getRowId(), $event->getRowPosition());
        
        $event->setExtraData(array('key' => 'some extra data'));
    }
    
    protected function changePosition($id, $position)
    {
        //do sorting here
    }

}
```

**Registering in service container**

``` xml
<service id="app.datagrid.event_listener.address_management_row_position_change" 
	class="%app.datagrid.event_listener.address_management_row_position_change.class%"
>
	<tag name="kernel.event_listener" event="thrace_datagrid.onRowPositionChange" method="onRowPositionChange" />
</service>
```

**Theory of operation:** When you sort a row an ajax post request is sent to *DataGridController* and then is dispatched *thrace_datagrid.onRowPositionChange* event.

We catch the event with the lister registered in the example above. We receive datagrid name, id and position of the row.
You have to do the actual sorting by yourself. There is a very good doctrine extension [Sortable](https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/sortable.md).
You may want to set some extra data with the response use *setExtraData()* method.
After response is received by the client then datagrid refreshes and dispatches jQuery event *thrace_datagrid.event.sortable*.
You can listen to this event by registering jQuery event listener:

``` javascript
jQuery('body').bind('thrace_datagrid.event.sortable', function(event){});
```

**Note:** The event has the following properties: name, rowId, rowPosition and response.

[Back to home](index.md)
