<?php

namespace Drupal\workflow_twilio_notify\Event;

use Symfony\Component\EventDispatcher\Event;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines the moderation event.
 */
class ModerationEvent extends Event {

  const MODERATION_PRESAVE = 'workflow_twilio_notify.modiration.presave';

  /**
   * The entity label.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   *   The entity.
   */
  protected $entity;

  /**
   * The construct.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   */
  public function __construct(EntityInterface $entity) {
    $this->entity = $entity;
  }

  /**
   * The EntityInterface.
   *
   * @return array
   *   An array.
   */
  public function getEntity() {
    return $this->entity;
  }

}
