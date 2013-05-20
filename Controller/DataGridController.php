<?php
/*
 * This file is part of NeutronDataGridBundle
 *
 * (c) Nikolay Georgiev <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\DataGridBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\DependencyInjection\ContainerAware;

use Thrace\DataGridBundle\Event\QueryEvent;

use Thrace\DataGridBundle\Event\DataEvent;

use Thrace\DataGridBundle\Event\RowPositionChangeEvent;

use Thrace\DataGridBundle\Event\MassActionEvent;

use Thrace\DataGridBundle\Event\RowEvent;

use Thrace\DataGridBundle\DataGridEvents;

use Thrace\DataGridBundle\DataGrid\DataGridInterface;

/**
 * This controller handles ajax requests for managing datagrid
 *
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 */
class DataGridController extends ContainerAware
{
    
    /**
     * @param string $name
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function dataAction ($name)
    {
        $dataGrid = $this->getDataGrid($name);
        $handler = $this->container->get('thrace_data_grid.handler.datagrid');

        $handler
            ->setDataGrid($dataGrid)
            ->resolveOptions($this->getRequestParameters())
            ->buildQuery()
            ->buildData()
        ;
               
        return new JsonResponse($handler->getProcessedData());
    }

    /**
     * This action dispatch onRowPositionChange
     * 
     * @param string $name
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sortableAction ($name)
    {
        $event = new RowPositionChangeEvent(
            $name, $this->getRequest()->get('row_id'), 
            $this->getRequest()->get('row_position')
        );
        
        $this->container->get('event_dispatcher')->dispatch(DataGridEvents::onRowPositionChange, $event);
        
        return new JsonResponse($event->getExtraData());
    }

    /**
     * This action handles mass actions
     * 
     * @param string $name
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function massAction ($name)
    {          
        $dataGrid = $this->getDataGrid($name);
        
        $action = $this->getRequest()->get('action');
        
        if (!$action){
            throw new \InvalidArgumentException('Parameter "action" in not valid'); 
        }
        
        $handler = $this->container->get('thrace_data_grid.handler.datagrid');
        $handler
            ->setDataGrid($dataGrid)
            ->resolveOptions($this->getRequestParameters())
            ->buildQuery()
        ;
        
        $ids = $this->getRequest()->get('ids', array());
        $selectAll = ($this->getRequest()->get('selectAll') === 'true');

        $event = new MassActionEvent($name, $action, $ids, $selectAll, $handler->getQuery());
        $this->container->get('event_dispatcher')->dispatch(DataGridEvents::onMassAction, $event);
        
        return new JsonResponse($event->getExtraData());
    }

    /**
     * This action handles manipulating of record
     * 
     * @param string $name
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rowAction ($name)
    {
        $oper = $this->getRequest()->get('oper', false);
        if (!in_array($oper, array('add', 'edit', 'del'))){
            throw new \InvalidArgumentException(sprintf('Event type: %s is not valid', $oper));
        }
        
        $eventType = 'thrace_datagrid.onRow' . ucfirst($oper);
        
        $event = new RowEvent($name, $this->getRequest()->get('id'));

        $this->container->get('event_dispatcher')->dispatch($eventType, $event);
        
        return new JsonResponse(array(
            'errors'  => $event->getErrors(), 
            'success' => $event->getSuccess(), 
            'id'      => $event->getId(),
            'data'    => $event->getData()
        ));
    }

    /**
     * This method retrieves datagrid by name
     * 
     * @param string $name
     * @return \Thrace\DataGridBundle\DataGrid\DataGridInterface
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getDataGrid ($name)
    {
        if (! $this->getRequest()->isXmlHttpRequest()) {
            throw new \RuntimeException('Request must be XmlHttpRequest');
        }
        
        $provider = $this->container->get('thrace_data_grid.provider');
        
        if (!$provider->has($name)) {
            throw new \InvalidArgumentException(sprintf('The datagrid %s is not defined', $name));
        }
        
        return $provider->get($name);
    }
    
    /**
     *  Http request
     *  
     * @return Request
     */
    public function getRequest()
    {
        return $this->container->get('request');
    }
    
    /**
     * Gets requested parameters
     * 
     * @return array
     */
    public function getRequestParameters()
    {
        // $page current page of the grid
        $page = (int) $this->getRequest()->get('page', 0);
        
        // $orderBy column name to sort datagrid records
        $orderBy = $this->getRequest()->get('sidx', false);
        
        // ASC | DESC $sort
        $sort = strtoupper($this->getRequest()->get('sord', 'asc'));
        
        // $records  number of records to display
        $records = (int) $this->getRequest()->get('rows', 0);
        
        // $search is search in the query
        $search = ($this->getRequest()->get('_search', false) === 'true');
 
        // $filters Filters from the search query
        $filters = (array) json_decode($this->getRequest()->get('filters', '{}'));
        
        // $masterRowId Id of the master grid record
        $masterGridRowId = (int) $this->getRequest()->get('masterGridRowId', 0);
        
        $parameters = array(
            'page'            => $page,
            'orderBy'         => $orderBy,
            'sort'            => $sort,
            'records'         => $records,
            'search'          => $search,
            'filters'         => $filters,
            'masterGridRowId' => $masterGridRowId
        );
        
        return $parameters;
    }
}
