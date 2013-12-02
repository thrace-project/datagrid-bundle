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
 * Row position change Event Class 
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class RowPositionChangeEvent extends Event
{

    /**
     * @var string
     */
    protected $dataGridName;
    
    /**
     * @var integer
     */
    protected $rowId;
    
    /**
     *  @var integer
     */
    protected $rowPosition;
       
    /**
     * @var integer
     */
    protected $extraData = array();

    /**
     * Contructor
     * 
     * @param string $dataGridName
     * @param integer $rowId
     * @param integer $rowPosition
     */
    public function __construct ($dataGridName, $rowId, $rowPosition)
    {
        $this->dataGridName = (string) $dataGridName;
        $this->rowId = (int) $rowId;
        $this->rowPosition = ((int) $rowPosition - 1);
    }

    /**
     * Gets DataGrid name
     * 
     * @return string
     */
    public function getDataGridName () 
    {
        return $this->dataGridName;
    }

    /**
     * Gets row id
     * 
     * @return integer
     */
    public function getRowId()
    {
        return $this->rowId;
    }
    
    /**
     * Gets row position
     * 
     * @return integer
     */
    public function getRowPosition()
    {
        return $this->rowPosition;
    }
    
    /**
     * Sets extra data used for response
     * 
     * Provide fluent interface
     * 
     * @param array $extraData
     * @return Thrace\DataGridBundle\Event\RowSortEvent
     */
    public function setExtraData(array $extraData)
    {
        $this->extraData = $extraData;
        
        return $this;
    }
    
    /**
     * Retrieve extra data (used for response)
     * 
     * @return mixed | array
     */
    public function getExtraData()
    {
        return $this->extraData;
    }
}