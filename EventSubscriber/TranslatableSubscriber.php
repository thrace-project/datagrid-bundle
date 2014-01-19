<?php

namespace Thrace\DataGridBundle\EventSubscriber;

use Doctrine\ORM\Query;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thrace\DataGridBundle\DataGridEvents;
use Thrace\DataGridBundle\Event\QueryEvent;

/**
 * DataGrid subscriber
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class TranslatableSubscriber implements EventSubscriberInterface
{

    /**
     * @var string
     */
    protected $locale = 'en';
    
    protected $fallback = true;
    
    public function setLocale($locale)
    {
        $this->locale = (string) $locale;
        
        return $this;
    }
    
    public function getLocale()
    {
        return $this->locale;
    }
    
    public function setFallback($fallback)
    {
        $this->fallback = (bool) $fallback;
        
        return $this;
    }
    
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * Handles datagrid event
     * 
     * @param QueryEvent $event
     */
    public function onQueryReady(QueryEvent $event)
    {

        $query = $event->getQuery();

        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
        $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $this->locale);
        $query->setHint(TranslatableListener::HINT_FALLBACK, $this->fallback);
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            DataGridEvents::onQueryReady => 'onQueryReady'
        );
    }
}