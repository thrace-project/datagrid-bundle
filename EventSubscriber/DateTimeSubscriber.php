<?php

namespace Thrace\DataGridBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Intl\DateFormatter\IntlDateFormatter;
use Thrace\DataGridBundle\DataGridEvents;
use Thrace\DataGridBundle\Event\DataEvent;

/**
 * DataGrid listener
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class DateTimeSubscriber implements EventSubscriberInterface
{

    public static $formats = array(
        'none'   => IntlDateFormatter::NONE,
        'short'  => IntlDateFormatter::SHORT,
        'medium' => IntlDateFormatter::MEDIUM,
        'long'   => IntlDateFormatter::LONG,
        'full'   => IntlDateFormatter::FULL,
    );
    
    protected $locale = 'en';
    
    protected $timezone = 'Europe/London';
    
    protected $dateFormat = 'medium';
    
    protected $timeFormat = 'medium';
    
    protected $format;
    
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }
    
    public function setTimezone($timezone)
    {
        $this->timezone = (string) $timezone;
        
        return $this;
    }
    
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }
    
    
    
    /**
     * Handles DateTime fields
     * 
     * @param DataEvent $event
     */
    public function onDataReady(DataEvent $event)
    {
        $data  = array();
       
        foreach ($event->getData() as $key => $row){
            foreach ($row as $field => $value){
                
                if($value instanceof \DateTime){
                    
                    $formatter = \IntlDateFormatter::create(
                        $this->locale,
                        static::$formats[$this->dateFormat],
                        static::$formats[$this->timeFormat],
                        $this->timezone,
                        \IntlDateFormatter::GREGORIAN,
                        $this->format
                    );

                    $value =  $formatter->format($value->getTimestamp());

                }
                
                $data[$key][$field] = $value;
            }
        }
        
        $event->setData($data);
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            DataGridEvents::onDataReady => 'onDataReady'
        );
    }
}