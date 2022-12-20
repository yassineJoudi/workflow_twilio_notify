<?php

namespace Drupal\workflow_twilio_notify\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\workflow_twilio_notify\Service\Notification;

/**
 * Class NotifyForm for create template by workflow state.
 */
class NotifyForm extends EntityForm {

  /**
   * Create an notification object.
   *
   * @param \Drupal\workflow_twilio_notify\Service\Notification $workflow
   *   The notification manager.
   */
  public function __construct() {
    $this->workflow = \Drupal::service('workflow_twilio_notify.notication');
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $notifications = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $notifications->label(),
      '#description' => $this->t("Label for the Notifications."),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $notifications->id(),
      '#machine_name' => [
        'exists' => '\Drupal\workflow_twilio_notify\Entity\Notify::load',
      ],
      '#disabled' => !$notifications->isNew(),
    ];
    $form['workflow'] = [
      '#type' => 'select',
      '#title' => $this->t('Choisir workflow'),
      '#options' => $this->workflow->getWorkflows()['workflow'],
      '#default_value' => $notifications->get('workflow'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => [$this, 'getListOfStates'],
        'event' => 'click',
      ],
    ];
    $form['template'] = [
      '#type' => 'details',
      '#title' => $this->t('Paramétrages Template SMS'),
      '#open' => TRUE,
    ];
    $workflow_selected = !empty($this->entity->id()) ? $this->entity->get('from_state') : $form_state->getValue("from_state");
    $form['template']['gateway'] = [
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => $this->t('gateway'),
      '#options' => $this->workflow->gatewaysList(),
      '#default_value' => $notifications->get('gateway'),
    ];
    $form['template']['from_state'] = [
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => $this->t("De l'état"),
      '#options' => !empty($notifications->get('workflow')) ? $this->workflow->getPreparedWorkflowOptions($notifications->get('workflow')) : [],
      '#default_value' => $notifications->get('from_state'),
    ];
    $form['template']['to_state'] = [
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => $this->t("Vers l'étaté"),
      '#options' => !empty($notifications->get('workflow')) ? $this->workflow->getPreparedWorkflowOptions($notifications->get('workflow')) : [],
      '#default_value' => $notifications->get('to_state'),
    ];
    $form['template']['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Corps du SMS'),
      '#format' => 'full_html',
      '#default_value' => $notifications->get('message'),
      '#required' => TRUE,
    ];
    $form['template']['recipients'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Destinataires'),
      '#format' => 'full_html',
      '#default_value' => $notifications->get('recipients'),
      '#required' => TRUE,
      '#description' => $this->t('Le champ destinataires contient des adresses mail séparées par des virgules.'),
    ];
    $form['template']['token_tree'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['node'],
      '#show_restricted' => TRUE,
      '#weight' => 90,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $notify = $this->entity;
    $status = $notify->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Notify.', [
          '%label' => $notify->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Notify.', [
          '%label' => $notify->label(),
        ]));
    }
    $form_state->setRedirectUrl($notify->toUrl('collection'));
  }

  /**
   * {@inheritdoc}
   */
  public function getListOfStates(array &$form, FormStateInterface $form_state) {
    $options = "<option></option>";
    $type = $form_state->getValue('workflow');
    $states = $this->workflow->getWorkflowsStates($type);
    foreach ($states as $state) {
      $options .= "<option value=" . $state['machine_name'] . ">" . $state['label'] . "</option>";
    }
    $response = new AjaxResponse();
    $response->addCommand(new RemoveCommand('select[name="from_state"] option'))
      ->addCommand(new RemoveCommand('select[name="to_state"] option'))
      ->addCommand(new AppendCommand('select[name="to_state"]', $options))
      ->addCommand(new AppendCommand('select[name="from_state"]', $options));
    return $response;
  }

}
