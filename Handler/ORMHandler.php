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

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Implementation of AbstractDataGridHandler
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class ORMHandler extends AbstractHandler
{      
    
    public function buildCount($query)
    {
        // Getting count
        $paginator = new Paginator($this->getQuery());
        //$paginator->setUseOutputWalkers(false);
        $this->setCount($paginator->count());
        
        return $this;
    }
    
    public function getResult()
    {
        if (null === $this->getQuery()){
            throw new \LogicException('Query is not ready. Use handle method first.');
        }
        
        return $this->getQuery()->getArrayResult();
    }
    
    protected function modifyQueryBuilder($qb, array $parameters)
    {
        if (!$qb instanceof QueryBuilder){
            throw new \InvalidArgumentException('Value must be instance of Doctrine\ORM\QueryBuilder.');
        }
        
        // Applying search filters
        if ($filters = $this->getFilters($parameters)) {
             $this->applyFilters($qb, $filters); 
        }
        
        if ($parameters['orderBy']) {
            $qb->addOrderBy($parameters['orderBy'], $parameters['sort']);
        }
        
        if ($parameters['page'] && $parameters['records']){
            $qb->setMaxResults($parameters['records']);
            $qb->setFirstResult(($parameters['page'] - 1) * $parameters['records']);
        }
        
        return $this;
    }
    
    
    /**
     * Applying the filters on QueryBuilder
     *
     * @param QueryBuilder $qb
     * @param object $filters
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function applyFilters ($qb, array $filters)
    {
        if (!$qb instanceof QueryBuilder){
            throw new \InvalidArgumentException('Value must be instance of Doctrine\ORM\QueryBuilder.');
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
            $isAgrigated = $this->isAggregatedField($rule['field']);
            
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
        
        return $this;
    }
    
    /**
     * Builds where clause
     *
     * @param string $field
     * @param string $searchOper
     * @param string $term
     * @param QueryBuilder $qb
     * @return Expr | \Doctrine\ORM\Query\Func
     * @throws \InvalidArgumentException
     */
    protected function getFieldQuery ($field, $searchOper, $term, QueryBuilder $qb)
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
}