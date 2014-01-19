<?php

namespace Thrace\DataGridBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thrace\DataGridBundle\DataGridEvents;
use Thrace\DataGridBundle\Event\DataEvent;

/**
 * DataGrid listener
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class TimezoneSubscriber implements EventSubscriberInterface
{

    protected $timezone = 'Europe/London';
    
    public function setTimezone($timezone)
    {
        $this->timezone = (string) $timezone;
        
        return $this;
    }
    
    /**
     * Handles DateTime fields
     * 
     * @param DataEvent $event
     */
    public function onDataReady(DataEvent $event)
    {
        
        $data = $event->getData();  
        $new  = array();
       
        foreach ($data['rows'] as $key => $row){
            foreach ($row as $field => $value){
                
                if($value instanceof \DateTime){
                    $value->setTimezone(new \DateTimeZone($this->timezone));
                }
                
                $new[$key][$field] = $value;
            }
        }
        
        $data['rows'] = $new;

        $event->setData($data);
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            DataGridEvents::onDataReady => 'onDataReady'
        );
    }
}