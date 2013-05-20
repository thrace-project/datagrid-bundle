<?php
/*
 * This file is part of ThraceDataGridBundle
 *
 * (c) Nikolay Georgiev <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\DataGridBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class implementation of Event
 *
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 */
class QueryEvent extends Event
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Object
     */
    protected $query;

    /**
     * Constructor
     *
     * @param string $name   
     * @param Object $query                  
     */
    public function __construct ($name, $query)
    {
        $this->name = (string) $name;
        $this->query = $query;
    }

    /**
     * Gets DataGrid name
     */
    public function getName () 
    {
        return $this->name;
    }

    /**
     * Gets Query object
     */
    public function getQuery ()
    {
        return $this->query;
    }

}