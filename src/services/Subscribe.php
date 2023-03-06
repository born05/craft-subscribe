<?php

namespace born05\craftsubscribe\services;

use born05\craftsubscribe\CraftSubscribe;
use born05\craftsubscribe\interfaces\CrmInterface;
use Craft;
use craft\base\Component;
use Exception;

class Subscribe extends Component
{
    // Public Methods
    // =========================================================================

    public function getCrm(): CrmInterface
    {
        $settings = Craft::$app->getConfig()->getConfigFromFile('craft-subscribe');
        $crmHandle = $settings['type'];

        try {
            $crmClassString = [
                CraftSubscribe::MAILCHIMP_CRM_HANDLE => MailchimpCrm::class
            ][$crmHandle];
            return new $crmClassString();
        } catch (Exception $e) {
            throw new Exception("$crmHandle is not an invalid or unsupported CRM class");
        }
    }
}
