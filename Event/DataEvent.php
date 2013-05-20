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

use Symfony\Component\HttpFoundation\Request;

use Thrace\DataGridBundle\DataGrid\DataGridInterface;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class implementation of Event
 *
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 */
class DataEvent extends Event
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * Constructor
     *
     * @param string $name            
     * @param Request $request            
     */
    public function __construct ($name, array $data)
    {
        $this->name = (string) $name;
        $this->data = $data;
    }

    /**
     * Gets DataGrid name
     */
    public function getName () 
    {
        return $this->name;
    }

    /**
     * Get request object
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getData ()
    {
        return $this->data;
    }

    /**
     * Sets data
     * 
     * @param array $data
     * @return Thrace\DataGridBundle\Event\DataEvent
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }
}