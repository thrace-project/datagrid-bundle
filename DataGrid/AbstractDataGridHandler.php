<?php
/*
 * This file is part of ThraceDataGridBundle
 *
 * (c) Nikolay Georgiev <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\DataGridBundle\DataGrid;

use Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface;

use Thrace\DataGridBundle\Event\DataEvent;

use Thrace\DataGridBundle\DataGridEvents;

use Thrace\DataGridBundle\Event\QueryEvent;

use Thrace\DataGridBundle\DataGrid\DataGridInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Implementation of DataGridHandlerInterface   
 *
 * @author Zender <azazen09@gmail.com>
 * @since 1.0
 */
abstract class AbstractDataGridHandler implements DataGridHandlerInterface   
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface 
     */
    protected $dispatcher; 
    
    /**
     * @var \Thrace\DataGridBundle\DataGrid\DataGridInterface
     */
    protected $dataGrid;
    
    /**
     * @var array
     */
    protected $options;
    
    /**
     * @var Object
     */
    protected $query;
    
    /**
     * @var integer
     */
    protected $count;
    
    /**
     * @var array
     */
    protected $data;
    
    /**
     * @var array
     */
    protected $processedData;
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::setDispatcher()
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::setDataGrid()
     */
    public function setDataGrid(DataGridInterface $dataGrid)
    {
        $this->dataGrid = $dataGrid;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::getDataGrid()
     */
    public function getDataGrid()
    {
        if (empty($this->dataGrid)){
            throw new \LogicException('DataGrid is not set.');
        }
        
        return $this->dataGrid;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::getOptions()
     */
    public function getOptions()
    {
        if (empty($this->options)){
            throw new \LogicException('Options are not resolved. Use DataGridHandlerInterface::resolveOptions($options).');
        }
        
        return $this->options;
    }

    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::getData()
     */
    public function getData()
    {
        if (null === $this->data){
            throw new \LogicException('Data is not ready. Use buildData method first.');
        }
        
        return $this->data;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::getProcessedData()
     */
    public function getProcessedData()
    {
        if (null === $this->processedData){
            throw new \LogicException('ProcessedData is not ready. Use buildData method first.');
        }
        
        return $this->processedData;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::setCount()
     */
    public function setCount($count)
    {
        $this->count = (int) $count;
        
        return $this; 
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::getCount()
     */
    public function getCount()
    {
        if (null === $this->count){
            throw new \LogicException('Count is not ready. Use buildData method first.');
        }
        
        return $this->count;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::setQuery()
     */
    public function setQuery($query)
    {
        $this->dispatcher
            ->dispatch(DataGridEvents::onQueryReady, new QueryEvent($this->dataGrid->getName(), $query));
        
        $this->query = $query;
        
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::getQuery()
     */
    public function getQuery()
    {
        if (null === $this->query){
            throw new \LogicException('Query is not ready. Use buildQuery method first.');
        }
        
        return $this->query;
    }

    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::buildData()
     */
    public function buildData()
    {
        $options = $this->getOptions();
        
        $total = $this->getCount();
        
        $data  = $this->getResult();
        
        $dataEvent = new DataEvent($this->dataGrid->getName(), $data);
        
        $this->dispatcher
            ->dispatch(DataGridEvents::onDataReady, $dataEvent);
        
        $this->data = $dataEvent->getData();
        
        $rows = array();
        
        if ($options['page'] && $options['records']){
            $total = ceil($this->getCount() / $options['records']);
        }

        foreach ($this->getData() as $row) {
            $colData = array();
            foreach($this->getDataGrid()->getColModel() as $col){
                $colData[] = $row[$col['name']];
            }

            $rows[] = array('id' => $row['id'], 'cell' => $colData);
        }
        
        $processedData =  array(
            'page'    => $options['page'],
            'total'   => (int) $total,
            'records' => $this->getCount(),
            'rows'    => $rows
        );
        
        $this->processedData = $processedData;
        
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::resolveOptions()
     */
    public function resolveOptions(array $options)
    {
        $resolver = new OptionsResolver();
    
        $resolver->setRequired(array(
            'search', 'filters', 'page', 'orderBy', 'sort', 'records', 'masterGridRowId'
        ));
    
        $resolver->setAllowedTypes(array(
            'page' => array('int'),
            'orderBy' => array('string', 'bool'),
            'sort' => array('string'),
            'records' => array('int'),
            'search' => array('bool'),
            'filters' => array('array', 'bool'),
            'masterGridRowId' => array('int')
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
            'masterGridRowId' => 0
        ));
    
        $this->options = $resolver->resolve($options);
        
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
}