<?php

namespace Drupal\workflow_twilio_notify\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\workflow_twilio_notify\Event\ModerationEvent;


/**
 * Class EntityTypeSubscriber.
 *
 * @package Drupal\workflow_twilio_notify\EventSubscriber
 */
class ModirationSubscriber implements EventSubscriberInterface {

  protected $notification;

  public function __construct() {
      $this->notification = \Drupal::service('workflow_twilio_notify.notication');
  }

    /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
        ModerationEvent::MODERATION_PRESAVE => ['ModirationPresave', 0],
    ];
    return $events;
  }

 

  public function ModirationPresave(ModerationEvent $event) {
    $entity = $event->getEntity();
    if($entity->isNew()) {
      return;
    }
    $this->notification->notify($entity->original->moderation_state->value, $entity->moderation_state->value, $entity);
  }

}