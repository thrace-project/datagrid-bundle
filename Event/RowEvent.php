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
class RowEvent extends Event
{

    /**
     * @var string
     */
    protected $dataGridName;

    /**
     * @var boolean
     */
    protected $success = false;

    /**
     * @var Array
     */
    protected $errors = array();

    /**
     * @var integer | null
     */
    protected $id = null;
    
    /**
     * @var array
     */
    protected $data = array();

    /**
     * Constructor
     *
     * @param string $dataGridName                     
     */
    public function __construct ($dataGridName, $id)
    {
        $this->dataGridName = (string) $dataGridName;
        $this->setId($id);
    }

    /**
     * Gets DataGrid name
     */
    public function getDataGridName () 
    {
        return $this->dataGridName;
    }

    /**
     * Is successful ?
     *
     * Provides fluent interface
     *
     * @param boolean $bool            
     * @return \Thrace\Bundle\DataGridBundle\Event\RowEvent
     */
    public function setSuccess ($bool)
    {
        $this->success = (bool) $bool;
        
        return $this;
    }

    /**
     * Gets if operation is successful
     *
     * @return boolean
     */
    public function getSuccess ()
    {
        return $this->success;
    }

    /**
     * Sets the id of the record
     *
     * Provides fluent interface
     *
     * @param integer $id            
     * @return \Thrace\Bundle\DataGridBundle\Event\RowEvent
     */
    public function setId ($id)
    {
        $this->id = (int) $id;
        
        return $this;
    }

    /**
     * Gets the id of the record
     *
     * @return integer | null
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * Sets validation errors if any
     *
     * Provides fluent interface
     *
     * @param array $errors            
     * @return \Thrace\DataGridBundle\Event\RowEvent
     */
    public function setErrors (array $errors)
    {
        $this->errors = $errors;
        
        return $this;
    }

    /**
     * Gets validation errors
     *
     * @return array
     */
    public function getErrors ()
    {
        return $this->errors;
    }
    
    /**
     * Sets data
     * 
     * @param array $data
     * @return \Thrace\DataGridBundle\Event\RowEvent
     */
    public function setData(array $data)
    {
        $this->data = $data;
        
        return $this;
    }
    
    /**
     * Gets data
     * 
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}