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
class QueryEvent extends Event
{

    /**
     * @var string
     */
    protected $dataGridName;

    /**
     * @var Object
     */
    protected $query;

    /**
     * Constructor
     *
     * @param string $dataGridName   
     * @param Object $query                  
     */
    public function __construct ($dataGridName, $query)
    {
        $this->dataGridName = (string) $dataGridName;
        $this->query = $query;
    }

    /**
     * Gets DataGrid name
     */
    public function getDataGridName () 
    {
        return $this->dataGridName;
    }

    /**
     * Gets Query object
     */
    public function getQuery ()
    {
        return $this->query;
    }

}