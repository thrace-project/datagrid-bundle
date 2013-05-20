Using ThraceDataGridBundle
===========================
<a name="top"></a>
ThraceDataGridBundle adds supports for building RIA datagrids with jqgrid without writing a single line of javascript code!

**Basic Docs**

* [Installation](#installation)
* [Your first datagrid](#first-datagrid)
* [Retrieving datagrid by alias key](#retrieving-datagrid)
* [Rendering datagrid](#rendering-datagrid)
* [Using jqGrid query builder](#jqgrid-query-builder)

**More Advanced Stuff**

* [Custom buttons](custom_buttons.md)
* [Mass actions](mass_actions.md)
* [Dependent datagrids](dependent_grids.md)
* [Sortable rows](sortable_rows.md)
* [Row editing](row_editing.md)
* [Security](security.md)


<a name="installation"></a>

## Installation

### Step 1) Get the bundle

First, grab the  ThraceDataGridBundle using composer (symfony 2.1 pattern)

Add on composer.json (see http://getcomposer.org/)

    "require" :  {
        // ...
        "thrace/datagrid-bundle":"dev-master",
    }

### Step 2) Register the bundle

To start using the bundle, register it in your Kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Thrace\DataGridBundle\ThraceDataGridBundle(),
    );
    // ...
}
```
### Step 3) Register the bundle routes

``` yaml
# app/config/routing.yaml

thrace_data_grid:
    resource: "@ThraceDataGridBundle/Resources/config/routing.xml"
    prefix:   / 
```

### Step 4) Download jQuery, jQueryUI and jqgrid plugin

You need to download latest version of [jQuery](http://jquery.com/), [jqueryui](http://jqueryui.com/) 
	and [jqgrid plugin](http://www.trirand.com/blog/?page_id=6) then put the sources somewhere in the web folder.

### Step 5) (optional) Configure the bundle

The bundle comes with a very simple configuration, which is listed below.
If you skip this step, these defaults will be used.

```yaml
# app/config/config.yml
thrace_data_grid:
	translation_domain: ThraceDataGridBundle
```

**Note:** If you wish to use default texts provided in this bundle, you have to make sure you have translator enabled in your config.

``` yaml
# app/config/config.yml

framework:
    translator: { fallback: %locale% }
```

<a name="first-datagrid"></a>

## Create your first datagrid!

### Step 1) Create entity *User* which you will associate with the datagrid and another entity *Order*.
The association between them is one-to-many bidirectional.

```php
<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class User
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
     * @ORM\Column(type="string", length=255, nullable=true, unique=false)
     */
    protected $firstName;
    
    /**
     * @var string 
     *
     * @ORM\Column(type="string", length=255, nullable=true, unique=false)
     */
    protected $lastName;
    
    /**
     * @var boolean 
     *
     * @ORM\Column(type="boolean")
     */
    protected $enabled = false;
    
    /**
     * @ORM\OneToMany(targetEntity="Order",  mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */  
    protected $orders; 
    
    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }
    
    public function getFistName()
    {
        return $this->firstName;
    }
    
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
    
    public function getLastName()
    {
        return $this->lastName;
    }
    
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
    
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    public function addOrder(Order $order)
    {
        if (!$this->orders->contains($order)){
            $this->orders[] = $order;
            $order->setUser($this);
        }
        
        return $this; 
        
    }
    
    public function getOrders()
    {
        return $this->orders;
    }
    
    public function removeOrder(Order $order)
    {
        if ($this->orders->contains($order)){
            $this->orders->remove($order);
            $order->setUser(null);
        }
        
        return $this;
    }

}
```

```php
<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="product_order")
 * @ORM\Entity
 */
class Order
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
     * @ORM\Column(type="string", name="order_identifier", length=255, nullable=true, unique=true)
     */
    protected $identifier;
    
    /**
     * @var decimal
     *
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $total;
    
    /**
     * @ORM\ManyToOne(targetEntity="User",  inversedBy="orders")
     */   
    protected $user;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }
    
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    public function setTotal($total)
    {
        $this->total = $total;
    }
    
    public function getTotal()
    {
        return $this->total;
    }
    
    public function setUser(User $user = null)
    {
        $this->user = $user;
    }
    
    public function getUser()
    {
        return $this->user;
    }
}
```


**Note:** Update your database schema running the following command:

``` bash
$ php app/console doctrine:schema:update --force
```

### Step 2) Create datagrid associated with the entity above.

```php
<?php
namespace AppBundle\DataGrid;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Translation\TranslatorInterface;

use Thrace\DataGridBundle\DataGrid\DataGridFactoryInterface;

class UserManagementBuilder
{

    const IDENTIFIER = 'user_management';
    
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
            ->setCaption($this->translator->trans('user_management_datagrid.caption'))
            ->setColNames(array(
                $this->translator->trans('column.firstName'), 
                $this->translator->trans('column.lastName'), 
                $this->translator->trans('column.grandTotal'), 
                $this->translator->trans('column.enabled'), 
            ))
            ->setColModel(array(
                array(
                    'name' => 'firstName', 'index' => 'u.firstName', 'width' => 200,
                    'align' => 'left', 'sortable' => true, 'search' => true,
                ), 
                array(
                    'name' => 'lastName', 'index' => 'u.lastName', 'width' => 200,
                    'align' => 'left', 'sortable' => true, 'search' => true,
                ),
                array(
                    'name' => 'total', 'index' => 'total', 'width' => 200, 'aggregated' => true,
                    'align' => 'left', 'sortable' => true, 'search' => true, 
                    'formatter' => 'currency',
				),
				array(
					'name' => 'enabled', 'index' => 'u.enabled', 'width' => 30,
					'align' => 'left', 'sortable' => true, 'search' => true,
					'formatter' => 'checkbox',  'search' => true, 'stype' => 'select',
					'searchoptions' => array(
						'value' => array(
							1 => 'enable',
							0 => 'disabled',
						)
					)
				),
			))
            ->setQueryBuilder($this->getQueryBuilder())
            ->enableSearchButton(true)
        ;

        return $dataGrid;
    }


    protected function getQueryBuilder()
    {
        $qb = $this->em->getRepository('AppBundle:User')->createQueryBuilder('u');
        $qb
            ->select('u.id, u.firstName, u.lastName, SUM(o.total) as total, u.enabled, u')
            ->leftJoin('u.orders', 'o')
            ->groupBy('u.id')
        ;
        
        return $qb;
    }
}

```

**Important:** In the select clause of query builder method  you must include *id* field at the beginning and the *root alias* at the end.
*Id* is needed for jqgrid for internal identifier. If your identifier column is named differently then use alias *u.identifier as id* .
*Root alias* is needed for *Doctrine\ORM\Tools\Pagination\Paginator* to count the records. If not set it will throw an exception: *Not all identifier properties can be found in the ResultSetMapping: id*
If you use aggregate columns then you must add the following option in your colModel *'aggregated' => true* .It will tell the query builder to use *addHaving/orHaving* instead of *addWhere/orWhere*. 

**Important:** When setting *colModels* with *DataGridInterface::setColModel()* method you need to provide minimum options *array('name' => 'firstName', 'index' => u.firstName)*. 
*Index* option is needed to build the query. *Name* option is needed to build the result.

**Important:** You can use inner and left joins to build a query.

**Note:** All options are converted to json objects and passed to jqgrid plugin.
	     
### Step 3) Register datagrid in the service container

All datagrids are created via factory service.

``` xml
<service id="app.datagrid.builder.user_management" class="AppBundle\DataGrid\UserManagementBuilder" >
	<argument type="service" id="thrace_data_grid.factory.datagrid" />
	<argument type="service" id="translator" />
	<argument type="service" id="router" />
	<argument type="service" id="doctrine.orm.entity_manager" />
</service>

<service id="app.datagrid.user_management" class="Thrace\DataGridBundle\DataGrid\DataGrid"
	factory-service="app.datagrid.builder.user_management"
	factory-method="build">
	<tag name="thrace_data_grid.datagrid" alias="user_management" />
</service>
```

**Note:**  You have to set unique alias for each datagrid otherwise it will throw an exception.

<a name="retrieving-datagrid"></a>

## Retrieving datagrid by alias key

In order to render the datagrid you will have to get it from the service container. You are able to do this using datagrid provider service *thrace_data_grid.provider* .

The following example demonstrates how to get a datagrid in a controller method and pass it to the view:

``` php
<?php
namespace AppBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction()
    {    
   	    /** @var \Thrace\DataGridBundle\DataGrid\DataGridInterface */
        $userManagementDataGrid = $this->container->get('thrace_data_grid.provider')->get('user_management');
        
    	return $this->render('AppBundle:Backend\Default:index.html.twig',array(
    	    'userManagementDataGrid' => $userManagementDataGrid,
        ));
    }
}
```

<a name="rendering-datagrid"></a>

## Rendering datagrid

There is a build-in twig extension which helps you to render the datagrid. The following example shows you how to render a datagrid in twig template.

### Step 1) Let's include necessary css files:

``` jinja
{% block stylesheets %}
            
	{% stylesheets
		'jquery/css/smoothness/jquery-ui.css' 
        'jquery/plugins/jqgrid/css/ui.jqgrid.css' 
        filter='cssrewrite'
	%}
		<link rel="stylesheet" href="{{ asset_url }}" />
	{% endstylesheets %}

{% endblock %}
```
**Note:** See path to css files. It has to point to the files previously downloaded to your web root. 
See [installation](#installation)

### Step 2) Let's include necessary javascript files:

``` jinja
{% block javascripts %}

	{% javascripts
		'jquery/js/jquery.js'
        'jquery/js/jquery-ui.js'
        'jquery/i18n/jquery-ui-i18n.js'
        'jquery/plugins/jqgrid/js/i18n/grid.locale-en.js'
        'jquery/plugins/jqgrid/js/jquery.jqGrid.src.js'
        'bundles/thracedatagrid/js/init-datagrid.js'
    %}
		<script src="{{ asset_url }}"></script>
	{% endjavascripts %}

{% endblock %}
```

### Step 3) Install bundle assets running the following command:

**Note:** You have to include *'bundles/thracedatagrid/js/init-datagrid.js'*. This script initiates all datagrids in the template.

``` bash
$ php app/console assets:install --symlink web
```

**Note:** See path to javascript files. It has to point to the files previously downloaded to your web root. 
See [installation](#installation)

### Step 4) Render the datagrid with *thrace_datagrid* twig extension.

Parameter *userManagementDataGrid* must be an instance of *DataGridInterface*

``` jinja
<div>{{ thrace_datagrid(userManagementDataGrid) }}</div>
```

or just provide datagrid alias. This way is more flexible because there is no need to get the datagrid from the provider.

``` jinja
<div>{{ thrace_datagrid('user_management') }}</div>
```

<a name="jqgrid-query-builder"></a>

### Using jqGrid query buider

To enable searching use *DataGridInterface::enableSearchButton(true)*

To set search operators use *DataGridInterface::setSearchOptions*. By default these operators are enabled *array('eq', 'ne', 'lt', 'le', 'gt', 'ge', 'bw', 'bn', 'ew', 'en', 'cn', 'nc')* 

**Important:** If you search on aggregate columns you should know that supported operators are *array('eq', 'ne', 'lt', 'le', 'gt', 'ge')* the rest are ignored.

**Important:** *MultipleGroup search* is not supported for now. 

**Note:** Load some data to your database and you will see datagrid in action.

That's it. You have created your first datagrid. For more documentation go to [jqgrid documentation](http://www.trirand.com/jqgridwiki/doku.php?id=wiki:jqgriddocs)
For more configurations see: [DataGridInterface](../DataGrid/DataGridInterface.php)

[back to top](#top)