Row editing
===========

ThraceDataGridBundle provides two ways to add/edit/delete a row.

## First way) Inline editing. 

You are able to perform rows manipulation from the datagrid. Let's create one.

### Step 1) Create an entity.

``` php
<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Product
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
     * @ORM\Column(type="string", name="name", length=255, nullable=true, unique=false)
     */
    protected $name;
    
    /**
     * @var decimal
     *
     * @ORM\Column(type="decimal", name="price", scale=2, precision=10)
     */
    protected $price;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setPrice($price)
    {
        $this->price = $price;
    }
    
    public function getPrice()
    {
        return $this->price;
    }
    
}
```

**Note:** Update your database schema running the following command:

``` bash
$ php app/console doctrine:schema:update --force
```

### Step 2) Create the datagrid associated with the entity above.

``` php
<?php
namespace AppBundle\DataGrid;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Translation\TranslatorInterface;

use Thrace\DataGridBundle\DataGrid\DataGridFactoryInterface;

class ProductManagementBuilder
{

    const IDENTIFIER = 'product_management';
    
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
                $this->translator->trans('column.name'), 
                $this->translator->trans('column.price'),  
            ))
            ->setColModel(array(
                array(
                    'name' => 'name', 'index' => 'p.name', 'width' => 200,
                    'align' => 'left', 'sortable' => true, 'search' => true,
                    'editable' => true, 'editrules' => array('required' => true)
                ), 
                array(
                    'name' => 'price', 'index' => 'p.price', 'width' => 200,
                    'align' => 'left', 'sortable' => true, 'search' => true, 
                    'formatter' => 'currency', 'editable' => true, 'editrules' => array('required' => true)
                ),
            ))
            ->setQueryBuilder($this->getQueryBuilder())
            ->enableSearchButton(true)
            ->enableAddButton(true)
            ->enableEditButton(true)
            ->enableDeleteButton(true)
        ;

        return $dataGrid;
    }


    protected function getQueryBuilder()
    {
        $qb = $this->em->getRepository('AppBundle:Product')->createQueryBuilder('p');
        $qb
            ->select('p.id, p.name, p.price, p')
        ;
        
        return $qb;
    }
}
```

**Note:** In *DataGridInterface::setColModel* method we added *'editable' => true*. This sets a row as editable. There are more options for editing. 
To read complete documentation click [here](http://www.trirand.com/jqgridwiki/doku.php?id=wiki:colmodel_options).

**Note:** We enabled add/edit/delete buttons by setting *DataGridInterface::enableAddButton(true)*, *DataGridInterface::enableEditButton(true)* and *DataGridInterface::enableDeleteButton(true)*

### Step 3) Create the RowListener.

``` php
<?php
namespace AppBundle\DataGrid\EventListener;

use Doctrine\ORM\EntityNotFoundException;

use AppBundle\Entity\Product;

use Symfony\Component\DependencyInjection\ContainerAware;

use AppBundle\DataGrid\ProductManagementBuilder;

use Thrace\DataGridBundle\Event\RowEvent;

class ProductManagementRowListener extends ContainerAware
{

    public function onRowAdd(RowEvent $event)
    {   
        if ($event->getName() !== ProductManagementBuilder::IDENTIFIER){
            return;
        }
        
        $product = new Product();
        $product->setName($this->container->get('request')->request->get('name'));
        $product->setPrice($this->container->get('request')->request->get('price'));
        
        $this->process($product, $event);
    }
    
    public function onRowEdit(RowEvent $event)
    {
        if ($event->getName() !== ProductManagementBuilder::IDENTIFIER){
            return;
        }
        
        $repo = $this->container->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Product');
        
        $product = $repo->findOneById($event->getId());
        
        if (!$product){
            throw new EntityNotFoundException();
        }
        
        $product->setName($this->container->get('request')->request->get('name'));
        $product->setPrice($this->container->get('request')->request->get('price'));
        
        $this->process($product, $event);
        
    }
    
    public function onRowDelete(RowEvent $event)
    {
        if ($event->getName() !== ProductManagementBuilder::IDENTIFIER){
            return;
        }
        
        $repo = $this->container->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Product');
        
        $product = $repo->findOneById($event->getId());
        
        if (!$product){
            throw new EntityNotFoundException();
        }
        
        $this->container->get('doctrine.orm.entity_manager')->remove($product);
        $this->container->get('doctrine.orm.entity_manager')->flush();
        
        $event->setSuccess(true);
        
    }
    
    protected function process(Product $product, RowEvent $event)
    {
        $errors = $this->container->get('validator')->validate($product, array('default'));
        
        if ($errors->count() > 0){
            $event->setErrors($this->errorsToArray($errors));
            $event->setSuccess(false);
        
        } else {
            $this->container->get('doctrine.orm.entity_manager')->persist($product);
            $this->container->get('doctrine.orm.entity_manager')->flush();
            $event->setSuccess(true);
        }
    }
    
    protected function errorsToArray($errors)
    {
        $data = array();
        foreach ($errors as $error) {
            $data[] = $error->getMessage();
        }
        return $data;
    }

}

```

Register the listener:

``` xml
<service id="app.datagrid.event_listener.product_management_row" 
	class="%app.datagrid.event_listener.product_management_row.class%"
>
	<call method="setContainer">
		<argument type="service" id="service_container" />
	</call>
	
	<tag name="kernel.event_listener" event="thrace_datagrid.onRowAdd" method="onRowAdd" />
	<tag name="kernel.event_listener" event="thrace_datagrid.onRowEdit" method="onRowEdit" />
	<tag name="kernel.event_listener" event="thrace_datagrid.onRowDel" method="onRowDelete" />
</service>
```

The row listener acts as a *Controller*. The *RowEvent* provides you with the following data:
  - name (identifier of the datagrid)
  - id (ID of the row)

**Note:** If operation is success *RowEvent::setSuccess* must be set to *true*

**Note:** If operation is failed then you may send error messages back to the client by setting *RowEvent::setErrors*.

Validation can be performed on the client side and server side (recommended).

After operation is completed datagrid refreshes.

## Second way) External editing.

When you click on *add/edit/delete* buttons you will be redirected to another page. 

### Step 1) Create a route.

``` xml
<!-- Resources/config/routing.xml -->

<route id="app.product.update" pattern="/admin/product/edit/{id}">
	<default key="_controller">AppBundle:Backend\Product:update</default>
	<requirement key="_method">GET|POST</requirement>
</route>
```

### Step 2) Modify Product datagrid.

``` php
<?php
namespace AppBundle\DataGrid;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Translation\TranslatorInterface;

use Thrace\DataGridBundle\DataGrid\DataGridFactoryInterface;

class ProductManagementBuilder
{

    // .....

    public function build ()
    {
        
        $dataGrid = $this->factory->createDataGrid(self::IDENTIFIER);
        $dataGrid
			// .....

            ->setAddBtnUri($this->router->generate('app.product.add', array(), true))
            ->setEditBtnUri($this->router->generate('app.product.edit', array('id' => '{id}'), true))
            ->setDeleteBtnUri($this->router->generate('app.product.delete', array('id' => '{id}'), true))

			// .....
        ;

        return $dataGrid;
    }

// .....

}
```

**Note:** *{id}* is replaced by internal id of jqgrid but if you want to use another property(s) then use *{propertyName}*.
It takes *name* value as key from *colModel* and replace it with row value corresponding to the entity property.
You can replace as many properties as you want.

[Back to home](index.md)







