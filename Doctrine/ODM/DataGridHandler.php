<?php
/*
 * This file is part of ThraceDataGridBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\DataGridBundle\Doctrine\ODM;

use Knp\Component\Pager\Paginator;

use Doctrine\ODM\MongoDB\Query\Builder;

use Thrace\DataGridBundle\Event\QueryBuilderEvent;

use Thrace\DataGridBundle\DataGridEvents;

use Thrace\DataGridBundle\DataGrid\AbstractDataGridHandler;

/**
 * Implementation of AbstractDataGridHandler
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class DataGridHandler extends AbstractDataGridHandler
{      
    
    protected $paginator;
    
    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::buildQuery()
     */
    public function buildQuery() 
    {
        $qb = $this->dataGrid->getQueryBuilder();

        if (!$qb instanceof Builder){
            throw new \InvalidArgumentException('Value must be instance of Doctrine\ODM\MongoDB\Query\Builder.');
        }
        
        $options = $this->getOptions();
        
        if ($this->dataGrid->isDependentGrid()) {
            $qb->field($this->getDependantGridField())->equals($options['masterGridRowId']);
        }
        
        if ($options['orderBy']) {
            $qb->sort($options['orderBy'], $options['sort']);
        }
        
        // Applying search filters 
        if ($options['search'] && !empty($options['filters'])) {
            $this->applyFilters($qb, $options['filters']);
        }
        
        if (!$this->dataGrid->isSortableEnabled() && $options['page'] && $options['records']){
            $qb->limit($options['records']);
            $qb->skip(($options['page'] - 1) * $options['records']);
        }

        $queryBuilderEvent = new QueryBuilderEvent($this->dataGrid->getName(), $qb);
        $this->dispatcher->dispatch(DataGridEvents::onQueryBuilderReady, $queryBuilderEvent);
        
        $this->setQuery($queryBuilderEvent->getQueryBuilder()->hydrate(false)->getQuery());
        
        // Getting count
        $pagination = $this->paginator->paginate(
            $this->getQuery(),
            $options['page'],
            $options['records']
        );
        
        $this->setCount($pagination->getTotalItemCount());
        
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\DataGridBundle\DataGrid\DataGridHandlerInterface::getResult()
     */
    public function getResult()
    {   
        return $this->getQuery()->execute()->toArray();
    }
      
    /**
     * Applying the filters on QueryBuilder
     *
     * @param QueryBuilder $qb
     * @param object $filters
     * @return void
     * @throws \RuntimeException
     */
    private function applyFilters (Builder $qb, array $filters)
    {

        if (!isset($filters['groupOp']) || !in_array($filters['groupOp'], array('AND', 'OR'))){
            throw new \InvalidArgumentException('Operator does not match OR | AND');
        }

        if (!isset($filters['rules']) || !is_array($filters['rules'])){
            throw new \InvalidArgumentException('Rules are not set.');
        }
        
        $groupOp = $filters['groupOp'];
    
        $andXWithWhere = $qb->expr();
        //$andXWithHaving = $qb->expr();
        
        $orXWithWhere = $qb->expr();
        //$orXWithHaving = $qb->expr();

        $supportedAggregateOperators = array('eq', 'ne', 'lt', 'le', 'gt', 'ge');
    
        foreach ($filters['rules'] as $rule) {
            $rule = $this->getResolvedRule((array) $rule);
            
            $field = $this->getFieldQuery($rule['field'], $rule['op'], $rule['data'], $qb);
            $isAgrigated = $this->isAggregated($rule['field']);
            
            if ($groupOp === 'AND'){
                if (false === $isAgrigated){
                    $andXWithWhere->addAnd($field);
                } else if ($isAgrigated && in_array($rule['op'], $supportedAggregateOperators)){
                    $andXWithHaving->add($field);
                }  
            } elseif ($groupOp === 'OR'){
                if (false === $isAgrigated){
                    $orXWithWhere->addOr($field);
                } else if ($isAgrigated && in_array($rule['op'], $supportedAggregateOperators)){
                    $orXWithHaving->add($field);
                }
            }
        }
    
        if($groupOp === 'AND'){
            $qb->addAnd($andXWithWhere);
        } elseif($groupOp === 'OR'){
            $qb->addAnd($orXWithWhere);
        }
   
    }
    
    /**
     * Builds where clause
     *
     * @param string $field
     * @param string $searchOper
     * @param string $term
     * @param Builder $qb
     * @return \Doctrine\ORM\Query\Expr | \Doctrine\ORM\Query\Func
     * @throws \InvalidArgumentException
     */
    private function getFieldQuery ($field, $searchOper, $term, Builder $qb)
    {   
      
        switch ($searchOper) {
            case 'eq':
                return $qb->expr()->field($field)->equals($term);
                 
            case 'ne':
                return $qb->expr()->field($field)->notEqual($term);
                 
            case 'lt':
                return $qb->expr()->field($field)->lt($term);
                 
            case 'le':
                return $qb->expr()->field($field)->lte($term);
                 
            case 'gt':
                return $qb->expr()->field($field)->gt($term);
                 
            case 'ge':
                return $qb->expr()->field($field)->gte($term);
                 
            case 'bw':
                return $qb->expr()->field($field)->equals(new \MongoRegex("/^{$term}/"));
    
            case 'bn':
                return $qb->expr()->field($field)->equals(new \MongoRegex("/^(?!{$term})/"));
                 
            case 'ew':
                return $qb->expr()->field($field)->equals(new \MongoRegex("/{$term}$/"));
    
            case 'en':
                return $qb->expr()->field($field)->equals(new \MongoRegex("/(?<!{$term})$/"));
    
            case 'cn':
                return $qb->expr()->field($field)->equals(new \MongoRegex("/{$term}/"));
    
            case 'nc':
                return $qb->expr()->field($field)->equals(new \MongoRegex("/^((?!{$term}).)*$/"));
    
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