<?php
/*
 * This file is part of ThraceDataGridBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\DataGridBundle\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Implementation of DataGridHandlerInterface   
 *
 * Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
abstract class AbstractHandler  
{

    protected $eventDispatcher;
    
    protected $queryBuilder;
    
    /**
     * @var Object
     */
    protected $query;
    
    protected $result;
    
    protected $data;
    
    protected $count;
    
    protected $parameters;
    
    protected $locked = false;
    
    public function setEventDispatcher(\Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }

    public function addEventListener($eventName, $listener, $priority = 0)
    {
        if ($this->locked) {
            throw new \BadMethodCallException('EventListeners can be added before handle method is called');
        }
        
        $this->eventDispatcher->addListener($eventName, $listener, $priority);
        
        return $this;
    }
    
    public function addEventSubscriber(\Symfony\Component\EventDispatcher\EventSubscriberInterface $subscriber)
    {
        if ($this->locked) {
            throw new \BadMethodCallException('EventSubscribers can be added before handle method is called');
        }

        $this->eventDispatcher->addSubscriber($subscriber);

        return $this;
    }

    public function getParameters()
    {
        if (null === $this->parameters){
            throw new \LogicException('Parameters is not ready. Use handle method first.');
        }
        
        return $this->parameters;
    }
    
   
    public function setQueryBuilder($queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }
    
    public function getQueryBuilder()
    {
        if (null === $this->queryBuilder){
            throw new \LogicException('QueryBuilder. Use setQueryBuilder method first.');
        }
        
        return $this->queryBuilder;
    }
    
    
    public function setQuery($query)
    {
        $this->query = $query;
        
        return $this;
    }
    
    public function getQuery()
    {
        if (null === $this->query){
            throw new \LogicException('Query is not ready. Use handle method first.');
        }
        
        return $this->query;
    }
    
    public function setCount($count)
    {
        $this->count = (int) $count;
        return $this;
    }
    
    public function getCount()
    {
        if (null === $this->count){
            throw new \LogicException('Count is not ready. Use handle method first.');
        }
        
        return $this->count;
    }
    
    abstract public function getResult();

    public function getData()
    {
        if (null === $this->data){
            throw new \LogicException('Data is not ready. Use handle method first.');
        }
        
        return $this->data;
    }

    public function handle(Request $request)
    {
        $this->locked = true;
        $requestParameters = $this->getRequestParameters($request);
        $this->resolveParameters($requestParameters);

        $qb = $this->getQueryBuilder();
        $this->modifyQueryBuilder($qb, $this->getParameters());
        
        if ($this->eventDispatcher->hasListeners(\Thrace\DataGridBundle\DataGridEvents::onQueryBuilderReady)) {
            // Events need to be refactored.
            $queryBuilderEvent = new \Thrace\DataGridBundle\Event\QueryBuilderEvent('ORM', $qb);
            $this->eventDispatcher->dispatch(\Thrace\DataGridBundle\DataGridEvents::onQueryBuilderReady, $queryBuilderEvent);
            $qb = $queryBuilderEvent->getQueryBuilder();
        }
        
        

        $query = $qb->getQuery();
        
        if ($this->eventDispatcher->hasListeners(\Thrace\DataGridBundle\DataGridEvents::onQueryReady)) {
            // Events need to be refactored.
            $queryEvent = new \Thrace\DataGridBundle\Event\QueryEvent('ORM', $query);
            $this->eventDispatcher->dispatch(\Thrace\DataGridBundle\DataGridEvents::onQueryReady, $queryEvent);
            $query = $queryEvent->getQuery();
        }
        
        $this->setQuery($query);
        
        $this->buildCount($query);
        
        $this->buildData($this->getResult(), $this->getParameters());
        
        if ($this->eventDispatcher->hasListeners(\Thrace\DataGridBundle\DataGridEvents::onDataReady)) {
            // Events need to be refactored.
            $dataEvent = new \Thrace\DataGridBundle\Event\DataEvent('ORM', $this->getData());
            $this->eventDispatcher->dispatch(\Thrace\DataGridBundle\DataGridEvents::onDataReady, $dataEvent);
            $this->data = $dataEvent->getData();
        }
        
        return $this;
    }
    
    protected function resolveParameters(array $options)
    {
        $resolver = new OptionsResolver();
    
        $resolver->setRequired(array(
            'search', 'filters', 'page', 'orderBy', 'sort', 'records'
        ));
    
        $resolver->setAllowedTypes(array(
            'page' => array('int'),
            'orderBy' => array('string', 'bool'),
            'sort' => array('string'),
            'records' => array('int'),
            'search' => array('bool'),
            'filters' => array('array', 'bool'),
        ));
    
        $resolver->setAllowedValues(array(
            'sort' => array('ASC', 'DESC')
        ));
    
        $resolver->setDefaults(array(
            'search'          => false,
            'filters'         => false,
            'page'            => 0,
            'orderBy'         => false,
            'sort'            => 'ASC',
            'records'         => 0,
        ));
    
        $this->parameters = $resolver->resolve($options);
        
        return $this;
    }
    
    /**
     * 
     * Resolves rule option
     * 
     * @param array $rule
     * @return array:
     */
    protected function getResolvedRule(array $rule)
    {
        $resolver = new OptionsResolver();
        
        $resolver->setRequired(array(
            'field', 'op', 'data'
        ));
        
        $resolver->setAllowedTypes(array(
            'field' => array('string'),
            'op'    => array('string'),
        ));
        
        return $resolver->resolve($rule); 
    }
    
    /**
     * Gets requested parameters
     * 
     * @return array
     */
    protected function getRequestParameters(Request $request)
    {
        // $page current page of the grid
        $page = (int) $request->get('page', 0);
        
        // $orderBy column name to sort datagrid records
        $orderBy = $request->get('sidx', false);
        
        // ASC | DESC $sort
        $sort = strtoupper($request->get('sord', 'asc'));
        
        // $records  number of records to display
        $records = (int) $request->get('rows', 0);
        
        // $search is search in the query
        $search = ($request->get('_search', false) === 'true');
 
        // $filters Filters from the search query
        $filters = (array) json_decode($request->get('filters', '{}'));
        
        
        $parameters = array(
            'page'            => $page,
            'orderBy'         => $orderBy,
            'sort'            => $sort,
            'records'         => $records,
            'search'          => $search,
            'filters'         => $filters,
        );
        
        return $parameters;
    }
    
    protected function isAggregatedField($field)
    {
        return (bool) preg_match('/_aggregated$/', $field);
    }
    
    protected function getFilters(array $parameters)
    {
        if ($parameters['search'] && !empty($parameters['filters'])) {
            $filters = $parameters['filters'];
            
            if (!isset($filters['groupOp']) || !in_array($filters['groupOp'], array('AND', 'OR'))){
                throw new \InvalidArgumentException('Operator does not match OR | AND');
            }

            if (!isset($filters['rules']) || !is_array($filters['rules'])){
                throw new \InvalidArgumentException('Rules are not set.');
            }
            
            return $filters;
        }
        
        return false;
    }
    
    protected function buildData(array $result, array $parameters)
    {
        $total = ($parameters['page'] && $parameters['records']) ?  ceil($this->getCount() / $parameters['records']) : $this->getCount();
        
        $rows = array();

        foreach ($result as $row) {
            $colData = array();
            foreach($row as $key => $col){
                if(!is_string($key)){
                    continue;
                }
                $colData[$key] = $col;
            }

            $rows[] = array('id' => $row['id'], 'cell' => $colData);
        }
        
        $data =  array(
            'page'    => $parameters['page'],
            'total'   => $total,
            'records' => $this->getCount(),
            'rows'    => $rows
        );

        $this->data = $data;
        
        return $this;
    }
    
    abstract protected function modifyQueryBuilder($qb, array $parameters);
    
    abstract protected function buildCount($query);
}