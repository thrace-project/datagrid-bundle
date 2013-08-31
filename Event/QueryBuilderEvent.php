<?php
/*
 * This file is part of ThraceDataGridBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\DataGridBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class implementation of Event
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class QueryBuilderEvent extends Event
{

    /**
     * @var string
     */
    protected $dataGridName;

    /**
     * @var Object
     */
    protected $queryBuilder;

    /**
     * Constructor
     *
     * @param string $dataGridName   
     * @param Object $queryBuilder                 
     */
    public function __construct ($dataGridName, $queryBuilder)
    {
        $this->dataGridName = (string) $dataGridName;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Gets DataGrid name
     */
    public function getDataGridName () 
    {
        return $this->dataGridName;
    }

    /**
     * Gets Object object
     */
    public function getQueryBuilder ()
    {
        return $this->queryBuilder;
    }
    
    /**
     * Sets QueryBuilder
     * 
     * @param Object $queryBuilder
     */
    public function setQueryBuilder($queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

}