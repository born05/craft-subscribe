<?php

namespace born05\craftsubscribe\controllers;

use born05\craftsubscribe\CraftSubscribe;
use Craft;
use craft\web\Controller;
use yii\web\Response;

class SubscribeController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected array|int|bool $allowAnonymous = true;

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $settings = Craft::$app->getConfig()->getConfigFromFile('craft-subscribe');
        
        if (array_key_exists('honeypot', $settings) && $settings['honeypot']) {
            $honeypotKey = array_key_exists('honeypotKey', $settings) ? $settings['honeypotKey'] : 'name';
            $honeypot = $this->request->getBodyParam($honeypotKey, '');
    
            if (!empty(trim($honeypot))) {
                $this->response->setStatusCode(400);
                return $this->asJson(['error' => 'Wrong request']);
            }
        }

        $crm = CraftSubscribe::$instance->subscribe->getCrm();
        $subscriber = $crm->createModel($this->request->getBodyParams());

        if (!$subscriber->validate()) {
            $this->response->setStatusCode(400);
            return $this->asJson($subscriber->getErrors());
        }

        if ($this->request->isPut) {
            $result = $crm->upsert($subscriber);
        } else if ($this->request->isPost) {
            $result = $crm->insert($subscriber);
        } else {
            return $this->methodNotAllowed();
        }

        if ($result['success']) {
            return $this->asJson($result);
        } else {
            return $this->error($result);
        }
    }

    // Private Methods
    // =========================================================================

    /**
     * @return mixed
     */
    private function methodNotAllowed(): Response
    {
        $resp = Craft::$app->getResponse();
        $resp->setStatusCode(405);
        return $this->asRaw('Method not allowed');
    }

    /**
     * @return mixed
     */
    private function error(array $result): Response
    {
        $resp = Craft::$app->getResponse();

        try {
            $statusCode = $result['error']?->status;
        } catch (\Throwable $e) {
            $statusCode = 500;
        }

        $resp->setStatusCode($statusCode);
        return $this->asJson($result);
    }
}
