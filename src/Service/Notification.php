<?php

namespace Drupal\workflow_twilio_notify\Service;

use Drupal\sms\Provider\SmsProviderInterface;
use Drupal\sms\Entity\SmsGateway;
use Drupal\sms\Entity\SmsMessage;
use Drupal\sms\Direction;

/**
 * Class Notification for function of all services.
 *
 * @package Notification
 */
class Notification
{

    /**
     * The account interface.
     *
     * @var \Drupal\sms\Provider\SmsProviderInterface
     */
    protected $smsHandler;

    /**
     * {@inheritdoc}
     */
    protected $token;

    /**
     * @param $smsHandler
     * {@inheritdoc}
     */
    public function __construct(SmsProviderInterface $smsHandler)
    {
        $this->smsHandler = $smsHandler;
        $this->token = \Drupal::token();
    }

    /**
     * @return array
     * {@inheritdoc}
     */
    public function notify($state_from, $state_to, $entity)
    {
        $phones = [];
        $templates = $this->getTemplates($state_from, $state_to, $entity);
        $node = \Drupal::routeMatch()->getParameter('node');
        foreach ($templates as $template) {
            $phones = explode(",", $template['recipients']);
            if (empty($phones)) {
                return [];
            }
            foreach ($phones as $phone) {
                $this->messagePrepare($this->token->replace($phone, ['node' => $node]), $template['message'], $template['gateway']);
            }
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplates($state_from, $state_to, $entity)
    {
        $templates = $result = [];
        if (empty($state_from) && empty($state_to)) {
            return $templates;
        }
        $storage = \Drupal::entityTypeManager()->getStorage('notify');
        $ids = \Drupal::entityQuery('notify')
            ->condition('from_state', $state_from)
            ->condition('to_state', $state_to)
            ->condition('workflow', $entity->workflow->target_id)
            ->execute();
        $items = $storage->loadMultiple($ids);
        foreach ($items as $key => $entity) {
            if (!$entity->get('status')) {
                continue;
            }
            $templates[] = [
                'from_state' => $entity->get("from_state"),
                'to_state' => $entity->get("to_state"),
                'message' => $entity->get("message"),
                'recipients' => $entity->get("recipients"),
                'originalId' => $entity->get("originalId"),
                'status' => $entity->get('status'),
                'gateway' => $entity->get('gateway'),
            ];
        }
        return $templates;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflows()
    {
        $workflows_list = $workflow_options = $options = [];
        $workflows = \Drupal::entityTypeManager()->getStorage('workflow')->loadByProperties();
        if (count($workflows) > 0) {
            foreach ($workflows as $key => $workflow) {
                $workflow_options[$workflow->id()] = $key;
                $states = $workflow->get('type_settings')['states'];
                foreach ($states as $_key => $state) {
                    $options[$_key] = $state['label'];
                }
                $workflows_list['workflow'] = $workflow_options;
                $workflows_list['states'] = $options;
            }
        }
        return $workflows_list;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowsStates($type)
    {
        $options = [];
        $workflow = \Drupal::entityTypeManager()->getStorage('workflow')->load($type);
        if (empty($workflow)) {
            return $options;
        }
        $states = $workflow->get('type_settings')['states'];
        foreach ($states as $key => $state) {
            $state['machine_name'] = $key;
            $options[] = $state;
        }
        usort(
            $options, function ($a, $b) {
                return ($a['weight'] < $b['weight']) ? -1 : 1; 
            }
        );
        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreparedWorkflowOptions($type)
    {
        $options = [];
        $states = $this->getWorkflowsStates($type);
        foreach ($states as $state) {
            $options[$state['machine_name']] = $state['label'];
        }
        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function messagePrepare($phone, $text, $gateway)
    {
        if (empty($phone) || empty($text)) {
            return [];
        }
        try {
            $message = SmsMessage::create()
                ->addRecipient($phone)
                ->setGateway(SmsGateway::load($gateway))
                ->setMessage($text)
                ->setDirection(Direction::OUTGOING);
            $this->sendMessage($message);
        } catch (\Exception $exception) {
            return [
                $exception->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendMessage($message)
    {
        return $this->smsHandler->send($message);
    }

    /**
     * {@inheritdoc}
     */
    public function gatewaysList()
    {
        $gateways = [];
        foreach (SmsGateway::loadMultiple() as $sms_gateway) {
            $gateways[$sms_gateway->id()] = $sms_gateway->label();
        }
        return $gateways;
    }

}
