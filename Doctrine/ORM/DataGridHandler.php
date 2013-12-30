<?php
/*
 * This file is part of ThraceDataGridBundle
 *
 * (c) Nikolay Georgiev <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\DataGridBundle\Doctrine\ORM;

use Thrace\DataGridBundle\Event\QueryBuilderEvent;

use Thrace\DataGridBundle\DataGridEvents;

use Doctrine\ORM\QueryBuilder;

use Doctrine\ORM\Tools\Pagination\Paginator;

use Thrace\DataGridBundle\DataGrid\AbstractDataGridHandler;

/**
 * Implementation of AbstractDataGridHandler
 *
 * @author Zender <azazen09@gmail.com>
 * @since 1.0
 */
class DataGridHandler extends AbstractDataGridHandler
{      
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::buildQuery()
     */
    public function buildQuery() 
    {
        $qb = $this->dataGrid->getQueryBuilder();

        if (!$qb instanceof QueryBuilder){
            throw new \InvalidArgumentException('Value must be instance of Doctrine\ORM\QueryBuilder.');
        }
        
        $options = $this->getOptions();
        
        // Is dependant grid ?
        if ($this->dataGrid->isDependentGrid()) {
            $qb->setParameter('masterGridRowId', $options['masterGridRowId']);
        }
        
        // Orders the records
        if ($options['orderBy']) {
            $qb->addOrderBy($options['orderBy'], $options['sort']);
        }
        
        // Applying search filters
        if ($options['search'] && !empty($options['filters'])) {
            $this->applyFilters($qb, $options['filters']);
        }
        
        if (!$this->dataGrid->isSortableEnabled() && $options['page'] && $options['records']){
            $qb->setMaxResults($options['records']);
            $qb->setFirstResult(($options['page'] - 1) * $options['records']);
        }

        $queryBuilderEvent = new QueryBuilderEvent($this->dataGrid->getName(), $qb);
        $this->dispatcher->dispatch(DataGridEvents::onQueryBuilderReady, $queryBuilderEvent);
        
        $this->setQuery($queryBuilderEvent->getQueryBuilder()->getQuery());
        
        // Getting count
        $paginator = new Paginator($this->getQuery());
        //$paginator->setUseOutputWalkers(false);
        $this->setCount($paginator->count());
        
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::getResult()
     */
    public function getResult()
    {   
        return $this->getQuery()->getArrayResult();
    }
      
    /**
     * Applying the filters on QueryBuilder
     *
     * @param QueryBuilder $qb
     * @param object $filters
     * @return void
     * @throws \RuntimeException
     */
    private function applyFilters (QueryBuilder $qb, array $filters)
    {

        if (!isset($filters['groupOp']) || !in_array($filters['groupOp'], array('AND', 'OR'))){
            throw new \InvalidArgumentException('Operator does not match OR | AND');
        }

        if (!isset($filters['rules']) || !is_array($filters['rules'])){
            throw new \InvalidArgumentException('Rules are not set.');
        }
        
        $groupOp = $filters['groupOp'];
    
        $andXWithWhere = $qb->expr()->andX();
        $andXWithHaving = $qb->expr()->andX();
        
        $orXWithWhere = $qb->expr()->orX();
        $orXWithHaving = $qb->expr()->orX();

        $supportedAggregateOperators = array('eq', 'ne', 'lt', 'le', 'gt', 'ge');
    
        foreach ($filters['rules'] as $rule) {
            $rule = $this->getResolvedRule((array) $rule);
            
            $field = $this->getFieldQuery($rule['field'], $rule['op'], $rule['data'], $qb);
            $isAgrigated = $this->isAggregated($rule['field']);
            
            if ($groupOp === 'AND'){
                if (false === $isAgrigated){
                    $andXWithWhere->add($field);
                } else if ($isAgrigated && in_array($rule['op'], $supportedAggregateOperators)){
                    $andXWithHaving->add($field);
                }  
            } elseif ($groupOp === 'OR'){
                if (false === $isAgrigated){
                    $orXWithWhere->add($field);
                } else if ($isAgrigated && in_array($rule['op'], $supportedAggregateOperators)){
                    $orXWithHaving->add($field);
                }
            }
        }
    
        
          
        (count($andXWithWhere->getParts()) > 0) ? $qb->andWhere($andXWithWhere) : null;
        (count($andXWithHaving->getParts()) > 0) ? $qb->andHaving($andXWithHaving) : null; 
        
        // we use addWhere/addHaving because if we add a where/having clause beforehand then it will be ignored.
        (count($orXWithWhere->getParts()) > 0) ? $qb->andWhere($orXWithWhere) : null;
        (count($orXWithHaving->getParts()) > 0) ? $qb->andHaving($orXWithHaving) : null;     
    }
    
    /**
     * Builds where clause
     *
     * @param string $field
     * @param string $searchOper
     * @param string $term
     * @param QueryBuilder $qb
     * @return \Doctrine\ORM\Query\Expr | \Doctrine\ORM\Query\Func
     * @throws \InvalidArgumentException
     */
    private function getFieldQuery ($field, $searchOper, $term, QueryBuilder $qb)
    {   
      
        switch ($searchOper) {
            case 'eq':
                return $qb->expr()->eq($field, $qb->expr()->literal($term));
                 
            case 'ne':
                return $qb->expr()->neq($field, $qb->expr()->literal($term));
                 
            case 'lt':
                return $qb->expr()->lt($field, $qb->expr()->literal($term));
                 
            case 'le':
                return $qb->expr()->lte($field, $qb->expr()->literal($term));
                 
            case 'gt':
                return $qb->expr()->gt($field, $qb->expr()->literal($term));
                 
            case 'ge':
                return $qb->expr()->gte($field, $qb->expr()->literal($term));
                 
            case 'bw':
                return $qb->expr()->like($field, $qb->expr()->literal($term . '%'));
    
            case 'bn':
                return $qb->expr()->not($qb->expr()->like($field, $qb->expr()->literal($term . '%')));
                 
            case 'ew':
                return $qb->expr()->like($field, $qb->expr()->literal('%' . $term));
    
            case 'en':
                return $qb->expr()->not($qb->expr()->like($field, $qb->expr()->literal('%' . $term)));
    
            case 'cn':
                return $qb->expr()->like($field, $qb->expr()->literal('%' . $term . '%'));
    
            case 'nc':
                return $qb->expr()->not($qb->expr()->like($field, $qb->expr()->literal('%' . $term . '%')));
    
            default:
                throw new \InvalidArgumentException(sprintf('Search operator %s is not valid', $searchOper));
        }
    }
    
    /**
     * Checks if field is aggregated value
     * 
     * @param string $field
     * @return boolean
     */
    private function isAggregated($field)
    {
        $cols = $this->getDataGrid()->getColModel();
        
        foreach ($cols as $col){
            if ($col['index'] === $field && isset($col['aggregated']) && $col['aggregated'] === true){
                return true;
            }
        }
        
        return false;
    }

}