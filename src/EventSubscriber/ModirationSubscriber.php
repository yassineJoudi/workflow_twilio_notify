<?php

namespace Drupal\workflow_twilio_notify\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\workflow_twilio_notify\Event\ModerationEvent;
use Drupal\workflow_twilio_notify\Service\Notification;

/**
 * Class EntityTypeSubscriber.
 *
 *  Category = EntityTypeSubscriber
 * 
 * @package EventSubscriber
 */
class ModirationSubscriber implements EventSubscriberInterface
{
    /**
     * Create an notification object.
     *
     * @param \Drupal\workflow_twilio_notify\Service\Notification $notification
     *   The notification manager.
     */
    public function __construct() 
    {
        $this->notification = \Drupal::service('workflow_twilio_notify.notication');
    }

    /**
     * Registers the methods in this class that should be listeners.
     *
     * @return array
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() 
    {
        $events = [
          ModerationEvent::MODERATION_PRESAVE => ['getModirationPresave', 0],
        ];
        return $events;
    }

    /**
     * Registers the methods in this class that should be listeners.
     *
     * @return array
     *
     * @param \Symfony\Component\HttpKernel\Event\TerminateEvent $event
     * Send notify by sms or return null if entity is not new.
     */
    public function getModirationPresave(ModerationEvent $event) 
    {
        $entity = $event->getEntity();
        if ($entity->isNew()) {
            return;
        }
        $this->notification->notify($entity->original->moderation_state->value, $entity->moderation_state->value, $entity);
    }

}
