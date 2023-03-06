<?php

namespace born05\craftsubscribe\services;

use born05\craftsubscribe\events\SubscribeEvent;
use Craft;
use craft\base\Component;
use born05\craftsubscribe\interfaces\CrmInterface;
use GuzzleHttp\Exception\RequestException;
use yii\base\DynamicModel;

class MailchimpCrm extends Component implements CrmInterface
{
    // Constants
    // =========================================================================

    /**
     * @event SubscribeEvent The event that is triggered before and after
     *                       a user is subscribed.
     *
     * ```php
     * use born05\craftsubscribe\services\MailchimpCrm;
     * use born05\craftsubscribe\events\SubscribeEvent;
     *
     * Event::on(MailchimpCrm::class,
     *     MailchimpCrm::EVENT_AFTER_SUBSCRIBE,
     *     function(ShipmentEvent $event) {
     *         $labels = $event->labels;
     *     }
     * );
     * ```
     */
    const EVENT_BEFORE_SUBSCRIBE = 'beforeSubscribeEvent';
    const EVENT_AFTER_SUBSCRIBE = 'afterSubscribeEvent';

    // Public Methods
    // =========================================================================

    public function insert(DynamicModel $subscriber): array
    {
        $this->trigger(self::EVENT_BEFORE_SUBSCRIBE, new SubscribeEvent([
            'subscriber' => $subscriber,
        ]));

        $settings = Craft::$app->getConfig()->getConfigFromFile('craft-subscribe');
        $apiBasePath = $settings['apiBasePath'];
        $listId = $settings['listId'];

        $resp =  $this->sendApiRequest(
            "$apiBasePath/lists/$listId/members",
            'POST',
            $this->createInsertBody($subscriber),
            $settings['apiKey'],
        );

        $this->trigger(self::EVENT_AFTER_SUBSCRIBE, new SubscribeEvent([
            'subscriber' => $subscriber,
            'success' => $resp['success'],
        ]));

        return $resp;
    }

    public function upsert(DynamicModel $subscriber): array
    {
        $this->trigger(self::EVENT_BEFORE_SUBSCRIBE, new SubscribeEvent([
            'subscriber' => $subscriber,
        ]));

        $settings = Craft::$app->getConfig()->getConfigFromFile('craft-subscribe');
        $apiBasePath = $settings['apiBasePath'];
        $listId = $settings['listId'];
        $emailHash = md5(strtolower($subscriber->email_address));

        $resp = $this->sendApiRequest(
            "$apiBasePath/lists/$listId/members/$emailHash",
            'PUT',
            $this->createInsertBody($subscriber),
            $settings['apiKey'],
        );

        $this->trigger(self::EVENT_AFTER_SUBSCRIBE, new SubscribeEvent([
            'subscriber' => $subscriber,
            'success' => $resp['success'],
        ]));

        return $resp;
    }

    public function createModel(array $params): DynamicModel
    {
        $rules = [
            [['email_address'], 'email'],
            [['email_address'], 'required'],
            [['status'], 'string'],
            [['status'], 'required'],
        ];

        return DynamicModel::validateData($params, $rules);
    }

    // Private Methods
    // =========================================================================

    private function createInsertBody(DynamicModel $subscriber): array
    {
        return $subscriber->toArray();
    }

    private function sendApiRequest($url, $method, $body, $apiKey): array
    {
        $client = Craft::createGuzzleClient([
            'headers' => ['Authorization' => "apikey $apiKey"],
            'json' => $body,
        ]);

        try {
            $client->request($method, $url);
        } catch (RequestException $e) {
            $message = $e->hasResponse() ? json_decode($e->getResponse()->getBody()) : $e;
            return [
                'success' => false,
                'error' => $message,
            ];
        }

        return ['success' =>  true];
    }
}
