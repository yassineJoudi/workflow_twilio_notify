<?php

namespace Drupal\workflow_twilio_notify;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;

/**
 * Provides routes for Notify entities.
 *
 * @see Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class NotifyHtmlRouteProvider extends AdminHtmlRouteProvider
{

    /**
     * Routes.
     *
     * @return array
     *
     * @param \Symfony\Component\HttpKernel\Event\TerminateEvent $event
     * Send notify by sms or return null if entity is not new.     
     */
    public function getRoutes(EntityTypeInterface $entity_type) 
    {
          $collection = parent::getRoutes($entity_type);

          // Provide your custom entity routes here.
          return $collection;
    }

}
