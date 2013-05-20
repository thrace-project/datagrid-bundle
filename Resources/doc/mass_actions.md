ThraceDataGrid Mass Actions
============================
This bundle gives you a convenient way to create mass actions.
You can manipulate large amount of data at once. 
For this example below we are using *User* entity and *User* datagrid.

**Deployment**

* [Create a mass action listener](#create-mass-action-listener)
* [Register mass action listener](#register-mass-action-listener)
* [Enable mass actions in datagrid](#enable-mass-action-listener)
* [Translations of mass action buttons](#translations-mass-action-buttons)
* [jQuery mass action event](#jQuery-mass-action-event)

<a name="create-mass-action-listener"></a>

## Step 1) Create a mass action listener:

``` php
<?php
namespace AppBundle\DataGrid\EventListener;

use Doctrine\ORM\Query;

use Thrace\DataGridBundle\Event\MassActionEvent;

use AppBundle\DataGrid\UserManagementBuilder;

use Doctrine\ORM\EntityManager;

class UserManagementMassActionListener
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onMassAction(MassActionEvent $event)
    {
        if (!$event->getName() == UserManagementBuilder::IDENTIFIER){
            return;
        }

        $event->getSelectAll() ? 
            $ids = $this->getIds($event->getQuery()) : $ids = $event->getIds();
        
        $result = 0;

        if (is_callable($event->getMassActionName(), true)){
            $result = call_user_func_array(array($this, $event->getMassActionName()), array('ids' => $ids));
        }
   
        $event->setExtraData(array('num_records_affected' => $result));
    }
    
    private function getIds(Query $query)
    {

        $ids = array();
        $result =  $query->getArrayResult(); 
        array_map(function ($row) use (&$ids) {
            $ids[] = $row['id'];
        }, $result);
        
        return $ids;
    }
    
    private function doMassAction($ids, $enabled)
    {
        if (empty($ids)){
            return false;
        }
        
        $query =
            $this->em->createQuery("UPDATE AppBundle:User u SET u.enabled = :enabled WHERE u.id IN (:ids)");
        
        return $query->execute(array('ids' => $ids, 'enabled' => $enabled));
    }
    
    private function enableUsers(array $ids)
    {
        return $this->doMassAction($ids, true);
    }
    
    private function disableUsers(array $ids)
    {
        return $this->doMassAction($ids, false);
    }


}
```

When event *thrace_datagrid.onMassAction* is dispatched then method onMassAction is called.

We get the event object with the following data:
 - name (datagrid name)
 - massActionName (name of the mass action)
 - ids (ids of selected rows)
 - selectAll (if true all rows are selected including ones from the rest of the pages)
 - query (in this case (Doctrine\ORM\Query) is returned with all search filters applied except for *setMaxResult* and *setFirstResult*)
 
 As you see you can do whatever you want with selected rows.
 
<a name="register-mass-action-listener"></a>

## Step 2) Register a mass action listener:

Mass actions have to be registered in service container. Here is the example:

``` xml
<service id="app.datagrid.event_listener.user_management_mass_action" 
	class="AppBundle\DataGrid\EventListener\UserManagementMassActionListener"
>
	<argument type="service" id="doctrine.orm.entity_manager" />
	<tag name="kernel.event_listener" event="thrace_datagrid.onMassAction" method="onMassAction" />
</service>
```

<a name="enable-mass-action-listener"></a>

## Step 3) Enable a mass action listener in datagrid:

You have to tell your datagrid which mass actions to use:

``` php
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
        // .....
        
		$dataGrid
            ->enableMassActions(true)
            ->setMassActions(array(
                'enableUsers' => 'label.enable_users',        
                'disableUsers' => 'label.disable_users'        
            ))
        ;

        return $dataGrid;
    }
    
    //.....
}
```

<a name="translations-mass-action-buttons"></a>

## Step 4) (Optional) Translations of mass action buttons:

Translations are located in [ThraceDataGridBundle](../translations/ThraceDataGridBundle.en.xlf)

You can add more translation by overwriting the file in *app/Resource* folder or setting the translation domain in the config:

```yaml
# app/config/config.yml
thrace_data_grid:
	translation_domain: YourTranslationDomain
```

<a name="jQuery-mass-action-event"></a>

## Step 5) (Optional) Using jQuery mass action event.

When mass action is executed on the server sends response back to the client and jqgrid refreshes 
then dispatches jQuery event *thrace_datagrid.event.massAction*. The event provides you with the following data:
 - name (name of the grid)
 - action (mass action name)
 - response (response of the server)
 
 If you want to listen to this event: 
 
 ``` javascript
 jQuery('body').bind('thrace_datagrid.event.massAction', function(event){})
 ```
 
 That's all. You see how easy is to create mass actions.
 
 [Back to home page](index.md)

