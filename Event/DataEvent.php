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
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 */
class DataEvent extends Event
{

    /**
     * @var string
     */
    protected $dataGridName;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * Constructor
     *
     * @param string $dataGridName           
     * @param array $data        
     */
    public function __construct ($dataGridName, array $data)
    {
        $this->dataGridName = (string) $dataGridName;
        $this->data = $data;
    }

    /**
     * Gets DataGrid name
     */
    public function getDataGridName () 
    {
        return $this->dataGridName;
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