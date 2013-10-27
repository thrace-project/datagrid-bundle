<?php
namespace Thrace\DataGridBundle\EventListener;

use Thrace\DataGridBundle\Event\DataEvent;

class DataEventListener
{
    public function onDataReady(DataEvent $event)
    {
        $data = array();
        
        foreach($event->getData() as $key => $row){
      
            $mongoId = $row['_id'];
            unset($row['_id']);
            
            if(!$mongoId instanceof \MongoId){
                throw new InvalidArgumentException('Id should be instance of MongoId');
            }
            
            $id = $mongoId->__toString();
            
            $data[] = array_merge(array('id' => $id), $row);
        }
        
        $event->setData($data);
    }
}