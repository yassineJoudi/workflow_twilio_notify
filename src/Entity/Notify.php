<?php

namespace Drupal\workflow_twilio_notify\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Notify entity.
 *
 * @ConfigEntityType(
 *   id = "notify",
 *   label = @Translation("Notify"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\workflow_twilio_notify\NotifyListBuilder",
 *     "form" = {
 *       "add" = "Drupal\workflow_twilio_notify\Form\NotifyForm",
 *       "edit" = "Drupal\workflow_twilio_notify\Form\NotifyForm",
 *       "delete" = "Drupal\workflow_twilio_notify\Form\NotifyDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\workflow_twilio_notify\NotifyHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "notify",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "workflow" = "workflow",
 *     "gateway" = "gateway",
 *     "from_state" = "from_state",
 *     "to_state" = "to_state",
 *     "message" = "message", 
 *     "recipients" = "recipients",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/notify/{notify}",
 *     "add-form" = "/admin/structure/notify/add",
 *     "edit-form" = "/admin/structure/notify/{notify}/edit",
 *     "delete-form" = "/admin/structure/notify/{notify}/delete",
 *     "collection" = "/admin/structure/notify"
 *   }
 * )
 */
class Notify extends ConfigEntityBase implements NotifyInterface {

  /**
   * The Notify ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Notify label.
   *
   * @var string
   */
  protected $label;


  /**
   * The Notification Template current state.
   *
   * @var string
   */
  protected $workflow;

  /**
   * The Notification Template current state.
   *
   * @var string
   */
  protected $from_state;

   /**
   * The Notification Template current state.
   *
   * @var string
   */
  protected $to_state;


   /**
   * The Notification Template current state.
   *
   * @var string
   */
  protected $message;

  /**
   * The Notification Template current state.
   *
   * @var string
   */
  protected $recipients;
  
  /**
   * The Notification Template current state.
   *
   * @var string
   */
  protected $gateway;

}
